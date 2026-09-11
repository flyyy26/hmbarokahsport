{{-- customer/cart/popup.blade.php --}}
@if (empty($cart))
    <div class="popup-body-empty">
        <iconify-icon icon="mdi:cart-outline"></iconify-icon>
        <p>Keranjang kosong</p>
        <p>Yuk, mulai belanja!</p>
    </div>
@else
    <div class="space-y-3">
        @foreach ($cart as $item)
            @php
                // 🔥 HITUNG APAKAH ADA DISKON
                $hasDiscount = isset($item['original_price']) && $item['original_price'] > $item['price'];
                $discountPercent = 0;
                if ($hasDiscount && $item['original_price'] > 0) {
                    $discountPercent = round((($item['original_price'] - $item['price']) / $item['original_price']) * 100);
                }
                
                // 🔥 CEK APAKAH ADA DISKON DARI PRODUK (jika ada data tambahan)
                $hasProductDiscount = isset($item['has_product_discount']) && $item['has_product_discount'];
                $productDiscountPercent = $item['product_discount_percent'] ?? 0;
                
                // 🔥 GABUNGKAN DISKON
                $finalDiscountPercent = $discountPercent;
                if ($hasProductDiscount && $productDiscountPercent > $finalDiscountPercent) {
                    $finalDiscountPercent = $productDiscountPercent;
                }
                
                // 🔥 AMBIL GAMBAR
                $imageUrl = null;
                if (!empty($item['image'])) {
                    if (Storage::disk('public')->exists($item['image'])) {
                        $imageUrl = Storage::url($item['image']);
                    } elseif (filter_var($item['image'], FILTER_VALIDATE_URL)) {
                        $imageUrl = $item['image'];
                    }
                }
            @endphp
            
            <div class="cart-item" data-key="{{ $item['id'] }}">
                {{-- Image --}}
                <div class="cart-item-image">
                    @if ($imageUrl)
                        <img src="{{ $imageUrl }}" alt="{{ $item['product_name'] }}">
                    @else
                        <span class="placeholder">📦</span>
                    @endif
                    
                    @if($finalDiscountPercent > 0)
                        <span class="wishlist-discount-badge">-{{ $finalDiscountPercent }}%</span>
                    @endif
                </div>

                {{-- Info --}}
                <div class="cart-item-info">
                    <p class="cart-item-name">{{ $item['product_name'] }}</p>
                    @if (!empty($item['variant_name']))
                        <p class="cart-item-variant">{{ $item['variant_name'] }}</p>
                    @endif
                    
                    {{-- 🔥 HARGA DENGAN DISKON --}}
                    <div class="cart-item-price-wrapper">
                        @if($hasDiscount)
                            {{-- Ada Diskon --}}
                            <p class="cart-item-price cart-item-price-discount">
                                Rp {{ number_format($item['price'], 0, ',', '.') }}
                            </p>
                            <span class="cart-item-price-original">
                                Rp {{ number_format($item['original_price'], 0, ',', '.') }}
                            </span>
                        @else
                            {{-- Tidak Ada Diskon --}}
                            <p class="cart-item-price">
                                Rp {{ number_format($item['price'], 0, ',', '.') }}
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Quantity & Remove --}}
                <div class="cart-item-actions">
                    <div class="qty-wrapper">
                        <button class="qty-btn" data-action="decrease" data-key="{{ $item['id'] }}" aria-label="Kurangi">−</button>
                        <input type="number" class="qty-input" value="{{ $item['quantity'] }}" 
                               min="1" data-key="{{ $item['id'] }}" aria-label="Jumlah">
                        <button class="qty-btn" data-action="increase" data-key="{{ $item['id'] }}" aria-label="Tambah">+</button>
                    </div>
                    <button class="btn-remove" data-key="{{ $item['id'] }}" aria-label="Hapus item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endforeach
    </div>
@endif