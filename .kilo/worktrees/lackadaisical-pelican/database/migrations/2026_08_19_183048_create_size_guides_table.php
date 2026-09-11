<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('size_guides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('size')->unique(); // S, M, L, XL, XXL, dll
            $table->integer('chest_min')->nullable(); // Lingkar dada min
            $table->integer('chest_max')->nullable(); // Lingkar dada max
            $table->integer('waist_min')->nullable(); // Lingkar pinggang min
            $table->integer('waist_max')->nullable(); // Lingkar pinggang max
            $table->integer('length')->nullable(); // Panjang
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('size_guides');
    }
};