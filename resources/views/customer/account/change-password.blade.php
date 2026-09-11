@extends('layouts.account')

@section('title', 'Ganti Kata Sandi - Barokah Sport')
@section('page-title', 'Ganti Kata Sandi')
@section('page-subtitle', 'Perbarui kata sandi akun Anda untuk keamanan.')

@section('account-content')

<style>
    /* ============================================
       CHANGE PASSWORD STYLES
       ============================================ */
    .cp-wrapper {
        max-width: 32vw;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 1.3vw;
        margin-top:1vw;
    }

    /* --------------------------------------------
       INFO BANNER
       -------------------------------------------- */
    .cp-info-banner {
        display: flex;
        align-items: flex-start;
        gap: 0.7vw;
        padding: 1vw 1.2vw;
        background: linear-gradient(135deg, #fffbf0 0%, #fff7e0 100%);
        border: 0.1vw solid #fde68a;
        border-radius: 0.7vw;
        font-size: 0.82vw;
        color: rgb(102, 72, 9);
        line-height: 1.6;
    }

    .cp-info-banner iconify-icon {
        font-size: 1.2vw;
        color: #ecbc42;
        flex-shrink: 0;
        margin-top: 0.1vw;
    }

    .cp-info-banner strong {
        font-weight: 700;
    }

    /* --------------------------------------------
       FORM
       -------------------------------------------- */
    .cp-form {
        display: flex;
        flex-direction: column;
        gap: 1.3vw;
    }

    .cp-group {
        display: flex;
        flex-direction: column;
        gap: 0.4vw;
    }

    .cp-label {
        display: flex;
        align-items: center;
        gap: 0.35vw;
        font-size: 0.85vw;
        font-weight: 600;
        color: #334155;
    }

    .cp-label iconify-icon {
        color: #ecbc42;
        font-size: 1vw;
    }

    .cp-label .required {
        color: #dc2626;
    }

    .cp-input-wrap {
        position: relative;
    }

    .cp-input {
        width: 100%;
        padding: 0.85vw 3vw 0.85vw 1.1vw;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.6vw;
        font-size: 0.85vw;
        color: #0f172a;
        background: #ffffff;
        outline: none;
        transition: all 0.2s ease;
        font-family: inherit;
    }

    .cp-input::placeholder {
        color: #cbd5e1;
    }

    .cp-input:focus {
        border-color: #ecbc42;
        box-shadow: 0 0 0 0.25vw rgba(236, 188, 66, 0.2);
    }

    .cp-input.has-error {
        border-color: #fca5a5;
    }

    .cp-input.has-error:focus {
        border-color: #dc2626;
        box-shadow: 0 0 0 0.25vw rgba(220, 38, 38, 0.15);
    }

    /* Toggle Password Button */
    .cp-toggle-btn {
        position: absolute;
        right: 0.9vw;
        top: 50%;
        transform: translateY(-50%);
        display: flex;
        align-items: center;
        justify-content: center;
        width: 1.8vw;
        height: 1.8vw;
        border-radius: 50%;
        border: none;
        background: transparent;
        color: #94a3b8;
        cursor: pointer;
        transition: all 0.2s ease;
        padding: 0;
    }

    .cp-toggle-btn:hover {
        color: #ecbc42;
        background: #fffbf0;
    }

    .cp-toggle-btn iconify-icon {
        font-size: 1.15vw;
    }

    /* Hint */
    .cp-hint {
        display: flex;
        align-items: center;
        gap: 0.3vw;
        font-size: 0.72vw;
        color: #94a3b8;
        margin-top: 0.2vw;
    }
    .cp-hint iconify-icon {
        font-size: 0.85vw;
        flex-shrink: 0;
    }

    /* Error */
    .cp-error {
        display: flex;
        align-items: center;
        gap: 0.35vw;
        font-size: 0.75vw;
        color: #dc2626;
        margin-top: 0.2vw;
    }
    .cp-error iconify-icon {
        font-size: 0.9vw;
        flex-shrink: 0;
    }

    /* Match Indicator */
    .cp-match {
        display: none;
        align-items: center;
        gap: 0.35vw;
        font-size: 0.75vw;
        margin-top: 0.2vw;
        padding: 0.4vw 0.7vw;
        border-radius: 0.5vw;
        transition: all 0.2s ease;
    }

    .cp-match.is-visible {
        display: flex;
    }

    .cp-match.is-match {
        background: #ecfdf5;
        color: #047857;
    }

    .cp-match.is-mismatch {
        background: #fef2f2;
        color: #b91c1c;
    }

    .cp-match iconify-icon {
        font-size: 0.9vw;
        flex-shrink: 0;
    }

    /* --------------------------------------------
       ACTIONS
       -------------------------------------------- */
    .cp-actions {
        display: flex;
        gap: 0.7vw;
        padding-top: 1.2vw;
        border-top: 0.1vw solid #f1f5f9;
        margin-top: 0.5vw;
    }

    .cp-btn {
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4vw;
        padding: 0.9vw 1.4vw;
        border-radius: 0.7vw;
        font-size: 0.85vw;
        font-weight: 600;
        cursor: pointer;
        border: 0.1vw solid transparent;
        transition: all 0.2s ease;
        text-decoration: none;
        font-family: inherit;
    }

    .cp-btn iconify-icon {
        font-size: 1.05vw;
    }

    .cp-btn-outline {
        background: #ffffff;
        border-color: #e2e8f0;
        color: #475569;
    }
    .cp-btn-outline:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .cp-btn-gold {
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
        color: rgb(102, 72, 9);
        box-shadow: 0 0.15vw 0.5vw rgba(236, 188, 66, 0.3);
    }
    .cp-btn-gold:hover {
        transform: translateY(-0.1vw);
        box-shadow: 0 0.3vw 1vw rgba(236, 188, 66, 0.4);
    }
    .cp-btn-gold:active {
        transform: translateY(0);
    }

    /* ============================================
       RESPONSIVE - TABLET
       ============================================ */
    @media (max-width: 1024px) {
        .cp-wrapper {
            max-width: 75vw;
            gap: 3vw;
            margin-top:1vw;
        }

        .cp-info-banner {
            gap: 1.7vw;
            padding: 2.5vw 3vw;
            border-radius: 1.7vw;
            border-width: 0.2vw;
            font-size: 2vw;
        }
        .cp-info-banner iconify-icon { font-size: 3vw; }

        .cp-form { gap: 3vw; }
        .cp-group { gap: 1vw; }

        .cp-label {
            font-size: 2.1vw;
            gap: 0.9vw;
        }
        .cp-label iconify-icon { font-size: 2.5vw; }

        .cp-input {
            padding: 2.2vw 7vw 2.2vw 3vw;
            border-radius: 1.5vw;
            font-size: 2.2vw;
            border-width: 0.2vw;
        }

        .cp-input:focus {
            box-shadow: 0 0 0 0.6vw rgba(236, 188, 66, 0.2);
        }
        .cp-input.has-error:focus {
            box-shadow: 0 0 0 0.6vw rgba(220, 38, 38, 0.15);
        }

        .cp-toggle-btn {
            right: 2vw;
            width: 4.5vw;
            height: 4.5vw;
        }
        .cp-toggle-btn iconify-icon { font-size: 2.9vw; }

        .cp-hint {
            font-size: 1.8vw;
            gap: 0.8vw;
            margin-top: 0.5vw;
        }
        .cp-hint iconify-icon { font-size: 2.1vw; }

        .cp-error {
            font-size: 1.8vw;
            gap: 0.9vw;
            margin-top: 0.5vw;
        }
        .cp-error iconify-icon { font-size: 2.2vw; }

        .cp-match {
            gap: 0.9vw;
            font-size: 1.8vw;
            margin-top: 0.5vw;
            padding: 1vw 1.8vw;
            border-radius: 1.3vw;
        }
        .cp-match iconify-icon { font-size: 2.2vw; }

        .cp-actions {
            gap: 1.7vw;
            padding-top: 3vw;
            margin-top: 1.5vw;
            border-top-width: 0.2vw;
        }

        .cp-btn {
            padding: 2.3vw 3vw;
            border-radius: 1.5vw;
            font-size: 2.1vw;
            gap: 1vw;
            border-width: 0.2vw;
        }
        .cp-btn iconify-icon { font-size: 2.5vw; }
    }

    /* ============================================
       RESPONSIVE - MOBILE
       ============================================ */
    @media (max-width: 480px) {
        .cp-wrapper {
            max-width: 100%;
            gap: 4.5vw;
            margin-top:3vw;
        }

        .cp-info-banner {
            gap: 2.5vw;
            padding: 3.5vw 4vw;
            border-radius: 2.5vw;
            border-width: 0.3vw;
            font-size: 3.2vw;
            line-height: 1.6;
            align-items: flex-start;
        }
        .cp-info-banner iconify-icon {
            font-size: 4.5vw;
            margin-top: 0.3vw;
        }

        .cp-form { gap: 4.5vw; }
        .cp-group { gap: 1.5vw; }

        .cp-label {
            font-size: 3.2vw;
            gap: 1.2vw;
        }
        .cp-label iconify-icon { font-size: 4vw; }

        .cp-input {
            padding: 3.2vw 11vw 3.2vw 3.5vw;
            border-radius: 2.5vw;
            font-size: 3.2vw;
            border-width: 0.3vw;
        }

        .cp-input:focus {
            box-shadow: 0 0 0 0.9vw rgba(236, 188, 66, 0.2);
        }
        .cp-input.has-error:focus {
            box-shadow: 0 0 0 0.9vw rgba(220, 38, 38, 0.15);
        }

        .cp-toggle-btn {
            right: 2.5vw;
            width: 7vw;
            height: 7vw;
        }
        .cp-toggle-btn iconify-icon { font-size: 4.5vw; }

        .cp-hint {
            font-size: 2.8vw;
            gap: 1.2vw;
            margin-top: 0.8vw;
        }
        .cp-hint iconify-icon { font-size: 3.4vw; }

        .cp-error {
            font-size: 2.8vw;
            gap: 1.2vw;
            margin-top: 0.8vw;
        }
        .cp-error iconify-icon { font-size: 3.4vw; }

        .cp-match {
            gap: 1.2vw;
            font-size: 2.8vw;
            margin-top: 0.8vw;
            padding: 2vw 2.5vw;
            border-radius: 2vw;
        }
        .cp-match iconify-icon { font-size: 3.4vw; }

        .cp-actions {
            flex-direction: column-reverse;
            gap: 2.5vw;
            padding-top: 4.5vw;
            margin-top: 2.5vw;
            border-top-width: 0.3vw;
        }

        .cp-btn {
            width: 100%;
            padding: 3.5vw 4vw;
            border-radius: 2.5vw;
            font-size: 3.2vw;
            gap: 1.5vw;
            border-width: 0.3vw;
        }
        .cp-btn iconify-icon { font-size: 4vw; }
    }
</style>

<div class="cp-wrapper">

    {{-- Info Banner --}}
    <div class="cp-info-banner">
        <iconify-icon icon="mdi:shield-lock-outline"></iconify-icon>
        <div>
            <strong>Tips keamanan:</strong> gunakan kombinasi huruf besar, huruf kecil,
            angka, dan simbol. Jangan gunakan kata sandi yang sama dengan akun lain.
        </div>
    </div>

    {{-- Form --}}
    <form action="{{ route('customer.password.update') }}" method="POST" class="cp-form" id="change-password-form">
        @csrf
        @method('PUT')

        {{-- Kata Sandi Saat Ini --}}
        <div class="cp-group">
            <label for="current_password" class="cp-label">
                <iconify-icon icon="mdi:lock-outline"></iconify-icon>
                Kata Sandi Saat Ini <span class="required">*</span>
            </label>
            <div class="cp-input-wrap">
                <input type="password"
                       name="current_password"
                       id="current_password"
                       required
                       placeholder="Masukkan kata sandi saat ini"
                       class="cp-input @error('current_password') has-error @enderror">
                <button type="button"
                        onclick="togglePassword('current_password', this)"
                        class="cp-toggle-btn"
                        aria-label="Toggle password visibility">
                    <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                </button>
            </div>
            @error('current_password')
                <p class="cp-error">
                    <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Kata Sandi Baru --}}
        <div class="cp-group">
            <label for="password" class="cp-label">
                <iconify-icon icon="mdi:lock-plus-outline"></iconify-icon>
                Kata Sandi Baru <span class="required">*</span>
            </label>
            <div class="cp-input-wrap">
                <input type="password"
                       name="password"
                       id="password"
                       required
                       minlength="8"
                       placeholder="Minimal 8 karakter"
                       class="cp-input @error('password') has-error @enderror">
                <button type="button"
                        onclick="togglePassword('password', this)"
                        class="cp-toggle-btn"
                        aria-label="Toggle password visibility">
                    <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                </button>
            </div>
            <p class="cp-hint">
                <iconify-icon icon="mdi:information-outline"></iconify-icon>
                Minimal 8 karakter, tidak boleh sama dengan kata sandi saat ini.
            </p>
            @error('password')
                <p class="cp-error">
                    <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Konfirmasi Kata Sandi Baru --}}
        <div class="cp-group">
            <label for="password_confirmation" class="cp-label">
                <iconify-icon icon="mdi:lock-check-outline"></iconify-icon>
                Konfirmasi Kata Sandi Baru <span class="required">*</span>
            </label>
            <div class="cp-input-wrap">
                <input type="password"
                       name="password_confirmation"
                       id="password_confirmation"
                       required
                       minlength="8"
                       placeholder="Ulangi kata sandi baru"
                       class="cp-input @error('password_confirmation') has-error @enderror">
                <button type="button"
                        onclick="togglePassword('password_confirmation', this)"
                        class="cp-toggle-btn"
                        aria-label="Toggle password visibility">
                    <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                </button>
            </div>

            {{-- Match Indicator --}}
            <div id="password-match-message" class="cp-match">
                <iconify-icon icon="mdi:check-circle-outline"></iconify-icon>
                <span>Kata sandi cocok.</span>
            </div>

            @error('password_confirmation')
                <p class="cp-error">
                    <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Actions --}}
        <div class="cp-actions">
            <a href="{{ route('customer.account') }}" class="cp-btn cp-btn-outline">
                <iconify-icon icon="mdi:arrow-left"></iconify-icon>
                Kembali
            </a>
            <button type="submit" class="cp-btn cp-btn-gold">
                <iconify-icon icon="mdi:content-save-outline"></iconify-icon>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
    /* ============================================
       TOGGLE PASSWORD (SHOW/HIDE)
       ============================================ */
    function togglePassword(inputId, button) {
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
    window.togglePassword = togglePassword;

    /* ============================================
       MATCH INDICATOR KONFIRMASI PASSWORD
       ============================================ */
    document.addEventListener('DOMContentLoaded', function () {
        const newPassword = document.getElementById('password');
        const confirmPassword = document.getElementById('password_confirmation');
        const matchMessage = document.getElementById('password-match-message');

        if (!newPassword || !confirmPassword || !matchMessage) return;

        function checkMatch() {
            const val1 = newPassword.value;
            const val2 = confirmPassword.value;

            // Reset state
            matchMessage.classList.remove('is-visible', 'is-match', 'is-mismatch');
            confirmPassword.setCustomValidity('');

            // Jika salah satu kosong, sembunyikan
            if (!val1 || !val2) return;

            if (val1 === val2) {
                // Cocok
                matchMessage.classList.add('is-visible', 'is-match');
                matchMessage.innerHTML = `
                    <iconify-icon icon="mdi:check-circle-outline"></iconify-icon>
                    <span>Kata sandi cocok.</span>
                `;
            } else {
                // Tidak cocok
                matchMessage.classList.add('is-visible', 'is-mismatch');
                matchMessage.innerHTML = `
                    <iconify-icon icon="mdi:close-circle-outline"></iconify-icon>
                    <span>Konfirmasi kata sandi tidak cocok.</span>
                `;
                confirmPassword.setCustomValidity('Konfirmasi kata sandi tidak cocok.');
            }
        }

        newPassword.addEventListener('input', checkMatch);
        confirmPassword.addEventListener('input', checkMatch);
    });
</script>
@endpush