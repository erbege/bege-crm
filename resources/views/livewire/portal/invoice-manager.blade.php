<div class="space-y-6 pb-24" x-data="{ showDetailModal: false }" x-on:open-detail-modal.window="showDetailModal = true">
    <div class="flex items-center space-x-4 mb-2">
        <button onclick="history.back()"
            class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </button>
        <h2 class="text-xl font-bold dark:text-white">Riwayat Tagihan</h2>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 gap-3">
        <div
            class="bg-indigo-600 rounded-3xl p-5 text-white shadow-xl shadow-indigo-500/20 space-y-3 relative overflow-hidden">
            <div class="bg-white/20 p-2 rounded-xl w-fit">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </div>
            <div class="space-y-0.5">
                <p class="text-[10px] font-bold uppercase tracking-wider opacity-80">Total Belum Bayar</p>
                <p class="text-xl font-black">Rp
                    {{ number_format($totalUnpaid, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <div
            class="bg-white dark:bg-slate-800/50 rounded-3xl p-5 border border-slate-100 dark:border-slate-800/60 space-y-3">
            <div class="bg-indigo-100 dark:bg-indigo-500/10 p-2 rounded-xl w-fit text-indigo-600 dark:text-indigo-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                </svg>
            </div>
            <div class="space-y-0.5">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Jatuh Tempo
                    Terdekat</p>
                <p class="text-xl font-black dark:text-white">
                    {{ $nearestDue ? $nearestDue->format('d M y') : '-' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex items-center space-x-2 overflow-x-auto pb-2 -mx-4 px-4 no-scrollbar">
        <button wire:click="setStatusFilter('')"
            class="px-5 py-2 rounded-full text-sm font-bold whitespace-nowrap shadow-sm transition-all {{ $statusFilter === '' ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400' }}">
            Semua
        </button>
        <button wire:click="setStatusFilter('unpaid')"
            class="px-5 py-2 rounded-full text-sm font-bold whitespace-nowrap flex items-center transition-all {{ $statusFilter === 'unpaid' ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400' }}">
            Belum Bayar
            <span
                class="ml-2 px-1.5 py-0.5 {{ $statusFilter === 'unpaid' ? 'bg-white/20 text-white' : 'bg-rose-500/20 text-rose-500' }} text-[10px] rounded-full">
                {{ $unpaidCount }}
            </span>
        </button>
        <button wire:click="setStatusFilter('paid')"
            class="px-5 py-2 rounded-full text-sm font-bold whitespace-nowrap transition-all {{ $statusFilter === 'paid' ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400' }}">
            Lunas
        </button>
    </div>

    <!-- Invoice List -->
    <div class="relative min-h-[400px]">
        <div wire:loading.flex
            class="absolute inset-0 z-10 items-center justify-center bg-white/50 dark:bg-slate-900/50 backdrop-blur-[2px] rounded-3xl">
            <div class="flex flex-col items-center space-y-2">
                <div class="h-8 w-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Memuat...</span>
            </div>
        </div>

        <div class="space-y-4">
            @php
                $currentYear = null;
            @endphp
            @forelse($invoices as $invoice)
                @php
                    $year = $invoice->issue_date ? $invoice->issue_date->format('Y') : null;
                @endphp

                @if($year && $year !== $currentYear)
                    <div class="flex items-center space-x-4 pt-4">
                        <div class="h-px flex-1 bg-slate-100 dark:bg-slate-800"></div>
                        <span
                            class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">{{ $year }}</span>
                        <div class="h-px flex-1 bg-slate-100 dark:bg-slate-800"></div>
                    </div>
                    @php $currentYear = $year; @endphp
                @endif

                <div class="bg-white dark:bg-slate-800/50 rounded-3xl p-5 border border-slate-100 dark:border-slate-800/60 space-y-4 group active:scale-[0.98] transition-all cursor-pointer"
                    @click="showDetailModal = true" wire:click="viewDetail({{ $invoice->id }})">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center space-x-4">
                            <div
                                class="p-3 rounded-2xl bg-slate-100 dark:bg-slate-900/50 text-slate-500 dark:text-slate-400 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                @if($invoice->status === 'paid')
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                @else
                                    <svg class="h-6 w-6 text-rose-500 dark:text-rose-400" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                                    </svg>
                                @endif
                            </div>
                            <div class="space-y-1">
                                <h4 class="font-bold dark:text-white">
                                    {{ $invoice->issue_date ? $invoice->issue_date->translatedFormat('F Y') : '-' }}
                                </h4>
                                <p class="text-xs font-medium text-rose-500">Jatuh tempo:
                                    {{ $invoice->due_date ? $invoice->due_date->format('d M Y') : '-' }}
                                </p>
                            </div>
                        </div>

                        @if($invoice->status === 'unpaid')
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20 uppercase tracking-widest">
                                <span class="h-1.5 w-1.5 rounded-full bg-rose-500 mr-1.5 animate-pulse"></span>
                                Belum Bayar
                            </span>
                        @else
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-emerald-500 dark:bg-emerald-600 text-white shadow-md shadow-emerald-500/20 border border-emerald-400 dark:border-emerald-500 uppercase tracking-widest pl-2">
                                <svg class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="3"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                                Lunas
                            </span>
                        @endif
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div class="space-y-0.5">
                            <p class="text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500 tracking-wider">
                                Total
                                Tagihan</p>
                            <p class="text-lg font-black dark:text-white">Rp
                                {{ number_format($invoice->total, 0, ',', '.') }}
                            </p>
                        </div>

                        @if($invoice->status === 'unpaid')
                            <button wire:click.stop="payInvoice({{ $invoice->id }})"
                                class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-indigo-500/30 transition-all">
                                Bayar Sekarang
                            </button>
                        @else
                            <button
                                class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-900/50 text-slate-500 dark:text-slate-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-12 text-center space-y-3">
                    <div
                        class="bg-slate-100 dark:bg-slate-800/50 w-20 h-20 rounded-full flex items-center justify-center mx-auto text-slate-400">
                        <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                    </div>
                    <p class="text-slate-500 dark:text-slate-400 font-medium">Tidak ada tagihan lainnya</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Detail Modal -->
    <div wire:key="modal-detail" x-show="showDetailModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-end justify-center min-h-screen pt-4 px-0 pb-0 text-center sm:block">
            <div class="fixed inset-0 transition-opacity" @click="showDetailModal = false; $wire.closeDetailModal()">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            </div>

            <div
                class="relative inline-block align-bottom bg-white dark:bg-slate-800 rounded-t-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all w-full max-w-lg border-t border-slate-100 dark:border-slate-700/50">
                <div class="px-6 pt-8 pb-12">
                    <div class="w-12 h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-8"></div>

                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-xl font-bold dark:text-white">Detail Tagihan</h3>
                        @if($selectedInvoice)
                            @if($selectedInvoice->status === 'paid')
                                <span
                                    class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black bg-emerald-500 dark:bg-emerald-600 text-white shadow-md shadow-emerald-500/20 uppercase tracking-widest">
                                    <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke-width="3"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                    {{ $selectedInvoice->status_label ?? 'LUNAS' }}
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-rose-100 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20 uppercase tracking-widest">
                                    <span class="h-1.5 w-1.5 rounded-full bg-rose-500 mr-2 animate-pulse"></span>
                                    {{ $selectedInvoice->status_label ?? 'BELUM BAYAR' }}
                                </span>
                            @endif
                        @endif
                    </div>

                    @if($selectedInvoice)
                        <div class="space-y-6">
                            <div
                                class="bg-slate-50 dark:bg-slate-900/50 rounded-3xl p-6 border border-slate-100 dark:border-slate-800/60">
                                <div class="text-center mb-6">
                                    <div class="text-2xl font-black dark:text-white tracking-tight">
                                        {{ $selectedInvoice->invoice_number }}
                                    </div>
                                    <div
                                        class="text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500 tracking-[0.2em] mt-1">
                                        Issued:
                                        {{ $selectedInvoice->issue_date ? $selectedInvoice->issue_date->format('d F Y') : '-' }}
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-6 text-sm">
                                    <div class="space-y-1">
                                        <span
                                            class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest block">Pelanggan</span>
                                        <div class="font-bold dark:text-white">{{ auth('customer')->user()->name }}</div>
                                    </div>
                                    <div class="space-y-1">
                                        <span
                                            class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest block">Paket</span>
                                        <div class="font-bold dark:text-white">
                                            {{ $selectedInvoice->subscription->package->name ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4 px-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-500 dark:text-slate-400">Biaya Langganan</span>
                                    <span class="font-bold dark:text-white">Rp
                                        {{ number_format($selectedInvoice->total, 0, ',', '.') }}</span>
                                </div>
                                <div class="h-px bg-slate-100 dark:bg-slate-800"></div>
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold dark:text-white">Total</span>
                                    <span class="text-2xl font-black text-indigo-600 dark:text-indigo-400">Rp
                                        {{ number_format($selectedInvoice->total, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="pt-6 space-y-3">
                                @if($selectedInvoice->status === 'unpaid')
                                    <button wire:click="payInvoice({{ $selectedInvoice->id }})"
                                        class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl shadow-xl shadow-indigo-500/30 transition-all">
                                        Bayar Sekarang
                                    </button>
                                @endif
                                <button @click="showDetailModal = false; $wire.closeDetailModal()"
                                    class="w-full py-4 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold rounded-2xl transition-all">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>