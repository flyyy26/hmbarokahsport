<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama Voucher (misal: "Diskon Gajian 100RB")
            $table->string('code')->unique(); // Kode Promo (misal: "EIGERPAYDAY")
            $table->text('description')->nullable(); // Deskripsi singkat
            $table->text('terms_and_conditions')->nullable(); // Syarat & Ketentuan
            
            // Tipe & Nilai Diskon
            $table->enum('discount_type', ['fixed', 'percentage']); // Potongan Rp atau %
            $table->decimal('discount_value', 12, 2); // Nilai (misal: 100000 atau 20)
            $table->decimal('max_discount_amount', 12, 2)->nullable(); // Maksimal potongan jika % (misal: max 150000)
            
            // Aturan Transaksi
            $table->decimal('min_transaction_amount', 12, 2)->default(0); // Syarat minimal belanja (misal: 300000)
            $table->unsignedInteger('usage_limit')->nullable(); // Total kuota penggunaan voucher
            $table->unsignedInteger('used_count')->default(0); // Jumlah yang sudah dipakai
            $table->unsignedInteger('limit_per_user')->default(1); // Batas pakai per akun
            
            // Masa Berlaku
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
        });

        // Tabel tracking pemakaian per user
        Schema::create('voucher_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voucher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('discount_applied', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher_usages');
        Schema::dropIfExists('vouchers');
    }
};