@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Syarat & Ketentuan</h1>
        <p class="mt-1 text-sm text-gray-500">Kelola syarat dan ketentuan yang berlaku di toko.</p>
    </div>

    {{-- SUCCESS & ERROR --}}
    @if (session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- FORM --}}
    <form action="{{ route('admin.terms.update') }}" method="POST" class="space-y-6" id="terms-form">
        @csrf
        @method('PUT')

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            {{-- STATUS BADGE --}}
            <div class="mb-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-sm font-medium text-gray-700">Status:</span>
                    <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $term && $term->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $term && $term->is_active ? '✅ Aktif' : '❌ Nonaktif' }}
                    </span>
                </div>
                @if($term)
                    <form action="{{ route('admin.terms.toggle') }}" method="POST" class="inline" id="toggle-form">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="rounded-lg px-4 py-2 text-sm font-medium {{ $term->is_active ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' : 'bg-green-100 text-green-800 hover:bg-green-200' }}">
                            {{ $term->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                @endif
            </div>

            {{-- JUDUL --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Judul</label>
                <input type="text" name="title" 
                       value="{{ old('title', $term?->title ?? 'Syarat & Ketentuan Barokah Sport') }}"
                       class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       required>
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- 🔥 KONTEN DENGAN QUILL.JS --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Konten</label>
                
                {{-- Hidden input untuk form submission --}}
                <textarea name="content" id="terms_content" style="display: none;">{{ old('content', $term?->content ?? '') }}</textarea>
                
                {{-- Quill Editor Container --}}
                <div id="quill-editor" class="mt-2 rounded-lg border border-gray-300 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500" style="min-height: 400px;">
                    {!! old('content', $term?->content ?? '') !!}
                </div>
                
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- VERSI --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Versi</label>
                    <input type="text" name="version" 
                           value="{{ old('version', $term?->version ?? '1.0') }}"
                           class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Contoh: 1.0">
                    @error('version')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- TANGGAL EFEKTIF --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Efektif</label>
                    <input type="date" name="effective_date" 
                           value="{{ old('effective_date', $term?->effective_date?->format('Y-m-d') ?? date('Y-m-d')) }}"
                           class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('effective_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- STATUS --}}
            <div class="mt-5 flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" 
                       {{ old('is_active', $term?->is_active ?? true) ? 'checked' : '' }}
                       class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="is_active" class="ml-2 block text-sm text-gray-700">Aktif</label>
            </div>
        </div>

        {{-- ACTION --}}
        <div class="flex justify-end rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <button type="submit" id="submit-terms-btn"
                    class="inline-flex items-center rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Syarat & Ketentuan
            </button>
        </div>
    </form>
</div>

{{-- 🔥 QUILL.JS CDN --}}
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<style>
    /* Quill Editor Styling */
    #quill-editor {
        min-height: 400px !important;
    }

    .ql-editor {
        min-height: 400px !important;
        max-height: 600px !important;
        font-size: 14px;
        line-height: 1.8;
        background: #ffffff;
    }

    .ql-toolbar.ql-snow {
        border-radius: 8px 8px 0 0;
        border-color: #d1d5db !important;
        background: #f9fafb;
    }

    .ql-container.ql-snow {
        border-radius: 0 0 8px 8px;
        border-color: #d1d5db !important;
        background: white;
        min-height: 400px;
    }

    .ql-container.ql-snow:focus-within {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* Dark mode support */
    .dark .ql-toolbar.ql-snow {
        background: #1f2937;
        border-color: #374151 !important;
    }

    .dark .ql-container.ql-snow {
        background: #1f2937;
        border-color: #374151 !important;
    }

    .dark .ql-editor {
        color: #e5e7eb;
    }

    .dark .ql-editor.ql-blank::before {
        color: #6b7280;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const hiddenInput = document.getElementById('terms_content');
    const editorContainer = document.getElementById('quill-editor');
    const form = document.getElementById('terms-form');
    const submitBtn = document.getElementById('submit-terms-btn');

    let quill = null;

    // ============================================
    // 🔥 QUILL.JS INITIALIZATION
    // ============================================

    if (editorContainer && typeof Quill !== 'undefined') {
        // 🔥 Toolbar configuration
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
            placeholder: 'Tulis konten syarat dan ketentuan di sini...',
            modules: {
                toolbar: toolbarOptions
            }
        });

        // 🔥 Set initial content
        const initialContent = hiddenInput.value;
        if (initialContent) {
            quill.root.innerHTML = initialContent;
        }

        // 🔥 Sync to hidden input on change
        quill.on('text-change', function() {
            hiddenInput.value = quill.root.innerHTML;
        });

        console.log('✅ Quill.js initialized for Terms & Conditions');
    } else {
        console.error('❌ Quill.js not loaded');
    }

    // ============================================
    // 🔥 SUBMIT FORM - SYNC CONTENT
    // ============================================

    function submitForm() {
        // 🔥 SYNC QUILL CONTENT TO HIDDEN INPUT
        if (quill) {
            hiddenInput.value = quill.root.innerHTML;
            console.log('📝 Quill content saved before submit');
        }

        // 🔥 VALIDASI KONTEN
        const content = hiddenInput.value.trim();
        const title = document.querySelector('input[name="title"]')?.value.trim();

        if (!title) {
            alert('Judul syarat & ketentuan wajib diisi!');
            document.querySelector('input[name="title"]')?.focus();
            return false;
        }

        if (!content || content === '<p><br></p>' || content === '<p></p>' || content === '') {
            alert('Konten syarat & ketentuan wajib diisi!');
            editorContainer.focus();
            return false;
        }

        console.log('✅ Form submitted successfully');
        return true;
    }

    // 🔥 Submit via button click
    if (submitBtn) {
        submitBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (submitForm()) {
                form.submit();
            }
        });
    }

    // 🔥 Submit via form submit event (for toggle button inside form)
    if (form) {
        form.addEventListener('submit', function(e) {
            // Cek apakah ini submit dari toggle button di dalam form
            const isToggle = e.submitter && e.submitter.closest('#toggle-form');
            if (isToggle) {
                // Toggle button sudah handle sendiri
                return true;
            }
            
            // Untuk submit normal, validasi
            if (!submitForm()) {
                e.preventDefault();
                return false;
            }
        });
    }

    console.log('✅ Terms & Conditions form initialized');
});
</script>
@endsection