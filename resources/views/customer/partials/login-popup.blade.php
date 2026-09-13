<div id="login-popup-overlay" class="login-popup-overlay" style="display: none;">
    <div class="login-popup-container">
        <button type="button" class="login-popup-close" onclick="closeLoginPopup()" aria-label="Tutup">
            <iconify-icon icon="mdi:close"></iconify-icon>
        </button>

        <div class="login-popup-content">

            {{-- ============================================ --}}
            {{-- STATE SUKSES --}}
            {{-- ============================================ --}}
            <div id="login-popup-success" style="display: none;">
                <div class="success-icon">
                    <iconify-icon icon="mdi:check-circle"></iconify-icon>
                </div>
                <h2 class="success-title" id="login-popup-success-title">Login Berhasil!</h2>
                <p class="success-subtitle" id="login-popup-success-message">Selamat datang kembali!</p>
                <div class="success-loader"></div>
            </div>

            {{-- ============================================ --}}
            {{-- FORM LOGIN --}}
            {{-- ============================================ --}}
            <div id="login-popup-login-form">
                <h2 class="login-popup-title">Login untuk Melanjutkan</h2>
                <p class="login-popup-subtitle">Silakan login untuk menambahkan produk ke keranjang atau wishlist</p>

                <div id="login-popup-error" class="login-popup-alert" style="display: none;">
                    <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                    <span id="login-popup-error-message">Nomor HP atau password salah</span>
                </div>

                <form id="login-popup-form" onsubmit="submitLoginPopup(event)">
                    @csrf

                    <div class="login-popup-form-group">
                        <label for="login-popup-phone">
                            <iconify-icon icon="mdi:phone-outline"></iconify-icon>
                            Nomor HP
                        </label>
                        <input type="tel" id="login-popup-phone" placeholder="08123456789" required>
                    </div>

                    <div class="login-popup-form-group">
                        <label for="login-popup-password">
                            <iconify-icon icon="mdi:lock-outline"></iconify-icon>
                            Password
                        </label>
                        <div class="password-wrapper">
                            <input type="password" id="login-popup-password" placeholder="••••••••" required>
                            <button type="button" class="toggle-password-btn" onclick="togglePasswordVisibility('login-popup-password', this)" aria-label="Toggle password">
                                <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                            </button>
                        </div>
                    </div>

                    <div class="login-popup-options">
                        <label class="remember-me">
                            <input type="checkbox" id="login-popup-remember">
                            <span>Ingat saya</span>
                        </label>
                        <a href="#" class="forgot-link">Lupa password?</a>
                    </div>

                    <button type="submit" class="login-popup-btn" id="login-popup-btn">
                        <span class="spinner" id="login-popup-spinner" style="display:none;"></span>
                        <iconify-icon icon="mdi:login"></iconify-icon>
                        <span class="btn-text">Login</span>
                    </button>
                </form>

                <div class="login-popup-footer">
                    Belum punya akun?
                    <button type="button" class="login-popup-switch-btn" onclick="switchToRegister()">
                        Daftar Sekarang
                    </button>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- FORM REGISTER --}}
            {{-- ============================================ --}}
            <div id="login-popup-register-form" style="display: none;">
                <h2 class="login-popup-title">Daftar Akun</h2>
                <p class="login-popup-subtitle">Buat akun untuk berbelanja dengan mudah</p>

                <div id="register-popup-error" class="login-popup-alert" style="display: none;">
                    <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                    <span id="register-popup-error-message">Registrasi gagal</span>
                </div>

                <form id="register-popup-form" onsubmit="submitRegisterPopup(event)">
                    @csrf

                    <div class="login-popup-form-group">
                        <label for="register-popup-name">
                            <iconify-icon icon="mdi:account-outline"></iconify-icon>
                            Nama Lengkap
                        </label>
                        <input type="text" id="register-popup-name" placeholder="Nama lengkap Anda" required>
                    </div>

                    <div class="login-popup-form-group">
                        <label for="register-popup-phone">
                            <iconify-icon icon="mdi:phone-outline"></iconify-icon>
                            Nomor HP
                        </label>
                        <input type="tel" id="register-popup-phone" placeholder="08123456789" required>
                    </div>

                    <div class="login-popup-form-group">
                        <label for="register-popup-password">
                            <iconify-icon icon="mdi:lock-outline"></iconify-icon>
                            Password
                        </label>
                        <div class="password-wrapper">
                            <input type="password" id="register-popup-password" placeholder="Minimal 8 karakter" required>
                            <button type="button" class="toggle-password-btn" onclick="togglePasswordVisibility('register-popup-password', this)" aria-label="Toggle password">
                                <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                            </button>
                        </div>
                        <div id="register-password-strength" class="password-strength"></div>
                    </div>

                    <div class="login-popup-form-group">
                        <label for="register-popup-password-confirm">
                            <iconify-icon icon="mdi:lock-check-outline"></iconify-icon>
                            Konfirmasi Password
                        </label>
                        <div class="password-wrapper">
                            <input type="password" id="register-popup-password-confirm" placeholder="Ketik ulang password" required>
                            <button type="button" class="toggle-password-btn" onclick="togglePasswordVisibility('register-popup-password-confirm', this)" aria-label="Toggle password">
                                <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                            </button>
                        </div>
                        <div id="register-password-match" class="password-match"></div>
                    </div>

                    <div class="login-popup-options login-popup-terms">
                        <label class="remember-me">
                            <input type="checkbox" id="register-popup-terms" required>
                            <span>
                                Saya setuju dengan
                                <a href="{{ route('customer.terms') }}" target="_blank">Syarat & Ketentuan</a>
                            </span>
                        </label>
                    </div>

                    <button type="submit" class="login-popup-btn" id="register-popup-btn">
                        <span class="spinner" id="register-popup-spinner" style="display:none;"></span>
                        <iconify-icon icon="mdi:account-plus-outline"></iconify-icon>
                        <span class="btn-text">Daftar Sekarang</span>
                    </button>
                </form>

                <div class="login-popup-footer">
                    Sudah punya akun?
                    <button type="button" class="login-popup-switch-btn" onclick="switchToLogin()">
                        Masuk
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* ============================================
       LOGIN POPUP OVERLAY
       ============================================ */
    .login-popup-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.55);
        backdrop-filter: blur(0.3vw);
        -webkit-backdrop-filter: blur(0.3vw);
        z-index: 100000;
        display: none;
        justify-content: center;
        align-items: center;
        padding: 1.5vw;
    }

    .login-popup-overlay.active {
        display: flex;
    }

    @keyframes loginFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* ============================================
       CONTAINER
       ============================================ */
    .login-popup-container {
        background: #ffffff;
        border-radius: 1.2vw;
        max-width: 28vw;
        width: 100%;
        padding: 2.5vw 3vw;
        position: relative;
        box-shadow: 0 1vw 4vw rgba(0, 0, 0, 0.2);
        max-height: 92vh;
        overflow-y: auto;
    }

    @keyframes loginSlideIn {
        from { opacity: 0; transform: scale(0.96) translateY(-1vw); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }

    .login-popup-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 0.35vw;
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
        border-radius: 1.2vw 1.2vw 0 0;
    }

    .login-popup-container::-webkit-scrollbar {
        width: 0.3vw;
    }
    .login-popup-container::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 100vw;
    }
    .login-popup-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 100vw;
    }

    /* ============================================
       CLOSE BUTTON
       ============================================ */
    .login-popup-close {
        position: absolute;
        top: 1vw;
        right: 1.2vw;
        background: #f8fafc;
        border: 0.1vw solid #e2e8f0;
        border-radius: 50%;
        width: 2.2vw;
        height: 2.2vw;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.25s ease;
        color: #64748b;
        padding: 0;
        z-index: 2;
    }

    .login-popup-close:hover {
        background: #fffbf0;
        border-color: #fde68a;
        color: rgb(102, 72, 9);
        transform: rotate(90deg);
    }

    .login-popup-close iconify-icon {
        font-size: 1.3vw;
    }

    /* ============================================
       HEADER
       ============================================ */
    .login-popup-title {
        font-size: 1.4vw;
        font-weight: 800;
        color: #0f172a;
        text-align: center;
        margin-bottom: 0.4vw;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    .login-popup-subtitle {
        font-size: 0.82vw;
        color: #94a3b8;
        text-align: center;
        margin-bottom: 1.5vw;
        line-height: 1.5;
    }

    /* ============================================
       STATE SUKSES
       ============================================ */
    #login-popup-success {
        text-align: center;
        padding: 1vw 0;
    }

    .success-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 5vw;
        height: 5vw;
        margin: 0 auto 1.3vw;
        border-radius: 50%;
        background: linear-gradient(135deg, #FDDD57 0%, #ecbc42 100%);
        color: rgb(102, 72, 9);
        box-shadow: 0 0 0 0.4vw #fffbf0, 0 0.5vw 1.5vw rgba(236, 188, 66, 0.35);
        animation: successPop 0.5s ease;
    }

    .success-icon iconify-icon {
        font-size: 3vw;
    }

    @keyframes successPop {
        0%   { transform: scale(0); opacity: 0; }
        50%  { transform: scale(1.15); }
        100% { transform: scale(1); opacity: 1; }
    }

    .success-title {
        font-size: 1.6vw;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 0.3vw;
    }

    .success-subtitle {
        font-size: 0.85vw;
        color: #94a3b8;
        margin-bottom: 1.5vw;
        line-height: 1.5;
    }

    .success-loader {
        width: 2vw;
        height: 2vw;
        margin: 0 auto;
        border: 0.2vw solid #fde68a;
        border-top-color: #ecbc42;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
    }

    /* ============================================
       ALERT
       ============================================ */
    .login-popup-alert {
        display: flex;
        align-items: center;
        gap: 0.5vw;
        padding: 0.75vw 1vw;
        border-radius: 0.5vw;
        font-size: 0.78vw;
        margin-bottom: 1vw;
        background: #fef2f2;
        border: 0.1vw solid #fecaca;
        color: #b91c1c;
        line-height: 1.5;
    }

    .login-popup-alert iconify-icon {
        font-size: 1.1vw;
        flex-shrink: 0;
    }

    /* ============================================
       FORM
       ============================================ */
    .login-popup-form-group {
        margin-bottom: 1vw;
    }

    .login-popup-form-group label {
        display: flex;
        align-items: center;
        gap: 0.35vw;
        font-size: 0.78vw;
        font-weight: 600;
        color: #334155;
        margin-bottom: 0.4vw;
    }

    .login-popup-form-group label iconify-icon {
        color: #ecbc42;
        font-size: 0.95vw;
    }

    .login-popup-form-group input {
        width: 100%;
        padding: 0.75vw 1vw;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.5vw;
        font-size: 0.85vw;
        color: #0f172a;
        transition: all 0.2s ease;
        background: #f8fafc;
        font-family: inherit;
        outline: none;
    }

    .login-popup-form-group input::placeholder {
        color: #cbd5e1;
    }

    .login-popup-form-group input:focus {
        border-color: #ecbc42;
        background: #ffffff;
        box-shadow: 0 0 0 0.25vw rgba(236, 188, 66, 0.15);
    }

    /* ============================================
       PASSWORD WRAPPER
       ============================================ */
    .password-wrapper {
        position: relative;
        width: 100%;
    }

    .password-wrapper input {
        width: 100%;
        padding-right: 3vw;
    }

    .toggle-password-btn {
        position: absolute;
        right: 0.8vw;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        color: #94a3b8;
        padding: 0.2vw;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.2s ease;
    }

    .toggle-password-btn:hover {
        color: #ecbc42;
    }

    .toggle-password-btn iconify-icon {
        font-size: 1.1vw;
    }

    /* ============================================
       OPTIONS
       ============================================ */
    .login-popup-options {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5vw;
        margin-bottom: 1.2vw;
        font-size: 0.75vw;
        color: #475569;
    }

    .login-popup-options.login-popup-terms {
        margin-bottom: 1.2vw;
        font-size: 0.72vw;
    }

    .login-popup-options .remember-me {
        display: flex;
        align-items: center;
        gap: 0.4vw;
        cursor: pointer;
        user-select: none;
        line-height: 1.5;
    }

    .login-popup-options .remember-me input[type="checkbox"] {
        width: 0.9vw;
        height: 0.9vw;
        accent-color: #ecbc42;
        cursor: pointer;
        flex-shrink: 0;
    }

    .login-popup-options .remember-me a {
        color: rgb(102, 72, 9);
        text-decoration: none;
        font-weight: 600;
        transition: opacity 0.2s ease;
    }

    .login-popup-options .remember-me a:hover {
        text-decoration: underline;
    }

    .login-popup-options .forgot-link {
        color: rgb(102, 72, 9);
        text-decoration: none;
        font-weight: 600;
        white-space: nowrap;
        transition: opacity 0.2s ease;
    }

    .login-popup-options .forgot-link:hover {
        text-decoration: underline;
    }

    /* ============================================
       BUTTON SUBMIT
       ============================================ */
    .login-popup-btn {
        width: 100%;
        padding: 0.9vw;
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
        color: rgb(102, 72, 9);
        border: none;
        border-radius: 0.6vw;
        font-size: 0.88vw;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.6vw;
        font-family: inherit;
        box-shadow: 0 0.15vw 0.5vw rgba(236, 188, 66, 0.3);
    }

    .login-popup-btn:hover:not(:disabled) {
        transform: translateY(-0.1vw);
        box-shadow: 0 0.35vw 1.2vw rgba(236, 188, 66, 0.5);
    }

    .login-popup-btn:active:not(:disabled) {
        transform: translateY(0);
    }

    .login-popup-btn:disabled {
        opacity: 0.75;
        cursor: not-allowed;
        transform: none;
    }

    .login-popup-btn iconify-icon {
        font-size: 1.05vw;
    }

    .login-popup-btn.loading iconify-icon {
        display: none;
    }

    .login-popup-btn .spinner {
        display: none;
        width: 1vw;
        height: 1vw;
        border: 0.15vw solid rgba(102, 72, 9, 0.3);
        border-top-color: rgb(102, 72, 9);
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
        flex-shrink: 0;
    }

    .login-popup-btn.loading .spinner {
        display: block;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* ============================================
       FOOTER
       ============================================ */
    .login-popup-footer {
        text-align: center;
        margin-top: 1.2vw;
        font-size: 0.78vw;
        color: #94a3b8;
    }

    .login-popup-switch-btn {
        background: none;
        border: none;
        color: rgb(102, 72, 9);
        font-weight: 700;
        cursor: pointer;
        font-size: 0.78vw;
        font-family: inherit;
        padding: 0;
        transition: opacity 0.2s ease;
    }

    .login-popup-switch-btn:hover {
        text-decoration: underline;
    }

    /* ============================================
       PASSWORD STRENGTH & MATCH
       ============================================ */
    .password-strength {
        margin-top: 0.5vw;
        font-size: 0.68vw;
        min-height: 0.8vw;
        color: #94a3b8;
        line-height: 1.5;
    }

    .password-strength .strength-bar {
        display: flex;
        gap: 0.25vw;
        margin-top: 0.3vw;
    }

    .password-strength .strength-bar span {
        flex: 1;
        height: 0.25vw;
        background: #e2e8f0;
        border-radius: 100vw;
        transition: background 0.3s ease;
    }

    .password-strength .strength-bar span.active.weak   { background: #ef4444; }
    .password-strength .strength-bar span.active.medium { background: #f59e0b; }
    .password-strength .strength-bar span.active.strong { background: #10b981; }

    .password-match {
        margin-top: 0.4vw;
        font-size: 0.68vw;
        min-height: 0.8vw;
        display: flex;
        align-items: center;
        gap: 0.25vw;
    }

    .password-match.match-success { color: #10b981; }
    .password-match.match-error   { color: #ef4444; }

    .password-match iconify-icon {
        font-size: 0.85vw;
    }

    /* ============================================
       RESPONSIVE - TABLET
       ============================================ */
    @media (max-width: 1024px) {
        .login-popup-overlay { padding: 2vw; }

        .login-popup-container {
            max-width: 70vw;
            padding: 5vw 6vw;
            border-radius: 3vw;
            border-width: 0.2vw;
        }

        .login-popup-container::before { height: 0.7vw; }

        .login-popup-close {
            top: 2.5vw;
            right: 2.5vw;
            width: 5.5vw;
            height: 5.5vw;
            border-width: 0.2vw;
        }
        .login-popup-close iconify-icon { font-size: 3.2vw; }

        .login-popup-title { font-size: 3.5vw; margin-bottom: 1vw; }
        .login-popup-subtitle { font-size: 2vw; margin-bottom: 3.5vw; }

        .login-popup-form-group { margin-bottom: 2.5vw; }

        .login-popup-form-group label {
            font-size: 1.9vw;
            gap: 0.8vw;
            margin-bottom: 1vw;
        }
        .login-popup-form-group label iconify-icon { font-size: 2.3vw; }

        .login-popup-form-group input {
            padding: 2vw 2.5vw;
            font-size: 2.1vw;
            border-radius: 1.3vw;
            border-width: 0.2vw;
        }

        .login-popup-form-group input:focus {
            box-shadow: 0 0 0 0.6vw rgba(236, 188, 66, 0.15);
        }

        .password-wrapper input { padding-right: 6vw; }

        .toggle-password-btn {
            right: 2vw;
            padding: 0.5vw;
        }
        .toggle-password-btn iconify-icon { font-size: 2.6vw; }

        .login-popup-options {
            font-size: 1.9vw;
            gap: 1.5vw;
            margin-bottom: 3vw;
        }
        .login-popup-options.login-popup-terms { font-size: 1.8vw; }

        .login-popup-options .remember-me { gap: 1vw; }
        .login-popup-options .remember-me input[type="checkbox"] {
            width: 2.5vw;
            height: 2.5vw;
        }

        .login-popup-btn {
            padding: 2.3vw;
            font-size: 2.2vw;
            border-radius: 1.5vw;
            gap: 1.2vw;
        }
        .login-popup-btn iconify-icon { font-size: 2.6vw; }

        .login-popup-btn .spinner {
            width: 2.5vw;
            height: 2.5vw;
            border-width: 0.35vw;
        }

        .login-popup-footer {
            font-size: 1.9vw;
            margin-top: 3vw;
        }
        .login-popup-switch-btn { font-size: 1.9vw; }

        .login-popup-alert {
            gap: 1vw;
            padding: 2vw 2.5vw;
            font-size: 1.9vw;
            border-radius: 1.3vw;
            border-width: 0.2vw;
            margin-bottom: 2.5vw;
        }
        .login-popup-alert iconify-icon { font-size: 2.4vw; }

        /* Success */
        .success-icon {
            width: 12vw;
            height: 12vw;
            margin-bottom: 3vw;
            box-shadow: 0 0 0 1vw #fffbf0, 0 1vw 3vw rgba(236, 188, 66, 0.35);
        }
        .success-icon iconify-icon { font-size: 7vw; }

        .success-title { font-size: 3.2vw; margin-bottom: 1vw; }
        .success-subtitle { font-size: 2vw; margin-bottom: 3vw; }

        .success-loader {
            width: 5vw;
            height: 5vw;
            border-width: 0.45vw;
        }

        /* Password strength */
        .password-strength {
            font-size: 1.6vw;
            margin-top: 1vw;
            min-height: 2vw;
        }
        .password-strength .strength-bar { gap: 0.6vw; margin-top: 0.7vw; }
        .password-strength .strength-bar span { height: 0.6vw; }

        .password-match {
            font-size: 1.6vw;
            margin-top: 1vw;
            min-height: 2vw;
            gap: 0.6vw;
        }
        .password-match iconify-icon { font-size: 2vw; }
    }

    /* ============================================
       RESPONSIVE - MOBILE
       ============================================ */
    @media (max-width: 480px) {
        .login-popup-overlay {
            padding: 3vw;
            align-items: flex-start;
            padding-top: 5vw;
        }

        .login-popup-container {
            max-width: 100%;
            max-height: 94vh;
            padding: 8vw 5vw 6vw;
            border-radius: 4vw;
            border-width: 0.3vw;
        }

        .login-popup-container::before { height: 1vw; border-radius: 4vw 4vw 0 0; }

        .login-popup-close {
            top: 3.5vw;
            right: 3.5vw;
            width: 9vw;
            height: 9vw;
            border-width: 0.3vw;
        }
        .login-popup-close iconify-icon { font-size: 5vw; }

        .login-popup-title { font-size: 5.5vw; margin-bottom: 1.5vw; }
        .login-popup-subtitle { font-size: 3.5vw; margin-bottom: 6vw; }

        .login-popup-form-group { margin-bottom: 4.5vw; }

        .login-popup-form-group label {
            font-size: 3.5vw;
            gap: 1.2vw;
            margin-bottom: 1.5vw;
        }
        .login-popup-form-group label iconify-icon { font-size: 3.9vw; }

        .login-popup-form-group input {
            padding: 3vw 3.5vw;
            font-size: 4.2vw;
            border-radius: 2.5vw;
            border-width: 0.3vw;
        }

        .login-popup-form-group input:focus {
            box-shadow: 0 0 0 0.9vw rgba(236, 188, 66, 0.15);
        }

        .password-wrapper input { padding-right: 10vw; }

        .toggle-password-btn {
            right: 2.5vw;
            padding: 1vw;
        }
        .toggle-password-btn iconify-icon { font-size: 4.2vw; }

        .login-popup-options {
            font-size: 3.5vw;
            gap: 2vw;
            margin-bottom: 5vw;
            flex-wrap: wrap;
        }
        .login-popup-options.login-popup-terms {
            font-size: 3.5vw;
            margin-bottom:4vw;
            align-items: flex-start;
        }

        .login-popup-options .remember-me { gap: 1.5vw; }
        .login-popup-options .remember-me input[type="checkbox"] {
            width: 4vw;
            height: 4vw;
            margin-top: 0.5vw;
        }

        .login-popup-btn {
            padding: 3.5vw;
            font-size: 4.5vw;
            border-radius: 2.5vw;
            gap: 1.8vw;
            letter-spacing: 0.1em;
        }
        .login-popup-btn iconify-icon { font-size: 4vw; }

        .login-popup-btn .spinner {
            width: 4vw;
            height: 4vw;
            border-width: 0.5vw;
        }

        .login-popup-footer {
            font-size: 4vw;
            margin-top: 4.5vw;
        }
        .login-popup-switch-btn { font-size: 4vw; }

        .login-popup-alert {
            gap: 1.5vw;
            padding: 3vw 3.5vw;
            font-size: 3vw;
            border-radius: 2.5vw;
            border-width: 0.3vw;
            margin-bottom: 4.5vw;
        }
        .login-popup-alert iconify-icon { font-size: 4vw; }

        /* Success */
        .success-icon {
            width: 22vw;
            height: 22vw;
            margin-bottom: 5vw;
            box-shadow: 0 0 0 2vw #fffbf0, 0 2vw 5vw rgba(236, 188, 66, 0.35);
        }
        .success-icon iconify-icon { font-size: 12vw; }

        .success-title { font-size: 5.5vw; margin-bottom: 1.5vw; }
        .success-subtitle { font-size: 3.2vw; margin-bottom: 5vw; }

        .success-loader {
            width: 9vw;
            height: 9vw;
            border-width: 0.7vw;
        }

        /* Password strength */
        .password-strength {
            font-size: 2.8vw;
            margin-top: 1.5vw;
            min-height: 4vw;
        }
        .password-strength .strength-bar { gap: 1.2vw; margin-top: 1.2vw; }
        .password-strength .strength-bar span { height: 1.2vw; }

        .password-match {
            font-size: 2.8vw;
            margin-top: 1.5vw;
            min-height: 4vw;
            gap: 1.2vw;
        }
        .password-match iconify-icon { font-size: 3.5vw; }
    }
</style>