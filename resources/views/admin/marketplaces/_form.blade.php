@php
    $isEdit = isset($marketplace);
@endphp

<div class="space-y-6">

    {{-- ============================================ --}}
    {{-- ERROR VALIDATION --}}
    {{-- ============================================ --}}
    @if ($errors->any())
        <div class="flex items-start gap-3 rounded-xl px-4 py-3
                    bg-red-500/10 border border-red-500/30 text-red-400">
            <iconify-icon icon="mdi:alert-circle-outline" class="text-xl flex-shrink-0 mt-0.5"></iconify-icon>
            <div class="text-sm">
                <p class="font-bold mb-1">Mohon perbaiki kesalahan berikut:</p>
                <ul class="list-disc list-inside space-y-0.5 text-red-300">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif


    {{-- ============================================ --}}
    {{-- SECTION 1: INFORMASI MARKETPLACE --}}
    {{-- ============================================ --}}
    <div class="rounded-2xl border overflow-hidden"
         style="background: var(--bg-card); border-color: var(--border-2)">

        <div class="px-5 py-4 border-b flex items-center gap-2"
             style="background: var(--bg-input); border-color: var(--border-2)">
            <iconify-icon icon="mdi:information-outline" class="text-[#ecbc42] text-base"></iconify-icon>
            <h2 class="font-bold text-sm" style="color: var(--text-1)">Informasi Marketplace</h2>
        </div>

        <div class="p-5 space-y-5">

            {{-- Nama --}}
            <div>
                <label class="form-label">
                    <iconify-icon icon="mdi:store-outline" class="text-[#ecbc42]"></iconify-icon>
                    Nama Marketplace <span class="text-red-400">*</span>
                </label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $marketplace->name ?? '') }}"
                       required
                       placeholder="Contoh: Shopee"
                       class="form-input">
                @error('name')
                    <p class="text-[10px] mt-1 text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- URL --}}
            <div>
                <label class="form-label">
                    <iconify-icon icon="mdi:link-variant" class="text-[#ecbc42]"></iconify-icon>
                    URL Marketplace <span class="text-red-400">*</span>
                </label>
                <input type="url"
                       name="url"
                       value="{{ old('url', $marketplace->url ?? '') }}"
                       required
                       placeholder="https://shopee.co.id/toko-kamu"
                       class="form-input">
                @error('url')
                    <p class="text-[10px] mt-1 text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Sort Order --}}
            <div>
                <label class="form-label">
                    <iconify-icon icon="mdi:sort-numeric-ascending" class="text-[#ecbc42]"></iconify-icon>
                    Urutan
                </label>
                <input type="number"
                       name="sort_order"
                       min="0"
                       value="{{ old('sort_order', $marketplace->sort_order ?? 0) }}"
                       class="form-input"
                       placeholder="0">
                <p class="text-[10px] mt-1.5 flex items-center gap-1" style="color: var(--text-5);">
                    <iconify-icon icon="mdi:information-outline"></iconify-icon>
                    Angka yang lebih kecil akan tampil lebih dahulu.
                </p>
            </div>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- SECTION 2: ICON MARKETPLACE --}}
    {{-- ============================================ --}}
    <div class="rounded-2xl border overflow-hidden"
         style="background: var(--bg-card); border-color: var(--border-2)">

        <div class="px-5 py-4 border-b flex items-center gap-2"
             style="background: var(--bg-input); border-color: var(--border-2)">
            <iconify-icon icon="mdi:emoticon-outline" class="text-[#ecbc42] text-base"></iconify-icon>
            <h2 class="font-bold text-sm flex-1" style="color: var(--text-1)">Icon Marketplace</h2>
            <span class="text-[10px] font-mono px-2 py-0.5 rounded-full"
                  style="background: rgba(236,188,66,0.1); color: #ecbc42;">
                <iconify-icon icon="mdi:check-circle" class="inline"></iconify-icon>
                Pilih 1
            </span>
        </div>

        <div class="p-5">

            <p class="text-[11px] mb-4 flex items-center gap-1.5" style="color: var(--text-5);">
                <iconify-icon icon="mdi:information-outline" class="text-[#ecbc42]"></iconify-icon>
                Pilih icon yang sesuai dengan marketplace atau platform yang digunakan.
            </p>

            @php
                $marketplaceIcons = [
                    ['name' => 'Shopee',     'icon' => 'simple-icons:shopee'],
                    ['name' => 'Tokopedia',  'icon' => 'arcticons:tokopedia'],
                    ['name' => 'TikTok Shop','icon' => 'akar-icons:tiktok-fill'],
                    ['name' => 'Lazada',     'icon' => 'arcticons:lazada'],
                    ['name' => 'Blibli',     'icon' => 'simple-icons:blibli'],
                    ['name' => 'Bukalapak',  'icon' => 'arcticons:bukalapak'],
                    ['name' => 'Facebook',   'icon' => 'akar-icons:facebook-fill'],
                    ['name' => 'Instagram',  'icon' => 'griddy-icons:instagram'],
                    ['name' => 'WhatsApp',   'icon' => 'uil:whatsapp'],
                ];

                $selectedIcon = old('icon', $marketplace->icon ?? 'simple-icons:shopee');
            @endphp

            <input type="hidden" name="icon" id="selectedIcon" value="{{ $selectedIcon }}">

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">

                @foreach ($marketplaceIcons as $item)
                    <button type="button"
                            data-icon="{{ $item['icon'] }}"
                            class="marketplace-icon-option
                                   group relative flex flex-col items-center justify-center gap-2
                                   rounded-xl border p-4 transition-all duration-200
                                   hover:border-[#ecbc42]/50 hover:bg-[#ecbc42]/5"
                            style="background: var(--bg-input); border-color: var(--border-2);">

                        <div class="w-12 h-12 rounded-lg flex items-center justify-center
                                    transition-all duration-200
                                    group-hover:bg-[#ecbc42]/10"
                             style="background: var(--bg-card);">
                            <iconify-icon icon="{{ $item['icon'] }}"
                                          width="28" height="28"
                                          class="transition-colors duration-200"
                                          style="color: var(--text-2);"></iconify-icon>
                        </div>

                        <span class="text-xs font-semibold transition-colors"
                              style="color: var(--text-3);">
                            {{ $item['name'] }}
                        </span>

                        {{-- Selected Indicator --}}
                        <div class="icon-check absolute top-2 right-2 hidden">
                            <div class="w-5 h-5 rounded-full flex items-center justify-center
                                        bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                                        shadow-md shadow-amber-500/30">
                                <iconify-icon icon="mdi:check" class="text-slate-900 text-xs"></iconify-icon>
                            </div>
                        </div>
                    </button>
                @endforeach
            </div>

            @error('icon')
                <p class="text-[10px] mt-2 text-red-400">{{ $message }}</p>
            @enderror

            {{-- Preview Icon Terpilih --}}
            <div class="mt-5 pt-5 border-t flex items-center gap-3"
                 style="border-color: var(--border-1);">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0
                            bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                            shadow-md shadow-amber-500/20">
                    <iconify-icon id="iconPreview"
                                  icon="{{ $selectedIcon }}"
                                  width="26" height="26"
                                  class="text-slate-900"></iconify-icon>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider mb-0.5" style="color: var(--text-5);">
                        Icon Terpilih
                    </p>
                    <p class="text-xs font-mono" style="color: var(--text-3);" id="selectedIconText">
                        {{ $selectedIcon }}
                    </p>
                </div>
            </div>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- SECTION 3: STATUS --}}
    {{-- ============================================ --}}
    <div class="rounded-2xl border overflow-hidden"
         style="background: var(--bg-card); border-color: var(--border-2)">

        <div class="px-5 py-4 border-b flex items-center gap-2"
             style="background: var(--bg-input); border-color: var(--border-2)">
            <iconify-icon icon="mdi:shield-check-outline" class="text-[#ecbc42] text-base"></iconify-icon>
            <h2 class="font-bold text-sm" style="color: var(--text-1)">Status Marketplace</h2>
        </div>

        <div class="p-5">
            <label class="flex items-start gap-3 cursor-pointer p-3 rounded-lg border transition-all"
                   style="background: var(--bg-input); border-color: var(--border-2);"
                   onmouseover="this.style.borderColor='rgba(52,211,153,0.3)'"
                   onmouseout="this.style.borderColor='var(--border-2)'">

                <input type="hidden" name="is_active" value="0">

                <input type="checkbox"
                       name="is_active"
                       value="1"
                       @checked(old('is_active', $marketplace->is_active ?? true))
                       class="mt-0.5 h-4 w-4 rounded cursor-pointer"
                       style="accent-color: #ecbc42;">

                <div>
                    <span class="text-sm font-semibold flex items-center gap-1.5" style="color: var(--text-1);">
                        <iconify-icon icon="mdi:check-circle-outline" class="text-emerald-400"></iconify-icon>
                        Aktifkan Marketplace
                    </span>
                    <p class="text-[10px] mt-0.5" style="color: var(--text-5);">
                        Marketplace aktif akan ditampilkan di website.
                    </p>
                </div>
            </label>
        </div>
    </div>

</div>


{{-- ============================================ --}}
{{-- STYLES --}}
{{-- ============================================ --}}
<style>
    .form-input {
        width: 100%;
        padding: 0.7rem 1rem;
        background: var(--bg-input);
        border: 1px solid var(--border-2);
        border-radius: 0.65rem;
        font-size: 0.875rem;
        color: var(--text-1);
        transition: all 0.2s ease;
        font-family: inherit;
        outline: none;
    }
    .form-input:focus {
        border-color: #ecbc42;
        box-shadow: 0 0 0 3px rgba(236, 188, 66, 0.15);
    }
    .form-input::placeholder {
        color: var(--text-6);
    }
    .form-label {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-3);
        margin-bottom: 0.5rem;
    }

    /* ============================================ */
    /* MARKETPLACE ICON SELECTION */
    /* ============================================ */
    .marketplace-icon-option.selected {
        border-color: #ecbc42 !important;
        background: rgba(236, 188, 66, 0.08) !important;
        box-shadow: 0 0 0 3px rgba(236, 188, 66, 0.15);
    }

    .marketplace-icon-option.selected .icon-check {
        display: flex !important;
    }

    .marketplace-icon-option.selected iconify-icon {
        color: #ecbc42 !important;
    }

    .marketplace-icon-option.selected span {
        color: #ecbc42 !important;
    }
</style>


{{-- ============================================ --}}
{{-- SCRIPT --}}
{{-- ============================================ --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    const iconInput = document.getElementById('selectedIcon');
    const iconPreview = document.getElementById('iconPreview');
    const selectedIconText = document.getElementById('selectedIconText');
    const iconOptions = document.querySelectorAll('.marketplace-icon-option');

    function updateSelectedIcon() {
        const selectedIcon = iconInput.value;

        // Update preview
        if (iconPreview) {
            iconPreview.setAttribute('icon', selectedIcon);
        }
        if (selectedIconText) {
            selectedIconText.textContent = selectedIcon;
        }

        // Update option states
        iconOptions.forEach(function(button) {
            const icon = button.dataset.icon;
            if (icon === selectedIcon) {
                button.classList.add('selected');
            } else {
                button.classList.remove('selected');
            }
        });
    }

    // Click handler
    iconOptions.forEach(function(button) {
        button.addEventListener('click', function() {
            iconInput.value = this.dataset.icon;
            updateSelectedIcon();
        });
    });

    // Initial state
    updateSelectedIcon();

});
</script>
@endpush