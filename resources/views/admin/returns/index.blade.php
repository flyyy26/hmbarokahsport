@extends('layouts.admin')

@section('title', 'Manajemen Retur - Admin E-Commerce')
@section('page-title', 'Manajemen Retur')

@section('content')

<div class="w-full space-y-6">

    {{-- ============================================ --}}
    {{-- FLASH MESSAGES --}}
    {{-- ============================================ --}}
    @if (session('success'))
        <div class="flex items-start gap-3 rounded-xl px-4 py-3
                    bg-emerald-500/10 border border-emerald-500/30 text-emerald-400">
            <iconify-icon icon="mdi:check-circle-outline" class="text-xl flex-shrink-0 mt-0.5"></iconify-icon>
            <span class="text-sm">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-start gap-3 rounded-xl px-4 py-3
                    bg-red-500/10 border border-red-500/30 text-red-400">
            <iconify-icon icon="mdi:alert-circle-outline" class="text-xl flex-shrink-0 mt-0.5"></iconify-icon>
            <span class="text-sm">{{ session('error') }}</span>
        </div>
    @endif


    {{-- ============================================ --}}
    {{-- HEADER --}}
    {{-- ============================================ --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <h1 class="text-2xl font-bold flex items-center gap-2.5" style="color: var(--text-1)">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl
                             bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                             shadow-lg shadow-amber-500/20 flex-shrink-0">
                    <iconify-icon icon="mdi:backup-restore" class="text-slate-900 text-2xl"></iconify-icon>
                </span>
                Manajemen Retur
            </h1>
            <p class="text-sm mt-1.5 ml-12" style="color: var(--text-5)">
                Daftar permintaan retur dari pelanggan.
            </p>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- STATS CARDS --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

        {{-- Menunggu Persetujuan --}}
        <div class="rounded-xl border p-4"
             style="background: var(--bg-card); border-color: var(--border-2);">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider mb-1" style="color: var(--text-5)">
                        Menunggu Persetujuan
                    </p>
                    <p class="text-2xl font-bold" style="color: #fbbf24;">
                        {{ $pendingCount }}
                    </p>
                </div>
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg
                             bg-amber-500/10 border border-amber-500/30">
                    <iconify-icon icon="mdi:clock-outline" class="text-amber-400 text-lg"></iconify-icon>
                </span>
            </div>
        </div>

        {{-- Disetujui --}}
        <div class="rounded-xl border p-4"
             style="background: var(--bg-card); border-color: var(--border-2);">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider mb-1" style="color: var(--text-5)">
                        Disetujui (perlu kembalikan stok)
                    </p>
                    <p class="text-2xl font-bold" style="color: #60a5fa;">
                        {{ $approvedCount }}
                    </p>
                </div>
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg
                             bg-blue-500/10 border border-blue-500/30">
                    <iconify-icon icon="mdi:check-circle-outline" class="text-blue-400 text-lg"></iconify-icon>
                </span>
            </div>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- FILTER --}}
    {{-- ============================================ --}}
    <div class="flex flex-wrap items-center gap-2">

        @php
            $filterMap = [
                ['value' => '',          'label' => 'Semua Status'],
                ['value' => 'pending',   'label' => 'Menunggu Persetujuan'],
                ['value' => 'approved',  'label' => 'Disetujui'],
            ];
            $activeStatus = request('status', '');
        @endphp

        @foreach($filterMap as $filter)
            @php
                $isActive = $activeStatus === $filter['value'];
                $url = $filter['value'] === '' 
                    ? route('admin.returns.index') 
                    : route('admin.returns.index', ['status' => $filter['value']]);
            @endphp

            <a href="{{ $url }}"
               class="px-3.5 py-1.5 text-xs font-semibold rounded-lg border transition-all"
               @if($isActive)
                    style="background: #ecbc42; color: #422006; border-color: #ecbc42;"
               @else
                    style="background: var(--bg-input); color: var(--text-4); border-color: var(--border-2);"
                    onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                    onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-4)'"
               @endif>
                {{ $filter['label'] }}
            </a>
        @endforeach
    </div>


    {{-- ============================================ --}}
    {{-- TABLE --}}
    {{-- ============================================ --}}
    <div class="overflow-hidden rounded-xl border"
         style="background: var(--bg-card); border-color: var(--border-2);">

        @if ($orders->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full">

                    {{-- Header --}}
                    <thead class="border-b"
                           style="background: var(--bg-input); border-color: var(--border-2);">
                        <tr>
                            <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                                No. Pesanan
                            </th>
                            <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                                Pelanggan
                            </th>
                            <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                                Produk Retur
                            </th>
                            <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                                Qty
                            </th>
                            <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                                Status
                            </th>
                            <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                                Alasan
                            </th>
                            <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                                Tanggal
                            </th>
                            <th class="px-4 py-4 text-right text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    {{-- Body --}}
                    <tbody>
                        @foreach ($orders as $order)
                            @php
                                $totalReturnQty = $order->items->sum('quantity');

                                $statusColorMap = [
                                    'pending'   => '#fbbf24',
                                    'approved'  => '#60a5fa',
                                    'completed' => '#34d399',
                                    'rejected'  => '#f87171',
                                ];
                                $sc = $statusColorMap[$order->return_status] ?? '#94a3b8';
                            @endphp

                            <tr class="border-b last:border-0 transition-colors"
                                style="border-color: var(--border-1);"
                                onmouseover="this.style.background='var(--bg-hover)'"
                                onmouseout="this.style.background='transparent'">

                                {{-- No. Pesanan --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="font-mono text-xs font-bold" style="color: var(--text-1);">
                                        #{{ $order->order_number }}
                                    </span>
                                </td>

                                {{-- Pelanggan --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <p class="text-sm font-semibold truncate max-w-[180px]" style="color: var(--text-1);">
                                        {{ $order->user->name ?? '-' }}
                                    </p>
                                    @if($order->user->email)
                                        <p class="text-[10px] truncate max-w-[180px]" style="color: var(--text-5);">
                                            {{ $order->user->email }}
                                        </p>
                                    @endif
                                </td>

                                {{-- Produk Retur --}}
                                <td class="px-4 py-3">
                                    <div class="space-y-1">
                                        @foreach ($order->items as $item)
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="text-xs font-semibold truncate max-w-[200px]" style="color: var(--text-3);">
                                                    {{ $item->product_name }}
                                                </span>
                                                @if ($item->variant_name)
                                                    <span class="text-[10px] px-1.5 py-0.5 rounded border"
                                                          style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-5);">
                                                        {{ $item->variant_name }}
                                                    </span>
                                                @endif
                                                <span class="text-[10px] font-mono" style="color: var(--text-5);">
                                                    ×{{ $item->quantity }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>

                                {{-- Qty --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center justify-center min-w-[28px] h-7 px-2
                                                 rounded-lg border text-xs font-bold"
                                          style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-1);">
                                        {{ $totalReturnQty }}
                                    </span>
                                </td>

                                {{-- Status --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border"
                                          style="background: {{ $sc }}15; border-color: {{ $sc }}40; color: {{ $sc }};">
                                        <span class="w-1.5 h-1.5 rounded-full" style="background: currentColor;"></span>
                                        {{ $order->return_status_label }}
                                    </span>
                                </td>

                                {{-- Alasan --}}
                                <td class="px-4 py-3">
                                    @if ($order->return_reason)
                                        <p class="text-xs max-w-[200px] truncate" 
                                           title="{{ $order->return_reason }}"
                                           style="color: var(--text-4);">
                                            {{ $order->return_reason }}
                                        </p>
                                    @else
                                        <span class="text-xs italic" style="color: var(--text-5);">-</span>
                                    @endif
                                </td>

                                {{-- Tanggal --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($order->return_requested_at)
                                        <p class="text-xs font-semibold" style="color: var(--text-3);">
                                            {{ $order->return_requested_at->format('d M Y') }}
                                        </p>
                                        <p class="text-[10px] font-mono" style="color: var(--text-5);">
                                            {{ $order->return_requested_at->format('H:i') }}
                                        </p>
                                    @else
                                        <span class="text-xs italic" style="color: var(--text-5);">-</span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.returns.show', $order) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                                              text-xs font-bold transition-all active:scale-95
                                              bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                                              text-slate-900
                                              hover:shadow-lg hover:shadow-amber-500/30">
                                        <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            {{-- Empty State --}}
            <div class="py-20">
                <div class="flex flex-col items-center justify-center text-center max-w-sm mx-auto">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4 border"
                         style="background: var(--bg-input); border-color: var(--border-2);">
                        <iconify-icon icon="mdi:backup-restore" class="text-2xl" style="color: var(--text-6);"></iconify-icon>
                    </div>
                    <p class="text-sm font-semibold" style="color: var(--text-4);">
                        Tidak ada permintaan retur
                    </p>
                    <p class="text-xs mt-1" style="color: var(--text-5);">
                        Belum ada permintaan retur dari pelanggan saat ini.
                    </p>
                </div>
            </div>
        @endif
    </div>


    {{-- ============================================ --}}
    {{-- PAGINATION --}}
    {{-- ============================================ --}}
    @if ($orders->hasPages())
        <div class="rounded-xl border px-4 py-4"
             style="background: var(--bg-input); border-color: var(--border-2);">
            {{ $orders->links() }}
        </div>
    @endif

</div>

@endsection