@extends('layouts.account')

@section('title', 'Voucher Saya - Barokah Sport')
@section('page-title', 'Voucher Saya')
@section('page-subtitle', 'Kumpulkan dan gunakan voucher untuk mendapatkan potongan harga.')

@section('account-content')

<style>
    /* ============================================
       VOUCHER PAGE STYLES
       ============================================ */
    .vc-wrapper {
        display: flex;
        flex-direction: column;
        gap: 1.2vw;
    }

    /* --------------------------------------------
       TABS
       -------------------------------------------- */
    .vc-tabs {
        display: flex;
        gap: 0.2vw;
        border-bottom: 0.1vw solid #e5e7eb;
        overflow-x: auto;
        scrollbar-width: none;
    }
    .vc-tabs::-webkit-scrollbar { display: none; }

    .vc-tab {
        display: flex;
        align-items: center;
        gap: 0.5vw;
        padding: 1vw 1.3vw;
        font-size: 0.85vw;
        font-weight: 500;
        color: #64748b;
        text-decoration: none;
        border-bottom: 0.2vw solid transparent;
        white-space: nowrap;
        transition: all 0.2s ease;
        flex-shrink: 0;
        margin-bottom: -0.1vw;
    }

    .vc-tab:hover {
        color: #0f172a;
        border-bottom-color: #cbd5e1;
    }

    .vc-tab.active {
        color: rgb(102, 72, 9);
        border-bottom-color: #ecbc42;
        font-weight: 700;
    }

    .vc-tab iconify-icon {
        font-size: 1.1vw;
    }

    .vc-tab.active iconify-icon {
        color: #ecbc42;
    }

    .vc-tab-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 1.5vw;
        height: 1.5vw;
        padding: 0 0.5vw;
        border-radius: 100vw;
        font-size: 0.7vw;
        font-weight: 700;
        background: #f1f5f9;
        color: #64748b;
    }

    .vc-tab.active .vc-tab-badge {
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
        color: rgb(102, 72, 9);
    }

    /* --------------------------------------------
       FILTER
       -------------------------------------------- */
    .vc-filter {
        display: flex;
        align-items: center;
        gap: 0.7vw;
        flex-wrap: wrap;
    }

    .vc-filter-form {
        display: flex;
        align-items: center;
        gap: 0.5vw;
        flex: 1;
        flex-wrap: wrap;
    }

    .vc-search {
        flex: 1;
        min-width: 12vw;
        padding: 0.65vw 1vw;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.6vw;
        font-size: 0.82vw;
        color: #0f172a;
        outline: none;
        transition: all 0.2s ease;
        font-family: inherit;
    }

    .vc-search::placeholder { color: #cbd5e1; }

    .vc-search:focus {
        border-color: #ecbc42;
        box-shadow: 0 0 0 0.2vw rgba(236, 188, 66, 0.15);
    }

    .vc-select {
        padding: 0.65vw 2vw 0.65vw 1vw;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.6vw;
        font-size: 0.82vw;
        color: #0f172a;
        background: #ffffff;
        outline: none;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: inherit;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.6vw center;
        background-size: 0.9vw;
    }

    .vc-select:focus {
        border-color: #ecbc42;
        box-shadow: 0 0 0 0.2vw rgba(236, 188, 66, 0.15);
    }

    .vc-reset {
        display: inline-flex;
        align-items: center;
        gap: 0.3vw;
        font-size: 0.8vw;
        color: #dc2626;
        text-decoration: none;
        padding: 0.5vw 0.8vw;
        border-radius: 0.5vw;
        transition: background 0.2s ease;
    }
    .vc-reset:hover {
        background: #fef2f2;
    }

    .vc-reset iconify-icon {
        font-size: 0.9vw;
    }

    .vc-count {
        font-size: 0.82vw;
        color: #94a3b8;
        white-space: nowrap;
    }

    /* --------------------------------------------
       EMPTY STATE
       -------------------------------------------- */
    .vc-empty {
        text-align: center;
        padding: 4vw 2vw;
    }

    .vc-empty-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 5vw;
        height: 5vw;
        margin: 0 auto;
        border-radius: 50%;
        background: linear-gradient(135deg, #FDDD57 0%, #ecbc42 100%);
        color: rgb(102, 72, 9);
        box-shadow: 0 0.3vw 1vw rgba(236, 188, 66, 0.3);
    }

    .vc-empty-icon iconify-icon {
        font-size: 2.5vw;
    }

    .vc-empty-title {
        font-size: 1.05vw;
        font-weight: 700;
        color: #0f172a;
        margin-top: 1.2vw;
    }

    .vc-empty-desc {
        font-size: 0.85vw;
        color: #64748b;
        margin-top: 0.5vw;
        line-height: 1.6;
    }

    /* --------------------------------------------
       VOUCHER GRID
       -------------------------------------------- */
    .vc-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1vw;
    }

    .vc-card {
        position: relative;
        background: #ffffff;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.9vw;
        overflow: hidden;
        transition: all 0.25s ease;
        display: flex;
        flex-direction: column;
    }

    .vc-card:hover {
        border-color: #ecbc42;
        box-shadow: 0 0.5vw 1.5vw rgba(0, 0, 0, 0.06);
        transform: translateY(-0.15vw);
    }

    .vc-card.is-used {
        opacity: 0.75;
    }

    /* Used Badge */
    .vc-used-badge {
        position: absolute;
        top: 0.7vw;
        right: 0.7vw;
        display: inline-flex;
        align-items: center;
        gap: 0.25vw;
        padding: 0.3vw 0.7vw;
        border-radius: 100vw;
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 100%);
        color: rgb(102, 72, 9);
        font-size: 0.65vw;
        font-weight: 700;
        z-index: 2;
        box-shadow: 0 0.15vw 0.5vw rgba(236, 188, 66, 0.35);
    }

    .vc-used-badge iconify-icon {
        font-size: 0.75vw;
    }

    /* Top Section - Value */
    .vc-card-top {
        position: relative;
        padding: 1.2vw 1.2vw 1vw;
        background: linear-gradient(135deg, #fffbf0 0%, #fff7e0 100%);
        border-bottom: 0.1vw dashed #fde68a;
    }

    .vc-card-top::before,
    .vc-card-top::after {
        content: '';
        position: absolute;
        bottom: -0.5vw;
        width: 1vw;
        height: 1vw;
        background: #ffffff;
        border-radius: 50%;
        border: 0.1vw solid #e2e8f0;
        border-top-color: #fde68a;
        border-right-color: #fde68a;
    }
    .vc-card-top::before { left: -0.5vw; transform: rotate(45deg); }
    .vc-card-top::after  { right: -0.5vw; transform: rotate(-135deg); }

    .vc-value {
        font-size: 1.7vw;
        font-weight: 800;
        color: rgb(102, 72, 9);
        line-height: 1.1;
        display: flex;
        align-items: baseline;
        gap: 0.2vw;
        flex-wrap: wrap;
    }

    .vc-value .unit {
        font-size: 0.9vw;
        font-weight: 500;
        color: #94a3b8;
    }

    .vc-value .badge-label {
        font-size: 0.85vw;
        font-weight: 700;
        color: rgb(102, 72, 9);
        background: rgba(255, 255, 255, 0.6);
        padding: 0.2vw 0.5vw;
        border-radius: 0.4vw;
    }

    .vc-code-wrap {
        display: flex;
        align-items: center;
        gap: 0.4vw;
        margin-top: 0.7vw;
    }

    .vc-code {
        font-family: 'Courier New', monospace;
        font-size: 0.75vw;
        font-weight: 800;
        color: rgb(102, 72, 9);
        background: #ffffff;
        padding: 0.35vw 0.7vw;
        border-radius: 0.4vw;
        border: 0.1vw dashed #ecbc42;
        letter-spacing: 0.05em;
    }

    .vc-min {
        font-size: 0.72vw;
        color: #94a3b8;
        margin-top: 0.5vw;
        display: flex;
        align-items: center;
        gap: 0.3vw;
    }

    .vc-min iconify-icon {
        font-size: 0.8vw;
    }

    /* Body Section */
    .vc-card-body {
        padding: 1.1vw 1.2vw;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 0.6vw;
    }

    .vc-name {
        font-size: 0.9vw;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.4;
    }

    .vc-desc {
        font-size: 0.75vw;
        color: #64748b;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .vc-time {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.4vw;
        flex-wrap: wrap;
        margin-top: auto;
        padding-top: 0.6vw;
        border-top: 0.1vw solid #f1f5f9;
    }

    .vc-time-label {
        font-size: 0.72vw;
        color: #94a3b8;
    }

    .vc-time-value {
        display: inline-flex;
        align-items: center;
        gap: 0.25vw;
        font-size: 0.75vw;
        font-weight: 600;
    }

    .vc-time-value iconify-icon {
        font-size: 0.85vw;
    }

    .vc-time-value.gray   { color: #64748b; }
    .vc-time-value.orange { color: #f59e0b; }
    .vc-time-value.red    { color: #dc2626; }

    /* Footer */
    .vc-card-footer {
        padding: 0 1.2vw 1.1vw;
    }

    .vc-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4vw;
        width: 100%;
        padding: 0.75vw 1.2vw;
        border-radius: 0.6vw;
        font-size: 0.8vw;
        font-weight: 700;
        cursor: pointer;
        border: none;
        transition: all 0.2s ease;
        text-decoration: none;
        font-family: inherit;
    }

    .vc-btn iconify-icon {
        font-size: 0.95vw;
    }

    .vc-btn-gold {
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
        color: rgb(102, 72, 9);
        box-shadow: 0 0.15vw 0.5vw rgba(236, 188, 66, 0.3);
    }

    .vc-btn-gold:hover {
        box-shadow: 0 0.3vw 1vw rgba(236, 188, 66, 0.45);
        transform: translateY(-0.1vw);
    }

    .vc-btn-neutral {
        background: #f8fafc;
        color: #475569;
        border: 0.1vw solid #e2e8f0;
    }

    .vc-btn-neutral:hover {
        background: #f1f5f9;
        color: #0f172a;
    }

    /* ============================================
       RESPONSIVE - TABLET
       ============================================ */
    @media (max-width: 1024px) {
        .vc-wrapper { gap: 3vw; }

        .vc-tabs { border-bottom-width: 0.2vw; }

        .vc-tab {
            padding: 2.5vw 3vw;
            font-size: 2vw;
            gap: 1.2vw;
            border-bottom-width: 0.4vw;
        }
        .vc-tab iconify-icon { font-size: 2.5vw; }

        .vc-tab-badge {
            min-width: 3.5vw;
            height: 3.5vw;
            padding: 0 1vw;
            font-size: 1.7vw;
        }

        .vc-filter { gap: 1.5vw; }
        .vc-filter-form { gap: 1.2vw; }

        .vc-search {
            padding: 1.8vw 2.5vw;
            border-radius: 1.5vw;
            font-size: 2vw;
            border-width: 0.2vw;
            min-width: 40vw;
        }

        .vc-select {
            padding: 1.8vw 5vw 1.8vw 2.5vw;
            border-radius: 1.5vw;
            font-size: 2vw;
            border-width: 0.2vw;
            background-size: 2.4vw;
            background-position: right 2vw center;
        }

        .vc-reset {
            font-size: 1.9vw;
            gap: 0.8vw;
            padding: 1.2vw 2vw;
            border-radius: 1.2vw;
        }
        .vc-reset iconify-icon { font-size: 2.2vw; }

        .vc-count { font-size: 2vw; }

        .vc-empty { padding: 8vw 4vw; }
        .vc-empty-icon {
            width: 12vw;
            height: 12vw;
        }
        .vc-empty-icon iconify-icon { font-size: 6vw; }
        .vc-empty-title { font-size: 2.7vw; margin-top: 3vw; }
        .vc-empty-desc { font-size: 2vw; margin-top: 1.2vw; }

        .vc-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 2.5vw;
        }

        .vc-card {
            border-radius: 2vw;
            border-width: 0.2vw;
        }

        .vc-used-badge {
            top: 1.5vw;
            right: 1.5vw;
            padding: 0.7vw 1.6vw;
            font-size: 1.6vw;
            gap: 0.6vw;
        }
        .vc-used-badge iconify-icon { font-size: 1.8vw; }

        .vc-card-top {
            padding: 3vw 3vw 2.5vw;
            border-bottom-width: 0.2vw;
        }

        .vc-card-top::before,
        .vc-card-top::after {
            bottom: -1vw;
            width: 2vw;
            height: 2vw;
            border-width: 0.2vw;
        }
        .vc-card-top::before { left: -1vw; }
        .vc-card-top::after  { right: -1vw; }

        .vc-value { font-size: 4vw; gap: 0.5vw; }
        .vc-value .unit { font-size: 2.2vw; }
        .vc-value .badge-label { font-size: 2vw; padding: 0.5vw 1.2vw; border-radius: 1vw; }

        .vc-code {
            font-size: 1.9vw;
            padding: 0.9vw 1.7vw;
            border-radius: 1vw;
            border-width: 0.2vw;
        }
        .vc-code-wrap { gap: 1vw; margin-top: 1.7vw; }

        .vc-min { font-size: 1.8vw; margin-top: 1.2vw; gap: 0.7vw; }
        .vc-min iconify-icon { font-size: 2.1vw; }

        .vc-card-body {
            padding: 2.8vw 3vw;
            gap: 1.5vw;
        }

        .vc-name { font-size: 2.2vw; }
        .vc-desc { font-size: 1.9vw; }

        .vc-time {
            gap: 1vw;
            padding-top: 1.5vw;
            border-top-width: 0.2vw;
        }
        .vc-time-label { font-size: 1.8vw; }
        .vc-time-value { font-size: 1.9vw; gap: 0.6vw; }
        .vc-time-value iconify-icon { font-size: 2.1vw; }

        .vc-card-footer { padding: 0 3vw 2.8vw; }

        .vc-btn {
            padding: 2.2vw 3vw;
            border-radius: 1.5vw;
            font-size: 2vw;
            gap: 1vw;
        }
        .vc-btn iconify-icon { font-size: 2.4vw; }
    }

    /* ============================================
       RESPONSIVE - MOBILE
       ============================================ */
    @media (max-width: 480px) {
        .vc-wrapper { gap: 4vw; }

        .vc-tabs { border-bottom-width: 0.3vw; }

        .vc-tab {
            padding: 3vw 4vw;
            font-size: 3.2vw;
            gap: 1.5vw;
            border-bottom-width: 0.6vw;
        }
        .vc-tab iconify-icon { font-size: 4vw; }

        .vc-tab-badge {
            min-width: 5vw;
            height: 5vw;
            padding: 0 1.5vw;
            font-size: 2.5vw;
        }

        .vc-filter {
            flex-direction: column;
            align-items: stretch;
            gap: 3vw;
        }

        .vc-filter-form {
            flex-direction: column;
            align-items: stretch;
            gap: 2.5vw;
        }

        .vc-search {
            padding: 3.2vw 4vw;
            border-radius: 2.5vw;
            font-size: 3.2vw;
            min-width: 0;
            border-width: 0.3vw;
        }

        .vc-select {
            padding: 3.2vw 8vw 3.2vw 4vw;
            border-radius: 2.5vw;
            font-size: 3.2vw;
            border-width: 0.3vw;
            background-size: 4vw;
            background-position: right 3.5vw center;
        }

        .vc-reset {
            justify-content: center;
            font-size: 3vw;
            gap: 1.5vw;
            padding: 2.5vw 3vw;
            border-radius: 2vw;
        }
        .vc-reset iconify-icon { font-size: 4vw; }

        .vc-count {
            text-align: center;
            font-size: 3vw;
        }

        .vc-empty { padding: 10vw 5vw; }
        .vc-empty-icon {
            width: 20vw;
            height: 20vw;
        }
        .vc-empty-icon iconify-icon { font-size: 10vw; }
        .vc-empty-title { font-size: 4.2vw; margin-top: 5vw; }
        .vc-empty-desc { font-size: 3.2vw; margin-top: 2vw; line-height: 1.7; }

        .vc-grid {
            grid-template-columns: 1fr;
            gap: 4vw;
        }

        .vc-card {
            border-radius: 3vw;
            border-width: 0.3vw;
        }

        .vc-used-badge {
            top: 2.5vw;
            right: 2.5vw;
            padding: 1.2vw 2.8vw;
            font-size: 2.8vw;
            gap: 1vw;
        }
        .vc-used-badge iconify-icon { font-size: 3.2vw; }

        .vc-card-top {
            padding: 5vw 4vw 4.5vw;
            border-bottom-width: 0.3vw;
        }

        .vc-card-top::before,
        .vc-card-top::after {
            bottom: -1.5vw;
            width: 3vw;
            height: 3vw;
            border-width: 0.3vw;
        }
        .vc-card-top::before { left: -1.5vw; }
        .vc-card-top::after  { right: -1.5vw; }

        .vc-value { font-size: 7vw; gap: 1vw; }
        .vc-value .unit { font-size: 4vw; }
        .vc-value .badge-label {
            font-size: 3.2vw;
            padding: 1vw 2vw;
            border-radius: 1.5vw;
        }

        .vc-code {
            font-size: 3.2vw;
            padding: 1.5vw 2.5vw;
            border-radius: 1.8vw;
            border-width: 0.3vw;
        }
        .vc-code-wrap { gap: 2vw; margin-top: 3vw; }

        .vc-min { font-size: 3vw; margin-top: 2.5vw; gap: 1.2vw; }
        .vc-min iconify-icon { font-size: 3.5vw; }

        .vc-card-body {
            padding: 4.5vw 4vw;
            gap: 2.5vw;
        }

        .vc-name { font-size: 3.8vw; line-height: 1.4; }
        .vc-desc { font-size: 3vw; }

        .vc-time {
            gap: 2vw;
            padding-top: 2.5vw;
            border-top-width: 0.3vw;
        }
        .vc-time-label { font-size: 2.8vw; }
        .vc-time-value { font-size: 3vw; gap: 1vw; }
        .vc-time-value iconify-icon { font-size: 3.5vw; }

        .vc-card-footer { padding: 0 4vw 4.5vw; }

        .vc-btn {
            padding: 3.5vw 4vw;
            border-radius: 2.5vw;
            font-size: 3.2vw;
            gap: 1.5vw;
        }
        .vc-btn iconify-icon { font-size: 4vw; }
    }
</style>

<div class="vc-wrapper">

    @if ($vouchers->isEmpty())
        {{-- Empty State --}}
        <div class="vc-empty">
            <div class="vc-empty-icon">
                <iconify-icon icon="{{ $tab === 'riwayat' ? 'mdi:history' : 'mdi:ticket-percent-outline' }}"></iconify-icon>
            </div>
            @if($tab === 'riwayat')
                <div class="vc-empty-title">Belum Ada Riwayat Voucher</div>
                <div class="vc-empty-desc">Anda belum pernah menggunakan voucher apapun.</div>
            @else
                <div class="vc-empty-title">Belum Ada Voucher Tersedia</div>
                <div class="vc-empty-desc">Saat ini belum ada voucher yang tersedia untuk Anda. Cek kembali nanti!</div>
            @endif
        </div>
    @else

        {{-- Tabs --}}
        <div class="vc-tabs">
            <a href="{{ route('customer.vouchers.index', array_filter(['tab' => 'semua', 'search' => request('search'), 'sort' => request('sort')])) }}"
               class="vc-tab {{ $tab === 'semua' ? 'active' : '' }}">
                <iconify-icon icon="mdi:ticket-percent-outline"></iconify-icon>
                Semua
                @if($allCount > 0)
                    <span class="vc-tab-badge">{{ $allCount }}</span>
                @endif
            </a>

            @auth('customer')
                <a href="{{ route('customer.vouchers.index', array_filter(['tab' => 'riwayat', 'search' => request('search'), 'sort' => request('sort')])) }}"
                   class="vc-tab {{ $tab === 'riwayat' ? 'active' : '' }}">
                    <iconify-icon icon="mdi:history"></iconify-icon>
                    Riwayat
                    @if($historyCount > 0)
                        <span class="vc-tab-badge">{{ $historyCount }}</span>
                    @endif
                </a>
            @endauth
        </div>

        {{-- Filter --}}
        <div class="vc-filter">
            <form method="GET" action="{{ route('customer.vouchers.index') }}" class="vc-filter-form">
                <input type="hidden" name="tab" value="{{ $tab }}">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif

                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari voucher..." 
                       class="vc-search"
                       onchange="this.form.submit()">

                <select name="sort" onchange="this.form.submit()" class="vc-select">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="discount_desc" {{ request('sort') == 'discount_desc' ? 'selected' : '' }}>Diskon Terbesar</option>
                    <option value="discount_asc" {{ request('sort') == 'discount_asc' ? 'selected' : '' }}>Diskon Terkecil</option>
                    <option value="expired_soon" {{ request('sort') == 'expired_soon' ? 'selected' : '' }}>Segera Berakhir</option>
                </select>

                @if(request()->anyFilled(['search', 'sort']))
                    <a href="{{ route('customer.vouchers.index', ['tab' => $tab]) }}" class="vc-reset">
                        <iconify-icon icon="mdi:refresh"></iconify-icon>
                        Reset
                    </a>
                @endif
            </form>
            <span class="vc-count">{{ $vouchers->total() }} voucher</span>
        </div>

        {{-- Grid --}}
        <div class="vc-grid">
            @foreach ($vouchers as $voucher)
                <div class="vc-card {{ $voucher->is_used_by_user ? 'is-used' : '' }}">
                    @if($voucher->is_used_by_user)
                        <span class="vc-used-badge">
                            <iconify-icon icon="mdi:check-circle"></iconify-icon>
                            Digunakan
                        </span>
                    @endif

                    {{-- Top Section --}}
                    <div class="vc-card-top">
                        <div class="vc-value">
                            @if($voucher->is_free_shipping)
                                <span class="badge-label">
                                    <iconify-icon icon="mdi:truck-fast-outline" style="vertical-align:middle;margin-right:0.2vw;"></iconify-icon>
                                    Gratis Ongkir
                                </span>
                            @elseif($voucher->discount_target === 'shipping')
                                <span class="badge-label">
                                    <iconify-icon icon="mdi:truck-outline" style="vertical-align:middle;margin-right:0.2vw;"></iconify-icon>
                                    Diskon Ongkir
                                </span>
                                @if($voucher->discount_type === 'percentage')
                                    <span>{{ round($voucher->discount_value) }}<span class="unit">%</span></span>
                                @else
                                    <span><span class="unit">Rp</span> {{ number_format($voucher->discount_value, 0, ',', '.') }}</span>
                                @endif
                            @else
                                @if($voucher->discount_type === 'percentage')
                                    <span>{{ round($voucher->discount_value) }}<span class="unit">%</span></span>
                                @else
                                    <span><span class="unit">Rp</span> {{ number_format($voucher->discount_value, 0, ',', '.') }}</span>
                                @endif
                            @endif
                        </div>

                        <div class="vc-code-wrap">
                            <span class="vc-code">{{ $voucher->code }}</span>
                        </div>

                        <div class="vc-min">
                            @if($voucher->is_free_shipping)
                                <iconify-icon icon="mdi:information-outline"></iconify-icon>
                                Gratis biaya pengiriman
                            @else
                                <iconify-icon icon="mdi:cart-outline"></iconify-icon>
                                Min. Belanja Rp {{ number_format($voucher->min_transaction_amount, 0, ',', '.') }}
                            @endif
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="vc-card-body">
                        <div class="vc-name">{{ $voucher->name }}</div>

                        @if($voucher->description)
                            <div class="vc-desc">{{ $voucher->description }}</div>
                        @endif

                        @php
                            $now = now();
                            $endDate = $voucher->end_date;
                            $isExpired = $now->greaterThan($endDate);

                            if ($isExpired) {
                                $daysLeft = 0;
                                $hoursLeft = 0;
                            } else {
                                $diff = $now->diff($endDate);
                                $daysLeft = $diff->days;
                                $hoursLeft = $diff->h;
                            }

                            if ($isExpired) {
                                $timeClass = 'gray';
                                $timeIcon = 'mdi:clock-remove-outline';
                                $timeText = 'Kadaluarsa';
                            } elseif ($daysLeft <= 3) {
                                $timeClass = 'red';
                                $timeIcon = 'mdi:clock-alert-outline';
                                $timeText = $daysLeft > 0 ? "{$daysLeft} hari lagi" : "{$hoursLeft} jam lagi";
                            } elseif ($daysLeft <= 7) {
                                $timeClass = 'orange';
                                $timeIcon = 'mdi:clock-outline';
                                $timeText = "{$daysLeft} hari lagi";
                            } else {
                                $timeClass = 'gray';
                                $timeIcon = 'mdi:clock-outline';
                                $timeText = null;
                            }
                        @endphp

                        <div class="vc-time">
                            <span class="vc-time-label">
                                <iconify-icon icon="mdi:calendar-clock-outline" style="vertical-align:middle;margin-right:0.2vw;"></iconify-icon>
                                Berlaku sampai
                            </span>
                            <span class="vc-time-value {{ $timeClass }}">
                                <iconify-icon icon="{{ $timeIcon }}"></iconify-icon>
                                @if($timeText)
                                    {{ $timeText }}
                                @else
                                    {{ $endDate->translatedFormat('d M Y') }}
                                @endif
                            </span>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="vc-card-footer">
                        <a href="{{ route('customer.vouchers.show', $voucher) }}" 
                           class="vc-btn {{ $voucher->is_used_by_user ? 'vc-btn-neutral' : 'vc-btn-gold' }}">
                            <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                            Lihat Detail Voucher
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $vouchers->links() }}
        </div>
    @endif
</div>

@endsection