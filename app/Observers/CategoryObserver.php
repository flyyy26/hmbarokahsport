<?php

namespace App\Observers;

use App\Models\Category;
use App\Http\Controllers\Customer\CustomerHomeController;
use App\Http\Controllers\Customer\CustomerProductController;

class CategoryObserver
{
    public function created(Category $category): void
    {
        $this->clearCache();
    }

    public function updated(Category $category): void
    {
        $this->clearCache();
    }

    public function deleted(Category $category): void
    {
        $this->clearCache();
    }

    public function restored(Category $category): void
    {
        $this->clearCache();
    }

    public function forceDeleted(Category $category): void
    {
        $this->clearCache();
    }

    /**
     * Clear cache homepage + product (karena kategori dipakai di filter).
     */
    protected function clearCache(): void
    {
        CustomerHomeController::clearCache();
        CustomerProductController::clearCache();
    }
}