<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            color: #333;
            margin: 0;
            padding: 0;
            line-height: 1.4;
        }
        .header-bar {
            background-color: #2563eb; /* Blue-600 */
            height: 8px;
            width: 100%;
        }
        .container {
            padding: 30px 40px;
        }
        
        /* Header Section */
        .header-table {
            width: 100%;
            margin-bottom: 30px;
        }
        .header-table td {
            vertical-align: top;
        }
        .logo-img {
            max-height: 60px;
            max-width: 200px;
            margin-bottom: 10px;
        }
        .company-name {
            font-size: 16pt;
            font-weight: bold;
            color: #1e40af; /* Blue-800 */
            margin: 0;
        }
        .company-info {
            font-size: 9pt;
            color: #555;
        }
        .invoice-title {
            font-size: 24pt;
            font-weight: bold;
            color: #2563eb;
            text-transform: uppercase;
            text-align: right;
            margin: 0;
        }
        .invoice-details {
            text-align: right;
            margin-top: 10px;
            font-size: 10pt;
        }
        
        /* Status Badges */
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            color: white;
            margin-top: 5px;
        }
        .badge-unpaid { background-color: #d97706; } /* Amber-600 */
        .badge-paid { background-color: #059669; } /* Emerald-600 */
        .badge-cancelled { background-color: #dc2626; } /* Red-600 */
        .badge-partial { background-color: #ca8a04; } /* Yellow-600 */

        /* Client Info */
        .client-section {
            margin-bottom: 30px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 20px;
        }
        .section-label {
            font-size: 9pt;
            color: #6b7280; /* Gray-500 */
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .client-name {
            font-size: 12pt;
            font-weight: bold;
            color: #111;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #f3f4f6; /* Gray-100 */
            color: #374151; /* Gray-700 */
            text-align: left;
            padding: 10px;
            font-size: 9pt;
            text-transform: uppercase;
            border-bottom: 2px solid #e5e7eb;
        }
        .items-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: top;
        }
        .items-table tr:last-child td {
            border-bottom: none;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }

        /* Totals */
        .totals-container {
            width: 100%;
            margin-bottom: 30px;
        }
        .totals-table {
            width: 40%;
            float: right;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 6px 0;
            text-align: right;
        }
        .totals-table .label {
            color: #6b7280;
            padding-right: 15px;
        }
        .total-row td {
            border-top: 2px solid #2563eb;
            padding-top: 10px;
            font-size: 12pt;
            font-weight: bold;
            color: #2563eb;
        }
        .discount-text { color: #dc2626; }

        /* Payment Info */
        .payment-section {
            clear: both;
            background-color: #f9fafb; /* Gray-50 */
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 15px;
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .payment-title {
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
            font-size: 10pt;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }
        .bank-grid {
            width: 100%;
        }
        .bank-item {
            margin-bottom: 5px;
        }
        .bank-name { font-weight: bold; color: #4b5563; }
        
        /* Footer */
        .footer {
            position: fixed;
            bottom: 0px;
            left: 0px;
            right: 0px;
            height: 40px;
            text-align: center;
            font-size: 8pt;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
            margin: 0 40px;
            background-color: white; /* Ensure background covers content */
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80pt;
            color: rgba(220, 38, 38, 0.1); /* Red with low opacity */
            font-weight: bold;
            z-index: -1000;
            text-transform: uppercase;
        }

        /* Helpers */
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        .sub-text { font-size: 8pt; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header-bar"></div>

    <div class="container">
        <!-- Watermark for specific statuses -->
        @if($invoice->status === 'paid')
            <div class="watermark" style="color: rgba(5, 150, 105, 0.1);">LUNAS</div>
        @elseif($invoice->status === 'cancelled')
            <div class="watermark">BATAL</div>
        @endif

        <!-- Header -->
        <table class="header-table">
            <tr>
                <td style="width: 55%;">
                    @if(!empty($company['logo']))
                        <img src="{{ public_path('storage/' . $company['logo']) }}" class="logo-img" alt="Logo">
                    @else
                        <h1 class="company-name">{{ $company['name'] }}</h1>
                    @endif
                    
                    <div class="company-info">
                        @if($company['address']){{ $company['address'] }}<br>@endif
                        @if($company['phone'])Telp: {{ $company['phone'] }}<br>@endif
                        @if($company['email'])Email: {{ $company['email'] }}@endif
                    </div>
                </td>
                <td style="width: 45%; vertical-align: top;">
                    <h1 class="invoice-title">INVOICE</h1>
                    <div class="invoice-details">
                        <table style="width: 100%; text-align: right;">
                            <tr>
                                <td style="padding-bottom: 3px; color: #6b7280;">Nomor Invoice :</td>
                                <td style="font-weight: bold;">{{ $invoice->invoice_number }}</td>
                            </tr>
                            <tr>
                                <td style="padding-bottom: 3px; color: #6b7280;">Tanggal Terbit :</td>
                                <td>{{ $invoice->issue_date->isoFormat('D MMMM Y') }}</td>
                            </tr>
                            <tr>
                                <td style="padding-bottom: 3px; color: #6b7280;">Jatuh Tempo :</td>
                                <td>{{ $invoice->due_date->isoFormat('D MMMM Y') }}</td>
                            </tr>
                        </table>
                        
                        @php
                            $badgeClass = match($invoice->status) {
                                'paid' => 'badge-paid',
                                'cancelled' => 'badge-cancelled',
                                'partial' => 'badge-partial',
                                default => 'badge-unpaid'
                            };
                        @endphp
                        <div style="margin-top: 10px;">
                            <span class="badge {{ $badgeClass }}">{{ $invoice->status_label }}</span>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Bill To -->
        <div class="client-section">
            <div class="section-label">DITAGIHKAN KEPADA:</div>
            <div class="client-name">{{ $invoice->customer->name }}</div>
            <div style="font-size: 10pt; color: #4b5563;">
                ID: {{ $invoice->customer->customer_id }}<br>
                {{ $invoice->customer->full_address ?? '-' }}<br>
                {{ $invoice->customer->phone ?? '' }}
            </div>
        </div>

        <!-- Items -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Deskripsi</th>
                    <th style="width: 25%;">Periode</th>
                    <th style="width: 25%;" class="text-right">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <span class="font-bold">{{ $invoice->subscription->package->name ?? 'Layanan Internet' }}</span>
                        @if($invoice->subscription->package && $invoice->subscription->package->bwProfile)
                            <br><span class="sub-text">{{ $invoice->subscription->package->bwProfile->name }}</span>
                        @endif
                        
                        @php
                            $packagePrice = $invoice->subscription->package->price ?? 0;
                            $isProrated = abs($invoice->subtotal - $packagePrice) > 1;
                        @endphp
                        
                        @if($isProrated && $invoice->subtotal < $packagePrice)
                            <br><span style="color: #ea580c; font-size: 8pt; font-style: italic;">(Prorata / Penyesuaian)</span>
                        @endif
                    </td>
                    <td>{{ $invoice->subscription->period_label ?? '-' }}</td>
                    <td class="text-right">{{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                </tr>
                @if($invoice->installation_fee > 0)
                <tr>
                    <td>
                        <span class="font-bold">Biaya Instalasi</span>
                    </td>
                    <td>-</td>
                    <td class="text-right">{{ number_format($invoice->installation_fee, 0, ',', '.') }}</td>
                </tr>
                @endif
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-container clearfix">
            <table class="totals-table">
                <tr>
                    <td class="label">Subtotal</td>
                    <td>Rp {{ number_format($invoice->subtotal + $invoice->installation_fee, 0, ',', '.') }}</td>
                </tr>
                @if($invoice->tax > 0)
                <tr>
                    <td class="label">Pajak (PPN)</td>
                    <td>Rp {{ number_format($invoice->tax, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($invoice->discount > 0)
                <tr>
                    <td class="label">Diskon</td>
                    <td class="discount-text">- Rp {{ number_format($invoice->discount, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td class="label" style="color: #2563eb;">TOTAL</td>
                    <td>Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <!-- Payment Info -->
        @if($invoice->status !== 'paid' && $banks->count() > 0)
        <div class="payment-section">
            <div class="payment-title">METODE PEMBAYARAN</div>
            <p style="font-size: 9pt; margin-bottom: 10px; margin-top: 0;">Silakan lakukan pembayaran melalui rekening berikut:</p>
            
            <table class="bank-grid">
                @foreach($banks->chunk(2) as $chunk)
                <tr>
                    @foreach($chunk as $bank)
                    <td style="width: 50%; padding-bottom: 10px; vertical-align: top;">
                        <div class="bank-name">{{ $bank->bank_name }}</div>
                        <div style="font-family: monospace; font-size: 11pt; color: #111;">{{ $bank->account_number }}</div>
                        <div style="font-size: 9pt;">a.n. {{ $bank->account_holder }}</div>
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </table>
            
            <div style="margin-top: 10px; font-size: 8pt; color: #6b7280; font-style: italic;">
                * Harap cantumkan <strong>{{ $invoice->invoice_number }}</strong> pada berita transfer untuk mempercepat verifikasi.
            </div>
        </div>
        @endif

        @if($invoice->status === 'paid' && $invoice->paid_at)
        <div class="payment-section" style="background-color: #ecfdf5; border-color: #10b981;">
            <div class="payment-title" style="color: #047857; margin-bottom: 5px;">PEMBAYARAN DITERIMA</div>
            <table style="width: 100%; font-size: 9pt;">
                <tr>
                    <td style="width: 100px;">Tanggal</td>
                    <td>: {{ $invoice->paid_at->isoFormat('D MMMM Y, HH:mm') }}</td>
                </tr>
                @if($invoice->payment_method)
                <tr>
                    <td>Metode</td>
                    <td>: {{ ucfirst($invoice->payment_method) }}</td>
                </tr>
                @endif
            </table>
        </div>
        @endif
    </div>

    <div class="footer">
        Dicetak otomatis oleh Sistem pada {{ now()->isoFormat('D MMMM Y, HH:mm') }}. Dokumen ini sah tanpa tanda tangan basah.<br>
        Terima kasih telah berlangganan layanan {{ $company['name'] }}.
    </div>
</body>
</html>