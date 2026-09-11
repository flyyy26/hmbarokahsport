<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Cart;
use App\Models\Voucher;
use App\Models\Setting;
use App\Services\BiteshipService;
use App\Models\VoucherUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use App\Traits\ProductDiscountTrait;
use Midtrans\Config;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    use ProductDiscountTrait;

    protected $biteship;

    public function __construct(BiteshipService $biteship)
    {
        $this->biteship = $biteship;
    }

    // ============================================
    // HELPER - GET EFFECTIVE PRICE
    // ============================================

    private function getEffectivePrice($variant, $product)
    {
        if ($variant) {
            return $variant->effective_price;
        }
        return $product->price;
    }

    // ============================================
    // HELPER - GET SUBTOTAL
    // ============================================

    private function getSubtotalFromCart($cart): float
    {
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        return $subtotal;
    }

    private function getTotalWeightFromCart($cart): int
    {
        $weight = 0;
        foreach ($cart as $item) {
            $weight += ($item['weight'] ?? 1000) * $item['quantity'];
        }
        return $weight;
    }

    // ============================================
    // INDEX - Tampilkan Halaman Checkout
    // ============================================

    // ============================================
    // INDEX - Perbaikan Total
    // ============================================

    public function index()
{
    // 🔥 AMBIL CART DARI SESSION TAPI JANGAN SIMPAN KE SESSION PERMANEN
    $cart = session()->get('cart', []);
    $isBuyNow = session()->get('is_buy_now', false);

    if (empty($cart)) {
        $user = Auth::guard('customer')->user();
        if ($user) {
            $cartItems = Cart::with(['product.images', 'variant'])
                ->where('user_id', $user->id)
                ->get();

            if ($cartItems->isNotEmpty()) {
                $cart = [];
                foreach ($cartItems as $item) {
                    $variant = $item->variant;
                    $product = $item->product;

                    $price = $this->getEffectivePrice($variant, $product);

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
                        'original_price' => $variant?->price ?? $product->price,
                        'quantity' => $item->quantity,
                        'image' => $variantImage,
                        'slug' => $product->slug,
                        'weight' => $variant?->weight ?? $product->weight ?? 1000,
                        'stock' => $variant?->stock ?? 999,
                    ];
                }

                session()->put('cart', $cart);
            }
        }
    }

    if (empty($cart)) {
        if (session()->has('old_cart_backup') && !empty(session()->get('old_cart_backup'))) {
            session()->put('cart', session()->get('old_cart_backup'));
            session()->forget('old_cart_backup');
            session()->forget('is_buy_now');
            return redirect()->route('customer.cart.index');
        }

        return redirect()
            ->route('customer.cart.index')
            ->with('error', 'Keranjang belanja kosong.');
    }

    $subtotal = $this->getSubtotalFromCart($cart);
    $totalWeight = $this->getTotalWeightFromCart($cart);

    // Cek stok
    foreach ($cart as $key => $item) {
        if ($item['variant_id']) {
            $variant = ProductVariant::find($item['variant_id']);
            if (!$variant || $variant->stock < $item['quantity']) {
                if (session()->has('old_cart_backup') && !empty(session()->get('old_cart_backup'))) {
                    session()->put('cart', session()->get('old_cart_backup'));
                    session()->forget('old_cart_backup');
                    session()->forget('is_buy_now');
                }
                return redirect()
                    ->route('customer.cart.index')
                    ->with('error', "Stok {$item['product_name']} tidak mencukupi.");
            }
        }
    }

    // 🔥 RESET DATA CHECKOUT - TIDAK PAKAI SESSION
    $this->resetCheckoutData();

    // 🔥 SHIPPING COST RESET
    $shippingCost = 0;
    $hasCourierSelected = false;
    $selectedCourier = null;
    $selectedService = null;

    // 🔥 VOUCHER RESET
    $productDiscount = 0;
    $shippingDiscount = 0;
    $isFreeShipping = false;
    $totalVoucherDiscount = 0;
    $appliedVoucher = null;
    $isAutoApplied = false;

    $addresses = [];
    $customer = null;
    $defaultAddress = null;

    if (Auth::guard('customer')->check()) {
        $customer = Auth::guard('customer')->user();
        $addresses = $customer->addresses()->orderBy('is_default', 'desc')->get();
        $defaultAddress = $customer->addresses()->where('is_default', true)->first();
    }

    return view('customer.checkout.index', compact(
        'cart',
        'subtotal',
        'customer',
        'addresses',
        'defaultAddress',
        'isBuyNow',
        'appliedVoucher',
        'totalVoucherDiscount',
        'productDiscount',
        'shippingDiscount',
        'isFreeShipping',
        'shippingCost',
        'totalWeight',
        'isAutoApplied',
        'hasCourierSelected',
        'selectedCourier',
        'selectedService'
    ));
}

private function resetCheckoutData(): void
{
    // 🔥 HAPUS SEMUA DATA CHECKOUT DARI SESSION
    session()->forget([
        'shipping_cost',
        'original_shipping_cost',
        'selected_courier',
        'selected_service',
        'voucher_code',
        'voucher_discount',
        'voucher_type',
        'voucher_product_discount',
        'voucher_shipping_discount',
        'voucher_is_free_shipping',
        'voucher_auto_applied',
        'checkout_data', // data guest
        'checkout_address', // data alamat sementara
    ]);
}


    private function recalculateVoucherAfterShipping(): void
{
    $cart = session()->get('cart', []);
    if (empty($cart)) {
        return;
    }

    $subtotal = $this->getSubtotalFromCart($cart);
    $shippingCost = (int) session()->get('shipping_cost', 0);
    $selectedCourier = session()->get('selected_courier');
    $selectedService = session()->get('selected_service');

    // 🔥 CEK APAKAH KURIR SUDAH DIPILIH
    if (empty($selectedCourier) || empty($selectedService) || $shippingCost <= 0) {
        // Jika kurir belum dipilih atau shipping cost 0, hapus voucher ongkir
        if (session()->has('voucher_code')) {
            $voucher = Voucher::where('code', session('voucher_code'))->first();
            if ($voucher && ($voucher->discount_target === 'shipping' || $voucher->is_free_shipping)) {
                $this->clearVoucherSession();
            }
        }
        return;
    }

    // 🔥 RE-CALCULATE VOUCHER ONGKIR
    $voucherCode = session()->get('voucher_code');
    if ($voucherCode) {
        $voucher = Voucher::where('code', $voucherCode)->first();
        if ($voucher && ($voucher->discount_target === 'shipping' || $voucher->is_free_shipping)) {
            $userId = Auth::guard('customer')->id();
            $eligibility = $voucher->checkEligibility($subtotal, $userId, $shippingCost, $selectedCourier);

            if ($eligibility['eligible'] && $voucher->isCourierApplicable($selectedCourier)) {
                // Update diskon ongkir
                $shippingDiscount = 0;
                $isFreeShipping = false;

                if ($voucher->is_free_shipping) {
                    $shippingDiscount = $shippingCost;
                    $isFreeShipping = true;
                } else {
                    $shippingDiscount = $voucher->calculateShippingDiscount($shippingCost);
                }

                $productDiscount = session()->get('voucher_product_discount', 0);
                $totalDiscount = $productDiscount + $shippingDiscount;

                session()->put('voucher_discount', $totalDiscount);
                session()->put('voucher_shipping_discount', $shippingDiscount);
                session()->put('voucher_is_free_shipping', $isFreeShipping);
            } else {
                $this->clearVoucherSession();
            }
        }
    }
}

    // ============================================
    // 🔥 UPDATE CART ITEM - Perbaikan Lengkap
    // ============================================

    public function updateCartItem(Request $request)
{
    try {
        $validated = $request->validate([
            'item_key' => 'required|string',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = session()->get('cart', []);
        $itemKey = $validated['item_key'];
        $newQuantity = (int) $validated['quantity'];

        if (!isset($cart[$itemKey])) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan di keranjang.'
            ], 404);
        }

        $item = $cart[$itemKey];

        if ($item['variant_id']) {
            $variant = ProductVariant::find($item['variant_id']);
            if (!$variant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Varian produk tidak ditemukan.'
                ], 404);
            }
            if ($variant->stock < $newQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi. Tersedia: ' . ($variant->stock ?? 0),
                    'available_stock' => $variant->stock ?? 0
                ], 400);
            }
        }

        $variantStock = $item['variant_id'] ? ProductVariant::find($item['variant_id'])->stock ?? 999 : 999;

        // Update kuantitas item
        $cart[$itemKey]['quantity'] = $newQuantity;
        session()->put('cart', $cart);

        // 🔥 RESET SEMUA DATA CHECKOUT
        $this->resetCheckoutData();

        $hasCourierSelected = false;
        $shippingCost = 0;
        $subtotal = $this->getSubtotalFromCart($cart);

        // 🔥 HAPUS VOUCHER
        $voucherDiscount = 0;
        $productDiscount = 0;
        $shippingDiscount = 0;
        $isFreeShipping = false;
        $voucherCode = null;

        $total = $subtotal + $shippingCost - $voucherDiscount;
        $itemSubtotal = $item['price'] * $newQuantity;

        return response()->json([
            'success' => true,
            'message' => 'Kuantitas berhasil diperbarui.',
            'data' => [
                'quantity' => $newQuantity,
                'item_subtotal' => $itemSubtotal,
                'item_subtotal_formatted' => 'Rp ' . number_format($itemSubtotal, 0, ',', '.'),
                'subtotal' => $subtotal,
                'subtotal_formatted' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
                'shipping_cost' => $shippingCost,
                'shipping_cost_formatted' => 'Belum dipilih',
                'voucher_discount' => $voucherDiscount,
                'voucher_discount_formatted' => 'Rp 0',
                'total' => $total,
                'total_formatted' => 'Rp ' . number_format($total, 0, ',', '.'),
                'cart_empty' => empty($cart),
                'has_voucher' => false,
                'voucher_code' => null,
                'product_discount' => $productDiscount,
                'shipping_discount' => $shippingDiscount,
                'is_free_shipping' => $isFreeShipping,
                'has_courier_selected' => $hasCourierSelected,
                'available_stock' => $variantStock
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal memperbarui kuantitas: ' . $e->getMessage()
        ], 500);
    }
}


    // ============================================
    // 🔥 REMOVE CART ITEM
    // ============================================

    public function removeCartItem(Request $request)
{
    try {
        $validated = $request->validate([
            'item_key' => 'required|string'
        ]);

        $cart = session()->get('cart', []);
        $itemKey = $validated['item_key'];

        if (!isset($cart[$itemKey])) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan.'
            ], 404);
        }

        unset($cart[$itemKey]);
        $cart = array_values($cart);
        session()->put('cart', $cart);

        // 🔥 RESET SEMUA DATA CHECKOUT
        $this->resetCheckoutData();

        $hasCourierSelected = false;
        $shippingCost = 0;
        $subtotal = $this->getSubtotalFromCart($cart);

        $voucherDiscount = 0;
        $voucherCode = null;

        $total = $subtotal + $shippingCost - $voucherDiscount;

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil dihapus.',
            'data' => [
                'subtotal' => $subtotal,
                'subtotal_formatted' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
                'shipping_cost' => $shippingCost,
                'shipping_cost_formatted' => 'Belum dipilih',
                'voucher_discount' => $voucherDiscount,
                'voucher_discount_formatted' => 'Rp 0',
                'total' => $total,
                'total_formatted' => 'Rp ' . number_format($total, 0, ',', '.'),
                'cart_empty' => empty($cart),
                'has_voucher' => false,
                'voucher_code' => null,
                'has_courier_selected' => $hasCourierSelected
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal menghapus item: ' . $e->getMessage()
        ], 500);
    }
}


    // ============================================
    // CLEAR VOUCHER SESSION
    // ============================================

    private function clearVoucherSession(): void
    {
        // 🔥 KEMBALIKAN SHIPPING COST KE NILAI ASLI
        $hasShippingDiscount = session()->get('voucher_shipping_discount', 0) > 0 || session()->get('voucher_is_free_shipping', false);
        $originalShippingCost = session()->get('original_shipping_cost', 0);

        if ($hasShippingDiscount && $originalShippingCost > 0) {
            session()->put('shipping_cost', $originalShippingCost);
        }

        // 🔥 HAPUS SEMUA SESSION VOUCHER
        session()->forget('voucher_code');
        session()->forget('voucher_discount');
        session()->forget('voucher_type');
        session()->forget('voucher_product_discount');
        session()->forget('voucher_shipping_discount');
        session()->forget('voucher_is_free_shipping');
        session()->forget('voucher_auto_applied');

        // 🔥 JANGAN HAPUS original_shipping_cost, biarkan untuk digunakan nanti
    }

    // ============================================
    // FIND BEST VOUCHER
    // ============================================

    private function findBestVoucher(float $subtotal, bool $hasCourierSelected = false): ?Voucher
    {
        $userId = Auth::guard('customer')->id();
        $shippingCost = session()->get('shipping_cost', 0);

        $vouchers = Voucher::publicActive()->get();

        $bestVoucher = null;
        $bestDiscount = 0;

        foreach ($vouchers as $voucher) {
            // Cek eligibility
            $eligibility = $voucher->checkEligibility($subtotal, $userId);
            if (!$eligibility['eligible']) {
                continue;
            }

            // 🔥 CEK KHUSUS UNTUK VOUCHER ONGKIR
            if ($voucher->discount_target === 'shipping' || $voucher->is_free_shipping) {
                // Jika kurir belum dipilih, skip voucher ongkir
                if (!$hasCourierSelected) {
                    continue;
                }

                $selectedCourier = session()->get('selected_courier', 'JNE');
                if (!$voucher->isCourierApplicable($selectedCourier)) {
                    continue;
                }
            }

            // Hitung total diskon
            $discount = 0;
            if ($voucher->is_free_shipping) {
                $discount = $shippingCost;
            } elseif ($voucher->discount_target === 'shipping') {
                $discount = $voucher->calculateShippingDiscount($shippingCost);
            } else {
                $discount = $voucher->calculateDiscount($subtotal);
            }

            if ($discount > $bestDiscount) {
                $bestDiscount = $discount;
                $bestVoucher = $voucher;
            }
        }

        return $bestVoucher;
    }

    // ============================================
    // APPLY VOUCHER TO SESSION
    // ============================================

    private function applyVoucherToSession(Voucher $voucher, float $subtotal, bool $hasCourierSelected = false): void
    {
        $shippingCost = (int) session()->get('shipping_cost', 0);
        $productDiscount = 0;
        $shippingDiscount = 0;
        $isFreeShipping = false;

        // 🔥 SIMPAN ORIGINAL SHIPPING COST
        if (!session()->has('original_shipping_cost') || session()->get('original_shipping_cost') == 0) {
            session()->put('original_shipping_cost', $shippingCost);
        }

        if ($voucher->is_free_shipping) {
            if ($hasCourierSelected && $shippingCost > 0) {
                $shippingDiscount = $shippingCost;
                $isFreeShipping = true;
                session()->put('shipping_cost', 0);
            }
        } elseif ($voucher->discount_target === 'shipping') {
            if ($hasCourierSelected && $shippingCost > 0) {
                $shippingDiscount = $voucher->calculateShippingDiscount($shippingCost);
                $newShippingCost = max(0, $shippingCost - $shippingDiscount);
                session()->put('shipping_cost', $newShippingCost);
            }
        } else {
            // Voucher produk - tidak perlu cek kurir
            $productDiscount = $voucher->calculateDiscount($subtotal);
        }

        $totalDiscount = $productDiscount + $shippingDiscount;

        session()->put('voucher_code', $voucher->code);
        session()->put('voucher_discount', $totalDiscount);
        session()->put('voucher_type', $voucher->discount_target);
        session()->put('voucher_product_discount', $productDiscount);
        session()->put('voucher_shipping_discount', $shippingDiscount);
        session()->put('voucher_is_free_shipping', $isFreeShipping);
    }

    // ============================================
    // GET SHIPPING COST
    // ============================================

    public function getShippingCost(Request $request)
    {
        try {
            $validator = validator($request->all(), [
                'origin_postal_code' => 'required|string|min:4',
                'destination_postal_code' => 'required|string|min:4',
                'items' => 'required|array|min:1',
                'items.*.name' => 'required|string',
                'items.*.weight' => 'required|integer|min:1',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'couriers' => 'nullable|array',
                'couriers.*' => 'string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak lengkap.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $originPostalCode = (string) $request->input('origin_postal_code');
            $destinationPostalCode = (string) $request->input('destination_postal_code');

            $defaultCouriers = [
                'jne', 'jnt', 'sicepat', 'pos', 'anteraja',
                'lion', 'ninja', 'rpx', 'pahala', 'wahana',
                'tiki', 'ncs', 'first', 'idexpress', 'star',
            ];

            $couriers = $request->input('couriers', $defaultCouriers);

            $items = collect($request->input('items'))
                ->map(function ($item) {
                    return [
                        'name' => $item['name'] ?? 'Product',
                        'value' => (int) ($item['price'] ?? 0),
                        'weight' => max(1, (int) ($item['weight'] ?? 1000)),
                        'quantity' => max(1, (int) ($item['quantity'] ?? 1)),
                    ];
                })
                ->values()
                ->all();

            $data = $this->biteship->getShippingRates(
                $originPostalCode,
                $destinationPostalCode,
                $items,
                $couriers
            );

            $grouped = [];

            foreach (($data['pricing'] ?? []) as $rate) {
                $courierCode = $rate['courier_code'] ?? $rate['company'] ?? null;

                if (!$courierCode) {
                    continue;
                }

                $courierName = $rate['courier_name'] ?? $rate['company'] ?? strtoupper($courierCode);

                $cost = (int) ($rate['price'] ?? $rate['shipping_fee'] ?? 0);

                if ($cost <= 0) {
                    continue;
                }

                if (!isset($grouped[$courierCode])) {
                    $grouped[$courierCode] = [
                        'code' => $courierCode,
                        'name' => $courierName,
                        'services' => [],
                    ];
                }

                $serviceName = $rate['courier_service_name']
                    ?? $rate['service_name']
                    ?? $rate['type']
                    ?? $rate['courier_service_code']
                    ?? 'Reguler';

                $serviceCode = $rate['courier_service_code'] ?? $rate['type'] ?? $rate['service_code'] ?? 'regular';

                $exists = false;
                foreach ($grouped[$courierCode]['services'] as $existing) {
                    if ($existing['service'] === $serviceCode) {
                        $exists = true;
                        break;
                    }
                }

                if ($exists) {
                    continue;
                }

                $duration = $rate['duration'] ?? $rate['shipment_duration_range'] ?? '-';

                $grouped[$courierCode]['services'][] = [
                    'service' => $serviceCode,
                    'name' => $serviceName,
                    'description' => $rate['description'] ?? '',
                    'cost' => $cost,
                    'etd' => $duration,
                    'duration' => $duration,
                ];
            }

            foreach ($grouped as &$courier) {
                usort(
                    $courier['services'],
                    fn ($a, $b) => $a['cost'] <=> $b['cost']
                );
            }

            unset($courier);

            $result = array_values(
                array_filter(
                    $grouped,
                    fn ($courier) => !empty($courier['services'])
                )
            );

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Berhasil mendapatkan ongkir.',
                'origin_postal_code' => $originPostalCode,
                'destination_postal_code' => $destinationPostalCode,
                'debug' => [
                    'pricing_count' => count($data['pricing'] ?? []),
                    'couriers_sent' => $couriers,
                    'couriers_found' => count($result),
                ],
            ]);

        } catch (\Throwable $e) {
            \Log::error('BITESHIP RATES ERROR', [
                'message' => $e->getMessage(),
                'request' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // ============================================
    // SEARCH LOCATION
    // ============================================

    public function searchLocation(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
        ]);

        $apiKey = config('services.biteship.api_key');

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Biteship API Key tidak ditemukan.'
            ], 400);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->get('https://api.biteship.com/v1/maps/areas', [
                'q' => $request->query,
                'limit' => 10,
            ]);

            $data = $response->json();

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => $data['error'] ?? 'Gagal mencari lokasi.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => $data['areas'] ?? [],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============================================
    // APPLY VOUCHER
    // ============================================

    public function applyVoucher(Request $request)
{
    try {
        \Log::info('Apply Voucher Request:', $request->all());

        $validator = validator($request->all(), [
            'voucher_code' => 'required|string|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Kode voucher harus diisi.'
            ], 400);
        }

        $voucherCode = strtoupper(trim($request->input('voucher_code')));
        $voucher = Voucher::where('code', $voucherCode)->first();

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Kode voucher "' . $voucherCode . '" tidak ditemukan.'
            ], 404);
        }

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang belanja kosong.'
            ], 400);
        }

        $subtotal = $this->getSubtotalFromCart($cart);
        $userId = Auth::guard('customer')->id();

        $shippingCost = (int) $request->input('shipping_cost', 0);
        $selectedCourier = $request->input('courier', 'JNE');
        $hasCourierSelected = $shippingCost > 0;

        $isShippingVoucher = ($voucher->discount_target === 'shipping' || $voucher->is_free_shipping);

        $eligibility = $voucher->checkEligibility($subtotal, $userId, $shippingCost, $selectedCourier);

        if (!$eligibility['eligible']) {
            return response()->json([
                'success' => false,
                'message' => $eligibility['message']
            ], 400);
        }

        if ($isShippingVoucher && $hasCourierSelected) {
            if (!$voucher->isCourierApplicable($selectedCourier)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Voucher ongkir ini tidak berlaku untuk kurir yang dipilih.'
                ], 400);
            }
        }

        $productDiscount = 0;
        $shippingDiscount = 0;
        $isFreeShipping = false;
        $newShippingCost = $shippingCost;

        if ($voucher->is_free_shipping) {
            $shippingDiscount = $shippingCost;
            $isFreeShipping = true;
            $newShippingCost = 0;
        } elseif ($voucher->discount_target === 'shipping') {
            $shippingDiscount = $voucher->calculateShippingDiscount($shippingCost);
            $newShippingCost = max(0, $shippingCost - $shippingDiscount);
        } else {
            $productDiscount = $voucher->calculateDiscount($subtotal);
        }

        $totalDiscount = $productDiscount + $shippingDiscount;

        if ($totalDiscount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher tidak memberikan potongan untuk transaksi ini.'
            ], 400);
        }

        $total = $subtotal + $newShippingCost - $productDiscount;

        // 🔥 JANGAN SIMPAN KE SESSION PERMANEN
        // Kirim langsung ke response

        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil diterapkan!',
            'discount' => $totalDiscount,
            'product_discount' => $productDiscount,
            'shipping_discount' => $shippingDiscount,
            'is_free_shipping' => $isFreeShipping,
            'new_subtotal' => $subtotal - $productDiscount,
            'new_subtotal_formatted' => 'Rp ' . number_format($subtotal - $productDiscount, 0, ',', '.'),
            'new_shipping_cost' => $newShippingCost,
            'new_shipping_cost_formatted' => 'Rp ' . number_format($newShippingCost, 0, ',', '.'),
            'new_total' => $total,
            'new_total_formatted' => 'Rp ' . number_format($total, 0, ',', '.'),
            'has_courier_selected' => $hasCourierSelected,
            'courier' => $selectedCourier,
            'service' => $request->input('service', 'Reguler'),
            'voucher' => [
                'code' => $voucher->code,
                'name' => $voucher->name,
                'discount_target' => $voucher->discount_target,
                'is_free_shipping' => $voucher->is_free_shipping,
            ],
            // 🔥 KIRIM DATA UNTUK UI UPDATE
            'voucher_applied' => true,
            'voucher_code' => $voucher->code,
            'voucher_name' => $voucher->name,
            'voucher_discount' => $totalDiscount,
            'voucher_discount_formatted' => '-Rp ' . number_format($totalDiscount, 0, ',', '.'),
            'applied_voucher_summary' => [
                'code' => $voucher->code,
                'name' => $voucher->name,
                'discount' => $totalDiscount,
                'discount_formatted' => 'Rp ' . number_format($totalDiscount, 0, ',', '.'),
                'is_free_shipping' => $isFreeShipping
            ]
        ]);

    } catch (\Exception $e) {
        \Log::error('Apply Voucher Error:', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}

    // ============================================
    // REMOVE VOUCHER
    // ============================================

    public function removeVoucher(Request $request)
{
    $cart = session()->get('cart', []);
    $subtotal = $this->getSubtotalFromCart($cart);
    $shippingCost = 0;

    $total = $subtotal + $shippingCost;

    return response()->json([
        'success' => true,
        'message' => 'Voucher dibatalkan.',
        'new_subtotal' => $subtotal,
        'new_subtotal_formatted' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
        'new_shipping_cost' => $shippingCost,
        'new_shipping_cost_formatted' => 'Rp 0',
        'new_total' => $total,
        'new_total_formatted' => 'Rp ' . number_format($total, 0, ',', '.'),
        'voucher_discount' => 0,
        'voucher_discount_formatted' => 'Rp 0',
        'auto_applied' => false,
        'has_courier_selected' => false,
        'voucher_removed' => true
    ]);
}



    // ============================================
    // UPDATE SHIPPING
    // ============================================

    public function updateShipping(Request $request)
{
    try {
        $request->validate([
            'shipping_cost' => 'required|numeric|min:0',
            'courier' => 'nullable|string',
            'service' => 'nullable|string'
        ]);

        $shippingCost = (int) $request->shipping_cost;
        $courier = $request->input('courier', 'JNE');
        $service = $request->input('service', 'Reguler');

        // 🔥 SIMPAN KE SESSION
        session()->put('shipping_cost', $shippingCost);
        session()->put('selected_courier', $courier);
        session()->put('selected_service', $service);

        // 🔥 SIMPAN ORIGINAL SHIPPING COST (untuk voucher)
        if (!session()->has('original_shipping_cost') || session()->get('original_shipping_cost') == 0) {
            session()->put('original_shipping_cost', $shippingCost);
        }

        // 🔥 RE-CALCULATE VOUCHER ONGKIR
        $this->recalculateVoucherAfterShipping();

        // 🔥 AMBIL DATA TERBARU
        $cart = session()->get('cart', []);
        $subtotal = $this->getSubtotalFromCart($cart);
        $voucherDiscount = (int) session()->get('voucher_discount', 0);
        $total = $subtotal + $shippingCost - $voucherDiscount;

        return response()->json([
            'success' => true,
            'message' => 'Shipping cost updated successfully',
            'shipping_cost' => $shippingCost,
            'shipping_cost_formatted' => 'Rp ' . number_format($shippingCost, 0, ',', '.'),
            'courier' => $courier,
            'service' => $service,
            'has_courier_selected' => true,
            'subtotal' => $subtotal,
            'subtotal_formatted' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
            'voucher_discount' => $voucherDiscount,
            'voucher_discount_formatted' => $voucherDiscount > 0 ? '-Rp ' . number_format($voucherDiscount, 0, ',', '.') : 'Rp 0',
            'total' => $total,
            'total_formatted' => 'Rp ' . number_format($total, 0, ',', '.'),
        ]);

    } catch (\Exception $e) {
        \Log::error('Update shipping error:', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Gagal memperbarui ongkir: ' . $e->getMessage()
        ], 500);
    }
}


    // ============================================
    // PROCESS - Proses Checkout
    // ============================================

    public function process(Request $request)
    {
        $cart = session()->get('cart', []);
        $isBuyNow = session()->get('is_buy_now', false);

        if (empty($cart)) {
            if (session()->has('old_cart_backup') && !empty(session()->get('old_cart_backup'))) {
                session()->put('cart', session()->get('old_cart_backup'));
                session()->forget('old_cart_backup');
                session()->forget('is_buy_now');
                return redirect()->route('customer.cart.index');
            }
            return redirect()->route('customer.cart.index')->with('error', 'Keranjang belanja kosong.');
        }

        // Cek stok
        foreach ($cart as $item) {
            if ($item['variant_id']) {
                $variant = ProductVariant::find($item['variant_id']);
                if (!$variant || $variant->stock < $item['quantity']) {
                    return back()->with('error', "Stok {$item['product_name']} tidak mencukupi.");
                }
            }
        }

        $rules = [
            'shipping_name' => 'required|string|max:100',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'shipping_province' => 'required|string|max:100',
            'shipping_city' => 'required|string|max:100',
            'shipping_district' => 'required|string|max:100',
            'shipping_subdistrict' => 'required|string|max:100',
            'shipping_postal_code' => 'nullable|string|max:10',
            'courier' => 'nullable|string|max:50',
            'shipping_service' => 'nullable|string|max:50',
            'shipping_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ];

        if (!Auth::guard('customer')->check()) {
            $rules['email'] = 'nullable|email|max:255|unique:customers,email';
        }

        $validated = $request->validate($rules);

        try {
            DB::beginTransaction();

            // ============================================
            // CUSTOMER HANDLING
            // ============================================

            if (Auth::guard('customer')->check()) {
                $customer = Auth::guard('customer')->user();
            } else {
                $existingCustomer = User::where('phone', $validated['shipping_phone'])->first();

                if ($existingCustomer) {
                    $customer = $existingCustomer;
                    Auth::guard('customer')->login($customer);
                    $request->session()->regenerate();
                } else {
                    $defaultPassword = substr(preg_replace('/[^0-9]/', '', $validated['shipping_phone']), -6);

                    $customer = User::create([
                        'name' => $validated['shipping_name'],
                        'email' => $validated['email'] ?? null,
                        'phone' => $validated['shipping_phone'],
                        'password' => $defaultPassword,
                        'is_active' => true,
                        'role' => 'customer',
                    ]);

                    Auth::guard('customer')->login($customer);
                    $request->session()->regenerate();

                    UserAddress::create([
                        'user_id' => $customer->id,
                        'label' => 'Alamat Utama',
                        'recipient_name' => $validated['shipping_name'],
                        'recipient_phone' => $validated['shipping_phone'],
                        'address' => $validated['shipping_address'],
                        'city' => $validated['shipping_city'],
                        'district' => $validated['shipping_district'],
                        'subdistrict' => $validated['shipping_subdistrict'],
                        'province' => $validated['shipping_province'],
                        'postal_code' => $validated['shipping_postal_code'] ?? '0',
                        'is_default' => true,
                    ]);
                }
            }

            // ============================================
            // CALCULATE TOTALS
            // ============================================

            $subtotal = $this->getSubtotalFromCart($cart);
            $shippingCost = $validated['shipping_cost'] ?? 0;
            $courier = $request->input('courier');
            $service = $request->input('shipping_service');

            // 🔥 AMBIL VOUCHER DARI REQUEST
            $voucherCode = $request->input('voucher_code');
            $productDiscount = 0;
            $shippingDiscount = 0;
            $totalDiscount = 0;
            $appliedVoucher = null;
            $isFreeShipping = false;

            if ($voucherCode) {
                $voucher = Voucher::where('code', $voucherCode)->first();
                if ($voucher) {
                    $eligibility = $voucher->checkEligibility($subtotal, $customer->id);
                    if ($eligibility['eligible']) {
                        $selectedCourier = $validated['courier'] ?? 'JNE';
                        if (($voucher->discount_target === 'shipping' || $voucher->is_free_shipping) &&
                            !$voucher->isCourierApplicable($selectedCourier)) {
                            $voucherCode = null;
                        } else {
                            $appliedVoucher = $voucher;

                            if ($voucher->is_free_shipping) {
                                $shippingDiscount = $shippingCost;
                                $isFreeShipping = true;
                            } elseif ($voucher->discount_target === 'shipping') {
                                $shippingDiscount = $voucher->calculateShippingDiscount($shippingCost);
                            } else {
                                $productDiscount = $voucher->calculateDiscount($subtotal);
                            }

                            $totalDiscount = $productDiscount + $shippingDiscount;
                        }
                    } else {
                        $voucherCode = null;
                    }
                }
            }

            $total = $subtotal + $shippingCost - $totalDiscount;

            $orderNumber = 'ORD-' . strtoupper(uniqid());

            // ============================================
            // CREATE ORDER
            // ============================================

            $order = Order::create([
                'user_id' => $customer->id,
                'order_number' => $orderNumber,
                'payment_status' => 'unpaid',
                'shipping_status' => 'pending',
                'shipping_name' => $validated['shipping_name'],
                'shipping_phone' => $validated['shipping_phone'],
                'shipping_address' => $validated['shipping_address'],
                'shipping_city' => $validated['shipping_city'],
                'shipping_province' => $validated['shipping_province'],
                'shipping_district' => $validated['shipping_district'] ?? null,
                'shipping_subdistrict' => $validated['shipping_subdistrict'] ?? null,
                'shipping_postal_code' => $validated['shipping_postal_code'] ?? '0',
                'courier' => $validated['courier'] ?? null,
                'service' => $validated['shipping_service'] ?? null,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'product_discount' => $productDiscount,
                'shipping_discount' => $shippingDiscount,
                'is_free_shipping' => $isFreeShipping,
                'discount' => $totalDiscount,
                'total' => $total,
                'notes' => $validated['notes'] ?? null,
                'payment_method' => 'midtrans',
            ]);

            // ============================================
            // CREATE ORDER ITEMS
            // ============================================

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['variant_id'] ?: null,
                    'product_name' => $item['product_name'],
                    'variant_name' => $item['variant_name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);

                if ($item['variant_id']) {
                    $variant = ProductVariant::find($item['variant_id']);
                    if ($variant) {
                        $variant->decrement('stock', $item['quantity']);
                    }
                }
            }

            // ============================================
            // SAVE VOUCHER USAGE
            // ============================================

            if ($appliedVoucher && $totalDiscount > 0) {
                VoucherUsage::create([
                    'voucher_id' => $appliedVoucher->id,
                    'user_id' => $customer->id,
                    'order_id' => $order->id,
                    'discount_applied' => $totalDiscount,
                    'product_discount' => $productDiscount,
                    'shipping_discount' => $shippingDiscount,
                    'is_free_shipping' => $isFreeShipping,
                ]);

                $appliedVoucher->increment('used_count');
            }

            DB::commit();

            // ============================================
            // CLEANUP SESSION
            // ============================================

            session()->forget('voucher_code');
            session()->forget('voucher_discount');

            if ($isBuyNow && session()->has('old_cart_backup')) {
                $oldCart = session()->get('old_cart_backup');
                session()->put('cart', $oldCart);
                session()->forget('old_cart_backup');
                session()->forget('is_buy_now');
            } else {
                session()->forget('cart');
            }

            // 🔥 RESET DATA CHECKOUT
            $this->resetCheckoutData();

            return redirect()->route('customer.midtrans.pay', $order);

        } catch (\Exception $e) {
            DB::rollBack();
            report($e);

            if ($isBuyNow && session()->has('old_cart_backup')) {
                session()->put('cart', session()->get('old_cart_backup'));
                session()->forget('old_cart_backup');
                session()->forget('is_buy_now');
            }

            return back()
                ->with('error', 'Gagal memproses pesanan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function getTotal()
    {
        $cart = session()->get('cart', []);
        $subtotal = $this->getSubtotalFromCart($cart);

        $hasCourierSelected = !empty(session()->get('selected_courier')) && !empty(session()->get('selected_service'));
        $shippingCost = $hasCourierSelected ? (int) session()->get('shipping_cost', 0) : 0;
        $voucherDiscount = (int) session()->get('voucher_discount', 0);

        $total = $subtotal + $shippingCost - $voucherDiscount;

        return response()->json([
            'success' => true,
            'data' => [
                'subtotal' => $subtotal,
                'subtotal_formatted' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
                'shipping_cost' => $shippingCost,
                'shipping_cost_formatted' => $hasCourierSelected ? 'Rp ' . number_format($shippingCost, 0, ',', '.') : 'Belum dipilih',
                'voucher_discount' => $voucherDiscount,
                'voucher_discount_formatted' => $voucherDiscount > 0 ? '-Rp ' . number_format($voucherDiscount, 0, ',', '.') : 'Rp 0',
                'total' => $total,
                'total_formatted' => 'Rp ' . number_format($total, 0, ',', '.'),
                'has_courier_selected' => $hasCourierSelected
            ]
        ]);
    }

    // ============================================
    // SUCCESS
    // ============================================

    public function success(Order $order)
    {
        if ($order->user_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        $order->load(['items.product', 'items.variant']);
        $setting = \App\Models\Setting::first();

        return view('customer.checkout.success', compact('order', 'setting'));
    }

    // ============================================
    // BUY NOW
    // ============================================

    public function buyNow(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = \App\Models\Product::with(['images', 'variants'])->findOrFail($request->product_id);
        $variant = \App\Models\ProductVariant::with(['variantValues.optionValue'])->findOrFail($request->variant_id);

        $effectivePrice = $variant->effective_price;

        $oldCart = session()->get('cart', []);
        session()->put('old_cart_backup', $oldCart);
        session()->forget('cart');

        $variantName = $variant->variantValues->map(function($vv) {
            return $vv->optionValue->value ?? '';
        })->filter()->implode(' / ');

        $imageUrl = null;
        if ($product->images->first()) {
            $imageUrl = $product->images->first()->image;
        }

        $buyNowItems = [
            [
                'product_id' => $product->id,
                'variant_id' => $variant->id,
                'product_name' => $product->name,
                'variant_name' => $variantName,
                'price' => $effectivePrice,
                'original_price' => $variant->price,
                'quantity' => $request->quantity,
                'weight' => $variant->weight,
                'image' => $imageUrl,
                'slug' => $product->slug,
            ]
        ];

        session()->put('cart', $buyNowItems);
        session()->put('is_buy_now', true);

        return response()->json([
            'success' => true,
            'redirect' => route('customer.checkout.index'),
        ]);
    }

    // ============================================
    // HELPER - GET VARIANT IMAGE
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

    // ============================================
    // TRACKING
    // ============================================

    public function trackOrder(Order $order)
    {
        if ($order->user_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        if (!$order->biteship_order_id) {
            return response()->json([
                'success' => false,
                'message' => 'Order belum memiliki tracking'
            ]);
        }

        $trackingId = $order->biteship_order_id;
        if ($order->biteship_tracking_url) {
            $parsed = parse_url($order->biteship_tracking_url);
            if (!empty($parsed['path'])) {
                $trackingId = trim($parsed['path'], '/');
            }
        }

        $tracking = $this->biteship->trackOrder(
            $order->biteship_order_id,
            $order->tracking_number,
            $order->biteship_tracking_url
        );

        return response()->json($tracking);
    }

    // ============================================
    // GET WAYBILL
    // ============================================

    public function getWaybill(Order $order)
    {
        if ($order->user_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        if (!$order->biteship_order_id) {
            return response()->json([
                'success' => false,
                'message' => 'Order belum memiliki resi'
            ]);
        }

        $waybill = $this->biteship->getWaybill($order->biteship_order_id);

        if (isset($waybill['waybill_url'])) {
            return redirect($waybill['waybill_url']);
        }

        return response()->json($waybill);
    }

    // ============================================
    // GET AVAILABLE VOUCHERS
    // ============================================

    public function getAvailableVouchers(Request $request)
{
    try {
        \Log::info('=== getAvailableVouchers called ===', [
            'request_all' => $request->all(),
            'shipping_cost' => $request->input('shipping_cost'),
            'subtotal' => $request->input('subtotal'),
        ]);

        $cart = session()->get('cart', []);

        // Hitung subtotal
        $subtotal = (float) $request->input('subtotal', 0);
        if ($subtotal <= 0) {
            $subtotal = array_reduce($cart, function ($carry, $item) {
                return $carry + ($item['price'] * $item['quantity']);
            }, 0);
        }

        $shippingCost = (float) $request->input('shipping_cost', session('shipping_cost', 0));
        $userId = Auth::guard('customer')->id();
        $selectedCourier = session('selected_courier', 'JNE');
        $now = \Carbon\Carbon::now();

        \Log::info('Voucher filter params:', [
            'subtotal' => $subtotal,
            'shippingCost' => $shippingCost,
            'userId' => $userId,
            'selectedCourier' => $selectedCourier,
        ]);

        // 🔥 QUERY DASAR - HANYA VOUCHER AKTIF & PUBLIK & BELUM KADALUARSA
        $query = Voucher::where('is_active', true)
            ->where('is_public', true)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where(function($q) {
                $q->whereNull('usage_limit')
                  ->orWhereRaw('used_count < usage_limit');
            });

        // 🔥 FILTER UNTUK USER (jika login)
        if ($userId) {
            $query->where(function($q) use ($userId) {
                // Voucher untuk semua user
                $q->where('is_for_all_users', true)
                  // ATAU voucher khusus user yang belum pernah dipakai
                  ->orWhere(function($sub) use ($userId) {
                      $sub->where('is_for_all_users', false)
                          ->whereNotExists(function($exists) use ($userId) {
                              $exists->select('id')
                                  ->from('voucher_usages')
                                  ->whereColumn('voucher_usages.voucher_id', 'vouchers.id')
                                  ->where('voucher_usages.user_id', $userId);
                          });
                  });
            });
        } else {
            // User tidak login: hanya tampilkan voucher untuk semua user
            $query->where('is_for_all_users', true);
        }

        // 🔥 FILTER MINIMAL TRANSAKSI (voucher gratis ongkir TETAP DITAMPILKAN walau nominal belum cukup)
        $query->where(function($q) use ($subtotal) {
            $q->whereNull('min_transaction_amount')
              ->orWhere('min_transaction_amount', '<=', $subtotal)
              ->orWhere('is_free_shipping', true);
        });

        // 🔥 FILTER LIMIT PER USER (jika login)
        if ($userId) {
            $query->where(function($q) use ($userId) {
                $q->whereNull('limit_per_user')
                  ->orWhere('limit_per_user', 0)
                  ->orWhereRaw('(SELECT COUNT(*) FROM voucher_usages WHERE voucher_usages.voucher_id = vouchers.id AND voucher_usages.user_id = ?) < limit_per_user', [$userId]);
            });
        }

        // 🔥 FILTER UNTUK VOUCHER ONGKIR
        if ($shippingCost > 0) {
            // Tampilkan semua voucher yang eligible
            $query->where(function($q) use ($shippingCost, $selectedCourier) {
                // Voucher produk: selalu tampilkan
                $q->where('discount_target', 'product')
                  ->where('is_free_shipping', false)
                  // ATAU voucher ongkir yang berlaku
                  ->orWhere(function($sub) use ($shippingCost, $selectedCourier) {
                      $sub->where(function($inner) {
                          $inner->where('discount_target', 'shipping')
                                ->orWhere('is_free_shipping', true);
                      })
                      ->where(function($inner) use ($selectedCourier) {
                          $inner->where('apply_to_all_couriers', true)
                                ->orWhereJsonContains('applicable_couriers', strtoupper($selectedCourier));
                      });
                  });
            });
        } else {
            // Jika shipping cost 0, hanya tampilkan voucher produk (bukan ongkir)
            $query->where('discount_target', 'product')
                  ->where('is_free_shipping', false);
        }

        $vouchers = $query
            ->orderBy('min_transaction_amount', 'asc')
            ->orderBy('discount_value', 'desc')
            ->get();

        \Log::info('Vouchers found:', ['count' => $vouchers->count()]);

        $voucherData = $vouchers->map(function ($voucher) use ($subtotal, $shippingCost, $userId, $selectedCourier) {
            // CEK ELIGIBILITY LENGKAP
            $eligibility = $voucher->checkEligibility($subtotal, $userId, $shippingCost, $selectedCourier);
            $isApplicable = $eligibility['eligible'];

            // Format diskon
            $discountText = '';
            $isShippingVoucher = ($voucher->discount_target === 'shipping' || $voucher->is_free_shipping);

            if ($voucher->is_free_shipping) {
                $discountText = 'Gratis Ongkir';
            } elseif ($voucher->discount_target === 'shipping') {
                if ($voucher->discount_type === 'fixed') {
                    $discountText = 'Rp ' . number_format($voucher->discount_value, 0, ',', '.') . ' (Ongkir)';
                } else {
                    $discountText = $voucher->discount_value . '% (Ongkir)';
                    if ($voucher->max_shipping_discount) {
                        $discountText .= ' (Maks. Rp ' . number_format($voucher->max_shipping_discount, 0, ',', '.') . ')';
                    }
                }
            } else {
                if ($voucher->discount_type === 'fixed') {
                    $discountText = 'Rp ' . number_format($voucher->discount_value, 0, ',', '.');
                } else {
                    $discountText = $voucher->discount_value . '%';
                    if ($voucher->max_discount_amount) {
                        $discountText .= ' (Maks. Rp ' . number_format($voucher->max_discount_amount, 0, ',', '.') . ')';
                    }
                }
            }

            return [
                'id' => $voucher->id,
                'code' => $voucher->code,
                'name' => $voucher->name,
                'discount_type' => $voucher->discount_type,
                'discount_value' => (float) $voucher->discount_value,
                'discount_target' => $voucher->discount_target ?? 'product',
                'is_free_shipping' => (bool) $voucher->is_free_shipping,
                'min_transaction_amount' => (float) $voucher->min_transaction_amount,
                'max_discount_amount' => (float) $voucher->max_discount_amount,
                'max_shipping_discount' => (float) $voucher->max_shipping_discount,
                'is_applicable' => $isApplicable,
                'is_shipping_voucher' => $isShippingVoucher,
                'discount_text' => $discountText,
                // 🔥 NOMINAL YANG MASIH KURANG UNTUK BISA PAKAI VOUCHER
                'remaining_amount' => max(0, (float) $voucher->min_transaction_amount - $subtotal),
                'end_date_label' => $voucher->end_date ? \Carbon\Carbon::parse($voucher->end_date)->translatedFormat('d M Y') : 'Tanpa Batas',
                'detail_url' => route('customer.vouchers.show', $voucher->id),
                'eligibility_message' => $eligibility['message'],
            ];
        })
        // TAMPILKAN: voucher yang bisa dipakai + voucher gratis ongkir yang nominalnya belum cukup
        ->filter(function ($voucher) {
            return $voucher['is_applicable'] === true || $voucher['is_free_shipping'] === true;
        })
        ->values();

        return response()->json([
            'success' => true,
            'vouchers' => $voucherData,
            'has_courier_selected' => $shippingCost > 0,
            'shipping_cost' => $shippingCost,
            'subtotal' => $subtotal,
            'total_vouchers' => $voucherData->count(),
        ]);

    } catch (\Exception $e) {
        \Log::error('Error in getAvailableVouchers:', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan saat memuat voucher: ' . $e->getMessage(),
            'vouchers' => [],
        ], 500);
    }
}
    // ============================================
    // FORMAT DISCOUNT TEXT
    // ============================================

    private function formatDiscountText($voucher): string
    {
        if ($voucher->is_free_shipping) {
            return 'Gratis Ongkir';
        }

        if ($voucher->discount_target === 'shipping') {
            if ($voucher->discount_type === 'fixed') {
                return 'Diskon Ongkir Rp ' . number_format($voucher->discount_value, 0, ',', '.');
            }
            $text = $voucher->discount_value . '%';
            if ($voucher->max_shipping_discount) {
                $text .= ' (Maks. Rp ' . number_format($voucher->max_shipping_discount, 0, ',', '.') . ')';
            }
            return 'Diskon Ongkir ' . $text;
        }

        if ($voucher->discount_type === 'fixed') {
            return 'Rp ' . number_format($voucher->discount_value, 0, ',', '.');
        }

        $text = $voucher->discount_value . '%';
        if ($voucher->max_discount_amount) {
            $text .= ' (Maks. Rp ' . number_format($voucher->max_discount_amount, 0, ',', '.') . ')';
        }
        return $text;
    }
}