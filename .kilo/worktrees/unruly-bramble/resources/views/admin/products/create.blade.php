@extends('layouts.admin')

@section('title', 'Tambah Produk')

@section('page-title', 'Tambah Produk')

@section('content')

<div class="mx-auto max-w-6xl">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Tambah Produk</h1>
        <p class="mt-1 text-sm text-gray-500">Tambahkan produk baru ke katalog.</p>
    </div>

    <form id="product-form" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('admin.products._form', [
            'isEdit' => false,
            'product' => null,
            'existingOptions' => [],
            'existingVariants' => [],
            'features' => $features ?? [],
            'categories' => $categories ?? [],
        ])
    </form>

</div>

@endsection