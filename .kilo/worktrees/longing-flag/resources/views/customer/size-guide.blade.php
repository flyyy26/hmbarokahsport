@extends('layouts.customer')

@section('title', 'Panduan Ukuran - Barokah Sport')

@section('content')

<style>
    html {
        scroll-behavior: smooth;
    }

    /* ============================================
       SIZE GUIDE CONTAINER
       ============================================ */
    .sizeguide_container {
        width: 100%;
        margin: 0 auto;
        border-top: 0.1vw solid #076694;
    }

    .sizeguide_top_container {
        width: 100%;
        padding: 1.3vw 7.54vw;
        padding-bottom: 1.8vw;
        background: #f9fafb;
    }

    .sizeguide-header h1 {
        font-size: 2.3vw;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        font-family: heading, sans-serif;
        text-transform: uppercase;
    }

    .sizeguide-header p {
        font-size: 0.85vw;
        color: #94a3b8;
        margin-top: 0.2vw;
    }

    .sizeguide_content_container {
        width: 100%;
        padding: 3vw 7.4vw;
    }

    /* ============================================
       SIZE GUIDE GRID
       ============================================ */
    .sizeguide_grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 2vw;
    }

    .sizeguide_card {
        background: white;
        border-radius: 0.9vw;
        padding: 0.7vw;
        padding-bottom: 1.2vw;
        box-shadow: 0 0.1vw 0.5vw rgba(0, 0, 0, 0.05);
        border: 0.1vw solid #e2e8f0;
        transition: all 0.3s ease;
        text-decoration: none;
        display: block;
        text-align: center;
        overflow: hidden;
    }

    .sizeguide_card:hover {
        transform: translateY(-0.3vw);
        box-shadow: 0 0.5vw 1.5vw rgba(0, 0, 0, 0.1);
        border-color: #076694;
    }

    .sizeguide_card_icon {
        display: block;
        margin-bottom: 0.8vw;
    }

    .sizeguide_image {
        width: 100%;
        height: 15vw;
        position: relative;
        overflow: hidden;
        border-radius: 0.7vw;
        background: #f1f5f9;
    }

    .sizeguide_image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        transition: transform 0.3s ease;
    }

    .sizeguide_card:hover .sizeguide_image img {
        transform: scale(1.05);
    }

    .sizeguide_image .no-image {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        font-size: 5vw;
        color: #cbd5e1;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    }

    .sizeguide_card h3 {
        font-size: 1.3vw;
        font-weight: 600;
        color: #0f172a;
        margin: 0.8vw 0 0.3vw;
        font-family: heading, sans-serif;
    }

    .sizeguide_card p {
        font-size: 0.8vw;
        color: #94a3b8;
        margin: 0;
    }

    .sizeguide_card .badge {
        display: inline-block;
        margin-top: 0.8vw;
        padding: 0.3vw 1.5vw;
        background: #076694;
        color: white;
        border-radius: 100vw;
        font-size: 0.75vw;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .sizeguide_card:hover .badge {
        background: #055a7a;
        transform: scale(1.05);
    }

    /* ============================================
       EMPTY STATE
       ============================================ */
    .sizeguide_empty {
        text-align: center;
        padding: 4vw;
        background: white;
        border-radius: 0.7vw;
        border: 0.1vw solid #e2e8f0;
    }

    .sizeguide_empty iconify-icon {
        font-size: 4vw;
        color: #94a3b8;
        margin-bottom: 1vw;
        display: block;
    }

    .sizeguide_empty h3 {
        font-size: 1.2vw;
        color: #0f172a;
        margin-bottom: 0.5vw;
    }

    .sizeguide_empty p {
        font-size: 0.85vw;
        color: #94a3b8;
    }

    /* ============================================
       🔥 RESPONSIVE MOBILE (max-width: 768px)
       ============================================ */
    @media (max-width: 768px) {
        /* ============================================
           HEADER - MOBILE
           ============================================ */
        .sizeguide_top_container {
            padding: 4.5vw 4.5vw;
            background: #f9fafb;
        }

        .sizeguide-header h1 {
            font-size: 6vw;
            position: relative;
            display: inline-block;
        }

        .sizeguide-header p {
            font-size: 3vw;
            margin-top: 0.2vw;
            color: #64748b;
        }

        /* ============================================
           CONTENT - MOBILE
           ============================================ */
        .sizeguide_content_container {
            padding: 4vw 4vw 8vw;
        }

        /* ============================================
           GRID - MOBILE
           ============================================ */
        .sizeguide_grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 3vw;
        }

        /* ============================================
           CARD - MOBILE
           ============================================ */
        .sizeguide_card {
            padding: 0;
            padding-bottom: 3vw;
            border-radius: 2vw;
            border: 0.15vw solid #e8edf4;
            background: #ffffff;
            transition: all 0.3s ease;
            box-shadow: 0 0.3vw 1.5vw rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .sizeguide_card:active {
            transform: scale(0.97);
            box-shadow: 0 0.3vw 1vw rgba(0, 0, 0, 0.06);
        }

        .sizeguide_card_icon {
            margin-bottom: 0;
        }

        .sizeguide_image {
            height: 30vw;
            border-radius: 2vw 2vw 0 0;
            background: #f1f5f9;
        }

        .sizeguide_image .no-image {
            font-size: 10vw;
        }

        .sizeguide_card h3 {
            font-size: 5.5vw;
            margin: 2vw 0 0.5vw;
            padding: 0 2vw;
        }

        .sizeguide_card p {
            font-size: 2.5vw;
            padding: 0 2vw;
            color: #94a3b8;
        }

        .sizeguide_card .badge {
            font-size: 3vw;
            padding: 0.8vw 3.5vw;
            margin-top: 3.5vw;
            border-radius: 100vw;
        }

        /* ============================================
           EMPTY STATE - MOBILE
           ============================================ */
        .sizeguide_empty {
            padding: 8vw 4vw;
            border-radius: 2vw;
            border: 0.15vw solid #e8edf4;
        }

        .sizeguide_empty iconify-icon {
            font-size: 10vw;
            margin-bottom: 2vw;
        }

        .sizeguide_empty h3 {
            font-size: 4vw;
            margin-bottom: 1vw;
        }

        .sizeguide_empty p {
            font-size: 3vw;
        }

        .sizeguide_empty .mt-2 {
            margin-top: 1.5vw;
        }

        .sizeguide_empty .text-sm {
            font-size: 2.5vw;
        }

        .sizeguide_empty .text-gray-400 {
            color: #94a3b8;
        }
    }
</style>

<div class="sizeguide_container">
    {{-- HEADER --}}
    <div class="sizeguide_top_container">
        <div class="sizeguide-header">
            <h1>Panduan Ukuran</h1>
            <p>Temukan panduan ukuran yang tepat untuk setiap kategori produk</p>
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="sizeguide_content_container">
        @if($categories->count() > 0)
            <div class="sizeguide_grid">
                @foreach($categories as $category)
                    <a href="{{ route('customer.size-guide.show', $category->slug) }}" class="sizeguide_card">
                        <span class="sizeguide_card_icon">
                            @if($category->image)
                                <div class="sizeguide_image">
                                    <img src="{{ asset('storage/' . $category->image) }}" 
                                         alt="{{ $category->name }}"
                                         loading="lazy">
                                </div>
                            @else
                                <div class="sizeguide_image">
                                    <div class="no-image">
                                        📦
                                    </div>
                                </div>
                            @endif
                        </span>
                        <h3>{{ $category->name }}</h3>
                        <p>{{ $category->sizeGuides->count() }} ukuran tersedia</p>
                        <span class="badge">Lihat Panduan →</span>
                    </a>
                @endforeach
            </div>
        @else
            <div class="sizeguide_empty">
                <iconify-icon icon="mdi:ruler-square-compass"></iconify-icon>
                <h3>Belum Ada Panduan Ukuran</h3>
                <p>Saat ini belum tersedia panduan ukuran untuk produk kami.</p>
                <p class="mt-2 text-sm text-gray-400">Silakan cek kembali nanti.</p>
            </div>
        @endif
    </div>
</div>

@endsection