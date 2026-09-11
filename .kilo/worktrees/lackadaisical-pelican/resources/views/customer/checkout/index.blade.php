@extends('layouts.customer')

@section('title', 'Checkout - Barokah Sport')

@section('content')

    <style>
        /* ============================================
           CHECKOUT CONTAINER
           ============================================ */
        .checkout-container {
            max-width: 100%;
            margin: 0 auto;
            background-color:#f9fafb;
            padding: 1.5vw 8vw;
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
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5vw;
            align-items: stretch;
        }

        .checkout-grid > form,
        .checkout-grid > .checkout-summary {
            display: flex;
            flex-direction: column;
            min-width: 0;
            width: 100%;
            box-sizing: border-box;
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

        .empty-cart-message p{
            font-size:1vw;
        }
        .empty-cart-message a{
            font-size:1vw;
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
        .summary-item-product{
            width: 75%;
            display: flex;
            gap: .8vw;
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
            margin-bottom:.3vw;
        }

        .checkout-summary .summary-item .item-image{
            width:5vw;
            overflow:hidden;
            border:.1vw solid #94a3b8;
            border-radius:.5vw;
            height:5vw;
            position:relative;
        }
        .checkout-summary .summary-item .item-image img{
            width:100%;
            height:100%;
            position:absolute;
            top:0;
            left:0;
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
        .summary-qty-layout{
            display:flex;
            gap:1vw;
            margin-top:.7vw;
        }
        .summary-qty{
            display:flex;
            align-items:center;
            background-color:white;
            border:.1vw solid #94a3b8;
            border-radius:.6vw;
            padding:.3vw;
        }
        .summary-qty button{
            width:1.3vw;
            height:1.3vw;
            outline:none;
            border:none;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:.9vw;
            cursor: pointer;
            background-color:transparent;
        }
        .summary-qty input{
            width:2vw;
            font-size:.85vw;
            height:1.3vw;
            background-color:transparent;
            outline:none;
            border:none;
            text-align:center;
        }
        .summary-remove{
            background-color:transparent;
            border:none;
            outline:none;
            cursor: pointer;
            font-size:.8vw;
            display:flex;
            align-items:center;
            justify-content:center;
            gap:.3vw;
            color:#e60023;
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
                font-size: 10vw;
                font-weight: 700;
                color: #0f172a;
                position: relative;
                display: inline-block;
            }

            .checkout-header p {
                font-size: 3.5vw;
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
                font-size: 5vw;
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
                font-size: 3.5vw;
                color: #94a3b8;
                margin-bottom: 3.5vw;
            }

            /* ============================================
            FORM ELEMENTS - MOBILE
            ============================================ */
            .form-grid {
                grid-template-columns: 1fr;
                gap: 3vw;
            }

            .form-grid .full-width {
                grid-column: 1;
            }

            .form-group {
                gap: 0.8vw;
            }

            .form-group label {
                font-size: 4vw;
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
                font-size: 4vw;
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
                font-size: 4vw;
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
                margin-bottom:4vw;
            }

            .form-group .error-text {
                font-size: 2.2vw;
                color: #ef4444;
                margin-top: 0.5vw;
                padding: 0.5vw 1.5vw;
                background: #fef2f2;
                border-radius: 0.8vw;
            }

            .shipping-popup-note{
                margin: 0 0 .9rem;
                padding: .7rem .8rem;
                border-radius: .6rem;
                background: #f0f9ff;
                color: #075985;
                font-size: 4vw;
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
                font-size: 4.5vw;
                font-weight: 500;
            }
            .shipping-option-name {
                font-size: 3.5vw;
                font-weight: 400;
            }
            .shipping-option-meta {
                display: block;
                margin-top: .18rem;
                color: #64748b;
                font-size: 3.5VW;
            }

            .popup-voucher-list .list-title {
                font-size: 3.8vw;
                margin-bottom: 2.2vw;
            }
            .empty-cart-message p{
                font-size:3.5vw;
            }
            .empty-cart-message a{
                font-size:3.5vw;
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
                font-size: 4.5vw;
                font-weight: 700;
                color: #0f172a;
                margin-bottom: 2.5vw;
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
                font-size: 4vw;
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

            .checkout-summary .summary-item .item-image {
                width: 20vw;
                overflow: hidden;
                border: .1vw solid #94a3b8;
                border-radius: 2.5vw;
                height: 20vw;
                position: relative;
            }
            .summary-item-product {
                width: 75%;
                display: flex;
                gap: 2.8vw;
            }
            .summary-qty {
                display: flex;
                align-items: center;
                background-color: white;
                border: .1vw solid #94a3b8;
                border-radius: .6vw;
                padding: 1.3vw;
            }
            .summary-qty button {
                width: 5.3vw;
                height: 5.3vw;
                outline: none;
                border: none;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 4vw;
                cursor: pointer;
                background-color: transparent;
            }
            .summary-qty input {
                width: 8vw;
                font-size: 3.5vw;
                height: 5.3vw;
                background-color: transparent;
                outline: none;
                border: none;
                text-align: center;
            }
            .summary-qty {
                display: flex;
                align-items: center;
                background-color: white;
                border: .1vw solid #94a3b8;
                border-radius: 1.6vw;
                padding: 1.3vw;
            }
            .summary-qty-layout {
                display: flex;
                gap: 3vw;
                margin-top: 2.7vw;
            }
            .summary-remove {
                background-color: transparent;
                border: none;
                outline: none;
                cursor: pointer;
                font-size: 3.5vw;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: .8vw;
                color: #e60023;
            }

            .summary-qty-layout {
                display: flex;
                gap: 1.6vw;
                margin-top: 1.7vw;
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
                font-size: 4vw;
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
                font-size: 4vw;
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
                font-size: 4.5vw;
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
            <form action="{{ route('customer.checkout.process') }}" style="width:100%;" method="POST" id="checkout-form">
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
                        <div class="form-group">
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
                            <select name="courier" id="courier" class="hidden" disabled>
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
                            <div id="shipping-error" class="error-text hidden" style="color:#ef4444;margin-top:0.5vw;">
                                ⚠️ Silakan pilih kurir dan layanan pengiriman terlebih dahulu!
                            </div>
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

            {{-- Summary --}}
            <div class="checkout-summary">
                <h2 class="summary-title">Ringkasan Pesanan</h2>

                {{-- Items --}}
                <div class="summary-items" id="summary-items">
                    @foreach ($cart as $key => $item)
                        <div class="summary-item"
                                data-item-key="{{ $key }}"
                                data-item-id="{{ $item['id'] ?? '' }}"
                                data-max-stock="{{ $item['stock'] ?? 999 }}"
                                data-weight="{{ (int) ($item['weight'] ?? 1000) }}"
                                data-price="{{ (int) ($item['price'] ?? 0) }}">
                            <div class="summary-item-product">
                                <div class="item-image">
                                    @if (!empty($item['image']))
                                        <img src="{{ Storage::url($item['image']) }}"
                                            alt="{{ $item['product_name'] }}"
                                            class="summary-item-image"
                                            loading="lazy"
                                            onerror="this.src='{{ asset('images/placeholder.png') }}'">
                                    @else
                                        <img src="{{ asset('images/placeholder.png') }}"
                                            alt="No image"
                                            class="summary-item-image">
                                    @endif
                                </div>
                                <div class="item-info">
                                    <div class="item-name">{{ $item['product_name'] }}</div>
                                    @if ($item['variant_name'])
                                        <div class="item-variant">{{ $item['variant_name'] }}</div>
                                    @endif
                                    <div class="summary-qty-layout">
                                        <div class="summary-qty">
                                            <button type="button" class="qty-btn qty-decrease" data-key="{{ $key }}">
                                                <iconify-icon icon="ant-design:minus-outlined"></iconify-icon>
                                            </button>
                                            <input type="number" class="qty-input" data-key="{{ $key }}"
                                                value="{{ $item['quantity'] }}" min="1" readonly>
                                            <button type="button" class="qty-btn qty-increase" data-key="{{ $key }}">
                                                <iconify-icon icon="ant-design:plus-outlined"></iconify-icon>
                                            </button>
                                        </div>
                                        <button type="button" class="summary-remove" data-key="{{ $key }}">
                                            <iconify-icon icon="bytesize:trash"></iconify-icon>
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                            {{-- 🔥 PASTIKAN CLASS INI SESUAI --}}
                            <span class="item-price item-price-{{ $key }}">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
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

                <div id="checkout-warning-summary" class="checkout-warning-summary hidden">
                    <div style="display:flex;align-items:center;gap:0.5vw;padding:0.5vw 0.8vw;background:#fef3c7;border:0.1vw solid #f59e0b;border-radius:0.6vw;color:#92400e;font-size:0.7vw;margin-top:0.5vw;">
                        <span>⚠️</span>
                        <span>Lengkapi data pengiriman dan pilih kurir terlebih dahulu</span>
                    </div>
                </div>

                {{-- Submit Button --}}
                <button type="button" class="btn-submit" id="btn-submit-order">
                    Buat Pesanan
                </button>

                <a href="{{ route('customer.cart.index') }}" class="btn-back" id="btn-back-to-cart">
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
        // ============================================
// GLOBAL VARIABLES
// ============================================
var updateTimeoutGlobal = null;

// ============================================
// UTILITY FUNCTIONS - GLOBAL SCOPE
// ============================================

function formatNumber(num) {
    if (num === undefined || num === null) num = 0;
    return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function showToast(message, type) {
    type = type || 'info';
    var colors = {
        success: '#22c55e',
        error: '#ef4444',
        warning: '#f59e0b',
        info: '#3b82f6'
    };

    var existing = document.querySelector('.toast-notification');
    if (existing) {
        existing.remove();
    }

    var toast = document.createElement('div');
    toast.className = 'toast-notification';
    toast.style.cssText = `
        position: fixed;
        bottom: 2vw;
        right: 2vw;
        padding: 1vw 1.5vw;
        background: ${colors[type] || colors.info};
        color: white;
        border-radius: 0.7vw;
        font-size: 0.85vw;
        box-shadow: 0 0.2vw 1vw rgba(0,0,0,0.15);
        z-index: 99999;
        max-width: 25vw;
        transform: translateY(120%);
        transition: transform 0.3s ease;
        font-family: inherit;
    `;
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(function() {
        toast.style.transform = 'translateY(0)';
    }, 100);

    setTimeout(function() {
        toast.style.transform = 'translateY(120%)';
        setTimeout(function() {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

function escapeHtml(value) {
    return String(value || '').replace(/[&<>'"]/g, function(char) {
        return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' })[char];
    });
}

// ============================================
// STOCK WARNING FUNCTIONS
// ============================================

function showStockWarning(key, message, type) {
    var $item = $(`.summary-item[data-item-key="${key}"]`);
    var $warning = $item.find('.stock-warning');

    if ($warning.length === 0) {
        $warning = $('<div class="stock-warning"></div>');
        $item.find('.item-info').append($warning);
    }

    var colors = {
        warning: '#f59e0b',
        error: '#ef4444',
        success: '#22c55e'
    };

    $warning.html('<iconify-icon icon="mdi:information-outline"></iconify-icon> ' + message);
    $warning.css({
        'color': colors[type] || '#f59e0b',
        'font-size': '0.6vw',
        'margin-top': '0.2vw',
        'display': 'block'
    });

    clearTimeout($warning.data('timeout'));
    var timeout = setTimeout(function() {
        $warning.fadeOut(300);
    }, 5000);
    $warning.data('timeout', timeout);
}

function updateStockDisplay(key, availableStock, currentQuantity) {
    $(`.summary-item[data-item-key="${key}"]`).data('max-stock', availableStock);

    if (currentQuantity >= availableStock) {
        $(`.qty-increase[data-key="${key}"]`).prop('disabled', true);
        showStockWarning(key, 'Stok maksimal ' + availableStock + ' item', 'warning');
    } else {
        $(`.qty-increase[data-key="${key}"]`).prop('disabled', false);
        var $warning = $(`.summary-item[data-item-key="${key}"] .stock-warning`);
        if ($warning.length > 0) {
            $warning.fadeOut(300);
        }
    }
}

function handleStockError(response, key) {
    var message = response.message || 'Gagal memperbarui kuantitas.';
    var availableStock = response.available_stock || 0;

    console.log('🔥 Stock Error:', { message, availableStock, key });

    if (availableStock > 0) {
        $(`.qty-input[data-key="${key}"]`).val(availableStock);
        showStockWarning(key, 'Stok tersisa ' + availableStock + ' item', 'warning');
        $(`.summary-item[data-item-key="${key}"]`).data('max-stock', availableStock);

        if (availableStock <= 1) {
            $(`.qty-increase[data-key="${key}"]`).prop('disabled', true);
        } else {
            $(`.qty-increase[data-key="${key}"]`).prop('disabled', false);
        }

        showToast('Stok tersisa ' + availableStock + ' item', 'warning');
        doUpdateCartItem(key, availableStock);
    } else {
        $(`.qty-input[data-key="${key}"]`).val(1);
        $(`.qty-increase[data-key="${key}"]`).prop('disabled', true);
        showStockWarning(key, 'Stok habis!', 'error');
        showToast('Stok habis!', 'error');
    }
}

// ============================================
// UPDATE CART ITEM FUNCTIONS
// ============================================

function updateCartItem(itemKey, newQuantity) {
    if (newQuantity < 1) {
        showToast('Kuantitas minimal 1', 'warning');
        return;
    }

    if (updateTimeoutGlobal) {
        clearTimeout(updateTimeoutGlobal);
    }

    updateTimeoutGlobal = setTimeout(function() {
        doUpdateCartItem(itemKey, newQuantity);
        updateTimeoutGlobal = null;
    }, 300);
}

function doUpdateCartItem(itemKey, newQuantity) {
    console.log('🔄 Updating cart item:', { itemKey, newQuantity });

    var key = String(itemKey);

    $(`.qty-btn[data-key="${key}"]`).prop('disabled', true);
    $(`.summary-remove[data-key="${key}"]`).prop('disabled', true);

    var $item = $(`.summary-item[data-item-key="${key}"]`);
    $item.addClass('loading-opacity');

    $.ajax({
        url: '/checkout/update-cart-item',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        data: JSON.stringify({
            item_key: key,
            quantity: newQuantity
        }),
        timeout: 10000,
        success: function(response) {
            console.log('✅ Update response:', response);

            if (response.success) {
                var data = response.data;

                $(`.qty-input[data-key="${key}"]`).val(data.quantity);
                $(`.item-price-${key}`).text(data.item_subtotal_formatted);
                $('#subtotal-display').text(data.subtotal_formatted);
                $('#shipping-cost-text').text(data.shipping_cost_formatted);

                if (data.voucher_discount > 0 && !data.free_shipping_applied) {
                    $('#voucher-discount-text').text(data.voucher_discount_formatted);
                    $('#voucher-discount-row').removeClass('hidden');
                    $('#applied-voucher-summary').removeClass('hidden');
                    $('#voucher-action-text').text('Ganti Voucher');
                } else if (data.free_shipping_applied) {
                    // 🔥 GRATIS ONGKIR: row voucher disembunyikan, ongkir sudah 0
                    window.freeShippingApplied = true;
                    $('#voucher-discount-text').text('Gratis Ongkir');
                    $('#voucher-discount-row').removeClass('hidden');
                    $('#applied-voucher-summary').removeClass('hidden');
                    $('#voucher-action-text').text('Ganti Voucher');
                } else {
                    $('#voucher-discount-row').addClass('hidden');
                    if (!data.has_voucher) {
                        $('#applied-voucher-summary').addClass('hidden');
                        $('#voucher-action-text').text('Pilih Voucher');
                    }
                }

                $('#total-display').text(data.total_formatted);

                // 🔥 BERAT TOTAL BERUBAH → SELALU HITUNG ULANG ONGKIR (DIPILIH ATAU BELUM)
                calculateTotalWeight();
                var zip = $('#shipping_postal_code').val();
                if (zip && zip !== '0' && zip.length >= 4) {
                    // 🔥 INGAT PILIHAN LAMA (JIKA ADA) AGAR OTOMATIS DITERAPKAN KEMBALI
                    if (window.shippingSelection) {
                        window.pendingReapplySelection = window.shippingSelection;
                        window.shippingSelection = null;
                        window.hasCourierSelected = false;
                        $('#shipping_cost').val(0);
                        $('#shipping-cost-text').text('Rp 0');
                        resetShippingPicker('Berat berubah — memperbarui ongkir...');
                    }
                    checkShippingCost(zip);
                }

                if (data.available_stock !== undefined) {
                    updateStockDisplay(key, data.available_stock, data.quantity);
                }

                showToast('Kuantitas berhasil diperbarui!', 'success');

                if (data.cart_empty) {
                    setTimeout(function() {
                        window.location.href = '/cart';
                    }, 1000);
                }
            } else {
                handleStockError(response, key);
            }
        },
        error: function(xhr) {
            console.error('❌ AJAX Error:', xhr);

            var response = null;
            try {
                response = JSON.parse(xhr.responseText);
            } catch(e) {}

            if (response) {
                handleStockError(response, key);
            } else {
                showToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
            }
        },
        complete: function() {
            $(`.qty-btn[data-key="${key}"]`).prop('disabled', false);
            $(`.summary-remove[data-key="${key}"]`).prop('disabled', false);
            $(`.summary-item[data-item-key="${key}"]`).removeClass('loading-opacity');
        }
    });
}

// ============================================
// REMOVE CART ITEM
// ============================================

function removeCartItem(itemKey) {
    var key = String(itemKey);
    var itemName = $(`.summary-item[data-item-key="${key}"] .item-name`).text() || 'Item';

    if (!confirm('Apakah Anda yakin ingin menghapus "' + itemName + '" dari pesanan?')) {
        return;
    }

    var $item = $(`.summary-item[data-item-key="${key}"]`);

    $item.addClass('removing');
    $item.css('opacity', '0.4');
    $item.css('transition', 'all 0.3s ease');

    $.ajax({
        url: '/checkout/remove-cart-item',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        data: JSON.stringify({
            item_key: key
        }),
        timeout: 10000,
        success: function(response) {
            if (response.success) {
                var data = response.data;

                $item.slideUp(300, function() {
                    $(this).remove();

                    $('#subtotal-display').text(data.subtotal_formatted);
                    $('#shipping-cost-text').text(data.shipping_cost_formatted);

                    if (data.voucher_discount > 0 && !data.free_shipping_applied) {
                        $('#voucher-discount-text').text(data.voucher_discount_formatted);
                        $('#voucher-discount-row').removeClass('hidden');
                    } else if (data.free_shipping_applied) {
                        window.freeShippingApplied = true;
                        $('#voucher-discount-text').text('Gratis Ongkir');
                        $('#voucher-discount-row').removeClass('hidden');
                    } else {
                        window.freeShippingApplied = false;
                        $('#voucher-discount-row').addClass('hidden');
                        $('#applied-voucher-summary').addClass('hidden');
                        $('#voucher-action-text').text('Pilih Voucher');
                    }

                    $('#total-display').text(data.total_formatted);
                    showToast('Item berhasil dihapus!', 'success');

                    if (data.cart_empty) {
                        setTimeout(function() {
                            window.location.href = '/cart';
                        }, 1500);
                    }
                });
            } else {
                showToast(response.message || 'Gagal menghapus item.', 'error');
                $item.css('opacity', '1');
                $item.removeClass('removing');
            }
        },
        error: function(xhr) {
            showToast('Gagal menghapus item. Silakan coba lagi.', 'error');
            $item.css('opacity', '1');
            $item.removeClass('removing');
        }
    });
}

// ============================================
// VOUCHER FUNCTIONS
// ============================================

function openVoucherPopup() {
    var popup = document.getElementById('voucher-popup');
    if (popup) {
        popup.classList.add('active');
        document.body.classList.add('popup-open');
        refreshVoucherList();
        setTimeout(function() {
            var input = document.getElementById('popup-voucher-input');
            if (input) input.focus();
        }, 350);
    }
}

function closeVoucherPopup() {
    var popup = document.getElementById('voucher-popup');
    if (popup) {
        popup.classList.remove('active');
        document.body.classList.remove('popup-open');
    }
}

function refreshVoucherList() {
    var container = document.getElementById('popup-voucher-items');
    var emptyContainer = document.getElementById('popup-voucher-empty');
    if (!container) {
        console.warn('Container popup-voucher-items not found');
        return;
    }

    // Ambil nilai ongkir saat ini
    var shippingCost = parseInt(document.getElementById('shipping_cost')?.value || 0);
    var subtotalText = document.getElementById('subtotal-display')?.textContent?.replace(/[^0-9]/g, '') || '0';
    var subtotal = parseInt(subtotalText) || 0;

    container.innerHTML = '<p style="text-align:center;padding:1vw 0;color:#94a3b8;">⏳ Memuat voucher...</p>';
    if (emptyContainer) emptyContainer.classList.add('hidden');

    // 🔥 KIRIM SHIPPING COST DAN SUBTOTAL KE SERVER
    var url = '{{ route("customer.checkout.vouchers-ajax") }}?shipping_cost=' + shippingCost + '&subtotal=' + subtotal;

    console.log('🔍 Fetching vouchers from:', url);

    fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        }
    })
    .then(function(response) {
        console.log('📡 Voucher response status:', response.status);

        if (!response.ok) {
            return response.text().then(function(text) {
                console.error('❌ Voucher error response:', text);
                throw new Error('Server error: ' + response.status + ' - ' + text.substring(0, 200));
            });
        }
        return response.json();
    })
    .then(function(data) {
        console.log('✅ Voucher data received:', data);

        if (data.success) {
            var vouchers = data.vouchers || [];

            if (vouchers.length > 0) {
                var html = '';
                var lockedHtml = '';
                vouchers.forEach(function(voucher) {
                    var isShippingVoucher = voucher.is_shipping_voucher || false;
                    var isApplicable = voucher.is_applicable !== false;

                    // 🔥 VOUCHER GRATIS ONGKIR YANG NOMINALNYA BELUM CUKUP → TETAP DITAMPILKAN (TERKUNCI)
                    if (!isApplicable && voucher.is_free_shipping) {
                        var remaining = voucher.remaining_amount || 0;
                        lockedHtml += `
                            <div class="popup-voucher-item locked"
                                data-voucher-code="${voucher.code}"
                                data-is-shipping="${isShippingVoucher}"
                                data-applicable="false">
                                <div class="item-content">
                                    <div class="item-info">
                                        <div class="voucher-card-top">
                                            <span class="item-discount">${voucher.discount_text || ''}</span>
                                        </div>
                                        <span class="item-name">${voucher.name}</span>
                                        <div class="voucher-card-meta">
                                            <span class="item-min">
                                                ${voucher.min_transaction_amount > 0 ? 'Min. belanja Rp ' + formatNumber(voucher.min_transaction_amount) : 'Min. belanja Rp 0'}
                                            </span>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-use" disabled style="opacity:.5;cursor:not-allowed;">
                                        Pakai
                                    </button>
                                </div>
                                <div class="popup-voucher-footer">
                                    <span class="popup-voucher-expiry">
                                        <iconify-icon icon="mdi:calendar-clock-outline"></iconify-icon>
                                        Berlaku sampai ${voucher.end_date_label || 'Tanpa Batas'}
                                    </span>
                                    <a href="${voucher.detail_url || '#'}" class="popup-voucher-terms" target="_blank" rel="noopener">
                                        Syarat &amp; Ketentuan
                                    </a>
                                </div>
                                <p class="voucher-locked-note" style="margin:0;padding:.5rem .8rem;background:#fef3c7;border-radius:.5rem;color:#92400e;font-size:.72rem;">
                                    🛒 Belanjakan <strong>Rp ${formatNumber(remaining)}</strong> lagi untuk pakai voucher
                                </p>
                            </div>
                        `;
                        return;
                    }

                    // Hanya tampilkan yang applicable
                    if (!isApplicable) return;

                    html += `
                        <div class="popup-voucher-item applicable"
                            data-voucher-code="${voucher.code}"
                            data-is-shipping="${isShippingVoucher}"
                            data-applicable="true">
                            <div class="item-content">
                                <div class="item-info">
                                    <div class="voucher-card-top">
                                        <span class="item-discount">${voucher.discount_text || ''}</span>
                                    </div>
                                    <span class="item-name">${voucher.name}</span>
                                    <div class="voucher-card-meta">
                                        <span class="item-min">
                                            ${voucher.min_transaction_amount > 0 ? 'Min. belanja Rp ' + formatNumber(voucher.min_transaction_amount) : 'Min. belanja Rp 0'}
                                            ${isShippingVoucher && data.shipping_cost > 0 ? ' · Ongkir Rp ' + formatNumber(data.shipping_cost) : ''}
                                        </span>
                                    </div>
                                </div>
                                <button type="button" data-code="${voucher.code}" class="btn-use btn-apply-item">
                                    Pakai
                                </button>
                            </div>
                            <div class="popup-voucher-footer">
                                <span class="popup-voucher-expiry">
                                    <iconify-icon icon="mdi:calendar-clock-outline"></iconify-icon>
                                    Berlaku sampai ${voucher.end_date_label || 'Tanpa Batas'}
                                </span>
                                <a href="${voucher.detail_url || '#'}" class="popup-voucher-terms" target="_blank" rel="noopener">
                                    Syarat &amp; Ketentuan
                                </a>
                            </div>
                        </div>
                    `;
                });

                // 🔥 GABUNGKAN: voucher applicable dulu, lalu voucher gratis ongkir terkunci di bawahnya
                var combinedHtml = html + (lockedHtml
                    ? '<h4 class="list-title" style="margin-top:1.2rem;font-size:.85rem;color:#92400e;">Belum bisa dipakai</h4>' + lockedHtml
                    : '');

                if (combinedHtml) {
                    container.innerHTML = combinedHtml;
                    if (emptyContainer) emptyContainer.classList.add('hidden');
                } else {
                    container.innerHTML = '';
                    showEmptyVoucherMessage(emptyContainer, data);
                }
            } else {
                container.innerHTML = '';
                showEmptyVoucherMessage(emptyContainer, data);
            }
        } else {
            container.innerHTML = '';
            showEmptyVoucherMessage(emptyContainer, { shipping_cost: shippingCost });
        }
    })
    .catch(function(error) {
        console.error('❌ Error loading vouchers:', error);
        container.innerHTML = '<p style="text-align:center;padding:1vw 0;color:#ef4444;">⚠️ Gagal memuat voucher. Silakan refresh halaman.</p>';
        if (emptyContainer) emptyContainer.classList.add('hidden');
    });
}

function showEmptyVoucherMessage(emptyContainer, data) {
    if (!emptyContainer) return;
    emptyContainer.classList.remove('hidden');

    var msg = emptyContainer.querySelector('p:first-child');
    var subMsg = emptyContainer.querySelector('p:last-child');

    if (msg) {
        if (data && data.shipping_cost > 0) {
            msg.textContent = '😊 Belum ada voucher yang tersedia untuk transaksi ini.';
            if (subMsg) subMsg.textContent = 'Coba gunakan kode voucher manual atau pilih kurir lain.';
        } else {
            msg.textContent = '😊 Pilih kurir terlebih dahulu untuk melihat voucher ongkir.';
            if (subMsg) subMsg.textContent = 'Voucher produk mungkin tersedia setelah kurir dipilih.';
        }
    }
}

function applyVoucherFromPopup(code) {
    var voucherCode = code || document.getElementById('popup-voucher-input').value;

    if (!voucherCode) {
        showToast('Masukkan kode voucher terlebih dahulu.', 'warning');
        return;
    }

    voucherCode = voucherCode.trim().toUpperCase();

    var btn = document.getElementById('btn-apply-manual-voucher');
    var originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Memproses...';

    // 🔥 AMBIL SHIPPING COST DARI UI (BUKAN SESSION)
    var shippingCost = parseInt($('#shipping_cost').val()) || 0;
    var courier = $('#courier').val() || 'JNE';
    var service = $('#service').val() || 'Reguler';

    fetch('{{ route("customer.checkout.apply-voucher") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            voucher_code: voucherCode,
            shipping_cost: shippingCost,
            courier: courier,
            service: service
        })
    })
    .then(function(response) {
        if (!response.ok) {
            return response.json().then(function(data) {
                throw {
                    status: response.status,
                    data: data,
                    message: data.message || 'Gagal menerapkan voucher'
                };
            });
        }
        return response.json();
    })
    .then(function(data) {
        console.log('✅ Apply voucher response:', data);

        if (data.success) {
            // 🔥 UPDATE UI DARI RESPONSE (TANPA SESSION)
            updateVoucherUIFromResponse(data);
            closeVoucherPopup();

            var message = data.message || 'Voucher berhasil diterapkan!';
            if (data.is_free_shipping) {
                message = '🎁 Gratis Ongkir berhasil diterapkan!';
            } else if (data.shipping_discount > 0) {
                message = '🚚 Diskon Ongkir Rp ' + formatNumber(data.shipping_discount) + ' berhasil diterapkan!';
            }
            showToast(message, 'success');
        } else {
            showToast(data.message || 'Gagal menerapkan voucher.', 'error');
        }
    })
    .catch(function(error) {
        console.error('❌ Apply voucher error:', error);
        var message = error.message || 'Terjadi kesalahan. Silakan coba lagi.';
        if (error.data && error.data.message) {
            message = error.data.message;
        }
        showToast('❌ ' + message, 'error');
    })
    .finally(function() {
        btn.disabled = false;
        btn.textContent = originalText;
    });
}

function updateVoucherUIFromResponse(data) {
    // 🔥 FLAG GRATIS ONGKIR: diskon ongkir TIDAK boleh dikurangkan lagi dari total (hindari pengurangan ganda)
    window.freeShippingApplied = !!data.is_free_shipping;

    // Update voucher summary
    var summary = document.getElementById('applied-voucher-summary');
    if (summary) {
        summary.classList.remove('hidden');
        var nameEl = summary.querySelector('.name');
        var discountEl = summary.querySelector('.discount');
        if (nameEl) nameEl.textContent = data.voucher_name || data.voucher.name;
        if (discountEl) discountEl.textContent = window.freeShippingApplied
            ? 'Gratis Ongkir'
            : 'Dapat potongan Rp ' + formatNumber(data.voucher_discount || data.discount);
    }

    // Update voucher discount row
    var voucherRow = document.getElementById('voucher-discount-row');
    var discountText = document.getElementById('voucher-discount-text');
    if (discountText) {
        if (window.freeShippingApplied) {
            // 🔥 Potongan sudah tercermin di baris Ongkir (Gratis Ongkir) — JANGAN ditampilkan lagi
            discountText.textContent = 'Rp 0';
            if (voucherRow) voucherRow.classList.add('hidden');
        } else {
            discountText.textContent = '-Rp ' + formatNumber(data.voucher_discount || data.discount);
            if (voucherRow) voucherRow.classList.remove('hidden');
        }
    }

    // Update action button
    var actionText = document.getElementById('voucher-action-text');
    if (actionText) actionText.textContent = 'Ganti Voucher';

    // Update shipping cost jika ada perubahan
    if (data.new_shipping_cost !== undefined) {
        var shippingCostText = document.getElementById('shipping-cost-text');
        if (shippingCostText) {
            shippingCostText.textContent = 'Rp ' + formatNumber(data.new_shipping_cost);
        }
        $('#shipping_cost').val(data.new_shipping_cost);
    }

    // Update total
    if (data.new_total !== undefined) {
        $('#total-display').text(data.new_total_formatted);
    }

    // Update subtotal
    if (data.new_subtotal !== undefined) {
        $('#subtotal-display').text(data.new_subtotal_formatted);
    }

    // Update shipping row
    if (data.is_free_shipping) {
        $('#shipping-cost-text').text('Gratis Ongkir');
        $('#shipping_cost').val(0);
    }

    // 🔥 TOTAL FINAL DARI SERVER (sudah benar, tidak dikurangi ganda)
    if (data.new_total !== undefined && data.new_total_formatted) {
        $('#total-display').text(data.new_total_formatted);
    }

    // Simpan data voucher ke hidden input untuk proses checkout
    if (data.voucher_code) {
        $('#applied-voucher-code').val(data.voucher_code);
        $('#applied-voucher-discount').val(data.voucher_discount || data.discount);
    }
}

function reapplyVoucherAfterShippingChange() {
    // 🔥 HITUNG ULANG POTONGAN VOUCHER (TERMASUK GRATIS ONGKIR) SETELAH ONGKIR BERUBAH
    var code = ($('#applied-voucher-code').val() || '').trim();
    if (!code) return;

    var sel = window.shippingSelection || {};
    fetch('{{ route("customer.checkout.apply-voucher") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            voucher_code: code,
            shipping_cost: parseInt($('#shipping_cost').val()) || 0,
            courier: sel.courier || '',
            service: sel.service || ''
        })
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            updateVoucherUIFromResponse(data);
            console.log('✅ Voucher recalculated after shipping change:', data);
        } else {
            // 🔥 VOUCHER TIDAK LAGI BERLAKU UNTUK ONGKIR BARU → RESET
            resetVoucherUI();
            $('#applied-voucher-code').val('');
            $('#applied-voucher-discount').val(0);
            showToast(data.message || 'Voucher dibatalkan karena syarat tidak terpenuhi setelah ongkir berubah.', 'warning');
        }
    })
    .catch(function(err) {
        console.warn('⚠️ Gagal menghitung ulang voucher:', err);
    });
}

function removeVoucher() {
    var removeBtn = document.getElementById('btn-remove-voucher');
    if (removeBtn) {
        removeBtn.disabled = true;
        removeBtn.innerHTML = '...';
    }

    fetch('{{ route("customer.checkout.remove-voucher") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(function(response) {
        if (!response.ok) {
            return response.json().then(function(err) {
                throw new Error(err.message || 'Gagal membatalkan voucher');
            });
        }
        return response.json();
    })
    .then(function(data) {
        if (data.success) {
            // 🔥 RESET UI VOUCHER
            resetVoucherUI();

            // Reset hidden inputs
            $('#applied-voucher-code').val('');
            $('#applied-voucher-discount').val(0);

            // Update total
            if (data.new_total !== undefined) {
                $('#total-display').text(data.new_total_formatted);
            }

            showToast(data.message || 'Voucher dibatalkan.', 'info');
        } else {
            showToast(data.message || 'Gagal membatalkan voucher.', 'error');
        }
    })
    .catch(function(error) {
        showToast(error.message || 'Gagal membatalkan voucher.', 'error');
    })
    .finally(function() {
        if (removeBtn) {
            removeBtn.disabled = false;
            removeBtn.innerHTML = '✕';
        }
    });
}

function updateVoucherUI(data) {
    var summary = document.getElementById('applied-voucher-summary');
    if (summary) {
        summary.classList.remove('hidden');
        var codeEl = summary.querySelector('.code');
        var nameEl = summary.querySelector('.name');
        var discountEl = summary.querySelector('.discount');
        if (codeEl) codeEl.textContent = data.voucher.code;
        if (nameEl) nameEl.textContent = data.voucher.name + (data.is_free_shipping ? ' 🎁' : '');
        if (discountEl) discountEl.textContent = 'Dapat potongan Rp ' + formatNumber(data.discount);
    }

    var voucherRow = document.getElementById('voucher-discount-row');
    var discountText = document.getElementById('voucher-discount-text');
    if (discountText) {
        if (data.discount > 0) {
            discountText.textContent = '-Rp ' + formatNumber(data.discount);
            if (voucherRow) voucherRow.classList.remove('hidden');
        } else {
            if (voucherRow) voucherRow.classList.add('hidden');
        }
    }

    var productRow = document.getElementById('product-discount-row');
    var productText = document.getElementById('product-discount-text');
    if (productRow && productText) {
        if (data.product_discount > 0) {
            productRow.classList.remove('hidden');
            productText.textContent = '-Rp ' + formatNumber(data.product_discount);
        } else {
            productRow.classList.add('hidden');
        }
    }

    var shippingRow = document.getElementById('shipping-discount-row');
    var shippingText = document.getElementById('shipping-discount-text');
    var shippingLabel = document.getElementById('shipping-discount-label');

    if (shippingRow && shippingText) {
        if (data.shipping_discount > 0 || data.is_free_shipping) {
            shippingRow.classList.remove('hidden');
            if (data.is_free_shipping) {
                shippingText.textContent = 'Gratis Ongkir';
                if (shippingLabel) shippingLabel.textContent = 'Gratis Ongkir';
            } else {
                shippingText.textContent = '-Rp ' + formatNumber(data.shipping_discount);
                if (shippingLabel) shippingLabel.textContent = 'Diskon Ongkir';
            }
        } else {
            shippingRow.classList.add('hidden');
        }
    }

    if (data.new_shipping_cost !== undefined) {
        var shippingCostText = document.getElementById('shipping-cost-text');
        if (shippingCostText) {
            shippingCostText.textContent = 'Rp ' + formatNumber(data.new_shipping_cost);
        }
        var shippingCostInput = document.getElementById('shipping_cost');
        if (shippingCostInput) {
            shippingCostInput.value = data.new_shipping_cost;
        }
    }

    var actionText = document.getElementById('voucher-action-text');
    if (actionText) actionText.textContent = 'Ganti Voucher';

    updateTotalWithVoucher(data.discount);
}

function resetVoucherUI() {
    // 🔥 Reset flag gratis ongkir agar kalkulasi kembali normal
    window.freeShippingApplied = false;

    var summary = document.getElementById('applied-voucher-summary');
    if (summary) summary.classList.add('hidden');

    var voucherRow = document.getElementById('voucher-discount-row');
    if (voucherRow) voucherRow.classList.add('hidden');

    var discountText = document.getElementById('voucher-discount-text');
    if (discountText) discountText.textContent = 'Rp 0';

    var actionText = document.getElementById('voucher-action-text');
    if (actionText) actionText.textContent = 'Pilih Voucher';

    var shippingCostText = document.getElementById('shipping-cost-text');
    if (shippingCostText) {
        shippingCostText.textContent = 'Belum dipilih';
    }

    $('#shipping_cost').val(0);

    var input = document.getElementById('popup-voucher-input');
    if (input) input.value = '';

    // Recalculate total
    updateTotalAfterVoucherRemove();
}

function updateTotalAfterVoucherRemove() {
    var subtotalText = $('#subtotal-display').text().replace(/[^0-9]/g, '');
    var subtotal = parseInt(subtotalText) || 0;
    var shippingCost = parseInt($('#shipping_cost').val()) || 0;
    var total = subtotal + shippingCost;

    $('#total-display').text('Rp ' + formatNumber(total));
}

function updateVoucherSession(data) {
    if (data && data.voucher) {
        var hiddenVoucher = document.getElementById('applied-voucher-code');
        if (!hiddenVoucher) {
            hiddenVoucher = document.createElement('input');
            hiddenVoucher.type = 'hidden';
            hiddenVoucher.id = 'applied-voucher-code';
            hiddenVoucher.name = 'voucher_code';
            var form = document.getElementById('checkout-form');
            if (form) form.appendChild(hiddenVoucher);
        }
        if (hiddenVoucher) hiddenVoucher.value = data.voucher.code;

        var hiddenDiscount = document.getElementById('applied-voucher-discount');
        if (!hiddenDiscount) {
            hiddenDiscount = document.createElement('input');
            hiddenDiscount.type = 'hidden';
            hiddenDiscount.id = 'applied-voucher-discount';
            hiddenDiscount.name = 'voucher_discount';
            var form = document.getElementById('checkout-form');
            if (form) form.appendChild(hiddenDiscount);
        }
        if (hiddenDiscount) hiddenDiscount.value = data.discount;
    } else {
        var hiddenVoucher = document.getElementById('applied-voucher-code');
        if (hiddenVoucher) hiddenVoucher.remove();

        var hiddenDiscount = document.getElementById('applied-voucher-discount');
        if (hiddenDiscount) hiddenDiscount.remove();
    }
}

function updateTotalWithVoucher(discount) {
    var subtotalEl = document.getElementById('subtotal-display');
    var shippingEl = document.getElementById('shipping_cost');
    var totalEl = document.getElementById('total-display');

    if (!subtotalEl || !totalEl) return;

    var subtotal = parseFloat(subtotalEl.textContent.replace(/[^0-9]/g, '')) || 0;
    var shippingCost = parseInt(shippingEl ? shippingEl.value : 0) || 0;
    // 🔥 GRATIS ONGKIR: diskon sudah tercermin di ongkir 0
    if (window.freeShippingApplied) discount = 0;
    var total = subtotal + shippingCost - discount;
    if (total < 0) total = 0;
    totalEl.textContent = 'Rp ' + formatNumber(total);
}

// ============================================
// SHIPPING POPUP FUNCTIONS
// ============================================

var pendingShippingOption = null;

// 🔥 SINGLE SOURCE OF TRUTH untuk pilihan pengiriman
window.shippingSelection = null; // { courier, service, courierName, display, cost, etd }

function resetShippingPicker(message) {
    message = message || 'Lengkapi alamat tujuan terlebih dahulu';
    pendingShippingOption = null;
    window.shippingSelection = null;
    window.pendingReapplySelection = null;
    $('#shipping-popup').addClass('hidden');
    $('#shipping-popup-confirm').prop('disabled', true).text('Pilih layanan');
    $('#shipping-picker-button').prop('disabled', true).html(
        '<span><span class="picker-title">Pilih kurir dan layanan</span><span class="picker-subtitle">' + escapeHtml(message) + '</span></span><span class="picker-arrow">›</span>'
    );
}

function renderShippingOptions(couriers) {
    var html = '';
    var totalServices = 0;

    var sortedCouriers = [...couriers].sort(function(a, b) {
        return String(a.name).localeCompare(String(b.name), 'id');
    });

    sortedCouriers.forEach(function(courier) {
        if (!courier.services || courier.services.length === 0) {
            return;
        }

        var seenNames = new Set();
        var uniqueServices = courier.services.filter(function(service) {
            var name = String(service.name || service.service || 'Reguler').trim();
            if (seenNames.has(name)) {
                return false;
            }
            seenNames.add(name);
            return true;
        });

        var services = uniqueServices
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

        html += '<section class="shipping-courier-group">';
        html += '<h4 class="shipping-courier-name">' + escapeHtml(courier.name) + '</h4>';

        services.forEach(function(service) {
            var serviceName = service.name || service.service || 'Reguler';

            if (serviceName.includes(' - ')) {
                var parts = serviceName.split(' - ');
                if (parts[0] === parts[1]) {
                    serviceName = parts[0];
                }
            }
            serviceName = serviceName.replace(/\s*-\s*$/, '').trim();

            var cost = parseInt(service.cost || 0);
            var etd = service.etd || service.duration || '-';

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
                            ${escapeHtml(service.description || serviceName)}
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

        html += '</section>';
    });

    $('#shipping-options').html(
        html || '<p class="shipping-popup-note">Layanan pengiriman belum tersedia.</p>'
    );

    var totalCouriers = sortedCouriers.filter(function(courier) {
        return courier.services && courier.services.length;
    }).length;

    var buttonText = totalCouriers > 0
        ? totalCouriers + ' kurir tersedia (' + totalServices + ' layanan)'
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

// ============================================
// DOCUMENT READY - MAIN
// ============================================

$(document).ready(function() {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    window.hasCourierSelected = false;

    // 🔥 EKSPOR FUNGSI KE SCOPE GLOBAL (dipakai doUpdateCartItem di luar closure ini)
    window.calculateTotalWeight = calculateTotalWeight;
    window.checkShippingCost = checkShippingCost;
    window.resetShippingPicker = resetShippingPicker;

    // 🔥 CEK APAKAH KURIR SUDAH DIPILIH DARI SESSION
    var shippingCost = parseInt($('#shipping_cost').val()) || 0;
    if (shippingCost > 0) {
        window.hasCourierSelected = true;
    }

    // ============================================
    // QUANTITY BUTTON HANDLERS
    // ============================================

    $(document).on('click', '.qty-increase', function() {
        var key = $(this).data('key');
        var $input = $(`.qty-input[data-key="${key}"]`);
        var currentQty = parseInt($input.val()) || 1;

        var $item = $(`.summary-item[data-item-key="${key}"]`);
        var maxStock = parseInt($item.data('max-stock')) || 999;

        if (currentQty >= maxStock) {
            showStockWarning(key, 'Stok maksimal ' + maxStock + ' item', 'warning');
            showToast('Stok maksimal ' + maxStock + ' item', 'warning');
            $(this).prop('disabled', true);
            return;
        }

        var newQty = currentQty + 1;
        $input.val(newQty);
        updateCartItem(String(key), newQty);
    });

    $(document).on('click', '.qty-decrease', function() {
        var key = $(this).data('key');
        var $input = $(`.qty-input[data-key="${key}"]`);
        var currentQty = parseInt($input.val()) || 1;

        console.log('🔽 Decrease clicked:', { key, currentQty });

        if (currentQty > 1) {
            var newQty = currentQty - 1;
            $input.val(newQty);
            updateCartItem(String(key), newQty);
            $(`.qty-increase[data-key="${key}"]`).prop('disabled', false);
        } else {
            showToast('Kuantitas minimal 1', 'warning');
        }
    });

    $(document).on('keydown', '.qty-input', function(e) {
        if (e.key === 'Enter') {
            var key = $(this).data('key');
            var newQty = parseInt($(this).val()) || 1;

            if (newQty < 1) {
                newQty = 1;
                $(this).val(1);
            }

            updateCartItem(String(key), newQty);
            $(this).blur();
        }
    });

    // ============================================
    // REMOVE ITEM HANDLER
    // ============================================

    $(document).on('click', '.summary-remove', function() {
        var key = $(this).data('key');
        removeCartItem(key);
    });

    // ============================================
    // SUBMIT ORDER HANDLER
    // ============================================

    $(document).on('click', '#btn-submit-order', function(e) {
    e.preventDefault();

    var $btn = $(this);
    var form = document.getElementById('checkout-form');
    var errors = [];

    // 🔥 CEK SHIPPING COST DARI HIDDEN INPUT
    var shippingCost = parseInt($('#shipping_cost').val()) || 0;

    console.log('🔍 Submit order - shippingCost:', shippingCost);
    console.log('🔍 hasCourierSelected:', window.hasCourierSelected);
    console.log('🔍 shippingCost from hidden:', $('#shipping_cost').val());
    console.log('🔍 courier value:', $('#courier').val());
    console.log('🔍 service value:', $('#service').val());

    // 🔥 CEK APAKAH KURIR SUDAH DIPILIH (dari single source of truth)
    var selection = window.shippingSelection;
    var shippingCost = selection ? parseInt(selection.cost) || 0 : (parseInt($('#shipping_cost').val()) || 0);
    var hasShipping = !!(selection && selection.courier && selection.service) && shippingCost >= 0 && (shippingCost > 0 || window.hasCourierSelected);

    if (!hasShipping) {
        errors.push('⚠️ Silakan pilih kurir dan layanan pengiriman terlebih dahulu!');
        $('#shipping-error').removeClass('hidden');

        // 🔥 SCROLL KE SECTION SHIPPING
        var shippingSection = $('#courier').closest('.checkout-section');
        if (shippingSection.length) {
            $('html, body').animate({
                scrollTop: shippingSection.offset().top - 100
            }, 500);
        }
    } else {
        $('#shipping-error').addClass('hidden');
        $('#shipping-warning').addClass('hidden');
    }

    // CEK FIELD WAJIB
    var requiredFields = form.querySelectorAll('input:not([type="hidden"]):not([disabled]), select:not([disabled]), textarea:not([disabled])');
    var firstInvalid = null;

    requiredFields.forEach(function(field) {
        if (field.hasAttribute('required') && !field.value.trim()) {
            var label = field.closest('.form-group')?.querySelector('label')?.textContent?.trim() || field.name;
            errors.push('⚠️ ' + label + ' wajib diisi!');
            if (!firstInvalid) {
                firstInvalid = field;
            }
        }
    });

    if (errors.length > 0) {
        showToast(errors[0], 'error');
        if (firstInvalid) {
            firstInvalid.focus();
            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return;
    }

    // 🔥 PASTIKAN SHIPPING_COST, COURIER, SERVICE TERISI DARI SELECTION
    if (hasShipping) {
        var selection = window.shippingSelection;
        var shippingCost = parseInt($('#shipping_cost').val()) || 0;

        if (!$('input[name="shipping_cost"]').length) {
            $('<input>').attr({
                type: 'hidden',
                name: 'shipping_cost',
                value: shippingCost
            }).appendTo(form);
        } else {
            $('input[name="shipping_cost"]').val(shippingCost);
        }

        // Tambahkan courier dan service
        if (!$('input[name="courier"]').length) {
            $('<input>').attr({
                type: 'hidden',
                name: 'courier',
                value: selection.courier
            }).appendTo(form);
        } else {
            $('input[name="courier"]').val(selection.courier);
        }
        $('#courier').val(selection.courier);

        if (!$('input[name="shipping_service"]').length) {
            $('<input>').attr({
                type: 'hidden',
                name: 'shipping_service',
                value: selection.service
            }).appendTo(form);
        } else {
            $('input[name="shipping_service"]').val(selection.service);
        }
        $('#service').val(selection.service);
    }

    $btn.prop('disabled', true).text('Memproses...');
    form.submit();
});
    var initialShippingCost = parseInt($('#shipping_cost').val()) || 0;
    if (initialShippingCost > 0 && !window.shippingSelection) {
        // 🔥 REKONSTRUKSI SELECTION DARI SESSION/HIDDEN INPUT
        window.shippingSelection = {
            courier: String($('#courier').val() || ''),
            service: String($('#service').val() || ''),
            courierName: String($('#courier option:selected').text() || $('#courier').val() || '').trim().toUpperCase(),
            display: String($('#service option:selected').text() || $('#service').val() || '').trim(),
            cost: initialShippingCost,
            etd: '-'
        };
    }
    if (initialShippingCost > 0) {
        window.hasCourierSelected = true;
        window.shippingCost = initialShippingCost;

        // Update total
        updateTotalAfterShipping(initialShippingCost);

        // Sembunyikan warning
        $('#shipping-warning').addClass('hidden');
        $('#shipping-error').addClass('hidden');

        console.log('✅ Initial shipping cost loaded:', initialShippingCost);
    }

    $('#checkout-form').on('submit', function(e) {
        var selection = window.shippingSelection;

        console.log('🔍 Form submit - selection:', selection);

        // Pastikan kurir & layanan sudah dipilih
        if (!selection || !selection.courier || !selection.service) {
            e.preventDefault();
            showToast('⚠️ Silakan pilih kurir dan layanan pengiriman terlebih dahulu!', 'warning');
            $('#shipping-error').removeClass('hidden');
            return false;
        }

        var shippingCost = parseInt($('#shipping_cost').val()) || 0;

        // Pastikan hidden fields terisi dari selection (ongkir dari hidden input, bisa 0 jika gratis ongkir)
        if (!$('input[name="shipping_cost"]').length) {
            $('<input>').attr({
                type: 'hidden',
                name: 'shipping_cost',
                value: shippingCost
            }).appendTo(this);
        } else {
            $('input[name="shipping_cost"]').val(shippingCost);
        }

        if (!$('input[name="courier"]').length) {
            $('<input>').attr({
                type: 'hidden',
                name: 'courier',
                value: selection.courier
            }).appendTo(this);
        } else {
            $('input[name="courier"]').val(selection.courier);
        }

        if (!$('input[name="shipping_service"]').length) {
            $('<input>').attr({
                type: 'hidden',
                name: 'shipping_service',
                value: selection.service
            }).appendTo(this);
        } else {
            $('input[name="shipping_service"]').val(selection.service);
        }

        return true;
    });

    // ============================================
    // SHIPPING POPUP HANDLERS
    // ============================================

    $('#shipping-picker-button').on('click', function() {
        if (!$(this).prop('disabled')) {
            $('#shipping-popup').removeClass('hidden');
        }
    });

    $(document).on('click', '[data-close-shipping-popup]', function() {
        $('#shipping-popup').addClass('hidden');
    });

        $(document).on('click', '.shipping-option', function() {
        var $option = $(this);
        pendingShippingOption = {
            courier: $option.data('courier'),
            service: $option.data('service-code'),
            display: $option.find('.shipping-option-name').text().trim(),
            cost: parseInt($option.data('cost')),
            etd: $option.data('etd')
        };
        $('.shipping-option').removeClass('selected');
        $option.addClass('selected');
        $('#shipping-popup-confirm').prop('disabled', false).text('Pilih layanan ini');
    });

        $('#shipping-popup-confirm').on('click', function() {
        if (!pendingShippingOption) return;

        var selection = {
            courier: String(pendingShippingOption.courier || ''),
            service: String(pendingShippingOption.service || ''),
            display: String(pendingShippingOption.display || ''),
            cost: parseInt(pendingShippingOption.cost) || 0,
            etd: String(pendingShippingOption.etd || '-')
        };

        var couriers = $('#courier').data('shipping-rates') || {};
        var selectedCourier = couriers[selection.courier];
        selection.courierName = String(selectedCourier?.name || selection.courier || '').toUpperCase();
        if (!selection.service) {
            selection.service = selection.display || 'Reguler';
        }

        // 🔥 SIMPAN SEBAGAI SATU-SATUNYA SUMBER KEBENARAN
        window.shippingSelection = selection;

        // 🔥 UPDATE UI INSTANT
        updateShippingUI(selection);

        $('#shipping-popup').addClass('hidden');
        pendingShippingOption = null;
    });

function updateShippingUI(selection) {
    var courierCode = selection.courier;
    var serviceCode = selection.service;
    var cost = parseInt(selection.cost) || 0;
    var etd = selection.etd || '-';
    var courierName = selection.courierName || String(courierCode || '').toUpperCase();
    var serviceDisplayName = selection.display || serviceCode;

    // 🔥 UPDATE HIDDEN INPUTS & SELECTS
    // Pastikan select memiliki option agar .val() bekerja
    if ($('#courier option[value="' + courierCode + '"]').length === 0) {
        $('#courier').append(new Option(courierName, courierCode));
    }
    $('#courier').val(courierCode).prop('disabled', false);

    // Update service select dengan option yang dipilih
    $('#service').html('<option value="' + escapeHtml(serviceCode) + '" selected>' + escapeHtml(serviceDisplayName) + '</option>').prop('disabled', false);

    $('#shipping_cost').val(cost);

    // 🔥 FORMAT HARGA
    var costDisplay = 'Rp ' + formatNumber(cost);

    // 🔥 UPDATE RINGKASAN PESANAN (KANAN) — SELALU DILAKUKAN DULU
    $('#shipping-cost-text').text(costDisplay);

    // 🔥 UPDATE TAMPILAN DI SECTION PENGIRIMAN
    const costDisplayEl = document.getElementById('shipping-cost-display');
    if (costDisplayEl) {
        costDisplayEl.innerHTML = `
            <div class="success">
                <div style="font-weight:600;">${escapeHtml(courierName)} · ${escapeHtml(serviceDisplayName)}</div>
                <div style="font-size:0.65vw;color:#94a3b8;">Estimasi: ${escapeHtml(String(etd))}</div>
                <div style="font-size:0.8vw;color:#0f172a;font-weight:700;margin-top:0.2vw;">${costDisplay}</div>
            </div>
        `;
    }

    // 🔥 UPDATE PICKER BUTTON
    $('#shipping-picker-button').html(`
        <span>
            <span class="picker-title">${escapeHtml(courierName)} · ${escapeHtml(serviceDisplayName)}</span>
            <span class="picker-subtitle">Estimasi ${escapeHtml(String(etd))} · ${costDisplay}</span>
        </span>
        <span class="picker-arrow">›</span>
    `);

    // 🔥 UPDATE TOTAL AKHIR
    updateTotalAfterShipping(cost);

    // 🔥 SEMBUNYIKAN ERROR
    $('#shipping-error').addClass('hidden');
    $('#shipping-warning').addClass('hidden');

    window.hasCourierSelected = true;

    // 🔥 HITUNG ULANG POTONGAN VOUCHER (TERMASUK GRATIS ONGKIR)
    reapplyVoucherAfterShippingChange();
}
    function updateTotalAfterShipping(shippingCost) {
    // 🔥 AMBIL SUBTOTAL DARI UI
    var subtotalText = $('#subtotal-display').text().replace(/[^0-9]/g, '');
    var subtotal = parseInt(subtotalText) || 0;

    // 🔥 AMBIL VOUCHER DISCOUNT
    var voucherDiscount = 0;
    var voucherDiscountText = $('#voucher-discount-text').text().replace(/[^0-9]/g, '');
    if (voucherDiscountText) {
        voucherDiscount = parseInt(voucherDiscountText) || 0;
    }

    // 🔥 AMBIL DARI HIDDEN INPUT JUGA
    var hiddenDiscount = parseInt($('#applied-voucher-discount').val()) || 0;
    if (hiddenDiscount > voucherDiscount) {
        voucherDiscount = hiddenDiscount;
    }

    // 🔥 GRATIS ONGKIR: potongan ongkir JANGAN dikurangkan lagi (sudah tercermin di ongkir = 0)
    if (window.freeShippingApplied) {
        voucherDiscount = 0;
    }

    // 🔥 HITUNG TOTAL
    var total = subtotal + shippingCost - voucherDiscount;
    if (total < 0) total = 0;

    // 🔥 UPDATE UI
    $('#total-display').text('Rp ' + formatNumber(total));
    $('#shipping_cost').val(shippingCost);
    $('#shipping-cost-text').text('Rp ' + formatNumber(shippingCost));

    // 🔥 SET FLAG
    window.hasCourierSelected = true;
    window.shippingCost = shippingCost;

    // 🔥 SIMPAN KE SESSION VIA AJAX (SYNC)
    $.ajax({
        url: '/checkout/update-shipping',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        data: JSON.stringify({
            shipping_cost: shippingCost,
            courier: $('#courier').val(),
            service: $('#service').val()
        }),
        async: false, // 🔥 SYNC AGAR LANGSUNG TERSIMPAN
        success: function(response) {
            console.log('✅ Shipping total updated:', response);
            if (typeof refreshVoucherList === 'function') refreshVoucherList();
        },
        error: function() {
            console.warn('⚠️ Failed to update shipping total via AJAX');
        }
    });

    console.log('🔄 Total updated after shipping:', {
        subtotal: subtotal,
        shippingCost: shippingCost,
        voucherDiscount: voucherDiscount,
        total: total,
        hasCourierSelected: window.hasCourierSelected
    });
}

    // ============================================
    // VOUCHER POPUP HANDLERS
    // ============================================

    $('#btn-open-voucher').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        openVoucherPopup();
    });

    $('#close-voucher-popup').on('click', function(e) {
        e.preventDefault();
        closeVoucherPopup();
    });

    $('#voucher-popup .popup_slide_overlay').on('click', function(e) {
        e.preventDefault();
        closeVoucherPopup();
    });

    $('#btn-apply-manual-voucher').on('click', function(e) {
        e.preventDefault();
        applyVoucherFromPopup();
    });

    $('#popup-voucher-input').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            applyVoucherFromPopup();
        }
    });

    $(document).on('click', '.btn-apply-item', function() {
        var code = $(this).data('code');
        if (code) {
            applyVoucherFromPopup(code);
        }
    });

    $('#btn-remove-voucher').on('click', function(e) {
        e.preventDefault();
        removeVoucher();
    });

    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            closeVoucherPopup();
        }
    });

    $(document).on('click', function(e) {
        var popup = document.getElementById('voucher-popup');
        if (popup && popup.classList.contains('active')) {
            var isInside = popup.contains(e.target);
            var isOpenBtn = e.target.closest('#btn-open-voucher') || e.target.closest('.btn-open-voucher');
            if (!isInside && !isOpenBtn) {
                closeVoucherPopup();
            }
        }
    });

    // ============================================
    // ADDRESS FUNCTIONS
    // ============================================

    var defaultAddress = @json($defaultAddress);
    var savedAddresses = @json($addresses);
    var isLoggedIn = @json(Auth::guard('customer')->check());
    var pendingSavedAddress = null;

    var CACHE_KEY_PROVINCES = 'checkout_provinces_v2';
    var CACHE_KEY_CITIES = 'checkout_cities_v2_';
    var CACHE_KEY_DISTRICTS = 'checkout_districts_v2_';
    var CACHE_KEY_VILLAGES = 'checkout_villages_v2_';

    function getCache(key) {
        try {
            var data = localStorage.getItem(key);
            if (data) {
                var parsed = JSON.parse(data);
                if (parsed.expiry && parsed.expiry > Date.now()) {
                    return parsed.data;
                }
                localStorage.removeItem(key);
            }
        } catch(e) {}
        return null;
    }

    function setCache(key, data, ttl) {
        ttl = ttl || 3600000;
        try {
            localStorage.setItem(key, JSON.stringify({
                data: data,
                expiry: Date.now() + ttl
            }));
        } catch(e) {}
    }

    function loadProvinces() {
        var cached = getCache(CACHE_KEY_PROVINCES);

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
                var provinces = Array.isArray(response) ? response : [];
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
        var options = '<option value="">-- Pilih Provinsi --</option>';
        if (data && Array.isArray(data) && data.length > 0) {
            $.each(data, function(index, province) {
                var code = province.code;
                var name = province.name;
                if (code && name) {
                    options += '<option value="' + name + '" data-code="' + code + '">' + name + '</option>';
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
        if (!address) return;

        $('#shipping_name').val(address.recipient_name || '');
        $('#shipping_phone').val(address.recipient_phone || '');
        $('#shipping_address').val(address.address || '');
        $('#shipping_postal_code').val(address.postal_code || '0');

        if (address.province) {
            $('#province').val(address.province);
            var provinceCode = $('#province').find(':selected').data('code');

            if (provinceCode) {
                loadCities(provinceCode, address.city || null, address.district || null, address.subdistrict || null);
            }
        }
    }

    function loadCities(provinceCode, selectedCity, selectedDistrict, selectedSubdistrict) {
        var cacheKey = CACHE_KEY_CITIES + provinceCode;
        var cached = getCache(cacheKey);

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
                var cities = Array.isArray(response) ? response : [];
                setCache(cacheKey, cities);
                renderCities(cities, selectedCity, selectedDistrict, selectedSubdistrict);
            },
            error: function() {
                $('#city').html('<option value="">-- Gagal memuat --</option>').prop('disabled', true);
            }
        });
    }

    function renderCities(data, selectedCity, selectedDistrict, selectedSubdistrict) {
        var options = '<option value="">-- Pilih Kota --</option>';
        if (data && data.length > 0) {
            $.each(data, function(index, city) {
                var code = city.code;
                var name = city.name;
                if (code && name) {
                    options += '<option value="' + name + '" data-code="' + code + '">' + name + '</option>';
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

    function loadDistricts(cityCode, selectedDistrict, selectedSubdistrict) {
        var cacheKey = CACHE_KEY_DISTRICTS + cityCode;
        var cached = getCache(cacheKey);

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
                var districts = Array.isArray(response) ? response : [];
                setCache(cacheKey, districts);
                renderDistricts(districts, selectedDistrict, selectedSubdistrict);
            },
            error: function() {
                $('#district').html('<option value="">-- Gagal memuat --</option>').prop('disabled', true);
            }
        });
    }

    function renderDistricts(data, selectedDistrict, selectedSubdistrict) {
        var options = '<option value="">-- Pilih Kecamatan --</option>';
        if (data && data.length > 0) {
            $.each(data, function(index, district) {
                var code = district.code;
                var name = district.name;
                if (code && name) {
                    options += '<option value="' + name + '" data-code="' + code + '">' + name + '</option>';
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

    function loadVillages(districtCode, selectedVillage) {
        var cacheKey = CACHE_KEY_VILLAGES + districtCode;
        var cached = getCache(cacheKey);

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
                var villages = Array.isArray(response) ? response : [];

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
                console.error('Error loading villages:', { status: status, error: error, response: xhr.responseText });
                $('#subdistrict').html('<option value="">-- Gagal memuat --</option>').prop('disabled', false);
            }
        });
    }

    function renderVillages(data, selectedVillage) {
        var options = '<option value="">-- Pilih Kelurahan --</option>';

        if (data && data.length > 0) {
            $.each(data, function(index, village) {
                var code = village.code;
                var name = village.name;
                var postalCode = village.postal_code || '';

                if (code && name) {
                    options += '<option value="' + name + '" data-code="' + code + '" data-zip="' + postalCode + '">' + name + (postalCode ? ' (' + postalCode + ')' : '') + '</option>';
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
        var $display = $('#shipping-cost-display');
        var $courier = $('#courier');
        var $service = $('#service');

        $display.html('<span class="loading">⏳ Mencari ongkir...</span>');
        $courier.prop('disabled', true).html('<option value="">-- Memuat kurir --</option>');
        $service.prop('disabled', true).html('<option value="">-- Pilih Layanan --</option>');

        if (!destinationPostalCode || destinationPostalCode === '0' || destinationPostalCode === '') {
            $display.html('<span class="error">❌ Kode pos tujuan tidak tersedia</span>');
            resetShippingPicker('Lengkapi alamat tujuan terlebih dahulu');
            return;
        }

        var originPostalCode = $('#origin_postal_code').val() || '{{ config("services.biteship.origin_postal_code", "46191") }}';

        // 🔥 ITEMS DINAMIS DARI DOM (qty & berat terkini)
        var items = [];
        $('.summary-item').each(function() {
            var qty = parseInt($(this).find('.qty-input').val()) || 1;
            items.push({
                name: $(this).find('.item-name').text().trim() || 'Product',
                weight: parseInt($(this).data('weight')) || 1000,
                quantity: qty,
                price: parseInt($(this).data('price')) || 0
            });
        });

        if (items.length === 0) {
            $display.html('<span class="error">❌ Tidak ada produk di keranjang</span>');
            return;
        }

        var allCouriers = ['jne', 'jnt', 'sicepat', 'pos', 'anteraja', 'lion', 'ninja', 'rpx', 'pahala', 'wahana', 'tiki', 'ncs', 'first', 'idexpress', 'star'];

        var payload = {
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
                    $display.html('<span class="error">❌ ' + (response.message || 'Gagal mendapatkan ongkir') + '</span>');
                    resetShippingPicker('Gagal mendapatkan layanan pengiriman');
                    return;
                }

                var couriers = response.data || [];

                if (!couriers.length) {
                    $display.html('<span class="error">❌ Tidak ada layanan pengiriman untuk rute ini</span>');
                    resetShippingPicker('Tidak ada kurir tersedia');
                    return;
                }

                var availableCouriers = couriers.filter(function(courier) {
                    return courier.services && courier.services.length > 0;
                });

                if (!availableCouriers.length) {
                    $display.html('<span class="error">❌ Tidak ada layanan pengiriman tersedia</span>');
                    resetShippingPicker('Tidak ada layanan tersedia');
                    return;
                }

                var courierMap = {};
                availableCouriers.forEach(function(courier) {
                    courierMap[courier.code] = courier;
                });

                var courierOptions = '<option value="">-- Pilih Kurir --</option>';
                availableCouriers.forEach(function(courier) {
                    courierOptions += '<option value="' + escapeHtml(courier.code) + '">' + escapeHtml(courier.name) + '</option>';
                });

                $courier.html(courierOptions).prop('disabled', false).data('shipping-rates', courierMap);
                renderShippingOptions(availableCouriers);
                $service.prop('disabled', true).html('<option value="">-- Pilih Layanan --</option>');

                // 🔥 AUTO RE-APPLY: TERAPKAN KEMBALI KURIR/LAYANAN SEBELUMNYA (ONGKIR BARU)
                if (window.pendingReapplySelection) {
                    var prev = window.pendingReapplySelection;
                    window.pendingReapplySelection = null;

                    var $match = $('.shipping-option').filter(function() {
                        return String($(this).data('courier')) === String(prev.courier) &&
                               String($(this).data('service-code')) === String(prev.service);
                    }).first();

                    if ($match.length) {
                        window.shippingSelection = {
                            courier: String($match.data('courier')),
                            service: String($match.data('service-code')),
                            display: $match.find('.shipping-option-name').text().trim(),
                            cost: parseInt($match.data('cost')) || 0,
                            etd: String($match.data('etd') || '-'),
                            courierName: prev.courierName
                        };
                        updateShippingUI(window.shippingSelection);
                        showToast('Ongkir diperbarui sesuai berat terbaru.', 'success');
                    } else {
                        showToast('Layanan sebelumnya tidak tersedia — silakan pilih ulang kurir.', 'warning');
                    }
                }

                var totalServices = 0;
                availableCouriers.forEach(function(courier) {
                    totalServices += courier.services.length;
                });

                $display.html('<span class="success">✅ ' + availableCouriers.length + ' kurir tersedia (' + totalServices + ' layanan)</span>');
            },
            error: function(xhr) {
                console.error('Biteship AJAX Error:', xhr.responseText);
                var message = 'Gagal mendapatkan ongkir';
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.message) message += ': ' + response.message;
                } catch(e) {
                    console.error('Gagal membaca response Biteship', e);
                }
                $courier.prop('disabled', true).html('<option value="">-- Gagal memuat kurir --</option>');
                $service.prop('disabled', true).html('<option value="">-- Pilih Layanan --</option>');
                $display.html('<span class="error">❌ ' + escapeHtml(message) + '</span>');
                resetShippingPicker(message);
            }
        });
    }

    // ============================================
    // ADDRESS EVENT HANDLERS
    // ============================================

    $('#province').on('change', function() {
        var provinceCode = $(this).find(':selected').data('code');
        $('#province_id').val(provinceCode);
        $('#city_id').val('');
        $('#district_id').val('');
        $('#subdistrict_id').val('');
        $('#shipping_cost').val(0);
        $('#shipping-cost-text').text('Rp 0');
        window.shippingSelection = null;
        window.hasCourierSelected = false;
        updateTotal();

        if (provinceCode) {
            var selectedCity = pendingSavedAddress?.city || defaultAddress?.city || null;
            var selectedDistrict = pendingSavedAddress?.district || defaultAddress?.district || null;
            var selectedSubdistrict = pendingSavedAddress?.subdistrict || defaultAddress?.subdistrict || null;
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

    $('#city').on('change', function() {
        var cityCode = $(this).find(':selected').data('code');
        var selectedDistrict = $(this).data('selected-district') || pendingSavedAddress?.district || defaultAddress?.district || null;
        var selectedSubdistrict = $(this).data('selected-subdistrict') || pendingSavedAddress?.subdistrict || defaultAddress?.subdistrict || null;

        $('#city_id').val(cityCode);
        $('#district_id').val('');
        $('#subdistrict_id').val('');
        $('#shipping_cost').val(0);
        $('#shipping-cost-text').text('Rp 0');
        window.shippingSelection = null;
        window.hasCourierSelected = false;
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

    $('#district').on('change', function() {
        var districtCode = $(this).find(':selected').data('code');
        var selectedSubdistrict = $(this).data('selected-subdistrict') || pendingSavedAddress?.subdistrict || defaultAddress?.subdistrict || null;

        $('#district_id').val(districtCode);
        $('#subdistrict_id').val('');
        $('#shipping_cost').val(0);
        $('#shipping-cost-text').text('Rp 0');
        window.shippingSelection = null;
        window.hasCourierSelected = false;
        updateTotal();

        $('#subdistrict').html('<option value="">-- Pilih Kelurahan --</option>').prop('disabled', true);

        if (districtCode) {
            loadVillages(districtCode, selectedSubdistrict);
        }

        $('#courier').prop('disabled', true);
        $('#service').html('<option value="">-- Pilih Layanan --</option>');
        $('#shipping-cost-display').text('Pilih kurir dan kelurahan tujuan');
    });

    $('#subdistrict').on('change', function() {
        var selectedOption = $(this).find(':selected');
        var villageCode = selectedOption.attr('data-code') || '';
        var zipCode = selectedOption.attr('data-zip') || '';

        console.log('=== SUBDISTRICT SELECTED ===');
        console.log('Village Code:', villageCode);
        console.log('Postal Code:', zipCode);

        $('#subdistrict_id').val(villageCode);
        $('#shipping_postal_code').val(zipCode);
        $('#shipping_cost').val(0);
        $('#shipping-cost-text').text('Rp 0');
        window.shippingSelection = null;
        window.hasCourierSelected = false;
        updateTotal();

        $('#service').prop('disabled', true).html('<option value="">-- Pilih Layanan --</option>');
        window.shippingSelection = null;
        window.hasCourierSelected = false;

        if (!zipCode || zipCode === '0' || zipCode === '' || zipCode.length < 4) {
            $('#courier').prop('disabled', true).html('<option value="">-- Kode pos tidak valid --</option>');
            $('#shipping-cost-display').html('<span class="error">❌ Kode pos tujuan tidak valid</span>');
            return;
        }

        $('#courier').prop('disabled', true).html('<option value="">⏳ Memuat Kurir...</option>');
        $('#shipping-cost-display').html('<span class="loading">⏳ Mencari layanan pengiriman...</span>');

        var weight = calculateTotalWeight();

        if (!weight || weight <= 0) {
            $('#courier').prop('disabled', true).html('<option value="">-- Kurir Tidak Tersedia --</option>');
            $('#shipping-cost-display').html('<span class="error">❌ Berat produk belum tersedia</span>');
            return;
        }

        console.log('=== REQUEST ONGKIR BITESHIP ===');
        console.log('Origin Postal Code:', '{{ config('services.biteship.origin.postal_code', '46196') }}');
        console.log('Destination Postal Code:', zipCode);
        console.log('Weight:', weight);

        checkShippingCost(zipCode);
    });

    $('#saved_address').on('change', function() {
        var addressId = String($(this).val());
        var address = savedAddresses.find(function(item) {
            return String(item.id) === addressId;
        });

        if (address) {
            pendingSavedAddress = address;
            fillSavedAddress(address);
        }
    });

    // ============================================
    // COURIER & SERVICE HANDLERS
    // ============================================

    $('#courier').on('change', function() {
        var selectedCourier = $(this).val();

        console.log('=== COURIER SELECTED ===');
        console.log('Courier:', selectedCourier);

        $('#service').prop('disabled', true).html('<option value="">-- Memuat Layanan --</option>');
        $('#shipping_cost').val(0);
        $('#shipping-cost-text').text('Rp 0');
        window.shippingSelection = null;
        window.hasCourierSelected = false;
        updateTotal();

        if (!selectedCourier) {
            $('#service').html('<option value="">-- Pilih Layanan --</option>').prop('disabled', true);
            $('#shipping-cost-display').html('Pilih kurir');
            return;
        }

        var couriers = $('#courier').data('shipping-rates') || {};
        var selectedCourierData = couriers[selectedCourier];

        if (!selectedCourierData || !selectedCourierData.services || !selectedCourierData.services.length) {
            $('#service').html('<option value="">-- Layanan Tidak Tersedia --</option>').prop('disabled', true);
            $('#shipping-cost-display').html('<span class="error">❌ Layanan kurir tidak tersedia</span>');
            return;
        }

        var serviceOptions = '<option value="">-- Pilih Layanan --</option>';
        $.each(selectedCourierData.services, function(index, service) {
            var serviceName = service.service || '';
            var description = service.description || '';
            var cost = parseInt(service.cost, 10) || 0;
            var etd = service.etd || '-';

            serviceOptions += `
                <option value="${serviceName}" data-cost="${cost}" data-etd="${etd}">
                    ${serviceName} ${description ? '- ' + description : ''} - Rp ${formatNumber(cost)} (${etd} hari)
                </option>
            `;
        });

        $('#service').html(serviceOptions).prop('disabled', false);
        $('#shipping-cost-display').html('<span class="success">✅ ' + selectedCourierData.services.length + ' layanan tersedia</span>');
    });

    $('#service').on('change', function() {
    var selected = $(this).find(':selected');
    var cost = parseInt(selected.data('cost')) || 0;
    var etd = selected.data('etd') || '-';
    var serviceName = selected.val();
    var courierName = $('#courier option:selected').text();
    var courierCode = $('#courier').val();

    console.log('=== SERVICE SELECTED ===');
    console.log('Service:', serviceName);
    console.log('Cost:', cost);
    console.log('ETD:', etd);
    console.log('Courier:', courierName);

    if (!serviceName || cost <= 0) {
        $('#shipping_cost').val(0);
        $('#shipping-cost-text').text('Rp 0');
        window.shippingSelection = null;
        window.hasCourierSelected = false;
        updateTotal();
        return;
    }

    // 🔥 UPDATE UI
    $('#shipping_cost').val(cost);
    $('#shipping-cost-text').text('Rp ' + formatNumber(cost));

    var costDisplay = 'Rp ' + formatNumber(cost);
    $('#shipping-cost-display').html(`
        <div class="success">
            <div style="font-weight:600;">${escapeHtml(courierName)} · ${escapeHtml(serviceName)}</div>
            <div style="font-size:0.65vw;color:#94a3b8;">Estimasi: ${escapeHtml(etd)} hari</div>
            <div style="font-size:0.8vw;color:#0f172a;font-weight:700;margin-top:0.2vw;">${costDisplay}</div>
        </div>
    `);

    $('#shipping-picker-button').prop('disabled', false).html(
        '<span><span class="picker-title">' + escapeHtml(courierName) + ' · ' + escapeHtml(serviceName) + '</span>' +
        '<span class="picker-subtitle">Estimasi ' + escapeHtml(etd) + ' . ' + costDisplay + '</span></span><span class="picker-arrow">›</span>'
    );

    window.hasCourierSelected = true;
    window.shippingCost = cost;

    // 🔥 SINKRONKAN SINGLE SOURCE OF TRUTH
    window.shippingSelection = {
        courier: String(courierCode || ''),
        service: String(serviceName || ''),
        courierName: String(courierName || courierCode || '').toUpperCase(),
        display: String(serviceName || ''),
        cost: cost,
        etd: String(etd || '-')
    };

    // 🔥 UPDATE TOTAL LANGSUNG
    updateTotalAfterShipping(cost);

    // 🔥 SIMPAN KE SESSION
    $.ajax({
        url: '/checkout/update-shipping',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        data: JSON.stringify({
            shipping_cost: cost,
            courier: courierCode,
            service: serviceName
        }),
        success: function(response) {
            console.log('✅ Shipping cost saved to session:', response);
            window.hasCourierSelected = true;
            $('#shipping-warning').addClass('hidden');
            $('#shipping-error').addClass('hidden');
        },
        error: function() {
            console.warn('⚠️ Failed to save shipping cost to session');
            // TETAP UPDATE UI
            window.hasCourierSelected = true;
            $('#shipping-warning').addClass('hidden');
            $('#shipping-error').addClass('hidden');
        }
    });
});

    // ============================================
    // UPDATE TOTAL FUNCTION
    // ============================================

    function updateTotal() {
        var subtotalText = $('#subtotal-display').text().replace(/[^0-9]/g, '');
        var subtotal = parseInt(subtotalText) || 0;

        var shippingCost = parseInt($('#shipping_cost').val()) || 0;

        // 🔥 AMBIL VOUCHER DISCOUNT DARI SESSION HIDDEN INPUT
        var voucherDiscount = parseInt($('#applied-voucher-discount').val()) || 0;

        // 🔥 JIKA TIDAK ADA DI HIDDEN, AMBIL DARI DISPLAY
        if (voucherDiscount === 0) {
            var displayDiscount = $('#voucher-discount-text').text().replace(/[^0-9]/g, '');
            voucherDiscount = parseInt(displayDiscount) || 0;
        }

        // 🔥 GRATIS ONGKIR: JANGAN kurangi voucher discount lagi (hindari total minus)
        if (window.freeShippingApplied) {
            voucherDiscount = 0;
        }

        var total = subtotal + shippingCost - voucherDiscount;
        if (total < 0) total = 0;

        $('#total-display').text('Rp ' + formatNumber(total));

        console.log('🔄 Total updated:', {
            subtotal: subtotal,
            shippingCost: shippingCost,
            voucherDiscount: voucherDiscount,
            total: total
        });
    }

    // ============================================
    // CALCULATE TOTAL WEIGHT
    // ============================================

    function calculateTotalWeight() {
        // 🔥 HITUNG DARI DOM AGAR MENGIKUTI PERUBAHAN QTY
        var totalWeight = 0;
        $('.summary-item').each(function() {
            var weight = parseInt($(this).data('weight')) || 1000;
            var qty = parseInt($(this).find('.qty-input').val()) || 1;
            totalWeight += weight * qty;
        });
        $('#total-weight').text(totalWeight);
        return totalWeight;
    }

    // ============================================
    // GUEST DATA FUNCTIONS
    // ============================================

    function saveCheckoutData() {
        var data = {
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

    function restoreGuestData() {
        if (isLoggedIn) return;

        try {
            var saved = localStorage.getItem('checkout_data');
            if (saved) {
                var data = JSON.parse(saved);
                var savedDate = new Date(data.saved_at);
                var now = new Date();
                var diffDays = (now - savedDate) / (1000 * 60 * 60 * 24);

                if (diffDays < 7) {
                    $('#shipping_name').val(data.shipping_name || '');
                    $('#shipping_phone').val(data.shipping_phone || '');
                    $('#shipping_address').val(data.shipping_address || '');

                    if (data.shipping_province) {
                        setTimeout(function() {
                            $('#province').val(data.shipping_province).trigger('change');
                        }, 1500);
                    }
                }
            }
        } catch(e) {}
    }

    $(document).on('change', '#shipping_name, #shipping_phone, #shipping_address, #province, #city, #district, #subdistrict', function() {
        if (!isLoggedIn) {
            saveCheckoutData();
        }
    });

    // ============================================
    // MOBILE SUMMARY TOGGLE
    // ============================================

    var isMobile = window.innerWidth <= 768;
    var summary = document.querySelector('.checkout-summary');
    var summaryTitle = document.querySelector('.checkout-summary .summary-title');
    var isSummaryOpen = false;

    if (isMobile && summary) {
        summaryTitle?.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleSummary();
        });

        var handle = summary.querySelector('::before');
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

    setTimeout(function() {
        if (isMobile && summary) {
            isSummaryOpen = true;
            summary.classList.add('active');
        }
    }, 600);

    // ============================================
    // INIT
    // ============================================

    // Load provinces & auto fill
    if ($('#saved_address').val()) {
        var selectedAddress = savedAddresses.find(function(item) {
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

        var attempts = 0;
        var maxAttempts = 10;
        var autoFillInterval = setInterval(function() {
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

    // Restore guest data
    if (!isLoggedIn) {
        setTimeout(restoreGuestData, 2000);
    }

    // Calculate weight
    calculateTotalWeight();
    $('#courier').prop('disabled', true);

    console.log('✅ Checkout page initialized successfully!');
});
    </script>
@endsection
