<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Models\Customer;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\CustomerReportExport;
use Maatwebsite\Excel\Facades\Excel;

class CustomerReport extends Component
{
    public $startDate;
    public $endDate;
    public $statusFilter = 'all'; // all, active, suspended, terminated, etc.

    public function mount()
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    private function getData()
    {
        // Summary Stats (Snapshot at current time, not historical unless we have history table)
        // For report period, we can show "New Customers" in this period.

        $totalCustomers = Customer::count();
        $activeCustomers = Customer::active()->count();
        $suspendedCustomers = Customer::suspended()->count();
        $cancelledCustomers = Customer::terminated()->count();
        $pendingCustomers = Customer::pending()->count();
        $inactiveCustomers = Customer::inactive()->count();

        // List of Customers based on filter
        $query = Customer::query();

        if ($this->statusFilter !== 'all') {
            switch ($this->statusFilter) {
                case 'active':
                    $query->active();
                    break;
                case 'suspended':
                    $query->suspended();
                    break;
                case 'terminated':
                    $query->terminated();
                    break;
                case 'pending':
                    $query->pending();
                    break;
                case 'inactive':
                    $query->inactive();
                    break;
            }
        }

        // Optional: Filter by Registration Date if needed, but usually customer report lists all current status
        // OR lists customers *registered* in that period.
        // Let's list customers registered in period OR all customers if date range is ignored?
        // User asked for "laporan keseluruhan pelanggan terdaftar, pelanggan aktif... dibuat per hari, bulan"
        // This implies a snapshot or a list of people who *were* active?
        // For simplicity: List all customers matching status, and maybe highlight those registered in date range.
        // Let's filter by created_at for "New Customers" context, but for "All Customers" we might want everyone.

        // Interpretation: "Laporan Pelanggan" usually means "List of current customers".
        // But "Per hari/bulan/tahun" implies historical data.
        // Since we don't have a snapshot table, we can show:
        // 1. Customers registered in this period.
        // 2. Customers who are *currently* in specific status.

        // Let's apply date filter to `created_at` ONLY if user wants "New Customers".
        // But the request says "report made per day/month".
        // Use case: "Who joined this month?" -> Filter by created_at.
        // Use case: "Who is active now?" -> Status filter (ignore date? or just show all).

        // Compromise:
        // Show STATS for the period (New customers count).
        // Table lists ALL customers matching the status filter, column `created_at` shows when they joined.
        // If status filter is 'all', show everyone.

        // Let's add a toggle or just use date for "Registration Date".
        // Actually, let's filter by `created_at` range to show "Growth".
        // AND show a separate "Current State" summary.

        // Let's stick to: List customers matching Status.
        // AND option to filter by Created Date.

        $query->whereBetween('created_at', [
            Carbon::parse($this->startDate)->startOfDay(),
            Carbon::parse($this->endDate)->endOfDay()
        ]);

        $newCustomers = $query->count();
        $customers = $query->with(['activeSubscription.package'])->latest()->get();

        return [
            'totalCustomers' => $totalCustomers,
            'activeCustomers' => $activeCustomers,
            'suspendedCustomers' => $suspendedCustomers,
            'cancelledCustomers' => $cancelledCustomers,
            'pendingCustomers' => $pendingCustomers,
            'inactiveCustomers' => $inactiveCustomers,
            'newCustomersCount' => $newCustomers,
            'customers' => $customers
        ];
    }

    public function render()
    {
        return view('livewire.reports.customer-report', $this->getData())->layout('layouts.app');
    }

    public function exportPdf()
    {
        $data = $this->getData();
        $data['startDate'] = $this->startDate;
        $data['endDate'] = $this->endDate;
        $data['statusFilter'] = $this->statusFilter;

        // Company info for kop surat
        $data['companyName'] = \App\Models\Setting::get('general.company_name', config('app.name', 'SKNET CRM'));
        $data['companyAddress'] = \App\Models\Setting::get('general.company_address', '');
        $data['companyPhone'] = \App\Models\Setting::get('general.company_phone', '');
        $data['companyEmail'] = \App\Models\Setting::get('general.company_email', '');

        $logoPath = \App\Models\Setting::get('general.company_logo', '');
        $data['companyLogo'] = $logoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($logoPath)
            ? \Illuminate\Support\Facades\Storage::disk('public')->path($logoPath)
            : '';

        $pdf = Pdf::loadView('reports.customer-pdf', $data)
            ->setPaper('a4', 'landscape');
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Laporan-Pelanggan-' . date('Y-m-d') . '.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(
            new CustomerReportExport($this->startDate, $this->endDate, $this->statusFilter),
            'Laporan-Pelanggan-' . date('Y-m-d') . '.xlsx'
        );
    }
}
