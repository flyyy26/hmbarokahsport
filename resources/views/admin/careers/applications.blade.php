@extends('layouts.admin')

@section('title', 'Pelamar - ' . $career->title)
@section('page-title', 'Pelamar')

@section('content')

<div class="w-full space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <a href="{{ route('admin.careers.index') }}"
               class="inline-flex items-center gap-1.5 text-xs font-semibold mb-2 transition-colors"
               style="color: var(--text-5);"
               onmouseover="this.style.color='#ecbc42'"
               onmouseout="this.style.color='var(--text-5)'">
                <iconify-icon icon="mdi:arrow-left"></iconify-icon>
                Kembali ke Daftar Karir
            </a>
            <h1 class="text-2xl font-bold flex items-center gap-2.5" style="color: var(--text-1)">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl
                             bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                             shadow-lg shadow-amber-500/20 flex-shrink-0">
                    <iconify-icon icon="mdi:account-multiple-outline" class="text-slate-900 text-2xl"></iconify-icon>
                </span>
                Daftar Pelamar
            </h1>
            <p class="text-sm mt-1.5 ml-12 line-clamp-1" style="color: var(--text-5)">
                {{ $career->title }} · {{ $applications->total() }} pelamar
            </p>
        </div>

        <a href="{{ route('admin.careers.edit', $career) }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg
                  text-xs font-bold transition-all active:scale-95 flex-shrink-0 border"
           style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)">
            <iconify-icon icon="mdi:pencil-outline"></iconify-icon>
            Edit Lowongan
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

    {{-- SEARCH --}}
    <form method="GET" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari nama, email, atau no HP..."
               class="form-input flex-1"
               style="padding: 0.5rem 0.9rem; font-size: 0.85rem;">
        <button type="submit"
                class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg
                       text-xs font-bold transition-all active:scale-95
                       bg-gradient-to-r from-[#FDDD57] to-[#ecbc42] text-slate-900
                       hover:shadow-lg hover:shadow-amber-500/30">
            <iconify-icon icon="mdi:magnify"></iconify-icon>
            Cari
        </button>
    </form>

    {{-- TABLE --}}
    <div class="overflow-hidden rounded-xl border"
         style="background: var(--bg-card); border-color: var(--border-2)">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="border-b" style="background: var(--bg-input); border-color: var(--border-2)">
                    <tr>
                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider w-12" style="color: var(--text-5)">#</th>
                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">Pelamar</th>
                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">Kontak</th>
                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">Pendidikan</th>
                        <th class="px-4 py-4 text-center text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">Pengalaman</th>
                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">Tanggal</th>
                        <th class="px-4 py-4 text-right text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $app)
                        <tr class="border-b last:border-0 transition-colors"
                            style="border-color: var(--border-1)"
                            onmouseover="this.style.background='var(--bg-hover)'"
                            onmouseout="this.style.background='transparent'">

                            <td class="px-4 py-4">
                                <span class="text-xs font-mono font-bold" style="color: var(--text-5);">
                                    {{ $applications->perPage() * ($applications->currentPage() - 1) + $loop->iteration }}
                                </span>
                            </td>

                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0
                                                bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]">
                                        <span class="text-slate-900 font-bold text-xs">
                                            {{ strtoupper(substr($app->full_name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold truncate" style="color: var(--text-1);">
                                            {{ $app->full_name }}
                                        </p>
                                        @if($app->gender)
                                            <p class="text-[10px]" style="color: var(--text-5);">
                                                {{ $app->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                <div class="text-xs" style="color: var(--text-3);">
                                    <div class="flex items-center gap-1">
                                        <iconify-icon icon="mdi:email-outline" class="text-[#ecbc42]"></iconify-icon>
                                        {{ $app->email }}
                                    </div>
                                    <div class="flex items-center gap-1 mt-1" style="color: var(--text-5);">
                                        <iconify-icon icon="mdi:phone-outline"></iconify-icon>
                                        {{ $app->formatted_phone }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                @if($app->last_education)
                                    <div class="text-xs" style="color: var(--text-3);">
                                        <div>{{ $app->last_education }}</div>
                                        @if($app->major)
                                            <div class="text-[10px]" style="color: var(--text-5);">{{ $app->major }}</div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs" style="color: var(--text-6);">-</span>
                                @endif
                            </td>

                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border"
                                      style="background: rgba(96,165,250,0.1); border-color: rgba(96,165,250,0.3); color: #60a5fa;">
                                    {{ $app->experience_years }} th
                                </span>
                            </td>

                            <td class="px-4 py-4">
                                <div class="text-xs" style="color: var(--text-3);">
                                    {{ $app->created_at->format('d M Y') }}
                                </div>
                                <div class="text-[10px]" style="color: var(--text-5);">
                                    {{ $app->created_at->diffForHumans() }}
                                </div>
                            </td>

                            <td class="px-4 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">

                                    {{-- WhatsApp --}}
                                    @if($app->whatsapp_url)
                                        <a href="{{ $app->whatsapp_url }}" target="_blank"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                                  text-xs font-semibold border transition-all"
                                           style="background: rgba(37,211,102,0.1); border-color: rgba(37,211,102,0.3); color: #25d366;"
                                           title="Hubungi via WhatsApp">
                                            <iconify-icon icon="mdi:whatsapp"></iconify-icon>
                                        </a>
                                    @endif

                                    {{-- Detail --}}
                                    <a href="{{ route('admin.careers.applications.show', ['career' => $career, 'application' => $app]) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                              text-xs font-semibold border transition-all"
                                       style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)"
                                       onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                                       onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'"
                                       title="Lihat detail">
                                        <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                                    </a>

                                    {{-- Hapus --}}
                                    <form action="{{ route('admin.careers.applications.destroy', ['career' => $career, 'application' => $app]) }}"
                                          method="POST"
                                          onsubmit="return confirm('Hapus lamaran ini?')"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                                       text-xs font-semibold border transition-all
                                                       bg-red-500/5 border-red-500/20 text-red-400 hover:bg-red-500/10"
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
                                        <iconify-icon icon="mdi:account-off-outline" class="text-2xl" style="color: var(--text-6)"></iconify-icon>
                                    </div>
                                    <p class="text-sm font-semibold mb-1" style="color: var(--text-3)">
                                        Belum ada pelamar
                                    </p>
                                    <p class="text-xs" style="color: var(--text-5)">
                                        Belum ada yang melamar posisi ini.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($applications->hasPages())
            <div class="border-t px-6 py-4" style="border-color: var(--border-2); background: var(--bg-input)">
                {{ $applications->links() }}
            </div>
        @endif
    </div>
</div>

<style>
    .form-input {
        width: 100%;
        background: var(--bg-input);
        border: 1px solid var(--border-2);
        border-radius: 0.6rem;
        color: var(--text-1);
        outline: none;
        transition: all 0.2s ease;
        font-family: inherit;
    }
    .form-input:focus {
        border-color: #ecbc42;
        box-shadow: 0 0 0 3px rgba(236, 188, 66, 0.15);
    }
</style>

@endsection