<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FilterTrafficAnalytics
{
    public function handle(Request $request, Closure $next)
    {
        // 🔥 Daftar path yang mau DI-SKIP (tidak dicatat)
        $skipPaths = [
            'admin',
            'admin/*',
            'api/*',
            'cart/count',
            'cart/popup',
            'wishlist/popup',
            'wishlist/status',
            'checkout/get-total',
            'checkout/vouchers-ajax',
            'checkout/update-shipping',
            'storage/*',
            'images/*',
            'css/*',
            'js/*',
            'favicon.ico',
            'robots.txt',
            'login',
            'register',
            'logout',
            'test-*',
        ];

        // Cek apakah request path cocok dengan salah satu pattern
        foreach ($skipPaths as $pattern) {
            if ($request->is($pattern)) {
                // Tandai request supaya middleware Traffic Analytics skip
                $request->attributes->set('skip_traffic_analytics', true);
                break;
            }
        }

        return $next($request);
    }
}