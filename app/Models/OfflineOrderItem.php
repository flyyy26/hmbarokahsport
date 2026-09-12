<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo as BelongsToRelation;

class OfflineOrderItem extends Model
{
    protected $table = 'offline_order_items';

    protected $fillable = [
        'offline_order_id',
        'product_id',
        'product_variant_id',
        'product_name',
        'variant_name',
        'sku',
        'price',
        'quantity',
        'subtotal',
        'variant_attributes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'variant_attributes' => 'array',
    ];

    public function offlineOrder(): BelongsTo
    {
        return $this->belongsTo(OfflineOrder::class, 'offline_order_id');
    }

    public function product(): BelongsToRelation
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsToRelation
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
