@extends('layouts.account')

@section('title', 'Testimoni - Barokah Sport')
@section('page-title', 'Testimoni')
@section('page-subtitle', 'Berikan ulasan untuk pesanan yang sudah selesai.')

@section('account-content')

<style>
    /* ============================================
       TESTIMONIAL PAGE STYLES
       ============================================ */
    .ts-wrapper {
        display: flex;
        flex-direction: column;
        gap: 1.2vw;
    }

    /* --------------------------------------------
       TABS
       -------------------------------------------- */
    .ts-tabs {
        display: flex;
        gap: 0.2vw;
        border-bottom: 0.1vw solid #e5e7eb;
        overflow-x: auto;
        scrollbar-width: none;
    }
    .ts-tabs::-webkit-scrollbar { display: none; }

    .ts-tab {
        display: inline-flex;
        align-items: center;
        gap: 0.5vw;
        padding: 1vw 1.3vw;
        font-size: 0.85vw;
        font-weight: 500;
        color: #64748b;
        text-decoration: none;
        border-bottom: 0.2vw solid transparent;
        white-space: nowrap;
        transition: all 0.2s ease;
        flex-shrink: 0;
        margin-bottom: -0.1vw;
    }

    .ts-tab:hover {
        color: #0f172a;
        border-bottom-color: #cbd5e1;
    }

    .ts-tab.active {
        color: rgb(102, 72, 9);
        border-bottom-color: #ecbc42;
        font-weight: 700;
    }

    .ts-tab iconify-icon {
        font-size: 1.1vw;
    }

    .ts-tab.active iconify-icon { color: #ecbc42; }

    .ts-tab-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 1.5vw;
        height: 1.5vw;
        padding: 0 0.5vw;
        border-radius: 100vw;
        font-size: 0.7vw;
        font-weight: 700;
        background: #f1f5f9;
        color: #64748b;
    }

    .ts-tab.active .ts-tab-badge {
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
        color: rgb(102, 72, 9);
    }

    /* --------------------------------------------
       EMPTY STATE
       -------------------------------------------- */
    .ts-empty {
        text-align: center;
        padding: 4vw 2vw;
    }

    .ts-empty-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 5vw;
        height: 5vw;
        margin: 0 auto;
        border-radius: 50%;
        background: linear-gradient(135deg, #FDDD57 0%, #ecbc42 100%);
        color: rgb(102, 72, 9);
        box-shadow: 0 0.3vw 1vw rgba(236, 188, 66, 0.3);
    }

    .ts-empty-icon iconify-icon {
        font-size: 2.5vw;
    }

    .ts-empty-title {
        font-size: 1.05vw;
        font-weight: 700;
        color: #0f172a;
        margin-top: 1.2vw;
    }

    .ts-empty-desc {
        font-size: 0.85vw;
        color: #64748b;
        margin-top: 0.5vw;
        line-height: 1.6;
        max-width: 30vw;
        margin-left: auto;
        margin-right: auto;
    }

    /* --------------------------------------------
       ORDER LIST
       -------------------------------------------- */
    .ts-list {
        display: flex;
        flex-direction: column;
        gap: 1vw;
    }

    .ts-card {
        background: #ffffff;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.9vw;
        padding: 1.3vw;
        transition: all 0.25s ease;
    }

    .ts-card:hover {
        border-color: #ecbc42;
        box-shadow: 0 0.4vw 1.5vw rgba(0, 0, 0, 0.06);
    }

    .ts-card.is-completed {
        background: linear-gradient(135deg, #fffbf0 0%, #fff7e0 100%);
        border-color: #fde68a;
    }

    /* Head */
    .ts-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1vw;
        flex-wrap: wrap;
    }

    .ts-order-num {
        display: flex;
        align-items: center;
        gap: 0.4vw;
        font-size: 0.9vw;
        font-weight: 700;
        color: #0f172a;
    }

    .ts-order-num iconify-icon {
        color: #ecbc42;
        font-size: 1.05vw;
    }

    .ts-order-date {
        display: flex;
        align-items: center;
        gap: 0.3vw;
        font-size: 0.72vw;
        color: #94a3b8;
        margin-top: 0.3vw;
    }

    .ts-order-date iconify-icon {
        font-size: 0.85vw;
    }

    .ts-order-total {
        font-size: 1.05vw;
        font-weight: 800;
        color: #0f172a;
    }

    /* Items Preview */
    .ts-items {
        margin-top: 1vw;
        padding-top: 1vw;
        border-top: 0.1vw dashed #e2e8f0;
        display: flex;
        gap: 1vw;
        overflow-x: auto;
        scrollbar-width: thin;
        padding-bottom: 0.5vw;
    }

    .ts-items::-webkit-scrollbar { height: 0.3vw; }
    .ts-items::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 100vw;
    }

    .ts-item {
        display: flex;
        align-items: center;
        gap: 0.7vw;
        flex-shrink: 0;
        max-width: 16vw;
    }

    .ts-item-img {
        width: 3vw;
        height: 3vw;
        border-radius: 0.6vw;
        background: #f1f5f9;
        overflow: hidden;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .ts-item-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .ts-item-img iconify-icon {
        font-size: 1.4vw;
        color: #cbd5e1;
    }

    .ts-item-info { min-width: 0; }

    .ts-item-name {
        font-size: 0.8vw;
        font-weight: 600;
        color: #334155;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .ts-item-qty {
        font-size: 0.7vw;
        color: #94a3b8;
        margin-top: 0.15vw;
    }

    .ts-item-more {
        display: inline-flex;
        align-items: center;
        gap: 0.3vw;
        padding: 0.5vw 0.8vw;
        border-radius: 0.5vw;
        background: #f8fafc;
        color: #64748b;
        font-size: 0.75vw;
        font-weight: 600;
        flex-shrink: 0;
        white-space: nowrap;
    }

    /* Footer */
    .ts-footer {
        margin-top: 1.2vw;
        padding-top: 1vw;
        border-top: 0.1vw solid #f1f5f9;
    }

    .ts-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4vw;
        padding: 0.75vw 1.3vw;
        border-radius: 0.7vw;
        font-size: 0.82vw;
        font-weight: 700;
        cursor: pointer;
        border: none;
        transition: all 0.2s ease;
        text-decoration: none;
        font-family: inherit;
    }

    .ts-btn iconify-icon {
        font-size: 1vw;
    }

    .ts-btn-gold {
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
        color: rgb(102, 72, 9);
        box-shadow: 0 0.15vw 0.5vw rgba(236, 188, 66, 0.3);
    }

    .ts-btn-gold:hover {
        transform: translateY(-0.1vw);
        box-shadow: 0 0.3vw 1vw rgba(236, 188, 66, 0.45);
    }

    /* Completed Badge */
    .ts-done {
        display: inline-flex;
        align-items: center;
        gap: 0.4vw;
        padding: 0.6vw 1vw;
        border-radius: 0.6vw;
        background: #ecfdf5;
        border: 0.1vw solid #a7f3d0;
        color: #047857;
        font-size: 0.8vw;
        font-weight: 600;
    }

    .ts-done iconify-icon {
        font-size: 1vw;
    }

    .ts-done .stars {
        display: inline-flex;
        gap: 0.05vw;
        margin-left: 0.2vw;
    }

    .ts-done .stars iconify-icon {
        color: #f59e0b;
        font-size: 0.85vw;
    }

    /* ============================================
       RESPONSIVE - TABLET
       ============================================ */
    @media (max-width: 1024px) {
        .ts-wrapper { gap: 3vw; }

        .ts-tabs { border-bottom-width: 0.2vw; }

        .ts-tab {
            padding: 2.5vw 3vw;
            font-size: 2vw;
            gap: 1.2vw;
            border-bottom-width: 0.4vw;
        }
        .ts-tab iconify-icon { font-size: 2.5vw; }

        .ts-tab-badge {
            min-width: 3.5vw;
            height: 3.5vw;
            padding: 0 1vw;
            font-size: 1.7vw;
        }

        .ts-empty { padding: 8vw 4vw; }
        .ts-empty-icon {
            width: 12vw;
            height: 12vw;
        }
        .ts-empty-icon iconify-icon { font-size: 6vw; }
        .ts-empty-title { font-size: 2.7vw; margin-top: 3vw; }
        .ts-empty-desc {
            font-size: 2vw;
            margin-top: 1.2vw;
            max-width: 70vw;
        }

        .ts-list { gap: 2.5vw; }

        .ts-card {
            padding: 3vw;
            border-radius: 2vw;
            border-width: 0.2vw;
        }

        .ts-head { gap: 2vw; }

        .ts-order-num {
            font-size: 2.3vw;
            gap: 1vw;
        }
        .ts-order-num iconify-icon { font-size: 2.7vw; }

        .ts-order-date {
            font-size: 1.8vw;
            gap: 0.8vw;
            margin-top: 0.7vw;
        }
        .ts-order-date iconify-icon { font-size: 2.2vw; }

        .ts-order-total { font-size: 2.5vw; }

        .ts-items {
            margin-top: 2.5vw;
            padding-top: 2.5vw;
            gap: 2.5vw;
            border-top-width: 0.2vw;
        }

        .ts-item {
            gap: 1.7vw;
            max-width: 40vw;
        }

        .ts-item-img {
            width: 8vw;
            height: 8vw;
            border-radius: 1.5vw;
        }
        .ts-item-img iconify-icon { font-size: 3.5vw; }

        .ts-item-name { font-size: 2vw; }
        .ts-item-qty { font-size: 1.7vw; margin-top: 0.4vw; }

        .ts-item-more {
            padding: 1.5vw 2.5vw;
            border-radius: 1.5vw;
            font-size: 1.9vw;
            gap: 0.7vw;
        }

        .ts-footer {
            margin-top: 2.5vw;
            padding-top: 2.3vw;
            border-top-width: 0.2vw;
        }

        .ts-btn {
            padding: 2.2vw 3vw;
            border-radius: 1.5vw;
            font-size: 2.1vw;
            gap: 1vw;
        }
        .ts-btn iconify-icon { font-size: 2.5vw; }

        .ts-done {
            padding: 1.7vw 2.5vw;
            border-radius: 1.5vw;
            font-size: 2vw;
            gap: 1vw;
            border-width: 0.2vw;
        }
        .ts-done iconify-icon { font-size: 2.4vw; }
        .ts-done .stars iconify-icon { font-size: 2.1vw; }
    }

    /* ============================================
       RESPONSIVE - MOBILE
       ============================================ */
    @media (max-width: 480px) {
        .ts-wrapper { gap: 4vw; }

        .ts-tabs { border-bottom-width: 0.3vw; }

        .ts-tab {
            padding: 3.5vw 4vw;
            font-size: 3.2vw;
            gap: 1.5vw;
            border-bottom-width: 0.6vw;
        }
        .ts-tab iconify-icon { font-size: 4vw; }

        .ts-tab-badge {
            min-width: 5vw;
            height: 5vw;
            padding: 0 1.5vw;
            font-size: 2.5vw;
        }

        .ts-empty { padding: 10vw 5vw; }
        .ts-empty-icon {
            width: 20vw;
            height: 20vw;
        }
        .ts-empty-icon iconify-icon { font-size: 10vw; }
        .ts-empty-title { font-size: 4.2vw; margin-top: 5vw; }
        .ts-empty-desc { font-size: 3.2vw; margin-top: 2vw; }

        .ts-list { gap: 4vw; }

        .ts-card {
            padding: 4.5vw 4vw;
            border-radius: 3vw;
            border-width: 0.3vw;
        }

        .ts-head {
            gap: 2.5vw;
        }

        .ts-order-num {
            font-size: 3.8vw;
            gap: 1.5vw;
        }
        .ts-order-num iconify-icon { font-size: 4.2vw; }

        .ts-order-date {
            font-size: 2.8vw;
            gap: 1.2vw;
            margin-top: 1vw;
        }
        .ts-order-date iconify-icon { font-size: 3.3vw; }

        .ts-order-total { font-size: 4vw; }

        .ts-items {
            margin-top: 4vw;
            padding-top: 4vw;
            gap: 3vw;
            border-top-width: 0.3vw;
        }

        .ts-item {
            gap: 2.5vw;
            max-width: 70vw;
        }

        .ts-item-img {
            width: 14vw;
            height: 14vw;
            border-radius: 2.5vw;
        }
        .ts-item-img iconify-icon { font-size: 6vw; }

        .ts-item-name { font-size: 3.2vw; }
        .ts-item-qty { font-size: 2.7vw; margin-top: 0.7vw; }

        .ts-item-more {
            padding: 2.5vw 3.5vw;
            border-radius: 2.5vw;
            font-size: 3vw;
            gap: 1.2vw;
        }

        .ts-footer {
            margin-top: 4vw;
            padding-top: 4vw;
            border-top-width: 0.3vw;
        }

        .ts-btn {
            width: 100%;
            padding: 3.5vw 4vw;
            border-radius: 2.5vw;
            font-size: 3.2vw;
            gap: 1.5vw;
        }
        .ts-btn iconify-icon { font-size: 4.2vw; }

        .ts-done {
            flex-wrap: wrap;
            padding: 2.8vw 3.5vw;
            border-radius: 2.5vw;
            font-size: 3.2vw;
            gap: 1.5vw;
            border-width: 0.3vw;
        }
        .ts-done iconify-icon { font-size: 4vw; }
        .ts-done .stars { gap: 0.2vw; }
        .ts-done .stars iconify-icon { font-size: 3.4vw; }
    }
</style>

<div class="ts-wrapper">

    {{-- 🔥 TABS --}}
    <div class="ts-tabs">
        <a href="{{ route('customer.testimonials.index', ['tab' => 'all']) }}"
           class="ts-tab {{ $activeTab == 'all' ? 'active' : '' }}">
            <iconify-icon icon="mdi:star-outline"></iconify-icon>
            Semua
            @if($orders->count() > 0 && $activeTab == 'all')
                <span class="ts-tab-badge">{{ $orders->count() }}</span>
            @endif
        </a>
        <a href="{{ route('customer.testimonials.index', ['tab' => 'completed']) }}"
           class="ts-tab {{ $activeTab == 'completed' ? 'active' : '' }}">
            <iconify-icon icon="mdi:check-circle-outline"></iconify-icon>
            Selesai
            @if($activeTab == 'completed' && $orders->count() > 0)
                <span class="ts-tab-badge">{{ $orders->count() }}</span>
            @elseif($activeTab == 'all' && isset($completedCount) && $completedCount > 0)
                <span class="ts-tab-badge">{{ $completedCount }}</span>
            @endif
        </a>
    </div>

    @if ($orders->isEmpty())
        {{-- Empty State --}}
        <div class="ts-empty">
            <div class="ts-empty-icon">
                <iconify-icon icon="mdi:star-circle-outline"></iconify-icon>
            </div>
            <div class="ts-empty-title">
                {{ $activeTab == 'all' ? 'Belum Ada Pesanan yang Perlu Diberi Testimonial' : 'Belum Ada Testimonial' }}
            </div>
            <div class="ts-empty-desc">
                {{ $activeTab == 'all'
                    ? 'Pesanan yang sudah selesai dan belum diberi testimonial akan muncul di sini.'
                    : 'Pesanan yang sudah selesai dan sudah diberi testimonial akan muncul di sini.' }}
            </div>
        </div>
    @else
        {{-- Order List --}}
        <div class="ts-list">
            @foreach ($orders as $order)
                <div class="ts-card {{ $activeTab == 'completed' ? 'is-completed' : '' }}">
                    {{-- Head --}}
                    <div class="ts-head">
                        <div>
                            <div class="ts-order-num">
                                <iconify-icon icon="mdi:receipt-text-outline"></iconify-icon>
                                #{{ $order->order_number }}
                            </div>
                            <div class="ts-order-date">
                                <iconify-icon icon="mdi:calendar-clock-outline"></iconify-icon>
                                {{ $order->created_at->translatedFormat('d M Y, H:i') }}
                            </div>
                        </div>
                        <div class="ts-order-total">
                            Rp {{ number_format($order->total, 0, ',', '.') }}
                        </div>
                    </div>

                    {{-- Items Preview --}}
                    <div class="ts-items">
                        @foreach ($order->items->take(3) as $item)
                            <div class="ts-item">
                                <div class="ts-item-img">
                                    @if ($item->product && $item->product->images->first())
                                        <img src="{{ Storage::url($item->product->images->first()->image) }}"
                                             alt="{{ $item->product_name }}">
                                    @else
                                        <iconify-icon icon="mdi:image-off-outline"></iconify-icon>
                                    @endif
                                </div>
                                <div class="ts-item-info">
                                    <div class="ts-item-name">{{ $item->product_name }}</div>
                                    <div class="ts-item-qty">
                                        {{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if ($order->items->count() > 3)
                            <div class="ts-item-more">
                                <iconify-icon icon="mdi:plus-circle-outline"></iconify-icon>
                                +{{ $order->items->count() - 3 }} lainnya
                            </div>
                        @endif
                    </div>

                    {{-- Footer --}}
                    <div class="ts-footer">
                        @if ($activeTab == 'completed')
                            <div class="ts-done">
                                <iconify-icon icon="mdi:check-circle"></iconify-icon>
                                <span>Testimonial telah diberikan</span>
                                <span class="stars">
                                    @for($i = 0; $i < 5; $i++)
                                        <iconify-icon icon="mdi:star"></iconify-icon>
                                    @endfor
                                </span>
                            </div>
                        @else
                            <button type="button"
                                    onclick="openTestimonialModal({{ $order->id }})"
                                    class="ts-btn ts-btn-gold">
                                <iconify-icon icon="mdi:star-outline"></iconify-icon>
                                Beri Testimonial
                            </button>

                            @include('customer.testimonial._modal', ['order' => $order, 'customer' => Auth::guard('customer')->user()])
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    /* ============================================
       OPEN / CLOSE MODAL
       ============================================ */
    function openTestimonialModal(orderId) {
        const modal = document.getElementById('testimonial-modal-' + orderId);
        if (modal) {
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
            initRating(orderId);
        }
    }

    function closeTestimonialModal(orderId) {
        const modal = document.getElementById('testimonial-modal-' + orderId);
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    /* ============================================
       INIT RATING
       ============================================ */
    function initRating(orderId) {
        const wrapper = document.getElementById('rating_wrapper_' + orderId);
        if (!wrapper) return;

        const ratingInputs = wrapper.querySelectorAll('.rating-input-' + orderId);
        const ratingLabel = document.getElementById('rating-label-' + orderId);
        const starLabels = wrapper.querySelectorAll('.star-label-' + orderId);

        function updateStars(rating) {
            if (ratingLabel) ratingLabel.textContent = rating + '/5';
            starLabels.forEach(function(sl) {
                const idx = parseInt(sl.getAttribute('data-index'));
                const icon = sl.querySelector('.star-icon');
                if (icon) {
                    icon.setAttribute('icon', idx <= rating ? 'mdi:star' : 'mdi:star-outline');
                    icon.className = 'star-icon h-7 w-7 cursor-pointer ' + (idx <= rating ? 'text-yellow-400' : 'text-gray-300');
                }
            });
        }

        ratingInputs.forEach(function(input) {
            input.addEventListener('change', function() {
                updateStars(parseInt(this.value));
            });
        });

        updateStars(5);
    }

    /* ============================================
       INIT IMAGE PREVIEW
       ============================================ */
    function initImagePreview(orderId) {
        const input = document.getElementById('images_' + orderId);
        if (!input) return;

        input.addEventListener('change', function(e) {
            const preview = document.getElementById('image-preview-' + orderId);
            if (!preview) return;
            preview.innerHTML = '';

            const files = Array.from(e.target.files);
            files.slice(0, 10).forEach(function(file) {
                if (!file.type.startsWith('image/')) return;

                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative h-20 w-20';
                    div.innerHTML = '<img src="' + e.target.result + '" alt="Preview" class="h-full w-full rounded-lg object-cover ring-1 ring-gray-200">';
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });
    }

    /* ============================================
       SUBMIT TESTIMONIAL FORM VIA AJAX
       ============================================ */
    function submitTestimonialForm(orderId) {
        const form = document.getElementById('testimonial-form-' + orderId);
        if (!form) return;

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                closeTestimonialModal(orderId);

                const btn = document.querySelector('[onclick="openTestimonialModal(' + orderId + ')"]');
                if (btn) {
                    const footer = btn.closest('.ts-footer');
                    if (footer) {
                        footer.innerHTML = `
                            <div class="ts-done">
                                <iconify-icon icon="mdi:check-circle"></iconify-icon>
                                <span>Testimonial telah diberikan</span>
                                <span class="stars">
                                    <iconify-icon icon="mdi:star"></iconify-icon>
                                    <iconify-icon icon="mdi:star"></iconify-icon>
                                    <iconify-icon icon="mdi:star"></iconify-icon>
                                    <iconify-icon icon="mdi:star"></iconify-icon>
                                    <iconify-icon icon="mdi:star"></iconify-icon>
                                </span>
                            </div>
                        `;
                    }
                }
            } else {
                alert(data.message || 'Gagal mengirim testimonial.');
            }
        })
        .catch(function(error) {
            console.error(error);
            alert('Terjadi kesalahan. Coba lagi.');
        });
    }

    /* ============================================
       GLOBAL EXPORTS
       ============================================ */
    window.openTestimonialModal = openTestimonialModal;
    window.closeTestimonialModal = closeTestimonialModal;
    window.submitTestimonialForm = submitTestimonialForm;
    window.removePreview = function(button) {
        button.closest('.relative').remove();
    };

    /* ============================================
       CLOSE MODAL ON OUTSIDE CLICK
       ============================================ */
    document.addEventListener('click', function(e) {
        document.querySelectorAll('[id^="testimonial-modal-"]').forEach(function(modal) {
            if (e.target === modal) {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
        });
    });

    /* ============================================
       CLOSE ON ESC
       ============================================ */
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('[id^="testimonial-modal-"]').forEach(function(modal) {
                if (modal.style.display === 'block') {
                    modal.style.display = 'none';
                    document.body.style.overflow = '';
                }
            });
        }
    });

    /* ============================================
       INIT IMAGE PREVIEW ON PAGE LOAD
       ============================================ */
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('[id^="testimonial-modal-"]').forEach(function(modal) {
            const orderId = modal.id.replace('testimonial-modal-', '');
            initImagePreview(orderId);
        });
    });
</script>
@endpush