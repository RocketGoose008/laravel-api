<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// Bootstrap Laravel
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// ตอนนี้สามารถใช้ storage_path() ได้แล้ว
$file = storage_path('api-docs/api-docs.json');

if (!file_exists($file)) {
    echo "Swagger JSON file not found.\n";
    exit(1);
}

$json = json_decode(file_get_contents($file), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Error decoding JSON: " . json_last_error_msg() . "\n";
    exit(1);
}

if (!isset($json['tags'])) {
    echo "No tags found in Swagger file.\n";
    exit(0);
}

// ลบ description ออกจากทุก tag
foreach ($json['tags'] as &$tag) {
    unset($tag['description']);
}

file_put_contents($file, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo "Cleaned up tag descriptions in Swagger JSON.\n";
