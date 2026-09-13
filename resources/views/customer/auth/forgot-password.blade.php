@extends('layouts.customer')

@section('title', 'Lupa Kata Sandi - Barokah Sport')

@section('content')

<style>
    .forgot_container {
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
    .forgot_container::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 0.35vw;
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
    }
    .forgot_icon {
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
    .forgot_icon iconify-icon { font-size: 2.8vw; }
    .forgot_title {
        font-size: 1.6vw;
        font-weight: 800;
        color: #0f172a;
        text-align: center;
        margin-bottom: 0.4vw;
        text-transform: uppercase;
    }
    .forgot_subtitle {
        font-size: 0.82vw;
        color: #94a3b8;
        text-align: center;
        margin-bottom: 2vw;
        line-height: 1.5;
    }
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
    .form_group input {
        width: 100%;
        padding: 0.75vw 1vw;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.5vw;
        font-size: 0.85vw;
        color: #0f172a;
        background: #f8fafc;
        transition: all 0.2s;
        font-family: inherit;
        outline: none;
    }
    .form_group input:focus {
        border-color: #ecbc42;
        background: #fff;
        box-shadow: 0 0 0 0.25vw rgba(236, 188, 66, 0.15);
    }
    .input_error {
        font-size: 0.7vw;
        color: #dc2626;
        margin-top: 0.3vw;
    }
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
    .btn_submit:disabled { opacity: 0.7; cursor: not-allowed; }
    .info_box {
        display: flex;
        gap: 0.6vw;
        padding: 0.9vw 1vw;
        background: #fffbeb;
        border: 0.1vw solid #fde68a;
        border-radius: 0.5vw;
        font-size: 0.75vw;
        color: #92400e;
        line-height: 1.5;
        margin-bottom: 1.5vw;
    }
    .info_box iconify-icon { font-size: 1.1vw; flex-shrink: 0; margin-top: 0.1vw; }
    .back_link {
        display: block;
        text-align: center;
        margin-top: 1.5vw;
        font-size: 0.78vw;
        color: #64748b;
        text-decoration: none;
    }
    .back_link:hover { color: #ecbc42; }

    @media (max-width: 480px) {
        .forgot_container { max-width: 92vw; padding: 6vw 5vw; border-radius: 4vw; }
        .forgot_container::before { height: 1vw; }
        .forgot_icon { width: 18vw; height: 18vw; margin: 2vw auto 5vw; }
        .forgot_icon iconify-icon { font-size: 9vw; }
        .forgot_title { font-size: 6vw; }
        .forgot_subtitle { font-size: 3.5vw; margin-bottom: 6vw; }
        .form_group { margin-bottom: 4.5vw; }
        .form_group label { font-size: 3.5vw; gap: 1.2vw; margin-bottom: 1.5vw; }
        .form_group label iconify-icon { font-size: 3.8vw; }
        .form_group input { padding: 3vw 3.5vw; font-size: 4vw; border-radius: 2.5vw; }
        .input_error { font-size: 2.8vw; margin-top: 1vw; }
        .btn_submit { padding: 3.5vw; font-size: 4.5vw; border-radius: 2.5vw; }
        .info_box { font-size: 3vw; padding: 3vw 3.5vw; border-radius: 2.5vw; }
        .info_box iconify-icon { font-size: 4vw; }
        .back_link { font-size: 3.5vw; margin-top: 5vw; }
    }
</style>

<div class="forgot_container">
    <div class="forgot_icon">
        <iconify-icon icon="mdi:lock-reset"></iconify-icon>
    </div>

    <h1 class="forgot_title">Lupa Kata Sandi?</h1>
    <p class="forgot_subtitle">
        Masukkan nomor WhatsApp yang terdaftar. Admin akan memverifikasi permintaan Anda.
    </p>

    <div class="info_box">
        <iconify-icon icon="mdi:information-outline"></iconify-icon>
        <span>
            Proses verifikasi dilakukan oleh admin. Setelah disetujui, Anda akan mendapat link reset password.
        </span>
    </div>

    @if(session('error'))
        <div class="input_error" style="text-align:center; margin-bottom:1vw;">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('customer.forgot-password.submit') }}" method="POST" id="forgot-form">
        @csrf

        <div class="form_group">
            <label for="phone">
                <iconify-icon icon="mdi:whatsapp"></iconify-icon>
                Nomor WhatsApp <span class="required">*</span>
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

        <button type="submit" class="btn_submit" id="submit-btn">
            <iconify-icon icon="mdi:send"></iconify-icon>
            <span>Kirim Permintaan</span>
        </button>
    </form>

    <a href="{{ route('customer.login') }}" class="back_link">
        ← Kembali ke Halaman Masuk
    </a>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('forgot-form');
    const btn = document.getElementById('submit-btn');

    form.addEventListener('submit', function () {
        btn.disabled = true;
        btn.querySelector('span').textContent = 'Mengirim...';
    });
});
</script>

@endsection