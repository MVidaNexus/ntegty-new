<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$types = \App\Models\ExamType::all(['id', 'name_ar', 'code']);
foreach ($types as $type) {
    echo "ID: {$type->id}, Name: {$type->name_ar}, Code: '{$type->code}'\n";
}
