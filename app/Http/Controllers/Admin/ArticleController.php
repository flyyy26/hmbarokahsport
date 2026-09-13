<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleComment;
use App\Models\ArticleLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
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

    /**
     * 🔥 HALAMAN DETAIL STATISTIK ARTIKEL
     * Menggunakan tabel `article_views` untuk data chart real per hari.
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

        // 🔥 JUMLAH KOMENTAR YANG BELUM DIBALAS ADMIN
        $unrepliedCount = ArticleComment::where('article_id', $article->id)
            ->where('is_active', true)
            ->whereNull('parent_id')  // hanya parent yang dihitung
            ->whereNull('replied_at')
            ->count();

        $engagementRate = $totalViews > 0
            ? (($totalLikes + $totalComments) / $totalViews) * 100
            : 0;

        // ============================================
        // VIEWS PER HARI - 30 HARI TERAKHIR
        // ============================================
        $startDate = now()->subDays(29)->startOfDay();

        // 🔥 Ambil agregat per hari dari tabel article_views
        $rawViews = \App\Models\ArticleView::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->where('article_id', $article->id)
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->pluck('total', 'date'); // ['2026-09-14' => 2, ...]

        // 🔥 Bangun array 30 hari — isi 0 kalau tidak ada data
        $viewsPerDay = collect();
        for ($i = 0; $i < 30; $i++) {
            $date = $startDate->copy()->addDays($i);
            $key  = $date->format('Y-m-d');

            $viewsPerDay->push([
                'date'  => $key,
                'label' => $date->format('d M'),
                'views' => (int) ($rawViews[$key] ?? 0),  // 0 kalau tidak ada
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
            ->orderBy('replied_at', 'asc')   // 🔥 yang belum dibalas di atas
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
    public function replyComment(Request $request, ArticleComment $comment)
    {
        $request->validate([
            'content' => 'required|string|min:1|max:1000',
        ]);

        $admin = Auth::user();

        // 🔥 Buat reply sebagai user admin
        $reply = ArticleComment::create([
            'article_id' => $comment->article_id,
            'user_id'    => $admin->id,
            'parent_id'  => $comment->id,
            'content'    => $request->content,
            'is_active'  => true,
            'replied_at' => now(), // reply dari admin otomatis dianggap "dibalas"
        ]);

        // 🔥 Tandai komentar induk sudah dibalas admin
        $comment->update(['replied_at' => now()]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Balasan berhasil dikirim',
                'reply'   => [
                    'id'         => $reply->id,
                    'content'    => $reply->content,
                    'user_name'  => $admin->name,
                    'created_at' => $reply->created_at->diffForHumans(),
                ],
            ]);
        }

        return back()->with('success', 'Balasan berhasil dikirim.');
    }
}