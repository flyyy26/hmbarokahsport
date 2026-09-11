<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutUs extends Model
{
    use HasFactory;

    protected $table = 'about_us';

    protected $fillable = [
        'title',
        'content',
        'vision',
        'mission',
        'version',
        'effective_date',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'effective_date' => 'date',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function getParsedContentAttribute()
    {
        return $this->content;
    }
}