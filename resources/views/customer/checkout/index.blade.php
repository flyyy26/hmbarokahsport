@extends('layouts.customer')

@section('title', 'Checkout - Barokah Sport')

@section('content')

<style>
/* ============================================
   CHECKOUT PAGE STYLES
   ============================================ */
.checkout-container {
    max-width: 100%;
    margin: 0 auto;
    background: #f8fafc;
    padding: 2vw 8vw;
}

/* --------------------------------------------
   HEADER
   -------------------------------------------- */
.checkout-header {
    margin-bottom: 1.8vw;
    padding-bottom: 1vw;
    border-bottom: 0.1vw solid #f1f5f9;
}

.checkout-header h1 {
    font-size: 2.5vw;
    font-weight: 800;
    color: #0f172a;
    text-transform: uppercase;
    letter-spacing: 0.02em;
    display: inline-flex;
    align-items: center;
    font-family: heading, sans-serif;
    gap: 0.6vw;
}

.checkout-header h1 iconify-icon {
    color: #ecbc42;
    font-size: 2.2vw;
}

.checkout-header p {
    font-size: 0.85vw;
    color: #94a3b8;
    margin-top: 0.4vw;
    line-height: 1.5;
}

/* --------------------------------------------
   ALERT
   -------------------------------------------- */
.checkout-alert {
    display: flex;
    align-items: flex-start;
    gap: 0.7vw;
    padding: 1vw 1.3vw;
    border-radius: 0.7vw;
    margin-bottom: 1.2vw;
    font-size: 0.82vw;
    line-height: 1.6;
}

.checkout-alert.error {
    background: #fef2f2;
    border: 0.1vw solid #fecaca;
    color: #b91c1c;
}

.checkout-alert .alert-title {
    display: flex;
    align-items: center;
    gap: 0.4vw;
    font-weight: 700;
    margin-bottom: 0.3vw;
}

.checkout-alert .alert-list {
    margin-top: 0.4vw;
    padding-left: 1.2vw;
}

.checkout-alert .alert-list li {
    list-style: disc;
    margin-bottom: 0.2vw;
}

/* --------------------------------------------
   GRID (2 kolom: form + summary)
   -------------------------------------------- */
.checkout-grid {
    display: grid;
    grid-template-columns: 1.2fr 1fr;
    gap: 1.5vw;
    align-items: start;
}

.checkout-grid > form,
.checkout-grid > .checkout-summary {
    display: flex;
    flex-direction: column;
    min-width: 0;
    width: 100%;
    box-sizing: border-box;
}

/* --------------------------------------------
   SECTION (Card)
   -------------------------------------------- */
.checkout-section {
    background: #ffffff;
    border: 0.1vw solid #e2e8f0;
    border-radius: 1vw;
    padding: 1.5vw;
    margin-bottom: 1.2vw;
    transition: border-color 0.2s ease;
}

.checkout-section:hover {
    border-color: #fde68a;
}

.checkout-section:last-child {
    margin-bottom: 0;
}

.checkout-section .section-title {
    display: flex;
    align-items: center;
    gap: 0.5vw;
    font-size: 1.05vw;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 0.3vw;
}

.checkout-section .section-title iconify-icon {
    color: #ecbc42;
    font-size: 1.2vw;
}

.checkout-section .section-subtitle {
    font-size: 0.78vw;
    color: #94a3b8;
    margin-bottom: 1vw;
    line-height: 1.5;
}

/* --------------------------------------------
   FORM
   -------------------------------------------- */
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1vw;
}

.form-grid .full-width {
    grid-column: 1 / -1;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.35vw;
}

.form-group label {
    display: flex;
    align-items: center;
    gap: 0.35vw;
    font-size: 0.78vw;
    font-weight: 600;
    color: #334155;
}

.form-group label iconify-icon {
    color: #ecbc42;
    font-size: 0.9vw;
}

.form-group label .required {
    color: #dc2626;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.75vw 1vw;
    border: 0.1vw solid #e2e8f0;
    border-radius: 0.6vw;
    font-size: 0.82vw;
    color: #0f172a;
    background: #ffffff;
    transition: all 0.2s ease;
    font-family: inherit;
    outline: none;
}

.form-group input::placeholder,
.form-group textarea::placeholder {
    color: #cbd5e1;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: #ecbc42;
    box-shadow: 0 0 0 0.2vw rgba(236, 188, 66, 0.15);
}

.form-group input:disabled,
.form-group select:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    background: #f8fafc;
}

.form-group textarea {
    resize: vertical;
    min-height: 4vw;
    line-height: 1.6;
}

.form-group .helper-text {
    display: flex;
    align-items: center;
    gap: 0.3vw;
    font-size: 0.68vw;
    color: #94a3b8;
    margin-top: 0.2vw;
}

.form-group .helper-text iconify-icon {
    font-size: 0.8vw;
}

.form-group .error-text {
    display: flex;
    align-items: center;
    gap: 0.3vw;
    font-size: 0.72vw;
    color: #dc2626;
    margin-top: 0.2vw;
    padding: 0.4vw 0.7vw;
    background: #fef2f2;
    border-radius: 0.4vw;
}

.form-group .error-text iconify-icon {
    font-size: 0.85vw;
    flex-shrink: 0;
}

/* --------------------------------------------
   SHIPPING COST DISPLAY
   -------------------------------------------- */
.shipping-cost-display {
    width: 100%;
    padding: 0.85vw 1vw;
    border: 0.1vw solid #e2e8f0;
    border-radius: 0.6vw;
    font-size: 0.82vw;
    color: #94a3b8;
    background: #f8fafc;
    min-height: 3vw;
    display: flex;
    align-items: center;
}

.shipping-cost-display .loading { color: #ecbc42; }
.shipping-cost-display .success { color: #059669; }
.shipping-cost-display .error   { color: #dc2626; }

/* --------------------------------------------
   SHIPPING PICKER BUTTON
   -------------------------------------------- */
.shipping-picker-button {
    width: 100%;
    min-height: 4vw;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1vw;
    padding: 0.9vw 1.1vw;
    border: 0.1vw solid #e2e8f0;
    border-radius: 0.7vw;
    background: #ffffff;
    color: #0f172a;
    text-align: left;
    font: inherit;
    font-family: inherit;
    cursor: pointer;
    transition: all 0.2s ease;
}

.shipping-picker-button:hover:not(:disabled) {
    border-color: #ecbc42;
    background: #fffbf0;
}

.shipping-picker-button:disabled {
    cursor: not-allowed;
    background: #f8fafc;
    color: #94a3b8;
}

.shipping-picker-button .picker-title {
    display: block;
    font-weight: 700;
    font-size: 0.82vw;
    color: #0f172a;
}

.shipping-picker-button .picker-subtitle {
    display: block;
    margin-top: 0.15vw;
    color: #64748b;
    font-size: 0.72vw;
}

.shipping-picker-button .picker-arrow {
    color: #ecbc42;
    font-size: 1.4vw;
    font-weight: 700;
    line-height: 1;
}

/* --------------------------------------------
   SHIPPING POPUP (Modal)
   -------------------------------------------- */
.shipping-popup {
    position: fixed;
    inset: 0;
    z-index: 10001;
    display: grid;
    place-items: center;
    padding: 1vw;
}

.shipping-popup.hidden { display: none; }

.shipping-popup-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.55);
    backdrop-filter: blur(0.3vw);
    -webkit-backdrop-filter: blur(0.3vw);
}

.shipping-popup-card {
    position: relative;
    width: min(100%, 35vw);
    max-height: 82vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    background: #ffffff;
    border-radius: 1vw;
    box-shadow: 0 1.5vw 4vw rgba(15, 23, 42, 0.3);
}

.shipping-popup-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.2vw 1.4vw;
    border-bottom: 0.1vw solid #f1f5f9;
    background: #fafbfc;
}

.shipping-popup-header h3 {
    display: flex;
    align-items: center;
    gap: 0.5vw;
    margin: 0;
    color: #0f172a;
    font-size: 1.05vw;
    font-weight: 800;
}

.shipping-popup-header h3 iconify-icon {
    color: #ecbc42;
    font-size: 1.2vw;
}

.shipping-popup-close {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2vw;
    height: 2vw;
    border-radius: 50%;
    border: 0.1vw solid #e2e8f0;
    background: #ffffff;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s ease;
    padding: 0;
}

.shipping-popup-close:hover {
    background: #fffbf0;
    border-color: #fde68a;
    color: rgb(102, 72, 9);
    transform: rotate(90deg);
}

.shipping-popup-close iconify-icon {
    font-size: 1.15vw;
}

.shipping-popup-body {
    overflow-y: auto;
    padding: 1.2vw 1.4vw;
}

.shipping-popup-body::-webkit-scrollbar { width: 0.3vw; }
.shipping-popup-body::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 100vw;
}
.shipping-popup-body::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 100vw;
}

.shipping-popup-note {
    display: flex;
    align-items: center;
    gap: 0.4vw;
    margin: 0 0 1vw;
    padding: 0.7vw 0.9vw;
    border-radius: 0.6vw;
    background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
    color: rgb(102, 72, 9);
    font-size: 0.78vw;
    font-weight: 600;
}

.shipping-courier-group + .shipping-courier-group {
    margin-top: 1.2vw;
}

.shipping-courier-name {
    margin: 0 0 0.5vw;
    color: #0f172a;
    font-size: 0.85vw;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    display: flex;
    align-items: center;
    gap: 0.35vw;
}

.shipping-courier-name::before {
    content: '';
    display: inline-block;
    width: 0.25vw;
    height: 0.9vw;
    background: linear-gradient(180deg, #FDDD57 0%, #ecbc42 100%);
    border-radius: 100vw;
}

.shipping-option {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.8vw;
    padding: 0.85vw 1vw;
    border: 0.1vw solid #e2e8f0;
    border-radius: 0.6vw;
    background: #ffffff;
    text-align: left;
    cursor: pointer;
    font: inherit;
    font-family: inherit;
    transition: all 0.2s ease;
}

.shipping-option + .shipping-option {
    margin-top: 0.5vw;
}

.shipping-option:hover,
.shipping-option.selected {
    border-color: #ecbc42;
    background: #fffbf0;
}

.shipping-option.selected {
    box-shadow: 0 0 0 0.15vw rgba(236, 188, 66, 0.3);
}

.shipping-option-name {
    display: block;
    color: #0f172a;
    font-size: 0.82vw;
    font-weight: 600;
}

.shipping-option-meta {
    display: flex;
    align-items: center;
    gap: 0.3vw;
    margin-top: 0.2vw;
    color: #64748b;
    font-size: 0.72vw;
}

.shipping-option-price {
    color: rgb(102, 72, 9);
    font-size: 0.9vw;
    font-weight: 800;
    white-space: nowrap;
}

.shipping-popup-footer {
    padding: 1vw 1.4vw 1.2vw;
    border-top: 0.1vw solid #f1f5f9;
    background: #fafbfc;
}

.shipping-popup-confirm {
    width: 100%;
    padding: 0.95vw 1vw;
    border: 0;
    border-radius: 0.7vw;
    background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
    color: rgb(102, 72, 9);
    font: inherit;
    font-family: inherit;
    font-weight: 800;
    font-size: 0.85vw;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 0.15vw 0.5vw rgba(236, 188, 66, 0.3);
}

.shipping-popup-confirm:hover:not(:disabled) {
    transform: translateY(-0.1vw);
    box-shadow: 0 0.35vw 1.2vw rgba(236, 188, 66, 0.5);
}

.shipping-popup-confirm:disabled {
    background: #e2e8f0;
    color: #94a3b8;
    box-shadow: none;
    cursor: not-allowed;
}

/* --------------------------------------------
   SUMMARY
   -------------------------------------------- */
.checkout-summary {
    background: #ffffff;
    border: 0.1vw solid #e2e8f0;
    border-radius: 1vw;
    padding: 1.5vw;
    position: sticky;
    top: 8vw;
    height: fit-content;
}

.checkout-summary .summary-title {
    display: flex;
    align-items: center;
    gap: 0.5vw;
    font-size: 1.05vw;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 1.2vw;
    padding-bottom: 0.9vw;
    border-bottom: 0.1vw dashed #e2e8f0;
}

.checkout-summary .summary-title iconify-icon {
    color: #ecbc42;
    font-size: 1.2vw;
}

.checkout-summary .summary-items {
    max-height: 24vw;
    overflow-y: auto;
    margin-bottom: 1vw;
    padding-right: 0.3vw;
}

.checkout-summary .summary-items::-webkit-scrollbar { width: 0.25vw; }
.checkout-summary .summary-items::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 100vw;
}
.checkout-summary .summary-items::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 100vw;
}

.checkout-summary .summary-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 0.8vw;
    padding: 0.7vw 0;
    border-bottom: 0.1vw solid #f1f5f9;
}

.checkout-summary .summary-item:last-child {
    border-bottom: none;
}

.summary-item-product {
    display: flex;
    gap: 0.8vw;
    flex: 1;
    min-width: 0;
}

.checkout-summary .summary-item .item-image {
    width: 4vw;
    height: 4vw;
    overflow: hidden;
    border-radius: 0.5vw;
    border: 0.1vw solid #e2e8f0;
    position: relative;
    flex-shrink: 0;
    background: #f8fafc;
}

.checkout-summary .summary-item .item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    position: absolute;
    top: 0;
    left: 0;
}

.checkout-summary .summary-item .item-info {
    flex: 1;
    min-width: 0;
}

.checkout-summary .summary-item .item-name {
    font-size: 0.82vw;
    font-weight: 600;
    color: #0f172a;
    line-height: 1.35;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.item-name-layout-mobile .item-name:nth-child(2){
    display: none;
}

.checkout-summary .summary-item .item-variant {
    font-size: 0.7vw;
    color: #94a3b8;
    margin-top: 0.15vw;
}

.summary-qty-layout {
    display: flex;
    align-items: center;
    gap: 0.5vw;
    margin-top: 0.5vw;
}

.summary-qty {
    display: flex;
    align-items: center;
    background: #ffffff;
    border: 0.1vw solid #e2e8f0;
    border-radius: 0.5vw;
    overflow: hidden;
}

.summary-qty button {
    width: 1.4vw;
    height: 1.4vw;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75vw;
    cursor: pointer;
    background: transparent;
    border: none;
    color: #475569;
    transition: all 0.15s ease;
    padding: 0;
}

.summary-qty button:hover:not(:disabled) {
    background: #fffbf0;
    color: rgb(102, 72, 9);
}

.summary-qty button:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.summary-qty input {
    width: 2vw;
    height: 1.4vw;
    font-size: 0.78vw;
    font-weight: 700;
    background: transparent;
    border: none;
    border-left: 0.1vw solid #f1f5f9;
    border-right: 0.1vw solid #f1f5f9;
    text-align: center;
    outline: none;
    font-family: inherit;
    -moz-appearance: textfield;
}

.summary-qty input::-webkit-outer-spin-button,
.summary-qty input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.summary-remove {
    display: inline-flex;
    align-items: center;
    gap: 0.25vw;
    background: transparent;
    border: none;
    cursor: pointer;
    font-size: 0.72vw;
    color: #dc2626;
    padding: 0.25vw 0.5vw;
    border-radius: 0.35vw;
    transition: all 0.2s ease;
    font-family: inherit;
}

.summary-remove:hover {
    background: #fef2f2;
}

.checkout-summary .summary-item .item-price {
    font-size: 0.85vw;
    font-weight: 800;
    color: #0f172a;
    white-space: nowrap;
    flex-shrink: 0;
    display: block;
}

/* Divider & Rows */
.checkout-summary .summary-divider {
    border-top: 0.1vw dashed #e2e8f0;
    margin: 0.8vw 0;
}

.checkout-summary .summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.82vw;
    padding: 0.35vw 0;
    gap: 1vw;
}

.checkout-summary .summary-row .row-label {
    color: #64748b;
}

.checkout-summary .summary-row .row-value {
    font-weight: 700;
    color: #0f172a;
    white-space: nowrap;
}

.checkout-summary .summary-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1vw;
    font-size: 1vw;
    font-weight: 800;
    color: #0f172a;
    padding-top: 0.8vw;
    border-top: 0.15vw dashed #ecbc42;
    margin-top: 0.5vw;
}

.checkout-summary .summary-total > span:last-child {
    color: rgb(102, 72, 9);
    font-size: 1.15vw;
}

/* Submit Button */
.checkout-summary .btn-submit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5vw;
    width: 100%;
    padding: 0.95vw 1.5vw;
    background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
    color: rgb(102, 72, 9);
    font-size: 0.88vw;
    font-weight: 800;
    text-align: center;
    border: none;
    border-radius: 0.7vw;
    cursor: pointer;
    transition: all 0.2s ease;
    margin-top: 1.2vw;
    font-family: inherit;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    box-shadow: 0 0.2vw 0.6vw rgba(236, 188, 66, 0.35);
}

.checkout-summary .btn-submit:hover:not(:disabled) {
    transform: translateY(-0.1vw);
    box-shadow: 0 0.4vw 1.2vw rgba(236, 188, 66, 0.5);
}

.checkout-summary .btn-submit:active:not(:disabled) {
    transform: translateY(0);
}

.checkout-summary .btn-submit:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

.checkout-summary .btn-back {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.35vw;
    width: 100%;
    text-align: center;
    font-size: 0.8vw;
    color: #94a3b8;
    text-decoration: none;
    margin-top: 0.8vw;
    padding: 0.5vw;
    transition: color 0.2s;
    border-radius: 0.5vw;
}

.checkout-summary .btn-back:hover {
    color: rgb(102, 72, 9);
    background: #fffbf0;
}

/* --------------------------------------------
   VOUCHER SECTION
   -------------------------------------------- */
.voucher-summary-section {
    border-top: 0.1vw dashed #e2e8f0;
    padding-top: 0.9vw;
    margin-top: 0.7vw;
}

.voucher-summary-section .voucher-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5vw;
    margin-bottom: 0.7vw;
}

.voucher-summary-section .voucher-header .voucher-label {
    display: flex;
    align-items: center;
    gap: 0.35vw;
    font-size: 0.82vw;
    font-weight: 700;
    color: #0f172a;
}

.voucher-summary-section .voucher-header .voucher-label iconify-icon {
    color: #ecbc42;
    font-size: 1vw;
}

.voucher-summary-section .voucher-header .btn-open-voucher {
    display: inline-flex;
    align-items: center;
    gap: 0.3vw;
    background: none;
    border: none;
    color: rgb(102, 72, 9);
    font-size: 0.75vw;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
    font-family: inherit;
    padding: 0.3vw 0.6vw;
    border-radius: 0.4vw;
}

.voucher-summary-section .voucher-header .btn-open-voucher:hover {
    background: #fffbf0;
    text-decoration: underline;
}

.applied-voucher-summary {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5vw;
    padding: 0.6vw 0.8vw;
    background: #ecfdf5;
    border: 0.1vw solid #a7f3d0;
    border-radius: 0.6vw;
    margin-top: 0.3vw;
    margin-bottom: 0.6vw;
}

.applied-voucher-summary .voucher-info {
    display: flex;
    flex-direction: column;
    gap: 0.15vw;
    min-width: 0;
    flex: 1;
}

.applied-voucher-summary .voucher-info .code {
    font-family: 'Courier New', monospace;
    font-weight: 800;
    font-size: 0.65vw;
    padding: 0.15vw 0.5vw;
    background: #ecbc42;
    color: rgb(102, 72, 9);
    border-radius: 0.25vw;
    letter-spacing: 0.05em;
    align-self: flex-start;
}

.applied-voucher-summary .voucher-info .name {
    font-size: 0.75vw;
    font-weight: 700;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.applied-voucher-summary .voucher-info .discount {
    font-weight: 700;
    font-size: 0.72vw;
    color: #059669;
}

.applied-voucher-summary .btn-remove-voucher-sm {
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 0.2vw 0.4vw;
    border-radius: 0.3vw;
    transition: all 0.2s;
    font-size: 0.9vw;
    line-height: 1;
    font-family: inherit;
}

.applied-voucher-summary .btn-remove-voucher-sm:hover {
    color: #dc2626;
    background: #fef2f2;
}

/* --------------------------------------------
   GUEST INFO
   -------------------------------------------- */
.guest-info {
    padding: 1vw 1.3vw;
    border-radius: 0.7vw;
    background: #fffbf0;
    border: 0.1vw solid #fde68a;
    font-size: 0.8vw;
    color: rgb(102, 72, 9);
    margin-top: 1vw;
    line-height: 1.6;
}

.guest-info .guest-title {
    display: flex;
    align-items: center;
    gap: 0.35vw;
    font-weight: 800;
    margin-bottom: 0.3vw;
}

.guest-info .guest-title iconify-icon {
    color: #ecbc42;
    font-size: 1vw;
}

.guest-info .guest-text {
    margin-top: 0.2vw;
}

.guest-info .guest-hint {
    font-size: 0.72vw;
    color: rgba(102, 72, 9, 0.75);
    margin-top: 0.3vw;
}

/* --------------------------------------------
   WEIGHT DISPLAY
   -------------------------------------------- */
.weight-display {
    display: flex;
    align-items: center;
    gap: 0.3vw;
    font-size: 0.78vw;
    color: #94a3b8;
    margin-top: 0.5vw;
    padding-top: 0.6vw;
    border-top: 0.1vw dashed #f1f5f9;
}

.weight-display .weight-value {
    font-weight: 700;
    color: #0f172a;
}

.weight-display .weight-items {
    font-size: 0.7vw;
    color: #94a3b8;
    margin-left: 0.5vw;
}

/* Warning Box */
.checkout-warning-summary {
    margin-top: 0.5vw;
}

.checkout-warning-summary .warning-box {
    display: flex;
    align-items: center;
    gap: 0.4vw;
    padding: 0.6vw 0.9vw;
    background: #fffbeb;
    border: 0.1vw solid #fde68a;
    border-radius: 0.5vw;
    color: #b45309;
    font-size: 0.72vw;
    font-weight: 600;
}

.checkout-warning-summary.hidden {
    display: none;
}

/* Empty Cart */
.empty-cart-message {
    text-align: center;
    padding: 3vw 2vw;
    background: #ffffff;
    border-radius: 1vw;
    border: 0.1vw solid #e2e8f0;
}

.empty-cart-message iconify-icon {
    font-size: 4vw;
    color: #ecbc42;
}

.empty-cart-message p {
    font-size: 1vw;
    color: #64748b;
    margin-top: 1vw;
}

.empty-cart-message a {
    display: inline-flex;
    align-items: center;
    gap: 0.4vw;
    font-size: 0.9vw;
    color: rgb(102, 72, 9);
    text-decoration: none;
    font-weight: 700;
    margin-top: 1vw;
    padding: 0.7vw 1.4vw;
    background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
    border-radius: 0.6vw;
    transition: all 0.2s ease;
}

.empty-cart-message a:hover {
    transform: translateY(-0.1vw);
    box-shadow: 0 0.3vw 1vw rgba(236, 188, 66, 0.4);
}

/* Hidden utility */
.hidden { display: none !important; }

.loading-opacity {
    opacity: 0.6;
    pointer-events: none;
}

/* ============================================
   ADDRESS CARD UI
   ============================================ */
.address-card-selected {
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 0.8vw;
    padding: 1.2vw 1.5vw;
    margin-bottom: 1vw;
}

.address-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.8vw;
}

.address-label-badge {
    font-size: 0.8vw;
    font-weight: 700;
    color: #0f172a;
    background: #fef3c7;
    padding: 0.3vw 0.8vw;
    border-radius: 0.4vw;
}

.badge {
    font-size: 0.65vw;
    padding: 0.2vw 0.6vw;
    border-radius: 0.3vw;
}

.badge-default {
    background: #fef3c7;
    color: rgb(102, 72, 9);
    font-weight: 700;
}

.address-card-body {
    font-size: 0.85vw;
    line-height: 1.6;
}

.address-card-name {
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 0.3vw;
}

.address-card-phone {
    color: #475569;
    margin-bottom: 0.3vw;
}

.address-card-text {
    color: #475569;
    margin-bottom: 0.2vw;
}

.address-card-location {
    color: #64748b;
    font-size: 0.8vw;
    margin-bottom: 0.2vw;
}

.address-card-region {
    color: #64748b;
    font-size: 0.8vw;
}

.address-form-toolbar {
    display: flex;
    gap: 1vw;
    align-items: center;
    margin-bottom: 1vw;
    flex-wrap: wrap;
}

.address-form-toolbar .address-link {
    color: rgb(102, 72, 9);
    text-decoration: none;
    font-weight: 600;
    font-size: 0.85vw;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.3vw;
}

.address-form-toolbar .address-link:hover {
    text-decoration: underline;
}

.btn-add-address {
    display: inline-block;
    padding: 0.8vw 1.5vw;
    background: #FDDD57;
    color: rgb(102, 72, 9);
    border: none;
    border-radius: 0.5vw;
    font-weight: 700;
    cursor: pointer;
    font-size: 0.85vw;
}

.btn-add-address:hover {
    background: #ecbc42;
}

.address-empty {
    text-align: center;
    padding: 2vw;
    color: #94a3b8;
}

/* --------------------------------------------
   ADDRESS SELECTOR MODAL
   -------------------------------------------- */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.55);
    backdrop-filter: blur(0.3vw);
    z-index: 2000;
    display: none;
    justify-content: center;
    align-items: center;
    padding: 2vw;
}

.modal-overlay.active {
    display: flex;
}

.modal-content {
    background: #fff;
    border-radius: 0.8vw;
    max-width: 40vw;
    width: 100%;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 1vw 4vw rgba(0, 0, 0, 0.2);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.2vw 1.5vw;
    border-bottom: 0.1vw solid #e2e8f0;
}

.modal-header h3 {
    font-size: 1.1vw;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5vw;
    cursor: pointer;
    color: #94a3b8;
}

.modal-body {
    padding: 1.2vw 1.5vw;
}

.address-option {
    margin-bottom: 0.8vw;
    border: 0.1vw solid #e2e8f0;
    border-radius: 0.5vw;
    padding: 0.8vw;
    cursor: pointer;
    transition: all 0.2s ease;
}

.address-option:hover {
    border-color: #FDDD57;
    background: #fffbf0;
}

.address-option-card {
    display: flex;
    align-items: flex-start;
    gap: 0.6vw;
    cursor: pointer;
    margin: 0;
}

.address-option-card input[type="radio"] {
    margin-top: 0.2vw;
    accent-color: #ecbc42;
}

.address-details {
    flex: 1;
}

.address-details .address-label {
    font-weight: 700;
    font-size: 0.85vw;
    color: #0f172a;
    margin-bottom: 0.3vw;
}

.address-details .address-name {
    font-weight: 600;
    font-size: 0.8vw;
    color: #0f172a;
}

.address-details .address-phone {
    font-size: 0.8vw;
    color: #475569;
}

.address-details .address-text {
    font-size: 0.8vw;
    color: #475569;
    margin-top: 0.2vw;
    white-space: normal;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 0.8vw;
    padding: 1vw 1.5vw;
    border-top: 0.1vw solid #e2e8f0;
}

.btn-secondary {
    padding: 0.6vw 1.2vw;
    border: 0.1vw solid #e2e8f0;
    border-radius: 0.4vw;
    background: #fff;
    font-weight: 600;
    cursor: pointer;
    font-size: 0.85vw;
}

.btn-primary {
    padding: 0.6vw 1.2vw;
    border: none;
    border-radius: 0.4vw;
    background: #FDDD57;
    color: rgb(102, 72, 9);
    font-weight: 700;
    cursor: pointer;
    font-size: 0.85vw;
}

.btn-primary:hover {
    background: #ecbc42;
}

/* ============================================
   RESPONSIVE — TABLET (≤ 1024px)
   ============================================ */
@media (max-width: 1024px) {
    .checkout-container {
        padding: 3vw 4vw;
    }

    .checkout-grid {
        grid-template-columns: 1fr;
        gap: 3vw;
    }

    .checkout-summary {
        position: static;
        top: auto;
    }

    /* Header */
    .checkout-header { margin-bottom: 3vw; padding-bottom: 2vw; border-bottom-width: 0.2vw; }
    .checkout-header h1 { font-size: 4vw; gap: 1.2vw; }
    .checkout-header h1 iconify-icon { font-size: 4.5vw; }
    .checkout-header p { font-size: 2vw; margin-top: 1vw; }

    /* Alert */
    .checkout-alert {
        gap: 1.5vw;
        padding: 2.3vw 3vw;
        border-radius: 1.7vw;
        margin-bottom: 3vw;
        font-size: 2vw;
        border-width: 0.2vw;
    }
    .checkout-alert .alert-title { gap: 1vw; margin-bottom: 0.7vw; }
    .checkout-alert .alert-title iconify-icon { font-size: 2.5vw; }
    .checkout-alert .alert-list { padding-left: 3.5vw; }
    .checkout-alert .alert-list li { margin-bottom: 0.5vw; }

    /* Section */
    .checkout-section {
        padding: 3vw;
        border-radius: 2vw;
        margin-bottom: 3vw;
        border-width: 0.2vw;
    }

    .checkout-section .section-title {
        gap: 1vw;
        font-size: 2.6vw;
        margin-bottom: 0.7vw;
    }
    .checkout-section .section-title iconify-icon { font-size: 3vw; }
    .checkout-section .section-subtitle { font-size: 1.9vw; margin-bottom: 2.5vw; }

    /* Form */
    .form-grid { gap: 2.5vw; }
    .form-group { gap: 1vw; }

    .form-group label {
        gap: 0.9vw;
        font-size: 1.9vw;
    }
    .form-group label iconify-icon { font-size: 2.3vw; }

    .form-group input,
    .form-group select,
    .form-group textarea {
        padding: 2.2vw 3vw;
        border-radius: 1.5vw;
        font-size: 2.1vw;
        border-width: 0.2vw;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        box-shadow: 0 0 0 0.6vw rgba(236, 188, 66, 0.15);
    }

    .form-group textarea { min-height: 14vw; }
    .form-group .helper-text {
        gap: 0.9vw;
        font-size: 1.7vw;
        margin-top: 0.5vw;
    }
    .form-group .helper-text iconify-icon { font-size: 2vw; }
    .form-group .error-text {
        gap: 0.9vw;
        font-size: 1.8vw;
        margin-top: 0.5vw;
        padding: 1vw 1.8vw;
        border-radius: 1.2vw;
    }
    .form-group .error-text iconify-icon { font-size: 2.2vw; }

    /* Shipping picker */
    .shipping-picker-button {
        min-height: 8vw;
        gap: 2vw;
        padding: 2.5vw 3vw;
        border-radius: 1.6vw;
        border-width: 0.2vw;
    }
    .shipping-picker-button .picker-title { font-size: 2.1vw; }
    .shipping-picker-button .picker-subtitle { font-size: 1.8vw; margin-top: 0.5vw; }
    .shipping-picker-button .picker-arrow { font-size: 3.5vw; }

    .shipping-cost-display {
        padding: 2.2vw 3vw;
        border-radius: 1.5vw;
        font-size: 2vw;
        min-height: 8vw;
        border-width: 0.2vw;
    }

    /* Shipping popup */
    .shipping-popup { padding: 3vw; }
    .shipping-popup-card {
        width: min(100%, 85vw);
        max-height: 88vh;
        border-radius: 3vw;
    }
    .shipping-popup-header { padding: 3vw 3.5vw; border-bottom-width: 0.2vw; }
    .shipping-popup-header h3 { gap: 1.2vw; font-size: 2.7vw; }
    .shipping-popup-header h3 iconify-icon { font-size: 3.2vw; }
    .shipping-popup-close {
        width: 5.5vw; height: 5.5vw; border-width: 0.2vw;
    }
    .shipping-popup-close iconify-icon { font-size: 3.2vw; }
    .shipping-popup-body { padding: 3vw 3.5vw; }
    .shipping-popup-note {
        gap: 1vw;
        margin-bottom: 2.5vw;
        padding: 2vw 2.5vw;
        border-radius: 1.5vw;
        font-size: 2vw;
    }
    .shipping-courier-group + .shipping-courier-group { margin-top: 3vw; }
    .shipping-courier-name {
        font-size: 2.2vw;
        gap: 1vw;
        margin-bottom: 1.5vw;
    }
    .shipping-courier-name::before { width: 0.6vw; height: 2.3vw; }
    .shipping-option {
        gap: 2vw;
        padding: 2.3vw 2.5vw;
        border-radius: 1.5vw;
        border-width: 0.2vw;
    }
    .shipping-option + .shipping-option { margin-top: 1.5vw; }
    .shipping-option.selected { box-shadow: 0 0 0 0.4vw rgba(236, 188, 66, 0.3); }
    .shipping-option-name { font-size: 2.1vw; }
    .shipping-option-meta { gap: 0.7vw; margin-top: 0.5vw; font-size: 1.8vw; }
    .shipping-option-price { font-size: 2.3vw; }
    .shipping-popup-footer { padding: 2.5vw 3.5vw 3vw; border-top-width: 0.2vw; }
    .shipping-popup-confirm {
        padding: 2.5vw;
        border-radius: 1.6vw;
        font-size: 2.2vw;
    }

    /* Summary */
    .checkout-summary {
        padding: 3vw;
        border-radius: 2vw;
        border-width: 0.2vw;
    }

    .checkout-summary .summary-title {
        gap: 1vw;
        font-size: 2.6vw;
        margin-bottom: 2.5vw;
        padding-bottom: 2vw;
        border-bottom-width: 0.2vw;
    }
    .checkout-summary .summary-title iconify-icon { font-size: 3vw; }

    .checkout-summary .summary-items {
        max-height: 50vw;
        margin-bottom: 2vw;
    }
    .checkout-summary .summary-items::-webkit-scrollbar { width: 0.5vw; }

    .checkout-summary .summary-item {
        gap: 1.7vw;
        padding: 1.7vw 0;
        border-bottom-width: 0.2vw;
    }

    .summary-item-product { gap: 1.7vw; }

    .checkout-summary .summary-item .item-image {
        width: 9vw; height: 9vw;
        border-radius: 1.5vw;
        border-width: 0.2vw;
    }

    .checkout-summary .summary-item .item-name { font-size: 2.1vw; }
    .checkout-summary .summary-item .item-variant { font-size: 1.8vw; margin-top: 0.4vw; }

    .summary-qty-layout { gap: 1.2vw; margin-top: 1.3vw; }
    .item-name-layout-mobile .item-name:nth-child(2){ display: none; }

    .summary-qty {
        border-radius: 1.2vw;
        border-width: 0.2vw;
    }
    .summary-qty button {
        width: 3.5vw; height: 3.5vw;
        font-size: 2vw;
    }
    .summary-qty input {
        width: 5vw; height: 3.5vw;
        font-size: 2vw;
        border-left-width: 0.2vw;
        border-right-width: 0.2vw;
    }

    .summary-remove {
        gap: 0.7vw;
        font-size: 1.8vw;
        padding: 0.7vw 1.2vw;
        border-radius: 1vw;
    }

    .checkout-summary .summary-item .item-price { font-size: 2.2vw; }

    .checkout-summary .summary-divider { margin: 2vw 0; border-top-width: 0.2vw; }
    .checkout-summary .summary-row {
        font-size: 2.1vw;
        padding: 1vw 0;
        gap: 2vw;
    }
    .checkout-summary .summary-total {
        gap: 2vw;
        font-size: 2.5vw;
        padding-top: 2.5vw;
        margin-top: 1.5vw;
        border-top-width: 0.3vw;
    }
    .checkout-summary .summary-total > span:last-child { font-size: 2.8vw; }

    .checkout-summary .btn-submit {
        gap: 1.2vw;
        padding: 2.5vw 3vw;
        border-radius: 1.7vw;
        font-size: 2.2vw;
        margin-top: 3vw;
    }

    .checkout-summary .btn-back {
        gap: 0.9vw;
        font-size: 2vw;
        margin-top: 2vw;
        padding: 1.5vw;
        border-radius: 1.3vw;
    }

    /* Voucher */
    .voucher-summary-section {
        padding-top: 2.5vw;
        margin-top: 2vw;
        border-top-width: 0.2vw;
    }
    .voucher-summary-section .voucher-header {
        gap: 1.5vw;
        margin-bottom: 2vw;
    }
    .voucher-summary-section .voucher-header .voucher-label {
        gap: 0.9vw;
        font-size: 2.1vw;
    }
    .voucher-summary-section .voucher-header .voucher-label iconify-icon { font-size: 2.5vw; }
    .voucher-summary-section .voucher-header .btn-open-voucher {
        gap: 0.7vw;
        font-size: 1.9vw;
        padding: 0.8vw 1.5vw;
        border-radius: 1vw;
    }

    .applied-voucher-summary {
        gap: 1.5vw;
        padding: 1.7vw 2.2vw;
        border-radius: 1.5vw;
        margin-bottom: 1.5vw;
        border-width: 0.2vw;
    }
    .applied-voucher-summary .voucher-info { gap: 0.5vw; }
    .applied-voucher-summary .voucher-info .code {
        font-size: 1.8vw;
        padding: 0.5vw 1.5vw;
        border-radius: 0.8vw;
    }
    .applied-voucher-summary .voucher-info .name { font-size: 2vw; }
    .applied-voucher-summary .voucher-info .discount { font-size: 1.9vw; }
    .applied-voucher-summary .btn-remove-voucher-sm {
        padding: 0.7vw 1.2vw;
        border-radius: 1vw;
        font-size: 2.3vw;
    }

    /* Guest info */
    .guest-info {
        padding: 2.3vw 3vw;
        border-radius: 1.7vw;
        margin-top: 3vw;
        font-size: 2vw;
        border-width: 0.2vw;
    }
    .guest-info .guest-title { gap: 1vw; margin-bottom: 1vw; }
    .guest-info .guest-title iconify-icon { font-size: 2.5vw; }
    .guest-info .guest-hint { font-size: 1.8vw; margin-top: 1vw; }

    /* Warning */
    .checkout-warning-summary .warning-box {
        gap: 1vw;
        padding: 1.5vw 2vw;
        border-radius: 1.3vw;
        font-size: 1.8vw;
    }

    /* Weight */
    .weight-display {
        gap: 0.7vw;
        font-size: 1.9vw;
        margin-top: 1.5vw;
        padding-top: 2vw;
    }
    .weight-display .weight-value { font-size: 2.2vw; }
    .weight-display .weight-items { font-size: 1.8vw; margin-left: 1vw; }

    /* Empty state */
    .empty-cart-message { padding: 6vw 3vw; border-radius: 2vw; }
    .empty-cart-message iconify-icon { font-size: 10vw; }
    .empty-cart-message p { font-size: 2.5vw; margin-top: 2.5vw; }
    .empty-cart-message a {
        gap: 1vw; font-size: 2.2vw; margin-top: 2.5vw;
        padding: 2vw 3.5vw; border-radius: 1.5vw;
    }

    /* Address */
    .modal-content { max-width: 80vw; }
    .modal-header h3 { font-size: 2.8vw; }
    .address-form-toolbar .address-link { font-size: 1.8vw; }
    .btn-add-address { font-size: 1.8vw; padding: 1.8vw 3vw; }
    .address-card-body { font-size: 1.8vw; line-height: 1.6; }
    .address-card-name { font-size: 1.8vw; }
    .address-details .address-label { font-size: 1.9vw; }
    .address-details .address-name { font-size: 1.7vw; }
    .address-details .address-text { font-size: 1.7vw; }
}

/* ============================================
   RESPONSIVE — MOBILE (≤ 480px)
   ============================================ */
@media (max-width: 480px) {
    .checkout-container {
        padding: 0;
        background: #f5f6f8;
    }

    /* Header */
    .checkout-header {
        padding: 5vw 5vw 4vw;
        background: #ffffff;
        margin-bottom: 0;
        border-bottom: 0.3vw solid #f1f5f9;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .checkout-header h1 {
        font-size: 8vw;
        gap: 2vw;
    }
    .checkout-header h1 iconify-icon { font-size: 9vw; }
    .checkout-header p { font-size: 3.2vw; margin-top: 1.5vw; }

    /* Alert */
    .checkout-alert {
        gap: 2.5vw;
        padding: 3.5vw 4.5vw;
        border-radius: 2.5vw;
        margin: 3vw 4vw;
        font-size: 3.2vw;
        border-width: 0.3vw;
        align-items: flex-start;
    }
    .checkout-alert .alert-title {
        gap: 1.5vw;
        margin-bottom: 1.5vw;
        font-size: 3.5vw;
    }
    .checkout-alert .alert-list {
        padding-left: 5vw;
        margin-top: 1.5vw;
    }
    .checkout-alert .alert-list li {
        margin-bottom: 1vw;
        font-size: 3vw;
    }

    /* Grid → Single column */
    .checkout-grid {
        grid-template-columns: 1fr;
        gap: 0;
    }

    /* Section */
    .checkout-section {
        background: #ffffff;
        border: none;
        border-radius: 0;
        padding: 5vw 4.5vw;
        margin-bottom: 2.5vw;
        box-shadow: 0 0.2vw 1vw rgba(0, 0, 0, 0.03);
    }
    .checkout-section:last-child { margin-bottom: 0; }

    .checkout-section .section-title {
        gap: 2vw;
        font-size: 4.5vw;
        margin-bottom: 1.5vw;
    }
    .checkout-section .section-title iconify-icon { font-size: 5.5vw; }
    .checkout-section .section-subtitle { font-size: 3.2vw; margin-bottom: 4.5vw; }

    /* Form */
    .form-grid {
        grid-template-columns: 1fr;
        gap: 4.5vw;
    }
    .form-grid .full-width { grid-column: 1; }

    .form-group { gap: 1.5vw; }

    .form-group label {
        gap: 1.2vw;
        font-size: 3.2vw;
    }
    .form-group label iconify-icon { font-size: 3.8vw; }
    .form-group label .required { font-size: 3.2vw; }

    .form-group input,
    .form-group select,
    .form-group textarea {
        padding: 3.2vw 3.5vw;
        border-radius: 2.5vw;
        font-size: 3.2vw;
        border-width: 0.3vw;
    }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        box-shadow: 0 0 0 0.9vw rgba(236, 188, 66, 0.15);
    }
    .form-group textarea { min-height: 26vw; line-height: 1.7; }

    .form-group .helper-text {
        gap: 1.2vw;
        font-size: 2.8vw;
        margin-top: 1.2vw;
    }
    .form-group .helper-text iconify-icon { font-size: 3.4vw; }
    .form-group .error-text {
        gap: 1.2vw;
        font-size: 2.8vw;
        margin-top: 1.2vw;
        padding: 2vw 2.5vw;
        border-radius: 2vw;
    }
    .form-group .error-text iconify-icon { font-size: 3.4vw; }

    /* Shipping picker */
    .shipping-picker-button {
        min-height: 16vw;
        gap: 3vw;
        padding: 3.5vw 4vw;
        border-radius: 2.5vw;
        border-width: 0.3vw;
    }
    .shipping-picker-button .picker-title { font-size: 3.4vw; }
    .shipping-picker-button .picker-subtitle { font-size: 2.8vw; margin-top: 0.8vw; }
    .shipping-picker-button .picker-arrow { font-size: 6vw; }

    .shipping-cost-display {
        padding: 3.2vw 4vw;
        border-radius: 2.5vw;
        font-size: 3.2vw;
        min-height: 14vw;
        border-width: 0.3vw;
    }

    /* Shipping popup — Bottom sheet */
    .shipping-popup {
        padding: 0;
        align-items: flex-end;
    }
    .shipping-popup-card {
        width: 100%;
        max-height: 90vh;
        border-radius: 4vw 4vw 0 0;
        animation: slideUp 0.3s ease-out;
    }
    @keyframes slideUp {
        from { transform: translateY(100%); opacity: 0; }
        to   { transform: translateY(0); opacity: 1; }
    }

    .shipping-popup-header {
        padding: 4vw 5vw 3.5vw;
        border-bottom-width: 0.3vw;
    }
    .shipping-popup-header h3 {
        gap: 2vw;
        font-size: 4.5vw;
    }
    .shipping-popup-header h3 iconify-icon { font-size: 5.5vw; }
    .shipping-popup-close {
        width: 9vw; height: 9vw; border-width: 0.3vw;
    }
    .shipping-popup-close iconify-icon { font-size: 5vw; }

    .shipping-popup-body { padding: 4vw 5vw; }
    .shipping-popup-note {
        gap: 1.5vw;
        margin-bottom: 4vw;
        padding: 3vw 3.5vw;
        border-radius: 2.5vw;
        font-size: 3.2vw;
        line-height: 1.5;
    }
    .shipping-courier-group + .shipping-courier-group { margin-top: 5vw; }
    .shipping-courier-name {
        font-size: 4vw;
        gap: 1.5vw;
        margin-bottom: 3vw;
    }
    .shipping-courier-name::before { width: 1vw; height: 4vw; }
    .shipping-option {
        gap: 3vw;
        padding: 3.5vw 4vw;
        border-radius: 2.5vw;
        border-width: 0.3vw;
    }
    .shipping-option + .shipping-option { margin-top: 2.5vw; }
    .shipping-option.selected { box-shadow: 0 0 0 0.7vw rgba(236, 188, 66, 0.3); }
    .shipping-option-name { font-size: 3.4vw; }
    .shipping-option-meta { gap: 1.2vw; margin-top: 1vw; font-size: 3vw; }
    .shipping-option-price { font-size: 3.8vw; }
    .shipping-popup-footer {
        padding: 4vw 5vw 5vw;
        border-top-width: 0.3vw;
    }
    .shipping-popup-confirm {
        padding: 4.5vw;
        border-radius: 3vw;
        font-size: 3.8vw;
        letter-spacing: 0.1em;
    }

    /* Summary */
    .checkout-summary {
        background: #ffffff;
        border: none;
        border-radius: 0;
        padding: 5vw 4.5vw;
        margin-top: 2.5vw;
    }

    .checkout-summary .summary-title {
        gap: 2vw;
        font-size: 5vw;
        margin-bottom: 4.5vw;
        padding-bottom: 3vw;
        border-bottom-width: 0.3vw;
    }
    .checkout-summary .summary-title iconify-icon { font-size: 5.5vw; }

    .checkout-summary .summary-items {
        max-height: none;
        overflow: visible;
        margin-bottom: 3vw;
    }

    .checkout-summary .summary-item {
        gap: 3vw;
        padding: 4vw 0;
        border-bottom-width: 0.3vw;
        flex-wrap: wrap;
    }
    .summary-item-product {
        gap: 3vw;
        width: 100%;
    }

    .checkout-summary .summary-item .item-image {
        width: 18vw; height: 18vw;
        border-radius: 2.5vw;
        border-width: 0.3vw;
    }

    .checkout-summary .summary-item .item-name {
        font-size: 3.4vw;
        -webkit-line-clamp: 2;
        width: 40vw;
    }
    .item-name-layout-mobile{
        display: flex;
        justify-content: space-between;
    }
    .item-name-layout-mobile .item-name:nth-child(2){
        display: block;
        width: max-content;
    }
    .checkout-summary .summary-item .item-variant {
        font-size: 2.8vw;
        margin-top: 1vw;
    }

    .summary-qty-layout {
        gap: 2.5vw;
        margin-top: 2.5vw;
        flex-wrap: wrap;
    }

    .summary-qty {
        border-radius: 2.5vw;
        border-width: 0.3vw;
    }
    .summary-qty button {
        width: 8vw; height: 8vw;
        font-size: 4.5vw;
    }
    .summary-qty input {
        width: 12vw; height: 8vw;
        font-size: 3.8vw;
        border-left-width: 0.3vw;
        border-right-width: 0.3vw;
    }

    .summary-remove {
        gap: 1.2vw;
        font-size: 3vw;
        padding: 2vw 2.5vw;
        border-radius: 2vw;
    }

    .checkout-summary .summary-item .item-price {
        font-size: 3.8vw;
        width: 100%;
        text-align: right;
        margin-top: 1vw;
        display: none;
    }

    .checkout-summary .summary-divider { margin: 4vw 0; border-top-width: 0.3vw; }
    .checkout-summary .summary-row {
        font-size: 3.4vw;
        padding: 2vw 0;
        gap: 3vw;
    }
    .checkout-summary .summary-total {
        gap: 3vw;
        font-size: 4.5vw;
        padding-top: 4vw;
        margin-top: 3vw;
        border-top-width: 0.4vw;
    }
    .checkout-summary .summary-total > span:last-child { font-size: 5vw; }

    .checkout-summary .btn-submit {
        gap: 2vw;
        padding: 4.5vw 5vw;
        border-radius: 3vw;
        font-size: 3.8vw;
        margin-top: 5vw;
        letter-spacing: 0.1em;
    }

    .checkout-summary .btn-back {
        gap: 1.5vw;
        font-size: 3.2vw;
        margin-top: 3vw;
        padding: 2.5vw;
        border-radius: 2.5vw;
    }

    /* Voucher */
    .voucher-summary-section {
        padding-top: 4.5vw;
        margin-top: 4vw;
        border-top-width: 0.3vw;
    }
    .voucher-summary-section .voucher-header {
        gap: 2.5vw;
        margin-bottom: 3.5vw;
    }
    .voucher-summary-section .voucher-header .voucher-label {
        gap: 1.2vw;
        font-size: 3.5vw;
    }
    .voucher-summary-section .voucher-header .voucher-label iconify-icon { font-size: 4vw; }
    .voucher-summary-section .voucher-header .btn-open-voucher {
        gap: 1vw;
        font-size: 3.2vw;
        padding: 1.5vw 2.5vw;
        border-radius: 1.7vw;
    }

    .applied-voucher-summary {
        gap: 2.5vw;
        padding: 3vw 3.5vw;
        border-radius: 2.5vw;
        margin-bottom: 3vw;
        border-width: 0.3vw;
        flex-wrap: wrap;
    }
    .applied-voucher-summary .voucher-info { gap: 1vw; }
    .applied-voucher-summary .voucher-info .code {
        font-size: 2.8vw;
        padding: 0.8vw 2.5vw;
        border-radius: 1.5vw;
    }
    .applied-voucher-summary .voucher-info .name { font-size: 3.2vw; white-space: normal; }
    .applied-voucher-summary .voucher-info .discount { font-size: 3vw; }
    .applied-voucher-summary .btn-remove-voucher-sm {
        padding: 1.5vw 2.5vw;
        border-radius: 1.5vw;
        font-size: 4vw;
    }

    /* Guest info */
    .guest-info {
        padding: 4vw 4.5vw;
        border-radius: 2.5vw;
        margin-top: 4.5vw;
        font-size: 3.2vw;
        border-width: 0.3vw;
        line-height: 1.6;
    }
    .guest-info .guest-title {
        gap: 1.5vw;
        margin-bottom: 2vw;
        font-size: 3.5vw;
    }
    .guest-info .guest-title iconify-icon { font-size: 4.2vw; }
    .guest-info .guest-hint { font-size: 2.8vw; margin-top: 2vw; }

    /* Warning */
    .checkout-warning-summary .warning-box {
        gap: 1.5vw;
        padding: 2.5vw 3vw;
        border-radius: 2vw;
        font-size: 2.8vw;
        line-height: 1.5;
    }

    /* Weight */
    .weight-display {
        gap: 1.5vw;
        font-size: 3vw;
        margin-top: 3vw;
        padding-top: 3.5vw;
        flex-wrap: wrap;
    }
    .weight-display .weight-value { font-size: 3.4vw; }
    .weight-display .weight-items { font-size: 2.8vw; margin-left: 1.5vw; }

    /* Empty state */
    .empty-cart-message { padding: 10vw 5vw; border-radius: 3vw; }
    .empty-cart-message iconify-icon { font-size: 20vw; }
    .empty-cart-message p { font-size: 3.5vw; margin-top: 5vw; }
    .empty-cart-message a {
        gap: 1.8vw; font-size: 3.4vw; margin-top: 5vw;
        padding: 3.5vw 5vw; border-radius: 2.5vw;
    }

    /* Address Card (Mobile) */
    .address-card-selected {
        border-radius: 2.8vw;
        padding: 3.2vw 3.5vw;
        margin-bottom: 4vw;
        border-width: 0.5vw;
    }
    .address-card-header { margin-bottom: 3vw; }
    .address-label-badge {
        font-size: 4.5vw;
        padding: 0.8vw 2.8vw;
        border-radius: 2.4vw;
    }
    .badge {
        font-size: 3vw;
        padding: 1.2vw 2.6vw;
        border-radius: 2.3vw;
    }
    .address-card-name { font-size: 3.5vw; }
    .address-card-phone { font-size: 3.5vw; margin-bottom: 0; }
    .address-card-text { font-size: 3.5vw; margin-bottom: 0.2vw; }
    .address-card-location { font-size: 3.5vw; margin-bottom: 0.2vw; }
    .address-card-region { font-size: 3.5vw; }
    .address-form-toolbar {
        gap: 5vw;
        margin-bottom: 2vw;
        flex-wrap: wrap;
    }
    .address-form-toolbar .address-link { font-size: 3.5vw; }

    /* Address Modal (Mobile) */
    .modal-content { max-width: 95vw; }
    .modal-header h3 { font-size: 4vw; }
    .address-details .address-label { font-size: 3vw; }
    .address-details .address-name { font-size: 2.8vw; }
    .address-details .address-text { font-size: 2.8vw; }
    .btn-add-address { font-size: 2.8vw; padding: 2.5vw 4vw; }
}
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(15, 23, 42, 0.55);
    backdrop-filter: blur(0.3vw);
    -webkit-backdrop-filter: blur(0.3vw);
    z-index: 2000;
    display: none;
    justify-content: center;
    align-items: center;
    padding: 2vw;
    animation: fadeInOverlay 0.25s ease-out;
}

.modal-overlay.active {
    display: flex;
}

@keyframes fadeInOverlay {
    from { opacity: 0; }
    to   { opacity: 1; }
}

.modal-content {
    background: #ffffff;
    border-radius: 1vw;
    max-width: 42vw;
    width: 100%;
    max-height: 82vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: 0 1.5vw 4vw rgba(15, 23, 42, 0.25);
    animation: modalSlideIn 0.25s ease-out;
}

@keyframes modalSlideIn {
    from { opacity: 0; transform: scale(0.96) translateY(-1vw); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}

/* Header */
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1vw;
    padding: 1.2vw 1.5vw;
    border-bottom: 0.1vw solid #f1f5f9;
    background: #fafbfc;
    flex-shrink: 0;
}

.modal-header h3 {
    display: flex;
    align-items: center;
    gap: 0.5vw;
    font-size: 1.05vw;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 0.02em;
}

.modal-header h3::before {
    content: '';
    display: inline-block;
    width: 0.3vw;
    height: 1vw;
    background: linear-gradient(180deg, #FDDD57 0%, #ecbc42 100%);
    border-radius: 100vw;
}

.modal-close {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2vw;
    height: 2vw;
    border-radius: 50%;
    border: 0.1vw solid #e2e8f0;
    background: #ffffff;
    color: #64748b;
    cursor: pointer;
    font-size: 1.2vw;
    line-height: 1;
    padding: 0;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

.modal-close:hover {
    background: #fffbf0;
    border-color: #fde68a;
    color: rgb(102, 72, 9);
    transform: rotate(90deg);
}

/* Body */
.modal-body {
    padding: 1.2vw 1.5vw;
    overflow-y: auto;
    flex: 1;
    min-height: 0;
}

.modal-body::-webkit-scrollbar { width: 0.3vw; }
.modal-body::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 100vw;
}
.modal-body::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 100vw;
}

/* Address option */
.address-option {
    margin-bottom: 0.8vw;
    border: 0.1vw solid #e2e8f0;
    border-radius: 0.6vw;
    padding: 0.9vw 1vw;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #ffffff;
}

.address-option:last-child {
    margin-bottom: 0;
}

.address-option:hover {
    border-color: #FDDD57;
    background: #fffbf0;
}

.address-option.selected {
    border-color: #ecbc42;
    background: #fffbf0;
    box-shadow: 0 0 0 0.15vw rgba(236, 188, 66, 0.3);
}

.address-option-card {
    display: flex;
    align-items: flex-start;
    gap: 0.7vw;
    cursor: pointer;
    margin: 0;
}

.address-option-card input[type="radio"] {
    width: 1.1vw;
    height: 1.1vw;
    margin-top: 0.2vw;
    accent-color: #ecbc42;
    cursor: pointer;
    flex-shrink: 0;
}

.address-details {
    flex: 1;
    min-width: 0;
}

.address-details .address-label {
    font-weight: 700;
    font-size: 0.88vw;
    color: #0f172a;
    margin-bottom: 0.3vw;
    display: flex;
    align-items: center;
    gap: 0.4vw;
}

.address-details .address-name {
    font-weight: 600;
    font-size: 0.82vw;
    color: #0f172a;
    margin-bottom: 0.15vw;
}

.address-details .address-phone {
    font-size: 0.78vw;
    color: #475569;
    margin-bottom: 0.2vw;
}

.address-details .address-text {
    font-size: 0.78vw;
    color: #475569;
    margin-top: 0.2vw;
    line-height: 1.55;
    white-space: normal;
    word-break: break-word;
}

.address-details .badge-default {
    display: inline-flex;
    align-items: center;
    gap: 0.25vw;
    margin-top: 0.4vw;
    padding: 0.2vw 0.6vw;
    background: #fef3c7;
    color: rgb(102, 72, 9);
    font-size: 0.65vw;
    font-weight: 700;
    border-radius: 0.3vw;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

/* Footer */
.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 0.8vw;
    padding: 1vw 1.5vw;
    border-top: 0.1vw solid #f1f5f9;
    background: #fafbfc;
    flex-shrink: 0;
}

.modal-footer .btn-secondary,
.modal-footer .btn-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.4vw;
    padding: 0.75vw 1.4vw;
    border-radius: 0.6vw;
    font-size: 0.82vw;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
    font-family: inherit;
    border: 0.1vw solid transparent;
}

.modal-footer .btn-secondary {
    background: #ffffff;
    border-color: #e2e8f0;
    color: #475569;
}

.modal-footer .btn-secondary:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
}

.modal-footer .btn-primary {
    background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
    color: rgb(102, 72, 9);
    box-shadow: 0 0.15vw 0.5vw rgba(236, 188, 66, 0.3);
}

.modal-footer .btn-primary:hover {
    transform: translateY(-0.1vw);
    box-shadow: 0 0.35vw 1.2vw rgba(236, 188, 66, 0.5);
}

/* ============================================
   RESPONSIVE — TABLET (≤ 1024px)
   ============================================ */
@media (max-width: 1024px) {
    .modal-overlay {
        padding: 3vw;
    }

    .modal-content {
        max-width: 85vw;
        max-height: 88vh;
        border-radius: 3vw;
    }

    .modal-header {
        gap: 2vw;
        padding: 3vw 3.5vw;
        border-bottom-width: 0.2vw;
    }

    .modal-header h3 {
        gap: 1.2vw;
        font-size: 2.6vw;
    }

    .modal-header h3::before {
        width: 0.6vw;
        height: 2.5vw;
    }

    .modal-close {
        width: 5.5vw;
        height: 5.5vw;
        border-width: 0.2vw;
        font-size: 3.4vw;
    }

    .modal-body {
        padding: 3vw 3.5vw;
    }

    .modal-body::-webkit-scrollbar { width: 0.5vw; }

    .address-option {
        margin-bottom: 2vw;
        border-radius: 1.7vw;
        padding: 2.5vw 3vw;
        border-width: 0.2vw;
    }

    .address-option.selected {
        box-shadow: 0 0 0 0.4vw rgba(236, 188, 66, 0.3);
    }

    .address-option-card {
        gap: 2vw;
    }

    .address-option-card input[type="radio"] {
        width: 2.8vw;
        height: 2.8vw;
        margin-top: 0.5vw;
    }

    .address-details .address-label {
        font-size: 2.3vw;
        gap: 1vw;
        margin-bottom: 1vw;
    }

    .address-details .address-name {
        font-size: 2.1vw;
        margin-bottom: 0.5vw;
    }

    .address-details .address-phone {
        font-size: 2vw;
        margin-bottom: 0.7vw;
    }

    .address-details .address-text {
        font-size: 2vw;
        line-height: 1.7;
        margin-top: 0.7vw;
    }

    .address-details .badge-default {
        gap: 0.7vw;
        margin-top: 1.2vw;
        padding: 0.7vw 1.7vw;
        font-size: 1.7vw;
        border-radius: 1vw;
    }

    .modal-footer {
        gap: 1.7vw;
        padding: 3vw 3.5vw;
        border-top-width: 0.2vw;
    }

    .modal-footer .btn-secondary,
    .modal-footer .btn-primary {
        gap: 1vw;
        padding: 2.3vw 3.5vw;
        border-radius: 1.7vw;
        font-size: 2.2vw;
        border-width: 0.2vw;
    }
}

/* ============================================
   RESPONSIVE — MOBILE (≤ 480px) — Bottom Sheet
   ============================================ */
@media (max-width: 480px) {
    .modal-overlay {
        padding: 0;
        align-items: flex-end;
        justify-content: stretch;
    }

    .modal-content {
        max-width: 100%;
        width: 100%;
        max-height: 90vh;
        border-radius: 4vw 4vw 0 0;
        animation: slideUp 0.3s ease-out;
    }

    @keyframes slideUp {
        from { transform: translateY(100%); opacity: 0; }
        to   { transform: translateY(0); opacity: 1; }
    }

    .modal-header {
        gap: 2.5vw;
        padding: 4vw 5vw 3.5vw;
        border-bottom-width: 0.3vw;
        position: relative;
    }

    /* Grabber indicator di atas modal */
    .modal-header::before {
        content: '';
        position: absolute;
        top: 1.5vw;
        left: 50%;
        transform: translateX(-50%);
        width: 12vw;
        height: 1vw;
        background: #cbd5e1;
        border-radius: 100vw;
    }

    .modal-header h3 {
        gap: 2vw;
        font-size: 4.5vw;
        padding-top: 2vw;
    }

    .modal-header h3::before {
        width: 1vw;
        height: 4vw;
    }

    .modal-close {
        width: 9vw;
        height: 9vw;
        border-width: 0.3vw;
        font-size: 5vw;
        margin-top: 2vw;
    }

    .modal-body {
        padding: 4vw 5vw;
    }

    .modal-body::-webkit-scrollbar { width: 0.6vw; }

    .address-option {
        margin-bottom: 3vw;
        border-radius: 3vw;
        padding: 4vw 4.5vw;
        border-width: 0.3vw;
    }

    .address-option.selected {
        box-shadow: 0 0 0 0.7vw rgba(236, 188, 66, 0.3);
    }

    .address-option-card {
        gap: 3vw;
    }

    .address-option-card input[type="radio"] {
        width: 4.5vw;
        height: 4.5vw;
        margin-top: 1vw;
    }

    .address-details .address-label {
        font-size: 4vw;
        gap: 1.5vw;
        margin-bottom: 1.7vw;
    }

    .address-details .address-name {
        font-size: 3.6vw;
        margin-bottom: 1vw;
    }

    .address-details .address-phone {
        font-size: 3.4vw;
        margin-bottom: 1.2vw;
    }

    .address-details .address-text {
        font-size: 3.2vw;
        line-height: 1.7;
        margin-top: 1vw;
    }

    .address-details .badge-default {
        gap: 1vw;
        margin-top: 2vw;
        padding: 1.2vw 2.8vw;
        font-size: 2.8vw;
        border-radius: 2vw;
    }

    .modal-footer {
        flex-direction: column-reverse;
        gap: 2.5vw;
        padding: 4vw 5vw 5vw;
        border-top-width: 0.3vw;
        padding-bottom: max(4vw, env(safe-area-inset-bottom));
    }

    .modal-footer .btn-secondary,
    .modal-footer .btn-primary {
        gap: 2vw;
        padding: 4vw 5vw;
        border-radius: 3vw;
        font-size: 3.4vw;
        border-width: 0.3vw;
        width: 100%;
    }
}
</style>

    <main class="checkout-container">

    {{-- Header --}}
    <div class="checkout-header">
        <h1>Checkout</h1>
        <p>Lengkapi data untuk menyelesaikan pesanan.</p>
    </div>

    @if (session('error'))
        <div class="checkout-alert error">
            <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
            <div>
                <div class="alert-title">Terjadi kesalahan</div>
                <div>{{ session('error') }}</div>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="checkout-alert error">
            <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
            <div style="flex:1;">
                <div class="alert-title">
                    <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                    Terjadi kesalahan:
                </div>
                <ul class="alert-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="checkout-grid">

        {{-- FORM --}}
        <form action="{{ route('customer.checkout.process') }}" method="POST" id="checkout-form" style="width:100%;">
            @csrf

            <input type="hidden" name="voucher_code" id="applied-voucher-code" value="{{ session('voucher_code') }}">
            <input type="hidden" name="voucher_discount" id="applied-voucher-discount" value="{{ session('voucher_discount') ?? 0 }}">
            <input type="hidden" name="origin_postal_code" id="origin_postal_code" value="{{ $setting->postal_code ?? config('services.biteship.origin_postal_code', '46191') }}">
            <input type="hidden" name="payment_method" value="midtrans">
            <input type="hidden" name="address_id" id="selected_address_id" value="{{ $defaultAddress?->id ?? '' }}">

            @php
                $hasAddresses = $customer && $addresses->isNotEmpty();
                $showForm = !$defaultAddress;
            @endphp

            {{-- ============================================ --}}
            {{-- INFORMASI PENGIRIMAN --}}
            {{-- ============================================ --}}
            <div class="checkout-section">
                <h2 class="section-title">
                    <iconify-icon icon="mdi:map-marker-outline"></iconify-icon>
                    Informasi Pengiriman
                </h2>
                <p class="section-subtitle">Isi data penerima paket.</p>

                {{-- Address Card UI (Login + punya alamat) --}}
                @if($hasAddresses)
                    <div id="address-card-ui" style="{{ $showForm ? 'display:none' : '' }}">
                        @if($defaultAddress)
                            <div class="address-card-selected">
                                <div class="address-card-header">
                                    <span class="address-label-badge">{{ $defaultAddress->label ?: 'Alamat Utama' }}</span>
                                    @if($defaultAddress->is_default)
                                        <span class="badge badge-default">Utama</span>
                                    @endif
                                </div>
                                <div class="address-card-body">
                                    <div class="address-card-name">{{ $defaultAddress->recipient_name }}</div>
                                    <div class="address-card-phone">{{ $defaultAddress->recipient_phone }}</div>
                                    <div class="address-card-text">{{ $defaultAddress->address }}</div>
                                    <div class="address-card-location">{{ $defaultAddress->city }}, {{ $defaultAddress->district }}, {{ $defaultAddress->subdistrict }}</div>
                                    <div class="address-card-region">{{ $defaultAddress->province }} {{ $defaultAddress->postal_code }}</div>
                                </div>
                            </div>
                            <div class="address-form-toolbar">
                                <a href="#" class="address-link" onclick="openAddressSelectorModal(event)">Pilih Alamat Lain</a>
                                <a href="#" class="address-link" onclick="showNewAddressForm(event)">+ Tambah Alamat Baru</a>
                                <a href="#" class="address-link" id="cancel-new-address" onclick="hideNewAddressForm()" style="display:none;">Batal</a>
                            </div>
                        @else
                            <div class="address-empty">
                                <p>Belum ada alamat tersimpan.</p>
                                <a href="#" class="btn-add-address" onclick="showNewAddressForm(event)">+ Tambah Alamat Baru</a>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Form Alamat --}}
                <div id="address-form-wrapper" style="{{ $hasAddresses && !$showForm ? 'display:none' : '' }}">
                    <div class="form-grid">
                        {{-- Nama Penerima --}}
                        <div class="form-group full-width">
                            <label>
                                <iconify-icon icon="mdi:account-outline"></iconify-icon>
                                Nama Penerima <span class="required">*</span>
                            </label>
                            <input type="text" name="shipping_name" id="shipping_name"
                                value="{{ old('shipping_name', $guestData['shipping_name'] ?? $defaultAddress->recipient_name ?? $customer->name ?? '') }}"
                                placeholder="Nama lengkap penerima" required>
                            @error('shipping_name')
                                <span class="error-text">
                                    <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        {{-- Nomor WhatsApp --}}
                        <div class="form-group full-width">
                            <label>
                                <iconify-icon icon="mdi:whatsapp"></iconify-icon>
                                Nomor WhatsApp <span class="required">*</span>
                            </label>
                            <input type="tel" name="shipping_phone" id="shipping_phone"
                                value="{{ old('shipping_phone', $defaultAddress->recipient_phone ?? $customer->phone ?? '') }}"
                                placeholder="08xxxxxxxxxx" required>
                            @error('shipping_phone')
                                <span class="error-text">
                                    <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        {{-- Alamat Lengkap --}}
                        <div class="form-group full-width">
                            <label>
                                <iconify-icon icon="mdi:home-outline"></iconify-icon>
                                Alamat Lengkap <span class="required">*</span>
                            </label>
                            <textarea name="shipping_address" id="shipping_address" rows="3" placeholder="Contoh: Jalan Mawar No. 10, RT 01 RW 02" required>{{ old('shipping_address', $defaultAddress->address ?? '') }}</textarea>
                            <span class="helper-text">
                                <iconify-icon icon="mdi:information-outline"></iconify-icon>
                                Tulis nama jalan, nomor rumah, RT/RW, patokan.
                            </span>
                            @error('shipping_address')
                                <span class="error-text">
                                    <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        {{-- Provinsi --}}
                        <div class="form-group">
                            <label>
                                <iconify-icon icon="mdi:map-marker-outline"></iconify-icon>
                                Provinsi <span class="required">*</span>
                            </label>
                            <select name="shipping_province" id="province" required>
                                <option value="">-- Pilih Provinsi --</option>
                            </select>
                            <input type="hidden" name="shipping_province_id" id="province_id">
                            @error('shipping_province')
                                <span class="error-text">
                                    <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        {{-- Kota --}}
                        <div class="form-group">
                            <label>
                                <iconify-icon icon="mdi:city-variant-outline"></iconify-icon>
                                Kota/Kabupaten <span class="required">*</span>
                            </label>
                            <select name="shipping_city" id="city" required>
                                <option value="">-- Pilih Kota --</option>
                            </select>
                            <input type="hidden" name="shipping_city_id" id="city_id">
                            @error('shipping_city')
                                <span class="error-text">
                                    <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        {{-- Kecamatan --}}
                        <div class="form-group">
                            <label>
                                <iconify-icon icon="mdi:map-marker-radius-outline"></iconify-icon>
                                Kecamatan <span class="required">*</span>
                            </label>
                            <select name="shipping_district" id="district" required>
                                <option value="">-- Pilih Kecamatan --</option>
                            </select>
                            <input type="hidden" name="shipping_district_id" id="district_id">
                            @error('shipping_district')
                                <span class="error-text">
                                    <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        {{-- Kelurahan --}}
                        <div class="form-group">
                            <label>
                                <iconify-icon icon="mdi:home-map-marker"></iconify-icon>
                                Kelurahan <span class="required">*</span>
                            </label>
                            <select name="shipping_subdistrict" id="subdistrict" required>
                                <option value="">-- Pilih Kelurahan --</option>
                            </select>
                            <input type="hidden" name="shipping_subdistrict_id" id="subdistrict_id">
                            @error('shipping_subdistrict')
                                <span class="error-text">
                                    <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <input type="hidden" name="shipping_postal_code" id="shipping_postal_code" value="0">
                    </div>
                </div>
                {{-- /#address-form-wrapper --}}

                {{-- Address Selector Modal --}}
                @if($hasAddresses)
                    <div id="address-selector-modal" class="modal-overlay" style="display:none;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3>Pilih Alamat Pengiriman</h3>
                                <button type="button" class="modal-close" onclick="closeAddressSelectorModal()">&times;</button>
                            </div>
                            <div class="modal-body" id="address-list-modal">
                                @foreach($addresses as $addr)
                                    <div class="address-option" data-id="{{ $addr->id }}" data-json='@json($addr)'>
                                        <label class="address-option-card">
                                            <input type="radio" name="selected_address_radio" value="{{ $addr->id }}" {{ $addr->is_default ? 'checked' : '' }}>
                                            <div class="address-details">
                                                <div class="address-label">{{ $addr->label ?: 'Alamat ' . $loop->iteration }}</div>
                                                <div class="address-name">{{ $addr->recipient_name }}</div>
                                                <div class="address-phone">{{ $addr->recipient_phone }}</div>
                                                <div class="address-text">{{ $addr->address }}, {{ $addr->city }}, {{ $addr->district }}, {{ $addr->subdistrict }}, {{ $addr->province }} {{ $addr->postal_code }}</div>
                                                @if($addr->is_default)
                                                    <span class="badge-default"> Utama</span>
                                                @endif
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn-secondary" onclick="closeAddressSelectorModal()">Batal</button>
                                <button type="button" class="btn-primary" onclick="selectAddressFromModal()">Pilih</button>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
            {{-- /.checkout-section --}}

            {{-- ============================================ --}}
            {{-- EKSPEDISI & ONGKIR --}}
            {{-- ============================================ --}}
            <div class="checkout-section">
                <h2 class="section-title">
                    <iconify-icon icon="mdi:truck-delivery-outline"></iconify-icon>
                    Ekspedisi & Ongkir
                </h2>
                <p class="section-subtitle">Pilih kurir dan lihat estimasi ongkir.</p>

                <div id="shipping-warning" class="checkout-alert error hidden" style="background:#fffbeb;border-color:#fde68a;color:#b45309;">
                    <iconify-icon icon="mdi:alert-outline"></iconify-icon>
                    <div>
                        <strong>Pilih kurir dan layanan pengiriman</strong> terlebih dahulu untuk melihat biaya ongkir.
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>
                            <iconify-icon icon="mdi:truck-fast-outline"></iconify-icon>
                            Layanan Pengiriman <span class="required">*</span>
                        </label>
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
                        <input type="hidden" name="original_shipping_cost" id="original_shipping_cost" value="0">
                        <div id="shipping-error" class="error-text hidden">
                            <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                            Silakan pilih kurir dan layanan pengiriman terlebih dahulu!
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- CATATAN --}}
            {{-- ============================================ --}}
            <div class="checkout-section">
                <h2 class="section-title">
                    <iconify-icon icon="mdi:note-text-outline"></iconify-icon>
                    Catatan
                </h2>
                <div class="form-group">
                    <textarea name="notes" rows="3" placeholder="Tambahkan catatan untuk pesanan (opsional)">{{ old('notes') }}</textarea>
                </div>
            </div>

        </form>

        {{-- ============================================ --}}
        {{-- SUMMARY --}}
        {{-- ============================================ --}}
        <div class="checkout-summary">
            <h2 class="summary-title">
                <iconify-icon icon="mdi:receipt-text-outline"></iconify-icon>
                Ringkasan Pesanan
            </h2>

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
                                <div class="item-name-layout-mobile">
                                    <div class="item-name">{{ $item['product_name'] }}</div>
                                    <div class="item-name item-price-{{ $key }}">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</div>
                                </div>
                                @if ($item['variant_name'])
                                    <div class="item-variant">{{ $item['variant_name'] }}</div>
                                @endif
                                <div class="summary-qty-layout">
                                    <div class="summary-qty">
                                        <button type="button" class="qty-btn qty-decrease" data-key="{{ $key }}" aria-label="Kurangi">
                                            <iconify-icon icon="mdi:minus"></iconify-icon>
                                        </button>
                                        <input type="number" class="qty-input" data-key="{{ $key }}"
                                            value="{{ $item['quantity'] }}" min="1" readonly>
                                        <button type="button" class="qty-btn qty-increase" data-key="{{ $key }}" aria-label="Tambah">
                                            <iconify-icon icon="mdi:plus"></iconify-icon>
                                        </button>
                                    </div>
                                    <button type="button" class="summary-remove" data-key="{{ $key }}">
                                        <iconify-icon icon="mdi:trash-can-outline"></iconify-icon>
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
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

            {{-- Voucher --}}
            <div class="voucher-summary-section">
                <div class="voucher-header">
                    <span class="voucher-label">
                        <iconify-icon icon="mdi:ticket-percent-outline"></iconify-icon>
                        Voucher
                    </span>
                    <button type="button" class="btn-open-voucher" id="btn-open-voucher">
                        <span id="voucher-action-text">
                            @if($appliedVoucher)
                                Ganti Voucher
                            @else
                                Pilih Voucher
                            @endif
                        </span>
                    </button>
                </div>

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
                <div class="warning-box">
                    <iconify-icon icon="mdi:alert-outline"></iconify-icon>
                    <span>Lengkapi data pengiriman dan pilih kurir terlebih dahulu</span>
                </div>
            </div>

            {{-- Submit --}}
            <button type="button" class="btn-submit" id="btn-submit-order">
                <iconify-icon icon="mdi:check-circle-outline"></iconify-icon>
                Buat Pesanan
            </button>

            <a href="{{ route('customer.cart.index') }}" class="btn-back" id="btn-back-to-cart">
                <iconify-icon icon="mdi:arrow-left"></iconify-icon>
                Kembali ke Keranjang
            </a>
        </div>

    </div>

</main>

    <div id="shipping-popup" class="shipping-popup hidden" role="dialog" aria-modal="true" aria-labelledby="shipping-popup-title">
    <div class="shipping-popup-backdrop" data-close-shipping-popup></div>
    <div class="shipping-popup-card">
        <div class="shipping-popup-header">
            <h3 id="shipping-popup-title">
                <iconify-icon icon="mdi:truck-fast-outline"></iconify-icon>
                Pilih Layanan Pengiriman
            </h3>
            <button type="button" class="shipping-popup-close" data-close-shipping-popup aria-label="Tutup">
                <iconify-icon icon="mdi:close"></iconify-icon>
            </button>
        </div>
        <div class="shipping-popup-body">
            <p class="shipping-popup-note">
                <iconify-icon icon="mdi:information-outline"></iconify-icon>
                Pilih satu layanan kurir beserta harga dan estimasi pengiriman.
            </p>
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
                        $('#original_shipping_cost').val(0);
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

    // 🔥 AMBIL ORIGINAL SHIPPING COST dari hidden input (bukan dari #shipping_cost yang mungkin sudah diskon)
    var originalShippingCost = parseInt($('#original_shipping_cost').val()) || 0;
    var shippingCost = originalShippingCost > 0
        ? originalShippingCost
        : (parseInt($('#shipping_cost').val()) || 0);
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
            // 🔥 RESET UI VOUCHER (restore original shipping cost)
            resetVoucherUI();

            // Reset hidden inputs
            $('#applied-voucher-code').val('');
            $('#applied-voucher-discount').val(0);

            // 🔥 RESTORE shipping cost & total dari response server
            if (data.new_shipping_cost !== undefined) {
                $('#shipping_cost').val(data.new_shipping_cost);
                var shippingCostText = document.getElementById('shipping-cost-text');
                if (shippingCostText) {
                    shippingCostText.textContent = 'Rp ' + formatNumber(data.new_shipping_cost);
                }
            }

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
        var originalCost = parseInt($('#original_shipping_cost').val()) || 0;
        if (originalCost > 0) {
            shippingCostText.textContent = 'Rp ' + formatNumber(originalCost);
        } else {
            shippingCostText.textContent = 'Belum dipilih';
        }
    }

    // 🔥 RESTORE original shipping cost (bukan 0) agar tidak kehilangan ongkir asli
    var originalCost = parseInt($('#original_shipping_cost').val()) || 0;
    $('#shipping_cost').val(originalCost);

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
// ADDRESS SELECTOR FUNCTIONS (Logged-in Users)
// ============================================

function getCsrfToken() {
    return $('meta[name="csrf-token"]').attr('content') || '';
}

function openAddressSelectorModal(e) {
    if (e) e.preventDefault();
    var modal = document.getElementById('address-selector-modal');
    if (modal) {
        modal.style.display = 'flex';
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';

        var currentId = $('#selected_address_id').val();
        if (currentId) {
            $('input[name="selected_address_radio"][value="' + currentId + '"]').prop('checked', true);
        }
    }
}

function closeAddressSelectorModal() {
    var modal = document.getElementById('address-selector-modal');
    if (modal) {
        modal.style.display = 'none';
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

function selectAddressFromModal() {
    var selectedRadio = $('input[name="selected_address_radio"]:checked');
    var addressId = selectedRadio.val();

    if (!addressId) {
        var firstRadio = $('input[name="selected_address_radio"]').first();
        addressId = firstRadio.val();
    }

    if (addressId) {
        var address = window.savedAddresses.find(function(item) {
            return String(item.id) === String(addressId);
        });

        if (address) {
            selectAddressFromList(address);
        }
    }

    closeAddressSelectorModal();
}

function selectAddressFromList(address) {
    $('#selected_address_id').val(address.id);

    var cardHtml = '<div class="address-card-header">' +
        '<span class="address-label-badge">' + (address.label || 'Alamat Utama') + '</span>' +
        (address.is_default ? '<span class="badge badge-default">Utama</span>' : '') +
        '</div><div class="address-card-body">' +
        '<div class="address-card-name">' + address.recipient_name + '</div>' +
        '<div class="address-card-phone">' + address.recipient_phone + '</div>' +
        '<div class="address-card-text">' + address.address + '</div>' +
        '<div class="address-card-location">' + address.city + ', ' + address.district + ', ' + address.subdistrict + '</div>' +
        '<div class="address-card-region">' + address.province + ' ' + address.postal_code + '</div>' +
        '</div>';
    $('#address-card-ui .address-card-selected').html(cardHtml);

    $('#address-form-wrapper').hide();
    $('#address-card-ui').show();

    hideNewAddressForm();

    var zipCode = address.postal_code || '0';
    $('#shipping_postal_code').val(zipCode);

    if (zipCode && zipCode !== '0' && zipCode.length >= 4) {
        checkShippingCost(zipCode);
    }
}

function showNewAddressForm(e) {
    if (e) e.preventDefault();

    $('#selected_address_id').val('');

    $('#address-card-ui').show();
    $('#address-form-wrapper').show();
    $('#cancel-new-address').show();

    $('#shipping_cost').val(0);
    $('#original_shipping_cost').val(0);
    $('#shipping-cost-text').text('Rp 0');
    window.shippingSelection = null;
    window.hasCourierSelected = false;
    $('#courier').prop('disabled', true).html('<option value="">-- Pilih kota/kelurahan dulu --</option>');
    $('#service').html('<option value="">-- Pilih Layanan --</option>').prop('disabled', true);
    $('#shipping-cost-display').text('Pilih kurir');

    setTimeout(function() {
        $('#shipping_name').focus();
    }, 300);
}

function hideNewAddressForm() {
    $('#address-form-wrapper').hide();
    $('#cancel-new-address').hide();
}

function saveNewAddress(callback) {
    var $btn = $('#btn-submit-order');
    $btn.prop('disabled', true).text('Menyimpan alamat...');

    var formData = $('#checkout-form').serializeArray();
    var payload = {};
    formData.forEach(function(item) {
        if (item.name !== 'address_id' && item.name !== '_token' && item.name !== 'voucher_code' && item.name !== 'voucher_discount' && item.name !== 'origin_postal_code' && item.name !== 'payment_method') {
            payload[item.name] = item.value;
        }
    });

    payload['province_id'] = $('#province_id').val();
    payload['city_id'] = $('#city_id').val();
    payload['district_id'] = $('#district_id').val();
    payload['subdistrict_id'] = $('#subdistrict_id').val();

    $.ajax({
        url: '{{ route("customer.checkout.save-address") }}',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': getCsrfToken()
        },
        data: payload,
        success: function(response) {
            $btn.prop('disabled', false).text('Buat Pesanan');
            if (response.success && response.address_id) {
                if (typeof callback === 'function') callback(response.address_id);
            } else {
                showToast('Gagal menyimpan alamat: ' + (response.message || 'Unknown error'), 'error');
                $btn.prop('disabled', false).text('Buat Pesanan');
            }
        },
        error: function(xhr) {
            $btn.prop('disabled', false).text('Buat Pesanan');
            var msg = 'Gagal menyimpan alamat';
            try {
                var resp = JSON.parse(xhr.responseText);
                msg = resp.message || msg;
            } catch(e) {}
            showToast(msg, 'error');
        }
    });
}

function submitOrderForm() {
    var $btn = $('#btn-submit-order');
    var form = document.getElementById('checkout-form');
    var selection = window.shippingSelection;

    if (!selection || !selection.courier || !selection.service) {
        showToast('Silakan pilih kurir dan layanan pengiriman terlebih dahulu!', 'warning');
        $('#shipping-error').removeClass('hidden');
        return false;
    }

    var shippingCost = parseInt($('#shipping_cost').val()) || 0;

    if (!$('input[name="shipping_cost"]').length) {
        $('<input>').attr({type: 'hidden', name: 'shipping_cost', value: shippingCost}).appendTo(form);
    } else {
        $('input[name="shipping_cost"]').val(shippingCost);
    }

    if (!$('input[name="courier"]').length) {
        $('<input>').attr({type: 'hidden', name: 'courier', value: selection.courier}).appendTo(form);
    } else {
        $('input[name="courier"]').val(selection.courier);
    }

    if (!$('input[name="shipping_service"]').length) {
        $('<input>').attr({type: 'hidden', name: 'shipping_service', value: selection.service}).appendTo(form);
    } else {
        $('input[name="shipping_service"]').val(selection.service);
    }

    $btn.prop('disabled', true).text('Memproses...');
    form.submit();
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

        var isLoggedIn = $('meta[name="customer-logged-in"]').attr('content') === 'true'
                        || '{{ Auth::guard("customer")->check() ? "true" : "false" }}' === 'true';

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
        // Untuk user login yang sudah pilih address_id, lewati validasi field alamat
        var selectedAddressId = $('#selected_address_id').val();
        var skipAddressValidation = isLoggedIn && selectedAddressId;

        var requiredFields = form.querySelectorAll('input:not([type="hidden"]):not([disabled]), select:not([disabled]), textarea:not([disabled])');
        var firstInvalid = null;

        if (!skipAddressValidation) {
            requiredFields.forEach(function(field) {
                if (field.hasAttribute('required') && !field.value.trim()) {
                    var label = field.closest('.form-group')?.querySelector('label')?.textContent?.trim() || field.name;
                    errors.push('⚠️ ' + label + ' wajib diisi!');
                    if (!firstInvalid) {
                        firstInvalid = field;
                    }
                }
            });
        }

        if (errors.length > 0) {
            showToast(errors[0], 'error');
            if (firstInvalid) {
                firstInvalid.focus();
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return;
        }

        // 🔥 JIKA GUEST, SIMPAN DATA CHECKOUT DULU KE SESSION
        if (!isLoggedIn) {
            saveCheckoutDataToSession(function() {
                // Setelah tersimpan, buka login popup
                openLoginPopupForCheckout();
            });
            return;
        }

        // 🔥 USER LOGIN YANG ISI FORM "TAMBAH ALAMAT BARU" (tanpa address_id)
        // Simpan alamat ke database dulu, lalu submit form
        if (isLoggedIn && !selectedAddressId) {
            saveNewAddress(function(addressId) {
                if (addressId) {
                    $('#selected_address_id').val(addressId);
                }
                submitOrderForm();
            });
            return;
        }


        // 🔥 USER LOGIN DENGAN address_id TERPILIH → SUBMIT LANGSUNG
        submitOrderForm();
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

function saveCheckoutDataToSession(callback) {
    var form = document.getElementById('checkout-form');
    var formData = new FormData(form);

    // Tambahkan data alamat lengkap (yang mungkin tidak ada di form)
    formData.append('save_checkout_data', '1');

    $.ajax({
        url: '{{ route("customer.checkout.save-data") }}', // route baru
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        },
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            console.log('✅ Checkout data saved to session:', response);
            if (typeof callback === 'function') callback();
        },
        error: function(xhr) {
            console.error('❌ Failed to save checkout data:', xhr);
            showToast('Gagal menyimpan data checkout. Silakan coba lagi.', 'error');
        }
    });
}

    function openLoginPopupForCheckout() {
        openLoginPopup(null, function() {
            // 🔥 Update CSRF token di form (setelah login, token bisa berubah)
            var newToken = document.querySelector('meta[name="csrf-token"]').content;
            var formToken = document.querySelector('#checkout-form input[name="_token"]');
            if (formToken) {
                formToken.value = newToken;
            }

            var form = document.getElementById('checkout-form');
            var selection = window.shippingSelection;

            // 🔥 Pastikan data pengiriman tersimpan di hidden input
            if (selection && selection.courier && selection.service) {
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

                if (!$('input[name="courier"]').length) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'courier',
                        value: selection.courier
                    }).appendTo(form);
                }

                if (!$('input[name="shipping_service"]').length) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'shipping_service',
                        value: selection.service
                    }).appendTo(form);
                }
            }

            // 🔥 Submit form ke route checkout process
            var $btn = $('#btn-submit-order');
            $btn.prop('disabled', true).text('Memproses...');
            form.submit();
        });
    }

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

    // 🔥 SIMPAN ORIGINAL SHIPPING COST (bukan yang diskon) untuk restore saat ganti voucher
    if (cost > 0) {
        $('#original_shipping_cost').val(cost);
    }
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

    // 🔥 VARIABEL GLOBAL (dipakai fungsi global dan $(document).ready)
    window.defaultAddress = @json($defaultAddress);
    window.savedAddresses = @json($addresses);
    window.isLoggedIn = @json(Auth::guard('customer')->check());
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

        var originPostalCode = $('#origin_postal_code').val() || '{{ $setting->postal_code ?? config("services.biteship.origin_postal_code", "46191") }}';

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
        $('#original_shipping_cost').val(0);
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
        $('#original_shipping_cost').val(0);
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
        $('#original_shipping_cost').val(0);
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
        $('#original_shipping_cost').val(0);
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

    $('#courier').on('change', function() {
        var selectedCourier = $(this).val();

        console.log('=== COURIER SELECTED ===');
        console.log('Courier:', selectedCourier);

        $('#service').prop('disabled', true).html('<option value="">-- Memuat Layanan --</option>');
        $('#shipping_cost').val(0);
        $('#original_shipping_cost').val(0);
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
        $('#original_shipping_cost').val(0);
        $('#shipping-cost-text').text('Rp 0');
        window.shippingSelection = null;
        window.hasCourierSelected = false;
        updateTotal();
        return;
    }

    // 🔥 UPDATE UI
    $('#shipping_cost').val(cost);
    $('#original_shipping_cost').val(cost);
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

    // For logged-in users with addresses: show card UI, hide form
    if (isLoggedIn && defaultAddress) {
        $('#selected_address_id').val(defaultAddress.id);
        $('#address-card-ui').show();
        $('#address-form-wrapper').hide();
    }

    // Close modal on overlay click
    $(document).on('click', '#address-selector-modal', function(e) {
        if (e.target === this) {
            closeAddressSelectorModal();
        }
    });

    // Close modal on Escape key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $('#address-selector-modal').is(':visible')) {
            closeAddressSelectorModal();
        }
    });

    loadProvinces();

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
