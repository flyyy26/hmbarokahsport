<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$apiKey = config('services.biteship.api_key');
$baseUrl = config('services.biteship.base_url', 'https://api.biteship.com/v1');

$orderId = '6a9fc439e2c7730bccac8cf5';

$endpoints = [
    '/orders/' . $orderId . '/waybill',
    '/orders/' . $orderId . '/label',
    '/waybills/' . $orderId,
    '/waybills/' . $orderId . '/label',
    '/tracking/' . $orderId . '/label',
];

foreach ($endpoints as $endpoint) {
    $url = $baseUrl . $endpoint;
    $response = \Illuminate\Support\Facades\Http::timeout(30)
        ->accept('application/pdf')
        ->withToken($apiKey)
        ->get($url);
    echo "Endpoint: $endpoint => Status: " . $response->status() . ", Type: " . $response->header('Content-Type') . PHP_EOL;
}