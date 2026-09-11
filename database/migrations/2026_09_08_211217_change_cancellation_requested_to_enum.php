<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Backup data dulu (opsional tapi disarankan)
        // DB::table('orders')->where('shipping_status', 'cancellation_requested')->update(['shipping_status' => 'pending']);
        
        // Ubah enum dengan menambahkan nilai baru
        DB::statement("ALTER TABLE orders MODIFY shipping_status ENUM('pending','processing','shipped','delivered','cancelled','cancellation_requested') DEFAULT 'pending'");
    }

    public function down()
    {
        // Kembalikan ke enum sebelumnya
        DB::statement("ALTER TABLE orders MODIFY shipping_status ENUM('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending'");
    }
};