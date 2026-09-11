@extends('layouts.admin')

@section('title', 'Tambah FAQ')
@section('page-title', 'Tambah FAQ')

@section('content')

<div class="w-full max-w-4xl mx-auto space-y-6">

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
                    <iconify-icon icon="mdi:help-circle-outline" class="text-slate-900 text-2xl"></iconify-icon>
                </span>
                Tambah FAQ
            </h1>
            <p class="text-sm mt-1.5 ml-12" style="color: var(--text-5)">
                Tambahkan pertanyaan baru untuk pelanggan.
            </p>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- FORM --}}
    {{-- ============================================ --}}
    <form action="{{ route('admin.faqs.store') }}"
          method="POST"
          id="faq-form"
          class="space-y-6">
        @csrf

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
        {{-- SECTION 1: INFORMASI FAQ --}}
        {{-- ============================================ --}}
        <div class="rounded-2xl border overflow-hidden"
             style="background: var(--bg-card); border-color: var(--border-2)">

            <div class="px-5 py-4 border-b flex items-center gap-2"
                 style="background: var(--bg-input); border-color: var(--border-2)">
                <iconify-icon icon="mdi:information-outline" class="text-[#ecbc42] text-base"></iconify-icon>
                <h2 class="font-bold text-sm" style="color: var(--text-1)">Informasi FAQ</h2>
            </div>

            <div class="p-5 space-y-5">

                {{-- Pertanyaan --}}
                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:comment-question-outline" class="text-[#ecbc42]"></iconify-icon>
                        Pertanyaan <span class="text-red-400">*</span>
                    </label>
                    <input type="text"
                           name="question"
                           value="{{ old('question') }}"
                           required
                           class="form-input"
                           placeholder="Contoh: Bagaimana cara memesan produk?">
                    @error('question')
                        <p class="text-[10px] mt-1 text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Jawaban --}}
                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:comment-text-outline" class="text-[#ecbc42]"></iconify-icon>
                        Jawaban <span class="text-red-400">*</span>
                    </label>

                    {{-- Hidden input untuk form submission --}}
                    <textarea name="answer" id="faq_answer" style="display: none;">{{ old('answer') }}</textarea>

                    {{-- Quill Editor Container --}}
                    <div id="quill-editor"
                         class="rounded-lg border overflow-hidden"
                         style="border-color: var(--border-2); min-height: 200px;">
                        {!! old('answer') !!}
                    </div>

                    @error('answer')
                        <p class="text-[10px] mt-1 text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kategori --}}
                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:folder-outline" class="text-[#ecbc42]"></iconify-icon>
                        Kategori <span class="text-red-400">*</span>
                    </label>
                    <select name="category_id" class="form-input" required>
                        <option value="">Pilih Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-[10px] mt-1 text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="text-[10px] mt-1.5 flex items-center gap-1" style="color: var(--text-5);">
                        <iconify-icon icon="mdi:information-outline"></iconify-icon>
                        Belum ada kategori?
                        <a href="{{ route('admin.faqs.categories') }}"
                           class="font-semibold transition-colors"
                           style="color: #ecbc42;"
                           onmouseover="this.style.color='#FDDD57'"
                           onmouseout="this.style.color='#ecbc42'">
                            Kelola Kategori
                        </a>
                    </p>
                </div>

                {{-- Urutan --}}
                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:sort-numeric-ascending" class="text-[#ecbc42]"></iconify-icon>
                        Urutan
                    </label>
                    <input type="number"
                           name="order"
                           value="{{ old('order', isset($faqs) ? $faqs->count() + 1 : 1) }}"
                           min="0"
                           class="form-input"
                           placeholder="1">
                    <p class="text-[10px] mt-1.5 flex items-center gap-1" style="color: var(--text-5);">
                        <iconify-icon icon="mdi:information-outline"></iconify-icon>
                        Semakin kecil angka, semakin atas tampilannya.
                    </p>
                    @error('order')
                        <p class="text-[10px] mt-1 text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>


        {{-- ============================================ --}}
        {{-- SECTION 2: STATUS --}}
        {{-- ============================================ --}}
        <div class="rounded-2xl border overflow-hidden"
             style="background: var(--bg-card); border-color: var(--border-2)">

            <div class="px-5 py-4 border-b flex items-center gap-2"
                 style="background: var(--bg-input); border-color: var(--border-2)">
                <iconify-icon icon="mdi:shield-check-outline" class="text-[#ecbc42] text-base"></iconify-icon>
                <h2 class="font-bold text-sm" style="color: var(--text-1)">Status FAQ</h2>
            </div>

            <div class="p-5">
                <label class="flex items-start gap-3 cursor-pointer p-3 rounded-lg border transition-all"
                       style="background: var(--bg-input); border-color: var(--border-2);"
                       onmouseover="this.style.borderColor='rgba(52,211,153,0.3)'"
                       onmouseout="this.style.borderColor='var(--border-2)'">
                    <input type="checkbox"
                           name="is_active"
                           value="1"
                           checked
                           class="mt-0.5 h-4 w-4 rounded cursor-pointer"
                           style="accent-color: #ecbc42;">
                    <div>
                        <span class="text-sm font-semibold flex items-center gap-1.5" style="color: var(--text-1);">
                            <iconify-icon icon="mdi:check-circle-outline" class="text-emerald-400"></iconify-icon>
                            Aktifkan FAQ
                        </span>
                        <p class="text-[10px] mt-0.5" style="color: var(--text-5);">
                            FAQ akan tampil di halaman publik.
                        </p>
                    </div>
                </label>
            </div>
        </div>


        {{-- ============================================ --}}
        {{-- TOMBOL AKSI --}}
        {{-- ============================================ --}}
        <div class="flex items-center justify-end gap-3 pb-4">
            <a href="{{ route('admin.faqs.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg
                      text-sm font-semibold transition-all active:scale-95 border"
               style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)"
               onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
               onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'">
                <iconify-icon icon="mdi:arrow-left"></iconify-icon>
                Batal
            </a>
            <button type="button" id="submit-faq-btn"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg
                           text-sm font-bold transition-all active:scale-95
                           bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                           text-slate-900
                           shadow-lg shadow-amber-500/20
                           hover:shadow-xl hover:shadow-amber-500/40
                           hover:-translate-y-0.5">
                <iconify-icon icon="mdi:content-save-outline" class="text-base"></iconify-icon>
                Simpan FAQ
            </button>
        </div>
    </form>
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
        min-height: 200px !important;
        max-height: 400px !important;
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
    const hiddenInput = document.getElementById('faq_answer');
    const editorContainer = document.getElementById('quill-editor');
    const form = document.getElementById('faq-form');
    const submitBtn = document.getElementById('submit-faq-btn');

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
            ['link'],
            ['clean']
        ];

        quill = new Quill(editorContainer, {
            theme: 'snow',
            placeholder: 'Tuliskan jawaban lengkap di sini...',
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
    function submitForm() {
        if (quill) hiddenInput.value = quill.root.innerHTML;

        const question = document.querySelector('input[name="question"]')?.value.trim();
        const answer = hiddenInput.value.trim();
        const category = document.querySelector('select[name="category_id"]')?.value;

        if (!question) {
            alert('Pertanyaan wajib diisi!');
            document.querySelector('input[name="question"]')?.focus();
            return false;
        }

        if (!category) {
            alert('Kategori wajib dipilih!');
            document.querySelector('select[name="category_id"]')?.focus();
            return false;
        }

        if (!answer || answer === '<p><br></p>' || answer === '<p></p>' || answer === '') {
            alert('Jawaban wajib diisi!');
            editorContainer.focus();
            return false;
        }

        return true;
    }

    if (submitBtn) {
        submitBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (submitForm()) form.submit();
        });
    }

    if (form) {
        form.addEventListener('submit', function(e) {
            if (!submitForm()) {
                e.preventDefault();
                return false;
            }
        });
    }
});
</script>
@endpush

@endsection