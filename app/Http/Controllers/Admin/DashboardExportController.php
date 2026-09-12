<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OfflineOrder;
use App\Models\OrderItem;
use App\Models\OfflineOrderItem;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardExportController extends Controller
{
    /**
     * 🔥 Export data dashboard per bulan ke CSV
     */
    public function export(Request $request)
    {
        $monthParam = $request->input('month', now()->format('Y-m'));

        try {
            $month = Carbon::createFromFormat('Y-m', $monthParam)->startOfMonth();
        } catch (\Exception $e) {
            $month = now()->startOfMonth();
        }

        $monthStart = $month->copy()->startOfMonth();
        $monthEnd = $month->copy()->endOfMonth();
        $monthLabel = $month->translatedFormat('F Y');

        // ============================================
        // 🔥 Ambil data online (paid)
        // ============================================
        $onlineOrders = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->with(['items'])
            ->orderBy('created_at')
            ->get();

        // ============================================
        // 🔥 Ambil data offline (paid)
        // ============================================
        $offlineOrders = OfflineOrder::where('payment_status', 'paid')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->with(['items'])
            ->orderBy('created_at')
            ->get();

        // ============================================
        // 🔥 Top Produk Terlaris (gabungan)
        // ============================================
        $topProductsOnline = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.payment_status', 'paid')
            ->whereBetween('orders.created_at', [$monthStart, $monthEnd])
            ->selectRaw('
                order_items.product_name,
                order_items.variant_name as variant,
                SUM(order_items.quantity) as sold,
                SUM(order_items.subtotal) as revenue
            ')
            ->groupBy('order_items.product_name', 'order_items.variant_name')
            ->get();

        $topProductsOffline = OfflineOrderItem::query()
            ->join('offline_orders', 'offline_orders.id', '=', 'offline_order_items.offline_order_id')
            ->where('offline_orders.payment_status', 'paid')
            ->whereBetween('offline_orders.created_at', [$monthStart, $monthEnd])
            ->selectRaw('
                offline_order_items.product_name,
                offline_order_items.variant_name as variant,
                SUM(offline_order_items.quantity) as sold,
                SUM(offline_order_items.subtotal) as revenue
            ')
            ->groupBy('offline_order_items.product_name', 'offline_order_items.variant_name')
            ->get();

        $topProducts = $topProductsOnline
            ->concat($topProductsOffline)
            ->groupBy(fn($item) => $item->product_name . '|' . ($item->variant ?? ''))
            ->map(function ($group) {
                return (object) [
                    'product_name' => $group->first()->product_name,
                    'variant'      => $group->first()->variant,
                    'sold'         => $group->sum('sold'),
                    'revenue'      => $group->sum('revenue'),
                ];
            })
            ->sortByDesc('revenue')
            ->values();

        // ============================================
        // 🔥 Build CSV
        // ============================================
        $filename = 'dashboard-' . $month->format('Y-m') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use (
            $onlineOrders,
            $offlineOrders,
            $topProducts,
            $monthLabel,
            $monthStart,
            $monthEnd
        ) {
            $file = fopen('php://output', 'w');

            // 🔥 BOM untuk Excel (supaya UTF-8 terbaca)
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // ============================================
            // HEADER
            // ============================================
            fputcsv($file, ['Laporan Dashboard — ' . $monthLabel]);
            fputcsv($file, ['Periode: ' . $monthStart->format('d/m/Y') . ' - ' . $monthEnd->format('d/m/Y')]);
            fputcsv($file, []);

            // ============================================
            // SECTION 1: RINGKASAN
            // ============================================
            $totalOnline = $onlineOrders->sum('total');
            $totalOffline = $offlineOrders->sum('total');
            $grandTotal = $totalOnline + $totalOffline;
            $totalOrders = $onlineOrders->count() + $offlineOrders->count();
            $aov = $totalOrders > 0 ? $grandTotal / $totalOrders : 0;

            fputcsv($file, ['=== RINGKASAN ===']);
            fputcsv($file, ['Metrik', 'Nilai']);
            fputcsv($file, ['Total Penjualan Online', 'Rp ' . number_format($totalOnline, 0, ',', '.')]);
            fputcsv($file, ['Total Penjualan Offline', 'Rp ' . number_format($totalOffline, 0, ',', '.')]);
            fputcsv($file, ['Total Keseluruhan', 'Rp ' . number_format($grandTotal, 0, ',', '.')]);
            fputcsv($file, ['Total Order Online', $onlineOrders->count()]);
            fputcsv($file, ['Total Order Offline', $offlineOrders->count()]);
            fputcsv($file, ['Total Order', $totalOrders]);
            fputcsv($file, ['Rata-rata Order Value (AOV)', 'Rp ' . number_format($aov, 0, ',', '.')]);
            fputcsv($file, []);

            // ============================================
            // SECTION 2: PESANAN ONLINE
            // ============================================
            fputcsv($file, ['=== PESANAN ONLINE ===']);
            fputcsv($file, ['No. Order', 'Tanggal', 'Customer', 'Status Pembayaran', 'Status Pengiriman', 'Total']);

            foreach ($onlineOrders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->created_at->format('d/m/Y H:i'),
                    $order->shipping_name ?? $order->user->name ?? 'Guest',
                    $order->payment_status_label,
                    $order->shipping_status_label,
                    'Rp ' . number_format($order->total, 0, ',', '.'),
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['Subtotal Online', '', '', '', '', 'Rp ' . number_format($totalOnline, 0, ',', '.')]);
            fputcsv($file, []);

            // ============================================
            // SECTION 3: PESANAN OFFLINE
            // ============================================
            fputcsv($file, ['=== PESANAN OFFLINE ===']);
            fputcsv($file, ['No. Order', 'Tanggal', 'Customer', 'Metode Bayar', 'Total']);

            foreach ($offlineOrders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->created_at->format('d/m/Y H:i'),
                    $order->customer_name ?? 'Walk-in Customer',
                    strtoupper($order->payment_method ?? '-'),
                    'Rp ' . number_format($order->total, 0, ',', '.'),
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['Subtotal Offline', '', '', '', 'Rp ' . number_format($totalOffline, 0, ',', '.')]);
            fputcsv($file, []);

            // ============================================
            // SECTION 4: PRODUK TERLARIS
            // ============================================
            fputcsv($file, ['=== PRODUK TERLARIS ===']);
            fputcsv($file, ['Peringkat', 'Produk', 'Varian', 'Terjual', 'Total Penjualan']);

            foreach ($topProducts as $index => $product) {
                fputcsv($file, [
                    $index + 1,
                    $product->product_name ?? '-',
                    $product->variant ?? '-',
                    $product->sold ?? 0,
                    'Rp ' . number_format($product->revenue ?? 0, 0, ',', '.'),
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['Dicetak pada: ' . now()->format('d/m/Y H:i:s')]);

            fclose($file);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }
}