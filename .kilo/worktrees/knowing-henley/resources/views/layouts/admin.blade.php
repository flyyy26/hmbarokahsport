<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', 'Admin E-Commerce')
    </title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.iconify.design/iconify-icon/3.0.0/iconify-icon.min.js"></script>

    <script src="https://cdn.tiny.cloud/1/f0qff2j87jgv24lrb8m0hd4yuglweewk56pa79tykafgtc6g/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Hanken+Grotesk:ital,wght@0,100..900;1,100..900&display=swap');
        *{
            font-family: "Hanken Grotesk", sans-serif;
        }
        .ql-editor {
            min-height: 250px;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .ql-toolbar.ql-snow {
            border-radius: 8px 8px 0 0;
            border-color: #d1d5db !important;
            background: #f9fafb;
        }
        
        .ql-container.ql-snow {
            border-radius: 0 0 8px 8px;
            border-color: #d1d5db !important;
            background: white;
        }
        
        .ql-container.ql-snow:focus-within {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        /* Dark mode support */
        .dark .ql-toolbar.ql-snow {
            background: #1f2937;
            border-color: #374151 !important;
        }
        
        .dark .ql-container.ql-snow {
            background: #1f2937;
            border-color: #374151 !important;
        }
        
        .dark .ql-editor {
            color: #e5e7eb;
        }
        
        .dark .ql-editor.ql-blank::before {
            color: #6b7280;
        }
        #quill-editor-terms .ql-editor {
            min-height: 180px !important;
            max-height: 400px !important;
            font-size: 14px;
            line-height: 1.8;
            background: #ffffff;
        }

        #quill-editor-terms .ql-toolbar.ql-snow {
            border-radius: 8px 8px 0 0;
            border-color: #d1d5db !important;
            background: #f9fafb;
        }

        #quill-editor-terms .ql-container.ql-snow {
            border-radius: 0 0 8px 8px;
            border-color: #d1d5db !important;
            background: white;
            min-height: 180px;
        }

        #quill-editor-terms .ql-container.ql-snow:focus-within {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Dark mode support */
        .dark #quill-editor-terms .ql-toolbar.ql-snow {
            background: #1f2937;
            border-color: #374151 !important;
        }

        .dark #quill-editor-terms .ql-container.ql-snow {
            background: #1f2937;
            border-color: #374151 !important;
        }

        .dark #quill-editor-terms .ql-editor {
            color: #e5e7eb;
        }

        .dark #quill-editor-terms .ql-editor.ql-blank::before {
            color: #6b7280;
        }
    </style>
</head>


<body class="bg-gray-100 text-gray-900">


    <div class="min-h-screen">


        {{-- Mobile Overlay --}}
        <div
            id="sidebarOverlay"
            class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden"
        ></div>


        {{-- Sidebar --}}
        <aside
            id="sidebar"
            class="fixed inset-y-0 left-0 z-50
                   w-64 bg-white border-r border-gray-200
                   transform -translate-x-full
                   lg:translate-x-0
                   transition-transform duration-300"
        >

            {{-- Logo --}}
            <div
                class="h-16 flex items-center px-6
                       border-b border-gray-200"
            >

                <a
                    href="{{ route('admin.dashboard') }}"
                    class="text-xl font-bold text-blue-600"
                >
                    E-Commerce
                </a>

            </div>


            {{-- Navigation --}}
            <nav class="p-4 space-y-1">


                {{-- Dashboard --}}
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 px-4 py-3
                           rounded-lg text-sm font-medium
                           text-gray-700 hover:bg-blue-50
                           hover:text-blue-600"
                >

                    <span>📊</span>

                    <span>
                        Dashboard
                    </span>

                </a>


                {{-- Kategori --}}
                <a
                    href="{{ route('admin.categories.index') }}"
                    class="flex items-center gap-3 px-4 py-3
                        rounded-lg text-sm font-medium
                        text-gray-700 hover:bg-blue-50
                        hover:text-blue-600"
                >

                    <span>📁</span>

                    <span>
                        Kategori
                    </span>

                </a>

                <a href="{{ route('admin.vouchers.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('admin.vouchers.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600' }}">
                    <span>🎟️</span>
                    <span>Voucher Promo</span>
                </a>


                {{-- Produk --}}
                <a
                    href="{{ route('admin.products.index') }}"
                    class="flex items-center gap-3 px-4 py-3
                        rounded-lg text-sm font-medium
                        text-gray-700 hover:bg-blue-50
                        hover:text-blue-600"
                >
                    <span>📦</span>

                    <span>
                        Produk
                    </span>
                </a>

                <a href="{{ route('admin.stock.index') }}"
                    class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.stock.*') ? 'bg-gray-200 text-gray-900' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Manajemen Stok
                    <span class="ml-auto">
                        <span class="inline-flex rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">
                            {{ \App\Models\Product::where('is_active', true)->criticalStock()->count() }}
                        </span>
                    </span>
                </a>

                <a
                    href="{{ route('admin.articles.index') }}"
                    class="flex items-center gap-3 px-4 py-3
                        rounded-lg text-sm font-medium
                        text-gray-700 hover:bg-blue-50
                        hover:text-blue-600"
                >

                    <span>
                        Artikel
                    </span>
                </a>

                <a
                    href="{{ route('admin.faqs.index') }}"
                    class="flex items-center gap-3 px-4 py-3
                        rounded-lg text-sm font-medium
                        text-gray-700 hover:bg-blue-50
                        hover:text-blue-600"
                >

                    <span>
                        FAQ
                    </span>
                </a>
                <a
                    href="{{ route('admin.terms.index') }}"
                    class="flex items-center gap-3 px-4 py-3
                        rounded-lg text-sm font-medium
                        text-gray-700 hover:bg-blue-50
                        hover:text-blue-600"
                >

                    <span>
                        Syarat & Ketentuan
                    </span>
                </a>
                <a
                    href="{{ route('admin.privacy.index') }}"
                    class="flex items-center gap-3 px-4 py-3
                        rounded-lg text-sm font-medium
                        text-gray-700 hover:bg-blue-50
                        hover:text-blue-600"
                >

                    <span>
                        Kebijakan Privasi
                    </span>
                </a>

                <a href="{{ route('admin.about.index') }}"
                    class="flex items-center gap-3 px-4 py-3
                        rounded-lg text-sm font-medium
                        text-gray-700 hover:bg-blue-50
                        hover:text-blue-600">
                    <span>🏢</span>
                    <span>Tentang Kami</span>
                </a>

                <a
                    href="{{ route('admin.banners.index') }}"
                    class="flex items-center gap-3 px-4 py-3
                        rounded-lg text-sm font-medium
                        text-gray-700 hover:bg-blue-50
                        hover:text-blue-600"
                >

                    <span>
                        Banner
                    </span>
                </a>

                <a
                    href="{{ route('admin.settings.edit') }}"
                    class="flex items-center gap-3 px-4 py-3
                        rounded-lg text-sm font-medium
                        text-gray-700 hover:bg-blue-50
                        hover:text-blue-600"
                >

                    <span>
                        Pengaturan Toko
                    </span>
                </a>

                <a
                    href="{{ route('admin.marketplaces.index') }}"
                    class="flex items-center gap-3 px-4 py-3
                        rounded-lg text-sm font-medium
                        text-gray-700 hover:bg-blue-50
                        hover:text-blue-600"
                >

                    <span>
                        Marketplace
                    </span>
                </a>


                {{-- Pesanan --}}
                <a
                    href="{{ route('admin.orders.index') }}"
                    class="flex items-center gap-3 px-4 py-3
                           rounded-lg text-sm font-medium
                           text-gray-700 hover:bg-blue-50
                           hover:text-blue-600"
                >

                    <span>🛒</span>

                    <span>
                        Pesanan
                    </span>

                </a>


                {{-- Pelanggan --}}
                <a
                    href="#"
                    class="flex items-center gap-3 px-4 py-3
                           rounded-lg text-sm font-medium
                           text-gray-700 hover:bg-blue-50
                           hover:text-blue-600"
                >

                    <span>👤</span>

                    <span>
                        Pelanggan
                    </span>

                </a>


            </nav>


            {{-- Bottom Sidebar --}}
            <div
                class="absolute bottom-0 left-0 right-0
                       p-4 border-t border-gray-200"
            >

                <div class="mb-3 px-4">

                    <p class="text-xs text-gray-500">
                        Login sebagai
                    </p>

                    <p class="text-sm font-semibold">
                        {{ auth()->user()->name }}
                    </p>

                </div>


                <form
                    action="{{ route('logout') }}"
                    method="POST"
                >

                    @csrf

                    <button
                        type="submit"
                        class="w-full flex items-center gap-3
                               px-4 py-3 rounded-lg
                               text-sm font-medium
                               text-red-600 hover:bg-red-50"
                    >

                        <span>🚪</span>

                        <span>
                            Logout
                        </span>

                    </button>

                </form>

            </div>

        </aside>


        {{-- Main Content --}}
        <div class="lg:ml-64">


            {{-- Header --}}
            <header
                class="h-16 bg-white border-b border-gray-200
                       flex items-center justify-between
                       px-4 lg:px-8"
            >

                {{-- Mobile Menu --}}
                <button
                    id="menuButton"
                    type="button"
                    class="lg:hidden p-2 rounded-lg
                           hover:bg-gray-100"
                >

                    ☰

                </button>


                {{-- Page Title --}}
                <h1 class="text-lg font-semibold">

                    @yield('page-title', 'Dashboard')

                </h1>


                {{-- User --}}
                <div class="flex items-center gap-3">

                    <div
                        class="w-9 h-9 rounded-full
                               bg-blue-100 text-blue-600
                               flex items-center justify-center
                               font-bold"
                    >

                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}

                    </div>

                </div>

            </header>


            {{-- Page Content --}}
            <main class="p-4 lg:p-8">

                @yield('content')

            </main>


        </div>


    </div>

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ============================================
            // 🔥 QUILL.JS - SYARAT & KETENTUAN
            // ============================================
            
            const hiddenInput = document.getElementById('terms_and_conditions');
            const editorContainer = document.getElementById('quill-editor-terms');
            const form = document.querySelector('form');
            
            let quill = null;
            
            if (editorContainer && typeof Quill !== 'undefined') {
                // 🔥 Toolbar configuration
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
                
                quill = new Quill(editorContainer, {
                    theme: 'snow',
                    placeholder: '1. Berlaku untuk seluruh produk.\n2. Tidak dapat digabung dengan promo lain.\n3. Dll...',
                    modules: {
                        toolbar: toolbarOptions
                    }
                });
                
                // 🔥 Set initial content
                const initialContent = hiddenInput.value;
                if (initialContent) {
                    quill.root.innerHTML = initialContent;
                }
                
                // 🔥 Sync to hidden input on change
                quill.on('text-change', function() {
                    hiddenInput.value = quill.root.innerHTML;
                });
                
                console.log('✅ Quill.js initialized for Terms & Conditions');
            } else {
                console.error('❌ Quill.js not loaded');
            }
            
            // ============================================
            // 🔥 SUBMIT FORM - SYNC CONTENT
            // ============================================
            
            if (form) {
                form.addEventListener('submit', function(e) {
                    // 🔥 SYNC QUILL CONTENT TO HIDDEN INPUT
                    if (quill) {
                        hiddenInput.value = quill.root.innerHTML;
                        console.log('📝 Quill content saved before submit');
                    }
                });
            }
            
            // ============================================
            // 🔥 DISCOUNT TYPE HANDLER
            // ============================================
            
            var discountType = document.getElementById('discount_type');
            var maxWrapper = document.getElementById('max_discount_wrapper');
            var unitLabel = document.getElementById('discount_unit_label');
            var valInput = document.getElementById('discount_value');
            
            if (discountType) {
                function updateFields() {
                    if (discountType.value === 'percentage') {
                        maxWrapper?.classList.remove('hidden');
                        unitLabel.textContent = '(%)';
                        valInput.placeholder = 'Contoh: 20';
                    } else {
                        maxWrapper?.classList.add('hidden');
                        unitLabel.textContent = '(Rp)';
                        valInput.placeholder = 'Contoh: 100000';
                    }
                }
                
                discountType.addEventListener('change', updateFields);
                updateFields();
            }
        });
    </script>


    {{-- Vanilla JavaScript --}}
    <script>

        const sidebar = document.getElementById('sidebar');

        const overlay = document.getElementById('sidebarOverlay');

        const menuButton = document.getElementById('menuButton');


        function openSidebar() {

            sidebar.classList.remove('-translate-x-full');

            overlay.classList.remove('hidden');

        }


        function closeSidebar() {

            sidebar.classList.add('-translate-x-full');

            overlay.classList.add('hidden');

        }


        menuButton.addEventListener(
            'click',
            openSidebar
        );


        overlay.addEventListener(
            'click',
            closeSidebar
        );

    </script>


</body>

</html>