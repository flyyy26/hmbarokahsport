<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Voucher extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'terms_and_conditions',
        'discount_type',
        'discount_value',
        'max_discount_amount',
        'min_transaction_amount',
        'usage_limit',
        'used_count',
        'limit_per_user',
        'start_date',
        'end_date',
        'is_active',
        'is_public',
        'is_for_all_users',
        // 🔥 KOLOM BARU
        'discount_target',
        'is_free_shipping',
        'max_shipping_discount',
        'apply_to_all_couriers',
        'applicable_couriers',
    ];

    protected $casts = [
        'discount_value'         => 'decimal:2',
        'max_discount_amount'    => 'decimal:2',
        'min_transaction_amount' => 'decimal:2',
        'max_shipping_discount'  => 'decimal:2',
        'usage_limit'            => 'integer',
        'used_count'             => 'integer',
        'limit_per_user'         => 'integer',
        'start_date'             => 'datetime',
        'end_date'               => 'datetime',
        'is_active'              => 'boolean',
        'is_public'              => 'boolean',
        'is_for_all_users'       => 'boolean',
        'is_free_shipping'       => 'boolean',
        'apply_to_all_couriers'  => 'boolean',
        'applicable_couriers'    => 'array',
    ];

    public function usages(): HasMany
    {
        return $this->hasMany(VoucherUsage::class);
    }

    /**
     * Scope untuk voucher yang aktif dan publik
     */
    public function scopePublicActive($query)
    {
        $now = now();
        return $query->where('is_active', true)
                    ->where('is_public', true)
                    ->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now)
                    ->where(function ($q) {
                        $q->whereNull('usage_limit')
                          ->orWhereRaw('used_count < usage_limit');
                    });
    }

    /**
     * Cek apakah voucher valid secara umum (waktu, status, dan kuota global)
     */
    public function isValidNow(): bool
    {
        if (!$this->is_active) return false;

        $now = now();
        if ($now->lt($this->start_date) || $now->gt($this->end_date)) {
            return false;
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Cek apakah user & nominal belanja memenuhi syarat voucher
     */
    public function checkEligibility(float $subtotal, ?int $userId = null): array
    {
        // 🔥 DEBUG
        \Log::info('checkEligibility called:', [
            'subtotal' => $subtotal,
            'user_id' => $userId,
            'voucher_code' => $this->code,
            'min_transaction_amount' => $this->min_transaction_amount,
            'is_active' => $this->is_active
        ]);

        if (!$this->isValidNow()) {
            return ['eligible' => false, 'message' => 'Voucher sudah tidak aktif atau kuota habis.'];
        }

        if ($subtotal < $this->min_transaction_amount) {
            $remainingAmount = $this->min_transaction_amount - $subtotal;

            return [
                'eligible' => false,
                'message' => 'Belanjakan lagi Rp ' . number_format($remainingAmount, 0, ',', '.') . ' untuk memakai voucher ini.'
            ];
        }

        if ($userId && $this->limit_per_user > 0) {
            $userUsage = $this->usages()->where('user_id', $userId)->count();
            if ($userUsage >= $this->limit_per_user) {
                return ['eligible' => false, 'message' => 'Anda sudah mencapai batas penggunaan voucher ini.'];
            }
        }

        return ['eligible' => true, 'message' => 'Voucher dapat digunakan.'];
    }

    /**
     * Hitung nominal potongan berdasarkan subtotal
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($this->discount_type === 'fixed') {
            return min($this->discount_value, $subtotal);
        }

        // Tipe percentage
        $potongan = $subtotal * ($this->discount_value / 100);

        if ($this->max_discount_amount && $potongan > $this->max_discount_amount) {
            $potongan = $this->max_discount_amount;
        }

        return min(round($potongan, 2), $subtotal);
    }

    /**
     * 🔥 HITUNG DISKON ONGKIR
     */
    public function calculateShippingDiscount(float $shippingCost): float
    {
        // 🔥 DEBUG
        \Log::info('calculateShippingDiscount called:', [
            'shipping_cost' => $shippingCost,
            'is_free_shipping' => $this->is_free_shipping,
            'discount_target' => $this->discount_target,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'max_shipping_discount' => $this->max_shipping_discount
        ]);

        // Jika shipping cost 0, tidak ada diskon
        if ($shippingCost <= 0) {
            return 0;
        }

        // Jika gratis ongkir
        if ($this->is_free_shipping) {
            return $shippingCost;
        }

        // Jika bukan target shipping, return 0
        if ($this->discount_target !== 'shipping') {
            return 0;
        }

        // Jika diskon ongkir
        if ($this->discount_type === 'fixed') {
            // 🔥 NILAI TETAP
            $discount = min((float) $this->discount_value, $shippingCost);
            return max(0, round($discount, 2));
        }

        if ($this->discount_type === 'percentage') {
            // 🔥 PERSENTASE
            $percentage = (float) $this->discount_value;
            $discount = $shippingCost * ($percentage / 100);
            
            // 🔥 CEK MAX SHIPPING DISCOUNT
            if ($this->max_shipping_discount && (float) $this->max_shipping_discount > 0) {
                $maxDiscount = (float) $this->max_shipping_discount;
                if ($discount > $maxDiscount) {
                    $discount = $maxDiscount;
                }
            }
            
            return max(0, round($discount, 2));
        }

        return 0;
    }

    /**
     * 🔥 SCOPE: VOUCHER YANG BELUM PERNAH DIGUNAKAN USER
     */
    public function scopeNotUsedByUser($query, ?int $userId = null)
    {
        if ($userId) {
            return $query->whereNotExists(function($sub) use ($userId) {
                $sub->select('id')
                    ->from('voucher_usages')
                    ->whereColumn('voucher_usages.voucher_id', 'vouchers.id')
                    ->where('voucher_usages.user_id', $userId);
            });
        }
        return $query;
    }

    /**
     * 🔥 SCOPE: VOUCHER YANG BELUM PERNAH DIGUNAKAN USER DAN MASIH BERLAKU
     */
    public function scopeAvailableForCheckout($query, ?int $userId = null)
    {
        $now = now();
        
        return $query->where('is_active', true)
            ->where('is_public', true)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where(function($q) {
                $q->whereNull('usage_limit')
                ->orWhereRaw('used_count < usage_limit');
            })
            ->when($userId, function($q) use ($userId) {
                return $q->whereNotExists(function($sub) use ($userId) {
                    $sub->select('id')
                        ->from('voucher_usages')
                        ->whereColumn('voucher_usages.voucher_id', 'vouchers.id')
                        ->where('voucher_usages.user_id', $userId);
                });
            });
    }


    /**
     * 🔥 CEK APAKAH VOUCHER BERLAKU UNTUK KURIR TERTENTU
     */
    public function isCourierApplicable(string $courierCode): bool
    {
        // Jika untuk semua kurir
        if ($this->apply_to_all_couriers) {
            return true;
        }

        // Jika tidak ada kurir yang dipilih
        if (empty($this->applicable_couriers)) {
            return true;
        }

        // Cek apakah kurir ada di daftar
        $couriers = is_array($this->applicable_couriers) 
            ? $this->applicable_couriers 
            : json_decode($this->applicable_couriers, true) ?? [];
        
        return in_array(strtoupper($courierCode), array_map('strtoupper', $couriers));
    }

    public function isAvailableForUser(?int $userId): bool
    {
        // Jika voucher untuk semua user, cek hanya limit per user
        if ($this->is_for_all_users) {
            if ($userId) {
                $usageCount = $this->usages()->where('user_id', $userId)->count();
                $limitPerUser = $this->limit_per_user ?? 0;
                
                if ($limitPerUser > 0 && $usageCount >= $limitPerUser) {
                    return false;
                }
            }
            return true;
        }

        if ($userId) {
            $usageCount = $this->usages()->where('user_id', $userId)->count();
            $limitPerUser = $this->limit_per_user ?? 0;
            
            if ($limitPerUser > 0 && $usageCount >= $limitPerUser) {
                return false;
            }
        }

        return true;
    }

    public function scopeAvailableForUser($query, ?int $userId = null)
    {
        $now = now();
        
        return $query->where('is_active', true)
            ->where('is_public', true)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where(function($q) {
                $q->where('is_for_all_users', true)
                  ->orWhere(function($sub) {
                      $sub->whereNull('usage_limit')
                          ->orWhereRaw('used_count < usage_limit');
                  });
            })
            ->where(function($q) use ($userId) {
                if ($userId) {
                    $q->where(function($sub) use ($userId) {
                        $sub->where('is_for_all_users', true)
                            ->where(function($inner) use ($userId) {
                                $inner->where('limit_per_user', 0)
                                      ->orWhere('limit_per_user', null)
                                      ->orWhereRaw('(SELECT COUNT(*) FROM voucher_usages WHERE voucher_usages.voucher_id = vouchers.id AND voucher_usages.user_id = ?) < limit_per_user', [$userId]);
                            });
                    })->orWhere(function($sub) use ($userId) {
                        $sub->where('is_for_all_users', false)
                            ->where(function($inner) use ($userId) {
                                $inner->whereNull('limit_per_user')
                                      ->orWhere('limit_per_user', 0)
                                      ->orWhereRaw('(SELECT COUNT(*) FROM voucher_usages WHERE voucher_usages.voucher_id = vouchers.id AND voucher_usages.user_id = ?) < limit_per_user', [$userId]);
                            })
                            ->whereNotExists(function($exists) use ($userId) {
                                $exists->select('id')
                                    ->from('voucher_usages')
                                    ->whereColumn('voucher_usages.voucher_id', 'vouchers.id')
                                    ->where('voucher_usages.user_id', $userId);
                            });
                    });
                } else {
                    $q->where('is_for_all_users', true);
                }
            });
    }

    public function scopeForAllUsers($query)
    {
        return $query->where('is_for_all_users', true);
    }

    public function scopeForSpecificUser($query)
    {
        return $query->where('is_for_all_users', false);
    }

    /**
     * 🔥 SCOPE: VOUCHER DISKON PRODUK
     */
    public function scopeProductDiscount($query)
    {
        return $query->where('discount_target', 'product')
                     ->where('is_free_shipping', false);
    }

    /**
     * 🔥 SCOPE: VOUCHER DISKON ONGKIR
     */
    public function scopeShippingDiscount($query)
    {
        return $query->where('discount_target', 'shipping')
                     ->where('is_free_shipping', false);
    }

    /**
     * 🔥 SCOPE: VOUCHER GRATIS ONGKIR
     */
    public function scopeFreeShipping($query)
    {
        return $query->where('is_free_shipping', true);
    }
}
