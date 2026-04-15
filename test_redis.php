<?php

use Illuminate\Support\Facades\Redis;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Redis connection to: " . env('REDIS_HOST') . ":" . env('REDIS_PORT') . "\n";

try {
    $start = microtime(true);
    $redis = Redis::connection();
    $redis->ping();
    $end = microtime(true);
    echo "Successfully connected to Redis in " . round($end - $start, 4) . "s\n";
    
    $redis->set('test_key', 'test_value');
    echo "Set test_key: " . $redis->get('test_key') . "\n";
} catch (\Exception $e) {
    echo "Failed to connect to Redis!\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
