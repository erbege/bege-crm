<div x-data="{
                                        prefix: '{{ $pppoePrefix }}',
                                        suffix: '{{ $pppoeSuffix }}',
                                        length: {{ $pppoePasswordLength }},

                                        showFormModal: false,
                                        showCustomerModal: false,
                                        showAlert: false,
                                        alertMessage: '',

    triggerAlert(msg) {
    this.alertMessage = msg;
    this.showAlert = true;
    },

    closeAlert() {
    this.showAlert = false;
    this.alertMessage = '';
    },

    confirmShow: false,
    confirmAction: '',
    confirmId: null,
    confirmTitle: '',
    confirmMessage: '',

    triggerConfirm(data) {
    this.confirmAction = data.action;
    this.confirmId = data.id;
    this.confirmTitle = data.title;
    this.confirmMessage = data.message;
    this.confirmShow = true;
    },

    generatePppoeUsername() {
    let name = $wire.customer_name;
    if (!name) {
    this.triggerAlert('Nama pelanggan harus diisi terlebih dahulu.');
    return;
    }

    // Sanitize name: lowercase, remove non-alphanumeric
    let cleanName = name.toLowerCase().replace(/[^a-z0-9]/g, '');

    // Format date: ddmmyyyy
    let dateStr = '';
    let installDate = $wire.installation_date;

    if (installDate) {
    // Assume date format is YYYY-MM-DD
    let parts = installDate.split('-');
    if (parts.length === 3) {
    dateStr = parts[2] + parts[1] + parts[0];
    } else {
    // Fallback to JS Date parsing
    let d = new Date(installDate);
    if (isNaN(d.getTime())) {
    d = new Date();
    }
    let day = String(d.getDate()).padStart(2, '0');
    let month = String(d.getMonth() + 1).padStart(2, '0');
    let year = d.getFullYear();
    dateStr = `${day}${month}${year}`;
    }
    } else {
    let d = new Date();
    let day = String(d.getDate()).padStart(2, '0');
    let month = String(d.getMonth() + 1).padStart(2, '0');
    let year = d.getFullYear();
    dateStr = `${day}${month}${year}`;
    }

    $wire.pppoe_username = this.prefix + cleanName + dateStr + this.suffix;
    },

    generatePppoePassword() {
    const pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    let result = '';
    for (let i = 0; i < this.length; i++) { result +=pool.charAt(Math.floor(Math.random() * pool.length)); }
        $wire.pppoe_password=result; } }" @open-modal.window="showFormModal = true"
    @close-modal.window="showFormModal = false; confirmShow = false"
    @close-customer-picker.window="showCustomerModal = false" @open-customer-modal.window="showCustomerModal = true">
    <!-- Confirmation Modal -->
    <div x-show="confirmShow" x-cloak class="fixed z-[100] inset-0 overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="confirmShow" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-900/60 dark:bg-gray-950/80 backdrop-blur-sm transition-opacity"
                @click="confirmShow = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="confirmShow" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white dark:bg-gray-900 rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-white/20 dark:border-gray-800/50">
                <div class="px-8 pt-8 pb-6">
                    <div class="flex flex-col items-center text-center">
                        <div
                            class="w-20 h-20 bg-amber-100 dark:bg-amber-900/30 rounded-3xl flex items-center justify-center text-amber-600 dark:text-amber-400 mb-6 animate-bounce-subtle">
                            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-black text-gray-900 dark:text-gray-100 tracking-tight mb-2"
                            x-text="confirmTitle"></h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium px-4" x-text="confirmMessage">
                        </p>
                    </div>
                </div>

                <div
                    class="px-8 py-6 bg-gray-50 dark:bg-gray-800/50 flex flex-col sm:flex-row-reverse gap-3 border-t border-gray-100 dark:border-gray-700/30">
                    <button type="button" @click="$wire.call(confirmAction, confirmId); confirmShow = false"
                        class="w-full inline-flex justify-center rounded-2xl border border-transparent shadow-lg shadow-amber-500/20 px-6 py-3 bg-amber-600 text-xs font-bold uppercase tracking-widest text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all active:scale-95">
                        Ya, Lanjutkan
                    </button>
                    <button type="button" @click="confirmShow = false"
                        class="w-full inline-flex justify-center rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm px-6 py-3 bg-white dark:bg-gray-900 text-xs font-bold uppercase tracking-widest text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <x-slot name="header">
                <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Manajemen Langganan') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Kelola paket langganan pelanggan
                </p>
            </x-slot>

            <div
                class="bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700/50 transition-all duration-500">
                <div class="p-6">
                    <!-- Actions Bar -->
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-8">
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap gap-3">
                                <div class="relative">
                                    <select wire:model.live="perPage"
                                        class="block w-full pl-3 pr-10 py-2.5 bg-gray-50 dark:bg-gray-900/50 border-gray-100 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 shadow-sm cursor-pointer appearance-none">
                                        <option value="10">10</option>
                                        <option value="15">15</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                    <div
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>

                                <div class="relative flex-1 max-w-sm">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input wire:model.live.debounce.300ms="search" type="search"
                                        placeholder="Cari Nama / ID Pelanggan..."
                                        class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 dark:bg-gray-900/50 border-gray-100 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 shadow-sm">
                                </div>

                                {{-- Status Multiselect Dropdown (Alpine-driven for instant feedback) --}}
                                <div class="relative w-full sm:w-48 z-10" x-data="{
                                        open: false,
                                        statuses: @entangle('filterStatuses').live,
                                        options: [
                                            { value: 'active', label: 'Aktif', color: 'bg-emerald-500' },
                                            { value: 'suspended', label: 'Terisolir', color: 'bg-orange-500' },
                                            { value: 'cancelled', label: 'Dibatalkan', color: 'bg-slate-500' },
                                            { value: 'pending', label: 'Menunggu', color: 'bg-amber-500' }
                                        ],
                                        toggle(val) {
                                            if (this.statuses.includes(val)) {
                                                if (this.statuses.length > 1) {
                                                    this.statuses = this.statuses.filter(s => s !== val);
                                                }
                                            } else {
                                                this.statuses = [...this.statuses, val];
                                            }
                                        }
                                    }" @click.outside="open = false">
                                    <button type="button" @click="open = !open"
                                        class="block w-full text-left pl-3 pr-10 py-2.5 bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 shadow-sm cursor-pointer whitespace-nowrap overflow-hidden text-ellipsis relative font-medium">
                                        <span
                                            x-text="statuses.length === options.length ? 'Semua Status' : statuses.length + ' Status Dipilih'"></span>
                                        <div
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </button>

                                    <div x-show="open" x-transition x-cloak
                                        class="absolute left-0 z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/50 rounded-xl shadow-xl py-1">
                                        <template x-for="option in options" :key="option.value">
                                            <label
                                                class="flex items-center px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer">
                                                <input type="checkbox" :checked="statuses.includes(option.value)"
                                                    @change="toggle(option.value)"
                                                    class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-indigo-600 focus:ring-indigo-500 w-4 h-4 mr-3 transition-colors">
                                                <div
                                                    class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                                    <span class="w-2 h-2 rounded-full" :class="option.color"></span>
                                                    <span x-text="option.label"></span>
                                                </div>
                                            </label>
                                        </template>
                                    </div>
                                </div>

                                {{-- Date Range --}}
                                <div class="flex items-center gap-2">
                                    <input type="date" wire:model.live.debounce.300ms="filterDateFrom"
                                        class="block w-full bg-gray-50 dark:bg-gray-900/50 border-gray-100 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 shadow-sm px-3 py-2.5">
                                    <span class="text-xs text-gray-400 font-medium shrink-0">s/d</span>
                                    <input type="date" wire:model.live.debounce.300ms="filterDateTo"
                                        class="block w-full bg-gray-50 dark:bg-gray-900/50 border-gray-100 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 shadow-sm px-3 py-2.5">
                                </div>

                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <button @click="$wire.openModal()"
                                class="inline-flex items-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-widest rounded-xl shadow-lg hover:shadow-indigo-500/40 transition-all duration-300 gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Tambah Langganan</span>
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead
                                class="bg-gray-50/50 dark:bg-gray-900/30 border-b border-gray-100 dark:border-gray-700/50">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Pelanggan</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Paket</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Periode</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                @forelse($subscriptions as $subscription)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div>
                                                    <div class="text-sm font-bold text-gray-900 dark:text-white">
                                                        {{ $subscription->customer->name }}
                                                    </div>
                                                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400">
                                                        {{ $subscription->customer->customer_id }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $subscription->package->name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $subscription->period_label }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $subscription->period_start->format('d M') }} -
                                                {{ $subscription->period_end->format('d M Y') }}
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-bold rounded-lg {{ $subscription->status_color }}">
                                                {{ $subscription->status_label }}
                                            </span>
                                            @if($subscription->olt_id)
                                                <div class="mt-1">
                                                    <span
                                                        class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wide">OLT
                                                        Configured</span>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end gap-2">
                                                @if(in_array($subscription->status, ['active', 'pending']))
                                                    <button type="button"
                                                        @click.stop="triggerConfirm({ action: 'toggleStatus', id: {{ $subscription->id }}, title: 'Konfirmasi Isolir', message: 'Yakin ingin mengisolir (suspend) langganan ini? User akan terputus dari internet.' })"
                                                        class="p-2 rounded-lg text-amber-600 hover:bg-amber-50 hover:text-amber-700 dark:text-amber-400 dark:hover:bg-amber-900/30 dark:hover:text-amber-300 transition-colors duration-200"
                                                        title="Isolir (Suspend)">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                        </svg>
                                                    </button>
                                                @elseif($subscription->status === 'suspended')
                                                    <button type="button"
                                                        @click.stop="triggerConfirm({ action: 'toggleStatus', id: {{ $subscription->id }}, title: 'Konfirmasi Aktivasi', message: 'Yakin ingin mengaktifkan kembali langganan ini?' })"
                                                        class="p-2 rounded-lg text-blue-600 hover:bg-blue-50 hover:text-blue-700 dark:text-blue-400 dark:hover:bg-blue-900/30 dark:hover:text-blue-300 transition-colors duration-200"
                                                        title="Aktifkan Kembali">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                                        </svg>
                                                    </button>
                                                @endif
                                                <button wire:click="editSubscription({{ $subscription->id }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="editSubscription({{ $subscription->id }})"
                                                    @if($subscription->status === 'pending') disabled @endif
                                                    class="p-2 rounded-lg text-indigo-600 hover:bg-indigo-50 hover:text-indigo-700 dark:text-indigo-400 dark:hover:bg-indigo-900/30 dark:hover:text-indigo-300 transition-colors duration-200 disabled:opacity-30 disabled:cursor-not-allowed {{ $subscription->status === 'pending' ? 'grayscale' : '' }}"
                                                    title="{{ $subscription->status === 'pending' ? 'Menunggu pembayaran - Tidak dapat diedit' : 'Edit' }}">
                                                    <svg wire:loading.remove
                                                        wire:target="editSubscription({{ $subscription->id }})"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                        stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                    </svg>
                                                    <svg wire:loading
                                                        wire:target="editSubscription({{ $subscription->id }})"
                                                        class="animate-spin w-5 h-5 text-indigo-500"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                                            stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor"
                                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                        </path>
                                                    </svg>
                                                </button>
                                                <button type="button"
                                                    @click.stop="triggerConfirm({ action: 'deleteSubscription', id: {{ $subscription->id }}, title: 'Hapus Langganan?', message: 'Apakah Anda yakin ingin menghapus data langganan ini? Data yang dihapus tidak dapat dikembalikan.' })"
                                                    class="p-2 rounded-lg text-red-600 hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-900/30 dark:hover:text-red-300 transition-colors duration-200"
                                                    title="Hapus">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                        stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                    </svg>
                                                </button>
                                                <a href="{{ route('provisioning.script', $subscription->id) }}"
                                                    target="_blank"
                                                    class="p-2 rounded-lg text-teal-600 hover:bg-teal-50 hover:text-teal-700 dark:text-teal-400 dark:hover:bg-teal-900/30 dark:hover:text-teal-300 transition-colors duration-200"
                                                    title="Lihat Script">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                        </path>
                                                    </svg>
                                                </a>

                                                <!-- Sync Button -->
                                                @if($subscription->pppoe_username && $subscription->status === 'active')
                                                    <button wire:click="manualSync({{ $subscription->id }})"
                                                        wire:loading.attr="disabled"
                                                        wire:target="manualSync({{ $subscription->id }})"
                                                        class="p-2 rounded-lg text-orange-600 hover:bg-orange-50 hover:text-orange-700 dark:text-orange-400 dark:hover:bg-orange-900/30 dark:hover:text-orange-300 transition-colors duration-200 disabled:opacity-50"
                                                        title="Sync ke Radius">
                                                        <svg wire:loading.remove
                                                            wire:target="manualSync({{ $subscription->id }})" class="w-5 h-5"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                        </svg>
                                                        <svg wire:loading wire:target="manualSync({{ $subscription->id }})"
                                                            class="animate-spin w-5 h-5 text-orange-500"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                stroke="currentColor" stroke-width="4"></circle>
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
                                        <td colspan="6" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-12 h-12 text-gray-400 dark:text-gray-500 mb-4" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                                    </path>
                                                </svg>
                                                <p class="text-gray-500 dark:text-gray-400 text-lg">Tidak ada data
                                                    langganan.</p>
                                                <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Klik tombol
                                                    "Tambah
                                                    Langganan" untuk membuat yang baru.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $subscriptions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div x-show="showFormModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true"
                @click="showFormModal = false; $wire.closeModal()">
                <div class="absolute inset-0 bg-gray-500/75 dark:bg-gray-900/75 backdrop-blur-sm transition-opacity">
                </div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full border border-gray-100 dark:border-gray-700/50">
                @if($showModal)
                    <!-- Header -->
                    <div
                        class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-100 dark:border-gray-700/50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                        {{ $editMode ? 'Edit Langganan' : 'Langganan Baru' }}
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $editMode ? 'Perbarui informasi langganan pelanggan' : 'Buat paket berlangganan baru untuk pelanggan' }}
                                    </p>
                                </div>
                            </div>
                            <button type="button" @click="showFormModal = false; $wire.closeModal()"
                                class="text-gray-400 hover:text-gray-500 transition-colors">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <form wire:submit="save">
                        <!-- Validation Errors Alert -->
                        @if ($errors->any())
                            <div
                                class="mx-4 mt-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/40 rounded-xl relative overflow-hidden group">
                                <div class="absolute top-0 left-0 w-1 h-full bg-red-500"></div>
                                <div class="flex items-start gap-3">
                                    <div class="p-1.5 bg-red-100 dark:bg-red-900/40 rounded-lg text-red-600 dark:text-red-400">
                                        <svg class="w-5 h-5 font-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-black text-red-800 dark:text-red-300 uppercase tracking-tight">
                                            Terjadi Kesalahan Validasi</h4>
                                        <ul
                                            class="mt-1 list-disc list-inside text-xs text-red-700 dark:text-red-400 font-medium space-y-0.5">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="px-4 py-6 sm:p-6 bg-gray-50/50 dark:bg-gray-900/20">
                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                                <!-- Left Column -->
                                <div class="lg:col-span-7 space-y-6">
                                    <!-- Customer Section -->
                                    <div
                                        class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700/50 shadow-sm relative overflow-hidden">
                                        <div class="mb-4 flex items-center justify-between">
                                            <h4
                                                class="text-sm font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                Data Pelanggan
                                            </h4>
                                            @if(!$editMode)
                                                <button type="button" @click="$wire.openCustomerModal()"
                                                    class="text-[10px] font-bold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 uppercase tracking-wider border border-indigo-100 dark:border-indigo-800/30 px-3 py-1.5 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-all">
                                                    {{ $customer_id ? 'Ganti Pelanggan' : 'Pilih Pelanggan' }}
                                                </button>
                                            @endif
                                        </div>

                                        @if($customer_id)
                                            <div
                                                class="p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl border border-indigo-100 dark:border-indigo-800/30 group hover:border-indigo-200 dark:hover:border-indigo-700/50 transition-all">
                                                <div class="flex items-start gap-4">
                                                    <div
                                                        class="h-10 w-10 bg-indigo-200 dark:bg-indigo-800 rounded-full shrink-0 flex items-center justify-center">
                                                        <span class="text-lg font-black text-indigo-700 dark:text-indigo-300">
                                                            {{ strtoupper(substr($customer_name, 0, 1)) }}
                                                        </span>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <h5 class="font-bold text-gray-900 dark:text-gray-100 text-sm">
                                                            {{ $customer_name }}
                                                        </h5>
                                                        <div class="flex items-center gap-2 mt-1">
                                                            <span
                                                                class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300">
                                                                {{ $customer_number }}
                                                            </span>
                                                        </div>
                                                        @if($customer_phone)
                                                            <div
                                                                class="flex items-center gap-1.5 mt-2 text-[11px] text-gray-500 dark:text-gray-400">
                                                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24"
                                                                    stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                                </svg>
                                                                <span class="font-medium">{{ $customer_phone }}</span>
                                                            </div>
                                                        @endif
                                                        @if($customer_address)
                                                            <div
                                                                class="flex items-start gap-1.5 mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                                                                <svg class="w-3.5 h-3.5 shrink-0 mt-0.5" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                </svg>
                                                                <span
                                                                    class="font-medium line-clamp-2">{{ $customer_address }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <input type="hidden" wire:model="customer_id">
                                                @error('customer_id') <span
                                                    class="text-red-500 text-[10px] mt-2 block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        @else
                                            <div class="text-center py-8 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-xl cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/10 transition-all group"
                                                @click="$wire.openCustomerModal()">
                                                <div
                                                    class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-indigo-200 dark:group-hover:bg-indigo-800 transition-colors">
                                                    <svg class="w-6 h-6 text-gray-400 group-hover:text-indigo-600 dark:text-gray-500 dark:group-hover:text-indigo-300"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                                    </svg>
                                                </div>
                                                <p
                                                    class="text-sm font-medium text-gray-500 dark:text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-300">
                                                    Klik untuk memilih pelanggan</p>
                                            </div>
                                            @error('customer_id') <span
                                                class="text-red-500 text-[10px] text-center block mt-2">{{ $message }}</span>
                                            @enderror
                                        @endif
                                    </div>

                                    <!-- Package & Period -->
                                    <div
                                        class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700/50 shadow-sm">
                                        <h4
                                            class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                                            <svg class="w-4 h-4 text-pink-500" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                            Paket & Layanan
                                        </h4>

                                        <div class="space-y-4">
                                            <!-- Service Type (Moved from Right Column) -->
                                            <div>
                                                <label
                                                    class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Tipe
                                                    Layanan</label>
                                                <select wire:model.live="service_type"
                                                    class="block w-full bg-gray-50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 px-4 py-2.5">
                                                    <option value="ppp">PPPoE (Broadband)</option>
                                                    <option value="dhcp">IP Static / DHCP (Leased Line)</option>
                                                    <option value="hotspot">Hotspot (Member Bulanan)</option>
                                                </select>
                                            </div>

                                            <!-- Package Select -->
                                            <div>
                                                <div class="flex justify-between items-center mb-1">
                                                    <label
                                                        class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Pilih
                                                        Paket *</label>
                                                    <span wire:loading wire:target="service_type"
                                                        class="text-[10px] text-indigo-500 font-medium animate-pulse">Memuat
                                                        paket...</span>
                                                </div>
                                                <select wire:model.live="package_id"
                                                    class="block w-full bg-gray-50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 px-4 py-2.5">
                                                    <option value="">-- Pilih Paket Internet --</option>
                                                    @foreach($this->packages as $pkg)
                                                        <option value="{{ $pkg->id }}">{{ $pkg->name }} -
                                                            {{ $pkg->formatted_price }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('package_id') <span
                                                    class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            {{-- Upgrade/Downgrade Info (only in edit mode when package changed) --}}
                                            @if($editMode && $original_package_id && $package_id && $package_id != $original_package_id)
                                                                            @php
                                                                                $newPkg = $this->packages->firstWhere('id', $package_id);
                                                                                $newPrice = $newPkg?->price ?? 0;
                                                                                $priceDiff = $newPrice - $original_package_price;
                                                                                $isUpgrade = $priceDiff > 0;
                                                                            @endphp
                                                                            <div
                                                                                class="p-3 rounded-xl border transition-all duration-300
                                                                                                                                                                                                    {{ $isUpgrade
                                                ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800/40'
                                                : 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800/40' }}">
                                                                                <div class="flex items-center gap-2 mb-2">
                                                                                    @if($isUpgrade)
                                                                                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none"
                                                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                                                stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                                                                        </svg>
                                                                                        <span
                                                                                            class="text-[10px] font-black uppercase tracking-widest text-emerald-700 dark:text-emerald-400">Upgrade
                                                                                            Paket</span>
                                                                                    @else
                                                                                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none"
                                                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                                                stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                                                                                        </svg>
                                                                                        <span
                                                                                            class="text-[10px] font-black uppercase tracking-widest text-amber-700 dark:text-amber-400">Downgrade
                                                                                            Paket</span>
                                                                                    @endif
                                                                                </div>
                                                                                <div class="flex items-center justify-between text-xs">
                                                                                    <div>
                                                                                        <span class="text-gray-500 dark:text-gray-400 font-medium">Paket
                                                                                            saat ini:</span>
                                                                                        <span
                                                                                            class="font-bold text-gray-700 dark:text-gray-300 ml-1">{{ $original_package_name }}</span>
                                                                                        <span class="text-gray-400 ml-1">(Rp
                                                                                            {{ number_format($original_package_price, 0, ',', '.') }})</span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex items-center justify-between text-xs mt-1">
                                                                                    <div>
                                                                                        <span
                                                                                            class="text-gray-500 dark:text-gray-400 font-medium">Selisih:</span>
                                                                                        <span
                                                                                            class="font-black ml-1 {{ $isUpgrade ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                                                                                            {{ $isUpgrade ? '+' : '-' }}Rp
                                                                                            {{ number_format(abs($priceDiff), 0, ',', '.') }}
                                                                                        </span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                            @endif

                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label
                                                        class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Mulai
                                                        *</label>
                                                    <input type="date" wire:model="period_start"
                                                        class="block w-full bg-gray-50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 px-4 py-2.5">
                                                    @error('period_start') <span
                                                        class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Hingga
                                                        *</label>
                                                    <input type="date" wire:model="period_end"
                                                        class="block w-full bg-gray-50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 px-4 py-2.5">
                                                    @error('period_end') <span
                                                        class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label
                                                        class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Tgl.
                                                        Instalasi</label>
                                                    <input type="date" wire:model="installation_date"
                                                        class="block w-full bg-gray-50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 px-4 py-2.5">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Jenis
                                                        Langganan *</label>
                                                    @if($allowedSubscriptionType === 'both')
                                                        <select wire:model="subscription_type"
                                                            class="block w-full bg-gray-50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 px-4 py-2.5">
                                                            <option value="prepaid">Prabayar</option>
                                                            <option value="postpaid">Pascabayar</option>
                                                        </select>
                                                    @else
                                                        <input type="text" readonly
                                                            value="{{ $allowedSubscriptionType === 'prepaid' ? 'Prabayar' : 'Pascabayar' }}"
                                                            class="block w-full bg-gray-100 dark:bg-gray-800 border-none rounded-xl text-sm text-gray-500 cursor-not-allowed px-4 py-2.5">
                                                    @endif
                                                    @error('subscription_type') <span
                                                        class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if(!$editMode)
                                        <div
                                            class="bg-indigo-50/50 dark:bg-indigo-900/10 rounded-xl p-5 border border-indigo-100 dark:border-indigo-800/30">
                                            <h4
                                                class="text-sm font-bold text-indigo-700 dark:text-indigo-300 mb-4 flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                                Tagihan Awal
                                            </h4>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4"
                                                x-data="{
                                                                                                                                                                                                                                                                                                                                                                     amount: @entangle('amount'),
                                                                                                                                                                                                                                                                                                                                                                     discount: @entangle('discount'),
                                                                                                                                                                                                                                                                                                                                                                     installation_fee: @entangle('installation_fee'),
                                                                                                                                                                                                                                                                                                                                                                     tax: @entangle('tax'),
                                                                                                                                                                                                                                                                                                                                                                     total: @entangle('total'),
                                                                                                                                                                                                                                                                                                                                                                     isProrated: @entangle('isProrated'),
                                                                                                                                                                                                                                                                                                                                                                     taxPercentage: @entangle('taxPercentage'),
                                                                                                                                                                                                                                                                                                                                                                      formatMoney(value) { return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value || 0); },
                                                                                                                                                                                                                                                                                                                                                                      calculate() {
                                                                                                                                                                                                                                                                                                                                                                          let amt = parseFloat(this.amount) || 0;
                                                                                                                                                                                                                                                                                                                                                                          let disc = parseFloat(this.discount) || 0;
                                                                                                                                                                                                                                                                                                                                                                          let inst = parseFloat(this.installation_fee) || 0;
                                                                                                                                                                                                                                                                                                                                                                          let subtotal = Math.max(0, amt - disc);
                                                                                                                                                                                                                                                                                                                                                                          this.tax = Math.ceil(subtotal * (this.taxPercentage / 100));
                                                                                                                                                                                                                                                                                                                                                                          this.total = Math.ceil(subtotal + this.tax + inst);
                                                                                                                                                                                                                                                                                                                                                                      },
                                                                                                                                                                                                                                                                                                                                                                      updateTotalOnly() {
                                                                                                                                                                                                                                                                                                                                                                          let amt = parseFloat(this.amount) || 0;
                                                                                                                                                                                                                                                                                                                                                                          let disc = parseFloat(this.discount) || 0;
                                                                                                                                                                                                                                                                                                                                                                          let inst = parseFloat(this.installation_fee) || 0;
                                                                                                                                                                                                                                                                                                                                                                          let tx = parseFloat(this.tax) || 0;
                                                                                                                                                                                                                                                                                                                                                                          let subtotal = Math.max(0, amt - disc);
                                                                                                                                                                                                                                                                                                                                                                          this.total = Math.ceil(subtotal + tx + inst);
                                                                                                                                                                                                                                                                                                                                                                      }
                                                                                                                                                                                                                                                                                                                                                                 }">

                                                <div>
                                                    <label
                                                        class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Jumlah</label>
                                                    <input type="number" x-model="amount" step="1"
                                                        class="block w-full bg-gray-100 dark:bg-gray-800 border-transparent rounded-xl text-sm focus:ring-0 shadow-sm cursor-not-allowed py-2 px-3 opacity-70"
                                                        readonly>
                                                    <template x-if="isProrated">
                                                        <p
                                                            class="mt-1 text-[10px] text-orange-600 dark:text-orange-400 flex items-center gap-1">
                                                            <span>(Prorata)</span>
                                                        </p>
                                                    </template>
                                                    @error('amount') <span
                                                        class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div>
                                                    <label
                                                        class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Diskon</label>
                                                    <input type="number" x-model="discount" @input="calculate()" step="1"
                                                        class="block w-full bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-4 py-2">
                                                    @error('discount') <span
                                                        class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div>
                                                    <label
                                                        class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Instalasi</label>
                                                    <input type="number" x-model="installation_fee" @input="updateTotalOnly()"
                                                        step="1"
                                                        class="block w-full bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-4 py-2">
                                                    @error('installation_fee') <span
                                                        class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div>
                                                    <label
                                                        class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Pajak</label>
                                                    <input type="number" x-model="tax" @input="updateTotalOnly()" step="1"
                                                        class="block w-full bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-4 py-2">
                                                    @error('tax') <span
                                                        class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div
                                                    class="sm:col-span-2 mt-2 pt-2 border-t border-indigo-200 dark:border-indigo-800/30 flex justify-between items-center">
                                                    <span
                                                        class="text-xs font-black text-indigo-700 dark:text-indigo-300 uppercase tracking-widest">TOTAL</span>
                                                    <span class="text-xl font-black text-indigo-600 dark:text-indigo-400"
                                                        x-text="'Rp ' + formatMoney(total)"></span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Right Column(Technical & Notes) - 5 cols -->
                                <div class="lg:col-span-5 space-y-6">
                                    <!-- Technical Data -->
                                    <div
                                        class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700/50 shadow-sm">
                                        <h4
                                            class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                                            <svg class="w-4 h-4 text-cyan-500" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            </svg>
                                            Konfigurasi Teknis
                                        </h4>

                                        <div class="space-y-4">
                                            <div class="space-y-4">
                                                <!-- NAS & Server Name -->
                                                <div class="grid grid-cols-2 gap-4">
                                                    <div>
                                                        <label
                                                            class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Router
                                                            (NAS)</label>
                                                        <select wire:model="nas_id"
                                                            class="block w-full bg-gray-50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 px-4 py-2.5">
                                                            <option value="">ALL (Semua Router)</option>
                                                            @foreach($this->nasList as $nas)
                                                                <option value="{{ $nas->id }}">{{ $nas->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Server</label>
                                                        <select wire:model="server_name"
                                                            class="block w-full bg-gray-50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 px-4 py-2.5">
                                                            <option value="">ALL (Default)</option>
                                                            <!-- Add logic to list available servers if needed -->
                                                        </select>
                                                    </div>
                                                </div>





                                                <!-- DYNAMIC FIELDS BASED ON SERVICE TYPE -->
                                                {{-- Dynamic fields re-evaluated on every Livewire re-render --}}
                                                <div>

                                                    <!-- PPP & HOTSPOT Credentials -->
                                                    @if(in_array($service_type, ['ppp', 'hotspot']))
                                                        <div
                                                            class="space-y-3 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-100 dark:border-gray-700/50 mb-4 transition-all duration-300">
                                                            <div>
                                                                <label
                                                                    class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Username
                                                                    {{ $service_type === 'hotspot' ? 'Hotspot' : 'PPPoE' }}</label>
                                                                <div class="flex gap-2">
                                                                    <input type="text" wire:model="pppoe_username"
                                                                        class="block w-full bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3 py-2"
                                                                        placeholder="username">
                                                                    <button type="button" @click="generatePppoeUsername()"
                                                                        class="p-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl hover:bg-indigo-200 transition-colors"
                                                                        title="Generate">
                                                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                                            stroke="currentColor">
                                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                                @error('pppoe_username') <span
                                                                    class="text-red-500 text-[10px]">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                            <div>
                                                                <label
                                                                    class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Password</label>
                                                                <div class="flex gap-2">
                                                                    <input type="text" wire:model="pppoe_password"
                                                                        class="block w-full bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3 py-2"
                                                                        placeholder="password">
                                                                    <button type="button" @click="generatePppoePassword()"
                                                                        class="p-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl hover:bg-indigo-200 transition-colors"
                                                                        title="Generate">
                                                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                                            stroke="currentColor">
                                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11.542 16.31 15.14 19.914a1 1 0 01-1.414 1.414l-4.242-4.242a1 1 0 01-.293-.707V15a2 2 0 012-2h2V9a2 2 0 012-2zm-6 0a2 2 0 11-4 0 2 2 0 014 0z" />
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                                @error('pppoe_password') <span
                                                                    class="text-red-500 text-[10px]">{{ $message }}</span>
                                                                @enderror
                                                            </div>

                                                            @if($service_type === 'ppp')
                                                                <div class="mt-3">
                                                                    <label
                                                                        class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">IP
                                                                        Address (Opsional - Static)</label>
                                                                    <input type="text" wire:model="ip_address"
                                                                        class="block w-full bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3 py-2"
                                                                        placeholder="192.168.x.x (Kosongi untuk Dynamic)">
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif

                                                    <!-- DHCP / Static IP Fields -->
                                                    @if($service_type === 'dhcp')
                                                        <div
                                                            class="space-y-3 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-100 dark:border-gray-700/50 mb-4 transition-all duration-300">
                                                            <div>
                                                                <label
                                                                    class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">MAC
                                                                    Address *</label>
                                                                <input type="text" wire:model="mac_address"
                                                                    class="block w-full bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3 py-2"
                                                                    placeholder="AA:BB:CC:DD:EE:FF">
                                                                @error('mac_address') <span
                                                                    class="text-red-500 text-[10px]">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                            <div>
                                                                <label
                                                                    class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">IP
                                                                    Address (Opsional)</label>
                                                                <input type="text" wire:model="ip_address"
                                                                    class="block w-full bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3 py-2"
                                                                    placeholder="192.168.x.x">
                                                                @error('ip_address') <span
                                                                    class="text-red-500 text-[10px]">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div> <!-- End dynamic fields -->

                                                <!-- OLT Information is relevant for PPP and DHCP mainly, maybe Hotspot too if on OLT? keep it general -->
                                                <!-- OLT & Device -->
                                                <div>
                                                    <label
                                                        class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">OLT
                                                        Server</label>
                                                    <select wire:model="olt_id"
                                                        class="block w-full bg-gray-50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 px-4 py-2.5">
                                                        <option value="">-- Tidak dikonfigurasi ke OLT --</option>
                                                        @foreach($this->olts as $olt)
                                                            <option value="{{ $olt->id }}">{{ $olt->name }}
                                                                ({{ $olt->ip_address }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <!-- Removed OLT ID condition -->
                                                <div class="grid grid-cols-4 gap-2">
                                                    <div>
                                                        <label class="text-[10px] text-gray-400 uppercase">Frame</label>
                                                        <input type="number" wire:model="olt_frame"
                                                            class="block w-full bg-gray-50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 px-4 py-2.5">
                                                    </div>
                                                    <div>
                                                        <label class="text-[10px] text-gray-400 uppercase">Slot</label>
                                                        <input type="number" wire:model="olt_slot"
                                                            class="block w-full bg-gray-50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 px-4 py-2.5">
                                                    </div>
                                                    <div>
                                                        <label class="text-[10px] text-gray-400 uppercase">Port</label>
                                                        <input type="number" wire:model="olt_port"
                                                            class="block w-full bg-gray-50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 px-4 py-2.5">
                                                    </div>
                                                    <div>
                                                        <label class="text-[10px] text-gray-400 uppercase">ONU</label>
                                                        <input type="number" wire:model="olt_onu_id"
                                                            class="block w-full bg-gray-50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 px-4 py-2.5">
                                                    </div>
                                                </div>

                                                <!-- SN -->
                                                <div>
                                                    <label
                                                        class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Serial
                                                        Number (SN)</label>
                                                    <input type="text" wire:model="device_sn"
                                                        class="block w-full bg-gray-50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 px-4 py-2.5"
                                                        placeholder="CTTC...">
                                                </div>
                                                <!-- Coverage -->
                                                <div>
                                                    <label
                                                        class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Titik
                                                        Coverage</label>
                                                    <select wire:model="coverage_point_id"
                                                        class="block w-full bg-gray-50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 px-4 py-2.5">
                                                        <option value="">-- Pilih Titik Coverage --</option>
                                                        @foreach($this->coveragePoints as $point)
                                                            <option value="{{ $point->id }}">{{ $point->name }}
                                                                ({{ $point->type_label }})</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Notes -->
                                        <div
                                            class="bg-white dark:bg-gray-800 rounded-xl mt-5 p-5 border border-gray-200 dark:border-gray-700/50 shadow-sm">
                                            <h4
                                                class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                                                <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Catatan
                                            </h4>
                                            <textarea wire:model="notes" rows="3"
                                                class="block w-full bg-gray-50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 px-4 py-2.5"
                                                placeholder="Tambahkan catatan tambahan..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div
                                class="bg-gray-50 dark:bg-gray-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100 dark:border-gray-700/50">
                                <button type="submit"
                                    class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-lg shadow-indigo-500/30 px-4 py-2 bg-indigo-600 text-base font-bold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-xs uppercase tracking-widest transition-all duration-300">
                                    {{ $editMode ? 'Simpan Perubahan' : 'Proses Langganan' }}
                                </button>
                                @if($editMode)
                                    <button type="button"
                                        @click.stop="triggerConfirm({ action: 'terminateSubscription', id: {{ $subscription_id }}, title: 'Berhenti Berlangganan?', message: 'Yakin ingin menghentikan langganan pelanggan ini? Akses internet akan dihentikan.' })"
                                        class="mt-3 sm:mt-0 w-full inline-flex justify-center rounded-xl border border-transparent shadow-lg shadow-red-500/30 px-4 py-2 bg-red-600 text-base font-bold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-xs uppercase tracking-widest transition-all duration-300">
                                        Berhenti Berlangganan
                                    </button>
                                @endif
                                <button type="button" @click="showFormModal = false; $wire.closeModal()"
                                    class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-200 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-xs uppercase tracking-widest transition-all duration-300">
                                    Batal
                                </button>
                            </div>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Customer Picker Modal -->
    <div x-show="showCustomerModal" x-cloak class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 dark:bg-gray-950/80 backdrop-blur-sm transition-opacity"
                @click="showCustomerModal = false; $wire.closeCustomerModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white dark:bg-gray-900 rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-white/20 dark:border-gray-800/50">
                <div class="px-8 pt-8 pb-4">
                    <div class="flex justify-between items-center mb-8">
                        <div>
                            <h3
                                class="text-2xl font-black text-gray-900 dark:text-gray-100 tracking-tight flex items-center gap-3">
                                <div
                                    class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/50 rounded-2xl flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                Pilih Pelanggan
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium italic">Silakan
                                pilih
                                pelanggan untuk langganan baru</p>
                        </div>
                        <button @click="showCustomerModal = false; $wire.closeCustomerModal()" type="button"
                            class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Search Box -->
                    <div
                        class="mb-8 p-1 bg-gray-100/50 dark:bg-gray-800/50 rounded-2xl border border-gray-200/50 dark:border-gray-700/50">
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-indigo-500 transition-colors"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="customerSearch"
                                class="block w-full pl-12 pr-4 py-4 bg-transparent border-none focus:ring-0 text-gray-900 dark:text-gray-100 placeholder-gray-400"
                                placeholder="Cari Nama atau No. Pelanggan...">
                        </div>
                    </div>

                    <!-- Customer List -->
                    <div
                        class="overflow-hidden bg-gray-50/50 dark:bg-gray-900/30 rounded-3xl border border-gray-200/50 dark:border-gray-800/50 max-h-96 overflow-y-auto custom-scrollbar">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                            <thead class="bg-gray-100 dark:bg-gray-800/50 sticky top-0 z-10">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-left text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                                        Identitas</th>
                                    <th
                                        class="px-6 py-4 text-left text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                                        Kontak</th>
                                    <th
                                        class="px-6 py-4 text-right text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                                @forelse($filteredCustomers as $cust)
                                    <tr class="group hover:bg-white dark:hover:bg-gray-800 transition-all duration-200">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="h-9 w-9 bg-indigo-100 dark:bg-indigo-900/40 rounded-xl flex items-center justify-center text-xs font-black text-indigo-600 dark:text-indigo-400">
                                                    {{ strtoupper(substr($cust->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div
                                                        class="text-sm font-bold text-gray-900 dark:text-gray-100 line-clamp-1">
                                                        {{ $cust->name }}
                                                    </div>
                                                    <div class="text-[10px] font-bold text-gray-400 uppercase">
                                                        {{ $cust->customer_id }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-xs text-gray-600 dark:text-gray-400 font-medium">
                                                {{ $cust->phone ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <button type="button" wire:click="selectCustomer({{ $cust->id }})"
                                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-bold uppercase tracking-widest rounded-xl transition-all shadow-md shadow-indigo-500/20 active:scale-95">
                                                Pilih
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 text-gray-300 dark:text-gray-700 mb-3" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                                </svg>
                                                <p class="text-sm text-gray-500 dark:text-gray-600 font-medium italic">
                                                    Tidak
                                                    ditemukan pelanggan yang cocok</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($filteredCustomers instanceof \Illuminate\Pagination\LengthAwarePaginator && $filteredCustomers->hasPages())
                        <div class="mt-4 px-2">
                            <div class="flex items-center justify-between">
                                <span
                                    class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                                    {{ $filteredCustomers->firstItem() }}–{{ $filteredCustomers->lastItem() }} dari
                                    {{ $filteredCustomers->total() }} pelanggan
                                </span>
                                <div class="flex items-center gap-1">
                                    {{-- Previous --}}
                                    @if($filteredCustomers->onFirstPage())
                                        <span
                                            class="px-3 py-1.5 text-xs font-bold text-gray-300 dark:text-gray-600 cursor-not-allowed">&laquo;</span>
                                    @else
                                        <button wire:click="previousPage('customerPage')"
                                            class="px-3 py-1.5 text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-lg transition-colors">&laquo;</button>
                                    @endif

                                    {{-- Page Numbers --}}
                                    @foreach($filteredCustomers->getUrlRange(max(1, $filteredCustomers->currentPage() - 2), min($filteredCustomers->lastPage(), $filteredCustomers->currentPage() + 2)) as $page => $url)
                                        @if($page == $filteredCustomers->currentPage())
                                            <span
                                                class="px-3 py-1.5 text-xs font-black text-white bg-indigo-600 rounded-lg shadow-sm">{{ $page }}</span>
                                        @else
                                            <button wire:click="gotoPage({{ $page }}, 'customerPage')"
                                                class="px-3 py-1.5 text-xs font-bold text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">{{ $page }}</button>
                                        @endif
                                    @endforeach

                                    {{-- Next --}}
                                    @if($filteredCustomers->hasMorePages())
                                        <button wire:click="nextPage('customerPage')"
                                            class="px-3 py-1.5 text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-lg transition-colors">&raquo;</button>
                                    @else
                                        <span
                                            class="px-3 py-1.5 text-xs font-bold text-gray-300 dark:text-gray-600 cursor-not-allowed">&raquo;</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div
                    class="px-8 py-6 bg-gray-100 dark:bg-gray-800/50 flex flex-row-reverse border-t border-gray-200 dark:border-gray-700/30">
                    <button type="button" @click="showCustomerModal = false; $wire.closeCustomerModal()"
                        class="px-6 py-2 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 text-[10px] font-bold uppercase tracking-widest rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Alpine.js Alert Modal -->
    <div x-show="showAlert" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95" class="fixed inset-0 z-[100] overflow-y-auto"
        style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 dark:bg-gray-950/80 backdrop-blur-sm" @click="closeAlert()">
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white dark:bg-gray-900 rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm sm:w-full border border-white/20 dark:border-gray-800/50">
                <div class="px-8 pt-10 pb-6 text-center">
                    <div
                        class="w-16 h-16 bg-red-100 dark:bg-red-900/40 rounded-3xl flex items-center justify-center text-red-600 dark:text-red-400 mx-auto mb-6">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 dark:text-gray-100 tracking-tight mb-2">Perhatian
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium" x-text="alertMessage"></p>
                </div>
                <div class="px-8 pb-8">
                    <button type="button" @click="closeAlert()"
                        class="w-full py-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-xs font-bold uppercase tracking-widest rounded-2xl hover:bg-gray-800 dark:hover:bg-gray-100 transition-all shadow-xl shadow-gray-950/10 active:scale-95">
                        Mengerti
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>