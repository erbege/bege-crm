<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Laporan Pelanggan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6 no-print">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <!-- Date Range (Registration) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Terdaftar Dari</label>
                        <input type="date" wire:model.live="startDate"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sampai</label>
                        <input type="date" wire:model.live="endDate"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <select wire:model.live="statusFilter"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="all">Semua Status</option>
                            <option value="active">Aktif</option>
                            <option value="suspended">Terisolir</option>
                            <option value="terminated">Dibatalkan</option>
                            <option value="pending">Menunggu</option>
                            <option value="inactive">Nonaktif</option>
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-2">
                        <button wire:click="exportExcel"
                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Excel
                        </button>
                        <button wire:click="exportPdf" wire:loading.attr="disabled"
                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg wire:loading.remove wire:target="exportPdf" class="w-4 h-4 mr-2" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                </path>
                            </svg>
                            <svg wire:loading wire:target="exportPdf" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Download PDF
                        </button>
                    </div>
                </div>
            </div>

            <!-- Report Content -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-8 print-area">

                <!-- Report Header (Print Only) -->
                <div class="hidden print:block mb-8 text-center">
                    <h1 class="text-2xl font-bold uppercase text-gray-900">Laporan Pelanggan</h1>
                    <p class="text-gray-600">Pelanggan Baru Periode:
                        {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} -
                        {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                    </p>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-100 dark:border-gray-600">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Total
                            Pelanggan</span>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $totalCustomers }}</p>
                    </div>
                    <div
                        class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-100 dark:border-green-800">
                        <span
                            class="text-[10px] font-black text-green-500 uppercase tracking-widest block mb-1">Aktif</span>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $activeCustomers }}</p>
                    </div>
                    <div
                        class="bg-amber-50 dark:bg-amber-900/20 p-4 rounded-lg border border-amber-100 dark:border-amber-800">
                        <span
                            class="text-[10px] font-black text-amber-500 uppercase tracking-widest block mb-1">Menunggu</span>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $pendingCustomers }}</p>
                    </div>
                    <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg border border-red-100 dark:border-red-800">
                        <span
                            class="text-[10px] font-black text-red-500 uppercase tracking-widest block mb-1">Terisolir</span>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $suspendedCustomers }}</p>
                    </div>
                    <div
                        class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                        <span
                            class="text-[10px] font-black text-gray-500 uppercase tracking-widest block mb-1">Dibatalkan</span>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $cancelledCustomers }}</p>
                    </div>
                    <div
                        class="bg-indigo-50 dark:bg-indigo-900/20 p-4 rounded-lg border border-indigo-100 dark:border-indigo-800">
                        <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest block mb-1">Baru
                            (Periode)</span>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $newCustomersCount }}</p>
                    </div>
                </div>

                <!-- Details Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    CID</th>
                                <th
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Nama</th>
                                <th
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Paket</th>
                                <th
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Terdaftar</th>
                                <th
                                    class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Biaya</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            @forelse($customers as $customer)
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                        {{ $customer->cid }}
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap font-medium text-gray-900 dark:text-white">
                                        {{ $customer->name }}
                                    </td>
                                    <td class="px-4 py-2 text-gray-700 dark:text-gray-300">
                                        {{ $customer->service_package_name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        <span
                                            class="px-2.5 py-0.5 inline-flex text-[10px] leading-4 font-bold rounded-lg {{ $customer->status_color }}">
                                            {{ $customer->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                        {{ $customer->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-right text-gray-900 dark:text-white">
                                        Rp {{ number_format($customer->package_price ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">Tidak ada
                                        pelanggan baru pada periode ini atau tidak sesuai filter.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            .print-area {
                box-shadow: none !important;
                padding: 0 !important;
                border: none !important;
            }

            body {
                background-color: white !important;
            }
        }
    </style>
</div>