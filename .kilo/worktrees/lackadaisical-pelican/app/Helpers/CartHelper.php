<?php

namespace App\Helpers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class CartHelper
{
    /**
     * Get cart items (from database if logged in, else from localStorage via cookie)
     */
    public static function getCart()
    {
        $user = Auth::user();

        if ($user) {
            // User logged in - get from database
            $cartItems = Cart::with(['product.images', 'variant'])
                ->where('user_id', $user->id)
                ->get();

            $cart = [];
            foreach ($cartItems as $item) {
                $variant = $item->variant;
                $product = $item->product;

                $price = $variant ? 
                    ($variant->discount_price ?? $variant->price) : 
                    $product->price;

                $cart[$item->id] = [
                    'id' => $item->id,
                    'product_id' => $product->id,
                    'variant_id' => $variant?->id,
                    'product_name' => $product->name,
                    'variant_name' => $variant ? self::getVariantCombination($variant) : null,
                    'price' => $price,
                    'original_price' => $variant?->price ?? $product->price,
                    'quantity' => $item->quantity,
                    'image' => self::getVariantImage($variant, $product),
                    'slug' => $product->slug,
                    'weight' => $variant?->weight ?? $product->weight ?? 1000,
                    'in_cart' => true,
                ];
            }

            return $cart;
        }

        // Guest user - get from localStorage (via cookie)
        $cartData = Cookie::get('guest_cart');
        if ($cartData) {
            $cart = json_decode($cartData, true);
            return $cart ?: [];
        }

        return [];
    }

    /**
     * Add item to cart
     */
    public static function addToCart($productId, $variantId = null, $quantity = 1)
    {
        $user = Auth::user();

        if ($user) {
            // Save to database
            $cartItem = Cart::where('user_id', $user->id)
                ->where('product_id', $productId)
                ->where('variant_id', $variantId)
                ->first();

            if ($cartItem) {
                $cartItem->quantity += $quantity;
                $cartItem->save();
            } else {
                $cartItem = Cart::create([
                    'user_id' => $user->id,
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'quantity' => $quantity,
                ]);
            }

            return [
                'success' => true,
                'count' => Cart::where('user_id', $user->id)->count(),
                'item_id' => $cartItem->id,
                'message' => 'Produk ditambahkan ke keranjang',
            ];
        }

        // Guest user - save to localStorage (via cookie)
        $cartData = Cookie::get('guest_cart');
        $cart = $cartData ? json_decode($cartData, true) : [];

        $key = $productId . '-' . ($variantId ?? '0');

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $quantity;
        } else {
            // Get product details
            $product = Product::with(['images', 'variants', 'options.values'])->find($productId);
            if (!$product) {
                return ['success' => false, 'message' => 'Produk tidak ditemukan'];
            }

            $variant = null;
            $price = 0;
            $originalPrice = 0;
            $variantName = null;
            $weight = 1000;
            $variantImage = null;

            if ($variantId) {
                $variant = ProductVariant::with('values')->find($variantId);
                if ($variant) {
                    $price = $variant->discount_price ?? $variant->price;
                    $originalPrice = $variant->price;
                    $variantName = self::getVariantCombination($variant);
                    $weight = $variant->weight ?? 1000;
                    $variantImage = self::getVariantImage($variant, $product);
                }
            } else {
                $firstVariant = $product->variants->first();
                if ($firstVariant) {
                    $price = $firstVariant->discount_price ?? $firstVariant->price;
                    $originalPrice = $firstVariant->price;
                    $weight = $firstVariant->weight ?? 1000;
                    $variantImage = self::getVariantImage($firstVariant, $product);
                }
            }

            if (!$variantImage) {
                $variantImage = $product->images->first()?->image;
            }

            $cart[$key] = [
                'product_id' => $productId,
                'product_name' => $product->name,
                'variant_id' => $variantId,
                'variant_name' => $variantName,
                'price' => $price,
                'original_price' => $originalPrice,
                'quantity' => $quantity,
                'image' => $variantImage,
                'slug' => $product->slug,
                'weight' => $weight,
            ];
        }

        // Save to cookie (expires in 30 days)
        Cookie::queue('guest_cart', json_encode($cart), 60 * 24 * 30);

        $count = array_sum(array_column($cart, 'quantity'));

        return [
            'success' => true,
            'count' => $count,
            'message' => 'Produk ditambahkan ke keranjang',
        ];
    }

    /**
     * Remove item from cart
     */
    public static function removeFromCart($key)
    {
        $user = Auth::user();

        if ($user) {
            // Remove from database
            $cartItem = Cart::where('id', $key)->where('user_id', $user->id)->first();
            if ($cartItem) {
                $cartItem->delete();
                return [
                    'success' => true,
                    'count' => Cart::where('user_id', $user->id)->count(),
                    'message' => 'Item dihapus dari keranjang',
                ];
            }
            return ['success' => false, 'message' => 'Item tidak ditemukan'];
        }

        // Guest user - remove from cookie
        $cartData = Cookie::get('guest_cart');
        if (!$cartData) {
            return ['success' => false, 'message' => 'Keranjang kosong'];
        }

        $cart = json_decode($cartData, true);
        if (isset($cart[$key])) {
            unset($cart[$key]);
            Cookie::queue('guest_cart', json_encode($cart), 60 * 24 * 30);
            $count = array_sum(array_column($cart, 'quantity'));
            return [
                'success' => true,
                'count' => $count,
                'message' => 'Item dihapus dari keranjang',
            ];
        }

        return ['success' => false, 'message' => 'Item tidak ditemukan'];
    }

    /**
     * Update cart item quantity
     */
    public static function updateCartQuantity($key, $quantity)
    {
        $user = Auth::user();

        if ($user) {
            // Update in database
            $cartItem = Cart::where('id', $key)->where('user_id', $user->id)->first();
            if ($cartItem) {
                $cartItem->quantity = $quantity;
                $cartItem->save();
                return [
                    'success' => true,
                    'message' => 'Keranjang diperbarui',
                ];
            }
            return ['success' => false, 'message' => 'Item tidak ditemukan'];
        }

        // Guest user - update in cookie
        $cartData = Cookie::get('guest_cart');
        if (!$cartData) {
            return ['success' => false, 'message' => 'Keranjang kosong'];
        }

        $cart = json_decode($cartData, true);
        if (isset($cart[$key])) {
            $cart[$key]['quantity'] = $quantity;
            Cookie::queue('guest_cart', json_encode($cart), 60 * 24 * 30);
            return [
                'success' => true,
                'message' => 'Keranjang diperbarui',
            ];
        }

        return ['success' => false, 'message' => 'Item tidak ditemukan'];
    }

    /**
     * Clear cart
     */
    public static function clearCart()
    {
        $user = Auth::user();

        if ($user) {
            Cart::where('user_id', $user->id)->delete();
            return [
                'success' => true,
                'count' => 0,
                'message' => 'Keranjang dikosongkan',
            ];
        }

        Cookie::queue('guest_cart', '', -1);
        return [
            'success' => true,
            'count' => 0,
            'message' => 'Keranjang dikosongkan',
        ];
    }

    /**
     * Get cart count
     */
    public static function getCartCount()
    {
        $user = Auth::user();

        if ($user) {
            return Cart::where('user_id', $user->id)->count();
        }

        $cartData = Cookie::get('guest_cart');
        if ($cartData) {
            $cart = json_decode($cartData, true);
            return array_sum(array_column($cart, 'quantity'));
        }

        return 0;
    }

    /**
     * Helper functions
     */
    private static function getVariantCombination($variant)
    {
        if ($variant->relationLoaded('values')) {
            return $variant->values->pluck('value')->implode(' / ');
        }
        return $variant->variantValues->pluck('optionValue.value')->implode(' / ');
    }

    private static function getVariantImage($variant, $product)
    {
        if (!$variant) return null;

        // Check if variant has image directly
        if ($variant->image) {
            return $variant->image;
        }

        // Check from option values (warna)
        $variantValueIds = $variant->variantValues->pluck('product_option_value_id')->toArray();
        foreach ($product->options as $option) {
            if (strtolower($option->name) === 'warna' || strtolower($option->name) === 'color') {
                foreach ($option->values as $value) {
                    if (in_array($value->id, $variantValueIds) && $value->image) {
                        return $value->image;
                    }
                }
            }
        }

        return $product->images->first()?->image;
    }
}