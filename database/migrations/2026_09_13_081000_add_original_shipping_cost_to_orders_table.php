<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'original_shipping_cost')) {
                $table->decimal('original_shipping_cost', 15, 2)->default(0)->after('shipping_cost');
            }
        });

        Schema::table('offline_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('offline_orders', 'original_shipping_cost')) {
                $table->decimal('original_shipping_cost', 15, 2)->default(0)->after('shipping_cost');
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'original_shipping_cost')) {
                $table->dropColumn('original_shipping_cost');
            }
        });

        Schema::table('offline_orders', function (Blueprint $table) {
            if (Schema::hasColumn('offline_orders', 'original_shipping_cost')) {
                $table->dropColumn('original_shipping_cost');
            }
        });
    }
};
