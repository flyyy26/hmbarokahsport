@php
    $isEdit = isset($banner);
@endphp

{{-- ============================================ --}}
{{-- ERROR VALIDATION --}}
{{-- ============================================ --}}
@if (session('error') || $errors->any())
    <div class="flex items-start gap-3 rounded-xl px-4 py-3
                bg-red-500/10 border border-red-500/30 text-red-400">
        <iconify-icon icon="mdi:alert-circle-outline" class="text-xl flex-shrink-0 mt-0.5"></iconify-icon>
        <div class="text-sm">
            @if (session('error'))
                <p class="font-bold">{{ session('error') }}</p>
            @endif
            @if ($errors->any())
                <ul class="list-disc list-inside space-y-0.5 {{ session('error') ? 'mt-1.5 text-red-300' : '' }}">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endif


{{-- ============================================ --}}
{{-- INFO: REKOMENDASI UKURAN --}}
{{-- ============================================ --}}
<div class="rounded-2xl border overflow-hidden"
     style="background: rgba(96,165,250,0.05); border-color: rgba(96,165,250,0.3);">
    <div class="p-4 flex items-start gap-3">
        <iconify-icon icon="mdi:information-outline" class="text-blue-400 text-xl flex-shrink-0 mt-0.5"></iconify-icon>
        <div class="min-w-0">
            <p class="text-sm font-bold" style="color: #60a5fa;">Rekomendasi Ukuran Gambar</p>
            <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1.5 text-[11px]" style="color: #93c5fd;">
                <span class="inline-flex items-center gap-1.5">
                    <iconify-icon icon="mdi:monitor"></iconify-icon>
                    Desktop: <strong class="font-mono">1040px × 377px</strong>
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <iconify-icon icon="mdi:cellphone"></iconify-icon>
                    Mobile: <strong class="font-mono">455px × 269px</strong>
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <iconify-icon icon="mdi:file-image-outline"></iconify-icon>
                    Format: JPG, PNG, WEBP
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <iconify-icon icon="mdi:database-outline"></iconify-icon>
                    Max: 5MB
                </span>
            </div>
        </div>
    </div>
</div>


{{-- ============================================ --}}
{{-- SECTION 1: INFORMASI BANNER --}}
{{-- ============================================ --}}
<div class="rounded-2xl border overflow-hidden"
     style="background: var(--bg-card); border-color: var(--border-2)">

    <div class="px-5 py-4 border-b flex items-center gap-2"
         style="background: var(--bg-input); border-color: var(--border-2)">
        <iconify-icon icon="mdi:information-outline" class="text-[#ecbc42] text-base"></iconify-icon>
        <h2 class="font-bold text-sm" style="color: var(--text-1)">Informasi Banner</h2>
    </div>

    <div class="p-5 space-y-5">

        {{-- Judul --}}
        <div>
            <label class="form-label">
                <iconify-icon icon="mdi:format-title" class="text-[#ecbc42]"></iconify-icon>
                Judul Banner
                <span class="text-[10px] font-normal normal-case" style="color: var(--text-5);">(Opsional)</span>
            </label>
            <input type="text"
                   name="title"
                   value="{{ old('title', $banner->title ?? '') }}"
                   class="form-input"
                   placeholder="Contoh: Koleksi Terbaru">
            <p class="text-[10px] mt-1.5 flex items-center gap-1" style="color: var(--text-5);">
                <iconify-icon icon="mdi:information-outline"></iconify-icon>
                Tidak wajib diisi, hanya untuk keperluan administrasi.
            </p>
        </div>

        {{-- Subjudul --}}
        <div>
            <label class="form-label">
                <iconify-icon icon="mdi:format-text" class="text-[#ecbc42]"></iconify-icon>
                Subjudul
                <span class="text-[10px] font-normal normal-case" style="color: var(--text-5);">(Opsional)</span>
            </label>
            <input type="text"
                   name="subtitle"
                   value="{{ old('subtitle', $banner->subtitle ?? '') }}"
                   class="form-input"
                   placeholder="Contoh: Temukan produk terbaik kami">
            <p class="text-[10px] mt-1.5 flex items-center gap-1" style="color: var(--text-5);">
                <iconify-icon icon="mdi:information-outline"></iconify-icon>
                Tidak wajib diisi, hanya untuk keperluan administrasi.
            </p>
        </div>
    </div>
</div>


{{-- ============================================ --}}
{{-- SECTION 2: GAMBAR BANNER --}}
{{-- ============================================ --}}
<div class="rounded-2xl border overflow-hidden"
     style="background: var(--bg-card); border-color: var(--border-2)">

    <div class="px-5 py-4 border-b flex items-center gap-2"
         style="background: var(--bg-input); border-color: var(--border-2)">
        <iconify-icon icon="mdi:image-multiple-outline" class="text-[#ecbc42] text-base"></iconify-icon>
        <h2 class="font-bold text-sm" style="color: var(--text-1)">Gambar Banner</h2>
    </div>

    <div class="p-5 space-y-5">

        {{-- Gambar Desktop --}}
        <div>
            <label class="form-label">
                <iconify-icon icon="mdi:monitor" class="text-[#ecbc42]"></iconify-icon>
                Gambar Desktop <span class="text-red-400">*</span>
                <span class="text-[10px] font-normal normal-case" style="color: var(--text-5);">
                    (Rekomendasi: 1040×377)
                </span>
            </label>

            {{-- Preview Existing --}}
            @if ($isEdit && $banner->image)
                <div class="mb-4 p-3 rounded-lg border flex items-start gap-3"
                     style="background: var(--bg-input); border-color: var(--border-2);">
                    <div class="relative flex-shrink-0">
                        <img src="{{ asset('storage/' . $banner->image) }}"
                             class="h-24 w-auto max-w-[280px] rounded-lg object-cover border"
                             style="border-color: var(--border-2);"
                             alt="{{ $banner->title ?? 'Banner' }}">
                        <span class="absolute top-1.5 left-1.5 inline-flex items-center gap-1
                                     rounded px-1.5 py-0.5 text-[9px] font-bold tracking-wider
                                     bg-blue-500/90 text-white backdrop-blur-sm">
                            <iconify-icon icon="mdi:monitor"></iconify-icon>
                            DESKTOP
                        </span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-wider mb-0.5" style="color: var(--text-5);">
                            Gambar Saat Ini
                        </p>
                        <p class="text-[11px]" style="color: var(--text-3);">
                            Upload gambar baru untuk mengganti.
                        </p>
                    </div>
                </div>
            @endif

            {{-- Input File --}}
            <input type="file"
                   name="image"
                   id="image"
                   accept="image/jpeg,image/png,image/webp"
                   {{ $isEdit ? '' : 'required' }}
                   class="form-input file:mr-4 file:py-1.5 file:px-4 file:rounded-lg file:border-0
                          file:text-xs file:font-bold file:cursor-pointer
                          file:bg-gradient-to-r file:from-[#FDDD57] file:to-[#ecbc42]
                          file:text-slate-900
                          hover:file:shadow-lg hover:file:shadow-amber-500/30">

            @if ($isEdit)
                <p class="text-[10px] mt-1.5 flex items-center gap-1" style="color: var(--text-5);">
                    <iconify-icon icon="mdi:information-outline"></iconify-icon>
                    Kosongkan jika tidak ingin mengganti gambar desktop.
                </p>
            @endif

            {{-- Preview Baru --}}
            <div id="preview-desktop" class="mt-4"></div>
        </div>


        {{-- Gambar Mobile --}}
        <div>
            <label class="form-label">
                <iconify-icon icon="mdi:cellphone" class="text-[#ecbc42]"></iconify-icon>
                Gambar Mobile
                <span class="text-[10px] font-normal normal-case" style="color: var(--text-5);">
                    (Rekomendasi: 455×269)
                </span>
            </label>

            {{-- Preview Existing --}}
            @if ($isEdit && $banner->image_mobile)
                <div class="mb-4 p-3 rounded-lg border flex items-start gap-3"
                     style="background: var(--bg-input); border-color: var(--border-2);">
                    <div class="relative flex-shrink-0">
                        <img src="{{ asset('storage/' . $banner->image_mobile) }}"
                             class="h-24 w-20 rounded-lg object-cover border"
                             style="border-color: var(--border-2);"
                             alt="{{ $banner->title ?? 'Banner Mobile' }}">
                        <span class="absolute top-1.5 left-1.5 inline-flex items-center gap-1
                                     rounded px-1.5 py-0.5 text-[9px] font-bold tracking-wider
                                     bg-purple-500/90 text-white backdrop-blur-sm">
                            <iconify-icon icon="mdi:cellphone"></iconify-icon>
                            MOBILE
                        </span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-wider mb-0.5" style="color: var(--text-5);">
                            Gambar Saat Ini
                        </p>
                        <p class="text-[11px]" style="color: var(--text-3);">
                            Upload gambar baru untuk mengganti.
                        </p>
                    </div>
                </div>
            @endif

            {{-- Input File --}}
            <input type="file"
                   name="image_mobile"
                   id="image_mobile"
                   accept="image/jpeg,image/png,image/webp"
                   class="form-input file:mr-4 file:py-1.5 file:px-4 file:rounded-lg file:border-0
                          file:text-xs file:font-bold file:cursor-pointer
                          file:bg-purple-500/10 file:text-purple-400
                          hover:file:bg-purple-500/20">

            @if ($isEdit)
                <p class="text-[10px] mt-1.5 flex items-center gap-1" style="color: var(--text-5);">
                    <iconify-icon icon="mdi:information-outline"></iconify-icon>
                    Kosongkan jika tidak ingin mengganti gambar mobile.
                </p>
            @else
                <p class="text-[10px] mt-1.5 flex items-center gap-1" style="color: var(--text-5);">
                    <iconify-icon icon="mdi:information-outline"></iconify-icon>
                    Jika tidak diisi, akan menggunakan gambar desktop secara otomatis.
                </p>
            @endif

            {{-- Preview Baru --}}
            <div id="preview-mobile" class="mt-4"></div>
        </div>
    </div>
</div>


{{-- ============================================ --}}
{{-- SECTION 3: TOMBOL CTA --}}
{{-- ============================================ --}}
<div class="rounded-2xl border overflow-hidden"
     style="background: var(--bg-card); border-color: var(--border-2)">

    <div class="px-5 py-4 border-b flex items-center gap-2"
         style="background: var(--bg-input); border-color: var(--border-2)">
        <iconify-icon icon="mdi:gesture-tap-button" class="text-[#ecbc42] text-base"></iconify-icon>
        <h2 class="font-bold text-sm" style="color: var(--text-1)">Tombol Call-to-Action</h2>
        <span class="text-[10px] font-mono px-2 py-0.5 rounded-full"
              style="background: rgba(148,163,184,0.1); color: var(--text-4);">
            Opsional
        </span>
    </div>

    <div class="p-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Button Text --}}
            <div>
                <label class="form-label">
                    <iconify-icon icon="mdi:button-cursor" class="text-[#ecbc42]"></iconify-icon>
                    Teks Tombol
                </label>
                <input type="text"
                       name="button_text"
                       value="{{ old('button_text', $banner->button_text ?? '') }}"
                       class="form-input"
                       placeholder="Contoh: Belanja Sekarang">
            </div>

            {{-- Button URL --}}
            <div>
                <label class="form-label">
                    <iconify-icon icon="mdi:link-variant" class="text-[#ecbc42]"></iconify-icon>
                    Link Tombol
                </label>
                <input type="text"
                       name="button_url"
                       value="{{ old('button_url', $banner->button_url ?? '') }}"
                       class="form-input"
                       placeholder="/products">
            </div>
        </div>
    </div>
</div>


{{-- ============================================ --}}
{{-- SECTION 4: PENGATURAN TAMPILAN --}}
{{-- ============================================ --}}
<div class="rounded-2xl border overflow-hidden"
     style="background: var(--bg-card); border-color: var(--border-2)">

    <div class="px-5 py-4 border-b flex items-center gap-2"
         style="background: var(--bg-input); border-color: var(--border-2)">
        <iconify-icon icon="mdi:cog-outline" class="text-[#ecbc42] text-base"></iconify-icon>
        <h2 class="font-bold text-sm" style="color: var(--text-1)">Pengaturan Tampilan</h2>
    </div>

    <div class="p-5 space-y-5">

        {{-- Sort Order --}}
        <div>
            <label class="form-label">
                <iconify-icon icon="mdi:sort-numeric-ascending" class="text-[#ecbc42]"></iconify-icon>
                Urutan <span class="text-red-400">*</span>
            </label>
            <input type="number"
                   name="sort_order"
                   min="0"
                   value="{{ old('sort_order', $banner->sort_order ?? 0) }}"
                   required
                   class="form-input"
                   placeholder="0">
            <p class="text-[10px] mt-1.5 flex items-center gap-1" style="color: var(--text-5);">
                <iconify-icon icon="mdi:information-outline"></iconify-icon>
                Semakin kecil angka, semakin awal tampil.
            </p>
        </div>

        {{-- Periode --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div>
                <label class="form-label">
                    <iconify-icon icon="mdi:calendar-start" class="text-[#ecbc42]"></iconify-icon>
                    Mulai Tampil
                    <span class="text-[10px] font-normal normal-case" style="color: var(--text-5);">(Opsional)</span>
                </label>
                <input type="datetime-local"
                       name="starts_at"
                       value="{{ old('starts_at', isset($banner->starts_at) ? $banner->starts_at->format('Y-m-d\TH:i') : '') }}"
                       class="form-input">
            </div>

            <div>
                <label class="form-label">
                    <iconify-icon icon="mdi:calendar-end" class="text-[#ecbc42]"></iconify-icon>
                    Berakhir
                    <span class="text-[10px] font-normal normal-case" style="color: var(--text-5);">(Opsional)</span>
                </label>
                <input type="datetime-local"
                       name="ends_at"
                       value="{{ old('ends_at', isset($banner->ends_at) ? $banner->ends_at->format('Y-m-d\TH:i') : '') }}"
                       class="form-input">
            </div>
        </div>

        {{-- Status Aktif --}}
        <div class="pt-4 border-t" style="border-color: var(--border-1);">

            {{-- Hidden input untuk default value --}}
            <input type="hidden" name="is_active" value="0">

            <label class="flex items-start gap-3 cursor-pointer p-3 rounded-lg border transition-all"
                   style="background: var(--bg-input); border-color: var(--border-2);"
                   onmouseover="this.style.borderColor='rgba(52,211,153,0.3)'"
                   onmouseout="this.style.borderColor='var(--border-2)'">
                <input type="checkbox"
                       name="is_active"
                       value="1"
                       @checked(old('is_active', $banner->is_active ?? true))
                       class="mt-0.5 h-4 w-4 rounded cursor-pointer"
                       style="accent-color: #ecbc42;">
                <div>
                    <span class="text-sm font-semibold flex items-center gap-1.5" style="color: var(--text-1);">
                        <iconify-icon icon="mdi:check-circle-outline" class="text-emerald-400"></iconify-icon>
                        Banner Aktif
                    </span>
                    <p class="text-[10px] mt-0.5" style="color: var(--text-5);">
                        Banner akan ditampilkan di halaman utama toko.
                    </p>
                </div>
            </label>
        </div>
    </div>
</div>


{{-- ============================================ --}}
{{-- TOMBOL AKSI --}}
{{-- ============================================ --}}
<div class="flex items-center justify-end gap-3 pb-4">
    <a href="{{ route('admin.banners.index') }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg
              text-sm font-semibold transition-all active:scale-95 border"
       style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)"
       onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
       onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'">
        <iconify-icon icon="mdi:arrow-left"></iconify-icon>
        Batal
    </a>
    <button type="submit"
            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg
                   text-sm font-bold transition-all active:scale-95
                   bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                   text-slate-900
                   shadow-lg shadow-amber-500/20
                   hover:shadow-xl hover:shadow-amber-500/40
                   hover:-translate-y-0.5">
        <iconify-icon icon="mdi:content-save-outline" class="text-base"></iconify-icon>
        {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Banner' }}
    </button>
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
</style>


{{-- ============================================ --}}
{{-- SCRIPT - IMAGE PREVIEW --}}
{{-- ============================================ --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ============================================
    // PREVIEW GAMBAR DESKTOP
    // ============================================
    const imageInput = document.getElementById('image');
    const previewDesktop = document.getElementById('preview-desktop');

    if (imageInput && previewDesktop) {
        imageInput.addEventListener('change', function(e) {
            previewDesktop.innerHTML = '';
            const file = e.target.files[0];
            if (!file || !file.type.startsWith('image/')) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                previewDesktop.innerHTML = `
                    <div class="p-3 rounded-lg border flex items-start gap-3"
                         style="background: var(--bg-input); border-color: rgba(52,211,153,0.3);">
                        <div class="relative flex-shrink-0">
                            <img src="${e.target.result}"
                                 class="h-24 w-auto max-w-[280px] rounded-lg object-cover border"
                                 style="border-color: var(--border-2);">
                            <span class="absolute top-1.5 left-1.5 inline-flex items-center gap-1
                                         rounded px-1.5 py-0.5 text-[9px] font-bold tracking-wider
                                         bg-emerald-500/90 text-white backdrop-blur-sm">
                                <iconify-icon icon="mdi:check-circle"></iconify-icon>
                                BARU
                            </span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold uppercase tracking-wider mb-0.5 flex items-center gap-1" style="color: #34d399;">
                                <iconify-icon icon="mdi:monitor"></iconify-icon>
                                Gambar Desktop Baru
                            </p>
                            <p class="text-xs truncate" style="color: var(--text-3);">${file.name}</p>
                            <p class="text-[10px] mt-0.5 font-mono" style="color: var(--text-5);">
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
    // PREVIEW GAMBAR MOBILE
    // ============================================
    const imageMobileInput = document.getElementById('image_mobile');
    const previewMobile = document.getElementById('preview-mobile');

    if (imageMobileInput && previewMobile) {
        imageMobileInput.addEventListener('change', function(e) {
            previewMobile.innerHTML = '';
            const file = e.target.files[0];
            if (!file || !file.type.startsWith('image/')) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                previewMobile.innerHTML = `
                    <div class="p-3 rounded-lg border flex items-start gap-3"
                         style="background: var(--bg-input); border-color: rgba(52,211,153,0.3);">
                        <div class="relative flex-shrink-0">
                            <img src="${e.target.result}"
                                 class="h-24 w-20 rounded-lg object-cover border"
                                 style="border-color: var(--border-2);">
                            <span class="absolute top-1.5 left-1.5 inline-flex items-center gap-1
                                         rounded px-1.5 py-0.5 text-[9px] font-bold tracking-wider
                                         bg-emerald-500/90 text-white backdrop-blur-sm">
                                <iconify-icon icon="mdi:check-circle"></iconify-icon>
                                BARU
                            </span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold uppercase tracking-wider mb-0.5 flex items-center gap-1" style="color: #34d399;">
                                <iconify-icon icon="mdi:cellphone"></iconify-icon>
                                Gambar Mobile Baru
                            </p>
                            <p class="text-xs truncate" style="color: var(--text-3);">${file.name}</p>
                            <p class="text-[10px] mt-0.5 font-mono" style="color: var(--text-5);">
                                ${(file.size / 1024).toFixed(1)} KB
                            </p>
                        </div>
                    </div>
                `;
            };
            reader.readAsDataURL(file);
        });
    }

});
</script>
@endpush