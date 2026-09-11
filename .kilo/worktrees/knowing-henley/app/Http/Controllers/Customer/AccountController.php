<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\Wishlist;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    protected function getAccountData()
    {
        $customer = Auth::guard('customer')->user();
        
        $orderCount = Order::where('customer_id', $customer->id)->count();
        $wishlistCount = Wishlist::where('user_id', $customer->id)->count();
        
        // 🔥 HITUNG VOUCHER TERSEDIA
        $availableVouchers = Voucher::where('is_active', true)
            ->where('is_public', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where(function($q) {
                $q->whereNull('usage_limit')
                  ->orWhereRaw('used_count < usage_limit');
            })
            ->where(function($q) use ($customer) {
                // 🔥 CEK APAKAH USER SUDAH PERNAH PAKAI
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

    public function orders()
    {
        $customer = Auth::guard('customer')->user();
        
        $orders = Order::with(['items.product', 'items.variant'])
            ->where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = $this->getAccountData();

        return view('customer.account.orders', array_merge(['orders' => $orders], $data));
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

        $validated['customer_id'] = $customer->id;
        $validated['user_id'] = $customer->id;

        CustomerAddress::create($validated);

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

        $validated['customer_id'] = $customer->id;
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
        if ($order->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        $order->load(['items.product', 'items.variant']);

        return view('customer.account.order-detail', compact('order'));
    }
}