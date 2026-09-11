@php
    // 🔥 CEK LOGIN DARI SEMUA GUARD
    $isLoggedIn = Auth::guard('customer')->check() || Auth::check();
    $userId = Auth::guard('customer')->id() ?? Auth::id();
    $userRole = Auth::guard('customer')->user()->role ?? Auth::user()->role ?? 'customer';
    $isAdmin = $userRole === 'admin';
    
    // 🔥 CEK LIKE STATUS
    $isLiked = false;
    if ($userId) {
        $isLiked = $article->isLikedByUser($userId);
    }
@endphp

@extends('layouts.customer')

@section('title', $article->title . ' - Barokah Sport')

@section('content')



<style>
    /* ============================================
       LAYOUT UTAMA 75% - 25%
       ============================================ */
    .article-detail-wrapper {
        max-width: 82vw;
        margin: 0 auto;
        padding: 2vw 7.54vw 4vw;
        display: grid;
        grid-template-columns: 75% 25%;
        gap: 2.5vw;
    }

    /* ============================================
       KONTEN UTAMA
       ============================================ */
    .article-main {
        min-width: 0;
    }

    .article-detail-header {
        margin-bottom: 1.5vw;
    }

    .article-detail-header .article-back {
        display: inline-flex;
        align-items: center;
        gap: 0.4vw;
        color: #076694;
        text-decoration: none;
        font-size: 0.8vw;
        margin-bottom: 0.8vw;
        transition: color 0.2s;
        font-weight: 500;
    }

    .article-detail-header .article-back:hover {
        color: #055a7a;
    }

    .article-detail-header h1 {
        font-size: 2.2vw;
        font-weight: 700;
        color: #0f172a;
        margin: 0.5vw 0;
        line-height: 1.3;
    }

    .article-detail-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 1.5vw;
        margin-top: 0.8vw;
        font-size: 0.75vw;
        color: #94a3b8;
        padding-bottom: 1vw;
        border-bottom: 0.1vw solid #f1f5f9;
    }

    .article-detail-meta .meta-item {
        display: flex;
        align-items: center;
        gap: 0.3vw;
    }

    .article-detail-meta .meta-item iconify-icon {
        font-size: 0.9vw;
    }

    .article-detail-meta .meta-divider {
        width: 0.1vw;
        height: 1.2vw;
        background: #e2e8f0;
    }

    .article-detail-image {
        width: 100%;
        aspect-ratio: 16/8;
        border-radius: 0.8vw;
        overflow: hidden;
        margin: 1.2vw 0;
        background: #f1f5f9;
        position: relative;
    }

    .article-detail-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .article-detail-image .image-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        color: #94a3b8;
        font-size: 4vw;
    }

    /* ============================================
       LIKE & SHARE - DI BAWAH GAMBAR
       ============================================ */
    .article-interaction-section {
        margin: 1vw 0 1.5vw 0;
        padding: 1vw 0;
        border-bottom: 0.1vw solid #e2e8f0;
    }

    .article-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1vw;
    }

    .action-group {
        display: flex;
        align-items: center;
        gap: 0.8vw;
    }

    .action-btn {
        display: flex;
        align-items: center;
        gap: 0.4vw;
        padding: 0.4vw 1vw;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.5vw;
        background: #ffffff;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.8vw;
        color: #475569;
        font-family: inherit;
    }

    .action-btn:hover {
        background: #f8fafc;
        border-color: #94a3b8;
    }
    .action-icon{
        display:flex;
        align-items:center;
        justify-content:center;
    }

    .action-btn .action-icon {
        font-size: 1.2vw;
    }

    .action-btn .action-count {
        font-weight: 600;
        color: #0f172a;
    }

    .action-btn.liked {
        color: #ef4444;
        border-color: #ef4444;
        background: #fef2f2;
    }

    .action-btn.liked .action-icon {
        animation: heartBeat 0.3s ease;
    }

    @keyframes heartBeat {
        0% { transform: scale(1); }
        50% { transform: scale(1.3); }
        100% { transform: scale(1); }
    }

    .comment-count {
        display: flex;
        align-items: center;
        gap: 0.3vw;
        font-size: 0.8vw;
        color: #94a3b8;
    }

    /* ============================================
       KONTEN ARTIKEL
       ============================================ */
    .article-detail-content {
        font-size: 0.95vw;
        line-height: 1.8;
        color: #1e293b;
        margin-top: 0.5vw;
    }

    .article-detail-content h2 {
        font-size: 1.4vw;
        margin: 1.8vw 0 0.6vw;
        color: #0f172a;
        font-weight: 700;
    }

    .article-detail-content h3 {
        font-size: 1.1vw;
        margin: 1.2vw 0 0.5vw;
        color: #0f172a;
        font-weight: 600;
    }

    .article-detail-content ul,
    .article-detail-content ol {
        margin: 0.5vw 0 0.8vw 1.5vw;
    }

    .article-detail-content li {
        margin-bottom: 0.3vw;
    }

    .article-detail-content img {
        max-width: 100%;
        border-radius: 0.5vw;
        margin: 0.8vw 0;
    }

    .article-detail-content blockquote {
        border-left: 0.3vw solid #076694;
        padding: 0.8vw 1.2vw;
        margin: 1vw 0;
        background: #f8fafc;
        border-radius: 0 0.4vw 0.4vw 0;
        font-style: italic;
        color: #475569;
    }

    .article-detail-content table {
        width: 100%;
        border-collapse: collapse;
        margin: 1vw 0;
        font-size: 0.85vw;
    }

    .article-detail-content table th,
    .article-detail-content table td {
        padding: 0.5vw 0.8vw;
        border: 0.05vw solid #e2e8f0;
        text-align: left;
    }

    .article-detail-content table th {
        background: #f1f5f9;
        font-weight: 600;
    }

    /* ============================================
       TAGS
       ============================================ */
    .article-detail-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4vw;
        margin: 2vw 0 1vw;
        padding-top: 1.5vw;
        border-top: 0.1vw solid #e2e8f0;
    }

    .article-detail-tags .tag-label {
        font-size: 0.75vw;
        font-weight: 600;
        color: #475569;
        margin-right: 0.3vw;
    }

    .article-detail-tags .tag {
        padding: 0.2vw 0.8vw;
        border-radius: 100vw;
        font-size: 0.65vw;
        color: #475569;
        background: #f1f5f9;
        border: 0.05vw solid #e2e8f0;
        transition: all 0.2s;
        text-decoration: none;
    }

    .article-detail-tags .tag:hover {
        background: #076694;
        color: #ffffff;
        border-color: #076694;
    }

    /* ============================================
       SHARE MODAL
       ============================================ */
    .share-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
        z-index: 99999;
        display: none;
        justify-content: center;
        align-items: center;
        animation: modalFadeIn 0.3s ease;
    }

    .share-modal.active {
        display: flex !important;
    }

    .share-modal-content {
        background: #ffffff;
        border-radius: 0.8vw;
        padding: 2vw;
        max-width: 30vw;
        width: 100%;
        position: relative;
        animation: modalSlideUp 0.3s ease;
        box-shadow: 0 1vw 3vw rgba(0, 0, 0, 0.2);
    }

    @keyframes modalFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes modalSlideUp {
        from { 
            opacity: 0;
            transform: translateY(2vw) scale(0.95);
        }
        to { 
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .share-modal-close {
        position: absolute;
        top: 0.8vw;
        right: 1vw;
        background: none;
        border: none;
        font-size: 1.8vw;
        cursor: pointer;
        color: #94a3b8;
        transition: color 0.3s ease;
        line-height: 1;
        padding: 0.2vw 0.5vw;
    }

    .share-modal-close:hover {
        color: #0f172a;
    }

    .share-modal-content h3 {
        font-size: 1.2vw;
        font-weight: 700;
        margin-bottom: 1.5vw;
        color: #0f172a;
        text-align: center;
    }

    .share-modal-buttons {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.8vw;
    }

    .share-modal-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5vw;
        padding: 0.8vw 1vw;
        border-radius: 0.5vw;
        text-decoration: none;
        font-size: 0.8vw;
        font-weight: 600;
        color: #ffffff;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .share-modal-btn:hover {
        transform: translateY(-0.15vw);
        opacity: 0.9;
        box-shadow: 0 0.3vw 0.8vw rgba(0, 0, 0, 0.15);
    }

    .share-modal-btn.facebook { background: #1877f2; }
    .share-modal-btn.twitter { background: #000000; }
    .share-modal-btn.whatsapp { background: #25d366; }
    .share-modal-btn.telegram { background: #0088cc; }
    .share-modal-btn.copy { background: #64748b; }

    .share-modal-btn iconify-icon {
        font-size: 1.2vw;
    }


    /* ============================================
       SIDEBAR
       ============================================ */
    .article-sidebar {
        min-width: 0;
    }

    .sidebar-widget {
        background: #ffffff;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.8vw;
        padding: 1.2vw;
        margin-bottom: 1.5vw;
    }

    .sidebar-widget .widget-title {
        font-size: 0.9vw;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 0.8vw 0;
        padding-bottom: 0.6vw;
        border-bottom: 0.15vw solid #076694;
        display: flex;
        align-items: center;
        gap: 0.4vw;
    }

    .sidebar-widget .widget-title iconify-icon {
        color: #076694;
        font-size: 1.1vw;
    }

    .widget-search-form {
        display: flex;
        gap: 0.5vw;
    }

    .widget-search-form input {
        flex: 1;
        padding: 0.4vw 0.8vw;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.4vw;
        font-size: 0.75vw;
        outline: none;
        transition: border-color 0.2s;
    }

    .widget-search-form input:focus {
        border-color: #076694;
        box-shadow: 0 0 0 0.15vw rgba(7, 102, 148, 0.1);
    }

    .widget-search-form button {
        padding: 0.4vw 1vw;
        background: #076694;
        color: #ffffff;
        border: none;
        border-radius: 0.4vw;
        font-size: 0.75vw;
        cursor: pointer;
        transition: background 0.2s;
    }

    .widget-search-form button:hover {
        background: #055a7a;
    }

    .category-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .category-list li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.4vw 0;
        border-bottom: 0.05vw solid #f1f5f9;
    }

    .category-list li:last-child {
        border-bottom: none;
    }

    .category-list li a {
        color: #475569;
        text-decoration: none;
        font-size: 0.75vw;
        transition: color 0.2s;
    }

    .category-list li a:hover {
        color: #076694;
    }

    .category-list li .count {
        font-size: 0.6vw;
        color: #94a3b8;
        background: #f1f5f9;
        padding: 0.1vw 0.5vw;
        border-radius: 100vw;
    }

    .widget-article-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .widget-article-list li {
        display: flex;
        gap: 0.8vw;
        padding: 0.6vw 0;
        border-bottom: 0.05vw solid #f1f5f9;
    }

    .widget-article-list li:last-child {
        border-bottom: none;
    }

    .widget-article-list .widget-article-image {
        width: 4.5vw;
        height: 4.5vw;
        border-radius: 0.4vw;
        overflow: hidden;
        flex-shrink: 0;
        background: #f1f5f9;
    }

    .widget-article-list .widget-article-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .widget-article-list .widget-article-image .no-image {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        color: #cbd5e1;
        font-size: 1.5vw;
    }

    .widget-article-list .widget-article-info {
        flex: 1;
        min-width: 0;
    }

    .widget-article-list .widget-article-info h4 {
        font-size: 0.75vw;
        font-weight: 600;
        color: #0f172a;
        margin: 0 0 0.2vw 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.3;
    }

    .widget-article-list .widget-article-info h4 a {
        color: #0f172a;
        text-decoration: none;
        transition: color 0.2s;
    }

    .widget-article-list .widget-article-info h4 a:hover {
        color: #076694;
    }

    .widget-article-list .widget-article-info .widget-article-meta {
        font-size: 0.6vw;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 0.4vw;
    }

    .widget-article-list .widget-article-info .widget-article-meta iconify-icon {
        font-size: 0.65vw;
    }

    .popular-list {
        list-style: none;
        padding: 0;
        margin: 0;
        counter-reset: popular-counter;
    }

    .popular-list li {
        counter-increment: popular-counter;
        display: flex;
        gap: 0.8vw;
        padding: 0.5vw 0;
        border-bottom: 0.05vw solid #f1f5f9;
        align-items: center;
    }

    .popular-list li:last-child {
        border-bottom: none;
    }

    .popular-list .popular-number {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 1.8vw;
        height: 1.8vw;
        border-radius: 50%;
        background: #f1f5f9;
        color: #475569;
        font-size: 0.65vw;
        font-weight: 700;
        flex-shrink: 0;
    }

    .popular-list li:nth-child(1) .popular-number {
        background: #f59e0b;
        color: #ffffff;
    }
    .popular-list li:nth-child(2) .popular-number {
        background: #94a3b8;
        color: #ffffff;
    }
    .popular-list li:nth-child(3) .popular-number {
        background: #cd7f32;
        color: #ffffff;
    }

    .popular-list .popular-info {
        flex: 1;
        min-width: 0;
    }

    .popular-list .popular-info h4 {
        font-size: 0.75vw;
        font-weight: 600;
        color: #0f172a;
        margin: 0 0 0.1vw 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.3;
    }

    .popular-list .popular-info h4 a {
        color: #0f172a;
        text-decoration: none;
        transition: color 0.2s;
    }

    .popular-list .popular-info h4 a:hover {
        color: #076694;
    }

    .popular-list .popular-info .popular-views {
        font-size: 0.6vw;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 0.2vw;
    }

    .recommendation-badge {
        display: inline-block;
        padding: 0.1vw 0.5vw;
        border-radius: 0.2vw;
        font-size: 0.5vw;
        font-weight: 600;
        color: #ffffff;
        background: #8b5cf6;
        margin-left: 0.3vw;
        vertical-align: middle;
    }

    /* ============================================
       KOMENTAR - DI PALING BAWAH
       ============================================ */
    .comment-section {
        margin-top: 2.5vw;
        padding-top: 1.5vw;
        border-top: 0.15vw solid #e2e8f0;
    }

    .comment-section-title {
        font-size: 1.1vw;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 1.2vw;
        display: flex;
        align-items: center;
        gap: 0.5vw;
    }

    .comment-form-wrapper {
        margin-bottom: 1.5vw;
    }

    .comment-form-input-wrapper {
        display: flex;
        gap: 0.5vw;
        align-items: flex-end;
    }

    .comment-form-input-wrapper textarea {
        flex: 1;
        padding: 0.6vw 0.8vw;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.5vw;
        font-size: 0.8vw;
        resize: vertical;
        min-height: 4vw;
        transition: border-color 0.3s ease;
        font-family: inherit;
    }

    .comment-form-input-wrapper textarea:focus {
        outline: none;
        border-color: #076694;
        box-shadow: 0 0 0 0.15vw rgba(7, 102, 148, 0.1);
    }

    .comment-submit-btn {
        display: flex;
        align-items: center;
        gap: 0.3vw;
        padding: 0.6vw 1.2vw;
        background: #076694;
        color: #ffffff;
        border: none;
        border-radius: 0.5vw;
        font-size: 0.8vw;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s ease;
        white-space: nowrap;
        height: fit-content;
    }

    .comment-submit-btn:hover {
        background: #055a7a;
    }

    .comment-submit-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .comment-reply-indicator {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.4vw 0.8vw;
        background: #f1f5f9;
        border-radius: 0.4vw;
        font-size: 0.75vw;
        color: #475569;
        margin-top: 0.4vw;
    }

    .comment-reply-indicator strong {
        color: #076694;
    }

    .cancel-reply-btn {
        background: none;
        border: none;
        color: #ef4444;
        cursor: pointer;
        font-size: 0.75vw;
        font-weight: 600;
    }

    .comment-login-required {
        padding: 1vw;
        background: #f8fafc;
        border-radius: 0.5vw;
        border: 0.1vw dashed #e2e8f0;
        text-align: center;
    }

    .comment-login-required p {
        font-size: 0.85vw;
        color: #475569;
    }

    .comment-login-required .login-link {
        color: #076694;
        font-weight: 600;
        text-decoration: none;
    }

    .comment-login-required .login-link:hover {
        text-decoration: underline;
    }

    .comment-list {
        margin-top: 0.5vw;
    }

    .comment-loading {
        text-align: center;
        padding: 2vw;
        color: #94a3b8;
        font-size: 0.8vw;
    }

    .loading-spinner {
        display: inline-block;
        width: 1.5vw;
        height: 1.5vw;
        border: 0.2vw solid #e2e8f0;
        border-top-color: #076694;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
        margin-right: 0.5vw;
        vertical-align: middle;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .comment-item {
        padding: 0.8vw 0;
        border-bottom: 0.05vw solid #f1f5f9;
    }

    .comment-item:last-child {
        border-bottom: none;
    }

    .comment-header {
        display: flex;
        align-items: center;
        gap: 0.6vw;
        margin-bottom: 0.3vw;
    }

    .comment-delete-btn {
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        font-size: 0.7vw;
        padding: 0.1vw 0.3vw;
        transition: all 0.3s ease;
        margin-left: auto;
        border-radius: 0.2vw;
        line-height: 1;
    }

    .comment-delete-btn:hover {
        color: #ef4444;
        background: #fef2f2;
    }

    .comment-avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2.2vw;
        height: 2.2vw;
        border-radius: 50%;
        background: #076694;
        color: #ffffff;
        font-size: 0.7vw;
        font-weight: 700;
        flex-shrink: 0;
    }

    .comment-user {
        font-weight: 600;
        font-size: 0.8vw;
        color: #0f172a;
    }

    .comment-time {
        font-size: 0.65vw;
        color: #94a3b8;
    }

    .comment-content {
        font-size: 0.8vw;
        color: #475569;
        line-height: 1.6;
        margin-bottom: 0.3vw;
        margin-left: 2.8vw;
    }

    .comment-actions {
        display: flex;
        gap: 1vw;
        margin-left: 2.8vw;
    }

    .comment-actions button {
        background: none;
        border: none;
        font-size: 0.65vw;
        color: #94a3b8;
        cursor: pointer;
        transition: color 0.3s ease;
    }

    .comment-actions button:hover {
        color: #076694;
    }

    .comment-reply {
        margin-left: 2.8vw;
        padding-left: 1vw;
    }

    .comment-reply .comment-item {
        border-bottom: none;
        padding: 0.5vw 0;
    }

    .comment-reply .comment-item:first-child {
        padding-top: 0.5vw;
    }

    @keyframes replySlideIn {
        from {
            opacity: 0;
            transform: translateX(-1vw);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .comment-reply .comment-item:first-child {
        animation: replySlideIn 0.3s ease forwards;
    }

    .comment-empty {
        text-align: center;
        padding: 2vw;
        color: #94a3b8;
        font-size: 0.8vw;
    }

    .comment-empty iconify-icon {
        font-size: 2vw;
        display: block;
        margin-bottom: 0.5vw;
    }

    /* ============================================
       TOAST NOTIFICATION
       ============================================ */
    .custom-toast {
        position: fixed;
        bottom: 2vw;
        right: 2vw;
        padding: 0.8vw 1.5vw;
        border-radius: 0.5vw;
        font-size: 0.85vw;
        z-index: 99999;
        box-shadow: 0 0.3vw 1vw rgba(0, 0, 0, 0.15);
        display: flex;
        align-items: center;
        gap: 0.6vw;
        animation: slideUp 0.3s ease;
        max-width: 25vw;
        color: #ffffff;
    }

    .custom-toast-success { background: #10b981; }
    .custom-toast-error { background: #ef4444; }
    .custom-toast-warning { background: #f59e0b; }
    .custom-toast-info { background: #3b82f6; }

    .custom-toast .custom-toast-close {
        cursor: pointer;
        opacity: 0.7;
        font-size: 1vw;
        margin-left: 0.5vw;
    }

    .custom-toast .custom-toast-close:hover {
        opacity: 1;
    }

    .custom-toast.hide {
        animation: slideDown 0.3s ease forwards;
    }

    @keyframes slideUp {
        from { transform: translateY(2vw); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    @keyframes slideDown {
        from { transform: translateY(0); opacity: 1; }
        to { transform: translateY(2vw); opacity: 0; }
    }

    .comment-reply {
        margin-top: 0.3vw;
        padding-left: 0.5vw;
    }

    .comment-reply .comment-item {
        padding: 0.5vw 0;
        border-bottom: 0.05vw solid #f1f5f9;
    }

    .comment-reply .comment-item:last-child {
        border-bottom: none;
    }

    .comment-reply .comment-header .comment-avatar {
        width: 1.8vw;
        height: 1.8vw;
        font-size: 0.6vw;
    }

    .comment-reply .comment-user {
        font-size: 0.75vw;
    }

    .comment-reply .comment-time {
        font-size: 0.6vw;
    }

    .comment-reply .comment-content {
        font-size: 0.75vw;
        margin-left: 2.5vw;
    }

    .comment-reply .comment-actions {
        margin-left: 2.5vw;
    }

    .comment-reply .comment-actions button {
        font-size: 0.6vw;
    }

    /* 🔥 NESTED REPLY - LEVEL LEBIH DALAM */
    .comment-reply .comment-reply {
        margin-left: 1.5vw !important;
        padding-left: 0.3vw;
    }

    .comment-reply .comment-reply .comment-item {
        border-left: 0.15vw solid #e2e8f0;
        padding-left: 0.8vw;
    }
    @media (max-width: 768px) {
        .article-detail-wrapper {
            width: 100%;
            max-width: 100%;
            grid-template-columns: 1fr;
            padding: 0vw 0vw 0vw;
            gap: 2vw;
        }

        /* ============================================ */
        /* HEADER */
        /* ============================================ */
        .article-detail-header {
            padding: 4vw 4vw;
            padding-bottom: 0;
            margin-bottom: 0;
        }

        .article-detail-header .article-back {
            display: inline-flex;
            align-items: center;
            gap: 0.8vw;
            color: #076694;
            text-decoration: none;
            font-size: 2.8vw;
            margin-bottom: 1.5vw;
            transition: color 0.2s;
            font-weight: 500;
        }

        .article-detail-header h1 {
            font-size: 5vw;
            font-weight: 600;
            color: #0f172a;
            margin: 1.5vw 0;
            line-height: 1.2;
        }

        .article-detail-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 1.5vw;
            margin-top: 4.5vw;
            font-size: 2.8vw;
            color: #94a3b8;
            padding-bottom: 2vw;
            border-bottom: none;
        }

        .article-detail-meta .meta-item {
            display: flex;
            align-items: center;
            gap: 1vw;
        }

        .article-detail-meta .meta-item iconify-icon {
            font-size: 3vw;
        }

        .article-detail-meta .meta-divider {
            width: 0.1vw;
            height: 3vw;
            background: #e2e8f0;
        }

        /* ============================================ */
        /* GAMBAR */
        /* ============================================ */
        .article-detail-image {
            aspect-ratio: 16/9;
            border-radius: 0;
            margin: 0;
            width: 100%;
        }

        .article-detail-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .article-detail-image .image-placeholder {
            font-size: 8vw;
        }

        /* ============================================ */
        /* LIKE & SHARE */
        /* ============================================ */
        .article-interaction-section {
            margin: 0;
            padding: 5vw 4vw;
            padding-bottom: 4.5vw;
            border-bottom: 0.1vw solid #e2e8f0;
            border-top: 0.1vw solid #e2e8f0;
        }

        .article-actions {
            flex-direction: row;
            gap: 2vw;
            align-items: stretch;
        }

        .action-group {
            display: flex;
            align-items: center;
            gap: 1.8vw;
            justify-content: center;
        }

        .action-btn {
            display: flex;
            align-items: center;
            gap: 1.2vw;
            padding: 1.8vw 4vw;
            border: 0.15vw solid #e2e8f0;
            border-radius: 2vw;
            background: #ffffff;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 3vw;
            color: #475569;
            font-family: inherit;
        }

        .action-btn .action-icon {
            font-size: 3.5vw;
        }

        .action-btn .action-count {
            font-size: 2.8vw;
        }

        .comment-count {
            display: flex;
            align-items: center;
            gap: 1vw;
            font-size: 3vw;
            color: #94a3b8;
            justify-content: center;
            padding: 0.5vw 0;
        }

        .comment-count iconify-icon {
            font-size: 3.5vw;
        }

        /* ============================================ */
        /* KONTEN ARTIKEL */
        /* ============================================ */
        .article-detail-content {
            font-size: 3.2vw;
            padding: 2vw 4vw;
            line-height: 1.6;
        }

        .article-detail-content h2 {
            font-size: 4.5vw;
            margin: 4vw 0 2vw;
            color: #0f172a;
            font-weight: 700;
        }

        .article-detail-content h3 {
            font-size: 4vw;
            margin: 3vw 0 1.5vw;
            font-weight: 600;
        }

        .article-detail-content h4 {
            font-size: 3.5vw;
            margin: 2.5vw 0 1.5vw;
            font-weight: 600;
        }

        .article-detail-content p {
            font-size: 3.2vw;
            margin-bottom: 2vw;
        }

        .article-detail-content ul,
        .article-detail-content ol {
            margin: 1.5vw 0 2vw 3vw;
        }

        .article-detail-content li {
            font-size: 3vw;
            margin-bottom: 1vw;
        }

        .article-detail-content img {
            border-radius: 1.5vw;
            margin: 2vw 0;
            width: 100%;
            height: auto;
        }

        .article-detail-content blockquote {
            border-left: 0.8vw solid #076694;
            padding: 2vw 3vw;
            margin: 2vw 0;
            background: #f8fafc;
            border-radius: 0 1.5vw 1.5vw 0;
            font-style: italic;
            color: #475569;
            font-size: 3vw;
        }

        .article-detail-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 2vw 0;
            font-size: 2.8vw;
            display: block;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .article-detail-content table th,
        .article-detail-content table td {
            padding: 1.5vw 2vw;
            border: 0.1vw solid #e2e8f0;
            text-align: left;
            font-size: 2.8vw;
        }

        /* ============================================ */
        /* TAGS */
        /* ============================================ */
        .article-detail-tags {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 1.8vw;
            margin: 0;
            padding: 3vw 4vw;
            padding-top: 3vw;
            border-top: 0.1vw solid #e2e8f0;
        }

        .article-detail-tags .tag-label {
            font-size: 3vw;
            font-weight: 600;
            color: #475569;
            margin-right: 1.3vw;
        }

        .article-detail-tags .tag {
            padding: 1vw 2.5vw;
            border-radius: 100vw;
            font-size: 2.8vw;
            color: #475569;
            background: #f1f5f9;
            border: 0.1vw solid #e2e8f0;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 0.5vw;
        }

        /* ============================================ */
        /* 🔥 KOMENTAR - KE BAWAH */
        /* ============================================ */
        .comment-section {
            padding: 4vw 4vw 8vw;
            margin-top: 0;
            padding-top: 4vw;
            border-top: 0.2vw solid #e2e8f0;
        }

        .comment-section-title {
            font-size: 4vw;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 3vw;
            display: flex;
            align-items: center;
            gap: 1.5vw;
        }

        .comment-section-title iconify-icon {
            font-size: 4.5vw;
        }

        .comment-form-wrapper {
            margin-bottom: 4vw;
        }

        .comment-form-input-wrapper {
            display: flex;
            flex-direction: column;
            gap: 2.5vw;
            align-items: stretch;
            width: 100%;
        }

        .comment-form-input-wrapper textarea {
            flex: 1;
            padding: 3vw 3.5vw;
            border: 0.15vw solid #e2e8f0;
            border-radius: 2vw;
            font-size: 3.2vw;
            resize: vertical;
            min-height: 12vw;
            transition: border-color 0.3s ease;
            font-family: inherit;
            width: 100%;
            background: #ffffff;
        }

        .comment-form-input-wrapper textarea:focus {
            outline: none;
            border-color: #076694;
            box-shadow: 0 0 0 0.3vw rgba(7, 102, 148, 0.1);
        }

        .comment-submit-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.3vw;
            padding: 2.5vw 4vw;
            background: #076694;
            color: #ffffff;
            border: none;
            border-radius: 2vw;
            font-size: 3.5vw;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
            white-space: nowrap;
            width: 100%;
            height: fit-content;
            font-family: inherit;
        }

        .comment-submit-btn:active {
            background: #055a7a;
            transform: scale(0.97);
        }

        .comment-submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .comment-submit-btn iconify-icon {
            font-size: 4vw;
        }

        .comment-reply-indicator {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.5vw 2.5vw;
            background: #f1f5f9;
            border-radius: 1.5vw;
            font-size: 2.8vw;
            color: #475569;
            margin-top: 1.5vw;
            flex-wrap: wrap;
            gap: 1vw;
        }

        .comment-reply-indicator strong {
            color: #076694;
        }

        .cancel-reply-btn {
            background: none;
            border: none;
            color: #ef4444;
            cursor: pointer;
            font-size: 2.8vw;
            font-weight: 600;
            padding: 0.5vw 1.5vw;
        }

        .cancel-reply-btn:active {
            opacity: 0.7;
        }

        .comment-login-required {
            padding: 3vw 4vw;
            background: #f8fafc;
            border-radius: 2vw;
            border: 0.1vw dashed #e2e8f0;
            text-align: center;
        }

        .comment-login-required p {
            font-size: 3vw;
            color: #475569;
        }

        .comment-login-required .login-link {
            color: #076694;
            font-weight: 600;
            text-decoration: none;
        }

        .comment-login-required .login-link:hover {
            text-decoration: underline;
        }

        .comment-login-required iconify-icon {
            font-size: 3.5vw;
            vertical-align: middle;
            margin-right: 1vw;
        }

        /* ============================================ */
        /* COMMENT LIST */
        /* ============================================ */
        .comment-list {
            margin-top: 4vw;
        }

        .comment-loading {
            text-align: center;
            padding: 4vw;
            color: #94a3b8;
            font-size: 3vw;
        }

        .loading-spinner {
            display: inline-block;
            width: 4vw;
            height: 4vw;
            border: 0.4vw solid #e2e8f0;
            border-top-color: #076694;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            margin-right: 1.5vw;
            vertical-align: middle;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .comment-empty {
            text-align: center;
            padding: 6vw 2vw;
            color: #94a3b8;
            font-size: 3.2vw;
        }

        .comment-empty iconify-icon {
            font-size: 6vw;
            display: block;
            margin-bottom: 2vw;
            color: #cbd5e1;
        }

        /* ============================================ */
        /* COMMENT ITEM - MOBILE */
        /* ============================================ */
        .comment-item {
            padding: 2.5vw 0;
            border-bottom: 0.1vw solid #f1f5f9;
            padding-left: 0;
        }

        .comment-item:last-child {
            border-bottom: none;
        }

        .comment-header {
            display: flex;
            align-items: center;
            gap: 2.5vw;
            margin-bottom: 1vw;
            flex-wrap: wrap;
        }

        .comment-avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 6vw;
            height: 6vw;
            border-radius: 50%;
            background: #076694;
            color: #ffffff;
            font-size: 2.8vw;
            font-weight: 700;
            flex-shrink: 0;
        }

        .comment-user {
            font-weight: 600;
            font-size: 3.2vw;
            color: #0f172a;
        }

        .comment-time {
            font-size: 2.5vw;
            color: #94a3b8;
        }

        .comment-delete-btn {
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 2.8vw;
            padding: 0.5vw 1vw;
            transition: all 0.3s ease;
            margin-left: auto;
            border-radius: 0.5vw;
            line-height: 1;
        }

        .comment-delete-btn:active {
            color: #ef4444;
            background: #fef2f2;
        }

        .comment-content {
            font-size: 3.2vw;
            color: #475569;
            line-height: 1.6;
            margin-bottom: 1vw;
            margin-left: 8.5vw;
            word-wrap: break-word;
        }

        .comment-actions {
            display: flex;
            gap: 2vw;
            margin-left: 8.5vw;
            padding-bottom: 0.5vw;
        }

        .comment-actions button {
            background: none;
            border: none;
            font-size: 2.8vw;
            color: #94a3b8;
            cursor: pointer;
            transition: color 0.3s ease;
            padding: 0.5vw 0;
        }

        .comment-actions button:active {
            color: #076694;
        }

        /* ============================================ */
        /* COMMENT REPLY - MOBILE */
        /* ============================================ */
        .comment-reply {
            margin-top: 1vw;
            padding-left: 0;
            margin-left: 0;
        }

        .comment-reply .comment-item {
            padding: 2vw 0;
            border-bottom: 0.05vw solid #f1f5f9;
            border-left: 0.3vw solid #e2e8f0;
            padding-left: 2vw;
            margin-left: 2vw;
        }

        .comment-reply .comment-item:last-child {
            border-bottom: none;
        }

        .comment-reply .comment-header .comment-avatar {
            width: 5vw;
            height: 5vw;
            font-size: 2.2vw;
        }

        .comment-reply .comment-user {
            font-size: 2.8vw;
        }

        .comment-reply .comment-time {
            font-size: 2.2vw;
        }

        .comment-reply .comment-content {
            font-size: 2.8vw;
            margin-left: 7.5vw;
        }

        .comment-reply .comment-actions {
            margin-left: 7.5vw;
        }

        .comment-reply .comment-actions button {
            font-size: 2.5vw;
        }

        /* 🔥 NESTED REPLY - LEVEL LEBIH DALAM */
        .comment-reply .comment-reply {
            margin-left: 2vw !important;
            padding-left: 1vw;
        }

        .comment-reply .comment-reply .comment-item {
            border-left: 0.3vw solid #e2e8f0;
            padding-left: 2vw;
        }

        /* ============================================ */
        /* SIDEBAR - HIDE / TAMPILKAN DI BAWAH */
        /* ============================================ */
        .article-sidebar {
            padding: 0 4vw 4vw;
            margin-top: 0;
        }

        .sidebar-widget {
            border-radius: 2vw;
            padding: 3vw 3.5vw;
            margin-bottom: 3vw;
        }

        .sidebar-widget .widget-title {
            font-size: 3.5vw;
            margin-bottom: 2.5vw;
            padding-bottom: 1.5vw;
        }

        .sidebar-widget .widget-title iconify-icon {
            font-size: 4vw;
        }

        .widget-search-form {
            gap: 1.5vw;
            flex-wrap: wrap;
        }

        .widget-search-form input {
            padding: 1.5vw 2.5vw;
            border-radius: 1.5vw;
            font-size: 3vw;
            min-width: 60%;
            flex: 1;
        }

        .widget-search-form button {
            padding: 1.5vw 3vw;
            border-radius: 1.5vw;
            font-size: 3vw;
        }

        .category-list li {
            padding: 1.2vw 0;
        }

        .category-list li a {
            font-size: 2.8vw;
        }

        .category-list li .count {
            font-size: 2.2vw;
            padding: 0.3vw 1.5vw;
            border-radius: 100vw;
        }

        .widget-article-list li {
            gap: 2vw;
            padding: 1.5vw 0;
        }

        .widget-article-list .widget-article-image {
            width: 12vw;
            height: 12vw;
            border-radius: 1.2vw;
            flex-shrink: 0;
        }

        .widget-article-list .widget-article-image .no-image {
            font-size: 4vw;
        }

        .widget-article-list .widget-article-info h4 {
            font-size: 2.8vw;
            margin: 0 0 0.5vw 0;
        }

        .widget-article-list .widget-article-info .widget-article-meta {
            font-size: 2.2vw;
            gap: 1vw;
        }

        .widget-article-list .widget-article-info .widget-article-meta iconify-icon {
            font-size: 2.4vw;
        }

        .recommendation-badge {
            padding: 0.2vw 1.2vw;
            border-radius: 0.5vw;
            font-size: 1.8vw;
            margin-left: 0.8vw;
            vertical-align: middle;
            display: inline-block;
        }

        /* Popular List */
        .popular-list li {
            gap: 2vw;
            padding: 1.2vw 0;
        }

        .popular-list .popular-number {
            width: 5vw;
            height: 5vw;
            font-size: 2.5vw;
            flex-shrink: 0;
        }

        .popular-list .popular-info h4 {
            font-size: 2.8vw;
        }

        .popular-list .popular-info .popular-views {
            font-size: 2.2vw;
            gap: 0.5vw;
        }

        .popular-list .popular-info .popular-views iconify-icon {
            font-size: 2.4vw;
        }

        /* ============================================ */
        /* SHARE MODAL - MOBILE */
        /* ============================================ */
        .share-modal-content {
            max-width: 85vw;
            padding: 5vw 4vw;
            border-radius: 2.5vw;
            margin: 0 3vw;
        }

        .share-modal-close {
            top: 1.5vw;
            right: 2.5vw;
            font-size: 4vw;
        }

        .share-modal-content h3 {
            font-size: 3.5vw;
            margin-bottom: 3vw;
        }

        .share-modal-buttons {
            grid-template-columns: 1fr 1fr;
            gap: 1.5vw;
        }

        .share-modal-btn {
            padding: 2vw 2vw;
            border-radius: 1.5vw;
            font-size: 2.8vw;
            gap: 1vw;
        }

        .share-modal-btn iconify-icon {
            font-size: 3.5vw;
        }

        /* ============================================ */
        /* TOAST - MOBILE */
        /* ============================================ */
        .custom-toast {
            bottom: 6vw;
            right: 4vw;
            left: 4vw;
            padding: 2.5vw 3.5vw;
            font-size: 2.8vw;
            max-width: 100%;
            border-radius: 2vw;
            gap: 1.5vw;
        }

        .custom-toast .custom-toast-close {
            font-size: 2.5vw;
            padding: 0.5vw 1vw;
        }

        /* ============================================ */
        /* KEYFRAMES */
        /* ============================================ */
        @keyframes slideUp {
            from { transform: translateY(4vw); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes slideDown {
            from { transform: translateY(0); opacity: 1; }
            to { transform: translateY(4vw); opacity: 0; }
        }

        @keyframes replySlideIn {
            from {
                opacity: 0;
                transform: translateX(-2vw);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .comment-reply .comment-item:first-child {
            animation: replySlideIn 0.3s ease forwards;
        }
    }
</style>

{{-- ============================================ --}}
{{-- LAYOUT UTAMA 75% - 25% --}}
{{-- ============================================ --}}
<div class="article-detail-wrapper">
    {{-- ============================================ --}}
    {{-- KONTEN UTAMA (75%) --}}
    {{-- ============================================ --}}
    <div class="article-main">
        {{-- HEADER --}}
        <div class="article-detail-header">
            <a href="{{ route('customer.articles.index') }}" class="article-back">
                <iconify-icon icon="mdi:arrow-left"></iconify-icon>
                Kembali ke Artikel
            </a>

            <h1>{{ $article->title }}</h1>

            <div class="article-detail-meta">
                <span class="meta-item">
                    <iconify-icon icon="mdi:user"></iconify-icon>
                    {{ $article->author ?? 'Admin' }}
                </span>
                <span class="meta-divider"></span>
                <span class="meta-item">
                    <iconify-icon icon="lets-icons:date-fill"></iconify-icon>
                    {{ $article->formatted_published_at }}
                </span>
                <span class="meta-divider"></span>
                <span class="meta-item">
                    <iconify-icon icon="mdi:eye"></iconify-icon>
                    <span id="article-views-count">{{ number_format($article->views ?? 0) }}</span> dilihat
                </span>
                @if($article->articleCategory)
                    <span class="meta-divider"></span>
                    <span class="meta-item">
                        <iconify-icon icon="mdi:folder"></iconify-icon>
                        {{ $article->articleCategory->name }}
                    </span>
                @endif
            </div>
        </div>

        {{-- GAMBAR --}}
        @if($article->image)
            <div class="article-detail-image">
                <img src="{{ Storage::url($article->image) }}" alt="{{ $article->title }}">
            </div>
        @else
            <div class="article-detail-image">
                <div class="image-placeholder">
                    <iconify-icon icon="mdi:newspaper-variant-outline"></iconify-icon>
                </div>
            </div>
        @endif

        {{-- ============================================ --}}
        {{-- LIKE & SHARE - DI BAWAH GAMBAR --}}
        {{-- ============================================ --}}
        <div class="article-interaction-section">
            <div class="article-actions">
                <div class="action-group">
                    {{-- LIKE BUTTON --}}
                    <button class="action-btn like-btn {{ $isLiked ? 'liked' : '' }}" 
                            data-article-id="{{ $article->id }}"
                            onclick="toggleLike({{ $article->id }})">
                        <span class="action-icon">
                            <iconify-icon icon="{{ $isLiked ? 'mdi:heart' : 'mdi:heart-outline' }}"></iconify-icon>
                        </span>
                        <span class="action-text">Suka</span>
                        <span class="action-count" id="likes-count">{{ number_format($article->likes_count) }}</span>
                    </button>
                    
                    {{-- SHARE BUTTON --}}
                    <button class="action-btn share-btn" onclick="openShareModal()">
                        <span class="action-icon">
                            <iconify-icon icon="mdi:share-variant"></iconify-icon>
                        </span>
                        <span class="action-text">Bagikan</span>
                    </button>
                </div>
                
                <div class="action-group">
                    {{-- COMMENT COUNT --}}
                    <span class="comment-count">
                        <iconify-icon icon="mdi:comment-outline"></iconify-icon>
                        <span id="comments-count">{{ number_format($article->comments_count) }}</span> Komentar
                    </span>
                </div>
            </div>
        </div>

        {{-- SHARE MODAL --}}
        

        {{-- ============================================ --}}
        {{-- KONTEN ARTIKEL --}}
        {{-- ============================================ --}}
        <div class="article-detail-content">
            {!! preg_replace('/<p>\s*(&nbsp;|\s)*\s*<\/p>/i', '', $article->content) !!}
        </div>

        {{-- ============================================ --}}
        {{-- TAGS --}}
        {{-- ============================================ --}}
        @if($article->tags && count($article->tags) > 0)
            <div class="article-detail-tags">
                <span class="tag-label">Tags:</span>
                @foreach($article->tags as $tag)
                    <a href="{{ route('customer.articles.index', ['tag' => $tag]) }}" class="tag">#{{ $tag }}</a>
                @endforeach
            </div>
        @endif

        {{-- ============================================ --}}
        {{-- KOMENTAR - DI PALING BAWAH --}}
        {{-- ============================================ --}}
        <div class="comment-section" id="comment-section">
            <h3 class="comment-section-title">
                <iconify-icon icon="mdi:comment-outline"></iconify-icon>
                Komentar (<span id="comment-count-display">{{ number_format($article->comments_count) }}</span>)
            </h3>

            {{-- COMMENT FORM --}}
            <div class="comment-form-wrapper">
                @if($isLoggedIn)
                    <form id="comment-form" onsubmit="submitComment(event)">
                        @csrf
                        <input type="hidden" id="comment-article-id" value="{{ $article->id }}">
                        <input type="hidden" id="comment-parent-id" value="">
                        <div class="comment-form-input-wrapper">
                            <textarea id="comment-content" rows="3" placeholder="Tulis komentar Anda..." required></textarea>
                            <button type="submit" class="comment-submit-btn">
                                <iconify-icon icon="mdi:send"></iconify-icon>
                                Kirim
                            </button>
                        </div>
                        <div id="comment-reply-indicator" style="display:none;" class="comment-reply-indicator">
                            <span>Membalas: <strong id="reply-to-name"></strong></span>
                            <button type="button" onclick="cancelReply()" class="cancel-reply-btn">Batal</button>
                        </div>
                    </form>
                @else
                    <div class="comment-login-required">
                        <p>
                            <iconify-icon icon="mdi:login"></iconify-icon>
                            Silakan <a href="{{ route('customer.login') }}" class="login-link">login</a> untuk memberikan komentar.
                        </p>
                    </div>
                @endif
            </div>

            {{-- COMMENT LIST --}}
            <div class="comment-list" id="comment-list">
                <div class="comment-loading" id="comment-loading">
                    <span class="loading-spinner"></span>
                    Memuat komentar...
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- SIDEBAR (25%) --}}
    {{-- ============================================ --}}
    <div class="article-sidebar">
        {{-- WIDGET 1: SEARCH --}}
        <div class="sidebar-widget">
            <div class="widget-title">
                <iconify-icon icon="mdi:search"></iconify-icon>
                Cari Artikel
            </div>
            <form action="{{ route('customer.articles.index') }}" method="GET" class="widget-search-form">
                <input type="text" name="search" placeholder="Cari artikel..." value="{{ request('search') }}">
                <button type="submit">
                    <iconify-icon icon="mdi:search"></iconify-icon>
                </button>
            </form>
        </div>

        {{-- WIDGET 2: KATEGORI --}}
        @if($categories->isNotEmpty())
            <div class="sidebar-widget">
                <div class="widget-title">
                    <iconify-icon icon="mdi:folder"></iconify-icon>
                    Kategori
                </div>
                <ul class="category-list">
                    @foreach($categories as $category)
                        <li>
                            <a href="{{ route('customer.articles.index', ['category' => $category->id]) }}">
                                {{ $category->name }}
                            </a>
                            <span class="count">{{ $category->articles_count }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- WIDGET 3: ARTIKEL POPULER --}}
        @if($popularArticles->isNotEmpty())
            <div class="sidebar-widget">
                <div class="widget-title">
                    <iconify-icon icon="mdi:fire"></iconify-icon>
                    Populer
                </div>
                <ol class="popular-list">
                    @foreach($popularArticles as $popular)
                        <li>
                            <span class="popular-number">{{ $loop->iteration }}</span>
                            <div class="popular-info">
                                <h4>
                                    <a href="{{ route('customer.articles.show', $popular->slug) }}">
                                        {{ $popular->title }}
                                    </a>
                                </h4>
                                <span class="popular-views">
                                    <iconify-icon icon="mdi:eye"></iconify-icon>
                                    {{ number_format($popular->views ?? 0) }} dilihat
                                </span>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        @endif

        {{-- WIDGET 4: REKOMENDASI ARTIKEL LAIN --}}
        @if($recommendedArticles->isNotEmpty() || $tagRelatedArticles->isNotEmpty())
            <div class="sidebar-widget">
                <div class="widget-title">
                    <iconify-icon icon="mdi:star"></iconify-icon>
                    Rekomendasi Untukmu
                </div>
                <ul class="widget-article-list">
                    @if($tagRelatedArticles->isNotEmpty())
                        @foreach($tagRelatedArticles as $related)
                            <li>
                                <div class="widget-article-image">
                                    @if($related->image)
                                        <img src="{{ Storage::url($related->image) }}" alt="{{ $related->title }}">
                                    @else
                                        <div class="no-image">
                                            <iconify-icon icon="mdi:newspaper-variant-outline"></iconify-icon>
                                        </div>
                                    @endif
                                </div>
                                <div class="widget-article-info">
                                    <h4>
                                        <a href="{{ route('customer.articles.show', $related->slug) }}">
                                            {{ $related->title }}
                                        </a>
                                        <span class="recommendation-badge">Tag</span>
                                    </h4>
                                    <span class="widget-article-meta">
                                        <iconify-icon icon="lets-icons:date-fill"></iconify-icon>
                                        {{ $related->formatted_published_at }}
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    @endif

                    @if($recommendedArticles->isNotEmpty())
                        @foreach($recommendedArticles as $recommended)
                            <li>
                                <div class="widget-article-image">
                                    @if($recommended->image)
                                        <img src="{{ Storage::url($recommended->image) }}" alt="{{ $recommended->title }}">
                                    @else
                                        <div class="no-image">
                                            <iconify-icon icon="mdi:newspaper-variant-outline"></iconify-icon>
                                        </div>
                                    @endif
                                </div>
                                <div class="widget-article-info">
                                    <h4>
                                        <a href="{{ route('customer.articles.show', $recommended->slug) }}">
                                            {{ $recommended->title }}
                                        </a>
                                        <span class="recommendation-badge">Rekomendasi</span>
                                    </h4>
                                    <span class="widget-article-meta">
                                        <iconify-icon icon="lets-icons:date-fill"></iconify-icon>
                                        {{ $recommended->formatted_published_at }}
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>
        @endif
    </div>
</div>

<div id="share-modal" class="share-modal">
    <div class="share-modal-content">
        <button class="share-modal-close" onclick="closeShareModal()">✕</button>
        <h3>Bagikan Artikel</h3>
        <div class="share-modal-buttons">
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" 
               target="_blank" class="share-modal-btn facebook" onclick="closeShareModal()">
                <iconify-icon icon="mdi:facebook"></iconify-icon>
                Facebook
            </a>
            <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($article->title) }}" 
               target="_blank" class="share-modal-btn twitter" onclick="closeShareModal()">
                <iconify-icon icon="mdi:twitter"></iconify-icon>
                Twitter
            </a>
            <a href="https://api.whatsapp.com/send?text={{ urlencode($article->title . ' - ' . url()->current()) }}" 
               target="_blank" class="share-modal-btn whatsapp" onclick="closeShareModal()">
                <iconify-icon icon="mdi:whatsapp"></iconify-icon>
                WhatsApp
            </a>
            <a href="https://t.me/share/url?url={{ urlencode(url()->current()) }}&text={{ urlencode($article->title) }}" 
               target="_blank" class="share-modal-btn telegram" onclick="closeShareModal()">
                <iconify-icon icon="mdi:telegram"></iconify-icon>
                Telegram
            </a>
            <button onclick="copyLink()" class="share-modal-btn copy">
                <iconify-icon icon="mdi:content-copy"></iconify-icon>
                Salin Link
            </button>
        </div>
    </div>
</div>

{{-- ============================================ --}}
{{-- JAVASCRIPT --}}
{{-- ============================================ --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const articleId = {{ $article->id }};
    const storageKey = 'article_viewed_' + articleId;
    const viewsKey = 'article_views_count_' + articleId;
    
    const serverViews = {{ $article->views ?? 0 }};
    
    const viewsSpan = document.getElementById('article-views-count');
    if (viewsSpan) {
        viewsSpan.textContent = new Intl.NumberFormat('id-ID').format(serverViews);
    }
    
    const alreadyViewed = localStorage.getItem(storageKey);
    
    if (!alreadyViewed) {
        localStorage.setItem(storageKey, 'true');
        
        fetch('{{ route("customer.articles.record-view") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ article_id: articleId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('✅ Views recorded:', data.views);
                localStorage.setItem(viewsKey, data.views);
                if (viewsSpan) {
                    viewsSpan.textContent = new Intl.NumberFormat('id-ID').format(data.views);
                }
            }
        })
        .catch(error => {
            console.error('❌ Error recording view:', error);
        });
    } else {
        console.log('ℹ️ Article already viewed, skip counting');
        if (viewsSpan) {
            viewsSpan.textContent = new Intl.NumberFormat('id-ID').format(serverViews);
        }
    }
    
    console.log('📊 Article view tracking initialized');
});

function checkLoginStatus() {
    return new Promise((resolve) => {
        // 🔥 CEK META TAG USER LOGIN (dari semua guard)
        const metaLoggedIn = document.querySelector('meta[name="customer-logged-in"]');
        const metaRole = document.querySelector('meta[name="user-role"]');
        
        if (metaLoggedIn) {
            const isLoggedIn = metaLoggedIn.getAttribute('content') === 'true';
            const role = metaRole ? metaRole.getAttribute('content') : 'guest';
            
            // 🔥 ADMIN DIANGGAP LOGIN (bisa like & comment)
            if (isLoggedIn || role === 'admin') {
                resolve(true);
                return;
            }
            resolve(isLoggedIn);
            return;
        }
        
        // 🔥 FALLBACK: CEK MELALUI AJAX
        fetch('/api/check-login', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            resolve(data.logged_in === true);
        })
        .catch(() => {
            const stored = localStorage.getItem('user_logged_in');
            resolve(stored === 'true');
        });
    });
}

function openLoginPopup(action, callback) {
    // Redirect ke halaman login dengan return URL
    const currentUrl = encodeURIComponent(window.location.href);
    const loginUrl = '{{ route("customer.login") }}?redirect=' + currentUrl + '&action=' + action;
    
    // Simpan callback untuk dieksekusi setelah login
    if (callback) {
        window._loginCallback = callback;
    }
    
    // Redirect ke login
    window.location.href = loginUrl;
}


// ============================================
// LIKE FUNCTION
// ============================================

function toggleLike(articleId) {
    const btn = document.querySelector('.like-btn');
    const countSpan = document.getElementById('likes-count');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // 🔥 CEK LOGIN STATUS
    checkLoginStatus().then(isLoggedIn => {
        if (!isLoggedIn) {
            window._pendingArticleId = articleId;
            window._pendingAction = 'like';
            openLoginPopup('like', function() {
                if (window._pendingArticleId) {
                    toggleLikeDirect(window._pendingArticleId);
                    window._pendingArticleId = null;
                }
            });
            return;
        }

        toggleLikeDirect(articleId);
    });
}

function toggleLikeDirect(articleId) {
    const btn = document.querySelector('.like-btn');
    const countSpan = document.getElementById('likes-count');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    btn.disabled = true;
    btn.style.opacity = '0.6';

    fetch('{{ route("customer.articles.toggle-like") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ article_id: articleId })
    })
    .then(response => {
        if (response.status === 401) {
            showToast('Silakan login terlebih dahulu', 'warning');
            setTimeout(() => {
                window.location.href = '{{ route("customer.login") }}';
            }, 1500);
            throw new Error('Unauthorized');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const isLiked = data.liked;
            const count = data.likes_count;

            const icon = btn.querySelector('.action-icon iconify-icon');
            if (isLiked) {
                icon.setAttribute('icon', 'mdi:heart');
                btn.classList.add('liked');
            } else {
                icon.setAttribute('icon', 'mdi:heart-outline');
                btn.classList.remove('liked');
            }

            countSpan.textContent = new Intl.NumberFormat('id-ID').format(count);
            showToast(data.message, 'success');
        }
    })
    .catch(error => {
        if (error.message !== 'Unauthorized') {
            showToast('Terjadi kesalahan', 'error');
        }
    })
    .finally(() => {
        btn.disabled = false;
        btn.style.opacity = '1';
    });
}

// ============================================
// SHARE FUNCTIONS
// ============================================

function openShareModal() {
    document.getElementById('share-modal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeShareModal() {
    document.getElementById('share-modal').classList.remove('active');
    document.body.style.overflow = '';
}

function copyLink() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        showToast('Link berhasil disalin!', 'success');
        closeShareModal();
    }).catch(() => {
        const input = document.createElement('input');
        input.value = url;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        input.remove();
        showToast('Link berhasil disalin!', 'success');
        closeShareModal();
    });
}

document.addEventListener('click', function(e) {
    const modal = document.getElementById('share-modal');
    if (e.target === modal) {
        closeShareModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeShareModal();
    }
});

// ============================================
// COMMENT FUNCTIONS
// ============================================

let replyToId = null;
let replyToName = null;

function loadComments() {
    const articleId = {{ $article->id }};
    const loading = document.getElementById('comment-loading');
    const list = document.getElementById('comment-list');

    loading.style.display = 'block';

    fetch('{{ route("customer.articles.get-comments") }}?article_id=' + articleId, {
        headers: { 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        loading.style.display = 'none';
        if (data.success) {
            renderComments(data.comments);
        } else {
            list.innerHTML = `
                <div class="comment-empty">
                    <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                    <p>${data.message || 'Gagal memuat komentar'}</p>
                </div>
            `;
        }
    })
    .catch(() => {
        loading.style.display = 'none';
        list.innerHTML = `
            <div class="comment-empty">
                <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                <p>Gagal memuat komentar. Silakan refresh halaman.</p>
            </div>
        `;
    });
}


function renderComments(comments) {
    const list = document.getElementById('comment-list');
    
    if (!comments || comments.length === 0) {
        list.innerHTML = `
            <div class="comment-empty">
                <p>Belum ada komentar. Jadilah yang pertama!</p>
            </div>
        `;
        return;
    }

    // 🔥 AMBIL USER ROLE DARI META
    const metaRole = document.querySelector('meta[name="user-role"]');
    const userRole = metaRole ? metaRole.getAttribute('content') : 'guest';
    const metaUserId = document.querySelector('meta[name="user-id"]');
    const currentUserId = metaUserId ? parseInt(metaUserId.getAttribute('content')) : 0;
    
    // 🔥 ADMIN BISA HAPUS SEMUA KOMENTAR
    const isAdmin = userRole === 'admin';

    let html = '';
    comments.forEach(function(comment) {
        const isOwner = currentUserId === comment.user_id;
        const canDelete = isOwner || isAdmin;
        
        html += `
            <div class="comment-item" id="comment-${comment.id}">
                <div class="comment-header">
                    <div class="comment-avatar">${comment.user_avatar || 'U'}</div>
                    <span class="comment-user">${comment.user_name || 'User'}</span>
                    <span class="comment-time">${comment.created_at || 'Baru saja'}</span>
                    ${canDelete ? `<button class="comment-delete-btn" onclick="deleteComment(${comment.id})" title="Hapus komentar">✕</button>` : ''}
                </div>
                <div class="comment-content">${escapeHtml(comment.content)}</div>
                <div class="comment-actions">
                    <button onclick="setReply(${comment.id}, '${escapeHtml(comment.user_name || 'User')}')">Balas</button>
                </div>
                ${comment.replies && comment.replies.length > 0 ? renderReplies(comment.replies, 1) : ''}
            </div>
        `;
    });

    list.innerHTML = html;
}

function renderReplies(replies, level = 1) {
    if (!replies || replies.length === 0) return '';

    // 🔥 AMBIL USER ROLE DARI META
    const metaRole = document.querySelector('meta[name="user-role"]');
    const userRole = metaRole ? metaRole.getAttribute('content') : 'guest';
    const metaUserId = document.querySelector('meta[name="user-id"]');
    const currentUserId = metaUserId ? parseInt(metaUserId.getAttribute('content')) : 0;
    const isAdmin = userRole === 'admin';

    let html = `<div class="comment-reply" style="margin-left: ${level * 1.5}vw;">`;
    replies.forEach(function(reply) {
        const isOwner = currentUserId === reply.user_id;
        const canDelete = isOwner || isAdmin;
        
        const hasNestedReplies = reply.replies && reply.replies.length > 0;
        
        html += `
            <div class="comment-item" id="comment-${reply.id}" style="border-left: 0.15vw solid #e2e8f0; padding-left: 0.8vw;">
                <div class="comment-header">
                    <div class="comment-avatar">${reply.user_avatar || 'U'}</div>
                    <span class="comment-user">${reply.user_name || 'User'}</span>
                    <span class="comment-time">${reply.created_at || 'Baru saja'}</span>
                    ${canDelete ? `<button class="comment-delete-btn" onclick="deleteComment(${reply.id})" title="Hapus komentar">✕</button>` : ''}
                </div>
                <div class="comment-content">${escapeHtml(reply.content)}</div>
                <div class="comment-actions">
                    <button onclick="setReply(${reply.id}, '${escapeHtml(reply.user_name || 'User')}')">Balas</button>
                </div>
                ${hasNestedReplies ? renderReplies(reply.replies, level + 1) : ''}
            </div>
        `;
    });
    html += '</div>';

    return html;
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function setReply(commentId, userName) {
    replyToId = commentId;
    replyToName = userName;
    
    document.getElementById('comment-parent-id').value = commentId;
    document.getElementById('reply-to-name').textContent = userName;
    document.getElementById('comment-reply-indicator').style.display = 'flex';
    document.getElementById('comment-content').focus();
    
    // 🔥 SCROLL KE FORM REPLY
    const form = document.getElementById('comment-form');
    if (form) {
        setTimeout(() => {
            form.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 200);
    }
}

function cancelReply() {
    replyToId = null;
    replyToName = null;
    document.getElementById('comment-parent-id').value = '';
    document.getElementById('comment-reply-indicator').style.display = 'none';
}

function submitComment(event) {
    event.preventDefault();

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const articleId = {{ $article->id }};
    const content = document.getElementById('comment-content').value.trim();
    const parentId = document.getElementById('comment-parent-id').value;
    const submitBtn = document.querySelector('.comment-submit-btn');

    if (!content) {
        showToast('Komentar tidak boleh kosong', 'warning');
        return;
    }

    // 🔥 CEK LOGIN STATUS
    checkLoginStatus().then(isLoggedIn => {
        if (!isLoggedIn) {
            window._pendingArticleId = articleId;
            window._pendingContent = content;
            window._pendingParentId = parentId;
            window._pendingAction = 'comment';
            openLoginPopup('comment', function() {
                if (window._pendingArticleId) {
                    submitCommentDirect(
                        window._pendingArticleId,
                        window._pendingContent,
                        window._pendingParentId
                    );
                    window._pendingArticleId = null;
                    window._pendingContent = null;
                    window._pendingParentId = null;
                }
            });
            return;
        }

        // 🔥 KIRIM PARENT_ID KE DIRECT FUNCTION
        submitCommentDirect(articleId, content, parentId);
    });
}

function submitCommentDirect(articleId, content, parentId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const submitBtn = document.querySelector('.comment-submit-btn');

    submitBtn.disabled = true;
    submitBtn.innerHTML = '⏳ Mengirim...';

    fetch('{{ route("customer.articles.post-comment") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            article_id: articleId,
            content: content,
            parent_id: parentId || null
        })
    })
    .then(response => {
        if (response.status === 401) {
            showToast('Silakan login terlebih dahulu', 'warning');
            setTimeout(() => {
                window.location.href = '{{ route("customer.login") }}';
            }, 1500);
            throw new Error('Unauthorized');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // KOSONGKAN FORM
            document.getElementById('comment-content').value = '';
            cancelReply();
            
            // UPDATE COUNT
            const formattedCount = new Intl.NumberFormat('id-ID').format(data.comments_count || 0);
            document.getElementById('comments-count').textContent = formattedCount;
            document.getElementById('comment-count-display').textContent = formattedCount;
            
            // 🔥 TAMBAHKAN KOMENTAR ATAU REPLY KE TEMPAT YANG BENAR
            if (parentId) {
                // 🔥 INI ADALAH REPLY - TAMBAHKAN KE DALAM THREAD REPLY
                addReplyToThread(parentId, data.comment);
            } else {
                // 🔥 INI KOMENTAR BARU - TAMBAHKAN KE PALING ATAS
                addNewComment(data.comment);
            }
            
            showToast(data.message || 'Komentar berhasil ditambahkan!', 'success');
        } else {
            showToast(data.message || 'Gagal menambahkan komentar', 'error');
        }
    })
    .catch(error => {
        if (error.message !== 'Unauthorized') {
            console.error('Error:', error);
            showToast('Terjadi kesalahan, silakan coba lagi', 'error');
        }
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<iconify-icon icon="mdi:send"></iconify-icon> Kirim';
    });
}

function addNewComment(comment) {
    const list = document.getElementById('comment-list');
    
    // Hapus empty state jika ada
    const emptyState = list.querySelector('.comment-empty');
    if (emptyState) {
        list.innerHTML = '';
    }
    
    // Buat elemen comment baru
    const newComment = document.createElement('div');
    newComment.className = 'comment-item';
    newComment.id = 'comment-' + comment.id;
    newComment.innerHTML = `
        <div class="comment-header">
            <div class="comment-avatar">${comment.user_avatar || 'U'}</div>
            <span class="comment-user">${comment.user_name || 'User'}</span>
            <span class="comment-time">${comment.created_at || 'Baru saja'}</span>
        </div>
        <div class="comment-content">${escapeHtml(comment.content)}</div>
        <div class="comment-actions">
            <button onclick="setReply(${comment.id}, '${escapeHtml(comment.user_name || 'User')}')">Balas</button>
        </div>
        <div class="comment-reply" id="replies-for-${comment.id}"></div>
    `;
    
    // Tambahkan ke paling atas
    list.prepend(newComment);
}

function addReplyToThread(parentId, reply) {
    // 🔥 CARI ELEMEN INDUK KOMENTAR
    const parentComment = document.getElementById('comment-' + parentId);
    
    if (!parentComment) {
        // Jika parent tidak ditemukan, reload comments
        loadComments();
        return;
    }
    
    // 🔥 CARI ATAU BUAT CONTAINER REPLY
    let replyContainer = parentComment.querySelector('.comment-reply');
    if (!replyContainer) {
        // Buat container reply jika belum ada
        replyContainer = document.createElement('div');
        replyContainer.className = 'comment-reply';
        replyContainer.id = 'replies-for-' + parentId;
        parentComment.appendChild(replyContainer);
    }
    
    // 🔥 BUAT ELEMEN REPLY BARU
    const replyElement = document.createElement('div');
    replyElement.className = 'comment-item';
    replyElement.id = 'comment-' + reply.id;
    replyElement.style.cssText = 'border-left: 0.15vw solid #e2e8f0; padding-left: 0.8vw;';
    replyElement.innerHTML = `
        <div class="comment-header">
            <div class="comment-avatar" style="width: 1.8vw; height: 1.8vw; font-size: 0.6vw;">${reply.user_avatar || 'U'}</div>
            <span class="comment-user" style="font-size: 0.75vw;">${reply.user_name || 'User'}</span>
            <span class="comment-time" style="font-size: 0.6vw;">${reply.created_at || 'Baru saja'}</span>
        </div>
        <div class="comment-content" style="font-size: 0.75vw; margin-left: 2.5vw;">${escapeHtml(reply.content)}</div>
        <div class="comment-actions" style="margin-left: 2.5vw;">
            <button onclick="setReply(${reply.id}, '${escapeHtml(reply.user_name || 'User')}')" style="font-size: 0.6vw;">Balas</button>
        </div>
        <div class="comment-reply" id="replies-for-${reply.id}"></div>
    `;
    
    // 🔥 TAMBAHKAN REPLY KE DALAM CONTAINER
    // Tambahkan di awal (paling baru di atas)
    replyContainer.prepend(replyElement);
    
    // 🔥 UPDATE BORDER DAN STYLE UNTUK CONTAINER
    replyContainer.style.marginTop = '0.3vw';
    replyContainer.style.paddingLeft = '0.5vw';
    
    // 🔥 SCROLL KE REPLY YANG BARU
    setTimeout(() => {
        replyElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }, 300);
}

function deleteCommentDirect(commentId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    fetch('{{ route("customer.articles.delete-comment") }}', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            comment_id: commentId
        })
    })
    .then(response => {
        if (response.status === 401) {
            showToast('Silakan login terlebih dahulu', 'warning');
            setTimeout(() => {
                window.location.href = '{{ route("customer.login") }}';
            }, 1500);
            throw new Error('Unauthorized');
        }
        if (response.status === 403) {
            showToast('Anda tidak memiliki izin untuk menghapus komentar ini', 'error');
            throw new Error('Forbidden');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const commentElement = document.getElementById('comment-' + data.comment_id);
            if (commentElement) {
                commentElement.style.transition = 'all 0.3s ease';
                commentElement.style.opacity = '0';
                commentElement.style.transform = 'translateX(-20px)';
                setTimeout(function() {
                    commentElement.remove();
                    const list = document.getElementById('comment-list');
                    if (list && list.children.length === 0) {
                        list.innerHTML = `
                            <div class="comment-empty">
                                <p>Belum ada komentar. Jadilah yang pertama!</p>
                            </div>
                        `;
                    }
                }, 300);
            }
            
            const formattedCount = new Intl.NumberFormat('id-ID').format(data.comments_count || 0);
            document.getElementById('comments-count').textContent = formattedCount;
            document.getElementById('comment-count-display').textContent = formattedCount;
            
            showToast(data.message || 'Komentar berhasil dihapus', 'success');
        } else {
            showToast(data.message || 'Gagal menghapus komentar', 'error');
        }
    })
    .catch(error => {
        if (error.message !== 'Unauthorized' && error.message !== 'Forbidden') {
            console.error('Error:', error);
            showToast('Terjadi kesalahan, silakan coba lagi', 'error');
        }
    });
}

function deleteComment(commentId) {
    if (!confirm('Apakah Anda yakin ingin menghapus komentar ini?')) {
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // 🔥 CEK LOGIN STATUS
    checkLoginStatus().then(isLoggedIn => {
        if (!isLoggedIn) {
            showToast('Silakan login terlebih dahulu', 'warning');
            setTimeout(() => {
                window.location.href = '{{ route("customer.login") }}';
            }, 1500);
            return;
        }

        deleteCommentDirect(commentId);
    });
}

// ============================================
// TOAST FUNCTION
// ============================================

function showToast(message, type = 'info') {
    const oldToast = document.querySelector('.custom-toast');
    if (oldToast) oldToast.remove();

    const toast = document.createElement('div');
    toast.className = `custom-toast custom-toast-${type}`;
    
    const icons = {
        success: '✅',
        error: '❌',
        warning: '⚠️',
        info: 'ℹ️'
    };
    
    toast.innerHTML = `
        <span>${icons[type] || 'ℹ️'}</span>
        <span>${message}</span>
        <span class="custom-toast-close">×</span>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('hide');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
    
    toast.querySelector('.custom-toast-close').addEventListener('click', function() {
        toast.classList.add('hide');
        setTimeout(() => toast.remove(), 300);
    });
}

// ============================================
// INITIALIZATION
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    loadComments();
});
</script>

@endsection