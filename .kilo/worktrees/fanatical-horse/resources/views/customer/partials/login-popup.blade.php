<div id="login-popup-overlay" class="login-popup-overlay" style="display: none;">
    <div class="login-popup-container">
        <button type="button" class="login-popup-close" onclick="closeLoginPopup()">
            <iconify-icon icon="mdi:close"></iconify-icon>
        </button>

        <div class="login-popup-content">
            {{-- ============================================ --}}
            {{-- STATE SUKSES (Login/Register Berhasil) --}}
            {{-- ============================================ --}}
            <div id="login-popup-success" style="display: none; text-align: center; padding: 1vw 0;">
                <div class="success-icon"><iconify-icon icon="material-symbols:check-rounded"></iconify-icon></div>
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
                    <span class="alert-icon"><iconify-icon icon="mdi:close"></iconify-icon></span>
                    <span class="alert-message" id="login-popup-error-message">Email atau password salah</span>
                </div>

                <form id="login-popup-form" onsubmit="submitLoginPopup(event)">
                    @csrf
                    <div class="login-popup-form-group">
                        <label for="login-popup-email">Email</label>
                        <input type="email" id="login-popup-email" placeholder="contoh@email.com" required>
                    </div>

                    {{-- 🔥 PASSWORD DENGAN TOGGLE MATA --}}
                    <div class="login-popup-form-group">
                        <label for="login-popup-password">Password</label>
                        <div class="password-wrapper">
                            <input type="password" id="login-popup-password" placeholder="••••••••" required>
                            <button type="button" class="toggle-password-btn" onclick="togglePasswordVisibility('login-popup-password', this)">
                                <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                            </button>
                        </div>
                    </div>

                    <div class="login-popup-options">
                        <label class="remember-me">
                            <input type="checkbox" id="login-popup-remember">
                            Ingat saya
                        </label>
                        <a href="#" class="forgot-link">Lupa password?</a>
                    </div>

                    <button type="submit" class="login-popup-btn" id="login-popup-btn">
                        <span class="spinner" id="login-popup-spinner" style="display:none;"></span>
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
                    <span class="alert-icon"><iconify-icon icon="mdi:close"></iconify-icon></span>
                    <span class="alert-message" id="register-popup-error-message">Registrasi gagal</span>
                </div>

                <form id="register-popup-form" onsubmit="submitRegisterPopup(event)">
                    @csrf
                    <div class="login-popup-form-group">
                        <label for="register-popup-name">Nama Lengkap</label>
                        <input type="text" id="register-popup-name" placeholder="Nama lengkap Anda" required>
                    </div>

                    <div class="login-popup-form-group">
                        <label for="register-popup-email">Email</label>
                        <input type="email" id="register-popup-email" placeholder="contoh@email.com" required>
                    </div>

                    {{-- 🔥 PASSWORD DENGAN TOGGLE MATA --}}
                    <div class="login-popup-form-group">
                        <label for="register-popup-password">Password</label>
                        <div class="password-wrapper">
                            <input type="password" id="register-popup-password" placeholder="Minimal 8 karakter" required>
                            <button type="button" class="toggle-password-btn" onclick="togglePasswordVisibility('register-popup-password', this)">
                                <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                            </button>
                        </div>
                        <div id="register-password-strength" class="password-strength"></div>
                    </div>

                    {{-- 🔥 KONFIRMASI PASSWORD DENGAN VALIDASI --}}
                    <div class="login-popup-form-group">
                        <label for="register-popup-password-confirm">Konfirmasi Password</label>
                        <div class="password-wrapper">
                            <input type="password" id="register-popup-password-confirm" placeholder="Ketik ulang password" required>
                            <button type="button" class="toggle-password-btn" onclick="togglePasswordVisibility('register-popup-password-confirm', this)">
                                <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                            </button>
                        </div>
                        <div id="register-password-match" class="password-match"></div>
                    </div>

                    <div class="login-popup-options" style="margin-bottom:1.2vw;">
                        <label class="remember-me">
                            <input type="checkbox" id="register-popup-terms" required>
                            Saya setuju dengan 
                            <a href="{{ route('customer.terms') }}" target="_blank" style="color:#076694;text-decoration:none;">Syarat & Ketentuan</a>
                        </label>
                    </div>

                    <button type="submit" class="login-popup-btn" id="register-popup-btn">
                        <span class="spinner" id="register-popup-spinner" style="display:none;"></span>
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
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(6px);
        z-index: 100000;
        display: none;
        justify-content: center;
        align-items: center;
        animation: loginFadeIn 0.3s ease;
    }

    .login-popup-overlay.active {
        display: flex;
    }

    @keyframes loginFadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }

    /* ============================================
       LOGIN POPUP CONTAINER
       ============================================ */
    .login-popup-container {
        background: #ffffff;
        border-radius: 1.2vw;
        max-width: 28vw;
        width: 100%;
        padding: 2.5vw 3vw;
        position: relative;
        box-shadow: 0 1vw 4vw rgba(0, 0, 0, 0.2);
        max-height: 90vh;
        overflow-y: auto;
    }

    .login-popup-container::-webkit-scrollbar {
        width: 0.3vw;
    }

    .login-popup-container::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 0.3vw;
    }

    .login-popup-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 0.3vw;
    }

    /* ============================================
       CLOSE BUTTON
       ============================================ */
    .login-popup-close {
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

    .login-popup-close:hover {
        background: #e2e8f0;
        transform: rotate(90deg);
    }

    /* ============================================
       LOGO & HEADER
       ============================================ */
    .login-popup-logo {
        text-align: center;
        margin-bottom: 1.2vw;
    }

    .login-popup-logo img {
        height: 3vw;
    }

    .login-popup-title {
        font-size: 1.6vw;
        font-weight: 700;
        color: #0f172a;
        text-align: center;
        margin-bottom: 0.3vw;
    }

    .login-popup-subtitle {
        font-size: 0.85vw;
        color: #94a3b8;
        text-align: center;
        margin-bottom: 1.5vw;
    }

    /* ============================================
       STATE SUKSES
       ============================================ */
    .success-icon {
        font-size: 4vw;
        display: flex;
        align-items:center;
        justify-content:center;
        margin-bottom: 0.5vw;
        animation: successPop 0.5s ease;
        width:5vw;
        height:5vw;
        margin:auto;
        border-radius:100vw;
        border:.2vw solid #10b981;
        margin-bottom:1.3vw;
    }
    .success-icon iconify-icon{
        font-size:3vw;
        color:#10b981;
    }
    .alert-icon {
        font-size: 4vw;
        display: flex;
        align-items:center;
        justify-content:center;
        margin-bottom: 0.5vw;
        animation: successPop 0.5s ease;
        width:5vw;
        height:5vw;
        margin:auto;
        border-radius:100vw;
        border:.2vw solid #e40b1d;
        margin-bottom:1.3vw;
    }
    .alert-icon iconify-icon{
        font-size:3vw;
        color:#e40b1d;
    }

    @keyframes successPop {
        0% { transform: scale(0); opacity: 0; }
        50% { transform: scale(1.3); }
        100% { transform: scale(1); opacity: 1; }
    }

    .success-title {
        font-size: 1.8vw;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.2vw;
    }

    .success-subtitle {
        font-size: 0.85vw;
        color: #94a3b8;
        margin-bottom: 1.5vw;
    }

    .success-loader {
        width: 2vw;
        height: 2vw;
        margin: 0 auto;
        border: 0.2vw solid #e2e8f0;
        border-top-color: #076694;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
    }

    /* ============================================
       ALERT
       ============================================ */
    .login-popup-alert {
        padding: 0.7vw 1vw;
        border-radius: 0.5vw;
        font-size: 0.8vw;
        margin-bottom: 1vw;
        background: #fef2f2;
        border: 0.1vw solid #fecaca;
        color: #dc2626;
        display: flex;
        align-items: center;
        gap: 0.5vw;
    }

    /* ============================================
       FORM
       ============================================ */
    .login-popup-form-group {
        margin-bottom: 1vw;
    }

    .login-popup-form-group label {
        display: block;
        font-size: 0.8vw;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 0.3vw;
    }

    .login-popup-form-group input {
        width: 100%;
        padding: 0.7vw 1vw;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.5vw;
        font-size: 0.85vw;
        color: #0f172a;
        transition: all 0.3s ease;
        background: #f8fafc;
    }

    .login-popup-form-group input:focus {
        outline: none;
        border-color: #076694;
        background: #ffffff;
        box-shadow: 0 0 0 0.2vw rgba(7, 102, 148, 0.1);
    }

    /* ============================================
       OPTIONS
       ============================================ */
    .login-popup-options {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.2vw;
        font-size: 0.75vw;
        color: #475569;
    }

    .login-popup-options .remember-me {
        display: flex;
        align-items: center;
        gap: 0.4vw;
        cursor: pointer;
    }

    .login-popup-options .remember-me input[type="checkbox"] {
        width: 0.9vw;
        height: 0.9vw;
        accent-color: #076694;
        cursor: pointer;
    }

    .login-popup-options .forgot-link {
        color: #076694;
        text-decoration: none;
    }

    .login-popup-options .forgot-link:hover {
        text-decoration: underline;
    }

    /* ============================================
       SUBMIT BUTTON
       ============================================ */
    .login-popup-btn {
        width: 100%;
        padding: 0.8vw;
        background: #076694;
        color: #ffffff;
        border: none;
        border-radius: 0.5vw;
        font-size: 0.9vw;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.05vw;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.8vw;
    }

    .login-popup-btn:hover {
        background: #055a7a;
    }

    .login-popup-btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .login-popup-btn .spinner {
        width: 1.2vw;
        height: 1.2vw;
        border: 0.15vw solid rgba(255, 255, 255, 0.3);
        border-top-color: #ffffff;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* ============================================
       FOOTER & SWITCH BUTTON
       ============================================ */
    .login-popup-footer {
        text-align: center;
        margin-top: 1.2vw;
        font-size: 0.8vw;
        color: #94a3b8;
    }

    .login-popup-switch-btn {
        background: none;
        border: none;
        color: #076694;
        font-weight: 600;
        cursor: pointer;
        font-size: 0.8vw;
        font-family: inherit;
        padding: 0;
        transition: all 0.3s ease;
    }

    .login-popup-switch-btn:hover {
        text-decoration: underline;
    }

    /* ============================================
       RESPONSIVE
       ============================================ */
    @media (max-width: 768px) {
        .login-popup-container {
            max-width: 80vw;
            padding: 5vw 6vw;
            border-radius: 3vw;
        }

        .login-popup-logo img {
            height: 8vw;
        }

        .login-popup-title {
            font-size: 4vw;
        }

        .login-popup-subtitle {
            font-size: 2.5vw;
        }

        .login-popup-close {
            width: 6vw;
            height: 6vw;
            font-size: 3.5vw;
            top: 2.5vw;
            right: 3vw;
        }

        .login-popup-form-group label {
            font-size: 2.2vw;
        }

        .login-popup-form-group input {
            font-size: 2.5vw;
            padding: 2vw 3vw;
            border-radius: 1.5vw;
        }

        .login-popup-options {
            font-size: 2.2vw;
        }

        .login-popup-options .remember-me input[type="checkbox"] {
            width: 2.5vw;
            height: 2.5vw;
        }

        .login-popup-btn {
            font-size: 2.8vw;
            padding: 2.2vw;
            border-radius: 1.5vw;
        }

        .login-popup-btn .spinner {
            width: 3vw;
            height: 3vw;
            border-width: 0.3vw;
        }

        .login-popup-footer {
            font-size: 2.2vw;
            margin-top: 3vw;
        }

        .login-popup-switch-btn {
            font-size: 2.2vw;
        }

        .login-popup-alert {
            font-size: 2.2vw;
            padding: 2vw 3vw;
            border-radius: 1.5vw;
        }

        .success-icon {
            font-size: 10vw;
        }

        .success-title {
            font-size: 4.5vw;
        }

        .success-subtitle {
            font-size: 2.5vw;
        }

        .success-loader {
            width: 5vw;
            height: 5vw;
            border-width: 0.3vw;
        }
    }

    @media (max-width: 480px) {
        .login-popup-container {
            max-width: 92vw;
            padding: 6vw 5vw;
            border-radius: 4vw;
        }

        .login-popup-logo img {
            height: 10vw;
        }

        .login-popup-title {
            font-size: 5.5vw;
        }

        .login-popup-subtitle {
            font-size: 3vw;
        }

        .login-popup-close {
            width: 8vw;
            height: 8vw;
            font-size: 4.5vw;
        }

        .login-popup-form-group label {
            font-size: 2.8vw;
        }

        .login-popup-form-group input {
            font-size: 3vw;
            padding: 2.5vw 3.5vw;
            border-radius: 2vw;
        }

        .login-popup-options {
            font-size: 2.8vw;
            flex-direction: column;
            gap: 1.5vw;
            align-items: flex-start;
        }

        .login-popup-options .remember-me input[type="checkbox"] {
            width: 3.5vw;
            height: 3.5vw;
        }

        .login-popup-btn {
            font-size: 3.5vw;
            padding: 3vw;
            border-radius: 2vw;
        }

        .login-popup-btn .spinner {
            width: 4vw;
            height: 4vw;
            border-width: 0.4vw;
        }

        .login-popup-footer {
            font-size: 2.8vw;
            margin-top: 4vw;
        }

        .login-popup-switch-btn {
            font-size: 2.8vw;
        }

        .login-popup-alert {
            font-size: 2.8vw;
            padding: 2.5vw 4vw;
            border-radius: 2vw;
        }

        .success-icon {
            font-size: 14vw;
        }

        .success-title {
            font-size: 5.5vw;
        }

        .success-subtitle {
            font-size: 3vw;
        }

        .success-loader {
            width: 6vw;
            height: 6vw;
            border-width: 0.4vw;
        }
    }

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
        font-size: 1.1vw;
        padding: 0.2vw;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.3s ease;
    }

    .toggle-password-btn:hover {
        color: #0f172a;
    }

    .toggle-password-btn iconify-icon {
        font-size: 1.2vw;
    }

    /* ============================================
       PASSWORD STRENGTH & MATCH
       ============================================ */
    .password-strength {
        margin-top: 0.3vw;
        font-size: 0.7vw;
        height: 1.2vw;
    }

    .password-strength .strength-bar {
        display: flex;
        gap: 0.2vw;
        margin-top: 0.2vw;
    }

    .password-strength .strength-bar span {
        flex: 1;
        height: 0.2vw;
        background: #e2e8f0;
        border-radius: 0.1vw;
        transition: background 0.3s ease;
    }

    .password-strength .strength-bar span.active {
        background: #076694;
    }

    .password-strength .strength-bar span.active.weak {
        background: #ef4444;
    }

    .password-strength .strength-bar span.active.medium {
        background: #f59e0b;
    }

    .password-strength .strength-bar span.active.strong {
        background: #10b981;
    }

    .password-strength .strength-text {
        font-size: 0.65vw;
        color: #94a3b8;
    }

    .password-match {
        margin-top: 0.3vw;
        font-size: 0.7vw;
        height: 1.2vw;
    }

    .password-match.match-success {
        color: #10b981;
    }

    .password-match.match-error {
        color: #ef4444;
    }

    /* ============================================
       RESPONSIVE
       ============================================ */
    @media (max-width: 768px) {
        .toggle-password-btn {
            right: 1.5vw;
            font-size: 2vw;
        }

        .toggle-password-btn iconify-icon {
            font-size: 2.5vw;
        }

        .password-strength {
            font-size: 1.5vw;
        }

        .password-strength .strength-text {
            font-size: 1.3vw;
        }

        .password-match {
            font-size: 1.5vw;
        }
    }

    @media (max-width: 480px) {
        .toggle-password-btn {
            right: 2vw;
            font-size: 2.8vw;
        }

        .toggle-password-btn iconify-icon {
            font-size: 3.5vw;
        }

        .password-strength {
            font-size: 2vw;
        }

        .password-strength .strength-text {
            font-size: 1.8vw;
        }

        .password-match {
            font-size: 2vw;
        }
    }
</style>