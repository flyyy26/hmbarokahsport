<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class TestimonialImage extends Model
{
    protected $fillable = [
        'testimonial_id',
        'image',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function testimonial(): BelongsTo
    {
        return $this->belongsTo(Testimonial::class);
    }

    /**
     * 🔥 Image URL dengan fallback.
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }
        return asset('images/placeholder.webp');
    }
}