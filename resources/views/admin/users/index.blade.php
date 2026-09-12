@extends('layouts.admin')

@section('title', 'Pelanggan')
@section('page-title', 'Pelanggan')

@section('content')

<div class="w-full space-y-6">

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
                Kelola daftar pelanggan yang terdaftar di toko.
            </p>
        </div>

        {{-- Search --}}
        <form method="GET" action="{{ route('admin.customers.index') }}" class="relative w-full sm:w-72 flex-shrink-0">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Cari nama, email, atau telepon..."
                   class="w-full pl-10 pr-4 py-2.5 rounded-lg text-sm"
                   style="background: var(--bg-input); border: 1px solid var(--border-2); color: var(--text-1)">
            <iconify-icon icon="mdi:magnify"
                          class="absolute left-3 top-1/2 -translate-y-1/2 text-lg pointer-events-none"
                          style="color: var(--text-5)"></iconify-icon>
        </form>
    </div>


    {{-- ============================================ --}}
    {{-- ALERTS --}}
    {{-- ============================================ --}}
    @if(session('success'))
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl
                    bg-emerald-500/10 border border-emerald-500/30 text-emerald-400">
            <iconify-icon icon="mdi:check-circle-outline" class="text-xl flex-shrink-0 mt-0.5"></iconify-icon>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold">Berhasil!</p>
                <p class="text-xs opacity-80 mt-0.5">{{ session('success') }}</p>
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
    {{-- STATS --}}
    {{-- ============================================ --}}
    @php
        $totalActive = $users->where('is_active', true)->count();
        $totalInactive = $users->where('is_active', false)->count();
        $totalOrders = $users->sum('orders_count');
        $totalSpent = $users->sum('orders_sum_total');
    @endphp

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">

        {{-- Total Pelanggan --}}
        <div class="rounded-xl border p-4 transition-colors"
             style="background: var(--bg-card); border-color: var(--border-2);"
             onmouseover="this.style.borderColor='rgba(236,188,66,0.3)'"
             onmouseout="this.style.borderColor='var(--border-2)'">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                            bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                            shadow-md shadow-amber-500/20">
                    <iconify-icon icon="mdi:account-multiple-outline" class="text-slate-900 text-lg"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5);">
                        Total Pelanggan
                    </p>
                    <p class="text-xl font-bold leading-tight" style="color: var(--text-1);">
                        {{ $users->total() }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Aktif --}}
        <div class="rounded-xl border p-4 transition-colors"
             style="background: var(--bg-card); border-color: var(--border-2);"
             onmouseover="this.style.borderColor='rgba(52,211,153,0.3)'"
             onmouseout="this.style.borderColor='var(--border-2)'">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                            bg-emerald-500/10 border border-emerald-500/30">
                    <iconify-icon icon="mdi:account-check-outline" class="text-emerald-400 text-lg"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5);">
                        Aktif
                    </p>
                    <p class="text-xl font-bold leading-tight text-emerald-400">
                        {{ $totalActive }}
                    </p>
                </div>
            </div>
        </div>

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
             onmouseover="this.style.borderColor='rgba(167,139,250,0.3)'"
             onmouseout="this.style.borderColor='var(--border-2)'">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                            bg-purple-500/10 border border-purple-500/30">
                    <iconify-icon icon="mdi:wallet-outline" class="text-purple-400 text-lg"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5);">
                        Total Belanja
                    </p>
                    <p class="text-base font-bold leading-tight text-purple-400 truncate">
                        Rp {{ number_format($totalSpent, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- TABLE --}}
    {{-- ============================================ --}}
    <div class="rounded-xl overflow-hidden border"
         style="background: var(--bg-card); border-color: var(--border-2)">

        <div class="overflow-x-auto">
            <table class="w-full text-sm">

                {{-- Table Header --}}
                <thead class="border-b"
                       style="background: var(--bg-input); border-color: var(--border-2)">
                    <tr>
                        <th class="text-left px-4 py-4 text-[10px] font-bold uppercase tracking-wider w-16"
                            style="color: var(--text-5)">#</th>
                        <th class="text-left px-4 py-4 text-[10px] font-bold uppercase tracking-wider"
                            style="color: var(--text-5)">Pelanggan</th>
                        <th class="text-left px-4 py-4 text-[10px] font-bold uppercase tracking-wider hidden lg:table-cell"
                            style="color: var(--text-5)">No. HP</th>
                        <th class="text-center px-4 py-4 text-[10px] font-bold uppercase tracking-wider w-32 hidden sm:table-cell"
                            style="color: var(--text-5)">Order</th>
                        <th class="text-right px-4 py-4 text-[10px] font-bold uppercase tracking-wider w-36 hidden md:table-cell"
                            style="color: var(--text-5)">Total Belanja</th>
                        <th class="text-center px-4 py-4 text-[10px] font-bold uppercase tracking-wider w-32"
                            style="color: var(--text-5)">Status</th>
                        <th class="text-right px-4 py-4 text-[10px] font-bold uppercase tracking-wider w-32"
                            style="color: var(--text-5)">Aksi</th>
                    </tr>
                </thead>

                {{-- Table Body --}}
                <tbody>
                    @forelse($users as $user)
                        @php
                            $orderCount = $user->orders_count ?? 0;
                            $totalSpent = $user->orders_sum_total ?? 0;
                        @endphp
                        <tr class="transition-colors border-b last:border-0"
                            style="border-color: var(--border-1)"
                            onmouseover="this.style.background='var(--bg-hover)'"
                            onmouseout="this.style.background='transparent'">

                            {{-- No --}}
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                             text-xs font-mono font-semibold border"
                                      style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-4)">
                                    {{ $users->firstItem() + $loop->index }}
                                </span>
                            </td>

                            {{-- Pelanggan --}}
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
                                                bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                                                shadow-md shadow-amber-500/20">
                                        <span class="text-xs font-bold text-slate-900">
                                            {{ strtoupper(substr($user->name ?? '?', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold flex items-center gap-1.5 truncate max-w-[260px]"
                                           style="color: var(--text-1);"
                                           title="{{ $user->name }}">
                                            <span class="truncate">{{ $user->name }}</span>
                                            @if($user->email_verified_at)
                                                <iconify-icon icon="mdi:shield-check-outline"
                                                              class="text-sm flex-shrink-0"
                                                              style="color: #60a5fa;"
                                                              title="Email terverifikasi"></iconify-icon>
                                            @endif
                                        </p>
                                        <p class="text-xs mt-0.5 truncate max-w-[260px]"
                                           style="color: var(--text-5)"
                                           title="{{ $user->email }}">
                                            {{ $user->email }}
                                        </p>
                                        {{-- Phone untuk mobile --}}
                                        <p class="text-xs mt-0.5 lg:hidden" style="color: var(--text-5)">
                                            {{ $user->phone ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- No. HP --}}
                            <td class="px-4 py-4 hidden lg:table-cell">
                                @if($user->phone)
                                    <span class="text-sm font-mono" style="color: var(--text-3);">
                                        {{ $user->phone }}
                                    </span>
                                @else
                                    <span class="text-xs" style="color: var(--text-6);">-</span>
                                @endif
                            </td>

                            {{-- Order --}}
                            <td class="px-4 py-4 text-center hidden sm:table-cell">
                                @if($orderCount > 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                                                 text-[11px] font-bold border"
                                          style="background: rgba(96,165,250,0.1); border-color: rgba(96,165,250,0.3); color: #60a5fa;">
                                        <iconify-icon icon="mdi:cart-outline"></iconify-icon>
                                        {{ $orderCount }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                                                 text-[11px] font-bold border"
                                          style="background: rgba(148,163,184,0.1); border-color: rgba(148,163,184,0.3); color: var(--text-4);">
                                        <iconify-icon icon="mdi:minus-circle-outline"></iconify-icon>
                                        0
                                    </span>
                                @endif
                            </td>

                            {{-- Total Belanja --}}
                            <td class="px-4 py-4 text-right hidden md:table-cell">
                                @if($totalSpent > 0)
                                    <span class="text-sm font-bold" style="color: #ecbc42;">
                                        Rp {{ number_format($totalSpent, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-xs" style="color: var(--text-6);">Rp 0</span>
                                @endif
                            </td>

                            {{-- Status Toggle --}}
                            <td class="px-4 py-4 text-center">
                                <button type="button"
                                        onclick="toggleUserStatus({{ $user->id }}, {{ $user->is_active ? 'false' : 'true' }})"
                                        class="relative inline-flex h-6 w-11 items-center rounded-full
                                               transition-all active:scale-95 cursor-pointer"
                                        style="background: {{ $user->is_active ? '#34d399' : '#4b5563' }};"
                                        title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <span class="absolute top-0.5 left-0.5 h-5 w-5 rounded-full
                                                 bg-white shadow transform transition-transform"
                                          style="transform: translateX({{ $user->is_active ? '20px' : '0' }});"></span>
                                </button>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 py-4 text-right">
                                <div class="flex justify-end items-center gap-1.5">
                                    {{-- Detail --}}
                                    <a href="{{ route('admin.customers.show', $user) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                              border transition-all active:scale-95"
                                       style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)"
                                       onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                                       onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'"
                                       title="Lihat detail">
                                        <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                                    </a>

                                    {{-- Delete --}}
                                    <form onsubmit="return handleDelete(event, {{ $user->id }}, '{{ addslashes($user->name) }}')"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                                       border transition-all active:scale-95
                                                       bg-red-500/5 border-red-500/20 text-red-400
                                                       hover:bg-red-500/15 hover:border-red-500/40"
                                                title="Hapus pelanggan">
                                            <iconify-icon icon="mdi:trash-can-outline"></iconify-icon>
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                    @empty
                        {{-- Empty State --}}
                        <tr>
                            <td colspan="7" class="px-6 py-20">
                                <div class="flex flex-col items-center justify-center text-center max-w-sm mx-auto">
                                    <div class="relative mb-5">
                                        <div class="w-20 h-20 rounded-full flex items-center justify-center border"
                                             style="background: var(--bg-input); border-color: var(--border-2)">
                                            <iconify-icon icon="mdi:account-multiple-outline"
                                                          class="text-4xl"
                                                          style="color: var(--text-6);"></iconify-icon>
                                        </div>
                                    </div>

                                    <h3 class="text-base font-bold mb-1.5" style="color: var(--text-1)">
                                        @if(request('search'))
                                            Pelanggan Tidak Ditemukan
                                        @else
                                            Belum Ada Pelanggan
                                        @endif
                                    </h3>
                                    <p class="text-xs mb-5" style="color: var(--text-5)">
                                        @if(request('search'))
                                            Tidak ada pelanggan yang cocok dengan pencarian "{{ request('search') }}".
                                        @else
                                            Belum ada pelanggan yang terdaftar di sistem ini.
                                        @endif
                                    </p>

                                    @if(request('search') || request('status'))
                                        <a href="{{ route('admin.customers.index') }}"
                                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg
                                                  text-xs font-bold transition-all active:scale-95 border"
                                           style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)"
                                           onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                                           onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'">
                                            <iconify-icon icon="mdi:refresh"></iconify-icon>
                                            Reset Filter
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
            <div class="px-6 py-4 border-t"
                 style="border-color: var(--border-2); background: var(--bg-input)">
                {{ $users->appends(request()->except('page'))->links() }}
            </div>
        @endif

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
function handleDelete(event, userId, userName) {
    event.preventDefault();

    if (!confirm('Hapus pelanggan "' + userName + '"?\nSemua data terkait akan dihapus.')) {
        return false;
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
            location.reload();
        } else {
            alert('Gagal menghapus pelanggan.');
        }
    })
    .catch(function(err) {
        console.error('Gagal menghapus:', err);
        alert('Terjadi kesalahan saat menghapus pelanggan.');
    });

    return false;
}
</script>
@endpush

@endsection