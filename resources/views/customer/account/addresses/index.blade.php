@extends('layouts.account')

@section('title', 'Alamat Pengiriman - Barokah Sport')
@section('page-title', 'Alamat Pengiriman')
@section('page-subtitle', 'Kelola daftar alamat pengiriman Anda.')

@section('account-content')

<style>
    /* ============================================
       ADDRESS PAGE STYLES
       ============================================ */
    .addr-wrapper {
        display: flex;
        flex-direction: column;
        gap: 1.2vw;
        margin-top:1vw;
    }

    /* --------------------------------------------
       HEADER (Judul + Tombol Tambah)
       -------------------------------------------- */
    .addr-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1vw;
        flex-wrap: wrap;
    }

    .addr-header-title {
        font-size: 0.9vw;
        font-weight: 700;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 0.4vw;
    }

    .addr-header-title iconify-icon {
        color: #ecbc42;
        font-size: 1.1vw;
    }

    .addr-header-desc {
        font-size: 0.75vw;
        color: #94a3b8;
        margin-top: 0.2vw;
    }

    /* --------------------------------------------
       BUTTONS
       -------------------------------------------- */
    .addr-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4vw;
        padding: 0.7vw 1.2vw;
        border-radius: 0.7vw;
        font-size: 0.82vw;
        font-weight: 600;
        cursor: pointer;
        border: 0.1vw solid transparent;
        transition: all 0.2s ease;
        text-decoration: none;
        font-family: inherit;
    }

    .addr-btn iconify-icon {
        font-size: 1.05vw;
    }

    .addr-btn-gold {
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
        color: rgb(102, 72, 9);
        box-shadow: 0 0.15vw 0.5vw rgba(236, 188, 66, 0.3);
    }
    .addr-btn-gold:hover {
        transform: translateY(-0.1vw);
        box-shadow: 0 0.3vw 1vw rgba(236, 188, 66, 0.4);
    }

    .addr-btn-outline {
        background: #ffffff;
        border-color: #e2e8f0;
        color: #475569;
    }
    .addr-btn-outline:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .addr-btn-danger {
        background: #fef2f2;
        border-color: #fecaca;
        color: #dc2626;
    }
    .addr-btn-danger:hover {
        background: #fee2e2;
    }

    /* --------------------------------------------
       EMPTY STATE
       -------------------------------------------- */
    .addr-empty {
        text-align: center;
        padding: 3vw 2vw;
        background: #fafbfc;
        border: 0.15vw dashed #cbd5e1;
        border-radius: 1vw;
    }

    .addr-empty-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 5vw;
        height: 5vw;
        margin: 0 auto;
        border-radius: 50%;
        background: linear-gradient(135deg, #FDDD57 0%, #ecbc42 100%);
        color: rgb(102, 72, 9);
        box-shadow: 0 0.3vw 1vw rgba(236, 188, 66, 0.3);
    }

    .addr-empty-icon iconify-icon {
        font-size: 2.5vw;
    }

    .addr-empty-title {
        font-size: 1.1vw;
        font-weight: 700;
        color: #0f172a;
        margin-top: 1.2vw;
    }

    .addr-empty-desc {
        font-size: 0.82vw;
        color: #64748b;
        margin-top: 0.5vw;
        line-height: 1.6;
    }

    .addr-empty .addr-btn {
        margin-top: 1.5vw;
    }

    /* --------------------------------------------
       ADDRESS LIST
       -------------------------------------------- */
    .addr-list {
        display: flex;
        flex-direction: column;
        gap: 1vw;
    }

    .addr-card {
        background: #ffffff;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.9vw;
        padding: 1.3vw;
        transition: all 0.25s ease;
        position: relative;
    }

    .addr-card:hover {
        border-color: #ecbc42;
        box-shadow: 0 0.4vw 1.5vw rgba(0, 0, 0, 0.06);
    }

    .addr-card.is-default {
        background: linear-gradient(135deg, #fffbf0 0%, #fff7e0 100%);
        border-color: #fde68a;
    }

    .addr-card.is-default::before {
        content: '';
        position: absolute;
        left: 0;
        top: 1vw;
        bottom: 1vw;
        width: 0.25vw;
        background: linear-gradient(180deg, #FDDD57 0%, #ecbc42 100%);
        border-radius: 0 0.25vw 0.25vw 0;
    }

    /* Head: Label + Badge */
    .addr-card-head {
        display: flex;
        align-items: center;
        gap: 0.6vw;
        flex-wrap: wrap;
        margin-bottom: 0.7vw;
    }

    .addr-label {
        display: inline-flex;
        align-items: center;
        gap: 0.4vw;
        font-size: 0.9vw;
        font-weight: 700;
        color: #0f172a;
    }

    .addr-label iconify-icon {
        color: #ecbc42;
        font-size: 1.05vw;
    }

    .addr-default-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3vw;
        padding: 0.25vw 0.7vw;
        border-radius: 100vw;
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 100%);
        color: rgb(102, 72, 9);
        font-size: 0.65vw;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        box-shadow: 0 0.1vw 0.4vw rgba(236, 188, 66, 0.35);
    }

    .addr-default-badge iconify-icon {
        font-size: 0.75vw;
    }

    /* Body: Recipient Info */
    .addr-info {
        display: flex;
        flex-direction: column;
        gap: 0.35vw;
        font-size: 0.82vw;
        color: #475569;
    }

    .addr-name-row {
        display: flex;
        align-items: center;
        gap: 0.5vw;
        flex-wrap: wrap;
    }

    .addr-name {
        font-size: 0.9vw;
        font-weight: 700;
        color: #0f172a;
    }

    .addr-divider {
        color: #cbd5e1;
        font-size: 0.85vw;
    }

    .addr-phone {
        display: inline-flex;
        align-items: center;
        gap: 0.3vw;
        color: #64748b;
        font-size: 0.82vw;
    }

    .addr-phone iconify-icon {
        font-size: 0.9vw;
    }

    .addr-detail {
        display: flex;
        align-items: flex-start;
        gap: 0.4vw;
        color: #475569;
        line-height: 1.6;
    }

    .addr-detail iconify-icon {
        font-size: 0.9vw;
        color: #94a3b8;
        flex-shrink: 0;
        margin-top: 0.15vw;
    }

    /* Actions */
    .addr-actions {
        display: flex;
        gap: 0.5vw;
        margin-top: 1vw;
        padding-top: 0.9vw;
        border-top: 0.1vw dashed #e2e8f0;
        flex-wrap: wrap;
    }

    .addr-btn-sm {
        padding: 0.5vw 0.9vw;
        font-size: 0.75vw;
        border-radius: 0.55vw;
    }

    .addr-btn-sm iconify-icon {
        font-size: 0.9vw;
    }

    /* ============================================
       RESPONSIVE - TABLET
       ============================================ */
    @media (max-width: 1024px) {
        .addr-wrapper { gap: 3vw; margin-top:1vw;}

        .addr-header {
            gap: 2.5vw;
        }

        .addr-header-title {
            font-size: 2.3vw;
            gap: 1vw;
        }
        .addr-header-title iconify-icon { font-size: 2.8vw; }

        .addr-header-desc {
            font-size: 1.9vw;
            margin-top: 0.5vw;
        }

        .addr-btn {
            padding: 2.2vw 3vw;
            border-radius: 1.5vw;
            font-size: 2vw;
            gap: 1vw;
            border-width: 0.2vw;
        }
        .addr-btn iconify-icon { font-size: 2.5vw; }

        .addr-empty {
            padding: 6vw 4vw;
            border-radius: 2.5vw;
            border-width: 0.4vw;
        }

        .addr-empty-icon {
            width: 12vw;
            height: 12vw;
        }
        .addr-empty-icon iconify-icon { font-size: 6vw; }

        .addr-empty-title {
            font-size: 2.8vw;
            margin-top: 3vw;
        }
        .addr-empty-desc {
            font-size: 2vw;
            margin-top: 1.2vw;
        }
        .addr-empty .addr-btn { margin-top: 3.5vw; }

        .addr-list { gap: 2.5vw; }

        .addr-card {
            padding: 3vw;
            border-radius: 2vw;
            border-width: 0.2vw;
        }
        .addr-card.is-default::before {
            top: 2.5vw;
            bottom: 2.5vw;
            width: 0.6vw;
            border-radius: 0 0.6vw 0.6vw 0;
        }

        .addr-card-head {
            gap: 1.5vw;
            margin-bottom: 1.7vw;
        }

        .addr-label {
            font-size: 2.3vw;
            gap: 1vw;
        }
        .addr-label iconify-icon { font-size: 2.7vw; }

        .addr-default-badge {
            padding: 0.7vw 1.7vw;
            font-size: 1.7vw;
            gap: 0.7vw;
        }
        .addr-default-badge iconify-icon { font-size: 1.9vw; }

        .addr-info {
            font-size: 2vw;
            gap: 1vw;
        }

        .addr-name-row { gap: 1.2vw; }
        .addr-name { font-size: 2.2vw; }
        .addr-divider { font-size: 2vw; }

        .addr-phone {
            font-size: 2vw;
            gap: 0.7vw;
        }
        .addr-phone iconify-icon { font-size: 2.3vw; }

        .addr-detail {
            gap: 1vw;
        }
        .addr-detail iconify-icon {
            font-size: 2.3vw;
            margin-top: 0.4vw;
        }

        .addr-actions {
            gap: 1.2vw;
            margin-top: 2.5vw;
            padding-top: 2.3vw;
            border-top-width: 0.2vw;
        }

        .addr-btn-sm {
            padding: 1.5vw 2.5vw;
            font-size: 1.9vw;
            border-radius: 1.4vw;
        }
        .addr-btn-sm iconify-icon { font-size: 2.3vw; }
    }

    /* ============================================
       RESPONSIVE - MOBILE
       ============================================ */
    @media (max-width: 480px) {
        .addr-wrapper { gap: 4vw; margin-top:3vw;}

        .addr-header {
            flex-direction: column;
            align-items: stretch;
            gap: 3vw;
        }

        .addr-header-title {
            font-size: 3.5vw;
            gap: 1.5vw;
        }
        .addr-header-title iconify-icon { font-size: 4.2vw; }

        .addr-header-desc {
            font-size: 2.8vw;
            margin-top: 1vw;
        }

        .addr-header .addr-btn {
            width: 100%;
        }

        .addr-btn {
            width: 100%;
            padding: 3.2vw 4vw;
            border-radius: 2.5vw;
            font-size: 3.2vw;
            gap: 1.5vw;
            border-width: 0.3vw;
        }
        .addr-btn iconify-icon { font-size: 4vw; }

        .addr-empty {
            padding: 10vw 5vw;
            border-radius: 3.5vw;
            border-width: 0.5vw;
        }

        .addr-empty-icon {
            width: 20vw;
            height: 20vw;
        }
        .addr-empty-icon iconify-icon { font-size: 10vw; }

        .addr-empty-title {
            font-size: 4.2vw;
            margin-top: 5vw;
        }
        .addr-empty-desc {
            font-size: 3.2vw;
            margin-top: 2vw;
            line-height: 1.7;
        }
        .addr-empty .addr-btn { margin-top: 5vw; }

        .addr-list { gap: 4vw; }

        .addr-card {
            padding: 5vw 4vw;
            border-radius: 3vw;
            border-width: 0.3vw;
        }
        .addr-card.is-default::before {
            top: 4vw;
            bottom: 4vw;
            width: 1vw;
            border-radius: 0 1vw 1vw 0;
        }

        .addr-card-head {
            gap: 2.5vw;
            margin-bottom: 3vw;
        }

        .addr-label {
            font-size: 4vw;
            gap: 1.5vw;
        }
        .addr-label iconify-icon { font-size: 5vw; }

        .addr-default-badge {
            padding: 1.2vw 3vw;
            font-size: 2.8vw;
            gap: 1vw;
        }
        .addr-default-badge iconify-icon { font-size: 3.2vw; }

        .addr-info {
            font-size: 3.2vw;
            gap: 2vw;
        }

        .addr-name-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 1vw;
        }
        .addr-name { font-size: 3.8vw; }
        .addr-divider { display: none; }

        .addr-phone {
            font-size: 3.2vw;
            gap: 1.5vw;
        }
        .addr-phone iconify-icon { font-size: 3.8vw; }

        .addr-detail {
            gap: 2vw;
            line-height: 1.7;
        }
        .addr-detail iconify-icon {
            font-size: 4vw;
            margin-top: 0.6vw;
        }

        .addr-actions {
            gap: 2.5vw;
            margin-top: 4vw;
            padding-top: 4vw;
            border-top-width: 0.3vw;
        }

        .addr-btn-sm {
            flex: 1;
            padding: 2.8vw 3.5vw;
            font-size: 3vw;
            border-radius: 2.2vw;
            gap: 1.2vw;
        }
        .addr-btn-sm iconify-icon { font-size: 3.8vw; }
    }
</style>

<div class="addr-wrapper">

    {{-- ============================================ --}}
    {{-- HEADER --}}
    {{-- ============================================ --}}
    <div class="addr-header">
        <div>
            <div class="addr-header-title">
                <iconify-icon icon="mdi:map-marker-multiple-outline"></iconify-icon>
                Daftar Alamat
            </div>
            <div class="addr-header-desc">
                Atur alamat utama dan alamat lain untuk pengiriman.
            </div>
        </div>

        <a href="{{ route('customer.addresses.create') }}" class="addr-btn addr-btn-gold">
            <iconify-icon icon="mdi:plus-circle-outline"></iconify-icon>
            Tambah Alamat
        </a>
    </div>

    {{-- ============================================ --}}
    {{-- EMPTY STATE --}}
    {{-- ============================================ --}}
    @if($addresses->isEmpty())
        <div class="addr-empty">
            <div class="addr-empty-icon">
                <iconify-icon icon="mdi:map-marker-off-outline"></iconify-icon>
            </div>
            <h4 class="addr-empty-title">Belum Ada Alamat Tersimpan</h4>
            <p class="addr-empty-desc">
                Tambahkan alamat baru untuk memudahkan proses checkout.
            </p>
            <a href="{{ route('customer.addresses.create') }}" class="addr-btn addr-btn-gold">
                <iconify-icon icon="mdi:plus-circle-outline"></iconify-icon>
                Tambah Alamat Baru
            </a>
        </div>
    @else
        {{-- ============================================ --}}
        {{-- ADDRESS LIST --}}
        {{-- ============================================ --}}
        <div class="addr-list">
            @foreach($addresses as $address)
                <div class="addr-card {{ $address->is_default ? 'is-default' : '' }}">
                    {{-- Head: Label + Badge --}}
                    <div class="addr-card-head">
                        <span class="addr-label">
                            <iconify-icon icon="mdi:tag-outline"></iconify-icon>
                            {{ $address->label ?: 'Alamat ' . $loop->iteration }}
                        </span>

                        @if($address->is_default)
                            <span class="addr-default-badge">
                                <iconify-icon icon="mdi:star"></iconify-icon>
                                Utama
                            </span>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="addr-info">
                        <div class="addr-name-row">
                            <span class="addr-name">{{ $address->recipient_name }}</span>
                            <span class="addr-divider">|</span>
                            <span class="addr-phone">
                                <iconify-icon icon="mdi:phone-outline"></iconify-icon>
                                {{ $address->recipient_phone }}
                            </span>
                        </div>

                        <div class="addr-detail">
                            <iconify-icon icon="mdi:home-outline"></iconify-icon>
                            <span>{{ $address->address }}</span>
                        </div>

                        <div class="addr-detail">
                            <iconify-icon icon="mdi:map-marker-outline"></iconify-icon>
                            <span>
                                {{ $address->city }}, {{ $address->province }} {{ $address->postal_code }}
                            </span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="addr-actions">
                        <a href="{{ route('customer.addresses.edit', ['address' => $address->id]) }}"
                           class="addr-btn addr-btn-outline addr-btn-sm">
                            <iconify-icon icon="mdi:pencil-outline"></iconify-icon>
                            Edit
                        </a>

                        <form action="{{ route('customer.addresses.destroy', ['address' => $address->id]) }}" 
                              method="POST" 
                              onsubmit="return confirm('Hapus alamat ini?');"
                              style="display:contents;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="addr-btn addr-btn-danger addr-btn-sm">
                                <iconify-icon icon="mdi:delete-outline"></iconify-icon>
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@endsection