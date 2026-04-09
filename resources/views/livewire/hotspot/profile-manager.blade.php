<div x-data="{ showFormModal: false, showConfirmationModal: false }" @open-modal.window="showFormModal = true"
    @open-confirmation-modal.window="showConfirmationModal = true"
    @close-modal.window="showFormModal = false; showConfirmationModal = false;">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Hotspot Profile Manager') }}
        </h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Kelola profil hotspot dan batasan bandwidth
        </p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-6">
                <!-- Control Bar -->
                <div
                    class="bg-white dark:bg-gray-800 shadow-2xl border border-gray-100 dark:border-gray-700/50 rounded-lg p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                        <div class="flex-1 max-w-md relative group">
                            <div
                                class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-indigo-500">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-indigo-500 transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="search"
                                class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 dark:bg-gray-900 border-transparent dark:border-gray-700 focus:bg-white dark:focus:bg-gray-800 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 dark:focus:border-indigo-500 rounded-lg text-sm font-medium transition-all dark:text-gray-200 placeholder-gray-400"
                                placeholder="Cari nama profil hotspot...">
                        </div>

                        <div class="flex items-center gap-3">
                            <button @click="showFormModal = true; $wire.openModal()"
                                class="group relative flex items-center justify-center px-6 py-3.5 bg-indigo-600 text-white rounded-lg font-black uppercase tracking-widest text-[10px] hover:bg-indigo-500 focus:ring-4 focus:ring-indigo-200 transition-all shadow-xl shadow-indigo-100 dark:shadow-none overflow-hidden">
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 -translate-x-full group-hover:translate-x-full transition-transform duration-700">
                                </div>
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span>Create Profile</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table Container -->
                <div
                    class="bg-white dark:bg-gray-800 shadow-2xl border border-gray-100 dark:border-gray-700/50 rounded-lg overflow-hidden">
                    <div
                        class="px-8 py-5 border-b border-gray-50 dark:border-gray-700/50 bg-gray-50/50 dark:bg-gray-900/20 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl">
                                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">
                                Hotspot Profiles List</h3>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span
                                class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-tighter">Live
                                Database Sync</span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700/50">
                            <thead class="bg-gray-50/50 dark:bg-gray-900/40">
                                <tr>
                                    <th
                                        class="px-8 py-4 text-left text-[10px] font-black uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">
                                        Profile Name</th>
                                    <th
                                        class="px-8 py-4 text-left text-[10px] font-black uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">
                                        Bandwidth</th>
                                    <th
                                        class="px-8 py-4 text-left text-[10px] font-black uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">
                                        Validity</th>
                                    <th
                                        class="px-8 py-4 text-left text-[10px] font-black uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">
                                        Price</th>
                                    <th
                                        class="px-8 py-4 text-center text-[10px] font-black uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">
                                        Availability</th>
                                    <th
                                        class="px-8 py-4 text-right text-[10px] font-black uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-50 dark:divide-gray-700/60">
                                @forelse ($profiles as $profile)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/20 transition-all group">
                                        <td class="px-8 py-5 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div
                                                    class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center mr-3 group-hover:bg-indigo-600 transition-colors">
                                                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400 group-hover:text-white transition-colors"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                        </path>
                                                    </svg>
                                                </div>
                                                <div
                                                    class="text-sm font-black text-gray-900 dark:text-white tracking-tight">
                                                    {{ $profile->name }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 whitespace-nowrap">
                                            <div
                                                class="text-[11px] font-bold text-gray-600 dark:text-gray-300 uppercase tracking-tighter">
                                                {{ $profile->rate_limit ?? 'NO LIMIT' }}
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 whitespace-nowrap">
                                            <div
                                                class="inline-flex items-center text-[10px] font-bold text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-900/40 px-2 py-1 rounded-lg border border-slate-100 dark:border-slate-800">
                                                <svg class="w-3 h-3 mr-1 text-slate-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ $profile->validity_value }} {{ strtoupper($profile->validity_unit) }}
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 whitespace-nowrap">
                                            <div
                                                class="text-sm font-black text-emerald-600 dark:text-emerald-400 tracking-tight">
                                                Rp {{ number_format($profile->price, 0, ',', '.') }}</div>
                                        </td>
                                        <td class="px-8 py-5 whitespace-nowrap text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{ $profile->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300' }}">
                                                <span
                                                    class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $profile->is_active ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                                {{ $profile->is_active ? 'Active' : 'Offline' }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-5 whitespace-nowrap text-right">
                                            <div class="flex items-center justify-end space-x-2">
                                                <button @click="showFormModal = true; $wire.edit({{ $profile->id }})"
                                                    class="p-2 rounded-xl text-indigo-600 bg-indigo-50 dark:bg-indigo-900/20 hover:bg-indigo-600 hover:text-white transition-all shadow-sm active:shadow-none"
                                                    title="Modify Profile">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                </button>
                                                <button
                                                    @click="showConfirmationModal = true; $wire.triggerConfirm('delete', {{ $profile->id }}, 'Hapus Profil?', 'Apakah Anda yakin ingin menghapus profil {{ $profile->name }}?')"
                                                    class="p-2 rounded-xl text-rose-600 bg-rose-50 dark:bg-rose-900/20 hover:bg-rose-600 hover:text-white transition-all shadow-sm active:shadow-none"
                                                    title="Delete Profile">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6"
                                            class="px-8 py-12 text-center text-gray-400 dark:text-gray-500 italic text-sm">
                                            No profiles found in database.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div
                        class="px-8 py-4 bg-gray-50/50 dark:bg-gray-900/30 border-t border-gray-100 dark:border-gray-700/50">
                        {{ $profiles->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div x-show="showFormModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" @click="showFormModal = false; $wire.closeModal()">
                <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">
                        {{ $isEdit ? 'Edit Profile' : 'Profile Voucher' }}
                    </h3>

                    <div class="space-y-4">
                        <!-- Nama Profile & Warna (Warna skipped per instruction) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama
                                profile</label>
                            <input type="text" wire:model="name"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Mikrotik Group & Address List -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mikrotik
                                    group</label>
                                <input type="text" wire:model="mikrotik_group"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <p class="text-[10px] text-gray-500 mt-1">Harus sama dengan nama profile di
                                    mikrotik</p>
                                @error('mikrotik_group') <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mikrotik
                                    address list</label>
                                <input type="text" wire:model="address_list" placeholder="Opsional"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <p class="text-[10px] text-gray-500 mt-1">Jika diisi maka setiap user akan di
                                    tambahkan
                                    ke Address List</p>
                            </div>
                        </div>

                        <!-- Mikrotik Rate Limit & Shared -->
                        <div class="grid grid-cols-4 gap-4">
                            <div class="col-span-3">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mikrotik
                                    rate
                                    limit</label>
                                <input type="text" wire:model="rate_limit" placeholder="1M/1500k 0/0 0/0 0/0 8 0/0"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <p class="text-[10px] text-gray-500 mt-1">Jika dikosongkan, maka akan digunakan
                                    limitasi
                                    profile mikrotik</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Shared</label>
                                <input type="number" wire:model="shared_users" min="1"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>

                        <!-- Kuota -->
                        <div class="grid grid-cols-4 gap-4">
                            <div class="col-span-3">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kuota</label>
                                <input type="number" wire:model="data_limit" {{ $data_limit_unit === 'UNLIMITED' ? 'disabled' : '' }}
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm disabled:bg-gray-100 disabled:text-gray-400">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Satuan</label>
                                <select wire:model.live="data_limit_unit"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="UNLIMITED">UNLIMITED</option>
                                    <option value="MB">MB</option>
                                    <option value="GB">GB</option>
                                </select>
                            </div>
                        </div>

                        <!-- Durasi -->
                        <div class="grid grid-cols-4 gap-4">
                            <div class="col-span-3">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Durasi</label>
                                <input type="number" wire:model="time_limit" {{ $time_limit_unit === 'UNLIMITED' ? 'disabled' : '' }}
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm disabled:bg-gray-100 disabled:text-gray-400">
                                <p class="text-[10px] text-gray-500 mt-1">Nilai durasi harus kecil dari Masa
                                    Aktif</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Satuan</label>
                                <select wire:model.live="time_limit_unit"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="UNLIMITED">UNLIMITED</option>
                                    <option value="minutes">MENIT</option>
                                    <option value="hours">JAM</option>
                                    <option value="days">HARI</option>
                                </select>
                            </div>
                        </div>

                        <!-- Masa Aktif -->
                        <div class="grid grid-cols-4 gap-4">
                            <div class="col-span-3">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Masa
                                    aktif</label>
                                <input type="number" wire:model="validity_value" min="1"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <p class="text-[10px] text-gray-500 mt-1">Masa Aktif dihitung sejak pertama kali
                                    vocher
                                    digunakan</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Satuan</label>
                                <select wire:model="validity_unit"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="minutes">MENIT</option>
                                    <option value="hours">JAM</option>
                                    <option value="days">HARI</option>
                                    <option value="weeks">MINGGU</option>
                                    <option value="months">BULAN</option>
                                </select>
                            </div>
                        </div>

                        <!-- HARGA -->
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 uppercase">HARGA</label>
                                <input type="number" wire:model="price" min="0"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <p class="text-[10px] text-gray-500 mt-1">Harga jual ke konsumen</p>
                            </div>
                        </div>

                        <div class="flex items-center mt-4">
                            <x-checkbox id="is_active" wire:model="is_active" />
                            <x-label for="is_active" class="ml-2" value="{{ __('Active') }}" />
                        </div>
                    </div>
                </div>
                <!-- Action Footer should be inside the inline-block modal card -->
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="{{ $isEdit ? 'update' : 'store' }}"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        {{ __('Save') }}
                    </button>
                    <button type="button" @click="showFormModal = false; $wire.closeModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        {{ __('Cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div x-show="showConfirmationModal" x-cloak class="fixed inset-0 z-[70] overflow-y-auto"
        aria-labelledby="modal-title" role="dialog" aria-modal="true" x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                @click="showConfirmationModal = false; $wire.closeConfirmationModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                {{ $confirmationTitle }}
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $confirmationMessage }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="executeAction"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Yes, Proceed
                    </button>
                    <button type="button" @click="showConfirmationModal = false; $wire.closeConfirmationModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>