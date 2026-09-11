<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CustomerCategoryController extends Controller
{
    public function show(Category $category, Request $request)
    {
        if (!$category->is_active) {
            abort(404);
        }

        // Query produk dengan filter stok dari variants
        $query = Product::with(['images', 'variants'])
            ->where('category_id', $category->id)
            ->where('is_active', true)
            ->whereHas('variants', function($q) {
                $q->where('stock', '>', 0);
            });

        // Sort
        $sort = $request->sort ?? 'newest';
        switch ($sort) {
            case 'price_asc':
                $query->orderBy(
                    Product::select('price')
                        ->from('product_variants')
                        ->whereColumn('product_variants.product_id', 'products.id')
                        ->orderBy('price')
                        ->limit(1),
                    'asc'
                );
                break;
            case 'price_desc':
                $query->orderBy(
                    Product::select('price')
                        ->from('product_variants')
                        ->whereColumn('product_variants.product_id', 'products.id')
                        ->orderBy('price', 'desc')
                        ->limit(1),
                    'desc'
                );
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(12)->withQueryString();

        return view('customer.categories.show', compact('category', 'products'));
    }
}