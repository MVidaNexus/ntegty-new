<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Setting;

$setting = Setting::firstOrCreate(
    ['key' => 'show_academic_year_filter'],
    [
        'value' => '0',
        'type' => 'boolean',
        'group' => 'general'
    ]
);

echo "Setting created/retrieved: " . $setting->id . "\n";
