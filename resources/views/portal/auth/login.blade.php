<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer Login - {{ config('app.name', 'BEGE CRM') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <script>
        if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body
    class="font-sans antialiased bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen flex flex-col items-center justify-center p-6 relative overflow-hidden">

    <!-- Premium Background Decor -->
    <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
        <div
            class="absolute top-[-10%] right-[-10%] w-[600px] h-[600px] bg-indigo-500/10 dark:bg-indigo-500/20 rounded-full blur-[120px] animate-pulse">
        </div>
        <div
            class="absolute bottom-[-10%] left-[-10%] w-[600px] h-[600px] bg-purple-500/10 dark:bg-purple-900/20 rounded-full blur-[120px]">
        </div>
    </div>

    <!-- Dark Mode Toggle -->
    <div class="absolute top-6 right-6 z-20">
        <button x-data="{
            toggle() {
                if (localStorage.getItem('darkMode') === 'true') {
                    localStorage.setItem('darkMode', 'false');
                    document.documentElement.classList.remove('dark');
                } else {
                    localStorage.setItem('darkMode', 'true');
                    document.documentElement.classList.add('dark');
                }
            }
        }" @click="toggle()"
            class="p-3 rounded-2xl bg-white dark:bg-slate-900 shadow-xl dark:shadow-none border border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 group transition-all duration-300">
            <svg class="h-5 w-5 dark:hidden group-hover:text-indigo-600 transition-colors" fill="none"
                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
            </svg>
            <svg class="h-5 w-5 hidden dark:block group-hover:text-amber-400 transition-colors" fill="none"
                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
            </svg>
        </button>
    </div>

    <div class="w-full max-w-md relative z-10 space-y-8 animate-fade-in">
        <!-- Logo & Title -->
        <div class="text-center space-y-4">
            <div
                class="inline-flex items-center justify-center w-20 h-20 bg-white dark:bg-slate-900 rounded-3xl shadow-2xl dark:shadow-none border border-slate-100 dark:border-slate-800 mb-2 transform hover:rotate-6 transition-transform duration-500">
                <x-authentication-card-logo class="h-12 w-auto" />
            </div>
            <div class="space-y-1">
                <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Portal Pelanggan</h2>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Kelola layanan internet Anda dengan
                    mudah</p>
            </div>
        </div>

        <!-- Login Card -->
        <div
            class="bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-2xl dark:shadow-none border border-white dark:border-slate-800/60 p-8 sm:p-10 relative overflow-hidden group">
            <!-- Decorative Light Beam -->
            <div
                class="absolute -top-24 -left-24 w-48 h-48 bg-indigo-500/10 rounded-full blur-3xl group-hover:bg-indigo-500/20 transition-colors duration-700">
            </div>

            @if ($errors->any())
                <div class="mb-6 animate-shake">
                    <div
                        class="bg-rose-50 dark:bg-rose-500/10 border border-rose-100 dark:border-rose-500/20 rounded-2xl p-4 flex items-center space-x-3">
                        <svg class="h-5 w-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                        <p class="text-xs font-bold text-rose-600 dark:text-rose-400">{{ $errors->first() }}</p>
                    </div>
                </div>
            @endif

            <form action="{{ route('portal.login') }}" method="POST" class="space-y-6">
                @csrf
                <div class="space-y-2">
                    <label for="phone"
                        class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-2">Nomor
                        Telepon</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                            </svg>
                        </div>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required
                            class="block w-full pl-14 pr-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-800/60 rounded-3xl text-sm placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 dark:focus:border-indigo-500/50 transition-all"
                            placeholder="08123456789">
                    </div>
                </div>

                <div class="space-y-2" x-data="{ show: false }">
                    <label for="password"
                        class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                        </div>
                        <input :type="show ? 'text' : 'password'" name="password" id="password" required
                            class="block w-full pl-14 pr-14 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-800/60 rounded-3xl text-sm placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 dark:focus:border-indigo-500/50 transition-all"
                            placeholder="••••••••">
                        <button type="button" @click="show = !show"
                            class="absolute inset-y-0 right-0 pr-5 flex items-center text-slate-400 hover:text-indigo-600 transition-colors">
                            <svg x-show="!show" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.644m17.83 0a1.012 1.012 0 0 1 0-.644M12 18.75a6.75 6.75 0 1 1 0-13.5 6.75 6.75 0 0 1 0 13.5ZM12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                            </svg>
                            <svg x-show="show" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 mt-1 ml-2"><i>Password default
                            adalah nomor telepon Anda.</i></p>
                </div>

                <div class="flex items-center space-x-3 ml-2">
                    <input id="remember" type="checkbox" name="remember"
                        class="w-5 h-5 text-indigo-600 bg-slate-100 border-slate-200 dark:bg-slate-800 dark:border-slate-700 rounded-lg focus:ring-indigo-500 transition-all cursor-pointer">
                    <label for="remember"
                        class="text-sm font-bold text-slate-600 dark:text-slate-400 cursor-pointer">Ingat saya</label>
                </div>

                <button type="submit"
                    class="w-full py-5 bg-indigo-600 hover:bg-indigo-700 text-white font-black uppercase tracking-widest rounded-3xl shadow-xl shadow-indigo-500/30 transform hover:-translate-y-1 active:scale-[0.98] transition-all duration-300">
                    Masuk ke Portal
                </button>
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center text-[10px] font-bold text-slate-400 dark:text-slate-600 uppercase tracking-[0.3em]">
            &copy; {{ date('Y') }} {{ config('app.name') }} &bull; ISP Premium
        </p>
    </div>

    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        .animate-shake {
            animation: shake 0.4s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
        }
    </style>
    @livewireScripts
</body>

</html>