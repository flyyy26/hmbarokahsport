<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoucherUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'voucher_id',
        'user_id',
        'order_id',
        'discount_applied',
        'product_discount',    
        'shipping_discount',   
        'is_free_shipping',    
        'courier_applied',     
        'shipping_cost_before',
        'shipping_cost_after',
        'subtotal_before',
        'subtotal_after',
        'total_before',
        'total_after',
    ];

    protected $casts = [
        'voucher_id'           => 'integer',
        'user_id'              => 'integer',
        'order_id'             => 'integer',
        'discount_applied'     => 'decimal:2',
        'product_discount'     => 'decimal:2',    
        'shipping_discount'    => 'decimal:2',   
        'is_free_shipping'     => 'boolean',
        'shipping_cost_before' => 'decimal:2',
        'shipping_cost_after'  => 'decimal:2',
        'subtotal_before'      => 'decimal:2',
        'subtotal_after'       => 'decimal:2',
        'total_before'         => 'decimal:2',
        'total_after'          => 'decimal:2',
    ];

    protected $appends = [
        'discount_label',
        'savings_percent',
    ];

    // ============================================
    // RELATIONS
    // ============================================

    /**
     * Relasi ke Voucher
     */
    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    /**
     * Relasi ke User (Customer)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Scope untuk usage dengan diskon produk
     */
    public function scopeWithProductDiscount($query)
    {
        return $query->where('product_discount', '>', 0);
    }

    /**
     * Scope untuk usage dengan diskon ongkir
     */
    public function scopeWithShippingDiscount($query)
    {
        return $query->where(function($q) {
            $q->where('shipping_discount', '>', 0)
              ->orWhere('is_free_shipping', true);
        });
    }

    /**
     * Scope untuk usage dengan gratis ongkir
     */
    public function scopeWithFreeShipping($query)
    {
        return $query->where('is_free_shipping', true);
    }

    /**
     * Scope berdasarkan user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope berdasarkan voucher
     */
    public function scopeForVoucher($query, int $voucherId)
    {
        return $query->where('voucher_id', $voucherId);
    }

    // ============================================
    // ACCESSORS / APPENDS
    // ============================================

    /**
     * Mendapatkan label diskon
     */
    public function getDiscountLabelAttribute(): string
    {
        if ($this->is_free_shipping) {
            return '🎁 Gratis Ongkir';
        }

        if ($this->shipping_discount > 0) {
            return '🚚 Diskon Ongkir Rp ' . number_format($this->shipping_discount, 0, ',', '.');
        }

        if ($this->product_discount > 0) {
            return '💰 Diskon Produk Rp ' . number_format($this->product_discount, 0, ',', '.');
        }

        return 'Diskon Rp ' . number_format($this->discount_applied, 0, ',', '.');
    }

    /**
     * Mendapatkan persentase penghematan
     */
    public function getSavingsPercentAttribute(): float
    {
        $totalBefore = $this->total_before ?? ($this->subtotal_before + ($this->shipping_cost_before ?? 0));
        if ($totalBefore <= 0) {
            return 0;
        }
        return round(($this->discount_applied / $totalBefore) * 100, 2);
    }

    // ============================================
    // HELPERS
    // ============================================

    /**
     * Cek apakah usage memiliki diskon produk
     */
    public function hasProductDiscount(): bool
    {
        return $this->product_discount > 0;
    }

    /**
     * Cek apakah usage memiliki diskon ongkir
     */
    public function hasShippingDiscount(): bool
    {
        return $this->shipping_discount > 0 || $this->is_free_shipping;
    }

    /**
     * Cek apakah usage adalah gratis ongkir
     */
    public function isFreeShipping(): bool
    {
        return $this->is_free_shipping;
    }

    /**
     * Mendapatkan total penghematan
     */
    public function getTotalSavings(): float
    {
        return $this->discount_applied;
    }

    /**
     * Mendapatkan total setelah diskon
     */
    public function getTotalAfterDiscount(): float
    {
        return $this->total_after ?? ($this->subtotal_after + ($this->shipping_cost_after ?? 0));
    }
}