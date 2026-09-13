<?php

namespace App\Traits;

trait ProductDiscountTrait
{
    /**
     * 🔥 ATTACH DISCOUNT DATA TO PRODUCT
     */
    public function attachDiscountData($product)
    {
        if (!$product || !$product->relationLoaded('variants')) {
            return $product;
        }

        // ============================================
        // 1. HITUNG DISKON PER VARIAN
        // ============================================
        $hasProductDiscount = $product->isOnProductDiscount();
        $productDiscountPercent = 0;
        $maxDiscountPercent = 0;
        $hasDiscount = false;
        $bestDiscountVariant = null;

        // Simpan harga ke array untuk memudahkan perhitungan
        $allPrices = [];
        $allEffectivePrices = [];

        foreach ($product->variants as $variant) {
            $price = (float) $variant->price;
            $effectivePrice = (float) ($variant->effective_price ?? $variant->price);
            $discountPercent = (float) ($variant->discount_percent ?? 0);

            // Simpan ke variant
            $variant->effective_price = $effectivePrice;
            $variant->discount_percent = $discountPercent;

            $allPrices[] = $price;
            $allEffectivePrices[] = $effectivePrice;

            if ($discountPercent > 0) {
                $hasDiscount = true;
                if ($discountPercent > $maxDiscountPercent) {
                    $maxDiscountPercent = $discountPercent;
                    $bestDiscountVariant = $variant;
                }
            }
        }

        // ============================================
        // 2. FALLBACK KE DISKON PRODUK
        // ============================================
        if (!$hasDiscount && $hasProductDiscount) {
            $minPrice = !empty($allPrices) ? min($allPrices) : 0;
            $productDiscountPercent = $product->getProductDiscountPercent($minPrice);

            if ($productDiscountPercent > 0) {
                $hasDiscount = true;
                $maxDiscountPercent = $productDiscountPercent;

                // Rebuild effective prices dengan diskon produk
                $allEffectivePrices = [];
                foreach ($product->variants as $variant) {
                    $variant->discount_percent = $productDiscountPercent;
                    $variant->effective_price = $product->calculateProductDiscount($variant->price);
                    $allEffectivePrices[] = (float) $variant->effective_price;
                }
            }
        }

        // ============================================
        // 3. HITUNG RANGE HARGA
        // ============================================
        $minOriginalPrice = !empty($allPrices) ? min($allPrices) : 0;
        $maxOriginalPrice = !empty($allPrices) ? max($allPrices) : 0;
        $minEffectivePrice = !empty($allEffectivePrices) ? min($allEffectivePrices) : 0;
        $maxEffectivePrice = !empty($allEffectivePrices) ? max($allEffectivePrices) : 0;

        // ============================================
        // 4. SET PROPERTIES KE PRODUCT
        // ============================================
        $product->has_discount = $hasDiscount;
        $product->max_discount_percent = $maxDiscountPercent;
        $product->best_discount_variant = $bestDiscountVariant;
        $product->has_product_discount = $hasProductDiscount;
        $product->product_discount_percent = $productDiscountPercent;

        $product->min_price = $minOriginalPrice;
        $product->max_price = $maxOriginalPrice;
        $product->min_effective_price = $minEffectivePrice;
        $product->max_effective_price = $maxEffectivePrice;

        // ============================================
        // 5. BUILD PRICE LABEL
        // ============================================
        if ($hasDiscount) {
            // Range harga DISKON
            if ($minEffectivePrice == $maxEffectivePrice) {
                $product->price_label = 'Rp ' . number_format($minEffectivePrice, 0, ',', '.');
            } else {
                $product->price_label = 'Rp ' . number_format($minEffectivePrice, 0, ',', '.') .
                                        ' - Rp ' . number_format($maxEffectivePrice, 0, ',', '.');
            }
            $product->discount_label = 'Diskon ' . round($maxDiscountPercent) . '%';
        } else {
            // Range harga NORMAL
            if ($minOriginalPrice == $maxOriginalPrice) {
                $product->price_label = 'Rp ' . number_format($minOriginalPrice, 0, ',', '.');
            } else {
                $product->price_label = 'Rp ' . number_format($minOriginalPrice, 0, ',', '.') .
                                        ' - Rp ' . number_format($maxOriginalPrice, 0, ',', '.');
            }
            $product->discount_label = '';
        }

        // ============================================
        // 6. ORIGINAL PRICE DISPLAY (untuk coret)
        // ============================================
        if ($hasDiscount) {
            if ($minOriginalPrice == $maxOriginalPrice) {
                $product->original_price_display = 'Rp ' . number_format($minOriginalPrice, 0, ',', '.');
            } else {
                $product->original_price_display = 'Rp ' . number_format($minOriginalPrice, 0, ',', '.') .
                                                    ' - Rp ' . number_format($maxOriginalPrice, 0, ',', '.');
            }
        } else {
            $product->original_price_display = null;
        }

        // ============================================
        // 7. BADGE
        // ============================================
        if ($hasDiscount && $maxDiscountPercent > 0) {
            $product->badge_label = 'Diskon ' . round($maxDiscountPercent) . '%';
            $product->badge_color = 'red';
        } else {
            $product->badge_label = null;
            $product->badge_color = null;
        }

        // ============================================
        // 8. DISPLAY PRICE (untuk blade)
        // ============================================
        $product->display_price = $product->price_label;

        return $product;
    }
}