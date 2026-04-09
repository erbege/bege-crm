<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Models\Subscription;
use App\Services\RadiusSyncService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DailyBillingCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:daily-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily check for billing, invoice generation, and isolation.';

    protected $radiusService;

    public function __construct(RadiusSyncService $radiusService)
    {
        parent::__construct();
        $this->radiusService = $radiusService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Daily Billing Check...');

        // 1. Generate Recurring Invoices (Integration with existing command)
        $this->info('Generating Recurring Invoices...');
        $this->call('invoices:generate-recurring');

        // 2. Isolation Check (Isolir Otomatis)
        $this->info('Checking for Overdue Subscriptions to Isolate...');
        $this->checkAndIsolateOverdue();

        $this->info('Daily Billing Check Completed.');
    }

    private function checkAndIsolateOverdue()
    {
        // Get Grace Period from Settings
        $gracePeriodDays = (int) Setting::get('billing.grace_period_days', 3);

        $today = Carbon::now();

        // Find Unpaid Subscriptions
        // Logic: Status is 'unpaid' AND (period_end + grace period) < today
        // Note: Subscription model has no due_date, we calculate it from period_end + grace? 
        // Or we assume period_end IS the due date?
        // User flow says: "Cek pelanggan yang sudah melewati batas waktu toleransi bayar."

        // We will assume period_end is the billing cycle end.
        // Due date = period_end (or period_start + X days).
        // Let's use period_end + grace period as the cutoff for isolation.

        $subscriptions = Subscription::where('status', 'pending')
            ->whereNotNull('period_end')
            ->get();

        $isolatedCount = 0;

        /** @var \App\Models\Subscription $subscription */
        foreach ($subscriptions as $subscription) {
            // Calculate cutoff date based on period_start + grace period
            $cutoffDate = $subscription->period_start->copy()->addDays($gracePeriodDays)->endOfDay();

            if ($today->gt($cutoffDate)) {
                $this->info("Updating Subscription: {$subscription->customer->name} (ID: {$subscription->id}) - Grace period ended {$cutoffDate->format('Y-m-d')}");

                // Check if this is the customer's first subscription ever
                $isFirstSubscription = !\App\Models\Subscription::where('customer_id', $subscription->customer_id)
                    ->where('id', '<', $subscription->id)
                    ->exists();

                $newStatus = $isFirstSubscription ? 'cancelled' : 'suspended';

                // Update Status
                $subscription->status = $newStatus;
                $subscription->save(); // This triggers SubscriptionObserver -> RadiusSyncService::sync()

                // Explicitly sync just in case, though Observer should handle it.
                // $this->radiusService->sync($subscription);

                // Add Log/Notification logic here if needed
                Log::info("Subscription isolated due to non-payment: ID {$subscription->id}");

                $isolatedCount++;
            }
        }

        $this->info("Total Subscriptions Isolated: {$isolatedCount}");
    }
}
