<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Laporan Keuangan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6 no-print">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <!-- Date Range -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dari Tanggal</label>
                        <input type="date" wire:model.live="startDate"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sampai Tanggal</label>
                        <input type="date" wire:model.live="endDate"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>

                    <!-- Report Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipe Laporan</label>
                        <select wire:model.live="reportType"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="all">Semua</option>
                            <option value="subscription">Langganan (Invoice)</option>
                            <option value="hotspot">Hotspot (Voucher)</option>
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
                            <svg wire:loading.remove wire:target="exportPdf" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                </path>
                            </svg>
                            <svg wire:loading wire:target="exportPdf" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
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
                    <h1 class="text-2xl font-bold uppercase text-gray-900">Laporan Keuangan</h1>
                    <p class="text-gray-600">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} -
                        {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div
                        class="bg-indigo-50 dark:bg-indigo-900/20 p-4 rounded-lg border border-indigo-100 dark:border-indigo-800">
                        <span class="text-sm font-medium text-indigo-500 uppercase">Total Pendapatan</span>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">Rp
                            {{ number_format($totalIncome, 0, ',', '.') }}</p>
                    </div>
                    @if($reportType === 'all' || $reportType === 'subscription')
                        <div
                            class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-100 dark:border-blue-800">
                            <span class="text-sm font-medium text-blue-500 uppercase">Langganan</span>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">Rp
                                {{ number_format($subscriptionIncome, 0, ',', '.') }}</p>
                        </div>
                    @endif
                    @if($reportType === 'all' || $reportType === 'hotspot')
                        <div
                            class="bg-orange-50 dark:bg-orange-900/20 p-4 rounded-lg border border-orange-100 dark:border-orange-800">
                            <span class="text-sm font-medium text-orange-500 uppercase">Hotspot</span>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">Rp
                                {{ number_format($hotspotIncome, 0, ',', '.') }}</p>
                        </div>
                    @endif
                </div>

                <!-- Details Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Tanggal</th>
                                <th
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Tipe</th>
                                <th
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Deskripsi</th>
                                <th
                                    class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            @forelse($transactions as $trx)
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                        {{ $trx['date'] }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $trx['type'] === 'Subscription' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800' }}">
                                            {{ $trx['type'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-gray-700 dark:text-gray-300">{{ $trx['description'] }}</td>
                                    <td
                                        class="px-4 py-2 whitespace-nowrap text-right font-medium text-gray-900 dark:text-white">
                                        Rp {{ number_format($trx['amount'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">Tidak ada
                                        data untuk periode ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="font-bold border-t border-gray-200 dark:border-gray-700">
                                <td colspan="3" class="px-4 py-3 text-right text-gray-900 dark:text-white text-base">
                                    Grand Total</td>
                                <td class="px-4 py-3 text-right text-gray-900 dark:text-white text-base">
                                    Rp {{ number_format($totalIncome, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
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
            }

            body {
                background-color: white !important;
            }

            .min-h-screen {
                background-color: white !important;
            }
        }
    </style>
</div>