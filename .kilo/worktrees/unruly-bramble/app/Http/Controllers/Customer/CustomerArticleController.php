<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleLike;
use App\Models\ArticleComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerArticleController extends Controller
{
    public function index(Request $request)
    {
        // 🔥 AMBIL KATEGORI UNTUK FILTER
        $categories = ArticleCategory::active()->sorted()->get();

        // 🔥 QUERY ARTIKEL
        $query = Article::with('articleCategory')
            ->active()
            ->published();

        // 🔥 FILTER KATEGORI
        if ($request->filled('category')) {
            $query->where('article_category_id', $request->category);
        }

        // 🔥 FILTER TAGS
        if ($request->filled('tag')) {
            $query->where('tags', 'like', '%' . $request->tag . '%');
        }

        // 🔥 SEARCH
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('content', 'like', '%' . $search . '%')
                  ->orWhere('author', 'like', '%' . $search . '%');
            });
        }

        // 🔥 SORT
        switch ($request->sort) {
            case 'oldest':
                $query->orderBy('published_at', 'asc')
                      ->orderBy('created_at', 'asc');
                break;
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('published_at', 'desc')
                      ->orderBy('created_at', 'desc');
                break;
        }

        // 🔥 PAGINATION
        $articles = $query->paginate(12);

        // 🔥 AMBIL SEMUA TAGS UNIK UNTUK FILTER
        $allTags = Article::active()
            ->published()
            ->whereNotNull('tags')
            ->get()
            ->pluck('tags')
            ->flatten()
            ->unique()
            ->values()
            ->toArray();

        return view('customer.articles.index', compact(
            'articles',
            'categories',
            'allTags'
        ));
    }

    public function show($slug)
    {
        // 🔥 AMBIL ARTIKEL UTAMA
        $article = Article::with('articleCategory')
            ->where('slug', $slug)
            ->active()
            ->published()
            ->firstOrFail();

        // 🔥 INCREMENT VIEWS - HANYA JIKA BELUM DIBACA
        // Views akan diincrement melalui AJAX setelah halaman dimuat
        // dan dicek via LocalStorage

        // 🔥 1. ARTIKEL TERKAIT (same category)
        $relatedArticles = Article::with('articleCategory')
            ->where('article_category_id', $article->article_category_id)
            ->where('id', '!=', $article->id)
            ->active()
            ->published()
            ->latest()
            ->limit(5)
            ->get();

        // 🔥 2. ARTIKEL TERBARU
        $latestArticles = Article::with('articleCategory')
            ->where('id', '!=', $article->id)
            ->active()
            ->published()
            ->latest()
            ->limit(5)
            ->get();

        // 🔥 3. ARTIKEL POPULER (berdasarkan views) - TAMPILKAN SEMUA
        $popularArticles = Article::with('articleCategory')
            ->where('id', '!=', $article->id)
            ->active()
            ->published()
            ->orderBy('views', 'desc')
            ->limit(5)
            ->get();

        // 🔥 4. ARTIKEL REKOMENDASI (mix dari berbagai kategori, random)
        $recommendedArticles = Article::with('articleCategory')
            ->where('id', '!=', $article->id)
            ->where('article_category_id', '!=', $article->article_category_id)
            ->active()
            ->published()
            ->inRandomOrder()
            ->limit(4)
            ->get();

        // 🔥 5. ARTIKEL DENGAN TAGS SAMA (jika ada tags)
        $tagRelatedArticles = collect();
        if ($article->tags && count($article->tags) > 0) {
            $tagRelatedArticles = Article::with('articleCategory')
                ->where('id', '!=', $article->id)
                ->where(function($query) use ($article) {
                    foreach ($article->tags as $tag) {
                        $query->orWhere('tags', 'like', '%' . $tag . '%');
                    }
                })
                ->active()
                ->published()
                ->latest()
                ->limit(4)
                ->get();
        }

        // 🔥 6. SEMUA KATEGORI UNTUK SIDEBAR
        $categories = ArticleCategory::active()
            ->withCount('articles')
            ->sorted()
            ->get();

        return view('customer.articles.show', compact(
            'article',
            'relatedArticles',
            'latestArticles',
            'popularArticles',
            'recommendedArticles',
            'tagRelatedArticles',
            'categories'
        ));
    }

    /**
     * 🔥 API untuk mencatat views (dipanggil via AJAX)
     */
    public function recordView(Request $request)
    {
        $request->validate([
            'article_id' => 'required|exists:articles,id',
        ]);

        $article = Article::find($request->article_id);
        
        // 🔥 INCREMENT VIEWS
        $article->increment('views');
        
        return response()->json([
            'success' => true,
            'message' => 'View recorded',
            'views' => $article->views
        ]);
    }

    public function toggleLike(Request $request)
    {
        $request->validate([
            'article_id' => 'required|exists:articles,id',
        ]);

        // 🔥 GUNAKAN GUARD CUSTOMER
        $user = Auth::guard('customer')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu',
                'redirect' => route('customer.login')
            ], 401);
        }

        $articleId = $request->article_id;
        $existingLike = ArticleLike::where('article_id', $articleId)
            ->where('user_id', $user->id)
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            $liked = false;
            $message = 'Anda membatalkan like';
        } else {
            ArticleLike::create([
                'article_id' => $articleId,
                'user_id' => $user->id,
            ]);
            $liked = true;
            $message = 'Anda menyukai artikel ini';
        }

        $likesCount = ArticleLike::where('article_id', $articleId)->count();

        return response()->json([
            'success' => true,
            'message' => $message,
            'liked' => $liked,
            'likes_count' => $likesCount,
        ]);
    }

    /**
     * 🔥 GET LIKES COUNT
     */
    public function getLikes(Request $request)
    {
        $request->validate([
            'article_id' => 'required|exists:articles,id',
        ]);

        $articleId = $request->article_id;
        $likesCount = ArticleLike::where('article_id', $articleId)->count();
        $isLiked = false;

        // 🔥 GUNAKAN GUARD CUSTOMER
        if (Auth::guard('customer')->check()) {
            $isLiked = ArticleLike::where('article_id', $articleId)
                ->where('user_id', Auth::guard('customer')->id())
                ->exists();
        }

        return response()->json([
            'success' => true,
            'likes_count' => $likesCount,
            'is_liked' => $isLiked,
        ]);
    }

    /**
     * 🔥 GET COMMENTS
     */
    public function getComments(Request $request)
    {
        $request->validate([
            'article_id' => 'required|exists:articles,id',
        ]);

        // 🔥 AMBIL KOMENTAR UTAMA DENGAN SEMUA REPLY BERTINGKAT
        $comments = ArticleComment::with(['user', 'replies.user', 'replies.replies.user'])
            ->where('article_id', $request->article_id)
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'comments' => $this->formatCommentsWithReplies($comments),
        ]);
    }

    private function formatCommentsWithReplies($comments)
    {
        return $comments->map(function($comment) {
            return [
                'id' => $comment->id,
                'user_name' => $comment->user->name ?? 'User',
                'user_avatar' => strtoupper(substr($comment->user->name ?? 'U', 0, 1)),
                'content' => $comment->content,
                'created_at' => $comment->formatted_date,
                'user_id' => $comment->user_id,
                'replies' => $this->formatRepliesRecursive($comment->replies),
            ];
        });
    }

    private function formatRepliesRecursive($replies)
    {
        return $replies->map(function($reply) {
            return [
                'id' => $reply->id,
                'user_name' => $reply->user->name ?? 'User',
                'user_avatar' => strtoupper(substr($reply->user->name ?? 'U', 0, 1)),
                'content' => $reply->content,
                'created_at' => $reply->formatted_date,
                'user_id' => $reply->user_id,
                'replies' => $reply->replies->isNotEmpty() 
                    ? $this->formatRepliesRecursive($reply->replies) 
                    : [],
            ];
        });
    }

    /**
     * 🔥 POST COMMENT
     */
    public function postComment(Request $request)
    {
        $request->validate([
            'article_id' => 'required|exists:articles,id',
            'content' => 'required|string|min:1|max:1000',
            'parent_id' => 'nullable|exists:article_comments,id',
        ]);

        $user = Auth::guard('customer')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu',
                'redirect' => route('customer.login')
            ], 401);
        }

        $comment = ArticleComment::create([
            'article_id' => $request->article_id,
            'user_id' => $user->id,
            'parent_id' => $request->parent_id,
            'content' => $request->content,
            'is_active' => true,
        ]);

        $comment->load('user');

        $commentsCount = ArticleComment::where('article_id', $request->article_id)
            ->where('is_active', true)
            ->count();

        return response()->json([
            'success' => true,
            'message' => 'Komentar berhasil ditambahkan',
            'comment' => [
                'id' => $comment->id,
                'user_name' => $comment->user->name,
                'user_avatar' => strtoupper(substr($comment->user->name, 0, 1)),
                'content' => $comment->content,
                'created_at' => $comment->formatted_date,
                'parent_id' => $comment->parent_id,
                'user_id' => $comment->user_id,
                'replies' => [],
            ],
            'comments_count' => $commentsCount,
        ]);
    }

    /**
     * 🔥 DELETE COMMENT (hanya owner atau admin)
     */
    public function deleteComment(Request $request)
    {
        $request->validate([
            'comment_id' => 'required|exists:article_comments,id',
        ]);

        // 🔥 CEK DARI SEMUA GUARD
        $user = Auth::guard('customer')->user() ?? Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu',
            ], 401);
        }

        $comment = ArticleComment::with('replies')->find($request->comment_id);

        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Komentar tidak ditemukan',
            ], 404);
        }

        // 🔥 ADMIN BISA HAPUS SEMUA KOMENTAR
        $isAdmin = $user->role === 'admin';
        $isOwner = $comment->user_id == $user->id;

        if (!$isOwner && !$isAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk menghapus komentar ini',
            ], 403);
        }

        // Hapus semua reply jika ada
        if ($comment->replies->isNotEmpty()) {
            foreach ($comment->replies as $reply) {
                $reply->update(['is_active' => false]);
            }
        }

        $comment->update(['is_active' => false]);

        $commentsCount = ArticleComment::where('article_id', $comment->article_id)
            ->where('is_active', true)
            ->count();

        return response()->json([
            'success' => true,
            'message' => 'Komentar berhasil dihapus',
            'comments_count' => $commentsCount,
            'comment_id' => $comment->id,
        ]);
    }
}