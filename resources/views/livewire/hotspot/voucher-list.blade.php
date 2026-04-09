<div x-data="{ 
    selected: @entangle('selectedVouchers'), 
    allIds: @js($vouchers->pluck('id')->map(fn($id) => (string) $id))
}">
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <x-slot name="header">
                <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Hotspot Vouchers') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Daftar voucher berdasarkan profil dan status.
                </p>
            </x-slot>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($summaryProfiles as $profile)
                    <div
                        class="relative overflow-hidden bg-white dark:bg-gray-800 shadow-xl border border-gray-100 dark:border-gray-700/50 rounded-2xl p-6 transition-all hover:shadow-2xl hover:-translate-y-1">
                        <!-- Background Accents -->
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-indigo-50 dark:bg-indigo-900/10 rounded-full">
                        </div>

                        <div class="relative flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $profile->name }}</h3>
                            </div>
                            <span
                                class="px-3 py-1 text-[10px] font-extrabold uppercase tracking-widest rounded-full {{ $profile->is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-gray-100 text-gray-500' }}">
                                {{ $profile->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div class="relative grid grid-cols-3 gap-2">
                            <div
                                class="p-3 bg-gray-50 dark:bg-gray-700/30 rounded-xl text-center border border-transparent hover:border-indigo-200 dark:hover:border-indigo-800 transition-colors">
                                <span
                                    class="block text-xl font-black text-gray-900 dark:text-white leading-none">{{ $profile->total_count }}</span>
                                <span
                                    class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 mt-1">Total</span>
                            </div>
                            <div
                                class="p-3 bg-emerald-50/50 dark:bg-emerald-900/10 rounded-xl text-center border border-transparent hover:border-emerald-200 dark:hover:border-emerald-800 transition-colors">
                                <span
                                    class="block text-xl font-black text-emerald-600 leading-none">{{ $profile->active_count }}</span>
                                <span class="text-[10px] uppercase font-bold text-emerald-500/70 mt-1">Unused</span>
                            </div>
                            <div
                                class="p-3 bg-rose-50/50 dark:bg-rose-900/10 rounded-xl text-center border border-transparent hover:border-rose-200 dark:hover:border-rose-800 transition-colors">
                                <span
                                    class="block text-xl font-black text-rose-500 leading-none">{{ $profile->used_count }}</span>
                                <span class="text-[10px] uppercase font-bold text-rose-400 mt-1">Used</span>
                            </div>
                        </div>

                        <div
                            class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center text-[11px]">
                            <span class="text-gray-400 font-medium tracking-wide">ESTIMATED PRICE</span>
                            <span class="text-gray-900 dark:text-white font-black text-sm">Rp
                                {{ number_format($profile->price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Enhanced Filters & Search -->
            <div
                class="bg-white dark:bg-gray-800 shadow-lg border border-gray-200 dark:border-gray-700 rounded-2xl p-4 mb-8">
                <div class="flex flex-col lg:flex-row gap-4 items-center">
                    <div class="relative flex-1 w-full group">
                        <div
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none transition-colors group-focus-within:text-indigo-500 text-gray-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Kode Voucher..."
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl leading-5 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-all shadow-sm">
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto">
                        <div class="relative flex-1 sm:w-48">
                            <div
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16m-7 6h7"></path>
                                </svg>
                            </div>
                            <select wire:model.live="profileFilter"
                                class="block w-full pl-9 pr-10 py-2.5 text-sm border-gray-200 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 rounded-xl bg-gray-50 dark:bg-gray-700/50 dark:text-white transition-all shadow-sm">
                                <option value="">Semua Profil</option>
                                @foreach($summaryProfiles as $profile)
                                    <option value="{{ $profile->id }}">{{ $profile->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="relative flex-1 sm:w-48">
                            <div
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <select wire:model.live="statusFilter"
                                class="block w-full pl-9 pr-10 py-2.5 text-sm border-gray-200 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 rounded-xl bg-gray-50 dark:bg-gray-700/50 dark:text-white transition-all shadow-sm">
                                <option value="">Semua Status</option>
                                <option value="active">Active (Unused)</option>
                                <option value="used">Used</option>
                                <option value="expired">Expired</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="w-full lg:w-auto">
                        <button wire:click="syncUsage" wire:loading.attr="disabled"
                            class="inline-flex justify-center items-center px-4 py-2.5 bg-indigo-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition-all shadow-lg active:shadow-none whitespace-nowrap">
                            <svg wire:loading.remove wire:target="syncUsage" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <svg wire:loading wire:target="syncUsage" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Sync Usage
                        </button>
                    </div>

                    <div class="w-full lg:w-auto flex flex-col sm:flex-row gap-3" x-show="selected.length > 0"
                        style="display: none;">
                        <button @click="$el.blur(); $wire.confirmBulkDelete(selected)"
                            class="inline-flex justify-center items-center px-5 py-2.5 bg-rose-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-rose-500 active:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition-all shadow-lg active:shadow-none">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                            Hapus (<span x-text="selected.length"></span>)
                        </button>
                    </div>
                </div>
            </div>

            <!-- Vouchers Table -->
            <div
                class="bg-white dark:bg-gray-800 shadow-2xl border border-gray-100 dark:border-gray-700/50 rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200/50 dark:divide-gray-700/50">
                        <thead>
                            <tr class="bg-gray-50/80 dark:bg-gray-900/50">
                                <th scope="col" class="px-6 py-4 text-left">
                                    <input type="checkbox" @change="selected = $el.checked ? allIds : []"
                                        :checked="allIds.length > 0 && selected.length === allIds.length"
                                        class="rounded-md border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700">
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.1em] text-gray-500 dark:text-gray-400">
                                    Kode Voucher</th>
                                <th
                                    class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.1em] text-gray-500 dark:text-gray-400">
                                    Profil / Paket</th>
                                <th
                                    class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.1em] text-gray-500 dark:text-gray-400 text-right">
                                    Harga</th>
                                <th
                                    class="px-6 py-4 text-center text-[10px] font-black uppercase tracking-[0.1em] text-gray-500 dark:text-gray-400">
                                    Status</th>
                                <th
                                    class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.1em] text-gray-500 dark:text-gray-400">
                                    Dibuat</th>
                                <th
                                    class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.1em] text-gray-500 dark:text-gray-400">
                                    Digunakan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700/70">
                            @forelse($vouchers as $voucher)
                                <tr wire:key="voucher-{{ $voucher->id }}"
                                    class="hover:bg-gray-50/50 dark:hover:bg-gray-700/20 transition-colors group">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" value="{{ $voucher->id }}" x-model="selected"
                                            class="rounded-md border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-300 dark:bg-gray-700">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-2">
                                            <span
                                                class="px-3 py-1.5 bg-gray-100 dark:bg-gray-700/50 rounded-lg font-mono text-sm font-black text-gray-900 dark:text-white tracking-widest border border-gray-200/50 dark:border-gray-600/50 shadow-sm">
                                                {{ $voucher->code }}
                                            </span>
                                            <button
                                                @click="navigator.clipboard.writeText('{{ $voucher->code }}'); alert('Kode disalin!')"
                                                class="opacity-0 group-hover:opacity-100 p-1 text-gray-400 hover:text-indigo-500 transition-all"
                                                title="Salin Kode">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-800 dark:text-gray-200">
                                            {{ $voucher->profile->name ?? '-' }}</div>
                                        <div class="text-[10px] text-gray-500 dark:text-gray-500 font-medium">HOTSPOT
                                            PACKAGE</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <span class="text-sm font-black text-gray-900 dark:text-white">Rp
                                            {{ number_format($voucher->profile->price ?? 0, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                        @php
                                            $color = match ($voucher->status) {
                                                'active' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                                                'used' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400 border-rose-200 dark:border-rose-800',
                                                'expired' => 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-400 border-slate-200 dark:border-slate-700',
                                                default => 'bg-gray-100 text-gray-800'
                                            };
                                        @endphp
                                        <span
                                            class="px-3 py-1 text-[10px] font-black uppercase tracking-widest border rounded-full {{ $color }}">
                                            {{ ucfirst($voucher->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-[11px] font-bold text-gray-700 dark:text-gray-300">
                                            {{ $voucher->created_at->format('d M Y') }}</div>
                                        <div
                                            class="text-[10px] text-gray-400 dark:text-gray-500 font-medium tracking-tighter">
                                            {{ $voucher->created_at->format('H:i:s') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($voucher->used_at)
                                            <div class="text-[11px] font-bold text-gray-700 dark:text-gray-300">
                                                {{ $voucher->used_at->format('d M Y') }}</div>
                                            <div
                                                class="text-[10px] text-gray-400 dark:text-gray-500 font-medium tracking-tighter">
                                                {{ $voucher->used_at->format('H:i:s') }}</div>
                                        @else
                                            <span class="text-[11px] text-gray-300 dark:text-gray-600 font-bold italic">NOT
                                                USED</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div
                                            class="inline-flex items-center justify-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-full mb-4">
                                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                                </path>
                                            </svg>
                                        </div>
                                        <p
                                            class="text-sm font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                                            Tidak ada voucher ditemukan</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div
                    class="px-6 py-4 bg-gray-50/50 dark:bg-gray-900/30 border-t border-gray-100 dark:border-gray-700/50 mt-auto">
                    {{ $vouchers->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Delete Confirmation Modal -->
    <x-confirmation-modal wire:model="confirmingBulkDelete">
        <x-slot name="title">
            {{ __('Hapus Voucher?') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Apakah Anda yakin ingin menghapus ' . count($selectedVouchers) . ' voucher yang dipilih? Tindakan ini tidak dapat dibatalkan.') }}
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('confirmingBulkDelete', false)" wire:loading.attr="disabled">
                {{ __('Batal') }}
            </x-secondary-button>

            <x-danger-button class="ms-3" wire:click="deleteSelected" wire:loading.attr="disabled">
                {{ __('Hapus Voucher') }}
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>
</div>