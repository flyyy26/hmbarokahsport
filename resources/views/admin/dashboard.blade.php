@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('content')

<div class="w-full space-y-6">

    {{-- ============================================ --}}
    {{-- TOP BAR: Periode & Export --}}
    {{-- ============================================ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold flex items-center gap-2" style="color: var(--text-1)">
                Dashboard
            </h1>
            <p class="text-sm mt-0.5" style="color: var(--text-5)">
                Ringkasan performa toko online Anda
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            {{-- 🔥 Filter Bulan (hanya untuk 4 card stats) --}}
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-bold uppercase tracking-wider hidden sm:inline"
                    style="color: var(--text-5);">
                    Filter Stats:
                </span>
                <form method="GET" action="{{ route('admin.dashboard') }}" class="relative">
                    <select name="month"
                            onchange="this.form.submit()"
                            class="form-input text-xs font-semibold py-2 pl-9 pr-8 cursor-pointer"
                            style="min-width: 200px;">
                        @foreach($monthOptions as $opt)
                            <option value="{{ $opt['value'] }}"
                                    {{ ($stats['month_param'] ?? '') === $opt['value'] ? 'selected' : '' }}>
                                {{ $opt['label'] }}
                            </option>
                        @endforeach
                    </select>
                    <iconify-icon icon="mdi:calendar-outline"
                                class="absolute left-3 top-1/2 -translate-y-1/2 text-base pointer-events-none"
                                style="color: var(--text-5);"></iconify-icon>
                </form>
            </div>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- STATS ROW 1: 3 Kartu Grafik --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Card 1: 7 Hari --}}
        <div class="rounded-2xl border overflow-hidden relative"
             style="background: linear-gradient(135deg, rgba(167, 139, 250, 0.08) 0%, var(--bg-card) 60%);
                    border-color: rgba(167, 139, 250, 0.25);">
            <div class="p-5">
                <p class="text-[10px] font-bold uppercase tracking-widest" style="color: #a78bfa;">
                    7 Hari Terakhir
                </p>
                <div class="flex items-baseline gap-2 mt-2">
                    <p class="text-2xl font-bold" style="color: var(--text-1);">
                        Rp {{ number_format($stats['sales_7d'] ?? 0, 0, ',', '.') }}
                    </p>
                    <span class="inline-flex items-center gap-0.5 text-[11px] font-bold {{ ($stats['change_7d'] ?? 0) >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                        @if(($stats['change_7d'] ?? 0) >= 0)
                            <iconify-icon icon="mdi:arrow-up"></iconify-icon>
                        @else
                            <iconify-icon icon="mdi:arrow-down"></iconify-icon>
                        @endif
                        {{ abs($stats['change_7d'] ?? 0) }}%
                    </span>
                </div>
                <p class="text-[10px] mt-1" style="color: var(--text-5);">
                    vs 7 hari sebelumnya
                </p>
            </div>

            {{-- Sparkline SVG --}}
            <div class="h-16 relative">
                <svg viewBox="0 0 300 60" preserveAspectRatio="none" class="w-full h-full">
                    <defs>
                        <linearGradient id="spark1" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#a78bfa" stop-opacity="0.4"/>
                            <stop offset="100%" stop-color="#a78bfa" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
                    @if($stats['sparkline_7d'] ?? '')
                    <path d="{{ $stats['sparkline_7d'] }} L300,60 L0,60 Z"
                          fill="url(#spark1)"/>
                    <path d="{{ $stats['sparkline_7d'] }}"
                          fill="none" stroke="#a78bfa" stroke-width="2" stroke-linecap="round"/>
                    @endif
                </svg>
            </div>
            <div class="px-5 pb-3 flex justify-between text-[9px]" style="color: var(--text-5);">
                <span>{{ $stats['chart_labels'][0] ?? 'N/A' }}</span>
                <span>{{ $stats['chart_labels'][6] ?? 'N/A' }}</span>
            </div>
        </div>


        {{-- Card 2: 30 Hari --}}
        <div class="rounded-2xl border overflow-hidden relative"
             style="background: linear-gradient(135deg, rgba(96, 165, 250, 0.08) 0%, var(--bg-card) 60%);
                    border-color: rgba(96, 165, 250, 0.25);">
            <div class="p-5">
                <p class="text-[10px] font-bold uppercase tracking-widest" style="color: #60a5fa;">
                    30 Hari Terakhir
                </p>
                <div class="flex items-baseline gap-2 mt-2">
                    <p class="text-2xl font-bold" style="color: var(--text-1);">
                        Rp {{ number_format($stats['sales_30d'] ?? 0, 0, ',', '.') }}
                    </p>
                    <span class="inline-flex items-center gap-0.5 text-[11px] font-bold {{ ($stats['change_30d'] ?? 0) >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                        @if(($stats['change_30d'] ?? 0) >= 0)
                            <iconify-icon icon="mdi:arrow-up"></iconify-icon>
                        @else
                            <iconify-icon icon="mdi:arrow-down"></iconify-icon>
                        @endif
                        {{ abs($stats['change_30d'] ?? 0) }}%
                    </span>
                </div>
                <p class="text-[10px] mt-1" style="color: var(--text-5);">
                    vs 30 hari sebelumnya
                </p>
            </div>

            <div class="h-16 relative">
                <svg viewBox="0 0 300 60" preserveAspectRatio="none" class="w-full h-full">
                    <defs>
                        <linearGradient id="spark2" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#60a5fa" stop-opacity="0.4"/>
                            <stop offset="100%" stop-color="#60a5fa" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
                    @if($stats['sparkline_30d'] ?? '')
                    <path d="{{ $stats['sparkline_30d'] }} L300,60 L0,60 Z"
                          fill="url(#spark2)"/>
                    <path d="{{ $stats['sparkline_30d'] }}"
                          fill="none" stroke="#60a5fa" stroke-width="2" stroke-linecap="round"/>
                    @endif
                </svg>
            </div>
            <div class="px-5 pb-3 flex justify-between text-[9px]" style="color: var(--text-5);">
                @php
                    $days30_ago = ($stats['chart_labels'] ? $stats['chart_labels'][0] : 'N/A');
                    // For 30-day period, show month-level labels
                @endphp
                <span>{{ now()->subDays(29)->format('d M') }}</span>
                <span>{{ now()->format('d M') }}</span>
            </div>
        </div>


        {{-- Card 3: 90 Hari --}}
        <div class="rounded-2xl border overflow-hidden relative"
             style="background: linear-gradient(135deg, rgba(52, 211, 153, 0.08) 0%, var(--bg-card) 60%);
                    border-color: rgba(52, 211, 153, 0.25);">
            <div class="p-5">
                <p class="text-[10px] font-bold uppercase tracking-widest" style="color: #34d399;">
                    90 Hari Terakhir
                </p>
                <div class="flex items-baseline gap-2 mt-2">
                    <p class="text-2xl font-bold" style="color: var(--text-1);">
                        Rp {{ number_format($stats['sales_90d'] ?? 0, 0, ',', '.') }}
                    </p>
                    <span class="inline-flex items-center gap-0.5 text-[11px] font-bold {{ ($stats['change_90d'] ?? 0) >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                        @if(($stats['change_90d'] ?? 0) >= 0)
                            <iconify-icon icon="mdi:arrow-up"></iconify-icon>
                        @else
                            <iconify-icon icon="mdi:arrow-down"></iconify-icon>
                        @endif
                        {{ abs($stats['change_90d'] ?? 0) }}%
                    </span>
                </div>
                <p class="text-[10px] mt-1" style="color: var(--text-5);">
                    vs 90 hari sebelumnya
                </p>
            </div>

            <div class="h-16 relative">
                <svg viewBox="0 0 300 60" preserveAspectRatio="none" class="w-full h-full">
                    <defs>
                        <linearGradient id="spark3" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#34d399" stop-opacity="0.4"/>
                            <stop offset="100%" stop-color="#34d399" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
                    @if($stats['sparkline_90d'] ?? '')
                    <path d="{{ $stats['sparkline_90d'] }} L300,60 L0,60 Z"
                          fill="url(#spark3)"/>
                    <path d="{{ $stats['sparkline_90d'] }}"
                          fill="none" stroke="#34d399" stroke-width="2" stroke-linecap="round"/>
                    @endif
                </svg>
            </div>
            <div class="px-5 pb-3 flex justify-between text-[9px]" style="color: var(--text-5);">
                <span>{{ now()->subDays(89)->format('d M') }}</span>
                <span>{{ now()->format('d M') }}</span>
            </div>
        </div>
    </div>

    <div>
        {{-- Label periode --}}
        <div class="flex items-center gap-2 mb-3">
            <iconify-icon icon="mdi:calendar-month-outline" class="text-[#ecbc42] text-base"></iconify-icon>
            <span class="text-xs font-bold uppercase tracking-wider" style="color: var(--text-4);">
                Periode: <span style="color: #ecbc42;">{{ $stats['month_label'] }}</span>
            </span>
            <div class="flex-1 h-px" style="background: var(--border-1);"></div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- Card: Total Penjualan Online --}}
            <div class="rounded-2xl border p-4 transition-colors"
                style="background: var(--bg-card); border-color: var(--border-2);"
                onmouseover="this.style.borderColor='rgba(52,211,153,0.3)'"
                onmouseout="this.style.borderColor='var(--border-2)'">
                <div class="flex items-start gap-3">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0
                                bg-emerald-500/10 border border-emerald-500/30">
                        <iconify-icon icon="mdi:wallet-outline" class="text-emerald-400 text-xl"></iconify-icon>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5);">
                            Total Penjualan Online
                        </p>
                        <p class="text-lg font-bold mt-0.5" style="color: var(--text-1);">
                            Rp {{ number_format($stats['cash_in'] ?? 0, 0, ',', '.') }}
                        </p>
                        <div class="flex items-center gap-1.5 mt-1.5">
                            <span class="inline-flex items-center gap-0.5 text-[10px] font-bold {{ ($stats['change_cash_in'] ?? 0) >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                                @if(($stats['change_cash_in'] ?? 0) >= 0)
                                    <iconify-icon icon="mdi:arrow-up"></iconify-icon>
                                @else
                                    <iconify-icon icon="mdi:arrow-down"></iconify-icon>
                                @endif
                                {{ abs($stats['change_cash_in'] ?? 0) }}%
                            </span>
                            <span class="text-[9px]" style="color: var(--text-5);">vs bulan lalu</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card: Total Penjualan Offline --}}
            <div class="rounded-2xl border p-4 transition-colors"
                style="background: var(--bg-card); border-color: var(--border-2);"
                onmouseover="this.style.borderColor='rgba(167,139,250,0.3)'"
                onmouseout="this.style.borderColor='var(--border-2)'">
                <div class="flex items-start gap-3">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0
                                bg-purple-500/10 border border-purple-500/30">
                        <iconify-icon icon="mdi:storefront-outline" class="text-purple-400 text-xl"></iconify-icon>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5);">
                            Total Penjualan Offline
                        </p>
                        <p class="text-lg font-bold mt-0.5" style="color: var(--text-1);">
                            Rp {{ number_format($stats['sales_offline'] ?? 0, 0, ',', '.') }}
                        </p>
                        <div class="flex items-center gap-1.5 mt-1.5">
                            <span class="inline-flex items-center gap-0.5 text-[10px] font-bold {{ ($stats['change_offline'] ?? 0) >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                                @if(($stats['change_offline'] ?? 0) >= 0)
                                    <iconify-icon icon="mdi:arrow-up"></iconify-icon>
                                @else
                                    <iconify-icon icon="mdi:arrow-down"></iconify-icon>
                                @endif
                                {{ abs($stats['change_offline'] ?? 0) }}%
                            </span>
                            <span class="text-[9px]" style="color: var(--text-5);">vs bulan lalu</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card: Total Order --}}
            <div class="rounded-2xl border p-4 transition-colors"
                style="background: var(--bg-card); border-color: var(--border-2);"
                onmouseover="this.style.borderColor='rgba(236,188,66,0.3)'"
                onmouseout="this.style.borderColor='var(--border-2)'">
                <div class="flex items-start gap-3">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0
                                bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                                shadow-md shadow-amber-500/20">
                        <iconify-icon icon="mdi:cart-outline" class="text-slate-900 text-xl"></iconify-icon>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5);">
                            Total Order
                        </p>
                        <p class="text-lg font-bold mt-0.5" style="color: var(--text-1);">
                            {{ number_format($stats['total_orders'] ?? 0, 0, ',', '.') }}
                        </p>
                        <div class="flex items-center gap-1.5 mt-1.5">
                            <span class="inline-flex items-center gap-0.5 text-[10px] font-bold {{ ($stats['change_orders'] ?? 0) >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                                @if(($stats['change_orders'] ?? 0) >= 0)
                                    <iconify-icon icon="mdi:arrow-up"></iconify-icon>
                                @else
                                    <iconify-icon icon="mdi:arrow-down"></iconify-icon>
                                @endif
                                {{ abs($stats['change_orders'] ?? 0) }}%
                            </span>
                            <span class="text-[9px]" style="color: var(--text-5);">vs bulan lalu</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card: Rata-rata Order Value --}}
            <div class="rounded-2xl border p-4 transition-colors"
                style="background: var(--bg-card); border-color: var(--border-2);"
                onmouseover="this.style.borderColor='rgba(96,165,250,0.3)'"
                onmouseout="this.style.borderColor='var(--border-2)'">
                <div class="flex items-start gap-3">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0
                                bg-blue-500/10 border border-blue-500/30">
                        <iconify-icon icon="mdi:chart-bar" class="text-blue-400 text-xl"></iconify-icon>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5);">
                            Rata-rata Order Value
                        </p>
                        <p class="text-lg font-bold mt-0.5" style="color: var(--text-1);">
                            Rp {{ number_format($stats['aov'] ?? 0, 0, ',', '.') }}
                        </p>
                        <div class="flex items-center gap-1.5 mt-1.5">
                            <span class="inline-flex items-center gap-0.5 text-[10px] font-bold {{ ($stats['change_aov'] ?? 0) >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                                @if(($stats['change_aov'] ?? 0) >= 0)
                                    <iconify-icon icon="mdi:arrow-up"></iconify-icon>
                                @else
                                    <iconify-icon icon="mdi:arrow-down"></iconify-icon>
                                @endif
                                {{ abs($stats['change_aov'] ?? 0) }}%
                            </span>
                            <span class="text-[9px]" style="color: var(--text-5);">vs bulan lalu</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- ROW 3: Grafik Penjualan + Produk Terlaris --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Grafik Penjualan --}}
        <div class="lg:col-span-2 rounded-2xl border overflow-hidden"
            style="background: var(--bg-card); border-color: var(--border-2);">

            <div class="px-5 py-4 border-b flex flex-wrap items-center gap-3"
                style="background: var(--bg-input); border-color: var(--border-2);">
                <iconify-icon icon="mdi:chart-line" class="text-[#ecbc42] text-base"></iconify-icon>
                <h3 class="font-bold text-sm flex-1" style="color: var(--text-1);">
                    Grafik Penjualan
                </h3>

                {{-- 🔥 Filter Periode --}}
                <select id="chartPeriodFilter"
                        class="text-[10px] font-semibold rounded-lg px-3 py-1.5 cursor-pointer
                            focus:outline-none transition-all"
                        style="background: var(--bg-card);
                            border: 1px solid var(--border-2);
                            color: var(--text-3);">
                    <option value="7" selected>7 Hari Terakhir</option>
                    <option value="30">30 Hari Terakhir</option>
                    <option value="90">90 Hari Terakhir</option>
                </select>
            </div>

            <div class="p-5">
                {{-- Legend --}}
                <div class="flex items-center gap-4 mb-4 text-[11px]">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-0.5 rounded" style="background: #60a5fa;"></span>
                        <span style="color: var(--text-4);">Penjualan (Rp)</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-0.5 rounded" style="background: #34d399;"></span>
                        <span style="color: var(--text-4);">Order</span>
                    </div>

                    {{-- Loading indicator --}}
                    <div id="chartLoading" class="ml-auto hidden">
                        <div class="flex items-center gap-1.5 text-[10px]" style="color: var(--text-5);">
                            <div class="w-3 h-3 rounded-full border-2 border-t-transparent animate-spin"
                                style="border-color: #ecbc42; border-top-color: transparent;"></div>
                            <span>Memuat...</span>
                        </div>
                    </div>
                </div>

                {{-- Chart Area --}}
                <div class="relative" style="height: 260px;">
                    {{-- Y-axis Left --}}
                    <div id="chartYAxisLeft"
                        class="absolute left-0 top-0 flex flex-col justify-between text-[9px] pr-2 text-right"
                        style="width: 48px; height: calc(100% - 24px); color: var(--text-5);">
                        {{-- diisi JS --}}
                    </div>

                    {{-- Y-axis Right --}}
                    <div id="chartYAxisRight"
                        class="absolute right-0 top-0 flex flex-col justify-between text-[9px] pl-2 text-left"
                        style="width: 40px; height: calc(100% - 24px); color: var(--text-5);">
                        {{-- diisi JS --}}
                    </div>

                    {{-- Chart Container --}}
                    <div class="absolute left-12 right-10 top-0"
                        style="bottom: 24px;"
                        id="chartContainer">
                        <svg id="salesChart" viewBox="0 0 700 220"
                            preserveAspectRatio="none" class="w-full h-full">
                            {{-- Grid lines --}}
                            <g id="chartGrid">
                                @for ($i = 0; $i <= 5; $i++)
                                    <line x1="0" y1="{{ $i * 44 }}" x2="700" y2="{{ $i * 44 }}"
                                        stroke="var(--border-2)" stroke-width="0.5" stroke-dasharray="3,3" opacity="0.5"/>
                                @endfor
                            </g>

                            {{-- Sales path (blue) --}}
                            <path id="chartSalesPath" d=""
                                fill="none" stroke="#60a5fa" stroke-width="2.5"
                                stroke-linecap="round" stroke-linejoin="round"/>
                            <g id="chartSalesDots"></g>

                            {{-- Order path (green) --}}
                            <path id="chartOrderPath" d=""
                                fill="none" stroke="#34d399" stroke-width="2.5"
                                stroke-linecap="round" stroke-linejoin="round"/>
                            <g id="chartOrderDots"></g>
                        </svg>

                        {{-- X-axis Labels --}}
                        <div id="chartXAxis"
                            class="absolute -bottom-5 left-0 right-0 flex justify-between text-[9px]"
                            style="color: var(--text-5);">
                            {{-- diisi JS --}}
                        </div>
                    </div>
                </div>

                {{-- Summary Stats --}}
                <div class="grid grid-cols-2 gap-3 mt-6 pt-4 border-t"
                    style="border-color: var(--border-1);">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider mb-0.5"
                        style="color: var(--text-5);">
                            Total Penjualan
                        </p>
                        <p id="chartTotalSales" class="text-sm font-bold font-mono"
                        style="color: #ecbc42;">
                            Rp 0
                        </p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider mb-0.5"
                        style="color: var(--text-5);">
                            Total Order
                        </p>
                        <p id="chartTotalOrders" class="text-sm font-bold font-mono"
                        style="color: #34d399;">
                            0
                        </p>
                    </div>
                </div>
            </div>
        </div>



        {{-- Produk Terlaris --}}
        <div class="rounded-2xl border overflow-hidden"
             style="background: var(--bg-card); border-color: var(--border-2);">

            <div class="px-5 py-4 border-b flex items-center gap-2"
                 style="background: var(--bg-input); border-color: var(--border-2);">
                <iconify-icon icon="mdi:fire" class="text-[#ecbc42] text-base"></iconify-icon>
                <h3 class="font-bold text-sm flex-1" style="color: var(--text-1);">
                    Produk Terlaris
                </h3>
                <a href="{{ route('admin.products.index') }}"
                   class="text-[10px] font-bold px-2 py-1 rounded-lg transition-all"
                   style="background: var(--bg-card); border: 1px solid var(--border-2); color: var(--text-4);"
                   onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                   onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-4)'">
                    Lihat Semua
                </a>
            </div>

            <div class="overflow-hidden">
                <table class="min-w-full">
                    <thead class="border-b" style="border-color: var(--border-1);">
                        <tr>
                            <th class="px-3 py-2 text-left text-[9px] font-bold uppercase tracking-wider" style="color: var(--text-5);">
                                Produk
                            </th>
                            <th class="px-3 py-2 text-center text-[9px] font-bold uppercase tracking-wider" style="color: var(--text-5);">
                                Terjual
                            </th>
                            <th class="px-3 py-2 text-right text-[9px] font-bold uppercase tracking-wider" style="color: var(--text-5);">
                                Penjualan
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($topProducts ?? [] as $product)
                            <tr class="border-b last:border-0 transition-colors"
                                style="border-color: var(--border-1);"
                                onmouseover="this.style.background='var(--bg-hover)'"
                                onmouseout="this.style.background='transparent'">

                                {{-- Rank & Product --}}
                                <td class="px-3 py-3">
                                    <div class="flex items-center gap-2.5">
                                        <span class="text-xs font-bold w-4 flex-shrink-0" style="color: var(--text-4);">
                                            {{ $loop->iteration }}
                                        </span>
                                        <div class="min-w-0">
                                            <p class="text-[11px] font-semibold truncate max-w-[200px]" style="color: var(--text-1);">
                                                {{ $product->product_name ?? 'Produk' }}
                                            </p>
                                            @if($product->variant ?? '')
                                                <p class="text-[9px] truncate" style="color: var(--text-5);">
                                                    {{ $product->variant }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Sold --}}
                                <td class="px-3 py-3 text-center">
                                    <span class="text-xs font-bold" style="color: var(--text-2);">
                                        {{ $product->sold ?? 0 }}
                                    </span>
                                </td>

                                {{-- Revenue --}}
                                <td class="px-3 py-3 text-right">
                                    <span class="text-xs font-bold" style="color: #ecbc42;">
                                        Rp {{ number_format($product->revenue ?? 0, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-3 py-8 text-center text-[11px]" style="color: var(--text-5);">
                                    Belum ada data penjualan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>



    {{-- ============================================ --}}
    {{-- ROW 4: 3 Kartu Donut Charts --}}
    {{-- ============================================ --}}
    


    {{-- ============================================ --}}
    {{-- FOOTER INFO --}}
    {{-- ============================================ --}}
    <div class="flex items-start gap-2.5 p-4 rounded-xl border"
         style="background: var(--bg-input); border-color: var(--border-2);">
        <iconify-icon icon="mdi:information-outline" class="text-base flex-shrink-0 mt-0.5"
                      style="color: var(--text-5);"></iconify-icon>
        <p class="text-[11px]" style="color: var(--text-5);">
            Semua data diperbarui secara real-time. Pastikan data sudah sesuai dengan periode yang dipilih.
        </p>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ============================================
    // STATE & ELEMENTS
    // ============================================
    const chartPeriodFilter = document.getElementById('chartPeriodFilter');
    const chartLoading = document.getElementById('chartLoading');
    const salesPath = document.getElementById('chartSalesPath');
    const orderPath = document.getElementById('chartOrderPath');
    const salesDots = document.getElementById('chartSalesDots');
    const orderDots = document.getElementById('chartOrderDots');
    const xAxis = document.getElementById('chartXAxis');
    const yAxisLeft = document.getElementById('chartYAxisLeft');
    const yAxisRight = document.getElementById('chartYAxisRight');
    const totalSalesEl = document.getElementById('chartTotalSales');
    const totalOrdersEl = document.getElementById('chartTotalOrders');

    const CHART_WIDTH = 700;
    const CHART_HEIGHT = 214;
    const CHART_TOP = 6;

    // ============================================
    // FORMAT HELPERS
    // ============================================
    function formatRupiah(num) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(num || 0));
    }

    function formatShort(num) {
        if (num >= 1_000_000_000) return (num / 1_000_000_000).toFixed(1) + ' M';
        if (num >= 1_000_000)     return (num / 1_000_000).toFixed(1) + ' Jt';
        if (num >= 1_000)         return (num / 1_000).toFixed(0) + ' Rb';
        return Math.round(num);
    }

    // ============================================
    // RENDER CHART
    // ============================================
    function renderChart(labels, salesData, ordersData) {
        const pointCount = salesData.length;
        if (pointCount === 0) return;

        // ============================================
        // 🔥 FIX: Bulatkan max agar Y-axis cantik
        // ============================================
        const niceMax = (val) => {
            if (val <= 0) return 1;
            
            // Cari magnitude (10, 100, 1000, 10000, ...)
            const magnitude = Math.pow(10, Math.floor(Math.log(val) / Math.LN10));
            const normalized = val / magnitude;
            
            // Pilih "nice" multiplier
            let niceMultiplier;
            if (normalized <= 1)        niceMultiplier = 1;
            else if (normalized <= 1.5) niceMultiplier = 1.5;
            else if (normalized <= 2)   niceMultiplier = 2;
            else if (normalized <= 2.5) niceMultiplier = 2.5;
            else if (normalized <= 3)   niceMultiplier = 3;
            else if (normalized <= 4)   niceMultiplier = 4;
            else if (normalized <= 5)   niceMultiplier = 5;
            else if (normalized <= 6)   niceMultiplier = 6;
            else if (normalized <= 8)   niceMultiplier = 8;
            else                        niceMultiplier = 10;
            
            return niceMultiplier * magnitude;
        };

        // Kalkulasi max data
        const maxSales = Math.max(...salesData, 1);
        const maxOrders = Math.max(...ordersData, 1);

        // 🔥 Y-axis max dengan padding 10%
        const niceMaxSales = niceMax(maxSales * 1.1);
        const niceMaxOrders = niceMax(maxOrders * 1.1);
        
        // 🔥 Debug log (hapus setelah fix)
        console.log('📊 Chart Scale:', {
            maxSales: maxSales,
            niceMaxSales: niceMaxSales,
            maxOrders: maxOrders,
            niceMaxOrders: niceMaxOrders,
        });

        // X positions
        const xStep = pointCount > 1 ? CHART_WIDTH / (pointCount - 1) : 0;

        // Build paths
        let sPath = '';
        let oPath = '';
        let sDots = '';
        let oDots = '';

        for (let i = 0; i < pointCount; i++) {
            const x = xStep * i;
            const yS = CHART_TOP + CHART_HEIGHT - (salesData[i] / niceMaxSales) * CHART_HEIGHT;
            const yO = CHART_TOP + CHART_HEIGHT - (ordersData[i] / niceMaxOrders) * CHART_HEIGHT;

            // Sales path
            sPath += (i === 0 ? 'M' : ' L') + x.toFixed(1) + ',' + yS.toFixed(1);
            sDots += `<circle cx="${x.toFixed(1)}" cy="${yS.toFixed(1)}" r="4" fill="#60a5fa" stroke="var(--bg-card)" stroke-width="2"/>`;

            // Order path
            oPath += (i === 0 ? 'M' : ' L') + x.toFixed(1) + ',' + yO.toFixed(1);
            oDots += `<circle cx="${x.toFixed(1)}" cy="${yO.toFixed(1)}" r="4" fill="#34d399" stroke="var(--bg-card)" stroke-width="2"/>`;
        }

        salesPath.setAttribute('d', sPath);
        orderPath.setAttribute('d', oPath);
        salesDots.innerHTML = sDots;
        orderDots.innerHTML = oDots;

        // 🔥 Y-axis LEFT (Sales)
        let yLeftHtml = '';
        for (let i = 5; i >= 0; i--) {
            const val = (niceMaxSales / 5) * i;
            yLeftHtml += `<span>${formatShort(val)}</span>`;
        }
        yAxisLeft.innerHTML = yLeftHtml;

        // 🔥 Y-axis RIGHT (Orders)
        let yRightHtml = '';
        for (let i = 5; i >= 0; i--) {
            const val = Math.round((niceMaxOrders / 5) * i);
            yRightHtml += `<span>${val}</span>`;
        }
        yAxisRight.innerHTML = yRightHtml;

        // 🔥 X-axis Labels
        let xHtml = '';
        const maxLabels = 10;
        const step = pointCount > maxLabels ? Math.ceil(pointCount / maxLabels) : 1;

        for (let i = 0; i < pointCount; i++) {
            if (i % step === 0 || i === pointCount - 1) {
                xHtml += `<span>${labels[i]}</span>`;
            } else {
                xHtml += `<span class="invisible">${labels[i]}</span>`;
            }
        }
        xAxis.innerHTML = xHtml;

        // 🔥 Update Totals
        const totalSales = salesData.reduce((a, b) => a + b, 0);
        const totalOrders = ordersData.reduce((a, b) => a + b, 0);

        totalSalesEl.textContent = formatRupiah(totalSales);
        totalOrdersEl.textContent = new Intl.NumberFormat('id-ID').format(totalOrders);
    }


    // ============================================
    // FETCH DATA VIA AJAX
    // ============================================
    function fetchChartData(period) {
        chartLoading.classList.remove('hidden');

        fetch('{{ route("admin.dashboard.chart-data") }}?period=' + period, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                renderChart(data.labels, data.sales, data.orders);
            } else {
                console.error('Failed to load chart data');
            }
        })
        .catch(err => {
            console.error('Chart fetch error:', err);
        })
        .finally(() => {
            chartLoading.classList.add('hidden');
        });
    }

    // ============================================
    // INIT — Render default 7 hari dari server
    // ============================================
    @php
        $initialLabels = $stats['chart_labels'] ?? [];
        $initialSales = $stats['chart_sales'] ?? [];
        $initialOrders = $stats['chart_orders'] ?? [];
    @endphp

    const initialLabels = @json($initialLabels);
    const initialSales = @json($initialSales);
    const initialOrders = @json($initialOrders);

    renderChart(initialLabels, initialSales, initialOrders);

    // ============================================
    // EVENT: Filter change
    // ============================================
    if (chartPeriodFilter) {
        chartPeriodFilter.addEventListener('change', function() {
            fetchChartData(this.value);
        });
    }
});
</script>
@endpush

@endsection