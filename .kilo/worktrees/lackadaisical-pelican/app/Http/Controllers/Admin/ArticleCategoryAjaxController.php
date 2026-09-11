<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleCategoryAjaxController extends Controller
{
    /**
     * Get all categories (for select)
     */
    public function index()
    {
        $categories = ArticleCategory::active()->sorted()->get();
        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }

    /**
     * Store new category via AJAX
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:article_categories,name',
        ]);

        $category = ArticleCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'is_active' => true,
            'sort_order' => ArticleCategory::max('sort_order') + 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan.',
            'category' => $category
        ]);
    }

    /**
     * Delete category via AJAX
     */
    public function destroy($id)
    {
        $category = ArticleCategory::findOrFail($id);

        // Cek apakah ada artikel yang menggunakan kategori ini
        if ($category->articles()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak dapat dihapus karena masih digunakan oleh artikel.'
            ], 400);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus.'
        ]);
    }
}