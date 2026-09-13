<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('event', 50);
            $table->string('ip', 45)->nullable();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('session_id', 100)->nullable()->index();
            $table->text('user_agent')->nullable();
            $table->string('referer', 1000)->nullable();
            $table->integer('quantity')->default(1);
            $table->timestamp('created_at')->useCurrent()->index();

            $table->index(['product_id', 'event', 'created_at']);
            $table->index(['event', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_analytics');
    }
};
