@extends('layouts.admin')

@section('title', 'Banner & Promo Bar')
@section('page-title', 'Banner & Promo Bar')

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
                    <iconify-icon icon="mdi:image-multiple-outline" class="text-slate-900 text-2xl"></iconify-icon>
                </span>
                Banner & Promo Bar
            </h1>
        </div>

        <a href="{{ route('admin.banners.create') }}"
           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg
                  text-sm font-bold transition-all active:scale-95 flex-shrink-0
                  bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                  text-slate-900
                  shadow-lg shadow-amber-500/20
                  hover:shadow-xl hover:shadow-amber-500/40
                  hover:-translate-y-0.5">
            <iconify-icon icon="mdi:plus-circle-outline" class="text-lg"></iconify-icon>
            Tambah Banner
        </a>
    </div>


    {{-- ============================================ --}}
    {{-- PROMO BAR SECTION --}}
    {{-- ============================================ --}}
    <div class="rounded-2xl border overflow-hidden"
         style="background: var(--bg-card); border-color: var(--border-2)">

        <div class="px-5 py-4 border-b flex items-center gap-2"
             style="background: var(--bg-input); border-color: var(--border-2)">
            <iconify-icon icon="mdi:bullhorn-outline" class="text-[#ecbc42] text-base"></iconify-icon>
            <h2 class="font-bold text-sm flex-1" style="color: var(--text-1)">Promo Bar</h2>
            <span class="text-[10px] font-mono px-2 py-0.5 rounded-full"
                  style="background: rgba(236,188,66,0.1); color: #ecbc42;">
                #Global
            </span>
        </div>

        <div class="p-5">
            <form action="{{ route('admin.banners.promo-bar.update') }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- TEXT LEFT --}}
                    <div>
                        <label class="form-label">
                            <iconify-icon icon="mdi:format-align-left" class="text-[#ecbc42]"></iconify-icon>
                            Teks Kiri
                        </label>
                        <input type="text"
                               name="text_left"
                               value="{{ old('text_left', $promoBar->text_left ?? '') }}"
                               class="form-input"
                               placeholder="Contoh: GRATIS ONGKIR UNTUK PESANAN DI ATAS Rp750.000">
                        <p class="text-[10px] mt-1.5 flex items-center gap-1" style="color: var(--text-5);">
                            <iconify-icon icon="mdi:arrow-left-thin"></iconify-icon>
                            Muncul di sisi kiri promo bar.
                        </p>
                    </div>

                    {{-- TEXT RIGHT --}}
                    <div>
                        <label class="form-label">
                            <iconify-icon icon="mdi:format-align-right" class="text-[#ecbc42]"></iconify-icon>
                            Teks Kanan
                        </label>
                        <input type="text"
                               name="text_right"
                               value="{{ old('text_right', $promoBar->text_right ?? '') }}"
                               class="form-input"
                               placeholder="Contoh: DISKON 20% UNTUK PESANAN PERTAMA | KODE: BAROKAH01">
                        <p class="text-[10px] mt-1.5 flex items-center gap-1" style="color: var(--text-5);">
                            <iconify-icon icon="mdi:arrow-right-thin"></iconify-icon>
                            Muncul di sisi kanan promo bar.
                        </p>
                    </div>
                </div>

                {{-- Checkbox + Submit --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-4 border-t"
                     style="border-color: var(--border-1);">

                    <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border transition-all"
                           style="background: var(--bg-input); border-color: var(--border-2);"
                           onmouseover="this.style.borderColor='rgba(52,211,153,0.3)'"
                           onmouseout="this.style.borderColor='var(--border-2)'">
                        <input type="checkbox"
                               name="is_active"
                               value="1"
                               @checked(old('is_active', $promoBar->is_active ?? true))
                               class="h-4 w-4 rounded cursor-pointer"
                               style="accent-color: #ecbc42;">
                        <div>
                            <span class="text-sm font-semibold flex items-center gap-1.5" style="color: var(--text-1);">
                                <iconify-icon icon="mdi:check-circle-outline" class="text-emerald-400"></iconify-icon>
                                Promo Bar Aktif
                            </span>
                            <p class="text-[10px] mt-0.5" style="color: var(--text-5);">
                                Tampilkan promo bar di atas halaman.
                            </p>
                        </div>
                    </label>

                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-lg
                                   text-sm font-bold transition-all active:scale-95 flex-shrink-0
                                   bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                                   text-slate-900
                                   shadow-lg shadow-amber-500/20
                                   hover:shadow-xl hover:shadow-amber-500/40
                                   hover:-translate-y-0.5">
                        <iconify-icon icon="mdi:content-save-outline" class="text-base"></iconify-icon>
                        Simpan Promo Bar
                    </button>
                </div>

                {{-- Success --}}
                @if (session('success'))
                    <div class="flex items-start gap-3 rounded-xl px-4 py-3
                                bg-emerald-500/10 border border-emerald-500/30 text-emerald-400">
                        <iconify-icon icon="mdi:check-circle-outline" class="text-xl flex-shrink-0 mt-0.5"></iconify-icon>
                        <span class="text-sm">{{ session('success') }}</span>
                    </div>
                @endif

                {{-- Error --}}
                @if (session('error'))
                    <div class="flex items-start gap-3 rounded-xl px-4 py-3
                                bg-red-500/10 border border-red-500/30 text-red-400">
                        <iconify-icon icon="mdi:alert-circle-outline" class="text-xl flex-shrink-0 mt-0.5"></iconify-icon>
                        <span class="text-sm">{{ session('error') }}</span>
                    </div>
                @endif

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="flex items-start gap-3 rounded-xl px-4 py-3
                                bg-red-500/10 border border-red-500/30 text-red-400">
                        <iconify-icon icon="mdi:alert-circle-outline" class="text-xl flex-shrink-0 mt-0.5"></iconify-icon>
                        <ul class="text-sm list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </form>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- BANNER LIST --}}
    {{-- ============================================ --}}
    <div class="overflow-hidden rounded-xl border"
         style="background: var(--bg-card); border-color: var(--border-2)">

        {{-- Header --}}
        <div class="px-5 py-4 border-b flex items-center gap-2"
             style="background: var(--bg-input); border-color: var(--border-2)">
            <iconify-icon icon="mdi:image-outline" class="text-[#ecbc42] text-base"></iconify-icon>
            <h2 class="font-bold text-sm flex-1" style="color: var(--text-1)">Daftar Banner</h2>
            <span class="text-[10px] font-mono px-2 py-0.5 rounded-full"
                  style="background: rgba(236,188,66,0.1); color: #ecbc42;">
                {{ $banners->count() }} banner
            </span>
        </div>

        @if ($banners->isEmpty())

            {{-- EMPTY STATE --}}
            <div class="px-6 py-20">
                <div class="flex flex-col items-center justify-center text-center max-w-sm mx-auto">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4 border"
                         style="background: var(--bg-input); border-color: var(--border-2)">
                        <iconify-icon icon="mdi:image-off-outline" class="text-2xl" style="color: var(--text-6)"></iconify-icon>
                    </div>
                    <p class="text-sm font-semibold mb-1" style="color: var(--text-3)">
                        Belum ada banner
                    </p>
                    <p class="text-xs mb-4" style="color: var(--text-5)">
                        Tambahkan banner pertama untuk ditampilkan pada halaman utama toko.
                    </p>
                    <a href="{{ route('admin.banners.create') }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg
                              text-xs font-bold transition-all active:scale-95
                              bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                              text-slate-900
                              hover:shadow-lg hover:shadow-amber-500/30">
                        <iconify-icon icon="mdi:plus-circle-outline"></iconify-icon>
                        Tambah Banner
                    </a>
                </div>
            </div>

        @else

            <div class="overflow-x-auto">
                <table class="min-w-full">

                    {{-- Table Header --}}
                    <thead class="border-b"
                           style="background: var(--bg-input); border-color: var(--border-2)">
                        <tr>
                            <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                                Banner
                            </th>
                            <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                                Tombol
                            </th>
                            <th class="px-4 py-4 text-center text-[10px] font-bold uppercase tracking-wider w-20" style="color: var(--text-5)">
                                Urutan
                            </th>
                            <th class="px-4 py-4 text-center text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                                Status
                            </th>
                            <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                                Periode
                            </th>
                            <th class="px-4 py-4 text-right text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    {{-- Table Body --}}
                    <tbody>
                        @foreach ($banners as $banner)
                            <tr class="transition-colors border-b last:border-0"
                                style="border-color: var(--border-1)"
                                onmouseover="this.style.background='var(--bg-hover)'"
                                onmouseout="this.style.background='transparent'">

                                {{-- Banner Preview --}}
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-4 min-w-[300px]">
                                        <div class="flex gap-2 flex-shrink-0">
                                            {{-- Desktop --}}
                                            <div class="relative h-20 w-32 rounded-lg overflow-hidden border"
                                                 style="background: var(--bg-input); border-color: var(--border-2);">
                                                <img src="{{ asset('storage/' . $banner->image) }}"
                                                     alt="{{ $banner->title }}"
                                                     class="h-full w-full object-cover"
                                                     loading="lazy">
                                                <span class="absolute bottom-0 left-0 right-0 bg-black/60 text-white text-[8px] text-center py-0.5 font-bold tracking-wider">
                                                    DESKTOP
                                                </span>
                                            </div>

                                            {{-- Mobile --}}
                                            <div class="relative h-20 w-16 rounded-lg overflow-hidden border"
                                                 style="background: var(--bg-input); border-color: var(--border-2);">
                                                @if($banner->image_mobile)
                                                    <img src="{{ asset('storage/' . $banner->image_mobile) }}"
                                                         alt="{{ $banner->title }}"
                                                         class="h-full w-full object-cover"
                                                         loading="lazy">
                                                @else
                                                    <div class="flex h-full w-full items-center justify-center" style="background: var(--bg-hover);">
                                                        <iconify-icon icon="mdi:image-off-outline" class="text-lg" style="color: var(--text-6)"></iconify-icon>
                                                    </div>
                                                @endif
                                                <span class="absolute bottom-0 left-0 right-0 bg-black/60 text-white text-[8px] text-center py-0.5 font-bold tracking-wider">
                                                    MOBILE
                                                </span>
                                            </div>
                                        </div>

                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold truncate" style="color: var(--text-1);">
                                                {{ $banner->title ?? 'Untitled' }}
                                            </p>
                                            @if ($banner->subtitle)
                                                <p class="text-xs mt-1 line-clamp-2" style="color: var(--text-5);">
                                                    {{ $banner->subtitle }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Button --}}
                                <td class="px-4 py-4">
                                    @if ($banner->button_text)
                                        <div class="min-w-0">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border"
                                                  style="background: rgba(96,165,250,0.1); border-color: rgba(96,165,250,0.3); color: #60a5fa;">
                                                <iconify-icon icon="mdi:gesture-tap-button"></iconify-icon>
                                                {{ $banner->button_text }}
                                            </span>
                                            @if ($banner->button_url)
                                                <p class="text-[10px] mt-1.5 truncate max-w-[180px] flex items-center gap-1"
                                                   style="color: var(--text-5);"
                                                   title="{{ $banner->button_url }}">
                                                    <iconify-icon icon="mdi:link-variant"></iconify-icon>
                                                    {{ $banner->button_url }}
                                                </p>
                                            @endif
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border"
                                              style="background: rgba(148,163,184,0.1); border-color: rgba(148,163,184,0.3); color: var(--text-4);">
                                            <iconify-icon icon="mdi:minus-circle-outline"></iconify-icon>
                                            Tidak ada
                                        </span>
                                    @endif
                                </td>

                                {{-- Sort Order --}}
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg font-mono text-xs font-bold border"
                                          style="background: rgba(236,188,66,0.1); border-color: rgba(236,188,66,0.3); color: #ecbc42;">
                                        {{ $banner->sort_order }}
                                    </span>
                                </td>

                                {{-- Status --}}
                                <td class="px-4 py-4 text-center">
                                    @if ($banner->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border"
                                              style="background: rgba(52,211,153,0.1); border-color: rgba(52,211,153,0.3); color: #34d399;">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border"
                                              style="background: rgba(148,163,184,0.1); border-color: rgba(148,163,184,0.3); color: var(--text-4);">
                                            <span class="w-1.5 h-1.5 rounded-full" style="background: var(--text-5);"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                {{-- Periode --}}
                                <td class="px-4 py-4">
                                    <div class="space-y-1.5 text-xs">
                                        @if ($banner->starts_at)
                                            <div class="flex items-center gap-1.5" style="color: var(--text-3);">
                                                <iconify-icon icon="mdi:calendar-start" class="text-emerald-400"></iconify-icon>
                                                <span class="font-mono">{{ $banner->starts_at->format('d M Y, H:i') }}</span>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-1.5" style="color: var(--text-6);">
                                                <iconify-icon icon="mdi:calendar-start"></iconify-icon>
                                                <span>Mulai: -</span>
                                            </div>
                                        @endif

                                        @if ($banner->ends_at)
                                            <div class="flex items-center gap-1.5" style="color: var(--text-3);">
                                                <iconify-icon icon="mdi:calendar-end" class="text-red-400"></iconify-icon>
                                                <span class="font-mono">{{ $banner->ends_at->format('d M Y, H:i') }}</span>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-1.5" style="color: var(--text-6);">
                                                <iconify-icon icon="mdi:calendar-end"></iconify-icon>
                                                <span>Berakhir: -</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-4 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">

                                        {{-- Edit --}}
                                        <a href="{{ route('admin.banners.edit', $banner) }}"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                                  text-xs font-semibold border transition-all active:scale-95"
                                           style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)"
                                           onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                                           onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'"
                                           title="Edit">
                                            <iconify-icon icon="mdi:pencil-outline"></iconify-icon>
                                        </a>

                                        {{-- Delete --}}
                                        <form action="{{ route('admin.banners.destroy', $banner) }}"
                                              method="POST"
                                              class="inline"
                                              onsubmit="return confirm('Yakin ingin menghapus banner ini? Gambar banner juga akan dihapus secara permanen.')">
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
    .form-label {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-3);
        margin-bottom: 0.5rem;
    }
</style>

@endsection