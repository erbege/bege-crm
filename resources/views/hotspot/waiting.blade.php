<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no, maximum-scale=1, user-scalable=0">
    <title>Menunggu Pembayaran - {{ \App\Models\Setting::get('general.company_name', 'SKNET') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f1f5f9;
        }

        .app-header {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            padding-bottom: 80px;
        }

        .main-container {
            margin-top: -60px;
            border-radius: 32px 32px 0 0;
            background: #ffffff;
            min-height: calc(100vh - 100px);
        }

        .pulse-premium {
            box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.4);
            animation: pulse-ring 2s infinite;
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.7);
            }

            70% {
                transform: scale(1);
                box-shadow: 0 0 0 15px rgba(37, 99, 235, 0);
            }

            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(37, 99, 235, 0);
            }
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

    <div x-data="pollingPayment('{{ route('hotspot.status', ['reference' => $transaction->reference_number]) }}')">
        <!-- App Header -->
        <header class="app-header relative overflow-hidden px-6 pt-8 pb-12">
            <div class="absolute inset-0 illustration-bg pointer-events-none"></div>

            <div class="relative z-10 flex justify-between items-center mb-6">
                <div class="flex items-center space-x-3">
                    @if($logo)
                        <img src="{{ asset('storage/' . $logo) }}" alt="{{ $companyName }}" class="h-8 w-auto">
                    @else
                        <div class="bg-white p-1.5 rounded-lg shadow-sm text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                    @endif
                    <span class="text-white font-bold text-lg tracking-tight">{{ $companyName }}</span>
                </div>
            </div>

            <div class="relative z-10 flex items-center space-x-2 text-white/90 text-sm font-medium">
                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                    </path>
                </svg>
                <span>Memproses Pembayaran...</span>
            </div>
        </header>

        <!-- Main Content Surface -->
        <main class="main-container relative z-20 px-6 pt-12 pb-10 shadow-2xl shadow-blue-900/10 text-center">

            <div class="mb-10">
                <div class="relative w-32 h-32 mx-auto mb-6" x-show="!isPaid">
                    <div class="absolute inset-0 bg-blue-100 rounded-full pulse-premium"></div>
                    <div
                        class="absolute inset-4 border-4 border-blue-600 border-t-transparent rounded-full animate-spin">
                    </div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                            </path>
                        </svg>
                    </div>
                </div>

                <div class="w-32 h-32 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner animate-bounce"
                    x-show="isPaid" style="display: none;">
                    <svg class="h-16 w-16 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>

                <div x-show="!isPaid">
                    <h2 class="text-2xl font-black text-slate-800 tracking-tight mb-2">Konfirmasi Pembayaran</h2>
                    <p class="text-slate-400 text-sm font-medium px-4 leading-relaxed">Kami sedang memeriksa status
                        pembayaran Anda secara otomatis. Mohon tunggu sejenak.</p>
                </div>

                <div x-show="isPaid" style="display: none;">
                    <h2 class="text-2xl font-black text-emerald-600 tracking-tight mb-2">Pembayaran Diterima!</h2>
                    <p class="text-slate-400 text-sm font-medium px-4">Mengalihkan Anda ke jaringan internet premium...
                    </p>
                </div>
            </div>

            <div
                class="bg-slate-50/80 backdrop-blur-sm rounded-[32px] p-6 border border-slate-100/50 text-left mb-10 shadow-inner">
                <div class="flex justify-between items-center mb-5 pb-5 border-b border-slate-200/50">
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-[0.2em]">Referensi</span>
                    <span
                        class="text-sm font-black text-slate-700 font-mono">{{ $transaction->reference_number }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-[0.2em]">Metode</span>
                    <span
                        class="text-sm font-black text-blue-600 uppercase tracking-tight">{{ $transaction->payment_method }}</span>
                </div>
                <div class="mt-5 pt-5 border-t border-slate-200/50 flex justify-between items-center">
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-[0.2em]">Total</span>
                    <span class="text-2xl font-black text-slate-800 tracking-tighter">Rp
                        {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <div
                class="flex items-center justify-center space-x-3 text-[11px] font-bold text-slate-400 uppercase tracking-widest italic group">
                <svg class="h-5 w-5 animate-pulse text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Halaman ini akan diperbarui otomatis</span>
            </div>

            <!-- Hidden form for Auto-Login to mikrotik -->
            <form id="auto-login-form" x-show="isPaid" style="display: none;"
                action="{{ $mikrotikParams['link-login'] ?? '#' }}" method="POST" class="mt-10">
                <input type="hidden" name="username" :value="voucherCode">
                <input type="hidden" name="password" :value="voucherPassword">
                <input type="hidden" name="dst"
                    value="{{ route('hotspot.success', ['reference' => $transaction->reference_number]) }}">

                <button type="submit"
                    class="w-full flex items-center justify-center h-16 rounded-2xl shadow-xl shadow-emerald-500/20 text-base font-black text-white bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 transition-all transform active:scale-[0.98] tracking-tight">
                    Lanjut Menghubungkan
                </button>
            </form>

            <footer class="mt-16">
                <p class="text-[10px] text-slate-300 font-bold uppercase tracking-[0.3em] mb-4">Powered by
                    {{ $companyName }} Hub
                </p>
            </footer>
        </main>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('pollingPayment', (statusUrl) => ({
                isPaid: false,
                voucherCode: '',
                voucherPassword: '',
                pollingInterval: null,

                init() {
                    this.pollingInterval = setInterval(() => {
                        this.checkStatus();
                    }, 3000);
                },

                destroy() {
                    if (this.pollingInterval) clearInterval(this.pollingInterval);
                },

                async checkStatus() {
                    try {
                        const response = await fetch(statusUrl);
                        if (!response.ok) return;

                        const data = await response.json();
                        if (data.status === 'paid' && data.voucher_code) {
                            clearInterval(this.pollingInterval);
                            this.isPaid = true;
                            this.voucherCode = data.voucher_code;
                            this.voucherPassword = data.voucher_password;

                            setTimeout(() => {
                                document.getElementById('auto-login-form').submit();
                            }, 2000);
                        }
                    } catch (error) {
                        console.error('Polling error', error);
                    }
                }
            }));
        });
    </script>
</body>

</html>