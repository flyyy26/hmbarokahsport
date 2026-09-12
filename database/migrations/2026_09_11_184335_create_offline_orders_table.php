<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offline_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();

            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid', 'failed', 'refunded'])->default('unpaid');
            $table->enum('shipping_status', ['pending', 'processing', 'shipped', 'delivered'])->default('pending');

            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->text('customer_address')->nullable();
            $table->string('shipping_name');
            $table->string('shipping_phone');
            $table->text('shipping_address');
            $table->string('shipping_city');
            $table->string('shipping_province');
            $table->string('shipping_postal_code')->default('0');
            $table->string('shipping_district')->nullable();
            $table->string('shipping_subdistrict')->nullable();

            $table->string('cancellation_status')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->foreignId('cancelled_by_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cancellation_requested_at')->nullable();
            $table->timestamp('cancellation_processed_at')->nullable();
            $table->string('previous_shipping_status')->nullable();

            $table->string('return_status')->nullable();
            $table->text('return_reason')->nullable();
            $table->timestamp('return_requested_at')->nullable();
            $table->timestamp('return_processed_at')->nullable();
            $table->foreignId('returned_by_admin_id')->nullable()->constrained('users')->nullOnDelete();

            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->decimal('transaction_discount', 15, 2)->default(0);
            $table->string('transaction_discount_type')->default('nominal')->comment('nominal, percentage');
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);

            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();

            $table->string('tracking_number')->nullable();
            $table->string('courier')->nullable();
            $table->string('service')->nullable();

            $table->string('payment_method')->nullable();
            $table->string('midtrans_status')->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();

            $table->index('order_number');
            $table->index('status');
            $table->index('payment_status');
        });

        Schema::create('offline_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offline_order_id')->constrained('offline_orders')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->string('product_name');
            $table->string('variant_name')->nullable();
            $table->string('sku')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->integer('quantity')->default(1);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->json('variant_attributes')->nullable();
            $table->timestamps();

            $table->index('offline_order_id');
            $table->index('product_id');
            $table->index('product_variant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offline_order_items');
        Schema::dropIfExists('offline_orders');
    }
};
