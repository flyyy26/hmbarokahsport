<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Article;
use App\Models\Marketplace; 
use App\Traits\ProductDiscountTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CustomerHomeController extends Controller
{
    use ProductDiscountTrait;

    public function index()
    {
        $banners = Banner::where('is_active', true)
            ->where(function($query) {
                $query->whereNull('starts_at')
                      ->orWhere('starts_at', '<=', now());
            })
            ->where(function($query) {
                $query->whereNull('ends_at')
                      ->orWhere('ends_at', '>=', now());
            })
            ->orderBy('sort_order')
            ->get();

        $categories = Category::where('is_active', true)
            ->withCount('products')
            ->orderBy('created_at', 'asc')
            ->get();

        // 🔥 AMBIL 6 PRODUK TERBARU (untuk mobile)
        $latestProducts = Product::with(['category', 'images', 'variants'])
            ->where('is_active', true)
            ->latest()
            ->limit(6) // 🔥 Ubah dari 5 menjadi 6
            ->get();

        // 🔥 AMBIL 6 PRODUK UNGGULAN (untuk mobile)
        $featuredProducts = Product::with(['category', 'images', 'variants'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->limit(6) // 🔥 Ubah dari 5 menjadi 6
            ->get();

        // 🔥 AMBIL 6 PRODUK TERLARIS (untuk mobile)
        $bestSellers = Product::with(['category', 'images', 'variants'])
            ->where('is_active', true)
            ->where('is_best_seller', true)
            ->limit(6) // 🔥 Ubah dari 5 menjadi 6
            ->get();

        // 🔥 FLASH SALE PRODUCTS
        $flashSaleProducts = Product::with(['category', 'images', 'variants'])
            ->where('is_active', true)
            ->where('is_flash_sale', true)
            ->whereNotNull('flash_sale_value')
            ->where('flash_sale_value', '>', 0)
            ->where(function($query) {
                $query->whereNull('flash_sale_start_date')
                      ->orWhere('flash_sale_start_date', '<=', now());
            })
            ->where(function($query) {
                $query->whereNull('flash_sale_end_date')
                      ->orWhere('flash_sale_end_date', '>=', now());
            })
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // 🔥 TAMBAHKAN DATA DISKON DAN THUMBNAIL KE SEMUA PRODUK
        $allProducts = $latestProducts->merge($featuredProducts)->merge($bestSellers)->merge($flashSaleProducts);
        foreach ($allProducts as $product) {
            if (!$product->relationLoaded('variants')) {
                $product->load('variants');
            }
            $this->attachDiscountData($product);
            $this->attachThumbnail($product);
        }

        // 🔥 AMBIL ARTIKEL
        $articles = Article::with('articleCategory')
            ->active()
            ->published()
            ->orderBy('published_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        $featuredArticle = Article::active()
            ->published()
            ->featured()
            ->latest()
            ->first();

        $marketplaces = Marketplace::active()->get();

        // 🔥 HITUNG TOTAL DURASI DARI SEMUA FLASH SALE PRODUCTS
        $combinedFlashSaleDuration = $this->calculateCombinedFlashSaleDuration($flashSaleProducts);

        return view('customer.home', compact(
            'banners',
            'categories',
            'latestProducts',
            'featuredProducts',
            'bestSellers',
            'flashSaleProducts',
            'combinedFlashSaleDuration',
            'articles',
            'featuredArticle',
            'marketplaces'
        ));
    }

    /**
     * 🔥 ATTACH THUMBNAIL KE PRODUCT - VERSI DENGAN UKURAN KECIL
     */
    protected function attachThumbnail($product)
    {
        $thumbnail = null;
        
        // Gunakan gambar produk pertama
        if ($product->images->isNotEmpty()) {
            $originalPath = $product->images->first()->image;
            
            // 🔥 BUAT THUMBNAIL PATH DENGAN UKURAN KECIL
            // Coba cek apakah ada thumbnail yang sudah dibuat
            $thumbnailPath = 'thumbnails/300x300/' . $originalPath;
            
            // Cek apakah thumbnail sudah ada di storage
            if (Storage::disk('public')->exists($thumbnailPath)) {
                $thumbnail = Storage::url($thumbnailPath);
            } else {
                // Jika thumbnail belum ada, gunakan original dengan query string untuk cache
                // Tapi kita tetap tampilkan original (akan di-resize oleh CSS)
                $thumbnail = Storage::url($originalPath);
                
                // Tambahkan parameter untuk cache busting
                $timestamp = Storage::disk('public')->lastModified($originalPath);
                $thumbnail .= '?v=' . $timestamp;
            }
        }
        
        $product->thumbnail = $thumbnail;
    }

    /**
     * 🔥 HITUNG TOTAL DURASI DARI SEMUA FLASH SALE PRODUCTS
     */
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
                // Default jika tidak ditentukan: 1 hari (86400 detik)
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
}