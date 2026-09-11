@extends('layouts.customer')

@section('title', 'Login - Barokah Sport')

@section('content')

<style>
    /* ============================================
       AUTH PAGE STYLES
       ============================================ */
    .auth_container {
        width: 100%;
        max-width: 28vw;
        margin: 4vw auto;
        padding: 2.5vw;
        background: #ffffff;
        border-radius: 1vw;
        border: 0.1vw solid #e2e8f0;
        box-shadow: 0 0.5vw 2vw rgba(0, 0, 0, 0.05);
        position: relative;
        overflow: hidden;
    }

    .auth_container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 0.35vw;
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
    }

    /* --------------------------------------------
       LOGO
       -------------------------------------------- */
    .auth_container .auth_logo {
        text-align: center;
        margin-bottom: 1.2vw;
        margin-top: 0.5vw;
    }

    .auth_container .auth_logo img {
        height: 3.5vw;
    }

    /* --------------------------------------------
       TITLE
       -------------------------------------------- */
    .auth_container .auth_title {
        font-size: 1.8vw;
        font-weight: 800;
        color: #0f172a;
        text-align: center;
        margin-bottom: 0.4vw;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .auth_container .auth_subtitle {
        font-size: 0.82vw;
        color: #94a3b8;
        text-align: center;
        margin-bottom: 2vw;
        line-height: 1.5;
    }

    /* --------------------------------------------
       FORM GROUP
       -------------------------------------------- */
    .auth_container .form_group {
        margin-bottom: 1.2vw;
    }

    .auth_container .form_group label {
        display: flex;
        align-items: center;
        gap: 0.35vw;
        font-size: 0.78vw;
        font-weight: 600;
        color: #334155;
        margin-bottom: 0.4vw;
    }

    .auth_container .form_group label iconify-icon {
        color: #ecbc42;
        font-size: 0.95vw;
    }

    .auth_container .form_group label .required {
        color: #dc2626;
    }

    .auth_container .form_group input {
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

    .auth_container .form_group input::placeholder {
        color: #cbd5e1;
    }

    .auth_container .form_group input:focus {
        border-color: #ecbc42;
        background: #ffffff;
        box-shadow: 0 0 0 0.25vw rgba(236, 188, 66, 0.15);
    }

    .auth_container .form_group .input_error {
        display: flex;
        align-items: center;
        gap: 0.3vw;
        font-size: 0.7vw;
        color: #dc2626;
        margin-top: 0.3vw;
    }

    .auth_container .form_group .input_error::before {
        content: '⚠';
        font-size: 0.8vw;
    }

    /* --------------------------------------------
       PASSWORD WRAPPER
       -------------------------------------------- */
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

    /* --------------------------------------------
       FORM OPTIONS
       -------------------------------------------- */
    .auth_container .form_options {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5vw;
        margin-bottom: 1.5vw;
        font-size: 0.75vw;
    }

    .auth_container .form_options .remember_me {
        display: flex;
        align-items: center;
        gap: 0.4vw;
        cursor: pointer;
        color: #475569;
        user-select: none;
    }

    .auth_container .form_options .remember_me input[type="checkbox"] {
        width: 0.9vw;
        height: 0.9vw;
        accent-color: #ecbc42;
        cursor: pointer;
    }

    .auth_container .form_options .forgot_link {
        color: rgb(102, 72, 9);
        text-decoration: none;
        font-weight: 600;
        transition: opacity 0.2s ease;
    }

    .auth_container .form_options .forgot_link:hover {
        text-decoration: underline;
        opacity: 0.8;
    }

    /* --------------------------------------------
       BUTTON LOGIN
       -------------------------------------------- */
    .auth_container .btn_login {
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
        position: relative;
        font-family: inherit;
        box-shadow: 0 0.15vw 0.5vw rgba(236, 188, 66, 0.3);
    }

    .auth_container .btn_login:hover:not(:disabled) {
        transform: translateY(-0.1vw);
        box-shadow: 0 0.35vw 1.2vw rgba(236, 188, 66, 0.5);
    }

    .auth_container .btn_login:active:not(:disabled) {
        transform: translateY(0);
    }

    .auth_container .btn_login:disabled {
        opacity: 0.75;
        cursor: not-allowed;
        transform: none;
    }

    .auth_container .btn_login iconify-icon {
        font-size: 1.05vw;
    }

    .auth_container .btn_login .spinner {
        display: none;
        width: 1vw;
        height: 1vw;
        border: 0.15vw solid rgba(102, 72, 9, 0.3);
        border-top-color: rgb(102, 72, 9);
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
        flex-shrink: 0;
    }

    .auth_container .btn_login.loading .spinner {
        display: block;
    }

    .auth_container .btn_login.loading iconify-icon {
        display: none;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* --------------------------------------------
       DIVIDER
       -------------------------------------------- */
    .auth_divider {
        display: flex;
        align-items: center;
        gap: 0.7vw;
        margin: 1.5vw 0;
        font-size: 0.7vw;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .auth_divider::before,
    .auth_divider::after {
        content: '';
        flex: 1;
        height: 0.1vw;
        background: #e2e8f0;
    }

    /* --------------------------------------------
       FOOTER
       -------------------------------------------- */
    .auth_container .auth_footer {
        text-align: center;
        margin-top: 1.5vw;
        font-size: 0.78vw;
        color: #94a3b8;
    }

    .auth_container .auth_footer a {
        color: rgb(102, 72, 9);
        text-decoration: none;
        font-weight: 700;
        transition: opacity 0.2s ease;
    }

    .auth_container .auth_footer a:hover {
        text-decoration: underline;
    }

    /* --------------------------------------------
       ALERT
       -------------------------------------------- */
    .auth_container .alert {
        display: flex;
        align-items: center;
        gap: 0.5vw;
        padding: 0.8vw 1vw;
        border-radius: 0.5vw;
        font-size: 0.78vw;
        margin-bottom: 1.2vw;
        line-height: 1.5;
    }

    .auth_container .alert iconify-icon {
        font-size: 1.1vw;
        flex-shrink: 0;
    }

    .auth_container .alert_error {
        background: #fef2f2;
        border: 0.1vw solid #fecaca;
        color: #b91c1c;
    }

    .auth_container .alert_success {
        background: #ecfdf5;
        border: 0.1vw solid #a7f3d0;
        color: #047857;
    }

    /* ============================================
       RESPONSIVE - TABLET
       ============================================ */
    @media (max-width: 1024px) {
        .auth_container {
            max-width: 60vw;
            padding: 4vw 5vw;
            margin: 6vw auto;
            border-radius: 2.5vw;
            border-width: 0.2vw;
        }

        .auth_container::before { height: 0.7vw; }

        .auth_container .auth_logo { margin-bottom: 2.5vw; margin-top: 1vw; }
        .auth_container .auth_logo img { height: 7vw; }

        .auth_container .auth_title { font-size: 4vw; margin-bottom: 1vw; }
        .auth_container .auth_subtitle { font-size: 2vw; margin-bottom: 4vw; }

        .auth_container .form_group { margin-bottom: 3vw; }

        .auth_container .form_group label {
            font-size: 1.9vw;
            gap: 0.8vw;
            margin-bottom: 1vw;
        }
        .auth_container .form_group label iconify-icon { font-size: 2.3vw; }

        .auth_container .form_group input {
            padding: 2vw 2.5vw;
            font-size: 2.1vw;
            border-radius: 1.3vw;
            border-width: 0.2vw;
        }

        .auth_container .form_group input:focus {
            box-shadow: 0 0 0 0.6vw rgba(236, 188, 66, 0.15);
        }

        .auth_container .form_group .input_error {
            font-size: 1.7vw;
            gap: 0.7vw;
            margin-top: 0.7vw;
        }
        .auth_container .form_group .input_error::before { font-size: 2vw; }

        .password-wrapper input { padding-right: 6vw; }

        .toggle-password-btn {
            right: 2vw;
            padding: 0.5vw;
        }
        .toggle-password-btn iconify-icon { font-size: 2.6vw; }

        .auth_container .form_options {
            font-size: 1.9vw;
            gap: 1.5vw;
            margin-bottom: 3.5vw;
        }
        .auth_container .form_options .remember_me { gap: 1vw; }
        .auth_container .form_options .remember_me input[type="checkbox"] {
            width: 2.5vw;
            height: 2.5vw;
        }

        .auth_container .btn_login {
            padding: 2.3vw;
            font-size: 2.2vw;
            border-radius: 1.5vw;
            gap: 1.2vw;
        }
        .auth_container .btn_login iconify-icon { font-size: 2.6vw; }

        .auth_container .btn_login .spinner {
            width: 2.5vw;
            height: 2.5vw;
            border-width: 0.35vw;
        }

        .auth_container .auth_footer {
            font-size: 1.9vw;
            margin-top: 3vw;
        }

        .auth_container .alert {
            gap: 1vw;
            padding: 2vw 2.5vw;
            font-size: 1.9vw;
            border-radius: 1.3vw;
            border-width: 0.2vw;
            margin-bottom: 3vw;
        }
        .auth_container .alert iconify-icon { font-size: 2.4vw; }
    }

    /* ============================================
       RESPONSIVE - MOBILE
       ============================================ */
    @media (max-width: 480px) {
        .auth_container {
            max-width: 92vw;
            padding: 6vw 5vw;
            margin: 6vw auto;
            border-radius: 4vw;
            border-width: 0.3vw;
        }

        .auth_container::before { height: 1vw; }

        .auth_container .auth_logo { margin-bottom: 4vw; margin-top: 1.5vw; }
        .auth_container .auth_logo img { height: 12vw; }

        .auth_container .auth_title { font-size: 6vw; margin-bottom: 1.5vw; }
        .auth_container .auth_subtitle { font-size: 3.5vw; margin-bottom: 6vw; }

        .auth_container .form_group { margin-bottom: 4.5vw; }

        .auth_container .form_group label {
            font-size: 3.5vw;
            gap: 1.2vw;
            margin-bottom: 1.5vw;
        }
        .auth_container .form_group label iconify-icon { font-size: 3.8vw; }

        .auth_container .form_group input {
            padding: 3vw 3.5vw;
            font-size: 4.2vw;
            border-radius: 2.5vw;
            border-width: 0.3vw;
        }

        .auth_container .form_group input:focus {
            box-shadow: 0 0 0 0.9vw rgba(236, 188, 66, 0.15);
        }

        .auth_container .form_group .input_error {
            font-size: 2.7vw;
            gap: 1vw;
            margin-top: 1vw;
        }
        .auth_container .form_group .input_error::before { font-size: 3.2vw; }

        .password-wrapper input { padding-right: 10vw; }

        .toggle-password-btn {
            right: 2.5vw;
            padding: 1vw;
        }
        .toggle-password-btn iconify-icon { font-size: 4.2vw; }

        .auth_container .form_options {
            font-size: 3.5vw;
            gap: 2vw;
            margin-bottom: 5vw;
            flex-wrap: wrap;
        }
        .auth_container .form_options .remember_me { gap: 1.5vw; }
        .auth_container .form_options .remember_me input[type="checkbox"] {
            width: 4vw;
            height: 4vw;
        }

        .auth_container .btn_login {
            padding: 3.5vw;
            font-size: 4.5vw;
            border-radius: 2.5vw;
            gap: 1.8vw;
            letter-spacing: 0.1em;
        }
        .auth_container .btn_login iconify-icon { font-size: 4vw; }

        .auth_container .btn_login .spinner {
            width: 4vw;
            height: 4vw;
            border-width: 0.5vw;
        }

        .auth_container .auth_footer {
            font-size: 4vw;
            margin-top: 4.5vw;
        }

        .auth_container .alert {
            gap: 1.5vw;
            padding: 3vw 3.5vw;
            font-size: 3vw;
            border-radius: 2.5vw;
            border-width: 0.3vw;
            margin-bottom: 4.5vw;
            line-height: 1.6;
        }
        .auth_container .alert iconify-icon { font-size: 4vw; }
    }
</style>

<div class="auth_container">

    <h1 class="auth_title">Masuk</h1>
    <p class="auth_subtitle">Masuk untuk berbelanja dan kelola pesanan Anda</p>

    {{-- Alert Error --}}
    @if(session('error'))
        <div class="alert alert_error">
            <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="alert alert_success">
            <iconify-icon icon="mdi:check-circle-outline"></iconify-icon>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Form Login --}}
    <form action="{{ route('customer.login.process') }}" method="POST" id="login-form">
        @csrf

        <div class="form_group">
            <label for="phone">
                <iconify-icon icon="mdi:phone-outline"></iconify-icon>
                Nomor HP <span class="required">*</span>
            </label>
            <input type="tel"
                   name="phone"
                   id="phone"
                   value="{{ old('phone') }}"
                   placeholder="08123456789"
                   required
                   autofocus>
            @error('phone')
                <p class="input_error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form_group">
            <label for="password">
                <iconify-icon icon="mdi:lock-outline"></iconify-icon>
                Kata Sandi <span class="required">*</span>
            </label>
            <div class="password-wrapper">
                <input type="password"
                       name="password"
                       id="password"
                       placeholder="••••••••"
                       required>
                <button type="button"
                        class="toggle-password-btn"
                        onclick="togglePasswordVisibility('password', this)"
                        aria-label="Toggle password visibility">
                    <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                </button>
            </div>
            @error('password')
                <p class="input_error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form_options">
            <label class="remember_me">
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                Ingat saya
            </label>
            <a href="#" class="forgot_link">Lupa kata sandi?</a>
        </div>

        <button type="submit" class="btn_login" id="login-btn">
            <span class="spinner"></span>
            <iconify-icon icon="mdi:login"></iconify-icon>
            <span class="btn-text">Masuk</span>
        </button>
    </form>

    <div class="auth_footer">
        Belum punya akun? <a href="{{ route('customer.register') }}">Daftar Sekarang</a>
    </div>
</div>

@if(session('success'))
<script>
    // 🔥 SET LOCALSTORAGE SAAT LOGIN BERHASIL
    localStorage.setItem('customer_logged_in', 'true');
    localStorage.setItem('customer_name', '{{ Auth::guard('customer')->user()->name ?? '' }}');

    // 🔥 CEK APAKAH ADA REDIRECT
    const urlParams = new URLSearchParams(window.location.search);
    const redirect = urlParams.get('redirect');
    if (redirect) {
        setTimeout(function() {
            window.location.href = decodeURIComponent(redirect);
        }, 500);
    }
</script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('login-form');
    const loginBtn = document.getElementById('login-btn');

    if (loginForm) {
        loginForm.addEventListener('submit', function() {
            loginBtn.disabled = true;
            loginBtn.classList.add('loading');
            loginBtn.querySelector('.btn-text').textContent = 'Memproses...';
        });
    }
});

// 🔥 TOGGLE PASSWORD
function togglePasswordVisibility(inputId, button) {
    const input = document.getElementById(inputId);
    const icon = button.querySelector('iconify-icon');

    if (input.type === 'password') {
        input.type = 'text';
        icon.setAttribute('icon', 'mdi:eye-off-outline');
    } else {
        input.type = 'password';
        icon.setAttribute('icon', 'mdi:eye-outline');
    }
}
window.togglePasswordVisibility = togglePasswordVisibility;
</script>

@endsection