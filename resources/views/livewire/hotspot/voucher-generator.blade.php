<div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <x-slot name="header">
                <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Hotspot Voucher Generator') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Buat dan cetak voucher hotspot secara massal
                </p>
            </x-slot>
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
                <!-- Generator Form -->
                <div class="xl:col-span-12">
                    <div
                        class="relative overflow-hidden bg-white dark:bg-gray-800 shadow-2xl border border-gray-100 dark:border-gray-700/50 rounded-lg p-8 transition-all">
                        <!-- Background Accent -->
                        <div
                            class="absolute -top-24 -right-24 w-64 h-64 bg-indigo-50 dark:bg-indigo-900/10 rounded-full blur-3xl">
                        </div>

                        <div class="relative flex items-center space-x-4 mb-8">
                            <div class="p-3 bg-indigo-600 rounded-lg shadow-lg shadow-indigo-200 dark:shadow-none">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white tracking-tight uppercase">
                                    Konfigurasi Voucher</h3>
                                <p class="text-sm text-gray-500 font-medium">Atur parameter untuk pembuatan voucher
                                    massal</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            <!-- Section: Basic Settings -->
                            <div class="space-y-6">
                                <div
                                    class="p-5 bg-gray-50/50 dark:bg-gray-900/30 rounded-lg border border-gray-100 dark:border-gray-700/50">
                                    <h4
                                        class="text-[10px] font-black uppercase tracking-widest text-indigo-500 mb-4 flex items-center">
                                        <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Basic Configuration
                                    </h4>
                                    <div class="space-y-4">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label
                                                    class="block text-[11px] font-bold text-gray-400 uppercase tracking-tighter mb-1.5 ml-1">Quantity</label>
                                                <input type="number" wire:model="quantity" min="1" max="999"
                                                    class="block w-full px-4 py-2.5 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-all dark:text-white">
                                                @error('quantity') <span
                                                    class="text-rose-500 text-[10px] font-bold mt-1 inline-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-[11px] font-bold text-gray-400 uppercase tracking-tighter mb-1.5 ml-1">User
                                                    Mode</label>
                                                <select wire:model="user_mode"
                                                    class="block w-full px-4 py-2.5 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-all dark:text-white">
                                                    <option value="username_password">User & Pass</option>
                                                    <option value="username_equals_password">User = Pass</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div>
                                            <label
                                                class="block text-[11px] font-bold text-gray-400 uppercase tracking-tighter mb-1.5 ml-1">Hotspot
                                                Profile</label>
                                            <select wire:model="profile_id"
                                                class="block w-full px-4 py-2.5 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600 rounded-xl text-sm font-bold text-indigo-600 dark:text-indigo-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-all">
                                                <option value="">Pilih Profil</option>
                                                @foreach($profiles as $profile)
                                                    <option value="{{ $profile->id }}">{{ $profile->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('profile_id') <span
                                                class="text-rose-500 text-[10px] font-bold mt-1 inline-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section: Code Structure -->
                            <div class="space-y-6">
                                <div
                                    class="p-5 bg-indigo-50/30 dark:bg-indigo-900/10 rounded-lg border border-indigo-100 dark:border-indigo-800/50">
                                    <h4
                                        class="text-[10px] font-black uppercase tracking-widest text-indigo-500 mb-4 flex items-center">
                                        <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z">
                                            </path>
                                        </svg>
                                        Code Generator Style
                                    </h4>
                                    <div class="space-y-4">
                                        <div>
                                            <label
                                                class="block text-[11px] font-bold text-gray-400 uppercase tracking-tighter mb-1.5 ml-1">Character
                                                Type</label>
                                            <select wire:model="character_type"
                                                class="block w-full px-4 py-2.5 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-all dark:text-white">
                                                <option value="lower">Random abcd</option>
                                                <option value="upper">Random ABCD</option>
                                                <option value="mixed">Random aBcD</option>
                                                <option value="lower_num">Random 5ab2c34d</option>
                                                <option value="upper_num">Random 5AB2C34D</option>
                                                <option value="mixed_num">Random 5aB2c34D</option>
                                                <option value="numeric">Random 123456</option>
                                            </select>
                                        </div>

                                        <div class="grid grid-cols-3 gap-4">
                                            <div>
                                                <label
                                                    class="block text-[11px] font-bold text-gray-400 uppercase tracking-tighter mb-1.5 ml-1">Length</label>
                                                <select wire:model="length"
                                                    class="block w-full px-4 py-2.5 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-all dark:text-white">
                                                    @foreach(range(3, 12) as $len)
                                                        <option value="{{ $len }}">{{ $len }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-span-2">
                                                <label
                                                    class="block text-[11px] font-bold text-gray-400 uppercase tracking-tighter mb-1.5 ml-1">Prefix</label>
                                                <input type="text" wire:model="prefix" placeholder="VC-"
                                                    class="block w-full px-4 py-2.5 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600 rounded-xl text-sm placeholder-gray-300 dark:placeholder-gray-600 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-all dark:text-white font-mono tracking-widest">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section: Infrastructure & Limits -->
                            <div class="space-y-6">
                                <div
                                    class="p-5 bg-gray-50/50 dark:bg-gray-900/30 rounded-lg border border-gray-100 dark:border-gray-700/50">
                                    <h4
                                        class="text-[10px] font-black uppercase tracking-widest text-indigo-500 mb-4 flex items-center">
                                        <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                            </path>
                                        </svg>
                                        Network & Usage
                                    </h4>
                                    <div class="space-y-4">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label
                                                    class="block text-[11px] font-bold text-gray-400 uppercase tracking-tighter mb-1.5 ml-1">Time
                                                    Limit</label>
                                                <input type="text" wire:model="time_limit" placeholder="12h"
                                                    class="placeholder-gray-300 dark:placeholder-gray-600 block w-full px-4 py-2.5 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-all dark:text-white">
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-[11px] font-bold text-gray-400 uppercase tracking-tighter mb-1.5 ml-1">NAS
                                                    / Router</label>
                                                <select wire:model="nas_id"
                                                    class="block w-full px-4 py-2.5 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-all dark:text-white">
                                                    <option value="all">All NAS</option>
                                                    @foreach($nases as $nas)
                                                        <option value="{{ $nas->id }}">{{ $nas->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div>
                                            <label
                                                class="block text-[11px] font-bold text-gray-400 uppercase tracking-tighter mb-1.5 ml-1">Usage
                                                Data Limit</label>
                                            <div class="flex">
                                                <input type="number" wire:model="data_limit"
                                                    class="block w-full px-4 py-2.5 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600 rounded-l-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-all dark:text-white">
                                                <select wire:model="data_unit"
                                                    class="px-3 py-2.5 bg-gray-100 dark:bg-gray-700 border border-l-0 border-gray-200 dark:border-gray-600 rounded-r-xl text-sm font-bold text-gray-600 dark:text-gray-300 focus:outline-none">
                                                    <option value="MB">MB</option>
                                                    <option value="GB">GB</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex flex-col md:flex-row items-end gap-6">
                            <div class="flex-1 w-full">
                                <label
                                    class="block text-[11px] font-bold text-gray-400 uppercase tracking-tighter mb-1.5 ml-1">Catatan
                                    Tambahan (Komentar)</label>
                                <input type="text" wire:model="comment" placeholder="Voucher untuk event lokal..."
                                    class="placeholder-gray-300 dark:placeholder-gray-600 block w-full px-4 py-3 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-all dark:text-white">
                            </div>
                            <div class="w-full md:w-auto">
                                <button wire:click="generate"
                                    class="w-full md:w-auto group flex items-center justify-center px-8 py-3.5 bg-indigo-600 text-white rounded-lg font-black uppercase tracking-widest text-xs hover:bg-indigo-500 focus:ring-4 focus:ring-indigo-200 transition-all shadow-xl shadow-indigo-100 dark:shadow-none">
                                    <span>Generate Sekarang</span>
                                    <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Batches Section -->
                <div class="xl:col-span-12">
                    <div
                        class="bg-white dark:bg-gray-800 shadow-2xl border border-gray-100 dark:border-gray-700/50 rounded-lg overflow-hidden">
                        <div
                            class="px-8 py-5 border-b border-gray-50 dark:border-gray-700/50 bg-gray-50/50 dark:bg-gray-900/20 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl">
                                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">
                                    Riwayat Batch Terbaru</h3>
                            </div>
                            <span
                                class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-tighter">Auto-update
                                every generation</span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700/50">
                                <thead class="bg-gray-50/50 dark:bg-gray-900/40">
                                    <tr>
                                        <th
                                            class="px-8 py-4 text-left text-[10px] font-black uppercase tracking-[0.1em] text-gray-400 dark:text-gray-500">
                                            Waktu Pembuatan</th>
                                        <th
                                            class="px-8 py-4 text-left text-[10px] font-black uppercase tracking-[0.1em] text-gray-400 dark:text-gray-500">
                                            Profil Hotspot</th>
                                        <th
                                            class="px-8 py-4 text-center text-[10px] font-black uppercase tracking-[0.1em] text-gray-400 dark:text-gray-500">
                                            Jumlah</th>
                                        <th
                                            class="px-8 py-4 text-right text-[10px] font-black uppercase tracking-[0.1em] text-gray-400 dark:text-gray-500">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white dark:bg-gray-800 divide-y divide-gray-50 dark:divide-gray-700/60">
                                    @foreach($batches as $batch)
                                        @php $profile = \App\Models\HotspotProfile::find($batch->hotspot_profile_id); @endphp
                                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/20 transition-all group">
                                            <td class="px-8 py-5 whitespace-nowrap">
                                                <div
                                                    class="text-[11px] font-bold text-gray-800 dark:text-gray-200 uppercase tracking-tighter leading-none mb-1">
                                                    {{ $batch->created_at->format('d M Y') }}</div>
                                                <div
                                                    class="text-[10px] text-gray-400 dark:text-gray-500 font-medium font-mono lowercase tracking-tighter leading-none">
                                                    {{ $batch->created_at->format('H:i:s') }}</div>
                                            </td>
                                            <td class="px-8 py-5 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="w-2 h-2 rounded-full bg-indigo-500 mr-2.5"></div>
                                                    <div
                                                        class="text-sm font-black text-gray-900 dark:text-white tracking-tight">
                                                        {{ $profile->name ?? 'Unknown Profile' }}</div>
                                                </div>
                                            </td>
                                            <td class="px-8 py-5 whitespace-nowrap text-center">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-300 uppercase">
                                                    {{ $batch->count }} Vouchers
                                                </span>
                                            </td>
                                            <td class="px-8 py-5 whitespace-nowrap text-right">
                                                <button wire:click="openPrintModal('{{ $batch->batch_id }}')"
                                                    class="inline-flex items-center justify-center p-2 rounded-xl text-indigo-600 bg-indigo-50 dark:bg-indigo-900/20 hover:bg-indigo-600 hover:text-white transition-all shadow-sm active:shadow-none"
                                                    title="Print This Batch">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div
                            class="px-8 py-4 bg-gray-50/50 dark:bg-gray-900/30 border-t border-gray-100 dark:border-gray-700/50">
                            {{ $batches->links() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Print Modal -->
            <x-dialog-modal wire:model="showPrintModal">
                <x-slot name="title">
                    {{ __('Print Vouchers') }}
                </x-slot>

                <x-slot name="content">
                    <div class="space-y-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Select a template to print the vouchers. If no template is selected, the default layout will
                            be used.
                        </p>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Template</label>
                            <select wire:model.live="selectedTemplate"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Default Layout</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}">{{ $template->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </x-slot>

                <x-slot name="footer">
                    <x-secondary-button wire:click="closePrintModal" wire:loading.attr="disabled">
                        {{ __('Close') }}
                    </x-secondary-button>

                    @if($printBatchId)
                        <a href="{{ route('hotspot.vouchers.print', ['batch' => $printBatchId]) }}?template={{ $selectedTemplate }}"
                            target="_blank"
                            class="ml-3 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Print') }}
                        </a>
                    @endif
                </x-slot>
            </x-dialog-modal>
        </div>
    </div>
</div>