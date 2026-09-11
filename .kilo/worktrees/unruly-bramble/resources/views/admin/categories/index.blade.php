@extends('layouts.admin')

@section('title', 'Kategori')

@section('page-title', 'Kategori')

@section('content')

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row
                    sm:items-center
                    sm:justify-between gap-4">

            <div>

                <h2 class="text-2xl font-bold">
                    Kategori
                </h2>

                <p class="text-gray-500 mt-1">
                    Kelola kategori produk toko.
                </p>

            </div>


            <a
                href="{{ route('admin.categories.create') }}"
                class="inline-flex items-center justify-center
                       px-5 py-3 bg-blue-600
                       hover:bg-blue-700
                       text-white font-medium
                       rounded-lg"
            >
                + Tambah Kategori
            </a>

        </div>


        {{-- Success Message --}}
        @if(session('success'))

            <div
                class="bg-green-50
                       border border-green-200
                       text-green-700
                       px-4 py-3
                       rounded-lg"
            >

                {{ session('success') }}

            </div>

        @endif


        {{-- Table --}}
        <div
            class="bg-white
                   border border-gray-200
                   rounded-xl
                   overflow-hidden"
        >

            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-gray-50 border-b">

                        <tr>

                            <th class="text-left px-6 py-4">
                                #
                            </th>

                            <th class="text-left px-6 py-4">
                                Kategori
                            </th>

                            <th class="text-left px-6 py-4">
                                Slug
                            </th>

                            <th class="text-left px-6 py-4">
                                Status
                            </th>

                            <th class="text-right px-6 py-4">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y">

                        @forelse($categories as $category)

                            <tr class="hover:bg-gray-50">

                                <td class="px-6 py-4">

                                    {{ $categories->firstItem() + $loop->index }}

                                </td>


                                <td class="px-6 py-4">

                                    <div class="font-medium">

                                        {{ $category->name }}

                                    </div>

                                    @if($category->description)

                                        <div
                                            class="text-xs
                                                   text-gray-500
                                                   mt-1"
                                        >

                                            {{ Str::limit(
                                                $category->description,
                                                60
                                            ) }}

                                        </div>

                                    @endif

                                </td>


                                <td class="px-6 py-4 text-gray-500">

                                    {{ $category->slug }}

                                </td>


                                <td class="px-6 py-4">

                                    @if($category->is_active)

                                        <span
                                            class="inline-flex
                                                   px-3 py-1
                                                   rounded-full
                                                   text-xs
                                                   font-medium
                                                   bg-green-100
                                                   text-green-700"
                                        >

                                            Aktif

                                        </span>

                                    @else

                                        <span
                                            class="inline-flex
                                                   px-3 py-1
                                                   rounded-full
                                                   text-xs
                                                   font-medium
                                                   bg-gray-100
                                                   text-gray-600"
                                        >

                                            Nonaktif

                                        </span>

                                    @endif

                                </td>


                                <td class="px-6 py-4">

                                    <div
                                        class="flex
                                               justify-end
                                               gap-2"
                                    >

                                        <a
                                            href="{{ route(
                                                'admin.categories.edit',
                                                $category
                                            ) }}"
                                            class="px-3 py-2
                                                   text-blue-600
                                                   bg-blue-50
                                                   rounded-lg"
                                        >
                                            Edit
                                        </a>


                                        <form
                                            action="{{ route(
                                                'admin.categories.destroy',
                                                $category
                                            ) }}"
                                            method="POST"
                                        >

                                            @csrf

                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                onclick="
                                                    return confirm(
                                                        'Hapus kategori ini?'
                                                    )
                                                "
                                                class="px-3 py-2
                                                       text-red-600
                                                       bg-red-50
                                                       rounded-lg"
                                            >
                                                Hapus
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="5"
                                    class="px-6 py-12
                                           text-center
                                           text-gray-500"
                                >

                                    Belum ada kategori.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Pagination --}}
            @if($categories->hasPages())

                <div
                    class="px-6 py-4
                           border-t"
                >

                    {{ $categories->links() }}

                </div>

            @endif

        </div>

    </div>

@endsection