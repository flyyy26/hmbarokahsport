@props([
    'product',
    'skeletonPrefix' => 'product',
    'showFlashSale' => false,
])

@php
    $maxDiscount = $product->max_discount_percent ?? 0;
    $minEffectivePrice = $product->min_effective_price ?? 0;
    $maxOriginalPrice = $product->max_price ?? 0;
    $minOriginalPrice = $product->variants->min('price') ?? 0;
    $isFlashSale = $showFlashSale && ($product->is_flash_sale ?? false);
    $isOutOfStock = $product->isOutOfStock();
    $thumbnail = $product->thumbnail ?? null;
    if (!$thumbnail && $product->images->isNotEmpty()) {
        $thumbnail = Storage::url($product->images->first()->image);
    }
    $badgeText = $product->badge_label ?? '';
    $inWishlist = in_array($product->id, array_keys(session()->get('wishlist', [])));

    // Format harga
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

    $skeletonId = "skeleton-{$skeletonPrefix}-{$product->id}";
@endphp

<div class="product_layout_box {{ $isFlashSale ? 'product_layout_box_flash' : '' }}" data-product-id="{{ $product->id }}">
    <div class="product_layout_img">
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
        </a>
        
        @if($isOutOfStock)
            <span class="product_badge out-of-stock">HABIS</span>
        @endif
        
        @if($isFlashSale)
            <span class="discount_badge flash-sale-badge">
                ⚡ Flash Sale{{ $maxDiscount > 0 ? ' ' . round($maxDiscount) . '%' : '' }}
            </span>
        @elseif($maxDiscount > 0)
            <span class="discount_badge">Diskon {{ round($maxDiscount) }}%</span>
        @elseif(!empty($badgeText))
            <span class="discount_badge">{{ $badgeText }}</span>
        @endif
    </div>
    
    <div class="product_layout_content">
        <h5>{{ $product->name }}</h5>
        <div class="product_layout_price">
            @if($maxDiscount > 0 || $isFlashSale)
                <div class="product_layout_price_box">
                    <p class="price-discount {{ $isFlashSale ? 'flash-sale-price' : '' }}">{{ $discountedPriceDisplay }}</p>
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