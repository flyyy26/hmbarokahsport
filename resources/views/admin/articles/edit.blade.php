@extends('layouts.admin')

@section('title', 'Edit Artikel')
@section('page-title', 'Edit Artikel')

@section('content')

<div class="w-full max-w-5xl mx-auto space-y-6">

    {{-- ============================================ --}}
    {{-- HEADER --}}
    {{-- ============================================ --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <a href="{{ route('admin.articles.index') }}"
               class="inline-flex items-center gap-1.5 text-xs font-semibold transition-colors"
               style="color: var(--text-5)"
               onmouseover="this.style.color='#FDDD57'"
               onmouseout="this.style.color='var(--text-5)'">
                <iconify-icon icon="mdi:arrow-left"></iconify-icon>
                Kembali ke Artikel
            </a>
            <h1 class="text-2xl font-bold flex items-center gap-2.5 mt-2" style="color: var(--text-1)">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl
                             bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                             shadow-lg shadow-amber-500/20 flex-shrink-0">
                    <iconify-icon icon="mdi:newspaper-edit" class="text-slate-900 text-2xl"></iconify-icon>
                </span>
                Edit Artikel
            </h1>
            <p class="text-sm mt-1.5 ml-12 truncate" style="color: var(--text-5)">
                {{ $article->title }}
            </p>
        </div>

        {{-- Status Badge --}}
        <div class="flex flex-wrap items-center gap-2 flex-shrink-0">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border"
                  style="background: {{ $article->is_active ? 'rgba(52,211,153,0.1)' : 'rgba(148,163,184,0.1)' }};
                         border-color: {{ $article->is_active ? 'rgba(52,211,153,0.3)' : 'rgba(148,163,184,0.3)' }};
                         color: {{ $article->is_active ? '#34d399' : 'var(--text-4)' }};">
                <span class="w-1.5 h-1.5 rounded-full" style="background: currentColor;"></span>
                {{ $article->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
            @if($article->is_featured)
                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-bold border"
                      style="background: rgba(251,191,36,0.1); border-color: rgba(251,191,36,0.3); color: #fbbf24;">
                    <iconify-icon icon="mdi:star"></iconify-icon>
                    Featured
                </span>
            @endif
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- FORM --}}
    {{-- ============================================ --}}
    <form action="{{ route('admin.articles.update', $article) }}"
          method="POST"
          enctype="multipart/form-data"
          id="article-form"
          class="space-y-6">
        @csrf
        @method('PUT')

        @include('admin.articles._form', ['isEdit' => true, 'article' => $article])

        {{-- ============================================ --}}
        {{-- TOMBOL AKSI --}}
        {{-- ============================================ --}}
        <div class="flex items-center justify-end gap-3 pb-4">
            <a href="{{ route('admin.articles.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg
                      text-sm font-semibold transition-all active:scale-95 border"
               style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)"
               onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
               onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'">
                <iconify-icon icon="mdi:arrow-left"></iconify-icon>
                Batal
            </a>
            <button type="button" id="submit-article-btn"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg
                           text-sm font-bold transition-all active:scale-95
                           bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                           text-slate-900
                           shadow-lg shadow-amber-500/20
                           hover:shadow-xl hover:shadow-amber-500/40
                           hover:-translate-y-0.5">
                <iconify-icon icon="mdi:content-save-outline" class="text-base"></iconify-icon>
                Update Artikel
            </button>
        </div>
    </form>
</div>

@endsection