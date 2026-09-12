@extends('layouts.admin')

@section('title', 'Detail Pelanggan')
@section('page-title', 'Detail Pelanggan')

@section('content')

<div class="w-full max-w-6xl mx-auto space-y-6">

    {{-- ============================================ --}}
    {{-- HEADER --}}
    {{-- ============================================ --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <a href="{{ route('admin.customers.index') }}"
               class="inline-flex items-center gap-1.5 text-xs font-semibold transition-colors"
               style="color: var(--text-5)"
               onmouseover="this.style.color='#FDDD57'"
               onmouseout="this.style.color='var(--text-5)'">
                <iconify-icon icon="mdi:arrow-left"></iconify-icon>
                Kembali ke Pelanggan
            </a>
            <h1 class="text-2xl font-bold flex items-center gap-2.5 mt-2" style="color: var(--text-1)">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl
                             bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                             shadow-lg shadow-amber-500/20 flex-shrink-0">
                    <iconify-icon icon="mdi:account-details-outline" class="text-slate-900 text-2xl"></iconify-icon>
                </span>
                Detail Pelanggan
            </h1>
            <p class="text-sm mt-1.5 ml-12 truncate" style="color: var(--text-5)">
                {{ $user->name }} • {{ $user->email }}
            </p>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-wrap items-center gap-2 flex-shrink-0">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border"
                  style="background: {{ $user->is_active ? 'rgba(52,211,153,0.1)' : 'rgba(248,113,113,0.1)' }};
                         border-color: {{ $user->is_active ? 'rgba(52,211,153,0.3)' : 'rgba(248,113,113,0.3)' }};
                         color: {{ $user->is_active ? '#34d399' : '#f87171' }};">
                <span class="w-1.5 h-1.5 rounded-full bg-current {{ $user->is_active ? 'animate-pulse' : '' }}"></span>
                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
            @if($user->email_verified_at)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border"
                      style="background: rgba(96,165,250,0.1); border-color: rgba(96,165,250,0.3); color: #60a5fa;">
                    <iconify-icon icon="mdi:shield-check-outline"></iconify-icon>
                    Email Terverifikasi
                </span>
            @endif
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- PROFILE CARD --}}
    {{-- ============================================ --}}
    <div class="rounded-2xl border overflow-hidden"
         style="background: var(--bg-card); border-color: var(--border-2)">

        <div class="p-6">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5">

                {{-- Avatar Besar --}}
                <div class="w-24 h-24 rounded-2xl overflow-hidden flex-shrink-0
                            bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                            shadow-xl shadow-amber-500/20
                            flex items-center justify-center">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}"
                            alt="{{ $user->name }}"
                            class="w-full h-full object-cover"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <span class="text-3xl font-bold text-slate-900 hidden">
                            {{ strtoupper(substr($user->name ?? '?', 0, 1)) }}
                        </span>
                    @else
                        <span class="text-3xl font-bold text-slate-900">
                            {{ strtoupper(substr($user->name ?? '?', 0, 1)) }}
                        </span>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0 text-center sm:text-left">
                    <h2 class="text-xl font-bold" style="color: var(--text-1);">
                        {{ $user->name }}
                    </h2>

                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-3 mt-2 text-sm">
                        <span class="inline-flex items-center gap-1.5" style="color: var(--text-4);">
                            <iconify-icon icon="mdi:email-outline" class="text-[#ecbc42]"></iconify-icon>
                            {{ $user->email }}
                        </span>
                        @if($user->phone)
                            <span class="inline-flex items-center gap-1.5" style="color: var(--text-4);">
                                <iconify-icon icon="mdi:phone-outline" class="text-[#ecbc42]"></iconify-icon>
                                {{ $user->phone }}
                            </span>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-3 mt-3 text-xs"
                         style="color: var(--text-5);">
                        <span class="inline-flex items-center gap-1.5">
                            <iconify-icon icon="mdi:calendar-clock-outline"></iconify-icon>
                            Bergabung: {{ $user->created_at?->format('d M Y') ?? '-' }}
                        </span>
                        @if($user->role)
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full border"
                                  style="background: rgba(236,188,66,0.1); border-color: rgba(236,188,66,0.3); color: #ecbc42;">
                                <iconify-icon icon="mdi:shield-account-outline"></iconify-icon>
                                {{ ucfirst($user->role) }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- STATS CARDS --}}
    {{-- ============================================ --}}
    @php
        $totalOrders = $user->orders->count();
        $totalSpent = $user->orders->where('payment_status', 'paid')->sum('total');
        $lastOrder = $user->orders->sortByDesc('created_at')->first();
    @endphp

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">

        {{-- Total Order --}}
        <div class="rounded-xl border p-4 transition-colors"
             style="background: var(--bg-card); border-color: var(--border-2);"
             onmouseover="this.style.borderColor='rgba(96,165,250,0.3)'"
             onmouseout="this.style.borderColor='var(--border-2)'">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                            bg-blue-500/10 border border-blue-500/30">
                    <iconify-icon icon="mdi:cart-outline" class="text-blue-400 text-lg"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5);">
                        Total Order
                    </p>
                    <p class="text-xl font-bold leading-tight text-blue-400">
                        {{ $totalOrders }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Total Belanja --}}
        <div class="rounded-xl border p-4 transition-colors"
             style="background: var(--bg-card); border-color: var(--border-2);"
             onmouseover="this.style.borderColor='rgba(236,188,66,0.3)'"
             onmouseout="this.style.borderColor='var(--border-2)'">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                            bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                            shadow-md shadow-amber-500/20">
                    <iconify-icon icon="mdi:wallet-outline" class="text-slate-900 text-lg"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5);">
                        Total Belanja
                    </p>
                    <p class="text-sm font-bold leading-tight truncate" style="color: #ecbc42;">
                        Rp {{ number_format($totalSpent, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Alamat --}}
        <div class="rounded-xl border p-4 transition-colors"
             style="background: var(--bg-card); border-color: var(--border-2);"
             onmouseover="this.style.borderColor='rgba(167,139,250,0.3)'"
             onmouseout="this.style.borderColor='var(--border-2)'">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                            bg-purple-500/10 border border-purple-500/30">
                    <iconify-icon icon="mdi:map-marker-outline" class="text-purple-400 text-lg"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5);">
                        Alamat
                    </p>
                    <p class="text-xl font-bold leading-tight text-purple-400">
                        {{ $user->addresses->count() }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Order Terakhir --}}
        <div class="rounded-xl border p-4 transition-colors"
             style="background: var(--bg-card); border-color: var(--border-2);"
             onmouseover="this.style.borderColor='rgba(52,211,153,0.3)'"
             onmouseout="this.style.borderColor='var(--border-2)'">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                            bg-emerald-500/10 border border-emerald-500/30">
                    <iconify-icon icon="mdi:clock-outline" class="text-emerald-400 text-lg"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5);">
                        Order Terakhir
                    </p>
                    <p class="text-xs font-bold leading-tight truncate text-emerald-400">
                        {{ $lastOrder?->created_at?->diffForHumans() ?? 'Belum ada' }}
                    </p>
                </div>
            </div>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- GRID: ALAMAT + INFO --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Alamat Pelanggan --}}
        <div class="rounded-2xl border overflow-hidden"
             style="background: var(--bg-card); border-color: var(--border-2)">

            <div class="px-5 py-4 border-b flex items-center gap-2"
                 style="background: var(--bg-input); border-color: var(--border-2)">
                <iconify-icon icon="mdi:map-marker-multiple-outline" class="text-[#ecbc42] text-base"></iconify-icon>
                <h3 class="font-bold text-sm flex-1" style="color: var(--text-1);">Alamat Tersimpan</h3>
                @if($user->addresses->count() > 0)
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded-full"
                          style="background: rgba(236,188,66,0.1); color: #ecbc42;">
                        {{ $user->addresses->count() }}
                    </span>
                @endif
            </div>

            <div class="p-5">
                @if($user->addresses->count() > 0)
                    <div class="space-y-3">
                        @foreach($user->addresses->take(3) as $address)
                            <div class="p-3 rounded-lg border"
                                 style="background: var(--bg-input); border-color: var(--border-2);">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0
                                                bg-[#ecbc42]/10 border border-[#ecbc42]/30">
                                        <iconify-icon icon="mdi:home-outline" class="text-[#ecbc42] text-sm"></iconify-icon>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <p class="text-sm font-semibold" style="color: var(--text-1);">
                                                {{ $address->label ?? 'Alamat' }}
                                            </p>
                                            @if($address->is_default ?? false)
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold"
                                                      style="background: rgba(52,211,153,0.1); color: #34d399;">
                                                    <iconify-icon icon="mdi:star"></iconify-icon>
                                                    Utama
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs mt-1 leading-relaxed" style="color: var(--text-4);">
                                            {{ $address->recipient_name ?? $user->name }} • {{ $address->phone ?? $user->phone }}<br>
                                            {{ $address->address }}<br>
                                            {{ $address->city ?? '' }}{{ $address->province ? ', '.$address->province : '' }}
                                            {{ $address->postal_code ?? '' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if($user->addresses->count() > 3)
                            <p class="text-[10px] text-center pt-2" style="color: var(--text-5);">
                                +{{ $user->addresses->count() - 3 }} alamat lainnya
                            </p>
                        @endif
                    </div>
                @else
                    <div class="py-8 text-center">
                        <iconify-icon icon="mdi:map-marker-off-outline" class="text-3xl mb-2"
                                      style="color: var(--text-6);"></iconify-icon>
                        <p class="text-xs" style="color: var(--text-5);">Belum ada alamat tersimpan</p>
                    </div>
                @endif
            </div>
        </div>


        {{-- Informasi Akun --}}
        <div class="rounded-2xl border overflow-hidden"
             style="background: var(--bg-card); border-color: var(--border-2)">

            <div class="px-5 py-4 border-b flex items-center gap-2"
                 style="background: var(--bg-input); border-color: var(--border-2)">
                <iconify-icon icon="mdi:account-cog-outline" class="text-[#ecbc42] text-base"></iconify-icon>
                <h3 class="font-bold text-sm" style="color: var(--text-1);">Informasi Akun</h3>
            </div>

            <div class="p-5 space-y-4">

                {{-- Email --}}
                <div class="flex items-start gap-3 pb-4 border-b" style="border-color: var(--border-1);">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background: var(--bg-input);">
                        <iconify-icon icon="mdi:email-outline" class="text-sm" style="color: var(--text-4);"></iconify-icon>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[10px] font-bold uppercase tracking-wider mb-0.5" style="color: var(--text-5);">
                            Email
                        </p>
                        <p class="text-sm font-semibold break-all" style="color: var(--text-1);">
                            {{ $user->email }}
                        </p>
                    </div>
                </div>

                {{-- Phone --}}
                <div class="flex items-start gap-3 pb-4 border-b" style="border-color: var(--border-1);">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background: var(--bg-input);">
                        <iconify-icon icon="mdi:phone-outline" class="text-sm" style="color: var(--text-4);"></iconify-icon>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[10px] font-bold uppercase tracking-wider mb-0.5" style="color: var(--text-5);">
                            Nomor Telepon
                        </p>
                        <p class="text-sm font-semibold font-mono" style="color: var(--text-1);">
                            {{ $user->phone ?? 'Tidak tersedia' }}
                        </p>
                    </div>
                </div>

                {{-- Alamat (Utama) --}}
                @if($user->address)
                    <div class="flex items-start gap-3 pb-4 border-b" style="border-color: var(--border-1);">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                             style="background: var(--bg-input);">
                            <iconify-icon icon="mdi:home-outline" class="text-sm" style="color: var(--text-4);"></iconify-icon>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[10px] font-bold uppercase tracking-wider mb-0.5" style="color: var(--text-5);">
                                Alamat Utama
                            </p>
                            <p class="text-xs leading-relaxed" style="color: var(--text-3);">
                                {{ $user->address }}
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Bergabung --}}
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background: var(--bg-input);">
                        <iconify-icon icon="mdi:calendar-outline" class="text-sm" style="color: var(--text-4);"></iconify-icon>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[10px] font-bold uppercase tracking-wider mb-0.5" style="color: var(--text-5);">
                            Bergabung Sejak
                        </p>
                        <p class="text-sm font-semibold" style="color: var(--text-1);">
                            {{ $user->created_at?->format('d F Y, H:i') ?? '-' }}
                        </p>
                        <p class="text-[10px] mt-0.5" style="color: var(--text-5);">
                            {{ $user->created_at?->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- RIWAYAT PESANAN --}}
    {{-- ============================================ --}}
    <div class="rounded-2xl border overflow-hidden"
         style="background: var(--bg-card); border-color: var(--border-2)">

        <div class="px-5 py-4 border-b flex items-center gap-2"
             style="background: var(--bg-input); border-color: var(--border-2)">
            <iconify-icon icon="mdi:history" class="text-[#ecbc42] text-base"></iconify-icon>
            <h3 class="font-bold text-sm flex-1" style="color: var(--text-1);">Riwayat Pesanan</h3>
            @if($user->orders->count() > 0)
                <a href="{{ route('admin.orders.index', ['search' => $user->email]) }}"
                   class="text-[10px] font-bold px-2.5 py-1 rounded-lg transition-all"
                   style="background: var(--bg-card); border: 1px solid var(--border-2); color: var(--text-4);"
                   onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                   onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-4)'">
                    Lihat Semua
                </a>
            @endif
        </div>

        @if($user->orders->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">

                    <thead class="border-b"
                           style="background: var(--bg-input); border-color: var(--border-2)">
                        <tr>
                            <th class="text-left px-4 py-3.5 text-[10px] font-bold uppercase tracking-wider"
                                style="color: var(--text-5)">No. Order</th>
                            <th class="text-left px-4 py-3.5 text-[10px] font-bold uppercase tracking-wider hidden sm:table-cell"
                                style="color: var(--text-5)">Tanggal</th>
                            <th class="text-left px-4 py-3.5 text-[10px] font-bold uppercase tracking-wider hidden md:table-cell"
                                style="color: var(--text-5)">Total</th>
                            <th class="text-left px-4 py-3.5 text-[10px] font-bold uppercase tracking-wider"
                                style="color: var(--text-5)">Status</th>
                            <th class="text-right px-4 py-3.5 text-[10px] font-bold uppercase tracking-wider w-24"
                                style="color: var(--text-5)">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($user->orders as $order)
                            @php
                                $statusColors = [
                                    'pending'    => ['#fbbf24', 'rgba(251,191,36,0.1)', 'rgba(251,191,36,0.3)'],
                                    'processing' => ['#60a5fa', 'rgba(96,165,250,0.1)', 'rgba(96,165,250,0.3)'],
                                    'shipped'    => ['#a78bfa', 'rgba(167,139,250,0.1)', 'rgba(167,139,250,0.3)'],
                                    'delivered'  => ['#34d399', 'rgba(52,211,153,0.1)', 'rgba(52,211,153,0.3)'],
                                    'cancelled'  => ['#f87171', 'rgba(248,113,113,0.1)', 'rgba(248,113,113,0.3)'],
                                ];
                                $sc = $statusColors[$order->shipping_status] ?? ['var(--text-4)', 'rgba(148,163,184,0.1)', 'rgba(148,163,184,0.3)'];

                                $paymentColors = [
                                    'paid'    => ['#34d399', 'rgba(52,211,153,0.1)', 'rgba(52,211,153,0.3)'],
                                    'unpaid'  => ['#fb923c', 'rgba(251,146,60,0.1)', 'rgba(251,146,60,0.3)'],
                                    'failed'  => ['#f87171', 'rgba(248,113,113,0.1)', 'rgba(248,113,113,0.3)'],
                                ];
                                $pc = $paymentColors[$order->payment_status] ?? ['var(--text-4)', 'rgba(148,163,184,0.1)', 'rgba(148,163,184,0.3)'];
                            @endphp
                            <tr class="transition-colors border-b last:border-0"
                                style="border-color: var(--border-1)"
                                onmouseover="this.style.background='var(--bg-hover)'"
                                onmouseout="this.style.background='transparent'">

                                {{-- No Order --}}
                                <td class="px-4 py-3.5">
                                    <span class="font-mono text-xs font-bold" style="color: var(--text-1);">
                                        #{{ $order->order_number }}
                                    </span>
                                    {{-- Tanggal untuk mobile --}}
                                    <p class="text-[10px] mt-0.5 sm:hidden" style="color: var(--text-5);">
                                        {{ $order->created_at->format('d M Y') }}
                                    </p>
                                </td>

                                {{-- Tanggal --}}
                                <td class="px-4 py-3.5 hidden sm:table-cell">
                                    <p class="text-xs font-semibold" style="color: var(--text-3);">
                                        {{ $order->created_at->format('d M Y') }}
                                    </p>
                                    <p class="text-[10px]" style="color: var(--text-5);">
                                        {{ $order->created_at->format('H:i') }}
                                    </p>
                                </td>

                                {{-- Total --}}
                                <td class="px-4 py-3.5 hidden md:table-cell">
                                    <span class="text-sm font-bold" style="color: #ecbc42;">
                                        Rp {{ number_format($order->total, 0, ',', '.') }}
                                    </span>
                                </td>

                                {{-- Status --}}
                                <td class="px-4 py-3.5">
                                    <div class="flex flex-col gap-1.5">
                                        {{-- Shipping status --}}
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full
                                                     text-[10px] font-bold border w-fit"
                                              style="background: {{ $sc[1] }}; border-color: {{ $sc[2] }}; color: {{ $sc[0] }};">
                                            <span class="w-1 h-1 rounded-full bg-current"></span>
                                            {{ $order->shipping_status_label }}
                                        </span>

                                        {{-- Payment status --}}
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full
                                                     text-[10px] font-bold border w-fit"
                                              style="background: {{ $pc[1] }}; border-color: {{ $pc[2] }}; color: {{ $pc[0] }};">
                                            <span class="w-1 h-1 rounded-full bg-current"></span>
                                            {{ $order->payment_status_label }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-4 py-3.5 text-right">
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                              border transition-all active:scale-95"
                                       style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)"
                                       onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                                       onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'"
                                       title="Lihat detail order">
                                        <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            {{-- Empty State --}}
            <div class="px-6 py-16">
                <div class="flex flex-col items-center justify-center text-center max-w-sm mx-auto">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4 border"
                         style="background: var(--bg-input); border-color: var(--border-2)">
                        <iconify-icon icon="mdi:cart-off" class="text-2xl" style="color: var(--text-6);"></iconify-icon>
                    </div>
                    <p class="text-sm font-semibold mb-1" style="color: var(--text-3);">
                        Belum Ada Pesanan
                    </p>
                    <p class="text-xs" style="color: var(--text-5);">
                        Pelanggan ini belum pernah melakukan pemesanan.
                    </p>
                </div>
            </div>
        @endif
    </div>


    {{-- ============================================ --}}
    {{-- ACTION BUTTONS --}}
    {{-- ============================================ --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 pb-4">

        {{-- Toggle Status --}}
        <button type="button"
                onclick="toggleUserStatus({{ $user->id }}, {{ $user->is_active ? 'false' : 'true' }})"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg
                       text-sm font-bold transition-all active:scale-95 border"
                style="background: {{ $user->is_active ? 'rgba(251,191,36,0.05)' : 'rgba(52,211,153,0.05)' }};
                       border-color: {{ $user->is_active ? 'rgba(251,191,36,0.3)' : 'rgba(52,211,153,0.3)' }};
                       color: {{ $user->is_active ? '#fbbf24' : '#34d399' }};"
                onmouseover="this.style.background='{{ $user->is_active ? 'rgba(251,191,36,0.15)' : 'rgba(52,211,153,0.15)' }}'"
                onmouseout="this.style.background='{{ $user->is_active ? 'rgba(251,191,36,0.05)' : 'rgba(52,211,153,0.05)' }}'">
            <iconify-icon icon="{{ $user->is_active ? 'mdi:account-off-outline' : 'mdi:account-check-outline' }}"></iconify-icon>
            {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
        </button>

        {{-- Delete --}}
        <button type="button"
                onclick="handleDelete({{ $user->id }}, '{{ addslashes($user->name) }}')"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg
                       text-sm font-bold transition-all active:scale-95
                       bg-red-500/10 border border-red-500/30 text-red-400
                       hover:bg-red-500/20 hover:border-red-500/50 hover:text-red-300">
            <iconify-icon icon="mdi:trash-can-outline"></iconify-icon>
            Hapus Pelanggan
        </button>
    </div>

</div>


{{-- ============================================ --}}
{{-- SCRIPTS --}}
{{-- ============================================ --}}
@push('scripts')
<script>
// ============================================
// TOGGLE STATUS
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
        if (data.success) {
            location.reload();
        } else {
            alert('Gagal mengubah status.');
        }
    })
    .catch(function(err) {
        console.error('Gagal mengubah status:', err);
        alert('Terjadi kesalahan saat mengubah status.');
    });
}

// ============================================
// DELETE CUSTOMER
// ============================================
function handleDelete(userId, userName) {
    if (!confirm('Hapus pelanggan "' + userName + '"?\nSemua data terkait akan dihapus secara permanen.')) {
        return;
    }

    var token = document.querySelector('meta[name="csrf-token"]').content;

    fetch('/admin/customers/' + userId, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest',
        },
    })
    .then(r => r.json())
    .then(function(data) {
        if (data.success) {
            window.location.href = '{{ route("admin.customers.index") }}';
        } else {
            alert('Gagal menghapus pelanggan.');
        }
    })
    .catch(function(err) {
        console.error('Gagal menghapus:', err);
        alert('Terjadi kesalahan saat menghapus pelanggan.');
    });
}
</script>
@endpush

@endsection