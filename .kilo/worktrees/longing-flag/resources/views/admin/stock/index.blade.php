{{-- resources/views/admin/stock/index.blade.php --}}

@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Stok</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola stok semua produk dan lihat riwayat perubahan.</p>
        </div>
    </div>

    {{-- STATISTIK --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
        <div class="rounded-lg border border-gray-200 bg-white p-4">
            <p class="text-sm text-gray-500">Total Produk</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_products'] }}</p>
        </div>
        <div class="rounded-lg border border-red-200 bg-red-50 p-4">
            <p class="text-sm text-red-600">🔴 Kritis</p>
            <p class="text-2xl font-bold text-red-700">{{ $stats['critical_count'] }}</p>
        </div>
        <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4">
            <p class="text-sm text-yellow-600">🟡 Menipis</p>
            <p class="text-2xl font-bold text-yellow-700">{{ $stats['low_count'] }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
            <p class="text-sm text-gray-600">⚫ Habis</p>
            <p class="text-2xl font-bold text-gray-700">{{ $stats['out_of_stock_count'] }}</p>
        </div>
    </div>

    {{-- FILTER & SEARCH --}}
    <div class="flex flex-wrap items-center gap-3">
        <span class="text-sm font-medium text-gray-700">Filter Stok:</span>
        <a href="{{ route('admin.stock.index', ['stock' => 'all']) }}" 
            class="px-3 py-1 text-sm rounded-full border {{ request('stock') == 'all' || !request('stock') ? 'bg-gray-900 text-white' : 'bg-white text-gray-700 border-gray-300' }}">
            Semua
        </a>
        <a href="{{ route('admin.stock.index', ['stock' => 'critical']) }}" 
            class="px-3 py-1 text-sm rounded-full border {{ request('stock') == 'critical' ? 'bg-red-600 text-white' : 'bg-white text-red-600 border-red-300' }}">
            🔴 Kritis
        </a>
        <a href="{{ route('admin.stock.index', ['stock' => 'low']) }}" 
            class="px-3 py-1 text-sm rounded-full border {{ request('stock') == 'low' ? 'bg-yellow-500 text-white' : 'bg-white text-yellow-600 border-yellow-300' }}">
            🟡 Menipis
        </a>
        <a href="{{ route('admin.stock.index', ['stock' => 'out_of_stock']) }}" 
            class="px-3 py-1 text-sm rounded-full border {{ request('stock') == 'out_of_stock' ? 'bg-gray-600 text-white' : 'bg-white text-gray-600 border-gray-300' }}">
            ⚫ Habis
        </a>
        <a href="{{ route('admin.stock.index', ['stock' => 'in_stock']) }}" 
            class="px-3 py-1 text-sm rounded-full border {{ request('stock') == 'in_stock' ? 'bg-green-600 text-white' : 'bg-white text-green-600 border-green-300' }}">
            ✅ Aman
        </a>

        <div class="ml-auto">
            <form method="GET" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari produk..."
                    class="rounded-lg border-gray-300 px-3 py-1 text-sm focus:border-blue-500 focus:ring-blue-500">
                <button type="submit" class="rounded-lg bg-blue-600 px-3 py-1 text-sm text-white hover:bg-blue-700">
                    Cari
                </button>
            </form>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Produk</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Kategori</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Varian</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Total Stok</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($products as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if ($product->images->first())
                                        <img src="{{ Storage::url($product->images->first()->image) }}" 
                                            alt="{{ $product->name }}"
                                            class="h-10 w-10 rounded-lg object-cover">
                                    @endif
                                    <span class="text-sm font-medium text-gray-900">{{ $product->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $product->category->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $product->variants->count() }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ number_format($product->total_stock) }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-xs font-medium
                                    {{ $product->stock_status_color == 'red' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $product->stock_status_color == 'yellow' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $product->stock_status_color == 'green' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $product->stock_status_color == 'gray' ? 'bg-gray-100 text-gray-700' : '' }}">
                                    <span class="inline-block h-2 w-2 rounded-full
                                        {{ $product->stock_status_color == 'red' ? 'bg-red-500' : '' }}
                                        {{ $product->stock_status_color == 'yellow' ? 'bg-yellow-500' : '' }}
                                        {{ $product->stock_status_color == 'green' ? 'bg-green-500' : '' }}
                                        {{ $product->stock_status_color == 'gray' ? 'bg-gray-400' : '' }}">
                                    </span>
                                    {{ $product->stock_status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.stock.edit', $product) }}"
                                        class="rounded-lg bg-blue-600 px-3 py-1 text-xs text-white hover:bg-blue-700">
                                        Edit Stok
                                    </a>
                                    <a href="{{ route('admin.stock.history', $product) }}"
                                        class="rounded-lg bg-gray-600 px-3 py-1 text-xs text-white hover:bg-gray-700">
                                        History
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-gray-500">
                                Tidak ada produk ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-gray-200 px-6 py-4">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection