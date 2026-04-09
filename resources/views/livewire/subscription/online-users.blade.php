<div class="py-6" wire:poll.60s>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Pelanggan Online (Real-time)') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Monitor sesi aktif pelanggan
            </p>
        </x-slot>

        @if($radiusOffline)
            <div class="mb-6 rounded-xl border border-amber-300 dark:border-amber-600/50 bg-amber-50 dark:bg-amber-900/20 p-4">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-amber-800 dark:text-amber-300">Server FreeRadius Tidak Terjangkau</h3>
                        <p class="mt-1 text-xs text-amber-700 dark:text-amber-400">Database Radius sedang offline atau tidak dapat dijangkau. Data sesi aktif tidak tersedia. Fitur lain tetap berjalan normal.</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
                <!-- Search -->
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
                    <div class="w-full sm:w-1/3">
                        <input wire:model.live.debounce.300ms="search" type="search" placeholder="Cari Username, IP, atau MAC..."
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
                        <span>Total Online: <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ method_exists($sessions, 'total') ? $sessions->total() : 0 }}</span> Sesi</span>
                        <button wire:click="refresh" wire:loading.attr="disabled" class="inline-flex items-center p-2 border border-transparent rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors disabled:opacity-50" title="Refresh Data">
                            <svg wire:loading.class="animate-spin" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">User Info</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Network Info</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Start Time / Duration</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Traffic (Up/Down)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">NAS</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($sessions as $session)
                                @php
                                    $sub = $subscriptions[$session->username] ?? null;
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $session->username }}
                                                </div>
                                                @if($sub)
                                                    <div class="text-xs text-indigo-500 dark:text-indigo-400">
                                                        {{ $sub->customer->name ?? 'Unknown Customer' }}
                                                    </div>
                                                    <div class="text-xs text-gray-400">
                                                        {{ $sub->package->name ?? '-' }}
                                                    </div>
                                                @else
                                                    <div class="text-xs text-gray-500 italic">
                                                        Unregistered / Manual
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $session->framedipaddress }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $session->callingstationid }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                            {{ \Carbon\Carbon::parse($session->acctstarttime)->format('d M H:i') }}
                                        </div>
                                        <div class="text-xs text-green-600 dark:text-green-400 font-bold">
                                            {{ $this->formatDuration(time() - strtotime($session->acctstarttime)) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            Upload: <span class="text-gray-900 dark:text-gray-100">{{ $this->formatBytes($session->acctinputoctets) }}</span>
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            Download: <span class="text-gray-900 dark:text-gray-100">{{ $this->formatBytes($session->acctoutputoctets) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $session->nasipaddress }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $session->nasportid }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button wire:click="confirmKick({{ $session->radacctid }}, '{{ addslashes($session->username) }}')"
                                            wire:loading.attr="disabled"
                                            wire:target="confirmKick({{ $session->radacctid }}, '{{ addslashes($session->username) }}')"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 disabled:opacity-50 transition-opacity"
                                            title="Kick User">
                                            <svg wire:loading.remove wire:target="kickUser({{ $session->radacctid }})" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1012.728 0M12 3v9" />
                                            </svg>
                                            <svg wire:loading wire:target="kickUser({{ $session->radacctid }})" class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        Tidak ada pelanggan yang sedang online saat ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if(method_exists($sessions, 'links'))
                <div class="mt-4">
                    {{ $sessions->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Kick Confirmation Modal -->
    <x-dialog-modal wire:model.live="confirmingKick">
        <x-slot name="title">
            {{ __('Putus Koneksi Pelanggan') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Apakah Anda yakin ingin memutus sesi aktif untuk pelanggan ') }} 
            <span class="font-bold">"{{ $userToKickName }}"</span>? 
            {{ __('Pelanggan akan terputus dari jaringan dan harus login/koneksi ulang.') }}
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('confirmingKick', false)" wire:loading.attr="disabled">
                {{ __('Batal') }}
            </x-secondary-button>

            <x-danger-button class="ml-3" wire:click="kickUser" wire:loading.attr="disabled">
                {{ __('Ya, Putus Koneksi') }}
            </x-danger-button>
        </x-slot>
    </x-dialog-modal>
</div>
