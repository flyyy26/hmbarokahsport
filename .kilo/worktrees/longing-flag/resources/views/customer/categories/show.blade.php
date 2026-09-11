@extends('layouts.customer')

@section('title', 'Barokah Sport')

@section('content')

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900">{{ $category->name }}</h1>
            <p class="mt-1 text-sm text-slate-500">{{ $products->total() }} produk dalam kategori ini</p>
        </div>

        <div class="mb-4 flex items-center justify-end">
            <div class="flex items-center gap-2">
                <span class="text-sm text-slate-500">Urutkan:</span>
                <select onchange="window.location.href=this.value" class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm outline-none focus:border-blue-500">
                    <option value="{{ route('customer.categories.show', array_merge([$category], request()->query(), ['sort' => 'newest'])) }}" {{ request('sort') == 'newest' ? 'selected' : '' }}>
                        Terbaru
                    </option>
                    <option value="{{ route('customer.categories.show', array_merge([$category], request()->query(), ['sort' => 'price_asc'])) }}" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>
                        Harga: Rendah → Tinggi
                    </option>
                    <option value="{{ route('customer.categories.show', array_merge([$category], request()->query(), ['sort' => 'price_desc'])) }}" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>
                        Harga: Tinggi → Rendah
                    </option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            @forelse ($products as $product)
                <div class="group rounded-2xl border border-slate-200 bg-white p-3 transition hover:shadow-lg">
                    <a href="{{ route('customer.products.show', $product) }}" class="block">
                        <div class="aspect-square overflow-hidden rounded-xl bg-slate-100">
                            @if ($product->images->first())
                                <img src="{{ Storage::url($product->images->first()->image) }}" 
                                     alt="{{ $product->name }}" 
                                     class="h-full w-full object-cover transition group-hover:scale-105">
                            @else
                                <div class="flex h-full items-center justify-center text-4xl text-slate-300">📦</div>
                            @endif
                        </div>
                        <div class="mt-3">
                            <h3 class="text-sm font-semibold text-slate-900 line-clamp-1">{{ $product->name }}</h3>
                            <div class="mt-1 flex items-center justify-between">
                                <span class="font-bold text-slate-900">{{ $product->price_formatted }}</span>
                                @if ($product->stock > 0)
                                    <span class="text-xs text-emerald-600">Tersedia</span>
                                @else
                                    <span class="text-xs text-red-500">Habis</span>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <p class="col-span-full py-12 text-center text-slate-500">Belum ada produk dalam kategori ini.</p>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $products->links() }}
        </div>

    </div>

@endsection