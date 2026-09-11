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

    protected $appends = [
        'discount_label',
        'status_label',
        'status_color',
    ];

    // ============================================
    // RELATIONS
    // ============================================

    public function usages(): HasMany
    {
        return $this->hasMany(VoucherUsage::class);
    }

    // ============================================
    // SCOPES - YANG BENAR-BENAR BISA DIGUNAKAN USER
    // ============================================

    /**
     * 🔥 SCOPE UTAMA: VOUCHER YANG BISA DIGUNAKAN USER DI CHECKOUT
     * Ini adalah scope utama yang harus dipakai untuk menampilkan voucher
     */
    public function scopeAvailableForUser($query, ?int $userId = null, ?float $subtotal = null, ?float $shippingCost = null, ?string $courier = null)
    {
        $now = now();
        
        $query->where('is_active', true)
              ->where('is_public', true)
              ->where('start_date', '<=', $now)
              ->where('end_date', '>=', $now)
              // Kuota global belum habis
              ->where(function($q) {
                  $q->whereNull('usage_limit')
                    ->orWhereRaw('used_count < usage_limit');
              });

        // 🔥 FILTER UNTUK USER (jika login)
        if ($userId) {
            $query->where(function($q) use ($userId) {
                // Voucher untuk semua user (tidak perlu cek is_for_all_users)
                // TAPI tetap cek limit_per_user
                $q->where(function($sub) use ($userId) {
                    $sub->where('is_for_all_users', true)
                        ->where(function($inner) use ($userId) {
                            $inner->whereNull('limit_per_user')
                                  ->orWhere('limit_per_user', 0)
                                  ->orWhereRaw('(SELECT COUNT(*) FROM voucher_usages WHERE voucher_usages.voucher_id = vouchers.id AND voucher_usages.user_id = ?) < limit_per_user', [$userId]);
                        });
                })
                // ATAU voucher khusus user yang belum pernah dipakai
                ->orWhere(function($sub) use ($userId) {
                    $sub->where('is_for_all_users', false)
                        ->whereNull('limit_per_user') // tidak perlu limit
                        ->whereNotExists(function($exists) use ($userId) {
                            $exists->select('id')
                                ->from('voucher_usages')
                                ->whereColumn('voucher_usages.voucher_id', 'vouchers.id')
                                ->where('voucher_usages.user_id', $userId);
                        });
                });
            });
        } else {
            // User tidak login: hanya tampilkan voucher untuk semua user
            $query->where('is_for_all_users', true);
        }

        // 🔥 FILTER MINIMAL TRANSAKSI (jika ada subtotal)
        if ($subtotal !== null) {
            $query->where(function($q) use ($subtotal) {
                $q->whereNull('min_transaction_amount')
                  ->orWhere('min_transaction_amount', '<=', $subtotal);
            });
        }

        // 🔥 FILTER UNTUK VOUCHER ONGKIR (hanya jika ada shipping cost dan courier)
        if ($shippingCost !== null && $courier !== null) {
            $query->where(function($q) use ($shippingCost, $courier) {
                // Voucher produk: selalu tampilkan
                $q->where('discount_target', 'product')
                  ->where('is_free_shipping', false)
                  // ATAU voucher ongkir yang berlaku untuk kurir ini
                  ->orWhere(function($sub) use ($shippingCost, $courier) {
                      $sub->where(function($inner) {
                          $inner->where('discount_target', 'shipping')
                                ->orWhere('is_free_shipping', true);
                      })
                      ->where('shipping_cost', '<=', $shippingCost) // 🔥 HANYA TAMPILKAN JIKA ONGKIR >= NILAI YANG DITENTUKAN
                      ->where(function($inner) use ($courier) {
                          $inner->where('apply_to_all_couriers', true)
                                ->orWhereJsonContains('applicable_couriers', strtoupper($courier));
                      });
                  });
            });
        }

        return $query;
    }

    /**
     * 🔥 SCOPE KHUSUS UNTUK VOUCHER YANG BISA DIGUNAKAN DI CHECKOUT
     * Lebih simpel, hanya filter dasar
     */
    public function scopeUsableForCheckout($query, ?int $userId = null)
    {
        $now = now();
        
        $query->where('is_active', true)
              ->where('is_public', true)
              ->where('start_date', '<=', $now)
              ->where('end_date', '>=', $now)
              ->where(function($q) {
                  $q->whereNull('usage_limit')
                    ->orWhereRaw('used_count < usage_limit');
              });

        // Filter user
        if ($userId) {
            $query->where(function($q) use ($userId) {
                // Voucher untuk semua user
                $q->where('is_for_all_users', true)
                  // ATAU voucher khusus user yang belum pernah dipakai
                  ->orWhere(function($sub) use ($userId) {
                      $sub->where('is_for_all_users', false)
                          ->whereNotExists(function($exists) use ($userId) {
                              $exists->select('id')
                                  ->from('voucher_usages')
                                  ->whereColumn('voucher_usages.voucher_id', 'vouchers.id')
                                  ->where('voucher_usages.user_id', $userId);
                          });
                  });
            });
        } else {
            $query->where('is_for_all_users', true);
        }

        return $query;
    }

    // ============================================
    // SCOPES LAINNYA (tetap dipertahankan)
    // ============================================

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

    public function scopeProductDiscount($query)
    {
        return $query->where('discount_target', 'product')
                     ->where('is_free_shipping', false);
    }

    public function scopeShippingDiscount($query)
    {
        return $query->where('discount_target', 'shipping')
                     ->where('is_free_shipping', false);
    }

    public function scopeFreeShipping($query)
    {
        return $query->where('is_free_shipping', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeValid($query)
    {
        $now = now();
        return $query->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
    }

    public function scopeForAllUsers($query)
    {
        return $query->where('is_for_all_users', true);
    }

    public function scopeForSpecificUser($query)
    {
        return $query->where('is_for_all_users', false);
    }

    // ============================================
    // VALIDATION METHODS
    // ============================================

    public function isValidNow(): bool
    {
        if (!$this->is_active) return false;
        if (!$this->is_public) return false;

        $now = now();
        if ($now->lt($this->start_date) || $now->gt($this->end_date)) {
            return false;
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function checkEligibility(float $subtotal, ?int $userId = null, float $shippingCost = 0, ?string $selectedCourier = null): array
    {
        \Log::info('=== VOUCHER ELIGIBILITY CHECK ===', [
            'voucher_code' => $this->code,
            'voucher_name' => $this->name,
            'is_active' => $this->is_active,
            'is_public' => $this->is_public,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'now' => now(),
            'subtotal' => $subtotal,
            'user_id' => $userId,
            'shipping_cost' => $shippingCost,
            'selected_courier' => $selectedCourier,
            'discount_target' => $this->discount_target,
            'is_free_shipping' => $this->is_free_shipping,
            'min_transaction_amount' => $this->min_transaction_amount,
            'usage_limit' => $this->usage_limit,
            'used_count' => $this->used_count,
            'limit_per_user' => $this->limit_per_user,
            'apply_to_all_couriers' => $this->apply_to_all_couriers,
            'applicable_couriers' => $this->applicable_couriers,
        ]);

        // 1. CEK STATUS AKTIF & PUBLIK
        if (!$this->is_active) {
            return ['eligible' => false, 'message' => 'Voucher sedang tidak aktif.'];
        }

        if (!$this->is_public) {
            return ['eligible' => false, 'message' => 'Voucher tidak tersedia untuk umum.'];
        }

        // 2. CEK TANGGAL BERLAKU
        $now = now();
        if ($now->lt($this->start_date)) {
            return ['eligible' => false, 'message' => 'Voucher belum mulai berlaku.'];
        }
        
        if ($now->gt($this->end_date)) {
            return ['eligible' => false, 'message' => 'Voucher sudah kadaluarsa.'];
        }

        // 3. CEK KUOTA GLOBAL
        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return ['eligible' => false, 'message' => 'Kuota voucher sudah habis.'];
        }

        // 4. CEK USER SPECIFIC
        if (!$this->is_for_all_users && !$userId) {
            return ['eligible' => false, 'message' => 'Voucher ini khusus untuk member.'];
        }

        if (!$this->is_for_all_users && $userId) {
            $hasUsed = $this->usages()->where('user_id', $userId)->exists();
            if ($hasUsed) {
                return ['eligible' => false, 'message' => 'Anda sudah pernah menggunakan voucher ini.'];
            }
        }

        // 5. CEK LIMIT PER USER
        if ($userId && $this->limit_per_user > 0) {
            $userUsage = $this->usages()->where('user_id', $userId)->count();
            if ($userUsage >= $this->limit_per_user) {
                return ['eligible' => false, 'message' => 'Anda sudah mencapai batas penggunaan voucher ini.'];
            }
        }

        // 6. CEK MINIMAL TRANSAKSI
        if ($subtotal < $this->min_transaction_amount) {
            $remainingAmount = $this->min_transaction_amount - $subtotal;
            return [
                'eligible' => false,
                'message' => 'Belanjakan lagi Rp ' . number_format($remainingAmount, 0, ',', '.') . ' untuk memakai voucher ini.'
            ];
        }

        // 7. CEK VOUCHER ONGKIR
        if ($this->discount_target === 'shipping' || $this->is_free_shipping) {
            if ($shippingCost <= 0) {
                return [
                    'eligible' => false,
                    'message' => 'Pilih kurir dan layanan pengiriman terlebih dahulu untuk menggunakan voucher ongkir ini.'
                ];
            }

            if (!$this->isCourierApplicable($selectedCourier)) {
                return [
                    'eligible' => false,
                    'message' => 'Voucher ini tidak berlaku untuk kurir ' . strtoupper($selectedCourier) . '.'
                ];
            }
        }

        \Log::info('✅ Voucher ELIGIBLE!', [
            'voucher_code' => $this->code,
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost
        ]);

        return ['eligible' => true, 'message' => 'Voucher dapat digunakan.'];
    }

    public function isCourierApplicable(string $courierCode): bool
    {
        if ($this->apply_to_all_couriers) {
            return true;
        }

        if (empty($this->applicable_couriers)) {
            return true;
        }

        $couriers = is_array($this->applicable_couriers) 
            ? $this->applicable_couriers 
            : json_decode($this->applicable_couriers, true) ?? [];
        
        return in_array(strtoupper($courierCode), array_map('strtoupper', $couriers));
    }

    public function isAvailableForUser(?int $userId): bool
    {
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

    // ============================================
    // DISCOUNT CALCULATION
    // ============================================

    public function calculateDiscount(float $subtotal): float
    {
        if ($subtotal <= 0) {
            return 0;
        }

        if ($this->discount_type === 'fixed') {
            $discount = min((float) $this->discount_value, $subtotal);
            return max(0, round($discount, 2));
        }

        if ($this->discount_type === 'percentage') {
            $percentage = (float) $this->discount_value;
            $discount = $subtotal * ($percentage / 100);
            
            if ($this->max_discount_amount && (float) $this->max_discount_amount > 0) {
                $maxDiscount = (float) $this->max_discount_amount;
                if ($discount > $maxDiscount) {
                    $discount = $maxDiscount;
                }
            }
            
            if ($discount > $subtotal) {
                $discount = $subtotal;
            }
            
            return max(0, round($discount, 2));
        }

        return 0;
    }

    public function calculateShippingDiscount(float $shippingCost): float
    {
        \Log::info('calculateShippingDiscount called:', [
            'shipping_cost' => $shippingCost,
            'is_free_shipping' => $this->is_free_shipping,
            'discount_target' => $this->discount_target,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'max_shipping_discount' => $this->max_shipping_discount
        ]);

        if ($shippingCost <= 0) {
            return 0;
        }

        if ($this->is_free_shipping) {
            return $shippingCost;
        }

        if ($this->discount_target !== 'shipping') {
            return 0;
        }

        if ($this->discount_type === 'fixed') {
            $discount = min((float) $this->discount_value, $shippingCost);
            return max(0, round($discount, 2));
        }

        if ($this->discount_type === 'percentage') {
            $percentage = (float) $this->discount_value;
            $discount = $shippingCost * ($percentage / 100);
            
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

    public function calculateTotalDiscount(float $subtotal, float $shippingCost): array
    {
        $productDiscount = 0;
        $shippingDiscount = 0;
        $isFreeShipping = false;

        if ($this->is_free_shipping) {
            $shippingDiscount = $shippingCost;
            $isFreeShipping = true;
        } elseif ($this->discount_target === 'shipping') {
            $shippingDiscount = $this->calculateShippingDiscount($shippingCost);
        } else {
            $productDiscount = $this->calculateDiscount($subtotal);
        }

        $totalDiscount = $productDiscount + $shippingDiscount;

        return [
            'product_discount' => $productDiscount,
            'shipping_discount' => $shippingDiscount,
            'total_discount' => $totalDiscount,
            'is_free_shipping' => $isFreeShipping,
            'effective_shipping' => $shippingCost - $shippingDiscount,
        ];
    }

    // ============================================
    // ACCESSORS / APPENDS
    // ============================================

    public function getDiscountLabelAttribute(): string
    {
        if ($this->is_free_shipping) {
            return '🎁 Gratis Ongkir';
        }

        if ($this->discount_target === 'shipping') {
            if ($this->discount_type === 'fixed') {
                return '🚚 Diskon Ongkir Rp ' . number_format($this->discount_value, 0, ',', '.');
            }
            $text = $this->discount_value . '%';
            if ($this->max_shipping_discount) {
                $text .= ' (Maks. Rp ' . number_format($this->max_shipping_discount, 0, ',', '.') . ')';
            }
            return '🚚 Diskon Ongkir ' . $text;
        }

        if ($this->discount_type === 'fixed') {
            return '💰 Rp ' . number_format($this->discount_value, 0, ',', '.');
        }

        $text = $this->discount_value . '%';
        if ($this->max_discount_amount) {
            $text .= ' (Maks. Rp ' . number_format($this->max_discount_amount, 0, ',', '.') . ')';
        }
        return '💰 ' . $text;
    }

    public function getStatusLabelAttribute(): string
    {
        $now = now();
        
        if (!$this->is_active) {
            return 'Tidak Aktif';
        }
        
        if ($now->lt($this->start_date)) {
            return 'Akan Datang';
        }
        
        if ($now->gt($this->end_date)) {
            return 'Kadaluarsa';
        }
        
        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return 'Habis';
        }
        
        return 'Aktif';
    }

    public function getStatusColorAttribute(): string
    {
        $now = now();
        
        if (!$this->is_active) {
            return 'gray';
        }
        
        if ($now->lt($this->start_date)) {
            return 'yellow';
        }
        
        if ($now->gt($this->end_date)) {
            return 'red';
        }
        
        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return 'red';
        }
        
        return 'green';
    }
}