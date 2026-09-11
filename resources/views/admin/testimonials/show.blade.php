@extends('layouts.admin')

@section('title', 'Detail Testimonial')
@section('page-title', 'Detail Testimonial')

@section('content')

<div class="w-full max-w-5xl mx-auto space-y-6">

    {{-- ============================================ --}}
    {{-- HEADER --}}
    {{-- ============================================ --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <a href="{{ route('admin.testimonials.index') }}"
               class="inline-flex items-center gap-1.5 text-xs font-semibold transition-colors"
               style="color: var(--text-5)"
               onmouseover="this.style.color='#FDDD57'"
               onmouseout="this.style.color='var(--text-5)'">
                <iconify-icon icon="mdi:arrow-left"></iconify-icon>
                Kembali ke Testimonial
            </a>
            <h1 class="text-2xl font-bold flex items-center gap-2.5 mt-2" style="color: var(--text-1)">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl
                             bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                             shadow-lg shadow-amber-500/20 flex-shrink-0">
                    <iconify-icon icon="mdi:star-outline" class="text-slate-900 text-2xl"></iconify-icon>
                </span>
                Detail Testimonial
            </h1>
            <p class="text-sm mt-1.5 ml-12" style="color: var(--text-5)">
                Lihat testimonial pelanggan secara lengkap.
            </p>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-wrap gap-2 flex-shrink-0">
            <a href="{{ route('admin.testimonials.edit', $testimonial) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg
                      text-sm font-bold transition-all active:scale-95
                      bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                      text-slate-900
                      shadow-lg shadow-amber-500/20
                      hover:shadow-xl hover:shadow-amber-500/40
                      hover:-translate-y-0.5">
                <iconify-icon icon="mdi:pencil-outline"></iconify-icon>
                Edit
            </a>
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


    {{-- ============================================ --}}
    {{-- STATUS BADGES --}}
    {{-- ============================================ --}}
    <div class="flex flex-wrap items-center gap-2">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border"
              style="background: rgba(236,188,66,0.1); border-color: rgba(236,188,66,0.3); color: #ecbc42;">
            <div class="flex text-amber-400">
                @for ($i = 1; $i <= 5; $i++)
                    @if ($i <= $testimonial->rating)
                        <iconify-icon icon="mdi:star" class="text-sm"></iconify-icon>
                    @else
                        <iconify-icon icon="mdi:star-outline" class="text-sm opacity-40"></iconify-icon>
                    @endif
                @endfor
            </div>
            <span class="font-mono">{{ $testimonial->rating }}/5</span>
        </span>

        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border"
              style="background: {{ $testimonial->is_active ? 'rgba(52,211,153,0.1)' : 'rgba(248,113,113,0.1)' }};
                     border-color: {{ $testimonial->is_active ? 'rgba(52,211,153,0.3)' : 'rgba(248,113,113,0.3)' }};
                     color: {{ $testimonial->is_active ? '#34d399' : '#f87171' }};">
            <span class="w-1.5 h-1.5 rounded-full bg-current {{ $testimonial->is_active ? 'animate-pulse' : '' }}"></span>
            {{ $testimonial->is_active ? 'Aktif' : 'Nonaktif' }}
        </span>

        @if ($testimonial->is_verified_purchase)
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border"
                  style="background: rgba(96,165,250,0.1); border-color: rgba(96,165,250,0.3); color: #60a5fa;">
                <iconify-icon icon="mdi:check-decagram"></iconify-icon>
                Verifikasi Pembelian
            </span>
        @endif
    </div>


    {{-- ============================================ --}}
    {{-- MAIN CONTENT: Testimonial --}}
    {{-- ============================================ --}}
    <div class="rounded-2xl border overflow-hidden"
         style="background: var(--bg-card); border-color: var(--border-2)">

        <div class="px-5 py-4 border-b flex items-center gap-2"
             style="background: var(--bg-input); border-color: var(--border-2)">
            <iconify-icon icon="mdi:comment-quote-outline" class="text-[#ecbc42] text-base"></iconify-icon>
            <h2 class="font-bold text-sm" style="color: var(--text-1)">Isi Testimonial</h2>
        </div>

        <div class="p-5 lg:p-6">
            <div class="flex flex-col lg:flex-row gap-6">

                {{-- Konten Testimonial --}}
                <div class="flex-1 min-w-0">

                    {{-- Title (jika ada) --}}
                    @if ($testimonial->title)
                        <h3 class="text-lg font-bold mb-3" style="color: var(--text-1);">
                            {{ $testimonial->title }}
                        </h3>
                    @endif

                    {{-- Testimonial Text --}}
                    <div class="rounded-lg p-4 border-l-4 mb-5"
                         style="background: var(--bg-input); border-color: #ecbc42;">
                        <p class="text-sm leading-relaxed italic" style="color: var(--text-2);">
                            "{{ $testimonial->testimonial }}"
                        </p>
                    </div>

                    {{-- Customer Info --}}
                    <div class="flex items-center gap-3 pt-4 border-t" style="border-color: var(--border-1);">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0
                                    bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                                    shadow-md shadow-amber-500/20">
                            <span class="text-slate-900 font-bold text-lg">
                                {{ strtoupper(substr($testimonial->customer_name, 0, 1)) }}
                            </span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-bold truncate" style="color: var(--text-1);">
                                {{ $testimonial->customer_name }}
                            </p>
                            <div class="flex items-center gap-2 text-[11px] mt-0.5" style="color: var(--text-5);">
                                <iconify-icon icon="mdi:calendar-clock-outline"></iconify-icon>
                                {{ $testimonial->published_at?->format('d M Y, H:i') ?? 'Belum diterbitkan' }}
                            </div>
                        </div>
                    </div>
                </div>


                {{-- Foto Testimonial --}}
                <div class="lg:w-80 flex-shrink-0">
                    <p class="text-[10px] font-bold uppercase tracking-wider mb-3 flex items-center gap-1.5"
                       style="color: var(--text-5);">
                        <iconify-icon icon="mdi:image-multiple-outline"></iconify-icon>
                        Foto Testimonial
                        @if($testimonial->images->isNotEmpty())
                            <span class="ml-auto text-[10px] font-mono px-2 py-0.5 rounded-full"
                                  style="background: rgba(236,188,66,0.1); color: #ecbc42;">
                                {{ $testimonial->images->count() }}
                            </span>
                        @endif
                    </p>

                    @if ($testimonial->images->isNotEmpty())
                        <div class="grid grid-cols-2 gap-2">
                            @foreach ($testimonial->images as $image)
                                <a href="{{ $image->image_url }}"
                                   target="_blank"
                                   class="group relative aspect-square rounded-lg overflow-hidden border transition-all"
                                   style="border-color: var(--border-2);"
                                   onmouseover="this.style.borderColor='#ecbc42'"
                                   onmouseout="this.style.borderColor='var(--border-2)'">
                                    <img src="{{ $image->image_url }}"
                                         alt="Foto testimonial"
                                         class="h-full w-full object-cover transition-transform group-hover:scale-110">
                                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100
                                                flex items-center justify-center transition-opacity">
                                        <iconify-icon icon="mdi:magnify-plus-outline" class="text-white text-2xl"></iconify-icon>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-10 rounded-lg border-2 border-dashed"
                             style="border-color: var(--border-2); background: var(--bg-input);">
                            <iconify-icon icon="mdi:image-off-outline" class="text-3xl mb-2" style="color: var(--text-6);"></iconify-icon>
                            <p class="text-xs" style="color: var(--text-5);">Tidak ada foto</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- INFORMASI PRODUK --}}
    {{-- ============================================ --}}
    <div class="rounded-2xl border overflow-hidden"
         style="background: var(--bg-card); border-color: var(--border-2)">

        <div class="px-5 py-4 border-b flex items-center gap-2"
             style="background: var(--bg-input); border-color: var(--border-2)">
            <iconify-icon icon="mdi:package-variant-closed" class="text-[#ecbc42] text-base"></iconify-icon>
            <h2 class="font-bold text-sm" style="color: var(--text-1)">Informasi Produk</h2>
        </div>

        <div class="p-5 lg:p-6">
            <div class="flex flex-col sm:flex-row gap-5">

                {{-- Product Image --}}
                @if ($testimonial->product && $testimonial->product->images->isNotEmpty())
                    @php $productImage = $testimonial->product->images->first(); @endphp
                    <div class="flex-shrink-0">
                        <img src="{{ asset('storage/' . $productImage->image) }}"
                             alt="{{ $testimonial->product->name }}"
                             class="h-24 w-24 rounded-xl object-cover border"
                             style="border-color: var(--border-2);">
                    </div>
                @endif

                {{-- Product Details --}}
                <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider mb-1 flex items-center gap-1.5"
                           style="color: var(--text-5);">
                            <iconify-icon icon="mdi:package-variant"></iconify-icon>
                            Produk
                        </p>
                        <p class="text-sm font-semibold" style="color: var(--text-1);">
                            {{ $testimonial->product?->name ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider mb-1 flex items-center gap-1.5"
                           style="color: var(--text-5);">
                            <iconify-icon icon="mdi:layers-outline"></iconify-icon>
                            Varian
                        </p>
                        <p class="text-sm font-semibold" style="color: var(--text-1);">
                            {{ $testimonial->variant_label ?? 'Tanpa Varian' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider mb-1 flex items-center gap-1.5"
                           style="color: var(--text-5);">
                            <iconify-icon icon="mdi:account-outline"></iconify-icon>
                            Pelanggan
                        </p>
                        <p class="text-sm font-semibold" style="color: var(--text-1);">
                            {{ $testimonial->customer_name }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- ACTIONS --}}
    {{-- ============================================ --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 pb-4">

        {{-- Toggle Status --}}
        <form action="{{ route('admin.testimonials.toggle', $testimonial) }}" method="POST" class="inline">
            @csrf
            @method('PATCH')
            <button type="submit"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg
                           text-sm font-bold transition-all active:scale-95 border"
                    style="background: rgba(251,191,36,0.05); border-color: rgba(251,191,36,0.3); color: #fbbf24;"
                    onmouseover="this.style.background='rgba(251,191,36,0.15)'; this.style.borderColor='rgba(251,191,36,0.5)'"
                    onmouseout="this.style.background='rgba(251,191,36,0.05)'; this.style.borderColor='rgba(251,191,36,0.3)'">
                <iconify-icon icon="{{ $testimonial->is_active ? 'mdi:lock-outline' : 'mdi:lock-open-outline' }}"></iconify-icon>
                {{ $testimonial->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
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
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg
                           text-sm font-bold transition-all active:scale-95
                           bg-red-500/10 border border-red-500/30 text-red-400
                           hover:bg-red-500/20 hover:border-red-500/50 hover:text-red-300">
                <iconify-icon icon="mdi:delete-outline"></iconify-icon>
                Hapus Testimonial
            </button>
        </form>
    </div>

</div>

@endsection