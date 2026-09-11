@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Tentang Kami</h1>
        <p class="mt-1 text-sm text-gray-500">Kelola halaman tentang kami yang akan ditampilkan di toko.</p>
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
    <form action="{{ route('admin.about.update') }}" method="POST" class="space-y-6" id="about-form">
        @csrf
        @method('PUT')

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            {{-- STATUS BADGE --}}
            <div class="mb-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-sm font-medium text-gray-700">Status:</span>
                    <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $about && $about->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $about && $about->is_active ? '✅ Aktif' : '❌ Nonaktif' }}
                    </span>
                </div>
                @if($about)
                    <button type="button" 
                            onclick="toggleAbout()"
                            class="rounded-lg px-4 py-2 text-sm font-medium {{ $about->is_active ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' : 'bg-green-100 text-green-800 hover:bg-green-200' }}">
                        {{ $about->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                @endif
            </div>

            {{-- JUDUL --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Judul</label>
                <input type="text" name="title" 
                       value="{{ old('title', $about?->title ?? 'Tentang Kami Barokah Sport') }}"
                       class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       required>
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- 🔥 KONTEN UTAMA --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Konten Utama</label>
                
                {{-- Hidden input untuk form submission --}}
                <textarea name="content" id="about_content" style="display: none;">{{ old('content', $about?->content ?? '') }}</textarea>
                
                {{-- Quill Editor Container --}}
                <div id="quill-editor-content" class="mt-2 rounded-lg border border-gray-300 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500" style="min-height: 300px;">
                    {!! old('content', $about?->content ?? '') !!}
                </div>
                
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- 🔥 VISI --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Visi</label>
                
                <textarea name="vision" id="about_vision" style="display: none;">{{ old('vision', $about?->vision ?? '') }}</textarea>
                
                <div id="quill-editor-vision" class="mt-2 rounded-lg border border-gray-300 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500" style="min-height: 150px;">
                    {!! old('vision', $about?->vision ?? '') !!}
                </div>
                
                @error('vision')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- 🔥 MISI --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Misi</label>
                
                <textarea name="mission" id="about_mission" style="display: none;">{{ old('mission', $about?->mission ?? '') }}</textarea>
                
                <div id="quill-editor-mission" class="mt-2 rounded-lg border border-gray-300 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500" style="min-height: 150px;">
                    {!! old('mission', $about?->mission ?? '') !!}
                </div>
                
                @error('mission')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- VERSI --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Versi</label>
                    <input type="text" name="version" 
                           value="{{ old('version', $about?->version ?? '1.0') }}"
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
                           value="{{ old('effective_date', $about?->effective_date?->format('Y-m-d') ?? date('Y-m-d')) }}"
                           class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('effective_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- STATUS --}}
            <div class="mt-5 flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" 
                       {{ old('is_active', $about?->is_active ?? true) ? 'checked' : '' }}
                       class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="is_active" class="ml-2 block text-sm text-gray-700">Aktif</label>
            </div>
        </div>

        {{-- ACTION --}}
        <div class="flex justify-end rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <button type="submit" id="submit-about-btn"
                    class="inline-flex items-center rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Tentang Kami
            </button>
        </div>
    </form>

    {{-- FORM TOGGLE TERPISAH --}}
    @if($about)
        <form action="{{ route('admin.about.toggle') }}" method="POST" id="toggle-form" style="display: none;">
            @csrf
            @method('PATCH')
        </form>
    @endif

    {{-- PREVIEW --}}
    @if($about && $about->content)
        <div class="mt-6 rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">📄 Preview</h2>
            
            {{-- PREVIEW KONTEN UTAMA --}}
            <div class="prose prose-blue max-w-none border-t border-gray-200 pt-4">
                <h3 class="text-sm font-semibold text-gray-500 mb-2">Konten Utama</h3>
                {!! $about->content !!}
            </div>
            
            {{-- PREVIEW VISI --}}
            @if($about->vision)
                <div class="mt-4 border-t border-gray-200 pt-4">
                    <h3 class="text-sm font-semibold text-gray-500 mb-2">Visi</h3>
                    <div class="prose prose-blue max-w-none">
                        {!! $about->vision !!}
                    </div>
                </div>
            @endif
            
            {{-- PREVIEW MISI --}}
            @if($about->mission)
                <div class="mt-4 border-t border-gray-200 pt-4">
                    <h3 class="text-sm font-semibold text-gray-500 mb-2">Misi</h3>
                    <div class="prose prose-blue max-w-none">
                        {!! $about->mission !!}
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>

{{-- 🔥 QUILL.JS CDN --}}
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<style>
    /* Quill Editor Styling */
    .ql-editor {
        min-height: 150px !important;
        max-height: 500px !important;
        font-size: 14px;
        line-height: 1.8;
        background: #ffffff;
    }

    #quill-editor-content .ql-editor {
        min-height: 300px !important;
    }

    #quill-editor-vision .ql-editor,
    #quill-editor-mission .ql-editor {
        min-height: 150px !important;
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
    const hiddenContent = document.getElementById('about_content');
    const hiddenVision = document.getElementById('about_vision');
    const hiddenMission = document.getElementById('about_mission');
    
    const editorContent = document.getElementById('quill-editor-content');
    const editorVision = document.getElementById('quill-editor-vision');
    const editorMission = document.getElementById('quill-editor-mission');
    
    const form = document.getElementById('about-form');
    const submitBtn = document.getElementById('submit-about-btn');

    let quillContent = null;
    let quillVision = null;
    let quillMission = null;

    // ============================================
    // 🔥 TOOLBAR CONFIGURATION
    // ============================================

    const fullToolbar = [
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

    const simpleToolbar = [
        ['bold', 'italic', 'underline'],
        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
        ['clean']
    ];

    // ============================================
    // 🔥 QUILL.JS - KONTEN UTAMA
    // ============================================

    if (editorContent && typeof Quill !== 'undefined') {
        quillContent = new Quill(editorContent, {
            theme: 'snow',
            placeholder: 'Tulis konten tentang kami di sini...',
            modules: {
                toolbar: fullToolbar
            }
        });

        const initialContent = hiddenContent.value;
        if (initialContent) {
            quillContent.root.innerHTML = initialContent;
        }

        quillContent.on('text-change', function() {
            hiddenContent.value = quillContent.root.innerHTML;
        });

        console.log('✅ Quill.js - Content initialized');
    }

    // ============================================
    // 🔥 QUILL.JS - VISI
    // ============================================

    if (editorVision && typeof Quill !== 'undefined') {
        quillVision = new Quill(editorVision, {
            theme: 'snow',
            placeholder: 'Tulis visi perusahaan di sini...',
            modules: {
                toolbar: simpleToolbar
            }
        });

        const initialVision = hiddenVision.value;
        if (initialVision) {
            quillVision.root.innerHTML = initialVision;
        }

        quillVision.on('text-change', function() {
            hiddenVision.value = quillVision.root.innerHTML;
        });

        console.log('✅ Quill.js - Vision initialized');
    }

    // ============================================
    // 🔥 QUILL.JS - MISI
    // ============================================

    if (editorMission && typeof Quill !== 'undefined') {
        quillMission = new Quill(editorMission, {
            theme: 'snow',
            placeholder: 'Tulis misi perusahaan di sini...',
            modules: {
                toolbar: simpleToolbar
            }
        });

        const initialMission = hiddenMission.value;
        if (initialMission) {
            quillMission.root.innerHTML = initialMission;
        }

        quillMission.on('text-change', function() {
            hiddenMission.value = quillMission.root.innerHTML;
        });

        console.log('✅ Quill.js - Mission initialized');
    }

    // ============================================
    // 🔥 SUBMIT FORM - SYNC ALL CONTENT
    // ============================================

    function syncAllContent() {
        if (quillContent) {
            hiddenContent.value = quillContent.root.innerHTML;
        }
        if (quillVision) {
            hiddenVision.value = quillVision.root.innerHTML;
        }
        if (quillMission) {
            hiddenMission.value = quillMission.root.innerHTML;
        }
        console.log('📝 All Quill content synced');
    }

    function validateForm() {
        syncAllContent();

        const title = document.querySelector('input[name="title"]')?.value.trim();
        const content = hiddenContent.value.trim();

        if (!title) {
            alert('Judul tentang kami wajib diisi!');
            document.querySelector('input[name="title"]')?.focus();
            return false;
        }

        if (!content || content === '<p><br></p>' || content === '<p></p>' || content === '') {
            alert('Konten utama tentang kami wajib diisi!');
            editorContent.focus();
            return false;
        }

        return true;
    }

    // 🔥 Submit via button click
    if (submitBtn) {
        submitBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (validateForm()) {
                console.log('✅ Submitting form...');
                form.submit();
            }
        });
    }

    // 🔥 Submit via form submit event
    if (form) {
        form.addEventListener('submit', function(e) {
            syncAllContent();
            // Validasi tetap berjalan
            if (!validateForm()) {
                e.preventDefault();
                return false;
            }
        });
    }

    // ============================================
    // 🔥 TOGGLE STATUS
    // ============================================

    window.toggleAbout = function() {
        if (confirm('Apakah Anda yakin ingin mengubah status Tentang Kami?')) {
            syncAllContent();
            document.getElementById('toggle-form').submit();
        }
    };

    console.log('✅ About Us form initialized with Quill.js');
});
</script>
@endsection