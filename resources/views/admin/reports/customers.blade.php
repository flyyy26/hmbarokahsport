@extends('layouts.admin')

@section('title', 'Laporan Pelanggan')
@section('page-title', 'Laporan Pelanggan')

@section('content')

<div class="w-full space-y-6">

    {{-- TOP BAR --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold flex items-center gap-2" style="color: var(--text-1)">
                <iconify-icon icon="mdi:account-multiple-outline" class="text-2xl" style="color: var(--gold-bright)"></iconify-icon>
                Laporan Pelanggan
            </h1>
            <p class="text-sm mt-0.5" style="color: var(--text-5)">
                Daftar pelanggan, jumlah order, dan total pembayaran
            </p>
        </div>

        <form method="GET" action="{{ route('admin.reports.export') }}">
            <input type="hidden" name="month" value="{{ $monthParam }}">
            <input type="hidden" name="report_type" value="customers">
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
    </div>

    {{-- MONTH FILTER --}}
    <form method="GET" action="{{ route('admin.reports.customers') }}">
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
        <div class="rounded-xl border p-3 text-center"
             style="background: var(--bg-card); border-color: var(--border-2);">
            <p class="text-[10px] font-bold uppercase" style="color: var(--text-5);">Total Pelanggan</p>
            <p class="text-xl font-bold mt-1" style="color: var(--gold-bright)">
                {{ number_format($totalCustomers) }}
            </p>
        </div>
        <div class="rounded-xl border p-3 text-center"
             style="background: var(--bg-card); border-color: var(--border-2);">
            <p class="text-[10px] font-bold uppercase" style="color: var(--text-5);">Total Pendapatan</p>
            <p class="text-xl font-bold mt-1" style="color: #34d399">
                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
            </p>
        </div>
        <div class="rounded-xl border p-3 text-center"
             style="background: var(--bg-card); border-color: var(--border-2);">
            <p class="text-[10px] font-bold uppercase" style="color: var(--text-5);">Total Order</p>
            <p class="text-xl font-bold mt-1" style="color: #a78bfa">
                {{ number_format($totalOrderCount) }}
            </p>
        </div>
        <div class="rounded-xl border p-3 text-center"
             style="background: var(--bg-card); border-color: var(--border-2);">
            <p class="text-[10px] font-bold uppercase" style="color: var(--text-5);">Total Stok Barang</p>
            <p class="text-xl font-bold mt-1" style="color: var(--text-1)">
                Rp {{ number_format($totalStockValue, 0, ',', '.') }}
            </p>
        </div>
        <div class="rounded-xl border p-3 text-center"
             style="background: var(--bg-card); border-color: var(--border-2);">
            <p class="text-[10px] font-bold uppercase" style="color: var(--text-5);">Dana Biteship</p>
            <p class="text-xl font-bold mt-1" style="color: #ef4444">
                Rp {{ number_format($biteshipShippingCost, 0, ',', '.') }}
            </p>
            <p class="text-[10px] mt-0.5" style="color: var(--text-5)">{{ number_format($biteshipOrderCount) }} order</p>
        </div>
    </div>

    {{-- CUSTOMERS TABLE --}}
    <div class="rounded-2xl border overflow-hidden"
         style="background: var(--bg-card); border-color: var(--border-2);">

        <div class="px-5 py-4 border-b flex items-center justify-between"
             style="background: var(--bg-input); border-color: var(--border-2);">
            <h3 class="font-bold text-sm" style="color: var(--text-1);">
                Daftar Pelanggan ({{ $customerStats->total() }} pelanggan)
            </h3>
            <span class="text-xs" style="color: var(--text-5)">
                Periode: {{ $start->format('d/m/Y') }} - {{ $end->format('d/m/Y') }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="border-b" style="border-color: var(--border-1);">
                    <tr>
                        <th class="px-4 py-2 text-center text-[9px] font-bold uppercase" style="color: var(--text-5)">#</th>
                        <th class="px-4 py-2 text-left text-[9px] font-bold uppercase" style="color: var(--text-5)">Nama</th>
                        <th class="px-4 py-2 text-left text-[9px] font-bold uppercase" style="color: var(--text-5)">Email</th>
                        <th class="px-4 py-2 text-center text-[9px] font-bold uppercase" style="color: var(--text-5)">Order</th>
                        <th class="px-4 py-2 text-right text-[9px] font-bold uppercase" style="color: var(--text-5)">Total Pembayaran</th>
                        <th class="px-4 py-2 text-right text-[9px] font-bold uppercase" style="color: var(--text-5)">Tanggal Daftar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customerStats as $customer)
                        @php
                            $orderCount = $customer->orders_count ?? 0;
                            $aov        = $orderCount > 0 ? round(($customer->lifetime_revenue ?? 0) / $orderCount) : 0;
                        @endphp
                        <tr class="border-b last:border-0" style="border-color: var(--border-1);">
                            <td class="px-4 py-2 text-center">
                                <span class="text-xs" style="color: var(--text-4)">
                                    {{ $customerStats->perPage() * ($customerStats->currentPage() - 1) + $loop->iteration }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full flex-shrink-0 flex items-center justify-center"
                                         style="background: var(--bg-elevated);">
                                        <span class="text-xs font-bold" style="color: var(--gold-bright)">
                                            {{ strtoupper(substr($customer->name ?? '?', 0, 1)) }}
                                        </span>
                                    </div>
                                    <p class="text-xs font-semibold" style="color: var(--text-1)">{{ $customer->name ?? '-' }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-2">
                                <p class="text-xs" style="color: var(--text-4)">{{ $customer->email ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <span class="text-xs font-bold" style="color: var(--text-1)">{{ number_format($orderCount) }}</span>
                            </td>
                            <td class="px-4 py-2 text-right">
                                <span class="text-xs font-bold" style="color: var(--gold-bright)">
                                    Rp {{ number_format($customer->lifetime_revenue ?? 0, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-right">
                                <span class="text-xs" style="color: var(--text-4)">{{ $customer->created_at?->format('d/m/Y') ?? '-' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-[11px]" style="color: var(--text-5)">
                                Tidak ada pelanggan ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($customerStats->hasPages())
            <div class="px-5 py-3 border-t" style="border-color: var(--border-1);">
                {{ $customerStats->appends(request()->except('page'))->links() }}
            </div>
        @endif
    </div>

</div>

@endsection
