@extends('layouts.account')

@section('title', 'Detail Pesanan #' . $order->order_number . ' - Barokah Sport')
@section('page-title', '📋 Detail Pesanan')
@section('page-subtitle', '#' . $order->order_number)

@section('account-content')

<div class="space-y-6">
    {{-- Status --}}
    <div class="flex flex-wrap items-center gap-4">
        <span class="rounded-full px-3 py-1 text-sm font-medium
            @if($order->status == 'delivered' || $order->status == 'completed') bg-emerald-100 text-emerald-700
            @elseif($order->status == 'cancelled') bg-red-100 text-red-700
            @elseif($order->status == 'shipped') bg-blue-100 text-blue-700
            @elseif($order->status == 'processing') bg-indigo-100 text-indigo-700
            @else bg-yellow-100 text-yellow-700 @endif">
            {{ ucfirst($order->status ?? 'Pending') }}
        </span>
        <span class="rounded-full px-3 py-1 text-sm font-medium
            @if($order->payment_status == 'paid') bg-emerald-100 text-emerald-700
            @elseif($order->payment_status == 'unpaid') bg-orange-100 text-orange-700
            @else bg-red-100 text-red-700 @endif">
            {{ $order->payment_status == 'paid' ? '✅ Lunas' : '⏳ Belum Bayar' }}
        </span>
    </div>

    {{-- Items --}}
    <div>
        <h3 class="text-sm font-semibold text-gray-700 mb-3">🛍️ Item Pesanan</h3>
        <div class="space-y-3">
            @foreach ($order->items as $item)
                <div class="flex items-center justify-between border-b border-gray-100 pb-3 last:border-0">
                    <div class="flex items-center gap-4">
                        <div class="h-16 w-16 rounded-lg bg-gray-100 overflow-hidden shrink-0">
                            @if ($item->product && $item->product->images->first())
                                <img src="{{ Storage::url($item->product->images->first()->image) }}" 
                                     alt="{{ $item->product_name }}" 
                                     class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full items-center justify-center text-2xl text-gray-300">📦</div>
                            @endif
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $item->product_name }}</p>
                            @if ($item->variant_name)
                                <p class="text-xs text-gray-500">Varian: {{ $item->variant_name }}</p>
                            @endif
                            <p class="text-xs text-gray-400">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <p class="font-semibold text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                </div>
            @endforeach
        </div>

        {{-- Total --}}
        <div class="mt-4 border-t border-gray-200 pt-4 space-y-1 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Subtotal</span>
                <span class="text-gray-700">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Ongkir</span>
                <span class="text-gray-700">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
            </div>
            @if ($order->discount > 0)
                <div class="flex justify-between">
                    <span class="text-gray-500">Diskon</span>
                    <span class="text-red-500">-Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="flex justify-between border-t border-gray-200 pt-2 text-base font-bold">
                <span>Total</span>
                <span class="text-gray-900">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Alamat Pengiriman --}}
    <div>
        <h3 class="text-sm font-semibold text-gray-700 mb-2">📍 Alamat Pengiriman</h3>
        <div class="text-sm text-gray-600 bg-gray-50 rounded-lg p-4">
            <p class="font-medium text-gray-900">{{ $order->shipping_name }}</p>
            <p>{{ $order->shipping_phone }}</p>
            <p class="mt-2">{{ $order->shipping_address }}</p>
            <p>{{ $order->shipping_district ?? '' }}, {{ $order->shipping_city }}</p>
            <p>{{ $order->shipping_province }}</p>
            @if ($order->shipping_postal_code)
                <p>Kode Pos: {{ $order->shipping_postal_code }}</p>
            @endif
        </div>
    </div>

    {{-- Informasi Pengiriman --}}
    @if ($order->courier)
        <div>
            <h3 class="text-sm font-semibold text-gray-700 mb-2">🚚 Informasi Pengiriman</h3>
            <div class="text-sm text-gray-600 bg-gray-50 rounded-lg p-4">
                <p><span class="text-gray-500">Kurir:</span> {{ strtoupper($order->courier) }}</p>
                @if ($order->service)
                    <p><span class="text-gray-500">Layanan:</span> {{ $order->service }}</p>
                @endif
                @if ($order->tracking_number)
                    <p><span class="text-gray-500">No. Resi:</span> <strong class="text-gray-900">{{ $order->tracking_number }}</strong></p>
                @endif
            </div>
        </div>
    @endif

    {{-- Timeline --}}
    <div>
        <h3 class="text-sm font-semibold text-gray-700 mb-2">📊 Timeline</h3>
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <div class="h-3 w-3 rounded-full bg-emerald-500"></div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Pesanan Dibuat</p>
                    <p class="text-xs text-gray-400">{{ $order->created_at->format('d M Y, H:i') }}</p>
                </div>
            </div>
            @if ($order->paid_at)
                <div class="flex items-center gap-3">
                    <div class="h-3 w-3 rounded-full bg-emerald-500"></div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Pembayaran Dikonfirmasi</p>
                        <p class="text-xs text-gray-400">{{ $order->paid_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            @endif
            @if ($order->shipped_at)
                <div class="flex items-center gap-3">
                    <div class="h-3 w-3 rounded-full bg-blue-500"></div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Pesanan Dikirim</p>
                        <p class="text-xs text-gray-400">{{ $order->shipped_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            @endif
            @if ($order->delivered_at)
                <div class="flex items-center gap-3">
                    <div class="h-3 w-3 rounded-full bg-emerald-500"></div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Pesanan Selesai</p>
                        <p class="text-xs text-gray-400">{{ $order->delivered_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            @endif
            @if ($order->cancelled_at)
                <div class="flex items-center gap-3">
                    <div class="h-3 w-3 rounded-full bg-red-500"></div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Pesanan Dibatalkan</p>
                        <p class="text-xs text-gray-400">{{ $order->cancelled_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Catatan --}}
    @if ($order->notes)
        <div>
            <h3 class="text-sm font-semibold text-gray-700 mb-2">📝 Catatan</h3>
            <p class="text-sm text-gray-600 bg-gray-50 rounded-lg p-4">{{ $order->notes }}</p>
        </div>
    @endif

    <div class="mt-4">
        <a href="{{ route('customer.orders') }}" class="text-sm text-blue-600 hover:text-blue-800">
            ← Kembali ke Riwayat Pesanan
        </a>
    </div>
</div>

@endsection