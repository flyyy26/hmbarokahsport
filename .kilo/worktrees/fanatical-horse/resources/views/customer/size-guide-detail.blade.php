@extends('layouts.customer')

@section('title', 'Panduan Ukuran ' . $category->name . ' - Barokah Sport')

@section('content')

<style>
    html {
        scroll-behavior: smooth;
    }

    /* ============================================
       SIZE GUIDE DETAIL CONTAINER
       ============================================ */
    .sizeguide_detail_container {
        width: 100%;
        margin: 0 auto;
        border-top: 0.1vw solid #076694;
    }

    .sizeguide_detail_top_container {
        width: 100%;
        padding: 1.3vw 7.54vw;
        padding-bottom: 1.8vw;
        background: #f9fafb;
    }

    .sizeguide_detail_header h1 {
        font-size: 2.3vw;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        font-family: heading, sans-serif;
        text-transform: uppercase;
    }

    .sizeguide_detail_header p {
        font-size: 0.85vw;
        color: #94a3b8;
        margin-top: 0.2vw;
    }

    .sizeguide_detail_header .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5vw;
        color: #076694;
        font-size: 0.85vw;
        text-decoration: none;
        margin-top: 0.5vw;
        transition: color 0.2s;
    }

    .sizeguide_detail_header .back-link:hover {
        color: #055a7a;
        text-decoration: underline;
    }

    .sizeguide_detail_content_container {
        width: 100%;
        padding: 3vw 7.4vw;
    }

    /* ============================================
       TABLE WRAPPER
       ============================================ */
    .sizeguide_detail_table_wrapper {
        background: white;
        border-radius: 0.7vw;
        padding: 2vw;
        box-shadow: 0 0.1vw 0.5vw rgba(0, 0, 0, 0.05);
        border: 0.1vw solid #e2e8f0;
        overflow-x: auto;
        transition: all 0.3s ease;
    }

    .sizeguide_detail_table_wrapper:hover {
        box-shadow: 0 0.3vw 1.5vw rgba(0, 0, 0, 0.08);
    }

    .sizeguide_detail_table_wrapper .table-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5vw;
        flex-wrap: wrap;
        gap: 1vw;
    }

    .sizeguide_detail_table_wrapper .table-header h2 {
        font-size: 1.2vw;
        font-weight: 600;
        color: #0f172a;
        margin: 0;
    }

    .sizeguide_detail_table_wrapper .table-header .category-badge {
        padding: 0.3vw 1.2vw;
        background: #076694;
        color: white;
        border-radius: 100vw;
        font-size: 0.7vw;
        font-weight: 500;
    }

    /* ============================================
       TABLE STYLES
       ============================================ */
    .sizeguide_detail_table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85vw;
        min-width: 300px;
    }

    .sizeguide_detail_table thead {
        background: #f8fafc;
        border-radius: 0.5vw;
    }

    .sizeguide_detail_table thead th {
        padding: 0.8vw 1.2vw;
        text-align: center;
        font-weight: 600;
        color: #0f172a;
        border-bottom: 0.15vw solid #e2e8f0;
        font-size: 0.8vw;
        text-transform: uppercase;
        letter-spacing: 0.05vw;
    }

    .sizeguide_detail_table thead th:first-child {
        text-align: left;
        padding-left: 1.5vw;
    }

    .sizeguide_detail_table tbody td {
        padding: 0.8vw 1.2vw;
        text-align: center;
        border-bottom: 0.05vw solid #f1f5f9;
        color: #475569;
        font-size: 0.85vw;
        transition: background 0.2s;
    }

    .sizeguide_detail_table tbody td:first-child {
        text-align: left;
        padding-left: 1.5vw;
    }

    .sizeguide_detail_table tbody tr:hover {
        background: #f8fafc;
    }

    .sizeguide_detail_table tbody tr:last-child td {
        border-bottom: none;
    }

    .sizeguide_detail_table .size-label {
        font-weight: 700;
        color: #076694;
        background: #f0f9ff;
        font-size: 0.9vw;
    }

    .sizeguide_detail_table .size-label .size-icon {
        margin-right: 0.5vw;
    }

    /* ============================================
       NOTE / TIPS
       ============================================ */
    .sizeguide_detail_note {
        margin-top: 1.5vw;
        padding: 1.2vw 1.8vw;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 0.7vw;
        border-left: 0.4vw solid #076694;
        font-size: 0.8vw;
        color: #64748b;
        display: flex;
        align-items: flex-start;
        gap: 1vw;
    }

    .sizeguide_detail_note .note-icon {
        font-size: 1.5vw;
        flex-shrink: 0;
        margin-top: 0.1vw;
    }

    .sizeguide_detail_note .note-content {
        flex: 1;
    }

    .sizeguide_detail_note .note-content strong {
        color: #0f172a;
        display: block;
        margin-bottom: 0.2vw;
    }

    .sizeguide_detail_note .note-content .note-list {
        padding-left: 1.2vw;
        margin: 0.3vw 0 0;
    }

    .sizeguide_detail_note .note-content .note-list li {
        margin-bottom: 0.2vw;
    }

    /* ============================================
       EMPTY STATE
       ============================================ */
    .sizeguide_detail_empty {
        text-align: center;
        padding: 4vw 2vw;
    }

    .sizeguide_detail_empty .empty-icon {
        font-size: 4vw;
        color: #cbd5e1;
        margin-bottom: 1vw;
        display: block;
    }

    .sizeguide_detail_empty h3 {
        font-size: 1.2vw;
        color: #0f172a;
        margin-bottom: 0.3vw;
    }

    .sizeguide_detail_empty p {
        color: #94a3b8;
        font-size: 0.9vw;
    }

    /* ============================================
       🔥 RESPONSIVE MOBILE (max-width: 768px)
       ============================================ */
    @media (max-width: 768px) {
        /* ============================================
           HEADER - MOBILE
           ============================================ */
        .sizeguide_detail_top_container {
            padding: 4.5vw 4.5vw;
            background: #f9fafb;
        }

        .sizeguide_detail_header .back-link {
            font-size: 2.8vw;
            gap: 1vw;
            margin-top: 0;
            margin-bottom: 1.5vw;
            padding: 0.5vw 0;
        }

        .sizeguide_detail_header .back-link iconify-icon {
            font-size: 3vw;
        }

        .sizeguide_detail_header h1 {
            font-size: 6vw;
            position: relative;
            display: inline-block;
        }

        .sizeguide_detail_header p {
            font-size: 3vw;
            margin-top: 1vw;
            color: #64748b;
        }

        /* ============================================
           CONTENT - MOBILE
           ============================================ */
        .sizeguide_detail_content_container {
            padding: 4vw 4vw 8vw;
        }

        /* ============================================
           TABLE WRAPPER - MOBILE
           ============================================ */
        .sizeguide_detail_table_wrapper {
            padding: 3vw;
            border-radius: 2vw;
            border: 0.15vw solid #e8edf4;
        }

        .sizeguide_detail_table_wrapper .table-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1.5vw;
            margin-bottom: 3vw;
        }

        .sizeguide_detail_table_wrapper .table-header h2 {
            font-size: 4vw;
        }

        .sizeguide_detail_table_wrapper .table-header .category-badge {
            padding: 0.8vw 3vw;
            font-size: 2.2vw;
            border-radius: 100vw;
        }

        /* ============================================
           TABLE - MOBILE
           ============================================ */
        .sizeguide_detail_table {
            font-size: 2.8vw;
            min-width: 250px;
        }

        .sizeguide_detail_table thead th {
            padding: 2vw 1.5vw;
            font-size: 2.5vw;
        }

        .sizeguide_detail_table thead th:first-child {
            padding-left: 2vw;
        }

        .sizeguide_detail_table tbody td {
            padding: 2vw 1.5vw;
            font-size: 2.8vw;
        }

        .sizeguide_detail_table tbody td:first-child {
            padding-left: 2vw;
        }

        .sizeguide_detail_table .size-label {
            font-size: 3vw;
        }

        .sizeguide_detail_table .size-label .size-icon {
            margin-right: 1vw;
        }

        /* ============================================
           NOTE - MOBILE
           ============================================ */
        .sizeguide_detail_note {
            margin-top: 3vw;
            padding: 3vw 3.5vw;
            border-radius: 2vw;
            border-left-width: 0.8vw;
            font-size: 2.6vw;
            gap: 2vw;
            flex-direction: column;
            align-items: flex-start;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        }

        .sizeguide_detail_note .note-icon {
            font-size: 5vw;
        }

        .sizeguide_detail_note .note-content strong {
            font-size: 3vw;
            margin-bottom: 0.5vw;
        }

        .sizeguide_detail_note .note-content .note-list {
            padding-left: 3vw;
            margin: 0.5vw 0 0;
        }

        .sizeguide_detail_note .note-content .note-list li {
            font-size: 2.6vw;
            margin-bottom: 0.5vw;
        }

        /* ============================================
           EMPTY STATE - MOBILE
           ============================================ */
        .sizeguide_detail_empty {
            padding: 8vw 4vw;
        }

        .sizeguide_detail_empty .empty-icon {
            font-size: 10vw;
            margin-bottom: 2vw;
        }

        .sizeguide_detail_empty h3 {
            font-size: 4vw;
            margin-bottom: 0.5vw;
        }

        .sizeguide_detail_empty p {
            font-size: 3vw;
        }
    }
</style>

<div class="sizeguide_detail_container">
    {{-- HEADER --}}
    <div class="sizeguide_detail_top_container">
        <div class="sizeguide_detail_header">
            <a href="{{ route('customer.size-guide') }}" class="back-link">
                ← Kembali ke Panduan Ukuran
            </a>
            <h1>Panduan Ukuran {{ $category->name }}</h1>
            <p>Temukan ukuran yang tepat untuk produk {{ $category->name }}</p>
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="sizeguide_detail_content_container">
        @php
            $dimensionLabels = $category->dimension_labels ?? [];
            $sizeGuides = $category->sizeGuides;
        @endphp

        @if($sizeGuides->isNotEmpty() && !empty($dimensionLabels))
            <div class="sizeguide_detail_table_wrapper">
                <div class="table-header">
                    <h2>Tabel Ukuran {{ $category->name }}</h2>
                    <span class="category-badge">{{ $sizeGuides->count() }} Ukuran</span>
                </div>
                
                <table class="sizeguide_detail_table">
                    <thead>
                        <tr>
                            <th>Ukuran</th>
                            @foreach($dimensionLabels as $label)
                                <th>{{ $label }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sizeGuides as $guide)
                            <tr>
                                <td class="size-label">
                                    {{ $guide->size }}
                                </td>
                                @foreach($dimensionLabels as $label)
                                    <td>{{ $guide->dimensions[$label] ?? '-' }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="sizeguide_detail_note">
                <div class="note-content">
                    <strong>Tips Memilih Ukuran yang Tepat</strong>
                    <ul class="note-list">
                        <li>Ukur tubuh Anda dengan pita meteran pada posisi yang tepat</li>
                        <li>Bandingkan hasil ukuran dengan tabel di atas</li>
                        <li>Pilih ukuran yang paling mendekati ukuran tubuh Anda</li>
                        <li>Jika di antara dua ukuran, pilih ukuran yang lebih besar</li>
                    </ul>
                </div>
            </div>
        @else
            <div class="sizeguide_detail_table_wrapper">
                <div class="sizeguide_detail_empty">
                    <span class="empty-icon">📏</span>
                    <h3>Belum Ada Panduan Ukuran</h3>
                    <p>Maaf, panduan ukuran untuk kategori ini belum tersedia.</p>
                </div>
            </div>
        @endif
    </div>
</div>

@endsection