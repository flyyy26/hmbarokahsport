<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('career_applications', function (Blueprint $table) {
            // Drop kolom yang tidak dipakai
            if (Schema::hasColumn('career_applications', 'gpa')) {
                $table->dropColumn('gpa');
            }
            if (Schema::hasColumn('career_applications', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('career_applications', 'admin_notes')) {
                $table->dropColumn('admin_notes');
            }
            if (Schema::hasColumn('career_applications', 'reviewed_at')) {
                $table->dropColumn('reviewed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('career_applications', function (Blueprint $table) {
            $table->decimal('gpa', 3, 2)->nullable();
            $table->enum('status', [
                'pending', 'reviewed', 'shortlisted',
                'interview', 'hired', 'rejected',
            ])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
        });
    }
};