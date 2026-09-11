<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // 🔥 Diskon per produk (berlaku untuk semua varian)
            $table->boolean('has_product_discount')->default(false)->after('restock_threshold');
            $table->enum('discount_type', ['percentage', 'fixed'])->nullable()->after('has_product_discount');
            $table->decimal('discount_value', 10, 2)->nullable()->after('discount_type');
            $table->timestamp('discount_start_date')->nullable()->after('discount_value');
            $table->timestamp('discount_end_date')->nullable()->after('discount_start_date');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'has_product_discount',
                'discount_type',
                'discount_value',
                'discount_start_date',
                'discount_end_date'
            ]);
        });
    }
};