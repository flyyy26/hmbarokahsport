<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('biteship_api_usage', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->nullable()->index();
            $table->string('endpoint')->nullable();
            $table->string('action')->nullable();
            $table->string('origin_postal_code', 10)->nullable();
            $table->string('destination_postal_code', 10)->nullable();
            $table->decimal('api_cost', 14, 2)->default(5000)->comment('Biaya per hit API, default Rp5');
            $table->string('ip_address')->nullable();
            $table->text('request_data')->nullable();
            $table->json('response_summary')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biteship_api_usage');
    }
};
