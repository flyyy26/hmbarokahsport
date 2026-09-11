@php
    $isEdit = $isEdit ?? false;

    $existingOptions = $existingOptions ?? [];
    $existingVariants = $existingVariants ?? [];

    $existingOptionsJson = json_encode($existingOptions);
    $existingVariantsJson = json_encode($existingVariants);

    $savedFlashSaleIsExpired = isset($product) && $product->is_flash_sale
        && $product->flash_sale_status === 'expired';
    $hasSavedActiveFlashSale = isset($product) && $product->is_flash_sale
        && !$savedFlashSaleIsExpired;
    $flashSaleChecked = !$savedFlashSaleIsExpired
        && old('is_flash_sale', $hasSavedActiveFlashSale);
    $flashSaleStartValue = old(
        'flash_sale_start_date',
        $hasSavedActiveFlashSale && $product->flash_sale_start_date
            ? date('Y-m-d\TH:i', strtotime($product->flash_sale_start_date))
            : now()->format('Y-m-d\TH:i')
    );
    $flashSaleEndValue = old(
        'flash_sale_end_date',
        $hasSavedActiveFlashSale && $product->flash_sale_end_date
            ? date('Y-m-d\TH:i', strtotime($product->flash_sale_end_date))
            : now()->addDays(3)->format('Y-m-d\TH:i')
    );
@endphp

@csrf

{{-- ============================================ --}}
{{-- ERROR ALERT --}}
{{-- ============================================ --}}
@if ($errors->any())
    <div class="flex items-start gap-3 rounded-xl px-4 py-3 mb-6
                bg-red-500/10 border border-red-500/30 text-red-400">
        <iconify-icon icon="mdi:alert-circle-outline" class="text-xl flex-shrink-0 mt-0.5"></iconify-icon>
        <div class="flex-1 min-w-0">
            <p class="font-semibold text-sm">Produk belum tersimpan:</p>
            <ul class="mt-2 list-disc space-y-1 pl-5 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

{{-- Toast Container --}}
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2 w-full max-w-sm pointer-events-none"></div>

<style>
    .toast {
        transform: translateX(calc(100% + 2rem));
        transition: transform 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        pointer-events: auto;
    }
    .toast.show { transform: translateX(0); }
    .toast.hide { transform: translateX(calc(100% + 2rem)); }

    /* Section styling */
    .form-section {
        background: var(--bg-card);
        border: 1px solid var(--border-2);
        border-radius: 16px;
        padding: 1.5rem;
        transition: border-color 0.2s ease;
    }
    .form-section:hover {
        border-color: rgba(236, 188, 66, 0.3);
    }

    .form-section-header {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding-bottom: 1rem;
        margin-bottom: 1.25rem;
        border-bottom: 1px dashed var(--border-2);
    }

    .form-section-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.75rem;
        background: linear-gradient(135deg, #FDDD57 0%, #ecbc42 100%);
        color: #422006;
        flex-shrink: 0;
    }

    /* Inputs */
    .form-input {
        width: 100%;
        padding: 0.75rem 1rem;
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
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text-3);
        margin-bottom: 0.5rem;
    }

    .form-help {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.7rem;
        color: var(--text-5);
        margin-top: 0.4rem;
    }
</style>

<div class="space-y-6">

    {{-- ============================================ --}}
    {{-- SECTION 1: INFORMASI DASAR --}}
    {{-- ============================================ --}}
    <div class="form-section">
        <div class="form-section-header">
            <div class="form-section-icon">
                <iconify-icon icon="mdi:information-outline" class="text-xl"></iconify-icon>
            </div>
            <div>
                <h2 class="text-base font-bold" style="color: var(--text-1)">Informasi Dasar</h2>
                <p class="text-xs mt-0.5" style="color: var(--text-5)">Data utama produk Anda.</p>
            </div>
        </div>

        <div class="space-y-5">

            {{-- Nama Produk --}}
            <div>
                <label for="name" class="form-label">
                    <iconify-icon icon="mdi:tag-outline" class="text-[#ecbc42]"></iconify-icon>
                    Nama Produk <span class="text-red-400">*</span>
                </label>
                <input type="text" name="name" id="name"
                       value="{{ old('name', $product->name ?? '') }}" required
                       class="form-input"
                       placeholder="Contoh: Jaket Sport Pro Series">
                @error('name') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Kategori --}}
            <div>
                <label for="category_id" class="form-label">
                    <iconify-icon icon="mdi:folder-outline" class="text-[#ecbc42]"></iconify-icon>
                    Kategori <span class="text-red-400">*</span>
                </label>
                <select name="category_id" id="category_id" required class="form-input">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? '') == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Deskripsi (Quill) --}}
            <div>
                <label for="description" class="form-label">
                    <iconify-icon icon="mdi:text-box-outline" class="text-[#ecbc42]"></iconify-icon>
                    Deskripsi Produk
                </label>
                <textarea name="description" id="description" style="display:none;">{{ old('description', $product->description ?? '') }}</textarea>
                <div id="quill-editor" class="mt-1"></div>
                @error('description') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Grid: Gender + Bahan --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                {{-- Gender --}}
                <div>
                    <label for="gender" class="form-label">
                        <iconify-icon icon="mdi:gender-male-female" class="text-[#ecbc42]"></iconify-icon>
                        Jenis Kelamin
                    </label>
                    <select name="gender" id="gender" class="form-input">
                        <option value="">-- Pilih --</option>
                        <option value="pria" {{ old('gender', $product->gender ?? '') == 'pria' ? 'selected' : '' }}>Pria</option>
                        <option value="wanita" {{ old('gender', $product->gender ?? '') == 'wanita' ? 'selected' : '' }}>Wanita</option>
                        <option value="unisex" {{ old('gender', $product->gender ?? '') == 'unisex' ? 'selected' : '' }}>Unisex</option>
                    </select>
                    @error('gender') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Bahan --}}
                <div>
                    <label for="material" class="form-label">
                        <iconify-icon icon="mdi:texture-box" class="text-[#ecbc42]"></iconify-icon>
                        Bahan
                    </label>
                    <input type="text" name="material" id="material"
                           value="{{ old('material', $product->material ?? '') }}"
                           class="form-input"
                           placeholder="Contoh: Poliester, Katun">
                    @error('material') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Grid: Stok Minimum + Restock --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="minimum_stock" class="form-label">
                        <iconify-icon icon="mdi:alert-circle-outline" class="text-red-400"></iconify-icon>
                        Stok Minimum (Kritis)
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="minimum_stock" id="minimum_stock"
                               value="{{ old('minimum_stock', $product->minimum_stock ?? 5) }}"
                               min="0" class="form-input">
                        <span class="text-xs whitespace-nowrap" style="color: var(--text-5)">unit</span>
                    </div>
                    <p class="form-help text-red-400">
                        <iconify-icon icon="mdi:alert"></iconify-icon>
                        Jika stok mencapai angka ini, produk diberi label "Kritis"
                    </p>
                </div>

                <div>
                    <label for="restock_threshold" class="form-label">
                        <iconify-icon icon="mdi:alert-outline" class="text-amber-400"></iconify-icon>
                        Ambang Restock (Menipis)
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="restock_threshold" id="restock_threshold"
                               value="{{ old('restock_threshold', $product->restock_threshold ?? 10) }}"
                               min="0" class="form-input">
                        <span class="text-xs whitespace-nowrap" style="color: var(--text-5)">unit</span>
                    </div>
                    <p class="form-help text-amber-400">
                        <iconify-icon icon="mdi:alert"></iconify-icon>
                        Jika stok di bawah angka ini, produk diberi label "Menipis"
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- SECTION 2: GAMBAR PRODUK --}}
    {{-- ============================================ --}}
    <div class="form-section">
        <div class="form-section-header">
            <div class="form-section-icon">
                <iconify-icon icon="mdi:image-multiple-outline" class="text-xl"></iconify-icon>
            </div>
            <div>
                <h2 class="text-base font-bold" style="color: var(--text-1)">Gambar Produk</h2>
                <p class="text-xs mt-0.5" style="color: var(--text-5)">Upload beberapa gambar sekaligus.</p>
            </div>
        </div>

        @if ($isEdit && isset($product) && $product->images->isNotEmpty())
            <div class="mb-5">
                <p class="form-label">Gambar Saat Ini</p>
                <div id="existing-images-container" class="grid grid-cols-2 gap-3 sm:grid-cols-4 md:grid-cols-6">
                    @foreach ($product->images as $image)
                        <div class="existing-image relative group overflow-hidden rounded-xl border"
                             data-image-id="{{ $image->id }}"
                             style="border-color: var(--border-2)">
                            <img src="{{ Storage::url($image->image) }}" alt="{{ $product->name }}"
                                 class="aspect-square w-full object-cover">
                            <input type="hidden" name="existing_images[]" value="{{ $image->id }}">
                            <button type="button"
                                    class="remove-existing-image absolute right-2 top-2 flex h-7 w-7 items-center justify-center
                                           rounded-full bg-red-500/90 text-white shadow-lg
                                           transition-all opacity-0 group-hover:opacity-100
                                           hover:bg-red-600 active:scale-95">
                                <iconify-icon icon="mdi:close" class="text-sm"></iconify-icon>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div>
            <label for="images" class="form-label">
                <iconify-icon icon="mdi:cloud-upload-outline" class="text-[#ecbc42]"></iconify-icon>
                {{ $isEdit ? 'Tambah Gambar Baru' : 'Upload Gambar' }}
            </label>
            <input type="file" name="images[]" id="images" multiple accept="image/jpeg,image/png,image/webp"
                   class="form-input file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#ecbc42] file:text-slate-900 hover:file:bg-[#d4a72e] file:cursor-pointer cursor-pointer">
            <p class="form-help">
                <iconify-icon icon="mdi:information-outline"></iconify-icon>
                Format: JPG, PNG, WebP · Maks 2MB per gambar
            </p>
            @error('images') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            @error('images.*') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- SECTION 3: DISKON & FLASH SALE --}}
    {{-- ============================================ --}}
    <div class="form-section">
        <div class="form-section-header">
            <div class="form-section-icon">
                <iconify-icon icon="mdi:ticket-percent-outline" class="text-xl"></iconify-icon>
            </div>
            <div>
                <h2 class="text-base font-bold" style="color: var(--text-1)">Diskon & Flash Sale</h2>
                <p class="text-xs mt-0.5" style="color: var(--text-5)">Hanya satu jenis diskon yang dapat aktif dalam satu waktu.</p>
            </div>
        </div>

        {{-- Info Banner --}}
        <div class="flex items-start gap-2 px-3 py-2.5 rounded-lg mb-5
                    bg-amber-500/10 border border-amber-500/30 text-amber-400">
            <iconify-icon icon="mdi:information-outline" class="text-base flex-shrink-0 mt-0.5"></iconify-icon>
            <p class="text-xs">
                <strong>Pilih salah satu:</strong> Diskon Produk, Flash Sale, atau Diskon Varian.
                Mengaktifkan salah satu akan otomatis menonaktifkan yang lain.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">

            {{-- ============================================ --}}
            {{-- DISKON PRODUK --}}
            {{-- ============================================ --}}
            <div class="rounded-xl p-4 border"
                 style="background: var(--bg-input); border-color: var(--border-2)">

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="has_product_discount" value="1"
                           id="has_product_discount"
                           {{ old('has_product_discount', isset($product) ? $product->has_product_discount : false) ? 'checked' : '' }}
                           class="w-4 h-4 rounded cursor-pointer"
                           style="accent-color: #ecbc42">
                    <div>
                        <p class="text-sm font-bold" style="color: var(--text-1)">Diskon Produk</p>
                        <p class="text-xs" style="color: var(--text-5)">Berlaku untuk semua varian</p>
                    </div>
                </label>

                <div id="product_discount_fields"
                     class="mt-4 space-y-3 {{ (old('has_product_discount', isset($product) ? $product->has_product_discount : false)) ? '' : 'hidden' }}">

                    <div>
                        <label for="discount_type" class="form-label">Tipe Diskon</label>
                        <select name="discount_type" id="discount_type" class="form-input">
                            <option value="percentage" {{ old('discount_type', isset($product) ? $product->discount_type : '') == 'percentage' ? 'selected' : '' }}>
                                Persentase (%)
                            </option>
                            <option value="fixed" {{ old('discount_type', isset($product) ? $product->discount_type : '') == 'fixed' ? 'selected' : '' }}>
                                Potongan Harga (Rp)
                            </option>
                        </select>
                    </div>

                    <div>
                        <label for="discount_value" class="form-label">
                            Nilai Diskon
                            <span id="discount_value_label" class="text-[10px]" style="color: var(--text-5)">(%)</span>
                        </label>
                        <input type="number" name="discount_value" id="discount_value"
                               value="{{ old('discount_value', isset($product) ? $product->discount_value : '') }}"
                               min="0" step="0.01" class="form-input"
                               placeholder="Contoh: 10">
                    </div>
                </div>

                @if(isset($product) && $product->has_product_discount && $product->discount_value)
                    <div class="mt-3 px-3 py-2 rounded-lg bg-emerald-500/10 border border-emerald-500/30">
                        <p class="text-xs text-emerald-400">
                            <iconify-icon icon="mdi:check-circle-outline"></iconify-icon>
                            Status: <strong>{{ $product->product_discount_status_label }}</strong>
                            · @if($product->discount_type == 'percentage') {{ $product->discount_value }}% @else Rp {{ number_format($product->discount_value, 0, ',', '.') }} @endif
                        </p>
                    </div>
                @endif
            </div>

            {{-- ============================================ --}}
            {{-- FLASH SALE --}}
            {{-- ============================================ --}}
            <div class="rounded-xl p-4 border"
                 style="background: var(--bg-input); border-color: var(--border-2)">

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="is_flash_sale" value="0">
                    <input type="checkbox" name="is_flash_sale" value="1"
                           id="is_flash_sale"
                           {{ $flashSaleChecked ? 'checked' : '' }}
                           class="w-4 h-4 rounded cursor-pointer"
                           style="accent-color: #ecbc42">
                    <div>
                        <p class="text-sm font-bold flex items-center gap-2" style="color: var(--text-1)">
                            Flash Sale
                            <span class="text-[10px] text-amber-400 bg-amber-500/10 border border-amber-500/30 px-1.5 py-0.5 rounded-full">
                                ⏰ Waktu Terbatas
                            </span>
                        </p>
                        <p class="text-xs" style="color: var(--text-5)">Diskon dengan periode</p>
                    </div>
                </label>

                <div id="flash_sale_fields"
                     class="mt-4 space-y-3 {{ (old('is_flash_sale', isset($product) ? $product->is_flash_sale : false)) ? '' : 'hidden' }}">

                    <div>
                        <label for="flash_sale_type" class="form-label">Tipe Diskon</label>
                        <select name="flash_sale_type" id="flash_sale_type" class="form-input">
                            <option value="percentage" {{ old('flash_sale_type', isset($product) ? $product->flash_sale_type : '') == 'percentage' ? 'selected' : '' }}>
                                Persentase (%)
                            </option>
                            <option value="fixed" {{ old('flash_sale_type', isset($product) ? $product->flash_sale_type : '') == 'fixed' ? 'selected' : '' }}>
                                Potongan Harga (Rp)
                            </option>
                        </select>
                    </div>

                    <div>
                        <label for="flash_sale_value" class="form-label">
                            Nilai Diskon
                            <span id="flash_sale_value_label" class="text-[10px]" style="color: var(--text-5)">(%)</span>
                        </label>
                        <input type="number" name="flash_sale_value" id="flash_sale_value"
                               value="{{ old('flash_sale_value', isset($product) ? $product->flash_sale_value : '') }}"
                               min="0" step="0.01" class="form-input"
                               placeholder="Contoh: 20">
                    </div>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label for="flash_sale_start_date" class="form-label">
                                Mulai <span class="text-red-400">*</span>
                            </label>
                            <input type="datetime-local" name="flash_sale_start_date" id="flash_sale_start_date"
                                   value="{{ $flashSaleStartValue }}" class="form-input">
                            @error('flash_sale_start_date') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="flash_sale_end_date" class="form-label">
                                Berakhir <span class="text-red-400">*</span>
                            </label>
                            <input type="datetime-local" name="flash_sale_end_date" id="flash_sale_end_date"
                                   value="{{ $flashSaleEndValue }}" class="form-input">
                            @error('flash_sale_end_date') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                @if($hasSavedActiveFlashSale && $product->flash_sale_value)
                    <div class="mt-3 px-3 py-2 rounded-lg bg-amber-500/10 border border-amber-500/30">
                        <p class="text-xs text-amber-400">
                            <iconify-icon icon="mdi:clock-outline"></iconify-icon>
                            Status: <strong>{{ $product->flash_sale_status_label }}</strong>
                            · @if($product->flash_sale_type == 'percentage') {{ $product->flash_sale_value }}% @else Rp {{ number_format($product->flash_sale_value, 0, ',', '.') }} @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- SECTION 4: OPSI PRODUK --}}
    {{-- ============================================ --}}
    <div class="form-section">
        <div class="form-section-header">
            <div class="form-section-icon">
                <iconify-icon icon="mdi:palette-swatch-outline" class="text-xl"></iconify-icon>
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-base font-bold" style="color: var(--text-1)">Opsi Produk</h2>
                <p class="text-xs mt-0.5" style="color: var(--text-5)">Tambahkan opsi seperti Warna atau Ukuran.</p>
            </div>
            <div class="flex flex-wrap gap-2 ml-auto">
                <button type="button" id="add-color-option"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg
                               text-xs font-semibold transition-all active:scale-95"
                        style="background: var(--bg-input); border: 1px solid var(--border-2); color: var(--text-3)"
                        onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                        onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'">
                    <iconify-icon icon="mdi:palette-outline"></iconify-icon>
                    Tambah Warna
                </button>
                <button type="button" id="add-size-option"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg
                               text-xs font-bold transition-all active:scale-95
                               bg-gradient-to-r from-[#FDDD57] to-[#ecbc42] text-slate-900
                               hover:shadow-lg hover:shadow-amber-500/30">
                    <iconify-icon icon="mdi:ruler-square"></iconify-icon>
                    Tambah Ukuran
                </button>
            </div>
        </div>

        <div id="options-container" class="space-y-4"></div>
    </div>

    {{-- ============================================ --}}
    {{-- SECTION 5: VARIAN PRODUK --}}
    {{-- ============================================ --}}
    <div class="form-section">
        <div class="form-section-header">
            <div class="form-section-icon">
                <iconify-icon icon="mdi:layers-triple-outline" class="text-xl"></iconify-icon>
            </div>
            <div>
                <h2 class="text-base font-bold" style="color: var(--text-1)">Varian Produk</h2>
                <p class="text-xs mt-0.5" style="color: var(--text-5)">Kombinasi otomatis dari opsi yang Anda buat.</p>
            </div>
        </div>

        <div id="variants-container" class="space-y-3"></div>

        <div id="variants-empty" class="mt-4 rounded-xl border-2 border-dashed p-8 text-center"
             style="border-color: var(--border-2)">
            <iconify-icon icon="mdi:layers-off-outline" class="text-3xl mb-2" style="color: var(--text-6)"></iconify-icon>
            <p class="text-sm" style="color: var(--text-5)">Belum ada varian. Tambahkan opsi di atas untuk generate otomatis.</p>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- SECTION 6: FITUR PRODUK --}}
    {{-- ============================================ --}}
    <div class="form-section" id="features-container">
        <div class="form-section-header">
            <div class="form-section-icon">
                <iconify-icon icon="mdi:star-four-points-outline" class="text-xl"></iconify-icon>
            </div>
            <div>
                <h2 class="text-base font-bold" style="color: var(--text-1)">Fitur Produk</h2>
                <p class="text-xs mt-0.5" style="color: var(--text-5)">Pilih atau tambahkan fitur untuk produk ini.</p>
            </div>
        </div>

        {{-- Form Tambah Fitur --}}
        <div class="rounded-xl p-4 mb-4 border"
             style="background: var(--bg-input); border-color: var(--border-2)">
            <div class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[200px]">
                    <label for="new-feature-name" class="form-label">Nama Fitur Baru</label>
                    <input type="text" id="new-feature-name" class="form-input"
                           placeholder="Contoh: Anti Air, Ringan, Berkualitas">
                </div>
                <button type="button" id="add-feature-btn"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg
                               text-xs font-bold whitespace-nowrap
                               bg-gradient-to-r from-[#FDDD57] to-[#ecbc42] text-slate-900
                               hover:shadow-lg hover:shadow-amber-500/30
                               transition-all active:scale-95">
                    <iconify-icon icon="mdi:plus-circle"></iconify-icon>
                    Tambah Fitur
                </button>
            </div>
            <div id="feature-feedback" class="mt-2 text-xs hidden"></div>
        </div>

        {{-- Daftar Fitur --}}
        <div>
            <p class="form-label mb-3">Fitur Tersedia:</p>
            <div id="features-list" class="flex flex-wrap gap-2">
                @php
                    $selectedFeatures = $isEdit && isset($product) ? $product->features->pluck('id')->toArray() : [];
                @endphp
                @foreach ($features as $feature)
                    <label class="feature-item flex items-center gap-2 rounded-lg px-3 py-2 cursor-pointer
                                  transition-all border"
                           style="background: var(--bg-input); border-color: var(--border-2)">
                        <input type="checkbox" name="features[]" value="{{ $feature->id }}"
                               {{ in_array($feature->id, old('features', $selectedFeatures)) ? 'checked' : '' }}
                               class="feature-checkbox rounded cursor-pointer"
                               style="accent-color: #ecbc42">
                        <span class="text-sm" style="color: var(--text-3)">{{ $feature->name }}</span>
                    </label>
                @endforeach
            </div>
            @if($features->isEmpty())
                <p id="no-features-message" class="text-xs" style="color: var(--text-5)">
                    Belum ada fitur. Tambahkan fitur baru di atas.
                </p>
            @endif
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- SECTION 7: STATUS PRODUK --}}
    {{-- ============================================ --}}
    <div class="form-section">
        <div class="form-section-header">
            <div class="form-section-icon">
                <iconify-icon icon="mdi:flag-outline" class="text-xl"></iconify-icon>
            </div>
            <div>
                <h2 class="text-base font-bold" style="color: var(--text-1)">Status Produk</h2>
                <p class="text-xs mt-0.5" style="color: var(--text-5)">Atur tampilan produk di toko.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
            {{-- Featured --}}
            <label class="flex items-center gap-3 p-3 rounded-xl cursor-pointer border transition-all"
                   style="background: var(--bg-input); border-color: var(--border-2)"
                   onmouseover="this.style.borderColor='#ecbc42'"
                   onmouseout="this.style.borderColor='var(--border-2)'">
                <input type="hidden" name="is_featured" value="0">
                <input type="checkbox" name="is_featured" value="1"
                       {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}
                       class="w-4 h-4 rounded cursor-pointer"
                       style="accent-color: #ecbc42">
                <div>
                    <p class="text-sm font-semibold" style="color: var(--text-1)">Produk Unggulan</p>
                    <p class="text-xs" style="color: var(--text-5)">Tampil di bagian utama</p>
                </div>
            </label>

            {{-- Best Seller --}}
            <label class="flex items-center gap-3 p-3 rounded-xl cursor-pointer border transition-all"
                   style="background: var(--bg-input); border-color: var(--border-2)"
                   onmouseover="this.style.borderColor='#ecbc42'"
                   onmouseout="this.style.borderColor='var(--border-2)'">
                <input type="hidden" name="is_best_seller" value="0">
                <input type="checkbox" name="is_best_seller" value="1"
                       {{ old('is_best_seller', $product->is_best_seller ?? false) ? 'checked' : '' }}
                       class="w-4 h-4 rounded cursor-pointer"
                       style="accent-color: #ecbc42">
                <div>
                    <p class="text-sm font-semibold" style="color: var(--text-1)">Produk Laris</p>
                    <p class="text-xs" style="color: var(--text-5)">Label best seller</p>
                </div>
            </label>

            {{-- Active --}}
            <label class="flex items-center gap-3 p-3 rounded-xl cursor-pointer border transition-all"
                   style="background: var(--bg-input); border-color: var(--border-2)"
                   onmouseover="this.style.borderColor='#ecbc42'"
                   onmouseout="this.style.borderColor='var(--border-2)'">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1"
                       {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}
                       class="w-4 h-4 rounded cursor-pointer"
                       style="accent-color: #ecbc42">
                <div>
                    <p class="text-sm font-semibold" style="color: var(--text-1)">Produk Aktif</p>
                    <p class="text-xs" style="color: var(--text-5)">Tampil di toko</p>
                </div>
            </label>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- ACTIONS --}}
    {{-- ============================================ --}}
    <div class="flex flex-col-reverse sm:flex-row sm:justify-between gap-3 pt-6 border-t"
         style="border-color: var(--border-2)">

        <div class="hidden sm:flex items-center gap-2 text-xs" style="color: var(--text-5)">
            <iconify-icon icon="mdi:information-outline"></iconify-icon>
            <span>Kolom dengan tanda <span class="text-red-400">*</span> wajib diisi</span>
        </div>

        <div class="flex flex-col-reverse sm:flex-row gap-3 w-full sm:w-auto">
            <a href="{{ route('admin.products.index') }}"
               class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-lg
                      text-sm font-semibold transition-all active:scale-95"
               style="background: var(--bg-input); border: 1px solid var(--border-2); color: var(--text-3)"
               onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
               onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'">
                <iconify-icon icon="mdi:close"></iconify-icon>
                Batal
            </a>

            <button type="submit" id="submit-btn"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg
                           text-sm font-bold
                           bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                           text-slate-900
                           shadow-lg shadow-amber-500/20
                           hover:shadow-xl hover:shadow-amber-500/40
                           hover:-translate-y-0.5
                           transition-all active:scale-95 active:translate-y-0">
                <iconify-icon icon="mdi:content-save-outline" class="text-lg"></iconify-icon>
                {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Produk' }}
            </button>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quill for product description is initialized in admin layout
});
</script>

<script>
// ============================================
// 🔥 DATA DARI PHP
// ============================================
var existingOptions = [];
var existingVariants = [];
var isEditMode = false;
var productId = 0;

try {
    existingOptions = @json($existingOptions ?? []);
} catch(e) { existingOptions = []; }

try {
    existingVariants = @json($existingVariants ?? []);
} catch(e) { existingVariants = []; }

isEditMode = {{ $isEdit ? 'true' : 'false' }};
productId = {{ isset($product) && $product ? $product->id : 0 }};

function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    var div = document.createElement('div');
    div.textContent = String(text);
    return div.innerHTML;
}

function showToast(message, type) {
    var container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed top-4 right-4 z-50 space-y-2 w-full max-w-sm';
        document.body.appendChild(container);
    }
    var toast = document.createElement('div');
    var colors = {
        error: 'bg-red-50 border-red-500 text-red-700',
        success: 'bg-green-50 border-green-500 text-green-700',
        warning: 'bg-yellow-50 border-yellow-500 text-yellow-700'
    };
    toast.className = 'toast show rounded-lg border-l-4 ' + (colors[type] || colors.error) + ' bg-white shadow-lg p-4';
    toast.innerHTML = '<div class="flex items-start gap-3"><span>' + message + '</span></div>';
    container.appendChild(toast);
    setTimeout(function() { toast.remove(); }, 5000);
}

document.addEventListener('DOMContentLoaded', function() {

    // ============================================
    // 🔥 MUTUAL EXCLUSION FOR THREE DISCOUNT TYPES
    // ============================================

    // Check if any variant has discount
    function hasAnyVariantDiscount() {
        var discountInputs = document.querySelectorAll('.discount-percent-input');
        for (var i = 0; i < discountInputs.length; i++) {
            var val = parseFloat(discountInputs[i].value);
            if (!isNaN(val) && val > 0) return true;
        }
        return false;
    }

    // Check if flash sale is active
    function isFlashSaleActive() {
        var checkbox = document.getElementById('is_flash_sale');
        return checkbox && checkbox.checked;
    }

    // Check if product discount is active
    function isProductDiscountActive() {
        var checkbox = document.getElementById('has_product_discount');
        return checkbox && checkbox.checked;
    }

    // Sync all discount states - only one can be active
    function syncDiscountStates() {
        var isProductActive = isProductDiscountActive();
        var isVariantActive = hasAnyVariantDiscount();
        var isFlashActive = isFlashSaleActive();

        var productCheckbox = document.getElementById('has_product_discount');
        var flashCheckbox = document.getElementById('is_flash_sale');
        var discountFields = document.getElementById('product_discount_fields');
        var flashFields = document.getElementById('flash_sale_fields');
        
        var variantInputs = document.querySelectorAll('.discount-percent-input');
        var variantPriceInputs = document.querySelectorAll('.discount-price-input');

        // CASE 1: FLASH SALE ACTIVE
        if (isFlashActive) {
            // Disable Product Discount
            if (productCheckbox) {
                productCheckbox.checked = false;
                productCheckbox.disabled = true;
                if (discountFields) discountFields.classList.add('hidden');
            }
            
            // Disable Variant Discount
            variantInputs.forEach(function(input) {
                input.value = '0';
                input.readOnly = true;
                input.classList.add('bg-gray-100', 'cursor-not-allowed', 'opacity-60');
            });
            variantPriceInputs.forEach(function(input) {
                input.value = '';
                input.style.backgroundColor = '#f3f4f6';
                input.style.color = '#6b7280';
            });
            
            // Enable Flash Sale fields
            if (flashFields) flashFields.classList.remove('hidden');
        }
        // CASE 2: PRODUCT DISCOUNT ACTIVE
        else if (isProductActive) {
            // Disable Flash Sale
            if (flashCheckbox) {
                flashCheckbox.checked = false;
                flashCheckbox.disabled = true;
                if (flashFields) flashFields.classList.add('hidden');
            }
            
            // Disable Variant Discount
            variantInputs.forEach(function(input) {
                input.value = '0';
                input.readOnly = true;
                input.classList.add('bg-gray-100', 'cursor-not-allowed', 'opacity-60');
            });
            variantPriceInputs.forEach(function(input) {
                input.value = '';
                input.style.backgroundColor = '#f3f4f6';
                input.style.color = '#6b7280';
            });
            
            // Enable Product Discount fields
            if (discountFields) discountFields.classList.remove('hidden');
        }
        // CASE 3: VARIANT DISCOUNT ACTIVE
        else if (isVariantActive) {
            // Disable Product Discount
            if (productCheckbox) {
                productCheckbox.checked = false;
                productCheckbox.disabled = true;
                if (discountFields) discountFields.classList.add('hidden');
            }
            
            // Disable Flash Sale
            if (flashCheckbox) {
                flashCheckbox.checked = false;
                flashCheckbox.disabled = true;
                if (flashFields) flashFields.classList.add('hidden');
            }
            
            // Enable Variant Discount inputs
            variantInputs.forEach(function(input) {
                input.readOnly = false;
                input.classList.remove('bg-gray-100', 'cursor-not-allowed', 'opacity-60');
            });
            variantPriceInputs.forEach(function(input) {
                input.style.backgroundColor = '#f3f4f6';
                input.style.color = '#6b7280';
            });
        }
        // CASE 4: NO DISCOUNT ACTIVE
        else {
            // Enable all checkboxes
            if (productCheckbox) productCheckbox.disabled = false;
            if (flashCheckbox) flashCheckbox.disabled = false;
            
            // Enable Variant Discount inputs
            variantInputs.forEach(function(input) {
                input.readOnly = false;
                input.classList.remove('bg-gray-100', 'cursor-not-allowed', 'opacity-60');
            });
            variantPriceInputs.forEach(function(input) {
                input.style.backgroundColor = '#f3f4f6';
                input.style.color = '#6b7280';
            });
        }
    }

    // ============================================
    // 🔥 PRODUCT DISCOUNT TOGGLE
    // ============================================

    var productDiscountCheckbox = document.getElementById('has_product_discount');
    var discountFields = document.getElementById('product_discount_fields');

    function toggleDiscountFields() {
        if (productDiscountCheckbox && discountFields) {
            discountFields.classList.toggle('hidden', !productDiscountCheckbox.checked);
        }
    }

    if (productDiscountCheckbox) {
        productDiscountCheckbox.addEventListener('change', function() {
            // If turning ON product discount, turn OFF others
            if (this.checked) {
                // Turn off Flash Sale
                var flashCheckbox = document.getElementById('is_flash_sale');
                if (flashCheckbox) {
                    flashCheckbox.checked = false;
                    flashCheckbox.disabled = true;
                    var flashFields = document.getElementById('flash_sale_fields');
                    if (flashFields) flashFields.classList.add('hidden');
                }
                
                // Turn off variant discounts
                document.querySelectorAll('.discount-percent-input').forEach(function(input) {
                    input.value = '0';
                    input.readOnly = true;
                    input.classList.add('bg-gray-100', 'cursor-not-allowed', 'opacity-60');
                });
                document.querySelectorAll('.discount-price-input').forEach(function(input) {
                    input.value = '';
                    input.style.backgroundColor = '#f3f4f6';
                    input.style.color = '#6b7280';
                });
            } else {
                // Enable others
                var flashCheckbox = document.getElementById('is_flash_sale');
                if (flashCheckbox) flashCheckbox.disabled = false;
                
                document.querySelectorAll('.discount-percent-input').forEach(function(input) {
                    input.readOnly = false;
                    input.classList.remove('bg-gray-100', 'cursor-not-allowed', 'opacity-60');
                });
            }
            toggleDiscountFields();
            syncDiscountStates();
        });
        
        // Initial state
        toggleDiscountFields();
    }

    // ============================================
    // 🔥 PRODUCT DISCOUNT - UPDATE LABEL
    // ============================================

    var discountType = document.getElementById('discount_type');
    var discountValueLabel = document.getElementById('discount_value_label');
    var discountValueInput = document.getElementById('discount_value');

    function updateDiscountLabel() {
        if (discountType && discountValueLabel && discountValueInput) {
            var isPercentage = discountType.value === 'percentage';
            discountValueLabel.textContent = isPercentage ? '(%)' : '(Rp)';
            discountValueInput.placeholder = isPercentage ? 'Contoh: 10' : 'Contoh: 50000';
            discountValueInput.step = isPercentage ? '0.01' : '1000';
        }
    }

    if (discountType) {
        discountType.addEventListener('change', updateDiscountLabel);
        updateDiscountLabel();
    }

    // ============================================
    // 🔥 FLASH SALE TOGGLE
    // ============================================

    var flashSaleCheckbox = document.getElementById('is_flash_sale');
    var flashSaleFields = document.getElementById('flash_sale_fields');

    function toggleFlashSaleFields() {
        if (flashSaleCheckbox && flashSaleFields) {
            flashSaleFields.classList.toggle('hidden', !flashSaleCheckbox.checked);
        }
    }

    if (flashSaleCheckbox) {
        flashSaleCheckbox.addEventListener('change', function() {
            // If turning ON flash sale, turn OFF others
            if (this.checked) {
                // Turn off Product Discount
                var productCheckbox = document.getElementById('has_product_discount');
                if (productCheckbox) {
                    productCheckbox.checked = false;
                    productCheckbox.disabled = true;
                    if (discountFields) discountFields.classList.add('hidden');
                }
                
                // Turn off variant discounts
                document.querySelectorAll('.discount-percent-input').forEach(function(input) {
                    input.value = '0';
                    input.readOnly = true;
                    input.classList.add('bg-gray-100', 'cursor-not-allowed', 'opacity-60');
                });
                document.querySelectorAll('.discount-price-input').forEach(function(input) {
                    input.value = '';
                    input.style.backgroundColor = '#f3f4f6';
                    input.style.color = '#6b7280';
                });
                
                // Set default dates if empty
                var startDate = document.getElementById('flash_sale_start_date');
                var endDate = document.getElementById('flash_sale_end_date');
                if (startDate && !startDate.value) {
                    var now = new Date();
                    startDate.value = now.getFullYear() + '-' + 
                        String(now.getMonth() + 1).padStart(2, '0') + '-' + 
                        String(now.getDate()).padStart(2, '0') + 'T' + 
                        String(now.getHours()).padStart(2, '0') + ':' + 
                        String(now.getMinutes()).padStart(2, '0');
                }
                if (endDate && !endDate.value) {
                    var future = new Date();
                    future.setDate(future.getDate() + 3);
                    endDate.value = future.getFullYear() + '-' + 
                        String(future.getMonth() + 1).padStart(2, '0') + '-' + 
                        String(future.getDate()).padStart(2, '0') + 'T' + 
                        String(future.getHours()).padStart(2, '0') + ':' + 
                        String(future.getMinutes()).padStart(2, '0');
                }
            } else {
                // Enable others
                var productCheckbox = document.getElementById('has_product_discount');
                if (productCheckbox) productCheckbox.disabled = false;
                
                document.querySelectorAll('.discount-percent-input').forEach(function(input) {
                    input.readOnly = false;
                    input.classList.remove('bg-gray-100', 'cursor-not-allowed', 'opacity-60');
                });
            }
            toggleFlashSaleFields();
            syncDiscountStates();
        });
        
        // Initial state
        toggleFlashSaleFields();
    }

    // ============================================
    // 🔥 FLASH SALE - UPDATE LABEL
    // ============================================

    var flashSaleType = document.getElementById('flash_sale_type');
    var flashSaleValueLabel = document.getElementById('flash_sale_value_label');
    var flashSaleValue = document.getElementById('flash_sale_value');

    function updateFlashSaleLabel() {
        if (flashSaleType && flashSaleValueLabel && flashSaleValue) {
            var isPercentage = flashSaleType.value === 'percentage';
            flashSaleValueLabel.textContent = isPercentage ? '(%)' : '(Rp)';
            flashSaleValue.placeholder = isPercentage ? 'Contoh: 20' : 'Contoh: 50000';
            flashSaleValue.step = isPercentage ? '0.01' : '1000';
        }
    }

    if (flashSaleType) {
        flashSaleType.addEventListener('change', updateFlashSaleLabel);
        updateFlashSaleLabel();
    }

    // ============================================
    // 🔥 VARIANT DISCOUNT - LISTEN FOR CHANGES
    // ============================================

    document.addEventListener('input', function(e) {
        if (e.target && e.target.classList.contains('discount-percent-input')) {
            var val = parseFloat(e.target.value);
            if (val > 0) {
                // If variant discount is being set, turn off others
                var productCheckbox = document.getElementById('has_product_discount');
                if (productCheckbox) {
                    productCheckbox.checked = false;
                    productCheckbox.disabled = true;
                    if (discountFields) discountFields.classList.add('hidden');
                }
                
                var flashCheckbox = document.getElementById('is_flash_sale');
                if (flashCheckbox) {
                    flashCheckbox.checked = false;
                    flashCheckbox.disabled = true;
                    if (flashSaleFields) flashSaleFields.classList.add('hidden');
                }
            } else {
                // Check if any variant still has discount
                var hasDiscount = false;
                document.querySelectorAll('.discount-percent-input').forEach(function(input) {
                    var v = parseFloat(input.value);
                    if (!isNaN(v) && v > 0) hasDiscount = true;
                });
                
                if (!hasDiscount) {
                    var productCheckbox = document.getElementById('has_product_discount');
                    if (productCheckbox) productCheckbox.disabled = false;
                    
                    var flashCheckbox = document.getElementById('is_flash_sale');
                    if (flashCheckbox) flashCheckbox.disabled = false;
                }
            }
            syncDiscountStates();
        }
    });

    // ============================================
    // 🔥 INITIAL SYNC
    // ============================================

    // Call sync after all elements are ready
    setTimeout(function() {
        syncDiscountStates();
    }, 200);

    // ============================================
    // 🔥 DOM ELEMENTS VARIAN (Existing Logic)
    // ============================================

    var optionsContainer = document.getElementById('options-container');
    var optionsEmpty = document.getElementById('options-empty');
    var variantsContainer = document.getElementById('variants-container');
    var variantsEmpty = document.getElementById('variants-empty');
    var addColorOptionButton = document.getElementById('add-color-option');
    var addSizeOptionButton = document.getElementById('add-size-option');
    var generateVariantsButton = document.getElementById('generate-variants');

    var optionIndex = 0;
    var DEFAULT_SIZES = ['S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL', '5XL'];
    var savedVariantData = [];

    function updateOptionsEmptyState() {
        if (optionsContainer && optionsEmpty) {
            var total = optionsContainer.querySelectorAll('.option-item').length;
            optionsEmpty.classList.toggle('hidden', total > 0);
        }
    }

    function updateVariantsEmptyState() {
        if (variantsContainer && variantsEmpty) {
            var total = variantsContainer.querySelectorAll('.variant-item').length;
            variantsEmpty.classList.toggle('hidden', total > 0);
        }
    }

    function updateOptionValueCounter(optionElement) {
        if (!optionElement) return;

        var counter = optionElement.querySelector('.value-counter');
        if (!counter) return;

        var total = optionElement.querySelectorAll('.option-value-row').length;
        counter.textContent = total;

        // Update teks "warna" / "ukuran"
        var counterLabel = counter.parentElement;
        if (counterLabel) {
            var isSize = optionElement.dataset.isSize === 'true';
            var textNode = counterLabel.childNodes[counterLabel.childNodes.length - 1];
            if (textNode && textNode.nodeType === Node.TEXT_NODE) {
                textNode.textContent = isSize ? ' ukuran' : ' warna';
            }
        }
    }

    // ============================================
    // LOGIKA VARIAN & RE-INDEXING
    // ============================================
    function captureVariantData() {
        var variants = [];
        var variantItems = document.querySelectorAll('.variant-item');
        
        variantItems.forEach(function(item) {
            var data = {};
            var comboEl = item.querySelector('.variant-item-combination');
            data.combination_text = comboEl ? comboEl.textContent.trim() : '';
            
            var valueNames = [];
            item.querySelectorAll('.variant-value-name').forEach(function(el) {
                valueNames.push(el.textContent.trim());
            });
            data.value_names = valueNames.sort().join('|');
            
            var skuInput = item.querySelector('.sku-input');
            data.sku = skuInput ? skuInput.value : '';
            
            var priceInput = item.querySelector('.price-input');
            data.price = priceInput ? priceInput.value : '';
            
            var percentInput = item.querySelector('.discount-percent-input');
            data.discount_percent = percentInput ? percentInput.value : '0';
            
            var discountPriceInput = item.querySelector('.discount-price-input');
            data.discount_price = discountPriceInput ? discountPriceInput.value : '';
            
            var stockInput = item.querySelector('.stock-input');
            data.stock = stockInput ? stockInput.value : '0';
            
            var weightInput = item.querySelector('input[name$="[weight]"]');
            data.weight = weightInput ? weightInput.value : '1000';
            
            var idInput = item.querySelector('input[name$="[id]"]');
            data.id = idInput ? idInput.value : '';
            
            var indexes = {};
            item.querySelectorAll('input[name$="[option_value_indexes]"]').forEach(function(input) {
                var match = input.name.match(/variants\[\d+\]\[option_value_indexes\]\[(\d+)\]/);
                if (match) indexes[parseInt(match[1])] = parseInt(input.value);
            });
            data.option_value_indexes = indexes;
            
            variants.push(data);
        });
        
        savedVariantData = variants;
        return variants;
    }

    function getOptions() {
        var options = [];
        if (!optionsContainer) return options;
        
        optionsContainer.querySelectorAll('.option-item').forEach(function(el) {
            var nameInput = el.querySelector('.option-name');
            var name = nameInput ? nameInput.value.trim() : '';
            
            var values = [];
            el.querySelectorAll('.option-value-input').forEach(function(input) {
                var val = input.value.trim();
                if (val !== '') values.push(val);
            });
            
            if (name && values.length > 0) {
                options.push({ name: name, values: values });
            }
        });
        return options;
    }

    function generateCombinations(options) {
        var result = [[]];
        options.forEach(function(option, optionIdx) {
            var newResult = [];
            result.forEach(function(combination) {
                option.values.forEach(function(value, valueIdx) {
                    newResult.push([...combination, {
                        optionIndex: optionIdx,
                        valueIndex: valueIdx,
                        value: value
                    }]);
                });
            });
            result = newResult;
        });
        return result;
    }

    function renderVariants(combinations) {
        if (!variantsContainer) return;
        variantsContainer.innerHTML = '';

        var validCombinations = combinations.filter(function(combo) {
            return combo.every(function(item) {
                return item.value && item.value.trim() !== '';
            });
        });

        if (validCombinations.length === 0) {
            variantsContainer.innerHTML = `
                <div class="p-8 text-center rounded-xl border-2 border-dashed"
                    style="border-color: var(--border-2); background: var(--bg-input)">
                    <iconify-icon icon="mdi:layers-off-outline" class="text-3xl mb-2" style="color: var(--text-6)"></iconify-icon>
                    <p class="text-sm" style="color: var(--text-5)">Tidak ada kombinasi varian yang valid.</p>
                </div>
            `;
            updateVariantsEmptyState();
            return;
        }

        // Counter variant
        var variantsCounterHtml = `
            <div class="flex items-center justify-between gap-3 mb-3 px-4 py-2.5 rounded-lg border"
                style="background: var(--bg-input); border-color: var(--border-2)">
                <div class="flex items-center gap-2">
                    <iconify-icon icon="mdi:layers-triple-outline" class="text-[#ecbc42] text-base"></iconify-icon>
                    <span class="text-xs font-semibold" style="color: var(--text-3)">
                        <span id="variant-count">${validCombinations.length}</span> varian
                    </span>
                </div>
                <button type="button"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                            text-[11px] font-semibold
                            transition-all active:scale-95"
                        style="background: var(--bg-card); border: 1px solid var(--border-2); color: var(--text-4)"
                        onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                        onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-4)'"
                        onclick="clearAllVariants()">
                    <iconify-icon icon="mdi:refresh"></iconify-icon>
                    Reset Semua
                </button>
            </div>
        `;

        var existingMap = {};
        savedVariantData.forEach(function(v) {
            if (v.value_names) existingMap[v.value_names] = v;
            if (v.combination_text) existingMap[v.combination_text] = v;
        });

        validCombinations.forEach(function(combination, index) {
            var comboText = combination.map(function(item) { return item.value; }).join(' · ');
            var valueNames = combination.map(function(item) { return item.value.trim(); }).sort().join('|');
            var existingData = existingMap[valueNames] || existingMap[comboText] || null;

            var variant = document.createElement('div');
            variant.className = 'variant-item rounded-2xl border overflow-hidden mb-3';
            variant.style.cssText = 'background: var(--bg-card); border-color: var(--border-2);';

            var optionValueIndexesHtml = '';
            combination.forEach(function(item) {
                optionValueIndexesHtml += `
                    <input type="hidden"
                        name="variants[${index}][option_value_indexes][${item.optionIndex}]"
                        value="${item.valueIndex}">
                `;
            });

            var existingIdHtml = existingData?.id ?
                `<input type="hidden" name="variants[${index}][id]" value="${existingData.id}">` : '';

            var sku = existingData?.sku ?? '';
            var price = existingData?.price ?? '';
            var discountPercent = existingData?.discount_percent ?? '0';
            var discountPrice = existingData?.discount_price ?? '';
            var stock = existingData?.stock ?? '0';
            var weight = existingData?.weight ?? '1000';

            var isNew = !existingData;
            var statusBadge = isNew ?
                `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                            text-[10px] font-bold
                            bg-[#ecbc42]/10 border border-[#ecbc42]/30 text-[#FDDD57]">
                    <iconify-icon icon="mdi:plus-circle" class="text-xs"></iconify-icon>
                    Varian Baru
                </span>` :
                `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                            text-[10px] font-bold
                            bg-emerald-500/10 border border-emerald-500/30 text-emerald-400">
                    <iconify-icon icon="mdi:check-circle" class="text-xs"></iconify-icon>
                    Sudah Ada
                </span>`;

            var valueNamesHtml = combination.map(function(item) {
                return `<span class="variant-value-name hidden">${escapeHtml(item.value)}</span>`;
            }).join('');

            // Split combination values jadi chips
            var comboBadgesHtml = combination.map(function(item) {
                return `<span class="inline-flex items-center px-2 py-0.5 rounded-md
                                    text-[11px] font-semibold"
                            style="background: var(--bg-input); border: 1px solid var(--border-2); color: var(--text-3)">
                            ${escapeHtml(item.value)}
                        </span>`;
            }).join('<iconify-icon icon="mdi:chevron-right" class="text-xs" style="color: var(--text-6)"></iconify-icon>');

            variant.innerHTML = `
                ${optionValueIndexesHtml}
                ${existingIdHtml}
                ${valueNamesHtml}

                {{-- Header Varian --}}
                <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 border-b"
                    style="background: var(--bg-input); border-color: var(--border-2)">

                    <div class="flex items-center gap-3 min-w-0 flex-wrap">
                        <div class="flex items-center gap-1 flex-wrap variant-item-combination">
                            ${comboBadgesHtml}
                        </div>
                        ${statusBadge}
                    </div>
                </div>

                {{-- Body --}}
                <div class="p-4">

                    {{-- Row 1: SKU (Full width) --}}
                    <div class="mb-3">
                        <label class="form-label">
                            <iconify-icon icon="mdi:barcode-scan" class="text-[#ecbc42]"></iconify-icon>
                            SKU <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="variants[${index}][sku]"
                            value="${escapeHtml(sku)}" required
                            class="sku-input form-input"
                            placeholder="Contoh: JKT-MERAH-M">
                    </div>

                    {{-- Row 2: Grid 4 kolom --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">

                        {{-- Harga --}}
                        <div>
                            <label class="form-label">
                                <iconify-icon icon="mdi:cash" class="text-[#ecbc42]"></iconify-icon>
                                Harga <span class="text-red-400">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-0 top-0 bottom-0 flex items-center pl-3 pr-2.5 text-xs font-bold pointer-events-none border-r"
                                    style="color: var(--text-4); border-color: var(--border-2); background: var(--bg-hover); border-radius: 0.65rem 0 0 0.65rem;">
                                    Rp
                                </span>
                                <input type="number" name="variants[${index}][price]"
                                    value="${escapeHtml(price)}" min="0" required
                                    class="price-input form-input font-semibold"
                                    style="padding-left: 3.25rem;"
                                    data-variant-index="${index}"
                                    oninput="calculateDiscount(this, ${index})"
                                    placeholder="0">
                            </div>
                        </div>

                        {{-- Diskon (%) --}}
                        <div>
                            <label class="form-label">
                                <iconify-icon icon="mdi:percent" class="text-[#ecbc42]"></iconify-icon>
                                Diskon
                            </label>
                            <div class="relative">
                                <input type="number" name="variants[${index}][discount_percent]"
                                    value="${escapeHtml(discountPercent)}" min="0" max="100"
                                    class="discount-percent-input form-input font-semibold"
                                    style="padding-right: 2.75rem;"
                                    data-variant-index="${index}"
                                    oninput="calculateDiscount(this, ${index})"
                                    placeholder="0">
                                <span class="absolute right-0 top-0 bottom-0 flex items-center pl-2.5 pr-3 text-xs font-bold pointer-events-none border-l"
                                    style="color: var(--text-4); border-color: var(--border-2); background: var(--bg-hover); border-radius: 0 0.65rem 0.65rem 0;">
                                    %
                                </span>
                            </div>
                        </div>

                        {{-- Harga Diskon --}}
                        <div>
                            <label class="form-label">
                                <iconify-icon icon="mdi:tag-outline" class="text-[#ecbc42]"></iconify-icon>
                                Harga Diskon
                            </label>
                            <div class="relative">
                                <span class="absolute left-0 top-0 bottom-0 flex items-center pl-3 pr-2.5 text-xs font-bold pointer-events-none border-r"
                                    style="color: var(--text-4); border-color: var(--border-2); background: var(--bg-hover); border-radius: 0.65rem 0 0 0.65rem;">
                                    Rp
                                </span>
                                <input type="number" name="variants[${index}][discount_price]"
                                    value="${escapeHtml(discountPrice)}" min="0" step="0.01"
                                    class="discount-price-input form-input font-semibold"
                                    style="padding-left: 3.25rem;"
                                    data-variant-index="${index}"
                                    readonly
                                    placeholder="0">
                            </div>
                        </div>

                        {{-- Stok --}}
                        <div>
                            <label class="form-label">
                                <iconify-icon icon="mdi:package-variant-closed" class="text-[#ecbc42]"></iconify-icon>
                                Stok <span class="text-red-400">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" name="variants[${index}][stock]"
                                    value="${stock}" min="0" required
                                    class="stock-input form-input pr-14"
                                    placeholder="0">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-semibold pointer-events-none"
                                    style="color: var(--text-5)">pcs</span>
                            </div>
                        </div>

                        {{-- Row 3: Berat --}}
                        <div>
                            <label class="form-label">
                                <iconify-icon icon="mdi:weight-gram" class="text-[#ecbc42]"></iconify-icon>
                                Berat
                            </label>
                            <div class="relative max-w-xs">
                                <input type="number" name="variants[${index}][weight]"
                                    value="${escapeHtml(weight)}" min="0" required
                                    class="form-input pr-14"
                                    placeholder="1000">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-semibold pointer-events-none"
                                    style="color: var(--text-5)">gram</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            variantsContainer.appendChild(variant);
        });

        // Prepend counter di atas
        if (variantsContainer.firstChild) {
            variantsContainer.insertAdjacentHTML('afterbegin', variantsCounterHtml);
        }

        updateVariantsEmptyState();
        syncDiscountStates();
        setTimeout(recalculateAllDiscounts, 100);
    }

    function triggerRegenerate() {
        clearTimeout(window.regenerateTimeout);
        window.regenerateTimeout = setTimeout(function() {
            var options = getOptions();
            if (options.length >= 1) {
                captureVariantData();
                var combinations = generateCombinations(options);
                renderVariants(combinations);
            } else {
                if (variantsContainer) variantsContainer.innerHTML = '';
                updateVariantsEmptyState();
            }
        }, 300);
    }

    function reindexOptions() {
        if (!optionsContainer) return;
        optionsContainer.querySelectorAll('.option-item').forEach(function(optionEl, optIdx) {
            optionEl.dataset.optionIndex = optIdx;
            
            var nameInput = optionEl.querySelector('.option-name');
            if (nameInput) nameInput.name = `options[${optIdx}][name]`;
            
            var oldIdInput = optionEl.querySelector('input[name*="[old_id]"]');
            if (oldIdInput) oldIdInput.name = `options[${optIdx}][old_id]`;
            
            optionEl.querySelectorAll('.option-value-row').forEach(function(row, valIdx) {
                var valInput = row.querySelector('.option-value-input');
                if (valInput) valInput.name = `options[${optIdx}][values][${valIdx}]`;
                
                var fileInput = row.querySelector('.option-image-input');
                if (fileInput) fileInput.name = `options[${optIdx}][images][${valIdx}]`;
                
                var existingImgInput = row.querySelector('input[name*="[existing_images]"]');
                if (existingImgInput) existingImgInput.name = `options[${optIdx}][existing_images][${valIdx}]`;
                
                var oldValIdInput = row.querySelector('input[name*="[old_value_ids]"]');
                if (oldValIdInput) oldValIdInput.name = `options[${optIdx}][old_value_ids][${valIdx}]`;
            });
        });
    }

    function addOptionValue(optionElement, optionIdx, value, image, oldValueId) {
        var valuesContainer = optionElement.querySelector('.option-values');
        var isSize = optionElement.dataset.isSize === 'true';
        var valIdx = valuesContainer.querySelectorAll('.option-value-row').length;

        var row = document.createElement('div');
        row.className = 'option-value-row';

        // ============================================
        // UKURAN: Compact box (1 field + tombol hapus)
        // ============================================
        if (isSize) {
            row.className += ' relative flex items-center gap-1 p-1.5 rounded-lg border';
            row.style.cssText = 'background: var(--bg-input); border-color: var(--border-2);';

            row.innerHTML = `
                ${oldValueId ? `<input type="hidden" name="options[${optionIdx}][old_value_ids][${valIdx}]" value="${oldValueId}">` : ''}

                <input type="text"
                    name="options[${optionIdx}][values][${valIdx}]"
                    value="${escapeHtml(value || '')}"
                    required
                    class="option-value-input form-input text-center font-semibold"
                    style="padding: 0.5rem 0.5rem; font-size: 0.85rem;"
                    placeholder="XL">

                <button type="button"
                        class="remove-option-value inline-flex items-center justify-center w-8 h-8 rounded-md
                            bg-red-500/10 border border-red-500/20 text-red-400
                            hover:bg-red-500/20 hover:border-red-500/40
                            transition-all active:scale-95 flex-shrink-0"
                        title="Hapus ukuran">
                    <iconify-icon icon="mdi:close" class="text-sm"></iconify-icon>
                </button>
            `;
        }
        // ============================================
        // WARNA: Horizontal (input + upload + preview + hapus)
        // ============================================
        else {
            row.className += ' flex flex-wrap items-center gap-2 p-2.5 rounded-lg border';
            row.style.cssText = 'background: var(--bg-input); border-color: var(--border-2);';

            row.innerHTML = `
                ${oldValueId ? `<input type="hidden" name="options[${optionIdx}][old_value_ids][${valIdx}]" value="${oldValueId}">` : ''}

                <div class="flex-1 min-w-[140px]">
                    <input type="text"
                        name="options[${optionIdx}][values][${valIdx}]"
                        value="${escapeHtml(value || '')}"
                        required
                        class="option-value-input form-input"
                        placeholder="Contoh: Merah">
                </div>

                <div class="flex items-center gap-2 flex-shrink-0">
                    <label class="flex items-center justify-center w-9 h-9 rounded-lg cursor-pointer
                                bg-[#ecbc42]/10 border border-[#ecbc42]/30 text-[#FDDD57]
                                hover:bg-[#ecbc42]/20 hover:border-[#ecbc42]
                                transition-all active:scale-95"
                        title="${image ? 'Ganti gambar' : 'Upload gambar warna'}">
                        <iconify-icon icon="${image ? 'mdi:image-edit-outline' : 'mdi:image-plus'}" class="text-base"></iconify-icon>
                        <input type="file"
                            name="options[${optionIdx}][images][${valIdx}]"
                            accept="image/*"
                            class="option-image-input hidden">
                    </label>

                    <input type="hidden"
                        name="options[${optionIdx}][existing_images][${valIdx}]"
                        value="${image || ''}">

                    ${image
                        ? `<div class="w-9 h-9 rounded-lg overflow-hidden border" style="border-color: var(--border-2);">
                            <img src="${image}" class="w-full h-full object-cover" alt="Preview">
                        </div>`
                        : `<div class="w-9 h-9 rounded-lg flex items-center justify-center border"
                                style="background: var(--bg-card); border-color: var(--border-2);">
                            <iconify-icon icon="mdi:image-off-outline" class="text-base" style="color: var(--text-6);"></iconify-icon>
                        </div>`
                    }

                    <button type="button"
                            class="remove-option-value inline-flex items-center justify-center w-9 h-9 rounded-lg
                                bg-red-500/10 border border-red-500/20 text-red-400
                                hover:bg-red-500/20 hover:border-red-500/40
                                transition-all active:scale-95 flex-shrink-0"
                            title="Hapus warna">
                        <iconify-icon icon="mdi:close" class="text-base"></iconify-icon>
                    </button>
                </div>
            `;

            // Live preview saat upload gambar
            setTimeout(function() {
                var fileInput = row.querySelector('.option-image-input');
                if (fileInput) {
                    fileInput.addEventListener('change', function(e) {
                        var file = e.target.files[0];
                        if (!file) return;

                        var reader = new FileReader();
                        reader.onload = function(event) {
                            var label = fileInput.closest('label');
                            var icon = label.querySelector('iconify-icon');
                            if (icon) icon.setAttribute('icon', 'mdi:image-edit-outline');

                            var previewWrapper = label.nextElementSibling.nextElementSibling;
                            if (previewWrapper && previewWrapper.tagName === 'DIV') {
                                previewWrapper.outerHTML = `
                                    <div class="w-9 h-9 rounded-lg overflow-hidden border" style="border-color: #ecbc42;">
                                        <img src="${event.target.result}" class="w-full h-full object-cover" alt="Preview">
                                    </div>
                                `;
                            }
                        };
                        reader.readAsDataURL(file);
                    });
                }
            }, 0);
        }

        valuesContainer.appendChild(row);

        updateOptionValueCounter(optionElement);
        reindexOptions();
        triggerRegenerate();
    }

    function addColorOption(optionName, values, images, oldOptionId, valueIds) {
        var currentIndex = optionIndex++;
        var el = document.createElement('div');
        el.className = 'option-item option-item-color rounded-2xl border overflow-hidden mb-4';
        el.style.cssText = 'background: var(--bg-card); border-color: var(--border-2);';
        el.dataset.isColor = 'true';
        el.dataset.optionIndex = currentIndex;
        el.innerHTML = `
            {{-- Header --}}
            <div class="flex items-center justify-between gap-3 px-4 py-3 border-b"
                style="background: linear-gradient(90deg, rgba(236, 188, 66, 0.1) 0%, transparent 100%); border-color: var(--border-2)">

                <div class="flex items-center gap-3 min-w-0">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                                text-slate-900 flex-shrink-0">
                        <iconify-icon icon="mdi:palette-outline" class="text-base"></iconify-icon>
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-bold flex items-center gap-2" style="color: var(--text-1)">
                            Opsi Warna
                            <span class="text-[10px] font-semibold text-[#FDDD57]
                                        bg-[#ecbc42]/10 border border-[#ecbc42]/30
                                        px-2 py-0.5 rounded-full">
                                Bisa ada gambar
                            </span>
                        </p>
                        <p class="text-[10px]" style="color: var(--text-5)">Contoh: Merah, Biru, Hitam</p>
                    </div>
                </div>

                <button type="button"
                        class="remove-option inline-flex items-center gap-1.5 px-3 py-2 rounded-lg
                            text-xs font-semibold
                            bg-red-500/10 border border-red-500/20 text-red-400
                            hover:bg-red-500/20 hover:border-red-500/40
                            transition-all active:scale-95 flex-shrink-0">
                    <iconify-icon icon="mdi:trash-can-outline" class="text-sm"></iconify-icon>
                    Hapus Opsi
                </button>
            </div>

            {{-- Body --}}
            <div class="p-4 space-y-4">

                {{-- Nama Opsi --}}
                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:tag-outline" class="text-[#ecbc42]"></iconify-icon>
                        Nama Opsi
                    </label>
                    ${oldOptionId ? `<input type="hidden" name="options[${currentIndex}][old_id]" value="${oldOptionId}">` : ''}
                    <input type="text" name="options[${currentIndex}][name]"
                        value="${escapeHtml(optionName || 'Warna')}" required
                        class="option-name form-input"
                        placeholder="Contoh: Warna">
                </div>

                {{-- Nilai Opsi --}}
                <div>
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <label class="form-label" style="margin-bottom: 0;">
                            <iconify-icon icon="mdi:format-list-bulleted" class="text-[#ecbc42]"></iconify-icon>
                            Nilai Warna
                        </label>
                        <span class="text-[10px]" style="color: var(--text-5)">
                            <span class="value-counter">0</span> warna
                        </span>
                    </div>

                    <div class="option-values space-y-2"></div>

                    <button type="button"
                            class="add-option-value inline-flex items-center gap-1.5 mt-3 px-3 py-2 rounded-lg
                                text-xs font-semibold
                                transition-all active:scale-95"
                            style="background: var(--bg-input); border: 1px dashed var(--border-3); color: var(--text-3)"
                            onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'; this.style.borderStyle='solid'"
                            onmouseout="this.style.borderColor='var(--border-3)'; this.style.color='var(--text-3)'; this.style.borderStyle='dashed'">
                        <iconify-icon icon="mdi:plus-circle-outline"></iconify-icon>
                        Tambah Warna
                    </button>
                </div>
            </div>
        `;
        optionsContainer.appendChild(el);

        var vals = values || [];
        if (vals.length > 0) {
            vals.forEach(function(v, i) {
                var img = (images && images[i]) ? images[i] : '';
                var oldValId = (valueIds && valueIds[i]) ? valueIds[i] : null;
                addOptionValue(el, currentIndex, v, img, oldValId);
            });
        } else {
            addOptionValue(el, currentIndex, '', '', null);
        }

        updateOptionsEmptyState();
        captureVariantData();
        triggerRegenerate();
    }


    function addSizeOption(optionName, values, oldOptionId, valueIds) {
        var currentIndex = optionIndex++;
        var el = document.createElement('div');
        el.className = 'option-item option-item-size rounded-2xl border overflow-hidden mb-4';
        el.style.cssText = 'background: var(--bg-card); border-color: var(--border-2);';
        el.dataset.isSize = 'true';
        el.dataset.optionIndex = currentIndex;
        el.innerHTML = `
            {{-- Header --}}
            <div class="flex items-center justify-between gap-3 px-4 py-3 border-b"
                style="background: linear-gradient(90deg, rgba(236, 188, 66, 0.1) 0%, transparent 100%); border-color: var(--border-2)">

                <div class="flex items-center gap-3 min-w-0">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                                text-slate-900 flex-shrink-0">
                        <iconify-icon icon="mdi:ruler-square" class="text-base"></iconify-icon>
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-bold flex items-center gap-2" style="color: var(--text-1)">
                            Opsi Ukuran
                            <span class="text-[10px] font-semibold text-[#FDDD57]
                                        bg-[#ecbc42]/10 border border-[#ecbc42]/30
                                        px-2 py-0.5 rounded-full">
                                Tanpa gambar
                            </span>
                        </p>
                        <p class="text-[10px]" style="color: var(--text-5)">Contoh: S, M, L, XL</p>
                    </div>
                </div>

                <button type="button"
                        class="remove-option inline-flex items-center gap-1.5 px-3 py-2 rounded-lg
                            text-xs font-semibold
                            bg-red-500/10 border border-red-500/20 text-red-400
                            hover:bg-red-500/20 hover:border-red-500/40
                            transition-all active:scale-95 flex-shrink-0">
                    <iconify-icon icon="mdi:trash-can-outline" class="text-sm"></iconify-icon>
                    Hapus Opsi
                </button>
            </div>

            {{-- Body --}}
            <div class="p-4 space-y-4">

                {{-- Nama Opsi --}}
                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:tag-outline" class="text-[#ecbc42]"></iconify-icon>
                        Nama Opsi
                    </label>
                    ${oldOptionId ? `<input type="hidden" name="options[${currentIndex}][old_id]" value="${oldOptionId}">` : ''}
                    <input type="text" name="options[${currentIndex}][name]"
                        value="${escapeHtml(optionName || 'Ukuran')}" required
                        class="option-name form-input"
                        placeholder="Contoh: Ukuran">
                </div>

                {{-- Nilai Opsi (Grid Horizontal) --}}
                <div>
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <label class="form-label" style="margin-bottom: 0;">
                            <iconify-icon icon="mdi:format-list-bulleted" class="text-[#ecbc42]"></iconify-icon>
                            Nilai Ukuran
                        </label>
                        <span class="text-[10px]" style="color: var(--text-5)">
                            <span class="value-counter">0</span> ukuran
                        </span>
                    </div>

                    {{-- 🔥 GRID layout horizontal --}}
                    <div class="option-values grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2"></div>

                    <button type="button"
                            class="add-option-value inline-flex items-center gap-1.5 mt-3 px-3 py-2 rounded-lg
                                text-xs font-semibold
                                transition-all active:scale-95"
                            style="background: var(--bg-input); border: 1px dashed var(--border-3); color: var(--text-3)"
                            onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'; this.style.borderStyle='solid'"
                            onmouseout="this.style.borderColor='var(--border-3)'; this.style.color='var(--text-3)'; this.style.borderStyle='dashed'">
                        <iconify-icon icon="mdi:plus-circle-outline"></iconify-icon>
                        Tambah Ukuran
                    </button>
                </div>
            </div>
        `;
        optionsContainer.appendChild(el);

        var vals = (values && values.length > 0) ? values : DEFAULT_SIZES;
        vals.forEach(function(v, i) {
            var oldValId = (valueIds && valueIds[i]) ? valueIds[i] : null;
            addOptionValue(el, currentIndex, v, '', oldValId);
        });

        updateOptionsEmptyState();
        captureVariantData();
        triggerRegenerate();
    }

    if (optionsContainer) {
        optionsContainer.addEventListener('click', function(e) {
            var target = e.target;
            
            if (target.classList.contains('remove-option')) {
                var el = target.closest('.option-item');
                if (el && confirm('Hapus opsi ini?')) {
                    el.remove();
                    reindexOptions();
                    updateOptionsEmptyState();
                    captureVariantData();
                    triggerRegenerate();
                }
                return;
            }
            
            if (target.classList.contains('add-option-value')) {
                var el = target.closest('.option-item');
                var optIdx = Array.from(optionsContainer.querySelectorAll('.option-item')).indexOf(el);
                addOptionValue(el, optIdx, '', '', null);
                return;
            }
            
            if (target.classList.contains('remove-option-value')) {
                var row = target.closest('.option-value-row');
                if (row) {
                    var container = row.closest('.option-values');
                    if (container.querySelectorAll('.option-value-row').length <= 1) {
                        if (!confirm('Ini nilai terakhir. Hapus?')) return;
                    }
                    row.remove();
                    reindexOptions();
                    captureVariantData();
                    triggerRegenerate();
                }
                return;
            }
        });

        optionsContainer.addEventListener('input', function(e) {
            var target = e.target;
            if (target.classList.contains('option-value-input') || target.classList.contains('option-name')) {
                triggerRegenerate();
            }
        });
    }

    if (addColorOptionButton) {
        addColorOptionButton.addEventListener('click', function() {
            captureVariantData();
            addColorOption();
        });
    }

    if (addSizeOptionButton) {
        addSizeOptionButton.addEventListener('click', function() {
            captureVariantData();
            addSizeOption();
        });
    }

    if (generateVariantsButton) {
        generateVariantsButton.addEventListener('click', function() {
            var options = getOptions();
            if (options.length === 0) {
                showToast('⚠️ Tambahkan minimal satu opsi produk', 'warning');
                return;
            }
            captureVariantData();
            var combinations = generateCombinations(options);
            renderVariants(combinations);
            showToast('✅ ' + combinations.length + ' varian berhasil digenerate', 'success');
        });
    }

    // ============================================
    // LOAD DATA EDIT
    // ============================================
    if (existingOptions && existingOptions.length > 0) {
        optionIndex = 0;

        existingOptions.forEach(function(option) {
            var values = [];
            var images = [];
            var valueIds = [];

            if (option.values && Array.isArray(option.values)) {
                option.values.forEach(function(v) {
                    if (v && v.value) {
                        values.push(v.value);
                        images.push(v.image || '');
                        valueIds.push(v.id || null);
                    }
                });
            }

            if (values.length === 0) {
                console.warn('⚠️ Option "' + (option.name || '') + '" tidak punya nilai, skip.');
                return;
            }

            var name = (option.name || '').trim();
            var nameLower = name.toLowerCase();

            // 🔥 Deteksi tipe: ukuran vs warna (lebih robust)
            var isSizeOption =
                nameLower === 'ukuran' ||
                nameLower === 'size' ||
                nameLower === 'sizes' ||
                nameLower.includes('ukuran') ||
                nameLower.includes('size');

            if (isSizeOption) {
                addSizeOption(name, values, option.id, valueIds);
            } else {
                // Default ke warna (untuk apapun yang bukan ukuran)
                addColorOption(name, values, images, option.id, valueIds);
            }
        });

        // 🔥 LOAD VARIANTS SETELAH OPSI SELESAI RENDER
        setTimeout(function() {
            if (existingVariants && existingVariants.length > 0) {
                savedVariantData = existingVariants.map(function(v) {
                    // 🔥 PAKAI value_names yang sudah di-sort dari controller (paling reliable)
                    var namesArray = v.value_names || v.option_value_names || [];
                    var sortedNames = namesArray.slice().sort();

                    return {
                        id: v.id,
                        sku: v.sku || '',
                        price: v.price || '',
                        discount_percent: v.discount_percent || '0',
                        discount_price: v.discount_price || '',
                        stock: v.stock || '0',
                        weight: v.weight || '1000',
                        value_names: sortedNames.join('|'),
                        combination_text: namesArray.join(' · '),
                        option_value_ids: v.option_value_ids || [],
                    };
                });

                console.log('✅ savedVariantData loaded:', savedVariantData);
            }

            // 🔥 TRIGGER REGENERATE DENGAN DELAY
            var options = getOptions();
            if (options.length > 0) {
                var combinations = generateCombinations(options);
                renderVariants(combinations);
            }
        }, 600); // naikkan delay dari 300 ke 600
    }


    // ============================================
    // SUBMIT VALIDATION
    // ============================================
    var productForm = document.getElementById('product-form');
    if (productForm) {
        productForm.addEventListener('submit', function(e) {
            captureVariantData();
            
            var skuInputs = document.querySelectorAll('.sku-input');
            var hasEmptySku = false;
            skuInputs.forEach(function(input) {
                if (input.value.trim() === '') {
                    hasEmptySku = true;
                    input.style.borderColor = 'red';
                    input.style.backgroundColor = '#fee2e2';
                }
            });
            
            if (hasEmptySku) {
                e.preventDefault();
                showToast('⚠️ Semua SKU harus diisi', 'error');
                return false;
            }
            
            var skus = [];
            var duplicateSkus = [];
            skuInputs.forEach(function(input) {
                var sku = input.value.trim();
                if (sku !== '' && skus.includes(sku)) {
                    duplicateSkus.push(sku);
                }
                skus.push(sku);
            });
            
            if (duplicateSkus.length > 0) {
                e.preventDefault();
                showToast('❌ SKU duplikat: ' + [...new Set(duplicateSkus)].join(', '), 'error');
                return false;
            }
            
            if (document.querySelectorAll('.variant-item').length === 0) {
                e.preventDefault();
                showToast('⚠️ Minimal harus ada 1 varian', 'warning');
                return false;
            }
        });
    }

    // ============================================
    // PERHITUNGAN DISKON
    // ============================================
    window.calculateDiscount = function(element, index) {
        var row = element.closest('.variant-item');
        if (!row) return;
        var priceInput = row.querySelector('.price-input');
        var percentInput = row.querySelector('.discount-percent-input');
        var discountInput = row.querySelector('.discount-price-input');
        if (!priceInput || !percentInput || !discountInput) return;
        
        var price = parseFloat(priceInput.value) || 0;
        var percent = parseFloat(percentInput.value) || 0;
        
        if (percent > 0 && price > 0) {
            var discount = price - (price * (percent / 100));
            discountInput.value = Math.round(discount * 100) / 100;
            discountInput.style.backgroundColor = '#ecfdf5';
            discountInput.style.color = '#065f46';
        } else {
            discountInput.value = '';
            discountInput.style.backgroundColor = '#f3f4f6';
            discountInput.style.color = '#6b7280';
        }

        syncDiscountStates();
    };

    window.recalculateAllDiscounts = function() {
        document.querySelectorAll('.variant-item').forEach(function(row) {
            var priceInput = row.querySelector('.price-input');
            var discountInput = row.querySelector('.discount-price-input');
            var percentInput = row.querySelector('.discount-percent-input');
            if (!priceInput || !discountInput) return;
            
            var price = parseFloat(priceInput.value) || 0;
            var discount = parseFloat(discountInput.value) || 0;
            
            if (price > 0 && discount > 0 && discount < price) {
                var percent = Math.round(((price - discount) / price) * 100);
                if (percentInput) percentInput.value = percent;
                discountInput.style.backgroundColor = '#ecfdf5';
                discountInput.style.color = '#065f46';
            } else if (discount === 0 || discount === '') {
                if (percentInput) percentInput.value = 0;
                discountInput.style.backgroundColor = '#f3f4f6';
                discountInput.style.color = '#6b7280';
            }
        });
        syncDiscountStates();
    };

    window.clearAllVariants = function() {
        savedVariantData = [];
        if (variantsContainer) {
            variantsContainer.innerHTML = '';
        }
        updateVariantsEmptyState();
        showToast('Semua varian telah dihasilkan ulang.', 'warning');
    };

    updateOptionsEmptyState();
    updateVariantsEmptyState();
    setTimeout(recalculateAllDiscounts, 400);
    setTimeout(syncDiscountStates, 500);
});
</script>