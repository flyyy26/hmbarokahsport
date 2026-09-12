<div class="space-y-6">

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
    {{-- INFORMASI UTAMA --}}
    {{-- ============================================ --}}
    <div class="rounded-2xl border overflow-hidden"
         style="background: var(--bg-card); border-color: var(--border-2)">

        <div class="px-5 py-4 border-b flex items-center gap-2"
             style="background: var(--bg-input); border-color: var(--border-2)">
            <iconify-icon icon="mdi:information-outline" class="text-[#ecbc42] text-base"></iconify-icon>
            <h2 class="font-bold text-sm" style="color: var(--text-1)">Informasi Voucher</h2>
        </div>

        <div class="p-5 space-y-5">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Nama Voucher --}}
                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:tag-outline" class="text-[#ecbc42]"></iconify-icon>
                        Nama Voucher <span class="text-red-400">*</span>
                    </label>
                    <input type="text"
                           name="name"
                           value="{{ old('name', $voucher->name ?? '') }}"
                           required
                           class="form-input"
                           placeholder="Contoh: Diskon Gajian 100RB">
                </div>

                {{-- Kode Promo --}}
                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:code-tags" class="text-[#ecbc42]"></iconify-icon>
                        Kode Promo <span class="text-red-400">*</span>
                    </label>
                    <input type="text"
                           name="code"
                           value="{{ old('code', $voucher->code ?? '') }}"
                           required
                           class="form-input uppercase font-mono tracking-wider"
                           placeholder="Contoh: PAYDAY100K">
                </div>
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="form-label">
                    <iconify-icon icon="mdi:text-box-outline" class="text-[#ecbc42]"></iconify-icon>
                    Deskripsi Singkat
                </label>
                <textarea name="description"
                          rows="2"
                          class="form-input"
                          placeholder="Potongan belanja spesial gajian untuk semua kategori.">{{ old('description', $voucher->description ?? '') }}</textarea>
            </div>

            {{-- Checkbox Semua User --}}
            <div class="pt-4 border-t" style="border-color: var(--border-1)">
                <label class="flex items-start gap-3 cursor-pointer group">
                    <input type="checkbox"
                           id="is_for_all_users"
                           name="is_for_all_users"
                           value="1"
                           {{ old('is_for_all_users', $voucher->is_for_all_users ?? false) ? 'checked' : '' }}
                           class="mt-0.5 h-4 w-4 rounded cursor-pointer"
                           style="accent-color: #ecbc42;">
                    <div>
                        <span class="text-sm font-semibold flex items-center gap-1.5" style="color: var(--text-1)">
                            <iconify-icon icon="mdi:account-group-outline" class="text-[#ecbc42]"></iconify-icon>
                            Untuk Semua User
                        </span>
                        <p class="text-xs mt-0.5" style="color: var(--text-5)">
                            Total kuota penggunaan menjadi tidak terbatas, tetapi batas per akun tetap berlaku.
                        </p>
                    </div>
                </label>
            </div>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- SKEMA DISKON --}}
    {{-- ============================================ --}}
    <div class="rounded-2xl border overflow-hidden"
         style="background: var(--bg-card); border-color: var(--border-2)">

        <div class="px-5 py-4 border-b flex items-center gap-2"
             style="background: var(--bg-input); border-color: var(--border-2)">
            <iconify-icon icon="mdi:scissors-cutting" class="text-[#ecbc42] text-base"></iconify-icon>
            <h2 class="font-bold text-sm" style="color: var(--text-1)">Skema Diskon & Batasan Belanja</h2>
        </div>

        <div class="p-5 space-y-5">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                {{-- Target Diskon --}}
                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:target" class="text-[#ecbc42]"></iconify-icon>
                        Target Diskon <span class="text-red-400">*</span>
                    </label>
                    <select name="discount_target" id="discount_target" class="form-input">
                        <option value="product" {{ old('discount_target', $voucher->discount_target ?? 'product') === 'product' ? 'selected' : '' }}>
                            Diskon Produk
                        </option>
                        <option value="shipping" {{ old('discount_target', $voucher->discount_target ?? '') === 'shipping' ? 'selected' : '' }}>
                            Diskon Ongkir
                        </option>
                    </select>
                    <p class="text-[10px] mt-1" style="color: var(--text-5)">Pilih jenis potongan yang diberikan.</p>
                </div>

                {{-- Tipe Potongan --}}
                <div id="discount_type_wrapper">
                    <label class="form-label">
                        <iconify-icon icon="mdi:percent-outline" class="text-[#ecbc42]"></iconify-icon>
                        Tipe Potongan <span class="text-red-400">*</span>
                    </label>
                    <select name="discount_type" id="discount_type" class="form-input">
                        <option value="fixed" {{ old('discount_type', $voucher->discount_type ?? '') === 'fixed' ? 'selected' : '' }}>
                            Nominal Tetap (Rp)
                        </option>
                        <option value="percentage" {{ old('discount_type', $voucher->discount_type ?? '') === 'percentage' ? 'selected' : '' }}>
                            Persentase (%)
                        </option>
                    </select>
                </div>

                {{-- Nilai Potongan --}}
                <div id="discount_value_wrapper">
                    <label class="form-label">
                        <iconify-icon icon="mdi:cash" class="text-[#ecbc42]"></iconify-icon>
                        Nilai Potongan <span id="discount_unit_label">(Rp)</span> <span class="text-red-400">*</span>
                    </label>
                    <input type="number"
                           name="discount_value"
                           id="discount_value"
                           value="{{ old('discount_value', $voucher->discount_value ?? '') }}"
                           min="0"
                           class="form-input"
                           placeholder="100000">
                </div>
            </div>

            {{-- Gratis Ongkir --}}
            <div id="free_shipping_wrapper" class="hidden flex items-center gap-3 pt-4 border-t" style="border-color: var(--border-1)">
                <input type="checkbox"
                    id="is_free_shipping"
                    name="is_free_shipping"
                    value="1"
                    {{ old('is_free_shipping', $voucher->is_free_shipping ?? false) ? 'checked' : '' }}
                    class="h-4 w-4 rounded cursor-pointer"
                    style="accent-color: #ecbc42;">
                <label for="is_free_shipping" class="cursor-pointer">
                    <span class="text-sm font-semibold flex items-center gap-1.5" style="color: var(--text-1)">
                        <iconify-icon icon="mdi:truck-fast-outline" class="text-[#ecbc42]"></iconify-icon>
                        Gratis Ongkir
                    </span>
                    <p class="text-xs" style="color: var(--text-5)">Voucher ini akan menghapus biaya pengiriman sepenuhnya.</p>
                </label>
            </div>

            {{-- Max Shipping Discount --}}
            <div id="max_shipping_discount_wrapper" class="hidden">
                <label class="form-label">
                    <iconify-icon icon="mdi:cash-limit" class="text-[#ecbc42]"></iconify-icon>
                    Maksimal Potongan Ongkir (Rp)
                </label>
                <input type="number"
                    name="max_shipping_discount"
                    value="{{ old('max_shipping_discount', $voucher->max_shipping_discount ?? '') }}"
                    min="0"
                    class="form-input"
                    placeholder="Contoh: 50000">
                <p class="text-[10px] mt-1" style="color: var(--text-5)">Kosongkan jika tanpa batas maks potongan ongkir.</p>
            </div>

            {{-- Max Discount --}}
            <div id="max_discount_wrapper" class="hidden">
                <label class="form-label">
                    <iconify-icon icon="mdi:cash-limit" class="text-[#ecbc42]"></iconify-icon>
                    Maksimal Potongan (Rp)
                </label>
                <input type="number"
                    name="max_discount_amount"
                    value="{{ old('max_discount_amount', $voucher->max_discount_amount ?? '') }}"
                    min="0"
                    class="form-input"
                    placeholder="Contoh: 150000">
                <p class="text-[10px] mt-1" style="color: var(--text-5)">
                    Kosongkan jika tidak ada batas maksimum potongan.
                </p>
                @error('max_discount_amount')
                    <p class="text-[10px] mt-1 text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Pilih Kurir --}}
            <div id="courier_selection_wrapper" class="hidden pt-4 border-t" style="border-color: var(--border-1)">
                <label class="flex items-center gap-2 cursor-pointer mb-3">
                    <input type="checkbox"
                           id="apply_to_all_couriers"
                           name="apply_to_all_couriers"
                           value="1"
                           {{ old('apply_to_all_couriers', $voucher->apply_to_all_couriers ?? true) ? 'checked' : '' }}
                           class="h-4 w-4 rounded cursor-pointer"
                           style="accent-color: #ecbc42;">
                    <span class="text-sm font-semibold" style="color: var(--text-1)">Berlaku untuk semua kurir</span>
                </label>

                <div id="courier_list_wrapper" class="{{ old('apply_to_all_couriers', $voucher->apply_to_all_couriers ?? true) ? 'hidden' : '' }}">
                    <label class="form-label">
                        Kurir yang Berlaku
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        @php
                            $couriers = [
                                'jne'       => 'JNE',
                                'jnt'       => 'J&T Express',
                                'sicepat'   => 'SiCepat',
                                'pos'       => 'POS Indonesia',
                                'anteraja'  => 'AnterAja',
                                'lion'      => 'Lion Parcel',
                                'ninja'     => 'Ninja Xpress',
                                'rpx'       => 'RPX',
                                'pahala'    => 'Pahala Express',
                                'wahana'    => 'Wahana',
                                'tiki'      => 'TIKI',
                                'ncs'       => 'NCS',
                                'first'     => 'First Logistics',
                                'idexpress' => 'ID Express',
                                'star'      => 'Star Cargo',
                            ];

                            // Ambil kurir yang sudah dipilih (dari old atau database)
                            $selectedCouriers = old('applicable_couriers', $voucher->applicable_couriers ?? []);

                            // 🔥 Normalisasi: pastikan semua lowercase untuk perbandingan
                            $selectedCouriers = array_map('strtolower', (array) $selectedCouriers);
                        @endphp
                        @foreach ($couriers as $value => $label)
                            <label class="flex items-center gap-2 text-sm cursor-pointer px-3 py-2 rounded-lg border transition-all
                                        hover:border-[#ecbc42]/50"
                                style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3);">

                                <input type="checkbox"
                                    name="applicable_couriers[]"
                                    value="{{ $value }}"
                                    {{ in_array($value, $selectedCouriers) ? 'checked' : '' }}
                                    class="h-3.5 w-3.5 rounded cursor-pointer"
                                    style="accent-color: #ecbc42;">

                                <span class="text-xs font-semibold">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-[10px] mt-2" style="color: var(--text-5)">Pilih kurir yang diperbolehkan menggunakan voucher ini.</p>
                </div>
            </div>

            {{-- Min, Kuota, Per User --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">

                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:cart-outline" class="text-[#ecbc42]"></iconify-icon>
                        Minimal Transaksi (Rp) <span class="text-red-400">*</span>
                    </label>
                    <input type="number"
                           name="min_transaction_amount"
                           value="{{ old('min_transaction_amount', $voucher->min_transaction_amount ?? 0) }}"
                           min="0"
                           required
                           class="form-input"
                           placeholder="300000">
                </div>

                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:counter" class="text-[#ecbc42]"></iconify-icon>
                        Total Kuota Penggunaan
                    </label>
                    <input type="number"
                           name="usage_limit"
                           id="usage_limit"
                           value="{{ old('usage_limit', $voucher->usage_limit ?? '') }}"
                           min="1"
                           class="form-input"
                           placeholder="Contoh: 100"
                           {{ old('is_for_all_users', $voucher->is_for_all_users ?? false) ? 'disabled' : '' }}>
                    <p class="text-[10px] mt-1" style="color: var(--text-5)" id="usage_limit_hint">
                        {{ old('is_for_all_users', $voucher->is_for_all_users ?? false) ? '🔓 Tidak terbatas (Untuk Semua User)' : 'Kosongkan jika kuota tidak terbatas.' }}
                    </p>
                </div>

                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:account-check-outline" class="text-[#ecbc42]"></iconify-icon>
                        Batas Pakai Per Akun <span class="text-red-400">*</span>
                    </label>
                    <input type="number"
                           name="limit_per_user"
                           value="{{ old('limit_per_user', $voucher->limit_per_user ?? 1) }}"
                           min="1"
                           required
                           class="form-input"
                           placeholder="1">
                    <p class="text-[10px] mt-1" style="color: var(--text-5)">Berapa kali user bisa memakai voucher ini.</p>
                </div>
            </div>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- PERIODE & S&K --}}
    {{-- ============================================ --}}
    <div class="rounded-2xl border overflow-hidden"
         style="background: var(--bg-card); border-color: var(--border-2)">

        <div class="px-5 py-4 border-b flex items-center gap-2"
             style="background: var(--bg-input); border-color: var(--border-2)">
            <iconify-icon icon="mdi:calendar-range" class="text-[#ecbc42] text-base"></iconify-icon>
            <h2 class="font-bold text-sm" style="color: var(--text-1)">Periode & Syarat Ketentuan</h2>
        </div>

        <div class="p-5 space-y-5">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Tanggal Mulai --}}
                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:calendar-start" class="text-[#ecbc42]"></iconify-icon>
                        Tanggal Mulai <span class="text-red-400">*</span>
                    </label>
                    <input type="datetime-local"
                           name="start_date"
                           required
                           value="{{ old('start_date', isset($voucher->start_date) ? $voucher->start_date->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
                           class="form-input">
                </div>

                {{-- Tanggal Berakhir --}}
                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:calendar-end" class="text-[#ecbc42]"></iconify-icon>
                        Tanggal Berakhir <span class="text-red-400">*</span>
                    </label>
                    <input type="datetime-local"
                           name="end_date"
                           required
                           value="{{ old('end_date', isset($voucher->end_date) ? $voucher->end_date->format('Y-m-d\TH:i') : now()->addDays(7)->format('Y-m-d\TH:i')) }}"
                           class="form-input">
                </div>
            </div>

            {{-- S&K --}}
            <div>
                <label class="form-label">
                    <iconify-icon icon="mdi:clipboard-text-outline" class="text-[#ecbc42]"></iconify-icon>
                    Syarat & Ketentuan (S&K)
                </label>

                <textarea name="terms_and_conditions"
                          id="terms_and_conditions"
                          style="display: none;">{{ old('terms_and_conditions', $voucher->terms_and_conditions ?? '') }}</textarea>

                <div id="quill-editor-terms"
                     class="rounded-lg border overflow-hidden"
                     style="min-height: 200px; border-color: var(--border-2);"
                     data-placeholder="1. Berlaku untuk seluruh produk.
2. Tidak dapat digabung dengan promo lain.
3. Dll...">
                    {!! old('terms_and_conditions', $voucher->terms_and_conditions ?? '') !!}
                </div>
            </div>

            {{-- Status Checkboxes --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t" style="border-color: var(--border-1)">

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox"
                           id="is_active"
                           name="is_active"
                           value="1"
                           {{ old('is_active', $voucher->is_active ?? true) ? 'checked' : '' }}
                           class="h-4 w-4 rounded cursor-pointer"
                           style="accent-color: #ecbc42;">
                    <span class="text-sm font-semibold" style="color: var(--text-1)">Aktifkan Voucher Ini</span>
                </label>

                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox"
                           id="is_public"
                           name="is_public"
                           value="1"
                           {{ old('is_public', $voucher->is_public ?? true) ? 'checked' : '' }}
                           class="mt-0.5 h-4 w-4 rounded cursor-pointer"
                           style="accent-color: #ecbc42;">
                    <div>
                        <span class="text-sm font-semibold" style="color: var(--text-1)">Tampilkan di Daftar Voucher (Publik)</span>
                        <p class="text-xs mt-0.5" style="color: var(--text-5)">
                            Customer bisa langsung klik tombol "Pakai" saat checkout tanpa mengetik kode promo.
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
        <a href="{{ route('admin.vouchers.index') }}"
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
            {{ isset($voucher) && $voucher ? 'Simpan Perubahan' : 'Buat Voucher' }}
        </button>
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
    .form-input:disabled {
        background: var(--bg-hover) !important;
        color: var(--text-5) !important;
        cursor: not-allowed;
        opacity: 0.7;
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

    /* Quill Editor Override */
    #quill-editor-terms .ql-toolbar {
        border: none;
        border-bottom: 1px solid var(--border-2);
        background: var(--bg-input);
        border-radius: 0;
    }
    #quill-editor-terms .ql-container {
        border: none;
        background: var(--bg-input);
        font-family: inherit;
    }
    #quill-editor-terms .ql-editor {
        min-height: 180px !important;
        max-height: 400px !important;
        color: var(--text-2);
        font-size: 0.875rem;
    }
    #quill-editor-terms .ql-editor.ql-blank::before {
        color: var(--text-6);
        font-style: normal;
    }
</style>


{{-- ============================================ --}}
{{-- SCRIPTS --}}
{{-- ============================================ --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    


    // ============================================
    // TOGGLE FUNCTIONS
    // ============================================
    var discountTarget = document.getElementById('discount_target');
    var discountType = document.getElementById('discount_type');
    var freeShipping = document.getElementById('is_free_shipping');
    var allCouriers = document.getElementById('apply_to_all_couriers');
    var isForAllUsers = document.getElementById('is_for_all_users');
    var usageLimit = document.getElementById('usage_limit');
    var usageLimitHint = document.getElementById('usage_limit_hint');

    function toggleUsageLimit() {
        if (!isForAllUsers || !usageLimit) return;

        if (isForAllUsers.checked) {
            usageLimit.disabled = true;
            usageLimit.value = '';
            usageLimit.placeholder = 'Tidak Terbatas';
            if (usageLimitHint) usageLimitHint.textContent = '🔓 Tidak terbatas (Untuk Semua User)';
        } else {
            usageLimit.disabled = false;
            usageLimit.placeholder = 'Contoh: 100';
            if (usageLimitHint) usageLimitHint.textContent = 'Kosongkan jika kuota tidak terbatas.';
        }
    }

    function toggleCourierSelection() {
        var courierList = document.getElementById('courier_list_wrapper');
        if (!allCouriers || !courierList) return;

        if (allCouriers.checked) {
            courierList.classList.add('hidden');
        } else {
            courierList.classList.remove('hidden');
        }
    }

    function toggleFreeShipping() {
        if (!freeShipping) return;

        var typeWrapper = document.getElementById('discount_type_wrapper');
        var valueWrapper = document.getElementById('discount_value_wrapper');
        var maxDiscountWrapper = document.getElementById('max_discount_wrapper');
        var maxShippingWrapper = document.getElementById('max_shipping_discount_wrapper');

        if (freeShipping.checked) {
            // Sembunyikan semua
            if (typeWrapper) typeWrapper.classList.add('hidden');
            if (valueWrapper) valueWrapper.classList.add('hidden');
            if (maxDiscountWrapper) {
                maxDiscountWrapper.classList.add('hidden');
                maxDiscountWrapper.style.display = 'none';
            }
            if (maxShippingWrapper) {
                maxShippingWrapper.classList.add('hidden');
                maxShippingWrapper.style.display = 'none';
            }
            
            if (discountType) discountType.value = 'fixed';
            var discountValue = document.getElementById('discount_value');
            if (discountValue) discountValue.value = '';
        } else {
            if (typeWrapper) typeWrapper.classList.remove('hidden');
            if (valueWrapper) valueWrapper.classList.remove('hidden');
            updateMaxDiscountWrapper();
        }
    }


    function updateMaxDiscountWrapper() {
        if (!discountTarget || !discountType) return;

        var target = discountTarget.value;
        var type = discountType.value;
        var maxDiscountWrapper = document.getElementById('max_discount_wrapper');
        var maxShippingWrapper = document.getElementById('max_shipping_discount_wrapper');
        var freeShippingChecked = freeShipping && freeShipping.checked;

        // 🔥 RESET: Sembunyikan KEDUANYA dulu
        if (maxDiscountWrapper) {
            maxDiscountWrapper.classList.add('hidden');
            maxDiscountWrapper.style.display = 'none';
        }
        if (maxShippingWrapper) {
            maxShippingWrapper.classList.add('hidden');
            maxShippingWrapper.style.display = 'none';
        }

        // Jika gratis ongkir, jangan tampilkan apapun
        if (freeShippingChecked) return;

        // 🔥 Tampilkan HANYA yang sesuai
        if (target === 'product' && type === 'percentage') {
            if (maxDiscountWrapper) {
                maxDiscountWrapper.classList.remove('hidden');
                maxDiscountWrapper.style.display = 'block';
            }
        } else if (target === 'shipping' && type === 'percentage') {
            if (maxShippingWrapper) {
                maxShippingWrapper.classList.remove('hidden');
                maxShippingWrapper.style.display = 'block';
            }
        }
        // Untuk tipe 'fixed': keduanya tetap hidden

        // 🔥 Debug log (hapus setelah fix terbukti bekerja)
        console.log('🔍 updateMaxDiscountWrapper:', {
            target: target,
            type: type,
            freeShipping: freeShippingChecked,
            showMaxProduct: target === 'product' && type === 'percentage',
            showMaxShipping: target === 'shipping' && type === 'percentage',
        });
    }


    function toggleDiscountTarget() {
        if (!discountTarget) return;

        var target = discountTarget.value;
        var courierWrapper = document.getElementById('courier_selection_wrapper');
        var typeWrapper = document.getElementById('discount_type_wrapper');
        var valueWrapper = document.getElementById('discount_value_wrapper');
        var freeShippingWrapper = document.getElementById('free_shipping_wrapper');
        var freeShippingChecked = freeShipping && freeShipping.checked;

        // Toggle kurir
        if (target === 'shipping') {
            if (courierWrapper) courierWrapper.classList.remove('hidden');
            // 🔥 Tampilkan checkbox Gratis Ongkir
            if (freeShippingWrapper) freeShippingWrapper.classList.remove('hidden');
        } else {
            if (courierWrapper) courierWrapper.classList.add('hidden');
            // 🔥 Sembunyikan checkbox Gratis Ongkir
            if (freeShippingWrapper) freeShippingWrapper.classList.add('hidden');
            
            // 🔥 Reset checkbox gratis ongkir ke unchecked
            if (freeShipping) {
                freeShipping.checked = false;
            }
        }

        // Tampilkan type & value (jika bukan gratis ongkir)
        if (!freeShippingChecked) {
            if (typeWrapper) typeWrapper.classList.remove('hidden');
            if (valueWrapper) valueWrapper.classList.remove('hidden');
        }

        // 🔥 SELALU update max wrapper di akhir
        updateMaxDiscountWrapper();
    }

    // ============================================
    // EVENT LISTENERS
    // ============================================
    if (discountTarget) discountTarget.addEventListener('change', toggleDiscountTarget);

    if (discountType) {
        discountType.addEventListener('change', function() {
            var unitLabel = document.getElementById('discount_unit_label');
            if (unitLabel) {
                unitLabel.textContent = this.value === 'percentage' ? '(%)' : '(Rp)';
            }
            var discountValue = document.getElementById('discount_value');
            if (discountValue) {
                discountValue.placeholder = this.value === 'percentage' ? 'Contoh: 20' : 'Contoh: 100000';
            }
            // 🔥 WAJIB panggil updateMaxDiscountWrapper
            updateMaxDiscountWrapper();
        });
    }

    if (freeShipping) freeShipping.addEventListener('change', toggleFreeShipping);
    if (allCouriers) allCouriers.addEventListener('change', toggleCourierSelection);
    if (isForAllUsers) isForAllUsers.addEventListener('change', toggleUsageLimit);

    // ============================================
    // INIT
    // ============================================
    toggleUsageLimit();
    toggleDiscountTarget();
    toggleCourierSelection();
    updateMaxDiscountWrapper();
});
</script>
@endpush