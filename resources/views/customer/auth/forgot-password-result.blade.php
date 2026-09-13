@extends('layouts.customer')

@section('title', 'Status Permintaan - Barokah Sport')

@section('content')

<style>
    .result_container {
        width: 100%;
        max-width: 28vw;
        margin: 4vw auto;
        padding: 3vw 2.5vw;
        background: #fff;
        border-radius: 1vw;
        border: 0.1vw solid #e2e8f0;
        box-shadow: 0 0.5vw 2vw rgba(0,0,0,0.05);
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .result_container::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 0.35vw;
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
    }
    .result_icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 6vw;
        height: 6vw;
        margin: 0.5vw auto 1.5vw;
        border-radius: 50%;
    }
    .result_icon iconify-icon { font-size: 3.5vw; }
    .result_icon.success { background: #ecfdf5; color: #10b981; }
    .result_icon.warning { background: #fffbeb; color: #f59e0b; }
    .result_icon.error { background: #fef2f2; color: #dc2626; }
    .result_title {
        font-size: 1.5vw;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 0.6vw;
    }
    .result_message {
        font-size: 0.85vw;
        color: #64748b;
        line-height: 1.6;
        margin-bottom: 1.5vw;
    }
    .result_message strong { color: #0f172a; }
    .result_steps {
        text-align: left;
        background: #f8fafc;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.6vw;
        padding: 1vw 1.2vw;
        margin-bottom: 2vw;
        font-size: 0.8vw;
        color: #475569;
        line-height: 1.8;
    }
    .result_steps strong { color: #0f172a; }
    .result_actions {
        display: flex;
        gap: 0.8vw;
        justify-content: center;
        flex-wrap: wrap;
    }
    .btn_action {
        padding: 0.75vw 1.5vw;
        border-radius: 0.5vw;
        font-size: 0.82vw;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.4vw;
        transition: all 0.2s;
        font-family: inherit;
        border: none;
        cursor: pointer;
    }
    .btn_primary {
        background: linear-gradient(90deg, #FDDD57, #ecbc42, #FDDD57);
        color: rgb(102, 72, 9);
        box-shadow: 0 0.15vw 0.5vw rgba(236, 188, 66, 0.3);
    }
    .btn_primary:hover {
        transform: translateY(-0.1vw);
        box-shadow: 0 0.35vw 1.2vw rgba(236, 188, 66, 0.5);
    }
    .btn_outline {
        background: #fff;
        color: #64748b;
        border: 0.1vw solid #e2e8f0;
    }
    .btn_outline:hover {
        border-color: #ecbc42;
        color: #ecbc42;
    }
    .btn_whatsapp {
        background: linear-gradient(135deg, #25D366, #128C7E);
        color: #fff;
        box-shadow: 0 0.15vw 0.5vw rgba(37, 211, 102, 0.3);
        width: 100%;
        justify-content: center;
        padding: 0.9vw 1.5vw;
        font-size: 0.88vw;
        margin-bottom: 0.8vw;
    }
    .btn_whatsapp:hover {
        transform: translateY(-0.1vw);
        box-shadow: 0 0.35vw 1.2vw rgba(37, 211, 102, 0.5);
    }
    .btn_whatsapp iconify-icon { font-size: 1.2vw; }

    @media (max-width: 480px) {
        .result_container { max-width: 92vw; padding: 6vw 5vw; border-radius: 4vw; }
        .result_container::before { height: 1vw; }
        .result_icon { width: 20vw; height: 20vw; margin: 2vw auto 5vw; }
        .result_icon iconify-icon { font-size: 11vw; }
        .result_title { font-size: 5.5vw; }
        .result_message { font-size: 3.5vw; margin-bottom: 5vw; }
        .result_steps { font-size: 3.2vw; padding: 4vw; border-radius: 2.5vw; margin-bottom: 6vw; }
        .result_actions { flex-direction: column; gap: 3vw; }
        .btn_action { padding: 3vw 5vw; font-size: 4vw; border-radius: 2.5vw; justify-content: center; }
        .btn_whatsapp { padding: 3.5vw 5vw; font-size: 4vw; margin-bottom: 3vw; }
        .btn_whatsapp iconify-icon { font-size: 5vw; }
    }
</style>

<div class="result_container">

    @if(session('not_registered'))
        {{-- Nomor tidak terdaftar --}}
        <div class="result_icon error">
            <iconify-icon icon="mdi:account-off-outline"></iconify-icon>
        </div>
        <h2 class="result_title">Nomor Tidak Terdaftar</h2>
        <p class="result_message">
            Nomor WhatsApp <strong>{{ session('phone') }}</strong> tidak terdaftar di sistem kami.
            <br><br>
            Pastikan Anda sudah mendaftar atau gunakan nomor yang benar.
        </p>

    @elseif(session('already_pending'))
        {{-- Sudah ada request pending --}}
        <div class="result_icon warning">
            <iconify-icon icon="mdi:clock-outline"></iconify-icon>
        </div>
        <h2 class="result_title">Permintaan Sedang Diproses</h2>
        <p class="result_message">
            Anda sudah memiliki permintaan reset password yang <strong>sedang menunggu verifikasi admin</strong>.
            <br><br>
            Silakan hubungi admin via WhatsApp untuk mempercepat proses verifikasi.
        </p>

    @elseif(session('inactive_account'))
        {{-- Akun tidak aktif --}}
        <div class="result_icon error">
            <iconify-icon icon="mdi:account-cancel-outline"></iconify-icon>
        </div>
        <h2 class="result_title">Akun Tidak Aktif</h2>
        <p class="result_message">
            Akun Anda sedang tidak aktif. Silakan hubungi admin untuk informasi lebih lanjut.
        </p>

    @elseif(session('request_created'))
        {{-- Request berhasil dibuat --}}
        <div class="result_icon success">
            <iconify-icon icon="mdi:check-circle-outline"></iconify-icon>
        </div>
        <h2 class="result_title">Permintaan Terkirim</h2>
        <p class="result_message">
            Permintaan reset password dari nomor <strong>{{ session('phone') }}</strong>
            telah dikirim ke admin.
        </p>

        <div class="result_steps">
            <strong>📋 Langkah selanjutnya:</strong><br>
            1. Klik tombol WhatsApp di bawah<br>
            2. Kirim pesan ke admin<br>
            3. Tunggu verifikasi admin<br>
            4. Terima link reset password
        </div>

    @else
        {{-- Fallback --}}
        <div class="result_icon warning">
            <iconify-icon icon="mdi:information-outline"></iconify-icon>
        </div>
        <h2 class="result_title">Halaman Status</h2>
        <p class="result_message">
            Tidak ada permintaan aktif saat ini.
        </p>
    @endif

    {{-- ============================================ --}}
    {{-- 🔥 TOMBOL WHATSAPP KE ADMIN --}}
    {{-- Muncul kalau ada session phone (artinya user submit form) --}}
    {{-- ============================================ --}}
    @if(session('phone'))
        @php
            // 🔥 Normalisasi nomor admin (hilangkan karakter non-digit)
            $adminPhone = preg_replace('/[^0-9]/', '', $setting->whatsapp ?? '08123516518');
            
            // 🔥 Convert ke format internasional (62xxx) untuk wa.me
            if (str_starts_with($adminPhone, '0')) {
                $adminPhone = '62' . substr($adminPhone, 1);
            } elseif (str_starts_with($adminPhone, '8')) {
                $adminPhone = '62' . $adminPhone;
            }
            
            // 🔥 Ambil nomor customer dari session
            $customerPhone = session('phone');
            
            // 🔥 Pesan default WhatsApp
            $waMessage = "Halo Admin Barokah Sport,\n\n"
                . "Saya ingin reset password akun saya.\n\n"
                . "📱 Nomor HP saya: *{$customerPhone}*\n"
                . "🔐 Keperluan: Reset Password\n\n"
                . "Mohon dibantu verifikasi ya. Terima kasih. 🙏";
            
            // 🔥 Build WA link
            $waLink = 'https://wa.me/' . $adminPhone . '?text=' . urlencode($waMessage);
        @endphp

        <a href="{{ $waLink }}" 
           target="_blank" 
           rel="noopener noreferrer"
           class="btn_action btn_whatsapp">
            <iconify-icon icon="mdi:whatsapp"></iconify-icon>
            <span>Hubungi Admin via WhatsApp</span>
        </a>
    @endif

    {{-- ============================================ --}}
    {{-- TOMBOL AKSI LAINNYA --}}
    {{-- ============================================ --}}
    <div class="result_actions">
        <a href="{{ route('customer.login') }}" class="btn_action btn_outline">
            <iconify-icon icon="mdi:arrow-left"></iconify-icon>
            Kembali ke Login
        </a>
        <a href="{{ route('customer.forgot-password') }}" class="btn_action btn_primary">
            <iconify-icon icon="mdi:refresh"></iconify-icon>
            Ajukan Ulang
        </a>
    </div>
</div>

@endsection