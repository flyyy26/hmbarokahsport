@extends('layouts.admin')


@section('title', 'Dashboard Admin')


@section('page-title', 'Dashboard')


@section('content')


    {{-- Welcome --}}
    <div class="mb-8">

        <h2 class="text-2xl font-bold text-gray-900">

            Selamat datang,
            {{ auth()->user()->name }}

        </h2>

        <p class="text-gray-500 mt-1">

            Berikut ringkasan toko kamu hari ini.

        </p>

    </div>


    {{-- Statistics --}}
    <div
        class="grid grid-cols-1
               sm:grid-cols-2
               xl:grid-cols-4
               gap-5"
    >


        {{-- Total Produk --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-gray-500">
                        Total Produk
                    </p>

                    <p class="text-3xl font-bold mt-2">
                        0
                    </p>

                </div>

                <div
                    class="w-12 h-12 rounded-xl
                           bg-blue-100
                           flex items-center justify-center
                           text-xl"
                >
                    📦
                </div>

            </div>

        </div>


        {{-- Total Pesanan --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-gray-500">
                        Total Pesanan
                    </p>

                    <p class="text-3xl font-bold mt-2">
                        0
                    </p>

                </div>

                <div
                    class="w-12 h-12 rounded-xl
                           bg-green-100
                           flex items-center justify-center
                           text-xl"
                >
                    🛒
                </div>

            </div>

        </div>


        {{-- Total Pelanggan --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-gray-500">
                        Total Pelanggan
                    </p>

                    <p class="text-3xl font-bold mt-2">
                        0
                    </p>

                </div>

                <div
                    class="w-12 h-12 rounded-xl
                           bg-purple-100
                           flex items-center justify-center
                           text-xl"
                >
                    👤
                </div>

            </div>

        </div>


        {{-- Pendapatan --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-gray-500">
                        Pendapatan
                    </p>

                    <p class="text-3xl font-bold mt-2">
                        Rp 0
                    </p>

                </div>

                <div
                    class="w-12 h-12 rounded-xl
                           bg-yellow-100
                           flex items-center justify-center
                           text-xl"
                >
                    💰
                </div>

            </div>

        </div>


    </div>


    {{-- Recent Orders --}}
    <div class="mt-8">

        <div class="bg-white rounded-xl border border-gray-200">


            <div
                class="px-6 py-5
                       border-b border-gray-200"
            >

                <h3 class="font-semibold">

                    Pesanan Terbaru

                </h3>

            </div>


            <div
                class="p-10
                       text-center text-gray-500"
            >

                Belum ada pesanan.

            </div>


        </div>

    </div>


@endsection