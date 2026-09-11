@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-3xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit FAQ</h1>
        <p class="mt-1 text-sm text-gray-500">Perbarui pertanyaan yang sering diajukan.</p>
    </div>

    <form action="{{ route('admin.faqs.update', $faq) }}" method="POST" class="space-y-6" id="faq-form">
        @csrf
        @method('PUT')

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            {{-- PERTANYAAN --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Pertanyaan <span class="text-red-500">*</span></label>
                <input type="text" name="question" value="{{ old('question', $faq->question) }}"
                       class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       placeholder="Contoh: Bagaimana cara memesan produk?" required>
                @error('question')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- 🔥 JAWABAN DENGAN QUILL.JS --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Jawaban <span class="text-red-500">*</span></label>
                
                {{-- Hidden input untuk form submission --}}
                <textarea name="answer" id="faq_answer" style="display: none;">{{ old('answer', $faq->answer) }}</textarea>
                
                {{-- Quill Editor Container --}}
                <div id="quill-editor" class="mt-2 rounded-lg border border-gray-300 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500" style="min-height: 200px;">
                    {!! old('answer', $faq->answer) !!}
                </div>
                
                @error('answer')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- KATEGORI --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Kategori <span class="text-red-500">*</span></label>
                <select name="category_id" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    <option value="">Pilih Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $faq->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">
                    <a href="{{ route('admin.faqs.categories') }}" class="text-blue-600 hover:underline">Kelola Kategori</a>
                </p>
            </div>

            {{-- URUTAN --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Urutan</label>
                <input type="number" name="order" value="{{ old('order', $faq->order) }}"
                       class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       min="0">
                <p class="mt-1 text-xs text-gray-500">Semakin kecil angka, semakin atas tampilannya.</p>
                @error('order')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- STATUS --}}
            <div class="flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $faq->is_active) ? 'checked' : '' }}
                       class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label class="ml-2 block text-sm text-gray-700">Aktif</label>
            </div>
        </div>

        {{-- ACTION --}}
        <div class="flex justify-end gap-3 rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <a href="{{ route('admin.faqs.index') }}" 
               class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Batal
            </a>
            <button type="button" id="submit-faq-btn"
                    class="inline-flex items-center rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                Update FAQ
            </button>
        </div>
    </form>
</div>

{{-- 🔥 QUILL.JS CDN --}}
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<style>
    /* Quill Editor Styling */
    .ql-editor {
        min-height: 200px !important;
        max-height: 400px !important;
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
        min-height: 200px;
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
    const hiddenInput = document.getElementById('faq_answer');
    const editorContainer = document.getElementById('quill-editor');
    const form = document.getElementById('faq-form');
    const submitBtn = document.getElementById('submit-faq-btn');

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
            ['link'],
            ['clean']
        ];

        quill = new Quill(editorContainer, {
            theme: 'snow',
            placeholder: 'Tuliskan jawaban lengkap di sini...',
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

        console.log('✅ Quill.js initialized for FAQ Edit');
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

        // 🔥 VALIDASI
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

    // 🔥 Submit via form submit event
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!submitForm()) {
                e.preventDefault();
                return false;
            }
        });
    }

    console.log('✅ FAQ Edit form initialized with Quill.js');
});
</script>
@endsection