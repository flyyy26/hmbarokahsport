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
                <div class="flex gap-2">
                    <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank" 
                       class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        🖨️ Invoice
                    </a>
                </div>
            </div>

            {{-- Status Summary --}}
            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-xl border border-slate-200 bg-white p-4">
                    <p class="text-xs text-slate-400">Status Pesanan</p>
                    <p class="mt-1 text-lg font-semibold">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                            @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($order->status == 'processing') bg-blue-100 text-blue-800
                            @elseif($order->status == 'shipped') bg-indigo-100 text-indigo-800
                            @elseif($order->status == 'delivered') bg-emerald-100 text-emerald-800
                            @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $order->status_label }}
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

                    {{-- Customer Info --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-6">
                        <h2 class="font-semibold text-slate-900">Informasi Customer</h2>
                        <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-slate-500">Nama</p>
                                <p class="font-medium text-slate-900">{{ $order->customer->name ?? 'Guest' }}</p>
                            </div>
                            <div>
                                <p class="text-slate-500">Email</p>
                                <p class="font-medium text-slate-900">{{ $order->customer->email ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-slate-500">Telepon</p>
                                <p class="font-medium text-slate-900">{{ $order->customer->phone ?? '-' }}</p>
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
                                <p class="text-xs text-slate-400">Catatan Customer:</p>
                                <p class="text-sm text-slate-600">{{ $order->notes }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Update Status --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-6">
                        <h2 class="font-semibold text-slate-900">Update Status</h2>

                        {{-- Status Pesanan --}}
                        <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="mt-4">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="text-sm font-medium text-slate-700">Status Pesanan</label>
                                <select name="status" class="mt-1 block w-full rounded-lg border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Menunggu</option>
                                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Diproses</option>
                                    <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Dikirim</option>
                                    <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Selesai</option>
                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                </select>
                            </div>
                            <div class="mt-3">
                                <label class="text-sm font-medium text-slate-700">Catatan Admin</label>
                                <textarea name="admin_notes" rows="2" class="mt-1 block w-full rounded-lg border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">{{ $order->admin_notes }}</textarea>
                            </div>
                            <button type="submit" class="mt-3 w-full rounded-lg bg-blue-600 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                Update Status
                            </button>
                        </form>
                    </div>

                    {{-- Update Payment --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-6">
                        <h2 class="font-semibold text-slate-900">💳 Pembayaran</h2>
                        <form action="{{ route('admin.orders.payment', $order) }}" method="POST" class="mt-4">
                            @csrf
                            @method('PUT')
                            <select name="payment_status" class="block w-full rounded-lg border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="unpaid" {{ $order->payment_status == 'unpaid' ? 'selected' : '' }}>Belum Bayar</option>
                                <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Lunas</option>
                                <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>Gagal</option>
                                <option value="refunded" {{ $order->payment_status == 'refunded' ? 'selected' : '' }}>Dikembalikan</option>
                            </select>
                            <button type="submit" class="mt-3 w-full rounded-lg bg-emerald-600 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                                Update Pembayaran
                            </button>
                        </form>
                        @if ($order->paid_at)
                            <p class="mt-2 text-xs text-slate-400">Dibayar: {{ $order->paid_at->format('d M Y H:i') }}</p>
                        @endif
                    </div>

                    {{-- Update Shipping --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-6">
                        <h2 class="font-semibold text-slate-900">🚚 Pengiriman</h2>
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
                                <label class="text-sm font-medium text-slate-700">No. Resi</label>
                                <input type="text" name="tracking_number" value="{{ $order->tracking_number }}" 
                                       class="mt-1 block w-full rounded-lg border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-700">Status Pengiriman</label>
                                <select name="shipping_status" class="mt-1 block w-full rounded-lg border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="pending" {{ $order->shipping_status == 'pending' ? 'selected' : '' }}>Menunggu</option>
                                    <option value="processing" {{ $order->shipping_status == 'processing' ? 'selected' : '' }}>Diproses</option>
                                    <option value="shipped" {{ $order->shipping_status == 'shipped' ? 'selected' : '' }}>Dikirim</option>
                                    <option value="delivered" {{ $order->shipping_status == 'delivered' ? 'selected' : '' }}>Terkirim</option>
                                </select>
                            </div>
                            <button type="submit" class="w-full rounded-lg bg-indigo-600 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                                Update Pengiriman
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