@extends('layouts.account')

@section('title', $voucher->name . ' - Barokah Sport')
@section('page-title', 'Detail Voucher')
@section('page-subtitle', 'Informasi lengkap voucher')

@section('account-content')

<style>
    /* ============================================
       VOUCHER DETAIL STYLES
       ============================================ */
    .vd-wrapper {
        max-width: 42vw;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 1vw;
    }

    /* --------------------------------------------
       BACK LINK
       -------------------------------------------- */
    .vd-back {
        display: inline-flex;
        align-items: center;
        gap: 0.4vw;
        font-size: 0.85vw;
        color: #64748b;
        text-decoration: none;
        transition: color 0.2s ease;
        padding: 0.5vw 0;
        width: fit-content;
    }
    .vd-back:hover { color: rgb(102, 72, 9); }
    .vd-back iconify-icon {
        font-size: 1.1vw;
        transition: transform 0.2s ease;
    }
    .vd-back:hover iconify-icon { transform: translateX(-0.2vw); }

    /* --------------------------------------------
       CARD
       -------------------------------------------- */
    .vd-card {
        background: #ffffff;
        border: 0.1vw solid #e2e8f0;
        border-radius: 1.2vw;
        overflow: hidden;
        box-shadow: 0 0.2vw 0.8vw rgba(0, 0, 0, 0.04);
        transition: box-shadow 0.25s ease;
    }
    .vd-card:hover {
        box-shadow: 0 0.5vw 1.5vw rgba(0, 0, 0, 0.08);
    }

    /* --------------------------------------------
       HEADER (Gradient Emas)
       -------------------------------------------- */
    .vd-header {
        position: relative;
        background: linear-gradient(135deg, #FDDD57 0%, #ecbc42 50%, #d4a72e 100%);
        padding: 1.8vw;
        overflow: hidden;
    }

    .vd-header-deco-1,
    .vd-header-deco-2 {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.15);
        pointer-events: none;
    }
    .vd-header-deco-1 {
        top: -2vw;
        right: -2vw;
        width: 8vw;
        height: 8vw;
    }
    .vd-header-deco-2 {
        bottom: -1.5vw;
        left: -1.5vw;
        width: 6vw;
        height: 6vw;
        background: rgba(255, 255, 255, 0.1);
    }

    .vd-header-inner {
        position: relative;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1vw;
    }

    .vd-header-left {
        flex: 1;
        min-width: 0;
    }

    .vd-value {
        display: flex;
        align-items: baseline;
        gap: 0.3vw;
        font-size: 2.3vw;
        font-weight: 800;
        color: rgb(102, 72, 9);
        line-height: 1.1;
        flex-wrap: wrap;
    }

    .vd-value .unit {
        font-size: 1.2vw;
        font-weight: 500;
        color: rgba(102, 72, 9, 0.6);
    }

    .vd-value .badge-label {
        display: inline-flex;
        align-items: center;
        gap: 0.3vw;
        font-size: 1.1vw;
        font-weight: 700;
        padding: 0.3vw 0.8vw;
        border-radius: 0.5vw;
        background: rgba(255, 255, 255, 0.4);
    }

    .vd-value .badge-label iconify-icon {
        font-size: 1.3vw;
    }

    .vd-code {
        display: inline-flex;
        align-items: center;
        gap: 0.4vw;
        margin-top: 0.8vw;
        padding: 0.4vw 0.9vw;
        background: rgba(255, 255, 255, 0.5);
        border: 0.15vw dashed rgba(102, 72, 9, 0.3);
        border-radius: 0.5vw;
        font-family: 'Courier New', monospace;
        font-size: 0.85vw;
        font-weight: 800;
        color: rgb(102, 72, 9);
        letter-spacing: 0.08em;
        backdrop-filter: blur(0.3vw);
    }

    .vd-code iconify-icon {
        font-size: 1vw;
        opacity: 0.7;
    }

    /* Status Badge */
    .vd-status {
        display: inline-flex;
        align-items: center;
        gap: 0.35vw;
        padding: 0.5vw 1vw;
        border-radius: 100vw;
        font-size: 0.78vw;
        font-weight: 700;
        backdrop-filter: blur(0.3vw);
        flex-shrink: 0;
        white-space: nowrap;
        background: rgba(255, 255, 255, 0.5);
        color: rgb(102, 72, 9);
        border: 0.1vw solid rgba(255, 255, 255, 0.6);
    }
    .vd-status iconify-icon {
        font-size: 0.95vw;
    }

    .vd-status.is-active    { background: rgba(16, 185, 129, 0.9); color: #ffffff; border-color: rgba(16, 185, 129, 0.5); }
    .vd-status.is-used      { background: rgba(255, 255, 255, 0.5); color: rgb(102, 72, 9); }
    .vd-status.is-expired   { background: rgba(220, 38, 38, 0.9); color: #ffffff; border-color: rgba(220, 38, 38, 0.5); }
    .vd-status.is-upcoming  { background: rgba(250, 204, 21, 0.95); color: #422006; border-color: rgba(250, 204, 21, 0.5); }
    .vd-status.is-quota     { background: rgba(249, 115, 22, 0.9); color: #ffffff; border-color: rgba(249, 115, 22, 0.5); }

    /* --------------------------------------------
       TICKET STUB (Dashed separator)
       -------------------------------------------- */
    .vd-stub {
        position: relative;
        height: 0;
    }

    .vd-stub::before {
        content: '';
        position: absolute;
        top: 0;
        left: 1.2vw;
        right: 1.2vw;
        border-top: 0.15vw dashed #e2e8f0;
    }

    .vd-stub-circle {
        position: absolute;
        top: -0.65vw;
        width: 1.3vw;
        height: 1.3vw;
        background: #f8fafc;
        border-radius: 50%;
        border: 0.1vw solid #e2e8f0;
    }
    .vd-stub-circle.left {
        left: -0.65vw;
        border-right-color: transparent;
    }
    .vd-stub-circle.right {
        right: -0.65vw;
        border-left-color: transparent;
    }

    /* --------------------------------------------
       BODY
       -------------------------------------------- */
    .vd-body {
        padding: 1.8vw;
        display: flex;
        flex-direction: column;
        gap: 1.5vw;
    }

    /* Title & Desc */
    .vd-title-wrap {
        text-align: center;
    }

    .vd-title {
        font-size: 1.3vw;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.3;
    }

    .vd-desc {
        font-size: 0.85vw;
        color: #64748b;
        margin-top: 0.5vw;
        line-height: 1.6;
        max-width: 30vw;
        margin-left: auto;
        margin-right: auto;
    }

    /* --------------------------------------------
       INFO GRID
       -------------------------------------------- */
    .vd-info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.8vw;
    }

    .vd-info-item {
        padding: 1vw 1.1vw;
        background: #fafbfc;
        border: 0.1vw solid #f1f5f9;
        border-radius: 0.7vw;
        transition: all 0.2s ease;
    }

    .vd-info-item:hover {
        border-color: #fde68a;
        background: #fffbf0;
    }

    .vd-info-label {
        display: flex;
        align-items: center;
        gap: 0.35vw;
        font-size: 0.72vw;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 600;
        margin-bottom: 0.4vw;
    }

    .vd-info-label iconify-icon {
        font-size: 0.85vw;
        color: #ecbc42;
    }

    .vd-info-value {
        font-size: 0.9vw;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.3;
    }

    .vd-info-value .muted {
        font-size: 0.78vw;
        color: #94a3b8;
        font-weight: 500;
    }

    /* --------------------------------------------
       PERIODE (Progress)
       -------------------------------------------- */
    .vd-period {
        padding: 1.2vw;
        background: #fafbfc;
        border: 0.1vw solid #f1f5f9;
        border-radius: 0.8vw;
        display: flex;
        flex-direction: column;
        gap: 0.9vw;
    }

    .vd-period-head {
        display: flex;
        align-items: center;
        gap: 0.4vw;
        font-size: 0.72vw;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 600;
    }
    .vd-period-head iconify-icon {
        font-size: 0.9vw;
        color: #ecbc42;
    }

    .vd-period-timeline {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.8vw;
    }

    .vd-period-side {
        flex: 1;
        min-width: 0;
    }
    .vd-period-side.right { text-align: right; }

    .vd-period-label {
        font-size: 0.68vw;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #94a3b8;
        font-weight: 600;
    }

    .vd-period-date {
        font-size: 0.82vw;
        font-weight: 700;
        color: #0f172a;
        margin-top: 0.2vw;
    }
    .vd-period-date.status-red     { color: #dc2626; }
    .vd-period-date.status-orange  { color: #f59e0b; }
    .vd-period-date.status-green   { color: #059669; }
    .vd-period-date.status-yellow  { color: #d97706; }
    .vd-period-date.status-gray    { color: #94a3b8; }

    .vd-period-arrow {
        font-size: 1.2vw;
        color: #cbd5e1;
        flex-shrink: 0;
    }

    /* Progress Bar */
    .vd-progress-wrap {
        display: flex;
        flex-direction: column;
        gap: 0.5vw;
    }

    .vd-progress-track {
        width: 100%;
        height: 0.4vw;
        background: #e2e8f0;
        border-radius: 100vw;
        overflow: hidden;
    }

    .vd-progress-fill {
        height: 100%;
        border-radius: 100vw;
        transition: width 0.3s ease;
    }
    .vd-progress-fill.fill-gray   { background: #cbd5e1; }
    .vd-progress-fill.fill-yellow { background: linear-gradient(90deg, #fbbf24, #f59e0b); }
    .vd-progress-fill.fill-red    { background: linear-gradient(90deg, #f87171, #dc2626); }
    .vd-progress-fill.fill-orange { background: linear-gradient(90deg, #fb923c, #ea580c); }
    .vd-progress-fill.fill-green  { background: linear-gradient(90deg, #34d399, #059669); }

    .vd-progress-status {
        display: inline-flex;
        align-items: center;
        gap: 0.35vw;
        font-size: 0.75vw;
        font-weight: 700;
    }
    .vd-progress-status iconify-icon { font-size: 0.9vw; }
    .vd-progress-status.status-red    { color: #dc2626; }
    .vd-progress-status.status-orange { color: #f59e0b; }
    .vd-progress-status.status-green  { color: #059669; }
    .vd-progress-status.status-yellow { color: #d97706; }
    .vd-progress-status.status-gray   { color: #94a3b8; }

    /* --------------------------------------------
       SYARAT & KETENTUAN
       -------------------------------------------- */
    .vd-tnc-head {
        display: flex;
        align-items: center;
        gap: 0.4vw;
        font-size: 0.88vw;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.7vw;
    }
    .vd-tnc-head iconify-icon {
        font-size: 1.05vw;
        color: #ecbc42;
    }

    .vd-tnc-content {
        padding: 1.1vw 1.2vw;
        background: #fafbfc;
        border: 0.1vw solid #f1f5f9;
        border-radius: 0.7vw;
        font-size: 0.82vw;
        color: #475569;
        line-height: 1.7;
    }

    .vd-tnc-content ul,
    .vd-tnc-content ol {
        padding-left: 1.2vw;
        margin: 0.3vw 0;
    }
    .vd-tnc-content li {
        margin-bottom: 0.25vw;
    }
    .vd-tnc-content p {
        margin-bottom: 0.5vw;
    }
    .vd-tnc-content p:last-child {
        margin-bottom: 0;
    }

    .vd-tnc-empty {
        font-size: 0.82vw;
        color: #94a3b8;
        font-style: italic;
        padding: 0.8vw 1vw;
        background: #fafbfc;
        border: 0.1vw dashed #e2e8f0;
        border-radius: 0.7vw;
        text-align: center;
    }

    /* --------------------------------------------
       ACTION BUTTON
       -------------------------------------------- */
    .vd-action {
        padding-top: 0.5vw;
    }

    .vd-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5vw;
        width: 100%;
        padding: 1.1vw 1.4vw;
        border-radius: 0.8vw;
        font-size: 0.9vw;
        font-weight: 700;
        cursor: pointer;
        border: 0.1vw solid transparent;
        transition: all 0.2s ease;
        text-decoration: none;
        font-family: inherit;
    }
    .vd-btn iconify-icon {
        font-size: 1.15vw;
    }

    .vd-btn-gold {
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
        color: rgb(102, 72, 9);
        box-shadow: 0 0.2vw 0.6vw rgba(236, 188, 66, 0.35);
    }
    .vd-btn-gold:hover {
        transform: translateY(-0.1vw);
        box-shadow: 0 0.4vw 1.2vw rgba(236, 188, 66, 0.5);
    }
    .vd-btn-gold:active {
        transform: translateY(0);
    }

    /* Disabled state */
    .vd-disabled {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5vw;
        padding: 1.1vw 1.4vw;
        background: #f8fafc;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.8vw;
        font-size: 0.85vw;
        font-weight: 600;
        color: #94a3b8;
        text-align: center;
    }
    .vd-disabled iconify-icon {
        font-size: 1.15vw;
    }

    .vd-eligibility {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.35vw;
        margin-top: 0.7vw;
        font-size: 0.75vw;
        color: #94a3b8;
        text-align: center;
        line-height: 1.5;
    }
    .vd-eligibility iconify-icon {
        font-size: 0.9vw;
        flex-shrink: 0;
    }

    /* --------------------------------------------
       BACK LINK BOTTOM
       -------------------------------------------- */
    .vd-back-bottom {
        text-align: center;
    }

    .vd-back-bottom a {
        display: inline-flex;
        align-items: center;
        gap: 0.4vw;
        font-size: 0.82vw;
        color: #94a3b8;
        text-decoration: none;
        transition: color 0.2s ease;
        padding: 0.4vw 0;
    }
    .vd-back-bottom a:hover { color: rgb(102, 72, 9); }
    .vd-back-bottom a iconify-icon { font-size: 1vw; }

    /* ============================================
       RESPONSIVE - TABLET
       ============================================ */
    @media (max-width: 1024px) {
        .vd-wrapper {
            max-width: 90vw;
            gap: 2.5vw;
        }

        .vd-back {
            font-size: 2.1vw;
            gap: 1vw;
            padding: 1.2vw 0;
        }
        .vd-back iconify-icon { font-size: 2.7vw; }
        .vd-back:hover iconify-icon { transform: translateX(-0.5vw); }

        .vd-card {
            border-radius: 3vw;
            border-width: 0.2vw;
        }

        .vd-header { padding: 4vw; }
        .vd-header-deco-1 { top: -4vw; right: -4vw; width: 18vw; height: 18vw; }
        .vd-header-deco-2 { bottom: -3vw; left: -3vw; width: 14vw; height: 14vw; }

        .vd-header-inner { gap: 2.5vw; }

        .vd-value { font-size: 5.5vw; gap: 0.8vw; }
        .vd-value .unit { font-size: 2.8vw; }
        .vd-value .badge-label {
            font-size: 2.6vw;
            padding: 0.8vw 2vw;
            border-radius: 1.2vw;
            gap: 0.7vw;
        }
        .vd-value .badge-label iconify-icon { font-size: 3vw; }

        .vd-code {
            margin-top: 2vw;
            padding: 1vw 2.2vw;
            border-radius: 1.2vw;
            font-size: 2.1vw;
            gap: 1vw;
            border-width: 0.35vw;
        }
        .vd-code iconify-icon { font-size: 2.5vw; }

        .vd-status {
            gap: 0.9vw;
            padding: 1.3vw 2.3vw;
            font-size: 1.9vw;
            border-width: 0.2vw;
        }
        .vd-status iconify-icon { font-size: 2.3vw; }

        .vd-stub::before {
            left: 3vw;
            right: 3vw;
            border-top-width: 0.35vw;
        }
        .vd-stub-circle {
            top: -1.5vw;
            width: 3vw;
            height: 3vw;
            border-width: 0.2vw;
        }
        .vd-stub-circle.left  { left: -1.5vw; }
        .vd-stub-circle.right { right: -1.5vw; }

        .vd-body { padding: 4vw; gap: 3vw; }

        .vd-title { font-size: 3.2vw; }
        .vd-desc {
            font-size: 2vw;
            margin-top: 1.2vw;
            max-width: 70vw;
        }

        .vd-info-grid { gap: 2vw; }
        .vd-info-item {
            padding: 2.5vw 2.8vw;
            border-radius: 1.8vw;
            border-width: 0.2vw;
        }

        .vd-info-label {
            font-size: 1.7vw;
            gap: 0.9vw;
            margin-bottom: 1vw;
        }
        .vd-info-label iconify-icon { font-size: 2.1vw; }

        .vd-info-value { font-size: 2.2vw; }
        .vd-info-value .muted { font-size: 1.9vw; }

        .vd-period {
            padding: 3vw;
            border-radius: 2vw;
            gap: 2.2vw;
            border-width: 0.2vw;
        }

        .vd-period-head {
            font-size: 1.7vw;
            gap: 1vw;
        }
        .vd-period-head iconify-icon { font-size: 2.1vw; }

        .vd-period-timeline { gap: 2vw; }

        .vd-period-label { font-size: 1.6vw; }
        .vd-period-date {
            font-size: 2vw;
            margin-top: 0.5vw;
        }

        .vd-period-arrow { font-size: 2.8vw; }

        .vd-progress-wrap { gap: 1.2vw; }

        .vd-progress-track {
            height: 0.9vw;
        }

        .vd-progress-status {
            font-size: 1.8vw;
            gap: 0.9vw;
        }
        .vd-progress-status iconify-icon { font-size: 2.2vw; }

        .vd-tnc-head {
            font-size: 2.1vw;
            gap: 1vw;
            margin-bottom: 1.7vw;
        }
        .vd-tnc-head iconify-icon { font-size: 2.6vw; }

        .vd-tnc-content {
            padding: 2.8vw 3vw;
            border-radius: 1.8vw;
            font-size: 2vw;
            line-height: 1.8;
            border-width: 0.2vw;
        }
        .vd-tnc-content ul,
        .vd-tnc-content ol { padding-left: 3vw; }
        .vd-tnc-content li { margin-bottom: 0.7vw; }
        .vd-tnc-content p { margin-bottom: 1.2vw; }

        .vd-tnc-empty {
            font-size: 2vw;
            padding: 2vw 2.5vw;
            border-radius: 1.8vw;
            border-width: 0.2vw;
        }

        .vd-action { padding-top: 1.5vw; }

        .vd-btn {
            gap: 1.2vw;
            padding: 2.8vw 3.5vw;
            border-radius: 2vw;
            font-size: 2.2vw;
            border-width: 0.2vw;
        }
        .vd-btn iconify-icon { font-size: 2.8vw; }

        .vd-disabled {
            gap: 1.2vw;
            padding: 2.8vw 3.5vw;
            border-radius: 2vw;
            font-size: 2.1vw;
            border-width: 0.2vw;
        }
        .vd-disabled iconify-icon { font-size: 2.8vw; }

        .vd-eligibility {
            font-size: 1.8vw;
            gap: 0.9vw;
            margin-top: 1.5vw;
        }
        .vd-eligibility iconify-icon { font-size: 2.2vw; }

        .vd-back-bottom a {
            font-size: 2vw;
            gap: 1vw;
            padding: 1vw 0;
        }
        .vd-back-bottom a iconify-icon { font-size: 2.5vw; }
    }

    /* ============================================
       RESPONSIVE - MOBILE
       ============================================ */
    @media (max-width: 480px) {
        .vd-wrapper {
            max-width: 100%;
            gap: 4vw;
        }

        .vd-back {
            font-size: 3.2vw;
            gap: 1.5vw;
            padding: 2vw 0;
        }
        .vd-back iconify-icon { font-size: 4vw; }

        .vd-card {
            border-radius: 4vw;
            border-width: 0.3vw;
        }

        .vd-header { padding: 6vw 5vw; }
        .vd-header-deco-1 { top: -6vw; right: -6vw; width: 26vw; height: 26vw; }
        .vd-header-deco-2 { bottom: -4vw; left: -4vw; width: 20vw; height: 20vw; }

        .vd-header-inner {
            flex-direction: column;
            align-items: stretch;
            gap: 3.5vw;
        }

        .vd-value { font-size: 8vw; gap: 1vw; }
        .vd-value .unit { font-size: 4.2vw; }
        .vd-value .badge-label {
            font-size: 4vw;
            padding: 1.2vw 3vw;
            border-radius: 2vw;
            gap: 1vw;
        }
        .vd-value .badge-label iconify-icon { font-size: 4.5vw; }

        .vd-code {
            margin-top: 3vw;
            padding: 1.8vw 3.5vw;
            border-radius: 2vw;
            font-size: 3.4vw;
            gap: 1.5vw;
            border-width: 0.5vw;
        }
        .vd-code iconify-icon { font-size: 4vw; }

        .vd-status {
            align-self: flex-start;
            gap: 1.2vw;
            padding: 2vw 3.5vw;
            font-size: 3vw;
            border-width: 0.3vw;
        }
        .vd-status iconify-icon { font-size: 3.6vw; }

        .vd-stub::before {
            left: 5vw;
            right: 5vw;
            border-top-width: 0.5vw;
        }
        .vd-stub-circle {
            top: -2.2vw;
            width: 4.4vw;
            height: 4.4vw;
            border-width: 0.3vw;
        }
        .vd-stub-circle.left  { left: -2.2vw; }
        .vd-stub-circle.right { right: -2.2vw; }

        .vd-body { padding: 6vw 5vw; gap: 5vw; }

        .vd-title { font-size: 5vw; line-height: 1.3; }
        .vd-desc {
            font-size: 3.2vw;
            margin-top: 2vw;
            max-width: 100%;
            line-height: 1.7;
        }

        .vd-info-grid {
            grid-template-columns: 1fr;
            gap: 2.5vw;
        }

        .vd-info-item {
            padding: 4vw 4.5vw;
            border-radius: 3vw;
            border-width: 0.3vw;
        }

        .vd-info-label {
            font-size: 2.7vw;
            gap: 1.2vw;
            margin-bottom: 1.7vw;
        }
        .vd-info-label iconify-icon { font-size: 3.4vw; }

        .vd-info-value { font-size: 3.5vw; }
        .vd-info-value .muted { font-size: 3vw; }

        .vd-period {
            padding: 4.5vw;
            border-radius: 3vw;
            gap: 3.5vw;
            border-width: 0.3vw;
        }

        .vd-period-head {
            font-size: 2.7vw;
            gap: 1.5vw;
        }
        .vd-period-head iconify-icon { font-size: 3.4vw; }

        .vd-period-timeline {
            flex-direction: column;
            align-items: stretch;
            gap: 3vw;
        }

        .vd-period-side,
        .vd-period-side.right {
            text-align: left;
        }

        .vd-period-label { font-size: 2.7vw; }
        .vd-period-date {
            font-size: 3.4vw;
            margin-top: 1vw;
        }

        .vd-period-arrow {
            align-self: center;
            transform: rotate(90deg);
            font-size: 5vw;
        }

        .vd-progress-wrap { gap: 2vw; }

        .vd-progress-track {
            height: 1.4vw;
        }

        .vd-progress-status {
            font-size: 3vw;
            gap: 1.2vw;
        }
        .vd-progress-status iconify-icon { font-size: 3.6vw; }

        .vd-tnc-head {
            font-size: 3.6vw;
            gap: 1.5vw;
            margin-bottom: 2.5vw;
        }
        .vd-tnc-head iconify-icon { font-size: 4.2vw; }

        .vd-tnc-content {
            padding: 4vw 4.5vw;
            border-radius: 3vw;
            font-size: 3.2vw;
            line-height: 1.8;
            border-width: 0.3vw;
        }
        .vd-tnc-content ul,
        .vd-tnc-content ol { padding-left: 5vw; }
        .vd-tnc-content li { margin-bottom: 1.2vw; }
        .vd-tnc-content p { margin-bottom: 2vw; }

        .vd-tnc-empty {
            font-size: 3.2vw;
            padding: 3.5vw 4vw;
            border-radius: 3vw;
            border-width: 0.3vw;
        }

        .vd-action { padding-top: 2.5vw; }

        .vd-btn {
            gap: 2vw;
            padding: 4.5vw 5vw;
            border-radius: 3vw;
            font-size: 3.4vw;
            border-width: 0.3vw;
        }
        .vd-btn iconify-icon { font-size: 4.2vw; }

        .vd-disabled {
            flex-direction: column;
            gap: 2vw;
            padding: 4.5vw 5vw;
            border-radius: 3vw;
            font-size: 3.2vw;
            border-width: 0.3vw;
            line-height: 1.5;
        }
        .vd-disabled iconify-icon { font-size: 5vw; }

        .vd-eligibility {
            font-size: 3vw;
            gap: 1.2vw;
            margin-top: 2.5vw;
            line-height: 1.6;
        }
        .vd-eligibility iconify-icon { font-size: 3.6vw; }

        .vd-back-bottom a {
            font-size: 3.2vw;
            gap: 1.5vw;
            padding: 2vw 0;
        }
        .vd-back-bottom a iconify-icon { font-size: 4vw; }
    }
</style>

<div class="vd-wrapper">

    {{-- Back Link Top --}}
    <a href="{{ route('customer.vouchers.index') }}" class="vd-back">
        <iconify-icon icon="mdi:arrow-left"></iconify-icon>
        Kembali ke Daftar Voucher
    </a>

    {{-- Voucher Card --}}
    <div class="vd-card">

        {{-- HEADER --}}
        <div class="vd-header">
            <div class="vd-header-deco-1"></div>
            <div class="vd-header-deco-2"></div>

            <div class="vd-header-inner">
                <div class="vd-header-left">
                    {{-- Value --}}
                    <div class="vd-value">
                        @if($voucher->is_free_shipping)
                            <span class="badge-label">
                                <iconify-icon icon="mdi:truck-fast-outline"></iconify-icon>
                                Gratis Ongkir
                            </span>
                        @elseif($voucher->discount_target === 'shipping')
                            <span class="badge-label">
                                <iconify-icon icon="mdi:truck-outline"></iconify-icon>
                                Diskon Ongkir
                            </span>
                            @if($voucher->discount_type === 'percentage')
                                <span>{{ round($voucher->discount_value) }}<span class="unit">%</span></span>
                            @else
                                <span><span class="unit">Rp</span> {{ number_format($voucher->discount_value, 0, ',', '.') }}</span>
                            @endif
                        @else
                            @if($voucher->discount_type === 'percentage')
                                <span>{{ round($voucher->discount_value) }}<span class="unit">%</span></span>
                            @else
                                <span><span class="unit">Rp</span> {{ number_format($voucher->discount_value, 0, ',', '.') }}</span>
                            @endif
                        @endif
                    </div>

                    {{-- Code --}}
                    <div class="vd-code">
                        <iconify-icon icon="mdi:ticket-percent-outline"></iconify-icon>
                        {{ $voucher->code }}
                    </div>
                </div>

                {{-- Status Badge --}}
                <span class="vd-status
                    @if($isUsedByUser) is-used
                    @elseif($isValid) is-active
                    @elseif($isExpired) is-expired
                    @elseif($isUpcoming) is-upcoming
                    @elseif($isQuotaFull) is-quota
                    @else is-used @endif">
                    @if($isUsedByUser)
                        <iconify-icon icon="mdi:check-circle"></iconify-icon> Digunakan
                    @elseif($isValid)
                        <iconify-icon icon="mdi:check-decagram"></iconify-icon> Aktif
                    @elseif($isExpired)
                        <iconify-icon icon="mdi:clock-alert-outline"></iconify-icon> Kadaluarsa
                    @elseif($isUpcoming)
                        <iconify-icon icon="mdi:calendar-clock"></iconify-icon> Akan Datang
                    @elseif($isQuotaFull)
                        <iconify-icon icon="mdi:lock-outline"></iconify-icon> Habis
                    @else
                        <iconify-icon icon="mdi:close-circle-outline"></iconify-icon> Tidak Tersedia
                    @endif
                </span>
            </div>
        </div>

        {{-- Ticket Stub --}}
        <div class="vd-stub">
            <div class="vd-stub-circle left"></div>
            <div class="vd-stub-circle right"></div>
        </div>

        {{-- BODY --}}
        <div class="vd-body">

            {{-- Title & Desc --}}
            <div class="vd-title-wrap">
                <div class="vd-title">{{ $voucher->name }}</div>
                @if($voucher->description)
                    <div class="vd-desc">{{ $voucher->description }}</div>
                @endif
            </div>

            {{-- Info Grid --}}
            <div class="vd-info-grid">
                <div class="vd-info-item">
                    <div class="vd-info-label">
                        <iconify-icon icon="mdi:cart-outline"></iconify-icon>
                        Min. Transaksi
                    </div>
                    <div class="vd-info-value">
                        Rp {{ number_format($voucher->min_transaction_amount, 0, ',', '.') }}
                    </div>
                </div>

                <div class="vd-info-item">
                    <div class="vd-info-label">
                        <iconify-icon icon="mdi:scissors-cutting"></iconify-icon>
                        Maks. Potongan
                    </div>
                    <div class="vd-info-value">
                        @if($voucher->max_discount_amount)
                            Rp {{ number_format($voucher->max_discount_amount, 0, ',', '.') }}
                        @else
                            <span class="muted">Tanpa batas</span>
                        @endif
                    </div>
                </div>

                <div class="vd-info-item">
                    <div class="vd-info-label">
                        <iconify-icon icon="mdi:account-check-outline"></iconify-icon>
                        Kamu Pakai
                    </div>
                    <div class="vd-info-value">
                        {{ $usageCountByUser }} kali
                        @if($voucher->limit_per_user > 0)
                            <span class="muted">/ {{ $voucher->limit_per_user }}x</span>
                        @endif
                    </div>
                </div>

                <div class="vd-info-item">
                    <div class="vd-info-label">
                        <iconify-icon icon="mdi:tag-outline"></iconify-icon>
                        Jenis Voucher
                    </div>
                    <div class="vd-info-value">
                        @if($voucher->is_free_shipping)
                            Gratis Ongkir
                        @elseif($voucher->discount_target === 'shipping')
                            Diskon Ongkir
                        @else
                            Diskon Produk
                        @endif
                    </div>
                </div>
            </div>

            {{-- Periode --}}
            @php
                $now = now();
                $endDate = $voucher->end_date;
                $startDate = $voucher->start_date;
                $isExpiredLocal = $now->greaterThan($endDate);
                $isUpcomingLocal = $now->lessThan($startDate);

                if ($isExpiredLocal) {
                    $daysLeft = 0; $hoursLeft = 0;
                } else {
                    $diff = $now->diff($endDate);
                    $daysLeft = $diff->days;
                    $hoursLeft = $diff->h;
                }

                if ($isUpcomingLocal) {
                    $progressPercent = 0;
                } elseif ($isExpiredLocal) {
                    $progressPercent = 100;
                } else {
                    $totalSeconds = $startDate->diffInSeconds($endDate);
                    $elapsedSeconds = $startDate->diffInSeconds($now);
                    $progressPercent = $totalSeconds > 0 
                        ? min(100, max(0, ($elapsedSeconds / $totalSeconds) * 100)) 
                        : 100;
                }

                if ($isExpiredLocal) {
                    $statusText = 'Kadaluarsa';
                    $statusClass = 'status-gray';
                    $fillClass = 'fill-gray';
                    $statusIcon = 'mdi:clock-remove-outline';
                } elseif ($isUpcomingLocal) {
                    $statusText = 'Belum mulai';
                    $statusClass = 'status-yellow';
                    $fillClass = 'fill-yellow';
                    $statusIcon = 'mdi:calendar-clock';
                } elseif ($daysLeft <= 3) {
                    $statusText = $daysLeft > 0 ? "{$daysLeft} hari lagi" : "{$hoursLeft} jam lagi";
                    $statusClass = 'status-red';
                    $fillClass = 'fill-red';
                    $statusIcon = 'mdi:clock-alert-outline';
                } elseif ($daysLeft <= 7) {
                    $statusText = "{$daysLeft} hari lagi";
                    $statusClass = 'status-orange';
                    $fillClass = 'fill-orange';
                    $statusIcon = 'mdi:clock-outline';
                } else {
                    $statusText = "{$daysLeft} hari lagi";
                    $statusClass = 'status-green';
                    $fillClass = 'fill-green';
                    $statusIcon = 'mdi:clock-outline';
                }
            @endphp

            <div class="vd-period">
                <div class="vd-period-head">
                    <iconify-icon icon="mdi:calendar-range"></iconify-icon>
                    Periode Berlaku
                </div>

                <div class="vd-period-timeline">
                    <div class="vd-period-side">
                        <div class="vd-period-label">Mulai</div>
                        <div class="vd-period-date">
                            {{ $startDate->translatedFormat('d M Y, H:i') }}
                        </div>
                    </div>

                    <iconify-icon icon="mdi:arrow-right" class="vd-period-arrow"></iconify-icon>

                    <div class="vd-period-side right">
                        <div class="vd-period-label">Berakhir</div>
                        <div class="vd-period-date {{ $statusClass }}">
                            {{ $endDate->translatedFormat('d M Y, H:i') }}
                        </div>
                    </div>
                </div>

                <div class="vd-progress-wrap">
                    <div class="vd-progress-track">
                        <div class="vd-progress-fill {{ $fillClass }}" 
                             style="width: {{ $progressPercent }}%"></div>
                    </div>
                    <div class="vd-progress-status {{ $statusClass }}">
                        <iconify-icon icon="{{ $statusIcon }}"></iconify-icon>
                        {{ $statusText }}
                    </div>
                </div>
            </div>

            {{-- Syarat & Ketentuan --}}
            <div>
                <div class="vd-tnc-head">
                    <iconify-icon icon="mdi:clipboard-text-outline"></iconify-icon>
                    Syarat & Ketentuan
                </div>
                @if($voucher->terms_and_conditions)
                    <div class="vd-tnc-content">
                        {!! $voucher->terms_and_conditions !!}
                    </div>
                @else
                    <div class="vd-tnc-empty">
                        Tidak ada syarat & ketentuan khusus untuk voucher ini.
                    </div>
                @endif
            </div>

            {{-- Action Button --}}
            <div class="vd-action">
                @if($isUsedByUser)
                    <div class="vd-disabled">
                        <iconify-icon icon="mdi:check-circle"></iconify-icon>
                        <span>Voucher sudah Anda gunakan</span>
                    </div>
                @elseif(!$isValid)
                    <div class="vd-disabled">
                        @if($isExpired)
                            <iconify-icon icon="mdi:clock-alert-outline"></iconify-icon>
                            <span>Voucher sudah kadaluarsa</span>
                        @elseif($isUpcoming)
                            <iconify-icon icon="mdi:calendar-clock"></iconify-icon>
                            <span>Aktif mulai {{ $voucher->start_date->translatedFormat('d M Y, H:i') }}</span>
                        @elseif($isQuotaFull)
                            <iconify-icon icon="mdi:lock-outline"></iconify-icon>
                            <span>Kuota voucher sudah habis</span>
                        @else
                            <iconify-icon icon="mdi:close-circle-outline"></iconify-icon>
                            <span>Voucher tidak tersedia</span>
                        @endif
                    </div>
                @else
                    <form action="{{ route('customer.vouchers.use', $voucher) }}" method="POST">
                        @csrf
                        <button type="submit" class="vd-btn vd-btn-gold">
                            <iconify-icon icon="mdi:cart-arrow-right"></iconify-icon>
                            Pakai Voucher Sekarang
                        </button>
                    </form>
                    @if($eligibilityMessage)
                        <div class="vd-eligibility">
                            <iconify-icon icon="mdi:information-outline"></iconify-icon>
                            <span>{{ $eligibilityMessage }}</span>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- Back Link Bottom --}}
    <div class="vd-back-bottom">
        <a href="{{ route('customer.vouchers.index') }}">
            <iconify-icon icon="mdi:arrow-left"></iconify-icon>
            Kembali ke Daftar Voucher
        </a>
    </div>
</div>

@endsection