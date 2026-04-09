<div class="py-6" x-data="{ showFormModal: false, showDeleteModal: false }" @open-modal.window="showFormModal = true"
    @open-delete-modal.window="showDeleteModal = true"
    @close-modal.window="showFormModal = false; showDeleteModal = false;">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Master Data OLT') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Kelola perangkat OLT (Optical Line Terminal) ZTE atau Huawei
            </p>
        </x-slot>

        <div
            class="bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700/50 transition-all duration-500">
            <div class="p-6">
                <!-- Actions Bar -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-col sm:flex-row gap-3">
                            <div class="relative flex-1 max-w-md">
                                <div
                                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input wire:model.live.debounce.300ms="search" type="search"
                                    placeholder="Cari Nama, Brand atau IP..."
                                    class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 dark:bg-gray-900/50 border-gray-100 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 shadow-sm">
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 flex-shrink-0">
                        <a href="{{ route('master-data.script-templates') }}"
                            class="inline-flex items-center justify-center px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-bold uppercase tracking-widest rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300 gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Template Script</span>
                        </a>
                        <button @click="showFormModal = true; $wire.openModal()"
                            class="inline-flex items-center justify-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-widest rounded-xl shadow-lg hover:shadow-indigo-500/40 transition-all duration-300 gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Tambah OLT</span>
                        </button>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-700/50">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700/50">
                        <thead class="bg-gray-50/50 dark:bg-gray-900/40">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Nama / Identitas</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Informasi Koneksi</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Brand</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Deskripsi</th>
                                <th
                                    class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700/50">
                            @forelse($olts as $olt)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors group">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="h-10 w-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center text-indigo-600 dark:text-indigo-400 group-hover:scale-105 transition-transform duration-300">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                                                </svg>
                                            </div>
                                            <div>
                                                <div
                                                    class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                    {{ $olt->name }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 font-mono">
                                                    ID: #OLT-{{ str_pad($olt->id, 3, '0', STR_PAD_LEFT) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col gap-1">
                                            <div class="flex items-center gap-2">
                                                <span class="text-[10px] font-bold text-gray-400 uppercase w-8">IP</span>
                                                <code
                                                    class="text-xs font-mono text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700/50 px-1.5 py-0.5 rounded">{{ $olt->ip_address }}</code>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-[10px] font-bold text-gray-400 uppercase w-8">Port</span>
                                                <code
                                                    class="text-xs font-mono text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700/50 px-1.5 py-0.5 rounded">{{ $olt->port }}</code>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-[10px] font-bold text-gray-400 uppercase w-8">SNMP</span>
                                                <div class="flex flex-col gap-1">
                                                    <div class="flex items-center gap-1.5 min-w-0">
                                                        <div
                                                            class="p-1.5 rounded-lg bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600/50 group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/30 group-hover:border-indigo-100 dark:group-hover:border-indigo-800 transition-all duration-300">
                                                            <svg class="w-3.5 h-3.5 text-gray-400 group-hover:text-indigo-500 transition-colors"
                                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                                            </svg>
                                                        </div>
                                                        <div class="flex flex-col min-w-0">
                                                            <span
                                                                class="text-xs font-bold text-gray-800 dark:text-gray-200 truncate">{{ $olt->ip_address }}</span>
                                                            <span
                                                                class="text-[10px] text-gray-500 dark:text-gray-400 font-medium">Port:
                                                                {{ $olt->snmp_port ?? 161 }}</span>
                                                        </div>
                                                    </div>
                                                    <code
                                                        class="text-xs font-mono text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700/50 px-1.5 py-0.5 rounded">{{ $olt->snmp_version }}</code>
                                                    <code
                                                        class="text-xs font-mono text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700/50 px-1.5 py-0.5 rounded"
                                                        title="Read Community">R: {{ $olt->snmp_community_read ?: '-' }}</code>
                                                    <code
                                                        class="text-xs font-mono text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700/50 px-1.5 py-0.5 rounded"
                                                        title="Write Community">W: {{ $olt->snmp_community_write ?: '-' }}</code>
                                                </div>
                                            </div>

                                            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700/50">
                                                @if(isset($oltStatuses[$olt->id]))
                                                    @if($oltStatuses[$olt->id]['status'] === 'Online')
                                                        <div class="flex flex-col gap-1.5">
                                                            <span
                                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-widest bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800/50 w-fit">
                                                                <span
                                                                    class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                                Online
                                                            </span>
                                                            <div class="text-[10px] text-gray-500 dark:text-gray-400 font-medium">
                                                                <span class="block">Uptime: <span
                                                                        class="text-gray-700 dark:text-gray-300 font-bold">{{ $oltStatuses[$olt->id]['uptime'] }}</span></span>
                                                                <span class="block truncate max-w-[150px]"
                                                                    title="{{ $oltStatuses[$olt->id]['name'] }}">Sys: <span
                                                                        class="text-gray-700 dark:text-gray-300 font-bold">{{ $oltStatuses[$olt->id]['name'] }}</span></span>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="flex flex-col gap-1.5">
                                                            <span
                                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-widest bg-red-50 text-red-700 border border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800/50 w-fit">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                                Offline / Error
                                                            </span>
                                                            <span
                                                                class="text-[10px] font-medium {{ str_contains($oltStatuses[$olt->id]['error'] ?? '', 'extension') ? 'text-amber-600 dark:text-amber-400 font-bold' : 'text-red-500 dark:text-red-400' }} mt-1"
                                                                title="{{ $oltStatuses[$olt->id]['error'] }}">
                                                                @if(str_contains($oltStatuses[$olt->id]['error'] ?? '', 'extension'))
                                                                    <span class="flex items-center gap-1">
                                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                                            stroke="currentColor">
                                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                                        </svg>
                                                                        PHP SNMP Extension Tidak Aktif
                                                                    </span>
                                                                @else
                                                                    {{ $oltStatuses[$olt->id]['error'] }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                    @endif
                                                @else
                                                    <button wire:click="checkOltStatus({{ $olt->id }})"
                                                        wire:loading.attr="disabled"
                                                        class="group flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-[10px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 hover:border-indigo-200 dark:hover:border-indigo-800/50 transition-all shadow-sm w-fit">
                                                        <svg wire:loading.class="animate-spin"
                                                            wire:target="checkOltStatus({{ $olt->id }})"
                                                            class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                        </svg>
                                                        Cek Status OLT
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $olt->brand === 'zte' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 border border-blue-200 dark:border-blue-800/50' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800/50' }}">
                                            {{ strtoupper($olt->brand) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <p class="text-xs text-gray-500 dark:text-gray-400 max-w-[200px] truncate"
                                            title="{{ $olt->description }}">
                                            {{ $olt->description ?: '-' }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <button wire:click="monitorOnts({{ $olt->id }})" wire:loading.attr="disabled"
                                                wire:target="monitorOnts({{ $olt->id }})"
                                                class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg transition-all disabled:opacity-50"
                                                title="Monitor Semua ONT">
                                                <svg wire:loading.remove wire:target="monitorOnts({{ $olt->id }})"
                                                    class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                <svg wire:loading wire:target="monitorOnts({{ $olt->id }})"
                                                    class="animate-spin w-4 h-4 text-indigo-600"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                        stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                            </button>
                                            <button @click="showFormModal = true; $wire.editOlt({{ $olt->id }})"
                                                class="p-2 text-gray-400 hover:text-amber-500 hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg transition-all"
                                                title="Edit">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button @click="showDeleteModal = true; $wire.confirmDelete({{ $olt->id }})"
                                                class="p-2 text-gray-400 hover:text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg transition-all"
                                                title="Hapus">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center gap-3">
                                            <div
                                                class="h-12 w-12 bg-gray-100 dark:bg-gray-800 rounded-xl flex items-center justify-center text-gray-400">
                                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                                                </svg>
                                            </div>
                                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Belum ada data
                                                OLT.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($olts->hasPages())
                    <div class="mt-6">
                        {{ $olts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div x-show="showFormModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" @click="showFormModal = false; $wire.closeModal()">
                <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-gray-100 dark:border-gray-700/50">
                <form wire:submit="save">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                            {{ $editMode ? 'Update Perangkat OLT' : 'Tambah OLT Baru' }}
                        </h3>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label
                                    class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Nama
                                    Perangkat</label>
                                <input type="text" wire:model="name" placeholder="OLT Pusat ZTE"
                                    class="block w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-span-2 sm:col-span-1">
                                <label
                                    class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Host/IP
                                    OLT</label>
                                <input type="text" wire:model="ip_address" placeholder="10.10.10.1"
                                    class="block w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('ip_address') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label
                                    class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">SNMP
                                    Port</label>
                                <input type="number" wire:model="snmp_port" placeholder="161"
                                    class="block w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('snmp_port') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label
                                    class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Port
                                    Telnet/SSH</label>
                                <input type="number" wire:model="port" placeholder="23"
                                    class="block w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('port') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-span-2 sm:col-span-1">
                                <label
                                    class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Username</label>
                                <input type="text" wire:model="username" placeholder="admin"
                                    class="block w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('username') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label
                                    class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Password</label>
                                <input type="password" wire:model="password"
                                    placeholder="{{ $editMode ? 'Kosongkan jika tetap' : 'Password' }}"
                                    class="block w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('password') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-span-2">
                                <label
                                    class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">SNMP
                                    Version</label>
                                <select wire:model="snmp_version"
                                    class="block w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="v1">v1</option>
                                    <option value="v2c">v2c</option>
                                    <option value="v3">v3</option>
                                </select>
                                @error('snmp_version') <span
                                    class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label
                                    class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">SNMP
                                    Community (Read)</label>
                                <input type="text" wire:model="snmp_community_read" placeholder="public"
                                    class="block w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('snmp_community_read') <span
                                    class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label
                                    class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">SNMP
                                    Community (Write)</label>
                                <input type="text" wire:model="snmp_community_write" placeholder="private"
                                    class="block w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('snmp_community_write') <span
                                    class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-span-2">
                                <label
                                    class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Brand</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label
                                        class="flex items-center justify-between px-4 py-2 bg-indigo-50 dark:bg-indigo-900/20 border-2 border-indigo-500 dark:border-indigo-400 rounded-lg cursor-pointer transition-all">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-black text-indigo-700 dark:text-indigo-300">ZTE
                                                C320</span>
                                            <span class="text-[10px] text-indigo-500 font-medium">Terverifikasi</span>
                                        </div>
                                        <input type="radio" wire:model="brand" value="zte" checked
                                            class="text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded-full">
                                    </label>
                                    <label
                                        class="flex items-center justify-between px-4 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg opacity-50 cursor-not-allowed">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium text-gray-400">Huawei</span>
                                            <span class="text-[10px] text-gray-400">Coming Soon</span>
                                        </div>
                                        <input type="radio" disabled class="text-gray-300 border-gray-300 rounded-full">
                                    </label>
                                </div>
                                @error('brand') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-span-2">
                                <label
                                    class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Deskripsi
                                    & Lokasi</label>
                                <textarea wire:model="description" rows="3" placeholder="Catatan teknis..."
                                    class="block w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                            </div>
                        </div>
                    </div>
                    <div
                        class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100 dark:border-gray-700/50">
                        <button type="submit" wire:loading.attr="disabled"
                            class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-xs font-bold uppercase tracking-wider text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto transition-all duration-300">
                            {{ $editMode ? 'Simpan Perubahan' : 'Buat OLT' }}
                        </button>
                        <button type="button" @click="showFormModal = false; $wire.closeModal()"
                            wire:loading.attr="disabled"
                            class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto transition-all duration-300">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" @click="showDeleteModal = false; $wire.closeDeleteModal()">
                <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100 dark:border-gray-700/50">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Hapus OLT</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Apakah Anda yakin ingin menghapus
                                    perangkat OLT ini? Data yang dihapus tidak dapat dikembalikan.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100 dark:border-gray-700/50">
                    <button wire:click="deleteOlt" wire:loading.attr="disabled"
                        class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-red-600 text-xs font-bold uppercase tracking-wider text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto transition-all duration-300">
                        Hapus Data
                    </button>
                    <button @click="showDeleteModal = false; $wire.closeDeleteModal()" wire:loading.attr="disabled"
                        class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto transition-all duration-300">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ONT Monitor Modal -->
    <div x-show="$wire.showOntMonitorModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" @click="$wire.closeOntMonitor()">
                <div class="absolute inset-0 bg-gray-900/80 backdrop-blur-sm"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white dark:bg-gray-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-gray-100 dark:border-gray-800">
                <div class="bg-indigo-600 dark:bg-indigo-900 px-6 py-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg leading-6 font-black text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Monitor ONT (Live SNMP)
                        </h3>
                        <p class="text-xs text-indigo-200 mt-1 font-medium">Data real-time dari:
                            {{ $monitoringOltName }} ({{ strtoupper($monitoringOltBrand) }})
                        </p>
                    </div>
                    <button wire:click="closeOntMonitor"
                        class="text-indigo-200 hover:text-white transition-colors bg-indigo-700/50 hover:bg-indigo-700 rounded-lg p-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6">
                    <!-- Stats summary -->
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div
                            class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-xl border border-blue-100 dark:border-blue-900/30">
                            <p class="text-[10px] font-black uppercase tracking-widest text-blue-500 mb-1">Total ONT
                                Aktif</p>
                            <p class="text-2xl font-black text-blue-700 dark:text-blue-400">
                                {{ collect($ontStatuses)->where('status', 'Online')->count() }}
                            </p>
                        </div>
                        <div
                            class="bg-red-50 dark:bg-red-900/20 p-4 rounded-xl border border-red-100 dark:border-red-900/30">
                            <p class="text-[10px] font-black uppercase tracking-widest text-red-500 mb-1">Total
                                LOS/Offline</p>
                            <p class="text-2xl font-black text-red-700 dark:text-red-400">
                                {{ collect($ontStatuses)->whereIn('status', ['Offline', 'LOS'])->count() }}
                            </p>
                        </div>
                        <div
                            class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl border border-gray-200 dark:border-gray-700/50">
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-500 mb-1">Total Terbaca
                                (SNMP)</p>
                            <p class="text-2xl font-black text-gray-700 dark:text-gray-300">
                                {{ count($ontStatuses) }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
                        <div class="max-h-[500px] overflow-y-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                                <thead class="bg-gray-50 dark:bg-gray-800/80 sticky top-0 z-10">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                                            SNMP Index</th>
                                        <th
                                            class="px-6 py-3 text-left text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                                            Status Phase</th>
                                        <th
                                            class="px-6 py-3 text-left text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                                            Optical Power (Rx)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                    @empty($ontStatuses)
                                        <tr>
                                            <td colspan="3"
                                                class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                                Tidak ada data ONT yang terbaca dari perangkat.
                                            </td>
                                        </tr>
                                    @else
                                        @foreach($ontStatuses as $index => $statusInfo)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                                                <td
                                                    class="px-6 py-3 whitespace-nowrap text-xs font-mono text-gray-600 dark:text-gray-400">
                                                    {{ $index }}
                                                </td>
                                                <td class="px-6 py-3 whitespace-nowrap">
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider
                                                                                        @if($statusInfo['status'] === 'Online') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400
                                                                                        @elseif($statusInfo['status'] === 'LOS') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                                                                        @else bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400 @endif">
                                                        {{ $statusInfo['status'] }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-3 whitespace-nowrap">
                                                    @if($statusInfo['rx_power'] !== '-')
                                                        <span
                                                            class="text-sm font-black {{ floatval($statusInfo['rx_power']) < -28 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                                            {{ $statusInfo['rx_power'] }}
                                                        </span>
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endempty
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gray-50 dark:bg-gray-800/80 px-6 py-3 border-t border-gray-100 dark:border-gray-800 flex justify-end">
                    <button wire:click="closeOntMonitor"
                        class="px-6 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-xs font-bold uppercase tracking-widest text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all shadow-sm">
                        Tutup Panel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>