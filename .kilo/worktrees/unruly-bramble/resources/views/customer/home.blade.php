@extends('layouts.customer')

@section('title', 'Barokah Sport')

@section('content')

<div class="banner_slide">
    <div class="swiper mySwiper">
        <div class="swiper-wrapper">
            @forelse($banners as $banner)
                <div class="swiper-slide">
                    <div class="slide_box" 
                         style="background-image:url('{{ Storage::url($banner->image) }}'); 
                                background-position:center; 
                                background-size:cover;">
                        
                        @if($banner->title)
                            <h2>{{ $banner->title }}</h2>
                        @endif
                        
                        @if($banner->subtitle)
                            <p>{{ $banner->subtitle }}</p>
                        @endif
                        
                        @if($banner->button_text && $banner->button_url)
                            <div class="banner_button">
                                <a href="{{ $banner->button_url }}">
                                    <button>{{ $banner->button_text }}</button>
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="slide_box slide_box_mobile" 
                         style="background-image:url('{{ Storage::url($banner->image_mobile) }}'); 
                                background-position:center; 
                                background-size:cover;">
                        
                        @if($banner->title)
                            <h2>{{ $banner->title }}</h2>
                        @endif
                        
                        @if($banner->subtitle)
                            <p>{{ $banner->subtitle }}</p>
                        @endif
                        
                        @if($banner->button_text && $banner->button_url)
                            <div class="banner_button">
                                <a href="{{ $banner->button_url }}">
                                    <button>{{ $banner->button_text }}</button>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                {{-- Default banner jika tidak ada data --}}
                <div class="swiper-slide">
                    <div class="slide_box slide_box_dummy" style="background-image:url({{ asset('images/slide_img.webp') }}); background-position:center; background-size:cover;">
                        <h2>Performa dan Gaya dalam <span>Satu</span> Pilihan.</h2>
                        <p>Tampil sporty dengan jaket dan celana olahraga yang nyaman, stylish, dan siap menemani setiap aktivitas.</p>
                        <div class="banner_button">
                            <a href="{{ route('customer.products.index') }}">
                                <button>Belanja Sekarang</button>
                            </a>
                            <a href="https://api.whatsapp.com/send?phone=6287866291056" target="_blank">
                                <button>Hubungi Kami</button>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="slide_box slide_box_dummy" style="background-image:url({{ asset('images/slide_img_2.png') }}); background-position:center; background-size:cover;">
                        <h2>Nyaman <span>Maksimal</span>, Bergerak Bebas.</h2>
                        <p>Dirancang dengan material ringan dan fleksibel, paduan sempurna untuk performa latihan terbaik dan gaya kasual harianmu.</p>
                        <div class="banner_button">
                            <a href="{{ route('customer.products.index') }}">
                                <button>Belanja Sekarang</button>
                            </a>
                            <a href="https://api.whatsapp.com/send?phone=6287866291056" target="_blank">
                                <button>Hubungi Kami</button>
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
        @if($banners->count() > 1)
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        @endif
    </div>
</div>

<div class="keunggulan_layout">
    <div class="keunggulan_box_layout">
        <img src="{{ asset('images/gratis_ongkir.svg') }}" alt="">
        <div class="keunggulan_box_content">
            <h3>GRATIS ONGKIR</h3>
            <p>Pembelian di atas 750.000</p>
        </div>
    </div>
    <div class="keunggulan_box_layout">
        <img src="{{ asset('images/retur_mudah.svg') }}" alt="">
        <div class="keunggulan_box_content">
            <h3>PENGEMBALIAN MUDAH</h3>
            <p>Retur mudah dalam 7 hari.</p>
        </div>
    </div>
    <div class="keunggulan_box_layout">
        <img src="{{ asset('images/safety_pay.svg') }}" alt="">
        <div class="keunggulan_box_content">
            <h3>PEMBAYARAN AMAN</h3>
            <p>Metode pembayaran terpercaya</p>
        </div>
    </div>
    <div class="keunggulan_box_layout">
        <img src="{{ asset('images/cs.svg') }}" alt="">
        <div class="keunggulan_box_content">
            <h3>BANTUAN CEPAT</h3>
            <p>24/7 Support</p>
        </div>
    </div>
</div>

<div class="kategori_layout">
    <div class="swiper categoriesSwiper">
        <div class="swiper-wrapper">
            @foreach($categories as $category)
            <div class="swiper-slide">
                <div class="kategori_box_layout" 
                    style="background-image:url('{{ $category->image ? Storage::url($category->image) : asset('images/default_category.png') }}'); 
                            background-size:100% 100%; 
                            background-position:center; cursor:pointer;"
                    onclick="window.location.href='{{ route('customer.products.index', ['category' => $category->id]) }}'">
                    <h3>{{ $category->name }}</h3>
                    <a href="{{ route('customer.products.index', ['category' => $category->id]) }}" 
                       onclick="event.stopPropagation();">
                       BELI SEKARANG
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>
</div>

<div class="product_layout">
    <div class="heading_product_layout">
        <h3>KOLEKSI TERBARU</h3>
        <a href="{{ route('customer.products.latest') }}">LIHAT SEMUA</a>
    </div>
    <div class="product_layout_grid product-layout-grid--show-5-desktop">
        @forelse($latestProducts as $product)
        @php
            $hasDiscount = $product->has_discount ?? false;
            $maxDiscountPercent = $product->max_discount_percent ?? 0;
            $hasProductDiscount = $product->has_product_discount ?? false;
            $productDiscountPercent = $product->product_discount_percent ?? 0;
            $badgeLabel = $product->badge_label ?? null;
            
            $maxDiscount = 0;
            foreach ($product->variants as $variant) {
                $discount = $variant->discount_percent ?? 0;
                if ($discount > $maxDiscount) {
                    $maxDiscount = $discount;
                }
            }
            
            if ($maxDiscount == 0 && $hasProductDiscount) {
                $maxDiscount = $productDiscountPercent;
            }
            
            $minEffectivePrice = $product->min_effective_price ?? null;
            $maxOriginalPrice = $product->max_price ?? null;
            $minOriginalPrice = $product->variants->min('price') ?? 0;
            $maxOriginalPrice = $product->variants->max('price') ?? 0;
            
            $discountedPriceDisplay = '';
            $originalPriceDisplay = '';
            
            if ($minEffectivePrice !== null && $maxOriginalPrice !== null) {
                if ($minEffectivePrice == $maxOriginalPrice) {
                    $discountedPriceDisplay = 'Rp ' . number_format($minEffectivePrice, 0, ',', '.');
                } else {
                    $discountedPriceDisplay = 'Rp ' . number_format($minEffectivePrice, 0, ',', '.') . ' - Rp ' . number_format($maxOriginalPrice, 0, ',', '.');
                }
                
                if ($minOriginalPrice == $maxOriginalPrice) {
                    $originalPriceDisplay = 'Rp ' . number_format($minOriginalPrice, 0, ',', '.');
                } else {
                    $originalPriceDisplay = 'Rp ' . number_format($minOriginalPrice, 0, ',', '.') . ' - Rp ' . number_format($maxOriginalPrice, 0, ',', '.');
                }
            } else {
                $prices = [];
                foreach ($product->variants as $variant) {
                    $prices[] = $variant->effective_price ?? $variant->price;
                }
                $minEffective = min($prices);
                $maxEffective = max($prices);
                $discountedPriceDisplay = 'Rp ' . number_format($minEffective, 0, ',', '.');
                if ($minEffective != $maxEffective) {
                    $discountedPriceDisplay .= ' - Rp ' . number_format($maxEffective, 0, ',', '.');
                }
                
                $originalPrices = $product->variants->pluck('price')->toArray();
                $minOrig = min($originalPrices);
                $maxOrig = max($originalPrices);
                $originalPriceDisplay = 'Rp ' . number_format($minOrig, 0, ',', '.');
                if ($minOrig != $maxOrig) {
                    $originalPriceDisplay .= ' - Rp ' . number_format($maxOrig, 0, ',', '.');
                }
            }
            
            $isOutOfStock = $product->isOutOfStock();
            $badgeText = $badgeLabel ?? '';
            
            $thumbnail = $product->thumbnail ?? null;
            if (!$thumbnail && $product->images->isNotEmpty()) {
                $thumbnail = Storage::url($product->images->first()->image);
            }
        @endphp
        <div class="product_layout_box" data-product-id="{{ $product->id }}">
            <div class="product_layout_img">
                <div class="skeleton-image-box" id="skeleton-latest-{{ $product->id }}">
                    <div class="skeleton" style="width:100%;height:100%;"></div>
                </div>
                <a href="{{ route('customer.products.show', $product->slug) }}">
                    @if($thumbnail)
                        <img src="{{ $thumbnail }}" 
                            alt="{{ $product->name }}"
                            loading="lazy"
                            width="300"
                            height="300"
                            decoding="async"
                            class="loading"
                            onload="this.classList.remove('loading'); this.classList.add('loaded'); document.getElementById('skeleton-latest-{{ $product->id }}').classList.add('hidden');"
                            onerror="this.onerror=null; this.src='{{ asset('images/placeholder.png') }}'; this.classList.remove('loading'); this.classList.add('loaded'); document.getElementById('skeleton-latest-{{ $product->id }}').classList.add('hidden');">
                    @else
                        <div class="placeholder">
                            <iconify-icon icon="mdi:image-off-outline"></iconify-icon>
                        </div>
                        <script>
                            document.getElementById('skeleton-latest-{{ $product->id }}')?.classList.add('hidden');
                        </script>
                    @endif
                </a>
                @if($isOutOfStock)
                <span class="product_badge out-of-stock">HABIS</span>
                @endif
                
                @if($maxDiscount > 0)
                    <span class="discount_badge">Diskon {{ round($maxDiscount) }}%</span>
                @endif
                
                @if(!empty($badgeText) && $maxDiscount == 0)
                    <span class="discount_badge">{{ $badgeText }}</span>
                @endif
            </div>
            <div class="product_layout_content">
                <h5>{{ $product->name }}</h5>
                <div class="product_layout_price">
                    @if($maxDiscount > 0)
                        <div class="product_layout_price_box">
                            <p class="price-discount">{{ $discountedPriceDisplay }}</p>
                            <span class="price-original">{{ $originalPriceDisplay }}</span>
                        </div>
                    @else
                        <p>{{ $discountedPriceDisplay }}</p>
                    @endif
                </div>
            </div>
            <div class="product_layout_button">
                <button class="buy_now_btn {{ $isOutOfStock ? 'disabled' : '' }}" 
                        onclick="{{ $isOutOfStock ? '' : 'buyNow(' . $product->id . ')' }}"
                        {{ $isOutOfStock ? 'disabled' : '' }}>
                    {{ $isOutOfStock ? 'HABIS' : 'BELI SEKARANG' }}
                </button>
                <button class="add_to_cart_btn {{ $isOutOfStock ? 'disabled' : '' }}" 
                        onclick="{{ $isOutOfStock ? '' : 'addToCart(' . $product->id . ')' }}"
                        {{ $isOutOfStock ? 'disabled' : '' }}>
                    <iconify-icon icon="solar:cart-linear"></iconify-icon>
                </button>
                <button class="add_to_wishlist_btn" 
                        data-product-id="{{ $product->id }}"
                        data-in-wishlist="{{ in_array($product->id, array_keys(session()->get('wishlist', []))) ? 'true' : 'false' }}"
                        onclick="addToWishlist({{ $product->id }})">
                    @if(in_array($product->id, array_keys(session()->get('wishlist', []))))
                        <iconify-icon icon="solar:heart-bold" style="color: #ef4444;"></iconify-icon>
                    @else
                        <iconify-icon icon="solar:heart-linear"></iconify-icon>
                    @endif
                </button>
            </div>
        </div>
        @empty
        <div class="empty_state">
            <p>Belum ada produk terbaru</p>
        </div>
        @endforelse
    </div>
</div>

{{-- ============================================ --}}
{{-- FLASH SALE --}}
{{-- ============================================ --}}
@if($flashSaleProducts->isNotEmpty())
<div class="flash_sale_section">
    <img src="{{ asset('images/flash_sale_logo.svg') }}" class="flash_sale_logo" alt="Flash Sale">
    <div class="heading_product_layout heading_product_layout_flash">
        <div class="flash_sale_time" id="flash-sale-timer">
            <div class="timer-countdown">
                <div class="timer-countdown-box">
                    <div id="flash-sale-days">{{ str_pad($combinedFlashSaleDuration['days'], 2, '0', STR_PAD_LEFT) }}</div> Hari :
                </div>
                <div class="timer-countdown-box">
                    <div id="flash-sale-hours">{{ str_pad($combinedFlashSaleDuration['hours'], 2, '0', STR_PAD_LEFT) }}</div> Jam :
                </div>
                <div class="timer-countdown-box">
                    <div id="flash-sale-minutes">{{ str_pad($combinedFlashSaleDuration['minutes'], 2, '0', STR_PAD_LEFT) }}</div> Menit :
                </div>
                <div class="timer-countdown-box">
                    <div id="flash-sale-seconds">{{ str_pad($combinedFlashSaleDuration['seconds'], 2, '0', STR_PAD_LEFT) }}</div> Detik
                </div>
            </div>
        </div>
        <a href="{{ route('customer.products.flash-sale') }}">LIHAT SEMUA</a>
    </div>
    <div class="product_layout_grid product_layout_grid_4">
        @foreach($flashSaleProducts as $product)
        @php
            $hasDiscount = $product->has_discount ?? false;
            $maxDiscountPercent = $product->max_discount_percent ?? 0;
            $hasProductDiscount = $product->has_product_discount ?? false;
            $productDiscountPercent = $product->product_discount_percent ?? 0;
            
            $maxDiscount = 0;
            foreach ($product->variants as $variant) {
                $discount = $variant->discount_percent ?? 0;
                if ($discount > $maxDiscount) {
                    $maxDiscount = $discount;
                }
            }
            
            if ($maxDiscount == 0 && $hasProductDiscount) {
                $maxDiscount = $productDiscountPercent;
            }
            
            $flashSaleDiscount = 0;
            if ($product->is_flash_sale && $product->flash_sale_value > 0) {
                if ($product->flash_sale_type == 'percentage') {
                    $flashSaleDiscount = $product->flash_sale_value;
                } else {
                    $minPrice = $product->variants->min('price') ?? 0;
                    if ($minPrice > 0) {
                        $flashSaleDiscount = round(($product->flash_sale_value / $minPrice) * 100);
                    }
                }
            }
            
            $minEffectivePrice = null;
            $minOriginalPrice = 0;
            $maxOriginalPrice = 0;
            
            foreach ($product->variants as $variant) {
                $effPrice = $variant->effective_price ?? $variant->price;
                if ($minEffectivePrice === null || $effPrice < $minEffectivePrice) {
                    $minEffectivePrice = $effPrice;
                }
                if ($variant->price < $minOriginalPrice || $minOriginalPrice == 0) {
                    $minOriginalPrice = $variant->price;
                }
                if ($variant->price > $maxOriginalPrice) {
                    $maxOriginalPrice = $variant->price;
                }
            }
            
            $discountedPriceDisplay = 'Rp ' . number_format($minEffectivePrice ?? $minOriginalPrice, 0, ',', '.');
            $originalPriceDisplay = 'Rp ' . number_format($minOriginalPrice, 0, ',', '.');
            if ($minOriginalPrice != $maxOriginalPrice) {
                $originalPriceDisplay = 'Rp ' . number_format($minOriginalPrice, 0, ',', '.') . ' - Rp ' . number_format($maxOriginalPrice, 0, ',', '.');
            }
            
            $isOutOfStock = $product->isOutOfStock();
            $badgeText = '⚡ Flash Sale';
            if ($flashSaleDiscount > 0) {
                $badgeText = '⚡ Flash Sale ' . round($flashSaleDiscount) . '%';
            }
            
            $thumbnail = $product->thumbnail ?? null;
            if (!$thumbnail && $product->images->isNotEmpty()) {
                $thumbnail = Storage::url($product->images->first()->image);
            }
        @endphp
        <div class="product_layout_box product_layout_box_flash" data-product-id="{{ $product->id }}">
            <div class="product_layout_img">
                <div class="skeleton-image-box" id="skeleton-flash-{{ $product->id }}">
                    <div class="skeleton" style="width:100%;height:100%;"></div>
                </div>
                <a href="{{ route('customer.products.show', $product->slug) }}">
                    @if($thumbnail)
                        <img src="{{ $thumbnail }}" 
                            alt="{{ $product->name }}"
                            loading="lazy"
                            width="300"
                            height="300"
                            decoding="async"
                            class="loading"
                            onload="this.classList.remove('loading'); this.classList.add('loaded'); document.getElementById('skeleton-flash-{{ $product->id }}').classList.add('hidden');"
                            onerror="this.onerror=null; this.src='{{ asset('images/placeholder.png') }}'; this.classList.remove('loading'); this.classList.add('loaded'); document.getElementById('skeleton-flash-{{ $product->id }}').classList.add('hidden');">
                    @else
                        <div class="placeholder">
                            <iconify-icon icon="mdi:image-off-outline"></iconify-icon>
                        </div>
                        <script>
                            document.getElementById('skeleton-flash-{{ $product->id }}')?.classList.add('hidden');
                        </script>
                    @endif
                </a>
                @if($isOutOfStock)
                <span class="product_badge out-of-stock">HABIS</span>
                @endif
                
                @if($product->is_flash_sale)
                    <span class="discount_badge flash-sale-badge">{{ $badgeText }}</span>
                @elseif($maxDiscount > 0)
                    <span class="discount_badge">Diskon {{ round($maxDiscount) }}%</span>
                @endif
            </div>
            <div class="product_layout_content">
                <h5>{{ $product->name }}</h5>
                <div class="product_layout_price">
                    @if($maxDiscount > 0 || $product->is_flash_sale)
                        <div class="product_layout_price_box">
                            <p class="price-discount flash-sale-price">{{ $discountedPriceDisplay }}</p>
                            <span class="price-original">{{ $originalPriceDisplay }}</span>
                        </div>
                    @else
                        <p>{{ $discountedPriceDisplay }}</p>
                    @endif
                </div>
            </div>
            <div class="product_layout_button">
                <button class="buy_now_btn {{ $isOutOfStock ? 'disabled' : '' }}" 
                        onclick="{{ $isOutOfStock ? '' : 'buyNow(' . $product->id . ')' }}"
                        {{ $isOutOfStock ? 'disabled' : '' }}>
                    {{ $isOutOfStock ? 'HABIS' : 'BELI SEKARANG' }}
                </button>
                <button class="add_to_cart_btn {{ $isOutOfStock ? 'disabled' : '' }}" 
                        onclick="{{ $isOutOfStock ? '' : 'addToCart(' . $product->id . ')' }}"
                        {{ $isOutOfStock ? 'disabled' : '' }}>
                    <iconify-icon icon="solar:cart-linear"></iconify-icon>
                </button>
                <button class="add_to_wishlist_btn" 
                        data-product-id="{{ $product->id }}"
                        data-in-wishlist="{{ in_array($product->id, array_keys(session()->get('wishlist', []))) ? 'true' : 'false' }}"
                        onclick="addToWishlist({{ $product->id }})">
                    @if(in_array($product->id, array_keys(session()->get('wishlist', []))))
                        <iconify-icon icon="solar:heart-bold" style="color: #ef4444;"></iconify-icon>
                    @else
                        <iconify-icon icon="solar:heart-linear"></iconify-icon>
                    @endif
                </button>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- ============================================ --}}
{{-- PRODUK UNGGULAN --}}
{{-- ============================================ --}}
<div class="product_layout">
    <div class="heading_product_layout">
        <h3>PRODUK UNGGULAN</h3>
        <a href="{{ route('customer.products.index') }}">LIHAT SEMUA</a>
    </div>
    <div class="product_layout_grid product-layout-grid--show-5-desktop">
        @forelse($featuredProducts as $product)
        @php
            $hasDiscount = $product->has_discount ?? false;
            $maxDiscountPercent = $product->max_discount_percent ?? 0;
            $hasProductDiscount = $product->has_product_discount ?? false;
            $productDiscountPercent = $product->product_discount_percent ?? 0;
            $badgeLabel = $product->badge_label ?? null;
            
            $maxDiscount = 0;
            foreach ($product->variants as $variant) {
                $discount = $variant->discount_percent ?? 0;
                if ($discount > $maxDiscount) {
                    $maxDiscount = $discount;
                }
            }
            
            if ($maxDiscount == 0 && $hasProductDiscount) {
                $maxDiscount = $productDiscountPercent;
            }
            
            $minEffectivePrice = $product->min_effective_price ?? null;
            $maxOriginalPrice = $product->max_price ?? null;
            $minOriginalPrice = $product->variants->min('price') ?? 0;
            $maxOriginalPrice = $product->variants->max('price') ?? 0;
            
            $discountedPriceDisplay = '';
            $originalPriceDisplay = '';
            
            if ($minEffectivePrice !== null && $maxOriginalPrice !== null) {
                if ($minEffectivePrice == $maxOriginalPrice) {
                    $discountedPriceDisplay = 'Rp ' . number_format($minEffectivePrice, 0, ',', '.');
                } else {
                    $discountedPriceDisplay = 'Rp ' . number_format($minEffectivePrice, 0, ',', '.') . ' - Rp ' . number_format($maxOriginalPrice, 0, ',', '.');
                }
                
                if ($minOriginalPrice == $maxOriginalPrice) {
                    $originalPriceDisplay = 'Rp ' . number_format($minOriginalPrice, 0, ',', '.');
                } else {
                    $originalPriceDisplay = 'Rp ' . number_format($minOriginalPrice, 0, ',', '.') . ' - Rp ' . number_format($maxOriginalPrice, 0, ',', '.');
                }
            } else {
                $prices = [];
                foreach ($product->variants as $variant) {
                    $prices[] = $variant->effective_price ?? $variant->price;
                }
                $minEffective = min($prices);
                $maxEffective = max($prices);
                $discountedPriceDisplay = 'Rp ' . number_format($minEffective, 0, ',', '.');
                if ($minEffective != $maxEffective) {
                    $discountedPriceDisplay .= ' - Rp ' . number_format($maxEffective, 0, ',', '.');
                }
                
                $originalPrices = $product->variants->pluck('price')->toArray();
                $minOrig = min($originalPrices);
                $maxOrig = max($originalPrices);
                $originalPriceDisplay = 'Rp ' . number_format($minOrig, 0, ',', '.');
                if ($minOrig != $maxOrig) {
                    $originalPriceDisplay .= ' - Rp ' . number_format($maxOrig, 0, ',', '.');
                }
            }
            
            $isOutOfStock = $product->isOutOfStock();
            $badgeText = $badgeLabel ?? '';
            
            $thumbnail = $product->thumbnail ?? null;
            if (!$thumbnail && $product->images->isNotEmpty()) {
                $thumbnail = Storage::url($product->images->first()->image);
            }
        @endphp
        <div class="product_layout_box" data-product-id="{{ $product->id }}">
            <div class="product_layout_img">
                <div class="skeleton-image-box" id="skeleton-featured-{{ $product->id }}">
                    <div class="skeleton" style="width:100%;height:100%;"></div>
                </div>
                <a href="{{ route('customer.products.show', $product->slug) }}">
                    @if($thumbnail)
                        <img src="{{ $thumbnail }}" 
                            alt="{{ $product->name }}"
                            loading="lazy"
                            width="300"
                            height="300"
                            decoding="async"
                            class="loading"
                            onload="this.classList.remove('loading'); this.classList.add('loaded'); document.getElementById('skeleton-featured-{{ $product->id }}').classList.add('hidden');"
                            onerror="this.onerror=null; this.src='{{ asset('images/placeholder.png') }}'; this.classList.remove('loading'); this.classList.add('loaded'); document.getElementById('skeleton-featured-{{ $product->id }}').classList.add('hidden');">
                    @else
                        <div class="placeholder">
                            <iconify-icon icon="mdi:image-off-outline"></iconify-icon>
                        </div>
                        <script>
                            document.getElementById('skeleton-featured-{{ $product->id }}')?.classList.add('hidden');
                        </script>
                    @endif
                </a>
                @if($isOutOfStock)
                <span class="product_badge out-of-stock">HABIS</span>
                @endif
                
                @if($maxDiscount > 0)
                    <span class="discount_badge">Diskon {{ round($maxDiscount) }}%</span>
                @endif
                
                @if(!empty($badgeText) && $maxDiscount == 0)
                    <span class="discount_badge">{{ $badgeText }}</span>
                @endif
            </div>
            <div class="product_layout_content">
                <h5>{{ $product->name }}</h5>
                <div class="product_layout_price">
                    @if($maxDiscount > 0)
                        <div class="product_layout_price_box">
                            <p class="price-discount">{{ $discountedPriceDisplay }}</p>
                            <span class="price-original">{{ $originalPriceDisplay }}</span>
                        </div>
                    @else
                        <p>{{ $discountedPriceDisplay }}</p>
                    @endif
                </div>
            </div>
            <div class="product_layout_button">
                <button class="buy_now_btn {{ $isOutOfStock ? 'disabled' : '' }}" 
                        onclick="{{ $isOutOfStock ? '' : 'buyNow(' . $product->id . ')' }}"
                        {{ $isOutOfStock ? 'disabled' : '' }}>
                    {{ $isOutOfStock ? 'HABIS' : 'BELI SEKARANG' }}
                </button>
                <button class="add_to_cart_btn {{ $isOutOfStock ? 'disabled' : '' }}" 
                        onclick="{{ $isOutOfStock ? '' : 'addToCart(' . $product->id . ')' }}"
                        {{ $isOutOfStock ? 'disabled' : '' }}>
                    <iconify-icon icon="solar:cart-linear"></iconify-icon>
                </button>
                <button class="add_to_wishlist_btn" 
                        data-product-id="{{ $product->id }}"
                        data-in-wishlist="{{ in_array($product->id, array_keys(session()->get('wishlist', []))) ? 'true' : 'false' }}"
                        onclick="addToWishlist({{ $product->id }})">
                    @if(in_array($product->id, array_keys(session()->get('wishlist', []))))
                        <iconify-icon icon="solar:heart-bold" style="color: #ef4444;"></iconify-icon>
                    @else
                        <iconify-icon icon="solar:heart-linear"></iconify-icon>
                    @endif
                </button>
            </div>
        </div>
        @empty
        <div class="empty_state">
            <p>Belum ada produk unggulan</p>
        </div>
        @endforelse
    </div>
</div>

{{-- ============================================ --}}
{{-- PROMO SECTION --}}
{{-- ============================================ --}}
<div class="promo_section" style="background-image:url({{ asset('images/promo_section_bg.png') }}); background-size:cover; background-position:center; background-repeat:no-repeat;">
    <div class="promo_section_content">
        <button><iconify-icon icon="mdi:fire"></iconify-icon> PROMO TERBATAS</button>
    </div>
    <h3>Harga Lebih Hemat<span>.</span></h3>
    <p>Pilihan Sporty, Harga Lebih Hemat <br/>Temukan produk favorit dengan harga spesial.</p>
    <a href="/katalog/promo">
        <button class="promo_button">Lihat Produk Promo</button>
    </a>
</div>

{{-- ============================================ --}}
{{-- PRODUK TERLARIS --}}
{{-- ============================================ --}}
<div class="product_layout">
    <div class="heading_product_layout">
        <h3>PRODUK <span>TERLARIS BULAN</span> INI</h3>
        <a href="{{ route('customer.products.index') }}">LIHAT SEMUA</a>
    </div>
    <div class="product_layout_grid product-layout-grid--show-5-desktop">
        @forelse($bestSellers as $product)
        @php
            $hasDiscount = $product->has_discount ?? false;
            $maxDiscountPercent = $product->max_discount_percent ?? 0;
            $hasProductDiscount = $product->has_product_discount ?? false;
            $productDiscountPercent = $product->product_discount_percent ?? 0;
            $badgeLabel = $product->badge_label ?? null;
            
            $maxDiscount = 0;
            foreach ($product->variants as $variant) {
                $discount = $variant->discount_percent ?? 0;
                if ($discount > $maxDiscount) {
                    $maxDiscount = $discount;
                }
            }
            
            if ($maxDiscount == 0 && $hasProductDiscount) {
                $maxDiscount = $productDiscountPercent;
            }
            
            $minEffectivePrice = $product->min_effective_price ?? null;
            $maxOriginalPrice = $product->max_price ?? null;
            $minOriginalPrice = $product->variants->min('price') ?? 0;
            $maxOriginalPrice = $product->variants->max('price') ?? 0;
            
            $discountedPriceDisplay = '';
            $originalPriceDisplay = '';
            
            if ($minEffectivePrice !== null && $maxOriginalPrice !== null) {
                if ($minEffectivePrice == $maxOriginalPrice) {
                    $discountedPriceDisplay = 'Rp ' . number_format($minEffectivePrice, 0, ',', '.');
                } else {
                    $discountedPriceDisplay = 'Rp ' . number_format($minEffectivePrice, 0, ',', '.') . ' - Rp ' . number_format($maxOriginalPrice, 0, ',', '.');
                }
                
                if ($minOriginalPrice == $maxOriginalPrice) {
                    $originalPriceDisplay = 'Rp ' . number_format($minOriginalPrice, 0, ',', '.');
                } else {
                    $originalPriceDisplay = 'Rp ' . number_format($minOriginalPrice, 0, ',', '.') . ' - Rp ' . number_format($maxOriginalPrice, 0, ',', '.');
                }
            } else {
                $prices = [];
                foreach ($product->variants as $variant) {
                    $prices[] = $variant->effective_price ?? $variant->price;
                }
                $minEffective = min($prices);
                $maxEffective = max($prices);
                $discountedPriceDisplay = 'Rp ' . number_format($minEffective, 0, ',', '.');
                if ($minEffective != $maxEffective) {
                    $discountedPriceDisplay .= ' - Rp ' . number_format($maxEffective, 0, ',', '.');
                }
                
                $originalPrices = $product->variants->pluck('price')->toArray();
                $minOrig = min($originalPrices);
                $maxOrig = max($originalPrices);
                $originalPriceDisplay = 'Rp ' . number_format($minOrig, 0, ',', '.');
                if ($minOrig != $maxOrig) {
                    $originalPriceDisplay .= ' - Rp ' . number_format($maxOrig, 0, ',', '.');
                }
            }
            
            $isOutOfStock = $product->isOutOfStock();
            $badgeText = $badgeLabel ?? '';
            
            $thumbnail = $product->thumbnail ?? null;
            if (!$thumbnail && $product->images->isNotEmpty()) {
                $thumbnail = Storage::url($product->images->first()->image);
            }
        @endphp
        <div class="product_layout_box" data-product-id="{{ $product->id }}">
            <div class="product_layout_img">
                <div class="skeleton-image-box" id="skeleton-bestseller-{{ $product->id }}">
                    <div class="skeleton" style="width:100%;height:100%;"></div>
                </div>
                <a href="{{ route('customer.products.show', $product->slug) }}">
                    @if($thumbnail)
                        <img src="{{ $thumbnail }}" 
                            alt="{{ $product->name }}"
                            loading="lazy"
                            width="300"
                            height="300"
                            decoding="async"
                            class="loading"
                            onload="this.classList.remove('loading'); this.classList.add('loaded'); document.getElementById('skeleton-bestseller-{{ $product->id }}').classList.add('hidden');"
                            onerror="this.onerror=null; this.src='{{ asset('images/placeholder.png') }}'; this.classList.remove('loading'); this.classList.add('loaded'); document.getElementById('skeleton-bestseller-{{ $product->id }}').classList.add('hidden');">
                    @else
                        <div class="placeholder">
                            <iconify-icon icon="mdi:image-off-outline"></iconify-icon>
                        </div>
                        <script>
                            document.getElementById('skeleton-bestseller-{{ $product->id }}')?.classList.add('hidden');
                        </script>
                    @endif
                </a>
                @if($isOutOfStock)
                <span class="product_badge out-of-stock">HABIS</span>
                @endif
                
                @if($maxDiscount > 0)
                    <span class="discount_badge">Diskon {{ round($maxDiscount) }}%</span>
                @endif
                
                @if(!empty($badgeText) && $maxDiscount == 0)
                    <span class="discount_badge">{{ $badgeText }}</span>
                @endif
            </div>
            <div class="product_layout_content">
                <h5>{{ $product->name }}</h5>
                <div class="product_layout_price">
                    @if($maxDiscount > 0)
                        <div class="product_layout_price_box">
                            <p class="price-discount">{{ $discountedPriceDisplay }}</p>
                            <span class="price-original">{{ $originalPriceDisplay }}</span>
                        </div>
                    @else
                        <p>{{ $discountedPriceDisplay }}</p>
                    @endif
                </div>
            </div>
            <div class="product_layout_button">
                <button class="buy_now_btn {{ $isOutOfStock ? 'disabled' : '' }}" 
                        onclick="{{ $isOutOfStock ? '' : 'buyNow(' . $product->id . ')' }}"
                        {{ $isOutOfStock ? 'disabled' : '' }}>
                    {{ $isOutOfStock ? 'HABIS' : 'BELI SEKARANG' }}
                </button>
                <button class="add_to_cart_btn {{ $isOutOfStock ? 'disabled' : '' }}" 
                        onclick="{{ $isOutOfStock ? '' : 'addToCart(' . $product->id . ')' }}"
                        {{ $isOutOfStock ? 'disabled' : '' }}>
                    <iconify-icon icon="solar:cart-linear"></iconify-icon>
                </button>
                <button class="add_to_wishlist_btn" 
                        data-product-id="{{ $product->id }}"
                        data-in-wishlist="{{ in_array($product->id, array_keys(session()->get('wishlist', []))) ? 'true' : 'false' }}"
                        onclick="addToWishlist({{ $product->id }})">
                    @if(in_array($product->id, array_keys(session()->get('wishlist', []))))
                        <iconify-icon icon="solar:heart-bold" style="color: #ef4444;"></iconify-icon>
                    @else
                        <iconify-icon icon="solar:heart-linear"></iconify-icon>
                    @endif
                </button>
            </div>
        </div>
        @empty
        <div class="empty_state">
            <p>Belum ada produk terlaris</p>
        </div>
        @endforelse
    </div>
</div>

{{-- ============================================ --}}
{{-- TESTIMONIAL SECTION --}}
{{-- ============================================ --}}
<div class="testimonial_section">
    <div class="heading_product_layout">
        <h3>MEREKA SUDAH <span>MEMBUKTIKAN</span></h3>
        <a href="">LIHAT SEMUA</a>
    </div>
    <div class="testimonial_layout">
        <div class="swiper testimonialSwiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="testimonial_box">
                        <div class="testimonial_box_header">
                            <div class="testimonial_box_header_profile">
                                <div class="testimonial_box_header_profile_name">
                                    <h5>MR</h5>
                                </div>
                                <div class="testimonial_box_header_desc">
                                    <h3>M****** R****</h3>
                                    <div class="testimonial_box_rating">
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                    </div>
                                </div>
                            </div>
                            <div class="testimonial_box_header_img">
                                <img src="images/pap.png" alt="">
                            </div>
                        </div>
                        <div class="testimonial_box_content">
                            <p>Udah 2 kali order jaket dan celana running di Barokah Sport, kualitas bahan emang ga pernah ngecewain. Bahannya adem, jahitan rapi, dan dipakainya nyaman banget buat olahraga harian.</p>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="testimonial_box">
                        <div class="testimonial_box_header">
                            <div class="testimonial_box_header_profile">
                                <div class="testimonial_box_header_profile_name">
                                    <h5>MR</h5>
                                </div>
                                <div class="testimonial_box_header_desc">
                                    <h3>M****** R****</h3>
                                    <div class="testimonial_box_rating">
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                    </div>
                                </div>
                            </div>
                            <div class="testimonial_box_header_img">
                                <img src="images/pap.png" alt="">
                            </div>
                        </div>
                        <div class="testimonial_box_content">
                            <p>Udah 2 kali order jaket dan celana running di Barokah Sport, kualitas bahan emang ga pernah ngecewain. Bahannya adem, jahitan rapi, dan dipakainya nyaman banget buat olahraga harian.</p>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="testimonial_box">
                        <div class="testimonial_box_header">
                            <div class="testimonial_box_header_profile">
                                <div class="testimonial_box_header_profile_name">
                                    <h5>MR</h5>
                                </div>
                                <div class="testimonial_box_header_desc">
                                    <h3>M****** R****</h3>
                                    <div class="testimonial_box_rating">
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                    </div>
                                </div>
                            </div>
                            <div class="testimonial_box_header_img">
                                <img src="images/pap.png" alt="">
                            </div>
                        </div>
                        <div class="testimonial_box_content">
                            <p>Udah 2 kali order jaket dan celana running di Barokah Sport, kualitas bahan emang ga pernah ngecewain. Bahannya adem, jahitan rapi, dan dipakainya nyaman banget buat olahraga harian.</p>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="testimonial_box">
                        <div class="testimonial_box_header">
                            <div class="testimonial_box_header_profile">
                                <div class="testimonial_box_header_profile_name">
                                    <h5>MR</h5>
                                </div>
                                <div class="testimonial_box_header_desc">
                                    <h3>M****** R****</h3>
                                    <div class="testimonial_box_rating">
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                    </div>
                                </div>
                            </div>
                            <div class="testimonial_box_header_img">
                                <img src="images/pap.png" alt="">
                            </div>
                        </div>
                        <div class="testimonial_box_content">
                            <p>Udah 2 kali order jaket dan celana running di Barokah Sport, kualitas bahan emang ga pernah ngecewain. Bahannya adem, jahitan rapi, dan dipakainya nyaman banget buat olahraga harian.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</div>

{{-- ============================================ --}}
{{-- ARTIKEL SECTION --}}
{{-- ============================================ --}}
@if($articles->isNotEmpty())
    <div class="artikel_section">
        <div class="heading_product_layout">
            <h3>ARTIKEL <span>TERBARU</span> KAMI</h3>
            <a href="{{ route('customer.articles.index') }}">LIHAT SEMUA</a>
        </div>
        <div class="artikel_section_layout">
            @foreach($articles as $article)
                <div class="artikel_section_box">
                    <div class="artikel_section_box_img">
                        <a href="{{ route('customer.articles.show', $article->slug) }}">
                            @if($article->image)
                                <img src="{{ Storage::url($article->image) }}" alt="{{ $article->title }}">
                            @else
                                <img src="{{ asset('images/default-article.jpg') }}" alt="{{ $article->title }}">
                            @endif
                        </a>
                    </div>
                    <div class="artikel_section_content">
                        <div class="artikel_section_meta">
                            <div class="artikel_section_meta_box">
                                <iconify-icon icon="mdi:user"></iconify-icon>
                                <span>{{ $article->author ?? 'Admin' }}</span>
                            </div>
                            <div class="artikel_section_meta_box">
                                <iconify-icon icon="lets-icons:date-fill"></iconify-icon>
                                <span>{{ $article->formatted_published_at }}</span>
                            </div>
                            @if($article->articleCategory)
                                <div class="artikel_section_meta_box">
                                    <iconify-icon icon="material-symbols:category"></iconify-icon>
                                    <span>{{ $article->articleCategory->name }}</span>
                                </div>
                            @endif
                        </div>
                        <h3>{{ $article->title }}</h3>
                        <p>{{ Str::limit(strip_tags($article->excerpt ?: $article->content), 120) }}</p>
                        <a href="{{ route('customer.articles.show', $article->slug) }}">
                            <button>Baca Selengkapnya</button>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<div class="keunggulan_layout keunggulan_layout_mobile">
    <div class="keunggulan_box_layout">
        <img src="{{ asset('images/gratis_ongkir.svg') }}" alt="">
        <div class="keunggulan_box_content">
            <h3>GRATIS ONGKIR</h3>
            <p>Pembelian di atas 750.000</p>
        </div>
    </div>
    <div class="keunggulan_box_layout">
        <img src="{{ asset('images/retur_mudah.svg') }}" alt="">
        <div class="keunggulan_box_content">
            <h3>PENGEMBALIAN MUDAH</h3>
            <p>Retur mudah dalam 7 hari.</p>
        </div>
    </div>
    <div class="keunggulan_box_layout">
        <img src="{{ asset('images/safety_pay.svg') }}" alt="">
        <div class="keunggulan_box_content">
            <h3>PEMBAYARAN AMAN</h3>
            <p>Metode pembayaran terpercaya</p>
        </div>
    </div>
    <div class="keunggulan_box_layout">
        <img src="{{ asset('images/cs.svg') }}" alt="">
        <div class="keunggulan_box_content">
            <h3>BANTUAN CEPAT</h3>
            <p>24/7 Support</p>
        </div>
    </div>
</div>

{{-- ============================================ --}}
{{-- SCRIPTS --}}
{{-- ============================================ --}}
<script>
    // ============================================
    // OVERRIDE FUNGSI WISHLIST UNTUK HOME PAGE
    // ============================================
    
    function updateAllWishlistButtons(productId, inWishlist) {
        const allButtons = document.querySelectorAll(`.add_to_wishlist_btn[data-product-id="${productId}"]`);
        
        console.log(`❤️ Updating ${allButtons.length} wishlist buttons for product ${productId}`);
        
        allButtons.forEach(function(btn) {
            if (inWishlist) {
                btn.innerHTML = '<iconify-icon icon="solar:heart-bold" style="color: #ef4444;"></iconify-icon>';
                btn.classList.add('active');
                btn.dataset.inWishlist = 'true';
            } else {
                btn.innerHTML = '<iconify-icon icon="solar:heart-linear"></iconify-icon>';
                btn.classList.remove('active');
                btn.dataset.inWishlist = 'false';
            }
            btn.disabled = false;
        });
    }

    if (typeof window.addToWishlist === 'function') {
        const originalAddToWishlist = window.addToWishlist;
        
        window.addToWishlist = function(productId) {
            console.log('❤️ addToWishlist called from home page for product:', productId);
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            
            const allButtons = document.querySelectorAll(`.add_to_wishlist_btn[data-product-id="${productId}"]`);
            allButtons.forEach(function(btn) {
                btn.disabled = true;
                btn.innerHTML = '⏳';
            });
            
            fetch(window.customerRoutes.wishlistAdd, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const count = data.count || 0;
                    const inWishlist = data.in_wishlist || false;
                    
                    console.log('❤️ Wishlist response:', { count, inWishlist });
                    
                    updateAllWishlistButtons(productId, inWishlist);
                    
                    if (typeof window.updateNavbarWishlistCount === 'function') {
                        window.updateNavbarWishlistCount(count);
                    } else {
                        const wishlistCountEl = document.getElementById('wishlist-count');
                        if (wishlistCountEl) {
                            wishlistCountEl.textContent = count;
                            wishlistCountEl.style.display = count > 0 ? 'inline-flex' : 'none';
                        }
                    }
                    
                    document.dispatchEvent(new CustomEvent('wishlist-updated', {
                        detail: { 
                            count: count, 
                            product_id: productId,
                            in_wishlist: inWishlist
                        }
                    }));
                    
                    showToast(data.message || (inWishlist ? 'Produk ditambahkan ke wishlist!' : 'Produk dihapus dari wishlist!'), 'success');
                    
                    if (typeof loadWishlistPopup === 'function') {
                        loadWishlistPopup();
                    }
                } else {
                    showToast(data.message || 'Gagal menambahkan ke wishlist', 'error');
                    allButtons.forEach(function(btn) {
                        const currentState = btn.dataset.inWishlist === 'true';
                        if (currentState) {
                            btn.innerHTML = '<iconify-icon icon="solar:heart-bold" style="color: #ef4444;"></iconify-icon>';
                            btn.classList.add('active');
                        } else {
                            btn.innerHTML = '<iconify-icon icon="solar:heart-linear"></iconify-icon>';
                            btn.classList.remove('active');
                        }
                        btn.disabled = false;
                    });
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                showToast('Terjadi kesalahan', 'error');
                allButtons.forEach(function(btn) {
                    const currentState = btn.dataset.inWishlist === 'true';
                    if (currentState) {
                        btn.innerHTML = '<iconify-icon icon="solar:heart-bold" style="color: #ef4444;"></iconify-icon>';
                        btn.classList.add('active');
                    } else {
                        btn.innerHTML = '<iconify-icon icon="solar:heart-linear"></iconify-icon>';
                        btn.classList.remove('active');
                    }
                    btn.disabled = false;
                });
            });
        };
    }
    
    console.log('✅ Home page wishlist functions initialized');
</script>

<script>
    // ============================================
    // 🔥 FLASH SALE COUNTDOWN TIMER
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        var durationData = @json($combinedFlashSaleDuration ?? null);

        if (!durationData || durationData.is_expired || durationData.total_seconds <= 0) {
            var timerContainer = document.getElementById('flash-sale-timer');
            if (timerContainer) {
                timerContainer.innerHTML = '<div class="timer-expired" style="color:#ef4444; font-weight:600;">⏰ Flash Sale Telah Berakhir</div>';
            }
            return;
        }

        startFlashSaleCountdown(durationData.total_seconds);
    });

    function startFlashSaleCountdown(initialSeconds) {
        var daysEl = document.getElementById('flash-sale-days');
        var hoursEl = document.getElementById('flash-sale-hours');
        var minutesEl = document.getElementById('flash-sale-minutes');
        var secondsEl = document.getElementById('flash-sale-seconds');
        var timerContainer = document.getElementById('flash-sale-timer');

        var endTime = Date.now() + (initialSeconds * 1000);

        function renderTimer() {
            var remainingMs = endTime - Date.now();
            var remainingSec = Math.floor(remainingMs / 1000);

            if (remainingSec <= 0) {
                clearInterval(timerInterval);
                if (daysEl) daysEl.textContent = '00';
                if (hoursEl) hoursEl.textContent = '00';
                if (minutesEl) minutesEl.textContent = '00';
                if (secondsEl) secondsEl.textContent = '00';

                if (timerContainer) {
                    timerContainer.innerHTML = '<div class="timer-expired" style="color:#ef4444; font-weight:600;">⏰ Flash Sale Telah Berakhir</div>';
                }
                return;
            }

            var days = Math.floor(remainingSec / 86400);
            var rem = remainingSec % 86400;
            var hours = Math.floor(rem / 3600);
            rem %= 3600;
            var minutes = Math.floor(rem / 60);
            var seconds = rem % 60;

            if (daysEl) daysEl.textContent = String(days).padStart(2, '0');
            if (hoursEl) hoursEl.textContent = String(hours).padStart(2, '0');
            if (minutesEl) minutesEl.textContent = String(minutes).padStart(2, '0');
            if (secondsEl) secondsEl.textContent = String(seconds).padStart(2, '0');
        }

        renderTimer();
        var timerInterval = setInterval(renderTimer, 1000);
    }
</script>

{{-- ============================================ --}}
{{-- FUNGSI UNTUK MEMASTIKAN COUNTER UPDATE --}}
{{-- ============================================ --}}
<script>
    if (typeof window.addToCartDirect === 'function') {
        const originalAddToCartDirect = window.addToCartDirect;
        
        window.addToCartDirect = function(productId, variantId = null, quantity = 1) {
            console.log('🔥 addToCartDirect called from home page');
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            
            const btn = document.querySelector(`.product_layout_box[data-product-id="${productId}"] .add_to_cart_btn`);
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '⏳';
            }
            
            fetch(window.customerRoutes.cartAdd, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    variant_id: variantId,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const count = data.count || data.cart_count || 0;
                    console.log('✅ Cart add success, count:', count);
                    
                    if (typeof window.updateNavbarCartCount === 'function') {
                        window.updateNavbarCartCount(count);
                    } else {
                        const cartCountEl = document.getElementById('cart-count');
                        if (cartCountEl) {
                            cartCountEl.textContent = count;
                            cartCountEl.style.display = count > 0 ? 'inline-flex' : 'none';
                        }
                    }
                    
                    document.dispatchEvent(new CustomEvent('cart-updated', {
                        detail: { count: count, message: data.message }
                    }));
                    
                    showToast(data.message || 'Produk ditambahkan ke keranjang!', 'success');
                    
                    if (typeof loadCartPopup === 'function') {
                        loadCartPopup();
                    }
                    
                    if (window._buyNowMode) {
                        window._buyNowMode = false;
                        setTimeout(() => {
                            window.location.href = window.customerRoutes.checkout;
                        }, 500);
                    }
                } else {
                    showToast(data.message || 'Gagal menambahkan produk', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Terjadi kesalahan', 'error');
            })
            .finally(() => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<iconify-icon icon="solar:cart-linear"></iconify-icon>';
                }
            });
        };
    }
    
    console.log('✅ Home page cart functions initialized');
</script>

@endsection