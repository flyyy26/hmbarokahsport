@extends('layouts.admin')

@section('title', 'Voucher Promo')
@section('page-title', 'Voucher Promo')

@section('content')
<div class="space-y-6">
    {{-- Header & Tombol Tambah --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Daftar Voucher</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola kupon diskon transaksi dan kode promo toko.</p>
        </div>
        <a href="{{ route('admin.vouchers.create') }}"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700 shadow-sm">
            <span>+</span>
            <span>Tambah Voucher</span>
        </a>
    </div>

    @if (session('success'))
        <div class="rounded-lg border-l-4 border-green-500 bg-green-50 p-4 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="rounded-lg border-l-4 border-red-500 bg-red-50 p-4 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- Filter & Search --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.vouchers.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[240px]">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari kode promo atau nama voucher..."
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <select name="status" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif Berjalan</option>
                    <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>Akan Datang</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Kadaluarsa</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Dinonaktifkan</option>
                </select>
            </div>
            <button type="submit" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">
                Filter
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.vouchers.index') }}" class="text-sm text-blue-600 hover:underline">Reset</a>
            @endif
        </form>
    </div>

    {{-- Tabel Voucher --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
            <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500">
                <tr>
                    <th class="px-6 py-3.5">Voucher & Kode</th>
                    <th class="px-6 py-3.5">Potongan</th>
                    <th class="px-6 py-3.5">Min. Belanja</th>
                    <th class="px-6 py-3.5">Penggunaan</th>
                    <th class="px-6 py-3.5">Periode</th>
                    <th class="px-6 py-3.5">Status</th>
                    <th class="px-6 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse ($vouchers as $voucher)
                    @php
                        $now = now();
                        $isExpired = $voucher->end_date < $now;
                        $isUpcoming = $voucher->start_date > $now;
                    @endphp
                    <tr class="hover:bg-gray-50/80 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-900">{{ $voucher->name }}</p>
                            <span class="inline-block mt-1 font-mono text-xs font-bold px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $voucher->code }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if ($voucher->discount_type === 'fixed')
                                <span class="font-medium text-gray-900">Rp {{ number_format($voucher->discount_value, 0, ',', '.') }}</span>
                            @else
                                <span class="font-medium text-gray-900">{{ (float) $voucher->discount_value }}%</span>
                                @if ($voucher->max_discount_amount)
                                    <p class="text-xs text-gray-500">Maks. Rp {{ number_format($voucher->max_discount_amount, 0, ',', '.') }}</p>
                                @endif
                            @endif
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-700">
                            Rp {{ number_format($voucher->min_transaction_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-600">
                            <span class="font-semibold text-gray-900">{{ $voucher->usages_count }}</span>
                            / {{ $voucher->usage_limit ?? '∞' }} dipakai
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-600 whitespace-nowrap">
                            <p>{{ $voucher->start_date->format('d M Y, H:i') }}</p>
                            <p class="text-gray-400">s/d {{ $voucher->end_date->format('d M Y, H:i') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @if (!$voucher->is_active)
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">Nonaktif</span>
                            @elseif ($isExpired)
                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-700">Kadaluarsa</span>
                            @elseif ($isUpcoming)
                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">Akan Datang</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">Aktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.vouchers.edit', $voucher->id) }}" class="text-xs font-medium text-blue-600 hover:text-blue-800">
                                Edit
                            </a>
                            <form action="{{ route('admin.vouchers.destroy', $voucher->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus voucher {{ $voucher->code }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-800">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">
                            Belum ada voucher yang dibuat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($vouchers->hasPages())
            <div class="border-t border-gray-200 p-4">
                {{ $vouchers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection