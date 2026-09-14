@extends('layouts.account')

@section('title', 'Riwayat Pesanan - Barokah Sport')
@section('page-title', 'Riwayat Pesanan')
@section('page-subtitle', 'Lihat semua pesanan yang pernah kamu buat.')

@section('account-content')

<style>
    /* ============================================
       ORDER PAGE STYLES
       ============================================ */
    .orders-tabs {
        display: flex;
        gap: 0;
        border-bottom: 0.1vw solid #e5e7eb;
        margin-bottom: 1.5vw;
        overflow-x: auto;
        scrollbar-width: none;
    }
    .orders-tabs::-webkit-scrollbar { display: none; }

    .order-tab {
        display: flex;
        align-items: center;
        gap: 0.5vw;
        padding: 1vw 1.2vw;
        font-size: 0.85vw;
        font-weight: 500;
        color: #64748b;
        text-decoration: none;
        border-bottom: 0.2vw solid transparent;
        white-space: nowrap;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .order-tab:hover {
        color: #0f172a;
        border-bottom-color: #e2e8f0;
    }

    .order-tab.active {
        color: rgb(102, 72, 9);
        border-bottom-color: #ecbc42;
        font-weight: 700;
    }

    .order-tab-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 1.5vw;
        height: 1.5vw;
        padding: 0 0.5vw;
        border-radius: 100vw;
        font-size: 0.7vw;
        font-weight: 700;
        background: #f1f5f9;
        color: #64748b;
    }

    .order-tab.active .order-tab-badge {
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
        color: rgb(102, 72, 9);
    }

    /* ============================================
       EMPTY STATE
       ============================================ */
    .orders-empty {
        text-align: center;
        padding: 3vw 1vw;
    }

    .orders-empty-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 5vw;
        height: 5vw;
        margin: 0 auto;
        border-radius: 50%;
        background: linear-gradient(135deg, #FDDD57 0%, #ecbc42 100%);
        color: rgb(102, 72, 9);
    }

    .orders-empty-icon iconify-icon {
        font-size: 2.5vw;
    }

    .orders-empty h3 {
        font-size: 1.1vw;
        font-weight: 700;
        color: #0f172a;
        margin-top: 1vw;
    }

    .orders-empty p {
        font-size: 0.85vw;
        color: #64748b;
        margin-top: 0.5vw;
        line-height: 1.6;
    }

    .orders-empty-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5vw;
        margin-top: 1.5vw;
        padding: 0.85vw 1.5vw;
        border-radius: 0.7vw;
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
        color: rgb(102, 72, 9);
        font-size: 0.85vw;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 0.15vw 0.5vw rgba(236, 188, 66, 0.3);
    }

    .orders-empty-btn:hover {
        transform: translateY(-0.1vw);
        box-shadow: 0 0.3vw 1vw rgba(236, 188, 66, 0.4);
    }

    /* ============================================
       ORDER CARD
       ============================================ */
    .orders-list {
        display: flex;
        flex-direction: column;
        gap: 1vw;
    }

    .order-card {
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.9vw;
        padding: 1.4vw;
        background: #ffffff;
        transition: all 0.25s ease;
    }

    .order-card:hover {
        box-shadow: 0 0.4vw 1.5vw rgba(0, 0, 0, 0.06);
        border-color: #ecbc42;
    }

    .order-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1vw;
        flex-wrap: wrap;
    }

    .order-number {
        display: flex;
        align-items: center;
        gap: 0.5vw;
        font-size: 0.9vw;
        font-weight: 700;
        color: #0f172a;
    }

    .order-number iconify-icon {
        color: #ecbc42;
        font-size: 1.1vw;
    }

    .order-date {
        display: flex;
        align-items: center;
        gap: 0.3vw;
        font-size: 0.75vw;
        color: #94a3b8;
        margin-top: 0.3vw;
    }

    .order-date iconify-icon {
        font-size: 0.9vw;
    }

    .order-total {
        text-align: right;
    }

    .order-total-price {
        font-size: 1.15vw;
        font-weight: 800;
        color: #0f172a;
    }

    .order-total-count {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.3vw;
        font-size: 0.75vw;
        color: #94a3b8;
        margin-top: 0.2vw;
    }

    .order-total-count iconify-icon {
        font-size: 0.9vw;
    }

    /* ============================================
       ITEM PREVIEW
       ============================================ */
    .order-items {
        margin-top: 1vw;
        padding-top: 1vw;
        border-top: 0.1vw dashed #e2e8f0;
        display: flex;
        gap: 1vw;
        overflow-x: auto;
        scrollbar-width: thin;
        padding-bottom: 0.5vw;
    }

    .order-items::-webkit-scrollbar {
        height: 0.3vw;
    }
    .order-items::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 100vw;
    }

    .order-item {
        display: flex;
        align-items: center;
        gap: 0.7vw;
        flex-shrink: 0;
        max-width: 16vw;
    }

    .order-item-img {
        width: 3vw;
        height: 3vw;
        border-radius: 0.6vw;
        background: #f1f5f9;
        overflow: hidden;
        flex-shrink: 0;
    }

    .order-item-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .order-item-img iconify-icon {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #cbd5e1;
        font-size: 1.4vw;
    }

    .order-item-info {
        min-width: 0;
    }

    .order-item-name {
        font-size: 0.8vw;
        font-weight: 600;
        color: #334155;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .order-item-qty {
        font-size: 0.7vw;
        color: #94a3b8;
        margin-top: 0.15vw;
    }

    .order-item-more {
        display: flex;
        align-items: center;
        gap: 0.3vw;
        padding: 0.5vw 0.8vw;
        border-radius: 0.5vw;
        background: #f8fafc;
        color: #64748b;
        font-size: 0.75vw;
        font-weight: 600;
        flex-shrink: 0;
        white-space: nowrap;
    }

    /* ============================================
       ACTIONS
       ============================================ */
    .order-actions {
        display: flex;
        gap: 0.6vw;
        margin-top: 1.2vw;
        flex-wrap: wrap;
    }

    .btn-order {
        display: inline-flex;
        align-items: center;
        gap: 0.4vw;
        padding: 0.7vw 1.1vw;
        border-radius: 0.6vw;
        font-size: 0.8vw;
        font-weight: 600;
        cursor: pointer;
        border: 0.1vw solid transparent;
        transition: all 0.2s ease;
        text-decoration: none;
        font-family: inherit;
    }

    .btn-order iconify-icon {
        font-size: 1vw;
    }

    .btn-order-outline {
        background: #ffffff;
        border-color: #e2e8f0;
        color: #475569;
    }
    .btn-order-outline:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .btn-order-danger {
        background: #fef2f2;
        border-color: #fecaca;
        color: #dc2626;
    }
    .btn-order-danger:hover {
        background: #fee2e2;
    }

    .btn-order-success {
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
        color: rgb(102, 72, 9);
        box-shadow: 0 0.15vw 0.5vw rgba(236, 188, 66, 0.3);
    }
    .btn-order-success:hover {
        transform: translateY(-0.1vw);
        box-shadow: 0 0.3vw 1vw rgba(236, 188, 66, 0.4);
    }

    /* ============================================
       RESPONSIVE - TABLET
       ============================================ */
    @media (max-width: 1024px) {
        .orders-tabs {
            margin-bottom: 3vw;
            border-bottom-width: 0.2vw;
        }

        .order-tab {
            padding: 2.5vw 3vw;
            font-size: 2vw;
            gap: 1vw;
            border-bottom-width: 0.4vw;
        }

        .order-tab-badge {
            min-width: 3.5vw;
            height: 3.5vw;
            padding: 0 1vw;
            font-size: 1.7vw;
        }

        .orders-empty {
            padding: 6vw 3vw;
        }

        .orders-empty-icon {
            width: 12vw;
            height: 12vw;
        }
        .orders-empty-icon iconify-icon {
            font-size: 6vw;
        }

        .orders-empty h3 {
            font-size: 2.8vw;
            margin-top: 2.5vw;
        }
        .orders-empty p {
            font-size: 2vw;
            margin-top: 1vw;
        }
        .orders-empty-btn {
            margin-top: 3vw;
            padding: 2.3vw 4vw;
            border-radius: 1.8vw;
            font-size: 2.1vw;
            gap: 1vw;
        }

        .orders-list { gap: 2.5vw; }

        .order-card {
            padding: 3vw;
            border-radius: 2vw;
            border-width: 0.2vw;
        }

        .order-number {
            font-size: 2.3vw;
            gap: 1vw;
        }
        .order-number iconify-icon { font-size: 2.8vw; }

        .order-date {
            font-size: 1.9vw;
            gap: 0.7vw;
            margin-top: 0.7vw;
        }
        .order-date iconify-icon { font-size: 2.3vw; }

        .order-total-price { font-size: 2.8vw; }

        .order-total-count {
            font-size: 1.9vw;
            gap: 0.7vw;
            margin-top: 0.5vw;
        }
        .order-total-count iconify-icon { font-size: 2.3vw; }

        .order-items {
            margin-top: 2.5vw;
            padding-top: 2.5vw;
            gap: 2.5vw;
            border-top-width: 0.2vw;
        }

        .order-item {
            gap: 1.7vw;
            max-width: 40vw;
        }

        .order-item-img {
            width: 8vw;
            height: 8vw;
            border-radius: 1.5vw;
        }
        .order-item-img iconify-icon { font-size: 3.5vw; }

        .order-item-name { font-size: 2vw; }
        .order-item-qty { font-size: 1.7vw; margin-top: 0.4vw; }

        .order-item-more {
            padding: 1.5vw 2.5vw;
            border-radius: 1.5vw;
            font-size: 1.9vw;
            gap: 0.7vw;
        }

        .order-actions {
            gap: 2vw;
            margin-top: 3vw;
        }

        .btn-order {
            padding: 2vw 3vw;
            border-radius: 1.5vw;
            font-size: 2vw;
            gap: 1vw;
            border-width: 0.2vw;
        }
        .btn-order iconify-icon { font-size: 2.5vw; }
    }

    /* ============================================
       RESPONSIVE - MOBILE
       ============================================ */
    @media (max-width: 480px) {
        .orders-tabs {
            display: flex;
            flex-wrap: nowrap;
            gap: 0;
            width: 100%;
            max-width: 100%;
            border-bottom: 0.3vw solid #e5e7eb;
            margin-bottom: 4vw;
            /* 🔥 Scroll horizontal */
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            /* 🔥 Padding kiri-kanan agar tab pertama & terakhir tidak mentok */
            padding: 0 2vw;
            box-sizing: border-box;
            /* ❌ JANGAN pakai scroll-snap-type, mask-image, scroll-padding */
        }

        .orders-tabs::-webkit-scrollbar {
            display: none;
            height: 0;
            width: 0;
        }

        .order-tab {
            display: inline-flex;
            align-items: center;
            gap: 1.5vw;
            padding: 3vw 3.5vw;
            font-size: 3.2vw;
            font-weight: 500;
            color: #64748b;
            text-decoration: none;
            border-bottom: 0.6vw solid transparent;
            white-space: nowrap;
            flex-shrink: 0;
            flex-grow: 0;
            /* ❌ JANGAN pakai scroll-snap-align, scroll-margin */
            transition: color 0.2s ease, border-color 0.2s ease;
        }

        .order-tab.iconify-icon,
        .order-tab > iconify-icon {
            font-size: 4vw;
        }

        .order-tab.active {
            color: rgb(102, 72, 9);
            border-bottom-color: #ecbc42;
            font-weight: 700;
        }

        .order-tab-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 5vw;
            height: 5vw;
            padding: 0 1.5vw;
            border-radius: 100vw;
            font-size: 2.5vw;
            font-weight: 700;
            background: #f1f5f9;
            color: #64748b;
            flex-shrink: 0;
        }

        .order-tab.active .order-tab-badge {
            background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
            color: rgb(102, 72, 9);
        }

        /* Sisanya tetap sama seperti sebelumnya */
        .orders-empty {
            padding: 10vw 3vw;
        }

        .orders-empty-icon {
            width: 20vw;
            height: 20vw;
        }
        .orders-empty-icon iconify-icon {
            font-size: 10vw;
        }

        .orders-empty h3 {
            font-size: 4vw;
            margin-top: 4vw;
        }
        .orders-empty p {
            font-size: 3vw;
            margin-top: 2vw;
        }
        .orders-empty-btn {
            margin-top: 5vw;
            padding: 3.5vw 6vw;
            border-radius: 2.5vw;
            font-size: 3.2vw;
            gap: 1.5vw;
        }

        .orders-list { gap: 4vw; }

        .order-card {
            padding: 4vw;
            border-radius: 3vw;
            border-width: 0.3vw;
        }

        .order-head {
            gap: 2vw;
        }

        .order-number {
            font-size: 3.5vw;
            gap: 1.5vw;
        }
        .order-number iconify-icon { font-size: 4vw; }

        .order-date {
            font-size: 2.8vw;
            gap: 1vw;
            margin-top: 1vw;
        }
        .order-date iconify-icon { font-size: 3.3vw; }

        .order-total-price { font-size: 4.2vw; }

        .order-total-count {
            font-size: 2.8vw;
            gap: 1vw;
            margin-top: 0.8vw;
        }
        .order-total-count iconify-icon { font-size: 3.3vw; }

        .order-items {
            margin-top: 4vw;
            padding-top: 4vw;
            gap: 3vw;
            border-top-width: 0.3vw;
        }

        .order-item {
            gap: 2.5vw;
            max-width: 70vw;
        }

        .order-item-img {
            width: 14vw;
            height: 14vw;
            border-radius: 2.5vw;
        }
        .order-item-img iconify-icon { font-size: 6vw; }

        .order-item-name { font-size: 3.2vw; }
        .order-item-qty { font-size: 2.7vw; margin-top: 0.7vw; }

        .order-item-more {
            padding: 2.5vw 3.5vw;
            border-radius: 2.5vw;
            font-size: 3vw;
            gap: 1.2vw;
        }

        .order-actions {
            gap: 2.5vw;
            margin-top: 4vw;
        }

        .btn-order {
            flex: 1;
            min-width: calc(50% - 1.5vw);
            padding: 3vw 4vw;
            border-radius: 2.5vw;
            font-size: 3vw;
            gap: 1.5vw;
            border-width: 0.3vw;
            justify-content: center;
        }
        .btn-order iconify-icon { font-size: 4vw; }
    }
</style>

{{-- 🔥 TABS --}}
<div class="tab_layout">
    <div class="orders-tabs">
        <a href="{{ route('customer.orders', ['tab' => 'unpaid']) }}" 
        class="order-tab {{ $activeTab == 'unpaid' ? 'active' : '' }}">
            <iconify-icon icon="mdi:clock-alert-outline"></iconify-icon>
            Belum Bayar
            <span class="order-tab-badge">{{ $unpaidCount ?? 0 }}</span>
        </a>
        <a href="{{ route('customer.orders', ['tab' => 'processing']) }}" 
        class="order-tab {{ $activeTab == 'processing' ? 'active' : '' }}">
            <iconify-icon icon="mdi:package-variant-closed"></iconify-icon>
            Sedang Dikemas
            <span class="order-tab-badge">{{ $processingCount ?? 0 }}</span>
        </a>
        <a href="{{ route('customer.orders', ['tab' => 'shipped']) }}" 
        class="order-tab {{ $activeTab == 'shipped' ? 'active' : '' }}">
            <iconify-icon icon="mdi:truck-fast-outline"></iconify-icon>
            Dikirim
            <span class="order-tab-badge">{{ $shippedCount ?? 0 }}</span>
        </a>
        <a href="{{ route('customer.orders', ['tab' => 'completed']) }}" 
        class="order-tab {{ $activeTab == 'completed' ? 'active' : '' }}">
            <iconify-icon icon="mdi:check-circle-outline"></iconify-icon>
            Selesai
            <span class="order-tab-badge">{{ $completedCount ?? 0 }}</span>
        </a>
    </div>
</div>

@if ($orders->isEmpty())
    {{-- ============================================ --}}
    {{-- EMPTY STATE --}}
    {{-- ============================================ --}}
    <div class="orders-empty">
        <div class="orders-empty-icon">
            <iconify-icon icon="mdi:package-variant"></iconify-icon>
        </div>
        <h3>Belum Ada Pesanan</h3>
        <p>
            Kamu belum melakukan pemesanan apapun.<br>
            Yuk, mulai belanja sekarang!
        </p>
        <a href="{{ route('customer.products.index') }}" class="orders-empty-btn">
            <iconify-icon icon="mdi:cart-outline"></iconify-icon>
            Mulai Belanja
        </a>
    </div>
@else
    {{-- ============================================ --}}
    {{-- ORDER LIST --}}
    {{-- ============================================ --}}
    <div class="orders-list">
        @foreach ($orders as $order)
            <div class="order-card">
                {{-- Header --}}
                <div class="order-head">
                    <div>
                        <div class="order-number">
                            <iconify-icon icon="mdi:receipt-text-outline"></iconify-icon>
                            #{{ $order->order_number }}
                        </div>
                        <div class="order-date">
                            <iconify-icon icon="mdi:calendar-clock-outline"></iconify-icon>
                            {{ $order->created_at->translatedFormat('d M Y, H:i') }}
                        </div>
                    </div>
                    <div class="order-total">
                        <div class="order-total-price">
                            Rp {{ number_format($order->total ?? 0, 0, ',', '.') }}
                        </div>
                        <div class="order-total-count">
                            <iconify-icon icon="mdi:package-variant-closed"></iconify-icon>
                            {{ $order->items->count() ?? 0 }} produk
                        </div>
                    </div>
                </div>

                {{-- Items Preview --}}
                <div class="order-items">
                    @foreach ($order->items->take(3) as $item)
                        <div class="order-item">
                            <div class="order-item-img">
                                @php
                                    $itemImage = $item->variant ? $item->variant->image_url : null;
                                    if (!$itemImage && $item->product && $item->product->images->first()) {
                                        $itemImage = \Illuminate\Support\Facades\Storage::url($item->product->images->first()->image);
                                    }
                                @endphp

                                @if ($itemImage)
                                    <img src="{{ $itemImage }}" alt="{{ $item->product_name }}">
                                @else
                                    <iconify-icon icon="mdi:image-off-outline"></iconify-icon>
                                @endif
                            </div>
                            <div class="order-item-info">
                                <div class="order-item-name">{{ $item->product_name }}</div>
                                <div class="order-item-qty">
                                    {{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if ($order->items->count() > 3)
                        <div class="order-item-more">
                            <iconify-icon icon="mdi:plus-circle-outline"></iconify-icon>
                            +{{ $order->items->count() - 3 }} lainnya
                        </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="order-actions">
                    <a href="{{ route('customer.orders.show', $order) }}" class="btn-order btn-order-outline">
                        <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                        Lihat Detail
                    </a>

                    @if($order->shipping_status == 'pending' && $order->payment_status == 'unpaid')
                        <a href="{{ route('customer.midtrans.pay', $order) }}" class="btn-order btn-order-success">
                            <iconify-icon icon="mdi:credit-card-outline"></iconify-icon>
                            Bayar Sekarang
                        </a>

                        <form action="{{ route('customer.orders.cancel-direct', $order) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('Anda yakin ingin membatalkan pesanan #{{ $order->order_number }}?\n\nStok produk akan dikembalikan otomatis.')"
                                    class="btn-order btn-order-danger">
                                <iconify-icon icon="mdi:close-circle-outline"></iconify-icon>
                                Batalkan Pesanan
                            </button>
                        </form>
                    @endif

                    @if($order->shipping_status == 'shipped')
                        <form action="{{ route('customer.orders.confirm-received', $order) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('Anda yakin sudah menerima pesanan #{{ $order->order_number }}?\n\nSetelah dikonfirmasi, status pesanan akan berubah menjadi Selesai.')"
                                    class="btn-order btn-order-success">
                                <iconify-icon icon="mdi:check-circle-outline"></iconify-icon>
                                Pesanan Diterima
                            </button>
                        </form>
                    @endif

                    @if($order->shipping_status == 'delivered' || $order->delivered_at)
                        <div class="testimonial-btn-wrapper">
                            <button type="button"
                                    onclick="openTestimonialModal({{ $order->id }})"
                                    class="btn-order btn-order-success">
                                <iconify-icon icon="mdi:star-outline"></iconify-icon>
                                Beri Testimonial
                            </button>
                        </div>

                        @php
                            $testimonialCustomer = Auth::guard('customer')->user();
                        @endphp
                        @include('customer.testimonial._modal', ['order' => $order, 'customer' => $testimonialCustomer])
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $orders->appends(['tab' => $activeTab])->links() }}
    </div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 🔥 STAR RATING
    document.querySelectorAll('[id^="rating_wrapper_"]').forEach(function(wrapper) {
        const orderId = wrapper.id.replace('rating_wrapper_', '');
        const ratingInputs = wrapper.querySelectorAll('.rating-input-' + orderId);
        const ratingLabel = document.getElementById('rating-label-' + orderId);
        const starLabels = wrapper.querySelectorAll('.star-label-' + orderId);

        function updateStars(rating) {
            if (ratingLabel) ratingLabel.textContent = rating + '/5';
            starLabels.forEach(function(sl) {
                const idx = parseInt(sl.getAttribute('data-index'));
                const icon = sl.querySelector('.star-icon');
                if (icon) {
                    icon.setAttribute('icon', idx <= rating ? 'mdi:star' : 'mdi:star-outline');
                    icon.className = 'star-icon h-7 w-7 cursor-pointer ' + (idx <= rating ? 'text-yellow-400' : 'text-gray-300');
                }
            });
        }

        ratingInputs.forEach(function(input) {
            input.addEventListener('change', function() {
                updateStars(parseInt(this.value));
            });
        });

        updateStars(5);
    });

    // 🔥 IMAGE PREVIEW
    document.querySelectorAll('[id^="images_"]').forEach(function(input) {
        input.addEventListener('change', function(e) {
            const orderId = input.id.replace('images_', '');
            const preview = document.getElementById('image-preview-' + orderId);
            preview.innerHTML = '';

            const files = Array.from(e.target.files);
            if (files.length === 0) return;

            files.slice(0, 10).forEach(function(file) {
                if (!file.type.startsWith('image/')) return;

                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative h-20 w-20';
                    div.innerHTML = '<img src="' + e.target.result + '" alt="Preview" class="h-full w-full rounded-lg object-cover ring-1 ring-gray-200">';
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });
    });
});

// 🔥 OPEN / CLOSE MODAL
window.openTestimonialModal = function(orderId) {
    const modal = document.getElementById('testimonial-modal-' + orderId);
    if (modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
};

window.closeTestimonialModal = function(orderId) {
    const modal = document.getElementById('testimonial-modal-' + orderId);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
};

// 🔥 SUBMIT FORM VIA AJAX
window.submitTestimonialForm = function(orderId) {
    const form = document.getElementById('testimonial-form-' + orderId);
    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeTestimonialModal(orderId);
            const btnWrapper = document.querySelector('[onclick="openTestimonialModal(' + orderId + ')"]')?.closest('.testimonial-btn-wrapper');
            if (btnWrapper) {
                btnWrapper.innerHTML = '<span class="text-sm text-gray-500" style="display:inline-flex;align-items:center;gap:6px;"><iconify-icon icon="mdi:check-circle"></iconify-icon> Testimonial terkirim</span>';
            }
        } else {
            alert(data.message || 'Gagal mengirim testimonial.');
        }
    })
    .catch(function(error) {
        console.error(error);
        alert('Terjadi kesalahan. Coba lagi.');
    });
};

// 🔥 CLOSE ON OUTSIDE CLICK
document.addEventListener('click', function(e) {
    document.querySelectorAll('[id^="testimonial-modal-"]').forEach(function(modal) {
        if (e.target === modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    });
});

// 🔥 CLOSE ON ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('[id^="testimonial-modal-"]').forEach(function(modal) {
            if (modal.style.display === 'block') {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
        });
    }
});

window.removePreview = function(button, index) {
    const container = button.closest('.relative');
    container.remove();
};
</script>
@endpush

@endsection