<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 🔥 CEK APAKAH KOLOM customer_id MASIH ADA
        $hasCustomerId = Schema::hasColumn('orders', 'customer_id');
        $hasUserId = Schema::hasColumn('orders', 'user_id');

        if (!$hasCustomerId && $hasUserId) {
            // 🔥 JIKA SUDAH USER_ID, TAMBAHKAN FOREIGN KEY SAJA
            Schema::table('orders', function (Blueprint $table) {
                $table->foreign('user_id')
                      ->references('id')
                      ->on('users')
                      ->onDelete('cascade');
            });
            return;
        }

        if (!$hasCustomerId) {
            // 🔥 JIKA customer_id TIDAK ADA, TAMBAHKAN user_id
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('id');
                    $table->foreign('user_id')
                          ->references('id')
                          ->on('users')
                          ->onDelete('cascade');
                }
            });
            return;
        }

        // 🔥 HAPUS FOREIGN KEY LAMA
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
        });

        // 🔥 RENAME KOLOM
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('customer_id', 'user_id');
        });

        // 🔥 TAMBAHKAN FOREIGN KEY BARU
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 🔥 HAPUS FOREIGN KEY
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'user_id')) {
                $table->dropForeign(['user_id']);
            }
        });

        // 🔥 RENAME KEMBALI
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'user_id')) {
                $table->renameColumn('user_id', 'customer_id');
            }
        });

        // 🔥 TAMBAHKAN FOREIGN KEY LAMA
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'customer_id')) {
                $table->foreign('customer_id')
                      ->references('id')
                      ->on('customers')
                      ->onDelete('cascade');
            }
        });
    }
};