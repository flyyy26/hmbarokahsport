<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageVisit extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'url',
        'full_url',
        'method',
        'ip',
        'user_agent',
        'referer',
        'user_id',
        'session_id',
        'response_time',
        'status_code',
        'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
        'response_time' => 'integer',
        'status_code' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopeInRange($query, $start, $end = null)
    {
        $query->where('visited_at', '>=', $start);
        if ($end) {
            $query->where('visited_at', '<=', $end);
        }
        return $query;
    }

    public function scopeSuccess($query)
    {
        return $query->whereBetween('status_code', [200, 299]);
    }

    // ============================================
    // ACCESSORS
    // ============================================

    public function getDeviceAttribute(): string
    {
        $ua = strtolower($this->user_agent ?? '');

        if (str_contains($ua, 'mobile') || str_contains($ua, 'android') || str_contains($ua, 'iphone')) {
            return 'Mobile';
        }
        if (str_contains($ua, 'tablet') || str_contains($ua, 'ipad')) {
            return 'Tablet';
        }
        return 'Desktop';
    }

    public function getBrowserAttribute(): string
    {
        $ua = $this->user_agent ?? '';

        if (str_contains($ua, 'Edg')) return 'Edge';
        if (str_contains($ua, 'OPR') || str_contains($ua, 'Opera')) return 'Opera';
        if (str_contains($ua, 'Chrome')) return 'Chrome';
        if (str_contains($ua, 'Firefox')) return 'Firefox';
        if (str_contains($ua, 'Safari')) return 'Safari';
        return 'Other';
    }

    public function getFormattedUrlAttribute(): string
    {
        return $this->url === '/' ? 'Home' : $this->url;
    }
}