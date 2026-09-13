@extends('layouts.admin')

@section('title', 'Edit Lowongan')
@section('page-title', 'Edit Lowongan')

@section('content')

<div class="w-full max-w-4xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <a href="{{ route('admin.careers.index') }}"
               class="inline-flex items-center gap-1.5 text-xs font-semibold mb-2 transition-colors"
               style="color: var(--text-5);"
               onmouseover="this.style.color='#ecbc42'"
               onmouseout="this.style.color='var(--text-5)'">
                <iconify-icon icon="mdi:arrow-left"></iconify-icon>
                Kembali ke Daftar Karir
            </a>
            <h1 class="text-2xl font-bold flex items-center gap-2.5" style="color: var(--text-1)">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl
                             bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]
                             shadow-lg shadow-amber-500/20 flex-shrink-0">
                    <iconify-icon icon="mdi:pencil-outline" class="text-slate-900 text-2xl"></iconify-icon>
                </span>
                Edit Lowongan
            </h1>
            <p class="text-sm mt-1.5 ml-12 line-clamp-1" style="color: var(--text-5)">
                {{ $career->title }}
            </p>
        </div>

        {{-- Quick Actions --}}
        <div class="flex items-center gap-2 flex-shrink-0">
            <a href="{{ route('admin.careers.applications', $career) }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-bold border transition-all"
               style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3);"
               onmouseover="this.style.borderColor='#60a5fa'; this.style.color='#60a5fa'"
               onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'">
                <iconify-icon icon="mdi:account-multiple-outline"></iconify-icon>
                Lihat Pelamar ({{ $career->applications_count }})
            </a>
        </div>
    </div>

    {{-- ERROR --}}
    @if($errors->any())
        <div class="rounded-xl px-4 py-3 bg-red-500/10 border border-red-500/30 text-red-400">
            <div class="flex items-start gap-3">
                <iconify-icon icon="mdi:alert-circle-outline" class="text-xl flex-shrink-0 mt-0.5"></iconify-icon>
                <div class="text-sm">
                    <p class="font-bold mb-1">Periksa kembali input Anda:</p>
                    <ul class="list-disc ml-4 space-y-0.5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- FORM --}}
    <form action="{{ route('admin.careers.update', $career) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        {{-- 🔥 INCLUDE PARTIAL FORM --}}
        @include('admin.careers._form', ['career' => $career])

        {{-- SUBMIT --}}
        <div class="flex flex-col sm:flex-row justify-end gap-2">
            <a href="{{ route('admin.careers.index') }}"
               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg
                      text-sm font-bold border transition-all"
               style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3)"
               onmouseover="this.style.borderColor='var(--text-4)'"
               onmouseout="this.style.borderColor='var(--border-2)'">
                Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg
                           text-sm font-bold transition-all active:scale-95
                           bg-gradient-to-r from-[#FDDD57] to-[#ecbc42] text-slate-900
                           shadow-lg shadow-amber-500/20 hover:shadow-xl hover:shadow-amber-500/40">
                <iconify-icon icon="mdi:content-save-outline" class="text-base"></iconify-icon>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

{{-- STYLES (sama seperti create) --}}
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
    .form-input::placeholder { color: var(--text-6); }
    .form-input.has-error { border-color: #fca5a5; }

    .form-label {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-3);
        margin-bottom: 0.5rem;
    }

    .form-error {
        display: flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.7rem;
        color: #f87171;
        margin-top: 0.3rem;
    }
</style>

{{-- QUILL INIT --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Quill === 'undefined') return;

    const toolbarOptions = [
        [{ 'header': [1, 2, 3, false] }],
        ['bold', 'italic', 'underline'],
        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
        ['link'],
        ['clean']
    ];

    const editors = [
        { container: 'quill-editor-description',   input: 'description' },
        { container: 'quill-editor-requirements',  input: 'requirements' },
        { container: 'quill-editor-benefits',      input: 'benefits' },
    ];

    editors.forEach(({ container, input }) => {
        const el = document.getElementById(container);
        const hidden = document.getElementById(input);
        if (!el || !hidden) return;

        const q = new Quill(el, {
            theme: 'snow',
            placeholder: 'Tulis di sini...',
            modules: { toolbar: toolbarOptions }
        });

        // 🔥 EDIT MODE: load existing content
        if (hidden.value) q.root.innerHTML = hidden.value;

        // Sync ke hidden input
        q.on('text-change', () => { hidden.value = q.root.innerHTML; });
    });
});
</script>
@endpush

@endsection