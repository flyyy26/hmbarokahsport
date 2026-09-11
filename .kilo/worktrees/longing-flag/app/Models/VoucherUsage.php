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
    ];

    protected $casts = [
        'voucher_id'       => 'integer',
        'user_id'          => 'integer',
        'order_id'         => 'integer',
        'discount_applied' => 'decimal:2',
        'product_discount' => 'decimal:2',    
        'shipping_discount' => 'decimal:2',   
        'is_free_shipping' => 'boolean',      
    ];

    /**
     * Relasi ke Voucher
     */
    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}