<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <x-slot name="header">
            <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Invoice') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Kelola invoice tagihan pelanggan
            </p>
        </x-slot>

        <div
            class="bg-white dark:bg-gray-800 shadow-2xl rounded-lg overflow-hidden border border-gray-100 dark:border-gray-700/50 transition-all duration-500">
            <div class="p-6">
                <!-- Filters & Search -->
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-8">
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap gap-3">
                            <div class="relative flex-1 max-w-md">
                                <div
                                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input wire:model.live.debounce.300ms="search" type="search"
                                    placeholder="Cari No. Invoice / Nama..."
                                    class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 dark:bg-gray-900/50 border-none rounded-lg text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all duration-300 shadow-sm">
                            </div>

                            <div class="relative">
                                <select wire:model.live="filterStatus"
                                    class="block w-full pl-3 pr-10 py-2.5 bg-gray-50 dark:bg-gray-900/50 border-none rounded-lg text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all duration-300 shadow-sm cursor-pointer appearance-none">
                                    <option value="">Semua Status</option>
                                    <option value="unpaid">Belum Bayar</option>
                                    <option value="paid">Lunas</option>
                                    <option value="cancelled">Dibatalkan</option>
                                </select>
                                <div
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>

                            <div class="relative">
                                <input type="month" wire:model.live="filterMonth"
                                    class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900/50 border-none rounded-lg text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all duration-300 shadow-sm cursor-pointer">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50/50 dark:bg-gray-900/30">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-[0.15em]">
                                    No. Invoice</th>
                                <th
                                    class="px-6 py-4 text-left text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-[0.15em]">
                                    Pelanggan</th>
                                <th
                                    class="px-6 py-4 text-left text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-[0.15em]">
                                    Periode</th>
                                <th
                                    class="px-6 py-4 text-left text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-[0.15em]">
                                    Total</th>
                                <th
                                    class="px-6 py-4 text-left text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-[0.15em]">
                                    Jatuh Tempo</th>
                                <th
                                    class="px-6 py-4 text-left text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-[0.15em]">
                                    Status</th>
                                <th
                                    class="px-6 py-4 text-right text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-[0.15em]">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                            @forelse($invoices as $invoice)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $invoice->invoice_number }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $invoice->issue_date->format('d/m/Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $invoice->customer->name }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $invoice->customer->customer_id }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                            {{ $invoice->subscription->period_label ?? '-' }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $invoice->subscription->package->name ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $invoice->formatted_total }}
                                        </div>
                                        @if($invoice->installation_fee > 0)
                                            <div class="text-xs text-indigo-500 dark:text-indigo-400">
                                                + Instalasi
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div
                                            class="text-sm text-gray-900 dark:text-gray-100 {{ $invoice->is_overdue ? 'text-red-600 dark:text-red-400 font-semibold' : '' }}">
                                            {{ $invoice->due_date->format('d/m/Y') }}
                                        </div>
                                        @if($invoice->is_overdue)
                                            <div class="text-xs text-red-500 dark:text-red-400">
                                                Lewat jatuh tempo
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $colors = [
                                                'unpaid' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                                'paid' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                                'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                            ];
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colors[$invoice->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $invoice->status_label }}
                                        </span>
                                        @if($invoice->sent_at)
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                <svg class="inline w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                                Terkirim
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <!-- View Detail -->
                                            <button wire:click="viewDetail({{ $invoice->id }})" wire:loading.attr="disabled"
                                                wire:target="viewDetail({{ $invoice->id }})"
                                                class="p-1.5 text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 disabled:opacity-50 transition-colors"
                                                title="Lihat Detail">
                                                <svg wire:loading.remove wire:target="viewDetail({{ $invoice->id }})"
                                                    class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                <svg wire:loading wire:target="viewDetail({{ $invoice->id }})"
                                                    class="animate-spin w-5 h-5 text-indigo-500"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                        stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                            </button>

                                            <!-- Download PDF -->
                                            <button wire:click="downloadPdf({{ $invoice->id }})"
                                                wire:loading.attr="disabled" wire:target="downloadPdf({{ $invoice->id }})"
                                                class="p-1.5 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 disabled:opacity-50 transition-colors"
                                                title="Download PDF">
                                                <svg wire:loading.remove wire:target="downloadPdf({{ $invoice->id }})"
                                                    class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                                <svg wire:loading wire:target="downloadPdf({{ $invoice->id }})"
                                                    class="animate-spin w-5 h-5 text-red-500"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                        stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                            </button>

                                            @if($invoice->status !== 'paid')
                                                <!-- Send WhatsApp -->
                                                <button wire:click="sendWhatsApp({{ $invoice->id }})"
                                                    wire:loading.attr="disabled" wire:target="sendWhatsApp({{ $invoice->id }})"
                                                    class="p-1.5 text-gray-500 hover:text-green-600 dark:text-gray-400 dark:hover:text-green-400 disabled:opacity-50 transition-colors"
                                                    title="Kirim via WhatsApp">
                                                    <svg wire:loading.remove wire:target="sendWhatsApp({{ $invoice->id }})"
                                                        class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                        <path
                                                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                                                    </svg>
                                                    <svg wire:loading wire:target="sendWhatsApp({{ $invoice->id }})"
                                                        class="animate-spin w-5 h-5 text-green-500"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                            stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor"
                                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                        </path>
                                                    </svg>
                                                </button>
                                            @endif

                                            @if($invoice->status === 'unpaid')
                                                <!-- Mark as Paid -->
                                                <button wire:click="openPaymentModal({{ $invoice->id }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="openPaymentModal({{ $invoice->id }})"
                                                    class="p-1.5 text-gray-500 hover:text-green-600 dark:text-gray-400 dark:hover:text-green-400 disabled:opacity-50 transition-colors"
                                                    title="Tandai Lunas">
                                                    <svg wire:loading.remove wire:target="openPaymentModal({{ $invoice->id }})"
                                                        class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <svg wire:loading wire:target="openPaymentModal({{ $invoice->id }})"
                                                        class="animate-spin w-5 h-5 text-green-500"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                            stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor"
                                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                        </path>
                                                    </svg>
                                                </button>

                                                <!-- Cancel -->
                                                <button
                                                    wire:click="triggerConfirm('cancelInvoice', {{ $invoice->id }}, 'Batalkan Invoice?', 'Invoice yang dibatalkan tidak dapat dikembalikan.')"
                                                    wire:loading.attr="disabled"
                                                    wire:target="triggerConfirm('cancelInvoice', {{ $invoice->id }})"
                                                    class="p-1.5 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 disabled:opacity-50 transition-colors"
                                                    title="Batalkan">
                                                    <svg wire:loading.remove
                                                        wire:target="triggerConfirm('cancelInvoice', {{ $invoice->id }})"
                                                        class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <svg wire:loading
                                                        wire:target="triggerConfirm('cancelInvoice', {{ $invoice->id }})"
                                                        class="animate-spin w-5 h-5 text-red-500"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                            stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor"
                                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                        </path>
                                                    </svg>
                                                </button>
                                            @endif

                                            @if($invoice->status === 'paid')
                                                <!-- Rollback Payment -->
                                                <button
                                                    wire:click="triggerConfirm('rollbackPayment', {{ $invoice->id }}, 'Rollback Pembayaran', 'Status invoice akan kembali ke \'Belum Bayar\'.', true, 'Alasan Rollback', 'Masukkan alasan rollback pembayaran...')"
                                                    wire:loading.attr="disabled"
                                                    wire:target="triggerConfirm('rollbackPayment', {{ $invoice->id }})"
                                                    class="p-1.5 text-gray-500 hover:text-orange-600 dark:text-gray-400 dark:hover:text-orange-400 disabled:opacity-50 transition-colors"
                                                    title="Rollback Pembayaran">
                                                    <svg wire:loading.remove
                                                        wire:target="triggerConfirm('rollbackPayment', {{ $invoice->id }})"
                                                        class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                                    </svg>
                                                    <svg wire:loading
                                                        wire:target="triggerConfirm('rollbackPayment', {{ $invoice->id }})"
                                                        class="animate-spin w-5 h-5 text-orange-500"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                            stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor"
                                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                        </path>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="mt-4 text-lg font-medium">Tidak ada invoice</p>
                                        <p class="mt-1">Invoice akan dibuat otomatis saat subscription baru ditambahkan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $invoices->links() }}
                </div>
            </div>
        </div>

        <!-- Detail Modal -->
        <div wire:key="modal-detail" x-show="$wire.showDetailModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
            x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity"
                    @click="$wire.showDetailModal = false; $wire.closeDetailModal()">
                    <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div
                    class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100 dark:border-gray-700/50">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6">
                        <div class="flex items-center justify-between mb-8 px-2">
                            <h3 class="text-xl font-black text-gray-900 dark:text-gray-100 tracking-tight">
                                Detail Invoice
                            </h3>
                            <div class="h-1 w-20 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full"></div>
                        </div>

                        @if($selectedInvoice)
                                <div class="space-y-4">
                                    <div
                                        class="bg-gray-50/50 dark:bg-gray-900/30 rounded-lg p-6 border border-gray-100 dark:border-gray-700/50 shadow-inner">
                                        <div class="text-center mb-6">
                                            <div class="text-3xl font-black text-gray-900 dark:text-gray-100 tracking-tighter">
                                                {{ $selectedInvoice->invoice_number }}
                                            </div>
                                            <div
                                                class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 tracking-[0.2em] mt-1">
                                                Dibuat: {{ $selectedInvoice->issue_date->format('d F Y') }}
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-6 text-sm">
                                            <div class="space-y-1">
                                                <span
                                                    class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Pelanggan</span>
                                                <div class="font-bold text-gray-900 dark:text-gray-100 text-base">
                                                    {{ $selectedInvoice->customer->name }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $selectedInvoice->customer->customer_id }}
                                                </div>
                                            </div>
                                            <div class="space-y-1">
                                                <span
                                                    class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Paket
                                                    & Periode</span>
                                                <div class="font-bold text-gray-900 dark:text-gray-100">
                                                    {{ $selectedInvoice->subscription->package->name ?? '-' }}
                                                </div>
                                                <div class="text-xs text-indigo-500 font-semibold">
                                                    {{ $selectedInvoice->subscription->period_label ?? '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-2 text-sm">
                                        <!-- Subscription Fee -->
                                        <div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-gray-400">Biaya Langganan</span>
                                                <span
                                                    class="text-gray-900 dark:text-gray-100">{{ $selectedInvoice->formatted_subtotal }}</span>
                                            </div>
                                            @php
                                                $packagePrice = $selectedInvoice->subscription->package->price ?? 0;
                                                $isProrated = abs($selectedInvoice->subtotal - $packagePrice) > 1;
                                            @endphp
                                            @if($isProrated && $selectedInvoice->subtotal < $packagePrice)
                                                <span
                                                    class="block text-xs text-orange-600 dark:text-orange-400 mt-1 italic text-right">
                                                    (Prorata / Penyesuaian Harga dari Rp
                                                    {{ number_format($packagePrice, 0, ',', '.') }})
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Installation Fee -->
                                        @if($selectedInvoice->installation_fee > 0)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-gray-400">Biaya Instalasi</span>
                                                <span
                                                    class="text-gray-900 dark:text-gray-100">{{ $selectedInvoice->formatted_installation_fee }}</span>
                                            </div>
                                        @endif

                                        <!-- Tax -->
                                        @if($selectedInvoice->tax > 0)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-gray-400">Pajak</span>
                                                <span class="text-gray-900 dark:text-gray-100">Rp
                                                    {{ number_format($selectedInvoice->tax, 0, ',', '.') }}</span>
                                            </div>
                                        @endif

                                        <!-- Discount -->
                                        @if($selectedInvoice->discount > 0)
                                            <div class="flex justify-between text-red-600 dark:text-red-400">
                                                <span>Diskon</span>
                                                <span>- {{ $selectedInvoice->formatted_discount }}</span>
                                            </div>
                                        @endif

                                        <!-- Total -->
                                        <div
                                            class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-600 font-bold">
                                            <span class="text-gray-900 dark:text-gray-100">Total</span>
                                            <span
                                                class="text-indigo-600 dark:text-indigo-400 text-lg">{{ $selectedInvoice->formatted_total }}</span>
                                        </div>
                                    </div>

                                    <div
                                        class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-600">
                                        <div>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">Status:</span>
                                            @php
                                                $colors = [
                                                    'unpaid' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                                    'paid' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                                    'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                                ];
                                            @endphp
                                            <span
                                                class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colors[$selectedInvoice->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ $selectedInvoice->status_label }}
                                            </span>
                                        </div>
                                        <div class="text-sm">
                                            <span class="text-gray-500 dark:text-gray-400">Jatuh Tempo:</span>
                                            <span
                                                class="font-medium text-gray-900 dark:text-gray-100 {{ $selectedInvoice->is_overdue ? 'text-red-600 dark:text-red-400' : '' }}">
                                                {{ $selectedInvoice->due_date->format('d/m/Y') }}
                                            </span>
                                        </div>
                                    </div>

                                    @if($selectedInvoice->paid_at)
                                        <div class="text-sm text-green-600 dark:text-green-400">
                                            <span>Dibayar pada: {{ $selectedInvoice->paid_at->format('d/m/Y H:i') }}</span>
                                            @if($selectedInvoice->payment_method)
                                                <span class="ml-2">({{ ucfirst($selectedInvoice->payment_method) }})</span>
                                            @endif
                                        </div>
                                    @endif

                                    <!-- Riwayat Aktivitas -->
                                    <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Riwayat
                                            Aktivitas</h4>
                                        <div class="space-y-4">
                                            @forelse($selectedInvoice->paymentHistories as $history)
                                                <div class="flex gap-3">
                                                    <div class="flex-shrink-0 mt-1">
                                                        @php
                                                            $bulletColors = [
                                                                'payment' => 'bg-green-500',
                                                                'rollback' => 'bg-orange-500',
                                                                'cancelled' => 'bg-red-500',
                                                            ];
                                                            $bulletColor = $bulletColors[$history->action] ?? 'bg-gray-400';
                                                        @endphp
                                                        <div
                                                            class="w-2 h-2 rounded-full {{ $bulletColor }} ring-4 ring-white dark:ring-gray-800 shadow-sm">
                                                        </div>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex justify-between items-start">
                                                            <span class="text-xs font-bold text-gray-900 dark:text-gray-100">
                                                                {{ $history->action_label }}
                                                            </span>
                                                            <span class="text-[10px] text-gray-400 tabular-nums">
                                                                {{ $history->created_at->format('d/m/Y H:i') }}
                                                            </span>
                                                        </div>
                                                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                                            <span
                                                                class="font-semibold text-gray-700 dark:text-gray-300">{{ $history->user->name ?? 'System' }}</span>
                                                            @if($history->action === 'payment')
                                                                mencatat pelunasan sebesar <span
                                                                    class="text-green-600 dark:text-green-400 font-bold">{{ $history->formatted_amount }}</span>
                                                                via {{ $history->payment_method_label }}.
                                                            @elseif($history->action === 'rollback')
                                                                melakukan rollback tagihan. Alasan: <span
                                                                    class="italic">"{{ $history->notes }}"</span>
                                                            @elseif($history->action === 'cancelled')
                                                                membatalkan invoice ini.
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="text-xs text-gray-400 italic text-center py-2">Belum ada riwayat
                                                    aktivitas.</div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="px-8 py-6 bg-gray-50/50 dark:bg-gray-900/10 flex flex-col sm:flex-row-reverse gap-3 rounded-b-[2.5rem]">
                                <button wire:click="downloadPdf({{ $selectedInvoice->id }})" wire:loading.attr="disabled"
                                    wire:target="downloadPdf({{ $selectedInvoice->id }})"
                                    class="inline-flex items-center justify-center px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-widest rounded-lg shadow-lg hover:shadow-indigo-500/40 transition-all duration-300 disabled:opacity-50">
                                    <svg wire:loading wire:target="downloadPdf({{ $selectedInvoice->id }})"
                                        class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <span wire:loading.remove wire:target="downloadPdf({{ $selectedInvoice->id }})">Download
                                        PDF</span>
                                    <span wire:loading
                                        wire:target="downloadPdf({{ $selectedInvoice->id }})">Generating...</span>
                                </button>
                                <button wire:click="closeDetailModal"
                                    class="inline-flex items-center justify-center px-8 py-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs font-bold uppercase tracking-widest rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300">
                                    Tutup
                                </button>
                            </div>
                        @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div wire:key="modal-payment" x-show="$wire.showPaymentModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity"
                @click="$wire.showPaymentModal = false; $wire.closePaymentModal()">
                <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div
                class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-100 dark:border-gray-700/50">
                @if($selectedInvoice)
                    <form wire:submit="markAsPaid">
                        <div class="bg-white dark:bg-gray-800 px-8 pt-6 pb-6">
                            <div class="flex items-center justify-between mb-8">
                                <h3 class="text-xl font-black text-gray-900 dark:text-gray-100 tracking-tight">
                                    Konfirmasi Bayar
                                </h3>
                                <div class="h-1 w-16 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full"></div>
                            </div>
                            <div
                                class="mb-6 p-6 bg-gray-50/50 dark:bg-gray-900/30 rounded-lg border border-gray-100 dark:border-gray-700/50 shadow-inner text-center">
                                <div
                                    class="text-[10px] uppercase font-black text-gray-400 dark:text-gray-500 tracking-[0.2em] mb-1">
                                    Total Tagihan</div>
                                <div class="text-3xl font-black text-indigo-600 dark:text-indigo-400 tracking-tighter">
                                    {{ $selectedInvoice->formatted_total }}
                                </div>
                                <div class="text-xs text-gray-500 mt-2 font-medium">
                                    {{ $selectedInvoice->invoice_number }}
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label
                                        class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Metode
                                        Pembayaran</label>
                                    <select wire:model="paymentMethod"
                                        class="w-full bg-gray-50 dark:bg-gray-900/50 border-none rounded-lg text-sm focus:ring-2 focus:ring-indigo-500/20 shadow-sm transition-all duration-300 cursor-pointer appearance-none px-4 py-2.5">
                                        <option value="cash">Cash</option>
                                        <option value="transfer">Transfer Bank</option>
                                        <option value="e-wallet">E-Wallet</option>
                                        <option value="qris">QRIS</option>
                                    </select>
                                </div>

                                <div>
                                    <label
                                        class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">No.
                                        Referensi / Bukti Transfer</label>
                                    <input type="text" wire:model="paymentReference"
                                        class="w-full bg-gray-50 dark:bg-gray-900/50 border-none rounded-lg text-sm focus:ring-2 focus:ring-indigo-500/20 shadow-sm transition-all duration-300 px-4 py-2.5"
                                        placeholder="TRF123456789">
                                </div>

                                <div>
                                    <label
                                        class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Catatan</label>
                                    <textarea wire:model="paymentNotes" rows="2"
                                        class="w-full bg-gray-50 dark:bg-gray-900/50 border-none rounded-lg text-sm focus:ring-2 focus:ring-indigo-500/20 shadow-sm transition-all duration-300 px-4 py-2.5"
                                        placeholder="Catatan pembayaran..."></textarea>
                                </div>
                            </div>
                        </div>
                        <div
                            class="px-8 py-6 bg-gray-50/50 dark:bg-gray-900/10 flex flex-col sm:flex-row-reverse gap-3 rounded-b-[2.5rem]">
                            <button type="submit" wire:loading.attr="disabled" wire:target="markAsPaid"
                                class="inline-flex items-center justify-center px-8 py-3 bg-green-600 hover:bg-green-700 text-white text-xs font-bold uppercase tracking-widest rounded-lg shadow-lg hover:shadow-green-500/40 transition-all duration-300 disabled:opacity-50">
                                <svg wire:loading wire:target="markAsPaid"
                                    class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span wire:loading.remove wire:target="markAsPaid">Konfirmasi Lunas</span>
                                <span wire:loading wire:target="markAsPaid">Memproses...</span>
                            </button>
                            <button type="button" wire:click="closePaymentModal"
                                class="inline-flex items-center justify-center px-8 py-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs font-bold uppercase tracking-widest rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300">
                                Batal
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div wire:key="modal-confirmation" x-show="$wire.showConfirmationModal" x-cloak
    class="fixed inset-0 z-[70] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true"
    x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
            @click="$wire.showConfirmationModal = false; $wire.closeConfirmationModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div
            class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100 dark:border-gray-700/50">
            <div class="bg-white dark:bg-gray-800 px-8 pt-8 pb-6">
                <div class="flex flex-col items-center text-center">
                    <div
                        class="mx-auto flex-shrink-0 flex items-center justify-center h-16 w-16 rounded-full bg-red-50 dark:bg-red-900/20 mb-6 font-black">
                        <svg class="h-8 w-8 text-red-600 dark:text-red-400" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 dark:text-gray-100 tracking-tight mb-2">
                        {{ $confirmationTitle }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $confirmationMessage }}
                    </p>

                    @if($requiresInput)
                        <div class="mt-6 w-full text-left">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">
                                {{ $inputLabel }} <span class="text-red-500">*</span>
                            </label>
                            <textarea wire:model="confirmationInput" rows="3"
                                class="w-full bg-gray-100 dark:bg-gray-900/50 border-none rounded-lg text-sm focus:ring-2 focus:ring-indigo-500/20 shadow-sm transition-all duration-300 px-4 py-2.5"
                                placeholder="{{ $inputPlaceholder }}"></textarea>
                            @error('confirmationInput') <span
                                class="text-red-500 text-[10px] mt-1 italic">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif
                </div>
            </div>
            <div
                class="px-8 py-6 bg-gray-50/50 dark:bg-gray-900/10 flex flex-col sm:flex-row-reverse gap-3 rounded-b-[2.5rem]">
                <button type="button" wire:click="executeAction"
                    class="inline-flex items-center justify-center px-8 py-3 bg-red-600 hover:bg-red-700 text-white text-xs font-bold uppercase tracking-widest rounded-lg shadow-lg hover:shadow-red-500/40 transition-all duration-300">
                    Ya, Lanjutkan
                </button>
                <button type="button" wire:click="closeConfirmationModal"
                    class="inline-flex items-center justify-center px-8 py-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs font-bold uppercase tracking-widest rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Scripts -->
@script
<script>
    $wire.on('openWhatsApp', ({ url }) => {
        window.open(url, '_blank');
    });
</script>
@endscript
</div>