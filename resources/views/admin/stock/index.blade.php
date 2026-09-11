{{-- resources/views/admin/stock/index.blade.php --}}

@extends('layouts.admin')

@section('content')
<div class="w-full space-y-6">

    {{-- ============================================ --}}
    {{-- HEADER --}}
    {{-- ============================================ --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <h1 class="text-2xl font-bold" style="color: var(--text-1)">
                Manajemen Stok
            </h1>
            <p class="mt-1.5 text-sm" style="color: var(--text-5)">
                Kelola stok semua produk dan lihat riwayat perubahan.
            </p>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- STATISTIK --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">

        {{-- Total Produk --}}
        <div class="rounded-xl border p-4"
             style="background: var(--bg-card); border-color: var(--border-2)">
            <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--text-5)">
                Total Produk
            </p>
            <p class="text-2xl font-bold mt-1" style="color: var(--text-1)">
                {{ $stats['total_products'] }}
            </p>
        </div>

        {{-- Kritis --}}
        <div class="rounded-xl border p-4 bg-red-500/10 border-red-500/30">
            <p class="text-xs font-semibold uppercase tracking-wider text-red-400">
                Kritis
            </p>
            <p class="text-2xl font-bold mt-1 text-red-400">
                {{ $stats['critical_count'] }}
            </p>
        </div>

        {{-- Menipis --}}
        <div class="rounded-xl border p-4 bg-amber-500/10 border-amber-500/30">
            <p class="text-xs font-semibold uppercase tracking-wider text-amber-400">
                Menipis
            </p>
            <p class="text-2xl font-bold mt-1 text-amber-400">
                {{ $stats['low_count'] }}
            </p>
        </div>

        {{-- Habis --}}
        <div class="rounded-xl border p-4"
             style="background: var(--bg-input); border-color: var(--border-2)">
            <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--text-5)">
                Habis
            </p>
            <p class="text-2xl font-bold mt-1" style="color: var(--text-3)">
                {{ $stats['out_of_stock_count'] }}
            </p>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- FILTER & SEARCH --}}
    {{-- ============================================ --}}
    <div class="flex flex-wrap items-center gap-2">

        {{-- Filter Buttons --}}
        <a href="{{ route('admin.stock.index', ['stock' => 'all']) }}"
           class="px-3.5 py-1.5 text-xs font-semibold rounded-lg border transition-all"
           @if(request('stock') == 'all' || !request('stock'))
                style="background: #ecbc42; color: #422006; border-color: #ecbc42;"
           @else
                style="background: var(--bg-input); color: var(--text-4); border-color: var(--border-2);"
                onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-4)'"
           @endif>
            Semua
        </a>

        <a href="{{ route('admin.stock.index', ['stock' => 'critical']) }}"
           class="px-3.5 py-1.5 text-xs font-semibold rounded-lg border transition-all"
           @if(request('stock') == 'critical')
                style="background: #ef4444; color: #fff; border-color: #ef4444;"
           @else
                style="background: var(--bg-input); color: #f87171; border-color: var(--border-2);"
                onmouseover="this.style.borderColor='#ef4444';"
                onmouseout="this.style.borderColor='var(--border-2)'"
           @endif>
            Kritis
        </a>

        <a href="{{ route('admin.stock.index', ['stock' => 'low']) }}"
           class="px-3.5 py-1.5 text-xs font-semibold rounded-lg border transition-all"
           @if(request('stock') == 'low')
                style="background: #f59e0b; color: #fff; border-color: #f59e0b;"
           @else
                style="background: var(--bg-input); color: #fbbf24; border-color: var(--border-2);"
                onmouseover="this.style.borderColor='#f59e0b';"
                onmouseout="this.style.borderColor='var(--border-2)'"
           @endif>
            Menipis
        </a>

        <a href="{{ route('admin.stock.index', ['stock' => 'out_of_stock']) }}"
           class="px-3.5 py-1.5 text-xs font-semibold rounded-lg border transition-all"
           @if(request('stock') == 'out_of_stock')
                style="background: var(--text-5); color: #fff; border-color: var(--text-5);"
           @else
                style="background: var(--bg-input); color: var(--text-4); border-color: var(--border-2);"
                onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-4)'"
           @endif>
            Habis
        </a>

        <a href="{{ route('admin.stock.index', ['stock' => 'in_stock']) }}"
           class="px-3.5 py-1.5 text-xs font-semibold rounded-lg border transition-all"
           @if(request('stock') == 'in_stock')
                style="background: #10b981; color: #fff; border-color: #10b981;"
           @else
                style="background: var(--bg-input); color: #34d399; border-color: var(--border-2);"
                onmouseover="this.style.borderColor='#10b981';"
                onmouseout="this.style.borderColor='var(--border-2)'"
           @endif>
            Aman
        </a>

        {{-- Search --}}
        <div class="ml-auto">
            <form method="GET" class="flex gap-2">
                @if(request('stock'))
                    <input type="hidden" name="stock" value="{{ request('stock') }}">
                @endif

                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari produk..."
                       class="form-input"
                       style="padding: 0.4rem 0.85rem; font-size: 0.8rem; min-width: 200px;">

                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg
                               text-xs font-bold transition-all active:scale-95
                               bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                               text-slate-900
                               hover:shadow-lg hover:shadow-amber-500/30">
                    Cari
                </button>
            </form>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- TABLE --}}
    {{-- ============================================ --}}
    <div class="overflow-hidden rounded-xl border"
         style="background: var(--bg-card); border-color: var(--border-2)">

        <div class="overflow-x-auto">
            <table class="min-w-full">

                {{-- Header --}}
                <thead class="border-b"
                       style="background: var(--bg-input); border-color: var(--border-2)">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Produk
                        </th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Kategori
                        </th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Varian
                        </th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Total Stok
                        </th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Status
                        </th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Aksi
                        </th>
                    </tr>
                </thead>

                {{-- Body --}}
                <tbody>
                    @forelse ($products as $product)
                        <tr class="transition-colors border-b last:border-0"
                            style="border-color: var(--border-1)"
                            onmouseover="this.style.background='var(--bg-hover)'"
                            onmouseout="this.style.background='transparent'">

                            {{-- Produk --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if ($product->images->first())
                                        <div class="w-10 h-10 rounded-lg overflow-hidden border flex-shrink-0"
                                             style="border-color: var(--border-2)">
                                            <img src="{{ Storage::url($product->images->first()->image) }}"
                                                 alt="{{ $product->name }}"
                                                 class="h-full w-full object-cover">
                                        </div>
                                    @else
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 border"
                                             style="background: var(--bg-input); border-color: var(--border-2)">
                                            <span class="text-xs font-bold" style="color: var(--text-6)">?</span>
                                        </div>
                                    @endif
                                    <span class="text-sm font-semibold truncate max-w-[240px]" style="color: var(--text-1)">
                                        {{ $product->name }}
                                    </span>
                                </div>
                            </td>

                            {{-- Kategori --}}
                            <td class="px-6 py-4 text-sm" style="color: var(--text-4)">
                                {{ $product->category->name ?? '-' }}
                            </td>

                            {{-- Varian --}}
                            <td class="px-6 py-4 text-sm font-semibold" style="color: var(--text-3)">
                                {{ $product->variants->count() }}
                            </td>

                            {{-- Total Stok --}}
                            <td class="px-6 py-4 text-sm font-bold" style="color: var(--text-1)">
                                {{ number_format($product->total_stock) }}
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4">
                                @php
                                    $statusMap = [
                                        'red'    => ['bg' => 'rgba(239,68,68,0.1)',  'border' => 'rgba(239,68,68,0.3)',  'text' => '#f87171', 'dot' => '#ef4444'],
                                        'yellow' => ['bg' => 'rgba(245,158,11,0.1)', 'border' => 'rgba(245,158,11,0.3)', 'text' => '#fbbf24', 'dot' => '#f59e0b'],
                                        'green'  => ['bg' => 'rgba(16,185,129,0.1)', 'border' => 'rgba(16,185,129,0.3)', 'text' => '#34d399', 'dot' => '#10b981'],
                                        'gray'   => ['bg' => 'var(--bg-input)',       'border' => 'var(--border-2)',       'text' => 'var(--text-4)', 'dot' => 'var(--text-5)'],
                                    ];
                                    $s = $statusMap[$product->stock_status_color] ?? $statusMap['gray'];
                                @endphp

                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                             text-[11px] font-bold border"
                                      style="background: {{ $s['bg'] }}; border-color: {{ $s['border'] }}; color: {{ $s['text'] }};">
                                    <span class="w-1.5 h-1.5 rounded-full" style="background: {{ $s['dot'] }};"></span>
                                    {{ $product->stock_status_label }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">

                                    <a href="{{ route('admin.stock.edit', $product) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg
                                              text-xs font-bold transition-all active:scale-95
                                              bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                                              text-slate-900
                                              hover:shadow-lg hover:shadow-amber-500/30">
                                        Edit Stok
                                    </a>

                                    <a href="{{ route('admin.stock.history', $product) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg
                                              text-xs font-semibold transition-all active:scale-95 border"
                                       style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)"
                                       onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                                       onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'">
                                        Histori
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-20">
                                <div class="flex flex-col items-center justify-center text-center max-w-sm mx-auto">
                                    <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4 border"
                                         style="background: var(--bg-input); border-color: var(--border-2)">
                                        <span class="text-2xl" style="color: var(--text-6)">—</span>
                                    </div>
                                    <p class="text-sm" style="color: var(--text-5)">
                                        Tidak ada produk ditemukan.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($products->hasPages())
            <div class="border-t px-6 py-4"
                 style="border-color: var(--border-2); background: var(--bg-input)">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection