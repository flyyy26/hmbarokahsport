<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OfflineOrder;
use App\Models\Product;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\OfflineOrderItem;
use App\Models\ProductVariant;
use App\Models\BiteshipApiUsage;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * 🔥 HELPER: Filter hanya order selesai & tidak retur (untuk revenue)
     */
    private function applyCompletedFilter($query)
    {
        return $query
            ->where('shipping_status', 'delivered')
            ->where(function ($q) {
                $q->whereNull('return_status')
                  ->orWhere('return_status', 'rejected');
            });
    }

    /**
     * 🔥 HELPER: Filter hanya order selesai (untuk ongkir — TANPA cek retur)
     */
    private function applyDeliveredFilterOnly($query)
    {
        return $query->where('shipping_status', 'delivered');
    }

    private function parseMonth(Request $request)
    {
        $monthParam = $request->input('month', now()->format('Y-m'));
        try {
            $month = Carbon::createFromFormat('Y-m', $monthParam)->startOfMonth();
        } catch (\Exception $e) {
            $month = now()->startOfMonth();
        }
        $monthStart = $month->copy()->startOfMonth();
        $monthEnd   = $month->copy()->endOfMonth();

        return [$monthStart, $monthEnd, $monthParam];
    }

     /**
      * 🔥 HELPER: Hitung Total Penjualan (orders.total = subtotal - voucher + ongkir)
      * HANYA order yang TIDAK retur — sudah termasuk ongkir, sudah termasuk diskon voucher
      */
     private function getOnlineProductRevenue($start, $end): float
     {
         return (float) Order::query()
             ->whereBetween('created_at', [$start, $end])
             ->where('payment_status', 'paid')
             ->where('shipping_status', 'delivered')
             ->where(function ($q) {
                 $q->whereNull('return_status')
                   ->orWhere('return_status', 'rejected');
             })
             ->sum('total');
     }

     private function getOfflineProductRevenue($start, $end): float
     {
         return (float) OfflineOrder::query()
             ->whereBetween('created_at', [$start, $end])
             ->where('payment_status', 'paid')
             ->where('shipping_status', 'delivered')
             ->where(function ($q) {
                 $q->whereNull('return_status')
                   ->orWhere('return_status', 'rejected');
             })
             ->sum('total');
     }

    /**
     * 🔥 HELPER: Hitung Total Ongkir (SEMUA order delivered, TERMASUK yang retur)
     * Karena ongkir sudah dibayar customer, tidak bisa ditarik lagi walaupun retur
     */
    private function getOnlineShippingCost($start, $end): float
    {
        return (float) Order::query()
            ->whereBetween('created_at', [$start, $end])
            ->where('shipping_status', 'delivered')
            ->whereNotNull('original_shipping_cost')
            ->sum('original_shipping_cost');
    }

    private function getOfflineShippingCost($start, $end): float
    {
        return (float) OfflineOrder::query()
            ->whereBetween('created_at', [$start, $end])
            ->where('shipping_status', 'delivered')
            ->whereNotNull('original_shipping_cost')
            ->sum('original_shipping_cost');
    }

    // ============================================
    // INDEX — LAPORAN RINGKASAN
    // ============================================
    public function index(Request $request)
    {
        [$start, $end, $monthParam] = $this->parseMonth($request);

        // ============================================
        // 🔥 RINGKASAN UMUM
        // ============================================
        // Untuk order COUNT — pakai filter completed (tidak retur)
        $onlineOrdersQuery = Order::whereBetween('created_at', [$start, $end]);
        $this->applyCompletedFilter($onlineOrdersQuery);
        $onlineOrders = $onlineOrdersQuery->get();

        $offlineOrdersQuery = OfflineOrder::whereBetween('created_at', [$start, $end]);
        $this->applyCompletedFilter($offlineOrdersQuery);
        $offlineOrders = $offlineOrdersQuery->get();

        // 🔥 REVENUE = TOTAL TRANSAKSI customer (subtotal - voucher + ongkir, tidak retur)
        $onlineRevenue  = $this->getOnlineProductRevenue($start, $end);
        $offlineRevenue = $this->getOfflineProductRevenue($start, $end);
        $totalRevenue   = $onlineRevenue + $offlineRevenue;

        $onlineOrderCount  = $onlineOrders->count();
        $offlineOrderCount = $offlineOrders->count();
        $totalOrderCount   = $onlineOrderCount + $offlineOrderCount;

        $aov = $totalOrderCount > 0 && $totalRevenue > 0
            ? round($totalRevenue / $totalOrderCount)
            : 0;

        // 🔥 ONGKIR = SEMUA order delivered (TERMASUK yang retur)
        // Karena ongkir sudah dibayar customer, tidak bisa ditarik
        $totalShippingOnline  = $this->getOnlineShippingCost($start, $end);
        $totalShippingOffline = $this->getOfflineShippingCost($start, $end);
        $totalShippingCost    = $totalShippingOnline + $totalShippingOffline;

        // 🔥 Total Transaksi = totalRevenue (sudah termasuk ongkir + diskon voucher)
        $totalTransaction = $totalRevenue;

        // Biaya ongkir Biteship
        $biteshipShippingCost = (float) Order::whereBetween('created_at', [$start, $end])
            ->whereNotNull('biteship_order_id')
            ->where('biteship_order_id', '!=', '')
            ->where('shipping_status', '!=', 'cancelled')
            ->whereNotNull('original_shipping_cost')
            ->sum('original_shipping_cost');
        $biteshipOrderCount = Order::whereBetween('created_at', [$start, $end])
            ->whereNotNull('biteship_order_id')
            ->where('biteship_order_id', '!=', '')
            ->count();
        $biteshipAvgCost = $biteshipOrderCount > 0 ? round($biteshipShippingCost / $biteshipOrderCount) : 0;

        $totalStockValue = (float) ProductVariant::sum(DB::raw('stock * price'));
        $totalCustomers = User::where('role', 'customer')->count();

        $biteshipApiUsageCount = BiteshipApiUsage::whereBetween('created_at', [$start, $end])->count();
        $biteshipApiCost       = $biteshipApiUsageCount * BiteshipApiUsage::COST_PER_HIT;
        $biteshipApiCostPerHit = BiteshipApiUsage::COST_PER_HIT;

        // ============================================
        // 🔥 PENJUALAN PER HARI — TOTAL TRANSAKSI (termasuk ongkir, net diskon)
        // ============================================
        $dailyOnline = Order::query()
            ->whereBetween('created_at', [$start, $end])
            ->where('payment_status', 'paid')
            ->where('shipping_status', 'delivered')
            ->where(function ($q) {
                $q->whereNull('return_status')
                ->orWhere('return_status', 'rejected');
            })
            ->selectRaw('DATE(created_at) as date, SUM(total) as sales, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $dailyOffline = OfflineOrder::query()
            ->whereBetween('created_at', [$start, $end])
            ->where('payment_status', 'paid')
            ->where('shipping_status', 'delivered')
            ->where(function ($q) {
                $q->whereNull('return_status')
                ->orWhere('return_status', 'rejected');
            })
            ->selectRaw('DATE(created_at) as date, SUM(total) as sales, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $chartLabels  = [];
        $chartOnline  = [];
        $chartOffline = [];
        $chartCombined = [];

        $days = (int) $start->diffInDays($end) + 1;
        for ($i = 0; $i < $days; $i++) {
            $day     = $start->copy()->addDays($i)->format('Y-m-d');
            $label   = $start->copy()->addDays($i)->format('d M');

            $online  = $dailyOnline[$day] ?? null;
            $offline = $dailyOffline[$day] ?? null;

            $chartLabels[]   = $label;
            $chartOnline[]   = (float) ($online->sales ?? 0);
            $chartOffline[]  = (float) ($offline->sales ?? 0);
            $chartCombined[] = (float) (($online->sales ?? 0) + ($offline->sales ?? 0));
        }

        // ============================================
        // STATUS ORDER
        // ============================================
        $orderStatusData = Order::whereBetween('created_at', [$start, $end])
            ->selectRaw('shipping_status, COUNT(*) as count, SUM(total) as total')
            ->groupBy('shipping_status')
            ->get();

        $offlineOrderStatusData = OfflineOrder::whereBetween('created_at', [$start, $end])
            ->selectRaw('shipping_status, COUNT(*) as count, SUM(total) as total')
            ->groupBy('shipping_status')
            ->get();

        // ============================================
        // METODE PEMBAYARAN — PRODUK NET SETELAH DISKON VOUCHER (tidak retur)
        // ============================================
        $paymentMethods = Order::query()
            ->whereBetween('created_at', [$start, $end])
            ->where('payment_status', 'paid')
            ->where('shipping_status', 'delivered')
            ->whereNotNull('payment_method')
            ->where(function ($q) {
                $q->whereNull('return_status')
                  ->orWhere('return_status', 'rejected');
            })
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total) as total')
            ->groupBy('payment_method')
            ->get();

        $offlinePaymentMethods = OfflineOrder::query()
            ->whereBetween('created_at', [$start, $end])
            ->where('payment_status', 'paid')
            ->where('shipping_status', 'delivered')
            ->whereNotNull('payment_method')
            ->where(function ($q) {
                $q->whereNull('return_status')
                  ->orWhere('return_status', 'rejected');
            })
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total) as total')
            ->groupBy('payment_method')
            ->get();

        // ============================================
        // PRODUK TERLARIS — PRODUK NET SETELAH DISKON (tidak retur)
        // ============================================
        $topProductsOnline = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->where('orders.payment_status', 'paid')
            ->where('orders.shipping_status', 'delivered')
            ->where(function ($q) {
                $q->whereNull('orders.return_status')
                  ->orWhere('orders.return_status', 'rejected');
            })
            ->selectRaw('order_items.product_name, order_items.variant_name as variant, SUM(order_items.quantity) as sold, SUM(order_items.subtotal - (orders.discount * order_items.subtotal / NULLIF(orders.subtotal, 0))) as revenue')
            ->groupBy('order_items.product_name', 'order_items.variant_name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        $topProductsOffline = OfflineOrderItem::query()
            ->join('offline_orders', 'offline_orders.id', '=', 'offline_order_items.offline_order_id')
            ->whereBetween('offline_orders.created_at', [$start, $end])
            ->where('offline_orders.payment_status', 'paid')
            ->where('offline_orders.shipping_status', 'delivered')
            ->where(function ($q) {
                $q->whereNull('offline_orders.return_status')
                  ->orWhere('offline_orders.return_status', 'rejected');
            })
            ->selectRaw('offline_order_items.product_name, offline_order_items.variant_name as variant, SUM(offline_order_items.quantity) as sold, SUM(offline_order_items.subtotal - (offline_orders.discount * offline_order_items.subtotal / NULLIF(offline_orders.subtotal, 0))) as revenue')
            ->groupBy('offline_order_items.product_name', 'offline_order_items.variant_name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        $topProducts = $topProductsOnline->concat($topProductsOffline)
            ->groupBy(fn($item) => ($item->product_name ?? '') . '|' . ($item->variant ?? ''))
            ->map(function ($group) {
                return (object) [
                    'product_name' => $group->first()->product_name,
                    'variant'      => $group->first()->variant,
                    'sold'         => $group->sum('sold'),
                    'revenue'      => $group->sum('revenue'),
                ];
            })
            ->sortByDesc('revenue')
            ->values()
            ->take(10);

        return view('admin.reports.index', compact(
            'monthParam',
            'start',
            'end',
            'onlineRevenue',
            'offlineRevenue',
            'totalRevenue',
            'totalTransaction',
            'onlineOrderCount',
            'offlineOrderCount',
            'totalOrderCount',
            'aov',
            'totalShippingCost',
            'totalStockValue',
            'totalCustomers',
            'biteshipShippingCost',
            'biteshipOrderCount',
            'biteshipAvgCost',
            'biteshipApiUsageCount',
            'biteshipApiCost',
            'biteshipApiCostPerHit',
            'chartLabels',
            'chartOnline',
            'chartOffline',
            'chartCombined',
            'orderStatusData',
            'offlineOrderStatusData',
            'paymentMethods',
            'offlinePaymentMethods',
            'topProducts'
        ));
    }

    // ============================================
    // ORDERS — LAPORAN PESANAN
    // ============================================
    public function orders(Request $request)
    {
        [$start, $end, $monthParam] = $this->parseMonth($request);

        $type = $request->input('type', 'all');

        // 🔥 Untuk listing — pakai filter completed
        $onlineQuery = Order::whereBetween('created_at', [$start, $end]);
        $this->applyCompletedFilter($onlineQuery);
        $onlineOrders = $onlineQuery->with('items')->orderBy('created_at', 'desc')->get();

        $offlineQuery = OfflineOrder::whereBetween('created_at', [$start, $end]);
        $this->applyCompletedFilter($offlineQuery);
        $offlineOrders = $offlineQuery->with('items')->orderBy('created_at', 'desc')->get();

        // 🔥 Summary totals — PRODUK NET SETELAH DISKON VOUCHER
        $onlineTotal  = $this->getOnlineProductRevenue($start, $end);
        $offlineTotal = $this->getOfflineProductRevenue($start, $end);
        $totalRevenue = $onlineTotal + $offlineTotal;

        // 🔥 ONGKIR = SEMUA order delivered (TERMASUK retur)
        $onlineShipping    = $this->getOnlineShippingCost($start, $end);
        $offlineShipping   = $this->getOfflineShippingCost($start, $end);
        $totalShippingCost = $onlineShipping + $offlineShipping;

        $totalTransaction  = $totalRevenue;

        $totalOrderCount   = $onlineOrders->count() + $offlineOrders->count();
        $totalCustomers    = User::where('role', 'customer')
            ->whereBetween('created_at', [$start, $end])
            ->count();
        $totalStockValue   = (float) ProductVariant::sum(DB::raw('stock * price'));

        // 🔥 TOTAL RETUR
        $returnCount = Order::whereBetween('created_at', [$start, $end])
            ->whereIn('return_status', ['pending', 'approved', 'completed'])
            ->count();
        $returnCount += OfflineOrder::whereBetween('created_at', [$start, $end])
            ->whereIn('return_status', ['pending', 'approved', 'completed'])
            ->count();

        // 🔥 TOTAL ORDER BATAL
        $cancelledCount = Order::whereBetween('created_at', [$start, $end])
            ->where('shipping_status', 'cancelled')
            ->count();
        $cancelledCount += OfflineOrder::whereBetween('created_at', [$start, $end])
            ->where('shipping_status', 'cancelled')
            ->count();

        $biteshipApiUsageCount = BiteshipApiUsage::whereBetween('created_at', [$start, $end])->count();
        $biteshipApiCost       = $biteshipApiUsageCount * BiteshipApiUsage::COST_PER_HIT;
        $biteshipApiCostPerHit = BiteshipApiUsage::COST_PER_HIT;

        return view('admin.reports.orders', compact(
            'onlineOrders',
            'offlineOrders',
            'start',
            'end',
            'monthParam',
            'type',
            'onlineTotal',
            'offlineTotal',
            'totalRevenue',
            'totalTransaction',
            'totalShippingCost',
            'totalOrderCount',
            'totalCustomers',
            'totalStockValue',
            'returnCount',
            'cancelledCount',
            'biteshipApiUsageCount',
            'biteshipApiCost',
            'biteshipApiCostPerHit'
        ));
    }

    // ============================================
    // PRODUCTS — LAPORAN PRODUK
    // ============================================
    public function products(Request $request)
    {
        [$start, $end, $monthParam] = $this->parseMonth($request);

        $allProducts = Product::with(['variants', 'category'])
            ->orderBy('name')
            ->get();

        // 🔥 Penjualan online — NET SETELAH DISKON VOUCHER (tidak retur)
        $productSalesOnline = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->where('orders.payment_status', 'paid')
            ->where('orders.shipping_status', 'delivered')
            ->where(function ($q) {
                $q->whereNull('orders.return_status')
                ->orWhere('orders.return_status', 'rejected');
            })
            ->selectRaw('order_items.product_name, order_items.variant_name as variant, order_items.sku, SUM(order_items.quantity) as sold, SUM(order_items.subtotal - (orders.discount * order_items.subtotal / NULLIF(orders.subtotal, 0))) as revenue')
            ->groupBy('order_items.product_name', 'order_items.variant_name', 'order_items.sku')
            ->get();

        // 🔥 Penjualan offline — NET SETELAH DISKON VOUCHER (tidak retur)
        $productSalesOffline = OfflineOrderItem::query()
            ->join('offline_orders', 'offline_orders.id', '=', 'offline_order_items.offline_order_id')
            ->whereBetween('offline_orders.created_at', [$start, $end])
            ->where('offline_orders.payment_status', 'paid')
            ->where('offline_orders.shipping_status', 'delivered')
            ->where(function ($q) {
                $q->whereNull('offline_orders.return_status')
                ->orWhere('offline_orders.return_status', 'rejected');
            })
            ->selectRaw('offline_order_items.product_name, offline_order_items.variant_name as variant, offline_order_items.sku, SUM(offline_order_items.quantity) as sold, SUM(offline_order_items.subtotal - (offline_orders.discount * offline_order_items.subtotal / NULLIF(offline_orders.subtotal, 0))) as revenue')
            ->groupBy('offline_order_items.product_name', 'offline_order_items.variant_name', 'offline_order_items.sku')
            ->get();

        $salesData = $productSalesOnline->concat($productSalesOffline)
            ->groupBy(fn($item) => ($item->product_name ?? '') . '|' . ($item->variant ?? '') . '|' . ($item->sku ?? ''))
            ->map(function ($group) {
                return [
                    'product_name' => $group->first()->product_name,
                    'variant' => $group->first()->variant,
                    'sku' => $group->first()->sku,
                    'sold' => $group->sum('sold'),
                    'revenue' => $group->sum('revenue'),
                ];
            });

        $salesByProductName = [];
        foreach ($salesData as $key => $data) {
            $productName = $data['product_name'];
            if (!isset($salesByProductName[$productName])) {
                $salesByProductName[$productName] = [
                    'sold' => 0,
                    'revenue' => 0,
                    'variants' => [],
                ];
            }
            $salesByProductName[$productName]['sold'] += $data['sold'];
            $salesByProductName[$productName]['revenue'] += $data['revenue'];
            $salesByProductName[$productName]['variants'][] = $data;
        }

        $combined = $allProducts->map(function ($product) use ($salesByProductName) {
            $sales = $salesByProductName[$product->name] ?? null;
            $totalStock = $product->variants->sum('stock');
            $priceRange = $product->price_range;
            $firstVariant = $product->variants->first();
            $sku = $firstVariant ? $firstVariant->sku : '-';

            return (object) [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'variant' => $sales ? ($sales['variants'][0]['variant'] ?? '-') : '-',
                'sku' => $sku,
                'sold' => $sales ? $sales['sold'] : 0,
                'revenue' => $sales ? $sales['revenue'] : 0,
                'stock' => $totalStock,
                'price_range' => $priceRange,
                'category' => $product->category->name ?? '-',
                'is_active' => $product->is_active,
                'has_sales' => $sales !== null,
            ];
        })
        ->sortByDesc('revenue')
        ->values();

        $totalProductsSold = $combined->sum('sold');
        $totalRevenue = $this->getOnlineProductRevenue($start, $end) + $this->getOfflineProductRevenue($start, $end);
        $totalUniqueProducts = $combined->count();
        $totalProductsWithSales = $combined->where('has_sales', true)->count();
        $totalProductsWithoutSales = $combined->where('has_sales', false)->count();

        $totalStockValue = (float) ProductVariant::sum(DB::raw('stock * price'));
        $totalStockQuantity = (int) ProductVariant::sum('stock');

        // 🔥 ONGKIR — SEMUA order delivered (termasuk retur)
        $totalShippingCost = $this->getOnlineShippingCost($start, $end)
            + $this->getOfflineShippingCost($start, $end);

        $totalTransaction = $totalRevenue;

        // Total order — hanya delivered & tidak retur
        $totalOrderCount = Order::whereBetween('created_at', [$start, $end])
            ->where('shipping_status', 'delivered')
            ->where(function ($q) {
                $q->whereNull('return_status')
                  ->orWhere('return_status', 'rejected');
            })
            ->count()
            + OfflineOrder::whereBetween('created_at', [$start, $end])
            ->where('shipping_status', 'delivered')
            ->where(function ($q) {
                $q->whereNull('return_status')
                  ->orWhere('return_status', 'rejected');
            })
            ->count();

        $totalCustomers = User::where('role', 'customer')
            ->whereBetween('created_at', [$start, $end])->count();

        $biteshipApiUsageCount = BiteshipApiUsage::whereBetween('created_at', [$start, $end])->count();
        $biteshipApiCost = $biteshipApiUsageCount * BiteshipApiUsage::COST_PER_HIT;
        $biteshipApiCostPerHit = BiteshipApiUsage::COST_PER_HIT;

        return view('admin.reports.products', compact(
            'combined',
            'start',
            'end',
            'monthParam',
            'totalProductsSold',
            'totalRevenue',
            'totalTransaction',
            'totalUniqueProducts',
            'totalProductsWithSales',
            'totalProductsWithoutSales',
            'totalStockValue',
            'totalStockQuantity',
            'totalShippingCost',
            'totalOrderCount',
            'totalCustomers',
            'biteshipApiUsageCount',
            'biteshipApiCost',
            'biteshipApiCostPerHit'
        ));
    }

    // ============================================
    // CUSTOMERS — LAPORAN PELANGGAN
    // ============================================
    public function customers(Request $request)
    {
        [$start, $end, $monthParam] = $this->parseMonth($request);

        $customerStats = User::where('role', 'customer')
            ->whereBetween('created_at', [$start, $end])
            ->withCount([
                'orders' => function ($q) use ($start, $end) {
                    $q->whereBetween('created_at', [$start, $end])
                      ->where('shipping_status', 'delivered')
                      ->where(function ($sub) {
                          $sub->whereNull('return_status')
                              ->orWhere('return_status', 'rejected');
                      });
                }
            ])
            ->withSum([
                'orders as lifetime_revenue' => function ($q) use ($start, $end) {
                    $q->whereBetween('created_at', [$start, $end])
                      ->where('shipping_status', 'delivered')
                      ->where(function ($sub) {
                          $sub->whereNull('return_status')
                              ->orWhere('return_status', 'rejected');
                      });
                }
            ], 'total')
            ->orderByDesc('lifetime_revenue')
            ->paginate(20);

        $totalCustomers = $customerStats->total();

        $totalOrderCount = Order::whereBetween('created_at', [$start, $end])
            ->where('shipping_status', 'delivered')
            ->where(function ($q) {
                $q->whereNull('return_status')
                  ->orWhere('return_status', 'rejected');
            })
            ->count()
            + OfflineOrder::whereBetween('created_at', [$start, $end])
            ->where('shipping_status', 'delivered')
            ->where(function ($q) {
                $q->whereNull('return_status')
                  ->orWhere('return_status', 'rejected');
            })
            ->count();

        // 🔥 Total revenue — PRODUK NET SETELAH DISKON VOUCHER (tidak retur)
        $totalRevenue = $this->getOnlineProductRevenue($start, $end)
            + $this->getOfflineProductRevenue($start, $end);

        $totalStockValue = (float) ProductVariant::sum(DB::raw('stock * price'));

        // 🔥 ONGKIR — semua order delivered (termasuk retur)
        $totalShippingCost = $this->getOnlineShippingCost($start, $end)
            + $this->getOfflineShippingCost($start, $end);

        $totalTransaction = $totalRevenue;

        // Biaya ongkir Biteship
        $biteshipShippingCost = (float) Order::whereBetween('created_at', [$start, $end])
            ->whereNotNull('biteship_order_id')
            ->where('biteship_order_id', '!=', '')
            ->where('shipping_status', '!=', 'cancelled')
            ->whereNotNull('original_shipping_cost')
            ->sum('original_shipping_cost');
        $biteshipOrderCount = Order::whereBetween('created_at', [$start, $end])
            ->whereNotNull('biteship_order_id')
            ->where('biteship_order_id', '!=', '')
            ->count();

        return view('admin.reports.customers', compact(
            'customerStats',
            'start',
            'end',
            'monthParam',
            'totalCustomers',
            'totalOrderCount',
            'totalRevenue',
            'totalTransaction',
            'totalStockValue',
            'totalShippingCost',
            'biteshipShippingCost',
            'biteshipOrderCount'
        ));
    }

    // ============================================
    // EXPORT
    // ============================================
    public function export(Request $request)
    {
        [$start, $end, $monthParam] = $this->parseMonth($request);
        $format = $request->input('format', 'csv');
        $reportType = $request->input('report_type', 'index');

        $filename = 'laporan-' . $start->format('Y-m') . '-' . $reportType;

        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
            ];

            $callback = function () use ($start, $end, $reportType) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

                if ($reportType === 'orders') {
                    $this->exportOrdersCsv($file, $start, $end);
                } elseif ($reportType === 'products') {
                    $this->exportProductsCsv($file, $start, $end);
                } elseif ($reportType === 'customers') {
                    $this->exportCustomersCsv($file, $start, $end);
                } else {
                    $this->exportDashboardCsv($file, $start, $end);
                }

                fclose($file);
            };

            return response()->streamDownload($callback, $filename . '.csv', $headers);
        }

        $data = $this->getReportData($start, $end, $reportType);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.pdf.dashboard', $data);
        return $pdf->download($filename . '.pdf');
    }

    // ============================================
    // EXPORT ORDERS CSV
    // ============================================
    private function exportOrdersCsv($file, $start, $end)
    {
        $onlineOrdersQuery = Order::whereBetween('created_at', [$start, $end]);
        $this->applyCompletedFilter($onlineOrdersQuery);
        $onlineOrders = $onlineOrdersQuery->orderBy('created_at', 'desc')->get();

        $offlineOrdersQuery = OfflineOrder::whereBetween('created_at', [$start, $end]);
        $this->applyCompletedFilter($offlineOrdersQuery);
        $offlineOrders = $offlineOrdersQuery->orderBy('created_at', 'desc')->get();

        fputcsv($file, ['Laporan Pesanan (Selesai & Tidak Retur)', 'Periode: ' . $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y')]);
        fputcsv($file, []);
        fputcsv($file, ['No.', 'No. Order', 'Tanggal', 'Tipe', 'Customer', 'Status Kirim', 'Total']);

        $no = 1;
        foreach ($onlineOrders as $order) {
            fputcsv($file, [
                $no++,
                $order->order_number,
                $order->created_at->format('d/m/Y H:i'),
                'Online',
                $order->shipping_name ?? $order->user->name ?? 'Guest',
                $order->shipping_status_label,
                'Rp ' . number_format($order->total, 0, ',', '.'),
            ]);
        }
        foreach ($offlineOrders as $order) {
            fputcsv($file, [
                $no++,
                $order->order_number,
                $order->created_at->format('d/m/Y H:i'),
                'Offline',
                $order->customer_name ?? 'Walk-in',
                $order->shipping_status_label ?? ucfirst($order->shipping_status),
                'Rp ' . number_format($order->total, 0, ',', '.'),
            ]);
        }

        $totalOnline  = $this->getOnlineProductRevenue($start, $end);
        $totalOffline = $this->getOfflineProductRevenue($start, $end);
        $totalShippingOnline = $this->getOnlineShippingCost($start, $end);
        $totalShippingOffline = $this->getOfflineShippingCost($start, $end);

        fputcsv($file, []);
        fputcsv($file, ['Total Order Online', $onlineOrders->count()]);
        fputcsv($file, ['Total Order Offline', $offlineOrders->count()]);
        fputcsv($file, ['Total Order', $onlineOrders->count() + $offlineOrders->count()]);
        fputcsv($file, ['Total Penjualan Online (termasuk ongkir)', 'Rp ' . number_format($totalOnline, 0, ',', '.')]);
        fputcsv($file, ['Total Penjualan Offline (termasuk ongkir)', 'Rp ' . number_format($totalOffline, 0, ',', '.')]);
        fputcsv($file, ['Total Penjualan (termasuk ongkir)', 'Rp ' . number_format($totalOnline + $totalOffline, 0, ',', '.')]);
        fputcsv($file, ['Total Ongkir (asal, termasuk retur)', 'Rp ' . number_format($totalShippingOnline + $totalShippingOffline, 0, ',', '.')]);
        fputcsv($file, ['Total Transaksi', 'Rp ' . number_format($totalOnline + $totalOffline, 0, ',', '.')]);
    }

    // ============================================
    // EXPORT PRODUCTS CSV
    // ============================================
    private function exportProductsCsv($file, $start, $end)
    {
        $allProducts = Product::with(['variants', 'category'])
            ->orderBy('name')
            ->get();

        $productSalesOnline = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->where('orders.payment_status', 'paid')
            ->where('orders.shipping_status', 'delivered')
            ->where(function ($q) {
                $q->whereNull('orders.return_status')
                ->orWhere('orders.return_status', 'rejected');
            })
            ->selectRaw('order_items.product_name, SUM(order_items.quantity) as sold, SUM(order_items.subtotal - (orders.discount * order_items.subtotal / NULLIF(orders.subtotal, 0))) as revenue')
            ->groupBy('order_items.product_name')
            ->get()
            ->keyBy('product_name');

        $productSalesOffline = OfflineOrderItem::query()
            ->join('offline_orders', 'offline_orders.id', '=', 'offline_order_items.offline_order_id')
            ->whereBetween('offline_orders.created_at', [$start, $end])
            ->where('offline_orders.payment_status', 'paid')
            ->where('offline_orders.shipping_status', 'delivered')
            ->where(function ($q) {
                $q->whereNull('offline_orders.return_status')
                ->orWhere('offline_orders.return_status', 'rejected');
            })
            ->selectRaw('offline_order_items.product_name, SUM(offline_order_items.quantity) as sold, SUM(offline_order_items.subtotal - (offline_orders.discount * offline_order_items.subtotal / NULLIF(offline_orders.subtotal, 0))) as revenue')
            ->groupBy('offline_order_items.product_name')
            ->get()
            ->keyBy('product_name');

        $salesMap = [];
        foreach ($productSalesOnline as $name => $data) {
            $salesMap[$name] = [
                'sold' => $data->sold ?? 0,
                'revenue' => $data->revenue ?? 0,
            ];
        }
        foreach ($productSalesOffline as $name => $data) {
            if (!isset($salesMap[$name])) {
                $salesMap[$name] = ['sold' => 0, 'revenue' => 0];
            }
            $salesMap[$name]['sold'] += $data->sold ?? 0;
            $salesMap[$name]['revenue'] += $data->revenue ?? 0;
        }

        $exportData = $allProducts->map(function ($product) use ($salesMap) {
            $sales = $salesMap[$product->name] ?? null;
            $firstVariant = $product->variants->first();

            return [
                'product_name' => $product->name,
                'sku' => $firstVariant ? $firstVariant->sku : '-',
                'category' => $product->category->name ?? '-',
                'sold' => $sales ? $sales['sold'] : 0,
                'revenue' => $sales ? $sales['revenue'] : 0,
                'stock' => $product->variants->sum('stock'),
                'price_range' => $product->price_range,
            ];
        })->sortByDesc('revenue')->values();

        fputcsv($file, ['Laporan Produk (Semua Produk)', 'Periode: ' . $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y')]);
        fputcsv($file, []);
        fputcsv($file, ['No.', 'Produk', 'SKU', 'Kategori', 'Terjual', 'Pendapatan', 'Stok', 'Range Harga']);

        $no = 1;
        foreach ($exportData as $product) {
            fputcsv($file, [
                $no++,
                $product['product_name'] ?? '-',
                $product['sku'] ?? '-',
                $product['category'] ?? '-',
                $product['sold'] ?? 0,
                'Rp ' . number_format($product['revenue'] ?? 0, 0, ',', '.'),
                $product['stock'] ?? 0,
                $product['price_range'] ?? '-',
            ]);
        }

        fputcsv($file, []);
        fputcsv($file, ['Total Produk', $exportData->count()]);
        fputcsv($file, ['Produk Terjual', $exportData->where('sold', '>', 0)->count()]);
        fputcsv($file, ['Produk Belum Terjual', $exportData->where('sold', '=', 0)->count()]);
        fputcsv($file, ['Total Terjual', $exportData->sum('sold')]);
        fputcsv($file, ['Total Pendapatan Produk', 'Rp ' . number_format($exportData->sum('revenue'), 0, ',', '.')]);
        fputcsv($file, ['Total Stok', $exportData->sum('stock')]);
    }

    // ============================================
    // EXPORT CUSTOMERS CSV
    // ============================================
    private function exportCustomersCsv($file, $start, $end)
    {
        $customers = User::where('role', 'customer')
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->get();

        fputcsv($file, ['Laporan Pelanggan (Pesanan Selesai & Tidak Retur)', 'Periode: ' . $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y')]);
        fputcsv($file, []);
        fputcsv($file, ['No.', 'Nama', 'Email', 'No. HP', 'Order', 'Total Pembayaran']);

        $no = 1;
        foreach ($customers as $customer) {
            $orderCount = $customer->orders()
                ->whereBetween('created_at', [$start, $end])
                ->where('shipping_status', 'delivered')
                ->where(function ($q) {
                    $q->whereNull('return_status')
                      ->orWhere('return_status', 'rejected');
                })
                ->count();

            $totalSpent = Order::query()
                ->where('user_id', $customer->id)
                ->whereBetween('created_at', [$start, $end])
                ->where('payment_status', 'paid')
                ->where('shipping_status', 'delivered')
                ->where(function ($q) {
                    $q->whereNull('return_status')
                    ->orWhere('return_status', 'rejected');
                })
                ->sum('total');

            fputcsv($file, [
                $no++,
                $customer->name,
                $customer->email,
                $customer->phone ?? '-',
                $orderCount,
                'Rp ' . number_format($totalSpent, 0, ',', '.'),
            ]);
        }

        fputcsv($file, []);
        fputcsv($file, ['Total Pelanggan', $customers->count()]);
    }

    // ============================================
    // EXPORT DASHBOARD CSV
    // ============================================
    private function exportDashboardCsv($file, $start, $end)
    {
        $onlineOrdersQuery = Order::whereBetween('created_at', [$start, $end])
            ->where('payment_status', 'paid');
        $this->applyCompletedFilter($onlineOrdersQuery);
        $onlineOrders = $onlineOrdersQuery->get();

        $offlineOrdersQuery = OfflineOrder::whereBetween('created_at', [$start, $end])
            ->where('payment_status', 'paid');
        $this->applyCompletedFilter($offlineOrdersQuery);
        $offlineOrders = $offlineOrdersQuery->get();

        // 🔥 Revenue — TOTAL TRANSAKSI customer (subtotal - voucher + ongkir, tidak retur)
        $onlineRevenue  = $this->getOnlineProductRevenue($start, $end);
        $offlineRevenue = $this->getOfflineProductRevenue($start, $end);
        $totalRevenue   = $onlineRevenue + $offlineRevenue;

        // 🔥 Ongkir — semua order delivered (termasuk retur)
        $onlineShipping   = $this->getOnlineShippingCost($start, $end);
        $offlineShipping  = $this->getOfflineShippingCost($start, $end);
        $totalShipping    = $onlineShipping + $offlineShipping;

        $totalTransaction = $totalRevenue;

        $totalStockValue  = (float) ProductVariant::sum(DB::raw('stock * price'));
        $totalCustomers   = User::where('role', 'customer')->count();
        $totalOrders      = $onlineOrders->count() + $offlineOrders->count();
        $aov              = $totalOrders > 0 ? round($totalRevenue / $totalOrders) : 0;

        $biteshipApiUsageCount = BiteshipApiUsage::whereBetween('created_at', [$start, $end])->count();
        $biteshipApiCost       = $biteshipApiUsageCount * BiteshipApiUsage::COST_PER_HIT;
        $biteshipApiCostPerHit = BiteshipApiUsage::COST_PER_HIT;

        $returnCount = Order::whereBetween('created_at', [$start, $end])
            ->whereIn('return_status', ['pending', 'approved', 'completed'])
            ->count();
        $returnCount += OfflineOrder::whereBetween('created_at', [$start, $end])
            ->whereIn('return_status', ['pending', 'approved', 'completed'])
            ->count();

        $cancelledCount = Order::whereBetween('created_at', [$start, $end])
            ->where('shipping_status', 'cancelled')
            ->count();
        $cancelledCount += OfflineOrder::whereBetween('created_at', [$start, $end])
            ->where('shipping_status', 'cancelled')
            ->count();

        fputcsv($file, ['=== RINGKASAN (Pesanan Selesai & Tidak Retur) ===']);
        fputcsv($file, ['Periode', $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y')]);
        fputcsv($file, []);
        fputcsv($file, ['Metrik', 'Nilai']);
        fputcsv($file, ['--- TOTAL PENJUALAN (termasuk ongkir, net diskon voucher) ---', '']);
        fputcsv($file, ['Total Penjualan Online', 'Rp ' . number_format($onlineRevenue, 0, ',', '.')]);
        fputcsv($file, ['Total Penjualan Offline', 'Rp ' . number_format($offlineRevenue, 0, ',', '.')]);
        fputcsv($file, ['Total Penjualan', 'Rp ' . number_format($totalRevenue, 0, ',', '.')]);
        fputcsv($file, []);
        fputcsv($file, ['--- INFO TAMBAHAN ---', '']);
        fputcsv($file, ['Total Ongkir (asal, termasuk retur)', 'Rp ' . number_format($totalShipping, 0, ',', '.')]);
        fputcsv($file, ['Total Transaksi (termasuk ongkir)', 'Rp ' . number_format($totalTransaction, 0, ',', '.')]);
        fputcsv($file, ['Biaya API Biteship', 'Rp ' . number_format($biteshipApiCost, 0, ',', '.') . ' (' . number_format($biteshipApiUsageCount) . ' hit × Rp ' . number_format($biteshipApiCostPerHit) . ')']);
        fputcsv($file, ['Total Stok Barang', 'Rp ' . number_format($totalStockValue, 0, ',', '.')]);
        fputcsv($file, ['Total Pelanggan', $totalCustomers]);
        fputcsv($file, ['Total Order Online', $onlineOrders->count()]);
        fputcsv($file, ['Total Order Offline', $offlineOrders->count()]);
        fputcsv($file, ['Total Order', $totalOrders]);
        fputcsv($file, ['Rata-rata Order Value (Produk)', 'Rp ' . number_format($aov, 0, ',', '.')]);
        fputcsv($file, []);
        fputcsv($file, ['Total Retur', $returnCount]);
        fputcsv($file, ['Total Order Batal', $cancelledCount]);
        fputcsv($file, []);
        fputcsv($file, ['Dicetak pada: ' . now()->format('d/m/Y H:i:s')]);
    }

    // ============================================
    // GET REPORT DATA (untuk PDF)
    // ============================================
    private function getReportData($start, $end, $type)
    {
        $onlineQuery = Order::whereBetween('created_at', [$start, $end])
            ->where('payment_status', 'paid');
        $this->applyCompletedFilter($onlineQuery);
        $onlineOrders = $onlineQuery->with('items')->orderBy('created_at', 'desc')->get();

        $offlineQuery = OfflineOrder::whereBetween('created_at', [$start, $end])
            ->where('payment_status', 'paid');
        $this->applyCompletedFilter($offlineQuery);
        $offlineOrders = $offlineQuery->with('items')->orderBy('created_at', 'desc')->get();

        $onlineRevenue = (float) $onlineOrders->sum('total');
        $offlineRevenue = (float) $offlineOrders->sum('total');
        $totalRevenue = $onlineRevenue + $offlineRevenue;

        // 🔥 ONGKIR — semua order delivered (termasuk retur)
        $totalShipping = $this->getOnlineShippingCost($start, $end)
                       + $this->getOfflineShippingCost($start, $end);
        $totalTransaction = $totalRevenue;

        return compact(
            'onlineOrders',
            'offlineOrders',
            'onlineRevenue',
            'offlineRevenue',
            'totalRevenue',
            'totalShipping',
            'totalTransaction',
            'start',
            'end'
        );
    }
}