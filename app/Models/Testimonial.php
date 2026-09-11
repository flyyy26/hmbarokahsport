<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Testimonial extends Model
{
    protected $fillable = [
        'product_id',
        'product_variant_id',
        'user_id',
        'customer_name',
        'testimonial',
        'rating',
        'title',
        'is_active',
        'is_verified_purchase',
        'sort_order',
        'published_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_verified_purchase' => 'boolean',
        'rating' => 'integer',
        'sort_order' => 'integer',
        'published_at' => 'datetime',
    ];

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

    public function images(): HasMany
    {
        return $this->hasMany(TestimonialImage::class)->orderBy('sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc');
    }

    public function getRatingLabelAttribute(): string
    {
        return match ($this->rating) {
            1 => 'Sangat Buruk',
            2 => 'Buruk',
            3 => 'Cukup',
            4 => 'Baik',
            5 => 'Sangat Baik',
            default => 'Belum Diberi Rating',
        };
    }

    public function getRatingStarsAttribute(): string
    {
        $stars = '';
        for ($i = 0; $i < 5; $i++) {
            $stars .= $i < $this->rating ? '★' : '☆';
        }
        return $stars;
    }

    public function getFirstImageAttribute()
    {
        return $this->images->first();
    }

    public function getFirstImageUrlAttribute(): ?string
    {
        $image = $this->images->first();
        return $image ? asset('storage/' . $image->image) : null;
    }

    public function getVariantLabelAttribute(): string
    {
        if (!$this->variant) {
            return '-';
        }

        return $this->variant->option_combination ?: '-';
    }
}
