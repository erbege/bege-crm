<?php

namespace App\Services;

use App\Models\HotspotTransaction;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HotspotVoucherCheckoutService
{
    /**
     * Process checkout for Hotspot Transaction
     */
    public function processCheckout(HotspotTransaction $transaction, string $gateway, ?string $method = null)
    {
        Log::info("Starting hotspot checkout for {$transaction->reference_number} via {$gateway}");

        if ($gateway === 'tripay') {
            return $this->processTripay($transaction, $method ?? 'BRIVA');
        } elseif ($gateway === 'duitku') {
            return $this->processDuitku($transaction, $method);
        } elseif ($gateway === 'ipaymu') {
            return $this->processIpaymu($transaction, $method);
        }

        throw new \Exception("Unsupported payment gateway: {$gateway}");
    }

    private function processTripay(HotspotTransaction $transaction, $method)
    {
        $apiKey = Setting::get('payment.tripay_api_key');
        $privateKey = Setting::get('payment.tripay_private_key');
        $merchantCode = Setting::get('payment.tripay_merchant_code');
        $mode = Setting::get('payment.tripay_mode', 'sandbox');

        $endpoint = $mode === 'production'
            ? 'https://tripay.co.id/api/transaction/create'
            : 'https://tripay.co.id/api-sandbox/transaction/create';

        $returnUrl = route('hotspot.waiting', ['reference' => $transaction->reference_number]);

        $data = [
            'method' => $method,
            'merchant_ref' => $transaction->reference_number,
            'amount' => (int) $transaction->amount,
            'customer_name' => $transaction->customer_name ?: 'Guest Hotspot',
            'customer_email' => str_contains($transaction->customer_contact, '@') ? $transaction->customer_contact : 'guest@example.com',
            'customer_phone' => (strlen($transaction->customer_contact) >= 10 && is_numeric($transaction->customer_contact)) ? $transaction->customer_contact : '081234567890',
            'order_items' => [
                [
                    'name' => 'Voucher Hotspot ' . $transaction->profile->name,
                    'price' => (int) $transaction->amount,
                    'quantity' => 1,
                ]
            ],
            'return_url' => $returnUrl,
            'callback_url' => route('portal.webhooks.payment', ['gateway' => 'tripay']),
            'expired_time' => (time() + (24 * 60 * 60)), // 24 hours
            'signature' => hash_hmac('sha256', $merchantCode . $transaction->reference_number . (int) $transaction->amount, $privateKey)
        ];

        $response = Http::withToken($apiKey)->post($endpoint, $data);

        if ($response->successful() && $response->json('success')) {
            $transaction->update(['external_reference' => $response->json('data.reference')]);
            return [
                'success' => true,
                'checkout_url' => $response->json('data.checkout_url'),
                'pay_code' => $response->json('data.pay_code'),
                'qr_url' => $response->json('data.qr_url'),
                'instructions' => $response->json('data.instructions'),
            ];
        }

        Log::error('Tripay Hotspot Payment Failed: ' . $response->body());
        throw new \Exception("Gagal menghubungi Tripay: " . ($response->json('message') ?? 'Unknown error'));
    }

    private function processDuitku(HotspotTransaction $transaction, $method)
    {
        $merchantCode = Setting::get('payment.duitku_merchant_code');
        $apiKey = Setting::get('payment.duitku_api_key');
        $amount = (int) $transaction->amount;
        $orderId = $transaction->reference_number;

        $signature = md5($merchantCode . $orderId . $amount . $apiKey);

        $returnUrl = route('hotspot.waiting', ['reference' => $transaction->reference_number]);

        $data = [
            'merchantCode' => $merchantCode,
            'paymentAmount' => $amount,
            'merchantOrderId' => $orderId,
            'productDetails' => 'Voucher Hotspot ' . $transaction->profile->name,
            'email' => str_contains($transaction->customer_contact, '@') ? $transaction->customer_contact : 'guest@example.com',
            'phoneNumber' => (strlen($transaction->customer_contact) >= 10 && is_numeric($transaction->customer_contact)) ? $transaction->customer_contact : '081234567890',
            'customerVaName' => $transaction->customer_name ?: 'Guest Hotspot',
            'callbackUrl' => route('portal.webhooks.payment', ['gateway' => 'duitku']),
            'returnUrl' => $returnUrl,
            'signature' => $signature,
        ];

        if ($method) {
            $data['paymentMethod'] = $method;
        }

        $url = str_starts_with($merchantCode, 'DS')
            ? 'https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry'
            : 'https://passport.duitku.com/webapi/api/merchant/v2/inquiry';

        $response = Http::post($url, $data);

        if ($response->successful() && $response->json('statusCode') == '00') {
            return [
                'success' => true,
                'checkout_url' => $response->json('paymentUrl'),
            ];
        }

        Log::error('Duitku Hotspot Payment Failed: ' . $response->body());
        throw new \Exception("Gagal menghubungi Duitku.");
    }

    private function processIpaymu(HotspotTransaction $transaction, $method)
    {
        $va = Setting::get('payment.ipaymu_va');
        $apiKey = Setting::get('payment.ipaymu_api_key');

        $url = str_starts_with($apiKey, 'SANDBOX')
            ? 'https://sandbox.ipaymu.com/api/v2/payment'
            : 'https://my.ipaymu.com/api/v2/payment';

        $returnUrl = route('hotspot.waiting', ['reference' => $transaction->reference_number]);

        $body = [
            'product' => ['Voucher Hotspot ' . $transaction->profile->name],
            'qty' => ['1'],
            'price' => [(string) $transaction->amount],
            'returnUrl' => $returnUrl,
            'cancelUrl' => $returnUrl,
            'notifyUrl' => route('portal.webhooks.payment', ['gateway' => 'ipaymu']),
            'buyerName' => $transaction->customer_name ?: 'Guest',
            'buyerEmail' => str_contains($transaction->customer_contact, '@') ? $transaction->customer_contact : 'guest@example.com',
            'buyerPhone' => (strlen($transaction->customer_contact) >= 10 && is_numeric($transaction->customer_contact)) ? $transaction->customer_contact : '081234567890',
            'reference' => $transaction->reference_number,
        ];

        if ($method) {
            $body['paymentMethod'] = $method; // Simplified for this example
        }

        $jsonBody = json_encode($body, JSON_UNESCAPED_SLASHES);
        $requestBody = strtolower(hash('sha256', $jsonBody));
        $stringToSign = "POST:" . $va . ":" . $requestBody . ":" . $apiKey;
        $signature = hash_hmac('sha256', $stringToSign, $apiKey);
        $timestamp = date('YmdHis');

        $response = Http::withHeaders([
            'va' => $va,
            'signature' => $signature,
            'timestamp' => $timestamp,
            'Content-Type' => 'application/json'
        ])->post($url, $body);

        if ($response->successful() && $response->json('Status') == 200) {
            return [
                'success' => true,
                'checkout_url' => $response->json('Data.Url'),
            ];
        }

        Log::error('iPaymu Hotspot Payment Failed: ' . $response->body());
        throw new \Exception("Gagal menghubungi iPaymu.");
    }
}
