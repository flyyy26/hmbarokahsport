<?php

namespace App\Observers;

use App\Models\ProductVariant;
use App\Http\Controllers\Customer\CustomerHomeController;
use App\Http\Controllers\Customer\CustomerProductController;

class ProductVariantObserver
{
    public function created(ProductVariant $variant): void
    {
        $this->clearCache();
    }

    public function updated(ProductVariant $variant): void
    {
        $this->clearCache();
    }

    public function deleted(ProductVariant $variant): void
    {
        $this->clearCache();
    }

    public function restored(ProductVariant $variant): void
    {
        $this->clearCache();
    }

    public function forceDeleted(ProductVariant $variant): void
    {
        $this->clearCache();
    }

    /**
     * Clear cache homepage + product.
     * Karena varian menentukan harga, stok, diskon, dan gambar.
     */
    protected function clearCache(): void
    {
        CustomerHomeController::clearCache();
        CustomerProductController::clearCache();
    }
}