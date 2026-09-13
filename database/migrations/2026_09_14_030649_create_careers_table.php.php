<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('careers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('department')->nullable();
            $table->string('location')->nullable();
            $table->enum('type', ['full_time', 'part_time', 'contract', 'internship', 'freelance'])
                ->default('full_time');
            $table->enum('level', ['staff', 'senior', 'supervisor', 'manager', 'director'])
                ->default('staff');
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();       // rich text
            $table->longText('requirements')->nullable();      // rich text
            $table->longText('benefits')->nullable();          // rich text
            $table->decimal('salary_min', 12, 2)->nullable();
            $table->decimal('salary_max', 12, 2)->nullable();
            $table->boolean('show_salary')->default(false);
            $table->date('deadline')->nullable();
            $table->integer('quota')->default(1);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('careers');
    }
};