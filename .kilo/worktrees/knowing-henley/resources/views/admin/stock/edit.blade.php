{{-- resources/views/admin/stock/edit.blade.php --}}

@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Stok: {{ $product->name }}</h1>
            <p class="mt-1 text-sm text-gray-500">Update stok per varian atau massal.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.stock.history', $product) }}"
                class="rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">
                Lihat History
            </a>
            <a href="{{ route('admin.stock.index') }}"
                class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Kembali
            </a>
        </div>
    </div>

    {{-- FORM --}}
    <form action="{{ route('admin.stock.update', $product) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            @foreach ($product->variants as $index => $variant)
                <div class="rounded-lg border border-gray-200 bg-white p-4">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-6">
                        {{-- Varian Info --}}
                        <div class="col-span-2">
                            <p class="text-sm font-semibold text-gray-900">
                                {{ $variant->values->pluck('value')->implode(' - ') }}
                            </p>
                            <p class="text-xs text-gray-500">SKU: {{ $variant->sku ?? '-' }}</p>
                            <p class="text-xs text-gray-500">Harga: Rp {{ number_format($variant->price, 0, ',', '.') }}</p>
                            @if($variant->last_history)
                                <p class="text-xs text-gray-400">
                                    Last update: {{ $variant->last_history->created_at->format('d/m/Y H:i') }}
                                </p>
                            @endif
                        </div>

                        {{-- Stok --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Stok</label>
                            <input type="number" name="variants[{{ $index }}][stock]" 
                                value="{{ $variant->stock }}"
                                min="0"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 stock-input">
                            <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">
                        </div>

                        {{-- Alasan --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Alasan</label>
                            <select name="variants[{{ $index }}][reason]" 
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="restock">🔄 Restock</option>
                                <option value="adjustment">📝 Penyesuaian</option>
                                <option value="return">↩️ Pengembalian</option>
                                <option value="damaged">❌ Rusak/Expired</option>
                                <option value="transfer_in">📦 Transfer Masuk</option>
                                <option value="order_cancelled">🚫 Pesanan Dibatalkan</option>
                                <option value="other">📌 Lainnya</option>
                            </select>
                        </div>

                        {{-- Catatan --}}
                        <div class="col-span-2">
                            <label class="block text-xs font-medium text-gray-700">Catatan (Opsional)</label>
                            <input type="text" name="variants[{{ $index }}][note]" 
                                placeholder="Contoh: Restock bulanan"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Submit --}}
        <div class="mt-6 flex justify-end gap-3 border-t pt-6">
            <a href="{{ route('admin.stock.index') }}"
                class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Batal
            </a>
            <button type="submit"
                class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
                Simpan Semua
            </button>
        </div>
    </form>

    {{-- BULK UPDATE --}}
    <div class="mt-8 border-t pt-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Update Stok Massal</h2>
                <p class="mt-1 text-sm text-gray-500">Update stok semua varian sekaligus.</p>
            </div>
        </div>

        <div class="mt-4 rounded-lg border border-gray-200 bg-gray-50 p-4">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Jenis Perubahan</label>
                    <select id="bulk-stock-type" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="add">+ Tambah Stok</option>
                        <option value="subtract">- Kurangi Stok</option>
                        <option value="set">= Set Stok</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                    <input type="number" id="bulk-stock-quantity" min="0" value="10"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Alasan</label>
                    <select id="bulk-stock-reason" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="restock">🔄 Restock</option>
                        <option value="adjustment">📝 Penyesuaian</option>
                        <option value="return">↩️ Pengembalian</option>
                        <option value="damaged">❌ Rusak/Expired</option>
                        <option value="transfer_in">📦 Transfer Masuk</option>
                        <option value="order_cancelled">🚫 Pesanan Dibatalkan</option>
                        <option value="other">📌 Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
                    <input type="text" id="bulk-stock-note" placeholder="Contoh: Restock bulanan"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>
            <button type="button" id="apply-bulk-stock"
                data-product-id="{{ $product->id }}"
                class="mt-3 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Terapkan ke Semua Varian
            </button>
            <p id="bulk-stock-feedback" class="mt-2 text-sm hidden"></p>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // ============================================
    // 🔥 BULK STOCK UPDATE - AJAX
    // ============================================

    const applyBulkBtn = document.getElementById('apply-bulk-stock');
    if (applyBulkBtn) {
        applyBulkBtn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const type = document.getElementById('bulk-stock-type').value;
            const quantity = parseInt(document.getElementById('bulk-stock-quantity').value) || 0;
            const reason = document.getElementById('bulk-stock-reason').value;
            const note = document.getElementById('bulk-stock-note').value || '';
            const feedback = document.getElementById('bulk-stock-feedback');
            
            if (quantity <= 0) {
                feedback.textContent = '⚠️ Jumlah harus lebih dari 0';
                feedback.className = 'mt-2 text-sm text-red-600';
                feedback.classList.remove('hidden');
                return;
            }

            // 🔥 AMBIL SEMUA ID VARIAN
            const variantIds = [];
            document.querySelectorAll('input[name$="[id]"]').forEach(function(input) {
                variantIds.push(parseInt(input.value));
            });

            if (variantIds.length === 0) {
                feedback.textContent = '⚠️ Tidak ada varian ditemukan.';
                feedback.className = 'mt-2 text-sm text-red-600';
                feedback.classList.remove('hidden');
                return;
            }

            // Disable button
            this.disabled = true;
            this.textContent = 'Memproses...';
            feedback.className = 'mt-2 text-sm hidden';

            fetch('{{ route("admin.stock.bulk") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    variant_ids: variantIds,
                    type: type,
                    quantity: quantity,
                    reason: reason,
                    note: note
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    feedback.textContent = '✅ ' + data.message;
                    feedback.className = 'mt-2 text-sm text-green-600';
                    feedback.classList.remove('hidden');

                    // 🔥 UPDATE INPUT STOK DI FORM
                    document.querySelectorAll('.stock-input').forEach(function(input) {
                        let currentVal = parseInt(input.value) || 0;
                        let newVal = currentVal;
                        
                        if (type === 'add') {
                            newVal = currentVal + quantity;
                        } else if (type === 'subtract') {
                            newVal = Math.max(0, currentVal - quantity);
                        } else if (type === 'set') {
                            newVal = quantity;
                        }
                        
                        input.value = newVal;
                        // Trigger change event
                        input.dispatchEvent(new Event('change', { bubbles: true }));
                    });

                    // 🔥 RELOAD PAGE AFTER 2 SECONDS
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    feedback.textContent = '❌ ' + data.message;
                    feedback.className = 'mt-2 text-sm text-red-600';
                    feedback.classList.remove('hidden');
                }
            })
            .catch(error => {
                feedback.textContent = '❌ Terjadi kesalahan: ' + error.message;
                feedback.className = 'mt-2 text-sm text-red-600';
                feedback.classList.remove('hidden');
            })
            .finally(() => {
                this.disabled = false;
                this.textContent = 'Terapkan ke Semua Varian';
            });
        });
    }
});
</script>
@endsection