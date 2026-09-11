// ============================================
// CART FUNCTIONS - LENGKAP & TERSTRUKTUR
// ============================================

/**
 * Tambah ke Keranjang - Cek varian dulu
 */
function addToCart(productId) {
    console.log('🛒 addToCart called for product:', productId);
    
    // 🔥 LANGSUNG CEK VARIAN PRODUK
    fetch(`/api/products/${productId}/variants`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.variants && data.variants.length > 0) {
            // 🔥 ADA VARIAN → BUKA MODAL (TANPA CEK LOGIN)
            if (typeof openVariantModal === 'function') {
                openVariantModal(productId, 'add_to_cart');
            } else {
                console.error('openVariantModal tidak tersedia');
                showToast('Terjadi kesalahan', 'error');
            }
        } else {
            // 🔥 TIDAK ADA VARIAN → LANGSUNG TAMBAH KE CART
            // TAPI CEK LOGIN DULU
            checkLoginStatus().then(isLoggedIn => {
                if (!isLoggedIn) {
                    window._pendingProductId = productId;
                    openLoginPopup('add_to_cart', function() {
                        if (window._pendingProductId) {
                            addToCartDirect(window._pendingProductId);
                            window._pendingProductId = null;
                        }
                    });
                } else {
                    addToCartDirect(productId);
                }
            });
        }
    })
    .catch(error => {
        console.error('Error checking variants:', error);
        // Fallback: coba langsung
        checkLoginStatus().then(isLoggedIn => {
            if (!isLoggedIn) {
                window._pendingProductId = productId;
                openLoginPopup('add_to_cart', function() {
                    if (window._pendingProductId) {
                        addToCartDirect(window._pendingProductId);
                        window._pendingProductId = null;
                    }
                });
            } else {
                addToCartDirect(productId);
            }
        });
    });
}

function addToCartFromVariant(productId, variantId, quantity, mode) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    
    // 🔥 JIKA MODE BUY_NOW, PAKAI ROUTE BUY_NOW
    const url = mode === 'buy_now' ? window.customerRoutes.buyNow : window.customerRoutes.cartAdd;

    // 🔥 TAMPILKAN LOADING DI TOMBOL
    const btn = document.getElementById('modal-add-to-cart-btn');
    if (btn) {
        btn.disabled = true;
        btn.textContent = 'Memproses...';
    }

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId,
            variant_id: variantId,
            quantity: quantity
        })
    })
    .then(response => {
        // 🔥 CEK 401 UNAUTHORIZED
        if (response.status === 401) {
            // 🔥 BUKA POPUP LOGIN
            window._pendingProductId = productId;
            window._pendingVariantId = variantId;
            window._pendingQuantity = quantity;
            window._pendingMode = mode;
            
            // 🔥 TUTUP MODAL VARIAN
            closeVariantModal();
            
            // 🔥 BUKA POPUP LOGIN
            openLoginPopup('add_to_cart', function() {
                console.log('✅ Login success, reopening variant modal');
                // 🔥 BUKA ULANG MODAL VARIAN
                setTimeout(function() {
                    if (window._pendingProductId) {
                        openVariantModal(window._pendingProductId, window._pendingMode || 'add_to_cart');
                        window._pendingProductId = null;
                        window._pendingVariantId = null;
                        window._pendingQuantity = null;
                        window._pendingMode = null;
                    }
                }, 400);
            });
            throw new Error('Unauthorized');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            if (mode === 'buy_now') {
                showToast('Mengarahkan ke checkout...', 'success');
                closeVariantModal();
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 500);
            } else {
                updateNavbarCartCount(data.count);
                document.dispatchEvent(new CustomEvent('cart-updated', {
                    detail: { count: data.count, message: data.message }
                }));
                showToast(data.message || 'Produk ditambahkan ke keranjang!', 'success');
                if (typeof window.loadCartPopup === 'function') {
                    window.loadCartPopup();
                }
                closeVariantModal();
            }
        } else {
            // Tampilkan error di modal
            const modalError = document.getElementById('modal-error');
            if (modalError) {
                modalError.textContent = data.message || 'Gagal memproses.';
                modalError.classList.remove('hidden');
            }
            // Reset button
            if (btn) {
                btn.disabled = false;
                btn.textContent = mode === 'buy_now' ? 'Beli Sekarang' : 'Tambah ke Keranjang';
            }
        }
    })
    .catch(error => {
        if (error.message !== 'Unauthorized') {
            console.error('Error:', error);
            const modalError = document.getElementById('modal-error');
            if (modalError) {
                modalError.textContent = 'Terjadi kesalahan. Silakan coba lagi.';
                modalError.classList.remove('hidden');
            }
            // Reset button
            if (btn) {
                btn.disabled = false;
                btn.textContent = mode === 'buy_now' ? 'Beli Sekarang' : 'Tambah ke Keranjang';
            }
        }
    });
}

function checkLoginStatus() {
    return fetch(window.customerRoutes.cartCount, {
        headers: { 'Accept': 'application/json' }
    })
    .then(response => {
        // Jika response 401, berarti belum login
        if (response.status === 401) {
            return false;
        }
        return response.json().then(data => {
            // Jika bisa dapat count, berarti login
            return data.is_logged_in === true;
        });
    })
    .catch(() => {
        return false;
    });
}

function updateNavbarCartCount(count) {
    console.log('🛒 Updating navbar cart count to:', count);
    
    const finalCount = parseInt(count) || 0;
    
    const cartCountElements = document.querySelectorAll('#cart-count');
    cartCountElements.forEach(function(element) {
        element.textContent = finalCount;
        
        if (finalCount > 0) {
            element.style.display = 'inline-flex';
            element.style.visibility = 'visible';
            element.classList.remove('hidden');
        } else {
            element.style.display = 'none';
            element.style.visibility = 'hidden';
            element.classList.add('hidden');
        }
    });
    
    document.querySelectorAll('.cart-count, .cart-badge, .cart-counter').forEach(function(el) {
        el.textContent = finalCount;
        if (finalCount > 0) {
            el.style.display = 'inline-flex';
            el.classList.remove('hidden');
        } else {
            el.style.display = 'none';
            el.classList.add('hidden');
        }
    });
}


document.addEventListener('cart-updated', function(e) {
    console.log('🛒 Cart updated event received:', e.detail);
    if (e.detail && e.detail.count !== undefined) {
        updateNavbarCartCount(e.detail.count);
    } else {
        loadCartCount(); // Reload from server
    }
});

document.addEventListener('wishlist-updated', function(e) {
    console.log('❤️ Wishlist updated event received:', e.detail);
    if (e.detail && e.detail.count !== undefined) {
        updateNavbarWishlistCount(e.detail.count);
    } else {
        loadWishlistCount(); // Reload from server
    }
});

function updateNavbarWishlistCount(count) {
    console.log('❤️ Updating navbar wishlist count to:', count);
    
    const finalCount = parseInt(count) || 0;
    
    const wishlistCountElements = document.querySelectorAll('#wishlist-count');
    wishlistCountElements.forEach(function(element) {
        element.textContent = finalCount;
        
        if (finalCount > 0) {
            element.style.display = 'inline-flex';
            element.style.visibility = 'visible';
            element.classList.remove('hidden');
        } else {
            element.style.display = 'none';
            element.style.visibility = 'hidden';
            element.classList.add('hidden');
        }
    });
    
    document.querySelectorAll('.wishlist-count, .wishlist-badge, .wishlist-counter').forEach(function(el) {
        el.textContent = finalCount;
        if (finalCount > 0) {
            el.style.display = 'inline-flex';
            el.classList.remove('hidden');
        } else {
            el.style.display = 'none';
            el.classList.add('hidden');
        }
    });
}

/**
 * Tambah ke Keranjang - Langsung (tanpa varian)
 */
function addToCartDirect(productId, variantId = null, quantity = 1) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    
    const btn = document.querySelector(`.product_layout_box[data-product-id="${productId}"] .add_to_cart_btn`);
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '⏳';
    }
    
    fetch(window.customerRoutes.cartAdd, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId,
            variant_id: variantId,
            quantity: quantity
        })
    })
    .then(response => {
        // 🔥 CEK 401 UNAUTHORIZED
        if (response.status === 401) {
            // BUKA POPUP LOGIN
            window._pendingProductId = productId;
            openLoginPopup('add_to_cart', function() {
                if (window._pendingProductId) {
                    addToCartDirect(window._pendingProductId);
                    window._pendingProductId = null;
                }
            });
            throw new Error('Unauthorized');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const count = data.count || 0;
            console.log('✅ Cart add success, count:', count);
            
            updateNavbarCartCount(count);
            
            document.dispatchEvent(new CustomEvent('cart-updated', {
                detail: { count: count, message: data.message }
            }));
            
            showToast(data.message || 'Produk ditambahkan ke keranjang!', 'success');
            
            if (typeof loadCartPopup === 'function') {
                loadCartPopup();
            }
        } else {
            showToast(data.message || 'Gagal menambahkan produk', 'error');
        }
    })
    .catch(error => {
        if (error.message !== 'Unauthorized') {
            console.error('Error:', error);
            showToast('Terjadi kesalahan', 'error');
        }
    })
    .finally(() => {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<iconify-icon icon="solar:cart-linear"></iconify-icon>';
        }
    });
}


/**
 * Beli Sekarang
 */
function buyNow(productId) {
    fetch(`/api/products/${productId}/variants`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.variants && data.variants.length > 0) {
            if (typeof openVariantModal === 'function') {
                window._buyNowMode = true;
                openVariantModal(productId, 'buy_now');
            } else {
                buyNowDirect(productId);
            }
        } else {
            buyNowDirect(productId);
        }
    })
    .catch(() => {
        buyNowDirect(productId);
    });
}

function buyNowDirect(productId, variantId = null, quantity = 1) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    
    const btn = document.querySelector(`.product_layout_box[data-product-id="${productId}"] .buy_now_btn`);
    if (btn) {
        btn.disabled = true;
        btn.textContent = '⏳';
    }
    
    fetch(window.customerRoutes.buyNow, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId,
            variant_id: variantId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Mengarahkan ke checkout...', 'success');
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 500);
        } else {
            showToast(data.message || 'Gagal memproses', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan', 'error');
    })
    .finally(() => {
        if (btn) {
            btn.disabled = false;
            btn.textContent = 'BELI SEKARANG';
        }
    });
}

/**
 * Tambah ke Wishlist
 */
function addToWishlist(productId) {
    console.log('❤️ addToWishlist called for product:', productId);
    
    // 🔥 CEK STATUS LOGIN
    checkLoginStatus().then(isLoggedIn => {
        if (!isLoggedIn) {
            // 🔥 BUKA POPUP LOGIN
            console.log('❌ User not logged in, showing login popup');
            window._pendingProductId = productId;
            openLoginPopup('add_to_wishlist', function() {
                console.log('✅ Login success callback for wishlist');
                if (window._pendingProductId) {
                    addToWishlistDirect(window._pendingProductId);
                    window._pendingProductId = null;
                }
            });
            return;
        }
        
        console.log('✅ User logged in, adding to wishlist');
        addToWishlistDirect(productId);
    });
}

function addToWishlistDirect(productId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    
    const allButtons = document.querySelectorAll(`.add_to_wishlist_btn[data-product-id="${productId}"]`);
    allButtons.forEach(function(btn) {
        btn.disabled = true;
        btn.innerHTML = '⏳';
    });
    
    fetch(window.customerRoutes.wishlistAdd, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId
        })
    })
    .then(response => {
        // 🔥 CEK 401 UNAUTHORIZED (fallback)
        if (response.status === 401) {
            console.log('❌ Unauthorized, showing login popup');
            window._pendingProductId = productId;
            openLoginPopup('add_to_wishlist', function() {
                if (window._pendingProductId) {
                    addToWishlistDirect(window._pendingProductId);
                    window._pendingProductId = null;
                }
            });
            throw new Error('Unauthorized');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const count = data.count || 0;
            const inWishlist = data.in_wishlist || false;
            
            console.log('❤️ Wishlist toggled:', { count, inWishlist });
            
            updateWishlistIcon(productId, inWishlist);
            
            if (typeof window.updateNavbarWishlistCount === 'function') {
                window.updateNavbarWishlistCount(count);
            }
            
            document.dispatchEvent(new CustomEvent('wishlist-updated', {
                detail: { count, product_id: productId, in_wishlist: inWishlist }
            }));
            
            showToast(data.message || (inWishlist ? 'Produk ditambahkan ke wishlist!' : 'Produk dihapus dari wishlist!'), 'success');
            
            if (typeof loadWishlistPopup === 'function') {
                loadWishlistPopup();
            }
        } else {
            showToast(data.message || 'Gagal menambahkan ke wishlist', 'error');
        }
    })
    .catch(error => {
        if (error.message !== 'Unauthorized') {
            console.error('Error:', error);
            showToast('Terjadi kesalahan', 'error');
        }
    })
    .finally(() => {
        allButtons.forEach(function(btn) {
            const currentState = btn.dataset.inWishlist === 'true';
            if (currentState) {
                btn.innerHTML = '<iconify-icon icon="solar:heart-bold" style="color: #ef4444;"></iconify-icon>';
                btn.classList.add('active');
            } else {
                btn.innerHTML = '<iconify-icon icon="solar:heart-linear"></iconify-icon>';
                btn.classList.remove('active');
            }
            btn.disabled = false;
        });
    });
}

/**
 * 🔥 UPDATE CART COUNT - PASTIKAN BEKERJA
 */
function updateCartCount(count) {
    console.log('Updating cart count to:', count); // Debug
    
    // Cari semua elemen dengan id cart-count
    const cartCountElements = document.querySelectorAll('#cart-count');
    
    cartCountElements.forEach(function(element) {
        element.textContent = count || 0;
        
        if (count > 0) {
            element.style.display = 'inline-flex';
            element.style.visibility = 'visible';
        } else {
            element.style.display = 'none';
            element.style.visibility = 'hidden';
        }
    });
    
    // Juga update elemen dengan class .cart-count jika ada
    document.querySelectorAll('.cart-count, .cart-badge').forEach(function(el) {
        el.textContent = count || 0;
        if (count > 0) {
            el.style.display = 'inline-flex';
        } else {
            el.style.display = 'none';
        }
    });
}

function updateWishlistIcon(productId, inWishlist = null) {
    // Cari SEMUA tombol wishlist untuk produk ini
    const allButtons = document.querySelectorAll(
        `.add_to_wishlist_btn[data-product-id="${productId}"], ` +
        `#wishlist-toggle-product[data-product-id="${productId}"], ` +
        `#mobile-wishlist-btn[data-product-id="${productId}"]`
    );
    
    console.log(`❤️ Updating ${allButtons.length} wishlist buttons for product ${productId}`);
    
    allButtons.forEach(function(btn) {
        // Jika inWishlist tidak diberikan, cek dari data attribute
        let isInWishlist = inWishlist;
        if (isInWishlist === null) {
            isInWishlist = btn.dataset.inWishlist === 'true';
        }
        
        if (isInWishlist) {
            btn.innerHTML = '<iconify-icon icon="solar:heart-bold" style="color: #ef4444;"></iconify-icon>';
            btn.classList.add('active');
            btn.dataset.inWishlist = 'true';
        } else {
            btn.innerHTML = '<iconify-icon icon="solar:heart-linear"></iconify-icon>';
            btn.classList.remove('active');
            btn.dataset.inWishlist = 'false';
        }
        
        btn.disabled = false;
    });
}

function loadWishlistStatus() {
    // 🔥 CEK STATUS WISHLIST DARI SERVER
    fetch(window.customerRoutes.wishlistStatus, {
        headers: { 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.wishlist_ids) {
            const wishlistIds = data.wishlist_ids.map(id => parseInt(id));
            
            console.log('❤️ Wishlist status loaded:', wishlistIds);
            
            // 🔥 UPDATE SEMUA TOMBOL WISHLIST DI HALAMAN
            document.querySelectorAll('.add_to_wishlist_btn').forEach(function(btn) {
                const productId = parseInt(btn.dataset.productId);
                if (productId) {
                    const inWishlist = wishlistIds.includes(productId);
                    
                    if (inWishlist) {
                        btn.innerHTML = '<iconify-icon icon="solar:heart-bold" style="color: #ef4444;"></iconify-icon>';
                        btn.classList.add('active');
                        btn.dataset.inWishlist = 'true';
                    } else {
                        btn.innerHTML = '<iconify-icon icon="solar:heart-linear"></iconify-icon>';
                        btn.classList.remove('active');
                        btn.dataset.inWishlist = 'false';
                    }
                }
            });
            
            // 🔥 UPDATE WISHLIST COUNT
            updateNavbarWishlistCount(wishlistIds.length);
        }
    })
    .catch(error => {
        console.error('Error loading wishlist status:', error);
    });
}

/**
 * 🔥 UPDATE WISHLIST COUNT - PASTIKAN BEKERJA
 */
function updateWishlistCount(count) {
    console.log('Updating wishlist count to:', count); // Debug
    
    // Cari semua elemen dengan id wishlist-count
    const wishlistCountElements = document.querySelectorAll('#wishlist-count');
    
    wishlistCountElements.forEach(function(element) {
        element.textContent = count || 0;
        
        if (count > 0) {
            element.style.display = 'inline-flex';
            element.style.visibility = 'visible';
        } else {
            element.style.display = 'none';
            element.style.visibility = 'hidden';
        }
    });
    
    // Juga update elemen dengan class .wishlist-count jika ada
    document.querySelectorAll('.wishlist-count, .wishlist-badge').forEach(function(el) {
        el.textContent = count || 0;
        if (count > 0) {
            el.style.display = 'inline-flex';
        } else {
            el.style.display = 'none';
        }
    });
}

/**
 * Load Cart Count dari Server
 */
function loadCartCount() {
    fetch(window.customerRoutes.cartCount, {
        headers: { 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.count !== undefined) {
            updateNavbarCartCount(data.count);
        }
    })
    .catch(error => {
        console.error('Error loading cart count:', error);
    });
}

/**
 * Load Wishlist Count dari Server
 */
function loadWishlistCount() {
    if (window.customerRoutes.wishlistPopup) {
        fetch(window.customerRoutes.wishlistPopup, {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.count !== undefined) {
                updateNavbarWishlistCount(data.count);
            }
        })
        .catch(error => {
            console.error('Error loading wishlist count:', error);
        });
    }
}

/**
 * Show Toast Notification
 */
function showToast(message, type = 'info') {
    // Hapus toast lama
    const oldToast = document.querySelector('.custom-toast');
    if (oldToast) {
        oldToast.remove();
    }
    
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
    
    // Auto close
    setTimeout(() => {
        toast.classList.add('hide');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
    
    // Close button
    toast.querySelector('.custom-toast-close').addEventListener('click', function() {
        toast.classList.add('hide');
        setTimeout(() => toast.remove(), 300);
    });
}

function removeFromWishlist(productId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    
    fetch(window.customerRoutes.wishlistRemove, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateNavbarWishlistCount(data.count || 0);
            showToast(data.message || 'Produk dihapus dari wishlist', 'success');
            
            if (typeof loadWishlistPopup === 'function') {
                loadWishlistPopup();
            }
        } else {
            showToast(data.message || 'Gagal menghapus dari wishlist', 'error');
        }
    })
    .catch(() => {
        showToast('Terjadi kesalahan', 'error');
    });
}

// Load count saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, loading counts...');
    loadCartCount();
    loadWishlistCount();
});

// Expose ke global
window.addToCart = addToCart;
window.addToCartDirect = addToCartDirect;
// 🔥 HANYA definisikan buyNow jika belum ada. Halaman detail produk (products/show)
// punya buyNow() sendiri yang menggunakan varian terpilih di form - JANGAN ditimpa.
if (typeof window.buyNow !== 'function') {
    window.buyNow = buyNow;
}
window.addToWishlist = addToWishlist;
window.removeFromWishlist = removeFromWishlist;
window.updateNavbarCartCount = updateNavbarCartCount;
window.updateNavbarWishlistCount = updateNavbarWishlistCount;
window.loadCartCount = loadCartCount;
window.loadWishlistCount = loadWishlistCount;
window.showToast = showToast;
window.updateCartCount = updateNavbarCartCount;
window.updateWishlistIcon = updateWishlistIcon;
window.updateAllWishlistButtons = updateWishlistIcon;

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, loading counts...');
    loadCartCount();
    loadWishlistCount();
});