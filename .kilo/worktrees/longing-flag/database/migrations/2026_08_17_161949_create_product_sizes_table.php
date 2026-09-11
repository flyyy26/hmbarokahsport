<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('size_id')->constrained()->onDelete('cascade');
            $table->integer('stock')->default(0);
            $table->decimal('price', 12, 2)->nullable();
            $table->decimal('discount_price', 12, 2)->nullable();
            $table->string('sku')->nullable();
            $table->integer('weight')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['product_id', 'size_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_sizes');
    }
};