<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Banner extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'image_mobile',
        'button_text',
        'button_url',
        'sort_order',
        'is_active',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /**
     * Scope untuk banner aktif
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('starts_at')
                  ->orWhere('starts_at', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', now());
            })
            ->orderBy('sort_order');
    }

    /**
     * Get image URL untuk desktop
     */
    public function getImageUrlAttribute(): string
    {
        return $this->image ? asset('storage/' . $this->image) : asset('images/default-banner.jpg');
    }

    /**
     * Get image URL untuk mobile
     */
    public function getImageMobileUrlAttribute(): string
    {
        return $this->image_mobile ? asset('storage/' . $this->image_mobile) : $this->image_url;
    }
}