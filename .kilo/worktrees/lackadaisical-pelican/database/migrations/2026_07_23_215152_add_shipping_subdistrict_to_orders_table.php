<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Hanya tambahkan kolom yang belum ada
            if (!Schema::hasColumn('orders', 'shipping_subdistrict')) {
                $table->string('shipping_subdistrict')->nullable()->after('shipping_district');
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('shipping_subdistrict');
        });
    }
};