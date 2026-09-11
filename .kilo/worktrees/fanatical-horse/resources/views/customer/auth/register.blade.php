@extends('layouts.customer')

@section('title', 'Daftar - Barokah Sport')

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

    .auth_container .form_group .password_hint {
        font-size: 0.65vw;
        color: #94a3b8;
        margin-top: 0.3vw;
    }

    .auth_container .form_terms {
        display: flex;
        align-items: flex-start;
        gap: 0.5vw;
        margin-bottom: 1.5vw;
        font-size: 0.75vw;
        color: #475569;
    }

    .auth_container .form_terms input[type="checkbox"] {
        width: 0.9vw;
        height: 0.9vw;
        margin-top: 0.1vw;
        accent-color: #076694;
        cursor: pointer;
        flex-shrink: 0;
    }

    .auth_container .form_terms a {
        color: #076694;
        text-decoration: none;
    }

    .auth_container .form_terms a:hover {
        text-decoration: underline;
    }

    .auth_container .btn_register {
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

    .auth_container .btn_register:hover {
        background: #055a7a;
    }

    .auth_container .btn_register:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .auth_container .btn_register .spinner {
        display: none;
        width: 1.2vw;
        height: 1.2vw;
        border: 0.15vw solid rgba(255, 255, 255, 0.3);
        border-top-color: #ffffff;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
        flex-shrink: 0;
    }

    .auth_container .btn_register.loading .spinner {
        display: block;
    }

    .auth_container .btn_register.loading .btn-text {
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

        .auth_container .form_group .password_hint {
            font-size: 1.8vw;
        }

        .auth_container .form_terms {
            font-size: 2vw;
            gap: 1.5vw;
        }

        .auth_container .form_terms input[type="checkbox"] {
            width: 2.5vw;
            height: 2.5vw;
        }

        .auth_container .btn_register {
            padding: 2.2vw;
            font-size: 2.8vw;
            border-radius: 1.5vw;
        }

        .auth_container .btn_register .spinner {
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

        .auth_container .form_group .password_hint {
            font-size: 2.2vw;
        }

        .auth_container .form_terms {
            font-size: 2.5vw;
            gap: 2vw;
        }

        .auth_container .form_terms input[type="checkbox"] {
            width: 3.5vw;
            height: 3.5vw;
        }

        .auth_container .btn_register {
            padding: 3vw;
            font-size: 3.5vw;
            border-radius: 2vw;
        }

        .auth_container .btn_register .spinner {
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

    <h1 class="auth_title">Daftar Akun</h1>
    <p class="auth_subtitle">Mulai berbelanja dengan membuat akun baru</p>

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

    {{-- Form Register --}}
    <form action="{{ route('customer.register.process') }}" method="POST" id="register-form">
        @csrf

        <div class="form_group">
            <label for="name">Nama Lengkap</label>
            <input type="text" name="name" id="name" 
                   value="{{ old('name') }}" 
                   placeholder="Nama lengkap Anda" 
                   required>
            @error('name')
                <p class="input_error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form_group">
            <label for="email">Alamat Email</label>
            <input type="email" name="email" id="email" 
                   value="{{ old('email') }}" 
                   placeholder="contoh@email.com" 
                   required>
            @error('email')
                <p class="input_error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form_group">
            <label for="password">Kata Sandi</label>
            <div class="password-wrapper">
                <input type="password" name="password" id="password" 
                    placeholder="Minimal 8 karakter" required>
                <button type="button" class="toggle-password-btn" onclick="togglePasswordVisibility('password', this)">
                    <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                </button>
            </div>
            <div id="register-password-strength" class="password-strength"></div>
            <p class="password_hint">Gunakan minimal 8 karakter dengan kombinasi huruf dan angka.</p>
            @error('password')
                <p class="input_error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form_group">
            <label for="password_confirmation">Konfirmasi Kata Sandi</label>
            <div class="password-wrapper">
                <input type="password" name="password_confirmation" id="password_confirmation" 
                    placeholder="Ketik ulang kata sandi" required>
                <button type="button" class="toggle-password-btn" onclick="togglePasswordVisibility('password_confirmation', this)">
                    <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                </button>
            </div>
            <div id="register-password-match" class="password-match"></div>
        </div>

        <div class="form_terms">
            <input type="checkbox" name="terms" id="terms" value="1" {{ old('terms') ? 'checked' : '' }} required>
            <label for="terms">
                Saya menyetujui 
                <a href="{{ route('customer.terms') }}" target="_blank">Syarat & Ketentuan</a> 
                dan 
                <a href="{{ route('customer.privacy') }}" target="_blank">Kebijakan Privasi</a>
            </label>
        </div>
        @error('terms')
            <p style="color:#ef4444;font-size:0.7vw;margin-top:-0.5vw;margin-bottom:1vw;">{{ $message }}</p>
        @enderror

        <button type="submit" class="btn_register" id="register-btn">
            <span class="spinner"></span>
            <span class="btn-text">Daftar Sekarang</span>
        </button>
    </form>

    <div class="auth_footer">
        Sudah punya akun? <a href="{{ route('customer.login') }}">Masuk</a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('register-form');
    const registerBtn = document.getElementById('register-btn');

    if (registerForm) {
        registerForm.addEventListener('submit', function() {
            // Disable button dan tampilkan loading
            registerBtn.disabled = true;
            registerBtn.classList.add('loading');
            registerBtn.querySelector('.btn-text').textContent = 'Memproses...';
        });
    }
});
</script>

@endsection