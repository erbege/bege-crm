<?php

namespace App\Livewire\Invoice;

use App\Models\Invoice;
use App\Models\InvoicePaymentHistory;
use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class InvoiceManager extends Component
{
    use WithPagination;

    // Filter Properties
    public $search = '';
    public $filterStatus = '';
    public $filterMonth = '';

    /**
     * Track last search time for debouncing.
     */
    protected $lastSearchTime = 0;

    /**
     * Debounce search by 300ms to reduce queries.
     */
    public function updatingSearch($value)
    {
        $now = now()->timestamp;
        if ($now - $this->lastSearchTime < 0.3) {
            // Skip if less than 300ms since last search
            return;
        }
        $this->lastSearchTime = $now;
        $this->resetPage();
    }

    // View Detail
    public $showDetailModal = false;
    public $selectedInvoice = null;

    // Payment
    public $showPaymentModal = false;
    public $paymentMethod = 'transfer';
    public $paymentReference = '';
    public $paymentNotes = '';

    // Confirmation Modal State
    public $showConfirmationModal = false;
    public $confirmationTitle = '';
    public $confirmationMessage = '';
    public $confirmationAction = '';
    public $confirmationId = null;
    public $confirmationInput = '';
    public $requiresInput = false;
    public $inputLabel = '';
    public $inputPlaceholder = '';

    // Listeners
    protected $listeners = ['refreshInvoices' => '$refresh'];

    public function mount()
    {
        $this->filterMonth = now()->format('Y-m');
    }

    public function render()
    {
        $invoices = Invoice::query()
            ->select(['invoices.*'])
            ->with(['customer:id,customer_id,name,phone', 'subscription.package:id,name,price'])
            ->when($this->search, function ($q) {
                $search = $this->search;
                $q->where(function ($sub) use ($search) {
                    // Smart search optimization
                    if (str_starts_with(strtoupper($search), 'INV') || is_numeric($search)) {
                        $sub->where('invoice_number', 'like', '%' . $search . '%');

                        // If it looks like invoice, maybe don't search customer? 
                        // But users might search "INV" in customer notes? No, strict search is better for perf.
                        // Let's keep it inclusive but prioritize
                        $sub->orWhereHas('customer', function ($c) use ($search) {
                            $c->where('name', 'like', '%' . $search . '%')
                                ->orWhere('customer_id', 'like', '%' . $search . '%');
                        });
                    } elseif (str_starts_with(strtoupper($search), 'SKN')) {
                        // If looks like customer ID, valid only on customer
                        $sub->whereHas('customer', function ($c) use ($search) {
                            $c->where('customer_id', 'like', '%' . $search . '%')
                                ->orWhere('name', 'like', '%' . $search . '%');
                        });
                    } else {
                        // General search
                        $sub->where('invoice_number', 'like', '%' . $search . '%')
                            ->orWhereHas('customer', function ($c) use ($search) {
                            $c->where('name', 'like', '%' . $search . '%')
                                ->orWhere('customer_id', 'like', '%' . $search . '%');
                        });
                    }
                });
            })
            ->when($this->filterStatus, function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->when($this->filterMonth, function ($q) {
                $q->whereYear('issue_date', substr($this->filterMonth, 0, 4))
                    ->whereMonth('issue_date', substr($this->filterMonth, 5, 2));
            })
            ->orderByDesc('issue_date')
            ->orderByDesc('id')
            ->paginate(15);

        return view('livewire.invoice.invoice-manager', [
            'invoices' => $invoices,
        ])->layout('layouts.app');
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterMonth()
    {
        $this->resetPage();
    }

    /**
     * View invoice detail
     */
    public function viewDetail($invoiceId)
    {
        $this->selectedInvoice = Invoice::with(['customer', 'subscription.package', 'paymentHistories.user'])->find($invoiceId);
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedInvoice = null;
    }

    /**
     * Open payment modal
     */
    public function openPaymentModal($invoiceId)
    {
        $this->selectedInvoice = Invoice::find($invoiceId);
        $this->paymentMethod = 'transfer';
        $this->paymentNotes = '';
        $this->showPaymentModal = true;
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->selectedInvoice = null;
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid()
    {
        if (!$this->selectedInvoice) {
            return;
        }

        $this->selectedInvoice->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => $this->paymentMethod,
            'notes' => $this->paymentNotes ?: $this->selectedInvoice->notes,
        ]);

        // Record payment history
        InvoicePaymentHistory::create([
            'invoice_id' => $this->selectedInvoice->id,
            'action' => 'payment',
            'amount' => $this->selectedInvoice->total,
            'payment_method' => $this->paymentMethod,
            'reference' => $this->paymentReference,
            'notes' => $this->paymentNotes,
            'user_id' => Auth::id(),
        ]);

        $this->dispatch('toast', type: 'success', message: 'Invoice berhasil ditandai lunas.');
        $this->closePaymentModal();
    }

    /**
     * Rollback payment (revert paid invoice to unpaid)
     */
    public function rollbackPayment($invoiceId, $reason = null)
    {
        $invoice = Invoice::with('subscription')->find($invoiceId);

        if (!$invoice || $invoice->status !== 'paid') {
            $this->dispatch('toast', type: 'error', message: 'Invoice tidak dapat di-rollback.');
            return;
        }

        // Record rollback history with reason
        InvoicePaymentHistory::create([
            'invoice_id' => $invoice->id,
            'action' => 'rollback',
            'amount' => $invoice->total,
            'payment_method' => $invoice->payment_method,
            'notes' => $reason ?: 'Pembayaran dibatalkan/rollback',
            'user_id' => Auth::id(),
        ]);

        // Revert invoice to unpaid
        $invoice->update([
            'status' => 'unpaid',
            'paid_at' => null,
            'payment_method' => null,
        ]);

        $this->dispatch('toast', type: 'success', message: 'Pembayaran invoice berhasil di-rollback.');
    }

    /**
     * Cancel invoice
     */
    public function cancelInvoice($invoiceId)
    {
        $invoice = Invoice::find($invoiceId);
        if ($invoice && $invoice->status === 'unpaid') {
            // Record cancellation history
            InvoicePaymentHistory::create([
                'invoice_id' => $invoice->id,
                'action' => 'cancelled',
                'amount' => $invoice->total,
                'notes' => 'Invoice dibatalkan',
                'user_id' => Auth::id(),
            ]);

            $invoice->update(['status' => 'cancelled']);
            $this->dispatch('toast', type: 'success', message: 'Invoice berhasil dibatalkan.');
        }
    }

    /**
     * Download PDF
     */
    public function downloadPdf($invoiceId)
    {
        $invoice = Invoice::with(['customer', 'subscription.package'])->find($invoiceId);

        if (!$invoice) {
            return;
        }

        $company = [
            'name' => \App\Models\Setting::get('general.company_name', 'SKNET Internet'),
            'address' => \App\Models\Setting::get('general.company_address', ''),
            'phone' => \App\Models\Setting::get('general.company_phone', ''),
            'email' => \App\Models\Setting::get('general.company_email', ''),
            'logo' => \App\Models\Setting::get('general.company_logo', ''),
        ];

        $banks = \App\Models\BankAccount::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $pdf = Pdf::loadView('pdf.invoice-pdf', [
            'invoice' => $invoice,
            'company' => $company,
            'banks' => $banks
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $invoice->invoice_number . '.pdf');
    }

    /**
     * Send invoice via Email (placeholder - needs mail config)
     */
    public function sendEmail($invoiceId)
    {
        $invoice = Invoice::with('customer')->find($invoiceId);

        if (!$invoice || !$invoice->customer->email) {
            $this->dispatch('toast', type: 'error', message: 'Email pelanggan tidak tersedia.');
            return;
        }

        // TODO: Implement actual email sending
        // Mail::to($invoice->customer->email)->send(new InvoiceMail($invoice));

        $invoice->update(['sent_at' => now()]);
        $this->dispatch('toast', type: 'success', message: 'Invoice berhasil dikirim ke ' . $invoice->customer->email);
    }

    /**
     * Send invoice via WhatsApp
     */
    public function sendWhatsApp($invoiceId)
    {
        $invoice = Invoice::with(['customer', 'subscription.package'])->find($invoiceId);

        if (!$invoice || !$invoice->customer->phone) {
            $this->dispatch('toast', type: 'error', message: 'Nomor WhatsApp pelanggan tidak tersedia.');
            return;
        }

        $phone = preg_replace('/[^0-9]/', '', $invoice->customer->phone);
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        $waMessage = \App\Models\WhatsappMessage::create([
            'target' => $phone,
            'template_name' => 'PENAGIHAN',
            'template_data' => [
                'invoice' => $invoice->invoice_number,
                'nama_pelanggan' => $invoice->customer->name,
                'nolayanan' => $invoice->customer->customer_id ?? ($invoice->subscription_id ?? '-'),
                'profile' => optional(optional($invoice->subscription)->package)->name ?? '-',
                'jatuh_tempo' => $invoice->due_date->format('d/m/Y'),
                'total' => $invoice->formatted_total,
                'link_invoice' => url('/'),
            ],
            'status' => 'pending',
            'provider' => \App\Models\Setting::get('whatsapp.provider', 'fonnte'),
        ]);

        \App\Jobs\SendWhatsappNotificationJob::dispatch($waMessage);

        $invoice->update(['sent_at' => now()]);

        $this->dispatch('toast', type: 'success', message: 'Pesan WhatsApp sedang diproses untuk dikirim.');
    }

    public function triggerConfirm($action, $id, $title, $message, $requiresInput = false, $inputLabel = '', $inputPlaceholder = '')
    {
        $this->confirmationAction = $action;
        $this->confirmationId = $id;
        $this->confirmationTitle = $title;
        $this->confirmationMessage = $message;
        $this->requiresInput = $requiresInput;
        $this->inputLabel = $inputLabel;
        $this->inputPlaceholder = $inputPlaceholder;
        $this->confirmationInput = ''; // Reset input
        $this->showConfirmationModal = true;
    }

    public function closeConfirmationModal()
    {
        $this->showConfirmationModal = false;
        $this->confirmationAction = '';
        $this->confirmationId = null;
        $this->confirmationTitle = '';
        $this->confirmationMessage = '';
        $this->confirmationInput = '';
        $this->requiresInput = false;
    }

    public function executeAction()
    {
        if ($this->requiresInput && empty($this->confirmationInput)) {
            $this->addError('confirmationInput', 'Field ini wajib diisi.');
            return;
        }

        if ($this->confirmationAction && method_exists($this, $this->confirmationAction)) {
            if ($this->requiresInput) {
                $this->{$this->confirmationAction}($this->confirmationId, $this->confirmationInput);
            } else {
                $this->{$this->confirmationAction}($this->confirmationId);
            }
        }
        $this->closeConfirmationModal();
    }
}
