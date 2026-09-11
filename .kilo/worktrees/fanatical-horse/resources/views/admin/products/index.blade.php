@extends('layouts.admin')

@section('content')

<div class="space-y-6">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Produk
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Kelola semua produk yang tersedia di toko.
            </p>
        </div>

        <div class="flex items-center gap-3">
            {{-- 🔥 BULK DELETE BUTTON --}}
            <button
                id="bulk-delete-btn"
                type="button"
                class="inline-flex items-center justify-center
                       rounded-lg bg-red-600
                       px-4 py-2.5
                       text-sm font-semibold
                       text-white
                       transition
                       hover:bg-red-700
                       disabled:opacity-50 disabled:cursor-not-allowed"
                disabled
                onclick="confirmBulkDelete()"
            >
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                <span id="bulk-delete-text">Hapus Terpilih</span>
                <span id="bulk-delete-count" class="ml-2 rounded-full bg-white/20 px-2 py-0.5 text-xs">0</span>
            </button>

            <a
                href="{{ route('admin.products.create') }}"
                class="inline-flex items-center justify-center
                       rounded-lg bg-blue-600
                       px-4 py-2.5
                       text-sm font-semibold
                       text-white
                       transition
                       hover:bg-blue-700"
            >
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Produk
            </a>
        </div>

    </div>

    {{-- ========================================================= --}}
    {{-- SUCCESS MESSAGE --}}
    {{-- ========================================================= --}}

    @if (session('success'))
        <div id="success-message" class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div id="error-message" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- ========================================================= --}}
    {{-- TABLE --}}
    {{-- ========================================================= --}}

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        {{-- TABLE WRAPPER --}}
        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-200">

                {{-- ================================================= --}}
                {{-- TABLE HEADER --}}
                {{-- ================================================= --}}

                <thead class="bg-gray-50">
                    <tr>
                        {{-- 🔥 CHECKBOX HEADER --}}
                        <th scope="col" class="px-4 py-4 text-center">
                            <input
                                type="checkbox"
                                id="select-all"
                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            >
                        </th>

                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Produk
                        </th>

                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Kategori
                        </th>

                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Varian
                        </th>

                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Harga
                        </th>

                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Stok
                        </th>

                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Status
                        </th>

                        <th scope="col" class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Aksi
                        </th>
                    </tr>
                </thead>

                {{-- ================================================= --}}
                {{-- TABLE BODY --}}
                {{-- ================================================= --}}

                <tbody class="divide-y divide-gray-100 bg-white" id="products-table-body">

                    @forelse ($products as $product)

                        <tr class="transition hover:bg-gray-50" data-product-id="{{ $product->id }}">

                            {{-- 🔥 CHECKBOX --}}
                            <td class="whitespace-nowrap px-4 py-4 text-center">
                                <input
                                    type="checkbox"
                                    class="product-checkbox h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    data-product-id="{{ $product->id }}"
                                    data-product-name="{{ $product->name }}"
                                >
                            </td>

                            {{-- ===================================== --}}
                            {{-- PRODUK --}}
                            {{-- ===================================== --}}

                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="flex items-center gap-4">
                                    {{-- GAMBAR --}}
                                    <div class="h-14 w-14 flex-shrink-0 overflow-hidden rounded-lg bg-gray-100">
                                        @if ($product->images->isNotEmpty())
                                            <img
                                                src="{{ asset('storage/' . $product->images->first()->image) }}"
                                                alt="{{ $product->name }}"
                                                class="h-full w-full object-cover"
                                            >
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-gray-400">
                                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 016.828 0L20 16m-2-2l1.586-1.586a2 2 0 011.414-.586M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- NAMA --}}
                                    <div>
                                        <div class="font-semibold text-gray-900">
                                            {{ $product->name }}
                                        </div>
                                        <div class="mt-1 text-xs text-gray-500">
                                            {{ $product->slug }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- ===================================== --}}
                            {{-- KATEGORI --}}
                            {{-- ===================================== --}}

                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700">
                                    {{ $product->category->name ?? '-' }}
                                </span>
                            </td>

                            {{-- ===================================== --}}
                            {{-- VARIAN --}}
                            {{-- ===================================== --}}

                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="text-sm font-medium text-gray-700">
                                    {{ $product->variants->count() }} varian
                                </span>
                            </td>

                            {{-- ===================================== --}}
                            {{-- HARGA --}}
                            {{-- ===================================== --}}

                            <td class="whitespace-nowrap px-6 py-4">
                                @php
                                    $minPrice = $product->variants->min('price');
                                    $maxPrice = $product->variants->max('price');
                                @endphp

                                @if ($minPrice !== null && $maxPrice !== null)
                                    @if ($minPrice == $maxPrice)
                                        <span class="text-sm font-semibold text-gray-900">
                                            Rp {{ number_format($minPrice, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-sm font-semibold text-gray-900">
                                            Rp {{ number_format($minPrice, 0, ',', '.') }}
                                            -
                                            Rp {{ number_format($maxPrice, 0, ',', '.') }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-sm text-gray-400">Belum ada harga</span>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-6 py-4">
                                @php
                                    $totalStock = $product->variants->sum('stock');
                                    $status = $product->stock_status ?? 'in_stock';
                                    $label = $product->stock_status_label ?? 'Aman';
                                    $color = $product->stock_status_color ?? 'green';
                                @endphp
                                
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ number_format($totalStock, 0, ',', '.') }}
                                    </span>
                                    
                                    <span class="inline-flex items-center gap-1 text-xs mt-1">
                                        <span class="inline-block w-2 h-2 rounded-full 
                                            {{ $color == 'red' ? 'bg-red-500' : '' }}
                                            {{ $color == 'yellow' ? 'bg-yellow-500' : '' }}
                                            {{ $color == 'green' ? 'bg-green-500' : '' }}
                                            {{ $color == 'gray' ? 'bg-gray-400' : '' }}">
                                        </span>
                                        <span class="
                                            {{ $color == 'red' ? 'text-red-600 font-semibold' : '' }}
                                            {{ $color == 'yellow' ? 'text-yellow-600 font-semibold' : '' }}
                                            {{ $color == 'green' ? 'text-green-600' : '' }}
                                            {{ $color == 'gray' ? 'text-gray-500' : '' }}">
                                            {{ $label }}
                                        </span>
                                    </span>
                                    
                                    {{-- 🔥 Tambahkan tooltip minimal dan maks --}}
                                    @if($product->minimum_stock || $product->restock_threshold)
                                        <span class="text-[10px] text-gray-400 mt-0.5">
                                            Min: {{ $product->minimum_stock ?? 5 }} | Restock: {{ $product->restock_threshold ?? 10 }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- ===================================== --}}
                            {{-- STATUS --}}
                            {{-- ===================================== --}}

                            <td class="whitespace-nowrap px-6 py-4">
                                @if ($product->is_active)
                                    <span class="inline-flex rounded-full bg-green-50 px-3 py-1 text-xs font-medium text-green-700">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            {{-- ===================================== --}}
                            {{-- AKSI --}}
                            {{-- ===================================== --}}

                            {{-- Di bagian Aksi --}}
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    
                                    {{-- EDIT --}}
                                    <a href="{{ route('admin.products.edit', $product) }}"
                                        class="rounded-lg border border-gray-200 p-2 text-gray-500 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-600"
                                        title="Edit Produk">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.5-9.5a2.121 2.121 0 013 3L12 14l-4 1 1-4 7.5-7.5z"/>
                                        </svg>
                                    </a>

                                    {{-- DELETE --}}
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                        onsubmit="return confirm('Apakah kamu yakin ingin menghapus produk ini?')"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="rounded-lg border border-gray-200 p-2 text-gray-500 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600"
                                            title="Hapus Produk">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
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
                            <td colspan="8" class="px-6 py-16 text-center">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0H4m4 4h8"/>
                                    </svg>
                                </div>
                                <h3 class="mt-4 text-sm font-semibold text-gray-900">Belum ada produk</h3>
                                <p class="mt-1 text-sm text-gray-500">Mulai tambahkan produk pertama ke toko kamu.</p>
                                <a href="{{ route('admin.products.create') }}" class="mt-5 inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                    Tambah Produk
                                </a>
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
            <div class="border-t border-gray-200 px-6 py-4">
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
    const bulkDeleteText = document.getElementById('bulk-delete-text');

    // 🔥 UPDATE BULK DELETE BUTTON STATE
    function updateBulkDeleteButton() {
        const checked = document.querySelectorAll('.product-checkbox:checked');
        const count = checked.length;

        if (count > 0) {
            bulkDeleteBtn.disabled = false;
            bulkDeleteCount.textContent = count;
            bulkDeleteText.textContent = count > 1 ? 'Hapus Terpilih' : 'Hapus Terpilih';
        } else {
            bulkDeleteBtn.disabled = true;
            bulkDeleteCount.textContent = '0';
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

    // 🔥 UPDATE BUTTON ON PAGE LOAD
    updateBulkDeleteButton();

    // 🔥 SHOW ALERT
    function showAlert(message, type = 'info') {
        const oldAlert = document.querySelector('.custom-alert');
        if (oldAlert) {
            oldAlert.remove();
        }

        const colors = {
            success: 'border-green-200 bg-green-50 text-green-700',
            error: 'border-red-200 bg-red-50 text-red-700',
            warning: 'border-yellow-200 bg-yellow-50 text-yellow-700',
            info: 'border-blue-200 bg-blue-50 text-blue-700'
        };

        const icons = {
            success: '✅',
            error: '❌',
            warning: '⚠️',
            info: 'ℹ️'
        };

        const alert = document.createElement('div');
        alert.className = `custom-alert rounded-lg border ${colors[type] || colors.info} px-4 py-3 text-sm`;
        alert.innerHTML = `
            <div class="flex items-center gap-2">
                <span>${icons[type] || 'ℹ️'}</span>
                <span>${message}</span>
                <button class="ml-auto text-lg leading-none hover:opacity-70" onclick="this.parentElement.parentElement.remove()">&times;</button>
            </div>
        `;

        const header = document.querySelector('.flex.flex-col.gap-4.sm\\:flex-row');
        if (header) {
            header.parentNode.insertBefore(alert, header.nextSibling);
        } else {
            const container = document.querySelector('.space-y-6');
            if (container) {
                container.prepend(alert);
            }
        }

        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);
    }

    // 🔥 BULK DELETE - GUNAKAN FORM SUBMISSION BIASA + AJAX
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

        // 🔥 DISABLE BUTTON
        bulkDeleteBtn.disabled = true;
        bulkDeleteBtn.innerHTML = `
            <svg class="mr-2 h-5 w-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Menghapus...
        `;

        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

        if (!csrfToken) {
            resetBulkDeleteButton();
            showAlert('Token keamanan tidak ditemukan. Silakan muat ulang halaman lalu coba lagi.', 'error');
            return;
        }

        // 🔥 KIRIM REQUEST KE ENDPOINT BULK DELETE YANG BENAR
        fetch('{{ route('admin.products.bulk-destroy') }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                product_ids: productIds
            })
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
            // 🔥 RESET BUTTON
            resetBulkDeleteButton();

            if (data.success) {
                showAlert(data.message || `Berhasil menghapus ${data.deleted || count} produk.`, 'success');
                setTimeout(() => {
                    location.reload();
                }, 2000);
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
            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            <span id="bulk-delete-text">Hapus Terpilih</span>
            <span id="bulk-delete-count" class="ml-2 rounded-full bg-white/20 px-2 py-0.5 text-xs">${count}</span>
        `;
        
        if (count === 0) {
            bulkDeleteBtn.disabled = true;
        } else {
            bulkDeleteBtn.disabled = false;
        }
    }
});
</script>

@endsection