<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Size extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'label',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // 🔥 Relasi ke Product melalui product_sizes
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_sizes')
                    ->withPivot('stock', 'price', 'discount_price', 'sku', 'weight', 'is_active')
                    ->withTimestamps();
    }

    // Scope untuk ukuran aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope untuk sorting
    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order');
    }
}