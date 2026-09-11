@extends('layouts.admin')

@section('title', 'Voucher Promo')
@section('page-title', 'Voucher Promo')

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
                    <iconify-icon icon="mdi:ticket-percent-outline" class="text-slate-900 text-2xl"></iconify-icon>
                </span>
                Voucher Promo
            </h1>
            <p class="text-sm mt-1.5 ml-12" style="color: var(--text-5)">
                Kelola kupon diskon transaksi dan kode promo toko.
            </p>
        </div>

        <a href="{{ route('admin.vouchers.create') }}"
           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg
                  text-sm font-bold transition-all active:scale-95 flex-shrink-0
                  bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                  text-slate-900
                  shadow-lg shadow-amber-500/20
                  hover:shadow-xl hover:shadow-amber-500/40
                  hover:-translate-y-0.5">
            <iconify-icon icon="mdi:plus-circle-outline" class="text-lg"></iconify-icon>
            Tambah Voucher
        </a>
    </div>


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
    {{-- STATS CARDS --}}
    {{-- ============================================ --}}
    @php
        $totalVouchers = $vouchers->total();
        $activeVouchers = $vouchers->filter(fn($v) => $v->is_active && $v->end_date >= now() && $v->start_date <= now())->count();
        $expiredVouchers = $vouchers->filter(fn($v) => $v->end_date < now())->count();
        $totalUsage = $vouchers->sum('usages_count');
    @endphp

    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="rounded-xl border p-4 text-center transition-colors"
             style="background: var(--bg-card); border-color: var(--border-2);"
             onmouseover="this.style.borderColor='rgba(236,188,66,0.3)'"
             onmouseout="this.style.borderColor='var(--border-2)'">
            <p class="text-2xl font-bold" style="color: var(--text-1);">{{ $totalVouchers }}</p>
            <p class="text-[11px] font-semibold uppercase tracking-wider mt-1" style="color: var(--text-5);">Total</p>
        </div>
        <div class="rounded-xl border p-4 text-center transition-colors"
             style="background: var(--bg-card); border-color: var(--border-2);"
             onmouseover="this.style.borderColor='rgba(52,211,153,0.3)'"
             onmouseout="this.style.borderColor='var(--border-2)'">
            <p class="text-2xl font-bold text-emerald-400">{{ $activeVouchers }}</p>
            <p class="text-[11px] font-semibold uppercase tracking-wider mt-1 text-emerald-400/80">Aktif</p>
        </div>
        <div class="rounded-xl border p-4 text-center transition-colors"
             style="background: var(--bg-card); border-color: var(--border-2);"
             onmouseover="this.style.borderColor='rgba(248,113,113,0.3)'"
             onmouseout="this.style.borderColor='var(--border-2)'">
            <p class="text-2xl font-bold text-red-400">{{ $expiredVouchers }}</p>
            <p class="text-[11px] font-semibold uppercase tracking-wider mt-1 text-red-400/80">Kadaluarsa</p>
        </div>
        <div class="rounded-xl border p-4 text-center transition-colors"
             style="background: var(--bg-card); border-color: var(--border-2);"
             onmouseover="this.style.borderColor='rgba(236,188,66,0.3)'"
             onmouseout="this.style.borderColor='var(--border-2)'">
            <p class="text-2xl font-bold" style="color: #ecbc42;">{{ $totalUsage }}</p>
            <p class="text-[11px] font-semibold uppercase tracking-wider mt-1" style="color: rgba(236,188,66,0.8);">Total Dipakai</p>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- FILTER & SEARCH --}}
    {{-- ============================================ --}}
    <div class="flex flex-col lg:flex-row lg:items-center gap-3">

        {{-- Filter Buttons --}}
        <div class="flex flex-wrap gap-2 flex-1">
            @php
                $filters = [
                    ['value' => null,        'label' => 'Semua'],
                    ['value' => 'active',    'label' => 'Aktif'],
                    ['value' => 'upcoming',  'label' => 'Akan Datang'],
                    ['value' => 'expired',   'label' => 'Kadaluarsa'],
                    ['value' => 'inactive',  'label' => 'Nonaktif'],
                ];
            @endphp

            @foreach($filters as $filter)
                @php
                    $isActive = request('status') == $filter['value'] || ($filter['value'] === null && !request('status'));
                    $url = $filter['value']
                        ? route('admin.vouchers.index', ['status' => $filter['value']])
                        : route('admin.vouchers.index');
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
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <input type="text"
                   name="search"
                   placeholder="Cari kode atau nama voucher..."
                   value="{{ request('search') }}"
                   class="form-input"
                   style="padding: 0.4rem 0.85rem; font-size: 0.8rem; min-width: 240px;">

            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg
                           text-xs font-bold transition-all active:scale-95
                           bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                           text-slate-900
                           hover:shadow-lg hover:shadow-amber-500/30">
                <iconify-icon icon="mdi:magnify"></iconify-icon>
                Cari
            </button>
        </form>
    </div>


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
                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Voucher & Kode
                        </th>
                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Potongan
                        </th>
                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Min. Belanja
                        </th>
                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Penggunaan
                        </th>
                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Periode
                        </th>
                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Status
                        </th>
                        <th class="px-4 py-4 text-right text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Aksi
                        </th>
                    </tr>
                </thead>

                {{-- Table Body --}}
                <tbody>
                    @forelse ($vouchers as $voucher)
                        @php
                            $now = now();
                            $isExpired = $voucher->end_date < $now;
                            $isUpcoming = $voucher->start_date > $now;
                        @endphp
                        <tr class="transition-colors border-b last:border-0"
                            style="border-color: var(--border-1)"
                            onmouseover="this.style.background='var(--bg-hover)'"
                            onmouseout="this.style.background='transparent'">

                            {{-- Voucher & Kode --}}
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                                                bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                                                shadow-md shadow-amber-500/20">
                                        <iconify-icon icon="mdi:ticket-percent-outline" class="text-slate-900 text-lg"></iconify-icon>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold truncate" style="color: var(--text-1)">
                                            {{ $voucher->name }}
                                        </p>
                                        <span class="inline-block mt-0.5 font-mono text-[10px] font-bold px-2 py-0.5 rounded
                                                     bg-amber-500/10 text-amber-400 border border-amber-500/30 tracking-wider">
                                            {{ $voucher->code }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            {{-- Potongan --}}
                            <td class="px-4 py-4">
                                @if ($voucher->is_free_shipping)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold
                                                 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400">
                                        <iconify-icon icon="mdi:truck-fast-outline"></iconify-icon>
                                        Gratis Ongkir
                                    </span>
                                @elseif ($voucher->discount_type === 'fixed')
                                    <span class="text-sm font-bold" style="color: var(--text-1)">
                                        Rp {{ number_format($voucher->discount_value, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-sm font-bold" style="color: var(--text-1)">
                                        {{ (float) $voucher->discount_value }}%
                                    </span>
                                    @if ($voucher->max_discount_amount)
                                        <p class="text-[10px] mt-0.5" style="color: var(--text-5)">
                                            Maks. Rp {{ number_format($voucher->max_discount_amount, 0, ',', '.') }}
                                        </p>
                                    @endif
                                @endif
                            </td>

                            {{-- Min. Belanja --}}
                            <td class="px-4 py-4">
                                <span class="text-sm font-semibold" style="color: var(--text-2)">
                                    Rp {{ number_format($voucher->min_transaction_amount, 0, ',', '.') }}
                                </span>
                            </td>

                            {{-- Penggunaan --}}
                            <td class="px-4 py-4">
                                @if($voucher->is_for_all_users)
                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold" style="color: #ecbc42;">
                                        <iconify-icon icon="mdi:account-group-outline"></iconify-icon>
                                        Semua User
                                    </span>
                                @else
                                    <div class="flex items-center gap-1.5">
                                        <div class="flex-1 h-1.5 rounded-full overflow-hidden" style="background: var(--bg-input);">
                                            @php
                                                $percentage = $voucher->usage_limit
                                                    ? min(100, ($voucher->usages_count / $voucher->usage_limit) * 100)
                                                    : 0;
                                            @endphp
                                            <div class="h-full rounded-full bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]"
                                                 style="width: {{ $percentage }}%;"></div>
                                        </div>
                                        <span class="text-[11px] font-mono font-bold" style="color: var(--text-3);">
                                            {{ $voucher->usages_count }}/{{ $voucher->usage_limit ?? '∞' }}
                                        </span>
                                    </div>
                                @endif
                            </td>

                            {{-- Periode --}}
                            <td class="px-4 py-4">
                                <p class="text-xs font-semibold" style="color: var(--text-3);">
                                    {{ $voucher->start_date->format('d M Y') }}
                                </p>
                                <p class="text-[10px]" style="color: var(--text-5);">
                                    s/d {{ $voucher->end_date->format('d M Y') }}
                                </p>
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-4">
                                @if (!$voucher->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border"
                                          style="background: rgba(148,163,184,0.1); border-color: rgba(148,163,184,0.3); color: var(--text-4);">
                                        <span class="w-1.5 h-1.5 rounded-full" style="background: var(--text-5);"></span>
                                        Nonaktif
                                    </span>
                                @elseif ($isExpired)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border"
                                          style="background: rgba(248,113,113,0.1); border-color: rgba(248,113,113,0.3); color: #f87171;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                        Kadaluarsa
                                    </span>
                                @elseif ($isUpcoming)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border"
                                          style="background: rgba(251,191,36,0.1); border-color: rgba(251,191,36,0.3); color: #fbbf24;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                        Akan Datang
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border"
                                          style="background: rgba(52,211,153,0.1); border-color: rgba(52,211,153,0.3); color: #34d399;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                        Aktif
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.vouchers.edit', $voucher->id) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                              text-xs font-semibold border transition-all active:scale-95"
                                       style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)"
                                       onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                                       onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'"
                                       title="Edit">
                                        <iconify-icon icon="mdi:pencil-outline"></iconify-icon>
                                    </a>

                                    <form action="{{ route('admin.vouchers.destroy', $voucher->id) }}"
                                          method="POST"
                                          class="inline"
                                          onsubmit="return confirm('Hapus voucher {{ $voucher->code }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                                       text-xs font-semibold border transition-all active:scale-95
                                                       bg-red-500/5 border-red-500/20 text-red-400
                                                       hover:bg-red-500/10 hover:border-red-500/40 hover:text-red-300"
                                                title="Hapus">
                                            <iconify-icon icon="mdi:delete-outline"></iconify-icon>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-20">
                                <div class="flex flex-col items-center justify-center text-center max-w-sm mx-auto">
                                    <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4 border"
                                         style="background: var(--bg-input); border-color: var(--border-2)">
                                        <iconify-icon icon="mdi:ticket-percent-outline" class="text-2xl" style="color: var(--text-6)"></iconify-icon>
                                    </div>
                                    <p class="text-sm font-semibold mb-1" style="color: var(--text-3)">
                                        Belum ada voucher
                                    </p>
                                    <p class="text-xs mb-4" style="color: var(--text-5)">
                                        Buat voucher pertama Anda untuk mulai memberikan diskon ke pelanggan.
                                    </p>
                                    <a href="{{ route('admin.vouchers.create') }}"
                                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg
                                              text-xs font-bold transition-all active:scale-95
                                              bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                                              text-slate-900
                                              hover:shadow-lg hover:shadow-amber-500/30">
                                        <iconify-icon icon="mdi:plus-circle-outline"></iconify-icon>
                                        Buat Voucher
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($vouchers->hasPages())
            <div class="border-t px-6 py-4"
                 style="border-color: var(--border-2); background: var(--bg-input)">
                {{ $vouchers->links() }}
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

@endsection