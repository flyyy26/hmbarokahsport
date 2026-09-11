@extends('layouts.admin')

@section('title', 'Pengaturan Toko')
@section('page-title', 'Pengaturan Toko')

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
                    <iconify-icon icon="mdi:cog-outline" class="text-slate-900 text-2xl"></iconify-icon>
                </span>
                Pengaturan Toko
            </h1>
            <p class="text-sm mt-1.5 ml-12" style="color: var(--text-5)">
                Kelola informasi utama yang akan ditampilkan pada website toko.
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

    @if ($errors->any())
        <div class="flex items-start gap-3 rounded-xl px-4 py-3
                    bg-red-500/10 border border-red-500/30 text-red-400">
            <iconify-icon icon="mdi:alert-circle-outline" class="text-xl flex-shrink-0 mt-0.5"></iconify-icon>
            <div class="text-sm">
                <p class="font-bold mb-1">Terdapat kesalahan:</p>
                <ul class="list-disc list-inside space-y-0.5 text-red-300">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif


    {{-- ============================================ --}}
    {{-- FORM --}}
    {{-- ============================================ --}}
    <form action="{{ route('admin.settings.update') }}"
          method="POST"
          enctype="multipart/form-data"
          class="space-y-6">
        @csrf
        @method('PUT')


        {{-- ============================================ --}}
        {{-- SECTION 1: INFORMASI TOKO --}}
        {{-- ============================================ --}}
        <div class="rounded-2xl border overflow-hidden"
             style="background: var(--bg-card); border-color: var(--border-2)">

            <div class="px-5 py-4 border-b flex items-center gap-2"
                 style="background: var(--bg-input); border-color: var(--border-2)">
                <iconify-icon icon="mdi:store-outline" class="text-[#ecbc42] text-base"></iconify-icon>
                <h2 class="font-bold text-sm flex-1" style="color: var(--text-1)">Informasi Toko</h2>
                <span class="text-[10px] font-mono px-2 py-0.5 rounded-full"
                      style="background: rgba(236,188,66,0.1); color: #ecbc42;">
                    <iconify-icon icon="mdi:star" class="inline"></iconify-icon>
                    Utama
                </span>
            </div>

            <div class="p-5 space-y-5">

                {{-- Store Name --}}
                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:storefront-outline" class="text-[#ecbc42]"></iconify-icon>
                        Nama Toko
                    </label>
                    <input type="text"
                           name="store_name"
                           value="{{ old('store_name', $setting?->store_name) }}"
                           class="form-input"
                           placeholder="Contoh: Toko Saya">
                </div>

                {{-- Description --}}
                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:text-box-outline" class="text-[#ecbc42]"></iconify-icon>
                        Deskripsi Toko
                    </label>
                    <textarea name="store_description"
                              rows="4"
                              class="form-input"
                              placeholder="Tuliskan deskripsi singkat toko...">{{ old('store_description', $setting?->store_description) }}</textarea>
                </div>
            </div>
        </div>


        {{-- ============================================ --}}
        {{-- SECTION 2: LOGO & FAVICON --}}
        {{-- ============================================ --}}
        <div class="rounded-2xl border overflow-hidden"
             style="background: var(--bg-card); border-color: var(--border-2)">

            <div class="px-5 py-4 border-b flex items-center gap-2"
                 style="background: var(--bg-input); border-color: var(--border-2)">
                <iconify-icon icon="mdi:image-outline" class="text-[#ecbc42] text-base"></iconify-icon>
                <h2 class="font-bold text-sm" style="color: var(--text-1)">Logo & Favicon</h2>
            </div>

            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- LOGO --}}
                    <div>
                        <label class="form-label">
                            <iconify-icon icon="mdi:image-size-select-large" class="text-[#ecbc42]"></iconify-icon>
                            Logo Toko
                        </label>

                        @if ($setting?->logo)
                            <div class="mt-2 rounded-xl border overflow-hidden relative group"
                                 style="background: var(--bg-input); border-color: var(--border-2);">
                                <div class="flex h-40 items-center justify-center p-4">
                                    <img src="{{ asset('storage/' . $setting->logo) }}"
                                         alt="Logo Toko"
                                         class="max-h-full max-w-full object-contain">
                                </div>
                                <span class="absolute top-2 left-2 inline-flex items-center gap-1
                                             rounded px-1.5 py-0.5 text-[9px] font-bold tracking-wider
                                             bg-emerald-500/90 text-white backdrop-blur-sm">
                                    <iconify-icon icon="mdi:check-circle"></iconify-icon>
                                    SAAT INI
                                </span>
                            </div>
                        @else
                            <div class="mt-2 flex h-40 items-center justify-center rounded-xl
                                        border-2 border-dashed"
                                 style="border-color: var(--border-2); background: var(--bg-input);">
                                <div class="text-center">
                                    <iconify-icon icon="mdi:image-off-outline" class="text-3xl mb-1" style="color: var(--text-6);"></iconify-icon>
                                    <p class="text-xs" style="color: var(--text-5);">Belum ada logo</p>
                                </div>
                            </div>
                        @endif

                        <input type="file"
                               name="logo"
                               id="logo-input"
                               accept="image/jpeg,image/png,image/webp"
                               class="form-input mt-3 file:mr-4 file:py-1.5 file:px-4 file:rounded-lg file:border-0
                                      file:text-xs file:font-bold file:cursor-pointer
                                      file:bg-gradient-to-r file:from-[#FDDD57] file:to-[#ecbc42]
                                      file:text-slate-900
                                      hover:file:shadow-lg hover:file:shadow-amber-500/30">

                        <p class="text-[10px] mt-1.5 flex items-center gap-1" style="color: var(--text-5);">
                            <iconify-icon icon="mdi:information-outline"></iconify-icon>
                            Format JPG, PNG, atau WebP. Maksimal 2 MB.
                        </p>

                        {{-- Preview --}}
                        <div id="logo-preview" class="mt-3"></div>
                    </div>


                    {{-- FAVICON --}}
                    <div>
                        <label class="form-label">
                            <iconify-icon icon="mdi:star-four-points-outline" class="text-[#ecbc42]"></iconify-icon>
                            Favicon
                        </label>

                        @if ($setting?->favicon)
                            <div class="mt-2 rounded-xl border overflow-hidden relative"
                                 style="background: var(--bg-input); border-color: var(--border-2);">
                                <div class="flex h-40 items-center justify-center p-4">
                                    <img src="{{ asset('storage/' . $setting->favicon) }}"
                                         alt="Favicon"
                                         class="h-20 w-20 object-contain">
                                </div>
                                <span class="absolute top-2 left-2 inline-flex items-center gap-1
                                             rounded px-1.5 py-0.5 text-[9px] font-bold tracking-wider
                                             bg-emerald-500/90 text-white backdrop-blur-sm">
                                    <iconify-icon icon="mdi:check-circle"></iconify-icon>
                                    SAAT INI
                                </span>
                            </div>
                        @else
                            <div class="mt-2 flex h-40 items-center justify-center rounded-xl
                                        border-2 border-dashed"
                                 style="border-color: var(--border-2); background: var(--bg-input);">
                                <div class="text-center">
                                    <iconify-icon icon="mdi:star-off-outline" class="text-3xl mb-1" style="color: var(--text-6);"></iconify-icon>
                                    <p class="text-xs" style="color: var(--text-5);">Belum ada favicon</p>
                                </div>
                            </div>
                        @endif

                        <input type="file"
                               name="favicon"
                               id="favicon-input"
                               accept="image/jpeg,image/png,image/webp,image/x-icon"
                               class="form-input mt-3 file:mr-4 file:py-1.5 file:px-4 file:rounded-lg file:border-0
                                      file:text-xs file:font-bold file:cursor-pointer
                                      file:bg-gradient-to-r file:from-[#FDDD57] file:to-[#ecbc42]
                                      file:text-slate-900
                                      hover:file:shadow-lg hover:file:shadow-amber-500/30">

                        <p class="text-[10px] mt-1.5 flex items-center gap-1" style="color: var(--text-5);">
                            <iconify-icon icon="mdi:information-outline"></iconify-icon>
                            Format PNG, JPG, WebP, atau ICO. Maksimal 1 MB.
                        </p>

                        {{-- Preview --}}
                        <div id="favicon-preview" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>


        {{-- ============================================ --}}
        {{-- SECTION 3: KONTAK --}}
        {{-- ============================================ --}}
        <div class="rounded-2xl border overflow-hidden"
             style="background: var(--bg-card); border-color: var(--border-2)">

            <div class="px-5 py-4 border-b flex items-center gap-2"
                 style="background: var(--bg-input); border-color: var(--border-2)">
                <iconify-icon icon="mdi:phone-outline" class="text-[#ecbc42] text-base"></iconify-icon>
                <h2 class="font-bold text-sm" style="color: var(--text-1)">Informasi Kontak</h2>
            </div>

            <div class="p-5 space-y-5">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Phone --}}
                    <div>
                        <label class="form-label">
                            <iconify-icon icon="mdi:phone" class="text-[#ecbc42]"></iconify-icon>
                            Nomor Telepon
                        </label>
                        <input type="text"
                               name="phone"
                               value="{{ old('phone', $setting?->phone) }}"
                               class="form-input"
                               placeholder="021xxxxxxxx">
                    </div>

                    {{-- WhatsApp --}}
                    <div>
                        <label class="form-label">
                            <iconify-icon icon="mdi:whatsapp" class="text-[#ecbc42]"></iconify-icon>
                            WhatsApp
                        </label>
                        <input type="text"
                               name="whatsapp"
                               value="{{ old('whatsapp', $setting?->whatsapp) }}"
                               class="form-input"
                               placeholder="628123456789">
                        <p class="text-[10px] mt-1.5 flex items-center gap-1" style="color: var(--text-5);">
                            <iconify-icon icon="mdi:information-outline"></iconify-icon>
                            Gunakan format internasional tanpa tanda +.
                        </p>
                    </div>

                    {{-- Email --}}
                    <div class="md:col-span-2">
                        <label class="form-label">
                            <iconify-icon icon="mdi:email-outline" class="text-[#ecbc42]"></iconify-icon>
                            Email
                        </label>
                        <input type="email"
                               name="email"
                               value="{{ old('email', $setting?->email) }}"
                               class="form-input"
                               placeholder="email@contoh.com">
                    </div>

                    {{-- Address --}}
                    <div class="md:col-span-2">
                        <label class="form-label">
                            <iconify-icon icon="mdi:map-marker-outline" class="text-[#ecbc42]"></iconify-icon>
                            Alamat
                        </label>
                        <textarea name="address"
                                  rows="3"
                                  class="form-input"
                                  placeholder="Alamat toko...">{{ old('address', $setting?->address) }}</textarea>
                    </div>

                    {{-- Postal Code --}}
                    <div>
                        <label class="form-label">
                            <iconify-icon icon="mdi:mailbox-outline" class="text-[#ecbc42]"></iconify-icon>
                            Kode Pos
                        </label>
                        <input type="text"
                               name="postal_code"
                               value="{{ old('postal_code', $setting?->postal_code) }}"
                               class="form-input"
                               placeholder="Contoh: 46195">
                    </div>

                    {{-- District --}}
                    <div>
                        <label class="form-label">
                            <iconify-icon icon="mdi:city-variant-outline" class="text-[#ecbc42]"></iconify-icon>
                            Kecamatan (Admin Level 3)
                        </label>
                        <input type="text"
                               name="district"
                               value="{{ old('district', $setting?->district) }}"
                               class="form-input"
                               placeholder="Contoh: Cihapit">
                        <p class="text-[10px] mt-1.5 flex items-start gap-1" style="color: var(--text-5);">
                            <iconify-icon icon="mdi:information-outline" class="flex-shrink-0 mt-0.5"></iconify-icon>
                            <span>Kecamatan/Kota tempat toko berada. Digunakan sebagai Acuan Admin Level 3 pada data origin Biteship.</span>
                        </p>
                    </div>
                </div>


                {{-- Google Maps --}}
                <div class="pt-4 border-t" style="border-color: var(--border-1);">
                    <label class="form-label">
                        <iconify-icon icon="mdi:google-maps" class="text-[#ecbc42]"></iconify-icon>
                        Google Maps Embed
                    </label>

                    <textarea name="google_maps"
                              rows="4"
                              class="form-input font-mono text-xs"
                              placeholder='Tempelkan kode iframe Google Maps disini...'>{{ old('google_maps', $setting?->google_maps) }}</textarea>

                    {{-- Info Cara Mendapatkan Kode --}}
                    <div class="mt-3 p-4 rounded-lg border"
                         style="background: rgba(96,165,250,0.05); border-color: rgba(96,165,250,0.2);">
                        <p class="text-xs font-bold mb-2 flex items-center gap-1.5" style="color: #60a5fa;">
                            <iconify-icon icon="mdi:lightbulb-outline"></iconify-icon>
                            Cara mendapatkan kode iframe:
                        </p>
                        <ol class="text-[11px] list-decimal list-inside space-y-1 ml-1" style="color: var(--text-4);">
                            <li>Buka <strong>Google Maps</strong> dan cari lokasi toko</li>
                            <li>Klik tombol <strong>"Bagikan"</strong> (Share)</li>
                            <li>Pilih tab <strong>"Sematan peta"</strong> (Embed a map)</li>
                            <li>Copy kode <strong>iframe</strong> dan tempelkan di atas</li>
                        </ol>
                        <p class="text-[10px] mt-3 flex items-start gap-1.5" style="color: var(--text-5);">
                            <iconify-icon icon="mdi:code-tags" class="flex-shrink-0 mt-0.5"></iconify-icon>
                            <span>Contoh: <code class="px-1.5 py-0.5 rounded font-mono" style="background: var(--bg-input); color: #ecbc42;">&lt;iframe src="https://www.google.com/maps/embed?pb=..."&gt;&lt;/iframe&gt;</code></span>
                        </p>
                    </div>

                    @error('google_maps')
                        <p class="text-[10px] mt-1 text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>


        {{-- ============================================ --}}
        {{-- SECTION 4: SOCIAL MEDIA --}}
        {{-- ============================================ --}}
        <div class="rounded-2xl border overflow-hidden"
             style="background: var(--bg-card); border-color: var(--border-2)">

            <div class="px-5 py-4 border-b flex items-center gap-2"
                 style="background: var(--bg-input); border-color: var(--border-2)">
                <iconify-icon icon="mdi:share-variant-outline" class="text-[#ecbc42] text-base"></iconify-icon>
                <h2 class="font-bold text-sm" style="color: var(--text-1)">Social Media</h2>
            </div>

            <div class="p-5 space-y-4">

                {{-- Instagram --}}
                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:instagram" class="text-[#ecbc42]"></iconify-icon>
                        Instagram
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <iconify-icon icon="mdi:link-variant" class="text-base" style="color: var(--text-5);"></iconify-icon>
                        </span>
                        <input type="text"
                               name="instagram"
                               value="{{ old('instagram', $setting?->instagram) }}"
                               class="form-input pl-9"
                               placeholder="https://instagram.com/toko">
                    </div>
                </div>

                {{-- Facebook --}}
                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:facebook" class="text-[#ecbc42]"></iconify-icon>
                        Facebook
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <iconify-icon icon="mdi:link-variant" class="text-base" style="color: var(--text-5);"></iconify-icon>
                        </span>
                        <input type="text"
                               name="facebook"
                               value="{{ old('facebook', $setting?->facebook) }}"
                               class="form-input pl-9"
                               placeholder="https://facebook.com/toko">
                    </div>
                </div>

                {{-- TikTok --}}
                <div>
                    <label class="form-label">
                        <iconify-icon icon="ic:baseline-tiktok" class="text-[#ecbc42]"></iconify-icon>
                        TikTok
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <iconify-icon icon="mdi:link-variant" class="text-base" style="color: var(--text-5);"></iconify-icon>
                        </span>
                        <input type="text"
                               name="tiktok"
                               value="{{ old('tiktok', $setting?->tiktok) }}"
                               class="form-input pl-9"
                               placeholder="https://tiktok.com/@toko">
                    </div>
                </div>
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
                Simpan Pengaturan
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
</style>


{{-- ============================================ --}}
{{-- SCRIPT - IMAGE PREVIEW --}}
{{-- ============================================ --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ============================================
    // LOGO PREVIEW
    // ============================================
    const logoInput = document.getElementById('logo-input');
    const logoPreview = document.getElementById('logo-preview');

    if (logoInput && logoPreview) {
        logoInput.addEventListener('change', function(e) {
            logoPreview.innerHTML = '';
            const file = e.target.files[0];
            if (!file || !file.type.startsWith('image/')) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                logoPreview.innerHTML = `
                    <div class="rounded-xl border overflow-hidden relative"
                         style="background: var(--bg-input); border-color: rgba(52,211,153,0.3);">
                        <div class="flex h-40 items-center justify-center p-4">
                            <img src="${e.target.result}"
                                 class="max-h-full max-w-full object-contain">
                        </div>
                        <span class="absolute top-2 left-2 inline-flex items-center gap-1
                                     rounded px-1.5 py-0.5 text-[9px] font-bold tracking-wider
                                     bg-emerald-500/90 text-white backdrop-blur-sm">
                            <iconify-icon icon="mdi:check-circle"></iconify-icon>
                            BARU
                        </span>
                    </div>
                    <p class="text-[10px] mt-2 truncate" style="color: var(--text-5);">${file.name} · ${(file.size / 1024).toFixed(1)} KB</p>
                `;
            };
            reader.readAsDataURL(file);
        });
    }


    // ============================================
    // FAVICON PREVIEW
    // ============================================
    const faviconInput = document.getElementById('favicon-input');
    const faviconPreview = document.getElementById('favicon-preview');

    if (faviconInput && faviconPreview) {
        faviconInput.addEventListener('change', function(e) {
            faviconPreview.innerHTML = '';
            const file = e.target.files[0];
            if (!file || !file.type.startsWith('image/')) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                faviconPreview.innerHTML = `
                    <div class="rounded-xl border overflow-hidden relative"
                         style="background: var(--bg-input); border-color: rgba(52,211,153,0.3);">
                        <div class="flex h-40 items-center justify-center p-4">
                            <img src="${e.target.result}"
                                 class="h-20 w-20 object-contain">
                        </div>
                        <span class="absolute top-2 left-2 inline-flex items-center gap-1
                                     rounded px-1.5 py-0.5 text-[9px] font-bold tracking-wider
                                     bg-emerald-500/90 text-white backdrop-blur-sm">
                            <iconify-icon icon="mdi:check-circle"></iconify-icon>
                            BARU
                        </span>
                    </div>
                    <p class="text-[10px] mt-2 truncate" style="color: var(--text-5);">${file.name} · ${(file.size / 1024).toFixed(1)} KB</p>
                `;
            };
            reader.readAsDataURL(file);
        });
    }

});
</script>
@endpush

@endsection