{{-- customer/wishlist/popup.blade.php --}}
@if (empty($products) || $products->isEmpty())
    <div class="popup-body-empty">
        <iconify-icon icon="mdi:heart-outline"></iconify-icon>
        <p>Wishlist kosong</p>
        <p>Simpan produk favoritmu di sini!</p>
    </div>
@else
    <div class="wishlist-items">
        @foreach ($products as $product)
            @php
                // 🔥 AMBIL DATA DISKON DARI PRODUCT (SUDAH DIHITUNG DI CONTROLLER)
                $hasDiscount = $product->has_discount ?? false;
                $maxDiscountPercent = $product->max_discount_percent ?? 0;
                $hasProductDiscount = $product->has_product_discount ?? false;
                $productDiscountPercent = $product->product_discount_percent ?? 0;
                $priceLabel = $product->price_label ?? '';
                $discountLabel = $product->discount_label ?? '';
                $badgeLabel = $product->badge_label ?? null;
                
                // 🔥 HITUNG MAKSIMAL DISKON
                $maxDiscount = 0;
                foreach ($product->variants as $variant) {
                    $discount = $variant->discount_percent ?? 0;
                    if ($discount > $maxDiscount) {
                        $maxDiscount = $discount;
                    }
                }
                
                // Jika tidak ada diskon dari varian, cek diskon produk
                if ($maxDiscount == 0 && $hasProductDiscount) {
                    $maxDiscount = $productDiscountPercent;
                }
                
                // 🔥 AMBIL HARGA TERMURAH (SUDAH TERMASUK DISKON)
                $minEffectivePrice = $product->min_effective_price ?? null;
                $maxEffectivePrice = $product->max_price ?? null;
                
                // 🔥 HITUNG HARGA ASLI (TANPA DISKON)
                $minOriginalPrice = $product->variants->min('price') ?? 0;
                $maxOriginalPrice = $product->variants->max('price') ?? 0;
                
                // 🔥 FORMAT HARGA DISKON
                $discountedPriceDisplay = '';
                $originalPriceDisplay = '';
                $hasAnyDiscount = false;
                
                if ($minEffectivePrice !== null && $maxEffectivePrice !== null) {
                    // Cek apakah ada diskon
                    $hasAnyDiscount = $maxDiscount > 0;
                    
                    // Harga setelah diskon
                    if ($minEffectivePrice == $maxEffectivePrice) {
                        $discountedPriceDisplay = 'Rp ' . number_format($minEffectivePrice, 0, ',', '.');
                    } else {
                        $discountedPriceDisplay = 'Rp ' . number_format($minEffectivePrice, 0, ',', '.') . ' - Rp ' . number_format($maxEffectivePrice, 0, ',', '.');
                    }
                    
                    // Harga asli (coret) - hanya jika ada diskon
                    if ($hasAnyDiscount) {
                        if ($minOriginalPrice == $maxOriginalPrice) {
                            $originalPriceDisplay = 'Rp ' . number_format($minOriginalPrice, 0, ',', '.');
                        } else {
                            $originalPriceDisplay = 'Rp ' . number_format($minOriginalPrice, 0, ',', '.') . ' - Rp ' . number_format($maxOriginalPrice, 0, ',', '.');
                        }
                    }
                } else {
                    // Fallback: hitung manual
                    $prices = [];
                    foreach ($product->variants as $variant) {
                        $prices[] = $variant->effective_price ?? $variant->price;
                    }
                    $minEff = min($prices);
                    $maxEff = max($prices);
                    $discountedPriceDisplay = 'Rp ' . number_format($minEff, 0, ',', '.');
                    if ($minEff != $maxEff) {
                        $discountedPriceDisplay .= ' - Rp ' . number_format($maxEff, 0, ',', '.');
                    }
                    
                    // Cek diskon
                    $hasAnyDiscount = $maxDiscount > 0;
                    if ($hasAnyDiscount) {
                        $origPrices = $product->variants->pluck('price')->toArray();
                        $minOrig = min($origPrices);
                        $maxOrig = max($origPrices);
                        $originalPriceDisplay = 'Rp ' . number_format($minOrig, 0, ',', '.');
                        if ($minOrig != $maxOrig) {
                            $originalPriceDisplay .= ' - Rp ' . number_format($maxOrig, 0, ',', '.');
                        }
                    }
                }
                
                // 🔥 TOTAL STOK
                $totalStock = $product->variants->sum('stock');
                
                // 🔥 CARI VARIAN PERTAMA UNTUK TOMBOL ADD TO CART
                $firstVariant = $product->variants->first();
                $firstVariantId = $firstVariant?->id;
            @endphp
            
            <div class="wishlist-item" data-product-id="{{ $product->id }}">
                {{-- Image --}}
                <div class="wishlist-item-image">
                    @if ($product->images->first())
                        <img src="{{ Storage::url($product->images->first()->image) }}" 
                             alt="{{ $product->name }}">
                    @else
                        <span class="placeholder">📦</span>
                    @endif
                    
                    {{-- 🔥 BADGE DISKON --}}
                    @if($hasAnyDiscount && $maxDiscount > 0)
                        <span class="wishlist-discount-badge">-{{ round($maxDiscount) }}%</span>
                    @endif
                </div>

                {{-- Info --}}
                <div class="wishlist-item-info">
                    <a href="{{ route('customer.products.show', $product) }}" 
                       class="wishlist-item-name hover:text-blue-600" 
                       target="_blank">
                        {{ $product->name }}
                    </a>
                    @if ($product->category)
                        <p class="wishlist-item-category">{{ $product->category->name }}</p>
                    @endif
                    
                    {{-- 🔥 HARGA DENGAN DISKON --}}
                    <div class="wishlist-item-price-wrapper">
                        @if($hasAnyDiscount)
                            {{-- Ada Diskon --}}
                            <span class="wishlist-item-price discounted">{{ $discountedPriceDisplay }}</span>
                            <span class="wishlist-item-price-original">{{ $originalPriceDisplay }}</span>
                            @if($hasProductDiscount && $productDiscountPercent > 0)
                                <span class="wishlist-discount-label">🏷️ Produk</span>
                            @endif
                        @else
                            {{-- Tidak Ada Diskon --}}
                            <span class="wishlist-item-price">{{ $discountedPriceDisplay }}</span>
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div class="wishlist-item-actions">
                    @if ($totalStock > 0)
                        <button class="btn-sm btn-sm-primary add-to-cart-wishlist" 
                                data-product-id="{{ $product->id }}"
                                data-variant-id="{{ $firstVariantId }}"
                                title="Tambah ke Keranjang">
                            <iconify-icon icon="mdi:cart-plus"></iconify-icon>
                        </button>
                    @endif
                    <button class="btn-sm btn-sm-danger remove-wishlist" 
                            data-product-id="{{ $product->id }}"
                            title="Hapus dari Wishlist">
                        <iconify-icon icon="mdi:heart-broken"></iconify-icon>
                    </button>
                </div>
            </div>
        @endforeach
    </div>
@endif