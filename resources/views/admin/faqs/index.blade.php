@extends('layouts.admin')

@section('title', 'Manajemen FAQ')
@section('page-title', 'FAQ')

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
                    <iconify-icon icon="mdi:help-circle-outline" class="text-slate-900 text-2xl"></iconify-icon>
                </span>
                Manajemen FAQ
            </h1>
            <p class="text-sm mt-1.5 ml-12" style="color: var(--text-5)">
                Kelola pertanyaan yang sering diajukan pelanggan.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2 flex-shrink-0">
            {{-- Kelola Kategori --}}
            <a href="{{ route('admin.faqs.categories') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg
                      text-sm font-semibold transition-all active:scale-95 border"
               style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)"
               onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
               onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'">
                <iconify-icon icon="mdi:folder-multiple-outline"></iconify-icon>
                Kelola Kategori
            </a>

            {{-- Tambah FAQ --}}
            <a href="{{ route('admin.faqs.create') }}"
               class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-lg
                      text-sm font-bold transition-all active:scale-95
                      bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                      text-slate-900
                      shadow-lg shadow-amber-500/20
                      hover:shadow-xl hover:shadow-amber-500/40
                      hover:-translate-y-0.5">
                <iconify-icon icon="mdi:plus-circle-outline" class="text-lg"></iconify-icon>
                Tambah FAQ
            </a>
        </div>
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
    {{-- STATS CARDS (Collection & Paginator Safe) --}}
    {{-- ============================================ --}}
    @php
        // ✅ Support both Collection and Paginator
        $items = $faqs instanceof \Illuminate\Pagination\LengthAwarePaginator
              || $faqs instanceof \Illuminate\Pagination\Paginator
                ? $faqs->getCollection()
                : $faqs;

        $totalFaqs     = $items->count();
        $activeFaqs    = $items->where('is_active', true)->count();
        $inactiveFaqs  = $items->where('is_active', false)->count();
        $categoryCount = $items->pluck('category')->filter()->unique()->count();
    @endphp

    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="rounded-xl border p-4 text-center transition-colors"
             style="background: var(--bg-card); border-color: var(--border-2);"
             onmouseover="this.style.borderColor='rgba(236,188,66,0.3)'"
             onmouseout="this.style.borderColor='var(--border-2)'">
            <p class="text-2xl font-bold" style="color: var(--text-1);">{{ $totalFaqs }}</p>
            <p class="text-[11px] font-semibold uppercase tracking-wider mt-1" style="color: var(--text-5);">Total FAQ</p>
        </div>
        <div class="rounded-xl border p-4 text-center transition-colors"
             style="background: var(--bg-card); border-color: var(--border-2);"
             onmouseover="this.style.borderColor='rgba(52,211,153,0.3)'"
             onmouseout="this.style.borderColor='var(--border-2)'">
            <p class="text-2xl font-bold text-emerald-400">{{ $activeFaqs }}</p>
            <p class="text-[11px] font-semibold uppercase tracking-wider mt-1 text-emerald-400/80">Aktif</p>
        </div>
        <div class="rounded-xl border p-4 text-center transition-colors"
             style="background: var(--bg-card); border-color: var(--border-2);"
             onmouseover="this.style.borderColor='rgba(148,163,184,0.3)'"
             onmouseout="this.style.borderColor='var(--border-2)'">
            <p class="text-2xl font-bold" style="color: var(--text-4);">{{ $inactiveFaqs }}</p>
            <p class="text-[11px] font-semibold uppercase tracking-wider mt-1" style="color: var(--text-5);">Nonaktif</p>
        </div>
        <div class="rounded-xl border p-4 text-center transition-colors"
             style="background: var(--bg-card); border-color: var(--border-2);"
             onmouseover="this.style.borderColor='rgba(96,165,250,0.3)'"
             onmouseout="this.style.borderColor='var(--border-2)'">
            <p class="text-2xl font-bold text-blue-400">{{ $categoryCount }}</p>
            <p class="text-[11px] font-semibold uppercase tracking-wider mt-1 text-blue-400/80">Kategori</p>
        </div>
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
                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider w-12" style="color: var(--text-5)">#</th>
                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Pertanyaan
                        </th>
                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Kategori
                        </th>
                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Status
                        </th>
                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider w-20" style="color: var(--text-5)">
                            Urutan
                        </th>
                        <th class="px-4 py-4 text-right text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Aksi
                        </th>
                    </tr>
                </thead>

                {{-- Table Body --}}
                <tbody>
                    @forelse ($faqs as $faq)
                        <tr class="transition-colors border-b last:border-0"
                            style="border-color: var(--border-1)"
                            onmouseover="this.style.background='var(--bg-hover)'"
                            onmouseout="this.style.background='transparent'">

                            {{-- No --}}
                            <td class="px-4 py-4">
                                <span class="text-xs font-mono font-bold" style="color: var(--text-5);">
                                    {{ method_exists($faqs, 'perPage') ? $faqs->perPage() * ($faqs->currentPage() - 1) + $loop->iteration : $loop->iteration }}
                                </span>
                            </td>

                            {{-- Pertanyaan --}}
                            <td class="px-4 py-4 max-w-md">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0
                                                bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                                                shadow-md shadow-amber-500/20 mt-0.5">
                                        <iconify-icon icon="mdi:help" class="text-slate-900 text-base"></iconify-icon>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold leading-snug" style="color: var(--text-1);">
                                            {{ $faq->question }}
                                        </p>
                                        <p class="text-xs mt-1 line-clamp-1" style="color: var(--text-5);">
                                            {{ Str::limit($faq->answer, 80) }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- Kategori --}}
                            <td class="px-4 py-4">
                                @if($faq->category)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border"
                                          style="background: rgba(96,165,250,0.1); border-color: rgba(96,165,250,0.3); color: #60a5fa;">
                                        <iconify-icon icon="mdi:folder-outline"></iconify-icon>
                                        {{ $faq->category_label ?? $faq->category }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border"
                                          style="background: rgba(148,163,184,0.1); border-color: rgba(148,163,184,0.3); color: var(--text-4);">
                                        <iconify-icon icon="mdi:folder-off-outline"></iconify-icon>
                                        Tanpa Kategori
                                    </span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-4">
                                @if($faq->is_active)
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

                            {{-- Urutan --}}
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg font-mono text-xs font-bold border"
                                      style="background: rgba(236,188,66,0.1); border-color: rgba(236,188,66,0.3); color: #ecbc42;">
                                    {{ $faq->order }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">

                                    {{-- Edit --}}
                                    <a href="{{ route('admin.faqs.edit', $faq) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                              text-xs font-semibold border transition-all active:scale-95"
                                       style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)"
                                       onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                                       onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'"
                                       title="Edit">
                                        <iconify-icon icon="mdi:pencil-outline"></iconify-icon>
                                    </a>

                                    {{-- Toggle --}}
                                    <form action="{{ route('admin.faqs.toggle', $faq) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                                       text-xs font-semibold border transition-all active:scale-95"
                                                style="background: rgba(251,191,36,0.05); border-color: rgba(251,191,36,0.2); color: #fbbf24;"
                                                onmouseover="this.style.background='rgba(251,191,36,0.15)'; this.style.borderColor='rgba(251,191,36,0.4)'"
                                                onmouseout="this.style.background='rgba(251,191,36,0.05)'; this.style.borderColor='rgba(251,191,36,0.2)'"
                                                title="{{ $faq->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <iconify-icon icon="{{ $faq->is_active ? 'mdi:lock-outline' : 'mdi:lock-open-outline' }}"></iconify-icon>
                                        </button>
                                    </form>

                                    {{-- Delete --}}
                                    <form action="{{ route('admin.faqs.destroy', $faq) }}"
                                          method="POST"
                                          class="inline"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus FAQ ini?')">
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
                            <td colspan="6" class="px-6 py-20">
                                <div class="flex flex-col items-center justify-center text-center max-w-sm mx-auto">
                                    <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4 border"
                                         style="background: var(--bg-input); border-color: var(--border-2)">
                                        <iconify-icon icon="mdi:help-circle-outline" class="text-2xl" style="color: var(--text-6)"></iconify-icon>
                                    </div>
                                    <p class="text-sm font-semibold mb-1" style="color: var(--text-3)">
                                        Belum ada FAQ
                                    </p>
                                    <p class="text-xs mb-4" style="color: var(--text-5)">
                                        Buat FAQ pertama untuk membantu pelanggan Anda.
                                    </p>
                                    <a href="{{ route('admin.faqs.create') }}"
                                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg
                                              text-xs font-bold transition-all active:scale-95
                                              bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                                              text-slate-900
                                              hover:shadow-lg hover:shadow-amber-500/30">
                                        <iconify-icon icon="mdi:plus-circle-outline"></iconify-icon>
                                        Tambah FAQ
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(method_exists($faqs, 'hasPages') && $faqs->hasPages())
            <div class="border-t px-6 py-4"
                 style="border-color: var(--border-2); background: var(--bg-input)">
                {{ $faqs->links() }}
            </div>
        @endif
    </div>

</div>

@endsection