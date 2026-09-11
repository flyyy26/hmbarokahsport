@extends('layouts.admin')

@section('title', 'Edit Testimonial')
@section('page-title', 'Edit Testimonial')

@section('content')

<div class="w-full max-w-4xl mx-auto space-y-6">

    {{-- ============================================ --}}
    {{-- HEADER --}}
    {{-- ============================================ --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <a href="{{ route('admin.testimonials.index') }}"
               class="inline-flex items-center gap-1.5 text-xs font-semibold transition-colors"
               style="color: var(--text-5)"
               onmouseover="this.style.color='#FDDD57'"
               onmouseout="this.style.color='var(--text-5)'">
                <iconify-icon icon="mdi:arrow-left"></iconify-icon>
                Kembali ke Testimonial
            </a>
            <h1 class="text-2xl font-bold flex items-center gap-2.5 mt-2" style="color: var(--text-1)">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl
                             bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                             shadow-lg shadow-amber-500/20 flex-shrink-0">
                    <iconify-icon icon="mdi:star-edit-outline" class="text-slate-900 text-2xl"></iconify-icon>
                </span>
                Edit Testimonial
            </h1>
            <p class="text-sm mt-1.5 ml-12" style="color: var(--text-5)">
                Perbarui testimonial dan foto produk.
            </p>
        </div>

        {{-- Badge Rating --}}
        <div class="flex items-center gap-2 flex-shrink-0">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border"
                  style="background: rgba(236,188,66,0.1); border-color: rgba(236,188,66,0.3); color: #ecbc42;">
                <iconify-icon icon="mdi:star"></iconify-icon>
                {{ $testimonial->rating }}/5
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border"
                  style="background: {{ $testimonial->is_active ? 'rgba(52,211,153,0.1)' : 'rgba(248,113,113,0.1)' }};
                         border-color: {{ $testimonial->is_active ? 'rgba(52,211,153,0.3)' : 'rgba(248,113,113,0.3)' }};
                         color: {{ $testimonial->is_active ? '#34d399' : '#f87171' }};">
                <span class="w-1.5 h-1.5 rounded-full" style="background: currentColor;"></span>
                {{ $testimonial->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- FORM --}}
    {{-- ============================================ --}}
    <form action="{{ route('admin.testimonials.update', $testimonial) }}"
          method="POST"
          enctype="multipart/form-data"
          id="testimonial-form"
          class="space-y-6">
        @csrf
        @method('PUT')

        {{-- ============================================ --}}
        {{-- SECTION 1: INFORMASI PRODUK --}}
        {{-- ============================================ --}}
        <div class="rounded-2xl border overflow-hidden"
             style="background: var(--bg-card); border-color: var(--border-2)">

            <div class="px-5 py-4 border-b flex items-center gap-2"
                 style="background: var(--bg-input); border-color: var(--border-2)">
                <iconify-icon icon="mdi:package-variant-closed" class="text-[#ecbc42] text-base"></iconify-icon>
                <h2 class="font-bold text-sm" style="color: var(--text-1)">Informasi Produk</h2>
            </div>

            <div class="p-5 space-y-5">

                {{-- Produk --}}
                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:package-variant" class="text-[#ecbc42]"></iconify-icon>
                        Produk <span class="text-red-400">*</span>
                    </label>
                    <select name="product_id"
                            id="product_id"
                            class="form-input"
                            required>
                        <option value="">Pilih Produk</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id', $testimonial->product_id) == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <p class="text-[10px] mt-1 text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Varian --}}
                <div id="variant_wrapper">
                    <label class="form-label">
                        <iconify-icon icon="mdi:layers-outline" class="text-[#ecbc42]"></iconify-icon>
                        Varian Produk
                    </label>
                    <select name="product_variant_id"
                            id="product_variant_id"
                            class="form-input">
                        <option value="">Pilih Varian (opsional)</option>
                        @if ($testimonial->product && $testimonial->product->variants)
                            @foreach ($testimonial->product->variants as $variant)
                                <option value="{{ $variant->id }}"
                                    {{ old('product_variant_id', $testimonial->product_variant_id) == $variant->id ? 'selected' : '' }}>
                                    {{ $variant->option_combination ?: 'Default' }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <p class="text-[10px] mt-1" style="color: var(--text-5);">Jika produk memiliki varian, pilih varian yang sesuai.</p>
                    @error('product_variant_id')
                        <p class="text-[10px] mt-1 text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>


        {{-- ============================================ --}}
        {{-- SECTION 2: DETAIL TESTIMONIAL --}}
        {{-- ============================================ --}}
        <div class="rounded-2xl border overflow-hidden"
             style="background: var(--bg-card); border-color: var(--border-2)">

            <div class="px-5 py-4 border-b flex items-center gap-2"
                 style="background: var(--bg-input); border-color: var(--border-2)">
                <iconify-icon icon="mdi:comment-quote-outline" class="text-[#ecbc42] text-base"></iconify-icon>
                <h2 class="font-bold text-sm" style="color: var(--text-1)">Detail Testimonial</h2>
            </div>

            <div class="p-5 space-y-5">

                {{-- Nama Pelanggan --}}
                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:account-outline" class="text-[#ecbc42]"></iconify-icon>
                        Nama Pelanggan <span class="text-red-400">*</span>
                    </label>
                    <input type="text"
                           name="customer_name"
                           value="{{ old('customer_name', $testimonial->customer_name) }}"
                           class="form-input"
                           placeholder="Nama lengkap pelanggan">
                    @error('customer_name')
                        <p class="text-[10px] mt-1 text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Rating --}}
                <div id="rating_wrapper">
                    <label class="form-label">
                        <iconify-icon icon="mdi:star-outline" class="text-[#ecbc42]"></iconify-icon>
                        Rating <span class="text-red-400">*</span>
                    </label>
                    <div class="flex items-center gap-1.5 mt-2">
                        @php
                            $oldRating = old('rating', $testimonial->rating);
                        @endphp
                        @for ($i = 1; $i <= 5; $i++)
                            <label class="cursor-pointer">
                                <input type="radio"
                                       name="rating"
                                       value="{{ $i }}"
                                       {{ $i == $oldRating ? 'checked' : '' }}
                                       class="hidden rating-input"
                                       data-rating="{{ $i }}">
                                <iconify-icon
                                    icon="mdi:star"
                                    class="text-2xl transition-all rating-star
                                           {{ $i <= $oldRating ? 'text-amber-400' : 'text-gray-500 hover:text-amber-300' }}"
                                    style="{{ $i <= $oldRating ? '' : 'opacity: 0.3;' }}">
                                </iconify-icon>
                            </label>
                        @endfor
                        <span id="rating-label"
                              class="ml-2 text-sm font-bold px-2 py-0.5 rounded-lg"
                              style="background: rgba(236,188,66,0.1); color: #ecbc42;">
                            {{ $oldRating }}/5
                        </span>
                    </div>
                    @error('rating')
                        <p class="text-[10px] mt-1 text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Testimonial --}}
                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:text-box-outline" class="text-[#ecbc42]"></iconify-icon>
                        Testimonial <span class="text-red-400">*</span>
                    </label>
                    <textarea name="testimonial"
                              rows="5"
                              class="form-input"
                              placeholder="Tuliskan ulasan produk...">{{ old('testimonial', $testimonial->testimonial) }}</textarea>
                    @error('testimonial')
                        <p class="text-[10px] mt-1 text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>


        {{-- ============================================ --}}
        {{-- SECTION 3: FOTO PRODUK --}}
        {{-- ============================================ --}}
        <div class="rounded-2xl border overflow-hidden"
             style="background: var(--bg-card); border-color: var(--border-2)">

            <div class="px-5 py-4 border-b flex items-center gap-2"
                 style="background: var(--bg-input); border-color: var(--border-2)">
                <iconify-icon icon="mdi:image-multiple-outline" class="text-[#ecbc42] text-base"></iconify-icon>
                <h2 class="font-bold text-sm" style="color: var(--text-1)">Foto Produk</h2>
                @if($testimonial->images->isNotEmpty())
                    <span class="ml-auto text-[10px] font-mono px-2 py-0.5 rounded-full"
                          style="background: rgba(236,188,66,0.1); color: #ecbc42;">
                        {{ $testimonial->images->count() }} foto
                    </span>
                @endif
            </div>

            <div class="p-5 space-y-5">

                {{-- Foto yang sudah ada --}}
                @if ($testimonial->images->isNotEmpty())
                    <div>
                        <label class="form-label">
                            <iconify-icon icon="mdi:image-check-outline" class="text-[#ecbc42]"></iconify-icon>
                            Foto yang Sudah Ada
                            <span class="text-[10px] font-normal ml-1" style="color: var(--text-5);">
                                (centang untuk menghapus)
                            </span>
                        </label>
                        <div class="flex flex-wrap gap-3">
                            @foreach ($testimonial->images as $image)
                                <label class="relative h-24 w-24 group cursor-pointer">
                                    <img src="{{ $image->image_url }}"
                                         alt="Foto testimonial"
                                         class="h-full w-full rounded-lg object-cover border transition-all group-hover:opacity-75"
                                         style="border-color: var(--border-2);">
                                    <input type="checkbox"
                                           name="existing_images[]"
                                           value="{{ $image->id }}"
                                           class="absolute top-1.5 right-1.5 h-4 w-4 rounded cursor-pointer peer"
                                           style="accent-color: #ef4444;">
                                    <div class="absolute inset-0 bg-red-500/0 peer-checked:bg-red-500/40
                                                rounded-lg flex items-center justify-center transition-all">
                                        <iconify-icon icon="mdi:delete-outline"
                                                      class="text-white text-2xl opacity-0 peer-checked:opacity-100 transition-opacity">
                                        </iconify-icon>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Upload Foto Baru --}}
                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:cloud-upload-outline" class="text-[#ecbc42]"></iconify-icon>
                        Tambah Foto Baru
                    </label>
                    <input type="file"
                           name="images[]"
                           id="images"
                           accept="image/*"
                           multiple
                           class="form-input file:mr-4 file:py-1.5 file:px-4 file:rounded-lg file:border-0
                                  file:text-xs file:font-bold file:cursor-pointer
                                  file:bg-gradient-to-r file:from-[#FDDD57] file:to-[#ecbc42]
                                  file:text-slate-900
                                  hover:file:shadow-lg hover:file:shadow-amber-500/30">
                    <p class="text-[10px] mt-1.5 flex items-center gap-1" style="color: var(--text-5);">
                        <iconify-icon icon="mdi:information-outline"></iconify-icon>
                        Upload foto produk baru. Maksimal 2MB per foto. Bisa pilih lebih dari satu.
                    </p>
                    @error('images.*')
                        <p class="text-[10px] mt-1 text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Preview Foto Baru --}}
                <div id="image-preview" class="flex flex-wrap gap-3"></div>
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
                <h2 class="font-bold text-sm" style="color: var(--text-1)">Status Testimonial</h2>
            </div>

            <div class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <label class="flex items-start gap-3 cursor-pointer p-3 rounded-lg border transition-all"
                           style="background: var(--bg-input); border-color: var(--border-2);"
                           onmouseover="this.style.borderColor='rgba(236,188,66,0.3)'"
                           onmouseout="this.style.borderColor='var(--border-2)'">
                        <input type="checkbox"
                               name="is_active"
                               value="1"
                               {{ old('is_active', $testimonial->is_active) ? 'checked' : '' }}
                               class="mt-0.5 h-4 w-4 rounded cursor-pointer"
                               style="accent-color: #ecbc42;">
                        <div>
                            <span class="text-sm font-semibold flex items-center gap-1.5" style="color: var(--text-1);">
                                <iconify-icon icon="mdi:check-circle-outline" class="text-emerald-400"></iconify-icon>
                                Aktifkan Testimonial
                            </span>
                            <p class="text-[10px] mt-0.5" style="color: var(--text-5);">
                                Testimonial akan tampil di halaman produk.
                            </p>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 cursor-pointer p-3 rounded-lg border transition-all"
                           style="background: var(--bg-input); border-color: var(--border-2);"
                           onmouseover="this.style.borderColor='rgba(236,188,66,0.3)'"
                           onmouseout="this.style.borderColor='var(--border-2)'">
                        <input type="checkbox"
                               name="is_verified_purchase"
                               value="1"
                               {{ old('is_verified_purchase', $testimonial->is_verified_purchase) ? 'checked' : '' }}
                               class="mt-0.5 h-4 w-4 rounded cursor-pointer"
                               style="accent-color: #ecbc42;">
                        <div>
                            <span class="text-sm font-semibold flex items-center gap-1.5" style="color: var(--text-1);">
                                <iconify-icon icon="mdi:check-decagram" class="text-blue-400"></iconify-icon>
                                Verifikasi Pembelian
                            </span>
                            <p class="text-[10px] mt-0.5" style="color: var(--text-5);">
                                Tandai bahwa pelanggan benar-benar membeli produk.
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
            <a href="{{ route('admin.testimonials.index') }}"
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
                Update Testimonial
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
{{-- SCRIPTS --}}
{{-- ============================================ --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ============================================
    // LOAD VARIANTS
    // ============================================
    const productIdSelect = document.getElementById('product_id');
    const variantWrapper = document.getElementById('variant_wrapper');
    const variantSelect = document.getElementById('product_variant_id');

    const productsData = @json($productsData);

    function loadVariants(productId, selectedVariantId = null) {
        const variants = productsData[productId] || [];
        variantSelect.innerHTML = '<option value="">Pilih Varian (opsional)</option>';

        if (variants.length > 0) {
            variants.forEach(function(v) {
                const opt = document.createElement('option');
                opt.value = v.id;
                opt.textContent = v.option_combination + ' (Rp ' + new Intl.NumberFormat('id-ID').format(v.price) + ')';
                if (selectedVariantId && v.id == selectedVariantId) {
                    opt.selected = true;
                }
                variantSelect.appendChild(opt);
            });
            variantWrapper.style.display = 'block';
        } else {
            variantWrapper.style.display = 'none';
        }
    }

    if (productIdSelect) {
        productIdSelect.addEventListener('change', function() {
            loadVariants(this.value);
        });

        // Load on page load
        if (productIdSelect.value) {
            loadVariants(productIdSelect.value, '{{ old('product_variant_id', $testimonial->product_variant_id) }}');
        }
    }


    // ============================================
    // STAR RATING
    // ============================================
    const ratingInputs = document.querySelectorAll('.rating-input');
    const ratingLabel = document.getElementById('rating-label');
    const ratingStars = document.querySelectorAll('#rating_wrapper .rating-star');

    ratingInputs.forEach(function(input) {
        input.addEventListener('change', function() {
            const rating = parseInt(this.value);
            if (ratingLabel) ratingLabel.textContent = rating + '/5';

            ratingStars.forEach(function(star, i) {
                if (i < rating) {
                    star.classList.remove('text-gray-500');
                    star.classList.add('text-amber-400');
                    star.style.opacity = '1';
                } else {
                    star.classList.remove('text-amber-400');
                    star.classList.add('text-gray-500');
                    star.style.opacity = '0.3';
                }
            });
        });
    });


    // ============================================
    // IMAGE PREVIEW
    // ============================================
    const imageInput = document.getElementById('images');
    const imagePreview = document.getElementById('image-preview');

    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            imagePreview.innerHTML = '';

            const files = Array.from(e.target.files);
            if (files.length === 0) return;

            const maxFiles = 10;
            const filesToPreview = files.slice(0, maxFiles);

            filesToPreview.forEach(function(file) {
                if (!file.type.startsWith('image/')) return;

                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative h-24 w-24 group';
                    div.innerHTML = `
                        <img src="${e.target.result}"
                             alt="Preview"
                             class="h-full w-full rounded-lg object-cover border"
                             style="border-color: var(--border-2);">
                        <div class="absolute inset-0 bg-emerald-500/20 opacity-0 group-hover:opacity-100
                                    rounded-lg flex items-center justify-center transition-opacity">
                            <iconify-icon icon="mdi:plus-circle" class="text-emerald-400 text-2xl"></iconify-icon>
                        </div>
                    `;
                    imagePreview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });

            if (files.length > maxFiles) {
                alert('Maksimal ' + maxFiles + ' foto yang dapat diupload.');
            }
        });
    }
});
</script>
@endpush

@endsection