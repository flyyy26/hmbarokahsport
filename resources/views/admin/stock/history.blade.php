{{-- resources/views/admin/stock/history.blade.php --}}

@extends('layouts.admin')

@section('content')
<div class="w-full space-y-6">

    {{-- ============================================ --}}
    {{-- HEADER --}}
    {{-- ============================================ --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <h1 class="text-2xl font-bold flex items-center gap-2.5" style="color: var(--text-1)">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl
                             bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                             shadow-lg shadow-amber-500/20 flex-shrink-0">
                    <iconify-icon icon="mdi:history" class="text-slate-900 text-2xl"></iconify-icon>
                </span>
                History Stok
            </h1>
            <p class="text-sm mt-1.5 ml-12 truncate" style="color: var(--text-5)">
                Produk <strong style="color: var(--text-3)">{{ $product->name }}</strong>
            </p>
        </div>

        <div class="flex gap-2 flex-shrink-0">
            <a href="{{ route('admin.stock.edit', $product) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg
                      text-sm font-bold transition-all active:scale-95
                      bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                      text-slate-900
                      shadow-lg shadow-amber-500/20
                      hover:shadow-xl hover:shadow-amber-500/40">
                Edit Stok
            </a>
            <a href="{{ route('admin.stock.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg
                      text-sm font-semibold transition-all active:scale-95 border"
               style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)"
               onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
               onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'">
                <iconify-icon icon="mdi:arrow-left"></iconify-icon>
                Kembali
            </a>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- SUMMARY --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">

        {{-- Total Stok --}}
        <div class="rounded-xl border p-4"
             style="background: var(--bg-card); border-color: var(--border-2)">
            <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--text-5)">
                Total Stok
            </p>
            <p class="text-2xl font-bold mt-1" style="color: var(--text-1)">
                {{ number_format($product->variants->sum('stock')) }}
            </p>
        </div>

        {{-- Total History --}}
        <div class="rounded-xl border p-4"
             style="background: var(--bg-card); border-color: var(--border-2)">
            <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--text-5)">
                Total History
            </p>
            <p class="text-2xl font-bold mt-1" style="color: var(--text-1)">
                {{ $histories->total() }}
            </p>
        </div>

        {{-- Terakhir Update --}}
        <div class="rounded-xl border p-4"
             style="background: var(--bg-card); border-color: var(--border-2)">
            <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--text-5)">
                Terakhir Update
            </p>
            <p class="text-sm font-bold mt-1" style="color: var(--text-1)">
                {{ $histories->first()?->created_at->format('d M Y, H:i') ?? '-' }}
            </p>
        </div>

        {{-- Varian --}}
        <div class="rounded-xl border p-4"
             style="background: var(--bg-card); border-color: var(--border-2)">
            <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--text-5)">
                Varian
            </p>
            <p class="text-2xl font-bold mt-1" style="color: var(--text-1)">
                {{ $product->variants->count() }}
            </p>
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
                        <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Tanggal
                        </th>
                        <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Varian
                        </th>
                        <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Stok Lama
                        </th>
                        <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Stok Baru
                        </th>
                        <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Perubahan
                        </th>
                        <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Alasan
                        </th>
                        <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Catatan
                        </th>
                        <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Oleh
                        </th>
                    </tr>
                </thead>

                {{-- Body --}}
                <tbody>
                    @forelse ($histories as $history)
                        <tr class="transition-colors border-b last:border-0"
                            style="border-color: var(--border-1)"
                            onmouseover="this.style.background='var(--bg-hover)'"
                            onmouseout="this.style.background='transparent'">

                            {{-- Tanggal --}}
                            <td class="px-5 py-4 whitespace-nowrap text-xs" style="color: var(--text-4)">
                                <div class="font-semibold" style="color: var(--text-3)">
                                    {{ $history->created_at->format('d M Y') }}
                                </div>
                                <div class="text-[10px]" style="color: var(--text-5)">
                                    {{ $history->created_at->format('H:i') }}
                                </div>
                            </td>

                            {{-- Varian --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                @php
                                    $variantName = $history->variant?->values->pluck('value')->implode(' · ') ?? '-';
                                @endphp
                                <span class="text-xs font-semibold" style="color: var(--text-3)">
                                    {{ $variantName }}
                                </span>
                            </td>

                            {{-- Stok Lama --}}
                            <td class="px-5 py-4 whitespace-nowrap text-sm font-semibold" style="color: var(--text-5)">
                                {{ $history->old_stock }}
                            </td>

                            {{-- Stok Baru --}}
                            <td class="px-5 py-4 whitespace-nowrap text-sm font-bold" style="color: var(--text-1)">
                                {{ $history->new_stock }}
                            </td>

                            {{-- Perubahan --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                @php
                                    $isIncrease = $history->quantity_change > 0;
                                    $isDecrease = $history->quantity_change < 0;
                                @endphp

                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                                             text-xs font-bold border"
                                      @if($isIncrease)
                                          style="background: rgba(16,185,129,0.1); border-color: rgba(16,185,129,0.3); color: #34d399;"
                                      @elseif($isDecrease)
                                          style="background: rgba(239,68,68,0.1); border-color: rgba(239,68,68,0.3); color: #f87171;"
                                      @else
                                          style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-4);"
                                      @endif>
                                    {{ $isIncrease ? '+' : '' }}{{ $history->quantity_change }}
                                </span>
                            </td>

                            {{-- Alasan --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                @php
                                    $reasonMap = [
                                        'green'  => ['bg' => 'rgba(16,185,129,0.1)', 'border' => 'rgba(16,185,129,0.3)', 'text' => '#34d399'],
                                        'blue'   => ['bg' => 'rgba(59,130,246,0.1)', 'border' => 'rgba(59,130,246,0.3)', 'text' => '#60a5fa'],
                                        'red'    => ['bg' => 'rgba(239,68,68,0.1)',  'border' => 'rgba(239,68,68,0.3)',  'text' => '#f87171'],
                                        'yellow' => ['bg' => 'rgba(245,158,11,0.1)', 'border' => 'rgba(245,158,11,0.3)', 'text' => '#fbbf24'],
                                        'purple' => ['bg' => 'rgba(168,85,247,0.1)', 'border' => 'rgba(168,85,247,0.3)', 'text' => '#c084fc'],
                                        'indigo' => ['bg' => 'rgba(99,102,241,0.1)', 'border' => 'rgba(99,102,241,0.3)', 'text' => '#a5b4fc'],
                                        'orange' => ['bg' => 'rgba(249,115,22,0.1)', 'border' => 'rgba(249,115,22,0.3)', 'text' => '#fb923c'],
                                        'gray'   => ['bg' => 'var(--bg-input)',       'border' => 'var(--border-2)',       'text' => 'var(--text-4)'],
                                    ];
                                    $r = $reasonMap[$history->reason_color] ?? $reasonMap['gray'];
                                @endphp

                                <span class="inline-flex items-center px-2.5 py-1 rounded-full
                                             text-[11px] font-bold border"
                                      style="background: {{ $r['bg'] }}; border-color: {{ $r['border'] }}; color: {{ $r['text'] }};">
                                    {{ $history->reason_label }}
                                </span>
                            </td>

                            {{-- Catatan --}}
                            <td class="px-5 py-4 text-xs max-w-[200px] truncate" style="color: var(--text-5)">
                                {{ $history->note ?? '-' }}
                            </td>

                            {{-- Oleh --}}
                            <td class="px-5 py-4 whitespace-nowrap text-xs font-semibold" style="color: var(--text-3)">
                                {{ $history->user?->name ?? 'Sistem' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-20">
                                <div class="flex flex-col items-center justify-center text-center max-w-sm mx-auto">
                                    <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4 border"
                                         style="background: var(--bg-input); border-color: var(--border-2)">
                                        <iconify-icon icon="mdi:history" class="text-2xl" style="color: var(--text-6)"></iconify-icon>
                                    </div>
                                    <p class="text-sm" style="color: var(--text-5)">
                                        Belum ada history stok untuk produk ini.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($histories->hasPages())
            <div class="border-t px-6 py-4"
                 style="border-color: var(--border-2); background: var(--bg-input)">
                {{ $histories->links() }}
            </div>
        @endif
    </div>
</div>
@endsection