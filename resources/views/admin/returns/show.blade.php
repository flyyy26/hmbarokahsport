@extends('layouts.admin')

@section('title', 'Retur #' . $order->order_number . ' - Admin E-Commerce')
@section('page-title', 'Detail Retur')

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
            <div class="flex flex-wrap items-center gap-3 text-xs font-semibold">
                <a href="{{ route('admin.returns.index') }}"
                   class="inline-flex items-center gap-1.5 transition-colors"
                   style="color: var(--text-5)"
                   onmouseover="this.style.color='#FDDD57'"
                   onmouseout="this.style.color='var(--text-5)'">
                    <iconify-icon icon="mdi:arrow-left"></iconify-icon>
                    Kembali ke Retur
                </a>
                <span style="color: var(--text-6)">·</span>
                <a href="{{ route('admin.orders.show', $order) }}"
                   class="inline-flex items-center gap-1.5 transition-colors"
                   style="color: var(--text-5)"
                   onmouseover="this.style.color='#FDDD57'"
                   onmouseout="this.style.color='var(--text-5)'">
                    <iconify-icon icon="mdi:receipt-text-outline"></iconify-icon>
                    Pesanan #{{ $order->order_number }}
                </a>
            </div>

            <h1 class="text-2xl font-bold flex items-center gap-2.5 mt-2" style="color: var(--text-1)">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl
                             bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                             shadow-lg shadow-amber-500/20 flex-shrink-0">
                    <iconify-icon icon="mdi:backup-restore" class="text-slate-900 text-2xl"></iconify-icon>
                </span>
                Detail Retur
            </h1>
        </div>

        {{-- Status Badge --}}
        @php
            $badgeMap = [
                'pending'   => '#fbbf24',
                'approved'  => '#60a5fa',
                'completed' => '#34d399',
                'rejected'  => '#f87171',
            ];
            $sc = $badgeMap[$order->return_status] ?? '#94a3b8';
        @endphp

        <div class="flex-shrink-0">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border"
                  style="background: {{ $sc }}15; border-color: {{ $sc }}40; color: {{ $sc }};">
                <span class="w-1.5 h-1.5 rounded-full" style="background: currentColor;"></span>
                {{ $order->return_status_label }}
            </span>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- INFORMASI RETUR --}}
    {{-- ============================================ --}}
    <div class="rounded-2xl border overflow-hidden"
         style="background: var(--bg-card); border-color: var(--border-2);">

        <div class="px-5 py-4 border-b flex items-center gap-2"
             style="background: var(--bg-input); border-color: var(--border-2);">
            <iconify-icon icon="mdi:information-outline" class="text-[#ecbc42] text-base"></iconify-icon>
            <h2 class="font-bold text-sm" style="color: var(--text-1);">Informasi Retur</h2>
        </div>

        <div class="p-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">

                {{-- Pelanggan --}}
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider mb-1" style="color: var(--text-5);">
                        Pelanggan
                    </p>
                    <p class="font-semibold" style="color: var(--text-1);">
                        {{ $order->user->name ?? '-' }}
                    </p>
                </div>

                {{-- Nomor HP --}}
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider mb-1" style="color: var(--text-5);">
                        Nomor HP
                    </p>
                    <p class="font-semibold break-all" style="color: var(--text-1);">
                        {{ $order->user->phone ?? '-' }}
                    </p>
                </div>

                {{-- Alasan Retur --}}
                <div class="sm:col-span-2">
                    <p class="text-[10px] font-bold uppercase tracking-wider mb-1" style="color: var(--text-5);">
                        Alasan Retur
                    </p>
                    <div class="rounded-lg p-3 border"
                         style="background: var(--bg-input); border-color: var(--border-2);">
                        <p class="text-sm leading-relaxed" style="color: var(--text-3);">
                            {{ $order->return_reason ?? '-' }}
                        </p>
                    </div>
                </div>

                {{-- Tanggal Permintaan --}}
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider mb-1" style="color: var(--text-5);">
                        Tanggal Permintaan
                    </p>
                    <p class="font-semibold" style="color: var(--text-1);">
                        {{ $order->return_requested_at?->format('d M Y, H:i') ?? '-' }}
                    </p>
                </div>

                @if($order->return_processed_at)
                    {{-- Tanggal Diproses --}}
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider mb-1" style="color: var(--text-5);">
                            Tanggal Diproses
                        </p>
                        <p class="font-semibold" style="color: var(--text-1);">
                            {{ $order->return_processed_at->format('d M Y, H:i') }}
                        </p>
                    </div>

                    {{-- Diproses Oleh --}}
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider mb-1" style="color: var(--text-5);">
                            Diproses Oleh
                        </p>
                        <p class="font-semibold" style="color: var(--text-1);">
                            {{ $order->returnedByAdmin->name ?? '-' }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- PRODUK RETUR --}}
    {{-- ============================================ --}}
    <div class="rounded-2xl border overflow-hidden"
         style="background: var(--bg-card); border-color: var(--border-2);">

        <div class="px-5 py-4 border-b flex items-center gap-2"
             style="background: var(--bg-input); border-color: var(--border-2);">
            <iconify-icon icon="mdi:package-variant-closed" class="text-[#ecbc42] text-base"></iconify-icon>
            <h2 class="font-bold text-sm" style="color: var(--text-1);">Produk Retur</h2>
        </div>

        @if($order->return_status === 'approved')
            {{-- ============================================ --}}
            {{-- FORM: KEMBALIKAN STOK --}}
            {{-- ============================================ --}}
            <form action="{{ route('admin.returns.restore', $order) }}" method="POST">

                <div class="px-5 py-3 border-b"
                     style="background: {{ $sc }}10; border-color: var(--border-1);">
                    <p class="text-xs flex items-start gap-2" style="color: {{ $sc }};">
                        <iconify-icon icon="mdi:information-outline" class="text-base flex-shrink-0 mt-0.5"></iconify-icon>
                        <span>
                            Pilih jumlah barang yang ingin dikembalikan ke stok.
                            Jika tidak diubah, semua barang akan dikembalikan.
                        </span>
                    </p>
                </div>

                @csrf

                <div class="p-5">
                    <div class="space-y-3">
                        @foreach ($order->items as $item)
                            <div class="flex flex-col sm:flex-row sm:items-center gap-3 pb-3 border-b last:border-0 last:pb-0"
                                 style="border-color: var(--border-1);">

                                <div class="flex items-center gap-3 min-w-0 flex-1">
                                    <div class="w-12 h-12 rounded-lg overflow-hidden border flex-shrink-0"
                                         style="background: var(--bg-input); border-color: var(--border-2);">
                                        @if ($item->product && $item->product->images->first())
                                            <img src="{{ Storage::url($item->product->images->first()->image) }}"
                                                 alt="{{ $item->product_name }}"
                                                 class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full items-center justify-center">
                                                <iconify-icon icon="mdi:image-off-outline" class="text-lg" style="color: var(--text-6);"></iconify-icon>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold truncate" style="color: var(--text-1);">
                                            {{ $item->product_name }}
                                        </p>
                                        @if ($item->variant_name)
                                            <p class="text-[11px]" style="color: var(--text-5);">
                                                Varian: {{ $item->variant_name }}
                                            </p>
                                        @endif
                                        <p class="text-[11px]" style="color: var(--text-5);">
                                            Qty Beli: {{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 flex-shrink-0">
                                    {{-- Input Qty --}}
                                    <div>
                                        <label class="block text-[10px] font-bold uppercase tracking-wider mb-1" style="color: var(--text-5);">
                                            Kembalikan
                                        </label>
                                        <input type="number"
                                               name="items[{{ $item->id }}]"
                                               value="{{ $item->quantity }}"
                                               min="0"
                                               max="{{ $item->quantity }}"
                                               class="form-input"
                                               style="width: 90px; padding: 0.5rem 0.75rem; text-align: center; font-weight: 600;">
                                    </div>

                                    {{-- Subtotal --}}
                                    <div class="text-right min-w-[100px]">
                                        <p class="text-[10px] font-bold uppercase tracking-wider mb-1" style="color: var(--text-5);">
                                            Subtotal
                                        </p>
                                        <p class="text-sm font-bold" style="color: var(--text-1);">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="px-5 pb-5 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                    <a href="{{ route('admin.returns.index') }}"
                       class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg
                              text-sm font-semibold transition-all active:scale-95"
                       style="background: var(--bg-input); border: 1px solid var(--border-2); color: var(--text-3);"
                       onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                       onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'">
                        <iconify-icon icon="mdi:close"></iconify-icon>
                        Batal
                    </a>

                    <button type="submit"
                            onclick="return confirm('Kembalikan stok untuk pesanan #{{ $order->order_number }}?\n\nBarang akan dikembalikan ke gudang dan status retur akan diselesaikan.')"
                            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-lg
                                   text-sm font-bold transition-all active:scale-95
                                   bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                                   text-slate-900
                                   shadow-lg shadow-amber-500/20
                                   hover:shadow-xl hover:shadow-amber-500/40">
                        <iconify-icon icon="mdi:package-variant"></iconify-icon>
                        Kembalikan ke Stok
                    </button>
                </div>
            </form>
        @else
            {{-- ============================================ --}}
            {{-- VIEW ONLY --}}
            {{-- ============================================ --}}
            <div class="p-5">
                <div class="space-y-3">
                    @foreach ($order->items as $item)
                        <div class="flex items-center gap-3 pb-3 border-b last:border-0 last:pb-0"
                             style="border-color: var(--border-1);">

                            <div class="w-12 h-12 rounded-lg overflow-hidden border flex-shrink-0"
                                 style="background: var(--bg-input); border-color: var(--border-2);">
                                @if ($item->product && $item->product->images->first())
                                    <img src="{{ Storage::url($item->product->images->first()->image) }}"
                                         alt="{{ $item->product_name }}"
                                         class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full items-center justify-center">
                                        <iconify-icon icon="mdi:image-off-outline" class="text-lg" style="color: var(--text-6);"></iconify-icon>
                                    </div>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold truncate" style="color: var(--text-1);">
                                    {{ $item->product_name }}
                                </p>
                                @if ($item->variant_name)
                                    <p class="text-[11px]" style="color: var(--text-5);">
                                        Varian: {{ $item->variant_name }}
                                    </p>
                                @endif
                                <p class="text-[11px]" style="color: var(--text-5);">
                                    Qty: {{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}
                                </p>
                            </div>

                            <p class="text-sm font-bold flex-shrink-0" style="color: var(--text-1);">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </p>
                        </div>
                    @endforeach
                </div>

                {{-- Back Button --}}
                <div class="mt-5 pt-4 border-t" style="border-color: var(--border-1);">
                    <a href="{{ route('admin.returns.index') }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg
                              text-xs font-semibold transition-all active:scale-95"
                       style="background: var(--bg-input); border: 1px solid var(--border-2); color: var(--text-3);"
                       onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                       onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'">
                        <iconify-icon icon="mdi:arrow-left"></iconify-icon>
                        Kembali ke Daftar Retur
                    </a>
                </div>
            </div>
        @endif
    </div>


    {{-- ============================================ --}}
    {{-- ACTION: PENDING STATUS --}}
    {{-- ============================================ --}}
    @if($order->return_status === 'pending')
        <div class="rounded-2xl border overflow-hidden"
             style="background: var(--bg-card); border-color: var(--border-2);">

            <div class="px-5 py-4 border-b flex items-center gap-2"
                 style="background: linear-gradient(90deg, rgba(251, 191, 36, 0.15) 0%, transparent 100%); border-color: var(--border-2);">
                <iconify-icon icon="mdi:alert-outline" class="text-amber-400 text-base"></iconify-icon>
                <h2 class="font-bold text-sm" style="color: var(--text-1);">Persetujuan Retur</h2>
            </div>

            <div class="p-5">
                <p class="text-xs mb-5" style="color: var(--text-5);">
                    Setujui permintaan retur ini atau tolak jika tidak memenuhi syarat.
                    Stok tidak akan dikembalikan otomatis — gunakan form di atas setelah barang diterima.
                </p>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                    <form action="{{ route('admin.orders.reject-return', $order) }}" method="POST">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('Tolak permintaan retur pesanan #{{ $order->order_number }}?')"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-5 py-2.5 rounded-lg
                                       text-xs font-bold transition-all active:scale-95
                                       bg-red-500/15 border border-red-500/40 text-red-300
                                       hover:bg-red-500/25">
                            <iconify-icon icon="mdi:close"></iconify-icon>
                            Tolak Retur
                        </button>
                    </form>

                    <form action="{{ route('admin.orders.approve-return', $order) }}" method="POST">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('Setujui permintaan retur pesanan #{{ $order->order_number }}?')"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-6 py-2.5 rounded-lg
                                       text-xs font-bold transition-all active:scale-95
                                       bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                                       text-slate-900
                                       shadow-lg shadow-amber-500/20
                                       hover:shadow-xl hover:shadow-amber-500/40">
                            <iconify-icon icon="mdi:check"></iconify-icon>
                            Setujui Retur
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

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
</style>

@endsection