@extends('layouts.customer')

@section('title', $career->title . ' - Karir Barokah Sport')

@section('content')

<style>
    .cd-wrapper {
        width: 100%;
        padding: 3vw 15vw;
        background: #f8fafc;
    }

    .cd-back {
        display: inline-flex;
        align-items: center;
        gap: 0.4vw;
        font-size: 0.85vw;
        color: #64748b;
        text-decoration: none;
        margin-bottom: 1.5vw;
        transition: color 0.2s ease;
    }
    .cd-back:hover { color: rgb(102, 72, 9); }
    .cd-back iconify-icon { font-size: 1.1vw; }

    .cd-layout {
        display: grid;
        grid-template-columns: 1fr 22vw;
        gap: 2vw;
        align-items: start;
    }

    .cd-main {
        display: flex;
        flex-direction: column;
        gap: 1.2vw;
    }

    /* HEADER CARD */
    .cd-header {
        background: linear-gradient(135deg, #FDDD57 0%, #ecbc42 50%, #d4a72e 100%);
        border-radius: 1.2vw;
        padding: 2vw;
        position: relative;
        overflow: hidden;
    }

    .cd-header::before {
        content: '';
        position: absolute;
        top: -3vw;
        right: -3vw;
        width: 12vw;
        height: 12vw;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.15);
    }

    .cd-header-inner {
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 1.2vw;
    }

    .cd-header-icon {
        width: 4vw;
        height: 4vw;
        border-radius: 1vw;
        background: rgba(255, 255, 255, 0.4);
        color: rgb(102, 72, 9);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        backdrop-filter: blur(0.3vw);
    }

    .cd-header-icon iconify-icon { font-size: 2vw; }

    .cd-header-info { flex: 1; min-width: 0; }

    .cd-header-title {
        font-size: 1.7vw;
        font-weight: 800;
        color: rgb(102, 72, 9);
        line-height: 1.25;
    }

    .cd-header-sub {
        font-size: 0.85vw;
        color: rgba(102, 72, 9, 0.8);
        margin-top: 0.4vw;
        font-weight: 500;
    }

    .cd-header-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5vw;
        margin-top: 1vw;
    }

    .cd-header-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.3vw;
        padding: 0.4vw 0.9vw;
        background: rgba(255, 255, 255, 0.5);
        border-radius: 100vw;
        font-size: 0.72vw;
        font-weight: 700;
        color: rgb(102, 72, 9);
        backdrop-filter: blur(0.3vw);
    }

    .cd-header-tag iconify-icon { font-size: 0.85vw; }

    /* SECTION */
    .cd-section {
        background: #ffffff;
        border: 0.1vw solid #e2e8f0;
        border-radius: 1vw;
        overflow: hidden;
    }

    .cd-section-head {
        padding: 1vw 1.3vw;
        border-bottom: 0.1vw solid #f1f5f9;
        background: #fafbfc;
        display: flex;
        align-items: center;
        gap: 0.5vw;
        font-size: 0.85vw;
        font-weight: 700;
        color: #0f172a;
    }

    .cd-section-head iconify-icon {
        font-size: 1.1vw;
        color: #ecbc42;
    }

    .cd-section-body {
        padding: 1.3vw;
        font-size: 0.85vw;
        color: #334155;
        line-height: 1.75;
    }

    .cd-section-body h2 { font-size: 1.1vw; margin: 1vw 0 0.5vw; }
    .cd-section-body h3 { font-size: 0.95vw; margin: 0.8vw 0 0.4vw; }
    .cd-section-body p  { margin-bottom: 0.7vw; }
    .cd-section-body ul,
    .cd-section-body ol { padding-left: 1.3vw; margin: 0.5vw 0; }
    .cd-section-body li { margin-bottom: 0.3vw; }

    /* SIDEBAR */
    .cd-sidebar {
        display: flex;
        flex-direction: column;
        gap: 1vw;
        position: sticky;
        top: 8vw;
    }

    .cd-summary {
        background: #ffffff;
        border: 0.1vw solid #e2e8f0;
        border-radius: 1vw;
        padding: 1.2vw;
    }

    .cd-summary-title {
        font-size: 0.85vw;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.9vw;
        padding-bottom: 0.6vw;
        border-bottom: 0.15vw solid #ecbc42;
    }

    .cd-summary-item {
        display: flex;
        align-items: flex-start;
        gap: 0.6vw;
        padding: 0.6vw 0;
        border-bottom: 0.05vw solid #f1f5f9;
    }
    .cd-summary-item:last-child { border-bottom: none; }

    .cd-summary-item iconify-icon {
        font-size: 1.1vw;
        color: #ecbc42;
        flex-shrink: 0;
        margin-top: 0.1vw;
    }

    .cd-summary-item-info { min-width: 0; flex: 1; }

    .cd-summary-item-label {
        font-size: 0.68vw;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 600;
    }

    .cd-summary-item-value {
        font-size: 0.82vw;
        font-weight: 700;
        color: #0f172a;
        margin-top: 0.15vw;
    }

    /* CTA */
    .cd-cta {
        background: linear-gradient(135deg, #fffbf0 0%, #fff7e0 100%);
        border: 0.15vw solid #fde68a;
        border-radius: 1vw;
        padding: 1.2vw;
        text-align: center;
    }

    .cd-cta-title {
        font-size: 0.85vw;
        font-weight: 700;
        color: rgb(102, 72, 9);
        margin-bottom: 0.3vw;
    }

    .cd-cta-desc {
        font-size: 0.72vw;
        color: #64748b;
        line-height: 1.5;
        margin-bottom: 0.9vw;
    }

    .cd-cta-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4vw;
        width: 100%;
        padding: 0.9vw 1.2vw;
        border-radius: 0.6vw;
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
        color: rgb(102, 72, 9);
        font-size: 0.82vw;
        font-weight: 700;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s ease;
        font-family: inherit;
    }

    .cd-cta-btn:hover {
        box-shadow: 0 0.4vw 1.2vw rgba(236, 188, 66, 0.45);
        transform: translateY(-0.1vw);
    }

    .cd-cta-btn iconify-icon { font-size: 1vw; }

    .cd-cta-btn.disabled {
        background: #e2e8f0;
        color: #94a3b8;
        cursor: not-allowed;
        box-shadow: none;
        transform: none;
    }

    /* MODAL APPLY */
    .cd-modal {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(0.3vw);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 2vw;
    }

    .cd-modal.active { display: flex; }

    .cd-modal-box {
        background: #ffffff;
        border-radius: 1.2vw;
        padding: 0;
        width: 100%;
        max-width: 40vw;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        animation: cdSlideUp 0.3s ease;
    }

    @keyframes cdSlideUp {
        from { opacity: 0; transform: translateY(2vw) scale(0.96); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    .cd-modal-head {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.5vw 2vw;
        border-bottom: 0.1vw solid #f1f5f9;
        background: #ffffff;
        position: sticky;
        top: 0;
        z-index: 2;
    }

    .cd-modal-title {
        font-size: 1.2vw;
        font-weight: 800;
        color: #0f172a;
    }

    .cd-modal-close {
        width: 2vw;
        height: 2vw;
        border-radius: 50%;
        background: #f1f5f9;
        border: none;
        color: #64748b;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }
    .cd-modal-close:hover { background: #e2e8f0; color: #0f172a; }
    .cd-modal-close iconify-icon { font-size: 1vw; }

    .cd-modal-body {
        flex: 1;
        overflow-y: auto;
        padding: 1.5vw 2vw;
        -webkit-overflow-scrolling: touch;
    }

    .cd-form-group { margin-bottom: 1vw; }

    .cd-form-label {
        display: block;
        font-size: 0.78vw;
        font-weight: 600;
        color: #334155;
        margin-bottom: 0.4vw;
    }

    .cd-form-input,
    .cd-form-textarea,
    .cd-form-file {
        width: 100%;
        padding: 0.75vw 1vw;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.6vw;
        font-size: 0.82vw;
        color: #0f172a;
        background: #ffffff;
        outline: none;
        font-family: inherit;
        transition: all 0.2s ease;
    }

    .cd-form-input:focus,
    .cd-form-textarea:focus {
        border-color: #ecbc42;
        box-shadow: 0 0 0 0.25vw rgba(236, 188, 66, 0.15);
    }

    .cd-form-textarea { resize: vertical; min-height: 5vw; }

    .cd-form-file {
        padding: 0.5vw;
        font-size: 0.75vw;
    }
    .cd-form-file::file-selector-button {
        padding: 0.5vw 1vw;
        margin-right: 0.8vw;
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 100%);
        color: rgb(102, 72, 9);
        border: none;
        border-radius: 0.4vw;
        font-size: 0.75vw;
        font-weight: 700;
        cursor: pointer;
        font-family: inherit;
    }

    .cd-form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.8vw;
    }

    .cd-form-hint {
        font-size: 0.68vw;
        color: #94a3b8;
        margin-top: 0.3vw;
    }

    .cd-modal-actions {
        flex-shrink: 0;
        display: flex;
        gap: 0.6vw;
        padding: 1.2vw 2vw;
        border-top: 0.1vw solid #f1f5f9;
        background: #ffffff;
        position: sticky;
        bottom: 0;
        z-index: 2;
    }

    .cd-modal-actions button {
        flex: 1;
        padding: 0.85vw 1.2vw;
        border-radius: 0.6vw;
        font-size: 0.82vw;
        font-weight: 700;
        cursor: pointer;
        border: none;
        font-family: inherit;
        transition: all 0.2s ease;
    }

    .cd-modal-actions .btn-cancel {
        background: #f1f5f9;
        color: #64748b;
        flex: 0.5;
    }
    .cd-modal-actions .btn-cancel:hover { background: #e2e8f0; }

    .cd-modal-actions .btn-submit {
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 100%);
        color: rgb(102, 72, 9);
        box-shadow: 0 0.2vw 0.6vw rgba(236, 188, 66, 0.35);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4vw;
    }
    .cd-modal-actions .btn-submit:hover {
        box-shadow: 0 0.4vw 1.2vw rgba(236, 188, 66, 0.5);
        transform: translateY(-0.1vw);
    }

    .cd-modal-actions .btn-submit iconify-icon { font-size: 1vw; }

    /* Sticky CTA (default hidden, muncul di mobile) */
    .cd-sticky-cta {
        display: none;
    }

    /* ============================================
       RESPONSIVE (≤ 768px)
       ============================================ */
    @media (max-width: 768px) {
        .cd-wrapper {
            padding: 5vw 4vw;
            padding-bottom: 24vw; /* ruang untuk sticky bar */
        }

        .cd-back {
            font-size: 3.2vw;
            gap: 1.5vw;
            margin-bottom: 4vw;
        }
        .cd-back iconify-icon { font-size: 4vw; }

        .cd-layout {
            grid-template-columns: 1fr;
            gap: 4vw;
        }

        .cd-main { gap: 3vw; }

        .cd-header {
            border-radius: 3vw;
            padding: 5vw 4vw;
        }
        .cd-header::before {
            top: -8vw;
            right: -8vw;
            width: 30vw;
            height: 30vw;
        }

        .cd-header-inner {
            flex-direction: column;
            gap: 3vw;
        }

        .cd-header-icon {
            width: 14vw;
            height: 14vw;
            border-radius: 3vw;
        }
        .cd-header-icon iconify-icon { font-size: 7vw; }

        .cd-header-title { font-size: 5.5vw; line-height: 1.25; }
        .cd-header-sub { font-size: 3.2vw; margin-top: 1.5vw; }

        .cd-header-tags { gap: 2vw; margin-top: 3vw; }

        .cd-header-tag {
            padding: 1.2vw 2.8vw;
            font-size: 2.8vw;
            gap: 1vw;
        }
        .cd-header-tag iconify-icon { font-size: 3.2vw; }

        .cd-section { border-radius: 3vw; }

        .cd-section-head {
            padding: 3.5vw 4vw;
            font-size: 3.5vw;
            gap: 2vw;
        }
        .cd-section-head iconify-icon { font-size: 4.2vw; }

        .cd-section-body {
            padding: 4vw;
            font-size: 3.2vw;
            line-height: 1.75;
        }
        .cd-section-body h2 { font-size: 4.2vw; margin: 3vw 0 1.5vw; }
        .cd-section-body h3 { font-size: 3.8vw; margin: 2.5vw 0 1.2vw; }
        .cd-section-body p  { margin-bottom: 2.5vw; }
        .cd-section-body ul,
        .cd-section-body ol { padding-left: 5vw; margin: 2vw 0; }
        .cd-section-body li { margin-bottom: 1.2vw; }

        .cd-sidebar {
            position: static;
            gap: 3vw;
        }

        .cd-summary {
            border-radius: 3vw;
            padding: 4vw;
        }

        .cd-summary-title {
            font-size: 3.5vw;
            margin-bottom: 2.5vw;
            padding-bottom: 2vw;
        }

        .cd-summary-item {
            gap: 2vw;
            padding: 2vw 0;
        }
        .cd-summary-item iconify-icon { font-size: 4.5vw; }
        .cd-summary-item-label { font-size: 2.8vw; }
        .cd-summary-item-value { font-size: 3.4vw; margin-top: 0.8vw; }

        /* Sidebar CTA disembunyikan di mobile */
        .cd-cta {
            display: none;
        }

        /* ============================================ */
        /* 🔥 STICKY CTA MOBILE */
        /* ============================================ */
        .cd-sticky-cta {
            display: flex;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 9000;
            background: #ffffff;
            border-top: 0.3vw solid #ecbc42;
            padding: 3vw 4vw;
            padding-bottom: calc(3vw + env(safe-area-inset-bottom, 0px));
            gap: 3vw;
            align-items: center;
            box-shadow: 0 -1vw 3vw rgba(0, 0, 0, 0.08);
            transform: translateY(0);
            transition: transform 0.3s ease;
        }

        .cd-sticky-cta.hidden {
            transform: translateY(100%);
        }

        .cd-sticky-cta-info {
            flex: 1;
            min-width: 0;
        }

        .cd-sticky-cta-title {
            font-size: 3.2vw;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .cd-sticky-cta-sub {
            font-size: 2.6vw;
            color: #94a3b8;
            margin-top: 0.5vw;
            display: flex;
            align-items: center;
            gap: 1.5vw;
        }

        .cd-sticky-cta-sub span {
            display: inline-flex;
            align-items: center;
            gap: 0.8vw;
        }

        .cd-sticky-cta-sub iconify-icon {
            font-size: 3vw;
            color: #ecbc42;
        }

        .cd-sticky-cta-btn {
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 1.2vw;
            padding: 3.2vw 5vw;
            background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
            color: rgb(102, 72, 9);
            border: none;
            border-radius: 2.5vw;
            font-size: 3.4vw;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            box-shadow: 0 1vw 3vw rgba(236, 188, 66, 0.4);
            cursor: pointer;
            font-family: inherit;
            white-space: nowrap;
            transition: all 0.2s ease;
        }

        .cd-sticky-cta-btn:active {
            transform: scale(0.96);
        }

        .cd-sticky-cta-btn iconify-icon {
            font-size: 4vw;
        }

        /* ============================================ */
        /* 🔥 MODAL REDESIGN — BOTTOM SHEET */
        /* ============================================ */
        .cd-modal {
            padding: 0;
            align-items: flex-end;
        }

        .cd-modal-box {
            max-width: 100%;
            width: 100%;
            max-height: 92vh;
            border-radius: 5vw 5vw 0 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            animation: cdSlideUpMobile 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes cdSlideUpMobile {
            from { transform: translateY(100%); }
            to   { transform: translateY(0); }
        }

        /* Header sticky */
        .cd-modal-head {
            padding: 4vw 5vw;
            padding-bottom: 3vw;
            margin-bottom: 0;
            border-bottom: 0.3vw solid #f1f5f9;
            background: #ffffff;
            position: sticky;
            top: 0;
            z-index: 2;
        }

        .cd-modal-title { font-size: 4.5vw; }

        .cd-modal-close {
            width: 8vw;
            height: 8vw;
        }
        .cd-modal-close iconify-icon { font-size: 4vw; }

        /* Body scrollable */
        .cd-modal-body {
            padding: 4vw 5vw;
        }

        /* Footer sticky */
        .cd-modal-actions {
            flex-direction: row;
            gap: 2.5vw;
            padding: 3.5vw 5vw;
            padding-bottom: calc(3.5vw + env(safe-area-inset-bottom, 0px));
            border-top: 0.3vw solid #f1f5f9;
            background: #ffffff;
            position: sticky;
            bottom: 0;
            z-index: 2;
        }

        .cd-modal-actions button {
            padding: 3.5vw 4vw;
            border-radius: 2.5vw;
            font-size: 3.4vw;
        }

        .cd-modal-actions .btn-cancel {
            flex: 0.5;
        }

        .cd-modal-actions .btn-submit {
            flex: 1;
        }

        .cd-modal-actions .btn-submit iconify-icon { font-size: 4vw; }

        /* Form */
        .cd-form-group { margin-bottom: 3.5vw; }
        .cd-form-label { font-size: 3.2vw; margin-bottom: 1.5vw; }

        .cd-form-input,
        .cd-form-textarea,
        .cd-form-file {
            padding: 3vw 3.5vw;
            border-radius: 2.5vw;
            font-size: 3.2vw;
        }

        .cd-form-textarea { min-height: 20vw; }

        .cd-form-file { padding: 2vw; font-size: 3vw; }
        .cd-form-file::file-selector-button {
            padding: 2vw 3vw;
            font-size: 3vw;
            border-radius: 1.5vw;
            margin-right: 2vw;
        }

        .cd-form-row {
            grid-template-columns: 1fr;
            gap: 3vw;
        }

        .cd-form-hint { font-size: 2.6vw; margin-top: 1vw; }
    }
</style>

<div class="cd-wrapper">

    <a href="{{ route('customer.careers.index') }}" class="cd-back">
        <iconify-icon icon="mdi:arrow-left"></iconify-icon>
        Kembali ke Daftar Karir
    </a>

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div style="margin-bottom:1.5vw;padding:1vw 1.2vw;background:#ecfdf5;border:0.1vw solid #a7f3d0;border-radius:0.8vw;color:#047857;font-size:0.85vw;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="margin-bottom:1.5vw;padding:1vw 1.2vw;background:#fef2f2;border:0.1vw solid #fecaca;border-radius:0.8vw;color:#b91c1c;font-size:0.85vw;">
            {{ session('error') }}
        </div>
    @endif

    <div class="cd-layout">

        {{-- MAIN --}}
        <div class="cd-main">

            {{-- HEADER --}}
            <div class="cd-header">
                <div class="cd-header-inner">
                    <div class="cd-header-icon">
                        <iconify-icon icon="mdi:briefcase-outline"></iconify-icon>
                    </div>
                    <div class="cd-header-info">
                        <div class="cd-header-title">{{ $career->title }}</div>
                        <div class="cd-header-sub">
                            {{ $career->department ?? 'Umum' }}
                            @if($career->location) · {{ $career->location }} @endif
                        </div>
                        <div class="cd-header-tags">
                            <span class="cd-header-tag">
                                <iconify-icon icon="mdi:clock-outline"></iconify-icon>
                                {{ $career->type_label }}
                            </span>
                            <span class="cd-header-tag">
                                <iconify-icon icon="mdi:chart-line"></iconify-icon>
                                {{ $career->level_label }}
                            </span>
                            @if($career->deadline && !$career->is_expired)
                                <span class="cd-header-tag">
                                    <iconify-icon icon="mdi:calendar-clock"></iconify-icon>
                                    Deadline: {{ $career->deadline->translatedFormat('d M Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- DESKRIPSI --}}
            @if($career->description)
                <div class="cd-section">
                    <div class="cd-section-head">
                        <iconify-icon icon="mdi:text-box-outline"></iconify-icon>
                        Deskripsi Pekerjaan
                    </div>
                    <div class="cd-section-body">
                        {!! $career->description !!}
                    </div>
                </div>
            @endif

            {{-- PERSYARATAN --}}
            @if($career->requirements)
                <div class="cd-section">
                    <div class="cd-section-head">
                        <iconify-icon icon="mdi:clipboard-check-outline"></iconify-icon>
                        Persyaratan
                    </div>
                    <div class="cd-section-body">
                        {!! $career->requirements !!}
                    </div>
                </div>
            @endif

            {{-- BENEFIT --}}
            @if($career->benefits)
                <div class="cd-section">
                    <div class="cd-section-head">
                        <iconify-icon icon="mdi:gift-outline"></iconify-icon>
                        Benefit
                    </div>
                    <div class="cd-section-body">
                        {!! $career->benefits !!}
                    </div>
                </div>
            @endif
        </div>

        {{-- SIDEBAR --}}
        <aside class="cd-sidebar">

            <div class="cd-summary">
                <div class="cd-summary-title">Ringkasan</div>

                <div class="cd-summary-item">
                    <iconify-icon icon="mdi:clock-outline"></iconify-icon>
                    <div class="cd-summary-item-info">
                        <div class="cd-summary-item-label">Tipe</div>
                        <div class="cd-summary-item-value">{{ $career->type_label }}</div>
                    </div>
                </div>

                <div class="cd-summary-item">
                    <iconify-icon icon="mdi:chart-line"></iconify-icon>
                    <div class="cd-summary-item-info">
                        <div class="cd-summary-item-label">Level</div>
                        <div class="cd-summary-item-value">{{ $career->level_label }}</div>
                    </div>
                </div>

                <div class="cd-summary-item">
                    <iconify-icon icon="mdi:map-marker-outline"></iconify-icon>
                    <div class="cd-summary-item-info">
                        <div class="cd-summary-item-label">Lokasi</div>
                        <div class="cd-summary-item-value">{{ $career->location ?? 'Tidak disebutkan' }}</div>
                    </div>
                </div>

                @if($career->salary_range)
                    <div class="cd-summary-item">
                        <iconify-icon icon="mdi:cash-multiple"></iconify-icon>
                        <div class="cd-summary-item-info">
                            <div class="cd-summary-item-label">Gaji</div>
                            <div class="cd-summary-item-value">{{ $career->salary_range }}</div>
                        </div>
                    </div>
                @endif

                @if($career->deadline)
                    <div class="cd-summary-item">
                        <iconify-icon icon="mdi:calendar-clock"></iconify-icon>
                        <div class="cd-summary-item-info">
                            <div class="cd-summary-item-label">Deadline</div>
                            <div class="cd-summary-item-value">
                                {{ $career->deadline->translatedFormat('d M Y') }}
                            </div>
                        </div>
                    </div>
                @endif

                <div class="cd-summary-item">
                    <iconify-icon icon="mdi:account-group-outline"></iconify-icon>
                    <div class="cd-summary-item-info">
                        <div class="cd-summary-item-label">Kuota</div>
                        <div class="cd-summary-item-value">{{ $career->quota }} orang</div>
                    </div>
                </div>
            </div>

            {{-- CTA (desktop only) --}}
            <div class="cd-cta">
                @if($hasApplied)
                    <div class="cd-cta-title">✓ Anda Sudah Melamar</div>
                    <div class="cd-cta-desc">Lamaran Anda sedang ditinjau oleh tim HR kami.</div>
                    <div class="cd-cta-btn disabled">
                        <iconify-icon icon="mdi:check-circle"></iconify-icon>
                        Sudah Dilamar
                    </div>
                @elseif($career->is_expired)
                    <div class="cd-cta-title">Lowongan Ditutup</div>
                    <div class="cd-cta-desc">Deadline untuk lowongan ini sudah berakhir.</div>
                    <div class="cd-cta-btn disabled">
                        <iconify-icon icon="mdi:clock-remove-outline"></iconify-icon>
                        Ditutup
                    </div>
                @else
                    <div class="cd-cta-title">Tertarik Bergabung?</div>
                    <div class="cd-cta-desc">
                        Kirim lamaran Anda sekarang sebelum deadline berakhir.
                    </div>
                    <button type="button" class="cd-cta-btn" onclick="window.openApplyModal()">
                        <iconify-icon icon="mdi:send-outline"></iconify-icon>
                        Lamar Sekarang
                    </button>
                @endif
            </div>
        </aside>
    </div>
</div>

{{-- ============================================ --}}
{{-- 🔥 STICKY CTA BAR — MOBILE ONLY --}}
{{-- ============================================ --}}
@if(!$hasApplied && !$career->is_expired)
<div class="cd-sticky-cta" id="stickyCta">
    <div class="cd-sticky-cta-info">
        <div class="cd-sticky-cta-title">{{ $career->title }}</div>
        <div class="cd-sticky-cta-sub">
            <span>
                <iconify-icon icon="mdi:clock-outline"></iconify-icon>
                {{ $career->type_label }}
            </span>
            @if($career->location)
                <span>
                    <iconify-icon icon="mdi:map-marker-outline"></iconify-icon>
                    {{ $career->location }}
                </span>
            @endif
        </div>
    </div>
    <button type="button" class="cd-sticky-cta-btn" onclick="window.openApplyModal()">
        <iconify-icon icon="mdi:send-outline"></iconify-icon>
        Lamar
    </button>
</div>
@endif

{{-- ============================================ --}}
{{-- MODAL APPLY --}}
{{-- ============================================ --}}
<div id="applyModal" class="cd-modal">
    <div class="cd-modal-box">

        {{-- HEADER STICKY --}}
        <div class="cd-modal-head">
            <h3 class="cd-modal-title">Lamar Posisi Ini</h3>
            <button type="button" class="cd-modal-close" onclick="window.closeApplyModal()">✕</button>
        </div>

        {{-- FORM --}}
        <form action="{{ route('customer.careers.apply', $career) }}"
              method="POST"
              enctype="multipart/form-data"
              style="display: flex; flex-direction: column; flex: 1; min-height: 0; overflow: hidden;">
            @csrf

            {{-- BODY SCROLLABLE --}}
            <div class="cd-modal-body">

                {{-- 🔥 TAMPILKAN ERROR VALIDATION --}}
                @if($errors->any())
                    <div style="margin-bottom:1.5vw;padding:1vw 1.2vw;background:#fef2f2;border:0.1vw solid #fecaca;border-radius:0.8vw;color:#b91c1c;font-size:0.85vw;">
                        <p style="font-weight:700;margin-bottom:0.5vw;">Periksa kembali input Anda:</p>
                        <ul style="padding-left:1.2vw;margin:0;">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="cd-form-group">
                    <label class="cd-form-label">Nama Lengkap *</label>
                    <input type="text" name="full_name" required class="cd-form-input"
                           value="{{ Auth::guard('customer')->user()->name ?? '' }}">
                </div>

                <div class="cd-form-row">
                    <div class="cd-form-group">
                        <label class="cd-form-label">Email *</label>
                        <input type="email" name="email" required class="cd-form-input"
                               value="{{ Auth::guard('customer')->user()->email ?? '' }}">
                    </div>
                    <div class="cd-form-group">
                        <label class="cd-form-label">No. HP / WhatsApp *</label>
                        <input type="text" name="phone" required class="cd-form-input"
                               value="{{ Auth::guard('customer')->user()->phone ?? '' }}">
                    </div>
                </div>

                <div class="cd-form-group">
                    <label class="cd-form-label">Alamat</label>
                    <textarea name="address" rows="2" class="cd-form-textarea"></textarea>
                </div>

                <div class="cd-form-row">
                    <div class="cd-form-group">
                        <label class="cd-form-label">Tanggal Lahir</label>
                        <input type="date" name="birth_date" class="cd-form-input">
                    </div>
                    <div class="cd-form-group">
                        <label class="cd-form-label">Jenis Kelamin</label>
                        <select name="gender" class="cd-form-input">
                            <option value="">- Pilih -</option>
                            <option value="male">Laki-laki</option>
                            <option value="female">Perempuan</option>
                        </select>
                    </div>
                </div>

                <div class="cd-form-row">
                    <div class="cd-form-group">
                        <label class="cd-form-label">Pendidikan Terakhir</label>
                        <input type="text" name="last_education" class="cd-form-input" placeholder="S1, SMA, dll">
                    </div>
                    <div class="cd-form-group">
                        <label class="cd-form-label">Jurusan</label>
                        <input type="text" name="major" class="cd-form-input">
                    </div>
                </div>

                <div class="cd-form-group">
                    <label class="cd-form-label">Pengalaman (tahun)</label>
                    <input type="number" name="experience_years" min="0" max="50" value="0" class="cd-form-input">
                </div>

                <div class="cd-form-group">
                    <label class="cd-form-label">Cover Letter</label>
                    <textarea name="cover_letter" rows="3" class="cd-form-textarea"
                              placeholder="Ceritakan kenapa Anda cocok untuk posisi ini..."></textarea>
                </div>

                <div class="cd-form-group">
                    <label class="cd-form-label">Upload CV * (PDF/DOC, maks 5MB)</label>
                    <input type="file" name="cv_file" accept=".pdf,.doc,.docx" required class="cd-form-file">
                </div>

                <div class="cd-form-group">
                    <label class="cd-form-label">Portfolio (opsional)</label>
                    <input type="file" name="portfolio_file" accept=".pdf,.doc,.docx,.zip,.rar" class="cd-form-file">
                    <p class="cd-form-hint">Maks 10MB</p>
                </div>

                <div style="height: 1vw;"></div>
            </div>

            {{-- FOOTER STICKY --}}
            <div class="cd-modal-actions">
                <button type="button" class="btn-cancel" onclick="window.closeApplyModal()">Batal</button>
                <button type="submit" class="btn-submit">
                    <iconify-icon icon="mdi:send-outline"></iconify-icon>
                    Kirim Lamaran
                </button>
            </div>
        </form>
    </div>
</div>

<script>

    window.openApplyModal = function () {
        const modal = document.getElementById('applyModal');
        if (!modal) return;
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    };

window.closeApplyModal = function () {
    const modal = document.getElementById('applyModal');
    if (!modal) return;
    modal.classList.remove('active');
    document.body.style.overflow = '';
};

// ============================================
// INIT LISTENERS
// ============================================
document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('applyModal');
    const stickyCta = document.getElementById('stickyCta');

    if (!modal) return;

    // Close saat klik overlay
    modal.addEventListener('click', function (e) {
        if (e.target === this) window.closeApplyModal();
    });

    // Close saat ESC
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            window.closeApplyModal();
        }
    });

    // Sembunyikan sticky CTA saat modal terbuka
    if (stickyCta) {
        const observer = new MutationObserver(function () {
            if (modal.classList.contains('active')) {
                stickyCta.classList.add('hidden');
            } else {
                stickyCta.classList.remove('hidden');
            }
        });
        observer.observe(modal, { attributes: true, attributeFilter: ['class'] });
    }
});
</script>

@endsection