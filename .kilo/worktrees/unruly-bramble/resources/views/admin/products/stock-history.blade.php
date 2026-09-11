{{-- resources/views/admin/products/stock-history.blade.php --}}

@extends('layouts.admin')

@section('content')

<div class="space-y-6">
    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                History Stok: {{ $product->name }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Riwayat perubahan stok produk.
            </p>
        </div>
        <a href="{{ route('admin.products.edit', $product) }}"
            class="inline-flex items-center rounded-lg bg-gray-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-gray-700">
            ← Kembali ke Produk
        </a>
    </div>

    {{-- SUMMARY --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
        <div class="rounded-lg border border-gray-200 bg-white p-4">
            <p class="text-sm text-gray-500">Total Stok</p>
            <p class="text-2xl font-bold text-gray-900">{{ $product->variants->sum('stock') }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4">
            <p class="text-sm text-gray-500">Total History</p>
            <p class="text-2xl font-bold text-gray-900">{{ $histories->total() }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4">
            <p class="text-sm text-gray-500">Terakhir Update</p>
            <p class="text-sm font-semibold text-gray-900">
                {{ $histories->first()?->created_at->format('d/m/Y H:i') ?? '-' }}
            </p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4">
            <p class="text-sm text-gray-500">Varian</p>
            <p class="text-2xl font-bold text-gray-900">{{ $product->variants->count() }}</p>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Tanggal
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Varian
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Stok Lama
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Stok Baru
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Perubahan
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Alasan
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Catatan
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Oleh
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($histories as $history)
                        <tr class="hover:bg-gray-50">
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                {{ $history->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                {{ $history->variant?->option_combination ?? $product->name }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                {{ $history->old_stock }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                {{ $history->new_stock }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold {{ $history->quantity_color }}">
                                {{ $history->quantity_display }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium
                                    {{ $history->reason_color == 'green' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $history->reason_color == 'blue' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $history->reason_color == 'red' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $history->reason_color == 'yellow' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $history->reason_color == 'purple' ? 'bg-purple-100 text-purple-700' : '' }}
                                    {{ $history->reason_color == 'indigo' ? 'bg-indigo-100 text-indigo-700' : '' }}
                                    {{ $history->reason_color == 'orange' ? 'bg-orange-100 text-orange-700' : '' }}
                                    {{ $history->reason_color == 'gray' ? 'bg-gray-100 text-gray-700' : '' }}">
                                    {{ $history->reason_label }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                {{ $history->note ?? '-' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                {{ $history->user?->name ?? 'Sistem' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center text-gray-500">
                                Belum ada history stok untuk produk ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-gray-200 px-6 py-4">
            {{ $histories->links() }}
        </div>
    </div>
</div>

@endsection