<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function sizeGuides(): HasMany
    {
        return $this->hasMany(SizeGuide::class)->orderBy('sort_order');
    }

    /**
     * 🔥 Mendapatkan semua label dimensi yang digunakan di kategori ini
     */
    public function getDimensionLabelsAttribute()
    {
        $labels = [];
        foreach ($this->sizeGuides as $guide) {
            if ($guide->dimensions) {
                foreach ($guide->dimensions as $key => $value) {
                    if (!in_array($key, $labels)) {
                        $labels[] = $key;
                    }
                }
            }
        }
        return $labels;
    }

    /**
     * 🔥 Mendapatkan data size guide dalam format array untuk JavaScript
     */
    public function getSizeGuideDataAttribute()
    {
        return $this->sizeGuides->map(function($guide) {
            return [
                'size' => $guide->size,
                'dimensions' => $guide->dimensions ?? [],
            ];
        })->toArray();
    }

    public function setDimensionLabelsAttribute($value)
    {
        $this->attributes['dimension_labels'] = is_array($value) ? json_encode($value) : $value;
    }

    // Scope untuk kategori aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}