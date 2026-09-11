<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\Customer;
use App\Models\Cart;
use App\Models\Voucher;
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

    public function index()
    {
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

        // ============================================
        // 🔥 AUTO APPLY VOUCHER TERBAIK
        // ============================================
        
        // Cek apakah sudah ada voucher yang dipilih manual
        $hasManualVoucher = session()->has('voucher_code') && !empty(session('voucher_code'));
        $isAutoApplied = session()->get('voucher_auto_applied', false);
        
        // Jika tidak ada voucher sama sekali, coba auto apply
        if (!$hasManualVoucher) {
            // Cari voucher terbaik untuk subtotal ini
            $bestVoucher = $this->findBestVoucher($subtotal);
            
            if ($bestVoucher) {
                // 🔥 AUTO APPLY VOUCHER
                $this->applyVoucherToSession($bestVoucher, $subtotal);
                
                // Tandai bahwa ini auto-apply
                session()->put('voucher_auto_applied', true);
                $isAutoApplied = true;
            }
        }

        // Ambil data voucher dari session
        $productDiscount = session()->get('voucher_product_discount', 0);
        $shippingDiscount = session()->get('voucher_shipping_discount', 0);
        $isFreeShipping = session()->get('voucher_is_free_shipping', false);
        $totalVoucherDiscount = session()->get('voucher_discount', 0);

        $appliedVoucher = null;
        if (session()->has('voucher_code')) {
            $voucher = Voucher::where('code', session('voucher_code'))->first();
            if ($voucher) {
                $userId = Auth::guard('customer')->id();
                $eligibility = $voucher->checkEligibility($subtotal, $userId);
                if ($eligibility['eligible']) {
                    $appliedVoucher = $voucher;
                } else {
                    // Voucher tidak valid, hapus session
                    $this->clearVoucherSession();
                    $productDiscount = 0;
                    $shippingDiscount = 0;
                    $totalVoucherDiscount = 0;
                    $isFreeShipping = false;
                    $isAutoApplied = false;
                }
            }
        }

        $addresses = [];
        $customer = null;
        $defaultAddress = null;
        $shippingCost = session()->get('shipping_cost', 0);
        
        if (Auth::guard('customer')->check()) {
            $customer = Auth::guard('customer')->user();
            $addresses = $customer->addresses()->orderBy('is_default', 'desc')->get();
            $defaultAddress = $customer->addresses()->where('is_default', true)->first();
        }

        // 🔥 KIRIM SEMUA VARIABLE KE VIEW
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
            'isAutoApplied'  // 🔥 TAMBAHKAN INI
        ));
    }

    private function findBestVoucher(float $subtotal): ?Voucher
    {
        $userId = Auth::guard('customer')->id();
        $shippingCost = session()->get('shipping_cost', 0);
        
        // Ambil semua voucher yang aktif dan publik
        $vouchers = Voucher::publicActive()->get();
        
        $bestVoucher = null;
        $bestDiscount = 0;
        
        foreach ($vouchers as $voucher) {
            // Cek eligibility
            $eligibility = $voucher->checkEligibility($subtotal, $userId);
            if (!$eligibility['eligible']) {
                continue;
            }
            
            // Cek kurir untuk diskon ongkir
            if ($voucher->discount_target === 'shipping' || $voucher->is_free_shipping) {
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
            
            // Pilih yang terbaik
            if ($discount > $bestDiscount) {
                $bestDiscount = $discount;
                $bestVoucher = $voucher;
            }
        }
        
        return $bestVoucher;
    }

    private function applyVoucherToSession(Voucher $voucher, float $subtotal): void
    {
        $shippingCost = session()->get('shipping_cost', 0);
        $productDiscount = 0;
        $shippingDiscount = 0;
        $isFreeShipping = false;
        
        if ($voucher->is_free_shipping) {
            $shippingDiscount = $shippingCost;
            $isFreeShipping = true;
        } elseif ($voucher->discount_target === 'shipping') {
            $shippingDiscount = $voucher->calculateShippingDiscount($shippingCost);
        } else {
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

    private function clearVoucherSession(): void
    {
        session()->forget('voucher_code');
        session()->forget('voucher_discount');
        session()->forget('voucher_type');
        session()->forget('voucher_product_discount');
        session()->forget('voucher_shipping_discount');
        session()->forget('voucher_is_free_shipping');
        session()->forget('voucher_auto_applied');
    }

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

                // 🔥 PERBAIKI: AMBIL NAMA SERVICE DENGAN PRIORITAS YANG BENAR
                // Prioritas: courier_service_name > service_name > type > courier_service_code
                
                $serviceName = $rate['courier_service_name'] 
                    ?? $rate['service_name'] 
                    ?? $rate['type'] 
                    ?? $rate['courier_service_code']
                    ?? 'Reguler';
                
                // 🔥 HAPUS DUPLIKASI: Jika service_name sama dengan courier_service_code + nama tambahan
                // Contoh: "Besok Sampai Tujuan" muncul 2x karena ada di type dan courier_service_name
                
                // 🔥 CEK APAKAH SERVICE SUDAH ADA (UNTUK MENCEGAH DUPLIKAT)
                $serviceCode = $rate['courier_service_code'] ?? $rate['type'] ?? $rate['service_code'] ?? 'regular';
                
                // 🔥 BUAT UNIQUE KEY UNTUK MENCEGAH DUPLIKAT SERVICE
                $uniqueKey = $courierCode . '_' . $serviceCode;
                
                // 🔥 CEK APAKAH SERVICE SUDAH ADA DI GROUP
                $exists = false;
                foreach ($grouped[$courierCode]['services'] as $existing) {
                    if ($existing['service'] === $serviceCode) {
                        $exists = true;
                        break;
                    }
                }
                
                if ($exists) {
                    continue; // Skip duplicate service
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

            // 🔥 SORT SERVICE TERMURAH
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
    // APPLY VOUCHER - Apply Voucher to Checkout
    // ============================================

    public function applyVoucher(Request $request)
    {
        // 🔥 LOG REQUEST YANG MASUK
        \Log::info('Apply Voucher Request:', [
            'all' => $request->all(),
            'json' => $request->json()->all(),
            'content' => $request->getContent(),
            'voucher_code' => $request->input('voucher_code')
        ]);

        try {
            // 🔥 VALIDASI DENGAN ERROR YANG JELAS
            $validator = validator($request->all(), [
                'voucher_code' => 'required|string|max:50'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode voucher harus diisi.',
                    'errors' => $validator->errors()
                ], 400);
            }

            $voucherCode = strtoupper(trim($request->input('voucher_code')));
            
            // 🔥 CEK VOUCHER
            $voucher = Voucher::where('code', $voucherCode)->first();
            
            if (!$voucher) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode voucher "' . $voucherCode . '" tidak ditemukan.'
                ], 404);
            }

            // Get current cart and subtotal
            $cart = session()->get('cart', []);
            if (empty($cart)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Keranjang belanja kosong.'
                ], 400);
            }

            $cart = session()->get('cart', []);
            $subtotal = $this->getSubtotalFromCart($cart);
            $userId = Auth::guard('customer')->id();
            $shippingCost = $request->input('shipping_cost', session()->get('shipping_cost', 0));
            $selectedCourier = session()->get('selected_courier', 'JNE');

            if ($shippingCost > 0) {
                session()->put('shipping_cost', $shippingCost);
            }

            \Log::info('=== VOUCHER DEBUG ===', [
                'voucher_code' => $voucher->code,
                'voucher_discount_target' => $voucher->discount_target,
                'voucher_is_free_shipping' => $voucher->is_free_shipping,
                'voucher_discount_type' => $voucher->discount_type,
                'voucher_discount_value' => $voucher->discount_value,
                'voucher_max_shipping_discount' => $voucher->max_shipping_discount,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total_cart_items' => count($cart)
            ]);

            \Log::info('Voucher Check:', [
                'voucher_code' => $voucherCode,
                'voucher_id' => $voucher->id,
                'subtotal' => $subtotal,
                'user_id' => $userId,
                'shipping_cost' => $shippingCost,
                'courier' => $selectedCourier,
                'discount_target' => $voucher->discount_target,
                'is_free_shipping' => $voucher->is_free_shipping
            ]);

            // Check eligibility
            $eligibility = $voucher->checkEligibility($subtotal, $userId);
            
            \Log::info('Eligibility Check:', $eligibility);

            if (!$eligibility['eligible']) {
                return response()->json([
                    'success' => false,
                    'message' => $eligibility['message']
                ], 400);
            }

            // 🔥 CEK KURIR UNTUK DISKON ONGKIR
            if (($voucher->discount_target === 'shipping' || $voucher->is_free_shipping) && $shippingCost <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => '⚠️ Silakan pilih kurir dan layanan pengiriman terlebih dahulu sebelum menggunakan voucher ongkir!'
                ], 400);
            }

            // 🔥 HITUNG DISKON
            $productDiscount = 0;
            $shippingDiscount = 0;
            $isFreeShipping = false;

            if ($voucher->is_free_shipping) {
                // 🎁 GRATIS ONGKIR
                $shippingDiscount = $shippingCost;
                $isFreeShipping = true;
            } elseif ($voucher->discount_target === 'shipping') {
                // 🚚 DISKON ONGKIR
                $shippingDiscount = $voucher->calculateShippingDiscount($shippingCost);
                \Log::info('Shipping Discount:', [
                    'shipping_cost' => $shippingCost,
                    'discount' => $shippingDiscount,
                    'type' => $voucher->discount_type,
                    'value' => $voucher->discount_value
                ]);
            } else {
                // 🛒 DISKON PRODUK
                $productDiscount = $voucher->calculateDiscount($subtotal);
            }

            $totalDiscount = $productDiscount + $shippingDiscount;

            \Log::info('Discount Calculation:', [
                'product_discount' => $productDiscount,
                'shipping_discount' => $shippingDiscount,
                'total_discount' => $totalDiscount,
                'is_free_shipping' => $isFreeShipping
            ]);

            if ($totalDiscount <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Voucher tidak memberikan potongan untuk transaksi ini. (Subtotal: ' . $subtotal . ', Shipping: ' . $shippingCost . ')'
                ], 400);
            }

            // 🔥 SIMPAN KE SESSION
            session()->put('voucher_code', $voucher->code);
            session()->put('voucher_discount', $totalDiscount);
            session()->put('voucher_type', $voucher->discount_target);
            session()->put('voucher_product_discount', $productDiscount);
            session()->put('voucher_shipping_discount', $shippingDiscount);
            session()->put('voucher_is_free_shipping', $isFreeShipping);

            $total = $subtotal + $shippingCost - $totalDiscount;

            return response()->json([
                'success' => true,
                'message' => 'Voucher berhasil diterapkan!',
                'discount' => $totalDiscount,
                'product_discount' => $productDiscount,
                'shipping_discount' => $shippingDiscount,
                'is_free_shipping' => $isFreeShipping,
                'discount_formatted' => 'Rp ' . number_format($totalDiscount, 0, ',', '.'),
                'new_subtotal' => $subtotal - $productDiscount,
                'new_shipping_cost' => $shippingCost - $shippingDiscount,
                'new_total' => $total,
                'voucher' => [
                    'code' => $voucher->code,
                    'name' => $voucher->name,
                    'discount_target' => $voucher->discount_target,
                    'is_free_shipping' => $voucher->is_free_shipping,
                    'discount_type' => $voucher->discount_type,
                    'discount_value' => (float) $voucher->discount_value,
                    'max_discount_amount' => $voucher->max_discount_amount ? (float) $voucher->max_discount_amount : null,
                    'max_shipping_discount' => $voucher->max_shipping_discount ? (float) $voucher->max_shipping_discount : null,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Apply Voucher Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============================================
    // REMOVE VOUCHER - Remove Applied Voucher
    // ============================================

    public function removeVoucher(Request $request)
    {
        // 🔥 HAPUS SEMUA SESSION VOUCHER
        session()->forget('voucher_code');
        session()->forget('voucher_discount');
        session()->forget('voucher_type');
        session()->forget('voucher_product_discount');
        session()->forget('voucher_shipping_discount');
        session()->forget('voucher_is_free_shipping');
        session()->forget('voucher_auto_applied');

        $cart = session()->get('cart', []);
        $subtotal = $this->getSubtotalFromCart($cart);
        $shippingCost = session()->get('shipping_cost', 0);
        
        // 🔥 AUTO APPLY VOUCHER TERBAIK LAGI
        $bestVoucher = $this->findBestVoucher($subtotal);
        $total = $subtotal + $shippingCost;
        
        if ($bestVoucher) {
            $this->applyVoucherToSession($bestVoucher, $subtotal);
            session()->put('voucher_auto_applied', true);
            
            $totalDiscount = session()->get('voucher_discount', 0);
            $total = $subtotal + $shippingCost - $totalDiscount;
            
            return response()->json([
                'success' => true,
                'message' => 'Voucher dibatalkan, voucher terbaik otomatis diterapkan.',
                'new_subtotal' => $subtotal,
                'new_shipping_cost' => $shippingCost,
                'new_total' => $total,
                'auto_applied' => true
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Voucher dibatalkan.',
            'new_subtotal' => $subtotal,
            'new_shipping_cost' => $shippingCost,
            'new_total' => $total,
            'auto_applied' => false
        ]);
    }

    // ============================================
    // PROCESS - Proses Checkout (Support Guest)
    // ============================================

    public function updateShipping(Request $request)
    {
        $request->validate([
            'shipping_cost' => 'required|numeric|min:0',
            'courier' => 'nullable|string',
            'service' => 'nullable|string'
        ]);

        session()->put('shipping_cost', $request->shipping_cost);
        session()->put('selected_courier', $request->courier);
        session()->put('selected_service', $request->service);

        return response()->json([
            'success' => true,
            'message' => 'Shipping cost updated'
        ]);
    }

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
            // 🔥 HAPUS VALIDASI payment_method
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
                $existingCustomer = Customer::where('phone', $validated['shipping_phone'])->first();
                
                if ($existingCustomer) {
                    $customer = $existingCustomer;
                    Auth::guard('customer')->login($customer);
                    $request->session()->regenerate();
                } else {
                    $defaultPassword = substr(preg_replace('/[^0-9]/', '', $validated['shipping_phone']), -6);

                    $customer = Customer::create([
                        'name' => $validated['shipping_name'],
                        'email' => $validated['email'] ?? null,
                        'phone' => $validated['shipping_phone'],
                        'password' => $defaultPassword,
                        'is_active' => true,
                    ]);

                    Auth::guard('customer')->login($customer);
                    $request->session()->regenerate();

                    CustomerAddress::create([
                        'user_id' => $customer->id,
                        'customer_id' => $customer->id,
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
            
            $voucherCode = $request->input('voucher_code') ?: session('voucher_code');
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

            // ============================================
            // CREATE ORDER
            // ============================================
            
            $order = Order::create([
                'customer_id' => $customer->id,
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'status' => 'pending',
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
                // 🔥 SET PAYMENT_METHOD = midtrans (DEFAULT)
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

            // 🔥 LANGSUNG KE HALAMAN PEMBAYARAN MIDTRANS
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


    // ============================================
    // SUCCESS - Halaman Sukses
    // ============================================

    public function success(Order $order)
    {
        if ($order->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        $order->load(['items.product', 'items.variant']);

        return view('customer.checkout.success', compact('order'));
    }

    // ============================================
    // BUY NOW - Direct Checkout
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
    // TRACKING - Order Tracking
    // ============================================

    public function trackOrder(Order $order)
    {
        if ($order->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        if (!$order->biteship_order_id) {
            return response()->json([
                'success' => false,
                'message' => 'Order belum memiliki tracking'
            ]);
        }

        $tracking = $this->biteship->trackOrder($order->biteship_order_id);

        return response()->json($tracking);
    }

    public function getWaybill(Order $order)
    {
        if ($order->customer_id !== Auth::guard('customer')->id()) {
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

    public function getAvailableVouchers(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return response()->json([
                'success' => true,
                'vouchers' => []
            ]);
        }

        $subtotal = $this->getSubtotalFromCart($cart);
        $userId = Auth::guard('customer')->id();
        $shippingCost = session()->get('shipping_cost', 0);
        
        // Get applied voucher code from session
        $appliedCode = session('voucher_code');
        $isAutoApplied = session('voucher_auto_applied', false);
        
        // ============================================
        // 🔥 AMBIL VOUCHER YANG BELUM PERNAH DIGUNAKAN USER
        // ============================================
        
        $vouchers = Voucher::publicActive()
            ->where(function($query) use ($userId) {
                // 🔥 HANYA TAMPILKAN VOUCHER YANG BELUM PERNAH DIGUNAKAN USER
                if ($userId) {
                    $query->whereNotExists(function($sub) use ($userId) {
                        $sub->select('id')
                            ->from('voucher_usages')
                            ->whereColumn('voucher_usages.voucher_id', 'vouchers.id')
                            ->where('voucher_usages.user_id', $userId);
                    });
                }
                // Jika user tidak login, tampilkan semua voucher (tapi nanti di filter eligibility)
            })
            ->get();
        
        $result = [];
        $bestVoucher = null;
        $bestDiscount = 0;
        
        foreach ($vouchers as $voucher) {
            // Skip if already applied
            if ($voucher->code === $appliedCode) {
                continue;
            }
            
            $eligibility = $voucher->checkEligibility($subtotal, $userId);
            
            // 🔥 SKIP JIKA USER SUDAH MENCAPAI BATAS PENGGUNAAN
            if (!$eligibility['eligible']
                && str_contains($eligibility['message'], 'batas penggunaan voucher ini')) {
                continue;
            }
            
            // Hitung potensi diskon
            $potentialDiscount = 0;
            if ($voucher->is_free_shipping) {
                $potentialDiscount = $shippingCost;
            } elseif ($voucher->discount_target === 'shipping') {
                $potentialDiscount = $voucher->calculateShippingDiscount($shippingCost);
            } else {
                $potentialDiscount = $voucher->calculateDiscount($subtotal);
            }
            
            // Cari yang terbaik
            if ($eligibility['eligible'] && $potentialDiscount > $bestDiscount) {
                $bestDiscount = $potentialDiscount;
                $bestVoucher = $voucher;
            }
            
            $discountText = $this->formatDiscountText($voucher);
            
            $result[] = [
                'code' => $voucher->code,
                'name' => $voucher->name,
                'discount_type' => $voucher->discount_type,
                'discount_value' => (float) $voucher->discount_value,
                'max_discount_amount' => $voucher->max_discount_amount ? (float) $voucher->max_discount_amount : null,
                'min_transaction_amount' => (float) $voucher->min_transaction_amount,
                'end_date_label' => $voucher->end_date->locale('id')->translatedFormat('d M Y'),
                'detail_url' => route('customer.vouchers.show', $voucher),
                'is_applicable' => $eligibility['eligible'],
                'message' => $eligibility['eligible'] ? 'Voucher dapat digunakan' : $eligibility['message'],
                'discount_target' => $voucher->discount_target,
                'is_free_shipping' => $voucher->is_free_shipping,
                'max_shipping_discount' => $voucher->max_shipping_discount ? (float) $voucher->max_shipping_discount : null,
                'discount_text' => $discountText,
                'is_best' => ($bestVoucher && $voucher->id === $bestVoucher->id)
            ];
        }
        
        // Tandai apakah auto-applied aktif
        $hasManualVoucher = !empty($appliedCode) && !$isAutoApplied;
        
        return response()->json([
            'success' => true,
            'vouchers' => $result,
            'auto_applied' => $isAutoApplied,
            'applied_code' => $appliedCode,
            'has_manual_voucher' => $hasManualVoucher,
            'best_voucher' => $bestVoucher ? [
                'code' => $bestVoucher->code,
                'name' => $bestVoucher->name,
                'discount' => $bestDiscount
            ] : null
        ]);
    }

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
        
        // Product discount
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
