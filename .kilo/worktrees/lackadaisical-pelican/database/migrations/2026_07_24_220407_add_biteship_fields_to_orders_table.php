<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('biteship_order_id')->nullable()->after('order_number');
            $table->string('biteship_waybill_id')->nullable()->after('biteship_order_id');
            $table->string('biteship_tracking_url')->nullable()->after('biteship_waybill_id');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['biteship_order_id', 'biteship_waybill_id', 'biteship_tracking_url']);
        });
    }
};