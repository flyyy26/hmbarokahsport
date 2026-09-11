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
        Schema::table('voucher_usages', function (Blueprint $table) {
            // 🔥 CEK APAKAH KOLOM SUDAH ADA SEBELUM MENAMBAHKAN
            if (!Schema::hasColumn('voucher_usages', 'product_discount')) {
                $table->decimal('product_discount', 15, 2)->default(0)->after('discount_applied');
            }
            
            if (!Schema::hasColumn('voucher_usages', 'shipping_discount')) {
                $table->decimal('shipping_discount', 15, 2)->default(0)->after('product_discount');
            }
            
            if (!Schema::hasColumn('voucher_usages', 'is_free_shipping')) {
                $table->boolean('is_free_shipping')->default(false)->after('shipping_discount');
            }
            
            // 🔥 KOLOM TAMBAHAN UNTUK TRACKING LEBIH LENGKAP
            if (!Schema::hasColumn('voucher_usages', 'courier_applied')) {
                $table->string('courier_applied')->nullable()->after('is_free_shipping');
            }
            
            if (!Schema::hasColumn('voucher_usages', 'shipping_cost_before')) {
                $table->decimal('shipping_cost_before', 15, 2)->default(0)->after('courier_applied');
            }
            
            if (!Schema::hasColumn('voucher_usages', 'shipping_cost_after')) {
                $table->decimal('shipping_cost_after', 15, 2)->default(0)->after('shipping_cost_before');
            }
            
            if (!Schema::hasColumn('voucher_usages', 'subtotal_before')) {
                $table->decimal('subtotal_before', 15, 2)->default(0)->after('shipping_cost_after');
            }
            
            if (!Schema::hasColumn('voucher_usages', 'subtotal_after')) {
                $table->decimal('subtotal_after', 15, 2)->default(0)->after('subtotal_before');
            }
            
            if (!Schema::hasColumn('voucher_usages', 'total_before')) {
                $table->decimal('total_before', 15, 2)->default(0)->after('subtotal_after');
            }
            
            if (!Schema::hasColumn('voucher_usages', 'total_after')) {
                $table->decimal('total_after', 15, 2)->default(0)->after('total_before');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voucher_usages', function (Blueprint $table) {
            $columns = [
                'product_discount',
                'shipping_discount',
                'is_free_shipping',
                'courier_applied',
                'shipping_cost_before',
                'shipping_cost_after',
                'subtotal_before',
                'subtotal_after',
                'total_before',
                'total_after',
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('voucher_usages', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};