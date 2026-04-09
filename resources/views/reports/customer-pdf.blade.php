<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Pelanggan</title>
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
            border-bottom: 3px solid #059669;
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
            color: #059669;
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
            border-left: 4px solid #059669;
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
            padding: 10px 12px;
            text-align: center;
        }

        .summary-card.blue {
            border-left: 4px solid #3b82f6;
        }

        .summary-card.green {
            border-left: 4px solid #10b981;
        }

        .summary-card.red {
            border-left: 4px solid #ef4444;
        }

        .summary-card.amber {
            border-left: 4px solid #f59e0b;
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
            font-size: 18px;
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
            border-bottom: 2px solid #059669;
            margin-bottom: 10px;
        }

        /* ── Data Table ── */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        table.data-table thead th {
            background-color: #059669;
            color: #ffffff;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 8px 5px;
            text-align: left;
            border: 1px solid #047857;
        }

        table.data-table tbody td {
            padding: 6px 5px;
            border: 1px solid #e5e7eb;
            font-size: 9px;
        }

        table.data-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
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

        /* ── Status Badge ── */
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-active {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-inactive {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .status-suspended {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-terminated {
            background-color: #fce7f3;
            color: #9d174d;
        }

        /* ── Total Row ── */
        .total-row {
            background-color: #ecfdf5 !important;
        }

        .total-row td {
            font-weight: bold;
            color: #059669;
            font-size: 10px;
            padding: 8px 6px;
            border: 1px solid #e5e7eb;
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
                <h2>Laporan Pelanggan</h2>
                <p>No. Dok: CUS/{{ now()->format('Y/m') }}</p>
                <p>Dicetak: {{ now()->isoFormat('D MMM YYYY HH:mm') }}</p>
            </td>
        </tr>
    </table>

    <!-- Meta Info -->
    <div class="meta-box">
        <table>
            <tr>
                <td class="label">Periode Registrasi</td>
                <td>: {{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMMM YYYY') }} s/d
                    {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMMM YYYY') }}
                </td>
            </tr>
            <tr>
                <td class="label">Filter Status</td>
                <td>: {{ $statusFilter === 'all' ? 'Semua Status' : ucfirst($statusFilter) }}</td>
            </tr>
            <tr>
                <td class="label">Jumlah Data</td>
                <td>: {{ $customers->count() }} pelanggan</td>
            </tr>
        </table>
    </div>

    <!-- Summary Section (Vertical Table to match Excel) -->
    <div class="section-title">Ringkasan</div>
    <table style="width: 50%; margin-bottom: 20px; border-collapse: collapse;">
        <tr>
            <td style="padding: 4px; border-bottom: 1px solid #e5e7eb;">Total Pelanggan (Keseluruhan)</td>
            <td
                style="padding: 4px; border-bottom: 1px solid #e5e7eb; text-align: left; font-weight: bold; padding-left: 20px;">
                {{ $totalCustomers }}</td>
        </tr>
        <tr>
            <td style="padding: 4px; border-bottom: 1px solid #e5e7eb;">Pelanggan Aktif</td>
            <td
                style="padding: 4px; border-bottom: 1px solid #e5e7eb; text-align: left; font-weight: bold; padding-left: 20px;">
                {{ $activeCustomers }}</td>
        </tr>
        <tr>
            <td style="padding: 4px; border-bottom: 1px solid #e5e7eb;">Pelanggan Non-Aktif</td>
            <td
                style="padding: 4px; border-bottom: 1px solid #e5e7eb; text-align: left; font-weight: bold; padding-left: 20px;">
                {{ $inactiveCustomers }}</td>
        </tr>
        <tr>
            <td style="padding: 4px; border-bottom: 1px solid #e5e7eb;">Pelanggan Baru (Periode Ini)</td>
            <td
                style="padding: 4px; border-bottom: 1px solid #e5e7eb; text-align: left; font-weight: bold; padding-left: 20px;">
                {{ $newCustomersCount }}</td>
        </tr>
    </table>

    <!-- Customer List -->
    <div class="section-title">Daftar Pelanggan</div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 3%;" class="text-center">No</th>
                <th style="width: 8%;">CID</th>
                <th style="width: 16%;">Nama Pelanggan</th>
                <th style="width: 12%;">Paket Layanan</th>
                <th style="width: 10%;">Telepon</th>
                <th style="width: 14%;">Email</th>
                <th style="width: 8%;" class="text-center">Status</th>
                <th style="width: 9%;">Tgl Terdaftar</th>
                <th style="width: 20%;">Alamat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $index => $customer)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $customer->customer_id }}</td>
                    <td class="text-bold">{{ $customer->name }}</td>
                    <td>{{ $customer->service_package_name ?? '-' }}</td>
                    <td>{{ $customer->phone ?? '-' }}</td>
                    <td>{{ $customer->email ?? '-' }}</td>
                    <td class="text-center">
                        <span class="status-badge status-{{ $customer->status }}">
                            {{ ucfirst($customer->status) }}
                        </span>
                    </td>
                    <td>{{ $customer->created_at->format('d/m/Y') }}</td>
                    <td>{{ $customer->address ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="empty-row">
                        Tidak ada data pelanggan yang sesuai filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($customers->count() > 0)
            <tfoot>
                <tr class="total-row">
                    <td colspan="9" style="text-align: center; font-weight: bold; color: white; background-color: #059669;">
                        Total Data: {{ $customers->count() }} pelanggan</td>
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
        Laporan Pelanggan &mdash; {{ $companyName }} | Halaman <span class="page-number"></span>
    </div>
</body>

</html>