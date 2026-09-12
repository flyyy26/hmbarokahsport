<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offline_orders', function (Blueprint $table) {
            // 🔥 Cek dulu apakah kolom sudah ada (untuk avoid error kalau migrate ulang)
            if (!Schema::hasColumn('offline_orders', 'transaction_discount')) {
                $table->decimal('transaction_discount', 15, 2)->default(0)->after('shipping_cost');
            }

            if (!Schema::hasColumn('offline_orders', 'transaction_discount_type')) {
                $table->string('transaction_discount_type', 20)->nullable()->after('transaction_discount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('offline_orders', function (Blueprint $table) {
            if (Schema::hasColumn('offline_orders', 'transaction_discount')) {
                $table->dropColumn('transaction_discount');
            }
            if (Schema::hasColumn('offline_orders', 'transaction_discount_type')) {
                $table->dropColumn('transaction_discount_type');
            }
        });
    }
};