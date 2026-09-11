<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Hapus shipping_postal_code jika ada
            if (Schema::hasColumn('orders', 'shipping_postal_code')) {
                $table->dropColumn('shipping_postal_code');
            }
            
            // Tambah shipping_subdistrict jika belum ada
            if (!Schema::hasColumn('orders', 'shipping_subdistrict')) {
                $table->string('shipping_subdistrict')->nullable()->after('shipping_district');
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'shipping_subdistrict')) {
                $table->dropColumn('shipping_subdistrict');
            }
            if (!Schema::hasColumn('orders', 'shipping_postal_code')) {
                $table->string('shipping_postal_code')->nullable()->after('shipping_district');
            }
        });
    }
};