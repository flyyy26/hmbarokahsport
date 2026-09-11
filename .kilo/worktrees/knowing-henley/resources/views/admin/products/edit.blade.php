@extends('layouts.admin')

@section('content')

<div class="mx-auto max-w-6xl">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Produk</h1>
        <p class="mt-1 text-sm text-gray-500">Perbarui informasi produk, gambar, opsi, dan varian.</p>
    </div>

    <form id="product-form" action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        @include('admin.products._form', [
            'isEdit' => true,
            'product' => $product,
            'existingOptions' => $existingOptions ?? [],
            'existingVariants' => $existingVariants ?? [],
            'features' => $features ?? [],
            'categories' => $categories ?? [],
        ])
    </form>

</div>

@endsection