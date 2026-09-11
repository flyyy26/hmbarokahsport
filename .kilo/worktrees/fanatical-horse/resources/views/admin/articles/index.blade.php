@extends('layouts.admin')

@section('title', 'Artikel')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold">📝 Artikel</h2>
            <p class="text-gray-500 mt-1">Kelola artikel blog toko.</p>
        </div>
        <a href="{{ route('admin.articles.create') }}" 
           class="inline-flex items-center justify-center px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg">
            + Tambah Artikel
        </a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-6 py-4">#</th>
                        <th class="text-left px-6 py-4">Judul</th>
                        <th class="text-left px-6 py-4">Kategori</th>
                        <th class="text-left px-6 py-4">Penulis</th>
                        <th class="text-left px-6 py-4">Status</th>
                        <th class="text-right px-6 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($articles as $article)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">
                                <div class="font-medium">{{ $article->title }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $article->created_at->format('d/m/Y H:i') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                    {{ $article->category ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $article->author }}</td>
                            <td class="px-6 py-4">
                                @if($article->is_active)
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        Nonaktif
                                    </span>
                                @endif
                                @if($article->is_featured)
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 ml-1">
                                        ★ Featured
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.articles.edit', $article) }}" 
                                       class="px-3 py-2 text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.articles.destroy', $article) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Hapus artikel ini?')" 
                                                class="px-3 py-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                Belum ada artikel.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($articles->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $articles->links() }}
            </div>
        @endif
    </div>

</div>

@endsection