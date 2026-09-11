@extends('layouts.admin')

@section('title', 'Tambah Voucher Promo')
@section('page-title', 'Tambah Voucher')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Buat Voucher Baru</h1>
        <p class="mt-1 text-sm text-gray-500">Atur kode promo, batas belanja, dan periode masa berlaku voucher.</p>
    </div>

    <form action="{{ route('admin.vouchers.store') }}" method="POST">
        @csrf
        @include('admin.vouchers._form', ['voucher' => null])
    </form>
</div>
@endsection