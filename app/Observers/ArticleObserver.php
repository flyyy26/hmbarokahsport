<?php

namespace App\Observers;

use App\Models\Article;
use App\Http\Controllers\Customer\CustomerArticleController;
use App\Http\Controllers\Customer\CustomerHomeController;

class ArticleObserver
{
    public function created(Article $article): void
    {
        $this->clearCache();
    }

    public function updated(Article $article): void
    {
        $this->clearCache();
    }

    public function deleted(Article $article): void
    {
        $this->clearCache();
    }

    public function restored(Article $article): void
    {
        $this->clearCache();
    }

    public function forceDeleted(Article $article): void
    {
        $this->clearCache();
    }

    /**
     * Clear cache artikel + homepage.
     */
    protected function clearCache(): void
    {
        CustomerArticleController::clearCache();
        CustomerHomeController::clearCache();
    }
}