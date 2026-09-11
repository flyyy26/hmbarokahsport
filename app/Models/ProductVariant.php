<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'discount_price',
        'stock',
        'weight',
        'is_active',
        'image',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'stock' => 'integer',
        'weight' => 'integer',
        'is_active' => 'boolean',
    ];

    // ============================================
    // RELATIONS
    // ============================================

    /**
     * Relasi ke Product (belongs to)
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi ke ProductVariantValue (has many)
     * Melalui tabel product_variant_values
     */
    public function variantValues(): HasMany
    {
        return $this->hasMany(ProductVariantValue::class, 'product_variant_id');
    }

    /**
     * 🔥 RELASI KE PRODUCT_OPTION_VALUES (MANY-TO-MANY)
     * Melalui tabel product_variant_values
     * 
     * Digunakan untuk mendapatkan nilai-nilai option dari varian ini
     * Contoh: $variant->values->pluck('value') -> ['Merah', 'M']
     */
    public function values(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductOptionValue::class,
            'product_variant_values',
            'product_variant_id',
            'product_option_value_id'
        )->withTimestamps();
    }

    public function getCombinationAttribute(): string
    {
        if (!$this->relationLoaded('variantValues')) {
            $this->load('variantValues.optionValue');
        }
        
        return $this->variantValues
            ->map(function($vv) {
                return $vv->optionValue->value ?? '';
            })
            ->filter()
            ->implode(' - ');
    }

    public function getOptionValueNamesAttribute(): array
    {
        if (!$this->relationLoaded('variantValues')) {
            $this->load('variantValues.optionValue');
        }
        
        return $this->variantValues
            ->map(function($vv) {
                return $vv->optionValue->value ?? '';
            })
            ->filter()
            ->values()
            ->toArray();
    }

    public function options(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductOption::class,
            'product_variant_values',
            'product_variant_id',
            'product_option_value_id'
        );
    }

    public function stockHistories(): HasMany
    {
        return $this->hasMany(StockHistory::class, 'product_variant_id');
    }

    public function updateStock(int $newStock, string $reason = 'adjustment', ?string $note = null): bool
    {
        $oldStock = $this->stock;
        
        if ($oldStock == $newStock) {
            return true;
        }

        $quantityChange = $newStock - $oldStock;

        // 🔥 UPDATE STOK
        $this->stock = $newStock;
        $saved = $this->save();

        if ($saved) {
            // 🔥 CATAT HISTORY
            $this->recordHistory($oldStock, $newStock, $quantityChange, $reason, $note);
        }

        return $saved;
    }

    public function addStock(int $quantity, string $reason = 'restock', ?string $note = null): bool
    {
        $newStock = $this->stock + $quantity;
        return $this->updateStock($newStock, $reason, $note);
    }

    public function reduceStock(int $quantity, string $reason = 'sale', ?string $note = null): bool
    {
        if ($this->stock < $quantity) {
            return false;
        }
        $newStock = $this->stock - $quantity;
        return $this->updateStock($newStock, $reason, $note);
    }

    public function recordHistory(int $oldStock, int $newStock, int $quantityChange, string $reason, ?string $note = null): void
    {
        $this->stockHistories()->create([
            'product_id' => $this->product_id,
            'product_variant_id' => $this->id,
            'user_id' => auth()->id(),
            'old_stock' => $oldStock,
            'new_stock' => $newStock,
            'quantity_change' => $quantityChange,
            'reason' => $reason,
            'note' => $note,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    // ============================================
    // 🔥 EFFECTIVE PRICE WITH FLASH SALE PRIORITY
    // ============================================

    /**
     * Mendapatkan harga efektif dengan prioritas:
     * 1. Flash Sale (tertinggi)
     * 2. Diskon Varian
     * 3. Diskon Produk
     * 4. Harga Original
     */
    public function getEffectivePriceAttribute()
    {
        // 1. Check Flash Sale (highest priority)
        if ($this->product && $this->product->isOnFlashSale()) {
            $flashPrice = $this->product->calculateFlashSaleDiscount($this->price);
            if ($flashPrice !== null && $flashPrice < $this->price) {
                return round($flashPrice, 2);
            }
        }
        
        // 2. Check variant discount
        if ($this->discount_price !== null && $this->discount_price < $this->price) {
            return round($this->discount_price, 2);
        }
        
        // 3. Check product discount
        if ($this->product && $this->product->isOnProductDiscount()) {
            $productDiscountPrice = $this->product->calculateProductDiscount($this->price);
            if ($productDiscountPrice !== null && $productDiscountPrice < $this->price) {
                return round($productDiscountPrice, 2);
            }
        }
        
        // 4. Original price
        return round($this->price, 2);
    }

    /**
     * Mendapatkan persentase diskon berdasarkan harga efektif
     */
    public function getDiscountPercentAttribute()
    {
        $effectivePrice = $this->effective_price;
        if ($effectivePrice < $this->price && $this->price > 0) {
            return round((($this->price - $effectivePrice) / $this->price) * 100, 2);
        }
        return 0;
    }

    /**
     * Mendapatkan label harga untuk ditampilkan
     */
    public function getPriceLabelAttribute(): string
    {
        $effectivePrice = $this->effective_price;
        if ($effectivePrice < $this->price) {
            return 'Rp ' . number_format($effectivePrice, 0, ',', '.') . 
                   ' (diskon ' . number_format($this->discount_percent, 0) . '%)';
        }
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Mendapatkan harga diskon final (null jika tidak ada diskon)
     */
    public function getFinalDiscountPriceAttribute(): ?float
    {
        // 1. Cek flash sale
        if ($this->product && $this->product->isOnFlashSale()) {
            $flashPrice = $this->product->calculateFlashSaleDiscount($this->price);
            if ($flashPrice !== null && $flashPrice < $this->price) {
                return (float) $flashPrice;
            }
        }

        // 2. Cek diskon varian (individual)
        if ($this->discount_price && $this->discount_price < $this->price) {
            return (float) $this->discount_price;
        }

        // 3. Cek diskon produk (global)
        if ($this->product && $this->product->isOnProductDiscount()) {
            return $this->product->calculateProductDiscount($this->price);
        }

        return null;
    }

    /**
     * Cek apakah varian memiliki diskon (flash sale, variant, atau product)
     */
    public function getHasAnyDiscountAttribute(): bool
    {
        return $this->discount_percent > 0;
    }

    /**
     * Mendapatkan label diskon
     */
    public function getDiscountLabelAttribute(): string
    {
        if ($this->discount_percent > 0) {
            // Cek jenis diskon
            if ($this->product && $this->product->isOnFlashSale()) {
                return '⚡ Flash Sale ' . $this->discount_percent . '%';
            }
            return 'Diskon ' . $this->discount_percent . '%';
        }
        return '';
    }

    /**
     * Mendapatkan tipe diskon yang aktif (flash_sale, variant, product, atau null)
     */
    public function getDiscountTypeAttribute(): ?string
    {
        if ($this->product && $this->product->isOnFlashSale()) {
            return 'flash_sale';
        }
        
        if ($this->discount_price && $this->discount_price < $this->price) {
            return 'variant';
        }
        
        if ($this->product && $this->product->isOnProductDiscount()) {
            return 'product';
        }
        
        return null;
    }

    /**
     * Mendapatkan badge label untuk diskon
     */
    public function getDiscountBadgeAttribute(): string
    {
        $type = $this->discount_type;
        $percent = $this->discount_percent;
        
        if ($percent <= 0) return '';
        
        if ($type === 'flash_sale') {
            return '⚡ ' . $percent . '%';
        }
        
        return $percent . '%';
    }

    // ============================================
    // STOCK STATUS
    // ============================================

    /**
     * Mendapatkan status stok (in_stock / out_of_stock / low_stock)
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->stock <= 0) {
            return 'out_of_stock';
        }
        if ($this->stock <= 5) {
            return 'low_stock';
        }
        return 'in_stock';
    }

    /**
     * Mendapatkan label status stok
     */
    public function getStockStatusLabelAttribute(): string
    {
        return [
            'out_of_stock' => 'Habis',
            'low_stock' => 'Stok Terbatas',
            'in_stock' => 'Tersedia',
        ][$this->stock_status] ?? 'Tersedia';
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Scope untuk varian yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk varian yang memiliki stok
     */
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Scope untuk varian yang sedang diskon (termasuk flash sale)
     */
    public function scopeOnSale($query)
    {
        return $query->where(function($q) {
            $q->whereNotNull('discount_price')
              ->whereColumn('discount_price', '<', 'price')
              ->orWhereHas('product', function($pq) {
                  $pq->where('is_flash_sale', true)
                     ->whereNotNull('flash_sale_value')
                     ->where('flash_sale_value', '>', 0)
                     ->where('flash_sale_start_date', '<=', now())
                     ->where('flash_sale_end_date', '>=', now());
              })
              ->orWhereHas('product', function($pq) {
                  $pq->where('has_product_discount', true)
                     ->whereNotNull('discount_value')
                     ->where('discount_value', '>', 0);
              });
        });
    }

    /**
     * Scope untuk varian yang sedang flash sale
     */
    public function scopeOnFlashSale($query)
    {
        return $query->whereHas('product', function($pq) {
            $pq->where('is_flash_sale', true)
               ->whereNotNull('flash_sale_value')
               ->where('flash_sale_value', '>', 0)
               ->where('flash_sale_start_date', '<=', now())
               ->where('flash_sale_end_date', '>=', now());
        });
    }

    // ============================================
    // HELPERS
    // ============================================

    /**
     * Cek apakah varian ini memiliki stok
     */
    public function hasStock(): bool
    {
        return $this->stock > 0;
    }

    /**
     * Cek apakah varian ini sedang diskon (termasuk flash sale)
     */
    public function isOnSale(): bool
    {
        return $this->discount_percent > 0;
    }

    /**
     * Cek apakah varian ini sedang flash sale
     */
    public function isOnFlashSale(): bool
    {
        return $this->discount_type === 'flash_sale';
    }

    /**
     * Mendapatkan kombinasi option values sebagai string
     * Contoh: "Merah / XL"
     */
    public function getOptionCombinationAttribute(): string
    {
        if (!$this->relationLoaded('values')) {
            $this->load('values');
        }

        return $this->values->pluck('value')->implode(' / ');
    }

    /**
     * Mendapatkan option values sebagai array
     */
    public function getOptionValuesArray(): array
    {
        if (!$this->relationLoaded('values')) {
            $this->load('values');
        }

        return $this->values->pluck('id')->toArray();
    }

    /**
     * Cek apakah varian ini memiliki kombinasi option values tertentu
     */
    public function hasOptionValues(array $valueIds): bool
    {
        if (!$this->relationLoaded('values')) {
            $this->load('values');
        }

        $variantValueIds = $this->values->pluck('id')->toArray();
        return empty(array_diff($valueIds, $variantValueIds));
    }

    /**
     * Kurangi stok
     */
    public function decreaseStock(int $quantity): bool
    {
        if ($this->stock < $quantity) {
            return false;
        }

        $this->stock -= $quantity;
        return $this->save();
    }

    /**
     * Tambah stok
     */
    public function increaseStock(int $quantity): bool
    {
        $this->stock += $quantity;
        return $this->save();
    }

    /**
     * Mendapatkan harga original dengan format Rupiah
     */
    public function getOriginalPriceFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Mendapatkan harga efektif dengan format Rupiah
     */
    public function getEffectivePriceFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->effective_price, 0, ',', '.');
    }

    /**
     * Mendapatkan informasi diskon lengkap
     */
    public function getDiscountInfoAttribute(): array
    {
        if ($this->discount_percent <= 0) {
            return [
                'has_discount' => false,
                'percent' => 0,
                'type' => null,
                'label' => '',
                'original_price' => $this->price,
                'final_price' => $this->price,
            ];
        }

        return [
            'has_discount' => true,
            'percent' => $this->discount_percent,
            'type' => $this->discount_type,
            'label' => $this->discount_label,
            'original_price' => $this->price,
            'final_price' => $this->effective_price,
        ];
    }
}