@extends('layouts.admin')

@section('content')

<div class="mx-auto max-w-5xl">

    <div class="mb-6">

        <h1 class="text-2xl font-bold text-gray-900">
            Tambah Banner
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Tambahkan banner baru untuk ditampilkan di halaman utama.
        </p>

    </div>


    <form
        action="{{ route(
            'admin.banners.store'
        ) }}"
        method="POST"
        enctype="multipart/form-data"
        class="rounded-xl
               bg-white
               p-6
               shadow-sm
               ring-1
               ring-gray-200"
    >

        @include(
            'admin.banners._form'
        )

    </form>

</div>

@endsection