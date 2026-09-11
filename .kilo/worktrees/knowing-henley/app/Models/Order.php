<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'order_number',
        'status',
        'payment_status',
        'shipping_status',
        'shipping_name',
        'shipping_phone',
        'shipping_address',
        'shipping_city',
        'shipping_province',
        'shipping_postal_code',
        'subtotal',
        'shipping_cost',
        'discount',
        'total',
        'notes',
        'admin_notes',
        'tracking_number',
        'courier',
        'service',
        'paid_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
        'biteship_order_id',
        'biteship_waybill_id',
        'biteship_tracking_url',
        'payment_method',
        'midtrans_status',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // ============================================
    // RELATIONS
    // ============================================

    public function getTrackingStatus()
    {
        if (!$this->biteship_order_id) {
            return null;
        }

        $biteship = app(BiteshipService::class);
        return $biteship->trackOrder($this->biteship_order_id);
    }

    // Method untuk cetak resi
    public function getWaybill()
    {
        if (!$this->biteship_order_id) {
            return null;
        }

        $biteship = app(BiteshipService::class);
        return $biteship->getWaybill($this->biteship_order_id);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ============================================
    // ACCESSORS
    // ============================================

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
        ][$this->shipping_status] ?? $this->shipping_status;
    }

    public function getStatusBadgeAttribute()
    {
        $colors = [
            'pending' => 'yellow',
            'processing' => 'blue',
            'shipped' => 'indigo',
            'delivered' => 'green',
            'cancelled' => 'red',
            'refunded' => 'gray',
        ];

        $color = $colors[$this->status] ?? 'gray';

        return "<span class='inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-{$color}-100 text-{$color}-800'>
            {$this->status_label}
        </span>";
    }

    // ============================================
    // BOOT
    // ============================================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . Str::random(8) . '-' . time();
            }
        });
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'unpaid');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }
}