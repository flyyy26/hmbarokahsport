@extends('layouts.admin')

@section('title', 'Kebijakan Privasi')
@section('page-title', 'Kebijakan Privasi')

@section('content')

<div class="w-full max-w-5xl mx-auto space-y-6">

    {{-- ============================================ --}}
    {{-- HEADER --}}
    {{-- ============================================ --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <h1 class="text-2xl font-bold flex items-center gap-2.5" style="color: var(--text-1)">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl
                             bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                             shadow-lg shadow-amber-500/20 flex-shrink-0">
                    <iconify-icon icon="mdi:shield-lock-outline" class="text-slate-900 text-2xl"></iconify-icon>
                </span>
                Kebijakan Privasi
            </h1>
            <p class="text-sm mt-1.5 ml-12" style="color: var(--text-5)">
                Kelola kebijakan privasi yang berlaku di toko.
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
    {{-- STATUS CARD --}}
    {{-- ============================================ --}}
    <div class="rounded-2xl border overflow-hidden"
         style="background: var(--bg-card); border-color: var(--border-2);">

        <div class="p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0
                            {{ $privacy && $privacy->is_active
                                ? 'bg-gradient-to-br from-emerald-400 to-emerald-500'
                                : 'bg-gradient-to-br from-slate-500 to-slate-600' }}
                            shadow-lg shadow-{{ $privacy && $privacy->is_active ? 'emerald' : 'slate' }}-500/20">
                    <iconify-icon icon="{{ $privacy && $privacy->is_active ? 'mdi:shield-check' : 'mdi:shield-off-outline' }}"
                                  class="text-white text-2xl"></iconify-icon>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider mb-0.5" style="color: var(--text-5);">
                        Status Kebijakan
                    </p>
                    @if($privacy && $privacy->is_active)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border"
                              style="background: rgba(52,211,153,0.1); border-color: rgba(52,211,153,0.3); color: #34d399;">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border"
                              style="background: rgba(148,163,184,0.1); border-color: rgba(148,163,184,0.3); color: var(--text-4);">
                            <span class="w-1.5 h-1.5 rounded-full" style="background: var(--text-5);"></span>
                            {{ $privacy ? 'Nonaktif' : 'Belum Dibuat' }}
                        </span>
                    @endif
                </div>
            </div>

            @if($privacy)
                <button type="button"
                        onclick="togglePrivacy()"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg
                               text-sm font-bold transition-all active:scale-95 border flex-shrink-0"
                        style="background: {{ $privacy->is_active ? 'rgba(251,191,36,0.05)' : 'rgba(52,211,153,0.05)' }};
                               border-color: {{ $privacy->is_active ? 'rgba(251,191,36,0.3)' : 'rgba(52,211,153,0.3)' }};
                               color: {{ $privacy->is_active ? '#fbbf24' : '#34d399' }};"
                        onmouseover="this.style.background='{{ $privacy->is_active ? 'rgba(251,191,36,0.15)' : 'rgba(52,211,153,0.15)' }}'; this.style.borderColor='{{ $privacy->is_active ? 'rgba(251,191,36,0.5)' : 'rgba(52,211,153,0.5)' }}'"
                        onmouseout="this.style.background='{{ $privacy->is_active ? 'rgba(251,191,36,0.05)' : 'rgba(52,211,153,0.05)' }}'; this.style.borderColor='{{ $privacy->is_active ? 'rgba(251,191,36,0.3)' : 'rgba(52,211,153,0.3)' }}'">
                    <iconify-icon icon="{{ $privacy->is_active ? 'mdi:lock-outline' : 'mdi:lock-open-outline' }}"></iconify-icon>
                    {{ $privacy->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                </button>
            @endif
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- FORM --}}
    {{-- ============================================ --}}
    <form action="{{ route('admin.privacy.update') }}"
          method="POST"
          id="privacy-form"
          class="space-y-6">
        @csrf
        @method('PUT')

        {{-- ERROR VALIDATION --}}
        @if ($errors->any())
            <div class="flex items-start gap-3 rounded-xl px-4 py-3
                        bg-red-500/10 border border-red-500/30 text-red-400">
                <iconify-icon icon="mdi:alert-circle-outline" class="text-xl flex-shrink-0 mt-0.5"></iconify-icon>
                <div class="text-sm">
                    <p class="font-bold mb-1">Mohon perbaiki kesalahan berikut:</p>
                    <ul class="list-disc list-inside space-y-0.5 text-red-300">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif


        {{-- ============================================ --}}
        {{-- SECTION 1: INFORMASI KEBIJAKAN --}}
        {{-- ============================================ --}}
        <div class="rounded-2xl border overflow-hidden"
             style="background: var(--bg-card); border-color: var(--border-2)">

            <div class="px-5 py-4 border-b flex items-center gap-2"
                 style="background: var(--bg-input); border-color: var(--border-2)">
                <iconify-icon icon="mdi:information-outline" class="text-[#ecbc42] text-base"></iconify-icon>
                <h2 class="font-bold text-sm" style="color: var(--text-1)">Informasi Kebijakan</h2>
            </div>

            <div class="p-5 space-y-5">

                {{-- Judul --}}
                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:format-title" class="text-[#ecbc42]"></iconify-icon>
                        Judul <span class="text-red-400">*</span>
                    </label>
                    <input type="text"
                           name="title"
                           value="{{ old('title', $privacy?->title ?? 'Kebijakan Privasi Barokah Sport') }}"
                           required
                           class="form-input"
                           placeholder="Kebijakan Privasi Barokah Sport">
                    @error('title')
                        <p class="text-[10px] mt-1 text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Versi + Tanggal Efektif --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div>
                        <label class="form-label">
                            <iconify-icon icon="mdi:tag-outline" class="text-[#ecbc42]"></iconify-icon>
                            Versi
                        </label>
                        <input type="text"
                               name="version"
                               value="{{ old('version', $privacy?->version ?? '1.0') }}"
                               class="form-input"
                               placeholder="Contoh: 1.0">
                        @error('version')
                            <p class="text-[10px] mt-1 text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label">
                            <iconify-icon icon="mdi:calendar-check-outline" class="text-[#ecbc42]"></iconify-icon>
                            Tanggal Efektif
                        </label>
                        <input type="date"
                               name="effective_date"
                               value="{{ old('effective_date', $privacy?->effective_date?->format('Y-m-d') ?? date('Y-m-d')) }}"
                               class="form-input">
                        @error('effective_date')
                            <p class="text-[10px] mt-1 text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>


        {{-- ============================================ --}}
        {{-- SECTION 2: KONTEN KEBIJAKAN --}}
        {{-- ============================================ --}}
        <div class="rounded-2xl border overflow-hidden"
             style="background: var(--bg-card); border-color: var(--border-2)">

            <div class="px-5 py-4 border-b flex items-center gap-2"
                 style="background: var(--bg-input); border-color: var(--border-2)">
                <iconify-icon icon="mdi:text-box-edit-outline" class="text-[#ecbc42] text-base"></iconify-icon>
                <h2 class="font-bold text-sm flex-1" style="color: var(--text-1)">Konten Kebijakan Privasi</h2>
                <span class="text-[10px] font-mono px-2 py-0.5 rounded-full"
                      style="background: rgba(236,188,66,0.1); color: #ecbc42;">
                    <iconify-icon icon="mdi:shield-lock-outline" class="inline"></iconify-icon>
                    Konten Legal
                </span>
            </div>

            <div class="p-5">
                <label class="form-label">
                    <iconify-icon icon="mdi:text-box-outline" class="text-[#ecbc42]"></iconify-icon>
                    Isi Konten <span class="text-red-400">*</span>
                </label>

                {{-- Hidden input untuk form submission --}}
                <textarea name="content" id="privacy_content" style="display: none;">{{ old('content', $privacy?->content ?? '') }}</textarea>

                {{-- Quill Editor Container --}}
                <div id="quill-editor"
                     class="rounded-lg border overflow-hidden"
                     style="border-color: var(--border-2); min-height: 400px;">
                    {!! old('content', $privacy?->content ?? '') !!}
                </div>

                @error('content')
                    <p class="text-[10px] mt-1 text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>


        {{-- ============================================ --}}
        {{-- SECTION 3: STATUS --}}
        {{-- ============================================ --}}
        <div class="rounded-2xl border overflow-hidden"
             style="background: var(--bg-card); border-color: var(--border-2)">

            <div class="px-5 py-4 border-b flex items-center gap-2"
                 style="background: var(--bg-input); border-color: var(--border-2)">
                <iconify-icon icon="mdi:shield-check-outline" class="text-[#ecbc42] text-base"></iconify-icon>
                <h2 class="font-bold text-sm" style="color: var(--text-1)">Status Publikasi</h2>
            </div>

            <div class="p-5">
                <label class="flex items-start gap-3 cursor-pointer p-3 rounded-lg border transition-all"
                       style="background: var(--bg-input); border-color: var(--border-2);"
                       onmouseover="this.style.borderColor='rgba(52,211,153,0.3)'"
                       onmouseout="this.style.borderColor='var(--border-2)'">
                    <input type="checkbox"
                           name="is_active"
                           id="is_active"
                           value="1"
                           {{ old('is_active', $privacy?->is_active ?? true) ? 'checked' : '' }}
                           class="mt-0.5 h-4 w-4 rounded cursor-pointer"
                           style="accent-color: #ecbc42;">
                    <div>
                        <span class="text-sm font-semibold flex items-center gap-1.5" style="color: var(--text-1);">
                            <iconify-icon icon="mdi:check-circle-outline" class="text-emerald-400"></iconify-icon>
                            Aktifkan Kebijakan Privasi
                        </span>
                        <p class="text-[10px] mt-0.5" style="color: var(--text-5);">
                            Kebijakan privasi akan tampil di halaman publik dan dapat diakses pelanggan.
                        </p>
                    </div>
                </label>
            </div>
        </div>


        {{-- ============================================ --}}
        {{-- TOMBOL AKSI --}}
        {{-- ============================================ --}}
        <div class="flex items-center justify-end gap-3 pb-4">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg
                           text-sm font-bold transition-all active:scale-95
                           bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                           text-slate-900
                           shadow-lg shadow-amber-500/20
                           hover:shadow-xl hover:shadow-amber-500/40
                           hover:-translate-y-0.5">
                <iconify-icon icon="mdi:content-save-outline" class="text-base"></iconify-icon>
                Simpan Kebijakan Privasi
            </button>
        </div>
    </form>


    {{-- ============================================ --}}
    {{-- FORM TOGGLE (HIDDEN) --}}
    {{-- ============================================ --}}
    @if($privacy)
        <form action="{{ route('admin.privacy.toggle') }}" method="POST" id="toggle-form" style="display: none;">
            @csrf
            @method('PATCH')
        </form>
    @endif
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

    /* ============================================ */
    /* QUILL EDITOR OVERRIDE */
    /* ============================================ */
    #quill-editor .ql-toolbar.ql-snow {
        border: none;
        border-bottom: 1px solid var(--border-2);
        background: var(--bg-input);
        border-radius: 0;
        padding: 0.75rem;
    }

    #quill-editor .ql-toolbar.ql-snow .ql-stroke { stroke: var(--text-4); }
    #quill-editor .ql-toolbar.ql-snow .ql-fill   { fill: var(--text-4); }
    #quill-editor .ql-toolbar.ql-snow .ql-picker { color: var(--text-4); }
    #quill-editor .ql-toolbar.ql-snow .ql-picker-options {
        background: var(--bg-card);
        border-color: var(--border-2);
    }

    #quill-editor .ql-toolbar.ql-snow button:hover .ql-stroke,
    #quill-editor .ql-toolbar.ql-snow button.ql-active .ql-stroke {
        stroke: #ecbc42 !important;
    }

    #quill-editor .ql-toolbar.ql-snow button:hover .ql-fill,
    #quill-editor .ql-toolbar.ql-snow button.ql-active .ql-fill {
        fill: #ecbc42 !important;
    }

    #quill-editor .ql-toolbar.ql-snow button:hover,
    #quill-editor .ql-toolbar.ql-snow button.ql-active,
    #quill-editor .ql-toolbar.ql-snow .ql-picker-label:hover {
        color: #ecbc42 !important;
    }

    #quill-editor .ql-container.ql-snow {
        border: none;
        background: var(--bg-input);
        font-family: inherit;
        font-size: 0.875rem;
    }

    #quill-editor .ql-editor {
        min-height: 400px !important;
        max-height: 600px !important;
        color: var(--text-2);
        font-size: 0.875rem;
        line-height: 1.8;
    }

    #quill-editor .ql-editor.ql-blank::before {
        color: var(--text-6);
        font-style: normal;
    }

    #quill-editor .ql-editor::-webkit-scrollbar {
        width: 6px;
    }
    #quill-editor .ql-editor::-webkit-scrollbar-thumb {
        background: rgba(236, 188, 66, 0.3);
        border-radius: 100px;
    }
</style>


{{-- ============================================ --}}
{{-- SCRIPT --}}
{{-- ============================================ --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const hiddenInput = document.getElementById('privacy_content');
    const editorContainer = document.getElementById('quill-editor');
    const form = document.getElementById('privacy-form');

    let quill = null;

    // ============================================
    // QUILL.JS INITIALIZATION
    // ============================================
    if (editorContainer && typeof Quill !== 'undefined' && hiddenInput) {
        const toolbarOptions = [
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
            [{ 'indent': '-1' }, { 'indent': '+1' }],
            [{ 'align': [] }],
            ['blockquote', 'code-block'],
            ['link', 'image'],
            ['clean']
        ];

        quill = new Quill(editorContainer, {
            theme: 'snow',
            placeholder: 'Tulis konten kebijakan privasi di sini...',
            modules: { toolbar: toolbarOptions }
        });

        const initialContent = hiddenInput.value;
        if (initialContent) {
            quill.root.innerHTML = initialContent;
        }

        quill.on('text-change', function() {
            hiddenInput.value = quill.root.innerHTML;
        });
    }

    // ============================================
    // SUBMIT FORM - SYNC CONTENT
    // ============================================
    if (form) {
        form.addEventListener('submit', function(e) {
            if (quill) hiddenInput.value = quill.root.innerHTML;

            const content = hiddenInput.value.trim();
            if (!content || content === '<p><br></p>' || content === '<p></p>' || content === '') {
                e.preventDefault();
                alert('Konten kebijakan privasi wajib diisi!');
                editorContainer.focus();
                return false;
            }
            return true;
        });
    }

    // ============================================
    // TOGGLE STATUS
    // ============================================
    window.togglePrivacy = function() {
        if (confirm('Apakah Anda yakin ingin mengubah status Kebijakan Privasi?')) {
            if (quill) hiddenInput.value = quill.root.innerHTML;
            document.getElementById('toggle-form').submit();
        }
    };
});
</script>
@endpush

@endsection