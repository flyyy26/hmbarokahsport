<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('cancellation_status')->nullable()->after('shipping_status')->comment('pending, approved, rejected');
            $table->text('cancellation_reason')->nullable()->after('cancellation_status');
            $table->foreignId('cancelled_by_admin_id')->nullable()->after('cancellation_reason')->constrained('users')->nullOnDelete();
            $table->timestamp('cancellation_requested_at')->nullable()->after('cancelled_by_admin_id');
            $table->timestamp('cancellation_processed_at')->nullable()->after('cancellation_requested_at');
            $table->string('previous_shipping_status')->nullable()->after('cancellation_processed_at')->comment('Store original status before cancellation request');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['cancelled_by_admin_id']);
            $table->dropColumn([
                'cancellation_status',
                'cancellation_reason',
                'cancelled_by_admin_id',
                'cancellation_requested_at',
                'cancellation_processed_at',
                'previous_shipping_status',
            ]);
        });
    }
};
