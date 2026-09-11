@extends('layouts.admin')

@section('title', 'Edit Artikel')

@section('content')

<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold">Edit Artikel</h2>
        <p class="text-gray-500 mt-1">Edit artikel yang sudah ada.</p>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-6">
        {{-- 🔥 TAMBAHKAN ERROR MESSAGE --}}
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-red-700 font-medium">Terjadi kesalahan:</p>
                <ul class="mt-2 list-disc list-inside text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.articles.update', $article) }}" method="POST" enctype="multipart/form-data" id="article-form">
            @csrf
            @method('PUT')
            @include('admin.articles._form', ['isEdit' => true, 'article' => $article])

            <div class="flex justify-end gap-3 mt-8 pt-6 border-t">
                <a href="{{ route('admin.articles.index') }}" 
                   class="px-5 py-3 bg-gray-100 hover:bg-gray-200 rounded-lg">
                    Batal
                </a>
                <button type="button" id="submit-article-btn"
                        class="px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                    Update Artikel
                </button>
            </div>
        </form>
    </div>
</div>

@endsection