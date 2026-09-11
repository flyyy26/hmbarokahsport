@extends('layouts.admin')

@section('content')

<div class="w-full max-w-5xl mx-auto space-y-6">

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
            <a href="{{ route('admin.orders.index') }}"
               class="inline-flex items-center gap-1.5 text-xs font-semibold transition-colors"
               style="color: var(--text-5)"
               onmouseover="this.style.color='#FDDD57'"
               onmouseout="this.style.color='var(--text-5)'">
                <iconify-icon icon="mdi:arrow-left"></iconify-icon>
                Kembali ke Pesanan
            </a>
            <h1 class="text-2xl font-bold flex items-center gap-2.5 mt-2" style="color: var(--text-1)">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl
                             bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                             shadow-lg shadow-amber-500/20 flex-shrink-0">
                    <iconify-icon icon="mdi:receipt-text-outline" class="text-slate-900 text-2xl"></iconify-icon>
                </span>
                Detail Pesanan
            </h1>
            <p class="text-sm mt-1 ml-12 font-mono" style="color: var(--text-5)">
                #{{ $order->order_number }}
            </p>
        </div>

        <div class="flex flex-wrap gap-2 flex-shrink-0">
            <a href="{{ route('admin.orders.label', $order) }}"
               target="_blank"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg
                      text-sm font-semibold transition-all active:scale-95 border"
               style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)"
               onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
               onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'">
                <iconify-icon icon="mdi:tag-outline"></iconify-icon>
                Print Label
            </a>

            <a href="{{ route('admin.orders.tracking', $order) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg
                      text-sm font-bold transition-all active:scale-95
                      bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                      text-slate-900
                      shadow-lg shadow-amber-500/20
                      hover:shadow-xl hover:shadow-amber-500/40">
                <iconify-icon icon="mdi:truck-fast-outline"></iconify-icon>
                Tracking
            </a>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- STATUS SUMMARY (Ringkas) --}}
    {{-- ============================================ --}}
    @php
        $badgeMap = [
            'pending'    => '#fbbf24',
            'processing' => '#60a5fa',
            'shipped'    => '#a78bfa',
            'delivered'  => '#34d399',
            'cancelled'  => '#f87171',
            'paid'       => '#34d399',
            'unpaid'     => '#fb923c',
            'failed'     => '#f87171',
            'approved'   => '#60a5fa',
            'rejected'   => '#f87171',
            'completed'  => '#34d399',
        ];
        $statusItems = [
            ['label' => 'Pesanan',   'value' => $order->shipping_status, 'text' => $order->shipping_status_label],
            ['label' => 'Pembayaran','value' => $order->payment_status,  'text' => $order->payment_status_label],
        ];
        if ($order->return_status) {
            $statusItems[] = ['label' => 'Retur', 'value' => $order->return_status, 'text' => $order->return_status_label];
        }
    @endphp

    <div class="flex flex-wrap items-center gap-2">
        @foreach($statusItems as $item)
            @php $c = $badgeMap[$item['value']] ?? '#94a3b8'; @endphp
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border"
                  style="background: {{ $c }}15; border-color: {{ $c }}40; color: {{ $c }};">
                <span class="w-1.5 h-1.5 rounded-full" style="background: currentColor;"></span>
                <span class="opacity-70">{{ $item['label'] }}:</span>
                {{ $item['text'] }}
            </span>
        @endforeach
    </div>


    {{-- ============================================ --}}
    {{-- STATUS ALERTS --}}
    {{-- ============================================ --}}

    {{-- Cancellation Pending --}}
    @if($order->cancellation_status === 'pending')
        <div class="rounded-xl border p-4 bg-amber-500/10 border-amber-500/30">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-sm font-bold flex items-center gap-1.5 text-amber-400">
                        <iconify-icon icon="mdi:clock-outline"></iconify-icon>
                        Permintaan Pembatalan
                    </p>
                    <p class="text-sm text-amber-300 mt-1">{{ $order->cancellation_reason }}</p>
                    <p class="text-xs text-amber-400/70 mt-1">
                        Diminta: {{ $order->cancellation_requested_at->format('d M Y, H:i') }}
                    </p>
                </div>
                <div class="flex gap-2 flex-shrink-0">
                    <form action="{{ route('admin.orders.approve-cancellation', $order) }}" method="POST">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('Setujui pembatalan pesanan #{{ $order->order_number }}?')"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg
                                       text-xs font-bold transition-all active:scale-95
                                       bg-emerald-500/20 border border-emerald-500/40 text-emerald-300
                                       hover:bg-emerald-500/30">
                            <iconify-icon icon="mdi:check"></iconify-icon>
                            Setujui
                        </button>
                    </form>
                    <form action="{{ route('admin.orders.reject-cancellation', $order) }}" method="POST">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('Tolak permintaan pembatalan?')"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg
                                       text-xs font-bold transition-all active:scale-95
                                       bg-red-500/20 border border-red-500/40 text-red-300
                                       hover:bg-red-500/30">
                            <iconify-icon icon="mdi:close"></iconify-icon>
                            Tolak
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Order Cancelled --}}
    @if($order->shipping_status === 'cancelled')
        <div class="rounded-xl border p-4 bg-red-500/10 border-red-500/30">
            <p class="text-sm font-bold flex items-center gap-1.5 text-red-400">
                <iconify-icon icon="mdi:close-circle-outline"></iconify-icon>
                Pesanan Dibatalkan
            </p>
            @if($order->cancellation_reason)
                <p class="text-sm text-red-300 mt-1">
                    <strong>Alasan:</strong> {{ $order->cancellation_reason }}
                </p>
            @endif
        </div>
    @endif

    {{-- Return Pending --}}
    @if($order->return_status === 'pending')
        <div class="rounded-xl border p-4 bg-amber-500/10 border-amber-500/30">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-sm font-bold flex items-center gap-1.5 text-amber-400">
                        <iconify-icon icon="mdi:clock-outline"></iconify-icon>
                        Permintaan Retur
                    </p>
                    @if($order->return_reason)
                        <p class="text-sm text-amber-300 mt-1">
                            <strong>Alasan:</strong> {{ $order->return_reason }}
                        </p>
                    @endif
                </div>
                <div class="flex gap-2 flex-shrink-0">
                    <form action="{{ route('admin.orders.approve-return', $order) }}" method="POST">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('Setujui permintaan retur?')"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg
                                       text-xs font-bold transition-all active:scale-95
                                       bg-blue-500/20 border border-blue-500/40 text-blue-300
                                       hover:bg-blue-500/30">
                            <iconify-icon icon="mdi:check"></iconify-icon>
                            Setujui
                        </button>
                    </form>
                    <form action="{{ route('admin.orders.reject-return', $order) }}" method="POST">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('Tolak permintaan retur?')"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg
                                       text-xs font-bold transition-all active:scale-95
                                       bg-red-500/20 border border-red-500/40 text-red-300
                                       hover:bg-red-500/30">
                            <iconify-icon icon="mdi:close"></iconify-icon>
                            Tolak
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Return Approved --}}
    @if($order->return_status === 'approved')
        <div class="rounded-xl border p-4 bg-blue-500/10 border-blue-500/30 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <p class="text-sm font-bold flex items-center gap-1.5 text-blue-400">
                <iconify-icon icon="mdi:check-circle-outline"></iconify-icon>
                Retur Disetujui
            </p>
            <a href="{{ route('admin.returns.show', $order) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold
                      bg-blue-500/20 border border-blue-500/40 text-blue-300 hover:bg-blue-500/30
                      transition-all active:scale-95 flex-shrink-0">
                <iconify-icon icon="mdi:package-variant"></iconify-icon>
                Lihat Retur
            </a>
        </div>
    @endif


    {{-- ============================================ --}}
    {{-- ⭐ PRIORITAS UTAMA: ATUR PENGIRIMAN --}}
    {{-- ============================================ --}}
    <div class="rounded-2xl border-2 overflow-hidden"
         style="background: var(--bg-card); border-color: #ecbc42;">

        {{-- Header dengan Highlight --}}
        <div class="px-5 py-4 border-b flex items-center gap-3"
             style="background: linear-gradient(90deg, rgba(236, 188, 66, 0.15) 0%, transparent 100%); border-color: var(--border-2);">

            <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl
                         bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                         shadow-lg shadow-amber-500/30 flex-shrink-0">
                <iconify-icon icon="mdi:truck-delivery-outline" class="text-slate-900 text-xl"></iconify-icon>
            </span>

            <div class="flex-1 min-w-0">
                <h2 class="text-base font-bold flex items-center gap-2" style="color: var(--text-1)">
                    Atur Pengiriman
                </h2>
                <p class="text-[11px]" style="color: var(--text-5)">
                    Update nomor resi dan status pengiriman pesanan ini.
                </p>
            </div>
        </div>

        {{-- Form Body --}}
        <form action="{{ route('admin.orders.shipping', $order) }}"
              method="POST"
              class="p-5">

            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Kurir (Disabled) --}}
                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:package-variant-closed" class="text-[#ecbc42]"></iconify-icon>
                        Kurir
                        <span class="text-[10px] font-normal normal-case ml-1" style="color: var(--text-6)">
                            (dari sistem)
                        </span>
                    </label>
                    <input type="text"
                           name="courier"
                           value="{{ $order->courier }}"
                           disabled
                           readonly
                           class="form-input form-input-disabled">
                    <input type="hidden" name="courier" value="{{ $order->courier }}">
                </div>

                {{-- Layanan (Disabled) --}}
                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:layers-outline" class="text-[#ecbc42]"></iconify-icon>
                        Layanan
                        <span class="text-[10px] font-normal normal-case ml-1" style="color: var(--text-6)">
                            (dari sistem)
                        </span>
                    </label>
                    <input type="text"
                           name="service"
                           value="{{ $order->service }}"
                           disabled
                           readonly
                           class="form-input form-input-disabled">
                    <input type="hidden" name="service" value="{{ $order->service }}">
                </div>

                {{-- Nomor Resi (Editable) --}}
                <div class="sm:col-span-2">
                    <label class="form-label">
                        <iconify-icon icon="mdi:barcode-scan" class="text-[#ecbc42]"></iconify-icon>
                        No. Resi
                        <span class="text-[10px] font-normal normal-case ml-1" style="color: var(--text-6)">
                            (opsional)
                        </span>
                    </label>
                    <input type="text"
                           name="tracking_number"
                           value="{{ $order->tracking_number }}"
                           placeholder="Contoh: JNE1234567890"
                           class="form-input">
                    <p class="text-[10px] mt-1" style="color: var(--text-5)">
                        Kosongkan jika ingin menggunakan resi otomatis dari Biteship.
                    </p>
                </div>

                {{-- Status Pengiriman (Editable) --}}
                <div class="sm:col-span-2">
                    <label class="form-label">
                        <iconify-icon icon="mdi:state-machine" class="text-[#ecbc42]"></iconify-icon>
                        Status Pengiriman
                    </label>
                    <select name="shipping_status" class="form-input">
                        @php
                            $defaultShippingStatus = $order->shipping_status;
                            if ($order->payment_status === 'paid' && $order->shipping_status === 'pending') {
                                $defaultShippingStatus = 'processing';
                            }
                        @endphp
                        <option value="pending" {{ $defaultShippingStatus == 'pending' ? 'selected' : '' }}>
                            Belum Bayar
                        </option>
                        <option value="processing" {{ $defaultShippingStatus == 'processing' ? 'selected' : '' }}>
                            Sedang Dikemas
                        </option>
                        <option value="shipped" {{ $defaultShippingStatus == 'shipped' ? 'selected' : '' }}>
                            Dikirim
                        </option>
                        <option value="delivered" {{ $defaultShippingStatus == 'delivered' ? 'selected' : '' }}>
                            Terkirim
                        </option>
                    </select>
                    <p class="text-[10px] mt-1" style="color: var(--text-5)">
                        @if($order->shipping_status == 'pending') Menunggu pembayaran.
                        @elseif($order->shipping_status == 'processing') Sedang disiapkan untuk dikirim.
                        @elseif($order->shipping_status == 'shipped') Paket dalam perjalanan.
                        @elseif($order->shipping_status == 'delivered') Paket telah diterima.
                        @endif
                    </p>
                </div>
            </div>

            {{-- Timestamps --}}
            @if ($order->shipped_at || $order->delivered_at)
                <div class="mt-4 pt-4 border-t flex flex-wrap gap-4 text-xs"
                     style="border-color: var(--border-1); color: var(--text-5)">
                    @if ($order->shipped_at)
                        <span>
                            <iconify-icon icon="mdi:truck-fast-outline" class="inline"></iconify-icon>
                            Dikirim: <strong style="color: var(--text-3)">{{ $order->shipped_at->format('d M Y, H:i') }}</strong>
                        </span>
                    @endif
                    @if ($order->delivered_at)
                        <span>
                            <iconify-icon icon="mdi:check-circle-outline" class="inline"></iconify-icon>
                            Terkirim: <strong style="color: var(--text-3)">{{ $order->delivered_at->format('d M Y, H:i') }}</strong>
                        </span>
                    @endif
                </div>
            @endif

            {{-- Submit Button --}}
            <div class="mt-5 pt-4 border-t" style="border-color: var(--border-2)">
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg
                               text-sm font-bold transition-all active:scale-95
                               bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                               text-slate-900
                               shadow-lg shadow-amber-500/30
                               hover:shadow-xl hover:shadow-amber-500/50
                               hover:-translate-y-0.5">
                    <iconify-icon icon="mdi:content-save-outline" class="text-lg"></iconify-icon>
                    Simpan Pengiriman
                </button>
            </div>
        </form>
    </div>


    {{-- ============================================ --}}
    {{-- INFO SEKUNDER: DUA KOLOM --}}
    {{-- ============================================ --}}
    <div class="grid gap-5 lg:grid-cols-3">

        {{-- ============================================ --}}
        {{-- LEFT: Item Pesanan --}}
        {{-- ============================================ --}}
        <div class="lg:col-span-2">
            <div class="rounded-2xl border overflow-hidden"
                 style="background: var(--bg-card); border-color: var(--border-2)">

                <div class="px-5 py-4 border-b flex items-center gap-2"
                     style="background: var(--bg-input); border-color: var(--border-2)">
                    <iconify-icon icon="mdi:shopping-outline" class="text-[#ecbc42] text-base"></iconify-icon>
                    <h2 class="font-bold text-sm" style="color: var(--text-1)">Item Pesanan</h2>
                    <span class="ml-auto text-[10px] font-mono" style="color: var(--text-5)">
                        {{ $order->items->count() }} item
                    </span>
                </div>

                <div class="p-5">
                    <div class="space-y-3">
                        @foreach ($order->items as $item)
                            <div class="flex items-center justify-between gap-3 pb-3 border-b last:border-0 last:pb-0"
                                 style="border-color: var(--border-1)">
                                <div class="flex items-center gap-3 min-w-0 flex-1">
                                    <div class="w-12 h-12 rounded-lg overflow-hidden border flex-shrink-0"
                                         style="background: var(--bg-input); border-color: var(--border-2)">
                                        @if ($item->product && $item->product->images->first())
                                            <img src="{{ Storage::url($item->product->images->first()->image) }}"
                                                 alt="{{ $item->product_name }}"
                                                 class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full items-center justify-center">
                                                <iconify-icon icon="mdi:image-off-outline" class="text-lg" style="color: var(--text-6)"></iconify-icon>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold truncate" style="color: var(--text-1)">
                                            {{ $item->product_name }}
                                        </p>
                                        @if ($item->variant_name)
                                            <p class="text-[11px]" style="color: var(--text-5)">
                                                Varian: {{ $item->variant_name }}
                                            </p>
                                        @endif
                                        <p class="text-[11px]" style="color: var(--text-5)">
                                            {{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                                <p class="text-sm font-bold flex-shrink-0" style="color: var(--text-1)">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </p>
                            </div>
                        @endforeach
                    </div>

                    {{-- Summary --}}
                    <div class="mt-5 pt-4 space-y-2 border-t" style="border-color: var(--border-2)">

                        <div class="flex justify-between text-sm">
                            <span style="color: var(--text-4)">Subtotal</span>
                            <span class="font-semibold" style="color: var(--text-3)">
                                Rp {{ number_format($order->subtotal, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex justify-between text-sm">
                            <span style="color: var(--text-4)">Ongkir</span>
                            <span class="font-semibold" style="color: var(--text-3)">
                                Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}
                            </span>
                        </div>

                        @if ($order->discount > 0)
                            <div class="flex justify-between text-sm">
                                <span style="color: var(--text-4)">Diskon</span>
                                <span class="font-semibold text-red-400">
                                    −Rp {{ number_format($order->discount, 0, ',', '.') }}
                                </span>
                            </div>
                        @endif

                        <div class="flex justify-between items-center pt-3 border-t"
                             style="border-color: var(--border-2)">
                            <span class="text-sm font-bold" style="color: var(--text-1)">Total</span>
                            <span class="text-lg font-bold" style="color: #ecbc42;">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- ============================================ --}}
        {{-- RIGHT: Alamat + Info User --}}
        {{-- ============================================ --}}
        <div class="space-y-5">

            {{-- Alamat Pengiriman --}}
            <div class="rounded-2xl border overflow-hidden"
                 style="background: var(--bg-card); border-color: var(--border-2)">

                <div class="px-5 py-4 border-b flex items-center gap-2"
                     style="background: var(--bg-input); border-color: var(--border-2)">
                    <iconify-icon icon="mdi:map-marker-outline" class="text-[#ecbc42] text-base"></iconify-icon>
                    <h2 class="font-bold text-sm" style="color: var(--text-1)">Alamat Pengiriman</h2>
                </div>

                <div class="p-5 space-y-2">
                    <p class="font-bold text-sm" style="color: var(--text-1)">
                        {{ $order->shipping_name }}
                    </p>
                    <p class="text-xs" style="color: var(--text-4)">
                        {{ $order->shipping_phone }}
                    </p>
                    <p class="text-xs mt-3 leading-relaxed" style="color: var(--text-4)">
                        {{ $order->shipping_address }}<br>
                        {{ $order->shipping_city }}, {{ $order->shipping_province }}<br>
                        {{ $order->shipping_postal_code }}
                    </p>

                    @if ($order->notes)
                        <div class="mt-4 pt-3 border-t" style="border-color: var(--border-1)">
                            <p class="text-[10px] font-bold uppercase tracking-wider mb-1" style="color: var(--text-5)">
                                Catatan User
                            </p>
                            <p class="text-xs" style="color: var(--text-4)">
                                {{ $order->notes }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>


            {{-- Informasi User --}}
            <div class="rounded-2xl border overflow-hidden"
                 style="background: var(--bg-card); border-color: var(--border-2)">

                <div class="px-5 py-4 border-b flex items-center gap-2"
                     style="background: var(--bg-input); border-color: var(--border-2)">
                    <iconify-icon icon="mdi:account-outline" class="text-[#ecbc42] text-base"></iconify-icon>
                    <h2 class="font-bold text-sm" style="color: var(--text-1)">Informasi User</h2>
                </div>

                <div class="p-5 space-y-3 text-sm">

                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider mb-1" style="color: var(--text-5)">
                            Nama
                        </p>
                        <p class="font-semibold" style="color: var(--text-1)">
                            {{ $order->user->name ?? 'Guest' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider mb-1" style="color: var(--text-5)">
                            Email
                        </p>
                        <p class="font-semibold break-all" style="color: var(--text-1)">
                            {{ $order->user->email ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider mb-1" style="color: var(--text-5)">
                            Telepon
                        </p>
                        <p class="font-semibold" style="color: var(--text-1)">
                            {{ $order->user->phone ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider mb-1" style="color: var(--text-5)">
                            Tanggal Order
                        </p>
                        <p class="font-semibold" style="color: var(--text-1)">
                            {{ $order->created_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                </div>
            </div>

        </div>
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
        font-size: 0.85rem;
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
    .form-input-disabled {
        background: var(--bg-hover) !important;
        color: var(--text-5) !important;
        cursor: not-allowed;
        opacity: 0.75;
    }
    .form-label {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-3);
        margin-bottom: 0.4rem;
    }
</style>

@endsection