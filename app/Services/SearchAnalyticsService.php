<?php

namespace App\Services;

use App\Models\SearchAnalytics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SearchAnalyticsService
{
    /**
     * 🔥 Record keyword pencarian
     * Termasuk guest (user_id = null)
     */
    public static function record(string $keyword, int $resultsCount = 0, ?Request $request = null): void
    {
        $request = $request ?? request();

        // Normalisasi keyword
        $keyword = trim(strtolower($keyword));
        if (empty($keyword)) {
            return;
        }

        try {
            // 🔥 Pastikan session berjalan
            if (!$request->hasSession()) {
                return;
            }

            // 🔥 Ambil user ID (guest = null)
            $userId = null;
            if (auth('customer')->check()) {
                $userId = auth('customer')->id();
            } elseif ($request->user('customer')) {
                $userId = $request->user('customer')->id;
            }

            // 🔥 DEDUP: cegah spam — 1 keyword hanya dicatat 1x per session
            // Kecuali kalau mau pakai window waktu, tinggal adjust
            $dedupKey = "search_recorded_" . md5($keyword);
            if (Session::has($dedupKey)) {
                return;
            }

            SearchAnalytics::create([
                'keyword'       => $keyword,
                'ip'            => $request->ip(),
                'user_id'       => $userId, // 🔥 bisa null untuk guest
                'session_id'    => Session::getId(),
                'user_agent'    => $request->userAgent(),
                'referer'       => $request->header('referer'),
                'results_count' => $resultsCount,
                'created_at'    => now(),
            ]);

            // Tandai sudah dicatat di session
            Session::put($dedupKey, true);

        } catch (\Throwable $e) {
            \Log::warning('SearchAnalytics record error: ' . $e->getMessage());
        }
    }
}