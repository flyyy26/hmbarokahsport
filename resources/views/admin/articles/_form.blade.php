@php
    $isEdit = $isEdit ?? false;
    $article = $article ?? null;
@endphp

<style>
    .char-counter {
        font-size: 0.65rem;
        font-family: 'SF Mono', 'Monaco', 'Consolas', monospace;
        color: var(--text-5);
        transition: color 0.2s ease;
        user-select: none;
    }

    .char-counter.warning {
        color: #f59e0b;
        font-weight: 600;
    }

    .char-counter.danger {
        color: #ef4444;
        font-weight: 700;
    }

    .char-counter.full {
        color: #10b981;
        font-weight: 700;
    }

</style>

{{-- ============================================ --}}
{{-- ERROR VALIDATION --}}
{{-- ============================================ --}}
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
{{-- SECTION 1: INFORMASI UTAMA --}}
{{-- ============================================ --}}
<div class="rounded-2xl border overflow-hidden"
     style="background: var(--bg-card); border-color: var(--border-2)">

    <div class="px-5 py-4 border-b flex items-center gap-2"
         style="background: var(--bg-input); border-color: var(--border-2)">
        <iconify-icon icon="mdi:information-outline" class="text-[#ecbc42] text-base"></iconify-icon>
        <h2 class="font-bold text-sm" style="color: var(--text-1)">Informasi Utama</h2>
    </div>

    <div class="p-5 space-y-5">

        {{-- Judul --}}
        <div>
            <label class="form-label">
                <iconify-icon icon="mdi:format-title" class="text-[#ecbc42]"></iconify-icon>
                Judul Artikel <span class="text-red-400">*</span>
            </label>
            <input type="text"
                name="title"
                id="title"
                value="{{ old('title', $article->title ?? '') }}"
                required
                maxlength="60"
                data-counter-target="title-counter"
                data-max-length="60"
                class="form-input"
                placeholder="Contoh: Tips Memilih Sepatu Running untuk Pemula">
            <div class="flex items-center justify-between mt-1">
                @error('title')
                    <p class="text-[10px] text-red-400">{{ $message }}</p>
                @else
                    <p class="text-[10px]" style="color: var(--text-5);">
                        Judul singkat & jelas, maks 60 karakter.
                    </p>
                @enderror
                <p class="text-[10px] font-mono" id="title-counter" style="color: var(--text-5);">
                    <span class="counter-current">0</span>/<span class="counter-max">60</span>
                </p>
            </div>
        </div>

        {{-- Kategori + Action Buttons --}}
        <div>
            <label class="form-label">
                <iconify-icon icon="mdi:folder-outline" class="text-[#ecbc42]"></iconify-icon>
                Kategori <span class="text-red-400">*</span>
            </label>
            <div class="flex gap-2">
                <select name="article_category_id"
                        id="article_category_id"
                        required
                        class="form-input flex-1">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}"
                                {{ (old('article_category_id', $article->article_category_id ?? '') == $cat->id) ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>

                <button type="button"
                        id="btn-add-category"
                        class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg
                               text-xs font-bold transition-all active:scale-95 whitespace-nowrap
                               bg-emerald-500/10 border border-emerald-500/30 text-emerald-400
                               hover:bg-emerald-500/20 hover:border-emerald-500/50">
                    <iconify-icon icon="mdi:plus" class="text-base"></iconify-icon>
                    Tambah
                </button>

                <button type="button"
                        id="btn-delete-category"
                        disabled
                        class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg
                               text-xs font-bold transition-all active:scale-95 whitespace-nowrap
                               bg-red-500/5 border border-red-500/20 text-red-400
                               hover:bg-red-500/10 hover:border-red-500/40
                               disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-red-500/5">
                    <iconify-icon icon="mdi:delete-outline" class="text-base"></iconify-icon>
                    Hapus
                </button>
            </div>
            @error('article_category_id')
                <p class="text-[10px] mt-1 text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Author + Tags (Grid) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Author --}}
            <div>
                <label class="form-label">
                    <iconify-icon icon="mdi:account-outline" class="text-[#ecbc42]"></iconify-icon>
                    Penulis
                </label>
                <input type="text"
                       name="author"
                       id="author"
                       value="{{ old('author', $article->author ?? 'Admin') }}"
                       class="form-input"
                       placeholder="Nama penulis">
            </div>

            {{-- Tags --}}
            <div>
                <label class="form-label">
                    <iconify-icon icon="mdi:tag-multiple-outline" class="text-[#ecbc42]"></iconify-icon>
                    Tags <span class="text-[10px] font-normal" style="color: var(--text-5);">(pisahkan dengan koma)</span>
                </label>
                <input type="text"
                       name="tags"
                       id="tags"
                       value="{{ old('tags', isset($article) ? $article->tags_string : '') }}"
                       class="form-input"
                       placeholder="Contoh: olahraga, jaket, running">
            </div>
        </div>

        {{-- Excerpt --}}
        <div>
            <label class="form-label">
                <iconify-icon icon="mdi:text-short" class="text-[#ecbc42]"></iconify-icon>
                Deskripsi Pendek (Excerpt)
            </label>
            <textarea name="excerpt"
                    id="excerpt"
                    rows="3"
                    maxlength="160"
                    data-counter-target="excerpt-counter"
                    data-max-length="160"
                    class="form-input"
                    placeholder="Ringkasan singkat artikel, maks 160 karakter...">{{ old('excerpt', $article->excerpt ?? '') }}</textarea>
            <div class="flex items-center justify-between mt-1">
                <p class="text-[10px] flex items-center gap-1" style="color: var(--text-5);">
                    <iconify-icon icon="mdi:information-outline"></iconify-icon>
                    Dipakai untuk meta description SEO (ideal 120–160 karakter).
                </p>
                <p class="text-[10px] font-mono" id="excerpt-counter" style="color: var(--text-5);">
                    <span class="counter-current">0</span>/<span class="counter-max">160</span>
                </p>
            </div>
        </div>
    </div>
</div>


{{-- ============================================ --}}
{{-- SECTION 2: KONTEN ARTIKEL --}}
{{-- ============================================ --}}
<div class="rounded-2xl border overflow-hidden"
     style="background: var(--bg-card); border-color: var(--border-2)">

    <div class="px-5 py-4 border-b flex items-center gap-2"
         style="background: var(--bg-input); border-color: var(--border-2)">
        <iconify-icon icon="mdi:text-box-edit-outline" class="text-[#ecbc42] text-base"></iconify-icon>
        <h2 class="font-bold text-sm" style="color: var(--text-1)">Konten Artikel</h2>
    </div>

    <div class="p-5">
        <label class="form-label">
            <iconify-icon icon="mdi:text-box-outline" class="text-[#ecbc42]"></iconify-icon>
            Isi Konten <span class="text-red-400">*</span>
        </label>

        {{-- Hidden input untuk form submission --}}
        <textarea name="content"
                  id="content"
                  style="display: none;">{{ old('content', $article->content ?? '') }}</textarea>

        {{-- Quill Editor Container --}}
        <div id="quill-editor"
             class="rounded-lg border overflow-hidden"
             style="border-color: var(--border-2); min-height: 300px;">
            {!! old('content', $article->content ?? '') !!}
        </div>

        @error('content')
            <p class="text-[10px] mt-1 text-red-400">{{ $message }}</p>
        @enderror
    </div>
</div>


{{-- ============================================ --}}
{{-- SECTION 3: GAMBAR & PUBLIKASI --}}
{{-- ============================================ --}}
<div class="rounded-2xl border overflow-hidden"
     style="background: var(--bg-card); border-color: var(--border-2)">

    <div class="px-5 py-4 border-b flex items-center gap-2"
         style="background: var(--bg-input); border-color: var(--border-2)">
        <iconify-icon icon="mdi:image-outline" class="text-[#ecbc42] text-base"></iconify-icon>
        <h2 class="font-bold text-sm" style="color: var(--text-1)">Gambar & Publikasi</h2>
    </div>

    <div class="p-5 space-y-5">

        {{-- Gambar Utama --}}
        <div>
            <label class="form-label">
                <iconify-icon icon="mdi:image-plus-outline" class="text-[#ecbc42]"></iconify-icon>
                Gambar Utama Artikel
            </label>

            <input type="file"
                   name="image"
                   id="image"
                   accept="image/jpeg,image/png,image/webp"
                   class="form-input file:mr-4 file:py-1.5 file:px-4 file:rounded-lg file:border-0
                          file:text-xs file:font-bold file:cursor-pointer
                          file:bg-gradient-to-r file:from-[#FDDD57] file:to-[#ecbc42]
                          file:text-slate-900
                          hover:file:shadow-lg hover:file:shadow-amber-500/30">

            <p class="text-[10px] mt-1.5 flex items-center gap-1" style="color: var(--text-5);">
                <iconify-icon icon="mdi:information-outline"></iconify-icon>
                Maksimal 2MB. Format JPG, PNG, atau WebP.
            </p>

            @error('image')
                <p class="text-[10px] mt-1 text-red-400">{{ $message }}</p>
            @enderror

            {{-- Preview Gambar Existing --}}
            @if(isset($article) && $article->image)
                <div class="mt-4 flex items-start gap-4 p-3 rounded-lg border"
                     style="background: var(--bg-input); border-color: var(--border-2);">
                    <img src="{{ $article->image_url }}"
                         alt="{{ $article->title }}"
                         class="w-24 h-24 rounded-lg object-cover border flex-shrink-0"
                         style="border-color: var(--border-2);">
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-wider mb-1" style="color: var(--text-5);">
                            Gambar Saat Ini
                        </p>
                        <p class="text-xs" style="color: var(--text-3);">
                            Upload gambar baru untuk mengganti.
                        </p>
                    </div>
                </div>
            @endif

            {{-- Preview Gambar Baru --}}
            <div id="image-preview" class="mt-4"></div>
        </div>

        {{-- Tanggal Publikasi --}}
        <div>
            <label class="form-label">
                <iconify-icon icon="mdi:calendar-clock-outline" class="text-[#ecbc42]"></iconify-icon>
                Tanggal Publikasi
            </label>
            <input type="datetime-local"
                   name="published_at"
                   id="published_at"
                   value="{{ old('published_at', isset($article) && $article->published_at ? $article->published_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
                   class="form-input">
            <p class="text-[10px] mt-1 flex items-center gap-1" style="color: var(--text-5);">
                <iconify-icon icon="mdi:information-outline"></iconify-icon>
                Tanggal artikel akan dipublikasikan.
            </p>
        </div>
    </div>
</div>


{{-- ============================================ --}}
{{-- SECTION 4: STATUS --}}
{{-- ============================================ --}}
<div class="rounded-2xl border overflow-hidden"
     style="background: var(--bg-card); border-color: var(--border-2)">

    <div class="px-5 py-4 border-b flex items-center gap-2"
         style="background: var(--bg-input); border-color: var(--border-2)">
        <iconify-icon icon="mdi:shield-check-outline" class="text-[#ecbc42] text-base"></iconify-icon>
        <h2 class="font-bold text-sm" style="color: var(--text-1)">Status Artikel</h2>
    </div>

    <div class="p-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            {{-- Aktif --}}
            <label class="flex items-start gap-3 cursor-pointer p-3 rounded-lg border transition-all"
                   style="background: var(--bg-input); border-color: var(--border-2);"
                   onmouseover="this.style.borderColor='rgba(52,211,153,0.3)'"
                   onmouseout="this.style.borderColor='var(--border-2)'">
                <input type="checkbox"
                       name="is_active"
                       value="1"
                       @checked(old('is_active', $article->is_active ?? true))
                       class="mt-0.5 h-4 w-4 rounded cursor-pointer"
                       style="accent-color: #ecbc42;">
                <div>
                    <span class="text-sm font-semibold flex items-center gap-1.5" style="color: var(--text-1);">
                        <iconify-icon icon="mdi:check-circle-outline" class="text-emerald-400"></iconify-icon>
                        Aktifkan Artikel
                    </span>
                    <p class="text-[10px] mt-0.5" style="color: var(--text-5);">
                        Artikel akan tampil di halaman publik.
                    </p>
                </div>
            </label>

            {{-- Featured --}}
            <label class="flex items-start gap-3 cursor-pointer p-3 rounded-lg border transition-all"
                   style="background: var(--bg-input); border-color: var(--border-2);"
                   onmouseover="this.style.borderColor='rgba(251,191,36,0.3)'"
                   onmouseout="this.style.borderColor='var(--border-2)'">
                <input type="checkbox"
                       name="is_featured"
                       value="1"
                       @checked(old('is_featured', $article->is_featured ?? false))
                       class="mt-0.5 h-4 w-4 rounded cursor-pointer"
                       style="accent-color: #ecbc42;">
                <div>
                    <span class="text-sm font-semibold flex items-center gap-1.5" style="color: var(--text-1);">
                        <iconify-icon icon="mdi:star" class="text-amber-400"></iconify-icon>
                        Artikel Unggulan
                    </span>
                    <p class="text-[10px] mt-0.5" style="color: var(--text-5);">
                        Tandai sebagai artikel featured di halaman utama.
                    </p>
                </div>
            </label>
        </div>
    </div>
</div>


{{-- ============================================ --}}
{{-- MODAL TAMBAH KATEGORI --}}
{{-- ============================================ --}}
<div id="modal-add-category"
     class="fixed inset-0 z-50 flex items-center justify-center hidden"
     style="background: rgba(0,0,0,0.75); backdrop-filter: blur(4px);">

    <div class="w-full max-w-md mx-4 rounded-2xl border overflow-hidden"
         style="background: var(--bg-card); border-color: var(--border-2);">

        {{-- Modal Header --}}
        <div class="px-5 py-4 border-b flex items-center gap-3"
             style="background: var(--bg-input); border-color: var(--border-2);">
            <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg
                         bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]">
                <iconify-icon icon="mdi:folder-plus-outline" class="text-slate-900 text-lg"></iconify-icon>
            </span>
            <h3 class="text-base font-bold flex-1" style="color: var(--text-1);">Tambah Kategori</h3>
            <button type="button"
                    onclick="closeCategoryModal()"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                           transition-all active:scale-95"
                    style="color: var(--text-5);"
                    onmouseover="this.style.background='var(--bg-hover)'; this.style.color='#f87171'"
                    onmouseout="this.style.background='transparent'; this.style.color='var(--text-5)'">
                <iconify-icon icon="mdi:close"></iconify-icon>
            </button>
        </div>

        {{-- Modal Body --}}
        <div class="p-5">
            <label class="form-label">
                <iconify-icon icon="mdi:folder-outline" class="text-[#ecbc42]"></iconify-icon>
                Nama Kategori <span class="text-red-400">*</span>
            </label>
            <input type="text"
                   id="new-category-name"
                   class="form-input"
                   placeholder="Masukkan nama kategori..."
                   required>
        </div>

        {{-- Modal Footer --}}
        <div class="px-5 py-4 border-t flex items-center justify-end gap-2"
             style="background: var(--bg-input); border-color: var(--border-2);">
            <button type="button"
                    onclick="closeCategoryModal()"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg
                           text-xs font-semibold transition-all active:scale-95 border"
                    style="background: var(--bg-card); border-color: var(--border-2); color: var(--text-3)"
                    onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                    onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'">
                <iconify-icon icon="mdi:close"></iconify-icon>
                Batal
            </button>
            <button type="button"
                    id="btn-save-category"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg
                           text-xs font-bold transition-all active:scale-95
                           bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                           text-slate-900
                           hover:shadow-lg hover:shadow-amber-500/30">
                <iconify-icon icon="mdi:content-save-outline"></iconify-icon>
                Simpan Kategori
            </button>
        </div>
    </div>
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
        min-height: 300px !important;
        max-height: 500px !important;
        color: var(--text-2);
        font-size: 0.875rem;
        line-height: 1.6;
    }

    #quill-editor .ql-editor.ql-blank::before {
        color: var(--text-6);
        font-style: normal;
    }

    /* Scrollbar untuk editor */
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

    // ============================================
    // 🔥 QUILL.JS INITIALIZATION
    // ============================================
    const hiddenInput = document.getElementById('content');
    const editorContainer = document.getElementById('quill-editor');
    let quill = null;

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
            placeholder: 'Tulis konten artikel di sini...',
            modules: { toolbar: toolbarOptions }
        });

        const initialContent = hiddenInput.value;
        if (initialContent) {
            quill.root.innerHTML = initialContent;
        }

        quill.on('text-change', function() {
            hiddenInput.value = quill.root.innerHTML;
        });

        console.log('✅ Quill.js initialized');
    }

    // ============================================
    // 🔥 KATEGORI MANAGEMENT
    // ============================================
    const selectCategory = document.getElementById('article_category_id');
    const btnAdd = document.getElementById('btn-add-category');
    const btnDelete = document.getElementById('btn-delete-category');
    const modal = document.getElementById('modal-add-category');
    const newCategoryName = document.getElementById('new-category-name');
    const btnSaveCategory = document.getElementById('btn-save-category');

    // Update delete button state
    function updateDeleteButton() {
        if (btnDelete && selectCategory) {
            btnDelete.disabled = !selectCategory.value;
        }
    }

    if (selectCategory) {
        selectCategory.addEventListener('change', updateDeleteButton);
        updateDeleteButton();
    }

    // Open modal
    if (btnAdd && modal) {
        btnAdd.addEventListener('click', function(e) {
            e.preventDefault();
            modal.classList.remove('hidden');
            if (newCategoryName) newCategoryName.focus();
        });
    }

    // Close modal
    window.closeCategoryModal = function() {
        if (modal) modal.classList.add('hidden');
        if (newCategoryName) newCategoryName.value = '';
    };

    // Save category
    if (btnSaveCategory) {
        btnSaveCategory.addEventListener('click', saveCategory);
    }

    if (newCategoryName) {
        newCategoryName.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                saveCategory();
            }
        });
    }

    function saveCategory() {
        const name = newCategoryName.value.trim();
        if (!name) {
            alert('Nama kategori wajib diisi!');
            newCategoryName.focus();
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        btnSaveCategory.disabled = true;
        btnSaveCategory.innerHTML = '<iconify-icon icon="mdi:loading" class="animate-spin"></iconify-icon> Menyimpan...';

        fetch('{{ route("admin.article-categories.ajax.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name: name })
        })
        .then(response => response.json())
        .then(data => {
            btnSaveCategory.disabled = false;
            btnSaveCategory.innerHTML = '<iconify-icon icon="mdi:content-save-outline"></iconify-icon> Simpan Kategori';

            if (data.success) {
                const option = document.createElement('option');
                option.value = data.category.id;
                option.textContent = data.category.name;
                selectCategory.appendChild(option);
                selectCategory.value = data.category.id;
                updateDeleteButton();
                closeCategoryModal();
            } else {
                alert(data.message || 'Gagal menambahkan kategori');
            }
        })
        .catch(() => {
            btnSaveCategory.disabled = false;
            btnSaveCategory.innerHTML = '<iconify-icon icon="mdi:content-save-outline"></iconify-icon> Simpan Kategori';
            alert('Terjadi kesalahan. Silakan coba lagi.');
        });
    }

    // Delete category
    if (btnDelete) {
        btnDelete.addEventListener('click', function(e) {
            e.preventDefault();

            const categoryId = selectCategory.value;
            const categoryName = selectCategory.options[selectCategory.selectedIndex]?.text;

            if (!categoryId) {
                alert('Pilih kategori yang akan dihapus.');
                return;
            }

            if (!confirm(`Hapus kategori "${categoryName}"?`)) return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

            btnDelete.disabled = true;
            btnDelete.innerHTML = '<iconify-icon icon="mdi:loading" class="animate-spin"></iconify-icon>';

            fetch(`{{ url('admin/article-categories/ajax') }}/${categoryId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                btnDelete.innerHTML = '<iconify-icon icon="mdi:delete-outline" class="text-base"></iconify-icon> Hapus';

                if (data.success) {
                    const option = selectCategory.querySelector(`option[value="${categoryId}"]`);
                    if (option) option.remove();
                    selectCategory.value = '';
                    updateDeleteButton();
                } else {
                    alert(data.message || 'Gagal menghapus kategori');
                    updateDeleteButton();
                }
            })
            .catch(() => {
                btnDelete.innerHTML = '<iconify-icon icon="mdi:delete-outline" class="text-base"></iconify-icon> Hapus';
                updateDeleteButton();
                alert('Terjadi kesalahan. Silakan coba lagi.');
            });
        });
    }

    // Close modal on backdrop click
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) closeCategoryModal();
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
            closeCategoryModal();
        }
    });

    // ============================================
    // 🔥 IMAGE PREVIEW
    // ============================================
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');

    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function(e) {
            imagePreview.innerHTML = '';
            const file = e.target.files[0];
            if (!file || !file.type.startsWith('image/')) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.innerHTML = `
                    <div class="p-3 rounded-lg border flex items-start gap-4"
                         style="background: var(--bg-input); border-color: rgba(52,211,153,0.3);">
                        <img src="${e.target.result}"
                             class="w-24 h-24 rounded-lg object-cover border flex-shrink-0"
                             style="border-color: var(--border-2);">
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold uppercase tracking-wider mb-1 flex items-center gap-1" style="color: #34d399;">
                                <iconify-icon icon="mdi:check-circle"></iconify-icon>
                                Gambar Baru
                            </p>
                            <p class="text-xs truncate" style="color: var(--text-3);">${file.name}</p>
                            <p class="text-[10px] mt-0.5" style="color: var(--text-5);">
                                ${(file.size / 1024).toFixed(1)} KB
                            </p>
                        </div>
                    </div>
                `;
            };
            reader.readAsDataURL(file);
        });
    }

    // ============================================
    // 🔥 SUBMIT FORM
    // ============================================
    const submitBtn = document.getElementById('submit-article-btn');
    const articleForm = document.getElementById('article-form');

    if (submitBtn && articleForm) {
        submitBtn.addEventListener('click', function(e) {
            e.preventDefault();

            // Sync Quill content
            if (quill) {
                hiddenInput.value = quill.root.innerHTML;
            }

            // Validation
            const titleInput = document.getElementById('title');
            const excerptInput = document.getElementById('excerpt');
            const category = document.getElementById('article_category_id').value;
            const content = hiddenInput.value.trim();

            const title = titleInput.value.trim();
            const excerpt = excerptInput.value.trim();

            // 🔥 VALIDASI JUDUL
            if (!title) {
                alert('Judul artikel wajib diisi!');
                titleInput.focus();
                return;
            }

            if (title.length > 60) {
                alert('Judul artikel maksimal 60 karakter! Saat ini: ' + title.length + ' karakter.');
                titleInput.focus();
                return;
            }

            // 🔥 VALIDASI EXCERPT
            if (excerpt.length > 160) {
                alert('Deskripsi pendek (excerpt) maksimal 160 karakter! Saat ini: ' + excerpt.length + ' karakter.');
                excerptInput.focus();
                return;
            }

            if (!category) {
                alert('Kategori wajib dipilih!');
                document.getElementById('article_category_id').focus();
                return;
            }

            if (!content || content === '<p><br></p>' || content === '<p></p>' || content === '') {
                alert('Konten artikel wajib diisi!');
                editorContainer.focus();
                return;
            }

            articleForm.submit();
        });
    }

    function initCharCounter(inputId, counterId, maxLength) {
        const input = document.getElementById(inputId);
        const counter = document.getElementById(counterId);

        if (!input || !counter) return;

        const currentEl = counter.querySelector('.counter-current');
        const maxEl = counter.querySelector('.counter-max');

        if (maxEl) maxEl.textContent = maxLength;

        function updateCounter() {
            const length = input.value.length;
            if (currentEl) currentEl.textContent = length;

            // Reset class
            counter.classList.remove('warning', 'danger', 'full');

            if (length >= maxLength) {
                counter.classList.add('full');
            } else if (length >= maxLength * 0.9) {
                counter.classList.add('warning');
            } else if (length >= maxLength * 0.75) {
                counter.classList.add('warning');
            }

            // Optional: warn kalau terlalu pendek untuk SEO
            if (length > 0 && length < maxLength * 0.5) {
                counter.classList.add('warning');
            }
        }

        // Update saat user mengetik
        input.addEventListener('input', updateCounter);

        // Handle paste yang melebihi max
        input.addEventListener('paste', function(e) {
            const pasted = (e.clipboardData || window.clipboardData).getData('text');
            const currentLength = input.value.length;
            const selectionLength = (input.selectionEnd || 0) - (input.selectionStart || 0);
            const newLength = currentLength - selectionLength + pasted.length;

            if (newLength > maxLength) {
                e.preventDefault();
                // Potong paste agar pas
                const allowedLength = maxLength - (currentLength - selectionLength);
                const truncated = pasted.substring(0, allowedLength);
                
                // Insert manual
                const start = input.selectionStart || 0;
                const end = input.selectionEnd || 0;
                input.value = input.value.substring(0, start) + truncated + input.value.substring(end);
                
                // Trigger event supaya counter update
                input.dispatchEvent(new Event('input'));
            }
        });

        // Initial update
        updateCounter();
    }

    initCharCounter('title', 'title-counter', 60);
    initCharCounter('excerpt', 'excerpt-counter', 160);
});
</script>
@endpush