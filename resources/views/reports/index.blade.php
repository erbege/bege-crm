<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Laporan Ringkasan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Summary Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Total Customers -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-sm text-gray-500 dark:text-gray-400 font-semibold uppercase">Total Pelanggan
                            </h2>
                            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalCustomers }}</p>
                            <p class="text-xs text-green-500 font-semibold">+{{ $newCustomersThisMonth }} bulan ini</p>
                        </div>
                    </div>
                </div>

                <!-- Active Customers -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-sm text-gray-500 dark:text-gray-400 font-semibold uppercase">Pelanggan Aktif
                            </h2>
                            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $activeCustomers }}</p>
                            <p class="text-xs text-gray-500">{{ $inactiveCustomers }} non-aktif</p>
                        </div>
                    </div>
                </div>

                <!-- Subscription Income -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-sm text-gray-500 dark:text-gray-400 font-semibold uppercase">Pendapatan
                                Langganan</h2>
                            <p class="text-xl font-bold text-gray-800 dark:text-white">Rp
                                {{ number_format($subscriptionIncome, 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-gray-500">Bulan ini</p>
                        </div>
                    </div>
                </div>

                <!-- Hotspot Income -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-sm text-gray-500 dark:text-gray-400 font-semibold uppercase">Pendapatan
                                Hotspot</h2>
                            <p class="text-xl font-bold text-gray-800 dark:text-white">Rp
                                {{ number_format($hotspotIncome, 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-gray-500">Estimasi bulan ini</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Income Chart -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Tren Pendapatan (6 Bulan
                        Terakhir)</h3>
                    <div class="relative h-64">
                        <canvas id="incomeChart"></canvas>
                    </div>
                </div>

                <!-- Customer Composition Chart (Placeholder for future or just structure) -->
                <!-- Ideally breakdown by packge but for now maybe just simple pie chart of Status -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Komposisi Pelanggan</h3>
                    <div class="relative h-64 flex justify-center items-center">
                        <canvas id="customerChart"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            // Use an IIFE - by the time @stack('scripts') executes, DOM is already ready
            // This also works with wire:navigate SPA navigation (DOMContentLoaded won't fire)
            (function () {
                const incomeCanvas = document.getElementById('incomeChart');
                const customerCanvas = document.getElementById('customerChart');

                if (!incomeCanvas || !customerCanvas) return;

                // Destroy existing chart instances to prevent "Canvas is already in use" errors
                const existingIncome = Chart.getChart(incomeCanvas);
                const existingCustomer = Chart.getChart(customerCanvas);
                if (existingIncome) existingIncome.destroy();
                if (existingCustomer) existingCustomer.destroy();

                // Income Chart
                new Chart(incomeCanvas.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [
                            {
                                label: 'Langganan',
                                data: @json($chartSubscription),
                                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                                borderColor: 'rgba(59, 130, 246, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Hotspot',
                                data: @json($chartHotspot),
                                backgroundColor: 'rgba(249, 115, 22, 0.5)',
                                borderColor: 'rgba(249, 115, 22, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function (value) {
                                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        let label = context.dataset.label || '';
                                        if (label) label += ': ';
                                        if (context.parsed.y !== null) {
                                            label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });

                // Customer Chart
                new Chart(customerCanvas.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Aktif', 'Non-Aktif'],
                        datasets: [{
                            data: [{{ $activeCustomers }}, {{ $inactiveCustomers }}],
                            backgroundColor: [
                                'rgba(16, 185, 129, 0.6)',
                                'rgba(239, 68, 68, 0.6)'
                            ],
                            borderColor: [
                                'rgba(16, 185, 129, 1)',
                                'rgba(239, 68, 68, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                    }
                });
            })();
        </script>
    @endpush
</x-app-layout>