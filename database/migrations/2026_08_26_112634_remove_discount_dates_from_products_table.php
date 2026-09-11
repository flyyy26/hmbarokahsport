<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['discount_start_date', 'discount_end_date']);
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->timestamp('discount_start_date')->nullable()->after('discount_value');
            $table->timestamp('discount_end_date')->nullable()->after('discount_start_date');
        });
    }
};