<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\Order;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('orders:auto-complete', function () {
    $delayMinutes = config('app.orders_auto_complete_minutes', 60);

    $orders = Order::where('shipping_status', 'shipped')
        ->whereNull('delivered_at')
        ->where('shipped_at', '<=', now()->subMinutes($delayMinutes))
        ->get();

    $completed = 0;

    foreach ($orders as $order) {
        if ($order->isDeliveredOnBiteship()) {
            $order->update([
                'shipping_status' => 'delivered',
                'delivered_at' => now(),
            ]);
            $completed++;
            $this->info("Order #{$order->order_number} auto-completed (Biteship confirmed delivery).");
        }
    }

    $this->info("Auto-completed {$completed} order(s) with confirmed Biteship delivery.");
})->purpose('Auto-complete orders that Biteship has confirmed as delivered');

Artisan::command('orders:auto-cancel-unpaid', function () {
    $delayMinutes = config('app.orders_auto_cancel_minutes', 1440);

    $orders = Order::where('payment_status', 'unpaid')
        ->whereIn('shipping_status', ['pending', 'cancelled'])
        ->where('created_at', '<=', now()->subMinutes($delayMinutes))
        ->get();

    $deleted = 0;

    foreach ($orders as $order) {
        $order->loadMissing(['items.variant', 'items.product.variants']);

        DB::beginTransaction();
        try {
            foreach ($order->items as $item) {
                if ($item->variant) {
                    $item->variant->addStock(
                        $item->quantity,
                        'order_cancelled',
                        "Pesanan {$order->order_number} dihapus otomatis (belum bayar {$delayMinutes} menit)"
                    );
                } elseif ($item->product) {
                    $firstVariant = $item->product->variants->first();
                    if ($firstVariant) {
                        $firstVariant->addStock(
                            $item->quantity,
                            'order_cancelled',
                            "Pesanan {$order->order_number} dihapus otomatis (belum bayar {$delayMinutes} menit)"
                        );
                    }
                }
            }

            $order->delete();

            DB::commit();
            $deleted++;
            $this->info("Order #{$order->order_number} deleted (unpaid {$delayMinutes} minutes).");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Failed to delete order #{$order->order_number}: " . $e->getMessage());
        }
    }

    $this->info("Deleted {$deleted} unpaid order(s) after {$delayMinutes} minutes and restored stock.");
})->purpose('Auto-delete unpaid orders after 24 hours and restore stock');

