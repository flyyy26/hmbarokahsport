<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Nota - {{ $order->order_number ?? '' }}</title>
    <style>
        @page {
            size: 105mm 148mm;
            margin: 6mm;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Helvetica Neue', 'Arial', sans-serif;
            color: #0f172a;
            background: #ffffff;
            font-size: 9pt;
            line-height: 1.35;
        }

        .page {
            width: 100%;
            position: relative;
        }

        /* ============================================
           HEADER
           ============================================ */
        .receipt-header {
            text-align: center;
            padding-bottom: 3mm;
            margin-bottom: 3mm;
            border-bottom: 1.5pt solid #0f172a;
            position: relative;
        }

        /* Gold accent bar */
        .receipt-header::after {
            content: '';
            position: absolute;
            bottom: -1.5pt;
            left: 0;
            right: 0;
            height: 1.5pt;
            background: linear-gradient(90deg,
                transparent 0%,
                #ecbc42 20%,
                #FDDD57 50%,
                #ecbc42 80%,
                transparent 100%);
        }

        .store-logo {
            max-height: 18mm;
            max-width: 55mm;
            height: auto;
            object-fit: contain;
            display: block;
            margin: 0 auto 2mm;
        }

        .store-name {
            font-size: 13pt;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #0f172a;
        }

        .store-name-sub {
            display: inline-block;
            font-size: 6.5pt;
            font-weight: 600;
            color: #b8860b;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-top: 0.5mm;
        }

        .store-info {
            font-size: 6.5pt;
            color: #64748b;
            margin-top: 2mm;
            line-height: 1.5;
        }

        /* ============================================
           ORDER INFO
           ============================================ */
        .order-info {
            margin-bottom: 3mm;
        }

        .order-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            border: 0.75pt solid #e2e8f0;
            border-radius: 2mm;
            overflow: hidden;
        }

        .order-info-cell {
            padding: 2mm 2.5mm;
            border-bottom: 0.75pt solid #e2e8f0;
        }

        .order-info-cell:nth-child(odd) {
            border-right: 0.75pt solid #e2e8f0;
            background: #f8fafc;
        }

        .order-info-cell:nth-last-child(-n+2) {
            border-bottom: none;
        }

        .order-info-label {
            font-size: 6pt;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 0.5mm;
        }

        .order-info-value {
            font-size: 8pt;
            font-weight: 700;
            color: #0f172a;
        }

        /* ============================================
           CUSTOMER INFO
           ============================================ */
        .customer-info {
            border: 0.75pt solid #e2e8f0;
            border-left: 2.5pt solid #ecbc42;
            border-radius: 2mm;
            padding: 2.5mm 3mm;
            margin-bottom: 3mm;
            background: #fffbf0;
        }

        .customer-info-title {
            font-size: 6.5pt;
            font-weight: 800;
            text-transform: uppercase;
            color: #b8860b;
            letter-spacing: 1px;
            margin-bottom: 1.5mm;
            display: flex;
            align-items: center;
            gap: 1.5mm;
        }

        .customer-info-title::before {
            content: '';
            display: inline-block;
            width: 0.5mm;
            height: 3mm;
            background: #ecbc42;
            border-radius: 100px;
        }

        .customer-info-row {
            display: flex;
            font-size: 7.5pt;
            padding: 0.75mm 0;
            align-items: flex-start;
        }

        .customer-info-row .label {
            width: 22%;
            color: #64748b;
            font-weight: 500;
            flex-shrink: 0;
        }

        .customer-info-row .value {
            width: 78%;
            color: #0f172a;
            font-weight: 600;
        }

        /* ============================================
           ITEMS TABLE
           ============================================ */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3mm;
            border: 0.75pt solid #e2e8f0;
            border-radius: 2mm;
            overflow: hidden;
        }

        .items-table thead {
            background: #0f172a;
        }

        .items-table th {
            font-size: 6pt;
            font-weight: 700;
            text-transform: uppercase;
            color: #ffffff;
            letter-spacing: 0.5px;
            padding: 2mm 2mm;
            text-align: left;
        }

        .items-table th.center { text-align: center; }
        .items-table th.right { text-align: right; }

        .items-table .th-qty { width: 8%; }
        .items-table .th-product { width: 46%; }
        .items-table .th-price { width: 23%; }
        .items-table .th-subtotal { width: 23%; }

        .items-table td {
            font-size: 7.5pt;
            padding: 2mm 2mm;
            border-bottom: 0.5pt solid #f1f5f9;
            vertical-align: top;
        }

        .items-table td.center { text-align: center; }
        .items-table td.right { text-align: right; }

        .items-table tbody tr:last-child td {
            border-bottom: none;
        }

        .items-table tbody tr:nth-child(even) {
            background: #fafbfc;
        }

        .product-name {
            font-weight: 700;
            color: #0f172a;
            line-height: 1.3;
        }

        .product-variant {
            display: block;
            font-size: 6.5pt;
            color: #64748b;
            margin-top: 0.5mm;
            font-style: italic;
        }

        .product-sku {
            display: block;
            font-size: 6pt;
            color: #94a3b8;
            margin-top: 0.3mm;
            font-family: 'Courier New', monospace;
        }

        .qty-badge {
            display: inline-block;
            background: #f1f5f9;
            color: #0f172a;
            font-size: 7pt;
            font-weight: 700;
            padding: 0.5mm 1.5mm;
            border-radius: 1mm;
            min-width: 8mm;
            text-align: center;
        }

        .price-text {
            font-size: 7pt;
            color: #475569;
            font-weight: 500;
        }

        .subtotal-text {
            font-size: 8pt;
            color: #0f172a;
            font-weight: 700;
        }

        /* ============================================
           TOTALS
           ============================================ */
        .totals-wrapper {
            border: 0.75pt solid #e2e8f0;
            border-radius: 2mm;
            padding: 2.5mm 3mm;
            background: #fafbfc;
            margin-bottom: 3mm;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 1mm 0;
            font-size: 7.5pt;
        }

        .totals-label {
            text-align: right;
            color: #64748b;
            font-weight: 500;
            padding-right: 3mm;
        }

        .totals-value {
            text-align: right;
            font-weight: 700;
            color: #0f172a;
            width: 40%;
        }

        .totals-discount .totals-label,
        .totals-discount .totals-value {
            color: #059669;
        }

        .totals-shipping .totals-label,
        .totals-shipping .totals-value {
            color: #475569;
        }

        .totals-total td {
            border-top: 1.5pt solid #0f172a;
            padding-top: 2mm;
            font-weight: 800;
        }

        .totals-total .totals-label {
            font-size: 9pt;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .totals-total .totals-value {
            font-size: 12pt;
            color: #b8860b;
        }

        /* ============================================
           PAYMENT METHOD
           ============================================ */
        .payment-wrapper {
            text-align: center;
            margin-bottom: 3mm;
        }

        .payment-badge {
            display: inline-flex;
            align-items: center;
            gap: 1.5mm;
            background: #0f172a;
            color: #ffffff;
            font-size: 7pt;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 1.2mm 4mm;
            border-radius: 100px;
            box-shadow: 0 1mm 2mm rgba(0, 0, 0, 0.1);
        }

        .payment-badge .dot {
            width: 1.5mm;
            height: 1.5mm;
            border-radius: 50%;
            background: #34d399;
            box-shadow: 0 0 0 1mm rgba(52, 211, 153, 0.3);
        }

        .paid-stamp {
            display: block;
            margin-top: 2mm;
            font-size: 8pt;
            font-weight: 800;
            color: #059669;
            letter-spacing: 2px;
            text-transform: uppercase;
            border: 1pt solid #059669;
            border-radius: 1mm;
            padding: 1mm 3mm;
            width: fit-content;
            margin-left: auto;
            margin-right: auto;
            transform: rotate(-3deg);
        }

        /* ============================================
           NOTES
           ============================================ */
        .notes-box {
            padding: 2mm 2.5mm;
            background: #fef3c7;
            border-left: 2mm solid #f59e0b;
            border-radius: 1.5mm;
            margin-bottom: 3mm;
        }

        .notes-title {
            font-size: 6pt;
            font-weight: 800;
            color: #92400e;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.75mm;
        }

        .notes-content {
            font-size: 7pt;
            color: #78350f;
            line-height: 1.5;
        }

        /* ============================================
           FOOTER
           ============================================ */
        .receipt-footer {
            border-top: 0.75pt dashed #cbd5e1;
            padding-top: 2.5mm;
            text-align: center;
            font-size: 6pt;
            color: #94a3b8;
            line-height: 1.6;
        }

        .receipt-footer .thankyou {
            font-size: 7.5pt;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 1mm;
        }

        .receipt-footer .divider {
            display: inline-block;
            color: #cbd5e1;
            margin: 0 1.5mm;
        }
    </style>
</head>
<body>

<div class="page">

    {{-- ============================================ --}}
    {{-- HEADER --}}
    {{-- ============================================ --}}
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
            <img src="{{ $logoBase64 }}" class="store-logo" alt="{{ $setting->store_name ?? 'Toko' }}">
        @else
            <div class="store-name">{{ $setting->store_name ?? 'Toko' }}</div>
            <span class="store-name-sub">Sport Store</span>
        @endif

        @if($setting)
            <div class="store-info">
                {{ $setting->address ?? '' }}
                @if($setting->phone || $setting->whatsapp)
                    <br>
                    @if($setting->phone)Telp: {{ $setting->phone }}@endif
                    @if($setting->phone && $setting->whatsapp) <span class="divider">•</span> @endif
                    @if($setting->whatsapp)WA: {{ $setting->whatsapp }}@endif
                @endif
                @if($setting->email)
                    <br>Email: {{ $setting->email }}
                @endif
            </div>
        @endif
    </div>

    {{-- ============================================ --}}
    {{-- ORDER INFO --}}
    {{-- ============================================ --}}
    <div class="order-info">
        <div class="order-info-grid">
            <div class="order-info-cell">
                <span class="order-info-label">No. Nota</span>
                <span class="order-info-value">{{ $order->order_number ?? '-' }}</span>
            </div>
            <div class="order-info-cell">
                <span class="order-info-label">Tanggal</span>
                <span class="order-info-value">
                    {{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}
                </span>
            </div>
            <div class="order-info-cell">
                <span class="order-info-label">Kasir</span>
                <span class="order-info-value">{{ auth()->user()->name ?? '-' }}</span>
            </div>
            <div class="order-info-cell">
                <span class="order-info-label">Status</span>
                <span class="order-info-value">{{ $order->payment_status_label ?? '-' }}</span>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- CUSTOMER INFO --}}
    {{-- ============================================ --}}
    <div class="customer-info">
        <div class="customer-info-title">Pelanggan / Penerima</div>

        <div class="customer-info-row">
            <span class="label">Nama</span>
            <span class="value">{{ $order->shipping_name ?? 'Walk-in Customer' }}</span>
        </div>
        <div class="customer-info-row">
            <span class="label">No. HP</span>
            <span class="value">{{ $order->shipping_phone ?? '-' }}</span>
        </div>
        @if($order->shipping_address && $order->shipping_address !== '-')
            <div class="customer-info-row">
                <span class="label">Alamat</span>
                <span class="value">{{ $order->shipping_address }}</span>
            </div>
        @endif
        @if($order->courier)
            <div class="customer-info-row">
                <span class="label">Kurir</span>
                <span class="value">{{ strtoupper($order->courier) }} {{ $order->service ? '- ' . $order->service : '' }}</span>
            </div>
        @endif
    </div>

    {{-- ============================================ --}}
    {{-- ITEMS TABLE --}}
    {{-- ============================================ --}}
    <table class="items-table">
        <thead>
            <tr>
                <th class="center th-qty">Qty</th>
                <th class="th-product">Produk</th>
                <th class="right th-price">Harga</th>
                <th class="right th-subtotal">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td class="center">
                        <span class="qty-badge">{{ $item->quantity }}x</span>
                    </td>
                    <td>
                        <div class="product-name">{{ $item->product_name }}</div>
                        @if($item->variant_name)
                            <span class="product-variant">{{ $item->variant_name }}</span>
                        @endif
                        @if($item->sku)
                            <span class="product-sku">SKU: {{ $item->sku }}</span>
                        @endif
                    </td>
                    <td class="right">
                        <span class="price-text">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                    </td>
                    <td class="right">
                        <span class="subtotal-text">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ============================================ --}}
    {{-- TOTALS --}}
    {{-- ============================================ --}}
    <div class="totals-wrapper">
        <table class="totals-table">
            <tr>
                <td class="totals-label">Subtotal</td>
                <td class="totals-value">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
            </tr>

            @if($order->discount > 0)
                <tr class="totals-discount">
                    <td class="totals-label">Diskon Produk</td>
                    <td class="totals-value">- Rp {{ number_format($order->discount, 0, ',', '.') }}</td>
                </tr>
            @endif

            @if($order->transaction_discount > 0)
                <tr class="totals-discount">
                    <td class="totals-label">
                        Diskon {{ $order->transaction_discount_type === 'percentage' ? '(%)' : '(Nominal)' }}
                    </td>
                    <td class="totals-value">- Rp {{ number_format($order->transaction_discount, 0, ',', '.') }}</td>
                </tr>
            @endif

            @if($order->shipping_cost > 0)
                <tr class="totals-shipping">
                    <td class="totals-label">Ongkir</td>
                    <td class="totals-value">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                </tr>
            @endif

            <tr class="totals-total">
                <td class="totals-label">Total</td>
                <td class="totals-value">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    {{-- ============================================ --}}
    {{-- PAYMENT METHOD --}}
    {{-- ============================================ --}}
    <div class="payment-wrapper">
        <span class="payment-badge">
            <span class="dot"></span>
            {{ match($order->payment_method) {
                'cash' => 'Tunai',
                'transfer' => 'Transfer',
                'qris' => 'QRIS',
                'midtrans' => 'Midtrans',
                default => strtoupper($order->payment_method ?? '-'),
            } }}
        </span>

        @if($order->payment_status === 'paid')
            <span class="paid-stamp">✓ LUNAS</span>
        @endif
    </div>

    {{-- ============================================ --}}
    {{-- NOTES --}}
    {{-- ============================================ --}}
    @if($order->notes)
        <div class="notes-box">
            <div class="notes-title">📝 Catatan</div>
            <div class="notes-content">{{ $order->notes }}</div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- FOOTER --}}
    {{-- ============================================ --}}
    <div class="receipt-footer">
        <div class="thankyou">Terima Kasih! 🙏</div>
        <div>
            Barang yang sudah dibeli tidak dapat ditukar / dikembalikan.<br>
            Simpan nota ini sebagai bukti pembayaran yang sah.
        </div>
        <div style="margin-top: 1.5mm; color: #cbd5e1;">
            Dicetak pada {{ now()->format('d/m/Y H:i') }}
            <span class="divider">•</span>
            {{ $setting->store_name ?? 'Toko' }}
        </div>
    </div>

</div>

</body>
</html>