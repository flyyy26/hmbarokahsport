<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Services\BiteshipService;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'payment_status',
        'shipping_status',
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
        'cancellation_requested_at' => 'datetime',
        'cancellation_processed_at' => 'datetime',
        'return_requested_at' => 'datetime',
        'return_processed_at' => 'datetime',
    ];

    // ============================================
    // RELATIONS
    // ============================================

    public function getTrackingStatus()
    {
        if (!$this->biteship_order_id) {
            return null;
        }

        $trackingId = $this->biteship_order_id;
        if ($this->biteship_tracking_url) {
            $parsed = parse_url($this->biteship_tracking_url);
            if (!empty($parsed['path'])) {
                $trackingId = trim($parsed['path'], '/');
            }
        }

        $biteship = app(BiteshipService::class);
        return $biteship->trackOrder(
            $this->biteship_order_id,
            $this->tracking_number,
            $this->biteship_tracking_url
        );
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

    /**
     * Check Biteship actual tracking status for delivery confirmation
     */
    public function isDeliveredOnBiteship(): bool
    {
        if (!$this->biteship_order_id) {
            return false;
        }

        $biteship = app(BiteshipService::class);
        $result = $biteship->getTrackingDetails(
            $this->biteship_order_id,
            $this->tracking_number,
            $this->biteship_tracking_url
        );

        if (!$result['success']) {
            return false;
        }

        $status = strtolower($result['data']['status'] ?? '');
        return $status === 'delivered';
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cancelledByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by_admin_id');
    }

    public function returnedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_by_admin_id');
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
            'cancellation_requested' => 'Menunggu Pembatalan',
        ][$this->shipping_status] ?? $this->shipping_status;
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
            'cancellation_requested' => 'Menunggu Pembatalan',
        ][$this->shipping_status] ?? $this->shipping_status;
    }

    public function getPreviousShippingStatusLabelAttribute()
    {
        return [
            'pending' => 'Menunggu',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'delivered' => 'Terkirim',
            'cancelled' => 'Dibatalkan',
        ][$this->previous_shipping_status] ?? 'Menunggu';
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

        $color = $colors[$this->shipping_status] ?? 'gray';

        return "<span class='inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-{$color}-100 text-{$color}-800'>
            {$this->shipping_status_label}
        </span>";
    }

    // ============================================
    // CANCELLATION ACCESSORS
    // ============================================

    public function getCancellationStatusLabelAttribute()
    {
        return [
            'pending' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
        ][$this->cancellation_status] ?? '-';
    }

    public function getCancellationStatusColorAttribute()
    {
        return [
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
        ][$this->cancellation_status] ?? 'gray';
    }

    public function getCanRequestCancellationAttribute(): bool
    {
        $nonCancellableStatuses = ['shipped', 'delivered', 'cancelled'];
        if (in_array($this->shipping_status, $nonCancellableStatuses)) {
            return false;
        }
        if ($this->cancellation_status === 'pending') {
            return false;
        }
        return true;
    }

    public function getCancellationButtonTextAttribute(): string
    {
        if ($this->cancellation_status === 'pending') {
            return 'Menunggu Persetujuan Admin';
        }
        if ($this->shipping_status === 'cancelled') {
            return 'Pesanan Dibatalkan';
        }
        return 'Batalkan Pesanan';
    }

    // ============================================
    // RETURN ACCESSORS
    // ============================================

    public function getReturnStatusLabelAttribute()
    {
        return [
            'pending' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'completed' => 'Selesai',
        ][$this->return_status] ?? '-';
    }

    public function getReturnStatusColorAttribute()
    {
        return [
            'pending' => 'yellow',
            'approved' => 'blue',
            'rejected' => 'red',
            'completed' => 'emerald',
        ][$this->return_status] ?? 'gray';
    }

    public function getCanRequestReturnAttribute(): bool
    {
        if ($this->shipping_status !== 'delivered') {
            return false;
        }
        if (in_array($this->return_status, ['pending', 'approved', 'completed'])) {
            return false;
        }
        if ($this->delivered_at && $this->delivered_at->addDays(7)->isPast()) {
            return false;
        }
        return true;
    }

    // ============================================
    // CANCELLATION METHODS
    // ============================================

    /**
     * Request cancellation by customer
     */
    public function requestCancellation(string $reason): bool
    {
        if (!$this->can_request_cancellation) {
            return false;
        }

        $this->update([
            'cancellation_status' => 'pending',
            'cancellation_reason' => $reason,
            'cancellation_requested_at' => now(),
            'previous_shipping_status' => $this->shipping_status,
            'shipping_status' => 'cancellation_requested',
        ]);

        return true;
    }

    /**
     * Approve cancellation by admin - restock products
     */
    public function approveCancellation(int $adminId): bool
    {
        if ($this->cancellation_status !== 'pending') {
            return false;
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($adminId) {
            // Restore stock for each item
            foreach ($this->items as $item) {
                if ($item->variant) {
                    $item->variant->addStock($item->quantity, 'order_cancelled', "Order {$this->order_number} dibatalkan oleh admin");
                } elseif ($item->product) {
                    $firstVariant = $item->product->variants->first();
                    if ($firstVariant) {
                        $firstVariant->addStock($item->quantity, 'order_cancelled', "Order {$this->order_number} dibatalkan oleh admin");
                    }
                }
            }

            $this->update([
                'cancellation_status' => 'approved',
                'shipping_status' => 'cancelled',
                'cancelled_by_admin_id' => $adminId,
                'cancellation_processed_at' => now(),
                'cancelled_at' => now(),
            ]);

            return true;
        });
    }

    /**
     * Reject cancellation by admin - restore original status
     */
    public function rejectCancellation(int $adminId): bool
    {
        if ($this->cancellation_status !== 'pending') {
            return false;
        }

        $this->update([
            'cancellation_status' => 'rejected',
            'shipping_status' => $this->previous_shipping_status ?? 'pending',
            'cancelled_by_admin_id' => $adminId,
            'cancellation_processed_at' => now(),
            'previous_shipping_status' => null,
        ]);

        return true;
    }

    public function cancelByCustomer(string $reason): bool
    {
        // Cek apakah pesanan bisa dibatalkan
        if (!$this->canBeCancelled()) {
            return false;
        }

        return DB::transaction(function () use ($reason) {
            // Restore stok untuk setiap item
            foreach ($this->items as $item) {
                if ($item->variant) {
                    // Tambah stok varian
                    $item->variant->addStock(
                        $item->quantity,
                        'order_cancelled',
                        "Pesanan {$this->order_number} dibatalkan oleh customer"
                    );
                } elseif ($item->product) {
                    // Jika tidak ada varian, coba ambil varian pertama
                    $firstVariant = $item->product->variants->first();
                    if ($firstVariant) {
                        $firstVariant->addStock(
                            $item->quantity,
                            'order_cancelled',
                            "Pesanan {$this->order_number} dibatalkan oleh customer"
                        );
                    }
                }
            }

            // Update status order
            $this->update([
                'shipping_status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_status' => 'approved',
                'cancellation_reason' => $reason,
                'cancellation_requested_at' => now(),
                'cancellation_processed_at' => now(),
            ]);

            // Jika sudah bayar, refund (opsional)
            if ($this->payment_status === 'paid') {
                // Logika refund di sini jika diperlukan
                // $this->payment_status = 'refunded';
                // $this->save();
            }

            return true;
        });
    }

    public function cancelByAdmin(int $adminId, string $reason): bool
    {
        if ($this->shipping_status === 'cancelled') {
            return false;
        }

        return DB::transaction(function () use ($adminId, $reason) {
            // Restore stok untuk setiap item
            foreach ($this->items as $item) {
                if ($item->variant) {
                    $item->variant->addStock(
                        $item->quantity,
                        'order_cancelled',
                        "Pesanan {$this->order_number} dibatalkan oleh admin"
                    );
                } elseif ($item->product) {
                    $firstVariant = $item->product->variants->first();
                    if ($firstVariant) {
                        $firstVariant->addStock(
                            $item->quantity,
                            'order_cancelled',
                            "Pesanan {$this->order_number} dibatalkan oleh admin"
                        );
                    }
                }
            }

            $this->update([
                'shipping_status' => 'cancelled',
                'cancelled_at' => now(),
                'cancelled_by_admin_id' => $adminId,
                'cancellation_status' => 'approved',
                'cancellation_reason' => $reason,
                'cancellation_processed_at' => now(),
            ]);

            return true;
        });
    }

    public function canBeCancelled(): bool
    {
        $cancellable = ['pending', 'processing'];
        
        if (!in_array($this->shipping_status, $cancellable)) {
            return false;
        }

        if ($this->cancellation_status === 'pending') {
            return false;
        }

        return true;
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
        return $query->where('shipping_status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('shipping_status', 'processing');
    }

    public function scopeShipped($query)
    {
        return $query->where('shipping_status', 'shipped');
    }

    public function scopeDelivered($query)
    {
        return $query->where(function ($q) {
            $q->where('shipping_status', 'delivered')
              ->orWhereNotNull('delivered_at');
        });
    }

    public function scopeCancelled($query)
    {
        return $query->where('shipping_status', 'cancelled');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'unpaid');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    // ============================================
    // RETURN METHODS
    // ============================================

    public function requestReturn(string $reason): bool
    {
        if (!$this->can_request_return) {
            return false;
        }

        $this->update([
            'return_status' => 'pending',
            'return_reason' => $reason,
            'return_requested_at' => now(),
        ]);

        return true;
    }

    public function approveReturn(int $adminId): bool
    {
        if ($this->return_status !== 'pending') {
            return false;
        }

        $this->update([
            'return_status' => 'approved',
            'return_processed_at' => now(),
            'returned_by_admin_id' => $adminId,
        ]);

        return true;
    }

    public function rejectReturn(int $adminId): bool
    {
        if ($this->return_status !== 'pending') {
            return false;
        }

        $this->update([
            'return_status' => 'rejected',
            'return_processed_at' => now(),
            'returned_by_admin_id' => $adminId,
        ]);

        return true;
    }

    public function completeReturn(): bool
    {
        if ($this->return_status !== 'approved') {
            return false;
        }

        return DB::transaction(function () {
            foreach ($this->items as $item) {
                if ($item->variant) {
                    $item->variant->addStock(
                        $item->quantity,
                        'order_cancelled',
                        "Retur Pesanan {$this->order_number} disetujui oleh admin"
                    );
                } elseif ($item->product) {
                    $firstVariant = $item->product->variants->first();
                    if ($firstVariant) {
                        $firstVariant->addStock(
                            $item->quantity,
                            'order_cancelled',
                            "Retur Pesanan {$this->order_number} disetujui oleh admin"
                        );
                    }
                }
            }

            $this->update([
                'return_status' => 'completed',
                'return_processed_at' => now(),
            ]);

            return true;
        });
    }
}