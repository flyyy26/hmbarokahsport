@extends('layouts.admin')

@section('title', 'Manajemen Testimonial')
@section('page-title', 'Testimonial')

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
                    <iconify-icon icon="mdi:star-outline" class="text-slate-900 text-2xl"></iconify-icon>
                </span>
                Manajemen Testimonial
            </h1>
            <p class="text-sm mt-1.5 ml-12" style="color: var(--text-5)">
                Kelola testimonial dan ulasan pelanggan untuk setiap produk.
            </p>
        </div>

        <a href="{{ route('admin.testimonials.create') }}"
           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg
                  text-sm font-bold transition-all active:scale-95 flex-shrink-0
                  bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                  text-slate-900
                  shadow-lg shadow-amber-500/20
                  hover:shadow-xl hover:shadow-amber-500/40
                  hover:-translate-y-0.5">
            <iconify-icon icon="mdi:plus-circle-outline" class="text-lg"></iconify-icon>
            Tambah Testimonial
        </a>
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
    {{-- STATS CARDS --}}
    {{-- ============================================ --}}
    @php
        $totalTestimonials = $testimonials->total();
        $activeTestimonials = $testimonials->where('is_active', true)->count();
        $verifiedTestimonials = $testimonials->where('is_verified_purchase', true)->count();
        $averageRating = $testimonials->count() > 0 ? round($testimonials->avg('rating'), 1) : 0;
    @endphp

    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="rounded-xl border p-4 text-center transition-colors"
             style="background: var(--bg-card); border-color: var(--border-2);"
             onmouseover="this.style.borderColor='rgba(236,188,66,0.3)'"
             onmouseout="this.style.borderColor='var(--border-2)'">
            <p class="text-2xl font-bold" style="color: var(--text-1);">{{ $totalTestimonials }}</p>
            <p class="text-[11px] font-semibold uppercase tracking-wider mt-1" style="color: var(--text-5);">Total</p>
        </div>
        <div class="rounded-xl border p-4 text-center transition-colors"
             style="background: var(--bg-card); border-color: var(--border-2);"
             onmouseover="this.style.borderColor='rgba(52,211,153,0.3)'"
             onmouseout="this.style.borderColor='var(--border-2)'">
            <p class="text-2xl font-bold text-emerald-400">{{ $activeTestimonials }}</p>
            <p class="text-[11px] font-semibold uppercase tracking-wider mt-1 text-emerald-400/80">Aktif</p>
        </div>
        <div class="rounded-xl border p-4 text-center transition-colors"
             style="background: var(--bg-card); border-color: var(--border-2);"
             onmouseover="this.style.borderColor='rgba(96,165,250,0.3)'"
             onmouseout="this.style.borderColor='var(--border-2)'">
            <p class="text-2xl font-bold text-blue-400">{{ $verifiedTestimonials }}</p>
            <p class="text-[11px] font-semibold uppercase tracking-wider mt-1 text-blue-400/80">Terverifikasi</p>
        </div>
        <div class="rounded-xl border p-4 text-center transition-colors"
             style="background: var(--bg-card); border-color: var(--border-2);"
             onmouseover="this.style.borderColor='rgba(236,188,66,0.3)'"
             onmouseout="this.style.borderColor='var(--border-2)'">
            <p class="text-2xl font-bold flex items-center justify-center gap-1" style="color: #ecbc42;">
                {{ $averageRating }}
                <iconify-icon icon="mdi:star" class="text-lg"></iconify-icon>
            </p>
            <p class="text-[11px] font-semibold uppercase tracking-wider mt-1" style="color: rgba(236,188,66,0.8);">Rata-rata</p>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- FILTER & SEARCH --}}
    {{-- ============================================ --}}
    <div class="rounded-xl border p-5"
         style="background: var(--bg-card); border-color: var(--border-2)">

        <form method="GET" action="{{ route('admin.testimonials.index') }}">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                {{-- Produk --}}
                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:package-variant" class="text-[#ecbc42]"></iconify-icon>
                        Produk
                    </label>
                    <select name="product_id" class="form-input">
                        <option value="">Semua Produk</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Rating --}}
                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:star-outline" class="text-[#ecbc42]"></iconify-icon>
                        Rating
                    </label>
                    <select name="rating" class="form-input">
                        <option value="">Semua Rating</option>
                        <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>⭐ 5 Bintang</option>
                        <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>⭐ 4 Bintang</option>
                        <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>⭐ 3 Bintang</option>
                        <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>⭐ 2 Bintang</option>
                        <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>⭐ 1 Bintang</option>
                    </select>
                </div>

                {{-- Status --}}
                <div>
                    <label class="form-label">
                        <iconify-icon icon="mdi:state-machine" class="text-[#ecbc42]"></iconify-icon>
                        Status
                    </label>
                    <select name="status" class="form-input">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                {{-- Actions --}}
                <div class="flex items-end gap-2">
                    <button type="submit"
                            class="inline-flex flex-1 items-center justify-center gap-1.5 px-4 py-2.5 rounded-lg
                                   text-xs font-bold transition-all active:scale-95
                                   bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                                   text-slate-900
                                   hover:shadow-lg hover:shadow-amber-500/30">
                        <iconify-icon icon="mdi:filter-outline"></iconify-icon>
                        Filter
                    </button>

                    @if(request()->hasAny(['product_id', 'rating', 'status']))
                        <a href="{{ route('admin.testimonials.index') }}"
                           class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg
                                  text-xs font-semibold transition-all active:scale-95 border"
                           style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-4)"
                           onmouseover="this.style.borderColor='#f87171'; this.style.color='#f87171'"
                           onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-4)'"
                           title="Reset Filter">
                            <iconify-icon icon="mdi:close-circle-outline" class="text-lg"></iconify-icon>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>


    {{-- ============================================ --}}
    {{-- TABLE --}}
    {{-- ============================================ --}}
    <div class="overflow-hidden rounded-xl border"
         style="background: var(--bg-card); border-color: var(--border-2)">

        <div class="overflow-x-auto">
            <table class="min-w-full">

                {{-- Table Header --}}
                <thead class="border-b"
                       style="background: var(--bg-input); border-color: var(--border-2)">
                    <tr>
                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider w-12" style="color: var(--text-5)">#</th>
                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Produk / Varian
                        </th>
                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Pelanggan
                        </th>
                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Rating
                        </th>
                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Testimonial
                        </th>
                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Foto
                        </th>
                        <th class="px-4 py-4 text-left text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Status
                        </th>
                        <th class="px-4 py-4 text-right text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">
                            Aksi
                        </th>
                    </tr>
                </thead>

                {{-- Table Body --}}
                <tbody>
                    @forelse ($testimonials as $testimonial)
                        <tr class="transition-colors border-b last:border-0"
                            style="border-color: var(--border-1)"
                            onmouseover="this.style.background='var(--bg-hover)'"
                            onmouseout="this.style.background='transparent'">

                            {{-- No --}}
                            <td class="px-4 py-4">
                                <span class="text-xs font-mono font-bold" style="color: var(--text-5);">
                                    {{ $testimonials->perPage() * ($testimonials->currentPage() - 1) + $loop->iteration }}
                                </span>
                            </td>

                            {{-- Produk / Varian --}}
                            <td class="px-4 py-4">
                                <p class="text-sm font-semibold truncate max-w-[200px]" style="color: var(--text-1);">
                                    {{ $testimonial->product?->name ?? '-' }}
                                </p>
                                @if ($testimonial->variant)
                                    <p class="text-[11px] mt-0.5" style="color: var(--text-5);">
                                        {{ $testimonial->variant_label }}
                                    </p>
                                @endif
                            </td>

                            {{-- Pelanggan --}}
                            <td class="px-4 py-4">
                                <div class="flex flex-col gap-1">
                                    @if ($testimonial->is_verified_purchase)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold
                                                     bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 w-fit">
                                            <iconify-icon icon="mdi:check-decagram"></iconify-icon>
                                            Terverifikasi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold
                                                     bg-slate-500/10 border border-slate-500/30 w-fit"
                                              style="color: var(--text-4);">
                                            <iconify-icon icon="mdi:close-circle-outline"></iconify-icon>
                                            Belum Diverifikasi
                                        </span>
                                    @endif
                                    <p class="text-sm font-semibold" style="color: var(--text-1);">
                                        {{ $testimonial->customer_name }}
                                    </p>
                                </div>
                            </td>

                            {{-- Rating --}}
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-1">
                                    <div class="flex text-amber-400">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $testimonial->rating)
                                                <iconify-icon icon="mdi:star" class="text-base"></iconify-icon>
                                            @else
                                                <iconify-icon icon="mdi:star-outline" class="text-base opacity-30"></iconify-icon>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-[10px] mt-0.5 font-mono" style="color: var(--text-5);">
                                    {{ $testimonial->rating }}/5
                                </p>
                            </td>

                            {{-- Testimonial --}}
                            <td class="px-4 py-4 max-w-xs">
                                @if ($testimonial->title)
                                    <p class="text-sm font-semibold truncate" style="color: var(--text-1);">
                                        {{ $testimonial->title }}
                                    </p>
                                @endif
                                <p class="text-xs truncate" style="color: var(--text-4);">
                                    {{ Str::limit(strip_tags($testimonial->testimonial), 60) }}
                                </p>
                            </td>

                            {{-- Foto --}}
                            <td class="px-4 py-4">
                                @if ($testimonial->images->isNotEmpty())
                                    <div class="flex items-center gap-1.5">
                                        @foreach ($testimonial->images->take(3) as $image)
                                            <img src="{{ $image->image_url }}"
                                                 alt="Foto testimonial"
                                                 class="h-10 w-10 rounded-lg object-cover border"
                                                 style="border-color: var(--border-2);">
                                        @endforeach
                                        @if ($testimonial->images->count() > 3)
                                            <span class="inline-flex items-center justify-center h-10 w-10 rounded-lg
                                                         text-[10px] font-bold border"
                                                  style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-4);">
                                                +{{ $testimonial->images->count() - 3 }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs" style="color: var(--text-6);">
                                        <iconify-icon icon="mdi:image-off-outline"></iconify-icon>
                                        -
                                    </span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-4">
                                @if ($testimonial->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border"
                                          style="background: rgba(52,211,153,0.1); border-color: rgba(52,211,153,0.3); color: #34d399;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border"
                                          style="background: rgba(248,113,113,0.1); border-color: rgba(248,113,113,0.3); color: #f87171;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">

                                    {{-- View --}}
                                    <a href="{{ route('admin.testimonials.show', $testimonial) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                              text-xs font-semibold border transition-all active:scale-95"
                                       style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)"
                                       onmouseover="this.style.borderColor='#60a5fa'; this.style.color='#60a5fa'"
                                       onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'"
                                       title="Lihat">
                                        <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('admin.testimonials.edit', $testimonial) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                              text-xs font-semibold border transition-all active:scale-95"
                                       style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)"
                                       onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                                       onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'"
                                       title="Edit">
                                        <iconify-icon icon="mdi:pencil-outline"></iconify-icon>
                                    </a>

                                    {{-- Toggle --}}
                                    <form action="{{ route('admin.testimonials.toggle', $testimonial) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                                       text-xs font-semibold border transition-all active:scale-95"
                                                style="background: rgba(251,191,36,0.05); border-color: rgba(251,191,36,0.2); color: #fbbf24;"
                                                onmouseover="this.style.background='rgba(251,191,36,0.15)'; this.style.borderColor='rgba(251,191,36,0.4)'"
                                                onmouseout="this.style.background='rgba(251,191,36,0.05)'; this.style.borderColor='rgba(251,191,36,0.2)'"
                                                title="{{ $testimonial->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <iconify-icon icon="{{ $testimonial->is_active ? 'mdi:lock-outline' : 'mdi:lock-open-outline' }}"></iconify-icon>
                                        </button>
                                    </form>

                                    {{-- Delete --}}
                                    <form action="{{ route('admin.testimonials.destroy', $testimonial) }}"
                                          method="POST"
                                          class="inline"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus testimonial ini? Semua foto akan dihapus juga.')">
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
                            <td colspan="8" class="px-6 py-20">
                                <div class="flex flex-col items-center justify-center text-center max-w-sm mx-auto">
                                    <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4 border"
                                         style="background: var(--bg-input); border-color: var(--border-2)">
                                        <iconify-icon icon="mdi:star-outline" class="text-2xl" style="color: var(--text-6)"></iconify-icon>
                                    </div>
                                    <p class="text-sm font-semibold mb-1" style="color: var(--text-3)">
                                        Belum ada testimonial
                                    </p>
                                    <p class="text-xs mb-4" style="color: var(--text-5)">
                                        Mulai kumpulkan ulasan pelanggan untuk meningkatkan kepercayaan.
                                    </p>
                                    <a href="{{ route('admin.testimonials.create') }}"
                                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg
                                              text-xs font-bold transition-all active:scale-95
                                              bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                                              text-slate-900
                                              hover:shadow-lg hover:shadow-amber-500/30">
                                        <iconify-icon icon="mdi:plus-circle-outline"></iconify-icon>
                                        Tambah Testimonial
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($testimonials->hasPages())
            <div class="border-t px-6 py-4"
                 style="border-color: var(--border-2); background: var(--bg-input)">
                {{ $testimonials->links() }}
            </div>
        @endif
    </div>

</div>

{{-- ============================================ --}}
{{-- STYLES --}}
{{-- ============================================ --}}
<style>
    .form-input {
        width: 100%;
        padding: 0.6rem 0.85rem;
        background: var(--bg-input);
        border: 1px solid var(--border-2);
        border-radius: 0.65rem;
        font-size: 0.8rem;
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
        font-size: 0.7rem;
        font-weight: 600;
        color: var(--text-3);
        margin-bottom: 0.4rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
</style>

@endsection