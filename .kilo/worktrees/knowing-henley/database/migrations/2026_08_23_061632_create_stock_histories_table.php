<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_variant_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // 🔥 DATA PERUBAHAN
            $table->integer('old_stock')->default(0);
            $table->integer('new_stock')->default(0);
            $table->integer('quantity_change')->default(0); // positif = masuk, negatif = keluar
            
            // 🔥 ALASAN PERUBAHAN
            $table->enum('reason', [
                'restock',           // Restock barang
                'sale',              // Penjualan
                'return',            // Pengembalian barang
                'adjustment',        // Penyesuaian manual
                'damaged',           // Rusak/expired
                'transfer_in',       // Transfer masuk dari gudang lain
                'transfer_out',      // Transfer keluar ke gudang lain
                'order_cancelled',   // Pesanan dibatalkan
                'system_adjustment', // Penyesuaian otomatis sistem
                'other'              // Lainnya
            ])->default('adjustment');
            
            $table->text('note')->nullable(); // Catatan tambahan
            
            // 🔥 DATA PENGGUNA
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            
            $table->timestamps();
            
            // 🔥 INDEX
            $table->index(['product_id', 'created_at']);
            $table->index(['product_variant_id', 'created_at']);
            $table->index('reason');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_histories');
    }
};