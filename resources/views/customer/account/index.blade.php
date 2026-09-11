@extends('layouts.account')

@section('title', 'Profil Saya - Barokah Sport')
@section('page-title', 'Profil Saya')
@section('page-subtitle', 'Lihat dan kelola informasi akun Anda.')

@section('account-content')

<style>
    /* ============================================
       PROFILE PAGE STYLES
       ============================================ */
    .profile-wrapper {
        display: flex;
        flex-direction: column;
        gap: 1.5vw;
        margin-top:1vw;
    }

    /* Alert Error */
    .profile-alert {
        padding: 1vw 1.2vw;
        border-radius: 0.6vw;
        border: 0.1vw solid #fecaca;
        background: #fef2f2;
        color: #b91c1c;
        font-size: 0.85vw;
    }

    /* Form */
    .profile-form {
        display: flex;
        flex-direction: column;
        gap: 1.5vw;
    }

    /* Avatar Section */
    .profile-avatar-section {
        display: flex;
        align-items: center;
        gap: 1.5vw;
    }

    .avatar-wrap {
        position: relative;
        flex-shrink: 0;
    }

    .avatar-preview {
        width: 6vw;
        height: 6vw;
        border-radius: 50%;
        object-fit: cover;
        ring: 0.15vw solid #e5e7eb;
        box-shadow: 0 0 0 0.15vw #e5e7eb;
    }

    .avatar-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 6vw;
        height: 6vw;
        border-radius: 50%;
        background: linear-gradient(135deg, #FDDD57 0%, #ecbc42 100%);
        color: rgb(102, 72, 9);
        box-shadow: 0 0 0 0.15vw #e5e7eb;
    }

    .avatar-placeholder iconify-icon {
        font-size: 2.4vw;
    }

    .avatar-edit-btn {
        position: absolute;
        bottom: 0;
        right: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2vw;
        height: 2vw;
        border-radius: 50%;
        background: linear-gradient(135deg, #FDDD57 0%, #ecbc42 100%);
        color: rgb(102, 72, 9);
        box-shadow: 0 0 0 0.15vw #ffffff;
        cursor: pointer;
        transition: transform 0.2s ease;
    }

    .avatar-edit-btn:hover {
        transform: scale(1.08);
    }

    .avatar-edit-btn iconify-icon {
        font-size: 1vw;
    }

    .avatar-info {
        flex: 1;
        min-width: 0;
    }

    .avatar-info-title {
        font-size: 0.95vw;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 0.2vw;
    }

    .avatar-info-desc {
        font-size: 0.75vw;
        color: #94a3b8;
        line-height: 1.5;
    }

    /* Form Group */
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.4vw;
    }

    .form-label {
        font-size: 0.85vw;
        font-weight: 600;
        color: #334155;
    }

    .form-label .required {
        color: #dc2626;
    }

    .form-input {
        width: 100%;
        padding: 0.85vw 1.1vw;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.6vw;
        font-size: 0.85vw;
        color: #0f172a;
        background: #ffffff;
        outline: none;
        transition: all 0.2s ease;
        font-family: inherit;
    }

    .form-input::placeholder {
        color: #cbd5e1;
    }

    .form-input:focus {
        border-color: #ecbc42;
        box-shadow: 0 0 0 0.25vw rgba(236, 188, 66, 0.2);
    }

    .form-input.has-error {
        border-color: #fca5a5;
    }

    .form-input.has-error:focus {
        border-color: #dc2626;
        box-shadow: 0 0 0 0.25vw rgba(220, 38, 38, 0.15);
    }

    .form-error {
        font-size: 0.75vw;
        color: #dc2626;
        display: flex;
        align-items: center;
        gap: 0.3vw;
    }

    /* Submit Area */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.8vw;
        padding-top: 1.5vw;
        border-top: 0.1vw solid #f1f5f9;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5vw;
        padding: 0.85vw 1.4vw;
        border-radius: 0.6vw;
        font-size: 0.85vw;
        font-weight: 600;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s ease;
        font-family: inherit;
    }

    .btn-outline {
        background: #ffffff;
        border: 0.1vw solid #e2e8f0;
        color: #475569;
    }

    .btn-outline:hover {
        background: #f8fafc;
    }

    .btn-gold {
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
        color: rgb(102, 72, 9);
        box-shadow: 0 0.15vw 0.5vw rgba(236, 188, 66, 0.3);
    }

    .btn-gold:hover {
        box-shadow: 0 0.3vw 1vw rgba(236, 188, 66, 0.4);
        transform: translateY(-0.1vw);
    }

    .btn-gold:active {
        transform: translateY(0);
    }

    /* Change Password Section */
    .password-section {
        border-top: 0.1vw solid #f1f5f9;
        padding-top: 1.5vw;
    }

    .password-section h3 {
        font-size: 1.1vw;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.4vw;
    }

    .password-section p {
        font-size: 0.85vw;
        color: #64748b;
        margin-bottom: 1vw;
        line-height: 1.6;
    }

    /* ============================================
       RESPONSIVE - TABLET
       ============================================ */
    @media (max-width: 1024px) {
        .profile-wrapper {
            gap: 3vw;
            margin-top:1vw;
        }

        .profile-alert {
            padding: 2.5vw 3vw;
            border-radius: 1.5vw;
            font-size: 2vw;
        }

        .profile-form {
            gap: 3vw;
        }

        .profile-avatar-section {
            gap: 3vw;
            flex-direction: row;
        }

        .avatar-preview,
        .avatar-placeholder {
            width: 14vw;
            height: 14vw;
            box-shadow: 0 0 0 0.3vw #e5e7eb;
        }

        .avatar-placeholder iconify-icon {
            font-size: 5.5vw;
        }

        .avatar-edit-btn {
            width: 5vw;
            height: 5vw;
            box-shadow: 0 0 0 0.3vw #ffffff;
        }

        .avatar-edit-btn iconify-icon {
            font-size: 2.5vw;
        }

        .avatar-info-title {
            font-size: 2.3vw;
            margin-bottom: 0.5vw;
        }

        .avatar-info-desc {
            font-size: 1.8vw;
        }

        .form-group {
            gap: 1vw;
        }

        .form-label {
            font-size: 2.1vw;
        }

        .form-input {
            padding: 2.2vw 3vw;
            border-radius: 1.5vw;
            font-size: 2.2vw;
            border-width: 0.2vw;
        }

        .form-input:focus {
            box-shadow: 0 0 0 0.6vw rgba(236, 188, 66, 0.2);
        }

        .form-error {
            font-size: 1.8vw;
            gap: 0.7vw;
        }

        .form-actions {
            gap: 2vw;
            padding-top: 3vw;
            border-top-width: 0.2vw;
        }

        .btn {
            padding: 2.2vw 3.5vw;
            border-radius: 1.5vw;
            font-size: 2.1vw;
            gap: 1vw;
        }

        .btn-outline {
            border-width: 0.2vw;
        }

        .btn-gold {
            box-shadow: 0 0.4vw 1.5vw rgba(236, 188, 66, 0.3);
        }

        .password-section {
            padding-top: 3vw;
            border-top-width: 0.2vw;
        }

        .password-section h3 {
            font-size: 2.8vw;
            margin-bottom: 1vw;
        }

        .password-section p {
            font-size: 2vw;
            margin-bottom: 2.5vw;
        }
    }

    /* ============================================
       RESPONSIVE - MOBILE
       ============================================ */
    @media (max-width: 480px) {
        .profile-wrapper {
            gap: 4vw;
            margin-top:3vw;
        }

        .profile-alert {
            padding: 3vw 4vw;
            border-radius: 2vw;
            font-size: 3vw;
        }

        .profile-form {
            gap: 4vw;
        }

        /* Avatar Section - Stack Vertikal */
        .profile-avatar-section {
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 3vw;
            padding: 4vw 0;
            background: linear-gradient(135deg, #fffbf0 0%, #fff7e0 100%);
            border-radius: 3vw;
            border: 0.2vw solid #fde68a;
        }

        .avatar-preview,
        .avatar-placeholder {
            width: 22vw;
            height: 22vw;
            box-shadow: 0 0 0 0.4vw #ffffff;
        }

        .avatar-placeholder iconify-icon {
            font-size: 9vw;
        }

        .avatar-edit-btn {
            width: 7vw;
            height: 7vw;
            box-shadow: 0 0 0 0.4vw #ffffff;
        }

        .avatar-edit-btn iconify-icon {
            font-size: 3.8vw;
        }

        .avatar-info {
            width: 100%;
            padding: 0 3vw;
        }

        .avatar-info-title {
            font-size: 3.5vw;
            margin-bottom: 0.8vw;
            color: rgb(102, 72, 9);
        }

        .avatar-info-desc {
            font-size: 2.8vw;
            color: rgb(102, 72, 9);
            opacity: 0.75;
            line-height: 1.5;
        }

        .form-group {
            gap: 1.5vw;
        }

        .form-label {
            font-size: 3.2vw;
        }

        .form-input {
            padding: 3vw 3.5vw;
            border-radius: 2vw;
            font-size: 3.2vw;
            border-width: 0.3vw;
        }

        .form-input:focus {
            box-shadow: 0 0 0 0.9vw rgba(236, 188, 66, 0.2);
        }

        .form-error {
            font-size: 2.7vw;
            gap: 1vw;
        }

        /* Submit Buttons - Full width stack */
        .form-actions {
            flex-direction: column-reverse;
            gap: 2.5vw;
            padding-top: 4vw;
            border-top-width: 0.3vw;
        }

        .btn {
            width: 100%;
            padding: 3.2vw 4vw;
            border-radius: 2.5vw;
            font-size: 3.2vw;
            gap: 1.5vw;
            justify-content: center;
        }

        .btn-outline {
            border-width: 0.3vw;
        }

        .btn-gold {
            box-shadow: 0 0.7vw 2.5vw rgba(236, 188, 66, 0.35);
        }

        .password-section {
            padding-top: 4vw;
            border-top-width: 0.3vw;
        }

        .password-section h3 {
            font-size: 4vw;
            margin-bottom: 1.5vw;
        }

        .password-section p {
            font-size: 3vw;
            margin-bottom: 3.5vw;
        }
    }
</style>

<div class="profile-wrapper">

    @if (session('error'))
        <div class="profile-alert">
            {{ session('error') }}
        </div>
    @endif

    {{-- Edit Profil --}}
    <form action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data" class="profile-form">
        @csrf
        @method('PUT')

        {{-- Avatar --}}
        <div class="profile-avatar-section">
            <div class="avatar-wrap">
                @if ($user->avatar)
                    <img id="avatar-preview" src="{{ Storage::url($user->avatar) }}" alt="Avatar" class="avatar-preview">
                @else
                    <div id="avatar-preview-placeholder" class="avatar-placeholder">
                        <iconify-icon icon="mdi:account"></iconify-icon>
                    </div>
                @endif

                <label class="avatar-edit-btn">
                    <iconify-icon icon="mdi:camera-outline"></iconify-icon>
                    <input type="file" name="avatar" id="avatar-input" accept="image/*" class="hidden" style="display:none;">
                </label>
            </div>

            <div class="avatar-info">
                <div class="avatar-info-title">Foto Profil</div>
                <div class="avatar-info-desc">
                    Format JPG, PNG, atau WEBP. Maks 2MB.
                    @error('avatar')
                        <br><span style="color:#dc2626;">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Name --}}
        <div class="form-group">
            <label class="form-label">Nama Lengkap <span class="required">*</span></label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                   class="form-input @error('name') has-error @enderror"
                   placeholder="Nama lengkap">
            @error('name')
                <p class="form-error">
                    <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Email --}}
        <div class="form-group">
            <label class="form-label">Email <span class="required">*</span></label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                   class="form-input @error('email') has-error @enderror"
                   placeholder="email@contoh.com">
            @error('email')
                <p class="form-error">
                    <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Phone --}}
        <div class="form-group">
            <label class="form-label">Nomor Telepon</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                   class="form-input @error('phone') has-error @enderror"
                   placeholder="081234567890">
            @error('phone')
                <p class="form-error">
                    <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Submit --}}
        <div class="form-actions">
            <button type="reset" class="btn btn-outline">
                <iconify-icon icon="mdi:refresh"></iconify-icon>
                Reset
            </button>
            <button type="submit" class="btn btn-gold">
                <iconify-icon icon="mdi:content-save-outline"></iconify-icon>
                Simpan Perubahan
            </button>
        </div>
    </form>

    {{-- Ganti Kata Sandi --}}
    <div class="password-section">
        <h3>Ganti Kata Sandi</h3>
        <p>Untuk menjaga keamanan akun, ganti kata sandi secara berkala. Klik tombol di bawah untuk melanjutkan.</p>
        <a href="{{ route('customer.password.change') }}" class="btn btn-gold">
            <iconify-icon icon="mdi:lock-outline"></iconify-icon>
            Ganti Kata Sandi
        </a>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var input = document.getElementById('avatar-input');
        if (!input) return;

        input.addEventListener('change', function (e) {
            var file = e.target.files[0];
            if (!file) return;

            // Validasi ukuran file (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file terlalu besar. Maksimal 2MB.');
                input.value = '';
                return;
            }

            var reader = new FileReader();
            reader.onload = function (event) {
                var img = document.getElementById('avatar-preview');
                if (!img) {
                    img = document.createElement('img');
                    img.id = 'avatar-preview';
                    img.className = 'avatar-preview';
                    img.alt = 'Avatar';
                    var placeholder = document.getElementById('avatar-preview-placeholder');
                    var wrapper = placeholder.parentNode;
                    wrapper.replaceChild(img, placeholder);
                }
                img.src = event.target.result;
            };
            reader.readAsDataURL(file);
        });
    });
</script>
@endpush