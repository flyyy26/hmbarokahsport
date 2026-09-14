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
            <p class="flex items-center gap-1 text-xs mt-1" style="color: #34d399;">
                <iconify-icon icon="mdi:check-decagram"></iconify-icon>
                Gambar akan otomatis dikonversi ke WebP untuk performa lebih baik.
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
                        <span class="text-sm font-semibold text-[#c0911b]">{{ $label }}</span>
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

            {{-- Container Grid 2 Kolom --}}
            <div id="size-guides-container"
                class="grid grid-cols-1 lg:grid-cols-2 gap-3">

                {{-- Baris akan di-generate oleh JS --}}
                @php
                    $sizeGuides = isset($category) ? $category->sizeGuides : collect();
                    $defaultSizes = ['S', 'M', 'L', 'XL', 'XXL', '3XL'];
                    $currentLabels = $labels;
                @endphp

                @if($sizeGuides->isNotEmpty())
                    @foreach($sizeGuides as $index => $guide)
                        <div class="size-guide-card rounded-xl border overflow-hidden transition-colors"
                            style="background: var(--bg-input); border-color: var(--border-2);">
                            <div class="flex items-center gap-2 px-3 py-2.5 border-b"
                                style="background: var(--bg-card); border-color: var(--border-2);">
                                <iconify-icon icon="mdi:ruler" class="text-[#ecbc42]"></iconify-icon>
                                <input type="text"
                                    name="size_guides[{{ $index }}][size]"
                                    value="{{ $guide->size }}"
                                    placeholder="S"
                                    required
                                    class="flex-1 px-3 py-1.5 rounded-lg text-sm font-bold text-center
                                            focus:outline-none transition-all"
                                    style="background: var(--bg-input); border: 1px solid var(--border-2); color: var(--text-1)">
                                <input type="hidden" name="size_guides[{{ $index }}][id]" value="{{ $guide->id }}">
                                <button type="button"
                                        class="remove-size-guide inline-flex items-center justify-center w-7 h-7 rounded-lg transition-all"
                                        style="color: var(--text-5)"
                                        onmouseover="this.style.background='rgba(239,68,68,0.1)'; this.style.color='#f87171'"
                                        onmouseout="this.style.background='transparent'; this.style.color='var(--text-5)'"
                                        title="Hapus ukuran">
                                    <iconify-icon icon="mdi:trash-can-outline" class="text-sm"></iconify-icon>
                                </button>
                            </div>

                            <div class="p-3 grid grid-cols-2 gap-2">
                                @foreach($currentLabels as $labelIndex => $label)
                                    @php
                                        $dimensionValue = $guide->dimensions[$label] ?? '';
                                    @endphp
                                    <div>
                                        <label class="block text-[9px] font-bold uppercase tracking-wider mb-1 truncate"
                                            style="color: var(--text-5)"
                                            title="{{ $label }} (cm)">
                                            {{ $label }}
                                        </label>
                                        <input type="number"
                                            name="size_guides[{{ $index }}][dimensions][{{ $labelIndex }}]"
                                            value="{{ $dimensionValue }}"
                                            placeholder="0"
                                            class="w-full px-2.5 py-1.5 rounded-lg text-sm text-center
                                                    focus:outline-none transition-all"
                                            style="background: var(--bg-card); border: 1px solid var(--border-2); color: var(--text-1)">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @else
                    @foreach($defaultSizes as $index => $size)
                        <div class="size-guide-card rounded-xl border overflow-hidden transition-colors"
                            style="background: var(--bg-input); border-color: var(--border-2);">
                            <div class="flex items-center gap-2 px-3 py-2.5 border-b"
                                style="background: var(--bg-card); border-color: var(--border-2);">
                                <iconify-icon icon="mdi:ruler" class="text-[#ecbc42]"></iconify-icon>
                                <input type="text"
                                    name="size_guides[{{ $index }}][size]"
                                    value="{{ $size }}"
                                    placeholder="S"
                                    required
                                    class="flex-1 px-3 py-1.5 rounded-lg text-sm font-bold text-center
                                            focus:outline-none transition-all"
                                    style="background: var(--bg-input); border: 1px solid var(--border-2); color: var(--text-1)">
                                <button type="button"
                                        class="remove-size-guide inline-flex items-center justify-center w-7 h-7 rounded-lg transition-all"
                                        style="color: var(--text-5)"
                                        onmouseover="this.style.background='rgba(239,68,68,0.1)'; this.style.color='#f87171'"
                                        onmouseout="this.style.background='transparent'; this.style.color='var(--text-5)'"
                                        title="Hapus ukuran">
                                    <iconify-icon icon="mdi:trash-can-outline" class="text-sm"></iconify-icon>
                                </button>
                            </div>

                            <div class="p-3 grid grid-cols-2 gap-2">
                                @foreach($currentLabels as $labelIndex => $label)
                                    <div>
                                        <label class="block text-[9px] font-bold uppercase tracking-wider mb-1 truncate"
                                            style="color: var(--text-5)"
                                            title="{{ $label }} (cm)">
                                            {{ $label }}
                                        </label>
                                        <input type="number"
                                            name="size_guides[{{ $index }}][dimensions][{{ $labelIndex }}]"
                                            value=""
                                            placeholder="0"
                                            class="w-full px-2.5 py-1.5 rounded-lg text-sm text-center
                                                    focus:outline-none transition-all"
                                            style="background: var(--bg-card); border: 1px solid var(--border-2); color: var(--text-1)">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- Empty State --}}
            <div id="size-guides-empty" class="hidden px-4 py-12 text-center rounded-xl border border-dashed"
                style="border-color: var(--border-2); background: var(--bg-input);">
                <iconify-icon icon="mdi:table-off" class="text-3xl" style="color: var(--text-6)"></iconify-icon>
                <p class="text-sm mt-2" style="color: var(--text-5)">Belum ada ukuran. Klik "Tambah Ukuran" untuk memulai.</p>
            </div>

            <p class="flex items-center gap-1 text-xs mt-3" style="color: var(--text-5)">
                <iconify-icon icon="mdi:information-outline"></iconify-icon>
                Kosongkan nilai jika tidak ingin menampilkan.
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
    const container = document.getElementById('size-guides-container');
    const labelsContainer = document.getElementById('dimension-labels-container');
    const addSizeBtn = document.getElementById('add-size-guide');
    const addDimensionBtn = document.getElementById('add-dimension');
    const rowCountEl = document.getElementById('row-count');
    const emptyState = document.getElementById('size-guides-empty');

    // ============================================
    // HELPER: Get dimension labels
    // ============================================
    function getDimensionLabels() {
        const inputs = labelsContainer.querySelectorAll('input[name="dimension_labels[]"]');
        return Array.from(inputs).map(el => el.value);
    }

    // ============================================
    // HELPER: Build card HTML
    // ============================================
    function buildCardHtml(index, size, dimensions, id) {
        const labels = getDimensionLabels();

        const idHtml = id
            ? `<input type="hidden" name="size_guides[${index}][id]" value="${id}">`
            : '';

        let dimensionsHtml = '';
        labels.forEach(function(label, labelIndex) {
            const value = dimensions[label] || '';
            dimensionsHtml += `
                <div>
                    <label class="block text-[9px] font-bold uppercase tracking-wider mb-1 truncate" style="color: var(--text-5)" title="${label} (cm)">
                        ${label}
                    </label>
                    <input type="number"
                           name="size_guides[${index}][dimensions][${labelIndex}]"
                           value="${value}"
                           placeholder="0"
                           class="w-full px-2.5 py-1.5 rounded-lg text-sm text-center focus:outline-none transition-all"
                           style="background: var(--bg-card); border: 1px solid var(--border-2); color: var(--text-1)">
                </div>
            `;
        });

        return `
            <div class="flex items-center gap-2 px-3 py-2.5 border-b"
                 style="background: var(--bg-card); border-color: var(--border-2);">
                <iconify-icon icon="mdi:ruler" class="text-[#ecbc42]"></iconify-icon>
                <input type="text"
                       name="size_guides[${index}][size]"
                       value="${size}"
                       placeholder="S"
                       required
                       class="flex-1 px-3 py-1.5 rounded-lg text-sm font-bold text-center focus:outline-none transition-all"
                       style="background: var(--bg-input); border: 1px solid var(--border-2); color: var(--text-1)">
                ${idHtml}
                <button type="button"
                        class="remove-size-guide inline-flex items-center justify-center w-7 h-7 rounded-lg transition-all"
                        style="color: var(--text-5)"
                        onmouseover="this.style.background='rgba(239,68,68,0.1)'; this.style.color='#f87171'"
                        onmouseout="this.style.background='transparent'; this.style.color='var(--text-5)'"
                        title="Hapus ukuran">
                    <iconify-icon icon="mdi:trash-can-outline" class="text-sm"></iconify-icon>
                </button>
            </div>

            <div class="p-3 grid grid-cols-2 gap-2">
                ${dimensionsHtml}
            </div>
        `;
    }

    // ============================================
    // UPDATE ROW COUNTER & EMPTY STATE
    // ============================================
    function updateRowState() {
        const cards = container.querySelectorAll('.size-guide-card');
        if (rowCountEl) rowCountEl.textContent = cards.length;

        if (emptyState) {
            if (cards.length === 0) {
                emptyState.classList.remove('hidden');
                container.classList.add('hidden');
            } else {
                emptyState.classList.add('hidden');
                container.classList.remove('hidden');
            }
        }
    }

    // ============================================
    // TAMBAH UKURAN
    // ============================================
    if (addSizeBtn) {
        addSizeBtn.addEventListener('click', function() {
            const cards = container.querySelectorAll('.size-guide-card');
            const index = cards.length;

            const card = document.createElement('div');
            card.className = 'size-guide-card rounded-xl border overflow-hidden transition-colors';
            card.style.cssText = 'background: var(--bg-input); border-color: var(--border-2);';
            card.innerHTML = buildCardHtml(index, '', {}, null);
            container.appendChild(card);

            updateRowState();

            const sizeInput = card.querySelector('input[name*="[size]"]');
            if (sizeInput) sizeInput.focus();
        });
    }

    // ============================================
    // TAMBAH DIMENSI
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

                // Update semua card dengan label baru
                updateAllCards();
            }
        });
    }

    // ============================================
    // HAPUS DIMENSI
    // ============================================
    if (labelsContainer) {
        labelsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-dimension')) {
                const item = e.target.closest('.dimension-label-item');
                const label = item.querySelector('span').textContent;

                if (confirm(`Hapus dimensi "${label}"? Semua nilai "${label}" di tabel akan hilang.`)) {
                    item.remove();
                    updateAllCards();
                }
            }
        });
    }

    // ============================================
    // HAPUS UKURAN
    // ============================================
    if (container) {
        container.addEventListener('click', function(e) {
            if (e.target.closest('.remove-size-guide')) {
                const card = e.target.closest('.size-guide-card');
                const cards = container.querySelectorAll('.size-guide-card');

                if (cards.length > 1) {
                    card.remove();
                    reindexCards();
                    updateRowState();
                } else {
                    // Jika tinggal 1, kosongkan saja isinya
                    card.querySelectorAll('input[type="text"], input[type="number"]').forEach(function(input) {
                        input.value = '';
                    });
                }
            }
        });
    }

    // ============================================
    // UPDATE ALL CARDS (setelah tambah/hapus dimensi)
    // ============================================
    function updateAllCards() {
        const cards = container.querySelectorAll('.size-guide-card');
        const labels = getDimensionLabels();

        cards.forEach(function(card, index) {
            // Ambil nilai size
            const sizeInput = card.querySelector('input[name*="[size]"]');
            const size = sizeInput ? sizeInput.value : '';

            // Ambil ID
            const idInput = card.querySelector('input[name*="[id]"]');
            const id = idInput ? idInput.value : null;

            // Ambil nilai dimensions existing
            const existingDimensions = {};
            const oldLabels = getDimensionLabels(); // pakai labels baru
            card.querySelectorAll('input[name*="[dimensions]"]').forEach(function(input) {
                const match = input.name.match(/dimensions\[(\d+)\]/);
                if (match) {
                    const labelIndex = parseInt(match[1]);
                    if (oldLabels[labelIndex]) {
                        existingDimensions[oldLabels[labelIndex]] = input.value;
                    }
                }
            });

            // Rebuild card content
            card.innerHTML = buildCardHtml(index, size, existingDimensions, id);
        });
    }

    // ============================================
    // REINDEX CARDS (setelah hapus baris)
    // ============================================
    function reindexCards() {
        const cards = container.querySelectorAll('.size-guide-card');
        cards.forEach(function(card, index) {
            card.querySelectorAll('input').forEach(function(input) {
                const newName = input.name.replace(/size_guides\[\d+\]/, `size_guides[${index}]`);
                input.name = newName;
            });
        });
    }

    // ============================================
    // INIT
    // ============================================
    updateRowState();

    console.log('✅ Size guide grid initialized');
});
</script>