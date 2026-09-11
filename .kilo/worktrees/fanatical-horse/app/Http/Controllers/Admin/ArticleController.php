<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::with('articleCategory')->latest()->paginate(10);
        return view('admin.articles.index', compact('articles'));
    }

    public function create()
    {
        $categories = ArticleCategory::active()->sorted()->get();
        return view('admin.articles.create', compact('categories'));
    }

    public function store(Request $request)
    {
        \Log::info('Article store request:', $request->all());

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'article_category_id' => 'required|exists:article_categories,id',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'author' => 'nullable|string|max:100',
            'tags' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ]);

        // 🔥 GENERATE SLUG UNIK
        $slug = Str::slug($validated['title']);
        $originalSlug = $slug;
        $counter = 1;
        
        while (Article::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        $validated['slug'] = $slug;
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_featured'] = $request->boolean('is_featured', false);

        if ($request->filled('tags')) {
            $validated['tags'] = array_map('trim', explode(',', $request->tags));
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('articles', 'public');
        }

        if ($request->filled('published_at')) {
            $validated['published_at'] = $request->published_at;
        }

        $article = Article::create($validated);

        \Log::info('Article created:', ['id' => $article->id]);

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Artikel berhasil ditambahkan.');
    }

    public function edit(Article $article)
    {
        $categories = ArticleCategory::active()->sorted()->get();
        return view('admin.articles.edit', compact('article', 'categories'));
    }

    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'article_category_id' => 'required|exists:article_categories,id',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'author' => 'nullable|string|max:100',
            'tags' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ]);

        // 🔥 GENERATE SLUG UNIK
        $slug = Str::slug($validated['title']);
        if ($slug !== $article->slug) {
            $originalSlug = $slug;
            $counter = 1;
            
            while (Article::where('slug', $slug)->where('id', '!=', $article->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            $validated['slug'] = $slug;
        } else {
            $validated['slug'] = $article->slug;
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_featured'] = $request->boolean('is_featured', false);

        if ($request->filled('tags')) {
            $validated['tags'] = array_map('trim', explode(',', $request->tags));
        }

        // 🔥 PERBAIKAN: HANYA UPLOAD GAMBAR JIKA ADA FILE BARU
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($article->image && Storage::disk('public')->exists($article->image)) {
                Storage::disk('public')->delete($article->image);
            }
            // Upload gambar baru
            $validated['image'] = $request->file('image')->store('articles', 'public');
        } else {
            // 🔥 JANGAN HAPUS GAMBAR, PERTAHANKAN YANG LAMA
            // Hanya set image jika ada di database
            if ($article->image) {
                $validated['image'] = $article->image;
            }
            // Jika tidak ada gambar, biarkan null
        }

        if ($request->filled('published_at')) {
            $validated['published_at'] = $request->published_at;
        }

        // 🔥 HAPUS FIELD YANG TIDAK PERLU DIUPDATE
        // Pastikan tidak ada field tambahan yang masuk

        $article->update($validated);

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Article $article)
    {
        if ($article->image && Storage::disk('public')->exists($article->image)) {
            Storage::disk('public')->delete($article->image);
        }

        $article->delete();

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Artikel berhasil dihapus.');
    }
}