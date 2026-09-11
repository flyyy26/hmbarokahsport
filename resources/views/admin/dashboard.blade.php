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
            {{-- Periode Dropdown --}}
            <div class="relative">
                <select class="form-input text-xs font-semibold py-2 pl-9 pr-8 cursor-pointer"
                        style="min-width: 220px;">
                    <option>20 Mei 2026 - 27 Mei 2026</option>
                    <option>1 Mei 2026 - 31 Mei 2026</option>
                    <option>1 Jan 2026 - 31 Des 2026</option>
                </select>
                <iconify-icon icon="mdi:calendar-outline"
                              class="absolute left-3 top-1/2 -translate-y-1/2 text-base pointer-events-none"
                              style="color: var(--text-5);"></iconify-icon>
            </div>

            {{-- Export Button --}}
            <button type="button"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg
                           text-xs font-bold transition-all active:scale-95 border"
                    style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)"
                    onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                    onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'">
                <iconify-icon icon="mdi:download-outline"></iconify-icon>
                Export
            </button>
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
                        Rp {{ number_format($stats['sales_7d'] ?? 28_450_000, 0, ',', '.') }}
                    </p>
                    <span class="inline-flex items-center gap-0.5 text-[11px] font-bold text-emerald-400">
                        <iconify-icon icon="mdi:arrow-up"></iconify-icon>
                        18.6%
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
                    <path d="M0,45 C20,40 40,30 60,35 C80,40 100,25 120,20 C140,15 160,25 180,22 C200,19 220,30 240,25 C260,20 280,28 300,25 L300,60 L0,60 Z"
                          fill="url(#spark1)"/>
                    <path d="M0,45 C20,40 40,30 60,35 C80,40 100,25 120,20 C140,15 160,25 180,22 C200,19 220,30 240,25 C260,20 280,28 300,25"
                          fill="none" stroke="#a78bfa" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="px-5 pb-3 flex justify-between text-[9px]" style="color: var(--text-5);">
                <span>21 Mei</span>
                <span>27 Mei</span>
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
                        Rp {{ number_format($stats['sales_30d'] ?? 126_750_000, 0, ',', '.') }}
                    </p>
                    <span class="inline-flex items-center gap-0.5 text-[11px] font-bold text-emerald-400">
                        <iconify-icon icon="mdi:arrow-up"></iconify-icon>
                        22.4%
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
                    <path d="M0,50 C20,35 40,45 60,30 C80,35 100,20 120,25 C140,30 160,15 180,18 C200,21 220,10 240,15 C260,20 280,12 300,10 L300,60 L0,60 Z"
                          fill="url(#spark2)"/>
                    <path d="M0,50 C20,35 40,45 60,30 C80,35 100,20 120,25 C140,30 160,15 180,18 C200,21 220,10 240,15 C260,20 280,12 300,10"
                          fill="none" stroke="#60a5fa" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="px-5 pb-3 flex justify-between text-[9px]" style="color: var(--text-5);">
                <span>28 Apr</span>
                <span>27 Mei</span>
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
                        Rp {{ number_format($stats['sales_90d'] ?? 342_980_000, 0, ',', '.') }}
                    </p>
                    <span class="inline-flex items-center gap-0.5 text-[11px] font-bold text-emerald-400">
                        <iconify-icon icon="mdi:arrow-up"></iconify-icon>
                        15.7%
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
                    <path d="M0,45 C20,48 40,35 60,38 C80,41 100,25 120,28 C140,31 160,20 180,22 C200,24 220,15 240,18 C260,21 280,10 300,8 L300,60 L0,60 Z"
                          fill="url(#spark3)"/>
                    <path d="M0,45 C20,48 40,35 60,38 C80,41 100,25 120,28 C140,31 160,20 180,22 C200,24 220,15 240,18 C260,21 280,10 300,8"
                          fill="none" stroke="#34d399" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="px-5 pb-3 flex justify-between text-[9px]" style="color: var(--text-5);">
                <span>28 Feb</span>
                <span>27 Mei</span>
            </div>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- STATS ROW 2: 4 Metrik Tambahan --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Kas Masuk --}}
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
                        Kas Masuk
                    </p>
                    <p class="text-lg font-bold mt-0.5" style="color: var(--text-1);">
                        Rp {{ number_format($stats['cash_in'] ?? 315_200_000, 0, ',', '.') }}
                    </p>
                    <div class="flex items-center gap-1.5 mt-1.5">
                        <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-emerald-400">
                            <iconify-icon icon="mdi:arrow-up"></iconify-icon>
                            17.3%
                        </span>
                        <span class="text-[9px]" style="color: var(--text-5);">vs periode sebelumnya</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Pencapaian SLS --}}
        <div class="rounded-2xl border p-4 transition-colors"
             style="background: var(--bg-card); border-color: var(--border-2);"
             onmouseover="this.style.borderColor='rgba(167,139,250,0.3)'"
             onmouseout="this.style.borderColor='var(--border-2)'">
            <div class="flex items-start gap-3">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0
                            bg-purple-500/10 border border-purple-500/30">
                    <iconify-icon icon="mdi:trophy-outline" class="text-purple-400 text-xl"></iconify-icon>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5);">
                        Total Pencapaian SLS
                    </p>
                    <p class="text-lg font-bold mt-0.5" style="color: var(--text-1);">
                        Rp {{ number_format($stats['sls_total'] ?? 342_980_000, 0, ',', '.') }}
                    </p>
                    <div class="flex items-center gap-1.5 mt-1.5">
                        <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-emerald-400">
                            <iconify-icon icon="mdi:arrow-up"></iconify-icon>
                            15.7%
                        </span>
                        <span class="text-[9px]" style="color: var(--text-5);">vs periode sebelumnya</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Order --}}
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
                        {{ number_format($stats['total_orders'] ?? 1248, 0, ',', '.') }}
                    </p>
                    <div class="flex items-center gap-1.5 mt-1.5">
                        <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-emerald-400">
                            <iconify-icon icon="mdi:arrow-up"></iconify-icon>
                            21.4%
                        </span>
                        <span class="text-[9px]" style="color: var(--text-5);">vs periode sebelumnya</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rata-rata Order Value --}}
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
                        Rp {{ number_format($stats['aov'] ?? 274_827, 0, ',', '.') }}
                    </p>
                    <div class="flex items-center gap-1.5 mt-1.5">
                        <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-red-400">
                            <iconify-icon icon="mdi:arrow-down"></iconify-icon>
                            2.1%
                        </span>
                        <span class="text-[9px]" style="color: var(--text-5);">vs periode sebelumnya</span>
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

                <select class="text-[10px] font-semibold rounded-lg px-2 py-1 cursor-pointer"
                        style="background: var(--bg-card); border: 1px solid var(--border-2); color: var(--text-3);">
                    <option>7 Hari Terakhir</option>
                    <option>30 Hari Terakhir</option>
                    <option>90 Hari Terakhir</option>
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
                </div>

                {{-- Chart SVG --}}
                <div class="relative h-64">
                    {{-- Y-axis labels --}}
                    <div class="absolute left-0 top-0 bottom-6 flex flex-col justify-between text-[9px] pr-2 text-right"
                         style="width: 40px; color: var(--text-5);">
                        <span>50 Jt</span>
                        <span>40 Jt</span>
                        <span>30 Jt</span>
                        <span>20 Jt</span>
                        <span>10 Jt</span>
                        <span>0</span>
                    </div>

                    {{-- Y-axis right labels --}}
                    <div class="absolute right-0 top-0 bottom-6 flex flex-col justify-between text-[9px] pl-2 text-left"
                         style="width: 30px; color: var(--text-5);">
                        <span>100</span>
                        <span>80</span>
                        <span>60</span>
                        <span>40</span>
                        <span>20</span>
                        <span>0</span>
                    </div>

                    {{-- Chart Area --}}
                    <div class="absolute left-12 right-8 top-0 bottom-6">
                        <svg viewBox="0 0 700 220" preserveAspectRatio="none" class="w-full h-full">
                            {{-- Horizontal grid lines --}}
                            @for ($i = 0; $i <= 5; $i++)
                                <line x1="0" y1="{{ $i * 44 }}" x2="700" y2="{{ $i * 44 }}"
                                      stroke="currentColor" stroke-width="0.5" stroke-dasharray="3,3"
                                      style="color: var(--border-2); opacity: 0.5;"/>
                            @endfor

                            {{-- Sales line (blue) --}}
                            <path d="M0,180 L100,160 L200,90 L300,110 L400,80 L500,120 L600,60 L700,40"
                                  fill="none" stroke="#60a5fa" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            @foreach ([0,100,200,300,400,500,600,700] as $i => $x)
                                @php $y = [180,160,90,110,80,120,60,40][$i]; @endphp
                                <circle cx="{{ $x }}" cy="{{ $y }}" r="4" fill="#60a5fa" stroke="#0a0a0a" stroke-width="2"/>
                            @endforeach

                            {{-- Order line (green) --}}
                            <path d="M0,200 L100,185 L200,145 L300,160 L400,130 L500,165 L600,110 L700,90"
                                  fill="none" stroke="#34d399" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            @foreach ([0,100,200,300,400,500,600,700] as $i => $x)
                                @php $y = [200,185,145,160,130,165,110,90][$i]; @endphp
                                <circle cx="{{ $x }}" cy="{{ $y }}" r="4" fill="#34d399" stroke="#0a0a0a" stroke-width="2"/>
                            @endforeach
                        </svg>

                        {{-- X-axis labels --}}
                        <div class="absolute -bottom-5 left-0 right-0 flex justify-between text-[9px]"
                             style="color: var(--text-5);">
                            <span>21 Mei</span>
                            <span>22 Mei</span>
                            <span>23 Mei</span>
                            <span>24 Mei</span>
                            <span>25 Mei</span>
                            <span>26 Mei</span>
                            <span>27 Mei</span>
                        </div>
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
                        @php
                            $topProducts = $topProducts ?? [
                                ['rank' => 1, 'name' => 'Jaket Training Premium', 'variant' => 'Hitam, M-XL',   'sold' => 256, 'revenue' => 76_800_000],
                                ['rank' => 2, 'name' => 'Celana Training Sport',  'variant' => 'Hitam, M-XXL',  'sold' => 198, 'revenue' => 49_500_000],
                                ['rank' => 3, 'name' => 'Setelan Olahraga Unisex', 'variant' => 'Navy, M-XL',   'sold' => 162, 'revenue' => 48_600_000],
                                ['rank' => 4, 'name' => 'Jaket Windbreaker',      'variant' => 'Grey, M-XXL',   'sold' => 134, 'revenue' => 33_500_000],
                                ['rank' => 5, 'name' => 'Kaos Olahraga Dry Fit',  'variant' => 'Hitam, M-XL',   'sold' => 98,  'revenue' => 19_600_000],
                            ];
                        @endphp

                        @foreach ($topProducts as $product)
                            <tr class="border-b last:border-0 transition-colors"
                                style="border-color: var(--border-1);"
                                onmouseover="this.style.background='var(--bg-hover)'"
                                onmouseout="this.style.background='transparent'">

                                {{-- Rank & Product --}}
                                <td class="px-3 py-3">
                                    <div class="flex items-center gap-2.5">
                                        <span class="text-xs font-bold w-4 flex-shrink-0" style="color: var(--text-4);">
                                            {{ $product['rank'] }}
                                        </span>
                                        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0
                                                    bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                                                    shadow-sm shadow-amber-500/20">
                                            <iconify-icon icon="mdi:tshirt-crew-outline" class="text-slate-900"></iconify-icon>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[11px] font-semibold truncate" style="color: var(--text-1);">
                                                {{ $product['name'] }}
                                            </p>
                                            <p class="text-[9px] truncate" style="color: var(--text-5);">
                                                {{ $product['variant'] }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Sold --}}
                                <td class="px-3 py-3 text-center">
                                    <span class="text-xs font-bold" style="color: var(--text-2);">
                                        {{ $product['sold'] }}
                                    </span>
                                </td>

                                {{-- Revenue --}}
                                <td class="px-3 py-3 text-right">
                                    <span class="text-xs font-bold" style="color: #ecbc42;">
                                        Rp {{ number_format($product['revenue'], 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- ROW 4: 3 Kartu Donut Charts --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Metode Pembayaran --}}
        <div class="rounded-2xl border overflow-hidden"
             style="background: var(--bg-card); border-color: var(--border-2);">

            <div class="px-5 py-4 border-b" style="background: var(--bg-input); border-color: var(--border-2);">
                <h3 class="font-bold text-sm" style="color: var(--text-1);">Metode Pembayaran</h3>
            </div>

            <div class="p-5">
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    {{-- Donut Chart --}}
                    <div class="relative flex-shrink-0">
                        <svg viewBox="0 0 100 100" class="w-28 h-28 -rotate-90">
                            @php
                                $paymentData = $paymentMethods ?? [
                                    ['label' => 'COD (Bayar di Tempat)', 'percent' => 48, 'amount' => 164_630_000, 'color' => '#60a5fa'],
                                    ['label' => 'Transfer Bank',         'percent' => 32, 'amount' => 109_760_000, 'color' => '#fb923c'],
                                    ['label' => 'E-Wallet',              'percent' => 15, 'amount' => 51_480_000,  'color' => '#a78bfa'],
                                    ['label' => 'Kartu Kredit',          'percent' => 5,  'amount' => 17_110_000,  'color' => '#ecbc42'],
                                ];
                                $offset = 0;
                                $circumference = 2 * 3.14159 * 40;
                            @endphp

                            @foreach ($paymentData as $item)
                                @php
                                    $dash = ($item['percent'] / 100) * $circumference;
                                    $gap = $circumference - $dash;
                                @endphp
                                <circle cx="50" cy="50" r="40"
                                        fill="none"
                                        stroke="{{ $item['color'] }}"
                                        stroke-width="12"
                                        stroke-dasharray="{{ $dash }} {{ $gap }}"
                                        stroke-dashoffset="{{ -$offset }}"/>
                                @php $offset += $dash; @endphp
                            @endforeach
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <p class="text-[9px] font-bold uppercase" style="color: var(--text-5);">Total</p>
                                <p class="text-xs font-bold" style="color: var(--text-1);">100%</p>
                            </div>
                        </div>
                    </div>

                    {{-- Legend --}}
                    <div class="flex-1 min-w-0 space-y-2">
                        @foreach ($paymentData as $item)
                            <div class="flex items-center gap-2 text-[10px]">
                                <span class="w-2 h-2 rounded-full flex-shrink-0"
                                      style="background: {{ $item['color'] }};"></span>
                                <span class="flex-1 truncate" style="color: var(--text-4);">{{ $item['label'] }}</span>
                                <span class="font-bold" style="color: var(--text-2);">{{ $item['percent'] }}%</span>
                                <span class="font-bold w-20 text-right" style="color: #ecbc42;">
                                    Rp {{ number_format($item['amount'], 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>


        {{-- Status Pesanan --}}
        <div class="rounded-2xl border overflow-hidden"
             style="background: var(--bg-card); border-color: var(--border-2);">

            <div class="px-5 py-4 border-b" style="background: var(--bg-input); border-color: var(--border-2);">
                <h3 class="font-bold text-sm" style="color: var(--text-1);">Status Pesanan</h3>
            </div>

            <div class="p-5">
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    {{-- Donut Chart --}}
                    <div class="relative flex-shrink-0">
                        <svg viewBox="0 0 100 100" class="w-28 h-28 -rotate-90">
                            @php
                                $statusData = $orderStatuses ?? [
                                    ['label' => 'Selesai',    'percent' => 82, 'count' => 1023, 'color' => '#34d399'],
                                    ['label' => 'Proses',     'percent' => 12, 'count' => 150,  'color' => '#60a5fa'],
                                    ['label' => 'Dikirim',    'percent' => 4,  'count' => 50,   'color' => '#fb923c'],
                                    ['label' => 'Dibatalkan', 'percent' => 2,  'count' => 25,   'color' => '#f87171'],
                                ];
                                $offset = 0;
                                $circumference = 2 * 3.14159 * 40;
                            @endphp

                            @foreach ($statusData as $item)
                                @php
                                    $dash = ($item['percent'] / 100) * $circumference;
                                    $gap = $circumference - $dash;
                                @endphp
                                <circle cx="50" cy="50" r="40"
                                        fill="none"
                                        stroke="{{ $item['color'] }}"
                                        stroke-width="12"
                                        stroke-dasharray="{{ $dash }} {{ $gap }}"
                                        stroke-dashoffset="{{ -$offset }}"/>
                                @php $offset += $dash; @endphp
                            @endforeach
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <p class="text-[9px] font-bold uppercase" style="color: var(--text-5);">Total</p>
                                <p class="text-xs font-bold" style="color: var(--text-1);">100%</p>
                            </div>
                        </div>
                    </div>

                    {{-- Legend --}}
                    <div class="flex-1 min-w-0 space-y-2">
                        @foreach ($statusData as $item)
                            <div class="flex items-center gap-2 text-[10px]">
                                <span class="w-2 h-2 rounded-full flex-shrink-0"
                                      style="background: {{ $item['color'] }};"></span>
                                <span class="flex-1 truncate" style="color: var(--text-4);">{{ $item['label'] }}</span>
                                <span class="font-bold" style="color: var(--text-2);">{{ $item['percent'] }}%</span>
                                <span class="font-bold w-16 text-right" style="color: #ecbc42;">
                                    {{ number_format($item['count'], 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>


        {{-- Sumber Trafik --}}
        <div class="rounded-2xl border overflow-hidden"
             style="background: var(--bg-card); border-color: var(--border-2);">

            <div class="px-5 py-4 border-b" style="background: var(--bg-input); border-color: var(--border-2);">
                <h3 class="font-bold text-sm" style="color: var(--text-1);">Sumber Trafik</h3>
            </div>

            <div class="p-5">
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    {{-- Donut Chart --}}
                    <div class="relative flex-shrink-0">
                        <svg viewBox="0 0 100 100" class="w-28 h-28 -rotate-90">
                            @php
                                $trafficData = $trafficSources ?? [
                                    ['label' => 'Shopee',     'percent' => 45, 'amount' => 154_340_000, 'color' => '#fb923c'],
                                    ['label' => 'TikTok Shop','percent' => 30, 'amount' => 102_890_000, 'color' => '#a78bfa'],
                                    ['label' => 'Tokopedia',  'percent' => 15, 'amount' => 51_460_000,  'color' => '#34d399'],
                                    ['label' => 'Lainnya',    'percent' => 10, 'amount' => 34_290_000,  'color' => '#60a5fa'],
                                ];
                                $offset = 0;
                                $circumference = 2 * 3.14159 * 40;
                            @endphp

                            @foreach ($trafficData as $item)
                                @php
                                    $dash = ($item['percent'] / 100) * $circumference;
                                    $gap = $circumference - $dash;
                                @endphp
                                <circle cx="50" cy="50" r="40"
                                        fill="none"
                                        stroke="{{ $item['color'] }}"
                                        stroke-width="12"
                                        stroke-dasharray="{{ $dash }} {{ $gap }}"
                                        stroke-dashoffset="{{ -$offset }}"/>
                                @php $offset += $dash; @endphp
                            @endforeach
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <p class="text-[9px] font-bold uppercase" style="color: var(--text-5);">Total</p>
                                <p class="text-xs font-bold" style="color: var(--text-1);">100%</p>
                            </div>
                        </div>
                    </div>

                    {{-- Legend --}}
                    <div class="flex-1 min-w-0 space-y-2">
                        @foreach ($trafficData as $item)
                            <div class="flex items-center gap-2 text-[10px]">
                                <span class="w-2 h-2 rounded-full flex-shrink-0"
                                      style="background: {{ $item['color'] }};"></span>
                                <span class="flex-1 truncate" style="color: var(--text-4);">{{ $item['label'] }}</span>
                                <span class="font-bold" style="color: var(--text-2);">{{ $item['percent'] }}%</span>
                                <span class="font-bold w-20 text-right" style="color: #ecbc42;">
                                    Rp {{ number_format($item['amount'], 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>


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

@endsection