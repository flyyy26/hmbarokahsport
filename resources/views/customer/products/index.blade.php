@extends('layouts.customer')

@section('title', 'Katalog Produk - Barokah Sport')

@section('content')

<div class="catalog-container">
    <div class="katalog_top_container">
        <div class="catalog-header">
            <h1>
                @if(request('search'))
                    Hasil Pencarian: "{{ request('search') }}"
                @else
                    Katalog Produk
                @endif
            </h1>
            <p>
                @if(request('search'))
                    Menampilkan {{ $products->total() }} produk untuk "{{ request('search') }}"
                @else
                    Temukan produk terbaik dari Barokah Sport
                @endif
            </p>
            
            @if(request('search') && $products->total() > 0)
                <div style="margin-top: 0.5vw;">
                    <a href="{{ route('customer.products.index') }}" class="clear-search-catalog"
                    onmouseover="this.style.textDecoration='underline'"
                    onmouseout="this.style.textDecoration='none'">
                        ✕ Hapus pencarian
                    </a>
                </div>
            @endif
        </div>

        {{-- ============================================ --}}
        {{-- FILTER ROW - CUSTOM SELECT --}}
        {{-- ============================================ --}}
        <form id="filter-form" method="GET" action="{{ route('customer.products.index') }}" class="filter-row">
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
                <a href="{{ route('customer.products.index') }}" class="filter-reset">
                    ✕ Reset
                </a>
            @endif

            <span class="filter-count">{{ $products->total() }} produk</span>
        </form>
        <button type="button" class="filter-toggle-btn" onclick="openFilterPopup()">
            <iconify-icon icon="tabler:filter"></iconify-icon>
            Filter Produk
            <span class="filter-badge" id="filter-badge" style="display:none;">0</span>
        </button>
    </div>
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
                <div class="empty-icon"><iconify-icon icon="akar-icons:search"></iconify-icon></div>
                <p>Belum ada produk yang tersedia.</p>
            </div>
        @else
            <div class="product_layout_grid">
                @foreach ($products as $product)
                    @php
                        // 🔥 AMBIL DATA DISKON DARI PRODUCT (sudah dihitung di controller)
                        $hasDiscount = $product->has_discount ?? false;
                        $maxDiscountPercent = $product->max_discount_percent ?? 0;
                        $hasProductDiscount = $product->has_product_discount ?? false;
                        $productDiscountPercent = $product->product_discount_percent ?? 0;
                        $badgeLabel = $product->badge_label ?? null;
                        $discountLabel = $product->discount_label ?? '';
                        $priceLabel = $product->price_label ?? '';
                        
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
                        
                        // 🔥 AMBIL HARGA TERMURAH (SUDAH TERMASUK DISKON) - PAKAI DARI CONTROLLER
                        $minEffectivePrice = $product->min_effective_price ?? null;
                        $maxOriginalPrice = $product->max_price ?? null;
                        
                        // 🔥 HITUNG HARGA ASLI (TANPA DISKON) UNTUK PERBANDINGAN
                        $minOriginalPrice = $product->variants->min('price') ?? 0;
                        $maxOriginalPrice = $product->variants->max('price') ?? 0;
                        
                        // 🔥 FORMAT HARGA DISKON
                        $discountedPriceDisplay = '';
                        $originalPriceDisplay = '';
                        
                        if ($minEffectivePrice !== null && $maxOriginalPrice !== null) {
                            // Harga setelah diskon
                            if ($minEffectivePrice == $maxOriginalPrice) {
                                $discountedPriceDisplay = 'Rp ' . number_format($minEffectivePrice, 0, ',', '.');
                            } else {
                                $discountedPriceDisplay = 'Rp ' . number_format($minEffectivePrice, 0, ',', '.') . ' - Rp ' . number_format($maxOriginalPrice, 0, ',', '.');
                            }
                            
                            // Harga asli (coret)
                            if ($minOriginalPrice == $maxOriginalPrice) {
                                $originalPriceDisplay = 'Rp ' . number_format($minOriginalPrice, 0, ',', '.');
                            } else {
                                $originalPriceDisplay = 'Rp ' . number_format($minOriginalPrice, 0, ',', '.') . ' - Rp ' . number_format($maxOriginalPrice, 0, ',', '.');
                            }
                        } else {
                            // Fallback: hitung manual
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
                        
                        // 🔥 STATUS STOK
                        $isOutOfStock = $product->isOutOfStock();
                        $totalStock = $product->variants->sum('stock');
                        
                        // 🔥 TENTUKAN GAMBAR THUMBNAIL - PERBAIKAN DISINI
                        $thumbnailImage = null;
                        
                        // Cek apakah ada display_image dari controller (untuk filter warna)
                        if (isset($product->display_image) && $product->display_image) {
                            $thumbnailImage = $product->display_image;
                        } 
                        // Jika tidak ada, gunakan gambar produk pertama
                        elseif ($product->images->isNotEmpty()) {
                            $thumbnailImage = Storage::url($product->images->first()->image);
                        }
                    @endphp
                    
                    <div class="product_layout_box" data-product-id="{{ $product->id }}">
                        <div class="product_layout_img">
                            {{-- SKELETON LOADING --}}
                            <div class="skeleton-image" id="skeleton-{{ $product->id }}">
                                <div class="skeleton" style="width:100%;height:100%;"></div>
                            </div>
                            
                            <a href="{{ route('customer.products.show', $product->slug) }}">
                                @if($thumbnailImage)
                                    <img 
                                        src="{{ $thumbnailImage }}" 
                                        alt="{{ $product->name }} - {{ $selectedColor ?? 'default' }}"
                                        loading="lazy"
                                        width="300"
                                        height="300"
                                        decoding="async"
                                        class="loading"
                                        onload="this.classList.remove('loading'); this.classList.add('loaded'); document.getElementById('skeleton-{{ $product->id }}').classList.add('hidden');"
                                        onerror="this.onerror=null; this.src='{{ asset('images/placeholder.png') }}'; this.classList.remove('loading'); this.classList.add('loaded'); document.getElementById('skeleton-{{ $product->id }}').classList.add('hidden');">
                                @elseif($product->images->isNotEmpty())
                                    <img 
                                        src="{{ Storage::url($product->images->first()->image) }}" 
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
                                
                                {{-- BADGE HABIS --}}
                                @if($isOutOfStock)
                                    <span class="product_badge out-of-stock">HABIS</span>
                                @endif

                                {{-- BADGE DISKON --}}
                                @if($maxDiscount > 0)
                                    <span class="discount_badge">Diskon {{ round($maxDiscount) }}%</span>
                                @endif

                                @if(!empty($badgeText) && $maxDiscount == 0)
                                    <span class="discount_badge">{{ $badgeText }}</span>
                                @endif
                            </a>
                        </div>
                        
                        <div class="product_layout_content">
                            <h5>{{ $product->name }}</h5>
                            
                            <div class="product_layout_price">
                                @if($maxDiscount > 0)
                                    {{-- 🔥 ADA DISKON - TAMPILKAN HARGA CORET DAN HARGA DISKON --}}
                                    <div class="product_layout_price_box">
                                        <p class="price-discount">{{ $discountedPriceDisplay }}</p>
                                        <span class="price-original">{{ $originalPriceDisplay }}</span>
                                    </div>
                                @else
                                    {{-- TIDAK ADA DISKON --}}
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
    var filterState = {
        category: '{{ request('category') ?? '' }}',
        gender: '{{ request('gender') ?? '' }}',
        size: '{{ request('size') ?? '' }}',
        color: '{{ request('color') ?? '' }}',
        sort: '{{ request('sort', 'newest') }}'
    };

    var initialFilterState = Object.assign({}, filterState);

    // 🔥 BUKA POPUP FILTER
    function openFilterPopup() {
        var popup = document.getElementById('filter-popup');
        if (!popup) return;
        
        // Sync state dari URL
        syncFilterStateFromURL();
        
        // Reset aktifasi berdasarkan state
        applyFilterStateToPopup();
        
        popup.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    // 🔥 TUTUP POPUP FILTER
    function closeFilterPopup() {
        var popup = document.getElementById('filter-popup');
        if (!popup) return;
        
        popup.classList.remove('active');
        document.body.style.overflow = '';
    }

    // 🔥 SYNC FILTER STATE DARI URL
    function syncFilterStateFromURL() {
        var urlParams = new URLSearchParams(window.location.search);
        
        filterState.category = urlParams.get('category') || '';
        filterState.gender = urlParams.get('gender') || '';
        filterState.size = urlParams.get('size') || '';
        filterState.color = urlParams.get('color') || '';
        filterState.sort = urlParams.get('sort') || 'newest';
    }

    // 🔥 APPLY FILTER STATE KE POPUP
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

    // 🔥 UPDATE FILTER BADGE
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

    // 🔥 EVENT LISTENER UNTUK FILTER OPTIONS (DI POPUP)
    document.addEventListener('click', function(e) {
        var target = e.target.closest('.filter-popup-group .filter-option');
        if (!target) return;
        
        var group = target.closest('.filter-options');
        if (!group) return;
        
        var filterName = group.dataset.filter;
        
        // Hapus active dari semua di group yang sama
        var options = group.querySelectorAll('.filter-option');
        options.forEach(function(opt) {
            opt.classList.remove('active');
        });
        
        target.classList.add('active');
        
        // Update state
        filterState[filterName] = target.dataset.value;
        
        // Update badge
        updateFilterBadge();
    });

    // 🔥 APPLY FILTERS - SUBMIT FORM
    function applyFilters() {
        var form = document.getElementById('filter-form');
        if (!form) return;
        
        // Hapus input filter lama
        var filterNames = ['category', 'gender', 'size', 'color', 'sort'];
        filterNames.forEach(function(name) {
            var oldInput = form.querySelector('input[name="' + name + '"]');
            if (oldInput) {
                oldInput.remove();
            }
        });
        
        // Tambahkan input baru berdasarkan state
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
        
        // Tutup popup
        closeFilterPopup();
        
        // Submit form
        form.submit();
    }

    // 🔥 RESET ALL FILTERS
    function resetAllFilters() {
        filterState = {
            category: '',
            gender: '',
            size: '',
            color: '',
            sort: 'newest'
        };
        
        // Update UI
        var groups = document.querySelectorAll('.filter-popup-group .filter-options');
        groups.forEach(function(group) {
            var filterName = group.dataset.filter;
            var options = group.querySelectorAll('.filter-option');
            options.forEach(function(option) {
                option.classList.remove('active');
                if (option.dataset.value === '') {
                    option.classList.add('active');
                }
            });
        });
        
        // Update badge
        updateFilterBadge();
    }

    // 🔥 CLOSE POPUP ON OVERLAY CLICK
    document.getElementById('filter-popup')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeFilterPopup();
        }
    });

    // 🔥 ESCAPE KEY
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeFilterPopup();
        }
    });

    // 🔥 INIT - Update badge
    document.addEventListener('DOMContentLoaded', function() {
        syncFilterStateFromURL();
        updateFilterBadge();
        
        // Jika ada parameter filter, tampilkan badge
        var hasFilter = filterState.category || filterState.gender || filterState.size || filterState.color;
        if (hasFilter) {
            updateFilterBadge();
        }
    });
</script>

{{-- ============================================ --}}
{{-- JAVASCRIPT - CUSTOM SELECT --}}
{{-- ============================================ --}}
<script>
// ============================================
// CUSTOM SELECT - TOGGLE DROPDOWN
// ============================================

// 🔥 FUNGSI TOGGLE DROPDOWN
function toggleDropdown(trigger) {
    var wrapper = trigger.closest('.custom-select-wrapper');
    if (!wrapper) return;
    
    var dropdown = wrapper.querySelector('.custom-select-dropdown');
    if (!dropdown) return;
    
    var isOpen = dropdown.classList.contains('open');

    // Tutup semua dropdown lain
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

// 🔥 TUTUP DROPDOWN SAAT KLIK DI LUAR
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

// ============================================
// INITIALIZE
// ============================================
document.addEventListener('DOMContentLoaded', function() {

    // 🔥 EVENT LISTENER UNTUK TRIGGER
    var triggers = document.querySelectorAll('.custom-select-trigger');
    triggers.forEach(function(trigger) {
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleDropdown(this);
        });
    });

    // 🔥 SELECT ITEM
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

            // Update trigger text
            var triggerText = trigger?.querySelector('.trigger-text');
            if (triggerText) {
                triggerText.textContent = text;
            }

            // Update active state
            if (dropdown) {
                var allItems = dropdown.querySelectorAll('.dropdown-item');
                allItems.forEach(function(d) {
                    d.classList.remove('active');
                });
            }
            this.classList.add('active');

            // Close dropdown
            if (dropdown) dropdown.classList.remove('open');
            if (trigger) trigger.classList.remove('open');

            // 🔥 UPDATE FORM DAN SUBMIT
            var form = document.getElementById('filter-form');
            if (!form) return;
            
            // Hapus input lama jika ada
            var oldInput = form.querySelector('input[name="' + name + '"]');
            if (oldInput) {
                oldInput.remove();
            }

            // Buat input baru
            if (value !== '') {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = value;
                form.appendChild(input);
            }

            // Submit form
            form.submit();
        });
    });

    // 🔥 TUTUP DROPDOWN SAAT KLIK DI LUAR
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-select-wrapper')) {
            closeAllDropdowns();
        }
    });

    // 🔥 BUY NOW
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

    console.log('🛒 Katalog produk siap dengan custom select');
});

// ============================================
// COLOR FILTER IMAGE UPDATE
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // 🔥 TAMBAHKAN EVENT LISTENER UNTUK FILTER WARNA
    const colorItems = document.querySelectorAll('.custom-select-wrapper[data-name="color"] .dropdown-item');
    
    colorItems.forEach(function(item) {
        item.addEventListener('click', function() {
            // Ketika warna dipilih, form akan disubmit
            // Gambar akan diupdate oleh controller
            // Tapi kita bisa menambahkan loading state
            const productImages = document.querySelectorAll('.product-card .product-image img');
            productImages.forEach(function(img) {
                img.classList.add('loading');
            });
        });
    });
    
    // 🔥 HILANGKAN LOADING STATE SETELAH GAMBAR LOAD
    document.querySelectorAll('.product-card .product-image img').forEach(function(img) {
        if (img.complete) {
            img.classList.remove('loading');
            img.classList.add('loaded');
        } else {
            img.addEventListener('load', function() {
                this.classList.remove('loading');
                this.classList.add('loaded');
            });
            img.addEventListener('error', function() {
                this.classList.remove('loading');
            });
        }
    });
});

// 🔥 FUNGSI UNTUK UPDATE GAMBAR VIA AJAX (OPSIONAL)
function updateProductImagesByColor(color) {
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(function(card) {
        const productId = card.dataset.productId;
        if (!productId) return;
        
        const img = card.querySelector('.product-image img');
        if (!img) return;
        
        // Tampilkan loading state
        img.classList.add('loading');
        
        // Fetch gambar variant berdasarkan warna
        fetch('/api/products/' + productId + '/variant-image?color=' + encodeURIComponent(color), {
            headers: { 'Accept': 'application/json' }
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success && data.image) {
                img.src = data.image;
                img.alt = data.alt || img.alt;
            }
            img.classList.remove('loading');
            img.classList.add('loaded');
        })
        .catch(function() {
            img.classList.remove('loading');
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // 🔥 INTERSECTION OBSERVER UNTUK LAZY LOAD
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    const src = img.dataset.src || img.src;
                    
                    // Jika gambar belum dimuat, load
                    if (!img.classList.contains('loaded')) {
                        img.src = src;
                    }
                    
                    observer.unobserve(img);
                }
            });
        }, {
            rootMargin: '50px 0px',
            threshold: 0.01
        });

        document.querySelectorAll('.product_layout_img img[loading="lazy"]').forEach(function(img) {
            imageObserver.observe(img);
        });
    }

    // 🔥 HANDLE IMAGE ERROR
    document.querySelectorAll('.product_layout_img img').forEach(function(img) {
        img.addEventListener('error', function() {
            this.onerror = null;
            this.src = '{{ asset('images/placeholder.png') }}';
            this.classList.remove('loading');
            this.classList.add('loaded');
            
            // Sembunyikan skeleton
            const skeleton = this.closest('.product_layout_img').querySelector('.skeleton-image');
            if (skeleton) {
                skeleton.classList.add('hidden');
            }
        });
    });
});

// 🔥 FUNGSI UNTUK PRELOAD GAMBAR
function preloadImages(images, callback) {
    let loaded = 0;
    const total = images.length;
    
    if (total === 0) {
        if (typeof callback === 'function') callback();
        return;
    }
    
    images.forEach(function(src) {
        const img = new Image();
        img.onload = function() {
            loaded++;
            if (loaded === total && typeof callback === 'function') {
                callback();
            }
        };
        img.onerror = function() {
            loaded++;
            if (loaded === total && typeof callback === 'function') {
                callback();
            }
        };
        img.src = src;
    });
}
</script>

@endsection