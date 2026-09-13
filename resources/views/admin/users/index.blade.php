@extends('layouts.admin')

@section('title', 'Pelanggan')
@section('page-title', 'Pelanggan')

@section('content')

<div class="w-full space-y-6" x-data="{ tab: '{{ request('tab', 'customers') }}' }">

    {{-- ============================================ --}}
    {{-- HEADER --}}
    {{-- ============================================ --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <h1 class="text-2xl font-bold flex items-center gap-2.5" style="color: var(--text-1)">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl
                             bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                             shadow-lg shadow-amber-500/20 flex-shrink-0">
                    <iconify-icon icon="mdi:account-multiple-outline" class="text-slate-900 text-2xl"></iconify-icon>
                </span>
                Pelanggan
            </h1>
            <p class="text-sm mt-1.5 ml-12" style="color: var(--text-5)">
                Kelola pelanggan dan permintaan reset password.
            </p>
        </div>
    </div>

    @if(session('reject_wa_link'))
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl"
            style="background: var(--alert-reject-bg);
                    border: 1px solid var(--alert-reject-border);
                    color: var(--alert-reject-text);">
            <iconify-icon icon="mdi:information-outline" class="text-xl flex-shrink-0 mt-0.5"></iconify-icon>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold">Permintaan Ditolak</p>
                <p class="text-xs opacity-90 mt-0.5">
                    Permintaan reset password untuk 
                    <strong style="color: var(--alert-reject-text-strong);">
                        {{ session('reject_customer_name') }}
                    </strong>
                    ({{ session('reject_customer_phone') }}) telah ditolak.
                </p>

                <div class="mt-3 p-3 rounded-lg"
                    style="background: var(--alert-reject-bg-inner);
                            border: 1px solid var(--alert-reject-border);">
                    <p class="text-[11px] font-bold mb-2"
                    style="color: var(--alert-reject-text-strong);">
                        📱 Kirim notifikasi penolakan ke customer via WhatsApp:
                    </p>
                    <div class="flex items-center gap-2 flex-wrap">
                        <a href="{{ session('reject_wa_link') }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        id="reject-wa-btn"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg
                                text-xs font-bold transition-all
                                bg-[#25D366] text-white hover:bg-[#128C7E]
                                shadow-md shadow-emerald-500/30">
                            <iconify-icon icon="mdi:whatsapp" class="text-base"></iconify-icon>
                            Buka WhatsApp Customer
                        </a>
                    </div>
                    <p class="text-[10px] mt-2"
                    style="color: var(--alert-reject-text); opacity: 0.75;">
                        ⚡ WhatsApp akan terbuka otomatis. Tinggal klik "Kirim" untuk mengabari customer.
                    </p>
                </div>
            </div>
        </div>
    @endif


    {{-- ============================================ --}}
    {{-- FLASH MESSAGES --}}
    {{-- ============================================ --}}
    @if(session('success'))
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl"
            style="background: var(--alert-success-bg);
                    border: 1px solid var(--alert-success-border);
                    color: var(--alert-success-text);">
            <iconify-icon icon="mdi:check-circle-outline" class="text-xl flex-shrink-0 mt-0.5"></iconify-icon>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold">Berhasil!</p>
                <p class="text-xs opacity-90 mt-0.5">{{ session('success') }}</p>

                {{-- 🔥 Kalau ada reset_link, tampilkan link + copy button --}}
                @if(session('reset_link'))
                    <div class="mt-3 p-3 rounded-lg"
                        style="background: var(--alert-success-bg-inner);
                                border: 1px solid var(--alert-success-border);">
                        <p class="text-[11px] font-bold mb-2"
                        style="color: var(--alert-success-text-strong);">
                            🔗 Link Reset Password untuk <strong>{{ session('reset_user_name') }}</strong>
                            ({{ session('reset_phone') }}):
                        </p>
                        <div class="flex items-center gap-2">
                            <input type="text"
                                value="{{ session('reset_link') }}"
                                readonly
                                id="reset-link-input"
                                class="flex-1 px-3 py-2 rounded-lg text-xs font-mono"
                                style="background: rgba(0,0,0,0.15);
                                        border: 1px solid var(--alert-success-border);
                                        color: var(--alert-success-text-strong);"
                                onclick="this.select()">
                            <button type="button"
                                    onclick="copyResetLink()"
                                    class="px-3 py-2 rounded-lg text-xs font-bold
                                        bg-emerald-500 text-white
                                        hover:bg-emerald-600 transition-colors
                                        flex items-center gap-1.5">
                                <iconify-icon icon="mdi:content-copy"></iconify-icon>
                                Copy
                            </button>
                        </div>
                        <p class="text-[10px] mt-2 opacity-80"
                        style="color: var(--alert-success-text);">
                            ⏰ Link berlaku 24 jam. Kirim ke customer via WhatsApp.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl
                    bg-red-500/10 border border-red-500/30 text-red-400">
            <iconify-icon icon="mdi:alert-circle-outline" class="text-xl flex-shrink-0 mt-0.5"></iconify-icon>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold">Gagal!</p>
                <p class="text-xs opacity-80 mt-0.5">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- TABS --}}
    {{-- ============================================ --}}
    <div class="flex items-center gap-1 p-1 rounded-xl border overflow-x-auto"
         style="background: var(--bg-input); border-color: var(--border-2);">
        
        {{-- Tab: Customers --}}
        <button type="button"
                @click="tab = 'customers'"
                :class="tab === 'customers' 
                    ? 'bg-gradient-to-r from-[#FDDD57] to-[#ecbc42] text-slate-900 shadow-md shadow-amber-500/20' 
                    : 'text-slate-400 hover:text-[#FDDD57]'"
                class="flex-1 sm:flex-none flex items-center justify-center gap-2
                       px-4 py-2.5 rounded-lg text-sm font-bold transition-all
                       whitespace-nowrap">
            <iconify-icon icon="mdi:account-multiple-outline" class="text-lg"></iconify-icon>
            <span>Daftar Pelanggan</span>
        </button>

        {{-- Tab: Password Reset Requests --}}
        <button type="button"
                @click="tab = 'requests'"
                :class="tab === 'requests' 
                    ? 'bg-gradient-to-r from-[#FDDD57] to-[#ecbc42] text-slate-900 shadow-md shadow-amber-500/20' 
                    : 'text-slate-400 hover:text-[#FDDD57]'"
                class="flex-1 sm:flex-none flex items-center justify-center gap-2
                       px-4 py-2.5 rounded-lg text-sm font-bold transition-all
                       whitespace-nowrap relative">
            <iconify-icon icon="mdi:lock-reset" class="text-lg"></iconify-icon>
            <span>Permintaan Reset Password</span>
            @if($pendingResetCount > 0)
                <span class="inline-flex items-center justify-center
                            min-w-[18px] h-[18px] px-1.5
                            text-[10px] font-bold
                            rounded-full
                            bg-red-500 text-white
                            animate-pulse">
                    {{ $pendingResetCount > 99 ? '99+' : $pendingResetCount }}
                </span>
            @endif
        </button>
    </div>


    {{-- ============================================ --}}
    {{-- TAB CONTENT: CUSTOMERS --}}
    {{-- ============================================ --}}
    <div x-show="tab === 'customers'" x-cloak class="space-y-6">

        {{-- Stats --}}
        @php
            $totalActive = $users->where('is_active', true)->count();
            $totalInactive = $users->where('is_active', false)->count();
            $totalOrders = $users->sum('orders_count');
            $totalSpent = $users->sum('orders_sum_total');
        @endphp

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            {{-- Total Pelanggan --}}
            <div class="rounded-xl border p-4" style="background: var(--bg-card); border-color: var(--border-2);">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                                bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]">
                        <iconify-icon icon="mdi:account-multiple-outline" class="text-slate-900 text-lg"></iconify-icon>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5);">Total Pelanggan</p>
                        <p class="text-xl font-bold leading-tight" style="color: var(--text-1);">{{ $users->total() }}</p>
                    </div>
                </div>
            </div>

            {{-- Aktif --}}
            <div class="rounded-xl border p-4" style="background: var(--bg-card); border-color: var(--border-2);">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                                bg-emerald-500/10 border border-emerald-500/30">
                        <iconify-icon icon="mdi:account-check-outline" class="text-emerald-400 text-lg"></iconify-icon>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5);">Aktif</p>
                        <p class="text-xl font-bold leading-tight text-emerald-400">{{ $totalActive }}</p>
                    </div>
                </div>
            </div>

            {{-- Total Order --}}
            <div class="rounded-xl border p-4" style="background: var(--bg-card); border-color: var(--border-2);">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                                bg-blue-500/10 border border-blue-500/30">
                        <iconify-icon icon="mdi:cart-outline" class="text-blue-400 text-lg"></iconify-icon>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5);">Total Order</p>
                        <p class="text-xl font-bold leading-tight text-blue-400">{{ $totalOrders }}</p>
                    </div>
                </div>
            </div>

            {{-- Total Belanja --}}
            <div class="rounded-xl border p-4" style="background: var(--bg-card); border-color: var(--border-2);">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                                bg-purple-500/10 border border-purple-500/30">
                        <iconify-icon icon="mdi:wallet-outline" class="text-purple-400 text-lg"></iconify-icon>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5);">Total Belanja</p>
                        <p class="text-base font-bold leading-tight text-purple-400 truncate">
                            Rp {{ number_format($totalSpent, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Search --}}
        <form method="GET" action="{{ route('admin.customers.index') }}" class="relative w-full sm:w-72">
            <input type="hidden" name="tab" value="customers">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nama, email, atau telepon..."
                   class="w-full pl-10 pr-4 py-2.5 rounded-lg text-sm"
                   style="background: var(--bg-input); border: 1px solid var(--border-2); color: var(--text-1)">
            <iconify-icon icon="mdi:magnify"
                          class="absolute left-3 top-1/2 -translate-y-1/2 text-lg pointer-events-none"
                          style="color: var(--text-5)"></iconify-icon>
        </form>

        {{-- Table Customers --}}
        <div class="rounded-xl overflow-hidden border" style="background: var(--bg-card); border-color: var(--border-2)">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b" style="background: var(--bg-input); border-color: var(--border-2)">
                        <tr>
                            <th class="text-left px-4 py-4 text-[10px] font-bold uppercase tracking-wider w-16" style="color: var(--text-5)">#</th>
                            <th class="text-left px-4 py-4 text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">Pelanggan</th>
                            <th class="text-left px-4 py-4 text-[10px] font-bold uppercase tracking-wider hidden lg:table-cell" style="color: var(--text-5)">No. HP</th>
                            <th class="text-center px-4 py-4 text-[10px] font-bold uppercase tracking-wider w-32 hidden sm:table-cell" style="color: var(--text-5)">Order</th>
                            <th class="text-right px-4 py-4 text-[10px] font-bold uppercase tracking-wider w-36 hidden md:table-cell" style="color: var(--text-5)">Total Belanja</th>
                            <th class="text-center px-4 py-4 text-[10px] font-bold uppercase tracking-wider w-32" style="color: var(--text-5)">Status</th>
                            <th class="text-right px-4 py-4 text-[10px] font-bold uppercase tracking-wider w-32" style="color: var(--text-5)">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr class="transition-colors border-b last:border-0"
                                style="border-color: var(--border-1)"
                                onmouseover="this.style.background='var(--bg-hover)'"
                                onmouseout="this.style.background='transparent'">
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                                 text-xs font-mono font-semibold border"
                                          style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-4)">
                                        {{ $users->firstItem() + $loop->index }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
                                                    bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]">
                                            <span class="text-xs font-bold text-slate-900">
                                                {{ strtoupper(substr($user->name ?? '?', 0, 1)) }}
                                            </span>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold truncate max-w-[260px]" style="color: var(--text-1);">
                                                {{ $user->name }}
                                            </p>
                                            <p class="text-xs mt-0.5 truncate max-w-[260px]" style="color: var(--text-5)">
                                                {{ $user->email }}
                                            </p>
                                            <p class="text-xs mt-0.5 lg:hidden" style="color: var(--text-5)">
                                                {{ $user->phone ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 hidden lg:table-cell">
                                    <span class="text-sm font-mono" style="color: var(--text-3);">{{ $user->phone ?? '-' }}</span>
                                </td>
                                <td class="px-4 py-4 text-center hidden sm:table-cell">
                                    @if(($user->orders_count ?? 0) > 0)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border"
                                              style="background: rgba(96,165,250,0.1); border-color: rgba(96,165,250,0.3); color: #60a5fa;">
                                            <iconify-icon icon="mdi:cart-outline"></iconify-icon>
                                            {{ $user->orders_count }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border"
                                              style="background: rgba(148,163,184,0.1); border-color: rgba(148,163,184,0.3); color: var(--text-4);">
                                            0
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-right hidden md:table-cell">
                                    @if(($user->orders_sum_total ?? 0) > 0)
                                        <span class="text-sm font-bold" style="color: #ecbc42;">
                                            Rp {{ number_format($user->orders_sum_total, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-xs" style="color: var(--text-6);">Rp 0</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <button type="button"
                                            onclick="toggleUserStatus({{ $user->id }}, {{ $user->is_active ? 'false' : 'true' }})"
                                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-all active:scale-95 cursor-pointer"
                                            style="background: {{ $user->is_active ? '#34d399' : '#4b5563' }};">
                                        <span class="absolute top-0.5 left-0.5 h-5 w-5 rounded-full bg-white shadow transform transition-transform"
                                              style="transform: translateX({{ $user->is_active ? '20px' : '0' }});"></span>
                                    </button>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <div class="flex justify-end items-center gap-1.5">
                                        <a href="{{ route('admin.customers.show', $user) }}"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg border transition-all active:scale-95"
                                           style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)"
                                           onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                                           onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'">
                                            <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                                        </a>
                                        <form onsubmit="return handleDelete(event, {{ $user->id }}, '{{ addslashes($user->name) }}')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg border transition-all active:scale-95
                                                           bg-red-500/5 border-red-500/20 text-red-400
                                                           hover:bg-red-500/15 hover:border-red-500/40">
                                                <iconify-icon icon="mdi:trash-can-outline"></iconify-icon>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-20 text-center" style="color: var(--text-5);">
                                    Belum ada pelanggan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="px-6 py-4 border-t" style="border-color: var(--border-2); background: var(--bg-input)">
                    {{ $users->appends(request()->except('users_page'))->links() }}
                </div>
            @endif
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- TAB CONTENT: PASSWORD RESET REQUESTS --}}
    {{-- ============================================ --}}
    <div x-show="tab === 'requests'" x-cloak class="space-y-4">

        {{-- Search + Filter --}}
        <form method="GET" action="{{ route('admin.customers.index') }}" class="flex flex-wrap gap-2">
            <input type="hidden" name="tab" value="requests">
            <div class="relative flex-1 min-w-[200px]">
                <input type="text" name="reset_search" value="{{ request('reset_search') }}"
                       placeholder="Cari nama atau no HP..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-lg text-sm"
                       style="background: var(--bg-input); border: 1px solid var(--border-2); color: var(--text-1)">
                <iconify-icon icon="mdi:magnify"
                              class="absolute left-3 top-1/2 -translate-y-1/2 text-lg pointer-events-none"
                              style="color: var(--text-5)"></iconify-icon>
            </div>
            <select name="reset_status"
                    class="px-4 py-2.5 rounded-lg text-sm min-w-[160px]"
                    style="background: var(--bg-input); border: 1px solid var(--border-2); color: var(--text-1)">
                <option value="">Semua Status</option>
                <option value="pending" @selected(request('reset_status') === 'pending')>⏳ Menunggu</option>
                <option value="approved" @selected(request('reset_status') === 'approved')>✅ Disetujui</option>
                <option value="rejected" @selected(request('reset_status') === 'rejected')>❌ Ditolak</option>
                <option value="used" @selected(request('reset_status') === 'used')>🔒 Selesai</option>
            </select>
            <button type="submit"
                    class="px-4 py-2.5 rounded-lg text-sm font-bold
                           bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                           text-slate-900 transition-all active:scale-95
                           flex items-center gap-1.5">
                <iconify-icon icon="mdi:magnify"></iconify-icon>
                Filter
            </button>
            @if(request()->anyFilled(['reset_search', 'reset_status']))
                <a href="{{ route('admin.customers.index', ['tab' => 'requests']) }}"
                   class="px-4 py-2.5 rounded-lg text-sm font-bold border
                          border-red-500/30 text-red-400
                          hover:bg-red-500/10 transition-all active:scale-95">
                    Reset
                </a>
            @endif
        </form>

        {{-- Table Reset Requests --}}
        <div class="rounded-xl overflow-hidden border" style="background: var(--bg-card); border-color: var(--border-2)">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b" style="background: var(--bg-input); border-color: var(--border-2)">
                        <tr>
                            <th class="text-left px-4 py-4 text-[10px] font-bold uppercase tracking-wider w-16" style="color: var(--text-5)">#</th>
                            <th class="text-left px-4 py-4 text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">Customer</th>
                            <th class="text-left px-4 py-4 text-[10px] font-bold uppercase tracking-wider hidden md:table-cell" style="color: var(--text-5)">No. WA</th>
                            <th class="text-center px-4 py-4 text-[10px] font-bold uppercase tracking-wider w-40" style="color: var(--text-5)">Status</th>
                            <th class="text-left px-4 py-4 text-[10px] font-bold uppercase tracking-wider hidden lg:table-cell" style="color: var(--text-5)">Diminta</th>
                            <th class="text-right px-4 py-4 text-[10px] font-bold uppercase tracking-wider w-56" style="color: var(--text-5)">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($resetRequests as $req)
                            @php
                                $statusStyle = [
                                    'pending'  => ['#fbbf24', 'rgba(251,191,36,0.1)',  'rgba(251,191,36,0.3)'],
                                    'approved' => ['#34d399', 'rgba(52,211,153,0.1)',  'rgba(52,211,153,0.3)'],
                                    'rejected' => ['#f87171', 'rgba(248,113,113,0.1)', 'rgba(248,113,113,0.3)'],
                                    'used'     => ['#94a3b8', 'rgba(148,163,184,0.1)', 'rgba(148,163,184,0.3)'],
                                    'expired'  => ['#6b7280', 'rgba(107,114,128,0.1)', 'rgba(107,114,128,0.3)'],
                                ][$req->status] ?? ['var(--text-4)', 'rgba(148,163,184,0.1)', 'rgba(148,163,184,0.3)'];
                            @endphp
                            <tr class="transition-colors border-b last:border-0"
                                style="border-color: var(--border-1)"
                                onmouseover="this.style.background='var(--bg-hover)'"
                                onmouseout="this.style.background='transparent'">
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                                 text-xs font-mono font-semibold border"
                                          style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-4)">
                                        {{ $resetRequests->firstItem() + $loop->index }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
                                                    bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]">
                                            <span class="text-xs font-bold text-slate-900">
                                                {{ strtoupper(substr($req->user->name ?? '?', 0, 1)) }}
                                            </span>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold truncate max-w-[200px]" style="color: var(--text-1);">
                                                {{ $req->user->name ?? 'User dihapus' }}
                                            </p>
                                            <p class="text-xs mt-0.5 truncate max-w-[200px]" style="color: var(--text-5)">
                                                {{ $req->user->email ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 hidden md:table-cell">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg
                                                 text-xs font-mono font-bold border"
                                          style="background: rgba(34,197,94,0.1); border-color: rgba(34,197,94,0.3); color: #4ade80;">
                                        <iconify-icon icon="mdi:whatsapp"></iconify-icon>
                                        {{ $req->phone }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                                 text-[10px] font-bold border w-fit"
                                          style="background: {{ $statusStyle[1] }}; border-color: {{ $statusStyle[2] }}; color: {{ $statusStyle[0] }};">
                                        <span class="w-1.5 h-1.5 rounded-full bg-current {{ $req->status === 'pending' ? 'animate-pulse' : '' }}"></span>
                                        {{ $req->status_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 hidden lg:table-cell">
                                    <p class="text-xs" style="color: var(--text-3);">
                                        {{ $req->created_at->format('d M Y, H:i') }}
                                    </p>
                                    <p class="text-[10px]" style="color: var(--text-5);">
                                        {{ $req->created_at->diffForHumans() }}
                                    </p>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    @if($req->status === 'pending')
                                        <div class="flex justify-end items-center gap-1.5">
                                            {{-- Approve --}}
                                            <form action="{{ route('admin.customers.password-approve', $req) }}"
                                                  method="POST" class="inline"
                                                  onsubmit="return confirm('Setujui permintaan reset password untuk {{ addslashes($req->user->name ?? 'user') }}?\n\nLink reset akan dibuat.');">
                                                @csrf
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                                                               text-xs font-bold transition-all active:scale-95
                                                               bg-emerald-500/15 border border-emerald-500/30 text-emerald-400
                                                               hover:bg-emerald-500 hover:text-white hover:border-emerald-500">
                                                    <iconify-icon icon="mdi:check"></iconify-icon>
                                                    Setujui
                                                </button>
                                            </form>

                                            {{-- Reject --}}
                                            <button type="button"
                                                    onclick="openRejectModal({{ $req->id }}, '{{ addslashes($req->user->name ?? '') }}')"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                                                           text-xs font-bold transition-all active:scale-95
                                                           bg-red-500/15 border border-red-500/30 text-red-400
                                                           hover:bg-red-500 hover:text-white hover:border-red-500">
                                                <iconify-icon icon="mdi:close"></iconify-icon>
                                                Tolak
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-xs" style="color: var(--text-5);">
                                            {{ $req->processed_at?->format('d M Y, H:i') ?? '-' }}
                                            @if($req->processedBy)
                                                <br><small>oleh {{ $req->processedBy->name }}</small>
                                            @endif
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-20">
                                    <div class="flex flex-col items-center justify-center text-center max-w-sm mx-auto">
                                        <div class="w-20 h-20 rounded-full flex items-center justify-center border mb-5"
                                             style="background: var(--bg-input); border-color: var(--border-2)">
                                            <iconify-icon icon="mdi:lock-check-outline" class="text-4xl"
                                                          style="color: var(--text-6);"></iconify-icon>
                                        </div>
                                        <h3 class="text-base font-bold mb-1.5" style="color: var(--text-1)">
                                            Belum Ada Permintaan
                                        </h3>
                                        <p class="text-xs" style="color: var(--text-5)">
                                            Belum ada permintaan reset password dari pelanggan.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($resetRequests->hasPages())
                <div class="px-6 py-4 border-t" style="border-color: var(--border-2); background: var(--bg-input)">
                    {{ $resetRequests->appends(request()->except('requests_page'))->links() }}
                </div>
            @endif
        </div>
    </div>

</div>

{{-- ============================================ --}}
{{-- MODAL REJECT --}}
{{-- ============================================ --}}
<div id="reject-modal"
     class="fixed inset-0 z-[100] hidden items-center justify-center p-4"
     style="background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);">
    <div class="rounded-2xl border max-w-md w-full overflow-hidden"
         style="background: var(--bg-card); border-color: var(--border-2); box-shadow: 0 20px 60px rgba(0,0,0,0.5);">
        <div class="px-5 py-4 border-b flex items-center gap-3"
             style="background: var(--bg-input); border-color: var(--border-2);">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                        bg-red-500/15 border border-red-500/30">
                <iconify-icon icon="mdi:close-circle-outline" class="text-red-400 text-xl"></iconify-icon>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="font-bold text-sm" style="color: var(--text-1);">Tolak Permintaan</h3>
                <p class="text-[11px] truncate" style="color: var(--text-5);" id="reject-user-name"></p>
            </div>
        </div>
        <form id="reject-form" method="POST" class="p-5 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider mb-2" style="color: var(--text-5);">
                    Alasan Penolakan (Opsional)
                </label>
                <textarea name="admin_note" rows="4"
                          placeholder="Contoh: Identitas tidak dapat diverifikasi..."
                          class="w-full px-3 py-2 rounded-lg text-sm resize-vertical"
                          style="background: var(--bg-input); border: 1px solid var(--border-2); color: var(--text-1);"></textarea>
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeRejectModal()"
                        class="px-4 py-2 rounded-lg text-sm font-bold border transition-all active:scale-95"
                        style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3);">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 rounded-lg text-sm font-bold
                               bg-red-500 text-white transition-all active:scale-95
                               hover:bg-red-600
                               flex items-center gap-1.5">
                    <iconify-icon icon="mdi:close"></iconify-icon>
                    Tolak Permintaan
                </button>
            </div>
        </form>
    </div>
</div>


@push('scripts')
<style>[x-cloak]{display:none !important;}</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 🔥 Auto-open WhatsApp setelah 1 detik (biar admin lihat halaman dulu)
    const rejectWaBtn = document.getElementById('reject-wa-btn');
    if (rejectWaBtn) {
        setTimeout(function () {
            window.open(rejectWaBtn.href, '_blank');
        }, 800);
    }
});
</script>
<script>
// ============================================
// TOGGLE STATUS CUSTOMER
// ============================================
function toggleUserStatus(userId, isActive) {
    fetch('/admin/customers/' + userId + '/toggle-status', {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ is_active: isActive })
    })
    .then(r => r.json())
    .then(function(data) {
        if (data.success) location.reload();
        else alert('Gagal mengubah status.');
    })
    .catch(function(err) {
        console.error(err);
        alert('Terjadi kesalahan.');
    });
}

// ============================================
// DELETE CUSTOMER
// ============================================
function handleDelete(event, userId, userName) {
    event.preventDefault();
    if (!confirm('Hapus pelanggan "' + userName + '"?\nSemua data terkait akan dihapus.')) return false;

    fetch('/admin/customers/' + userId, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
        },
    })
    .then(r => r.json())
    .then(function(data) {
        if (data.success) location.reload();
        else alert('Gagal menghapus.');
    })
    .catch(function(err) { console.error(err); alert('Error.'); });

    return false;
}

// ============================================
// REJECT MODAL
// ============================================
function openRejectModal(requestId, userName) {
    const modal = document.getElementById('reject-modal');
    const form = document.getElementById('reject-form');
    const nameEl = document.getElementById('reject-user-name');

    form.action = '/admin/customers/password-requests/' + requestId + '/reject';
    nameEl.textContent = userName || '';

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeRejectModal() {
    const modal = document.getElementById('reject-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Close modal on overlay click
document.getElementById('reject-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
});

// ============================================
// COPY RESET LINK
// ============================================
function copyResetLink() {
    const input = document.getElementById('reset-link-input');
    if (!input) return;
    input.select();
    input.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(input.value).then(() => {
        const btn = event.target.closest('button');
        const original = btn.innerHTML;
        btn.innerHTML = '<iconify-icon icon="mdi:check"></iconify-icon> Tersalin!';
        btn.classList.add('bg-emerald-600');
        setTimeout(() => {
            btn.innerHTML = original;
            btn.classList.remove('bg-emerald-600');
        }, 2000);
    });
}

// Esc to close modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeRejectModal();
});
</script>
@endpush

@endsection