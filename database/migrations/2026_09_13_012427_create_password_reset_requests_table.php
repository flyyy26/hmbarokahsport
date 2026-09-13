<?php
// database/migrations/xxxx_xx_xx_create_password_reset_requests_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_reset_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('phone', 30);                    // nomor HP yang diminta
            $table->enum('status', [
                'pending',      // menunggu admin
                'approved',     // disetujui, token aktif
                'rejected',     // ditolak admin
                'used',         // sudah dipakai reset
                'expired',      // kadaluarsa
            ])->default('pending');
            $table->string('token', 64)->nullable()->unique(); // token reset
            $table->text('admin_note')->nullable();         // catatan admin (kalau reject)
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete(); // admin yg proses
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('expires_at')->nullable();    // token expire
            $table->timestamp('used_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            // Index untuk query cepat
            $table->index(['status', 'created_at']);
            $table->index(['user_id', 'status']);
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_requests');
    }
};