<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('article_comments', function (Blueprint $table) {
            $table->timestamp('replied_at')->nullable()->after('is_active');
            $table->index(['article_id', 'replied_at']);
        });
    }

    public function down(): void
    {
        Schema::table('article_comments', function (Blueprint $table) {
            $table->dropIndex(['article_id', 'replied_at']);
            $table->dropColumn('replied_at');
        });
    }
};