<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Models\Invoice;
use App\Models\HotspotVoucher;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\FinancialReportExport;
use Maatwebsite\Excel\Facades\Excel;

class FinancialReport extends Component
{
    public $startDate;
    public $endDate;
    public $reportType = 'all'; // all, subscription, hotspot
    public $nasFilter = ''; // Property untuk menyimpan pilihan filter NAS

    public function mount()
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    private function getData()
    {
        $subscriptionIncome = 0;
        $hotspotIncome = 0;
        $details = [];

        // 1. Subscription Data (Invoices)
        if ($this->reportType === 'all' || $this->reportType === 'subscription') {
            $query = Invoice::with('customer')
                ->where('status', 'paid')
                ->whereBetween('paid_at', [
                    Carbon::parse($this->startDate)->startOfDay(),
                    Carbon::parse($this->endDate)->endOfDay()
                ]);

            if (!empty($this->nasFilter)) {
                $query->whereHas('subscription', function ($q) {
                    $q->where('nas_id', $this->nasFilter);
                });
            }

            $invoices = $query->latest('paid_at')->get();

            $subscriptionIncome = $invoices->sum('total');

            foreach ($invoices as $inv) {
                $details[] = [
                    'date' => $inv->paid_at->format('Y-m-d H:i'),
                    'type' => 'Subscription',
                    'description' => 'Invoice #' . $inv->invoice_number . ' - ' . ($inv->customer->name ?? 'Unknown'),
                    'amount' => $inv->total,
                ];
            }
        }

        // 2. Hotspot Data (Vouchers)
        if ($this->reportType === 'all' || $this->reportType === 'hotspot') {
            $query = HotspotVoucher::with('profile')
                ->whereNotNull('used_at')
                ->whereBetween('used_at', [
                    Carbon::parse($this->startDate)->startOfDay(),
                    Carbon::parse($this->endDate)->endOfDay()
                ]);

            if (!empty($this->nasFilter)) {
                $query->where('nas_id', $this->nasFilter);
            }

            $vouchers = $query->latest('used_at')->get();

            // Calculate total based on profile price
            $hotspotIncome = $vouchers->sum(fn($v) => $v->profile->price ?? 0);

            foreach ($vouchers as $voucher) {
                $details[] = [
                    'date' => $voucher->used_at ? \Carbon\Carbon::parse($voucher->used_at)->format('Y-m-d H:i') : $voucher->created_at->format('Y-m-d H:i'),
                    'type' => 'Hotspot',
                    'description' => 'Voucher ' . $voucher->code . ' (' . ($voucher->profile->name ?? '?') . ')',
                    'amount' => $voucher->profile->price ?? 0,
                ];
            }
        }

        // Sort combined details by date desc
        usort($details, function ($a, $b) {
            return strcmp($b['date'], $a['date']);
        });

        return [
            'subscriptionIncome' => $subscriptionIncome,
            'hotspotIncome' => $hotspotIncome,
            'totalIncome' => $subscriptionIncome + $hotspotIncome,
            'transactions' => $details,
            // 'nases' => \App\Models\Nas::all() // Uncomment baris ini jika Model Nas sudah ada untuk dropdown
        ];
    }

    public function render()
    {
        return view('livewire.reports.financial-report', $this->getData())->layout('layouts.app');
    }

    public function exportPdf()
    {
        $subscriptionIncome = 0;
        $hotspotIncome = 0;
        $transactions = [];

        if ($this->reportType === 'all' || $this->reportType === 'subscription') {
            $query = Invoice::with('customer')
                ->where('status', 'paid')
                ->whereBetween('paid_at', [
                    Carbon::parse($this->startDate)->startOfDay(),
                    Carbon::parse($this->endDate)->endOfDay()
                ]);

            if (!empty($this->nasFilter)) {
                $query->whereHas('subscription', function ($q) {
                    $q->where('nas_id', $this->nasFilter);
                });
            }

            $invoices = $query->latest('paid_at')->get();
            $subscriptionIncome = $invoices->sum('total');

            foreach ($invoices as $inv) {
                $transactions[] = [
                    'date' => $inv->paid_at->format('d/m/Y H:i'),
                    'type' => 'Langganan',
                    'invoice' => $inv->invoice_number,
                    'customer' => $inv->customer->name ?? 'Unknown',
                    'description' => 'Invoice #' . $inv->invoice_number,
                    'method' => ucfirst($inv->payment_method ?? '-'),
                    'amount' => $inv->total,
                ];
            }
        }

        if ($this->reportType === 'all' || $this->reportType === 'hotspot') {
            $query = HotspotVoucher::with('profile')
                ->whereNotNull('used_at')
                ->whereBetween('used_at', [
                    Carbon::parse($this->startDate)->startOfDay(),
                    Carbon::parse($this->endDate)->endOfDay()
                ]);

            if (!empty($this->nasFilter)) {
                $query->where('nas_id', $this->nasFilter);
            }

            $vouchers = $query->latest('used_at')->get();
            $hotspotIncome = $vouchers->sum(fn($v) => $v->profile->price ?? 0);

            foreach ($vouchers as $voucher) {
                $transactions[] = [
                    'date' => $voucher->used_at ? Carbon::parse($voucher->used_at)->format('d/m/Y H:i') : $voucher->created_at->format('d/m/Y H:i'),
                    'type' => 'Hotspot',
                    'invoice' => '-',
                    'customer' => '-',
                    'description' => 'Voucher ' . $voucher->code . ' (' . ($voucher->profile->name ?? '?') . ')',
                    'method' => '-',
                    'amount' => $voucher->profile->price ?? 0,
                ];
            }
        }

        usort($transactions, fn($a, $b) => strcmp($b['date'], $a['date']));

        $data = [
            'subscriptionIncome' => $subscriptionIncome,
            'hotspotIncome' => $hotspotIncome,
            'totalIncome' => $subscriptionIncome + $hotspotIncome,
            'transactions' => $transactions,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'reportType' => $this->reportType,
        ];

        // Company info for kop surat
        $data['companyName'] = \App\Models\Setting::get('general.company_name', config('app.name', 'SKNET CRM'));
        $data['companyAddress'] = \App\Models\Setting::get('general.company_address', '');
        $data['companyPhone'] = \App\Models\Setting::get('general.company_phone', '');
        $data['companyEmail'] = \App\Models\Setting::get('general.company_email', '');

        $logoPath = \App\Models\Setting::get('general.company_logo', '');
        $data['companyLogo'] = $logoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($logoPath)
            ? \Illuminate\Support\Facades\Storage::disk('public')->path($logoPath)
            : '';

        $pdf = Pdf::loadView('reports.financial-pdf', $data)
            ->setPaper('a4', 'landscape');
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Laporan-Keuangan-' . date('Y-m-d') . '.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(
            new FinancialReportExport($this->startDate, $this->endDate, $this->reportType, $this->nasFilter),
            'Laporan-Keuangan-' . date('Y-m-d') . '.xlsx'
        );
    }
}
