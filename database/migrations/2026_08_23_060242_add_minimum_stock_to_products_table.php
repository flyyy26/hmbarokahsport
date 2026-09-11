<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // 🔥 Stok minimum per produk (global, berlaku untuk semua varian)
            $table->integer('minimum_stock')->default(5)->after('is_active');
            $table->integer('restock_threshold')->default(10)->after('minimum_stock');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['minimum_stock', 'restock_threshold']);
        });
    }
};