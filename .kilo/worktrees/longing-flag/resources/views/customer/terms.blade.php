@extends('layouts.customer')

@section('title', 'Syarat & Ketentuan - Barokah Sport')

@section('content')
<style>
    .terms-wrapper {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem 1.5rem;
    }

    .terms-header {
        text-align: center;
        padding: 2.5rem 2rem;
        background: linear-gradient(135deg, #076694 0%, #0a4a6e 100%);
        border-radius: 1rem;
        margin-bottom: 2.5rem;
        position: relative;
        overflow: hidden;
    }

    .terms-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }

    .terms-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        color: #ffffff;
        margin: 0 0 0.5rem 0;
        position: relative;
        z-index: 1;
        font-family: heading, sans-serif;
        letter-spacing: 1px;
        text-transform:uppercase;
    }

    .terms-header .subtitle {
        color: rgba(255, 255, 255, 0.8);
        font-size: 1rem;
        margin: 0;
        position: relative;
        z-index: 1;
    }

    .terms-meta {
        display: flex;
        justify-content: center;
        gap: 1.5rem;
        flex-wrap: wrap;
        margin-top: 1rem;
        position: relative;
        z-index: 1;
    }

    .terms-meta-item {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        padding: 0.4rem 1.2rem;
        border-radius: 100px;
        color: rgba(255, 255, 255, 0.9);
        font-size: 0.85rem;
    }

    .terms-meta-item iconify-icon {
        font-size: 1.2rem;
    }

    .terms-card {
        background: #ffffff;
        border-radius: 1rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        padding: 3rem;
        border: 1px solid #eef2f7;
    }

    /* Content Styling */
    .terms-content h1,
    .terms-content h2,
    .terms-content h3,
    .terms-content h4 {
        color: #0f172a;
        margin-top: 1.8rem;
        margin-bottom: 0.8rem;
    }

    .terms-content h2 {
        font-size: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #f1f5f9;
    }

    .terms-content h2:first-of-type {
        margin-top: 0;
    }

    .privacy-content h3 {
        font-size: 1rem;
        color: #076694;
    }

    .privacy-content h3 {
        font-size: .9rem;
        color: #076694;
    }
    .privacy-content h4 {
        font-size: .8rem;
        color: #076694;
    }
    .privacy-content h5 {
        font-size: .7rem;
        color: #076694;
    }

    .terms-content p {
        color: #334155;
        line-height: 1.4;
        margin-bottom: 1rem;
        font-size: 1rem;
    }

    .terms-content ul,
    .terms-content ol {
        margin: 0.8rem 0 1.2rem 1.5rem;
        color: #334155;
        line-height: 1.8;
    }

    .terms-content ul li,
    .terms-content ol li {
        margin-bottom: 0.4rem;
    }

    .terms-content ul li::marker {
        color: #076694;
    }

    .terms-content strong,
    .terms-content b {
        color: #0f172a;
    }

    .terms-content a {
        color: #076694;
        text-decoration: none;
        border-bottom: 1px dotted #076694;
        transition: all 0.3s ease;
    }

    .terms-content a:hover {
        color: #0a4a6e;
        border-bottom: 1px solid #0a4a6e;
    }

    .terms-content blockquote {
        border-left: 4px solid #076694;
        padding: 0.8rem 1.5rem;
        margin: 1rem 0;
        background: #f8fafc;
        border-radius: 0.5rem;
        color: #475569;
    }

    .terms-content table {
        width: 100%;
        border-collapse: collapse;
        margin: 1rem 0;
    }

    .terms-content table th,
    .terms-content table td {
        padding: 0.75rem 1rem;
        border: 1px solid #e2e8f0;
        text-align: left;
    }

    .terms-content table th {
        background: #f8fafc;
        font-weight: 600;
        color: #0f172a;
    }

    .terms-content table tr:nth-child(even) {
        background: #fafbfc;
    }

    .terms-footer {
        margin-top: 2.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #eef2f7;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .terms-footer-info {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        color: #94a3b8;
        font-size: 0.85rem;
    }

    .terms-footer-info span {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    .terms-footer-info iconify-icon {
        font-size: 1.1rem;
    }

    .terms-back {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #076694;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        padding: 0.5rem 1.2rem;
        border-radius: 100px;
        background: #f1f5f9;
    }

    .terms-back:hover {
        background: #e2e8f0;
        transform: translateX(-4px);
    }

    .terms-back iconify-icon {
        font-size: 1.2rem;
        transition: transform 0.3s ease;
    }

    .terms-back:hover iconify-icon {
        transform: translateX(-4px);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .terms-wrapper {
            padding: 1rem;
        }

        .terms-header {
            padding: 2rem 1.5rem;
            border-radius: 0.75rem;
        }

        .terms-header h1 {
            font-size: 1.8rem;
        }

        .terms-header .subtitle {
            font-size: 0.9rem;
        }

        .terms-meta {
            gap: 0.8rem;
        }

        .terms-meta-item {
            font-size: 0.75rem;
            padding: 0.3rem 0.8rem;
        }

        .terms-card {
            padding: 1.5rem;
        }

        .terms-content h2 {
            font-size: 1.3rem;
        }

        .terms-content p {
            font-size: 0.95rem;
        }

        .terms-content ul,
        .terms-content ol {
            margin-left: 1rem;
        }

        .terms-footer {
            flex-direction: column-reverse;
            align-items: stretch;
            text-align: center;
        }

        .terms-footer-info {
            justify-content: center;
            flex-wrap: wrap;
            gap: 0.8rem;
        }

        .terms-back {
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .terms-header h1 {
            font-size: 1.5rem;
        }

        .terms-card {
            padding: 1rem;
        }

        .terms-meta-item {
            font-size: 0.7rem;
            padding: 0.2rem 0.6rem;
        }
    }
</style>

<div class="terms-wrapper">
    {{-- HEADER --}}
    <div class="terms-header">
        <h1>{{ $term?->title ?? 'Syarat & Ketentuan' }}</h1>
        <p class="subtitle">Dengan menggunakan layanan Barokah Sport, Anda menyetujui syarat dan ketentuan yang berlaku</p>
        
        @if($term)
            <div class="terms-meta">
                @if($term->effective_date)
                    <span class="terms-meta-item">
                        <iconify-icon icon="mdi:calendar-today"></iconify-icon>
                        Efektif {{ $term->effective_date->format('d M Y') }}
                    </span>
                @endif
                <span class="terms-meta-item">
                    <iconify-icon icon="mdi:clock-outline"></iconify-icon>
                    Terakhir diperbarui {{ $term?->updated_at?->format('d M Y') ?? 'Belum diperbarui' }}
                </span>
            </div>
        @endif
    </div>

    {{-- CONTENT --}}
    <div class="terms-card">
        @if($term && $term->content)
            <div class="terms-content">
                {!! $term->content !!}
            </div>

            <div class="terms-footer">
                <div class="terms-footer-info">
                    <span>
                        <iconify-icon icon="mdi:check-circle-outline"></iconify-icon>
                        Berlaku sejak {{ $term->effective_date?->format('d M Y') ?? '-' }}
                    </span>
                </div>
            </div>
        @else
            <div class="text-center py-8">
                <iconify-icon icon="mdi:file-document-outline" style="font-size: 4rem; color: #94a3b8; margin-bottom: 1rem; display: block;"></iconify-icon>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Syarat & Ketentuan</h2>
                <p class="text-gray-500">Syarat & ketentuan sedang dalam proses pembuatan.</p>
            </div>
        @endif
    </div>
</div>
@endsection