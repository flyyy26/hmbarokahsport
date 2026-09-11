@extends('layouts.customer')

@section('title', 'Kontak - Barokah Sport')

@section('content')

<style>
    html {
        scroll-behavior: smooth;
    }

    /* ============================================
       CONTACT CONTAINER
       ============================================ */
    .contact_container {
        width: 100%;
        margin: 0 auto;
        border-top: 0.1vw solid #076694;
    }

    .contact-header h1 {
        font-size: 2.3vw;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        font-family: heading, sans-serif;
        text-transform: uppercase;
    }

    .contact-header p {
        font-size: 0.85vw;
        color: #94a3b8;
        margin-top: 0.2vw;
    }

    /* ============================================
       CONTACT LAYOUT
       ============================================ */
    .contact_top_container {
        width: 100%;
        padding: 1.6vw 7.54vw;
        padding-bottom: 1.9vw;
        background: #f9fafb;
    }

    .contact_layout_container {
        width: 100%;
        padding: 3vw 7.4vw;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        grid-gap: 2vw;
        align-items: center;
    }

    .contact_maps {
        width: 100%;
        height: 16vw;
        position: relative;
        border-radius: .7vw;
        overflow: hidden;
        box-shadow: 0 0.3vw 1vw rgba(0, 0, 0, 0.08);
    }

    .contact_maps iframe {
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
    }

    .contact_content_box {
        border-bottom: .1vw solid #e2e8f0;
        padding-bottom: .8vw;
    }

    .contact_content_box h3 {
        font-size: 1.4vw;
        color: #076694;
        margin-bottom: .8vw;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.5vw;
    }

    .contact_content_box_list ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .contact_content_box_list ul li {
        margin-bottom: .6vw;
        display: flex;
        align-items: center;
        gap: .8vw;
        font-size: .87vw;
        color: #475569;
        padding: 0.3vw 0;
        border-bottom: 0.05vw solid #f8fafc;
    }

    .contact_content_box_list ul li:last-child {
        border-bottom: none;
    }

    .contact_content_box_list ul li iconify-icon {
        font-size: 1.3vw;
        color: #076694;
        flex-shrink: 0;
        text-align: center;
    }

    .contact_content_medsos {
        display: flex;
        align-items: center;
        gap: 1vw;
        padding: 1.2vw .2vw;
    }

    .contact_content_medsos_box {
        width: 2.5vw;
        height: 2.5vw;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 100vw;
        border: .1vw solid #e2e8f0;
        transition: all 0.3s ease;
        background: #ffffff;
        text-decoration: none;
    }

    .contact_content_medsos_box:hover {
        background: #076694;
        border-color: #076694;
        transform: translateY(-0.15vw);
        box-shadow: 0 0.3vw 0.8vw rgba(7, 102, 148, 0.25);
    }

    .contact_content_medsos_box:hover iconify-icon {
        color: white;
    }

    .contact_content_medsos_box iconify-icon {
        font-size: 1.2vw;
        color: #475569;
        transition: color 0.3s ease;
    }

    /* ============================================
       FAQ SECTION - KONTAK
       ============================================ */
    #faq-section {
        scroll-margin-top: 5.7vw;
    }

    .faq_section {
        width: 100%;
        padding: 3vw 7.4vw 4vw 7.4vw;
        background: #f9fafb;
    }

    .faq_header {
        text-align: center;
        margin-bottom: 2vw;
    }

    .faq_header h2 {
        font-size: 2vw;
        font-weight: 700;
        color: #0f172a;
        font-family: heading, sans-serif;
        text-transform: uppercase;
    }

    .faq_header p {
        font-size: 0.85vw;
        color: #94a3b8;
        margin-top: 0.2vw;
    }

    /* FAQ Filter */
    .faq_filters {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 0.6vw;
        margin-bottom: 2vw;
    }

    .faq_filter_btn {
        padding: 0.5vw 1.5vw;
        border-radius: 100vw;
        border: 0.1vw solid #e2e8f0;
        background: white;
        color: #64748b;
        font-size: 0.8vw;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .faq_filter_btn:hover {
        background: #f1f5f9;
        border-color: #076694;
    }

    .faq_filter_btn.active {
        background: #076694;
        color: white;
        border-color: #076694;
    }

    /* FAQ Items */
    .faq_list {
        max-width: 80%;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 0.8vw;
    }

    .faq_item {
        background: white;
        border-radius: 0.7vw;
        overflow: hidden;
        box-shadow: 0 0.1vw 0.5vw rgba(0, 0, 0, 0.05);
        border: 0.1vw solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .faq_item:hover {
        box-shadow: 0 0.3vw 1vw rgba(0, 0, 0, 0.08);
    }

    .faq_question {
        width: 100%;
        padding: 1vw 1.5vw;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: white;
        border: none;
        cursor: pointer;
        font-size: 0.9vw;
        font-weight: 600;
        color: #0f172a;
        text-align: left;
        transition: all 0.3s ease;
        font-family: heading, sans-serif;
    }

    .faq_question:hover {
        background: #f8fafc;
    }

    .faq_question .faq_icon {
        font-size: 1.2vw;
        color: #076694;
        transition: transform 0.3s ease;
        flex-shrink: 0;
        margin-left: 1vw;
    }

    .faq_question .faq_icon.open {
        transform: rotate(180deg);
    }

    .faq_answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease, padding 0.3s ease;
        background: #f8fafc;
    }

    .faq_answer.active {
        max-height: 500px;
    }

    .faq_answer_content {
        padding: 1.2vw 1.5vw 1.2vw 1.5vw;
        font-size: 0.85vw;
        color: #475569;
        line-height: 1.6;
        border-top: 0.1vw solid #e2e8f0;
    }

    .faq_answer_content ul,
    .faq_answer_content ol {
        margin-left: 1vw;
        margin-bottom: 1vw;
    }

    /* Category Badge */
    .faq_category_badge {
        display: inline-block;
        padding: 0.2vw 0.8vw;
        border-radius: 100vw;
        font-size: 0.6vw;
        font-weight: 600;
        margin-right: 0.5vw;
        flex-shrink: 0;
    }

    .faq_category_badge.default {
        background: #e2e8f0;
        color: #475569;
    }
    .faq_category_badge.umum {
        background: #dbeafe;
        color: #1e40af;
    }
    .faq_category_badge.produk {
        background: #d1fae5;
        color: #065f46;
    }
    .faq_category_badge.pengiriman {
        background: #fef3c7;
        color: #92400e;
    }
    .faq_category_badge.pembayaran {
        background: #ede9fe;
        color: #5b21b6;
    }
    .faq_category_badge.garansi {
        background: #fce4ec;
        color: #b91c1c;
    }

    .faq_empty {
        text-align: center;
        padding: 3vw;
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
        .contact_top_container {
            padding: 4.5vw 4.5vw;
            background: #f9fafb;
        }

        .contact-header h1 {
            font-size: 6vw;
            position: relative;
            display: inline-block;
        }

        .contact-header p {
            font-size: 3vw;
            color: #94a3b8;
            margin-top: 0.2vw;
        }

        /* ============================================
           CONTACT LAYOUT - MOBILE
           ============================================ */
        .contact_layout_container {
            grid-template-columns: 1fr;
            padding: 4vw 4vw 6vw;
            gap: 4vw;
        }

        .contact_maps {
            height: 50vw;
            border-radius: 2vw;
            box-shadow: 0 0.5vw 2vw rgba(0, 0, 0, 0.08);
            order: 2;
        }

        .contact_content {
            order: 1;
        }

        .contact_content_box {
            border-bottom: 0.15vw solid #e2e8f0;
            padding-bottom: 1.5vw;
        }

        .contact_content_box h3 {
            font-size: 4vw;
            margin-bottom: 1.5vw;
            gap: 1vw;
        }

        .contact_content_box_list ul li {
            font-size: 3vw;
            margin-bottom: 1.2vw;
            padding: 0.8vw 0;
            gap: 1.5vw;
        }

        .contact_content_box_list ul li iconify-icon {
            font-size: 4vw;
            width: 5vw;
        }

        /* Sosial Media - Mobile */
        .contact_content_medsos {
            padding: 1.5vw 0.2vw;
            gap: 2vw;
        }

        .contact_content_medsos_box {
            width: 7vw;
            height: 7vw;
            border-radius: 100vw;
            border: 0.15vw solid #e2e8f0;
            background: #ffffff;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .contact_content_medsos_box:active {
            background: #076694;
            border-color: #076694;
            transform: scale(0.92);
        }

        .contact_content_medsos_box:active iconify-icon {
            color: white;
        }

        .contact_content_medsos_box iconify-icon {
            font-size: 3.5vw;
            color: #475569;
        }

        /* ============================================
           FAQ - MOBILE
           ============================================ */
        .faq_section {
            padding: 5vw 4vw 8vw;
        }

        .faq_header h2 {
            font-size: 5.5vw;
            margin-bottom: 1vw;
        }

        .faq_header p {
            font-size: 3vw;
        }

        .faq_filters {
            gap: 1.5vw;
            margin-bottom: 3.5vw;
        }

        .faq_filter_btn {
            padding: 1.5vw 4vw;
            font-size: 2.5vw;
            border-radius: 100vw;
            border: 0.15vw solid #e2e8f0;
        }

        .faq_filter_btn:active {
            transform: scale(0.95);
        }

        .faq_list {
            max-width: 100%;
            gap: 1.5vw;
        }

        .faq_item {
            border-radius: 1.5vw;
            border: 0.1vw solid #e8edf4;
            box-shadow: 0 0.3vw 1vw rgba(0, 0, 0, 0.04);
        }

        .faq_item:active {
            transform: scale(0.99);
        }

        .faq_question {
            font-size: 3vw;
            padding: 3.5vw 4vw;
            font-weight: 600;
            color: #0f172a;
            gap: 2vw;
        }

        .faq_question:active {
            background: #f1f5f9;
        }

        .faq_question .faq_icon {
            font-size: 4vw;
            color: #076694;
        }

        .faq_answer_content {
            font-size: 2.8vw;
            padding: 0 4vw 3.5vw 4vw;
            line-height: 1.6;
            color: #475569;
            border-top: 0.1vw solid #e8edf4;
        }

        .faq_answer_content ul,
        .faq_answer_content ol {
            margin-left: 3vw;
            margin-bottom: 1vw;
        }

        .faq_answer_content li {
            margin-bottom: 0.5vw;
        }

        .faq_category_badge {
            font-size: 2vw;
            padding: 0.5vw 2.5vw;
            border-radius: 100vw;
        }

        .faq_empty {
            padding: 4vw;
            font-size: 2.8vw;
        }
    }
</style>

<div class="contact_container">
    {{-- CONTACT HEADER --}}
    <div class="contact_top_container">
        <div class="contact-header">
            <h1>Kontak Kami</h1>
            <p>Siap membantu Anda. Hubungi kami sekarang!</p>
        </div>
    </div>

    {{-- CONTACT LAYOUT --}}
    <div class="contact_layout_container">
        <div class="contact_maps">
            @if($setting?->google_maps)
                {!! $setting->google_maps !!}
            @else
                <div style="width:100%;height:100%;background:#e2e8f0;display:flex;align-items:center;justify-content:center;color:#94a3b8;font-size:0.9vw;flex-direction:column;gap:0.5vw;">
                    <iconify-icon icon="mdi:map-marker-off" style="font-size:3vw;"></iconify-icon>
                    <span>Map belum tersedia</span>
                </div>
            @endif
        </div>
        <div class="contact_content">
            <div class="contact_content_box">
                <h3>Kantor Pusat</h3>
                <div class="contact_content_box_list">
                    <ul>
                        <li>
                            <iconify-icon icon="majesticons:map-marker"></iconify-icon>
                            {{ $setting?->address ?? 'Alamat belum diisi' }}
                        </li>
                        <li>
                            <iconify-icon icon="ic:baseline-whatsapp"></iconify-icon>
                            {{ $setting?->whatsapp ?? 'WhatsApp belum diisi' }}
                        </li>
                        <li>
                            <iconify-icon icon="ic:outline-email"></iconify-icon>
                            {{ $setting?->email ?? 'Email belum diisi' }}
                        </li>
                    </ul>
                </div>
            </div>
            <div class="contact_content_medsos">
                <a href="{{ $setting?->facebook ?? '#' }}" target="_blank" class="contact_content_medsos_box">
                    <iconify-icon icon="ic:baseline-facebook"></iconify-icon>
                </a>
                <a href="{{ $setting?->instagram ?? '#' }}" target="_blank" class="contact_content_medsos_box">
                    <iconify-icon icon="mdi:instagram"></iconify-icon>
                </a>
                <a href="{{ $setting?->tiktok ?? '#' }}" target="_blank" class="contact_content_medsos_box">
                    <iconify-icon icon="ic:baseline-tiktok"></iconify-icon>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- FAQ SECTION --}}
<div class="faq_section" id="faq-section">
    <div class="faq_header">
        <h2>Pertanyaan yang Sering Diajukan</h2>
        <p>Temukan jawaban atas pertanyaan yang sering ditanyakan pelanggan</p>
    </div>

    {{-- Category Filters dari Database --}}
    <div class="faq_filters">
        <button class="faq_filter_btn active" data-category="all">Semua</button>
        @foreach ($categories as $category)
            <button class="faq_filter_btn" data-category="{{ $category->slug }}">
                {{ $category->name }}
            </button>
        @endforeach
    </div>

    {{-- FAQ List --}}
    <div class="faq_list">
        @if ($faqs->count() > 0)
            @foreach ($faqs as $faq)
                @php
                    $categorySlug = $faq->category?->slug ?? 'umum';
                    $categoryName = $faq->category?->name ?? 'Umum';
                    $badgeClass = $categorySlug;
                @endphp
                <div class="faq_item" data-category="{{ $categorySlug }}">
                    <button class="faq_question" onclick="toggleFaq(this)">
                        <span>
                            {{ $faq->question }}
                        </span>
                        <iconify-icon class="faq_icon" icon="ic:sharp-keyboard-arrow-down"></iconify-icon>
                    </button>
                    <div class="faq_answer">
                        <div class="faq_answer_content">
                            {!! $faq->answer !!}
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="faq_empty">
                <p>Belum ada FAQ tersedia saat ini.</p>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 🔥 AMBIL FILTER DARI URL
        const urlParams = new URLSearchParams(window.location.search);
        const filterCategory = urlParams.get('category');
        
        // 🔥 CEK APAKAH ADA HASH #faq-section
        if (window.location.hash === '#faq-section') {
            setTimeout(function() {
                const faqSection = document.getElementById('faq-section');
                if (faqSection) {
                    faqSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }, 500);
        }

        // 🔥 FILTER FAQ BERDASARKAN CATEGORY DARI URL
        if (filterCategory) {
            setTimeout(function() {
                const filterButtons = document.querySelectorAll('.faq_filter_btn');
                let targetButton = null;
                
                filterButtons.forEach(function(btn) {
                    if (btn.dataset.category === filterCategory) {
                        targetButton = btn;
                    }
                });
                
                if (targetButton) {
                    targetButton.click();
                }
                
                const faqSection = document.getElementById('faq-section');
                if (faqSection) {
                    faqSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }, 600);
        }

        // Auto-open first FAQ
        const firstFaq = document.querySelector('.faq_item');
        if (firstFaq) {
            const firstButton = firstFaq.querySelector('.faq_question');
            if (firstButton) {
                setTimeout(function() {
                    toggleFaq(firstButton);
                }, 500);
            }
        }
    });

    // FAQ Toggle
    function toggleFaq(button) {
        const answer = button.nextElementSibling;
        const icon = button.querySelector('.faq_icon');
        const isActive = answer.classList.contains('active');

        // Close all other FAQs
        document.querySelectorAll('.faq_answer').forEach(function(el) {
            if (el !== answer) {
                el.classList.remove('active');
                const prevIcon = el.previousElementSibling?.querySelector('.faq_icon');
                if (prevIcon) prevIcon.classList.remove('open');
            }
        });

        // Toggle current FAQ
        if (isActive) {
            answer.classList.remove('active');
            icon.classList.remove('open');
        } else {
            answer.classList.add('active');
            icon.classList.add('open');
            
            // Scroll ke FAQ yang dibuka
            setTimeout(function() {
                button.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 100);
        }
    }

    // FAQ Filter
    document.querySelectorAll('.faq_filter_btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const category = this.dataset.category;

            // Update active button
            document.querySelectorAll('.faq_filter_btn').forEach(function(btn) {
                btn.classList.remove('active');
            });
            this.classList.add('active');

            // Filter FAQ items
            document.querySelectorAll('.faq_item').forEach(function(item) {
                if (category === 'all' || item.dataset.category === category) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });

            // Close all open FAQs when filtering
            document.querySelectorAll('.faq_answer').forEach(function(el) {
                el.classList.remove('active');
                const prevIcon = el.previousElementSibling?.querySelector('.faq_icon');
                if (prevIcon) prevIcon.classList.remove('open');
            });
        });
    });
</script>

@endsection