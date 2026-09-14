<?php

namespace App\Observers;

use App\Models\Testimonial;
use App\Http\Controllers\Customer\CustomerHomeController;
use App\Http\Controllers\Customer\CustomerProductController;

class TestimonialObserver
{
    public function created(Testimonial $testimonial): void
    {
        $this->clearCache();
    }

    public function updated(Testimonial $testimonial): void
    {
        $this->clearCache();
    }

    public function deleted(Testimonial $testimonial): void
    {
        $this->clearCache();
    }

    public function restored(Testimonial $testimonial): void
    {
        $this->clearCache();
    }

    public function forceDeleted(Testimonial $testimonial): void
    {
        $this->clearCache();
    }

    /**
     * Clear cache homepage + produk.
     * Karena testimonial muncul di homepage & detail produk.
     */
    protected function clearCache(): void
    {
        CustomerHomeController::clearCache();
        CustomerProductController::clearCache();
    }
}