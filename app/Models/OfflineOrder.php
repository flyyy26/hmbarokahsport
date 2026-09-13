<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfflineOrder extends Model
{
    protected $table = 'offline_orders';

    protected $fillable = [
        'order_number',
        'status',
        'payment_status',
        'shipping_status',
        'customer_name',
        'customer_phone',
        'customer_address',
        'shipping_name',
        'shipping_phone',
        'shipping_address',
        'shipping_city',
        'shipping_district',
        'shipping_subdistrict',
        'shipping_province',
        'shipping_postal_code',
        'subtotal',
        'shipping_cost',
        'original_shipping_cost',
        'transaction_discount',
        'transaction_discount_type',
        'discount',
        'total',
        'notes',
        'admin_notes',
        'tracking_number',
        'courier',
        'service',
        'payment_method',
        'midtrans_status',
        'paid_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
        'cancellation_status',
        'cancellation_reason',
        'cancelled_by_admin_id',
        'cancellation_requested_at',
        'cancellation_processed_at',
        'previous_shipping_status',
        'return_status',
        'return_reason',
        'return_requested_at',
        'return_processed_at',
        'returned_by_admin_id',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'original_shipping_cost' => 'decimal:2',
        'transaction_discount' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'cancellation_requested_at' => 'datetime',
        'cancellation_processed_at' => 'datetime',
        'return_requested_at' => 'datetime',
        'return_processed_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OfflineOrderItem::class, 'offline_order_id');
    }

    public function getStatusLabelAttribute()
    {
        return [
            'pending' => 'Menunggu',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'delivered' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            'refunded' => 'Dikembalikan',
        ][$this->status] ?? $this->status;
    }

    public function getPaymentStatusLabelAttribute()
    {
        return [
            'unpaid' => 'Belum Bayar',
            'paid' => 'Lunas',
            'failed' => 'Gagal',
            'refunded' => 'Dikembalikan',
        ][$this->payment_status] ?? $this->payment_status;
    }

    public function getShippingStatusLabelAttribute()
    {
        return [
            'pending' => 'Menunggu',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'delivered' => 'Terkirim',
            'cancelled' => 'Dibatalkan',
        ][$this->shipping_status] ?? $this->shipping_status;
    }
}
