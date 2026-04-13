<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$setting = \App\Models\Setting::find(6);
if ($setting) {
    echo "ID: " . $setting->id . "\n";
    echo "Key: " . $setting->key . "\n";
    echo "Value: '" . $setting->value . "'\n";
    echo "Type: " . $setting->type . "\n";
    echo "Value Type: " . gettype($setting->value) . "\n";
} else {
    echo "Setting not found.\n";
}
