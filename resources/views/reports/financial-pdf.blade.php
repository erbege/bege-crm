<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan</title>
    <style>
        @page {
            margin: 1.5cm 2cm;
        }

        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-size: 10px;
            color: #1f2937;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        /* ── Kop Surat ── */
        .kop-surat {
            width: 100%;
            border-bottom: 3px solid #1e40af;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .kop-surat td {
            vertical-align: top;
            border: none;
            padding: 0;
        }

        .kop-logo {
            width: 60px;
            padding-right: 12px;
        }

        .kop-logo img {
            width: 55px;
            height: 55px;
            object-fit: contain;
        }

        .kop-company {
            text-align: left;
        }

        .kop-company h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            letter-spacing: 0.5px;
        }

        .kop-company p {
            margin: 2px 0 0;
            font-size: 9px;
            color: #6b7280;
        }

        .kop-report {
            text-align: right;
        }

        .kop-report h2 {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
            color: #1e40af;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .kop-report p {
            margin: 2px 0 0;
            font-size: 9px;
            color: #6b7280;
        }

        /* ── Meta Info ── */
        .meta-box {
            background-color: #f8fafc;
            border-left: 4px solid #1e40af;
            padding: 10px 14px;
            margin-bottom: 20px;
        }

        .meta-box table {
            width: 100%;
            border: none;
        }

        .meta-box td {
            padding: 3px 8px;
            border: none;
            font-size: 10px;
        }

        .meta-box .label {
            font-weight: bold;
            color: #374151;
            width: 130px;
        }

        /* ── Summary Cards ── */
        .summary-table {
            width: 100%;
            margin-bottom: 20px;
            border: none;
        }

        .summary-table td {
            border: none;
            padding: 4px;
            vertical-align: top;
        }

        .summary-card {
            background-color: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px 14px;
            text-align: center;
        }

        .summary-card.blue {
            border-left: 4px solid #3b82f6;
        }

        .summary-card.amber {
            border-left: 4px solid #f59e0b;
        }

        .summary-card.green {
            border-left: 4px solid #10b981;
        }

        .summary-card .label {
            display: block;
            font-size: 8px;
            font-weight: bold;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .summary-card .value {
            display: block;
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
        }

        /* ── Section Title ── */
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #1f2937;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding-bottom: 6px;
            border-bottom: 2px solid #1e40af;
            margin-bottom: 10px;
        }

        /* ── Data Table ── */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        table.data-table thead th {
            background-color: #1e40af;
            color: #ffffff;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 8px 6px;
            text-align: left;
            border: 1px solid #1e3a8a;
        }

        table.data-table tbody td {
            padding: 7px 6px;
            border: 1px solid #e5e7eb;
            font-size: 9px;
        }

        table.data-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        table.data-table tfoot td {
            padding: 8px 6px;
            font-weight: bold;
            border: 1px solid #e5e7eb;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: bold;
        }

        .type-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .type-langganan {
            background-color: #dbeafe;
            color: #1d4ed8;
        }

        .type-hotspot {
            background-color: #fef3c7;
            color: #d97706;
        }

        .grand-total-row {
            background-color: #ecfdf5 !important;
        }

        .grand-total-row td {
            color: #059669;
            font-size: 11px;
        }

        /* ── Signature Block ── */
        .signature-block {
            width: 100%;
            margin-top: 40px;
            border: none;
        }

        .signature-block td {
            border: none;
            text-align: center;
            vertical-align: top;
            padding: 0 20px;
            font-size: 10px;
        }

        .signature-block .title {
            color: #6b7280;
            margin-bottom: 60px;
        }

        .signature-block .line {
            border-top: 1px solid #1f2937;
            display: inline-block;
            width: 140px;
            padding-top: 4px;
        }

        /* ── Footer ── */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 6px;
        }

        .page-number:after {
            content: counter(page);
        }

        .empty-row {
            text-align: center;
            color: #ef4444;
            font-style: italic;
            background-color: #fef2f2 !important;
        }
    </style>
</head>

<body>
    <!-- Kop Surat -->
    <table class="kop-surat">
        <tr>
            @if(!empty($companyLogo))
                <td class="kop-logo">
                    <img src="{{ $companyLogo }}" alt="Logo">
                </td>
            @endif
            <td class="kop-company">
                <h1>{{ $companyName }}</h1>
                @if(!empty($companyAddress))
                    <p>{{ $companyAddress }}</p>
                @endif
                @php
                    $contact = collect([$companyPhone ?? null, $companyEmail ?? null])->filter()->implode(' | ');
                @endphp
                @if($contact)
                    <p>{{ $contact }}</p>
                @endif
            </td>
            <td class="kop-report">
                <h2>Laporan Keuangan</h2>
                <p>No. Dok: FIN/{{ now()->format('Y/m') }}</p>
                <p>Dicetak: {{ now()->isoFormat('D MMM YYYY HH:mm') }}</p>
            </td>
        </tr>
    </table>

    <!-- Meta Info -->
    <div class="meta-box">
        <table>
            <tr>
                <td class="label">Periode Laporan</td>
                <td>: {{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMMM YYYY') }} s/d
                    {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMMM YYYY') }}
                </td>
            </tr>
            <tr>
                <td class="label">Tipe Laporan</td>
                <td>: {{ $reportType === 'all' ? 'Semua (Langganan + Hotspot)' : ucfirst($reportType) }}</td>
            </tr>
            <tr>
                <td class="label">Jumlah Transaksi</td>
                <td>: {{ count($transactions) }} transaksi</td>
            </tr>
        </table>
    </div>

    <!-- Summary Section (Vertical Table to match Excel) -->
    <div class="section-title">Ringkasan Pendapatan</div>
    <table style="width: 50%; margin-bottom: 20px; border-collapse: collapse;">
        @if($reportType === 'all' || $reportType === 'subscription')
            <tr>
                <td style="padding: 4px; border-bottom: 1px solid #e5e7eb;">Pendapatan Langganan</td>
                <td style="padding: 4px; border-bottom: 1px solid #e5e7eb; text-align: right; font-weight: bold;">Rp
                    {{ number_format($subscriptionIncome, 0, ',', '.') }}
                </td>
            </tr>
        @endif

        @if($reportType === 'all' || $reportType === 'hotspot')
            <tr>
                <td style="padding: 4px; border-bottom: 1px solid #e5e7eb;">Pendapatan Hotspot</td>
                <td style="padding: 4px; border-bottom: 1px solid #e5e7eb; text-align: right; font-weight: bold;">Rp
                    {{ number_format($hotspotIncome, 0, ',', '.') }}
                </td>
            </tr>
        @endif

        <tr style="background-color: #ecfdf5;">
            <td style="padding: 6px 4px; border-top: 2px solid #059669; color: #059669; font-weight: bold;">TOTAL
                PENDAPATAN</td>
            <td
                style="padding: 6px 4px; border-top: 2px solid #059669; text-align: right; color: #059669; font-weight: bold;">
                Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
        </tr>
    </table>

    <!-- Transaction Details -->
    <div class="section-title">Rincian Transaksi</div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 3%;" class="text-center">No</th>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 8%;" class="text-center">Tipe</th>
                <th style="width: 12%;">No. Invoice</th>
                <th style="width: 15%;">Pelanggan</th>
                <th style="width: 22%;">Keterangan</th>
                <th style="width: 10%;" class="text-center">Metode</th>
                <th style="width: 13%;" class="text-right">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item['date'] }}</td>
                    <td class="text-center">
                        <span class="type-badge {{ $item['type'] == 'Langganan' ? 'type-langganan' : 'type-hotspot' }}">
                            {{ $item['type'] }}
                        </span>
                    </td>
                    <td>{{ $item['invoice'] }}</td>
                    <td>{{ $item['customer'] }}</td>
                    <td>{{ $item['description'] }}</td>
                    <td class="text-center">{{ $item['method'] }}</td>
                    <td class="text-right">{{ number_format($item['amount'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="empty-row">
                        Tidak ada data transaksi pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if(count($transactions) > 0)
            <tfoot>
                <tr class="grand-total-row">
                    <td colspan="5" style="border: none; background-color: white;"></td>
                    <td class="text-right"
                        style="border: 1px solid #e5e7eb; font-weight: bold; background-color: #ecfdf5; color: #059669;">
                        GRAND TOTAL</td>
                    <td style="border: 1px solid #e5e7eb; background-color: #ecfdf5;"></td>
                    <td class="text-right"
                        style="border: 1px solid #e5e7eb; font-weight: bold; background-color: #ecfdf5; color: #059669;">Rp
                        {{ number_format($totalIncome, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <!-- Signature Block -->
    <table class="signature-block">
        <tr>
            <td style="width: 60%">&nbsp;</td>
            <td style="width: 20%">
                <div class="title">Mengetahui,</div>
                <div class="line">&nbsp;</div>
            </td>
            <td style="width: 20%">
                <div class="title">Dibuat Oleh,</div>
                <div class="line">&nbsp;</div>
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="footer">
        Laporan Keuangan &mdash; {{ $companyName }} | Halaman <span class="page-number"></span>
    </div>
</body>

</html>