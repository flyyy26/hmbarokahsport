<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerAddress extends Model
{
    protected $table = 'user_addresses';

    protected $fillable = [
        'user_id',
        'customer_id',
        'label',
        'recipient_name',
        'recipient_phone',
        'address',
        'city',
        'district',
        'subdistrict',
        'province',
        'postal_code',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}