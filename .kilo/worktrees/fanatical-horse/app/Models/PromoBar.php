<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PromoBar extends Model
{
    use HasFactory;

    protected $fillable = [
        'text_left',
        'text_right',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the active promo bar
     */
    public static function getActive()
    {
        return self::where('is_active', true)->first();
    }
}