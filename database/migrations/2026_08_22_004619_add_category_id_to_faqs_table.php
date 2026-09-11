<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Cek apakah kolom sudah ada
        if (!Schema::hasColumn('faqs', 'category_id')) {
            Schema::table('faqs', function (Blueprint $table) {
                $table->unsignedBigInteger('category_id')->nullable()->after('id');
            });
        }

        // Set default category_id
        $defaultCategory = DB::table('faq_categories')->first();
        if ($defaultCategory) {
            DB::table('faqs')->whereNull('category_id')->update(['category_id' => $defaultCategory->id]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('faqs', 'category_id')) {
            Schema::table('faqs', function (Blueprint $table) {
                $table->dropColumn('category_id');
            });
        }
    }
};