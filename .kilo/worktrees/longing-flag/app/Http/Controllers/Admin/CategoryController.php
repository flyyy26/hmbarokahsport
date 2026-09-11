<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SizeGuide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
            // 🔥 VALIDASI SIZE GUIDE FLEKSIBEL
            'size_guides' => ['nullable', 'array'],
            'size_guides.*.size' => ['required_with:size_guides', 'string', 'max:10'],
            'size_guides.*.dimensions' => ['nullable', 'array'],
            'size_guides.*.dimensions.*' => ['nullable', 'integer', 'min:0'],
            'dimension_labels' => ['nullable', 'array'],
            'dimension_labels.*' => ['required', 'string', 'max:50'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $category = Category::create($validated);

        // 🔥 SIMPAN SIZE GUIDE
        if (!empty($validated['size_guides'])) {
            $dimensionLabels = $request->dimension_labels ?? [];
            
            foreach ($validated['size_guides'] as $index => $guide) {
                if (!empty($guide['size'])) {
                    $dimensions = [];
                    if (!empty($guide['dimensions']) && !empty($dimensionLabels)) {
                        foreach ($dimensionLabels as $labelIndex => $label) {
                            if (isset($guide['dimensions'][$labelIndex])) {
                                $dimensions[$label] = (int) $guide['dimensions'][$labelIndex];
                            }
                        }
                    }
                    
                    $category->sizeGuides()->create([
                        'size' => $guide['size'],
                        'dimensions' => !empty($dimensions) ? $dimensions : null,
                        'sort_order' => $index,
                        'is_active' => true,
                    ]);
                }
            }
        }

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori dan panduan ukuran berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        $category->load('sizeGuides');
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,' . $category->id],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
            'size_guides' => ['nullable', 'array'],
            'size_guides.*.id' => ['nullable', 'integer', 'exists:size_guides,id'],
            'size_guides.*.size' => ['required_with:size_guides', 'string', 'max:10'],
            'size_guides.*.dimensions' => ['nullable', 'array'],
            'size_guides.*.dimensions.*' => ['nullable', 'integer', 'min:0'],
            'dimension_labels' => ['nullable', 'array'],
            'dimension_labels.*' => ['required', 'string', 'max:50'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($validated);

        // 🔥 PERBAIKI: UPDATE SIZE GUIDE - HAPUS SEMUA DULU, BARU INSERT ULANG
        if ($request->has('size_guides') && !empty($request->size_guides)) {
            
            // 🔥 1. DAPATKAN LIST SIZE YANG ADA DI REQUEST
            $newSizes = [];
            foreach ($request->size_guides as $guide) {
                if (!empty($guide['size'])) {
                    $newSizes[] = trim($guide['size']);
                }
            }

            // 🔥 2. HAPUS SIZE GUIDE YANG TIDAK ADA DI REQUEST
            $category->sizeGuides()
                ->whereNotIn('size', $newSizes)
                ->delete();

            // 🔥 3. UPDATE ATAU CREATE SIZE GUIDE
            $dimensionLabels = $request->dimension_labels ?? [];
            
            foreach ($request->size_guides as $index => $guide) {
                if (empty($guide['size'])) {
                    continue;
                }

                $size = trim($guide['size']);
                
                // Build dimensions array
                $dimensions = [];
                if (!empty($guide['dimensions']) && !empty($dimensionLabels)) {
                    foreach ($dimensionLabels as $labelIndex => $label) {
                        if (isset($guide['dimensions'][$labelIndex])) {
                            $dimensions[$label] = (int) $guide['dimensions'][$labelIndex];
                        }
                    }
                }

                // 🔥 4. UPDATE ATAU CREATE DENGAN AMAN
                $sizeGuide = $category->sizeGuides()->where('size', $size)->first();
                
                if ($sizeGuide) {
                    // Update existing
                    $sizeGuide->update([
                        'dimensions' => !empty($dimensions) ? $dimensions : null,
                        'sort_order' => $index,
                        'is_active' => true,
                    ]);
                } else {
                    // Create new
                    $category->sizeGuides()->create([
                        'size' => $size,
                        'dimensions' => !empty($dimensions) ? $dimensions : null,
                        'sort_order' => $index,
                        'is_active' => true,
                    ]);
                }
            }
        } else {
            // 🔥 JIKA TIDAK ADA SIZE GUIDE, HAPUS SEMUA
            $category->sizeGuides()->delete();
        }

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori dan panduan ukuran berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        // Hapus size guides terkait
        $category->sizeGuides()->delete();

        if ($category->image && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori dan panduan ukuran berhasil dihapus.');
    }
}