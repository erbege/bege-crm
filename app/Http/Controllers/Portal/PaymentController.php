<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function checkout(Invoice $invoice)
    {
        if ($invoice->customer_id !== auth('customer')->id()) {
            abort(403);
        }

        if ($invoice->status === 'paid') {
            return redirect()->route('portal.invoices')->with('error', 'Invoice is already paid.');
        }

        Log::info('Evaluating Payment Gateways...', [
            'tripay_enabled' => Setting::get('payment.tripay_enabled'),
            'ipaymu_enabled' => Setting::get('payment.ipaymu_enabled'),
            'duitku_enabled' => Setting::get('payment.duitku_enabled'),
        ]);

        // Logic to determine active payment gateway
        if (Setting::get('payment.duitku_enabled')) {
            Log::info('Selected Gateway: Duitku', ['invoice' => $invoice->invoice_number, 'method' => request('method')]);
            if (request()->has('method')) {
                return $this->processDuitku($invoice, request('method'));
            }
            return $this->showDuitkuChannels($invoice);
        }

        if (Setting::get('payment.ipaymu_enabled')) {
            Log::info('Selected: iPaymu');
            // If method is provided, process transaction
            if (request()->has('method')) {
                return $this->processIpaymu($invoice, request('method'), request('channel'));
            }

            // Otherwise, show channel selection
            return $this->showIpaymuChannels($invoice);
        }

        if (Setting::get('payment.tripay_enabled')) {
            Log::info('Selected: Tripay');
            // If method is provided, process transaction
            if (request()->has('method')) {
                return $this->processTripay($invoice, request('method'));
            }

            // Otherwise, show channel selection
            return $this->showTripayChannels($invoice);
        }

        if (Setting::get('payment.mayar_enabled')) {
            return $this->processMayar($invoice);
        }

        Log::warning('No payment gateway met, falling back to WA');
        // Fallback: Contact Admin via WhatsApp
        return $this->fallbackToWhatsApp($invoice);
    }

    private function processTripay(Invoice $invoice, $method = null)
    {
        // If no method provided, fallback (though selection should have happened)
        if (!$method) {
            $method = 'BRIVA'; // Default fallback
        }

        $apiKey = Setting::get('payment.tripay_api_key');
        $privateKey = Setting::get('payment.tripay_private_key');
        $merchantCode = Setting::get('payment.tripay_merchant_code');
        $mode = Setting::get('payment.tripay_mode', 'sandbox');

        $endpoint = $mode === 'production'
            ? 'https://tripay.co.id/api/transaction/create'
            : 'https://tripay.co.id/api-sandbox/transaction/create';

        // Data payload
        $data = [
            'method' => $method,
            'merchant_ref' => $invoice->invoice_number,
            'amount' => (int) $invoice->total,
            'customer_name' => $invoice->customer->name,
            'customer_email' => $invoice->customer->email ?? 'noemail@example.com',
            'customer_phone' => $invoice->customer->phone,
            'order_items' => [
                [
                    'name' => 'Pembayaran Layanan Internet',
                    'price' => (int) $invoice->total,
                    'quantity' => 1,
                ]
            ],
            'return_url' => route('portal.invoices'),
            'callback_url' => route('portal.webhooks.payment', ['gateway' => 'tripay']),
            'expired_time' => (time() + (24 * 60 * 60)), // 24 hours
            'signature' => hash_hmac('sha256', $merchantCode . $invoice->invoice_number . (int) $invoice->total, $privateKey)
        ];

        Log::info("Tripay Request Payload:", $data);
        $response = Http::withToken($apiKey)->post($endpoint, $data);
        Log::info("Tripay Response Body:", $response->json() ?? ['raw' => $response->body()]);

        if ($response->successful() && $response->json('success')) {
            return redirect()->away($response->json('data.checkout_url'));
        }

        Log::error('Tripay Payment Failed: ' . $response->body());
        return $this->fallbackToWhatsApp($invoice);
    }

    private function processIpaymu(Invoice $invoice, $method = null, $channel = null)
    {
        $va = Setting::get('payment.ipaymu_va');
        $va = Setting::get('payment.ipaymu_va');
        $apiKey = Setting::get('payment.ipaymu_api_key');

        $url = str_starts_with($apiKey, 'SANDBOX')
            ? 'https://sandbox.ipaymu.com/api/v2/payment'
            : 'https://my.ipaymu.com/api/v2/payment';

        $body = [
            'product' => ['Pembayaran Invoice ' . $invoice->invoice_number],
            'qty' => ['1'],
            'price' => [(string) $invoice->total],
            'returnUrl' => route('portal.invoices'),
            'cancelUrl' => route('portal.invoices'),
            'notifyUrl' => route('portal.webhooks.payment', ['gateway' => 'ipaymu']),
            'buyerName' => $invoice->customer->name,
            'buyerEmail' => $invoice->customer->email ?? 'noemail@example.com',
            'buyerPhone' => $invoice->customer->phone ?? '081234567890',
            'reference' => $invoice->invoice_number,
        ];

        if ($method) {
            $body['paymentMethod'] = $method;
        }

        if ($channel) {
            $body['paymentChannel'] = $channel;
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
            return redirect()->away($response->json('Data.Url'));
        }

        Log::error('iPaymu Payment Failed: ' . ($response->json('Message') ?? $response->body()), [
            'status' => $response->status(),
            'url' => $url,
            'payload' => $body
        ]);
        return $this->fallbackToWhatsApp($invoice);
    }

    private function processDuitku(Invoice $invoice, $method = null)
    {
        $merchantCode = Setting::get('payment.duitku_merchant_code');
        $apiKey = Setting::get('payment.duitku_api_key');
        $amount = (int) $invoice->total;
        $orderId = $invoice->invoice_number;

        $signature = md5($merchantCode . $orderId . $amount . $apiKey);
        Log::info('Duitku Inquiry Signature Calculation:', [
            'merchantCode' => $merchantCode,
            'amount' => $amount,
            'orderId' => $orderId,
            'signature' => $signature
        ]);

        $data = [
            'merchantCode' => $merchantCode,
            'paymentAmount' => $amount,
            'merchantOrderId' => $orderId,
            'productDetails' => 'Pembayaran Layanan Internet Invoice ' . $orderId,
            'email' => $invoice->customer->email ?? 'noemail@example.com',
            'phoneNumber' => $invoice->customer->phone ?? '081234567890',
            'customerVaName' => $invoice->customer->name,
            'callbackUrl' => route('portal.webhooks.payment', ['gateway' => 'duitku']),
            'returnUrl' => route('portal.invoices'),
            'signature' => $signature,
        ];

        if ($method) {
            $data['paymentMethod'] = $method;
        }

        // Use appropriate URL based on merchant code (DS = sandbox)
        $url = str_starts_with($merchantCode, 'DS')
            ? 'https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry'
            : 'https://passport.duitku.com/webapi/api/merchant/v2/inquiry';

        $response = Http::post($url, $data);

        if ($response->successful() && $response->json('statusCode') == '00') {
            return redirect()->away($response->json('paymentUrl'));
        }

        Log::error('Duitku Payment Failed: ' . ($response->json('Message') ?? $response->body()), [
            'status' => $response->status(),
            'url' => $url,
            'payload' => $data
        ]);
        return $this->fallbackToWhatsApp($invoice);
    }

    private function processMayar(Invoice $invoice)
    {
        $apiKey = Setting::get('payment.mayar_api_key');

        $data = [
            'name' => $invoice->customer->name,
            'email' => $invoice->customer->email ?? 'noemail@example.com',
            'amount' => (int) $invoice->total,
            'description' => 'Pembayaran Invoice ' . $invoice->invoice_number,
            'mobile' => $invoice->customer->phone ?? '081234567890',
            'redirect_url' => route('portal.invoices'),
        ];

        $response = Http::withToken($apiKey)->post('https://api.mayar.id/hl/v1/payment/create', $data);

        if ($response->successful() && isset($response->json()['data']['link'])) {
            return redirect()->away($response->json()['data']['link']);
        }

        Log::error('Mayar Payment Failed: ' . $response->body());
        return $this->fallbackToWhatsApp($invoice);
    }

    private function fallbackToWhatsApp(Invoice $invoice)
    {
        $phone = Setting::get('general.company_phone', '');
        $message = urlencode("Halo, saya ingin melakukan konfirmasi pembayaran untuk Invoice #{$invoice->invoice_number} sebesar Rp " . number_format((float) $invoice->total, 0, ',', '.'));
        $waUrl = "https://wa.me/{$phone}?text={$message}";

        return redirect()->away($waUrl);
    }

    private function showTripayChannels(Invoice $invoice)
    {
        $apiKey = Setting::get('payment.tripay_api_key');
        $mode = Setting::get('payment.tripay_mode', 'sandbox');

        $endpoint = $mode === 'production'
            ? 'https://tripay.co.id/api/merchant/payment-channel'
            : 'https://tripay.co.id/api-sandbox/merchant/payment-channel';

        try {
            $response = Http::withToken($apiKey)->get($endpoint);

            if ($response->successful() && $response->json('success')) {
                return view('portal.payments.tripay-methods', [
                    'invoice' => $invoice,
                    'channels' => $response->json('data')
                ]);
            }

            Log::error('Tripay Fetch Channels Failed: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Tripay Fetch Channels Error: ' . $e->getMessage());
        }

        // Fallback if failing to fetch channels
        return $this->processTripay($invoice, 'BRIVA');
    }

    private function showIpaymuChannels(Invoice $invoice)
    {
        $va = Setting::get('payment.ipaymu_va');
        $apiKey = Setting::get('payment.ipaymu_api_key');

        $url = str_starts_with($apiKey, 'SANDBOX')
            ? 'https://sandbox.ipaymu.com/api/v2/payment-method-list'
            : 'https://my.ipaymu.com/api/v2/payment-method-list';

        $body = ['account' => $va];
        $jsonBody = json_encode($body);
        $requestBody = strtolower(hash('sha256', $jsonBody));
        $stringToSign = "POST:" . $va . ":" . $requestBody . ":" . $apiKey;
        $signature = hash_hmac('sha256', $stringToSign, $apiKey);
        $timestamp = date('YmdHis');

        try {
            $response = Http::withHeaders([
                'va' => $va,
                'signature' => $signature,
                'timestamp' => $timestamp,
                'Content-Type' => 'application/json'
            ])->post($url, $body);

            if ($response->successful() && $response->json('Status') == 200) {
                return view('portal.payments.ipaymu-methods', [
                    'invoice' => $invoice,
                    'channels' => $response->json('Data')
                ]);
            }
            Log::error('iPaymu Fetch Channels Failed: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('iPaymu Fetch Channels Error: ' . $e->getMessage());
        }

        // Fallback: Hardcoded common methods for iPaymu
        $fallbackChannels = [
            ['PaymentMethod' => 'va', 'PaymentChannel' => 'bri', 'Description' => 'BRI Virtual Account'],
            ['PaymentMethod' => 'va', 'PaymentChannel' => 'bni', 'Description' => 'BNI Virtual Account'],
            ['PaymentMethod' => 'va', 'PaymentChannel' => 'mandiri', 'Description' => 'Mandiri Virtual Account'],
            ['PaymentMethod' => 'va', 'PaymentChannel' => 'bca', 'Description' => 'BCA Virtual Account'],
            ['PaymentMethod' => 'cstore', 'PaymentChannel' => 'alfamart', 'Description' => 'Alfamart'],
            ['PaymentMethod' => 'cstore', 'PaymentChannel' => 'indomaret', 'Description' => 'Indomaret'],
            ['PaymentMethod' => 'qris', 'PaymentChannel' => 'qris', 'Description' => 'QRIS (Gopay, OVO, Dana)']
        ];

        return view('portal.payments.ipaymu-methods', [
            'invoice' => $invoice,
            'channels' => $fallbackChannels
        ]);
    }

    private function showDuitkuChannels(Invoice $invoice)
    {
        $merchantCode = Setting::get('payment.duitku_merchant_code');
        $apiKey = Setting::get('payment.duitku_api_key');
        $datetime = date('Y-m-d H:i:s');
        $amount = (int) $invoice->total;

        // Duitku v2 getpaymentmethod signature: hash('sha256', merchantCode + amount + datetime + apiKey)
        $signature = hash('sha256', $merchantCode . $amount . $datetime . $apiKey);

        $url = str_starts_with($merchantCode, 'DS')
            ? 'https://sandbox.duitku.com/webapi/api/merchant/paymentmethod/getpaymentmethod'
            : 'https://passport.duitku.com/webapi/api/merchant/paymentmethod/getpaymentmethod';

        $body = [
            'merchantCode' => $merchantCode,
            'paymentAmount' => $amount,
            'datetime' => $datetime,
            'signature' => $signature
        ];

        try {
            $response = Http::post($url, $body);

            if ($response->successful() && isset($response->json()['paymentFee'])) {
                return view('portal.payments.duitku-methods', [
                    'invoice' => $invoice,
                    'channels' => $response->json()['paymentFee']
                ]);
            }
            Log::error('Duitku Fetch Channels Failed: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Duitku Fetch Channels Error: ' . $e->getMessage());
        }

        // Fallback: Verified working common methods for Duitku Sandbox
        $fallbackChannels = [
            ['method' => 'VC', 'name' => 'Kartu Kredit (Visa/Mastercard)'],
            ['method' => 'BC', 'name' => 'BCA Virtual Account'],
            ['method' => 'BR', 'name' => 'BRI Virtual Account'],
            ['method' => 'M2', 'name' => 'Mandiri Virtual Account'],
            ['method' => 'B1', 'name' => 'BNI Virtual Account'],
            ['method' => 'BT', 'name' => 'Permata Virtual Account'],
            ['method' => 'A1', 'name' => 'ATM Bersama'],
            ['method' => 'NC', 'name' => 'Bank Neo Commerce'],
            ['method' => 'I1', 'name' => 'Indomaret'],
            ['method' => 'OV', 'name' => 'OVO'],
            ['method' => 'SP', 'name' => 'ShopeePay'],
            ['method' => 'LA', 'name' => 'LinkAja'],
        ];

        return view('portal.payments.duitku-methods', [
            'invoice' => $invoice,
            'channels' => $fallbackChannels
        ]);
    }

    public function webhook(Request $request)
    {
        $gateway = $request->query('gateway', $request->input('gateway'));

        if ($gateway === 'ipaymu' || $request->has('sid')) {
            Log::info("iPaymu Webhook received: " . json_encode($request->all()));

            // Verify Signature
            $apiKey = Setting::get('payment.ipaymu_api_key');
            $va = Setting::get('payment.ipaymu_va');
            $status = $request->input('status');
            $reference = $request->input('reference');
            $trx_id = $request->input('trx_id');
            $receivedSignature = $request->header('signature'); // iPaymu sends signature in header

            // Note: iPaymu callback signature is typically a hash of the response body or specific fields
            // For simple verification, we can at least check if reference belongs to us
            // But ideally we should use their documented callback signature logic

            if ($status === 'berhasil' || $status === 'berhasil_va') {
                if (str_starts_with($reference, 'HTC-')) {
                    $transaction = \App\Models\HotspotTransaction::where('reference_number', $reference)->first();
                    if ($transaction) {
                        $this->markHotspotAsPaid($transaction, 'ipaymu', $trx_id);
                    }
                } else {
                    $invoice = Invoice::where('invoice_number', $reference)->first();
                    if ($invoice) {
                        $this->markInvoiceAsPaid(
                            $invoice,
                            $request->input('total_amount'),
                            'ipaymu',
                            $trx_id,
                            'Paid via iPaymu callback'
                        );
                    }
                }
            }
            return response()->json(['success' => true]);
        }

        if ($gateway === 'duitku' || $request->has('merchantCode')) {
            Log::info("Duitku Webhook received: " . json_encode($request->all()));

            $merchantCode = Setting::get('payment.duitku_merchant_code');
            $apiKey = Setting::get('payment.duitku_api_key');

            $amount = $request->input('amount');
            $merchantOrderId = $request->input('merchantOrderId');
            $signature = $request->input('signature');
            $resultCode = $request->input('resultCode');

            // Verify Signature
            $calcSignature = md5($merchantCode . $amount . $merchantOrderId . $apiKey);

            if ($signature === $calcSignature) {
                if ($resultCode === '00') {
                    if (str_starts_with($merchantOrderId, 'HTC-')) {
                        $transaction = \App\Models\HotspotTransaction::where('reference_number', $merchantOrderId)->first();
                        if ($transaction) {
                            $this->markHotspotAsPaid($transaction, 'duitku', $request->input('reference'));
                        }
                    } else {
                        $invoice = Invoice::where('invoice_number', $merchantOrderId)->first();
                        if ($invoice) {
                            $this->markInvoiceAsPaid(
                                $invoice,
                                $amount,
                                'duitku',
                                $request->input('reference'),
                                'Paid via Duitku callback'
                            );
                        }
                    }
                }
                return response('OK', 200);
            } else {
                Log::error('Duitku Invalid Signature', ['received' => $signature, 'calc' => $calcSignature]);
                return response('Invalid Signature', 400);
            }
        }

        // Assume Tripay if no gateway is specified (legacy behavior)
        $callbackSignature = $request->server('HTTP_X_CALLBACK_SIGNATURE');
        $json = $request->getContent();

        $privateKey = Setting::get('payment.tripay_private_key');
        $signature = hash_hmac('sha256', $json, $privateKey);

        if ($signature !== (string) $callbackSignature) {
            return response()->json(['success' => false, 'message' => 'Invalid signature'], 400);
        }

        $data = json_decode($json);

        if ($data && isset($data->status) && $data->status === 'PAID') {
            if (str_starts_with($data->merchant_ref, 'HTC-')) {
                $transaction = \App\Models\HotspotTransaction::where('reference_number', $data->merchant_ref)->first();
                if ($transaction) {
                    $this->markHotspotAsPaid($transaction, $data->payment_method ?? 'tripay', $data->reference);
                }
            } else {
                $invoice = Invoice::where('invoice_number', $data->merchant_ref)->first();
                if ($invoice) {
                    $this->markInvoiceAsPaid(
                        $invoice,
                        $data->total_amount,
                        $data->payment_method ?? 'tripay',
                        $data->reference,
                        'Paid via Tripay callback'
                    );
                }
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Helper to mark invoice as paid and record history
     */
    private function markInvoiceAsPaid(Invoice $invoice, $amount, $method, $reference, $notes)
    {
        if ($invoice->status !== 'paid') {
            $invoice->update([
                'status' => 'paid',
                'paid_at' => now(),
                'payment_method' => $method
            ]);

            // Record History
            \App\Models\InvoicePaymentHistory::create([
                'invoice_id' => $invoice->id,
                'amount' => $amount,
                'payment_method' => $method,
                'payment_date' => now(),
                'reference_number' => $reference,
                'notes' => $notes,
                'created_by' => null, // System
            ]);

            Log::info("Invoice #{$invoice->invoice_number} marked as paid via {$method}");
        }
    }

    private function markHotspotAsPaid(\App\Models\HotspotTransaction $transaction, $method, $reference)
    {
        if ($transaction->status !== 'paid') {
            // Update transaction status
            $transaction->update([
                'status' => 'paid',
                'payment_method' => $method,
                'external_reference' => $reference,
                'paid_at' => now(),
            ]);

            // Generate Voucher
            $profile = \App\Models\HotspotProfile::find($transaction->hotspot_profile_id);
            if ($profile) {
                // Generate secure alphanumeric code
                $chars = 'abcdefghijkmnpqrstuvwxyz23456789';
                $code = '';
                do {
                    $code = '';
                    for ($i = 0; $i < 6; $i++) {
                        $code .= $chars[random_int(0, strlen($chars) - 1)];
                    }
                } while (\App\Models\HotspotVoucher::where('code', $code)->exists());

                $voucher = \App\Models\HotspotVoucher::create([
                    'hotspot_profile_id' => $profile->id,
                    'code' => $code,
                    'password' => $code, // Default user_mode = username_equals_password for guest portal
                    'batch_id' => $transaction->reference_number, // Use transaction ID as batch
                    'status' => 'active',
                    'user_mode' => 'username_equals_password',
                    'created_by' => null, // System generated
                ]);

                // Link voucher to transaction
                $transaction->update(['hotspot_voucher_id' => $voucher->id]);

                // Sync to Radius
                $voucherData = $voucher->toArray();
                $voucherData['profile_name'] = $profile->name;
                $voucherData['mikrotik_group'] = $profile->mikrotik_group;
                $voucherData['nas_shortname'] = null;

                \App\Jobs\SyncHotspotVouchersJob::dispatch([$voucherData]);
            }

            Log::info("Hotspot Transaction #{$transaction->reference_number} marked as paid. Voucher generated.");
        }
    }
}
