<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\MarketplaceController;
use App\Http\Controllers\Customer\AuthController as CustomerAuthController;
use App\Http\Controllers\Customer\AccountController;
use App\Http\Controllers\Customer\CustomerHomeController;
use App\Http\Controllers\Customer\CustomerProductController;
use App\Http\Controllers\Customer\CustomerCategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OfflineOrderController;
use App\Http\Controllers\Admin\ReturnController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\AgenWebsiteController;
use App\Http\Controllers\Api\VillageController;
use App\Http\Controllers\Customer\BiteshipController;
use App\Http\Controllers\Customer\WishlistController;
use App\Http\Controllers\Admin\FeatureController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\ArticleCategoryAjaxController;
use App\Http\Controllers\Customer\CustomerArticleController;
use App\Http\Controllers\Customer\ContactController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\TermController;
use App\Http\Controllers\Customer\CustomerTermController;
use App\Http\Controllers\Admin\PrivacyPolicyController;
use App\Http\Controllers\Customer\CustomerPrivacyController;
use App\Http\Controllers\Admin\AboutUsController;
use App\Http\Controllers\Customer\CustomerAboutController;
use App\Http\Controllers\Customer\CustomerSizeGuideController;
use App\Http\Controllers\Customer\HelpController;
use App\Http\Controllers\Customer\CaraPesanController;
use App\Http\Controllers\Customer\MidtransController;
use App\Http\Controllers\Customer\CustomerVoucherController;
use App\Http\Controllers\Admin\FaqCategoryController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Customer\CustomerTestimonialController;
use App\Http\Controllers\Admin\AccountAdminController;
use App\Http\Controllers\Admin\DashboardExportController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\PasswordResetController;

// ============================================
// CUSTOMER FRONTEND
// ============================================

Route::get('/', [CustomerHomeController::class, 'index'])->name('customer.home');
Route::get('/kontak', [ContactController::class, 'index'])->name('customer.contact');
Route::get('/syarat-dan-ketentuan', [CustomerTermController::class, 'index'])->name('customer.terms');
Route::get('/kebijakan-privasi', [CustomerPrivacyController::class, 'index'])->name('customer.privacy');
Route::get('/tentang-kami', [CustomerAboutController::class, 'index'])->name('customer.about');
Route::get('/panduan-ukuran', [CustomerSizeGuideController::class, 'index'])->name('customer.size-guide');
Route::get('/panduan-ukuran/{category:slug}', [CustomerSizeGuideController::class, 'show'])->name('customer.size-guide.show');
Route::get('/api/size-guide/{category}', [CustomerSizeGuideController::class, 'getSizeGuideData'])->name('api.size-guide');
Route::get('/bantuan', [HelpController::class, 'index'])->name('customer.help');
Route::post('/api/articles/toggle-like', [CustomerArticleController::class, 'toggleLike'])->name('customer.articles.toggle-like');
Route::get('/api/articles/get-likes', [CustomerArticleController::class, 'getLikes'])->name('customer.articles.get-likes');
Route::get('/api/articles/get-comments', [CustomerArticleController::class, 'getComments'])->name('customer.articles.get-comments');
Route::post('/api/articles/post-comment', [CustomerArticleController::class, 'postComment'])->name('customer.articles.post-comment');
Route::delete('/api/articles/delete-comment', [CustomerArticleController::class, 'deleteComment'])->name('customer.articles.delete-comment');
Route::get('/cara-pesan', [CaraPesanController::class, 'index'])->name('customer.cara-pesan');
Route::get('/flash-sale', [CustomerProductController::class, 'flashSale'])->name('customer.products.flash-sale');
Route::get('/lupa-password', [CustomerAuthController::class, 'showForgotPassword'])
    ->name('customer.forgot-password');
Route::post('/lupa-password', [CustomerAuthController::class, 'submitForgotPassword'])
    ->name('customer.forgot-password.submit');
Route::get('/lupa-password/status', [CustomerAuthController::class, 'showForgotPasswordResult'])
    ->name('customer.forgot-password.result');

Route::get('/reset-password/{token}', [CustomerAuthController::class, 'showResetPassword'])
    ->name('customer.reset-password')
    ->where('token', '[A-Za-z0-9]{64}');
Route::post('/reset-password/{token}', [CustomerAuthController::class, 'processResetPassword'])
    ->name('customer.reset-password.process')
    ->where('token', '[A-Za-z0-9]{64}');

Route::post('/checkout/apply-voucher', [CheckoutController::class, 'applyVoucher'])
    ->name('customer.checkout.apply-voucher');
Route::post('/checkout/remove-voucher', [CheckoutController::class, 'removeVoucher'])
    ->name('customer.checkout.remove-voucher');
Route::get('/checkout/vouchers-ajax', [CheckoutController::class, 'getAvailableVouchers'])
    ->name('customer.checkout.vouchers-ajax');

// 🔥 ROUTE TEST TRACKING (GET - untuk testing di browser)
Route::get('/test-refresh/{id}', function ($id) {
    $order = App\Models\Order::find($id);
    
    if (!$order) {
        return response()->json([
            'success' => false, 
            'message' => 'Order not found'
        ], 404);
    }
    
    return response()->json([
        'success' => true,
        'message' => 'Test refresh berhasil',
        'order_id' => $order->id,
        'order_number' => $order->order_number,
        'biteship_order_id' => $order->biteship_order_id,
        'data' => [
            'status' => 'shipped',
            'courier_name' => $order->courier ?? 'JNE',
            'waybill_id' => $order->tracking_number ?? 'TEST123',
        ]
    ]);
});

Route::get('/api/check-login', function() {
    return response()->json([
        'logged_in' => Auth::guard('customer')->check()
    ]);
})->name('customer.check-login');

Route::get('/api/trending-search', function() {
    // Contoh trending search dari data populer
    $trends = [
        'Sepatu Futsal',
        'Jersey Bola', 
        'Kaos Olahraga',
        'Sepatu Lari',
        'Raket Badminton'
    ];
    
    // Bisa juga ambil dari search history atau produk terpopuler
    // $trends = \App\Models\SearchLog::orderBy('count', 'desc')->limit(8)->pluck('keyword')->toArray();
    
    return response()->json([
        'success' => true,
        'trends' => $trends
    ]);
})->name('api.trending-search');

// ============================================
// API PRODUCT VARIANTS (untuk modal)
// ============================================
Route::get('/api/products/{product}/variants', function (App\Models\Product $product) {
    // 🔥 LOAD SEMUA RELASI YANG DIPERLUKAN
    $product->load([
        'options' => function ($query) {
            $query->orderBy('sort_order', 'asc');
        },
        'options.values' => function ($query) {
            $query->orderBy('sort_order', 'asc');
        },
        'variants' => function ($query) {
            $query->orderBy('price', 'asc');
        },
        'variants.variantValues',
        'variants.variantValues.optionValue',
        'images' => function ($query) {
            $query->orderBy('sort_order', 'asc');
        },
    ]);

    // 🔥 CEK APAKAH PRODUK PUNYA DISKON PRODUK (GLOBAL)
    $hasProductDiscount = $product->isOnProductDiscount();
    $productDiscountPercent = 0;
    
    // 🔥 HITUNG DATA DISKON PRODUK
    if ($hasProductDiscount) {
        $minPrice = $product->variants->min('price') ?? 0;
        $productDiscountPercent = $product->getProductDiscountPercent($minPrice);
    }

    // 🔥 HITUNG DATA DISKON UNTUK SETIAP VARIAN
    $variants = $product->variants->map(function ($variant) use ($product, $hasProductDiscount, $productDiscountPercent) {
        // 🔥 HITUNG HARGA EFEKTIF (PRIORITAS: DISKON VARIAN > DISKON PRODUK)
        $effectivePrice = $variant->effective_price;
        $discountPercent = $variant->discount_percent;
        
        // 🔥 TAMBAHKAN INFO DISKON PRODUK KE VARIAN
        $variantData = [
            'id' => (int) $variant->id,
            'sku' => $variant->sku,
            'price' => (float) $variant->price,
            'discount_price' => $variant->discount_price ? (float) $variant->discount_price : null,
            'effective_price' => (float) $effectivePrice,
            'discount_percent' => (float) $discountPercent,
            'stock' => (int) $variant->stock,
            'weight' => (int) $variant->weight,
            'image' => $variant->image ? Storage::url($variant->image) : null,
            'values' => $variant->variantValues->pluck('product_option_value_id')->map(function($id) {
                return (int) $id;
            })->toArray(),
            'has_product_discount' => $hasProductDiscount,
            'product_discount_percent' => (float) $productDiscountPercent,
        ];
        
        return $variantData;
    })->values()->toArray();

    // 🔥 BUILD DATA OPTIONS
    $options = $product->options->map(function ($option) {
        return [
            'id' => (int) $option->id,
            'name' => $option->name,
            'values' => $option->values->map(function ($value) {
                return [
                    'id' => (int) $value->id,
                    'value' => $value->value,
                    'image' => $value->image ? Storage::url($value->image) : null,
                ];
            })->values()->toArray(),
        ];
    })->values()->toArray();

    // 🔥 AMBIL GAMBAR PRODUK
    $productImage = $product->images->first() 
        ? Storage::url($product->images->first()->image) 
        : null;

    // 🔥 HITUNG HARGA TERMURAH (TERMASUK DISKON PRODUK)
    $minEffectivePrice = null;
    foreach ($variants as $variant) {
        $effPrice = $variant['effective_price'] ?? $variant['price'];
        if ($minEffectivePrice === null || $effPrice < $minEffectivePrice) {
            $minEffectivePrice = $effPrice;
        }
    }

    return response()->json([
        'success' => true,
        'product' => [
            'id' => (int) $product->id,
            'name' => $product->name,
            'image' => $productImage,
            'has_options' => $product->options->isNotEmpty(),
            // 🔥 DATA DISKON PRODUK
            'has_product_discount' => $hasProductDiscount,
            'product_discount_percent' => (float) $productDiscountPercent,
            'min_effective_price' => (float) $minEffectivePrice,
            'max_price' => (float) $product->variants->max('price') ?? 0,
        ],
        'options' => $options,
        'variants' => $variants,
        'debug' => [
            'options_count' => count($options),
            'variants_count' => count($variants),
            'has_product_discount' => $hasProductDiscount,
            'product_discount_percent' => $productDiscountPercent,
        ]
    ]);
})->name('api.products.variants');

Route::get('/katalog', [CustomerProductController::class, 'index'])->name('customer.products.index');
Route::get('/produk/{product:slug}', [CustomerProductController::class, 'show'])->name('customer.products.show');
Route::get('/katalog/terbaru', [CustomerProductController::class, 'latest'])->name('customer.products.latest');
Route::get('/katalog/promo', [CustomerProductController::class, 'promo'])->name('customer.products.promo'); 

Route::get('/artikel', [CustomerArticleController::class, 'index'])
    ->name('customer.articles.index');

Route::get('/artikel/{slug}', [CustomerArticleController::class, 'show'])
    ->name('customer.articles.show');

Route::post('/api/articles/record-view', [App\Http\Controllers\Customer\CustomerArticleController::class, 'recordView'])
    ->name('customer.articles.record-view');

Route::get('/kategori/{category:slug}', [CustomerCategoryController::class, 'show'])->name('customer.categories.show');

// ============================================
// CUSTOMER AUTH
// ============================================

Route::get('/login', [CustomerAuthController::class, 'showLogin'])->name('customer.login');
Route::post('/login', [CustomerAuthController::class, 'login'])->name('customer.login.process');
Route::get('/register', [CustomerAuthController::class, 'showRegister'])->name('customer.register');
Route::post('/register', [CustomerAuthController::class, 'register'])->name('customer.register.process');
Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');

// ============================================
// WISHLIST
// ============================================

Route::get('/wishlist', [WishlistController::class, 'index'])->name('customer.wishlist.index');
Route::get('/wishlist/popup', [WishlistController::class, 'popup'])->name('customer.wishlist.popup');
Route::get('/wishlist/status', [WishlistController::class, 'status'])->name('customer.wishlist.status');
Route::post('/wishlist/add', [WishlistController::class, 'add'])->name('customer.wishlist.add');
Route::delete('/wishlist/remove', [WishlistController::class, 'remove'])->name('customer.wishlist.remove');
Route::delete('/wishlist/clear', [WishlistController::class, 'clear'])->name('customer.wishlist.clear');

// ============================================
// CART
// ============================================

Route::get('/cart', [CartController::class, 'index'])->name('customer.cart.index');
Route::get('/cart/popup', [CartController::class, 'popup'])->name('customer.cart.popup');
Route::post('/cart/add', [CartController::class, 'add'])->name('customer.cart.add');
Route::put('/cart/update', [CartController::class, 'update'])->name('customer.cart.update');
Route::delete('/cart/remove', [CartController::class, 'remove'])->name('customer.cart.remove');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('customer.cart.clear');
Route::get('/cart/count', [CartController::class, 'count'])->name('customer.cart.count');
Route::post('/cart/buy-now', [CartController::class, 'buyNow'])->name('customer.cart.buy-now');

// ============================================
// BUY NOW
// ============================================
Route::post('/buy-now', [CheckoutController::class, 'buyNow'])->name('customer.buy-now');

// ============================================
// CHECKOUT
// ============================================

Route::get('/checkout', [CheckoutController::class, 'index'])->name('customer.checkout.index');
Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('customer.checkout.process');
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('customer.checkout.success');

// ============================================
// API AGENWEBSITE (untuk ongkir)
// ============================================

Route::get('/test-biteship', function () {
    $apiKey = config('services.biteship.api_key');
    $originPostalCode = config('services.biteship.origin_postal_code', '46191');
    
    // 🔥 TEST DENGAN DESTINATION SAMA
    $payload = [
        'origin_postal_code' => $originPostalCode,
        'destination_postal_code' => $originPostalCode, // SAMA DENGAN ORIGIN
        'couriers' => 'jne,jnt,sicepat,pos,anteraja',
        'items' => [
            [
                'name' => 'Test Product',
                'weight' => 1000,
                'quantity' => 1,
                'value' => 100000
            ]
        ]
    ];
    
    Log::info('Test Biteship Same Postal Code:', $payload);
    
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $apiKey,
        'Content-Type' => 'application/json',
    ])->post('https://api.biteship.com/v1/rates/couriers', $payload);
    
    $data = $response->json();
    
    return response()->json([
        'status' => $response->status(),
        'success' => $response->successful(),
        'payload_sent' => $payload,
        'response' => $data,
        'pricing' => $data['pricing'] ?? []
    ]);
});

Route::prefix('midtrans')->name('customer.midtrans.')->group(function () {
    Route::get('/pay/{order}', [MidtransController::class, 'pay'])->name('pay');
    Route::get('/finish', [MidtransController::class, 'finish'])->name('finish');
    Route::get('/error', [MidtransController::class, 'error'])->name('error');
    Route::post('/notification', [MidtransController::class, 'notificationHandler'])
        ->name('notification')
        ->withoutMiddleware(['App\Http\Middleware\VerifyCsrfToken']);
    Route::get('/check-status/{order}', [MidtransController::class, 'checkStatus'])->name('check-status');
});
Route::get('/midtrans/refresh-token/{order}', [MidtransController::class, 'refreshToken'])
    ->name('customer.midtrans.refresh-token');
Route::post('/customer/midtrans/{order:order_number}/refresh-token', [MidtransController::class, 'refreshToken'])
    ->name('customer.midtrans.refresh');

Route::get('/api/villages', [VillageController::class, 'getVillages'])->name('api.villages');
Route::post('/api/biteship/rates', [CheckoutController::class, 'getShippingCost'])
    ->name('api.biteship.rates');
Route::post('/checkout/update-shipping', [CheckoutController::class, 'updateShipping'])
    ->name('customer.checkout.update-shipping');
Route::post('/checkout/update-cart-item', [CheckoutController::class, 'updateCartItem'])
    ->name('customer.checkout.update-cart-item');

Route::post('/checkout/remove-cart-item', [CheckoutController::class, 'removeCartItem'])
    ->name('customer.checkout.remove-cart-item');
Route::get('/checkout/get-total', [CheckoutController::class, 'getTotal'])
    ->name('customer.checkout.get-total');
Route::get('/checkout/vouchers-ajax', [App\Http\Controllers\Customer\CheckoutController::class, 'getAvailableVouchers'])->name('customer.checkout.vouchers-ajax');
    
// Route::prefix('api/biteship')->group(function () {
//     Route::post('/get-shipping-cost', [CheckoutController::class, 'getShippingCost'])
//         ->name('api.biteship.shipping-cost');
//     Route::get('/search-location', [CheckoutController::class, 'searchLocation'])
//         ->name('api.biteship.search-location');
// });

// ============================================
// CUSTOMER ACCOUNT (with auth middleware)
// ============================================

Route::middleware(['customer'])->group(function () {

    Route::get('/akun', [AccountController::class, 'index'])->name('customer.account');
    Route::put('/akun/profil', [AccountController::class, 'updateProfile'])->name('customer.profile.update');
    Route::get('/akun/ganti-kata-sandi', [AccountController::class, 'showChangePasswordForm'])->name('customer.password.change');
    Route::put('/akun/ganti-kata-sandi', [AccountController::class, 'updatePassword'])->name('customer.password.update');

    Route::get('/akun/pesanan', [AccountController::class, 'orders'])->name('customer.orders');
    Route::get('/akun/pesanan/{order}/tracking', [AccountController::class, 'tracking'])->name('customer.orders.tracking');
    Route::post('/akun/pesanan/{order}/tracking/refresh', [AccountController::class, 'refreshTracking'])->name('customer.orders.tracking.refresh');
    Route::get('/akun/pesanan/{order}', [AccountController::class, 'showOrder'])->name('customer.orders.show');
    Route::post('/akun/pesanan/{order}/request-cancel', [AccountController::class, 'requestCancellation'])
        ->name('customer.orders.request-cancel');
    Route::post('/akun/pesanan/{order}/cancel-direct', [AccountController::class, 'cancelOrderDirect'])
        ->name('customer.orders.cancel-direct');
    Route::post('/akun/pesanan/{order}/request-return', [AccountController::class, 'requestReturn'])
        ->name('customer.orders.request-return');
    Route::post('/akun/pesanan/{order}/confirm-received', [AccountController::class, 'confirmReceived'])
        ->name('customer.orders.confirm-received');

    Route::get('/akun/alamat', [AccountController::class, 'indexAddresses'])->name('customer.addresses.index');
    Route::get('/akun/alamat/tambah', [AccountController::class, 'createAddress'])->name('customer.addresses.create');
    Route::post('/akun/alamat', [AccountController::class, 'storeAddress'])->name('customer.addresses.store');
    Route::get('/akun/alamat/{address}/edit', [AccountController::class, 'editAddress'])->name('customer.addresses.edit');
    Route::put('/akun/alamat/{address}', [AccountController::class, 'updateAddress'])->name('customer.addresses.update');
    Route::delete('/akun/alamat/{address}', [AccountController::class, 'destroyAddress'])->name('customer.addresses.destroy');

    Route::get('/vouchers', [CustomerVoucherController::class, 'index'])->name('customer.vouchers.index');
    Route::get('/vouchers/{voucher}', [CustomerVoucherController::class, 'show'])->name('customer.vouchers.show'); 
    Route::post('/vouchers/{voucher}/use', [CustomerVoucherController::class, 'use'])->name('customer.vouchers.use');
    Route::delete('/vouchers/remove', [CustomerVoucherController::class, 'remove'])->name('customer.vouchers.remove');

    // TESTIMONIAL
    Route::get('/akun/testimoni', [CustomerTestimonialController::class, 'index'])
        ->name('customer.testimonials.index');
    Route::get('/akun/testimonial/{order}', [CustomerTestimonialController::class, 'create'])
        ->name('customer.testimonial.create');
    Route::post('/akun/testimonial/{order}', [CustomerTestimonialController::class, 'store'])
        ->name('customer.testimonial.store');

});

Route::post('/checkout/save-data', [CheckoutController::class, 'saveCheckoutData'])
    ->name('customer.checkout.save-data');

// 🔥 Checkout address endpoints (logged-in customer only)
Route::middleware(['customer'])->group(function () {
    Route::get('/checkout/addresses', [CheckoutController::class, 'getAddresses'])
        ->name('customer.checkout.addresses');
    Route::post('/checkout/save-address', [CheckoutController::class, 'saveCheckoutAddress'])
        ->name('customer.checkout.save-address');
});

// ============================================
// ADMIN
// ============================================

Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    Route::get('/dashboard', [ProductController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/dashboard/chart-data', [ProductController::class, 'getChartData'])
        ->name('admin.dashboard.chart-data');
    
    Route::get('/dashboard/export', [DashboardExportController::class, 'export'])->name('admin.dashboard.export');

    // CATEGORY
    Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('admin.categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('admin.categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');

    // PRODUCT
    Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('admin.products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/bulk-delete', [ProductController::class, 'bulkDestroy'])->name('admin.products.bulk-destroy');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
    Route::delete('/products/{product}/images/{image}', [ProductController::class, 'destroyImage'])->name('admin.products.images.destroy');
    Route::post('/products/check-sku', [ProductController::class, 'checkSku'])->name('admin.products.check-sku');

    Route::get('/products/{product}/stock-history', [ProductController::class, 'stockHistory'])
        ->name('admin.products.stock-history');

    Route::get('/stock', [StockController::class, 'index'])->name('admin.stock.index');
    Route::get('/stock/{product}/edit', [StockController::class, 'edit'])->name('admin.stock.edit');
    Route::put('/stock/{product}', [StockController::class, 'update'])->name('admin.stock.update');
    Route::put('/stock/{variant}/update-single', [StockController::class, 'updateSingle'])->name('admin.stock.update-single');
    Route::post('/stock/bulk', [StockController::class, 'bulkUpdate'])->name('admin.stock.bulk');
    Route::get('/stock/history/{product}', [StockController::class, 'history'])->name('admin.stock.history');
    Route::get('/stock/history-variant/{variant}', [StockController::class, 'variantHistory'])->name('admin.stock.history-variant');

    // BANNER
    Route::put('/banners/promo-bar', [BannerController::class, 'updatePromoBar'])->name('admin.banners.promo-bar.update');
    Route::get('/banners', [BannerController::class, 'index'])->name('admin.banners.index');
    Route::get('/banners/create', [BannerController::class, 'create'])->name('admin.banners.create');
    Route::post('/banners', [BannerController::class, 'store'])->name('admin.banners.store');
    Route::get('/banners/{banner}/edit', [BannerController::class, 'edit'])->name('admin.banners.edit');
    Route::put('/banners/{banner}', [BannerController::class, 'update'])->name('admin.banners.update');
    Route::delete('/banners/{banner}', [BannerController::class, 'destroy'])->name('admin.banners.destroy');

    Route::get('/password-requests', [PasswordResetController::class, 'index'])
        ->name('admin.password-requests.index');
    Route::get('/password-requests/{passwordRequest}', [PasswordResetController::class, 'show'])
        ->name('admin.password-requests.show');
    Route::post('/customers/password-requests/{passwordRequest}/approve', 
        [UserController::class, 'approveReset']
    )->name('admin.customers.password-approve');
    Route::post('/customers/password-requests/{passwordRequest}/reject', 
        [UserController::class, 'rejectReset']
    )->name('admin.customers.password-reject');
    Route::delete('/password-requests/{passwordRequest}', [PasswordResetController::class, 'destroy'])
        ->name('admin.password-requests.destroy');
    

    // MARKETPLACE
    Route::get('/marketplaces', [MarketplaceController::class, 'index'])->name('admin.marketplaces.index');
    Route::get('/marketplaces/create', [MarketplaceController::class, 'create'])->name('admin.marketplaces.create');
    Route::post('/marketplaces', [MarketplaceController::class, 'store'])->name('admin.marketplaces.store');
    Route::get('/marketplaces/{marketplace}/edit', [MarketplaceController::class, 'edit'])->name('admin.marketplaces.edit');
    Route::put('/marketplaces/{marketplace}', [MarketplaceController::class, 'update'])->name('admin.marketplaces.update');
    Route::delete('/marketplaces/{marketplace}', [MarketplaceController::class, 'destroy'])->name('admin.marketplaces.destroy');

    // SETTINGS
    Route::get('/settings', [SettingController::class, 'edit'])->name('admin.settings.edit');
    Route::put('/settings', [SettingController::class, 'update'])->name('admin.settings.update');

    // ORDERS
    Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('admin.orders.show');
    Route::put('/orders/{order}/shipping', [OrderController::class, 'updateShipping'])->name('admin.orders.shipping');
    Route::get('/orders/{order}/label', [OrderController::class, 'printLabel'])->name('admin.orders.label');
    Route::get('/orders/{order}/tracking', [OrderController::class, 'tracking'])->name('admin.orders.tracking');
    Route::post('/orders/{order}/tracking/refresh', [OrderController::class, 'refreshTracking'])->name('admin.orders.tracking.refresh');
    Route::post('/orders/{order}/biteship/create', [OrderController::class, 'createBiteshipOrder'])->name('admin.orders.biteship.create');
    Route::post('/orders/{order}/approve-cancellation', [OrderController::class, 'approveCancellation'])
        ->name('admin.orders.approve-cancellation');
    Route::post('/orders/{order}/reject-cancellation', [OrderController::class, 'rejectCancellation'])
        ->name('admin.orders.reject-cancellation');
    Route::post('/orders/{order}/approve-return', [OrderController::class, 'approveReturn'])
        ->name('admin.orders.approve-return');
    Route::post('/orders/{order}/reject-return', [OrderController::class, 'rejectReturn'])
        ->name('admin.orders.reject-return');
    Route::post('/orders/bulk-ship', [OrderController::class, 'bulkShip'])->name('admin.orders.bulk-ship');
    Route::post('/orders/bulk-print-label', [OrderController::class, 'bulkPrintLabel'])->name('admin.orders.bulk-print-label');

    // 🔥 OFFLINE ORDERS
    Route::get('/offline', [OfflineOrderController::class, 'index'])->name('admin.orders.offline');
    Route::post('/offline/create', [OfflineOrderController::class, 'createOrder'])->name('admin.orders.offline.create');
    Route::get('/offline/receipt/{order}', [OfflineOrderController::class, 'printReceipt'])->name('admin.orders.offline.receipt');
    Route::get('/offline/faktur/{order}', [OfflineOrderController::class, 'printFaktur'])->name('admin.orders.offline.faktur');

    // 🔥 RETURNS MANAGEMENT
    Route::get('/returns', [ReturnController::class, 'index'])->name('admin.returns.index');
    Route::get('/returns/{order}', [ReturnController::class, 'show'])->name('admin.returns.show');
    Route::post('/returns/{order}/restore', [ReturnController::class, 'restoreStock'])
        ->name('admin.returns.restore');

    Route::post('/features', [FeatureController::class, 'store'])->name('admin.features.store');

    // 🔥 LAPORAN (REPORTS)
    Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/reports/orders', [ReportController::class, 'orders'])->name('admin.reports.orders');
    Route::get('/reports/products', [ReportController::class, 'products'])->name('admin.reports.products');
    Route::get('/reports/customers', [ReportController::class, 'customers'])->name('admin.reports.customers');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('admin.reports.export');

    Route::get('/articles', [ArticleController::class, 'index'])->name('admin.articles.index');
    Route::get('/articles/create', [ArticleController::class, 'create'])->name('admin.articles.create');
    Route::post('/articles', [ArticleController::class, 'store'])->name('admin.articles.store');
    Route::get('/articles/{article}/edit', [ArticleController::class, 'edit'])->name('admin.articles.edit');
    Route::put('/articles/{article}', [ArticleController::class, 'update'])->name('admin.articles.update');
    Route::delete('/articles/{article}', [ArticleController::class, 'destroy'])->name('admin.articles.destroy');

    // 🔥 ARTICLE CATEGORIES AJAX
    Route::get('/article-categories/ajax', [ArticleCategoryAjaxController::class, 'index'])->name('admin.article-categories.ajax');
    Route::post('/article-categories/ajax', [ArticleCategoryAjaxController::class, 'store'])->name('admin.article-categories.ajax.store');
    Route::delete('/article-categories/ajax/{id}', [ArticleCategoryAjaxController::class, 'destroy'])->name('admin.article-categories.ajax.destroy');

    Route::patch('faqs/{faq}/toggle', [FaqController::class, 'toggle'])->name('admin.faqs.toggle');
    Route::get('/faqs', [FaqController::class, 'index'])->name('admin.faqs.index');
    Route::get('/faqs/create', [FaqController::class, 'create'])->name('admin.faqs.create');
    Route::post('/faqs', [FaqController::class, 'store'])->name('admin.faqs.store');
    Route::get('/faqs/{faq}/edit', [FaqController::class, 'edit'])->name('admin.faqs.edit');
    Route::put('/faqs/{faq}', [FaqController::class, 'update'])->name('admin.faqs.update');
    Route::delete('/faqs/{faq}', [FaqController::class, 'destroy'])->name('admin.faqs.destroy');

    Route::patch('vouchers/{voucher}/toggle-status', [VoucherController::class, 'toggleStatus'])->name('vouchers.toggle-status');
    Route::get('/vouchers', [VoucherController::class, 'index'])->name('admin.vouchers.index');
    Route::get('/vouchers/create', [VoucherController::class, 'create'])->name('admin.vouchers.create');
    Route::post('/vouchers', [VoucherController::class, 'store'])->name('admin.vouchers.store');
    Route::get('/vouchers/{voucher}/edit', [VoucherController::class, 'edit'])->name('admin.vouchers.edit');
    Route::put('/vouchers/{voucher}', [VoucherController::class, 'update'])->name('admin.vouchers.update');
    Route::delete('/vouchers/{voucher}', [VoucherController::class, 'destroy'])->name('admin.vouchers.destroy');

    // TESTIMONIAL
    Route::get('/testimonials', [TestimonialController::class, 'index'])->name('admin.testimonials.index');
    Route::get('/testimonials/create', [TestimonialController::class, 'create'])->name('admin.testimonials.create');
    Route::post('/testimonials', [TestimonialController::class, 'store'])->name('admin.testimonials.store');
    Route::get('/testimonials/{testimonial}', [TestimonialController::class, 'show'])->name('admin.testimonials.show');
    Route::get('/testimonials/{testimonial}/edit', [TestimonialController::class, 'edit'])->name('admin.testimonials.edit');
    Route::put('/testimonials/{testimonial}', [TestimonialController::class, 'update'])->name('admin.testimonials.update');
    Route::delete('/testimonials/{testimonial}', [TestimonialController::class, 'destroy'])->name('admin.testimonials.destroy');
    Route::delete('/testimonials/{testimonial}/images/{image}', [TestimonialController::class, 'destroyImage'])->name('admin.testimonials.images.destroy');
    Route::patch('/testimonials/{testimonial}/toggle', [TestimonialController::class, 'toggle'])->name('admin.testimonials.toggle');
    Route::patch('/testimonials/{testimonial}/verify', [TestimonialController::class, 'verify'])->name('admin.testimonials.verify');

    Route::get('/faqs/categories', [FaqCategoryController::class, 'index'])->name('admin.faqs.categories');
    Route::post('/faqs/categories', [FaqCategoryController::class, 'store'])->name('admin.faqs.categories.store');
    Route::delete('/faqs/categories/{category}', [FaqCategoryController::class, 'destroy'])->name('admin.faqs.categories.destroy');

    Route::get('/terms', [TermController::class, 'index'])->name('admin.terms.index');
    Route::put('/terms', [TermController::class, 'update'])->name('admin.terms.update');
    Route::patch('/terms/toggle', [TermController::class, 'toggle'])->name('admin.terms.toggle');

    Route::get('/privacy', [PrivacyPolicyController::class, 'index'])->name('admin.privacy.index');
    Route::put('/privacy', [PrivacyPolicyController::class, 'update'])->name('admin.privacy.update');
    Route::patch('/privacy/toggle', [PrivacyPolicyController::class, 'toggle'])->name('admin.privacy.toggle');

    Route::get('/about', [AboutUsController::class, 'index'])->name('admin.about.index');
    Route::put('/about', [AboutUsController::class, 'update'])->name('admin.about.update');
    Route::patch('/about/toggle', [AboutUsController::class, 'toggle'])->name('admin.about.toggle');

    // USERS / CUSTOMERS
    Route::get('/customers', [UserController::class, 'index'])->name('admin.customers.index');
    Route::get('/customers/{user}', [UserController::class, 'show'])->name('admin.customers.show');
    Route::patch('/customers/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('admin.customers.toggle-status');
    Route::delete('/customers/{user}', [UserController::class, 'destroy'])->name('admin.customers.destroy');

    Route::get('/account', [AccountAdminController::class, 'edit'])->name('admin.account.edit');
    Route::put('/account', [AccountAdminController::class, 'update'])->name('admin.account.update');
    Route::put('/account/password', [AccountAdminController::class, 'updatePassword'])->name('admin.account.password');
});
