<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Print Vouchers - {{ config('app.name', 'SKNET') }}</title>

    <!-- Styles -->
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                margin: 0;
                padding: 0;
                background: white;
            }
        }

        /* Ensure pages break correctly */
        .page-break {
            page-break-after: always;
        }

        /* Basic Reset for Print */
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    </style>

    {{ $styles ?? '' }}
</head>

<body onload="window.print()">
    {!! $slot !!}

    <script>
        // Optional: Close window after print dialog is closed (with delay)
        // window.onafterprint = function() { window.close(); };
    </script>
</body>

</html>