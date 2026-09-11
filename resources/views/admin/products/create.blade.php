@extends('layouts.admin')

@section('title', 'Tambah Produk')
@section('page-title', 'Tambah Produk')

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
                        <iconify-icon icon="mdi:package-variant-plus" class="text-slate-900 text-2xl"></iconify-icon>
                    </span>
                    Tambah Produk
                </h2>
                <p class="text-sm mt-1.5 ml-12" style="color: var(--text-5)">
                    Tambahkan produk baru ke katalog toko Anda.
                </p>
            </div>

            {{-- Back button (desktop) --}}
            <a href="{{ route('admin.products.index') }}"
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
        <form id="product-form"
              action="{{ route('admin.products.store') }}"
              method="POST"
              enctype="multipart/form-data"
              class="w-full">

            @csrf

            @include('admin.products._form', [
                'isEdit' => false,
                'product' => null,
                'existingOptions' => [],
                'existingVariants' => [],
                'features' => $features ?? [],
                'categories' => $categories ?? [],
            ])
        </form>

    </div>

@endsection