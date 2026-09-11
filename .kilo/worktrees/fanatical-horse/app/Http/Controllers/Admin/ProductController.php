<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Feature;
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

class ProductController extends Controller
{
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
                        $path = $image->store('products', 'public');
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
        // 🔥 LOAD SEMUA RELASI DENGAN BENAR
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
            'features',
        ]);

        // 🔥 BUILD EXISTING OPTIONS - PERBAIKI
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
            // Ambil option value IDs
            $optionValueIds = $variant->variantValues
                ->pluck('product_option_value_id')
                ->map(function($id) { return (int) $id; })
                ->values()
                ->toArray();
            
            // Ambil value names dengan urutan yang benar
            $valueNames = $variant->variantValues
                ->map(function($vv) {
                    return $vv->optionValue->value ?? '';
                })
                ->filter()
                ->values()
                ->toArray();
            
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
                    if (Storage::disk('public')->exists($oldImage->image)) {
                        Storage::disk('public')->delete($oldImage->image);
                    }
                    $oldImage->delete();
                }

                // ============================================
                // 3. UPLOAD NEW IMAGES
                // ============================================
                
                if ($request->hasFile('images')) {
                    $lastSortOrder = $product->images()->max('sort_order') ?? -1;

                    foreach ($request->file('images') as $index => $image) {
                        $path = $image->store('products', 'public');
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

    public function dashboard()
    {
        // Produk dengan stok kritis
        $criticalProducts = Product::with(['variants', 'category'])
            ->where('is_active', true)
            ->criticalStock()
            ->get()
            ->map(function($product) {
                $product->total_stock = $product->variants->sum('stock');
                return $product;
            });

        // Produk dengan stok menipis
        $lowProducts = Product::with(['variants', 'category'])
            ->where('is_active', true)
            ->lowStock()
            ->get()
            ->map(function($product) {
                $product->total_stock = $product->variants->sum('stock');
                return $product;
            });

        // Produk habis
        $outOfStockProducts = Product::with(['variants', 'category'])
            ->where('is_active', true)
            ->whereHas('variants', function($q) {
                $q->selectRaw('SUM(stock) as total_stock')
                  ->havingRaw('SUM(stock) = 0');
            })
            ->get()
            ->map(function($product) {
                $product->total_stock = $product->variants->sum('stock');
                return $product;
            });

        // Produk aman
        $inStockProducts = Product::with(['variants', 'category'])
            ->where('is_active', true)
            ->inStock()
            ->get()
            ->map(function($product) {
                $product->total_stock = $product->variants->sum('stock');
                return $product;
            });

        return view('admin.dashboard', compact(
            'criticalProducts',
            'lowProducts',
            'outOfStockProducts',
            'inStockProducts'
        ));
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
                    if ($oldOptionId && $oldValueId && isset($oldOptionValuesMap[$oldOptionId][$oldValueId])) {
                        $oldImagePath = $oldOptionValuesMap[$oldOptionId][$oldValueId];
                        if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                            Storage::disk('public')->delete($oldImagePath);
                        }
                    }
                    $imagePath = $newImage->store('products/option-values', 'public');
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
            'options',
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
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
            }

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
                
                $products = Product::with(['images', 'orderItems'])
                    ->whereIn('id', $productIds)
                    ->get();

                foreach ($products as $product) {
                    if ($product->orderItems()->exists()) {
                        $failedIds[] = $product->id;
                        $failedNames[] = $product->name;
                        continue;
                    }

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
                        if (Storage::disk('public')->exists($imagePath)) {
                            Storage::disk('public')->delete($imagePath);
                        }
                    }

                    $deletedCount++;
                }
            });

            $message = "Berhasil menghapus {$deletedCount} produk.";

            if (!empty($failedIds)) {
                $message .= " Gagal menghapus " . count($failedIds) . " produk: " . implode(', ', $failedNames) . " (sudah digunakan di pesanan).";
            }

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

        DB::transaction(function () use ($productImage, $path) {
            $productImage->delete();

            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        });

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
        $options = array_values($options); // Pastikan index 0, 1, 2... berurutan

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
                if (isset($images[$valueIndex]) && $images[$valueIndex] instanceof \Illuminate\Http\UploadedFile) {
                    $imagePath = $images[$valueIndex]->store('products/option-values', 'public');
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