@extends('layouts.customer')

@section('title', 'Reset Kata Sandi - Barokah Sport')

@section('content')

<style>
    .reset_container {
        width: 100%;
        max-width: 28vw;
        margin: 4vw auto;
        padding: 2.5vw;
        background: #fff;
        border-radius: 1vw;
        border: 0.1vw solid #e2e8f0;
        box-shadow: 0 0.5vw 2vw rgba(0,0,0,0.05);
        position: relative;
        overflow: hidden;
    }
    .reset_container::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 0.35vw;
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
    }
    .reset_icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 5vw;
        height: 5vw;
        margin: 0.5vw auto 1.5vw;
        background: linear-gradient(135deg, #FDDD57, #ecbc42);
        border-radius: 50%;
        color: rgb(102, 72, 9);
    }
    .reset_icon iconify-icon { font-size: 2.8vw; }
    .reset_title {
        font-size: 1.6vw;
        font-weight: 800;
        color: #0f172a;
        text-align: center;
        margin-bottom: 0.4vw;
        text-transform: uppercase;
    }
    .reset_subtitle {
        font-size: 0.82vw;
        color: #94a3b8;
        text-align: center;
        margin-bottom: 2vw;
        line-height: 1.5;
    }
    .reset_subtitle strong { color: #0f172a; }
    .form_group { margin-bottom: 1.2vw; }
    .form_group label {
        display: flex;
        align-items: center;
        gap: 0.35vw;
        font-size: 0.78vw;
        font-weight: 600;
        color: #334155;
        margin-bottom: 0.4vw;
    }
    .form_group label iconify-icon { color: #ecbc42; font-size: 0.95vw; }
    .form_group .required { color: #dc2626; }
    .password-wrapper { position: relative; width: 100%; }
    .password-wrapper input {
        width: 100%;
        padding: 0.75vw 3vw 0.75vw 1vw;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.5vw;
        font-size: 0.85vw;
        color: #0f172a;
        background: #f8fafc;
        transition: all 0.2s;
        font-family: inherit;
        outline: none;
    }
    .password-wrapper input:focus {
        border-color: #ecbc42;
        background: #fff;
        box-shadow: 0 0 0 0.25vw rgba(236, 188, 66, 0.15);
    }
    .toggle-pass {
        position: absolute;
        right: 0.8vw;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 0.2vw;
    }
    .toggle-pass:hover { color: #ecbc42; }
    .toggle-pass iconify-icon { font-size: 1.1vw; }
    .input_error { font-size: 0.7vw; color: #dc2626; margin-top: 0.3vw; }
    .btn_submit {
        width: 100%;
        padding: 0.9vw;
        background: linear-gradient(90deg, #FDDD57, #ecbc42, #FDDD57);
        color: rgb(102, 72, 9);
        border: none;
        border-radius: 0.6vw;
        font-size: 0.88vw;
        font-weight: 700;
        cursor: pointer;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.6vw;
        font-family: inherit;
        box-shadow: 0 0.15vw 0.5vw rgba(236, 188, 66, 0.3);
        transition: all 0.2s;
    }
    .btn_submit:hover:not(:disabled) {
        transform: translateY(-0.1vw);
        box-shadow: 0 0.35vw 1.2vw rgba(236, 188, 66, 0.5);
    }

    @media (max-width: 480px) {
        .reset_container { max-width: 92vw; padding: 6vw 5vw; border-radius: 4vw; }
        .reset_container::before { height: 1vw; }
        .reset_icon { width: 18vw; height: 18vw; margin: 2vw auto 5vw; }
        .reset_icon iconify-icon { font-size: 9vw; }
        .reset_title { font-size: 6vw; }
        .reset_subtitle { font-size: 3.5vw; margin-bottom: 6vw; }
        .form_group { margin-bottom: 4.5vw; }
        .form_group label { font-size: 3.5vw; gap: 1.2vw; margin-bottom: 1.5vw; }
        .form_group label iconify-icon { font-size: 3.8vw; }
        .password-wrapper input { padding: 3vw 10vw 3vw 3.5vw; font-size: 4vw; border-radius: 2.5vw; }
        .toggle-pass { right: 2.5vw; }
        .toggle-pass iconify-icon { font-size: 4.2vw; }
        .input_error { font-size: 2.8vw; margin-top: 1vw; }
        .btn_submit { padding: 3.5vw; font-size: 4.5vw; border-radius: 2.5vw; }
    }
</style>

<div class="reset_container">
    <div class="reset_icon">
        <iconify-icon icon="mdi:lock-check-outline"></iconify-icon>
    </div>

    <h1 class="reset_title">Kata Sandi Baru</h1>
    <p class="reset_subtitle">
        Buat kata sandi baru untuk akun <strong>{{ $user->name }}</strong>
    </p>

    <form action="{{ route('customer.reset-password.process', $token) }}" method="POST" id="reset-form">
        @csrf

        <div class="form_group">
            <label for="password">
                <iconify-icon icon="mdi:lock-outline"></iconify-icon>
                Kata Sandi Baru <span class="required">*</span>
            </label>
            <div class="password-wrapper">
                <input type="password"
                       name="password"
                       id="password"
                       placeholder="Minimal 8 karakter"
                       minlength="8"
                       required
                       autofocus>
                <button type="button" class="toggle-pass" onclick="togglePass('password', this)">
                    <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                </button>
            </div>
            @error('password')
                <p class="input_error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form_group">
            <label for="password_confirmation">
                <iconify-icon icon="mdi:lock-check-outline"></iconify-icon>
                Konfirmasi Kata Sandi <span class="required">*</span>
            </label>
            <div class="password-wrapper">
                <input type="password"
                       name="password_confirmation"
                       id="password_confirmation"
                       placeholder="Ulangi kata sandi"
                       minlength="8"
                       required>
                <button type="button" class="toggle-pass" onclick="togglePass('password_confirmation', this)">
                    <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                </button>
            </div>
        </div>

        <button type="submit" class="btn_submit" id="submit-btn">
            <iconify-icon icon="mdi:check"></iconify-icon>
            <span>Simpan Kata Sandi</span>
        </button>
    </form>
</div>

<script>
function togglePass(id, btn) {
    const input = document.getElementById(id);
    const icon = btn.querySelector('iconify-icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.setAttribute('icon', 'mdi:eye-off-outline');
    } else {
        input.type = 'password';
        icon.setAttribute('icon', 'mdi:eye-outline');
    }
}

document.getElementById('reset-form').addEventListener('submit', function () {
    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.querySelector('span').textContent = 'Menyimpan...';
});
</script>

@endsection