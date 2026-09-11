<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('about_us', function (Blueprint $table) {
            // Ubah dari string menjadi text
            $table->text('vision')->nullable()->change();
            $table->text('mission')->nullable()->change();
            
            // Jika content juga perlu diubah menjadi longText (opsional)
            $table->longText('content')->change();
        });
    }

    public function down(): void
    {
        Schema::table('about_us', function (Blueprint $table) {
            // Kembalikan ke string (hati-hati data akan terpotong)
            $table->string('vision')->nullable()->change();
            $table->string('mission')->nullable()->change();
            $table->text('content')->change();
        });
    }
};