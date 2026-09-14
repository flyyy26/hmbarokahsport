@extends('layouts.account')

@section('title', 'Detail Pesanan #' . $order->order_number . ' - Barokah Sport')
@section('page-title', 'Detail Pesanan')
@section('page-subtitle', '#' . $order->order_number)

@section('account-content')

<style>
    /* ============================================
       ORDER DETAIL STYLES
       ============================================ */
    .od-wrapper {
        display: flex;
        flex-direction: column;
        gap: 1.5vw;
    }

    .od-header-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1.5vw;
        flex-wrap: wrap;
    }

    /* --------------------------------------------
       STATUS BAR
       -------------------------------------------- */
    .od-status-bar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.6vw;
        flex: 1;
        min-width: 0;
    }

    .od-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4vw;
        padding: 0.5vw 0.9vw;
        border-radius: 100vw;
        font-size: 0.8vw;
        font-weight: 600;
        border: 0.1vw solid transparent;
        margin-top: 1.5vw;
        white-space: nowrap;
    }

    .od-badge iconify-icon {
        font-size: 1vw;
    }

    .od-badge-shipped   { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
    .od-badge-processing{ background: #eef2ff; color: #4338ca; border-color: #c7d2fe; }
    .od-badge-delivered { background: #ecfdf5; color: #047857; border-color: #a7f3d0; }
    .od-badge-cancelled { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
    .od-badge-pending   { background: #fffbeb; color: #b45309; border-color: #fde68a; }
    .od-badge-paid      { background: #ecfdf5; color: #047857; border-color: #a7f3d0; }
    .od-badge-unpaid    { background: #fff7ed; color: #c2410c; border-color: #fed7aa; }
    .od-badge-warning   { background: #fffbeb; color: #b45309; border-color: #fde68a; }

    .od-action-row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.6vw;
        justify-content: flex-end;
        align-items: flex-start;
        margin-top: 1.5vw;
        flex-shrink: 0;
    }

    /* --------------------------------------------
       BUTTONS
       -------------------------------------------- */
    .od-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4vw;
        padding: 0.75vw 1.2vw;
        border-radius: 0.7vw;
        font-size: 0.82vw;
        font-weight: 600;
        cursor: pointer;
        border: 0.1vw solid transparent;
        transition: all 0.2s ease;
        text-decoration: none;
        font-family: inherit;
    }

    .od-btn iconify-icon {
        font-size: 1.05vw;
    }

    .od-btn-gold {
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
        color: rgb(102, 72, 9);
        box-shadow: 0 0.15vw 0.5vw rgba(236, 188, 66, 0.3);
    }
    .od-btn-gold:hover {
        transform: translateY(-0.1vw);
        box-shadow: 0 0.3vw 1vw rgba(236, 188, 66, 0.4);
    }

    .od-btn-outline {
        background: #ffffff;
        border-color: #e2e8f0;
        color: #475569;
    }
    .od-btn-outline:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .od-btn-danger {
        background: #fef2f2;
        border-color: #fecaca;
        color: #dc2626;
    }
    .od-btn-danger:hover { background: #fee2e2; }

     .od-btn-primary {
         background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
         color:rgb(102, 72, 9);
     }

     .od-btn-success {
         background: #dcfce7;
         border-color: #86efac;
         color: #15803d;
     }
     .od-btn-success:hover {
         background: #c8e6c9;
     }

    /* --------------------------------------------
       SECTION / CARD
       -------------------------------------------- */
    .od-section {
        background: #ffffff;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.9vw;
        overflow: hidden;
    }

    .od-section-head {
        display: flex;
        align-items: center;
        gap: 0.5vw;
        padding: 0.9vw 1.2vw;
        border-bottom: 0.1vw solid #f1f5f9;
        background: #fafbfc;
        font-size: 0.85vw;
        font-weight: 700;
        color: #0f172a;
    }

    .od-section-head iconify-icon {
        color: #ecbc42;
        font-size: 1.1vw;
    }

    .od-section-body {
        padding: 1.2vw;
    }

    /* --------------------------------------------
       ORDER ITEMS
       -------------------------------------------- */
    .od-items {
        display: flex;
        flex-direction: column;
    }

    .od-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1vw;
        padding: 1vw 0;
        border-bottom: 0.1vw solid #f1f5f9;
    }
    .od-item:last-child { border-bottom: none; }

    .od-item-left {
        display: flex;
        align-items: center;
        gap: 0.9vw;
        min-width: 0;
        flex: 1;
    }

    .od-item-img {
        width: 3.6vw;
        height: 3.6vw;
        border-radius: 0.7vw;
        background: #f1f5f9;
        overflow: hidden;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .od-item-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .od-item-img iconify-icon {
        font-size: 1.6vw;
        color: #cbd5e1;
    }

    .od-item-info { min-width: 0; flex: 1; }

    .od-item-name {
        font-size: 0.85vw;
        font-weight: 600;
        color: #0f172a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .od-item-variant {
        font-size: 0.72vw;
        color: #64748b;
        margin-top: 0.15vw;
    }

    .od-item-qty {
        font-size: 0.72vw;
        color: #94a3b8;
        margin-top: 0.2vw;
    }

    .od-item-price {
        font-size: 0.9vw;
        font-weight: 700;
        color: #0f172a;
        white-space: nowrap;
    }

    /* --------------------------------------------
       SUMMARY
       -------------------------------------------- */
    .od-summary {
        padding: 1.2vw;
        background: #fafbfc;
        border-top: 0.1vw solid #f1f5f9;
        display: flex;
        flex-direction: column;
        gap: 0.5vw;
    }

    .od-summary-row {
        display: flex;
        justify-content: space-between;
        font-size: 0.82vw;
        color: #475569;
    }

    .od-summary-row.discount { color: #dc2626; }

    .od-summary-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 0.7vw;
        border-top: 0.1vw dashed #cbd5e1;
        font-size: 1vw;
        font-weight: 800;
        color: #0f172a;
    }

    .od-summary-total .price {
        font-size: 1.15vw;
        color: rgb(102, 72, 9);
    }

    /* --------------------------------------------
       ADDRESS / SHIPPING
       -------------------------------------------- */
    .od-address {
        font-size: 0.82vw;
        color: #475569;
        line-height: 1.7;
    }

    .od-address .name {
        font-size: 0.9vw;
        font-weight: 700;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 0.4vw;
        margin-bottom: 0.3vw;
    }

    .od-address .row {
        display: flex;
        gap: 0.5vw;
        align-items: center;
    }

    .od-address .row iconify-icon {
        font-size: 0.95vw;
        color: #94a3b8;
        flex-shrink: 0;
    }

    /* --------------------------------------------
       TIMELINE
       -------------------------------------------- */
    .od-timeline {
        position: relative;
        padding-left: 1.8vw;
    }

    .od-timeline::before {
        content: '';
        position: absolute;
        left: 0.45vw;
        top: 0.5vw;
        bottom: 0.5vw;
        width: 0.15vw;
        background: #e2e8f0;
        border-radius: 100vw;
    }

    .od-timeline-item {
        position: relative;
        padding-bottom: 1.2vw;
    }
    .od-timeline-item:last-child {
        padding-bottom: 0;
    }

    .od-timeline-dot {
        position: absolute;
        left: -1.8vw;
        top: 0.1vw;
        width: 1.1vw;
        height: 1.1vw;
        border-radius: 50%;
        background: #ffffff;
        border: 0.25vw solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .od-timeline-dot.completed {
        border-color: #10b981;
        background: #10b981;
    }

    .od-timeline-dot.completed iconify-icon {
        color: #ffffff;
        font-size: 0.6vw;
    }

    .od-timeline-dot.pending {
        border-color: #ecbc42;
        background: #ecbc42;
    }

    .od-timeline-dot.pending iconify-icon {
        color: #ffffff;
        font-size: 0.6vw;
    }

    .od-timeline-dot.cancelled {
        border-color: #dc2626;
        background: #dc2626;
    }

    .od-timeline-dot.cancelled iconify-icon {
        color: #ffffff;
        font-size: 0.6vw;
    }

    .od-timeline-title {
        font-size: 0.85vw;
        font-weight: 600;
        color: #0f172a;
    }

    .od-timeline-time {
        font-size: 0.72vw;
        color: #94a3b8;
        margin-top: 0.2vw;
    }

    /* --------------------------------------------
       NOTES
       -------------------------------------------- */
    .od-notes {
        font-size: 0.82vw;
        color: #475569;
        line-height: 1.7;
        background: #fffbeb;
        border: 0.1vw solid #fde68a;
        border-radius: 0.6vw;
        padding: 1vw;
        display: flex;
        gap: 0.7vw;
    }

    .od-notes iconify-icon {
        color: #b45309;
        font-size: 1.2vw;
        flex-shrink: 0;
        margin-top: 0.1vw;
    }

    /* --------------------------------------------
       BACK LINK
       -------------------------------------------- */
    .od-back {
        display: inline-flex;
        align-items: center;
        gap: 0.4vw;
        font-size: 0.85vw;
        color: #64748b;
        text-decoration: none;
        transition: color 0.2s ease;
        padding: 0.5vw 0;
    }
    .od-back:hover { color: rgb(102, 72, 9); }
    .od-back iconify-icon { font-size: 1.1vw; }

    /* --------------------------------------------
       MODAL
       -------------------------------------------- */
    .od-modal {
        position: fixed;
        inset: 0;
        z-index: 100;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(0.2vw);
        padding: 1vw;
    }
    .od-modal.active { display: flex; }

    .od-modal-box {
        width: 100%;
        max-width: 30vw;
        background: #ffffff;
        border-radius: 1vw;
        padding: 1.5vw;
        box-shadow: 0 1vw 3vw rgba(0, 0, 0, 0.15);
        animation: odSlideIn 0.25s ease-out;
    }

    @keyframes odSlideIn {
        from { opacity: 0; transform: translateY(-1vw) scale(0.96); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .od-modal-head {
        display: flex;
        align-items: center;
        gap: 0.7vw;
        margin-bottom: 0.7vw;
    }

    .od-modal-head .icon-wrap {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2.5vw;
        height: 2.5vw;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .od-modal-head .icon-wrap.danger {
        background: #fef2f2;
        color: #dc2626;
    }

    .od-modal-head .icon-wrap.primary {
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
        color:rgb(102, 72, 9);
    }

    .od-modal-head .icon-wrap iconify-icon {
        font-size: 1.4vw;
    }

    .od-modal-title {
        font-size: 1.1vw;
        font-weight: 700;
        color: #0f172a;
    }

    .od-modal-desc {
        font-size: 0.82vw;
        color: #64748b;
        line-height: 1.6;
        margin-bottom: 1vw;
    }

    .od-modal textarea {
        width: 100%;
        padding: 0.8vw 1vw;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.6vw;
        font-size: 0.82vw;
        font-family: inherit;
        color: #0f172a;
        outline: none;
        resize: vertical;
        min-height: 5vw;
        transition: all 0.2s ease;
    }

    .od-modal textarea:focus {
        border-color: #ecbc42;
        box-shadow: 0 0 0 0.25vw rgba(236, 188, 66, 0.2);
    }

    .od-modal-hint {
        font-size: 0.7vw;
        color: #94a3b8;
        margin-top: 0.3vw;
    }

    .od-modal-actions {
        display: flex;
        gap: 0.6vw;
        margin-top: 1.2vw;
    }

    .od-modal-actions .od-btn {
        flex: 1;
        padding: 0.9vw 1.2vw;
    }

    /* ============================================
       RESPONSIVE - TABLET
       ============================================ */
    @media (max-width: 1024px) {
        .od-wrapper { gap: 3vw; }
        .od-header-row {
            gap: 2vw;
        }

        .od-status-bar { gap: 1.5vw; }
        .od-action-row {
            gap: 1.5vw;
            margin-top: 1.5vw;
        }

        .od-badge {
            padding: 1.3vw 2.2vw;
            font-size: 1.9vw;
            gap: 1vw;
            border-width: 0.2vw;
        }
        .od-badge iconify-icon { font-size: 2.3vw; }

        .od-btn {
            padding: 2.2vw 3vw;
            border-radius: 1.5vw;
            font-size: 2vw;
            gap: 1vw;
            border-width: 0.2vw;
        }
        .od-btn iconify-icon { font-size: 2.5vw; }

        .od-section {
            border-radius: 2vw;
            border-width: 0.2vw;
        }

        .od-section-head {
            padding: 2.3vw 3vw;
            gap: 1.2vw;
            font-size: 2.1vw;
            border-bottom-width: 0.2vw;
        }
        .od-section-head iconify-icon { font-size: 2.8vw; }

        .od-section-body { padding: 3vw; }

        .od-item {
            padding: 2.5vw 0;
            gap: 2.5vw;
            border-bottom-width: 0.2vw;
        }
        .od-item-left { gap: 2.3vw; }
        .od-item-img {
            width: 9vw; height: 9vw;
            border-radius: 1.5vw;
        }
        .od-item-img iconify-icon { font-size: 4vw; }
        .od-item-name { font-size: 2.1vw; }
        .od-item-variant { font-size: 1.8vw; margin-top: 0.4vw; }
        .od-item-qty { font-size: 1.8vw; margin-top: 0.5vw; }
        .od-item-price { font-size: 2.2vw; }

        .od-summary {
            padding: 3vw;
            gap: 1.2vw;
            border-top-width: 0.2vw;
        }
        .od-summary-row { font-size: 2vw; }
        .od-summary-total {
            padding-top: 1.8vw;
            border-top-width: 0.2vw;
            font-size: 2.5vw;
        }
        .od-summary-total .price { font-size: 2.8vw; }

        .od-address { font-size: 2vw; }
        .od-address .name { font-size: 2.2vw; gap: 1vw; margin-bottom: 0.7vw; }
        .od-address .row { gap: 1.2vw; }
        .od-address .row iconify-icon { font-size: 2.3vw; }

        .od-timeline { padding-left: 4.5vw; }
        .od-timeline::before {
            left: 1.1vw;
            top: 1.2vw;
            bottom: 1.2vw;
            width: 0.4vw;
        }
        .od-timeline-item { padding-bottom: 3vw; }
        .od-timeline-dot {
            left: -4.5vw;
            top: 0.3vw;
            width: 2.8vw;
            height: 2.8vw;
            border-width: 0.6vw;
        }
        .od-timeline-dot iconify-icon { font-size: 1.5vw; }
        .od-timeline-title { font-size: 2.1vw; }
        .od-timeline-time { font-size: 1.8vw; margin-top: 0.5vw; }

        .od-notes {
            font-size: 2vw;
            border-radius: 1.5vw;
            padding: 2.5vw;
            gap: 1.7vw;
            border-width: 0.2vw;
        }
        .od-notes iconify-icon { font-size: 3vw; }

        .od-back {
            font-size: 2.1vw;
            gap: 1vw;
            padding: 1.2vw 0;
        }
        .od-back iconify-icon { font-size: 2.7vw; }

        .od-modal { padding: 3vw; }
        .od-modal-box {
            max-width: 80vw;
            border-radius: 2.5vw;
            padding: 4vw;
        }
        .od-modal-head { gap: 1.7vw; margin-bottom: 1.7vw; }
        .od-modal-head .icon-wrap {
            width: 6.5vw; height: 6.5vw;
        }
        .od-modal-head .icon-wrap iconify-icon { font-size: 3.5vw; }
        .od-modal-title { font-size: 2.8vw; }
        .od-modal-desc { font-size: 2vw; margin-bottom: 2.5vw; }
        .od-modal textarea {
            padding: 2vw 2.5vw;
            border-radius: 1.5vw;
            font-size: 2vw;
            min-height: 20vw;
            border-width: 0.2vw;
        }
        .od-modal-hint { font-size: 1.7vw; margin-top: 0.7vw; }
        .od-modal-actions { gap: 1.5vw; margin-top: 3vw; }
        .od-modal-actions .od-btn { padding: 2.3vw 3vw; }
    }

    /* ============================================
       RESPONSIVE - MOBILE
       ============================================ */
    @media (max-width: 480px) {
        .od-wrapper { gap: 5vw; }

        .od-header-row {
            flex-direction: column;
            align-items: stretch;
            gap: 3vw;
        }

        .od-status-bar {
            gap: 2.5vw;
        }

        .od-action-row {
            flex-direction: column;
            gap: 2.5vw;
            margin-top: 3vw;
            width: 100%;
        }

        .od-action-row .od-btn {
            width: 100%;
        }
        .od-badge {
            padding: 2vw 3.5vw;
            font-size: 3vw;
            gap: 1.5vw;
            border-width: 0.3vw;
        }
        .od-badge iconify-icon { font-size: 3.8vw; }

        .od-btn {
            width: 100%;
            padding: 3.2vw 4vw;
            border-radius: 2.5vw;
            font-size: 3.2vw;
            gap: 1.5vw;
            border-width: 0.3vw;
        }
        .od-btn iconify-icon { font-size: 4vw; }

        .od-section {
            border-radius: 3vw;
            border-width: 0.3vw;
        }

        .od-section-head {
            padding: 3.5vw 4vw;
            gap: 2vw;
            font-size: 3.5vw;
            border-bottom-width: 0.3vw;
        }
        .od-section-head iconify-icon { font-size: 4.2vw; }

        .od-section-body { padding: 4vw; }

        .od-item {
            padding: 3.5vw 0;
            gap: 3vw;
            border-bottom-width: 0.3vw;
            flex-wrap: wrap;
        }
        .od-item-left {
            gap: 3vw;
            width: 100%;
        }
        .od-item-img {
            width: 16vw; height: 16vw;
            border-radius: 2.5vw;
        }
        .od-item-img iconify-icon { font-size: 7vw; }
        .od-item-name { font-size: 3.4vw; }
        .od-item-variant { font-size: 2.8vw; margin-top: 0.8vw; }
        .od-item-qty { font-size: 2.8vw; margin-top: 1vw; }
        .od-item-price {
            font-size: 3.5vw;
            width: 100%;
            text-align: right;
            margin-top: 1.5vw;
        }

        .od-summary {
            padding: 4vw;
            gap: 2.5vw;
            border-top-width: 0.3vw;
        }
        .od-summary-row { font-size: 3.2vw; }
        .od-summary-total {
            padding-top: 3.5vw;
            border-top-width: 0.3vw;
            font-size: 4vw;
        }
        .od-summary-total .price { font-size: 4.5vw; }

        .od-address { font-size: 3.2vw; line-height: 1.8; }
        .od-address .name { font-size: 3.6vw; gap: 1.5vw; margin-bottom: 1vw; }
        .od-address .row { gap: 2vw; }
        .od-address .row iconify-icon { font-size: 3.6vw; }

        .od-timeline { padding-left: 8vw; }
        .od-timeline::before {
            left: 2vw;
            top: 2.5vw;
            bottom: 2.5vw;
            width: 0.6vw;
        }
        .od-timeline-item { padding-bottom: 5vw; }
        .od-timeline-dot {
            left: -8vw;
            top: 0.5vw;
            width: 5vw;
            height: 5vw;
            border-width: 1vw;
        }
        .od-timeline-dot iconify-icon { font-size: 2.8vw; }
        .od-timeline-title { font-size: 3.4vw; }
        .od-timeline-time { font-size: 2.8vw; margin-top: 0.8vw; }

        .od-notes {
            font-size: 3.2vw;
            border-radius: 2.5vw;
            padding: 3.5vw;
            gap: 2.5vw;
            border-width: 0.3vw;
            line-height: 1.7;
        }
        .od-notes iconify-icon { font-size: 5vw; }

        .od-back {
            font-size: 3.2vw;
            gap: 1.5vw;
            padding: 2vw 0;
        }
        .od-back iconify-icon { font-size: 4vw; }

        .od-modal { padding: 5vw; }
        .od-modal-box {
            max-width: 100%;
            border-radius: 4vw;
            padding: 6vw;
        }
        .od-modal-head { gap: 2.5vw; margin-bottom: 2.5vw; }
        .od-modal-head .icon-wrap {
            width: 10vw; height: 10vw;
        }
        .od-modal-head .icon-wrap iconify-icon { font-size: 5.5vw; }
        .od-modal-title { font-size: 4.2vw; }
        .od-modal-desc { font-size: 3.2vw; margin-bottom: 4vw; line-height: 1.7; }
        .od-modal textarea {
            padding: 3.5vw 4vw;
            border-radius: 2.5vw;
            font-size: 3.2vw;
            min-height: 30vw;
            border-width: 0.3vw;
        }
        .od-modal-hint { font-size: 2.7vw; margin-top: 1.5vw; }
        .od-modal-actions {
            flex-direction: column-reverse;
            gap: 2.5vw;
            margin-top: 5vw;
        }
        .od-modal-actions .od-btn { padding: 3.5vw 4vw; }
    }
</style>

<div class="od-wrapper">

    {{-- ============================================ --}}
    {{-- STATUS BAR --}}
    {{-- ============================================ --}}
    <div>
        <div class="od-status-bar">
            {{-- Shipping Status --}}
            <span class="od-badge 
                @if($order->shipping_status == 'delivered') od-badge-delivered
                @elseif($order->shipping_status == 'cancelled') od-badge-cancelled
                @elseif($order->shipping_status == 'shipped') od-badge-shipped
                @elseif($order->shipping_status == 'processing') od-badge-processing
                @else od-badge-pending @endif">
                <iconify-icon icon="
                    @if($order->shipping_status == 'delivered') mdi:check-circle
                    @elseif($order->shipping_status == 'cancelled') mdi:close-circle
                    @elseif($order->shipping_status == 'shipped') mdi:truck-fast
                    @elseif($order->shipping_status == 'processing') mdi:package-variant-closed
                    @else mdi:clock-outline @endif">
                </iconify-icon>
                {{ $order->shipping_status_label }}
            </span>

            {{-- Payment Status --}}
            <span class="od-badge {{ $order->payment_status == 'paid' ? 'od-badge-paid' : 'od-badge-unpaid' }}">
                <iconify-icon icon="{{ $order->payment_status == 'paid' ? 'mdi:check-decagram' : 'mdi:clock-alert-outline' }}"></iconify-icon>
                {{ $order->payment_status == 'paid' ? 'Lunas' : 'Belum Bayar' }}
            </span>

            {{-- Cancellation Pending --}}
            @if($order->cancellation_status === 'pending')
                <span class="od-badge od-badge-warning">
                    <iconify-icon icon="mdi:clock-outline"></iconify-icon>
                    Menunggu Persetujuan Admin
                </span>
            @endif

            {{-- Return Status --}}
            @if($order->return_status === 'pending')
                <span class="od-badge od-badge-warning">
                    <iconify-icon icon="mdi:clock-outline"></iconify-icon>
                    Retur Menunggu Persetujuan
                </span>
            @elseif($order->return_status === 'approved')
                <span class="od-badge od-badge-shipped">
                    <iconify-icon icon="mdi:check-decagram"></iconify-icon>
                    Retur Disetujui
                </span>
            @elseif($order->return_status === 'rejected')
                <span class="od-badge od-badge-cancelled">
                    <iconify-icon icon="mdi:close-circle"></iconify-icon>
                    Retur Ditolak
                </span>
            @elseif($order->return_status === 'completed')
                <span class="od-badge od-badge-delivered">
                    <iconify-icon icon="mdi:check-circle"></iconify-icon>
                    Retur Selesai
                </span>
            @endif
        </div>

        {{-- Alasan Pembatalan / Retur --}}
        @if($order->shipping_status === 'cancelled' && $order->cancellation_reason)
            <div class="od-notes" style="margin-top: 1vw; background: #fef2f2; border-color: #fecaca;">
                <iconify-icon icon="mdi:information-outline" style="color: #b91c1c;"></iconify-icon>
                <div>
                    <strong>Alasan Pembatalan:</strong> {{ $order->cancellation_reason }}
                </div>
            </div>
        @endif

        @if($order->return_status === 'approved' && $order->return_reason)
            <div class="od-notes" style="margin-top: 1vw; background: #eff6ff; border-color: #bfdbfe;">
                <iconify-icon icon="mdi:information-outline" style="color: #1d4ed8;"></iconify-icon>
                <div>
                    <strong>Alasan Retur:</strong> {{ $order->return_reason }}
                </div>
            </div>
        @endif

        {{-- Action Row --}}
        <div class="od-action-row">
            @if($order->biteship_order_id)
                <a href="{{ route('customer.orders.tracking', $order) }}" class="od-btn od-btn-primary">
                    <iconify-icon icon="mdi:truck-fast-outline"></iconify-icon>
                    Lacak Pengiriman
                </a>
            @endif

            @if(in_array($order->shipping_status, ['pending', 'processing']) && $order->cancellation_status !== 'pending')
            @if($order->payment_status === 'unpaid')
                <a href="{{ route('customer.midtrans.pay', $order) }}" class="od-btn od-btn-success">
                    <iconify-icon icon="mdi:credit-card-outline"></iconify-icon>
                    Bayar Sekarang
                </a>

                <button onclick="showCancelModal('direct')" class="od-btn od-btn-danger">
                    <iconify-icon icon="mdi:close-circle-outline"></iconify-icon>
                    Batalkan Pesanan
                </button>
            @else
                <button onclick="showCancelModal('request')" class="od-btn od-btn-danger">
                    <iconify-icon icon="mdi:close-circle-outline"></iconify-icon>
                    Minta Pembatalan
                </button>
            @endif
            @endif

            @if($order->shipping_status == 'shipped')
                <form action="{{ route('customer.orders.confirm-received', $order) }}" method="POST" style="display: contents;">
                    @csrf
                    <button type="submit" class="od-btn od-btn-success">
                        <iconify-icon icon="mdi:hand-heart-outline"></iconify-icon>
                        Pesanan Diterima
                    </button>
                </form>
            @endif

            @if($order->can_request_return)
                <button onclick="showReturnModal()" class="od-btn od-btn-outline">
                    <iconify-icon icon="mdi:package-variant-closed-remove"></iconify-icon>
                    Retur / Pengembalian
                </button>
            @endif
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- ITEM PESANAN + SUMMARY --}}
    {{-- ============================================ --}}
    <div class="od-section">
        <div class="od-section-head">
            <iconify-icon icon="mdi:shopping-outline"></iconify-icon>
            Item Pesanan
        </div>

        <div class="od-section-body" style="padding-bottom: 0;">
            <div class="od-items">
                @foreach ($order->items as $item)
                    <div class="od-item">
<div class="od-item-left">
                            <div class="od-item-img">
                                @php
                                    $itemImage = $item->variant ? $item->variant->image_url : null;
                                    if (!$itemImage && $item->product && $item->product->images->isNotEmpty()) {
                                        $firstImage = $item->product->images->first();
                                        $imagePath = ltrim($firstImage->image, '/');
                                        if (str_starts_with($imagePath, 'storage/')) {
                                            $imagePath = substr($imagePath, strlen('storage/'));
                                        }
                                        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($imagePath)) {
                                            $itemImage = \Illuminate\Support\Facades\Storage::url($imagePath);
                                        }
                                    }
                                @endphp

                                @if ($itemImage)
                                    <img src="{{ $itemImage }}" alt="{{ $item->product_name }}">
                                @else
                                    <iconify-icon icon="mdi:image-off-outline"></iconify-icon>
                                @endif
                            </div>
                            <div class="od-item-info">
                                <div class="od-item-name">{{ $item->product_name }}</div>
                                @if ($item->variant_name)
                                    <div class="od-item-variant">
                                        <iconify-icon icon="mdi:tag-outline" style="font-size: 0.7vw; margin-right: 0.2vw;"></iconify-icon>
                                        {{ $item->variant_name }}
                                    </div>
                                @endif
                                <div class="od-item-qty">
                                    {{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                        <div class="od-item-price">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Summary --}}
        <div class="od-summary">
            <div class="od-summary-row">
                <span>Subtotal</span>
                <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="od-summary-row">
                <span>Ongkir</span>
                <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
            </div>
            @if ($order->discount > 0)
                <div class="od-summary-row discount">
                    <span>Diskon</span>
                    <span>-Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="od-summary-total">
                <span>Total</span>
                <span class="price">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- ALAMAT PENGIRIMAN --}}
    {{-- ============================================ --}}
    <div class="od-section">
        <div class="od-section-head">
            <iconify-icon icon="mdi:map-marker-outline"></iconify-icon>
            Alamat Pengiriman
        </div>
        <div class="od-section-body">
            <div class="od-address">
                <div class="name">
                    <iconify-icon icon="mdi:account-circle-outline"></iconify-icon>
                    {{ $order->shipping_name }}
                </div>
                <div class="row" style="margin-bottom: 0.5vw;">
                    <iconify-icon icon="mdi:phone-outline"></iconify-icon>
                    <span>{{ $order->shipping_phone }}</span>
                </div>
                <div class="row" style="align-items: flex-start;">
                    <iconify-icon icon="mdi:home-outline" style="margin-top: 0.3vw;"></iconify-icon>
                    <div>
                        <p>{{ $order->shipping_address }}</p>
                        <p>
                            {{ $order->shipping_district ?? '' }}{{ $order->shipping_district ? ', ' : '' }}{{ $order->shipping_city }}
                        </p>
                        <p>{{ $order->shipping_province }}</p>
                        @if ($order->shipping_postal_code)
                            <p>Kode Pos: <strong>{{ $order->shipping_postal_code }}</strong></p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- INFORMASI PENGIRIMAN --}}
    {{-- ============================================ --}}
    @if ($order->courier)
        <div class="od-section">
            <div class="od-section-head">
                <iconify-icon icon="mdi:truck-delivery-outline"></iconify-icon>
                Informasi Pengiriman
            </div>
            <div class="od-section-body">
                <div class="od-address">
                    <div class="row">
                        <iconify-icon icon="mdi:package-variant-closed"></iconify-icon>
                        <span><strong>Kurir:</strong> {{ strtoupper($order->courier) }}</span>
                    </div>
                    @if ($order->service)
                        <div class="row">
                            <iconify-icon icon="mdi:layers-outline"></iconify-icon>
                            <span><strong>Layanan:</strong> {{ $order->service }}</span>
                        </div>
                    @endif
                    @if ($order->tracking_number)
                        <div class="row">
                            <iconify-icon icon="mdi:barcode-scan"></iconify-icon>
                            <span><strong>No. Resi:</strong> {{ $order->tracking_number }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- CATATAN --}}
    {{-- ============================================ --}}
    @if ($order->notes)
        <div class="od-notes">
            <iconify-icon icon="mdi:note-text-outline"></iconify-icon>
            <div>
                <strong>Catatan:</strong>
                <p style="margin-top: 0.3vw;">{{ $order->notes }}</p>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- BACK LINK --}}
    {{-- ============================================ --}}
    <div>
        <a href="{{ route('customer.orders') }}" class="od-back">
            <iconify-icon icon="mdi:arrow-left"></iconify-icon>
            Kembali ke Riwayat Pesanan
        </a>
    </div>
</div>

{{-- ============================================ --}}
{{-- CANCEL MODAL --}}
{{-- ============================================ --}}
<div id="cancelModal" class="od-modal">
    <div class="od-modal-box">
        <div class="od-modal-head">
            <div class="icon-wrap danger">
                <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
            </div>
            <h3 class="od-modal-title" id="cancelModalTitle">Batalkan Pesanan</h3>
        </div>
        <p class="od-modal-desc" id="cancelModalMessage">
            Anda yakin ingin membatalkan pesanan #{{ $order->order_number }}?
        </p>

        <form action="{{ route('customer.orders.request-cancel', $order) }}"
              method="POST"
              id="cancelForm"
              onsubmit="return validateCancelForm(event)">
            @csrf
            <div style="position: relative;">
                <textarea name="reason"
                          id="cancelReason"
                          rows="4"
                          required
                          minlength="10"
                          maxlength="500"
                          placeholder="Tuliskan alasan pembatalan..."
                          oninput="updateCancelCounter()"></textarea>

                {{-- Counter Karakter --}}
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.4vw;">
                    <p class="od-modal-hint" style="margin: 0;">
                        <iconify-icon icon="mdi:information-outline"></iconify-icon>
                        Minimal <strong>10</strong> karakter
                    </p>
                    <span id="cancelCounter"
                          style="font-size: 0.72vw; font-weight: 600; color: #94a3b8;">
                        0 / 500
                    </span>
                </div>

                {{-- Error Message --}}
                <p id="cancelError"
                   style="display: none; font-size: 0.75vw; color: #dc2626; margin-top: 0.5vw;
                          padding: 0.5vw 0.7vw; background: #fef2f2;
                          border: 0.1vw solid #fecaca; border-radius: 0.4vw;
                          display: none; align-items: center; gap: 0.4vw;">
                    <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                    <span id="cancelErrorText">Alasan minimal 10 karakter.</span>
                </p>
            </div>

            <div class="od-modal-actions">
                <button type="button" onclick="closeCancelModal()" class="od-btn od-btn-outline">
                    Batal
                </button>
                <button type="submit"
                        class="od-btn od-btn-danger"
                        id="cancelSubmitBtn"
                        disabled
                        style="opacity: 0.5; cursor: not-allowed;">
                    <span id="cancelModalSubmitText">Ya, Batalkan</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============================================ --}}
{{-- RETURN MODAL --}}
{{-- ============================================ --}}
<div id="returnModal" class="od-modal">
    <div class="od-modal-box">
        <div class="od-modal-head">
            <div class="icon-wrap primary">
                <iconify-icon icon="mdi:package-variant-closed-remove"></iconify-icon>
            </div>
            <h3 class="od-modal-title">Retur / Pengembalian</h3>
        </div>
        <p class="od-modal-desc">
            Anda yakin ingin mengembalikan pesanan #{{ $order->order_number }}?
            Barang akan dicek oleh admin dan stok dikembalikan setelah retur disetujui.
        </p>

        <form action="{{ route('customer.orders.request-return', $order) }}"
              method="POST"
              id="returnForm"
              onsubmit="return validateReturnForm(event)">
            @csrf
            <div style="position: relative;">
                <textarea name="reason"
                          id="returnReason"
                          rows="4"
                          required
                          minlength="10"
                          maxlength="500"
                          placeholder="Tuliskan alasan retur..."
                          oninput="updateReturnCounter()"></textarea>

                {{-- Counter Karakter --}}
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.4vw;">
                    <p class="od-modal-hint" style="margin: 0;">
                        <iconify-icon icon="mdi:information-outline"></iconify-icon>
                        Minimal <strong>10</strong> karakter
                    </p>
                    <span id="returnCounter"
                          style="font-size: 0.72vw; font-weight: 600; color: #94a3b8;">
                        0 / 500
                    </span>
                </div>

                {{-- Error Message --}}
                <p id="returnError"
                   style="display: none; font-size: 0.75vw; color: #dc2626; margin-top: 0.5vw;
                          padding: 0.5vw 0.7vw; background: #fef2f2;
                          border: 0.1vw solid #fecaca; border-radius: 0.4vw;
                          display: none; align-items: center; gap: 0.4vw;">
                    <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                    <span id="returnErrorText">Alasan minimal 10 karakter.</span>
                </p>
            </div>

            <div class="od-modal-actions">
                <button type="button" onclick="closeReturnModal()" class="od-btn od-btn-outline">
                    Batal
                </button>
                <button type="submit"
                        class="od-btn od-btn-primary"
                        id="returnSubmitBtn"
                        disabled
                        style="opacity: 0.5; cursor: not-allowed;">
                    <iconify-icon icon="mdi:send-outline"></iconify-icon>
                    Kirim Retur
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const MIN_CHARS = 10;
    const MAX_CHARS = 500;
    function showCancelModal(type) {
        var title = document.getElementById('cancelModalTitle');
        var message = document.getElementById('cancelModalMessage');
        var submitText = document.getElementById('cancelModalSubmitText');
        var form = document.getElementById('cancelForm');
        var textarea = document.getElementById('cancelReason');

        // 🔥 Set action & text sesuai tipe
        if (type === 'direct') {
            title.textContent = 'Batalkan Pesanan';
            message.textContent = 'Anda yakin ingin membatalkan pesanan #{{ $order->order_number }}? Pesanan akan dibatalkan langsung.';
            submitText.textContent = 'Ya, Batalkan Sekarang';
            form.action = '{{ route("customer.orders.cancel-direct", $order) }}';
        } else {
            title.textContent = 'Minta Pembatalan Pesanan';
            message.textContent = 'Anda yakin ingin meminta pembatalan pesanan #{{ $order->order_number }}? Permintaan ini perlu persetujuan admin.';
            submitText.textContent = 'Kirim Permintaan';
            form.action = '{{ route("customer.orders.request-cancel", $order) }}';
        }

        // 🔥 Reset form
        textarea.value = '';
        hideError('cancelError');
        updateCancelCounter();

        document.getElementById('cancelModal').classList.add('active');
        document.body.style.overflow = 'hidden';

        // 🔥 Auto focus textarea
        setTimeout(function() { textarea.focus(); }, 300);
    }

    function closeCancelModal() {
        document.getElementById('cancelModal').classList.remove('active');
        document.body.style.overflow = '';

        // 🔥 Reset textarea + state
        document.getElementById('cancelReason').value = '';
        hideError('cancelError');
        updateCancelCounter();
    }

    function updateCancelCounter() {
        var textarea = document.getElementById('cancelReason');
        var counter = document.getElementById('cancelCounter');
        var submitBtn = document.getElementById('cancelSubmitBtn');
        var errorEl = document.getElementById('cancelError');
        var errorText = document.getElementById('cancelErrorText');

        var length = textarea.value.trim().length;

        // 🔥 Update counter
        counter.textContent = length + ' / ' + MAX_CHARS;

        // 🔥 Update counter color
        if (length === 0) {
            counter.style.color = '#94a3b8';
        } else if (length < MIN_CHARS) {
            counter.style.color = '#dc2626';
        } else if (length >= MIN_CHARS && length < MAX_CHARS * 0.9) {
            counter.style.color = '#059669';
        } else {
            counter.style.color = '#f59e0b';
        }

        // 🔥 Update button state
        if (length >= MIN_CHARS) {
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
            submitBtn.style.cursor = 'pointer';
            hideError('cancelError');
        } else {
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.5';
            submitBtn.style.cursor = 'not-allowed';

            if (length > 0) {
                errorText.textContent = 'Alasan minimal ' + MIN_CHARS + ' karakter. Sisa ' + (MIN_CHARS - length) + ' karakter lagi.';
                showError('cancelError');
            } else {
                hideError('cancelError');
            }
        }
    }

    function validateCancelForm(event) {
        var textarea = document.getElementById('cancelReason');
        var length = textarea.value.trim().length;

        if (length < MIN_CHARS) {
            event.preventDefault();
            var errorText = document.getElementById('cancelErrorText');
            errorText.textContent = 'Alasan minimal ' + MIN_CHARS + ' karakter.';
            showError('cancelError');
            textarea.focus();
            return false;
        }
        return true;
    }

    document.getElementById('cancelModal').addEventListener('click', function(e) {
        if (e.target === this) closeCancelModal();
    });

    function showReturnModal() {
        var textarea = document.getElementById('returnReason');

        // 🔥 Reset form
        textarea.value = '';
        hideError('returnError');
        updateReturnCounter();

        document.getElementById('returnModal').classList.add('active');
        document.body.style.overflow = 'hidden';

        // 🔥 Auto focus textarea
        setTimeout(function() { textarea.focus(); }, 300);
    }

    function closeReturnModal() {
        document.getElementById('returnModal').classList.remove('active');
        document.body.style.overflow = '';

        // 🔥 Reset textarea + state
        document.getElementById('returnReason').value = '';
        hideError('returnError');
        updateReturnCounter();
    }

    function updateReturnCounter() {
        var textarea = document.getElementById('returnReason');
        var counter = document.getElementById('returnCounter');
        var submitBtn = document.getElementById('returnSubmitBtn');
        var errorEl = document.getElementById('returnError');
        var errorText = document.getElementById('returnErrorText');

        var length = textarea.value.trim().length;

        // 🔥 Update counter
        counter.textContent = length + ' / ' + MAX_CHARS;

        // 🔥 Update counter color
        if (length === 0) {
            counter.style.color = '#94a3b8';
        } else if (length < MIN_CHARS) {
            counter.style.color = '#dc2626';
        } else if (length >= MIN_CHARS && length < MAX_CHARS * 0.9) {
            counter.style.color = '#059669';
        } else {
            counter.style.color = '#f59e0b';
        }

        // 🔥 Update button state
        if (length >= MIN_CHARS) {
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
            submitBtn.style.cursor = 'pointer';
            hideError('returnError');
        } else {
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.5';
            submitBtn.style.cursor = 'not-allowed';

            if (length > 0) {
                errorText.textContent = 'Alasan minimal ' + MIN_CHARS + ' karakter. Sisa ' + (MIN_CHARS - length) + ' karakter lagi.';
                showError('returnError');
            } else {
                hideError('returnError');
            }
        }
    }

    function validateReturnForm(event) {
        var textarea = document.getElementById('returnReason');
        var length = textarea.value.trim().length;

        if (length < MIN_CHARS) {
            event.preventDefault();
            var errorText = document.getElementById('returnErrorText');
            errorText.textContent = 'Alasan minimal ' + MIN_CHARS + ' karakter.';
            showError('returnError');
            textarea.focus();
            return false;
        }
        return true;
    }

    function showError(elementId) {
        var el = document.getElementById(elementId);
        if (el) el.style.display = 'flex';
    }

    function hideError(elementId) {
        var el = document.getElementById(elementId);
        if (el) el.style.display = 'none';
    }


    document.getElementById('returnModal').addEventListener('click', function(e) {
        if (e.target === this) closeReturnModal();
    });

    // Close on ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCancelModal();
            closeReturnModal();
        }
    });
</script>
@endpush

@endsection