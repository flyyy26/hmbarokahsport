// ============================================
// POPUP.JS - Full Script for Cart & Wishlist
// ============================================

$(document).ready(function() {

    // ============================================
    // TOAST SYSTEM
    // ============================================
    
    /**
     * Show Toast Notification
     * @param {string} message - Pesan yang akan ditampilkan
     * @param {string} type - Jenis toast: success, error, warning, info
     */

    $(document).on('click', '.popup-footer-buttons .btn-primary, #checkout-popup-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('🛒 Checkout button clicked from popup');
        
        // 🔥 AMBIL COUNT DARI ELEMEN
        var cartCount = parseInt($('#cart-count').text()) || 0;
        
        if (cartCount === 0) {
            showToast('Keranjang kosong. Tambahkan produk terlebih dahulu.', 'warning');
            return;
        }
        
        // 🔥 TUTUP POPUP
        $('#cart-popup').removeClass('active');
        $('body').removeClass('popup-open');
        
        // 🔥 REDIRECT KE CHECKOUT
        if (window.customerRoutes && window.customerRoutes.checkout) {
            window.location.href = window.customerRoutes.checkout;
        } else {
            window.location.href = '{{ route("customer.checkout.index") }}';
        }
    });

    function showToast(message, type = 'info') {
        var container = $('#toast-container');
        
        // If container doesn't exist, create it
        if (container.length === 0) {
            $('body').append('<div id="toast-container" class="toast-container"></div>');
            container = $('#toast-container');
        }
        
        var colors = {
            success: 'toast-success',
            error: 'toast-error',
            warning: 'toast-warning',
            info: 'toast-info'
        };
        
        var icons = {
            success: '✅',
            error: '❌',
            warning: '⚠️',
            info: 'ℹ️'
        };

        var toast = $('<div>')
            .addClass('toast ' + (colors[type] || 'toast-info'))
            .html(`
                <span>${icons[type] || ''}</span>
                <span>${message}</span>
                <span class="toast-close">&times;</span>
            `);

        container.append(toast);

        // Auto close after 3 seconds
        var timer = setTimeout(function() {
            closeToast(toast);
        }, 3000);

        // Close button
        toast.find('.toast-close').on('click', function() {
            clearTimeout(timer);
            closeToast(toast);
        });

        // Hover pause
        toast.on('mouseenter', function() {
            clearTimeout(timer);
        });

        toast.on('mouseleave', function() {
            timer = setTimeout(function() {
                closeToast(toast);
            }, 1500);
        });
    }

    /**
     * Close Toast with animation
     */
    function closeToast(toast) {
        toast.addClass('hide');
        setTimeout(function() {
            toast.remove();
        }, 300);
    }


    // ============================================
    // CART FUNCTIONS
    // ============================================

    function updateCartCountFallback(count) {
        $('#cart-count').add('.cart-count, .cart-badge').each(function() {
            if (count > 0) {
                $(this).text(count).removeClass('hidden').show().css('display', 'inline-flex');
            } else {
                $(this).addClass('hidden').hide();
            }
        });
    }

    /**
     * Load Cart Popup Content - DENGAN SPINNER
     */
    function loadCartPopup() {
        var $content = $('#cart-content');
        
        // 🔥 TAMPILKAN SPINNER LOADING (HANYA SATU)
        $content.html(`
            <div class="popup-loading">
                <div class="popup-spinner"></div>
                <p>Memuat keranjang...</p>
            </div>
        `);
        
        $.ajax({
            url: window.customerRoutes.cartPopup,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('🛒 Cart popup response:', response);
                
                if (response.success) {
                    $content.html(response.html);
                    $('#cart-total').text('Rp ' + formatRupiah(response.total));
                    
                    // 🔥 UPDATE CART COUNT
                    if (typeof window.updateNavbarCartCount === 'function') {
                        window.updateNavbarCartCount(response.count);
                    } else {
                        updateCartCountFallback(response.count);
                    }
                    
                    toggleCartFooter(response.count > 0);
                    
                    // 🔥 Show/hide clear button berdasarkan count
                    if (response.count > 0) {
                        $('#cart-clear').addClass('visible').show();
                    } else {
                        $('#cart-clear').removeClass('visible').hide();
                    }
                }
            },
            error: function(xhr) {
                console.error('Error loading cart:', xhr);
                $content.html(`
                    <div class="popup-body-empty">
                        <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                        <p>Gagal memuat keranjang</p>
                        <p>Silakan coba lagi</p>
                        <button onclick="loadCartPopup()" class="btn-primary" style="margin-top:16px;padding:8px 24px;font-size:13px;">
                            <iconify-icon icon="mdi:refresh" width="16"></iconify-icon>
                            Coba Lagi
                        </button>
                    </div>
                `);
                showToast('Gagal memuat keranjang', 'error');
            }
        });
    }

    /**
     * Toggle Cart Footer (show/hide based on cart items)
     */
    function toggleCartFooter(hasItems) {
        if (hasItems) {
            $('#cart-footer').addClass('visible').show();
        } else {
            $('#cart-footer').removeClass('visible').hide();
        }
    }

    /**
     * Format Rupiah
     */
    function formatRupiah(amount) {
        return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    /**
     * Update Cart Count Badge
     */
    function updateCartCount(count) {
        if (typeof window.updateNavbarCartCount === 'function') {
            window.updateNavbarCartCount(count);
        } else {
            updateCartCountFallback(count);
        }
    }

    /**
     * Load Cart Count from Server
     */
    function loadCartCount() {
        if (window.customerRoutes.cartCount) {
            $.ajax({
                url: window.customerRoutes.cartCount,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.count !== undefined) {
                        updateCartCount(response.count);
                    }
                },
                error: function(xhr) {
                    console.error('Error loading cart count:', xhr);
                }
            });
        }
    }

    /**
     * Update Cart Item Quantity
     */
    function updateCartItem(key, quantity) {
        var $content = $('#cart-content');
        
        // 🔥 TAMPILKAN LOADING SAAT UPDATE
        $content.html(`
            <div class="popup-loading">
                <div class="popup-spinner"></div>
                <p>Memperbarui keranjang...</p>
            </div>
        `);
        
        $.ajax({
            url: window.customerRoutes.cartUpdate,
            method: 'PUT',
            data: {
                key: key,
                quantity: quantity,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    loadCartPopup();
                    loadCartCount();
                } else {
                    showToast(response.message || 'Gagal memperbarui keranjang', 'error');
                    loadCartPopup();
                }
            },
            error: function(xhr) {
                if (xhr.status === 400) {
                    showToast(xhr.responseJSON?.message || 'Stok tidak mencukupi', 'warning');
                } else {
                    showToast('Terjadi kesalahan, silakan coba lagi', 'error');
                }
                loadCartPopup();
            }
        });
    }

    /**
     * Remove Item from Cart
     */
    function removeCartItem(key) {
        if (!confirm('Hapus item ini dari keranjang?')) return;
        
        var $item = $('.cart-item[data-key="' + key + '"]');
        var $content = $('#cart-content');
        
        // Add removing animation
        $item.css({
            'transition': 'all 0.3s ease',
            'opacity': '1',
            'transform': 'translateX(0)'
        });
        
        setTimeout(function() {
            $item.css({
                'opacity': '0',
                'transform': 'translateX(-30px)'
            });
        }, 50);

        // 🔥 TAMPILKAN LOADING SAAT MENGHAPUS
        $content.html(`
            <div class="popup-loading">
                <div class="popup-spinner"></div>
                <p>Menghapus item...</p>
            </div>
        `);
        
        $.ajax({
            url: window.customerRoutes.cartRemove,
            method: 'DELETE',
            data: {
                key: key,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    setTimeout(function() {
                        // 🔥 HAPUS ELEMEN DARI DOM
                        $item.remove();
                        
                        // 🔥 CEK APAKAH CART KOSONG
                        if ($('.cart-item').length === 0) {
                            // Render ulang popup kosong
                            loadCartPopup();
                        } else {
                            // Reload popup untuk refresh data
                            loadCartPopup();
                        }
                        
                        // 🔥 UPDATE CART COUNT
                        if (typeof window.updateNavbarCartCount === 'function') {
                            window.updateNavbarCartCount(response.count);
                        }
                        
                        // 🔥 UPDATE CLEAR BUTTON
                        if (response.count > 0) {
                            $('#cart-clear').addClass('visible').show();
                        } else {
                            $('#cart-clear').removeClass('visible').hide();
                        }
                    }, 350);
                } else {
                    $item.css({
                        'opacity': '1',
                        'transform': 'translateX(0)'
                    });
                    showToast(response.message || 'Gagal menghapus item', 'error');
                    loadCartPopup();
                }
            },
            error: function(xhr) {
                $item.css({
                    'opacity': '1',
                    'transform': 'translateX(0)'
                });
                showToast('Terjadi kesalahan, silakan coba lagi', 'error');
                loadCartPopup();
            }
        });
    }

    /**
     * Clear All Cart Items
     */
    function clearCart() {
        // 🔥 Ambil count dari elemen #cart-count atau dari data
        var cartCount = parseInt($('#cart-count').text()) || 0;
        
        // Jika tidak ada, coba dari class .cart-count
        if (cartCount === 0) {
            cartCount = parseInt($('.cart-count').first().text()) || 0;
        }
        
        console.log('🛒 Cart count before clear:', cartCount);
        
        if (cartCount === 0) {
            showToast('Keranjang sudah kosong', 'info');
            return;
        }
        
        if (!confirm('Kosongkan semua item di keranjang?')) return;
        
        var $content = $('#cart-content');
        
        // 🔥 TAMPILKAN LOADING SAAT KOSONGKAN
        $content.html(`
            <div class="popup-loading">
                <div class="popup-spinner"></div>
                <p>Mengosongkan keranjang...</p>
            </div>
        `);
        
        $.ajax({
            url: window.customerRoutes.cartClear,
            method: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // 🔥 Update cart count ke 0
                    if (typeof window.updateNavbarCartCount === 'function') {
                        window.updateNavbarCartCount(0);
                    }
                    
                    // 🔥 Load ulang popup
                    loadCartPopup();
                    
                    // 🔥 Sembunyikan footer dan clear button
                    $('#cart-footer').removeClass('visible').hide();
                    $('#cart-clear').removeClass('visible').hide();
                } else {
                    showToast(response.message || 'Gagal mengosongkan keranjang', 'error');
                    loadCartPopup();
                }
            },
            error: function(xhr) {
                showToast('Terjadi kesalahan, silakan coba lagi', 'error');
                loadCartPopup();
            }
        });
    }


    // ============================================
    // WISHLIST FUNCTIONS
    // ============================================

    /**
     * Load Wishlist Popup Content
     */
    function loadWishlistPopup() {
        if (!window.customerRoutes.wishlistPopup) {
            console.warn('Wishlist route not configured');
            return;
        }

        var $content = $('#wishlist-content');
        
        // 🔥 TAMPILKAN SPINNER LOADING
        $content.html(`
            <div class="popup-loading">
                <div class="popup-spinner"></div>
                <p>Memuat wishlist...</p>
            </div>
        `);

        $.ajax({
            url: window.customerRoutes.wishlistPopup,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $content.html(response.html);
                    if (response.count !== undefined) {
                        updateWishlistCount(response.count);
                    }
                    
                    // Show/hide clear button
                    if (response.count > 0) {
                        $('#wishlist-clear').addClass('visible').show();
                    } else {
                        $('#wishlist-clear').removeClass('visible').hide();
                    }
                }
            },
            error: function(xhr) {
                console.error('Error loading wishlist:', xhr);
                $content.html(`
                    <div class="popup-body-empty">
                        <iconify-icon icon="mdi:heart-outline"></iconify-icon>
                        <p>Gagal memuat wishlist</p>
                        <p>Silakan coba lagi</p>
                        <button onclick="loadWishlistPopup()" class="btn-primary" style="margin-top:16px;padding:8px 24px;font-size:13px;">
                            <iconify-icon icon="mdi:refresh" width="16"></iconify-icon>
                            Coba Lagi
                        </button>
                    </div>
                `);
                showToast('Gagal memuat wishlist', 'error');
            }
        });
    }

    /**
     * Update Wishlist Count Badge
     */
    function updateWishlistCount(count) {
        // 🔥 Update badge navbar (#wishlist-count) maupun badge lain (.wishlist-count / .wishlist-badge)
        $('#wishlist-count').add('.wishlist-count, .wishlist-badge').each(function() {
            if (count > 0) {
                $(this).text(count).removeClass('hidden').show().css('display', 'inline-flex');
            } else {
                $(this).addClass('hidden').hide();
            }
        });
    }

    /**
     * Add product to cart from wishlist
     */
    function addToCartFromWishlist(productId, variantId) {
        if (!productId) {
            showToast('Produk tidak valid', 'error');
            return;
        }

        // 🔥 SET FLAG UNTUK MENGETAHUI DARI WISHLIST
        window._fromWishlist = true;

        // 🔥 CEK APAKAH PRODUK PUNYA VARIAN
        fetch(`/api/products/${productId}/variants`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.variants && data.variants.length > 0) {
                // 🔥 ADA VARIAN → BUKA MODAL
                if (typeof window.openVariantModal === 'function') {
                    // Tutup wishlist popup
                    $('#wishlist-popup').removeClass('active');
                    $('body').removeClass('popup-open');
                    
                    // Buka modal varian dengan mode add_to_cart
                    window.openVariantModal(productId, 'add_to_cart');
                } else {
                    showToast('Terjadi kesalahan', 'error');
                    window._fromWishlist = false;
                }
            } else {
                // 🔥 TIDAK ADA VARIAN → LANGSUNG TAMBAH KE CART
                addToCartDirectlyFromWishlist(productId, null);
                window._fromWishlist = false;
            }
        })
        .catch(error => {
            console.error('Error checking variants:', error);
            // Jika error, coba langsung tambah
            addToCartDirectlyFromWishlist(productId, null);
            window._fromWishlist = false;
        });
    }

    function addToCartDirectlyFromWishlist(productId, variantId) {
        var $btn = $('.add-to-cart-wishlist[data-product-id="' + productId + '"]');
        var originalHtml = $btn.html();
        
        // Show loading state
        $btn.html('<iconify-icon icon="mdi:loading" width="16" class="spin"></iconify-icon>');
        $btn.prop('disabled', true);

        $.ajax({
            url: window.customerRoutes.cartAdd,
            method: 'POST',
            data: {
                product_id: productId,
                variant_id: variantId || null,
                quantity: 1,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function(response) {
                $btn.html(originalHtml);
                $btn.prop('disabled', false);
                
                if (response.success) {
                    // 🔥 UPDATE CART COUNT
                    if (typeof window.updateNavbarCartCount === 'function') {
                        window.updateNavbarCartCount(response.count);
                    }
                    
                    // 🔥 TAMPILKAN TOAST
                    showToast('Produk ditambahkan ke keranjang!', 'success');
                    
                    // 🔥 LOAD CART POPUP
                    loadCartPopup();
                    
                } else {
                    showToast(response.message || 'Gagal menambahkan ke keranjang', 'error');
                }
            },
            error: function(xhr) {
                $btn.html(originalHtml);
                $btn.prop('disabled', false);
                
                if (xhr.status === 401) {
                    showToast('Silakan login terlebih dahulu', 'warning');
                    setTimeout(function() {
                        window.location.href = '/login';
                    }, 1500);
                } else {
                    showToast('Terjadi kesalahan, silakan coba lagi', 'error');
                }
            }
        });
    }

    /**
     * Remove Item from Wishlist with animation
     */
    function removeWishlistItem(productId) {
        if (!productId) return;
        
        if (!confirm('Hapus produk ini dari wishlist?')) return;

        var $item = $('.wishlist-item[data-product-id="' + productId + '"]');
        
        // Add removing animation
        $item.css({
            'transition': 'all 0.3s ease',
            'opacity': '1',
            'transform': 'translateX(0)'
        });
        
        setTimeout(function() {
            $item.css({
                'opacity': '0',
                'transform': 'translateX(30px)'
            });
        }, 50);

        $('#wishlist-content').addClass('loading');

        $.ajax({
            url: window.customerRoutes.wishlistRemove,
            method: 'DELETE',
            data: {
                product_id: productId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function(response) {
                $('#wishlist-content').removeClass('loading');
                
                if (response.success) {
                    setTimeout(function() {
                        $item.remove();
                        
                        if ($('.wishlist-item').length === 0) {
                            loadWishlistPopup();
                        }
                        
                        // 🔥 UPDATE COUNT
                        if (response.count !== undefined) {
                            if (typeof window.updateNavbarWishlistCount === 'function') {
                                window.updateNavbarWishlistCount(response.count);
                            }
                        }
                        
                        // 🔥 UPDATE SEMUA TOMBOL WISHLIST DI HALAMAN
                        if (typeof window.updateWishlistIcon === 'function') {
                            window.updateWishlistIcon(productId, false);
                        }
                        
                        // 🔥 UPDATE SEMUA TOMBOL LAINNYA
                        document.querySelectorAll(`.add_to_wishlist_btn[data-product-id="${productId}"]`).forEach(function(btn) {
                            btn.innerHTML = '<iconify-icon icon="solar:heart-linear"></iconify-icon>';
                            btn.classList.remove('active');
                            btn.dataset.inWishlist = 'false';
                            btn.disabled = false;
                        });
                        
                        if (response.count === 0) {
                            $('#wishlist-clear').removeClass('visible').hide();
                        }
                        
                    }, 350);
                } else {
                    $item.css({
                        'opacity': '1',
                        'transform': 'translateX(0)'
                    });
                    showToast(response.message || 'Gagal menghapus item', 'error');
                }
            },
            error: function(xhr) {
                $('#wishlist-content').removeClass('loading');
                $item.css({
                    'opacity': '1',
                    'transform': 'translateX(0)'
                });
                showToast('Terjadi kesalahan, silakan coba lagi', 'error');
            }
        });
    }

    /**
     * Clear All Wishlist Items
     */
    function clearWishlist() {
        // 🔥 Ambil count dari elemen #wishlist-count atau dari data
        var wishlistCount = parseInt($('#wishlist-count').text()) || 0;
        
        // Jika tidak ada, coba dari class .wishlist-count
        if (wishlistCount === 0) {
            wishlistCount = parseInt($('.wishlist-count').first().text()) || 0;
        }
        
        // Jika masih 0, coba dari jumlah item di popup
        if (wishlistCount === 0) {
            wishlistCount = $('.wishlist-item').length;
        }
        
        console.log('❤️ Wishlist count before clear:', wishlistCount);
        
        if (wishlistCount === 0) {
            showToast('Wishlist sudah kosong', 'info');
            return;
        }
        
        if (!confirm('Kosongkan semua item di wishlist?')) return;
        
        $('#wishlist-content').addClass('loading');

        $.ajax({
            url: window.customerRoutes.wishlistClear,
            method: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function(response) {
                $('#wishlist-content').removeClass('loading');
                if (response.success) {
                    // 🔥 Update wishlist count ke 0
                    if (typeof window.updateNavbarWishlistCount === 'function') {
                        window.updateNavbarWishlistCount(0);
                    }
                    
                    // 🔥 Load ulang popup
                    loadWishlistPopup();
                    
                    // 🔥 Sembunyikan clear button
                    $('#wishlist-clear').removeClass('visible').hide();
                    
                    // 🔥 Update semua icon wishlist di halaman
                    if (typeof window.loadWishlistStatus === 'function') {
                        window.loadWishlistStatus();
                    }
                    
                } else {
                    showToast(response.message || 'Gagal mengosongkan wishlist', 'error');
                }
            },
            error: function(xhr) {
                $('#wishlist-content').removeClass('loading');
                showToast('Terjadi kesalahan, silakan coba lagi', 'error');
            }
        });
    }


    // ============================================
    // EVENT HANDLERS
    // ============================================

    // ---------- CART EVENTS ----------

    /**
     * Open Cart Popup
     */
    $(document).on('click', '#cart-toggle, .cart-icon, .open-cart-popup', function(e) {
        e.preventDefault();
        loadCartPopup();
        $('#cart-popup').addClass('active');
        $('body').addClass('popup-open');
    });

    /**
     * Close Cart Popup
     */
    $(document).on('click', '#cart-close, #cart-popup .popup_slide_overlay', function(e) {
        e.preventDefault();
        $('#cart-popup').removeClass('active');
        $('body').removeClass('popup-open');
    });

    /**
     * Clear Cart
     */
    $(document).on('click', '#cart-clear', function(e) {
        e.preventDefault();
        e.stopPropagation();
        clearCart();
    });

    /**
     * Quantity Button - Increase/Decrease
     */
    $(document).on('click', '#cart-content .qty-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var $input = $(this).siblings('.qty-input');
        var currentVal = parseInt($input.val()) || 1;
        var action = $(this).data('action');
        var key = $input.data('key');

        if (!key) {
            console.error('Missing data-key attribute');
            return;
        }

        if (action === 'increase') {
            $input.val(currentVal + 1);
        } else if (action === 'decrease' && currentVal > 1) {
            $input.val(currentVal - 1);
        } else if (action === 'decrease' && currentVal <= 1) {
            return;
        }

        updateCartItem(key, parseInt($input.val()));
    });

    /**
     * Quantity Input - Direct Change
     */
    $(document).on('change', '#cart-content .qty-input', function(e) {
        e.preventDefault();
        var $input = $(this);
        var key = $input.data('key');
        var quantity = parseInt($input.val()) || 1;

        if (quantity < 1) {
            quantity = 1;
            $input.val(1);
        }

        if (key) {
            updateCartItem(key, quantity);
        }
    });

    /**
     * Quantity Input - Keypress (Enter)
     */
    $(document).on('keypress', '#cart-content .qty-input', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            var $input = $(this);
            var key = $input.data('key');
            var quantity = parseInt($input.val()) || 1;

            if (quantity < 1) {
                quantity = 1;
                $input.val(1);
            }

            if (key) {
                updateCartItem(key, quantity);
            }
        }
    });

    /**
     * Remove Cart Item
     */
    $(document).on('click', '#cart-content .btn-remove', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var key = $(this).data('key');
        if (key) {
            removeCartItem(key);
        }
    });

    // ---------- WISHLIST EVENTS ----------

    /**
     * Open Wishlist Popup
     */
    $(document).on('click', '#wishlist-toggle, .wishlist-icon, .open-wishlist-popup', function(e) {
        e.preventDefault();
        loadWishlistPopup();
        $('#wishlist-popup').addClass('active');
        $('body').addClass('popup-open');
    });

    /**
     * Close Wishlist Popup
     */
    $(document).on('click', '#wishlist-close, #wishlist-popup .popup_slide_overlay', function(e) {
        e.preventDefault();
        $('#wishlist-popup').removeClass('active');
        $('body').removeClass('popup-open');
    });

    /**
     * Clear Wishlist
     */
    $(document).on('click', '#wishlist-clear', function(e) {
        e.preventDefault();
        e.stopPropagation();
        clearWishlist();
    });

    /**
     * Add to Cart from Wishlist
     */
    $(document).on('click', '.add-to-cart-wishlist', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var productId = $(this).data('product-id');
        var variantId = $(this).data('variant-id');
        
        // 🔥 PANGGIL FUNGSI DENGAN MODAL VARIAN
        addToCartFromWishlist(productId, variantId);
    });

    /**
     * Remove from Wishlist
     */
    $(document).on('click', '.remove-wishlist', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var productId = $(this).data('product-id');
        removeWishlistItem(productId);
    });

    // ---------- KEYBOARD EVENTS ----------

    /**
     * Close popup with ESC key
     */
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            if ($('#cart-popup').hasClass('active')) {
                $('#cart-popup').removeClass('active');
                $('body').removeClass('popup-open');
            }
            if ($('#wishlist-popup').hasClass('active')) {
                $('#wishlist-popup').removeClass('active');
                $('body').removeClass('popup-open');
            }
        }
    });

    // ---------- CUSTOM EVENTS ----------

    /**
     * Listen for cart updated events from other scripts
     */
    $(document).on('cart-updated', function(e, data) {
        console.log('🛒 cart-updated event received:', data);
        
        // 🔥 HANYA UPDATE CART COUNTER
        if (typeof window.updateNavbarCartCount === 'function') {
            if (data && data.count !== undefined) {
                window.updateNavbarCartCount(data.count);
            } else {
                window.loadCartCount();
            }
        }
        
        // Load cart popup
        loadCartPopup();
    });

    /**
     * Listen for wishlist updated events from other scripts
     */
    $(document).on('wishlist-updated', function(e, data) {
        console.log('❤️ wishlist-updated event received:', data);
        
        if (typeof window.updateNavbarWishlistCount === 'function') {
            if (data && data.count !== undefined) {
                window.updateNavbarWishlistCount(data.count);
            } else {
                window.loadWishlistCount();
            }
        }
        
        // 🔥 UPDATE WISHLIST ICON
        if (data && data.product_id !== undefined) {
            if (typeof window.updateWishlistIcon === 'function') {
                window.updateWishlistIcon(data.product_id, data.in_wishlist);
            }
        }
    });

    // ---------- CLICK OUTSIDE ----------

    /**
     * Close popup when clicking outside (on overlay)
     */
    $(document).on('click', function(e) {
        var $cartPopup = $('#cart-popup');
        var $wishlistPopup = $('#wishlist-popup');
        
        // Close cart if clicking outside
        if ($cartPopup.hasClass('active') && 
            !$(e.target).closest('.popup-slide-box').length && 
            !$(e.target).closest('#cart-toggle').length &&
            !$(e.target).closest('.cart-icon').length &&
            !$(e.target).closest('.open-cart-popup').length) {
            $cartPopup.removeClass('active');
            $('body').removeClass('popup-open');
        }
        
        // Close wishlist if clicking outside
        if ($wishlistPopup.hasClass('active') && 
            !$(e.target).closest('.popup-slide-box').length && 
            !$(e.target).closest('#wishlist-toggle').length &&
            !$(e.target).closest('.wishlist-icon').length &&
            !$(e.target).closest('.open-wishlist-popup').length) {
            $wishlistPopup.removeClass('active');
            $('body').removeClass('popup-open');
        }
    });


    // ============================================
    // INITIALIZATION
    // ============================================

    // Load cart count on page load
    loadCartCount();

    // Load wishlist count on page load
    if (window.customerRoutes.wishlistPopup) {
        $.ajax({
            url: window.customerRoutes.wishlistPopup,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.count !== undefined) {
                    updateWishlistCount(response.count);
                    if (response.count > 0) {
                        $('#wishlist-clear').addClass('visible').show();
                    }
                }
            },
            error: function(xhr) {
                console.error('Error loading wishlist count:', xhr);
            }
        });
    }

    // Initial cart footer state
    setTimeout(function() {
        var cartCount = parseInt($('.cart-count').text()) || 0;
        toggleCartFooter(cartCount > 0);
        if (cartCount > 0) {
            $('#cart-clear').addClass('visible').show();
        } else {
            $('#cart-clear').removeClass('visible').hide();
        }
    }, 500);

    // Initial wishlist clear button state
    setTimeout(function() {
        var wishlistCount = parseInt($('.wishlist-count').text()) || 0;
        if (wishlistCount > 0) {
            $('#wishlist-clear').addClass('visible').show();
        } else {
            $('#wishlist-clear').removeClass('visible').hide();
        }
    }, 500);

    console.log('Popup.js initialized successfully');
});