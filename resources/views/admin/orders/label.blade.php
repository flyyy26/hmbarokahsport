<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Label Pengiriman - {{ $order->order_number ?? '' }}</title>
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
            padding: 2mm;
            color: #000000;
            background: #ffffff;
            font-size: 8pt;
            line-height: 1.2;
            border: 1px dashed black; border-width:.4mm; margin:2mm;
        }
        
        /* Utility Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        td, th {
            padding: 0;
            vertical-align: top;
            word-wrap: break-word;
        }

        /* Header / Toko */
        .store-header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 2mm;
            margin-bottom: 2mm;
        }
        .store-name {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .store-address {
            font-size: 6.5pt;
            color: #333;
            margin-top: 1px;
        }

        /* Kurir & Resi Box */
        .courier-badge {
            background-color: #000;
            color: #fff;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 3px 6px;
            text-align: center;
            display: inline-block;
        }

        .address-table td {
            padding: 1px 0;
        }
        .label-col {
            width: 22%;
            font-size: 6.5pt;
            font-weight: bold;
            color: #555;
            text-transform: uppercase;
        }
        .val-col {
            width: 78%;
            font-size: 7.5pt;
        }

        .recipient-name {
            font-size: 8pt;
        }
        .recipient-phone {
            font-size: 8pt;
        }

        .content-table {
            margin-bottom: 2mm;
        }
        .content-table th {
            font-size: 6.5pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 3px;
            text-align: left;
        }
        .content-table td {
            font-size: 7pt;
            padding: 3px;
            border-bottom: 0.5px solid #ccc;
        }
        .content-table tr:last-child td {
            border-bottom: none;
        }

        .no_resi{
            text-align:center;
            font-size:4mm;
            padding:2mm 0;
        }

        .note-box {
            font-size: 6.5pt;
            border: 1px dashed #666;
            padding: 3px 5px;
            background: #fff;
            margin-bottom: 2mm;
        }

        .footer-text {
            text-align:center;
            font-size:6pt;
            color:#777;
            border-top:1px solid #eee;
            padding-top:2mm;
        }

        .store-logo {
            width: 38mm;
            height: auto;
            max-height: 20mm;
            object-fit: contain;
        }
        .courier-logo {
            width: 17mm;
            height: auto;
            max-height: 17mm;
            object-fit: contain;
            margin-bottom: 6mm;
            margin-right: 2mm;
        }
        .qr-code {
            width: 15mm;
            height: auto;
            max-height: 15mm;
        }
        .barcode-img {
            width: 100%;
            height: auto;
            max-height: 12mm;
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <table style="padding:2mm; padding-bottom:0;">
                <tr>
                    <td>
                        <!-- 🔥 PERBAIKAN: Cari logo di multiple path -->
                        @php
                            $logoBase64 = null;
                            
                            // Daftar kemungkinan path logo
                            $logoPaths = [
                                // Hostinger: public_html
                                base_path('../public_html/images/logo-label.png'),
                                base_path('../public_html/storage/images/logo-label.png'),
                                base_path('../public_html/logo-label.png'),
                                
                                // Laravel public
                                public_path('images/logo-label.png'),
                                public_path('storage/images/logo-label.png'),
                                public_path('logo-label.png'),
                                
                                // Storage
                                storage_path('app/public/images/logo-label.png'),
                                storage_path('app/public/logo-label.png'),
                                
                                // Base path
                                base_path('public/images/logo-label.png'),
                                base_path('public/storage/images/logo-label.png'),
                                base_path('public/logo-label.png'),
                            ];
                            
                            $foundPath = null;
                            foreach ($logoPaths as $path) {
                                if (!empty($path) && file_exists($path) && is_readable($path)) {
                                    $foundPath = $path;
                                    break;
                                }
                            }
                            
                            if ($foundPath) {
                                // Baca file dan convert ke base64
                                $logoData = file_get_contents($foundPath);
                                if ($logoData !== false) {
                                    $logoMime = mime_content_type($foundPath);
                                    if ($logoMime === false) {
                                        $ext = pathinfo($foundPath, PATHINFO_EXTENSION);
                                        $logoMime = match(strtolower($ext)) {
                                            'png' => 'image/png',
                                            'jpg', 'jpeg' => 'image/jpeg',
                                            'gif' => 'image/gif',
                                            'webp' => 'image/webp',
                                            'svg' => 'image/svg+xml',
                                            default => 'image/png',
                                        };
                                    }
                                    $logoBase64 = 'data:' . $logoMime . ';base64,' . base64_encode($logoData);
                                }
                            }
                        @endphp
                        
                        @if($logoBase64)
                            <img src="{{ $logoBase64 }}" class="store-logo" alt="Store Logo">
                        @else
                            <!-- 🔥 FALLBACK: Tampilkan teks jika logo tidak ditemukan -->
                            <span class="store-name">{{ $setting->store_name ?? 'BAROKAH SPORT' }}</span>
                        @endif
                    </td>
                    <td>
                        <table style="text-align:right;">
                            <tr>
                                <td style="padding-top:2mm;">
                                    @if($courierLogoUri)
                                        <img src="{{ $courierLogoUri }}" class="courier-logo" alt="{{ $order->courier ?? '' }}">
                                    @else
                                        <span class="courier-badge" style="margin-bottom:11mm; margin-right:2mm;">{{ strtoupper($order->courier ?? '-') }}</span>
                                    @endif
                                    @if($qrCodeUri)
                                        <img src="{{ $qrCodeUri }}" class="qr-code" alt="QR Tracking">
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </tr>
        <tr>
            <td style="border-bottom:1px solid black; border-top:1px solid black;">
                <table class="address-table" style="padding-top:2mm; padding-bottom:2mm;">
                    <tr>
                        <td class="label-col">Pengirim</td>
                        <td class="val-col">
                            <span class="recipient-name">{{ $setting->store_name ?? 'BAROKAH SPORT' }}</span>
                            <span class="recipient-phone">@if($setting->phone) ({{ $setting->phone }}) @endif</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-col">Alamat</td>
                        <td class="val-col" style="line-height: 1.2;">
                            {{ $setting->address ?? '-' }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="border-bottom:1px solid black;">
                <table class="address-table" style="padding-top:2mm; padding-bottom:2mm;">
                    <tr>
                        <td class="label-col">Penerima</td>
                        <td class="val-col">
                            @php
                                $name = $order->shipping_name ?? '-';
                                $phone = $order->shipping_phone ?? '-';
                                $maskedName = strlen($name) > 1
                                    ? substr($name, 0, 1) . str_repeat('*', strlen($name) - 1)
                                    : $name;
                                $maskedPhone = strlen($phone) > 4
                                    ? str_repeat('*', strlen($phone) - 4) . substr($phone, -4)
                                    : $phone;
                            @endphp
                            <span class="recipient-name">{{ $maskedName }}</span>
                            <span class="recipient-phone">({{ $maskedPhone }})</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-col">Alamat</td>
                        <td class="val-col" style="line-height: 1.2;">
                            <span style="font-size:10pt; font-weight:bold;">{{ $order->shipping_address ?? '-' }}<br></span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr style="border-bottom:1px solid black;">
            <table style="padding-top:2mm; padding-bottom:2mm;">
                <tr>
                    <td style="padding:1mm;"><div style="border:1px solid black; text-align:center; padding-top:1mm; padding-bottom:1mm;">{{ $order->shipping_district ?? '' }}</div></td>
                    <td style="padding:1mm;"><div style="border:1px solid black; text-align:center; padding-top:1mm; padding-bottom:1mm;">{{ $order->shipping_city ?? '' }}</div></td>
                    <td style="padding:1mm;"><div style="border:1px solid black; text-align:center; padding-top:1mm; padding-bottom:1mm;">{{ $order->shipping_province ?? '' }}</div></td>
                </tr>
            </table>
        </tr>
        <tr>
            <td>
                <table>
                    <tr>
                        <td style="padding-top:4mm;">
                            @if($barcodeUri)
                                <img src="{{ $barcodeUri }}" class="barcode-img" alt="Barcode {{ $order->tracking_number ?? '' }}">
                            @else
                                <div style="font-size:10pt; font-family:'Courier New',monospace; letter-spacing:2px; text-align:center;">{{ $order->tracking_number ?? 'BELUM DICETAK' }}</div>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top:1mm; padding-bottom:1mm;">
                            <div class="no_resi">Nomor Resi : {{ $order->tracking_number ?? 'BELUM DICETAK' }}</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table class="content-table">
                    <thead style="border-bottom:1px solid black; border-top:1px solid black;">
                        <tr>
                            <th style="width: 15%; padding:2mm; text-align: center; font-size:8pt;">Qty</th>
                            <th style="width: 55%; padding:2mm; font-size:8pt;">Rincian Produk</th>
                            <th style="width: 30%; padding:2mm; text-align: right; font-size:8pt;">Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                        <tr>
                            <td style="text-align: center; font-weight: bold; font-size:8pt;">{{ $item->quantity }}x</td>
                            <td style="font-size:8pt;">
                                {{ $item->product_name }}
                                @if($item->variant_name)
                                    <br><span style="font-size: 6pt; color: #555;">SKU: {{ $item->variant_name }}</span>
                                @endif
                            </td>
                            <td style="text-align: right; font-size:8pt;">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
        </tr>
        @if ($order->notes)
        <tr>
            <td>
                <div class="note-box">Catatan: {{ $order->notes }}</div>
            </td>
        </tr>
        @endif
        <tr>
            <td>
                <div class="footer-text">
                    Dicetak pada {{ now()->format('d M Y H:i') }} | barokahsport.com
                </div>
            </td>
        </tr>
    </table>

</body>
</html>