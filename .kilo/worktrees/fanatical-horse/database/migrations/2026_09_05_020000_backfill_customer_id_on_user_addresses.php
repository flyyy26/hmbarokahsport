<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('user_addresses')
            ->whereNull('customer_id')
            ->whereNotNull('user_id')
            ->update(['customer_id' => DB::raw('user_id')]);
    }

    public function down(): void
    {
        DB::table('user_addresses')
            ->whereColumn('customer_id', 'user_id')
            ->update(['customer_id' => null]);
    }
};
