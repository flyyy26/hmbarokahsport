<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Traits\ProductDiscountTrait; 

class WishlistController extends Controller
{
    use ProductDiscountTrait;
    // ============================================
    // INDEX - Tampilkan Wishlist
    // ============================================

    public function index()
    {
        $user = Auth::guard('customer')->user();

        if (!$user) {
            return redirect()->route('customer.login');
        }

        $wishlist = Wishlist::with(['product.images', 'product.variants', 'product.category'])
            ->where('user_id', $user->id)
            ->get();

        $products = $wishlist->pluck('product');

        // 🔥 TAMBAHKAN DATA DISKON KE SETIAP PRODUK
        foreach ($products as $product) {
            if (!$product->relationLoaded('variants')) {
                $product->load('variants');
            }
            $this->attachDiscountData($product);
        }

        return view('customer.wishlist.index', compact('products'));
    }

    // ============================================
    // POPUP - Tampilkan Popup Wishlist (AJAX)
    // ============================================

    public function popup(Request $request)
    {
        $user = Auth::guard('customer')->user();

        if (!$user) {
            return response()->json([
                'success' => true,
                'html' => view('customer.wishlist.popup', ['products' => collect()])->render(),
                'count' => 0,
                'wishlist_ids' => [],
            ]);
        }

        $wishlist = Wishlist::with(['product.images', 'product.variants', 'product.category'])
            ->where('user_id', $user->id)
            ->get();

        $products = $wishlist->pluck('product');

        // 🔥 TAMBAHKAN DATA DISKON KE SETIAP PRODUK
        foreach ($products as $product) {
            if (!$product->relationLoaded('variants')) {
                $product->load('variants');
            }
            $this->attachDiscountData($product);
        }

        $count = $products->count();
        $wishlistIds = $wishlist->pluck('product_id')->toArray();

        if ($request->ajax() || $request->wantsJson()) {
            $html = view('customer.wishlist.popup', compact('products'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'count' => $count,
                'wishlist_ids' => $wishlistIds,
            ]);
        }

        return redirect()->route('customer.wishlist.index');
    }

    // ============================================
    // ADD - Tambah ke Wishlist
    // ============================================

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $user = Auth::guard('customer')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu',
                'redirect' => route('customer.login')
            ], 401);
        }

        $productId = $request->product_id;

        $exists = Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->exists();

        if ($exists) {
            Wishlist::where('user_id', $user->id)
                ->where('product_id', $productId)
                ->delete();

            $inWishlist = false;
            $message = 'Produk dihapus dari wishlist.';
        } else {
            Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $productId,
            ]);

            $inWishlist = true;
            $message = 'Produk ditambahkan ke wishlist!';
        }

        $count = Wishlist::where('user_id', $user->id)->count();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $count,
                'in_wishlist' => $inWishlist,
                'product_id' => (int) $productId,
            ]);
        }

        return back()->with('success', $message);
    }

    // ============================================
    // REMOVE - Hapus dari Wishlist
    // ============================================

    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $user = Auth::guard('customer')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu',
            ], 401);
        }

        $deleted = Wishlist::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->delete();

        $count = Wishlist::where('user_id', $user->id)->count();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $deleted ? 'Produk dihapus dari wishlist.' : 'Produk tidak ditemukan.',
                'count' => $count,
                'product_id' => (int) $request->product_id,
            ]);
        }

        return back()->with('success', 'Produk dihapus dari wishlist.');
    }

    // ============================================
    // CLEAR - Kosongkan Wishlist
    // ============================================

    public function clear(Request $request)
    {
        $user = Auth::guard('customer')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu',
            ], 401);
        }

        Wishlist::where('user_id', $user->id)->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Wishlist berhasil dikosongkan.',
                'count' => 0,
            ]);
        }

        return redirect()
            ->route('customer.wishlist.index')
            ->with('success', 'Wishlist berhasil dikosongkan.');
    }

    // ============================================
    // STATUS - Cek Status Wishlist
    // ============================================

    public function status(Request $request)
    {
        $user = Auth::guard('customer')->user();

        if (!$user) {
            return response()->json([
                'success' => true,
                'wishlist_ids' => [],
                'is_logged_in' => false,
            ]);
        }

        $wishlistIds = Wishlist::where('user_id', $user->id)
            ->pluck('product_id')
            ->toArray();

        return response()->json([
            'success' => true,
            'wishlist_ids' => $wishlistIds,
            'is_logged_in' => true,
        ]);
    }
}