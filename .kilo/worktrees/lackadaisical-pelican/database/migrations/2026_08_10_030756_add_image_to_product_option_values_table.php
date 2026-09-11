<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('product_option_values', function (Blueprint $table) {
            $table->string('image')->nullable()->after('value');
        });
    }

    public function down()
    {
        Schema::table('product_option_values', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};