@extends('layouts.admin')

@section('title', 'Analytics')
@section('page-title', 'Analytics')

@section('content')

<div class="w-full space-y-4">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold flex items-center gap-2" style="color: var(--text-1)">
                <iconify-icon icon="mdi:chart-line" class="text-2xl" style="color: #ecbc42"></iconify-icon>
                Analytics
            </h1>
            <p class="text-sm mt-0.5" style="color: var(--text-5)">
                Data traffic website real-time dari database Anda
            </p>
        </div>

        <form method="GET" class="flex gap-2">
            <select name="days" onchange="this.form.submit()"
                    class="px-3 py-1.5 rounded-lg text-sm font-semibold"
                    style="background: var(--bg-input); border: 1px solid var(--border-2); color: var(--text-1);">
                <option value="1"  {{ $days == 1 ? 'selected' : '' }}>Hari Ini</option>
                <option value="7"  {{ $days == 7 ? 'selected' : '' }}>7 Hari Terakhir</option>
                <option value="30" {{ $days == 30 ? 'selected' : '' }}>30 Hari Terakhir</option>
                <option value="90" {{ $days == 90 ? 'selected' : '' }}>90 Hari Terakhir</option>
            </select>
        </form>
    </div>

    {{-- ============================================ --}}
    {{-- REALTIME BANNER --}}
    {{-- ============================================ --}}
    <div class="rounded-xl border p-4"
         style="background: linear-gradient(135deg, rgba(52,211,153,0.15), var(--bg-card));
                border-color: rgba(52,211,153,0.4);">
        <div class="flex items-center gap-3">
            <div class="relative flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
            </div>
            <div class="flex-1 flex items-baseline gap-2">
                <p class="text-[10px] font-bold uppercase tracking-wider" style="color: #34d399;">
                    Realtime — 5 Menit Terakhir
                </p>
                <p class="text-2xl font-bold leading-none" style="color: var(--text-1);">
                    {{ $activeVisitors }}
                    <span class="text-sm font-medium" style="color: var(--text-5);">visitor aktif</span>
                </p>
            </div>
        </div>

        @if($realtimeVisits->isNotEmpty())
            <div class="mt-3 pt-3 border-t" style="border-color: rgba(52,211,153,0.3);">
                <p class="text-[10px] font-bold uppercase tracking-wider mb-1.5" style="color: var(--text-5);">
                    Request Terbaru
                </p>
                <div class="space-y-0.5 max-h-40 overflow-y-auto">
                    @foreach($realtimeVisits as $visit)
                        <div class="flex items-center justify-between text-xs py-0.5">
                            <div class="flex items-center gap-2 min-w-0 flex-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 flex-shrink-0"></span>
                                <code class="truncate" style="color: var(--text-2);">{{ $visit->url }}</code>
                                @if($visit->user)
                                    <span class="text-[10px] px-1.5 py-0.5 rounded whitespace-nowrap"
                                          style="background: rgba(236,188,66,0.15); color: #ecbc42;">
                                        {{ $visit->user->name }}
                                    </span>
                                @endif
                            </div>
                            <span class="text-[10px] whitespace-nowrap ml-2" style="color: var(--text-5);">
                                {{ $visit->visited_at->diffForHumans() }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- ============================================ --}}
    {{-- OVERVIEW CARDS (compact) --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">

        <div class="rounded-xl border p-3 flex items-center gap-2.5"
             style="background: var(--bg-card); border-color: var(--border-2);">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0
                        bg-blue-500/10 border border-blue-500/30">
                <iconify-icon icon="mdi:eye-outline" class="text-blue-400 text-base"></iconify-icon>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold uppercase tracking-wider leading-none" style="color: var(--text-5);">Total Views</p>
                <p class="text-lg font-bold leading-tight mt-1" style="color: var(--text-1);">
                    {{ number_format($totalViews) }}
                </p>
            </div>
        </div>

        <div class="rounded-xl border p-3 flex items-center gap-2.5"
             style="background: var(--bg-card); border-color: var(--border-2);">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0
                        bg-purple-500/10 border border-purple-500/30">
                <iconify-icon icon="mdi:account-multiple-outline" class="text-purple-400 text-base"></iconify-icon>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold uppercase tracking-wider leading-none" style="color: var(--text-5);">Unique Visitors</p>
                <p class="text-lg font-bold leading-tight mt-1" style="color: var(--text-1);">
                    {{ number_format($uniqueVisitors) }}
                </p>
            </div>
        </div>

        <div class="rounded-xl border p-3 flex items-center gap-2.5"
             style="background: var(--bg-card); border-color: var(--border-2);">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0
                        bg-emerald-500/10 border border-emerald-500/30">
                <iconify-icon icon="mdi:swap-horizontal" class="text-emerald-400 text-base"></iconify-icon>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold uppercase tracking-wider leading-none" style="color: var(--text-5);">Sessions</p>
                <p class="text-lg font-bold leading-tight mt-1" style="color: var(--text-1);">
                    {{ number_format($uniqueSessions) }}
                </p>
            </div>
        </div>

        <div class="rounded-xl border p-3 flex items-center gap-2.5"
             style="background: var(--bg-card); border-color: var(--border-2);">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0
                        bg-amber-500/10 border border-amber-500/30">
                <iconify-icon icon="mdi:timer-outline" class="text-amber-400 text-base"></iconify-icon>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold uppercase tracking-wider leading-none" style="color: var(--text-5);">Avg Response</p>
                <p class="text-lg font-bold leading-tight mt-1" style="color: var(--text-1);">
                    {{ round($avgResponseTime) }} ms
                </p>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- TOP PAGES + DEVICE --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">

        {{-- Top Pages --}}
        <div class="lg:col-span-2 rounded-xl border overflow-hidden"
             style="background: var(--bg-card); border-color: var(--border-2);">
            <div class="px-4 py-2.5 border-b" style="background: var(--bg-input); border-color: var(--border-2);">
                <h3 class="font-bold text-sm flex items-center gap-2" style="color: var(--text-1);">
                    <iconify-icon icon="mdi:file-document-outline" style="color: #ecbc42;"></iconify-icon>
                    Halaman Terpopuler
                </h3>
            </div>
            <div class="p-3 max-h-80 overflow-y-auto">
                @forelse($topPages as $page)
                    <div class="flex items-center justify-between gap-3 py-1.5 border-b last:border-0"
                         style="border-color: var(--border-1);">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-mono truncate" style="color: var(--text-2);" title="{{ $page->url }}">
                                {{ $page->url }}
                            </p>
                            <p class="text-[10px]" style="color: var(--text-5);">
                                {{ $page->visitors }} visitor · avg {{ round($page->avg_time) }}ms
                            </p>
                        </div>
                        <span class="text-xs font-bold whitespace-nowrap" style="color: #ecbc42;">
                            {{ number_format($page->views) }} views
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-center py-5" style="color: var(--text-5);">
                        Belum ada data kunjungan.
                    </p>
                @endforelse
            </div>
        </div>

        {{-- Device --}}
        <div class="rounded-xl border overflow-hidden"
             style="background: var(--bg-card); border-color: var(--border-2);">
            <div class="px-4 py-2.5 border-b" style="background: var(--bg-input); border-color: var(--border-2);">
                <h3 class="font-bold text-sm flex items-center gap-2" style="color: var(--text-1);">
                    <iconify-icon icon="mdi:devices" style="color: #ecbc42;"></iconify-icon>
                    Perangkat
                </h3>
            </div>
            <div class="p-3">
                @php $deviceTotal = array_sum($deviceStats); @endphp
                @forelse($deviceStats as $device => $count)
                    @php $percent = $deviceTotal > 0 ? ($count / $deviceTotal) * 100 : 0; @endphp
                    <div class="mb-2 last:mb-0">
                        <div class="flex items-center justify-between text-xs mb-0.5">
                            <span class="font-semibold" style="color: var(--text-2);">{{ $device }}</span>
                            <span class="font-bold" style="color: var(--text-1);">
                                {{ $count }}
                                <span class="text-[10px] font-normal" style="color: var(--text-5);">
                                    ({{ round($percent, 1) }}%)
                                </span>
                            </span>
                        </div>
                        <div class="h-1.5 rounded-full overflow-hidden" style="background: var(--bg-elevated);">
                            <div class="h-full rounded-full"
                                 style="width: {{ $percent }}%;
                                        background: {{ $device === 'Mobile' ? '#10b981' : ($device === 'Tablet' ? '#f59e0b' : '#3b82f6') }};">
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-center py-4" style="color: var(--text-5);">Belum ada data</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- BROWSER + REFERRERS --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">

        {{-- Browser --}}
        <div class="rounded-xl border overflow-hidden"
             style="background: var(--bg-card); border-color: var(--border-2);">
            <div class="px-4 py-2.5 border-b" style="background: var(--bg-input); border-color: var(--border-2);">
                <h3 class="font-bold text-sm flex items-center gap-2" style="color: var(--text-1);">
                    <iconify-icon icon="mdi:web" style="color: #ecbc42;"></iconify-icon>
                    Browser
                </h3>
            </div>
            <div class="p-3">
                @php $browserTotal = array_sum($browserStats); @endphp
                @forelse($browserStats as $browser => $count)
                    @php $percent = $browserTotal > 0 ? ($count / $browserTotal) * 100 : 0; @endphp
                    <div class="mb-2 last:mb-0">
                        <div class="flex items-center justify-between text-xs mb-0.5">
                            <span class="font-semibold" style="color: var(--text-2);">{{ $browser }}</span>
                            <span class="font-bold" style="color: var(--text-1);">
                                {{ $count }}
                                <span class="text-[10px] font-normal" style="color: var(--text-5);">
                                    ({{ round($percent, 1) }}%)
                                </span>
                            </span>
                        </div>
                        <div class="h-1.5 rounded-full overflow-hidden" style="background: var(--bg-elevated);">
                            <div class="h-full rounded-full" style="width: {{ $percent }}%; background: #ecbc42;"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-center py-4" style="color: var(--text-5);">Belum ada data</p>
                @endforelse
            </div>
        </div>

        {{-- Referrers --}}
        <div class="rounded-xl border overflow-hidden"
             style="background: var(--bg-card); border-color: var(--border-2);">
            <div class="px-4 py-2.5 border-b" style="background: var(--bg-input); border-color: var(--border-2);">
                <h3 class="font-bold text-sm flex items-center gap-2" style="color: var(--text-1);">
                    <iconify-icon icon="mdi:source-branch" style="color: #ecbc42;"></iconify-icon>
                    Sumber Traffic
                </h3>
            </div>
            <div class="p-3 max-h-72 overflow-y-auto">
                @forelse($topReferrers as $ref)
                    <div class="flex items-center justify-between gap-3 py-1.5 border-b last:border-0"
                         style="border-color: var(--border-1);">
                        <span class="text-xs font-semibold truncate" style="color: var(--text-2);"
                              title="{{ $ref->domain }}">
                            {{ $ref->domain }}
                        </span>
                        <span class="text-xs font-bold whitespace-nowrap" style="color: #10b981;">
                            {{ $ref->hits }}
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-center py-5" style="color: var(--text-5);">
                        Belum ada referrer eksternal.
                    </p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- TOP IPs + ADD TO CART (side by side) --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">

        {{-- Top IPs --}}
        <div class="rounded-xl border overflow-hidden"
             style="background: var(--bg-card); border-color: var(--border-2);">
            <div class="px-4 py-2.5 border-b" style="background: var(--bg-input); border-color: var(--border-2);">
                <h3 class="font-bold text-sm flex items-center gap-2" style="color: var(--text-1);">
                    <iconify-icon icon="mdi:ip-network-outline" style="color: #ecbc42;"></iconify-icon>
                    Top IP Addresses
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="border-b" style="background: var(--bg-input); border-color: var(--border-2);">
                        <tr>
                            <th class="px-3 py-1.5 text-left text-[10px] font-bold uppercase" style="color: var(--text-5);">IP</th>
                            <th class="px-3 py-1.5 text-right text-[10px] font-bold uppercase" style="color: var(--text-5);">Hits</th>
                            <th class="px-3 py-1.5 text-right text-[10px] font-bold uppercase" style="color: var(--text-5);">Terakhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topIps as $ip)
                            <tr class="border-b last:border-0" style="border-color: var(--border-1);">
                                <td class="px-3 py-1.5">
                                    <code class="text-xs" style="color: var(--text-2);">{{ $ip->ip }}</code>
                                </td>
                                <td class="px-3 py-1.5 text-right">
                                    <span class="text-xs font-bold" style="color: #ecbc42;">{{ $ip->hits }}</span>
                                </td>
                                <td class="px-3 py-1.5 text-right">
                                    <span class="text-[10px]" style="color: var(--text-5);">
                                        {{ \Carbon\Carbon::parse($ip->last_seen)->diffForHumans() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-3 py-5 text-center text-xs" style="color: var(--text-5);">
                                    Belum ada data
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Add to Cart --}}
        <div class="rounded-xl border overflow-hidden"
             style="background: var(--bg-card); border-color: var(--border-2);">
            <div class="px-4 py-2.5 border-b" style="background: var(--bg-input); border-color: var(--border-2);">
                <h3 class="font-bold text-sm flex items-center gap-2" style="color: var(--text-1);">
                    <iconify-icon icon="mdi:cart-outline" style="color: #ecbc42;"></iconify-icon>
                    Paling Ditambahkan ke Keranjang
                </h3>
            </div>
            <div class="p-3 max-h-72 overflow-y-auto">
                @forelse($topAddToCart as $product)
                    <div class="flex items-center justify-between gap-3 py-1.5 border-b last:border-0"
                         style="border-color: var(--border-1);">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-medium truncate" style="color: var(--text-1);" title="{{ $product->name }}">
                                {{ $product->name }}
                            </p>
                            <p class="text-[10px]" style="color: var(--text-5);">
                                {{ number_format($product->total_qty) }} item · {{ $product->additions }} sesi
                            </p>
                        </div>
                        <span class="text-xs font-bold whitespace-nowrap" style="color: #ecbc42;">
                            {{ $product->additions }}x
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-center py-5" style="color: var(--text-5);">
                        Belum ada data add to cart.
                    </p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- TOP PRODUCT VIEWS --}}
    {{-- ============================================ --}}
    <div class="rounded-xl border overflow-hidden"
        style="background: var(--bg-card); border-color: var(--border-2);">
        <div class="px-4 py-2.5 border-b flex items-center justify-between"
            style="background: var(--bg-input); border-color: var(--border-2);">
            <h3 class="font-bold text-sm flex items-center gap-2" style="color: var(--text-1);">
                <iconify-icon icon="mdi:eye-outline" style="color: #ecbc42;"></iconify-icon>
                Produk Dilihat
            </h3>
            <span class="text-[10px]" style="color: var(--text-5);">
                Termasuk guest (belum login)
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="border-b" style="background: var(--bg-input); border-color: var(--border-2);">
                    <tr>
                        <th class="px-3 py-1.5 text-left text-[10px] font-bold uppercase" style="color: var(--text-5);">#</th>
                        <th class="px-3 py-1.5 text-left text-[10px] font-bold uppercase" style="color: var(--text-5);">Produk</th>
                        <th class="px-3 py-1.5 text-right text-[10px] font-bold uppercase" style="color: var(--text-5);">Views</th>
                        <th class="px-3 py-1.5 text-right text-[10px] font-bold uppercase" style="color: var(--text-5);">Visitor</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topProductViews as $index => $product)
                        <tr class="border-b last:border-0" style="border-color: var(--border-1);">
                            <td class="px-3 py-1.5">
                                <span class="text-xs font-bold" style="color: var(--text-4);">
                                    {{ $index + 1 }}
                                </span>
                            </td>
                            <td class="px-3 py-1.5">
                                <a href="{{ route('customer.products.show', $product->slug) }}"
                                target="_blank"
                                class="text-xs font-medium hover:underline"
                                style="color: var(--text-1);">
                                    {{ $product->name }}
                                </a>
                            </td>
                            <td class="px-3 py-1.5 text-right">
                                <span class="text-xs font-bold" style="color: #ecbc42;">
                                    {{ number_format($product->views) }}
                                </span>
                            </td>
                            <td class="px-3 py-1.5 text-right">
                                <span class="text-xs" style="color: var(--text-4);">
                                    {{ number_format($product->visitors) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-3 py-5 text-center text-xs" style="color: var(--text-5);">
                                Belum ada data views produk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- TRENDING SEARCHES --}}
    {{-- ============================================ --}}
    <div class="rounded-xl border overflow-hidden"
        style="background: var(--bg-card); border-color: var(--border-2);">
        <div class="px-4 py-2.5 border-b flex items-center justify-between"
            style="background: var(--bg-input); border-color: var(--border-2);">
            <h3 class="font-bold text-sm flex items-center gap-2" style="color: var(--text-1);">
                <iconify-icon icon="mdi:magnify" style="color: #ecbc42;"></iconify-icon>
                Pencarian Populer
            </h3>
            <span class="text-[10px]" style="color: var(--text-5);">
                Termasuk guest (belum login)
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="border-b" style="background: var(--bg-input); border-color: var(--border-2);">
                    <tr>
                        <th class="px-3 py-1.5 text-left text-[10px] font-bold uppercase" style="color: var(--text-5);">#</th>
                        <th class="px-3 py-1.5 text-left text-[10px] font-bold uppercase" style="color: var(--text-5);">Keyword</th>
                        <th class="px-3 py-1.5 text-right text-[10px] font-bold uppercase" style="color: var(--text-5);">Total</th>
                        <th class="px-3 py-1.5 text-right text-[10px] font-bold uppercase" style="color: var(--text-5);">Visitor</th>
                        <th class="px-3 py-1.5 text-right text-[10px] font-bold uppercase" style="color: var(--text-5);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trendingSearches as $index => $search)
                        <tr class="border-b last:border-0" style="border-color: var(--border-1);">
                            <td class="px-3 py-1.5">
                                <span class="text-xs font-bold" style="color: var(--text-4);">
                                    {{ $index + 1 }}
                                </span>
                            </td>
                            <td class="px-3 py-1.5">
                                <span class="text-xs font-medium" style="color: var(--text-2);">
                                    {{ $search->keyword }}
                                </span>
                            </td>
                            <td class="px-3 py-1.5 text-right">
                                <span class="text-xs font-bold" style="color: #ecbc42;">
                                    {{ number_format($search->total) }}x
                                </span>
                            </td>
                            <td class="px-3 py-1.5 text-right">
                                <span class="text-xs" style="color: var(--text-4);">
                                    {{ number_format($search->unique_visitors) }}
                                </span>
                            </td>
                            <td class="px-3 py-1.5 text-right">
                                <a href="{{ route('customer.products.index', ['search' => $search->keyword]) }}"
                                target="_blank"
                                class="text-[10px] font-bold px-2 py-0.5 rounded transition-all"
                                style="background: var(--bg-input); border: 1px solid var(--border-2); color: var(--text-4);"
                                onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                                onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-4)'">
                                    Cek
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-5 text-center text-xs" style="color: var(--text-5);">
                                Belum ada data pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection