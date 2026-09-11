<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $query = Voucher::withCount('usages')->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            if ($request->type === 'product') {
                $query->where('discount_target', 'product')
                      ->where('is_free_shipping', false);
            } elseif ($request->type === 'shipping') {
                $query->where('discount_target', 'shipping')
                      ->where('is_free_shipping', false);
            } elseif ($request->type === 'free_shipping') {
                $query->where('is_free_shipping', true);
            }
        }

        if ($request->filled('status')) {
            $now = now();
            if ($request->status === 'active') {
                $query->where('is_active', true)
                      ->where('start_date', '<=', $now)
                      ->where('end_date', '>=', $now);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'expired') {
                $query->where('end_date', '<', $now);
            } elseif ($request->status === 'upcoming') {
                $query->where('start_date', '>', $now);
            }
        }

        $vouchers = $query->paginate(10)->withQueryString();

        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        return view('admin.vouchers.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateVoucher($request);
        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_public'] = $request->boolean('is_public');
        $validated['is_for_all_users'] = $request->boolean('is_for_all_users');
        $validated['is_free_shipping'] = $request->boolean('is_free_shipping');
        $validated['apply_to_all_couriers'] = $request->boolean('apply_to_all_couriers');

        // 🔥 JIKA GRATIS ONGKIR
        if ($validated['is_free_shipping']) {
            $validated['discount_target'] = 'shipping';
            $validated['discount_value'] = 0;
            $validated['discount_type'] = 'fixed';
            $validated['max_discount_amount'] = null;
            $validated['max_shipping_discount'] = null;
        }

        // 🔥 JIKA UNTUK SEMUA USER
        if ($validated['is_for_all_users']) {
            $validated['usage_limit'] = null;
        }

        // 🔥 JIKA TIDAK UNTUK SEMUA KURIR
        if (!$validated['apply_to_all_couriers']) {
            $validated['applicable_couriers'] = $request->input('applicable_couriers', []);
        } else {
            $validated['applicable_couriers'] = null;
        }

        // 🔥 RESET MAX_DISCOUNT_AMOUNT jika tipe fixed (untuk produk)
        if ($validated['discount_target'] === 'product' && $validated['discount_type'] === 'fixed') {
            $validated['max_discount_amount'] = null;
        }

        // 🔥 RESET MAX_SHIPPING_DISCOUNT jika tipe fixed (untuk ongkir)
        if ($validated['discount_target'] === 'shipping' && $validated['discount_type'] === 'fixed') {
            $validated['max_shipping_discount'] = null;
        }

        // 🔥 HAPUS MAX_SHIPPING_DISCOUNT jika target produk
        if ($validated['discount_target'] === 'product') {
            $validated['max_shipping_discount'] = null;
        }

        Voucher::create($validated);

        return redirect()
            ->route('admin.vouchers.index')
            ->with('success', 'Voucher berhasil dibuat.');
    }

    public function edit(Voucher $voucher)
    {
        return view('admin.vouchers.edit', compact('voucher'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        $validated = $this->validateVoucher($request, $voucher->id);
        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_public'] = $request->boolean('is_public');
        $validated['is_for_all_users'] = $request->boolean('is_for_all_users');
        $validated['is_free_shipping'] = $request->boolean('is_free_shipping');
        $validated['apply_to_all_couriers'] = $request->boolean('apply_to_all_couriers');

        // 🔥 JIKA GRATIS ONGKIR, SET DISCOUNT_TARGET = SHIPPING
        if ($validated['is_free_shipping']) {
            $validated['discount_target'] = 'shipping';
            $validated['discount_value'] = 0;
            $validated['discount_type'] = 'fixed';
            $validated['max_discount_amount'] = null;
            $validated['max_shipping_discount'] = null;
        }

        // 🔥 JIKA UNTUK SEMUA USER, TOTAL KUOTA MENJADI TIDAK TERBATAS
        if ($validated['is_for_all_users']) {
            $validated['usage_limit'] = null;
        }

        // 🔥 JIKA TIDAK UNTUK SEMUA KURIR, SIMPAN KURIR YANG DIPILIH
        if (!$validated['apply_to_all_couriers']) {
            $validated['applicable_couriers'] = $request->input('applicable_couriers', []);
        } else {
            $validated['applicable_couriers'] = null;
        }

        // 🔥 RESET MAX_DISCOUNT_AMOUNT jika tipe fixed (untuk produk)
        if (($validated['discount_target'] === 'product' || $validated['discount_target'] === 'shipping') && 
            $validated['discount_type'] === 'fixed') {
            $validated['max_discount_amount'] = null;
            $validated['max_shipping_discount'] = null;
        }

        // 🔥 RESET MAX_SHIPPING_DISCOUNT jika tipe fixed (untuk ongkir)
        if ($validated['discount_target'] === 'shipping' && $validated['discount_type'] === 'fixed') {
            $validated['max_shipping_discount'] = null;
        }

        $voucher->update($validated);

        return redirect()
            ->route('admin.vouchers.index')
            ->with('success', 'Voucher berhasil diperbarui.');
    }

    public function destroy(Voucher $voucher)
    {
        if ($voucher->usages()->exists()) {
            return redirect()
                ->route('admin.vouchers.index')
                ->with('error', 'Voucher tidak dapat dihapus karena sudah pernah digunakan pada transaksi.');
        }

        $voucher->delete();

        return redirect()
            ->route('admin.vouchers.index')
            ->with('success', 'Voucher berhasil dihapus.');
    }

    public function toggleStatus(Voucher $voucher)
    {
        $voucher->update(['is_active' => !$voucher->is_active]);

        return response()->json([
            'success'   => true,
            'is_active' => $voucher->is_active,
            'message'   => 'Status voucher berhasil diubah.'
        ]);
    }

    private function validateVoucher(Request $request, ?int $ignoreId = null): array
    {
        $rules = [
            'name'                   => ['required', 'string', 'max:255'],
            'code'                   => ['required', 'string', 'max:50', Rule::unique('vouchers', 'code')->ignore($ignoreId)],
            'description'            => ['nullable', 'string', 'max:500'],
            'terms_and_conditions'   => ['nullable', 'string'],
            'discount_target'        => ['required', 'in:product,shipping'],
            'is_free_shipping'       => ['nullable', 'boolean'],
            'min_transaction_amount' => ['required', 'numeric', 'min:0'],
            'usage_limit'            => ['nullable', 'integer', 'min:1'],
            'limit_per_user'         => ['required', 'integer', 'min:1'],
            'start_date'             => ['required', 'date'],
            'end_date'               => ['required', 'date', 'after_or_equal:start_date'],
            'is_active'              => ['nullable', 'boolean'],
            'is_public'              => ['nullable', 'boolean'],
            'is_for_all_users'       => ['nullable', 'boolean'],
            'apply_to_all_couriers'  => ['nullable', 'boolean'],
            'applicable_couriers'    => ['nullable', 'array'],
            'applicable_couriers.*'  => ['string'],
        ];

        $isFreeShipping = $request->boolean('is_free_shipping');
        $discountTarget = $request->discount_target;
        $discountType = $request->discount_type;

        // ============================================
        // 🔥 VALIDASI UNTUK DISKON PRODUK
        // ============================================
        if ($discountTarget === 'product' && !$isFreeShipping) {
            $rules['discount_type'] = ['required', 'in:fixed,percentage'];
            $rules['discount_value'] = ['required', 'numeric', 'min:0'];
            
            // 🔥 max_discount_amount WAJIB jika percentage
            $rules['max_discount_amount'] = [
                'nullable',
                Rule::requiredIf(function() use ($request) {
                    return $request->discount_type === 'percentage';
                }),
                'numeric',
                'min:0'
            ];
        }

        // ============================================
        // 🔥 VALIDASI UNTUK DISKON ONGKIR (BUKAN GRATIS)
        // ============================================
        if ($discountTarget === 'shipping' && !$isFreeShipping) {
            $rules['discount_type'] = ['required', 'in:fixed,percentage'];
            $rules['discount_value'] = ['required', 'numeric', 'min:0'];
            
            // 🔥 max_shipping_discount OPSIONAL
            $rules['max_shipping_discount'] = [
                'nullable',
                'numeric',
                'min:0'
            ];
        }

        // ============================================
        // 🔥 VALIDASI UNTUK GRATIS ONGKIR
        // ============================================
        if ($isFreeShipping) {
            $rules['discount_type'] = ['nullable'];
            $rules['discount_value'] = ['nullable'];
            $rules['max_discount_amount'] = ['nullable'];
            $rules['max_shipping_discount'] = ['nullable'];
        }

        return $request->validate($rules);
    }
}