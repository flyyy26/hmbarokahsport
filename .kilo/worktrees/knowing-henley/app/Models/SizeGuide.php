<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SizeGuide extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'size',
        'dimensions',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'dimensions' => 'array',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * 🔥 Mendapatkan dimensi sebagai collection
     */
    public function getDimensionsCollectionAttribute()
    {
        return collect($this->dimensions ?? []);
    }

    /**
     * 🔥 Mendapatkan label dimensi
     */
    public function getDimensionLabelsAttribute()
    {
        $labels = [];
        foreach ($this->dimensions ?? [] as $key => $value) {
            $labels[] = $key;
        }
        return $labels;
    }
}