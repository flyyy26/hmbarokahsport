@extends('layouts.admin')

@section('title', 'Tambah Kategori')

@section('page-title', 'Tambah Kategori')

@section('content')

    <div class="max-w-3xl">

        <div class="mb-6">

            <h2 class="text-2xl font-bold">
                Tambah Kategori
            </h2>

            <p class="text-gray-500 mt-1">
                Buat kategori produk baru.
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
                    'admin.categories.store'
                ) }}"
                method="POST"
                enctype="multipart/form-data"
            >

                @csrf


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
                        Simpan Kategori
                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection