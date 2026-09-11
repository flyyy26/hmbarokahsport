@extends('layouts.customer')

@section('title', 'Checkout - Barokah Sport')

@section('content')
    
    <style>
        /* ============================================
           CHECKOUT CONTAINER
           ============================================ */
        .checkout-container {
            max-width: 90vw;
            margin: 0 auto;
            padding: 1.5vw 3vw;
        }

        /* ============================================
           HEADER
           ============================================ */
        .checkout-header {
            margin-bottom: 2vw;
        }

        .checkout-header h1 {
            font-size: 2.7vw;
            font-weight: 700;
            color: #0f172a;
            font-family: heading;
            text-transform: uppercase;
        }

        .checkout-header p {
            font-size: 0.85vw;
            color: #94a3b8;
            margin-top: 0.2vw;
        }

        /* ============================================
           ALERT
           ============================================ */
        .checkout-alert {
            padding: 1vw 1.5vw;
            border-radius: 0.8vw;
            margin-bottom: 1.5vw;
            font-size: 0.85vw;
        }

        .checkout-alert.error {
            background: #fef2f2;
            border: 0.1vw solid #fca5a5;
            color: #991b1b;
        }

        .checkout-alert .alert-title {
            font-weight: 600;
        }

        .checkout-alert .alert-list {
            margin-top: 0.5vw;
            padding-left: 1.5vw;
        }

        .checkout-alert .alert-list li {
            list-style: disc;
        }

        /* ============================================
           CHECKOUT GRID
           ============================================ */
        .checkout-grid {
            display: grid;
            grid-template-columns: 68% 32%;
            gap: 1.5vw;
        }

        /* ============================================
           FORM SECTION
           ============================================ */
        .checkout-section {
            background: #ffffff;
            border: 0.1vw solid #e2e8f0;
            border-radius: 1.2vw;
            padding: 1.5vw;
            margin-bottom: 1.5vw;
        }

        .checkout-section:last-child {
            margin-bottom: 0;
        }

        .checkout-section .section-title {
            font-size: 1.1vw;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.2vw;
        }

        .checkout-section .section-subtitle {
            font-size: 0.8vw;
            color: #94a3b8;
            margin-bottom: 1vw;
        }

        /* ============================================
           FORM ELEMENTS
           ============================================ */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1vw;
        }

        .form-grid .full-width {
            grid-column: 1 / -1;
        }
        .form-grid-custom{
            display:flex;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.3vw;
        }

        .form-group label {
            font-size: 0.8vw;
            font-weight: 500;
            color: #0f172a;
        }

        .form-group label .required {
            color: #ef4444;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.7vw 1vw;
            border: 0.1vw solid #e2e8f0;
            border-radius: 0.7vw;
            font-size: 0.8vw;
            color: #0f172a;
            background: #ffffff;
            transition: border-color 0.2s, box-shadow 0.2s;
            font-family: inherit;
        }
        .checkout-section textarea {
            width: 100%;
            padding: 0.7vw 1vw;
            border: 0.1vw solid #e2e8f0;
            border-radius: 0.7vw;
            font-size: 0.8vw;
            color: #0f172a;
            background: #ffffff;
            transition: border-color 0.2s, box-shadow 0.2s;
            font-family: inherit;
        }
        .checkout-section textarea:focus {
            outline: none;
            border-color: #076694;
            box-shadow: 0 0 0 0.2vw rgba(59, 130, 246, 0.1);
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #076694;
            box-shadow: 0 0 0 0.2vw rgba(59, 130, 246, 0.1);
        }
        

        .form-group input:disabled,
        .form-group select:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            background: #f1f5f9;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 5vw;
        }

        .form-group .helper-text {
            font-size: 0.65vw;
            color: #94a3b8;
            margin-top: 0.2vw;
        }

        .form-group .error-text {
            font-size: 0.7vw;
            color: #ef4444;
            margin-top: 0.2vw;
        }

        /* ============================================
           SHIPPING COST DISPLAY
           ============================================ */
        .shipping-cost-display {
            width: 100%;
            padding: 0.7vw 1vw;
            border: 0.1vw solid #e2e8f0;
            border-radius: 0.7vw;
            font-size: 0.8vw;
            color: #94a3b8;
            background: #f8fafc;
            min-height: 3vw;
            display: flex;
            align-items: center;
        }

        .shipping-cost-display .loading {
            color: #076694;
        }

        .shipping-cost-display .success {
            color: #22c55e;
        }

        .shipping-cost-display .error {
            color: #ef4444;
        }

        .shipping-picker-button {
            width: 100%;
            min-height: 4.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.85rem 1rem;
            border: 1px solid #cbd5e1;
            border-radius: 0.75rem;
            background: #fff;
            color: #0f172a;
            text-align: left;
            font: inherit;
            cursor: pointer;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .shipping-picker-button:hover:not(:disabled) {
            border-color: #0284c7;
            box-shadow: 0 0 0 0.2rem rgba(2, 132, 199, .1);
        }

        .shipping-picker-button:disabled { cursor: not-allowed; background: #f8fafc; color: #94a3b8; }
        .shipping-picker-button .picker-title { display: block; font-weight: 600; font-size: 0.84rem; }
        .shipping-picker-button .picker-subtitle { display: block; margin-top: .2rem; color: #64748b; font-size: .75rem; }
        .shipping-picker-button .picker-arrow { color: #0284c7; font-size: 1.25rem; }

        .shipping-popup { position: fixed; inset: 0; z-index: 10001; display: grid; place-items: center; padding: 1rem; }
        .shipping-popup.hidden { display: none; }
        .shipping-popup-backdrop { position: absolute; inset: 0; background: rgba(15, 23, 42, .55); backdrop-filter: blur(2px); }
        .shipping-popup-card { position: relative; width: min(100%, 35rem); max-height: min(80vh, 44rem); overflow: hidden; display: flex; flex-direction: column; background: #fff; border-radius: 1rem; box-shadow: 0 1.5rem 4rem rgba(15, 23, 42, .3); }
        .shipping-popup-header { display: flex; align-items: center; justify-content: space-between; padding: 1.15rem 1.25rem; border-bottom: 1px solid #e2e8f0; }
        .shipping-popup-header h3 { margin: 0; color: #0f172a; font-size: 1.05rem; }
        .shipping-popup-close { border: 0; background: transparent; color: #334155; cursor: pointer; font-size: 1.7rem; line-height: 1; }
        .shipping-popup-body { overflow-y: auto; padding: 1rem 1.25rem; }
        .shipping-popup-note { margin: 0 0 .9rem; padding: .7rem .8rem; border-radius: .6rem; background: #f0f9ff; color: #075985; font-size: .78rem; }
        .shipping-courier-group + .shipping-courier-group { margin-top: 1rem; }
        .shipping-courier-name { margin: 0 0 .45rem; color: #0f172a; font-size: .86vw; font-weight: 600; }
        .shipping-option { width: 100%; display: flex; align-items: center; justify-content: space-between; gap: .8rem; padding: .85rem .9rem; border: 1px solid #e2e8f0; border-radius: .65rem; background: #fff; text-align: left; cursor: pointer; font: inherit; }
        .shipping-option + .shipping-option { margin-top: .5rem; }
        .shipping-option:hover, .shipping-option.selected { border-color: #0284c7; background: #f0f9ff; }
        .shipping-option-name { display: block; color: #0f172a; font-size: .8vw; font-weight: 400; }
        .shipping-option-meta { display: block; margin-top: .18rem; color: #64748b; font-size: .72rem; }
        .shipping-option-price { color: #075985; font-size: .86rem; font-weight: 700; white-space: nowrap; }
        .shipping-popup-footer { padding: .9rem 1.25rem 1.25rem; border-top: 1px solid #e2e8f0; }
        .shipping-popup-confirm { width: 100%; padding: .8rem 1rem; border: 0; border-radius: .6rem; background: #076694; color: #fff; font: inherit; font-weight: 700; cursor: pointer; }
        .shipping-popup-confirm:disabled { background: #cbd5e1; cursor: not-allowed; }

        /* ============================================
           PAYMENT METHOD
           ============================================ */
        .payment-options {
            display: flex;
            flex-direction: column;
            gap: 0.6vw;
        }

        .payment-option {
            display: flex;
            align-items: center;
            gap: 0.8vw;
            padding: 0.8vw 1.2vw;
            border: 0.1vw solid #e2e8f0;
            border-radius: 0.7vw;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .payment-option:hover {
            border-color: #93c5fd;
        }

        .payment-option input[type="radio"] {
            width: 1.2vw;
            height: 1.2vw;
            accent-color: #076694;
            flex-shrink: 0;
            cursor: pointer;
        }

        .payment-option .payment-info {
            display: flex;
            flex-direction: column;
        }

        .payment-option .payment-info .payment-name {
            font-size: 0.85vw;
            font-weight: 500;
            color: #0f172a;
        }

        .payment-option .payment-info .payment-desc {
            font-size: 0.7vw;
            color: #94a3b8;
        }

        .payment-option.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .payment-option.disabled input[type="radio"] {
            cursor: not-allowed;
        }

        /* ============================================
           GUEST INFO
           ============================================ */
        .guest-info {
            padding: 0.8vw 1.2vw;
            border-radius: 0.7vw;
            background: #eff6ff;
            border: 0.1vw solid #93c5fd;
            font-size: 0.8vw;
            color: #1d4ed8;
            margin-top: 1vw;
        }

        .guest-info .guest-title {
            font-weight: 600;
        }

        .guest-info .guest-text {
            margin-top: 0.2vw;
        }

        .guest-info .guest-hint {
            font-size: 0.7vw;
            color: #60a5fa;
            margin-top: 0.2vw;
        }

        /* ============================================
           WEIGHT DISPLAY
           ============================================ */
        .weight-display {
            font-size: 0.8vw;
            color: #94a3b8;
            margin-top: 0.5vw;
        }

        .weight-display .weight-value {
            font-weight: 600;
            color: #0f172a;
        }

        .weight-display .weight-unit {
            color: #94a3b8;
        }

        .weight-display .weight-items {
            font-size: 0.65vw;
            color: #94a3b8;
            margin-left: 0.5vw;
        }

        /* ============================================
           SUMMARY
           ============================================ */
        .checkout-summary {
            background: #ffffff;
            border: 0.1vw solid #e2e8f0;
            border-radius: 1.2vw;
            padding: 1.5vw;
            position: sticky;
            top: 8vw;
            height: fit-content;
        }

        .checkout-summary .summary-title {
            font-size: 1.1vw;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 1vw;
        }

        .checkout-summary .summary-items {
            max-height: 16vw;
            overflow-y: auto;
            margin-bottom: 1vw;
        }

        .checkout-summary .summary-items::-webkit-scrollbar {
            width: 0.2vw;
        }

        .checkout-summary .summary-items::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 0.2vw;
        }

        .checkout-summary .summary-items::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 0.2vw;
        }

        .checkout-summary .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 0.4vw 0;
            border-bottom: 0.05vw solid #f1f5f9;
            font-size: 0.8vw;
        }

        .checkout-summary .summary-item:last-child {
            border-bottom: none;
        }

        .checkout-summary .summary-item .item-info {
            flex: 1;
            min-width: 0;
        }

        .checkout-summary .summary-item .item-name {
            font-weight: 500;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .checkout-summary .summary-item .item-variant {
            font-size: 0.65vw;
            color: #94a3b8;
        }

        .checkout-summary .summary-item .item-qty {
            font-size: 0.65vw;
            color: #94a3b8;
        }

        .checkout-summary .summary-item .item-price {
            font-weight: 600;
            color: #0f172a;
            margin-left: 0.5vw;
            white-space: nowrap;
        }

        .checkout-summary .summary-divider {
            border-top: 0.1vw solid #e2e8f0;
            margin: 0.5vw 0;
        }

        .checkout-summary .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.8vw;
            padding: 0.3vw 0;
        }

        .checkout-summary .summary-row .row-label {
            color: #94a3b8;
        }

        .checkout-summary .summary-row .row-value {
            font-weight: 500;
            color: #0f172a;
        }

        .checkout-summary .summary-total {
            display: flex;
            justify-content: space-between;
            font-size: 1vw;
            font-weight: 700;
            color: #0f172a;
            padding-top: 0.5vw;
            border-top: 0.15vw solid #0f172a;
            margin-top: 0.3vw;
        }

        .checkout-summary .btn-submit {
            display: block;
            width: 100%;
            padding: 0.8vw 1.5vw;
            background: #0f172a;
            color: #ffffff;
            font-size: 0.9vw;
            font-weight: 600;
            text-align: center;
            border: none;
            border-radius: 0.7vw;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 1vw;
            font-family: inherit;
        }

        .checkout-summary .btn-submit:hover {
            background: #1e293b;
            transform: translateY(-0.1vw);
            box-shadow: 0 0.2vw 0.8vw rgba(15, 23, 42, 0.15);
        }

        .checkout-summary .btn-submit:active {
            transform: scale(0.97);
        }

        .checkout-summary .btn-back {
            display: block;
            text-align: center;
            font-size: 0.8vw;
            color: #94a3b8;
            text-decoration: none;
            margin-top: 0.8vw;
            transition: color 0.2s;
        }

        .checkout-summary .btn-back:hover {
            color: #0f172a;
        }

        /* ============================================
           VOUCHER DISPLAY IN SUMMARY (Right Side)
           ============================================ */
        .voucher-summary-section {
            border-top: 0.1vw solid #e2e8f0;
            padding-top: 0.8vw;
            margin-top: 0.5vw;
        }

        .voucher-summary-section .voucher-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.5vw;
        }

        .voucher-summary-section .voucher-header .voucher-label {
            font-size: 0.8vw;
            font-weight: 600;
            color: #0f172a;
        }

        .voucher-summary-section .voucher-header .btn-open-voucher {
            background: none;
            border: none;
            color: #076694;
            font-size: 0.7vw;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            font-family: inherit;
            display: flex;
            align-items: center;
            gap: 0.3vw;
        }

        .voucher-summary-section .voucher-header .btn-open-voucher:hover {
            background: #eff6ff;
        }

        .voucher-summary-section .voucher-header .btn-open-voucher .icon {
            font-size: 0.9vw;
        }

        /* Applied Voucher in Summary */
        .applied-voucher-summary {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5vw 0.8vw;
            background: #f0fdf4;
            border: 0.1vw solid #86efac;
            border-radius: 0.6vw;
            margin-top: 0.3vw;
            margin-bottom:.6vw;
        }

        .applied-voucher-summary .voucher-info {
            display: flex;
            align-items: center;
            gap: 0.5vw;
            flex-wrap: wrap;
        }

        .applied-voucher-summary .voucher-info .code {
            font-family: monospace;
            font-weight: 700;
            font-size: 0.65vw;
            padding: 0.1vw 0.4vw;
            background: #22c55e;
            color: white;
            border-radius: 0.2vw;
            letter-spacing: 0.05em;
        }

        .applied-voucher-summary .voucher-info .name {
            font-size: 0.7vw;
            color: #0f172a;
        }

        .applied-voucher-summary .voucher-info .discount {
            font-weight: 600;
            font-size: 0.75vw;
            color: #16a34a;
        }

        .applied-voucher-summary .btn-remove-voucher-sm {
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 0.1vw 0.3vw;
            border-radius: 0.2vw;
            transition: all 0.2s;
            font-size: 0.8vw;
        }

        .applied-voucher-summary .btn-remove-voucher-sm:hover {
            color: #ef4444;
            background: #fef2f2;
        }

        

        /* ============================================
           RESPONSIVE
           ============================================ */
        @media (max-width: 1024px) {
            .checkout-grid {
                grid-template-columns: 1fr;
                gap: 2vw;
            }

            .checkout-summary {
                position: static;
            }

            .voucher-popup {
                width: 70vw;
                right: -70vw;
            }
        }

        /* ============================================
        🔥 RESPONSIVE MOBILE (max-width: 768px)
        ============================================ */
        @media (max-width: 768px) {
            .checkout-container {
                max-width: 100%;
                padding: 0;
                background: #f5f6f8;
            }

            /* ============================================
            HEADER - MOBILE
            ============================================ */
            .checkout-header {
                padding: 4.5vw 4.5vw;
                background: #ffffff;
                margin-bottom: 0;
                border-bottom: 0.2vw solid #f1f5f9;
                position: sticky;
                top: 0;
                z-index: 10;
            }

            .checkout-header h1 {
                font-size: 6vw;
                font-weight: 700;
                color: #0f172a;
                position: relative;
                display: inline-block;
            }

            .checkout-header p {
                font-size: 3vw;
                margin-top: 0.2vw;
                color: #64748b;
            }

            /* ============================================
            ALERT - MOBILE
            ============================================ */
            .checkout-alert {
                font-size: 2.5vw;
                padding: 2.5vw 4vw;
                border-radius: 1.5vw;
                margin: 2vw 4vw;
                border-width: 0.15vw;
            }

            .checkout-alert .alert-title {
                font-size: 2.8vw;
                margin-bottom: 0.5vw;
            }

            .checkout-alert .alert-list {
                padding-left: 3vw;
            }

            .checkout-alert .alert-list li {
                font-size: 2.3vw;
                margin-bottom: 0.3vw;
            }

            /* ============================================
            CHECKOUT GRID - MOBILE
            ============================================ */
            .checkout-grid {
                grid-template-columns: 1fr;
                gap: 0;
                padding: 0;
            }

            /* ============================================
            FORM SECTION - MOBILE
            ============================================ */
            .checkout-section {
                background: #ffffff;
                border: none;
                border-radius: 0;
                padding: 4vw 4.5vw;
                margin-bottom: 2vw;
                box-shadow: 0 0.2vw 1.5vw rgba(0, 0, 0, 0.04);
            }

            .checkout-section:last-child {
                margin-bottom: 0;
            }

            .checkout-section .section-title {
                font-size: 3.8vw;
                font-weight: 700;
                color: #0f172a;
                margin-bottom: 0.5vw;
                display: flex;
                align-items: center;
                gap: 1.5vw;
            }

            .checkout-section .section-title .title-icon {
                font-size: 4.5vw;
            }

            .checkout-section .section-subtitle {
                font-size: 3vw;
                color: #94a3b8;
                margin-bottom: 2.5vw;
            }

            /* ============================================
            FORM ELEMENTS - MOBILE
            ============================================ */
            .form-grid {
                grid-template-columns: 1fr;
                gap: 2.5vw;
            }

            .form-grid .full-width {
                grid-column: 1;
            }

            .form-group {
                gap: 0.8vw;
            }

            .form-group label {
                font-size: 3vw;
                font-weight: 600;
                color: #1e293b;
            }

            .form-group label .required {
                color: #ef4444;
                font-size: 3vw;
            }

            .form-group input,
            .form-group select,
            .form-group textarea {
                font-size: 3vw;
                padding: 2.5vw 3.5vw;
                border-radius: 1.5vw;
                border: 0.15vw solid #e2e8f0;
                background: #f8fafc;
                color: #0f172a;
                transition: all 0.3s ease;
            }

            .form-group input:focus,
            .form-group select:focus,
            .form-group textarea:focus {
                border-color: #076694;
                background: #ffffff;
                box-shadow: 0 0 0 0.4vw rgba(7, 102, 148, 0.08);
            }

            .checkout-section textarea {
                font-size: 3vw;
                padding: 2.5vw 3.5vw;
                border-radius: 1.5vw;
                border: 0.15vw solid #e2e8f0;
                background: #f8fafc;
                color: #0f172a;
                transition: all 0.3s ease;
            }
            .checkout-section textarea:focus {
                outline: none;
                border-color: #076694;
                box-shadow: 0 0 0 0.2vw rgba(59, 130, 246, 0.1);
            }

            .form-group input::placeholder,
            .form-group select::placeholder,
            .form-group textarea::placeholder {
                color: #94a3b8;
            }

            .form-group .helper-text {
                font-size: 2.7vw;
                color: #94a3b8;
                margin-top: 0.5vw;
            }

            .form-group .error-text {
                font-size: 2.2vw;
                color: #ef4444;
                margin-top: 0.5vw;
                padding: 0.5vw 1.5vw;
                background: #fef2f2;
                border-radius: 0.8vw;
            }

            /* ============================================
            SHIPPING COST - MOBILE
            ============================================ */
            .shipping-cost-display {
                font-size: 3vw;
                padding: 2.5vw 3.5vw;
                min-height: 8vw;
                border-radius: 1.5vw;
                border: 0.15vw solid #e2e8f0;
                background: #f8fafc;
            }

            .shipping-cost-display .loading {
                color: #076694;
            }

            .shipping-cost-display .success {
                color: #22c55e;
            }

            .shipping-cost-display .error {
                color: #ef4444;
            }

            .shipping-popup{
                padding: 0;
                transform:translateX(0);
                transition:.3s all;
            }
            .shipping-popup.hidden{
                display:grid !important;
                transform:translateX(-100%);
            }
            .shipping-popup-card {
                max-height: 100vh;
                border-radius: 0;
                box-shadow: none;
            }
            .shipping-courier-name {
                font-size: 3.5vw;
                font-weight: 500;
            }
            .shipping-option-name {
                font-size: 3vw;
                font-weight: 400;
            }

            .popup-voucher-list .list-title {
                font-size: 3.8vw;
                margin-bottom: 2.2vw;
            }
            .popup-voucher-input {
                flex: 1;
                padding: 2.6vw 3.5vw;
                border: 0.1vw solid #e2e8f0;
                border-radius: 1.7vw;
                font-size: 3.5vw;
                color: #0f172a;
                background: #ffffff;
                font-family: inherit;
                text-transform: uppercase;
                outline: none;
                letter-spacing: 0;
            }
            .popup-voucher-input-group {
                display: flex;
                gap: 2.5vw;
                margin-bottom: 4vw;
            }
            .popup-btn-apply {
                padding: 0.4vw 4vw;
                background: #075985;
                color: #ffffff;
                border: none;
                border-radius: 1.7vw;
                font-size: 3vw;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s ease;
                font-family: inherit;
                white-space: nowrap;
            }

            /* ============================================
            WEIGHT DISPLAY - MOBILE
            ============================================ */
            .weight-display {
                font-size: 2.6vw;
                color: #94a3b8;
                margin-top: 2vw;
                padding: 1.5vw 0;
                border-top: 0.1vw solid #f1f5f9;
                display: flex;
                align-items: center;
                gap: 1vw;
                flex-wrap: wrap;
            }

            .weight-display .weight-value {
                font-weight: 700;
                color: #0f172a;
                font-size: 2.8vw;
            }

            .weight-display .weight-unit {
                color: #94a3b8;
            }

            .weight-display .weight-items {
                font-size: 2.2vw;
                color: #94a3b8;
                margin-left: 0;
            }

            /* ============================================
            PAYMENT METHOD - MOBILE
            ============================================ */
            .payment-options {
                gap: 1.5vw;
            }

            .payment-option {
                padding: 2.5vw 3.5vw;
                border-radius: 1.5vw;
                border: 0.15vw solid #e2e8f0;
                gap: 2vw;
                transition: all 0.3s ease;
            }

            .payment-option:active {
                transform: scale(0.98);
            }

            .payment-option input[type="radio"] {
                width: 3.5vw;
                height: 3.5vw;
                accent-color: #076694;
                flex-shrink: 0;
            }

            .payment-option .payment-info .payment-name {
                font-size: 3vw;
                font-weight: 600;
                color: #0f172a;
            }

            .payment-option .payment-info .payment-desc {
                font-size: 2.2vw;
                color: #94a3b8;
                margin-top: 0.2vw;
            }

            /* ============================================
            GUEST INFO - MOBILE
            ============================================ */
            .guest-info {
                font-size: 2.6vw;
                padding: 3vw 4vw;
                border-radius: 1.5vw;
                margin-top: 2vw;
                background: #eff6ff;
                border: 0.15vw solid #93c5fd;
            }

            .guest-info .guest-title {
                font-size: 2.8vw;
                font-weight: 600;
                color: #1d4ed8;
            }

            .guest-info .guest-text {
                margin-top: 0.5vw;
                font-size: 2.4vw;
                color: #2563eb;
            }

            .guest-info .guest-hint {
                font-size: 2.2vw;
                color: #60a5fa;
                margin-top: 0.5vw;
            }

            /* ============================================
            SUMMARY - MOBILE (FIXED BOTTOM)
            ============================================ */
            .checkout-summary {
                z-index: 50;
                background: #ffffff;
                border: none;
                padding: 3vw 4.5vw 4vw;
                overflow-y: auto;
                margin-top:3vw;
            }

            .checkout-summary .summary-title {
                font-size: 3.8vw;
                font-weight: 700;
                color: #0f172a;
                margin-bottom: 1.5vw;
                display: flex;
                align-items: center;
                gap: 1vw;
            }

            .checkout-summary .summary-title .summary-badge {
                background: #076694;
                color: #ffffff;
                font-size: 2vw;
                padding: 0.3vw 1.5vw;
                border-radius: 100vw;
                font-weight: 500;
                margin-left: auto;
            }

            .checkout-summary .summary-items {
                max-height: 20vh;
                overflow-y: auto;
                margin-bottom: 1.5vw;
            }

            .checkout-summary .summary-items::-webkit-scrollbar {
                width: 0.3vw;
            }

            .checkout-summary .summary-items::-webkit-scrollbar-track {
                background: #f1f5f9;
                border-radius: 0.3vw;
            }

            .checkout-summary .summary-items::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 0.3vw;
            }

            .checkout-summary .summary-item {
                font-size: 2.6vw;
                padding: 1.5vw 0;
                border-bottom: 0.1vw solid #f1f5f9;
            }

            .checkout-summary .summary-item .item-name {
                font-size: 3vw;
                font-weight: 500;
                padding-right: 6vw;
            }

            .checkout-summary .summary-item .item-variant {
                font-size: 3vw;
                color: #94a3b8;
            }

            .checkout-summary .summary-item .item-qty {
                font-size: 3vw;
                color: #94a3b8;
            }

            .checkout-summary .summary-item .item-price {
                font-size: 3vw;
                font-weight: 700;
                color: #0f172a;
            }

            .checkout-summary .summary-divider {
                border-top: 0.1vw solid #e2e8f0;
                margin: 2vw 0;
            }

            .checkout-summary .summary-row {
                font-size: 3vw;
                padding: 0.8vw 0;
            }

            .checkout-summary .summary-row .row-label {
                color: #94a3b8;
            }

            .checkout-summary .summary-row .row-value {
                font-weight: 500;
                color: #0f172a;
            }

            .checkout-summary .summary-total {
                font-size: 3.5vw;
                font-weight: 700;
                color: #0f172a;
                padding-top: 3vw;
                border-top: 0.2vw solid #076694;
                margin-top: 1.5vw;
            }

            /* ============================================
            VOUCHER - MOBILE
            ============================================ */
            .voucher-summary-section {
                border-top: 0.1vw solid #f1f5f9;
                padding-top: 3.5vw;
                margin-top: 2.5vw;
            }

            .voucher-summary-section .voucher-header .voucher-label {
                font-size: 3vw;
                font-weight: 600;
                color: #0f172a;
            }
            .voucher-summary-section .voucher-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 2.5vw;
            }

            .voucher-summary-section .voucher-header .btn-open-voucher {
                font-size: 3vw;
                padding: 0.5vw 1.5vw;
                border-radius: 1vw;
                background: #f0f9ff;
                color: #076694;
            }

            .applied-voucher-summary {
                padding: 2vw 3vw;
                border-radius: 1.2vw;
                border-width: 0.15vw;
                flex-wrap: wrap;
                gap: 1vw;
                margin-bottom: 2.6vw;
            }

            .applied-voucher-summary .voucher-info .code {
                font-size: 2.2vw;
                padding: 0.3vw 1vw;
            }

            .applied-voucher-summary .voucher-info .name {
                font-size: 3vw;
            }

            .applied-voucher-summary .voucher-info .discount {
                font-size: 3vw;
            }

            .applied-voucher-summary .btn-remove-voucher-sm {
                font-size: 2.8vw;
                padding: 0.3vw 0.8vw;
            }

            /* ============================================
            BUTTON SUBMIT - MOBILE (FIXED)
            ============================================ */
            .checkout-summary .btn-submit {
                font-size: 3.2vw;
                padding: 2.5vw 4vw;
                border-radius: 1.5vw;
                margin-top: 3.5vw;
                background: linear-gradient(135deg, #076694 0%, #0a8ab8 100%);
                box-shadow: 0 1vw 3vw rgba(7, 102, 148, 0.25);
                width: 100%;
                font-weight: 700;
                letter-spacing: 0.05vw;
                transition: all 0.3s ease;
            }

            .checkout-summary .btn-submit:active {
                transform: scale(0.97);
                box-shadow: 0 0.5vw 1.5vw rgba(7, 102, 148, 0.15);
            }

            .checkout-summary .btn-back {
                font-size: 3vw;
                margin-top: 1.5vw;
                color: #94a3b8;
                text-align: center;
                display: block;
                padding: 1vw 0;
            }

            .checkout-summary .btn-back:active {
                color: #0f172a;
            }

            /* ============================================
            OVERLAY UNTUK SUMMARY
            ============================================ */
            .checkout-summary-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.4);
                z-index: 40;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
            }

            .checkout-summary-overlay.active {
                opacity: 1;
                visibility: visible;
            }

            /* ============================================
            BADGE / COUNTER
            ============================================ */
            .summary-items-count {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: #f1f5f9;
                color: #475569;
                font-size: 2vw;
                padding: 0.2vw 1.2vw;
                border-radius: 100vw;
                margin-left: 1vw;
                font-weight: 600;
            }
        }
        /* ============================================
           UTILITY
           ============================================ */
        .hidden {
            display: none !important;
        }

        .loading-opacity {
            opacity: 0.6;
            pointer-events: none;
        }

        .text-center {
            text-align: center;
        }

        .mt-1 { margin-top: 0.5vw; }
        .mt-2 { margin-top: 1vw; }
        .mb-1 { margin-bottom: 0.5vw; }
        .mb-2 { margin-bottom: 1vw; }
    </style>

    <main class="checkout-container">

        {{-- Header --}}
        <div class="checkout-header">
            <h1>Checkout</h1>
            <p>Lengkapi data untuk menyelesaikan pesanan.</p>
        </div>

        @if (session('error'))
            <div class="checkout-alert error">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="checkout-alert error">
                <p class="alert-title">Terjadi kesalahan:</p>
                <ul class="alert-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="checkout-grid">

            {{-- Form --}}
            <div>
                <form action="{{ route('customer.checkout.process') }}" method="POST" id="checkout-form">
                    @csrf

                    <input type="hidden" name="voucher_code" id="applied-voucher-code" value="{{ session('voucher_code') }}">
                    <input type="hidden" name="voucher_discount" id="applied-voucher-discount" value="{{ session('voucher_discount') ?? 0 }}">
                    <input type="hidden" name="origin_postal_code" id="origin_postal_code" value="{{ config('services.biteship.origin_postal_code', '46191') }}">
                    <input type="hidden" name="payment_method" value="midtrans">

                    {{-- Informasi Pengiriman --}}
                    <div class="checkout-section">
                        <h2 class="section-title">Informasi Pengiriman</h2>
                        <p class="section-subtitle">Isi data penerima paket.</p>

                        @if($customer && $addresses->isNotEmpty())
                            <div class="form-group" style="margin-bottom: 1vw;">
                                <label for="saved_address">Pilih Alamat Tersimpan</label>
                                <select id="saved_address">
                                    <option value="">-- Gunakan alamat baru --</option>
                                    @foreach($addresses as $address)
                                        <option value="{{ $address->id }}" {{ $address->is_default ? 'selected' : '' }}>
                                            {{ $address->label ?: 'Alamat ' . $loop->iteration }} - {{ $address->recipient_name }}, {{ $address->city }}
                                            {{ $address->is_default ? '(Utama)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="helper-text">Anda tetap dapat mengubah data alamat sebelum membuat pesanan.</span>
                            </div>
                        @endif

                        <div class="form-grid">
                            {{-- Nama Penerima --}}
                            <div class="form-group full-width">
                                <label>Nama Penerima <span class="required">*</span></label>
                                <input type="text" name="shipping_name" id="shipping_name" 
                                    value="{{ old('shipping_name', $defaultAddress->recipient_name ?? $customer->name ?? '') }}" 
                                    required>
                                @error('shipping_name') <span class="error-text">{{ $message }}</span> @enderror
                            </div>

                            {{-- Nomor WhatsApp --}}
                            <div class="form-group full-width">
                                <label>Nomor WhatsApp <span class="required">*</span></label>
                                <input type="tel" name="shipping_phone" id="shipping_phone" 
                                    value="{{ old('shipping_phone', $defaultAddress->recipient_phone ?? $customer->phone ?? '') }}" 
                                    placeholder="08xxxxxxxxxx" required>
                                @error('shipping_phone') <span class="error-text">{{ $message }}</span> @enderror
                            </div>

                            {{-- Alamat Lengkap --}}
                            <div class="form-group full-width">
                                <label>Alamat Lengkap <span class="required">*</span></label>
                                <textarea name="shipping_address" id="shipping_address" rows="3" required>{{ old('shipping_address', $defaultAddress->address ?? '') }}</textarea>
                                <span class="helper-text">Contoh: Jalan Mawar No. 10, RT 01 RW 02</span>
                                @error('shipping_address') <span class="error-text">{{ $message }}</span> @enderror
                            </div>

                            {{-- Provinsi --}}
                            <div class="form-group">
                                <label>Provinsi <span class="required">*</span></label>
                                <select name="shipping_province" id="province" required>
                                    <option value="">-- Pilih Provinsi --</option>
                                </select>
                                <input type="hidden" name="shipping_province_id" id="province_id">
                                @error('shipping_province') <span class="error-text">{{ $message }}</span> @enderror
                            </div>

                            {{-- Kota/Kabupaten --}}
                            <div class="form-group">
                                <label>Kota/Kabupaten <span class="required">*</span></label>
                                <select name="shipping_city" id="city" required>
                                    <option value="">-- Pilih Kota --</option>
                                </select>
                                <input type="hidden" name="shipping_city_id" id="city_id">
                                @error('shipping_city') <span class="error-text">{{ $message }}</span> @enderror
                            </div>

                            {{-- Kecamatan --}}
                            <div class="form-group">
                                <label>Kecamatan <span class="required">*</span></label>
                                <select name="shipping_district" id="district" required>
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                                <input type="hidden" name="shipping_district_id" id="district_id">
                                @error('shipping_district') <span class="error-text">{{ $message }}</span> @enderror
                            </div>

                            {{-- Kelurahan --}}
                            <div class="form-group">
                                <label>Kelurahan <span class="required">*</span></label>
                                <select name="shipping_subdistrict" id="subdistrict" required>
                                    <option value="">-- Pilih Kelurahan --</option>
                                </select>
                                <input type="hidden" name="shipping_subdistrict_id" id="subdistrict_id">
                                @error('shipping_subdistrict') <span class="error-text">{{ $message }}</span> @enderror
                            </div>

                            {{-- Hidden Postal Code --}}
                            <input type="hidden" name="shipping_postal_code" id="shipping_postal_code" value="0">
                        </div>
                    </div>

                    {{-- Ekspedisi & Ongkir --}}
                    <div class="checkout-section">
                        <h2 class="section-title">Ekspedisi & Ongkir</h2>
                        <p class="section-subtitle">Pilih kurir dan lihat estimasi ongkir.</p>

                        {{-- 🔥 WARNING: HARUS PILIH KURIR DULU --}}
                        <div id="shipping-warning" class="hidden" style="background:#fef3c7;border:0.1vw solid #f59e0b;border-radius:0.7vw;padding:0.8vw 1vw;margin-bottom:1vw;font-size:0.8vw;color:#92400e;">
                            ⚠️ <strong>Pilih kurir dan layanan pengiriman</strong> terlebih dahulu untuk melihat biaya ongkir.
                        </div>

                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label>Layanan Pengiriman <span class="required">*</span></label>
                                <select name="courier" id="courier" required class="hidden" disabled>
                                    <option value="">-- Pilih Kurir --</option>
                                </select>
                                <select name="shipping_service" id="service" class="hidden" disabled>
                                    <option value="">-- Pilih Layanan --</option>
                                </select>
                                <button type="button" id="shipping-picker-button" class="shipping-picker-button" disabled>
                                    <span>
                                        <span class="picker-title">Pilih kurir dan layanan</span>
                                        <span class="picker-subtitle">Lengkapi alamat tujuan terlebih dahulu</span>
                                    </span>
                                    <span class="picker-arrow">›</span>
                                </button>
                                <input type="hidden" name="shipping_cost" id="shipping_cost" value="0">
                            </div>
                        </div>
                    </div>

                    {{-- Catatan --}}
                    <div class="checkout-section">
                        <h2 class="section-title">Catatan</h2>
                        <textarea name="notes" rows="3" class="full-width" placeholder="Tambahkan catatan untuk pesanan (opsional)">{{ old('notes') }}</textarea>
                    </div>

                    {{-- Info Guest --}}
                    @guest('customer')
                        <div class="guest-info">
                            <p class="guest-title">Akun akan dibuat otomatis</p>
                            <p class="guest-text">Kamu akan login otomatis dengan nomor WhatsApp. Password default: <strong>6 digit terakhir nomor WhatsApp</strong>.</p>
                            <p class="guest-hint">Kamu bisa mengganti password nanti di halaman profil.</p>
                        </div>
                    @endguest

                </form>
            </div>

            {{-- Summary --}}
            <div class="checkout-summary">
                <h2 class="summary-title">Ringkasan Pesanan</h2>

                {{-- Items --}}
                <div class="summary-items">
                    @foreach ($cart as $item)
                        <div class="summary-item">
                            <div class="item-info">
                                <div class="item-name">{{ $item['product_name'] }}</div>
                                @if ($item['variant_name'])
                                    <div class="item-variant">{{ $item['variant_name'] }}</div>
                                @endif
                                <div class="item-qty">{{ $item['quantity'] }}x</div>
                            </div>
                            <span class="item-price">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- Subtotal --}}
                <div class="summary-divider"></div>
                <div class="summary-row">
                    <span class="row-label">Subtotal</span>
                    <span class="row-value" id="subtotal-display">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>

                {{-- Ongkir --}}
                <div class="summary-row" id="shipping-summary">
                    <span class="row-label">Ongkir</span>
                    <span class="row-value" id="shipping-cost-text">Rp 0</span>
                </div>

                {{-- VOUCHER SECTION IN SUMMARY --}}
                <div class="voucher-summary-section">
                    <div class="voucher-header">
                        <span class="voucher-label">
                            Voucher
                        </span>
                        <button type="button" class="btn-open-voucher" id="btn-open-voucher">
                            <span id="voucher-action-text">
                                @if($appliedVoucher && $isAutoApplied)
                                    Ganti Voucher
                                @elseif($appliedVoucher)
                                    Ganti Voucher
                                @else
                                    Pilih Voucher
                                @endif
                            </span>
                        </button>
                    </div>
                    
                    {{-- Applied Voucher Display --}}
                    <div id="applied-voucher-summary" class="{{ $appliedVoucher ? '' : 'hidden' }}">
                        <div class="applied-voucher-summary">
                            <div class="voucher-info">
                                <span class="name">
                                    {{ $appliedVoucher->name ?? '' }}
                                </span>
                                <span class="discount">Dapat potongan Rp {{ number_format($totalVoucherDiscount, 0, ',', '.') }}</span>
                            </div>
                            <button type="button" class="btn-remove-voucher-sm" id="btn-remove-voucher" title="Hapus Voucher">
                                ✕
                            </button>
                        </div>
                    </div>

                    {{-- Voucher Discount Row --}}
                    <div class="summary-row" id="voucher-discount-row">
                        <span class="row-label">Diskon Voucher</span>
                        <span class="row-value" id="voucher-discount-text">
                            -Rp {{ number_format($totalVoucherDiscount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                {{-- Total --}}
                <div class="summary-total">
                    <span>Total</span>
                    <span id="total-display">Rp {{ number_format($subtotal + $shippingCost - $totalVoucherDiscount, 0, ',', '.') }}</span>
                </div>

                {{-- Submit Button --}}
                <button type="submit" form="checkout-form" class="btn-submit">
                    Buat Pesanan
                </button>

                <a href="{{ route('customer.cart.index') }}" class="btn-back">
                    ← Kembali ke Keranjang
                </a>
            </div>

        </div>

    </main>

    <div id="shipping-popup" class="shipping-popup hidden" role="dialog" aria-modal="true" aria-labelledby="shipping-popup-title">
        <div class="shipping-popup-backdrop" data-close-shipping-popup></div>
        <div class="shipping-popup-card">
            <div class="shipping-popup-header">
                <h3 id="shipping-popup-title">Pilih Layanan Pengiriman</h3>
                <button type="button" class="shipping-popup-close" data-close-shipping-popup aria-label="Tutup">×</button>
            </div>
            <div class="shipping-popup-body">
                <p class="shipping-popup-note">Pilih satu layanan kurir beserta harga dan estimasi pengiriman.</p>
                <div id="shipping-options"></div>
            </div>
            <div class="shipping-popup-footer">
                <button type="button" id="shipping-popup-confirm" class="shipping-popup-confirm" disabled>Pilih layanan</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isMobile = window.innerWidth <= 768;
            const summary = document.querySelector('.checkout-summary');
            const summaryTitle = document.querySelector('.checkout-summary .summary-title');
            let isSummaryOpen = false;

            if (isMobile && summary) {
                // Toggle summary saat klik header
                summaryTitle?.addEventListener('click', function(e) {
                    e.stopPropagation();
                    toggleSummary();
                });

                // Toggle summary saat klik handle
                const handle = summary.querySelector('::before');
                if (handle) {
                    summary.addEventListener('click', function(e) {
                        if (e.target === this) {
                            toggleSummary();
                        }
                    });
                }
            }

            function toggleSummary() {
                isSummaryOpen = !isSummaryOpen;
                summary.classList.toggle('active', isSummaryOpen);
            }

            // Auto open summary saat pertama kali
            setTimeout(function() {
                if (isMobile && summary) {
                    isSummaryOpen = true;
                    summary.classList.add('active');
                }
            }, 600);
        });
    </script>

    <script>
        $(document).ready(function() {
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            let subtotal = {{ $subtotal }};

            // 🔥 DATA DEFAULT ADDRESS (dari server)
            const defaultAddress = @json($defaultAddress);
            const savedAddresses = @json($addresses);
            const isLoggedIn = @json(Auth::guard('customer')->check());
            let pendingSavedAddress = null;
            let pendingShippingOption = null;

            function escapeHtml(value) {
                return String(value || '').replace(/[&<>'"]/g, function(char) {
                    return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' })[char];
                });
            }

            function resetShippingPicker(message = 'Lengkapi alamat tujuan terlebih dahulu') {
                pendingShippingOption = null;
                $('#shipping-popup').addClass('hidden');
                $('#shipping-popup-confirm').prop('disabled', true).text('Pilih layanan');
                $('#shipping-picker-button').prop('disabled', true).html(
                    '<span><span class="picker-title">Pilih kurir dan layanan</span><span class="picker-subtitle">' + escapeHtml(message) + '</span></span><span class="picker-arrow">›</span>'
                );
            }

            function renderShippingOptions(couriers) {
                let html = '';
                let totalServices = 0;

                const sortedCouriers = [...couriers].sort(function(a, b) {
                    return String(a.name).localeCompare(String(b.name), 'id');
                });

                sortedCouriers.forEach(function(courier) {
                    if (!courier.services || courier.services.length === 0) {
                        return;
                    }

                    // 🔥 FILTER SERVICE DUPLIKAT
                    const seenNames = new Set();
                    const uniqueServices = courier.services.filter(function(service) {
                        const name = String(service.name || service.service || 'Reguler').trim();
                        if (seenNames.has(name)) {
                            return false;
                        }
                        seenNames.add(name);
                        return true;
                    });

                    // 🔥 SORT BY COST TERMURAH
                    const services = uniqueServices
                        .filter(function(service) {
                            return parseInt(service.cost) > 0;
                        })
                        .sort(function(a, b) {
                            return (parseInt(a.cost || 0) - parseInt(b.cost || 0));
                        });

                    if (!services.length) {
                        return;
                    }

                    totalServices += services.length;

                    html += `<section class="shipping-courier-group">`;
                    html += `<h4 class="shipping-courier-name">${escapeHtml(courier.name)}</h4>`;

                    services.forEach(function(service, index) {
                        let serviceName = service.name || service.service || 'Reguler';
                        
                        // 🔥 HAPUS DUPLIKASI NAMA
                        if (serviceName.includes(' - ')) {
                            const parts = serviceName.split(' - ');
                            if (parts[0] === parts[1]) {
                                serviceName = parts[0];
                            }
                        }
                        serviceName = serviceName.replace(/\s*-\s*$/, '').trim();

                        const cost = parseInt(service.cost || 0);
                        const etd = service.etd || service.duration || '-';

                        const description = service.description 
                            ? ' · ' + escapeHtml(service.description) 
                            : '';

                        const cheapestBadge = index === 0 && services.length > 1
                            ? `<span style="background:#22c55e;color:white;font-size:.6rem;padding:.1rem .4rem;border-radius:.25rem;margin-left:.3rem;">TERMURAH</span>`
                            : '';

                        // 🔥 TAMBAHKAN DATA SERVICE LENGKAP
                        html += `
                            <button type="button" class="shipping-option" 
                                data-courier="${escapeHtml(courier.code)}" 
                                data-service="${escapeHtml(serviceName)}" 
                                data-service-code="${escapeHtml(service.service || '')}"
                                data-cost="${cost}" 
                                data-etd="${escapeHtml(etd)}"
                                data-description="${escapeHtml(service.description || '')}">
                                <span>
                                    <span class="shipping-option-name">
                                        ${escapeHtml(serviceName)}
                                        ${cheapestBadge}
                                        ${description}
                                    </span>
                                    <span class="shipping-option-meta">
                                        Estimasi tiba ${escapeHtml(String(etd))}
                                    </span>
                                </span>
                                <span class="shipping-option-price">
                                    Rp ${formatNumber(cost)}
                                </span>
                            </button>
                        `;
                    });

                    html += `</section>`;
                });

                $('#shipping-options').html(
                    html || '<p class="shipping-popup-note">Layanan pengiriman belum tersedia.</p>'
                );

                const totalCouriers = sortedCouriers.filter(function(courier) {
                    return courier.services && courier.services.length;
                }).length;

                const buttonText = totalCouriers > 0
                    ? `${totalCouriers} kurir tersedia (${totalServices} layanan)`
                    : 'Lengkapi alamat tujuan terlebih dahulu';

                $('#shipping-picker-button')
                    .prop('disabled', totalCouriers === 0)
                    .html(`
                        <span>
                            <span class="picker-title">Pilih kurir dan layanan</span>
                            <span class="picker-subtitle">${escapeHtml(buttonText)}</span>
                        </span>
                        <span class="picker-arrow">›</span>
                    `);
            }

            $('#shipping-picker-button').on('click', function() {
                if (!$(this).prop('disabled')) $('#shipping-popup').removeClass('hidden');
            });

            $(document).on('click', '[data-close-shipping-popup]', function() {
                $('#shipping-popup').addClass('hidden');
            });

            $(document).on('click', '.shipping-option', function() {
                const $option = $(this);
                pendingShippingOption = {
                    courier: $option.data('courier'),
                    service: $option.data('service')
                };
                $('.shipping-option').removeClass('selected');
                $option.addClass('selected');
                $('#shipping-popup-confirm').prop('disabled', false).text('Pilih layanan ini');
            });

            $('#shipping-popup-confirm').on('click', function() {
                if (!pendingShippingOption) return;

                const courierCode = pendingShippingOption.courier;
                const serviceName = pendingShippingOption.service;

                // 🔥 CARI DATA COURIER DAN SERVICE YANG DIPILIH
                const couriers = $('#courier').data('shipping-rates') || {};
                const selectedCourier = couriers[courierCode];

                if (!selectedCourier) {
                    showToast('Data kurir tidak ditemukan.', 'error');
                    return;
                }

                // 🔥 CARI SERVICE YANG DIPILIH
                let selectedService = null;
                let selectedCost = 0;
                let selectedEtd = '-';

                if (selectedCourier.services) {
                    selectedCourier.services.forEach(function(service) {
                        const serviceNameDisplay = service.name || service.service || 'Reguler';
                        if (serviceNameDisplay === serviceName || service.service === serviceName) {
                            selectedService = service;
                            selectedCost = service.cost || 0;
                            selectedEtd = service.etd || service.duration || '-';
                        }
                    });
                }

                if (!selectedService || selectedCost <= 0) {
                    showToast('Layanan tidak valid.', 'error');
                    return;
                }

                // 🔥 UPDATE COURIER SELECT
                $('#courier').val(courierCode).trigger('change');

                // 🔥 UPDATE SERVICE SELECT
                $('#service').val(serviceName).trigger('change');

                // 🔥 UPDATE SHIPPING COST HIDDEN INPUT
                $('#shipping_cost').val(selectedCost);

                // 🔥 UPDATE SHIPPING COST DISPLAY
                const courierName = selectedCourier.name || courierCode.toUpperCase();
                const costDisplay = 'Rp ' + formatNumber(selectedCost);
                $('#shipping-cost-text').text(costDisplay);
                $('#shipping-cost-display').html(`
                    <div class="success">
                        <div style="font-weight:600;">${courierName} · ${serviceName}</div>
                        <div style="font-size:0.65vw;color:#94a3b8;">Estimasi: ${selectedEtd}</div>
                        <div style="font-size:0.8vw;color:#0f172a;font-weight:700;margin-top:0.2vw;">${costDisplay}</div>
                    </div>
                `);

                // 🔥 UPDATE PICKER BUTTON
                $('#shipping-picker-button').prop('disabled', false).html(
                    '<span><span class="picker-title">' + escapeHtml(courierName) + ' · ' + escapeHtml(serviceName) + '</span>' +
                    '<span class="picker-subtitle">Estimasi ' + escapeHtml(selectedEtd) + ' . ' + costDisplay + '</span></span><span class="picker-arrow">›</span>'
                );

                // 🔥 UPDATE SESSION VIA AJAX
                $.ajax({
                    url: '{{ route("customer.checkout.update-shipping") }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify({
                        shipping_cost: selectedCost,
                        courier: courierCode,
                        service: serviceName
                    }),
                    success: function(response) {
                        console.log('Shipping cost saved to session:', response);
                    },
                    error: function() {
                        console.warn('Failed to save shipping cost to session');
                    }
                });

                // 🔥 UPDATE TOTAL
                updateTotal();

                // 🔥 TUTUP POPUP
                $('#shipping-popup').addClass('hidden');

                // 🔥 SEMBUNYIKAN WARNING
                $('#shipping-warning').addClass('hidden');
            });

            // ============================================
            // CACHE MANAGEMENT
            // ============================================

            const CACHE_KEY_PROVINCES = 'checkout_provinces_v2';
            const CACHE_KEY_CITIES = 'checkout_cities_v2_';
            const CACHE_KEY_DISTRICTS = 'checkout_districts_v2_';
            const CACHE_KEY_VILLAGES = 'checkout_villages_v2_';

            function getCache(key) {
                try {
                    const data = localStorage.getItem(key);
                    if (data) {
                        const parsed = JSON.parse(data);
                        if (parsed.expiry && parsed.expiry > Date.now()) {
                            return parsed.data;
                        }
                        localStorage.removeItem(key);
                    }
                } catch(e) {}
                return null;
            }

            function setCache(key, data, ttl = 3600000) {
                try {
                    localStorage.setItem(key, JSON.stringify({
                        data: data,
                        expiry: Date.now() + ttl
                    }));
                } catch(e) {}
            }

            // ============================================
            // LOAD PROVINCES
            // ============================================

            function loadProvinces() {
                let cached = getCache(CACHE_KEY_PROVINCES);

                if (cached) {
                    console.log('Provinces loaded from cache:', cached);
                    renderProvinces(cached);
                    return;
                }

                $('#province').html('<option value="">-- Memuat Provinsi --</option>');

                $.ajax({
                    url: '{{ request()->getBaseUrl() }}/api/regions/provinces',
                    method: 'GET',
                    timeout: 30000,
                    success: function(response) {
                        console.log('API Response - Cascade:', response);
                        const provinces = Array.isArray(response) ? response : [];
                        console.log('Provinces:', provinces);
                        setCache(CACHE_KEY_PROVINCES, provinces);
                        renderProvinces(provinces);
                    },
                    error: function(xhr) {
                        console.error('Error loading provinces:', xhr.responseText);
                        $('#province').html('<option value="">-- Gagal memuat data --</option>');
                        setTimeout(loadProvinces, 5000);
                    }
                });
            }

            function renderProvinces(data) {
                let options = '<option value="">-- Pilih Provinsi --</option>';
                if (data && Array.isArray(data) && data.length > 0) {
                    $.each(data, function(index, province) {
                        const code = province.code;
                        const name = province.name;
                        if (code && name) {
                            options += `<option value="${name}" data-code="${code}">${name}</option>`;
                        }
                    });
                }
                $('#province').html(options);

                if (pendingSavedAddress) {
                    fillSavedAddress(pendingSavedAddress);
                } else if (defaultAddress && defaultAddress.province) {
                    $('#province').val(defaultAddress.province).trigger('change');
                }
            }

            function fillSavedAddress(address) {
                if (!address) {
                    return;
                }

                $('#shipping_name').val(address.recipient_name || '');
                $('#shipping_phone').val(address.recipient_phone || '');
                $('#shipping_address').val(address.address || '');
                $('#shipping_postal_code').val(address.postal_code || '0');

                if (address.province) {
                    $('#province').val(address.province);
                    const provinceCode = $('#province').find(':selected').data('code');

                    if (provinceCode) {
                        loadCities(
                            provinceCode,
                            address.city || null,
                            address.district || null,
                            address.subdistrict || null
                        );
                    }
                }
            }

            // ============================================
            // LOAD CITIES
            // ============================================

            function loadCities(provinceCode, selectedCity = null, selectedDistrict = null, selectedSubdistrict = null) {
                const cacheKey = CACHE_KEY_CITIES + provinceCode;
                let cached = getCache(cacheKey);

                if (cached) {
                    renderCities(cached, selectedCity, selectedDistrict, selectedSubdistrict);
                    return;
                }

                $('#city').html('<option value="">-- Memuat Kota --</option>').prop('disabled', true);

                $.ajax({
                    url: '{{ request()->getBaseUrl() }}/api/regions/cities?province_code=' + encodeURIComponent(provinceCode),
                    method: 'GET',
                    success: function(response) {
                        console.log('Cities API response:', response);
                        const cities = Array.isArray(response) ? response : [];
                        setCache(cacheKey, cities);
                        renderCities(cities, selectedCity, selectedDistrict, selectedSubdistrict);
                    },
                    error: function() {
                        $('#city').html('<option value="">-- Gagal memuat --</option>').prop('disabled', true);
                    }
                });
            }

            function renderCities(data, selectedCity, selectedDistrict, selectedSubdistrict) {
                let options = '<option value="">-- Pilih Kota --</option>';
                if (data && data.length > 0) {
                    $.each(data, function(index, city) {
                        const code = city.code;
                        const name = city.name;
                        if (code && name) {
                            options += `<option value="${name}" data-code="${code}">${name}</option>`;
                        }
                    });
                }
                $('#city').html(options).prop('disabled', false);

                if (selectedCity) {
                    setTimeout(function() {
                        $('#city').data('selected-district', selectedDistrict || '');
                        $('#city').data('selected-subdistrict', selectedSubdistrict || '');
                        $('#city').val(selectedCity).trigger('change');
                    }, 100);
                }
            }

            // ============================================
            // LOAD DISTRICTS
            // ============================================

            function loadDistricts(cityCode, selectedDistrict = null, selectedSubdistrict = null) {
                const cacheKey = CACHE_KEY_DISTRICTS + cityCode;
                let cached = getCache(cacheKey);

                if (cached) {
                    renderDistricts(cached, selectedDistrict, selectedSubdistrict);
                    return;
                }

                $('#district').html('<option value="">-- Memuat Kecamatan --</option>').prop('disabled', true);

                $.ajax({
                    url: '{{ request()->getBaseUrl() }}/api/regions/districts?city_code=' + encodeURIComponent(cityCode),
                    method: 'GET',
                    success: function(response) {
                        console.log('Districts API response:', response);
                        const districts = Array.isArray(response) ? response : [];
                        setCache(cacheKey, districts);
                        renderDistricts(districts, selectedDistrict, selectedSubdistrict);
                    },
                    error: function() {
                        $('#district').html('<option value="">-- Gagal memuat --</option>').prop('disabled', true);
                    }
                });
            }

            function renderDistricts(data, selectedDistrict, selectedSubdistrict) {
                let options = '<option value="">-- Pilih Kecamatan --</option>';
                if (data && data.length > 0) {
                    $.each(data, function(index, district) {
                        const code = district.code;
                        const name = district.name;
                        if (code && name) {
                            options += `<option value="${name}" data-code="${code}">${name}</option>`;
                        }
                    });
                }
                $('#district').html(options).prop('disabled', false);

                if (selectedDistrict) {
                    setTimeout(function() {
                        $('#district').data('selected-subdistrict', selectedSubdistrict || '');
                        $('#district').val(selectedDistrict).trigger('change');
                    }, 100);
                }
            }

            // ============================================
            // LOAD VILLAGES (Kelurahan)
            // ============================================

            function loadVillages(districtCode, selectedVillage = null) {
                const cacheKey = CACHE_KEY_VILLAGES + districtCode;
                let cached = getCache(cacheKey);

                if (cached) {
                    console.log('Villages loaded from cache:', cached);
                    renderVillages(cached, selectedVillage);
                    return;
                }

                $('#subdistrict').html('<option value="">-- Memuat Kelurahan --</option>').prop('disabled', true);

                $.ajax({
                    url: '{{ request()->getBaseUrl() }}/api/regions/subdistricts?district_code=' + encodeURIComponent(districtCode),
                    method: 'GET',
                    timeout: 30000,
                    success: function(response) {
                        console.log('Villages API response:', response);

                        const villages = Array.isArray(response) ? response : [];

                        if (!villages || villages.length === 0) {
                            console.warn('No villages found for district:', districtCode);
                            $('#subdistrict').html('<option value="">-- Data tidak tersedia --</option>').prop('disabled', false);
                            return;
                        }

                        console.log('Villages found:', villages.length);
                        setCache(cacheKey, villages);
                        renderVillages(villages, selectedVillage);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading villages:', {
                            status: status,
                            error: error,
                            response: xhr.responseText
                        });
                        $('#subdistrict').html('<option value="">-- Gagal memuat --</option>').prop('disabled', false);
                    }
                });
            }

            function renderVillages(data, selectedVillage) {
                let options = '<option value="">-- Pilih Kelurahan --</option>';

                if (data && data.length > 0) {
                    $.each(data, function(index, village) {
                        const code = village.code;
                        const name = village.name;
                        const postalCode = village.postal_code || '';

                        if (code && name) {
                            options += `<option value="${name}" data-code="${code}" data-zip="${postalCode}">${name}${postalCode ? ' (' + postalCode + ')' : ''}</option>`;
                        }
                    });
                } else {
                    options += '<option value="">-- Data tidak tersedia --</option>';
                }

                $('#subdistrict').html(options).prop('disabled', false);

                if (selectedVillage) {
                    setTimeout(function() {
                        $('#subdistrict').val(selectedVillage).trigger('change');
                    }, 100);
                }
            }

            // ============================================
            // CHECK SHIPPING COST
            // ============================================

            function checkShippingCost(destinationPostalCode) {

                const $display = $('#shipping-cost-display');
                const $courier = $('#courier');
                const $service = $('#service');

                $display.html(
                    '<span class="loading">⏳ Mencari ongkir...</span>'
                );

                $courier
                    .prop('disabled', true)
                    .html('<option value="">-- Memuat kurir --</option>');

                $service
                    .prop('disabled', true)
                    .html('<option value="">-- Pilih Layanan --</option>');

                if (
                    !destinationPostalCode ||
                    destinationPostalCode === '0' ||
                    destinationPostalCode === ''
                ) {
                    $display.html(
                        '<span class="error">❌ Kode pos tujuan tidak tersedia</span>'
                    );

                    resetShippingPicker(
                        'Lengkapi alamat tujuan terlebih dahulu'
                    );

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | ORIGIN
                |--------------------------------------------------------------------------
                */

                const originPostalCode =
                    $('#origin_postal_code').val()
                    || '{{ config("services.biteship.origin_postal_code", "46191") }}';

                /*
                |--------------------------------------------------------------------------
                | ITEMS
                |--------------------------------------------------------------------------
                */

                const items = [];

                @foreach ($cart as $item)
                    items.push({
                        name: @json($item['product_name']),
                        weight: {{ (int) ($item['weight'] ?? 1000) }},
                        quantity: {{ (int) $item['quantity'] }},
                        price: {{ (int) $item['price'] }}
                    });
                @endforeach

                if (items.length === 0) {

                    $display.html(
                        '<span class="error">❌ Tidak ada produk di keranjang</span>'
                    );

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | SEMUA KURIR
                |--------------------------------------------------------------------------
                */

                const allCouriers = [
                    'jne',
                    'jnt',
                    'sicepat',
                    'pos',
                    'anteraja',
                    'lion',
                    'ninja',
                    'rpx',
                    'pahala',
                    'wahana',
                    'tiki',
                    'ncs',
                    'first',
                    'idexpress',
                    'star'
                ];

                const payload = {
                    origin_postal_code: originPostalCode,
                    destination_postal_code: destinationPostalCode,
                    items: items,
                    couriers: allCouriers
                };

                console.log('=== BITESHIP RATES ===');
                console.log('Payload:', payload);

                $.ajax({
                    url: '{{ route("api.biteship.rates") }}',

                    method: 'POST',

                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },

                    data: JSON.stringify(payload),

                    success: function(response) {

                        console.log('Biteship Response:', response);

                        if (!response.success) {

                            $display.html(
                                '<span class="error">❌ ' +
                                (response.message || 'Gagal mendapatkan ongkir') +
                                '</span>'
                            );

                            resetShippingPicker(
                                'Gagal mendapatkan layanan pengiriman'
                            );

                            return;
                        }

                        const couriers = response.data || [];

                        if (!couriers.length) {

                            $display.html(
                                '<span class="error">' +
                                '❌ Tidak ada layanan pengiriman untuk rute ini' +
                                '</span>'
                            );

                            resetShippingPicker(
                                'Tidak ada kurir tersedia'
                            );

                            return;
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | FILTER COURIER YANG MEMILIKI SERVICE
                        |--------------------------------------------------------------------------
                        */

                        const availableCouriers = couriers.filter(function(courier) {

                            return (
                                courier.services &&
                                courier.services.length > 0
                            );

                        });

                        if (!availableCouriers.length) {

                            $display.html(
                                '<span class="error">' +
                                '❌ Tidak ada layanan pengiriman tersedia' +
                                '</span>'
                            );

                            resetShippingPicker(
                                'Tidak ada layanan tersedia'
                            );

                            return;
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | SIMPAN RATE
                        |--------------------------------------------------------------------------
                        */

                        const courierMap = {};

                        availableCouriers.forEach(function(courier) {

                            courierMap[courier.code] = courier;

                        });

                        /*
                        |--------------------------------------------------------------------------
                        | SELECT COURIER
                        |--------------------------------------------------------------------------
                        */

                        let courierOptions =
                            '<option value="">-- Pilih Kurir --</option>';

                        availableCouriers.forEach(function(courier) {

                            courierOptions += `
                                <option value="${escapeHtml(courier.code)}">
                                    ${escapeHtml(courier.name)}
                                </option>
                            `;

                        });

                        $courier
                            .html(courierOptions)
                            .prop('disabled', false)
                            .data('shipping-rates', courierMap);

                        /*
                        |--------------------------------------------------------------------------
                        | RENDER POPUP
                        |--------------------------------------------------------------------------
                        */

                        renderShippingOptions(availableCouriers);

                        /*
                        |--------------------------------------------------------------------------
                        | RESET SERVICE
                        |--------------------------------------------------------------------------
                        */

                        $service
                            .prop('disabled', true)
                            .html(
                                '<option value="">-- Pilih Layanan --</option>'
                            );

                        /*
                        |--------------------------------------------------------------------------
                        | DISPLAY
                        |--------------------------------------------------------------------------
                        */

                        let totalServices = 0;

                        availableCouriers.forEach(function(courier) {
                            totalServices += courier.services.length;
                        });

                        $display.html(`
                            <span class="success">
                                ✅ ${availableCouriers.length} kurir tersedia
                                (${totalServices} layanan)
                            </span>
                        `);

                    },

                    error: function(xhr) {

                        console.error(
                            'Biteship AJAX Error:',
                            xhr.responseText
                        );

                        let message = 'Gagal mendapatkan ongkir';

                        try {

                            const response =
                                JSON.parse(xhr.responseText);

                            if (response.message) {
                                message += ': ' + response.message;
                            }

                        } catch (e) {

                            console.error(
                                'Gagal membaca response Biteship',
                                e
                            );

                        }

                        $courier
                            .prop('disabled', true)
                            .html(
                                '<option value="">-- Gagal memuat kurir --</option>'
                            );

                        $service
                            .prop('disabled', true)
                            .html(
                                '<option value="">-- Pilih Layanan --</option>'
                            );

                        $display.html(
                            '<span class="error">❌ ' +
                            escapeHtml(message) +
                            '</span>'
                        );

                        resetShippingPicker(message);
                    }
                });
            }

            // ============================================
            // EVENT HANDLERS
            // ============================================

            // Province Change
            $('#province').on('change', function() {
                const provinceCode = $(this).find(':selected').data('code');

                $('#province_id').val(provinceCode);
                $('#city_id').val('');
                $('#district_id').val('');
                $('#subdistrict_id').val('');
                $('#shipping_cost').val(0);
                $('#shipping-cost-text').text('Rp 0');
                updateTotal();

                if (provinceCode) {
                    const selectedCity = pendingSavedAddress?.city || defaultAddress?.city || null;
                    const selectedDistrict = pendingSavedAddress?.district || defaultAddress?.district || null;
                    const selectedSubdistrict = pendingSavedAddress?.subdistrict || defaultAddress?.subdistrict || null;
                    loadCities(provinceCode, selectedCity, selectedDistrict, selectedSubdistrict);
                } else {
                    $('#city').html('<option value="">-- Pilih Kota --</option>').prop('disabled', true);
                    $('#district').html('<option value="">-- Pilih Kecamatan --</option>').prop('disabled', true);
                    $('#subdistrict').html('<option value="">-- Pilih Kelurahan --</option>').prop('disabled', true);
                }

                $('#courier').prop('disabled', true);
                $('#service').html('<option value="">-- Pilih Layanan --</option>');
                $('#shipping-cost-display').text('Pilih kurir dan kelurahan tujuan');
            });

            // City Change
            $('#city').on('change', function() {
                const cityCode = $(this).find(':selected').data('code');
                const selectedDistrict = $(this).data('selected-district') || pendingSavedAddress?.district || defaultAddress?.district || null;
                const selectedSubdistrict = $(this).data('selected-subdistrict') || pendingSavedAddress?.subdistrict || defaultAddress?.subdistrict || null;

                $('#city_id').val(cityCode);
                $('#district_id').val('');
                $('#subdistrict_id').val('');
                $('#shipping_cost').val(0);
                $('#shipping-cost-text').text('Rp 0');
                updateTotal();

                $('#district').html('<option value="">-- Pilih Kecamatan --</option>').prop('disabled', true);
                $('#subdistrict').html('<option value="">-- Pilih Kelurahan --</option>').prop('disabled', true);

                if (cityCode) {
                    loadDistricts(cityCode, selectedDistrict, selectedSubdistrict);
                }

                $('#courier').prop('disabled', true);
                $('#service').html('<option value="">-- Pilih Layanan --</option>');
                $('#shipping-cost-display').text('Pilih kurir dan kelurahan tujuan');
            });

            // District Change
            $('#district').on('change', function() {
                const districtCode = $(this).find(':selected').data('code');
                const selectedSubdistrict = $(this).data('selected-subdistrict') || pendingSavedAddress?.subdistrict || defaultAddress?.subdistrict || null;

                $('#district_id').val(districtCode);
                $('#subdistrict_id').val('');
                $('#shipping_cost').val(0);
                $('#shipping-cost-text').text('Rp 0');
                updateTotal();

                $('#subdistrict').html('<option value="">-- Pilih Kelurahan --</option>').prop('disabled', true);

                if (districtCode) {
                    loadVillages(districtCode, selectedSubdistrict);
                }

                $('#courier').prop('disabled', true);
                $('#service').html('<option value="">-- Pilih Layanan --</option>');
                $('#shipping-cost-display').text('Pilih kurir dan kelurahan tujuan');
            });

            // Subdistrict Change
            $('#subdistrict').on('change', function() {
                const selectedOption = $(this).find(':selected');
                const villageCode = selectedOption.attr('data-code') || '';
                const zipCode = selectedOption.attr('data-zip') || '';

                console.log('=== SUBDISTRICT SELECTED ===');
                console.log('Village Code:', villageCode);
                console.log('Postal Code:', zipCode);

                $('#subdistrict_id').val(villageCode);
                $('#shipping_postal_code').val(zipCode);

                $('#shipping_cost').val(0);
                $('#shipping-cost-text').text('Rp 0');
                updateTotal();

                $('#service')
                    .prop('disabled', true)
                    .html('<option value="">-- Pilih Layanan --</option>');

                if (!zipCode || zipCode === '0' || zipCode === '' || zipCode.length < 4) {
                    $('#courier')
                        .prop('disabled', true)
                        .html('<option value="">-- Kode pos tidak valid --</option>');
                    $('#shipping-cost-display').html(
                        '<span class="error">❌ Kode pos tujuan tidak valid</span>'
                    );
                    return;
                }

                $('#courier')
                    .prop('disabled', true)
                    .html('<option value="">⏳ Memuat Kurir...</option>');

                $('#shipping-cost-display').html(
                    '<span class="loading">⏳ Mencari layanan pengiriman...</span>'
                );

                const weight = calculateTotalWeight();

                if (!weight || weight <= 0) {
                    $('#courier')
                        .prop('disabled', true)
                        .html('<option value="">-- Kurir Tidak Tersedia --</option>');
                    $('#shipping-cost-display').html(
                        '<span class="error">❌ Berat produk belum tersedia</span>'
                    );
                    return;
                }

                console.log('=== REQUEST ONGKIR BITESHIP ===');
                console.log('Origin Postal Code:', '{{ config('services.biteship.origin.postal_code', '46196') }}');
                console.log('Destination Postal Code:', zipCode);
                console.log('Weight:', weight);

                checkShippingCost(zipCode);
            });

            $('#saved_address').on('change', function() {
                const addressId = String($(this).val());
                const address = savedAddresses.find(function(item) {
                    return String(item.id) === addressId;
                });

                if (address) {
                    pendingSavedAddress = address;
                    fillSavedAddress(address);
                }
            });

            // Courier Change
            $('#courier').on('change', function() {
                const selectedCourier = $(this).val();

                console.log('=== COURIER SELECTED ===');
                console.log('Courier:', selectedCourier);

                $('#service')
                    .prop('disabled', true)
                    .html('<option value="">-- Memuat Layanan --</option>');

                $('#shipping_cost').val(0);
                $('#shipping-cost-text').text('Rp 0');
                updateTotal();

                if (!selectedCourier) {
                    $('#service')
                        .html('<option value="">-- Pilih Layanan --</option>')
                        .prop('disabled', true);
                    $('#shipping-cost-display').html('Pilih kurir');
                    return;
                }

                const couriers = $('#courier').data('shipping-rates') || {};
                const selectedCourierData = couriers[selectedCourier];

                if (!selectedCourierData || !selectedCourierData.services || !selectedCourierData.services.length) {
                    $('#service')
                        .html('<option value="">-- Layanan Tidak Tersedia --</option>')
                        .prop('disabled', true);
                    $('#shipping-cost-display').html(
                        '<span class="error">❌ Layanan kurir tidak tersedia</span>'
                    );
                    return;
                }

                let serviceOptions = '<option value="">-- Pilih Layanan --</option>';
                $.each(selectedCourierData.services, function(index, service) {
                    const serviceName = service.service || '';
                    const description = service.description || '';
                    const cost = parseInt(service.cost, 10) || 0;
                    const etd = service.etd || '-';

                    serviceOptions += `
                        <option value="${serviceName}" data-cost="${cost}" data-etd="${etd}">
                            ${serviceName} ${description ? '- ' + description : ''} - Rp ${formatNumber(cost)} (${etd} hari)
                        </option>
                    `;
                });

                $('#service')
                    .html(serviceOptions)
                    .prop('disabled', false);

                $('#shipping-cost-display').html(
                    `<span class="success">✅ ${selectedCourierData.services.length} layanan tersedia</span>`
                );
            });

            // Service Change
            $('#service').on('change', function() {
                const selected = $(this).find(':selected');
                const cost = parseInt(selected.data('cost')) || 0;
                const etd = selected.data('etd') || '-';
                const serviceName = selected.val();
                const courierName = $('#courier option:selected').text();

                console.log('=== SERVICE SELECTED ===');
                console.log('Service:', serviceName);
                console.log('Cost:', cost);
                console.log('ETD:', etd);
                console.log('Courier:', courierName);

                if (!serviceName || cost <= 0) {
                    $('#shipping_cost').val(0);
                    $('#shipping-cost-text').text('Rp 0');
                    updateTotal();
                    return;
                }

                // 🔥 UPDATE SHIPPING COST
                $('#shipping_cost').val(cost);
                $('#shipping-cost-text').text('Rp ' + formatNumber(cost));

                // 🔥 UPDATE DISPLAY
                const costDisplay = 'Rp ' + formatNumber(cost);
                $('#shipping-cost-display').html(`
                    <div class="success">
                        <div style="font-weight:600;">${courierName} · ${serviceName}</div>
                        <div style="font-size:0.65vw;color:#94a3b8;">Estimasi: ${etd} hari</div>
                        <div style="font-size:0.8vw;color:#0f172a;font-weight:700;margin-top:0.2vw;">${costDisplay}</div>
                    </div>
                `);

                // 🔥 UPDATE PICKER BUTTON
                $('#shipping-picker-button').prop('disabled', false).html(
                    '<span><span class="picker-title">' + escapeHtml(courierName) + ' · ' + escapeHtml(serviceName) + '</span>' +
                    '<span class="picker-subtitle">Estimasi ' + escapeHtml(etd) + ' . ' + costDisplay + '</span></span><span class="picker-arrow">›</span>'
                );

                // 🔥 UPDATE SESSION
                $.ajax({
                    url: '{{ route("customer.checkout.update-shipping") }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify({
                        shipping_cost: cost,
                        courier: $('#courier').val(),
                        service: serviceName
                    }),
                    success: function(response) {
                        console.log('Shipping cost saved to session:', response);
                    },
                    error: function() {
                        console.warn('Failed to save shipping cost to session');
                    }
                });

                // Sembunyikan warning
                $('#shipping-warning').addClass('hidden');

                // 🔥 UPDATE TOTAL
                updateTotal();
            });


            // ============================================
            // INIT - Load Provinces & Auto Fill
            // ============================================

            if ($('#saved_address').val()) {
                const selectedAddress = savedAddresses.find(function(item) {
                    return String(item.id) === String($('#saved_address').val());
                });

                if (selectedAddress) {
                    pendingSavedAddress = selectedAddress;
                }
            }

            loadProvinces();

            if (!pendingSavedAddress && defaultAddress) {
                console.log('Auto-fill address for logged-in user:', defaultAddress);

                function autoFillAddress() {
                    if (defaultAddress.province) {
                        $('#province option').each(function() {
                            if ($(this).text().trim() === defaultAddress.province) {
                                $(this).prop('selected', true);
                                $('#province').trigger('change');
                                return false;
                            }
                        });

                        setTimeout(function() {
                            if (defaultAddress.city) {
                                $('#city option').each(function() {
                                    if ($(this).text().trim() === defaultAddress.city) {
                                        $(this).prop('selected', true);
                                        $('#city').trigger('change');
                                        return false;
                                    }
                                });
                            }

                            setTimeout(function() {
                                if (defaultAddress.district) {
                                    $('#district option').each(function() {
                                        if ($(this).text().trim() === defaultAddress.district) {
                                            $(this).prop('selected', true);
                                            $('#district').trigger('change');
                                            return false;
                                        }
                                    });
                                }

                                setTimeout(function() {
                                    if (defaultAddress.subdistrict) {
                                        $('#subdistrict option').each(function() {
                                            if ($(this).text().trim() === defaultAddress.subdistrict) {
                                                $(this).prop('selected', true);
                                                $('#subdistrict').trigger('change');
                                                return false;
                                            }
                                        });
                                    }

                                    if (defaultAddress.postal_code) {
                                        $('#shipping_postal_code').val(defaultAddress.postal_code);
                                    }

                                    console.log('Auto-fill completed');
                                }, 800);
                            }, 800);
                        }, 800);
                    }
                }

                let attempts = 0;
                const maxAttempts = 10;
                const autoFillInterval = setInterval(function() {
                    attempts++;
                    if ($('#province option').length > 1) {
                        clearInterval(autoFillInterval);
                        autoFillAddress();
                    } else if (attempts >= maxAttempts) {
                        clearInterval(autoFillInterval);
                        console.log('Auto-fill timeout, trying once more');
                        autoFillAddress();
                    }
                }, 500);
            }

            // ============================================
            // SAVE TO LOCALSTORAGE (for guest)
            // ============================================

            function saveCheckoutData() {
                const data = {
                    shipping_name: $('#shipping_name').val(),
                    shipping_phone: $('#shipping_phone').val(),
                    shipping_address: $('#shipping_address').val(),
                    shipping_province: $('#province').val(),
                    shipping_city: $('#city').val(),
                    shipping_district: $('#district').val(),
                    shipping_subdistrict: $('#subdistrict').val(),
                    shipping_postal_code: $('#shipping_postal_code').val(),
                    saved_at: new Date().toISOString()
                };

                try {
                    localStorage.setItem('checkout_data', JSON.stringify(data));
                } catch(e) {}
            }

            $(document).on('change', '#shipping_name, #shipping_phone, #shipping_address, #province, #city, #district, #subdistrict', function() {
                if (!isLoggedIn) {
                    saveCheckoutData();
                }
            });

            // ============================================
            // RESTORE GUEST DATA
            // ============================================

            function restoreGuestData() {
                if (isLoggedIn) return;

                try {
                    const saved = localStorage.getItem('checkout_data');
                    if (saved) {
                        const data = JSON.parse(saved);
                        const savedDate = new Date(data.saved_at);
                        const now = new Date();
                        const diffDays = (now - savedDate) / (1000 * 60 * 60 * 24);

                        if (diffDays < 7) {
                            $('#shipping_name').val(data.shipping_name || '');
                            $('#shipping_phone').val(data.shipping_phone || '');
                            $('#shipping_address').val(data.shipping_address || '');

                            if (data.shipping_province) {
                                setTimeout(() => {
                                    $('#province').val(data.shipping_province).trigger('change');
                                }, 1500);
                            }
                        }
                    }
                } catch(e) {}
            }

            if (!isLoggedIn) {
                setTimeout(restoreGuestData, 2000);
            }

            // ============================================
            // UTILITY FUNCTIONS
            // ============================================

            function calculateTotalWeight() {
                let totalWeight = 0;
                @foreach ($cart as $item)
                    totalWeight += {{ $item['weight'] ?? 1000 }} * {{ $item['quantity'] }};
                @endforeach
                $('#total-weight').text(totalWeight);
                return totalWeight;
            }

            function updateTotal() {
                const subtotal = {{ $subtotal }};
                const shippingCost = parseInt($('#shipping_cost').val()) || 0;
                const voucherDiscount = parseInt($('#voucher-discount-text').text().replace(/[^0-9]/g, '')) || 0;
                const total = subtotal + shippingCost - voucherDiscount;
                $('#total-display').text('Rp ' + formatNumber(total));
                console.log('🔄 Total updated:', { subtotal, shippingCost, voucherDiscount, total });
            }

            function formatNumber(num) {
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            // ============================================
            // TOAST NOTIFICATION
            // ============================================

            function showToast(message, type = 'info') {
                const colors = {
                    success: '#22c55e',
                    error: '#ef4444',
                    warning: '#f59e0b',
                    info: '#076694'
                };

                const toast = $(`
                    <div class="toast-notification" style="
                        position: fixed;
                        bottom: 2vw;
                        right: 2vw;
                        padding: 1vw 1.5vw;
                        background: ${colors[type] || colors.info};
                        color: white;
                        border-radius: 0.7vw;
                        font-size: 0.85vw;
                        box-shadow: 0 0.2vw 1vw rgba(0,0,0,0.15);
                        z-index: 9999;
                        max-width: 25vw;
                        transform: translateY(120%);
                        transition: transform 0.3s ease;
                        font-family: inherit;
                    ">
                        ${message}
                    </div>
                `);

                $('body').append(toast);

                setTimeout(() => {
                    toast.css('transform', 'translateY(0)');
                }, 100);

                setTimeout(() => {
                    toast.css('transform', 'translateY(120%)');
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }

            // ============================================
            // INIT
            // ============================================

            calculateTotalWeight();
            $('#courier').prop('disabled', true);

        });
    </script>
@endsection
