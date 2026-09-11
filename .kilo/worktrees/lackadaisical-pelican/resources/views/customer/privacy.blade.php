@extends('layouts.customer')

@section('title', 'Kebijakan Privasi - Barokah Sport')

@section('content')
<style>
    .privacy-wrapper {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem 1.5rem;
    }

    .privacy-header {
        text-align: center;
        padding: 2.5rem 2rem;
        background: linear-gradient(135deg, #076694 0%, #0a4a6e 100%);
        border-radius: 1rem;
        margin-bottom: 2.5rem;
        position: relative;
        overflow: hidden;
    }

    .privacy-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }

    .privacy-header h1 {
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

    .privacy-header .subtitle {
        color: rgba(255, 255, 255, 0.8);
        font-size: 1rem;
        margin: 0;
        position: relative;
        z-index: 1;
    }

    .privacy-meta {
        display: flex;
        justify-content: center;
        gap: 1.5rem;
        flex-wrap: wrap;
        margin-top: 1rem;
        position: relative;
        z-index: 1;
    }

    .privacy-meta-item {
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

    .privacy-meta-item iconify-icon {
        font-size: 1.2rem;
    }

    .privacy-card {
        background: #ffffff;
        border-radius: 1rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        padding: 3rem;
        border: 1px solid #eef2f7;
    }

    .privacy-content h1,
    .privacy-content h2,
    .privacy-content h3,
    .privacy-content h4 {
        color: #0f172a;
        margin-top: 1.8rem;
        margin-bottom: 0.8rem;
    }

    .privacy-content h2 {
        font-size: 1.2rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #f1f5f9;
    }

    .privacy-content h2:first-of-type {
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

    .privacy-content p {
        color: #334155;
        line-height: 1.3;
        margin-bottom: .5rem;
        font-size: 1rem;
    }

    .privacy-content ul,
    .privacy-content ol {
        margin: 0.8rem 0 1.2rem 1.5rem;
        color: #334155;
        line-height: 1.8;
    }

    .privacy-content ul li,
    .privacy-content ol li {
        margin-bottom: 0.4rem;
    }

    .privacy-content ul li::marker {
        color: #076694;
    }

    .privacy-content strong,
    .privacy-content b {
        color: #0f172a;
    }

    .privacy-content a {
        color: #076694;
        text-decoration: none;
        border-bottom: 1px dotted #076694;
        transition: all 0.3s ease;
    }

    .privacy-content a:hover {
        color: #0a4a6e;
        border-bottom: 1px solid #0a4a6e;
    }

    .privacy-content blockquote {
        border-left: 4px solid #076694;
        padding: 0.8rem 1.5rem;
        margin: 1rem 0;
        background: #f8fafc;
        border-radius: 0.5rem;
        color: #475569;
    }

    .privacy-footer {
        margin-top: 2.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #eef2f7;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .privacy-footer-info {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        color: #94a3b8;
        font-size: 0.85rem;
    }

    .privacy-footer-info span {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    .privacy-footer-info iconify-icon {
        font-size: 1.1rem;
    }

    .privacy-back {
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

    .privacy-back:hover {
        background: #e2e8f0;
        transform: translateX(-4px);
    }

    .privacy-back iconify-icon {
        font-size: 1.2rem;
        transition: transform 0.3s ease;
    }

    .privacy-back:hover iconify-icon {
        transform: translateX(-4px);
    }

    @media (max-width: 768px) {
        .privacy-wrapper {
            padding: 1rem;
        }

        .privacy-header {
            padding: 2rem 1.5rem;
            border-radius: 0.75rem;
        }

        .privacy-header h1 {
            font-size: 1.8rem;
        }

        .privacy-header .subtitle {
            font-size: 0.9rem;
        }

        .privacy-meta {
            gap: 0.8rem;
        }

        .privacy-meta-item {
            font-size: 0.75rem;
            padding: 0.3rem 0.8rem;
        }

        .privacy-card {
            padding: 1.5rem;
        }

        .privacy-content h2 {
            font-size: 1rem;
        }

        .privacy-content h3 {
            font-size: .9rem;
            color: #076694;
        }

        .privacy-content h3 {
            font-size: .8rem;
            color: #076694;
        }
        .privacy-content h4 {
            font-size: .7rem;
            color: #076694;
        }
        .privacy-content h5 {
            font-size: .6rem;
            color: #076694;
        }

        .privacy-content p {
            font-size: 0.95rem;
        }

        .privacy-footer {
            flex-direction: column-reverse;
            align-items: stretch;
            text-align: center;
        }

        .privacy-footer-info {
            justify-content: center;
            flex-wrap: wrap;
            gap: 0.8rem;
        }

        .privacy-back {
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .privacy-header h1 {
            font-size: 1.5rem;
        }

        .privacy-card {
            padding: 1rem;
        }

        .privacy-meta-item {
            font-size: 0.7rem;
            padding: 0.2rem 0.6rem;
        }
    }
</style>

<div class="privacy-wrapper">
    {{-- HEADER --}}
    <div class="privacy-header">
        <h1>{{ $privacy?->title ?? 'Kebijakan Privasi' }}</h1>
        <p class="subtitle">Kami berkomitmen melindungi data pribadi Anda</p>
        
        @if($privacy)
            <div class="privacy-meta">
                @if($privacy->version)
                    <span class="privacy-meta-item">
                        <iconify-icon icon="mdi:tag-outline"></iconify-icon>
                        Versi {{ $privacy->version }}
                    </span>
                @endif
                @if($privacy->effective_date)
                    <span class="privacy-meta-item">
                        <iconify-icon icon="mdi:calendar-today"></iconify-icon>
                        Efektif {{ $privacy->effective_date->format('d M Y') }}
                    </span>
                @endif
                <span class="privacy-meta-item">
                    <iconify-icon icon="mdi:clock-outline"></iconify-icon>
                    Terakhir diperbarui {{ $privacy?->updated_at?->format('d M Y') ?? 'Belum diperbarui' }}
                </span>
            </div>
        @endif
    </div>

    {{-- CONTENT --}}
    <div class="privacy-card">
        @if($privacy && $privacy->content)
            <div class="privacy-content">
                {!! $privacy->content !!}
            </div>

            <div class="privacy-footer">
                <div class="privacy-footer-info">
                    <span>
                        <iconify-icon icon="mdi:check-circle-outline"></iconify-icon>
                        Berlaku sejak {{ $privacy->effective_date?->format('d M Y') ?? '-' }}
                    </span>
                    <span>
                        <iconify-icon icon="mdi:tag-outline"></iconify-icon>
                        Versi {{ $privacy->version ?? '1.0' }}
                    </span>
                </div>
            </div>
        @else
            <div class="text-center py-8">
                <iconify-icon icon="mdi:shield-outline" style="font-size: 4rem; color: #94a3b8; margin-bottom: 1rem; display: block;"></iconify-icon>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Kebijakan Privasi</h2>
                <p class="text-gray-500">Kebijakan privasi sedang dalam proses pembuatan.</p>
            </div>
        @endif
    </div>
</div>
@endsection