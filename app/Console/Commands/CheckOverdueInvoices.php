<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckOverdueInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:check-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for overdue invoices and suspend associated subscriptions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for overdue invoices...');

        // Find unpaid invoices that are past their due date
        $overdueInvoices = Invoice::query()
            ->where('status', 'unpaid')
            ->where('due_date', '<', now()->startOfDay()) // Strictly less than today (yesterday or earlier)
            ->with('subscription.customer')
            ->get();

        $count = 0;

        foreach ($overdueInvoices as $invoice) {
            $subscription = $invoice->subscription;

            if (!$subscription) {
                continue;
            }

            // Only suspend/cancel if currently active or pending (don't touch cancelled)
            if (in_array($subscription->status, ['active', 'pending'])) {

                // Check if this is the customer's first subscription ever
                $isFirstSubscription = !Subscription::where('customer_id', $subscription->customer_id)
                    ->where('id', '<', $subscription->id)
                    ->exists();

                $newStatus = $isFirstSubscription ? 'cancelled' : 'suspended';
                $statusLabel = $newStatus === 'cancelled' ? 'Dibatalkan (Tagihan Awal)' : 'Terisolir (Tunggakan)';

                $this->info("Updating subscription #{$subscription->id} (Customer: {$subscription->customer->name}) to {$statusLabel} due to overdue Invoice #{$invoice->invoice_number}");

                $subscription->update([
                    'status' => $newStatus,
                    'notes' => $subscription->notes . "\n[System] Updated to {$newStatus} due to overdue invoice #{$invoice->invoice_number} on " . now()->format('Y-m-d H:i')
                ]);

                // Log activity
                Log::info("Subscription #{$subscription->id} status set to {$newStatus} due to overdue invoice #{$invoice->invoice_number}");

                $count++;
            }
        }

        $this->info("Processed {$count} suspensions.");
    }
}
