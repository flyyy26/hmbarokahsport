<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_visits', function (Blueprint $table) {
            $table->id();
            $table->string('url', 500);                    // path halaman
            $table->string('full_url', 1000)->nullable();  // URL lengkap dengan query
            $table->string('method', 10)->default('GET');
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('referer', 1000)->nullable();
            $table->foreignId('user_id')->nullable()->index();  // customer ID kalau login
            $table->string('session_id', 100)->nullable()->index();
            $table->integer('response_time')->nullable();  // milliseconds
            $table->integer('status_code')->nullable();
            $table->timestamp('visited_at')->useCurrent()->index();

            $table->index(['url', 'visited_at']);
            $table->index(['ip', 'visited_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_visits');
    }
};