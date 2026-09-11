@extends('layouts.admin')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Marketplace
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Kelola marketplace tempat pelanggan dapat membeli produk toko.
            </p>
        </div>


        <a
            href="{{ route('admin.marketplaces.create') }}"
            class="inline-flex items-center justify-center gap-2
                   rounded-lg
                   bg-blue-600
                   px-4 py-2.5
                   text-sm font-semibold
                   text-white
                   transition
                   hover:bg-blue-700"
        >

            <iconify-icon
                icon="solar:add-circle-linear"
                width="20"
                height="20"
            ></iconify-icon>

            Tambah Marketplace

        </a>

    </div>


    {{-- SUCCESS MESSAGE --}}
    @if (session('success'))

        <div
            class="flex items-start gap-3
                   rounded-lg
                   border border-green-200
                   bg-green-50
                   px-4 py-3
                   text-sm text-green-700"
        >

            <iconify-icon
                icon="solar:check-circle-linear"
                width="20"
                height="20"
            ></iconify-icon>

            <span>
                {{ session('success') }}
            </span>

        </div>

    @endif


    {{-- ERROR MESSAGE --}}
    @if (session('error'))

        <div
            class="flex items-start gap-3
                   rounded-lg
                   border border-red-200
                   bg-red-50
                   px-4 py-3
                   text-sm text-red-700"
        >

            <iconify-icon
                icon="solar:danger-circle-linear"
                width="20"
                height="20"
            ></iconify-icon>

            <span>
                {{ session('error') }}
            </span>

        </div>

    @endif


    {{-- VALIDATION ERROR --}}
    @if ($errors->any())

        <div
            class="rounded-lg
                   border border-red-200
                   bg-red-50
                   px-4 py-3
                   text-sm text-red-700"
        >

            <p class="font-semibold">
                Terjadi kesalahan:
            </p>


            <ul class="mt-2 list-inside list-disc">

                @foreach ($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- TABLE CARD --}}
    <div
        class="overflow-hidden
               rounded-xl
               bg-white
               shadow-sm
               ring-1
               ring-gray-200"
    >

        {{-- TABLE HEADER --}}
        <div
            class="border-b
                   border-gray-200
                   px-6 py-4"
        >

            <div class="flex items-center justify-between">

                <div>

                    <h2
                        class="text-base
                               font-semibold
                               text-gray-900"
                    >
                        Daftar Marketplace
                    </h2>

                    <p
                        class="mt-1
                               text-sm
                               text-gray-500"
                    >
                        Total {{ $marketplaces->count() }} marketplace
                    </p>

                </div>

            </div>

        </div>


        {{-- EMPTY STATE --}}
        @if ($marketplaces->isEmpty())

            <div
                class="flex
                       flex-col
                       items-center
                       justify-center
                       px-6
                       py-16
                       text-center"
            >

                <div
                    class="flex
                           h-16
                           w-16
                           items-center
                           justify-center
                           rounded-full
                           bg-gray-100"
                >

                    <iconify-icon
                        icon="solar:shop-2-linear"
                        width="32"
                        height="32"
                        class="text-gray-400"
                    ></iconify-icon>

                </div>


                <h3
                    class="mt-4
                           text-base
                           font-semibold
                           text-gray-900"
                >
                    Belum ada marketplace
                </h3>


                <p
                    class="mt-1
                           max-w-sm
                           text-sm
                           text-gray-500"
                >
                    Tambahkan marketplace tempat pelanggan
                    dapat membeli produk toko.
                </p>


                <a
                    href="{{ route('admin.marketplaces.create') }}"
                    class="mt-5
                           inline-flex
                           items-center
                           gap-2
                           rounded-lg
                           bg-blue-600
                           px-4
                           py-2.5
                           text-sm
                           font-semibold
                           text-white
                           transition
                           hover:bg-blue-700"
                >

                    <iconify-icon
                        icon="solar:add-circle-linear"
                        width="18"
                        height="18"
                    ></iconify-icon>

                    Tambah Marketplace

                </a>

            </div>

        @else

            {{-- TABLE --}}
            <div class="overflow-x-auto">

                <table
                    class="min-w-full
                           divide-y
                           divide-gray-200"
                >

                    <thead class="bg-gray-50">

                        <tr>

                            {{-- ICON --}}

                            <th
                                class="w-20
                                       px-6
                                       py-3
                                       text-center
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wider
                                       text-gray-500"
                            >
                                Icon
                            </th>


                            {{-- MARKETPLACE --}}

                            <th
                                class="px-6
                                       py-3
                                       text-left
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wider
                                       text-gray-500"
                            >
                                Marketplace
                            </th>


                            {{-- URL --}}

                            <th
                                class="px-6
                                       py-3
                                       text-left
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wider
                                       text-gray-500"
                            >
                                URL
                            </th>


                            {{-- ORDER --}}

                            <th
                                class="w-28
                                       px-6
                                       py-3
                                       text-center
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wider
                                       text-gray-500"
                            >
                                Urutan
                            </th>


                            {{-- STATUS --}}

                            <th
                                class="w-32
                                       px-6
                                       py-3
                                       text-center
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wider
                                       text-gray-500"
                            >
                                Status
                            </th>


                            {{-- ACTION --}}

                            <th
                                class="w-48
                                       px-6
                                       py-3
                                       text-right
                                       text-xs
                                       font-semibold
                                       uppercase
                                       tracking-wider
                                       text-gray-500"
                            >
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody
                        class="divide-y
                               divide-gray-200
                               bg-white"
                    >

                        @foreach (
                            $marketplaces
                            as $marketplace
                        )

                            <tr
                                class="transition
                                       hover:bg-gray-50"
                            >

                                {{-- ICON --}}

                                <td
                                    class="px-6
                                           py-4
                                           text-center"
                                >

                                    <div
                                        class="mx-auto
                                               flex
                                               h-11
                                               w-11
                                               items-center
                                               justify-center
                                               rounded-lg
                                               border
                                               border-gray-200
                                               bg-gray-50"
                                    >

                                        <iconify-icon
                                            icon="{{ $marketplace->icon }}"
                                            width="26"
                                            height="26"
                                        ></iconify-icon>

                                    </div>

                                </td>


                                {{-- NAME --}}

                                <td class="px-6 py-4">

                                    <div>

                                        <p
                                            class="text-sm
                                                   font-semibold
                                                   text-gray-900"
                                        >
                                            {{ $marketplace->name }}
                                        </p>


                                        <p
                                            class="mt-1
                                                   text-xs
                                                   text-gray-400"
                                        >
                                            {{ $marketplace->slug }}
                                        </p>

                                    </div>

                                </td>


                                {{-- URL --}}

                                <td class="px-6 py-4">

                                    <a
                                        href="{{ $marketplace->url }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex
                                               max-w-[300px]
                                               items-center
                                               gap-1.5
                                               truncate
                                               text-sm
                                               text-blue-600
                                               hover:text-blue-800
                                               hover:underline"
                                    >

                                        <span class="truncate">
                                            {{ $marketplace->url }}
                                        </span>


                                        <iconify-icon
                                            icon="solar:arrow-up-right-linear"
                                            width="16"
                                            height="16"
                                            class="shrink-0"
                                        ></iconify-icon>

                                    </a>

                                </td>


                                {{-- SORT ORDER --}}

                                <td
                                    class="px-6
                                           py-4
                                           text-center"
                                >

                                    <span
                                        class="inline-flex
                                               h-8
                                               min-w-8
                                               items-center
                                               justify-center
                                               rounded-lg
                                               bg-gray-100
                                               px-2
                                               text-sm
                                               font-semibold
                                               text-gray-700"
                                    >
                                        {{ $marketplace->sort_order }}
                                    </span>

                                </td>


                                {{-- STATUS --}}

                                <td
                                    class="px-6
                                           py-4
                                           text-center"
                                >

                                    @if ($marketplace->is_active)

                                        <span
                                            class="inline-flex
                                                   items-center
                                                   gap-1.5
                                                   rounded-full
                                                   bg-green-50
                                                   px-3
                                                   py-1
                                                   text-xs
                                                   font-semibold
                                                   text-green-700"
                                        >

                                            <span
                                                class="h-1.5
                                                       w-1.5
                                                       rounded-full
                                                       bg-green-500"
                                            ></span>

                                            Aktif

                                        </span>

                                    @else

                                        <span
                                            class="inline-flex
                                                   items-center
                                                   gap-1.5
                                                   rounded-full
                                                   bg-gray-100
                                                   px-3
                                                   py-1
                                                   text-xs
                                                   font-semibold
                                                   text-gray-600"
                                        >

                                            <span
                                                class="h-1.5
                                                       w-1.5
                                                       rounded-full
                                                       bg-gray-400"
                                            ></span>

                                            Nonaktif

                                        </span>

                                    @endif

                                </td>


                                {{-- ACTION --}}

                                <td class="px-6 py-4">

                                    <div
                                        class="flex
                                               items-center
                                               justify-end
                                               gap-2"
                                    >

                                        {{-- EDIT --}}

                                        <a
                                            href="{{ route(
                                                'admin.marketplaces.edit',
                                                $marketplace
                                            ) }}"
                                            class="inline-flex
                                                   items-center
                                                   gap-1.5
                                                   rounded-lg
                                                   bg-blue-50
                                                   px-3
                                                   py-2
                                                   text-sm
                                                   font-medium
                                                   text-blue-600
                                                   transition
                                                   hover:bg-blue-100"
                                        >

                                            <iconify-icon
                                                icon="solar:pen-2-linear"
                                                width="17"
                                                height="17"
                                            ></iconify-icon>

                                            Edit

                                        </a>


                                        {{-- DELETE --}}

                                        <form
                                            action="{{ route(
                                                'admin.marketplaces.destroy',
                                                $marketplace
                                            ) }}"
                                            method="POST"
                                            onsubmit="return confirm(
                                                'Yakin ingin menghapus marketplace {{ addslashes($marketplace->name) }}?'
                                            )"
                                        >

                                            @csrf

                                            @method('DELETE')


                                            <button
                                                type="submit"
                                                class="inline-flex
                                                       items-center
                                                       gap-1.5
                                                       rounded-lg
                                                       bg-red-50
                                                       px-3
                                                       py-2
                                                       text-sm
                                                       font-medium
                                                       text-red-600
                                                       transition
                                                       hover:bg-red-100"
                                            >

                                                <iconify-icon
                                                    icon="solar:trash-bin-trash-linear"
                                                    width="17"
                                                    height="17"
                                                ></iconify-icon>

                                                Hapus

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @endif

    </div>

</div>

@endsection