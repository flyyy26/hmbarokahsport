@php
    $address = $address ?? null;
    $selectedProvince = old('province', $address?->province ?? '');
    $selectedCity = old('city', $address?->city ?? '');
    $selectedDistrict = old('district', $address?->district ?? '');
    $selectedSubdistrict = old('subdistrict', $address?->subdistrict ?? '');
@endphp

<div>
    <label for="province" class="mb-1 block text-sm font-medium text-gray-700">Provinsi <span class="text-red-500">*</span></label>
    <select name="province" id="province" required
            data-selected="{{ $selectedProvince }}"
            class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('province') border-red-400 @enderror">
        <option value="">-- Memuat Provinsi --</option>
    </select>
    @error('province') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
</div>

<div>
    <label for="city" class="mb-1 block text-sm font-medium text-gray-700">Kota/Kabupaten <span class="text-red-500">*</span></label>
    <select name="city" id="city" required disabled
            data-selected="{{ $selectedCity }}"
            class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('city') border-red-400 @enderror">
        <option value="">-- Pilih Provinsi Terlebih Dahulu --</option>
    </select>
    @error('city') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
</div>

<div>
    <label for="district" class="mb-1 block text-sm font-medium text-gray-700">Kecamatan <span class="text-red-500">*</span></label>
    <select name="district" id="district" required disabled
            data-selected="{{ $selectedDistrict }}"
            class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('district') border-red-400 @enderror">
        <option value="">-- Pilih Kota Terlebih Dahulu --</option>
    </select>
    @error('district') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
</div>

<div>
    <label for="subdistrict" class="mb-1 block text-sm font-medium text-gray-700">Kelurahan <span class="text-red-500">*</span></label>
    <select name="subdistrict" id="subdistrict" required disabled
            data-selected="{{ $selectedSubdistrict }}"
            class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 @error('subdistrict') border-red-400 @enderror">
        <option value="">-- Pilih Kecamatan Terlebih Dahulu --</option>
    </select>
    @error('subdistrict') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
</div>

@once
<script>
    $(function() {
        const apiBase = '{{ request()->getBaseUrl() }}/api/regions';
        const $province = $('#province');
        const $city = $('#city');
        const $district = $('#district');
        const $subdistrict = $('#subdistrict');

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
                $select.val(selectedValue).trigger('change');
            }
        }

        function loadProvinces() {
            $.get(`${apiBase}/provinces`, function(items) {
                fillSelect($province, items, '-- Pilih Provinsi --', $province.data('selected'));
            });
        }

        function loadCities(provinceCode, selectedValue) {
            $city.html('<option value="">-- Memuat Kota --</option>').prop('disabled', true);
            $.get(`${apiBase}/cities`, { province_code: provinceCode }, function(items) {
                fillSelect($city, items, '-- Pilih Kota --', selectedValue || $city.data('selected'));
            });
        }

        function loadDistricts(cityCode, selectedValue) {
            $district.html('<option value="">-- Memuat Kecamatan --</option>').prop('disabled', true);
            $.get(`${apiBase}/districts`, { city_code: cityCode }, function(items) {
                fillSelect($district, items, '-- Pilih Kecamatan --', selectedValue || $district.data('selected'));
            });
        }

        function loadSubdistricts(districtCode, selectedValue) {
            $subdistrict.html('<option value="">-- Memuat Kelurahan --</option>').prop('disabled', true);
            $.get(`${apiBase}/subdistricts`, { district_code: districtCode }, function(items) {
                fillSelect($subdistrict, items, '-- Pilih Kelurahan --', selectedValue || $subdistrict.data('selected'));
            });
        }

        $province.on('change', function() {
            const code = $(this).find(':selected').data('code');
            const selectedCity = $city.data('selected');
            $city.val('').prop('disabled', true);
            $district.html('<option value="">-- Pilih Kota Terlebih Dahulu --</option>').prop('disabled', true);
            $subdistrict.html('<option value="">-- Pilih Kecamatan Terlebih Dahulu --</option>').prop('disabled', true);
            if (code) loadCities(code, selectedCity);
        });

        $city.on('change', function() {
            const code = $(this).find(':selected').data('code');
            const selectedDistrict = $district.data('selected');
            $district.val('').prop('disabled', true);
            $subdistrict.html('<option value="">-- Pilih Kecamatan Terlebih Dahulu --</option>').prop('disabled', true);
            if (code) loadDistricts(code, selectedDistrict);
        });

        $district.on('change', function() {
            const code = $(this).find(':selected').data('code');
            const selectedSubdistrict = $subdistrict.data('selected');
            $subdistrict.val('').prop('disabled', true);
            if (code) loadSubdistricts(code, selectedSubdistrict);
        });

        $subdistrict.on('change', function() {
            const postalCode = $(this).find(':selected').data('postal-code') || '';
            $('#postal_code').val(postalCode);
            $('#postal_code').prop('readonly', Boolean(postalCode));
        });

        $('form').on('submit', function() {
            $province.prop('disabled', false);
            $city.prop('disabled', false);
            $district.prop('disabled', false);
            $subdistrict.prop('disabled', false);
        });

        loadProvinces();
    });
</script>
@endonce
