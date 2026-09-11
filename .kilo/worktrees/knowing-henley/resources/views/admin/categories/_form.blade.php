<div class="space-y-6">

    {{-- Nama --}}
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
            Nama Kategori
        </label>
        <input type="text" name="name" id="name" 
            value="{{ old('name', $category->name ?? '') }}" required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('name') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
    </div>

    {{-- Deskripsi --}}
    <div>
        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
            Deskripsi
        </label>
        <textarea name="description" id="description" rows="4"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $category->description ?? '') }}</textarea>
        @error('description') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
    </div>

    {{-- Gambar --}}
    <div>
        <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
            Gambar Kategori
        </label>
        <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/webp"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg">
        <p class="text-xs text-gray-500 mt-2">Maksimal 2MB. Format JPG, PNG, atau WebP.</p>
        @error('image') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror

        @if(isset($category) && $category->image)
            <div class="mt-4">
                <img src="{{ asset('storage/' . $category->image) }}" class="w-24 h-24 object-cover rounded-lg">
            </div>
        @endif
    </div>

    {{-- 🔥 PANDUAN UKURAN --}}
    <div class="border-t pt-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">📏 Panduan Ukuran</h3>
            <p class="text-sm text-gray-500">Atur panduan ukuran untuk kategori ini.</p>
        </div>
        <div class="flex gap-2">
            <button type="button" id="add-dimension" 
                    class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                + Tambah Dimensi
            </button>
            <button type="button" id="add-size-guide" 
                    class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
                + Tambah Ukuran
            </button>
        </div>
    </div>

    {{-- 🔥 DIMENSI LABELS --}}
    <div id="dimension-labels-container" class="flex flex-wrap gap-2 mb-4">
        @php
            $dimensionLabels = isset($category) ? $category->dimension_labels : [];
            $defaultLabels = ['Lebar Dada', 'Panjang Lengan', 'Panjang Badan'];
            $labels = !empty($dimensionLabels) ? $dimensionLabels : $defaultLabels;
        @endphp
        
        @foreach($labels as $index => $label)
            <div class="dimension-label-item flex items-center gap-2 bg-blue-50 border border-blue-200 rounded-lg px-3 py-2">
                <span class="text-sm font-medium text-blue-700">{{ $label }}</span>
                <input type="hidden" name="dimension_labels[]" value="{{ $label }}">
                <button type="button" class="remove-dimension text-blue-400 hover:text-red-500 text-lg leading-none">×</button>
            </div>
        @endforeach
    </div>

    {{-- 🔥 SIZE GUIDE TABLE --}}
    <div class="overflow-x-auto">
        <div>
            <div id="size-guides-container" class="min-w-full">
                @php
                    $sizeGuides = isset($category) ? $category->sizeGuides : collect();
                    $defaultSizes = ['S', 'M', 'L', 'XL', 'XXL', '3XL'];
                    $currentLabels = $labels;
                @endphp

                @if($sizeGuides->isNotEmpty())
                    @foreach($sizeGuides as $index => $guide)
                        <div class="size-guide-row flex flex-wrap items-center gap-2 p-4 border border-gray-200 rounded-lg bg-gray-50 mb-3">
                            <input type="hidden" name="size_guides[{{ $index }}][id]" value="{{ $guide->id }}">
                            
                            <div class="min-w-[60px]">
                                <label class="block text-xs font-medium text-gray-600">Ukuran</label>
                                <input type="text" name="size_guides[{{ $index }}][size]" value="{{ $guide->size }}" 
                                    class="w-16 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                    placeholder="S" required>
                            </div>
                            
                            @foreach($currentLabels as $labelIndex => $label)
                                @php
                                    $dimensionValue = $guide->dimensions[$label] ?? '';
                                @endphp
                                <div class="min-w-[80px]">
                                    <label class="block text-xs font-medium text-gray-600">{{ $label }} (cm)</label>
                                    <input type="number" name="size_guides[{{ $index }}][dimensions][{{ $labelIndex }}]" 
                                        value="{{ $dimensionValue }}"
                                        class="w-20 px-3 py-2 border border-gray-300 rounded-lg text-sm" 
                                        placeholder="0">
                                </div>
                            @endforeach
                            
                            <button type="button" class="remove-size-guide text-red-500 hover:text-red-700 px-2 py-1 text-lg">✕</button>
                        </div>
                    @endforeach
                @else
                    @foreach($defaultSizes as $index => $size)
                        <div class="size-guide-row flex flex-wrap items-center gap-2 p-4 border border-gray-200 rounded-lg bg-gray-50 mb-3">
                            <div class="min-w-[60px]">
                                <label class="block text-xs font-medium text-gray-600">Ukuran</label>
                                <input type="text" name="size_guides[{{ $index }}][size]" value="{{ $size }}" 
                                    class="w-16 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                    placeholder="S" required>
                            </div>
                            
                            @foreach($currentLabels as $labelIndex => $label)
                                <div class="min-w-[80px]">
                                    <label class="block text-xs font-medium text-gray-600">{{ $label }} (cm)</label>
                                    <input type="number" name="size_guides[{{ $index }}][dimensions][{{ $labelIndex }}]" 
                                        value=""
                                        class="w-20 px-3 py-2 border border-gray-300 rounded-lg text-sm" 
                                        placeholder="0">
                                </div>
                            @endforeach
                            
                            <button type="button" class="remove-size-guide text-red-500 hover:text-red-700 px-2 py-1 text-lg">✕</button>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <p class="text-xs text-gray-400 mt-2">Kosongkan nilai jika tidak ingin menampilkan.</p>
    </div>

    {{-- Status --}}
    <div>
        <label class="flex items-center gap-3">
            <input type="checkbox" name="is_active" value="1"
                @checked(old('is_active', $category->is_active ?? true))
                class="w-5 h-5 text-blue-600 rounded">
            <span class="text-sm text-gray-700">Aktifkan kategori</span>
        </label>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('size-guides-container');
    const labelsContainer = document.getElementById('dimension-labels-container');
    const addSizeBtn = document.getElementById('add-size-guide');
    const addDimensionBtn = document.getElementById('add-dimension');

    // 🔥 TAMBAH UKURAN
    addSizeBtn.addEventListener('click', function() {
        const rows = container.querySelectorAll('.size-guide-row');
        const index = rows.length;
        const labels = getDimensionLabels();

        let dimensionsHtml = '';
        labels.forEach(function(label, labelIndex) {
            dimensionsHtml += `
                <div class="min-w-[80px]">
                    <label class="block text-xs font-medium text-gray-600">${label} (cm)</label>
                    <input type="number" name="size_guides[${index}][dimensions][${labelIndex}]" 
                           value=""
                           class="w-20 px-3 py-2 border border-gray-300 rounded-lg text-sm" 
                           placeholder="0">
                </div>
            `;
        });

        const row = document.createElement('div');
        row.className = 'size-guide-row flex flex-wrap items-center gap-2 p-4 border border-gray-200 rounded-lg bg-gray-50 mb-3';
        row.innerHTML = `
            <div class="min-w-[60px]">
                <label class="block text-xs font-medium text-gray-600">Ukuran</label>
                <input type="text" name="size_guides[${index}][size]" value="" 
                       class="w-16 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                       placeholder="S" required>
            </div>
            ${dimensionsHtml}
            <button type="button" class="remove-size-guide text-red-500 hover:text-red-700 px-2 py-1 text-lg">✕</button>
        `;
        container.appendChild(row);
        reindexSizeGuides();
    });

    // 🔥 TAMBAH DIMENSI
    addDimensionBtn.addEventListener('click', function() {
        const labelInput = prompt('Masukkan nama dimensi (contoh: Lebar Dada, Panjang Lengan, Lebar Pinggang):');
        if (labelInput && labelInput.trim() !== '') {
            const label = labelInput.trim();
            
            // Tambah ke labels container
            const item = document.createElement('div');
            item.className = 'dimension-label-item flex items-center gap-2 bg-blue-50 border border-blue-200 rounded-lg px-3 py-2';
            item.innerHTML = `
                <span class="text-sm font-medium text-blue-700">${label}</span>
                <input type="hidden" name="dimension_labels[]" value="${label}">
                <button type="button" class="remove-dimension text-blue-400 hover:text-red-500 text-lg leading-none">×</button>
            `;
            labelsContainer.appendChild(item);

            // Update semua row dengan dimensi baru
            updateAllRowsWithDimension(label);
        }
    });

    // 🔥 HAPUS DIMENSI
    labelsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-dimension')) {
            const item = e.target.closest('.dimension-label-item');
            const label = item.querySelector('span').textContent;
            
            if (confirm(`Hapus dimensi "${label}"?`)) {
                item.remove();
                // Hapus dari semua row
                const rows = container.querySelectorAll('.size-guide-row');
                rows.forEach(function(row) {
                    const inputs = row.querySelectorAll('input[name*="[dimensions]"]');
                    // Cari index yang sesuai
                    const labelInputs = labelsContainer.querySelectorAll('input[name="dimension_labels[]"]');
                    const labels = Array.from(labelInputs).map(el => el.value);
                    
                    // Filter dimensi yang sesuai
                    // Kita akan rebuild row
                    rebuildRow(row, labels);
                });
                reindexSizeGuides();
            }
        }
    });

    // 🔥 HAPUS UKURAN
    container.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-size-guide')) {
            const row = e.target.closest('.size-guide-row');
            const rows = container.querySelectorAll('.size-guide-row');
            if (rows.length > 1) {
                row.remove();
                reindexSizeGuides();
            } else {
                row.querySelectorAll('input').forEach(input => {
                    if (input.name.includes('[size]')) {
                        input.value = '';
                    } else if (input.name.includes('[dimensions]')) {
                        input.value = '';
                    }
                });
            }
        }
    });

    // 🔥 GET DIMENSION LABELS
    function getDimensionLabels() {
        const inputs = labelsContainer.querySelectorAll('input[name="dimension_labels[]"]');
        return Array.from(inputs).map(el => el.value);
    }

    // 🔥 UPDATE ALL ROWS WITH NEW DIMENSION
    function updateAllRowsWithDimension(newLabel) {
        const rows = container.querySelectorAll('.size-guide-row');
        const labels = getDimensionLabels();
        
        rows.forEach(function(row, index) {
            const size = row.querySelector('input[name*="[size]"]').value;
            const existingDimensions = {};
            
            // Ambil nilai dimensi yang sudah ada
            row.querySelectorAll('input[name*="[dimensions]"]').forEach(function(input) {
                const name = input.name;
                const match = name.match(/dimensions\[(\d+)\]/);
                if (match) {
                    const labelIndex = parseInt(match[1]);
                    const labelInputs = labelsContainer.querySelectorAll('input[name="dimension_labels[]"]');
                    const labelNames = Array.from(labelInputs).map(el => el.value);
                    if (labelNames[labelIndex]) {
                        existingDimensions[labelNames[labelIndex]] = input.value;
                    }
                }
            });
            
            // Rebuild row dengan semua dimensi
            let dimensionsHtml = '';
            labels.forEach(function(label, labelIndex) {
                const value = existingDimensions[label] || '';
                dimensionsHtml += `
                    <div class="min-w-[80px]">
                        <label class="block text-xs font-medium text-gray-600">${label} (cm)</label>
                        <input type="number" name="size_guides[${index}][dimensions][${labelIndex}]" 
                               value="${value}"
                               class="w-20 px-3 py-2 border border-gray-300 rounded-lg text-sm" 
                               placeholder="0">
                    </div>
                `;
            });
            
            row.innerHTML = `
                <div class="min-w-[60px]">
                    <label class="block text-xs font-medium text-gray-600">Ukuran</label>
                    <input type="text" name="size_guides[${index}][size]" value="${size}" 
                           class="w-16 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                           placeholder="S" required>
                </div>
                ${dimensionsHtml}
                <button type="button" class="remove-size-guide text-red-500 hover:text-red-700 px-2 py-1 text-lg">✕</button>
            `;
        });
    }

    // 🔥 REBUILD ROW
    function rebuildRow(row, labels) {
        const index = Array.from(container.querySelectorAll('.size-guide-row')).indexOf(row);
        const size = row.querySelector('input[name*="[size]"]').value;
        
        // Ambil nilai dimensi yang ada
        const existingValues = {};
        row.querySelectorAll('input[name*="[dimensions]"]').forEach(function(input) {
            const name = input.name;
            const match = name.match(/dimensions\[(\d+)\]/);
            if (match) {
                const labelIndex = parseInt(match[1]);
                const labelInputs = labelsContainer.querySelectorAll('input[name="dimension_labels[]"]');
                const labelNames = Array.from(labelInputs).map(el => el.value);
                if (labelNames[labelIndex]) {
                    existingValues[labelNames[labelIndex]] = input.value;
                }
            }
        });
        
        let dimensionsHtml = '';
        labels.forEach(function(label, labelIndex) {
            const value = existingValues[label] || '';
            dimensionsHtml += `
                <div class="min-w-[80px]">
                    <label class="block text-xs font-medium text-gray-600">${label} (cm)</label>
                    <input type="number" name="size_guides[${index}][dimensions][${labelIndex}]" 
                           value="${value}"
                           class="w-20 px-3 py-2 border border-gray-300 rounded-lg text-sm" 
                           placeholder="0">
                </div>
            `;
        });
        
        row.innerHTML = `
            <div class="min-w-[60px]">
                <label class="block text-xs font-medium text-gray-600">Ukuran</label>
                <input type="text" name="size_guides[${index}][size]" value="${size}" 
                       class="w-16 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                       placeholder="S" required>
            </div>
            ${dimensionsHtml}
            <button type="button" class="remove-size-guide text-red-500 hover:text-red-700 px-2 py-1 text-lg">✕</button>
        `;
    }

    // 🔥 REINDEX SIZE GUIDES
    function reindexSizeGuides() {
        const rows = container.querySelectorAll('.size-guide-row');
        rows.forEach(function(row, index) {
            row.querySelectorAll('input').forEach(function(input) {
                const name = input.name;
                const newName = name.replace(/size_guides\[\d+\]/, `size_guides[${index}]`);
                input.name = newName;
            });
        });
    }
});
</script>