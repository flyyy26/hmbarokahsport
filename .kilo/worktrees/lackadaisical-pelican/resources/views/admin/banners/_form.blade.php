@csrf

<div class="space-y-6">

    @if (session('error') || $errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            @if (session('error'))
                <p>{{ session('error') }}</p>
            @endif

            @if ($errors->any())
                <ul class="mt-1 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    {{-- REKOMENDASI UKURAN --}}
    <div class="rounded-lg bg-blue-50 border border-blue-200 p-4">
        <div class="flex items-start gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="text-sm font-medium text-blue-800">Rekomendasi Ukuran Gambar</p>
                <div class="mt-1 flex flex-wrap gap-4 text-xs text-blue-700">
                    <span>🖥️ Desktop: <strong>1040px × 377px</strong></span>
                    <span>📱 Mobile: <strong>455px × 269px</strong></span>
                    <span>📁 Format: JPG, PNG, WEBP</span>
                    <span>📦 Max: 5MB</span>
                </div>
            </div>
        </div>
    </div>

    {{-- TITLE --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">
            Judul Banner <span class="text-xs text-gray-400">(Opsional)</span>
        </label>

        <input type="text"
            name="title"
            value="{{ old('title', $banner->title ?? '') }}"
            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            placeholder="Contoh: Koleksi Terbaru">
        <p class="mt-1 text-xs text-gray-400">Tidak wajib diisi, hanya untuk keperluan administrasi.</p>
    </div>

    {{-- SUBTITLE --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">
            Subjudul <span class="text-xs text-gray-400">(Opsional)</span>
        </label>

        <input type="text"
            name="subtitle"
            value="{{ old('subtitle', $banner->subtitle ?? '') }}"
            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            placeholder="Contoh: Temukan produk terbaik kami">
        <p class="mt-1 text-xs text-gray-400">Tidak wajib diisi, hanya untuk keperluan administrasi.</p>
    </div>

    {{-- IMAGE DESKTOP --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">
            Gambar Desktop <span class="text-red-500">*</span>
            <span class="text-xs text-gray-400">(Rekomendasi: 1040px × 377px)</span>
        </label>

        @if (isset($banner) && $banner->image)
            <div class="mt-3 mb-4">
                <div class="relative inline-block">
                    <img src="{{ asset('storage/' . $banner->image) }}"
                        class="h-48 w-full max-w-2xl rounded-xl object-cover border border-gray-200"
                        alt="{{ $banner->title ?? 'Banner' }}">
                    <span class="absolute top-2 left-2 rounded-lg bg-blue-600 px-2 py-0.5 text-xs font-semibold text-white">
                        Desktop
                    </span>
                </div>
            </div>
        @endif

        <input type="file"
            name="image"
            accept="image/jpeg,image/png,image/webp"
            {{ isset($banner) ? '' : 'required' }}
            class="mt-2 block w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100">

        @if (isset($banner))
            <p class="mt-2 text-xs text-gray-500">Kosongkan jika tidak ingin mengganti gambar desktop.</p>
        @endif
    </div>

    {{-- IMAGE MOBILE --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">
            Gambar Mobile <span class="text-xs text-gray-400">(Opsional)</span>
            <span class="text-xs text-gray-400">(Rekomendasi: 455px × 269px)</span>
        </label>

        @if (isset($banner) && $banner->image_mobile)
            <div class="mt-3 mb-4">
                <div class="relative inline-block">
                    <img src="{{ asset('storage/' . $banner->image_mobile) }}"
                        class="h-48 w-64 rounded-xl object-cover border border-gray-200"
                        alt="{{ $banner->title ?? 'Banner Mobile' }}">
                    <span class="absolute top-2 left-2 rounded-lg bg-purple-600 px-2 py-0.5 text-xs font-semibold text-white">
                        Mobile
                    </span>
                </div>
            </div>
        @endif

        <input type="file"
            name="image_mobile"
            accept="image/jpeg,image/png,image/webp"
            class="mt-2 block w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-purple-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-purple-700 hover:file:bg-purple-100">

        @if (isset($banner))
            <p class="mt-2 text-xs text-gray-500">Kosongkan jika tidak ingin mengganti gambar mobile.</p>
        @else
            <p class="mt-2 text-xs text-gray-400">Jika tidak diisi, akan menggunakan gambar desktop secara otomatis.</p>
        @endif
    </div>

    {{-- BUTTON TEXT --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">
            Teks Tombol <span class="text-xs text-gray-400">(Opsional)</span>
        </label>

        <input type="text"
            name="button_text"
            value="{{ old('button_text', $banner->button_text ?? '') }}"
            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            placeholder="Contoh: Belanja Sekarang">
    </div>

    {{-- BUTTON URL --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">
            Link Tombol <span class="text-xs text-gray-400">(Opsional)</span>
        </label>

        <input type="text"
            name="button_url"
            value="{{ old('button_url', $banner->button_url ?? '') }}"
            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            placeholder="/products">
    </div>

    {{-- SORT ORDER --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">
            Urutan <span class="text-red-500">*</span>
        </label>

        <input type="number"
            name="sort_order"
            min="0"
            value="{{ old('sort_order', $banner->sort_order ?? 0) }}"
            required
            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <p class="mt-1 text-xs text-gray-400">Semakin kecil angka, semakin awal tampil.</p>
    </div>

    {{-- DATE --}}
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-gray-700">
                Mulai Tampil <span class="text-xs text-gray-400">(Opsional)</span>
            </label>

            <input type="datetime-local"
                name="starts_at"
                value="{{ old('starts_at', isset($banner->starts_at) ? $banner->starts_at->format('Y-m-d\TH:i') : '') }}"
                class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">
                Berakhir <span class="text-xs text-gray-400">(Opsional)</span>
            </label>

            <input type="datetime-local"
                name="ends_at"
                value="{{ old('ends_at', isset($banner->ends_at) ? $banner->ends_at->format('Y-m-d\TH:i') : '') }}"
                class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
    </div>

    {{-- ACTIVE --}}
    <div>
        {{-- 🔥 TAMBAHKAN HIDDEN INPUT --}}
        <input type="hidden" name="is_active" value="0">
        
        <label class="flex items-center gap-3">
            <input type="checkbox"
                name="is_active"
                value="1"
                @checked(old('is_active', $banner->is_active ?? true))
                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <span class="text-sm text-gray-700">Banner aktif</span>
        </label>
    </div>

    {{-- ACTION --}}
    <div class="flex justify-end gap-3 border-t pt-6">
        <a href="{{ route('admin.banners.index') }}"
            class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
            Batal
        </a>

        <button type="submit"
            class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
            {{ isset($banner) ? 'Simpan Perubahan' : 'Simpan Banner' }}
        </button>
    </div>

</div>