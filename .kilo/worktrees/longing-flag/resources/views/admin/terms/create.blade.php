@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Tambah Syarat & Ketentuan</h1>
        <p class="mt-1 text-sm text-gray-500">Buat syarat dan ketentuan baru untuk toko.</p>
    </div>

    <form action="{{ route('admin.terms.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            {{-- JUDUL --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Judul</label>
                <input type="text" name="title" value="{{ old('title', 'Syarat & Ketentuan') }}"
                       class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       required>
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- KONTEN --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Konten</label>
                <textarea name="content" rows="12" 
                          class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm"
                          placeholder="Tuliskan syarat dan ketentuan..." required>{{ old('content') }}</textarea>
                <p class="mt-2 text-xs text-gray-500">
                    <span class="font-medium">Tips:</span> Gunakan format HTML jika perlu (untuk bold, list, dll). Contoh: 
                    &lt;b&gt;teks bold&lt;/b&gt;, &lt;ul&gt;&lt;li&gt;list item&lt;/li&gt;&lt;/ul&gt;
                </p>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- VERSI --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Versi</label>
                <input type="text" name="version" value="{{ old('version', '1.0') }}"
                       class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       placeholder="Contoh: 1.0, 2.1, 1.0.1">
                @error('version')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- TANGGAL EFEKTIF --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Tanggal Efektif</label>
                <input type="date" name="effective_date" value="{{ old('effective_date', date('Y-m-d')) }}"
                       class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('effective_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- STATUS --}}
            <div class="flex items-center">
                <input type="checkbox" name="is_active" value="1" checked
                       class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label class="ml-2 block text-sm text-gray-700">Aktif</label>
            </div>
        </div>

        {{-- ACTION --}}
        <div class="flex justify-end gap-3 rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <a href="{{ route('admin.terms.index') }}" 
               class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Batal
            </a>
            <button type="submit" 
                    class="inline-flex items-center rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection