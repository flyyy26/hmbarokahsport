@extends('layouts.admin')

@section('title', 'Kategori')
@section('page-title', 'Kategori')

@section('content')

    <div class="w-full space-y-6">

        {{-- ============================================ --}}
        {{-- HEADER --}}
        {{-- ============================================ --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <h2 class="text-2xl font-bold flex items-center gap-2.5" style="color: var(--text-1)">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl
                                 bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                                 shadow-lg shadow-amber-500/20">
                        <iconify-icon icon="mdi:folder-multiple-outline" class="text-slate-900 text-2xl"></iconify-icon>
                    </span>
                    Kategori
                </h2>
                <p class="text-sm mt-1.5 ml-12" style="color: var(--text-5)">
                    Kelola kategori produk toko.
                </p>
            </div>

            <a href="{{ route('admin.categories.create') }}"
               class="inline-flex items-center justify-center gap-2
                      px-5 py-3 rounded-lg
                      bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                      text-slate-900 font-bold text-sm
                      shadow-lg shadow-amber-500/20
                      hover:shadow-xl hover:shadow-amber-500/40
                      hover:-translate-y-0.5
                      transition-all active:scale-95 active:translate-y-0">
                <iconify-icon icon="mdi:plus-circle" class="text-lg"></iconify-icon>
                Tambah Kategori
            </a>
        </div>


        {{-- ============================================ --}}
        {{-- ALERTS --}}
        {{-- ============================================ --}}
        @if(session('success'))
            <div class="flex items-start gap-3 px-4 py-3 rounded-xl
                        bg-emerald-500/10
                        border border-emerald-500/30
                        text-emerald-400">
                <iconify-icon icon="mdi:check-circle-outline" class="text-xl flex-shrink-0 mt-0.5"></iconify-icon>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold">Berhasil!</p>
                    <p class="text-xs opacity-80 mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="flex items-start gap-3 px-4 py-3 rounded-xl
                        bg-red-500/10
                        border border-red-500/30
                        text-red-400">
                <iconify-icon icon="mdi:alert-circle-outline" class="text-xl flex-shrink-0 mt-0.5"></iconify-icon>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold">Terjadi Kesalahan</p>
                    <p class="text-xs opacity-80 mt-0.5">{{ session('error') }}</p>
                </div>
            </div>
        @endif


        {{-- ============================================ --}}
        {{-- STATS BAR (opsional, tapi informatif) --}}
        {{-- ============================================ --}}
        @if($categories->total() > 0)
            <div class="flex items-center justify-between gap-4 px-4 py-3 rounded-xl
                        border"
                 style="background: var(--bg-card); border-color: var(--border-2)">

                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-9 h-9 rounded-lg
                                bg-[#ecbc42]/10 border border-[#ecbc42]/30">
                        <iconify-icon icon="mdi:folder-outline" class="text-[#ecbc42] text-lg"></iconify-icon>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--text-5)">
                            Total Kategori
                        </p>
                        <p class="text-lg font-bold" style="color: var(--text-1)">
                            {{ $categories->total() }}
                        </p>
                    </div>
                </div>

                <div class="hidden sm:flex items-center gap-2 text-xs" style="color: var(--text-5)">
                    <iconify-icon icon="mdi:information-outline"></iconify-icon>
                    <span>Halaman {{ $categories->currentPage() }} dari {{ $categories->lastPage() }}</span>
                </div>
            </div>
        @endif


        {{-- ============================================ --}}
        {{-- TABLE --}}
        {{-- ============================================ --}}
        <div class="rounded-xl overflow-hidden border"
             style="background: var(--bg-card); border-color: var(--border-2)">

            <div class="overflow-x-auto">
                <table class="w-full text-sm">

                    {{-- Header --}}
                    <thead class="border-b"
                           style="background: var(--bg-input); border-color: var(--border-2)">
                        <tr>
                            <th class="text-left px-6 py-4 text-[10px] font-bold uppercase tracking-wider w-16"
                                style="color: var(--text-5)">
                                #
                            </th>
                            <th class="text-left px-6 py-4 text-[10px] font-bold uppercase tracking-wider"
                                style="color: var(--text-5)">
                                Kategori
                            </th>
                            <th class="text-left px-6 py-4 text-[10px] font-bold uppercase tracking-wider hidden md:table-cell"
                                style="color: var(--text-5)">
                                Slug
                            </th>
                            <th class="text-left px-6 py-4 text-[10px] font-bold uppercase tracking-wider w-32"
                                style="color: var(--text-5)">
                                Status
                            </th>
                            <th class="text-right px-6 py-4 text-[10px] font-bold uppercase tracking-wider w-44"
                                style="color: var(--text-5)">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    {{-- Body --}}
                    <tbody>
                        @forelse($categories as $category)
                            <tr class="transition-colors group border-b last:border-0"
                                style="border-color: var(--border-1)"
                                onmouseover="this.style.background='var(--bg-hover)'"
                                onmouseout="this.style.background='transparent'">

                                {{-- Nomor --}}
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                                 text-xs font-mono font-semibold border"
                                          style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-4)">
                                        {{ $categories->firstItem() + $loop->index }}
                                    </span>
                                </td>

                                {{-- Kategori --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        {{-- Thumbnail --}}
                                        @if($category->image)
                                            <div class="relative flex-shrink-0">
                                                <img src="{{ asset('storage/' . $category->image) }}"
                                                     alt="{{ $category->name }}"
                                                     class="w-11 h-11 rounded-lg object-cover border"
                                                     style="border-color: var(--border-2)">
                                            </div>
                                        @else
                                            <div class="w-11 h-11 rounded-lg flex items-center justify-center flex-shrink-0 border"
                                                 style="background: var(--bg-input); border-color: var(--border-2)">
                                                <iconify-icon icon="mdi:folder-outline" class="text-[#ecbc42] text-xl"></iconify-icon>
                                            </div>
                                        @endif

                                        <div class="min-w-0">
                                            <div class="font-semibold truncate flex items-center gap-2"
                                                 style="color: var(--text-1)">
                                                {{ $category->name }}
                                            </div>
                                            @if($category->description)
                                                <div class="text-xs mt-0.5 truncate max-w-[320px]"
                                                     style="color: var(--text-5)"
                                                     title="{{ $category->description }}">
                                                    {{ Str::limit($category->description, 60) }}
                                                </div>
                                            @else
                                                <div class="text-xs italic mt-0.5" style="color: var(--text-6)">
                                                    Tanpa deskripsi
                                                </div>
                                            @endif

                                            {{-- Slug mobile (muncul di < md) --}}
                                            <code class="md:hidden inline-block mt-1 px-1.5 py-0.5 rounded text-[10px] font-mono border"
                                                  style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-4)">
                                                {{ $category->slug }}
                                            </code>
                                        </div>
                                    </div>
                                </td>

                                {{-- Slug (desktop) --}}
                                <td class="px-6 py-4 hidden md:table-cell">
                                    <code class="inline-block px-2 py-1 rounded text-xs font-mono border"
                                          style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-4)">
                                        {{ $category->slug }}
                                    </code>
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4">
                                    @if($category->is_active)
                                        <span class="inline-flex items-center gap-1.5
                                                     px-2.5 py-1 rounded-full
                                                     text-[11px] font-bold
                                                     bg-emerald-500/10
                                                     border border-emerald-500/30
                                                     text-emerald-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5
                                                     px-2.5 py-1 rounded-full
                                                     text-[11px] font-bold
                                                     border"
                                              style="background: var(--bg-hover); border-color: var(--border-3); color: var(--text-5)">
                                            <span class="w-1.5 h-1.5 rounded-full" style="background: var(--text-5)"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-2">

                                        {{-- Edit --}}
                                        <a href="{{ route('admin.categories.edit', $category) }}"
                                           class="inline-flex items-center gap-1.5
                                                  px-3 py-2 rounded-lg
                                                  border text-xs font-semibold
                                                  transition-all active:scale-95"
                                           style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)"
                                           onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                                           onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'"
                                           title="Edit kategori">
                                            <iconify-icon icon="mdi:pencil-outline" class="text-sm"></iconify-icon>
                                            <span class="hidden sm:inline">Edit</span>
                                        </a>

                                        {{-- Hapus --}}
                                        <form action="{{ route('admin.categories.destroy', $category) }}"
                                              method="POST"
                                              onsubmit="return confirm('Hapus kategori "{{ $category->name }}" ?')"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="inline-flex items-center gap-1.5
                                                           px-3 py-2 rounded-lg
                                                           bg-red-500/5 border border-red-500/20
                                                           text-red-400 text-xs font-semibold
                                                           hover:bg-red-500/15 hover:border-red-500/40
                                                           transition-all active:scale-95"
                                                    title="Hapus kategori">
                                                <iconify-icon icon="mdi:trash-can-outline" class="text-sm"></iconify-icon>
                                                <span class="hidden sm:inline">Hapus</span>
                                            </button>
                                        </form>

                                    </div>
                                </td>

                            </tr>
                        @empty
                            {{-- Empty State --}}
                            <tr>
                                <td colspan="5" class="px-6 py-20">
                                    <div class="flex flex-col items-center justify-center text-center max-w-sm mx-auto">
                                        <div class="relative mb-5">
                                            <div class="w-24 h-24 rounded-full flex items-center justify-center border"
                                                 style="background: var(--bg-input); border-color: var(--border-2)">
                                                <iconify-icon icon="mdi:folder-open-outline" class="text-5xl text-[#ecbc42] opacity-60"></iconify-icon>
                                            </div>
                                            <span class="absolute -bottom-1 -right-1 w-8 h-8 rounded-full
                                                         bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                                                         flex items-center justify-center
                                                         shadow-lg shadow-amber-500/30">
                                                <iconify-icon icon="mdi:plus" class="text-slate-900 text-lg font-bold"></iconify-icon>
                                            </span>
                                        </div>

                                        <h3 class="text-lg font-bold mb-1.5" style="color: var(--text-1)">
                                            Belum Ada Kategori
                                        </h3>
                                        <p class="text-sm mb-6" style="color: var(--text-5)">
                                            Mulai dengan menambahkan kategori pertama untuk mengelola produk toko Anda.
                                        </p>

                                        <a href="{{ route('admin.categories.create') }}"
                                           class="inline-flex items-center gap-2
                                                  px-5 py-3 rounded-lg
                                                  bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                                                  text-slate-900 text-sm font-bold
                                                  shadow-lg shadow-amber-500/20
                                                  hover:shadow-xl hover:shadow-amber-500/40
                                                  hover:-translate-y-0.5
                                                  transition-all active:scale-95 active:translate-y-0">
                                            <iconify-icon icon="mdi:plus-circle" class="text-lg"></iconify-icon>
                                            Tambah Kategori Pertama
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>


            {{-- ============================================ --}}
            {{-- PAGINATION --}}
            {{-- ============================================ --}}
            @if($categories->hasPages())
                <div class="px-6 py-4 border-t" style="border-color: var(--border-2); background: var(--bg-input)">
                    {{ $categories->links() }}
                </div>
            @endif

        </div>

    </div>

@endsection