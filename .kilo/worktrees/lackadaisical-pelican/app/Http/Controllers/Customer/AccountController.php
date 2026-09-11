<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use App\Models\Order;
use App\Models\Wishlist;
use App\Models\Voucher;
use App\Services\BiteshipService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountController extends Controller
{
    protected function getAccountData(string $activeTab = 'all')
    {
        $customer = Auth::guard('customer')->user();
        
        $baseQuery = Order::where('user_id', $customer->id);
        
        $orderCount = (clone $baseQuery)->count();
        $unpaidCount = (clone $baseQuery)->where('payment_status', 'unpaid')->count();
        $processingCount = (clone $baseQuery)->where('shipping_status', 'processing')->count();
        $shippedCount = (clone $baseQuery)->where('shipping_status', 'shipped')->count();
        $completedCount = (clone $baseQuery)->where(function ($q) {
            $q->where('shipping_status', 'delivered')
              ->orWhereNotNull('delivered_at');
        })->count();
        
        $wishlistCount = Wishlist::where('user_id', $customer->id)->count();
        
        // 🔥 HITUNG VOUCHER TERSEDIA
        $availableVouchers = Voucher::where('is_active', true)
            ->where('is_public', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where(function($q) use ($customer) {
                $q->whereNotExists(function($sub) use ($customer) {
                    $sub->select('id')
                        ->from('voucher_usages')
                        ->whereColumn('voucher_usages.voucher_id', 'vouchers.id')
                        ->where('voucher_usages.user_id', $customer->id);
                });
            })
            ->count();

        return [
            'orderCount' => $orderCount,
            'unpaidCount' => $unpaidCount,
            'processingCount' => $processingCount,
            'shippedCount' => $shippedCount,
            'completedCount' => $completedCount,
            'wishlistCount' => $wishlistCount,
            'availableVouchers' => $availableVouchers,
        ];
    }

    public function index()
    {
        $user = Auth::guard('customer')->user();
        $data = $this->getAccountData();
        
        return view('customer.account.index', array_merge(['user' => $user], $data));
    }

    public function orders(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        $tab = $request->query('tab', 'unpaid');

        $query = Order::with(['items.product', 'items.variant'])
            ->where('user_id', $customer->id);

        switch ($tab) {
            case 'unpaid':
                $query->where('payment_status', 'unpaid');
                break;
            case 'processing':
                $query->where('shipping_status', 'processing');
                break;
            case 'shipped':
                $query->where('shipping_status', 'shipped');
                break;
            case 'completed':
                $query->where(function ($q) {
                    $q->where('shipping_status', 'delivered')
                      ->orWhereNotNull('delivered_at');
                });
                break;
            default:
                break;
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        $data = $this->getAccountData($tab);

        return view('customer.account.orders', array_merge(['orders' => $orders, 'activeTab' => $tab], $data));
    }

    // ============================================
    // ADDRESS CRUD
    // ============================================

    public function indexAddresses()
    {
        $customer = Auth::guard('customer')->user();

        $addresses = $customer->addresses()
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $data = $this->getAccountData();

        return view('customer.account.addresses.index', array_merge([
            'addresses' => $addresses,
        ], $data));
    }

    public function createAddress()
    {
        return view('customer.account.addresses.create');
    }

    public function storeAddress(Request $request)
    {
        $request->merge([
            'district' => $request->input('district') ?: $request->input('district_select'),
            'subdistrict' => $request->input('subdistrict') ?: $request->input('subdistrict_select'),
        ]);

        $validated = $request->validate([
            'label' => 'nullable|string|max:50',
            'recipient_name' => 'required|string|max:100',
            'recipient_phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'subdistrict' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'is_default' => 'boolean',
        ]);

        $customer = Auth::guard('customer')->user();

        if ($customer->addresses()->count() === 0) {
            $validated['is_default'] = true;
        }

        if (isset($validated['is_default']) && $validated['is_default']) {
            $customer->addresses()->update(['is_default' => false]);
        }

        $validated['user_id'] = $customer->id;
        $validated['user_id'] = $customer->id;

        UserAddress::create($validated);

        return redirect()
            ->route('customer.addresses.index')
            ->with('success', 'Alamat berhasil ditambahkan.');
    }

    public function editAddress(int $address)
    {
        $customer = Auth::guard('customer')->user();
        $address = $customer->addresses()->findOrFail($address);

        // 🔥 Debug: cek data address
        \Log::info('Edit Address Data:', [
            'id' => $address->id,
            'postal_code' => $address->postal_code,
            'city' => $address->city,
            'province' => $address->province,
        ]);

        return view('customer.account.addresses.edit', compact('address'));
    }

    public function updateAddress(Request $request, int $address)
    {
        $customer = Auth::guard('customer')->user();
        $address = $customer->addresses()->findOrFail($address);

        $request->merge([
            'district' => $request->input('district') ?: $request->input('district_select'),
            'subdistrict' => $request->input('subdistrict') ?: $request->input('subdistrict_select'),
        ]);

        $validated = $request->validate([
            'label' => 'nullable|string|max:50',
            'recipient_name' => 'required|string|max:100',
            'recipient_phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'subdistrict' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'is_default' => 'boolean',
        ]);

        if (isset($validated['is_default']) && $validated['is_default']) {
            $customer->addresses()
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        $validated['user_id'] = $customer->id;
        $validated['user_id'] = $customer->id;

        $address->update($validated);
        DB::table('user_addresses')
            ->where('id', $address->id)
            ->update([
                'district' => $validated['district'],
                'subdistrict' => $validated['subdistrict'],
                'updated_at' => now(),
            ]);

        return redirect()
            ->route('customer.addresses.index')
            ->with('success', 'Alamat berhasil diperbarui.');
    }

    public function destroyAddress(int $address)
    {
        $customer = Auth::guard('customer')->user();
        $address = $customer->addresses()->findOrFail($address);

        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $firstAddress = $customer->addresses()->first();

            if ($firstAddress) {
                $firstAddress->update(['is_default' => true]);
            }
        }

        return redirect()
            ->route('customer.addresses.index')
            ->with('success', 'Alamat berhasil dihapus.');
    }

    public function showOrder(Order $order)
    {
        // Pastikan order milik customer yang login
        if ($order->user_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        $order->load(['items.product', 'items.variant']);

        return view('customer.account.order-detail', compact('order'));
    }

    public function tracking(Order $order)
    {
        // 🔥 CEK APAKAH ORDER MILIK CUSTOMER INI
        if ($order->user_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        $order->load(['customer', 'items.product', 'items.variant']);

        $tracking = null;
        $trackingError = null;

        if ($order->biteship_order_id) {
            $biteship = app(BiteshipService::class);
            
            $result = $biteship->getTrackingDetails(
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

        return view('customer.orders.tracking', compact('order', 'tracking', 'trackingError'));
    }

    public function requestCancellation(Request $request, Order $order)
    {
        // Pastikan order milik customer yang login
        if ($order->user_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        $request->validate([
            'reason' => 'required|string|min:10|max:500',
        ]);

        try {
            // Cek apakah bisa dibatalkan
            if (!$order->canBeCancelled()) {
                return redirect()
                    ->route('customer.orders.show', $order)
                    ->with('error', 'Pesanan tidak dapat dibatalkan karena sudah dalam proses pengiriman atau selesai.');
            }

            // 🔥 PERBAIKAN: Gunakan DB transaction dan dd untuk debug
            DB::beginTransaction();
            
            try {
                // Simpan status sebelumnya
                $previousStatus = $order->shipping_status;

                // Update order
                $updated = $order->update([
                    'cancellation_status' => 'pending',
                    'cancellation_reason' => $request->reason,
                    'cancellation_requested_at' => now(),
                    'previous_shipping_status' => $previousStatus,
                    // JANGAN ubah shipping_status
                ]);

                // 🔥 DEBUG: Cek apakah update berhasil
                Log::info('Cancellation update result:', [
                    'order_id' => $order->id,
                    'updated' => $updated,
                    'cancellation_status' => $order->cancellation_status,
                ]);

                // 🔥 Refresh model dari database
                $order->refresh();

                DB::commit();

                Log::info('Customer requested cancellation', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'user_id' => Auth::guard('customer')->id(),
                    'reason' => $request->reason,
                    'cancellation_status' => $order->cancellation_status,
                ]);

                return redirect()
                    ->route('customer.orders.show', $order)
                    ->with('success', 'Permintaan pembatalan pesanan berhasil dikirim. Menunggu persetujuan admin.');

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Error requesting cancellation:', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->route('customer.orders.show', $order)
                ->with('error', 'Terjadi kesalahan saat memproses pembatalan: ' . $e->getMessage());
        }
    }

    public function cancelOrderDirect(Request $request, Order $order)
    {
        // Pastikan order milik customer yang login
        if ($order->user_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        // Hanya bisa dibatalkan jika status pending dan belum dibayar
        if ($order->shipping_status !== 'pending' || $order->payment_status !== 'unpaid') {
            return redirect()
                ->route('customer.orders.show', $order)
                ->with('error', 'Pesanan tidak dapat dibatalkan secara langsung. Silakan gunakan fitur request cancellation.');
        }

        try {
            $result = $order->cancelByCustomer('Dibatalkan oleh customer (langsung)');

            if ($result) {
                return redirect()
                    ->route('customer.orders.show', $order)
                    ->with('success', 'Pesanan berhasil dibatalkan.');
            }

            return redirect()
                ->route('customer.orders.show', $order)
                ->with('error', 'Gagal membatalkan pesanan.');

        } catch (\Exception $e) {
            Log::error('Error cancelling order directly:', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('customer.orders.show', $order)
                ->with('error', 'Terjadi kesalahan saat membatalkan pesanan.');
        }
    }

    /**
     * 🔥 GET SIMULATED TRACKING UNTUK CUSTOMER
     */
    private function getSimulatedTracking($order)
    {
        $randomStatus = $order->shipping_status ?? 'pending';

        return [
            'courier_name' => $order->courier ?? 'JNE',
            'waybill_id' => $order->tracking_number ?? 'TEST-' . strtoupper(uniqid()),
            'status' => $randomStatus,
            'service' => $order->service ?? 'Reguler',
            'updated_at' => now()->toISOString(),
            'delivery_date' => now()->addDays(3)->toISOString(),
            'history' => [
                [
                    'status' => 'pending',
                    'description' => 'Pesanan telah dibuat',
                    'location' => 'Tasikmalaya',
                    'time' => now()->subDays(2)->toISOString(),
                    'note' => 'Menunggu konfirmasi admin'
                ],
                [
                    'status' => 'processing',
                    'description' => 'Pesanan sedang diproses',
                    'location' => 'Tasikmalaya',
                    'time' => now()->subDays(1)->toISOString(),
                    'note' => 'Sedang disiapkan untuk pengiriman'
                ],
                [
                    'status' => 'shipped',
                    'description' => 'Pesanan telah dikirim',
                    'location' => 'Tasikmalaya',
                    'time' => now()->subHours(12)->toISOString(),
                    'note' => 'Paket telah diambil oleh kurir'
                ]
            ]
        ];
    }
}