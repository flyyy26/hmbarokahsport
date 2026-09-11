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
    ];

    protected $casts = [
        'is_active' => 'boolean',
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
}