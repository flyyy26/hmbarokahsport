{{-- Modal Pilih Varian --}}
<div id="variant-modal" class="variant-modal-overlay hidden" onclick="closeVariantModal(event)">
    <div class="variant-modal-box" onclick="event.stopPropagation()">
        {{-- Header --}}
        <div class="variant-modal-header">
            <h3 class="variant-modal-title">Pilih Varian</h3>
            <button type="button" onclick="closeVariantModal()" class="variant-modal-close">
                <svg class="variant-modal-close-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
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
                <button type="button" class="variant-qty-btn" data-action="decrease">−</button>
                <input type="number" id="modal-qty-input" value="1" min="1" max="999" class="variant-qty-input">
                <button type="button" class="variant-qty-btn" data-action="increase">+</button>
            </div>

            <button type="button" id="modal-add-to-cart-btn" class="variant-add-to-cart-btn">
                Tambah ke Keranjang
            </button>
        </div>

        {{-- Error Message --}}
        <p id="modal-error" class="variant-error-message hidden"></p>
    </div>
</div>