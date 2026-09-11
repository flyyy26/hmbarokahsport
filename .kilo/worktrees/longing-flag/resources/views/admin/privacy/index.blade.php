@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Kebijakan Privasi</h1>
        <p class="mt-1 text-sm text-gray-500">Kelola kebijakan privasi yang berlaku di toko.</p>
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
    <form action="{{ route('admin.privacy.update') }}" method="POST" class="space-y-6" id="privacy-form">
        @csrf
        @method('PUT')

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            {{-- STATUS BADGE --}}
            <div class="mb-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-sm font-medium text-gray-700">Status:</span>
                    <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $privacy && $privacy->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $privacy && $privacy->is_active ? '✅ Aktif' : '❌ Nonaktif' }}
                    </span>
                </div>
                @if($privacy)
                    <button type="button" 
                            onclick="togglePrivacy()"
                            class="rounded-lg px-4 py-2 text-sm font-medium {{ $privacy->is_active ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' : 'bg-green-100 text-green-800 hover:bg-green-200' }}">
                        {{ $privacy->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                @endif
            </div>

            {{-- JUDUL --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Judul</label>
                <input type="text" name="title" 
                       value="{{ old('title', $privacy?->title ?? 'Kebijakan Privasi Barokah Sport') }}"
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
                <textarea name="content" id="privacy_content" style="display: none;">{{ old('content', $privacy?->content ?? '') }}</textarea>
                
                {{-- Quill Editor Container --}}
                <div id="quill-editor" class="mt-2 rounded-lg border border-gray-300 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500" style="min-height: 400px;">
                    {!! old('content', $privacy?->content ?? '') !!}
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
                           value="{{ old('version', $privacy?->version ?? '1.0') }}"
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
                           value="{{ old('effective_date', $privacy?->effective_date?->format('Y-m-d') ?? date('Y-m-d')) }}"
                           class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('effective_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- STATUS --}}
            <div class="mt-5 flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" 
                       {{ old('is_active', $privacy?->is_active ?? true) ? 'checked' : '' }}
                       class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="is_active" class="ml-2 block text-sm text-gray-700">Aktif</label>
            </div>
        </div>

        {{-- ACTION --}}
        <div class="flex justify-end rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <button type="submit" 
                    class="inline-flex items-center rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Kebijakan Privasi
            </button>
        </div>
    </form>

    {{-- FORM TOGGLE TERPISAH --}}
    @if($privacy)
        <form action="{{ route('admin.privacy.toggle') }}" method="POST" id="toggle-form" style="display: none;">
            @csrf
            @method('PATCH')
        </form>
    @endif
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
    const hiddenInput = document.getElementById('privacy_content');
    const editorContainer = document.getElementById('quill-editor');
    const form = document.getElementById('privacy-form');

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
            placeholder: 'Tulis konten kebijakan privasi di sini...',
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

        console.log('✅ Quill.js initialized for Privacy Policy');
    } else {
        console.error('❌ Quill.js not loaded');
    }

    // ============================================
    // 🔥 SUBMIT FORM - SYNC CONTENT
    // ============================================

    if (form) {
        form.addEventListener('submit', function(e) {
            // 🔥 SYNC QUILL CONTENT TO HIDDEN INPUT
            if (quill) {
                hiddenInput.value = quill.root.innerHTML;
                console.log('📝 Quill content saved before submit');
            }
            
            // 🔥 VALIDASI KONTEN
            const content = hiddenInput.value.trim();
            if (!content || content === '<p><br></p>' || content === '<p></p>' || content === '') {
                e.preventDefault();
                alert('Konten kebijakan privasi wajib diisi!');
                editorContainer.focus();
                return false;
            }
            
            console.log('✅ Form submitted successfully');
            return true;
        });
    }

    // ============================================
    // 🔥 TOGGLE STATUS
    // ============================================

    window.togglePrivacy = function() {
        if (confirm('Apakah Anda yakin ingin mengubah status Kebijakan Privasi?')) {
            // 🔥 SYNC QUILL CONTENT BEFORE TOGGLE
            if (quill) {
                hiddenInput.value = quill.root.innerHTML;
            }
            document.getElementById('toggle-form').submit();
        }
    };

    console.log('✅ Privacy Policy form initialized');
});
</script>
@endsection