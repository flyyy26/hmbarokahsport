<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerVoucherController extends Controller
{
    /**
     * 🔥 DAFTAR VOUCHER YANG TERSEDIA UNTUK CUSTOMER
     */
    public function index(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        $customerId = $customer ? $customer->id : null;

        $query = Voucher::availableForUser($customerId);

        // 🔥 AMBIL VOUCHER PUBLIK YANG MASIH BERLAKU
        $query = Voucher::where('is_active', true)
            ->where('is_public', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where(function($q) {
                // Kuota global belum habis
                $q->whereNull('usage_limit')
                  ->orWhereRaw('used_count < usage_limit');
            });

        // 🔥 FILTER SEARCH
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%');
            });
        }

        // 🔥 SORT
        switch ($request->sort) {
            case 'discount_desc':
                $query->orderBy('discount_value', 'desc');
                break;
            case 'discount_asc':
                $query->orderBy('discount_value', 'asc');
                break;
            case 'expired_soon':
                $query->orderBy('end_date', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $vouchers = $query->paginate(12);

        // 🔥 CEK STATUS SETIAP VOUCHER UNTUK CUSTOMER
        foreach ($vouchers as $voucher) {
            // Cek apakah customer sudah pernah pakai voucher ini
            $isUsed = false;
            $canUse = true;
            $usageCount = 0;

            if ($customerId) {
                $usageCount = VoucherUsage::where('voucher_id', $voucher->id)
                    ->where('user_id', $customerId)
                    ->count();

                $isUsed = $usageCount > 0;
                
                // Cek limit per user (kecuali all users)
                if (!$voucher->is_for_all_users && $voucher->limit_per_user > 0 && $usageCount >= $voucher->limit_per_user) {
                    $canUse = false;
                }
            }

            // Cek apakah subtotal memenuhi (default 0)
            $eligibility = $voucher->checkEligibility(0, $customerId);
            
            $voucher->is_used_by_user = $isUsed;
            $voucher->can_use = $canUse && $eligibility['eligible'];
            $voucher->eligibility_message = $eligibility['message'];
            $voucher->usage_count_by_user = $usageCount;
            $voucher->remaining_quota = $voucher->usage_limit ? ($voucher->usage_limit - $voucher->used_count) : null;
            $voucher->terms_and_conditions = $voucher->terms_and_conditions;
            $voucher->discount_label = $this->getDiscountLabel($voucher);
            $voucher->user_label = $voucher->is_for_all_users ? '🎉 Untuk Semua User' : '🎯 Khusus User';
            
            // 🔥 Format diskon
            if ($voucher->discount_type === 'percentage') {
                $voucher->discount_label = $voucher->discount_value . '%';
                if ($voucher->max_discount_amount) {
                    $voucher->discount_label .= ' (Max Rp ' . number_format($voucher->max_discount_amount, 0, ',', '.') . ')';
                }
            } else {
                $voucher->discount_label = 'Rp ' . number_format($voucher->discount_value, 0, ',', '.');
            }
        }

        // 🔥 HITUNG TOTAL VOUCHER YANG TERSEDIA UNTUK CUSTOMER
        $availableCount = $vouchers->filter(function($v) {
            return $v->can_use && !$v->is_used_by_user;
        })->count();

        return view('customer.vouchers.index', compact('vouchers', 'availableCount'));
    }

    private function getDiscountLabel($voucher)
    {
        if ($voucher->discount_type === 'percentage') {
            $label = $voucher->discount_value . '%';
            if ($voucher->max_discount_amount) {
                $label .= ' (Max Rp ' . number_format($voucher->max_discount_amount, 0, ',', '.') . ')';
            }
            return $label;
        }
        return 'Rp ' . number_format($voucher->discount_value, 0, ',', '.');
    }

    /**
     * 🔥 CLAIM / PAKAI VOUCHER (Redirect ke Checkout)
     */
    public function use(Voucher $voucher)
    {
        $customer = Auth::guard('customer')->user();
        
        if (!$customer) {
            return redirect()->route('customer.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // 🔥 VALIDASI VOUCHER
        if (!$voucher->is_active || !$voucher->is_public) {
            return redirect()->route('customer.vouchers.index')
                ->with('error', 'Voucher tidak tersedia.');
        }

        if (now()->lt($voucher->start_date) || now()->gt($voucher->end_date)) {
            return redirect()->route('customer.vouchers.index')
                ->with('error', 'Voucher sudah tidak berlaku.');
        }

        if ($voucher->usage_limit !== null && $voucher->used_count >= $voucher->usage_limit) {
            return redirect()->route('customer.vouchers.index')
                ->with('error', 'Kuota voucher sudah habis.');
        }

        $usageCount = VoucherUsage::where('voucher_id', $voucher->id)
            ->where('user_id', $customer->id)
            ->count();

        if ($voucher->limit_per_user > 0 && $usageCount >= $voucher->limit_per_user) {
            return redirect()->route('customer.vouchers.index')
                ->with('error', 'Anda sudah mencapai batas penggunaan voucher ini.');
        }

        // 🔥 SIMPAN VOUCHER KE SESSION UNTUK CHECKOUT
        session()->put('applied_voucher', [
            'id' => $voucher->id,
            'code' => $voucher->code,
            'name' => $voucher->name,
            'discount_type' => $voucher->discount_type,
            'discount_value' => $voucher->discount_value,
            'max_discount_amount' => $voucher->max_discount_amount,
            'min_transaction_amount' => $voucher->min_transaction_amount,
        ]);

        return redirect()->route('customer.checkout.index')
            ->with('success', 'Voucher "' . $voucher->name . '" berhasil digunakan!');
    }

    /**
     * 🔥 HAPUS VOUCHER DARI SESSION
     */
    public function remove()
    {
        session()->forget('applied_voucher');
        
        return redirect()->route('customer.checkout.index')
            ->with('success', 'Voucher berhasil dihapus.');
    }

    public function show(Voucher $voucher)
    {
        $customer = Auth::guard('customer')->user();
        $customerId = $customer ? $customer->id : null;

        // 🔥 VALIDASI: VOUCHER HARUS PUBLIK DAN AKTIF
        if (!$voucher->is_active || !$voucher->is_public) {
            return redirect()->route('customer.vouchers.index')
                ->with('error', 'Voucher tidak tersedia.');
        }

        // 🔥 CEK APAKAH VOUCHER MASIH BERLAKU
        $isValid = $voucher->isValidNow();
        $isExpired = now()->gt($voucher->end_date);
        $isUpcoming = now()->lt($voucher->start_date);
        $isQuotaFull = $voucher->usage_limit !== null && $voucher->used_count >= $voucher->usage_limit;

        // 🔥 CEK APAKAH USER SUDAH PAKAI VOUCHER INI
        $isUsedByUser = false;
        $usageCountByUser = 0;
        if ($customerId) {
            $usageCountByUser = VoucherUsage::where('voucher_id', $voucher->id)
                ->where('user_id', $customerId)
                ->count();
            $isUsedByUser = $usageCountByUser > 0;
        }

        // 🔥 CEK APAKAH USER BISA PAKAI VOUCHER
        $canUse = $isValid && !$isUsedByUser;
        if ($voucher->limit_per_user > 0 && $usageCountByUser >= $voucher->limit_per_user) {
            $canUse = false;
        }

        // 🔥 CEK ELIGIBILITY (DENGAN SUBTOTAL 0)
        $eligibility = $voucher->checkEligibility(0, $customerId);
        $eligibilityMessage = $eligibility['message'];

        // 🔥 HITUNG REMAINING QUOTA
        $remainingQuota = $voucher->usage_limit ? ($voucher->usage_limit - $voucher->used_count) : null;

        // 🔥 FORMAT DISKON
        if ($voucher->discount_type === 'percentage') {
            $discountLabel = $voucher->discount_value . '%';
            if ($voucher->max_discount_amount) {
                $discountLabel .= ' (Max Rp ' . number_format($voucher->max_discount_amount, 0, ',', '.') . ')';
            }
        } else {
            $discountLabel = 'Rp ' . number_format($voucher->discount_value, 0, ',', '.');
        }

        // 🔥 AMBIL DATA ACCOUNT (UNTUK SIDEBAR)
        $data = $this->getAccountData();

        return view('customer.vouchers.show', array_merge([
            'voucher' => $voucher,
            'isValid' => $isValid,
            'isExpired' => $isExpired,
            'isUpcoming' => $isUpcoming,
            'isQuotaFull' => $isQuotaFull,
            'isUsedByUser' => $isUsedByUser,
            'canUse' => $canUse,
            'usageCountByUser' => $usageCountByUser,
            'remainingQuota' => $remainingQuota,
            'eligibilityMessage' => $eligibilityMessage,
            'discountLabel' => $discountLabel,
        ], $data));
    }

    protected function getAccountData()
    {
        $customer = Auth::guard('customer')->user();
        
        if (!$customer) {
            return [
                'orderCount' => 0,
                'wishlistCount' => 0,
                'availableVouchers' => 0,
            ];
        }

        $orderCount = \App\Models\Order::where('customer_id', $customer->id)->count();
        $wishlistCount = \App\Models\Wishlist::where('user_id', $customer->id)->count();
        
        $availableVouchers = Voucher::where('is_active', true)
            ->where('is_public', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where(function($q) {
                $q->whereNull('usage_limit')
                  ->orWhereRaw('used_count < usage_limit');
            })
            ->whereNotExists(function($sub) use ($customer) {
                $sub->select('id')
                    ->from('voucher_usages')
                    ->whereColumn('voucher_usages.voucher_id', 'vouchers.id')
                    ->where('voucher_usages.user_id', $customer->id);
            })
            ->count();

        return [
            'orderCount' => $orderCount,
            'wishlistCount' => $wishlistCount,
            'availableVouchers' => $availableVouchers,
        ];
    }
}