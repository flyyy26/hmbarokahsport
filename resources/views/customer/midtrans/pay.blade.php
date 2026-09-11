@extends('layouts.customer')

@section('title', 'Pembayaran - ' . config('app.name'))

@section('content')

<style>
    /* ============================================
       PAYMENT PAGE STYLES
       ============================================ */
    .pm-wrapper {
        max-width: 44vw;
        margin: 2vw auto;
        padding: 0 1.5vw;
    }

    .pm-card {
        background: #ffffff;
        border: 0.1vw solid #e2e8f0;
        border-radius: 1.2vw;
        box-shadow: 0 0.5vw 2vw rgba(0, 0, 0, 0.06);
        overflow: hidden;
    }

    /* --------------------------------------------
       HEADER
       -------------------------------------------- */
    .pm-header {
        position: relative;
        padding: 2vw;
        text-align: center;
        background: linear-gradient(135deg, #FDDD57 0%, #ecbc42 50%, #d4a72e 100%);
        overflow: hidden;
    }

    .pm-header::before {
        content: '';
        position: absolute;
        top: -3vw;
        right: -3vw;
        width: 10vw;
        height: 10vw;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.15);
    }

    .pm-header::after {
        content: '';
        position: absolute;
        bottom: -2vw;
        left: -2vw;
        width: 7vw;
        height: 7vw;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
    }

    .pm-header-inner {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5vw;
    }

    .pm-header-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 3.5vw;
        height: 3.5vw;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.5);
        color: rgb(102, 72, 9);
        margin-bottom: 0.3vw;
    }

    .pm-header-icon iconify-icon {
        font-size: 2vw;
    }

    .pm-header h2 {
        font-size: 1.4vw;
        font-weight: 800;
        color: rgb(102, 72, 9);
        line-height: 1.2;
    }

    .pm-header p {
        font-size: 0.85vw;
        color: rgba(102, 72, 9, 0.75);
        line-height: 1.5;
    }

    .pm-order-id {
        display: inline-flex;
        align-items: center;
        gap: 0.35vw;
        padding: 0.4vw 1vw;
        margin-top: 0.4vw;
        background: rgba(255, 255, 255, 0.5);
        border: 0.1vw dashed rgba(102, 72, 9, 0.3);
        border-radius: 0.5vw;
        font-family: 'Courier New', monospace;
        font-size: 0.8vw;
        font-weight: 800;
        color: rgb(102, 72, 9);
        letter-spacing: 0.05em;
        backdrop-filter: blur(0.3vw);
    }

    .pm-order-id iconify-icon {
        font-size: 0.9vw;
        opacity: 0.7;
    }

    /* --------------------------------------------
       STATUS BADGE
       -------------------------------------------- */
    .pm-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35vw;
        padding: 0.4vw 1vw;
        margin-top: 0.7vw;
        border-radius: 100vw;
        font-size: 0.75vw;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        background: rgba(255, 255, 255, 0.6);
        color: rgb(102, 72, 9);
    }

    .pm-status-badge iconify-icon {
        font-size: 0.9vw;
    }

    .pm-status-badge.unpaid  { background: rgba(251, 191, 36, 0.9);  color: #422006; }
    .pm-status-badge.paid    { background: rgba(16, 185, 129, 0.9);  color: #ffffff; }
    .pm-status-badge.pending { background: rgba(59, 130, 246, 0.9);  color: #ffffff; }
    .pm-status-badge.failed  { background: rgba(220, 38, 38, 0.9);   color: #ffffff; }

    /* --------------------------------------------
       BODY
       -------------------------------------------- */
    .pm-body {
        padding: 1.6vw;
        display: flex;
        flex-direction: column;
        gap: 1.2vw;
    }

    /* --------------------------------------------
       ALERTS
       -------------------------------------------- */
    .pm-alert {
        display: flex;
        align-items: flex-start;
        gap: 0.7vw;
        padding: 0.9vw 1.1vw;
        border-radius: 0.7vw;
        font-size: 0.82vw;
        line-height: 1.5;
        border: 0.1vw solid transparent;
        animation: pmFadeIn 0.3s ease-out;
    }

    @keyframes pmFadeIn {
        from { opacity: 0; transform: translateY(-0.5vw); }
        to { opacity: 1; transform: translateY(0); }
    }

    .pm-alert iconify-icon {
        font-size: 1.2vw;
        flex-shrink: 0;
        margin-top: 0.1vw;
    }

    .pm-alert-info    { background: #eff6ff; border-color: #bfdbfe; color: #1d4ed8; }
    .pm-alert-success { background: #ecfdf5; border-color: #a7f3d0; color: #047857; }
    .pm-alert-error   { background: #fef2f2; border-color: #fecaca; color: #b91c1c; }
    .pm-alert-warning { background: #fffbeb; border-color: #fde68a; color: #b45309; }

    .pm-alert a {
        color: inherit;
        font-weight: 700;
        text-decoration: underline;
    }

    /* --------------------------------------------
       ORDER SUMMARY
       -------------------------------------------- */
    .pm-summary {
        padding: 1.2vw;
        background: #fafbfc;
        border: 0.1vw solid #f1f5f9;
        border-radius: 0.8vw;
        display: flex;
        flex-direction: column;
        gap: 0.5vw;
    }

    .pm-summary-head {
        display: flex;
        align-items: center;
        gap: 0.4vw;
        font-size: 0.75vw;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700;
        margin-bottom: 0.3vw;
        padding-bottom: 0.6vw;
        border-bottom: 0.1vw dashed #e2e8f0;
    }

    .pm-summary-head iconify-icon {
        color: #ecbc42;
        font-size: 1vw;
    }

    .pm-summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.85vw;
        color: #475569;
        gap: 1vw;
    }

    .pm-summary-row .label {
        display: flex;
        align-items: center;
        gap: 0.35vw;
    }

    .pm-summary-row .label iconify-icon {
        font-size: 0.9vw;
        color: #94a3b8;
    }

    .pm-summary-row .value {
        font-weight: 600;
        color: #334155;
        white-space: nowrap;
    }

    .pm-summary-row.is-discount .value {
        color: #059669;
    }

    .pm-summary-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 0.9vw;
        margin-top: 0.3vw;
        border-top: 0.1vw dashed #cbd5e1;
    }

    .pm-summary-total .label {
        font-size: 0.9vw;
        font-weight: 700;
        color: #0f172a;
    }

    .pm-summary-total .value {
        font-size: 1.15vw;
        font-weight: 800;
        color: rgb(102, 72, 9);
        white-space: nowrap;
    }

    /* --------------------------------------------
       PAYMENT METHODS INFO
       -------------------------------------------- */
    .pm-methods-label {
        display: flex;
        align-items: center;
        gap: 0.4vw;
        font-size: 0.75vw;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700;
        margin-bottom: 0.5vw;
    }

    .pm-methods-label iconify-icon {
        color: #ecbc42;
        font-size: 1vw;
    }

    .pm-methods {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(6vw, 1fr));
        gap: 0.5vw;
    }

    .pm-method {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.3vw;
        padding: 0.7vw 0.5vw;
        background: #ffffff;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.6vw;
        text-align: center;
        transition: all 0.2s ease;
    }

    .pm-method:hover {
        border-color: #ecbc42;
        background: #fffbf0;
        transform: translateY(-0.1vw);
    }

    .pm-method iconify-icon {
        font-size: 1.4vw;
        color: #ecbc42;
    }

    .pm-method .name {
        font-size: 0.7vw;
        font-weight: 700;
        color: #334155;
        line-height: 1.3;
    }

    .pm-method .type {
        font-size: 0.6vw;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* --------------------------------------------
       ACTIONS
       -------------------------------------------- */
    .pm-actions {
        display: flex;
        flex-direction: column;
        gap: 0.7vw;
    }

    .pm-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5vw;
        width: 100%;
        padding: 1.1vw 1.4vw;
        border-radius: 0.8vw;
        font-size: 0.9vw;
        font-weight: 700;
        cursor: pointer;
        border: 0.1vw solid transparent;
        transition: all 0.2s ease;
        text-decoration: none;
        font-family: inherit;
    }

    .pm-btn iconify-icon {
        font-size: 1.15vw;
    }

    .pm-btn-gold {
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
        color: rgb(102, 72, 9);
        box-shadow: 0 0.2vw 0.6vw rgba(236, 188, 66, 0.35);
    }
    .pm-btn-gold:hover {
        transform: translateY(-0.1vw);
        box-shadow: 0 0.4vw 1.2vw rgba(236, 188, 66, 0.5);
    }
    .pm-btn-gold:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .pm-btn-outline {
        background: #ffffff;
        border-color: #e2e8f0;
        color: #475569;
    }
    .pm-btn-outline:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }
    .pm-btn-outline:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* Loading spinner */
    .pm-spinner {
        display: inline-block;
        width: 1vw;
        height: 1vw;
        border: 0.15vw solid currentColor;
        border-top-color: transparent;
        border-radius: 50%;
        animation: pmSpin 0.8s linear infinite;
        flex-shrink: 0;
    }

    @keyframes pmSpin {
        to { transform: rotate(360deg); }
    }

    /* --------------------------------------------
       BACK LINK
       -------------------------------------------- */
    .pm-back {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4vw;
        font-size: 0.82vw;
        color: #94a3b8;
        text-decoration: none;
        padding: 0.7vw 0;
        transition: color 0.2s ease;
    }
    .pm-back:hover { color: rgb(102, 72, 9); }
    .pm-back iconify-icon { font-size: 1vw; }

    /* ============================================
       RESPONSIVE - TABLET
       ============================================ */
    @media (max-width: 1024px) {
        .pm-wrapper {
            max-width: 85vw;
            margin: 4vw auto;
            padding: 0 3vw;
        }

        .pm-card { border-radius: 3vw; border-width: 0.2vw; }

        .pm-header { padding: 4vw; }
        .pm-header::before { top: -6vw; right: -6vw; width: 20vw; height: 20vw; }
        .pm-header::after  { bottom: -4vw; left: -4vw; width: 14vw; height: 14vw; }

        .pm-header-inner { gap: 1.5vw; }

        .pm-header-icon {
            width: 9vw;
            height: 9vw;
            margin-bottom: 1vw;
        }
        .pm-header-icon iconify-icon { font-size: 5vw; }

        .pm-header h2 { font-size: 3.5vw; }
        .pm-header p { font-size: 2vw; }

        .pm-order-id {
            gap: 0.9vw;
            padding: 1vw 2.2vw;
            margin-top: 1vw;
            border-radius: 1.4vw;
            font-size: 2vw;
            border-width: 0.2vw;
        }
        .pm-order-id iconify-icon { font-size: 2.3vw; }

        .pm-status-badge {
            gap: 0.9vw;
            padding: 1vw 2.4vw;
            margin-top: 1.5vw;
            font-size: 1.9vw;
        }
        .pm-status-badge iconify-icon { font-size: 2.3vw; }

        .pm-body {
            padding: 4vw;
            gap: 3.5vw;
        }

        .pm-alert {
            gap: 1.5vw;
            padding: 2.3vw 2.8vw;
            border-radius: 1.8vw;
            font-size: 2vw;
            border-width: 0.2vw;
        }
        .pm-alert iconify-icon { font-size: 3vw; }

        .pm-summary {
            padding: 3vw;
            border-radius: 2vw;
            gap: 1.3vw;
            border-width: 0.2vw;
        }

        .pm-summary-head {
            gap: 1vw;
            font-size: 1.8vw;
            margin-bottom: 0.7vw;
            padding-bottom: 1.5vw;
            border-bottom-width: 0.2vw;
        }
        .pm-summary-head iconify-icon { font-size: 2.4vw; }

        .pm-summary-row {
            font-size: 2.1vw;
            gap: 2vw;
        }
        .pm-summary-row .label { gap: 0.9vw; }
        .pm-summary-row .label iconify-icon { font-size: 2.3vw; }

        .pm-summary-total {
            padding-top: 2.3vw;
            margin-top: 0.7vw;
            border-top-width: 0.2vw;
        }
        .pm-summary-total .label { font-size: 2.2vw; }
        .pm-summary-total .value { font-size: 2.9vw; }

        .pm-methods-label {
            gap: 1vw;
            font-size: 1.8vw;
            margin-bottom: 1.2vw;
        }
        .pm-methods-label iconify-icon { font-size: 2.4vw; }

        .pm-methods {
            grid-template-columns: repeat(auto-fit, minmax(15vw, 1fr));
            gap: 1.3vw;
        }

        .pm-method {
            gap: 0.8vw;
            padding: 1.7vw 1.2vw;
            border-radius: 1.4vw;
            border-width: 0.2vw;
        }
        .pm-method iconify-icon { font-size: 3.4vw; }
        .pm-method .name { font-size: 1.7vw; }
        .pm-method .type { font-size: 1.5vw; }

        .pm-actions { gap: 1.7vw; }

        .pm-btn {
            gap: 1.2vw;
            padding: 2.8vw 3.5vw;
            border-radius: 2vw;
            font-size: 2.2vw;
            border-width: 0.2vw;
        }
        .pm-btn iconify-icon { font-size: 2.8vw; }

        .pm-spinner {
            width: 2.5vw;
            height: 2.5vw;
            border-width: 0.4vw;
        }

        .pm-back {
            font-size: 2vw;
            gap: 1vw;
            padding: 1.7vw 0;
        }
        .pm-back iconify-icon { font-size: 2.5vw; }
    }

    /* ============================================
       RESPONSIVE - MOBILE
       ============================================ */
    @media (max-width: 480px) {
        .pm-wrapper {
            max-width: 100%;
            margin: 3vw auto;
            padding: 0 3vw;
        }

        .pm-card { border-radius: 4vw; border-width: 0.3vw; }

        .pm-header { padding: 6vw 5vw; }
        .pm-header::before { top: -8vw; right: -8vw; width: 28vw; height: 28vw; }
        .pm-header::after  { bottom: -5vw; left: -5vw; width: 20vw; height: 20vw; }

        .pm-header-inner { gap: 2.5vw; }

        .pm-header-icon {
            width: 14vw;
            height: 14vw;
            margin-bottom: 2vw;
        }
        .pm-header-icon iconify-icon { font-size: 7.5vw; }

        .pm-header h2 { font-size: 5.5vw; }
        .pm-header p { font-size: 3.2vw; }

        .pm-order-id {
            gap: 1.5vw;
            padding: 1.7vw 3.5vw;
            margin-top: 2vw;
            border-radius: 2.5vw;
            font-size: 3.2vw;
            border-width: 0.3vw;
        }
        .pm-order-id iconify-icon { font-size: 3.8vw; }

        .pm-status-badge {
            gap: 1.5vw;
            padding: 1.8vw 4vw;
            margin-top: 2.5vw;
            font-size: 3vw;
        }
        .pm-status-badge iconify-icon { font-size: 3.6vw; }

        .pm-body {
            padding: 5vw 4vw;
            gap: 5vw;
        }

        .pm-alert {
            gap: 2.5vw;
            padding: 3.5vw 4vw;
            border-radius: 2.5vw;
            font-size: 3.2vw;
            border-width: 0.3vw;
            line-height: 1.6;
        }
        .pm-alert iconify-icon {
            font-size: 4.5vw;
            margin-top: 0.3vw;
        }

        .pm-summary {
            padding: 4vw;
            border-radius: 3vw;
            gap: 2.3vw;
            border-width: 0.3vw;
        }

        .pm-summary-head {
            gap: 1.5vw;
            font-size: 2.8vw;
            margin-bottom: 1.5vw;
            padding-bottom: 3vw;
            border-bottom-width: 0.3vw;
        }
        .pm-summary-head iconify-icon { font-size: 3.6vw; }

        .pm-summary-row {
            font-size: 3.2vw;
            gap: 3vw;
        }
        .pm-summary-row .label { gap: 1.5vw; }
        .pm-summary-row .label iconify-icon { font-size: 3.6vw; }

        .pm-summary-total {
            padding-top: 3.5vw;
            margin-top: 1.5vw;
            border-top-width: 0.3vw;
            flex-wrap: wrap;
        }
        .pm-summary-total .label { font-size: 3.5vw; }
        .pm-summary-total .value { font-size: 4.5vw; }

        .pm-methods-label {
            gap: 1.5vw;
            font-size: 2.8vw;
            margin-bottom: 2.5vw;
        }
        .pm-methods-label iconify-icon { font-size: 3.6vw; }

        .pm-methods {
            grid-template-columns: repeat(3, 1fr);
            gap: 2.5vw;
        }

        .pm-method {
            gap: 1.5vw;
            padding: 3.5vw 2vw;
            border-radius: 2.5vw;
            border-width: 0.3vw;
        }
        .pm-method iconify-icon { font-size: 7vw; }
        .pm-method .name { font-size: 2.8vw; }
        .pm-method .type { font-size: 2.3vw; }

        .pm-actions { gap: 2.5vw; }

        .pm-btn {
            gap: 2vw;
            padding: 4.5vw 5vw;
            border-radius: 3vw;
            font-size: 3.4vw;
            border-width: 0.3vw;
        }
        .pm-btn iconify-icon { font-size: 4.2vw; }

        .pm-spinner {
            width: 4.5vw;
            height: 4.5vw;
            border-width: 0.6vw;
        }

        .pm-back {
            font-size: 3.2vw;
            gap: 1.5vw;
            padding: 3vw 0;
        }
        .pm-back iconify-icon { font-size: 4vw; }
    }
</style>

<div class="pm-wrapper">
    <div class="pm-card">

        {{-- ============================================ --}}
        {{-- HEADER --}}
        {{-- ============================================ --}}
        <div class="pm-header">
            <div class="pm-header-inner">
                <div class="pm-header-icon">
                    <iconify-icon icon="mdi:credit-card-outline"></iconify-icon>
                </div>
                <h2>Selesaikan Pembayaran</h2>
                <p>Pilih metode pembayaran yang tersedia</p>

                <div class="pm-order-id">
                    <iconify-icon icon="mdi:receipt-text-outline"></iconify-icon>
                    {{ $order->order_number }}
                </div>

                <span class="pm-status-badge {{ $order->payment_status }}">
                    <iconify-icon icon="
                        @if($order->payment_status === 'unpaid') mdi:clock-alert-outline
                        @elseif($order->payment_status === 'paid') mdi:check-circle
                        @elseif($order->payment_status === 'pending') mdi:clock-outline
                        @elseif($order->payment_status === 'failed') mdi:close-circle-outline
                        @else mdi:help-circle-outline @endif
                    "></iconify-icon>
                    @if($order->payment_status === 'unpaid') Belum Dibayar
                    @elseif($order->payment_status === 'paid') Lunas
                    @elseif($order->payment_status === 'pending') Menunggu
                    @elseif($order->payment_status === 'failed') Gagal
                    @else {{ ucfirst($order->payment_status) }} @endif
                </span>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- BODY --}}
        {{-- ============================================ --}}
        <div class="pm-body">

            {{-- Alerts --}}
            <div id="alert-container">
                @if(session('info'))
                    <div class="pm-alert pm-alert-info" data-session="true">
                        <iconify-icon icon="mdi:information-outline"></iconify-icon>
                        <span>{{ session('info') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="pm-alert pm-alert-error" data-session="true">
                        <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if($order->payment_status === 'paid')
                    <div class="pm-alert pm-alert-success" data-session="true">
                        <iconify-icon icon="mdi:check-circle-outline"></iconify-icon>
                        <span>
                            Pembayaran sudah berhasil!
                            <a href="{{ route('customer.checkout.success', $order) }}">Lihat detail pesanan</a>
                        </span>
                    </div>
                @endif
            </div>

            {{-- Order Summary --}}
            <div class="pm-summary">
                <div class="pm-summary-head">
                    <iconify-icon icon="mdi:receipt-text-outline"></iconify-icon>
                    Ringkasan Pesanan
                </div>

                <div class="pm-summary-row">
                    <span class="label">
                        <iconify-icon icon="mdi:cart-outline"></iconify-icon>
                        Subtotal
                    </span>
                    <span class="value">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>

                @if($order->shipping_cost > 0)
                    <div class="pm-summary-row">
                        <span class="label">
                            <iconify-icon icon="mdi:truck-delivery-outline"></iconify-icon>
                            Ongkir
                        </span>
                        <span class="value">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                @endif

                @if($order->discount > 0)
                    <div class="pm-summary-row is-discount">
                        <span class="label">
                            <iconify-icon icon="mdi:tag-outline"></iconify-icon>
                            Diskon
                        </span>
                        <span class="value">-Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                    </div>
                @endif

                <div class="pm-summary-total">
                    <span class="label">Total</span>
                    <span class="value">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Actions --}}
            <div class="pm-actions">
                <button id="midtrans-button" type="button" class="pm-btn pm-btn-gold">
                    <span id="btn-text">
                        <iconify-icon icon="mdi:credit-card-outline"></iconify-icon>
                        Bayar Sekarang
                    </span>
                </button>

                <button id="change-method-button" type="button" class="pm-btn pm-btn-outline" style="display: none;">
                    <iconify-icon icon="mdi:refresh"></iconify-icon>
                    Ganti Metode Pembayaran
                </button>
            </div>

            <a href="{{ route('customer.orders.show', $order) }}" class="pm-back">
                <iconify-icon icon="mdi:arrow-left"></iconify-icon>
                Kembali ke Detail Pesanan
            </a>
        </div>
    </div>
</div>

{{-- ============================================ --}}
{{-- MIDTRANS SNAP SCRIPT --}}
{{-- ============================================ --}}
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 🔥 STATE
    let currentSnapToken = @json($snapToken);
    const orderId = @json($order->order_number);
    const paymentStatus = @json($order->payment_status);

    // 🔥 ELEMEN
    const btn = document.getElementById('midtrans-button');
    const btnText = document.getElementById('btn-text');
    const btnChange = document.getElementById('change-method-button');
    const alertContainer = document.getElementById('alert-container');

    // 🔥 JIKA SUDAH LUNAS
    if (paymentStatus === 'paid') {
        btn.disabled = true;
        btnText.innerHTML = '<iconify-icon icon="mdi:check-circle"></iconify-icon> Pembayaran Selesai';
        btnChange.style.display = 'none';
        return;
    }

    let isProcessing = false;
    let checkInterval = null;
    let checkCount = 0;
    const maxChecks = 30;

    // 🔥 SHOW ALERT
    function showAlert(message, type = 'info') {
        const types = {
            info:    { className: 'pm-alert-info',    icon: 'mdi:information-outline' },
            error:   { className: 'pm-alert-error',   icon: 'mdi:alert-circle-outline' },
            warning: { className: 'pm-alert-warning', icon: 'mdi:alert-outline' },
            success: { className: 'pm-alert-success', icon: 'mdi:check-circle-outline' }
        };
        const style = types[type] || types.info;

        // Hapus alert non-session
        const oldAlerts = alertContainer.querySelectorAll('.pm-alert:not([data-session])');
        oldAlerts.forEach(el => el.remove());

        const alert = document.createElement('div');
        alert.className = 'pm-alert ' + style.className;
        alert.innerHTML = `
            <iconify-icon icon="${style.icon}"></iconify-icon>
            <span>${message}</span>
        `;
        alertContainer.prepend(alert);
    }

    // 🔥 UPDATE BUTTON STATE
    function updateButton(state) {
        if (state === 'loading') {
            btn.disabled = true;
            btnChange.disabled = true;
            btnText.innerHTML = '<span class="pm-spinner"></span> Menghubungkan...';
        } else if (state === 'pending') {
            btn.disabled = false;
            btnChange.disabled = false;
            btnText.innerHTML = '<iconify-icon icon="mdi:arrow-right"></iconify-icon> Lanjutkan Pembayaran';
            btnChange.style.display = 'flex';
        } else {
            btn.disabled = false;
            btnChange.disabled = false;
            btnText.innerHTML = '<iconify-icon icon="mdi:credit-card-outline"></iconify-icon> Bayar Sekarang';
            btnChange.style.display = 'none';
        }
    }

    // 🔥 CEK SESSION LAMA
    const hasOpenedPopup = localStorage.getItem('midtrans_popup_opened_' + orderId) === 'true';

    if (hasOpenedPopup) {
        updateButton('pending');
        showAlert('Anda memiliki sesi pembayaran yang tertunda. Klik "Lanjutkan Pembayaran" atau "Ganti Metode".', 'warning');
    } else {
        showAlert('Pilih metode pembayaran di popup Midtrans.', 'info');
    }

    // 🔥 OPEN SNAP POPUP
    function openSnapPopup() {
        if (typeof window.snap === 'undefined') {
            showAlert('Midtrans Snap tidak terload. Coba refresh halaman.', 'error');
            isProcessing = false;
            updateButton('pending');
            return;
        }

        window.snap.pay(currentSnapToken, {
            onSuccess: function(result) {
                localStorage.removeItem('midtrans_popup_opened_' + orderId);
                showAlert('Pembayaran berhasil! Mengalihkan...', 'success');
                window.location.href = '{{ route("customer.midtrans.finish") }}?order_id=' + orderId + '&status=success';
            },
            onPending: function(result) {
                isProcessing = false;
                localStorage.setItem('midtrans_popup_opened_' + orderId, 'true');
                updateButton('pending');
                showAlert('Pembayaran pending. Silakan selesaikan atau ganti metode pembayaran.', 'warning');
            },
            onError: function(result) {
                isProcessing = false;
                localStorage.setItem('midtrans_popup_opened_' + orderId, 'true');
                updateButton('pending');
                showAlert('Pembayaran gagal: ' + (result.status_message || 'Silakan coba lagi.'), 'error');
            },
            onClose: function() {
                isProcessing = false;
                localStorage.setItem('midtrans_popup_opened_' + orderId, 'true');
                updateButton('pending');
                showAlert('Popup ditutup. Klik "Lanjutkan Pembayaran" untuk melanjutkan.', 'warning');
            }
        });
    }

    // 🔥 REFRESH TOKEN
    async function refreshSnapToken() {
        try {
            const response = await fetch('{{ route("customer.midtrans.refresh-token", $order) }}', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await response.json();

            if (data.success) {
                currentSnapToken = data.snap_token;
                localStorage.removeItem('midtrans_popup_opened_' + orderId);
                showAlert('Token berhasil diperbarui. Silakan pilih metode pembayaran.', 'success');
                return true;
            } else {
                showAlert('Gagal memperbarui token: ' + (data.message || 'Error server'), 'error');
                return false;
            }
        } catch (error) {
            showAlert('Terjadi kesalahan jaringan. Coba lagi.', 'error');
            return false;
        }
    }

    // 🔥 TOMBOL 1: BAYAR / LANJUTKAN
    btn.addEventListener('click', async function() {
        if (isProcessing) return;
        isProcessing = true;
        updateButton('loading');

        try {
            const checkResponse = await fetch('{{ route("customer.midtrans.check-status", $order) }}');
            const checkData = await checkResponse.json();

            if (checkData.paid) {
                window.location.href = '{{ route("customer.checkout.success", $order) }}';
                return;
            }
        } catch(e) {}

        openSnapPopup();
    });

    // 🔥 TOMBOL 2: GANTI METODE
    btnChange.addEventListener('click', async function() {
        if (isProcessing) return;
        isProcessing = true;
        updateButton('loading');

        const success = await refreshSnapToken();
        if (success) {
            openSnapPopup();
        } else {
            isProcessing = false;
            updateButton('pending');
        }
    });

    // 🔥 CEK STATUS PERIODIK
    function checkOrderStatus() {
        if (checkCount >= maxChecks) {
            clearInterval(checkInterval);
            return;
        }
        checkCount++;

        fetch('{{ route("customer.midtrans.check-status", $order) }}')
            .then(response => response.json())
            .then(data => {
                if (data.paid) {
                    clearInterval(checkInterval);
                    localStorage.removeItem('midtrans_popup_opened_' + orderId);
                    showAlert('Pembayaran berhasil! Mengalihkan...', 'success');
                    setTimeout(() => {
                        window.location.href = '{{ route("customer.checkout.success", $order) }}';
                    }, 1500);
                }
            })
            .catch(() => {});
    }

    checkInterval = setInterval(checkOrderStatus, 5000);

    // 🔥 CLEANUP
    window.addEventListener('beforeunload', function() {
        if (checkInterval) {
            clearInterval(checkInterval);
        }
    });

    // 🔥 LOG
    console.log('🔍 Midtrans payment page loaded');
    console.log('📦 Order ID:', orderId);
    console.log('🔑 Snap Token:', currentSnapToken ? currentSnapToken.substring(0, 20) + '...' : 'null');
    console.log('📊 Payment Status:', paymentStatus);
});
</script>
@endsection