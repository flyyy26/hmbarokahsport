@extends('layouts.customer')

@section('title', 'Login - Barokah Sport')

@section('content')

<style>
    .auth_container {
        width: 100%;
        max-width: 28vw;
        margin: 4vw auto;
        padding: 2.5vw;
        background: #ffffff;
        border-radius: 1vw;
        border: 0.1vw solid #e2e8f0;
        box-shadow: 0 0.5vw 2vw rgba(0, 0, 0, 0.05);
    }

    .auth_container .auth_logo {
        text-align: center;
        margin-bottom: 1.5vw;
    }

    .auth_container .auth_logo img {
        height: 3.5vw;
    }

    .auth_container .auth_title {
        font-size: 2.1vw;
        font-weight: 700;
        color: #0f172a;
        text-align: center;
        margin-bottom: 0.3vw;
        font-family: heading, sans-serif;
        text-transform: uppercase;
    }

    .auth_container .auth_subtitle {
        font-size: 0.85vw;
        color: #94a3b8;
        text-align: center;
        margin-bottom: 2vw;
    }

    .auth_container .form_group {
        margin-bottom: 1.2vw;
    }

    .auth_container .form_group label {
        display: block;
        font-size: 0.8vw;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 0.3vw;
    }

    .auth_container .form_group input {
        width: 100%;
        padding: 0.7vw 1vw;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.5vw;
        font-size: 0.85vw;
        color: #0f172a;
        transition: all 0.3s ease;
        background: #f8fafc;
    }

    .auth_container .form_group input:focus {
        outline: none;
        border-color: #076694;
        background: #ffffff;
        box-shadow: 0 0 0 0.2vw rgba(7, 102, 148, 0.1);
    }

    .auth_container .form_group .input_error {
        font-size: 0.7vw;
        color: #ef4444;
        margin-top: 0.3vw;
    }

    .auth_container .form_options {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5vw;
        font-size: 0.75vw;
    }

    .auth_container .form_options .remember_me {
        display: flex;
        align-items: center;
        gap: 0.4vw;
        cursor: pointer;
        color: #475569;
    }

    .auth_container .form_options .remember_me input[type="checkbox"] {
        width: 0.9vw;
        height: 0.9vw;
        accent-color: #076694;
        cursor: pointer;
    }

    .auth_container .form_options .forgot_link {
        color: #076694;
        text-decoration: none;
    }

    .auth_container .form_options .forgot_link:hover {
        text-decoration: underline;
    }

    .auth_container .btn_login {
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
        position: relative;
    }

    .auth_container .btn_login:hover {
        background: #055a7a;
    }

    .auth_container .btn_login:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .auth_container .btn_login .spinner {
        display: none;
        width: 1.2vw;
        height: 1.2vw;
        border: 0.15vw solid rgba(255, 255, 255, 0.3);
        border-top-color: #ffffff;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
        flex-shrink: 0;
    }

    .auth_container .btn_login.loading .spinner {
        display: block;
    }

    .auth_container .btn_login.loading .btn-text {
        opacity: 0.8;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .auth_container .auth_footer {
        text-align: center;
        margin-top: 1.5vw;
        font-size: 0.8vw;
        color: #94a3b8;
    }

    .auth_container .auth_footer a {
        color: #076694;
        text-decoration: none;
        font-weight: 600;
    }

    .auth_container .auth_footer a:hover {
        text-decoration: underline;
    }

    .auth_container .alert {
        padding: 0.8vw 1vw;
        border-radius: 0.5vw;
        font-size: 0.8vw;
        margin-bottom: 1.2vw;
    }

    .auth_container .alert_error {
        background: #fef2f2;
        border: 0.1vw solid #fecaca;
        color: #dc2626;
    }

    .auth_container .alert_success {
        background: #f0fdf4;
        border: 0.1vw solid #bbf7d0;
        color: #16a34a;
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

    @media (max-width: 768px) {
        .toggle-password-btn {
            right: 1.5vw;
            font-size: 2vw;
        }
        .toggle-password-btn iconify-icon {
            font-size: 2.5vw;
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
    }

    /* Responsive */
    @media (max-width: 768px) {
        .auth_container {
            max-width: 80vw;
            padding: 5vw 6vw;
            margin: 8vw auto;
            border-radius: 3vw;
        }

        .auth_container .auth_logo img {
            height: 8vw;
        }

        .auth_container .auth_title {
            font-size: 4.5vw;
        }

        .auth_container .auth_subtitle {
            font-size: 2.5vw;
        }

        .auth_container .form_group label {
            font-size: 2.2vw;
        }

        .auth_container .form_group input {
            padding: 2vw 3vw;
            font-size: 2.5vw;
            border-radius: 1.5vw;
        }

        .auth_container .form_group .input_error {
            font-size: 2vw;
        }

        .auth_container .form_options {
            font-size: 2.2vw;
        }

        .auth_container .form_options .remember_me input[type="checkbox"] {
            width: 2.5vw;
            height: 2.5vw;
        }

        .auth_container .btn_login {
            padding: 2.2vw;
            font-size: 2.8vw;
            border-radius: 1.5vw;
        }

        .auth_container .btn_login .spinner {
            width: 3vw;
            height: 3vw;
            border-width: 0.3vw;
        }

        .auth_container .auth_footer {
            font-size: 2.2vw;
            margin-top: 3vw;
        }

        .auth_container .alert {
            padding: 2vw 3vw;
            font-size: 2.2vw;
            border-radius: 1.5vw;
        }
    }

    @media (max-width: 480px) {
        .auth_container {
            max-width: 92vw;
            padding: 6vw 5vw;
            margin: 5vw auto;
        }

        .auth_container .auth_logo img {
            height: 10vw;
        }

        .auth_container .auth_title {
            font-size: 5.5vw;
        }

        .auth_container .auth_subtitle {
            font-size: 3vw;
        }

        .auth_container .form_group label {
            font-size: 2.8vw;
        }

        .auth_container .form_group input {
            padding: 2.5vw 3.5vw;
            font-size: 3vw;
            border-radius: 2vw;
        }

        .auth_container .form_group .input_error {
            font-size: 2.5vw;
        }

        .auth_container .form_options {
            font-size: 2.8vw;
            flex-direction: column;
            gap: 1.5vw;
            align-items: flex-start;
        }

        .auth_container .form_options .remember_me input[type="checkbox"] {
            width: 3.5vw;
            height: 3.5vw;
        }

        .auth_container .btn_login {
            padding: 3vw;
            font-size: 3.5vw;
            border-radius: 2vw;
        }

        .auth_container .btn_login .spinner {
            width: 4vw;
            height: 4vw;
            border-width: 0.4vw;
        }

        .auth_container .auth_footer {
            font-size: 2.8vw;
            margin-top: 4vw;
        }

        .auth_container .alert {
            padding: 2.5vw 4vw;
            font-size: 2.8vw;
            border-radius: 2vw;
        }
    }
</style>

<div class="auth_container">

    <h1 class="auth_title">Masuk</h1>
    <p class="auth_subtitle">Masuk untuk berbelanja dan kelola pesanan Anda</p>

    {{-- Alert Error --}}
    @if(session('error'))
        <div class="alert alert_error">
            {{ session('error') }}
        </div>
    @endif

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="alert alert_success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Form Login --}}
    <form action="{{ route('customer.login.process') }}" method="POST" id="login-form">
        @csrf

        <div class="form_group">
            <label for="email">Alamat Email</label>
            <input type="email" name="email" id="email" 
                   value="{{ old('email') }}" 
                   placeholder="contoh@email.com" 
                   required autofocus>
            @error('email')
                <p class="input_error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form_group">
            <label for="password">Kata Sandi</label>
            <div class="password-wrapper">
                <input type="password" name="password" id="password" 
                    placeholder="••••••••" required>
                <button type="button" class="toggle-password-btn" onclick="togglePasswordVisibility('password', this)">
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
            // Disable button dan tampilkan loading
            loginBtn.disabled = true;
            loginBtn.classList.add('loading');
            loginBtn.querySelector('.btn-text').textContent = 'Memproses...';
        });
    }
});
</script>

@endsection