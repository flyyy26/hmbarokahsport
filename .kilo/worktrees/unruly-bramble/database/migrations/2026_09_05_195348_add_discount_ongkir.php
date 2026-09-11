<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('vouchers', function (Blueprint $table) {
            // 🔥 TIPE DISKON: 'product' (diskon produk), 'shipping' (diskon ongkir)
            $table->enum('discount_target', ['product', 'shipping'])
                  ->default('product')
                  ->after('discount_type');
            
            // 🔥 Apakah voucher ini gratis ongkir?
            $table->boolean('is_free_shipping')
                  ->default(false)
                  ->after('discount_target');
            
            // 🔥 Maksimal potongan ongkir (jika ada batasan)
            $table->decimal('max_shipping_discount', 15, 2)
                  ->nullable()
                  ->after('max_discount_amount');
            
            // 🔥 Apakah berlaku untuk semua kurir?
            $table->boolean('apply_to_all_couriers')
                  ->default(true)
                  ->after('max_shipping_discount');
            
            // 🔥 Kurir yang berlaku (jika tidak semua)
            $table->json('applicable_couriers')
                  ->nullable()
                  ->after('apply_to_all_couriers');
        });
    }

    public function down()
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn([
                'discount_target',
                'is_free_shipping',
                'max_shipping_discount',
                'apply_to_all_couriers',
                'applicable_couriers'
            ]);
        });
    }
};