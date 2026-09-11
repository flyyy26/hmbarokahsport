<div class="space-y-6">
    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-semibold">Mohon perbaiki kesalahan berikut:</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Informasi Utama --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm space-y-4">
        <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3">Informasi Voucher</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase">Nama Voucher <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $voucher->name ?? '') }}" required
                    class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Contoh: Diskon Gajian 100RB">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase">Kode Promo <span class="text-red-500">*</span></label>
                <input type="text" name="code" value="{{ old('code', $voucher->code ?? '') }}" required
                    class="mt-1.5 block w-full uppercase rounded-lg border-gray-300 shadow-sm text-sm font-mono tracking-wider focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Contoh: PAYDAY100K">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase">Deskripsi Singkat</label>
            <textarea name="description" rows="2"
                class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"
                placeholder="Potongan belanja spesial gajian untuk semua kategori.">{{ old('description', $voucher->description ?? '') }}</textarea>
        </div>

        {{-- 🔥 CHECKBOX UNTUK SEMUA USER --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2 border-t border-gray-100">
            <div class="flex items-center gap-2">
                <input type="checkbox" id="is_for_all_users" name="is_for_all_users" value="1"
                    {{ old('is_for_all_users', $voucher->is_for_all_users ?? false) ? 'checked' : '' }}
                    class="h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                    onchange="toggleUsageLimit()">
                <div>
                    <label for="is_for_all_users" class="text-sm font-medium text-gray-900">🎉 Untuk Semua User</label>
                    <p class="text-xs text-gray-500">Total kuota penggunaan menjadi tidak terbatas, tetapi batas per akun tetap berlaku.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Skema Diskon & Ketentuan Transaksi --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm space-y-4">
        <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3">Skema Diskon & Batasan Belanja</h2>

        {{-- Tipe Diskon --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Target Diskon --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase">Target Diskon <span class="text-red-500">*</span></label>
                <select name="discount_target" id="discount_target" class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" onchange="toggleDiscountTarget()">
                    <option value="product" {{ old('discount_target', $voucher->discount_target ?? 'product') === 'product' ? 'selected' : '' }}>🛒 Diskon Produk</option>
                    <option value="shipping" {{ old('discount_target', $voucher->discount_target ?? '') === 'shipping' ? 'selected' : '' }}>🚚 Diskon Ongkir</option>
                </select>
                <p class="mt-1 text-[11px] text-gray-500">Pilih jenis potongan yang akan diberikan.</p>
            </div>

            {{-- Tipe Potongan --}}
            <div id="discount_type_wrapper">
                <label class="block text-xs font-semibold text-gray-700 uppercase">Tipe Potongan <span class="text-red-500">*</span></label>
                <select name="discount_type" id="discount_type" class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="fixed" {{ old('discount_type', $voucher->discount_type ?? '') === 'fixed' ? 'selected' : '' }}>Nominal Tetap (Rp)</option>
                    <option value="percentage" {{ old('discount_type', $voucher->discount_type ?? '') === 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                </select>
            </div>

            {{-- Nilai Potongan --}}
            <div id="discount_value_wrapper">
                <label class="block text-xs font-semibold text-gray-700 uppercase">Nilai Potongan <span id="discount_unit_label">(Rp)</span> <span class="text-red-500">*</span></label>
                <input type="number" name="discount_value" id="discount_value" value="{{ old('discount_value', $voucher->discount_value ?? '') }}" min="0"
                    class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="100000">
            </div>
        </div>

        <div class="mt-4 flex items-center gap-2 border-t border-gray-100 pt-4">
            <input type="checkbox" id="is_free_shipping" name="is_free_shipping" value="1"
                {{ old('is_free_shipping', $voucher->is_free_shipping ?? false) ? 'checked' : '' }}
                class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500"
                onchange="toggleFreeShipping()">
            <div>
                <label for="is_free_shipping" class="text-sm font-medium text-gray-900">🎁 Gratis Ongkir</label>
                <p class="text-xs text-gray-500">Voucher ini akan menghapus biaya pengiriman sepenuhnya.</p>
            </div>
        </div>

        <div id="max_shipping_discount_wrapper" class="hidden mt-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase">Maksimal Potongan Ongkir (Rp)</label>
                <input type="number" name="max_shipping_discount" value="{{ old('max_shipping_discount', $voucher->max_shipping_discount ?? '') }}" min="0"
                    class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Contoh: 50000">
                <p class="mt-1 text-[11px] text-gray-500">Kosongkan jika tanpa batas maks potongan ongkir.</p>
            </div>
        </div>

        <div id="max_discount_wrapper" class="hidden">
            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase">Maksimal Potongan (Rp)</label>
                <input type="number" name="max_discount_amount" 
                    value="{{ old('max_discount_amount', $voucher->max_discount_amount ?? '') }}" 
                    min="0"
                    class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" 
                    placeholder="Contoh: 150000">
                <p class="mt-1 text-[11px] text-gray-500">
                    💡 Kosongkan jika tidak ada batas maksimum potongan.
                </p>
                @error('max_discount_amount')
                    <p class="mt-1 text-[11px] text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- 🔥 PILIH KURIR --}}
        <div id="courier_selection_wrapper" class="hidden mt-4 border-t border-gray-100 pt-4">
            <div class="flex items-center gap-2 mb-3">
                <input type="checkbox" id="apply_to_all_couriers" name="apply_to_all_couriers" value="1"
                    {{ old('apply_to_all_couriers', $voucher->apply_to_all_couriers ?? true) ? 'checked' : '' }}
                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    onchange="toggleCourierSelection()">
                <label for="apply_to_all_couriers" class="text-sm font-medium text-gray-900">Berlaku untuk semua kurir</label>
            </div>

            <div id="courier_list_wrapper" class="{{ old('apply_to_all_couriers', $voucher->apply_to_all_couriers ?? true) ? 'hidden' : '' }}">
                <label class="block text-xs font-semibold text-gray-700 uppercase">Kurir yang Berlaku</label>
                <div class="mt-2 grid grid-cols-2 md:grid-cols-3 gap-2">
                    @php
                        $couriers = ['JNE', 'JNT', 'SICEPAT', 'POS', 'ANTERAJA', 'LION', 'NINJA', 'RPX'];
                        $selectedCouriers = old('applicable_couriers', $voucher->applicable_couriers ?? []);
                    @endphp
                    @foreach ($couriers as $courier)
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="applicable_couriers[]" value="{{ $courier }}"
                                {{ in_array($courier, $selectedCouriers) ? 'checked' : '' }}
                                class="h-3.5 w-3.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            {{ $courier }}
                        </label>
                    @endforeach
                </div>
                <p class="mt-1 text-[11px] text-gray-500">Pilih kurir yang diperbolehkan menggunakan voucher ini.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
            {{-- Minimal Transaksi --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase">Minimal Transaksi (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="min_transaction_amount" value="{{ old('min_transaction_amount', $voucher->min_transaction_amount ?? 0) }}" min="0" required
                    class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="300000">
            </div>

            {{-- 🔥 TOTAL KUOTA PENGGUNAAN --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase" id="usage_limit_label">Total Kuota Penggunaan</label>
                <input type="number" name="usage_limit" id="usage_limit" 
                    value="{{ old('usage_limit', $voucher->usage_limit ?? '') }}" min="1"
                    class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" 
                    placeholder="Contoh: 100"
                    {{ old('is_for_all_users', $voucher->is_for_all_users ?? false) ? 'disabled' : '' }}>
                <p class="mt-1 text-[11px] text-gray-500" id="usage_limit_hint">
                    {{ old('is_for_all_users', $voucher->is_for_all_users ?? false) ? '🔓 Tidak terbatas (Untuk Semua User)' : 'Kosongkan jika kuota tidak terbatas.' }}
                </p>
            </div>

            {{-- 🔥 BATAS PAKAI PER AKUN --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase">Batas Pakai Per Akun <span class="text-red-500">*</span></label>
                <input type="number" name="limit_per_user" value="{{ old('limit_per_user', $voucher->limit_per_user ?? 1) }}" min="1" required
                    class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="1">
                <p class="mt-1 text-[11px] text-gray-500">Berapa kali user bisa memakai voucher ini.</p>
            </div>
        </div>
    </div>

    {{-- Periode & Syarat Ketentuan --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm space-y-4">
        <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3">Periode & Syarat Ketentuan</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase">Tanggal Mulai <span class="text-red-500">*</span></label>
                <input type="datetime-local" name="start_date" required
                    value="{{ old('start_date', isset($voucher->start_date) ? $voucher->start_date->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
                    class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase">Tanggal Berakhir <span class="text-red-500">*</span></label>
                <input type="datetime-local" name="end_date" required
                    value="{{ old('end_date', isset($voucher->end_date) ? $voucher->end_date->format('Y-m-d\TH:i') : now()->addDays(7)->format('Y-m-d\TH:i')) }}"
                    class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>

        {{-- Syarat & Ketentuan (S&K) --}}
        <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase">Syarat & Ketentuan (S&K)</label>
            
            {{-- Hidden input untuk form submission --}}
            <textarea 
                name="terms_and_conditions" 
                id="terms_and_conditions" 
                style="display: none;"
            >{{ old('terms_and_conditions', $voucher->terms_and_conditions ?? '') }}</textarea>
            
            {{-- Quill Editor Container --}}
            <div 
                id="quill-editor-terms" 
                class="mt-1.5 rounded-lg border border-gray-300 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500" 
                style="min-height: 200px;"
            >
                {!! old('terms_and_conditions', $voucher->terms_and_conditions ?? '') !!}
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-gray-100">
            <div class="flex items-center gap-2">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                    {{ old('is_active', $voucher->is_active ?? true) ? 'checked' : '' }}
                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="is_active" class="text-sm font-medium text-gray-900">Aktifkan Voucher Ini</label>
            </div>

            <div class="flex items-start gap-2">
                <input type="checkbox" id="is_public" name="is_public" value="1"
                    {{ old('is_public', $voucher->is_public ?? true) ? 'checked' : '' }}
                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 mt-0.5">
                <div>
                    <label for="is_public" class="text-sm font-medium text-gray-900">Tampilkan di Daftar Voucher (Publik)</label>
                    <p class="text-xs text-gray-500">Customer bisa langsung klik tombol "Pakai" saat checkout tanpa harus mengetik kode promo.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Tombol Aksi --}}
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.vouchers.index') }}" class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
            Batal
        </a>
        <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 shadow-sm">
            {{ isset($voucher) ? 'Simpan Perubahan' : 'Buat Voucher' }}
        </button>
    </div>
</div>

@push('scripts')

<script>
function toggleDiscountTarget() {
    const target = document.getElementById('discount_target').value;
    const freeShipping = document.getElementById('is_free_shipping');
    const typeWrapper = document.getElementById('discount_type_wrapper');
    const valueWrapper = document.getElementById('discount_value_wrapper');
    const maxDiscountWrapper = document.getElementById('max_discount_wrapper');
    const maxShippingWrapper = document.getElementById('max_shipping_discount_wrapper');
    const courierWrapper = document.getElementById('courier_selection_wrapper');
    const unitLabel = document.getElementById('discount_unit_label');
    const discountType = document.getElementById('discount_type');

    // Tampilkan/sembunyikan berdasarkan target
    if (target === 'shipping') {
        courierWrapper.classList.remove('hidden');
        
        if (freeShipping.checked) {
            // Gratis Ongkir
            typeWrapper.classList.add('hidden');
            valueWrapper.classList.add('hidden');
            maxDiscountWrapper.classList.add('hidden');
            maxShippingWrapper.classList.add('hidden');
            unitLabel.textContent = '(Rp)';
        } else {
            // Diskon Ongkir
            typeWrapper.classList.remove('hidden');
            valueWrapper.classList.remove('hidden');
            maxShippingWrapper.classList.remove('hidden');
            maxDiscountWrapper.classList.add('hidden');
        }
    } else {
        // Diskon Produk
        courierWrapper.classList.add('hidden');
        maxShippingWrapper.classList.add('hidden');
        typeWrapper.classList.remove('hidden');
        valueWrapper.classList.remove('hidden');
        
        // 🔥 CEK TIPE DISKON UNTUK MAX DISCOUNT
        if (discountType.value === 'percentage') {
            maxDiscountWrapper.classList.remove('hidden');
        } else {
            maxDiscountWrapper.classList.add('hidden');
        }
    }
}

function toggleFreeShipping() {
    const freeShipping = document.getElementById('is_free_shipping');
    const target = document.getElementById('discount_target').value;
    const typeWrapper = document.getElementById('discount_type_wrapper');
    const valueWrapper = document.getElementById('discount_value_wrapper');
    const maxDiscountWrapper = document.getElementById('max_discount_wrapper');
    const maxShippingWrapper = document.getElementById('max_shipping_discount_wrapper');
    const discountType = document.getElementById('discount_type');

    if (freeShipping.checked) {
        // Gratis Ongkir
        typeWrapper.classList.add('hidden');
        valueWrapper.classList.add('hidden');
        maxDiscountWrapper.classList.add('hidden');
        maxShippingWrapper.classList.add('hidden');
        discountType.value = 'fixed';
        document.getElementById('discount_value').value = '';
    } else {
        // Bukan Gratis Ongkir
        if (target === 'product') {
            typeWrapper.classList.remove('hidden');
            valueWrapper.classList.remove('hidden');
            maxShippingWrapper.classList.add('hidden');
            
            // 🔥 CEK TIPE DISKON UNTUK MAX DISCOUNT
            if (discountType.value === 'percentage') {
                maxDiscountWrapper.classList.remove('hidden');
            } else {
                maxDiscountWrapper.classList.add('hidden');
            }
        } else {
            // shipping
            typeWrapper.classList.remove('hidden');
            valueWrapper.classList.remove('hidden');
            maxDiscountWrapper.classList.add('hidden');
            maxShippingWrapper.classList.remove('hidden');
        }
    }
}

function toggleCourierSelection() {
    const allCouriers = document.getElementById('apply_to_all_couriers');
    const courierList = document.getElementById('courier_list_wrapper');
    
    if (allCouriers.checked) {
        courierList.classList.add('hidden');
    } else {
        courierList.classList.remove('hidden');
    }
}

function updateMaxDiscountWrapper() {
    const target = document.getElementById('discount_target').value;
    const discountType = document.getElementById('discount_type').value;
    const maxDiscountWrapper = document.getElementById('max_discount_wrapper');
    const freeShipping = document.getElementById('is_free_shipping');

    // Hanya untuk produk, bukan free shipping, dan tipe percentage
    if (target === 'product' && !freeShipping.checked && discountType === 'percentage') {
        maxDiscountWrapper.classList.remove('hidden');
    } else {
        maxDiscountWrapper.classList.add('hidden');
    }
}

// Jalankan saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    // 🔥 PASTIKAN ELEMEN ADA
    const discountTarget = document.getElementById('discount_target');
    const discountType = document.getElementById('discount_type');
    const freeShipping = document.getElementById('is_free_shipping');
    const allCouriers = document.getElementById('apply_to_all_couriers');

    if (discountTarget) {
        discountTarget.addEventListener('change', toggleDiscountTarget);
    }
    if (discountType) {
        discountType.addEventListener('change', function() {
            updateMaxDiscountWrapper();
            // Update unit label
            const unitLabel = document.getElementById('discount_unit_label');
            if (this.value === 'percentage') {
                unitLabel.textContent = '(%)';
            } else {
                unitLabel.textContent = '(Rp)';
            }
        });
    }
    if (freeShipping) {
        freeShipping.addEventListener('change', toggleFreeShipping);
    }
    if (allCouriers) {
        allCouriers.addEventListener('change', toggleCourierSelection);
    }

    // Jalankan initial state
    toggleDiscountTarget();
    toggleFreeShipping();
    toggleCourierSelection();
    updateMaxDiscountWrapper();
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var discountType = document.getElementById('discount_type');
        var maxWrapper = document.getElementById('max_discount_wrapper');
        var unitLabel = document.getElementById('discount_unit_label');
        var valInput = document.getElementById('discount_value');

        var isForAllUsers = document.getElementById('is_for_all_users');
        var usageLimit = document.getElementById('usage_limit');
        var usageLimitLabel = document.getElementById('usage_limit_label');
        var usageLimitHint = document.getElementById('usage_limit_hint');

        // ============================================
        // 🔥 UPDATE FIELD DISKON
        // ============================================
        function updateDiscountFields() {
            if (discountType.value === 'percentage') {
                maxWrapper.classList.remove('hidden');
                unitLabel.textContent = '(%)';
                valInput.placeholder = 'Contoh: 20';
            } else {
                maxWrapper.classList.add('hidden');
                unitLabel.textContent = '(Rp)';
                valInput.placeholder = 'Contoh: 100000';
            }
        }

        discountType.addEventListener('change', updateDiscountFields);
        updateDiscountFields();

        // ============================================
        // 🔥 UPDATE USAGE LIMIT
        // ============================================
        function toggleUsageLimit() {
            if (isForAllUsers.checked) {
                // 🔥 UNTUK SEMUA USER: TOTAL KUOTA MENJADI TIDAK TERBATAS
                usageLimit.disabled = true;
                usageLimit.value = '';
                usageLimit.placeholder = 'Tidak Terbatas';
                usageLimit.classList.add('bg-gray-100', 'cursor-not-allowed');
                usageLimitLabel.textContent = 'Total Kuota Penggunaan';
                usageLimitHint.textContent = '🔓 Tidak terbatas (Untuk Semua User)';
                usageLimitHint.classList.remove('text-gray-500');
                usageLimitHint.classList.add('text-purple-600');
            } else {
                // 🔥 USER SPESIFIK: TOTAL KUOTA BISA DIISI
                usageLimit.disabled = false;
                usageLimit.placeholder = 'Contoh: 100';
                usageLimit.classList.remove('bg-gray-100', 'cursor-not-allowed');
                usageLimitLabel.textContent = 'Total Kuota Penggunaan';
                usageLimitHint.textContent = 'Kosongkan jika kuota tidak terbatas.';
                usageLimitHint.classList.remove('text-purple-600');
                usageLimitHint.classList.add('text-gray-500');
            }
        }

        // Jalankan saat halaman dimuat
        toggleUsageLimit();

        // Event listener
        isForAllUsers.addEventListener('change', toggleUsageLimit);
    });
</script>

@endpush