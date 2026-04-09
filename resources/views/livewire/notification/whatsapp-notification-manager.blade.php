<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('WhatsApp Gateway') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Kelola koneksi WhatsApp dan kirim pesan notifikasi
            </p>
        </x-slot>

        <!-- Device Status Card -->
        <div
            class="mb-6 bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700/50">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Status Perangkat</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Status koneksi ke server WhatsApp</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <button wire:click="checkDeviceStatus"
                            class="inline-flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-900 font-medium transition-colors">
                            <svg wire:loading.class="animate-spin" wire:target="checkDeviceStatus"
                                class="-ml-1 mr-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span>Refresh Status</span>
                        </button>
                    </div>
                </div>

                <div class="mt-4 flex flex-col md:flex-row items-center gap-6">
                    @if($deviceStatus)
                        <div class="flex items-center gap-3">
                            <div class="rounded-full bg-green-100 p-2">
                                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-base font-bold text-gray-900 dark:text-white">
                                    {{ $deviceStatus['pushname'] ?? $deviceStatus['name'] ?? 'Terhubung' }}
                                </h4>
                                <p class="text-xs text-gray-500">{{ $deviceStatus['wid'] ?? $deviceStatus['device'] ?? '' }}
                                </p>
                            </div>
                        </div>

                        <!-- Quota Info -->
                        @if(isset($deviceStatus['quota']))
                            <div
                                class="mt-4 md:mt-0 md:border-l md:border-gray-200 md:dark:border-gray-700 md:pl-6 md:ml-6 text-center md:text-left">
                                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Kuota Pesan</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                    {{ $deviceStatus['quota'] }}
                                </p>
                            </div>
                        @endif

                        <!-- Expiry Info -->
                        @if(isset($deviceStatus['expired']) || isset($deviceStatus['expire']))
                            <div
                                class="mt-4 md:mt-0 md:border-l md:border-gray-200 md:dark:border-gray-700 md:pl-6 md:ml-6 text-center md:text-left">
                                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Masa Aktif</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                    {{ \Carbon\Carbon::parse($deviceStatus['expired'] ?? $deviceStatus['expire'])->translatedFormat('d F Y') }}
                                </p>
                            </div>
                        @endif
                    @else
                        <div class="flex items-center gap-3">
                            <div class="rounded-full bg-red-100 p-2">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-base font-bold text-gray-900 dark:text-white">Terputus</h4>
                                <p class="text-xs text-gray-500">Silakan scan QR Code untuk menghubungkan.</p>
                            </div>
                        </div>
                    @endif

                    @if(!$deviceStatus && $qrCode)
                        <div class="mt-4 md:mt-0 flex-1">
                            @if(str_starts_with($qrCode, 'http'))
                                <img src="{{ $qrCode }}" alt="QR Code"
                                    class="h-32 w-32 object-contain border p-2 bg-white rounded-lg">
                            @else
                                <div class="bg-white p-2 rounded-lg inline-block">
                                    {!! $qrCode !!}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Main Content (History & Actions) -->
        <div data-page-ids="{{ json_encode($messages->pluck('id')->map(fn($id) => (string) $id)->toArray()) }}" x-data="{
                selectedMessages: @entangle('selectedMessages'),
                selectAll: @entangle('selectAll'),
                pageIds: [],
                toggleAll() {
                    if (this.selectAll) {
                        this.pageIds.forEach(id => {
                            if (!this.selectedMessages.includes(id)) {
                                this.selectedMessages.push(id);
                            }
                        });
                    } else {
                        this.selectedMessages = this.selectedMessages.filter(id => !this.pageIds.includes(id));
                    }
                },
                init() {
                    this.pageIds = JSON.parse($el.dataset.pageIds);
                    this.$watch('selectedMessages', value => {
                        const allSelected = this.pageIds.length > 0 && this.pageIds.every(id => value.includes(id));
                        if (this.selectAll !== allSelected) {
                            this.selectAll = allSelected;
                        }
                    });
                }
            }"
            class="bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700/50">
            <div class="p-6">


                <!-- Actions Bar -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
                    <div class="flex-1 min-w-0 flex gap-4">
                        <div class="relative max-w-md w-full">
                            <div
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input wire:model.live.debounce.300ms="search" type="search"
                                placeholder="Cari nomor atau isi pesan..."
                                class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 dark:bg-gray-900/50 border-gray-100 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 shadow-sm">
                        </div>

                        <!-- Bulk Actions Dropdown -->
                        <div class="relative" x-data="{ open: false }" x-show="selectedMessages.length > 0" x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95">
                            <button @click="open = !open" type="button"
                                class="inline-flex items-center px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition">
                                Aksi Masal (<span x-text="selectedMessages.length"></span>)
                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false"
                                class="absolute z-50 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 py-1 focus:outline-none"
                                style="display: none;">
                                <button wire:click="confirmBulkResend" @click="open = false"
                                    class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 transition">
                                    Kirim Ulang
                                </button>
                                <button wire:click="confirmBulkDelete" @click="open = false"
                                    class="block w-full px-4 py-2 text-left text-sm leading-5 text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 transition">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-shrink-0 gap-2">
                        <button wire:click="openSingleModal"
                            class="inline-flex items-center px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-25 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.013 8.013 0 01-5.45-2.146L3 19l1.15-3.45A8.013 8.013 0 012 12c0-4.418 3.582-8 8-8s8 3.582 8 8z" />
                            </svg>
                            Kirim Pesan
                        </button>
                        <button wire:click="openBlastModal"
                            class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-widest rounded-xl shadow-lg hover:shadow-indigo-500/40 transition-all duration-300 gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                            </svg>
                            Blast Message
                        </button>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-700/50">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700/50">
                        <thead class="bg-gray-50/50 dark:bg-gray-900/40">
                            <tr>
                                <th class="px-6 py-4 text-left">
                                    <input type="checkbox"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                        x-model="selectAll" @change="toggleAll">
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Target</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Pesan</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Waktu</th>
                                <th
                                    class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700/50">
                            @forelse($messages as $msg)
                                <tr wire:key="msg-{{ $msg->id }}" wire:click="openDetailModal({{ $msg->id }})"
                                    class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors cursor-pointer">
                                    <td class="px-6 py-4" @click.stop>
                                        <input type="checkbox" value="{{ $msg->id }}" x-model="selectedMessages"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $msg->target }}
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-500 dark:text-gray-400 max-w-xs truncate">
                                        {{ Str::limit($msg->message, 50) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                                                        {{ $msg->status === 'sent' ? 'bg-green-100 text-green-800' : '' }}
                                                                                        {{ $msg->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}
                                                                                        {{ $msg->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                            {{ ucfirst($msg->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                        {{ $msg->created_at->diffForHumans() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" @click.stop>
                                        <button wire:click="resend({{ $msg->id }})"
                                            class="text-indigo-600 hover:text-indigo-900 text-xs font-bold uppercase disabled:opacity-50"
                                            wire:loading.attr="disabled">Resend</button>
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
                                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.013 8.013 0 01-5.45-2.146L3 19l1.15-3.45A8.013 8.013 0 012 12c0-4.418 3.582-8 8-8s8 3.582 8 8z" />
                                                </svg>
                                            </div>
                                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Belum ada
                                                riwayat pesan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $messages->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Single Message Modal -->
    <x-dialog-modal wire:model="showSingleModal">
        <x-slot name="title">
            {{ __('Kirim Pesan WhatsApp') }}
        </x-slot>

        <x-slot name="content">
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor Tujuan</label>
                    <input type="text" wire:model="singleTarget"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm sm:text-sm"
                        placeholder="62812xxxxx">
                    @error('singleTarget') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pesan</label>
                    <textarea wire:model="singleMessage" rows="4"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm sm:text-sm"
                        placeholder="Tulis pesan anda..."></textarea>
                    @error('singleMessage') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showSingleModal', false)" wire:loading.attr="disabled">
                {{ __('Batal') }}
            </x-secondary-button>

            <x-button class="ml-3" wire:click="sendSingleMessage" wire:loading.attr="disabled">
                {{ __('Kirim') }}
            </x-button>
        </x-slot>
    </x-dialog-modal>

    <!-- Blast Message Modal -->
    <x-dialog-modal wire:model="showBlastModal">
        <x-slot name="title">
            {{ __('Kirim Pesan Massal (Blast)') }}
        </x-slot>

        <x-slot name="content">
            <div class="grid grid-cols-1 gap-4">

                <!-- Target Type Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Target Penerima</label>
                    <select wire:model.live="blastType"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="manual">Manual (Input Nomor)</option>
                        <option value="all">Semua Pelanggan (All Customers)</option>
                        <option value="active">Pelanggan Aktif (Active Subscriptions)</option>
                        <option value="area">Wilayah Spesifik (Per Area)</option>
                    </select>
                </div>

                <!-- Conditional Inputs based on Blast Type -->
                @if($blastType === 'manual')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Daftar Nomor</label>
                        <textarea wire:model="blastManualTargets" rows="3"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm sm:text-sm"
                            placeholder="Pisahkan dengan koma atau baris baru..."></textarea>
                        <p class="mt-1 text-xs text-gray-500">Contoh: 6281234567, 6289876543</p>
                        @error('blastManualTargets') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                @endif

                @if($blastType === 'area')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Wilayah</label>
                        <select wire:model="blastSpecificAreaId"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">-- Pilih Wilayah --</option>
                            @foreach($this->areas as $area)
                                <option value="{{ $area->id }}">{{ $area->name }} ({{ $area->type_label }})</option>
                            @endforeach
                        </select>
                        @error('blastSpecificAreaId') <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                @endif

                <!-- Message Input -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Isi Pesan</label>
                    <textarea wire:model="blastMessage" rows="5"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm sm:text-sm"
                        placeholder="Tulis pesan broadcast anda..."></textarea>
                    @error('blastMessage') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            @if($blastType !== 'manual')
                <div class="mt-2 bg-yellow-50 border-l-4 border-yellow-400 p-2">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-4 w-4 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs text-yellow-700">
                                Pesan akan dikirimkan ke semua nomor yang sesuai dengan kriteria. Pastikan pesan sudah
                                benar.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showBlastModal', false)" wire:loading.attr="disabled">
                {{ __('Batal') }}
            </x-secondary-button>

            <x-button class="ml-3" wire:click="sendBlast" wire:loading.attr="disabled">
                {{ __('Kirim Blast') }}
            </x-button>
        </x-slot>
    </x-dialog-modal>

    <!-- Detail Message Modal -->
    <x-dialog-modal wire:model="showDetailModal">
        <x-slot name="title">
            {{ __('Detail Pesan WhatsApp') }}
        </x-slot>

        <x-slot name="content">
            <div class="grid grid-cols-1 gap-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Target</label>
                        <input type="text" value="{{ $detailTarget }}" disabled
                            class="mt-1 block w-full bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-md shadow-sm sm:text-sm text-gray-500 dark:text-gray-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <div class="mt-2">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $detailStatus === 'sent' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $detailStatus === 'failed' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $detailStatus === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                {{ ucfirst($detailStatus) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Waktu Dibuat</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ $detailDate ? \Carbon\Carbon::parse($detailDate)->format('d F Y H:i:s') : '-' }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Isi Pesan</label>
                    <textarea wire:model="detailMessage" rows="6"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm sm:text-sm"
                        placeholder="Isi pesan..."></textarea>
                    @error('detailMessage') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    <p class="mt-1 text-xs text-gray-500">Anda dapat mengedit pesan ini sebelum mengirim ulang.</p>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showDetailModal', false)" wire:loading.attr="disabled">
                {{ __('Tutup') }}
            </x-secondary-button>

            <x-button class="ml-3" wire:click="resendFromModal" wire:loading.attr="disabled">
                {{ __('Simpan & Kirim Ulang') }}
            </x-button>
        </x-slot>
    </x-dialog-modal>

    <!-- Bulk Resend Confirmation Modal -->
    <x-confirmation-modal wire:model="confirmingBulkResend">
        <x-slot name="title">
            {{ __('Kirim Ulang Pesan Terpilih') }}
        </x-slot>

        <x-slot name="content">
            <p>{{ __('Apakah Anda yakin ingin mengirim ulang pesan-pesan yang dipilih? Status pesan akan diubah menjadi "pending" dan akan diproses oleh sistem.') }}</p>
            <p class="mt-2 font-bold text-indigo-600">{{ count($selectedMessages) }} pesan dipilih.</p>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('confirmingBulkResend', false)" wire:loading.attr="disabled">
                {{ __('Batal') }}
            </x-secondary-button>

            <x-button class="ml-3" wire:click="bulkResend" wire:loading.attr="disabled">
                {{ __('Kirim Ulang') }}
            </x-button>
        </x-slot>
    </x-confirmation-modal>

    <!-- Bulk Delete Confirmation Modal -->
    <x-confirmation-modal wire:model="confirmingBulkDelete">
        <x-slot name="title">
            {{ __('Hapus Pesan Terpilih') }}
        </x-slot>

        <x-slot name="content">
            <p>{{ __('Apakah Anda yakin ingin menghapus pesan-pesan yang dipilih? Tindakan ini tidak dapat dibatalkan.') }}</p>
            <p class="mt-2 font-bold text-red-600">{{ count($selectedMessages) }} pesan dipilih.</p>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('confirmingBulkDelete', false)" wire:loading.attr="disabled">
                {{ __('Batal') }}
            </x-secondary-button>

            <x-danger-button class="ml-3" wire:click="bulkDelete" wire:loading.attr="disabled">
                {{ __('Hapus Data') }}
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>
</div>