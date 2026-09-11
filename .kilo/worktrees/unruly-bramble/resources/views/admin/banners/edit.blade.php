@extends('layouts.admin')

@section('content')

<div class="mx-auto max-w-5xl">

    <div class="mb-6">

        <h1 class="text-2xl font-bold text-gray-900">
            Edit Banner
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Perbarui informasi banner.
        </p>

    </div>


    <form
        action="{{ route(
            'admin.banners.update',
            $banner
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

        @csrf

        @method('PUT')


        @include(
            'admin.banners._form'
        )

    </form>

</div>

@endsection