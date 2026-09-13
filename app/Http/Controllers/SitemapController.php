<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Career;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        // ============================================
        // 🔥 STATIC PAGES
        // ============================================
        $staticPages = [
            [
                'url'        => route('customer.home'),
                'changefreq' => 'daily',
                'priority'   => '1.0',
                'lastmod'    => now()->toAtomString(),
            ],
            [
                'url'        => route('customer.products.index'),
                'changefreq' => 'daily',
                'priority'   => '0.9',
                'lastmod'    => now()->toAtomString(),
            ],
            [
                'url'        => route('customer.products.latest'),
                'changefreq' => 'daily',
                'priority'   => '0.8',
                'lastmod'    => now()->toAtomString(),
            ],
            [
                'url'        => route('customer.products.promo'),
                'changefreq' => 'daily',
                'priority'   => '0.8',
                'lastmod'    => now()->toAtomString(),
            ],
            [
                'url'        => route('customer.products.flash-sale'),
                'changefreq' => 'daily',
                'priority'   => '0.8',
                'lastmod'    => now()->toAtomString(),
            ],
            [
                'url'        => route('customer.articles.index'),
                'changefreq' => 'weekly',
                'priority'   => '0.7',
                'lastmod'    => now()->toAtomString(),
            ],
            [
                'url'        => route('customer.careers.index'),
                'changefreq' => 'weekly',
                'priority'   => '0.7',
                'lastmod'    => now()->toAtomString(),
            ],
            [
                'url'        => route('customer.about'),
                'changefreq' => 'monthly',
                'priority'   => '0.5',
                'lastmod'    => now()->toAtomString(),
            ],
            [
                'url'        => route('customer.contact'),
                'changefreq' => 'monthly',
                'priority'   => '0.5',
                'lastmod'    => now()->toAtomString(),
            ],
            [
                'url'        => route('customer.help'),
                'changefreq' => 'monthly',
                'priority'   => '0.4',
                'lastmod'    => now()->toAtomString(),
            ],
            [
                'url'        => route('customer.cara-pesan'),
                'changefreq' => 'monthly',
                'priority'   => '0.4',
                'lastmod'    => now()->toAtomString(),
            ],
            [
                'url'        => route('customer.terms'),
                'changefreq' => 'yearly',
                'priority'   => '0.3',
                'lastmod'    => now()->toAtomString(),
            ],
            [
                'url'        => route('customer.privacy'),
                'changefreq' => 'yearly',
                'priority'   => '0.3',
                'lastmod'    => now()->toAtomString(),
            ],
        ];

        // ============================================
        // 🔥 DYNAMIC: CATEGORIES
        // ============================================
        $categories = Category::where('is_active', true)
            ->select('slug', 'updated_at')
            ->get()
            ->map(function ($cat) {
                return [
                    'url'        => route('customer.categories.show', $cat->slug),
                    'changefreq' => 'weekly',
                    'priority'   => '0.7',
                    'lastmod'    => $cat->updated_at?->toAtomString() ?? now()->toAtomString(),
                ];
            })
            ->toArray();

        // ============================================
        // 🔥 DYNAMIC: PRODUCTS
        // ============================================
        $products = Product::where('is_active', true)
            ->select('slug', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($product) {
                return [
                    'url'        => route('customer.products.show', $product->slug),
                    'changefreq' => 'weekly',
                    'priority'   => '0.8',
                    'lastmod'    => $product->updated_at?->toAtomString() ?? now()->toAtomString(),
                ];
            })
            ->toArray();

        // ============================================
        // 🔥 DYNAMIC: ARTICLES
        // ============================================
        $articles = Article::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            })
            ->select('slug', 'updated_at', 'published_at')
            ->orderBy('published_at', 'desc')
            ->get()
            ->map(function ($article) {
                return [
                    'url'        => route('customer.articles.show', $article->slug),
                    'changefreq' => 'monthly',
                    'priority'   => '0.6',
                    'lastmod'    => ($article->updated_at ?? $article->published_at)?->toAtomString() ?? now()->toAtomString(),
                ];
            })
            ->toArray();

        // ============================================
        // 🔥 DYNAMIC: CAREERS
        // ============================================
        $careers = Career::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('deadline')
                  ->orWhere('deadline', '>=', now()->startOfDay());
            })
            ->select('slug', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($career) {
                return [
                    'url'        => route('customer.careers.show', $career->slug),
                    'changefreq' => 'weekly',
                    'priority'   => '0.6',
                    'lastmod'    => $career->updated_at?->toAtomString() ?? now()->toAtomString(),
                ];
            })
            ->toArray();

        // ============================================
        // 🔥 MERGE SEMUA
        // ============================================
        $urls = array_merge($staticPages, $categories, $products, $articles, $careers);

        // ============================================
        // 🔥 RENDER XML
        // ============================================
        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }
}