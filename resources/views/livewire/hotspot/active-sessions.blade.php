<div class="py-6" wire:poll.30s>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <x-slot name="header">
            <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Active Sessions') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Monitor pengguna hotspot yang sedang online secara realtime.
            </p>
        </x-slot>

        @if($radiusOffline)
            <div
                class="mb-6 rounded-xl border border-amber-300 dark:border-amber-600/50 bg-amber-50 dark:bg-amber-900/20 p-4">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-amber-800 dark:text-amber-300">Server FreeRadius Tidak Terjangkau
                        </h3>
                        <p class="mt-1 text-xs text-amber-700 dark:text-amber-400">Database Radius sedang offline atau tidak
                            dapat dijangkau. Data sesi aktif tidak tersedia. Fitur lain tetap berjalan normal.</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Stats / Overview (Optional) -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700/50">
                <div class="flex items-center">
                    <div
                        class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl text-emerald-600 dark:text-emerald-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Online</p>
                        <h3 class="text-2xl font-black text-gray-900 dark:text-white">
                            {{ method_exists($sessions, 'total') ? $sessions->total() : 0 }}</h3>
                    </div>
                </div>
            </div>
            <!-- Add more stats if needed -->
        </div>

        <!-- Filter & Search -->
        <div
            class="bg-white dark:bg-gray-800 shadow-lg border border-gray-200 dark:border-gray-700 rounded-2xl p-4 mb-8">
            <div class="flex flex-col md:flex-row gap-4 justify-between items-center">
                <div class="relative w-full md:w-1/3">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari User, IP, atau MAC..."
                        class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl leading-5 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all sm:text-sm">
                </div>

                <div class="flex items-center space-x-2">
                    <span class="relative flex h-3 w-3">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Live
                        Update (30s)</span>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div
            class="bg-white dark:bg-gray-800 shadow-2xl border border-gray-100 dark:border-gray-700/50 rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200/50 dark:divide-gray-700/50">
                    <thead>
                        <tr class="bg-gray-50/80 dark:bg-gray-900/50">
                            <th
                                class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.1em] text-gray-500 dark:text-gray-400">
                                Username</th>
                            <th
                                class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.1em] text-gray-500 dark:text-gray-400">
                                IP Address</th>
                            <th
                                class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.1em] text-gray-500 dark:text-gray-400">
                                MAC Address</th>
                            <th
                                class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.1em] text-gray-500 dark:text-gray-400">
                                Started</th>
                            <th
                                class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.1em] text-gray-500 dark:text-gray-400">
                                Duration</th>
                            <th
                                class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-[0.1em] text-gray-500 dark:text-gray-400">
                                Data Usage</th>
                            <th
                                class="px-6 py-4 text-center text-[10px] font-black uppercase tracking-[0.1em] text-gray-500 dark:text-gray-400">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700/70">
                        @forelse($sessions as $session)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/20 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $session->username }}
                                    </div>
                                    <div class="text-[10px] text-gray-500">{{ $session->nasipaddress }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-xs font-mono font-bold">
                                        {{ $session->framedipaddress }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-xs font-mono text-gray-600 dark:text-gray-400">
                                        {{ $session->callingstationid }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                        {{ \Carbon\Carbon::parse($session->acctstarttime)->format('d M H:i') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                        {{ \Carbon\Carbon::parse($session->acctstarttime)->diffForHumans(null, true) }}
                                    </div>
                                    @php
                                        $limit = $session->voucher?->time_limit ?? $session->voucher?->profile?->time_limit;
                                        $unit = $session->voucher?->profile?->time_limit_unit ?? '';
                                    @endphp
                                    @if($limit)
                                        <div class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5">
                                            of {{ $limit }} {{ $unit }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-[11px] font-medium text-gray-600 dark:text-gray-400">
                                        <span class="text-rose-500">↓
                                            {{ \Illuminate\Support\Number::fileSize($session->acctoutputoctets) }}</span>
                                        <span class="mx-1">|</span>
                                        <span class="text-blue-500">↑
                                            {{ \Illuminate\Support\Number::fileSize($session->acctinputoctets) }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <button wire:click="confirmKick({{ $session->radacctid }})"
                                        class="text-gray-400 hover:text-rose-600 transition-colors" title="Disconnect User">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636">
                                            </path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <p class="text-sm font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                                        Tidak ada user online</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(method_exists($sessions, 'links'))
                <div class="px-6 py-4 bg-gray-50/50 dark:bg-gray-900/30 border-t border-gray-100 dark:border-gray-700/50">
                    {{ $sessions->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Kick Confirmation Modal -->
    <x-confirmation-modal wire:model="confirmingKick">
        <x-slot name="title">
            {{ __('Konfirmasi Kick User') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Apakah Anda yakin ingin memutuskan koneksi user ini? User harus login ulang untuk kembali online.') }}
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmingKick')" wire:loading.attr="disabled">
                {{ __('Batal') }}
            </x-secondary-button>

            <x-danger-button class="ms-3" wire:click="kick" wire:loading.attr="disabled">
                {{ __('Ya, Kick!') }}
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>
</div>
</div>