@extends('layouts.account')

@section('title', 'Profil Saya - Barokah Sport')
@section('page-title', 'Profil Saya')
@section('page-subtitle', 'Lihat dan kelola informasi akun Anda.')

@section('account-content')

<div class="space-y-6">
    {{-- Informasi Akun --}}
    <div>
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Informasi Akun</h3>
        <div class="space-y-2">
            <div class="flex flex-wrap py-2 border-b border-gray-100">
                <span class="w-24 text-sm text-gray-500">Nama</span>
                <span class="text-sm text-gray-900 font-medium">{{ Auth::guard('customer')->user()->name ?? '-' }}</span>
            </div>
            <div class="flex flex-wrap py-2 border-b border-gray-100">
                <span class="w-24 text-sm text-gray-500">Email</span>
                <span class="text-sm text-gray-900 font-medium">{{ Auth::guard('customer')->user()->email ?? '-' }}</span>
            </div>
            <div class="flex flex-wrap py-2">
                <span class="w-24 text-sm text-gray-500">Role</span>
                <span class="text-sm text-gray-900 font-medium">{{ ucfirst(Auth::guard('customer')->user()->role ?? 'Customer') }}</span>
            </div>
        </div>
    </div>
</div>

@endsection