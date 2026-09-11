@extends('layouts.admin')

@section('content')

<div class="ml-64 p-8">
    <div class="mx-auto max-w-5xl">

        {{-- Header --}}
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <a href="{{ route('admin.orders.show', $order) }}" class="text-sm text-blue-600 hover:text-blue-800">← Kembali ke Detail</a>
                <h1 class="mt-1 text-2xl font-bold text-slate-900">📦 Tracking Pengiriman</h1>
                <p class="text-sm text-slate-500">#{{ $order->order_number }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                {{-- 🔥 TOMBOL CREATE ORDER BITESHIP --}}
                @if(!$order->biteship_order_id)
                    <form action="{{ route('admin.orders.biteship.create', $order) }}" method="POST"
                          onsubmit="return confirm('Buat order di Biteship untuk order ini? Pastikan data pengiriman sudah lengkap.');">
                        @csrf
                        <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                            🚀 Buat Order Biteship
                        </button>
                    </form>
                @endif

                @if($order->tracking_number)
                    <span class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700">
                        Resi: {{ $order->tracking_number }}
                    </span>
                @endif

                @if($order->biteship_order_id)
                    <button type="button" class="refresh-tracking-btn rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                        🔄 Refresh
                    </button>
                @endif
            </div>
        </div>

        {{-- 🔥 ALERT --}}
        @if(session('success'))
            <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-emerald-700">
                <div class="flex items-start gap-3">
                    <iconify-icon icon="mdi:check-circle-outline" class="text-xl"></iconify-icon>
                    <div>
                        <p class="font-medium">{{ session('success') }}</p>
                        @if($order->biteship_order_id)
                            <p class="text-sm">Order ID: <span class="font-mono">{{ $order->biteship_order_id }}</span></p>
                            <p class="text-sm">Nomor Resi: <span class="font-mono">{{ $order->tracking_number ?? 'Belum tersedia' }}</span></p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">
                <div class="flex items-start gap-3">
                    <iconify-icon icon="mdi:alert-circle-outline" class="text-xl"></iconify-icon>
                    <div>
                        <p class="font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Order Info --}}
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-4">
            <div class="rounded-xl border border-slate-200 bg-white p-4">
                <p class="text-xs text-slate-400">Kurir</p>
                <p class="mt-1 font-semibold text-slate-900">{{ strtoupper($order->courier ?? '-') }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4">
                <p class="text-xs text-slate-400">Layanan</p>
                <p class="mt-1 font-semibold text-slate-900">{{ $order->service ?? '-' }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4">
                <p class="text-xs text-slate-400">Status Pengiriman</p>
                <p class="mt-1 font-semibold">
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                        @if($order->shipping_status == 'delivered') bg-emerald-100 text-emerald-800
                        @elseif($order->shipping_status == 'shipped') bg-indigo-100 text-indigo-800
                        @elseif($order->shipping_status == 'processing') bg-blue-100 text-blue-800
                        @else bg-yellow-100 text-yellow-800 @endif">
                        {{ $order->shipping_status_label }}
                    </span>
                </p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4">
                <p class="text-xs text-slate-400">Biteship ID</p>
                <p class="mt-1 font-mono text-sm font-semibold text-slate-900">
                    {{ $order->biteship_order_id ?? '-' }}
                    @if(!$order->biteship_order_id)
                        <span class="ml-2 text-xs text-yellow-600 font-sans">(Belum dibuat)</span>
                    @endif
                </p>
            </div>
        </div>

        {{-- 🔥 BITESHIP ORDER ID INFO --}}
        @if($order->biteship_order_id)
            <div class="mb-6 rounded-xl border border-slate-200 bg-white p-4">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <p class="text-sm text-slate-500">Biteship Order ID</p>
                            <p class="font-mono text-lg font-bold text-slate-900">{{ $order->biteship_order_id }}</p>
                        </div>
                    </div>
            </div>
        @else
            {{-- 🔥 INFO UNTUK MEMBUAT ORDER --}}
            <div class="mb-6 rounded-xl border border-yellow-200 bg-yellow-50 p-6">
                <div class="flex items-start gap-4">
                    <iconify-icon icon="mdi:information-outline" class="text-2xl text-yellow-600"></iconify-icon>
                    <div>
                        <h3 class="font-semibold text-yellow-800">Belum Ada Order Biteship</h3>
                        <p class="text-sm text-yellow-700 mt-1">
                            Order ini belum memiliki ID tracking dari Biteship.
                            Klik tombol <strong>"Buat Order Biteship"</strong> di atas untuk membuat order di Biteship.
                        </p>
                        <ul class="mt-2 text-sm text-yellow-700 list-disc list-inside space-y-1">
                            <li>Pastikan data pengiriman sudah lengkap (nama, alamat, kode pos)</li>
                            <li>Kurir dan layanan harus sudah ditentukan</li>
                            <li>Berat produk harus sudah diisi</li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Tracking Content --}}
        @if($order->biteship_order_id)
            @if($trackingError)
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-6 text-center">
                    <iconify-icon icon="mdi:alert-circle-outline" class="text-3xl text-red-500"></iconify-icon>
                    <p class="mt-2 text-sm text-red-700">{{ $trackingError }}</p>
                    <p class="text-xs text-red-400 mt-1">Pastikan order sudah dikirim dan memiliki nomor resi.</p>
                    <button type="button" class="refresh-tracking-btn mt-3 rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                        🔄 Coba Refresh
                    </button>
                </div>
            @endif

            @if($tracking)
                <div class="rounded-2xl border border-slate-200 bg-white p-6">

                    {{-- Tracking Status Header --}}
                    <div class="flex flex-wrap items-center justify-between border-b border-slate-200 pb-4">
                        <div>
                            <p class="text-sm text-slate-500">Status Pengiriman</p>
                            <p class="text-xl font-bold text-slate-900">
                                @php
                                    $statusMap = [
                                        'confirmed' => ['label' => 'Dikonfirmasi', 'color' => 'bg-indigo-100 text-indigo-800'],
                                        'allocated' => ['label' => 'Kurir Dialokasikan', 'color' => 'bg-blue-100 text-blue-800'],
                                        'picking_up' => ['label' => 'Menjemput Paket', 'color' => 'bg-blue-100 text-blue-800'],
                                        'picked' => ['label' => 'Paket Diambil', 'color' => 'bg-blue-100 text-blue-800'],
                                        'in_transit' => ['label' => 'Dalam Perjalanan', 'color' => 'bg-indigo-100 text-indigo-800'],
                                        'dropping_off' => ['label' => 'Menuju Tujuan', 'color' => 'bg-indigo-100 text-indigo-800'],
                                        'delivered' => ['label' => 'Telah Sampai', 'color' => 'bg-emerald-100 text-emerald-800'],
                                        'cancelled' => ['label' => 'Dibatalkan', 'color' => 'bg-red-100 text-red-800'],
                                        'returned' => ['label' => 'Dikembalikan', 'color' => 'bg-red-100 text-red-800'],
                                        'pending' => ['label' => 'Menunggu', 'color' => 'bg-yellow-100 text-yellow-800'],
                                        'processing' => ['label' => 'Diproses', 'color' => 'bg-blue-100 text-blue-800'],
                                        'shipped' => ['label' => 'Dikirim', 'color' => 'bg-indigo-100 text-indigo-800'],
                                    ];
                                    $status = $tracking['status'] ?? 'pending';
                                    $statusInfo = $statusMap[$status] ?? $statusMap['pending'];
                                @endphp
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $statusInfo['color'] }}">
                                    {{ $statusInfo['label'] }}
                                </span>
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-slate-400">Terakhir diperbarui</p>
                            <p class="text-sm font-medium text-slate-700">
                                @php
                                    $lastHistory = $tracking['history'][0] ?? null;
                                    $lastUpdated = $lastHistory['updated_at'] ?? ($tracking['updated_at'] ?? now());
                                @endphp
                                {{ \Carbon\Carbon::parse($lastUpdated)->locale('id')->isoFormat('DD MMMM YYYY HH:mm') }}
                            </p>
                        </div>
                    </div>

                    {{-- Courier Info --}}
                    <div class="mt-4 grid grid-cols-2 gap-4 rounded-lg bg-slate-50 p-4 text-sm">
                        <div>
                            <p class="text-slate-500">Kurir</p>
                            <p class="font-medium text-slate-900">{{ strtoupper($tracking['courier']['company'] ?? $tracking['courier_name'] ?? $order->courier ?? '-') }}</p>
                        </div>
                        <div>
                            <p class="text-slate-500">Nomor Resi</p>
                            <p class="font-medium text-slate-900">{{ $tracking['waybill_id'] ?? $order->tracking_number ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-slate-500">Layanan</p>
                            <p class="font-medium text-slate-900">{{ strtoupper($tracking['courier']['type'] ?? $tracking['service'] ?? $order->service ?? '-') }}</p>
                        </div>
                        <div>
                            <p class="text-slate-500">Estimasi Tiba</p>
                            <p class="font-medium text-slate-900">
                                @if(isset($tracking['delivery']['datetime']))
                                    {{ \Carbon\Carbon::parse($tracking['delivery']['datetime'])->locale('id')->isoFormat('DD MMMM YYYY HH:mm') }}
                                @elseif(isset($tracking['delivery_date']))
                                    {{ \Carbon\Carbon::parse($tracking['delivery_date'])->locale('id')->isoFormat('DD MMMM YYYY HH:mm') }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Tracking Timeline --}}
                    <div class="mt-6">
                        <h3 class="mb-4 font-semibold text-slate-900">Riwayat Pengiriman</h3>
                        <div class="relative">
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

                                $histories = $tracking['history'] ?? [];
                            @endphp

                            @if(empty($histories))
                                <div class="rounded-lg bg-slate-50 p-6 text-center text-sm text-slate-500">
                                    Belum ada riwayat tracking.
                                    <br><span class="text-xs">Tracking akan muncul setelah paket diproses oleh kurir.</span>
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
                                        @endphp
                                        <div class="relative flex gap-4 pb-6 pl-6 last:pb-0">
                                            {{-- Line --}}
                                            @if(!$loop->last)
                                                <div class="absolute left-2 top-6 h-full w-0.5 bg-slate-200"></div>
                                            @endif

                                            {{-- Dot --}}
                                            <div class="relative z-10 flex h-4 w-4 shrink-0 items-center justify-center rounded-full
                                                @if($loop->first) bg-emerald-500 ring-4 ring-emerald-100
                                                @elseif($rawStatus === 'delivered') bg-emerald-500
                                                @elseif($rawStatus === 'cancelled') bg-red-500
                                                @else bg-blue-500 @endif">
                                                @if($loop->first)
                                                    <span class="h-2 w-2 rounded-full bg-white"></span>
                                                @endif
                                            </div>

                                            {{-- Content --}}
                                            <div class="flex-1">
                                                <div class="flex flex-wrap items-start justify-between gap-2">
                                                    <div>
                                                        <p class="font-medium text-slate-900">
                                                            {{ $displayText }}
                                                        </p>
                                                        @if(isset($history['location']))
                                                            <p class="text-sm text-slate-500">
                                                                <iconify-icon icon="mdi:map-marker" class="inline"></iconify-icon>
                                                                {{ $history['location'] }}
                                                            </p>
                                                        @endif
                                                        @if(isset($history['note']) && $translatedNote !== $rawNote)
                                                            <p class="text-sm text-slate-400">{{ $history['note'] }}</p>
                                                        @endif
                                                    </div>
                                                    <span class="text-xs text-slate-400 whitespace-nowrap">
                                                        {{ $timeString ? \Carbon\Carbon::parse($timeString)->locale('id')->isoFormat('DD MMMM YYYY HH:mm') : '-' }}
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
                        <div class="mt-6 border-t border-slate-200 pt-6">
                            <a href="{{ $tracking['waybill_url'] }}" target="_blank"
                               class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-6 py-3 text-sm font-medium text-white hover:bg-slate-800">
                                <iconify-icon icon="mdi:printer-outline"></iconify-icon>
                                Cetak Resi
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        @endif

    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 🔥 pakai class agar bisa ada 2 tombol sekaligus (refresh + coba refresh)
    document.querySelectorAll('.refresh-tracking-btn').forEach(function(refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            const btn = this;
            const originalText = btn.innerHTML;
            btn.innerHTML = '⏳ Memuat...';
            btn.disabled = true;

            // 🔥 GUNAKAN NAMED ROUTE (agar selalu sesuai prefix admin)
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
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Refresh error:', error);
                alert('Terjadi kesalahan: ' + error.message);
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });
    });
});
</script>
@endpush