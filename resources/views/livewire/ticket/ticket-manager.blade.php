<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                </path>
            </svg>
            {{ __('Manajemen Tiket') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Stats & Filters -->
            <div class="mb-6 grid grid-cols-2 md:grid-cols-5 gap-4">
                <button wire:click="setStatusFilter('')" wire:loading.attr="disabled"
                    class="flex flex-col items-center justify-center p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border {{ $statusFilter === '' ? 'border-indigo-500 ring-1 ring-indigo-500' : 'border-gray-100 dark:border-gray-700/50 hover:border-indigo-300 dark:hover:border-indigo-700' }} transition-all relative group overflow-hidden">
                    <div wire:loading wire:target="setStatusFilter('')"
                        class="absolute inset-0 bg-white/50 dark:bg-gray-800/50 flex items-center justify-center z-10">
                        <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>
                    <span
                        class="text-2xl font-black text-gray-900 dark:text-gray-100">{{ $openTicketsCount + $inProgressTicketsCount + $resolvedTicketsCount + $closedTicketsCount }}</span>
                    <span
                        class="text-xs font-medium text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider">Semua
                        Tiket</span>
                </button>
                <button wire:click="setStatusFilter('open')" wire:loading.attr="disabled"
                    class="flex flex-col items-center justify-center p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border {{ $statusFilter === 'open' ? 'border-indigo-500 ring-1 ring-indigo-500' : 'border-gray-100 dark:border-gray-700/50 hover:border-indigo-300 dark:hover:border-indigo-700' }} transition-all relative group overflow-hidden">
                    <div wire:loading wire:target="setStatusFilter('open')"
                        class="absolute inset-0 bg-white/50 dark:bg-gray-800/50 flex items-center justify-center z-10">
                        <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>
                    <span class="text-2xl font-black text-gray-900 dark:text-gray-100">{{ $openTicketsCount }}</span>
                    <span
                        class="text-xs font-medium text-indigo-500 dark:text-indigo-400 mt-1 uppercase tracking-wider flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="10" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"></circle>
                        </svg>
                        Baru (Open)
                    </span>
                </button>
                <button wire:click="setStatusFilter('in_progress')" wire:loading.attr="disabled"
                    class="flex flex-col items-center justify-center p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border {{ $statusFilter === 'in_progress' ? 'border-indigo-500 ring-1 ring-indigo-500' : 'border-gray-100 dark:border-gray-700/50 hover:border-indigo-300 dark:hover:border-indigo-700' }} transition-all relative group overflow-hidden">
                    <div wire:loading wire:target="setStatusFilter('in_progress')"
                        class="absolute inset-0 bg-white/50 dark:bg-gray-800/50 flex items-center justify-center z-10">
                        <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>
                    <span
                        class="text-2xl font-black text-gray-900 dark:text-gray-100">{{ $inProgressTicketsCount }}</span>
                    <span
                        class="text-xs font-medium text-yellow-500 mt-1 uppercase tracking-wider flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="10" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"></circle>
                            <polyline points="12 6 12 12 16 14" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"></polyline>
                        </svg>
                        Diproses
                    </span>
                </button>
                <button wire:click="setStatusFilter('resolved')" wire:loading.attr="disabled"
                    class="flex flex-col items-center justify-center p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border {{ $statusFilter === 'resolved' ? 'border-indigo-500 ring-1 ring-indigo-500' : 'border-gray-100 dark:border-gray-700/50 hover:border-indigo-300 dark:hover:border-indigo-700' }} transition-all relative group overflow-hidden">
                    <div wire:loading wire:target="setStatusFilter('resolved')"
                        class="absolute inset-0 bg-white/50 dark:bg-gray-800/50 flex items-center justify-center z-10">
                        <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>
                    <span
                        class="text-2xl font-black text-gray-900 dark:text-gray-100">{{ $resolvedTicketsCount }}</span>
                    <span
                        class="text-xs font-medium text-green-500 mt-1 uppercase tracking-wider flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Selesai
                    </span>
                </button>
                <button wire:click="setStatusFilter('closed')" wire:loading.attr="disabled"
                    class="flex flex-col items-center justify-center p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border {{ $statusFilter === 'closed' ? 'border-indigo-500 ring-1 ring-indigo-500' : 'border-gray-100 dark:border-gray-700/50 hover:border-indigo-300 dark:hover:border-indigo-700' }} transition-all relative group overflow-hidden">
                    <div wire:loading wire:target="setStatusFilter('closed')"
                        class="absolute inset-0 bg-white/50 dark:bg-gray-800/50 flex items-center justify-center z-10">
                        <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>
                    <span class="text-2xl font-black text-gray-900 dark:text-gray-100">{{ $closedTicketsCount }}</span>
                    <span
                        class="text-xs font-medium text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4">
                            </path>
                        </svg>
                        Ditutup
                    </span>
                </button>
            </div>


            <!-- Search Bar -->
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 rounded-xl border border-gray-100 dark:border-gray-700/50 mb-6">
                <div
                    class="px-6 py-4 border-b border-gray-100 dark:border-gray-700/50 flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="relative w-full md:w-1/3">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Cari ID tiket, subjek, atau pelanggan..."
                            class="pl-10 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm text-sm">
                    </div>

                    <button @click="$dispatch('open-create-modal')"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-lg shadow-indigo-500/30">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        Buat Tiket Baru
                    </button>
                </div>

                <!-- Ticket Table -->
                <div class="overflow-x-auto relative">
                    <div wire:loading wire:target="setStatusFilter, search"
                        class="absolute inset-0 bg-white/40 dark:bg-gray-800/40 backdrop-blur-[1px] z-10 flex items-center justify-center">
                        <div class="flex flex-col items-center">
                            <svg class="animate-spin h-8 w-8 text-indigo-600 mb-2" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span
                                class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-widest">Memuat
                                Data...</span>
                        </div>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700/50">
                        <thead class="bg-gray-50 dark:bg-gray-700/20">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    ID Tiket</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Pelanggan</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Subjek / Kategori</th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Status</th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Prioritas</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Tgl Dibuat</th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700/50">
                            @forelse($tickets as $ticket)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-indigo-600 dark:text-indigo-400">
                                            #{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div
                                                class="h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-600 dark:text-indigo-300 font-bold text-xs shrink-0">
                                                {{ substr($ticket->customer->name ?? '?', 0, 2) }}
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-bold text-gray-900 dark:text-white line-clamp-1">
                                                    {{ $ticket->customer->name ?? 'Pelanggan Dihapus' }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $ticket->customer->customer_id ?? '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900 dark:text-white line-clamp-1">
                                            {{ $ticket->subject }}
                                        </div>
                                        <div
                                            class="text-xs text-gray-500 dark:text-gray-400 mt-1 inline-flex bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-md">
                                            {{ $ticket->category->name ?? 'Umum' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($ticket->status === 'open')
                                            <span
                                                class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-lg bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-400 shadow-sm border border-indigo-200 dark:border-indigo-800">Baru</span>
                                        @elseif($ticket->status === 'in_progress')
                                            <span
                                                class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-lg bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-400 shadow-sm border border-yellow-200 dark:border-yellow-800">Proses</span>
                                        @elseif($ticket->status === 'resolved')
                                            <span
                                                class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-lg bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-400 shadow-sm border border-green-200 dark:border-green-800">Selesai</span>
                                        @elseif($ticket->status === 'closed')
                                            <span
                                                class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-lg bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 shadow-sm border border-gray-200 dark:border-gray-600">Ditutup</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($ticket->priority === 'low')
                                            <span
                                                class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-lg bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 shadow-sm border border-gray-200 dark:border-gray-600">Low</span>
                                        @elseif($ticket->priority === 'medium')
                                            <span
                                                class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-lg bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-400 shadow-sm border border-blue-200 dark:border-blue-800">Medium</span>
                                        @elseif($ticket->priority === 'high')
                                            <span
                                                class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-lg bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-400 shadow-sm border border-orange-200 dark:border-orange-800">High</span>
                                        @elseif($ticket->priority === 'critical')
                                            <span
                                                class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-lg bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-400 shadow-sm border border-red-200 dark:border-red-800 animate-pulse">Critical</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                        <div class="font-medium text-gray-900 dark:text-gray-200">
                                            {{ $ticket->created_at->format('d M Y') }}
                                        </div>
                                        <div>{{ $ticket->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button wire:click="viewTicket({{ $ticket->id }})"
                                            class="inline-flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-xs font-semibold text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                            Lihat
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-full mb-4">
                                                <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                                    </path>
                                                </svg>
                                            </div>
                                            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1">Tiket Tidak
                                                Ditemukan</h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada tiket yang sesuai
                                                dengan filter/pencarian.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700/50">
                    {{ $tickets->links() }}
                </div>
            </div>

        </div>
    </div>


    <!-- View Ticket Modal (Sliding Panel Style) -->
    <div x-data="{ open: false }" x-show="open" @open-detail-modal.window="open = true"
        @close-modals.window="open = false" @keydown.escape.window="open = false; $wire.closeTicket()" x-cloak
        class="fixed inset-0 z-50 overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">

        <div class="absolute inset-0 overflow-hidden">
            <div x-show="open" x-transition:enter="ease-in-out duration-500" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-500"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="absolute inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity"
                @click="open = false; $wire.closeTicket()"></div>

            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                <div x-show="open" x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
                    x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
                    x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                    class="pointer-events-auto w-screen max-w-3xl">

                    <div class="flex h-full flex-col bg-gray-50 dark:bg-gray-900 shadow-xl">

                        @if($selectedTicket)
                            <!-- Header -->
                            <div
                                class="bg-white dark:bg-gray-800 px-6 py-6 sm:px-8 border-b border-gray-100 dark:border-gray-700 shadow-sm z-10 shrink-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <h2 class="text-xl font-bold text-gray-900 dark:text-white"
                                                id="slide-over-title">
                                                #{{ str_pad($selectedTicket->id, 5, '0', STR_PAD_LEFT) }} -
                                                {{ $selectedTicket->subject }}
                                            </h2>
                                            @if($selectedTicket->status === 'open')
                                                <span
                                                    class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-bold rounded-lg bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-400">Baru</span>
                                            @elseif($selectedTicket->status === 'in_progress')
                                                <span
                                                    class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-bold rounded-lg bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-400">Diproses</span>
                                            @elseif($selectedTicket->status === 'resolved')
                                                <span
                                                    class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-bold rounded-lg bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-400">Selesai</span>
                                            @elseif($selectedTicket->status === 'closed')
                                                <span
                                                    class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-bold rounded-lg bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Ditutup</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                                            Kategori: <span
                                                class="text-gray-900 dark:text-gray-200">{{ $selectedTicket->category->name ?? 'Umum' }}</span>
                                            •
                                            Pelanggan: <span
                                                class="text-gray-900 dark:text-gray-200">{{ $selectedTicket->customer->name ?? 'User' }}</span>
                                            •
                                            Dibuat: {{ $selectedTicket->created_at->format('d M Y H:i') }}
                                        </p>
                                    </div>
                                    <div class="ml-3 flex h-7 items-center gap-2">
                                        <!-- Controls -->
                                        <div class="flex bg-gray-100 dark:bg-gray-700/50 p-1 rounded-lg">
                                            <select
                                                wire:change="updateStatus({{ $selectedTicket->id }}, $event.target.value)"
                                                class="text-xs border-transparent bg-transparent dark:text-gray-200 py-1 pl-2 pr-6 focus:ring-0 cursor-pointer font-semibold">
                                                <option value="open" {{ $selectedTicket->status == 'open' ? 'selected' : '' }}>Open</option>
                                                <option value="in_progress" {{ $selectedTicket->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="resolved" {{ $selectedTicket->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                                <option value="closed" {{ $selectedTicket->status == 'closed' ? 'selected' : '' }}>Closed</option>
                                            </select>
                                        </div>
                                        <div class="flex bg-gray-100 dark:bg-gray-700/50 p-1 rounded-lg mr-2">
                                            <select
                                                wire:change="updatePriority({{ $selectedTicket->id }}, $event.target.value)"
                                                class="text-xs border-transparent bg-transparent dark:text-gray-200 py-1 pl-2 pr-6 focus:ring-0 cursor-pointer font-semibold">
                                                <option value="low" {{ $selectedTicket->priority == 'low' ? 'selected' : '' }}>Low</option>
                                                <option value="medium" {{ $selectedTicket->priority == 'medium' ? 'selected' : '' }}>Medium</option>
                                                <option value="high" {{ $selectedTicket->priority == 'high' ? 'selected' : '' }}>High</option>
                                                <option value="critical" {{ $selectedTicket->priority == 'critical' ? 'selected' : '' }}>Critical</option>
                                            </select>
                                        </div>

                                        <button type="button" wire:click="refreshTicket"
                                            class="relative rounded-md text-gray-400 hover:text-indigo-500 focus:outline-none dark:hover:text-indigo-400 mr-2"
                                            title="Refresh/Reload Data" wire:loading.attr="disabled"
                                            wire:target="refreshTicket">
                                            <span class="sr-only">Refresh</span>
                                            <svg wire:loading.remove wire:target="refreshTicket" class="h-6 w-6" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                            </svg>
                                            <svg wire:loading wire:target="refreshTicket"
                                                class="animate-spin h-6 w-6 text-indigo-500"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                        </button>

                                        <button type="button"
                                            class="relative rounded-md text-gray-400 hover:text-gray-500 focus:outline-none dark:hover:text-gray-300"
                                            @click="open = false; $wire.closeTicket()">
                                            <span class="absolute -inset-2.5"></span>
                                            <span class="sr-only">Tutup</span>
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Chat History -->
                            <div class="relative flex-1 px-4 sm:px-6 py-6 overflow-y-auto" x-ref="chatContainer" x-init="
                                        const scroll = () => { $el.scrollTop = $el.scrollHeight };
                                        $watch('open', value => { if(value) { setTimeout(scroll, 50); setTimeout(scroll, 300); } });
                                        new MutationObserver(() => { setTimeout(scroll, 50) }).observe($el, { childList: true, subtree: true });
                                     ">
                                <div class="space-y-6">
                                    @foreach($selectedTicket->messages as $msg)
                                        @if($msg->customer_id)
                                            <!-- Customer Message (Left) -->
                                            <div class="flex items-end gap-3 max-w-2xl">
                                                <div class="flex-shrink-0">
                                                    <div
                                                        class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center font-bold text-gray-600 dark:text-gray-300">
                                                        {{ substr($msg->customer->name ?? 'C', 0, 1) }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <span
                                                            class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ $msg->customer->name ?? 'Pelanggan' }}</span>
                                                        <span
                                                            class="text-xs text-gray-500">{{ $msg->created_at->format('d M H:i') }}</span>
                                                    </div>
                                                    <div
                                                        class="px-5 py-3 rounded-2xl rounded-bl-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm border border-gray-100 dark:border-gray-700">
                                                        <p class="text-sm leading-relaxed whitespace-pre-wrap">{{ $msg->message }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <!-- Admin Message (Right) -->
                                            <div class="flex items-end gap-3 justify-end ml-auto max-w-2xl">
                                                <div class="text-right">
                                                    <div class="flex items-center gap-2 justify-end mb-1">
                                                        <span
                                                            class="text-xs text-gray-500">{{ $msg->created_at->format('d M H:i') }}</span>
                                                        <span
                                                            class="text-xs font-bold text-indigo-600 dark:text-indigo-400">{{ $msg->user->name ?? 'Admin' }}</span>
                                                    </div>
                                                    <div
                                                        class="px-5 py-3 rounded-2xl rounded-br-sm bg-indigo-600 text-white shadow-sm border border-indigo-500 text-left">
                                                        <p class="text-sm leading-relaxed inline-block whitespace-pre-wrap">
                                                            {{ $msg->message }}</p>
                                                    </div>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <div
                                                        class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center font-bold text-indigo-600 dark:text-indigo-300">
                                                        {{ substr($msg->user->name ?? 'A', 0, 1) }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            <!-- Reply Box -->
                            <div
                                class="bg-white dark:bg-gray-800 px-4 py-4 sm:px-6 border-t border-gray-100 dark:border-gray-700 shrink-0">
                                @if($selectedTicket->status !== 'closed')
                                    <form wire:submit.prevent="sendReply" class="flex items-end gap-3 relative">
                                        <div class="relative flex-grow">
                                            <textarea wire:model="replyMessage" rows="2"
                                                class="block w-full rounded-2xl border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm pl-4 pr-12 py-3 resize-none transition-all"
                                                placeholder="Tulis balasan..."></textarea>
                                        </div>
                                        <button type="submit"
                                            class="inline-flex items-center justify-center h-12 w-12 border border-transparent rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-900 transition-transform active:scale-95 flex-shrink-0"
                                            wire:loading.attr="disabled" wire:target="sendReply">
                                            <span wire:loading.remove wire:target="sendReply">
                                                <svg class="h-5 w-5 transform rotate-90 ml-1" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                                </svg>
                                            </span>
                                            <svg wire:loading wire:target="sendReply" class="animate-spin h-5 w-5 text-white"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                        </button>
                                    </form>
                                    @error('replyMessage') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                    @enderror
                                @else
                                    <div
                                        class="text-center py-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-700">
                                        <p
                                            class="text-sm font-bold text-gray-500 dark:text-gray-400 flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                                </path>
                                            </svg>
                                            Tiket ini telah ditutup.
                                        </p>
                                        <p class="text-xs text-gray-400 mt-1">Ubah status tiket jika ingin melanjutkan
                                            percakapan.</p>
                                    </div>
                                @endif
                            </div>

                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Create Ticket Modal -->
    <div x-data="{ open: false }" x-show="open" @open-create-modal.window="open = true"
        @close-modals.window="open = false" @keydown.escape.window="open = false" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="open = false"
                class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity"
                aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="open" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100 dark:border-gray-700">

                <form wire:submit.prevent="createTicket">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white mb-6"
                                    id="modal-title">
                                    Buat Tiket Baru
                                </h3>

                                <div class="space-y-4">
                                    <!-- Customer Selection -->
                                    <div class="relative">
                                        <label
                                            class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Cari
                                            Pelanggan</label>
                                        <div class="relative">
                                            <input type="text" wire:model.live.debounce.300ms="customerSearch"
                                                class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm text-sm"
                                                placeholder="Ketik nama atau ID pelanggan...">

                                            @if(!empty($customersList))
                                                <div
                                                    class="absolute z-20 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg overflow-hidden max-h-48 overflow-y-auto">
                                                    @foreach($customersList as $cust)
                                                        <button type="button"
                                                            wire:click="selectCustomer({{ $cust['id'] }}, '{{ $cust['name'] }}')"
                                                            class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300 transition-colors">
                                                            <div class="font-bold">{{ $cust['name'] }}</div>
                                                            <div class="text-xs text-gray-500">{{ $cust['customer_id'] }}</div>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        @error('customerId') <span
                                        class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- Category -->
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Kategori</label>
                                        <select wire:model="categoryId"
                                            class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm text-sm">
                                            <option value="">Pilih Kategori</option>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('categoryId') <span
                                        class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- Subject -->
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Subjek</label>
                                        <input type="text" wire:model="subject"
                                            class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm text-sm"
                                            placeholder="Contoh: Gangguan Koneksi">
                                        @error('subject') <span
                                        class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- Message -->
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Pesan
                                            / Keluhan</label>
                                        <textarea wire:model="message" rows="4"
                                            class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm text-sm"
                                            placeholder="Jelaskan detail kendala pelanggan..."></textarea>
                                        @error('message') <span
                                        class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/30 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-bold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 transition-all"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="createTicket">Buat Tiket</span>
                            <span wire:loading wire:target="createTicket">Sabar ya...</span>
                        </button>
                        <button type="button" @click="open = false"
                            class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>