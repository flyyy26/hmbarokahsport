<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$setting = App\Models\Setting::first();
echo 'store_name: ' . $setting->store_name . PHP_EOL;
echo 'phone: ' . $setting->phone . PHP_EOL;
echo 'address: ' . $setting->address . PHP_EOL;
echo 'postal_code: ' . $setting->postal_code . PHP_EOL;