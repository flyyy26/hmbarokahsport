@php
    $minPrice = $product->min_price;
    $maxPrice = $product->max_price;
    $activeVariants = $product->variants->where('is_active', true);
    $hasVariants = $activeVariants->count() > 0;
    $firstImage = $product->images->first();
    $totalStock = $hasVariants
        ? $activeVariants->sum('stock')
        : ($product->stock ?? 0);

    $productData = json_encode([
        'product_id'   => $product->id,
        'product_name' => $product->name,
        'price'        => $hasVariants ? $minPrice : $product->price,
        'stock'        => (int) ($activeVariants->sum('stock') ?? 0),
    ]);
@endphp

<div class="rounded-2xl border overflow-hidden transition-all duration-200 group
            hover:-translate-y-0.5"
     style="background: var(--bg-card);
            border-color: var(--border-2);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);"
     onmouseover="this.style.borderColor='rgba(236,188,66,0.4)'; this.style.boxShadow='0 8px 20px rgba(236,188,66,0.1)';"
     onmouseout="this.style.borderColor='var(--border-2)'; this.style.boxShadow='0 1px 3px rgba(0,0,0,0.05)';">

    {{-- ============================================ --}}
    {{-- PRODUCT HEADER (Gambar + Info) --}}
    {{-- ============================================ --}}
    <div class="p-3 flex items-start gap-3">

        {{-- Product Image --}}
        <div class="relative flex-shrink-0 w-16 h-16 rounded-xl overflow-hidden border"
             style="background: var(--bg-input); border-color: var(--border-1);">
            @if($firstImage)
                <img src="{{ asset('storage/' . $firstImage->image) }}"
                     alt="{{ $product->name }}"
                     class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <iconify-icon icon="mdi:image-off-outline" class="text-xl"
                                  style="color: var(--text-5);"></iconify-icon>
                </div>
            @endif

            {{-- Variant Badge --}}
            @if($hasVariants)
                <span class="absolute top-1 right-1 inline-flex items-center gap-0.5
                             px-1.5 py-0.5 rounded-md
                             text-[9px] font-bold tracking-wider
                             backdrop-blur-sm"
                      style="background: rgba(96,165,250,0.9); color: white;">
                    <iconify-icon icon="mdi:layers-triple" class="text-[8px]"></iconify-icon>
                    {{ $activeVariants->count() }}
                </span>
            @endif
        </div>

        {{-- Product Info --}}
        <div class="flex-1 min-w-0">
            <p class="font-bold text-sm leading-tight line-clamp-2"
               style="color: var(--text-1);"
               title="{{ $product->name }}">
                {{ $product->name }}
            </p>

            {{-- Price --}}
            <div class="flex items-baseline gap-1 mt-1.5">
                @if($hasVariants)
                    <span class="text-sm font-bold font-mono" style="color: #ecbc42;">
                        Rp {{ number_format($minPrice, 0, ',', '.') }}
                    </span>
                    @if($minPrice != $maxPrice)
                        <span class="text-[10px]" style="color: var(--text-5);">
                            − Rp {{ number_format($maxPrice, 0, ',', '.') }}
                        </span>
                    @endif
                @else
                    <span class="text-sm font-bold font-mono" style="color: #ecbc42;">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </span>
                @endif
            </div>

            {{-- Stock Info --}}
            <div class="flex items-center gap-2 mt-1">
                @if($totalStock > 0)
                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold"
                          style="color: {{ $totalStock <= 5 ? '#fbbf24' : '#34d399' }};">
                        <span class="w-1 h-1 rounded-full" style="background: currentColor;"></span>
                        Stok: {{ $totalStock }}
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold"
                          style="color: #f87171;">
                        <span class="w-1 h-1 rounded-full" style="background: currentColor;"></span>
                        Stok habis
                    </span>
                @endif
            </div>
        </div>
    </div>


    {{-- ============================================ --}}
    {{-- VARIANT LIST (jika ada) --}}
    {{-- ============================================ --}}
    @if($hasVariants)
        <div class="px-3 pb-3">
            <div class="rounded-lg border overflow-hidden"
                 style="border-color: var(--border-1); background: var(--bg-input);">

                {{-- Header --}}
                <div class="px-2.5 py-1.5 border-b flex items-center justify-between"
                     style="border-color: var(--border-1);">
                    <span class="text-[9px] font-bold uppercase tracking-wider flex items-center gap-1"
                          style="color: var(--text-5);">
                        <iconify-icon icon="mdi:format-list-bulleted"></iconify-icon>
                        Pilih Varian
                    </span>
                    <span class="text-[9px] font-mono" style="color: var(--text-6);">
                        {{ $activeVariants->count() }} varian
                    </span>
                </div>

                {{-- Variant Items (scrollable) --}}
                <div class="max-h-[140px] overflow-y-auto">
                    @foreach($activeVariants as $variant)
                        @php
                            $effectivePrice = $variant->discount_price && $variant->discount_price < $variant->price
                                ? $variant->discount_price
                                : $variant->price;
                            $hasDiscount = $variant->discount_price && $variant->discount_price < $variant->price;
                            $variantLabel = $variant->option_combination ?: 'Standard';
                            $stock = $variant->stock ?? 0;
                            $isOutOfStock = $stock <= 0;

                            $variantData = json_encode([
                                'product_id'   => $product->id,
                                'product_name' => $product->name,
                                'price'        => $effectivePrice,
                                'variant_id'   => $variant->id,
                                'variant_name' => $variantLabel,
                                'stock'        => (int) $stock,
                            ]);
                        @endphp

                        <button type="button"
                                class="variant-option w-full flex items-center justify-between gap-2
                                       px-2.5 py-2 border-b last:border-b-0
                                       transition-all text-left
                                       {{ $isOutOfStock ? 'cursor-not-allowed opacity-50' : 'cursor-pointer hover:bg-[rgba(236,188,66,0.08)] active:scale-[0.98]' }}"
                                style="border-color: var(--border-1);"
                                data-variant="{{ $variantData }}"
                                {{ $isOutOfStock ? 'disabled' : '' }}>

                            {{-- Left: Label --}}
                            <div class="flex items-center gap-2 min-w-0 flex-1">
                                <span class="inline-flex items-center justify-center w-5 h-5 rounded
                                             text-[9px] font-bold flex-shrink-0"
                                      style="background: rgba(236,188,66,0.12); color: #ecbc42;">
                                    <iconify-icon icon="mdi:tag-outline" class="text-[10px]"></iconify-icon>
                                </span>
                                <span class="text-[11px] font-semibold truncate"
                                      style="color: var(--text-2);"
                                      title="{{ $variantLabel }}">
                                    {{ $variantLabel }}
                                </span>

                                @if($isOutOfStock)
                                    <span class="inline-flex items-center gap-0.5 px-1 py-0.5 rounded
                                                 text-[8px] font-bold flex-shrink-0"
                                          style="background: rgba(248,113,113,0.15); color: #f87171;">
                                        HABIS
                                    </span>
                                @elseif($stock <= 5)
                                    <span class="inline-flex items-center gap-0.5 px-1 py-0.5 rounded
                                                 text-[8px] font-bold flex-shrink-0"
                                          style="background: rgba(251,191,36,0.15); color: #fbbf24;">
                                        SISA {{ $stock }}
                                    </span>
                                @endif
                            </div>

                            {{-- Right: Price --}}
                            <div class="text-right flex-shrink-0">
                                <div class="flex items-center gap-1 justify-end">
                                    @if($hasDiscount)
                                        <span class="text-[9px] line-through font-mono" style="color: var(--text-5);">
                                            {{ number_format($variant->price, 0, ',', '.') }}
                                        </span>
                                    @endif
                                    <span class="text-[11px] font-bold font-mono"
                                          style="color: {{ $hasDiscount ? '#34d399' : '#ecbc42' }};">
                                        Rp {{ number_format($effectivePrice, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>


    {{-- ============================================ --}}
    {{-- NO VARIANT — Direct Add Button --}}
    {{-- ============================================ --}}
    @else
        <div class="px-3 pb-3">
            @php
                $isOutOfStock = ($product->stock ?? 0) <= 0;
            @endphp

            <button type="button"
                    class="product-option w-full flex items-center justify-between gap-2
                           px-3 py-2.5 rounded-lg border transition-all
                           {{ $isOutOfStock ? 'cursor-not-allowed opacity-50' : 'cursor-pointer hover:border-[#ecbc42] hover:bg-[rgba(236,188,66,0.08)] active:scale-[0.98]' }}"
                    style="background: var(--bg-input); border-color: var(--border-1);"
                    data-product="{{ $productData }}"
                    {{ $isOutOfStock ? 'disabled' : '' }}>

                <span class="flex items-center gap-1.5 text-[11px] font-bold"
                      style="color: var(--text-2);">
                    <iconify-icon icon="{{ $isOutOfStock ? 'mdi:cart-off' : 'mdi:cart-plus' }}"
                                  class="text-sm"
                                  style="color: {{ $isOutOfStock ? '#f87171' : '#ecbc42' }};"></iconify-icon>
                    {{ $isOutOfStock ? 'Stok Habis' : 'Tambah ke Pesanan' }}
                </span>

                @if(!$isOutOfStock)
                    <span class="text-[11px] font-bold font-mono" style="color: #ecbc42;">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </span>
                @endif
            </button>
        </div>
    @endif
</div>