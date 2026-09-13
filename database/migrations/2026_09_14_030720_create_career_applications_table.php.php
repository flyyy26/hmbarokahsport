<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('career_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('career_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->text('address')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();

            $table->string('last_education')->nullable();
            $table->string('major')->nullable();
            $table->decimal('gpa', 3, 2)->nullable();
            $table->integer('experience_years')->default(0);
            $table->text('cover_letter')->nullable();

            $table->string('cv_file');           // path storage
            $table->string('portfolio_file')->nullable();

            $table->enum('status', [
                'pending',
                'reviewed',
                'shortlisted',
                'interview',
                'hired',
                'rejected',
            ])->default('pending');

            $table->text('admin_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();

            $table->index(['career_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('career_applications');
    }
};