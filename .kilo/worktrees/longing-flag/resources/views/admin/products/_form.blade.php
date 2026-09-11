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

    // 🔥 HELPER UNTUK CHECKBOX
    function isChecked($field, $default = false) {
        $productObj = isset($GLOBALS['product']) ? $GLOBALS['product'] : (isset($GLOBALS['__env']) ? null : null);
        
        // Cek langsung variabel $product dari scope view jika tersedia
        global $product;
        $targetProduct = isset($product) ? $product : null;
        
        $value = old($field, $targetProduct ? $targetProduct->$field : $default);
        return $value == true || $value == 1 || $value === '1' || $value === 'on';
    }
@endphp

<style>
    /* resources/css/admin.css */

    /* Deskripsi editor container */
    .description-editor {
        min-height: 300px;
    }

    /* Toast notification untuk editor */
    .tox-tinymce {
        border-radius: 0.5rem !important;
        border-color: #d1d5db !important;
    }

    .tox-tinymce:focus-within {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
    }

    /* Dark mode support */
    .dark .tox-tinymce {
        background: #1f2937 !important;
    }

    .dark .tox-toolbar__group {
        background: #1f2937 !important;
    }

    .dark .tox-menubar {
        background: #1f2937 !important;
    }

    .feature-item {
        transition: all 0.2s ease;
    }

    .feature-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
</style>

@csrf

@if ($errors->any())
    <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        <p class="font-semibold">Produk belum tersimpan:</p>
        <ul class="mt-2 list-disc space-y-1 pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2 w-full max-w-sm pointer-events-none"></div>

<style>
    .toast {
        transform: translateX(calc(100% + 2rem));
        transition: transform 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        pointer-events: auto;
    }
    .toast.show {
        transform: translateX(0);
    }
    .toast.hide {
        transform: translateX(calc(100% + 2rem));
    }
</style>

<div class="space-y-8">

    {{-- Informasi Produk --}}
    <div>
        <h2 class="text-lg font-semibold text-gray-900">Informasi Produk</h2>
        <p class="mt-1 text-sm text-gray-500">Masukkan informasi dasar produk.</p>
    </div>

    {{-- Nama Produk --}}
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Nama Produk</label>
        <input type="text" name="name" id="name" value="{{ old('name', $product->name ?? '') }}" required
            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            placeholder="Contoh: Jaket Sport">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Kategori --}}
    <div>
        <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori</label>
        <select name="category_id" id="category_id" required
            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">-- Pilih Kategori --</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? '') == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Deskripsi --}}
    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi Produk</label>
        
        {{-- Hidden input untuk menyimpan nilai Quill --}}
        <textarea 
            name="description" 
            id="description" 
            style="display: none;"
        >{{ old('description', $product->description ?? '') }}</textarea>
        
        {{-- Quill Editor Container --}}
        <div id="quill-editor" class="mt-2" style="min-height: 300px; max-height: 500px; overflow-y: auto;">
            {!! old('description', $product->description ?? '') !!}
        </div>
        
        @error('description') 
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
        @enderror
    </div>

    {{-- GENDER --}}
    <div>
        <label for="gender" class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
        <select name="gender" id="gender"
            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">-- Pilih Jenis Kelamin --</option>
            <option value="pria" {{ old('gender', $product->gender ?? '') == 'pria' ? 'selected' : '' }}>Pria</option>
            <option value="wanita" {{ old('gender', $product->gender ?? '') == 'wanita' ? 'selected' : '' }}>Wanita</option>
            <option value="unisex" {{ old('gender', $product->gender ?? '') == 'unisex' ? 'selected' : '' }}>Unisex</option>
        </select>
        @error('gender') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- BAHAN --}}
    <div>
        <label for="material" class="block text-sm font-medium text-gray-700">Bahan</label>
        <input type="text" name="material" id="material" 
            value="{{ old('material', $product->material ?? '') }}"
            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            placeholder="Contoh: Poliester, Diadora, Katun">
        @error('material') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        {{-- Stok Minimum (Critical) --}}
        <div>
            <label for="minimum_stock" class="block text-sm font-medium text-gray-700">
                Stok Minimum (Kritis)
            </label>
            <div class="mt-2 flex items-center gap-2">
                <input type="number" 
                    name="minimum_stock" 
                    id="minimum_stock"
                    value="{{ old('minimum_stock', $product->minimum_stock ?? 5) }}"
                    min="0"
                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                <span class="text-xs text-gray-500 whitespace-nowrap">unit</span>
            </div>
            <p class="mt-1 text-xs text-red-500">
                🚨 Jika stok mencapai angka ini, produk akan diberi label <strong>"Kritis"</strong>
            </p>
        </div>

        {{-- Restock Threshold (Menipis) --}}
        <div>
            <label for="restock_threshold" class="block text-sm font-medium text-gray-700">
                Ambang Restock (Menipis)
            </label>
            <div class="mt-2 flex items-center gap-2">
                <input type="number" 
                    name="restock_threshold" 
                    id="restock_threshold"
                    value="{{ old('restock_threshold', $product->restock_threshold ?? 10) }}"
                    min="0"
                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                <span class="text-xs text-gray-500 whitespace-nowrap">unit</span>
            </div>
            <p class="mt-1 text-xs text-yellow-500">
                ⚠️ Jika stok di bawah angka ini (tapi di atas minimum), produk diberi label <strong>"Menipis"</strong>
            </p>
        </div>
    </div>

    {{-- Gambar Produk --}}
    <div>
        <h2 class="text-lg font-semibold text-gray-900">Gambar Produk</h2>
        <p class="mt-1 text-sm text-gray-500">Kamu dapat mengunggah beberapa gambar sekaligus.</p>

        @if ($isEdit && isset($product) && $product->images->isNotEmpty())
            <div class="mt-5">
                <p class="mb-3 text-sm font-medium text-gray-700">Gambar Saat Ini</p>
                <div id="existing-images-container" class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                    @foreach ($product->images as $image)
                        <div class="existing-image relative overflow-hidden rounded-xl border border-gray-200 bg-gray-100"
                            data-image-id="{{ $image->id }}">
                            <img src="{{ Storage::url($image->image) }}" alt="{{ $product->name }}"
                                class="aspect-square w-full object-cover">
                            <input type="hidden" name="existing_images[]" value="{{ $image->id }}">
                            <button type="button"
                                class="remove-existing-image absolute right-2 top-2 flex h-8 w-8 items-center justify-center rounded-full bg-white/90 text-lg font-bold text-red-600 shadow transition hover:bg-red-50">
                                ×
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mt-5">
            <label for="images" class="block text-sm font-medium text-gray-700">
                {{ $isEdit ? 'Tambah Gambar Baru' : 'Upload Gambar' }}
            </label>
            <input type="file" name="images[]" id="images" multiple accept="image/jpeg,image/png,image/webp"
                class="mt-2 block w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100">
        </div>
        @error('images') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        @error('images.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="border-t pt-6 mt-6">
        <h2 class="text-lg font-semibold text-gray-900">Diskon Produk</h2>
        <p class="mt-1 text-sm text-gray-500">Berlaku untuk SEMUA varian produk ini.</p>

        <div class="mt-4 rounded-lg border border-gray-200 bg-gray-50 p-4">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" 
                    name="has_product_discount" 
                    value="1"
                    id="has_product_discount"
                    {{ old('has_product_discount', isset($product) ? $product->has_product_discount : false) ? 'checked' : '' }}
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="text-sm font-medium text-gray-700">Aktifkan Diskon Produk</span>
            </label>

            <div id="product_discount_fields" class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 {{ (old('has_product_discount', isset($product) ? $product->has_product_discount : false)) ? '' : 'hidden' }}">
                {{-- Tipe Diskon --}}
                <div>
                    <label for="discount_type" class="block text-sm font-medium text-gray-700">Tipe Diskon</label>
                    <select name="discount_type" id="discount_type"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="percentage" {{ old('discount_type', isset($product) ? $product->discount_type : '') == 'percentage' ? 'selected' : '' }}>
                            Persentase (%)
                        </option>
                        <option value="fixed" {{ old('discount_type', isset($product) ? $product->discount_type : '') == 'fixed' ? 'selected' : '' }}>
                            Potongan Harga (Fixed)
                        </option>
                    </select>
                </div>

                {{-- Nilai Diskon --}}
                <div>
                    <label for="discount_value" class="block text-sm font-medium text-gray-700">
                        Nilai Diskon
                        <span id="discount_value_label" class="text-xs text-gray-400">
                            ({{ old('discount_type', isset($product) ? $product->discount_type : 'percentage') == 'percentage' ? '%' : 'Rp' }})
                        </span>
                    </label>
                    <input type="number" 
                        name="discount_value" 
                        id="discount_value"
                        value="{{ old('discount_value', isset($product) ? $product->discount_value : '') }}"
                        min="0"
                        step="{{ old('discount_type', isset($product) ? $product->discount_type : 'percentage') == 'percentage' ? '0.01' : '1000' }}"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="{{ old('discount_type', isset($product) ? $product->discount_type : 'percentage') == 'percentage' ? 'Contoh: 10' : 'Contoh: 50000' }}">
                    <p class="mt-1 text-xs text-gray-500">
                        {{ old('discount_type', isset($product) ? $product->discount_type : 'percentage') == 'percentage' ? 'Masukkan persentase diskon (contoh: 10 untuk 10%)' : 'Masukkan nominal potongan harga' }}
                    </p>
                </div>
            </div>

            {{-- Preview Diskon --}}
            @if(isset($product) && $product->has_product_discount && $product->discount_value)
                <div class="mt-4 rounded-lg border border-blue-200 bg-blue-50 p-3">
                    <p class="text-sm text-blue-700">
                        <span class="font-semibold">Status Diskon:</span>
                        <span class="{{ $product->product_discount_status == 'active' ? 'text-green-600' : 'text-gray-600' }}">
                            {{ $product->product_discount_status_label }}
                        </span>
                    </p>
                    @if($product->discount_type == 'percentage')
                        <p class="text-sm text-blue-700">Diskon: {{ $product->discount_value }}%</p>
                    @else
                        <p class="text-sm text-blue-700">Potongan: Rp {{ number_format($product->discount_value, 0, ',', '.') }}</p>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- 🔥 FLASH SALE SECTION --}}
    {{-- ============================================ --}}

    <div class="border-t pt-6 mt-6">
        <h2 class="text-lg font-semibold text-gray-900">⚡ Flash Sale</h2>
        <p class="mt-1 text-sm text-gray-500">Diskon waktu terbatas dengan periode tertentu.</p>

        <div class="mt-4 rounded-lg border border-orange-200 bg-orange-50 p-4">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="hidden" name="is_flash_sale" value="0">
                <input type="checkbox" 
                    name="is_flash_sale" 
                    value="1"
                    id="is_flash_sale"
                    {{ $flashSaleChecked ? 'checked' : '' }}
                    class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                <span class="text-sm font-medium text-gray-700">Aktifkan Flash Sale</span>
                <span class="text-xs text-orange-600 bg-orange-100 px-2 py-0.5 rounded-full">⏰ Waktu Terbatas</span>
            </label>

            <div id="flash_sale_fields" class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 {{ (old('is_flash_sale', isset($product) ? $product->is_flash_sale : false)) ? '' : 'hidden' }}">
                {{-- Tipe Diskon Flash Sale --}}
                <div>
                    <label for="flash_sale_type" class="block text-sm font-medium text-gray-700">Tipe Diskon</label>
                    <select name="flash_sale_type" id="flash_sale_type"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                        <option value="percentage" {{ old('flash_sale_type', isset($product) ? $product->flash_sale_type : '') == 'percentage' ? 'selected' : '' }}>
                            Persentase (%)
                        </option>
                        <option value="fixed" {{ old('flash_sale_type', isset($product) ? $product->flash_sale_type : '') == 'fixed' ? 'selected' : '' }}>
                            Potongan Harga (Fixed)
                        </option>
                    </select>
                </div>

                {{-- Nilai Diskon Flash Sale --}}
                <div>
                    <label for="flash_sale_value" class="block text-sm font-medium text-gray-700">
                        Nilai Diskon
                        <span id="flash_sale_value_label" class="text-xs text-gray-400">
                            ({{ old('flash_sale_type', isset($product) ? $product->flash_sale_type : 'percentage') == 'percentage' ? '%' : 'Rp' }})
                        </span>
                    </label>
                    <input type="number" 
                        name="flash_sale_value" 
                        id="flash_sale_value"
                        value="{{ old('flash_sale_value', isset($product) ? $product->flash_sale_value : '') }}"
                        min="0"
                        step="{{ old('flash_sale_type', isset($product) ? $product->flash_sale_type : 'percentage') == 'percentage' ? '0.01' : '1000' }}"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                        placeholder="{{ old('flash_sale_type', isset($product) ? $product->flash_sale_type : 'percentage') == 'percentage' ? 'Contoh: 20' : 'Contoh: 50000' }}">
                    <p class="mt-1 text-xs text-gray-500">
                        {{ old('flash_sale_type', isset($product) ? $product->flash_sale_type : 'percentage') == 'percentage' ? 'Masukkan persentase diskon (contoh: 20 untuk 20%)' : 'Masukkan nominal potongan harga' }}
                    </p>
                </div>

                {{-- Flash Sale Start Date --}}
                <div>
                    <label for="flash_sale_start_date" class="block text-sm font-medium text-gray-700">
                        Tanggal Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" 
                        name="flash_sale_start_date" 
                        id="flash_sale_start_date"
                        value="{{ $flashSaleStartValue }}"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                    @error('flash_sale_start_date') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                    @enderror
                </div>

                {{-- Flash Sale End Date --}}
                <div>
                    <label for="flash_sale_end_date" class="block text-sm font-medium text-gray-700">
                        Tanggal Berakhir <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" 
                        name="flash_sale_end_date" 
                        id="flash_sale_end_date"
                        value="{{ $flashSaleEndValue }}"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                    @error('flash_sale_end_date') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                    @enderror
                </div>
            </div>

            {{-- Preview Flash Sale --}}
            @if($hasSavedActiveFlashSale && $product->flash_sale_value)
                <div class="mt-4 rounded-lg border border-orange-200 bg-orange-100 p-3">
                    <p class="text-sm text-orange-800">
                        <span class="font-semibold">Status Flash Sale:</span>
                        <span class="{{ 
                            $product->flash_sale_status == 'active' ? 'text-green-600' : 
                            ($product->flash_sale_status == 'upcoming' ? 'text-yellow-600' : 
                            ($product->flash_sale_status == 'expired' ? 'text-red-600' : 'text-gray-600')) 
                        }}">
                            {{ $product->flash_sale_status_label }}
                        </span>
                    </p>
                    @if($product->flash_sale_type == 'percentage')
                        <p class="text-sm text-orange-800">Diskon: {{ $product->flash_sale_value }}%</p>
                    @else
                        <p class="text-sm text-orange-800">Potongan: Rp {{ number_format($product->flash_sale_value, 0, ',', '.') }}</p>
                    @endif
                    @if($product->flash_sale_start_date)
                        <p class="text-sm text-orange-800">Mulai: {{ $product->flash_sale_start_date->format('d/m/Y H:i') }}</p>
                    @endif
                    @if($product->flash_sale_end_date)
                        <p class="text-sm text-orange-800">Berakhir: {{ $product->flash_sale_end_date->format('d/m/Y H:i') }}</p>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- 🔥 OPSI PRODUK (WARNA & UKURAN) --}}
    {{-- ============================================ --}}

    <div>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Opsi Produk</h2>
                <p class="mt-1 text-sm text-gray-500">Tambahkan opsi seperti Warna atau Ukuran.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" id="add-color-option"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    🎨 + Tambah Warna
                </button>
                <button type="button" id="add-size-option"
                    class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                    📏 + Tambah Ukuran
                </button>
            </div>
        </div>

        <div id="options-container" class="mt-5 space-y-4"></div>

        <div id="options-empty" class="mt-4 rounded-lg border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500">
            Belum ada opsi produk. Tambahkan opsi Warna atau Ukuran.
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- 🔥 VARIAN PRODUK (KOMBINASI WARNA + UKURAN) --}}
    {{-- ============================================ --}}

    <div>
        <div id="variants-container" class="mt-5 space-y-4"></div>
        <div id="variants-empty" class="mt-4 rounded-lg border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500">
            Belum ada varian. Tambahkan opsi lalu klik <strong>Generate Varian</strong>.
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- 🔥 FITUR PRODUK - INLINE LIST --}}
    {{-- ============================================ --}}

    <div id="features-container">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Fitur Produk</h2>
                <p class="mt-1 text-sm text-gray-500">Pilih atau tambahkan fitur yang tersedia untuk produk ini.</p>
            </div>
        </div>

        {{-- Form Tambah Fitur Inline --}}
        <div class="mt-4 rounded-lg border border-gray-200 bg-gray-50 p-4">
            <div class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[200px]">
                    <label for="new-feature-name" class="block text-sm font-medium text-gray-700">Nama Fitur Baru</label>
                    <input type="text" id="new-feature-name" 
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Contoh: Anti Air, Ringan, Berkualitas">
                </div>
                <button type="button" id="add-feature-btn"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 whitespace-nowrap">
                    + Tambah Fitur
                </button>
            </div>
            <div id="feature-feedback" class="mt-2 text-sm hidden"></div>
        </div>

        {{-- Daftar Fitur yang Tersedia --}}
        <div class="mt-4">
            <p class="text-sm font-medium text-gray-700 mb-3">Fitur Tersedia:</p>
            <div id="features-list" class="flex flex-wrap gap-2">
                @php
                    $selectedFeatures = $isEdit && isset($product) ? $product->features->pluck('id')->toArray() : [];
                @endphp
                @foreach ($features as $feature)
                    <label class="feature-item flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 hover:bg-gray-50 cursor-pointer transition shadow-sm">
                        <input type="checkbox" 
                            name="features[]" 
                            value="{{ $feature->id }}"
                            {{ in_array($feature->id, old('features', $selectedFeatures)) ? 'checked' : '' }}
                            class="feature-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">{{ $feature->name }}</span>
                    </label>
                @endforeach
            </div>
            @if($features->isEmpty())
                <p id="no-features-message" class="text-sm text-gray-500">Belum ada fitur. Tambahkan fitur baru di atas.</p>
            @endif
        </div>
    </div>

    {{-- Status --}}
    <div class="border-t pt-6">
        <div class="flex flex-wrap items-center gap-8">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="hidden" name="is_featured" value="0">
                <input type="checkbox" name="is_featured" value="1"
                    {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="text-sm text-gray-700">⭐ Produk Unggulan</span>
            </label>

            <label class="flex items-center gap-2 cursor-pointer">
                <input type="hidden" name="is_best_seller" value="0">
                <input type="checkbox" name="is_best_seller" value="1"
                    {{ old('is_best_seller', $product->is_best_seller ?? false) ? 'checked' : '' }}
                    class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                <span class="text-sm text-gray-700">🔥 Produk Laris Bulan Ini</span>
            </label>

            <label class="flex items-center gap-2 cursor-pointer">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1"
                    {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="text-sm text-gray-700">✅ Produk Aktif</span>
            </label>
        </div>
    </div>

    {{-- Submit --}}
    <div class="flex justify-end gap-3 border-t pt-6">
        <a href="{{ route('admin.products.index') }}"
            class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
            Batal
        </a>
        <button type="submit" id="submit-btn"
            class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
            {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Produk' }}
        </button>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quill already initialized in layout
    // Additional custom logic if needed
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
            variantsContainer.innerHTML = '<div class="p-6 text-center text-sm text-gray-500">Tidak ada kombinasi varian yang valid.</div>';
            updateVariantsEmptyState();
            return;
        }

        var existingMap = {};
        savedVariantData.forEach(function(v) {
            if (v.value_names) existingMap[v.value_names] = v;
            if (v.combination_text) existingMap[v.combination_text] = v;
        });

        validCombinations.forEach(function(combination, index) {
            var comboText = combination.map(function(item) { return item.value; }).join(' - ');
            var valueNames = combination.map(function(item) { return item.value.trim(); }).sort().join('|');
            var existingData = existingMap[valueNames] || existingMap[comboText] || null;

            var variant = document.createElement('div');
            variant.className = 'variant-item rounded-xl border border-gray-200 bg-white p-5 shadow-sm mb-4';

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
                '<span class="text-xs text-blue-600 font-medium">🆕 Varian baru</span>' : 
                '<span class="text-xs text-green-600 font-medium">✅ Sudah ada</span>';

            var valueNamesHtml = combination.map(function(item) {
                return `<span class="variant-value-name hidden">${escapeHtml(item.value)}</span>`;
            }).join('');

            variant.innerHTML = `
                ${optionValueIndexesHtml}
                ${existingIdHtml}
                ${valueNamesHtml}

                <div class="mb-4 flex items-center justify-between">
                    <p class="text-sm font-semibold text-gray-900 variant-item-combination">${escapeHtml(comboText)}</p>
                    ${statusBadge}
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-600">SKU</label>
                        <input type="text" name="variants[${index}][sku]" value="${escapeHtml(sku)}" required
                            class="sku-input mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Masukkan SKU">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600">Harga</label>
                        <input type="number" name="variants[${index}][price]" value="${escapeHtml(price)}" min="0" required
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 price-input"
                            data-variant-index="${index}"
                            oninput="calculateDiscount(this, ${index})">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600">Diskon (%)</label>
                        <input type="number" name="variants[${index}][discount_percent]" value="${escapeHtml(discountPercent)}" min="0" max="100"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 discount-percent-input"
                            data-variant-index="${index}"
                            oninput="calculateDiscount(this, ${index})"
                            placeholder="0">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600">Harga Diskon</label>
                        <input type="number" name="variants[${index}][discount_price]" value="${escapeHtml(discountPrice)}" min="0" step="0.01"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 discount-price-input"
                            data-variant-index="${index}"
                            readonly style="background-color: #f3f4f6; cursor: not-allowed;">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600">Stok</label>
                        <input type="number" name="variants[${index}][stock]" value="${stock}" min="0" required
                            class="stock-input mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600">Berat (gram)</label>
                        <input type="number" name="variants[${index}][weight]" value="${escapeHtml(weight)}" min="0" required
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
            `;

            variantsContainer.appendChild(variant);
        });

        updateVariantsEmptyState();
        syncDiscountStates(); // 🔥 Call sync after rendering variants
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
        row.className = 'option-value-row flex flex-wrap items-center gap-2 mb-2';
        row.innerHTML = `
            <div class="flex-1 min-w-[120px]">
                ${oldValueId ? `<input type="hidden" name="options[${optionIdx}][old_value_ids][${valIdx}]" value="${oldValueId}">` : ''}
                <input type="text" name="options[${optionIdx}][values][${valIdx}]" value="${escapeHtml(value || '')}" required
                    class="option-value-input block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    placeholder="${isSize ? 'Contoh: XL' : 'Contoh: Merah'}">
            </div>
            ${isSize ? '<span class="text-xs text-gray-400">(tanpa gambar)</span>' : `
                <div class="flex-1 min-w-[100px]">
                    <input type="file" name="options[${optionIdx}][images][${valIdx}]" accept="image/*"
                        class="option-image-input block w-full text-xs text-gray-500">
                    <input type="hidden" name="options[${optionIdx}][existing_images][${valIdx}]" value="${image || ''}">
                </div>
                ${image ? `<img src="${image}" class="h-9 w-9 object-cover rounded-lg border">` : ''}
            `}
            <button type="button" class="remove-option-value rounded-lg bg-red-50 px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-100">Hapus</button>
        `;
        valuesContainer.appendChild(row);
        
        reindexOptions();
        triggerRegenerate();
    }

    function addColorOption(optionName, values, images, oldOptionId, valueIds) {
        var currentIndex = optionIndex++;
        var el = document.createElement('div');
        el.className = 'option-item rounded-xl border border-blue-200 bg-blue-50 p-5 mb-4';
        el.dataset.isColor = 'true';
        el.dataset.optionIndex = currentIndex;
        el.innerHTML = `
            <div class="flex items-start gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700">Nama Opsi</label>
                    ${oldOptionId ? `<input type="hidden" name="options[${currentIndex}][old_id]" value="${oldOptionId}">` : ''}
                    <input type="text" name="options[${currentIndex}][name]" value="${escapeHtml(optionName || 'Warna')}" required
                        class="option-name mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Contoh: Warna">
                    <p class="mt-1 text-xs text-blue-600">✅ Opsi warna dapat memiliki gambar</p>
                </div>
                <button type="button" class="remove-option mt-7 rounded-lg bg-red-50 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-100">Hapus</button>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700">Nilai Opsi (Warna)</label>
                <div class="option-values mt-2 space-y-2"></div>
                <button type="button" class="add-option-value mt-3 text-sm font-medium text-blue-600 hover:text-blue-700">+ Tambah Warna</button>
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
        el.className = 'option-item rounded-xl border border-green-200 bg-green-50 p-5 mb-4';
        el.dataset.isSize = 'true';
        el.dataset.optionIndex = currentIndex;
        el.innerHTML = `
            <div class="flex items-start gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700">Nama Opsi</label>
                    ${oldOptionId ? `<input type="hidden" name="options[${currentIndex}][old_id]" value="${oldOptionId}">` : ''}
                    <input type="text" name="options[${currentIndex}][name]" value="${escapeHtml(optionName || 'Ukuran')}" required
                        class="option-name mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                        placeholder="Contoh: Ukuran">
                    <p class="mt-1 text-xs text-green-600">✅ Opsi ukuran tidak memerlukan gambar</p>
                </div>
                <button type="button" class="remove-option mt-7 rounded-lg bg-red-50 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-100">Hapus</button>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700">Nilai Opsi (Ukuran)</label>
                <div class="option-values mt-2 space-y-2"></div>
                <button type="button" class="add-option-value mt-3 text-sm font-medium text-green-600 hover:text-green-700">+ Tambah Ukuran</button>
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
    if (existingOptions.length > 0) {
        optionIndex = 0;
        existingOptions.forEach(function(option) {
            var values = (option.values || []).map(function(v) { return v.value || ''; }).filter(function(v) { return v !== ''; });
            var images = (option.values || []).map(function(v) { return v.image || ''; });
            var valueIds = (option.values || []).map(function(v) { return v.id || null; });
            
            if (values.length > 0) {
                var name = option.name || '';
                if (name.toLowerCase().trim() === 'ukuran' || name.toLowerCase().trim() === 'size') {
                    addSizeOption(name, values, option.id, valueIds);
                } else {
                    addColorOption(name, values, images, option.id, valueIds);
                }
            }
        });

        setTimeout(function() {
            if (existingVariants && existingVariants.length > 0) {
                savedVariantData = existingVariants.map(function(v) {
                    var names = (v.option_value_names || []).sort().join('|');
                    return {
                        id: v.id,
                        sku: v.sku || '',
                        price: v.price || '',
                        discount_percent: v.discount_percent || '0',
                        discount_price: v.discount_price || '',
                        stock: v.stock || '0',
                        weight: v.weight || '1000',
                        value_names: names,
                        combination_text: (v.option_value_names || []).join(' - ')
                    };
                });
            }
            
            var options = getOptions();
            if (options.length > 0) {
                var combinations = generateCombinations(options);
                renderVariants(combinations);
            }
        }, 300);
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

    updateOptionsEmptyState();
    updateVariantsEmptyState();
    setTimeout(recalculateAllDiscounts, 400);
    setTimeout(syncDiscountStates, 500);
});
</script>