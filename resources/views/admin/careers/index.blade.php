@extends('layouts.admin')

@section('title', 'Karir')
@section('page-title', 'Karir')

@section('content')

<div class="w-full space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <h1 class="text-2xl font-bold flex items-center gap-2.5" style="color: var(--text-1)">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl
                             bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                             shadow-lg shadow-amber-500/20 flex-shrink-0">
                    <iconify-icon icon="mdi:briefcase-outline" class="text-slate-900 text-2xl"></iconify-icon>
                </span>
                Karir
            </h1>
            <p class="text-sm mt-1.5 ml-12" style="color: var(--text-5)">
                Kelola lowongan kerja dan pelamar.
            </p>
        </div>

        <a href="{{ route('admin.careers.create') }}"
           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg
                  text-sm font-bold transition-all active:scale-95 flex-shrink-0
                  bg-gradient-to-r from-[#FDDD57] to-[#ecbc42] text-slate-900
                  shadow-lg shadow-amber-500/20 hover:shadow-xl hover:shadow-amber-500/40">
            <iconify-icon icon="mdi:plus-circle-outline" class="text-lg"></iconify-icon>
            Tambah Lowongan
        </a>
    </div>

    {{-- FLASH --}}
    @if(session('success'))
        <div class="flex items-start gap-3 rounded-xl px-4 py-3
                    bg-emerald-500/10 border border-emerald-500/30 text-emerald-400">
            <iconify-icon icon="mdi:check-circle-outline" class="text-xl flex-shrink-0 mt-0.5"></iconify-icon>
            <span class="text-sm">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-start gap-3 rounded-xl px-4 py-3
                    bg-red-500/10 border border-red-500/30 text-red-400">
            <iconify-icon icon="mdi:alert-circle-outline" class="text-xl flex-shrink-0 mt-0.5"></iconify-icon>
            <span class="text-sm">{{ session('error') }}</span>
        </div>
    @endif

    {{-- STATS CARDS --}}
    @php
        $totalCareers = $careers->total();
        $activeCareers = \App\Models\Career::where('is_active', true)->count();
        $featuredCareers = \App\Models\Career::where('is_featured', true)->count();
        $totalApplications = \App\Models\CareerApplication::count();
    @endphp

    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="rounded-xl border p-4 text-center" style="background: var(--bg-card); border-color: var(--border-2);">
            <p class="text-2xl font-bold" style="color: var(--text-1);">{{ $totalCareers }}</p>
            <p class="text-[11px] font-semibold uppercase tracking-wider mt-1" style="color: var(--text-5);">Total</p>
        </div>
        <div class="rounded-xl border p-4 text-center" style="background: var(--bg-card); border-color: var(--border-2);">
            <p class="text-2xl font-bold text-emerald-400">{{ $activeCareers }}</p>
            <p class="text-[11px] font-semibold uppercase tracking-wider mt-1 text-emerald-400/80">Aktif</p>
        </div>
        <div class="rounded-xl border p-4 text-center" style="background: var(--bg-card); border-color: var(--border-2);">
            <p class="text-2xl font-bold text-amber-400 flex items-center justify-center gap-1">
                {{ $featuredCareers }}
                <iconify-icon icon="mdi:star" class="text-lg"></iconify-icon>
            </p>
            <p class="text-[11px] font-semibold uppercase tracking-wider mt-1 text-amber-400/80">Featured</p>
        </div>
        <div class="rounded-xl border p-4 text-center" style="background: var(--bg-card); border-color: var(--border-2);">
            <p class="text-2xl font-bold text-blue-400">{{ $totalApplications }}</p>
            <p class="text-[11px] font-semibold uppercase tracking-wider mt-1 text-blue-400/80">Pelamar</p>
        </div>
    </div>

    {{-- FILTER & SEARCH --}}
    <div class="flex flex-col lg:flex-row lg:items-center gap-3">

        {{-- Filter Buttons --}}
        <div class="flex flex-wrap gap-2 flex-1">
            @php
                $filters = [
                    ['value' => null,       'label' => 'Semua'],
                    ['value' => 'active',   'label' => 'Aktif'],
                    ['value' => 'inactive', 'label' => 'Nonaktif'],
                    ['value' => 'featured', 'label' => 'Featured'],
                ];
            @endphp

            @foreach($filters as $filter)
                @php
                    $isActive = request('status') == $filter['value'] || ($filter['value'] === null && !request('status'));
                    $url = $filter['value']
                        ? route('admin.careers.index', ['status' => $filter['value']])
                        : route('admin.careers.index');
                @endphp

                <a href="{{ $url }}"
                   class="px-3.5 py-1.5 text-xs font-semibold rounded-lg border transition-all"
                   @if($isActive)
                        style="background: #ecbc42; color: #422006; border-color: #ecbc42;"
                   @else
                        style="background: var(--bg-input); color: var(--text-4); border-color: var(--border-2);"
                        onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                        onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-4)'"
                   @endif>
                    {{ $filter['label'] }}
                </a>
            @endforeach
        </div>

        {{-- Search --}}
        <form method="GET" class="flex gap-2">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <input type="text"
                   name="search"
                   placeholder="Cari judul atau departemen..."
                   value="{{ request('search') }}"
                   class="form-input"
                   style="padding: 0.4rem 0.85rem; font-size: 0.8rem; min-width: 240px;">

            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg
                           text-xs font-bold transition-all active:scale-95
                           bg-gradient-to-r from-[#FDDD57] to-[#ecbc42] text-slate-900
                           hover:shadow-lg hover:shadow-amber-500/30">
                <iconify-icon icon="mdi:magnify"></iconify-icon>
                Cari
            </button>
        </form>
    </div>

    {{-- TABLE --}}
    <div class="overflow-hidden rounded-xl border"
         style="background: var(--bg-card); border-color: var(--border-2)">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="border-b" style="background: var(--bg-input); border-color: var(--border-2)">
                    <tr>
                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider w-12" style="color: var(--text-5)">#</th>
                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">Lowongan</th>
                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">Tipe</th>
                        <th class="px-4 py-4 text-center text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">Pelamar</th>
                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">Deadline</th>
                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">Status</th>
                        <th class="px-4 py-4 text-right text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($careers as $career)
                        <tr class="border-b last:border-0 transition-colors"
                            style="border-color: var(--border-1)"
                            onmouseover="this.style.background='var(--bg-hover)'"
                            onmouseout="this.style.background='transparent'">

                            <td class="px-4 py-4">
                                <span class="text-xs font-mono font-bold" style="color: var(--text-5);">
                                    {{ $careers->perPage() * ($careers->currentPage() - 1) + $loop->iteration }}
                                </span>
                            </td>

                            <td class="px-4 py-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                                                bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                                                shadow-md shadow-amber-500/20 mt-0.5">
                                        <iconify-icon icon="mdi:briefcase-outline" class="text-slate-900 text-lg"></iconify-icon>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold truncate max-w-md" style="color: var(--text-1);">
                                            {{ $career->title }}
                                        </p>
                                        <div class="flex items-center gap-1.5 text-[10px] mt-0.5" style="color: var(--text-5);">
                                            <iconify-icon icon="mdi:domain"></iconify-icon>
                                            {{ $career->department ?? 'Umum' }}
                                            @if($career->location)
                                                <span class="mx-0.5">·</span>
                                                <iconify-icon icon="mdi:map-marker-outline"></iconify-icon>
                                                {{ $career->location }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border"
                                      style="background: rgba(96,165,250,0.1); border-color: rgba(96,165,250,0.3); color: #60a5fa;">
                                    <iconify-icon icon="mdi:clock-outline"></iconify-icon>
                                    {{ $career->type_label }}
                                </span>
                            </td>

                            <td class="px-4 py-4 text-center">
                                <a href="{{ route('admin.careers.applications', $career) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border transition-all"
                                   style="background: rgba(236,188,66,0.1); border-color: rgba(236,188,66,0.3); color: #ecbc42;"
                                   onmouseover="this.style.borderColor='#ecbc42'"
                                   onmouseout="this.style.borderColor='rgba(236,188,66,0.3)'"
                                   title="Lihat pelamar">
                                    <iconify-icon icon="mdi:account-multiple-outline"></iconify-icon>
                                    {{ $career->applications_count }}
                                </a>
                            </td>

                            <td class="px-4 py-4">
                                @if($career->deadline)
                                    <div class="text-xs font-medium" style="color: var(--text-2);">
                                        {{ $career->deadline->format('d M Y') }}
                                    </div>
                                    @if($career->is_expired)
                                        <span class="text-[10px] font-bold text-red-400">Kadaluarsa</span>
                                    @else
                                        <span class="text-[10px]" style="color: var(--text-5);">
                                            {{ $career->days_left }} hari lagi
                                        </span>
                                    @endif
                                @else
                                    <span class="text-xs" style="color: var(--text-6);">-</span>
                                @endif
                            </td>

                            <td class="px-4 py-4">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    @if($career->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border"
                                              style="background: rgba(52,211,153,0.1); border-color: rgba(52,211,153,0.3); color: #34d399;">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border"
                                              style="background: rgba(148,163,184,0.1); border-color: rgba(148,163,184,0.3); color: var(--text-4);">
                                            <span class="w-1.5 h-1.5 rounded-full" style="background: var(--text-5);"></span>
                                            Nonaktif
                                        </span>
                                    @endif

                                    @if($career->is_featured)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border"
                                              style="background: rgba(251,191,36,0.1); border-color: rgba(251,191,36,0.3); color: #fbbf24;">
                                            <iconify-icon icon="mdi:star"></iconify-icon>
                                            Featured
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-4 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.careers.applications', $career) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                              text-xs font-semibold border transition-all active:scale-95"
                                       style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)"
                                       onmouseover="this.style.borderColor='#60a5fa'; this.style.color='#60a5fa'"
                                       onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'"
                                       title="Pelamar">
                                        <iconify-icon icon="mdi:account-multiple-outline"></iconify-icon>
                                    </a>

                                    <a href="{{ route('admin.careers.edit', $career) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                              text-xs font-semibold border transition-all active:scale-95"
                                       style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)"
                                       onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                                       onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'"
                                       title="Edit">
                                        <iconify-icon icon="mdi:pencil-outline"></iconify-icon>
                                    </a>

                                    <form action="{{ route('admin.careers.destroy', $career) }}"
                                          method="POST"
                                          class="inline"
                                          onsubmit="return confirm('Hapus lowongan ini beserta semua lamarannya?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                                       text-xs font-semibold border transition-all active:scale-95
                                                       bg-red-500/5 border-red-500/20 text-red-400
                                                       hover:bg-red-500/10 hover:border-red-500/40 hover:text-red-300"
                                                title="Hapus">
                                            <iconify-icon icon="mdi:delete-outline"></iconify-icon>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-20">
                                <div class="flex flex-col items-center justify-center text-center max-w-sm mx-auto">
                                    <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4 border"
                                         style="background: var(--bg-input); border-color: var(--border-2)">
                                        <iconify-icon icon="mdi:briefcase-outline" class="text-2xl" style="color: var(--text-6)"></iconify-icon>
                                    </div>
                                    <p class="text-sm font-semibold mb-1" style="color: var(--text-3)">Belum ada lowongan</p>
                                    <p class="text-xs mb-4" style="color: var(--text-5)">Mulai tambahkan lowongan pertama untuk karier di toko Anda.</p>
                                    <a href="{{ route('admin.careers.create') }}"
                                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg
                                              text-xs font-bold transition-all active:scale-95
                                              bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                                              text-slate-900 hover:shadow-lg hover:shadow-amber-500/30">
                                        <iconify-icon icon="mdi:plus-circle-outline"></iconify-icon>
                                        Tambah Lowongan
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($careers->hasPages())
            <div class="border-t px-6 py-4" style="border-color: var(--border-2); background: var(--bg-input)">
                {{ $careers->links() }}
            </div>
        @endif
    </div>
</div>

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
    .form-input::placeholder { color: var(--text-6); }
</style>

@endsection