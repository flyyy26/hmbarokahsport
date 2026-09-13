<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('search_analytics', function (Blueprint $table) {
            $table->id();
            $table->string('keyword', 255)->index();
            $table->string('ip', 45)->nullable();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('session_id', 100)->nullable()->index();
            $table->text('user_agent')->nullable();
            $table->string('referer', 1000)->nullable();
            $table->integer('results_count')->default(0);
            $table->timestamp('created_at')->useCurrent()->index();

            $table->index(['keyword', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_analytics');
    }
};
