<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('size_guides', function (Blueprint $table) {
            // Hapus unique constraint pada kolom size
            $table->dropUnique('size_guides_size_unique');
            
            // Buat unique constraint baru dengan kombinasi category_id dan size
            $table->unique(['category_id', 'size']);
        });
    }

    public function down(): void
    {
        Schema::table('size_guides', function (Blueprint $table) {
            $table->dropUnique(['category_id', 'size']);
            $table->unique('size');
        });
    }
};