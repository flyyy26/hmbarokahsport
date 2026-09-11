<?php

namespace App\Traits;

trait ProductDiscountTrait
{
    /**
     * 🔥 ATTACH DISCOUNT DATA TO PRODUCT
     * Menambahkan data diskon ke produk (termasuk diskon produk global)
     */
    public function attachDiscountData($product)
    {
        if (!$product || !$product->relationLoaded('variants')) {
            return;
        }

        // 🔥 CEK DISKON PRODUK (GLOBAL)
        $hasProductDiscount = $product->isOnProductDiscount();
        $productDiscountPercent = 0;
        $maxDiscountPercent = 0;
        $hasDiscount = false;
        $bestDiscountVariant = null;

        // 🔥 HITUNG DISKON DARI VARIAN
        foreach ($product->variants as $variant) {
            // Gunakan method dari ProductVariant
            $effectivePrice = $variant->effective_price;
            $discountPercent = $variant->discount_percent;
            
            // Simpan ke variant
            $variant->effective_price = $effectivePrice;
            $variant->discount_percent = $discountPercent;
            
            if ($discountPercent > 0) {
                $hasDiscount = true;
                if ($discountPercent > $maxDiscountPercent) {
                    $maxDiscountPercent = $discountPercent;
                    $bestDiscountVariant = $variant;
                }
            }
        }

        // 🔥 JIKA TIDAK ADA DISKON DARI VARIAN, CEK DISKON PRODUK
        if (!$hasDiscount && $hasProductDiscount) {
            $minPrice = $product->variants->min('price') ?? 0;
            $productDiscountPercent = $product->getProductDiscountPercent($minPrice);
            if ($productDiscountPercent > 0) {
                $hasDiscount = true;
                $maxDiscountPercent = $productDiscountPercent;
                
                // Update semua variant dengan diskon produk
                foreach ($product->variants as $variant) {
                    $variant->discount_percent = $productDiscountPercent;
                    $variant->effective_price = $product->calculateProductDiscount($variant->price);
                }
            }
        }

        // 🔥 SET PROPERTI KE PRODUCT
        $product->has_discount = $hasDiscount;
        $product->max_discount_percent = $maxDiscountPercent;
        $product->best_discount_variant = $bestDiscountVariant;
        $product->has_product_discount = $hasProductDiscount;
        $product->product_discount_percent = $productDiscountPercent;

        // 🔥 HITUNG HARGA TERMURAH
        $minEffectivePrice = null;
        foreach ($product->variants as $variant) {
            $effectivePrice = $variant->effective_price ?? $variant->price;
            if ($minEffectivePrice === null || $effectivePrice < $minEffectivePrice) {
                $minEffectivePrice = $effectivePrice;
            }
        }
        $product->min_effective_price = $minEffectivePrice;

        // 🔥 HITUNG HARGA TERTINGGI
        $maxPrice = $product->variants->max('price') ?? 0;
        $product->max_price = $maxPrice;

        // 🔥 LABEL HARGA
        if ($hasDiscount && $minEffectivePrice < $maxPrice) {
            $product->price_label = 'Rp ' . number_format($minEffectivePrice, 0, ',', '.') . 
                                    ' - Rp ' . number_format($maxPrice, 0, ',', '.');
            $product->discount_label = 'Diskon ' . round($maxDiscountPercent) . '%';
        } elseif ($hasDiscount) {
            $product->price_label = 'Rp ' . number_format($minEffectivePrice, 0, ',', '.');
            $product->discount_label = 'Diskon ' . round($maxDiscountPercent) . '%';
        } else {
            $product->price_label = 'Rp ' . number_format($minEffectivePrice ?? 0, 0, ',', '.');
            $product->discount_label = '';
        }

        // 🔥 BADGE
        if ($hasDiscount && $maxDiscountPercent > 0) {
            $product->badge_label = 'Diskon ' . round($maxDiscountPercent) . '%';
            $product->badge_color = 'red';
        } else {
            $product->badge_label = null;
            $product->badge_color = null;
        }

        // 🔥 DISPLAY PRICE UNTUK VIEW
        $product->display_price = $product->price_label;
        $product->original_price_display = $hasDiscount ? 'Rp ' . number_format($maxPrice, 0, ',', '.') : null;

        return $product;
    }
}