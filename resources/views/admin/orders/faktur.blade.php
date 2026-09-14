<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Faktur - {{ $order->order_number ?? '' }}</title>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica Neue', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #000;
            background: #fff;
            font-size: 11pt;
            line-height: 1.5;
        }
        .container {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
        }

        .section {
            margin-bottom: 12pt;
        }

        .flex-row {
            display: flex;
            justify-content: space-between;
        }
        .flex-row > div {
            flex: 1;
        }
        .flex-row > div:last-child {
            text-align: right;
        }

        .store-logo {
            max-height: 30mm;
            max-width: 60mm;
            height: auto;
            object-fit: contain;
        }

        .store-name {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .store-info {
            font-size: 9pt;
            color: #555;
        }

        .invoice-title {
            font-size: 20pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #000;
            margin-bottom: 4pt;
        }
        .invoice-meta {
            font-size: 9pt;
            color: #555;
        }
        .invoice-meta td {
            padding: 2pt 4pt;
            vertical-align: top;
        }
        .invoice-meta .meta-label {
            font-weight: bold;
            width: 100pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            font-size: 9pt;
            padding: 6pt 8pt;
            border: 1px solid #ccc;
            vertical-align: top;
        }
        th {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8pt;
            color: #000;
            background: #f5f5f5;
        }
        td.center, th.center {
            text-align: center;
        }
        td.right, th.right {
            text-align: right;
            font-family: 'Courier New', monospace;
        }
        td.desc {
            width: 50%;
        }

        .totals-table td {
            border: none;
            padding: 4pt 8pt;
        }
        .totals-table td.totals-label {
            text-align: right;
            font-weight: bold;
            width: 55%;
        }
        .totals-table td.totals-value {
            text-align: right;
            width: 45%;
        }
        .totals-table .totals-total td {
            border-top: 2px solid #000;
            font-weight: bold;
            font-size: 12pt;
        }

        .info-box {
            border: 1px solid #ccc;
            border-radius: 4pt;
            padding: 10pt;
            margin-bottom: 12pt;
        }
        .info-box-title {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 6pt;
            color: #333;
        }

        .watermark {
            position: fixed;
            bottom: 20mm;
            right: 20mm;
            font-size: 60pt;
            color: #e0e0e0;
            transform: rotate(-30deg);
            z-index: 0;
            pointer-events: none;
        }

        .footer-note {
            font-size: 8pt;
            color: #666;
            border-top: 1px dashed #ccc;
            padding-top: 10pt;
            margin-top: 12pt;
        }
    </style>
</head>
<body>

<div class="container">

    <div class="section flex-row">
        <div>
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
                <img src="{{ $logoBase64 }}" class="store-logo" alt="{{ $setting->store_name ?? 'Toko' }}">
            @else
                <div class="store-name">{{ $setting->store_name ?? 'Toko' }}</div>
            @endif

            @if($setting)
                <div class="store-info">
                    {{ $setting->address ?? '' }}<br>
                    WhatsApp: {{ $setting->whatsapp ?? '-' }}<br>
                    Email: {{ $setting->email ?? '-' }}
                </div>
            @endif
        </div>
        <div>
            <div class="invoice-title">Faktur</div>
            <table class="invoice-meta">
                <tr>
                    <td class="meta-label">No. Faktur</td>
                    <td>: {{ $order->order_number ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="meta-label">Tanggal</td>
                    <td>: {{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <td class="meta-label">Status</td>
                    <td>: {{ $order->payment_status_label ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="section flex-row">
        <div>
            <div class="info-box-title">Pelanggan / Penerima</div>
            <table>
                <tr>
                    <td class="desc"><strong>Nama</strong></td>
                    <td>{{ $order->shipping_name ?? 'Walk-in Customer' }}</td>
                </tr>
                <tr>
                    <td><strong>No. HP</strong></td>
                    <td>{{ $order->shipping_phone ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Alamat</strong></td>
                    <td>{{ $order->shipping_address ?? '-' }}</td>
                </tr>
            </table>
        </div>
        <div>
            <div class="info-box-title">Ringkasan Pesanan</div>
            <table>
                <tr>
                    <td class="desc"><strong>Kasir</strong></td>
                    <td>{{ auth()->user()->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Status Pembayaran</strong></td>
                    <td>{{ $order->payment_status_label ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Metode Pembayaran</strong></td>
                    <td>{{ match($order->payment_method ?? '') {
                        'cash' => 'Tunai',
                        'transfer' => 'Transfer',
                        'qris' => 'QRIS',
                        default => strtoupper($order->payment_method ?? '-'),
                    } }}</td>
                </tr>
                <tr>
                    <td><strong>Note / Keterangan</strong></td>
                    <td>{{ $order->notes ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="section">
        <table>
            <thead>
                <tr>
                    <th class="center">No.</th>
                    <th class="desc">Produk</th>
                    <th>Varian</th>
                    <th>SKU</th>
                    <th class="center">Qty</th>
                    <th class="right">Harga Satuan</th>
                    <th class="right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $i => $item)
                    <tr>
                        <td class="center">{{ $i + 1 }}</td>
                        <td class="desc">{{ $item->product_name }}</td>
                        <td>{{ $item->variant_name ?? '-' }}</td>
                        <td>{{ $item->sku ?? '-' }}</td>
                        <td class="center">{{ $item->quantity }}</td>
                        <td class="right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section" style="display: flex; justify-content: flex-end;">
        <table class="totals-table" style="width: auto; min-width: 220px;">
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
                <td class="totals-value">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="footer-note">
        Faktur ini adalah bukti pembayaran yang sah.<br>
        Dicetak pada {{ now()->format('d/m/Y H:i') }} | barokahsport.com
    </div>

</div>

</body>
</html>
