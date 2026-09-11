@extends('layouts.admin')

@section('content')

<div class="mx-auto max-w-3xl space-y-6">

    <div>

        <h1 class="text-2xl font-bold text-gray-900">
            Tambah Marketplace
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Tambahkan marketplace tempat pelanggan
            dapat membeli produk.
        </p>

    </div>


    <form
        action="{{ route(
            'admin.marketplaces.store'
        ) }}"
        method="POST"
    >

        @csrf


        <div
            class="rounded-xl
                   bg-white
                   p-6
                   shadow-sm
                   ring-1
                   ring-gray-200"
        >

            @include(
                'admin.marketplaces._form'
            )


            <div
                class="mt-8
                       flex
                       items-center
                       justify-end
                       gap-3
                       border-t
                       border-gray-200
                       pt-6"
            >

                <a
                    href="{{ route(
                        'admin.marketplaces.index'
                    ) }}"
                    class="rounded-lg
                           border
                           border-gray-300
                           px-4 py-2.5
                           text-sm
                           font-semibold
                           text-gray-700
                           hover:bg-gray-50"
                >
                    Batal
                </a>


                <button
                    type="submit"
                    class="rounded-lg
                           bg-blue-600
                           px-5 py-2.5
                           text-sm
                           font-semibold
                           text-white
                           hover:bg-blue-700"
                >
                    Simpan Marketplace
                </button>

            </div>

        </div>

    </form>

</div>

@endsection