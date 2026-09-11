<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SizeGuide;
use Illuminate\Http\Request;

class CustomerSizeGuideController extends Controller
{
    public function index()
    {
        // Ambil semua kategori yang memiliki size guide
        $categories = Category::with(['sizeGuides' => function ($query) {
            $query->where('is_active', true)->orderBy('sort_order', 'asc');
        }])->whereHas('sizeGuides', function ($query) {
            $query->where('is_active', true);
        })->where('is_active', true)->get();

        return view('customer.size-guide', compact('categories'));
    }

    public function show(Category $category)
    {
        // Ambil kategori dengan size guide yang aktif
        $category->load(['sizeGuides' => function ($query) {
            $query->where('is_active', true)->orderBy('sort_order', 'asc');
        }]);

        if ($category->sizeGuides->isEmpty()) {
            return redirect()->route('customer.size-guide')
                ->with('error', 'Kategori ini belum memiliki panduan ukuran.');
        }

        return view('customer.size-guide-detail', compact('category'));
    }

    public function getSizeGuideData(Category $category)
    {
        $sizeGuides = $category->sizeGuides()
            ->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();

        $dimensionLabels = $category->dimension_labels ?? [];

        return response()->json([
            'success' => true,
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
            ],
            'dimension_labels' => $dimensionLabels,
            'size_guides' => $sizeGuides->map(function ($guide) use ($dimensionLabels) {
                $dimensions = [];
                foreach ($dimensionLabels as $label) {
                    $dimensions[$label] = $guide->dimensions[$label] ?? null;
                }
                return [
                    'id' => $guide->id,
                    'size' => $guide->size,
                    'dimensions' => $dimensions,
                ];
            }),
        ]);
    }
}