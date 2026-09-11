@extends('layouts.admin')

@section('title', 'Edit Banner')
@section('page-title', 'Edit Banner')

@section('content')

<div class="w-full max-w-5xl mx-auto space-y-6">

    {{-- ============================================ --}}
    {{-- HEADER --}}
    {{-- ============================================ --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <a href="{{ route('admin.banners.index') }}"
               class="inline-flex items-center gap-1.5 text-xs font-semibold transition-colors"
               style="color: var(--text-5)"
               onmouseover="this.style.color='#FDDD57'"
               onmouseout="this.style.color='var(--text-5)'">
                <iconify-icon icon="mdi:arrow-left"></iconify-icon>
                Kembali ke Banner
            </a>
            <h1 class="text-2xl font-bold flex items-center gap-2.5 mt-2" style="color: var(--text-1)">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl
                             bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                             shadow-lg shadow-amber-500/20 flex-shrink-0">
                    <iconify-icon icon="mdi:image-edit" class="text-slate-900 text-2xl"></iconify-icon>
                </span>
                Edit Banner
            </h1>
            <p class="text-sm mt-1.5 ml-12 truncate" style="color: var(--text-5)">
                {{ $banner->title ?? 'Untitled Banner' }}
            </p>
        </div>

        {{-- Status Badge --}}
        <div class="flex flex-wrap items-center gap-2 flex-shrink-0">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border"
                  style="background: {{ $banner->is_active ? 'rgba(52,211,153,0.1)' : 'rgba(148,163,184,0.1)' }};
                         border-color: {{ $banner->is_active ? 'rgba(52,211,153,0.3)' : 'rgba(148,163,184,0.3)' }};
                         color: {{ $banner->is_active ? '#34d399' : 'var(--text-4)' }};">
                <span class="w-1.5 h-1.5 rounded-full" style="background: currentColor;"></span>
                {{ $banner->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border"
                  style="background: rgba(236,188,66,0.1); border-color: rgba(236,188,66,0.3); color: #ecbc42;">
                <iconify-icon icon="mdi:sort-numeric-ascending"></iconify-icon>
                Urutan: {{ $banner->sort_order }}
            </span>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- FORM --}}
    {{-- ============================================ --}}
    <form action="{{ route('admin.banners.update', $banner) }}"
          method="POST"
          enctype="multipart/form-data"
          class="space-y-6">
        @csrf
        @method('PUT')

        @include('admin.banners._form', ['banner' => $banner])
    </form>

</div>

@endsection