<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Traits\ProductDiscountTrait;

class CartController extends Controller
{
    use ProductDiscountTrait;

    // ============================================
    // INDEX - Tampilkan Keranjang
    // ============================================

    public function index()
    {
        $user = Auth::guard('customer')->user();

        if (!$user) {
            return redirect()->route('customer.login');
        }

        $cartItems = Cart::with(['product.images', 'variant'])
            ->where('user_id', $user->id)
            ->get();

        $cart = [];
        $subtotal = 0;

        foreach ($cartItems as $item) {
            $variant = $item->variant;
            $product = $item->product;

            $price = $this->getEffectivePrice($variant, $product);
            $subtotal += $price * $item->quantity;

            $variantImage = null;
            if ($variant) {
                $variantImage = $this->getVariantImage($variant, $product);
            }
            if (!$variantImage) {
                $variantImage = $product->images->first()?->image;
            }

            // 🔥 PASTIKAN KEY ADALAH ID CART
            $cart[] = [
                'id' => $item->id, // 🔥 INI ADALAH KEY
                'cart_id' => $item->id,
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'product_name' => $product->name,
                'variant_name' => $variant ? $variant->option_combination : null,
                'price' => $price,
                'original_price' => $variant?->price ?? $product->price,
                'quantity' => $item->quantity,
                'image' => $variantImage,
                'slug' => $product->slug,
                'weight' => $variant?->weight ?? $product->weight ?? 1000,
            ];
        }

        session()->put('cart', $cart);

        return view('customer.cart.index', compact('cart', 'subtotal'));
    }

    // ============================================
    // POPUP - Tampilkan Popup Keranjang (AJAX)
    // ============================================

    public function popup(Request $request)
    {
        $user = Auth::guard('customer')->user();

        if (!$user) {
            return response()->json([
                'success' => true,
                'html' => view('customer.cart.popup', ['cart' => []])->render(),
                'total' => 0,
                'count' => 0,
            ]);
        }

        $cartItems = Cart::with(['product.images', 'variant'])
            ->where('user_id', $user->id)
            ->get();

        $cart = [];
        $subtotal = 0;

        foreach ($cartItems as $item) {
            $variant = $item->variant;
            $product = $item->product;

            $price = $this->getEffectivePrice($variant, $product);
            $originalPrice = $variant?->price ?? $product->price;
            $hasDiscount = $price < $originalPrice;
            
            $hasProductDiscount = $product->isOnProductDiscount();
            $productDiscountPercent = 0;
            if ($hasProductDiscount) {
                $productDiscountPercent = $product->getProductDiscountPercent($originalPrice);
            }

            $subtotal += $price * $item->quantity;

            $variantImage = null;
            if ($variant) {
                $variantImage = $this->getVariantImage($variant, $product);
            }
            if (!$variantImage) {
                $variantImage = $product->images->first()?->image;
            }

            $cart[] = [
                'id' => $item->id,
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'product_name' => $product->name,
                'variant_name' => $variant ? $variant->option_combination : null,
                'price' => $price,
                'original_price' => $originalPrice,
                'has_discount' => $hasDiscount,
                'discount_percent' => $hasDiscount ? round((($originalPrice - $price) / $originalPrice) * 100) : 0,
                'has_product_discount' => $hasProductDiscount,
                'product_discount_percent' => $productDiscountPercent,
                'quantity' => $item->quantity,
                'image' => $variantImage,
                'slug' => $product->slug,
                'weight' => $variant?->weight ?? $product->weight ?? 1000,
            ];
        }

        $count = $cartItems->count();

        if ($request->ajax() || $request->wantsJson()) {
            $html = view('customer.cart.popup', compact('cart', 'subtotal'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'total' => $subtotal,
                'count' => $count,
            ]);
        }

        return redirect()->route('customer.cart.index');
    }

    // ============================================
    // ADD - Tambah ke Keranjang
    // ============================================

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::guard('customer')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu',
                'redirect' => route('customer.login')
            ], 401);
        }

        $product = Product::with(['variants', 'images', 'options.values'])->findOrFail($validated['product_id']);
        $variantId = $validated['variant_id'];
        $quantity = $validated['quantity'];

        // Cek stok
        if ($variantId) {
            $variant = ProductVariant::find($variantId);
            if ($variant && $variant->stock < $quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi. Tersedia: ' . $variant->stock,
                ], 400);
            }
        } else {
            $firstVariant = $product->variants->first();
            if ($firstVariant && $firstVariant->stock < $quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi. Tersedia: ' . $firstVariant->stock,
                ], 400);
            }
        }

        // Cek apakah item sudah ada di cart
        $cartItem = Cart::where('user_id', $user->id)
            ->where('product_id', $validated['product_id'])
            ->where('variant_id', $variantId)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            Cart::create([
                'user_id' => $user->id,
                'product_id' => $validated['product_id'],
                'variant_id' => $variantId,
                'quantity' => $quantity,
            ]);
        }

        $this->syncCartSession($user->id);

        $count = Cart::where('user_id', $user->id)->count();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke keranjang.',
                'count' => $count,
            ]);
        }

        return redirect()
            ->route('customer.cart.index')
            ->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    // ============================================
    // 🔥 BUY NOW - Direct Checkout
    // ============================================

    public function buyNow(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::with(['images', 'variants'])->findOrFail($request->product_id);
        $variant = ProductVariant::with(['variantValues.optionValue'])->findOrFail($request->variant_id);

        // 🔥 HITUNG HARGA EFEKTIF
        $effectivePrice = $variant->effective_price;

        // 🔥 BACKUP CART SEBELUMNYA
        $oldCart = session()->get('cart', []);
        session()->put('old_cart_backup', $oldCart);
        session()->forget('cart');

        // 🔥 BUILD VARIAN NAME
        $variantName = $variant->variantValues->map(function($vv) {
            return $vv->optionValue->value ?? '';
        })->filter()->implode(' / ');

        // 🔥 GET PRODUCT IMAGE
        $imageUrl = null;
        if ($product->images->first()) {
            $imageUrl = $product->images->first()->image;
        }

        // 🔥 CREATE BUY NOW ITEMS
        $buyNowItems = [
            [
                'product_id' => $product->id,
                'variant_id' => $variant->id,
                'product_name' => $product->name,
                'variant_name' => $variantName,
                'price' => $effectivePrice,
                'original_price' => $variant->price,
                'quantity' => $request->quantity,
                'weight' => $variant->weight ?? 1000,
                'image' => $imageUrl,
                'slug' => $product->slug,
            ]
        ];

        session()->put('cart', $buyNowItems);
        session()->put('is_buy_now', true);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route('customer.checkout.index'),
                'message' => 'Mengarahkan ke checkout...'
            ]);
        }

        return redirect()->route('customer.checkout.index')
            ->with('success', 'Silakan lanjutkan ke checkout.');
    }

    // ============================================
    // UPDATE - Update Quantity
    // ============================================

    public function update(Request $request)
    {
        // 🔥 PERBAIKI VALIDASI - Key adalah ID cart
        $validated = $request->validate([
            'key' => 'required|integer|exists:carts,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::guard('customer')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu',
            ], 401);
        }

        // 🔥 CARI CART ITEM BERDASARKAN ID
        $cartItem = Cart::where('id', $validated['key'])
            ->where('user_id', $user->id)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan.',
            ], 404);
        }

        // 🔥 CEK STOK
        if ($cartItem->variant_id) {
            $variant = ProductVariant::find($cartItem->variant_id);
            if ($variant && $variant->stock < $validated['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi. Tersedia: ' . $variant->stock,
                ], 400);
            }
        }

        $cartItem->quantity = $validated['quantity'];
        $cartItem->save();

        $this->syncCartSession($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil diperbarui.',
            'quantity' => $cartItem->quantity,
            'subtotal' => $cartItem->quantity * $cartItem->product->price,
        ]);
    }

    // ============================================
    // REMOVE - Hapus Item
    // ============================================

    public function remove(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|integer|exists:carts,id',
        ]);

        $user = Auth::guard('customer')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu',
            ], 401);
        }

        $cartItem = Cart::where('id', $validated['key'])
            ->where('user_id', $user->id)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan.',
            ], 404);
        }

        $cartItem->delete();

        $this->syncCartSession($user->id);

        $count = Cart::where('user_id', $user->id)->count();

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil dihapus.',
            'count' => $count,
        ]);
    }

    // ============================================
    // CLEAR - Kosongkan Keranjang
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

        Cart::where('user_id', $user->id)->delete();

        session()->forget('cart');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Keranjang berhasil dikosongkan.',
                'count' => 0,
            ]);
        }

        return redirect()
            ->route('customer.cart.index')
            ->with('success', 'Keranjang berhasil dikosongkan.');
    }

    // ============================================
    // COUNT - Jumlah Item di Keranjang
    // ============================================

    public function count()
    {
        $user = Auth::guard('customer')->user();

        if (!$user) {
            return response()->json(['count' => 0, 'is_logged_in' => false]);
        }

        $count = Cart::where('user_id', $user->id)->count();

        return response()->json(['count' => $count, 'is_logged_in' => true]);
    }

    // ============================================
    // SYNC CART SESSION
    // ============================================

    private function syncCartSession($userId)
    {
        $cartItems = Cart::with(['product.images', 'variant'])
            ->where('user_id', $userId)
            ->get();

        $cart = [];
        foreach ($cartItems as $item) {
            $variant = $item->variant;
            $product = $item->product;

            $price = $this->getEffectivePrice($variant, $product);
            $originalPrice = $variant?->price ?? $product->price;
            $hasDiscount = $price < $originalPrice;
            
            $hasProductDiscount = $product->isOnProductDiscount();
            $productDiscountPercent = 0;
            if ($hasProductDiscount) {
                $productDiscountPercent = $product->getProductDiscountPercent($originalPrice);
            }

            $variantImage = null;
            if ($variant) {
                $variantImage = $this->getVariantImage($variant, $product);
            }
            if (!$variantImage) {
                $variantImage = $product->images->first()?->image;
            }

            $cart[] = [
                'id' => $item->id,
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'product_name' => $product->name,
                'variant_name' => $variant ? $variant->option_combination : null,
                'price' => $price,
                'original_price' => $originalPrice,
                'has_discount' => $hasDiscount,
                'discount_percent' => $hasDiscount ? round((($originalPrice - $price) / $originalPrice) * 100) : 0,
                'has_product_discount' => $hasProductDiscount,
                'product_discount_percent' => $productDiscountPercent,
                'quantity' => $item->quantity,
                'image' => $variantImage,
                'slug' => $product->slug,
                'weight' => $variant?->weight ?? $product->weight ?? 1000,
            ];
        }

        session()->put('cart', $cart);
    }

    // ============================================
    // HELPER - Get Effective Price
    // ============================================

    private function getEffectivePrice($variant, $product)
    {
        if ($variant) {
            return $variant->effective_price;
        }
        return $product->price;
    }

    // ============================================
    // HELPER - Get Variant Image
    // ============================================

    private function getVariantImage($variant, $product)
    {
        if (!$variant) return null;

        if ($variant->image) {
            return $variant->image;
        }

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