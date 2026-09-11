@extends('layouts.admin')

@section('title', 'Edit Voucher Promo')
@section('page-title', 'Edit Voucher')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Edit Voucher: {{ $voucher->code }}</h1>
        <p class="mt-1 text-sm text-gray-500">Perbarui informasi diskon, kuota, atau syarat ketentuan voucher.</p>
    </div>

    <form action="{{ route('admin.vouchers.update', $voucher->id) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.vouchers._form', ['voucher' => $voucher])
    </form>
</div>
@endsection