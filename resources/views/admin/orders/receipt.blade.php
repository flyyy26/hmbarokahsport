<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Nota - {{ $order->order_number ?? '' }}</title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica Neue', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #000000;
            background: #ffffff;
            font-size: 10pt;
            line-height: 1.4;
        }
        .page {
            width: 100%;
            margin: 0 auto;
        }

        /* Header */
        .receipt-header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 4mm;
            margin-bottom: 4mm;
        }
        .store-logo {
            max-height: 24mm;
            max-width: 60mm;
            height: auto;
            object-fit: contain;
            display: block;
            margin: 0 auto 2mm;
        }
        .store-name {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .store-info {
            font-size: 7pt;
            color: #333;
            margin-top: 2mm;
        }

        /* Order Info */
        .order-info {
            margin-bottom: 4mm;
        }
        .order-info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .order-info-table td {
            padding: 1mm 0;
            vertical-align: top;
        }
        .order-info-label {
            font-size: 7pt;
            color: #666;
            width: 30%;
        }
        .order-info-value {
            font-size: 8pt;
            font-weight: bold;
            width: 70%;
        }

        /* Customer Info */
        .customer-info {
            border: 1px solid #ccc;
            border-radius: 4pt;
            padding: 3mm;
            margin-bottom: 4mm;
        }
        .customer-info-title {
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2mm;
            color: #555;
        }
        .customer-info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .customer-info-table td {
            padding: 0.5mm 0;
            vertical-align: top;
        }
        .customer-info-table .info-label {
            font-size: 7pt;
            color: #666;
            width: 25%;
        }
        .customer-info-table .info-value {
            font-size: 8pt;
            width: 75%;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4mm;
        }
        .items-table th {
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #000;
            border-bottom: 1px solid #000;
            padding: 2mm 3mm;
            text-align: center;
        }
        .items-table .th-qty { width: 8%; }
        .items-table .th-product { width: 45%; }
        .items-table .th-variant { width: 15%; }
        .items-table .th-sku { width: 12%; }
        .items-table .th-price { width: 10%; }
        .items-table .th-subtotal { width: 10%; }

        .items-table td {
            font-size: 8pt;
            padding: 1.5mm 3mm;
            border-bottom: 0.5px solid #ccc;
            vertical-align: top;
        }
        .items-table td.center { text-align: center; }
        .items-table td.right { text-align: right; }
        .items-table tr:last-child td {
            border-bottom: none;
        }
        .variant-name {
            font-size: 7pt;
            color: #555;
        }

        /* Totals */
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            border-top: 2px solid #000;
        }
        .totals-table td {
            padding: 1mm 3mm;
            font-size: 8pt;
        }
        .totals-label {
            text-align: right;
            color: #555;
        }
        .totals-value {
            text-align: right;
            font-weight: bold;
            width: 35%;
        }
        .totals-total td {
            border-top: 2px solid #000;
            font-weight: bold;
        }
        .totals-total .value {
            font-size: 10pt;
            color: #000;
        }

        /* Footer */
        .receipt-footer {
            border-top: 1px dashed #999;
            padding-top: 3mm;
            text-align: center;
            font-size: 6.5pt;
            color: #777;
        }
        .payment-method-badge {
            display: inline-block;
            background: #000;
            color: #fff;
            font-size: 7pt;
            font-weight: bold;
            padding: 1mm 4mm;
            border-radius: 3pt;
            margin-top: 1mm;
        }
    </style>
</head>
<body>

<div class="page">

    {{-- HEADER --}}
    <div class="receipt-header">
        @php
            $logoBase64 = null;
            if ($setting && $setting->logo) {
                $logoPath = public_path('storage/' . $setting->logo);
                if (file_exists($logoPath)) {
                    $logoData = file_get_contents($logoPath);
                    if ($logoData !== false) {
                        $logoMime = mime_content_type($logoPath) ?: 'image/png';
                        $logoBase64 = 'data:' . $logoMime . ';base64,' . base64_encode($logoData);
                    }
                }
            }
        @endphp

        @if($logoBase64)
            <img src="{{ $logoBase64 }}" class="store-logo"
                 alt="{{ $setting->store_name ?? 'Toko' }}">
        @else
            <div class="store-name">{{ $setting->store_name ?? 'Toko' }}</div>
        @endif

        @if($setting)
            <div class="store-info">
                {{ $setting->address ?? '' }}<br>
                Telp: {{ $setting->phone ?? '-' }} | WhatsApp: {{ $setting->whatsapp ?? '-' }}<br>
                Email: {{ $setting->email ?? '-' }}
            </div>
        @endif
    </div>

    {{-- ORDER INFO --}}
    <div class="order-info">
        <table class="order-info-table">
            <tr>
                <td class="order-info-label">No. Nota</td>
                <td class="order-info-value">{{ $order->order_number ?? '-' }}</td>
                <td class="order-info-label">Tanggal</td>
                <td class="order-info-value">
                    {{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}
                </td>
            </tr>
            <tr>
                <td class="order-info-label">Kasir</td>
                <td class="order-info-value">{{ auth()->user()->name ?? '-' }}</td>
                <td class="order-info-label">Status</td>
                <td class="order-info-value">{{ $order->payment_status_label ?? '-' }}</td>
            </tr>
        </table>
    </div>

    {{-- CUSTOMER INFO --}}
    <div class="customer-info">
        <div class="customer-info-title">Pelanggan / Penerima</div>
        <table class="customer-info-table">
            <tr>
                <td class="info-label">Nama</td>
                <td class="info-value">{{ $order->shipping_name ?? 'Walk-in Customer' }}</td>
            </tr>
            <tr>
                <td class="info-label">No. HP</td>
                <td class="info-value">{{ $order->shipping_phone ?? '-' }}</td>
            </tr>
            @if($order->shipping_address && $order->shipping_address !== '-')
            <tr>
                <td class="info-label">Alamat</td>
                <td class="info-value">{{ $order->shipping_address }}</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- ITEMS TABLE --}}
    <table class="items-table">
        <thead>
            <tr>
                <th class="th-qty">Qty</th>
                <th class="th-product">Produk</th>
                <th class="th-variant">Varian</th>
                <th class="th-sku">SKU</th>
                <th class="th-price">Harga</th>
                <th class="th-subtotal">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td class="center">{{ $item->quantity }}x</td>
                    <td>{{ $item->product_name }}</td>
                    <td>
                        @if($item->variant_name)
                            <span class="variant-name">{{ $item->variant_name }}</span>
                        @else
                            <span class="variant-name" style="color: #999;">-</span>
                        @endif
                    </td>
                    <td>{{ $item->sku ?? '-' }}</td>
                    <td class="right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- TOTALS --}}
    <table class="totals-table">
        <tr>
            <td class="totals-label">Subtotal</td>
            <td class="totals-value">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
        </tr>
        @if($order->discount > 0)
        <tr>
            <td class="totals-label">Diskon (Item)</td>
            <td class="totals-value">- Rp {{ number_format($order->discount, 0, ',', '.') }}</td>
        </tr>
        @endif
        @if($order->transaction_discount > 0)
        <tr>
            <td class="totals-label">
                Diskon ({{ $order->transaction_discount_type === 'percentage' ? 'Persen' : 'Nominal' }})
            </td>
            <td class="totals-value">- Rp {{ number_format($order->transaction_discount, 0, ',', '.') }}</td>
        </tr>
        @endif
        @if($order->shipping_cost > 0)
        <tr>
            <td class="totals-label">Ongkir</td>
            <td class="totals-value">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr class="totals-total">
            <td class="totals-label">TOTAL</td>
            <td class="totals-value value">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
        </tr>
    </table>

    {{-- PAYMENT METHOD --}}
    <div style="text-align: center; margin-top: 4mm;">
        <span class="payment-method-badge">
            {{ match($order->payment_method) {
                'cash' => 'TUNAI',
                'transfer' => 'TRANSFER',
                'qris' => 'QRIS',
                default => strtoupper($order->payment_method ?? '-'),
            } }}
        </span>
    </div>

    {{-- NOTES --}}
    @if($order->notes)
    <div style="margin-top: 4mm; padding: 2mm; border: 1px dashed #ccc; border-radius: 4pt;">
        <div style="font-size: 7pt; font-weight: bold; color: #666; margin-bottom: 1mm;">
            CATATAN
        </div>
        <div style="font-size: 8pt;">{{ $order->notes }}</div>
    </div>
    @endif

    {{-- FOOTER --}}
    <div class="receipt-footer">
        Terima kasih atas kunjungan Anda!<br>
        Dicetak pada {{ now()->format('d/m/Y H:i') }} |
        {{ $setting->store_name ?? 'Toko' }}
    </div>

</div>

</body>
</html>