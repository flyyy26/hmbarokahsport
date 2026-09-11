@extends('layouts.admin')

@section('title', 'Edit Kategori')

@section('page-title', 'Edit Kategori')

@section('content')

    <div class="max-w-3xl">

        <div class="mb-6">

            <h2 class="text-2xl font-bold">
                Edit Kategori
            </h2>

            <p class="text-gray-500 mt-1">
                Perbarui informasi kategori.
            </p>

        </div>


        <div
            class="bg-white
                   border border-gray-200
                   rounded-xl
                   p-6"
        >

            <form
                action="{{ route(
                    'admin.categories.update',
                    $category
                ) }}"
                method="POST"
                enctype="multipart/form-data"
            >

                @csrf

                @method('PUT')


                @include(
                    'admin.categories._form'
                )


                <div
                    class="flex
                           justify-end
                           gap-3
                           mt-8
                           pt-6
                           border-t"
                >

                    <a
                        href="{{ route(
                            'admin.categories.index'
                        ) }}"
                        class="px-5 py-3
                               bg-gray-100
                               hover:bg-gray-200
                               rounded-lg"
                    >
                        Batal
                    </a>


                    <button
                        type="submit"
                        class="px-5 py-3
                               bg-blue-600
                               hover:bg-blue-700
                               text-white
                               rounded-lg"
                    >
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection