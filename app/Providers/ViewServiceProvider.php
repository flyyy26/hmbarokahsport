<?php

namespace App\Providers;

use App\Models\ArticleComment;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\CareerApplication;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // 🔥 Share data ke layout admin (semua view admin)
        View::composer('layouts.admin', function ($view) {
            // Komentar artikel yang belum dibaca admin (24 jam terakhir)
            // Asumsi: admin dianggap sudah baca jika ada field `read_at` — kalau belum ada,
            // kita pakai komentar dalam 24 jam terakhir sebagai "baru"
            $newArticleComments = ArticleComment::where('is_active', true)
                ->whereNull('parent_id')  // hanya parent
                ->whereNull('replied_at') // belum dibalas
                ->count();

            $unreadCareerApplications = CareerApplication::whereNull('read_at')->count();

            // Kalau mau pakai sistem read/unread, tambahkan kolom `read_at` di article_comments
            // lalu query: ->whereNull('read_at')

            $view->with('newArticleComments', $newArticleComments);
            $view->with('unreadCareerApplications', $unreadCareerApplications);
        });
    }
}