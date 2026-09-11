@extends('layouts.admin')

@section('title', 'Edit Produk')
@section('page-title', 'Edit Produk')

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
                        <iconify-icon icon="mdi:package-variant-closed" class="text-slate-900 text-2xl"></iconify-icon>
                    </span>
                    Edit Produk
                </h2>
                <p class="text-sm mt-1.5 ml-12" style="color: var(--text-5)">
                    Perbarui informasi produk <strong style="color: var(--text-3)">"{{ $product->name }}"</strong>.
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
        {{-- META INFO --}}
        {{-- ============================================ --}}
        <div class="flex flex-wrap items-center gap-4 px-4 py-3 rounded-xl mb-6 border"
             style="background: var(--bg-card); border-color: var(--border-2)">

            {{-- ID --}}
            <div class="flex items-center gap-2">
                <iconify-icon icon="mdi:identifier" class="text-[#ecbc42] text-base"></iconify-icon>
                <span class="text-xs" style="color: var(--text-5)">ID:</span>
                <code class="text-xs font-mono font-semibold" style="color: var(--text-3)">#{{ $product->id }}</code>
            </div>

            <div class="w-px h-4" style="background: var(--border-2)"></div>

            {{-- Slug --}}
            <div class="flex items-center gap-2">
                <iconify-icon icon="mdi:link-variant" class="text-[#ecbc42] text-base"></iconify-icon>
                <span class="text-xs" style="color: var(--text-5)">Slug:</span>
                <code class="text-xs font-mono" style="color: var(--text-3)">{{ $product->slug }}</code>
            </div>

            @if($product->created_at)
                <div class="w-px h-4" style="background: var(--border-2)"></div>

                <div class="flex items-center gap-2">
                    <iconify-icon icon="mdi:clock-outline" class="text-[#ecbc42] text-base"></iconify-icon>
                    <span class="text-xs" style="color: var(--text-5)">Dibuat:</span>
                    <span class="text-xs font-semibold" style="color: var(--text-3)">
                        {{ $product->created_at->format('d M Y, H:i') }}
                    </span>
                </div>
            @endif

            @if($product->updated_at && $product->updated_at != $product->created_at)
                <div class="w-px h-4" style="background: var(--border-2)"></div>

                <div class="flex items-center gap-2">
                    <iconify-icon icon="mdi:update" class="text-[#ecbc42] text-base"></iconify-icon>
                    <span class="text-xs" style="color: var(--text-5)">Diperbarui:</span>
                    <span class="text-xs font-semibold" style="color: var(--text-3)">
                        {{ $product->updated_at->format('d M Y, H:i') }}
                    </span>
                </div>
            @endif
        </div>


        {{-- ============================================ --}}
        {{-- FORM --}}
        {{-- ============================================ --}}
        <form id="product-form"
              action="{{ route('admin.products.update', $product->id) }}"
              method="POST"
              enctype="multipart/form-data"
              class="w-full">

            @csrf
            @method('PUT')

            @include('admin.products._form', [
                'isEdit' => true,
                'product' => $product,
                'existingOptions' => $existingOptions ?? [],
                'existingVariants' => $existingVariants ?? [],
                'features' => $features ?? [],
                'categories' => $categories ?? [],
            ])
        </form>

    </div>

@endsection