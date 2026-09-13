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
use Illuminate\Support\Facades\Hash;
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
        $pendingRequestCount = (clone $baseQuery)->where(function ($q) {
            $q->where('cancellation_status', 'pending')
              ->orWhere('return_status', 'pending');
        })->count();
        $cancelledCount = (clone $baseQuery)->where('shipping_status', 'cancelled')->count();
        
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
            'pendingRequestCount' => $pendingRequestCount,
            'cancelledCount' => $cancelledCount,
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

    public function updateProfile(Request $request)
    {
        $user = Auth::guard('customer')->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        try {
            DB::beginTransaction();

            if ($request->hasFile('avatar')) {
                $oldAvatar = $user->avatar;

                $path = $request->file('avatar')->store('avatars', 'public');
                $validated['avatar'] = $path;

                if ($oldAvatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($oldAvatar)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldAvatar);
                }
            }

            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'avatar' => $validated['avatar'] ?? $user->avatar,
            ]);

            DB::commit();

            return redirect()
                ->route('customer.account')
                ->with('success', 'Profil berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Update profile error: ' . $e->getMessage());

            return redirect()
                ->route('customer.account')
                ->with('error', 'Gagal memperbarui profil: ' . $e->getMessage());
        }
    }

    public function showChangePasswordForm()
    {
        $data = $this->getAccountData();

        return view('customer.account.change-password', $data);
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::guard('customer')->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',           // 🔥 otomatis cek password_confirmation
                'different:current_password', // 🔥 password baru tidak boleh sama dengan yang lama
            ],
        ], [
            // 🔥 Custom message (opsional, biar bahasa Indonesia)
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.different' => 'Kata sandi baru tidak boleh sama dengan kata sandi saat ini.',
        ]);

        // 🔥 CEK CURRENT PASSWORD
        if (!Hash::check($validated['current_password'], $user->password)) {
            // 🔥 Kembalikan error ke field current_password
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['current_password' => 'Kata sandi saat ini tidak sesuai.']);
        }

        DB::beginTransaction();

        try {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);

            DB::commit();

            return redirect()
                ->route('customer.password.change')
                ->with('success', 'Kata sandi berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Update password error: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Gagal memperbarui kata sandi: ' . $e->getMessage());
        }
    }

    private function autoCompleteOrders($customer): void
    {
        $delayMinutes = config('app.orders_auto_complete_minutes', 60);

        $orders = Order::where('user_id', $customer->id)
            ->where('shipping_status', 'shipped')
            ->whereNull('delivered_at')
            ->where('shipped_at', '<=', now()->subMinutes($delayMinutes))
            ->get()
            ->filter(function ($order) {
                return $order->isDeliveredOnBiteship();
            });

        foreach ($orders as $order) {
            $order->update([
                'shipping_status' => 'delivered',
                'delivered_at' => now(),
            ]);
        }
    }

    private function autoCancelUnpaidOrders($customer): void
    {
        $delayMinutes = config('app.orders_auto_cancel_minutes', 1440);

        $orders = Order::where('user_id', $customer->id)
            ->where('payment_status', 'unpaid')
            ->whereIn('shipping_status', ['pending', 'cancelled'])
            ->where('created_at', '<=', now()->subMinutes($delayMinutes))
            ->get();

        foreach ($orders as $order) {
            $order->loadMissing(['items.variant', 'items.product.variants']);

            DB::beginTransaction();
            try {
                foreach ($order->items as $item) {
                    if ($item->variant) {
                        $item->variant->addStock(
                            $item->quantity,
                            'order_cancelled',
                            "Pesanan {$order->order_number} dihapus otomatis (belum bayar {$delayMinutes} menit)"
                        );
                    } elseif ($item->product) {
                        $firstVariant = $item->product->variants->first();
                        if ($firstVariant) {
                            $firstVariant->addStock(
                                $item->quantity,
                                'order_cancelled',
                                "Pesanan {$order->order_number} dihapus otomatis (belum bayar {$delayMinutes} menit)"
                            );
                        }
                    }
                }

                $order->delete();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
            }
        }
    }

    public function orders(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        $tab = $request->query('tab', 'unpaid');

        $this->autoCancelUnpaidOrders($customer);

        $query = Order::with(['items.product.images', 'items.variant'])
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
            case 'pending':
                $query->where(function ($q) {
                    $q->where('cancellation_status', 'pending')
                      ->orWhere('return_status', 'pending');
                });
                break;
            case 'cancelled':
                $query->where('shipping_status', 'cancelled');
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

        $order->load(['customer', 'items.product.images', 'items.variant']);

        $tracking = null;
        $trackingError = null;

        $cacheKey = 'tracking:' . $order->id . ':' . $order->updated_at->timestamp;
        $cached = \Cache::get($cacheKey);

        if ($cached) {
            $tracking = $cached['tracking'] ?? null;
            $trackingError = $cached['trackingError'] ?? null;
        } elseif ($order->biteship_order_id) {
            $biteship = app(BiteshipService::class);

            $result = $biteship->getTrackingDetails(
                $order->biteship_order_id,
                $order->tracking_number,
                $order->biteship_tracking_url
            );

            if ($result['success']) {
                $tracking = $result['data'];

                // 🔥 Sync Biteship status & metadata to local order
                $order->syncTrackingStatus($tracking);

                if (empty($tracking['history'] ?? [])) {
                    $trackingError = 'Tracking belum memiliki riwayat pengiriman.';
                    $tracking = $this->getSimulatedTracking($order);
                }
            } else {
                $trackingError = $result['message'];
                $tracking = $this->getSimulatedTracking($order);
            }

            \Cache::put($cacheKey, ['tracking' => $tracking, 'trackingError' => $trackingError], now()->addMinutes(5));
        } else {
            $trackingError = 'Order belum memiliki tracking.';
            $tracking = $this->getSimulatedTracking($order);
        }

        return view('customer.orders.tracking', compact('order', 'tracking', 'trackingError'));
    }

    /**
     * 🔥 REFRESH TRACKING (AJAX) - Clear cache and fetch fresh data from Biteship
     */
    public function refreshTracking(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        try {
            // 🔥 Clear BOTH controller-level cache and service-level cache
            $cacheKey = 'tracking:' . $order->id . ':' . $order->updated_at->timestamp;
            \Cache::forget($cacheKey);

            $biteship = app(BiteshipService::class);
            $biteship->clearTrackingCache(
                $order->biteship_order_id,
                $order->tracking_number,
                $order->biteship_tracking_url
            );

            if (! $order->biteship_order_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order tidak memiliki ID tracking Biteship.',
                ], 400);
            }

            $result = $biteship->getTrackingDetails(
                $order->biteship_order_id,
                $order->tracking_number,
                $order->biteship_tracking_url
            );

            if ($result['success']) {
                $tracking = $result['data'] ?? [];

                // 🔥 Sync Biteship status & metadata to local order
                $order->syncTrackingStatus($tracking);
                $order->refresh();

                return response()->json([
                    'success' => true,
                    'data' => $tracking,
                    'message' => 'Tracking berhasil diperbarui.',
                ]);
            }

            // Fallback simulasi
            if (str_contains(strtolower($result['message'] ?? ''), 'not found') ||
                str_contains(strtolower($result['message'] ?? ''), 'tidak ditemukan')) {
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
            \Log::error('Customer refresh tracking error:', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
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

            DB::beginTransaction();

            // Pesanan belum bayar dapat dibatalkan langsung.
            if ($order->shipping_status === 'pending' && $order->payment_status === 'unpaid') {
                $order->cancelByCustomer($request->reason);

                DB::commit();

                return redirect()
                    ->route('customer.orders.show', $order)
                    ->with('success', 'Pesanan #'.$order->order_number.' berhasil dibatalkan.');
            }

            // Simpan permintaan agar muncul di notifikasi dan daftar admin.
            if (!$order->requestCancellation($request->reason)) {
                DB::rollBack();

                return redirect()
                    ->route('customer.orders.show', $order)
                    ->with('error', 'Pesanan tidak dapat dibatalkan karena sudah dalam proses pengiriman atau selesai.');
            }

            Log::info('Customer requested cancellation', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'user_id' => Auth::guard('customer')->id(),
                'reason' => $request->reason,
                'cancellation_status' => $order->cancellation_status,
                'shipping_status' => $order->shipping_status,
            ]);

            DB::commit();

            return redirect()
                ->route('customer.orders.show', $order)
                ->with('success', 'Permintaan pembatalan pesanan berhasil dikirim. Menunggu persetujuan admin.');

        } catch (\Exception $e) {
            DB::rollBack();

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
            DB::beginTransaction();

            // Restore stok + tandai cancelled; pesanan tetap tersedia di riwayat admin/customer.
            $order->cancelByCustomer('Dibatalkan oleh customer (langsung)');

            DB::commit();

            return redirect()
                ->route('customer.orders.show', $order)
                ->with('success', 'Pesanan #'.$order->order_number.' berhasil dibatalkan.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error cancelling order directly:', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('customer.orders', ['tab' => 'unpaid'])
                ->with('error', 'Terjadi kesalahan saat membatalkan pesanan.');
        }
    }

    public function requestReturn(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        $request->validate([
            'reason' => 'required|string|min:10|max:500',
        ]);

        try {
            if (!$order->can_request_return) {
                return redirect()
                    ->route('customer.orders.show', $order)
                    ->with('error', 'Permintaan retur hanya tersedia untuk pesanan yang sudah selesai (dikirim/terkirim).');
            }

            DB::beginTransaction();
            $order->requestReturn($request->reason);
            DB::commit();

            Log::info('Customer requested return', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'user_id' => Auth::guard('customer')->id(),
                'reason' => $request->reason,
                'return_status' => $order->return_status,
            ]);

            return redirect()
                ->route('customer.orders.show', $order)
                ->with('success', 'Permintaan retur berhasil dikirim. Menunggu persetujuan admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error requesting return:', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->route('customer.orders.show', $order)
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function confirmReceived(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        if ($order->shipping_status !== 'shipped') {
            return redirect()
                ->route('customer.orders')
                ->with('error', 'Pesanan ini belum dikirim.');
        }

        try {
            $order->update([
                'shipping_status' => 'delivered',
                'delivered_at' => now(),
            ]);

            return redirect()
                ->route('customer.orders.show', $order)
                ->with('success', 'Pesanan telah diterima. Terima kasih!');
        } catch (\Exception $e) {
            Log::error('Error confirming order receipt:', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('customer.orders', ['tab' => 'shipped'])
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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