<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Article;
use App\Models\Marketplace;
use App\Models\Testimonial;
use App\Traits\ProductDiscountTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CustomerHomeController extends Controller
{
    use ProductDiscountTrait;

    private const CACHE_TTL = 300; // 5 menit
    private const CACHE_KEY = 'home_page_data_v3';

    // ============================================
    // INDEX
    // ============================================
    public function index()
    {
        $data = Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return $this->loadHomeData();
        });

        return view('customer.home', $data);
    }

    // ============================================
    // LOAD HOME DATA
    // ============================================
    private function loadHomeData(): array
    {
        // 1. BANNERS
        $banners = Banner::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('starts_at')
                      ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('ends_at')
                      ->orWhere('ends_at', '>=', now());
            })
            ->orderBy('sort_order')
            ->select(['id', 'title', 'subtitle', 'image', 'image_mobile', 'button_text', 'button_url'])
            ->get();

        // 2. CATEGORIES
        $categories = Category::where('is_active', true)
            ->withCount('products')
            ->orderBy('created_at', 'asc')
            ->select(['id', 'name', 'image'])
            ->get();

        // 3. DEKLARASI VARIABEL PRODUCT (SEBELUM DIPAKAI!)
        $productSelect = [
            'id',
            'category_id',
            'name',
            'slug',
            'is_featured',
            'is_best_seller',
            'is_flash_sale',
            'flash_sale_type',
            'flash_sale_value',
            'flash_sale_start_date',
            'flash_sale_end_date',
            'has_product_discount',
            'discount_type',
            'discount_value',
            'minimum_stock',
            'restock_threshold',
        ];

        // 🔥 EAGER LOAD - pakai 'images' (relasi yang sudah ada)
        $productRelations = [
            'images' => function ($q) {
                $q->select('id', 'product_id', 'image', 'sort_order')
                  ->orderBy('sort_order');
            },
            'variants' => function ($q) {
                $q->select('id', 'product_id', 'price', 'discount_price', 'stock', 'weight', 'is_active');
            },
            'category:id,name',
        ];

        // 4. PRODUK TERBARU (6)
        $latestProducts = Product::with($productRelations)
            ->where('is_active', true)
            ->latest()
            ->limit(6)
            ->select($productSelect)
            ->get();

        // 5. PRODUK UNGGULAN (6)
        $featuredProducts = Product::with($productRelations)
            ->where('is_active', true)
            ->where('is_featured', true)
            ->limit(6)
            ->select($productSelect)
            ->get();

        // 6. PRODUK TERLARIS (6)
        $bestSellers = Product::with($productRelations)
            ->where('is_active', true)
            ->where('is_best_seller', true)
            ->limit(6)
            ->select($productSelect)
            ->get();

        // 7. FLASH SALE PRODUCTS (8)
        $flashSaleProducts = Product::with($productRelations)
            ->where('is_active', true)
            ->where('is_flash_sale', true)
            ->whereNotNull('flash_sale_value')
            ->where('flash_sale_value', '>', 0)
            ->where(function ($query) {
                $query->whereNull('flash_sale_start_date')
                      ->orWhere('flash_sale_start_date', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('flash_sale_end_date')
                      ->orWhere('flash_sale_end_date', '>=', now());
            })
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->select($productSelect)
            ->get();

        // 8. ATTACH DISCOUNT & THUMBNAIL
        $productCollections = [
            $latestProducts,
            $featuredProducts,
            $bestSellers,
            $flashSaleProducts,
        ];

        foreach ($productCollections as $products) {
            foreach ($products as $product) {
                $this->attachDiscountDataFast($product);
                $this->attachThumbnailFast($product);
            }
        }

        // 9. ARTIKEL
        $articles = Article::with('articleCategory:id,name')
            ->active()
            ->published()
            ->orderBy('published_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->select([
                'id',
                'title',
                'slug',
                'excerpt',
                'content',
                'image',
                'author',
                'article_category_id',
                'published_at',
                'created_at',
            ])
            ->get();

        // 10. TESTIMONIALS
        $testimonials = Testimonial::active()
            ->ordered()
            ->with(['images' => function ($q) {
                $q->select('id', 'testimonial_id', 'image');
            }])
            ->limit(10)
            ->select(['id', 'customer_name', 'testimonial', 'rating'])
            ->get();

        // 11. FLASH SALE DURATION
        $combinedFlashSaleDuration = $this->calculateCombinedFlashSaleDuration($flashSaleProducts);

        return compact(
            'banners',
            'categories',
            'latestProducts',
            'featuredProducts',
            'bestSellers',
            'flashSaleProducts',
            'combinedFlashSaleDuration',
            'articles',
            'testimonials'
        );
    }

    // ============================================
    // 🔥 ATTACH DISCOUNT DATA (VERSI CEPAT)
    // ============================================
    protected function attachDiscountDataFast($product): void
    {
        $variants = $product->variants;

        if ($variants->isEmpty()) {
            $product->has_discount = false;
            $product->max_discount_percent = 0;
            $product->min_effective_price = 0;
            $product->max_price = 0;
            $product->badge_label = null;
            return;
        }

        $maxDiscount = 0;
        $minEffectivePrice = PHP_FLOAT_MAX;
        $maxPrice = 0;
        $minPrice = PHP_FLOAT_MAX;

        foreach ($variants as $variant) {
            $discountPercent = 0;
            if ($variant->discount_price && $variant->price > 0 && $variant->discount_price < $variant->price) {
                $discountPercent = round((($variant->price - $variant->discount_price) / $variant->price) * 100);
            }

            if ($discountPercent > $maxDiscount) {
                $maxDiscount = $discountPercent;
            }

            $effectivePrice = $variant->discount_price ?? $variant->price;

            // Cek flash sale
            if ($product->is_flash_sale && $product->flash_sale_value > 0) {
                $now = now();
                $isActive = true;
                if ($product->flash_sale_start_date && $now->lt($product->flash_sale_start_date)) {
                    $isActive = false;
                }
                if ($product->flash_sale_end_date && $now->gt($product->flash_sale_end_date)) {
                    $isActive = false;
                }

                if ($isActive) {
                    if ($product->flash_sale_type === 'percentage') {
                        $flashPrice = $variant->price * (1 - $product->flash_sale_value / 100);
                        if ($flashPrice < $effectivePrice) {
                            $effectivePrice = $flashPrice;
                        }
                        if ($product->flash_sale_value > $maxDiscount) {
                            $maxDiscount = $product->flash_sale_value;
                        }
                    } elseif ($product->flash_sale_type === 'fixed') {
                        $flashPrice = max(0, $variant->price - $product->flash_sale_value);
                        if ($flashPrice < $effectivePrice) {
                            $effectivePrice = $flashPrice;
                        }
                    }
                }
            }

            if ($effectivePrice < $minEffectivePrice) {
                $minEffectivePrice = $effectivePrice;
            }
            if ($variant->price > $maxPrice) {
                $maxPrice = $variant->price;
            }
            if ($variant->price < $minPrice) {
                $minPrice = $variant->price;
            }
        }

        $product->has_discount = $maxDiscount > 0;
        $product->max_discount_percent = $maxDiscount;
        $product->min_effective_price = $minEffectivePrice === PHP_FLOAT_MAX ? $minPrice : $minEffectivePrice;
        $product->max_price = $maxPrice;
        $product->badge_label = $product->is_flash_sale
            ? '⚡ Flash Sale'
            : ($product->is_best_seller ? 'Terlaris' : ($product->is_featured ? 'Unggulan' : null));
    }

    // ============================================
    // 🔥 ATTACH THUMBNAIL (VERSI CEPAT)
    // ============================================
    protected function attachThumbnailFast($product): void
    {
        $thumbnail = null;

        if ($product->relationLoaded('images') && $product->images->isNotEmpty()) {
            $imagePath = $product->images->first()->image;

            // 🔥 FIX: Normalize path
            $imagePath = ltrim($imagePath, '/');

            // Hapus prefix "storage/" jika ada
            if (str_starts_with($imagePath, 'storage/')) {
                $imagePath = substr($imagePath, strlen('storage/'));
            }

            // Hapus URL lengkap jika ada
            if (preg_match('#^https?://#i', $imagePath)) {
                $parsed = parse_url($imagePath, PHP_URL_PATH);
                $imagePath = ltrim($parsed ?? '', '/');
                if (str_starts_with($imagePath, 'storage/')) {
                    $imagePath = substr($imagePath, strlen('storage/'));
                }
            }

            // Cek file exists
            if (Storage::disk('public')->exists($imagePath)) {
                $thumbnail = Storage::url($imagePath);
            }
        }

        $product->thumbnail = $thumbnail;
    }

    // ============================================
    // 🔥 HITUNG TOTAL DURASI FLASH SALE
    // ============================================
    private function calculateCombinedFlashSaleDuration($flashSaleProducts): array
    {
        if ($flashSaleProducts->isEmpty()) {
            return [
                'days' => 0,
                'hours' => 0,
                'minutes' => 0,
                'seconds' => 0,
                'total_seconds' => 0,
                'is_expired' => true,
            ];
        }

        $totalSeconds = 0;
        $now = now();

        foreach ($flashSaleProducts as $product) {
            if (!empty($product->flash_sale_end_date)) {
                $endDate = \Carbon\Carbon::parse($product->flash_sale_end_date);
                if ($endDate->gt($now)) {
                    $totalSeconds += $now->diffInSeconds($endDate);
                }
            } else {
                $totalSeconds += 86400;
            }
        }

        if ($totalSeconds <= 0) {
            return [
                'days' => 0,
                'hours' => 0,
                'minutes' => 0,
                'seconds' => 0,
                'total_seconds' => 0,
                'is_expired' => true,
            ];
        }

        $days = floor($totalSeconds / 86400);
        $remaining = $totalSeconds % 86400;
        $hours = floor($remaining / 3600);
        $remaining %= 3600;
        $minutes = floor($remaining / 60);
        $seconds = $remaining % 60;

        return [
            'days'          => (int) $days,
            'hours'         => (int) $hours,
            'minutes'       => (int) $minutes,
            'seconds'       => (int) $seconds,
            'total_seconds' => (int) $totalSeconds,
            'is_expired'    => false,
        ];
    }

    // ============================================
    // 🔥 CLEAR CACHE
    // ============================================
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}