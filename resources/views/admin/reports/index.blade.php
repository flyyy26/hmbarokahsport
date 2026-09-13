@extends('layouts.admin')

@section('title', 'Laporan')
@section('page-title', 'Laporan')

@section('content')

<div class="w-full space-y-6">

    {{-- ============================================ --}}
    {{-- TOP BAR --}}
    {{-- ============================================ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold flex items-center gap-2" style="color: var(--text-1)">
                <iconify-icon icon="mdi:file-chart-outline" class="text-2xl" style="color: var(--gold-bright)"></iconify-icon>
                Laporan Penjualan
            </h1>
            <p class="text-sm mt-0.5" style="color: var(--text-5)">
                Ringkasan performa toko — laporan lengkap penjualan, stok, dan pelanggan
            </p>
        </div>

        <div class="flex items-center gap-2">
            <form id="exportForm" method="GET" action="{{ route('admin.reports.export') }}">
                <input type="hidden" name="month" value="{{ $monthParam }}">
                <input type="hidden" name="report_type" value="index">
                <button type="submit" name="format" value="csv"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg
                               text-xs font-bold border transition-all active:scale-95"
                        style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)"
                        onmouseover="this.style.borderColor='var(--gold-bright)'; this.style.color='var(--gold-dark)'"
                        onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'">
                    <iconify-icon icon="mdi:download-outline"></iconify-icon>
                    Export CSV
                </button>
            </form>
            <form id="exportPdfForm" method="GET" action="{{ route('admin.reports.export') }}">
                <input type="hidden" name="month" value="{{ $monthParam }}">
                <input type="hidden" name="report_type" value="index">
                <button type="submit" name="format" value="pdf"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg
                               text-xs font-bold border transition-all active:scale-95"
                        style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)"
                        onmouseover="this.style.borderColor='var(--gold-bright)'; this.style.color='var(--gold-dark)'"
                        onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'">
                    <iconify-icon icon="mdi:file-pdf-outline"></iconify-icon>
                    Export PDF
                </button>
            </form>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- MONTH FILTER --}}
    {{-- ============================================ --}}
    <form method="GET" action="{{ route('admin.reports.index') }}" id="monthFilterForm">
        <div class="flex flex-col sm:flex-row sm:items-end gap-3">
            <div class="flex flex-col gap-1.5">
                <label class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                    Bulan
                </label>
                <input type="month" name="month" value="{{ $monthParam }}"
                       onchange="this.form.submit()"
                       class="form-input text-xs py-2 pl-9 pr-4 cursor-pointer rounded-lg"
                       style="background: var(--bg-input); border: 1px solid var(--border-2); color: var(--text-1); min-width: 180px;">
            </div>
        </div>
    </form>

    {{-- ============================================ --}}
    {{-- ALL METRICS CARDS --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Total Penjualan --}}
        <div class="rounded-2xl border p-4 transition-colors"
             style="background: var(--bg-card); border-color: var(--border-2);"
             onmouseover="this.style.borderColor='rgba(236,188,66,0.3)'"
             onmouseout="this.style.borderColor='var(--border-2)'">
            <div class="flex items-start gap-3">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background: var(--bg-elevated);">
                    <iconify-icon icon="mdi:wallet-outline" class="text-xl" style="color:#ecbc42"></iconify-icon>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5);">
                        Total Penjualan
                    </p>
                    <p class="text-xl font-bold mt-0.5" style="color: var(--text-1);">
                        Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                    </p>
                    <p class="text-[10px] mt-0.5" style="color: var(--text-5)">
                        Online: Rp {{ number_format($onlineRevenue, 0, ',', '.') }} | Offline: Rp {{ number_format($offlineRevenue, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Total Ongkir --}}
        <div class="rounded-2xl border p-4 transition-colors"
             style="background: var(--bg-card); border-color: var(--border-2);"
             onmouseover="this.style.borderColor='rgba(52,211,153,0.3)'"
             onmouseout="this.style.borderColor='var(--border-2)'">
            <div class="flex items-start gap-3">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0
                            bg-emerald-500/10 border border-emerald-500/30">
                    <iconify-icon icon="mdi:motorcycle" class="text-emerald-400 text-xl"></iconify-icon>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5);">
                        Total Ongkir
                    </p>
                    <p class="text-xl font-bold mt-0.5" style="color: var(--text-1);">
                        Rp {{ number_format($totalShippingCost, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Total Stok Barang --}}
        <div class="rounded-2xl border p-4 transition-colors"
             style="background: var(--bg-card); border-color: var(--border-2);"
             onmouseover="this.style.borderColor='rgba(167,139,250,0.3)'"
             onmouseout="this.style.borderColor='var(--border-2)'">
            <div class="flex items-start gap-3">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0
                            bg-purple-500/10 border border-purple-500/30">
                    <iconify-icon icon="mdi:warehouse" class="text-purple-400 text-xl"></iconify-icon>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5);">
                        Total Stok Barang
                    </p>
                    <p class="text-xl font-bold mt-0.5" style="color: var(--text-1);">
                        Rp {{ number_format($totalStockValue, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Total Pelanggan --}}
        <div class="rounded-2xl border p-4 transition-colors"
             style="background: var(--bg-card); border-color: var(--border-2);"
             onmouseover="this.style.borderColor='rgba(96,165,250,0.3)'"
             onmouseout="this.style.borderColor='var(--border-2)'">
            <div class="flex items-start gap-3">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0
                            bg-blue-500/10 border border-blue-500/30">
                    <iconify-icon icon="mdi:account-multiple-outline" class="text-blue-400 text-xl"></iconify-icon>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5);">
                        Total Pelanggan
                    </p>
                    <p class="text-xl font-bold mt-0.5" style="color: var(--text-1);">
                        {{ number_format($totalCustomers) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- ADDITIONAL METRICS --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Total Order --}}
        <div class="rounded-2xl border p-4" style="background: var(--bg-card); border-color: var(--border-2);">
            <div class="flex items-start gap-3">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background: var(--bg-elevated);">
                    <iconify-icon icon="mdi:cart-outline" class="text-xl" style="color: #ecbc42"></iconify-icon>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5);">
                        Total Order
                    </p>
                    <p class="text-xl font-bold mt-0.5" style="color: var(--text-1);">
                        {{ number_format($totalOrderCount) }}
                    </p>
                    <p class="text-[10px] mt-0.5" style="color: var(--text-5)">
                        O: {{ $onlineOrderCount }} | Off: {{ $offlineOrderCount }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Rata-rata Order Value --}}
        <div class="rounded-2xl border p-4" style="background: var(--bg-card); border-color: var(--border-2);">
            <div class="flex items-start gap-3">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background: var(--bg-elevated);">
                    <iconify-icon icon="mdi:chart-bar" class="text-xl" style="color: #ecbc42"></iconify-icon>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5);">
                        Rata-rata Order Value
                    </p>
                    <p class="text-xl font-bold mt-0.5" style="color: var(--text-1);">
                        Rp {{ number_format($aov, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Produk Terjual --}}
        <div class="rounded-2xl border p-4" style="background: var(--bg-card); border-color: var(--border-2);">
            <div class="flex items-start gap-3">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background: var(--bg-elevated);">
                    <iconify-icon icon="mdi:fire" class="text-xl" style="color: #ecbc42"></iconify-icon>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5);">
                        Produk Terjual
                    </p>
                    <p class="text-xl font-bold mt-0.5" style="color: var(--text-1);">
                        {{ number_format($topProducts->sum('sold')) }} pcs
                    </p>
                    <p class="text-[10px] mt-0.5" style="color: var(--text-5)">
                        {{ $topProducts->unique('product_name')->count() }} produk unik
                    </p>
                </div>
            </div>
        </div>

        {{-- Biaya API Biteship --}}
        <div class="rounded-2xl border p-4 transition-colors"
             style="background: var(--bg-card); border-color: var(--border-2);"
             onmouseover="this.style.borderColor='rgba(245,158,66,0.3)'"
             onmouseout="this.style.borderColor='var(--border-2)'">
            <div class="flex items-start gap-3">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0
                            bg-amber-500/10 border border-amber-500/30">
                    <iconify-icon icon="carbon:api-1" class="text-amber-400 text-xl"></iconify-icon>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5);">
                        Biaya API Biteship
                    </p>
                    <p class="text-xl font-bold mt-0.5" style="color: var(--text-1);">
                        Rp {{ number_format($biteshipApiCost, 0, ',', '.') }}
                    </p>
                    <p class="text-[10px] mt-0.5" style="color: var(--text-5)">
                        {{ number_format($biteshipApiUsageCount) }} hit × Rp {{ number_format($biteshipApiCostPerHit, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- CHART + PAYMENT METHODS --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Grafik Penjualan --}}
        <div class="lg:col-span-2 rounded-2xl border overflow-hidden"
             style="background: var(--bg-card); border-color: var(--border-2);">

            <div class="px-5 py-4 border-b flex items-center justify-between"
                 style="background: var(--bg-input); border-color: var(--border-2);">
                <div class="flex items-center gap-2">
                    <iconify-icon icon="mdi:chart-line" class="text-[#ecbc42] text-base"></iconify-icon>
                    <h3 class="font-bold text-sm" style="color: var(--text-1);">
                        Penjualan Harian ({{ $start->format('d M') }} - {{ $end->format('d M') }})
                    </h3>
                </div>
                <span class="text-[10px]" style="color: var(--text-5);">
                    Online vs Offline vs Total
                </span>
            </div>

            <div class="p-5">
                <div class="relative" style="height: 260px;">
                    <canvas id="reportSalesChart" width="700" height="260"></canvas>
                </div>

                {{-- Summary --}}
                <div class="grid grid-cols-3 gap-3 mt-6 pt-4 border-t"
                     style="border-color: var(--border-1);">
                    <div class="text-center">
                        <p class="text-[10px] font-bold uppercase" style="color: var(--text-5);">Online</p>
                        <p class="text-lg font-bold mt-1" style="color: #34d399;">Rp {{ number_format($onlineRevenue, 0, ',', '.') }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-[10px] font-bold uppercase" style="color: var(--text-5);">Offline</p>
                        <p class="text-lg font-bold mt-1" style="color: #a78bfa;">Rp {{ number_format($offlineRevenue, 0, ',', '.') }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-[10px] font-bold uppercase" style="color: var(--text-5);">Total</p>
                        <p class="text-lg font-bold mt-1" style="color: #ecbc42;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Produk Terlaris (di kolom kanan) --}}
        <div class="rounded-2xl border overflow-hidden flex flex-col"
            style="background: var(--bg-card); border-color: var(--border-2);">

            {{-- Header --}}
            <div class="px-5 py-4 border-b flex items-center justify-between"
                style="background: var(--bg-input); border-color: var(--border-2);">
                <div class="flex items-center gap-2">
                    <iconify-icon icon="mdi:fire" class="text-[#ecbc42] text-base"></iconify-icon>
                    <h3 class="font-bold text-sm" style="color: var(--text-1);">
                        Produk Terlaris
                    </h3>
                </div>
                <a href="{{ route('admin.reports.products') }}?month={{ $monthParam }}"
                class="text-[10px] font-bold px-2 py-1 rounded-lg transition-all"
                style="background: var(--bg-card); border: 1px solid var(--border-2); color: var(--text-4);"
                onmouseover="this.style.borderColor='var(--gold-bright)'; this.style.color='var(--gold-dark)'"
                onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-4)'">
                    Detail
                </a>
            </div>

            {{-- Product List --}}
            <div class="flex-1 divide-y" style="divide-color: var(--border-1);">
                @forelse ($topProducts as $product)
                    <div class="px-4 py-3 flex items-center gap-3 hover:bg-[rgba(236,188,66,0.03)] transition-colors">

                        {{-- Rank Badge --}}
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0
                                    {{ $loop->iteration === 1
                                        ? 'bg-gradient-to-br from-[#FDDD57] to-[#ecbc42] shadow-sm shadow-amber-500/20'
                                        : '' }}"
                            style="{{ $loop->iteration !== 1 ? 'background: var(--bg-elevated);' : '' }}">
                            <span class="text-[11px] font-bold {{ $loop->iteration === 1 ? 'text-slate-900' : '' }}"
                                style="{{ $loop->iteration !== 1 ? 'color: #ecbc42;' : '' }}">
                                {{ $loop->iteration }}
                            </span>
                        </div>

                        {{-- Product Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold truncate" style="color: var(--text-1);"
                            title="{{ $product->product_name ?? '-' }}">
                                {{ $product->product_name ?? '-' }}
                            </p>
                            @if($product->variant ?? '')
                                <p class="text-[10px] truncate" style="color: var(--text-5);"
                                title="{{ $product->variant }}">
                                    {{ $product->variant }}
                                </p>
                            @endif
                        </div>

                        {{-- Stats --}}
                        <div class="text-right flex-shrink-0">
                            <p class="text-xs font-bold" style="color: #27c98d;">
                                {{ number_format($product->sold ?? 0) }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-8 text-center">
                        <iconify-icon icon="mdi:cart-off" class="text-3xl mb-2" style="color: var(--text-6);"></iconify-icon>
                        <p class="text-[11px]" style="color: var(--text-5);">
                            Belum ada data penjualan.
                        </p>
                    </div>
                @endforelse
            </div>

            {{-- Footer Summary --}}
            @if($topProducts->isNotEmpty())
                <div class="px-4 py-3 border-t flex items-center justify-between"
                    style="background: var(--bg-input); border-color: var(--border-2);">
                    <span class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5);">
                        Total Top 5
                    </span>
                    <div class="text-right">
                        <span class="text-xs font-bold" style="color: var(--text-1);">
                            {{ number_format($topProducts->sum('sold')) }} pcs
                        </span>
                    </div>
                </div>
            @endif
        </div>

    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('reportSalesChart').getContext('2d');

        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartLabels),
                datasets: [
                    {
                        label: 'Online',
                        data: @json($chartOnline),
                        backgroundColor: 'rgba(52, 211, 153, 0.25)',
                        borderColor: '#34d399',
                        borderWidth: 1,
                        borderRadius: 3,
                        barThickness: 8,
                    },
                    {
                        label: 'Offline',
                        data: @json($chartOffline),
                        backgroundColor: 'rgba(167, 139, 250, 0.25)',
                        borderColor: '#a78bfa',
                        borderWidth: 1,
                        borderRadius: 3,
                        barThickness: 8,
                    },
                    {
                        label: 'Total',
                        data: @json($chartCombined),
                        type: 'line',
                        borderColor: '#ecbc42',
                        borderWidth: 2.5,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: false,
                        tension: 0.3,
                        yAxisID: 'y',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: { size: 10 },
                            padding: 10,
                            usePointStyle: true,
                            color: getComputedStyle(document.documentElement).getPropertyValue('--text-4'),
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(20, 20, 20, 0.95)',
                        titleFont: { size: 10 },
                        bodyFont: { size: 10 },
                        padding: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 9 },
                            color: getComputedStyle(document.documentElement).getPropertyValue('--text-5'),
                        }
                    },
                    y: {
                        beginAtZero: true,
                        position: 'left',
                        grid: {
                            color: 'rgba(255,255,255,0.03)',
                        },
                        ticks: {
                            font: { size: 9 },
                            color: getComputedStyle(document.documentElement).getPropertyValue('--text-5'),
                            callback: function(value) {
                                if (value >= 1000000) return 'Rp ' + Math.round(value / 1000000) + 'M';
                                if (value >= 1000) return 'Rp ' + Math.round(value / 1000) + 'R';
                                return 'Rp ' + value;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush

@endsection
