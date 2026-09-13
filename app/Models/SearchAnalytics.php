<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchAnalytics extends Model
{
    public $timestamps = false;

    public const UPDATED_AT = null;

    protected $fillable = [
        'keyword',
        'ip',
        'user_id',
        'session_id',
        'user_agent',
        'referer',
        'results_count',
        'created_at',
    ];

    protected $casts = [
        'created_at'    => 'datetime',
        'results_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeInRange($query, $start, $end = null)
    {
        $query->where('created_at', '>=', $start);
        if ($end) {
            $query->where('created_at', '<=', $end);
        }
        return $query;
    }
}