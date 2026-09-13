@extends('layouts.admin')

@section('title', 'Detail Pelamar - ' . $application->full_name)
@section('page-title', 'Detail Pelamar')

@section('content')

<div class="w-full max-w-4xl mx-auto space-y-5">

    {{-- HEADER --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <a href="{{ route('admin.careers.applications', $career) }}"
               class="inline-flex items-center gap-1.5 text-xs font-semibold mb-2 transition-colors"
               style="color: var(--text-5);"
               onmouseover="this.style.color='#ecbc42'"
               onmouseout="this.style.color='var(--text-5)'">
                <iconify-icon icon="mdi:arrow-left"></iconify-icon>
                Kembali ke Daftar Pelamar
            </a>
            <h1 class="text-2xl font-bold" style="color: var(--text-1)">
                Detail Pelamar
            </h1>
            <p class="text-sm mt-1" style="color: var(--text-5)">
                Melamar untuk: <strong style="color: var(--text-3);">{{ $career->title }}</strong>
            </p>
        </div>

        {{-- WHATSAPP CTA --}}
        @if($application->whatsapp_url)
            <a href="{{ $application->whatsapp_url }}" target="_blank"
               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg
                      text-sm font-bold transition-all active:scale-95 flex-shrink-0
                      shadow-lg shadow-emerald-500/20 hover:shadow-xl hover:shadow-emerald-500/40"
               style="background: #25d366; color: #ffffff;">
                <iconify-icon icon="mdi:whatsapp" class="text-lg"></iconify-icon>
                Hubungi via WhatsApp
            </a>
        @endif
    </div>

    {{-- PROFILE CARD --}}
    <div class="rounded-2xl border p-5" style="background: var(--bg-card); border-color: var(--border-2)">
        <div class="flex items-start gap-4">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center flex-shrink-0
                        bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                        shadow-lg shadow-amber-500/20">
                <span class="text-2xl font-bold text-slate-900">
                    {{ strtoupper(substr($application->full_name, 0, 1)) }}
                </span>
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-lg font-bold" style="color: var(--text-1)">
                    {{ $application->full_name }}
                </h2>
                <p class="text-sm" style="color: var(--text-5)">
                    {{ $application->email }}
                </p>
                <div class="flex flex-wrap items-center gap-3 mt-2">
                    <span class="inline-flex items-center gap-1 text-xs font-semibold" style="color: var(--text-3);">
                        <iconify-icon icon="mdi:phone-outline" class="text-[#ecbc42]"></iconify-icon>
                        {{ $application->formatted_phone }}
                    </span>
                    <span class="text-[10px]" style="color: var(--text-5);">
                        Melamar {{ $application->created_at->diffForHumans() }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- DATA PRIBADI --}}
    <div class="rounded-2xl border overflow-hidden" style="background: var(--bg-card); border-color: var(--border-2)">
        <div class="px-5 py-3 border-b flex items-center gap-2"
             style="background: var(--bg-input); border-color: var(--border-2)">
            <iconify-icon icon="mdi:account-details-outline" class="text-[#ecbc42]"></iconify-icon>
            <h2 class="font-bold text-sm" style="color: var(--text-1)">Data Pribadi</h2>
        </div>
        <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">

            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">Nama Lengkap</p>
                <p class="text-sm font-semibold mt-1" style="color: var(--text-1)">{{ $application->full_name }}</p>
            </div>

            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">Email</p>
                <p class="text-sm font-semibold mt-1" style="color: var(--text-1)">{{ $application->email }}</p>
            </div>

            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">Telepon</p>
                <p class="text-sm font-semibold mt-1" style="color: var(--text-1)">{{ $application->formatted_phone }}</p>
            </div>

            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">Tanggal Lahir</p>
                <p class="text-sm font-semibold mt-1" style="color: var(--text-1)">
                    {{ $application->birth_date?->format('d M Y') ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">Jenis Kelamin</p>
                <p class="text-sm font-semibold mt-1" style="color: var(--text-1)">
                    {{ $application->gender === 'male' ? 'Laki-laki' : ($application->gender === 'female' ? 'Perempuan' : '-') }}
                </p>
            </div>

            <div class="md:col-span-2">
                <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">Alamat</p>
                <p class="text-sm mt-1" style="color: var(--text-2)">{{ $application->address ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- PENDIDIKAN --}}
    <div class="rounded-2xl border overflow-hidden" style="background: var(--bg-card); border-color: var(--border-2)">
        <div class="px-5 py-3 border-b flex items-center gap-2"
             style="background: var(--bg-input); border-color: var(--border-2)">
            <iconify-icon icon="mdi:school-outline" class="text-[#ecbc42]"></iconify-icon>
            <h2 class="font-bold text-sm" style="color: var(--text-1)">Pendidikan & Pengalaman</h2>
        </div>
        <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">

            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">Pendidikan</p>
                <p class="text-sm font-semibold mt-1" style="color: var(--text-1)">{{ $application->last_education ?? '-' }}</p>
            </div>

            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">Jurusan</p>
                <p class="text-sm font-semibold mt-1" style="color: var(--text-1)">{{ $application->major ?? '-' }}</p>
            </div>

            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">Pengalaman</p>
                <p class="text-sm font-semibold mt-1" style="color: var(--text-1)">
                    {{ $application->experience_years }} tahun
                </p>
            </div>
        </div>
    </div>

    {{-- COVER LETTER --}}
    @if($application->cover_letter)
        <div class="rounded-2xl border overflow-hidden" style="background: var(--bg-card); border-color: var(--border-2)">
            <div class="px-5 py-3 border-b flex items-center gap-2"
                 style="background: var(--bg-input); border-color: var(--border-2)">
                <iconify-icon icon="mdi:email-open-outline" class="text-[#ecbc42]"></iconify-icon>
                <h2 class="font-bold text-sm" style="color: var(--text-1)">Cover Letter</h2>
            </div>
            <div class="p-5">
                <p class="text-sm leading-relaxed whitespace-pre-line" style="color: var(--text-2)">{{ $application->cover_letter }}</p>
            </div>
        </div>
    @endif

    {{-- BERKAS --}}
    <div class="rounded-2xl border overflow-hidden" style="background: var(--bg-card); border-color: var(--border-2)">
        <div class="px-5 py-3 border-b flex items-center gap-2"
             style="background: var(--bg-input); border-color: var(--border-2)">
            <iconify-icon icon="mdi:paperclip" class="text-[#ecbc42]"></iconify-icon>
            <h2 class="font-bold text-sm" style="color: var(--text-1)">Berkas</h2>
        </div>
        <div class="p-5 space-y-3">
            @if($application->cv_url)
                <a href="{{ $application->cv_url }}" target="_blank"
                   class="flex items-center gap-3 p-3 rounded-lg border transition-all"
                   style="background: var(--bg-input); border-color: var(--border-2)"
                   onmouseover="this.style.borderColor='#ecbc42'"
                   onmouseout="this.style.borderColor='var(--border-2)'">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 bg-red-500/10">
                        <iconify-icon icon="mdi:file-pdf-box" class="text-red-400 text-lg"></iconify-icon>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold" style="color: var(--text-1)">CV / Resume</p>
                        <p class="text-[10px]" style="color: var(--text-5)">Klik untuk buka</p>
                    </div>
                    <iconify-icon icon="mdi:open-in-new" style="color: var(--text-5)"></iconify-icon>
                </a>
            @endif

            @if($application->portfolio_url)
                <a href="{{ $application->portfolio_url }}" target="_blank"
                   class="flex items-center gap-3 p-3 rounded-lg border transition-all"
                   style="background: var(--bg-input); border-color: var(--border-2)"
                   onmouseover="this.style.borderColor='#60a5fa'"
                   onmouseout="this.style.borderColor='var(--border-2)'">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 bg-blue-500/10">
                        <iconify-icon icon="mdi:folder-account-outline" class="text-blue-400 text-lg"></iconify-icon>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold" style="color: var(--text-1)">Portfolio</p>
                        <p class="text-[10px]" style="color: var(--text-5)">Klik untuk buka</p>
                    </div>
                    <iconify-icon icon="mdi:open-in-new" style="color: var(--text-5)"></iconify-icon>
                </a>
            @endif
        </div>
    </div>
</div>

@endsection