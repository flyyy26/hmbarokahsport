@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Kategori FAQ</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola kategori untuk pertanyaan yang sering diajukan.</p>
        </div>
        <a href="{{ route('admin.faqs.index') }}" 
           class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
            Kembali ke FAQ
        </a>
    </div>

    {{-- SUCCESS & ERROR --}}
    @if (session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- FORM TAMBAH KATEGORI --}}
        <div class="md:col-span-1">
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Tambah Kategori</h2>
                <form action="{{ route('admin.faqs.categories.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Nama Kategori</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="Contoh: Promo" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" 
                            class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        Tambah Kategori
                    </button>
                </form>
            </div>
        </div>

        {{-- LIST KATEGORI --}}
        <div class="md:col-span-2">
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Daftar Kategori</h2>
                
                @if ($categories->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kategori</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah FAQ</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Urutan</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach ($categories as $category)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                            {{ $category->name }}
                                            @if(!$category->is_active)
                                                <span class="ml-2 inline-flex rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-800">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $category->slug }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            {{ $category->faqs()->count() ?? 0 }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $category->order }}</td>
                                        <td class="px-4 py-3 text-right text-sm font-medium">
                                            <form action="{{ route('admin.faqs.categories.destroy', $category) }}" 
                                                  method="POST" 
                                                  class="inline" 
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori {{ $category->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-500">Belum ada kategori.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection