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

            {{-- Mobile Auth & Icons --}}
            <div class="mobile-nav-footer">
                <div class="mobile-auth">
                    @auth
                        @if(auth()->user()->role === 'customer' || auth()->user()->role === null)
                            <a href="{{ route('customer.account') }}">
                                <iconify-icon icon="iconamoon:profile-light"></iconify-icon>
                                Akun Saya
                            </a>
                        @else
                            <a href="{{ route('admin.dashboard') }}">
                                <iconify-icon icon="iconamoon:profile-light"></iconify-icon>
                                Dashboard
                            </a>
                        @endif
                    @else
                        <a href="{{ route('customer.login') }}">
                            <iconify-icon icon="iconamoon:profile-light"></iconify-icon>
                            Masuk / Daftar
                        </a>
                    @endauth
                </div>
            </div>
        </nav>

        {{-- Right Icons --}}
        <div class="btn_header_bottom">
            <button onclick="openSearchPopup()" class="icon-btn" aria-label="Search">
                <iconify-icon icon="mingcute:search-line"></iconify-icon>
            </button>

            @php
                $isLoggedIn = Auth::guard('customer')->check();
                $profileUrl = $isLoggedIn ? route('customer.account') : route('customer.login');
            @endphp
            <a href="{{ $profileUrl }}" class="icon-btn-link icon-btn-profile" aria-label="Profile">
                <button class="icon-btn">
                    <iconify-icon icon="iconamoon:profile-light"></iconify-icon>
                </button>
            </a>

            <button id="wishlist-toggle" class="icon-btn" style="position:relative;" aria-label="Wishlist">
                <iconify-icon icon="mynaui:heart"></iconify-icon>
                <span id="wishlist-count" style="display:none;">0</span>
            </button>

            <button id="cart-toggle" class="icon-btn" style="position:relative;" aria-label="Cart">
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
        document.querySelectorAll('.nav-menu ul a').forEach(link => {
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
