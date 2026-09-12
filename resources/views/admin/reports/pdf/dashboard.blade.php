<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 20px; }
        h1 { font-size: 14px; font-weight: bold; margin-bottom: 5px; }
        h2 { font-size: 11px; font-weight: bold; margin: 10px 0 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #ddd; padding: 4px 6px; text-align: left; }
        th { background: #f5f5f5; font-size: 9px; font-weight: bold; }
        .text-right { text-align: right; }
        .summary-box { background: #f9f9f9; padding: 8px; border-radius: 4px; margin-bottom: 10px; }
        .label { font-weight: bold; }
    </style>
</head>
<body>

    @php
        $setting = \App\Models\Setting::first();
    @endphp

    <h1>{{ $setting->store_name ?? 'Toko Online' }} - Laporan Penjualan</h1>
    <p>Periode: {{ $start->format('d/m/Y') }} - {{ $end->format('d/m/Y') }}</p>

    {{-- Ringkasan --}}
    <div class="summary-box">
        <h2>Ringkasan</h2>
        <table>
            <tr>
                <td class="label">Total Penjualan Online</td>
                <td class="text-right">Rp {{ number_format($onlineRevenue, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Total Penjualan Offline</td>
                <td class="text-right">Rp {{ number_format($offlineRevenue, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label"><strong>Total Keseluruhan</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($onlineRevenue + $offlineRevenue, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>

    {{-- Pesanan Online --}}
    <h2>Pesanan Online ({{ $onlineOrders->count() }})</h2>
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>No. Order</th>
                <th>Tanggal</th>
                <th>Customer</th>
                <th>Status</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($onlineOrders as $order)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $order->shipping_name ?? $order->user->name ?? 'Guest' }}</td>
                    <td>{{ $order->payment_status_label }}</td>
                    <td class="text-right">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Pesanan Offline --}}
    <h2>Pesanan Offline ({{ $offlineOrders->count() }})</h2>
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>No. Order</th>
                <th>Tanggal</th>
                <th>Customer</th>
                <th>Status</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($offlineOrders as $order)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $order->customer_name ?? 'Walk-in' }}</td>
                    <td>{{ $order->payment_status_label ?? ucfirst($order->payment_status) }}</td>
                    <td class="text-right">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top: 10px; font-size: 8px; color: #999;">Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>

</body>
</html>
