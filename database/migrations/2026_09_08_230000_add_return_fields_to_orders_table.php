<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('return_status')->nullable()->after('cancellation_processed_at')->comment('pending, approved, rejected');
            $table->text('return_reason')->nullable()->after('return_status');
            $table->timestamp('return_requested_at')->nullable()->after('return_reason');
            $table->timestamp('return_processed_at')->nullable()->after('return_requested_at');
            $table->foreignId('returned_by_admin_id')->nullable()->after('return_processed_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['returned_by_admin_id']);
            $table->dropColumn([
                'return_status',
                'return_reason',
                'return_requested_at',
                'return_processed_at',
                'returned_by_admin_id',
            ]);
        });
    }
};
