<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Marketplace extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'url',
        'is_active',
        'sort_order',
    ];


    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];


    /*
    |--------------------------------------------------------------------------
    | SCOPE ACTIVE
    |--------------------------------------------------------------------------
    */

    public function scopeActive(
        Builder $query
    ): Builder {

        return $query
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name');

    }
}