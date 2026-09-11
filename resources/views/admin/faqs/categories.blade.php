@extends('layouts.admin')

@section('title', 'Kategori FAQ')
@section('page-title', 'Kategori FAQ')

@section('content')

<div class="w-full space-y-6">

    {{-- ============================================ --}}
    {{-- HEADER --}}
    {{-- ============================================ --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <a href="{{ route('admin.faqs.index') }}"
               class="inline-flex items-center gap-1.5 text-xs font-semibold transition-colors"
               style="color: var(--text-5)"
               onmouseover="this.style.color='#FDDD57'"
               onmouseout="this.style.color='var(--text-5)'">
                <iconify-icon icon="mdi:arrow-left"></iconify-icon>
                Kembali ke FAQ
            </a>
            <h1 class="text-2xl font-bold flex items-center gap-2.5 mt-2" style="color: var(--text-1)">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl
                             bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                             shadow-lg shadow-amber-500/20 flex-shrink-0">
                    <iconify-icon icon="mdi:folder-multiple-outline" class="text-slate-900 text-2xl"></iconify-icon>
                </span>
                Manajemen Kategori FAQ
            </h1>
            <p class="text-sm mt-1.5 ml-12" style="color: var(--text-5)">
                Kelola kategori untuk pertanyaan yang sering diajukan.
            </p>
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
    {{-- GRID: FORM + LIST --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ============================================ --}}
        {{-- FORM TAMBAH KATEGORI --}}
        {{-- ============================================ --}}
        <div class="lg:col-span-1">
            <div class="rounded-2xl border overflow-hidden lg:sticky lg:top-24"
                 style="background: var(--bg-card); border-color: var(--border-2)">

                <div class="px-5 py-4 border-b flex items-center gap-2"
                     style="background: var(--bg-input); border-color: var(--border-2)">
                    <iconify-icon icon="mdi:folder-plus-outline" class="text-[#ecbc42] text-base"></iconify-icon>
                    <h2 class="font-bold text-sm" style="color: var(--text-1)">Tambah Kategori</h2>
                </div>

                <div class="p-5">
                    <form action="{{ route('admin.faqs.categories.store') }}" method="POST">
                        @csrf

                        <div>
                            <label class="form-label">
                                <iconify-icon icon="mdi:tag-outline" class="text-[#ecbc42]"></iconify-icon>
                                Nama Kategori <span class="text-red-400">*</span>
                            </label>
                            <input type="text"
                                   name="name"
                                   value="{{ old('name') }}"
                                   required
                                   class="form-input"
                                   placeholder="Contoh: Promo">
                            @error('name')
                                <p class="text-[10px] mt-1 text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                                class="w-full mt-4 inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg
                                       text-sm font-bold transition-all active:scale-95
                                       bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                                       text-slate-900
                                       shadow-lg shadow-amber-500/20
                                       hover:shadow-xl hover:shadow-amber-500/40
                                       hover:-translate-y-0.5">
                            <iconify-icon icon="mdi:plus-circle-outline" class="text-base"></iconify-icon>
                            Tambah Kategori
                        </button>
                    </form>

                    {{-- Info Box --}}
                    <div class="mt-5 pt-5 border-t" style="border-color: var(--border-1);">
                        <div class="flex items-start gap-2.5 p-3 rounded-lg"
                             style="background: rgba(236,188,66,0.05); border: 1px solid rgba(236,188,66,0.15);">
                            <iconify-icon icon="mdi:lightbulb-outline" class="text-[#ecbc42] text-base flex-shrink-0 mt-0.5"></iconify-icon>
                            <div>
                                <p class="text-[11px] font-bold" style="color: #ecbc42;">Tips</p>
                                <p class="text-[10px] mt-0.5 leading-relaxed" style="color: var(--text-5);">
                                    Gunakan nama kategori yang jelas dan singkat agar mudah dipahami pelanggan.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- ============================================ --}}
        {{-- LIST KATEGORI --}}
        {{-- ============================================ --}}
        <div class="lg:col-span-2">
            <div class="rounded-2xl border overflow-hidden"
                 style="background: var(--bg-card); border-color: var(--border-2)">

                {{-- Header --}}
                <div class="px-5 py-4 border-b flex items-center gap-2"
                     style="background: var(--bg-input); border-color: var(--border-2)">
                    <iconify-icon icon="mdi:format-list-bulleted" class="text-[#ecbc42] text-base"></iconify-icon>
                    <h2 class="font-bold text-sm flex-1" style="color: var(--text-1)">Daftar Kategori</h2>
                    @if($categories->count() > 0)
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded-full"
                              style="background: rgba(236,188,66,0.1); color: #ecbc42;">
                            {{ $categories->count() }} kategori
                        </span>
                    @endif
                </div>

                @if ($categories->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full">

                            {{-- Table Header --}}
                            <thead class="border-b"
                                   style="background: var(--bg-input); border-color: var(--border-2)">
                                <tr>
                                    <th class="px-4 py-3.5 text-left text-[10px] font-bold uppercase tracking-wider w-12" style="color: var(--text-5)">#</th>
                                    <th class="px-4 py-3.5 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                                        Nama Kategori
                                    </th>
                                    <th class="px-4 py-3.5 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                                        Slug
                                    </th>
                                    <th class="px-4 py-3.5 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                                        Jumlah FAQ
                                    </th>
                                    <th class="px-4 py-3.5 text-left text-[10px] font-bold uppercase tracking-wider w-20" style="color: var(--text-5)">
                                        Urutan
                                    </th>
                                    <th class="px-4 py-3.5 text-right text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>

                            {{-- Table Body --}}
                            <tbody>
                                @foreach ($categories as $category)
                                    <tr class="transition-colors border-b last:border-0"
                                        style="border-color: var(--border-1)"
                                        onmouseover="this.style.background='var(--bg-hover)'"
                                        onmouseout="this.style.background='transparent'">

                                        {{-- No --}}
                                        <td class="px-4 py-3.5">
                                            <span class="text-xs font-mono font-bold" style="color: var(--text-5);">
                                                {{ $loop->iteration }}
                                            </span>
                                        </td>

                                        {{-- Nama Kategori --}}
                                        <td class="px-4 py-3.5">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0
                                                            bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                                                            shadow-md shadow-amber-500/20">
                                                    <iconify-icon icon="mdi:folder-outline" class="text-slate-900 text-base"></iconify-icon>
                                                </div>
                                                <div class="min-w-0 flex items-center gap-2 flex-wrap">
                                                    <p class="text-sm font-semibold" style="color: var(--text-1);">
                                                        {{ $category->name }}
                                                    </p>
                                                    @if(!$category->is_active)
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold border"
                                                              style="background: rgba(248,113,113,0.1); border-color: rgba(248,113,113,0.3); color: #f87171;">
                                                            <span class="w-1 h-1 rounded-full bg-red-400"></span>
                                                            Nonaktif
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Slug --}}
                                        <td class="px-4 py-3.5">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded font-mono text-[10px]"
                                                  style="background: var(--bg-input); color: var(--text-4);">
                                                {{ $category->slug }}
                                            </span>
                                        </td>

                                        {{-- Jumlah FAQ --}}
                                        <td class="px-4 py-3.5">
                                            @php $faqCount = $category->faqs()->count() ?? 0; @endphp
                                            @if($faqCount > 0)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border"
                                                      style="background: rgba(96,165,250,0.1); border-color: rgba(96,165,250,0.3); color: #60a5fa;">
                                                    <iconify-icon icon="mdi:help-circle-outline"></iconify-icon>
                                                    {{ $faqCount }} FAQ
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border"
                                                      style="background: rgba(148,163,184,0.1); border-color: rgba(148,163,184,0.3); color: var(--text-4);">
                                                    <iconify-icon icon="mdi:minus-circle-outline"></iconify-icon>
                                                    Kosong
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Urutan --}}
                                        <td class="px-4 py-3.5">
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg font-mono text-xs font-bold border"
                                                  style="background: rgba(236,188,66,0.1); border-color: rgba(236,188,66,0.3); color: #ecbc42;">
                                                {{ $category->order }}
                                            </span>
                                        </td>

                                        {{-- Aksi --}}
                                        <td class="px-4 py-3.5 text-right">
                                            <form action="{{ route('admin.faqs.categories.destroy', $category) }}"
                                                  method="POST"
                                                  class="inline"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori {{ $category->name }}?')">
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
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    {{-- Empty State --}}
                    <div class="px-6 py-16">
                        <div class="flex flex-col items-center justify-center text-center max-w-sm mx-auto">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4 border"
                                 style="background: var(--bg-input); border-color: var(--border-2)">
                                <iconify-icon icon="mdi:folder-outline" class="text-2xl" style="color: var(--text-6)"></iconify-icon>
                            </div>
                            <p class="text-sm font-semibold mb-1" style="color: var(--text-3)">
                                Belum ada kategori
                            </p>
                            <p class="text-xs" style="color: var(--text-5)">
                                Tambahkan kategori pertama menggunakan form di samping.
                            </p>
                        </div>
                    </div>
                @endif
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