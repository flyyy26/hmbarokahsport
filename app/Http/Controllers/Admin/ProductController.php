<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Feature;
use App\Models\Order;
use App\Models\OfflineOrder;
use App\Models\OfflineOrderItem; 
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockHistory;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Models\ProductVariantValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;
use App\Services\ImageOptimizer;
use App\Http\Controllers\Customer\CustomerHomeController;

class ProductController extends Controller
{
    protected ImageOptimizer $imageOptimizer;

    public function __construct(ImageOptimizer $imageOptimizer)
    {
        $this->imageOptimizer = $imageOptimizer;
    }
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = Product::with([
            'category',
            'images',
            'variants',
        ]);

        // 🔥 FILTER STOK
        if ($request->filled('stock')) {
            switch ($request->stock) {
                case 'critical':
                    $query->criticalStock();
                    break;
                case 'low':
                    $query->lowStock();
                    break;
                case 'out_of_stock':
                    $query->whereHas('variants', function($q) {
                        $q->selectRaw('SUM(stock) as total_stock')
                        ->havingRaw('SUM(stock) = 0');
                    });
                    break;
                case 'in_stock':
                    $query->inStock();
                    break;
                // 'all' atau lainnya: tanpa filter
            }
        }

        $products = $query
            ->latest()
            ->paginate(10);

        // 🔥 TAMBAHKAN TOTAL STOCK KE SETIAP PRODUK
        foreach ($products as $product) {
            $product->total_stock = $product->variants->sum('stock');
            $product->stock_status = $product->stock_status;
            $product->stock_status_label = $product->stock_status_label;
            $product->stock_status_color = $product->stock_status_color;
        }

        return view('admin.products.index', compact('products'));
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        $features = Feature::active()->orderBy('name')->get();

        return view('admin.products.create', compact('categories', 'features'));
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        \Log::info('=== STORE PRODUCT ===');
        \Log::info('Request data:', $request->all());
        \Log::info('Variants:', $request->input('variants', []));
        \Log::info('Options:', $request->input('options', []));

        $validated = $this->validateProduct($request);

        \Log::info('Validated data:', $validated);

        $uploadedFiles = [];

        try {
            DB::transaction(function () use (
                $request,
                $validated,
                &$uploadedFiles
            ) {
                // 1. CREATE PRODUCT
                $product = Product::create([
                    'category_id' => $validated['category_id'],
                    'name' => $validated['name'],
                    'slug' => $this->generateUniqueSlug($validated['name']),
                    'description' => $validated['description'] ?? null,
                    'gender' => $validated['gender'] ?? null,
                    'material' => $validated['material'] ?? null,
                    'is_featured' => $validated['is_featured'],
                    'is_best_seller' => $validated['is_best_seller'],
                    'is_active' => $validated['is_active'],
                    'minimum_stock' => $request->input('minimum_stock', 5),
                    'restock_threshold' => $request->input('restock_threshold', 10),
                    'has_product_discount' => $request->boolean('has_product_discount'),
                    'discount_type' => $request->input('discount_type'),
                    'discount_value' => $request->input('discount_value'),
                    'is_flash_sale' => $request->boolean('is_flash_sale'),
                    'flash_sale_type' => $request->input('flash_sale_type'),
                    'flash_sale_value' => $request->input('flash_sale_value'),
                    'flash_sale_start_date' => $request->input('flash_sale_start_date'),
                    'flash_sale_end_date' => $request->input('flash_sale_end_date'),
                ]);

                // 2. UPLOAD IMAGES
                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $index => $image) {
                        if (!$image->isValid()) continue;

                        // 🔥 CONVERT KE WEBP
                        $path = $this->imageOptimizer->convertToWebp(
                            file: $image,
                            folder: 'products',
                            maxWidth: 1000,   // product image max 1000px
                            quality: 82
                        );
                        $uploadedFiles[] = $path;

                        $product->images()->create([
                            'image' => $path,
                            'sort_order' => $index,
                        ]);
                    }
                }

                // 🔥 3. CREATE OPTIONS (WARNA & UKURAN) - DENGAN GAMBAR
                if (!empty($validated['options'])) {
                    $optionValueMap = $this->createProductOptions(
                        $product,
                        $validated['options'],
                        $request->file('options') ?? []
                    );

                    // 4. CREATE VARIANTS
                    if (!empty($validated['variants'])) {
                        $this->createProductVariants(
                            $product,
                            $validated['variants'],
                            $optionValueMap
                        );
                    }
                }

                if (!empty($validated['features'])) {
                    $product->features()->sync($validated['features']);
                }
            });

            CustomerHomeController::clearCache();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Produk berhasil ditambahkan.');

        } catch (Throwable $e) {
            // Hapus file jika transaksi gagal
            foreach ($uploadedFiles as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            throw $e;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(Product $product)
    {
        // 🔥 LOAD SEMUA RELASI
        $product->load([
            'category',
            'images' => function ($query) {
                $query->orderBy('sort_order', 'asc');
            },
            'options' => function ($query) {
                $query->orderBy('sort_order', 'asc');
            },
            'options.values' => function ($query) {
                $query->orderBy('sort_order', 'asc');
            },
            'variants' => function ($query) {
                $query->orderBy('price', 'asc');
            },
            'variants.variantValues',
            'variants.variantValues.optionValue',
            'variants.variantValues.optionValue.option',
            'features',
        ]);

        // 🔥 BUILD EXISTING OPTIONS
        $existingOptions = $product->options->map(function ($option) {
            return [
                'id' => $option->id,
                'name' => $option->name,
                'values' => $option->values->map(function ($value) {
                    return [
                        'id' => $value->id,
                        'value' => $value->value,
                        'image' => $value->image ? Storage::url($value->image) : null,
                    ];
                })->values()->toArray(),
            ];
        })->values()->toArray();

        // 🔥 BUILD EXISTING VARIANTS - PERBAIKI
        $existingVariants = $product->variants->map(function ($variant) {
            // Ambil option value names DENGAN URUTAN SESUAI OPTION
            $valueNames = [];
            $optionValueIds = [];

            // Urutkan berdasarkan option sort_order
            $sortedVariantValues = $variant->variantValues->sortBy(function ($vv) {
                return $vv->optionValue->option->sort_order ?? 0;
            });

            foreach ($sortedVariantValues as $vv) {
                if ($vv->optionValue) {
                    $valueNames[] = $vv->optionValue->value;
                    $optionValueIds[] = (int) $vv->product_option_value_id;
                }
            }

            // Hitung diskon persen
            $discountPercent = 0;
            if ($variant->discount_price && $variant->price > 0 && $variant->discount_price < $variant->price) {
                $discountPercent = round((($variant->price - $variant->discount_price) / $variant->price) * 100);
            }

            return [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'price' => (float) $variant->price,
                'discount_price' => $variant->discount_price ? (float) $variant->discount_price : null,
                'discount_percent' => $discountPercent,
                'stock' => (int) $variant->stock,
                'weight' => (int) $variant->weight,
                'image' => $variant->image ? Storage::url($variant->image) : null,
                'option_value_ids' => $optionValueIds,
                'option_value_names' => $valueNames,
                // 🔥 TAMBAHKAN juga value_names yang di-sort untuk matching
                'value_names' => collect($valueNames)->sort()->values()->toArray(),
            ];
        })->values()->toArray();

        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        $features = Feature::active()->orderBy('name')->get();

        return view('admin.products.edit', compact(
            'product',
            'categories',
            'existingOptions',
            'existingVariants',
            'features'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, Product $product)
    {
        // 🔥 HELPER UNTUK CHECKBOX
        $hasCheckedValue = static function ($value): bool {
            return collect((array) $value)->contains(function ($item): bool {
                return in_array(strtolower((string) $item), ['1', 'true', 'on', 'yes'], true);
            });
        };

        \Log::info('=== UPDATE PRODUCT ===', [
            'product_id' => $product->id,
            'name' => $request->input('name'),
            'is_featured' => $request->input('is_featured'),
            'is_best_seller' => $request->input('is_best_seller'),
            'is_active' => $request->input('is_active'),
        ]);

        // 🔥 PASTIKAN CHECKBOX NILAI
        $request->merge([
            'is_featured' => $hasCheckedValue($request->input('is_featured')) ? 1 : 0,
            'is_best_seller' => $hasCheckedValue($request->input('is_best_seller')) ? 1 : 0,
            'is_active' => $hasCheckedValue($request->input('is_active')) ? 1 : 0,
            'is_flash_sale' => $hasCheckedValue($request->input('is_flash_sale')) ? 1 : 0,
            'has_product_discount' => $hasCheckedValue($request->input('has_product_discount')) ? 1 : 0,
        ]);

        $validated = $this->validateProduct($request, $product);

        $uploadedFiles = [];

        try {
            DB::transaction(function () use (
                $request,
                $product,
                $validated,
                &$uploadedFiles
            ) {
                // ============================================
                // 🔥 1. UPDATE PRODUCT - PERBAIKI
                // ============================================
                
                // ✅ UPDATE PRODUCT DENGAN BENAR
                $product->category_id = $validated['category_id'];
                $product->name = $validated['name'];
                $product->slug = $this->generateUniqueSlug($validated['name'], $product->id);
                $product->description = $validated['description'] ?? null;
                $product->gender = $validated['gender'] ?? null;
                $product->material = $validated['material'] ?? null;
                $product->minimum_stock = $request->input('minimum_stock', 5);
                $product->restock_threshold = $request->input('restock_threshold', 10);
                $product->has_product_discount = $validated['has_product_discount'] ? 1 : 0;
                $product->discount_type = $validated['has_product_discount'] ? ($validated['discount_type'] ?? null) : null;
                $product->discount_value = $validated['has_product_discount'] ? ($validated['discount_value'] ?? null) : null;
                $product->is_flash_sale = $validated['is_flash_sale'] ? 1 : 0;
                $product->flash_sale_type = $validated['is_flash_sale'] ? ($validated['flash_sale_type'] ?? null) : null;
                $product->flash_sale_value = $validated['is_flash_sale'] ? ($validated['flash_sale_value'] ?? null) : null;
                $product->flash_sale_start_date = $validated['is_flash_sale'] ? ($validated['flash_sale_start_date'] ?? null) : null;
                $product->flash_sale_end_date = $validated['is_flash_sale'] ? ($validated['flash_sale_end_date'] ?? null) : null;
                
                // 🔥 UPDATE STATUS
                $product->is_featured = $validated['is_featured'] ? 1 : 0;
                $product->is_best_seller = $validated['is_best_seller'] ? 1 : 0;
                $product->is_active = $validated['is_active'] ? 1 : 0;
                
                // ✅ SAVE PRODUCT - INI YANG PENTING!
                $product->save();

                \Log::info('Product updated successfully', [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'is_active' => $product->is_active,
                ]);

                // ============================================
                // 2. DELETE SELECTED OLD IMAGES
                // ============================================
                
                $keepImageIds = collect($request->input('existing_images', []))
                    ->map(fn ($id) => (int) $id)
                    ->toArray();

                $oldImages = $product
                    ->images()
                    ->when(!empty($keepImageIds), function ($query) use ($keepImageIds) {
                        return $query->whereNotIn('id', $keepImageIds);
                    })
                    ->get();

                foreach ($oldImages as $oldImage) {
                    // 🔥 HAPUS VIA HELPER
                    $this->imageOptimizer->delete($oldImage->image);
                    $oldImage->delete();
                }

                // ============================================
                // 3. UPLOAD NEW IMAGES
                // ============================================
                
                if ($request->hasFile('images')) {
                    $lastSortOrder = $product->images()->max('sort_order') ?? -1;

                    foreach ($request->file('images') as $index => $image) {
                        if (!$image->isValid()) continue;

                        // 🔥 CONVERT KE WEBP
                        $path = $this->imageOptimizer->convertToWebp(
                            file: $image,
                            folder: 'products',
                            maxWidth: 1000,
                            quality: 82
                        );
                        $uploadedFiles[] = $path;

                        $product->images()->create([
                            'image' => $path,
                            'sort_order' => $lastSortOrder + $index + 1,
                        ]);
                    }
                }

                // ============================================
                // 4. UPDATE OPTIONS & VARIANTS
                // ============================================

                $oldOptions = $product->options()->with('values')->get();
                $oldOptionValuesMap = [];

                foreach ($oldOptions as $oldOption) {
                    foreach ($oldOption->values as $oldValue) {
                        $oldOptionValuesMap[$oldOption->id][$oldValue->id] = $oldValue->image;
                    }
                }

                $product->options()->delete();
                $product->variants()->delete();

                if (!empty($validated['options'])) {
                    $optionValueMap = $this->createProductOptionsWithExistingImages(
                        $product,
                        $validated['options'],
                        $request->file('options') ?? [],
                        $oldOptionValuesMap
                    );

                    if (!empty($validated['variants'])) {
                        $this->createProductVariants(
                            $product,
                            $validated['variants'],
                            $optionValueMap
                        );
                    }
                }

                // ============================================
                // 5. UPDATE FEATURES
                // ============================================
                
                if (!empty($validated['features'])) {
                    $product->features()->sync($validated['features']);
                } else {
                    $product->features()->detach();
                }
            });

            CustomerHomeController::clearCache();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Produk berhasil diperbarui.');

        } catch (Throwable $e) {
            foreach ($uploadedFiles as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            \Log::error('Product update error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui produk: ' . $e->getMessage());
        }
    }

    public function dashboard(Request $request)
    {
        $now = now();

        // ============================================
        // 🔥 FILTER BULAN (HANYA UNTUK 4 CARD STATS)
        // ============================================
        $monthParam = $request->input('month');

        if ($monthParam) {
            try {
                $selectedMonth = \Carbon\Carbon::createFromFormat('Y-m', $monthParam)->startOfMonth();
            } catch (\Exception $e) {
                $selectedMonth = $now->copy()->startOfMonth();
            }
        } else {
            $selectedMonth = $now->copy()->startOfMonth();
        }

        $monthStart = $selectedMonth->copy()->startOfMonth();
        $monthEnd = $selectedMonth->copy()->endOfMonth();
        $prevMonthStart = $monthStart->copy()->subMonth()->startOfMonth();
        $prevMonthEnd = $monthStart->copy()->subMonth()->endOfMonth();

        // Generate list bulan (12 bulan terakhir)
        $monthOptions = [];
        for ($i = 0; $i < 12; $i++) {
            $m = $now->copy()->subMonths($i)->startOfMonth();
            $monthOptions[] = [
                'value' => $m->format('Y-m'),
                'label' => $m->translatedFormat('F Y'),
            ];
        }

        // ============================================
        // 🔥 HELPER: Filter HANYA order SELESAI & tidak retur
        // ============================================
        $filterCompletedOrders = function ($query) {
            return $query
                ->where('shipping_status', 'delivered')
                ->where(function ($q) {
                    $q->whereNull('return_status')
                    ->orWhere('return_status', 'rejected');
                });
        };

        // ============================================
        // 🔥 HELPER: Combined Total Penjualan (orders.total = subtotal - voucher + ongkir)
        // ============================================
        $getCombinedSales = function ($start, $end = null) {
            $onlineQuery = Order::query()
                ->where('payment_status', 'paid')
                ->where('shipping_status', 'delivered')
                ->where(function ($q) {
                    $q->whereNull('return_status')
                    ->orWhere('return_status', 'rejected');
                })
                ->when($end, fn($q) => $q->whereBetween('created_at', [$start, $end]))
                ->when(!$end, fn($q) => $q->where('created_at', '>=', $start));

            $offlineQuery = OfflineOrder::query()
                ->where('payment_status', 'paid')
                ->where('shipping_status', 'delivered')
                ->where(function ($q) {
                    $q->whereNull('return_status')
                    ->orWhere('return_status', 'rejected');
                })
                ->when($end, fn($q) => $q->whereBetween('created_at', [$start, $end]))
                ->when(!$end, fn($q) => $q->where('created_at', '>=', $start));

            return (float) $onlineQuery->sum('total')
                + (float) $offlineQuery->sum('total');
        };

        // ============================================
        // 🔥 3 CARD ATAS (7/30/90 hari) — TIDAK FILTER BULAN
        // ============================================
        $sales_7d  = $getCombinedSales($now->copy()->subDays(7));
        $sales_30d = $getCombinedSales($now->copy()->subDays(30));
        $sales_90d = $getCombinedSales($now->copy()->subDays(90));

        $prev_7d  = $getCombinedSales($now->copy()->subDays(14), $now->copy()->subDays(7));
        $prev_30d = $getCombinedSales($now->copy()->subDays(60), $now->copy()->subDays(30));
        $prev_90d = $getCombinedSales($now->copy()->subDays(180), $now->copy()->subDays(90));

        $change_7d  = $prev_7d  > 0 ? round((($sales_7d  - $prev_7d)  / $prev_7d)  * 100, 1) : 0;
        $change_30d = $prev_30d > 0 ? round((($sales_30d - $prev_30d) / $prev_30d) * 100, 1) : 0;
        $change_90d = $prev_90d > 0 ? round((($sales_90d - $prev_90d) / $prev_90d) * 100, 1) : 0;

        // ============================================
        // 🔥 CHART (7 HARI) — TOTAL TRANSAKSI (termasuk ongkir, net diskon)
        // ============================================
        $chartDefaultStart = $now->copy()->subDays(6)->startOfDay();

        // Daily online — total transaksi
        $dailyOnline = Order::query()
            ->where('payment_status', 'paid')
            ->where('shipping_status', 'delivered')
            ->where('created_at', '>=', $chartDefaultStart)
            ->where(function ($q) {
                $q->whereNull('return_status')
                ->orWhere('return_status', 'rejected');
            })
            ->selectRaw('DATE(created_at) as date, SUM(total) as sales, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Daily offline — total transaksi
        $dailyOffline = OfflineOrder::query()
            ->where('payment_status', 'paid')
            ->where('shipping_status', 'delivered')
            ->where('created_at', '>=', $chartDefaultStart)
            ->where(function ($q) {
                $q->whereNull('return_status')
                ->orWhere('return_status', 'rejected');
            })
            ->selectRaw('DATE(created_at) as date, SUM(total) as sales, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $chartLabels = [];
        $chartSales  = [];
        $chartOrders = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i)->format('Y-m-d');
            $online  = $dailyOnline[$day] ?? null;
            $offline = $dailyOffline[$day] ?? null;

            $chartLabels[] = $now->copy()->subDays($i)->format('d M');
            $chartSales[]  = (float) (($online->sales ?? 0) + ($offline->sales ?? 0));
            $chartOrders[] = (int)   (($online->orders ?? 0) + ($offline->orders ?? 0));
        }

        // ============================================
        // 🔥 SPARKLINE 7 HARI
        // ============================================
        $sparkline_7d = $this->generateSparklinePath($chartSales, 300, 60);

        // ============================================
        // 🔥 SPARKLINE 30 HARI
        // ============================================
        $daily30Online = Order::query()
            ->where('payment_status', 'paid')
            ->where('shipping_status', 'delivered')
            ->where('created_at', '>=', $now->copy()->subDays(29)->startOfDay())
            ->where(function ($q) {
                $q->whereNull('return_status')
                ->orWhere('return_status', 'rejected');
            })
            ->selectRaw('DATE(created_at) as date, SUM(total) as sales')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $daily30Offline = OfflineOrder::query()
            ->where('payment_status', 'paid')
            ->where('shipping_status', 'delivered')
            ->where('created_at', '>=', $now->copy()->subDays(29)->startOfDay())
            ->where(function ($q) {
                $q->whereNull('return_status')
                ->orWhere('return_status', 'rejected');
            })
            ->selectRaw('DATE(created_at) as date, SUM(total) as sales')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $sales30 = [];
        for ($i = 29; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i)->format('Y-m-d');
            $sales30[] = (float) (($daily30Online[$day]->sales ?? 0) + ($daily30Offline[$day]->sales ?? 0));
        }
        $sparkline_30d = $this->generateSparklinePath($sales30, 300, 60);

        // ============================================
        // 🔥 SPARKLINE 90 HARI
        // ============================================
        $daily90Online = Order::query()
            ->where('payment_status', 'paid')
            ->where('shipping_status', 'delivered')
            ->where('created_at', '>=', $now->copy()->subDays(89)->startOfDay())
            ->where(function ($q) {
                $q->whereNull('return_status')
                ->orWhere('return_status', 'rejected');
            })
            ->selectRaw('DATE(created_at) as date, SUM(total) as sales')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $daily90Offline = OfflineOrder::query()
            ->where('payment_status', 'paid')
            ->where('shipping_status', 'delivered')
            ->where('created_at', '>=', $now->copy()->subDays(89)->startOfDay())
            ->where(function ($q) {
                $q->whereNull('return_status')
                ->orWhere('return_status', 'rejected');
            })
            ->selectRaw('DATE(created_at) as date, SUM(total) as sales')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $sales90 = [];
        for ($i = 89; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i)->format('Y-m-d');
            $sales90[] = (float) (($daily90Online[$day]->sales ?? 0) + ($daily90Offline[$day]->sales ?? 0));
        }
        $sparkline_90d = $this->generateSparklinePath($sales90, 300, 60);

        // ============================================
        // 🔥 4 CARD STATS (TENGAH) — FILTER BULAN + TOTAL TRANSAKSI
        // ============================================

        // Total Penjualan Online (bulan) — total transaksi (termasuk ongkir, net diskon)
        $cashIn = (float) Order::query()
            ->where('payment_status', 'paid')
            ->where('shipping_status', 'delivered')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->where(function ($q) {
                $q->whereNull('return_status')
                ->orWhere('return_status', 'rejected');
            })
            ->sum('total');

        $prevCashIn = (float) Order::query()
            ->where('payment_status', 'paid')
            ->where('shipping_status', 'delivered')
            ->whereBetween('created_at', [$prevMonthStart, $prevMonthEnd])
            ->where(function ($q) {
                $q->whereNull('return_status')
                ->orWhere('return_status', 'rejected');
            })
            ->sum('total');

        $changeCashIn = $prevCashIn > 0
            ? round((($cashIn - $prevCashIn) / $prevCashIn) * 100, 1)
            : 0;

        // Total Penjualan Offline (bulan) — total transaksi (termasuk ongkir, net diskon)
        $offlineSales = (float) OfflineOrder::query()
            ->where('payment_status', 'paid')
            ->where('shipping_status', 'delivered')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->where(function ($q) {
                $q->whereNull('return_status')
                ->orWhere('return_status', 'rejected');
            })
            ->sum('total');

        $prevOfflineSales = (float) OfflineOrder::query()
            ->where('payment_status', 'paid')
            ->where('shipping_status', 'delivered')
            ->whereBetween('created_at', [$prevMonthStart, $prevMonthEnd])
            ->where(function ($q) {
                $q->whereNull('return_status')
                ->orWhere('return_status', 'rejected');
            })
            ->sum('total');

        $changeOffline = $prevOfflineSales > 0
            ? round((($offlineSales - $prevOfflineSales) / $prevOfflineSales) * 100, 1)
            : 0;

        // Total Order (bulan) — gabungan online + offline
        $totalOrdersOnlineQ = Order::whereBetween('created_at', [$monthStart, $monthEnd]);
        $filterCompletedOrders($totalOrdersOnlineQ);
        $totalOrdersOnline = $totalOrdersOnlineQ->count();

        $totalOrdersOfflineQ = OfflineOrder::whereBetween('created_at', [$monthStart, $monthEnd]);
        $filterCompletedOrders($totalOrdersOfflineQ);
        $totalOrdersOffline = $totalOrdersOfflineQ->count();

        $totalOrders = $totalOrdersOnline + $totalOrdersOffline;

        $prevTotalOrdersOnlineQ = Order::whereBetween('created_at', [$prevMonthStart, $prevMonthEnd]);
        $filterCompletedOrders($prevTotalOrdersOnlineQ);
        $prevTotalOrdersOnline = $prevTotalOrdersOnlineQ->count();

        $prevTotalOrdersOfflineQ = OfflineOrder::whereBetween('created_at', [$prevMonthStart, $prevMonthEnd]);
        $filterCompletedOrders($prevTotalOrdersOfflineQ);
        $prevTotalOrdersOffline = $prevTotalOrdersOfflineQ->count();

        $prevTotalOrders = $prevTotalOrdersOnline + $prevTotalOrdersOffline;

        $changeOrders = $prevTotalOrders > 0
            ? round((($totalOrders - $prevTotalOrders) / $prevTotalOrders) * 100, 1)
            : 0;

        // AOV (bulan) — total transaksi ÷ order count
        $totalRevenue = $cashIn + $offlineSales;
        $aov = $totalOrders > 0 ? round($totalRevenue / $totalOrders) : 0;

        $prevTotalRevenue = $prevCashIn + $prevOfflineSales;
        $prevAOV = $prevTotalOrders > 0 ? round($prevTotalRevenue / $prevTotalOrders) : 0;
        $changeAOV = $prevAOV > 0 ? round((($aov - $prevAOV) / $prevAOV) * 100, 1) : 0;

        // ============================================
        // 🔥 TOP 5 PRODUK TERLARIS — 90 HARI
        // ============================================
        $topProductsOnlineQ = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.payment_status', 'paid')
            ->where('orders.created_at', '>=', $now->copy()->subDays(90))
            ->where('orders.shipping_status', 'delivered')
            ->where(function ($q) {
                $q->whereNull('orders.return_status')
                ->orWhere('orders.return_status', 'rejected');
            });

        $topProductsOnline = $topProductsOnlineQ
            ->selectRaw('order_items.product_name, order_items.variant_name as variant, SUM(order_items.quantity) as sold, SUM(order_items.subtotal - (orders.discount * order_items.subtotal / NULLIF(orders.subtotal, 0))) as revenue')
            ->groupBy('order_items.product_name', 'order_items.variant_name')
            ->get();

        $topProductsOfflineQ = OfflineOrderItem::query()
            ->join('offline_orders', 'offline_orders.id', '=', 'offline_order_items.offline_order_id')
            ->where('offline_orders.payment_status', 'paid')
            ->where('offline_orders.created_at', '>=', $now->copy()->subDays(90))
            ->where('offline_orders.shipping_status', 'delivered')
            ->where(function ($q) {
                $q->whereNull('offline_orders.return_status')
                ->orWhere('offline_orders.return_status', 'rejected');
            });

        $topProductsOffline = $topProductsOfflineQ
            ->selectRaw('offline_order_items.product_name, offline_order_items.variant_name as variant, SUM(offline_order_items.quantity) as sold, SUM(offline_order_items.subtotal - (offline_orders.discount * offline_order_items.subtotal / NULLIF(offline_orders.subtotal, 0))) as revenue')
            ->groupBy('offline_order_items.product_name', 'offline_order_items.variant_name')
            ->get();

        $topProducts = $topProductsOnline
            ->concat($topProductsOffline)
            ->groupBy(fn($item) => $item->product_name . '|' . ($item->variant ?? ''))
            ->map(function ($group) {
                return (object) [
                    'product_name' => $group->first()->product_name,
                    'variant' => $group->first()->variant,
                    'sold' => $group->sum('sold'),
                    'revenue' => $group->sum('revenue'),
                ];
            })
            ->sortByDesc('revenue')
            ->take(5)
            ->values();

        // ============================================
        // STATS ARRAY
        // ============================================
        $stats = [
            'sales_7d'       => $sales_7d,
            'sales_30d'      => $sales_30d,
            'sales_90d'      => $sales_90d,
            'change_7d'      => $change_7d,
            'change_30d'     => $change_30d,
            'change_90d'     => $change_90d,
            'sparkline_7d'   => $sparkline_7d,
            'sparkline_30d'  => $sparkline_30d,
            'sparkline_90d'  => $sparkline_90d,
            'chart_labels'   => $chartLabels,
            'chart_sales'    => $chartSales,
            'chart_orders'   => $chartOrders,
            'cash_in'        => $cashIn,
            'change_cash_in' => $changeCashIn,
            'sales_offline'  => $offlineSales,
            'change_offline' => $changeOffline,
            'total_orders'   => $totalOrders,
            'change_orders'  => $changeOrders,
            'aov'            => $aov,
            'change_aov'     => $changeAOV,
            'month_label'    => $selectedMonth->translatedFormat('F Y'),
            'month_param'    => $selectedMonth->format('Y-m'),
        ];

        // ============================================
        // STOCK DATA
        // ============================================
        $criticalProducts = Product::with(['variants', 'category'])
            ->where('is_active', true)->criticalStock()->get()
            ->map(fn($p) => tap($p, fn($x) => $x->total_stock = $x->variants->sum('stock')));

        $lowProducts = Product::with(['variants', 'category'])
            ->where('is_active', true)->lowStock()->get()
            ->map(fn($p) => tap($p, fn($x) => $x->total_stock = $x->variants->sum('stock')));

        $outOfStockProducts = Product::with(['variants', 'category'])
            ->where('is_active', true)
            ->whereHas('variants', fn($q) => $q->selectRaw('SUM(stock) as total_stock')->havingRaw('SUM(stock) = 0'))
            ->get()
            ->map(fn($p) => tap($p, fn($x) => $x->total_stock = $x->variants->sum('stock')));

        $inStockProducts = Product::with(['variants', 'category'])
            ->where('is_active', true)->inStock()->get()
            ->map(fn($p) => tap($p, fn($x) => $x->total_stock = $x->variants->sum('stock')));

        return view('admin.dashboard', compact(
            'stats',
            'topProducts',
            'criticalProducts',
            'lowProducts',
            'outOfStockProducts',
            'inStockProducts',
            'monthOptions'
        ));
    }

    /**
     * 🔥 AJAX: Get chart data berdasarkan filter periode
     */
    public function getChartData(Request $request)
    {
        $period = (int) $request->input('period', 7);
        if (!in_array($period, [7, 30, 90])) {
            $period = 7;
        }

        $now = now();
        $startDate = $now->copy()->subDays($period - 1)->startOfDay();

        // 🔥 Online — total transaksi (termasuk ongkir, net diskon)
        $dailyOnline = Order::query()
            ->where('payment_status', 'paid')
            ->where('shipping_status', 'delivered')
            ->where('created_at', '>=', $startDate)
            ->where(function ($q) {
                $q->whereNull('return_status')
                ->orWhere('return_status', 'rejected');
            })
            ->selectRaw('DATE(created_at) as date, SUM(total) as sales, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // 🔥 Offline — total transaksi (termasuk ongkir, net diskon)
        $dailyOffline = OfflineOrder::query()
            ->where('payment_status', 'paid')
            ->where('shipping_status', 'delivered')
            ->where('created_at', '>=', $startDate)
            ->where(function ($q) {
                $q->whereNull('return_status')
                ->orWhere('return_status', 'rejected');
            })
            ->selectRaw('DATE(created_at) as date, SUM(total) as sales, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $sales = [];
        $orders = [];

        for ($i = $period - 1; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i)->format('Y-m-d');
            $label = $now->copy()->subDays($i)->format('d M');

            $online  = $dailyOnline[$day] ?? null;
            $offline = $dailyOffline[$day] ?? null;

            $labels[] = $label;
            $sales[]  = (float) (($online->sales ?? 0) + ($offline->sales ?? 0));
            $orders[] = (int)   (($online->orders ?? 0) + ($offline->orders ?? 0));
        }

        return response()->json([
            'success' => true,
            'period'  => $period,
            'labels'  => $labels,
            'sales'   => $sales,
            'orders'  => $orders,
            'total_sales'  => array_sum($sales),
            'total_orders' => array_sum($orders),
        ]);
    }

    /**
     * Generate an SVG path string for a sparkline from an array of values.
     */
    private function generateSparklinePath(array $values, int $width, int $height): string
    {
        if (empty($values)) {
            return '';
        }

        $max = max($values);
        $min = min($values);
        $range = $max - $min;
        if ($range == 0) {
            $range = 1;
        }

        $padding = 10;
        $chartHeight = $height - $padding * 2;
        $chartWidth = $width - 20;
        $step = $chartWidth / (count($values) - 1);

        $points = [];
        foreach ($values as $i => $val) {
            $x = 10 + $i * $step;
            $y = $height - $padding - (($val - $min) / $range) * $chartHeight;
            $points[] = $x . ',' . round($y, 1);
        }

        $d = 'M' . $points[0];
        for ($i = 1; $i < count($points); $i++) {
            $midX = (floatval(explode(',', $points[$i - 1])[0]) + floatval(explode(',', $points[$i])[0])) / 2;
            $d .= ' Q' . $midX . ',' . $height - $padding . ' ' . $points[$i];
        }

        return $d;
    }

    /**
     * Generate an SVG path fill string for a sparkline.
     */
    private function generateSparklineFill(array $values, int $width, int $height): string
    {
        if (empty($values)) {
            return '';
        }

        $max = max($values);
        $min = min($values);
        $range = $max - $min;
        if ($range == 0) {
            $range = 1;
        }

        $padding = 10;
        $chartHeight = $height - $padding * 2;
        $chartWidth = $width - 20;
        $step = $chartWidth / (count($values) - 1);

        $points = [];
        foreach ($values as $i => $val) {
            $x = 10 + $i * $step;
            $y = $height - $padding - (($val - $min) / $range) * $chartHeight;
            $points[] = $x . ',' . round($y, 1);
        }

        $d = 'M' . $points[0];
        for ($i = 1; $i < count($points); $i++) {
            $midX = (floatval(explode(',', $points[$i - 1])[0]) + floatval(explode(',', $points[$i])[0])) / 2;
            $d .= ' Q' . $midX . ',' . $height - $padding . ' ' . $points[$i];
        }
        $d .= ' L' . $width . ',' . $height;
        $d .= ' L0,' . $height . ' Z';

        return $d;
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE PRODUCT OPTIONS WITH EXISTING IMAGES - PERBAIKI
    |--------------------------------------------------------------------------
    */

    private function createProductOptionsWithExistingImages(
        Product $product,
        array $options,
        array $optionFiles = [],
        array $oldOptionValuesMap = []
    ): array {
        $optionValueMap = [];

        foreach ($options as $optionIndex => $optionData) {
            // BUAT OPTION
            $option = $product->options()->create([
                'name' => trim($optionData['name']),
                'sort_order' => $optionIndex,
            ]);

            $optionValueMap[$optionIndex] = [];

            $values = $optionData['values'] ?? [];
            
            // FILTER DUPLIKAT VALUE
            $uniqueValues = [];
            $uniqueValueIndexes = [];
            foreach ($values as $idx => $value) {
                $trimmed = trim($value);
                if (empty($trimmed)) continue;
                if (!in_array($trimmed, $uniqueValues)) {
                    $uniqueValues[] = $trimmed;
                    $uniqueValueIndexes[] = $idx;
                }
            }
            
            $newImages = $optionFiles[$optionIndex]['images'] ?? [];
            $oldOptionId = $optionData['old_id'] ?? null;
            $oldValueIds = $optionData['old_value_ids'] ?? [];
            $existingImages = $optionData['existing_images'] ?? [];

            $valueIndex = 0;
            foreach ($uniqueValueIndexes as $originalIndex => $uniqueIdx) {
                $value = $uniqueValues[$originalIndex];
                
                $imagePath = null;
                $newImage = $newImages[$valueIndex] ?? null;
                $selectedExistingImage = $existingImages[$valueIndex] ?? null;
                $oldValueId = $oldValueIds[$valueIndex] ?? null;

                // 1. Prioritas: upload baru dari input file
                if ($newImage instanceof \Illuminate\Http\UploadedFile) {
                    // Hapus gambar lama
                    if ($oldOptionId && $oldValueId && isset($oldOptionValuesMap[$oldOptionId][$oldValueId])) {
                        $oldImagePath = $oldOptionValuesMap[$oldOptionId][$oldValueId];
                        $this->imageOptimizer->delete($oldImagePath);
                    }

                    // 🔥 CONVERT KE WEBP
                    if ($newImage->isValid()) {
                        $imagePath = $this->imageOptimizer->convertToWebp(
                            file: $newImage,
                            folder: 'products/option-values',
                            maxWidth: 400,
                            quality: 85
                        );
                    }
                }
                // 2. Jika tidak ada upload baru, pertahankan gambar lama
                elseif (!empty($selectedExistingImage)) {
                    $imagePath = $this->normalizeStoredImagePath($selectedExistingImage);
                }
                // 3. Cek mapping value lama
                elseif ($oldOptionId && isset($oldOptionValuesMap[$oldOptionId])) {
                    if ($oldValueId && isset($oldOptionValuesMap[$oldOptionId][$oldValueId])) {
                        $imagePath = $oldOptionValuesMap[$oldOptionId][$oldValueId];
                    } else {
                        // Cari berdasarkan urutan
                        $oldValueIdList = array_keys($oldOptionValuesMap[$oldOptionId]);
                        if (isset($oldValueIdList[$valueIndex])) {
                            $mappedOldValueId = $oldValueIdList[$valueIndex];
                            $imagePath = $oldOptionValuesMap[$oldOptionId][$mappedOldValueId] ?? null;
                        }
                    }
                }

                // BUAT OPTION VALUE
                $optionValue = $option->values()->create([
                    'value' => trim($value),
                    'image' => $imagePath,
                    'sort_order' => $valueIndex,
                ]);

                // SIMPAN MAPPING
                $optionValueMap[$optionIndex][$valueIndex] = $optionValue->id;
                $valueIndex++;
            }
        }

        return $optionValueMap;
    }


    /*
    |--------------------------------------------------------------------------
    | NORMALIZE STORED IMAGE PATH
    |--------------------------------------------------------------------------
    */

    private function normalizeStoredImagePath(?string $imagePath): ?string
    {
        if (empty($imagePath)) {
            return null;
        }

        if (preg_match('#^https?://#i', $imagePath)) {
            $parsed = parse_url($imagePath, PHP_URL_PATH);
            $imagePath = $parsed ?: $imagePath;
        }

        $storagePrefix = '/storage/';
        if (str_starts_with($imagePath, $storagePrefix)) {
            $imagePath = substr($imagePath, strlen($storagePrefix));
        }

        return ltrim($imagePath, '/');
    }


    /*
    |--------------------------------------------------------------------------
    | DESTROY PRODUCT
    |--------------------------------------------------------------------------
    */

    public function destroy(Product $product)
    {
        $product->load([
            'images',
            'options.values',
            'variants',
            'orderItems',
        ]);

        if ($product->orderItems()->exists()) {
            return redirect()
                ->route('admin.products.index')
                ->with('error', 'Produk tidak dapat dihapus karena sudah digunakan pada pesanan.');
        }

        $imagePaths = $product
            ->images
            ->pluck('image')
            ->filter()
            ->values()
            ->toArray();

        try {
            DB::transaction(function () use ($product) {
                // 1. HAPUS VARIANT VALUES (PIVOT)
                $variantIds = $product
                    ->variants
                    ->pluck('id')
                    ->toArray();

                if (!empty($variantIds)) {
                    ProductVariantValue::whereIn(
                        'product_variant_id',
                        $variantIds
                    )->delete();
                }

                // 2. HAPUS VARIANTS
                $product->variants()->delete();

                // 3. HAPUS OPTION VALUES
                $optionIds = $product
                    ->options
                    ->pluck('id')
                    ->toArray();

                if (!empty($optionIds)) {
                    ProductOptionValue::whereIn(
                        'product_option_id',
                        $optionIds
                    )->delete();
                }

                // 4. HAPUS OPTIONS
                $product->options()->delete();

                // 5. HAPUS IMAGES (DATABASE)
                $product->images()->delete();

                // 6. HAPUS PRODUCT
                $product->delete();
            });

            // HAPUS FILE GAMBAR DARI STORAGE
           foreach ($imagePaths as $imagePath) {
                $this->imageOptimizer->delete($imagePath);
            }

            // 🔥 HAPUS FILE GAMBAR OPSI (WARNA)
            foreach ($product->options as $option) {
                foreach ($option->values as $value) {
                    $this->imageOptimizer->delete($value->image);
                }
            }

            CustomerHomeController::clearCache();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Produk berhasil dihapus.');

        } catch (Throwable $e) {
            report($e);

            return redirect()
                ->route('admin.products.index')
                ->with('error', 'Produk gagal dihapus: ' . $e->getMessage());
        }
    }


    /*
    |--------------------------------------------------------------------------
    | BULK DESTROY
    |--------------------------------------------------------------------------
    */

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'required|integer|exists:products,id',
        ]);

        $productIds = $request->product_ids;
        $deletedCount = 0;
        $failedIds = [];
        $failedNames = [];

        try {
            DB::transaction(function () use ($productIds, &$deletedCount, &$failedIds, &$failedNames) {
                
                $products = Product::with(['images', 'orderItems', 'options.values'])
                    ->whereIn('id', $productIds)
                    ->get();

                foreach ($products as $product) {
                    if ($product->orderItems()->exists()) {
                        $failedIds[] = $product->id;
                        $failedNames[] = $product->name;
                        continue;
                    }

                    $product->load('options.values');

                    $imagePaths = $product->images->pluck('image')->filter()->toArray();

                    $variantIds = $product->variants->pluck('id')->toArray();
                    if (!empty($variantIds)) {
                        ProductVariantValue::whereIn('product_variant_id', $variantIds)->delete();
                    }

                    $product->variants()->delete();

                    $optionIds = $product->options->pluck('id')->toArray();
                    if (!empty($optionIds)) {
                        ProductOptionValue::whereIn('product_option_id', $optionIds)->delete();
                    }

                    $product->options()->delete();
                    $product->images()->delete();
                    $product->delete();

                    foreach ($imagePaths as $imagePath) {
                        $this->imageOptimizer->delete($imagePath);
                    }

                    foreach ($product->options as $option) {
                        foreach ($option->values as $value) {
                            $this->imageOptimizer->delete($value->image);
                        }
                    }

                    $deletedCount++;
                }
            });

            $message = "Berhasil menghapus {$deletedCount} produk.";

            if (!empty($failedIds)) {
                $message .= " Gagal menghapus " . count($failedIds) . " produk: " . implode(', ', $failedNames) . " (sudah digunakan di pesanan).";
            }

            CustomerHomeController::clearCache();
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted' => $deletedCount,
                'failed' => $failedIds,
                'failed_names' => $failedNames,
            ]);

        } catch (Throwable $e) {
            \Log::error('Bulk delete error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus produk: ' . $e->getMessage(),
            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | DESTROY IMAGE
    |--------------------------------------------------------------------------
    */

    public function destroyImage(Product $product, $image)
    {
        $productImage = $product->images()->findOrFail($image);
        $path = $productImage->image;

        DB::transaction(function () use ($productImage) {
            $productImage->delete();
        });

        // 🔥 HAPUS VIA HELPER
        $this->imageOptimizer->delete($path);

        return back()
            ->with('success', 'Gambar produk berhasil dihapus.');
    }


    /*
    |--------------------------------------------------------------------------
    | CHECK SKU
    |--------------------------------------------------------------------------
    */

    public function checkSku(Request $request)
    {
        try {
            // 🔥 VALIDASI REQUEST - PERBAIKI
            $rules = [
                'skus' => 'required|array',
                'skus.*' => 'required|string|max:100',
                'product_id' => 'nullable|integer|exists:products,id', // nullable agar create bisa
                'is_edit' => 'nullable|boolean',
            ];

            $validated = $request->validate($rules);

            $skus = array_map('trim', $request->skus);
            $skus = array_filter($skus, function($sku) {
                return !empty($sku);
            });

            if (empty($skus)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tidak ada SKU untuk diperiksa.'
                ]);
            }

            // 🔥 CEK DUPLIKAT DALAM FORM
            $counts = array_count_values($skus);
            $duplicates = [];
            foreach ($counts as $sku => $count) {
                if ($count > 1 && !empty($sku)) {
                    $duplicates[] = $sku;
                }
            }

            if (!empty($duplicates)) {
                return response()->json([
                    'success' => false,
                    'message' => 'SKU ' . implode(', ', $duplicates) . ' duplikat dalam form.',
                    'skus' => $duplicates,
                ], 422);
            }

            // 🔥 CEK SKU DI DATABASE
            $query = \App\Models\ProductVariant::whereIn('sku', $skus);
            
            // Jika mode edit dan ada product_id, exclude product yang sedang diedit
            if ($request->is_edit && $request->filled('product_id') && $request->product_id > 0) {
                $query->where('product_id', '!=', $request->product_id);
            }
            
            $existingSkus = $query->pluck('sku')->toArray();

            if (!empty($existingSkus)) {
                return response()->json([
                    'success' => false,
                    'message' => 'SKU ' . implode(', ', $existingSkus) . ' sudah digunakan di produk lain.',
                    'skus' => $existingSkus,
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'Semua SKU tersedia.'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Check SKU validation failed:', [
                'errors' => $e->errors(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Check SKU error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDATE PRODUCT
    |--------------------------------------------------------------------------
    */

    private function validateProduct(Request $request, ?Product $product = null): array
    {
        $isEdit = $product !== null;
        
        $rules = [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'gender' => ['nullable', 'string', 'in:pria,wanita,unisex'],
            'material' => ['nullable', 'string', 'max:255'],
            'features' => ['nullable', 'array'],
            'features.*' => ['exists:features,id'],
            'minimum_stock' => ['nullable', 'integer', 'min:0'],
            'restock_threshold' => ['nullable', 'integer', 'min:0'],
            'options' => ['nullable', 'array'],
            'options.*.name' => ['required_with:options', 'string', 'max:100'],
            'options.*.values' => ['required_with:options', 'array', 'min:1'],
            'options.*.values.*' => ['required_with:options', 'string', 'max:100'],
            'options.*.old_id' => ['nullable', 'integer'],
            'options.*.old_value_ids' => ['nullable', 'array'],
            'options.*.old_value_ids.*' => ['nullable', 'integer'],
            'options.*.existing_images' => ['nullable', 'array'],
            'options.*.existing_images.*' => ['nullable', 'string'],
            'options.*.images' => ['nullable', 'array'],
            'options.*.images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            'variants' => ['nullable', 'array'],
            'variants.*.id' => ['nullable', 'integer'],
            'variants.*.sku' => ['required_with:variants', 'string', 'max:100'],
            'variants.*.price' => ['required_with:variants', 'numeric', 'min:0'],
            'variants.*.discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'variants.*.discount_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.stock' => ['nullable', 'integer', 'min:0'],
            'variants.*.weight' => ['required_with:variants', 'integer', 'min:0'],
            'variants.*.option_value_indexes' => ['required_with:variants', 'array', 'min:1'],
            'variants.*.option_value_indexes.*' => ['required_with:variants', 'integer', 'min:0'],
            'has_product_discount' => ['nullable', 'boolean'],
            'discount_type' => ['nullable', 'required_if:has_product_discount,1', 'in:percentage,fixed'],
            'discount_value' => ['nullable', 'required_if:has_product_discount,1', 'numeric', 'min:0'],
            'is_featured' => ['nullable', 'boolean'],
            'is_best_seller' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'is_flash_sale' => ['nullable', 'boolean'],
            'flash_sale_type' => ['nullable', 'required_if:is_flash_sale,1', 'in:percentage,fixed'],
            'flash_sale_value' => ['nullable', 'required_if:is_flash_sale,1', 'numeric', 'min:0'],
            'flash_sale_start_date' => ['nullable', 'required_if:is_flash_sale,1', 'date'],
            'flash_sale_end_date' => ['nullable', 'required_if:is_flash_sale,1', 'date', 'after_or_equal:flash_sale_start_date'],
        ];

        $validated = $request->validate($rules);

        // 🔥 PASTIKAN SEMUA CHECKBOX TERSIMPAN DENGAN BENAR
        $validated['has_product_discount'] = $request->boolean('has_product_discount');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_best_seller'] = $request->boolean('is_best_seller');
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_flash_sale'] = $request->boolean('is_flash_sale');

        // 🔥 Jika diskon produk tidak aktif, hapus nilai diskon
        if (!$validated['has_product_discount']) {
            $validated['discount_type'] = null;
            $validated['discount_value'] = null;
        }

        // 🔥 Jika flash sale tidak aktif, hapus nilai flash sale
        if (!$validated['is_flash_sale']) {
            $validated['flash_sale_type'] = null;
            $validated['flash_sale_value'] = null;
            $validated['flash_sale_start_date'] = null;
            $validated['flash_sale_end_date'] = null;
        }

        return $validated;
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE PRODUCT OPTIONS - PERBAIKI
    |--------------------------------------------------------------------------
    */

    private function createProductOptions(Product $product, array $options, array $optionFiles = []): array
    {
        $optionValueMap = [];
        $options = array_values($options);

        foreach ($options as $optionIndex => $optionData) {
            $option = $product->options()->create([
                'name' => trim($optionData['name']),
                'sort_order' => $optionIndex,
            ]);

            $optionValueMap[$optionIndex] = [];
            $values = array_values($optionData['values'] ?? []);
            
            $images = $optionFiles[$optionIndex]['images'] ?? [];

            foreach ($values as $valueIndex => $value) {
                $trimmed = trim($value);
                if ($trimmed === '') continue;

                $imagePath = null;
                
                // 🔥 CONVERT OPSI IMAGE KE WEBP
                if (isset($images[$valueIndex]) && $images[$valueIndex] instanceof \Illuminate\Http\UploadedFile) {
                    $uploadedFile = $images[$valueIndex];
                    
                    if ($uploadedFile->isValid()) {
                        $imagePath = $this->imageOptimizer->convertToWebp(
                            file: $uploadedFile,
                            folder: 'products/option-values',
                            maxWidth: 400,   // option value image (warna) max 400px
                            quality: 85
                        );
                    }
                }

                $optionValue = $option->values()->create([
                    'value' => $trimmed,
                    'image' => $imagePath,
                    'sort_order' => $valueIndex,
                ]);

                $optionValueMap[$optionIndex][$valueIndex] = $optionValue->id;
            }
        }

        return $optionValueMap;
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE PRODUCT VARIANTS - PERBAIKI
    |--------------------------------------------------------------------------
    */

    private function createProductVariants(
        Product $product,
        array $variants,
        array $optionValueMap
    ): void {
        $createdVariantKeys = [];
        
        foreach ($variants as $variantIndex => $variantData) {
            $optionValueIndexes = $variantData['option_value_indexes'] ?? [];
            $optionValueIds = [];

            // Mapping option values
            foreach ($optionValueIndexes as $optionIndex => $valueIndex) {
                $optionIndex = (int) $optionIndex;
                $valueIndex = (int) $valueIndex;
                
                if (isset($optionValueMap[$optionIndex][$valueIndex])) {
                    $optionValueIds[] = (int) $optionValueMap[$optionIndex][$valueIndex];
                }
            }

            // Jika tidak ada option value ids, skip
            if (empty($optionValueIds)) {
                \Log::warning('Variant skipped - no option values', ['variant' => $variantData]);
                continue;
            }

            $optionValueIds = array_unique($optionValueIds);
            sort($optionValueIds);
            
            $variantKey = implode('|', $optionValueIds);
            
            if (in_array($variantKey, $createdVariantKeys)) {
                continue;
            }
            $createdVariantKeys[] = $variantKey;

            // Hitung harga
            $price = (float) ($variantData['price'] ?? 0);
            $discountPercent = (float) ($variantData['discount_percent'] ?? 0);
            
            $discountPrice = null;
            if ($discountPercent > 0 && $price > 0) {
                $discountPrice = round($price - ($price * ($discountPercent / 100)), 2);
            }

            $stock = isset($variantData['stock']) && $variantData['stock'] !== '' ? (int) $variantData['stock'] : 0;
            $sku = trim($variantData['sku'] ?? 'SKU-' . strtoupper(Str::random(8)));

            // Buat variant
            $variant = $product->variants()->create([
                'sku' => $sku,
                'price' => $price,
                'discount_price' => $discountPrice,
                'stock' => $stock,
                'weight' => (int) ($variantData['weight'] ?? 1000),
                'is_active' => true,
            ]);

            // Simpan option values
            foreach ($optionValueIds as $optionValueId) {
                $variant->variantValues()->create([
                    'product_option_value_id' => $optionValueId,
                ]);
            }
        }
    }

    public function stockHistory(Product $product)
    {
        $histories = StockHistory::with(['variant', 'user'])
            ->where('product_id', $product->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.products.stock-history', compact('product', 'histories'));
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE UNIQUE SLUG
    |--------------------------------------------------------------------------
    */

    private function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (
            Product::query()
                ->where('slug', $slug)
                ->when($ignoreId, function ($query) use ($ignoreId) {
                    $query->where('id', '!=', $ignoreId);
                })
                ->exists()
        ) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}