<div>
    @push('styles')
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
    @endpush
    <x-slot name="header">
        <div class="flex justify-between items-center w-full">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Premium Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Customer Stats Card -->
                <div
                    class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700/50 overflow-hidden relative">
                    <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                        <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <div class="p-6 relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="p-2 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                            </div>
                            <span
                                class="text-xs font-bold uppercase tracking-widest text-indigo-500 bg-indigo-50 dark:bg-indigo-950 px-2 py-1 rounded-lg">Overall</span>
                        </div>
                        <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Total Berlangganan</h3>
                        <div class="flex items-baseline space-x-2">
                            <span
                                class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">{{ $customerStats['total'] ?? 0 }}</span>
                            <span class="text-xs font-bold text-green-500">+{{ $customerStats['new_this_month'] ?? 0 }}
                                mo</span>
                        </div>
                        <div
                            class="mt-4 pt-4 border-t border-gray-50 dark:border-gray-700/50 flex justify-between items-center text-xs">
                            <div class="flex items-center space-x-1">
                                <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                <span class="text-gray-500 font-medium">Aktif: <span
                                        class="font-black text-green-600 dark:text-green-400">{{ $customerStats['active'] ?? 0 }}</span></span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <div class="w-2 h-2 rounded-full bg-orange-500"></div>
                                <span class="text-gray-500 font-medium">Isolir: <span
                                        class="font-black text-orange-600 dark:text-orange-400">{{ $customerStats['suspended'] ?? 0 }}</span></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Invoice Stats Card -->
                <div
                    class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700/50 overflow-hidden relative">
                    <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                        <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                    <div class="p-6 relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="p-2 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                            <span
                                class="text-xs font-bold uppercase tracking-widest text-emerald-500 bg-emerald-50 dark:bg-emerald-950 px-2 py-1 rounded-lg">Bulan
                                Ini</span>
                        </div>
                        <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Tagihan Lunas</h3>
                        <div class="flex items-baseline space-x-2">
                            <span
                                class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">{{ $invoiceStats['paid_this_month'] ?? 0 }}</span>
                            <span class="text-xs font-bold text-gray-400">/ inv</span>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-50 dark:border-gray-700/50 flex space-x-4 text-xs">
                            <div class="flex flex-col">
                                <span class="text-gray-400 mb-0.5">Unpaid</span>
                                <span class="font-black text-yellow-600">{{ $invoiceStats['unpaid'] ?? 0 }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-gray-400 mb-0.5">Overdue</span>
                                <span class="font-black text-red-600">{{ $invoiceStats['overdue'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Revenue Stats Card -->
                <div
                    class="group bg-indigo-600 dark:bg-indigo-600 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden relative">
                    <div class="absolute top-0 right-0 p-4 opacity-10">
                        <svg class="w-20 h-20 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <div class="p-6 relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-2 rounded-lg bg-white/20 text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </div>
                            <span
                                class="text-xs font-bold uppercase tracking-widest text-indigo-100 bg-white/10 px-2 py-1 rounded-lg">Revenue</span>
                        </div>
                        <h3 class="text-xs font-bold text-indigo-100 uppercase tracking-wider mb-1">Pendapatan</h3>
                        <div class="flex flex-col">
                            <span class="text-2xl font-black text-white leading-tight tracking-tight">Rp
                                {{ number_format($revenueStats['this_month'] ?? 0, 0, ',', '.') }}</span>
                            <span class="text-xs font-bold text-indigo-200 mt-1 italic">Est: Rp
                                {{ number_format($revenueStats['projected'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Hotspot Stats Card -->
                <div
                    class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700/50 overflow-hidden relative">
                    <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                        <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0">
                            </path>
                        </svg>
                    </div>
                    <div class="p-6 relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-2 rounded-lg bg-sky-50 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0">
                                    </path>
                                </svg>
                            </div>
                            <span
                                class="text-xs font-bold uppercase tracking-widest text-sky-500 bg-sky-50 dark:bg-sky-950 px-2 py-1 rounded-lg">Hotspot</span>
                        </div>
                        <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Voucher Baru</h3>
                        <div class="flex items-baseline space-x-2">
                            <span
                                class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">{{ $hotspotStats['generated_this_month'] ?? 0 }}</span>
                            <span class="text-xs font-bold text-sky-500">units</span>
                        </div>
                        <div
                            class="mt-4 pt-4 border-t border-gray-50 dark:border-gray-700/50 flex justify-between items-center text-xs">
                            <div class="flex items-center space-x-1">
                                <div class="w-2 h-2 rounded-full bg-sky-500"></div>
                                <span class="text-gray-500 font-medium">Ready: {{ $hotspotStats['active'] ?? 0 }}</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                <span class="text-gray-500 font-medium">Used:
                                    {{ $hotspotStats['used_this_month'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Chart Section -->
            <div
                class="mb-8 bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100 dark:border-gray-700/50 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3
                        class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z">
                            </path>
                        </svg>
                        Tren Pendapatan Harian (Bulan Ini)
                    </h3>
                </div>

                <div class="relative w-full h-80 pt-4" wire:ignore x-data="{
                        chart: null,
                        chartData: @js($revenueChartData),
                        init() {
                            this.$nextTick(() => {
                                this.setup();
                            });
                        },
                        setup() {
                            if (typeof Chart === 'undefined') {
                                setTimeout(() => this.setup(), 200);
                                return;
                            }
                            this.render();
                            this.observeTheme();
                        },
                        render() {
                            const ctx = this.$refs.canvas?.getContext('2d');
                            if (!ctx) return;
                            
                            if (this.chart) {
                                this.chart.destroy();
                            }

                            const isDark = document.documentElement.classList.contains('dark');
                            const textColor = isDark ? '#9ca3af' : '#6b7280';
                            const gridColor = isDark ? '#374151' : '#f3f4f6';

                            const gradient = ctx.createLinearGradient(0, 0, 0, 320);
                            gradient.addColorStop(0, 'rgba(79, 70, 229, 0.4)');
                            gradient.addColorStop(1, 'rgba(79, 70, 229, 0.05)');

                            this.chart = new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: this.chartData.labels,
                                    datasets: [{
                                        label: 'Pendapatan',
                                        data: this.chartData.data,
                                        borderColor: '#4f46e5',
                                        backgroundColor: gradient,
                                        borderWidth: 3,
                                        tension: 0.4,
                                        fill: true,
                                        pointRadius: 0,
                                        pointHoverRadius: 6
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    interaction: {
                                        intersect: false,
                                        mode: 'index',
                                    },
                                    plugins: {
                                        legend: { display: false },
                                        tooltip: {
                                            backgroundColor: isDark ? 'rgba(17, 24, 39, 0.9)' : 'rgba(255, 255, 255, 0.9)',
                                            titleColor: isDark ? '#f3f4f6' : '#111827',
                                            bodyColor: isDark ? '#d1d5db' : '#374151',
                                            borderColor: isDark ? '#374151' : '#e5e7eb',
                                            borderWidth: 1,
                                            padding: 10,
                                            displayColors: false,
                                            callbacks: {
                                                label: (context) => 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y)
                                            }
                                        }
                                    },
                                    scales: {
                                        x: {
                                            grid: { display: false, drawBorder: false },
                                            ticks: { color: textColor, font: { family: 'Inter', size: 11 } }
                                        },
                                        y: {
                                            grid: { color: gridColor, borderDash: [4, 4], drawBorder: false },
                                            ticks: {
                                                color: textColor,
                                                font: { family: 'Inter', size: 11 },
                                                callback: (value) => {
                                                    if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                                                    if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'k';
                                                    return 'Rp ' + value;
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        },
                        observeTheme() {
                            const observer = new MutationObserver(() => {
                                if (this.chart) this.render();
                            });
                            observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
                        }
                    }">
                    <canvas x-ref="canvas"></canvas>
                </div>
            </div>

            <!-- Recent Invoices Table -->
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 rounded-xl border border-gray-100 dark:border-gray-700/50">
                <div
                    class="px-6 py-4 border-b border-gray-100 dark:border-gray-700/50 flex items-center justify-between">
                    <h3
                        class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                            </path>
                        </svg>
                        Tagihan Terbaru
                    </h3>
                    <a href="{{ route('invoices.index') }}"
                        class="text-xs font-bold text-indigo-600 hover:text-indigo-500 transition-colors">Lihat Semua
                        &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700/50">
                        <thead class="bg-gray-50 dark:bg-gray-700/20">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    No. Inv</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Pelanggan</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Periode</th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Total</th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700/50">
                            @forelse($recentInvoices as $invoice)
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                                        <td
                                                            class="px-6 py-4 whitespace-nowrap text-xs font-bold text-indigo-600 dark:text-indigo-400">
                                                            {{ $invoice->invoice_number }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm font-bold text-gray-900 dark:text-white">
                                                                {{ $invoice->subscription->customer->name ?? '-' }}
                                                            </div>
                                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                                {{ $invoice->subscription->customer->customer_id ?? '-' }}
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                                            {{ \Carbon\Carbon::parse($invoice->period_start)->format('d M') }} -
                                                            {{ \Carbon\Carbon::parse($invoice->period_end)->format('d M Y') }}
                                                        </td>
                                                        <td
                                                            class="px-6 py-4 whitespace-nowrap text-xs font-bold text-gray-900 dark:text-white text-right">
                                                            Rp {{ number_format($invoice->total, 0, ',', '.') }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-bold rounded-lg 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            {{ $invoice->status === 'paid'
                                ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
                                : ($invoice->status === 'unpaid'
                                    ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400'
                                    : 'bg-gray-100 text-gray-800') }}">
                                                                {{ ucfirst($invoice->status) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                        Belum ada data tagihan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Chart.js moved to layouts/app.blade.php for reliability
        </script>
    @endpush