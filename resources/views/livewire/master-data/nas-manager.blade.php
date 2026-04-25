<div class="py-6" wire:poll.15s="refreshConnectionStatuses"
    x-data="{ showFormModal: false, showDeleteModal: false, showDetailModal: false, showBulkDeleteServerModal: false }"
    @open-modal.window="showFormModal = true"
    @open-delete-modal.window="showDeleteModal = true"
    @open-detail-modal.window="showDetailModal = true"
    @open-bulk-delete-modal.window="showBulkDeleteServerModal = true"
    @close-modal.window="showFormModal = false; showDeleteModal = false; showDetailModal = false; showBulkDeleteServerModal = false;">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('NAS / Router') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Kelola infrastruktur perangkat NAS dan Router Mikrotik
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
                                    placeholder="Cari Nama, Shortname atau IP..."
                                    class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 dark:bg-gray-900/50 border-gray-100 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 shadow-sm">
                            </div>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <button wire:click="openModal"
                            class="inline-flex items-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-widest rounded-xl shadow-lg hover:shadow-indigo-500/40 transition-all duration-300 gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Tambah NAS</span>
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
                                    Network Info</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Koneksi</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700/50">
                            @forelse($nasList as $nas)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors group">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-4 cursor-pointer"
                                            wire:click="showNasDetails({{ $nas->id }})">
                                            <div class="relative">
                                                <div
                                                    class="h-10 w-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center text-indigo-600 dark:text-indigo-400 group-hover:scale-105 transition-transform duration-300">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                    </svg>
                                                </div>
                                                {{-- Connection indicator dot --}}
                                                @if($nas->last_check)
                                                    <div
                                                        class="absolute -bottom-1 -right-1 h-3 w-3 rounded-full border-2 border-white dark:border-gray-800 {{ $nas->is_online ? 'bg-green-500' : 'bg-red-500' }}">
                                                    </div>
                                                @else
                                                    <div
                                                        class="absolute -bottom-1 -right-1 h-3 w-3 rounded-full border-2 border-white dark:border-gray-800 bg-gray-400">
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div
                                                    class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                    {{ $nas->name }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 font-mono">
                                                    {{ $nas->shortname }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col gap-1">
                                            <div class="flex items-center gap-2">
                                                <span class="text-[10px] font-bold text-gray-400 uppercase w-8">IP</span>
                                                <code
                                                    class="text-xs font-mono text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700/50 px-1.5 py-0.5 rounded">{{ $nas->ip_address }}</code>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-[10px] font-bold text-gray-400 uppercase w-8">Port</span>
                                                <code
                                                    class="text-xs font-mono text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700/50 px-1.5 py-0.5 rounded">{{ $nas->api_port }}</code>
                                            </div>
                                        </div>
                                    </td>
                                    {{-- Connection Status Column --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col gap-1">
                                            @if($nas->last_check)
                                                @if($nas->is_online)
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50">
                                                        <span
                                                            class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                                                        Connected
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800/50">
                                                        <span
                                                            class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span>
                                                        Disconnected
                                                    </span>
                                                @endif
                                                <span class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5"
                                                    title="{{ $nas->last_check->format('d M Y H:i:s') }}">
                                                    {{ $nas->last_check->diffForHumans() }}
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 border border-gray-200 dark:border-gray-600">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-1.5"></span>
                                                    Unknown
                                                </span>
                                                <span class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">
                                                    Belum pernah dicek
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    {{-- Active/Inactive Status Column --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($nas->is_active)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800/50">
                                                Aktif
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            {{-- Check Connection Button --}}
                                            <button wire:click="checkSingleConnection({{ $nas->id }})"
                                                class="p-2 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg transition-all"
                                                title="Cek Koneksi"
                                                wire:loading.attr="disabled"
                                                wire:target="checkSingleConnection({{ $nas->id }})">
                                                <svg class="w-4 h-4"
                                                    wire:loading.class="animate-spin"
                                                    wire:target="checkSingleConnection({{ $nas->id }})"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                                    </path>
                                                </svg>
                                            </button>
                                            {{-- Detail Button --}}
                                            <button wire:click="showNasDetails({{ $nas->id }})"
                                                class="p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg transition-all"
                                                title="Detail Info">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </button>
                                            {{-- Edit Button --}}
                                            <button wire:click="editNas({{ $nas->id }})"
                                                class="p-2 text-gray-400 hover:text-amber-500 hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg transition-all"
                                                title="Edit">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            {{-- Delete Button --}}
                                            <button wire:click="confirmDelete({{ $nas->id }})"
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
                                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                </svg>
                                            </div>
                                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Belum ada data
                                                NAS.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($nasList->hasPages())
                    <div class="mt-6">
                        {{ $nasList->links() }}
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
                            {{ $editMode ? 'Update Perangkat NAS' : 'Tambah NAS Baru' }}
                        </h3>

                        <div class="grid grid-cols-2 gap-4">
                            <!-- Identitas -->
                            <div class="col-span-2 sm:col-span-1">
                                <label
                                    class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Nama
                                    Perangkat</label>
                                <input type="text" wire:model="name" placeholder="Mikrotik Pusat"
                                    class="block w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label
                                    class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Shortname</label>
                                <input type="text" wire:model="shortname" placeholder="NAS-01"
                                    class="block w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('shortname') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Koneksi -->
                            <div class="col-span-2 sm:col-span-1">
                                <label
                                    class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">IP
                                    Address</label>
                                <input type="text" wire:model="ip_address" placeholder="192.168.1.1"
                                    class="block w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('ip_address') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label
                                    class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">API
                                    Port</label>
                                <input type="number" wire:model="api_port" placeholder="8728"
                                    class="block w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('api_port') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Creds -->
                            <div class="col-span-2 sm:col-span-1">
                                <label
                                    class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Username</label>
                                <input type="text" wire:model="username" placeholder="admin"
                                    class="block w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('username') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-span-2 sm:col-span-1" x-data="{ show: false }">
                                <label
                                    class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Password</label>
                                <div class="relative">
                                    <input :type="show ? 'text' : 'password'" wire:model="password"
                                        placeholder="{{ $editMode ? 'Biarkan kosong jika tetap' : 'Password' }}"
                                        class="block w-full pl-3 pr-10 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <button type="button" @click="show = !show"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <svg x-show="!show" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg x-cloak x-show="show" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                    </button>
                                </div>
                                @error('password') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-2" x-data="{ show: false }">
                                <label
                                    class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Radius
                                    Secret</label>
                                <div class="relative">
                                    <input :type="show ? 'text' : 'password'" wire:model="secret" placeholder="{{ $editMode ? 'Biarkan kosong jika tetap' : 'Secret key...' }}"
                                        class="block w-full pl-3 pr-10 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <button type="button" @click="show = !show"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <svg x-show="!show" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg x-cloak x-show="show" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                    </button>
                                </div>
                                @error('secret') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-2">
                                <label
                                    class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Description</label>
                                <textarea wire:model="description" rows="3"
                                    class="block w-full px-3 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                            </div>

                            <div class="col-span-2">
                                <div class="grid grid-cols-2 gap-4">
                                    <div
                                        class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-700">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Status Aktif</span>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" wire:model="is_active" class="sr-only peer">
                                            <div
                                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600">
                                            </div>
                                        </label>
                                    </div>

                                    <div
                                        class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-700">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Message-Authenticator</span>
                                            <span class="text-[10px] text-amber-600 dark:text-amber-400 font-bold">Wajib untuk BlastRADIUS Mitigation</span>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" wire:model="require_message_authenticator" class="sr-only peer">
                                            <div
                                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-amber-600">
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100 dark:border-gray-700/50">
                        <button type="submit" wire:loading.attr="disabled" wire:target="save"
                            class="w-full inline-flex items-center gap-2 justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-xs font-bold uppercase tracking-wider text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>{{ $editMode ? 'Simpan Perubahan' : 'Buat NAS' }}</span>
                        </button>
                        <button type="button" @click="showFormModal = false; $wire.closeModal()" wire:loading.attr="disabled" wire:target="save"
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
            <div class="fixed inset-0 transition-opacity"
                @click="showDeleteModal = false; $wire.closeDeleteModal()">
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
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Hapus NAS</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Apakah Anda yakin ingin menghapus perangkat NAS ini? Data yang dihapus tidak dapat dikembalikan.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100 dark:border-gray-700/50">
                    <button wire:click="deleteNas" wire:loading.attr="disabled" wire:target="deleteNas"
                        class="w-full inline-flex items-center gap-2 justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-red-600 text-xs font-bold uppercase tracking-wider text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg wire:loading wire:target="deleteNas" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Hapus Data</span>
                    </button>
                    <button @click="showDeleteModal = false; $wire.closeDeleteModal()" wire:loading.attr="disabled" wire:target="deleteNas"
                        class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto transition-all duration-300">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div x-show="showDetailModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" @click="showDetailModal = false; $wire.closeDetailModal()">
                <div class="absolute inset-0 bg-gray-500/75 dark:bg-gray-900/80 backdrop-blur-sm"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-gray-100 dark:border-gray-700">
                @if($nasDetailId)
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white">
                                Detail Perangkat NAS
                            </h3>
                            <div class="flex items-center gap-2">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $nasDetail && $nasDetail['uptime'] ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                    <span
                                        class="w-1.5 h-1.5 rounded-full {{ $nasDetail && $nasDetail['uptime'] ? 'bg-emerald-500' : 'bg-red-500' }} mr-1.5"></span>
                                    {{ $nasDetail && $nasDetail['uptime'] ? 'Online' : 'Offline' }}
                                </span>
                                <button @click="showDetailModal = false; $wire.closeDetailModal()" class="text-gray-400 hover:text-gray-500">
                                    <span class="sr-only">Close</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Tabs -->
                        <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                            <nav class="-mb-px flex space-x-8">
                                <button wire:click="$set('activeTab', 'info')"
                                    class="{{ $activeTab === 'info' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 hover:border-gray-300' }} whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors">
                                    Info System
                                </button>
                                <button wire:click="$set('activeTab', 'servers')"
                                    class="{{ $activeTab === 'servers' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 hover:border-gray-300' }} whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors">
                                    Server Services
                                </button>
                            </nav>
                        </div>

                        @if($activeTab === 'info')
                            @if($nasDetail)
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                            Identity</p>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                            {{ $nasDetail['identity'] }}</p>
                                    </div>
                                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                            Platform</p>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                            {{ $nasDetail['platform'] }} / {{ $nasDetail['board_name'] }}</p>
                                    </div>
                                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                            Version</p>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                            {{ $nasDetail['version'] }}</p>
                                    </div>
                                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                                            Uptime</p>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                            {{ $nasDetail['uptime'] }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- CPU -->
                                    <div class="p-4 border border-gray-100 dark:border-gray-700/50 rounded-xl">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-xs font-bold text-gray-500 uppercase">CPU Load</span>
                                            <span class="text-xs font-bold text-indigo-600">{{ $nasDetail['cpu_load'] }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div class="bg-indigo-600 h-2 rounded-full"
                                                style="width: {{ $nasDetail['cpu_load'] }}%"></div>
                                        </div>
                                    </div>
                                    <!-- Memory -->
                                    <div class="p-4 border border-gray-100 dark:border-gray-700/50 rounded-xl">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-xs font-bold text-gray-500 uppercase">Memory</span>
                                            <span
                                                class="text-xs font-bold text-blue-600">{{ number_format($nasDetail['free_memory'] / 1024 / 1024, 1) }}MB
                                                Free</span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full"
                                                style="width: {{ $nasDetail['total_memory'] > 0 ? 100 - ($nasDetail['free_memory'] / $nasDetail['total_memory'] * 100) : 0 }}%">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- HDD -->
                                    <div class="p-4 border border-gray-100 dark:border-gray-700/50 rounded-xl">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-xs font-bold text-gray-500 uppercase">HDD</span>
                                            <span
                                                class="text-xs font-bold text-amber-600">{{ number_format($nasDetail['free_hdd'] / 1024 / 1024, 1) }}MB
                                                Free</span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div class="bg-amber-600 h-2 rounded-full"
                                                style="width: {{ $nasDetail['total_hdd'] > 0 ? 100 - ($nasDetail['free_hdd'] / $nasDetail['total_hdd'] * 100) : 0 }}%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Connection Failed</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Could not connect to Mikrotik API.</p>
                                </div>
                            @endif
                        @elseif($activeTab === 'servers')
                            <div x-data="{ selected: [] }">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white">Active Services</h4>
                                    <div class="flex gap-2">
                                        <button x-show="selected.length > 0" @click="showBulkDeleteServerModal = true; $wire.confirmBulkDeleteServers(selected)"
                                            class="px-3 py-1.5 bg-red-600 text-white text-xs font-bold uppercase tracking-wider rounded-lg hover:bg-red-700 transition">
                                            Delete Selected
                                        </button>
                                        <button wire:click="syncServers({{ $nasDetailId }})"
                                            wire:loading.attr="disabled" wire:target="syncServers"
                                            class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-50 text-indigo-700 border border-indigo-200 text-xs font-bold uppercase tracking-wider rounded-lg hover:bg-indigo-100 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                            <svg wire:loading wire:target="syncServers" class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span>Scan Services</span>
                                        </button>
                                    </div>
                                </div>

                                <div class="overflow-hidden border border-gray-100 dark:border-gray-700 rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                                            <tr>
                                                <th class="px-4 py-3 w-8"></th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Type
                                                </th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Name
                                                </th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">
                                                    Interface</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">
                                                    Profile</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            @forelse($nasServerList as $server)
                                                <tr>
                                                    <td class="px-4 py-2">
                                                        <input type="checkbox" x-model="selected" value="{{ $server->id }}"
                                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                    </td>
                                                    <td class="px-4 py-2">
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $server['type'] === 'pppoe' ? 'bg-blue-100 text-blue-800' : ($server['type'] === 'hotspot' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800') }}">
                                                            {{ strtoupper($server['type']) }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100 font-medium">
                                                        {{ $server['name'] }}</td>
                                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $server['interface'] }}</td>
                                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $server['profile'] }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">
                                                        No services found. Click "Scan Services" to sync.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="showDetailModal = false; $wire.closeDetailModal()"
                            class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Close
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Bulk Delete Server Modal -->
    <div x-show="showBulkDeleteServerModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity"
                @click="showBulkDeleteServerModal = false; $wire.closeBulkDeleteServerModal()">
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
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Delete Selected Servers</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Are you sure you want to delete these servers? Only the database records will be removed; Mikrotik configurations will remain untouched.</p>
                                <p class="mt-2 font-bold text-red-600">{{ count($selectedServers) }} servers selected.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100 dark:border-gray-700/50">
                    <button wire:click="deleteSelectedServers" wire:loading.attr="disabled" wire:target="deleteSelectedServers"
                        class="w-full inline-flex items-center gap-2 justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-red-600 text-xs font-bold uppercase tracking-wider text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg wire:loading wire:target="deleteSelectedServers" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Delete Servers</span>
                    </button>
                    <button @click="showBulkDeleteServerModal = false; $wire.closeBulkDeleteServerModal()" wire:loading.attr="disabled" wire:target="deleteSelectedServers"
                        class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto transition-all duration-300">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>