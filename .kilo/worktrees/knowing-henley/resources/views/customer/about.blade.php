@extends('layouts.customer')

@section('title', $about->title . ' - Barokah Sport')

@section('content')

<style>
    html {
        scroll-behavior: smooth;
    }

    .about_container {
        width: 100%;
        margin: 0 auto;
        border-top: 0.1vw solid #076694;
    }

    .about_top_container {
        width: 100%;
        padding: 1.3vw 7.54vw;
        padding-bottom: 1.8vw;
        background: #f9fafb;
    }

    .about-header h1 {
        font-size: 2.3vw;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        font-family: heading, sans-serif;
        text-transform: uppercase;
    }

    .about-header p {
        font-size: 0.85vw;
        color: #94a3b8;
        margin-top: 0.2vw;
    }

    .about_content_container {
        width: 100%;
        padding: 3vw 7.4vw;
        display: grid;
        grid-template-columns: 60% 40%;
        align-items: start;
        gap: 2.5vw;
    }

    /* ============================================
       MAIN CONTENT
       ============================================ */
    .about_main_content {
        background: white;
        border-radius: 0.7vw;
        padding: 2vw 2.5vw;
        box-shadow: 0 0.1vw 0.5vw rgba(0, 0, 0, 0.05);
        border: 0.1vw solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .about_main_content:hover {
        box-shadow: 0 0.3vw 1.5vw rgba(0, 0, 0, 0.08);
    }

    .about_main_content .content {
        font-size: 0.9vw;
        line-height: 1.5;
        color: #334155;
    }

    .about_main_content .content h2,
    .about_main_content .content h3 {
        color: #076694;
        margin-top: 1.5vw;
        margin-bottom: 0.5vw;
    }

    .about_main_content .content h1 {
        color: #076694;
        font-size: 1.8vw;
        margin-top: 1.5vw;
        margin-bottom: 0.5vw;
    }

    .about_main_content .content p {
        margin-bottom: .7vw;
    }

    .about_main_content .content ul,
    .about_main_content .content ol {
        padding-left: 1.5vw;
        margin-bottom: 1vw;
        list-style-position: outside;
    }

    .about_main_content .content ul {
        list-style-type: disc;
    }

    .about_main_content .content ol {
        list-style-type: decimal;
    }

    .about_main_content .content li {
        margin-bottom: 0.3vw;
        list-style: inherit;
    }

    .about_main_content .content strong {
        color: #0f172a;
    }

    .about_main_content .content blockquote {
        border-left: 0.3vw solid #076694;
        padding: 0.8vw 1.2vw;
        margin: 1vw 0;
        background: #f8fafc;
        border-radius: 0 0.4vw 0.4vw 0;
        font-style: italic;
        color: #475569;
        font-size: 0.9vw;
    }

    .about_main_content .content img {
        max-width: 100%;
        border-radius: 0.5vw;
        margin: 0.8vw 0;
    }

    /* ============================================
       SIDEBAR
       ============================================ */
    .about_sidebar {
        display: flex;
        flex-direction: column;
        gap: 1.5vw;
        padding-left: 0;
    }

    .about_sidebar_box {
        background: white;
        border-radius: 0.7vw;
        padding: 1.8vw 2vw;
        box-shadow: 0 0.1vw 0.5vw rgba(0, 0, 0, 0.05);
        border: 0.1vw solid #e2e8f0;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .about_sidebar_box::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 0.4vw;
        height: 100%;
        background: #076694;
        border-radius: 0.2vw 0 0 0.2vw;
    }

    .about_sidebar_box:hover {
        box-shadow: 0 0.3vw 1.5vw rgba(0, 0, 0, 0.08);
        transform: translateY(-0.15vw);
    }

    .about_sidebar_box h3 {
        font-size: 1.2vw;
        letter-spacing: 0.03vw;
        color: #076694;
        margin-bottom: 0.8vw;
        text-transform: uppercase;
        padding-bottom: 0.6vw;
        border-bottom: 0.15vw solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 0.5vw;
    }

    .about_sidebar_box h3 .icon {
        font-size: 1.4vw;
    }

    .about_sidebar_box .vision-content,
    .about_sidebar_box .mission-content {
        font-size: 0.85vw;
        color: #475569;
        line-height: 1.5;
    }

    .about_sidebar_box .vision-content p,
    .about_sidebar_box .mission-content p {
        margin-bottom: 0.5vw;
    }

    .about_sidebar_box .vision-content ul,
    .about_sidebar_box .mission-content ul,
    .about_sidebar_box .mission-content ol {
        margin-left: 1.2vw;
        margin-top: 0.5vw;
        list-style-position: outside;
    }

    .about_sidebar_box .vision-content ul,
    .about_sidebar_box .mission-content ul {
        list-style-type: disc;
    }

    .about_sidebar_box .mission-content ol {
        list-style-type: decimal;
    }

    .about_sidebar_box .vision-content ul li,
    .about_sidebar_box .mission-content ul li {
        margin-bottom: 0.3vw;
        font-size: 0.85vw;
        color: #475569;
        list-style: inherit;
    }

    .about_sidebar_box ul li {
        padding: 0.6vw 0;
        border-bottom: 0.05vw solid #f1f5f9;
        font-size: 0.85vw;
        color: #475569;
        display: list-item;
        list-style-type: disc;
        list-style-position: outside;
        margin-left: 1.2vw;
        gap: 0.6vw;
    }

    .about_sidebar_box ul li:last-child {
        border-bottom: none;
    }

    .about_sidebar_box ul li iconify-icon {
        color: #076694;
        font-size: 1.2vw;
        flex-shrink: 0;
    }

    /* ============================================
       🔥 RESPONSIVE MOBILE (max-width: 768px)
       ============================================ */
    @media (max-width: 768px) {
        /* ============================================
           HEADER - MOBILE
           ============================================ */
        .about_top_container {
            padding: 4.5vw 4.5vw;
            background: #f9fafb;
        }

        .about-header h1 {
            font-size: 6vw;
            position: relative;
            display: inline-block;
        }

        .about-header p {
            font-size: 3vw;
            margin-top: 0.2vw;
            color: #64748b;
        }

        /* ============================================
           CONTENT - MOBILE
           ============================================ */
        .about_content_container {
            grid-template-columns: 1fr;
            padding: 4vw 4vw;
            gap: 4vw;
        }

        /* ============================================
           MAIN CONTENT - MOBILE
           ============================================ */
        .about_main_content {
            padding: 4vw 4.5vw;
            border-radius: 2vw;
            border: 0.15vw solid #e8edf4;
        }

        .about_main_content .content {
            font-size: 3vw;
            line-height: 1.7;
            color: #334155;
        }

        .about_main_content .content h1 {
            font-size: 4.5vw;
            margin-top: 3vw;
            margin-bottom: 1.5vw;
        }

        .about_main_content .content h2 {
            font-size: 4vw;
            margin-top: 3vw;
            margin-bottom: 1.5vw;
        }

        .about_main_content .content h3 {
            font-size: 3.5vw;
            margin-top: 2.5vw;
            margin-bottom: 1.2vw;
        }

        .about_main_content .content p {
            font-size: 3vw;
            margin-bottom: 1.5vw;
        }

        .about_main_content .content ul,
        .about_main_content .content ol {
            padding-left: 4vw;
            margin-bottom: 1.5vw;
        }

        .about_main_content .content li {
            font-size: 3vw;
            margin-bottom: 0.8vw;
        }

        .about_main_content .content blockquote {
            border-left: 0.8vw solid #076694;
            padding: 2vw 3vw;
            margin: 2vw 0;
            font-size: 3vw;
            border-radius: 0 1.5vw 1.5vw 0;
        }

        .about_main_content .content img {
            border-radius: 1.5vw;
            margin: 1.5vw 0;
        }

        /* ============================================
           SIDEBAR - MOBILE
           ============================================ */
        .about_sidebar {
            padding-left: 0;
            gap: 3vw;
        }

        .about_sidebar_box {
            padding: 4vw 4.5vw;
            border-radius: 2vw;
            border: 0.15vw solid #e8edf4;
        }

        .about_sidebar_box::before {
            width: 0.8vw;
            border-radius: 0.4vw 0 0 0.4vw;
        }

        .about_sidebar_box:active {
            transform: scale(0.99);
        }

        .about_sidebar_box h3 {
            font-size: 3.5vw;
            margin-bottom: 1.5vw;
            padding-bottom: 1.2vw;
            border-bottom: 0.15vw solid #e8edf4;
            gap: 1vw;
        }

        .about_sidebar_box h3 .icon {
            font-size: 4vw;
        }

        .about_sidebar_box .vision-content,
        .about_sidebar_box .mission-content {
            font-size: 2.8vw;
            line-height: 1.7;
        }

        .about_sidebar_box .vision-content p,
        .about_sidebar_box .mission-content p {
            font-size: 2.8vw;
            margin-bottom: 1vw;
        }

        .about_sidebar_box .vision-content ul,
        .about_sidebar_box .mission-content ul {
            margin-left: 3vw;
        }

        .about_sidebar_box .vision-content ul li,
        .about_sidebar_box .mission-content ul li {
            font-size: 2.8vw;
            margin-bottom: 0.6vw;
        }

        .about_sidebar_box ul li {
            font-size: 2.8vw;
            padding: 1.2vw 0;
            gap: 1.5vw;
        }

        .about_sidebar_box ul li iconify-icon {
            font-size: 3.5vw;
        }
    }
</style>

<div class="about_container">
    {{-- HEADER --}}
    <div class="about_top_container">
        <div class="about-header">
            <h1>{{ $about->title }}</h1>
            <p>Mengetahui lebih dalam tentang perjalanan dan komitmen kami</p>
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="about_content_container">
        {{-- MAIN CONTENT --}}
        <div class="about_main_content">
            <div class="content">
                {!! $about->content !!}
            </div>
        </div>

        {{-- SIDEBAR --}}
        <div class="about_sidebar">
            {{-- VISI --}}
            @if($about->vision)
                <div class="about_sidebar_box">
                    <h3>
                        <span class="icon">🎯</span>
                        Visi Kami
                    </h3>
                    <div class="vision-content">
                        {!! $about->vision !!}
                    </div>
                </div>
            @endif

            {{-- MISI --}}
            @if($about->mission)
                <div class="about_sidebar_box">
                    <h3>
                        <span class="icon">🚀</span>
                        Misi Kami
                    </h3>
                    <div class="mission-content">
                        {!! $about->mission !!}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection