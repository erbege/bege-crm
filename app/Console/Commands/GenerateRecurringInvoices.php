<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateRecurringInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:generate-recurring 
                            {--dry-run : Run without creating actual records}
                            {--date= : Process as if today is this date (Y-m-d format)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate recurring invoices for subscriptions with expired periods';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $processDate = $this->option('date')
            ? Carbon::parse($this->option('date'))
            : now();

        $this->info("Processing recurring invoices as of: {$processDate->format('Y-m-d')}");

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No records will be created');
        }

        // Find subscriptions that need new invoices
        // Conditions:
        // 1. period_end has passed (or is today)
        // 2. Not cancelled
        // 3. No newer subscription exists for the same customer
        $subscriptions = Subscription::query()
            ->where('period_end', '<=', $processDate)
            ->where('status', '!=', 'cancelled')
            ->with(['customer', 'package', 'coveragePoint'])
            ->get();

        $processed = 0;
        $skipped = 0;

        foreach ($subscriptions as $subscription) {
            // Check if there's already a newer subscription for this customer
            $hasNewerSubscription = Subscription::where('customer_id', $subscription->customer_id)
                ->where('period_start', '>', $subscription->period_end)
                ->exists();

            if ($hasNewerSubscription) {
                $this->line("  Skipped: {$subscription->customer->name} - Already has newer subscription");
                $skipped++;
                continue;
            }

            // Calculate new period
            $newPeriodStart = Carbon::parse($subscription->period_end)->addDay();
            $newPeriodEnd = $newPeriodStart->copy()->endOfMonth();

            // Check if we already created subscription for this period
            $existingSubscription = Subscription::where('customer_id', $subscription->customer_id)
                ->where('period_start', $newPeriodStart)
                ->first();

            if ($existingSubscription) {
                $this->line("  Skipped: {$subscription->customer->name} - Subscription for {$newPeriodStart->format('M Y')} already exists");
                $skipped++;
                continue;
            }

            $this->info("  Processing: {$subscription->customer->name} ({$subscription->customer->customer_id})");
            $this->line("    Old period: {$subscription->period_start->format('d/m/Y')} - {$subscription->period_end->format('d/m/Y')}");
            $this->line("    New period: {$newPeriodStart->format('d/m/Y')} - {$newPeriodEnd->format('d/m/Y')}");

            if (!$dryRun) {
                // Get Settings
                $taxPercentage = \App\Models\Setting::get('billing.tax_percentage', 0);
                $gracePeriodDays = \App\Models\Setting::get('billing.grace_period_days', 7);

                // Calculate Amounts
                $amount = $subscription->package->price ?? 0;
                $tax = ceil(($amount * $taxPercentage) / 100);
                $total = ceil($amount + $tax);

                // Calculate Dates
                // Issue date is start of new period
                // Due date is issue date + grace period
                $issueDate = $newPeriodStart->copy();
                $dueDate = $issueDate->copy()->addDays($gracePeriodDays);

                // Create new subscription
                $newSubscription = Subscription::create([
                    'customer_id' => $subscription->customer_id,
                    'package_id' => $subscription->package_id,
                    'coverage_point_id' => $subscription->coverage_point_id,
                    'period_start' => $newPeriodStart,
                    'period_end' => $newPeriodEnd,
                    'installation_date' => $subscription->installation_date,
                    'status' => 'pending',
                    'pppoe_username' => $subscription->pppoe_username,
                    'pppoe_password' => $subscription->pppoe_password,
                    'device_sn' => $subscription->device_sn,
                ]);

                Invoice::create([
                    'invoice_number' => Invoice::generateInvoiceNumber(),
                    'subscription_id' => $newSubscription->id,
                    'customer_id' => $newSubscription->customer_id,
                    'issue_date' => $issueDate,
                    'due_date' => $dueDate,
                    'subtotal' => $amount,
                    'tax' => $tax, // Include Tax
                    'installation_fee' => 0,
                    'discount' => 0,
                    'total' => $total, // Total includes tax
                    'status' => 'unpaid',
                ]);

                $this->info("    ✓ Created subscription and invoice");
            }

            $processed++;
        }

        $this->newLine();
        $this->info("Summary:");
        $this->line("  Processed: {$processed}");
        $this->line("  Skipped: {$skipped}");

        if ($dryRun && $processed > 0) {
            $this->newLine();
            $this->warn("Run without --dry-run to create actual records");
        }

        return Command::SUCCESS;
    }
}
