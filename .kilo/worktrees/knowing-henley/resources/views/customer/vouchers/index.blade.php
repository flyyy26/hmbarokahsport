@extends('layouts.account')

@section('title', 'Voucher Saya - Barokah Sport')
@section('page-title', 'Voucher Saya')
@section('page-subtitle', 'Kumpulkan dan gunakan voucher untuk mendapatkan potongan harga.')

@section('account-content')

{{-- Filter --}}
<div class="mb-6 flex flex-wrap items-center gap-3">
    <form method="GET" action="{{ route('customer.vouchers.index') }}" class="flex flex-wrap items-center gap-2 flex-1">
        @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif

        <input type="text" name="search" value="{{ request('search') }}" 
               placeholder="Cari voucher..." 
               class="flex-1 min-w-[120px] rounded-lg border border-gray-300 px-3 py-1.5 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
               onchange="this.form.submit()">

        <select name="sort" onchange="this.form.submit()" 
                class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 bg-white">
            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
            <option value="discount_desc" {{ request('sort') == 'discount_desc' ? 'selected' : '' }}>Diskon Terbesar</option>
            <option value="discount_asc" {{ request('sort') == 'discount_asc' ? 'selected' : '' }}>Diskon Terkecil</option>
            <option value="expired_soon" {{ request('sort') == 'expired_soon' ? 'selected' : '' }}>Segera Berakhir</option>
        </select>

        @if(request()->anyFilled(['search', 'sort']))
            <a href="{{ route('customer.vouchers.index') }}" class="text-sm text-red-500 hover:text-red-700">Reset</a>
        @endif
    </form>
    <span class="text-sm text-gray-500">{{ $vouchers->total() }} voucher</span>
</div>

{{-- Voucher Grid --}}
@if ($vouchers->isEmpty())
    <div class="text-center py-10">
        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-gray-100 text-4xl">
            🎟️
        </div>
        <h3 class="mt-4 text-lg font-semibold text-gray-900">Belum Ada Voucher Tersedia</h3>
        <p class="mt-2 text-sm text-gray-500">Saat ini belum ada voucher yang tersedia untuk Anda. Cek kembali nanti!</p>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($vouchers as $voucher)
            <div class="rounded-xl border border-gray-200 bg-white overflow-hidden transition hover:shadow-md relative">
                @if($voucher->is_used_by_user)
                    <span class="absolute top-3 right-3 bg-red-500 text-white text-xs font-semibold px-2 py-0.5 rounded-full">
                        ✓ Digunakan
                    </span>
                @endif

                {{-- Top Section --}}
                <div class="p-4 border-b border-dashed border-gray-200">
                    <div class="text-2xl font-bold text-red-500">
                        @if($voucher->discount_type === 'percentage')
                            {{ round($voucher->discount_value) }}<span class="text-sm font-normal text-gray-400">%</span>
                        @else
                            <span class="text-sm font-normal text-gray-400">Rp</span> {{ number_format($voucher->discount_value, 0, ',', '.') }}
                        @endif
                    </div>
                    <div class="mt-1 flex items-center gap-2 flex-wrap">
                        <div class="font-mono text-xs font-bold text-gray-700 bg-gray-100 px-2 py-0.5 rounded inline-block">
                            {{ $voucher->code }}
                        </div>
                    </div>
                    <div class="mt-1 text-xs text-gray-400">
                        Min. Belanja Rp {{ number_format($voucher->min_transaction_amount, 0, ',', '.') }}
                    </div>
                </div>

                {{-- Body Section --}}
                <div class="p-4">
                    <h4 class="font-semibold text-gray-900">{{ $voucher->name }}</h4>
                    @if($voucher->description)
                        <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $voucher->description }}</p>
                    @endif
                    <div class="mt-2 text-xs text-gray-400">
                        Berlaku sampai: 
                        <span class="font-medium {{ now()->diffInDays($voucher->end_date) <= 3 ? 'text-red-500' : 'text-gray-600' }}">
                            {{ $voucher->end_date->format('d/m/Y H:i') }}
                            @if(now()->diffInDays($voucher->end_date) <= 3)
                                ({{ now()->diffInDays($voucher->end_date) }} hari lagi)
                            @endif
                        </span>
                    </div>
                </div>

                {{-- Footer Section --}}
                <div class="px-4 pb-4">
                    <div class="flex items-center justify-between gap-2">
                        <div class="text-xs text-gray-400">
                            @if($voucher->is_for_all_users)
                                @if($voucher->limit_per_user > 0)
                                    <span class="ml-1">Max {{ $voucher->limit_per_user }}x/user</span>
                                @else
                                    <span class="ml-1">Tanpa batas</span>
                                @endif
                            @else
                                @if($voucher->remaining_quota !== null)
                                    Sisa: <span class="quota-number font-medium text-gray-700">{{ $voucher->remaining_quota }}</span>
                                @else
                                    <span class="quota-number font-medium text-gray-700">∞</span> Tak Terbatas
                                @endif
                                @if($voucher->limit_per_user > 0)
                                    · Max {{ $voucher->limit_per_user }}x/user
                                @endif
                            @endif
                        </div>

                            
                        <a href="{{ route('customer.vouchers.show', $voucher) }}" >
                            <button class="rounded-lg px-3 py-1.5 text-xs font-medium text-white cursor-pointer" style="background-color:#076694;">
                                Lihat detail voucher
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $vouchers->links() }}
    </div>
@endif

{{-- ============================================ --}}
{{-- 🔥 MODAL DETAIL VOUCHER --}}
{{-- ============================================ --}}
<div id="voucher-detail-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden">
    <div class="bg-white rounded-xl max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto shadow-2xl animate-fadeIn">
        {{-- Header --}}
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-t-xl z-10">
            <h3 class="text-lg font-bold text-gray-900">Detail Voucher</h3>
            <button type="button" onclick="closeVoucherDetail()" 
                    class="text-gray-400 hover:text-gray-600 transition text-2xl leading-none">
                ✕
            </button>
        </div>

        {{-- Body --}}
        <div class="p-6 space-y-5" id="voucher-detail-body">
            {{-- Content akan diisi oleh JavaScript --}}
            <div class="flex justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-4 border-blue-500 border-t-transparent"></div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================ --}}
{{-- 🔥 STYLE UNTUK MODAL --}}
{{-- ============================================ --}}
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.95) translateY(-10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .animate-fadeIn {
        animation: fadeIn 0.25s ease-out forwards;
    }
    
    #voucher-detail-modal .scrollbar-thin::-webkit-scrollbar {
        width: 4px;
    }
    #voucher-detail-modal .scrollbar-thin::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    #voucher-detail-modal .scrollbar-thin::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
</style>

{{-- ============================================ --}}
{{-- 🔥 JAVASCRIPT --}}
{{-- ============================================ --}}
@php
    $voucherData = $vouchers->map(function ($v) {
        return [
            'id' => $v->id,
            'name' => $v->name,
            'code' => $v->code,
            'description' => $v->description,
            'terms_and_conditions' => $v->terms_and_conditions,
            'discount_type' => $v->discount_type,
            'discount_value' => $v->discount_value,
            'max_discount_amount' => $v->max_discount_amount,
            'min_transaction_amount' => $v->min_transaction_amount,
            'usage_limit' => $v->usage_limit,
            'used_count' => $v->used_count,
            'limit_per_user' => $v->limit_per_user,
            'is_used_by_user' => $v->is_used_by_user,
            'can_use' => $v->can_use,
            'start_date' => $v->start_date ? $v->start_date->format('d/m/Y H:i') : null,
            'end_date' => $v->end_date ? $v->end_date->format('d/m/Y H:i') : null,
            'remaining_quota' => $v->remaining_quota,
            'discount_label' => $v->discount_label,
            'usage_count_by_user' => $v->usage_count_by_user ?? 0,
        ];
    })->values()->all();
@endphp

<script>
    // 🔥 DATA VOUCHER DARI SERVER
    const voucherData = @json($voucherData);

    function openVoucherDetail(voucherId) {
        const modal = document.getElementById('voucher-detail-modal');
        const body = document.getElementById('voucher-detail-body');
        
        // Cari data voucher
        const voucher = voucherData.find(v => v.id === voucherId);
        
        if (!voucher) {
            body.innerHTML = '<p class="text-center text-red-500">Data voucher tidak ditemukan.</p>';
            modal.classList.remove('hidden');
            return;
        }

        // 🔥 RENDER DETAIL VOUCHER
        const discountDisplay = voucher.discount_type === 'percentage' 
            ? voucher.discount_value + '%' 
            : 'Rp ' + new Intl.NumberFormat('id-ID').format(voucher.discount_value);

        const maxDiscountDisplay = voucher.max_discount_amount 
            ? 'Rp ' + new Intl.NumberFormat('id-ID').format(voucher.max_discount_amount)
            : 'Tidak ada batas';

        const minTransactionDisplay = 'Rp ' + new Intl.NumberFormat('id-ID').format(voucher.min_transaction_amount);
        const usedCountDisplay = voucher.used_count ?? 0;
        const usageLimitDisplay = voucher.usage_limit ?? '∞ (Tak Terbatas)';
        const remainingQuotaDisplay = voucher.remaining_quota !== null ? voucher.remaining_quota : '∞';

        body.innerHTML = `
            {{-- Voucher Header --}}
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-5 text-center border border-blue-100">
                <div class="text-3xl font-bold text-red-500">
                    ${voucher.discount_type === 'percentage' ? voucher.discount_value + '%' : 'Rp ' + new Intl.NumberFormat('id-ID').format(voucher.discount_value)}
                </div>
                <div class="mt-1 font-mono text-sm font-bold text-gray-700 bg-white px-3 py-1 rounded inline-block border border-gray-200">
                    ${voucher.code}
                </div>
                <h4 class="mt-2 font-semibold text-gray-900">${voucher.name}</h4>
                ${voucher.description ? `<p class="text-sm text-gray-500 mt-1">${voucher.description}</p>` : ''}
            </div>

            {{-- Detail Info --}}
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-400">Tipe Diskon</p>
                    <p class="font-medium text-gray-700 capitalize">${voucher.discount_type}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-400">Min. Transaksi</p>
                    <p class="font-medium text-gray-700">${minTransactionDisplay}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-400">Maks. Potongan</p>
                    <p class="font-medium text-gray-700">${maxDiscountDisplay}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-400">Kuota Tersisa</p>
                    <p class="font-medium text-gray-700">${remainingQuotaDisplay}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-400">Total Kuota</p>
                    <p class="font-medium text-gray-700">${usageLimitDisplay}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-400">Sudah Digunakan</p>
                    <p class="font-medium text-gray-700">${usedCountDisplay} kali</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-400">Batas Per User</p>
                    <p class="font-medium text-gray-700">${voucher.limit_per_user > 0 ? voucher.limit_per_user + ' kali' : 'Tak Terbatas'}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-400">Kamu Pakai</p>
                    <p class="font-medium text-gray-700">${voucher.usage_count_by_user} kali</p>
                </div>
            </div>

            {{-- Periode --}}
            <div class="bg-gray-50 rounded-lg p-4 text-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-400">Mulai Berlaku</p>
                        <p class="font-medium text-gray-700">${voucher.start_date || '-'}</p>
                    </div>
                    <div class="text-gray-300">→</div>
                    <div>
                        <p class="text-xs text-gray-400">Berakhir</p>
                        <p class="font-medium ${new Date(voucher.end_date) - new Date() <= 3 * 24 * 60 * 60 * 1000 ? 'text-red-500' : 'text-gray-700'}">
                            ${voucher.end_date || '-'}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Syarat & Ketentuan --}}
            <div>
                <h5 class="text-sm font-semibold text-gray-900 mb-2 flex items-center gap-1">
                    <span>📋</span> Syarat & Ketentuan
                </h5>
                ${voucher.terms_and_conditions 
                    ? `<div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-600 prose prose-sm max-w-none">
                            ${voucher.terms_and_conditions}
                       </div>`
                    : `<p class="text-sm text-gray-400 italic">Tidak ada syarat & ketentuan khusus.</p>`
                }
            </div>

            {{-- Status --}}
            <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                <span class="inline-flex items-center gap-1 text-sm">
                    <span class="w-2 h-2 rounded-full ${voucher.can_use && !voucher.is_used_by_user ? 'bg-green-500' : 'bg-red-500'}"></span>
                    ${voucher.is_used_by_user ? 'Sudah digunakan' : (voucher.can_use ? 'Dapat digunakan' : 'Tidak tersedia')}
                </span>
                ${voucher.is_used_by_user ? `<span class="text-xs text-gray-400">(Sudah dipakai ${voucher.usage_count_by_user} kali)</span>` : ''}
            </div>
        `;

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeVoucherDetail() {
        const modal = document.getElementById('voucher-detail-modal');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    // 🔥 CLOSE MODAL ON OVERLAY CLICK
    document.getElementById('voucher-detail-modal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeVoucherDetail();
        }
    });

    // 🔥 CLOSE MODAL ON ESCAPE
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeVoucherDetail();
        }
    });
</script>

@endsection