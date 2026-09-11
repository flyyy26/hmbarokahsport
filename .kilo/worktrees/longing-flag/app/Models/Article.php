<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'image',
        'author',
        'article_category_id',
        'tags',
        'views',
        'is_active',
        'is_featured',
        'published_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'views' => 'integer',
    ];

    // ============================================
    // RELATIONS
    // ============================================

    public function articleCategory(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class);
    }

    // ============================================
    // ACCESSORS
    // ============================================

    public function getImageUrlAttribute()
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return Storage::url($this->image);
        }
        return asset('images/default-article.jpg');
    }

    public function getExcerptAttribute($value)
    {
        if ($value) {
            return $value;
        }
        return Str::limit(strip_tags($this->content), 150);
    }

    public function getFormattedPublishedAtAttribute()
    {
        if ($this->published_at) {
            return $this->published_at->format('d/m/Y');
        }
        return $this->created_at->format('d/m/Y');
    }

    public function getTagsArrayAttribute()
    {
        return is_array($this->tags) ? $this->tags : [];
    }

    public function getTagsStringAttribute()
    {
        return is_array($this->tags) ? implode(', ', $this->tags) : '';
    }

    public function getCategoryNameAttribute()
    {
        return $this->articleCategory?->name ?? '-';
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * 🔥 SCOPE PUBLISHED - FIXED
     * Ambil artikel yang sudah dipublikasi (published_at <= now atau null)
     */
    public function scopePublished($query)
    {
        return $query->where(function($q) {
            $q->where('published_at', '<=', now())
            ->orWhereNull('published_at');
        });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('published_at', 'desc')
                     ->orderBy('created_at', 'desc');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('title', 'like', '%' . $search . '%')
                     ->orWhere('content', 'like', '%' . $search . '%')
                     ->orWhere('author', 'like', '%' . $search . '%');
    }

    public function getCategoryAttribute()
    {
        return $this->articleCategory?->name ?? '-';
    }

    public function getFormattedViewsAttribute()
    {
        return number_format($this->views ?? 0, 0, ',', '.');
    }

    // 🔥 TAMBAHKAN SCOPE UNTUK POPULER
    public function scopePopular($query, $limit = 5)
    {
        return $query->orderBy('views', 'desc')->limit($limit);
    }

    public function likes()
    {
        return $this->hasMany(ArticleLike::class);
    }

    public function comments()
    {
        return $this->hasMany(ArticleComment::class)->whereNull('parent_id')->where('is_active', true);
    }

    public function allComments()
    {
        return $this->hasMany(ArticleComment::class)->where('is_active', true);
    }

    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    public function getCommentsCountAttribute()
    {
        return $this->allComments()->count();
    }

    public function isLikedByUser($userId)
    {
        if (!$userId) return false;
        return $this->likes()->where('user_id', $userId)->exists();
    }
}