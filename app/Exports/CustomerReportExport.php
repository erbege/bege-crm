<?php

namespace App\Exports;

use App\Models\Customer;
use App\Models\Setting;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CustomerReportExport implements WithEvents, WithTitle
{
    protected $startDate;
    protected $endDate;
    protected $statusFilter;

    public function __construct($startDate, $endDate, $statusFilter = 'all')
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->statusFilter = $statusFilter;
    }

    public function title(): string
    {
        return 'Laporan Pelanggan';
    }

    private function getData(): array
    {
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::active()->count();
        $suspendedCustomers = Customer::suspended()->count();
        $cancelledCustomers = Customer::terminated()->count();
        $pendingCustomers = Customer::pending()->count();
        $inactiveCustomers = Customer::inactive()->count();

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

        $query->whereBetween('created_at', [
            Carbon::parse($this->startDate)->startOfDay(),
            Carbon::parse($this->endDate)->endOfDay()
        ]);

        $customers = $query->with(['activeSubscription.package'])->latest()->get();

        return [
            'totalCustomers' => $totalCustomers,
            'activeCustomers' => $activeCustomers,
            'suspendedCustomers' => $suspendedCustomers,
            'cancelledCustomers' => $cancelledCustomers,
            'pendingCustomers' => $pendingCustomers,
            'inactiveCustomers' => $inactiveCustomers,
            'newCustomersCount' => $customers->count(),
            'customers' => $customers,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $data = $this->getData();
                $customers = $data['customers'];

                $companyName = Setting::get('general.company_name', config('app.name', 'SKNET CRM'));
                $companyAddress = Setting::get('general.company_address', '');
                $companyPhone = Setting::get('general.company_phone', '');
                $companyEmail = Setting::get('general.company_email', '');

                // ── Column widths ──
                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(14);
                $sheet->getColumnDimension('C')->setWidth(28);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(16);
                $sheet->getColumnDimension('F')->setWidth(22);
                $sheet->getColumnDimension('G')->setWidth(14);
                $sheet->getColumnDimension('H')->setWidth(15);
                $sheet->getColumnDimension('I')->setWidth(35);

                $lastCol = 'I';
                $row = 1;

                // ── Company Header ──
                $sheet->setCellValue("A{$row}", $companyName);
                $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1F2937']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);
                $row++;

                if ($companyAddress) {
                    $sheet->setCellValue("A{$row}", $companyAddress);
                    $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
                    $sheet->getStyle("A{$row}")->getFont()->setSize(10)->setColor(new Color('6B7280'));
                    $row++;
                }

                $contactLine = collect([$companyPhone, $companyEmail])->filter()->implode(' | ');
                if ($contactLine) {
                    $sheet->setCellValue("A{$row}", $contactLine);
                    $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
                    $sheet->getStyle("A{$row}")->getFont()->setSize(10)->setColor(new Color('6B7280'));
                    $row++;
                }

                // Separator line
                $row++;
                $sheet->getStyle("A{$row}:{$lastCol}{$row}")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM)->setColor(new Color('10B981'));
                $row++;

                // ── Report Title ──
                $sheet->setCellValue("A{$row}", 'LAPORAN PELANGGAN');
                $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '065F46']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $row++;

                $periodText = 'Periode Registrasi: ' . Carbon::parse($this->startDate)->isoFormat('D MMMM YYYY') . ' s/d ' . Carbon::parse($this->endDate)->isoFormat('D MMMM YYYY');
                $sheet->setCellValue("A{$row}", $periodText);
                $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font' => ['size' => 10, 'color' => ['rgb' => '6B7280']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $row++;

                if ($this->statusFilter !== 'all') {
                    $filterText = 'Filter Status: ' . ucfirst($this->statusFilter);
                    $sheet->setCellValue("A{$row}", $filterText);
                    $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
                    $sheet->getStyle("A{$row}")->applyFromArray([
                        'font' => ['size' => 10, 'color' => ['rgb' => '6B7280']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                    $row++;
                }

                $printDate = 'Dicetak: ' . now()->isoFormat('D MMMM YYYY [pukul] HH:mm');
                $sheet->setCellValue("A{$row}", $printDate);
                $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font' => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '9CA3AF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $row += 2;

                // ── Summary Section ──
                $sheet->setCellValue("A{$row}", 'RINGKASAN');
                $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1F2937']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
                    'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $row++;

                $summaryItems = [
                    ['Total Pelanggan', $data['totalCustomers']],
                    ['Pelanggan Aktif', $data['activeCustomers']],
                    ['Pelanggan Menunggu', $data['pendingCustomers']],
                    ['Pelanggan Terisolir', $data['suspendedCustomers']],
                    ['Pelanggan Dibatalkan', $data['cancelledCustomers']],
                    ['Pelanggan Baru (Periode)', $data['newCustomersCount']],
                ];

                foreach ($summaryItems as $item) {
                    $sheet->setCellValue("B{$row}", $item[0]);
                    $sheet->setCellValue("D{$row}", $item[1]);
                    $sheet->getStyle("B{$row}")->getFont()->setSize(10);
                    $sheet->getStyle("D{$row}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 10],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                    ]);
                    $row++;
                }
                $row++;

                // ── Data Table Header ──
                $headers = ['No', 'CID', 'Nama Pelanggan', 'Paket Layanan', 'Telepon', 'Email', 'Status', 'Tgl Terdaftar', 'Alamat'];
                $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];

                foreach ($headers as $i => $header) {
                    $sheet->setCellValue("{$cols[$i]}{$row}", $header);
                }

                $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '065F46']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '064E3B']]],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(28);
                $row++;

                // ── Data Rows ──
                $dataStartRow = $row;
                if ($customers->isEmpty()) {
                    $sheet->setCellValue("A{$row}", 'Tidak ada data pelanggan yang sesuai filter.');
                    $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
                    $sheet->getStyle("A{$row}")->applyFromArray([
                        'font' => ['italic' => true, 'color' => ['rgb' => '9CA3AF']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                    $row++;
                } else {
                    $statusColors = [
                        'active' => '059669', // Green
                        'suspended' => 'D97706', // Orange
                        'cancelled' => '991B1B', // Red
                        'pending' => 'CA8A04', // Yellow/Amber
                        'inactive' => '6B7280', // Gray
                    ];

                    foreach ($customers as $i => $customer) {
                        $sheet->setCellValue("A{$row}", $i + 1);
                        $sheet->setCellValue("B{$row}", $customer->customer_id);
                        $sheet->setCellValue("C{$row}", $customer->name);
                        $sheet->setCellValue("D{$row}", $customer->service_package_name ?? '-');
                        $sheet->setCellValue("E{$row}", $customer->phone ?? '-');
                        $sheet->setCellValue("F{$row}", $customer->email ?? '-');
                        $sheet->setCellValue("G{$row}", ucfirst($customer->status));
                        $sheet->setCellValue("H{$row}", $customer->created_at->format('d/m/Y'));
                        $sheet->setCellValue("I{$row}", $customer->address ?? '-');

                        // Alignment
                        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                        // Status color
                        $statusColor = $statusColors[$customer->status] ?? '6B7280';
                        $sheet->getStyle("G{$row}")->getFont()->setColor(new Color($statusColor))->setBold(true);

                        // Alternating row color
                        if ($i % 2 === 1) {
                            $sheet->getStyle("A{$row}:{$lastCol}{$row}")->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setRGB('F9FAFB');
                        }

                        $row++;
                    }
                }
                $dataEndRow = $row - 1;

                // Data table borders
                if ($customers->isNotEmpty()) {
                    $sheet->getStyle("A{$dataStartRow}:{$lastCol}{$dataEndRow}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
                        'font' => ['size' => 10],
                    ]);
                }

                // ── Total Row ──
                $row++;
                $sheet->setCellValue("A{$row}", "Total Data: {$customers->count()} pelanggan");
                $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '065F46']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $row += 3;

                // ── Signature Block ──
                $sheet->setCellValue("G{$row}", 'Mengetahui,');
                $sheet->setCellValue("I{$row}", 'Dibuat Oleh,');
                $sheet->getStyle("G{$row}:I{$row}")->getFont()->setSize(10);
                $row += 4;
                $sheet->setCellValue("G{$row}", '(.........................)');
                $sheet->setCellValue("I{$row}", '(.........................)');
                $sheet->getStyle("G{$row}:I{$row}")->getFont()->setSize(10);

                // ── Print Settings ──
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
                $sheet->getPageMargins()->setTop(0.5);
                $sheet->getPageMargins()->setBottom(0.5);
                $sheet->getPageMargins()->setLeft(0.5);
                $sheet->getPageMargins()->setRight(0.5);
            },
        ];
    }
}