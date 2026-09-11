<?php

namespace App\Helpers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class WishlistHelper
{
    /**
     * Get wishlist items
     */
    public static function getWishlist()
    {
        $user = Auth::user();

        if ($user) {
            $wishlistIds = Wishlist::where('user_id', $user->id)
                ->pluck('product_id')
                ->toArray();

            if (empty($wishlistIds)) {
                return [];
            }

            $products = Product::with(['images', 'variants', 'category'])
                ->whereIn('id', $wishlistIds)
                ->where('is_active', true)
                ->get();

            $wishlist = [];
            foreach ($products as $product) {
                $wishlist[$product->id] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'slug' => $product->slug,
                    'category_name' => $product->category?->name,
                    'price' => $product->min_price,
                    'image' => $product->images->first()?->image,
                    'added_at' => now(),
                ];
            }

            return $wishlist;
        }

        // Guest user - get from cookie
        $wishlistData = Cookie::get('guest_wishlist');
        if ($wishlistData) {
            $wishlist = json_decode($wishlistData, true);
            return $wishlist ?: [];
        }

        return [];
    }

    /**
     * Toggle wishlist item
     */
    public static function toggleWishlist($productId)
    {
        $user = Auth::user();

        if ($user) {
            $exists = Wishlist::where('user_id', $user->id)
                ->where('product_id', $productId)
                ->exists();

            if ($exists) {
                Wishlist::where('user_id', $user->id)
                    ->where('product_id', $productId)
                    ->delete();
                $inWishlist = false;
                $message = 'Produk dihapus dari wishlist';
            } else {
                Wishlist::create([
                    'user_id' => $user->id,
                    'product_id' => $productId,
                ]);
                $inWishlist = true;
                $message = 'Produk ditambahkan ke wishlist';
            }

            $count = Wishlist::where('user_id', $user->id)->count();

            return [
                'success' => true,
                'in_wishlist' => $inWishlist,
                'count' => $count,
                'message' => $message,
            ];
        }

        // Guest user - toggle in cookie
        $wishlistData = Cookie::get('guest_wishlist');
        $wishlist = $wishlistData ? json_decode($wishlistData, true) : [];

        if (isset($wishlist[$productId])) {
            unset($wishlist[$productId]);
            $inWishlist = false;
            $message = 'Produk dihapus dari wishlist';
        } else {
            $product = Product::find($productId);
            if (!$product) {
                return ['success' => false, 'message' => 'Produk tidak ditemukan'];
            }

            $wishlist[$productId] = [
                'product_id' => $productId,
                'product_name' => $product->name,
                'slug' => $product->slug,
                'category_name' => $product->category?->name,
                'price' => $product->min_price,
                'image' => $product->images->first()?->image,
                'added_at' => now(),
            ];
            $inWishlist = true;
            $message = 'Produk ditambahkan ke wishlist';
        }

        Cookie::queue('guest_wishlist', json_encode($wishlist), 60 * 24 * 30);

        return [
            'success' => true,
            'in_wishlist' => $inWishlist,
            'count' => count($wishlist),
            'message' => $message,
        ];
    }

    /**
     * Remove from wishlist
     */
    public static function removeFromWishlist($productId)
    {
        $user = Auth::user();

        if ($user) {
            Wishlist::where('user_id', $user->id)
                ->where('product_id', $productId)
                ->delete();

            $count = Wishlist::where('user_id', $user->id)->count();

            return [
                'success' => true,
                'count' => $count,
                'message' => 'Produk dihapus dari wishlist',
            ];
        }

        $wishlistData = Cookie::get('guest_wishlist');
        if (!$wishlistData) {
            return ['success' => false, 'message' => 'Wishlist kosong'];
        }

        $wishlist = json_decode($wishlistData, true);
        if (isset($wishlist[$productId])) {
            unset($wishlist[$productId]);
            Cookie::queue('guest_wishlist', json_encode($wishlist), 60 * 24 * 30);
            return [
                'success' => true,
                'count' => count($wishlist),
                'message' => 'Produk dihapus dari wishlist',
            ];
        }

        return ['success' => false, 'message' => 'Produk tidak ditemukan'];
    }

    /**
     * Clear wishlist
     */
    public static function clearWishlist()
    {
        $user = Auth::user();

        if ($user) {
            Wishlist::where('user_id', $user->id)->delete();
            return [
                'success' => true,
                'count' => 0,
                'message' => 'Wishlist dikosongkan',
            ];
        }

        Cookie::queue('guest_wishlist', '', -1);
        return [
            'success' => true,
            'count' => 0,
            'message' => 'Wishlist dikosongkan',
        ];
    }

    /**
     * Get wishlist count
     */
    public static function getWishlistCount()
    {
        $user = Auth::user();

        if ($user) {
            return Wishlist::where('user_id', $user->id)->count();
        }

        $wishlistData = Cookie::get('guest_wishlist');
        if ($wishlistData) {
            $wishlist = json_decode($wishlistData, true);
            return count($wishlist);
        }

        return 0;
    }

    /**
     * Get wishlist IDs
     */
    public static function getWishlistIds()
    {
        $user = Auth::user();

        if ($user) {
            return Wishlist::where('user_id', $user->id)
                ->pluck('product_id')
                ->toArray();
        }

        $wishlistData = Cookie::get('guest_wishlist');
        if ($wishlistData) {
            $wishlist = json_decode($wishlistData, true);
            return array_keys($wishlist);
        }

        return [];
    }
}