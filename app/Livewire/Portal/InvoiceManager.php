<?php

namespace App\Livewire\Portal;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Invoice;

class InvoiceManager extends Component
{
    use WithPagination;


    public $selectedInvoice = null;
    public $statusFilter = '';

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function setStatusFilter($status)
    {
        $this->statusFilter = $status;
        $this->resetPage();
    }

    public function viewDetail($id)
    {
        $this->selectedInvoice = Invoice::with(['subscription.package', 'paymentHistories.user'])
            ->where('customer_id', auth('customer')->id())
            ->findOrFail($id);

        $this->dispatch('open-detail-modal');
    }

    public function closeDetailModal()
    {
        $this->selectedInvoice = null;
    }

    public function downloadPdf($id)
    {
        return config('app.url') . "/invoice/{$id}/pdf";
    }

    public function payInvoice($id)
    {
        return redirect()->route('portal.invoices.pay', $id);
    }

    #[Layout('components.layouts.portal')]
    public function render()
    {
        $customer = auth('customer')->user();

        // Base query for stats (all customer invoices)
        $allInvoices = Invoice::where('customer_id', $customer->id)->get();
        $unpaidInvoices = $allInvoices->where('status', 'unpaid')->sortBy('due_date');

        $totalUnpaid = $unpaidInvoices->sum('total');
        $nearestDue = $unpaidInvoices->first()?->due_date;
        $unpaidCount = $unpaidInvoices->count();

        // Main paginated query with eager loading
        $query = Invoice::with(['subscription.package'])
            ->where('customer_id', $customer->id);

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $invoices = $query->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.portal.invoice-manager', [
            'invoices' => $invoices,
            'totalUnpaid' => $totalUnpaid,
            'nearestDue' => $nearestDue,
            'unpaidCount' => $unpaidCount,
        ]);
    }
}
