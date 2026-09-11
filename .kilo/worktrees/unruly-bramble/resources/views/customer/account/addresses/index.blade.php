@extends('layouts.account')

@section('title', 'Alamat Pengiriman - Barokah Sport')
@section('page-title', 'Alamat Pengiriman')
@section('page-subtitle', 'Kelola daftar alamat pengiriman Anda.')

@section('account-content')

<div class="space-y-5">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h3 class="text-sm font-semibold text-gray-700">Daftar Alamat</h3>
            <p class="text-xs text-gray-500">Atur alamat utama dan alamat lain untuk pengiriman.</p>
        </div>

        <a href="{{ route('customer.addresses.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700">
            <iconify-icon icon="mdi:plus"></iconify-icon>
            Tambah Alamat
        </a>
    </div>

    @if($addresses->isEmpty())
        <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-8 text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white text-3xl shadow-sm">
                📍
            </div>
            <h4 class="mt-4 text-lg font-semibold text-gray-900">Belum ada alamat tersimpan</h4>
            <p class="mt-2 text-sm text-gray-500">Tambahkan alamat baru untuk memudahkan proses checkout.</p>
            <a href="{{ route('customer.addresses.create') }}"
               class="mt-4 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700">
                <iconify-icon icon="mdi:plus"></iconify-icon>
                Tambah Alamat Baru
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($addresses as $address)
                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm transition hover:shadow-md">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h4 class="text-base font-semibold text-gray-900">
                                    {{ $address->label ?: 'Alamat ' . $loop->iteration }}
                                </h4>

                                @if($address->is_default)
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-emerald-700">
                                        Utama
                                    </span>
                                @endif
                            </div>

                            <div class="mt-3 space-y-2 text-sm text-gray-600">
                                <div class="flex flex-wrap gap-2">
                                    <span class="font-medium text-gray-900">{{ $address->recipient_name }}</span>
                                    <span class="text-gray-400">|</span>
                                    <span>{{ $address->recipient_phone }}</span>
                                </div>

                                <p>{{ $address->address }}</p>
                                <p>{{ $address->city }}, {{ $address->province }} {{ $address->postal_code }}</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2 sm:justify-end">
                            <a href="{{ route('customer.addresses.edit', ['address' => $address->id]) }}"
                               class="inline-flex items-center gap-1 rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-medium text-blue-700 transition hover:bg-blue-100">
                                <iconify-icon icon="mdi:pencil-outline"></iconify-icon>
                                Edit
                            </a>

                            <form action="{{ route('customer.addresses.destroy', ['address' => $address->id]) }}" method="POST" onsubmit="return confirm('Hapus alamat ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-medium text-red-600 transition hover:bg-red-100">
                                    <iconify-icon icon="mdi:delete-outline"></iconify-icon>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@endsection
