@extends('layouts.account')

@section('title', 'Riwayat Pesanan - Barokah Sport')
@section('page-title', 'Riwayat Pesanan')
@section('page-subtitle', 'Lihat semua pesanan yang pernah kamu buat.')

@section('account-content')

@if ($orders->isEmpty())
    <div class="text-center py-10">
        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-gray-100 text-4xl">
            📦
        </div>
        <h3 class="mt-4 text-lg font-semibold text-gray-900">Belum Ada Pesanan</h3>
        <p class="mt-2 text-sm text-gray-500">
            Kamu belum melakukan pemesanan apapun. Yuk, mulai belanja sekarang!
        </p>
        <a href="{{ route('customer.products.index') }}" 
           class="mt-6 inline-block rounded-xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
            Mulai Belanja
        </a>
    </div>
@else
    <div class="space-y-4">
        @foreach ($orders as $order)
            <div class="rounded-xl border border-gray-200 p-5 transition hover:shadow-md">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3 flex-wrap">
                            <span class="text-sm font-semibold text-gray-900">
                                #{{ $order->order_number }}
                            </span>
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-medium
                                @if($order->status == 'delivered' || $order->status == 'completed') bg-emerald-100 text-emerald-700
                                @elseif($order->status == 'cancelled') bg-red-100 text-red-700
                                @elseif($order->status == 'shipped') bg-blue-100 text-blue-700
                                @elseif($order->status == 'processing') bg-indigo-100 text-indigo-700
                                @else bg-yellow-100 text-yellow-700 @endif">
                                {{ ucfirst($order->status ?? 'Pending') }}
                            </span>
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-medium
                                @if($order->payment_status == 'paid') bg-emerald-100 text-emerald-700
                                @elseif($order->payment_status == 'unpaid') bg-orange-100 text-orange-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ $order->payment_status == 'paid' ? '✅ Lunas' : '⏳ Belum Bayar' }}
                            </span>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ $order->created_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-gray-900">
                            Rp {{ number_format($order->total ?? 0, 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $order->items->count() ?? 0 }} produk
                        </p>
                    </div>
                </div>

                {{-- Order Items Preview --}}
                <div class="mt-4 border-t border-gray-100 pt-4">
                    <div class="flex items-center gap-4 overflow-x-auto pb-2">
                        @foreach ($order->items->take(3) as $item)
                            <div class="flex shrink-0 items-center gap-3">
                                <div class="h-12 w-12 rounded-lg bg-gray-100 overflow-hidden shrink-0">
                                    @if ($item->product && $item->product->images->first())
                                        <img src="{{ Storage::url($item->product->images->first()->image) }}" 
                                             alt="{{ $item->product_name }}" 
                                             class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full items-center justify-center text-xl text-gray-300">📦</div>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700 truncate max-w-[120px]">{{ $item->product_name }}</p>
                                    <p class="text-xs text-gray-400">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @endforeach
                        @if ($order->items->count() > 3)
                            <span class="shrink-0 text-sm font-medium text-gray-400">
                                +{{ $order->items->count() - 3 }} lainnya
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Action --}}
                <div class="mt-4 flex gap-3">
                    <a href="{{ route('customer.orders.show', $order) }}" 
                       class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Lihat Detail
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $orders->links() }}
    </div>
@endif

@endsection