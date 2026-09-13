<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'user_id',
        'parent_id',
        'content',
        'is_active',
        'replied_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'replied_at' => 'datetime',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(ArticleComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(ArticleComment::class, 'parent_id')
                    ->where('is_active', true)
                    ->orderBy('created_at', 'asc');
    }

    public function allReplies()
    {
        return $this->hasMany(ArticleComment::class, 'parent_id')->where('is_active', true);
    }

    public function getFormattedDateAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getIsRepliedAttribute(): bool
    {
        return !is_null($this->replied_at);
    }

    /**
     * 🔥 Scope: komentar yang belum dibalas
     */
    public function scopeUnreplied($query)
    {
        return $query->whereNull('replied_at');
    }
}