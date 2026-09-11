<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'store_name',
        'store_description',
        'logo',
        'favicon',
        'phone',
        'whatsapp',
        'email',
        'address',
        'google_maps',
        'instagram',
        'facebook',
        'tiktok',
    ];
}