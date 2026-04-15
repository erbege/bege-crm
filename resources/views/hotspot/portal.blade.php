<!DOCTYPE html>
<html lang="id" class="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no, maximum-scale=1, user-scalable=0">
    <title>{{ \App\Models\Setting::get('general.company_name', 'SKNET') }} - Portal</title>

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
            background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%);
            padding-bottom: 80px;
        }

        .main-container {
            margin-top: -60px;
            border-radius: 32px 32px 0 0;
            background: #ffffff;
            min-height: calc(100vh - 100px);
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .package-card {
            min-width: 220px;
            scroll-snap-align: start;
        }

        .login-method-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }

        .login-method-active .login-method-icon {
            background: #eff6ff;
            border-color: #3b82f6;
            color: #2563eb;
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
        $linkLogin = $mikrotikParams['link-login'] ?? route('hotspot.portal');
        $linkOrig = $mikrotikParams['link-orig'] ?? 'http://google.com';

        // Professional color themes for package cards
        $cardThemes = [
            ['bg' => 'bg-blue-50/50', 'border' => 'border-blue-100', 'text' => 'text-blue-600', 'iconBg' => 'bg-blue-600', 'hover' => 'hover:border-blue-300', 'active' => 'border-blue-500 bg-blue-50/30 ring-4 ring-blue-500/5'],
            ['bg' => 'bg-emerald-50/50', 'border' => 'border-emerald-100', 'text' => 'text-emerald-600', 'iconBg' => 'bg-emerald-600', 'hover' => 'hover:border-emerald-300', 'active' => 'border-emerald-500 bg-emerald-50/30 ring-4 ring-emerald-500/5'],
            ['bg' => 'bg-amber-50/50', 'border' => 'border-amber-100', 'text' => 'text-amber-600', 'iconBg' => 'bg-amber-600', 'hover' => 'hover:border-amber-300', 'active' => 'border-amber-500 bg-amber-50/30 ring-4 ring-amber-500/5'],
            ['bg' => 'bg-violet-50/50', 'border' => 'border-violet-100', 'text' => 'text-violet-600', 'iconBg' => 'bg-violet-600', 'hover' => 'hover:border-violet-300', 'active' => 'border-violet-500 bg-violet-50/30 ring-4 ring-violet-500/5'],
        ];
    @endphp

    <div x-data="{ tab: 'beli', selectedPackage: null }">
        <!-- App Header -->
        <header class="app-header relative overflow-hidden px-6 pt-8 pb-12">
            <div class="absolute inset-0 illustration-bg pointer-events-none"></div>

            <div class="relative z-10 flex justify-between items-center mb-6">
                <div class="flex items-center space-x-3">
                    @if($logo)
                        <img src="{{ asset('storage/' . $logo) }}" alt="{{ $companyName }}" class="h-8 w-auto">
                    @else
                        <div class="bg-white p-1.5 rounded-lg shadow-sm">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0">
                                </path>
                            </svg>
                        </div>
                    @endif
                    <span class="text-white font-bold text-lg tracking-tight">{{ $companyName }}</span>
                </div>
                <button @click="Swal.fire({
                    title: 'Butuh Bantuan?',
                    text: 'Silakan hubungi admin di nomor 08XXXXXXXXXX atau kunjungi kantor layanan terdekat.',
                    icon: 'question',
                    confirmButtonColor: '#3b82f6',
                    confirmButtonText: 'Hubungi WhatsApp'
                })"
                    class="w-10 h-10 flex items-center justify-center bg-white/20 backdrop-blur-md rounded-xl text-white hover:bg-white/30 transition-all border border-white/10 active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </button>
            </div>

            <div class="relative z-10 flex items-center space-x-2 text-white/90 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                    </path>
                </svg>
                <span>{{ now()->translatedFormat('l, d F Y') }}</span>
            </div>
        </header>

        <!-- Main Content Surface -->
        <main class="main-container relative z-20 px-6 pt-8 pb-10 shadow-2xl shadow-blue-900/10">

            <!-- Login Methods Icons -->
            <div class="mb-10">
                <h3 class="text-[13px] font-bold text-slate-400 uppercase tracking-widest mb-4 px-2">Metode Login</h3>
                <div class="grid grid-cols-4 gap-4">
                    <style>
                        .login-method-active.tab-beli .login-method-icon {
                            background: #eff6ff;
                            border-color: #3b82f6;
                            color: #2563eb;
                        }

                        .login-method-active.tab-qr .login-method-icon {
                            background: #ecfdf5;
                            border-color: #10b981;
                            color: #059669;
                        }

                        .login-method-active.tab-voucher .login-method-icon {
                            background: #fffbeb;
                            border-color: #f59e0b;
                            color: #d97706;
                        }

                        .login-method-active.tab-member .login-method-icon {
                            background: #f5f3ff;
                            border-color: #8b5cf6;
                            color: #7c3aed;
                        }
                    </style>
                    <button @click="tab = 'beli'" class="flex flex-col items-center space-y-2 group tab-beli"
                        :class="tab === 'beli' ? 'login-method-active' : ''">
                        <div
                            class="login-method-icon shadow-sm group-active:scale-95 transition-transform group-hover:bg-blue-50 group-hover:border-blue-200">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                        <span class="text-[11px] font-bold text-slate-500">Beli</span>
                    </button>
                    <button @click="tab = 'qr'" class="flex flex-col items-center space-y-2 group tab-qr"
                        :class="tab === 'qr' ? 'login-method-active' : ''">
                        <div
                            class="login-method-icon shadow-sm group-active:scale-95 transition-transform group-hover:bg-emerald-50 group-hover:border-emerald-200">
                            <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z">
                                </path>
                            </svg>
                        </div>
                        <span class="text-[11px] font-bold text-slate-500">Scan QR</span>
                    </button>
                    <button @click="tab = 'voucher'" class="flex flex-col items-center space-y-2 group tab-voucher"
                        :class="tab === 'voucher' ? 'login-method-active' : ''">
                        <div
                            class="login-method-icon shadow-sm group-active:scale-95 transition-transform group-hover:bg-amber-50 group-hover:border-amber-200">
                            <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                                </path>
                            </svg>
                        </div>
                        <span class="text-[11px] font-bold text-slate-500">Voucher</span>
                    </button>
                    <button @click="tab = 'member'" class="flex flex-col items-center space-y-2 group tab-member"
                        :class="tab === 'member' ? 'login-method-active' : ''">
                        <div
                            class="login-method-icon shadow-sm group-active:scale-95 transition-transform group-hover:bg-violet-50 group-hover:border-violet-200">
                            <svg class="w-6 h-6 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <span class="text-[11px] font-bold text-slate-500">Member</span>
                    </button>
                </div>
            </div>

            <!-- TAB: BELI PAKET -->
            <div x-show="tab === 'beli'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">

                <form id="checkout-form" x-ref="checkoutForm" action="{{ route('hotspot.checkout') }}" method="POST"
                    class="space-y-6" @submit="
                    $refs.submitIconLoopGlobal.classList.remove('hidden');
                ">
                    @csrf
                    <input type="hidden" name="customer_name" value="Guest">
                    <input type="hidden" name="contact" value="0000">
                    <input type="hidden" name="mac" value="{{ $mikrotikParams['mac'] ?? '' }}">
                    <input type="hidden" name="ip" value="{{ $mikrotikParams['ip'] ?? '' }}">
                    <input type="hidden" name="link-login" value="{{ $linkLogin }}">
                    <input type="hidden" name="link-orig" value="{{ $linkOrig }}">

                    <!-- Premium Voucher Cards (Horizontal Scroll) -->
                    <div class="mb-4">
                        <div class="flex justify-between items-end mb-4 px-2">
                            <h3 class="text-[13px] font-bold text-slate-400 uppercase tracking-widest">Voucher Terbaik
                            </h3>
                            <span class="text-[11px] font-bold text-blue-600">Lihat Semua</span>
                        </div>
                        <div
                            class="flex space-x-4 overflow-x-auto pb-4 hide-scrollbar snap-x cursor-grab active:cursor-grabbing">
                            @foreach($packages->where('price', '>', 0)->take(4) as $index => $pkg)
                                @php $theme = $cardThemes[$index % count($cardThemes)]; @endphp
                                <div @click="selectedPackage = {{ $pkg->id }}"
                                    class="package-card snap-start p-5 rounded-3xl border-2 transition-all cursor-pointer relative overflow-hidden flex flex-col {{ $theme['bg'] }} {{ $theme['border'] }} {{ $theme['hover'] }}"
                                    :class="selectedPackage === {{ $pkg->id }} ? '{{ $theme['active'] }}' : ''">

                                    <div class="absolute -right-4 -top-4 w-16 h-16 bg-white/10 rounded-full"></div>

                                    <div class="flex items-center space-x-2 mb-3">
                                        <div class="{{ $theme['iconBg'] }} p-1.5 rounded-lg">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                            </svg>
                                        </div>
                                        <span
                                            class="text-xs font-bold {{ $theme['text'] }} uppercase tracking-tighter">{{ $pkg->name }}</span>
                                    </div>
                                    <div class="mb-4">
                                        <span class="text-[10px] text-slate-400 font-bold uppercase block mb-1">Masa
                                            Aktif</span>
                                        <p class="text-xl font-extrabold text-slate-900 tracking-tight">Unlimited <span
                                                class="text-xs font-medium text-slate-500">/ 24 Jam</span></p>
                                    </div>
                                    <div class="pt-3 border-t border-slate-200/50 mt-auto">
                                        <div class="flex items-center justify-between">
                                            <p class="text-xl font-black {{ $theme['text'] }} tracking-tight">
                                                Rp {{ number_format($pkg->price, 0, ',', '.') }}
                                            </p>
                                            <button type="submit" name="profile_id" value="{{ $pkg->id }}"
                                                class="{{ $theme['iconBg'] }} text-white px-4 py-2 rounded-xl text-[11px] font-bold hover:opacity-90 transition-all active:scale-95 shadow-lg shadow-black/5 flex items-center">
                                                <span>Beli</span>
                                                <div x-ref="submitIconLoopGlobal" class="ml-2 hidden">
                                                    <svg class="animate-spin h-3 w-3 text-white" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                                            stroke="currentColor" stroke-width="4" fill="none"></circle>
                                                        <path class="opacity-75" fill="currentColor"
                                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                        </path>
                                                    </svg>
                                                </div>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-blue-50/50 p-4 rounded-2xl border border-blue-100 flex items-center space-x-3">
                        <div class="bg-blue-600 p-2 rounded-xl text-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p class="text-[11px] font-bold text-blue-700 leading-tight">Klik tombol "Beli" pada paket
                            pilihan Anda untuk melanjutkan ke pembayaran instan.</p>
                    </div>
                </form>
            </div>

    <!-- TAB: SCAN QR -->
    <div x-show="tab === 'qr'" style="display: none;" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
        <div class="text-center py-12">
            <div class="inline-block p-6 bg-blue-50 rounded-[40px] mb-8 relative">
                <div class="absolute inset-0 bg-blue-100 animate-ping rounded-[40px] opacity-20"></div>
                <svg class="w-16 h-16 text-blue-600 relative z-10" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z">
                    </path>
                </svg>
            </div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight mb-3">Scan QR Voucher</h2>
            <p class="text-slate-400 text-sm font-medium px-8 leading-relaxed mb-8">Arahkan kamera ke QR Code
                yang ada di struk voucher Anda untuk login otomatis.</p>

            <button
                @click="Swal.fire({title: 'Fitur Kamera', text: 'Fitur pemindaian langsung sedang dikembangkan. Silakan masukkan kode voucher secara manual.', icon: 'info', confirmButtonColor: '#3b82f6'})"
                class="w-full h-16 flex items-center justify-center rounded-2xl shadow-xl shadow-blue-500/20 text-base font-bold text-white bg-blue-600 hover:bg-blue-700 transition-all transform active:scale-[0.98]">
                Buka Kamera Scanner
            </button>

            <p class="mt-6 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Atau</p>
            <button @click="tab = 'voucher'" class="mt-4 text-sm font-bold text-blue-600 hover:underline">Masukkan Kode
                Manual</button>
        </div>
    </div>

    <!-- TAB: VOUCHER -->
    <div x-show="tab === 'voucher'" style="display: none;" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
        <form action="{{ $linkLogin }}" method="POST" class="space-y-8" @submit="
                    $refs.submitBtnVoucher.disabled = true; 
                    $refs.submitBtnVoucher.innerHTML = 'Connecting...';
                ">
            <input type="hidden" name="dst" value="{{ $linkOrig }}">

            <div class="text-center py-6">
                <div class="inline-block p-4 bg-emerald-50 rounded-3xl mb-6">
                    <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                        </path>
                    </svg>
                </div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight mb-2">Punya Voucher?</h2>
                <p class="text-slate-400 text-sm font-medium px-4">Masukkan kode voucher unik Anda untuk mulai
                    menjelajah.</p>
            </div>

            <div class="relative">
                <input type="text" name="username"
                    class="w-full bg-slate-50 border-slate-200 rounded-3xl text-3xl p-6 focus:ring-8 focus:ring-blue-500/5 focus:border-blue-500 border transition-all text-center font-mono font-black tracking-[0.4em] text-blue-700 uppercase"
                    value="{{ $mikrotikParams['username'] ?? '' }}" placeholder="XXXXX" required autofocus>
            </div>

            <input type="hidden" name="password" id="voucher_pwd" value="">

            <button x-ref="submitBtnVoucher" type="submit"
                onclick="document.getElementById('voucher_pwd').value = document.getElementsByName('username')[0].value"
                class="w-full h-16 flex items-center justify-center rounded-2xl shadow-xl shadow-emerald-500/20 text-base font-bold text-white bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 focus:outline-none transition-all transform active:scale-[0.98]">
                Aktifkan Sekarang
            </button>

            <div class="bg-indigo-50/50 p-4 rounded-2xl border border-indigo-100 flex items-center space-x-3">
                <div class="bg-indigo-600 p-2 rounded-xl text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-[11px] font-bold text-indigo-700 leading-tight">Voucher ini bersifat single-use
                    dan terikat dengan perangkat Anda.</p>
            </div>
        </form>
    </div>

    <!-- TAB: MEMBER -->
    <div x-show="tab === 'member'" style="display: none;" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
        <form action="{{ $linkLogin }}" method="POST" class="space-y-6" @submit="
                    $refs.submitBtnMember.disabled = true; 
                    $refs.submitBtnMember.innerHTML = 'Logging in...';
                ">
            <input type="hidden" name="dst" value="{{ $linkOrig }}">

            <div class="text-center py-6">
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">Portal Member</h2>
                <p class="text-slate-400 text-sm font-medium">Gunakan akun pelanggan tetap Anda.</p>
            </div>

            <div class="space-y-4">
                <div>
                    <input type="text" name="username"
                        class="w-full bg-slate-50 border-slate-200 rounded-2xl text-sm p-4 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 border transition-all"
                        value="{{ $mikrotikParams['username'] ?? '' }}" placeholder="Username" required>
                </div>
                <div class="relative">
                    <input type="password" name="password"
                        class="w-full bg-slate-50 border-slate-200 rounded-2xl text-sm p-4 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 border transition-all"
                        placeholder="Password" required>
                </div>
            </div>

            <button x-ref="submitBtnMember" type="submit"
                class="w-full h-16 flex items-center justify-center rounded-2xl shadow-xl shadow-slate-900/10 text-base font-bold text-white bg-slate-800 hover:bg-slate-900 focus:outline-none transition-all transform active:scale-[0.98]">
                Masuk ke Akun
            </button>

            <a href="#" class="block text-center text-xs font-bold text-blue-600 hover:underline">Lupa Password
                Akun?</a>
        </form>
    </div>

    <!-- Spotlight Illustration -->
    <div class="mt-12">
        <div class="flex justify-between items-center mb-6 px-2">
            <h3 class="text-[13px] font-bold text-slate-400 uppercase tracking-widest">Layanan Premium</h3>
            <div class="flex space-x-1">
                <div class="w-1.5 h-1.5 bg-blue-600 rounded-full"></div>
                <div class="w-1.5 h-1.5 bg-slate-200 rounded-full"></div>
                <div class="w-1.5 h-1.5 bg-slate-200 rounded-full"></div>
            </div>
        </div>
        <div
            class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-[32px] p-6 text-white relative overflow-hidden group">
            <div
                class="absolute -right-4 -bottom-4 w-32 h-32 opacity-20 group-hover:scale-110 transition-transform duration-500">
                <img src="{{ asset('assets/hotspot/router_illustration.png') }}"
                    class="w-full h-full object-contain filter brightness-0 invert" alt="">
            </div>
            <div class="relative z-10 pr-20">
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] mb-2 opacity-80">Solusi Internet
                    Rumah</p>
                <h4 class="text-xl font-black leading-tight mb-4">Fiber Optic Kecepatan Tinggi</h4>
                <button
                    class="bg-white/20 backdrop-blur-md px-4 py-2 rounded-xl text-[11px] font-black uppercase tracking-wider hover:bg-white text-white hover:text-blue-700 transition-all">Hubungi
                    Kami</button>
            </div>
        </div>
    </div>

    <footer class="mt-12 text-center">
        <p class="text-[10px] text-slate-300 font-bold uppercase tracking-[0.3em] mb-4">Powered by
            {{ $companyName }} Hub
        </p>
        <div class="flex justify-center space-x-4 opacity-30">
            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
            </svg>
            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
            </svg>
        </div>
    </footer>
    </main>
    </div>

    <!-- Alert Styles Override -->
    <style>
        .swal2-popup {
            border-radius: 24px !important;
            font-family: 'Outfit', sans-serif !important;
        }

        .swal2-title {
            font-weight: 800 !important;
            color: #1e293b !important;
        }

        .swal2-confirm {
            border-radius: 12px !important;
            font-weight: 700 !important;
        }
    </style>
</body>

</html>