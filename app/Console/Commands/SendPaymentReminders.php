<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Setting;
use App\Models\WhatsappMessage;
use App\Jobs\SendWhatsappNotificationJob;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendPaymentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send payment reminders for unpaid invoices (3 days before and on due date)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if reminders are enabled in settings
        if (!Setting::get('billing.send_payment_reminders', false)) {
            $this->info("Payment reminders are disabled in settings.");
            return Command::SUCCESS;
        }

        $this->info("Checking for invoices needing reminders...");

        $today = now()->startOfDay();
        $threeDaysFromNow = $today->copy()->addDays(3);

        // 1. Reminders for 3 days before due date
        $reminders3Days = Invoice::where('status', 'unpaid')
            ->whereDate('due_date', $threeDaysFromNow)
            ->with('customer')
            ->get();

        $this->info("Sending 3-day reminders for " . $reminders3Days->count() . " invoices...");
        foreach ($reminders3Days as $invoice) {
            $this->sendReminder($invoice, 'PENGINGAT_3_HARI');
        }

        // 2. Reminders for today (due date)
        $remindersToday = Invoice::where('status', 'unpaid')
            ->whereDate('due_date', $today)
            ->with('customer')
            ->get();

        $this->info("Sending due-date reminders for " . $remindersToday->count() . " invoices...");
        foreach ($remindersToday as $invoice) {
            $this->sendReminder($invoice, 'PENAGIHAN'); // Use standard PENAGIHAN template or specific one
        }

        $this->info("Payment reminders completed.");

        return Command::SUCCESS;
    }

    /**
     * Send WhatsApp reminder for the invoice.
     */
    protected function sendReminder(Invoice $invoice, string $templateName): void
    {
        $customer = $invoice->customer;
        if (!$customer || !$customer->phone) {
            return;
        }

        $phone = preg_replace('/[^0-9]/', '', $customer->phone);
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        // Create WhatsApp Message record
        $waMessage = WhatsappMessage::create([
            'target' => $phone,
            'template_name' => $templateName,
            'template_data' => [
                'invoice' => $invoice->invoice_number,
                'nama_pelanggan' => $customer->name,
                'nolayanan' => $customer->customer_id ?? ($invoice->subscription_id ?? '-'),
                'profile' => optional(optional($invoice->subscription)->package)->name ?? '-',
                'jatuh_tempo' => $invoice->due_date->format('d/m/Y'),
                'total' => $invoice->formatted_total,
                'link_invoice' => url('/'),
            ],
            'status' => 'pending',
            'provider' => Setting::get('whatsapp.provider', 'fonnte'),
        ]);

        // Dispatch Job
        SendWhatsappNotificationJob::dispatch($waMessage);

        $this->line("  → Reminder queued for {$customer->name} (Invoice: {$invoice->invoice_number})");
    }
}
