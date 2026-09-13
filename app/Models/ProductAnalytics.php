<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAnalytics extends Model
{
    public $timestamps = false;

    public const EVENT_VIEW        = 'view';
    public const EVENT_ADD_TO_CART = 'add_to_cart';

    protected $fillable = [
        'product_id',
        'event',
        'ip',
        'user_id',
        'session_id',
        'user_agent',
        'referer',
        'quantity',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'quantity'   => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopeInRange($query, $start, $end = null)
    {
        $query->where('created_at', '>=', $start);
        if ($end) {
            $query->where('created_at', '<=', $end);
        }
        return $query;
    }

    public function scopeViews($query)
    {
        return $query->where('event', self::EVENT_VIEW);
    }

    public function scopeAddToCarts($query)
    {
        return $query->where('event', self::EVENT_ADD_TO_CART);
    }
}