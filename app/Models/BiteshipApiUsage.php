<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiteshipApiUsage extends Model
{
    protected $table = 'biteship_api_usage';

    protected $fillable = [
        'user_id',
        'endpoint',
        'action',
        'origin_postal_code',
        'destination_postal_code',
        'api_cost',
        'ip_address',
        'request_data',
        'response_summary',
    ];

    protected $casts = [
        'api_cost' => 'decimal:2',
        'response_summary' => 'array',
    ];

    const COST_PER_HIT = 5;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
