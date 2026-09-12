<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\BiteshipService;
use App\Services\LabelPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected $biteship;

    public function __construct(BiteshipService $biteship)
    {
        $this->biteship = $biteship;
    }
    // ============================================
    // INDEX
    // ============================================

    public function index(Request $request)
    {
        $query = Order::with(['customer', 'items'])
            ->orderBy('created_at', 'desc');

        // Filter by shipping_status
        if ($request->filled('shipping_status')) {
            // 🔥 TAB BARU: Permintaan Pembatalan
            if ($request->shipping_status === 'cancellation_requested') {
                $query->where('cancellation_status', 'pending');
            }
            elseif ($request->shipping_status === 'delivered') {
                $query->where(function ($q) {
                    $q->where('shipping_status', 'delivered')
                        ->orWhereNotNull('delivered_at');
                });
            }
            else {
                $query->where('shipping_status', $request->shipping_status);
            }
        }

        // Filter by payment_status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Search by order number or customer name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $orders = $query->paginate(20)->withQueryString();

        $statusCounts = [
            'total' => Order::count(),
            'pending' => Order::where('shipping_status', 'pending')->count(),
            'processing' => Order::where('shipping_status', 'processing')->count(),
            'shipped' => Order::where('shipping_status', 'shipped')->count(),
            'delivered' => Order::where('shipping_status', 'delivered')->count(),
            'cancelled' => Order::where('shipping_status', 'cancelled')->count(),
            'unpaid' => Order::where('payment_status', 'unpaid')->count(),
            'paid' => Order::where('payment_status', 'paid')->count(),

            // 🔥 TAB BARU
            'cancellation_requested' => Order::where('cancellation_status', 'pending')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'statusCounts'));
    }

    // ============================================
    // SHOW
    // ============================================

    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'items.variant']);

        return view('admin.orders.show', compact('order'));
    }

    // ============================================
    // UPDATE SHIPPING
    // ============================================

    public function updateShipping(Request $request, Order $order)
    {
        $validated = $request->validate([
            'courier' => 'nullable|string|max:100',
            'service' => 'nullable|string|max:100',
            'tracking_number' => 'nullable|string|max:100',
            'shipping_status' => 'required|in:pending,processing,shipped,delivered',
        ]);

        if ($validated['shipping_status'] === 'shipped' && $order->shipping_status !== 'shipped') {
            $order->shipped_at = now();
        }

        if ($validated['shipping_status'] === 'delivered' && $order->shipping_status !== 'delivered') {
            $order->delivered_at = now();
        }

        $order->courier = $validated['courier'];
        $order->service = $validated['service'];
        $order->tracking_number = $validated['tracking_number'];
        $order->shipping_status = $validated['shipping_status'];
        $order->save();

        $autoCreated = false;

        if ($validated['shipping_status'] === 'shipped' && ! $order->biteship_order_id) {
            $biteshipResult = $this->attemptCreateBiteshipOrder($order);

            if ($biteshipResult['success']) {
                $autoCreated = true;
            }
        }

        $message = 'Informasi pengiriman berhasil diperbarui.';

        if ($autoCreated) {
            $message .= ' Order Biteship berhasil dibuat secara otomatis!';
        }

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', $message);
    }

    // ============================================
    // BULK SHIP
    // ============================================

    public function bulkShip(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
            'courier' => 'nullable|string|max:100',
            'service' => 'nullable|string|max:100',
        ]);

        $orders = Order::whereIn('id', $request->order_ids)
            ->where('shipping_status', 'processing')
            ->get();

        if ($orders->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada pesanan yang valid untuk dikirim.',
            ], 400);
        }

        $shipped = 0;
        $failed = 0;
        $errors = [];

        foreach ($orders as $order) {
            DB::beginTransaction();
            try {
                $order->shipped_at = now();
                if ($request->filled('courier')) {
                    $order->courier = $request->courier;
                }
                if ($request->filled('service')) {
                    $order->service = $request->service;
                }
                $order->shipping_status = 'shipped';
                $order->save();

                if (! $order->biteship_order_id) {
                    $biteshipResult = $this->attemptCreateBiteshipOrder($order);
                    if (! $biteshipResult['success']) {
                        $errors[] = "Order {$order->order_number}: ".($biteshipResult['message'] ?? 'Biteship gagal');
                    }
                }
                DB::commit();
                $shipped++;
            } catch (\Exception $e) {
                DB::rollBack();
                $failed++;
                $errors[] = "Order {$order->order_number}: ".$e->getMessage();
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil kirim {$shipped} pesanan.".($failed > 0 ? " Gagal: {$failed} pesanan." : ''),
            'shipped' => $shipped,
            'failed' => $failed,
            'errors' => $errors,
        ]);
    }

    // ============================================
    // BULK PRINT LABEL
    // ============================================

    public function bulkPrintLabel(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
        ]);

        $orders = Order::whereIn('id', $request->order_ids)
            ->where('shipping_status', 'shipped')
            ->get();

        if ($orders->isEmpty()) {
            return back()->with('error', 'Tidak ada pesanan yang valid untuk mencetak label.');
        }

        // 🔥 Increase time & memory limits for bulk PDF processing
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        Log::info('Processing bulk label generation', [
            'total_orders' => $orders->count(),
            'order_numbers' => $orders->pluck('order_number')->toArray(),
        ]);

        try {
            $labelPdfService = new LabelPdfService;
            $pdfContent = $labelPdfService->generateBulkLabels($orders->all());

            if ($pdfContent) {
                return response($pdfContent, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="combined_labels_'.now()->format('Y-m-d_H-i').'.pdf"',
                    'Content-Length' => strlen($pdfContent),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Bulk label PDF generation failed: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Gagal mencetak label massal: '.$e->getMessage());
        }

        return back()->with('error', 'Gagal mencetak label massal.');
    }

    public function printLabel(Order $order)
    {
        try {
            $labelPdfService = new LabelPdfService;
            $pdfContent = $labelPdfService->generateSingleLabel($order);

            return response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="label_'.$order->order_number.'.pdf"',
                'Content-Length' => strlen($pdfContent),
            ]);
        } catch (\Exception $e) {
            Log::error('Label PDF generation failed: '.$e->getMessage(), [
                'order_id' => $order->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->route('admin.orders.show', $order)
                ->with('error', 'Gagal mencetak label: '.$e->getMessage());
        }
    }

    public function tracking(Order $order)
    {
        $order->load(['user', 'items.product.images', 'items.variant']);

        $tracking = null;
        $trackingError = null;

        $cacheKey = 'admin:tracking:'.$order->id.':'.$order->updated_at->timestamp;
        $cached = Cache::get($cacheKey);

        if ($cached) {
            $tracking = $cached['tracking'] ?? null;
            $trackingError = $cached['trackingError'] ?? null;
        } elseif ($order->biteship_order_id) {
            $result = $this->biteship->getTrackingDetails(
                $order->biteship_order_id,
                $order->tracking_number,
                $order->biteship_tracking_url
            );

            if ($result['success']) {
                $tracking = $result['data'];

                if (empty($tracking['history'] ?? [])) {
                    $trackingError = 'Tracking belum memiliki riwayat pengiriman.';
                    $tracking = $this->getSimulatedTracking($order);
                }
            } else {
                $trackingError = $result['message'];
                $tracking = $this->getSimulatedTracking($order);
            }
        } else {
            $trackingError = 'Order belum memiliki tracking.';
            $tracking = $this->getSimulatedTracking($order);
        }

        Cache::put($cacheKey, ['tracking' => $tracking, 'trackingError' => $trackingError], now()->addMinutes(5));

        return view('admin.orders.tracking', compact('order', 'tracking', 'trackingError'));
    }

    /**
     * 🔥 REFRESH TRACKING - TAMBAHKAN METHOD INI
     */
    public function refreshTracking(Request $request, Order $order)
    {
        try {
            Cache::forget('admin:tracking:'.$order->id.':'.$order->updated_at->timestamp);

            Log::info('Refresh tracking called:', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'biteship_order_id' => $order->biteship_order_id,
            ]);

            if (! $order->biteship_order_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order tidak memiliki ID tracking Biteship.',
                ], 400);
            }

            $result = $this->biteship->getTrackingDetails(
                $order->biteship_order_id,
                $order->tracking_number,
                $order->biteship_tracking_url
            );

            if ($result['success']) {
                // 🔥 SIMPAN STATUS TERBARU KE ORDER AGAR TAMPILAN SELALU UPDATE
                $this->syncTrackingToOrder($order, $result['data']);

                return response()->json([
                    'success' => true,
                    'data' => $result['data'],
                    'message' => 'Tracking berhasil diperbarui.',
                ]);
            }

            // 🔥 ORDER ID TIDAK DITEMUKAN DI BITESHIP (DATA TESTING / SUDAH TERHAPUS)
            // → fallback ke data simulasi dari status order lokal agar halaman tetap jalan
            if (str_contains(strtolower($result['message'] ?? ''), 'not found') || str_contains(strtolower($result['message'] ?? ''), 'tidak ditemukan')) {
                $simulated = $this->getSimulatedTracking($order);

                return response()->json([
                    'success' => true,
                    'data' => $simulated,
                    'message' => 'Menampilkan data lokal (order tidak ditemukan di Biteship).',
                    'simulated' => true,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Gagal refresh tracking',
            ], 500);

        } catch (\Exception $e) {
            Log::error('Refresh tracking error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: '.$e->getMessage(),
            ], 500);
        }
    }

    public function createBiteshipOrder(Order $order)
    {
        $result = $this->attemptCreateBiteshipOrder($order);

        if ($result['success']) {
            return redirect()
                ->route('admin.orders.tracking', $order)
                ->with('success', 'Order berhasil dibuat di Biteship! ID: '.($result['biteship_order_id'] ?? ''));
        }

        return redirect()
            ->route('admin.orders.tracking', $order)
            ->with('error', 'Gagal membuat order di Biteship: '.($result['message'] ?? 'Unknown error'));
    }

    public function approveCancellation(Order $order)
    {
        $order->loadMissing(['items.variant', 'items.product.variants']);

        Log::info('Approve cancellation called:', [
            'order_id' => $order->id,
            'cancellation_status' => $order->cancellation_status,
        ]);

        // Cek apakah ada request cancellation
        if ($order->cancellation_status !== 'pending') {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with('error', 'Tidak ada permintaan pembatalan yang pending untuk pesanan ini. Status saat ini: '.($order->cancellation_status ?? 'NULL'));
        }

        try {
            DB::beginTransaction();

            // Restore stok
            foreach ($order->items as $item) {
                if ($item->variant) {
                    $item->variant->addStock(
                        $item->quantity,
                        'order_cancelled',
                        "Pesanan {$order->order_number} dibatalkan oleh admin"
                    );
                } elseif ($item->product) {
                    $firstVariant = $item->product->variants->first();
                    if ($firstVariant) {
                        $firstVariant->addStock(
                            $item->quantity,
                            'order_cancelled',
                            "Pesanan {$order->order_number} dibatalkan oleh admin"
                        );
                    }
                }
            }

            // Update order
            $order->update([
                'shipping_status' => 'cancelled',
                'cancelled_at' => now(),
                'cancelled_by_admin_id' => Auth::id(),
                'cancellation_status' => 'approved',
                'cancellation_processed_at' => now(),
                'previous_shipping_status' => null,
            ]);

            DB::commit();

            Log::info('Cancellation approved by admin', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'admin_id' => Auth::id(),
            ]);

            Cache::forget('sidebar_badges');

            return redirect()
                ->route('admin.orders.show', $order)
                ->with('success', 'Permintaan pembatalan berhasil disetujui. Stok produk telah dikembalikan.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error approving cancellation:', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->route('admin.orders.show', $order)
                ->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function rejectCancellation(Order $order)
    {
        Log::info('Reject cancellation called:', [
            'order_id' => $order->id,
            'cancellation_status' => $order->cancellation_status,
        ]);

        // Cek apakah ada request cancellation
        if ($order->cancellation_status !== 'pending') {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with('error', 'Tidak ada permintaan pembatalan yang pending untuk pesanan ini. Status saat ini: '.($order->cancellation_status ?? 'NULL'));
        }

        try {
            $previousStatus = $order->previous_shipping_status ?? 'pending';

            $order->update([
                'cancellation_status' => 'rejected',
                'shipping_status' => $previousStatus,
                'cancellation_processed_at' => now(),
                'cancelled_by_admin_id' => Auth::id(),
                'previous_shipping_status' => null,
            ]);

            Log::info('Cancellation rejected by admin', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'admin_id' => Auth::id(),
            ]);

            Cache::forget('sidebar_badges');

            return redirect()
                ->route('admin.orders.show', $order)
                ->with('success', 'Permintaan pembatalan berhasil ditolak.');

        } catch (\Exception $e) {
            Log::error('Error rejecting cancellation:', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->route('admin.orders.show', $order)
                ->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function approveReturn(Order $order)
    {
        $order->loadMissing(['items.variant', 'items.product.variants']);

        Log::info('Approve return called:', [
            'order_id' => $order->id,
            'return_status' => $order->return_status,
        ]);

        if ($order->return_status !== 'pending') {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with('error', 'Tidak ada permintaan retur yang pending untuk pesanan ini.');
        }

        try {
            $order->approveReturn(Auth::id());

            Cache::forget('sidebar_badges');

            return redirect()
                ->route('admin.orders.show', $order)
                ->with('success', 'Permintaan retur disetujui. Stok akan dikembalikan saat retur selesai.');

        } catch (\Exception $e) {
            Log::error('Error approving return:', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('admin.orders.show', $order)
                ->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function rejectReturn(Order $order)
    {
        Log::info('Reject return called:', [
            'order_id' => $order->id,
            'return_status' => $order->return_status,
        ]);

        if ($order->return_status !== 'pending') {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with('error', 'Tidak ada permintaan retur yang pending untuk pesanan ini.');
        }

        try {
            $order->rejectReturn(Auth::id());

            Cache::forget('sidebar_badges');

            return redirect()
                ->route('admin.orders.show', $order)
                ->with('success', 'Permintaan retur berhasil ditolak.');
        } catch (\Exception $e) {
            Log::error('Error rejecting return:', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('admin.orders.show', $order)
                ->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    private function attemptCreateBiteshipOrder(Order $order): array
    {
        if ($order->biteship_order_id) {
            return [
                'success' => false,
                'message' => 'Order ini sudah memiliki ID Biteship: '.$order->biteship_order_id,
                'biteship_order_id' => $order->biteship_order_id,
            ];
        }

        $missingFields = [];
        if (empty($order->shipping_name)) {
            $missingFields[] = 'nama pengirim';
        }
        if (empty($order->shipping_phone)) {
            $missingFields[] = 'telepon pengirim';
        }
        if (empty($order->shipping_address)) {
            $missingFields[] = 'alamat pengirim';
        }
        if (empty($order->shipping_postal_code)) {
            $missingFields[] = 'kode pos pengirim';
        }
        if (empty($order->courier)) {
            $missingFields[] = 'kurir';
        }
        if (empty($order->service)) {
            $missingFields[] = 'layanan';
        }

        if ($missingFields) {
            return [
                'success' => false,
                'message' => 'Data pengiriman belum lengkap: '.implode(', ', $missingFields),
            ];
        }

        $order->loadMissing('items');

        if (! $order->items || $order->items->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Order tidak memiliki item. Tidak dapat membuat pengiriman.',
            ];
        }

        $items = [];
        foreach ($order->items as $item) {
            $items[] = [
                'name' => $item->product_name.($item->variant_name ? ' - '.$item->variant_name : ''),
                'value' => (int) $item->price,
                'weight' => (int) ($item->variant?->weight ?? $item->product?->weight ?? 1000),
                'quantity' => (int) $item->quantity,
            ];
        }

        $data = [
            'shipping_name' => $order->shipping_name,
            'shipping_phone' => $order->shipping_phone,
            'shipping_address' => $order->shipping_address,
            'shipping_postal_code' => $order->shipping_postal_code ?? '0',
            'courier' => $order->courier ?? 'jne',
            'service' => $order->service ?? 'REG',
            'items' => $items,
            'order_number' => $order->order_number,
        ];

        $result = $this->biteship->createOrder($data);

        if ($result['success']) {
            $biteshipData = $result['data'];
            $order->update([
                'biteship_order_id' => $biteshipData['id'] ?? null,
                'tracking_number' => $biteshipData['courier']['waybill_id'] ?? $biteshipData['courier']['tracking_id'] ?? $biteshipData['waybill_id'] ?? $biteshipData['tracking_id'] ?? $order->tracking_number,
                'biteship_tracking_url' => $biteshipData['courier']['link'] ?? $biteshipData['tracking_url'] ?? null,
            ]);

            return [
                'success' => true,
                'message' => 'Order Biteship berhasil dibuat! Resi: '.($biteshipData['courier']['waybill_id'] ?? $biteshipData['courier']['tracking_id'] ?? $biteshipData['waybill_id'] ?? $biteshipData['tracking_id'] ?? '-'),
                'biteship_order_id' => $biteshipData['id'] ?? null,
            ];
        }

        return [
            'success' => false,
            'message' => $result['message'] ?? 'Unknown error',
        ];
    }

    /**
     * 🔥 SINKRONKAN DATA TRACKING BITESHIP KE ORDER LOKAL
     */
    private function syncTrackingToOrder($order, array $tracking): void
    {
        $updates = [];

        if (! empty($tracking['waybill_id']) && $tracking['waybill_id'] !== $order->tracking_number) {
            $updates['tracking_number'] = $tracking['waybill_id'];
        }

        if (! empty($tracking['waybill_url']) && $tracking['waybill_url'] !== $order->biteship_tracking_url) {
            $updates['biteship_tracking_url'] = $tracking['waybill_url'];
        }

        // 🔥 PETAKAN STATUS BITESHIP → shipping_status LOKAL
        $statusMap = [
            'allocated' => 'processing',
            'picking_up' => 'processing',
            'picked' => 'shipped',
            'dropping_off' => 'shipped',
            'on_hold' => 'processing',
            'delivered' => 'delivered',
            'rejected' => 'pending',
            'cancelled' => 'pending',
            'disposed' => 'pending',
        ];
        $biteshipStatus = strtolower($tracking['status'] ?? '');
        $mappedStatus = $statusMap[$biteshipStatus] ?? null;

        if ($mappedStatus && $mappedStatus !== $order->shipping_status) {
            $updates['shipping_status'] = $mappedStatus;

            if ($mappedStatus === 'delivered' && ! $order->delivered_at) {
                $updates['delivered_at'] = now();
            }

            if ($mappedStatus === 'shipped' && ! $order->shipped_at) {
                $updates['shipped_at'] = now();
            }
        }

        if (! empty($updates)) {
            $order->update($updates);
        }
    }

    /**
     * 🔥 GET SIMULATED TRACKING DATA UNTUK TESTING
     */
    private function getSimulatedTracking($order)
    {
        $randomStatus = $order->shipping_status ?? 'pending';

        $simulatedData = [
            'success' => true,
            'data' => [
                'courier_name' => $order->courier ?? 'JNE',
                'waybill_id' => $order->tracking_number ?? 'TEST-'.strtoupper(uniqid()),
                'status' => $randomStatus,
                'service' => $order->service ?? 'Reguler',
                'updated_at' => now()->toISOString(),
                'delivery_date' => now()->addDays(3)->toISOString(),
                'history' => $this->generateSimulatedHistory($randomStatus),
                'waybill_url' => null,
            ],
        ];

        return $simulatedData['data'];
    }

    /**
     * 🔥 GENERATE SIMULATED HISTORY
     */
    private function generateSimulatedHistory($status)
    {
        $histories = [];

        // 1. Order dibuat
        $histories[] = [
            'status' => 'pending',
            'description' => 'Pesanan telah dibuat',
            'location' => 'Tasikmalaya',
            'time' => now()->subDays(2)->toISOString(),
            'note' => 'Menunggu konfirmasi admin',
        ];

        // 2. Diproses
        $histories[] = [
            'status' => 'processing',
            'description' => 'Pesanan sedang diproses',
            'location' => 'Tasikmalaya',
            'time' => now()->subDays(1)->toISOString(),
            'note' => 'Sedang disiapkan untuk pengiriman',
        ];

        // 3. Dikirim (jika status shipped atau delivered)
        if (in_array($status, ['shipped', 'delivered'])) {
            $histories[] = [
                'status' => 'shipped',
                'description' => 'Pesanan telah dikirim',
                'location' => 'Tasikmalaya',
                'time' => now()->subHours(12)->toISOString(),
                'note' => 'Paket telah diambil oleh kurir',
            ];
        }

        // 4. Dalam perjalanan
        if (in_array($status, ['shipped', 'delivered'])) {
            $histories[] = [
                'status' => 'shipped',
                'description' => 'Paket dalam perjalanan',
                'location' => 'Bandung',
                'time' => now()->subHours(6)->toISOString(),
                'note' => 'Paket sedang menuju ke tujuan',
            ];
        }

        // 5. Telah sampai (jika status delivered)
        if ($status === 'delivered') {
            $histories[] = [
                'status' => 'delivered',
                'description' => 'Paket telah sampai tujuan',
                'location' => $order->shipping_city ?? 'Jakarta',
                'time' => now()->toISOString(),
                'note' => 'Pesanan telah diterima oleh penerima',
            ];
        }

        return $histories;
    }
}
