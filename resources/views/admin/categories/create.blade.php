@extends('layouts.admin')

@section('title', 'Tambah Kategori')
@section('page-title', 'Tambah Kategori')

@section('content')

    <div class="w-full max-w-6xl mx-auto">

        {{-- ============================================ --}}
        {{-- HEADER --}}
        {{-- ============================================ --}}
        <div class="mb-6 flex items-start justify-between gap-4">
            <div class="min-w-0">
                <h2 class="text-2xl font-bold flex items-center gap-2.5" style="color: var(--text-1)">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl
                                 bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                                 shadow-lg shadow-amber-500/20 flex-shrink-0">
                        <iconify-icon icon="mdi:folder-plus-outline" class="text-slate-900 text-2xl"></iconify-icon>
                    </span>
                    Tambah Kategori
                </h2>
                <p class="text-sm mt-1.5 ml-12" style="color: var(--text-5)">
                    Buat kategori produk baru untuk toko Anda.
                </p>
            </div>

            {{-- Back button (desktop) --}}
            <a href="{{ route('admin.categories.index') }}"
               class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 rounded-lg
                      text-sm font-semibold transition-all active:scale-95 flex-shrink-0"
               style="background: var(--bg-input); border: 1px solid var(--border-2); color: var(--text-3)"
               onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
               onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'">
                <iconify-icon icon="mdi:arrow-left"></iconify-icon>
                Kembali
            </a>
        </div>


        {{-- ============================================ --}}
        {{-- FORM --}}
        {{-- ============================================ --}}
        <form action="{{ route('admin.categories.store') }}"
              method="POST"
              enctype="multipart/form-data"
              class="w-full">

            @csrf

            @include('admin.categories._form')


            {{-- ============================================ --}}
            {{-- ACTIONS --}}
            {{-- ============================================ --}}
            <div class="flex flex-col-reverse sm:flex-row sm:justify-between gap-3 mt-8 pt-6 border-t"
                 style="border-color: var(--border-2)">

                {{-- Info --}}
                <div class="hidden sm:flex items-center gap-2 text-xs" style="color: var(--text-5)">
                    <iconify-icon icon="mdi:information-outline"></iconify-icon>
                    <span>Kolom dengan tanda <span class="text-red-400">*</span> wajib diisi</span>
                </div>

                {{-- Buttons --}}
                <div class="flex flex-col-reverse sm:flex-row gap-3 w-full sm:w-auto">
                    <a href="{{ route('admin.categories.index') }}"
                       class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-lg
                              text-sm font-semibold transition-all active:scale-95"
                       style="background: var(--bg-input); border: 1px solid var(--border-2); color: var(--text-3)"
                       onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                       onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'">
                        <iconify-icon icon="mdi:close"></iconify-icon>
                        Batal
                    </a>

                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg
                                   text-sm font-bold
                                   bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                                   text-slate-900
                                   shadow-lg shadow-amber-500/20
                                   hover:shadow-xl hover:shadow-amber-500/40
                                   hover:-translate-y-0.5
                                   transition-all active:scale-95 active:translate-y-0">
                        <iconify-icon icon="mdi:content-save-outline" class="text-lg"></iconify-icon>
                        Simpan Kategori
                    </button>
                </div>
            </div>
        </form>

    </div>

@endsection