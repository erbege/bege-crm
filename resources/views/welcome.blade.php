<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'BEGE-CRM') }} - Terpadu & Profesional</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .dark .glass {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .text-gradient {
            background: linear-gradient(to right, #6366f1, #06b6d4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>

    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>

<body class="antialiased bg-slate-50 dark:bg-[#030712] text-slate-900 dark:text-slate-100 selection:bg-indigo-500/30">

    <!-- Hero Background Video -->
    <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
        <!-- <video autoplay muted loop playsinline
            class="absolute min-w-full min-h-full object-cover opacity-20 dark:opacity-30">
            <source
                src="https://player.vimeo.com/external/370331493.sd.mp4?s=7bcd1797fa62562479e000dbcf826477b494101e&profile_id=139&oauth2_token_id=57447761"
                type="video/mp4">
        </video> -->
        <video autoplay muted loop playsinline
            class="absolute min-w-full min-h-full object-cover opacity-20 dark:opacity-30">
            <source src="https://www.pexels.com/id-id/download/video/3129576/" type="video/mp4">
        </video>
        <div
            class="absolute inset-0 bg-gradient-to-b from-slate-50/50 via-transparent to-slate-50 dark:from-[#030712]/50 dark:via-transparent dark:to-[#030712]">
        </div>
    </div>

    <div class="relative z-10 flex flex-col min-h-screen">
        <!-- Navigation -->
        <header class="sticky top-0 z-50 px-6 py-4">
            <nav
                class="max-w-7xl mx-auto flex items-center justify-between glass px-6 py-3 rounded-2xl shadow-xl shadow-indigo-500/5">
                <div class="flex items-center gap-3">
                    <x-application-mark class="h-9 w-auto" />
                    <span class="text-xl font-extrabold tracking-tight text-slate-900 dark:text-white">BEGE<span
                            class="text-indigo-500">CRM</span></span>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Theme Switcher -->
                    <button id="theme-toggle" class="p-2 text-slate-500 hover:text-indigo-500 transition-colors">
                        <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                        <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path
                                d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                                fill-rule="evenodd" clip-rule="evenodd"></path>
                        </svg>
                    </button>

                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="px-5 py-2 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-500 transition-all shadow-lg shadow-indigo-500/25">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}"
                                class="px-6 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-500 transition-all shadow-lg shadow-indigo-500/25">Log
                                in</a>
                        @endauth
                    @endif
                </div>
            </nav>
        </header>

        <!-- Hero Section -->
        <main class="flex-grow flex flex-col justify-center px-4 max-w-7xl mx-auto w-full py-12">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-8 text-left">
                    <div
                        class="inline-flex items-center gap-2 px-3 py-2 rounded-full bg-indigo-500/10 text-indigo-500 text-xs font-bold uppercase tracking-widest border border-indigo-500/20">
                        <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                        Next-Gen ISP CRM Solution
                    </div>

                    <h1
                        class="text-5xl sm:text-6xl lg:text-7xl font-black tracking-tight leading-none text-slate-900 dark:text-white">
                        Beyond Just <br>
                        <span class="text-gradient">Management</span>
                    </h1>

                    <p class="text-xl text-slate-600 dark:text-slate-400 max-w-xl leading-relaxed">
                        Transformasikan bisnis ISP Anda dengan ekosistem digital yang mengotomatisasi segalanya mulai
                        dari
                        pendaftaran teknis, provisioning OLT, hingga penagihan dan isolir otomatis.
                    </p>

                    <div class="flex flex-wrap gap-4 pt-4">
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="px-8 py-4 text-lg font-bold text-white bg-slate-900 dark:bg-white dark:text-slate-900 rounded-2xl hover:scale-105 transition-all shadow-xl">Masuk
                                ke Dashboard</a>
                        @else
                            <a href="{{ route('login') }}"
                                class="px-8 py-4 text-lg font-bold text-white bg-indigo-600 rounded-2xl hover:scale-105 transition-all shadow-xl shadow-indigo-500/25">Mulai
                                Sekarang</a>
                        @endauth
                        <a href="#features"
                            class="px-8 py-4 text-lg font-bold text-slate-700 dark:text-slate-300 glass rounded-2xl hover:bg-white/10 transition-all">Lihat
                            Kemampuan</a>
                    </div>

                    <!-- Trust Indicators -->
                    <div
                        class="pt-8 flex items-center gap-8 opacity-50 grayscale hover:grayscale-0 transition-all duration-500">
                        <span class="font-bold text-sm tracking-widest text-slate-400">SUPPORTED HARDWARE:</span>
                        <div class="flex gap-6 items-center">
                            <span class="font-black text-xl italic">MikroTik</span>
                            <span class="font-black text-xl tracking-tighter">ZTE</span>
                            <span class="font-black text-xl tracking-widest">HUAWEI</span>
                        </div>
                    </div>
                </div>

                <div class="relative hidden lg:block">
                    <div class="absolute -inset-10 bg-indigo-500/15 blur-3xl rounded-full animate-pulse"></div>
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&q=80&w=800"
                            alt="ISP Infrastructure"
                            class="rounded-3xl shadow-2xl border border-white/10 transform rotate-2 hover:rotate-0 transition-transform duration-500 z-10 relative">
                        <!-- Floating Glass Card -->
                        <div class="absolute -bottom-6 -left-12 glass p-6 rounded-2xl shadow-2xl z-20">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-green-500/20 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Server Status
                                    </p>
                                    <p class="text-sm font-bold">Radius Online</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Narrative Section -->
            <div class="mt-32 grid lg:grid-cols-3 gap-12 border-t border-slate-200 dark:border-white/5 pt-16">
                <div class="space-y-4">
                    <h3 class="text-2xl font-bold text-indigo-500">Efisiensi Total</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed italic">
                        "Waktu adalah aset paling berharga bagi pemilik ISP."
                    </p>
                    <p class="text-slate-500 text-sm">
                        Otomatisasi kami memastikan teknisi lapangan dapat melakukan provisioning OLT hanya dengan
                        beberapa
                        klik di dashboard, tanpa perlu konfigurasi CLI manual yang berisiko.
                    </p>
                </div>
                <div class="space-y-4">
                    <h3 class="text-2xl font-bold text-indigo-500">Keamanan Cloud</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed italic">
                        "Data pelanggan Anda adalah prioritas utama kami."
                    </p>
                    <p class="text-slate-500 text-sm">
                        Dengan sinkronisasi FreeRadius yang real-time dan enkripsi database tingkat lanjut, akses
                        pelanggan
                        terjaga 24/7 dengan kontrol manajemen yang sangat granular.
                    </p>
                </div>
                <div class="space-y-4">
                    <h3 class="text-2xl font-bold text-indigo-500">Analisis Mendalam</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed italic">
                        "Keputusan berbasis data untuk pertumbuhan bisnis."
                    </p>
                    <p class="text-slate-500 text-sm">
                        Laporan keuangan otomatis dan visualisasi coverage area membantu Anda mengidentifikasi titik
                        potensi pertumbuhan baru sebelum kompetitor Anda menyadarinya.
                    </p>
                </div>
            </div>

            <!-- Features -->
            <div id="features" class="mt-32 space-y-12">
                <div class="text-center space-y-4">
                    <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white">Ekosistem Fitur Terpadu</h2>
                    <p class="text-slate-500 max-w-2xl mx-auto">Satu platform, ribuan kemungkinan. Kelola bisnis ISP
                        Anda
                        tanpa batasan teknologi.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Feature 1 -->
                    <div
                        class="group relative glass p-1 rounded-3xl overflow-hidden hover:shadow-2xl transition-all hover:-translate-y-2">
                        <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=400"
                            class="w-full aspect-[4/3] object-cover rounded-2xl opacity-80 group-hover:opacity-100 transition-opacity"
                            alt="Billing">
                        <div class="p-6">
                            <h3 class="text-lg font-bold mb-2">Automated Billing</h3>
                            <p class="text-sm text-slate-500">Penagihan otomatis setiap tanggal jatuh tempo dengan
                                notifikasi WhatsApp & Email instan. Mendukung berbagai payment gateway.</p>
                        </div>
                    </div>

                    <!-- Feature 2 -->
                    <div
                        class="group relative glass p-1 rounded-3xl overflow-hidden hover:shadow-2xl transition-all hover:-translate-y-2">
                        <img src="https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&q=80&w=400"
                            class="w-full aspect-[4/3] object-cover rounded-2xl opacity-80 group-hover:opacity-100 transition-opacity"
                            alt="Network">
                        <div class="p-6">
                            <h3 class="text-lg font-bold mb-2">Technical Provisioning</h3>
                            <p class="text-sm text-slate-500">Ucapkan selamat tinggal pada script manual. Integrasi
                                langsung
                                dengan Mikrotik & OLT (ZTE/Huawei) untuk aktivasi instan.</p>
                        </div>
                    </div>

                    <!-- Feature 3 -->
                    <div
                        class="group relative glass p-1 rounded-3xl overflow-hidden hover:shadow-2xl transition-all hover:-translate-y-2">
                        <img src="https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?auto=format&fit=crop&q=80&w=400"
                            class="w-full aspect-[4/3] object-cover rounded-2xl opacity-80 group-hover:opacity-100 transition-opacity"
                            alt="Support">
                        <div class="p-6">
                            <h3 class="text-lg font-bold mb-2">Ticketing System</h3>
                            <p class="text-sm text-slate-500">Kelola keluhan pelanggan secara sistematis. Dari laporan
                                masuk
                                hingga teknisi selesai memperbaiki, semua terrecord sempurna.</p>
                        </div>
                    </div>

                    <!-- Feature 4 -->
                    <div
                        class="group relative glass p-1 rounded-3xl overflow-hidden hover:shadow-2xl transition-all hover:-translate-y-2">
                        <img src="https://images.unsplash.com/photo-1526772662000-3f88f10405ff?auto=format&fit=crop&q=80&w=400"
                            class="w-full aspect-[4/3] object-cover rounded-2xl opacity-80 group-hover:opacity-100 transition-opacity"
                            alt="Map">
                        <div class="p-6">
                            <h3 class="text-lg font-bold mb-2">GIS & Coverage</h3>
                            <p class="text-sm text-slate-500">Visualisasikan jaringan fiber optic Anda. Pantau lokasi
                                ODC,
                                ODP, dan dropcore pelanggan melalui peta interaktif yang presisi.</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer class="py-12 px-6">
            <div
                class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-6 border-t border-slate-200 dark:border-white/10 pt-8">
                <div class="flex items-center gap-2">
                    <x-application-mark class="h-6 w-auto opacity-50" />
                    <span class="text-sm font-bold text-slate-400">BEGE-CRM &copy; {{ date('Y') }}</span>
                </div>
                <div class="flex gap-8 text-sm text-slate-400">
                    <a href="#" class="hover:text-indigo-500 transition-colors">Documentation</a>
                    <a href="#" class="hover:text-indigo-500 transition-colors">Privacy Policy</a>
                    <a href="#" class="hover:text-indigo-500 transition-colors">Terms of Service</a>
                </div>
            </div>
        </footer>
    </div>

    <script>
        const themeToggleBtn = document.getElementById('theme-toggle');
        const darkIcon = document.getElementById('theme-toggle-dark-icon');
        const lightIcon = document.getElementById('theme-toggle-light-icon');

        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            lightIcon.classList.remove('hidden');
        } else {
            darkIcon.classList.remove('hidden');
        }

        themeToggleBtn.addEventListener('click', function () {
            darkIcon.classList.toggle('hidden');
            lightIcon.classList.toggle('hidden');

            if (localStorage.getItem('color-theme')) {
                if (localStorage.getItem('color-theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                }
            } else {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            }
        });
    </script>
</body>

</html>