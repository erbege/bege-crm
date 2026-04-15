<?php

use App\Models\Customer;
use App\Models\Package;
use App\Models\Subscription;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting verification...\n";

$customer = Customer::first();
$package = Package::first();

if (!$customer || !$package) {
    echo "Error: No customer or package found.\n";
    exit(1);
}

DB::beginTransaction();
try {
    $subData = [
        'customer_id' => $customer->id,
        'package_id' => $package->id,
        'period_start' => now(),
        'period_end' => now()->addMonth(),
        'installation_date' => now(),
        'status' => 'unpaid',
    ];

    $subscription = Subscription::create($subData);
    echo "Subscription created: ID {$subscription->id}\n";

    // Check if financial fields are accessible (should be null/absent)
    if (isset($subscription->amount)) {
        echo "WARNING: amount field is still accessible (possibly via accessor or DB column still exists?)\n";
    }

    $invoiceData = [
        'invoice_number' => 'INV-TEST-001',
        'subscription_id' => $subscription->id,
        'customer_id' => $subscription->customer_id,
        'issue_date' => now(),
        'due_date' => now()->addDays(7),
        'subtotal' => 100000,
        'tax' => 11000,
        'total' => 111000,
        'status' => 'unpaid',
    ];

    $invoice = Invoice::create($invoiceData);
    echo "Invoice created: ID {$invoice->id}, Total {$invoice->total}\n";

    echo "SUCCESS: Subscription and Invoice created without error.\n";

} catch (\Exception $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
} finally {
    DB::rollBack();
    echo "Rolled back.\n";
}
