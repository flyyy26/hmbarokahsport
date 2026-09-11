<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'feature_id',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'feature_id' => 'integer',
    ];

    // ============================================
    // RELATIONS
    // ============================================

    /**
     * Relasi ke Product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi ke Feature
     */
    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class);
    }
}