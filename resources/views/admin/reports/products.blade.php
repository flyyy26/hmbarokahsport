@extends('layouts.admin')

@section('title', 'Laporan Produk')
@section('page-title', 'Laporan Produk')

@section('content')

<div class="w-full space-y-6">

    {{-- TOP BAR --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold flex items-center gap-2" style="color: var(--text-1)">
                <iconify-icon icon="mdi:package-variant-closed" class="text-2xl" style="color: #ecbc42"></iconify-icon>
                Laporan Produk
            </h1>
            <p class="text-sm mt-0.5" style="color: var(--text-5)">
                Daftar semua produk beserta performa penjualannya
            </p>
        </div>

        <form method="GET" action="{{ route('admin.reports.export') }}">
            <input type="hidden" name="month" value="{{ $monthParam }}">
            <input type="hidden" name="report_type" value="products">
            <button type="submit" name="format" value="csv"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg
                           text-xs font-bold border transition-all active:scale-95"
                    style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)"
                    onmouseover="this.style.borderColor='#ecbc42'; this.style.color='var(--gold-dark)'"
                    onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'">
                <iconify-icon icon="mdi:download-outline"></iconify-icon>
                Export CSV
            </button>
        </form>
    </div>

    {{-- MONTH FILTER --}}
    <form method="GET" action="{{ route('admin.reports.products') }}">
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

    {{-- METRICS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Produk --}}
        <div class="rounded-xl border p-3 text-center"
             style="background: var(--bg-card); border-color: var(--border-2);">
            <p class="text-[10px] font-bold uppercase" style="color: var(--text-5);">Total Produk</p>
            <p class="text-xl font-bold mt-1" style="color: var(--text-1)">
                {{ number_format($totalUniqueProducts) }}
            </p>
            <p class="text-[10px] mt-0.5" style="color: var(--text-5)">
                <span style="color: #34d399">{{ $totalProductsWithSales }} terjual</span>
                &middot;
                <span style="color: var(--text-4)">{{ $totalProductsWithoutSales }} belum</span>
            </p>
        </div>

        {{-- Total Terjual --}}
        <div class="rounded-xl border p-3 text-center"
             style="background: var(--bg-card); border-color: var(--border-2);">
            <p class="text-[10px] font-bold uppercase" style="color: var(--text-5);">Total Terjual</p>
            <p class="text-xl font-bold mt-1" style="color: #34d399">
                {{ number_format($totalProductsSold) }} pcs
            </p>
        </div>

        {{-- Total Pendapatan --}}
        <div class="rounded-xl border p-3 text-center"
             style="background: var(--bg-card); border-color: var(--border-2);">
            <p class="text-[10px] font-bold uppercase" style="color: var(--text-5);">Total Pendapatan</p>
            <p class="text-xl font-bold mt-1" style="color: #ecbc42">
                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
            </p>
        </div>

        {{-- Total Stok --}}
        <div class="rounded-xl border p-3 text-center"
             style="background: var(--bg-card); border-color: var(--border-2);">
            <p class="text-[10px] font-bold uppercase" style="color: var(--text-5);">Total Stok</p>
            <p class="text-xl font-bold mt-1" style="color: var(--text-1)">
                {{ number_format($totalStockQuantity ?? 0) }} pcs
            </p>
            <p class="text-[10px] mt-0.5" style="color: var(--text-5)">
                Nilai: Rp {{ number_format($totalStockValue, 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- PRODUCTS TABLE --}}
    <div class="rounded-2xl border overflow-hidden"
         style="background: var(--bg-card); border-color: var(--border-2);">

        <div class="px-5 py-4 border-b flex items-center justify-between"
             style="background: var(--bg-input); border-color: var(--border-2);">
            <h3 class="font-bold text-sm" style="color: var(--text-1);">
                Daftar Produk ({{ $start->format('d M') }} - {{ $end->format('d M') }})
            </h3>
            <span class="text-xs" style="color: var(--text-5)">
                {{ count($combined) }} produk
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="border-b" style="border-color: var(--border-1);">
                    <tr>
                        <th class="px-4 py-2 text-center text-[9px] font-bold uppercase" style="color: var(--text-5)">#</th>
                        <th class="px-4 py-2 text-left text-[9px] font-bold uppercase" style="color: var(--text-5)">Produk</th>
                        <th class="px-4 py-2 text-left text-[9px] font-bold uppercase" style="color: var(--text-5)">SKU</th>
                        <th class="px-4 py-2 text-left text-[9px] font-bold uppercase" style="color: var(--text-5)">Kategori</th>
                        <th class="px-4 py-2 text-center text-[9px] font-bold uppercase" style="color: var(--text-5)">Terjual</th>
                        <th class="px-4 py-2 text-right text-[9px] font-bold uppercase" style="color: var(--text-5)">Pendapatan</th>
                        <th class="px-4 py-2 text-center text-[9px] font-bold uppercase" style="color: var(--text-5)">Stok</th>
                        <th class="px-4 py-2 text-right text-[9px] font-bold uppercase" style="color: var(--text-5)">Range Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($combined as $product)
                        @php
                            $hasSales = ($product->sold ?? 0) > 0;
                        @endphp
                        <tr class="border-b last:border-0 transition-colors"
                            style="border-color: var(--border-1); {{ !$hasSales ? 'opacity: 0.6;' : '' }}">
                            <td class="px-4 py-2">
                                <span class="text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center mx-auto"
                                      style="background: var(--bg-elevated); color: {{ $hasSales ? '#ecbc42' : 'var(--text-5)' }}">
                                    {{ $loop->iteration }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <div class="flex items-center gap-2">
                                    <p class="text-xs font-semibold" style="color: var(--text-1)">
                                        {{ $product->product_name ?? '-' }}
                                    </p>
                                    @if(!$hasSales)
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-bold"
                                              style="background: var(--bg-elevated); color: var(--text-5)">
                                            Belum Terjual
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-2">
                                <p class="text-[10px] font-mono" style="color: var(--text-5)">
                                    {{ $product->sku ?? '-' }}
                                </p>
                            </td>
                            <td class="px-4 py-2">
                                <p class="text-[10px]" style="color: var(--text-4)">
                                    {{ $product->category ?? '-' }}
                                </p>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <span class="text-xs font-bold" style="color: {{ $hasSales ? '#34d399' : 'var(--text-5)' }}">
                                    {{ number_format($product->sold ?? 0) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-right">
                                <span class="text-xs font-bold" style="color: {{ $hasSales ? '#ecbc42' : 'var(--text-5)' }}">
                                    Rp {{ number_format($product->revenue ?? 0, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-center">
                                @php
                                    $stock = $product->stock ?? 0;
                                    $stockColor = $stock <= 0 ? '#ef4444' : ($stock <= 5 ? '#f59e0b' : 'var(--text-4)');
                                @endphp
                                <span class="text-xs font-semibold" style="color: {{ $stockColor }}">
                                    {{ number_format($stock) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-right">
                                <span class="text-[10px]" style="color: var(--text-4)">
                                    {{ $product->price_range ?? '-' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-[11px]" style="color: var(--text-5)">
                                Belum ada produk yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- FOOTER SUMMARY --}}
        @if(count($combined) > 0)
            <div class="px-5 py-3 border-t flex flex-wrap items-center justify-between gap-3"
                 style="background: var(--bg-input); border-color: var(--border-2);">
                <div class="flex flex-wrap items-center gap-4 text-[10px]" style="color: var(--text-5)">
                    <span>
                        <strong style="color: #34d399">{{ $totalProductsWithSales }}</strong> produk terjual
                    </span>
                    <span>
                        <strong style="color: var(--text-4)">{{ $totalProductsWithoutSales }}</strong> produk belum terjual
                    </span>
                </div>
                <div class="text-[10px]" style="color: var(--text-5)">
                    Total: <strong style="color: #ecbc42">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</strong>
                </div>
            </div>
        @endif
    </div>

</div>

@endsection