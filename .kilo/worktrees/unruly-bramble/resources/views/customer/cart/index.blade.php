@extends('layouts.customer')

@section('title', 'Keranjang - Barokah Sport')

@section('content')

<main class="cart-container-page">
    <div class="katalog_top_container_page">
        <div class="cart-header-page">
            <h1>Keranjang Belanja</h1>
            <p>Tinjau dan kelola item di keranjang belanja Anda.</p>
        </div>
    </div>

    @if (empty($cart) || count($cart) == 0)
        {{-- Empty Cart --}}
        <div class="cart-empty-page">
            <div class="empty-icon-page"><iconify-icon icon="mdi:cart-outline"></iconify-icon></div>
            <h3>Keranjang Kosong</h3>
            <p>Belum ada produk di keranjang. Yuk, mulai belanja!</p>
            <a href="{{ route('customer.products.index') }}" class="btn-shop-page">
                Mulai Belanja
            </a>
        </div>
    @else
        <div class="cart-grid-page">

            {{-- Cart Items --}}
            <div class="cart-items-wrapper-page">
                <div class="cart-items-page">
                    @php $subtotal = 0; @endphp
                    @foreach ($cart as $key => $item)
                        @php 
                            $itemPrice = isset($item['price']) ? $item['price'] : 0;
                            $itemQuantity = isset($item['quantity']) ? $item['quantity'] : 1;
                            $subtotal += $itemPrice * $itemQuantity; 
                        @endphp
                        <div class="cart-item-page" data-key="{{ $item['id'] }}">
                            <div class="cart-item-left-page">
                                {{-- Image --}}
                                <div class="cart-item-image-page">
                                    @if (!empty($item['image']) && Storage::disk('public')->exists($item['image']))
                                        <img src="{{ Storage::url($item['image']) }}" 
                                             alt="{{ $item['product_name'] ?? 'Produk' }}">
                                    @else
                                        <div class="placeholder-page">📦</div>
                                    @endif
                                </div>

                                {{-- Info --}}
                                <div class="cart-item-info-page">
                                    <a href="{{ route('customer.products.show', $item['slug'] ?? '#') }}" 
                                       class="item-name-page">
                                        {{ $item['product_name'] ?? 'Produk' }}
                                    </a>
                                    @if (!empty($item['variant_name']))
                                        <p class="item-variant-page">Varian: {{ $item['variant_name'] }}</p>
                                    @endif
                                    <p class="item-price-page">
                                        Rp {{ number_format($itemPrice, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="cart-item-actions-page">
                                {{-- Quantity --}}
                                <div class="qty-wrapper-page">
                                    <button class="qty-btn-page" data-action="decrease">−</button>
                                    <input type="number" class="qty-input-page" 
                                        value="{{ $item['quantity'] }}" min="1" 
                                        data-key="{{ $item['id'] }}">
                                    <button class="qty-btn-page" data-action="increase">+</button>
                                </div>

                                {{-- Remove --}}
                                <button class="btn-remove-page" data-key="{{ $item['id'] }}">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>

                                {{-- Subtotal item --}}
                                <p class="item-subtotal-page">
                                    Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Action Buttons --}}
                <div class="cart-bottom-actions-page">
                    <a href="{{ route('customer.products.index') }}" class="btn-continue-page">
                        ← Lanjut Belanja
                    </a>
                    <form action="{{ route('customer.cart.clear') }}" method="POST" 
                          onsubmit="return confirm('Kosongkan keranjang?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-clear-page">
                            Kosongkan Keranjang
                        </button>
                    </form>
                </div>
            </div>

            {{-- Summary --}}
            <div class="cart-summary-page">
                <h2>Ringkasan Belanja</h2>

                <div class="summary-row-page">
                    <span class="label-page">Subtotal</span>
                    <span class="value-page">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row-page">
                    <span class="label-page">Ongkir</span>
                    <span class="value-page">Dihitung di checkout</span>
                </div>

                <div class="summary-divider-page"></div>

                <div class="summary-total-page">
                    <span>Total</span>
                    <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>

                <form action="{{ route('customer.checkout.index') }}" method="GET" id="checkout-form">
                    <button type="submit" class="btn-checkout-page" id="checkout-btn">
                        Checkout →
                    </button>
                </form>
            </div>

        </div>
    @endif

</main>

@include('customer.partials.footer')

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 🔥 AMBIL CSRF TOKEN DENGAN BENAR
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // ============================================ */
    // UPDATE QUANTITY
    // ============================================ */

    document.querySelectorAll('.qty-btn-page').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const wrapper = this.closest('.qty-wrapper-page');
            const input = wrapper.querySelector('.qty-input-page');
            let value = parseInt(input.value) || 1;
            const action = this.dataset.action;

            if (action === 'increase') {
                value += 1;
            } else if (action === 'decrease' && value > 1) {
                value -= 1;
            }

            if (value < 1) return;

            input.value = value;
            
            const key = input.dataset.key;
            if (!key) {
                console.error('Key tidak ditemukan');
                return;
            }
            
            updateCart(key, value);
        });
    });

    document.querySelectorAll('.qty-input-page').forEach(input => {
        input.addEventListener('change', function(e) {
            let value = parseInt(this.value) || 1;
            if (value < 1) {
                value = 1;
                this.value = 1;
            }
            
            const key = this.dataset.key;
            if (!key) {
                console.error('Key tidak ditemukan');
                return;
            }
            
            updateCart(key, value);
        });
    });

    function updateCart(key, quantity) {
        console.log('🔄 Updating cart:', { key, quantity });

        fetch('{{ route("customer.cart.update") }}', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ key: key, quantity: quantity })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'Gagal update keranjang');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Gagal memperbarui keranjang');
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'Terjadi kesalahan. Silakan coba lagi.');
            window.location.reload();
        });
    }

    // ============================================ */
    // REMOVE ITEM
    // ============================================ */

    document.querySelectorAll('.btn-remove-page').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (!confirm('Hapus item ini dari keranjang?')) return;

            const key = this.dataset.key;
            if (!key) {
                console.error('Key tidak ditemukan');
                return;
            }

            console.log('🗑️ Removing item:', key);

            fetch('{{ route("customer.cart.remove") }}', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ key: key })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || 'Gagal hapus item');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Gagal menghapus item');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'Terjadi kesalahan. Silakan coba lagi.');
            });
        });
    });
});
</script>

@endsection