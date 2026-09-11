<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$apiKey = config('services.biteship.api_key');
$baseUrl = config('services.biteship.base_url', 'https://api.biteship.com/v1');

$orderId = '6a9fc439e2c7730bccac8cf5';

$url = $baseUrl . '/orders/' . $orderId . '/label';
$response = \Illuminate\Support\Facades\Http::timeout(30)
    ->acceptJson()
    ->withToken($apiKey)
    ->get($url);

echo "Status: " . $response->status() . PHP_EOL;
$data = $response->json();
print_r($data);