<div class="space-y-6">

    {{-- ============================================ --}}
    {{-- INFO DASAR --}}
    {{-- ============================================ --}}
    <div class="space-y-5">

        {{-- Nama --}}
        <div>
            <label for="name" class="flex items-center gap-2 text-sm font-semibold mb-2"
                   style="color: var(--text-3)">
                <iconify-icon icon="mdi:folder-outline" class="text-[#ecbc42] text-base"></iconify-icon>
                Nama Kategori <span class="text-red-400">*</span>
            </label>
            <input type="text" name="name" id="name"
                value="{{ old('name', $category->name ?? '') }}" required
                placeholder="Contoh: Sepatu Futsal"
                class="w-full px-4 py-3 rounded-lg text-sm placeholder-slate-600
                       focus:outline-none transition-all"
                style="background: var(--bg-input); border: 1px solid var(--border-2); color: var(--text-1)">
            @error('name')
                <p class="flex items-center gap-1 text-sm text-red-400 mt-2">
                    <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Deskripsi --}}
        <div>
            <label for="description" class="flex items-center gap-2 text-sm font-semibold mb-2"
                   style="color: var(--text-3)">
                <iconify-icon icon="mdi:text-box-outline" class="text-[#ecbc42] text-base"></iconify-icon>
                Deskripsi
            </label>
            <textarea name="description" id="description" rows="4"
                placeholder="Deskripsi singkat kategori (opsional)"
                class="w-full px-4 py-3 rounded-lg text-sm placeholder-slate-600
                       focus:outline-none transition-all resize-y"
                style="background: var(--bg-input); border: 1px solid var(--border-2); color: var(--text-1)">{{ old('description', $category->description ?? '') }}</textarea>
            @error('description')
                <p class="flex items-center gap-1 text-sm text-red-400 mt-2">
                    <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Gambar --}}
        <div>
            <label for="image" class="flex items-center gap-2 text-sm font-semibold mb-2"
                   style="color: var(--text-3)">
                <iconify-icon icon="mdi:image-outline" class="text-[#ecbc42] text-base"></iconify-icon>
                Gambar Kategori
            </label>
            <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/webp"
                class="w-full px-4 py-3 rounded-lg text-sm
                       file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                       file:text-sm file:font-semibold
                       file:bg-[#ecbc42] file:text-slate-900
                       hover:file:bg-[#d4a72e] file:cursor-pointer cursor-pointer
                       focus:outline-none transition-all"
                style="background: var(--bg-input); border: 1px solid var(--border-2); color: var(--text-1)">
            <p class="flex items-center gap-1 text-xs mt-2" style="color: var(--text-5)">
                <iconify-icon icon="mdi:information-outline"></iconify-icon>
                Maksimal 2MB. Format JPG, PNG, atau WebP.
            </p>
            @error('image')
                <p class="flex items-center gap-1 text-sm text-red-400 mt-2">
                    <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                    {{ $message }}
                </p>
            @enderror

            @if(isset($category) && $category->image)
                <div class="mt-4 inline-block relative group">
                    <img src="{{ asset('storage/' . $category->image) }}"
                         class="w-28 h-28 object-cover rounded-lg border-2 transition-colors"
                         style="border-color: var(--border-2)"
                         onmouseover="this.style.borderColor='#ecbc42'"
                         onmouseout="this.style.borderColor='var(--border-2)'">
                    <span class="absolute bottom-1 right-1 text-[10px] px-1.5 py-0.5 rounded"
                          style="background: rgba(0,0,0,0.7); color: var(--text-3)">
                        Sekarang
                    </span>
                </div>
            @endif
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- DIVIDER --}}
    {{-- ============================================ --}}
    <div class="border-t" style="border-color: var(--border-2)"></div>

    {{-- ============================================ --}}
    {{-- PANDUAN UKURAN --}}
    {{-- ============================================ --}}
    <div>
        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
            <div>
                <h3 class="flex items-center gap-2 text-lg font-bold" style="color: var(--text-1)">
                    <iconify-icon icon="mdi:ruler-square" class="text-[#ecbc42] text-xl"></iconify-icon>
                    Panduan Ukuran
                </h3>
                <p class="text-sm mt-0.5" style="color: var(--text-5)">Atur panduan ukuran untuk kategori ini.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" id="add-dimension"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg
                               transition-all active:scale-95"
                        style="background: var(--bg-input); border: 1px solid var(--border-2); color: var(--text-3)"
                        onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
                        onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'">
                    <iconify-icon icon="mdi:plus-circle-outline"></iconify-icon>
                    Tambah Dimensi
                </button>
                <button type="button" id="add-size-guide"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-bold rounded-lg
                               bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                               text-slate-900
                               hover:shadow-lg hover:shadow-amber-500/30
                               transition-all active:scale-95">
                    <iconify-icon icon="mdi:plus-circle"></iconify-icon>
                    Tambah Ukuran
                </button>
            </div>
        </div>

        {{-- 🔥 DIMENSI LABELS --}}
        <div class="mb-5">
            <label class="block text-xs font-bold uppercase tracking-wider mb-2" style="color: var(--text-5)">
                Label Dimensi
            </label>
            <div id="dimension-labels-container" class="flex flex-wrap gap-2">
                @php
                    $dimensionLabels = isset($category) ? $category->dimension_labels : [];
                    $defaultLabels = ['Lebar Dada', 'Panjang Lengan', 'Panjang Badan'];
                    $labels = !empty($dimensionLabels) ? $dimensionLabels : $defaultLabels;
                @endphp

                @foreach($labels as $index => $label)
                    <div class="dimension-label-item flex items-center gap-2 rounded-lg px-3 py-2
                                border border-[#ecbc42]/30 hover:border-[#ecbc42]
                                transition-colors"
                         style="background: var(--bg-input)">
                        <iconify-icon icon="mdi:label-outline" class="text-[#ecbc42] text-sm"></iconify-icon>
                        <span class="text-sm font-semibold text-[#FDDD57]">{{ $label }}</span>
                        <input type="hidden" name="dimension_labels[]" value="{{ $label }}">
                        <button type="button"
                                class="remove-dimension text-lg leading-none transition-colors"
                                style="color: var(--text-5)"
                                onmouseover="this.style.color='#f87171'"
                                onmouseout="this.style.color='var(--text-5)'"
                                title="Hapus dimensi">×</button>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- 🔥 SIZE GUIDE TABLE --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <label class="block text-xs font-bold uppercase tracking-wider" style="color: var(--text-5)">
                    Tabel Ukuran
                </label>
                <span class="text-[10px] font-mono" style="color: var(--text-5)">
                    <span id="row-count">0</span> baris
                </span>
            </div>

            {{-- Table Wrapper --}}
            <div class="rounded-xl overflow-hidden border"
                 style="border-color: var(--border-2); background: var(--bg-input)">

                {{-- Scrollable Area --}}
                <div class="overflow-x-auto">
                    <div id="size-guides-container" class="min-w-max">

                        {{-- Table Header --}}
                        <div class="size-guide-header flex items-center gap-2 px-4 py-3 border-b sticky top-0 z-10"
                             style="background: var(--bg-card); border-color: var(--border-2)">
                            <div class="w-24 flex-shrink-0">
                                <span class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5)">Ukuran</span>
                            </div>
                            <div id="header-dimensions" class="flex items-center gap-2">
                                {{-- Header dimensions akan di-inject oleh JS --}}
                            </div>
                            <div class="w-10 flex-shrink-0"></div>
                        </div>

                        {{-- Table Body --}}
                        <div id="size-guides-body">
                            @php
                                $sizeGuides = isset($category) ? $category->sizeGuides : collect();
                                $defaultSizes = ['S', 'M', 'L', 'XL', 'XXL', '3XL'];
                                $currentLabels = $labels;
                            @endphp

                            @if($sizeGuides->isNotEmpty())
                                @foreach($sizeGuides as $index => $guide)
                                    <div class="size-guide-row flex items-center gap-2 px-4 py-2.5 transition-colors group border-b last:border-0"
                                         style="border-color: var(--border-1)"
                                         onmouseover="this.style.background='var(--bg-hover)'"
                                         onmouseout="this.style.background='transparent'">
                                        <input type="hidden" name="size_guides[{{ $index }}][id]" value="{{ $guide->id }}">

                                        <div class="w-24 flex-shrink-0">
                                            <input type="text" name="size_guides[{{ $index }}][size]" value="{{ $guide->size }}"
                                                class="w-full px-3 py-2 rounded-lg text-sm font-semibold text-center
                                                       focus:outline-none transition-all"
                                                style="background: var(--bg-card); border: 1px solid var(--border-2); color: var(--text-1)"
                                                placeholder="S" required>
                                        </div>

                                        @foreach($currentLabels as $labelIndex => $label)
                                            @php
                                                $dimensionValue = $guide->dimensions[$label] ?? '';
                                            @endphp
                                            <div class="w-28 flex-shrink-0">
                                                <input type="number" name="size_guides[{{ $index }}][dimensions][{{ $labelIndex }}]"
                                                    value="{{ $dimensionValue }}"
                                                    class="w-full px-3 py-2 rounded-lg text-sm text-center
                                                           focus:outline-none transition-all"
                                                    style="background: var(--bg-card); border: 1px solid var(--border-2); color: var(--text-1)"
                                                    placeholder="0">
                                            </div>
                                        @endforeach

                                        <div class="w-10 flex-shrink-0 flex justify-center">
                                            <button type="button"
                                                    class="remove-size-guide flex items-center justify-center w-8 h-8 rounded-lg transition-all opacity-60 group-hover:opacity-100"
                                                    style="color: var(--text-5)"
                                                    onmouseover="this.style.background='rgba(239,68,68,0.1)'; this.style.color='#f87171'"
                                                    onmouseout="this.style.background='transparent'; this.style.color='var(--text-5)'"
                                                    title="Hapus ukuran">
                                                <iconify-icon icon="mdi:trash-can-outline" class="text-base"></iconify-icon>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                @foreach($defaultSizes as $index => $size)
                                    <div class="size-guide-row flex items-center gap-2 px-4 py-2.5 transition-colors group border-b last:border-0"
                                         style="border-color: var(--border-1)"
                                         onmouseover="this.style.background='var(--bg-hover)'"
                                         onmouseout="this.style.background='transparent'">
                                        <div class="w-24 flex-shrink-0">
                                            <input type="text" name="size_guides[{{ $index }}][size]" value="{{ $size }}"
                                                class="w-full px-3 py-2 rounded-lg text-sm font-semibold text-center
                                                       focus:outline-none transition-all"
                                                style="background: var(--bg-card); border: 1px solid var(--border-2); color: var(--text-1)"
                                                placeholder="S" required>
                                        </div>

                                        @foreach($currentLabels as $labelIndex => $label)
                                            <div class="w-28 flex-shrink-0">
                                                <input type="number" name="size_guides[{{ $index }}][dimensions][{{ $labelIndex }}]"
                                                    value=""
                                                    class="w-full px-3 py-2 rounded-lg text-sm text-center
                                                           focus:outline-none transition-all"
                                                    style="background: var(--bg-card); border: 1px solid var(--border-2); color: var(--text-1)"
                                                    placeholder="0">
                                            </div>
                                        @endforeach

                                        <div class="w-10 flex-shrink-0 flex justify-center">
                                            <button type="button"
                                                    class="remove-size-guide flex items-center justify-center w-8 h-8 rounded-lg transition-all opacity-60 group-hover:opacity-100"
                                                    style="color: var(--text-5)"
                                                    onmouseover="this.style.background='rgba(239,68,68,0.1)'; this.style.color='#f87171'"
                                                    onmouseout="this.style.background='transparent'; this.style.color='var(--text-5)'"
                                                    title="Hapus ukuran">
                                                <iconify-icon icon="mdi:trash-can-outline" class="text-base"></iconify-icon>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                    </div>
                </div>

                {{-- Empty State --}}
                <div id="size-guides-empty" class="hidden px-4 py-8 text-center">
                    <iconify-icon icon="mdi:table-off" class="text-3xl" style="color: var(--text-6)"></iconify-icon>
                    <p class="text-sm mt-2" style="color: var(--text-5)">Belum ada ukuran. Klik "Tambah Ukuran" untuk memulai.</p>
                </div>

            </div>

            <p class="flex items-center gap-1 text-xs mt-3" style="color: var(--text-5)">
                <iconify-icon icon="mdi:information-outline"></iconify-icon>
                Kosongkan nilai jika tidak ingin menampilkan. Scroll horizontal jika kolom terlalu banyak.
            </p>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- DIVIDER --}}
    {{-- ============================================ --}}
    <div class="border-t" style="border-color: var(--border-2)"></div>

    {{-- ============================================ --}}
    {{-- STATUS --}}
    {{-- ============================================ --}}
    <div>
        <label class="flex items-center gap-3 cursor-pointer group w-fit">
            <input type="checkbox" name="is_active" value="1"
                @checked(old('is_active', $category->is_active ?? true))
                class="w-5 h-5 rounded cursor-pointer"
                style="accent-color: #ecbc42; background: var(--bg-input); border-color: var(--border-3)">
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium transition-colors group-hover:text-[#FDDD57]"
                      style="color: var(--text-3)">
                    Aktifkan kategori
                </span>
            </div>
        </label>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('size-guides-body');
    const labelsContainer = document.getElementById('dimension-labels-container');
    const headerDimensions = document.getElementById('header-dimensions');
    const addSizeBtn = document.getElementById('add-size-guide');
    const addDimensionBtn = document.getElementById('add-dimension');
    const rowCountEl = document.getElementById('row-count');
    const emptyState = document.getElementById('size-guides-empty');

    // ============================================
    // 🔥 CLASS HELPERS (pakai CSS variables)
    // ============================================
    const sizeInputClass = 'w-full px-3 py-2 rounded-lg text-sm font-semibold text-center focus:outline-none transition-all';
    const dimInputClass = 'w-full px-3 py-2 rounded-lg text-sm text-center focus:outline-none transition-all';
    const inputStyle = 'background: var(--bg-card); border: 1px solid var(--border-2); color: var(--text-1)';
    const rowClass = 'size-guide-row flex items-center gap-2 px-4 py-2.5 transition-colors group border-b last:border-0';
    const rowStyle = 'border-color: var(--border-1)';
    const removeBtn = `<button type="button" class="remove-size-guide flex items-center justify-center w-8 h-8 rounded-lg transition-all opacity-60 group-hover:opacity-100" style="color: var(--text-5)" onmouseover="this.style.background='rgba(239,68,68,0.1)'; this.style.color='#f87171'" onmouseout="this.style.background='transparent'; this.style.color='var(--text-5)'" title="Hapus ukuran"><iconify-icon icon="mdi:trash-can-outline" class="text-base"></iconify-icon></button>`;

    // ============================================
    // 🔥 RENDER HEADER DIMENSIONS
    // ============================================
    function renderHeaderDimensions() {
        const labels = getDimensionLabels();
        let html = '';
        labels.forEach(function(label) {
            html += `
                <div class="w-28 flex-shrink-0">
                    <span class="text-[10px] font-bold uppercase tracking-wider truncate block text-center" style="color: var(--text-5)" title="${label}">
                        ${label} (cm)
                    </span>
                </div>
            `;
        });
        headerDimensions.innerHTML = html;
    }

    // ============================================
    // 🔥 BUILD ROW HTML
    // ============================================
    function buildRowHtml(index, size, dimensions, id) {
        const labels = getDimensionLabels();

        let idHtml = '';
        if (id) {
            idHtml = `<input type="hidden" name="size_guides[${index}][id]" value="${id}">`;
        }

        let dimensionsHtml = '';
        labels.forEach(function(label, labelIndex) {
            const value = dimensions[label] || '';
            dimensionsHtml += `
                <div class="w-28 flex-shrink-0">
                    <input type="number" name="size_guides[${index}][dimensions][${labelIndex}]"
                           value="${value}"
                           class="${dimInputClass}"
                           style="${inputStyle}"
                           placeholder="0">
                </div>
            `;
        });

        return `
            ${idHtml}
            <div class="w-24 flex-shrink-0">
                <input type="text" name="size_guides[${index}][size]" value="${size}"
                       class="${sizeInputClass}"
                       style="${inputStyle}"
                       placeholder="S" required>
            </div>
            ${dimensionsHtml}
            <div class="w-10 flex-shrink-0 flex justify-center">
                ${removeBtn}
            </div>
        `;
    }

    // ============================================
    // 🔥 UPDATE ROW COUNTER & EMPTY STATE
    // ============================================
    function updateRowState() {
        const rows = container.querySelectorAll('.size-guide-row');
        if (rowCountEl) rowCountEl.textContent = rows.length;

        if (emptyState) {
            if (rows.length === 0) {
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
            }
        }
    }

    // ============================================
    // 🔥 TAMBAH UKURAN
    // ============================================
    if (addSizeBtn) {
        addSizeBtn.addEventListener('click', function() {
            const rows = container.querySelectorAll('.size-guide-row');
            const index = rows.length;

            const row = document.createElement('div');
            row.className = rowClass;
            row.style.cssText = rowStyle;
            row.onmouseover = function() { this.style.background = 'var(--bg-hover)'; };
            row.onmouseout = function() { this.style.background = 'transparent'; };
            row.innerHTML = buildRowHtml(index, '', {}, null);
            container.appendChild(row);

            reindexSizeGuides();
            updateRowState();

            const newRow = container.lastElementChild;
            const sizeInput = newRow.querySelector('input[name*="[size]"]');
            if (sizeInput) sizeInput.focus();
        });
    }

    // ============================================
    // 🔥 TAMBAH DIMENSI
    // ============================================
    if (addDimensionBtn) {
        addDimensionBtn.addEventListener('click', function() {
            const labelInput = prompt('Masukkan nama dimensi (contoh: Lebar Dada, Panjang Lengan, Lebar Pinggang):');
            if (labelInput && labelInput.trim() !== '') {
                const label = labelInput.trim();

                const existingLabels = getDimensionLabels();
                if (existingLabels.includes(label)) {
                    alert('Dimensi "' + label + '" sudah ada!');
                    return;
                }

                const item = document.createElement('div');
                item.className = 'dimension-label-item flex items-center gap-2 rounded-lg px-3 py-2 border border-[#ecbc42]/30 hover:border-[#ecbc42] transition-colors';
                item.style.background = 'var(--bg-input)';
                item.innerHTML = `
                    <iconify-icon icon="mdi:label-outline" class="text-[#ecbc42] text-sm"></iconify-icon>
                    <span class="text-sm font-semibold text-[#FDDD57]">${label}</span>
                    <input type="hidden" name="dimension_labels[]" value="${label}">
                    <button type="button" class="remove-dimension text-lg leading-none transition-colors" style="color: var(--text-5)" onmouseover="this.style.color='#f87171'" onmouseout="this.style.color='var(--text-5)'" title="Hapus dimensi">×</button>
                `;
                labelsContainer.appendChild(item);

                renderHeaderDimensions();
                updateAllRows();
            }
        });
    }

    // ============================================
    // 🔥 HAPUS DIMENSI
    // ============================================
    if (labelsContainer) {
        labelsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-dimension')) {
                const item = e.target.closest('.dimension-label-item');
                const label = item.querySelector('span').textContent;

                if (confirm(`Hapus dimensi "${label}"? Semua nilai "${label}" di tabel akan hilang.`)) {
                    item.remove();
                    renderHeaderDimensions();
                    updateAllRows();
                }
            }
        });
    }

    // ============================================
    // 🔥 HAPUS UKURAN
    // ============================================
    if (container) {
        container.addEventListener('click', function(e) {
            if (e.target.closest('.remove-size-guide')) {
                const row = e.target.closest('.size-guide-row');
                const rows = container.querySelectorAll('.size-guide-row');

                if (rows.length > 1) {
                    row.remove();
                    reindexSizeGuides();
                    updateRowState();
                } else {
                    row.querySelectorAll('input[type="text"], input[type="number"]').forEach(function(input) {
                        input.value = '';
                    });
                }
            }
        });
    }

    // ============================================
    // 🔥 GET DIMENSION LABELS
    // ============================================
    function getDimensionLabels() {
        const inputs = labelsContainer.querySelectorAll('input[name="dimension_labels[]"]');
        return Array.from(inputs).map(el => el.value);
    }

    // ============================================
    // 🔥 UPDATE ALL ROWS
    // ============================================
    function updateAllRows() {
        const rows = container.querySelectorAll('.size-guide-row');
        const labels = getDimensionLabels();

        rows.forEach(function(row, index) {
            const sizeInput = row.querySelector('input[name*="[size]"]');
            const size = sizeInput ? sizeInput.value : '';

            const idInput = row.querySelector('input[name*="[id]"]');
            const id = idInput ? idInput.value : null;

            const existingDimensions = {};
            const oldLabels = getDimensionLabelsBeforeUpdate(row);

            row.querySelectorAll('input[name*="[dimensions]"]').forEach(function(input) {
                const match = input.name.match(/dimensions\[(\d+)\]/);
                if (match) {
                    const labelIndex = parseInt(match[1]);
                    if (oldLabels[labelIndex]) {
                        existingDimensions[oldLabels[labelIndex]] = input.value;
                    }
                }
            });

            row.innerHTML = buildRowHtml(index, size, existingDimensions, id);
        });
    }

    function getDimensionLabelsBeforeUpdate(row) {
        const inputs = labelsContainer.querySelectorAll('input[name="dimension_labels[]"]');
        return Array.from(inputs).map(el => el.value);
    }

    // ============================================
    // 🔥 REINDEX SIZE GUIDES
    // ============================================
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

    // ============================================
    // 🔥 INIT
    // ============================================
    renderHeaderDimensions();
    updateRowState();

    console.log('✅ Size guide table initialized');
});
</script>