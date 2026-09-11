@extends('layouts.customer')

@section('title', $product->name . ' - Barokah Sport')

@section('content')

<div class="product_show_layout">

    {{-- Breadcrumb --}}
    <nav class="breadcrumb">
        <ul>
            <li><a href="{{ route('customer.home') }}">Beranda</a></li>
            <li>/</li>
            <li><a href="{{ route('customer.products.index') }}">Produk</a></li>
            <li>/</li>
            @if ($product->category)
                <li><a href="{{ route('customer.categories.show', $product->category) }}">{{ $product->category->name }}</a></li>
                <li>/</li>
            @endif
            <li>{{ $product->name }}</li>
        </ul>
    </nav>

    <div class="product_main_layout">

        {{-- ============================================ --}}
        {{-- LEFT COLUMN - GAMBAR & DESKRIPSI --}}
        {{-- ============================================ --}}
        <div class="product_first_container">

            {{-- GAMBAR --}}
            <div class="product_photo_grid">
                <div class="thumbnail_grid_layout_container">
                    <div class="thumbnail_grid_layout">
                        {{-- Thumbnail Grid --}}
                        <div class="thumbnail-grid" id="thumbnail-container">
                            @php
                                $allThumbnails = [];
                                $usedImages = [];

                                // 1. Product images
                                foreach ($product->images as $image) {
                                    $url = Storage::url($image->image);
                                    if (!in_array($url, $usedImages)) {
                                        $usedImages[] = $url;
                                        $allThumbnails[] = [
                                            'type' => 'product',
                                            'url' => $url,
                                            'variant_id' => null,
                                            'option_value_id' => null,
                                            'label' => 'Produk',
                                            'sort' => $image->sort_order ?? 0,
                                        ];
                                    }
                                }

                                // 2. Option value images (unique)
                                foreach ($product->options as $option) {
                                    foreach ($option->values as $value) {
                                        if ($value->image) {
                                            $url = Storage::url($value->image);
                                            if (!in_array($url, $usedImages)) {
                                                $usedImages[] = $url;
                                                $allThumbnails[] = [
                                                    'type' => 'option',
                                                    'url' => $url,
                                                    'variant_id' => null,
                                                    'option_value_id' => $value->id,
                                                    'label' => $value->value,
                                                    'sort' => 100 + ($value->sort_order ?? 0),
                                                ];
                                            }
                                        }
                                    }
                                }

                                usort($allThumbnails, function($a, $b) {
                                    return $a['sort'] <=> $b['sort'];
                                });
                            @endphp

                            @foreach ($allThumbnails as $thumbnail)
                                <button type="button"
                                        class="image-thumb {{ $loop->first ? 'active' : '' }}"
                                        data-image="{{ $thumbnail['url'] }}"
                                        data-type="{{ $thumbnail['type'] }}"
                                        data-option-value-id="{{ $thumbnail['option_value_id'] }}"
                                        onclick="handleThumbnailClick('{{ $thumbnail['url'] }}', {{ $thumbnail['option_value_id'] ?? 'null' }}, this)">
                                    <img src="{{ $thumbnail['url'] }}"
                                        alt="{{ $product->name }}"
                                        loading="lazy">
                                </button>
                            @endforeach
                        </div>

                        {{-- 🔥 SCROLL BUTTONS --}}
                        <button type="button" class="thumbnail-scroll-btn thumbnail-scroll-btn-up hidden" id="thumbnail-scroll-up" aria-label="Scroll up">
                            <iconify-icon icon="tabler:chevron-up" width="16" height="16"></iconify-icon>
                        </button>
                        <button type="button" class="thumbnail-scroll-btn thumbnail-scroll-btn-down hidden" id="thumbnail-scroll-down" aria-label="Scroll down">
                            <iconify-icon icon="tabler:chevron-down" width="16" height="16"></iconify-icon>
                        </button>
                    </div>
                </div>

                <div class="main-image-container" id="main-image-container">
                    @php
                        $mainImage = $product->images->first();
                        $mainImageUrl = $mainImage ? Storage::url($mainImage->image) : null;
                    @endphp

                    @if ($mainImageUrl)
                        <img src="{{ $mainImageUrl }}"
                            alt="{{ $product->name }}"
                            id="main-image"
                            class="fade-in">
                        
                        <div class="magnifier-glass" id="magnifier-glass"></div>

                        {{-- 🔥 FLASH SALE TIMER --}}
                        @if($isFlashSale && $flashSaleEndDate)
                            <div class="flash-sale-timer" id="flash-sale-timer" data-end="{{ $flashSaleEndDate }}">
                                <span class="timer-label">
                                    <span class="flash-icon">⚡</span>
                                    FLASH SALE
                                    <span class="timer-discount-badge">{{ round($flashSaleDiscountPercent) }}% OFF</span>
                                </span>
                                <div class="timer-group" id="timer-group">
                                    <div class="timer-block">
                                        <span class="timer-number" id="timer-days">00</span>
                                        <span class="timer-label-small">Hari</span>
                                    </div>
                                    <span class="timer-separator">:</span>
                                    <div class="timer-block">
                                        <span class="timer-number" id="timer-hours">00</span>
                                        <span class="timer-label-small">Jam</span>
                                    </div>
                                    <span class="timer-separator">:</span>
                                    <div class="timer-block">
                                        <span class="timer-number" id="timer-minutes">00</span>
                                        <span class="timer-label-small">Menit</span>
                                    </div>
                                    <span class="timer-separator">:</span>
                                    <div class="timer-block">
                                        <span class="timer-number" id="timer-seconds">00</span>
                                        <span class="timer-label-small">Detik</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="placeholder">📦</div>
                    @endif
                </div>
            </div>

            <div class="product-accordion">
                <div class="accordion-item">
                    <button class="accordion-header" onclick="toggleAccordion(this)">
                        Deskripsi Produk
                        <span class="accordion-icon open">
                            <iconify-icon icon="tabler:chevron-down"></iconify-icon>
                        </span>
                    </button>
                    <div class="accordion-body open">
                        <div class="accordion-body-inner product-description">
                            @if($product->description)
                                {!! $product->description !!}
                            @else
                                <p style="color: #94a3b8;">Tidak ada deskripsi untuk produk ini.</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- 2. DETAIL TEKNIS --}}
                <div class="accordion-item">
                    <button class="accordion-header" onclick="toggleAccordion(this)">
                        Detail Teknis
                        <span class="accordion-icon open">
                            <iconify-icon icon="tabler:chevron-down"></iconify-icon>
                        </span>
                    </button>
                    <div class="accordion-body open">
                        <div class="accordion-body-inner">
                            <div class="detail-item">
                                <span class="label">Kategori</span>
                                <span>{{ $product->category->name ?? '-' }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="label">Berat</span>
                                <span>{{ $firstVariant?->weight ?? '500' }} gram</span>
                            </div>
                            <div class="detail-item">
                                <span class="label">Bahan</span>
                                <span>{{ $product->material ?? 'Poliester, Diadora' }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="label">Jenis Kelamin</span>
                                <span>{{ $product->gender ? ucfirst($product->gender) : 'Unisex' }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="label">Warna</span>
                                <span>
                                    @if(!empty($colors))
                                        {{ implode(', ', $colors) }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                            <div class="detail-item">
                                <span class="label">Ukuran</span>
                                <span>
                                    @if(!empty($sizes))
                                        {{ implode(', ', $sizes) }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. FITUR --}}
                <div class="accordion-item">
                    <button class="accordion-header" onclick="toggleAccordion(this)">
                        Fitur
                        <span class="accordion-icon open">
                            <iconify-icon icon="tabler:chevron-down"></iconify-icon>
                        </span>
                    </button>
                    <div class="accordion-body open">
                        <div class="accordion-body-inner">
                            @php
                                $features = $product->features;
                            @endphp
                            @if($features->isNotEmpty())
                                <ul>
                                    @foreach($features as $feature)
                                        <li>
                                            • {{ $feature->name }}
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p style="color: #94a3b8;">Belum ada fitur untuk produk ini.</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- 4. ULASAN PEMBELI --}}
                <div class="accordion-item">
                    <button class="accordion-header" onclick="toggleAccordion(this)">
                        Ulasan Pembeli
                        <span class="accordion-icon open">
                            <iconify-icon icon="tabler:chevron-down"></iconify-icon>
                        </span>
                    </button>
                    <div class="accordion-body open">
                        <div class="accordion-body-inner">
                            <p style="color: #94a3b8;">Belum ada ulasan untuk produk ini.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ============================================ --}}
        {{-- RIGHT COLUMN - INFO & PEMBELIAN --}}
        {{-- ============================================ --}}
        <div class="product_second_container">
            <div class="product_info_layout">

                {{-- Kategori --}}
                <span class="product_category_badge">{{ $product->category->name ?? 'Tanpa Kategori' }}</span>

                <div class="product_price_display product_price_display_mobile" id="price-display-container">
                    @if($hasAnyDiscount)
                        {{-- Ada Diskon - Tampilkan Range Harga dengan Coret --}}
                        <div class="product_price_box" id="default-price-box">
                            <span class="price-current discounted" id="display-price">
                                {{ $defaultDisplayPrice }}
                            </span>
                            <span class="price-original" id="original-price-display">
                                {{ $defaultOriginalPrice }}
                            </span>
                        </div>
                    @else
                        {{-- Tidak Ada Diskon --}}
                        <span class="price-current" id="display-price">
                            {{ $defaultDisplayPrice }}
                        </span>
                    @endif
                </div>
                {{-- Nama Produk --}}
                <h1 class="product_name">{{ $product->name }}</h1>

                {{-- Harga --}}
                @php
                    $firstVariant = $product->variants->first();
                    $totalStock = $product->variants->sum('stock');
                    
                    // 🔥 DATA DARI CONTROLLER
                    $hasAnyDiscount = $hasAnyDiscount ?? false;
                    $maxDiscountPercent = $maxDiscountPercent ?? 0;
                    $hasProductDiscount = $hasProductDiscount ?? false;
                    $productDiscountPercent = $productDiscountPercent ?? 0;
                    $defaultDisplayPrice = $defaultDisplayPrice ?? '';
                    $defaultOriginalPrice = $defaultOriginalPrice ?? '';
                    $defaultDiscountBadge = $defaultDiscountBadge ?? '';
                    $minEffective = $minEffective ?? 0;
                    $maxEffective = $maxEffective ?? 0;
                    $minPrice = $minPrice ?? 0;
                    $maxPrice = $maxPrice ?? 0;
                @endphp

                <div class="product_price_display" id="price-display-container">
                    @if($hasAnyDiscount)
                        {{-- Ada Diskon - Tampilkan Range Harga dengan Coret --}}
                        <div class="product_price_box" id="default-price-box">
                            <span class="price-current discounted" id="display-price">
                                {{ $defaultDisplayPrice }}
                            </span>
                            <span class="price-original" id="original-price-display">
                                {{ $defaultOriginalPrice }}
                            </span>
                            <span class="discount-badge">{{ $defaultDiscountBadge }}</span>
                            
                            {{-- 🔥 TAMPILKAN INFO DISKON PRODUK (jika ada) --}}
                            @if($hasProductDiscount && $productDiscountPercent > 0)
                                <span class="product-discount-info" style="font-size:0.6vw;color:#16a34a;display:block;margin-top:0.2vw;">
                                    {{ round($productDiscountPercent) }}%
                                </span>
                            @endif
                        </div>
                    @else
                        {{-- Tidak Ada Diskon --}}
                        <span class="price-current" id="display-price">
                            {{ $defaultDisplayPrice }}
                        </span>
                    @endif
                </div>

                <div class="product_stock_status {{ $totalStock > 0 ? 'in-stock' : 'out-of-stock' }}" id="stock-display">
                    {{ $totalStock > 0 ? 'Stok: ' . $totalStock : 'Stok Habis' }}
                </div>

                {{-- ============================================ --}}
                {{-- VARIAN --}}
                {{-- ============================================ --}}
                @if ($product->options->isNotEmpty())
                    <div class="variant-section">
                        @foreach ($product->options as $option)
                            <div class="variant_choose_box">
                                <div class="variant-label-wrapper">
                                    <label class="variant-label">{{ $option->name }}</label>
                                    
                                    @if (strtolower($option->name) === 'ukuran' || strtolower($option->name) === 'size')
                                        <button type="button" class="size-guide-btn" onclick="openSizeGuide()">
                                            Panduan Ukuran
                                        </button>
                                    @endif
                                </div>
                                <div class="variant-options" data-option-id="{{ $option->id }}">
                                    @php $values = $option->values->sortBy('sort_order'); @endphp
                                    @foreach ($values as $value)
                                        @php
                                            $hasStock = false;
                                            foreach ($product->variants as $variant) {
                                                if ($variant->stock > 0) {
                                                    foreach ($variant->variantValues as $vv) {
                                                        if ($vv->product_option_value_id == $value->id) {
                                                            $hasStock = true;
                                                            break 2;
                                                        }
                                                    }
                                                }
                                            }
                                        @endphp
                                        <button type="button"
                                                class="variant-option"
                                                data-option-id="{{ $option->id }}"
                                                data-option-name="{{ $option->name }}"
                                                data-value-id="{{ $value->id }}"
                                                data-value-name="{{ $value->value }}"
                                                data-image="{{ $value->image ? Storage::url($value->image) : '' }}"
                                                {{ !$hasStock ? 'disabled' : '' }}>
                                            {{ $value->value }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                
                <div class="product-accordion product-accordion-mobile">
                    <div class="accordion-item">
                        <button class="accordion-header" onclick="toggleAccordion(this)">
                            Deskripsi Produk
                            <span class="accordion-icon open">
                                <iconify-icon icon="tabler:chevron-down"></iconify-icon>
                            </span>
                        </button>
                        <div class="accordion-body open">
                            <div class="accordion-body-inner product-description">
                                @if($product->description)
                                    {!! $product->description !!}
                                @else
                                    <p style="color: #94a3b8;">Tidak ada deskripsi untuk produk ini.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item size-guide-accordion">
                        <button class="accordion-header" onclick="toggleAccordion(this)">
                            Panduan Ukuran
                            <span class="accordion-icon open">
                                <iconify-icon icon="tabler:chevron-down"></iconify-icon>
                            </span>
                        </button>
                        <div class="accordion-body open">
                            <div class="accordion-body-inner">
                                @php
                                    $sizeGuides = $product->category ? $product->category->sizeGuides : collect();
                                    $dimensionLabels = $product->category ? $product->category->dimension_labels : [];
                                @endphp

                                @if($sizeGuides->isNotEmpty() && !empty($dimensionLabels))
                                    <div class="size-guide-table-wrapper">
                                        <table class="size-guide-table">
                                            <thead>
                                                <tr>
                                                    <th>Ukuran</th>
                                                    @foreach($dimensionLabels as $label)
                                                        <th>{{ $label }} (cm)</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($sizeGuides as $guide)
                                                    <tr>
                                                        <td><strong>{{ $guide->size }}</strong></td>
                                                        @foreach($dimensionLabels as $label)
                                                            <td>{{ $guide->dimensions[$label] ?? '-' }}</td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p style="color: #94a3b8; font-size: 3vw;">Belum ada panduan ukuran untuk kategori ini.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- 2. DETAIL TEKNIS --}}
                    <div class="accordion-item">
                        <button class="accordion-header" onclick="toggleAccordion(this)">
                            Detail Teknis
                            <span class="accordion-icon open">
                                <iconify-icon icon="tabler:chevron-down"></iconify-icon>
                            </span>
                        </button>
                        <div class="accordion-body open">
                            <div class="accordion-body-inner">
                                <div class="detail-item">
                                    <span class="label">Kategori</span>
                                    <span>{{ $product->category->name ?? '-' }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="label">Berat</span>
                                    <span>{{ $firstVariant?->weight ?? '500' }} gram</span>
                                </div>
                                <div class="detail-item">
                                    <span class="label">Bahan</span>
                                    <span>{{ $product->material ?? 'Poliester, Diadora' }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="label">Jenis Kelamin</span>
                                    <span>{{ $product->gender ? ucfirst($product->gender) : 'Unisex' }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="label">Warna</span>
                                    <span>
                                        @if(!empty($colors))
                                            {{ implode(', ', $colors) }}
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <span class="label">Ukuran</span>
                                    <span>
                                        @if(!empty($sizes))
                                            {{ implode(', ', $sizes) }}
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. FITUR --}}
                    <div class="accordion-item">
                        <button class="accordion-header" onclick="toggleAccordion(this)">
                            Fitur
                            <span class="accordion-icon open">
                                <iconify-icon icon="tabler:chevron-down"></iconify-icon>
                            </span>
                        </button>
                        <div class="accordion-body open">
                            <div class="accordion-body-inner">
                                @php
                                    $features = $product->features;
                                @endphp
                                @if($features->isNotEmpty())
                                    <ul>
                                        @foreach($features as $feature)
                                            <li>
                                                • {{ $feature->name }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p style="color: #94a3b8;">Belum ada fitur untuk produk ini.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- 4. ULASAN PEMBELI --}}
                    <div class="accordion-item">
                        <button class="accordion-header" onclick="toggleAccordion(this)">
                            Ulasan Pembeli
                            <span class="accordion-icon open">
                                <iconify-icon icon="tabler:chevron-down"></iconify-icon>
                            </span>
                        </button>
                        <div class="accordion-body open">
                            <div class="accordion-body-inner">
                                <p style="color: #94a3b8;">Belum ada ulasan untuk produk ini.</p>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ============================================ --}}
                {{-- TOMBOL AKSI --}}
                {{-- ============================================ --}}
                <div class="action-buttons">
                    <form action="{{ route('customer.cart.add') }}" method="POST" id="add-to-cart-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="variant_id" id="selected-variant" value="">
                        <input type="hidden" name="variant_values" id="selected-variant-values" value="">

                        <div class="action-row">
                            <div class="quantity-wrapper">
                                <button type="button" class="qty-btn" data-action="decrease">−</button>
                                <input type="number" name="quantity" id="qty-input" value="1" min="1"
                                    max="{{ $totalStock > 0 ? $totalStock : 1 }}"
                                    class="qty-input">
                                <button type="button" class="qty-btn" data-action="increase">+</button>
                            </div>

                            <button type="submit"
                                    id="add-to-cart-btn"
                                    class="btn-add-to-cart"
                                    disabled>
                                Pilih Varian
                            </button>

                            {{-- 🔥 WISHLIST BUTTON - DENGAN STATUS ACTIVE --}}
                            <button type="button"
                                    id="wishlist-toggle-product"
                                    class="btn-wishlist {{ $inWishlist ? 'active' : '' }}"
                                    data-product-id="{{ $product->id }}"
                                    onclick="toggleWishlist({{ $product->id }})">
                                @if($inWishlist)
                                    ❤️
                                @else
                                    🤍
                                @endif
                            </button>
                        </div>

                        <button type="button"
                                id="buy-now-btn"
                                class="btn-buy-now"
                                disabled
                                onclick="buyNow()">
                            Pilih Varian
                        </button>
                    </form>

                    @if ($totalStock <= 0)
                        <p style="margin-top: 0.5vw; font-size: 0.7vw; color: #ef4444;">Maaf, produk ini sedang habis.</p>
                    @endif
                </div>

                <div class="action-buttons-mobile" style="display: none;">

                    {{-- Mobile Buy Now --}}
                    <button type="button"
                            class="mobile-btn-buy-now"
                            id="mobile-buy-now-btn"
                            onclick="openVariantPopup('buy_now')">
                        Beli Sekarang
                    </button>
                    <button type="button"
                            class="mobile-btn-add-to-cart"
                            id="mobile-add-to-cart-btn"
                            onclick="openVariantPopup('add_to_cart')">
                        <iconify-icon icon="solar:cart-linear"></iconify-icon>
                    </button>

                    <button type="button"
                            class="mobile-btn-wishlist {{ $inWishlist ? 'active' : '' }}"
                            id="mobile-wishlist-btn"
                            data-product-id="{{ $product->id }}">
                        @if($inWishlist)
                            <iconify-icon icon="solar:heart-bold" style="color: #ef4444;"></iconify-icon>
                        @else
                            <iconify-icon icon="solar:heart-linear"></iconify-icon>
                        @endif
                    </button>

                    @if ($totalStock <= 0)
                        <p style="margin-top: 2vw; font-size: 3vw; color: #ef4444; text-align: center;">Maaf, produk ini sedang habis.</p>
                    @endif
                </div>

                {{-- SHARE --}}
                <div class="share-section">
                    <span>Bagikan:</span>
                    <button onclick="shareProduct()" class="share-btn">🔗</button>
                </div>

            </div>
        </div>

    </div>

    <div id="size-guide-overlay" class="size-guide-overlay" onclick="closeSizeGuide()">
        <div class="size-guide-modal" onclick="event.stopPropagation()">
            <div class="size-guide-header">
                <h3>Panduan Ukuran</h3>
                <button type="button" class="size-guide-close" onclick="closeSizeGuide()">✕</button>
            </div>

            <div class="size-guide-body">
                <p class="size-guide-subtitle">Panduan ukuran dalam centimeter (cm).</p>

                <div class="size-guide-table-wrapper">
                    <table class="size-guide-table">
                        <thead>
                            <tr>
                                <th>Ukuran</th>
                                @php
                                    $sizeGuides = $product->category ? $product->category->sizeGuides : collect();
                                    $dimensionLabels = $product->category ? $product->category->dimension_labels : [];
                                @endphp
                                @foreach($dimensionLabels as $label)
                                    <th>{{ $label }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @if($sizeGuides->isNotEmpty())
                                @foreach($sizeGuides as $guide)
                                    <tr>
                                        <td><strong>{{ $guide->size }}</strong></td>
                                        @foreach($dimensionLabels as $label)
                                            <td>{{ $guide->dimensions[$label] ?? '-' }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @else
                                {{-- Fallback hardcode --}}
                                <tr><td><strong>S</strong></td><td>57</td><td>58</td><td>68</td></tr>
                                <tr><td><strong>M</strong></td><td>61</td><td>59</td><td>70</td></tr>
                                <tr><td><strong>L</strong></td><td>65</td><td>60</td><td>72</td></tr>
                                <tr><td><strong>XL</strong></td><td>69</td><td>61</td><td>74</td></tr>
                                <tr><td><strong>XXL</strong></td><td>73</td><td>62</td><td>76</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="size-guide-note">
                    <p><strong>Tips memilih ukuran:</strong></p>
                    <ul>
                        <li>Ukur menggunakan meteran kain pada posisi yang tepat</li>
                        <li>Pilih ukuran yang sesuai dengan ukuran tubuh Anda</li>
                        <li>Jika di antara dua ukuran, pilih ukuran yang lebih besar</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="variant-popup" class="variant-popup-overlay">
    <div class="variant-popup">
        {{-- Handle --}}
        <div class="variant-popup-handle"></div>

        {{-- Header --}}
        <div class="variant-popup-header">
            <h3>Pilih Varian</h3>
            <button type="button" class="variant-popup-close" onclick="closeVariantPopup()">✕</button>
        </div>

        {{-- Product Info --}}
        <div class="variant-popup-product">
            <div class="variant-popup-product-image">
                <img id="popup-product-image" src="{{ $product->images->first() ? Storage::url($product->images->first()->image) : '' }}" alt="{{ $product->name }}">
            </div>
            <div class="variant-popup-product-info">
                <div class="product-name">{{ $product->name }}</div>
                <div class="product-price" id="popup-price-display">
                    {{ $defaultDisplayPrice }}
                    @if($hasAnyDiscount)
                        <span class="original-price">{{ $defaultOriginalPrice }}</span>
                        <span class="discount-badge">{{ $defaultDiscountBadge }}</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Variant Options --}}
        <div class="variant-popup-options" id="popup-variant-options">
            @if ($product->options->isNotEmpty())
                @foreach ($product->options as $option)
                    <div class="variant-group">
                        <span class="variant-group-label">{{ $option->name }}</span>
                        <div class="variant-group-options" data-option-id="{{ $option->id }}">
                            @php $values = $option->values->sortBy('sort_order'); @endphp
                            @foreach ($values as $value)
                                @php
                                    $hasStock = false;
                                    foreach ($product->variants as $variant) {
                                        if ($variant->stock > 0) {
                                            foreach ($variant->variantValues as $vv) {
                                                if ($vv->product_option_value_id == $value->id) {
                                                    $hasStock = true;
                                                    break 2;
                                                }
                                            }
                                        }
                                    }
                                @endphp
                                <button type="button"
                                        class="popup-variant-option"
                                        data-option-id="{{ $option->id }}"
                                        data-value-id="{{ $value->id }}"
                                        data-value-name="{{ $value->value }}"
                                        data-image="{{ $value->image ? Storage::url($value->image) : '' }}"
                                        {{ !$hasStock ? 'disabled' : '' }}>
                                    {{ $value->value }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Stock Info --}}
        <div class="variant-popup-stock" id="popup-stock-display">
            <span class="in-stock">Stok: {{ $product->variants->sum('stock') }}</span>
        </div>

        {{-- Quantity --}}
        <div class="variant-popup-quantity">
            <span class="qty-label">Jumlah</span>
            <div class="qty-wrapper">
                <button type="button" class="qty-btn" data-action="decrease" id="popup-qty-decrease">−</button>
                <input type="number" name="popup_qty" id="popup-qty-input" value="1" min="1"
                    max="{{ $product->variants->sum('stock') > 0 ? $product->variants->sum('stock') : 1 }}"
                    class="qty-input">
                <button type="button" class="qty-btn" data-action="increase" id="popup-qty-increase">+</button>
            </div>
        </div>

        {{-- Actions --}}
        <div class="variant-popup-actions">
            <button type="button" class="popup-btn-add-to-cart" id="popup-add-to-cart" disabled>
                Pilih Varian
            </button>
            <button type="button" class="popup-btn-buy-now" id="popup-buy-now" disabled>
                Pilih Varian
            </button>
            <button type="button" class="popup-btn-wishlist" id="popup-wishlist">
                🤍 Tambah ke Wishlist
            </button>
        </div>
    </div>
</div>

{{-- ============================================ --}}
{{-- RECOMMENDED PRODUCTS --}}
{{-- ============================================ --}}
@if ($recommendedProducts->isNotEmpty())
    <section class="recommended-products">
        <div class="heading_product_layout">
            <h3>REKOMENDASI PRODUK</h3>
            <a href="{{ route('customer.products.index') }}">LIHAT SEMUA</a>
        </div>
        <div class="product_layout_grid">
            @foreach($recommendedProducts as $related)
            
            <div class="product_layout_box" data-product-id="{{ $related->id }}">
                <div class="product_layout_img">
                    <a href="{{ route('customer.products.show', $related->slug) }}">
                        @php
                            // Cek gambar dari product images
                            $imageUrl = asset('images/product_dummy.png');
                            if ($related->images->first() && Storage::disk('public')->exists($related->images->first()->image)) {
                                $imageUrl = Storage::url($related->images->first()->image);
                            }
                        @endphp
                        <img src="{{ $imageUrl }}" alt="{{ $related->name }}">
                    </a>
                    @php
                        // 🔥 CEK STOK (TERMASUK YANG HABIS)
                        $totalStock = $related->variants->sum('stock');
                        $isOutOfStock = $totalStock <= 0;
                        
                        // 🔥 HITUNG DISKON TERBESAR DARI VARIAN PRODUK
                        $maxDiscount = 0;
                        foreach ($related->variants as $variant) {
                            if ($variant->discount_price && $variant->discount_price < $variant->price) {
                                $discount = round((($variant->price - $variant->discount_price) / $variant->price) * 100);
                                if ($discount > $maxDiscount) {
                                    $maxDiscount = $discount;
                                }
                            }
                        }
                        
                        // 🔥 CEK APAKAH ADA DISKON
                        $hasDiscount = $maxDiscount > 0;
                    @endphp
                    @if($isOutOfStock)
                        <span class="product_badge out-of-stock">HABIS</span>
                    @endif
                    @if($hasDiscount)
                        <span class="discount_badge">Diskon {{ $maxDiscount }}%</span>
                    @endif
                </div>
                <div class="product_layout_content">
                    <h5>{{ $related->name }}</h5>
                    <div class="product_layout_price">
                        @php
                            // 🔥 HITUNG HARGA EFEKTIF (HARGA SETELAH DISKON)
                            $effectivePrices = [];
                            $originalPrices = [];
                            foreach ($related->variants as $variant) {
                                $effectivePrices[] = $variant->discount_price ? (float) $variant->discount_price : (float) $variant->price;
                                $originalPrices[] = (float) $variant->price;
                            }
                            
                            $minEffective = !empty($effectivePrices) ? min($effectivePrices) : 0;
                            $maxEffective = !empty($effectivePrices) ? max($effectivePrices) : 0;
                            $minOriginal = !empty($originalPrices) ? min($originalPrices) : 0;
                            $maxOriginal = !empty($originalPrices) ? max($originalPrices) : 0;
                        @endphp
                        
                        @if($hasDiscount)
                            <div class="product_layout_price_box">
                                {{-- Harga Diskon --}}
                                @if($minEffective == $maxEffective)
                                    <p class="price-discount">Rp {{ number_format($minEffective, 0, ',', '.') }}</p>
                                @else
                                    <p class="price-discount">Rp {{ number_format($minEffective, 0, ',', '.') }} - Rp {{ number_format($maxEffective, 0, ',', '.') }}</p>
                                @endif
                                
                                {{-- Harga Original (Coret) --}}
                                @if($minOriginal == $maxOriginal)
                                    <span class="price-original">Rp {{ number_format($minOriginal, 0, ',', '.') }}</span>
                                @else
                                    <span class="price-original">Rp {{ number_format($minOriginal, 0, ',', '.') }} - Rp {{ number_format($maxOriginal, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        @else
                            {{-- Tanpa Diskon --}}
                            @if($minEffective == $maxEffective)
                                <p>Rp {{ number_format($minEffective, 0, ',', '.') }}</p>
                            @else
                                <p>Rp {{ number_format($minEffective, 0, ',', '.') }} - Rp {{ number_format($maxEffective, 0, ',', '.') }}</p>
                            @endif
                        @endif
                    </div>
                </div>
                <div class="product_layout_button">
                    {{-- 🔥 TOMBOL BELI SEKARANG DI-DISABLE JIKA STOK HABIS --}}
                    <button class="buy_now_btn {{ $isOutOfStock ? 'disabled' : '' }}" 
                            onclick="{{ $isOutOfStock ? '' : 'buyNow(' . $related->id . ')' }}"
                            {{ $isOutOfStock ? 'disabled' : '' }}>
                        {{ $isOutOfStock ? 'HABIS' : 'BELI SEKARANG' }}
                    </button>
                    <button class="add_to_cart_btn {{ $isOutOfStock ? 'disabled' : '' }}" 
                            onclick="{{ $isOutOfStock ? '' : 'addToCart(' . $related->id . ')' }}"
                            {{ $isOutOfStock ? 'disabled' : '' }}>
                        <iconify-icon icon="solar:cart-linear"></iconify-icon>
                    </button>
                    <button class="add_to_wishlist_btn" 
                            data-product-id="{{ $related->id }}"
                            data-in-wishlist="{{ in_array($related->id, array_keys(session()->get('wishlist', []))) ? 'true' : 'false' }}"
                            onclick="addToWishlist({{ $related->id }})">
                        @if(in_array($related->id, array_keys(session()->get('wishlist', []))))
                            <iconify-icon icon="solar:heart-bold" style="color: #ef4444;"></iconify-icon>
                        @else
                            <iconify-icon icon="solar:heart-linear"></iconify-icon>
                        @endif
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </section>
@endif

<script>
        // ============================================ */
    // 🔥 MOBILE VARIANT POPUP - SHOPEE STYLE */
    // ============================================ */

    // Popup State
    var popupSelectedValues = {};
    var popupCurrentVariant = null;
    var popupAction = null; // 'add_to_cart' atau 'buy_now'

    // DOM Elements
    var variantPopup = document.getElementById('variant-popup');
    var popupOptionsContainer = document.getElementById('popup-variant-options');
    var popupStockDisplay = document.getElementById('popup-stock-display');
    var popupPriceDisplay = document.getElementById('popup-price-display');
    var popupProductImage = document.getElementById('popup-product-image');
    var popupQtyInput = document.getElementById('popup-qty-input');
    var popupAddToCartBtn = document.getElementById('popup-add-to-cart');
    var popupBuyNowBtn = document.getElementById('popup-buy-now');
    var popupWishlistBtn = document.getElementById('popup-wishlist');

    // ============================================ */
    // OPEN POPUP - HANYA UNTUK ADD TO CART DAN BUY NOW */
    // ============================================ */

    function openVariantPopup(action) {
        // 🔥 JIKA ACTION ADALAH WISHLIST, JANGAN BUKA POPUP
        if (action === 'wishlist') {
            console.log('⚠️ Wishlist tidak menggunakan popup, langsung tambahkan');
            return;
        }
        
        if (!variantPopup) return;

        // Reset state
        popupSelectedValues = {};
        popupCurrentVariant = null;
        popupAction = action;

        // Reset all options
        document.querySelectorAll('#popup-variant-options .popup-variant-option').forEach(function(btn) {
            btn.classList.remove('active');
            // 🔥 PASTIKAN TOMBOL TIDAK DISABLE SAAT RESET
            btn.disabled = false;
            btn.classList.remove('disabled');
        });

        // Reset quantity
        var totalStock = {{ $product->variants->sum('stock') }};
        popupQtyInput.value = 1;
        popupQtyInput.max = totalStock > 0 ? totalStock : 1;

        // 🔥 Sembunyikan tombol yang tidak sesuai dengan action
        if (action === 'add_to_cart') {
            popupAddToCartBtn.style.display = 'block';
            popupBuyNowBtn.style.display = 'none';
            popupAddToCartBtn.disabled = true;
            popupAddToCartBtn.textContent = 'Pilih Varian';
            document.querySelector('.variant-popup-header h3').textContent = 'Pilih Varian';
        } else if (action === 'buy_now') {
            popupAddToCartBtn.style.display = 'none';
            popupBuyNowBtn.style.display = 'block';
            popupBuyNowBtn.disabled = true;
            popupBuyNowBtn.textContent = 'Pilih Varian';
            document.querySelector('.variant-popup-header h3').textContent = 'Pilih Varian';
        }

        popupWishlistBtn.style.display = 'none';

        // Show popup
        variantPopup.classList.add('active');
        document.body.style.overflow = 'hidden';

        // 🔥 UPDATE AVAILABLE VARIANTS - TANPA SELECTED VALUES
        updatePopupAvailableVariants();
    }


    function closeVariantPopup() {
        if (!variantPopup) return;
        variantPopup.classList.remove('active');
        document.body.style.overflow = '';
    }

    // ============================================ */
    // POPUP VARIANT CLICK */
    // ============================================ */

    document.addEventListener('click', function(e) {
        var target = e.target.closest('.popup-variant-option');
        if (!target) return;
        if (target.disabled) return;

        var optionId = target.dataset.optionId;
        var valueId = target.dataset.valueId;
        var image = target.dataset.image || '';

        var group = target.closest('.variant-group-options');

        // 🔥 HAPUS ACTIVE DARI GRUP YANG SAMA
        group.querySelectorAll('.popup-variant-option').forEach(function(btn) {
            btn.classList.remove('active');
        });

        target.classList.add('active');
        popupSelectedValues[optionId] = parseInt(valueId);

        // 🔥 UPDATE GAMBAR JIKA WARNA
        var optionName = target.closest('.variant-group').querySelector('.variant-group-label');
        var isColor = optionName && (
            optionName.textContent.toLowerCase() === 'warna' ||
            optionName.textContent.toLowerCase() === 'color'
        );

        if (isColor && image) {
            popupProductImage.src = image;
        }

        // 🔥 CARI VARIAN YANG COCOK
        var variant = findPopupVariantByValues(popupSelectedValues, true);

        if (variant) {
            popupCurrentVariant = variant;
            var totalOptions = document.querySelectorAll('#popup-variant-options .variant-group').length;
            var selectedCount = Object.keys(popupSelectedValues).length;

            // 🔥 UPDATE HARGA & STOK
            updatePopupPrice(variant);
            updatePopupStock(variant);

            // 🔥 UPDATE TOMBOL
            if (selectedCount === totalOptions) {
                if (variant.stock > 0) {
                    if (popupAction === 'add_to_cart') {
                        popupAddToCartBtn.disabled = false;
                        popupAddToCartBtn.textContent = 'Tambah ke Keranjang';
                    } else if (popupAction === 'buy_now') {
                        popupBuyNowBtn.disabled = false;
                        popupBuyNowBtn.textContent = 'Beli Sekarang';
                    }
                } else {
                    if (popupAction === 'add_to_cart') {
                        popupAddToCartBtn.disabled = true;
                        popupAddToCartBtn.textContent = 'Stok Habis';
                    } else if (popupAction === 'buy_now') {
                        popupBuyNowBtn.disabled = true;
                        popupBuyNowBtn.textContent = 'Stok Habis';
                    }
                }
            } else {
                if (popupAction === 'add_to_cart') {
                    popupAddToCartBtn.disabled = true;
                    popupAddToCartBtn.textContent = 'Pilih Varian';
                } else if (popupAction === 'buy_now') {
                    popupBuyNowBtn.disabled = true;
                    popupBuyNowBtn.textContent = 'Pilih Varian';
                }
            }

            // 🔥 UPDATE QTY MAX
            popupQtyInput.max = variant.stock > 0 ? variant.stock : 1;
            if (parseInt(popupQtyInput.value) > variant.stock && variant.stock > 0) {
                popupQtyInput.value = variant.stock;
            }
        } else {
            // 🔥 TIDAK ADA VARIAN YANG COCOK
            popupCurrentVariant = null;
            if (popupAction === 'add_to_cart') {
                popupAddToCartBtn.disabled = true;
                popupAddToCartBtn.textContent = 'Varian Tidak Tersedia';
            } else if (popupAction === 'buy_now') {
                popupBuyNowBtn.disabled = true;
                popupBuyNowBtn.textContent = 'Varian Tidak Tersedia';
            }
            
            // 🔥 RESET STOK DISPLAY
            var totalStock = {{ $product->variants->sum('stock') }};
            popupStockDisplay.innerHTML = totalStock > 0 
                ? '<span class="in-stock">Stok: ' + totalStock + '</span>' 
                : '<span class="out-of-stock">Stok Habis</span>';
        }

        updatePopupAvailableVariants();
    });

    // ============================================ */
    // POPUP QUANTITY */
    // ============================================ */

    document.addEventListener('click', function(e) {
        var target = e.target.closest('#popup-qty-decrease, #popup-qty-increase');
        if (!target) return;

        var value = parseInt(popupQtyInput.value) || 1;
        var max = parseInt(popupQtyInput.max) || 999;

        if (target.id === 'popup-qty-increase' && value < max) {
            value += 1;
        } else if (target.id === 'popup-qty-decrease' && value > 1) {
            value -= 1;
        }
        popupQtyInput.value = value;
    });

    // ============================================ */
    // POPUP HELPERS */
    // ============================================ */

    function findPopupVariantByValues(values, allowPartial = false) {
        var selectedValueIds = Object.values(values).map(Number).sort();
        var productVariants = @json($variantData ?? []);

        var matchingVariants = productVariants.filter(function(variant) {
            var variantValueIds = variant.values.map(Number).sort();
            if (!allowPartial && variantValueIds.length !== selectedValueIds.length) {
                return false;
            }

            return selectedValueIds.every(function(valueId) {
                return variantValueIds.includes(valueId);
            });
        });

        return matchingVariants.find(function(variant) {
            return variant.stock > 0;
        }) || matchingVariants[0] || null;
    }

    function updatePopupPrice(variant) {
        if (!popupPriceDisplay) return;

        var price = variant.effective_price ?? variant.price;
        var originalPrice = variant.price;
        var hasDiscount = variant.discount_percent > 0;
        var discountPercent = variant.discount_percent || 0;

        if (hasDiscount) {
            var discountBadge = Math.round(discountPercent) + '%';
            popupPriceDisplay.innerHTML = `
                Rp ${new Intl.NumberFormat('id-ID').format(price)}
                <span class="original-price">Rp ${new Intl.NumberFormat('id-ID').format(originalPrice)}</span>
                <span class="discount-badge">${discountBadge}</span>
            `;
        } else {
            popupPriceDisplay.innerHTML = `
                Rp ${new Intl.NumberFormat('id-ID').format(price)}
            `;
        }
    }

    function updatePopupStock(variant) {
        if (!popupStockDisplay) return;

        if (variant.stock > 0) {
            popupStockDisplay.innerHTML = `<span class="in-stock">Stok: ${variant.stock}</span>`;
        } else {
            popupStockDisplay.innerHTML = `<span class="out-of-stock">Stok Habis</span>`;
        }
    }

    function updatePopupAvailableVariants() {
        var selectedCount = Object.keys(popupSelectedValues).length;
        var hasSelectedValues = selectedCount > 0;
        var productVariants = @json($variantData ?? []);

        console.log('🔄 Updating popup variants, selectedCount:', selectedCount);

        document.querySelectorAll('#popup-variant-options .variant-group-options').forEach(function(group) {
            var optionId = group.dataset.optionId;
            var buttons = group.querySelectorAll('.popup-variant-option');

            buttons.forEach(function(button) {
                var valueId = parseInt(button.dataset.valueId);

                // 🔥 CEK APAKAH VALUE INI ADA DI VARIAN MANAPUN
                var hasAnyVariant = false;
                var hasStockForValue = false;

                for (var i = 0; i < productVariants.length; i++) {
                    var variant = productVariants[i];
                    if (variant.values.includes(valueId)) {
                        hasAnyVariant = true;
                        if (variant.stock > 0) {
                            hasStockForValue = true;
                            break;
                        }
                    }
                }

                // 🔥 JIKA TIDAK ADA VARIAN SAMA SEKALI, DISABLE
                if (!hasAnyVariant) {
                    button.disabled = true;
                    button.classList.add('disabled');
                    return;
                }

                // 🔥 JIKA BELUM ADA YANG DIPILIH, AKTIFKAN SEMUA YANG ADA STOK
                if (!hasSelectedValues) {
                    if (hasStockForValue) {
                        button.disabled = false;
                        button.classList.remove('disabled');
                    } else {
                        button.disabled = true;
                        button.classList.add('disabled');
                    }
                    return;
                }

                // 🔥 JIKA SUDAH ADA YANG DIPILIH, CEK KOMBINASI
                var tempValues = Object.assign({}, popupSelectedValues);
                tempValues[optionId] = valueId;

                var variant = findPopupVariantByValues(tempValues, true);
                var isAvailable = variant && variant.stock > 0;

                if (!isAvailable && variant) {
                    button.disabled = true;
                    button.classList.add('disabled');
                } else if (!isAvailable) {
                    button.disabled = true;
                    button.classList.add('disabled');
                } else {
                    button.disabled = false;
                    button.classList.remove('disabled');
                }
            });

            // 🔥 JIKA BELUM ADA YANG DIPILIH, JANGAN PAKSA PILIH
            if (!hasSelectedValues) {
                return;
            }

        });
    }

    // ============================================ */
    // POPUP ADD TO CART */
    // ============================================ */

    document.getElementById('popup-add-to-cart')?.addEventListener('click', function() {
        if (this.disabled) return;

        var variant = popupCurrentVariant;
        if (!variant) return;

        var quantity = parseInt(popupQtyInput.value) || 1;

        if (quantity > variant.stock) {
            showToast('Stok tidak mencukupi! Stok tersedia: ' + variant.stock, 'error');
            return;
        }

        var productId = {{ $product->id }};

        checkLoginStatus().then(function(isLoggedIn) {
            if (!isLoggedIn) {
                window._pendingProductId = productId;
                window._pendingVariantId = variant.id;
                window._pendingQuantity = quantity;
                openLoginPopup('add_to_cart', function() {
                    if (window._pendingProductId) {
                        window.addToCartFromShowPage(
                            window._pendingProductId,
                            window._pendingVariantId,
                            window._pendingQuantity
                        );
                        window._pendingProductId = null;
                        window._pendingVariantId = null;
                        window._pendingQuantity = null;
                        closeVariantPopup();
                    }
                });
                return;
            }

            window.addToCartFromShowPage(productId, variant.id, quantity);
            closeVariantPopup();
        });
    });

    // ============================================ */
    // POPUP BUY NOW - LANGSUNG KE CHECKOUT */
    // ============================================ */

    document.getElementById('popup-buy-now')?.addEventListener('click', function() {
        if (this.disabled) return;

        var variant = popupCurrentVariant;
        if (!variant) return;

        var quantity = parseInt(popupQtyInput.value) || 1;

        if (quantity > variant.stock) {
            showToast('Stok tidak mencukupi! Stok tersedia: ' + variant.stock, 'error');
            return;
        }

        var csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        this.disabled = true;
        this.textContent = '⏳ Memproses...';

        fetch('{{ route("customer.buy-now") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: {{ $product->id }},
                variant_id: variant.id,
                quantity: quantity
            })
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                window.location.href = data.redirect || '{{ route("customer.checkout.index") }}';
            } else {
                showToast(data.message || 'Gagal memproses pesanan', 'error');
                document.getElementById('popup-buy-now').disabled = false;
                document.getElementById('popup-buy-now').textContent = 'Beli Sekarang';
            }
        })
        .catch(function() {
            showToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
            document.getElementById('popup-buy-now').disabled = false;
            document.getElementById('popup-buy-now').textContent = 'Beli Sekarang';
        });

        closeVariantPopup();
    });

    // ============================================ */
    // 🔥 FUNCTION UNTUK WISHLIST - GLOBAL SCOPE */
    // ============================================ */

    // 🔥 toggleWishlistDirect - DIBUAT GLOBAL
    window.toggleWishlistDirect = function(productId) {
        var csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        var btn = document.getElementById('wishlist-toggle-product');
        var mobileBtn = document.getElementById('mobile-wishlist-btn');
        var previousInWishlist = mobileBtn
            ? mobileBtn.classList.contains('active')
            : (btn ? btn.classList.contains('active') : false);
        var optimisticInWishlist = !previousInWishlist;

        if (btn) {
            btn.disabled = true;
            btn.style.opacity = '0.5';
        }

        if (mobileBtn) {
            mobileBtn.disabled = true;
            mobileBtn.style.opacity = '0.5';
        }

        // Update the icon immediately; the server response remains authoritative.
        updateAllWishlistButtons(productId, optimisticInWishlist);

        fetch(window.customerRoutes.wishlistAdd, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ product_id: productId })
        })
        .then(function(response) {
            if (response.status === 401) {
                updateAllWishlistButtons(productId, previousInWishlist);
                window._pendingProductId = productId;
                openLoginPopup('add_to_wishlist', function() {
                    if (window._pendingProductId) {
                        window.toggleWishlistDirect(window._pendingProductId);
                        window._pendingProductId = null;
                    }
                });
                throw new Error('Unauthorized');
            }
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                // 🔥 UPDATE SEMUA TOMBOL (DESKTOP & MOBILE)
                updateAllWishlistButtons(productId, data.in_wishlist);

                if (data.in_wishlist) {
                    showToast('❤️ ' + data.message, 'success');
                    
                    if (typeof loadWishlistPopup === 'function') {
                        setTimeout(function() {
                            loadWishlistPopup();
                            var popup = document.getElementById('wishlist-popup');
                            if (popup) {
                                popup.classList.add('active');
                                document.body.classList.add('popup-open');
                            }
                        }, 400);
                    }
                } else {
                    showToast('💔 ' + data.message, 'info');
                }

                // UPDATE WISHLIST COUNT DI NAVBAR
                var wishlistCount = document.getElementById('wishlist-count');
                if (wishlistCount) {
                    wishlistCount.textContent = data.count || 0;
                    wishlistCount.style.display = (data.count > 0) ? 'inline-flex' : 'none';
                }
                
                if (typeof window.updateNavbarWishlistCount === 'function') {
                    window.updateNavbarWishlistCount(data.count);
                }
                
            } else {
                updateAllWishlistButtons(productId, previousInWishlist);
                showToast(data.message || 'Gagal mengubah status wishlist', 'error');
            }
        })
        .catch(function(error) {
            updateAllWishlistButtons(productId, previousInWishlist);
            if (error.message !== 'Unauthorized') {
                console.error('Error:', error);
                showToast('Terjadi kesalahan', 'error');
            }
        })
        .finally(function() {
            if (btn) {
                btn.disabled = false;
                btn.style.opacity = '1';
            }
            if (mobileBtn) {
                mobileBtn.disabled = false;
                mobileBtn.style.opacity = '1';
            }
        });
    };

    // 🔥 FUNCTION UPDATE ALL WISHLIST BUTTONS - GLOBAL
    function updateAllWishlistButtons(productId, inWishlist) {
        // 1. Update semua tombol dengan class .add_to_wishlist_btn
        var allButtons = document.querySelectorAll('.add_to_wishlist_btn[data-product-id="' + productId + '"]');
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
        });
        
        // 2. Update tombol desktop utama
        var mainBtn = document.getElementById('wishlist-toggle-product');
        if (mainBtn) {
            if (inWishlist) {
                mainBtn.classList.add('active');
                mainBtn.innerHTML = '<iconify-icon icon="solar:heart-bold" style="color: #ef4444;"></iconify-icon>';
            } else {
                mainBtn.classList.remove('active');
                mainBtn.innerHTML = '<iconify-icon icon="solar:heart-linear"></iconify-icon>';
            }
        }

        // 3. Update tombol mobile wishlist
        var mobileBtn = document.getElementById('mobile-wishlist-btn');
        if (mobileBtn) {
            if (inWishlist) {
                mobileBtn.classList.add('active');
                mobileBtn.innerHTML = '<iconify-icon icon="solar:heart-bold" style="color: #ef4444;"></iconify-icon>';
            } else {
                mobileBtn.classList.remove('active');
                mobileBtn.innerHTML = '<iconify-icon icon="solar:heart-linear"></iconify-icon>';
            }
        }
    }

    // 🔥 FUNCTION CHECK WISHLIST STATUS - GLOBAL
    function checkWishlistStatus() {
        var productId = {{ $product->id }};
        var wishlistData = @json(session('wishlist', []));

        if (wishlistData[productId]) {
            // Desktop button
            var btn = document.getElementById('wishlist-toggle-product');
            if (btn) {
                btn.classList.add('active');
                btn.innerHTML = '❤️';
            }
            
            // 🔥 MOBILE BUTTON - DIPERBAIKI
            var mobileBtn = document.getElementById('mobile-wishlist-btn');
            if (mobileBtn) {
                mobileBtn.classList.add('active');
                mobileBtn.innerHTML = '<iconify-icon icon="solar:heart-bold" style="color: #ef4444;"></iconify-icon>';
            }
        } else {
            // Desktop button
            var btn = document.getElementById('wishlist-toggle-product');
            if (btn) {
                btn.classList.remove('active');
                btn.innerHTML = '🤍';
            }
            
            // 🔥 MOBILE BUTTON - DIPERBAIKI
            var mobileBtn = document.getElementById('mobile-wishlist-btn');
            if (mobileBtn) {
                mobileBtn.classList.remove('active');
                mobileBtn.innerHTML = '<iconify-icon icon="solar:heart-linear"></iconify-icon>';
            }
        }
    }

    // ============================================ */
    // 🔥 MOBILE - WISHLIST (LANGSUNG TAMBAH, TANPA POPUP) */
    // ============================================ */

    // 🔥 FUNCTION UNTUK WISHLIST MOBILE - LANGSUNG TANPA POPUP
    function handleMobileWishlist() {
        var productId = {{ $product->id }};
        
        checkLoginStatus().then(function(isLoggedIn) {
            if (!isLoggedIn) {
                window._pendingProductId = productId;
                openLoginPopup('add_to_wishlist', function() {
                    if (window._pendingProductId) {
                        window.toggleWishlistDirect(window._pendingProductId);
                        window._pendingProductId = null;
                    }
                });
                return;
            }
            
            // LANGSUNG TAMBAH KE WISHLIST - TANPA POPUP
            window.toggleWishlistDirect(productId);
        });
    }

    // ============================================ */
    // 🔥 MOBILE BUTTONS EVENT LISTENERS */
    // ============================================ */

    // 🔥 MOBILE - BELI SEKARANG (BUKA POPUP BUY NOW)
    document.getElementById('mobile-buy-now-btn')?.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        // Buka popup dengan action 'buy_now'
        openVariantPopup('buy_now');
    });

    // 🔥 MOBILE - TAMBAH KE KERANJANG (BUKA POPUP ADD TO CART)
    document.getElementById('mobile-add-to-cart-btn')?.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        // Buka popup dengan action 'add_to_cart'
        openVariantPopup('add_to_cart');
    });

    // 🔥 MOBILE - WISHLIST (LANGSUNG TAMBAH, TANPA POPUP) - MENGGUNAKAN handleMobileWishlist
    document.getElementById('mobile-wishlist-btn')?.addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    // 🔥 CEK APAKAH SUDAH DI WISHLIST
    var isActive = this.classList.contains('active');
    
    // LANGSUNG TAMBAH/HAPUS KE WISHLIST - TANPA POPUP
    handleMobileWishlist();
});

    // ============================================ */
    // OVERRIDE DESKTOP BUTTONS FOR MOBILE */
    // ============================================ */

    // Check if mobile (max-width: 768px)
    function isMobile() {
        return window.innerWidth <= 768;
    }

    // 🔥 DESKTOP - BELI SEKARANG (DI OVERRIDE UNTUK MOBILE)
    document.querySelector('.btn-buy-now')?.addEventListener('click', function(e) {
        if (isMobile()) {
            e.preventDefault();
            e.stopPropagation();
            
            // Cek apakah sudah ada varian yang dipilih di desktop
            var variantId = document.getElementById('selected-variant')?.value;
            if (variantId) {
                // Jika sudah ada varian, langsung beli
                window.buyNow();
                return;
            }
            
            // Jika belum ada varian, buka popup buy_now
            openVariantPopup('buy_now');
        }
    });

    // 🔥 DESKTOP - TAMBAH KE KERANJANG (DI OVERRIDE UNTUK MOBILE)
    document.querySelector('.btn-add-to-cart')?.addEventListener('click', function(e) {
        if (isMobile()) {
            e.preventDefault();
            e.stopPropagation();
            
            // Cek apakah sudah ada varian yang dipilih di desktop
            var variantId = document.getElementById('selected-variant')?.value;
            if (variantId) {
                // Jika sudah ada varian, submit form
                var form = document.getElementById('add-to-cart-form');
                if (form) {
                    form.submit();
                }
                return;
            }
            
            // Jika belum ada varian, buka popup add_to_cart
            openVariantPopup('add_to_cart');
        }
    });

    // 🔥 DESKTOP - WISHLIST (DI OVERRIDE UNTUK MOBILE - LANGSUNG TAMBAH)
    document.getElementById('wishlist-toggle-product')?.addEventListener('click', function(e) {
        if (isMobile()) {
            e.preventDefault();
            e.stopPropagation();
            
            var productId = {{ $product->id }};
            
            checkLoginStatus().then(function(isLoggedIn) {
                if (!isLoggedIn) {
                    window._pendingProductId = productId;
                    openLoginPopup('add_to_wishlist', function() {
                        if (window._pendingProductId) {
                            window.toggleWishlistDirect(window._pendingProductId);
                            window._pendingProductId = null;
                        }
                    });
                    return;
                }
                
                // LANGSUNG TAMBAH KE WISHLIST - TANPA POPUP
                window.toggleWishlistDirect(productId);
            });
        }
    });

    // ============================================ */
    // CLOSE POPUP ON OVERLAY CLICK */
    // ============================================ */

    variantPopup?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeVariantPopup();
        }
    });

    // ============================================ */
    // KEYBOARD ESCAPE */
    // ============================================ */

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeVariantPopup();
        }
    });

    // ============================================ */
    // 🔥 CHECK LOGIN STATUS - GLOBAL */
    // ============================================ */

    window.checkLoginStatus = function() {
        return new Promise(function(resolve) {
            var loggedIn = document.querySelector('meta[name="customer-logged-in"]')?.content === 'true';
            resolve(loggedIn);
        });
    };
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const timerElement = document.getElementById('flash-sale-timer');
        
        if (!timerElement) return;
        
        const endDate = new Date(timerElement.dataset.end);
        
        function updateTimer() {
            const now = new Date().getTime();
            const distance = endDate.getTime() - now;
            
            if (distance < 0) {
                // Timer expired
                timerElement.classList.add('expired');
                document.getElementById('timer-group').innerHTML = `
                    <span class="timer-expired-text">⏰ Flash Sale Telah Berakhir</span>
                `;
                return;
            }
            
            // Hitung waktu
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            // Update DOM
            document.getElementById('timer-days').textContent = String(days).padStart(2, '0');
            document.getElementById('timer-hours').textContent = String(hours).padStart(2, '0');
            document.getElementById('timer-minutes').textContent = String(minutes).padStart(2, '0');
            document.getElementById('timer-seconds').textContent = String(seconds).padStart(2, '0');
        }
        
        // Update setiap detik
        updateTimer();
        setInterval(updateTimer, 1000);
    });
</script>

{{-- ============================================ --}}
{{-- JAVASCRIPT --}}
{{-- ============================================ --}}
<script>
document.addEventListener('DOMContentLoaded', function() {

    const productVariants = @json($variantData ?? []);
    const productImages = @json($product->images);
    const defaultVariantId = null;

    const selectedVariantInput = document.getElementById('selected-variant');
    const variantValuesInput = document.getElementById('selected-variant-values');
    const stockDisplay = document.getElementById('stock-display');
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    const buyNowBtn = document.getElementById('buy-now-btn');
    const qtyInput = document.getElementById('qty-input');
    const mainImage = document.getElementById('main-image');
    const priceContainer = document.getElementById('price-display-container');

    let selectedValues = {};
    let currentVariantId = null;
    let isVariantSelected = false;

    // 🔥 DATA HARGA DEFAULT (RANGE)
    const defaultDisplayPrice = @json($defaultDisplayPrice);
    const defaultOriginalPrice = @json($defaultOriginalPrice);
    const defaultDiscountBadge = @json($defaultDiscountBadge);
    const hasAnyDiscount = {{ $hasAnyDiscount ? 'true' : 'false' }};
    const maxDiscountPercent = {{ $maxDiscountPercent ?? 0 }};
    const defaultMinEffective = {{ $minEffective ?? 0 }};
    const defaultMaxEffective = {{ $maxEffective ?? 0 }};
    const defaultMinOriginal = {{ $minOriginalPrice ?? 0 }};
    const defaultMaxOriginal = {{ $maxOriginalPrice ?? 0 }};

    let previousSelectedValues = {};
    let previousVariantId = null;
    let previousIsVariantSelected = false;

    function initThumbnailScroll() {
        const thumbnailContainer = document.getElementById('thumbnail-container');
        const scrollUpBtn = document.getElementById('thumbnail-scroll-up');
        const scrollDownBtn = document.getElementById('thumbnail-scroll-down');

        console.log('🔄 initThumbnailScroll called');
        console.log('📦 thumbnailContainer:', thumbnailContainer);
        console.log('⬆️ scrollUpBtn:', scrollUpBtn);
        console.log('⬇️ scrollDownBtn:', scrollDownBtn);

        if (!thumbnailContainer) {
            console.log('❌ thumbnailContainer not found');
            return;
        }

        // Fungsi untuk mengecek apakah perlu scroll buttons
        function checkScrollButtons() {
            if (!thumbnailContainer) return;

            const scrollHeight = thumbnailContainer.scrollHeight;
            const clientHeight = thumbnailContainer.clientHeight;
            const scrollTop = thumbnailContainer.scrollTop;

            console.log('📊 Scroll check:', { scrollHeight, clientHeight, scrollTop });

            // Sembunyikan jika konten tidak lebih tinggi dari container
            if (scrollHeight <= clientHeight + 2) {
                if (scrollUpBtn) scrollUpBtn.classList.add('hidden');
                if (scrollDownBtn) scrollDownBtn.classList.add('hidden');
                return;
            }

            // Tampilkan/sembunyikan tombol berdasarkan posisi scroll
            if (scrollUpBtn) {
                if (scrollTop <= 2) {
                    scrollUpBtn.classList.add('hidden');
                } else {
                    scrollUpBtn.classList.remove('hidden');
                }
            }

            if (scrollDownBtn) {
                if (scrollTop + clientHeight >= scrollHeight - 2) {
                    scrollDownBtn.classList.add('hidden');
                } else {
                    scrollDownBtn.classList.remove('hidden');
                }
            }
        }

        // Scroll up
        if (scrollUpBtn) {
            // Hapus event listener lama jika ada
            scrollUpBtn.removeEventListener('click', scrollUpBtn._clickHandler);
            
            scrollUpBtn._clickHandler = function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('⬆️ Scroll up clicked');
                thumbnailContainer.scrollBy({
                    top: -(thumbnailContainer.clientHeight * 0.8),
                    behavior: 'smooth'
                });
            };
            scrollUpBtn.addEventListener('click', scrollUpBtn._clickHandler);
        }

        // Scroll down
        if (scrollDownBtn) {
            // Hapus event listener lama jika ada
            scrollDownBtn.removeEventListener('click', scrollDownBtn._clickHandler);
            
            scrollDownBtn._clickHandler = function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('⬇️ Scroll down clicked');
                thumbnailContainer.scrollBy({
                    top: thumbnailContainer.clientHeight * 0.8,
                    behavior: 'smooth'
                });
            };
            scrollDownBtn.addEventListener('click', scrollDownBtn._clickHandler);
        }

        // Update buttons on scroll
        thumbnailContainer.removeEventListener('scroll', thumbnailContainer._scrollHandler);
        thumbnailContainer._scrollHandler = function() {
            checkScrollButtons();
        };
        thumbnailContainer.addEventListener('scroll', thumbnailContainer._scrollHandler);

        // Update buttons on window resize
        window.removeEventListener('resize', window._thumbnailResizeHandler);
        window._thumbnailResizeHandler = function() {
            checkScrollButtons();
        };
        window.addEventListener('resize', window._thumbnailResizeHandler);

        // Initial check setelah render
        setTimeout(function() {
            checkScrollButtons();
        }, 500);

        // Juga check ketika thumbnail container berubah (misal setelah image load)
        if (window.ResizeObserver) {
            if (thumbnailContainer._resizeObserver) {
                thumbnailContainer._resizeObserver.disconnect();
            }
            thumbnailContainer._resizeObserver = new ResizeObserver(function() {
                checkScrollButtons();
            });
            thumbnailContainer._resizeObserver.observe(thumbnailContainer);
        }

        // Check when images load
        const images = thumbnailContainer.querySelectorAll('img');
        let imagesLoaded = 0;
        if (images.length > 0) {
            images.forEach(function(img) {
                if (img.complete) {
                    imagesLoaded++;
                } else {
                    img.addEventListener('load', function() {
                        imagesLoaded++;
                        if (imagesLoaded === images.length) {
                            setTimeout(checkScrollButtons, 200);
                        }
                    });
                }
            });
            if (imagesLoaded === images.length) {
                setTimeout(checkScrollButtons, 300);
            }
        }

        // Simpan fungsi untuk dipanggil nanti
        window.checkThumbnailScroll = checkScrollButtons;
    }

    // ============================================
    // FUNGSI RESET KE HARGA DEFAULT
    // ============================================
    function resetToDefaultPrice() {
        if (!priceContainer) return;

        console.log('🔄 Resetting to default price');

        if (hasAnyDiscount) {
            priceContainer.innerHTML = `
                <div class="product_price_box" id="default-price-box">
                    <span class="price-current discounted" id="display-price">
                        ${defaultDisplayPrice}
                    </span>
                    <span class="price-original" id="original-price-display">
                        ${defaultOriginalPrice}
                    </span>
                    <span class="discount-badge">${defaultDiscountBadge}</span>
                </div>
            `;
        } else {
            priceContainer.innerHTML = `
                <span class="price-current" id="display-price">
                    ${defaultDisplayPrice}
                </span>
            `;
        }

        // Reset stock display ke total stok
        const totalStock = productVariants.reduce(function(sum, v) {
            return sum + v.stock;
        }, 0);
        
        stockDisplay.textContent = totalStock > 0 ? 'Stok: ' + totalStock : 'Stok Habis';
        stockDisplay.className = 'product_stock_status ' + (totalStock > 0 ? 'in-stock' : 'out-of-stock');

        // Disable tombol
        addToCartBtn.disabled = true;
        addToCartBtn.textContent = 'Pilih Varian';
        buyNowBtn.disabled = true;
        buyNowBtn.textContent = 'Pilih Varian';

        // Reset selected variant
        selectedVariantInput.value = '';
        currentVariantId = null;
        isVariantSelected = false;
        
        // Reset qty max ke total stok
        qtyInput.max = totalStock > 0 ? totalStock : 1;
        qtyInput.value = 1;
        
        // 🔥 RESET SEMUA PILIHAN VARIAN
        document.querySelectorAll('.variant-option').forEach(function(btn) {
            btn.classList.remove('active');
        });
        selectedValues = {};
        variantValuesInput.value = '';
        
        // 🔥 UPDATE AVAILABILITAS VARIAN - AKTIFKAN SEMUA YANG MEMILIKI STOK
        updateAvailableVariants();
    }

    function saveCurrentState() {
        previousSelectedValues = Object.assign({}, selectedValues);
        previousVariantId = currentVariantId;
        previousIsVariantSelected = isVariantSelected;
    }

    function restorePreviousState() {
        if (previousIsVariantSelected && previousVariantId) {
            
            // Restore selected values
            selectedValues = Object.assign({}, previousSelectedValues);
            
            // Restore active classes
            document.querySelectorAll('.variant-option').forEach(function(btn) {
                const valueId = parseInt(btn.dataset.valueId);
                const optionId = btn.dataset.optionId;
                
                if (previousSelectedValues[optionId] === valueId) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
            
            // Cari varian yang cocok
            const variant = findVariantByValues(selectedValues, false);
            if (variant) {
                selectVariant(variant, true); // true = skip image update
            }
            
            variantValuesInput.value = JSON.stringify(Object.values(selectedValues));
            updateAvailableVariants();
        } else {
            // Jika tidak ada state sebelumnya, reset ke default
            resetToDefaultPrice();
        }
    }

    // ============================================
    // FUNGSI BUKA POPUP
    // ============================================
    function openCartPopup() {
        if (typeof loadCartPopup === 'function') {
            loadCartPopup();
            const popup = document.getElementById('cart-popup');
            if (popup) {
                popup.classList.add('active');
                document.body.classList.add('popup-open');
            }
        }
    }

    function openWishlistPopup() {
        if (typeof loadWishlistPopup === 'function') {
            loadWishlistPopup();
            const popup = document.getElementById('wishlist-popup');
            if (popup) {
                popup.classList.add('active');
                document.body.classList.add('popup-open');
            }
        }
    }

    window.openCartPopup = openCartPopup;
    window.openWishlistPopup = openWishlistPopup;

    // ============================================
    // DETEKSI OPSI UKURAN
    // ============================================
    function detectSizeOption() {
        document.querySelectorAll('.variant-options[data-option-id]').forEach(function(group) {
            const optionLabel = group.closest('.variant-section')?.querySelector('.variant-label');
            if (optionLabel) {
                const labelText = optionLabel.textContent.toLowerCase();
                if (labelText === 'ukuran' || labelText === 'size') {
                    group.dataset.isSize = 'true';
                }
            }
        });
    }
    detectSizeOption();

    // ============================================
    // FIND VARIANT BY VALUES
    // ============================================
    function findVariantByValues(values) {
        const selectedValueIds = Object.values(values).map(Number).sort();

        return productVariants.find(function(variant) {
            const variantValueIds = variant.values.map(Number).sort();
            return JSON.stringify(variantValueIds) === JSON.stringify(selectedValueIds);
        });
    }

    function findVariantForSelectedOption(optionId, valueId) {
        // 🔥 BUAT CANDIDATE VALUES DARI SELECTED VALUES + PILIHAN BARU
        const candidateValues = Object.assign({}, selectedValues, {
            [optionId]: parseInt(valueId)
        });

        // 🔥 CEK APAKAH ADA VARIAN YANG COCOK (TERMASUK YANG STOK 0)
        const exactMatch = findVariantByValues(candidateValues, false);
        if (exactMatch) {
            // Jika ada varian yang cocok (walaupun stok 0), return
            return exactMatch;
        }

        // 🔥 CARI VARIAN YANG MEMILIKI VALUE INI (TANPA HARUS SEMUA OPTION MATCH)
        const targetValueId = parseInt(valueId);
        
        // Cari varian yang memiliki value ini (stok > 0)
        const variantsWithValue = productVariants.filter(function(variant) {
            return variant.values.includes(targetValueId) && variant.stock > 0;
        });

        if (variantsWithValue.length === 0) {
            // Jika tidak ada yang stok > 0, cari yang stok 0 (untuk ditampilkan)
            const anyVariant = productVariants.find(function(variant) {
                return variant.values.includes(targetValueId);
            });
            return anyVariant || null;
        }

        // 🔥 PRIORITASKAN VARIAN YANG COCOK DENGAN SEMUA SELECTED VALUES (STOK > 0)
        const selectedIds = Object.values(candidateValues).map(Number);

        return variantsWithValue.find(function(variant) {
            const variantValueIds = variant.values.map(Number);
            return selectedIds.every(function(id) {
                return variantValueIds.includes(id);
            });
        }) || variantsWithValue[0]; // Fallback ke yang pertama
    }

    function findVariantById(id) {
        return productVariants.find(function(v) { return v.id == id; });
    }

    function getOptionNameByValueId(valueId) {
        const btn = document.querySelector(`.variant-option[data-value-id="${valueId}"]`);
        if (btn) {
            return (btn.dataset.optionName || '').trim();
        }
        return '';
    }

    function getValueImageByValueId(valueId) {
        const btn = document.querySelector(`.variant-option[data-value-id="${valueId}"]`);
        if (btn) {
            return btn.dataset.image || '';
        }
        return '';
    }

    // ============================================
    // CHANGE MAIN IMAGE
    // ============================================
    window.changeMainImage = function(imageUrl, element, isOptionImage = false) {
        if (!imageUrl || !mainImage) return;

        console.log('🖼️ changeMainImage called:', { imageUrl, isOptionImage });

        mainImage.src = imageUrl;
        mainImage.classList.remove('fade-in');
        void mainImage.offsetWidth;
        mainImage.classList.add('fade-in');

        // 🔥 RESET ZOOM SAAT GANTI GAMBAR
        const container = document.getElementById('main-image-container');
        const magnifier = document.getElementById('magnifier-glass');
        if (container) {
            container.classList.remove('zoomed');
        }
        if (magnifier) {
            magnifier.classList.remove('active');
            magnifier.style.backgroundImage = '';
        }

        document.querySelectorAll('.image-thumb').forEach(function(thumb) {
            thumb.classList.remove('active');
        });

        if (element) {
            element.classList.add('active');
        } else {
            document.querySelectorAll('.image-thumb').forEach(function(thumb) {
                if (thumb.dataset.image === imageUrl) {
                    thumb.classList.add('active');
                }
            });
        }

        // 🔥 LOGIKA UTAMA:
        // - Jika gambar dari option values (isOptionImage = true) -> PILIH VARIAN
        // - Jika gambar dari product images (isOptionImage = false) -> JANGAN RESET, PERTAHANKAN STATE
        if (isOptionImage) {
            // Gambar dari option value - pilih varian yang sesuai
            const optionValueId = element ? element.dataset.optionValueId : null;
            console.log('🔄 Option image clicked, optionValueId:', optionValueId);
            if (optionValueId) {
                // Hapus semua active class terlebih dahulu
                document.querySelectorAll('.variant-option').forEach(function(btn) {
                    btn.classList.remove('active');
                });
                selectVariantByOptionValueId(parseInt(optionValueId));
            }
        } else {
            // 🔥 GAMBAR DARI PRODUCT IMAGES - JANGAN RESET, PERTAHANKAN STATE
            console.log('🔄 Product image clicked - KEEPING current selection');
            // Tidak melakukan reset, tetap pertahankan pilihan varian saat ini
            // Hanya update gambar thumbnail aktif
        }
    };

    // ============================================
    // PILIH VARIAN BERDASARKAN OPTION VALUE ID
    // ============================================
    function selectVariantByOptionValueId(optionValueId) {
        console.log('🔍 Selecting variant by option value ID:', optionValueId);
        
        // Cari varian yang memiliki value ini
        let foundVariant = null;
        for (let i = 0; i < productVariants.length; i++) {
            if (productVariants[i].values.includes(parseInt(optionValueId))) {
                // Prioritaskan yang stok > 0
                if (productVariants[i].stock > 0) {
                    foundVariant = productVariants[i];
                    break;
                } else if (!foundVariant) {
                    // Simpan yang stok 0 sebagai fallback
                    foundVariant = productVariants[i];
                }
            }
        }

        if (foundVariant) {
            console.log('✅ Found variant:', foundVariant);
            
            let variantValues = {};
            let valueIds = foundVariant.values;

            // Set active class untuk semua opsi yang cocok
            document.querySelectorAll('.variant-options[data-option-id]').forEach(function(group) {
                let optionId = group.dataset.optionId;
                let optionButtons = group.querySelectorAll('.variant-option');
                let hasActiveInGroup = false;
                
                optionButtons.forEach(function(btn) {
                    let valId = parseInt(btn.dataset.valueId);
                    if (valueIds.includes(valId)) {
                        variantValues[optionId] = valId;
                        btn.classList.add('active');
                        hasActiveInGroup = true;
                    } else {
                        btn.classList.remove('active');
                    }
                });
                
                // Jika tidak ada yang aktif di grup ini, pilih yang pertama tersedia
                if (!hasActiveInGroup) {
                    const firstAvailable = group.querySelector('.variant-option:not([disabled])');
                    if (firstAvailable) {
                        firstAvailable.classList.add('active');
                        variantValues[optionId] = parseInt(firstAvailable.dataset.valueId);
                    }
                }
            });

            selectedValues = variantValues;
            variantValuesInput.value = JSON.stringify(Object.values(variantValues));
            selectVariant(foundVariant);
            
            // 🔥 UPDATE GAMBAR - HANYA JIKA OPTION VALUE ADALAH WARNA
            // Cek apakah option value ini adalah warna
            const optionName = getOptionNameByValueId(parseInt(optionValueId));
            var isColorOption = false;
            var colorKeywords = ['warna', 'color', 'colour'];
            var optionNameLower = (optionName || '').toLowerCase().trim();
            
            for (var c = 0; c < colorKeywords.length; c++) {
                if (optionNameLower === colorKeywords[c]) {
                    isColorOption = true;
                    break;
                }
            }
            
            if (isColorOption) {
                updateVariantImage(foundVariant);
            }
            
            updateAvailableVariants();
            isVariantSelected = true;
        } else {
            console.log('❌ No variant found for option value ID:', optionValueId);
            updateAvailableVariants();
        }
    }


    // ============================================
    // HANDLE THUMBNAIL CLICK
    // ============================================
    window.handleThumbnailClick = function(imageUrl, optionValueId, element) {
        // 🔥 TENTUKAN APAKAH INI GAMBAR DARI OPTION VALUE ATAU PRODUCT IMAGES
        const isOptionImage = optionValueId !== null && optionValueId !== undefined && optionValueId !== 'null' && optionValueId !== '';
        
        console.log('🖼️ Thumbnail clicked:', { imageUrl, optionValueId, isOptionImage });
        
        // Ganti gambar utama
        changeMainImage(imageUrl, element, isOptionImage);
        
        // 🔥 JIKA GAMBAR DARI OPTION VALUE, PILIH VARIAN YANG SESUAI
        if (isOptionImage && optionValueId) {
            // Hapus semua active class terlebih dahulu
            document.querySelectorAll('.variant-option').forEach(function(btn) {
                btn.classList.remove('active');
            });
            selectVariantByOptionValueId(parseInt(optionValueId));
        }
        // 🔥 JIKA GAMBAR DARI PRODUCT IMAGES, TIDAK ADA TINDAKAN - PERTAHANKAN STATE
    };

    // ============================================
    // SELECT VARIANT
    // ============================================
    function selectVariant(variant, skipImageUpdate = false) {
        if (!variant) return;

        currentVariantId = variant.id;
        selectedVariantInput.value = variant.id;
        isVariantSelected = true;

        // 🔥 GUNakan effective_price dan discount_percent dari variant (SUDAH INCLUDE DISKON PRODUK)
        var price = variant.effective_price ?? variant.price;
        var originalPrice = variant.price;
        var hasDiscount = variant.discount_percent > 0;
        var discountPercent = variant.discount_percent || 0;
        var isOutOfStock = variant.stock <= 0;

        // 🔥 UPDATE HARGA DENGAN CORET
        if (priceContainer) {
            if (hasDiscount) {
                var discountBadge = Math.round(discountPercent) + '%';
                
                // 🔥 TAMBAHKAN INFO DISKON PRODUK JIKA ADA
                var productDiscountInfo = '';
                if ({{ $hasProductDiscount ? 'true' : 'false' }} && {{ $productDiscountPercent ?? 0 }} > 0) {
                    productDiscountInfo = '<span class="product-discount-info" style="font-size:0.6vw;color:#16a34a;display:block;margin-top:0.2vw;">' + Math.round({{ $productDiscountPercent ?? 0 }}) + '%</span>';
                }
                
                priceContainer.innerHTML = `
                    <div class="product_price_box">
                        <span class="price-current discounted" id="display-price">
                            Rp ${new Intl.NumberFormat('id-ID').format(price)}
                            ${isOutOfStock ? '<span class="text-red-500 text-xs ml-2 font-normal">(Stok Habis)</span>' : ''}
                        </span>
                        <span class="price-original" id="original-price-display">
                            Rp ${new Intl.NumberFormat('id-ID').format(originalPrice)}
                        </span>
                        <span class="discount-badge">${discountBadge}</span>
                        ${productDiscountInfo}
                    </div>
                `;
            } else {
                priceContainer.innerHTML = `
                    <span class="price-current" id="display-price">
                        Rp ${new Intl.NumberFormat('id-ID').format(price)}
                        ${isOutOfStock ? '<span class="text-red-500 text-xs ml-2 font-normal">(Stok Habis)</span>' : ''}
                    </span>
                `;
            }
        }

        stockDisplay.textContent = variant.stock > 0 ? 'Stok: ' + variant.stock : 'Stok Habis';
        stockDisplay.className = 'product_stock_status ' + (variant.stock > 0 ? 'in-stock' : 'out-of-stock');

        qtyInput.max = variant.stock > 0 ? variant.stock : 1;

        if (variant.stock > 0) {
            addToCartBtn.disabled = false;
            addToCartBtn.textContent = 'Tambah ke Keranjang';
            buyNowBtn.disabled = false;
            buyNowBtn.textContent = 'Beli Sekarang';
        } else {
            addToCartBtn.disabled = true;
            addToCartBtn.textContent = 'Stok Habis';
            buyNowBtn.disabled = true;
            buyNowBtn.textContent = 'Stok Habis';
        }

        updateVariantActiveState(variant);
        
        // 🔥 UPDATE GAMBAR HANYA JIKA TIDAK SKIP
        if (!skipImageUpdate) {
            updateVariantImage(variant);
        }
    }

    function isColorOptionName(optionName) {
        if (!optionName) return false;
        var colorKeywords = ['warna', 'color', 'colour'];
        var optionNameLower = optionName.toLowerCase().trim();
        
        for (var c = 0; c < colorKeywords.length; c++) {
            if (optionNameLower === colorKeywords[c]) {
                return true;
            }
        }
        return false;
    }

    function isSizeOptionName(optionName) {
        if (!optionName) return false;
        var sizeKeywords = ['ukuran', 'size'];
        var optionNameLower = optionName.toLowerCase().trim();
        
        for (var c = 0; c < sizeKeywords.length; c++) {
            if (optionNameLower === sizeKeywords[c]) {
                return true;
            }
        }
        return false;
    }

    function updateVariantActiveState(variant) {
        if (!variant) return;

        const variantValueIds = variant.values.map(Number);

        document.querySelectorAll('.variant-option').forEach(function(btn) {
            const valueId = parseInt(btn.dataset.valueId);
            btn.classList.remove('active');

            if (variantValueIds.includes(valueId)) {
                btn.classList.add('active');
            }
        });
    }

    function updateAvailableVariants() {
        const totalOptions = document.querySelectorAll('.variant-options').length;
        const hasSelectedValues = Object.keys(selectedValues).length > 0;
        
        console.log('🔄 Updating available variants, selectedValues:', selectedValues);
        console.log('Total options:', totalOptions, 'Has selected:', hasSelectedValues);
        
        document.querySelectorAll('.variant-options').forEach(function(group) {
            const optionId = group.dataset.optionId;
            const buttons = group.querySelectorAll('.variant-option');
            
            buttons.forEach(function(button) {
                const valueId = parseInt(button.dataset.valueId);
                
                // 🔥 CEK APAKAH OPSI INI MEMILIKI STOK DI SETIDAKNYA SATU VARIAN
                let hasStockForValue = false;
                let hasAnyVariant = false;
                
                for (var i = 0; i < productVariants.length; i++) {
                    var variant = productVariants[i];
                    if (variant.values.includes(valueId)) {
                        hasAnyVariant = true;
                        if (variant.stock > 0) {
                            hasStockForValue = true;
                            break;
                        }
                    }
                }
                
                // 🔥 JIKA OPSI INI TIDAK ADA DI VARIAN MANAPUN, DISABLE
                if (!hasAnyVariant) {
                    button.disabled = true;
                    button.classList.add('disabled');
                    return;
                }
                
                // 🔥 JIKA BELUM ADA VARIAN YANG DIPILIH, AKTIFKAN SEMUA YANG ADA STOK
                if (!hasSelectedValues) {
                    if (hasStockForValue) {
                        button.disabled = false;
                        button.classList.remove('disabled');
                    } else {
                        button.disabled = true;
                        button.classList.add('disabled');
                    }
                    return;
                }
                
                // 🔥 JIKA SUDAH ADA VARIAN YANG DIPILIH
                // Cek apakah kombinasi dengan nilai yang dipilih saat ini tersedia
                var tempValues = Object.assign({}, selectedValues);
                tempValues[optionId] = valueId;
                
                var variant = findVariantByValues(tempValues, false);
                var isAvailable = variant && variant.stock > 0;
                
                if (!isAvailable && variant) {
                    // Jika varian ada tapi stok 0, tetap disable
                    button.disabled = true;
                    button.classList.add('disabled');
                } else if (!isAvailable) {
                    // Jika tidak ada varian sama sekali
                    button.disabled = true;
                    button.classList.add('disabled');
                } else {
                    button.disabled = false;
                    button.classList.remove('disabled');
                }
            });
            
            // 🔥 PASTIKAN SETIDAKNYA SATU OPSI AKTIF DI SETIAP GRUP (JIKA ADA YANG AKTIF)
            const activeInGroup = group.querySelector('.variant-option.active:not([disabled])');
            const anyAvailable = group.querySelector('.variant-option:not([disabled])');
            
            // Jika tidak ada yang aktif dan ada yang tersedia, pilih yang pertama
            if (!activeInGroup && anyAvailable && hasSelectedValues) {
                // Cek apakah option ini sudah memiliki nilai yang dipilih di selectedValues
                if (selectedValues[optionId]) {
                    // Cari tombol dengan value yang sesuai
                    const matchingBtn = group.querySelector(`.variant-option[data-value-id="${selectedValues[optionId]}"]`);
                    if (matchingBtn && !matchingBtn.disabled) {
                        matchingBtn.classList.add('active');
                    } else {
                        // Jika yang dipilih tidak tersedia, pilih yang pertama tersedia
                        anyAvailable.classList.add('active');
                        selectedValues[optionId] = parseInt(anyAvailable.dataset.valueId);
                    }
                } else {
                    // Jika belum ada nilai di selectedValues, pilih yang pertama tersedia
                    anyAvailable.classList.add('active');
                    selectedValues[optionId] = parseInt(anyAvailable.dataset.valueId);
                }
            }
        });
    }

    function findVariantByValues(values, checkStock = true) {
        const selectedValueIds = Object.values(values).map(Number).sort();

        return productVariants.find(function(variant) {
            const variantValueIds = variant.values.map(Number).sort();
            const matches = JSON.stringify(variantValueIds) === JSON.stringify(selectedValueIds);
            if (checkStock) {
                return matches && variant.stock > 0;
            }
            return matches;
        });
    }

    function findColorImage(variant) {
        if (!variant) return null;

        const variantValueIds = variant.values.map(Number);

        for (let i = 0; i < variantValueIds.length; i++) {
            const valueId = variantValueIds[i];
            const optionName = getOptionNameByValueId(valueId);
            
            if (optionName.toLowerCase() === 'warna' || optionName.toLowerCase() === 'color') {
                const thumb = document.querySelector(`.image-thumb[data-option-value-id="${valueId}"]`);
                if (thumb) {
                    return thumb.dataset.image;
                }
            }
        }

        return null;
    }

    function updateVariantImage(variant) {
        if (!variant) return;

        const variantValueIds = variant.values.map(Number);
        let colorImage = null;

        for (let i = 0; i < variantValueIds.length; i++) {
            const valueId = variantValueIds[i];
            const optionName = getOptionNameByValueId(valueId);
            const valueImage = getValueImageByValueId(valueId);

            if ((optionName || '').toLowerCase() === 'warna' || (optionName || '').toLowerCase() === 'color') {
                if (valueImage) {
                    colorImage = valueImage;
                    break;
                }
            }

            const thumb = document.querySelector(`.image-thumb[data-option-value-id="${valueId}"]`);
            if (thumb && thumb.dataset.image) {
                colorImage = thumb.dataset.image;
            }
        }

        if (colorImage) {
            changeMainImage(colorImage, null, true);
            return;
        }

        if (variant.image) {
            changeMainImage(variant.image, null, true);
            return;
        }

        const firstImage = productImages.length > 0 ? 
            '{{ $product->images->first() ? Storage::url($product->images->first()->image) : '' }}' : null;
        if (firstImage) {
            changeMainImage(firstImage, null, false);
        }
    }

    // ============================================
    // SHOW TOAST
    // ============================================
    function showToast(message, type = 'info') {
        if (typeof window.showToast === 'function') {
            window.showToast(message, type);
            return;
        }

        const toast = document.createElement('div');
        const colors = {
            success: '#10b981',
            error: '#ef4444',
            warning: '#f59e0b',
            info: '#3b82f6'
        };
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 20px;
            background: ${colors[type] || colors.info};
            color: white;
            border-radius: 8px;
            font-size: 14px;
            z-index: 9999;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            animation: slideInRight 0.3s ease;
            max-width: 400px;
        `;
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(function() {
            toast.style.animation = 'slideOutRight 0.3s ease forwards';
            setTimeout(function() {
                toast.remove();
            }, 300);
        }, 3000);

        if (!document.getElementById('toast-styles')) {
            const style = document.createElement('style');
            style.id = 'toast-styles';
            style.textContent = `
                @keyframes slideInRight {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes slideOutRight {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(100%); opacity: 0; }
                }
            `;
            document.head.appendChild(style);
        }
    }

    // ============================================
    // BUY NOW
    // ============================================
    window.buyNow = function() {
        const variantId = selectedVariantInput.value;
        const quantity = parseInt(qtyInput.value) || 1;

        if (!variantId) {
            alert('Pilih varian produk terlebih dahulu!');
            return;
        }

        const variant = productVariants.find(function(v) { return v.id == variantId; });
        if (variant && quantity > variant.stock) {
            alert('Stok tidak mencukupi! Stok tersedia: ' + variant.stock);
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const btn = document.getElementById('buy-now-btn');

        btn.disabled = true;
        btn.textContent = '⏳ Memproses...';

        fetch('{{ route("customer.buy-now") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: {{ $product->id }},
                variant_id: variantId,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect || '{{ route("customer.checkout.index") }}';
            } else {
                alert(data.message || 'Gagal memproses pesanan');
                btn.disabled = false;
                btn.textContent = 'Beli Sekarang';
            }
        })
        .catch(() => {
            alert('Terjadi kesalahan. Silakan coba lagi.');
            btn.disabled = false;
            btn.textContent = 'Beli Sekarang';
        });
    };

    // ============================================
    // TOGGLE WISHLIST
    // ============================================
    window.toggleWishlist = function(productId) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const btn = document.getElementById('wishlist-toggle-product');

        // 🔥 CEK LOGIN STATUS
        checkLoginStatus().then(isLoggedIn => {
            if (!isLoggedIn) {
                window._pendingProductId = productId;
                openLoginPopup('add_to_wishlist', function() {
                    if (window._pendingProductId) {
                        toggleWishlistDirect(window._pendingProductId);
                        window._pendingProductId = null;
                    }
                });
                return;
            }

            toggleWishlistDirect(productId);
        });
    };

    function toggleWishlistDirect(productId) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const btn = document.getElementById('wishlist-toggle-product');

        if (btn) {
            btn.disabled = true;
            btn.style.opacity = '0.5';
        }

        fetch(window.customerRoutes.wishlistAdd, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ product_id: productId })
        })
        .then(response => {
            if (response.status === 401) {
                window._pendingProductId = productId;
                openLoginPopup('add_to_wishlist', function() {
                    if (window._pendingProductId) {
                        toggleWishlistDirect(window._pendingProductId);
                        window._pendingProductId = null;
                    }
                });
                throw new Error('Unauthorized');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // 🔥 UPDATE STATUS WISHLIST
                if (data.in_wishlist) {
                    if (btn) {
                        btn.classList.add('active');
                        btn.innerHTML = '❤️';
                    }
                    showToast('❤️ ' + data.message, 'success');
                    
                    if (typeof loadWishlistPopup === 'function') {
                        setTimeout(function() {
                            loadWishlistPopup();
                            const popup = document.getElementById('wishlist-popup');
                            if (popup) {
                                popup.classList.add('active');
                                document.body.classList.add('popup-open');
                            }
                        }, 400);
                    }
                } else {
                    if (btn) {
                        btn.classList.remove('active');
                        btn.innerHTML = '🤍';
                    }
                    showToast('💔 ' + data.message, 'info');
                }

                // 🔥 UPDATE WISHLIST COUNT DI NAVBAR
                const wishlistCount = document.getElementById('wishlist-count');
                if (wishlistCount) {
                    wishlistCount.textContent = data.count || 0;
                    wishlistCount.style.display = (data.count > 0) ? 'inline-flex' : 'none';
                }
                
                if (typeof window.updateNavbarWishlistCount === 'function') {
                    window.updateNavbarWishlistCount(data.count);
                }
                
                // 🔥 UPDATE ALL WISHLIST BUTTONS UNTUK PRODUK YANG SAMA
                updateAllWishlistButtons(productId, data.in_wishlist);
                
            } else {
                showToast(data.message || 'Gagal menambahkan ke wishlist', 'error');
            }
        })
        .catch(error => {
            if (error.message !== 'Unauthorized') {
                console.error('Error:', error);
                showToast('Terjadi kesalahan', 'error');
            }
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.style.opacity = '1';
            }
        });
    }

    function updateAllWishlistButtons(productId, inWishlist) {
        // Update semua tombol wishlist di halaman (termasuk recommended products)
        const allButtons = document.querySelectorAll(`.add_to_wishlist_btn[data-product-id="${productId}"]`);
        
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
        });
        
        // Update tombol di product show
        const mainBtn = document.getElementById('wishlist-toggle-product');
        if (mainBtn) {
            if (inWishlist) {
                mainBtn.classList.add('active');
                mainBtn.innerHTML = '❤️';
            } else {
                mainBtn.classList.remove('active');
                mainBtn.innerHTML = '🤍';
            }
        }
    }

    function checkWishlistStatus() {
        const productId = {{ $product->id }};
        const inWishlist = {{ $inWishlist ? 'true' : 'false' }};
        
        const btn = document.getElementById('wishlist-toggle-product');
        if (btn) {
            if (inWishlist) {
                btn.classList.add('active');
                btn.innerHTML = '❤️';
            } else {
                btn.classList.remove('active');
                btn.innerHTML = '🤍';
            }
        }
    }

    function checkLoginStatus() {
        return new Promise(function(resolve) {
            const loggedIn = document.querySelector('meta[name="customer-logged-in"]')?.content === 'true';
            resolve(loggedIn);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(checkWishlistStatus, 400);
    });

    // ============================================
    // SHARE PRODUCT
    // ============================================
    window.shareProduct = function() {
        var url = window.location.href;
        var title = '{{ $product->name }}';

        if (navigator.share) {
            navigator.share({ title: title, url: url }).catch(function() {});
        } else {
            navigator.clipboard.writeText(url).then(function() {
                alert('Link produk telah disalin!');
            }).catch(function() {
                prompt('Salin link ini:', url);
            });
        }
    };

    // ============================================
    // QUANTITY BUTTONS
    // ============================================
    document.querySelectorAll('.qty-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            var value = parseInt(qtyInput.value) || 1;
            var max = parseInt(qtyInput.max) || 999;

            if (this.dataset.action === 'increase' && value < max) {
                value += 1;
            } else if (this.dataset.action === 'decrease' && value > 1) {
                value -= 1;
            }
            qtyInput.value = value;
        });
    });

    // ============================================
    // HANDLE KLIK VARIAN
    // ============================================
    document.querySelectorAll('.variant-option').forEach(function(button) {
        button.addEventListener('click', function() {
            if (this.disabled) return;

            var optionId = this.dataset.optionId;
            var valueId = this.dataset.valueId;
            var optionName = this.dataset.optionName || '';

            var group = this.closest('[data-option-id]');
            
            // HAPUS ACTIVE CLASS DARI SEMUA TOMBOL DI GRUP YANG SAMA
            group.querySelectorAll('.variant-option').forEach(function(btn) {
                btn.classList.remove('active');
            });

            this.classList.add('active');
            selectedValues[optionId] = parseInt(valueId);

            updateAvailableVariants();

            var totalOptions = document.querySelectorAll('.variant-options').length;
            var selectedCount = Object.keys(selectedValues).length;
            
            // CARI VARIAN YANG COCOK
            var variant = null;
            if (selectedCount === totalOptions) {
                variant = findVariantByValues(selectedValues, false);
            } else {
                variant = findVariantByValues(selectedValues, false);
            }
            
            if (variant) {
                selectedVariantInput.value = variant.id;
                currentVariantId = variant.id;
                isVariantSelected = true;
                
                // 🔥 GUNAKAN EFFECTIVE_PRICE DAN DISCOUNT_PERCENT
                var price = variant.effective_price ?? variant.price;
                var originalPrice = variant.price;
                var hasDiscount = variant.discount_percent > 0;
                var discountPercent = variant.discount_percent || 0;
                var isOutOfStock = variant.stock <= 0;
                
                if (priceContainer) {
                    if (hasDiscount) {
                        var discountBadge = Math.round(discountPercent) + '%';
                        
                        // 🔥 TAMBAHKAN INFO DISKON PRODUK
                        var productDiscountInfo = '';
                        if ({{ $hasProductDiscount ? 'true' : 'false' }} && {{ $productDiscountPercent ?? 0 }} > 0) {
                            productDiscountInfo = '<span class="product-discount-info" style="font-size:0.6vw;color:#16a34a;display:block;margin-top:0.2vw;">' + Math.round({{ $productDiscountPercent ?? 0 }}) + '%</span>';
                        }
                        
                        priceContainer.innerHTML = `
                            <div class="product_price_box">
                                <span class="price-current discounted" id="display-price">
                                    Rp ${new Intl.NumberFormat('id-ID').format(price)}
                                    ${isOutOfStock ? '<span class="text-red-500 text-xs ml-2 font-normal">(Stok Habis)</span>' : ''}
                                </span>
                                <span class="price-original" id="original-price-display">
                                    Rp ${new Intl.NumberFormat('id-ID').format(originalPrice)}
                                </span>
                                <span class="discount-badge">${discountBadge}</span>
                                ${productDiscountInfo}
                            </div>
                        `;
                    } else {
                        priceContainer.innerHTML = `
                            <span class="price-current" id="display-price">
                                Rp ${new Intl.NumberFormat('id-ID').format(price)}
                                ${isOutOfStock ? '<span class="text-red-500 text-xs ml-2 font-normal">(Stok Habis)</span>' : ''}
                            </span>
                        `;
                    }
                }
                
                // 🔥 UPDATE STOK
                stockDisplay.textContent = variant.stock > 0 ? 'Stok: ' + variant.stock : 'Stok Habis';
                stockDisplay.className = 'product_stock_status ' + (variant.stock > 0 ? 'in-stock' : 'out-of-stock');
                
                // 🔥 TOMBOL HANYA AKTIF JIKA STOK > 0 DAN SEMUA OPSI DIPILIH
                if (variant.stock > 0 && selectedCount === totalOptions) {
                    addToCartBtn.disabled = false;
                    addToCartBtn.textContent = 'Tambah ke Keranjang';
                    buyNowBtn.disabled = false;
                    buyNowBtn.textContent = 'Beli Sekarang';
                } else if (selectedCount === totalOptions && variant.stock <= 0) {
                    addToCartBtn.disabled = true;
                    addToCartBtn.textContent = 'Stok Habis';
                    buyNowBtn.disabled = true;
                    buyNowBtn.textContent = 'Stok Habis';
                } else {
                    addToCartBtn.disabled = true;
                    addToCartBtn.textContent = 'Pilih Varian';
                    buyNowBtn.disabled = true;
                    buyNowBtn.textContent = 'Pilih Varian';
                }
                
                // 🔥 UPDATE GAMBAR - HANYA JIKA YANG DIPILIH ADALAH WARNA
                // Cek apakah option yang diklik adalah Warna
                var isColorOption = false;
                var colorKeywords = ['warna', 'color', 'colour'];
                var optionNameLower = (optionName || '').toLowerCase().trim();
                
                for (var c = 0; c < colorKeywords.length; c++) {
                    if (optionNameLower === colorKeywords[c]) {
                        isColorOption = true;
                        break;
                    }
                }
                
                // 🔥 JIKA WARNA, UPDATE GAMBAR
                if (isColorOption) {
                    updateVariantImage(variant);
                }
                // 🔥 JIKA UKURAN, JANGAN UPDATE GAMBAR
                
                // 🔥 UPDATE ACTIVE STATE
                updateVariantActiveState(variant);
                
            } else {
                // 🔥 TIDAK ADA VARIAN - RESET KE DEFAULT
                resetToDefaultPrice();
            }

            updateAvailableVariants();
        });
    });

    // ============================================
    // ADD TO CART - AJAX
    // ============================================
    document.getElementById('add-to-cart-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const variantId = selectedVariantInput.value;
        const productId = {{ $product->id }};
        const quantity = parseInt(qtyInput.value) || 1;

        if (!variantId) {
            alert('Pilih varian produk terlebih dahulu!');
            return;
        }

        const variant = productVariants.find(function(v) { return v.id == variantId; });
        if (variant && quantity > variant.stock) {
            alert('Stok tidak mencukupi! Stok tersedia: ' + variant.stock);
            return;
        }

        checkLoginStatus().then(isLoggedIn => {
            if (!isLoggedIn) {
                window._pendingProductId = productId;
                window._pendingVariantId = variantId;
                window._pendingQuantity = quantity;
                openLoginPopup('add_to_cart', function() {
                    if (window._pendingProductId) {
                        window.addToCartFromShowPage(
                            window._pendingProductId, 
                            window._pendingVariantId, 
                            window._pendingQuantity
                        );
                        window._pendingProductId = null;
                        window._pendingVariantId = null;
                        window._pendingQuantity = null;
                    }
                });
                return;
            }

            window.addToCartFromShowPage(productId, variantId, quantity);
        });
    });

    window.addToCartFromShowPage = function(productId, variantId, quantity) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const btn = document.getElementById('add-to-cart-btn');

        if (btn) {
            btn.disabled = true;
            btn.textContent = '⏳ Memproses...';
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
        .then(response => {
            if (response.status === 401) {
                window._pendingProductId = productId;
                window._pendingVariantId = variantId;
                window._pendingQuantity = quantity;
                openLoginPopup('add_to_cart', function() {
                    if (window._pendingProductId) {
                        window.addToCartFromShowPage(
                            window._pendingProductId, 
                            window._pendingVariantId, 
                            window._pendingQuantity
                        );
                        window._pendingProductId = null;
                        window._pendingVariantId = null;
                        window._pendingQuantity = null;
                    }
                });
                throw new Error('Unauthorized');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const cartCountEl = document.getElementById('cart-count');
                if (cartCountEl) {
                    cartCountEl.textContent = data.count || 0;
                    cartCountEl.style.display = (data.count > 0) ? 'inline-flex' : 'none';
                }
                
                if (typeof window.updateNavbarCartCount === 'function') {
                    window.updateNavbarCartCount(data.count);
                }
                
                if (typeof loadCartPopup === 'function') {
                    setTimeout(function() {
                        loadCartPopup();
                        const popup = document.getElementById('cart-popup');
                        if (popup) {
                            popup.classList.add('active');
                            document.body.classList.add('popup-open');
                        }
                    }, 300);
                }
                
                showToast(data.message, 'success');
            } else {
                showToast(data.message || 'Gagal menambahkan ke keranjang', 'error');
            }
        })
        .catch(error => {
            if (error.message !== 'Unauthorized') {
                console.error('Error:', error);
                showToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
            }
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.textContent = 'Tambah ke Keranjang';
            }
        });
    };

    // ============================================
    // CHECK WISHLIST STATUS
    // ============================================
    function checkWishlistStatus() {
        const productId = {{ $product->id }};
        const wishlistData = @json(session('wishlist', []));

        if (wishlistData[productId]) {
            const btn = document.getElementById('wishlist-toggle-product');
            if (btn) {
                btn.classList.add('active');
                btn.textContent = '❤️';
            }
        }
    }

    // ============================================
    // INITIALIZATION - DEFAULT TIDAK PILIH VARIAN
    // ============================================
    function initVariantSelection() {
        resetToDefaultPrice();
        
        // Jangan pilih varian apapun
        document.querySelectorAll('.variant-option').forEach(function(btn) {
            btn.classList.remove('active');
        });
        
        selectedValues = {};
        variantValuesInput.value = '';
        isVariantSelected = false;
        
        updateAvailableVariants();
    }

    window.toggleAccordion = function(button) {
        const item = button.closest('.accordion-item');
        const body = item ? item.querySelector('.accordion-body') : button.nextElementSibling;
        const icon = button.querySelector('.accordion-icon');

        if (!body) return;

        const isOpen = body.classList.contains('open');

        if (isOpen) {
            body.classList.remove('open');
            body.style.maxHeight = '0px';
            if (icon) {
                icon.classList.remove('open');
            }
        } else {
            body.classList.add('open');
            body.style.maxHeight = body.scrollHeight + 'px';
            if (icon) {
                icon.classList.add('open');
            }
        }
    };

    function initAccordionState() {
        document.querySelectorAll('.accordion-item').forEach(function(item) {
            const body = item.querySelector('.accordion-body');
            const icon = item.querySelector('.accordion-icon');

            if (!body) return;

            const isOpen = body.classList.contains('open');
            body.style.maxHeight = isOpen ? body.scrollHeight + 'px' : '0px';

            if (icon) {
                icon.classList.toggle('open', isOpen);
            }
        });
    }

    // ============================================
    // ZOOM FUNCTIONALITY
    // ============================================
    (function() {
        const container = document.getElementById('main-image-container');
        const image = document.getElementById('main-image');
        const magnifier = document.getElementById('magnifier-glass');

        if (!container || !image || !magnifier) return;

        let isZoomed = false;
        container.addEventListener('mousemove', function(e) {
            if (!isZoomed) return;

            const rect = container.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const glassSize = magnifier.offsetWidth;
            const glassX = x - glassSize / 2;
            const glassY = y - glassSize / 2;

            const maxX = rect.width - glassSize;
            const maxY = rect.height - glassSize;
            
            magnifier.style.left = Math.max(0, Math.min(glassX, maxX)) + 'px';
            magnifier.style.top = Math.max(0, Math.min(glassY, maxY)) + 'px';

            const percentX = (x / rect.width) * 100;
            const percentY = (y / rect.height) * 100;

            magnifier.style.backgroundImage = `url('${image.src}')`;
            magnifier.style.backgroundPosition = `${percentX}% ${percentY}%`;
            magnifier.style.backgroundSize = `${rect.width * 2}px ${rect.height * 2}px`;
        });

        container.addEventListener('mouseenter', function() {
            isZoomed = true;
            container.classList.add('zoomed');
            magnifier.classList.add('active');
        });

        container.addEventListener('mouseleave', function() {
            isZoomed = false;
            container.classList.remove('zoomed');
            magnifier.classList.remove('active');
            magnifier.style.backgroundImage = '';
        });

        container.addEventListener('click', function() {
            if (isZoomed) {
                isZoomed = false;
                container.classList.remove('zoomed');
                magnifier.classList.remove('active');
                magnifier.style.backgroundImage = '';
            } else {
                isZoomed = true;
                container.classList.add('zoomed');
                magnifier.classList.add('active');
                
                const rect = container.getBoundingClientRect();
                const glassSize = magnifier.offsetWidth;
                const centerX = (rect.width - glassSize) / 2;
                const centerY = (rect.height - glassSize) / 2;
                
                magnifier.style.left = centerX + 'px';
                magnifier.style.top = centerY + 'px';
                
                magnifier.style.backgroundImage = `url('${image.src}')`;
                magnifier.style.backgroundSize = `${rect.width * 2}px ${rect.height * 2}px`;
                magnifier.style.backgroundPosition = `50% 50%`;
            }
        });

        window.updateZoomImage = function(newImageUrl) {
            if (!newImageUrl) return;
            image.src = newImageUrl;
            
            isZoomed = false;
            container.classList.remove('zoomed');
            magnifier.classList.remove('active');
            magnifier.style.backgroundImage = '';
        };

        window.addEventListener('resize', function() {
            if (isZoomed) {
                const rect = container.getBoundingClientRect();
                magnifier.style.backgroundSize = `${rect.width * 2}px ${rect.height * 2}px`;
            }
        });

    })();

    initAccordionState();
    initThumbnailScroll();

    window.addEventListener('resize', function() {
        document.querySelectorAll('.accordion-body.open').forEach(function(body) {
            body.style.maxHeight = body.scrollHeight + 'px';
        });
    });

    // 🔥 INIT - JANGAN PILIH VARIAN, TAMPILKAN RANGE HARGA
    setTimeout(initVariantSelection, 300);
    setTimeout(checkWishlistStatus, 400);

});

function openSizeGuide() {
    const overlay = document.getElementById('size-guide-overlay');
    if (overlay) {
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeSizeGuide() {
    const overlay = document.getElementById('size-guide-overlay');
    if (overlay) {
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeSizeGuide();
    }
});
</script>

@endsection