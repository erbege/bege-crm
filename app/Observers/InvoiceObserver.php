<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Models\Subscription;

class InvoiceObserver
{
    /**
     * Handle the Invoice "updated" event.
     */
    public function updated(Invoice $invoice): void
    {
        static $processedInvoices = [];

        // Check if status changed to 'paid'
        if ($invoice->wasChanged('status') && $invoice->status === 'paid') {

            // Prevent duplicate creation of notifications within the same request lifecycle
            if (isset($processedInvoices[$invoice->id])) {
                return;
            }
            $processedInvoices[$invoice->id] = true;

            // Check if this invoice is linked to a subscription
            if ($invoice->subscription_id) {
                $subscription = Subscription::find($invoice->subscription_id);

                if ($subscription) {
                    // Check if there are any other UNPAID invoices for this customer
                    $hasOtherUnpaid = Invoice::where('customer_id', $subscription->customer_id)
                        ->where('status', 'unpaid')
                        ->where('id', '!=', $invoice->id)
                        ->exists();

                    $hasOverdue = Invoice::where('customer_id', $subscription->customer_id)
                        ->where('status', 'unpaid')
                        ->where('due_date', '<', now()->startOfDay())
                        ->exists();

                    $newStatus = 'active';
                    if ($hasOtherUnpaid) {
                        $newStatus = $hasOverdue ? 'suspended' : 'pending';
                    }

                    // Only update if status should change (e.g., from pending/suspended to active)
                    if ($subscription->status !== $newStatus) {
                        $subscription->update([
                            'status' => $newStatus,
                        ]);
                        \Log::info("Subscription #{$subscription->id} updated to {$newStatus} after invoice payment.");
                    }
                }
            }

            // Send WhatsApp Notification for PELUNASAN
            $invoice->loadMissing(['customer', 'subscription.package']);
            if ($invoice->customer && $invoice->customer->phone) {
                $phone = preg_replace('/[^0-9]/', '', $invoice->customer->phone);
                if (substr($phone, 0, 1) === '0') {
                    $phone = '62' . substr($phone, 1);
                }

                $waMessage = \App\Models\WhatsappMessage::create([
                    'target' => $phone,
                    'template_name' => 'PELUNASAN',
                    'template_data' => [
                        'invoice' => $invoice->invoice_number,
                        'nama_pelanggan' => $invoice->customer->name,
                        'nolayanan' => $invoice->customer->customer_id ?? ($invoice->subscription_id ?? '-'),
                        'profile' => optional(optional($invoice->subscription)->package)->name ?? '-',
                        'channel' => strtoupper($invoice->payment_method ?? 'TRANSFER'),
                        'total' => $invoice->formatted_total,
                        'tgl_lunas' => now()->format('d/m/Y H:i'),
                        'tgl_isolir' => optional($invoice->subscription)->period_end ? $invoice->subscription->period_end->format('d/m/Y') : '-',
                    ],
                    'status' => 'pending',
                    'provider' => \App\Models\Setting::get('whatsapp.provider', 'fonnte'),
                ]);

                \App\Jobs\SendWhatsappNotificationJob::dispatch($waMessage);
            }
        }

        // Check if status changed from 'paid' back to 'unpaid' (Rollback)
        if ($invoice->wasChanged('status') && $invoice->getOriginal('status') === 'paid' && $invoice->status === 'unpaid') {
            if ($invoice->subscription_id) {
                $subscription = Subscription::with('customer')->find($invoice->subscription_id);

                if ($subscription) {
                    // Rule 1: First Invoice Rollback -> 'pending'
                    $isFirstSubscription = !Subscription::where('customer_id', $subscription->customer_id)
                        ->where('id', '<', $subscription->id)
                        ->exists();

                    if ($isFirstSubscription) {
                        $newStatus = 'pending';
                    } else {
                        // Rule 2 & 3: Check if overdue
                        // Overdue if due_date is strictly before today
                        $isOverdue = $invoice->due_date->startOfDay()->isPast();
                        $newStatus = $isOverdue ? 'suspended' : 'active';
                    }

                    if ($subscription->status !== $newStatus) {
                        $subscription->update(['status' => $newStatus]);
                        \Log::info("Subscription #{$subscription->id} reverted to {$newStatus} after invoice rollback.");
                    }
                }
            }
        }
    }
}
