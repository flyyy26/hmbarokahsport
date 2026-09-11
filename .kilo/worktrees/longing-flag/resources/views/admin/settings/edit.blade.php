@extends('layouts.admin')

@section('content')

<div class="mx-auto max-w-5xl space-y-6">

    {{-- HEADER --}}

    <div>

        <h1 class="text-2xl font-bold text-gray-900">
            Pengaturan Toko
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Kelola informasi utama yang akan ditampilkan
            pada website toko.
        </p>

    </div>


    {{-- SUCCESS --}}

    @if (session('success'))

        <div
            class="flex items-start gap-3
                   rounded-lg
                   border border-green-200
                   bg-green-50
                   px-4 py-3
                   text-sm text-green-700"
        >

            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="mt-0.5 h-5 w-5 shrink-0"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M5 13l4 4L19 7"
                />
            </svg>

            <span>
                {{ session('success') }}
            </span>

        </div>

    @endif


    {{-- ERROR --}}

    @if (session('error'))

        <div
            class="rounded-lg
                   border border-red-200
                   bg-red-50
                   px-4 py-3
                   text-sm text-red-700"
        >
            {{ session('error') }}
        </div>

    @endif


    {{-- VALIDATION --}}

    @if ($errors->any())

        <div
            class="rounded-lg
                   border border-red-200
                   bg-red-50
                   px-4 py-3
                   text-sm text-red-700"
        >

            <p class="font-semibold">
                Terdapat kesalahan:
            </p>

            <ul class="mt-2 list-inside list-disc">

                @foreach (
                    $errors->all()
                    as $error
                )

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- FORM --}}

    <form
        action="{{ route(
            'admin.settings.update'
        ) }}"
        method="POST"
        enctype="multipart/form-data"
        class="space-y-6"
    >

        @csrf

        @method('PUT')


        {{-- INFORMASI TOKO --}}

        <div
            class="rounded-xl
                   bg-white
                   p-6
                   shadow-sm
                   ring-1
                   ring-gray-200"
        >

            <div class="mb-6">

                <h2
                    class="text-base
                           font-semibold
                           text-gray-900"
                >
                    Informasi Toko
                </h2>

                <p
                    class="mt-1
                           text-sm
                           text-gray-500"
                >
                    Informasi dasar mengenai toko kamu.
                </p>

            </div>


            <div class="space-y-5">


                {{-- STORE NAME --}}

                <div>

                    <label
                        class="block
                               text-sm
                               font-medium
                               text-gray-700"
                    >
                        Nama Toko
                    </label>


                    <input
                        type="text"
                        name="store_name"
                        value="{{ old(
                            'store_name',
                            $setting?->store_name
                        ) }}"
                        class="mt-2
                               block
                               w-full
                               rounded-lg
                               border-gray-300
                               shadow-sm
                               focus:border-blue-500
                               focus:ring-blue-500"
                        placeholder="Contoh: Toko Saya"
                    >

                </div>


                {{-- DESCRIPTION --}}

                <div>

                    <label
                        class="block
                               text-sm
                               font-medium
                               text-gray-700"
                    >
                        Deskripsi Toko
                    </label>


                    <textarea
                        name="store_description"
                        rows="4"
                        class="mt-2
                               block
                               w-full
                               rounded-lg
                               border-gray-300
                               shadow-sm
                               focus:border-blue-500
                               focus:ring-blue-500"
                        placeholder="Tuliskan deskripsi singkat toko..."
                    >{{ old(
                        'store_description',
                        $setting?->store_description
                    ) }}</textarea>

                </div>

            </div>

        </div>


        {{-- LOGO & FAVICON --}}

        <div
            class="rounded-xl
                   bg-white
                   p-6
                   shadow-sm
                   ring-1
                   ring-gray-200"
        >

            <div class="mb-6">

                <h2
                    class="text-base
                           font-semibold
                           text-gray-900"
                >
                    Logo & Favicon
                </h2>

                <p
                    class="mt-1
                           text-sm
                           text-gray-500"
                >
                    Digunakan untuk identitas visual website.
                </p>

            </div>


            <div
                class="grid
                       grid-cols-1
                       gap-8
                       md:grid-cols-2"
            >


                {{-- LOGO --}}

                <div>

                    <label
                        class="block
                               text-sm
                               font-medium
                               text-gray-700"
                    >
                        Logo Toko
                    </label>


                    @if (
                        $setting?->logo
                    )

                        <div
                            class="mt-3
                                   flex
                                   h-40
                                   items-center
                                   justify-center
                                   rounded-xl
                                   border
                                   border-gray-200
                                   bg-gray-50
                                   p-4"
                        >

                            <img
                                src="{{ asset(
                                    'storage/' .
                                    $setting->logo
                                ) }}"
                                alt="Logo Toko"
                                class="max-h-full
                                       max-w-full
                                       object-contain"
                            >

                        </div>

                    @else

                        <div
                            class="mt-3
                                   flex
                                   h-40
                                   items-center
                                   justify-center
                                   rounded-xl
                                   border
                                   border-dashed
                                   border-gray-300
                                   bg-gray-50"
                        >

                            <span
                                class="text-sm
                                       text-gray-400"
                            >
                                Belum ada logo
                            </span>

                        </div>

                    @endif


                    <input
                        type="file"
                        name="logo"
                        accept="image/jpeg,image/png,image/webp"
                        class="mt-3
                               block
                               w-full
                               text-sm
                               text-gray-600
                               file:mr-4
                               file:rounded-lg
                               file:border-0
                               file:bg-blue-50
                               file:px-4
                               file:py-2
                               file:text-sm
                               file:font-semibold
                               file:text-blue-700
                               hover:file:bg-blue-100"
                    >


                    <p
                        class="mt-2
                               text-xs
                               text-gray-500"
                    >
                        Format JPG, PNG, atau WebP. Maksimal 2 MB.
                    </p>

                </div>


                {{-- FAVICON --}}

                <div>

                    <label
                        class="block
                               text-sm
                               font-medium
                               text-gray-700"
                    >
                        Favicon
                    </label>


                    @if (
                        $setting?->favicon
                    )

                        <div
                            class="mt-3
                                   flex
                                   h-40
                                   items-center
                                   justify-center
                                   rounded-xl
                                   border
                                   border-gray-200
                                   bg-gray-50
                                   p-4"
                        >

                            <img
                                src="{{ asset(
                                    'storage/' .
                                    $setting->favicon
                                ) }}"
                                alt="Favicon"
                                class="h-20
                                       w-20
                                       object-contain"
                            >

                        </div>

                    @else

                        <div
                            class="mt-3
                                   flex
                                   h-40
                                   items-center
                                   justify-center
                                   rounded-xl
                                   border
                                   border-dashed
                                   border-gray-300
                                   bg-gray-50"
                        >

                            <span
                                class="text-sm
                                       text-gray-400"
                            >
                                Belum ada favicon
                            </span>

                        </div>

                    @endif


                    <input
                        type="file"
                        name="favicon"
                        accept="image/jpeg,image/png,image/webp,image/x-icon"
                        class="mt-3
                               block
                               w-full
                               text-sm
                               text-gray-600
                               file:mr-4
                               file:rounded-lg
                               file:border-0
                               file:bg-blue-50
                               file:px-4
                               file:py-2
                               file:text-sm
                               file:font-semibold
                               file:text-blue-700
                               hover:file:bg-blue-100"
                    >


                    <p
                        class="mt-2
                               text-xs
                               text-gray-500"
                    >
                        Format PNG, JPG, WebP, atau ICO. Maksimal 1 MB.
                    </p>

                </div>

            </div>

        </div>


        {{-- KONTAK --}}

        <div
            class="rounded-xl
                   bg-white
                   p-6
                   shadow-sm
                   ring-1
                   ring-gray-200"
        >

            <div class="mb-6">

                <h2
                    class="text-base
                           font-semibold
                           text-gray-900"
                >
                    Informasi Kontak
                </h2>

                <p
                    class="mt-1
                           text-sm
                           text-gray-500"
                >
                    Informasi yang dapat digunakan pelanggan
                    untuk menghubungi toko.
                </p>

            </div>


            <div
                class="grid
                       grid-cols-1
                       gap-5
                       md:grid-cols-2"
            >


                {{-- PHONE --}}

                <div>

                    <label
                        class="block
                               text-sm
                               font-medium
                               text-gray-700"
                    >
                        Nomor Telepon
                    </label>


                    <input
                        type="text"
                        name="phone"
                        value="{{ old(
                            'phone',
                            $setting?->phone
                        ) }}"
                        class="mt-2
                               block
                               w-full
                               rounded-lg
                               border-gray-300
                               shadow-sm
                               focus:border-blue-500
                               focus:ring-blue-500"
                        placeholder="021xxxxxxxx"
                    >

                </div>


                {{-- WHATSAPP --}}

                <div>

                    <label
                        class="block
                               text-sm
                               font-medium
                               text-gray-700"
                    >
                        WhatsApp
                    </label>


                    <input
                        type="text"
                        name="whatsapp"
                        value="{{ old(
                            'whatsapp',
                            $setting?->whatsapp
                        ) }}"
                        class="mt-2
                               block
                               w-full
                               rounded-lg
                               border-gray-300
                               shadow-sm
                               focus:border-blue-500
                               focus:ring-blue-500"
                        placeholder="628123456789"
                    >

                    <p
                        class="mt-2
                               text-xs
                               text-gray-500"
                    >
                        Gunakan format internasional tanpa tanda +.
                    </p>

                </div>


                {{-- EMAIL --}}

                <div>

                    <label
                        class="block
                               text-sm
                               font-medium
                               text-gray-700"
                    >
                        Email
                    </label>


                    <input
                        type="email"
                        name="email"
                        value="{{ old(
                            'email',
                            $setting?->email
                        ) }}"
                        class="mt-2
                               block
                               w-full
                               rounded-lg
                               border-gray-300
                               shadow-sm
                               focus:border-blue-500
                               focus:ring-blue-500"
                        placeholder="email@contoh.com"
                    >

                </div>


                {{-- ADDRESS --}}

                <div>

                    <label
                        class="block
                               text-sm
                               font-medium
                               text-gray-700"
                    >
                        Alamat
                    </label>


                    <textarea
                        name="address"
                        rows="3"
                        class="mt-2
                               block
                               w-full
                               rounded-lg
                               border-gray-300
                               shadow-sm
                               focus:border-blue-500
                               focus:ring-blue-500"
                        placeholder="Alamat toko..."
                    >{{ old(
                        'address',
                        $setting?->address
                    ) }}</textarea>

                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Google Maps
                    </label>
                    
                    <textarea 
                        name="google_maps" 
                        rows="4"
                        class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Tempelkan kode iframe Google Maps disini..."
                    >{{ old('google_maps', $setting?->google_maps) }}</textarea>
                    
                    <div class="mt-2 text-xs text-gray-500 space-y-1">
                        <p>🔹 Cara mendapatkan kode iframe:</p>
                        <ol class="list-decimal list-inside ml-2">
                            <li>Buka Google Maps dan cari lokasi toko</li>
                            <li>Klik tombol "Bagikan" (Share)</li>
                            <li>Pilih tab "Sematan peta" (Embed a map)</li>
                            <li>Copy kode iframe dan tempelkan di atas</li>
                        </ol>
                        <p class="mt-1">📌 Contoh: <code class="bg-gray-100 px-1 py-0.5 rounded">&lt;iframe src="https://www.google.com/maps/embed?pb=..." width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"&gt;&lt;/iframe&gt;</code></p>
                    </div>
                    
                    @error('google_maps') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                    @enderror
                </div>

            </div>

        </div>


        {{-- SOCIAL MEDIA --}}

        <div
            class="rounded-xl
                   bg-white
                   p-6
                   shadow-sm
                   ring-1
                   ring-gray-200"
        >

            <div class="mb-6">

                <h2
                    class="text-base
                           font-semibold
                           text-gray-900"
                >
                    Social Media
                </h2>

                <p
                    class="mt-1
                           text-sm
                           text-gray-500"
                >
                    Masukkan link media sosial toko.
                </p>

            </div>


            <div class="space-y-5">


                {{-- INSTAGRAM --}}

                <div>

                    <label
                        class="block
                               text-sm
                               font-medium
                               text-gray-700"
                    >
                        Instagram
                    </label>


                    <input
                        type="text"
                        name="instagram"
                        value="{{ old(
                            'instagram',
                            $setting?->instagram
                        ) }}"
                        class="mt-2
                               block
                               w-full
                               rounded-lg
                               border-gray-300
                               shadow-sm
                               focus:border-blue-500
                               focus:ring-blue-500"
                        placeholder="https://instagram.com/toko"
                    >

                </div>


                {{-- FACEBOOK --}}

                <div>

                    <label
                        class="block
                               text-sm
                               font-medium
                               text-gray-700"
                    >
                        Facebook
                    </label>


                    <input
                        type="text"
                        name="facebook"
                        value="{{ old(
                            'facebook',
                            $setting?->facebook
                        ) }}"
                        class="mt-2
                               block
                               w-full
                               rounded-lg
                               border-gray-300
                               shadow-sm
                               focus:border-blue-500
                               focus:ring-blue-500"
                        placeholder="https://facebook.com/toko"
                    >

                </div>


                {{-- TIKTOK --}}

                <div>

                    <label
                        class="block
                               text-sm
                               font-medium
                               text-gray-700"
                    >
                        TikTok
                    </label>


                    <input
                        type="text"
                        name="tiktok"
                        value="{{ old(
                            'tiktok',
                            $setting?->tiktok
                        ) }}"
                        class="mt-2
                               block
                               w-full
                               rounded-lg
                               border-gray-300
                               shadow-sm
                               focus:border-blue-500
                               focus:ring-blue-500"
                        placeholder="https://tiktok.com/@toko"
                    >

                </div>

            </div>

        </div>


        {{-- ACTION --}}

        <div
            class="flex
                   justify-end
                   rounded-xl
                   bg-white
                   p-6
                   shadow-sm
                   ring-1
                   ring-gray-200"
        >

            <button
                type="submit"
                class="inline-flex
                       items-center
                       justify-center
                       rounded-lg
                       bg-blue-600
                       px-6 py-2.5
                       text-sm
                       font-semibold
                       text-white
                       transition
                       hover:bg-blue-700"
            >
                Simpan Pengaturan
            </button>

        </div>

    </form>

</div>

@endsection