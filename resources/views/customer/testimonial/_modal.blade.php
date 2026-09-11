<div class="testimonial-modal" id="testimonial-modal-{{ $order->id }}" style="display: none;">
    <div class="tm-overlay">
        <div class="tm-box" id="testimonial-modal-content-{{ $order->id }}">

            {{-- HEADER --}}
            <div class="tm-header">
                <div class="tm-header-left">
                    <div class="tm-header-icon">
                        <iconify-icon icon="mdi:star-circle-outline"></iconify-icon>
                    </div>
                    <div>
                        <div class="tm-title">Beri Testimonial</div>
                        <div class="tm-subtitle">Pesanan #{{ $order->order_number }}</div>
                    </div>
                </div>
                <button type="button"
                        onclick="closeTestimonialModal({{ $order->id }})"
                        class="tm-close-btn"
                        aria-label="Tutup">
                    <iconify-icon icon="mdi:close"></iconify-icon>
                </button>
            </div>

            {{-- BODY --}}
            <div class="tm-body">
                <form id="testimonial-form-{{ $order->id }}"
                      action="{{ route('customer.testimonial.store', $order) }}"
                      method="POST" enctype="multipart/form-data">

                    @csrf

                    {{-- Nama --}}
                    <div class="tm-group">
                        <label class="tm-label">
                            <iconify-icon icon="mdi:account-outline"></iconify-icon>
                            Nama <span class="required">*</span>
                        </label>
                        <input type="text"
                               name="customer_name"
                               value="{{ old('customer_name', $customer->name ?? '') }}"
                               placeholder="Nama lengkap Anda"
                               class="tm-input @error('customer_name') has-error @enderror">
                        @error('customer_name')
                            <p class="tm-error">
                                <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Rating --}}
                    <div class="tm-group" id="rating_wrapper_{{ $order->id }}">
                        <label class="tm-label">
                            <iconify-icon icon="mdi:star-outline"></iconify-icon>
                            Rating <span class="required">*</span>
                        </label>

                        @php
                            $oldRating = old('rating', 5);
                        @endphp

                        <div class="tm-rating-wrap">
                            @for ($i = 1; $i <= 5; $i++)
                                <label class="tm-star-label star-label-{{ $order->id }}" data-index="{{ $i }}">
                                    <input type="radio"
                                           name="rating"
                                           value="{{ $i }}"
                                           {{ $i == $oldRating ? 'checked' : '' }}
                                           class="rating-input-{{ $order->id }}"
                                           data-rating="{{ $i }}">
                                    <iconify-icon
                                        icon="{{ $i <= $oldRating ? 'mdi:star' : 'mdi:star-outline' }}"
                                        class="star-icon tm-star {{ $i <= $oldRating ? 'is-active' : '' }}">
                                    </iconify-icon>
                                </label>
                            @endfor

                            <span id="rating-label-{{ $order->id }}" class="tm-rating-label">
                                {{ $oldRating }}/5
                            </span>
                        </div>

                        @error('rating')
                            <p class="tm-error">
                                <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Testimonial --}}
                    <div class="tm-group">
                        <label class="tm-label">
                            <iconify-icon icon="mdi:comment-text-outline"></iconify-icon>
                            Testimonial <span class="required">*</span>
                        </label>
                        <textarea name="testimonial"
                                  rows="4"
                                  placeholder="Bagikan pengalaman Anda dengan produk ini..."
                                  class="tm-textarea @error('testimonial') has-error @enderror">{{ old('testimonial') }}</textarea>
                        @error('testimonial')
                            <p class="tm-error">
                                <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Foto --}}
                    <div class="tm-group">
                        <label class="tm-label">
                            <iconify-icon icon="mdi:camera-outline"></iconify-icon>
                            Foto Produk
                            <span class="optional">(opsional)</span>
                        </label>

                        <label class="tm-upload" for="images_{{ $order->id }}">
                            <iconify-icon icon="mdi:cloud-upload-outline"></iconify-icon>
                            <span class="tm-upload-text">Klik untuk memilih foto</span>
                            <span class="tm-upload-hint">Maks 2MB per foto · Maks 10 foto</span>
                            <input type="file"
                                   name="images[]"
                                   id="images_{{ $order->id }}"
                                   accept="image/*"
                                   multiple>
                        </label>

                        <div id="image-preview-{{ $order->id }}" class="tm-preview"></div>

                        @error('images')
                            <p class="tm-error">
                                <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </form>
            </div>

            {{-- FOOTER --}}
            <div class="tm-footer">
                <button type="button"
                        onclick="closeTestimonialModal({{ $order->id }})"
                        class="tm-btn tm-btn-outline">
                    <iconify-icon icon="mdi:close"></iconify-icon>
                    Batal
                </button>
                <button type="button"
                        onclick="submitTestimonialForm({{ $order->id }})"
                        class="tm-btn tm-btn-gold">
                    <iconify-icon icon="mdi:send-outline"></iconify-icon>
                    Kirim Testimonial
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* ============================================
       TESTIMONIAL MODAL STYLES
       ============================================ */
    .testimonial-modal {
        position: fixed;
        inset: 0;
        z-index: 999;
    }

    .tm-overlay {
        position: fixed;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1vw;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(0.3vw);
        -webkit-backdrop-filter: blur(0.3vw);
        overflow-y: auto;
    }

    .tm-box {
        position: relative;
        width: 100%;
        max-width: 40vw;
        max-height: 90vh;
        background: #ffffff;
        border-radius: 1.2vw;
        box-shadow: 0 1.5vw 4vw rgba(0, 0, 0, 0.2);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        animation: tmFadeIn 0.25s ease-out;
        margin: 1vw;
    }

    @keyframes tmFadeIn {
        from { opacity: 0; transform: scale(0.95) translateY(-1vw); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }

    /* --------------------------------------------
       HEADER
       -------------------------------------------- */
    .tm-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1vw;
        padding: 1.3vw 1.4vw;
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
        flex-shrink: 0;
    }

    .tm-header-left {
        display: flex;
        align-items: center;
        gap: 0.7vw;
        min-width: 0;
    }

    .tm-header-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2.4vw;
        height: 2.4vw;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.5);
        color: rgb(102, 72, 9);
        flex-shrink: 0;
    }

    .tm-header-icon iconify-icon {
        font-size: 1.4vw;
    }

    .tm-title {
        font-size: 1vw;
        font-weight: 800;
        color: rgb(102, 72, 9);
        line-height: 1.2;
    }

    .tm-subtitle {
        font-size: 0.72vw;
        color: rgba(102, 72, 9, 0.7);
        margin-top: 0.15vw;
        font-weight: 500;
    }

    .tm-close-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2vw;
        height: 2vw;
        border-radius: 50%;
        border: none;
        background: rgba(255, 255, 255, 0.3);
        color: rgb(102, 72, 9);
        cursor: pointer;
        transition: all 0.2s ease;
        flex-shrink: 0;
        padding: 0;
    }

    .tm-close-btn:hover {
        background: rgba(255, 255, 255, 0.6);
        transform: rotate(90deg);
    }

    .tm-close-btn iconify-icon {
        font-size: 1.2vw;
    }

    /* --------------------------------------------
       BODY
       -------------------------------------------- */
    .tm-body {
        flex: 1;
        overflow-y: auto;
        padding: 1.4vw;
        display: flex;
        flex-direction: column;
        gap: 1.2vw;
    }

    .tm-body::-webkit-scrollbar {
        width: 0.3vw;
    }
    .tm-body::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 100vw;
    }

    .tm-group {
        display: flex;
        flex-direction: column;
        gap: 0.4vw;
        margin-bottom:1vw;
    }

    .tm-label {
        display: flex;
        align-items: center;
        gap: 0.35vw;
        font-size: 0.82vw;
        font-weight: 600;
        color: #334155;
    }

    .tm-label iconify-icon {
        color: #ecbc42;
        font-size: 1vw;
    }

    .tm-label .required {
        color: #dc2626;
    }

    .tm-label .optional {
        color: #94a3b8;
        font-weight: 400;
        font-size: 0.72vw;
    }

    .tm-input,
    .tm-textarea {
        width: 100%;
        padding: 0.8vw 1vw;
        border: 0.1vw solid #e2e8f0;
        border-radius: 0.6vw;
        font-size: 0.82vw;
        color: #0f172a;
        background: #ffffff;
        outline: none;
        transition: all 0.2s ease;
        font-family: inherit;
    }

    .tm-input::placeholder,
    .tm-textarea::placeholder {
        color: #cbd5e1;
    }

    .tm-input:focus,
    .tm-textarea:focus {
        border-color: #ecbc42;
        box-shadow: 0 0 0 0.25vw rgba(236, 188, 66, 0.2);
    }

    .tm-input.has-error,
    .tm-textarea.has-error {
        border-color: #fca5a5;
    }

    .tm-textarea {
        resize: vertical;
        min-height: 5vw;
        line-height: 1.6;
    }

    .tm-error {
        display: flex;
        align-items: center;
        gap: 0.3vw;
        font-size: 0.72vw;
        color: #dc2626;
        margin-top: 0.2vw;
    }

    .tm-error iconify-icon {
        font-size: 0.85vw;
        flex-shrink: 0;
    }

    /* --------------------------------------------
       RATING
       -------------------------------------------- */
    .tm-rating-wrap {
        display: flex;
        align-items: center;
        gap: 0.3vw;
        padding: 0.4vw 0;
    }

    .tm-star-label {
        display: inline-flex;
        cursor: pointer;
        transition: transform 0.15s ease;
    }

    .tm-star-label:hover {
        transform: scale(1.15);
    }

    .tm-star-label input {
        display: none;
    }

    .tm-star {
        font-size: 1.9vw;
        color: #cbd5e1;
        transition: all 0.2s ease;
    }

    .tm-star.is-active {
        color: #f59e0b;
        filter: drop-shadow(0 0.1vw 0.2vw rgba(245, 158, 11, 0.3));
    }

    .tm-rating-label {
        display: inline-flex;
        align-items: center;
        padding: 0.3vw 0.7vw;
        margin-left: 0.5vw;
        border-radius: 100vw;
        background: #f8fafc;
        color: rgb(102, 72, 9);
        font-size: 0.75vw;
        font-weight: 700;
    }

    /* --------------------------------------------
       UPLOAD FOTO
       -------------------------------------------- */
    .tm-upload {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.4vw;
        padding: 1.5vw 1vw;
        border: 0.15vw dashed #cbd5e1;
        border-radius: 0.7vw;
        background: #fafbfc;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
    }

    .tm-upload:hover {
        border-color: #ecbc42;
        background: #fffbf0;
    }

    .tm-upload iconify-icon {
        font-size: 1.8vw;
        color: #ecbc42;
    }

    .tm-upload-text {
        font-size: 0.82vw;
        font-weight: 600;
        color: #475569;
    }

    .tm-upload-hint {
        font-size: 0.7vw;
        color: #94a3b8;
    }

    .tm-upload input[type="file"] {
        display: none;
    }

    .tm-preview {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5vw;
        margin-top: 0.7vw;
    }

    .tm-preview:empty {
        display: none;
    }

    .tm-preview > div {
        position: relative;
        width: 4vw;
        height: 4vw;
        border-radius: 0.6vw;
        overflow: hidden;
        border: 0.1vw solid #e2e8f0;
        background: #f1f5f9;
    }

    .tm-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    /* --------------------------------------------
       FOOTER
       -------------------------------------------- */
    .tm-footer {
        display: flex;
        gap: 0.7vw;
        padding: 1.2vw 1.4vw;
        border-top: 0.1vw solid #f1f5f9;
        background: #fafbfc;
        flex-shrink: 0;
    }

    .tm-btn {
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4vw;
        padding: 0.9vw 1.4vw;
        border-radius: 0.7vw;
        font-size: 0.85vw;
        font-weight: 600;
        cursor: pointer;
        border: 0.1vw solid transparent;
        transition: all 0.2s ease;
        text-decoration: none;
        font-family: inherit;
    }

    .tm-btn iconify-icon {
        font-size: 1.05vw;
    }

    .tm-btn-outline {
        background: #ffffff;
        border-color: #e2e8f0;
        color: #475569;
    }
    .tm-btn-outline:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .tm-btn-gold {
        background: linear-gradient(90deg, #FDDD57 0%, #ecbc42 49.04%, #FDDD57 100%);
        color: rgb(102, 72, 9);
        box-shadow: 0 0.15vw 0.5vw rgba(236, 188, 66, 0.3);
    }
    .tm-btn-gold:hover {
        transform: translateY(-0.1vw);
        box-shadow: 0 0.3vw 1vw rgba(236, 188, 66, 0.45);
    }
    .tm-star-label{
        font-size:1.7vw;
    }

    /* ============================================
       RESPONSIVE - TABLET
       ============================================ */
    @media (max-width: 1024px) {
        .tm-overlay { padding: 2vw; }

        .tm-box {
            max-width: 85vw;
            border-radius: 3vw;
            margin: 2vw;
        }

        .tm-header {
            gap: 2vw;
            padding: 3vw 3.5vw;
        }

        .tm-header-left { gap: 2vw; }

        .tm-header-icon {
            width: 6.5vw;
            height: 6.5vw;
        }
        .tm-header-icon iconify-icon { font-size: 3.5vw; }

        .tm-title { font-size: 2.8vw; }
        .tm-subtitle {
            font-size: 1.9vw;
            margin-top: 0.4vw;
        }

        .tm-close-btn {
            width: 6vw;
            height: 6vw;
        }
        .tm-close-btn iconify-icon { font-size: 3.4vw; }

        .tm-body {
            padding: 4vw;
            gap: 3.5vw;
        }

        .tm-group { gap: 1vw; margin-bottom:1vw;}

        .tm-label {
            font-size: 2.1vw;
            gap: 0.9vw;
        }
        .tm-label iconify-icon { font-size: 2.5vw; }
        .tm-label .optional { font-size: 1.8vw; }

        .tm-input,
        .tm-textarea {
            padding: 2.2vw 3vw;
            border-radius: 1.5vw;
            font-size: 2.2vw;
            border-width: 0.2vw;
        }
        .tm-textarea { min-height: 18vw; }

        .tm-error {
            font-size: 1.8vw;
            gap: 0.9vw;
            margin-top: 0.5vw;
        }
        .tm-error iconify-icon { font-size: 2.2vw; }

        /* Rating */
        .tm-rating-wrap { gap: 1vw; padding: 1.2vw 0; }
        .tm-star { font-size: 5vw; }
        .tm-rating-label {
            font-size: 2vw;
            padding: 0.8vw 1.8vw;
            margin-left: 1.5vw;
            border-radius: 100vw;
        }
        .tm-star-label{
            font-size:1.7vw;
        }

        /* Upload */
        .tm-upload {
            gap: 1vw;
            padding: 4vw 3vw;
            border-radius: 2vw;
            border-width: 0.35vw;
        }
        .tm-upload iconify-icon { font-size: 5vw; }
        .tm-upload-text { font-size: 2.1vw; }
        .tm-upload-hint { font-size: 1.8vw; }

        .tm-preview {
            gap: 1.5vw;
            margin-top: 2vw;
        }
        .tm-preview > div {
            width: 12vw;
            height: 12vw;
            border-radius: 1.7vw;
            border-width: 0.2vw;
        }

        /* Footer */
        .tm-footer {
            gap: 1.7vw;
            padding: 3vw 3.5vw;
            border-top-width: 0.2vw;
        }

        .tm-btn {
            padding: 2.3vw 3vw;
            border-radius: 1.5vw;
            font-size: 2.1vw;
            gap: 1vw;
            border-width: 0.2vw;
        }
        .tm-btn iconify-icon { font-size: 2.5vw; }
    }

    /* ============================================
       RESPONSIVE - MOBILE
       ============================================ */
    @media (max-width: 480px) {
        .tm-overlay {
            padding: 3vw;
            align-items: flex-start;
        }

        .tm-box {
            max-width: 100%;
            max-height: 96vh;
            border-radius: 4vw;
            margin: 3vw 0;
        }

        .tm-header {
            gap: 2.5vw;
            padding: 4vw;
        }

        .tm-header-left { gap: 2.5vw; }

        .tm-header-icon {
            width: 10vw;
            height: 10vw;
        }
        .tm-header-icon iconify-icon { font-size: 5.5vw; }

        .tm-title { font-size: 4.2vw; }
        .tm-subtitle {
            font-size: 2.8vw;
            margin-top: 0.7vw;
        }

        .tm-close-btn {
            width: 9vw;
            height: 9vw;
        }
        .tm-close-btn iconify-icon { font-size: 5vw; }

        .tm-body {
            padding: 5vw 4vw;
            gap: 5vw;
        }

        .tm-group { gap: 1.5vw; margin-bottom:2vw;}
        .tm-star-label {
            font-size:6vw;
        }

        .tm-label {
            font-size: 3.2vw;
            gap: 1.2vw;
        }
        .tm-label iconify-icon { font-size: 4vw; }
        .tm-label .optional { font-size: 2.8vw; }

        .tm-input,
        .tm-textarea {
            padding: 3.2vw 3.5vw;
            border-radius: 2.5vw;
            font-size: 3.2vw;
            border-width: 0.3vw;
        }
        .tm-textarea {
            min-height: 28vw;
            line-height: 1.7;
        }

        .tm-error {
            font-size: 2.8vw;
            gap: 1.2vw;
            margin-top: 0.8vw;
        }
        .tm-error iconify-icon { font-size: 3.4vw; }

        /* Rating */
        .tm-rating-wrap {
            gap: 1.2vw;
            padding: 2.5vw 0;
            flex-wrap: wrap;
        }
        .tm-star { font-size: 8.5vw; }
        .tm-rating-label {
            font-size: 3.2vw;
            padding: 1.2vw 2.8vw;
            margin-left: 2vw;
            border-radius: 100vw;
        }

        /* Upload */
        .tm-upload {
            gap: 1.5vw;
            padding: 7vw 4vw;
            border-radius: 3vw;
            border-width: 0.5vw;
        }
        .tm-upload iconify-icon { font-size: 9vw; }
        .tm-upload-text { font-size: 3.4vw; }
        .tm-upload-hint { font-size: 2.8vw; }

        .tm-preview {
            gap: 2.5vw;
            margin-top: 3.5vw;
        }
        .tm-preview > div {
            width: 20vw;
            height: 20vw;
            border-radius: 2.5vw;
            border-width: 0.3vw;
        }

        /* Footer */
        .tm-footer {
            flex-direction: column-reverse;
            gap: 2.5vw;
            padding: 4vw;
            border-top-width: 0.3vw;
        }

        .tm-btn {
            width: 100%;
            padding: 3.5vw 4vw;
            border-radius: 2.5vw;
            font-size: 3.2vw;
            gap: 1.5vw;
            border-width: 0.3vw;
        }
        .tm-btn iconify-icon { font-size: 4.2vw; }
    }
</style>