{{-- ============================================ --}}
{{-- MODAL PILIH VARIAN --}}
{{-- ============================================ --}}
<div id="variant-modal" class="variant-modal-overlay hidden" onclick="closeVariantModal(event)">
    <div class="variant-modal-box" onclick="event.stopPropagation()">

        {{-- Header --}}
        <div class="variant-modal-header">
            <h3 class="variant-modal-title">
                <iconify-icon icon="mdi:tag-multiple-outline"></iconify-icon>
                Pilih Varian
            </h3>
            <button type="button" onclick="closeVariantModal()" class="variant-modal-close" aria-label="Tutup">
                <iconify-icon icon="mdi:close"></iconify-icon>
            </button>
        </div>

        {{-- Product Info --}}
        <div class="variant-product-info">
            <div class="variant-product-image-wrapper">
                <img id="modal-product-image" src="" alt="Product" class="variant-product-image">
            </div>
            <div class="variant-product-details">
                <h4 id="modal-product-name" class="variant-product-name"></h4>
                <p id="modal-product-price" class="variant-product-price"></p>
                <p id="modal-product-stock" class="variant-product-stock"></p>
            </div>
        </div>

        {{-- Hidden Inputs --}}
        <input type="hidden" id="modal-product-id" value="">
        <input type="hidden" id="modal-selected-variant" value="">
        <input type="hidden" id="modal-variant-values" value="">
        <input type="hidden" id="modal-action-mode" value="add_to_cart">

        {{-- Varian Options --}}
        <div id="modal-variant-options" class="variant-options-wrapper">
            {{-- Akan diisi oleh JavaScript --}}
        </div>

        {{-- Quantity --}}
        <div class="variant-quantity-wrapper">
            <div class="variant-quantity-control">
                <button type="button" class="variant-qty-btn" data-action="decrease" aria-label="Kurangi">−</button>
                <input type="number" id="modal-qty-input" value="1" min="1" max="999" class="variant-qty-input">
                <button type="button" class="variant-qty-btn" data-action="increase" aria-label="Tambah">+</button>
            </div>

            <button type="button" id="modal-add-to-cart-btn" class="variant-add-to-cart-btn">
                <iconify-icon icon="mdi:cart-plus"></iconify-icon>
                <span class="btn-label">Tambah ke Keranjang</span>
            </button>
        </div>

        {{-- Error Message --}}
        <p id="modal-error" class="variant-error-message hidden"></p>
    </div>
</div>

<style>
    /* ============================================
       VARIANT MODAL STYLES
       ============================================ */
    /* --------------------------------------------
       BOX
       -------------------------------------------- */
    .variant-modal-box {
        background: #ffffff;
        border-radius: 1.2vw;
        width: 100%;
        max-width: 32vw;
        max-height: 92vh;
        overflow-y: auto;
        padding: 1.5vw;
        position: relative;
        box-shadow: 0 1vw 4vw rgba(0, 0, 0, 0.2);
    }

    @keyframes variantSlideIn {
        from { opacity: 0; transform: scale(0.96) translateY(-1vw); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }

    .variant-modal-box::-webkit-scrollbar { width: 0.3vw; }
    .variant-modal-box::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 100vw;
    }
    .variant-modal-box::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 100vw;
    }

    /* --------------------------------------------
       HEADER
       -------------------------------------------- */
    .variant-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1vw;
        padding-bottom: 1vw;
        margin-bottom: 1.2vw;
        border-bottom: 0.1vw solid #f1f5f9;
    }

    .variant-modal-title {
        display: flex;
        align-items: center;
        gap: 0.5vw;
        font-size: 1.1vw;
        font-weight: 800;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    .variant-modal-title iconify-icon {
        color: #ecbc42;
        font-size: 1.3vw;
    }

    .variant-modal-close {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2vw;
        height: 2vw;
        border-radius: 50%;
        border: 0.1vw solid #e2e8f0;
        background: #f8fafc;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s ease;
        padding: 0;
        flex-shrink: 0;
    }

    .variant-modal-close:hover {
        background: #fffbf0;
        border-color: #fde68a;
        color: rgb(102, 72, 9);
        transform: rotate(90deg);
    }

    .variant-modal-close iconify-icon {
        font-size: 1.15vw;
    }

    /* --------------------------------------------
       PRODUCT INFO
       -------------------------------------------- */
    .variant-product-info {
        display: flex;
        align-items: center;
        gap: 1vw;
        padding: 1vw;
        background: #fafbfc;
        border: 0.1vw solid #f1f5f9;
        border-radius: 0.8vw;
        margin-bottom: 1.2vw;
    }

    .variant-product-image-wrapper {
        width: 4.5vw;
        height: 4.5vw;
        border-radius: 0.6vw;
        overflow: hidden;
        background: #ffffff;
        border: 0.1vw solid #e2e8f0;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .variant-product-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .variant-product-details {
        flex: 1;
        min-width: 0;
    }

    .variant-product-name {
        font-size: 0.9vw;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .variant-product-price {
        display: flex;
        align-items: center;
        gap: 0.35vw;
        font-size: 0.95vw;
        font-weight: 800;
        color: rgb(102, 72, 9);
        margin-top: 0.3vw;
        line-height: 1.2;
    }

    .variant-product-stock {
        display: inline-flex;
        align-items: center;
        gap: 0.25vw;
        font-size: 0.72vw;
        color: #64748b;
        margin-top: 0.35vw;
        padding: 0.2vw 0.5vw;
        background: #ffffff;
        border: 0.1vw solid #e2e8f0;
        border-radius: 100vw;
        width: fit-content;
    }

    .variant-product-stock iconify-icon {
        font-size: 0.8vw;
        color: #ecbc42;
    }

    /* --------------------------------------------
       VARIAN OPTIONS WRAPPER
       -------------------------------------------- */
    .variant-options-wrapper {
        display: flex;
        flex-direction: column;
        gap: 1vw;
        margin-bottom: 1.2vw;
    }

    /* --------------------------------------------
       QUANTITY
       -------------------------------------------- */
    .variant-quantity-wrapper {
        display: flex;
        align-items: center;
        gap: 1vw;
        padding-top: 1vw;
        border-top: 0.1vw dashed #e2e8f0;
    }

    .variant-quantity-control {
        display: flex;
        align-items: center;
        gap: 0;
        background: #fafbfc;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.6vw;
        overflow: hidden;
        flex-shrink: 0;
    }

    .variant-qty-btn {
        width: 2.2vw;
        height: 2.2vw;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #ffffff;
        border: none;
        cursor: pointer;
        color: #475569;
        font-size: 1.15vw;
        font-weight: 700;
        transition: all 0.2s ease;
        padding: 0;
        font-family: inherit;
    }

    .variant-qty-btn:hover {
        background: #fffbf0;
        color: rgb(102, 72, 9);
    }

    .variant-qty-btn:active {
        background: #fde68a;
    }

    .variant-qty-input {
        width: 3.5vw;
        height: 2.2vw;
        border: none;
        border-left: 0.1vw solid #e2e8f0;
        border-right: 0.1vw solid #e2e8f0;
        text-align: center;
        font-size: 0.85vw;
        font-weight: 700;
        color: #0f172a;
        background: #ffffff;
        outline: none;
        font-family: inherit;
        -moz-appearance: textfield;
    }

    .variant-qty-input::-webkit-outer-spin-button,
    .variant-qty-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .variant-qty-input:focus {
        background: #fffbf0;
    }

    /* --------------------------------------------
       ADD TO CART BUTTON
       -------------------------------------------- */
    .variant-add-to-cart-btn {
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5vw;
        padding: 0.9vw 1.4vw;
        border-radius: 0.6vw;
        border: none;
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
        color: rgb(102, 72, 9);
        font-size: 0.85vw;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-family: inherit;
        box-shadow: 0 0.15vw 0.5vw rgba(236, 188, 66, 0.3);
    }

    .variant-add-to-cart-btn:hover:not(:disabled) {
        transform: translateY(-0.1vw);
        box-shadow: 0 0.35vw 1.2vw rgba(236, 188, 66, 0.5);
    }

    .variant-add-to-cart-btn:active:not(:disabled) {
        transform: translateY(0);
    }

    .variant-add-to-cart-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .variant-add-to-cart-btn iconify-icon {
        font-size: 1.1vw;
    }

    /* --------------------------------------------
       ERROR MESSAGE
       -------------------------------------------- */
    .variant-error-message {
        display: flex;
        align-items: center;
        gap: 0.4vw;
        margin-top: 0.8vw;
        padding: 0.65vw 0.9vw;
        background: #fef2f2;
        border: 0.1vw solid #fecaca;
        border-radius: 0.5vw;
        font-size: 0.75vw;
        font-weight: 500;
        color: #b91c1c;
        line-height: 1.5;
    }

    .variant-error-message.hidden {
        display: none;
    }

    .variant-error-message::before {
        content: '';
        width: 1vw;
        height: 1vw;
        flex-shrink: 0;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23b91c1c' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3ccircle cx='12' cy='12' r='10'%3e%3c/circle%3e%3cline x1='12' y1='8' x2='12' y2='12'%3e%3c/line%3e%3cline x1='12' y1='16' x2='12.01' y2='16'%3e%3c/line%3e%3c/svg%3e");
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
    }

    /* ============================================
       RESPONSIVE - TABLET
       ============================================ */
    @media (max-width: 1024px) {
        .variant-modal-overlay { padding: 2vw; }

        .variant-modal-box {
            max-width: 75vw;
            border-radius: 3vw;
            padding: 3.5vw;
        }

        .variant-modal-header {
            gap: 2vw;
            padding-bottom: 2.5vw;
            margin-bottom: 3vw;
            border-bottom-width: 0.2vw;
        }

        .variant-modal-title {
            gap: 1vw;
            font-size: 2.6vw;
        }
        .variant-modal-title iconify-icon { font-size: 3.2vw; }

        .variant-modal-close {
            width: 5vw;
            height: 5vw;
            border-width: 0.2vw;
        }
        .variant-modal-close iconify-icon { font-size: 2.9vw; }

        .variant-product-info {
            gap: 2.3vw;
            padding: 2.5vw;
            border-radius: 2vw;
            margin-bottom: 3vw;
            border-width: 0.2vw;
        }

        .variant-product-image-wrapper {
            width: 11vw;
            height: 11vw;
            border-radius: 1.6vw;
            border-width: 0.2vw;
        }

        .variant-product-name { font-size: 2.2vw; }
        .variant-product-price {
            gap: 0.9vw;
            font-size: 2.4vw;
            margin-top: 0.8vw;
        }

        .variant-product-stock {
            gap: 0.6vw;
            font-size: 1.9vw;
            margin-top: 0.9vw;
            padding: 0.5vw 1.2vw;
            border-radius: 100vw;
            border-width: 0.2vw;
        }
        .variant-product-stock iconify-icon { font-size: 2.2vw; }

        .variant-options-wrapper { gap: 2.5vw; margin-bottom: 3vw; }

        .variant-quantity-wrapper {
            gap: 2.5vw;
            padding-top: 2.5vw;
            border-top-width: 0.2vw;
        }

        .variant-quantity-control {
            border-radius: 1.5vw;
            border-width: 0.2vw;
        }

        .variant-qty-btn {
            width: 5.5vw;
            height: 5.5vw;
            font-size: 2.9vw;
        }

        .variant-qty-input {
            width: 8vw;
            height: 5.5vw;
            font-size: 2.2vw;
            border-left-width: 0.2vw;
            border-right-width: 0.2vw;
        }

        .variant-add-to-cart-btn {
            gap: 1.2vw;
            padding: 2.3vw 3.5vw;
            border-radius: 1.5vw;
            font-size: 2.2vw;
        }
        .variant-add-to-cart-btn iconify-icon { font-size: 2.8vw; }

        .variant-error-message {
            gap: 1vw;
            margin-top: 2vw;
            padding: 1.7vw 2.2vw;
            border-radius: 1.5vw;
            font-size: 1.9vw;
            border-width: 0.2vw;
        }
        .variant-error-message::before {
            width: 2.4vw;
            height: 2.4vw;
        }
    }

    /* ============================================
       RESPONSIVE - MOBILE
       ============================================ */
    @media (max-width: 480px) {
        .variant-modal-overlay {
            padding: 0;
            align-items: flex-end;
        }

        .variant-modal-box {
            max-width: 100%;
            max-height: 90vh;
            border-radius: 4vw 4vw 0 0;
            padding: 6vw 5vw 6vw;
        }

        @keyframes variantSlideUp {
            from { opacity: 0; transform: translateY(5vw); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .variant-modal-header {
            gap: 3vw;
            padding-bottom: 3.5vw;
            margin-bottom: 4.5vw;
            border-bottom-width: 0.3vw;
        }

        .variant-modal-title {
            gap: 1.5vw;
            font-size: 4.5vw;
        }
        .variant-modal-title iconify-icon { font-size: 5.2vw; }

        .variant-modal-close {
            width: 9vw;
            height: 9vw;
            border-width: 0.3vw;
        }
        .variant-modal-close iconify-icon { font-size: 5vw; }

        .variant-product-info {
            gap: 3.5vw;
            padding: 4vw;
            border-radius: 3vw;
            margin-bottom: 5vw;
            border-width: 0.3vw;
        }

        .variant-product-image-wrapper {
            width: 20vw;
            height: 20vw;
            border-radius: 2.5vw;
            border-width: 0.3vw;
        }

        .variant-product-name { font-size: 3.6vw; line-height: 1.4; }
        .variant-product-price {
            gap: 1.5vw;
            font-size: 4vw;
            margin-top: 1.3vw;
        }

        .variant-product-stock {
            gap: 1vw;
            font-size: 3vw;
            margin-top: 1.5vw;
            padding: 1vw 2.5vw;
            border-radius: 100vw;
            border-width: 0.3vw;
        }
        .variant-product-stock iconify-icon { font-size: 3.5vw; }

        .variant-options-wrapper { gap: 4.5vw; margin-bottom: 5vw; }

        .variant-quantity-wrapper {
            flex-direction: column;
            align-items: stretch;
            gap: 3.5vw;
            padding-top: 4.5vw;
            border-top-width: 0.3vw;
        }

        .variant-quantity-control {
            align-self: center;
            border-radius: 2.5vw;
            border-width: 0.3vw;
        }

        .variant-qty-btn {
            width: 10vw;
            height: 10vw;
            font-size: 5.5vw;
        }

        .variant-qty-input {
            width: 15vw;
            height: 10vw;
            font-size: 4vw;
            border-left-width: 0.3vw;
            border-right-width: 0.3vw;
        }

        .variant-add-to-cart-btn {
            gap: 2vw;
            padding: 4vw 5vw;
            border-radius: 3vw;
            font-size: 3.6vw;
        }
        .variant-add-to-cart-btn iconify-icon { font-size: 4.4vw; }

        .variant-error-message {
            gap: 1.8vw;
            margin-top: 3.5vw;
            padding: 3vw 3.5vw;
            border-radius: 2.5vw;
            font-size: 3.2vw;
            border-width: 0.3vw;
            line-height: 1.5;
        }
        .variant-error-message::before {
            width: 4vw;
            height: 4vw;
        }
    }
</style>