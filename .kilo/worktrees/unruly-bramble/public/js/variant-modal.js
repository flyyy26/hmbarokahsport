// ============================================
// VARIANT MODAL - DENGAN DISKON PER PRODUK
// ============================================

document.addEventListener('DOMContentLoaded', function() {

    const modal = document.getElementById('variant-modal');
    const modalProductId = document.getElementById('modal-product-id');
    const modalProductName = document.getElementById('modal-product-name');
    const modalProductPrice = document.getElementById('modal-product-price');
    const modalProductImage = document.getElementById('modal-product-image');
    const modalProductStock = document.getElementById('modal-product-stock');
    const modalVariantOptions = document.getElementById('modal-variant-options');
    const modalSelectedVariant = document.getElementById('modal-selected-variant');
    const modalQtyInput = document.getElementById('modal-qty-input');
    const modalAddToCartBtn = document.getElementById('modal-add-to-cart-btn');
    const modalError = document.getElementById('modal-error');
    const modalActionMode = document.getElementById('modal-action-mode');

    let selectedValues = {};
    let productVariants = [];
    let currentVariant = null;
    let currentProductId = null;
    let allOptions = [];
    let productData = null;

    // ============================================
    // OPEN MODAL
    // ============================================
    window.openVariantModal = function(productId, mode = 'add_to_cart') {
        currentProductId = productId;
        allOptions = [];
        productData = null;
        
        selectedValues = {};
        modalSelectedVariant.value = '';
        modalQtyInput.value = 1;
        modalError.classList.add('hidden');
        modalAddToCartBtn.disabled = true;
        modalActionMode.value = mode;

        modalAddToCartBtn.textContent = mode === 'buy_now' ? 'Beli Sekarang' : 'Tambah ke Keranjang';

        modalVariantOptions.innerHTML = `
            <div class="variant-loading">
                <div class="variant-spinner"></div>
                <p>Memuat varian...</p>
            </div>
        `;

        modal.classList.remove('hidden');
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';

        fetch(`/api/products/${productId}/variants`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                throw new Error(data.message || 'Gagal memuat data');
            }

            console.log('🔍 API Response:', data);

            productVariants = data.variants || [];
            allOptions = data.options || [];
            productData = data.product || {};

            modalProductId.value = productId;
            modalProductName.textContent = productData.name || '';
            
            // 🔥 HITUNG HARGA TERMURAH (TERMASUK DISKON PRODUK)
            const prices = productVariants.map(v => v.effective_price ?? v.discount_price ?? v.price);
            const minPrice = prices.length > 0 ? Math.min(...prices) : 0;
            const totalStock = productVariants.reduce((sum, v) => sum + v.stock, 0);
            
            // 🔥 TAMPILKAN HARGA DENGAN DISKON PRODUK
            updateModalPriceWithProductDiscount(minPrice, totalStock);

            if (productData.image) {
                modalProductImage.src = productData.image;
            }

            if (!allOptions || allOptions.length === 0) {
                modalVariantOptions.innerHTML = `
                    <div class="variant-empty-state">Produk ini tidak memiliki varian.</div>
                `;
                return;
            }

            renderOptions(allOptions, productVariants);
        })
        .catch(error => {
            console.error('Error loading variants:', error);
            modalVariantOptions.innerHTML = `
                <div class="variant-empty-state error">
                    ❌ ${error.message}
                    <br>
                    <button onclick="openVariantModal(${productId}, '${mode}')" class="variant-retry-btn">Coba lagi</button>
                </div>
            `;
        });
    };

    // ============================================
    // 🔥 UPDATE MODAL PRICE - HANYA TAMPILKAN DISKON PRODUK
    // ============================================
    function updateModalPriceWithProductDiscount(price, stock) {
        const priceEl = document.getElementById('modal-product-price');
        const stockEl = document.getElementById('modal-product-stock');
        
        if (!priceEl) return;
        
        // 🔥 AMBIL DATA DISKON DARI productData
        const hasProductDiscount = productData?.has_product_discount || false;
        const productDiscountPercent = productData?.product_discount_percent || 0;
        
        // 🔥 HITUNG HARGA ASLI TERMURAH
        let minOriginalPrice = Infinity;
        productVariants.forEach(v => {
            if (v.price < minOriginalPrice) {
                minOriginalPrice = v.price;
            }
        });
        
        // 🔥 HITUNG HARGA EFEKTIF TERMURAH (SUDAH TERMASUK DISKON)
        let minEffectivePrice = Infinity;
        productVariants.forEach(v => {
            const effPrice = v.effective_price ?? v.discount_price ?? v.price;
            if (effPrice < minEffectivePrice) {
                minEffectivePrice = effPrice;
            }
        });
        
        // 🔥 CEK APAKAH ADA DISKON DARI VARIAN
        const hasVariantDiscount = productVariants.some(v => 
            (v.discount_price && v.discount_price < v.price) || 
            (v.effective_price && v.effective_price < v.price)
        );
        
        const hasDiscount = hasVariantDiscount || hasProductDiscount;
        
        if (hasDiscount && minOriginalPrice !== Infinity) {
            // 🔥 ADA DISKON - TAMPILKAN HARGA CORET DAN HARGA DISKON
            // 🔥 HANYA TAMPILKAN BADGE DISKON PRODUK (30%) SAJA
            let productBadge = '';
            
            // 🔥 BADGE DISKON PRODUK (GLOBAL) - PRIORITAS UTAMA
            if (hasProductDiscount && productDiscountPercent > 0) {
                productBadge = `<span class="modal-product-discount-badge">Diskon Produk ${Math.round(productDiscountPercent)}%</span>`;
            }
            
            // 🔥 JIKA TIDAK ADA DISKON PRODUK, TAMPILKAN DISKON VARIAN
            // TAPI KALAU ADA DISKON PRODUK, TAMPILKAN ITU SAJA
            let variantBadge = '';
            if (!hasProductDiscount && hasVariantDiscount) {
                const maxDiscount = productVariants.reduce((max, v) => {
                    const d = v.discount_percent || 0;
                    return d > max ? d : max;
                }, 0);
                if (maxDiscount > 0) {
                    variantBadge = `<span class="modal-discount-badge">Diskon ${Math.round(maxDiscount)}%</span>`;
                }
            }
            
            // 🔥 GABUNGKAN BADGE (PRIORITAS DISKON PRODUK)
            const finalBadge = productBadge || variantBadge;
            
            priceEl.innerHTML = `
                <div class="modal-price-wrapper">
                    <span class="modal-price-current discounted">Rp ${formatRupiah(minEffectivePrice)}</span>
                    <span class="modal-price-original">Rp ${formatRupiah(minOriginalPrice)}</span>
                    ${finalBadge}
                </div>
            `;
        } else {
            // TIDAK ADA DISKON
            priceEl.innerHTML = `
                <span class="modal-price-current">Rp ${formatRupiah(price)}</span>
            `;
        }
        
        if (stockEl) {
            stockEl.textContent = 'Stok: ' + (stock || 0);
        }
    }

    // ============================================
    // CLOSE MODAL
    // ============================================
    window.closeVariantModal = function(event) {
        if (event && event.target !== event.currentTarget) return;
        modal.classList.remove('active');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        selectedValues = {};
        productVariants = [];
        currentVariant = null;
        productData = null;
    };

    // ============================================
    // RENDER OPTIONS
    // ============================================
    function renderOptions(options, variants) {
        modalVariantOptions.innerHTML = '';

        options.forEach(option => {
            const group = document.createElement('div');
            group.className = 'variant-option-group';

            let valuesHtml = '';
            
            option.values.forEach(value => {
                const hasStock = variants.some(v => 
                    v.values && v.values.includes(value.id) && v.stock > 0
                );
                
                valuesHtml += `
                    <button type="button"
                        class="variant-option-btn ${hasStock ? '' : 'disabled'}"
                        data-option-id="${option.id}"
                        data-value-id="${value.id}"
                        data-image="${value.image || ''}"
                        ${!hasStock ? 'disabled' : ''}>
                        ${value.value}
                    </button>
                `;
            });

            group.innerHTML = `
                <label class="variant-option-label">${option.name}</label>
                <div class="variant-option-values" data-option-id="${option.id}">
                    ${valuesHtml}
                </div>
            `;

            modalVariantOptions.appendChild(group);
        });

        autoSelectInitialOptions(variants);
        updateOptionAvailability(variants);
        refreshActiveButtons();
        checkSelection(variants);
        attachVariantButtonListeners();
    }

    // ============================================
    // ATTACH VARIANT BUTTON LISTENERS
    // ============================================
    function attachVariantButtonListeners() {
        const buttons = document.querySelectorAll('.variant-option-btn:not(.disabled)');
        buttons.forEach(function(btn) {
            btn.removeEventListener('click', handleVariantClick);
            btn.addEventListener('click', handleVariantClick);
        });
    }

    // ============================================
    // HANDLE VARIANT CLICK
    // ============================================
    function handleVariantClick(e) {
        const btn = e.currentTarget;
        if (btn.disabled) return;

        const optionId = btn.dataset.optionId;
        const valueId = parseInt(btn.dataset.valueId);
        const group = btn.closest('.variant-option-values');
        
        if (!group || !optionId) return;

        group.querySelectorAll('.variant-option-btn').forEach(b => {
            b.classList.remove('active');
        });

        btn.classList.add('active');
        selectedValues[optionId] = valueId;

        updateOptionAvailability(productVariants);
        refreshActiveButtons();

        const image = btn.dataset.image;
        if (image) {
            modalProductImage.src = image;
        }

        checkSelection(productVariants);
    }

    // ============================================
    // AUTO SELECT INITIAL OPTIONS
    // ============================================
    function autoSelectInitialOptions(variants) {
        const availableVariant = variants.find(v => v.stock > 0);

        if (availableVariant && availableVariant.values) {
            const groups = document.querySelectorAll('.variant-option-values');
            
            groups.forEach(group => {
                const optionId = group.dataset.optionId;
                const buttons = group.querySelectorAll('.variant-option-btn');
                
                let found = false;
                buttons.forEach(btn => {
                    const valueId = parseInt(btn.dataset.valueId);
                    if (availableVariant.values.includes(valueId) && !btn.disabled) {
                        selectedValues[optionId] = valueId;
                        btn.classList.add('active');
                        found = true;
                    }
                });
                
                if (!found) {
                    const firstValid = group.querySelector('.variant-option-btn:not([disabled])');
                    if (firstValid) {
                        const valId = parseInt(firstValid.dataset.valueId);
                        selectedValues[optionId] = valId;
                        firstValid.classList.add('active');
                    }
                }
            });
        } else {
            const groups = document.querySelectorAll('.variant-option-values');
            groups.forEach(group => {
                const optionId = group.dataset.optionId;
                const firstValid = group.querySelector('.variant-option-btn:not([disabled])');
                if (firstValid) {
                    const valId = parseInt(firstValid.dataset.valueId);
                    selectedValues[optionId] = valId;
                    firstValid.classList.add('active');
                }
            });
        }
    }

    // ============================================
    // REFRESH ACTIVE BUTTONS
    // ============================================
    function refreshActiveButtons() {
        document.querySelectorAll('.variant-option-btn').forEach(btn => {
            const optionId = btn.dataset.optionId;
            const valueId = parseInt(btn.dataset.valueId);

            if (selectedValues[optionId] === valueId && !btn.disabled) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }

    // ============================================
    // UPDATE OPTION AVAILABILITY
    // ============================================
    function updateOptionAvailability(variants) {
        const groups = document.querySelectorAll('.variant-option-values');

        groups.forEach(group => {
            const optionId = group.dataset.optionId;
            const buttons = group.querySelectorAll('.variant-option-btn');

            buttons.forEach(btn => {
                const valueId = parseInt(btn.dataset.valueId);

                let isAvailable = false;
                
                if (!selectedValues[optionId]) {
                    isAvailable = variants.some(v => 
                        v.values && v.values.includes(valueId) && v.stock > 0
                    );
                } else {
                    isAvailable = variants.some(variant => {
                        if (!variant.values || variant.stock <= 0) return false;
                        if (!variant.values.includes(valueId)) return false;

                        for (const [otherOptId, otherValId] of Object.entries(selectedValues)) {
                            if (otherOptId !== optionId && !variant.values.includes(otherValId)) {
                                return false;
                            }
                        }
                        return true;
                    });
                }

                btn.disabled = !isAvailable;
                btn.classList.toggle('disabled', !isAvailable);

                if (!isAvailable && selectedValues[optionId] === valueId) {
                    delete selectedValues[optionId];
                }
            });

            if (!selectedValues[optionId]) {
                const firstValid = group.querySelector('.variant-option-btn:not([disabled])');
                if (firstValid) {
                    selectedValues[optionId] = parseInt(firstValid.dataset.valueId);
                    firstValid.classList.add('active');
                }
            }
        });
        
        attachVariantButtonListeners();
    }

    // ============================================
    // 🔥 CHECK SELECTION - UPDATE HARGA DENGAN DISKON PRODUK
    // ============================================
    function checkSelection(variants) {
        const totalOptions = document.querySelectorAll('.variant-option-values').length;
        const selectedCount = Object.keys(selectedValues).length;

        if (selectedCount < totalOptions) {
            modalAddToCartBtn.disabled = true;
            modalAddToCartBtn.textContent = modalActionMode.value === 'buy_now' ? 'Pilih Varian' : 'Pilih Varian';
            return;
        }

        const selectedIds = Object.values(selectedValues).map(Number).sort();

        const matched = variants.find(v => {
            if (!v.values) return false;
            const ids = v.values.map(Number).sort();
            return JSON.stringify(ids) === JSON.stringify(selectedIds) && v.stock > 0;
        });

        if (matched) {
            currentVariant = matched;
            modalSelectedVariant.value = matched.id;
            
            // 🔥 GUNAKAN EFFECTIVE_PRICE (SUDAH TERMASUK DISKON PRODUK)
            const price = matched.effective_price ?? matched.discount_price ?? matched.price;
            const originalPrice = matched.price;
            const hasDiscount = price < originalPrice;
            const discountPercent = matched.discount_percent || 0;
            
            // 🔥 CEK APAKAH ADA DISKON PRODUK (dari data varian)
            const hasProductDiscount = matched.has_product_discount || false;
            const productDiscountPercent = matched.product_discount_percent || 0;
            
            // 🔥 UPDATE HARGA DI MODAL
            const priceEl = document.getElementById('modal-product-price');
            if (priceEl) {
                if (hasDiscount || hasProductDiscount) {
                    // 🔥 HANYA TAMPILKAN BADGE DISKON PRODUK (30%)
                    let productBadge = '';
                    let variantBadge = '';
                    
                    // 🔥 PRIORITAS DISKON PRODUK
                    if (hasProductDiscount && productDiscountPercent > 0) {
                        productBadge = `<span class="modal-product-discount-badge"> Diskon Produk ${Math.round(productDiscountPercent)}%</span>`;
                    }
                    
                    // 🔥 JIKA TIDAK ADA DISKON PRODUK, TAMPILKAN DISKON VARIAN
                    if (!hasProductDiscount && hasDiscount && discountPercent > 0) {
                        variantBadge = `<span class="modal-discount-badge">Diskon ${Math.round(discountPercent)}%</span>`;
                    }
                    
                    const finalBadge = productBadge || variantBadge;
                    
                    priceEl.innerHTML = `
                        <div class="modal-price-wrapper">
                            <span class="modal-price-current discounted">Rp ${formatRupiah(price)}</span>
                            <span class="modal-price-original">Rp ${formatRupiah(originalPrice)}</span>
                            ${finalBadge}
                        </div>
                    `;
                } else {
                    priceEl.innerHTML = `
                        <span class="modal-price-current">Rp ${formatRupiah(price)}</span>
                    `;
                }
            }
            
            modalProductStock.textContent = 'Stok: ' + matched.stock;
            modalQtyInput.max = matched.stock;
            if (parseInt(modalQtyInput.value) > matched.stock) {
                modalQtyInput.value = matched.stock;
            }
            modalAddToCartBtn.disabled = false;
            modalAddToCartBtn.textContent = modalActionMode.value === 'buy_now' ? 'Beli Sekarang' : 'Tambah ke Keranjang';
            modalError.classList.add('hidden');
        } else {
            currentVariant = null;
            modalSelectedVariant.value = '';
            modalAddToCartBtn.disabled = true;
            modalAddToCartBtn.textContent = modalActionMode.value === 'buy_now' ? 'Stok Habis' : 'Stok Habis';
            
            const hasVariant = variants.some(v => {
                if (!v.values) return false;
                const ids = v.values.map(Number).sort();
                return JSON.stringify(ids) === JSON.stringify(selectedIds);
            });

            if (hasVariant) {
                modalError.textContent = '⚠️ Stok habis untuk kombinasi ini.';
            } else {
                modalError.textContent = '⚠️ Kombinasi tidak tersedia.';
            }
            modalError.classList.remove('hidden');
        }
    }

    // ============================================
    // QUANTITY BUTTONS
    // ============================================
    document.querySelectorAll('.variant-qty-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            let value = parseInt(modalQtyInput.value) || 1;
            const max = parseInt(modalQtyInput.max) || 999;

            if (this.dataset.action === 'increase' && value < max) {
                value++;
            } else if (this.dataset.action === 'decrease' && value > 1) {
                value--;
            }
            modalQtyInput.value = value;
        });
    });

    // ============================================
    // ADD TO CART FROM MODAL
    // ============================================
    modalAddToCartBtn.addEventListener('click', function() {
        const variantId = modalSelectedVariant.value;
        const productId = modalProductId.value;
        const quantity = parseInt(modalQtyInput.value) || 1;
        const mode = modalActionMode.value;

        if (!variantId) {
            modalError.textContent = '⚠️ Pilih varian terlebih dahulu!';
            modalError.classList.remove('hidden');
            return;
        }

        const variant = productVariants.find(v => v.id == variantId);
        if (variant && quantity > variant.stock) {
            modalError.textContent = '⚠️ Stok tidak mencukupi! Tersedia: ' + variant.stock;
            modalError.classList.remove('hidden');
            return;
        }

        if (typeof window.addToCartFromVariant === 'function') {
            window.addToCartFromVariant(productId, variantId, quantity, mode);
        } else {
            console.error('addToCartFromVariant tidak tersedia');
            modalError.textContent = 'Terjadi kesalahan. Silakan refresh halaman.';
            modalError.classList.remove('hidden');
        }
    });

    // ============================================
    // ESC CLOSE
    // ============================================
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeVariantModal();
        }
    });

    // ============================================
    // CLICK OUTSIDE
    // ============================================
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeVariantModal();
        }
    });

});

// ============================================
// HELPER: FORMAT RUPIAH
// ============================================
function formatRupiah(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}