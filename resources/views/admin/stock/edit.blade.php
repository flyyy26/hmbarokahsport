{{-- resources/views/admin/stock/edit.blade.php --}}

@extends('layouts.admin')

@section('content')
<div class="w-full max-w-6xl mx-auto space-y-6">

    {{-- ============================================ --}}
    {{-- HEADER --}}
    {{-- ============================================ --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <h1 class="text-2xl font-bold flex items-center gap-2.5" style="color: var(--text-1)">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl
                             bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                             shadow-lg shadow-amber-500/20 flex-shrink-0">
                    <iconify-icon icon="mdi:package-variant-closed" class="text-slate-900 text-2xl"></iconify-icon>
                </span>
                Edit Stok
            </h1>
            <p class="text-sm mt-1.5 ml-12 truncate" style="color: var(--text-5)">
                Produk <strong style="color: var(--text-3)">{{ $product->name }}</strong>
            </p>
        </div>

        <div class="flex gap-2 flex-shrink-0">
            <a href="{{ route('admin.stock.history', $product) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg
                      text-sm font-semibold transition-all active:scale-95 border"
               style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)"
               onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
               onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'">
                Histori
            </a>
            <a href="{{ route('admin.stock.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg
                      text-sm font-semibold transition-all active:scale-95 border"
               style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)"
               onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
               onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'">
                <iconify-icon icon="mdi:arrow-left"></iconify-icon>
                Kembali
            </a>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- FORM --}}
    {{-- ============================================ --}}
    <form action="{{ route('admin.stock.update', $product) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="space-y-4">

            {{-- Section Header --}}
            <div class="flex items-center justify-between gap-3 pb-2">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                 bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]">
                        <iconify-icon icon="mdi:layers-triple-outline" class="text-slate-900 text-base"></iconify-icon>
                    </span>
                    <div>
                        <h2 class="text-sm font-bold" style="color: var(--text-1)">Stok Per Varian</h2>
                        <p class="text-[10px]" style="color: var(--text-5)">
                            {{ $product->variants->count() }} varian
                        </p>
                    </div>
                </div>
            </div>

            {{-- Varian List --}}
            @foreach ($product->variants as $index => $variant)
                <div class="rounded-2xl border overflow-hidden"
                     style="background: var(--bg-card); border-color: var(--border-2)">

                    {{-- Header Varian --}}
                    <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 border-b"
                         style="background: var(--bg-input); border-color: var(--border-2)">

                        <div class="flex items-center gap-3 min-w-0 flex-wrap">
                            {{-- Varian Chips --}}
                            <div class="flex items-center gap-1 flex-wrap">
                                @foreach($variant->values as $value)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md
                                                 text-[11px] font-semibold"
                                          style="background: var(--bg-card); border: 1px solid var(--border-2); color: var(--text-3)">
                                        {{ $value->value }}
                                    </span>
                                @endforeach
                            </div>

                            {{-- SKU --}}
                            <code class="text-[10px] font-mono px-2 py-0.5 rounded border"
                                  style="background: var(--bg-card); border-color: var(--border-2); color: var(--text-4)">
                                {{ $variant->sku ?? 'Tanpa SKU' }}
                            </code>
                        </div>

                        {{-- Current Stock --}}
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full
                                     text-[11px] font-bold
                                     bg-[#ecbc42]/10 border border-[#ecbc42]/30 text-[#FDDD57]">
                            Stok: {{ $variant->stock }}
                        </span>
                    </div>

                    {{-- Body --}}
                    <div class="p-4">
                        {{-- Info Row --}}
                        <div class="flex flex-wrap items-center gap-4 mb-4 text-xs" style="color: var(--text-5)">
                            <span>
                                Harga: <strong style="color: var(--text-3)">
                                    Rp {{ number_format($variant->price, 0, ',', '.') }}
                                </strong>
                            </span>

                            @if($variant->last_history)
                                <span>
                                    Update terakhir: <strong style="color: var(--text-3)">
                                        {{ $variant->last_history->created_at->format('d M Y, H:i') }}
                                    </strong>
                                </span>
                            @endif
                        </div>

                        {{-- Input Grid --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                            {{-- Stok --}}
                            <div>
                                <label class="form-label">
                                    <iconify-icon icon="mdi:package-variant" class="text-[#ecbc42]"></iconify-icon>
                                    Stok Baru
                                </label>
                                <input type="number"
                                       name="variants[{{ $index }}][stock]"
                                       value="{{ $variant->stock }}"
                                       min="0"
                                       class="form-input font-semibold stock-input">
                                <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">
                            </div>

                            {{-- Alasan --}}
                            <div>
                                <label class="form-label">
                                    <iconify-icon icon="mdi:information-outline" class="text-[#ecbc42]"></iconify-icon>
                                    Alasan
                                </label>
                                <select name="variants[{{ $index }}][reason]" class="form-input">
                                    <option value="restock">Restock</option>
                                    <option value="adjustment">Penyesuaian</option>
                                    <option value="damaged">Rusak / Expired</option>
                                    <option value="transfer_in">Transfer Masuk</option>
                                    <option value="other">Lainnya</option>
                                </select>
                            </div>

                            {{-- Catatan --}}
                            <div class="lg:col-span-2">
                                <label class="form-label">
                                    <iconify-icon icon="mdi:note-text-outline" class="text-[#ecbc42]"></iconify-icon>
                                    Catatan <span class="text-[10px] font-normal" style="color: var(--text-5)">(opsional)</span>
                                </label>
                                <input type="text"
                                       name="variants[{{ $index }}][note]"
                                       placeholder="Contoh: Restock bulanan"
                                       class="form-input">
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Submit Actions --}}
        <div class="mt-6 flex flex-col-reverse sm:flex-row sm:justify-between gap-3 pt-6 border-t"
             style="border-color: var(--border-2)">

            <div class="hidden sm:flex items-center gap-2 text-xs" style="color: var(--text-5)">
                <iconify-icon icon="mdi:information-outline"></iconify-icon>
                <span>Perubahan stok akan tercatat di histori</span>
            </div>

            <div class="flex flex-col-reverse sm:flex-row gap-3 w-full sm:w-auto">
                <a href="{{ route('admin.stock.index') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-lg
                          text-sm font-semibold transition-all active:scale-95"
                   style="background: var(--bg-input); border: 1px solid var(--border-2); color: var(--text-3)"
                   onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                   onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'">
                    <iconify-icon icon="mdi:close"></iconify-icon>
                    Batal
                </a>

                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg
                               text-sm font-bold
                               bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                               text-slate-900
                               shadow-lg shadow-amber-500/20
                               hover:shadow-xl hover:shadow-amber-500/40
                               hover:-translate-y-0.5
                               transition-all active:scale-95 active:translate-y-0">
                    <iconify-icon icon="mdi:content-save-outline" class="text-lg"></iconify-icon>
                    Simpan Semua
                </button>
            </div>
        </div>
    </form>


    {{-- ============================================ --}}
    {{-- BULK UPDATE --}}
    {{-- ============================================ --}}
    <div class="rounded-2xl border overflow-hidden"
         style="background: var(--bg-card); border-color: var(--border-2)">

        {{-- Header --}}
        <div class="flex items-start gap-3 px-5 py-4 border-b"
             style="background: linear-gradient(90deg, rgba(236, 188, 66, 0.1) 0%, transparent 100%); border-color: var(--border-2)">

            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                         bg-gradient-to-br from-[#FDDD57] to-[#ecbc42] flex-shrink-0">
                <iconify-icon icon="mdi:flash-outline" class="text-slate-900 text-base"></iconify-icon>
            </span>
            <div class="min-w-0">
                <h2 class="text-sm font-bold flex items-center gap-2" style="color: var(--text-1)">
                    Update Stok Massal
                    <span class="text-[10px] font-semibold text-[#FDDD57]
                                 bg-[#ecbc42]/10 border border-[#ecbc42]/30
                                 px-2 py-0.5 rounded-full">
                        Semua varian
                    </span>
                </h2>
                <p class="text-[10px] mt-0.5" style="color: var(--text-5)">
                    Update stok untuk seluruh varian produk sekaligus.
                </p>
            </div>
        </div>

        {{-- Body --}}
        <div class="p-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                {{-- Jenis Perubahan --}}
                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:swap-vertical" class="text-[#ecbc42]"></iconify-icon>
                        Jenis Perubahan
                    </label>
                    <select id="bulk-stock-type" class="form-input font-semibold">
                        <option value="add">+ Tambah Stok</option>
                        <option value="subtract">− Kurangi Stok</option>
                        <option value="set">= Set Stok</option>
                    </select>
                </div>

                {{-- Jumlah --}}
                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:numeric" class="text-[#ecbc42]"></iconify-icon>
                        Jumlah
                    </label>
                    <input type="number"
                           id="bulk-stock-quantity"
                           min="0"
                           value="10"
                           class="form-input font-semibold">
                </div>

                {{-- Alasan --}}
                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:information-outline" class="text-[#ecbc42]"></iconify-icon>
                        Alasan
                    </label>
                    <select id="bulk-stock-reason" class="form-input">
                        <option value="restock">Restock</option>
                        <option value="adjustment">Penyesuaian</option>
                        <option value="damaged">Rusak / Expired</option>
                        <option value="transfer_in">Transfer Masuk</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>

                {{-- Catatan --}}
                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:note-text-outline" class="text-[#ecbc42]"></iconify-icon>
                        Catatan <span class="text-[10px] font-normal" style="color: var(--text-5)">(opsional)</span>
                    </label>
                    <input type="text"
                           id="bulk-stock-note"
                           placeholder="Contoh: Restock bulanan"
                           class="form-input">
                </div>
            </div>

            {{-- Action --}}
            <div class="flex flex-wrap items-center gap-3 mt-5">
                <button type="button"
                        id="apply-bulk-stock"
                        data-product-id="{{ $product->id }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg
                               text-sm font-bold transition-all active:scale-95
                               bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                               text-slate-900
                               shadow-lg shadow-amber-500/20
                               hover:shadow-xl hover:shadow-amber-500/40">
                    <iconify-icon icon="mdi:check-all" class="text-base"></iconify-icon>
                    Terapkan ke Semua Varian
                </button>

                <p id="bulk-stock-feedback" class="hidden text-xs font-semibold"></p>
            </div>
        </div>
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

    .form-label {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-3);
        margin-bottom: 0.4rem;
    }
</style>

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
                feedback.textContent = 'Jumlah harus lebih dari 0';
                feedback.className = 'text-xs font-semibold text-red-400';
                feedback.classList.remove('hidden');
                return;
            }

            const variantIds = [];
            document.querySelectorAll('input[name$="[id]"]').forEach(function(input) {
                variantIds.push(parseInt(input.value));
            });

            if (variantIds.length === 0) {
                feedback.textContent = 'Tidak ada varian ditemukan.';
                feedback.className = 'text-xs font-semibold text-red-400';
                feedback.classList.remove('hidden');
                return;
            }

            this.disabled = true;
            this.innerHTML = '<iconify-icon icon="mdi:loading" class="animate-spin text-base"></iconify-icon> Memproses...';
            feedback.className = 'hidden';

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
                    feedback.textContent = data.message;
                    feedback.className = 'text-xs font-semibold text-emerald-400';
                    feedback.classList.remove('hidden');

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
                        input.dispatchEvent(new Event('change', { bubbles: true }));
                    });

                    setTimeout(() => window.location.reload(), 2000);
                } else {
                    feedback.textContent = data.message;
                    feedback.className = 'text-xs font-semibold text-red-400';
                    feedback.classList.remove('hidden');
                }
            })
            .catch(error => {
                feedback.textContent = 'Terjadi kesalahan: ' + error.message;
                feedback.className = 'text-xs font-semibold text-red-400';
                feedback.classList.remove('hidden');
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = '<iconify-icon icon="mdi:check-all" class="text-base"></iconify-icon> Terapkan ke Semua Varian';
            });
        });
    }
});
</script>
@endsection