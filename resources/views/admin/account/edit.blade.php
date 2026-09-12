@extends('layouts.admin')

@section('title', 'Pengaturan Akun')
@section('page-title', 'Pengaturan Akun')

@section('content')

<div class="w-full max-w-4xl mx-auto space-y-6">

    {{-- ============================================ --}}
    {{-- HEADER --}}
    {{-- ============================================ --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <h1 class="text-2xl font-bold flex items-center gap-2.5" style="color: var(--text-1)">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl
                             bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                             shadow-lg shadow-amber-500/20 flex-shrink-0">
                    <iconify-icon icon="mdi:account-cog-outline" class="text-slate-900 text-2xl"></iconify-icon>
                </span>
                Pengaturan Akun
            </h1>
            <p class="text-sm mt-1.5 ml-12" style="color: var(--text-5)">
                Kelola informasi akun dan keamanan Anda.
            </p>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- FLASH MESSAGES --}}
    {{-- ============================================ --}}
    @if (session('success'))
        <div class="flex items-start gap-3 rounded-xl px-4 py-3
                    bg-emerald-500/10 border border-emerald-500/30 text-emerald-400">
            <iconify-icon icon="mdi:check-circle-outline" class="text-xl flex-shrink-0 mt-0.5"></iconify-icon>
            <span class="text-sm">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-start gap-3 rounded-xl px-4 py-3
                    bg-red-500/10 border border-red-500/30 text-red-400">
            <iconify-icon icon="mdi:alert-circle-outline" class="text-xl flex-shrink-0 mt-0.5"></iconify-icon>
            <span class="text-sm">{{ session('error') }}</span>
        </div>
    @endif


    {{-- ============================================ --}}
    {{-- PROFILE CARD --}}
    {{-- ============================================ --}}
    <div class="rounded-2xl border overflow-hidden"
         style="background: var(--bg-card); border-color: var(--border-2)">

        <div class="p-6">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5">

                {{-- Avatar Besar --}}
                <div class="relative flex-shrink-0">
                    <div class="w-20 h-20 rounded-2xl flex items-center justify-center
                                bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                                shadow-xl shadow-amber-500/20 overflow-hidden">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}"
                                 alt="{{ $user->name }}"
                                 class="w-full h-full object-cover">
                        @else
                            <span class="text-3xl font-bold text-slate-900">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </span>
                        @endif
                    </div>
                    <span class="absolute -bottom-1 -right-1
                                 w-6 h-6 rounded-full
                                 bg-emerald-500
                                 flex items-center justify-center
                                 shadow-md shadow-emerald-500/50"
                          style="box-shadow: 0 0 0 3px var(--bg-card);">
                        <iconify-icon icon="mdi:check" class="text-white text-xs"></iconify-icon>
                    </span>
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0 text-center sm:text-left">
                    <h2 class="text-lg font-bold" style="color: var(--text-1);">
                        {{ $user->name }}
                    </h2>
                    <p class="text-sm mt-0.5" style="color: var(--text-5);">
                        {{ $user->email }}
                    </p>
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mt-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border"
                              style="background: rgba(236,188,66,0.1); border-color: rgba(236,188,66,0.3); color: #ecbc42;">
                            <iconify-icon icon="mdi:shield-account-outline"></iconify-icon>
                            {{ ucfirst($user->role ?? 'Administrator') }}
                        </span>
                        <span class="text-[10px]" style="color: var(--text-5);">
                            Bergabung {{ $user->created_at?->format('d M Y') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- FORM 1: INFORMASI AKUN (Nama + Email) --}}
    {{-- ============================================ --}}
    <form action="{{ route('admin.account.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="rounded-2xl border overflow-hidden"
             style="background: var(--bg-card); border-color: var(--border-2)">

            <div class="px-5 py-4 border-b flex items-center gap-2"
                 style="background: var(--bg-input); border-color: var(--border-2)">
                <iconify-icon icon="mdi:account-edit-outline" class="text-[#ecbc42] text-base"></iconify-icon>
                <h2 class="font-bold text-sm flex-1" style="color: var(--text-1)">Informasi Akun</h2>
                <span class="text-[10px] font-mono px-2 py-0.5 rounded-full"
                      style="background: rgba(236,188,66,0.1); color: #ecbc42;">
                    Nama & Email
                </span>
            </div>

            <div class="p-5 space-y-5">

                {{-- Nama --}}
                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:account-outline" class="text-[#ecbc42]"></iconify-icon>
                        Nama Lengkap <span class="text-red-400">*</span>
                    </label>
                    <input type="text"
                           name="name"
                           value="{{ old('name', $user->name) }}"
                           required
                           class="form-input"
                           placeholder="Nama lengkap Anda">
                    @error('name')
                        <p class="text-[10px] mt-1 text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:email-outline" class="text-[#ecbc42]"></iconify-icon>
                        Email <span class="text-red-400">*</span>
                    </label>
                    <input type="email"
                           name="email"
                           value="{{ old('email', $user->email) }}"
                           required
                           class="form-input"
                           placeholder="email@contoh.com">
                    <p class="text-[10px] mt-1.5 flex items-center gap-1" style="color: var(--text-5);">
                        <iconify-icon icon="mdi:information-outline"></iconify-icon>
                        Email digunakan untuk login dan notifikasi.
                    </p>
                    @error('email')
                        <p class="text-[10px] mt-1 text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="px-5 py-4 border-t flex justify-end"
                 style="border-color: var(--border-2); background: var(--bg-input);">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg
                               text-sm font-bold transition-all active:scale-95
                               bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                               text-slate-900
                               shadow-lg shadow-amber-500/20
                               hover:shadow-xl hover:shadow-amber-500/40">
                    <iconify-icon icon="mdi:content-save-outline" class="text-base"></iconify-icon>
                    Simpan Informasi
                </button>
            </div>
        </div>
    </form>


    {{-- ============================================ --}}
    {{-- FORM 2: UBAH PASSWORD --}}
    {{-- ============================================ --}}
    <form action="{{ route('admin.account.password') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="rounded-2xl border overflow-hidden"
             style="background: var(--bg-card); border-color: var(--border-2)">

            <div class="px-5 py-4 border-b flex items-center gap-2"
                 style="background: var(--bg-input); border-color: var(--border-2)">
                <iconify-icon icon="mdi:lock-reset" class="text-[#ecbc42] text-base"></iconify-icon>
                <h2 class="font-bold text-sm flex-1" style="color: var(--text-1)">Ubah Password</h2>
                <span class="text-[10px] font-mono px-2 py-0.5 rounded-full"
                      style="background: rgba(248,113,113,0.1); color: #f87171;">
                    <iconify-icon icon="mdi:shield-key-outline" class="inline"></iconify-icon>
                    Keamanan
                </span>
            </div>

            <div class="p-5 space-y-5">

                {{-- Warning Info --}}
                <div class="flex items-start gap-3 rounded-xl px-4 py-3
                            bg-blue-500/10 border border-blue-500/30">
                    <iconify-icon icon="mdi:information-outline" class="text-blue-400 text-lg flex-shrink-0 mt-0.5"></iconify-icon>
                    <div class="text-xs" style="color: #93c5fd;">
                        <p class="font-bold mb-0.5">Tips Keamanan</p>
                        <p class="opacity-90">Gunakan password yang kuat dengan kombinasi huruf besar, huruf kecil, angka, dan simbol. Minimal 8 karakter.</p>
                    </div>
                </div>

                {{-- Password Saat Ini --}}
                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:lock-outline" class="text-[#ecbc42]"></iconify-icon>
                        Password Saat Ini <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <input type="password"
                               name="current_password"
                               id="current_password"
                               required
                               class="form-input"
                               style="padding-right: 3rem;"
                               placeholder="Masukkan password saat ini">
                        <button type="button"
                                onclick="togglePassword('current_password', this)"
                                class="absolute inset-y-0 right-0 flex items-center pr-3
                                       transition-colors"
                                style="color: var(--text-5);"
                                onmouseover="this.style.color='#FDDD57'"
                                onmouseout="this.style.color='var(--text-5)'">
                            <iconify-icon icon="mdi:eye-outline" class="text-lg"></iconify-icon>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="text-[10px] mt-1 text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Password Baru --}}
                    <div>
                        <label class="form-label">
                            <iconify-icon icon="mdi:lock-plus-outline" class="text-[#ecbc42]"></iconify-icon>
                            Password Baru <span class="text-red-400">*</span>
                        </label>
                        <div class="relative">
                            <input type="password"
                                   name="password"
                                   id="password"
                                   required
                                   class="form-input"
                                   style="padding-right: 3rem;"
                                   placeholder="Minimal 8 karakter">
                            <button type="button"
                                    onclick="togglePassword('password', this)"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3
                                           transition-colors"
                                    style="color: var(--text-5);"
                                    onmouseover="this.style.color='#FDDD57'"
                                    onmouseout="this.style.color='var(--text-5)'">
                                <iconify-icon icon="mdi:eye-outline" class="text-lg"></iconify-icon>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-[10px] mt-1 text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div>
                        <label class="form-label">
                            <iconify-icon icon="mdi:lock-check-outline" class="text-[#ecbc42]"></iconify-icon>
                            Konfirmasi Password <span class="text-red-400">*</span>
                        </label>
                        <div class="relative">
                            <input type="password"
                                   name="password_confirmation"
                                   id="password_confirmation"
                                   required
                                   class="form-input"
                                   style="padding-right: 3rem;"
                                   placeholder="Ulangi password baru">
                            <button type="button"
                                    onclick="togglePassword('password_confirmation', this)"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3
                                           transition-colors"
                                    style="color: var(--text-5);"
                                    onmouseover="this.style.color='#FDDD57'"
                                    onmouseout="this.style.color='var(--text-5)'">
                                <iconify-icon icon="mdi:eye-outline" class="text-lg"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Password Strength Indicator --}}
                <div id="password-strength" class="hidden">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5);">
                            Kekuatan Password
                        </span>
                        <span id="password-strength-label" class="text-[10px] font-bold"></span>
                    </div>
                    <div class="h-1.5 rounded-full overflow-hidden" style="background: var(--bg-input);">
                        <div id="password-strength-bar" class="h-full rounded-full transition-all duration-300"
                             style="width: 0%; background: #f87171;"></div>
                    </div>
                </div>
            </div>

            <div class="px-5 py-4 border-t flex justify-end"
                 style="border-color: var(--border-2); background: var(--bg-input);">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg
                               text-sm font-bold transition-all active:scale-95
                               bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                               text-slate-900
                               shadow-lg shadow-amber-500/20
                               hover:shadow-xl hover:shadow-amber-500/40">
                    <iconify-icon icon="mdi:lock-reset" class="text-base"></iconify-icon>
                    Ubah Password
                </button>
            </div>
        </div>
    </form>

</div>


{{-- ============================================ --}}
{{-- STYLES --}}
{{-- ============================================ --}}
<style>
    .form-input {
        width: 100%;
        padding: 0.7rem 1rem;
        background: var(--bg-input);
        border: 1px solid var(--border-2);
        border-radius: 0.65rem;
        font-size: 0.875rem;
        color: var(--text-1);
        transition: all 0.2s ease;
        font-family: inherit;
        outline: none;
    }
    .form-input:focus {
        border-color: #ecbc42;
        box-shadow: 0 0 0 3px rgba(236, 188, 66, 0.15);
    }
    .form-input::placeholder {
        color: var(--text-6);
    }
    .form-label {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-3);
        margin-bottom: 0.5rem;
    }
</style>


{{-- ============================================ --}}
{{-- SCRIPTS --}}
{{-- ============================================ --}}
@push('scripts')
<script>
// ============================================
// TOGGLE PASSWORD VISIBILITY
// ============================================
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

// ============================================
// PASSWORD STRENGTH METER
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const strengthWrapper = document.getElementById('password-strength');
    const strengthBar = document.getElementById('password-strength-bar');
    const strengthLabel = document.getElementById('password-strength-label');

    if (!passwordInput) return;

    passwordInput.addEventListener('input', function() {
        const val = this.value;
        if (val.length === 0) {
            strengthWrapper.classList.add('hidden');
            return;
        }
        strengthWrapper.classList.remove('hidden');

        let score = 0;
        if (val.length >= 8) score++;
        if (/[a-z]/.test(val) && /[A-Z]/.test(val)) score++;
        if (/\d/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        let width, color, label;
        if (score <= 1) {
            width = '25%'; color = '#f87171'; label = 'Lemah';
        } else if (score === 2) {
            width = '50%'; color = '#fbbf24'; label = 'Sedang';
        } else if (score === 3) {
            width = '75%'; color = '#60a5fa'; label = 'Kuat';
        } else {
            width = '100%'; color = '#34d399'; label = 'Sangat Kuat';
        }

        strengthBar.style.width = width;
        strengthBar.style.background = color;
        strengthLabel.textContent = label;
        strengthLabel.style.color = color;
    });
});
</script>
@endpush

@endsection