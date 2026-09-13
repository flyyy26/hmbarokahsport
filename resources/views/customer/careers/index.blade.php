@extends('layouts.customer')

@section('title', 'Karir - Barokah Sport')

@section('content')

<style>
    /* ============================================
       CAREER INDEX
       ============================================ */
    .career-container {
        width: 100%;
        padding: 3vw 15vw;
        background: #f8fafc;
    }

    .career-hero {
        text-align: center;
        padding: 2vw 0 3vw;
    }

    .career-hero h1 {
        font-size: 2.5vw;
        font-weight: 800;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    .career-hero h1 span { color: #ecbc42; }

    .career-hero p {
        font-size: 0.9vw;
        color: #64748b;
        margin-top: 0.5vw;
        line-height: 1.6;
        max-width: 40vw;
        margin-left: auto;
        margin-right: auto;
    }

    /* FILTER */
    .career-filter {
        display: flex;
        gap: 0.8vw;
        margin-bottom: 2vw;
        padding: 1vw;
        background: #ffffff;
        border: 0.1vw solid #e2e8f0;
        border-radius: 1vw;
        flex-wrap: wrap;
        align-items: center;
    }

    .career-filter input,
    .career-filter select {
        padding: 0.7vw 1vw;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.6vw;
        font-size: 0.82vw;
        outline: none;
        color: #0f172a;
        font-family: inherit;
        background: #ffffff;
        transition: all 0.2s ease;
    }

    .career-filter input:focus,
    .career-filter select:focus {
        border-color: #ecbc42;
        box-shadow: 0 0 0 0.2vw rgba(236, 188, 66, 0.15);
    }

    .career-filter input { flex: 1; min-width: 12vw; }

    .career-filter button {
        padding: 0.7vw 1.5vw;
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
        color: rgb(102, 72, 9);
        border: none;
        border-radius: 0.6vw;
        font-size: 0.82vw;
        font-weight: 700;
        cursor: pointer;
        font-family: inherit;
        display: inline-flex;
        align-items: center;
        gap: 0.4vw;
    }

    /* GRID */
    .career-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.2vw;
    }

    .career-card {
        background: #ffffff;
        border: 0.1vw solid #e2e8f0;
        border-radius: 1vw;
        padding: 1.3vw;
        display: flex;
        flex-direction: column;
        gap: 0.8vw;
        transition: all 0.25s ease;
        position: relative;
    }

    .career-card:hover {
        border-color: #ecbc42;
        box-shadow: 0 0.5vw 1.5vw rgba(236, 188, 66, 0.12);
        transform: translateY(-0.2vw);
    }

    .career-card.featured::before {
        content: 'FEATURED';
        position: absolute;
        top: -0.6vw;
        right: 1vw;
        padding: 0.2vw 0.8vw;
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 100%);
        color: rgb(102, 72, 9);
        font-size: 0.6vw;
        font-weight: 800;
        letter-spacing: 0.08em;
        border-radius: 100vw;
        box-shadow: 0 0.2vw 0.6vw rgba(236, 188, 66, 0.4);
    }

    .career-card-top {
        display: flex;
        align-items: flex-start;
        gap: 0.8vw;
    }

    .career-card-icon {
        width: 3.2vw;
        height: 3.2vw;
        border-radius: 0.7vw;
        background: linear-gradient(135deg, #FDDD57 0%, #ecbc42 100%);
        color: rgb(102, 72, 9);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 0.2vw 0.6vw rgba(236, 188, 66, 0.3);
    }

    .career-card-icon iconify-icon { font-size: 1.7vw; }

    .career-card-info { min-width: 0; flex: 1; }

    .career-card-title {
        font-size: 0.95vw;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.35;
        margin-bottom: 0.25vw;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .career-card-dept {
        font-size: 0.72vw;
        color: #94a3b8;
        font-weight: 500;
    }

    .career-card-desc {
        font-size: 0.78vw;
        color: #64748b;
        line-height: 1.55;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .career-card-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4vw;
        padding-top: 0.6vw;
        border-top: 0.1vw dashed #e2e8f0;
    }

    .career-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.25vw;
        padding: 0.25vw 0.7vw;
        border-radius: 100vw;
        font-size: 0.65vw;
        font-weight: 600;
        background: #f1f5f9;
        color: #475569;
    }

    .career-tag iconify-icon { font-size: 0.8vw; }

    .career-tag.tag-type    { background: #eff6ff; color: #1d4ed8; }
    .career-tag.tag-level   { background: #fdf4ff; color: #a855f7; }
    .career-tag.tag-deadline { background: #fff7ed; color: #c2410c; }

    .career-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5vw;
        padding-top: 0.7vw;
        margin-top: auto;
    }

    .career-salary {
        font-size: 0.78vw;
        font-weight: 700;
        color: rgb(102, 72, 9);
    }

    .career-apply-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.3vw;
        padding: 0.55vw 1vw;
        border-radius: 0.5vw;
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
        color: rgb(102, 72, 9);
        font-size: 0.75vw;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .career-apply-btn:hover {
        box-shadow: 0 0.3vw 1vw rgba(236, 188, 66, 0.4);
        transform: translateY(-0.1vw);
    }

    .career-apply-btn iconify-icon { font-size: 0.9vw; }

    /* EMPTY */
    .career-empty {
        grid-column: 1 / -1;
        text-align: center;
        padding: 5vw 2vw;
        background: #ffffff;
        border: 0.1vw dashed #e2e8f0;
        border-radius: 1vw;
    }

    .career-empty iconify-icon {
        font-size: 4vw;
        color: #cbd5e1;
    }

    .career-empty h3 {
        font-size: 1.1vw;
        font-weight: 700;
        color: #0f172a;
        margin-top: 1vw;
    }

    .career-empty p {
        font-size: 0.82vw;
        color: #94a3b8;
        margin-top: 0.4vw;
    }

    /* PAGINATION */
    .career-pagination {
        display: flex;
        justify-content: center;
        margin-top: 2vw;
    }

    /* ============================================
       RESPONSIVE
       ============================================ */
    @media (max-width: 768px) {
        .career-container { padding: 6vw 4vw; }

        .career-hero { padding: 4vw 0 6vw; }
        .career-hero h1 {
            font-size: 6.5vw;
            width: 70%;
            margin: auto;
        }
        .career-hero p {
            font-size: 3.2vw;
            margin-top: 2vw;
            max-width: 100%;
            line-height: 1.7;
        }

        .career-filter {
            flex-direction: column;
            align-items: stretch;
            gap: 2.5vw;
            padding: 3vw;
            border-radius: 2.5vw;
            margin-bottom: 5vw;
        }

        .career-filter input,
        .career-filter select {
            padding: 3vw 4vw;
            border-radius: 2.5vw;
            font-size: 3.2vw;
            min-width: 0;
        }

        .career-filter button {
            padding: 3.2vw 4vw;
            border-radius: 2.5vw;
            font-size: 3.4vw;
            justify-content: center;
        }

        .career-grid {
            grid-template-columns: 1fr;
            gap: 4vw;
        }

        .career-card {
            border-radius: 3vw;
            padding: 4.5vw 4vw;
            gap: 3vw;
        }

        .career-card.featured::before {
            top: -2vw;
            right: 4vw;
            padding: 1vw 3vw;
            font-size: 2.5vw;
            border-radius: 100vw;
        }

        .career-card-top { gap: 3vw; }

        .career-card-icon {
            width: 12vw;
            height: 12vw;
            border-radius: 2.5vw;
        }
        .career-card-icon iconify-icon { font-size: 6vw; }

        .career-card-title { font-size: 3.8vw; margin-bottom: 1vw; }
        .career-card-dept { font-size: 3vw; }
        .career-card-desc { font-size: 3.2vw; }

        .career-card-meta {
            gap: 1.5vw;
            padding-top: 2.5vw;
        }

        .career-tag {
            padding: 1vw 2.5vw;
            font-size: 2.8vw;
            gap: 1vw;
        }
        .career-tag iconify-icon { font-size: 3.5vw; }

        .career-card-footer {
            gap: 2vw;
            padding-top: 3vw;
        }

        .career-salary { font-size: 3.2vw; }

        .career-apply-btn {
            padding: 2.2vw 3.5vw;
            border-radius: 2vw;
            font-size: 3.2vw;
            gap: 1.2vw;
        }
        .career-apply-btn iconify-icon { font-size: 4vw; }

        .career-empty { padding: 12vw 5vw; border-radius: 3vw; }
        .career-empty iconify-icon { font-size: 14vw; }
        .career-empty h3 { font-size: 4.2vw; margin-top: 3vw; }
        .career-empty p { font-size: 3.2vw; margin-top: 1.5vw; }

        .career-pagination { margin-top: 6vw; }
    }
</style>

<div class="career-container">

    {{-- HERO --}}
    <div class="career-hero">
        <h1>Bergabung dengan <span>Tim Kami</span></h1>
        <p>
            Kami mencari individu berbakat dan bersemangat untuk tumbuh bersama Barokah Sport.
            Temukan posisi yang sesuai dengan keahlianmu.
        </p>
    </div>

    {{-- FILTER --}}
    <form method="GET" class="career-filter">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari posisi...">

        <select name="department">
            <option value="">Semua Departemen</option>
            @foreach($departments as $dep)
                <option value="{{ $dep }}" {{ request('department') === $dep ? 'selected' : '' }}>
                    {{ $dep }}
                </option>
            @endforeach
        </select>

        <select name="type">
            <option value="">Semua Tipe</option>
            <option value="full_time" {{ request('type') === 'full_time' ? 'selected' : '' }}>Full Time</option>
            <option value="part_time" {{ request('type') === 'part_time' ? 'selected' : '' }}>Part Time</option>
            <option value="contract" {{ request('type') === 'contract' ? 'selected' : '' }}>Kontrak</option>
            <option value="internship" {{ request('type') === 'internship' ? 'selected' : '' }}>Magang</option>
            <option value="freelance" {{ request('type') === 'freelance' ? 'selected' : '' }}>Freelance</option>
        </select>

        <button type="submit">
            <iconify-icon icon="mdi:magnify"></iconify-icon>
            Cari
        </button>
    </form>

    {{-- GRID --}}
    <div class="career-grid">
        @forelse($careers as $career)
            <div class="career-card {{ $career->is_featured ? 'featured' : '' }}">
                <div class="career-card-top">
                    <div class="career-card-icon">
                        <iconify-icon icon="mdi:briefcase-outline"></iconify-icon>
                    </div>
                    <div class="career-card-info">
                        <div class="career-card-title">{{ $career->title }}</div>
                        <div class="career-card-dept">
                            {{ $career->department ?? 'Umum' }}
                            @if($career->location)
                                · {{ $career->location }}
                            @endif
                        </div>
                    </div>
                </div>

                @if($career->short_description)
                    <div class="career-card-desc">{{ $career->short_description }}</div>
                @endif

                <div class="career-card-meta">
                    <span class="career-tag tag-type">
                        <iconify-icon icon="mdi:clock-outline"></iconify-icon>
                        {{ $career->type_label }}
                    </span>
                    <span class="career-tag tag-level">
                        <iconify-icon icon="mdi:chart-line"></iconify-icon>
                        {{ $career->level_label }}
                    </span>
                    @if($career->deadline && !$career->is_expired)
                        <span class="career-tag tag-deadline">
                            <iconify-icon icon="mdi:calendar-clock"></iconify-icon>
                            {{ $career->days_left }} hari lagi
                        </span>
                    @endif
                </div>

                <div class="career-card-footer">
                    @if($career->salary_range)
                        <span class="career-salary">{{ $career->salary_range }}</span>
                    @else
                        <span class="career-salary" style="color: #94a3b8;">Gaji kompetitif</span>
                    @endif

                    <a href="{{ route('customer.careers.show', $career->slug) }}" class="career-apply-btn">
                        Detail
                        <iconify-icon icon="mdi:arrow-right"></iconify-icon>
                    </a>
                </div>
            </div>
        @empty
            <div class="career-empty">
                <iconify-icon icon="mdi:briefcase-outline"></iconify-icon>
                <h3>Belum Ada Lowongan</h3>
                <p>Saat ini tidak ada lowongan yang tersedia. Cek kembali nanti!</p>
            </div>
        @endforelse
    </div>

    {{-- PAGINATION --}}
    @if($careers->hasPages())
        <div class="career-pagination">
            {{ $careers->links() }}
        </div>
    @endif
</div>

@endsection