@extends('layouts.account')

@section('title', 'Tambah Alamat - Barokah Sport')
@section('page-title', 'Tambah Alamat Baru')
@section('page-subtitle', 'Simpan alamat untuk memudahkan proses checkout.')

@section('account-content')

<form action="{{ route('customer.addresses.store') }}" method="POST" class="space-y-5">
    @csrf

    {{-- Label --}}
    <div>
        <label for="label" class="mb-1 block text-sm font-medium text-gray-700">Label Alamat <span class="text-gray-400">(opsional)</span></label>
        <input type="text" name="label" id="label" value="{{ old('label') }}" 
               placeholder="Contoh: Rumah, Kantor" 
               class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition">
    </div>

    {{-- Nama Penerima --}}
    <div>
        <label for="recipient_name" class="mb-1 block text-sm font-medium text-gray-700">Nama Penerima <span class="text-red-500">*</span></label>
        <input type="text" name="recipient_name" id="recipient_name" 
               value="{{ old('recipient_name', auth('customer')->user()->name) }}" 
               required 
               class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition @error('recipient_name') border-red-400 @enderror">
        @error('recipient_name') 
            <p class="mt-1 text-sm text-red-500">{{ $message }}</p> 
        @enderror
    </div>

    {{-- Nomor Telepon --}}
    <div>
        <label for="recipient_phone" class="mb-1 block text-sm font-medium text-gray-700">Nomor Telepon <span class="text-red-500">*</span></label>
        <input type="text" name="recipient_phone" id="recipient_phone" 
               value="{{ old('recipient_phone', auth('customer')->user()->phone) }}" 
               required 
               class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition @error('recipient_phone') border-red-400 @enderror">
        @error('recipient_phone') 
            <p class="mt-1 text-sm text-red-500">{{ $message }}</p> 
        @enderror
    </div>

    {{-- Alamat Lengkap --}}
    <div>
        <label for="address" class="mb-1 block text-sm font-medium text-gray-700">Alamat Lengkap <span class="text-red-500">*</span></label>
        <textarea name="address" id="address" rows="3" required 
                  class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition @error('address') border-red-400 @enderror">{{ old('address') }}</textarea>
        @error('address') 
            <p class="mt-1 text-sm text-red-500">{{ $message }}</p> 
        @enderror
    </div>

    @include('customer.account.addresses._region-fields')

    {{-- Kode Pos --}}
    <div>
        <label for="postal_code" class="mb-1 block text-sm font-medium text-gray-700">Kode Pos <span class="text-red-500">*</span></label>
        <input type="text" name="postal_code" id="postal_code" 
               value="{{ old('postal_code') }}" 
               required 
               class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition @error('postal_code') border-red-400 @enderror">
        @error('postal_code') 
            <p class="mt-1 text-sm text-red-500">{{ $message }}</p> 
        @enderror
    </div>

    {{-- Set as Default --}}
    <div class="flex items-center gap-3">
        <input type="checkbox" name="is_default" id="is_default" value="1" 
               {{ old('is_default') ? 'checked' : '' }} 
               class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
        <label for="is_default" class="text-sm text-gray-600">Jadikan alamat utama</label>
    </div>

    {{-- Actions --}}
    <div class="flex gap-3 pt-2">
        <a href="{{ route('customer.account') }}" 
           class="flex-1 rounded-lg border border-gray-200 bg-white py-3 text-center text-sm font-medium text-gray-700 transition hover:bg-gray-50">
            Batal
        </a>
        <button type="submit" 
                class="flex-1 rounded-lg bg-blue-600 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
            Simpan Alamat
        </button>
    </div>
</form>

@endsection