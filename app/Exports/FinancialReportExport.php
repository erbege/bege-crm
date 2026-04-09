<?php

namespace App\Exports;

use App\Models\Invoice;
use App\Models\HotspotVoucher;
use App\Models\Setting;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class FinancialReportExport implements WithEvents, WithTitle
{
    protected $startDate;
    protected $endDate;
    protected $reportType;
    protected $nasFilter;

    public function __construct($startDate, $endDate, $reportType = 'all', $nasFilter = '')
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->reportType = $reportType;
        $this->nasFilter = $nasFilter;
    }

    public function title(): string
    {
        return 'Laporan Keuangan';
    }

    private function getData(): array
    {
        $subscriptionIncome = 0;
        $hotspotIncome = 0;
        $details = [];

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
                $details[] = [
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

        usort($details, fn($a, $b) => strcmp($b['date'], $a['date']));

        return [
            'subscriptionIncome' => $subscriptionIncome,
            'hotspotIncome' => $hotspotIncome,
            'totalIncome' => $subscriptionIncome + $hotspotIncome,
            'details' => $details,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $data = $this->getData();
                $details = $data['details'];

                $companyName = Setting::get('general.company_name', config('app.name', 'SKNET CRM'));
                $companyAddress = Setting::get('general.company_address', '');
                $companyPhone = Setting::get('general.company_phone', '');
                $companyEmail = Setting::get('general.company_email', '');

                // ── Column widths ──
                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(18);
                $sheet->getColumnDimension('C')->setWidth(14);
                $sheet->getColumnDimension('D')->setWidth(18);
                $sheet->getColumnDimension('E')->setWidth(24);
                $sheet->getColumnDimension('F')->setWidth(35);
                $sheet->getColumnDimension('G')->setWidth(14);
                $sheet->getColumnDimension('H')->setWidth(20);

                $lastCol = 'H';
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
                $sheet->getStyle("A{$row}:{$lastCol}{$row}")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM)->setColor(new Color('3B82F6'));
                $row++;

                // ── Report Title ──
                $sheet->setCellValue("A{$row}", 'LAPORAN KEUANGAN');
                $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1E40AF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $row++;

                $periodText = 'Periode: ' . Carbon::parse($this->startDate)->isoFormat('D MMMM YYYY') . ' s/d ' . Carbon::parse($this->endDate)->isoFormat('D MMMM YYYY');
                $sheet->setCellValue("A{$row}", $periodText);
                $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font' => ['size' => 10, 'color' => ['rgb' => '6B7280']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $row++;

                $printDate = 'Dicetak: ' . now()->isoFormat('D MMMM YYYY [pukul] HH:mm');
                $sheet->setCellValue("A{$row}", $printDate);
                $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font' => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '9CA3AF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $row += 2;

                // ── Summary Section ──
                $summaryStartRow = $row;
                $sheet->setCellValue("A{$row}", 'RINGKASAN PENDAPATAN');
                $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1F2937']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
                    'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $row++;

                // Summary items
                $summaryItems = [];
                if ($this->reportType === 'all' || $this->reportType === 'subscription') {
                    $summaryItems[] = ['Pendapatan Langganan', $data['subscriptionIncome']];
                }
                if ($this->reportType === 'all' || $this->reportType === 'hotspot') {
                    $summaryItems[] = ['Pendapatan Hotspot', $data['hotspotIncome']];
                }

                foreach ($summaryItems as $item) {
                    $sheet->setCellValue("B{$row}", $item[0]);
                    $sheet->setCellValue("H{$row}", $item[1]);
                    $sheet->getStyle("B{$row}")->getFont()->setSize(10);
                    $sheet->getStyle("H{$row}")->applyFromArray([
                        'font' => ['size' => 10],
                        'numberFormat' => ['formatCode' => '#,##0'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ]);
                    $row++;
                }

                // Total
                $sheet->setCellValue("B{$row}", 'TOTAL PENDAPATAN');
                $sheet->setCellValue("H{$row}", $data['totalIncome']);
                $sheet->getStyle("B{$row}:H{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '059669']],
                    'borders' => ['top' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '059669']]],
                ]);
                $sheet->getStyle("H{$row}")->applyFromArray([
                    'numberFormat' => ['formatCode' => '#,##0'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);
                $row += 2;

                // ── Data Table Header ──
                $tableHeaderRow = $row;
                $headers = ['No', 'Tanggal', 'Tipe', 'No. Invoice', 'Pelanggan', 'Keterangan', 'Metode', 'Jumlah (Rp)'];
                $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];

                foreach ($headers as $i => $header) {
                    $sheet->setCellValue("{$cols[$i]}{$row}", $header);
                }

                $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1E3A8A']]],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(28);
                $row++;

                // ── Data Rows ──
                $dataStartRow = $row;
                if (empty($details)) {
                    $sheet->setCellValue("A{$row}", 'Tidak ada data transaksi pada periode ini.');
                    $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
                    $sheet->getStyle("A{$row}")->applyFromArray([
                        'font' => ['italic' => true, 'color' => ['rgb' => '9CA3AF']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                    $row++;
                } else {
                    foreach ($details as $i => $item) {
                        $sheet->setCellValue("A{$row}", $i + 1);
                        $sheet->setCellValue("B{$row}", $item['date']);
                        $sheet->setCellValue("C{$row}", $item['type']);
                        $sheet->setCellValue("D{$row}", $item['invoice']);
                        $sheet->setCellValue("E{$row}", $item['customer']);
                        $sheet->setCellValue("F{$row}", $item['description']);
                        $sheet->setCellValue("G{$row}", $item['method']);
                        $sheet->setCellValue("H{$row}", $item['amount']);

                        // Alignment
                        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("H{$row}")->applyFromArray([
                            'numberFormat' => ['formatCode' => '#,##0'],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                        ]);

                        // Alternating row color
                        if ($i % 2 === 1) {
                            $sheet->getStyle("A{$row}:{$lastCol}{$row}")->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setRGB('F9FAFB');
                        }

                        // Type badge color
                        $typeColor = $item['type'] === 'Langganan' ? '1D4ED8' : 'D97706';
                        $sheet->getStyle("C{$row}")->getFont()->setColor(new Color($typeColor))->setBold(true);

                        $row++;
                    }
                }
                $dataEndRow = $row - 1;

                // Data table borders
                if (!empty($details)) {
                    $sheet->getStyle("A{$dataStartRow}:{$lastCol}{$dataEndRow}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
                        'font' => ['size' => 10],
                    ]);
                }

                // ── Grand Total Row ──
                $row++;
                $sheet->setCellValue("F{$row}", 'GRAND TOTAL');
                $sheet->setCellValue("H{$row}", $data['totalIncome']);
                $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("H{$row}")->applyFromArray([
                    'numberFormat' => ['formatCode' => '#,##0'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);
                $row += 3;

                // ── Signature Block ──
                $sheet->setCellValue("F{$row}", 'Mengetahui,');
                $sheet->setCellValue("H{$row}", 'Dibuat Oleh,');
                $sheet->getStyle("F{$row}:H{$row}")->getFont()->setSize(10);
                $row += 4;
                $sheet->setCellValue("F{$row}", '(.........................)');
                $sheet->setCellValue("H{$row}", '(.........................)');
                $sheet->getStyle("F{$row}:H{$row}")->getFont()->setSize(10);

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