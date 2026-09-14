<div class="header_section">
    @php
        $promoBar = App\Models\PromoBar::getActive();
    @endphp
    <div class="top_section">
        <div class="text_top_section">
            @if($promoBar && $promoBar->text_left)
                <p>{{ $promoBar->text_left }}</p>
            @else
                <p>GRATIS ONGKIR UNTUK PESANAN DI ATAS Rp750.000</p>
            @endif
        </div>
        <div class="text_top_section">
            @if($promoBar && $promoBar->text_right)
                <p>{{ $promoBar->text_right }}</p>
            @else
                <p>DISKON 20% UNTUK PESANAN PERTAMA | KODE: BAROKAH01</p>
            @endif
        </div>
        <div class="text_top_section">
            <div class="text_top_section_link">
                <a href="{{ route('customer.help') }}">
                    BANTUAN & DUKUNGAN
                </a>
                |
                <a href="{{ route('customer.contact') }}">
                    LOKASI TOKO
                </a>
            </div>
        </div>
    </div>
    <div class="marquee-wrapper">
        <div class="marquee-content">
            <!-- Item 1 - Kiri -->
            <div class="marquee-item">
                @if($promoBar && $promoBar->text_left)
                    {{ $promoBar->text_left }}
                @else
                    <span>GRATIS ONGKIR UNTUK PESANAN DI ATAS <span class="highlight">Rp750.000</span></span>
                @endif
                <span class="separator">✦</span>
            </div>

            <!-- Item 2 - Kanan -->
            <div class="marquee-item">
                @if($promoBar && $promoBar->text_right)
                    {{ $promoBar->text_right }}
                @else
                    <span>DISKON <span class="highlight">20%</span> UNTUK PESANAN PERTAMA</span>
                    <span style="background: #ff5722; color: #fff; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 700;">KODE: BAROKAH01</span>
                @endif
                <span class="separator">✦</span>
            </div>

            <!-- Item 3 - Duplikat untuk seamless loop -->
            <div class="marquee-item">
                @if($promoBar && $promoBar->text_left)
                    {{ $promoBar->text_left }}
                @else
                    <span>GRATIS ONGKIR UNTUK PESANAN DI ATAS <span class="highlight">Rp750.000</span></span>
                @endif
                <span class="separator">✦</span>
            </div>

            <!-- Item 4 - Duplikat -->
            <div class="marquee-item">
                @if($promoBar && $promoBar->text_right)
                    {{ $promoBar->text_right }}
                @else
                    <span>DISKON <span class="highlight">20%</span> UNTUK PESANAN PERTAMA</span>
                    <span style="background: #ff5722; color: #fff; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 700;">KODE: BAROKAH01</span>
                @endif
                <span class="separator">✦</span>
            </div>
        </div>
    </div>
    <div class="bottom_section">
        {{-- Hamburger Button --}}
        <button class="hamburger-btn" id="hamburgerBtn" aria-label="Toggle Menu">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>

        {{-- Logo --}}
        <a href="{{ route('customer.home') }}" class="logo-wrapper">
            <img src="{{ $setting?->logo ? Storage::url($setting->logo) : asset('images/logo.png') }}" alt="Logo" class="logo_dekstop">
            <img src="{{ $setting?->favicon ? Storage::url($setting->favicon) : asset('images/favicon.png') }}" alt="Logo" class="logo_mobile">
        </a>

        {{-- Navigation Menu --}}
        <nav class="nav-menu" id="navMenu">
            <div class="logo_mobile_nav">
                <img src="{{ $setting?->logo ? Storage::url($setting->logo) : asset('images/logo.png') }}" alt="Logo">
                <button class="close-btn-nav" id="closeBtn" aria-label="Close Menu">
                    <iconify-icon icon="mingcute:close-line"></iconify-icon>
                </button>
            </div>
            <ul>
                <a href="/" class="{{ request()->is('/') ? 'active' : '' }}">
                    <li>
                        Beranda
                        <iconify-icon icon="eva:chevron-right-outline"></iconify-icon>
                    </li>
                </a>
                <a href="/katalog" class="{{ request()->is('katalog') ? 'active' : '' }}">
                    <li>
                        Katalog
                        <iconify-icon icon="eva:chevron-right-outline"></iconify-icon>
                    </li>
                </a>
                <a href="{{ route('customer.products.latest') }}" class="{{ request()->is('katalog/terbaru') ? 'active' : '' }}">
                    <li>
                        Produk Terbaru
                        <iconify-icon icon="eva:chevron-right-outline"></iconify-icon>
                    </li>
                </a>
                <a href="{{ route('customer.products.promo') }}" class="{{ request()->is('katalog/promo') ? 'active' : '' }}">
                    <li>
                        Promo
                        <iconify-icon icon="eva:chevron-right-outline"></iconify-icon>
                    </li>
                </a>
                <a href="{{ route('customer.articles.index') }}" class="{{ request()->is('artikel') ? 'active' : '' }}">
                    <li>
                        Artikel
                        <iconify-icon icon="eva:chevron-right-outline"></iconify-icon>
                    </li>
                </a>
                 <a href="{{ route('customer.cara-pesan') }}" class="{{ request()->is('cara-pesan') ? 'active' : '' }}">
                     <li>
                         Cara Pesan
                         <iconify-icon icon="eva:chevron-right-outline"></iconify-icon>
                     </li>
                 </a>
             </ul>

             {{-- 🔥 Mobile Account Submenu (Customer only) --}}
            @auth('customer')
            @php
                $navUser = Auth::guard('customer')->user();
            @endphp
            <div class="mobile-account-menu">
                {{-- 🔥 Profile Card di Mobile --}}
                

                <div class="account-divider">
                    <span class="account-divider-label">Akun Saya</span>
                </div>
                <div class="mobile-account-profile">
                    <div class="mobile-account-avatar">
                        @if($navUser && $navUser->avatar)
                            <img src="{{ Storage::url($navUser->avatar) }}" 
                                alt="{{ $navUser->name }}">
                        @else
                            {{ strtoupper(substr($navUser->name ?? 'U', 0, 1)) }}
                        @endif
                    </div>
                    <div class="mobile-account-info">
                        <div class="mobile-account-name">{{ $navUser->name ?? 'User' }}</div>
                        <div class="mobile-account-email">{{ $navUser->email ?? '' }}</div>
                    </div>
                </div>
                <ul class="account-submenu">
                    <a href="{{ route('customer.account') }}" class="{{ request()->routeIs('customer.account') ? 'active' : '' }}">
                        <li>
                            <iconify-icon icon="mdi:account-outline"></iconify-icon>
                            <span>Profil Saya</span>
                        </li>
                    </a>
                    <a href="{{ route('customer.orders') }}" class="{{ request()->routeIs('customer.orders*') ? 'active' : '' }}">
                        <li>
                            <iconify-icon icon="mdi:package-variant"></iconify-icon>
                            <span>Pesanan Saya</span>
                        </li>
                    </a>
                    <a href="{{ route('customer.addresses.index') }}" class="{{ request()->routeIs('customer.addresses*') ? 'active' : '' }}">
                        <li>
                            <iconify-icon icon="mdi:map-marker"></iconify-icon>
                            <span>Alamat Pengiriman</span>
                        </li>
                    </a>
                    <a href="{{ route('customer.vouchers.index') }}" class="{{ request()->routeIs('customer.vouchers*') ? 'active' : '' }}">
                        <li>
                            <iconify-icon icon="mdi:ticket-percent"></iconify-icon>
                            <span>Voucher Saya</span>
                        </li>
                    </a>
                    <a href="{{ route('customer.password.change') }}" class="{{ request()->routeIs('customer.password*') ? 'active' : '' }}">
                        <li>
                            <iconify-icon icon="mdi:lock-outline"></iconify-icon>
                            <span>Ganti Kata Sandi</span>
                        </li>
                    </a>
                    <a href="{{ route('customer.testimonials.index') }}" class="{{ request()->routeIs('customer.testimonials*') ? 'active' : '' }}">
                        <li>
                            <iconify-icon icon="mdi:star-outline"></iconify-icon>
                            <span>Testimoni</span>
                        </li>
                    </a>
                </ul>
            </div>
            @endauth

            {{-- Mobile Auth & Icons --}}
            <div class="mobile-nav-footer">
                <div class="mobile-auth">
                    @auth('customer')
                        {{-- 🔥 Sudah login: Tampilkan Logout --}}
                        <form action="{{ route('customer.logout') }}" method="POST" class="mobile-logout-form">
                            @csrf
                            <button type="submit" class="mobile-logout-btn">
                                <iconify-icon icon="mdi:logout"></iconify-icon>
                                <span>Logout</span>
                            </button>
                        </form>
                    @else
                        {{-- 🔥 Belum login: Tampilkan Masuk / Daftar --}}
                        <a href="{{ route('customer.login') }}" class="mobile-login-btn">
                            <iconify-icon icon="iconamoon:profile-light"></iconify-icon>
                            <span>Masuk / Daftar</span>
                        </a>
                    @endauth
                </div>
            </div>
        </nav>

        {{-- Right Icons --}}
        <div class="btn_header_bottom">
            <button onclick="openSearchPopup()" class="icon-btn" aria-label="Cari Produk">
                <iconify-icon icon="mingcute:search-line"></iconify-icon>
            </button>

            @php
                $isLoggedIn = Auth::guard('customer')->check();
                $profileUrl = $isLoggedIn ? route('customer.account') : route('customer.login');
            @endphp
            <a href="{{ $profileUrl }}" class="icon-btn-link icon-btn-profile" aria-label="Profile">
                <button class="icon-btn" aria-label="Keranjang Belanja">
                    <iconify-icon icon="solar:cart-linear" aria-hidden="true"></iconify-icon>
                </button>
            </a>

            <button id="wishlist-toggle" class="icon-btn" aria-label="Wishlist Saya" style="position:relative;" aria-label="Wishlist">
                <iconify-icon icon="mynaui:heart"></iconify-icon>
                <span id="wishlist-count" style="display:none;">0</span>
            </button>

            <button id="cart-toggle" class="icon-btn" aria-label="Keranjang Belanja" style="position:relative;" aria-label="Cart">
                <iconify-icon icon="solar:cart-linear"></iconify-icon>
                <span id="cart-count" style="display:none;">0</span>
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const navMenu = document.getElementById('navMenu');
        const closeBtn = document.getElementById('closeBtn'); // 🔥 Tambahkan ini

        // Create overlay
        const overlay = document.createElement('div');
        overlay.className = 'nav-overlay';
        document.body.appendChild(overlay);

        function toggleMenu() {
            hamburgerBtn.classList.toggle('active');
            navMenu.classList.toggle('open');
            overlay.classList.toggle('show');

            // Prevent body scroll
            if (navMenu.classList.contains('open')) {
                document.body.style.overflow = 'hidden';
                document.body.style.position = 'fixed';
                document.body.style.width = '100%';
            } else {
                document.body.style.overflow = '';
                document.body.style.position = '';
                document.body.style.width = '';
            }
        }

        // 🔥 Event listener untuk hamburger button
        hamburgerBtn.addEventListener('click', toggleMenu);

        // 🔥 Event listener untuk close button (TAMBAHKAN INI)
        if (closeBtn) {
            closeBtn.addEventListener('click', toggleMenu);
        }

        // 🔥 Event listener untuk overlay
        overlay.addEventListener('click', toggleMenu);

        // Close menu when clicking a link
        document.querySelectorAll('.nav-menu ul a, .account-submenu a').forEach(link => {
            link.addEventListener('click', function() {
                if (navMenu.classList.contains('open')) {
                    toggleMenu();
                }
            });
        });

        // Close menu on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && navMenu.classList.contains('open')) {
                toggleMenu();
            }
        });

        // 🔥 Opsional: Swipe to close (touch devices)
        let touchStartX = 0;
        let touchStartY = 0;
        
        navMenu.addEventListener('touchstart', function(e) {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
        }, { passive: true });

        navMenu.addEventListener('touchmove', function(e) {
            if (!navMenu.classList.contains('open')) return;
            
            const touchX = e.touches[0].clientX;
            const touchY = e.touches[0].clientY;
            const deltaX = touchX - touchStartX;
            const deltaY = touchY - touchStartY;
            
            // If swiping right more than 50px and horizontal movement > vertical
            if (deltaX > 50 && Math.abs(deltaX) > Math.abs(deltaY)) {
                toggleMenu();
            }
        }, { passive: true });
    });
</script>

<script>

    document.addEventListener('DOMContentLoaded', function() {
        // Panggil fungsi dari cart.js
        if (typeof window.loadCartCount === 'function') {
            window.loadCartCount();
        }
        
        if (typeof window.loadWishlistCount === 'function') {
            window.loadWishlistCount();
        }

        if (typeof window.loadWishlistStatus === 'function') {
            setTimeout(function() {
                window.loadWishlistStatus();
            }, 500);
        }
        
        // Jika cart.js belum load, gunakan fallback
        if (typeof window.loadCartCount !== 'function') {
            // Fallback: fetch langsung
            fetch('{{ route("customer.cart.count") }}')
                .then(response => response.json())
                .then(data => {
                    const cartCount = document.getElementById('cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.count || 0;
                        cartCount.style.display = (data.count > 0) ? 'inline-flex' : 'none';
                    }
                })
                .catch(() => {});
        }
        
        if (typeof window.loadWishlistCount !== 'function') {
            fetch('{{ route("customer.wishlist.popup") }}')
                .then(response => response.json())
                .then(data => {
                    const wishlistCount = document.getElementById('wishlist-count');
                    if (wishlistCount && data.count !== undefined) {
                        wishlistCount.textContent = data.count || 0;
                        wishlistCount.style.display = (data.count > 0) ? 'inline-flex' : 'none';
                    }
                })
                .catch(() => {});
        }
    });
</script>
