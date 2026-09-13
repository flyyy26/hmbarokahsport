{{-- ============================================ --}}
{{-- FORM PARTIAL: dipakai bareng create & edit --}}
{{-- Variabel: $career (nullable) --}}
{{-- ============================================ --}}

{{-- INFO DASAR --}}
<div class="rounded-2xl border overflow-hidden"
     style="background: var(--bg-card); border-color: var(--border-2)">

    <div class="px-5 py-4 border-b flex items-center gap-2"
         style="background: var(--bg-input); border-color: var(--border-2)">
        <iconify-icon icon="mdi:information-outline" class="text-[#ecbc42] text-base"></iconify-icon>
        <h2 class="font-bold text-sm flex-1" style="color: var(--text-1)">Informasi Dasar</h2>
        <span class="text-[10px] font-mono px-2 py-0.5 rounded-full"
              style="background: rgba(236,188,66,0.1); color: #ecbc42;">
            Wajib
        </span>
    </div>

    <div class="p-5 space-y-5">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Judul --}}
            <div class="md:col-span-2">
                <label class="form-label">
                    <iconify-icon icon="mdi:briefcase-outline" class="text-[#ecbc42]"></iconify-icon>
                    Judul Lowongan <span class="text-red-400">*</span>
                </label>
                <input type="text" name="title"
                       value="{{ old('title', $career->title ?? '') }}"
                       required
                       class="form-input @error('title') has-error @enderror"
                       placeholder="cth: Staff Marketing">
                @error('title')
                    <p class="form-error">
                        <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Departemen --}}
            <div>
                <label class="form-label">
                    <iconify-icon icon="mdi:domain" class="text-[#ecbc42]"></iconify-icon>
                    Departemen
                </label>
                <input type="text" name="department"
                       value="{{ old('department', $career->department ?? '') }}"
                       class="form-input"
                       placeholder="cth: Marketing, Operasional">
            </div>

            {{-- Lokasi --}}
            <div>
                <label class="form-label">
                    <iconify-icon icon="mdi:map-marker-outline" class="text-[#ecbc42]"></iconify-icon>
                    Lokasi
                </label>
                <input type="text" name="location"
                       value="{{ old('location', $career->location ?? '') }}"
                       class="form-input"
                       placeholder="cth: Bandung, Remote">
            </div>

            {{-- Tipe --}}
            <div>
                <label class="form-label">
                    <iconify-icon icon="mdi:clock-outline" class="text-[#ecbc42]"></iconify-icon>
                    Tipe <span class="text-red-400">*</span>
                </label>
                <select name="type" required class="form-input">
                    @foreach([
                        'full_time'  => 'Full Time',
                        'part_time'  => 'Part Time',
                        'contract'   => 'Kontrak',
                        'internship' => 'Magang',
                        'freelance'  => 'Freelance',
                    ] as $k => $v)
                        <option value="{{ $k }}"
                            {{ old('type', $career->type ?? 'full_time') === $k ? 'selected' : '' }}>
                            {{ $v }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Level --}}
            <div>
                <label class="form-label">
                    <iconify-icon icon="mdi:chart-line" class="text-[#ecbc42]"></iconify-icon>
                    Level <span class="text-red-400">*</span>
                </label>
                <select name="level" required class="form-input">
                    @foreach([
                        'staff'      => 'Staff',
                        'senior'     => 'Senior',
                        'supervisor' => 'Supervisor',
                        'manager'    => 'Manager',
                        'director'   => 'Director',
                    ] as $k => $v)
                        <option value="{{ $k }}"
                            {{ old('level', $career->level ?? 'staff') === $k ? 'selected' : '' }}>
                            {{ $v }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Deadline --}}
            <div>
                <label class="form-label">
                    <iconify-icon icon="mdi:calendar-clock-outline" class="text-[#ecbc42]"></iconify-icon>
                    Deadline
                </label>
                <input type="date" name="deadline"
                       value="{{ old('deadline', isset($career) && $career->deadline ? $career->deadline->format('Y-m-d') : '') }}"
                       class="form-input">
            </div>

            {{-- Kuota --}}
            <div>
                <label class="form-label">
                    <iconify-icon icon="mdi:account-group-outline" class="text-[#ecbc42]"></iconify-icon>
                    Kuota
                </label>
                <input type="number" name="quota" min="1"
                       value="{{ old('quota', $career->quota ?? 1) }}"
                       class="form-input">
            </div>
        </div>

        {{-- Short Description --}}
        <div>
            <label class="form-label">
                <iconify-icon icon="mdi:text-short" class="text-[#ecbc42]"></iconify-icon>
                Deskripsi Singkat
            </label>
            <textarea name="short_description" rows="3" maxlength="500"
                      class="form-input"
                      placeholder="Ringkasan singkat tentang posisi ini (maks 500 karakter)...">{{ old('short_description', $career->short_description ?? '') }}</textarea>
        </div>
    </div>
</div>

{{-- DETAIL --}}
<div class="rounded-2xl border overflow-hidden"
     style="background: var(--bg-card); border-color: var(--border-2)">
    <div class="px-5 py-4 border-b flex items-center gap-2"
         style="background: var(--bg-input); border-color: var(--border-2)">
        <iconify-icon icon="mdi:text-box-outline" class="text-[#ecbc42] text-base"></iconify-icon>
        <h2 class="font-bold text-sm flex-1" style="color: var(--text-1)">Detail Lowongan</h2>
    </div>

    <div class="p-5 space-y-5">
        <div>
            <label class="form-label">
                <iconify-icon icon="mdi:file-document-outline" class="text-[#ecbc42]"></iconify-icon>
                Deskripsi Pekerjaan
            </label>
            <div id="quill-editor-description" style="min-height: 180px;"></div>
            <input type="hidden" name="description" id="description"
                   value="{{ old('description', $career->description ?? '') }}">
        </div>

        <div>
            <label class="form-label">
                <iconify-icon icon="mdi:clipboard-check-outline" class="text-[#ecbc42]"></iconify-icon>
                Persyaratan
            </label>
            <div id="quill-editor-requirements" style="min-height: 150px;"></div>
            <input type="hidden" name="requirements" id="requirements"
                   value="{{ old('requirements', $career->requirements ?? '') }}">
        </div>

        <div>
            <label class="form-label">
                <iconify-icon icon="mdi:gift-outline" class="text-[#ecbc42]"></iconify-icon>
                Benefit
            </label>
            <div id="quill-editor-benefits" style="min-height: 150px;"></div>
            <input type="hidden" name="benefits" id="benefits"
                   value="{{ old('benefits', $career->benefits ?? '') }}">
        </div>
    </div>
</div>

{{-- GAJI & PENGATURAN --}}
<div class="rounded-2xl border overflow-hidden"
     style="background: var(--bg-card); border-color: var(--border-2)">
    <div class="px-5 py-4 border-b flex items-center gap-2"
         style="background: var(--bg-input); border-color: var(--border-2)">
        <iconify-icon icon="mdi:cash-multiple" class="text-[#ecbc42] text-base"></iconify-icon>
        <h2 class="font-bold text-sm flex-1" style="color: var(--text-1)">Gaji & Pengaturan</h2>
    </div>

    <div class="p-5 space-y-5">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="form-label">
                    <iconify-icon icon="mdi:cash" class="text-[#ecbc42]"></iconify-icon>
                    Gaji Minimum
                </label>
                <input type="number" name="salary_min" min="0" step="1000"
                       value="{{ old('salary_min', $career->salary_min ?? '') }}"
                       class="form-input"
                       placeholder="0">
            </div>

            <div>
                <label class="form-label">
                    <iconify-icon icon="mdi:cash-plus" class="text-[#ecbc42]"></iconify-icon>
                    Gaji Maksimum
                </label>
                <input type="number" name="salary_max" min="0" step="1000"
                       value="{{ old('salary_max', $career->salary_max ?? '') }}"
                       class="form-input"
                       placeholder="0">
                @error('salary_max')
                    <p class="form-error">
                        <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        {{-- Toggles --}}
        <div class="space-y-3 pt-2 border-t" style="border-color: var(--border-1);">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="show_salary" value="1"
                       {{ old('show_salary', $career->show_salary ?? false) ? 'checked' : '' }}>
                <span class="text-sm" style="color: var(--text-3)">
                    Tampilkan gaji ke pelamar
                </span>
            </label>

            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="is_active" value="1"
                       {{ old('is_active', $career->is_active ?? true) ? 'checked' : '' }}>
                <span class="text-sm" style="color: var(--text-3)">
                    Aktifkan lowongan (bisa dilihat pelamar)
                </span>
            </label>

            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="is_featured" value="1"
                       {{ old('is_featured', $career->is_featured ?? false) ? 'checked' : '' }}>
                <span class="text-sm" style="color: var(--text-3)">
                    Tandai sebagai <strong>Featured</strong>
                </span>
            </label>
        </div>
    </div>
</div>