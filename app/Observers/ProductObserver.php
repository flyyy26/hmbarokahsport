<?php

namespace App\Observers;

use App\Models\Product;
use App\Http\Controllers\Customer\CustomerProductController;
use App\Http\Controllers\Customer\CustomerHomeController;

class ProductObserver
{
    public function created(Product $product): void
    {
        $this->clearCache();
    }

    public function updated(Product $product): void
    {
        $this->clearCache();
    }

    public function deleted(Product $product): void
    {
        $this->clearCache();
    }

    public function restored(Product $product): void
    {
        $this->clearCache();
    }

    public function forceDeleted(Product $product): void
    {
        $this->clearCache();
    }

    /**
     * Clear cache produk + homepage.
     */
    protected function clearCache(): void
    {
        CustomerProductController::clearCache();
        CustomerHomeController::clearCache();
    }
}