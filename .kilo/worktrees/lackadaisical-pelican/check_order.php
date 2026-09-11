<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$order = App\Models\Order::find(47);
echo 'Order 47: ' . ($order ? 'found' : 'NOT FOUND') . PHP_EOL;
if ($order) {
    echo 'biteship_order_id: ' . $order->biteship_order_id . PHP_EOL;
    echo 'tracking_number: ' . $order->tracking_number . PHP_EOL;
    echo 'biteship_tracking_url: ' . $order->biteship_tracking_url . PHP_EOL;
}