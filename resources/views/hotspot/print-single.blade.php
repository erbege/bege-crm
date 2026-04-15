<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Cetak Voucher Hotspot</title>
    <style>
        body {
            font-family: monospace, sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }

        .voucher-card {
            width: 300px;
            background: #fff;
            border: 2px dashed #999;
            padding: 20px;
            margin: 0 auto;
            text-align: center;
        }

        .header {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .code {
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 2px;
            background: #eee;
            padding: 10px;
            margin: 15px 0;
        }

        .details {
            font-size: 14px;
            margin-bottom: 5px;
        }

        .footer {
            font-size: 12px;
            color: #666;
            margin-top: 15px;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .voucher-card {
                border: 1px solid #000;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="voucher-card">
        <div class="header">SKNET Hotspot</div>
        <div class="details">Paket: {{ $transaction->profile->name }}</div>
        <div class="details">Aktif: {{ $transaction->profile->validity_value }}
            {{ $transaction->profile->validity_unit }}</div>

        <div class="code">{{ $voucher->code }}</div>

        <div class="details">Terima Kasih</div>
        <div class="footer">
            Gunakan kode ini untuk login kembali ke jaringan. <br>
            Ref: {{ $transaction->reference_number }}
        </div>
    </div>
</body>

</html>