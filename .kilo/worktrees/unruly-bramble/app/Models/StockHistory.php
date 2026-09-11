<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_variant_id',
        'user_id',
        'old_stock',
        'new_stock',
        'quantity_change',
        'reason',
        'note',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_stock' => 'integer',
        'new_stock' => 'integer',
        'quantity_change' => 'integer',
    ];

    // ============================================
    // RELATIONS
    // ============================================

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ============================================
    // ACCESSORS
    // ============================================

    public function getReasonLabelAttribute()
    {
        return [
            'restock' => '🔄 Restock',
            'sale' => '🛒 Penjualan',
            'return' => '↩️ Pengembalian',
            'adjustment' => '📝 Penyesuaian',
            'damaged' => '❌ Rusak/Expired',
            'transfer_in' => '📦 Transfer Masuk',
            'transfer_out' => '📤 Transfer Keluar',
            'order_cancelled' => '🚫 Pesanan Dibatalkan',
            'system_adjustment' => '⚙️ Penyesuaian Sistem',
            'other' => '📌 Lainnya',
        ][$this->reason] ?? $this->reason;
    }

    public function getReasonColorAttribute()
    {
        return [
            'restock' => 'green',
            'sale' => 'blue',
            'return' => 'purple',
            'adjustment' => 'yellow',
            'damaged' => 'red',
            'transfer_in' => 'indigo',
            'transfer_out' => 'orange',
            'order_cancelled' => 'gray',
            'system_adjustment' => 'gray',
            'other' => 'gray',
        ][$this->reason] ?? 'gray';
    }

    public function getQuantityDisplayAttribute()
    {
        $change = $this->quantity_change;
        if ($change > 0) {
            return '+' . $change;
        }
        return (string) $change;
    }

    public function getQuantityColorAttribute()
    {
        if ($this->quantity_change > 0) {
            return 'text-green-600';
        } elseif ($this->quantity_change < 0) {
            return 'text-red-600';
        }
        return 'text-gray-500';
    }
}