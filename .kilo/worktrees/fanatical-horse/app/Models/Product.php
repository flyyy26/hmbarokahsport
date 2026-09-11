<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'gender',
        'material',
        'is_featured',
        'is_best_seller', 
        'is_active',
        'minimum_stock',
        'restock_threshold',
        'has_product_discount',
        'discount_type',
        'discount_value',
        'is_flash_sale',
        'flash_sale_type',
        'flash_sale_value',
        'flash_sale_start_date',
        'flash_sale_end_date',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_best_seller' => 'boolean',
        'is_active' => 'boolean',
        'minimum_stock' => 'integer',
        'restock_threshold' => 'integer',
        'has_product_discount' => 'boolean',
        'discount_value' => 'decimal:2',
        'is_flash_sale' => 'boolean',
        'flash_sale_value' => 'decimal:2',
        'flash_sale_start_date' => 'datetime',
        'flash_sale_end_date' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function options(): HasMany
    {
        return $this->hasMany(ProductOption::class)->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ============================================
    // 🔥 ACCESSORS UNTUK HARGA
    // ============================================

    public function stockHistories(): HasMany
    {
        return $this->hasMany(StockHistory::class);
    }

    // 🔥 GET LATEST STOCK HISTORY
    public function getLatestStockHistoryAttribute()
    {
        return $this->stockHistories()->latest()->first();
    }

    /**
     * Mendapatkan harga termurah dari semua varian
     */
    public function getMinPriceAttribute()
    {
        return (float) $this->variants->min('discount_price') ?: (float) $this->variants->min('price') ?: 0;
    }

    /**
     * Mendapatkan harga termahal dari semua varian
     */
    public function getMaxPriceAttribute()
    {
        return (float) $this->variants->max('price') ?: 0;
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'product_features')
                    ->withTimestamps();
    }

    /**
     * Mendapatkan harga dari varian pertama (untuk single price)
     */
    public function getPriceAttribute()
    {
        $firstVariant = $this->variants->first();
        return $firstVariant ? (float) $firstVariant->price : 0;
    }

    public function isOnFlashSale(): bool
    {
        if (!$this->is_flash_sale || !$this->flash_sale_value || $this->flash_sale_value <= 0) {
            return false;
        }

        $now = now();
        if ($this->flash_sale_start_date && $now->lt($this->flash_sale_start_date)) {
            return false;
        }

        if ($this->flash_sale_end_date && $now->gt($this->flash_sale_end_date)) {
            return false;
        }

        return true;
    }

    public function calculateFlashSaleDiscount(float $price): ?float
    {
        if (!$this->isOnFlashSale()) {
            return null;
        }

        if ($this->flash_sale_type === 'percentage') {
            $discountAmount = $price * ($this->flash_sale_value / 100);
            return max(round($price - $discountAmount, 2), 0);
        } elseif ($this->flash_sale_type === 'fixed') {
            return max(round($price - $this->flash_sale_value, 2), 0);
        }

        return null;
    }

    public function getFlashSaleDiscountPercent(float $price): float
    {
        if (!$this->isOnFlashSale() || $price <= 0) {
            return 0;
        }

        if ($this->flash_sale_type === 'percentage') {
            return (float) $this->flash_sale_value;
        } elseif ($this->flash_sale_type === 'fixed') {
            return round(($this->flash_sale_value / $price) * 100, 2);
        }

        return 0;
    }

    public function getFlashSaleStatusAttribute(): string
    {
        if (!$this->is_flash_sale) {
            return 'inactive';
        }

        if (!$this->flash_sale_value || $this->flash_sale_value <= 0) {
            return 'inactive';
        }

        $now = now();
        if ($this->flash_sale_start_date && $now->lt($this->flash_sale_start_date)) {
            return 'upcoming';
        }
        if ($this->flash_sale_end_date && $now->gt($this->flash_sale_end_date)) {
            return 'expired';
        }

        return 'active';
    }

    public function getFlashSaleStatusLabelAttribute(): string
    {
        return [
            'active' => '✅ Aktif',
            'inactive' => '❌ Tidak Aktif',
            'upcoming' => '📅 Akan Datang',
            'expired' => '⏳ Kadaluarsa',
        ][$this->flash_sale_status] ?? 'Tidak Aktif';
    }

    public function getFlashSaleStatusColorAttribute(): string
    {
        return [
            'active' => 'green',
            'inactive' => 'gray',
            'upcoming' => 'yellow',
            'expired' => 'red',
        ][$this->flash_sale_status] ?? 'gray';
    }

    public function getFlashSaleLabelAttribute(): string
    {
        if (!$this->isOnFlashSale()) {
            return '';
        }

        if ($this->flash_sale_type === 'percentage') {
            return 'Flash Sale ' . (float) $this->flash_sale_value . '%';
        } elseif ($this->flash_sale_type === 'fixed') {
            return 'Flash Sale Rp ' . number_format($this->flash_sale_value, 0, ',', '.');
        }

        return '';
    }

    public function isFlashSaleActive(): bool
    {
        return $this->isOnFlashSale();
    }

    public function isOnProductDiscount(): bool
    {
        // Cek apakah fitur diskon produk diaktifkan
        if (!$this->has_product_discount) {
            return false;
        }

        // Cek apakah nilai diskon valid
        if (!$this->discount_value || $this->discount_value <= 0) {
            return false;
        }

        // REMOVE date checks - diskon selalu aktif jika diaktifkan
        return true;
    }

    public function calculateProductDiscount(float $price): ?float
    {
        if (!$this->isOnProductDiscount()) {
            return null;
        }

        if ($this->discount_type === 'percentage') {
            // Diskon persentase
            $discountAmount = $price * ($this->discount_value / 100);
            $result = round($price - $discountAmount, 2);
            return max($result, 0);
        } elseif ($this->discount_type === 'fixed') {
            // Diskon fixed (potongan harga)
            $result = round($price - $this->discount_value, 2);
            return max($result, 0);
        }

        return null;
    }

    public function getProductDiscountPercent(float $price): float
    {
        if (!$this->isOnProductDiscount() || $price <= 0) {
            return 0;
        }

        if ($this->discount_type === 'percentage') {
            return (float) $this->discount_value;
        } elseif ($this->discount_type === 'fixed') {
            // Fixed discount dihitung persentasenya dari harga
            return round(($this->discount_value / $price) * 100, 2);
        }

        return 0;
    }

    public function getProductDiscountLabel(): string
    {
        if (!$this->isOnProductDiscount()) {
            return '';
        }

        if ($this->discount_type === 'percentage') {
            return 'Diskon ' . (float) $this->discount_value . '%';
        } elseif ($this->discount_type === 'fixed') {
            return 'Potongan Rp ' . number_format($this->discount_value, 0, ',', '.');
        }

        return '';
    }

    public function getProductDiscountStatusAttribute(): string
    {
        if (!$this->has_product_discount) {
            return 'inactive';
        }

        if (!$this->discount_value || $this->discount_value <= 0) {
            return 'inactive';
        }

        // Always active if discount is set
        return 'active';
    }

    public function getProductDiscountStatusLabelAttribute(): string
    {
        return [
            'active' => '✅ Aktif',
            'inactive' => '❌ Tidak Aktif',
        ][$this->product_discount_status] ?? 'Tidak Aktif';
    }

    public function getProductDiscountStatusColorAttribute(): string
    {
        return [
            'active' => 'green',
            'inactive' => 'gray',
        ][$this->product_discount_status] ?? 'gray';
    }

    /**
     * Mendapatkan harga diskon dari varian pertama (untuk single price)
     */
    public function getDiscountPriceAttribute()
    {
        $firstVariant = $this->variants->first();
        return $firstVariant ? (float) $firstVariant->discount_price : null;
    }

    /**
     * Mendapatkan harga efektif (diskon jika ada) dari varian pertama
     */
    public function getEffectivePriceAttribute()
    {
        $firstVariant = $this->variants->first();
        if ($firstVariant) {
            return $firstVariant->discount_price ? (float) $firstVariant->discount_price : (float) $firstVariant->price;
        }
        return 0;
    }

    /**
     * 🔥 Mendapatkan range harga sebagai string
     * Contoh: "Rp 58.400 - Rp 64.800"
     * Atau jika harga sama: "Rp 58.400"
     */
    public function getPriceRangeAttribute()
    {
        $minPrice = $this->min_price;
        $maxPrice = $this->max_price;

        // Jika tidak ada varian
        if ($minPrice == 0 && $maxPrice == 0) {
            return 'Rp 0';
        }

        // Jika harga min dan max sama
        if ($minPrice == $maxPrice) {
            return 'Rp ' . number_format($minPrice, 0, ',', '.');
        }

        // Jika ada diskon, cek harga diskon terkecil dan terbesar
        $minDiscount = (float) $this->variants->min('discount_price');
        $maxDiscount = (float) $this->variants->max('discount_price');
        
        // Jika semua varian memiliki diskon
        if ($minDiscount > 0 && $this->variants->every(function($v) { return $v->discount_price !== null; })) {
            $minDisplay = $minDiscount;
            $maxDisplay = $maxDiscount;
        } else {
            // Cari harga terendah (bisa diskon atau normal)
            $prices = [];
            foreach ($this->variants as $variant) {
                $prices[] = $variant->discount_price ? (float) $variant->discount_price : (float) $variant->price;
            }
            $minDisplay = min($prices);
            $maxDisplay = max($prices);
        }

        if ($minDisplay == $maxDisplay) {
            return 'Rp ' . number_format($minDisplay, 0, ',', '.');
        }

        return 'Rp ' . number_format($minDisplay, 0, ',', '.') . ' - Rp ' . number_format($maxDisplay, 0, ',', '.');
    }

    /**
     * 🔥 Mendapatkan range harga dengan label diskon
     * Contoh: "Rp 58.400 - Rp 64.800"
     */
    public function getPriceRangeLabelAttribute()
    {
        return $this->price_range;
    }

    /**
     * Mendapatkan harga terendah dengan format Rupiah
     */
    public function getMinPriceFormattedAttribute()
    {
        return 'Rp ' . number_format($this->min_price, 0, ',', '.');
    }

    /**
     * Mendapatkan harga tertinggi dengan format Rupiah
     */
    public function getMaxPriceFormattedAttribute()
    {
        return 'Rp ' . number_format($this->max_price, 0, ',', '.');
    }

    // ============================================
    // ACCESSORS LAINNYA
    // ============================================

    public function getStockAttribute()
    {
        return (int) $this->variants->sum('stock');
    }

    public function getWeightAttribute()
    {
        $firstVariant = $this->variants->first();
        return $firstVariant ? $firstVariant->weight : 1000;
    }

    public function getOriginalPriceRangeAttribute()
    {
        $minPrice = $this->variants->min('price');
        $maxPrice = $this->variants->max('price');

        if ($minPrice == $maxPrice) {
            return 'Rp ' . number_format($minPrice, 0, ',', '.');
        }

        return 'Rp ' . number_format($minPrice, 0, ',', '.') . ' - Rp ' . number_format($maxPrice, 0, ',', '.');
    }

    public function getOriginalMinPriceAttribute()
    {
        return (float) $this->variants->min('price') ?: 0;
    }

    public function getHasDiscountAttribute(): bool
    {
        return $this->variants->contains(function($variant) {
            return $variant->discount_price && $variant->discount_price < $variant->price;
        });
    }

    public function getMaxDiscountPercentAttribute(): float
    {
        return (float) $this->variants->max('discount_percent') ?: 0;
    }

    public function getOriginalMaxPriceAttribute()
    {
        return (float) $this->variants->max('price') ?: 0;
    }

    public function hasStock(): bool
    {
        return $this->variants->sum('stock') > 0;
    }

    public function isOutOfStock(): bool
    {
        return !$this->hasStock();
    }

    public function getTotalStockAttribute(): int
    {
        return (int) $this->variants->sum('stock');
    }

    public function getStockStatusAttribute()
    {
        $totalStock = $this->total_stock;
        $minStock = $this->minimum_stock ?? 5;
        $restockThreshold = $this->restock_threshold ?? 10;

        if ($totalStock <= 0) {
            return 'out_of_stock';
        }

        if ($totalStock <= $minStock) {
            return 'critical';
        }

        if ($totalStock <= $restockThreshold) {
            return 'low';
        }

        return 'in_stock';
    }

    public function getStockStatusLabelAttribute()
    {
        return [
            'out_of_stock' => 'Habis',
            'critical' => 'Kritis 🚨',
            'low' => 'Menipis ⚠️',
            'in_stock' => 'Aman ✅',
        ][$this->stock_status] ?? 'Aman';
    }

    public function getStockStatusColorAttribute()
    {
        return [
            'out_of_stock' => 'red',
            'critical' => 'red',
            'low' => 'yellow',
            'in_stock' => 'green',
        ][$this->stock_status] ?? 'green';
    }

    public function scopeCriticalStock($query)
    {
        return $query->whereHas('variants', function($q) {
            $q->selectRaw('SUM(stock) as total_stock')
            ->havingRaw('SUM(stock) <= COALESCE(products.minimum_stock, 5)'); // 🔥 PERBAIKI: gunakan default 5
        });
    }

    public function scopeLowStock($query)
    {
        return $query->whereHas('variants', function($q) {
            $q->selectRaw('SUM(stock) as total_stock')
            ->havingRaw('SUM(stock) <= COALESCE(products.restock_threshold, 10) AND SUM(stock) > COALESCE(products.minimum_stock, 5)'); // 🔥 PERBAIKI
        });
    }

    public function scopeInStock($query)
    {
        return $query->whereHas('variants', function($q) {
            $q->selectRaw('SUM(stock) as total_stock')
            ->havingRaw('SUM(stock) > COALESCE(products.restock_threshold, 10)'); // 🔥 PERBAIKI
        });
    }
}