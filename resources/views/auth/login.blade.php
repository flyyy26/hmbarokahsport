<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login Admin - Barokah Sport</title>
    <link rel="icon" src="{{ $setting?->favicon ? Storage::url($setting->favicon) : asset('images/favicon.png') }}" type="image/png">
    <link rel="shortcut icon" href="{{ $setting?->favicon ? Storage::url($setting->favicon) : asset('images/favicon.png') }}" type="image/x-icon">

    {{-- Script tema (sebelum render biar no flash) --}}
    <script>
        (function() {
            var theme = localStorage.getItem('admin-theme');
            if (theme === 'light') {
                document.documentElement.classList.add('light-mode');
            }
        })();
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.iconify.design/iconify-icon/3.0.0/iconify-icon.min.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Hanken+Grotesk:ital,wght@0,100..900;1,100..900&display=swap');
        * { font-family: "Hanken Grotesk", sans-serif; }

        /* ============================================
           CSS VARIABLES
           ============================================ */
        :root {
            --bg-body: #0a0a0a;
            --bg-card: #141414;
            --bg-hover: #1a1a1a;
            --bg-input: #0f0f0f;
            --border-1: #1e1e1e;
            --border-2: #262626;
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

        html.light-mode {
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --bg-hover: #f1f5f9;
            --bg-input: #ffffff;
            --border-1: #f1f5f9;
            --border-2: #e2e8f0;
            --text-1: #0f172a;
            --text-2: #1e293b;
            --text-3: #334155;
            --text-4: #475569;
            --text-5: #64748b;
            --text-6: #94a3b8;
        }

        body {
            background: var(--bg-body);
            color: var(--text-2);
            transition: background 0.3s ease, color 0.3s ease;
        }

        /* ============================================
           BACKGROUND PATTERN
           ============================================ */
        .login-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .login-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle at 20% 20%, rgba(236, 188, 66, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 80% 80%, rgba(236, 188, 66, 0.05) 0%, transparent 40%);
        }

        /* Grid pattern overlay */
        .login-bg::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(236, 188, 66, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(236, 188, 66, 0.03) 1px, transparent 1px);
            background-size: 40px 40px;
            mask-image: radial-gradient(ellipse at center, black 30%, transparent 80%);
            -webkit-mask-image: radial-gradient(ellipse at center, black 30%, transparent 80%);
        }

        /* ============================================
           FORM INPUT
           ============================================ */
        .form-input {
            width: 100%;
            padding: 0.85rem 1rem;
            background: var(--bg-input);
            border: 1px solid var(--border-2);
            border-radius: 0.65rem;
            font-size: 0.9rem;
            color: var(--text-1);
            transition: all 0.2s ease;
            font-family: inherit;
            outline: none;
        }

        .form-input:focus {
            border-color: #ecbc42;
            box-shadow: 0 0 0 3px rgba(236, 188, 66, 0.15);
        }

        .form-input::placeholder {
            color: var(--text-6);
        }

        /* ============================================
           BUTTON GOLD
           ============================================ */
        .btn-gold {
            background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
            color: var(--gold-text);
            font-weight: 700;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(236, 188, 66, 0.25);
        }

        .btn-gold:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(236, 188, 66, 0.4);
        }

        .btn-gold:active:not(:disabled) {
            transform: translateY(0);
        }

        .btn-gold:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        /* Spinner */
        .spinner {
            display: none;
            width: 1.1rem;
            height: 1.1rem;
            border: 2px solid rgba(66, 32, 6, 0.3);
            border-top-color: #422006;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        .btn-gold.loading .spinner { display: inline-block; }
        .btn-gold.loading .btn-icon { display: none; }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* ============================================
           LOGO ANIMATION
           ============================================ */
        .logo-glow {
            animation: logo-glow 3s ease-in-out infinite;
        }

        @keyframes logo-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(236, 188, 66, 0.3); }
            50% { box-shadow: 0 0 35px rgba(236, 188, 66, 0.5); }
        }

        /* ============================================
           FADE IN ANIMATION
           ============================================ */
        .fade-in-up {
            animation: fadeInUp 0.5s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Theme toggle button */
        .theme-toggle {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            width: 2.75rem;
            height: 2.75rem;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-card);
            border: 1px solid var(--border-2);
            color: var(--text-4);
            cursor: pointer;
            transition: all 0.2s ease;
            z-index: 10;
        }

        .theme-toggle:hover {
            border-color: #ecbc42;
            color: #FDDD57;
            transform: scale(1.05);
        }

        #themeIcon-sun  { display: block; }
        #themeIcon-moon { display: none; }
        html.light-mode #themeIcon-sun  { display: none; }
        html.light-mode #themeIcon-moon { display: block; }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center px-4 py-8 relative">

    {{-- Background Pattern --}}
    <div class="login-bg"></div>

    {{-- Theme Toggle --}}
    <button type="button"
            id="themeToggle"
            class="theme-toggle"
            title="Ganti Tema">
        <iconify-icon id="themeIcon-sun" icon="mdi:weather-sunny" class="text-xl"></iconify-icon>
        <iconify-icon id="themeIcon-moon" icon="mdi:weather-night" class="text-xl"></iconify-icon>
    </button>


    {{-- Login Card --}}
    <div class="w-full max-w-md relative z-10 fade-in-up">

        <div class="rounded-2xl overflow-hidden shadow-2xl"
             style="background: var(--bg-card); border: 1px solid var(--border-2);
                    box-shadow: 0 20px 60px rgba(0,0,0,0.3);">

            {{-- Gold accent bar --}}
            <div class="h-1.5 bg-gradient-to-r from-transparent via-[#ecbc42] to-transparent"></div>

            <div class="p-8 sm:p-10">

                {{-- ============================================ --}}
                {{-- HEADER --}}
                {{-- ============================================ --}}
                <div class="text-center mb-8">

                    {{-- Logo --}}
                    @if(!empty($setting?->logo) && Storage::disk('public')->exists($setting->logo))
                        {{-- 🔥 LOGO DARI SETTINGS --}}
                        <div class="inline-flex items-center justify-center mb-4 logo-glow
                                    rounded-2xl px-4 py-3"
                            style="background: var(--bg-card);
                                    border: 1px solid rgba(236,188,66,0.2);">
                            <img
                                src="{{ Storage::url($setting->logo) }}"
                                alt="{{ $setting->store_name ?? 'Admin' }}"
                                class="h-16 w-auto max-w-[220px] object-contain"
                                onerror="this.onerror=null; this.parentElement.style.display='none'; document.getElementById('fallback-logo').style.display='inline-flex';"
                            >
                        </div>
                        {{-- Fallback jika gambar gagal load --}}
                        <div id="fallback-logo"
                            class="hidden items-center justify-center w-16 h-16 rounded-2xl mb-4
                                    bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                                    logo-glow">
                            <iconify-icon icon="mdi:store" class="text-slate-900 text-3xl"></iconify-icon>
                        </div>
                    @else
                        {{-- 🔥 FALLBACK: Ikon default kalau belum ada logo --}}
                        <div id="fallback-logo"
                            class="inline-flex items-center justify-center w-16 h-16 rounded-2xl mb-4
                                    bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                                    logo-glow">
                            <iconify-icon icon="mdi:store" class="text-slate-900 text-3xl"></iconify-icon>
                        </div>
                    @endif

                    {{-- Subtitle "Admin Panel" --}}
                    <div class="flex items-center justify-center gap-2 mt-1">
                        <div class="h-px flex-1 max-w-[60px]" style="background: linear-gradient(90deg, transparent, #ecbc42);"></div>
                        <p class="text-xs font-bold text-[#ecbc42] uppercase tracking-[0.2em]">
                            Admin Panel
                        </p>
                        <div class="h-px flex-1 max-w-[60px]" style="background: linear-gradient(90deg, #ecbc42, transparent);"></div>
                    </div>

                    <p class="text-sm mt-4" style="color: var(--text-5);">
                        Silakan masuk ke akun admin Anda
                    </p>
                </div>


                {{-- ============================================ --}}
                {{-- ERROR ALERT --}}
                {{-- ============================================ --}}
                @if ($errors->any())
                    <div class="flex items-start gap-3 rounded-xl px-4 py-3 mb-6
                                bg-red-500/10 border border-red-500/30 text-red-400">
                        <iconify-icon icon="mdi:alert-circle-outline" class="text-xl flex-shrink-0 mt-0.5"></iconify-icon>
                        <div class="text-sm min-w-0">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="flex items-start gap-3 rounded-xl px-4 py-3 mb-6
                                bg-red-500/10 border border-red-500/30 text-red-400">
                        <iconify-icon icon="mdi:alert-circle-outline" class="text-xl flex-shrink-0 mt-0.5"></iconify-icon>
                        <span class="text-sm">{{ session('error') }}</span>
                    </div>
                @endif

                @if (session('success'))
                    <div class="flex items-start gap-3 rounded-xl px-4 py-3 mb-6
                                bg-emerald-500/10 border border-emerald-500/30 text-emerald-400">
                        <iconify-icon icon="mdi:check-circle-outline" class="text-xl flex-shrink-0 mt-0.5"></iconify-icon>
                        <span class="text-sm">{{ session('success') }}</span>
                    </div>
                @endif


                {{-- ============================================ --}}
                {{-- FORM --}}
                {{-- ============================================ --}}
                <form action="{{ route('login.process') }}" method="POST" id="login-form">
                    @csrf

                    {{-- Email --}}
                    <div class="mb-5">
                        <label for="email"
                               class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider mb-2"
                               style="color: var(--text-3);">
                            <iconify-icon icon="mdi:email-outline" class="text-[#ecbc42] text-base"></iconify-icon>
                            Email
                        </label>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="admin@barokahsport.com"
                               required
                               autofocus
                               autocomplete="email"
                               class="form-input">
                    </div>


                    {{-- Password --}}
                    <div class="mb-6">
                        <label for="password"
                               class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider mb-2"
                               style="color: var(--text-3);">
                            <iconify-icon icon="mdi:lock-outline" class="text-[#ecbc42] text-base"></iconify-icon>
                            Password
                        </label>
                        <div class="relative">
                            <input type="password"
                                   id="password"
                                   name="password"
                                   placeholder="Masukkan password Anda"
                                   required
                                   autocomplete="current-password"
                                   class="form-input"
                                   style="padding-right: 3rem;">
                            <button type="button"
                                    onclick="togglePassword()"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3
                                           transition-colors"
                                    style="color: var(--text-5);"
                                    onmouseover="this.style.color='#FDDD57'"
                                    onmouseout="this.style.color='var(--text-5)'"
                                    tabindex="-1">
                                <iconify-icon id="password-eye" icon="mdi:eye-outline" class="text-lg"></iconify-icon>
                            </button>
                        </div>
                    </div>


                    {{-- Remember Me + Forgot --}}
                    <div class="flex items-center justify-between gap-3 mb-6">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox"
                                   name="remember"
                                   value="1"
                                   {{ old('remember') ? 'checked' : '' }}
                                   class="w-4 h-4 rounded cursor-pointer"
                                   style="accent-color: #ecbc42;">
                            <span class="text-xs font-medium transition-colors group-hover:text-[#FDDD57]"
                                  style="color: var(--text-4);">
                                Ingat saya
                            </span>
                        </label>

                        @if(Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                               class="text-xs font-semibold transition-colors"
                               style="color: #ecbc42;"
                               onmouseover="this.style.color='#FDDD57'"
                               onmouseout="this.style.color='#ecbc42'">
                                Lupa password?
                            </a>
                        @endif
                    </div>


                    {{-- Submit Button --}}
                    <button type="submit"
                            id="login-btn"
                            class="btn-gold w-full flex items-center justify-center gap-2 px-6 py-3.5 rounded-lg
                                   text-sm font-bold tracking-wider uppercase">
                        <span class="spinner"></span>
                        <iconify-icon icon="mdi:login" class="btn-icon text-lg"></iconify-icon>
                        <span class="btn-text">Masuk ke Dashboard</span>
                    </button>
                </form>

            </div>

            {{-- ============================================ --}}
            {{-- FOOTER --}}
            {{-- ============================================ --}}
            <div class="px-8 py-4 border-t text-center"
                 style="background: var(--bg-hover); border-color: var(--border-1);">
                <p class="text-xs flex items-center justify-center gap-1.5" style="color: var(--text-5);">
                    <iconify-icon icon="mdi:shield-lock-outline"></iconify-icon>
                    Halaman ini dilindungi. Akses hanya untuk administrator.
                </p>
            </div>

        </div>

        {{-- Copyright --}}
        <p class="text-center text-xs mt-6" style="color: var(--text-5);">
            &copy; {{ date('Y') }} {{ $setting?->store_name ?? 'Barokah Sport' }}. All rights reserved.
        </p>

    </div>


    {{-- ============================================ --}}
    {{-- SCRIPT --}}
    {{-- ============================================ --}}
    <script>
        // ============================================
        // THEME TOGGLE
        // ============================================
        (function() {
            document.addEventListener('DOMContentLoaded', function() {
                // Sync body class
                if (document.documentElement.classList.contains('light-mode')) {
                    document.body.classList.add('light-mode');
                }

                var themeToggle = document.getElementById('themeToggle');
                if (themeToggle) {
                    themeToggle.addEventListener('click', function() {
                        document.documentElement.classList.toggle('light-mode');
                        document.body.classList.toggle('light-mode');

                        var isLight = document.documentElement.classList.contains('light-mode');
                        localStorage.setItem('admin-theme', isLight ? 'light' : 'dark');
                    });
                }
            });
        })();

        // ============================================
        // TOGGLE PASSWORD
        // ============================================
        function togglePassword() {
            var input = document.getElementById('password');
            var icon = document.getElementById('password-eye');

            if (input.type === 'password') {
                input.type = 'text';
                icon.setAttribute('icon', 'mdi:eye-off-outline');
            } else {
                input.type = 'password';
                icon.setAttribute('icon', 'mdi:eye-outline');
            }
        }

        // ============================================
        // SUBMIT LOADING STATE
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {
            var loginForm = document.getElementById('login-form');
            var loginBtn = document.getElementById('login-btn');

            if (loginForm && loginBtn) {
                loginForm.addEventListener('submit', function() {
                    loginBtn.disabled = true;
                    loginBtn.classList.add('loading');
                    var btnText = loginBtn.querySelector('.btn-text');
                    if (btnText) btnText.textContent = 'Memproses...';
                });
            }
        });
    </script>

</body>
</html>