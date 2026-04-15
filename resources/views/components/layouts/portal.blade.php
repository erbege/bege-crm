<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50 dark:bg-gray-900">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Customer Portal - {{ config('app.name', 'SKNET CRM') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <script>
        // Apply theme on initial page load (prevents flash)
        function applyPortalTheme() {
            if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
        applyPortalTheme();

        // Re-apply theme after every wire:navigate SPA navigation
        // Guard to prevent registering duplicate listeners on re-execution
        if (!window.__portalThemeListenerRegistered) {
            document.addEventListener('livewire:navigated', applyPortalTheme);
            window.__portalThemeListenerRegistered = true;
        }
    </script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
    @stack('styles')
</head>

<body
    class="font-sans antialiased min-h-screen bg-slate-50 dark:bg-[#0f172a] text-slate-900 dark:text-slate-100 selection:bg-indigo-500 selection:text-white">

    <div class="min-h-screen flex flex-col pb-20 sm:pb-0">
        <!-- Top Header (Greeting + Notifications) -->
        <header class="sticky top-0 z-30 bg-slate-50 dark:bg-[#0f172a] px-4 py-6 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Selamat Pagi,</p>
                    <h1 class="text-2xl font-bold dark:text-white tracking-tight">{{ auth('customer')->user()->name }}
                    </h1>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Dark mode toggle -->
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
                        class="p-2.5 rounded-full bg-slate-200/50 dark:bg-slate-800/50 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors">
                        <svg class="h-5 w-5 dark:hidden" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                        </svg>
                        <svg class="h-5 w-5 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                        </svg>
                    </button>

                    <!-- Notification Icon (Mockup) -->
                    <div class="relative">
                        <button
                            class="p-2.5 rounded-full bg-slate-200/50 dark:bg-slate-800/50 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31a8.967 8.967 0 0 1-2.312-6.022c0-3.472-1.425-6.361-4.103-7.675c-.389-.191-.823-.307-1.282-.307c-.459 0-.893.116-1.282.307C8.65 1.77 7.225 4.659 7.225 8.13c0 1.948-.624 3.797-1.835 5.307c-.256.319-.512.639-.768.959c-.43.539-.757 1.139-.856 1.833a1.45 1.45 0 0 0 .546 1.404a11.534 11.534 0 0 0 3.033 1.144m1.115 1.74a2.25 2.25 0 0 0 4.498 0m-2.249 0c.264 0 .524-.029.774-.084m-4.498 0a2.25 2.25 0 0 1-2.249-2.25" />
                            </svg>
                        </button>
                        <span class="absolute top-2 right-2 flex h-2 w-2">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                            <span
                                class="relative inline-flex rounded-full h-2 w-2 bg-rose-500 border border-white dark:border-slate-900"></span>
                        </span>
                    </div>

                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 w-full max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            {{ $slot }}
        </main>

        <!-- Bottom Navigation (Mobile & Tablet) -->
        <nav
            class="fixed bottom-0 inset-x-0 z-50 bg-slate-50 dark:bg-[#0f172a] border-t border-slate-200 dark:border-slate-800/60 pb-safe">
            <div class="max-w-3xl mx-auto flex items-center justify-around h-20 px-4">
                <a href="{{ route('portal.dashboard') }}" wire:navigate
                    class="flex flex-col items-center justify-center space-y-1 group relative flex-1 {{ request()->routeIs('portal.dashboard') ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500 hover:text-indigo-500' }}">
                    <div
                        class="p-2 rounded-2xl transition-all duration-300 {{ request()->routeIs('portal.dashboard') ? 'bg-indigo-50 dark:bg-indigo-500/10' : '' }}">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider">Beranda</span>
                </a>

                <a href="{{ route('portal.invoices') }}" wire:navigate
                    class="flex flex-col items-center justify-center space-y-1 group relative flex-1 {{ request()->routeIs('portal.invoices') ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500 hover:text-indigo-500' }}">
                    <div
                        class="p-2 rounded-2xl transition-all duration-300 {{ request()->routeIs('portal.invoices') ? 'bg-indigo-50 dark:bg-indigo-500/10' : '' }}">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider">Tagihan</span>
                </a>

                <a href="{{ route('portal.tickets') }}" wire:navigate
                    class="flex flex-col items-center justify-center space-y-1 group relative flex-1 {{ request()->routeIs('portal.tickets') ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500 hover:text-indigo-500' }}">
                    <div
                        class="p-2 rounded-2xl transition-all duration-300 {{ request()->routeIs('portal.tickets') ? 'bg-indigo-50 dark:bg-indigo-500/10' : '' }}">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider">Bantuan</span>
                </a>

                <div x-data="{ open: false }" @click.away="open = false" class="relative flex-1">
                    <button @click="open = !open"
                        class="w-full flex flex-col items-center justify-center space-y-1 text-slate-400 dark:text-slate-500 hover:text-indigo-500 focus:outline-none transition-colors">
                        <div
                            class="p-2 rounded-2xl transition-all duration-300 group-hover:bg-slate-100 dark:group-hover:bg-slate-800">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider">Akun</span>
                    </button>

                    <div x-show="open" x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-4"
                        class="absolute bottom-full right-0 mb-4 w-48 rounded-2xl bg-white dark:bg-slate-800 shadow-2xl ring-1 ring-black ring-opacity-5 divide-y divide-slate-100 dark:divide-slate-700 overflow-hidden"
                        style="display: none;">
                        <div class="px-4 py-3">
                            <p
                                class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                                Login sebagai</p>
                            <p class="text-sm font-bold text-slate-900 dark:text-white truncate">
                                {{ auth('customer')->user()->name }}
                            </p>
                        </div>
                        <form method="POST" action="{{ route('portal.logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full text-left px-4 py-3 text-sm font-bold text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors">
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
    </div>


    @stack('modals')

    @livewireScripts

    <!-- SweetAlert2 Global Handlers -->
    <script>
        document.addEventListener('livewire:init', () => {
            const handleToast = (data) => {
                const params = Array.isArray(data) ? data[0] : (data || {});
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });
                Toast.fire({
                    icon: params.type || params.icon || 'success',
                    title: params.message || params.title || 'Notification'
                });
            };
            Livewire.on('toast', handleToast);
            Livewire.on('alert', handleToast);

            // Allow clicking to copy texts inside sweetaler.
            const handleConfirm = (data) => {
                const params = Array.isArray(data) ? data[0] : (data || {});
                Swal.fire({
                    title: params.title || 'Apakah Anda yakin?',
                    text: params.text || params.message || "Pastikan tindakan ini benar!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: params.confirmButtonColor || '#4f46e5',
                    cancelButtonColor: params.cancelButtonColor || '#d33',
                    confirmButtonText: params.confirmButtonText || 'Ya, Lanjutkan!',
                    cancelButtonText: params.cancelButtonText || 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch(params.action || 'confirmed', { id: params.id });
                    }
                });
            };
            Livewire.on('trigger-confirm-modal', handleConfirm);
        });
    </script>
</body>

</html>