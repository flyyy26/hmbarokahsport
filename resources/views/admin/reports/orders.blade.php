@extends('layouts.admin')

@section('title', 'Laporan Pesanan')
@section('page-title', 'Laporan Pesanan')

@section('content')

<div class="w-full space-y-6">

    {{-- TOP BAR --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold flex items-center gap-2" style="color: var(--text-1)">
                <iconify-icon icon="mdi:file-chart-outline" class="text-2xl" style="color: #ecbc42"></iconify-icon>
                Laporan Pesanan
            </h1>
            <p class="text-sm mt-0.5" style="color: var(--text-5)">
                Semua pesanan (online + offline) dalam periode yang dipilih
            </p>
        </div>

        <div class="flex items-center gap-2">
            <form method="GET" action="{{ route('admin.reports.export') }}">
                <input type="hidden" name="month" value="{{ $monthParam }}">
                <input type="hidden" name="report_type" value="orders">
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
            <form method="GET" action="{{ route('admin.reports.export') }}">
                <input type="hidden" name="month" value="{{ $monthParam }}">
                <input type="hidden" name="report_type" value="orders">
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

    {{-- MONTH FILTER --}}
    <form method="GET" action="{{ route('admin.reports.orders') }}">
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

    {{-- SUMMARY METRICS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Total Penjualan --}}
        <div class="rounded-xl border p-3 text-center"
            style="background: var(--bg-card); border-color: var(--border-2);">
            <p class="text-[10px] font-bold uppercase" style="color: var(--text-5);">Total Penjualan</p>
            <p class="text-xl font-bold mt-1" style="color: #ecbc42">
                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
            </p>
        </div>

        {{-- Total Order Selesai--}}
        <div class="rounded-xl border p-3 text-center"
            style="background: var(--bg-card); border-color: var(--border-2);">
            <p class="text-[10px] font-bold uppercase" style="color: var(--text-5);">Total Order Selesai</p>
            <p class="text-xl font-bold mt-1" style="color: var(--text-1)">
                {{ number_format($totalOrderCount) }}
            </p>
        </div>

        {{-- 🔥 Total Retur (BARU) --}}
        <div class="rounded-xl border p-3 text-center"
            style="background: var(--bg-card); border-color: var(--border-2);">
            <p class="text-[10px] font-bold uppercase" style="color: var(--text-5);">Total Retur</p>
            <p class="text-xl font-bold mt-1" style="color: #60a5fa">
                {{ number_format($returnCount) }}
            </p>
        </div>

        {{-- 🔥 Total Order Batal (BARU) --}}
        <div class="rounded-xl border p-3 text-center"
            style="background: var(--bg-card); border-color: var(--border-2);">
            <p class="text-[10px] font-bold uppercase" style="color: var(--text-5);">Total Order Batal</p>
            <p class="text-xl font-bold mt-1" style="color: #f87171">
                {{ number_format($cancelledCount) }}
            </p>
        </div>
    </div>

    {{-- TABS --}}
    <div class="border-b" style="border-color: var(--border-2);">
        <nav class="-mb-px flex gap-4 overflow-x-auto">
            <a href="?month={{ $monthParam }}&type=all"
               class="py-2 px-4 text-xs font-bold border-b-2 transition-all"
               style="{{ $type === 'all' ? 'border-color: var(--gold-bright); color: var(--gold-dark)' : 'border-transparent; color: var(--text-5)' }}">
                Semua Pesanan
            </a>
            <a href="?month={{ $monthParam }}&type=online"
               class="py-2 px-4 text-xs font-bold border-b-2 transition-all"
               style="{{ $type === 'online' ? 'border-color: var(--gold-bright); color: var(--gold-dark)' : 'border-transparent; color: var(--text-5)' }}">
                Pesanan Online
            </a>
            <a href="?month={{ $monthParam }}&type=offline"
               class="py-2 px-4 text-xs font-bold border-b-2 transition-all"
               style="{{ $type === 'offline' ? 'border-color: var(--gold-bright); color: var(--gold-dark)' : 'border-transparent; color: var(--text-5)' }}">
                Pesanan Offline
            </a>
        </nav>
    </div>

    {{-- ONLINE ORDERS TABLE --}}
    @if($type !== 'offline')
        <div class="rounded-2xl border overflow-hidden" style="background: var(--bg-card); border-color: var(--border-2);">
            <div class="px-5 py-4 border-b flex items-center justify-between"
                 style="background: var(--bg-input); border-color: var(--border-2);">
                <h3 class="font-bold text-sm" style="color: var(--text-1);">Pesanan Online ({{ $onlineOrders->count() }})</h3>
                <span class="text-xs" style="color: var(--text-5)">
                    Total: Rp {{ number_format($onlineOrders->where('payment_status', 'paid')->sum('total'), 0, ',', '.') }}
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="border-b" style="border-color: var(--border-1);">
                        <tr>
                            <th class="px-4 py-2 text-left text-[9px] font-bold uppercase" style="color: var(--text-5)">Tanggal</th>
                            <th class="px-4 py-2 text-left text-[9px] font-bold uppercase" style="color: var(--text-5)">No. Order</th>
                            <th class="px-4 py-2 text-left text-[9px] font-bold uppercase" style="color: var(--text-5)">Customer</th>
                            <th class="px-4 py-2 text-center text-[9px] font-bold uppercase" style="color: var(--text-5)">Order</th>
                            <th class="px-4 py-2 text-right text-[9px] font-bold uppercase" style="color: var(--text-5)">Total</th>
                            <th class="px-4 py-2 text-right text-[9px] font-bold uppercase" style="color: var(--text-5)">Ongkir</th>
                            <th class="px-4 py-2 text-center text-[9px] font-bold uppercase" style="color: var(--text-5)">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($onlineOrders as $order)
                            <tr class="border-b last:border-0" style="border-color: var(--border-1);">
                                <td class="px-4 py-2">
                                    <span class="text-xs" style="color: var(--text-4)">{{ $order->created_at->format('d/m/Y') }}</span>
                                </td>
                                <td class="px-4 py-2">
                                    <span class="text-xs font-mono" style="color: var(--text-2)">{{ $order->order_number }}</span>
                                </td>
                                <td class="px-4 py-2">
                                    <p class="text-xs font-semibold" style="color: var(--text-1)">{{ $order->shipping_name ?? $order->user->name ?? 'Guest' }}</p>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <span class="text-xs" style="color: var(--text-4)">{{ $order->items->sum('quantity') ?? $order->items->count() }}</span>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <span class="text-xs font-bold" style="color: var(--text-1)">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <span class="text-xs" style="color: var(--text-4)">Rp {{ number_format($order->shipping_cost ?? 0, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                       class="text-[10px] px-2 py-1 rounded"
                                       style="background: var(--bg-elevated); color: var(--text-3)">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-[11px]" style="color: var(--text-5)">
                                    Tidak ada pesanan online.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- OFFLINE ORDERS TABLE --}}
    @if($type !== 'online')
        <div class="rounded-2xl border overflow-hidden" style="background: var(--bg-card); border-color: var(--border-2);">
            <div class="px-5 py-4 border-b flex items-center justify-between"
                 style="background: var(--bg-input); border-color: var(--border-2);">
                <h3 class="font-bold text-sm" style="color: var(--text-1);">Pesanan Offline ({{ $offlineOrders->count() }})</h3>
                <span class="text-xs" style="color: var(--text-5)">
                    Total: Rp {{ number_format($offlineOrders->where('payment_status', 'paid')->sum('total'), 0, ',', '.') }}
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="border-b" style="border-color: var(--border-1);">
                        <tr>
                            <th class="px-4 py-2 text-left text-[9px] font-bold uppercase" style="color: var(--text-5)">Tanggal</th>
                            <th class="px-4 py-2 text-left text-[9px] font-bold uppercase" style="color: var(--text-5)">No. Order</th>
                            <th class="px-4 py-2 text-left text-[9px] font-bold uppercase" style="color: var(--text-5)">Customer</th>
                            <th class="px-4 py-2 text-center text-[9px] font-bold uppercase" style="color: var(--text-5)">Order</th>
                            <th class="px-4 py-2 text-center text-[9px] font-bold uppercase" style="color: var(--text-5)">Bayar</th>
                            <th class="px-4 py-2 text-right text-[9px] font-bold uppercase" style="color: var(--text-5)">Total</th>
                            <th class="px-4 py-2 text-center text-[9px] font-bold uppercase" style="color: var(--text-5)">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($offlineOrders as $order)
                            <tr class="border-b last:border-0" style="border-color: var(--border-1);">
                                <td class="px-4 py-2">
                                    <span class="text-xs" style="color: var(--text-4)">{{ $order->created_at->format('d/m/Y') }}</span>
                                </td>
                                <td class="px-4 py-2">
                                    <span class="text-xs font-mono" style="color: var(--text-2)">{{ $order->order_number }}</span>
                                </td>
                                <td class="px-4 py-2">
                                    <p class="text-xs font-semibold" style="color: var(--text-1)">{{ $order->customer_name ?? 'Walk-in' }}</p>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <span class="text-xs" style="color: var(--text-4)">{{ $order->items->sum('quantity') ?? $order->items->count() }}</span>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <span class="text-xs font-mono" style="color: #34d399">{{ strtoupper($order->payment_method ?? 'CASH') }}</span>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <span class="text-xs font-bold" style="color: var(--text-1)">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <a href="{{ route('admin.orders.offline.receipt', $order) }}" target="_blank"
                                       class="text-[10px] px-2 py-1 rounded"
                                       style="background: var(--bg-elevated); color: var(--text-3)">
                                        Nota
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-[11px]" style="color: var(--text-5)">
                                    Tidak ada pesanan offline.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>

@endsection
