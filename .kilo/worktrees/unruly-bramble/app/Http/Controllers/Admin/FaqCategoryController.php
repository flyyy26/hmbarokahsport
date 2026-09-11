<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaqCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class FaqCategoryController extends Controller
{
    public function index()
    {
        $categories = FaqCategory::ordered()->get();
        return view('admin.faqs.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:faq_categories,name'],
        ]);

        try {
            DB::beginTransaction();

            $lastOrder = FaqCategory::max('order') ?? 0;

            FaqCategory::create([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'order' => $lastOrder + 1,
                'is_active' => true,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.faqs.categories')
                ->with('success', 'Kategori FAQ berhasil ditambahkan.');

        } catch (Throwable $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan kategori: ' . $e->getMessage());
        }
    }

    public function destroy(FaqCategory $category)
    {
        try {
            // Cek apakah ada FAQ yang menggunakan kategori ini
            if ($category->faqs()->count() > 0) {
                return back()->with('error', 'Kategori ini masih digunakan oleh ' . $category->faqs()->count() . ' FAQ. Pindahkan atau hapus FAQ tersebut terlebih dahulu.');
            }

            $category->delete();

            return redirect()
                ->route('admin.faqs.categories')
                ->with('success', 'Kategori FAQ berhasil dihapus.');

        } catch (Throwable $e) {
            return back()->with('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }
}