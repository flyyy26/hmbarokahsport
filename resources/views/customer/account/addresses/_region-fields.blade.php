@php
    $address = $address ?? null;
    $selectedProvince = old('province', $address?->province ?? '');
    $selectedCity = old('city', $address?->city ?? '');
    $selectedDistrict = old('district', $address?->district ?? '');
    $selectedSubdistrict = old('subdistrict', $address?->subdistrict ?? '');
@endphp

<style>
    /* ============================================
       REGION FIELDS STYLES
       ============================================ */
    .rf-group {
        display: flex;
        flex-direction: column;
        gap: 0.4vw;
    }

    .rf-label {
        display: flex;
        align-items: center;
        gap: 0.35vw;
        font-size: 0.85vw;
        font-weight: 600;
        color: #334155;
    }

    .rf-label iconify-icon {
        color: #ecbc42;
        font-size: 1vw;
    }

    .rf-label .required {
        color: #dc2626;
    }

    .rf-select-wrap {
        position: relative;
    }

    .rf-select {
        width: 100%;
        padding: 0.85vw 2.5vw 0.85vw 1.1vw;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.6vw;
        font-size: 0.85vw;
        color: #0f172a;
        background: #ffffff;
        outline: none;
        transition: all 0.2s ease;
        font-family: inherit;
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
    }

    .rf-select:disabled {
        background: #f8fafc;
        color: #94a3b8;
        cursor: not-allowed;
        opacity: 0.7;
    }

    .rf-select:focus {
        border-color: #ecbc42;
        box-shadow: 0 0 0 0.25vw rgba(236, 188, 66, 0.2);
    }

    .rf-select.has-error {
        border-color: #fca5a5;
    }

    .rf-select.has-error:focus {
        border-color: #dc2626;
        box-shadow: 0 0 0 0.25vw rgba(220, 38, 38, 0.15);
    }

    /* Custom arrow */
    .rf-select-arrow {
        position: absolute;
        right: 1vw;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        color: #94a3b8;
        font-size: 1.1vw;
        transition: color 0.2s ease;
    }

    .rf-select:focus ~ .rf-select-arrow {
        color: #ecbc42;
    }

    /* Loading state */
    .rf-select-wrap.is-loading .rf-select-arrow {
        animation: rfSpin 1s linear infinite;
        color: #ecbc42;
    }

    @keyframes rfSpin {
        from { transform: translateY(-50%) rotate(0deg); }
        to   { transform: translateY(-50%) rotate(360deg); }
    }

    .rf-error {
        display: flex;
        align-items: center;
        gap: 0.35vw;
        font-size: 0.75vw;
        color: #dc2626;
        margin-top: 0.2vw;
    }

    .rf-error iconify-icon {
        font-size: 0.9vw;
        flex-shrink: 0;
    }

    /* ============================================
       RESPONSIVE - TABLET
       ============================================ */
    @media (max-width: 1024px) {
        .rf-group { gap: 1vw; }

        .rf-label {
            font-size: 2.1vw;
            gap: 0.9vw;
        }
        .rf-label iconify-icon { font-size: 2.5vw; }

        .rf-select {
            padding: 2.2vw 6vw 2.2vw 3vw;
            border-radius: 1.5vw;
            font-size: 2.2vw;
            border-width: 0.2vw;
        }

        .rf-select:focus {
            box-shadow: 0 0 0 0.6vw rgba(236, 188, 66, 0.2);
        }

        .rf-select.has-error:focus {
            box-shadow: 0 0 0 0.6vw rgba(220, 38, 38, 0.15);
        }

        .rf-select-arrow {
            right: 2.5vw;
            font-size: 3vw;
        }

        .rf-error {
            font-size: 1.8vw;
            gap: 0.9vw;
            margin-top: 0.5vw;
        }
        .rf-error iconify-icon { font-size: 2.2vw; }
    }

    /* ============================================
       RESPONSIVE - MOBILE
       ============================================ */
    @media (max-width: 480px) {
        .rf-group { gap: 1.5vw; }

        .rf-label {
            font-size: 3.2vw;
            gap: 1.2vw;
        }
        .rf-label iconify-icon { font-size: 4vw; }

        .rf-select {
            padding: 3.2vw 8vw 3.2vw 3.5vw;
            border-radius: 2.5vw;
            font-size: 3.2vw;
            border-width: 0.3vw;
        }

        .rf-select:focus {
            box-shadow: 0 0 0 0.9vw rgba(236, 188, 66, 0.2);
        }

        .rf-select.has-error:focus {
            box-shadow: 0 0 0 0.9vw rgba(220, 38, 38, 0.15);
        }

        .rf-select-arrow {
            right: 4vw;
            font-size: 4.5vw;
        }

        .rf-error {
            font-size: 2.8vw;
            gap: 1.2vw;
            margin-top: 0.8vw;
        }
        .rf-error iconify-icon { font-size: 3.4vw; }
    }
</style>

{{-- Provinsi --}}
<div class="rf-group">
    <label for="province" class="rf-label">
        <iconify-icon icon="mdi:map-marker-outline"></iconify-icon>
        Provinsi <span class="required">*</span>
    </label>
    <div class="rf-select-wrap" id="province-wrap">
        <select name="province" id="province" required
                data-selected="{{ $selectedProvince }}"
                class="rf-select @error('province') has-error @enderror">
            <option value="">-- Memuat Provinsi --</option>
        </select>
        <iconify-icon icon="mdi:loading" class="rf-select-arrow" id="province-arrow"></iconify-icon>
    </div>
    @error('province')
        <p class="rf-error">
            <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
            {{ $message }}
        </p>
    @enderror
</div>

{{-- Kota/Kabupaten --}}
<div class="rf-group">
    <label for="city" class="rf-label">
        <iconify-icon icon="mdi:city-variant-outline"></iconify-icon>
        Kota/Kabupaten <span class="required">*</span>
    </label>
    <div class="rf-select-wrap" id="city-wrap">
        <select name="city" id="city" required disabled
                data-selected="{{ $selectedCity }}"
                class="rf-select @error('city') has-error @enderror">
            <option value="">-- Pilih Provinsi Terlebih Dahulu --</option>
        </select>
        <iconify-icon icon="mdi:chevron-down" class="rf-select-arrow"></iconify-icon>
    </div>
    @error('city')
        <p class="rf-error">
            <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
            {{ $message }}
        </p>
    @enderror
</div>

{{-- Kecamatan --}}
<div class="rf-group">
    <label for="district" class="rf-label">
        <iconify-icon icon="mdi:map-marker-radius-outline"></iconify-icon>
        Kecamatan <span class="required">*</span>
    </label>
    <div class="rf-select-wrap" id="district-wrap">
        <select name="district" id="district" required disabled
                data-selected="{{ $selectedDistrict }}"
                class="rf-select @error('district') has-error @enderror">
            <option value="">-- Pilih Kota Terlebih Dahulu --</option>
        </select>
        <iconify-icon icon="mdi:chevron-down" class="rf-select-arrow"></iconify-icon>
    </div>
    @error('district')
        <p class="rf-error">
            <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
            {{ $message }}
        </p>
    @enderror
</div>

{{-- Kelurahan --}}
<div class="rf-group">
    <label for="subdistrict" class="rf-label">
        <iconify-icon icon="mdi:home-map-marker"></iconify-icon>
        Kelurahan <span class="required">*</span>
    </label>
    <div class="rf-select-wrap" id="subdistrict-wrap">
        <select name="subdistrict" id="subdistrict" required disabled
                data-selected="{{ $selectedSubdistrict }}"
                class="rf-select @error('subdistrict') has-error @enderror">
            <option value="">-- Pilih Kecamatan Terlebih Dahulu --</option>
        </select>
        <iconify-icon icon="mdi:chevron-down" class="rf-select-arrow"></iconify-icon>
    </div>
    @error('subdistrict')
        <p class="rf-error">
            <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
            {{ $message }}
        </p>
    @enderror
</div>

@once
<script>
    $(function() {
        const apiBase = '{{ request()->getBaseUrl() }}/api/regions';
        const $province = $('#province');
        const $city = $('#city');
        const $district = $('#district');
        const $subdistrict = $('#subdistrict');

        // 🔥 Helper: set loading state
        function setLoading($select, isLoading) {
            const wrap = $select.closest('.rf-select-wrap');
            const arrow = wrap.find('.rf-select-arrow');
            
            if (isLoading) {
                wrap.addClass('is-loading');
                arrow.attr('icon', 'mdi:loading');
            } else {
                wrap.removeClass('is-loading');
                arrow.attr('icon', 'mdi:chevron-down');
            }
        }

        // 🔥 Helper: fill select with options
        function fillSelect($select, items, placeholder, selectedValue) {
            let options = `<option value="">${placeholder}</option>`;
            items.forEach(function(item) {
                const label = item.name;
                const value = item.code;
                const postalCode = item.postal_code || '';
                options += `<option value="${label}" data-code="${value}" data-postal-code="${postalCode}">${label}</option>`;
            });
            $select.html(options).prop('disabled', false);
            
            if (selectedValue) {
                // Cari option dengan value sama, jika ada baru set
                const $match = $select.find('option').filter(function() {
                    return $(this).val() === selectedValue;
                });
                if ($match.length) {
                    $select.val(selectedValue);
                }
            }
        }

        function loadProvinces() {
            setLoading($province, true);
            $.get(`${apiBase}/provinces`, function(items) {
                fillSelect($province, items, '-- Pilih Provinsi --', $province.data('selected'));
                setLoading($province, false);
                // Auto-trigger jika ada selected value
                if ($province.val()) $province.trigger('change');
            }).fail(function() {
                setLoading($province, false);
                $province.html('<option value="">-- Gagal memuat provinsi --</option>');
            });
        }

        function loadCities(provinceCode, selectedValue) {
            setLoading($city, true);
            $city.html('<option value="">-- Memuat Kota --</option>').prop('disabled', true);
            $.get(`${apiBase}/cities`, { province_code: provinceCode }, function(items) {
                fillSelect($city, items, '-- Pilih Kota --', selectedValue || $city.data('selected'));
                setLoading($city, false);
                if ($city.val()) $city.trigger('change');
            }).fail(function() {
                setLoading($city, false);
                $city.html('<option value="">-- Gagal memuat kota --</option>');
            });
        }

        function loadDistricts(cityCode, selectedValue) {
            setLoading($district, true);
            $district.html('<option value="">-- Memuat Kecamatan --</option>').prop('disabled', true);
            $.get(`${apiBase}/districts`, { city_code: cityCode }, function(items) {
                fillSelect($district, items, '-- Pilih Kecamatan --', selectedValue || $district.data('selected'));
                setLoading($district, false);
                if ($district.val()) $district.trigger('change');
            }).fail(function() {
                setLoading($district, false);
                $district.html('<option value="">-- Gagal memuat kecamatan --</option>');
            });
        }

        function loadSubdistricts(districtCode, selectedValue) {
            setLoading($subdistrict, true);
            $subdistrict.html('<option value="">-- Memuat Kelurahan --</option>').prop('disabled', true);
            $.get(`${apiBase}/subdistricts`, { district_code: districtCode }, function(items) {
                fillSelect($subdistrict, items, '-- Pilih Kelurahan --', selectedValue || $subdistrict.data('selected'));
                setLoading($subdistrict, false);
                if ($subdistrict.val()) $subdistrict.trigger('change');
            }).fail(function() {
                setLoading($subdistrict, false);
                $subdistrict.html('<option value="">-- Gagal memuat kelurahan --</option>');
            });
        }

        // 🔥 Chain: Province → City
        $province.on('change', function() {
            const code = $(this).find(':selected').data('code');
            $city.val('').prop('disabled', true);
            $district.html('<option value="">-- Pilih Kota Terlebih Dahulu --</option>').prop('disabled', true);
            $subdistrict.html('<option value="">-- Pilih Kecamatan Terlebih Dahulu --</option>').prop('disabled', true);
            if (code) loadCities(code, $city.data('selected'));
        });

        // 🔥 Chain: City → District
        $city.on('change', function() {
            const code = $(this).find(':selected').data('code');
            $district.val('').prop('disabled', true);
            $subdistrict.html('<option value="">-- Pilih Kecamatan Terlebih Dahulu --</option>').prop('disabled', true);
            if (code) loadDistricts(code, $district.data('selected'));
        });

        // 🔥 Chain: District → Subdistrict
        $district.on('change', function() {
            const code = $(this).find(':selected').data('code');
            $subdistrict.val('').prop('disabled', true);
            if (code) loadSubdistricts(code, $subdistrict.data('selected'));
        });

        // 🔥 Auto-fill postal code
        $subdistrict.on('change', function() {
            const postalCode = $(this).find(':selected').data('postal-code') || '';
            const $postal = $('#postal_code');
            
            if (postalCode) {
                $postal.val(postalCode).prop('readonly', true);
                $postal.css({ 'background': '#fafbfc', 'cursor': 'not-allowed' });
            } else {
                $postal.prop('readonly', false);
                $postal.css({ 'background': '', 'cursor': '' });
            }
        });

        // 🔥 Enable disabled selects saat form submit
        $('form').on('submit', function() {
            $province.prop('disabled', false);
            $city.prop('disabled', false);
            $district.prop('disabled', false);
            $subdistrict.prop('disabled', false);
        });

        // 🔥 Init
        loadProvinces();
    });
</script>
@endonce