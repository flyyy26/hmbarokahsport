@extends('layouts.account')

@section('title', 'Tracking Pesanan - ' . config('app.name'))
@section('page-title', 'Tracking Pengiriman')
@section('page-subtitle', 'Pantau status pengiriman pesanan Anda.')

@section('account-content')

<style>
    /* ============================================
       TRACKING PAGE STYLES
       ============================================ */
    .tk-wrapper {
        display: flex;
        flex-direction: column;
        gap: 1.5vw;
        margin-top:1vw;
        max-width: 100%;
    }

    /* --------------------------------------------
       ALERT
       -------------------------------------------- */
    .tk-alert {
        display: flex;
        align-items: center;
        gap: 0.6vw;
        padding: 1vw 1.2vw;
        border-radius: 0.7vw;
        font-size: 0.85vw;
        border: 0.1vw solid transparent;
    }

    .tk-alert iconify-icon {
        font-size: 1.2vw;
        flex-shrink: 0;
    }

    .tk-alert-danger {
        background: #fef2f2;
        border-color: #fecaca;
        color: #b91c1c;
    }

    .tk-alert-warning {
        background: #fffbeb;
        border-color: #fde68a;
        color: #b45309;
    }

    /* --------------------------------------------
       STATUS CARD
       -------------------------------------------- */
    .tk-status-card {
        background: #ffffff;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.9vw;
        overflow: hidden;
        position: relative;
    }

    .tk-status-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 0.35vw;
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
    }

    .tk-status-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1vw;
        flex-wrap: wrap;
        padding: 1.3vw 1.4vw;
        border-bottom: 0.1vw solid #f1f5f9;
    }

    .tk-order-num {
        font-size: 1.1vw;
        font-weight: 800;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 0.4vw;
    }

    .tk-order-num iconify-icon {
        color: #ecbc42;
        font-size: 1.2vw;
    }

    .tk-order-date {
        display: flex;
        align-items: center;
        gap: 0.35vw;
        font-size: 0.75vw;
        color: #94a3b8;
        margin-top: 0.3vw;
    }

    .tk-order-date iconify-icon {
        font-size: 0.9vw;
    }

    /* Status Badge */
    .tk-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4vw;
        padding: 0.55vw 1vw;
        border-radius: 100vw;
        font-size: 0.8vw;
        font-weight: 700;
        border: 0.1vw solid transparent;
        white-space: nowrap;
    }

    .tk-status-badge iconify-icon {
        font-size: 1vw;
    }

    .tk-status-pending    { background: #fffbeb; color: #b45309; border-color: #fde68a; }
    .tk-status-processing { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
    .tk-status-shipped    { background: #fff7ed; color: #c2410c; border-color: #fed7aa; }
    .tk-status-delivered  { background: #ecfdf5; color: #047857; border-color: #a7f3d0; }
    .tk-status-cancelled  { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }

    /* Info Grid */
    .tk-status-info {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0;
    }

    .tk-info-item {
        padding: 1.1vw 1.4vw;
        border-right: 0.1vw solid #f1f5f9;
        min-width: 0;
    }

    .tk-info-item:last-child {
        border-right: none;
    }

    .tk-info-label {
        display: flex;
        align-items: center;
        gap: 0.3vw;
        font-size: 0.7vw;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 600;
    }

    .tk-info-label iconify-icon {
        font-size: 0.85vw;
    }

    .tk-info-value {
        font-size: 0.88vw;
        font-weight: 700;
        color: #0f172a;
        margin-top: 0.3vw;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* --------------------------------------------
       TIMELINE CARD
       -------------------------------------------- */
    .tk-timeline-card {
        background: #ffffff;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.9vw;
        overflow: hidden;
    }

    .tk-section-head {
        display: flex;
        align-items: center;
        gap: 0.5vw;
        padding: 0.9vw 1.2vw;
        border-bottom: 0.1vw solid #f1f5f9;
        background: #fafbfc;
        font-size: 0.85vw;
        font-weight: 700;
        color: #0f172a;
    }

    .tk-section-head iconify-icon {
        color: #ecbc42;
        font-size: 1.1vw;
    }

    .tk-timeline-body {
        padding: 1.4vw;
    }

    /* Timeline Items */
    .tk-timeline {
        position: relative;
        padding-left: 0;
    }

    .tk-tl-item {
        position: relative;
        padding-left: 2.2vw;
        padding-bottom: 1.5vw;
    }

    .tk-tl-item:last-child {
        padding-bottom: 0;
    }

    /* Garis vertikal */
    .tk-tl-item::before {
        content: '';
        position: absolute;
        left: 0.55vw;
        top: 1.5vw;
        bottom: -0.2vw;
        width: 0.15vw;
        background: #e2e8f0;
        border-radius: 100vw;
    }

    .tk-tl-item:last-child::before {
        display: none;
    }

    /* Dot */
    .tk-tl-dot {
        position: absolute;
        left: 0;
        top: 0.2vw;
        width: 1.25vw;
        height: 1.25vw;
        border-radius: 50%;
        background: #ffffff;
        border: 0.25vw solid #cbd5e1;
        z-index: 1;
    }

    .tk-tl-item.is-completed .tk-tl-dot {
        border-color: #10b981;
        background: #10b981;
        box-shadow: 0 0 0 0.3vw rgba(16, 185, 129, 0.15);
    }

    .tk-tl-item.is-active .tk-tl-dot {
        border-color: #ecbc42;
        background: #ecbc42;
        box-shadow: 0 0 0 0.35vw rgba(236, 188, 66, 0.25);
        animation: tkPulse 1.8s ease-in-out infinite;
    }

    @keyframes tkPulse {
        0%, 100% {
            box-shadow: 0 0 0 0.35vw rgba(236, 188, 66, 0.25);
        }
        50% {
            box-shadow: 0 0 0 0.7vw rgba(236, 188, 66, 0.1);
        }
    }

    /* Content */
    .tk-tl-title {
        font-size: 0.88vw;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.4;
    }

    .tk-tl-item.is-active .tk-tl-title {
        color: rgb(102, 72, 9);
    }

    .tk-tl-time {
        display: inline-flex;
        align-items: center;
        gap: 0.3vw;
        font-size: 0.72vw;
        color: #94a3b8;
        margin-top: 0.35vw;
        padding: 0.25vw 0.6vw;
        border-radius: 100vw;
        background: #f8fafc;
    }

    .tk-tl-time iconify-icon {
        font-size: 0.85vw;
    }

    .tk-tl-item.is-active .tk-tl-time {
        background: #fffbf0;
        color: rgb(102, 72, 9);
    }

    /* Empty State */
    .tk-tl-empty {
        text-align: center;
        padding: 2.5vw 1vw;
        color: #94a3b8;
    }

    .tk-tl-empty-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 4vw;
        height: 4vw;
        margin: 0 auto 1vw;
        border-radius: 50%;
        background: linear-gradient(135deg, #FDDD57 0%, #ecbc42 100%);
        color: rgb(102, 72, 9);
    }

    .tk-tl-empty-icon iconify-icon {
        font-size: 2vw;
    }

    .tk-tl-empty-title {
        font-size: 0.95vw;
        font-weight: 700;
        color: #0f172a;
    }

    .tk-tl-empty-desc {
        font-size: 0.78vw;
        color: #94a3b8;
        margin-top: 0.4vw;
        line-height: 1.6;
    }

    /* --------------------------------------------
       ACTION BUTTONS
       -------------------------------------------- */
    .tk-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.6vw;
    }

    .tk-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4vw;
        padding: 0.85vw 1.4vw;
        border-radius: 0.7vw;
        font-size: 0.82vw;
        font-weight: 600;
        cursor: pointer;
        border: 0.1vw solid transparent;
        transition: all 0.2s ease;
        text-decoration: none;
        font-family: inherit;
    }

    .tk-btn iconify-icon {
        font-size: 1.05vw;
    }

    .tk-btn-outline {
        background: #ffffff;
        border-color: #e2e8f0;
        color: #475569;
    }
    .tk-btn-outline:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .tk-btn-gold {
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
        color: rgb(102, 72, 9);
        box-shadow: 0 0.15vw 0.5vw rgba(236, 188, 66, 0.3);
    }
    .tk-btn-gold:hover {
        transform: translateY(-0.1vw);
        box-shadow: 0 0.3vw 1vw rgba(236, 188, 66, 0.4);
    }

    .tk-btn-primary {
        background: #076694;
        color: #ffffff;
    }
    .tk-btn-primary:hover { background: #054868; }

    /* ============================================
       RESPONSIVE - TABLET
       ============================================ */
    @media (max-width: 1024px) {
        .tk-wrapper { gap: 3vw; margin-top:3vw;}

        .tk-alert {
            padding: 2.5vw 3vw;
            border-radius: 1.5vw;
            font-size: 2vw;
            gap: 1.5vw;
            border-width: 0.2vw;
        }
        .tk-alert iconify-icon { font-size: 3vw; }

        .tk-status-card,
        .tk-timeline-card {
            border-radius: 2vw;
            border-width: 0.2vw;
        }

        .tk-status-card::before { height: 0.7vw; }

        .tk-status-head {
            padding: 3vw 3.2vw;
            gap: 2vw;
            border-bottom-width: 0.2vw;
        }

        .tk-order-num {
            font-size: 2.6vw;
            gap: 1vw;
        }
        .tk-order-num iconify-icon { font-size: 3vw; }

        .tk-order-date {
            font-size: 1.8vw;
            gap: 0.8vw;
            margin-top: 0.7vw;
        }
        .tk-order-date iconify-icon { font-size: 2.2vw; }

        .tk-status-badge {
            padding: 1.5vw 2.5vw;
            font-size: 2vw;
            gap: 1vw;
            border-width: 0.2vw;
        }
        .tk-status-badge iconify-icon { font-size: 2.4vw; }

        .tk-status-info {
            grid-template-columns: repeat(2, 1fr);
        }

        .tk-info-item {
            padding: 2.8vw 3.2vw;
            border-right-width: 0.2vw;
            border-bottom: 0.2vw solid #f1f5f9;
        }
        .tk-info-item:nth-child(2n) {
            border-right: none;
        }
        .tk-info-item:nth-last-child(-n+2) {
            border-bottom: none;
        }

        .tk-info-label {
            font-size: 1.7vw;
            gap: 0.8vw;
        }
        .tk-info-label iconify-icon { font-size: 2.1vw; }

        .tk-info-value {
            font-size: 2.2vw;
            margin-top: 0.7vw;
        }

        .tk-section-head {
            padding: 2.3vw 3vw;
            gap: 1.2vw;
            font-size: 2.1vw;
            border-bottom-width: 0.2vw;
        }
        .tk-section-head iconify-icon { font-size: 2.8vw; }

        .tk-timeline-body { padding: 3.5vw; }

        .tk-tl-item {
            padding-left: 5.5vw;
            padding-bottom: 4vw;
        }

        .tk-tl-item::before {
            left: 1.35vw;
            top: 3.5vw;
            bottom: -0.5vw;
            width: 0.4vw;
        }

        .tk-tl-dot {
            top: 0.5vw;
            width: 3vw;
            height: 3vw;
            border-width: 0.6vw;
        }

        .tk-tl-item.is-completed .tk-tl-dot {
            box-shadow: 0 0 0 0.7vw rgba(16, 185, 129, 0.15);
        }

        .tk-tl-item.is-active .tk-tl-dot {
            box-shadow: 0 0 0 0.8vw rgba(236, 188, 66, 0.25);
        }

        @keyframes tkPulse {
            0%, 100% { box-shadow: 0 0 0 0.8vw rgba(236, 188, 66, 0.25); }
            50% { box-shadow: 0 0 0 1.6vw rgba(236, 188, 66, 0.1); }
        }

        .tk-tl-title {
            font-size: 2.1vw;
        }

        .tk-tl-time {
            font-size: 1.8vw;
            gap: 0.8vw;
            margin-top: 1vw;
            padding: 0.7vw 1.5vw;
        }
        .tk-tl-time iconify-icon { font-size: 2.2vw; }

        .tk-tl-empty { padding: 6vw 3vw; }
        .tk-tl-empty-icon {
            width: 10vw;
            height: 10vw;
            margin-bottom: 2.5vw;
        }
        .tk-tl-empty-icon iconify-icon { font-size: 5vw; }
        .tk-tl-empty-title { font-size: 2.3vw; }
        .tk-tl-empty-desc { font-size: 1.9vw; margin-top: 1vw; }

        .tk-actions { gap: 1.5vw; }

        .tk-btn {
            padding: 2.2vw 3vw;
            border-radius: 1.5vw;
            font-size: 2vw;
            gap: 1vw;
            border-width: 0.2vw;
        }
        .tk-btn iconify-icon { font-size: 2.5vw; }
    }

    /* ============================================
       RESPONSIVE - MOBILE
       ============================================ */
    @media (max-width: 480px) {
        .tk-wrapper { gap: 5vw; margin-top: 5vw;}

        .tk-alert {
            padding: 3.5vw 4vw;
            border-radius: 2.5vw;
            font-size: 3.2vw;
            gap: 2.5vw;
            border-width: 0.3vw;
            align-items: flex-start;
        }
        .tk-alert iconify-icon { font-size: 4.5vw; }

        .tk-status-card,
        .tk-timeline-card {
            border-radius: 3vw;
            border-width: 0.3vw;
        }

        .tk-status-card::before { height: 1vw; }

        .tk-status-head {
            flex-direction: column;
            align-items: stretch;
            padding: 4vw;
            gap: 3vw;
            border-bottom-width: 0.3vw;
        }

        .tk-order-num {
            font-size: 4.2vw;
            gap: 1.5vw;
        }
        .tk-order-num iconify-icon { font-size: 5vw; }

        .tk-order-date {
            font-size: 2.8vw;
            gap: 1.2vw;
            margin-top: 1.2vw;
        }
        .tk-order-date iconify-icon { font-size: 3.4vw; }

        .tk-status-badge {
            padding: 2.5vw 4vw;
            font-size: 3.2vw;
            gap: 1.5vw;
            border-width: 0.3vw;
            align-self: flex-start;
        }
        .tk-status-badge iconify-icon { font-size: 4vw; }

        .tk-status-info {
            grid-template-columns: 1fr;
        }

        .tk-info-item {
            padding: 3.5vw 4vw;
            border-right: none;
            border-bottom: 0.3vw solid #f1f5f9;
        }
        .tk-info-item:last-child {
            border-bottom: none;
        }

        .tk-info-label {
            font-size: 2.7vw;
            gap: 1.2vw;
        }
        .tk-info-label iconify-icon { font-size: 3.4vw; }

        .tk-info-value {
            font-size: 3.5vw;
            margin-top: 1vw;
            white-space: normal;
        }

        .tk-section-head {
            padding: 3.5vw 4vw;
            gap: 2vw;
            font-size: 3.5vw;
            border-bottom-width: 0.3vw;
        }
        .tk-section-head iconify-icon { font-size: 4.2vw; }

        .tk-timeline-body { padding: 4.5vw 4vw; }

        .tk-tl-item {
            padding-left: 8vw;
            padding-bottom: 5vw;
        }

        .tk-tl-item::before {
            left: 2vw;
            top: 5.5vw;
            bottom: -1vw;
            width: 0.6vw;
        }

        .tk-tl-dot {
            top: 0.8vw;
            width: 5vw;
            height: 5vw;
            border-width: 1vw;
        }

        .tk-tl-item.is-completed .tk-tl-dot {
            box-shadow: 0 0 0 1.2vw rgba(16, 185, 129, 0.15);
        }

        .tk-tl-item.is-active .tk-tl-dot {
            box-shadow: 0 0 0 1.4vw rgba(236, 188, 66, 0.25);
        }

        @keyframes tkPulse {
            0%, 100% { box-shadow: 0 0 0 1.4vw rgba(236, 188, 66, 0.25); }
            50% { box-shadow: 0 0 0 2.4vw rgba(236, 188, 66, 0.1); }
        }

        .tk-tl-title {
            font-size: 3.4vw;
            line-height: 1.5;
        }

        .tk-tl-time {
            font-size: 2.8vw;
            gap: 1.2vw;
            margin-top: 1.8vw;
            padding: 1.2vw 2.5vw;
        }
        .tk-tl-time iconify-icon { font-size: 3.4vw; }

        .tk-tl-empty { padding: 8vw 3vw; }
        .tk-tl-empty-icon {
            width: 16vw;
            height: 16vw;
            margin-bottom: 4vw;
        }
        .tk-tl-empty-icon iconify-icon { font-size: 8vw; }
        .tk-tl-empty-title { font-size: 4vw; }
        .tk-tl-empty-desc { font-size: 3vw; margin-top: 1.8vw; }

        .tk-actions {
            flex-direction: column;
            gap: 2.5vw;
        }

        .tk-btn {
            width: 100%;
            padding: 3.2vw 4vw;
            border-radius: 2.5vw;
            font-size: 3.2vw;
            gap: 1.5vw;
            border-width: 0.3vw;
        }
        .tk-btn iconify-icon { font-size: 4vw; }
    }
</style>

<div class="tk-wrapper">

    {{-- 🔥 ALERTS --}}
    @if(session('error'))
        <div class="tk-alert tk-alert-danger">
            <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($trackingError)
        <div class="tk-alert tk-alert-warning">
            <iconify-icon icon="mdi:information-outline"></iconify-icon>
            <span>{{ $trackingError }}</span>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- STATUS CARD --}}
    {{-- ============================================ --}}
    @php
        $statusMap = [
            'pending'      => ['label' => 'Menunggu',           'icon' => 'mdi:clock-outline',           'class' => 'pending'],
            'processing'   => ['label' => 'Diproses',           'icon' => 'mdi:package-variant',         'class' => 'processing'],
            'shipped'      => ['label' => 'Dikirim',            'icon' => 'mdi:truck-delivery-outline',  'class' => 'shipped'],
            'delivered'    => ['label' => 'Telah Sampai',       'icon' => 'mdi:check-circle-outline',    'class' => 'delivered'],
            'cancelled'    => ['label' => 'Dibatalkan',         'icon' => 'mdi:close-circle-outline',    'class' => 'cancelled'],
            'confirmed'    => ['label' => 'Dikonfirmasi',       'icon' => 'mdi:check-circle-outline',    'class' => 'processing'],
            'allocated'    => ['label' => 'Kurir Dialokasikan', 'icon' => 'mdi:account-outline',         'class' => 'processing'],
            'picking_up'   => ['label' => 'Menjemput Paket',    'icon' => 'mdi:truck-outline',           'class' => 'shipped'],
            'picked'       => ['label' => 'Paket Diambil',      'icon' => 'mdi:package-variant',         'class' => 'shipped'],
            'in_transit'   => ['label' => 'Dalam Perjalanan',   'icon' => 'mdi:truck-delivery-outline',  'class' => 'shipped'],
            'dropping_off' => ['label' => 'Menuju Tujuan',      'icon' => 'mdi:map-marker-outline',      'class' => 'shipped'],
            'returned'     => ['label' => 'Dikembalikan',       'icon' => 'mdi:arrow-u-left-top',        'class' => 'cancelled'],
        ];
        $status = $tracking['status'] ?? $order->shipping_status ?? 'pending';
        $statusInfo = $statusMap[$status] ?? $statusMap['pending'];
    @endphp

    <div class="tk-status-card">
        <div class="tk-status-head">
            <div>
                <div class="tk-order-num">
                    <iconify-icon icon="mdi:receipt-text-outline"></iconify-icon>
                    #{{ $order->order_number }}
                </div>
                <div class="tk-order-date">
                    <iconify-icon icon="mdi:calendar-clock-outline"></iconify-icon>
                    {{ $order->created_at->translatedFormat('d M Y, H:i') }}
                </div>
            </div>
            <span class="tk-status-badge tk-status-{{ $statusInfo['class'] }}">
                <iconify-icon icon="{{ $statusInfo['icon'] }}"></iconify-icon>
                {{ $statusInfo['label'] }}
            </span>
        </div>

        <div class="tk-status-info">
            <div class="tk-info-item">
                <div class="tk-info-label">
                    <iconify-icon icon="mdi:package-variant-closed"></iconify-icon>
                    Kurir
                </div>
                <div class="tk-info-value">
                    {{ strtoupper($tracking['courier']['company'] ?? $tracking['courier_name'] ?? $order->courier ?? '-') }}
                </div>
            </div>
            <div class="tk-info-item">
                <div class="tk-info-label">
                    <iconify-icon icon="mdi:layers-outline"></iconify-icon>
                    Layanan
                </div>
                <div class="tk-info-value">
                    {{ strtoupper($tracking['courier']['type'] ?? $tracking['service'] ?? $order->service ?? '-') }}
                </div>
            </div>
            <div class="tk-info-item">
                <div class="tk-info-label">
                    <iconify-icon icon="mdi:barcode-scan"></iconify-icon>
                    No. Resi
                </div>
                <div class="tk-info-value">
                    {{ $tracking['waybill_id'] ?? $order->tracking_number ?? '-' }}
                </div>
            </div>
            <div class="tk-info-item">
                <div class="tk-info-label">
                    <iconify-icon icon="mdi:calendar-check-outline"></iconify-icon>
                    Estimasi Tiba
                </div>
                <div class="tk-info-value">
                    @if(isset($tracking['delivery']['datetime']))
                        {{ \Carbon\Carbon::parse($tracking['delivery']['datetime'])->locale('id')->isoFormat('DD MMM YYYY, HH:mm') }}
                    @elseif(isset($tracking['delivery_date']))
                        {{ \Carbon\Carbon::parse($tracking['delivery_date'])->locale('id')->isoFormat('DD MMM YYYY, HH:mm') }}
                    @else
                        -
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- TIMELINE --}}
    {{-- ============================================ --}}
    <div class="tk-timeline-card">
        <div class="tk-section-head">
            <iconify-icon icon="mdi:history"></iconify-icon>
            Riwayat Pengiriman
        </div>

        <div class="tk-timeline-body">
            @php
                $statusTranslations = [
                    'confirmed'    => 'Pesanan Dikonfirmasi',
                    'allocated'    => 'Kurir Dialokasikan',
                    'picking_up'   => 'Kurir Menuju Lokasi Penjemputan',
                    'picked'       => 'Paket Diambil',
                    'in_transit'   => 'Dalam Perjalanan',
                    'dropping_off' => 'Kurir Menuju Lokasi Tujuan',
                    'delivered'    => 'Paket Diterima',
                    'cancelled'    => 'Pesanan Dibatalkan',
                    'returned'     => 'Paket Dikembalikan',
                    'pending'      => 'Menunggu Konfirmasi',
                    'processing'   => 'Sedang Diproses',
                    'shipped'      => 'Pesanan Dikirim',
                ];

                $historyTranslations = [
                    'Courier order is confirmed' => 'Pesanan telah dikonfirmasi',
                    'Courier order is confirmed. jne has been notified to pick up. Pickup Number: WYB-1788827540058' => 'Pesanan telah dikonfirmasi. JNE telah diminta mengambil paket. Nomor Pickup: WYB-1788827540058',
                    'Courier is allocated and ready to pick up' => 'Kurir siap mengambil paket',
                    'Courier is on the way to pick up location' => 'Kurir menuju lokasi penjemputan',
                    'Item has been picked by courier' => 'Paket telah diambil kurir',
                    'Item is on the way to destination' => 'Paket dalam perjalanan ke tujuan',
                    'Courier is dropping off item to destination' => 'Kurir menuju lokasi tujuan',
                    'Order has been delivered' => 'Paket telah diterima',
                ];

                $noteKeywordTranslations = [
                    'has been notified to pick up' => 'Kurir telah diminta mengambil paket',
                    'Pickup Number' => 'Nomor Pickup',
                ];

                $histories = $tracking['history'] ?? [];
            @endphp

            @if(empty($histories))
                <div class="tk-tl-empty">
                    <div class="tk-tl-empty-icon">
                        <iconify-icon icon="mdi:package-variant"></iconify-icon>
                    </div>
                    <div class="tk-tl-empty-title">Belum Ada Riwayat Pengiriman</div>
                    <div class="tk-tl-empty-desc">
                        Tracking akan muncul setelah paket diproses oleh kurir.
                    </div>
                </div>
            @else
                <div class="tk-timeline">
                    @foreach($histories as $index => $history)
                        @php
                            $isActive = $index === 0;
                            $isCompleted = $index < count($histories) - 1;

                            $rawStatus = strtolower($history['status'] ?? '');
                            $translatedStatus = $statusTranslations[$rawStatus] ?? ucfirst(str_replace('_', ' ', $rawStatus));

                            $rawNote = $history['note'] ?? '';
                            $translatedNote = $historyTranslations[$rawNote] ?? null;

                            if ($translatedNote === null && !empty($rawNote)) {
                                $translatedNote = $rawNote;
                                foreach ($noteKeywordTranslations as $keyword => $translation) {
                                    $translatedNote = str_ireplace($keyword, $translation, $translatedNote);
                                }
                            } elseif ($translatedNote === null) {
                                $translatedNote = $rawNote;
                            }

                            $displayText = $translatedNote ?: $translatedStatus;
                            $timeString = $history['updated_at'] ?? $history['time'] ?? null;
                        @endphp
                        <div class="tk-tl-item {{ $isActive ? 'is-active' : '' }} {{ $isCompleted ? 'is-completed' : '' }}">
                            <div class="tk-tl-dot"></div>
                            <div class="tk-tl-title">{{ $displayText }}</div>
                            <div class="tk-tl-time">
                                <iconify-icon icon="mdi:clock-outline"></iconify-icon>
                                {{ $timeString ? \Carbon\Carbon::parse($timeString)->locale('id')->isoFormat('DD MMM YYYY, HH:mm') : '-' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- ACTIONS --}}
    {{-- ============================================ --}}
    <div class="tk-actions">
        <a href="{{ route('customer.orders.show', $order) }}" class="tk-btn tk-btn-outline">
            <iconify-icon icon="mdi:arrow-left"></iconify-icon>
            Kembali ke Detail
        </a>

        @if($order->biteship_order_id)
            <button type="button" id="refresh-tracking-btn" class="tk-btn tk-btn-outline">
                <iconify-icon icon="mdi:refresh"></iconify-icon>
                Refresh
            </button>
        @endif

        @if($order->biteship_tracking_url)
            <a href="{{ $order->biteship_tracking_url }}" target="_blank" class="tk-btn tk-btn-gold">
                <iconify-icon icon="mdi:open-in-new"></iconify-icon>
                Tracking di Biteship
            </a>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var refreshBtn = document.getElementById('refresh-tracking-btn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            var originalHTML = refreshBtn.innerHTML;
            refreshBtn.innerHTML = '<iconify-icon icon="mdi:loading" class="animate-spin"></iconify-icon> Memperbarui...';
            refreshBtn.disabled = true;

            fetch('{{ route('customer.orders.tracking.refresh', $order) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Gagal refresh tracking: ' + (data.message || 'Unknown error'));
                    refreshBtn.innerHTML = originalHTML;
                    refreshBtn.disabled = false;
                }
            })
            .catch(function(error) {
                console.error('Refresh error:', error);
                alert('Terjadi kesalahan: ' + error.message);
                refreshBtn.innerHTML = originalHTML;
                refreshBtn.disabled = false;
            });
        });
    }
});
</script>
@endpush