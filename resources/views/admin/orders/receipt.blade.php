<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Nota - {{ $order->order_number ?? '' }}</title>
    <!-- <style>
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
    </style> -->
    <style>
        @page {
            size: 100mm 150mm;
            margin: 0;
        }
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 3mm 4mm;
            color: #000000;
            background: #ffffff;
            font-size: 8pt;
            line-height: 1.2;
            border:1px solid black;
        }

        .page {
            width: 100%;
            position: relative;
        }
    </style>
</head>
<body>

<div class="page">

    {{-- ============================================ --}}
    {{-- HEADER --}}
    {{-- ============================================ --}}
    <table style="border-collapse: collapse; width:100%;">
        <tr>
            <td style="text-align:left;" colspan="2">
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
                <img src="{{ $logoBase64 }}" class="store-logo" alt="{{ $setting->store_name ?? 'Toko' }}" style="width:70pt; margin-left:auto; text-align:right;">
            </td>
            <td colspan="2" style="text-align:right; padding-top:6pt; font-size:15pt; text-transform:uppercase; font-weight:bold;">NOTA</td>
        </tr>
        <tr>
            <td style="text-align:center; padding-top:4pt; font-size:8pt;" colspan="4">{{ $setting->address ?? '' }}</td>
        </tr>
        <tr style="border-bottom:1px solid black;">
            <td style="width:25%; text-align:center; font-size:7pt; padding-bottom:4pt;">
                <img src="images/tiktok.png" alt="" style="width:7pt; margin-top:7pt;">
                barokah.sport
            </td>
            <td style="width:25%; text-align:center; font-size:7pt; padding-bottom:4pt;">
                <img src="images/shopee.png" alt="" style="width:7pt; margin-top:7pt;">
                hmbarokah
            </td>
            <td style="width:25%; text-align:center; font-size:7pt; padding-bottom:4pt;">
                <img src="images/lazada.png" alt="" style="width:7pt; margin-top:7pt;">
                Barokah Sport
            </td>
            <td style="width:25%; text-align:center; font-size:7pt; padding-bottom:4pt;"><img src="images/whatsapp.png" alt="" style="width:7pt; margin-top:7pt;"> @if($setting->whatsapp){{ $setting->whatsapp }}@endif</td>
        </tr>
        <tr>
            <td colspan="4" style="padding-top:7pt; padding-bottom:4pt;">
                <table style="width:100%;">
                    <tr>
                        <td style="text-align:left;">Nama : {{ $order->shipping_name ?? 'Walk-in Customer' }}</td>
                        <td style="text-align:right;">Tanggal dibayar : {{ $order->created_at ? $order->created_at->format('d/m/Y') : now()->format('d/m/Y') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="4" style="padding-top:2pt;">
                <table style="width:100%; border-collapse: collapse; border:1px solid black; font-size:7pt;">
                    <tr>
                        <td style="border:1px solid black; padding:3pt; background-color:rgb(235, 229, 229);">Nama Barang</td>
                        <td style="border:1px solid black; padding:3pt; background-color:rgb(235, 229, 229);">Varian</td>
                        <td style="border:1px solid black; padding:3pt; background-color:rgb(235, 229, 229);">Qty</td>
                        <td style="border:1px solid black; padding:3pt; background-color:rgb(235, 229, 229);">Harga</td>
                        <td style="border:1px solid black; padding:3pt; background-color:rgb(235, 229, 229);">Jumlah</td>
                    </tr>
                    @foreach($order->items as $item)
                    <tr>
                        <td style="border:1px solid black; padding:3pt;">{{ $item->product_name }}</td>
                        <td style="border:1px solid black; padding:3pt; font-size:7pt;">{{ $item->variant_name }}</td>
                        <td style="border:1px solid black; padding:3pt; text-align:center;">{{ $item->quantity }}</td>
                        <td style="border:1px solid black; padding:3pt; text-align:right;">{{ number_format($item->price, 0, ',', '.') }}</td>
                        <td style="border:1px solid black; padding:3pt; text-align:right;">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td style="border:1px solid black; padding:3pt; text-align:center;" colspan="1">Jumlah</td>
                        @if($order->transaction_discount > 0)
                            <td colspan="3" style="border:1px solid black; padding:3pt; text-align:center;">Diskon {{ number_format($order->transaction_discount, 0, ',', '.') }} {{ $order->transaction_discount_type === 'percentage' ? '(%)' : '(Nominal)' }}</td>
                        @endif
                        <td style="border:1px solid black; padding:3pt; text-align:right;">{{ number_format($order->total, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="4" style="text-align:center; padding-top:20pt;">
                <table style="width:100%; padding-right:15pt; padding-left:15pt;">
                    <tr>
                        <td style="width:40%; text-align:center;">
                            Penerima
                        </td>
                        <td style="width:20%;"></td>
                        <td style="width:40%; text-align:center;">Checker Gudang</td>
                    </tr>
                    <tr>
                        <td style="width:40%; text-align:center; height:20pt; border-bottom:1px solid black;">
                            
                        </td>
                        <td style="width:20%;">    </td>
                        <td style="width:40%; text-align:center; height:20pt; border-bottom:1px solid black;">

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="4" style="text-align:center; padding-top:10pt; font-size:7pt;">
                Dicetak pada {{ now()->format('d/m/Y H:i') }}
                <span class="divider">|</span>
                barokahsport.com
            </td>
        </tr>
    </table>

</div>

</body>
</html>