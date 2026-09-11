<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateIndonesiaRegionsTable extends Migration
{
    public function up()
    {
        Schema::create('indonesia_regions', function (Blueprint $table) {
            $table->string('code')->primary();
            $table->string('name');
            $table->string('postal_code')->nullable();
            $table->string('status')->nullable();

            $table->index('name');
            $table->index('postal_code');
        });
    }

    public function down()
    {
        Schema::dropIfExists('indonesia_regions');
    }
}
