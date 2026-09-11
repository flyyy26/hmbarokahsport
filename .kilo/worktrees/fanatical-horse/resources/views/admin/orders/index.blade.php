@extends('layouts.admin')

@section('content')

    <div class="ml-64 p-8">
        <div class="mx-auto max-w-7xl">

            {{-- Header --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-900">📦 Pesanan</h1>
                <p class="mt-1 text-sm text-slate-500">Kelola semua pesanan customer.</p>
            </div>

            {{-- Status Cards --}}
            <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-7">
                <div class="rounded-xl border border-slate-200 bg-white p-4 text-center">
                    <p class="text-2xl font-bold text-slate-900">{{ $statusCounts['total'] }}</p>
                    <p class="text-xs text-slate-500">Total</p>
                </div>
                <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-4 text-center">
                    <p class="text-2xl font-bold text-yellow-700">{{ $statusCounts['pending'] }}</p>
                    <p class="text-xs text-yellow-600">Menunggu</p>
                </div>
                <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 text-center">
                    <p class="text-2xl font-bold text-blue-700">{{ $statusCounts['processing'] }}</p>
                    <p class="text-xs text-blue-600">Diproses</p>
                </div>
                <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4 text-center">
                    <p class="text-2xl font-bold text-indigo-700">{{ $statusCounts['shipped'] }}</p>
                    <p class="text-xs text-indigo-600">Dikirim</p>
                </div>
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-center">
                    <p class="text-2xl font-bold text-emerald-700">{{ $statusCounts['delivered'] }}</p>
                    <p class="text-xs text-emerald-600">Selesai</p>
                </div>
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-center">
                    <p class="text-2xl font-bold text-red-700">{{ $statusCounts['cancelled'] }}</p>
                    <p class="text-xs text-red-600">Dibatalkan</p>
                </div>
                <div class="rounded-xl border border-orange-200 bg-orange-50 p-4 text-center">
                    <p class="text-2xl font-bold text-orange-700">{{ $statusCounts['unpaid'] }}</p>
                    <p class="text-xs text-orange-600">Belum Bayar</p>
                </div>
            </div>

            {{-- Filter & Search --}}
            <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.orders.index') }}" 
                       class="rounded-lg px-3 py-1.5 text-sm font-medium {{ !request('status') ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        Semua
                    </a>
                    <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" 
                       class="rounded-lg px-3 py-1.5 text-sm font-medium {{ request('status') == 'pending' ? 'bg-yellow-500 text-white' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' }}">
                        Menunggu
                    </a>
                    <a href="{{ route('admin.orders.index', ['status' => 'processing']) }}" 
                       class="rounded-lg px-3 py-1.5 text-sm font-medium {{ request('status') == 'processing' ? 'bg-blue-500 text-white' : 'bg-blue-100 text-blue-700 hover:bg-blue-200' }}">
                        Diproses
                    </a>
                    <a href="{{ route('admin.orders.index', ['status' => 'shipped']) }}" 
                       class="rounded-lg px-3 py-1.5 text-sm font-medium {{ request('status') == 'shipped' ? 'bg-indigo-500 text-white' : 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200' }}">
                        Dikirim
                    </a>
                    <a href="{{ route('admin.orders.index', ['status' => 'delivered']) }}" 
                       class="rounded-lg px-3 py-1.5 text-sm font-medium {{ request('status') == 'delivered' ? 'bg-emerald-500 text-white' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' }}">
                        Selesai
                    </a>
                    <a href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}" 
                       class="rounded-lg px-3 py-1.5 text-sm font-medium {{ request('status') == 'cancelled' ? 'bg-red-500 text-white' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                        Dibatalkan
                    </a>
                </div>

                <form method="GET" class="flex gap-2">
                    <input type="text" name="search" placeholder="Cari order atau customer..." 
                           value="{{ request('search') }}"
                           class="rounded-lg border border-slate-200 px-4 py-2 text-sm outline-none focus:border-blue-500">
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                        Cari
                    </button>
                </form>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white">
                <table class="w-full text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-slate-600">No. Order</th>
                            <th class="px-4 py-3 text-left font-medium text-slate-600">Customer</th>
                            <th class="px-4 py-3 text-left font-medium text-slate-600">Total</th>
                            <th class="px-4 py-3 text-left font-medium text-slate-600">Status</th>
                            <th class="px-4 py-3 text-left font-medium text-slate-600">Pembayaran</th>
                            <th class="px-4 py-3 text-left font-medium text-slate-600">Tanggal</th>
                            <th class="px-4 py-3 text-center font-medium text-slate-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr class="border-b border-slate-100 hover:bg-slate-50">
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $order->order_number }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-slate-900">{{ $order->customer->name ?? 'Guest' }}</p>
                                    <p class="text-xs text-slate-400">{{ $order->customer->email ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3 font-bold text-slate-900">
                                    Rp {{ number_format($order->total, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->status == 'processing') bg-blue-100 text-blue-800
                                        @elseif($order->status == 'shipped') bg-indigo-100 text-indigo-800
                                        @elseif($order->status == 'delivered') bg-emerald-100 text-emerald-800
                                        @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        @if($order->payment_status == 'paid') bg-emerald-100 text-emerald-800
                                        @elseif($order->payment_status == 'unpaid') bg-orange-100 text-orange-800
                                        @elseif($order->payment_status == 'failed') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $order->payment_status_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-500">
                                    {{ $order->created_at->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('admin.orders.show', $order) }}" 
                                       class="text-blue-600 hover:text-blue-800">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                                    Belum ada pesanan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $orders->links() }}
            </div>

        </div>
    </div>

@endsection