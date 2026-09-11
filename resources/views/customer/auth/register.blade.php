@extends('layouts.customer')

@section('title', 'Daftar - Barokah Sport')

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

    .auth_container .form_group .input_error iconify-icon {
        font-size: 0.85vw;
        flex-shrink: 0;
    }

    .auth_container .form_group .password_hint {
        display: flex;
        align-items: center;
        gap: 0.3vw;
        font-size: 0.68vw;
        color: #94a3b8;
        margin-top: 0.4vw;
        line-height: 1.5;
    }

    .auth_container .form_group .password_hint iconify-icon {
        font-size: 0.8vw;
        flex-shrink: 0;
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
       PASSWORD STRENGTH / MATCH (jika JS ditambahkan)
       -------------------------------------------- */
    .password-strength,
    .password-match {
        margin-top: 0.4vw;
        font-size: 0.7vw;
    }

    .password-strength:empty,
    .password-match:empty {
        display: none;
    }

    /* --------------------------------------------
       FORM TERMS
       -------------------------------------------- */
    .auth_container .form_terms {
        display: flex;
        align-items: flex-start;
        gap: 0.5vw;
        margin-bottom: 1.5vw;
        font-size: 0.75vw;
        color: #475569;
        line-height: 1.6;
    }

    .auth_container .form_terms input[type="checkbox"] {
        width: 0.9vw;
        height: 0.9vw;
        margin-top: 0.2vw;
        accent-color: #ecbc42;
        cursor: pointer;
        flex-shrink: 0;
    }

    .auth_container .form_terms label {
        cursor: pointer;
        user-select: none;
    }

    .auth_container .form_terms a {
        color: rgb(102, 72, 9);
        text-decoration: none;
        font-weight: 600;
        transition: opacity 0.2s ease;
    }

    .auth_container .form_terms a:hover {
        text-decoration: underline;
        opacity: 0.8;
    }

    /* Terms error */
    .terms_error {
        display: flex;
        align-items: center;
        gap: 0.3vw;
        color: #dc2626;
        font-size: 0.7vw;
        margin-top: -0.5vw;
        margin-bottom: 1vw;
    }

    .terms_error iconify-icon {
        font-size: 0.85vw;
        flex-shrink: 0;
    }

    /* --------------------------------------------
       BUTTON REGISTER
       -------------------------------------------- */
    .auth_container .btn_register {
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

    .auth_container .btn_register:hover:not(:disabled) {
        transform: translateY(-0.1vw);
        box-shadow: 0 0.35vw 1.2vw rgba(236, 188, 66, 0.5);
    }

    .auth_container .btn_register:active:not(:disabled) {
        transform: translateY(0);
    }

    .auth_container .btn_register:disabled {
        opacity: 0.75;
        cursor: not-allowed;
        transform: none;
    }

    .auth_container .btn_register iconify-icon {
        font-size: 1.05vw;
    }

    .auth_container .btn_register .spinner {
        display: none;
        width: 1vw;
        height: 1vw;
        border: 0.15vw solid rgba(102, 72, 9, 0.3);
        border-top-color: rgb(102, 72, 9);
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
        flex-shrink: 0;
    }

    .auth_container .btn_register.loading .spinner {
        display: block;
    }

    .auth_container .btn_register.loading iconify-icon {
        display: none;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
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
        .auth_container .form_group .input_error iconify-icon { font-size: 2vw; }

        .auth_container .form_group .password_hint {
            gap: 0.7vw;
            font-size: 1.65vw;
            margin-top: 0.9vw;
        }
        .auth_container .form_group .password_hint iconify-icon { font-size: 2vw; }

        .password-wrapper input { padding-right: 6vw; }

        .toggle-password-btn {
            right: 2vw;
            padding: 0.5vw;
        }
        .toggle-password-btn iconify-icon { font-size: 2.6vw; }

        .password-strength,
        .password-match {
            font-size: 1.7vw;
            margin-top: 0.9vw;
        }

        .auth_container .form_terms {
            font-size: 1.9vw;
            gap: 1.2vw;
            margin-bottom: 3.5vw;
        }
        .auth_container .form_terms input[type="checkbox"] {
            width: 2.5vw;
            height: 2.5vw;
            margin-top: 0.5vw;
        }

        .terms_error {
            font-size: 1.7vw;
            gap: 0.7vw;
            margin-top: -1vw;
            margin-bottom: 2.5vw;
        }
        .terms_error iconify-icon { font-size: 2vw; }

        .auth_container .btn_register {
            padding: 2.3vw;
            font-size: 2.2vw;
            border-radius: 1.5vw;
            gap: 1.2vw;
        }
        .auth_container .btn_register iconify-icon { font-size: 2.6vw; }

        .auth_container .btn_register .spinner {
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
        .auth_container .form_group label iconify-icon { font-size: 3.9vw; }

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
        .auth_container .form_group .input_error iconify-icon { font-size: 3.2vw; }

        .auth_container .form_group .password_hint {
            gap: 1vw;
            font-size: 2.6vw;
            margin-top: 1.3vw;
            line-height: 1.6;
        }
        .auth_container .form_group .password_hint iconify-icon { font-size: 3.2vw; }

        .password-wrapper input { padding-right: 10vw; }

        .toggle-password-btn {
            right: 2.5vw;
            padding: 1vw;
        }
        .toggle-password-btn iconify-icon { font-size: 4.2vw; }

        .password-strength,
        .password-match {
            font-size: 2.7vw;
            margin-top: 1.3vw;
        }

        .auth_container .form_terms {
            font-size: 3.5vw;
            gap: 2vw;
            margin-bottom: 5vw;
            line-height: 1.7;
        }
        .auth_container .form_terms input[type="checkbox"] {
            width: 4vw;
            height: 4vw;
            margin-top: 0.7vw;
        }

        .terms_error {
            font-size: 2.7vw;
            gap: 1vw;
            margin-top: -1.5vw;
            margin-bottom: 3.5vw;
        }
        .terms_error iconify-icon { font-size: 3.2vw; }

        .auth_container .btn_register {
            padding: 3.5vw;
            font-size: 4.5vw;
            border-radius: 2.5vw;
            gap: 1.8vw;
            letter-spacing: 0.1em;
        }
        .auth_container .btn_register iconify-icon { font-size: 4vw; }

        .auth_container .btn_register .spinner {
            width: 4vw;
            height: 4vw;
            border-width: 0.5vw;
        }

        .auth_container .auth_footer {
            font-size: 3.5vw;
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

    <h1 class="auth_title">Daftar Akun</h1>
    <p class="auth_subtitle">Mulai berbelanja dengan membuat akun baru</p>

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

    {{-- Form Register --}}
    <form action="{{ route('customer.register.process') }}" method="POST" id="register-form">
        @csrf

        {{-- Nama --}}
        <div class="form_group">
            <label for="name">
                <iconify-icon icon="mdi:account-outline"></iconify-icon>
                Nama Lengkap <span class="required">*</span>
            </label>
            <input type="text"
                   name="name"
                   id="name"
                   value="{{ old('name') }}"
                   placeholder="Nama lengkap Anda"
                   required>
            @error('name')
                <p class="input_error">
                    <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Nomor HP --}}
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
                   required>
            @error('phone')
                <p class="input_error">
                    <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="form_group">
            <label for="password">
                <iconify-icon icon="mdi:lock-outline"></iconify-icon>
                Kata Sandi <span class="required">*</span>
            </label>
            <div class="password-wrapper">
                <input type="password"
                       name="password"
                       id="password"
                       placeholder="Minimal 8 karakter"
                       required>
                <button type="button"
                        class="toggle-password-btn"
                        onclick="togglePasswordVisibility('password', this)"
                        aria-label="Toggle password visibility">
                    <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                </button>
            </div>
            <div id="register-password-strength" class="password-strength"></div>
            <p class="password_hint">
                <iconify-icon icon="mdi:information-outline"></iconify-icon>
                Gunakan minimal 8 karakter dengan kombinasi huruf dan angka.
            </p>
            @error('password')
                <p class="input_error">
                    <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Konfirmasi Password --}}
        <div class="form_group">
            <label for="password_confirmation">
                <iconify-icon icon="mdi:lock-check-outline"></iconify-icon>
                Konfirmasi Kata Sandi <span class="required">*</span>
            </label>
            <div class="password-wrapper">
                <input type="password"
                       name="password_confirmation"
                       id="password_confirmation"
                       placeholder="Ketik ulang kata sandi"
                       required>
                <button type="button"
                        class="toggle-password-btn"
                        onclick="togglePasswordVisibility('password_confirmation', this)"
                        aria-label="Toggle password visibility">
                    <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                </button>
            </div>
            <div id="register-password-match" class="password-match"></div>
            @error('password_confirmation')
                <p class="input_error">
                    <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Terms --}}
        <div class="form_terms">
            <input type="checkbox"
                   name="terms"
                   id="terms"
                   value="1"
                   {{ old('terms') ? 'checked' : '' }}
                   required>
            <label for="terms">
                Saya menyetujui
                <a href="{{ route('customer.terms') }}" target="_blank">Syarat & Ketentuan</a>
                dan
                <a href="{{ route('customer.privacy') }}" target="_blank">Kebijakan Privasi</a>
            </label>
        </div>
        @error('terms')
            <p class="terms_error">
                <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                {{ $message }}
            </p>
        @enderror

        {{-- Submit --}}
        <button type="submit" class="btn_register" id="register-btn">
            <span class="spinner"></span>
            <iconify-icon icon="mdi:account-plus-outline"></iconify-icon>
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
            registerBtn.disabled = true;
            registerBtn.classList.add('loading');
            registerBtn.querySelector('.btn-text').textContent = 'Memproses...';
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