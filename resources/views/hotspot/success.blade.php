<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no, maximum-scale=1, user-scalable=0">
    <title>Akses Internet Berhasil - {{ \App\Models\Setting::get('general.company_name', 'SKNET') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f1f5f9;
        }

        .app-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            padding-bottom: 80px;
        }

        .main-container {
            margin-top: -60px;
            border-radius: 32px 32px 0 0;
            background: #ffffff;
            min-height: calc(100vh - 100px);
        }

        .voucher-premium-card {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }

        .illustration-bg {
            background-image: url('{{ asset('assets/hotspot/premium_waves.png') }}');
            background-size: cover;
            background-position: center;
            opacity: 0.15;
        }
    </style>
</head>

<body class="antialiased">
    @php
        $logo = \App\Models\Setting::get('general.company_logo');
        $companyName = \App\Models\Setting::get('general.company_name', 'SKNET');
    @endphp

    <!-- App Header -->
    <header class="app-header relative overflow-hidden px-6 pt-8 pb-12">
        <div class="absolute inset-0 illustration-bg pointer-events-none"></div>

        <div class="relative z-10 flex justify-between items-center mb-6">
            <div class="flex items-center space-x-3">
                @if($logo)
                    <img src="{{ asset('storage/' . $logo) }}" alt="{{ $companyName }}" class="h-8 w-auto">
                @else
                    <div class="bg-white p-1.5 rounded-lg shadow-sm text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                @endif
                <span class="text-white font-bold text-lg tracking-tight">{{ $companyName }}</span>
            </div>
        </div>

        <div class="relative z-10 flex items-center space-x-2 text-white/90 text-sm font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>Transaksi Berhasil</span>
        </div>
    </header>

    <!-- Main Content Surface -->
    <main class="main-container relative z-20 px-6 pt-10 pb-10 shadow-2xl shadow-emerald-900/10 text-center">

        <div class="mb-8">
            <div class="w-32 h-32 mx-auto mb-4 relative drop-shadow-2xl">
                <img src="{{ asset('assets/hotspot/payment_success.png') }}" alt="Success"
                    class="w-full h-full object-contain">
            </div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight mb-2">Terkoneksi!</h2>
            <p class="text-slate-500 text-sm font-medium px-4">Selamat! Perangkat Anda kini sudah terhubung ke jaringan
                internet premium.</p>
        </div>

        <!-- Voucher Card -->
        <div class="voucher-premium-card rounded-[32px] p-8 shadow-2xl mb-10 relative overflow-hidden group">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/5 rounded-full blur-3xl"></div>
            <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl"></div>

            <p class="text-slate-400 text-[10px] font-bold mb-4 relative z-10 uppercase tracking-[0.3em]">Kode Voucher
                Anda</p>
            <div
                class="bg-white/10 backdrop-blur-xl border border-white/10 px-4 py-5 rounded-2xl text-center font-mono text-3xl font-black tracking-[0.4em] text-white shadow-inner mb-6 relative z-10 uppercase">
                {{ $transaction->voucher->code ?? 'N/A' }}
            </div>

            <div
                class="flex justify-between items-center text-[11px] text-slate-400 relative z-10 border-t border-white/5 pt-5">
                <div class="text-left">
                    <span class="block text-[9px] uppercase tracking-widest mb-1 opacity-50 font-bold">Paket</span>
                    <strong class="text-white text-sm tracking-tight">{{ $transaction->profile->name }}</strong>
                </div>
                <div class="text-right">
                    <span class="block text-[9px] uppercase tracking-widest mb-1 opacity-50 font-bold">Masa Aktif</span>
                    <strong class="text-white text-sm tracking-tight">{{ $transaction->profile->validity_value }}
                        {{ $transaction->profile->validity_unit }}</strong>
                </div>
            </div>
        </div>

        <div class="bg-orange-50 rounded-2xl p-5 mb-10 text-left border border-orange-100 flex items-start space-x-3">
            <div class="flex-shrink-0 mt-0.5">
                <svg class="h-5 w-5 text-orange-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            <p class="text-[11px] text-orange-800 font-bold leading-relaxed tracking-tight">
                Simpan kode voucher di atas. Gunakan kembali jika perangkat Anda terputus sebelum masa aktif habis.
            </p>
        </div>

        <div class="space-y-4">
            @php
                $targetUrl = $mikrotikParams['link-orig'] ?? 'https://google.com';
                if (empty($targetUrl))
                    $targetUrl = 'https://google.com';
            @endphp

            <a href="{{ $targetUrl }}" id="start-internet-btn"
                class="w-full flex items-center justify-center h-16 rounded-2xl shadow-xl shadow-blue-500/20 text-base font-black text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 transition-all transform active:scale-[0.98] tracking-tight uppercase">
                Mulai Berinternet
                <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3">
                    </path>
                </svg>
            </a>

            <a href="{{ route('hotspot.print', ['reference' => $transaction->reference_number]) }}"
                class="w-full flex items-center justify-center h-14 border border-slate-200 shadow-sm text-xs font-bold rounded-2xl text-slate-700 bg-white hover:bg-slate-50 transition-all transform active:scale-[0.98] uppercase tracking-widest">
                <svg class="mr-2 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Download PDF Voucher
            </a>
        </div>

        <footer class="mt-12">
            <p class="text-[10px] text-slate-300 font-bold uppercase tracking-[0.3em] mb-4">Powered by
                {{ $companyName }} Hub
            </p>
        </footer>

        <!-- Auto-Login Form (Hidden) -->
        <form x-ref="loginForm" action="{{ $mikrotikParams['link-login'] ?? '#' }}" method="POST" class="hidden">
            <input type="hidden" name="username" value="{{ $transaction->voucher->code ?? '' }}">
            <input type="hidden" name="password"
                value="{{ $transaction->voucher->password ?? ($transaction->voucher->code ?? '') }}">
            <input type="hidden" name="dst" value="{{ $targetUrl }}">
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const startBtn = document.getElementById('start-internet-btn');
                const form = document.querySelector('form[x-ref="loginForm"]');

                if (startBtn && form && form.getAttribute('action') !== '#') {
                    // Auto-login after 2 seconds
                    setTimeout(() => {
                        console.log('Auto-connecting to Mikrotik...');
                        form.submit();
                    }, 2000);

                    // Manual click handler
                    startBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        form.submit();
                    });
                }
            });
        </script>
    </main>
</body>

</html>