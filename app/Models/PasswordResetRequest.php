<?php
// app/Models/PasswordResetRequest.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PasswordResetRequest extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'status',
        'token',
        'admin_note',
        'processed_by',
        'processed_at',
        'expires_at',
        'used_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    // ============================================
    // RELATIONS
    // ============================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved')
            ->where('expires_at', '>', now());
    }

    // ============================================
    // HELPERS
    // ============================================

    /**
     * Generate token baru (64 char random, unik)
     */
    public static function generateToken(): string
    {
        do {
            $token = Str::random(64);
        } while (self::where('token', $token)->exists());

        return $token;
    }

    /**
     * Approve request — generate token + expiry
     */
    public function approve(int $adminId, int $expiryHours = 24): bool
    {
        return $this->update([
            'status' => 'approved',
            'token' => self::generateToken(),
            'processed_by' => $adminId,
            'processed_at' => now(),
            'expires_at' => now()->addHours($expiryHours),
            'admin_note' => null,
        ]);
    }

    /**
     * Reject request
     */
    public function reject(int $adminId, string $note = null): bool
    {
        return $this->update([
            'status' => 'rejected',
            'processed_by' => $adminId,
            'processed_at' => now(),
            'admin_note' => $note,
            'token' => null,
        ]);
    }

    /**
     * Tandai sudah dipakai
     */
    public function markAsUsed(): bool
    {
        return $this->update([
            'status' => 'used',
            'used_at' => now(),
            'token' => null,
        ]);
    }

    /**
     * Cek apakah token masih valid
     */
    public function isTokenValid(): bool
    {
        return $this->status === 'approved'
            && $this->token
            && $this->expires_at
            && $this->expires_at->isFuture();
    }

    /**
     * Label status untuk UI
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => '⏳ Menunggu Verifikasi',
            'approved' => '✅ Disetujui',
            'rejected' => '❌ Ditolak',
            'used' => '🔒 Sudah Digunakan',
            'expired' => '⏰ Kadaluarsa',
            default => 'Unknown',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            'used' => 'gray',
            'expired' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Cek apakah user punya request pending
     */
    public static function hasPendingRequest(int $userId): bool
    {
        return self::where('user_id', $userId)
            ->where('status', 'pending')
            ->exists();
    }
}