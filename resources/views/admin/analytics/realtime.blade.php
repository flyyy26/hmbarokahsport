@extends('layouts.admin')
@section('title', 'Realtime Analytics')
@section('page-title', 'Realtime Analytics')

@section('content')
<div class="w-full space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold flex items-center gap-2" style="color: var(--text-1)">
                <iconify-icon icon="mdi:pulse" class="text-2xl" style="color: #34d399"></iconify-icon>
                Realtime Analytics
            </h1>
            <p class="text-sm mt-0.5" style="color: var(--text-5)">
                Update otomatis setiap 10 detik • Sumber: GA4 Realtime API
            </p>
        </div>
        <div class="flex items-center gap-2 text-xs" style="color: var(--text-5)">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span id="last-update">Baru saja</span>
        </div>
    </div>

    {{-- ACTIVE USERS --}}
    <div class="rounded-2xl border p-6"
         style="background: linear-gradient(135deg, rgba(52,211,153,0.15), var(--bg-card));
                border-color: rgba(52,211,153,0.4);">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider" style="color: #34d399;">
                    🟢 User Aktif Sekarang
                </p>
                <p class="text-5xl font-bold mt-1" style="color: var(--text-1);" id="active-users">
                    {{ $realtime['activeUsers'] }}
                </p>
                <p class="text-xs mt-1" style="color: var(--text-5);">30 menit terakhir</p>
            </div>
            <div class="text-right">
                <p class="text-xs font-bold uppercase" style="color: var(--text-5);">Total Event</p>
                <p class="text-3xl font-bold" style="color: #ecbc42;" id="total-events">
                    {{ number_format($realtime['totalEvents']) }}
                </p>
            </div>
        </div>
    </div>

    {{-- GRID: EVENTS + PAGES --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Top Events --}}
        <div class="rounded-2xl border overflow-hidden"
             style="background: var(--bg-card); border-color: var(--border-2);">
            <div class="px-5 py-3 border-b" style="background: var(--bg-input); border-color: var(--border-2);">
                <h3 class="font-bold text-sm flex items-center gap-2" style="color: var(--text-1);">
                    <iconify-icon icon="mdi:lightning-bolt" style="color: #ecbc42;"></iconify-icon>
                    Event Terkirim (30 Menit Terakhir)
                </h3>
            </div>
            <div class="p-4 space-y-2" id="events-list">
                @forelse($realtime['topEvents'] as $event)
                    <div class="flex items-center justify-between py-2 border-b last:border-0"
                         style="border-color: var(--border-1);">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full
                                {{ in_array($event['event'], ['view_item', 'add_to_cart', 'begin_checkout', 'purchase']) ? 'bg-emerald-500' : 'bg-slate-400' }}">
                            </span>
                            <code class="text-xs" style="color: var(--text-2);">{{ $event['event'] }}</code>
                            @if(in_array($event['event'], ['view_item', 'add_to_cart', 'begin_checkout', 'purchase']))
                                <span class="text-[9px] px-1.5 py-0.5 rounded"
                                      style="background: rgba(52,211,153,0.15); color: #34d399;">
                                    E-COMMERCE
                                </span>
                            @endif
                        </div>
                        <span class="text-xs font-bold" style="color: #ecbc42;">
                            {{ number_format($event['count']) }}
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-center py-6" style="color: var(--text-5);">
                        Belum ada event dalam 30 menit terakhir.
                    </p>
                @endforelse
            </div>
        </div>

        {{-- Top Pages --}}
        <div class="rounded-2xl border overflow-hidden"
             style="background: var(--bg-card); border-color: var(--border-2);">
            <div class="px-5 py-3 border-b" style="background: var(--bg-input); border-color: var(--border-2);">
                <h3 class="font-bold text-sm flex items-center gap-2" style="color: var(--text-1);">
                    <iconify-icon icon="mdi:file-eye-outline" style="color: #ecbc42;"></iconify-icon>
                    Halaman Sedang Dibuka
                </h3>
            </div>
            <div class="p-4 space-y-2" id="pages-list">
                @forelse($realtime['topPages'] as $page)
                    <div class="flex items-center justify-between py-2 border-b last:border-0"
                         style="border-color: var(--border-1);">
                        <code class="text-xs truncate flex-1" style="color: var(--text-2);" title="{{ $page['page'] }}">
                            {{ $page['page'] }}
                        </code>
                        <span class="text-xs font-bold whitespace-nowrap" style="color: #34d399;">
                            {{ $page['users'] }} user
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-center py-6" style="color: var(--text-5);">
                        Belum ada halaman yang dibuka.
                    </p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- DEVICE --}}
    <div class="rounded-2xl border p-5"
         style="background: var(--bg-card); border-color: var(--border-2);">
        <h3 class="font-bold text-sm mb-4 flex items-center gap-2" style="color: var(--text-1);">
            <iconify-icon icon="mdi:devices" style="color: #ecbc42;"></iconify-icon>
            Perangkat Aktif
        </h3>
        <div class="flex flex-wrap gap-3" id="device-list">
            @forelse($realtime['deviceBreakdown'] as $device)
                <div class="flex-1 min-w-[150px] rounded-xl border p-3"
                     style="background: var(--bg-input); border-color: var(--border-2);">
                    <p class="text-xs font-bold capitalize" style="color: var(--text-4);">
                        {{ $device['device'] }}
                    </p>
                    <p class="text-2xl font-bold" style="color: var(--text-1);">
                        {{ $device['users'] }}
                    </p>
                </div>
            @empty
                <p class="text-xs" style="color: var(--text-5);">Belum ada data perangkat.</p>
            @endforelse
        </div>
    </div>

</div>

@push('scripts')
<script>
// 🔥 Auto-refresh setiap 10 detik
function refreshRealtime() {
    fetch('{{ route("admin.analytics.realtime") }}?_t=' + Date.now(), {
        headers: { 
            'X-Requested-With': 'XMLHttpRequest',
            'Cache-Control': 'no-cache',
            'Pragma': 'no-cache'
        },
        cache: 'no-store'  // 🔥 INI KUNCINYA
    })
    .then(r => r.text())
    .then(html => {
        const doc = new DOMParser().parseFromString(html, 'text/html');

        const update = (id) => {
            const newVal = doc.getElementById(id)?.innerHTML;
            const el = document.getElementById(id);
            if (el && newVal) el.innerHTML = newVal;
        };

        update('active-users');
        update('total-events');
        update('events-list');
        update('pages-list');
        update('device-list');

        document.getElementById('last-update').textContent =
            new Date().toLocaleTimeString('id-ID');
    })
    .catch(e => console.error('Refresh error:', e));
}

setInterval(refreshRealtime, 10000);
</script>
@endpush
@endsection