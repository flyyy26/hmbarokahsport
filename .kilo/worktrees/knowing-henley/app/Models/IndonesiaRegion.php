<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndonesiaRegion extends Model
{
    protected $table = 'indonesia_regions';

    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'code',
        'name',
        'postal_code',
        'status',
        'search_text',
    ];
}