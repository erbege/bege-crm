<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Setting;
use Illuminate\Support\Facades\Http;

$merchantCode = Setting::get('payment.duitku_merchant_code');
$apiKey = Setting::get('payment.duitku_api_key');
$amount = 10000;
$dt = date('Y-m-d H:i:s');
$url = 'https://sandbox.duitku.com/webapi/api/merchant/paymentmethod/getpaymentmethod';

$permutations = [
    'm + a + d + k' => $merchantCode . $amount . $dt . $apiKey,
    'm + d + a + k' => $merchantCode . $dt . $amount . $apiKey,
    'k + m + a + d' => $apiKey . $merchantCode . $amount . $dt,
    'm + a + k + d' => $merchantCode . $amount . $apiKey . $dt,
];

foreach ($permutations as $label => $str) {
    echo "Testing $label: $str\n";
    $sig = hash('sha256', $str);
    $response = Http::post($url, [
        'merchantCode' => $merchantCode,
        'paymentAmount' => $amount,
        'datetime' => $dt,
        'signature' => $sig
    ]);
    echo "Result: " . $response->status() . " " . $response->body() . "\n\n";
}
