@extends('layouts.admin')

@section('content')

<div class="w-full space-y-6">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div class="min-w-0">
            <h1 class="text-2xl font-bold flex items-center gap-2.5" style="color: var(--text-1)">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl
                             bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                             shadow-lg shadow-amber-500/20 flex-shrink-0">
                    <iconify-icon icon="mdi:package-variant" class="text-slate-900 text-2xl"></iconify-icon>
                </span>
                Produk
            </h1>
            <p class="mt-1.5 ml-12 text-sm" style="color: var(--text-5)">
                Kelola semua produk yang tersedia di toko.
            </p>
        </div>

        <div class="flex items-center gap-2 sm:gap-3 flex-wrap">
            {{-- BULK DELETE BUTTON --}}
            <button
                id="bulk-delete-btn"
                type="button"
                class="inline-flex items-center justify-center gap-2
                       rounded-lg px-4 py-2.5
                       text-sm font-semibold
                       bg-red-500/10 border border-red-500/30 text-red-400
                       transition-all
                       hover:bg-red-500/20 hover:border-red-500/50
                       disabled:opacity-40 disabled:cursor-not-allowed
                       active:scale-95"
                disabled
                onclick="confirmBulkDelete()"
            >
                <iconify-icon icon="mdi:trash-can-outline" class="text-lg"></iconify-icon>
                <span id="bulk-delete-text">Hapus Terpilih</span>
                <span id="bulk-delete-count"
                      class="ml-1 rounded-full px-2 py-0.5 text-xs
                             bg-red-500/20 text-red-300
                             font-bold">0</span>
            </button>

            <a href="{{ route('admin.products.create') }}"
               class="inline-flex items-center justify-center gap-2
                      rounded-lg px-5 py-2.5
                      text-sm font-bold
                      bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                      text-slate-900
                      shadow-lg shadow-amber-500/20
                      hover:shadow-xl hover:shadow-amber-500/40
                      hover:-translate-y-0.5
                      transition-all active:scale-95 active:translate-y-0">
                <iconify-icon icon="mdi:plus-circle" class="text-lg"></iconify-icon>
                Tambah Produk
            </a>
        </div>

    </div>

    {{-- ========================================================= --}}
    {{-- SUCCESS / ERROR MESSAGE --}}
    {{-- ========================================================= --}}
    @if (session('success'))
        <div id="success-message" class="flex items-start gap-3 rounded-xl px-4 py-3
                                         bg-emerald-500/10 border border-emerald-500/30
                                         text-emerald-400">
            <iconify-icon icon="mdi:check-circle-outline" class="text-xl flex-shrink-0 mt-0.5"></iconify-icon>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold">Berhasil!</p>
                <p class="text-xs opacity-80 mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div id="error-message" class="flex items-start gap-3 rounded-xl px-4 py-3
                                       bg-red-500/10 border border-red-500/30
                                       text-red-400">
            <iconify-icon icon="mdi:alert-circle-outline" class="text-xl flex-shrink-0 mt-0.5"></iconify-icon>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold">Terjadi Kesalahan</p>
                <p class="text-xs opacity-80 mt-0.5">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- ========================================================= --}}
    {{-- STATS BAR --}}
    {{-- ========================================================= --}}
    @if($products->total() > 0)
        <div class="flex flex-wrap items-center justify-between gap-4 px-4 py-3 rounded-xl border"
             style="background: var(--bg-card); border-color: var(--border-2)">

            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg
                            bg-[#ecbc42]/10 border border-[#ecbc42]/30">
                    <iconify-icon icon="mdi:package-variant-closed" class="text-[#ecbc42] text-lg"></iconify-icon>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--text-5)">
                        Total Produk
                    </p>
                    <p class="text-lg font-bold" style="color: var(--text-1)">
                        {{ $products->total() }}
                    </p>
                </div>
            </div>

            <div class="hidden sm:flex items-center gap-4 text-xs" style="color: var(--text-5)">
                <span class="flex items-center gap-1.5">
                    <iconify-icon icon="mdi:information-outline"></iconify-icon>
                    Halaman {{ $products->currentPage() }} dari {{ $products->lastPage() }}
                </span>
            </div>
        </div>
    @endif

    {{-- ========================================================= --}}
    {{-- TABLE --}}
    {{-- ========================================================= --}}
    <div class="overflow-hidden rounded-xl border"
         style="background: var(--bg-card); border-color: var(--border-2)">

        <div class="overflow-x-auto">
            <table class="min-w-full">

                {{-- ================================================= --}}
                {{-- TABLE HEADER --}}
                {{-- ================================================= --}}
                <thead class="border-b" style="background: var(--bg-input); border-color: var(--border-2)">
                    <tr>
                        {{-- CHECKBOX HEADER --}}
                        <th scope="col" class="px-4 py-4 text-center w-12">
                            <input type="checkbox" id="select-all"
                                   class="w-4 h-4 rounded cursor-pointer"
                                   style="accent-color: #ecbc42">
                        </th>

                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Produk
                        </th>

                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Kategori
                        </th>

                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-wider hidden md:table-cell" style="color: var(--text-5)">
                            Varian
                        </th>

                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Harga
                        </th>

                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Stok
                        </th>

                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-wider w-28" style="color: var(--text-5)">
                            Status
                        </th>

                        <th scope="col" class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-wider w-32" style="color: var(--text-5)">
                            Aksi
                        </th>
                    </tr>
                </thead>

                {{-- ================================================= --}}
                {{-- TABLE BODY --}}
                {{-- ================================================= --}}
                <tbody id="products-table-body">
                    @forelse ($products as $product)
                        <tr class="transition-colors border-b last:border-0"
                            data-product-id="{{ $product->id }}"
                            style="border-color: var(--border-1)"
                            onmouseover="this.style.background='var(--bg-hover)'"
                            onmouseout="this.style.background='transparent'">

                            {{-- CHECKBOX --}}
                            <td class="px-4 py-4 text-center">
                                <input type="checkbox"
                                       class="product-checkbox w-4 h-4 rounded cursor-pointer"
                                       style="accent-color: #ecbc42"
                                       data-product-id="{{ $product->id }}"
                                       data-product-name="{{ $product->name }}">
                            </td>

                            {{-- ===================================== --}}
                            {{-- PRODUK --}}
                            {{-- ===================================== --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    {{-- GAMBAR --}}
                                    <div class="w-12 h-12 flex-shrink-0 overflow-hidden rounded-lg border"
                                         style="background: var(--bg-input); border-color: var(--border-2)">
                                        @if ($product->images->isNotEmpty())
                                            <img src="{{ asset('storage/' . $product->images->first()->image) }}"
                                                 alt="{{ $product->name }}"
                                                 class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center">
                                                <iconify-icon icon="mdi:image-off-outline" class="text-xl" style="color: var(--text-6)"></iconify-icon>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- NAMA --}}
                                    <div class="min-w-0">
                                        <div class="font-semibold truncate max-w-[240px]" style="color: var(--text-1)">
                                            {{ $product->name }}
                                        </div>
                                        <code class="inline-block mt-1 text-[10px] font-mono px-1.5 py-0.5 rounded border"
                                              style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-4)">
                                            {{ $product->slug }}
                                        </code>
                                    </div>
                                </div>
                            </td>

                            {{-- ===================================== --}}
                            {{-- KATEGORI --}}
                            {{-- ===================================== --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($product->category)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                                 text-xs font-semibold
                                                 bg-[#ecbc42]/10 border border-[#967010]/30 text-[#967010]">
                                        <iconify-icon icon="mdi:folder-outline" class="text-xs"></iconify-icon>
                                        {{ $product->category->name }}
                                    </span>
                                @else
                                    <span class="text-xs italic" style="color: var(--text-5)">-</span>
                                @endif
                            </td>

                            {{-- ===================================== --}}
                            {{-- VARIAN --}}
                            {{-- ===================================== --}}
                            <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold"
                                      style="color: var(--text-3)">
                                    <iconify-icon icon="mdi:tag-multiple-outline" class="text-[#ecbc42]"></iconify-icon>
                                    {{ $product->variants->count() }} varian
                                </span>
                            </td>

                            {{-- ===================================== --}}
                            {{-- HARGA --}}
                            {{-- ===================================== --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $minPrice = $product->variants->min('price');
                                    $maxPrice = $product->variants->max('price');
                                @endphp

                                @if ($minPrice !== null && $maxPrice !== null)
                                    @if ($minPrice == $maxPrice)
                                        <span class="text-sm font-bold" style="color: var(--text-1)">
                                            Rp {{ number_format($minPrice, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-sm font-bold" style="color: var(--text-1)">
                                            Rp {{ number_format($minPrice, 0, ',', '.') }}
                                        </span>
                                        <span class="text-xs" style="color: var(--text-5)">s/d</span>
                                        <span class="text-sm font-bold" style="color: var(--text-1)">
                                            Rp {{ number_format($maxPrice, 0, ',', '.') }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-xs italic" style="color: var(--text-5)">Belum ada harga</span>
                                @endif
                            </td>

                            {{-- ===================================== --}}
                            {{-- STOK --}}
                            {{-- ===================================== --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $totalStock = $product->variants->sum('stock');
                                    $status = $product->stock_status ?? 'in_stock';
                                    $label = $product->stock_status_label ?? 'Aman';
                                    $color = $product->stock_status_color ?? 'green';
                                @endphp

                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-bold" style="color: var(--text-1)">
                                        {{ number_format($totalStock, 0, ',', '.') }} pcs
                                    </span>

                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold
                                        @if($color == 'red') text-red-400
                                        @elseif($color == 'yellow') text-amber-400
                                        @elseif($color == 'green') text-emerald-400
                                        @else text-slate-400 @endif">
                                        <span class="inline-block w-1.5 h-1.5 rounded-full
                                            @if($color == 'red') bg-red-400
                                            @elseif($color == 'yellow') bg-amber-400
                                            @elseif($color == 'green') bg-emerald-400
                                            @else bg-slate-400 @endif">
                                        </span>
                                        {{ $label }}
                                    </span>

                                    @if($product->minimum_stock || $product->restock_threshold)
                                        <span class="text-[10px] font-mono" style="color: var(--text-6)">
                                            Min: {{ $product->minimum_stock ?? 5 }} · Restock: {{ $product->restock_threshold ?? 10 }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- ===================================== --}}
                            {{-- STATUS --}}
                            {{-- ===================================== --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($product->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                                 text-[11px] font-bold
                                                 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                                 text-[11px] font-bold border"
                                          style="background: var(--bg-hover); border-color: var(--border-3); color: var(--text-5)">
                                        <span class="w-1.5 h-1.5 rounded-full" style="background: var(--text-5)"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            {{-- ===================================== --}}
                            {{-- AKSI --}}
                            {{-- ===================================== --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">

                                    {{-- EDIT --}}
                                    <a href="{{ route('admin.products.edit', $product) }}"
                                       class="inline-flex items-center justify-center w-9 h-9 rounded-lg border
                                              transition-all active:scale-95"
                                       style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)"
                                       onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                                       onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'"
                                       title="Edit Produk">
                                        <iconify-icon icon="mdi:pencil-outline" class="text-base"></iconify-icon>
                                    </a>

                                    {{-- DELETE --}}
                                    <form action="{{ route('admin.products.destroy', $product) }}"
                                          method="POST"
                                          onsubmit="return confirm('Apakah kamu yakin ingin menghapus produk ini?')"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-9 h-9 rounded-lg
                                                       bg-red-500/5 border border-red-500/20
                                                       text-red-400
                                                       hover:bg-red-500/15 hover:border-red-500/40
                                                       transition-all active:scale-95"
                                                title="Hapus Produk">
                                            <iconify-icon icon="mdi:trash-can-outline" class="text-base"></iconify-icon>
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                    @empty
                        {{-- ========================================= --}}
                        {{-- EMPTY STATE --}}
                        {{-- ========================================= --}}
                        <tr>
                            <td colspan="8" class="px-6 py-20">
                                <div class="flex flex-col items-center justify-center text-center max-w-sm mx-auto">
                                    <div class="relative mb-5">
                                        <div class="w-24 h-24 rounded-full flex items-center justify-center border"
                                             style="background: var(--bg-input); border-color: var(--border-2)">
                                            <iconify-icon icon="mdi:package-variant-closed" class="text-5xl text-[#ecbc42] opacity-60"></iconify-icon>
                                        </div>
                                        <span class="absolute -bottom-1 -right-1 w-8 h-8 rounded-full
                                                     bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                                                     flex items-center justify-center
                                                     shadow-lg shadow-amber-500/30">
                                            <iconify-icon icon="mdi:plus" class="text-slate-900 text-lg font-bold"></iconify-icon>
                                        </span>
                                    </div>

                                    <h3 class="text-lg font-bold mb-1.5" style="color: var(--text-1)">
                                        Belum Ada Produk
                                    </h3>
                                    <p class="text-sm mb-6" style="color: var(--text-5)">
                                        Mulai tambahkan produk pertama ke toko kamu.
                                    </p>

                                    <a href="{{ route('admin.products.create') }}"
                                       class="inline-flex items-center gap-2 px-5 py-3 rounded-lg
                                              bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                                              text-slate-900 text-sm font-bold
                                              shadow-lg shadow-amber-500/20
                                              hover:shadow-xl hover:shadow-amber-500/40
                                              hover:-translate-y-0.5
                                              transition-all active:scale-95 active:translate-y-0">
                                        <iconify-icon icon="mdi:plus-circle" class="text-lg"></iconify-icon>
                                        Tambah Produk Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        {{-- ========================================================= --}}
        {{-- PAGINATION --}}
        {{-- ========================================================= --}}
        @if ($products->hasPages())
            <div class="border-t px-6 py-4"
                 style="border-color: var(--border-2); background: var(--bg-input)">
                {{ $products->links() }}
            </div>
        @endif

    </div>

</div>

{{-- ========================================================= --}}
{{-- JAVASCRIPT - BULK ACTIONS --}}
{{-- ========================================================= --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const productCheckboxes = document.querySelectorAll('.product-checkbox');
    const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
    const bulkDeleteCount = document.getElementById('bulk-delete-count');

    // 🔥 UPDATE BULK DELETE BUTTON STATE
    function updateBulkDeleteButton() {
        const checked = document.querySelectorAll('.product-checkbox:checked');
        const count = checked.length;

        if (bulkDeleteBtn) {
            bulkDeleteBtn.disabled = count === 0;
        }
        if (bulkDeleteCount) {
            bulkDeleteCount.textContent = count;
        }
    }

    // 🔥 SELECT ALL
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            productCheckboxes.forEach(function(checkbox) {
                checkbox.checked = selectAllCheckbox.checked;
            });
            updateBulkDeleteButton();
        });
    }

    // 🔥 INDIVIDUAL CHECKBOX
    productCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(productCheckboxes).every(cb => cb.checked);
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = allChecked;
            }
            updateBulkDeleteButton();
        });
    });

    updateBulkDeleteButton();

    // 🔥 SHOW ALERT
    function showAlert(message, type = 'info') {
        const oldAlert = document.querySelector('.custom-alert');
        if (oldAlert) oldAlert.remove();

        const colors = {
            success: 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400',
            error:   'bg-red-500/10 border-red-500/30 text-red-400',
            warning: 'bg-amber-500/10 border-amber-500/30 text-amber-400',
            info:    'bg-blue-500/10 border-blue-500/30 text-blue-400'
        };

        const icons = {
            success: 'mdi:check-circle-outline',
            error:   'mdi:alert-circle-outline',
            warning: 'mdi:alert-outline',
            info:    'mdi:information-outline'
        };

        const alert = document.createElement('div');
        alert.className = `custom-alert flex items-center gap-3 rounded-xl border px-4 py-3 text-sm ${colors[type] || colors.info}`;
        alert.innerHTML = `
            <iconify-icon icon="${icons[type] || icons.info}" class="text-xl flex-shrink-0"></iconify-icon>
            <span class="flex-1">${message}</span>
            <button class="text-lg leading-none hover:opacity-70" onclick="this.parentElement.remove()">&times;</button>
        `;

        const container = document.querySelector('.space-y-6');
        if (container) {
            container.prepend(alert);
        }

        setTimeout(() => {
            if (alert.parentNode) alert.remove();
        }, 5000);
    }

    // 🔥 BULK DELETE
    window.confirmBulkDelete = function() {
        const checked = document.querySelectorAll('.product-checkbox:checked');
        const count = checked.length;

        if (count === 0) {
            showAlert('Pilih minimal 1 produk untuk dihapus.', 'warning');
            return;
        }

        const productNames = Array.from(checked).map(cb => cb.dataset.productName);
        const productIds = Array.from(checked).map(cb => parseInt(cb.dataset.productId));

        if (!confirm(`Apakah kamu yakin ingin menghapus ${count} produk berikut?\n\n• ${productNames.join('\n• ')}\n\nSemua varian dan gambar produk juga akan dihapus.`)) {
            return;
        }

        bulkDeleteBtn.disabled = true;
        bulkDeleteBtn.innerHTML = `
            <iconify-icon icon="mdi:loading" class="animate-spin text-lg"></iconify-icon>
            Menghapus...
        `;

        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

        if (!csrfToken) {
            resetBulkDeleteButton();
            showAlert('Token keamanan tidak ditemukan. Silakan muat ulang halaman.', 'error');
            return;
        }

        fetch('{{ route('admin.products.bulk-destroy') }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ product_ids: productIds })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.message || 'Server error: ' + response.status);
                });
            }
            return response.json();
        })
        .then(data => {
            resetBulkDeleteButton();

            if (data.success) {
                showAlert(data.message || `Berhasil menghapus ${data.deleted || count} produk.`, 'success');
                setTimeout(() => location.reload(), 2000);
            } else {
                showAlert(data.message || 'Gagal menghapus produk.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            resetBulkDeleteButton();
            showAlert('Terjadi kesalahan: ' + error.message, 'error');
        });
    };

    // 🔥 RESET BULK DELETE BUTTON
    function resetBulkDeleteButton() {
        const checked = document.querySelectorAll('.product-checkbox:checked');
        const count = checked.length;

        bulkDeleteBtn.innerHTML = `
            <iconify-icon icon="mdi:trash-can-outline" class="text-lg"></iconify-icon>
            <span id="bulk-delete-text">Hapus Terpilih</span>
            <span id="bulk-delete-count" class="ml-1 rounded-full px-2 py-0.5 text-xs bg-red-500/20 text-red-300 font-bold">${count}</span>
        `;

        bulkDeleteBtn.disabled = count === 0;
    }
});
</script>

@endsection