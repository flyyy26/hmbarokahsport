@extends('layouts.account')

@section('title', 'Detail Pesanan #' . $order->order_number . ' - Barokah Sport')
@section('page-title', '📋 Detail Pesanan')
@section('page-subtitle', '#' . $order->order_number)

@section('account-content')

<div class="space-y-6">
    {{-- Status --}}
    <div class="flex flex-wrap items-center gap-4">
        <span class="rounded-full px-3 py-1 text-sm font-medium
            @if($order->shipping_status == 'delivered') bg-emerald-100 text-emerald-700
            @elseif($order->shipping_status == 'cancelled') bg-red-100 text-red-700
            @elseif($order->shipping_status == 'shipped') bg-blue-100 text-blue-700
            @elseif($order->shipping_status == 'processing') bg-indigo-100 text-indigo-700
            @else bg-yellow-100 text-yellow-700 @endif">
            {{ $order->shipping_status_label }}
        </span>
        <span class="rounded-full px-3 py-1 text-sm font-medium
            @if($order->payment_status == 'paid') bg-emerald-100 text-emerald-700
            @elseif($order->payment_status == 'unpaid') bg-orange-100 text-orange-700
            @else bg-red-100 text-red-700 @endif">
            {{ $order->payment_status == 'paid' ? '✅ Lunas' : '⏳ Belum Bayar' }}
        </span>
    </div>

    @if($order->biteship_order_id)
        <a href="{{ route('customer.orders.tracking', $order) }}" 
        class="rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-700">
            <iconify-icon icon="mdi:truck-fast-outline"></iconify-icon>
            Lacak Pengiriman
        </a>
    @endif

    @if($order->canBeCancelled() && $order->cancellation_status !== 'pending')
        <button onclick="showCancelModal()" 
                class="rounded-lg border border-red-600 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 transition">
            <iconify-icon icon="mdi:close-circle-outline" class="mr-1"></iconify-icon>
            Batalkan Pesanan
        </button>
    @endif

    @if($order->cancellation_status === 'pending')
        <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-sm font-medium text-yellow-800">
            ⏳ Menunggu Persetujuan Admin
        </span>
    @endif

    @if($order->shipping_status === 'cancelled')
        <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-800">
            ❌ Dibatalkan
        </span>
        @if($order->cancellation_reason)
            <p class="mt-2 text-sm text-gray-500">
                <strong>Alasan:</strong> {{ $order->cancellation_reason }}
            </p>
        @endif
    @endif

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

<div id="cancelModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
    <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
        <h3 class="text-lg font-semibold text-gray-900">Batalkan Pesanan</h3>
        <p class="mt-1 text-sm text-gray-500">
            Anda yakin ingin membatalkan pesanan #{{ $order->order_number }}?
        </p>
        
        <form action="{{ route('customer.orders.request-cancel', $order) }}" method="POST" class="mt-4">
            @csrf
            <div>
                <label class="text-sm font-medium text-gray-700">Alasan Pembatalan</label>
                <textarea name="reason" rows="3" required
                          class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"
                          placeholder="Tuliskan alasan pembatalan..."></textarea>
                <p class="mt-1 text-xs text-gray-400">Minimal 10 karakter</p>
            </div>
            
            <div class="mt-4 flex gap-3">
                <button type="button" onclick="closeCancelModal()"
                        class="flex-1 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                    Ya, Batalkan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function showCancelModal() {
        document.getElementById('cancelModal').classList.remove('hidden');
        document.getElementById('cancelModal').classList.add('flex');
    }

    function closeCancelModal() {
        document.getElementById('cancelModal').classList.add('hidden');
        document.getElementById('cancelModal').classList.remove('flex');
    }

    // Click outside to close
    document.getElementById('cancelModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeCancelModal();
        }
    });
</script>

@endsection