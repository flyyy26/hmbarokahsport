<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // 🔥 CEK APAKAH USER LOGIN SEBAGAI CUSTOMER
        if (!Auth::guard('customer')->check()) {
            return redirect()->route('customer.login');
        }

        // 🔥 AMBIL USER DARI GUARD CUSTOMER
        $user = Auth::guard('customer')->user();

        // 🔥 CEK ROLE (jika admin, redirect ke dashboard admin)
        if ($user && $user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}