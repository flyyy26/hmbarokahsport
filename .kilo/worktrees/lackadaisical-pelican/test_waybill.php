<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$order = App\Models\Order::find(47);
if (!$order) {
    echo "Order not found\n";
    exit(1);
}

echo "Testing Biteship getWaybill...\n";

$apiKey = config('services.biteship.api_key');
$baseUrl = config('services.biteship.base_url', 'https://api.biteship.com/v1');

$response = \Illuminate\Support\Facades\Http::timeout(30)
    ->accept('application/pdf')
    ->withToken($apiKey)
    ->get($baseUrl . '/orders/' . $order->biteship_order_id . '/waybill');

echo "Status: " . $response->status() . PHP_EOL;
echo "Content-Type: " . $response->header('Content-Type') . PHP_EOL;
echo "Content-Length: " . strlen($response->body()) . PHP_EOL;

if ($response->successful()) {
    echo "SUCCESS - PDF size: " . strlen($response->body()) . " bytes\n";
} else {
    echo "FAILED: " . $response->body() . PHP_EOL;
}