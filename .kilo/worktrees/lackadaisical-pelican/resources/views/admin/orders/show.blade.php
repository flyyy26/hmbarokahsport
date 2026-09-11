@extends('layouts.admin')

@section('content')

    <div class="ml-64 p-8">
        <div class="mx-auto max-w-5xl">

            {{-- Header --}}
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <a href="{{ route('admin.orders.index') }}" class="text-sm text-blue-600 hover:text-blue-800">← Kembali</a>
                    <h1 class="mt-1 text-2xl font-bold text-slate-900">Detail Pesanan</h1>
                    <p class="text-sm text-slate-500">#{{ $order->order_number }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.orders.label', $order) }}" target="_blank" 
                    class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        🏷️ Print Label
                    </a>
                    {{-- 🔥 TOMBOL TRACKING SELALU MUNCUL --}}
                    <a href="{{ route('admin.orders.tracking', $order) }}" 
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                        📦 Tracking
                    </a>
                </div>
                @if($order->cancellation_status === 'pending')
                    <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-yellow-800">
                                    ⏳ Permintaan Pembatalan
                                </p>
                                <p class="text-sm text-yellow-700">
                                    {{ $order->cancellation_reason }}
                                </p>
                                <p class="text-xs text-yellow-600 mt-1">
                                    Diminta: {{ $order->cancellation_requested_at->format('d M Y H:i') }}
                                </p>
                            </div>
                            <div class="flex gap-2">
                                <form action="{{ route('admin.orders.approve-cancellation', $order) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                                        ✅ Setujui
                                    </button>
                                </form>
                                <form action="{{ route('admin.orders.reject-cancellation', $order) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                                        ❌ Tolak
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                @if($order->shipping_status === 'cancelled')
                    <div class="rounded-xl border border-red-200 bg-red-50 p-4">
                        <p class="text-sm font-medium text-red-800">❌ Pesanan Dibatalkan</p>
                        @if($order->cancellation_reason)
                            <p class="text-sm text-red-700 mt-1">Alasan: {{ $order->cancellation_reason }}</p>
                        @endif
                        <p class="text-xs text-red-600 mt-1">
                            Dibatalkan: {{ $order->cancelled_at->format('d M Y H:i') }}
                        </p>
                    </div>
                @endif
            </div>

            

            {{-- Status Summary --}}
            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-xl border border-slate-200 bg-white p-4">
                    <p class="text-xs text-slate-400">Status Pesanan</p>
                        <p class="mt-1 text-lg font-semibold">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                @if($order->shipping_status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->shipping_status == 'processing') bg-blue-100 text-blue-800
                                @elseif($order->shipping_status == 'shipped') bg-indigo-100 text-indigo-800
                                @elseif($order->shipping_status == 'delivered') bg-emerald-100 text-emerald-800
                                @elseif($order->shipping_status == 'cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $order->shipping_status_label }}
                            </span>
                        </p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-4">
                    <p class="text-xs text-slate-400">Status Pembayaran</p>
                    <p class="mt-1 text-lg font-semibold">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                            @if($order->payment_status == 'paid') bg-emerald-100 text-emerald-800
                            @elseif($order->payment_status == 'unpaid') bg-orange-100 text-orange-800
                            @elseif($order->payment_status == 'failed') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $order->payment_status_label }}
                        </span>
                    </p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-4">
                    <p class="text-xs text-slate-400">Status Pengiriman</p>
                    <p class="mt-1 text-lg font-semibold">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                            @if($order->shipping_status == 'delivered') bg-emerald-100 text-emerald-800
                            @elseif($order->shipping_status == 'shipped') bg-indigo-100 text-indigo-800
                            @elseif($order->shipping_status == 'processing') bg-blue-100 text-blue-800
                            @else bg-yellow-100 text-yellow-800 @endif">
                            {{ $order->shipping_status_label }}
                        </span>
                    </p>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">

                {{-- Left Column --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Order Items --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-6">
                        <h2 class="font-semibold text-slate-900">Item Pesanan</h2>
                        <div class="mt-4 space-y-3">
                            @foreach ($order->items as $item)
                                <div class="flex items-center justify-between border-b border-slate-100 pb-3 last:border-0">
                                    <div class="flex items-center gap-4">
                                        <div class="h-14 w-14 rounded-lg bg-slate-100 overflow-hidden">
                                            @if ($item->product && $item->product->images->first())
                                                <img src="{{ Storage::url($item->product->images->first()->image) }}" 
                                                     alt="{{ $item->product_name }}" 
                                                     class="h-full w-full object-cover">
                                            @else
                                                <div class="flex h-full items-center justify-center text-2xl text-slate-300">📦</div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-medium text-slate-900">{{ $item->product_name }}</p>
                                            @if ($item->variant_name)
                                                <p class="text-xs text-slate-500">Varian: {{ $item->variant_name }}</p>
                                            @endif
                                            <p class="text-xs text-slate-400">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                    <p class="font-semibold text-slate-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 border-t border-slate-200 pt-4 space-y-1">
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500">Subtotal</span>
                                <span class="text-slate-700">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500">Ongkir</span>
                                <span class="text-slate-700">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                            </div>
                            @if ($order->discount > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-500">Diskon</span>
                                    <span class="text-red-500">-Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between border-t border-slate-200 pt-2 text-base font-bold">
                                <span>Total</span>
                                <span class="text-slate-900">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- User Info --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-6">
                        <h2 class="font-semibold text-slate-900">Informasi User</h2>
                        <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-slate-500">Nama</p>
                                <p class="font-medium text-slate-900">{{ $order->user->name ?? 'Guest' }}</p>
                            </div>
                            <div>
                                <p class="text-slate-500">Email</p>
                                <p class="font-medium text-slate-900">{{ $order->user->email ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-slate-500">Telepon</p>
                                <p class="font-medium text-slate-900">{{ $order->user->phone ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-slate-500">Tanggal Order</p>
                                <p class="font-medium text-slate-900">{{ $order->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Right Column --}}
                <div class="space-y-6">

                    {{-- Shipping Address --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-6">
                        <h2 class="font-semibold text-slate-900">📍 Alamat Pengiriman</h2>
                        <div class="mt-3 text-sm text-slate-600">
                            <p class="font-medium text-slate-900">{{ $order->shipping_name }}</p>
                            <p>{{ $order->shipping_phone }}</p>
                            <p class="mt-2">{{ $order->shipping_address }}</p>
                            <p>{{ $order->shipping_city }}, {{ $order->shipping_province }}</p>
                            <p>{{ $order->shipping_postal_code }}</p>
                        </div>
                        @if ($order->notes)
                            <div class="mt-3 border-t border-slate-100 pt-3">
                                <p class="text-xs text-slate-400">Catatan User:</p>
                                <p class="text-sm text-slate-600">{{ $order->notes }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Atur Pengiriman --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-6">
                        <h2 class="font-semibold text-slate-900">🚚 Atur Pengiriman</h2>
                        <form action="{{ route('admin.orders.shipping', $order) }}" method="POST" class="mt-4 space-y-3">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="text-sm font-medium text-slate-700">Kurir</label>
                                <input type="text" name="courier" value="{{ $order->courier }}" 
                                       class="mt-1 block w-full rounded-lg border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-700">Layanan</label>
                                <input type="text" name="service" value="{{ $order->service }}" 
                                       class="mt-1 block w-full rounded-lg border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-700">No. Resi <span class="text-xs text-slate-400">(opsional)</span></label>
                                <input type="text" name="tracking_number" value="{{ $order->tracking_number }}" 
                                       class="mt-1 block w-full rounded-lg border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-1 text-xs text-slate-400">Kosongkan jika ingin menggunakan resi otomatis dari Biteship.</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-700">Status Pengiriman</label>
                                <select name="shipping_status" class="mt-1 block w-full rounded-lg border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                                    @php
                                        $defaultShippingStatus = $order->shipping_status;
                                        if ($order->payment_status === 'paid' && $order->shipping_status === 'pending') {
                                            $defaultShippingStatus = 'processing';
                                        }
                                    @endphp
                                    <option value="pending" {{ $defaultShippingStatus == 'pending' ? 'selected' : '' }}>Belum Bayar</option>
                                    <option value="processing" {{ $defaultShippingStatus == 'processing' ? 'selected' : '' }}>Sedang Dikemas</option>
                                    <option value="shipped" {{ $defaultShippingStatus == 'shipped' ? 'selected' : '' }}>Dikirim</option>
                                    <option value="delivered" {{ $defaultShippingStatus == 'delivered' ? 'selected' : '' }}>Terkirim</option>
                                </select>
                                <p class="mt-1 text-xs text-slate-400">
                                    @if($order->shipping_status == 'pending') Menunggu pembayaran.
                                    @elseif($order->shipping_status == 'processing') Sedang disiapkan untuk dikirim.
                                    @elseif($order->shipping_status == 'shipped') Paket dalam perjalanan.
                                    @elseif($order->shipping_status == 'delivered') Paket telah diterima.
                                    @endif
                                </p>
                            </div>
                            <button type="submit" class="w-full rounded-lg bg-indigo-600 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                                Simpan Pengiriman
                            </button>
                        </form>
                        @if ($order->shipped_at)
                            <p class="mt-2 text-xs text-slate-400">Dikirim: {{ $order->shipped_at->format('d M Y H:i') }}</p>
                        @endif
                        @if ($order->delivered_at)
                            <p class="mt-1 text-xs text-slate-400">Terkirim: {{ $order->delivered_at->format('d M Y H:i') }}</p>
                        @endif
                    </div>

                </div>

            </div>

        </div>
    </div>

@endsection