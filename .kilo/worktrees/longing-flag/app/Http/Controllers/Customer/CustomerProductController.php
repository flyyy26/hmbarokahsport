<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\ProductOptionValue;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use App\Traits\ProductDiscountTrait;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Auth;

class CustomerProductController extends Controller
{
    use ProductDiscountTrait;

    public function index(Request $request)
    {
        $query = Product::with([
            'category', 
            'images', 
            'variants', 
            'variants.values', 
            'variants.values.option'
        ])->where('is_active', true);

        // 🔥 SEARCH
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')
                ->orWhereHas('category', function($cat) use ($search) {
                    $cat->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        // 🔥 CATEGORY FILTER
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // 🔥 GENDER FILTER - PERBAIKAN: TAMPILKAN UNISEX JUGA
        if ($request->filled('gender')) {
            $selectedGender = $request->gender;
            
            if (in_array($selectedGender, ['pria', 'wanita'])) {
                $query->where(function($q) use ($selectedGender) {
                    $q->where('gender', $selectedGender)
                      ->orWhere('gender', 'unisex');
                });
            } else {
                $query->where('gender', $selectedGender);
            }
        }

        // 🔥 SIZE FILTER
        if ($request->filled('size')) {
            $query->whereHas('variants', function($q) use ($request) {
                $q->whereHas('values', function($qq) use ($request) {
                    $qq->where('value', $request->size);
                });
            });
        }

        // 🔥 COLOR FILTER
        $selectedColor = null;
        if ($request->filled('color')) {
            $selectedColor = $request->color;
            $query->whereHas('variants', function($q) use ($request) {
                $q->whereHas('values', function($qq) use ($request) {
                    $qq->where('value', $request->color);
                });
            });
        }

        // 🔥 PRICE RANGE FILTER
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->whereHas('variants', function($q) use ($request) {
                if ($request->filled('min_price')) {
                    $q->where(function($sub) use ($request) {
                        $sub->where('price', '>=', (float) $request->min_price)
                            ->orWhere('discount_price', '>=', (float) $request->min_price);
                    });
                }
                if ($request->filled('max_price')) {
                    $q->where(function($sub) use ($request) {
                        $sub->where('price', '<=', (float) $request->max_price)
                            ->orWhere('discount_price', '<=', (float) $request->max_price);
                    });
                }
            });
        }

        // 🔥 SORTING
        switch ($request->sort) {
            case 'price_asc':
                $query->select('products.*')
                    ->addSelect(DB::raw('(SELECT MIN(COALESCE(discount_price, price)) FROM product_variants WHERE product_variants.product_id = products.id) as min_price'))
                    ->orderBy('min_price', 'asc');
                break;
            case 'price_desc':
                $query->select('products.*')
                    ->addSelect(DB::raw('(SELECT MIN(COALESCE(discount_price, price)) FROM product_variants WHERE product_variants.product_id = products.id) as min_price'))
                    ->orderBy('min_price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'newest':
            default:
                $query->latest('created_at');
                break;
        }

        $products = $query->paginate(12);
        
        if ($request->filled('search')) {
            $products->appends(['search' => $request->search]);
        }

        // 🔥 TAMBAHKAN DATA DISKON DAN GAMBAR VARIAN
        foreach ($products as $product) {
            if (!$product->relationLoaded('variants')) {
                $product->load('variants', 'variants.values', 'variants.values.option');
            }
            
            $this->attachDiscountData($product);
            $this->attachVariantImageByColor($product, $selectedColor);
        }

        $categories = Category::where('is_active', true)->orderBy('name')->get();

        $genders = ['pria', 'wanita', 'unisex'];
        
        $sizes = ProductOptionValue::whereHas('option', function($q) {
            $q->whereRaw('LOWER(name) LIKE ?', ['%ukuran%'])
            ->orWhereRaw('LOWER(name) LIKE ?', ['%size%']);
        })->distinct()->pluck('value')->toArray();
        sort($sizes);

        $colors = ProductOptionValue::whereHas('option', function($q) {
            $q->whereRaw('LOWER(name) LIKE ?', ['%warna%'])
            ->orWhereRaw('LOWER(name) LIKE ?', ['%color%']);
        })->distinct()->pluck('value')->toArray();
        sort($colors);

        $selectedGender = $request->filled('gender') ? $request->gender : null;

        return view('customer.products.index', compact('products', 'categories', 'genders', 'sizes', 'colors', 'selectedColor', 'selectedGender'));
    }

    /**
     * 🔥 ATTACH VARIANT IMAGE BY COLOR
     */
    protected function getThumbnailUrl($path, $width = 300, $height = 300)
    {
        if (!$path) {
            return null;
        }
        
        // Jika path sudah berupa URL lengkap
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }
        
        // Bersihkan path dari /storage/ prefix jika ada
        $cleanPath = str_replace('/storage/', '', $path);
        $cleanPath = str_replace('storage/', '', $cleanPath);
        
        // Cek apakah file ada
        if (!Storage::disk('public')->exists($cleanPath)) {
            return null;
        }
        
        // Generate thumbnail path
        $thumbnailPath = 'thumbnails/' . $width . 'x' . $height . '/' . $cleanPath;
        
        // Cek apakah thumbnail sudah ada
        if (!Storage::disk('public')->exists($thumbnailPath)) {
            try {
                // 🔥 PERUBAHAN UNTUK VERSION 3.x
                $manager = new ImageManager(new Driver());
                $image = $manager->read(Storage::disk('public')->path($cleanPath));
                
                // Resize dengan maintain aspect ratio dan crop center
                $image->cover($width, $height);
                
                // Simpan thumbnail sebagai WebP
                Storage::disk('public')->put(
                    $thumbnailPath, 
                    $image->toWebp(80)->toString()
                );
                
            } catch (\Exception $e) {
                // Jika gagal membuat thumbnail, return original
                return Storage::url($cleanPath);
            }
        }
        
        return Storage::url($thumbnailPath);
    }

    /**
     * 🔥 ATTACH VARIANT IMAGE BY COLOR - DENGAN THUMBNAIL
     */
    protected function attachVariantImageByColor($product, $selectedColor)
    {
        $displayImage = null;
        $displayVariant = null;
        $originalImage = null;
        
        if ($selectedColor) {
            foreach ($product->variants as $variant) {
                foreach ($variant->values as $value) {
                    if (strtolower(trim($value->value)) === strtolower(trim($selectedColor))) {
                        $displayVariant = $variant;
                        
                        // Prioritas: Gambar dari option value
                        if ($value->image) {
                            $originalImage = $value->image;
                            $displayImage = $this->getThumbnailUrl($value->image);
                        } 
                        // Kedua: Gambar dari variant
                        elseif ($variant->image) {
                            $originalImage = $variant->image;
                            $displayImage = $this->getThumbnailUrl($variant->image);
                        }
                        
                        break 2;
                    }
                }
            }
        }
        
        // Jika tidak ada gambar dari varian, gunakan gambar produk
        if (!$displayImage) {
            $firstImage = $product->images->first();
            if ($firstImage) {
                $originalImage = $firstImage->image;
                $displayImage = $this->getThumbnailUrl($firstImage->image);
            }
        }
        
        $product->display_image = $displayImage;
        $product->display_image_original = $originalImage;
        $product->display_variant = $displayVariant;
    }

    public function show($slug)
    {
        $product = Product::with([
            'category',
            'images',
            'options' => function($query) {
                $query->orderBy('sort_order', 'asc');
            },
            'options.values' => function($query) {
                $query->orderBy('sort_order', 'asc');
            },
            'variants' => function($query) {
                $query->orderBy('price', 'asc');
            },
            'variants.variantValues',
            'variants.variantValues.optionValue',
            'features',
        ])->where('slug', $slug)->firstOrFail();

        $this->attachDiscountData($product);

        // 🔥 CEK FLASH SALE
        $isFlashSale = $product->isOnFlashSale();
        $flashSaleEndDate = null;
        $flashSaleDiscountPercent = 0;
        
        if ($isFlashSale) {
            $flashSaleEndDate = $product->flash_sale_end_date;
            // Hitung persentase diskon dari harga termurah
            $minPrice = $product->variants->min('price') ?? 0;
            $flashSaleDiscountPercent = $product->getFlashSaleDiscountPercent($minPrice);
        }

        // 🔥 CEK APAKAH PRODUK ADA DI WISHLIST
        $inWishlist = false;
        $user = Auth::guard('customer')->user();
        if ($user) {
            $inWishlist = Wishlist::where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->exists();
        }

        $variantData = $product->variants->map(function($variant) {
            $effectivePrice = $variant->effective_price;
            $discountPercent = $variant->discount_percent;
            
            return [
                'id' => $variant->id,
                'price' => (float) $variant->price,
                'discount_price' => $variant->discount_price ? (float) $variant->discount_price : null,
                'effective_price' => $effectivePrice,
                'discount_percent' => $discountPercent,
                'stock' => (int) $variant->stock,
                'weight' => (int) $variant->weight,
                'image' => $variant->image ? Storage::url($variant->image) : null,
                'values' => $variant->variantValues->pluck('product_option_value_id')->map(function($id) {
                    return (int) $id;
                })->toArray(),
            ];
        })->toArray();

        $minPrice = $product->variants->min('price') ?? 0;
        $maxPrice = $product->variants->max('price') ?? 0;
        
        $effectivePrices = [];
        foreach ($product->variants as $variant) {
            $effectivePrices[] = $variant->effective_price;
        }
        $minEffective = !empty($effectivePrices) ? min($effectivePrices) : 0;
        $maxEffective = !empty($effectivePrices) ? max($effectivePrices) : 0;
        
        $hasAnyDiscount = $product->has_discount ?? false;
        $maxDiscountPercent = $product->max_discount_percent ?? 0;
        $hasProductDiscount = $product->has_product_discount ?? false;
        $productDiscountPercent = $product->product_discount_percent ?? 0;
        
        $defaultDisplayPrice = '';
        $defaultOriginalPrice = '';
        $defaultDiscountBadge = '';
        
        if ($hasAnyDiscount) {
            if ($minEffective == $maxEffective) {
                $defaultDisplayPrice = 'Rp ' . number_format($minEffective, 0, ',', '.');
            } else {
                $defaultDisplayPrice = 'Rp ' . number_format($minEffective, 0, ',', '.') . ' - Rp ' . number_format($maxEffective, 0, ',', '.');
            }
            
            if ($minPrice == $maxPrice) {
                $defaultOriginalPrice = 'Rp ' . number_format($minPrice, 0, ',', '.');
            } else {
                $defaultOriginalPrice = 'Rp ' . number_format($minPrice, 0, ',', '.') . ' - Rp ' . number_format($maxPrice, 0, ',', '.');
            }
            
            $defaultDiscountBadge = 'Diskon ' . round($maxDiscountPercent) . '%';
        } else {
            if ($minEffective == $maxEffective) {
                $defaultDisplayPrice = 'Rp ' . number_format($minEffective, 0, ',', '.');
            } else {
                $defaultDisplayPrice = 'Rp ' . number_format($minEffective, 0, ',', '.') . ' - Rp ' . number_format($maxEffective, 0, ',', '.');
            }
            $defaultOriginalPrice = '';
            $defaultDiscountBadge = '';
        }

        $firstVariant = $product->variants->first();

        $colors = [];
        $sizes = [];
        
        foreach ($product->options as $option) {
            if (strtolower($option->name) === 'warna' || strtolower($option->name) === 'color') {
                $colors = $option->values->pluck('value')->toArray();
            }
            if (strtolower($option->name) === 'ukuran' || strtolower($option->name) === 'size') {
                $sizes = $option->values->pluck('value')->toArray();
            }
        }

        $allProducts = Product::with(['images', 'variants', 'category'])
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->get();

        foreach ($allProducts as $item) {
            $this->attachDiscountData($item);
        }

        if ($allProducts->isEmpty()) {
            $recommendedProducts = collect();
        } else {
            $sameCategory = $allProducts->filter(function($item) use ($product) {
                return $item->category_id == $product->category_id;
            });

            $otherCategory = $allProducts->filter(function($item) use ($product) {
                return $item->category_id != $product->category_id;
            });

            $recommendedProducts = $sameCategory->concat($otherCategory)->take(10);

            if ($recommendedProducts->isEmpty()) {
                $fallback = Product::with(['images', 'variants', 'category'])
                    ->where('is_active', true)
                    ->where('id', '!=', $product->id)
                    ->inRandomOrder()
                    ->limit(4)
                    ->get();
                
                foreach ($fallback as $item) {
                    $this->attachDiscountData($item);
                }
                
                $recommendedProducts = $fallback;
            }
        }

        return view('customer.products.show', compact(
            'product', 
            'variantData', 
            'firstVariant', 
            'recommendedProducts',
            'colors',
            'sizes',
            'hasAnyDiscount',
            'maxDiscountPercent',
            'hasProductDiscount',
            'productDiscountPercent',
            'defaultDisplayPrice',
            'defaultOriginalPrice',
            'defaultDiscountBadge',
            'minEffective',
            'maxEffective',
            'minPrice',
            'maxPrice',
            'inWishlist',
            'isFlashSale',      
            'flashSaleEndDate', 
            'flashSaleDiscountPercent'
        ));
    }

    public function flashSale(Request $request)
    {
        $query = Product::with([
            'category', 
            'images', 
            'variants', 
            'variants.values', 
            'variants.values.option'
        ])
        ->where('is_active', true)
        ->where('is_flash_sale', true)
        ->whereNotNull('flash_sale_value')
        ->where('flash_sale_value', '>', 0)
        ->where(function($q) {
            $q->whereNull('flash_sale_start_date')
              ->orWhere('flash_sale_start_date', '<=', now());
        })
        ->where(function($q) {
            $q->whereNull('flash_sale_end_date')
              ->orWhere('flash_sale_end_date', '>=', now());
        });

        // 🔥 SEARCH
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhereHas('category', function($cat) use ($search) {
                      $cat->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // 🔥 CATEGORY FILTER
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // 🔥 GENDER FILTER
        if ($request->filled('gender')) {
            $selectedGender = $request->gender;
            if (in_array($selectedGender, ['pria', 'wanita'])) {
                $query->where(function($q) use ($selectedGender) {
                    $q->where('gender', $selectedGender)
                      ->orWhere('gender', 'unisex');
                });
            } else {
                $query->where('gender', $selectedGender);
            }
        }

        // 🔥 SIZE FILTER
        if ($request->filled('size')) {
            $query->whereHas('variants', function($q) use ($request) {
                $q->whereHas('values', function($qq) use ($request) {
                    $qq->where('value', $request->size);
                });
            });
        }

        // 🔥 COLOR FILTER
        $selectedColor = null;
        if ($request->filled('color')) {
            $selectedColor = $request->color;
            $query->whereHas('variants', function($q) use ($request) {
                $q->whereHas('values', function($qq) use ($request) {
                    $qq->where('value', $request->color);
                });
            });
        }

        // 🔥 SORTING
        switch ($request->sort) {
            case 'discount_desc':
                $query->orderBy('flash_sale_value', 'desc');
                break;
            case 'price_asc':
                $query->select('products.*')
                    ->addSelect(DB::raw('(SELECT MIN(COALESCE(discount_price, price)) FROM product_variants WHERE product_variants.product_id = products.id) as min_price'))
                    ->orderBy('min_price', 'asc');
                break;
            case 'price_desc':
                $query->select('products.*')
                    ->addSelect(DB::raw('(SELECT MIN(COALESCE(discount_price, price)) FROM product_variants WHERE product_variants.product_id = products.id) as min_price'))
                    ->orderBy('min_price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'newest':
            default:
                $query->latest('created_at');
                break;
        }

        $nearestFlashSale = Product::where('is_active', true)
            ->where('is_flash_sale', true)
            ->whereNotNull('flash_sale_value')
            ->where('flash_sale_value', '>', 0)
            ->where(function($q) {
                $q->whereNull('flash_sale_start_date')
                  ->orWhere('flash_sale_start_date', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('flash_sale_end_date')
                  ->orWhere('flash_sale_end_date', '>=', now());
            })
            ->whereNotNull('flash_sale_end_date')
            ->orderBy('flash_sale_end_date', 'asc')
            ->first();

        $flashSaleEndDate = null;
        $flashSaleLabel = '';

        if ($nearestFlashSale) {
            $flashSaleEndDate = $nearestFlashSale->flash_sale_end_date;
            
            // 🔥 HITUNG TOTAL PRODUK FLASH SALE
            $totalFlashProducts = Product::where('is_active', true)
                ->where('is_flash_sale', true)
                ->whereNotNull('flash_sale_value')
                ->where('flash_sale_value', '>', 0)
                ->where(function($q) {
                    $q->whereNull('flash_sale_start_date')
                      ->orWhere('flash_sale_start_date', '<=', now());
                })
                ->where(function($q) {
                    $q->whereNull('flash_sale_end_date')
                      ->orWhere('flash_sale_end_date', '>=', now());
                })
                ->count();

            // 🔥 HITUNG DISKON TERBESAR
            $maxDiscount = Product::where('is_active', true)
                ->where('is_flash_sale', true)
                ->whereNotNull('flash_sale_value')
                ->where('flash_sale_value', '>', 0)
                ->where(function($q) {
                    $q->whereNull('flash_sale_start_date')
                      ->orWhere('flash_sale_start_date', '<=', now());
                })
                ->where(function($q) {
                    $q->whereNull('flash_sale_end_date')
                      ->orWhere('flash_sale_end_date', '>=', now());
                })
                ->max('flash_sale_value');

            $flashSaleLabel = '⚡ ' . $totalFlashProducts . ' produk - Diskon hingga ' . round($maxDiscount) . '%';
        }

        $products = $query->paginate(12);

        if ($request->filled('search')) {
            $products->appends(['search' => $request->search]);
        }

        // 🔥 TAMBAHKAN DATA DISKON DAN GAMBAR VARIAN
        foreach ($products as $product) {
            if (!$product->relationLoaded('variants')) {
                $product->load('variants', 'variants.values', 'variants.values.option');
            }
            $this->attachDiscountData($product);
            $this->attachVariantImageByColor($product, $selectedColor);
            
            // 🔥 TAMBAHKAN FLASH SALE END DATE UNTUK TIMER
            $product->flash_sale_end_date_formatted = $product->flash_sale_end_date?->toIso8601String();
        }

        $categories = Category::where('is_active', true)->orderBy('name')->get();

        $genders = ['pria', 'wanita', 'unisex'];
        
        $sizes = ProductOptionValue::whereHas('option', function($q) {
            $q->whereRaw('LOWER(name) LIKE ?', ['%ukuran%'])
              ->orWhereRaw('LOWER(name) LIKE ?', ['%size%']);
        })->distinct()->pluck('value')->toArray();
        sort($sizes);

        $colors = ProductOptionValue::whereHas('option', function($q) {
            $q->whereRaw('LOWER(name) LIKE ?', ['%warna%'])
              ->orWhereRaw('LOWER(name) LIKE ?', ['%color%']);
        })->distinct()->pluck('value')->toArray();
        sort($colors);

        $selectedGender = $request->filled('gender') ? $request->gender : null;

        return view('customer.products.flash-sale', compact(
            'products', 
            'categories', 
            'genders', 
            'sizes', 
            'colors', 
            'selectedColor', 
            'selectedGender',
            'flashSaleEndDate', 
            'flashSaleLabel'  
        ));
    }

    public function latest(Request $request)
    {
        $query = Product::with([
            'category', 
            'images', 
            'variants', 
            'variants.values', 
            'variants.values.option'
        ])
            ->where('is_active', true)
            ->where('created_at', '>=', now()->subMonth());

        // 🔥 SEARCH
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 🔥 CATEGORY FILTER
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // 🔥 GENDER FILTER - TAMPILKAN UNISEX JUGA
        if ($request->filled('gender')) {
            $selectedGender = $request->gender;
            
            if (in_array($selectedGender, ['pria', 'wanita'])) {
                $query->where(function($q) use ($selectedGender) {
                    $q->where('gender', $selectedGender)
                      ->orWhere('gender', 'unisex');
                });
            } else {
                $query->where('gender', $selectedGender);
            }
        }

        // 🔥 SIZE FILTER
        if ($request->filled('size')) {
            $query->whereHas('variants', function($q) use ($request) {
                $q->whereHas('values', function($qq) use ($request) {
                    $qq->where('value', $request->size);
                });
            });
        }

        // 🔥 COLOR FILTER
        $selectedColor = null;
        if ($request->filled('color')) {
            $selectedColor = $request->color;
            $query->whereHas('variants', function($q) use ($request) {
                $q->whereHas('values', function($qq) use ($request) {
                    $qq->where('value', $request->color);
                });
            });
        }

        // 🔥 PRICE RANGE FILTER
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->whereHas('variants', function($q) use ($request) {
                if ($request->filled('min_price')) {
                    $q->where(function($sub) use ($request) {
                        $sub->where('price', '>=', (float) $request->min_price)
                            ->orWhere('discount_price', '>=', (float) $request->min_price);
                    });
                }
                if ($request->filled('max_price')) {
                    $q->where(function($sub) use ($request) {
                        $sub->where('price', '<=', (float) $request->max_price)
                            ->orWhere('discount_price', '<=', (float) $request->max_price);
                    });
                }
            });
        }

        // 🔥 SORTING
        switch ($request->sort) {
            case 'price_asc':
                $query->select('products.*')
                    ->addSelect(DB::raw('(SELECT MIN(COALESCE(discount_price, price)) FROM product_variants WHERE product_variants.product_id = products.id) as min_price'))
                    ->orderBy('min_price', 'asc');
                break;
            case 'price_desc':
                $query->select('products.*')
                    ->addSelect(DB::raw('(SELECT MIN(COALESCE(discount_price, price)) FROM product_variants WHERE product_variants.product_id = products.id) as min_price'))
                    ->orderBy('min_price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'newest':
            default:
                $query->latest('created_at');
                break;
        }

        $products = $query->paginate(12);

        if ($request->filled('search')) {
            $products->appends(['search' => $request->search]);
        }

        // 🔥 TAMBAHKAN DATA DISKON DAN GAMBAR VARIAN
        foreach ($products as $product) {
            if (!$product->relationLoaded('variants')) {
                $product->load('variants', 'variants.values', 'variants.values.option');
            }
            $this->attachDiscountData($product);
            $this->attachVariantImageByColor($product, $selectedColor);
        }

        $categories = Category::where('is_active', true)->orderBy('name')->get();

        $genders = ['pria', 'wanita', 'unisex'];
        
        $sizes = ProductOptionValue::whereHas('option', function($q) {
            $q->whereRaw('LOWER(name) LIKE ?', ['%ukuran%'])
            ->orWhereRaw('LOWER(name) LIKE ?', ['%size%']);
        })->distinct()->pluck('value')->toArray();
        sort($sizes);

        $colors = ProductOptionValue::whereHas('option', function($q) {
            $q->whereRaw('LOWER(name) LIKE ?', ['%warna%'])
            ->orWhereRaw('LOWER(name) LIKE ?', ['%color%']);
        })->distinct()->pluck('value')->toArray();
        sort($colors);

        $selectedGender = $request->filled('gender') ? $request->gender : null;

        return view('customer.products.latest', compact('products', 'categories', 'genders', 'sizes', 'colors', 'selectedColor', 'selectedGender'));
    }

    public function promo(Request $request)
    {
        $query = Product::with([
            'category', 
            'images', 
            'variants', 
            'variants.values', 
            'variants.values.option'
        ])
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereHas('variants', function($sub) {
                    $sub->whereNotNull('discount_price')
                        ->whereColumn('discount_price', '<', 'price');
                })
                ->orWhere(function($sub2) {
                    $sub2->where('has_product_discount', true)
                        ->whereNotNull('discount_value')
                        ->where('discount_value', '>', 0);
                })
                ->orWhere(function($sub3) {
                    $sub3->where('is_flash_sale', true)
                        ->whereNotNull('flash_sale_value')
                        ->where('flash_sale_value', '>', 0)
                        ->where(function($dateCheck) {
                            $dateCheck->whereNull('flash_sale_start_date')
                                    ->orWhere('flash_sale_start_date', '<=', now());
                        })
                        ->where(function($dateCheck) {
                            $dateCheck->whereNull('flash_sale_end_date')
                                    ->orWhere('flash_sale_end_date', '>=', now());
                        });
                });
            });

        // 🔥 SEARCH
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 🔥 CATEGORY FILTER
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // 🔥 GENDER FILTER - TAMPILKAN UNISEX JUGA
        if ($request->filled('gender')) {
            $selectedGender = $request->gender;
            
            if (in_array($selectedGender, ['pria', 'wanita'])) {
                $query->where(function($q) use ($selectedGender) {
                    $q->where('gender', $selectedGender)
                      ->orWhere('gender', 'unisex');
                });
            } else {
                $query->where('gender', $selectedGender);
            }
        }

        // 🔥 SIZE FILTER
        if ($request->filled('size')) {
            $query->whereHas('variants', function($q) use ($request) {
                $q->whereHas('values', function($qq) use ($request) {
                    $qq->where('value', $request->size);
                });
            });
        }

        // 🔥 COLOR FILTER
        $selectedColor = null;
        if ($request->filled('color')) {
            $selectedColor = $request->color;
            $query->whereHas('variants', function($q) use ($request) {
                $q->whereHas('values', function($qq) use ($request) {
                    $qq->where('value', $request->color);
                });
            });
        }

        // 🔥 PRICE RANGE FILTER
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->whereHas('variants', function($q) use ($request) {
                if ($request->filled('min_price')) {
                    $q->where(function($sub) use ($request) {
                        $sub->where('price', '>=', (float) $request->min_price)
                            ->orWhere('discount_price', '>=', (float) $request->min_price);
                    });
                }
                if ($request->filled('max_price')) {
                    $q->where(function($sub) use ($request) {
                        $sub->where('price', '<=', (float) $request->max_price)
                            ->orWhere('discount_price', '<=', (float) $request->max_price);
                    });
                }
            });
        }

        // 🔥 SORTING
        switch ($request->sort) {
            case 'discount_desc':
                $query->select('products.*')
                    ->addSelect(DB::raw('(
                        SELECT MAX(
                            CASE 
                                WHEN discount_price IS NOT NULL AND discount_price < price 
                                THEN ((price - discount_price) / price * 100)
                                WHEN products.has_product_discount = 1 
                                    AND products.discount_value > 0
                                THEN 
                                    CASE 
                                    WHEN products.discount_type = "percentage" THEN products.discount_value
                                    WHEN products.discount_type = "fixed" THEN (products.discount_value / price * 100)
                                    ELSE 0
                                    END
                                WHEN products.is_flash_sale = 1 
                                    AND products.flash_sale_value > 0
                                    AND (products.flash_sale_start_date IS NULL OR products.flash_sale_start_date <= NOW())
                                    AND (products.flash_sale_end_date IS NULL OR products.flash_sale_end_date >= NOW())
                                THEN 
                                    CASE 
                                    WHEN products.flash_sale_type = "percentage" THEN products.flash_sale_value
                                    WHEN products.flash_sale_type = "fixed" THEN (products.flash_sale_value / price * 100)
                                    ELSE 0
                                    END
                                ELSE 0
                            END
                        ) FROM product_variants WHERE product_variants.product_id = products.id) as max_discount'))
                        ->orderBy('max_discount', 'desc');
                break;
            case 'price_asc':
                $query->select('products.*')
                    ->addSelect(DB::raw('(SELECT MIN(COALESCE(discount_price, price)) FROM product_variants WHERE product_variants.product_id = products.id) as min_price'))
                    ->orderBy('min_price', 'asc');
                break;
            case 'price_desc':
                $query->select('products.*')
                    ->addSelect(DB::raw('(SELECT MIN(COALESCE(discount_price, price)) FROM product_variants WHERE product_variants.product_id = products.id) as min_price'))
                    ->orderBy('min_price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'newest':
            default:
                $query->latest('created_at');
                break;
        }

        $products = $query->paginate(12);

        if ($request->filled('search')) {
            $products->appends(['search' => $request->search]);
        }

        // 🔥 TAMBAHKAN DATA DISKON DAN GAMBAR VARIAN
        foreach ($products as $product) {
            if (!$product->relationLoaded('variants')) {
                $product->load('variants', 'variants.values', 'variants.values.option');
            }
            $this->attachDiscountData($product);
            $this->attachVariantImageByColor($product, $selectedColor);
        }

        $categories = Category::where('is_active', true)->orderBy('name')->get();

        $genders = ['pria', 'wanita', 'unisex'];
        
        $sizes = ProductOptionValue::whereHas('option', function($q) {
            $q->whereRaw('LOWER(name) LIKE ?', ['%ukuran%'])
            ->orWhereRaw('LOWER(name) LIKE ?', ['%size%']);
        })->distinct()->pluck('value')->toArray();
        sort($sizes);

        $colors = ProductOptionValue::whereHas('option', function($q) {
            $q->whereRaw('LOWER(name) LIKE ?', ['%warna%'])
            ->orWhereRaw('LOWER(name) LIKE ?', ['%color%']);
        })->distinct()->pluck('value')->toArray();
        sort($colors);

        $selectedGender = $request->filled('gender') ? $request->gender : null;

        return view('customer.products.promo', compact('products', 'categories', 'genders', 'sizes', 'colors', 'selectedColor', 'selectedGender'));
    }

    /**
     * 🔥 ATTACH DISCOUNT DATA - PAKAI TRAIT
     * Method ini akan menggunakan trait ProductDiscountTrait
     */
    // protected function attachDiscountData($product)
    // {
    //     // Method ini sudah ada di trait ProductDiscountTrait
    // }
}