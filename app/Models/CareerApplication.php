<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CareerApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'career_id',
        'user_id',
        'full_name',
        'email',
        'phone',
        'address',
        'birth_date',
        'gender',
        'last_education',
        'major',
        'experience_years',
        'cover_letter',
        'cv_file',
        'portfolio_file',
        'read_at',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'read_at'    => 'datetime',
    ];

    // ============================================
    // RELATIONS
    // ============================================
    public function career(): BelongsTo
    {
        return $this->belongsTo(Career::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
    // ============================================
    // ACCESSORS
    // ============================================
    public function getCvUrlAttribute(): ?string
    {
        return $this->cv_file && Storage::disk('public')->exists($this->cv_file)
            ? Storage::url($this->cv_file) : null;
    }

    public function getPortfolioUrlAttribute(): ?string
    {
        return $this->portfolio_file && Storage::disk('public')->exists($this->portfolio_file)
            ? Storage::url($this->portfolio_file) : null;
    }

    /**
     * 🔥 WhatsApp URL — auto-format nomor HP
     */
    public function getWhatsappUrlAttribute(): ?string
    {
        if (!$this->phone) return null;

        // Normalisasi: hapus non-digit, ubah +62/62/8 jadi 62
        $phone = preg_replace('/[^0-9]/', '', $this->phone);

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (str_starts_with($phone, '8')) {
            $phone = '62' . $phone;
        } elseif (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        // Pesan default
        $message = rawurlencode(
            "Halo {$this->full_name}, kami dari Barokah Sport ingin menindaklanjuti lamaran Anda untuk posisi {$this->career->title}."
        );

        return "https://wa.me/{$phone}?text={$message}";
    }

    /**
     * 🔥 Format nomor HP untuk display
     */
    public function getFormattedPhoneAttribute(): string
    {
        if (!$this->phone) return '-';

        $phone = preg_replace('/[^0-9]/', '', $this->phone);

        // Format 0812-3456-7890
        if (strlen($phone) >= 10) {
            return substr($phone, 0, 4) . '-' .
                   substr($phone, 4, 4) . '-' .
                   substr($phone, 8);
        }

        return $phone;
    }
}