@extends('layouts.account')

@section('title', $voucher->name . ' - Barokah Sport')
@section('page-title', 'Detail Voucher')
@section('page-subtitle', 'Informasi lengkap voucher')

@section('account-content')

<div class="max-w-2xl mx-auto">
    {{-- Back Button --}}
    <a href="{{ route('customer.vouchers.index') }}" class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 transition mb-6">
        <iconify-icon icon="mdi:arrow-left"></iconify-icon>
        Kembali ke Daftar Voucher
    </a>

    {{-- Voucher Card Detail --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        {{-- Header / Discount --}}
        <div class="bg-gradient-to-r from-red-50 to-orange-50 p-6 border-b border-dashed border-gray-200">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-4xl font-bold text-red-500">
                        @if($voucher->discount_type === 'percentage')
                            {{ round($voucher->discount_value) }}<span class="text-2xl font-normal text-gray-400">%</span>
                        @else
                            <span class="text-2xl font-normal text-gray-400">Rp</span> 
                            {{ number_format($voucher->discount_value, 0, ',', '.') }}
                        @endif
                    </div>
                    <div class="mt-2 font-mono text-sm font-bold text-gray-700 bg-white px-3 py-1 rounded inline-block border border-gray-200">
                        {{ $voucher->code }}
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-block rounded-full px-3 py-1 text-xs font-medium
                        @if($isValid && !$isUsedByUser) bg-green-100 text-green-700
                        @elseif($isUsedByUser) bg-gray-100 text-gray-600
                        @elseif($isExpired) bg-red-100 text-red-700
                        @elseif($isUpcoming) bg-yellow-100 text-yellow-700
                        @elseif($isQuotaFull) bg-orange-100 text-orange-700
                        @else bg-gray-100 text-gray-600 @endif">
                        @if($isUsedByUser)
                            ✅ Sudah Digunakan
                        @elseif($isValid)
                            ✅ Aktif
                        @elseif($isExpired)
                            ⏰ Kadaluarsa
                        @elseif($isUpcoming)
                            📅 Akan Datang
                        @elseif($isQuotaFull)
                            🔒 Kuota Habis
                        @else
                            ⛔ Tidak Tersedia
                        @endif
                    </span>
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="p-6 space-y-5">
            {{-- Nama & Deskripsi --}}
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $voucher->name }}</h2>
                @if($voucher->description)
                    <p class="mt-1 text-sm text-gray-500">{{ $voucher->description }}</p>
                @endif
            </div>

            {{-- Detail Informasi --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-400">Min. Transaksi</p>
                    <p class="font-medium text-gray-700">Rp {{ number_format($voucher->min_transaction_amount, 0, ',', '.') }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-400">Maks. Potongan</p>
                    <p class="font-medium text-gray-700">
                        @if($voucher->max_discount_amount)
                            Rp {{ number_format($voucher->max_discount_amount, 0, ',', '.') }}
                        @else
                            <span class="text-gray-400">Tidak ada batas</span>
                        @endif
                    </p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-400">Total Kuota</p>
                    <p class="font-medium text-gray-700">
                        @if($voucher->usage_limit)
                            {{ $voucher->usage_limit }} kali
                        @else
                            <span class="text-gray-400">Tak Terbatas</span>
                        @endif
                    </p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-400">Sisa Kuota</p>
                    <p class="font-medium text-gray-700">
                        @if($remainingQuota !== null)
                            {{ $remainingQuota }} kali
                        @else
                            <span class="text-gray-400">∞</span>
                        @endif
                    </p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-400">Batas Per User</p>
                    <p class="font-medium text-gray-700">
                        @if($voucher->limit_per_user > 0)
                            {{ $voucher->limit_per_user }} kali
                        @else
                            <span class="text-gray-400">Tak Terbatas</span>
                        @endif
                    </p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-400">Kamu Pakai</p>
                    <p class="font-medium text-gray-700">{{ $usageCountByUser }} kali</p>
                </div>
            </div>

            {{-- Periode --}}
            <div class="bg-gray-50 rounded-lg p-4 flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-400">Mulai Berlaku</p>
                    <p class="font-medium text-gray-700">{{ $voucher->start_date->format('d M Y H:i') }}</p>
                </div>
                <div class="text-gray-300">→</div>
                <div>
                    <p class="text-xs text-gray-400">Berakhir</p>
                    <p class="font-medium {{ now()->diffInDays($voucher->end_date) <= 3 && $isValid ? 'text-red-500' : 'text-gray-700' }}">
                        {{ $voucher->end_date->format('d M Y H:i') }}
                        @if(now()->diffInDays($voucher->end_date) <= 3 && $isValid)
                            <span class="text-xs text-red-400 block">({{ now()->diffInDays($voucher->end_date) }} hari lagi)</span>
                        @endif
                    </p>
                </div>
            </div>

            {{-- Syarat & Ketentuan --}}
            <div class="border-t border-gray-100 pt-4">
                <h4 class="font-semibold text-gray-900 mb-2 flex items-center gap-1">
                    <span>📋</span> Syarat & Ketentuan
                </h4>
                @if($voucher->terms_and_conditions)
                    <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-600 prose prose-sm max-w-none">
                        {!! $voucher->terms_and_conditions !!}
                    </div>
                @else
                    <p class="text-sm text-gray-400 italic">Tidak ada syarat & ketentuan khusus.</p>
                @endif
            </div>

            {{-- Action Button --}}
            <div class="pt-4 border-t border-gray-100">
                @if($isUsedByUser)
                    <div class="text-center py-3 bg-gray-100 rounded-lg text-gray-500 text-sm font-medium">
                        ✅ Voucher sudah Anda gunakan
                    </div>
                @elseif(!$isValid)
                    <div class="text-center py-3 bg-gray-100 rounded-lg text-gray-500 text-sm font-medium">
                        @if($isExpired)
                            ⏰ Voucher sudah kadaluarsa
                        @elseif($isUpcoming)
                            📅 Voucher akan aktif mulai {{ $voucher->start_date->format('d M Y H:i') }}
                        @elseif($isQuotaFull)
                            🔒 Kuota voucher sudah habis
                        @else
                            ⛔ Voucher tidak tersedia
                        @endif
                    </div>
                @else
                    <form action="{{ route('customer.vouchers.use', $voucher) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full rounded-lg bg-blue-600 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 active:scale-[0.98]">
                            🛒 Pakai Voucher Sekarang
                        </button>
                    </form>
                    <p class="mt-2 text-xs text-center text-gray-400">
                        {{ $eligibilityMessage }}
                    </p>
                @endif
            </div>
        </div>
    </div>

    {{-- Back Button Bottom --}}
    <div class="mt-6 text-center">
        <a href="{{ route('customer.vouchers.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">
            ← Kembali ke Daftar Voucher
        </a>
    </div>
</div>

@endsection

<style>
    .prose ul, .prose ol {
        padding-left: 1.2rem;
    }
    .prose li {
        margin-bottom: 0.2rem;
    }
</style>