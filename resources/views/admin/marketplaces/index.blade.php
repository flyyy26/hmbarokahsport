@extends('layouts.admin')

@section('title', 'Marketplace')
@section('page-title', 'Marketplace')

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
                    <iconify-icon icon="mdi:shopping-outline" class="text-slate-900 text-2xl"></iconify-icon>
                </span>
                Marketplace
            </h1>
            <p class="text-sm mt-1.5 ml-12" style="color: var(--text-5)">
                Kelola marketplace tempat pelanggan dapat membeli produk toko.
            </p>
        </div>

        <a href="{{ route('admin.marketplaces.create') }}"
           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg
                  text-sm font-bold transition-all active:scale-95 flex-shrink-0
                  bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                  text-slate-900
                  shadow-lg shadow-amber-500/20
                  hover:shadow-xl hover:shadow-amber-500/40
                  hover:-translate-y-0.5">
            <iconify-icon icon="mdi:plus-circle-outline" class="text-lg"></iconify-icon>
            Tambah Marketplace
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

    @if ($errors->any())
        <div class="flex items-start gap-3 rounded-xl px-4 py-3
                    bg-red-500/10 border border-red-500/30 text-red-400">
            <iconify-icon icon="mdi:alert-circle-outline" class="text-xl flex-shrink-0 mt-0.5"></iconify-icon>
            <div class="text-sm">
                <p class="font-bold mb-1">Terjadi kesalahan:</p>
                <ul class="list-disc list-inside space-y-0.5 text-red-300">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif


    {{-- ============================================ --}}
    {{-- STATS CARDS --}}
    {{-- ============================================ --}}
    @php
        $items = $marketplaces instanceof \Illuminate\Pagination\LengthAwarePaginator
              || $marketplaces instanceof \Illuminate\Pagination\Paginator
                ? $marketplaces->getCollection()
                : $marketplaces;

        $totalMarketplaces = $items->count();
        $activeMarketplaces = $items->where('is_active', true)->count();
        $inactiveMarketplaces = $items->where('is_active', false)->count();
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="rounded-xl border p-4 flex items-center gap-3 transition-colors"
             style="background: var(--bg-card); border-color: var(--border-2);"
             onmouseover="this.style.borderColor='rgba(236,188,66,0.3)'"
             onmouseout="this.style.borderColor='var(--border-2)'">
            <div class="w-11 h-11 rounded-lg flex items-center justify-center flex-shrink-0
                        bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                        shadow-md shadow-amber-500/20">
                <iconify-icon icon="mdi:store-outline" class="text-slate-900 text-xl"></iconify-icon>
            </div>
            <div>
                <p class="text-2xl font-bold" style="color: var(--text-1);">{{ $totalMarketplaces }}</p>
                <p class="text-[11px] font-semibold uppercase tracking-wider" style="color: var(--text-5);">Total</p>
            </div>
        </div>

        <div class="rounded-xl border p-4 flex items-center gap-3 transition-colors"
             style="background: var(--bg-card); border-color: var(--border-2);"
             onmouseover="this.style.borderColor='rgba(52,211,153,0.3)'"
             onmouseout="this.style.borderColor='var(--border-2)'">
            <div class="w-11 h-11 rounded-lg flex items-center justify-center flex-shrink-0
                        bg-emerald-500/10 border border-emerald-500/30">
                <iconify-icon icon="mdi:check-circle-outline" class="text-emerald-400 text-xl"></iconify-icon>
            </div>
            <div>
                <p class="text-2xl font-bold text-emerald-400">{{ $activeMarketplaces }}</p>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-emerald-400/80">Aktif</p>
            </div>
        </div>

        <div class="rounded-xl border p-4 flex items-center gap-3 transition-colors"
             style="background: var(--bg-card); border-color: var(--border-2);"
             onmouseover="this.style.borderColor='rgba(148,163,184,0.3)'"
             onmouseout="this.style.borderColor='var(--border-2)'">
            <div class="w-11 h-11 rounded-lg flex items-center justify-center flex-shrink-0"
                 style="background: rgba(148,163,184,0.1); border: 1px solid rgba(148,163,184,0.3);">
                <iconify-icon icon="mdi:close-circle-outline" class="text-xl" style="color: var(--text-4);"></iconify-icon>
            </div>
            <div>
                <p class="text-2xl font-bold" style="color: var(--text-4);">{{ $inactiveMarketplaces }}</p>
                <p class="text-[11px] font-semibold uppercase tracking-wider" style="color: var(--text-5);">Nonaktif</p>
            </div>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- TABLE CARD --}}
    {{-- ============================================ --}}
    <div class="overflow-hidden rounded-xl border"
         style="background: var(--bg-card); border-color: var(--border-2)">

        {{-- Header --}}
        <div class="px-5 py-4 border-b flex items-center gap-2"
             style="background: var(--bg-input); border-color: var(--border-2)">
            <iconify-icon icon="mdi:format-list-bulleted" class="text-[#ecbc42] text-base"></iconify-icon>
            <h2 class="font-bold text-sm flex-1" style="color: var(--text-1)">Daftar Marketplace</h2>
            <span class="text-[10px] font-mono px-2 py-0.5 rounded-full"
                  style="background: rgba(236,188,66,0.1); color: #ecbc42;">
                {{ $marketplaces->count() }} marketplace
            </span>
        </div>

        @if ($marketplaces->isEmpty())

            {{-- EMPTY STATE --}}
            <div class="px-6 py-20">
                <div class="flex flex-col items-center justify-center text-center max-w-sm mx-auto">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4 border"
                         style="background: var(--bg-input); border-color: var(--border-2)">
                        <iconify-icon icon="mdi:store-off-outline" class="text-2xl" style="color: var(--text-6)"></iconify-icon>
                    </div>
                    <p class="text-sm font-semibold mb-1" style="color: var(--text-3)">
                        Belum ada marketplace
                    </p>
                    <p class="text-xs mb-4" style="color: var(--text-5)">
                        Tambahkan marketplace tempat pelanggan dapat membeli produk toko.
                    </p>
                    <a href="{{ route('admin.marketplaces.create') }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg
                              text-xs font-bold transition-all active:scale-95
                              bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                              text-slate-900
                              hover:shadow-lg hover:shadow-amber-500/30">
                        <iconify-icon icon="mdi:plus-circle-outline"></iconify-icon>
                        Tambah Marketplace
                    </a>
                </div>
            </div>

        @else

            {{-- TABLE --}}
            <div class="overflow-x-auto">
                <table class="min-w-full">

                    {{-- Table Header --}}
                    <thead class="border-b"
                           style="background: var(--bg-input); border-color: var(--border-2)">
                        <tr>
                            <th class="px-4 py-4 text-center text-[10px] font-bold uppercase tracking-wider w-20" style="color: var(--text-5)">Icon</th>
                            <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">Marketplace</th>
                            <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">URL</th>
                            <th class="px-4 py-4 text-center text-[10px] font-bold uppercase tracking-wider w-24" style="color: var(--text-5)">Urutan</th>
                            <th class="px-4 py-4 text-center text-[10px] font-bold uppercase tracking-wider w-32" style="color: var(--text-5)">Status</th>
                            <th class="px-4 py-4 text-right text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">Aksi</th>
                        </tr>
                    </thead>

                    {{-- Table Body --}}
                    <tbody>
                        @foreach ($marketplaces as $marketplace)
                            <tr class="transition-colors border-b last:border-0"
                                style="border-color: var(--border-1)"
                                onmouseover="this.style.background='var(--bg-hover)'"
                                onmouseout="this.style.background='transparent'">

                                {{-- Icon --}}
                                <td class="px-4 py-4 text-center">
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl border
                                                bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                                                shadow-md shadow-amber-500/20"
                                         style="border-color: rgba(236,188,66,0.3);">
                                        <iconify-icon icon="{{ $marketplace->icon }}"
                                                      width="24" height="24"
                                                      class="text-slate-900"></iconify-icon>
                                    </div>
                                </td>

                                {{-- Name --}}
                                <td class="px-4 py-4">
                                    <p class="text-sm font-semibold" style="color: var(--text-1);">
                                        {{ $marketplace->name }}
                                    </p>
                                    <p class="text-[10px] mt-0.5 font-mono" style="color: var(--text-5);">
                                        {{ $marketplace->slug }}
                                    </p>
                                </td>

                                {{-- URL --}}
                                <td class="px-4 py-4">
                                    <a href="{{ $marketplace->url }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="inline-flex max-w-[300px] items-center gap-1.5 truncate
                                              text-sm font-medium transition-colors"
                                       style="color: #ecbc42;"
                                       onmouseover="this.style.color='#FDDD57'"
                                       onmouseout="this.style.color='#ecbc42'">
                                        <iconify-icon icon="mdi:link-variant" class="flex-shrink-0"></iconify-icon>
                                        <span class="truncate">{{ $marketplace->url }}</span>
                                        <iconify-icon icon="mdi:arrow-top-right" class="flex-shrink-0 text-xs"></iconify-icon>
                                    </a>
                                </td>

                                {{-- Sort Order --}}
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                                 font-mono text-xs font-bold border"
                                          style="background: rgba(236,188,66,0.1); border-color: rgba(236,188,66,0.3); color: #ecbc42;">
                                        {{ $marketplace->sort_order }}
                                    </span>
                                </td>

                                {{-- Status --}}
                                <td class="px-4 py-4 text-center">
                                    @if ($marketplace->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                                     text-[11px] font-bold border"
                                              style="background: rgba(52,211,153,0.1); border-color: rgba(52,211,153,0.3); color: #34d399;">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                                     text-[11px] font-bold border"
                                              style="background: rgba(148,163,184,0.1); border-color: rgba(148,163,184,0.3); color: var(--text-4);">
                                            <span class="w-1.5 h-1.5 rounded-full" style="background: var(--text-5);"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                {{-- Action --}}
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-end gap-1.5">

                                        {{-- Edit --}}
                                        <a href="{{ route('admin.marketplaces.edit', $marketplace) }}"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                                  text-xs font-semibold border transition-all active:scale-95"
                                           style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)"
                                           onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                                           onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'"
                                           title="Edit">
                                            <iconify-icon icon="mdi:pencil-outline"></iconify-icon>
                                        </a>

                                        {{-- Delete --}}
                                        <form action="{{ route('admin.marketplaces.destroy', $marketplace) }}"
                                              method="POST"
                                              class="inline"
                                              onsubmit="return confirm('Yakin ingin menghapus marketplace {{ addslashes($marketplace->name) }}?')">
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
                        @endforeach
                    </tbody>
                </table>
            </div>

        @endif
    </div>

</div>

@endsection