<?php

namespace App\Services;

use App\Models\ProductAnalytics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ProductAnalyticsService
{
    /**
     * 🔥 Record event VIEW produk
     * Dipanggil saat user (guest atau login) membuka halaman detail produk
     */
    public static function recordView(int $productId, ?Request $request = null): void
    {
        self::record(
            self::resolveRequest($request),
            ProductAnalytics::EVENT_VIEW,
            $productId
        );
    }

    /**
     * 🔥 Record event ADD TO CART
     */
    public static function recordAddToCart(int $productId, int $quantity = 1, ?Request $request = null): void
    {
        self::record(
            self::resolveRequest($request),
            ProductAnalytics::EVENT_ADD_TO_CART,
            $productId,
            $quantity
        );
    }

    /**
     * 🔥 Core: Simpan event ke database
     * PENTING: Handle guest user (user_id = null) dengan benar
     */
    protected static function record(Request $request, string $event, int $productId, int $quantity = 1): void
    {
        try {
            // 🔥 Pastikan session berjalan (kalau belum, start manual)
            if (!$request->hasSession()) {
                return;
            }

            // 🔥 Ambil user ID (guest = null, login = ID)
            $userId = null;
            if (auth('customer')->check()) {
                $userId = auth('customer')->id();
            } elseif ($request->user('customer')) {
                $userId = $request->user('customer')->id;
            }

            // 🔥 Dedup: cegah double-record di session yang sama
            // Kalau user refresh halaman produk yang sama, tidak dicatat lagi
            $dedupKey = "product_event_{$event}_{$productId}";
            if (Session::has($dedupKey)) {
                return; // sudah tercatat di session ini
            }

            ProductAnalytics::create([
                'product_id'  => $productId,
                'event'       => $event,
                'ip'          => $request->ip(),
                'user_id'     => $userId, // 🔥 bisa null untuk guest
                'session_id'  => Session::getId(), // pakai Session facade biar aman
                'user_agent'  => $request->userAgent(),
                'referer'     => $request->header('referer'),
                'quantity'    => $quantity,
                'created_at'  => now(),
            ]);

            // Tandai sudah dicatat di session
            Session::put($dedupKey, true);

        } catch (\Throwable $e) {
            \Log::warning('ProductAnalytics record error: ' . $e->getMessage());
        }
    }

    protected static function resolveRequest(?Request $request): Request
    {
        return $request ?? request();
    }
}