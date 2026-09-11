<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\ViewComposers\MarketplaceComposer;
use Illuminate\Support\Facades\View;

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
    public function boot()
    {
        View::composer('layouts.customer', MarketplaceComposer::class);
        View::composer('layouts.account', MarketplaceComposer::class);
        View::composer('customer.partials.navbar', MarketplaceComposer::class);
        View::composer('customer.partials.footer', MarketplaceComposer::class);
        View::composer('customer.home', MarketplaceComposer::class);
    }
}
