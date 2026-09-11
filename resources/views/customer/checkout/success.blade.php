@extends('layouts.customer')

@section('title', 'Pesanan Berhasil - ' . config('app.name'))

@section('content')

<style>
    /* ============================================
       SUCCESS PAGE STYLES
       ============================================ */
    .sc-container {
        max-width: 44vw;
        margin: 2vw auto;
        padding: 0 1.5vw;
    }

    .sc-card {
        background: #ffffff;
        border: 0.1vw solid #e2e8f0;
        border-radius: 1.2vw;
        box-shadow: 0 0.5vw 2vw rgba(0, 0, 0, 0.06);
        padding: 2.5vw 2vw;
        position: relative;
        overflow: hidden;
    }

    .sc-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 0.35vw;
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
    }

    /* --------------------------------------------
       ICON SUCCESS
       -------------------------------------------- */
    .sc-icon-wrapper {
        display: flex;
        justify-content: center;
        margin-bottom: 1.5vw;
    }

    .sc-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 7vw;
        height: 7vw;
        border-radius: 50%;
        background: linear-gradient(135deg, #FDDD57 0%, #ecbc42 100%);
        color: rgb(102, 72, 9);
        box-shadow: 0 0 0 0.5vw #fffbf0, 0 0.5vw 2vw rgba(236, 188, 66, 0.35);
        animation: scPulse 2s ease-in-out infinite;
    }

    .sc-icon iconify-icon {
        font-size: 4vw;
    }

    @keyframes scPulse {
        0%   { transform: scale(1);    box-shadow: 0 0 0 0.5vw #fffbf0, 0 0.5vw 2vw rgba(236, 188, 66, 0.35); }
        50%  { transform: scale(1.05); box-shadow: 0 0 0 0.8vw #fffbf0, 0 0.8vw 2.5vw rgba(236, 188, 66, 0.45); }
        100% { transform: scale(1);    box-shadow: 0 0 0 0.5vw #fffbf0, 0 0.5vw 2vw rgba(236, 188, 66, 0.35); }
    }

    /* --------------------------------------------
       HEADER
       -------------------------------------------- */
    .sc-header {
        text-align: center;
        margin-bottom: 1.8vw;
    }

    .sc-header h1 {
        font-size: 1.8vw;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
        margin-bottom: 0.4vw;
    }

    .sc-header p {
        font-size: 0.9vw;
        color: #94a3b8;
        line-height: 1.5;
    }

    .sc-order-id {
        display: inline-flex;
        align-items: center;
        gap: 0.35vw;
        padding: 0.4vw 1vw;
        margin-top: 0.8vw;
        background: #fafbfc;
        border: 0.1vw dashed #ecbc42;
        border-radius: 0.5vw;
        font-family: 'Courier New', monospace;
        font-size: 0.8vw;
        font-weight: 800;
        color: rgb(102, 72, 9);
        letter-spacing: 0.05em;
    }

    .sc-order-id iconify-icon {
        font-size: 0.95vw;
        opacity: 0.7;
    }

    /* --------------------------------------------
       SECTIONS
       -------------------------------------------- */
    .sc-section {
        background: #fafbfc;
        border: 0.1vw solid #f1f5f9;
        border-radius: 0.8vw;
        padding: 1.2vw;
        margin-bottom: 1.2vw;
    }

    .sc-section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5vw;
        padding-bottom: 0.8vw;
        margin-bottom: 0.8vw;
        border-bottom: 0.1vw dashed #e2e8f0;
    }

    .sc-section-title {
        display: flex;
        align-items: center;
        gap: 0.4vw;
        font-size: 0.85vw;
        font-weight: 800;
        color: #0f172a;
    }

    .sc-section-title iconify-icon {
        font-size: 1vw;
        color: #ecbc42;
    }

    .sc-section-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3vw;
        padding: 0.25vw 0.7vw;
        border-radius: 100vw;
        background: #ffffff;
        border: 0.1vw solid #e2e8f0;
        font-size: 0.7vw;
        font-weight: 600;
        color: #64748b;
    }

    .sc-section-badge iconify-icon {
        font-size: 0.8vw;
    }

    /* --------------------------------------------
       SUMMARY ROWS
       -------------------------------------------- */
    .sc-summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1vw;
        padding: 0.4vw 0;
        font-size: 0.85vw;
    }

    .sc-summary-row .label {
        display: flex;
        align-items: center;
        gap: 0.35vw;
        color: #64748b;
    }

    .sc-summary-row .label iconify-icon {
        font-size: 0.9vw;
        color: #94a3b8;
    }

    .sc-summary-row .value {
        font-weight: 600;
        color: #334155;
        white-space: nowrap;
    }

    .sc-summary-row.is-discount .value {
        color: #059669;
    }

    .sc-summary-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1vw;
        padding-top: 0.9vw;
        margin-top: 0.5vw;
        border-top: 0.1vw dashed #cbd5e1;
    }

    .sc-summary-total .label {
        font-size: 0.9vw;
        font-weight: 800;
        color: #0f172a;
    }

    .sc-summary-total .value {
        font-size: 1.15vw;
        font-weight: 800;
        color: rgb(102, 72, 9);
        white-space: nowrap;
    }

    /* --------------------------------------------
       SHIPPING
       -------------------------------------------- */
    .sc-shipping {
        font-size: 0.82vw;
        color: #475569;
        line-height: 1.7;
    }

    .sc-shipping .name {
        display: flex;
        align-items: center;
        gap: 0.35vw;
        font-size: 0.88vw;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 0.3vw;
    }

    .sc-shipping .name iconify-icon {
        font-size: 1vw;
        color: #ecbc42;
    }

    .sc-shipping .row {
        display: flex;
        align-items: flex-start;
        gap: 0.5vw;
        padding: 0.15vw 0;
    }

    .sc-shipping .row iconify-icon {
        font-size: 0.95vw;
        color: #94a3b8;
        flex-shrink: 0;
        margin-top: 0.25vw;
    }

    /* --------------------------------------------
       ITEMS
       -------------------------------------------- */
    .sc-items {
        display: flex;
        flex-direction: column;
    }

    .sc-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1vw;
        padding: 0.8vw 0;
        border-bottom: 0.1vw solid #f1f5f9;
    }

    .sc-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .sc-item:first-child {
        padding-top: 0;
    }

    .sc-item-info {
        display: flex;
        align-items: center;
        gap: 0.8vw;
        flex: 1;
        min-width: 0;
    }

    .sc-item-img {
        width: 3.5vw;
        height: 3.5vw;
        border-radius: 0.6vw;
        background: #ffffff;
        overflow: hidden;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 0.1vw solid #e2e8f0;
    }

    .sc-item-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .sc-item-img .placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
    }

    .sc-item-img .placeholder iconify-icon {
        font-size: 1.6vw;
        color: #cbd5e1;
    }

    .sc-item-details {
        flex: 1;
        min-width: 0;
    }

    .sc-item-name {
        font-size: 0.85vw;
        font-weight: 700;
        color: #0f172a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sc-item-variant {
        display: inline-flex;
        align-items: center;
        gap: 0.25vw;
        font-size: 0.72vw;
        color: #94a3b8;
        margin-top: 0.15vw;
    }

    .sc-item-variant iconify-icon {
        font-size: 0.8vw;
    }

    .sc-item-meta {
        display: flex;
        align-items: center;
        gap: 0.6vw;
        margin-top: 0.3vw;
        font-size: 0.72vw;
        color: #94a3b8;
    }

    .sc-item-price {
        font-size: 0.9vw;
        font-weight: 800;
        color: #0f172a;
        white-space: nowrap;
        flex-shrink: 0;
    }

    /* --------------------------------------------
       ACTIONS
       -------------------------------------------- */
    .sc-actions {
        display: flex;
        flex-direction: column;
        gap: 0.6vw;
        margin-top: 1.5vw;
        padding-top: 1.5vw;
        border-top: 0.1vw solid #f1f5f9;
    }

    .sc-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5vw;
        width: 100%;
        padding: 0.95vw 1.4vw;
        border-radius: 0.7vw;
        font-size: 0.88vw;
        font-weight: 700;
        cursor: pointer;
        border: 0.1vw solid transparent;
        transition: all 0.2s ease;
        text-decoration: none;
        font-family: inherit;
    }

    .sc-btn iconify-icon {
        font-size: 1.1vw;
    }

    .sc-btn-gold {
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
        color: rgb(102, 72, 9);
        box-shadow: 0 0.15vw 0.5vw rgba(236, 188, 66, 0.3);
    }

    .sc-btn-gold:hover {
        transform: translateY(-0.1vw);
        box-shadow: 0 0.35vw 1.2vw rgba(236, 188, 66, 0.5);
    }

    .sc-btn-outline {
        background: #ffffff;
        border-color: #e2e8f0;
        color: #475569;
    }

    .sc-btn-outline:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .sc-btn-ghost {
        background: transparent;
        border-color: transparent;
        color: #94a3b8;
    }

    .sc-btn-ghost:hover {
        color: rgb(102, 72, 9);
        background: #fffbf0;
    }

    /* --------------------------------------------
       CONFETTI
       -------------------------------------------- */
    .sc-confetti-container {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        pointer-events: none;
        z-index: 9999;
        overflow: hidden;
    }

    .sc-confetti {
        position: absolute;
        animation: confetti-fall linear forwards;
    }

    @keyframes confetti-fall {
        0% {
            transform: translateY(-10vh) rotate(0deg);
            opacity: 1;
        }
        100% {
            transform: translateY(110vh) rotate(720deg);
            opacity: 0;
        }
    }

    /* ============================================
       RESPONSIVE - TABLET
       ============================================ */
    @media (max-width: 1024px) {
        .sc-container {
            max-width: 85vw;
            margin: 4vw auto;
            padding: 0 3vw;
        }

        .sc-card {
            padding: 5vw 4vw;
            border-radius: 3vw;
            border-width: 0.2vw;
        }
        .sc-card::before { height: 0.7vw; }

        .sc-icon-wrapper { margin-bottom: 3.5vw; }

        .sc-icon {
            width: 16vw;
            height: 16vw;
            box-shadow: 0 0 0 1.2vw #fffbf0, 0 1vw 4vw rgba(236, 188, 66, 0.35);
        }
        .sc-icon iconify-icon { font-size: 9vw; }

        @keyframes scPulse {
            0%   { transform: scale(1);    box-shadow: 0 0 0 1.2vw #fffbf0, 0 1vw 4vw rgba(236, 188, 66, 0.35); }
            50%  { transform: scale(1.05); box-shadow: 0 0 0 1.8vw #fffbf0, 0 1.5vw 5vw rgba(236, 188, 66, 0.45); }
            100% { transform: scale(1);    box-shadow: 0 0 0 1.2vw #fffbf0, 0 1vw 4vw rgba(236, 188, 66, 0.35); }
        }

        .sc-header { margin-bottom: 4vw; }
        .sc-header h1 { font-size: 4vw; margin-bottom: 1vw; }
        .sc-header p { font-size: 2.1vw; }

        .sc-order-id {
            gap: 1vw;
            padding: 1vw 2.2vw;
            margin-top: 2vw;
            border-radius: 1.4vw;
            font-size: 2vw;
            border-width: 0.2vw;
        }
        .sc-order-id iconify-icon { font-size: 2.3vw; }

        .sc-section {
            padding: 3vw;
            border-radius: 2vw;
            margin-bottom: 3vw;
            border-width: 0.2vw;
        }

        .sc-section-head {
            gap: 1.2vw;
            padding-bottom: 2vw;
            margin-bottom: 2vw;
            border-bottom-width: 0.2vw;
        }

        .sc-section-title {
            gap: 1vw;
            font-size: 2.1vw;
        }
        .sc-section-title iconify-icon { font-size: 2.5vw; }

        .sc-section-badge {
            gap: 0.7vw;
            padding: 0.7vw 1.7vw;
            font-size: 1.8vw;
            border-radius: 100vw;
            border-width: 0.2vw;
        }
        .sc-section-badge iconify-icon { font-size: 2vw; }

        .sc-summary-row {
            gap: 2vw;
            padding: 1vw 0;
            font-size: 2.1vw;
        }
        .sc-summary-row .label { gap: 0.9vw; }
        .sc-summary-row .label iconify-icon { font-size: 2.3vw; }

        .sc-summary-total {
            gap: 2vw;
            padding-top: 2.3vw;
            margin-top: 1.2vw;
            border-top-width: 0.2vw;
        }
        .sc-summary-total .label { font-size: 2.2vw; }
        .sc-summary-total .value { font-size: 2.9vw; }

        .sc-shipping { font-size: 2vw; line-height: 1.8; }
        .sc-shipping .name {
            gap: 0.9vw;
            font-size: 2.2vw;
            margin-bottom: 1vw;
        }
        .sc-shipping .name iconify-icon { font-size: 2.5vw; }
        .sc-shipping .row {
            gap: 1.3vw;
            padding: 0.5vw 0;
        }
        .sc-shipping .row iconify-icon {
            font-size: 2.3vw;
            margin-top: 0.6vw;
        }

        .sc-item {
            gap: 2.5vw;
            padding: 2.3vw 0;
            border-bottom-width: 0.2vw;
        }

        .sc-item-info { gap: 2vw; }

        .sc-item-img {
            width: 10vw;
            height: 10vw;
            border-radius: 1.7vw;
            border-width: 0.2vw;
        }
        .sc-item-img .placeholder iconify-icon { font-size: 5vw; }

        .sc-item-name { font-size: 2.1vw; }
        .sc-item-variant {
            gap: 0.7vw;
            font-size: 1.8vw;
            margin-top: 0.5vw;
        }
        .sc-item-variant iconify-icon { font-size: 2vw; }

        .sc-item-meta {
            gap: 1.7vw;
            margin-top: 0.7vw;
            font-size: 1.8vw;
        }

        .sc-item-price { font-size: 2.2vw; }

        .sc-actions {
            gap: 1.7vw;
            margin-top: 4vw;
            padding-top: 4vw;
            border-top-width: 0.2vw;
        }

        .sc-btn {
            gap: 1.2vw;
            padding: 2.5vw 3.5vw;
            border-radius: 2vw;
            font-size: 2.2vw;
            border-width: 0.2vw;
        }
        .sc-btn iconify-icon { font-size: 2.7vw; }
    }

    /* ============================================
       RESPONSIVE - MOBILE
       ============================================ */
    @media (max-width: 480px) {
        .sc-container {
            max-width: 100%;
            margin: 3vw auto;
            padding: 0 3vw;
        }

        .sc-card {
            padding: 8vw 5vw 5vw;
            border-radius: 4vw;
            border-width: 0.3vw;
        }
        .sc-card::before { height: 1vw; }

        .sc-icon-wrapper { margin-bottom: 5vw; }

        .sc-icon {
            width: 28vw;
            height: 28vw;
            box-shadow: 0 0 0 2vw #fffbf0, 0 2vw 6vw rgba(236, 188, 66, 0.35);
        }
        .sc-icon iconify-icon { font-size: 16vw; }

        @keyframes scPulse {
            0%   { transform: scale(1);    box-shadow: 0 0 0 2vw #fffbf0, 0 2vw 6vw rgba(236, 188, 66, 0.35); }
            50%  { transform: scale(1.05); box-shadow: 0 0 0 3vw #fffbf0, 0 3vw 7vw rgba(236, 188, 66, 0.45); }
            100% { transform: scale(1);    box-shadow: 0 0 0 2vw #fffbf0, 0 2vw 6vw rgba(236, 188, 66, 0.35); }
        }

        .sc-header { margin-bottom: 6vw; }
        .sc-header h1 { font-size: 6.5vw; margin-bottom: 1.5vw; }
        .sc-header p { font-size: 3.2vw; }

        .sc-order-id {
            gap: 1.5vw;
            padding: 1.7vw 3.5vw;
            margin-top: 3.5vw;
            border-radius: 2.5vw;
            font-size: 3.2vw;
            border-width: 0.3vw;
        }
        .sc-order-id iconify-icon { font-size: 3.8vw; }

        .sc-section {
            padding: 4.5vw;
            border-radius: 3vw;
            margin-bottom: 4.5vw;
            border-width: 0.3vw;
        }

        .sc-section-head {
            gap: 2vw;
            padding-bottom: 3.5vw;
            margin-bottom: 3.5vw;
            border-bottom-width: 0.3vw;
        }

        .sc-section-title {
            gap: 1.5vw;
            font-size: 3.4vw;
        }
        .sc-section-title iconify-icon { font-size: 4.2vw; }

        .sc-section-badge {
            gap: 1vw;
            padding: 1.2vw 2.8vw;
            font-size: 2.8vw;
            border-radius: 100vw;
            border-width: 0.3vw;
        }
        .sc-section-badge iconify-icon { font-size: 3.2vw; }

        .sc-summary-row {
            gap: 3vw;
            padding: 1.7vw 0;
            font-size: 3.2vw;
        }
        .sc-summary-row .label { gap: 1.5vw; }
        .sc-summary-row .label iconify-icon { font-size: 3.6vw; }

        .sc-summary-total {
            gap: 3vw;
            padding-top: 3.5vw;
            margin-top: 2vw;
            border-top-width: 0.3vw;
            flex-wrap: wrap;
        }
        .sc-summary-total .label { font-size: 3.5vw; }
        .sc-summary-total .value { font-size: 4.5vw; }

        .sc-shipping { font-size: 3.2vw; line-height: 1.8; }
        .sc-shipping .name {
            gap: 1.5vw;
            font-size: 3.5vw;
            margin-bottom: 1.8vw;
        }
        .sc-shipping .name iconify-icon { font-size: 4.2vw; }
        .sc-shipping .row {
            gap: 2vw;
            padding: 0.8vw 0;
        }
        .sc-shipping .row iconify-icon {
            font-size: 3.6vw;
            margin-top: 1vw;
        }

        .sc-item {
            gap: 3vw;
            padding: 4vw 0;
            border-bottom-width: 0.3vw;
            flex-wrap: wrap;
        }

        .sc-item-info {
            gap: 3vw;
            width: 100%;
        }

        .sc-item-img {
            width: 16vw;
            height: 16vw;
            border-radius: 2.5vw;
            border-width: 0.3vw;
        }
        .sc-item-img .placeholder iconify-icon { font-size: 8vw; }

        .sc-item-name { font-size: 3.4vw; white-space: normal; }
        .sc-item-variant {
            gap: 1vw;
            font-size: 2.8vw;
            margin-top: 1vw;
        }
        .sc-item-variant iconify-icon { font-size: 3.2vw; }

        .sc-item-meta {
            gap: 2.5vw;
            margin-top: 1.2vw;
            font-size: 2.8vw;
        }

        .sc-item-price {
            font-size: 3.8vw;
            width: 100%;
            text-align: right;
            margin-top: 1.5vw;
        }

        .sc-actions {
            gap: 2.5vw;
            margin-top: 6vw;
            padding-top: 6vw;
            border-top-width: 0.3vw;
        }

        .sc-btn {
            gap: 1.8vw;
            padding: 3.8vw 4vw;
            border-radius: 3vw;
            font-size: 3.2vw;
            border-width: 0.3vw;
        }
        .sc-btn iconify-icon { font-size: 4.2vw; }
    }
</style>

<div class="sc-container">
    <div class="sc-card">

        {{-- ICON SUCCESS --}}
        <div class="sc-icon-wrapper">
            <div class="sc-icon">
                <iconify-icon icon="mdi:check-circle"></iconify-icon>
            </div>
        </div>

        {{-- HEADER --}}
        <div class="sc-header">
            <h1>Pesanan Berhasil!</h1>
            <p>Terima kasih telah berbelanja di {{ config('app.name') }}.</p>
            <div class="sc-order-id">
                <iconify-icon icon="mdi:receipt-text-outline"></iconify-icon>
                #{{ $order->order_number }}
            </div>
        </div>

        {{-- ORDER SUMMARY --}}
        <div class="sc-section">
            <div class="sc-section-head">
                <div class="sc-section-title">
                    <iconify-icon icon="mdi:receipt-text-outline"></iconify-icon>
                    Ringkasan Pembayaran
                </div>
            </div>

            <div class="sc-summary-row">
                <span class="label">
                    <iconify-icon icon="mdi:cart-outline"></iconify-icon>
                    Subtotal
                </span>
                <span class="value">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>

            @if($order->shipping_cost > 0)
                <div class="sc-summary-row">
                    <span class="label">
                        <iconify-icon icon="mdi:truck-delivery-outline"></iconify-icon>
                        Ongkir
                    </span>
                    <span class="value">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                </div>
            @endif

            @if($order->discount > 0)
                <div class="sc-summary-row is-discount">
                    <span class="label">
                        <iconify-icon icon="mdi:tag-outline"></iconify-icon>
                        Diskon
                    </span>
                    <span class="value">-Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                </div>
            @endif

            <div class="sc-summary-total">
                <span class="label">Total</span>
                <span class="value">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- SHIPPING INFO --}}
        @if($order->shipping_name)
            <div class="sc-section">
                <div class="sc-section-head">
                    <div class="sc-section-title">
                        <iconify-icon icon="mdi:map-marker-outline"></iconify-icon>
                        Alamat Pengiriman
                    </div>
                </div>

                <div class="sc-shipping">
                    <div class="name">
                        <iconify-icon icon="mdi:account-circle-outline"></iconify-icon>
                        {{ $order->shipping_name }}
                    </div>

                    <div class="row">
                        <iconify-icon icon="mdi:home-outline"></iconify-icon>
                        <span>{{ $order->shipping_address }}</span>
                    </div>

                    @if($order->shipping_city)
                        <div class="row">
                            <iconify-icon icon="mdi:city-variant-outline"></iconify-icon>
                            <span>
                                {{ $order->shipping_city }}{{ $order->shipping_province ? ', ' . $order->shipping_province : '' }}
                            </span>
                        </div>
                    @endif

                    @if($order->shipping_postal_code && $order->shipping_postal_code !== '0')
                        <div class="row">
                            <iconify-icon icon="mdi:mailbox-outline"></iconify-icon>
                            <span>Kode Pos: {{ $order->shipping_postal_code }}</span>
                        </div>
                    @endif

                    @if($order->shipping_phone)
                        <div class="row">
                            <iconify-icon icon="mdi:phone-outline"></iconify-icon>
                            <span>{{ $order->shipping_phone }}</span>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- ORDER ITEMS --}}
        <div class="sc-section">
            <div class="sc-section-head">
                <div class="sc-section-title">
                    <iconify-icon icon="mdi:shopping-outline"></iconify-icon>
                    Item Pesanan
                </div>
                <div class="sc-section-badge">
                    <iconify-icon icon="mdi:package-variant"></iconify-icon>
                    {{ $order->items->count() }} produk
                </div>
            </div>

            <div class="sc-items">
                @foreach ($order->items as $item)
                    <div class="sc-item">
                        <div class="sc-item-info">
                            <div class="sc-item-img">
                                @if ($item->product && $item->product->images->first())
                                    <img src="{{ Storage::url($item->product->images->first()->image) }}"
                                         alt="{{ $item->product_name }}"
                                         loading="lazy"
                                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                                    <div class="placeholder" style="display:none;">
                                        <iconify-icon icon="mdi:image-off-outline"></iconify-icon>
                                    </div>
                                @else
                                    <div class="placeholder">
                                        <iconify-icon icon="mdi:image-off-outline"></iconify-icon>
                                    </div>
                                @endif
                            </div>

                            <div class="sc-item-details">
                                <div class="sc-item-name" title="{{ $item->product_name }}">
                                    {{ $item->product_name }}
                                </div>

                                @if ($item->variant_name)
                                    <div class="sc-item-variant">
                                        <iconify-icon icon="mdi:tag-outline"></iconify-icon>
                                        {{ $item->variant_name }}
                                    </div>
                                @endif

                                <div class="sc-item-meta">
                                    <span>{{ $item->quantity }}x</span>
                                    <span>@ Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="sc-item-price">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="sc-actions">
            <a href="{{ route('customer.orders.show', $order) }}" class="sc-btn sc-btn-gold">
                <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                Lihat Detail Pesanan
            </a>
            <a href="{{ route('customer.orders') }}" class="sc-btn sc-btn-outline">
                <iconify-icon icon="mdi:history"></iconify-icon>
                Riwayat Pesanan
            </a>
            <a href="{{ route('customer.home') }}" class="sc-btn sc-btn-ghost">
                <iconify-icon icon="mdi:arrow-left"></iconify-icon>
                Lanjut Belanja
            </a>
        </div>

    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 🔥 CONFETTI - Palet emas + aksen
        const colors = ['#FDDD57', '#ecbc42', '#d4a72e', '#f59e0b', '#22c55e', '#3b82f6', '#ec4899'];
        const container = document.createElement('div');
        container.className = 'sc-confetti-container';
        document.body.appendChild(container);

        for (let i = 0; i < 60; i++) {
            const confetti = document.createElement('div');
            confetti.className = 'sc-confetti';

            const size = Math.random() * 8 + 4;
            const color = colors[Math.floor(Math.random() * colors.length)];
            const left = Math.random() * 100;
            const duration = Math.random() * 3 + 2;
            const delay = Math.random() * 2;

            confetti.style.cssText = `
                left: ${left}%;
                width: ${size}px;
                height: ${size}px;
                background: ${color};
                border-radius: ${Math.random() > 0.5 ? '50%' : '2px'};
                animation-duration: ${duration}s;
                animation-delay: ${delay}s;
            `;

            container.appendChild(confetti);

            setTimeout(() => confetti.remove(), (duration + delay) * 1000 + 500);
        }

        setTimeout(() => container.remove(), 6000);
    });
</script>
@endpush

@endsection