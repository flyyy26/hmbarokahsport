@extends('layouts.account')

@section('title', 'Tambah Alamat - Barokah Sport')
@section('page-title', 'Tambah Alamat Baru')
@section('page-subtitle', 'Simpan alamat untuk memudahkan proses checkout.')

@section('account-content')

<style>
    /* ============================================
       ADDRESS FORM STYLES
       ============================================ */
    .af-wrapper {
        display: flex;
        flex-direction: column;
        gap: 1.3vw;
        margin-top:1vw;
    }

    .af-group {
        display: flex;
        flex-direction: column;
        gap: 0.4vw;
    }

    .af-label {
        display: flex;
        align-items: center;
        gap: 0.35vw;
        font-size: 0.85vw;
        font-weight: 600;
        color: #334155;
    }

    .af-label iconify-icon {
        color: #ecbc42;
        font-size: 1vw;
    }

    .af-label .required {
        color: #dc2626;
    }

    .af-label .optional {
        color: #94a3b8;
        font-weight: 400;
        font-size: 0.75vw;
    }

    .af-input,
    .af-textarea {
        width: 100%;
        padding: 0.85vw 1.1vw;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.6vw;
        font-size: 0.85vw;
        color: #0f172a;
        background: #ffffff;
        outline: none;
        transition: all 0.2s ease;
        font-family: inherit;
    }

    .af-input::placeholder,
    .af-textarea::placeholder {
        color: #cbd5e1;
    }

    .af-input:focus,
    .af-textarea:focus {
        border-color: #ecbc42;
        box-shadow: 0 0 0 0.25vw rgba(236, 188, 66, 0.2);
    }

    .af-input.has-error,
    .af-textarea.has-error {
        border-color: #fca5a5;
    }

    .af-input.has-error:focus,
    .af-textarea.has-error:focus {
        border-color: #dc2626;
        box-shadow: 0 0 0 0.25vw rgba(220, 38, 38, 0.15);
    }

    .af-textarea {
        resize: vertical;
        min-height: 5vw;
        line-height: 1.6;
    }

    .af-error {
        display: flex;
        align-items: center;
        gap: 0.35vw;
        font-size: 0.75vw;
        color: #dc2626;
        margin-top: 0.2vw;
    }

    .af-error iconify-icon {
        font-size: 0.9vw;
        flex-shrink: 0;
    }

    /* --------------------------------------------
       CHECKBOX "Jadikan Alamat Utama"
       -------------------------------------------- */
    .af-checkbox-wrap {
        display: flex;
        align-items: center;
        gap: 0.7vw;
        padding: 1vw 1.2vw;
        background: linear-gradient(135deg, #fffbf0 0%, #fff7e0 100%);
        border: 0.1vw solid #fde68a;
        border-radius: 0.7vw;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .af-checkbox-wrap:hover {
        border-color: #ecbc42;
        box-shadow: 0 0.15vw 0.5vw rgba(236, 188, 66, 0.2);
    }

    .af-checkbox {
        width: 1.1vw;
        height: 1.1vw;
        accent-color: #ecbc42;
        cursor: pointer;
        flex-shrink: 0;
    }

    .af-checkbox-text {
        display: flex;
        align-items: center;
        gap: 0.4vw;
        font-size: 0.85vw;
        font-weight: 500;
        color: rgb(102, 72, 9);
    }

    .af-checkbox-text iconify-icon {
        font-size: 1.05vw;
        color: #ecbc42;
    }

    /* --------------------------------------------
       ACTIONS
       -------------------------------------------- */
    .af-actions {
        display: flex;
        gap: 0.7vw;
        padding-top: 1.2vw;
        border-top: 0.1vw solid #f1f5f9;
        margin-top: 0.5vw;
    }

    .af-btn {
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4vw;
        padding: 0.9vw 1.4vw;
        border-radius: 0.7vw;
        font-size: 0.85vw;
        font-weight: 600;
        cursor: pointer;
        border: 0.1vw solid transparent;
        transition: all 0.2s ease;
        text-decoration: none;
        font-family: inherit;
    }

    .af-btn iconify-icon {
        font-size: 1.05vw;
    }

    .af-btn-outline {
        background: #ffffff;
        border-color: #e2e8f0;
        color: #475569;
    }
    .af-btn-outline:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .af-btn-gold {
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
        color: rgb(102, 72, 9);
        box-shadow: 0 0.15vw 0.5vw rgba(236, 188, 66, 0.3);
    }
    .af-btn-gold:hover {
        transform: translateY(-0.1vw);
        box-shadow: 0 0.3vw 1vw rgba(236, 188, 66, 0.4);
    }

    /* ============================================
       RESPONSIVE - TABLET
       ============================================ */
    @media (max-width: 1024px) {
        .af-wrapper { gap: 3vw; margin-top:1vw;}

        .af-group { gap: 1vw; }

        .af-label {
            font-size: 2.1vw;
            gap: 0.9vw;
        }
        .af-label iconify-icon { font-size: 2.5vw; }
        .af-label .optional { font-size: 1.8vw; }

        .af-input,
        .af-textarea {
            padding: 2.2vw 3vw;
            border-radius: 1.5vw;
            font-size: 2.2vw;
            border-width: 0.2vw;
        }

        .af-input:focus,
        .af-textarea:focus {
            box-shadow: 0 0 0 0.6vw rgba(236, 188, 66, 0.2);
        }

        .af-input.has-error:focus,
        .af-textarea.has-error:focus {
            box-shadow: 0 0 0 0.6vw rgba(220, 38, 38, 0.15);
        }

        .af-textarea {
            min-height: 15vw;
        }

        .af-error {
            font-size: 1.8vw;
            gap: 0.9vw;
            margin-top: 0.5vw;
        }
        .af-error iconify-icon { font-size: 2.2vw; }

        .af-checkbox-wrap {
            gap: 1.7vw;
            padding: 2.5vw 3vw;
            border-radius: 1.7vw;
            border-width: 0.2vw;
        }

        .af-checkbox {
            width: 2.8vw;
            height: 2.8vw;
        }

        .af-checkbox-text {
            font-size: 2.1vw;
            gap: 1vw;
        }
        .af-checkbox-text iconify-icon { font-size: 2.6vw; }

        .af-actions {
            gap: 1.7vw;
            padding-top: 3vw;
            margin-top: 1.5vw;
            border-top-width: 0.2vw;
        }

        .af-btn {
            padding: 2.3vw 3vw;
            border-radius: 1.5vw;
            font-size: 2.1vw;
            gap: 1vw;
            border-width: 0.2vw;
        }
        .af-btn iconify-icon { font-size: 2.5vw; }
    }

    /* ============================================
       RESPONSIVE - MOBILE
       ============================================ */
    @media (max-width: 480px) {
        .af-wrapper { gap: 4.5vw; margin-top:3vw;}

        .af-group { gap: 1.5vw; }

        .af-label {
            font-size: 3.2vw;
            gap: 1.2vw;
        }
        .af-label iconify-icon { font-size: 4vw; }
        .af-label .optional { font-size: 2.8vw; }

        .af-input,
        .af-textarea {
            padding: 3.2vw 3.5vw;
            border-radius: 2.5vw;
            font-size: 3.2vw;
            border-width: 0.3vw;
        }

        .af-input:focus,
        .af-textarea:focus {
            box-shadow: 0 0 0 0.9vw rgba(236, 188, 66, 0.2);
        }

        .af-input.has-error:focus,
        .af-textarea.has-error:focus {
            box-shadow: 0 0 0 0.9vw rgba(220, 38, 38, 0.15);
        }

        .af-textarea {
            min-height: 25vw;
            line-height: 1.7;
        }

        .af-error {
            font-size: 2.8vw;
            gap: 1.2vw;
            margin-top: 0.8vw;
        }
        .af-error iconify-icon { font-size: 3.4vw; }

        .af-checkbox-wrap {
            gap: 2.5vw;
            padding: 3.5vw 4vw;
            border-radius: 2.5vw;
            border-width: 0.3vw;
        }

        .af-checkbox {
            width: 4.5vw;
            height: 4.5vw;
        }

        .af-checkbox-text {
            font-size: 3.2vw;
            gap: 1.5vw;
        }
        .af-checkbox-text iconify-icon { font-size: 4vw; }

        .af-actions {
            flex-direction: column-reverse;
            gap: 2.5vw;
            padding-top: 4.5vw;
            margin-top: 2.5vw;
            border-top-width: 0.3vw;
        }

        .af-btn {
            width: 100%;
            padding: 3.5vw 4vw;
            border-radius: 2.5vw;
            font-size: 3.2vw;
            gap: 1.5vw;
            border-width: 0.3vw;
        }
        .af-btn iconify-icon { font-size: 4vw; }
    }
</style>

<form action="{{ route('customer.addresses.store') }}" method="POST" class="af-wrapper">
    @csrf

    {{-- Label --}}
    <div class="af-group">
        <label for="label" class="af-label">
            <iconify-icon icon="mdi:tag-outline"></iconify-icon>
            Label Alamat
            <span class="optional">(opsional)</span>
        </label>
        <input type="text" name="label" id="label" value="{{ old('label') }}" 
               placeholder="Contoh: Rumah, Kantor" 
               class="af-input @error('label') has-error @enderror">
        @error('label')
            <p class="af-error">
                <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                {{ $message }}
            </p>
        @enderror
    </div>

    {{-- Nama Penerima --}}
    <div class="af-group">
        <label for="recipient_name" class="af-label">
            <iconify-icon icon="mdi:account-outline"></iconify-icon>
            Nama Penerima <span class="required">*</span>
        </label>
        <input type="text" name="recipient_name" id="recipient_name" 
               value="{{ old('recipient_name', auth('customer')->user()->name) }}" 
               required 
               class="af-input @error('recipient_name') has-error @enderror">
        @error('recipient_name')
            <p class="af-error">
                <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                {{ $message }}
            </p>
        @enderror
    </div>

    {{-- Nomor Telepon --}}
    <div class="af-group">
        <label for="recipient_phone" class="af-label">
            <iconify-icon icon="mdi:phone-outline"></iconify-icon>
            Nomor Telepon <span class="required">*</span>
        </label>
        <input type="text" name="recipient_phone" id="recipient_phone" 
               value="{{ old('recipient_phone', auth('customer')->user()->phone) }}" 
               required 
               class="af-input @error('recipient_phone') has-error @enderror">
        @error('recipient_phone')
            <p class="af-error">
                <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                {{ $message }}
            </p>
        @enderror
    </div>

    {{-- Alamat Lengkap --}}
    <div class="af-group">
        <label for="address" class="af-label">
            <iconify-icon icon="mdi:home-outline"></iconify-icon>
            Alamat Lengkap <span class="required">*</span>
        </label>
        <textarea name="address" id="address" rows="3" required 
                  placeholder="Tulis nama jalan, nomor rumah, RT/RW, patokan..."
                  class="af-textarea @error('address') has-error @enderror">{{ old('address') }}</textarea>
        @error('address')
            <p class="af-error">
                <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                {{ $message }}
            </p>
        @enderror
    </div>

    @include('customer.account.addresses._region-fields')

    {{-- Kode Pos --}}
    <div class="af-group">
        <label for="postal_code" class="af-label">
            <iconify-icon icon="mdi:mailbox-outline"></iconify-icon>
            Kode Pos <span class="required">*</span>
        </label>
        <input type="text" name="postal_code" id="postal_code" 
               value="{{ old('postal_code') }}" 
               required 
               class="af-input @error('postal_code') has-error @enderror">
        @error('postal_code')
            <p class="af-error">
                <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                {{ $message }}
            </p>
        @enderror
    </div>

    {{-- Set as Default --}}
    <label for="is_default" class="af-checkbox-wrap">
        <input type="checkbox" name="is_default" id="is_default" value="1" 
               {{ old('is_default') ? 'checked' : '' }} 
               class="af-checkbox">
        <span class="af-checkbox-text">
            <iconify-icon icon="mdi:star-outline"></iconify-icon>
            Jadikan alamat utama
        </span>
    </label>

    {{-- Actions --}}
    <div class="af-actions">
        <a href="{{ route('customer.addresses.index') }}" class="af-btn af-btn-outline">
            <iconify-icon icon="mdi:arrow-left"></iconify-icon>
            Batal
        </a>
        <button type="submit" class="af-btn af-btn-gold">
            <iconify-icon icon="mdi:content-save-outline"></iconify-icon>
            Simpan Alamat
        </button>
    </div>
</form>

@endsection