<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\ViewComposers\MarketplaceComposer;
use App\Models\Order;
use App\Models\PasswordResetRequest;
use App\Models\SearchAnalytics;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use App\Models\Setting;
use Illuminate\Support\Facades\Schedule;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ============================================
        // 🔥 MARKETPLACE COMPOSER (EXISTING)
        // ============================================
        View::composer([
            'layouts.customer',
            'layouts.account',
            'layouts.admin',         
            'customer.partials.navbar',
            'customer.partials.footer',
            'customer.home',
            'auth.login',
            'customer.products.show',
            'admin.*',                   
        ], MarketplaceComposer::class);

        View::composer('*', function ($view) {
            $view->with('setting', Setting::first());
        });


        // ============================================
        // 🔥 TRENDING SEARCHES (untuk search-popup)
        // ============================================
        View::composer('customer.partials.search-popup', function ($view) {
            $trendingSearches = Cache::remember('trending_searches', 300, function () {
                return SearchAnalytics::query()
                    ->whereNotNull('keyword')
                    ->where('keyword', '!=', '')
                    ->selectRaw('keyword, COUNT(*) as total, COUNT(DISTINCT ip) as unique_visitors')
                    ->groupBy('keyword')
                    ->orderByDesc('total')
                    ->limit(8)
                    ->get();
            });

            $view->with('trendingSearches', $trendingSearches);
        });


        // ============================================
        // 🔥 SIDEBAR BADGE NOTIFICATION (ADMIN)        // Untuk menu:
        //   - Pesanan (pending cancellation)
        //   - Retur (pending return)
        //   - Pelanggan (pending password reset) ← BARU
        // ============================================
        View::composer('layouts.admin', function ($view) {
            if (!Auth::check() || !Auth::user()->isAdmin()) {
                return;
            }

            $data = Cache::remember('sidebar_badges', 60, function () {
                return [
                    // Permintaan pembatalan yang belum diproses
                    'pendingCancellationCount' => Order::where('cancellation_status', 'pending')
                        ->whereNotIn('shipping_status', ['cancelled', 'delivered'])
                        ->count(),

                    // Permintaan retur yang belum diproses
                    'pendingReturnCount' => Order::where('return_status', 'pending')
                        ->count(),

                    // 🔐 Permintaan reset password yang belum diproses
                    'pendingPasswordResetCount' => PasswordResetRequest::where('status', 'pending')
                        ->count(),

                    // 🔥 BARU: Order yang butuh diproses (pending + processing)
                    'pendingOrderCount' => Order::whereIn('shipping_status', ['pending', 'processing'])
                        ->where('payment_status', 'paid')
                        ->count(),
                ];
            });

            $view->with($data);
        });


        // ============================================
        // 🔥 AUTO INVALIDATE CACHE
        // Setiap ada perubahan di Order / PasswordResetRequest,
        // hapus cache 'sidebar_badges' agar badge langsung update.
        // ============================================
        Order::saved(function () {
            Cache::forget('sidebar_badges');
        });
        Order::deleted(function () {
            Cache::forget('sidebar_badges');
        });

        PasswordResetRequest::saved(function () {
            Cache::forget('sidebar_badges');
        });
        PasswordResetRequest::deleted(function () {
            Cache::forget('sidebar_badges');
        });


        // ============================================
        // 🔥 SCHEDULER (EXISTING)
        // ============================================
        Schedule::command('orders:auto-complete')->hourly();
        Schedule::command('orders:auto-cancel-unpaid')->hourly();
    }
}