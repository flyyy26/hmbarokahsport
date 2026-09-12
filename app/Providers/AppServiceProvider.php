<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\ViewComposers\MarketplaceComposer;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
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
        View::composer('layouts.customer', MarketplaceComposer::class);
        View::composer('layouts.account', MarketplaceComposer::class);
        View::composer('customer.partials.navbar', MarketplaceComposer::class);
        View::composer('customer.partials.footer', MarketplaceComposer::class);
        View::composer('customer.home', MarketplaceComposer::class);
        View::composer('customer.products.show', MarketplaceComposer::class);
        

        // ============================================
        // 🔥 SIDEBAR BADGE NOTIFICATION (BARU)
        // Untuk menu Pesanan & Retur di admin
        // ============================================
        View::composer('layouts.admin', function ($view) {
            // Skip jika user belum login atau bukan admin
            if (!Auth::check() || !Auth::user()->isAdmin()) {
                return;
            }

            // 🔥 Cache 60 detik untuk optimasi query
            $data = Cache::remember('sidebar_badges', 60, function () {
                return [
                    // Permintaan pembatalan yang belum diproses
                    'pendingCancellationCount' => Order::where('cancellation_status', 'pending')
                        ->whereNotIn('shipping_status', ['cancelled', 'delivered'])
                        ->count(),

                    // Permintaan retur yang belum diproses
                    'pendingReturnCount' => Order::where('return_status', 'pending')
                        ->count(),
                ];
            });

            $view->with($data);
        });

        // ============================================
        // 🔥 SCHEDULER (EXISTING)
        // ============================================
        Schedule::command('orders:auto-complete')->hourly();
        Schedule::command('orders:auto-cancel-unpaid')->hourly();
    }
}