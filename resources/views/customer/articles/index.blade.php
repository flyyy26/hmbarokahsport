@extends('layouts.customer')

@php
    // ============================================
    // 🔥 SEO DATA
    // ============================================
    
    // 1. Tentukan apakah ada filter aktif
    $hasFilter = request()->anyFilled(['category', 'sort', 'search']);
    $hasSearch = request()->filled('search');
    $hasCategory = request()->filled('category');
    $currentPage = request()->get('page', 1);
    
    // 2. Title halaman
    $pageTitle = 'Artikel & Tips Olahraga';
    
    if ($hasSearch) {
        $pageTitle = 'Pencarian: ' . request('search');
    } elseif ($hasCategory) {
        $activeCategory = $categories->firstWhere('id', request('category'));
        if ($activeCategory) {
            $pageTitle = 'Artikel ' . $activeCategory->name;
        }
    }
    
    // Tambahkan halaman pagination ke title (untuk SEO)
    if ($currentPage > 1) {
        $pageTitle .= ' - Halaman ' . $currentPage;
    }
    
    $storeName = $setting?->store_name ?? 'Barokah Sport';
    $fullTitle = $pageTitle . ' - ' . $storeName;
    
    // 3. Meta Description
    if ($hasSearch) {
        $metaDescription = 'Hasil pencarian artikel "' . request('search') . '" di ' . $storeName 
            . '. Temukan tips, panduan, dan info olahraga terbaru.';
    } elseif ($hasCategory && $activeCategory ?? false) {
        $metaDescription = 'Kumpulan artikel ' . $activeCategory->name . ' terbaru di ' . $storeName 
            . '. Tips, panduan, dan informasi seputar ' . strtolower($activeCategory->name) 
            . ' untuk Anda.';
    } else {
        $metaDescription = 'Baca artikel & tips olahraga terbaru di ' . $storeName 
            . '. Informasi seputar sepatu, jersey, futsal, bulu tangkis, dan perlengkapan olahraga lainnya.';
    }
    
    // Batasi 160 karakter
    $metaDescription = Str::limit($metaDescription, 160, '...');
    
    // 4. OG Image — pakai logo setting
    $ogImage = $setting?->logo 
        ? (Str::startsWith($setting->logo, ['http://', 'https://']) 
            ? $setting->logo 
            : asset('storage/' . $setting->logo))
        : asset('images/default-og.jpg');
    
    // 5. Canonical URL
    // ⚠️ Halaman filter/search/pagination → canonical ke versi bersih
    // supaya Google tidak index duplicate content
    if ($hasFilter) {
        // Kalau ada filter/search → canonical ke halaman index murni
        $canonicalUrl = route('customer.articles.index');
    } elseif ($currentPage > 1) {
        // Pagination → canonical ke halaman itu sendiri (self-referencing)
        $canonicalUrl = route('customer.articles.index', ['page' => $currentPage]);
    } else {
        // Halaman 1 tanpa filter → canonical ke dirinya sendiri
        $canonicalUrl = route('customer.articles.index');
    }
    
    // 6. Robots meta
    // Search & filter → noindex, follow (jangan index, tapi ikuti link)
    // Halaman normal → index, follow
    if ($hasSearch) {
        $robotsContent = 'noindex, follow';
    } elseif ($hasFilter) {
        $robotsContent = 'noindex, follow';
    } else {
        $robotsContent = 'index, follow, max-image-preview:large, max-snippet:-1';
    }
@endphp

@section('title', $fullTitle)

{{-- ============================================ --}}
{{-- 🔥 META TAGS --}}
{{-- ============================================ --}}
@section('meta_description', $metaDescription)
@section('og_type', 'website')
@section('og_title', $fullTitle)
@section('og_description', $metaDescription)
@section('og_image', $ogImage)

@section('meta')
    <meta name="description" content="{{ $metaDescription }}">
    
    {{-- Keywords --}}
    @php
        $keywords = ['artikel olahraga', 'tips olahraga', 'blog olahraga'];
        if ($hasCategory && $activeCategory ?? false) {
            $keywords[] = 'artikel ' . strtolower($activeCategory->name);
            $keywords[] = strtolower($activeCategory->name);
        }
        $keywords[] = $storeName;
    @endphp
    <meta name="keywords" content="{{ implode(', ', $keywords) }}">
    
    {{-- Canonical --}}
    <link rel="canonical" href="{{ $canonicalUrl }}">
    
    {{-- Robots --}}
    <meta name="robots" content="{{ $robotsContent }}">
    
    {{-- Pagination hints (prev/next) --}}
    @if ($articles->onFirstPage() === false)
        <link rel="prev" href="{{ $articles->previousPageUrl() }}">
    @endif
    @if ($articles->hasMorePages())
        <link rel="next" href="{{ $articles->nextPageUrl() }}">
    @endif
@endsection

@section('schema')
@php
    // ============================================
    // 🔥 COLLECTION PAGE SCHEMA
    // ============================================
    $indexSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'CollectionPage',
        'name' => $pageTitle,
        'description' => $metaDescription,
        'url' => $canonicalUrl,
        'inLanguage' => 'id-ID',
        'isPartOf' => [
            '@type' => 'WebSite',
            'name' => $storeName,
            'url' => route('customer.home'),
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => $storeName,
            'logo' => [
                '@type' => 'ImageObject',
                'url' => $ogImage,
            ],
        ],
    ];
    
    // 🔥 ItemList: daftar artikel di halaman ini
    if ($articles->count() > 0) {
        $itemListElements = [];
        $position = 1;
        
        foreach ($articles as $article) {
            $itemListElements[] = [
                '@type' => 'ListItem',
                'position' => $position,
                'url' => route('customer.articles.show', $article->slug),
                'name' => $article->title,
                'image' => $article->image 
                    ? asset('storage/' . $article->image) 
                    : $ogImage,
            ];
            $position++;
        }
        
        $indexSchema['mainEntity'] = [
            '@type' => 'ItemList',
            'numberOfItems' => $articles->count(),
            'itemListElement' => $itemListElements,
        ];
    }
    
    // ============================================
    // 🔥 BREADCRUMB SCHEMA
    // ============================================
    $breadcrumbItems = [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Beranda',
            'item' => route('customer.home'),
        ],
    ];
    
    if ($hasCategory && $activeCategory ?? false) {
        $breadcrumbItems[] = [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => 'Artikel',
            'item' => route('customer.articles.index'),
        ];
        $breadcrumbItems[] = [
            '@type' => 'ListItem',
            'position' => 3,
            'name' => $activeCategory->name,
            'item' => $canonicalUrl,
        ];
    } else {
        $breadcrumbItems[] = [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => 'Artikel',
            'item' => $canonicalUrl,
        ];
    }
    
    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $breadcrumbItems,
    ];
@endphp

<script type="application/ld+json">
{!! json_encode($indexSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>

<script type="application/ld+json">
{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endsection

@section('content')

<style>
    /* ============================================
       ARTICLE GRID
       ============================================ */
       .article-container{
            padding-bottom:5vw;
       }
    .article-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 2.3vw;
        padding: 2vw 7.54vw;
        padding-bottom: 0;
    }

    /* ============================================
       ARTICLE CARD
       ============================================ */
    .artikel_section_box {
        background: #ffffff;
        transition: all 0.3s ease;
        position: relative;
    }

    .artikel_section_box_img{
        width: 100%;
        height:14vw;
        position:relative;
        border-radius:1vw;
        border:.1vw solid black;
        overflow: hidden;
        margin-bottom:.8vw;
    }
    .artikel_section_box_img img{
        position:absolute;
        width:100%;
        height:100%;
        left:0;
        top:0;
        object-fit: cover;
        object-position: center;
        transition:.4s all;
    }
    .artikel_section_box_img:hover img{
        transform:scale(1.1);
    }
    .artikel_section_meta{
        display:flex;
        gap:.7vw;
    }
    .artikel_section_meta_box{
        display:flex;
        align-items:center;
        gap:.2vw;
        margin-bottom:.5vw;
    }
    .artikel_section_meta_box iconify-icon{
        font-size:.9vw;
        color:#ecbc42;
    }
    .artikel_section_meta_box span{
        font-size:.8vw;
    }
    .artikel_section_content h3{
        font-size:1.17vw;
        font-weight:600;
        margin-bottom:.4vw;
    }
    .artikel_section_content p{
        font-size:.85vw;
        color:rgb(105, 105, 105);
        font-weight:300;
    }
    .artikel_section_content button{
        margin-top:1vw;
        outline:none;
        border:none;
        background-color:#DE161F;
        text-transform:uppercase;
        font-size:.83vw;
        padding:.4vw .8vw;
        border-radius:.4vw;
        color:white;
        cursor:pointer;
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
        
        color:rgb(102, 72, 9);
        transition:.4s all;
    }
    .artikel_section_content button:hover{
        transform: scale(1.05);
    }

    /* ============================================
       FILTER ROW (DESKTOP)
       ============================================ */
    .katalog_top_container {
        width: 100%;
        padding: 1.3vw 7.54vw;
        padding-bottom: 1.8vw;
        background: #f9fafb;
    }

    .catalog-header {
        margin-bottom: 1.5vw;
    }

    .catalog-header h1 {
        font-size: 2.3vw;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        font-family: heading, sans-serif;
        text-transform: uppercase;
    }

    .catalog-header p {
        font-size: 0.85vw;
        color: #94a3b8;
        margin-top: 0.2vw;
    }

    
    /* ============================================
       PAGINATION
       ============================================ */
    .pagination-wrapper {
        margin-top: 2vw;
        display: flex;
        justify-content: center;
        padding-bottom: 2vw;
    }

    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 4vw 0;
        color: #94a3b8;
    }

    .empty-state .empty-icon {
        font-size: 4vw;
        margin-bottom: 1vw;
    }

    .empty-state p {
        font-size: 1.2vw;
    }

    /* ============================================
       RESPONSIVE - TABLET
       ============================================ */
    @media (max-width: 1024px) {
        .article-grid {
            grid-template-columns: repeat(3, 1fr);
        }

        .katalog_top_container {
            padding: 1.5vw 3vw;
        }

        .filter-row {
            padding: 1vw 1.5vw;
            gap: 1vw;
        }

        .custom-select-wrapper {
            min-width: 10vw;
        }
    }

    /* ============================================
       🔥 RESPONSIVE - MOBILE (max-width: 768px)
       ============================================ */
    @media (max-width: 768px) {
        /* ============================================
           HEADER - MOBILE
           ============================================ */
        .katalog_top_container {
            padding: 4.5vw 4.5vw;
            background: #f9fafb;
        }

        .catalog-header h1 {
            font-size: 8vw;
            position: relative;
            display: inline-block;
        }

        .catalog-header p {
            font-size: 3.5vw;
            margin-top: .2vw;
            color: #64748b;
        }

        /* ============================================
           FILTER ROW - HIDE DI MOBILE
           ============================================ */
        .filter-row {
            display: none !important;
        }

        /* ============================================
           FILTER TOGGLE BUTTON - MOBILE
           ============================================ */
        .filter-toggle-btn {
            display: flex !important;
        }

        /* ============================================
           ARTICLE GRID - MOBILE
           ============================================ */
        .article-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 3.5vw;
            padding: 4vw 4vw 3vw;
        }
        .artikel_section_box_img {
            width: 100%;
            height: 33vw;
            position: relative;
            border-radius: 2vw;
            border: none;
            overflow: hidden;
            margin-bottom: 2.5vw;
        }
        .artikel_section_meta {
            display: flex;
            gap: 1.7vw;
        }
        .artikel_section_meta_box {
            gap: 1vw;
            margin-bottom: 1.5vw;
        }
        .artikel_section_meta_box iconify-icon {
            font-size: 2.2vw;
            color: #ecbc42;
        }
        .artikel_section_meta_box span {
            font-size: 2vw;
        }
        .artikel_section_content h3 {
            font-size: 3.5vw;
            font-weight: 600;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 1.4vw;
        }
        .artikel_section_content p {
            font-size: 3vw;
            color: rgb(105, 105, 105);
            font-weight: 300;
        }
        .artikel_section_content button {
            margin-top: 3vw;
            outline: none;
            border: none;
            background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
            text-transform: uppercase;
            font-size: 3vw;
            color:rgb(102, 72, 9) !important;
            padding: 1vw 2.8vw;
            border-radius: 1.4vw;
            color: white;
            cursor: pointer;
            transition: .4s all;
        }

        /* ============================================
           EMPTY STATE - MOBILE
           ============================================ */
        .empty-state .empty-icon {
            font-size: 10vw;
        }

        .empty-state p {
            font-size: 3vw;
        }

        /* ============================================
           PAGINATION - MOBILE
           ============================================ */
        .pagination-wrapper {
            margin-top: 3vw;
            padding-bottom: 4vw;
        }

        .pagination-wrapper .pagination {
            font-size: 2.8vw;
        }
    }
</style>

<div class="catalog-container article-container">
    <div class="katalog_top_container">
        <div class="catalog-header">
            <h1>Artikel</h1>
            <p>Informasi dan tips seputar olahraga dari Barokah Sport</p>
        </div>

        {{-- ============================================ --}}
        {{-- FILTER ROW - DESKTOP --}}
        {{-- ============================================ --}}
        <form id="filter-form" method="GET" action="{{ route('customer.articles.index') }}" class="filter-row">
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

            {{-- SORT BY --}}
            <span class="filter-label">Urutkan</span>
            <div class="custom-select-wrapper" data-name="sort">
                <div class="custom-select-trigger">
                    <span class="trigger-text">
                        @php
                            $sortOptions = [
                                'newest' => 'Terbaru',
                                'oldest' => 'Terlama',
                                'title_asc' => 'Judul (A-Z)',
                                'title_desc' => 'Judul (Z-A)'
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
                    <div class="dropdown-item {{ request('sort') == 'oldest' ? 'active' : '' }}" data-value="oldest">
                        <span>Terlama</span>
                        <span class="check-icon">✓</span>
                    </div>
                    <div class="dropdown-item {{ request('sort') == 'title_asc' ? 'active' : '' }}" data-value="title_asc">
                        <span>Judul (A-Z)</span>
                        <span class="check-icon">✓</span>
                    </div>
                    <div class="dropdown-item {{ request('sort') == 'title_desc' ? 'active' : '' }}" data-value="title_desc">
                        <span>Judul (Z-A)</span>
                        <span class="check-icon">✓</span>
                    </div>
                </div>
            </div>

            {{-- RESET FILTER --}}
            @if(request()->anyFilled(['category', 'sort']))
                <a href="{{ route('customer.articles.index') }}" class="filter-reset">
                    ✕ Reset
                </a>
            @endif

            <span class="filter-count">{{ $articles->total() }} artikel</span>
        </form>

        {{-- ============================================ --}}
        {{-- 🔥 FILTER TOGGLE BUTTON - MOBILE --}}
        {{-- ============================================ --}}
        <button type="button" class="filter-toggle-btn" onclick="openFilterPopup()">
            <iconify-icon icon="tabler:filter"></iconify-icon>
            Filter
            <span class="filter-badge" id="filter-badge" style="display:none;">0</span>
        </button>
    </div>

    {{-- ============================================ --}}
    {{-- ARTICLE GRID --}}
    {{-- ============================================ --}}
    <div class="article-grid">
        @forelse ($articles as $article)
            <div class="artikel_section_box">
                <div class="artikel_section_box_img">
                    <a href="{{ route('customer.articles.show', $article->slug) }}">
                        <img src="{{ $article->image_url }}"
                            alt="{{ $article->title }}"
                            width="400"
                            height="280"
                            loading="lazy"
                            decoding="async">
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
                    <h3>
                        {{ $article->title }}
                    </h3>
                    <p>{{ Str::limit(strip_tags($article->excerpt ?: $article->content), 90) }}</p>
                    <a href="{{ route('customer.articles.show', $article->slug) }}">
                        <button>Baca Selengkapnya</button>
                    </a>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-icon">📭</div>
                <p>Belum ada artikel yang tersedia.</p>
            </div>
        @endforelse
    </div>

    {{-- PAGINATION --}}
    @if ($articles->hasPages())
        <div class="pagination-wrapper">
            {{ $articles->links() }}
        </div>
    @endif
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
            <h3>Filter Artikel</h3>
            <button type="button" aria-label="Tutup" class="filter-popup-close" onclick="closeFilterPopup()">✕</button>
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

            {{-- URUTKAN --}}
            <div class="filter-popup-group">
                <span class="filter-group-label">Urutkan</span>
                <div class="filter-options" data-filter="sort">
                    @php
                        $sortOptions = [
                            'newest' => 'Terbaru',
                            'oldest' => 'Terlama',
                            'title_asc' => 'Judul (A-Z)',
                            'title_desc' => 'Judul (Z-A)'
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
{{-- JAVASCRIPT - CUSTOM SELECT & FILTER POPUP --}}
{{-- ============================================ --}}
<script>
// ============================================
// CUSTOM SELECT - TOGGLE DROPDOWN
// ============================================

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

// ============================================
// 🔥 FILTER POPUP
// ============================================

// State untuk menyimpan pilihan filter
var filterState = {
    category: '{{ request('category') ?? '' }}',
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
    if (filterState.sort && filterState.sort !== 'newest') count++;
    
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
    var filterNames = ['category', 'sort'];
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
    var hasFilter = filterState.category || (filterState.sort && filterState.sort !== 'newest');
    if (hasFilter) {
        updateFilterBadge();
    }

    // ============================================
    // CUSTOM SELECT - EVENT LISTENERS
    // ============================================
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

    console.log('📝 Artikel page ready with filter popup');
});
</script>

@endsection