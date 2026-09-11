<!-- Search Popup Overlay -->
<div id="search-popup-overlay" class="search-popup-overlay" style="display: none;">
    <div class="search-popup-container">
        <button type="button" class="search-popup-close" onclick="closeSearchPopup()">
            <iconify-icon icon="mdi:close"></iconify-icon>
        </button>

        <div class="search-popup-content">
            {{-- Logo --}}
            <div class="search-popup-logo">
                <a href="{{ route('customer.home') }}" onclick="closeSearchPopup()">
                    <img src="{{ asset('images/logo.png') }}" alt="Barokah Sport">
                </a>
            </div>

            {{-- Search Input --}}
            <div class="search-popup-input-wrapper">
                <form id="search-popup-form" action="{{ route('customer.products.index') }}" method="GET">
                    <iconify-icon icon="mdi:search" class="search-popup-icon"></iconify-icon>
                    <input 
                        type="text" 
                        name="search" 
                        id="search-popup-input" 
                        class="search-popup-input" 
                        placeholder="Cari produk..." 
                        autocomplete="off"
                        autofocus
                        value="{{ request('search') }}"
                    >
                    <button type="submit" class="search-popup-submit">Cari</button>
                </form>
            </div>

            {{-- Trending Search --}}
            <div class="search-popup-trending">
                <div class="search-popup-trending-header">
                    <span>Trending Search</span>
                </div>
                <div class="search-popup-trending-list">
                    @php
                        $trendingSearches = [
                            'Celana Training',
                            'Set Olahraga',
                            'Oneset Kaos Celana',
                            'Jaket olahraga',
                            'Bordir Sablon',
                            'Celana Cargo',
                            'Parasut Vest',
                            'Jaket Varsity'
                        ];
                    @endphp
                    @foreach($trendingSearches as $trend)
                        <a href="{{ route('customer.products.index', ['search' => $trend]) }}" 
                           class="search-popup-trending-item"
                           onclick="closeSearchPopup()">
                            <iconify-icon icon="mdi:fire" class="trending-icon"></iconify-icon>
                            {{ $trend }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Navigation Links --}}
            <div class="search-popup-nav">
                <a href="{{ route('customer.about') }}" class="search-popup-nav-link" onclick="closeSearchPopup()">
                    <iconify-icon icon="mdi:information-outline"></iconify-icon>
                    Tentang Kami
                </a>
                <a href="{{ route('customer.contact') }}#faq-section" class="search-popup-nav-link" onclick="closeSearchPopup()">
                    <iconify-icon icon="mdi:help-circle-outline"></iconify-icon>
                    FAQ
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    /* ============================================
       SEARCH POPUP OVERLAY
       ============================================ */
    .search-popup-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        z-index: 10000;
        display: none;
        justify-content: center;
        align-items: center;
        animation: searchFadeIn 0.3s ease;
    }

    .search-popup-overlay.active {
        display: flex;
    }

    @keyframes searchFadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    @keyframes searchFadeOut {
        from {
            opacity: 1;
            transform: scale(1);
        }
        to {
            opacity: 0;
            transform: scale(0.95);
        }
    }

    .search-popup-overlay.fade-out {
        animation: searchFadeOut 0.3s ease forwards;
    }

    /* ============================================
       SEARCH POPUP CONTAINER
       ============================================ */
    .search-popup-container {
        background: #ffffff;
        border-radius: 1.2vw;
        max-width: 50vw;
        width: 100%;
        max-height: 80vh;
        overflow-y: auto;
        padding: 2.5vw 3vw;
        position: relative;
        box-shadow: 0 1vw 4vw rgba(0, 0, 0, 0.2);
    }

    .search-popup-container::-webkit-scrollbar {
        width: 0.3vw;
    }

    .search-popup-container::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 0.3vw;
    }

    .search-popup-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 0.3vw;
    }

    /* ============================================
       CLOSE BUTTON
       ============================================ */
    .search-popup-close {
        position: absolute;
        top: 1vw;
        right: 1.5vw;
        background: #f1f5f9;
        border: none;
        border-radius: 50%;
        width: 2.5vw;
        height: 2.5vw;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 1.5vw;
        color: #475569;
    }

    .search-popup-close:hover {
        background: #e2e8f0;
        transform: rotate(90deg);
    }

    /* ============================================
       LOGO
       ============================================ */
    .search-popup-logo {
        text-align: center;
        margin-bottom: 1.5vw;
    }

    .search-popup-logo img {
        height: 3vw;
        max-height: 50px;
        transition: transform 0.3s ease;
    }

    .search-popup-logo a:hover img {
        transform: scale(1.05);
    }

    /* ============================================
       SEARCH INPUT
       ============================================ */
    .search-popup-input-wrapper {
        margin-bottom: 2vw;
    }

    .search-popup-input-wrapper form {
        display: flex;
        align-items: center;
        background: #f1f5f9;
        border-radius: 0.8vw;
        padding: 0 0.5vw;
        border: 0.15vw solid transparent;
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .search-popup-input-wrapper form:focus-within {
        border-color: #076694;
        background: #ffffff;
        box-shadow: 0 0 0 0.3vw rgba(7, 102, 148, 0.1);
    }

    .search-popup-icon {
        font-size: 1.5vw;
        color: #94a3b8;
        padding: 0 0.8vw;
        flex-shrink: 0;
    }

    .search-popup-input {
        flex: 1;
        border: none;
        background: transparent;
        padding: 0.8vw 0.5vw;
        font-size: 1vw;
        color: #0f172a;
        outline: none;
        font-family: inherit;
        min-width: 0;
    }

    .search-popup-input::placeholder {
        color: #94a3b8;
    }

    .search-popup-submit {
        background: #076694;
        color: #ffffff;
        border: none;
        padding: 0.6vw 1.8vw;
        border-radius: 0.6vw;
        font-size: 0.8vw;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        white-space: nowrap;
        text-transform: uppercase;
        letter-spacing: 0.05vw;
        flex-shrink: 0;
    }

    .search-popup-submit:hover {
        background: #055a7a;
    }

    .search-popup-submit:active {
        transform: scale(0.95);
    }

    /* ============================================
       TRENDING SEARCH
       ============================================ */
    .search-popup-trending {
        margin-bottom: 2vw;
    }

    .search-popup-trending-header {
        font-size: 0.85vw;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 0.8vw;
        padding-bottom: 0.5vw;
        border-bottom: 0.1vw solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 0.5vw;
    }

    .search-popup-trending-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.6vw;
    }

    .search-popup-trending-item {
        display: flex;
        align-items: center;
        gap: 0.3vw;
        padding: 0.4vw 1vw;
        background: #f1f5f9;
        border-radius: 100vw;
        font-size: 0.75vw;
        color: #475569;
        text-decoration: none;
        transition: all 0.3s ease;
        cursor: pointer;
        border: none;
    }

    .search-popup-trending-item:hover {
        background: #076694;
        color: #ffffff;
        transform: translateY(-0.1vw);
        box-shadow: 0 0.2vw 0.8vw rgba(7, 102, 148, 0.2);
    }

    .search-popup-trending-item:active {
        transform: scale(0.95);
    }

    .search-popup-trending-item .trending-icon {
        font-size: 0.8vw;
        color: #ef4444;
    }

    .search-popup-trending-item:hover .trending-icon {
        color: #ffffff;
    }

    /* ============================================
       NAVIGATION LINKS
       ============================================ */
    .search-popup-nav {
        display: flex;
        justify-content: center;
        gap: 2vw;
        padding-top: 1.5vw;
        border-top: 0.1vw solid #e2e8f0;
    }

    .search-popup-nav-link {
        display: flex;
        align-items: center;
        gap: 0.5vw;
        font-size: 0.8vw;
        color: #475569;
        text-decoration: none;
        transition: all 0.3s ease;
        padding: 0.3vw 0.8vw;
        border-radius: 0.4vw;
    }

    .search-popup-nav-link:hover {
        color: #076694;
        background: #f0f9ff;
        transform: translateY(-0.1vw);
    }

    .search-popup-nav-link:active {
        transform: scale(0.95);
    }

    .search-popup-nav-link iconify-icon {
        font-size: 1.2vw;
    }

    /* ============================================
       🔥 RESPONSIVE MOBILE (max-width: 768px)
       ============================================ */
    @media (max-width: 768px) {
        .search-popup-overlay {
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            align-items: flex-end;
            padding: 0;
        }

        .search-popup-overlay.active {
            display: flex;
        }

        .search-popup-container {
            max-width: 100%;
            max-height: 90vh;
            padding: 6vw 5vw 8vw;
            border-radius: 4vw 4vw 0 0;
            box-shadow: 0 -1vw 4vw rgba(0, 0, 0, 0.15);
            animation: searchSlideUp 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
            overflow-y: auto;
        }

        @keyframes searchSlideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Handle / Indicator */
        .search-popup-container::before {
            content: '';
            display: block;
            width: 15vw;
            height: 0.8vw;
            background: #d1d5db;
            border-radius: 1vw;
            margin: -1vw auto 3vw;
            flex-shrink: 0;
        }

        .search-popup-close {
            width: 7vw;
            height: 7vw;
            font-size: 4vw;
            top: 2.5vw;
            right: 3.5vw;
            background: #f1f5f9;
            box-shadow: 0 0.2vw 0.8vw rgba(0, 0, 0, 0.06);
        }

        .search-popup-close:active {
            transform: scale(0.9);
        }

        .search-popup-logo {
            margin-bottom: 3vw;
        }

        .search-popup-logo img {
            height: 7vw;
            max-height: 50px;
        }

        /* Search Input - Mobile */
        .search-popup-input-wrapper {
            margin-bottom: 4vw;
        }

        .search-popup-input-wrapper form {
            border-radius: 2.5vw;
            padding: 0 1.5vw;
            border: 0.2vw solid transparent;
            background: #f1f5f9;
        }

        .search-popup-input-wrapper form:focus-within {
            border-color: #076694;
            background: #ffffff;
            box-shadow: 0 0 0 0.4vw rgba(7, 102, 148, 0.08);
        }

        .search-popup-icon {
            font-size: 4vw;
            padding: 0 1.5vw;
            color: #94a3b8;
        }

        .search-popup-input {
            font-size: 3.2vw;
            padding: 2.5vw 1.5vw;
            color: #0f172a;
        }

        .search-popup-input::placeholder {
            font-size: 3vw;
        }

        .search-popup-submit {
            padding: 2vw 5vw;
            font-size: 2.5vw;
            border-radius: 2vw;
            background: linear-gradient(135deg, #076694 0%, #0a8ab8 100%);
            box-shadow: 0 0.3vw 1.2vw rgba(7, 102, 148, 0.25);
            letter-spacing: 0.1vw;
        }

        .search-popup-submit:active {
            transform: scale(0.95);
            box-shadow: 0 0.2vw 0.8vw rgba(7, 102, 148, 0.15);
        }

        /* Trending - Mobile */
        .search-popup-trending {
            margin-bottom: 3vw;
        }

        .search-popup-trending-header {
            font-size: 3vw;
            margin-bottom: 1.5vw;
            padding-bottom: 1vw;
            border-bottom: 0.15vw solid #e8edf4;
        }

        .search-popup-trending-list {
            gap: 1.5vw;
        }

        .search-popup-trending-item {
            padding: 1.5vw 3.5vw;
            font-size: 2.6vw;
            border-radius: 100vw;
            gap: 0.8vw;
            background: #f1f5f9;
            border: 0.1vw solid #e8edf4;
        }

        .search-popup-trending-item:active {
            transform: scale(0.95);
            background: #076694;
            color: #ffffff;
        }

        .search-popup-trending-item .trending-icon {
            font-size: 2.6vw;
            color: #ef4444;
        }

        .search-popup-trending-item:active .trending-icon {
            color: #ffffff;
        }

        /* Navigation - Mobile */
        .search-popup-nav {
            gap: 3vw;
            padding-top: 3vw;
            border-top: 0.15vw solid #e8edf4;
            flex-wrap: wrap;
            justify-content: center;
        }

        .search-popup-nav-link {
            font-size: 2.6vw;
            gap: 1.2vw;
            padding: 1vw 2.5vw;
            border-radius: 1.5vw;
            background: #f8fafc;
            border: 0.1vw solid #e8edf4;
        }

        .search-popup-nav-link:active {
            transform: scale(0.95);
            background: #f0f9ff;
            color: #076694;
        }

        .search-popup-nav-link iconify-icon {
            font-size: 3.5vw;
        }

        /* Scrollbar Mobile */
        .search-popup-container::-webkit-scrollbar {
            width: 0.5vw;
        }

        .search-popup-container::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .search-popup-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 0.5vw;
        }
    }

    /* ============================================
       RESPONSIVE - EXTRA SMALL (≤ 480px)
       ============================================ */
    @media (max-width: 480px) {
        .search-popup-container {
            padding: 7vw 4vw 10vw;
            border-radius: 5vw 5vw 0 0;
        }

        .search-popup-container::before {
            width: 20vw;
            height: 1vw;
            margin: -1.5vw auto 4vw;
        }

        .search-popup-close {
            width: 9vw;
            height: 9vw;
            font-size: 5vw;
            top: 2vw;
            right: 2.5vw;
        }

        .search-popup-logo img {
            height: 10vw;
        }

        .search-popup-input-wrapper form {
            border-radius: 3vw;
            padding: 0 2vw;
        }

        .search-popup-icon {
            font-size: 5vw;
            padding: 0 2vw;
        }

        .search-popup-input {
            font-size: 3.8vw;
            padding: 3vw 2vw;
        }

        .search-popup-input::placeholder {
            font-size: 3.5vw;
        }

        .search-popup-submit {
            padding: 2.5vw 6vw;
            font-size: 3vw;
            border-radius: 2.5vw;
        }

        .search-popup-trending-header {
            font-size: 3.5vw;
        }

        .search-popup-trending-item {
            padding: 2vw 4vw;
            font-size: 3vw;
            border-radius: 100vw;
        }

        .search-popup-trending-item .trending-icon {
            font-size: 3vw;
        }

        .search-popup-nav {
            gap: 4vw;
        }

        .search-popup-nav-link {
            font-size: 3vw;
            padding: 1.5vw 3vw;
            border-radius: 2vw;
        }

        .search-popup-nav-link iconify-icon {
            font-size: 4.5vw;
        }
    }
</style>