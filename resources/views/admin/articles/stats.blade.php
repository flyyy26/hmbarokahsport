@extends('layouts.admin')

@section('title', 'Statistik Artikel')
@section('page-title', 'Statistik Artikel')

@section('content')

<div class="w-full space-y-4">

    {{-- ============================================ --}}
    {{-- HEADER --}}
    {{-- ============================================ --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <a href="{{ route('admin.articles.index') }}"
               class="inline-flex items-center gap-1.5 text-xs font-semibold mb-2 transition-colors"
               style="color: var(--text-5);"
               onmouseover="this.style.color='#ecbc42'"
               onmouseout="this.style.color='var(--text-5)'">
                <iconify-icon icon="mdi:arrow-left"></iconify-icon>
                Kembali ke Daftar Artikel
            </a>
            <h1 class="text-xl font-bold flex items-center gap-2" style="color: var(--text-1)">
                <iconify-icon icon="mdi:chart-box-outline" class="text-2xl" style="color: #ecbc42"></iconify-icon>
                Statistik Artikel
            </h1>
            <p class="text-sm mt-1 line-clamp-2" style="color: var(--text-5)">
                {{ $article->title }}
            </p>
        </div>

        <div class="flex items-center gap-2 flex-shrink-0">
            <a href="{{ route('customer.articles.show', $article->slug) }}"
               target="_blank"
               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-bold border transition-all"
               style="background: var(--bg-input); border-color: var(--border-2); color: var(--text-3);"
               onmouseover="this.style.borderColor='#ecbc42'; this.style.color='#FDDD57'"
               onmouseout="this.style.borderColor='var(--border-2)'; this.style.color='var(--text-3)'">
                <iconify-icon icon="mdi:open-in-new"></iconify-icon>
                Lihat Artikel
            </a>
            <a href="{{ route('admin.articles.edit', $article) }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-bold transition-all
                      bg-gradient-to-r from-[#FDDD57] to-[#ecbc42] text-slate-900
                      shadow-md shadow-amber-500/20 hover:shadow-lg hover:shadow-amber-500/40">
                <iconify-icon icon="mdi:pencil-outline"></iconify-icon>
                Edit Artikel
            </a>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- SUMMARY CARDS --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">

        {{-- VIEWS --}}
        <div class="rounded-xl border p-4" style="background: var(--bg-card); border-color: var(--border-2);">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                            bg-blue-500/10 border border-blue-500/30">
                    <iconify-icon icon="mdi:eye-outline" class="text-blue-400 text-lg"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5);">Total Views</p>
                    <p class="text-xl font-bold" style="color: var(--text-1);">
                        {{ number_format($totalViews) }}
                    </p>
                </div>
            </div>
        </div>

        {{-- LIKES --}}
        <div class="rounded-xl border p-4" style="background: var(--bg-card); border-color: var(--border-2);">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                            bg-red-500/10 border border-red-500/30">
                    <iconify-icon icon="mdi:heart-outline" class="text-red-400 text-lg"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5);">Total Likes</p>
                    <p class="text-xl font-bold" style="color: var(--text-1);">
                        {{ number_format($totalLikes) }}
                    </p>
                </div>
            </div>
        </div>

        {{-- COMMENTS --}}
        <div class="rounded-xl border p-4" style="background: var(--bg-card); border-color: var(--border-2);">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                            bg-emerald-500/10 border border-emerald-500/30">
                    <iconify-icon icon="mdi:comment-outline" class="text-emerald-400 text-lg"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5);">Komentar</p>
                    <p class="text-xl font-bold" style="color: var(--text-1);">
                        {{ number_format($totalComments) }}
                    </p>
                    <p class="text-[10px]" style="color: var(--text-5);">
                        {{ $totalTopLevelComments }} utama · {{ $totalReplies }} balasan
                    </p>
                </div>
            </div>
        </div>

        {{-- ENGAGEMENT --}}
        <div class="rounded-xl border p-4" style="background: var(--bg-card); border-color: var(--border-2);">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                            bg-amber-500/10 border border-amber-500/30">
                    <iconify-icon icon="mdi:chart-line" class="text-amber-400 text-lg"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-5);">Engagement</p>
                    <p class="text-xl font-bold" style="color: var(--text-1);">
                        {{ number_format($engagementRate, 2) }}%
                    </p>
                    <p class="text-[10px]" style="color: var(--text-5);">(likes + komentar) / views</p>
                </div>
            </div>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- CHART: VIEWS 30 HARI TERAKHIR --}}
    {{-- ============================================ --}}
    <div class="rounded-xl border overflow-hidden"
         style="background: var(--bg-card); border-color: var(--border-2);">
        <div class="px-4 py-2.5 border-b flex items-center justify-between"
             style="background: var(--bg-input); border-color: var(--border-2);">
            <h3 class="font-bold text-sm flex items-center gap-2" style="color: var(--text-1);">
                <iconify-icon icon="mdi:chart-areaspline" style="color: #ecbc42;"></iconify-icon>
                Views 30 Hari Terakhir
            </h3>
            <span class="text-[10px]" style="color: var(--text-5);">
                Published: {{ $article->formatted_published_at }}
            </span>
        </div>
        <div class="p-4">
            @php
                $maxViews = collect($viewsPerDay)->max('views') ?: 0;
                $hasData  = $maxViews > 0;

                // ============================================
                // 🔥 SKALA ADAPTIF: bar tertinggi menyesuaikan skala views
                // ============================================
                if ($maxViews <= 3) {
                    // Skala sangat kecil (1-3 views)
                    $maxBarHeight = 20;    // bar tertinggi cuma 20px
                } elseif ($maxViews <= 10) {
                    $maxBarHeight = 40;    // 4-10 views
                } elseif ($maxViews <= 50) {
                    $maxBarHeight = 80;    // 11-50 views
                } elseif ($maxViews <= 200) {
                    $maxBarHeight = 120;   // 51-200 views
                } elseif ($maxViews <= 1000) {
                    $maxBarHeight = 150;   // 201-1000 views
                } else {
                    $maxBarHeight = 160;   // >1000 views → full
                }

                // Container height selalu 160px supaya konsisten tampilannya
                $containerHeight = 160;
            @endphp

            @if($hasData)
                <div class="flex items-end gap-1" style="height: {{ $containerHeight }}px;">
                    @foreach($viewsPerDay as $day)
                        @php
                            if ($day['views'] > 0) {
                                // Hitung tinggi proporsional terhadap max
                                $barHeight = ($day['views'] / $maxViews) * $maxBarHeight;

                                // Minimal 4px biar tetap kelihatan
                                $barHeight = max($barHeight, 4);
                            } else {
                                // Hari tanpa views → garis tipis 2px
                                $barHeight = 2;
                            }
                        @endphp
                        <div class="flex-1 flex flex-col items-center justify-end group relative" style="height: 100%;">
                            <div class="w-full rounded-t transition-all hover:opacity-80 cursor-pointer"
                                style="height: {{ $barHeight }}px;
                                        background: {{ $day['views'] > 0
                                            ? 'linear-gradient(180deg, #FDDD57 0%, #ecbc42 100%)'
                                            : 'var(--border-2)' }};">
                            </div>

                            {{-- Tooltip --}}
                            <div class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 rounded text-[10px] font-bold
                                        opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap"
                                style="background: #1e1e1e; color: #FDDD57; z-index: 10;">
                                {{ $day['label'] }}: {{ number_format($day['views']) }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex items-center justify-between mt-2 text-[10px]" style="color: var(--text-5);">
                    <span>{{ $viewsPerDay->first()['label'] ?? '' }}</span>
                    <span class="font-bold" style="color: var(--text-4);">
                        Total 30 hari: {{ number_format(collect($viewsPerDay)->sum('views')) }}
                    </span>
                    <span>{{ $viewsPerDay->last()['label'] ?? '' }}</span>
                </div>
            @else
                <div class="text-center py-12">
                    <iconify-icon icon="mdi:chart-areaspline" class="text-4xl" style="color: var(--text-6);"></iconify-icon>
                    <p class="text-xs mt-2" style="color: var(--text-5);">
                        Belum ada data views dalam 30 hari terakhir.
                    </p>
                </div>
            @endif

            <p class="text-[10px] mt-2 italic" style="color: var(--text-5);">
                * Data real dari tabel <code>article_views</code>.
            </p>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- GRID: KOMENTAR TERBARU + TOP LIKERS --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">

        {{-- 🔥 KOMENTAR DENGAN FITUR BALAS ADMIN --}}
        <div class="rounded-xl border overflow-hidden"
            style="background: var(--bg-card); border-color: var(--border-2);">
            <div class="px-4 py-2.5 border-b flex items-center justify-between"
                style="background: var(--bg-input); border-color: var(--border-2);">
                <h3 class="font-bold text-sm flex items-center gap-2" style="color: var(--text-1);">
                    <iconify-icon icon="mdi:comment-multiple-outline" style="color: #ecbc42;"></iconify-icon>
                    Komentar
                </h3>
                <div class="flex items-center gap-1.5">
                    @if($unrepliedCount > 0)
                        <span class="text-[10px] px-2 py-0.5 rounded-full font-bold"
                            style="background: rgba(239,68,68,0.15); color: #f87171;">
                            {{ $unrepliedCount }} belum dibalas
                        </span>
                    @endif
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-bold"
                        style="background: rgba(52,211,153,0.15); color: #34d399;">
                        {{ number_format($totalComments) }}
                    </span>
                </div>
            </div>

            <div class="p-3 max-h-[600px] overflow-y-auto" id="comments-container">
                @forelse($comments as $comment)
                    @php
                        $commentUser = $comment->user;
                        $commentAvatarUrl = $commentUser?->avatar_url;
                        $isUnreplied = is_null($comment->replied_at);
                    @endphp

                    <div class="py-3 border-b last:border-0"
                        id="comment-{{ $comment->id }}"
                        style="border-color: var(--border-1);">

                        {{-- HEADER --}}
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0
                                        overflow-hidden bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]">
                                @if($commentAvatarUrl)
                                    <img src="{{ $commentAvatarUrl }}"
                                        alt="{{ $commentUser->name }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <span class="text-slate-900 font-bold text-[11px]">
                                        {{ strtoupper(substr($commentUser->name ?? 'U', 0, 1)) }}
                                    </span>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-xs font-bold" style="color: var(--text-1);">
                                        {{ $commentUser->name ?? 'User' }}
                                    </span>
                                    <span class="text-[10px]" style="color: var(--text-5);">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </span>
                                    @if($isUnreplied)
                                        <span class="text-[9px] px-1.5 py-0.5 rounded font-bold"
                                            style="background: rgba(239,68,68,0.15); color: #f87171;">
                                            Belum dibalas
                                        </span>
                                    @else
                                        <span class="text-[9px] px-1.5 py-0.5 rounded font-bold"
                                            style="background: rgba(52,211,153,0.15); color: #34d399;">
                                            ✓ Dibalas
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs mt-1.5 leading-relaxed" style="color: var(--text-3);">
                                    {{ $comment->content }}
                                </p>
                            </div>
                        </div>

                        {{-- 🔥 EXISTING REPLIES --}}
                        @if($comment->replies->isNotEmpty())
                            <div class="mt-3 ml-12 space-y-2">
                                @foreach($comment->replies as $reply)
                                    @php
                                        $replyUser = $reply->user;
                                        $replyAvatarUrl = $replyUser?->avatar_url;
                                        $isAdminReply = $replyUser && $replyUser->role === 'admin';
                                    @endphp
                                    <div class="flex items-start gap-2.5 p-2.5 rounded-lg"
                                        style="background: {{ $isAdminReply ? 'rgba(236,188,66,0.06)' : 'var(--bg-input)' }};">
                                        <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0
                                                    overflow-hidden
                                                    {{ $isAdminReply ? 'bg-gradient-to-br from-[#FDDD57] to-[#ecbc42]' : 'bg-gradient-to-br from-blue-400 to-blue-600' }}">
                                            @if($replyAvatarUrl)
                                                <img src="{{ $replyAvatarUrl }}"
                                                    alt="{{ $replyUser->name }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <span class="{{ $isAdminReply ? 'text-slate-900' : 'text-white' }} font-bold text-[10px]">
                                                    {{ strtoupper(substr($replyUser->name ?? 'U', 0, 1)) }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="text-[11px] font-bold" style="color: var(--text-1);">
                                                    {{ $replyUser->name ?? 'User' }}
                                                </span>
                                                @if($isAdminReply)
                                                    <span class="text-[9px] px-1.5 py-0.5 rounded font-bold"
                                                        style="background: rgba(236,188,66,0.2); color: #ecbc42;">
                                                        Admin
                                                    </span>
                                                @endif
                                                <span class="text-[10px]" style="color: var(--text-5);">
                                                    {{ $reply->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                            <p class="text-[11px] mt-1 leading-relaxed" style="color: var(--text-3);">
                                                {{ $reply->content }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- 🔥 FORM BALAS ADMIN --}}
                        <div class="mt-3 ml-12">
                            <button type="button"
                                    onclick="toggleReplyForm({{ $comment->id }})"
                                    class="inline-flex items-center gap-1.5 text-[11px] font-semibold
                                        transition-colors"
                                    style="color: #ecbc42;"
                                    onmouseover="this.style.color='#FDDD57'"
                                    onmouseout="this.style.color='#ecbc42'">
                                <iconify-icon icon="mdi:reply"></iconify-icon>
                                Balas sebagai Admin
                            </button>

                            <form id="reply-form-{{ $comment->id }}"
                                action="{{ route('admin.articles.comments.reply', ['article' => $article->id, 'comment' => $comment->id]) }}"
                                method="POST"
                                onsubmit="return submitAdminReply(event, {{ $comment->id }})"
                                class="hidden mt-2">
                                @csrf
                                <textarea name="content"
                                        id="reply-input-{{ $comment->id }}"
                                        rows="2"
                                        required
                                        placeholder="Tulis balasan Anda sebagai admin..."
                                        class="w-full px-3 py-2 rounded-lg text-xs resize-none
                                                focus:outline-none focus:ring-2"
                                        style="background: var(--bg-input);
                                                border: 1px solid var(--border-2);
                                                color: var(--text-1);
                                                focus:border-color: #ecbc42;"></textarea>
                                <div class="flex items-center justify-end gap-2 mt-2">
                                    <button type="button"
                                            onclick="toggleReplyForm({{ $comment->id }})"
                                            class="px-3 py-1.5 rounded-lg text-[11px] font-semibold
                                                transition-all"
                                            style="color: var(--text-4); background: transparent; border: 1px solid var(--border-2);">
                                        Batal
                                    </button>
                                    <button type="submit"
                                            id="reply-submit-{{ $comment->id }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                                                text-[11px] font-bold
                                                bg-gradient-to-r from-[#FDDD57] to-[#ecbc42]
                                                text-slate-900
                                                shadow-md shadow-amber-500/20
                                                hover:shadow-lg hover:shadow-amber-500/40
                                                transition-all active:scale-95">
                                        <iconify-icon icon="mdi:send"></iconify-icon>
                                        Kirim Balasan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10">
                        <iconify-icon icon="mdi:comment-outline" class="text-4xl" style="color: var(--text-6);"></iconify-icon>
                        <p class="text-xs mt-2" style="color: var(--text-5);">Belum ada komentar</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- TOP LIKERS --}}
        <div class="rounded-xl border overflow-hidden"
            style="background: var(--bg-card); border-color: var(--border-2);">
            <div class="px-4 py-2.5 border-b flex items-center justify-between"
                style="background: var(--bg-input); border-color: var(--border-2);">
                <h3 class="font-bold text-sm flex items-center gap-2" style="color: var(--text-1);">
                    <iconify-icon icon="mdi:heart-multiple-outline" style="color: #ecbc42;"></iconify-icon>
                    Yang Menyukai
                </h3>
                <span class="text-[10px] px-2 py-0.5 rounded-full font-bold"
                    style="background: rgba(239,68,68,0.15); color: #f87171;">
                    {{ number_format($totalLikes) }}
                </span>
            </div>
            <div class="p-3 max-h-96 overflow-y-auto">
                @forelse($topLikers as $like)
                    @php
                        $user = $like->user;
                        $avatarUrl = null;
                        if ($user && $user->avatar) {
                            // kalau avatar disimpan di storage/app/public
                            $avatarUrl = \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)
                                ? \Illuminate\Support\Facades\Storage::url($user->avatar)
                                : null;
                        }
                    @endphp
                    <div class="flex items-center gap-3 py-2.5 border-b last:border-0"
                        style="border-color: var(--border-1);">

                        {{-- 🔥 AVATAR: foto kalau ada, inisial kalau tidak --}}
                        <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0
                                    overflow-hidden bg-gradient-to-br from-red-400 to-red-600
                                    shadow-md shadow-red-500/20">
                            @if($avatarUrl)
                                <img src="{{ $avatarUrl }}"
                                    alt="{{ $user->name }}"
                                    class="w-full h-full object-cover"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <span class="hidden text-white font-bold text-[11px] w-full h-full items-center justify-center">
                                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                </span>
                            @else
                                <span class="text-white font-bold text-[11px]">
                                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                </span>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold truncate" style="color: var(--text-1);">
                                {{ $user->name ?? 'User' }}
                            </p>
                            <p class="text-[10px]" style="color: var(--text-5);">
                                {{ $like->created_at->diffForHumans() }}
                            </p>
                        </div>

                        <iconify-icon icon="mdi:heart" class="text-red-400 text-base"></iconify-icon>
                    </div>
                @empty
                    <div class="text-center py-10">
                        <iconify-icon icon="mdi:heart-outline" class="text-4xl" style="color: var(--text-6);"></iconify-icon>
                        <p class="text-xs mt-2" style="color: var(--text-5);">Belum ada yang menyukai</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

<script>
// ============================================
// TOGGLE REPLY FORM
// ============================================
function toggleReplyForm(commentId) {
    const form = document.getElementById('reply-form-' + commentId);
    const input = document.getElementById('reply-input-' + commentId);

    if (form.classList.contains('hidden')) {
        form.classList.remove('hidden');
        input.focus();
    } else {
        form.classList.add('hidden');
        input.value = '';
    }
}

// ============================================
// SUBMIT ADMIN REPLY via AJAX
// ============================================
function submitAdminReply(event, commentId) {
    event.preventDefault();

    const form = event.target;
    const btn = document.getElementById('reply-submit-' + commentId);
    const input = document.getElementById('reply-input-' + commentId);
    const content = input.value.trim();

    if (!content) {
        alert('Balasan tidak boleh kosong');
        return false;
    }

    btn.disabled = true;
    btn.innerHTML = '<iconify-icon icon="mdi:loading" class="animate-spin"></iconify-icon> Mengirim...';

    const formData = new FormData(form);
    formData.append('content', content);

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'Accept': 'application/json',
        },
        body: formData,
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Reload halaman biar badge + reply baru muncul
            window.location.reload();
        } else {
            alert(data.message || 'Gagal mengirim balasan');
            btn.disabled = false;
            btn.innerHTML = '<iconify-icon icon="mdi:send"></iconify-icon> Kirim Balasan';
        }
    })
    .catch(err => {
        console.error(err);
        alert('Terjadi kesalahan, coba lagi.');
        btn.disabled = false;
        btn.innerHTML = '<iconify-icon icon="mdi:send"></iconify-icon> Kirim Balasan';
    });

    return false;
}

// ============================================
// AUTO SCROLL KE KOMENTAR YANG BELUM DIBALAS
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    const firstUnreplied = document.querySelector('.comment-item.unreplied') ||
                            document.querySelector('[id^="comment-"] span:contains("Belum dibalas")');

    // Kalau pakai ID di div parent
    const container = document.getElementById('comments-container');
    if (container) {
        const unrepliedEl = container.querySelector('.border-b [style*="rgba(239,68,68"]');
        if (unrepliedEl) {
            // Scroll ke komentar yang belum dibalas
            unrepliedEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
});
</script>

@endsection