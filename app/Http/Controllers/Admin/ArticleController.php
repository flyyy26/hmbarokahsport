<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Customer\CustomerHomeController;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleComment;
use App\Models\ArticleLike;
use App\Services\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    protected ImageOptimizer $imageOptimizer;

    public function __construct(ImageOptimizer $imageOptimizer)
    {
        $this->imageOptimizer = $imageOptimizer;
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $articles = Article::with('articleCategory')
            ->withCount([
                'likes',
                'allComments as comments_count' => function ($q) {
                    $q->where('is_active', true);
                }
            ])
            ->latest()
            ->paginate(10);

        return view('admin.articles.index', compact('articles'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $categories = ArticleCategory::active()->sorted()->get();
        return view('admin.articles.create', compact('categories'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        \Log::info('=== STORE ARTICLE ===');
        \Log::info('Request:', $request->except(['image', 'content']));

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

        // 🔥 TAGS: string → array
        if ($request->filled('tags')) {
            $validated['tags'] = array_map('trim', explode(',', $request->tags));
        }

        // 🔥 CONVERT IMAGE KE WEBP
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $validated['image'] = $this->imageOptimizer->convertToWebP(
                file: $request->file('image'),
                folder: 'articles',
                maxWidth: 1200,
                quality: 82
            );
            \Log::info('Article image uploaded: ' . $validated['image']);
        }

        if ($request->filled('published_at')) {
            $validated['published_at'] = $request->published_at;
        }

        $article = Article::create($validated);

        \Log::info('Article created:', ['id' => $article->id, 'slug' => $article->slug]);

        // 🔥 CLEAR CACHE HOMEPAGE
        CustomerHomeController::clearCache();

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Artikel berhasil ditambahkan.');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(Article $article)
    {
        $categories = ArticleCategory::active()->sorted()->get();
        return view('admin.articles.edit', compact('article', 'categories'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

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

        // Generate slug unik
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

        // ============================================
        // 🔥 HANDLE IMAGE UPDATE
        // ============================================
        
        $oldImage = $article->image;      // simpan dulu path lama
        $newImagePath = null;

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // 1. 🔥 UPLOAD & CONVERT GAMBAR BARU DULU (jangan hapus dulu)
            $newImagePath = $this->imageOptimizer->convertToWebp(
                file: $request->file('image'),
                folder: 'articles',
                maxWidth: 1200,
                quality: 82
            );

            // 2. Set ke validated
            $validated['image'] = $newImagePath;
        } else {
            // 🔥 JANGAN HAPUS, PERTAHANKAN YANG LAMA
            if ($article->image) {
                $validated['image'] = $article->image;
            }
        }

        if ($request->filled('published_at')) {
            $validated['published_at'] = $request->published_at;
        }

        // ============================================
        // 🔥 UPDATE DATABASE
        // ============================================
        $article->update($validated);

        // ============================================
        // 🔥 HAPUS GAMBAR LAMA SETELAH UPDATE SUKSES
        // ============================================
        if ($newImagePath && $oldImage && $oldImage !== $newImagePath) {
            $deleted = $this->deleteOldImage($oldImage);
            
            \Log::info('🗑️ Old article image deletion attempt', [
                'article_id' => $article->id,
                'old_image' => $oldImage,
                'new_image' => $newImagePath,
                'deleted' => $deleted,
            ]);
        }

        // 🔥 CLEAR CACHE HOMEPAGE
        CustomerHomeController::clearCache();

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Artikel berhasil diperbarui.');
    }

    private function deleteOldImage(?string $imagePath): bool
    {
        if (empty($imagePath)) {
            return false;
        }

        // Normalisasi path
        $normalizedPath = $this->normalizeImagePath($imagePath);

        if (!$normalizedPath) {
            \Log::warning('⚠️ Could not normalize path', ['path' => $imagePath]);
            return false;
        }

        // Cek ada di disk 'public'
        if (!Storage::disk('public')->exists($normalizedPath)) {
            \Log::warning('⚠️ File not found on disk', [
                'original' => $imagePath,
                'normalized' => $normalizedPath,
                'full_path' => Storage::disk('public')->path($normalizedPath),
            ]);
            return false;
        }

        // Hapus
        $deleted = Storage::disk('public')->delete($normalizedPath);

        if ($deleted) {
            \Log::info('✅ Old image deleted', ['path' => $normalizedPath]);
        } else {
            \Log::error('❌ Failed to delete image', ['path' => $normalizedPath]);
        }

        return $deleted;
    }

    /**
     * 🔥 Normalisasi path gambar — handle berbagai format.
     *
     * Input yang mungkin:
     * - "articles/abc.png" → "articles/abc.png"
     * - "storage/articles/abc.png" → "articles/abc.png"
     * - "/storage/articles/abc.png" → "articles/abc.png"
     * - "https://barokahsport.com/storage/articles/abc.png" → "articles/abc.png"
     */
    private function normalizeImagePath(?string $imagePath): ?string
    {
        if (empty($imagePath)) {
            return null;
        }

        $path = $imagePath;

        // 1. Kalau URL lengkap (http://...), ambil path-nya saja
        if (preg_match('#^https?://#i', $path)) {
            $parsed = parse_url($path, PHP_URL_PATH);
            $path = $parsed ?? '';
        }

        // 2. Hapus prefix "/storage/"
        if (str_starts_with($path, '/storage/')) {
            $path = substr($path, strlen('/storage/'));
        }

        // 3. Hapus prefix "storage/"
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        // 4. Hapus leading slash
        $path = ltrim($path, '/');

        return $path ?: null;
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */

    public function destroy(Article $article)
    {
        \Log::info('=== DELETE ARTICLE ===', ['id' => $article->id]);

        // 🔥 HAPUS GAMBAR VIA HELPER
        $this->deleteImageFile($article->image);

        $article->delete();

        // 🔥 CLEAR CACHE HOMEPAGE
        CustomerHomeController::clearCache();

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Artikel berhasil dihapus.');
    }

    /*
    |--------------------------------------------------------------------------
    | STATS
    |--------------------------------------------------------------------------
    */

    public function stats(Article $article)
    {
        $article->load('articleCategory');

        // ============================================
        // SUMMARY CARDS
        // ============================================
        $totalViews = $article->views ?? 0;

        $totalLikes = ArticleLike::where('article_id', $article->id)->count();

        $totalComments = ArticleComment::where('article_id', $article->id)
            ->where('is_active', true)
            ->count();

        $totalReplies = ArticleComment::where('article_id', $article->id)
            ->where('is_active', true)
            ->whereNotNull('parent_id')
            ->count();

        $totalTopLevelComments = $totalComments - $totalReplies;

        // 🔥 KOMENTAR YANG BELUM DIBALAS ADMIN
        $unrepliedCount = ArticleComment::where('article_id', $article->id)
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->whereNull('replied_at')
            ->count();

        $engagementRate = $totalViews > 0
            ? (($totalLikes + $totalComments) / $totalViews) * 100
            : 0;

        // ============================================
        // VIEWS PER HARI - 30 HARI TERAKHIR
        // ============================================
        $startDate = now()->subDays(29)->startOfDay();

        $rawViews = \App\Models\ArticleView::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->where('article_id', $article->id)
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->pluck('total', 'date');

        $viewsPerDay = collect();
        for ($i = 0; $i < 30; $i++) {
            $date = $startDate->copy()->addDays($i);
            $key = $date->format('Y-m-d');

            $viewsPerDay->push([
                'date' => $key,
                'label' => $date->format('d M'),
                'views' => (int) ($rawViews[$key] ?? 0),
            ]);
        }

        // ============================================
        // VIEWS TAMBAHAN
        // ============================================
        $viewsToday = \App\Models\ArticleView::where('article_id', $article->id)
            ->whereDate('created_at', today())
            ->count();

        $viewsYesterday = \App\Models\ArticleView::where('article_id', $article->id)
            ->whereDate('created_at', today()->subDay())
            ->count();

        $viewsGrowth = $viewsYesterday > 0
            ? (($viewsToday - $viewsYesterday) / $viewsYesterday) * 100
            : ($viewsToday > 0 ? 100 : 0);

        $uniqueVisitors = \App\Models\ArticleView::where('article_id', $article->id)
            ->whereNotNull('ip_address')
            ->distinct('ip_address')
            ->count('ip_address');

        // ============================================
        // KOMENTAR - LOAD SEMUA DENGAN REPLIES
        // ============================================
        $comments = ArticleComment::with([
                'user',
                'replies.user',
                'replies.replies.user',
            ])
            ->where('article_id', $article->id)
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('replied_at', 'asc')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        // ============================================
        // TOP LIKERS
        // ============================================
        $topLikers = ArticleLike::with('user')
            ->where('article_id', $article->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.articles.stats', compact(
            'article',
            'totalViews',
            'totalLikes',
            'totalComments',
            'totalReplies',
            'totalTopLevelComments',
            'unrepliedCount',
            'engagementRate',
            'viewsPerDay',
            'viewsToday',
            'viewsYesterday',
            'viewsGrowth',
            'uniqueVisitors',
            'comments',
            'topLikers',
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | REPLY COMMENT
    |--------------------------------------------------------------------------
    */

    public function replyComment(Request $request, ArticleComment $comment)
    {
        $request->validate([
            'content' => 'required|string|min:1|max:1000',
        ]);

        $admin = Auth::user();

        $reply = ArticleComment::create([
            'article_id' => $comment->article_id,
            'user_id' => $admin->id,
            'parent_id' => $comment->id,
            'content' => $request->content,
            'is_active' => true,
            'replied_at' => now(),
        ]);

        $comment->update(['replied_at' => now()]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Balasan berhasil dikirim',
                'reply' => [
                    'id' => $reply->id,
                    'content' => $reply->content,
                    'user_name' => $admin->name,
                    'created_at' => $reply->created_at->diffForHumans(),
                ],
            ]);
        }

        return back()->with('success', 'Balasan berhasil dikirim.');
    }

    /*
    |--------------------------------------------------------------------------
    | 🔥 HELPER: DELETE IMAGE FILE (NORMALIZED PATH)
    |--------------------------------------------------------------------------
    |
    | Handle berbagai format path:
    | - "articles/xxx.webp"                 → normal
    | - "/articles/xxx.webp"                → ada leading slash
    | - "storage/articles/xxx.webp"         → ada prefix "storage/"
    | - "https://domain.com/storage/..."    → URL lengkap
    |
    */

    private function deleteImageFile(?string $imagePath): bool
    {
        if (!$imagePath) {
            return false;
        }

        // 🔥 NORMALIZE PATH
        $normalizedPath = $imagePath;

        // 1. Hapus prefix "storage/"
        if (str_starts_with($normalizedPath, 'storage/')) {
            $normalizedPath = substr($normalizedPath, strlen('storage/'));
        }

        // 2. Hapus leading slash
        $normalizedPath = ltrim($normalizedPath, '/');

        // 3. Handle URL lengkap (https://...)
        if (preg_match('#^https?://#i', $normalizedPath)) {
            $parsedPath = parse_url($normalizedPath, PHP_URL_PATH);
            $normalizedPath = ltrim($parsedPath ?? '', '/');

            // Hapus prefix "storage/" lagi setelah parse URL
            if (str_starts_with($normalizedPath, 'storage/')) {
                $normalizedPath = substr($normalizedPath, strlen('storage/'));
            }
        }

        // 🔥 CEK & HAPUS
        if ($normalizedPath && Storage::disk('public')->exists($normalizedPath)) {
            $deleted = Storage::disk('public')->delete($normalizedPath);

            \Log::info('Image deleted', [
                'original' => $imagePath,
                'normalized' => $normalizedPath,
                'deleted' => $deleted,
            ]);

            return $deleted;
        }

        \Log::warning('Image not found for deletion', [
            'original' => $imagePath,
            'normalized' => $normalizedPath,
            'full_path' => Storage::disk('public')->path($normalizedPath),
        ]);

        return false;
    }
}