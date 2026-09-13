<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageVisit;
use App\Models\ProductAnalytics;
use App\Models\SearchAnalytics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TrafficController extends Controller
{
    public function index(Request $request)
    {
        $days = (int) $request->input('days', 7);
        if (!in_array($days, [1, 7, 30, 90])) {
            $days = 7;
        }

        $startDate = now()->subDays($days - 1)->startOfDay();
        $endDate = now()->endOfDay();

        // ============================================
        // 1. OVERVIEW METRICS
        // ============================================
        $baseQuery = PageVisit::inRange($startDate, $endDate)->success();

        $totalViews     = (clone $baseQuery)->count();
        $uniqueVisitors = (clone $baseQuery)->distinct('ip')->count('ip');
        $uniqueSessions = (clone $baseQuery)->distinct('session_id')->count('session_id');
        $avgResponseTime = (clone $baseQuery)->avg('response_time') ?? 0;

        // ============================================
        // 2. REALTIME — 5 MENIT TERAKHIR
        // ============================================
        $realtimeCutoff = now()->subMinutes(5);
        $activeVisitors = PageVisit::where('visited_at', '>=', $realtimeCutoff)
            ->distinct('ip')
            ->count('ip');

        $realtimeVisits = PageVisit::where('visited_at', '>=', $realtimeCutoff)
            ->with('user:id,name')
            ->latest('visited_at')
            ->limit(20)
            ->get();

        // ============================================
        // 3. DAILY TREND
        // ============================================
        $dailyTrend = PageVisit::inRange($startDate, $endDate)
            ->success()
            ->selectRaw('DATE(visited_at) as date')
            ->selectRaw('COUNT(*) as views')
            ->selectRaw('COUNT(DISTINCT ip) as visitors')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($row) {
                return [
                    'date' => Carbon::parse($row->date)->format('d M'),
                    'views' => (int) $row->views,
                    'visitors' => (int) $row->visitors,
                ];
            });

        // ============================================
        // 4. TOP PAGES
        // ============================================
        $topPages = PageVisit::inRange($startDate, $endDate)
            ->success()
            ->selectRaw('url')
            ->selectRaw('COUNT(*) as views')
            ->selectRaw('COUNT(DISTINCT ip) as visitors')
            ->selectRaw('AVG(response_time) as avg_time')
            ->groupBy('url')
            ->orderByDesc('views')
            ->limit(15)
            ->get();

        // ============================================
        // 5. DEVICE BREAKDOWN
        // ============================================
        $allVisits = PageVisit::inRange($startDate, $endDate)
            ->success()
            ->get(['user_agent', 'ip']);

        // Kelompokkan IP unik per device
        $deviceIpMap = [
            'Mobile'  => [],
            'Tablet'  => [],
            'Desktop' => [],
        ];

        foreach ($allVisits as $visit) {
            $device = $visit->device;
            if (!in_array($visit->ip, $deviceIpMap[$device])) {
                $deviceIpMap[$device][] = $visit->ip;
            }
        }

        // Hitung jumlah IP unik per device
        $deviceStats = [
            'Mobile'  => count($deviceIpMap['Mobile']),
            'Tablet'  => count($deviceIpMap['Tablet']),
            'Desktop' => count($deviceIpMap['Desktop']),
        ];

        arsort($deviceStats);

        // ============================================
        // 6. BROWSER BREAKDOWN
        // ============================================
        $browserIpMap = [];

        foreach ($allVisits as $visit) {
            $browser = $visit->browser;
            if (!isset($browserIpMap[$browser])) {
                $browserIpMap[$browser] = [];
            }
            if (!in_array($visit->ip, $browserIpMap[$browser])) {
                $browserIpMap[$browser][] = $visit->ip;
            }
        }

        // Hitung IP unik per browser
        $browserStats = [];
        foreach ($browserIpMap as $browser => $ips) {
            $browserStats[$browser] = count($ips);
        }
        arsort($browserStats);
        $browserStats = array_slice($browserStats, 0, 5);

        // ============================================
        // 7. TOP REFERRERS
        // ============================================
        $topReferrers = PageVisit::inRange($startDate, $endDate)
            ->success()
            ->whereNotNull('referer')
            ->where('referer', 'not like', '%' . request()->getHost() . '%')
            ->selectRaw('referer, COUNT(*) as hits')
            ->groupBy('referer')
            ->orderByDesc('hits')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $host = parse_url($row->referer, PHP_URL_HOST);
                return (object) [
                    'domain' => $host ?: $row->referer,
                    'hits' => (int) $row->hits,
                ];
            });

        // ============================================
        // 8. TOP IPs (Curiga / Power User)
        // ============================================
        $topIps = PageVisit::inRange($startDate, $endDate)
            ->success()
            ->selectRaw('ip, COUNT(*) as hits, MAX(visited_at) as last_seen')
            ->groupBy('ip')
            ->orderByDesc('hits')
            ->limit(10)
            ->get();

        // ============================================
        // 9. TOP PRODUCT VIEWS
        // ============================================
        $topProductViews = ProductAnalytics::inRange($startDate, $endDate)
            ->views()
            ->selectRaw('product_id')
            ->selectRaw('COUNT(*) as views')
            ->selectRaw('COUNT(DISTINCT COALESCE(user_id, session_id)) as visitors') // 🔥 hitung guest
            ->groupBy('product_id')
            ->orderByDesc('views')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $product = \App\Models\Product::select('id', 'name', 'slug')->find($row->product_id);
                return (object) [
                    'product_id' => $row->product_id,
                    'name'       => $product->name ?? 'Produk dihapus',
                    'slug'       => $product->slug ?? '#',
                    'views'      => (int) $row->views,
                    'visitors'   => (int) $row->visitors,
                ];
            });

        // ============================================
        // 10. TOP ADD TO CART
        // ============================================
        $topAddToCart = ProductAnalytics::inRange($startDate, $endDate)
            ->addToCarts()
            ->selectRaw('product_id')
            ->selectRaw('COUNT(*) as additions')
            ->selectRaw('SUM(quantity) as total_qty')
            ->selectRaw('COUNT(DISTINCT COALESCE(user_id, session_id)) as unique_users') // 🔥 hitung guest
            ->groupBy('product_id')
            ->orderByDesc('additions')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $product = \App\Models\Product::select('id', 'name', 'slug')->find($row->product_id);
                return (object) [
                    'product_id'  => $row->product_id,
                    'name'        => $product->name ?? 'Produk dihapus',
                    'slug'        => $product->slug ?? '#',
                    'additions'   => (int) $row->additions,
                    'total_qty'   => (int) $row->total_qty,
                    'unique_users' => (int) $row->unique_users,
                ];
            });

        // ============================================
        // 11. TRENDING SEARCHES
        // ============================================
        $trendingSearches = SearchAnalytics::inRange($startDate, $endDate)
            ->whereNotNull('keyword')
            ->where('keyword', '!=', '')
            ->selectRaw('keyword')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('COUNT(DISTINCT COALESCE(user_id, session_id)) as unique_visitors') // 🔥 hitung guest juga
            ->groupBy('keyword')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                return (object) [
                    'keyword'         => $row->keyword,
                    'total'           => (int) $row->total,
                    'unique_visitors' => (int) $row->unique_visitors,
                ];
            });

        return view('admin.analytics.index', compact(
            'days',
            'totalViews',
            'uniqueVisitors',
            'uniqueSessions',
            'avgResponseTime',
            'activeVisitors',
            'realtimeVisits',
            'dailyTrend',
            'topPages',
            'deviceStats',
            'browserStats',
            'topReferrers',
            'topIps',
            'topProductViews',
            'topAddToCart',
            'trendingSearches'
        ));
    }
}