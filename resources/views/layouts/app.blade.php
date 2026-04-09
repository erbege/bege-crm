<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50 dark:bg-gray-900">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
    @stack('styles')

    <!-- Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
</head>

<body
    class="font-sans antialiased min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 selection:bg-indigo-500 selection:text-white">
    <x-banner />

    <div class="min-h-screen">
        @livewire('navigation-menu')

        <!-- Page Heading -->
        @if (isset($header))
            <header
                class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700/50 shadow-sm sticky top-16 z-30 transition-colors duration-300">
                <div
                    class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-4 sm:gap-0">
                    <div>
                        {{ $header }}
                    </div>

                    <!-- Real-time Clock -->
                    <div x-data="{ 
                                                                    time: new Date(),
                                                                    init() {
                                                                        setInterval(() => {
                                                                            this.time = new Date();
                                                                        }, 1000);
                                                                    },
                                                                    get formattedTime() {
                                                                        return this.time.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }).replace(/\./g, ':');
                                                                    },
                                                                    get formattedDate() {
                                                                        return this.time.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
                                                                    }
                                                                }" class="hidden sm:flex flex-col items-end text-right">
                        <div class="text-2xl font-bold text-gray-800 dark:text-gray-100 font-mono tracking-tight leading-none"
                            x-text="formattedTime"></div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wider mt-1"
                            x-text="formattedDate"></div>
                    </div>
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main class="transition-all duration-300">
            {{ $slot }}
        </main>
    </div>

    @stack('modals')

    @livewireScripts

    <!-- SweetAlert2 Global Handlers -->
    <script>
        document.addEventListener('livewire:init', () => {
            // Unified toast handler (covers 'toast' and 'alert' events)
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

            // Unified confirmation handler (covers 'trigger-confirm-modal' and 'confirm-delete')
            const handleConfirm = (data) => {
                const params = Array.isArray(data) ? data[0] : (data || {});
                Swal.fire({
                    title: params.title || 'Apakah Anda yakin?',
                    text: params.text || params.message || "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: params.confirmButtonColor || '#d33',
                    cancelButtonColor: params.cancelButtonColor || '#3085d6',
                    confirmButtonText: params.confirmButtonText || 'Ya, Hapus!',
                    cancelButtonText: params.cancelButtonText || 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch(params.action || 'confirmed', { id: params.id });
                    }
                });
            };
            Livewire.on('trigger-confirm-modal', handleConfirm);
            Livewire.on('confirm-delete', handleConfirm);

            // Livewire Error Handling Hook (503 errors)
            Livewire.hook('request.respond', ({ status, response }) => {
                if (status === 503) {
                    response.text().then(text => {
                        try {
                            const data = JSON.parse(text);
                            Swal.fire({
                                icon: 'error',
                                title: data.error || 'Connection Error',
                                text: data.message || 'Service Unavailable',
                                confirmButtonColor: '#d33',
                            });
                        } catch (e) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Connection Error',
                                text: 'Layanan tidak tersedia saat ini.',
                                confirmButtonColor: '#d33',
                            });
                        }
                    });
                }
            });
        });

        // Axios Error Handling Interceptor
        document.addEventListener('DOMContentLoaded', () => {
            if (window.axios) {
                window.axios.interceptors.response.use(
                    response => response,
                    error => {
                        if (error.response && error.response.status === 503) {
                            Swal.fire({
                                icon: 'error',
                                title: error.response.data.error || 'Connection Error',
                                text: error.response.data.message || 'Layanan tidak tersedia saat ini.',
                                confirmButtonColor: '#d33',
                            });
                        }
                        return Promise.reject(error);
                    }
                );
            }
        });
    </script>
    <div id="offline-indicator"
        class="fixed bottom-4 right-4 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg transform translate-y-20 transition-transform duration-300 z-50 flex items-center space-x-3 hidden">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414" />
        </svg>
        <span>Anda sedang offline. Periksa koneksi internet Anda.</span>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const indicator = document.getElementById('offline-indicator');

            function updateOnlineStatus() {
                if (navigator.onLine) {
                    indicator.classList.add('translate-y-20');
                    setTimeout(() => indicator.classList.add('hidden'), 300);
                } else {
                    indicator.classList.remove('hidden');
                    // Small delay to allow removal of hidden class before animating
                    setTimeout(() => indicator.classList.remove('translate-y-20'), 10);
                }
            }

            window.addEventListener('online', updateOnlineStatus);
            window.addEventListener('offline', updateOnlineStatus);

            // Check initial status
            updateOnlineStatus();
        });
    </script>
    @stack('scripts')
</body>

</html>