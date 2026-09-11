@php
    $isEdit = isset($marketplace);
@endphp


<div class="space-y-6">

    {{-- NAME --}}

    <div>

        <label
            class="block
                   text-sm
                   font-medium
                   text-gray-700"
        >
            Nama Marketplace
        </label>

        <input
            type="text"
            name="name"
            value="{{ old(
                'name',
                $marketplace->name ?? ''
            ) }}"
            required
            placeholder="Contoh: Shopee"
            class="mt-2
                   block
                   w-full
                   rounded-lg
                   border-gray-300
                   shadow-sm
                   focus:border-blue-500
                   focus:ring-blue-500"
        >

        @error('name')

            <p
                class="mt-1
                       text-sm
                       text-red-600"
            >
                {{ $message }}
            </p>

        @enderror

    </div>

    {{-- MARKETPLACE ICON --}}

    <div>

        <label
            class="block
                text-sm
                font-medium
                text-gray-700"
        >
            Pilih Icon Marketplace
        </label>

        <p class="mt-1 text-xs text-gray-500">
            Pilih icon yang sesuai dengan marketplace atau platform yang digunakan.
        </p>


        @php
            $marketplaceIcons = [
                [
                    'name' => 'Shopee',
                    'icon' => 'arcticons:shopee',
                ],
                [
                    'name' => 'Tokopedia',
                    'icon' => 'arcticons:tokopedia',
                ],
                [
                    'name' => 'TikTok Shop',
                    'icon' => 'ic:sharp-tiktok',
                ],
                [
                    'name' => 'Lazada',
                    'icon' => 'arcticons:lazada',
                ],
                [
                    'name' => 'Blibli',
                    'icon' => 'simple-icons:blibli',
                ],
                [
                    'name' => 'Bukalapak',
                    'icon' => 'arcticons:bukalapak',
                ],
                [
                    'name' => 'Facebook',
                    'icon' => 'uit:facebook-f',
                ],
                [
                    'name' => 'Instagram',
                    'icon' => 'griddy-icons:instagram',
                ],
                [
                    'name' => 'WhatsApp',
                    'icon' => 'uil:whatsapp',
                ],
            ];

            $selectedIcon = old(
                'icon',
                $marketplace->icon ?? 'simple-icons:shopee'
            );
        @endphp


        <input
            type="hidden"
            name="icon"
            id="selectedIcon"
            value="{{ $selectedIcon }}"
        >


        <div
            class="mt-4
                grid
                grid-cols-2
                gap-3
                sm:grid-cols-3
                lg:grid-cols-4"
        >

            @foreach ($marketplaceIcons as $item)

                <button
                    type="button"
                    data-icon="{{ $item['icon'] }}"
                    class="marketplace-icon-option
                        flex
                        flex-col
                        items-center
                        justify-center
                        gap-2
                        rounded-xl
                        border
                        p-4
                        transition
                        hover:border-blue-400
                        hover:bg-blue-50"
                >

                    <iconify-icon
                        icon="{{ $item['icon'] }}"
                        width="32"
                        height="32"
                    ></iconify-icon>


                    <span
                        class="text-xs
                            font-medium
                            text-gray-700"
                    >
                        {{ $item['name'] }}
                    </span>

                </button>

            @endforeach

        </div>


        @error('icon')

            <p
                class="mt-2
                    text-sm
                    text-red-600"
            >
                {{ $message }}
            </p>

        @enderror

    </div>


    {{-- URL --}}

    <div>

        <label
            class="block
                   text-sm
                   font-medium
                   text-gray-700"
        >
            URL Marketplace
        </label>


        <input
            type="url"
            name="url"
            value="{{ old(
                'url',
                $marketplace->url ?? ''
            ) }}"
            required
            placeholder="https://shopee.co.id/toko-kamu"
            class="mt-2
                   block
                   w-full
                   rounded-lg
                   border-gray-300
                   shadow-sm
                   focus:border-blue-500
                   focus:ring-blue-500"
        >


        @error('url')

            <p
                class="mt-1
                       text-sm
                       text-red-600"
            >
                {{ $message }}
            </p>

        @enderror

    </div>


    {{-- SORT ORDER --}}

    <div>

        <label
            class="block
                   text-sm
                   font-medium
                   text-gray-700"
        >
            Urutan
        </label>


        <input
            type="number"
            name="sort_order"
            min="0"
            value="{{ old(
                'sort_order',
                $marketplace->sort_order ?? 0
            ) }}"
            class="mt-2
                   block
                   w-full
                   rounded-lg
                   border-gray-300
                   shadow-sm
                   focus:border-blue-500
                   focus:ring-blue-500"
        >


        <p
            class="mt-2
                   text-xs
                   text-gray-500"
        >
            Angka yang lebih kecil akan tampil lebih dahulu.
        </p>

    </div>


    {{-- ACTIVE --}}

    <div
        class="flex
               items-center
               justify-between
               rounded-lg
               border
               border-gray-200
               p-4"
    >

        <div>

            <p
                class="text-sm
                       font-medium
                       text-gray-900"
            >
                Status Marketplace
            </p>

            <p
                class="mt-1
                       text-xs
                       text-gray-500"
            >
                Marketplace aktif akan ditampilkan
                di website.
            </p>

        </div>


        <label
            class="relative
                   inline-flex
                   cursor-pointer
                   items-center"
        >

            <input
                type="checkbox"
                name="is_active"
                value="1"
                class="peer sr-only"
                @checked(
                    old(
                        'is_active',
                        $marketplace->is_active ?? true
                    )
                )
            >


            <div
                class="h-6
                       w-11
                       rounded-full
                       bg-gray-200
                       after:absolute
                       after:left-[2px]
                       after:top-[2px]
                       after:h-5
                       after:w-5
                       after:rounded-full
                       after:border
                       after:border-gray-300
                       after:bg-white
                       after:transition-all
                       peer-checked:bg-blue-600
                       peer-checked:after:translate-x-full
                       peer-checked:after:border-white"
            ></div>

        </label>

    </div>

</div>


{{-- LIVE ICON PREVIEW --}}

<script>

    const iconInput =
        document.getElementById('icon');

    const iconPreview =
        document.getElementById('iconPreview');


    if (
        iconInput &&
        iconPreview
    ) {

        iconInput.addEventListener(
            'input',
            function () {

                const value =
                    this.value.trim();

                if (value) {

                    iconPreview.setAttribute(
                        'icon',
                        value
                    );

                }

            }
        );

    }
    

</script>

<script>

    document.addEventListener(
        'DOMContentLoaded',
        function () {

            const iconInput =
                document.getElementById(
                    'selectedIcon'
                );


            const iconOptions =
                document.querySelectorAll(
                    '.marketplace-icon-option'
                );


            function updateSelectedIcon() {

                const selectedIcon =
                    iconInput.value;


                iconOptions.forEach(
                    function (button) {

                        const icon =
                            button.dataset.icon;


                        if (
                            icon === selectedIcon
                        ) {

                            button.classList.add(
                                'border-blue-500',
                                'bg-blue-50',
                                'ring-2',
                                'ring-blue-100'
                            );

                            button.classList.remove(
                                'border-gray-200'
                            );

                        } else {

                            button.classList.remove(
                                'border-blue-500',
                                'bg-blue-50',
                                'ring-2',
                                'ring-blue-100'
                            );

                            button.classList.add(
                                'border-gray-200'
                            );

                        }

                    }
                );

            }


            iconOptions.forEach(
                function (button) {

                    button.addEventListener(
                        'click',
                        function () {

                            const icon =
                                this.dataset.icon;


                            iconInput.value =
                                icon;


                            updateSelectedIcon();

                        }
                    );

                }
            );


            // Set icon terpilih saat halaman dibuka

            updateSelectedIcon();

        }
    );

</script>