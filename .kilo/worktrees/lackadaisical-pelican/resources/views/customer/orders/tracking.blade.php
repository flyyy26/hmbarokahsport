@extends('layouts.account')

@section('title', 'Tracking Pesanan - ' . config('app.name'))
@section('page-title', 'Tracking Pengiriman')
@section('page-subtitle', 'Pantau status pengiriman pesanan Anda.')

@section('account-content')

<style>
    .tracking-container {
        max-width: 100%;
    }

    /* 🔥 STATUS CARD */
    .tracking-status-card {
        background: #ffffff;
        border-radius: 1rem;
        border: 1px solid #e2e8f0;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .tracking-status-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #076694, #0a8ab8);
    }

    .tracking-status-card .status-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .tracking-status-card .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1.2rem;
        border-radius: 100vw;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .tracking-status-card .status-badge.pending {
        background: #fef3c7;
        color: #92400e;
    }

    .tracking-status-card .status-badge.processing {
        background: #dbeafe;
        color: #1e40af;
    }

    .tracking-status-card .status-badge.shipped {
        background: #fef3c7;
        color: #92400e;
    }

    .tracking-status-card .status-badge.delivered {
        background: #dcfce7;
        color: #16a34a;
    }

    .tracking-status-card .status-badge.cancelled {
        background: #fee2e2;
        color: #991b1b;
    }

    .tracking-status-card .status-badge iconify-icon {
        font-size: 1.2rem;
    }

    .tracking-status-card .status-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #f1f5f9;
    }

    .tracking-status-card .status-info .info-item {
        display: flex;
        flex-direction: column;
    }

    .tracking-status-card .status-info .info-item .label {
        font-size: 0.7rem;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .tracking-status-card .status-info .info-item .value {
        font-size: 0.9rem;
        font-weight: 600;
        color: #0f172a;
        margin-top: 0.2rem;
    }

    /* 🔥 TRACKING TIMELINE */
    .tracking-timeline {
        background: #ffffff;
        border-radius: 1rem;
        border: 1px solid #e2e8f0;
        padding: 1.5rem;
    }

    .tracking-timeline .timeline-title {
        font-size: 1rem;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .tracking-timeline .timeline-title iconify-icon {
        color: #076694;
    }

    .tracking-timeline .timeline-empty {
        text-align: center;
        padding: 2rem 0;
        color: #94a3b8;
    }

    .tracking-timeline .timeline-empty iconify-icon {
        font-size: 2.5rem;
        display: block;
        margin-bottom: 0.5rem;
        color: #cbd5e1;
    }

    /* 🔥 TIMELINE ITEM */
    .timeline-item {
        position: relative;
        padding-left: 2.5rem;
        padding-bottom: 1.8rem;
        border-left: 2px solid #e2e8f0;
    }

    .timeline-item:last-child {
        border-left: none;
        padding-bottom: 0;
    }

    .timeline-item .timeline-dot {
        position: absolute;
        left: -0.6rem;
        top: 0.2rem;
        width: 1.2rem;
        height: 1.2rem;
        border-radius: 50%;
        background: #cbd5e1;
        border: 2px solid #ffffff;
        box-shadow: 0 0 0 2px #e2e8f0;
        z-index: 1;
    }

    .timeline-item.active .timeline-dot {
        background: #076694;
        box-shadow: 0 0 0 3px rgba(7, 102, 148, 0.2);
    }

    .timeline-item.completed .timeline-dot {
        background: #22c55e;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);
    }

    .timeline-item .timeline-content {
        padding-top: 0.1rem;
    }

    .timeline-item .timeline-content .title {
        font-size: 0.9rem;
        font-weight: 600;
        color: #0f172a;
    }

    .timeline-item .timeline-content .description {
        font-size: 0.8rem;
        color: #64748b;
        margin-top: 0.2rem;
    }

    .timeline-item .timeline-content .location {
        font-size: 0.75rem;
        color: #94a3b8;
        margin-top: 0.2rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    .timeline-item .timeline-content .time {
        font-size: 0.7rem;
        color: #94a3b8;
        margin-top: 0.3rem;
    }

    .timeline-item .timeline-content .note {
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 0.3rem;
        padding: 0.3rem 0.8rem;
        background: #f8fafc;
        border-radius: 0.4rem;
        display: inline-block;
    }

    /* 🔥 RESPONSIVE */
    @media (max-width: 640px) {
        .tracking-status-card .status-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .tracking-status-card .status-info {
            grid-template-columns: 1fr 1fr;
        }

        .timeline-item {
            padding-left: 2rem;
        }

        .timeline-item .timeline-dot {
            left: -0.5rem;
            width: 1rem;
            height: 1rem;
        }
    }
</style>

<div class="tracking-container">

    {{-- 🔥 ALERT --}}
    @if(session('error'))
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <div class="flex items-center gap-2">
                <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if($trackingError)
        <div class="mb-4 rounded-xl border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-700">
            <div class="flex items-center gap-2">
                <iconify-icon icon="mdi:information-outline"></iconify-icon>
                <span>{{ $trackingError }}</span>
            </div>
        </div>
    @endif

    {{-- 🔥 STATUS CARD --}}
    <div class="tracking-status-card">
        <div class="status-header">
            <div>
                <h3 class="text-lg font-bold text-slate-900">
                    #{{ $order->order_number }}
                </h3>
                <p class="text-sm text-slate-500">
                    {{ $order->created_at->format('d M Y H:i') }}
                </p>
            </div>
            <div>
                @php
                    $statusMap = [
                        'pending' => ['label' => 'Menunggu', 'icon' => 'mdi:clock-outline', 'class' => 'pending'],
                        'processing' => ['label' => 'Diproses', 'icon' => 'mdi:package-variant', 'class' => 'processing'],
                        'shipped' => ['label' => 'Dikirim', 'icon' => 'mdi:truck-delivery-outline', 'class' => 'shipped'],
                        'delivered' => ['label' => 'Telah Sampai', 'icon' => 'mdi:check-circle-outline', 'class' => 'delivered'],
                        'cancelled' => ['label' => 'Dibatalkan', 'icon' => 'mdi:close-circle-outline', 'class' => 'cancelled'],
                        'confirmed' => ['label' => 'Dikonfirmasi', 'icon' => 'mdi:check-circle-outline', 'class' => 'processing'],
                        'allocated' => ['label' => 'Kurir Dialokasikan', 'icon' => 'mdi:account-outline', 'class' => 'processing'],
                        'picking_up' => ['label' => 'Menjemput Paket', 'icon' => 'mdi:truck-outline', 'class' => 'shipped'],
                        'picked' => ['label' => 'Paket Diambil', 'icon' => 'mdi:package-variant', 'class' => 'shipped'],
                        'in_transit' => ['label' => 'Dalam Perjalanan', 'icon' => 'mdi:truck-delivery-outline', 'class' => 'shipped'],
                        'dropping_off' => ['label' => 'Menuju Tujuan', 'icon' => 'mdi:map-marker-outline', 'class' => 'shipped'],
                        'returned' => ['label' => 'Dikembalikan', 'icon' => 'mdi:arrow-u-left-top', 'class' => 'cancelled'],
                    ];
                    $status = $tracking['status'] ?? $order->shipping_status ?? 'pending';
                    $statusInfo = $statusMap[$status] ?? $statusMap['pending'];
                @endphp
                <span class="status-badge {{ $statusInfo['class'] }}">
                    <iconify-icon icon="{{ $statusInfo['icon'] }}"></iconify-icon>
                    {{ $statusInfo['label'] }}
                </span>
            </div>
        </div>

        <div class="status-info">
            <div class="info-item">
                <span class="label">Kurir</span>
                <span class="value">{{ strtoupper($tracking['courier']['company'] ?? $tracking['courier_name'] ?? $order->courier ?? '-') }}</span>
            </div>
            <div class="info-item">
                <span class="label">Layanan</span>
                <span class="value">{{ strtoupper($tracking['courier']['type'] ?? $tracking['service'] ?? $order->service ?? '-') }}</span>
            </div>
            <div class="info-item">
                <span class="label">Nomor Resi</span>
                <span class="value">{{ $tracking['waybill_id'] ?? $order->tracking_number ?? '-' }}</span>
            </div>
            <div class="info-item">
                <span class="label">Estimasi Tiba</span>
                <span class="value">
                    @if(isset($tracking['delivery']['datetime']))
                        {{ \Carbon\Carbon::parse($tracking['delivery']['datetime'])->locale('id')->isoFormat('DD MMMM YYYY HH:mm') }}
                    @elseif(isset($tracking['delivery_date']))
                        {{ \Carbon\Carbon::parse($tracking['delivery_date'])->locale('id')->isoFormat('DD MMMM YYYY HH:mm') }}
                    @else
                        -
                    @endif
                </span>
            </div>
        </div>
    </div>

    {{-- 🔥 TIMELINE --}}
    <div class="tracking-timeline">
        <div class="timeline-title">
            <iconify-icon icon="mdi:history"></iconify-icon>
            Riwayat Pengiriman
        </div>

        @php
            $statusTranslations = [
                'confirmed' => 'Pesanan Dikonfirmasi',
                'allocated' => 'Kurir Dialokasikan',
                'picking_up' => 'Kurir Menuju Lokasi Penjemputan',
                'picked' => 'Paket Diambil',
                'in_transit' => 'Dalam Perjalanan',
                'dropping_off' => 'Kurir Menuju Lokasi Tujuan',
                'delivered' => 'Paket Diterima',
                'cancelled' => 'Pesanan Dibatalkan',
                'returned' => 'Paket Dikembalikan',
                'pending' => 'Menunggu Konfirmasi',
                'processing' => 'Sedang Diproses',
                'shipped' => 'Pesanan Dikirim',
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
        @endphp

        @php
            $histories = $tracking['history'] ?? [];
        @endphp

        @if(empty($histories))
            <div class="timeline-empty">
                <iconify-icon icon="mdi:package-variant"></iconify-icon>
                <p>Belum ada riwayat pengiriman.</p>
                <p class="text-xs mt-1">Tracking akan muncul setelah paket diproses oleh kurir.</p>
            </div>
        @else
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
                <div class="timeline-item {{ $isActive ? 'active' : '' }} {{ $isCompleted ? 'completed' : '' }}">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <div class="title">{{ $displayText }}</div>
                        <div class="time">
                            <iconify-icon icon="mdi:clock-outline" style="font-size:0.7rem;"></iconify-icon>
                            {{ $timeString ? \Carbon\Carbon::parse($timeString)->locale('id')->isoFormat('DD MMMM YYYY HH:mm') : '-' }}
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    {{-- 🔥 ACTION BUTTONS --}}
    <div class="mt-6 flex flex-wrap gap-3">
        <a href="{{ route('customer.orders.show', $order) }}" 
           class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-6 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
            <iconify-icon icon="mdi:arrow-left"></iconify-icon>
            Kembali ke Detail
        </a>

        @if($order->biteship_tracking_url)
            <a href="{{ $order->biteship_tracking_url }}" target="_blank" 
               class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-6 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                <iconify-icon icon="mdi:open-in-new"></iconify-icon>
                Tracking di Biteship
            </a>
        @endif

        <button onclick="location.reload()" 
                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-6 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
            <iconify-icon icon="mdi:refresh"></iconify-icon>
            Refresh
        </button>
    </div>

</div>

@endsection