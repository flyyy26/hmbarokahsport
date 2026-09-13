@extends('layouts.admin')

@section('title', 'Pesanan Offline')
@section('page-title', 'Pesanan Offline')

@section('content')

<style>
    .discount-type-label {
    background: transparent;
    color: var(--text-4);
    border: 1px solid transparent;
}

.discount-type-label:hover:not(:has(.discount-type-radio:checked)) {
    background: var(--bg-hover);
    color: var(--text-3);
}

/* Active state */
.discount-type-label:has(.discount-type-radio:checked) {
    background: linear-gradient(135deg, #FDDD57 0%, #ecbc42 100%);
    color: #422006;
    border-color: #ecbc42;
    box-shadow: 0 4px 12px rgba(236, 188, 66, 0.25);
    transform: translateY(-1px);
}

.discount-type-label:has(.discount-type-radio:checked) iconify-icon {
    color: #422006;
}

.discount-type-label:has(.discount-type-radio:checked)::before {
    content: '';
    position: absolute;
    bottom: -6px;
    left: 50%;
    transform: translateX(-50%);
    width: 40%;
    height: 2px;
    background: linear-gradient(90deg, transparent, #ecbc42, transparent);
    border-radius: 2px;
}
</style>

<div class="w-full space-y-6">

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

    {{-- TOAST CONTAINER --}}
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2 w-full max-w-sm pointer-events-none"></div>


    {{-- ============================================ --}}
    {{-- HEADER --}}
    {{-- ============================================ --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <h1 class="text-2xl font-bold flex items-center gap-2.5" style="color: var(--text-1)">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl
                             bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                             shadow-lg shadow-amber-500/20 flex-shrink-0">
                    <iconify-icon icon="mdi:cart-plus" class="text-slate-900 text-2xl"></iconify-icon>
                </span>
                Pesanan Offline
            </h1>
            <p class="text-sm mt-1.5 ml-12" style="color: var(--text-5)">
                Buat pesanan langsung untuk pelanggan yang belanja di toko.
            </p>
        </div>

        <a href="{{ route('admin.orders.index') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg
                  text-sm font-semibold transition-all active:scale-95 border flex-shrink-0"
           style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)"
           onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
           onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'">
            <iconify-icon icon="mdi:arrow-left"></iconify-icon>
            Kembali
        </a>
    </div>


    {{-- ============================================ --}}
    {{-- ORDER FORM --}}
    {{-- ============================================ --}}
    <form id="orderForm" method="POST" action="{{ route('admin.orders.offline.create') }}">
        @csrf
        <input type="hidden" name="items" id="itemsInput">

        <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">

            {{-- ============================================ --}}
            {{-- LEFT: PRODUCT SEARCH & GRID --}}
            {{-- ============================================ --}}
            <div class="xl:col-span-3 space-y-4">

                {{-- Search Box --}}
                <div class="relative">
                    <input type="text"
                           id="productSearch"
                           placeholder="Cari produk berdasarkan nama atau SKU..."
                           class="w-full pl-11 pr-4 py-3 rounded-xl text-sm
                                  transition-all focus:outline-none"
                           style="background: var(--bg-input);
                                  border: 1px solid var(--border-2);
                                  color: var(--text-1);"
                           value="{{ $search }}"
                           onfocus="this.style.borderColor='#ecbc42'; this.style.boxShadow='0 0 0 3px rgba(236,188,66,0.15)';"
                           onblur="this.style.borderColor='var(--border-2)'; this.style.boxShadow='none';">
                    <iconify-icon icon="mdi:magnify"
                                  class="absolute left-4 top-1/2 -translate-y-1/2 text-lg pointer-events-none"
                                  style="color: var(--text-5)"></iconify-icon>
                    <div id="searchLoading" class="absolute right-4 top-1/2 -translate-y-1/2 hidden">
                        <div class="w-4 h-4 rounded-full border-2 border-t-transparent animate-spin"
                             style="border-color: #ecbc42; border-top-color: transparent;"></div>
                    </div>
                </div>

                {{-- Category Tabs --}}
                <div class="mb-4">
                    <div class="flex overflow-x-auto pb-1 scrollbar-hide gap-2"
                         id="categoryTabs">

                        <button type="button"
                                data-category-id=""
                                onclick="filterByCategory('')"
                                class="category-tab flex-shrink-0 flex items-center justify-center
                                       gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold
                                       border transition-all whitespace-nowrap
                                       {{ empty($categoryId) ? 'active-tab' : 'inactive-tab' }}"
                                style="{{ empty($categoryId) ? 'background: #ecbc42; color: #1e293b; border-color: #ecbc42;' : 'background: var(--bg-input); color: var(--text-4); border-color: var(--border-2);' }}">
                            <iconify-icon icon="mdi:view-dashboard-outline"></iconify-icon>
                            Semua
                        </button>

                        @foreach($categories as $category)
                            <button type="button"
                                    data-category-id="{{ $category->id }}"
                                    onclick="filterByCategory({{ $category->id }})"
                                    class="category-tab flex-shrink-0 flex items-center justify-center
                                           gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold
                                           border transition-all whitespace-nowrap
                                           {{ $categoryId == $category->id ? 'active-tab' : 'inactive-tab' }}"
                                    style="{{ $categoryId == $category->id ? 'background: #ecbc42; color: #1e293b; border-color: #ecbc42;' : 'background: var(--bg-input); color: var(--text-4); border-color: var(--border-2);' }}">
                                {{ $category->name }}
                                @if($category->products_count > 0)
                                    <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[9px] font-bold"
                                          style="{{ $categoryId == $category->id ? 'background: rgba(30,41,77,0.2); color: #1e293b;' : 'background: rgba(236,188,66,0.15); color: #ecbc42;' }}">
                                        {{ $category->products_count }}
                                    </span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Product Grid --}}
                <div id="productGrid"
                     class="grid grid-cols-2 lg:grid-cols-3 gap-3">
                    @forelse($products as $product)
                        @include('admin.orders.partials.product-card', ['product' => $product])
                    @empty
                        <div class="col-span-full text-center py-16 rounded-xl border-2 border-dashed"
                             style="border-color: var(--border-2);">
                            <iconify-icon icon="mdi:cart-outline" class="text-5xl mb-3" style="color: var(--text-6);"></iconify-icon>
                            <p class="text-sm" style="color: var(--text-5);">Produk tidak ditemukan.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                @if($products->hasPages())
                    <div class="mt-4 flex justify-center">
                        @if ($products->hasMorePages())
                            <button type="button"
                                    id="loadMoreBtn"
                                    onclick="loadMore()"
                                    class="px-6 py-2.5 rounded-xl text-sm font-semibold
                                           transition-all active:scale-95"
                                    style="background: var(--bg-input); border: 1px solid var(--border-2); color: var(--text-3);"
                                    onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#ecbc42'"
                                    onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'">
                            Muat Lebih Banyak
                        </button>
                        @endif
                    </div>
                @endif
            </div>


            {{-- ============================================ --}}
            {{-- RIGHT: CART & CHECKOUT --}}
            {{-- ============================================ --}}
            <div class="xl:col-span-2 space-y-4">

                {{-- Cart Items --}}
                <div class="rounded-2xl border overflow-hidden"
                     style="background: var(--bg-card); border-color: var(--border-2);">

                    <div class="px-5 py-4 border-b flex items-center gap-2"
                         style="background: var(--bg-input); border-color: var(--border-2);">
                        <iconify-icon icon="mdi:cart-outline" class="text-[#ecbc42] text-base"></iconify-icon>
                        <h2 class="font-bold text-sm flex-1" style="color: var(--text-1);">Keranjang</h2>
                        <span class="inline-flex items-center justify-center min-w-[24px] h-6 px-2 rounded-full
                                     text-[11px] font-bold border"
                              style="background: rgba(236,188,66,0.1); border-color: rgba(236,188,66,0.3); color: #ecbc42;">
                            <span id="cartItemCount">0</span>
                        </span>
                    </div>

                    <div id="cartItems" class="p-4 max-h-[400px] overflow-y-auto">
                        <div class="text-center py-8">
                            <iconify-icon icon="mdi:cart-off" class="text-3xl mb-2" style="color: var(--text-6);"></iconify-icon>
                            <p class="text-xs" style="color: var(--text-5);">Belum ada produk ditambahkan</p>
                        </div>
                    </div>

                    {{-- Summary --}}
                    <div class="px-4 py-4 border-t space-y-3"
                         style="background: var(--bg-input); border-color: var(--border-2);">

                        <div class="flex justify-between items-center">
                            <span class="text-xs font-semibold" style="color: var(--text-4);">Subtotal</span>
                            <span class="text-sm font-bold font-mono" id="summarySubtotal" style="color: var(--text-1);">
                                Rp 0
                            </span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-xs font-semibold" style="color: var(--text-4);">Total Diskon Item</span>
                            <span class="text-sm font-bold font-mono text-emerald-400" id="summaryDiscount">
                                −Rp 0
                            </span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-xs font-semibold" style="color: var(--text-4);">Diskon Transaksi</span>
                            <span class="text-sm font-bold font-mono text-emerald-400" id="summaryTransactionDiscount">
                                −Rp 0
                            </span>
                        </div>

                        <div class="flex justify-between items-center pt-3 border-t"
                             style="border-color: var(--border-2);">
                            <span class="text-sm font-bold" style="color: var(--text-1);">Total</span>
                            <span class="text-xl font-bold font-mono" id="summaryTotal" style="color: #ecbc42;">
                                Rp 0
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Transaction Discount --}}
                <div class="rounded-2xl border overflow-hidden"
                     style="background: var(--bg-card); border-color: var(--border-2);">

                    <div class="px-5 py-4 border-b flex items-center gap-2"
                         style="background: var(--bg-input); border-color: var(--border-2);">
                        <iconify-icon icon="mdi:sale-outline" class="text-[#ecbc42] text-base"></iconify-icon>
                        <h2 class="font-bold text-sm flex-1" style="color: var(--text-1);">Diskon Transaksi</h2>
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded-full"
                              style="background: rgba(148,163,184,0.1); color: var(--text-4);">
                            Opsional
                        </span>
                    </div>

                    <div class="p-4 space-y-3">

                        {{-- Discount Type Toggle --}}
                        <div class="grid grid-cols-2 gap-2 p-1 rounded-xl"
                            style="background: var(--bg-input); border: 1px solid var(--border-2);"
                            id="discountTypeToggle">

                            {{-- Nominal --}}
                            <label class="discount-type-label relative flex items-center justify-center gap-2
                                        px-3 py-2.5 rounded-lg cursor-pointer
                                        text-xs font-bold transition-all duration-200
                                        active:scale-[0.98]">
                                <input type="radio"
                                    name="transaction_discount_type"
                                    value="nominal"
                                    class="sr-only discount-type-radio"
                                    checked
                                    onchange="toggleDiscountType()">
                                <iconify-icon icon="mdi:currency-idr" class="text-base"></iconify-icon>
                                <span>Nominal</span>
                            </label>

                            {{-- Persentase --}}
                            <label class="discount-type-label relative flex items-center justify-center gap-2
                                        px-3 py-2.5 rounded-lg cursor-pointer
                                        text-xs font-bold transition-all duration-200
                                        active:scale-[0.98]">
                                <input type="radio"
                                    name="transaction_discount_type"
                                    value="percentage"
                                    class="sr-only discount-type-radio"
                                    onchange="toggleDiscountType()">
                                <iconify-icon icon="mdi:percent" class="text-base"></iconify-icon>
                                <span>Persentase</span>
                            </label>
                        </div>

                        {{-- Discount Input --}}
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-mono"
                                  style="color: var(--text-5);">
                                <span id="tdPrefix">Rp</span>
                            </span>
                            <input type="text"
                                   id="transactionDiscount"
                                   inputmode="numeric"
                                   onfocus="this.select()"
                                   oninput="this.value = (isPercentageMode() ? this.value.replace(/[^0-9]/g, '') : formatNumberWithSeparator(this.value));
                                          updateCartTotals();"
                                   placeholder="0"
                                   class="w-full pl-12 pr-3 py-2.5 rounded-xl text-sm font-mono
                                          focus:outline-none transition-all"
                                   style="background: var(--bg-input);
                                          border: 1px solid var(--border-2);
                                          color: var(--text-1);">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs"
                                  id="tdSuffix"
                                  style="color: var(--text-5); display: none;">
                                %
                            </span>
                        </div>
                        <p class="text-[10px]" style="color: var(--text-5);">
                            Diskon tambahan untuk seluruh transaksi ini.
                        </p>

                        {{-- Calculated Discount Amount --}}
                        <div class="flex justify-between items-center pt-2 border-t"
                             style="border-color: var(--border-2);">
                            <span class="text-xs" style="color: var(--text-4);">Nominal Potongan</span>
                            <span class="text-sm font-bold font-mono text-emerald-400"
                                  id="transactionDiscountAmount">
                                Rp 0
                            </span>
                        </div>
                    </div>
                </div>


                {{-- Customer Info --}}
                <div class="rounded-2xl border overflow-hidden"
                     style="background: var(--bg-card); border-color: var(--border-2);">

                    <div class="px-5 py-4 border-b flex items-center gap-2"
                         style="background: var(--bg-input); border-color: var(--border-2);">
                        <iconify-icon icon="mdi:account-outline" class="text-[#ecbc42] text-base"></iconify-icon>
                        <h2 class="font-bold text-sm flex-1" style="color: var(--text-1);">Info Pelanggan</h2>
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded-full"
                              style="background: rgba(148,163,184,0.1); color: var(--text-4);">
                            Opsional
                        </span>
                    </div>

                    <div class="p-4 space-y-4">

                        <div>
                            <label class="form-label">
                                <iconify-icon icon="mdi:account-circle-outline" class="text-[#ecbc42]"></iconify-icon>
                                Nama Pelanggan
                            </label>
                            <input type="text"
                                   name="customer_name"
                                   class="form-input"
                                   placeholder="Walk-in Customer"
                                   value="Walk-in Customer">
                        </div>

                        <div>
                            <label class="form-label">
                                <iconify-icon icon="mdi:phone-outline" class="text-[#ecbc42]"></iconify-icon>
                                No. HP
                            </label>
                            <input type="text"
                                   name="customer_phone"
                                   class="form-input"
                                   placeholder="08123456789">
                        </div>

                        <div>
                            <label class="form-label">
                                <iconify-icon icon="mdi:map-marker-outline" class="text-[#ecbc42]"></iconify-icon>
                                Alamat
                            </label>
                            <textarea name="customer_address"
                                      rows="2"
                                      class="form-input resize-none"
                                      placeholder="Alamat pelanggan (opsional)"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Payment Method --}}
                <div class="rounded-2xl border overflow-hidden"
                     style="background: var(--bg-card); border-color: var(--border-2);">

                    <div class="px-5 py-4 border-b flex items-center gap-2"
                         style="background: var(--bg-input); border-color: var(--border-2);">
                        <iconify-icon icon="mdi:payment-outline" class="text-[#ecbc42] text-base"></iconify-icon>
                        <h2 class="font-bold text-sm" style="color: var(--text-1);">Metode Pembayaran</h2>
                    </div>

                    <div class="p-4">
                        <div class="grid grid-cols-3 gap-2">

                            @php
                                $paymentMethods = [
                                    ['value' => 'cash',     'label' => 'Tunai',    'icon' => 'mdi:cash'],
                                    ['value' => 'transfer', 'label' => 'Transfer', 'icon' => 'mdi:bank-outline'],
                                    ['value' => 'qris',     'label' => 'QRIS',     'icon' => 'mdi:qrcode'],
                                ];
                            @endphp

                            @foreach($paymentMethods as $pm)
                                <label class="payment-method-btn relative flex flex-col items-center justify-center gap-1.5
                                              px-3 py-3 rounded-xl cursor-pointer
                                              transition-all active:scale-95 border-2"
                                       style="background: var(--bg-input); border-color: var(--border-2);">
                                    <input type="radio"
                                           name="payment_method"
                                           value="{{ $pm['value'] }}"
                                           class="peer sr-only payment-method-radio"
                                           {{ $pm['value'] === 'cash' ? 'checked' : '' }}>
                                    <iconify-icon icon="{{ $pm['icon'] }}"
                                                  class="text-xl transition-colors peer-checked:text-[#ecbc42]">
                                    </iconify-icon>
                                    <span class="text-[11px] font-bold transition-colors peer-checked:text-[#ecbc42]">
                                        {{ $pm['label'] }}
                                    </span>
                                    <div class="pm-check absolute top-1 right-1 hidden">
                                        <div class="w-4 h-4 rounded-full flex items-center justify-center
                                                    bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]">
                                            <iconify-icon icon="mdi:check" class="text-slate-900 text-[10px]"></iconify-icon>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>


                {{-- Notes --}}
                <div class="rounded-2xl border overflow-hidden"
                     style="background: var(--bg-card); border-color: var(--border-2);">

                    <div class="px-5 py-4 border-b flex items-center gap-2"
                         style="background: var(--bg-input); border-color: var(--border-2);">
                        <iconify-icon icon="mdi:text-box-outline" class="text-[#ecbc42] text-base"></iconify-icon>
                        <h2 class="font-bold text-sm" style="color: var(--text-1);">Catatan</h2>
                    </div>

                    <div class="p-4">
                        <textarea name="notes"
                                  rows="2"
                                  class="form-input resize-none"
                                  placeholder="Catatan pesanan (opsional)"></textarea>
                    </div>
                </div>


                {{-- Hidden Print Type --}}
                <input type="hidden" name="print_type" id="printType" value="nota">

                {{-- Submit Buttons --}}
                <div class="flex gap-3">
                    <button type="submit"
                            onclick="document.getElementById('printType').value = 'nota'"
                            id="submitBtnNota"
                            disabled
                            class="flex-1 flex items-center justify-center gap-2 px-6 py-4 rounded-xl
                                   text-sm font-bold transition-all active:scale-[0.98]
                                   bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                                   text-slate-900
                                   shadow-lg shadow-amber-500/20
                                   hover:shadow-xl hover:shadow-amber-500/40
                                   disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:shadow-lg">
                        <iconify-icon icon="mdi:printer-check" class="text-lg"></iconify-icon>
                        Simpan & Cetak Nota
                    </button>

                    <button type="submit"
                            onclick="document.getElementById('printType').value = 'faktur'"
                            id="submitBtnFaktur"
                            disabled
                            class="flex-1 flex items-center justify-center gap-2 px-6 py-4 rounded-xl
                                   text-sm font-bold transition-all active:scale-[0.98]
                                   bg-gradient-to-r from-[#3b82f6] to-[#2563eb]
                                   text-white
                                   shadow-lg shadow-blue-500/20
                                   hover:shadow-xl hover:shadow-blue-500/40
                                   disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:shadow-lg">
                        <iconify-icon icon="mdi:file-document-multiple-outline" class="text-lg"></iconify-icon>
                        Simpan & Cetak Faktur
                    </button>
                </div>

            </div>
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
        font-size: 0.7rem;
        font-weight: 600;
        color: var(--text-3);
        margin-bottom: 0.4rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    /* Payment Method Selection */
    .payment-method-radio:checked ~ .pm-check {
        display: block;
    }
    .payment-method-btn:has(.payment-method-radio:checked) {
        border-color: #ecbc42 !important;
        background: rgba(236, 188, 66, 0.08) !important;
    }
    .payment-method-btn:has(.payment-method-radio:checked) iconify-icon,
    .payment-method-btn:has(.payment-method-radio:checked) span {
        color: #ecbc42 !important;
    }

    /* Toast */
    .toast {
        transform: translateX(calc(100% + 2rem));
        transition: transform 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        pointer-events: auto;
    }
    .toast.show { transform: translateX(0); }

    /* Cart item animation */
    .cart-item {
        animation: slideIn 0.3s ease-out;
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateX(-10px); }
        to { opacity: 1; transform: translateX(0); }
    }

    /* Hide number input spinner */
    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type="number"] {
        -moz-appearance: textfield;
    }
</style>


{{-- ============================================ --}}
{{-- SCRIPTS --}}
{{-- ============================================ --}}
@push('scripts')
<script>
// ============================================================
// OFFLINE ORDER SYSTEM
// ============================================================

let cart = [];

// ============================================================
// FORMAT RUPIAH
// ============================================================
function formatRupiah(number) {
    const num = Math.round(parseFloat(number) || 0);
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
}

function formatNumberInput(value) {
    // Hanya angka
    return String(value).replace(/\D/g, '');
}

function formatNumberWithSeparator(value) {
    const num = formatNumberInput(value);
    if (num === '') return '';
    return new Intl.NumberFormat('id-ID').format(num);
}

function parseFormattedNumber(value) {
    return parseFloat(String(value).replace(/\./g, '').replace(/,/g, '')) || 0;
}

// ============================================================
// TOAST
// ============================================================
function showToast(message, type) {
    const container = document.getElementById('toast-container');
    const colors = {
        success: { border: '#34d399', icon: 'mdi:check-circle-outline', bg: 'rgba(52,211,153,0.1)' },
        error:   { border: '#f87171', icon: 'mdi:alert-circle-outline', bg: 'rgba(248,113,113,0.1)' },
        warning: { border: '#fbbf24', icon: 'mdi:alert-outline', bg: 'rgba(251,191,36,0.1)' },
    };
    const c = colors[type] || colors.error;

    const toast = document.createElement('div');
    toast.className = 'toast rounded-xl border-l-4 shadow-2xl p-4 flex items-start gap-3';
    toast.style.cssText = `
        background: var(--bg-card);
        border-left-color: ${c.border};
        border-top: 1px solid var(--border-2);
        border-right: 1px solid var(--border-2);
        border-bottom: 1px solid var(--border-2);
        backdrop-filter: blur(8px);
    `;
    toast.innerHTML = `
        <iconify-icon icon="${c.icon}" class="text-xl flex-shrink-0" style="color: ${c.border};"></iconify-icon>
        <span class="text-sm font-medium" style="color: var(--text-1);">${message}</span>
    `;
    container.appendChild(toast);

    setTimeout(() => toast.classList.add('show'), 10);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 500);
    }, 4000);
}

// ============================================================
// CART OPERATIONS
// ============================================================
    function addToCart(productData) {
        const exists = cart.find(item =>
            item.product_id == productData.product_id &&
            item.variant_id == productData.variant_id
        );

        if (exists) {
            if (productData.stock !== null && productData.stock !== undefined && exists.quantity + 1 > productData.stock) {
                showToast(`Stok tidak mencukupi. Maksimum ${productData.stock} ${productData.variant_name ? 'varian ini' : ''}`, 'warning');
                return;
            }
            exists.quantity += 1;
            showToast(`${productData.product_name} - Qty ditambah`, 'success');
        } else {
            cart.push({
                product_id: productData.product_id,
                product_name: productData.product_name,
                price: parseFloat(productData.price) || 0,
                variant_id: productData.variant_id || null,
                variant_name: productData.variant_name || '',
                quantity: 1,
                discount: 0,
                stock: productData.stock || null
            });
            showToast(`${productData.product_name} ditambahkan`, 'success');
        }
        updateCartUI();
    }

function removeFromCart(index) {
    cart.splice(index, 1);
    updateCartUI();
}

    function updateItem(index, field, value) {
        if (field === 'price') {
            cart[index].price = parseFormattedNumber(value);
        } else if (field === 'quantity') {
            let qty = Math.max(1, parseInt(formatNumberInput(value)) || 1);
            const maxStock = cart[index].stock !== null && cart[index].stock !== undefined;
            if (maxStock && qty > cart[index].stock) {
                showToast(`Stok tidak mencukupi. Maksimum ${cart[index].stock}`, 'warning');
                qty = cart[index].stock;
            }
            cart[index].quantity = qty;
        } else if (field === 'discount') {
            cart[index].discount = parseFormattedNumber(value);
        }
        updateCartUI();
    }

function updateCartUI() {
    const cartEl = document.getElementById('cartItems');
    const submitBtnNota = document.getElementById('submitBtnNota');
    const submitBtnFaktur = document.getElementById('submitBtnFaktur');
    const itemsInput = document.getElementById('itemsInput');

    if (cart.length === 0) {
        cartEl.innerHTML = `
            <div class="text-center py-8">
                <iconify-icon icon="mdi:cart-off" class="text-3xl mb-2" style="color: var(--text-6);"></iconify-icon>
                <p class="text-xs" style="color: var(--text-5);">Belum ada produk ditambahkan</p>
            </div>
        `;
        submitBtnNota.disabled = true;
        submitBtnFaktur.disabled = true;
    } else {
        cartEl.innerHTML = cart.map((item, index) => {
            const itemSubtotal = (item.price * item.quantity) - ((item.discount || 0) * item.quantity);

            return `
                <div class="cart-item border-b pb-3 mb-3 last:border-0 last:mb-0 last:pb-0"
                     style="border-color: var(--border-1);">

                    {{-- Header --}}
                    <div class="flex justify-between items-start gap-2 mb-2">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm truncate" style="color: var(--text-1);">
                                ${item.product_name}
                            </p>
                            ${item.variant_name ? `
                                <p class="text-[10px] mt-0.5 flex items-center gap-1" style="color: var(--text-5);">
                                    <iconify-icon icon="mdi:tag-outline"></iconify-icon>
                                    ${item.variant_name}
                                </p>
                            ` : ''}
                        </div>
                        <button type="button"
                                onclick="removeFromCart(${index})"
                                class="flex items-center justify-center w-6 h-6 rounded-lg
                                       transition-all active:scale-95 flex-shrink-0"
                                style="color: #f87171; background: rgba(248,113,113,0.1);"
                                onmouseover="this.style.background='rgba(248,113,113,0.2)'"
                                onmouseout="this.style.background='rgba(248,113,113,0.1)'"
                                title="Hapus item">
                            <iconify-icon icon="mdi:close" class="text-sm"></iconify-icon>
                        </button>
                    </div>

                    {{-- Input Row --}}
                    <div class="grid grid-cols-12 gap-1.5 items-end">

                        {{-- Harga --}}
                        <div class="col-span-5">
                            <label class="block text-[9px] font-bold uppercase tracking-wider mb-1" style="color: var(--text-5);">
                                Harga
                            </label>
                            <div class="relative">
                                <span class="absolute left-2 top-1/2 -translate-y-1/2 text-[10px] font-mono pointer-events-none"
                                      style="color: var(--text-5);">Rp</span>
                                <input type="text"
                                       inputmode="numeric"
                                       onfocus="this.select()"
                                       oninput="this.value = formatNumberWithSeparator(this.value)"
                                       onchange="updateItem(${index}, 'price', this.value)"
                                       value="${new Intl.NumberFormat('id-ID').format(item.price)}"
                                       class="w-full pl-8 pr-1.5 py-1.5 rounded-lg text-xs text-right font-mono
                                              focus:outline-none transition-all"
                                       style="background: var(--bg-input);
                                              border: 1px solid var(--border-2);
                                              color: var(--text-1);">
                            </div>
                        </div>

                        {{-- Qty --}}
                        <div class="col-span-3">
                            <label class="block text-[9px] font-bold uppercase tracking-wider mb-1 text-center" style="color: var(--text-5);">
                                Qty
                            </label>
                            <input type="number"
                                   min="1"
                                   onfocus="this.select()"
                                   onchange="updateItem(${index}, 'quantity', this.value)"
                                   value="${item.quantity}"
                                   class="w-full px-1 py-1.5 rounded-lg text-xs text-center font-mono font-bold
                                          focus:outline-none transition-all"
                                   style="background: var(--bg-input);
                                          border: 1px solid var(--border-2);
                                          color: var(--text-1);">
                        </div>

                        {{-- Subtotal --}}
                        <div class="col-span-4">
                            <label class="block text-[9px] font-bold uppercase tracking-wider mb-1 text-right" style="color: var(--text-5);">
                                Subtotal
                            </label>
                            <div class="py-1.5 px-2 rounded-lg text-right font-mono text-xs font-bold"
                                 style="background: rgba(236,188,66,0.08); border: 1px solid rgba(236,188,66,0.2); color: #ecbc42;">
                                ${formatRupiah(itemSubtotal)}
                            </div>
                        </div>

                        {{-- Diskon --}}
                        <div class="col-span-6">
                            <label class="block text-[9px] font-bold uppercase tracking-wider mb-1" style="color: var(--text-5);">
                                Diskon / item
                            </label>
                            <div class="relative">
                                <span class="absolute left-2 top-1/2 -translate-y-1/2 text-[10px] font-mono pointer-events-none"
                                      style="color: var(--text-5);">Rp</span>
                                <input type="text"
                                       inputmode="numeric"
                                       onfocus="this.select()"
                                       oninput="this.value = formatNumberWithSeparator(this.value)"
                                       onchange="updateItem(${index}, 'discount', this.value)"
                                       value="${item.discount ? new Intl.NumberFormat('id-ID').format(item.discount) : ''}"
                                       placeholder="0"
                                       class="w-full pl-8 pr-1.5 py-1.5 rounded-lg text-xs text-right font-mono
                                              focus:outline-none transition-all"
                                       style="background: var(--bg-input);
                                              border: 1px solid var(--border-2);
                                              color: var(--text-1);">
                            </div>
                        </div>

                        {{-- Spacer --}}
                        <div class="col-span-6"></div>
                    </div>
                </div>
            `;
        }).join('');
        submitBtnNota.disabled = false;
    submitBtnFaktur.disabled = false;
    }

    itemsInput.value = JSON.stringify(cart);

    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const totalDiscount = cart.reduce((sum, item) => sum + ((item.discount || 0) * item.quantity), 0);
    const transactionDiscount = getTransactionDiscountValue(subtotal - totalDiscount);
    const total = subtotal - totalDiscount - transactionDiscount;

    document.getElementById('summarySubtotal').textContent = formatRupiah(subtotal);
    document.getElementById('summaryDiscount').textContent = '−' + formatRupiah(totalDiscount);
    document.getElementById('summaryTransactionDiscount').textContent = transactionDiscount > 0 ? '−' + formatRupiah(transactionDiscount) : '−Rp 0';
    document.getElementById('summaryTotal').textContent = formatRupiah(total);

    const itemCount = cart.reduce((sum, item) => sum + item.quantity, 0);
    document.getElementById('cartItemCount').textContent = itemCount;
}

// ============================================================
// DISCOUNT TYPE HELPERS
// ============================================================
function isPercentageMode() {
    const radios = document.querySelectorAll('input[name="transaction_discount_type"]');
    for (const r of radios) {
        if (r.checked && r.value === 'percentage') return true;
    }
    return false;
}

function getTransactionDiscountValue(baseAmount) {
    const input = document.getElementById('transactionDiscount');
    if (!input) return 0;
    const raw = input.value.trim();
    if (!raw) return 0;

    const num = parseFloat(raw.replace(/[^0-9]/g, '')) || 0;
    if (num <= 0) return 0;

    if (isPercentageMode()) {
        // Percentage: cap at 100%
        const pct = Math.min(num, 100);
        return baseAmount * pct / 100;
    }
    // Nominal: cap at base amount
    return Math.min(num, baseAmount);
}

function toggleDiscountType() {
    const input = document.getElementById('transactionDiscount');
    const tdPrefix = document.getElementById('tdPrefix');
    const tdSuffix = document.getElementById('tdSuffix');

    // 🔥 Fallback: Update class untuk active state
    document.querySelectorAll('.discount-type-label').forEach(function(label) {
        const radio = label.querySelector('.discount-type-radio');
        if (radio && radio.checked) {
            label.classList.add('active');
            label.style.background = 'linear-gradient(135deg, #FDDD57 0%, #ecbc42 100%)';
            label.style.color = '#422006';
            label.style.borderColor = '#ecbc42';
            label.style.boxShadow = '0 4px 12px rgba(236, 188, 66, 0.25)';
            label.style.transform = 'translateY(-1px)';
            const icon = label.querySelector('iconify-icon');
            if (icon) icon.style.color = '#422006';
        } else {
            label.classList.remove('active');
            label.style.background = 'transparent';
            label.style.color = 'var(--text-4)';
            label.style.borderColor = 'transparent';
            label.style.boxShadow = 'none';
            label.style.transform = 'translateY(0)';
            const icon = label.querySelector('iconify-icon');
            if (icon) icon.style.color = '';
        }
    });

    if (isPercentageMode()) {
        tdPrefix.textContent = '';
        tdSuffix.style.display = 'inline';
        input.value = '';
        input.placeholder = '0';
        input.oninput = function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (parseInt(this.value) > 100) this.value = '100';
            updateCartTotals();
        };
    } else {
        tdPrefix.textContent = 'Rp';
        tdSuffix.style.display = 'none';
        input.value = '';
        input.placeholder = '0';
        input.oninput = function() {
            this.value = formatNumberWithSeparator(this.value);
            updateCartTotals();
        };
    }
    updateCartTotals();
}

// ============================================================
// UPDATE CART TOTALS (when transaction discount changes)
// ============================================================
function updateCartTotals() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const totalDiscount = cart.reduce((sum, item) => sum + ((item.discount || 0) * item.quantity), 0);
    const discountableBase = subtotal - totalDiscount;
    const transactionDiscount = getTransactionDiscountValue(discountableBase);
    const total = discountableBase - transactionDiscount;

    document.getElementById('summarySubtotal').textContent = formatRupiah(subtotal);
    document.getElementById('summaryDiscount').textContent = '−' + formatRupiah(totalDiscount);
    document.getElementById('summaryTransactionDiscount').textContent = transactionDiscount > 0 ? '−' + formatRupiah(transactionDiscount) : '−Rp 0';
    document.getElementById('summaryTotal').textContent = formatRupiah(total);

    // Update calculated amount display
    const amountEl = document.getElementById('transactionDiscountAmount');
    if (amountEl) {
        amountEl.textContent = transactionDiscount > 0 ? '−' + formatRupiah(transactionDiscount) : 'Rp 0';
    }
}

// ============================================================
// PRODUCT CARD CLICK HANDLING
// ============================================================
document.addEventListener('click', function(e) {
    const variantOpt = e.target.closest('.variant-option');
    if (variantOpt) {
        try {
            const data = JSON.parse(variantOpt.dataset.variant);
            addToCart(data);
        } catch (err) {
            console.error('Invalid variant data', err);
        }
        return;
    }

    const productOpt = e.target.closest('.product-option');
    if (productOpt) {
        try {
            const data = JSON.parse(productOpt.dataset.product);
            addToCart(data);
        } catch (err) {
            console.error('Invalid product data', err);
        }
        return;
    }
});

// ============================================================
// PAYMENT METHOD UI
// ============================================================
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('payment-method-radio')) {
        document.querySelectorAll('.payment-method-btn').forEach(function(btn) {
            const radio = btn.querySelector('.payment-method-radio');
            const check = btn.querySelector('.pm-check');
            if (radio && radio.checked) {
                btn.style.borderColor = '#ecbc42';
                btn.style.background = 'rgba(236,188,66,0.08)';
                if (check) check.classList.remove('hidden');
            } else {
                btn.style.borderColor = 'var(--border-2)';
                btn.style.background = 'var(--bg-input)';
                if (check) check.classList.add('hidden');
            }
        });
    }
});

// Initial payment method state
document.addEventListener('DOMContentLoaded', function() {
    const checkedRadio = document.querySelector('.payment-method-radio:checked');
    if (checkedRadio) {
        const btn = checkedRadio.closest('.payment-method-btn');
        const check = btn.querySelector('.pm-check');
        btn.style.borderColor = '#ecbc42';
        btn.style.background = 'rgba(236,188,66,0.08)';
        if (check) check.classList.remove('hidden');
    }
});

// ============================================================
// CATEGORY FILTER
// ============================================================
let currentCategoryId = {{ $categoryId ?? 'null' }};

function filterByCategory(categoryId) {
    currentCategoryId = categoryId;

    // Update tab styles
    document.querySelectorAll('.category-tab').forEach(function(tab) {
        const tabCatId = tab.dataset.categoryId;
        if (String(tabCatId) === String(categoryId)) {
            tab.classList.remove('inactive-tab');
            tab.classList.add('active-tab');
            tab.style.background = '#ecbc42';
            tab.style.color = '#1e293b';
            tab.style.borderColor = '#ecbc42';
        } else {
            tab.classList.remove('active-tab');
            tab.classList.add('inactive-tab');
            tab.style.background = 'var(--bg-input)';
            tab.style.color = 'var(--text-4)';
            tab.style.borderColor = 'var(--border-2)';
        }
    });

    // Reset search and refetch
    productSearch.value = '';
    fetchProducts(1);
}

function fetchProducts(page, append) {
    const params = new URLSearchParams();
    params.set('page', page);

    if (productSearch.value.trim()) {
        params.set('search', productSearch.value.trim());
    }
    if (currentCategoryId) {
        params.set('category_id', currentCategoryId);
    }

    const loading = document.getElementById('searchLoading');
    if (loading) loading.classList.remove('hidden');

    fetch('{{ route('admin.orders.offline') }}' + '?' + params.toString(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
    })
    .then(r => r.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newGrid = doc.getElementById('productGrid');
        if (newGrid) {
            const gridEl = document.getElementById('productGrid');
            if (append) {
                gridEl.insertAdjacentHTML('beforeend', newGrid.innerHTML);
            } else {
                gridEl.innerHTML = newGrid.innerHTML;
                currentPage = 1;
            }

            // Remove load more button if no more pages
            const loadMoreDoc = doc.getElementById('loadMoreBtn');
            const loadMoreCurrent = document.getElementById('loadMoreBtn');
            if (loadMoreCurrent) {
                if (!loadMoreDoc) {
                    loadMoreCurrent.remove();
                }
            }
        }

        // Preserve category active state
        const newCatTabs = doc.getElementById('categoryTabs');
        if (newCatTabs) {
            document.getElementById('categoryTabs').innerHTML = newCatTabs.innerHTML;
            // Re-apply active state
            document.querySelectorAll('.category-tab').forEach(function(tab) {
                const tabCatId = tab.dataset.categoryId;
                if (String(tabCatId) === String(currentCategoryId || '')) {
                    tab.classList.remove('inactive-tab');
                    tab.classList.add('active-tab');
                    tab.style.background = '#ecbc42';
                    tab.style.color = '#1e293b';
                    tab.style.borderColor = '#ecbc42';
                } else {
                    tab.classList.remove('active-tab');
                    tab.classList.add('inactive-tab');
                    tab.style.background = 'var(--bg-input)';
                    tab.style.color = 'var(--text-4)';
                    tab.style.borderColor = 'var(--border-2)';
                }
            });
        }
    })
    .catch(err => {
        console.error('Fetch failed:', err);
        showToast('Gagal memuat produk', 'error');
    })
    .finally(() => {
        if (loading) loading.classList.add('hidden');
    });
}

// ============================================================
// PRODUCT SEARCH (AJAX)
// ============================================================
const productSearch = document.getElementById('productSearch');
let searchTimeout;

if (productSearch) {
    productSearch.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetchProducts(1);
        }, 300);
    });
}

// ============================================================
// LOAD MORE (Pagination)
// ============================================================
let currentPage = 1;

function loadMore() {
    currentPage += 1;
    fetchProducts(currentPage, true);
}

// ============================================================
// FORM SUBMIT
// ============================================================
const orderForm = document.getElementById('orderForm');
if (orderForm) {
    orderForm.addEventListener('submit', function(e) {
        if (cart.length === 0) {
            e.preventDefault();
            showToast('Tambahkan minimal 1 produk ke keranjang.', 'error');
            return;
        }
        document.getElementById('itemsInput').value = JSON.stringify(cart);

        // Compute transaction discount and send type + value
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const itemDiscount = cart.reduce((sum, item) => sum + ((item.discount || 0) * item.quantity), 0);
        const discountableBase = subtotal - itemDiscount;
        const tdInput = getOrCreateHiddenInput('transaction_discount', 'transactionDiscountInput');
        const tdTypeInput = getOrCreateHiddenInput('transaction_discount_type', 'transactionDiscountTypeInput');

        if (isPercentageMode()) {
            const pct = parseInt(document.getElementById('transactionDiscount').value.replace(/[^0-9]/g, '')) || 0;
            tdInput.value = Math.min(pct, 100);
            tdTypeInput.value = 'percentage';
            const tdAmountInput = getOrCreateHiddenInput('transaction_discount_amount', 'transactionDiscountAmountInput');
            tdAmountInput.value = (discountableBase * Math.min(pct, 100) / 100).toFixed(2);
        } else {
            const nominal = parseFormattedNumber(document.getElementById('transactionDiscount').value) || 0;
            tdInput.value = nominal;
            tdTypeInput.value = 'nominal';
            const tdAmountInput = getOrCreateHiddenInput('transaction_discount_amount', 'transactionDiscountAmountInput');
            tdAmountInput.value = Math.min(nominal, discountableBase).toFixed(2);
        }
    });
}

function getOrCreateHiddenInput(name, id) {
    let el = document.getElementById(id);
    if (!el) {
        el = document.createElement('input');
        el.type = 'hidden';
        el.id = id;
        el.name = name;
        document.getElementById('orderForm').appendChild(el);
    } else {
        el.name = name;
    }
    return el;
}

// ============================================================
// INIT
// ============================================================
updateCartUI();
</script>
@endpush

@endsection