<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Akun Saya - Barokah Sport')</title>

    {{-- Tailwind CSS --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/popup.css') }}">
    <link rel="stylesheet" href="{{ asset('css/product_show.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cart-page.css') }}">
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
    <link rel="stylesheet" href="{{ asset('css/variant-modal.css') }}">
    <link rel="icon" src="{{ $setting?->favicon ? Storage::url($setting->favicon) : asset('images/favicon.png') }}" type="image/png">
    <link rel="shortcut icon" href="{{ $setting?->favicon ? Storage::url($setting->favicon) : asset('images/favicon.png') }}" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.iconify.design/iconify-icon/3.0.0/iconify-icon.min.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Hanken+Grotesk:ital,wght@0,100..900;1,100..900&display=swap');
        
        * {
            font-family: "Hanken Grotesk", sans-serif;
        }

        .account-container {
            width: 100%;
            min-height: 70vh;
            padding: 3vw 7.54vw 5vw;
            background: #f8fafc;
            border-top: 0.1vw solid #076694;
        }

        .account-sidebar {
            background: #ffffff;
            border-radius: 1vw;
            border: 0.1vw solid #e2e8f0;
            padding: 1.2vw;
            position: sticky;
            top: 8vw;
            height: fit-content;
        }

        .account-sidebar .sidebar-profile {
            display: flex;
            align-items: center;
            gap: 1vw;
            padding-bottom: 1.2vw;
            border-bottom: 0.1vw solid #e2e8f0;
            margin-bottom: 1.2vw;
        }

        .account-sidebar .sidebar-profile .avatar {
            width: 4vw;
            height: 4vw;
            border-radius: 50%;
            background: #076694;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6vw;
            font-weight: 700;
            flex-shrink: 0;
        }

        .account-sidebar .sidebar-profile .profile-info .name {
            font-size: 1vw;
            font-weight: 600;
            color: #0f172a;
        }

        .account-sidebar .sidebar-profile .profile-info .email {
            font-size: 0.7vw;
            color: #94a3b8;
        }

        .account-sidebar .sidebar-menu {
            display: flex;
            flex-direction: column;
            gap: 0.3vw;
        }

        .account-sidebar .sidebar-menu .menu-link {
            display: flex;
            align-items: center;
            gap: 0.8vw;
            padding: 0.7vw 1vw;
            border-radius: 0.5vw;
            font-size: 0.8vw;
            font-weight: 500;
            color: #475569;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
        }

        .account-sidebar .sidebar-menu .menu-link:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .account-sidebar .sidebar-menu .menu-link.active {
            background: #eff6ff;
            color: #076694;
        }

        .account-sidebar .sidebar-menu .menu-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 10%;
            height: 80%;
            width: 0.3vw;
            background: #076694;
            border-radius: 0.2vw;
        }

        .account-sidebar .sidebar-menu .menu-link iconify-icon {
            font-size: 1.2vw;
            flex-shrink: 0;
        }

        .account-sidebar .sidebar-menu .menu-link .menu-badge {
            margin-left: auto;
            padding: 0.1vw 0.6vw;
            background: #ef4444;
            color: #ffffff;
            border-radius: 100vw;
            font-size: 0.55vw;
            font-weight: 600;
        }

        .account-sidebar .sidebar-menu .menu-link .menu-badge.count {
            background: #076694;
        }

        .account-sidebar .sidebar-divider {
            border-top: 0.05vw solid #e2e8f0;
            margin: 0.8vw 0;
        }

        .account-sidebar .btn-logout-sidebar {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 0.8vw;
            padding: 0.7vw 1vw;
            border-radius: 0.5vw;
            font-size: 0.8vw;
            font-weight: 500;
            color: #ef4444;
            background: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .account-sidebar .btn-logout-sidebar:hover {
            background: #fef2f2;
        }

        .account-sidebar .btn-logout-sidebar iconify-icon {
            font-size: 1.2vw;
        }

        .account-content {
            background: #ffffff;
            border-radius: 1vw;
            border: 0.1vw solid #e2e8f0;
            padding: 2vw;
            box-shadow: 0 0.1vw 0.5vw rgba(0, 0, 0, 0.03);
        }

        .account-content .content-header {
            margin-bottom: 1.5vw;
            padding-bottom: 1vw;
            border-bottom: 0.1vw solid #e2e8f0;
        }

        .account-content .content-header h1 {
            font-size: 1.6vw;
            font-weight: 700;
            color: #0f172a;
            font-family: heading, sans-serif;
            text-transform:uppercase;
        }

        .account-content .content-header p {
            font-size: 0.8vw;
            color: #94a3b8;
            margin-top: 0.2vw;
        }

        .account-grid {
            display: grid;
            grid-template-columns: 22% 78%;
            gap: 2vw;
            align-items: start;
        }

        /* ============================================
           RESPONSIVE
           ============================================ */
        @media (max-width: 1024px) {
            .account-grid {
                grid-template-columns: 1fr;
                gap: 3vw;
            }

            .account-sidebar {
                position: static;
                padding: 3vw;
                border-radius: 1.5vw;
            }

            .account-sidebar .sidebar-profile .avatar {
                width: 8vw;
                height: 8vw;
                font-size: 3vw;
            }

            .account-sidebar .sidebar-profile .profile-info .name {
                font-size: 2.5vw;
            }

            .account-sidebar .sidebar-profile .profile-info .email {
                font-size: 1.8vw;
            }

            .account-sidebar .sidebar-menu .menu-link {
                font-size: 2vw;
                padding: 1.5vw 2vw;
                gap: 1.5vw;
            }

            .account-sidebar .sidebar-menu .menu-link iconify-icon {
                font-size: 2.8vw;
            }

            .account-sidebar .btn-logout-sidebar {
                font-size: 2vw;
                padding: 1.5vw 2vw;
            }

            .account-sidebar .btn-logout-sidebar iconify-icon {
                font-size: 2.8vw;
            }

            .account-content {
                padding: 3vw;
                border-radius: 1.5vw;
            }

            .account-content .content-header h1 {
                font-size: 3.5vw;
            }

            .account-content .content-header p {
                font-size: 2vw;
            }
        }

        @media (max-width: 480px) {
            .account-container {
                padding: 3vw 3vw 8vw;
            }

            .account-grid {
                gap: 4vw;
            }

            .account-sidebar {
                padding: 4vw;
                border-radius: 2vw;
            }

            .account-sidebar .sidebar-profile .avatar {
                width: 10vw;
                height: 10vw;
                font-size: 4vw;
            }

            .account-sidebar .sidebar-profile .profile-info .name {
                font-size: 3.2vw;
            }

            .account-sidebar .sidebar-profile .profile-info .email {
                font-size: 2.2vw;
            }

            .account-sidebar .sidebar-menu .menu-link {
                font-size: 2.5vw;
                padding: 2vw 2.5vw;
                gap: 2vw;
            }

            .account-sidebar .sidebar-menu .menu-link iconify-icon {
                font-size: 3.5vw;
            }

            .account-sidebar .sidebar-menu .menu-link .menu-badge {
                font-size: 1.8vw;
                padding: 0.2vw 1.2vw;
            }

            .account-sidebar .btn-logout-sidebar {
                font-size: 2.5vw;
                padding: 2vw 2.5vw;
            }

            .account-sidebar .btn-logout-sidebar iconify-icon {
                font-size: 3.5vw;
            }

            .account-content {
                padding: 4vw;
                border-radius: 2vw;
            }

            .account-content .content-header h1 {
                font-size: 4.5vw;
            }

            .account-content .content-header p {
                font-size: 2.5vw;
            }
        }
    </style>
</head>

<body>

    @include('customer.partials.navbar')
    @include('customer.partials.search-popup')
    @include('customer.partials.login-popup')

    <div id="cart-popup" class="popup-slide">
        <div class="popup_slide_overlay"></div>
        <div class="popup-slide-box">
            <div class="popup-header">
                <h2>
                    <iconify-icon icon="mdi:cart-outline"></iconify-icon>
                    Keranjang Belanja
                </h2>
                <div class="popup-header-actions">
                    <button id="cart-clear" class="btn-clear">
                        <iconify-icon icon="mdi:delete-outline"></iconify-icon>
                        Kosongkan
                    </button>
                    <button id="wishlist-toggle" class="icon-btn icon-btn-wishlist" style="position:relative;" aria-label="Wishlist">
                        <iconify-icon icon="mynaui:heart"></iconify-icon> Wishlist
                    </button>
                    <button id="cart-close" class="btn-close" aria-label="Tutup">✕</button>
                </div>
            </div>

            <div id="cart-body" class="popup-body">
                <div id="cart-content">
                    <div class="popup-body-empty">
                        <iconify-icon icon="mdi:cart-outline"></iconify-icon>
                        <p>Keranjang kosong</p>
                        <p>Yuk, mulai belanja!</p>
                    </div>
                </div>
            </div>

            <div id="cart-footer" class="popup-footer">
                <div class="popup-footer-inner">
                    <div>
                        <p class="popup-footer-total-label">Total Belanja</p>
                        <p id="cart-total" class="popup-footer-total">Rp 0</p>
                    </div>
                    <div class="popup-footer-buttons">
                        <a href="{{ route('customer.cart.index') }}" class="btn-outline">
                            <iconify-icon icon="mdi:eye-outline" width="16"></iconify-icon>
                            Lihat
                        </a>
                        <button onclick="goToCheckout()" class="btn-primary" style="border:none;cursor:pointer;">
                            Checkout
                            <iconify-icon icon="mdi:arrow-right" width="16"></iconify-icon>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="wishlist-popup" class="popup-slide">
        <div class="popup_slide_overlay"></div>
        <div class="popup-slide-box">
            <div class="popup-header">
                <h2>
                    <iconify-icon icon="mdi:heart-outline"></iconify-icon>
                    Wishlist
                </h2>
                <div class="popup-header-actions">
                    <button id="wishlist-clear" class="btn-clear" style="display: none;">
                        <iconify-icon icon="mdi:delete-outline"></iconify-icon>
                        Kosongkan
                    </button>
                    <button id="wishlist-close" class="btn-close" aria-label="Tutup">✕</button>
                </div>
            </div>

            <div id="wishlist-body" class="popup-body">
                <div id="wishlist-content">
                    <div class="popup-body-empty">
                        <iconify-icon icon="mdi:heart-outline"></iconify-icon>
                        <p>Wishlist kosong</p>
                        <p>Simpan produk favoritmu di sini!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="account-container">
        <div class="account-grid">

            {{-- ============================================ --}}
            {{-- SIDEBAR --}}
            {{-- ============================================ --}}
            <aside class="account-sidebar">
                {{-- Profile --}}
                <div class="sidebar-profile">
                    <div class="avatar">
                        {{ strtoupper(substr(Auth::guard('customer')->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="profile-info">
                        <div class="name">{{ Auth::guard('customer')->user()->name ?? 'User' }}</div>
                        <div class="email">{{ Auth::guard('customer')->user()->email ?? '' }}</div>
                    </div>
                </div>

                {{-- Menu --}}
                <nav class="sidebar-menu">
                    <a href="{{ route('customer.account') }}" 
                       class="menu-link {{ request()->routeIs('customer.account') ? 'active' : '' }}">
                        <iconify-icon icon="mdi:account-outline"></iconify-icon>
                        Profil Saya
                    </a>

                    <a href="{{ route('customer.orders') }}" 
                       class="menu-link {{ request()->routeIs('customer.orders*') ? 'active' : '' }}">
                        <iconify-icon icon="mdi:package-variant"></iconify-icon>
                        Pesanan Saya
                        @if($orderCount ?? 0 > 0)
                            <span class="menu-badge count">{{ $orderCount ?? 0 }}</span>
                        @endif
                    </a>

                    <a href="{{ route('customer.addresses.index') }}" 
                       class="menu-link {{ request()->routeIs('customer.addresses*') ? 'active' : '' }}">
                        <iconify-icon icon="mdi:map-marker"></iconify-icon>
                        Alamat Pengiriman
                    </a>

                    <a href="{{ route('customer.wishlist.index') }}" 
                       class="menu-link {{ request()->routeIs('customer.wishlist*') ? 'active' : '' }}">
                        <iconify-icon icon="mdi:heart"></iconify-icon>
                        Wishlist
                        @if($wishlistCount ?? 0 > 0)
                            <span class="menu-badge count">{{ $wishlistCount ?? 0 }}</span>
                        @endif
                    </a>

                    <a href="{{ route('customer.vouchers.index') }}" 
                       class="menu-link {{ request()->routeIs('customer.vouchers*') ? 'active' : '' }}">
                        <iconify-icon icon="mdi:ticket-percent"></iconify-icon>
                        Voucher Saya
                        @if($availableVouchers ?? 0 > 0)
                            <span class="menu-badge count">{{ $availableVouchers ?? 0 }}</span>
                        @endif
                    </a>
                </nav>

                <div class="sidebar-divider"></div>

                {{-- Logout --}}
                <form action="{{ route('customer.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout-sidebar">
                        <iconify-icon icon="mdi:logout"></iconify-icon>
                        Logout
                    </button>
                </form>
            </aside>

            {{-- ============================================ --}}
            {{-- CONTENT --}}
            {{-- ============================================ --}}
            <main class="account-content">
                @if (session('success'))
                    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-700">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="content-header">
                    <h1>@yield('page-title', 'Akun Saya')</h1>
                    <p>@yield('page-subtitle', 'Kelola data akun Anda.')</p>
                </div>

                <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

                @yield('account-content')
            </main>

        </div>
    </div>

    @include('customer.partials.footer')

    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <script>
        window.customerRoutes = {
            cartAdd: '{{ route("customer.cart.add") }}',
            cartPopup: '{{ route("customer.cart.popup") }}',
            cartCount: '{{ route("customer.cart.count") }}',
            cartUpdate: '{{ route("customer.cart.update") }}',
            cartRemove: '{{ route("customer.cart.remove") }}',
            cartClear: '{{ route("customer.cart.clear") }}',
            buyNow: '{{ route("customer.cart.buy-now") }}',
            wishlistAdd: '{{ route("customer.wishlist.add") }}',
            wishlistPopup: '{{ route("customer.wishlist.popup") }}',
            wishlistStatus: '{{ route("customer.wishlist.status") }}',
            wishlistRemove: '{{ route("customer.wishlist.remove") }}',
            wishlistClear: '{{ route("customer.wishlist.clear") }}',
            checkout: '{{ route("customer.checkout.index") }}',
            login: '{{ route("customer.login") }}',
        };

        function openSearchPopup() {
            const overlay = document.getElementById('search-popup-overlay');
            if (overlay) {
                overlay.classList.remove('fade-out');
                overlay.style.display = 'flex';
                void overlay.offsetWidth;
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';

                setTimeout(function() {
                    const input = document.getElementById('search-popup-input');
                    if (input) {
                        input.focus();
                        input.select();
                    }
                }, 200);
            }
        }

        function closeSearchPopup() {
            const overlay = document.getElementById('search-popup-overlay');
            if (overlay) {
                overlay.classList.add('fade-out');
                overlay.classList.remove('active');
                setTimeout(function() {
                    overlay.style.display = 'none';
                    overlay.classList.remove('fade-out');
                }, 300);
                document.body.style.overflow = '';
            }
        }

        function goToCheckout() {
            const cartCount = parseInt(document.getElementById('cart-count')?.textContent || 0);
            if (cartCount === 0) {
                if (typeof window.showToast === 'function') {
                    window.showToast('Keranjang kosong. Tambahkan produk terlebih dahulu.', 'warning');
                }
                return;
            }

            const popup = document.getElementById('cart-popup');
            if (popup) {
                popup.classList.remove('active');
                document.body.classList.remove('popup-open');
            }

            window.location.href = window.customerRoutes.checkout || '{{ route("customer.checkout.index") }}';
        }

        window.goToCheckout = goToCheckout;
        window.openSearchPopup = openSearchPopup;
        window.closeSearchPopup = closeSearchPopup;
    </script>

    <script src="{{ asset('js/cart.js') }}"></script>
    <script src="{{ asset('js/popup.js') }}"></script>

    @stack('scripts')

</body>

</html>