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
                    <iconify-icon icon="mdi:receipt-text-outline" class="text-slate-900 text-2xl"></iconify-icon>
                </span>
                Pesanan
            </h1>
            <p class="text-sm mt-1.5 ml-12" style="color: var(--text-5)">
                Kelola semua pesanan user.
            </p>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- FILTER TABS + BADGE + SEARCH --}}
    {{-- ============================================ --}}
    <div class="flex flex-col lg:flex-row lg:items-center gap-3">

        {{-- Filter Tabs dengan Badge --}}
        <div class="flex flex-wrap gap-2 flex-1">

            @php
                $filters = [
                    [
                        'value'  => null,
                        'label'  => 'Semua',
                        'count'  => $statusCounts['total'] ?? 0,
                        'color'  => null,
                    ],
                    [
                        'value'  => 'pending',
                        'label'  => 'Menunggu',
                        'count'  => $statusCounts['pending'] ?? 0,
                        'color'  => '#fbbf24',
                    ],
                    [
                        'value'  => 'processing',
                        'label'  => 'Diproses',
                        'count'  => $statusCounts['processing'] ?? 0,
                        'color'  => '#60a5fa',
                    ],
                    [
                        'value'  => 'shipped',
                        'label'  => 'Dikirim',
                        'count'  => $statusCounts['shipped'] ?? 0,
                        'color'  => '#a78bfa',
                    ],
                    [
                        'value'  => 'delivered',
                        'label'  => 'Selesai',
                        'count'  => $statusCounts['delivered'] ?? 0,
                        'color'  => '#34d399',
                    ],
                    [
                        'value'  => 'cancelled',
                        'label'  => 'Dibatalkan',
                        'count'  => $statusCounts['cancelled'] ?? 0,
                        'color'  => '#f87171',
                    ],
                    [
                        'value'  => 'cancellation_requested',
                        'label'  => 'Permintaan Pembatalan',
                        'count'  => $statusCounts['cancellation_requested'] ?? 0,
                        'color'  => '#f97316',
                        'highlight' => true,
                    ],
                    [
                        'value'  => 'unpaid',
                        'label'  => 'Belum Bayar',
                        'count'  => $statusCounts['unpaid'] ?? 0,
                        'color'  => '#fb923c',
                        'payment_filter' => true,
                    ],
                ];
            @endphp

            @foreach($filters as $filter)
                @php
                    // Cek active state
                    if (isset($filter['payment_filter']) && $filter['payment_filter']) {
                        // Filter payment_status (bukan shipping_status)
                        $isActive = request('payment_status') === $filter['value'];
                        $url = route('admin.orders.index', ['payment_status' => $filter['value']]);
                    } else {
                        $isActive = request('shipping_status') == $filter['value']
                                || ($filter['value'] === null && !request('shipping_status') && !request('payment_status'));
                        $url = $filter['value']
                            ? route('admin.orders.index', ['shipping_status' => $filter['value']])
                            : route('admin.orders.index');
                    }

                    $count = $filter['count'] ?? 0;
                    $color = $filter['color'];
                    $isHighlight = $filter['highlight'] ?? false;
                    $showBadge = $count > 0;
                @endphp

                <a href="{{ $url }}"
                   class="group relative inline-flex items-center gap-2
                          px-3.5 py-2 text-xs font-semibold rounded-lg border
                          transition-all active:scale-95"
                   @if($isActive)
                        style="background: {{ $isHighlight ? '#f97316' : '#ecbc42' }};
                               color: {{ $isHighlight ? '#ffffff' : '#422006' }};
                               border-color: {{ $isHighlight ? '#f97316' : '#ecbc42' }};"
                   @elseif($isHighlight && $count > 0)
                        style="background: rgba(249,115,22,0.1);
                               color: #f97316;
                               border-color: rgba(249,115,22,0.3);"
                        onmouseover="this.style.background='rgba(249,115,22,0.2)'; this.style.borderColor='#f97316'"
                        onmouseout="this.style.background='rgba(249,115,22,0.1)'; this.style.borderColor='rgba(249,115,22,0.3)'"
                   @else
                        style="background: var(--bg-input); color: var(--text-4); border-color: var(--border-2);"
                        onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                        onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-4)'"
                   @endif>

                    <span>{{ $filter['label'] }}</span>

                    {{-- 🔥 BADGE COUNTER --}}
                    @if($showBadge)
                        @php
                            // Warna badge menyesuaikan status
                            if ($isActive) {
                                // Kalau tab aktif, badge putih transparan
                                $badgeStyle = 'background: rgba(255,255,255,0.25); color: inherit;';
                            } elseif ($isHighlight) {
                                // Untuk tab highlight (permintaan pembatalan)
                                $badgeStyle = 'background: #ef4444; color: #ffffff;';
                            } elseif ($color) {
                                // Warna badge sesuai status
                                $badgeStyle = "background: {$color}20; color: {$color}; border: 1px solid {$color}40;";
                            } else {
                                $badgeStyle = 'background: rgba(236,188,66,0.15); color: #ecbc42;';
                            }
                        @endphp

                        <span class="inline-flex items-center justify-center
                                     min-w-[20px] h-5 px-1.5
                                     text-[10px] font-bold
                                     rounded-full
                                     {{ $isHighlight && !$isActive ? 'animate-pulse' : '' }}"
                              style="{{ $badgeStyle }}">
                            {{ $count > 99 ? '99+' : $count }}
                        </span>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- Search --}}
        <form method="GET" class="flex gap-2">
            @if(request('shipping_status'))
                <input type="hidden" name="shipping_status" value="{{ request('shipping_status') }}">
            @endif
            @if(request('payment_status'))
                <input type="hidden" name="payment_status" value="{{ request('payment_status') }}">
            @endif

            <input type="text"
                   name="search"
                   placeholder="Cari order atau user..."
                   value="{{ request('search') }}"
                   class="form-input"
                   style="padding: 0.4rem 0.85rem; font-size: 0.8rem; min-width: 240px;">

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


    {{-- ============================================ --}}
    {{-- BULK ACTIONS BAR --}}
    {{-- ============================================ --}}
    @if(in_array(request('shipping_status'), ['processing', 'shipped']))
        <div id="bulk-actions-bar" class="hidden rounded-xl border px-4 py-3"
             style="background: var(--bg-card); border-color: #ecbc42;">

            <div class="flex flex-wrap items-center justify-between gap-3">

                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold" style="color: var(--text-3)">
                        <span id="selected-count">0</span> pesanan dipilih
                    </span>
                </div>

                <div class="flex gap-2">
                    @if(request('shipping_status') === 'processing')
                        <button type="button"
                                id="btn-bulk-ship"
                                disabled
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg
                                       text-xs font-bold transition-all active:scale-95
                                       bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                                       text-slate-900
                                       hover:shadow-lg hover:shadow-amber-500/30
                                       disabled:opacity-50 disabled:cursor-not-allowed">
                            <iconify-icon icon="mdi:truck-fast-outline"></iconify-icon>
                            Kirim Produk
                        </button>
                    @endif

                    @if(request('shipping_status') === 'shipped')
                        <button type="button"
                                id="btn-bulk-print"
                                disabled
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg
                                       text-xs font-bold transition-all active:scale-95
                                       bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                                       text-slate-900
                                       hover:shadow-lg hover:shadow-amber-500/30
                                       disabled:opacity-50 disabled:cursor-not-allowed">
                            <iconify-icon icon="mdi:printer-outline"></iconify-icon>
                            Print Label
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif


    {{-- ============================================ --}}
    {{-- TABLE --}}
    {{-- ============================================ --}}
    <div class="overflow-hidden rounded-xl border"
         style="background: var(--bg-card); border-color: var(--border-2)">

        <div class="overflow-x-auto">
            <table class="min-w-full">

                {{-- Table Header --}}
                <thead class="border-b"
                       style="background: var(--bg-input); border-color: var(--border-2)">
                    <tr>
                        @if(in_array(request('shipping_status'), ['processing', 'shipped']))
                            <th class="px-4 py-4 w-12">
                                <input type="checkbox"
                                       id="select-all-header"
                                       class="w-4 h-4 rounded cursor-pointer"
                                       style="accent-color: #ecbc42;">
                            </th>
                        @else
                            <th class="px-4 py-4 w-12"></th>
                        @endif

                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            No. Order
                        </th>
                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            User
                        </th>
                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Total
                        </th>
                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Status
                        </th>
                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Pembayaran
                        </th>
                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Tanggal
                        </th>
                        <th class="px-4 py-4 text-right text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Aksi
                        </th>
                    </tr>
                </thead>

                {{-- Table Body --}}
                <tbody>
                    @forelse ($orders as $order)
                        <tr class="transition-colors border-b last:border-0"
                            style="border-color: var(--border-1)"
                            onmouseover="this.style.background='var(--bg-hover)'"
                            onmouseout="this.style.background='transparent'">

                            {{-- Checkbox --}}
                            <td class="px-4 py-3.5">
                                @if(in_array(request('shipping_status'), ['processing', 'shipped']))
                                    <input type="checkbox"
                                           name="selected_orders[]"
                                           value="{{ $order->id }}"
                                           class="order-checkbox w-4 h-4 rounded cursor-pointer"
                                           style="accent-color: #ecbc42;">
                                @endif
                            </td>

                            {{-- No. Order --}}
                            <td class="px-4 py-3.5">
                                <span class="font-mono text-xs font-bold" style="color: var(--text-1)">
                                    {{ $order->order_number }}
                                </span>
                            </td>

                            {{-- User --}}
                            <td class="px-4 py-3.5">
                                <p class="text-sm font-semibold truncate max-w-[200px]" style="color: var(--text-1)">
                                    {{ $order->user->name ?? 'Guest' }}
                                </p>
                                <p class="text-[10px] truncate max-w-[200px]" style="color: var(--text-5)">
                                    {{ $order->user->email ?? '-' }}
                                </p>
                            </td>

                            {{-- Total --}}
                            <td class="px-4 py-3.5">
                                <span class="text-sm font-bold" style="color: var(--text-1)">
                                    Rp {{ number_format($order->total, 0, ',', '.') }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-3.5">
                                <div class="flex flex-col gap-1.5">

                                    {{-- 🔥 Badge Permintaan Pembatalan (PRIORITAS) --}}
                                    @if($order->cancellation_status === 'pending')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border
                                                    w-fit animate-pulse"
                                            style="background: rgba(249,115,22,0.15); border-color: rgba(249,115,22,0.4); color: #f97316;">
                                            <iconify-icon icon="mdi:clock-alert-outline" class="text-sm"></iconify-icon>
                                            Minta Pembatalan
                                        </span>
                                    @endif

                                    {{-- Shipping Status Badge --}}
                                    @php
                                        $shippingMap = [
                                            'pending'    => ['text' => '#fbbf24', 'dot' => '#f59e0b'],
                                            'processing' => ['text' => '#60a5fa', 'dot' => '#3b82f6'],
                                            'shipped'    => ['text' => '#a78bfa', 'dot' => '#8b5cf6'],
                                            'delivered'  => ['text' => '#34d399', 'dot' => '#10b981'],
                                            'cancelled'  => ['text' => '#f87171', 'dot' => '#ef4444'],
                                        ];
                                        $sc = $shippingMap[$order->shipping_status] ?? ['text' => 'var(--text-4)', 'dot' => 'var(--text-5)'];
                                    @endphp

                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border w-fit"
                                        style="background: {{ $sc['text'] }}15; border-color: {{ $sc['text'] }}40; color: {{ $sc['text'] }};">
                                        <span class="w-1.5 h-1.5 rounded-full" style="background: {{ $sc['dot'] }};"></span>
                                        {{ $order->shipping_status_label }}
                                    </span>
                                </div>
                            </td>

                            {{-- Payment --}}
                            <td class="px-4 py-3.5">
                                @php
                                    $paymentMap = [
                                        'paid'    => ['text' => '#34d399', 'dot' => '#10b981'],
                                        'unpaid'  => ['text' => '#fb923c', 'dot' => '#f97316'],
                                        'failed'  => ['text' => '#f87171', 'dot' => '#ef4444'],
                                    ];
                                    $pc = $paymentMap[$order->payment_status] ?? ['text' => 'var(--text-4)', 'dot' => 'var(--text-5)'];
                                @endphp

                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border"
                                      style="background: {{ $pc['text'] }}15; border-color: {{ $pc['text'] }}40; color: {{ $pc['text'] }};">
                                    <span class="w-1.5 h-1.5 rounded-full" style="background: {{ $pc['dot'] }};"></span>
                                    {{ $order->payment_status_label }}
                                </span>
                            </td>

                            {{-- Tanggal --}}
                            <td class="px-4 py-3.5">
                                <p class="text-xs font-semibold" style="color: var(--text-3)">
                                    {{ $order->created_at->format('d M Y') }}
                                </p>
                                <p class="text-[10px]" style="color: var(--text-5)">
                                    {{ $order->created_at->format('H:i') }}
                                </p>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">

                                    {{-- 🔥 Tombol Cepat Approve/Reject Pembatalan --}}
                                    @if($order->cancellation_status === 'pending')
                                        <form action="{{ route('admin.orders.approve-cancellation', $order) }}"
                                            method="POST"
                                            class="inline"
                                            onsubmit="return confirm('Setujui pembatalan pesanan #{{ $order->order_number }}?')">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                                        text-xs font-semibold border transition-all active:scale-95
                                                        bg-emerald-500/5 border-emerald-500/20 text-emerald-400
                                                        hover:bg-emerald-500/15 hover:border-emerald-500/40"
                                                    title="Setujui Pembatalan">
                                                <iconify-icon icon="mdi:check"></iconify-icon>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.orders.reject-cancellation', $order) }}"
                                            method="POST"
                                            class="inline"
                                            onsubmit="return confirm('Tolak permintaan pembatalan?')">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                                        text-xs font-semibold border transition-all active:scale-95
                                                        bg-red-500/5 border-red-500/20 text-red-400
                                                        hover:bg-red-500/15 hover:border-red-500/40"
                                                    title="Tolak Pembatalan">
                                                <iconify-icon icon="mdi:close"></iconify-icon>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Tombol Detail --}}
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                                            text-xs font-semibold border transition-all active:scale-95"
                                    style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)"
                                    onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                                    onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'">
                                        Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-20">
                                <div class="flex flex-col items-center justify-center text-center max-w-sm mx-auto">
                                    <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4 border"
                                         style="background: var(--bg-input); border-color: var(--border-2)">
                                        <iconify-icon icon="mdi:receipt-text-outline" class="text-2xl" style="color: var(--text-6)"></iconify-icon>
                                    </div>
                                    <p class="text-sm" style="color: var(--text-5)">
                                        Belum ada pesanan.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($orders->hasPages())
            <div class="border-t px-6 py-4"
                 style="border-color: var(--border-2); background: var(--bg-input)">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>

{{-- ============================================ --}}
{{-- STYLES --}}
{{-- ============================================ --}}
<style>
    .form-input {
        width: 100%;
        padding: 0.7rem 1rem;
        background: var(--bg-input);
        border: 1px solid var(--border-2);
        border-radius: 0.65rem;
        font-size: 0.875rem;
        color: var(--text-1);
        transition: all 0.2s ease;
        font-family: inherit;
        outline: none;
    }
    .form-input:focus {
        border-color: #ecbc42;
        box-shadow: 0 0 0 3px rgba(236, 188, 66, 0.15);
    }
    .form-input::placeholder {
        color: var(--text-6);
    }
</style>

{{-- ============================================ --}}
{{-- BULK ACTIONS SCRIPT --}}
{{-- ============================================ --}}
@if(in_array(request('shipping_status'), ['processing', 'shipped']))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const bulkActionsBar = document.getElementById('bulk-actions-bar');
            const selectAllHeader = document.getElementById('select-all-header');
            const selectAll = document.getElementById('select-all');
            const btnBulkShip = document.getElementById('btn-bulk-ship');
            const btnBulkPrint = document.getElementById('btn-bulk-print');
            const selectedCountEl = document.getElementById('selected-count');
            const checkboxes = document.querySelectorAll('.order-checkbox');

            function updateSelectedCount() {
                const count = document.querySelectorAll('.order-checkbox:checked').length;
                if (selectedCountEl) selectedCountEl.textContent = count;
                if (bulkActionsBar) bulkActionsBar.classList.toggle('hidden', count === 0);
                if (btnBulkShip) btnBulkShip.disabled = count === 0;
                if (btnBulkPrint) btnBulkPrint.disabled = count === 0;
            }

            if (selectAllHeader) {
                selectAllHeader.addEventListener('change', function () {
                    if (selectAll) selectAll.checked = this.checked;
                    checkboxes.forEach(cb => cb.checked = this.checked);
                    updateSelectedCount();
                });
            }

            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    checkboxes.forEach(cb => cb.checked = this.checked);
                    updateSelectedCount();
                });
            }

            checkboxes.forEach(cb => cb.addEventListener('change', updateSelectedCount));

            function getSelectedIds() {
                return Array.from(document.querySelectorAll('.order-checkbox:checked')).map(cb => cb.value);
            }

            if (btnBulkShip) {
                btnBulkShip.addEventListener('click', function () {
                    const ids = getSelectedIds();
                    if (ids.length === 0) return;
                    if (!confirm('Kirim ' + ids.length + ' pesanan ini? Pastikan data pengiriman (kurir, layanan) sudah diisi di masing-masing pesanan.')) return;

                    const formData = new FormData();
                    ids.forEach(id => formData.append('order_ids[]', id));

                    fetch('{{ route('admin.orders.bulk-ship') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: formData,
                    })
                    .then(r => {
                        if (!r.ok) {
                            return r.text().then(text => { throw new Error('Server error: ' + r.status + ' ' + text.substring(0, 200)); });
                        }
                        return r.json();
                    })
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            if (data.errors && data.errors.length > 0) {
                                alert('Peringatan:\n' + data.errors.join('\n'));
                            }
                            location.reload();
                        } else {
                            alert('Gagal: ' + data.message);
                        }
                    })
                    .catch(err => alert('Error: ' + err.message));
                });
            }

            if (btnBulkPrint) {
                btnBulkPrint.addEventListener('click', function () {
                    const ids = getSelectedIds();
                    if (ids.length === 0) return;

                    const formData = new FormData();
                    ids.forEach(id => formData.append('order_ids[]', id));

                    fetch('{{ route('admin.orders.bulk-print-label') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: formData,
                    })
                    .then(r => {
                        const contentType = r.headers.get('content-type');
                        if (contentType && contentType.includes('application/pdf')) {
                            return r.blob().then(blob => {
                                const url = URL.createObjectURL(blob);
                                window.open(url, '_blank');
                                setTimeout(() => URL.revokeObjectURL(url), 60000);

                                checkboxes.forEach(cb => cb.checked = false);
                                if (selectAllHeader) selectAllHeader.checked = false;
                                if (selectAll) selectAll.checked = false;
                                updateSelectedCount();
                            });
                        }
                        if (!r.ok) {
                            return r.text().then(text => { throw new Error('Server error: ' + r.status + ' ' + text.substring(0, 200)); });
                        }
                        return r.json();
                    })
                    .then(data => {
                        if (!data) return;
                        if (data.success && data.labels && data.labels.length > 0) {
                            if (data.errors && data.errors.length > 0) {
                                alert('Peringatan:\n' + data.errors.join('\n'));
                            }
                            let html = '<!DOCTYPE html><html><head><title>Semua Label Pesanan</title>';
                            html += '<style>body{margin:0;padding:20px;font-family:Arial;} .label-block{page-break-after:always;margin-bottom:0;padding-bottom:20px;} .label-title{font-size:14px;font-weight:bold;margin-bottom:5px;} object{border:1px solid #ccc;width:100%;height:400px;}</style>';
                            html += '</head><body>';
                            data.labels.forEach(function(label) {
                                html += '<div class="label-block">';
                                html += '<div class="label-title">' + label.order_number + '</div>';
                                if (label.pdf_base64) {
                                    html += '<object data="data:application/pdf;base64,' + label.pdf_base64 + '" type="application/pdf"></object>';
                                } else if (label.pdf_url) {
                                    html += '<object data="' + label.pdf_url + '" type="application/pdf"></object>';
                                }
                                html += '</div>';
                            });
                            html += '</body></html>';

                            const printWindow = window.open('', '_blank', 'width=800,height=600');
                            printWindow.document.write(html);
                            printWindow.document.close();
                            printWindow.focus();

                            let pollCount = 0;
                            const pollInterval = setInterval(function() {
                                pollCount++;
                                let allLoaded = true;
                                const objs = printWindow.document.querySelectorAll('object');
                                objs.forEach(function(obj) {
                                    try {
                                        if (obj.contentDocument && obj.contentDocument.readyState === 'complete') {
                                            // PDF loaded
                                        } else {
                                            allLoaded = false;
                                        }
                                    } catch (e) {
                                        allLoaded = false;
                                    }
                                });
                                if (allLoaded || pollCount >= 20) {
                                    clearInterval(pollInterval);
                                    setTimeout(function() {
                                        printWindow.print();
                                    }, 1000);
                                }
                            }, 500);
                        } else if (data) {
                            alert('Gagal: ' + (data.errors ? data.errors.join(', ') : data.message));
                        }
                    })
                    .catch(err => alert('Error: ' + err.message));
                });
            }

            updateSelectedCount();
        });
    </script>
@endif

@endsection