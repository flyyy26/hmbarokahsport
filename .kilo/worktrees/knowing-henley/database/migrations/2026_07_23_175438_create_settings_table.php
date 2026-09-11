<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {

            $table->id();

            $table->string('store_name')
                ->nullable();

            $table->text('store_description')
                ->nullable();

            $table->string('logo')
                ->nullable();

            $table->string('favicon')
                ->nullable();

            $table->string('phone')
                ->nullable();

            $table->string('whatsapp')
                ->nullable();

            $table->string('email')
                ->nullable();

            $table->text('address')
                ->nullable();

            $table->string('instagram')
                ->nullable();

            $table->string('facebook')
                ->nullable();

            $table->string('tiktok')
                ->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};