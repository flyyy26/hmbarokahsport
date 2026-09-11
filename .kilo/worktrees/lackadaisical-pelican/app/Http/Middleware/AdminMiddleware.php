<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        // ✅ Gunakan guard 'web' untuk admin (karena admin pakai guard default)
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // ✅ CEK ROLE
        if (auth()->user()->role !== 'admin') {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Akses ditolak. Anda bukan admin.');
        }

        return $next($request);
    }
}