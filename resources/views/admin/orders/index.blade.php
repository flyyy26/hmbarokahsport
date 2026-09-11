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
    {{-- STATUS SUMMARY CARDS --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-7">

        @php
            $statusCards = [
                ['key' => 'total',      'label' => 'Total',       'color' => null],
                ['key' => 'pending',    'label' => 'Menunggu',    'color' => '#fbbf24'],
                ['key' => 'processing', 'label' => 'Diproses',    'color' => '#60a5fa'],
                ['key' => 'shipped',    'label' => 'Dikirim',     'color' => '#a78bfa'],
                ['key' => 'delivered',  'label' => 'Selesai',     'color' => '#34d399'],
                ['key' => 'cancelled',  'label' => 'Dibatalkan',  'color' => '#f87171'],
                ['key' => 'unpaid',     'label' => 'Belum Bayar', 'color' => '#fb923c'],
            ];
        @endphp

        @foreach($statusCards as $card)
            @php
                $count = $statusCounts[$card['key']] ?? 0;
                $hasColor = $card['color'] !== null;
            @endphp

            <div class="rounded-xl border p-4 text-center transition-colors"
                 style="background: var(--bg-card); border-color: var(--border-2);"
                 onmouseover="this.style.borderColor='rgba(236,188,66,0.3)'"
                 onmouseout="this.style.borderColor='var(--border-2)'">

                <p class="text-2xl font-bold" style="color: {{ $hasColor ? $card['color'] : 'var(--text-1)' }};">
                    {{ $count }}
                </p>
                <p class="text-[11px] font-semibold uppercase tracking-wider mt-1"
                   style="color: {{ $hasColor ? $card['color'] : 'var(--text-5)' }}; opacity: {{ $hasColor ? '0.8' : '1' }};">
                    {{ $card['label'] }}
                </p>
            </div>
        @endforeach
    </div>


    {{-- ============================================ --}}
    {{-- FILTER & SEARCH --}}
    {{-- ============================================ --}}
    <div class="flex flex-col lg:flex-row lg:items-center gap-3">

        {{-- Filter Buttons --}}
        <div class="flex flex-wrap gap-2 flex-1">

            @php
                $filters = [
                    ['value' => null,            'label' => 'Semua'],
                    ['value' => 'pending',       'label' => 'Menunggu'],
                    ['value' => 'processing',    'label' => 'Diproses'],
                    ['value' => 'shipped',       'label' => 'Dikirim'],
                    ['value' => 'delivered',     'label' => 'Selesai'],
                    ['value' => 'cancelled',     'label' => 'Dibatalkan'],
                ];
            @endphp

            @foreach($filters as $filter)
                @php
                    $isActive = request('shipping_status') == $filter['value'] || ($filter['value'] === null && !request('shipping_status'));
                    $url = $filter['value'] ? route('admin.orders.index', ['shipping_status' => $filter['value']]) : route('admin.orders.index');
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

        {{-- Search --}}
        <form method="GET" class="flex gap-2">
            @if(request('shipping_status'))
                <input type="hidden" name="shipping_status" value="{{ request('shipping_status') }}">
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

                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border"
                                      style="background: {{ $sc['text'] }}15; border-color: {{ $sc['text'] }}40; color: {{ $sc['text'] }};">
                                    <span class="w-1.5 h-1.5 rounded-full" style="background: {{ $sc['dot'] }};"></span>
                                    {{ $order->shipping_status_label }}
                                </span>
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
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                                          text-xs font-semibold border transition-all active:scale-95"
                                   style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)"
                                   onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                                   onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'">
                                    Detail
                                </a>
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