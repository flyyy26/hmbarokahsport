<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Berhasil - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50 text-slate-900">

    @include('customer.partials.navbar')

    <main class="mx-auto max-w-2xl px-4 py-12 sm:px-6 lg:px-8">

        <div class="rounded-2xl border border-emerald-200 bg-white p-8 text-center shadow-sm">

            {{-- Icon --}}
            <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-emerald-100 text-5xl">
                ✅
            </div>

            <h1 class="mt-4 text-2xl font-bold text-slate-900">Pesanan Berhasil!</h1>
            <p class="mt-2 text-sm text-slate-500">Terima kasih telah berbelanja di {{ config('app.name') }}.</p>

            {{-- Order Info --}}
            <div class="mt-6 rounded-xl bg-slate-50 p-6 text-left">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-slate-500">No. Pesanan</p>
                        <p class="font-medium text-slate-900">{{ $order->order_number }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Total</p>
                        <p class="font-bold text-slate-900">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Status</p>
                        <p class="font-medium text-slate-900">{{ $order->status_label }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Pembayaran</p>
                        <p class="font-medium text-slate-900">{{ $order->payment_status_label }}</p>
                    </div>
                </div>
            </div>

            {{-- Order Items --}}
            <div class="mt-6 rounded-xl border border-slate-200 bg-white p-6 text-left">
                <h3 class="font-semibold text-slate-900">Item Pesanan</h3>
                <div class="mt-3 space-y-2">
                    @foreach ($order->items as $item)
                        <div class="flex justify-between text-sm border-b border-slate-100 pb-2 last:border-0">
                            <div>
                                <p class="font-medium text-slate-700">{{ $item->product_name }}</p>
                                @if ($item->variant_name)
                                    <p class="text-xs text-slate-400">{{ $item->variant_name }}</p>
                                @endif
                                <p class="text-xs text-slate-400">{{ $item->quantity }}x</p>
                            </div>
                            <p class="font-medium text-slate-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Actions --}}
            <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-center">
                <a href="{{ route('customer.orders') }}" 
                   class="rounded-xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                    Lihat Pesanan Saya
                </a>
                <a href="{{ route('customer.home') }}" 
                   class="rounded-xl border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Lanjut Belanja
                </a>
            </div>

        </div>

    </main>

    @include('customer.partials.footer')

</body>
</html>