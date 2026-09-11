@extends('layouts.customer')

@section('title', 'Flash Sale - Barokah Sport')

@section('content')

<style>
    .catalog-container{
        padding-bottom:4vw;
    }
    @media (max-width:768px) {
        .catalog-container{
            padding-bottom:9vw;
        }
    }
</style>

<div class="catalog-container">
    <div class="katalog_top_container katalog_top_container_flash">
        <div class="catalog_header_layout">
            <div class="catalog-header">
                <h1>Flash Sale</h1>
                <p>Diskon waktu terbatas! Jangan sampai kelewatan.</p>
                
            </div>
            
            @if($flashSaleEndDate && $flashSaleLabel)
                <div class="catalog-header-time">
                    <div class="flash-sale-header-timer" data-end="{{ $flashSaleEndDate->toIso8601String() }}">
                        <div class="timer-header-group" id="header-timer-group">
                            <div class="timer-header-block">
                                <span class="timer-header-number" id="header-days">00</span>
                                <span class="timer-header-label-small">Hari</span>
                            </div>
                            <span class="timer-header-separator">:</span>
                            <div class="timer-header-block">
                                <span class="timer-header-number" id="header-hours">00</span>
                                <span class="timer-header-label-small">Jam</span>
                            </div>
                            <span class="timer-header-separator">:</span>
                            <div class="timer-header-block">
                                <span class="timer-header-number" id="header-minutes">00</span>
                                <span class="timer-header-label-small">Menit</span>
                            </div>
                            <span class="timer-header-separator">:</span>
                            <div class="timer-header-block">
                                <span class="timer-header-number" id="header-seconds">00</span>
                                <span class="timer-header-label-small">Detik</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- ============================================ --}}
        {{-- FILTER ROW - CUSTOM SELECT --}}
        {{-- ============================================ --}}
        <form id="filter-form" method="GET" action="{{ route('customer.products.flash-sale') }}" class="filter-row">
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif

            {{-- KATEGORI --}}
            <span class="filter-label">Kategori</span>
            <div class="custom-select-wrapper" data-name="category">
                <div class="custom-select-trigger">
                    <span class="trigger-text">
                        {{ request('category') ? $categories->firstWhere('id', request('category'))->name ?? 'Semua' : 'Semua' }}
                    </span>
                    <iconify-icon icon="tabler:chevron-down"></iconify-icon>
                </div>
                <div class="custom-select-dropdown">
                    <div class="dropdown-item {{ !request('category') ? 'active' : '' }}" data-value="">
                        <span>Semua</span>
                        <span class="check-icon">✓</span>
                    </div>
                    @foreach ($categories as $category)
                        <div class="dropdown-item {{ request('category') == $category->id ? 'active' : '' }}" 
                             data-value="{{ $category->id }}">
                            <span>{{ $category->name }}</span>
                            <span class="check-icon">✓</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="filter-divider"></div>

            {{-- GENDER --}}
            <span class="filter-label">Gender</span>
            <div class="custom-select-wrapper" data-name="gender">
                <div class="custom-select-trigger">
                    <span class="trigger-text">
                        {{ request('gender') ? ucfirst(request('gender')) : 'Semua' }}
                    </span>
                    <iconify-icon icon="tabler:chevron-down"></iconify-icon>
                </div>
                <div class="custom-select-dropdown">
                    <div class="dropdown-item {{ !request('gender') ? 'active' : '' }}" data-value="">
                        <span>Semua</span>
                        <span class="check-icon">✓</span>
                    </div>
                    @foreach ($genders as $gender)
                        <div class="dropdown-item {{ request('gender') == $gender ? 'active' : '' }}" 
                             data-value="{{ $gender }}">
                            <span>{{ ucfirst($gender) }}</span>
                            <span class="check-icon">✓</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="filter-divider"></div>

            {{-- UKURAN --}}
            <span class="filter-label">Ukuran</span>
            <div class="custom-select-wrapper" data-name="size">
                <div class="custom-select-trigger">
                    <span class="trigger-text">
                        {{ request('size') ?: 'Semua' }}
                    </span>
                    <iconify-icon icon="tabler:chevron-down"></iconify-icon>
                </div>
                <div class="custom-select-dropdown">
                    <div class="dropdown-item {{ !request('size') ? 'active' : '' }}" data-value="">
                        <span>Semua</span>
                        <span class="check-icon">✓</span>
                    </div>
                    @foreach ($sizes as $size)
                        <div class="dropdown-item {{ request('size') == $size ? 'active' : '' }}" 
                             data-value="{{ $size }}">
                            <span>{{ $size }}</span>
                            <span class="check-icon">✓</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="filter-divider"></div>

            {{-- WARNA --}}
            <span class="filter-label">Warna</span>
            <div class="custom-select-wrapper" data-name="color">
                <div class="custom-select-trigger">
                    <span class="trigger-text">
                        {{ request('color') ?: 'Semua' }}
                    </span>
                    <iconify-icon icon="tabler:chevron-down"></iconify-icon>
                </div>
                <div class="custom-select-dropdown">
                    <div class="dropdown-item {{ !request('color') ? 'active' : '' }}" data-value="">
                        <span>Semua</span>
                        <span class="check-icon">✓</span>
                    </div>
                    @foreach ($colors as $color)
                        <div class="dropdown-item {{ request('color') == $color ? 'active' : '' }}" 
                             data-value="{{ $color }}">
                            <span>{{ $color }}</span>
                            <span class="check-icon">✓</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="filter-divider"></div>

            {{-- SORT BY --}}
            <span class="filter-label">Urutkan</span>
            <div class="custom-select-wrapper" data-name="sort">
                <div class="custom-select-trigger">
                    <span class="trigger-text">
                        @php
                            $sortOptions = [
                                'newest' => 'Terbaru',
                                'discount_desc' => 'Diskon Terbesar',
                                'price_asc' => 'Harga: Rendah → Tinggi',
                                'price_desc' => 'Harga: Tinggi → Rendah',
                                'name' => 'Nama (A-Z)'
                            ];
                        @endphp
                        {{ $sortOptions[request('sort', 'newest')] ?? 'Terbaru' }}
                    </span>
                    <iconify-icon icon="tabler:chevron-down"></iconify-icon>
                </div>
                <div class="custom-select-dropdown">
                    <div class="dropdown-item {{ request('sort', 'newest') == 'newest' ? 'active' : '' }}" data-value="newest">
                        <span>Terbaru</span>
                        <span class="check-icon">✓</span>
                    </div>
                    <div class="dropdown-item {{ request('sort') == 'discount_desc' ? 'active' : '' }}" data-value="discount_desc">
                        <span>Diskon Terbesar</span>
                        <span class="check-icon">✓</span>
                    </div>
                    <div class="dropdown-item {{ request('sort') == 'price_asc' ? 'active' : '' }}" data-value="price_asc">
                        <span>Harga: Rendah → Tinggi</span>
                        <span class="check-icon">✓</span>
                    </div>
                    <div class="dropdown-item {{ request('sort') == 'price_desc' ? 'active' : '' }}" data-value="price_desc">
                        <span>Harga: Tinggi → Rendah</span>
                        <span class="check-icon">✓</span>
                    </div>
                    <div class="dropdown-item {{ request('sort') == 'name' ? 'active' : '' }}" data-value="name">
                        <span>Nama (A-Z)</span>
                        <span class="check-icon">✓</span>
                    </div>
                </div>
            </div>

            {{-- RESET FILTER --}}
            @if(request()->anyFilled(['category', 'gender', 'size', 'color', 'sort']))
                <a href="{{ route('customer.products.flash-sale') }}" class="filter-reset">
                    ✕ Reset
                </a>
            @endif

            <span class="filter-count">{{ $products->total() }} produk</span>
        </form>

        {{-- 🔥 FILTER TOGGLE BUTTON - MOBILE --}}
        <button type="button" class="filter-toggle-btn filter-toggle-btn-flash" onclick="openFilterPopup()">
            <iconify-icon icon="tabler:filter"></iconify-icon>
            Filter
            <span class="filter-badge" id="filter-badge" style="display:none;">0</span>
        </button>
    </div>

    {{-- ============================================ --}}
    {{-- 🔥 FILTER POPUP - MOBILE --}}
    {{-- ============================================ --}}
    <div id="filter-popup" class="filter-popup-overlay">
        <div class="filter-popup">
            {{-- Handle --}}
            <div class="filter-popup-handle"></div>

            {{-- Header --}}
            <div class="filter-popup-header">
                <h3>Filter Produk</h3>
                <button type="button" class="filter-popup-close" onclick="closeFilterPopup()">✕</button>
            </div>

            {{-- Filter Body --}}
            <div id="filter-popup-body">
                {{-- KATEGORI --}}
                <div class="filter-popup-group">
                    <span class="filter-group-label">Kategori</span>
                    <div class="filter-options" data-filter="category">
                        <div class="filter-option active" data-value="">Semua</div>
                        @foreach ($categories as $category)
                            <div class="filter-option {{ request('category') == $category->id ? 'active' : '' }}" 
                                 data-value="{{ $category->id }}">
                                {{ $category->name }}
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- GENDER --}}
                <div class="filter-popup-group">
                    <span class="filter-group-label">Gender</span>
                    <div class="filter-options" data-filter="gender">
                        <div class="filter-option active" data-value="">Semua</div>
                        @foreach ($genders as $gender)
                            <div class="filter-option {{ request('gender') == $gender ? 'active' : '' }}" 
                                 data-value="{{ $gender }}">
                                {{ ucfirst($gender) }}
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- UKURAN --}}
                <div class="filter-popup-group">
                    <span class="filter-group-label">Ukuran</span>
                    <div class="filter-options" data-filter="size">
                        <div class="filter-option active" data-value="">Semua</div>
                        @foreach ($sizes as $size)
                            <div class="filter-option {{ request('size') == $size ? 'active' : '' }}" 
                                 data-value="{{ $size }}">
                                {{ $size }}
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- WARNA --}}
                <div class="filter-popup-group">
                    <span class="filter-group-label">Warna</span>
                    <div class="filter-options" data-filter="color">
                        <div class="filter-option active" data-value="">Semua</div>
                        @foreach ($colors as $color)
                            <div class="filter-option {{ request('color') == $color ? 'active' : '' }}" 
                                 data-value="{{ $color }}">
                                {{ $color }}
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- URUTKAN --}}
                <div class="filter-popup-group">
                    <span class="filter-group-label">Urutkan</span>
                    <div class="filter-options" data-filter="sort">
                        @php
                            $sortOptions = [
                                'newest' => 'Terbaru',
                                'discount_desc' => 'Diskon Terbesar',
                                'price_asc' => 'Harga: Rendah → Tinggi',
                                'price_desc' => 'Harga: Tinggi → Rendah',
                                'name' => 'Nama (A-Z)'
                            ];
                        @endphp
                        @foreach ($sortOptions as $value => $label)
                            <div class="filter-option {{ request('sort', 'newest') == $value ? 'active' : '' }}" 
                                 data-value="{{ $value }}">
                                {{ $label }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="filter-popup-actions">
                <button type="button" class="btn-reset-filter" onclick="resetAllFilters()">
                    Reset
                </button>
                <button type="button" class="btn-apply-filter" onclick="applyFilters()">
                    Terapkan Filter
                </button>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- PRODUCT GRID --}}
    {{-- ============================================ --}}
    <div class="product_layout">
        @if ($products->isEmpty())
            <div class="empty_state">
                <div class="empty-icon">⚡</div>
                <p>Belum ada produk flash sale yang tersedia.</p>
                <span>Pantau terus halaman ini untuk mendapatkan penawaran terbaik!</span>
            </div>
        @else
            <div class="product_layout_grid">
                @foreach ($products as $product)
                    @php
                        // 🔥 AMBIL DATA DISKON
                        $hasDiscount = $product->has_discount ?? false;
                        $maxDiscountPercent = $product->max_discount_percent ?? 0;
                        $hasProductDiscount = $product->has_product_discount ?? false;
                        $productDiscountPercent = $product->product_discount_percent ?? 0;
                        
                        // 🔥 HITUNG MAKSIMAL DISKON
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
                        
                        // 🔥 HITUNG HARGA DISKON
                        $minEffectivePrice = $product->min_effective_price ?? null;
                        $minOriginalPrice = $product->variants->min('price') ?? 0;
                        $maxOriginalPrice = $product->variants->max('price') ?? 0;
                        
                        // 🔥 FORMAT HARGA
                        $discountedPriceDisplay = '';
                        $originalPriceDisplay = '';
                        
                        if ($minEffectivePrice !== null) {
                            $discountedPriceDisplay = 'Rp ' . number_format($minEffectivePrice, 0, ',', '.');
                        }
                        
                        if ($minOriginalPrice == $maxOriginalPrice) {
                            $originalPriceDisplay = 'Rp ' . number_format($minOriginalPrice, 0, ',', '.');
                        } else {
                            $originalPriceDisplay = 'Rp ' . number_format($minOriginalPrice, 0, ',', '.') . ' - Rp ' . number_format($maxOriginalPrice, 0, ',', '.');
                        }
                        
                        // 🔥 STATUS STOK
                        $isOutOfStock = $product->isOutOfStock();
                        
                        // 🔥 GAMBAR THUMBNAIL
                        $thumbnailImage = null;
                        if (isset($product->display_image) && $product->display_image) {
                            $thumbnailImage = $product->display_image;
                        } elseif ($product->images->isNotEmpty()) {
                            $thumbnailImage = Storage::url($product->images->first()->image);
                        }
                        
                        // 🔥 FLASH SALE END DATE
                        $flashEndDate = $product->flash_sale_end_date?->toIso8601String();
                    @endphp
                    
                    <div class="product_layout_box flash-sale-product" data-product-id="{{ $product->id }}" data-flash-end="{{ $flashEndDate }}">
                        <div class="product_layout_img">
                            <div class="skeleton-image" id="skeleton-{{ $product->id }}">
                                <div class="skeleton" style="width:100%;height:100%;"></div>
                            </div>
                            
                            <a href="{{ route('customer.products.show', $product->slug) }}">
                                @if($thumbnailImage)
                                    <img 
                                        src="{{ $thumbnailImage }}" 
                                        alt="{{ $product->name }}"
                                        loading="lazy"
                                        width="300"
                                        height="300"
                                        decoding="async"
                                        class="loading"
                                        onload="this.classList.remove('loading'); this.classList.add('loaded'); document.getElementById('skeleton-{{ $product->id }}').classList.add('hidden');"
                                        onerror="this.onerror=null; this.src='{{ asset('images/placeholder.png') }}'; this.classList.remove('loading'); this.classList.add('loaded'); document.getElementById('skeleton-{{ $product->id }}').classList.add('hidden');">
                                @else
                                    <div class="placeholder">
                                        <iconify-icon icon="mdi:image-off-outline"></iconify-icon>
                                    </div>
                                    <script>
                                        document.getElementById('skeleton-{{ $product->id }}')?.classList.add('hidden');
                                    </script>
                                @endif
                                
                                {{-- FLASH SALE BADGE --}}
                                <span class="flash-sale-badge-top">⚡ FLASH SALE</span>
                                
                                {{-- BADGE HABIS --}}
                                @if($isOutOfStock)
                                    <span class="product_badge out-of-stock">HABIS</span>
                                @endif

                                {{-- BADGE DISKON --}}
                                @if($maxDiscount > 0)
                                    <span class="discount_badge">Diskon {{ round($maxDiscount) }}%</span>
                                @endif
                            </a>
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
                @endforeach
            </div>
        @endif
    </div>

    {{-- PAGINATION --}}
    @if ($products->hasPages())
        <div class="pagination-wrapper">
            {{ $products->links() }}
        </div>
    @endif

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const headerTimer = document.querySelector('.flash-sale-header-timer');
        
        if (!headerTimer) return;
        
        const endDate = new Date(headerTimer.dataset.end);
        
        function updateHeaderTimer() {
            const now = new Date().getTime();
            const distance = endDate.getTime() - now;
            
            if (distance < 0) {
                headerTimer.classList.add('expired');
                document.getElementById('header-timer-group').innerHTML = `
                    <span style="font-size:0.9vw;font-weight:600;color:#94a3b8;">
                        ⏰ Flash Sale Telah Berakhir
                    </span>
                `;
                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            document.getElementById('header-days').textContent = String(days).padStart(2, '0');
            document.getElementById('header-hours').textContent = String(hours).padStart(2, '0');
            document.getElementById('header-minutes').textContent = String(minutes).padStart(2, '0');
            document.getElementById('header-seconds').textContent = String(seconds).padStart(2, '0');
        }
        
        updateHeaderTimer();
        setInterval(updateHeaderTimer, 1000);
    });
</script>

{{-- ============================================ --}}
{{-- JAVASCRIPT - FLASH SALE TIMER --}}
{{-- ============================================ --}}
<script>
// ============================================ */
// 🔥 FLASH SALE TIMER MINI */
// ============================================ */

document.addEventListener('DOMContentLoaded', function() {
    // Update semua timer flash sale
    function updateAllFlashTimers() {
        const timerElements = document.querySelectorAll('.flash-sale-timer-mini');
        
        timerElements.forEach(function(timer) {
            const endDate = new Date(timer.dataset.end);
            const now = new Date().getTime();
            const distance = endDate.getTime() - now;
            const productId = timer.closest('.flash-sale-product')?.dataset.productId;
            
            if (distance < 0) {
                timer.innerHTML = '<span style="color:#ef4444;font-size:0.6vw;">⏰ Berakhir</span>';
                return;
            }
            
            // Hitung waktu
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            // Update timer
            const timerSpan = timer.querySelector('.timer-mini');
            if (timerSpan) {
                timerSpan.textContent = 
                    String(hours).padStart(2, '0') + ':' + 
                    String(minutes).padStart(2, '0') + ':' + 
                    String(seconds).padStart(2, '0');
            }
        });
    }
    
    // Update setiap detik
    updateAllFlashTimers();
    setInterval(updateAllFlashTimers, 1000);
});

// ============================================ */
// 🔥 FILTER POPUP - JAVASCRIPT (SAMA SEPERTI KATALOG) */
// ============================================ */

var filterState = {
    category: '{{ request('category') ?? '' }}',
    gender: '{{ request('gender') ?? '' }}',
    size: '{{ request('size') ?? '' }}',
    color: '{{ request('color') ?? '' }}',
    sort: '{{ request('sort', 'newest') }}'
};

function openFilterPopup() {
    var popup = document.getElementById('filter-popup');
    if (!popup) return;
    syncFilterStateFromURL();
    applyFilterStateToPopup();
    popup.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeFilterPopup() {
    var popup = document.getElementById('filter-popup');
    if (!popup) return;
    popup.classList.remove('active');
    document.body.style.overflow = '';
}

function syncFilterStateFromURL() {
    var urlParams = new URLSearchParams(window.location.search);
    filterState.category = urlParams.get('category') || '';
    filterState.gender = urlParams.get('gender') || '';
    filterState.size = urlParams.get('size') || '';
    filterState.color = urlParams.get('color') || '';
    filterState.sort = urlParams.get('sort') || 'newest';
}

function applyFilterStateToPopup() {
    var groups = document.querySelectorAll('.filter-popup-group .filter-options');
    groups.forEach(function(group) {
        var filterName = group.dataset.filter;
        var selectedValue = filterState[filterName] || '';
        var options = group.querySelectorAll('.filter-option');
        options.forEach(function(option) {
            option.classList.remove('active');
            if (option.dataset.value === selectedValue) {
                option.classList.add('active');
            }
        });
    });
}

function updateFilterBadge() {
    var badge = document.getElementById('filter-badge');
    if (!badge) return;
    var count = 0;
    if (filterState.category) count++;
    if (filterState.gender) count++;
    if (filterState.size) count++;
    if (filterState.color) count++;
    if (count > 0) {
        badge.textContent = count;
        badge.style.display = 'inline';
    } else {
        badge.style.display = 'none';
    }
}

document.addEventListener('click', function(e) {
    var target = e.target.closest('.filter-popup-group .filter-option');
    if (!target) return;
    var group = target.closest('.filter-options');
    if (!group) return;
    var filterName = group.dataset.filter;
    var options = group.querySelectorAll('.filter-option');
    options.forEach(function(opt) {
        opt.classList.remove('active');
    });
    target.classList.add('active');
    filterState[filterName] = target.dataset.value;
    updateFilterBadge();
});

function applyFilters() {
    var form = document.getElementById('filter-form');
    if (!form) return;
    var filterNames = ['category', 'gender', 'size', 'color', 'sort'];
    filterNames.forEach(function(name) {
        var oldInput = form.querySelector('input[name="' + name + '"]');
        if (oldInput) {
            oldInput.remove();
        }
    });
    if (filterState.category) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'category';
        input.value = filterState.category;
        form.appendChild(input);
    }
    if (filterState.gender) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'gender';
        input.value = filterState.gender;
        form.appendChild(input);
    }
    if (filterState.size) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'size';
        input.value = filterState.size;
        form.appendChild(input);
    }
    if (filterState.color) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'color';
        input.value = filterState.color;
        form.appendChild(input);
    }
    if (filterState.sort && filterState.sort !== 'newest') {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'sort';
        input.value = filterState.sort;
        form.appendChild(input);
    }
    closeFilterPopup();
    form.submit();
}

function resetAllFilters() {
    filterState = {
        category: '',
        gender: '',
        size: '',
        color: '',
        sort: 'newest'
    };
    var groups = document.querySelectorAll('.filter-popup-group .filter-options');
    groups.forEach(function(group) {
        var options = group.querySelectorAll('.filter-option');
        options.forEach(function(option) {
            option.classList.remove('active');
            if (option.dataset.value === '') {
                option.classList.add('active');
            }
        });
    });
    updateFilterBadge();
}

document.getElementById('filter-popup')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeFilterPopup();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeFilterPopup();
    }
});

document.addEventListener('DOMContentLoaded', function() {
    syncFilterStateFromURL();
    updateFilterBadge();
    var hasFilter = filterState.category || filterState.gender || filterState.size || filterState.color;
    if (hasFilter) {
        updateFilterBadge();
    }
});

// ============================================ */
// 🔥 CUSTOM SELECT (SAMA SEPERTI KATALOG) */
// ============================================ */

function toggleDropdown(trigger) {
    var wrapper = trigger.closest('.custom-select-wrapper');
    if (!wrapper) return;
    var dropdown = wrapper.querySelector('.custom-select-dropdown');
    if (!dropdown) return;
    var isOpen = dropdown.classList.contains('open');
    var allDropdowns = document.querySelectorAll('.custom-select-dropdown.open');
    allDropdowns.forEach(function(d) {
        if (d !== dropdown) {
            d.classList.remove('open');
            var t = d.closest('.custom-select-wrapper')?.querySelector('.custom-select-trigger');
            if (t) t.classList.remove('open');
        }
    });
    if (isOpen) {
        dropdown.classList.remove('open');
        trigger.classList.remove('open');
    } else {
        dropdown.classList.add('open');
        trigger.classList.add('open');
    }
}

function closeAllDropdowns() {
    var allDropdowns = document.querySelectorAll('.custom-select-dropdown.open');
    allDropdowns.forEach(function(dropdown) {
        dropdown.classList.remove('open');
        var trigger = dropdown.closest('.custom-select-wrapper')?.querySelector('.custom-select-trigger');
        if (trigger) {
            trigger.classList.remove('open');
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    var triggers = document.querySelectorAll('.custom-select-trigger');
    triggers.forEach(function(trigger) {
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleDropdown(this);
        });
    });

    var items = document.querySelectorAll('.dropdown-item');
    items.forEach(function(item) {
        item.addEventListener('click', function(e) {
            e.stopPropagation();
            var wrapper = this.closest('.custom-select-wrapper');
            if (!wrapper) return;
            var trigger = wrapper.querySelector('.custom-select-trigger');
            var dropdown = wrapper.querySelector('.custom-select-dropdown');
            var name = wrapper.dataset.name;
            var value = this.dataset.value;
            var text = this.querySelector('span')?.textContent || '';
            var triggerText = trigger?.querySelector('.trigger-text');
            if (triggerText) {
                triggerText.textContent = text;
            }
            if (dropdown) {
                var allItems = dropdown.querySelectorAll('.dropdown-item');
                allItems.forEach(function(d) {
                    d.classList.remove('active');
                });
            }
            this.classList.add('active');
            if (dropdown) dropdown.classList.remove('open');
            if (trigger) trigger.classList.remove('open');
            var form = document.getElementById('filter-form');
            if (!form) return;
            var oldInput = form.querySelector('input[name="' + name + '"]');
            if (oldInput) {
                oldInput.remove();
            }
            if (value !== '') {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = value;
                form.appendChild(input);
            }
            form.submit();
        });
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-select-wrapper')) {
            closeAllDropdowns();
        }
    });

    window.buyNow = function(productId) {
        fetch('/api/products/' + productId + '/variants', {
            headers: { 'Accept': 'application/json' }
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success && data.variants && data.variants.length > 0) {
                if (typeof openVariantModal === 'function') {
                    window._buyNowMode = true;
                    openVariantModal(productId, 'buy_now');
                }
            } else {
                window._buyNowMode = true;
                if (typeof addToCartDirect === 'function') {
                    addToCartDirect(productId);
                }
            }
        })
        .catch(function() {
            window._buyNowMode = true;
            if (typeof addToCartDirect === 'function') {
                addToCartDirect(productId);
            }
        });
    };

    console.log('⚡ Flash Sale page ready');
});
</script>

@endsection