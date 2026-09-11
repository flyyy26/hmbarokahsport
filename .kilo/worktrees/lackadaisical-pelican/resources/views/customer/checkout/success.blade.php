<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pesanan Berhasil - {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/popup.css') }}">
    <link rel="icon" src="{{ $setting?->favicon ? Storage::url($setting->favicon) : asset('images/favicon.png') }}" type="image/png">
    <link rel="shortcut icon" href="{{ $setting?->favicon ? Storage::url($setting->favicon) : asset('images/favicon.png') }}" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.iconify.design/iconify-icon/3.0.0/iconify-icon.min.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Hanken+Grotesk:ital,wght@0,100..900;1,100..900&display=swap');
        
        * {
            font-family: "Hanken Grotesk", sans-serif;
        }

        .success-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 1.5rem;
        }

        .success-card {
            background: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 2rem rgba(0,0,0,0.08);
            padding: 2.5rem;
            border: 1px solid #e2e8f0;
            position: relative;
            overflow: hidden;
        }

        .success-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #22c55e, #16a34a);
        }

        /* 🔥 ICON SECTION */
        .success-icon-wrapper {
            display: flex;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            animation: pulse 2s ease-in-out infinite;
        }

        .success-icon iconify-icon {
            font-size: 3rem;
            color: #16a34a;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        /* 🔥 HEADER */
        .success-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .success-header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.3rem;
        }

        .success-header p {
            color: #94a3b8;
            font-size: 0.95rem;
        }

        .success-header .order-id {
            display: inline-block;
            background: #f1f5f9;
            padding: 0.2rem 1rem;
            border-radius: 0.3rem;
            font-size: 0.8rem;
            color: #475569;
            margin-top: 0.3rem;
            font-family: monospace;
        }

        /* 🔥 STATUS BADGE */
        .status-badge {
            display: inline-block;
            padding: 0.25rem 1rem;
            border-radius: 100vw;
            font-size: 0.7rem;
            font-weight: 600;
            margin-top: 0.3rem;
        }

        .status-badge.paid {
            background: #dcfce7;
            color: #16a34a;
        }

        .status-badge.processing {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-badge.shipped {
            background: #fef3c7;
            color: #92400e;
        }

        .status-badge.delivered {
            background: #d1fae5;
            color: #065f46;
        }

        /* 🔥 ORDER SUMMARY */
        .order-summary {
            background: #f8fafc;
            border-radius: 0.75rem;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e2e8f0;
        }

        .order-summary .row {
            display: flex;
            justify-content: space-between;
            padding: 0.4rem 0;
            font-size: 0.9rem;
        }

        .order-summary .row .label {
            color: #94a3b8;
        }

        .order-summary .row .value {
            font-weight: 500;
            color: #0f172a;
        }

        .order-summary .total {
            font-weight: 700;
            font-size: 1.1rem;
            border-top: 1px solid #e2e8f0;
            padding-top: 0.6rem;
            margin-top: 0.4rem;
        }

        .order-summary .total .value {
            color: #076694;
            font-size: 1.2rem;
        }

        /* 🔥 ORDER ITEMS */
        .order-items {
            background: #ffffff;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .order-items .items-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.8rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .order-items .items-header h3 {
            font-size: 0.95rem;
            font-weight: 600;
            color: #0f172a;
        }

        .order-items .items-header .items-count {
            font-size: 0.75rem;
            color: #94a3b8;
            background: #f1f5f9;
            padding: 0.1rem 0.7rem;
            border-radius: 100vw;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.6rem 0;
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.2s;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .order-item:hover {
            background: #fafbfc;
            margin: 0 -0.5rem;
            padding: 0.6rem 0.5rem;
            border-radius: 0.4rem;
        }

        .order-item .item-info {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            flex: 1;
        }

        .order-item .item-image {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 0.5rem;
            overflow: hidden;
            background: #f1f5f9;
            flex-shrink: 0;
            border: 1px solid #e2e8f0;
        }

        .order-item .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .order-item .item-image .placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            font-size: 1.5rem;
            color: #94a3b8;
        }

        .order-item .item-details {
            flex: 1;
            min-width: 0;
        }

        .order-item .item-details .name {
            font-size: 0.85rem;
            font-weight: 500;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .order-item .item-details .variant {
            font-size: 0.7rem;
            color: #94a3b8;
        }

        .order-item .item-details .meta {
            display: flex;
            gap: 0.8rem;
            margin-top: 0.1rem;
            font-size: 0.7rem;
            color: #94a3b8;
        }

        .order-item .item-price {
            font-weight: 600;
            color: #0f172a;
            font-size: 0.9rem;
            white-space: nowrap;
        }

        /* 🔥 ACTIONS */
        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .action-buttons .btn-primary {
            width: 100%;
            padding: 0.8rem;
            background: #076694;
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .action-buttons .btn-primary:hover {
            background: #054b6e;
            transform: translateY(-1px);
            box-shadow: 0 0.25rem 1rem rgba(7, 102, 148, 0.2);
        }

        .action-buttons .btn-secondary {
            width: 100%;
            padding: 0.8rem;
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .action-buttons .btn-secondary:hover {
            background: #e2e8f0;
            transform: translateY(-1px);
        }

        /* 🔥 SHIPPING INFO */
        .shipping-info {
            background: #f8fafc;
            border-radius: 0.75rem;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e2e8f0;
        }

        .shipping-info .info-title {
            font-size: 0.8rem;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 0.3rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .shipping-info .info-title iconify-icon {
            color: #076694;
            font-size: 1.1rem;
        }

        .shipping-info .info-content {
            font-size: 0.85rem;
            color: #475569;
            line-height: 1.5;
        }

        .shipping-info .info-content .name {
            font-weight: 500;
            color: #0f172a;
        }

        /* 🔥 RESPONSIVE */
        @media (max-width: 640px) {
            .success-container {
                padding: 0.5rem;
                margin: 0.5rem auto;
            }

            .success-card {
                padding: 1.5rem;
                border-radius: 0.75rem;
            }

            .success-header h1 {
                font-size: 1.4rem;
            }

            .success-icon {
                width: 64px;
                height: 64px;
            }

            .success-icon iconify-icon {
                font-size: 2.2rem;
            }

            .order-summary {
                padding: 1rem;
            }

            .order-items {
                padding: 1rem;
            }

            .order-item .item-image {
                width: 2.8rem;
                height: 2.8rem;
            }

            .order-item .item-details .name {
                font-size: 0.8rem;
            }

            .order-item .item-price {
                font-size: 0.8rem;
            }

            .action-buttons .btn-primary,
            .action-buttons .btn-secondary {
                font-size: 0.9rem;
                padding: 0.7rem;
            }

            .shipping-info {
                padding: 0.8rem 1rem;
            }
        }

        @media (max-width: 480px) {
            .success-card {
                padding: 1rem;
            }

            .order-summary .row {
                font-size: 0.8rem;
            }

            .order-item {
                flex-wrap: wrap;
                gap: 0.3rem;
            }

            .order-item .item-info {
                flex: 1 1 100%;
            }

            .order-item .item-price {
                margin-left: auto;
            }
        }

        /* 🔥 CONFETTI ANIMATION (opsional) */
        .confetti-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            z-index: 9999;
            overflow: hidden;
        }

        .confetti {
            position: absolute;
            width: 10px;
            height: 10px;
            animation: confetti-fall linear forwards;
        }

        @keyframes confetti-fall {
            0% {
                transform: translateY(-10vh) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(110vh) rotate(720deg);
                opacity: 0;
            }
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900">

    @include('customer.partials.navbar')
    @include('customer.partials.search-popup')
    @include('customer.partials.login-popup')

    <div class="success-container">
        <div class="success-card">

            {{-- 🔥 ICON --}}
            <div class="success-icon-wrapper">
                <div class="success-icon">
                    <iconify-icon icon="mdi:check-circle"></iconify-icon>
                </div>
            </div>

            {{-- 🔥 HEADER --}}
            <div class="success-header">
                <h1>Pesanan Berhasil! 🎉</h1>
                <p>Terima kasih telah berbelanja di {{ config('app.name') }}.</p>
                <span class="order-id">#{{ $order->order_number }}</span>
                <br>
                <span class="status-badge {{ $order->payment_status }}">
                    @if($order->payment_status === 'paid')
                        ✅ Lunas
                    @elseif($order->payment_status === 'processing')
                        ⏳ Diproses
                    @elseif($order->payment_status === 'shipped')
                        📦 Dikirim
                    @elseif($order->payment_status === 'delivered')
                        📦 Selesai
                    @else
                        {{ ucfirst($order->payment_status) }}
                    @endif
                </span>
            </div>

            {{-- 🔥 ORDER SUMMARY --}}
            <div class="order-summary">
                <div class="row">
                    <span class="label">Subtotal</span>
                    <span class="value">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                @if($order->shipping_cost > 0)
                <div class="row">
                    <span class="label">Ongkir</span>
                    <span class="value">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($order->discount > 0)
                <div class="row" style="color:#16a34a;">
                    <span class="label">Diskon</span>
                    <span class="value">-Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="row total">
                    <span class="label">Total</span>
                    <span class="value">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- 🔥 SHIPPING INFO --}}
            @if($order->shipping_name)
            <div class="shipping-info">
                <div class="info-title">
                    <iconify-icon icon="mdi:truck-delivery-outline"></iconify-icon>
                    Alamat Pengiriman
                </div>
                <div class="info-content">
                    <span class="name">{{ $order->shipping_name }}</span>
                    <br>
                    {{ $order->shipping_address }}
                    @if($order->shipping_city)
                        <br>{{ $order->shipping_city }}, {{ $order->shipping_province }}
                    @endif
                    @if($order->shipping_postal_code && $order->shipping_postal_code !== '0')
                        <br>Kode Pos: {{ $order->shipping_postal_code }}
                    @endif
                    @if($order->shipping_phone)
                        <br>Telp: {{ $order->shipping_phone }}
                    @endif
                </div>
            </div>
            @endif

            {{-- 🔥 ORDER ITEMS --}}
            <div class="order-items">
                <div class="items-header">
                    <h3>Item Pesanan</h3>
                    <span class="items-count">{{ $order->items->count() }} produk</span>
                </div>
                @foreach ($order->items as $item)
                <div class="order-item">
                    <div class="item-info">
                        <div class="item-image">
                            @if ($item->product && $item->product->images->first())
                                <img src="{{ Storage::url($item->product->images->first()->image) }}" 
                                     alt="{{ $item->product_name }}" 
                                     loading="lazy"
                                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                                <div class="placeholder" style="display:none;">
                                    <iconify-icon icon="mdi:package-variant"></iconify-icon>
                                </div>
                            @else
                                <div class="placeholder">
                                    <iconify-icon icon="mdi:package-variant"></iconify-icon>
                                </div>
                            @endif
                        </div>
                        <div class="item-details">
                            <div class="name" title="{{ $item->product_name }}">{{ $item->product_name }}</div>
                            @if ($item->variant_name)
                                <div class="variant">{{ $item->variant_name }}</div>
                            @endif
                            <div class="meta">
                                <span>{{ $item->quantity }}x</span>
                                <span>@ Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="item-price">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                </div>
                @endforeach
            </div>

            {{-- 🔥 ACTIONS --}}
            <div class="action-buttons">
                <a href="{{ route('customer.orders.show', $order) }}" class="btn-primary">
                    <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                    Lihat Detail Pesanan
                </a>
                <a href="{{ route('customer.orders') }}" class="btn-secondary">
                    <iconify-icon icon="mdi:history"></iconify-icon>
                    Riwayat Pesanan
                </a>
                <a href="{{ route('customer.home') }}" class="btn-secondary" style="background:transparent;border-color:transparent;color:#94a3b8;">
                    <iconify-icon icon="mdi:arrow-left"></iconify-icon>
                    Lanjut Belanja
                </a>
            </div>

        </div>
    </div>

    @include('customer.partials.footer')

    {{-- 🔥 CONFETTI EFFECT (optional) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 🔥 Confetti sederhana untuk efek meriah
            const colors = ['#22c55e', '#16a34a', '#15803d', '#f59e0b', '#3b82f6', '#8b5cf6', '#ec4899'];
            const container = document.createElement('div');
            container.className = 'confetti-container';
            document.body.appendChild(container);

            for (let i = 0; i < 60; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                const size = Math.random() * 8 + 4;
                const color = colors[Math.floor(Math.random() * colors.length)];
                const left = Math.random() * 100;
                const duration = Math.random() * 3 + 2;
                const delay = Math.random() * 2;

                confetti.style.cssText = `
                    left: ${left}%;
                    width: ${size}px;
                    height: ${size}px;
                    background: ${color};
                    border-radius: ${Math.random() > 0.5 ? '50%' : '2px'};
                    animation-duration: ${duration}s;
                    animation-delay: ${delay}s;
                `;

                container.appendChild(confetti);

                // Hapus setelah animasi selesai
                setTimeout(() => {
                    confetti.remove();
                }, (duration + delay) * 1000 + 500);
            }

            // Hapus container setelah semua confetti selesai
            setTimeout(() => {
                container.remove();
            }, 6000);
        });
    </script>

    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
</body>
</html>