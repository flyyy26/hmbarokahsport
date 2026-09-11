<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$setting = App\Models\Setting::first();
$setting->update([
    'address' => 'Jl. Cilendek Kelurahan Sumelap Kecamatan Tamansari',
    'postal_code' => '46191',
]);
echo "Updated address: " . $setting->address . PHP_EOL;
echo "Updated postal_code: " . $setting->postal_code . PHP_EOL;