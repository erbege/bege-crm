<style>
    .voucher-container {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8mm;
        padding: 2mm;
    }

    .voucher {
        border: 1.5px solid #000000;
        border-radius: 8px;
        padding: 12px;
        background: #ffffff;
        page-break-inside: avoid;
        position: relative;
        min-height: 120px;
    }

    /* Header Section */
    .voucher-header {
        text-align: center;
        padding-bottom: 10px;
        margin-bottom: 10px;
        border-bottom: 1px solid #e0e0e0;
    }

    .voucher-brand {
        font-size: 16px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: #000000;
        margin-bottom: 4px;
    }

    .voucher-type {
        font-size: 11px;
        font-weight: 600;
        color: #333333;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    /* Credentials Section */
    .voucher-credentials {
        background: #f8f8f8;
        border: 1px dashed #cccccc;
        border-radius: 6px;
        padding: 10px;
        margin-bottom: 10px;
    }

    .credential-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
    }

    .credential-row:last-child {
        margin-bottom: 0;
    }

    .credential-label {
        font-size: 9px;
        font-weight: 600;
        color: #666666;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .credential-value {
        font-size: 13px;
        font-weight: 700;
        font-family: 'Courier New', monospace;
        color: #000000;
        letter-spacing: 1px;
    }

    /* Details Section */
    .voucher-details {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
        margin-bottom: 8px;
    }

    .detail-item {
        text-align: center;
    }

    .detail-label {
        font-size: 8px;
        font-weight: 600;
        color: #666666;
        text-transform: uppercase;
        letter-spacing: 0.2px;
        margin-bottom: 2px;
    }

    .detail-value {
        font-size: 11px;
        font-weight: 700;
        color: #000000;
    }

    /* Price Section */
    .voucher-price {
        text-align: center;
        padding: 8px;
        background: #000000;
        color: #ffffff;
        border-radius: 4px;
        margin-bottom: 8px;
    }

    .price-label {
        font-size: 8px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-bottom: 2px;
        opacity: 0.8;
    }

    .price-value {
        font-size: 16px;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    /* Footer Section */
    .voucher-footer {
        text-align: center;
        padding-top: 8px;
        border-top: 1px solid #e0e0e0;
    }

    .footer-text {
        font-size: 7px;
        color: #999999;
        line-height: 1.3;
    }

    /* QR Code Placeholder */
    .qr-placeholder {
        width: 50px;
        height: 50px;
        border: 1px solid #cccccc;
        border-radius: 4px;
        margin: 0 auto 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 8px;
        color: #999999;
        background: #f8f8f8;
    }

    /* Page Break */
    .page-break {
        page-break-after: always;
        height: 0;
        clear: both;
    }

    /* Print Optimizations */
    @media print {
        @page {
            size: A4;
            margin: 10mm;
        }
        
        body {
            background: #ffffff;
        }

        .voucher {
            box-shadow: none;
        }

        .page-break {
            display: block;
        }
    }

    /* Responsive adjustments for screen preview */
    @media screen {
        body {
            background: #f5f5f5;
            padding: 20px;
        }

        .voucher {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
    }
</style>

<div class="voucher-container">
    @foreach($vouchers as $voucher)
        <div class="voucher">
            <!-- Header -->
            <div class="voucher-header">
                @php
                    $logo = \App\Models\Setting::get('general.company_logo');
                @endphp
                @if($logo)
                    <img src="{{ asset('storage/' . $logo) }}" style="max-height: 25px; max-width: 120px; margin-bottom: 4px;">
                @else
                    <div class="voucher-brand">SKNET WiFi</div>
                @endif
                <div class="voucher-type">{{ $voucher->profile->name }}</div>
            </div>

            <!-- Credentials -->
            <div class="voucher-credentials">
                <div class="credential-row">
                    <span class="credential-label">Username</span>
                    <span class="credential-value">{{ $voucher->code }}</span>
                </div>
                @if($voucher->password)
                    <div class="credential-row">
                        <span class="credential-label">Password</span>
                        <span class="credential-value">{{ $voucher->password }}</span>
                    </div>
                @endif
            </div>

            <!-- Details -->
            <div class="voucher-details">
                <div class="detail-item">
                    <div class="detail-label">Validity</div>
                    <div class="detail-value">{{ $voucher->profile->validity_value }}
                        {{ ucfirst($voucher->profile->validity_unit) }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Speed</div>
                    <div class="detail-value">{{ $voucher->profile->rate_limit ?? 'Unlimited' }}</div>
                </div>
                @if($voucher->profile->data_limit)
                    <div class="detail-item">
                        <div class="detail-label">Data Limit</div>
                        <div class="detail-value">{{ $voucher->profile->data_limit }}
                            {{ strtoupper($voucher->profile->data_limit_unit ?? 'MB') }}</div>
                    </div>
                @endif
                @if($voucher->profile->time_limit)
                    <div class="detail-item">
                        <div class="detail-label">Time Limit</div>
                        <div class="detail-value">{{ $voucher->profile->time_limit }}
                            {{ ucfirst($voucher->profile->time_limit_unit ?? 'hours') }}</div>
                    </div>
                @endif
            </div>

            <!-- Price -->
            <div class="voucher-price">
                <div class="price-label">Harga</div>
                <div class="price-value">Rp {{ number_format($voucher->profile->price, 0, ',', '.') }}</div>
            </div>

            <!-- Footer -->
            <div class="voucher-footer">
                <div class="footer-text">
                    Hubungi customer service untuk bantuan<br>
                    Voucher ini berlaku sejak aktivasi pertama
                </div>
            </div>
        </div>

        {{-- Page break every 10 vouchers --}}
        @if($loop->iteration % 10 == 0 && !$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</div>