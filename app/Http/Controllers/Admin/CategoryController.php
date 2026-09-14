<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Customer\CustomerHomeController;
use App\Models\Category;
use App\Models\SizeGuide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\ImageOptimizer;

class CategoryController extends Controller
{
    protected ImageOptimizer $imageOptimizer;

    public function __construct(ImageOptimizer $imageOptimizer)
    {
        $this->imageOptimizer = $imageOptimizer;
    }
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
            'size_guides' => ['nullable', 'array'],
            'size_guides.*.size' => ['required_with:size_guides', 'string', 'max:10'],
            'size_guides.*.dimensions' => ['nullable', 'array'],
            'size_guides.*.dimensions.*' => ['nullable', 'integer', 'min:0'],
            'dimension_labels' => ['nullable', 'array'],
            'dimension_labels.*' => ['required', 'string', 'max:50'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

        // 🔥 CONVERT IMAGE KE WEBP
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $validated['image'] = $this->imageOptimizer->convertToWebp(
                file: $request->file('image'),
                folder: 'categories',
                maxWidth: 800,   // max 800px untuk kategori
                quality: 82
            );
        }

        $category = Category::create($validated);

        // 🔥 CLEAR CACHE HOMEPAGE
        CustomerHomeController::clearCache();

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

        // 🔥 CONVERT IMAGE BARU KE WEBP + HAPUS YANG LAMA
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Hapus gambar lama
            $this->imageOptimizer->delete($category->image);

            // Upload & convert gambar baru
            $validated['image'] = $this->imageOptimizer->convertToWebp(
                file: $request->file('image'),
                folder: 'categories',
                maxWidth: 800,
                quality: 82
            );
        }

        $category->update($validated);

        // 🔥 CLEAR CACHE HOMEPAGE
        CustomerHomeController::clearCache();

        // 🔥 UPDATE SIZE GUIDE
        if ($request->has('size_guides') && !empty($request->size_guides)) {
            $newSizes = [];
            foreach ($request->size_guides as $guide) {
                if (!empty($guide['size'])) {
                    $newSizes[] = trim($guide['size']);
                }
            }

            // Hapus size guide yang tidak ada di request
            $category->sizeGuides()->whereNotIn('size', $newSizes)->delete();

            $dimensionLabels = $request->dimension_labels ?? [];

            foreach ($request->size_guides as $index => $guide) {
                if (empty($guide['size'])) continue;

                $size = trim($guide['size']);
                $dimensions = [];

                if (!empty($guide['dimensions']) && !empty($dimensionLabels)) {
                    foreach ($dimensionLabels as $labelIndex => $label) {
                        if (isset($guide['dimensions'][$labelIndex])) {
                            $dimensions[$label] = (int) $guide['dimensions'][$labelIndex];
                        }
                    }
                }

                $sizeGuide = $category->sizeGuides()->where('size', $size)->first();

                if ($sizeGuide) {
                    $sizeGuide->update([
                        'dimensions' => !empty($dimensions) ? $dimensions : null,
                        'sort_order' => $index,
                        'is_active' => true,
                    ]);
                } else {
                    $category->sizeGuides()->create([
                        'size' => $size,
                        'dimensions' => !empty($dimensions) ? $dimensions : null,
                        'sort_order' => $index,
                        'is_active' => true,
                    ]);
                }
            }
        } else {
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

        // 🔥 HAPUS GAMBAR VIA HELPER
        $this->imageOptimizer->delete($category->image);

        $category->delete();

        // 🔥 CLEAR CACHE HOMEPAGE
        CustomerHomeController::clearCache();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori dan panduan ukuran berhasil dihapus.');
    }

}