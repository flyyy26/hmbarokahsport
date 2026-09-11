@extends('layouts.admin')

@section('content')

<div class="w-full max-w-5xl mx-auto space-y-6">

    {{-- ============================================ --}}
    {{-- HEADER --}}
    {{-- ============================================ --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <a href="{{ route('admin.orders.show', $order) }}"
               class="inline-flex items-center gap-1.5 text-xs font-semibold transition-colors"
               style="color: var(--text-5)"
               onmouseover="this.style.color='#FDDD57'"
               onmouseout="this.style.color='var(--text-5)'">
                <iconify-icon icon="mdi:arrow-left"></iconify-icon>
                Kembali ke Detail
            </a>
            <h1 class="text-2xl font-bold flex items-center gap-2.5 mt-2" style="color: var(--text-1)">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl
                             bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                             shadow-lg shadow-amber-500/20 flex-shrink-0">
                    <iconify-icon icon="mdi:truck-fast-outline" class="text-slate-900 text-2xl"></iconify-icon>
                </span>
                Tracking Pengiriman
            </h1>
            <p class="text-sm mt-1 ml-12 font-mono" style="color: var(--text-5)">
                #{{ $order->order_number }}
            </p>
        </div>

        <div class="flex flex-wrap gap-2 flex-shrink-0">
            @if($order->tracking_number)
                <span class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg
                             text-xs font-mono font-bold border"
                      style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)">
                    <iconify-icon icon="mdi:barcode-scan"></iconify-icon>
                    {{ $order->tracking_number }}
                </span>
            @endif

            @if($order->biteship_order_id)
                <button type="button"
                        class="refresh-tracking-btn inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg
                               text-sm font-semibold transition-all active:scale-95 border"
                        style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)"
                        onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                        onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'">
                    <iconify-icon icon="mdi:refresh"></iconify-icon>
                    Refresh
                </button>
            @endif

            @if(!$order->biteship_order_id)
                <form action="{{ route('admin.orders.biteship.create', $order) }}"
                      method="POST"
                      onsubmit="return confirm('Buat order di Biteship untuk order ini? Pastikan data pengiriman sudah lengkap.');">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg
                                   text-sm font-bold transition-all active:scale-95
                                   bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                                   text-slate-900
                                   shadow-lg shadow-amber-500/20
                                   hover:shadow-xl hover:shadow-amber-500/40">
                        <iconify-icon icon="mdi:rocket-launch-outline"></iconify-icon>
                        Buat Order Biteship
                    </button>
                </form>
            @endif
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- ALERTS --}}
    {{-- ============================================ --}}
    @if(session('success'))
        <div class="flex items-start gap-3 rounded-xl px-4 py-3
                    bg-emerald-500/10 border border-emerald-500/30 text-emerald-400">
            <iconify-icon icon="mdi:check-circle-outline" class="text-xl flex-shrink-0 mt-0.5"></iconify-icon>
            <div class="min-w-0">
                <p class="text-sm font-bold">Berhasil</p>
                <p class="text-xs mt-0.5">{{ session('success') }}</p>
                @if($order->biteship_order_id)
                    <p class="text-xs mt-1 font-mono" style="color: rgba(110, 231, 183, 0.7);">
                        Order ID: {{ $order->biteship_order_id }}
                    </p>
                    <p class="text-xs font-mono" style="color: rgba(110, 231, 183, 0.7);">
                        Resi: {{ $order->tracking_number ?? 'Belum tersedia' }}
                    </p>
                @endif
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-start gap-3 rounded-xl px-4 py-3
                    bg-red-500/10 border border-red-500/30 text-red-400">
            <iconify-icon icon="mdi:alert-circle-outline" class="text-xl flex-shrink-0 mt-0.5"></iconify-icon>
            <div class="min-w-0">
                <p class="text-sm font-bold">Terjadi Kesalahan</p>
                <p class="text-xs mt-0.5">{{ session('error') }}</p>
            </div>
        </div>
    @endif


    {{-- ============================================ --}}
    {{-- ORDER INFO GRID --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Kurir --}}
        <div class="rounded-xl border p-4"
             style="background: var(--bg-card); border-color: var(--border-2)">
            <p class="text-[10px] font-bold uppercase tracking-wider mb-1.5" style="color: var(--text-5)">
                Kurir
            </p>
            <p class="text-sm font-bold" style="color: var(--text-1)">
                {{ strtoupper($order->courier ?? '-') }}
            </p>
        </div>

        {{-- Layanan --}}
        <div class="rounded-xl border p-4"
             style="background: var(--bg-card); border-color: var(--border-2)">
            <p class="text-[10px] font-bold uppercase tracking-wider mb-1.5" style="color: var(--text-5)">
                Layanan
            </p>
            <p class="text-sm font-bold" style="color: var(--text-1)">
                {{ $order->service ?? '-' }}
            </p>
        </div>

        {{-- Status Pengiriman --}}
        <div class="rounded-xl border p-4"
             style="background: var(--bg-card); border-color: var(--border-2)">
            <p class="text-[10px] font-bold uppercase tracking-wider mb-1.5" style="color: var(--text-5)">
                Status
            </p>
            @php
                $orderStatusMap = [
                    'delivered'  => '#34d399',
                    'shipped'    => '#a78bfa',
                    'processing' => '#60a5fa',
                    'pending'    => '#fbbf24',
                    'cancelled'  => '#f87171',
                ];
                $osc = $orderStatusMap[$order->shipping_status] ?? '#94a3b8';
            @endphp
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border"
                  style="background: {{ $osc }}15; border-color: {{ $osc }}40; color: {{ $osc }};">
                <span class="w-1.5 h-1.5 rounded-full" style="background: currentColor;"></span>
                {{ $order->shipping_status_label }}
            </span>
        </div>

        {{-- Biteship ID --}}
        <div class="rounded-xl border p-4"
             style="background: var(--bg-card); border-color: var(--border-2)">
            <p class="text-[10px] font-bold uppercase tracking-wider mb-1.5" style="color: var(--text-5)">
                Biteship ID
            </p>
            @if($order->biteship_order_id)
                <p class="text-xs font-mono font-bold truncate" style="color: var(--text-1)">
                    {{ $order->biteship_order_id }}
                </p>
            @else
                <p class="text-xs font-semibold" style="color: #fbbf24;">
                    Belum dibuat
                </p>
            @endif
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- BELUM ADA BITESHIP ORDER --}}
    {{-- ============================================ --}}
    @if(!$order->biteship_order_id)
        <div class="rounded-2xl border overflow-hidden"
             style="background: var(--bg-card); border-color: #fbbf24;">

            <div class="px-5 py-4 border-b flex items-start gap-3"
                 style="background: linear-gradient(90deg, rgba(251, 191, 36, 0.15) 0%, transparent 100%); border-color: var(--border-2);">

                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl
                             bg-amber-500/20 border border-amber-500/30 flex-shrink-0">
                    <iconify-icon icon="mdi:information-outline" class="text-amber-400 text-xl"></iconify-icon>
                </span>

                <div class="flex-1 min-w-0">
                    <h2 class="text-base font-bold" style="color: var(--text-1)">
                        Belum Ada Order Biteship
                    </h2>
                    <p class="text-xs mt-0.5" style="color: var(--text-5)">
                        Klik tombol di bawah untuk membuat order di Biteship.
                    </p>
                </div>
            </div>

            <div class="p-5 space-y-3">
                <p class="text-sm" style="color: var(--text-4)">
                    Pastikan hal berikut sebelum membuat order:
                </p>

                <ul class="space-y-2">
                    @php
                        $checklist = [
                            'Data pengiriman lengkap (nama, alamat, kode pos)',
                            'Kurir dan layanan sudah ditentukan',
                            'Berat produk sudah diisi dengan benar',
                        ];
                    @endphp
                    @foreach($checklist as $item)
                        <li class="flex items-start gap-2 text-xs" style="color: var(--text-4)">
                            <iconify-icon icon="mdi:check-circle-outline" class="text-[#ecbc42] text-base flex-shrink-0 mt-0.5"></iconify-icon>
                            {{ $item }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif


    {{-- ============================================ --}}
    {{-- TRACKING CONTENT --}}
    {{-- ============================================ --}}
    @if($order->biteship_order_id)

        {{-- Tracking Error --}}
        @if($trackingError)
            <div class="rounded-2xl border p-6 bg-red-500/10 border-red-500/30 text-center">
                <iconify-icon icon="mdi:alert-circle-outline" class="text-3xl text-red-400"></iconify-icon>
                <p class="mt-2 text-sm font-semibold text-red-400">
                    {{ $trackingError }}
                </p>
                <p class="text-xs text-red-400/70 mt-1">
                    Pastikan order sudah dikirim dan memiliki nomor resi.
                </p>
                <button type="button"
                        class="refresh-tracking-btn mt-4 inline-flex items-center gap-1.5 px-4 py-2 rounded-lg
                               text-xs font-bold transition-all active:scale-95
                               bg-red-500/20 border border-red-500/40 text-red-300
                               hover:bg-red-500/30">
                    <iconify-icon icon="mdi:refresh"></iconify-icon>
                    Coba Refresh
                </button>
            </div>
        @endif


        {{-- Tracking Detail --}}
        @if($tracking)
            @php
                $statusMap = [
                    'confirmed'    => ['label' => 'Dikonfirmasi',        'color' => '#a78bfa'],
                    'allocated'    => ['label' => 'Kurir Dialokasikan',  'color' => '#60a5fa'],
                    'picking_up'   => ['label' => 'Menjemput Paket',     'color' => '#60a5fa'],
                    'picked'       => ['label' => 'Paket Diambil',       'color' => '#60a5fa'],
                    'in_transit'   => ['label' => 'Dalam Perjalanan',    'color' => '#a78bfa'],
                    'dropping_off' => ['label' => 'Menuju Tujuan',       'color' => '#a78bfa'],
                    'delivered'    => ['label' => 'Telah Sampai',        'color' => '#34d399'],
                    'cancelled'    => ['label' => 'Dibatalkan',          'color' => '#f87171'],
                    'returned'     => ['label' => 'Dikembalikan',        'color' => '#f87171'],
                    'pending'      => ['label' => 'Menunggu',            'color' => '#fbbf24'],
                    'processing'   => ['label' => 'Diproses',            'color' => '#60a5fa'],
                    'shipped'      => ['label' => 'Dikirim',             'color' => '#a78bfa'],
                ];
                $status = $tracking['status'] ?? 'pending';
                $statusInfo = $statusMap[$status] ?? $statusMap['pending'];
                $sc = $statusInfo['color'];

                $lastHistory = $tracking['history'][0] ?? null;
                $lastUpdated = $lastHistory['updated_at'] ?? ($tracking['updated_at'] ?? now());
            @endphp

            {{-- Status Header Card --}}
            <div class="rounded-2xl border overflow-hidden"
                 style="background: var(--bg-card); border-color: var(--border-2);">

                <div class="px-5 py-5 border-b flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
                     style="background: linear-gradient(90deg, {{ $sc }}15 0%, transparent 100%); border-color: var(--border-2);">

                    <div class="flex items-center gap-4 min-w-0">
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl border flex-shrink-0"
                              style="background: {{ $sc }}20; border-color: {{ $sc }}40;">
                            <iconify-icon icon="mdi:package-variant-closed" class="text-2xl" style="color: {{ $sc }};"></iconify-icon>
                        </span>

                        <div class="min-w-0">
                            <p class="text-[10px] font-bold uppercase tracking-wider mb-1" style="color: var(--text-5)">
                                Status Pengiriman
                            </p>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-bold border"
                                  style="background: {{ $sc }}15; border-color: {{ $sc }}40; color: {{ $sc }};">
                                <span class="w-1.5 h-1.5 rounded-full" style="background: currentColor;"></span>
                                {{ $statusInfo['label'] }}
                            </span>
                        </div>
                    </div>

                    <div class="text-left sm:text-right flex-shrink-0">
                        <p class="text-[10px] font-bold uppercase tracking-wider mb-1" style="color: var(--text-5)">
                            Terakhir Diperbarui
                        </p>
                        <p class="text-xs font-semibold" style="color: var(--text-3)">
                            {{ \Carbon\Carbon::parse($lastUpdated)->locale('id')->isoFormat('DD MMM YYYY, HH:mm') }}
                        </p>
                    </div>
                </div>

                {{-- Courier Info Grid --}}
                <div class="grid grid-cols-2 sm:grid-cols-4">
                    @php
                        $infoGrid = [
                            ['label' => 'Kurir',          'value' => strtoupper($tracking['courier']['company'] ?? $tracking['courier_name'] ?? $order->courier ?? '-')],
                            ['label' => 'Layanan',        'value' => strtoupper($tracking['courier']['type'] ?? $tracking['service'] ?? $order->service ?? '-')],
                            ['label' => 'Nomor Resi',     'value' => $tracking['waybill_id'] ?? $order->tracking_number ?? '-'],
                            ['label' => 'Estimasi Tiba',  'value' => (function() use ($tracking) {
                                if (isset($tracking['delivery']['datetime'])) {
                                    return \Carbon\Carbon::parse($tracking['delivery']['datetime'])->locale('id')->isoFormat('DD MMM YYYY, HH:mm');
                                }
                                if (isset($tracking['delivery_date'])) {
                                    return \Carbon\Carbon::parse($tracking['delivery_date'])->locale('id')->isoFormat('DD MMM YYYY, HH:mm');
                                }
                                return '-';
                            })()],
                        ];
                    @endphp

                    @foreach($infoGrid as $i => $info)
                        <div class="p-4 border-b sm:border-b-0"
                             style="border-color: var(--border-1); {{ $i < count($infoGrid) - 1 ? 'border-right: 1px solid var(--border-1);' : '' }}">
                            <p class="text-[10px] font-bold uppercase tracking-wider mb-1" style="color: var(--text-5)">
                                {{ $info['label'] }}
                            </p>
                            <p class="text-xs font-bold truncate" style="color: var(--text-1)">
                                {{ $info['value'] }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>


            {{-- Timeline Card --}}
            <div class="rounded-2xl border overflow-hidden"
                 style="background: var(--bg-card); border-color: var(--border-2);">

                <div class="px-5 py-4 border-b flex items-center gap-2"
                     style="background: var(--bg-input); border-color: var(--border-2);">
                    <iconify-icon icon="mdi:history" class="text-[#ecbc42] text-base"></iconify-icon>
                    <h2 class="font-bold text-sm" style="color: var(--text-1)">Riwayat Pengiriman</h2>
                </div>

                <div class="p-5">
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
                        <div class="py-10 text-center">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3 border"
                                 style="background: var(--bg-input); border-color: var(--border-2);">
                                <iconify-icon icon="mdi:package-variant" class="text-2xl" style="color: var(--text-6);"></iconify-icon>
                            </div>
                            <p class="text-sm font-semibold" style="color: var(--text-4);">
                                Belum ada riwayat tracking
                            </p>
                            <p class="text-xs mt-1" style="color: var(--text-5);">
                                Tracking akan muncul setelah paket diproses oleh kurir.
                            </p>
                        </div>
                    @else
                        <div class="space-y-0">
                            @foreach($histories as $history)
                                @php
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

                                    // Warna dot berdasarkan status
                                    $dotColor = '#60a5fa';
                                    if ($rawStatus === 'delivered') $dotColor = '#34d399';
                                    elseif ($rawStatus === 'cancelled' || $rawStatus === 'returned') $dotColor = '#f87171';
                                    elseif ($rawStatus === 'pending') $dotColor = '#fbbf24';
                                @endphp

                                <div class="relative flex gap-4 pb-6 pl-6 last:pb-0">
                                    {{-- Vertical Line --}}
                                    @if(!$loop->last)
                                        <div class="absolute left-2 top-6 h-full w-0.5"
                                             style="background: var(--border-2);"></div>
                                    @endif

                                    {{-- Dot --}}
                                    <div class="relative z-10 flex h-4 w-4 shrink-0 items-center justify-center rounded-full"
                                         style="background: {{ $dotColor }};
                                                @if($loop->first)
                                                    box-shadow: 0 0 0 4px {{ $dotColor }}30;
                                                @endif">
                                        @if($loop->first)
                                            <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
                                        @endif
                                    </div>

                                    {{-- Content --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-wrap items-start justify-between gap-2">
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold" style="color: var(--text-1);">
                                                    {{ $displayText }}
                                                </p>

                                                @if(isset($history['location']) && $history['location'])
                                                    <p class="text-xs flex items-center gap-1 mt-1" style="color: var(--text-5);">
                                                        <iconify-icon icon="mdi:map-marker-outline"></iconify-icon>
                                                        {{ $history['location'] }}
                                                    </p>
                                                @endif
                                            </div>
                                            <span class="text-[10px] font-mono whitespace-nowrap flex-shrink-0"
                                                  style="color: var(--text-5);">
                                                {{ $timeString ? \Carbon\Carbon::parse($timeString)->locale('id')->isoFormat('DD MMM YYYY, HH:mm') : '-' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>


            {{-- Waybill Button --}}
            @if(isset($tracking['waybill_url']))
                <div class="flex justify-end">
                    <a href="{{ $tracking['waybill_url'] }}"
                       target="_blank"
                       class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-lg
                              text-sm font-bold transition-all active:scale-95
                              bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                              text-slate-900
                              shadow-lg shadow-amber-500/20
                              hover:shadow-xl hover:shadow-amber-500/40">
                        <iconify-icon icon="mdi:printer-outline"></iconify-icon>
                        Cetak Resi
                    </a>
                </div>
            @endif
        @endif
    @endif

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.refresh-tracking-btn').forEach(function(refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            const btn = this;
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<iconify-icon icon="mdi:loading" class="animate-spin"></iconify-icon> Memuat...';
            btn.disabled = true;

            const url = '{{ route('admin.orders.tracking.refresh', $order) }}';

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.simulated) {
                        alert(data.message || 'Data tracking Biteship tidak ditemukan, menampilkan data lokal.');
                    }
                    location.reload();
                } else {
                    alert('Gagal refresh tracking: ' + (data.message || 'Unknown error'));
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Refresh error:', error);
                alert('Terjadi kesalahan: ' + error.message);
                btn.innerHTML = originalHTML;
                btn.disabled = false;
            });
        });
    });
});
</script>
@endpush