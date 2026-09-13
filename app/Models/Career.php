<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Career extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'department', 'location', 'type', 'level',
        'short_description', 'description', 'requirements', 'benefits',
        'salary_min', 'salary_max', 'show_salary', 'deadline', 'quota',
        'is_active', 'is_featured', 'published_at',
    ];

    protected $casts = [
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'show_salary' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'deadline' => 'date',
        'published_at' => 'datetime',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(CareerApplication::class);
    }

    // ============================================
    // ACCESSORS
    // ============================================
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'full_time'  => 'Full Time',
            'part_time'  => 'Part Time',
            'contract'   => 'Kontrak',
            'internship' => 'Magang',
            'freelance'  => 'Freelance',
            default      => '-',
        };
    }

    public function getLevelLabelAttribute(): string
    {
        return match ($this->level) {
            'staff'      => 'Staff',
            'senior'     => 'Senior',
            'supervisor' => 'Supervisor',
            'manager'    => 'Manager',
            'director'   => 'Director',
            default      => '-',
        };
    }

    public function getSalaryRangeAttribute(): ?string
    {
        if (!$this->show_salary || !$this->salary_min) {
            return null;
        }

        if ($this->salary_max && $this->salary_max > $this->salary_min) {
            return 'Rp ' . number_format($this->salary_min, 0, ',', '.') .
                   ' - Rp ' . number_format($this->salary_max, 0, ',', '.');
        }

        return 'Rp ' . number_format($this->salary_min, 0, ',', '.');
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->deadline && $this->deadline->isPast();
    }

    public function getDaysLeftAttribute(): ?int
    {
        if (!$this->deadline) return null;
        return max(0, (int) now()->diffInDays($this->deadline, false));
    }

    public function getApplicationsCountAttribute(): int
    {
        return $this->applications()->count();
    }

    // ============================================
    // SCOPES
    // ============================================
    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopePublished($q)
    {
        return $q->where(function ($sub) {
            $sub->whereNull('published_at')
                ->orWhere('published_at', '<=', now());
        });
    }

    public function scopeNotExpired($q)
    {
        return $q->where(function ($sub) {
            $sub->whereNull('deadline')
                ->orWhere('deadline', '>=', now()->startOfDay());
        });
    }

    public function scopeLatest($q)
    {
        return $q->orderBy('published_at', 'desc')
                 ->orderBy('created_at', 'desc');
    }

    // ============================================
    // BOOT
    // ============================================
    protected static function booted(): void
    {
        static::creating(function ($c) {
            if (empty($c->slug)) {
                $c->slug = Str::slug($c->title);
            }
        });

        static::updating(function ($c) {
            if ($c->isDirty('title')) {
                $c->slug = Str::slug($c->title);
            }
        });
    }
}