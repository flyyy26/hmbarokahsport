<?php

namespace App\Http\Middleware;

use App\Models\PageVisit;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPageVisit
{
    /**
     * Path yang TIDAK dicatat
     */
    protected array $skipPatterns = [
        // Admin
        'admin', 'admin/*',

        // API & AJAX internal
        'api/*',
        'cart/count', 'cart/popup',
        'wishlist/popup', 'wishlist/status',
        'checkout/get-total',
        'checkout/vouchers-ajax',
        'checkout/update-shipping',
        'checkout/update-cart-item',
        'checkout/remove-cart-item',
        'checkout/save-data',
        'checkout/save-address',
        'checkout/addresses',
        'midtrans/*',

        // Auth
        'login', 'register', 'logout',
        'lupa-password', 'lupa-password/*',
        'reset-password/*',

        // Static assets & file
        'storage/*', 'images/*', 'css/*', 'js/*',
        'favicon.ico', 'robots.txt',
        'sitemap.xml',

        // Development
        'test-*', 'telescope*', '_debugbar*',
    ];

    /**
     * Method yang dicatat (hanya GET — page view)
     */
    protected array $trackedMethods = ['GET'];

    /**
     * Session key untuk menyimpan daftar halaman yang sudah dikunjungi
     */
    protected string $sessionKey = 'tracked_pages';

    /**
     * Session key untuk reset waktu (dedup window)
     * Setelah sekian menit, halaman akan dihitung lagi
     */
    protected int $dedupWindowMinutes = 0; // 0 = seumur session

    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        $response = $next($request);

        // ============================================
        // CEK APAKAH REQUEST HARUS DICATAT
        // ============================================

        // 1. Skip kalau method bukan GET
        if (!in_array($request->method(), $this->trackedMethods)) {
            return $response;
        }

        // 2. Skip kalau path cocok dengan skip patterns
        foreach ($this->skipPatterns as $pattern) {
            if ($request->is($pattern)) {
                return $response;
            }
        }

        // 3. Skip kalau response bukan 200 OK
        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 400) {
            return $response;
        }

        // 4. Skip kalau request dari bot / crawler
        if ($this->isBot($request->userAgent())) {
            return $response;
        }

        // ============================================
        // 🔥 CEK APAKAH HALAMAN SUDAH DIKUNJUNGI DI SESSION INI
        // ============================================
        $currentPath = '/' . ltrim($request->path(), '/');
        $trackedPages = $request->session()->get($this->sessionKey, []);

        // Kalau halaman ini sudah ada di session, skip pencatatan
        if (isset($trackedPages[$currentPath])) {
            // Kalau mau pakai dedup window (opsional), cek waktu
            if ($this->dedupWindowMinutes > 0) {
                $lastVisit = $trackedPages[$currentPath];
                $minutesAgo = now()->diffInMinutes($lastVisit);

                if ($minutesAgo < $this->dedupWindowMinutes) {
                    return $response; // masih dalam window, skip
                }
            } else {
                // Mode seumur session: skip
                return $response;
            }
        }

        // ============================================
        // CATAT VISIT
        // ============================================
        try {
            $responseTime = round((microtime(true) - $startTime) * 1000);

            PageVisit::create([
                'url'           => $currentPath,
                'full_url'      => $request->fullUrl(),
                'method'        => $request->method(),
                'ip'            => $request->ip(),
                'user_agent'    => $request->userAgent(),
                'referer'       => $request->header('referer'),
                'user_id'       => auth('customer')->id(),
                'session_id'    => $request->session()->getId(),
                'response_time' => $responseTime,
                'status_code'   => $response->getStatusCode(),
                'visited_at'    => now(),
            ]);

            // 🔥 TANDAI HALAMAN INI SUDAH DIKUNJUNGI DI SESSION
            $trackedPages[$currentPath] = now();
            $request->session()->put($this->sessionKey, $trackedPages);

        } catch (\Throwable $e) {
            \Log::warning('TrackPageVisit error: ' . $e->getMessage());
        }

        return $response;
    }

    /**
     * Deteksi bot / crawler sederhana
     */
    protected function isBot(?string $userAgent): bool
    {
        if (empty($userAgent)) {
            return false;
        }

        $bots = [
            'bot', 'crawl', 'spider', 'slurp',
            'googlebot', 'bingbot', 'yandex', 'duckduckbot',
            'baiduspider', 'facebookexternalhit', 'twitterbot',
            'whatsapp', 'telegrambot', 'curl', 'wget',
        ];

        $ua = strtolower($userAgent);

        foreach ($bots as $bot) {
            if (str_contains($ua, $bot)) {
                return true;
            }
        }

        return false;
    }
}