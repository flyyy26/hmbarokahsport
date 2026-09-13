@props([
    'product',
    'skeletonPrefix' => 'product',
    'showFlashSale' => false,
])

@php
    // ============================================
    // 🔥 HARGA
    // ============================================
    $maxDiscount = $product->max_discount_percent ?? 0;
    $isFlashSale = $showFlashSale && ($product->is_flash_sale ?? false) && ($product->flash_sale_status ?? '') === 'active';
    $isOutOfStock = $product->isOutOfStock();
    $inWishlist  = in_array($product->id, array_keys(session()->get('wishlist', [])));
    $badgeText   = $product->badge_label ?? '';

    $hasDiscount = $product->has_discount ?? false;

    // 🔥 AMBIL DARI TRAIT
    $minEff  = $product->min_effective_price ?? null;
    $maxEff  = $product->max_effective_price ?? null;
    $minOrig = $product->min_price ?? null;
    $maxOrig = $product->max_price ?? null;

    // Fallback kalau trait tidak jalan
    if ($minEff === null || $maxEff === null) {
        $effectivePrices = $product->variants->map(fn($v) => $v->effective_price ?? $v->price);
        $originalPrices  = $product->variants->pluck('price');

        $minEff  = $effectivePrices->min() ?? 0;
        $maxEff  = $effectivePrices->max() ?? 0;
        $minOrig = $originalPrices->min() ?? 0;
        $maxOrig = $originalPrices->max() ?? 0;
    }

    // ============================================
    // 🔥 HARGA DISKON → RANGE (kalau beda)
    // ============================================
    if ($minEff == $maxEff) {
        $priceLabel = 'Rp ' . number_format($minEff, 0, ',', '.');
    } else {
        $priceLabel = 'Rp ' . number_format($minEff, 0, ',', '.') .
                      ' - Rp ' . number_format($maxEff, 0, ',', '.');
    }

    // ============================================
    // 🔥 HARGA CORET → 1 HARGA SAJA (pakai max original)
    // ============================================
    $originalPriceLabel = null;
    if ($hasDiscount && $maxOrig > $maxEff) {
        $originalPriceLabel = 'Rp ' . number_format($maxOrig, 0, ',', '.');
    }

    // Thumbnail
    $thumbnail = $product->display_image ?? $product->thumbnail ?? null;
    if (!$thumbnail && $product->relationLoaded('images') && $product->images->isNotEmpty()) {
        $imagePath = ltrim($product->images->first()->image, '/');
        if (str_starts_with($imagePath, 'storage/')) {
            $imagePath = substr($imagePath, strlen('storage/'));
        }
        $thumbnail = \Illuminate\Support\Facades\Storage::url($imagePath);
    }

    $skeletonId = "skeleton-{$skeletonPrefix}-{$product->id}";
@endphp

<div class="product_layout_box {{ $isFlashSale ? 'product_layout_box_flash' : '' }}"
     data-product-id="{{ $product->id }}"
     @if($isFlashSale && $product->flash_sale_end_date)
         data-flash-end="{{ $product->flash_sale_end_date->toIso8601String() }}"
     @endif>

    <div class="product_layout_img">
        {{-- Skeleton --}}
        <div class="skeleton-image-box" id="{{ $skeletonId }}">
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
                     onload="this.classList.remove('loading'); this.classList.add('loaded'); document.getElementById('{{ $skeletonId }}').classList.add('hidden');"
                     onerror="this.onerror=null; this.src='{{ asset('images/placeholder.png') }}'; this.classList.remove('loading'); this.classList.add('loaded'); document.getElementById('{{ $skeletonId }}').classList.add('hidden');">
            @else
                <div class="placeholder">
                    <iconify-icon icon="mdi:image-off-outline"></iconify-icon>
                </div>
                <script>document.getElementById('{{ $skeletonId }}')?.classList.add('hidden');</script>
            @endif

            {{-- Badge HABIS --}}
            @if($isOutOfStock)
                <span class="product_badge out-of-stock">HABIS</span>
            @endif

            {{-- Badge Diskon / Flash Sale / Featured --}}
            @if($isFlashSale)
                <span class="discount_badge flash-sale-badge">
                    ⚡ Flash Sale{{ $maxDiscount > 0 ? ' ' . round($maxDiscount) . '%' : '' }}
                </span>
            @elseif($maxDiscount > 0)
                <span class="discount_badge">Diskon {{ round($maxDiscount) }}%</span>
            @elseif(!empty($badgeText))
                <span class="discount_badge">{{ $badgeText }}</span>
            @endif
        </a>
    </div>

    <div class="product_layout_content">
        <h5>{{ $product->name }}</h5>
        <div class="product_layout_price">
            @if($hasDiscount || $isFlashSale)
                <div class="product_layout_price_box">
                    <p class="price-discount {{ $isFlashSale ? 'flash-sale-price' : '' }}">
                        {{ $priceLabel }}
                    </p>
                    @if($originalPriceLabel)
                        <span class="price-original">{{ $originalPriceLabel }}</span>
                    @endif
                </div>
            @else
                <p>{{ $priceLabel }}</p>
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
                data-in-wishlist="{{ $inWishlist ? 'true' : 'false' }}"
                onclick="addToWishlist({{ $product->id }})">
            @if($inWishlist)
                <iconify-icon icon="solar:heart-bold" style="color: #ef4444;"></iconify-icon>
            @else
                <iconify-icon icon="solar:heart-linear"></iconify-icon>
            @endif
        </button>
    </div>
</div>