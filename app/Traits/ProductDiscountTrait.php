<?php

namespace App\Traits;

trait ProductDiscountTrait
{
    /**
     * 🔥 ATTACH DISCOUNT DATA (SINKRON DENGAN HOME CONTROLLER)
     */
    public function attachDiscountData($product)
    {
        if (!$product || !$product->relationLoaded('variants')) {
            return $product;
        }

        $variants = $product->variants;

        if ($variants->isEmpty()) {
            $product->has_discount = false;
            $product->max_discount_percent = 0;
            $product->min_effective_price = 0;
            $product->max_price = 0;
            $product->min_price = 0;
            $product->has_flash_sale = false;
            $product->badge_label = null;
            $product->price_label = '-';
            $product->original_price_label = null;
            $product->is_flash_sale_active = false;
            return $product;
        }

        // ============================================
        // FLASH SALE STATE
        // ============================================
        $isFlashSaleActive = false;
        $flashSalePercent = 0;

        if ($product->is_flash_sale && $product->flash_sale_value > 0) {
            $now = now();
            $isActive = true;
            if ($product->flash_sale_start_date && $now->lt($product->flash_sale_start_date)) {
                $isActive = false;
            }
            if ($product->flash_sale_end_date && $now->gt($product->flash_sale_end_date)) {
                $isActive = false;
            }
            $isFlashSaleActive = $isActive;

            if ($isActive && $product->flash_sale_type === 'percentage') {
                $flashSalePercent = (float) $product->flash_sale_value;
            }
        }

        // ============================================
        // HITUNG DISKON & HARGA
        // ============================================
        $maxDiscountPercent = 0;
        $minEffectivePrice = PHP_FLOAT_MAX;
        $maxEffectivePrice = 0;
        $minPrice = PHP_FLOAT_MAX;
        $maxPrice = 0;

        foreach ($variants as $variant) {
            $price = (float) $variant->price;
            $variantDiscount = 0;

            // Diskon varian
            if ($variant->discount_price && $price > 0 && $variant->discount_price < $price) {
                $variantDiscount = round((($price - $variant->discount_price) / $price) * 100);
            }

            $effectivePrice = $variant->discount_price ?? $price;

            // Flash sale override
            if ($isFlashSaleActive) {
                if ($product->flash_sale_type === 'percentage') {
                    $flashPrice = $price * (1 - $product->flash_sale_value / 100);
                    if ($flashPrice < $effectivePrice) {
                        $effectivePrice = $flashPrice;
                    }
                    if ($product->flash_sale_value > $maxDiscountPercent) {
                        $maxDiscountPercent = (float) $product->flash_sale_value;
                    }
                } elseif ($product->flash_sale_type === 'fixed') {
                    $flashPrice = max(0, $price - (float) $product->flash_sale_value);
                    if ($flashPrice < $effectivePrice) {
                        $effectivePrice = $flashPrice;
                    }
                    $fixedPercent = $price > 0 ? round((($price - $flashPrice) / $price) * 100) : 0;
                    if ($fixedPercent > $maxDiscountPercent) {
                        $maxDiscountPercent = $fixedPercent;
                    }
                }
            } elseif ($variantDiscount > $maxDiscountPercent) {
                $maxDiscountPercent = $variantDiscount;
            }

            if ($effectivePrice < $minEffectivePrice) $minEffectivePrice = $effectivePrice;
            if ($effectivePrice > $maxEffectivePrice) $maxEffectivePrice = $effectivePrice;
            if ($price < $minPrice) $minPrice = $price;
            if ($price > $maxPrice) $maxPrice = $price;
        }

        if ($minEffectivePrice === PHP_FLOAT_MAX) $minEffectivePrice = $minPrice;

        // ============================================
        // SET PROPERTIES (SAMA DENGAN HOME CONTROLLER)
        // ============================================
        $product->has_discount = $maxDiscountPercent > 0;
        $product->max_discount_percent = $maxDiscountPercent;
        $product->min_effective_price = $minEffectivePrice;
        $product->max_effective_price = $maxEffectivePrice;
        $product->min_price = $minPrice === PHP_FLOAT_MAX ? 0 : $minPrice;
        $product->max_price = $maxPrice;
        $product->has_flash_sale = $isFlashSaleActive;
        $product->is_flash_sale_active = $isFlashSaleActive;

        // ============================================
        // PRICE LABEL (untuk blade)
        // ============================================
        if ($product->has_discount) {
            if ($minEffectivePrice == $maxEffectivePrice) {
                $product->price_label = 'Rp ' . number_format($minEffectivePrice, 0, ',', '.');
            } else {
                $product->price_label = 'Rp ' . number_format($minEffectivePrice, 0, ',', '.') .
                                        ' - Rp ' . number_format($maxEffectivePrice, 0, ',', '.');
            }
        } else {
            if ($minPrice == $maxPrice) {
                $product->price_label = 'Rp ' . number_format($minPrice, 0, ',', '.');
            } else {
                $product->price_label = 'Rp ' . number_format($minPrice, 0, ',', '.') .
                                        ' - Rp ' . number_format($maxPrice, 0, ',', '.');
            }
        }

        // ============================================
        // ORIGINAL PRICE LABEL (untuk coret)
        // ============================================
        if ($product->has_discount) {
            if ($minPrice == $maxPrice) {
                $product->original_price_label = 'Rp ' . number_format($minPrice, 0, ',', '.');
            } else {
                $product->original_price_label = 'Rp ' . number_format($minPrice, 0, ',', '.') .
                                                 ' - Rp ' . number_format($maxPrice, 0, ',', '.');
            }
        } else {
            $product->original_price_label = null;
        }

        // ============================================
        // BADGE
        // ============================================
        if ($isFlashSaleActive) {
            $product->badge_label = '⚡ Flash Sale';
        } elseif ($product->has_discount && $maxDiscountPercent > 0) {
            $product->badge_label = 'Diskon ' . round($maxDiscountPercent) . '%';
        } elseif ($product->is_best_seller) {
            $product->badge_label = 'Terlaris';
        } elseif ($product->is_featured) {
            $product->badge_label = 'Unggulan';
        } else {
            $product->badge_label = null;
        }

        return $product;
    }
}