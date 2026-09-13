<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ============================================
        // 🔥 PRODUCTS - INDEXES
        // ============================================
        Schema::table('products', function (Blueprint $table) {
            // Filter utama katalog (is_active + sorting)
            $table->index(['is_active', 'created_at'], 'idx_products_active_created');

            // Filter kategori
            $table->index(['is_active', 'category_id'], 'idx_products_active_category');

            // Filter gender
            $table->index(['is_active', 'gender'], 'idx_products_active_gender');

            // Flash sale (query flash sale page)
            $table->index(
                ['is_active', 'is_flash_sale', 'flash_sale_end_date'],
                'idx_products_flash'
            );

            // Slug lookup (show page)
            $table->index('slug', 'idx_products_slug');

            // Product discount (promo page)
            $table->index(
                ['is_active', 'has_product_discount', 'discount_value'],
                'idx_products_product_discount'
            );
        });

        // ============================================
        // 🔥 PRODUCT VARIANTS - INDEXES
        // ============================================
        Schema::table('product_variants', function (Blueprint $table) {
            // Stok per produk (untuk total stock)
            $table->index(['product_id', 'stock'], 'idx_variants_product_stock');

            // Harga (untuk sorting price_asc / price_desc)
            $table->index(['product_id', 'price'], 'idx_variants_product_price');

            // Diskon varian
            $table->index(
                ['product_id', 'discount_price'],
                'idx_variants_product_discount'
            );

            // Filter varian aktif
            $table->index(['product_id', 'is_active'], 'idx_variants_product_active');
        });

        // ============================================
        // 🔥 PRODUCT VARIANT VALUES (pivot) - INDEXES
        // ============================================
        Schema::table('product_variant_values', function (Blueprint $table) {
            // Lookup kombinasi varian → value
            $table->index(
                ['product_variant_id', 'product_option_value_id'],
                'idx_pvv_variant_value'
            );

            // Reverse lookup (value → varian) — untuk filter size/color
            $table->index('product_option_value_id', 'idx_pvv_value');
        });

        // ============================================
        // 🔥 PRODUCT IMAGES - INDEXES
        // ============================================
        Schema::table('product_images', function (Blueprint $table) {
            // Ambil gambar pertama per produk (sort_order)
            $table->index(
                ['product_id', 'sort_order'],
                'idx_images_product_sort'
            );
        });

        // ============================================
        // 🔥 PRODUCT OPTIONS - INDEXES
        // ============================================
        Schema::table('product_options', function (Blueprint $table) {
            $table->index(
                ['product_id', 'sort_order'],
                'idx_options_product_sort'
            );
        });

        // ============================================
        // 🔥 PRODUCT OPTION VALUES - INDEXES
        // ============================================
        Schema::table('product_option_values', function (Blueprint $table) {
            // Ambil value per option (sorted)
            $table->index(
                ['product_option_id', 'sort_order'],
                'idx_option_values_sort'
            );

            // Filter by value (size/color filter)
            $table->index('value', 'idx_option_values_value');
        });

        // ============================================
        // 🔥 TESTIMONIALS - INDEXES (opsional, buat show page)
        // ============================================
        if (Schema::hasTable('testimonials')) {
            Schema::table('testimonials', function (Blueprint $table) {
                $table->index(
                    ['product_id', 'is_active', 'created_at'],
                    'idx_testimonials_product_active'
                );
            });
        }

        // ============================================
        // 🔥 WISHLIST - INDEXES (opsional)
        // ============================================
        if (Schema::hasTable('wishlists')) {
            Schema::table('wishlists', function (Blueprint $table) {
                $table->index(
                    ['user_id', 'product_id'],
                    'idx_wishlist_user_product'
                );
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ============================================
        // DROP INDEXES - PRODUCTS
        // ============================================
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_active_created');
            $table->dropIndex('idx_products_active_category');
            $table->dropIndex('idx_products_active_gender');
            $table->dropIndex('idx_products_flash');
            $table->dropIndex('idx_products_slug');
            $table->dropIndex('idx_products_product_discount');
        });

        // ============================================
        // DROP INDEXES - PRODUCT VARIANTS
        // ============================================
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropIndex('idx_variants_product_stock');
            $table->dropIndex('idx_variants_product_price');
            $table->dropIndex('idx_variants_product_discount');
            $table->dropIndex('idx_variants_product_active');
        });

        // ============================================
        // DROP INDEXES - PRODUCT VARIANT VALUES
        // ============================================
        Schema::table('product_variant_values', function (Blueprint $table) {
            $table->dropIndex('idx_pvv_variant_value');
            $table->dropIndex('idx_pvv_value');
        });

        // ============================================
        // DROP INDEXES - PRODUCT IMAGES
        // ============================================
        Schema::table('product_images', function (Blueprint $table) {
            $table->dropIndex('idx_images_product_sort');
        });

        // ============================================
        // DROP INDEXES - PRODUCT OPTIONS
        // ============================================
        Schema::table('product_options', function (Blueprint $table) {
            $table->dropIndex('idx_options_product_sort');
        });

        // ============================================
        // DROP INDEXES - PRODUCT OPTION VALUES
        // ============================================
        Schema::table('product_option_values', function (Blueprint $table) {
            $table->dropIndex('idx_option_values_sort');
            $table->dropIndex('idx_option_values_value');
        });

        // ============================================
        // DROP INDEXES - TESTIMONIALS
        // ============================================
        if (Schema::hasTable('testimonials')) {
            Schema::table('testimonials', function (Blueprint $table) {
                $table->dropIndex('idx_testimonials_product_active');
            });
        }

        // ============================================
        // DROP INDEXES - WISHLIST
        // ============================================
        if (Schema::hasTable('wishlists')) {
            Schema::table('wishlists', function (Blueprint $table) {
                $table->dropIndex('idx_wishlist_user_product');
            });
        }
    }
};