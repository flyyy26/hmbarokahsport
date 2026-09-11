<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('size_guides', function (Blueprint $table) {
            // 🔥 1. HAPUS FOREIGN KEY TERLEBIH DAHULU
            $table->dropForeign(['category_id']);
            
            // 🔥 2. BARU HAPUS UNIQUE INDEX
            $table->dropUnique('size_guides_category_id_size_unique');
            
            // 🔥 3. TAMBAHKAN FOREIGN KEY KEMBALI
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('size_guides', function (Blueprint $table) {
            // Rollback: hapus foreign key, tambah unique, tambah foreign key lagi
            $table->dropForeign(['category_id']);
            $table->unique(['category_id', 'size']);
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('cascade');
        });
    }
};