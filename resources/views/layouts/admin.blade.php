<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', 'Admin E-Commerce')
    </title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.iconify.design/iconify-icon/3.0.0/iconify-icon.min.js"></script>

    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        gold: {
                            50:  '#fffbeb',
                            100: '#fef3c7',
                            200: '#fde68a',
                            300: '#fcd34d',
                            400: '#fbbf24',
                            500: '#ecbc42',
                            600: '#d4a72e',
                            700: '#a17319',
                            800: '#78551a',
                            900: '#63471c',
                        },
                    },
                },
            },
        };
    </script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Hanken+Grotesk:ital,wght@0,100..900;1,100..900&display=swap');
        *{
            font-family: "Hanken Grotesk", sans-serif;
        }

        /* ============================================
           CSS VARIABLES (THEME TOKENS)
           ============================================ */
        :root {
            --bg-body: #0a0a0a;
            --bg-card: #141414;
            --bg-hover: #1a1a1a;
            --bg-elevated: #1e1e1e;
            --bg-input: #0f0f0f;
            --border-1: #1e1e1e;
            --border-2: #262626;
            --border-3: #333333;
            --text-1: #f1f5f9;
            --text-2: #e2e8f0;
            --text-3: #cbd5e1;
            --text-4: #94a3b8;
            --text-5: #64748b;
            --text-6: #525252;
            --gold: #ecbc42;
            --gold-bright: #FDDD57;
            --gold-dark: #a17319;
            --gold-text: #422006;
        }

        body.light-mode {
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --bg-hover: #f1f5f9;
            --bg-elevated: #f1f5f9;
            --bg-input: #ffffff;
            --border-1: #f1f5f9;
            --border-2: #e2e8f0;
            --border-3: #cbd5e1;
            --text-1: #0f172a;
            --text-2: #1e293b;
            --text-3: #334155;
            --text-4: #475569;
            --text-5: #64748b;
            --text-6: #94a3b8;
        }

        /* ============================================
           BODY & BASE
           ============================================ */
        body {
            background: var(--bg-body);
            color: var(--text-2);
            transition: background 0.3s ease, color 0.3s ease;
        }

        /* ============================================
           SCROLLBAR CUSTOM
           ============================================ */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: var(--bg-elevated);
        }
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, var(--gold) 0%, var(--gold-dark) 100%);
            border-radius: 100px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, var(--gold-bright) 0%, var(--gold) 100%);
        }

        .admin-sidebar-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .admin-sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .admin-sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(236, 188, 66, 0.3);
            border-radius: 100px;
        }
        .admin-sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(236, 188, 66, 0.5);
        }
        .admin-sidebar-scroll {
            scrollbar-width: thin;
            scrollbar-color: rgba(236, 188, 66, 0.3) transparent;
        }

        /* ============================================
           SIDEBAR LINK STATE
           ============================================ */
        .sidebar-link {
            transition: all 0.2s ease;
            color: var(--text-4);
        }
        .sidebar-link:hover {
            background: rgba(236, 188, 66, 0.08);
            color: var(--gold-bright);
        }
        .sidebar-link:hover .sidebar-icon {
            color: var(--gold-bright);
            transform: scale(1.1);
        }
        .sidebar-link.active {
            background: linear-gradient(90deg, rgba(236, 188, 66, 0.15) 0%, rgba(236, 188, 66, 0.03) 100%);
            color: var(--gold-bright);
            position: relative;
        }
        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 20%;
            height: 60%;
            width: 3px;
            background: linear-gradient(180deg, var(--gold-bright) 0%, var(--gold) 100%);
            border-radius: 0 100px 100px 0;
            box-shadow: 0 0 10px rgba(236, 188, 66, 0.6);
        }
        .sidebar-link.active .sidebar-icon {
            color: var(--gold-bright);
        }

        .sidebar-icon {
            transition: all 0.2s ease;
            color: var(--text-5);
        }

        body.light-mode .sidebar-link {
            color: var(--text-4);
        }
        body.light-mode .sidebar-link:hover {
            background: rgba(236, 188, 66, 0.1);
            color: var(--gold-dark);
        }
        body.light-mode .sidebar-link.active {
            background: linear-gradient(90deg, rgba(236, 188, 66, 0.15) 0%, rgba(236, 188, 66, 0.02) 100%);
            color: var(--gold-dark);
        }
        body.light-mode .sidebar-link.active::before {
            background: linear-gradient(180deg, var(--gold) 0%, var(--gold-dark) 100%);
        }
        body.light-mode .sidebar-link.active .sidebar-icon {
            color: var(--gold-dark);
        }

        /* ============================================
           FORM ELEMENTS
           ============================================ */
        input[type="text"]:not([class*="border-transparent"]),
        input[type="email"],
        input[type="password"],
        input[type="number"],
        input[type="tel"],
        input[type="url"],
        input[type="date"],
        input[type="datetime-local"],
        input[type="time"],
        input[type="search"],
        textarea,
        select {
            background-color: var(--bg-input) !important;
            border-color: var(--border-2) !important;
            color: var(--text-1) !important;
        }

        input::placeholder,
        textarea::placeholder {
            color: var(--text-6) !important;
        }

        input:focus,
        textarea:focus,
        select:focus {
            border-color: var(--gold) !important;
            box-shadow: 0 0 0 3px rgba(236, 188, 66, 0.15) !important;
            outline: none !important;
        }

        /* Select arrow */
        select {
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23ecbc42' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right 0.75rem center !important;
            background-size: 1.1em !important;
            padding-right: 2.5rem !important;
            appearance: none !important;
            -webkit-appearance: none !important;
        }

        /* Checkbox & Radio */
        input[type="checkbox"],
        input[type="radio"] {
            accent-color: var(--gold);
        }

        /* ============================================
           TABLES
           ============================================ */
        table {
            color: var(--text-2);
        }

        table thead {
            background: var(--bg-input) !important;
            border-bottom: 1px solid var(--border-2) !important;
        }

        table thead th {
            color: var(--text-4) !important;
            font-weight: 600 !important;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.05em;
        }

        table tbody tr {
            border-bottom: 1px solid var(--border-1) !important;
            transition: background 0.15s ease;
        }

        table tbody tr:hover {
            background: var(--bg-hover) !important;
        }

        table tbody td {
            color: var(--text-3);
        }

        /* ============================================
           CARDS & PANELS
           ============================================ */
        .card,
        .panel {
            background: var(--bg-card);
            border: 1px solid var(--border-2);
            border-radius: 12px;
        }

        /* ============================================
           BUTTONS
           ============================================ */
        .btn-gold {
            background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
            color: var(--gold-text);
            font-weight: 700;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(236, 188, 66, 0.25);
        }
        .btn-gold:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(236, 188, 66, 0.4);
        }

        /* ============================================
           BADGE
           ============================================ */
        .badge-gold {
            background: rgba(236, 188, 66, 0.15);
            color: var(--gold-bright);
            border: 1px solid rgba(236, 188, 66, 0.3);
        }

        /* ============================================
           QUILL EDITOR
           ============================================ */
        .ql-toolbar.ql-snow {
            border-radius: 8px 8px 0 0;
            border-color: var(--border-2) !important;
            background: var(--bg-input) !important;
        }

        .ql-toolbar.ql-snow .ql-stroke { stroke: var(--text-4); }
        .ql-toolbar.ql-snow .ql-fill   { fill: var(--text-4); }
        .ql-toolbar.ql-snow .ql-picker { color: var(--text-4); }

        .ql-toolbar.ql-snow button:hover,
        .ql-toolbar.ql-snow button.ql-active,
        .ql-toolbar.ql-snow .ql-picker-label:hover,
        .ql-toolbar.ql-snow .ql-picker-item:hover {
            color: var(--gold-bright) !important;
        }

        .ql-toolbar.ql-snow button:hover .ql-stroke,
        .ql-toolbar.ql-snow button.ql-active .ql-stroke {
            stroke: var(--gold-bright) !important;
        }

        .ql-toolbar.ql-snow button:hover .ql-fill,
        .ql-toolbar.ql-snow button.ql-active .ql-fill {
            fill: var(--gold-bright) !important;
        }

        .ql-container.ql-snow {
            border-radius: 0 0 8px 8px;
            border-color: var(--border-2) !important;
            background: var(--bg-input) !important;
        }

        .ql-container.ql-snow:focus-within {
            border-color: var(--gold) !important;
            box-shadow: 0 0 0 3px rgba(236, 188, 66, 0.15);
        }

        .ql-editor {
            color: var(--text-2);
            min-height: 250px;
            font-size: 14px;
            line-height: 1.6;
        }

        .ql-editor.ql-blank::before {
            color: var(--text-6);
            font-style: normal;
        }

        #quill-editor-terms .ql-editor {
            min-height: 180px !important;
            max-height: 400px !important;
        }

        #quill-editor .ql-editor {
            min-height: 300px !important;
            max-height: 500px !important;
        }

        /* ============================================
           ALERTS
           ============================================ */
        .alert-success {
            background: rgba(16, 185, 129, 0.1) !important;
            border-color: rgba(16, 185, 129, 0.3) !important;
            color: #6ee7b7 !important;
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.1) !important;
            border-color: rgba(239, 68, 68, 0.3) !important;
            color: #fca5a5 !important;
        }
        .alert-warning {
            background: rgba(236, 188, 66, 0.1) !important;
            border-color: rgba(236, 188, 66, 0.3) !important;
            color: var(--gold-bright) !important;
        }
        .alert-info {
            background: rgba(59, 130, 246, 0.1) !important;
            border-color: rgba(59, 130, 246, 0.3) !important;
            color: #93c5fd !important;
        }

        body.light-mode .alert-success {
            color: #15803d !important;
        }
        body.light-mode .alert-error {
            color: #b91c1c !important;
        }
        body.light-mode .alert-warning {
            color: #a16207 !important;
        }
        body.light-mode .alert-info {
            color: #1d4ed8 !important;
        }

        /* ============================================
           MODAL
           ============================================ */
        .modal-overlay {
            background: rgba(0, 0, 0, 0.75) !important;
            backdrop-filter: blur(4px);
        }

        body.light-mode .modal-overlay {
            background: rgba(15, 23, 42, 0.5) !important;
        }

        /* ============================================
           PAGINATION
           ============================================ */
        nav[role="navigation"] a,
        nav[role="navigation"] span[aria-current="page"] span {
            background-color: var(--bg-card) !important;
            border-color: var(--border-2) !important;
            color: var(--text-4) !important;
        }

        nav[role="navigation"] a:hover {
            background-color: var(--bg-elevated) !important;
            color: var(--gold-bright) !important;
            border-color: var(--gold) !important;
        }

        nav[role="navigation"] span[aria-current="page"] span {
            background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 100%) !important;
            color: var(--gold-text) !important;
            border-color: transparent !important;
            font-weight: 700;
        }

        /* ============================================
           HOVER UTILS (THEME-AWARE)
           ============================================ */
        .hover\:bg-gray-50:hover   { background-color: var(--bg-hover) !important; }
        .hover\:bg-gray-100:hover  { background-color: var(--bg-elevated) !important; }
        .hover\:bg-slate-50:hover  { background-color: var(--bg-hover) !important; }
        .hover\:bg-slate-100:hover { background-color: var(--bg-elevated) !important; }
        .hover\:bg-blue-50:hover   { background-color: rgba(236, 188, 66, 0.08) !important; }

        /* ============================================
           GOLD GLOW
           ============================================ */
        .gold-glow {
            box-shadow: 0 0 20px rgba(236, 188, 66, 0.2);
        }

        /* ============================================
           ADMIN HEADER
           ============================================ */
        .admin-header {
            background: rgba(10, 10, 10, 0.85) !important;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-2) !important;
            transition: background 0.3s ease, border-color 0.3s ease;
        }

        body.light-mode .admin-header {
            background: rgba(255, 255, 255, 0.9) !important;
        }

        /* ============================================
           SIDEBAR
           ============================================ */
        .admin-sidebar {
            background: var(--bg-body);
            border-right: 1px solid var(--border-2);
            transition: background 0.3s ease, border-color 0.3s ease;
        }

        body.light-mode .admin-sidebar {
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
        }

        .admin-sidebar-logo {
            border-bottom: 1px solid var(--border-1);
        }

        .admin-sidebar-section {
            color: var(--text-5);
        }

        .admin-sidebar-user {
            background: var(--bg-card);
            border: 1px solid var(--border-2);
        }

        .admin-sidebar-divider {
            border-top: 1px solid var(--border-1);
        }

        /* ============================================
           SMOOTH THEME TRANSITION
           ============================================ */
        body, .admin-header, .admin-sidebar, .sidebar-link, .admin-sidebar-user,
        table, table thead, table tbody tr,
        input, textarea, select, .card, .panel {
            transition: background-color 0.3s ease,
                        border-color 0.3s ease,
                        color 0.3s ease;
        }

        /* Icon themes */
        #themeIcon-sun  { display: block; }
        #themeIcon-moon { display: none; }
        body.light-mode #themeIcon-sun  { display: none; }
        body.light-mode #themeIcon-moon { display: block; }
    </style>
</head>


<body class="text-slate-200 antialiased">

    <div class="min-h-screen">

        {{-- Mobile Overlay --}}
        <div
            id="sidebarOverlay"
            class="fixed inset-0 bg-black/70 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity"
        ></div>


        {{-- ============================================ --}}
        {{-- SIDEBAR --}}
        {{-- ============================================ --}}
        <aside
            id="sidebar"
            class="admin-sidebar fixed inset-y-0 left-0 z-50
                   w-64 flex flex-col
                   transform -translate-x-full
                   lg:translate-x-0
                   transition-transform duration-300"
        >

            {{-- Logo --}}
            <div class="admin-sidebar-logo h-16 flex items-center px-6 flex-shrink-0 relative">
                {{-- Gold accent bar --}}
                <div class="absolute bottom-0 left-0 right-0 h-[2px] bg-gradient-to-r from-transparent via-[#ecbc42] to-transparent"></div>

                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 group">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-[#FDDD57] to-[#ecbc42] flex items-center justify-center shadow-lg shadow-amber-500/20 group-hover:scale-105 transition-transform">
                        <iconify-icon icon="mdi:store" class="text-slate-900 text-xl"></iconify-icon>
                    </div>
                    <div class="flex flex-col leading-tight">
                        <span class="text-base font-bold tracking-tight" style="color: var(--text-1)">Barokah</span>
                        <span class="text-[10px] font-semibold text-[#ecbc42] uppercase tracking-widest">Sport Admin</span>
                    </div>
                </a>
            </div>


            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto admin-sidebar-scroll p-3 space-y-1">

                {{-- Dashboard --}}
                <a href="{{ route('admin.dashboard') }}"
                   class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium">
                    <iconify-icon icon="mdi:view-dashboard-outline" class="sidebar-icon text-lg"></iconify-icon>
                    <span>Dashboard</span>
                </a>


                {{-- SECTION: KATALOG --}}
                <div class="pt-4 pb-1 px-3">
                    <span class="admin-sidebar-section text-[10px] font-bold uppercase tracking-widest">Katalog</span>
                </div>

                <a href="{{ route('admin.categories.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium">
                    <iconify-icon icon="mdi:folder-outline" class="sidebar-icon text-lg"></iconify-icon>
                    <span>Kategori</span>
                </a>

                <a href="{{ route('admin.products.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium">
                    <iconify-icon icon="mdi:package-variant" class="sidebar-icon text-lg"></iconify-icon>
                    <span>Produk</span>
                </a>

                <a href="{{ route('admin.stock.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.stock.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium">
                    <iconify-icon icon="mdi:warehouse" class="sidebar-icon text-lg"></iconify-icon>
                    <span>Manajemen Stok</span>
                </a>


                {{-- SECTION: TRANSAKSI --}}
                <div class="pt-4 pb-1 px-3">
                    <span class="admin-sidebar-section text-[10px] font-bold uppercase tracking-widest">Transaksi</span>
                </div>

                <a href="{{ route('admin.orders.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium">
                    <iconify-icon icon="mdi:cart-outline" class="sidebar-icon text-lg"></iconify-icon>
                    <span>Pesanan</span>
                </a>

                <a href="{{ route('admin.returns.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.returns.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium">
                    <iconify-icon icon="mdi:backup-restore" class="sidebar-icon text-lg"></iconify-icon>
                    <span>Retur</span>
                </a>

                <a href="{{ route('admin.vouchers.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium">
                    <iconify-icon icon="mdi:ticket-percent-outline" class="sidebar-icon text-lg"></iconify-icon>
                    <span>Voucher Promo</span>
                </a>

                <a href="#"
                   class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium">
                    <iconify-icon icon="mdi:account-multiple-outline" class="sidebar-icon text-lg"></iconify-icon>
                    <span>Pelanggan</span>
                </a>

                <a href="{{ route('admin.testimonials.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.testimonials.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium">
                    <iconify-icon icon="mdi:star-outline" class="sidebar-icon text-lg"></iconify-icon>
                    <span>Testimonial</span>
                </a>


                {{-- SECTION: KONTEN --}}
                <div class="pt-4 pb-1 px-3">
                    <span class="admin-sidebar-section text-[10px] font-bold uppercase tracking-widest">Konten</span>
                </div>

                <a href="{{ route('admin.articles.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.articles.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium">
                    <iconify-icon icon="mdi:newspaper-variant-outline" class="sidebar-icon text-lg"></iconify-icon>
                    <span>Artikel</span>
                </a>

                <a href="{{ route('admin.faqs.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.faqs.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium">
                    <iconify-icon icon="mdi:help-circle-outline" class="sidebar-icon text-lg"></iconify-icon>
                    <span>FAQ</span>
                </a>

                <a href="{{ route('admin.terms.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.terms.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium">
                    <iconify-icon icon="mdi:file-document-outline" class="sidebar-icon text-lg"></iconify-icon>
                    <span>Syarat & Ketentuan</span>
                </a>

                <a href="{{ route('admin.privacy.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.privacy.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium">
                    <iconify-icon icon="mdi:shield-lock-outline" class="sidebar-icon text-lg"></iconify-icon>
                    <span>Kebijakan Privasi</span>
                </a>

                <a href="{{ route('admin.about.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.about.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium">
                    <iconify-icon icon="mdi:information-outline" class="sidebar-icon text-lg"></iconify-icon>
                    <span>Tentang Kami</span>
                </a>

                <a href="{{ route('admin.banners.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium">
                    <iconify-icon icon="mdi:image-multiple-outline" class="sidebar-icon text-lg"></iconify-icon>
                    <span>Banner</span>
                </a>


                {{-- SECTION: PENGATURAN --}}
                <div class="pt-4 pb-1 px-3">
                    <span class="admin-sidebar-section text-[10px] font-bold uppercase tracking-widest">Pengaturan</span>
                </div>

                <a href="{{ route('admin.marketplaces.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.marketplaces.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium">
                    <iconify-icon icon="mdi:shopping-outline" class="sidebar-icon text-lg"></iconify-icon>
                    <span>Marketplace</span>
                </a>

                <a href="{{ route('admin.settings.edit') }}"
                   class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium">
                    <iconify-icon icon="mdi:cog-outline" class="sidebar-icon text-lg"></iconify-icon>
                    <span>Pengaturan Toko</span>
                </a>

            </nav>


            {{-- Bottom Sidebar --}}
            <div class="admin-sidebar-divider p-3 flex-shrink-0">

                {{-- User Info --}}
                <div class="admin-sidebar-user flex items-center gap-3 px-3 py-2 mb-2 rounded-lg">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-[#FDDD57] to-[#ecbc42] flex items-center justify-center font-bold text-slate-900 flex-shrink-0 shadow-md shadow-amber-500/20">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs" style="color: var(--text-5)">Login sebagai</p>
                        <p class="text-sm font-semibold truncate" style="color: var(--text-1)">
                            {{ auth()->user()->name }}
                        </p>
                    </div>
                </div>

                {{-- Logout --}}
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button
                        type="submit"
                        class="w-full flex items-center justify-center gap-2
                               px-4 py-2.5 rounded-lg
                               text-sm font-semibold
                               text-red-400 bg-red-500/5
                               border border-red-500/20
                               hover:bg-red-500/10 hover:text-red-300
                               hover:border-red-500/40
                               transition-all duration-200
                               active:scale-95"
                    >
                        <iconify-icon icon="mdi:logout" class="text-lg"></iconify-icon>
                        <span>Logout</span>
                    </button>
                </form>

            </div>

        </aside>


        {{-- ============================================ --}}
        {{-- MAIN CONTENT --}}
        {{-- ============================================ --}}
        <div class="lg:ml-64">

            {{-- ============================================ --}}
            {{-- HEADER --}}
            {{-- ============================================ --}}
            <header class="admin-header h-16 flex items-center justify-between px-4 lg:px-8 sticky top-0 z-30">

                {{-- Left: Mobile Menu + Title --}}
                <div class="flex items-center gap-3 min-w-0">

                    {{-- Mobile Menu Button --}}
                    <button
                        id="menuButton"
                        type="button"
                        class="lg:hidden flex-shrink-0 p-2 rounded-lg
                               transition-all duration-200 active:scale-95"
                        style="color: var(--text-4)"
                        onmouseover="this.style.background='rgba(236,188,66,0.1)'; this.style.color='var(--gold-bright)'"
                        onmouseout="this.style.background='transparent'; this.style.color='var(--text-4)'"
                        aria-label="Toggle Menu"
                    >
                        <iconify-icon icon="mdi:menu" class="text-2xl"></iconify-icon>
                    </button>

                    {{-- Breadcrumb + Title --}}
                    <div class="min-w-0">
                        <div class="hidden sm:flex items-center gap-1.5 text-xs mb-0.5" style="color: var(--text-5)">
                            <a href="{{ route('admin.dashboard') }}"
                               class="hover:text-[#FDDD57] transition-colors flex items-center gap-1">
                                <iconify-icon icon="mdi:home-outline" class="text-sm"></iconify-icon>
                                <span>Dashboard</span>
                            </a>
                            @hasSection('page-title')
                                <iconify-icon icon="mdi:chevron-right" class="text-sm" style="color: var(--text-6)"></iconify-icon>
                                <span class="font-medium truncate" style="color: var(--text-3)">
                                    @yield('page-title')
                                </span>
                            @endif
                        </div>

                        <h1 class="text-base sm:text-lg font-bold truncate" style="color: var(--text-1)">
                            @yield('page-title', 'Dashboard')
                        </h1>

                        @hasSection('page-subtitle')
                            <p class="text-xs -mt-0.5 truncate hidden sm:block" style="color: var(--text-5)">
                                @yield('page-subtitle')
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Right: Actions + User --}}
                <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">

                    {{-- Quick Actions --}}
                    <div class="hidden md:flex items-center gap-1.5">

                        {{-- View Store --}}
                        <a href="{{ route('customer.home') }}"
                           target="_blank"
                           title="Lihat Toko"
                           class="flex items-center justify-center
                                  w-9 h-9 rounded-lg
                                  transition-all duration-200 active:scale-95"
                           style="color: var(--text-5)"
                           onmouseover="this.style.color='var(--gold-bright)'; this.style.background='rgba(236,188,66,0.1)'"
                           onmouseout="this.style.color='var(--text-5)'; this.style.background='transparent'">
                            <iconify-icon icon="mdi:storefront-outline" class="text-xl"></iconify-icon>
                        </a>

                        {{-- Notifications --}}
                        <button type="button"
                                title="Notifikasi"
                                class="relative flex items-center justify-center
                                       w-9 h-9 rounded-lg
                                       transition-all duration-200 active:scale-95"
                                style="color: var(--text-5)"
                                onmouseover="this.style.color='var(--gold-bright)'; this.style.background='rgba(236,188,66,0.1)'"
                                onmouseout="this.style.color='var(--text-5)'; this.style.background='transparent'">
                            <iconify-icon icon="mdi:bell-outline" class="text-xl"></iconify-icon>
                            <span class="absolute top-1.5 right-1.5
                                         w-2 h-2 rounded-full
                                         bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                                         animate-pulse
                                         shadow-md shadow-amber-500/50"></span>
                        </button>

                        {{-- Divider --}}
                        <div class="w-px h-6 mx-1" style="background: var(--border-2)"></div>

                        {{-- Theme Toggle --}}
                        <button type="button"
                                id="themeToggle"
                                title="Ganti Tema"
                                class="flex items-center justify-center
                                       w-9 h-9 rounded-lg
                                       transition-all duration-200 active:scale-95"
                                style="color: var(--text-4)"
                                onmouseover="this.style.color='var(--gold-bright)'; this.style.background='rgba(236,188,66,0.1)'"
                                onmouseout="this.style.color='var(--text-4)'; this.style.background='transparent'">
                            {{-- Sun (muncul di dark mode) --}}
                            <iconify-icon id="themeIcon-sun" icon="mdi:weather-sunny" class="text-xl"></iconify-icon>
                            {{-- Moon (muncul di light mode) --}}
                            <iconify-icon id="themeIcon-moon" icon="mdi:weather-night" class="text-xl"></iconify-icon>
                        </button>
                    </div>

                    {{-- User Profile --}}
                    <div class="flex items-center gap-2 sm:gap-3 pl-2 sm:pl-3">

                        <div class="hidden sm:block text-right">
                            <p class="text-sm font-semibold leading-tight truncate max-w-[140px]" style="color: var(--text-1)">
                                {{ auth()->user()->name }}
                            </p>
                            <div class="flex items-center justify-end gap-1 text-xs text-[#ecbc42] font-medium">
                                <iconify-icon icon="mdi:shield-check" class="text-xs"></iconify-icon>
                                <span>Administrator</span>
                            </div>
                        </div>

                        {{-- Avatar --}}
                        <div class="relative group cursor-pointer">
                            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full
                                        bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                                        flex items-center justify-center
                                        font-bold text-slate-900
                                        text-sm sm:text-base
                                        shadow-md shadow-amber-500/30
                                        transition-all duration-200
                                        group-hover:scale-105
                                        group-hover:shadow-lg
                                        group-hover:shadow-amber-500/50">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="absolute bottom-0 right-0
                                         w-3 h-3 rounded-full
                                         bg-emerald-500
                                         shadow-md shadow-emerald-500/50"
                                  style="box-shadow: 0 0 0 2px var(--bg-body)"></span>
                        </div>

                    </div>

                </div>

            </header>


            {{-- ============================================ --}}
            {{-- PAGE CONTENT --}}
            {{-- ============================================ --}}
            <main class="p-4 lg:p-8">
                @yield('content')
            </main>

        </div>

    </div>

    @stack('scripts')

    {{-- ============================================ --}}
    {{-- THEME TOGGLE SCRIPT --}}
    {{-- ============================================ --}}
    <script>
        (function() {
            // Load theme lebih awal (sebelum DOM ready) — mencegah flash
            var currentTheme = localStorage.getItem('admin-theme');
            if (currentTheme === 'light') {
                document.documentElement.classList.add('light-mode');
                document.addEventListener('DOMContentLoaded', function() {
                    document.body.classList.add('light-mode');
                });
            } else {
                document.addEventListener('DOMContentLoaded', function() {
                    document.body.classList.remove('light-mode');
                });
            }

            document.addEventListener('DOMContentLoaded', function() {
                var themeToggle = document.getElementById('themeToggle');
                if (!themeToggle) return;

                themeToggle.addEventListener('click', function() {
                    document.body.classList.toggle('light-mode');
                    var isLight = document.body.classList.contains('light-mode');
                    localStorage.setItem('admin-theme', isLight ? 'light' : 'dark');

                    // Sync html class juga
                    if (isLight) {
                        document.documentElement.classList.add('light-mode');
                    } else {
                        document.documentElement.classList.remove('light-mode');
                    }
                });
            });
        })();
    </script>

    {{-- ============================================ --}}
    {{-- QUILL EDITOR SCRIPT --}}
    {{-- ============================================ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            if (typeof Quill === 'undefined') {
                console.error('❌ Quill.js tidak terload!');
                return;
            }

            const toolbarOptions = [
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                [{ 'indent': '-1' }, { 'indent': '+1' }],
                [{ 'align': [] }],
                ['blockquote', 'code-block'],
                ['link'],
                ['clean']
            ];

            // Quill — Deskripsi Produk
            const descHiddenInput = document.getElementById('description');
            const descEditorContainer = document.getElementById('quill-editor');

            if (descEditorContainer && descHiddenInput) {
                try {
                    const quillDesc = new Quill(descEditorContainer, {
                        theme: 'snow',
                        placeholder: 'Deskripsi produk...',
                        modules: { toolbar: toolbarOptions }
                    });

                    const initialDescContent = descHiddenInput.value;
                    if (initialDescContent) {
                        quillDesc.root.innerHTML = initialDescContent;
                    }

                    quillDesc.on('text-change', function() {
                        descHiddenInput.value = quillDesc.root.innerHTML;
                    });

                    console.log('✅ Quill.js initialized for Product Description');
                } catch (e) {
                    console.error('❌ Error initializing product description Quill:', e);
                }
            }

            // Quill — Syarat & Ketentuan
            const termsHiddenInput = document.getElementById('terms_and_conditions');
            const termsEditorContainer = document.getElementById('quill-editor-terms');

            if (termsEditorContainer && termsHiddenInput) {
                try {
                    const quillTerms = new Quill(termsEditorContainer, {
                        theme: 'snow',
                        placeholder: '1. Berlaku untuk seluruh produk.\n2. Tidak dapat digabung dengan promo lain.\n3. Dll...',
                        modules: { toolbar: toolbarOptions }
                    });

                    const initialTermsContent = termsHiddenInput.value;
                    if (initialTermsContent) {
                        quillTerms.root.innerHTML = initialTermsContent;
                    }

                    quillTerms.on('text-change', function() {
                        termsHiddenInput.value = quillTerms.root.innerHTML;
                    });

                    console.log('✅ Quill.js initialized for Terms & Conditions');
                } catch (e) {
                    console.error('❌ Error initializing terms Quill:', e);
                }
            }

            // Submit Form - Sync Content
            const forms = document.querySelectorAll('form');
            forms.forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    const descInput = document.getElementById('description');
                    const descContainer = document.getElementById('quill-editor');
                    if (descInput && descContainer) {
                        const quillInstance = Quill.find(descContainer);
                        if (quillInstance) {
                            descInput.value = quillInstance.root.innerHTML;
                        }
                    }

                    const termsInput = document.getElementById('terms_and_conditions');
                    const termsContainer = document.getElementById('quill-editor-terms');
                    if (termsInput && termsContainer) {
                        const quillInstance = Quill.find(termsContainer);
                        if (quillInstance) {
                            termsInput.value = quillInstance.root.innerHTML;
                        }
                    }
                });
            });

            // Discount Type Handler
            const discountType = document.getElementById('discount_type');
            const maxWrapper = document.getElementById('max_discount_wrapper');
            const unitLabel = document.getElementById('discount_unit_label');
            const valInput = document.getElementById('discount_value');

            if (discountType) {
                function updateFields() {
                    if (discountType.value === 'percentage') {
                        if (maxWrapper) maxWrapper.classList.remove('hidden');
                        if (unitLabel) unitLabel.textContent = '(%)';
                        if (valInput) valInput.placeholder = 'Contoh: 20';
                    } else {
                        if (maxWrapper) maxWrapper.classList.add('hidden');
                        if (unitLabel) unitLabel.textContent = '(Rp)';
                        if (valInput) valInput.placeholder = 'Contoh: 100000';
                    }
                }

                discountType.addEventListener('change', updateFields);
                updateFields();
            }

        });
    </script>


    {{-- ============================================ --}}
    {{-- SIDEBAR TOGGLE SCRIPT --}}
    {{-- ============================================ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const menuButton = document.getElementById('menuButton');

            if (!sidebar || !overlay || !menuButton) return;

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            }

            menuButton.addEventListener('click', openSidebar);
            overlay.addEventListener('click', closeSidebar);

            document.querySelectorAll('#sidebar nav a').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 1024) {
                        closeSidebar();
                    }
                });
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !overlay.classList.contains('hidden')) {
                    closeSidebar();
                }
            });
        });
    </script>

</body>

</html>