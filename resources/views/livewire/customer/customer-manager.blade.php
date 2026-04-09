<div x-data="{
    showModal: false,
    confirmShow: false,
    confirmAction: '',
    confirmId: null,
    confirmTitle: '',
    confirmMessage: '',
    showBulkDeleteModal: false,
    
    // Zero-delay ODP
    allOdps: [],
    clientNearbyOdps: [],

    init() {
        this.$watch('showModal', async value => {
            if (value) {
                this.$dispatch('modal-opened');
                
                // Fetch ODPs dynamically if not loaded
                if (this.allOdps.length === 0) {
                    this.allOdps = await $wire.getAllOdps();
                }

                // Instant update ODPs if coords exist (for Edit mode)
                const lat = $wire.latitude;
                const lng = $wire.longitude;
                if (lat && lng) {
                    this.filterNearbyOdps(lat, lng);
                }
            } else {
                this.clientNearbyOdps = [];
            }
        });

        // Watch for coordinate changes from Livewire
        this.$watch('$wire.latitude', value => {
            if (value && $wire.longitude) {
                this.filterNearbyOdps(value, $wire.longitude);
            }
        });
        this.$watch('$wire.longitude', value => {
            if (value && $wire.latitude) {
                this.filterNearbyOdps($wire.latitude, value);
            }
        });
    },

    filterNearbyOdps(lat, lng) {
        if (!lat || !lng) {
            this.clientNearbyOdps = [];
            return;
        }

        const radiusMeters = 150;
        const results = this.allOdps.map(odp => {
            const dist = this.calculateDistance(lat, lng, odp.latitude, odp.longitude);
            return { ...odp, distance: Math.round(dist) };
        })
        .filter(odp => odp.distance <= radiusMeters)
        .sort((a, b) => a.distance - b.distance)
        .slice(0, 10);

        this.clientNearbyOdps = results;
    },

    calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371e3; // metres
        const φ1 = lat1 * Math.PI/180;
        const φ2 = lat2 * Math.PI/180;
        const Δφ = (lat2-lat1) * Math.PI/180;
        const Δλ = (lon2-lon1) * Math.PI/180;

        const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                Math.cos(φ1) * Math.cos(φ2) *
                Math.sin(Δλ/2) * Math.sin(Δλ/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

        return R * c;
    },

    triggerConfirm(data) {
        this.confirmAction = data.action;
        this.confirmId = data.id;
        this.confirmTitle = data.title;
        this.confirmMessage = data.message;
        this.confirmShow = true;
    }
}" @keyup.escape.window="showModal = false; confirmShow = false; showBulkDeleteModal = false"
    @close-modal.window="showModal = false" @open-customer-modal.window="showModal = true"
    @update-nearby-odps.window="filterNearbyOdps($event.detail.lat, $event.detail.lng)">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-fullscreen@1.0.2/dist/Leaflet.fullscreen.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

    <!-- Individual Confirmation Modal -->
    <div x-show="confirmShow" x-cloak class="fixed z-[100] inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="confirmShow" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-900/60 transition-opacity backdrop-blur-sm" @click="confirmShow = false">
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div x-show="confirmShow" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white dark:bg-gray-900 rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-white/20 dark:border-gray-800/50">
                <div class="px-8 pt-8 pb-6 text-center">
                    <div
                        class="w-20 h-20 bg-amber-100 dark:bg-amber-900/30 rounded-3xl flex items-center justify-center text-amber-600 dark:text-amber-400 mx-auto mb-6">
                        <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 dark:text-gray-100 mb-2" x-text="confirmTitle"></h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400" x-text="confirmMessage"></p>
                </div>
                <div class="px-8 py-6 bg-gray-50 dark:bg-gray-800/50 flex flex-col sm:flex-row-reverse gap-3 border-t">
                    <button type="button" @click="$wire.call(confirmAction, confirmId).then(() => confirmShow = false)"
                        class="w-full inline-flex justify-center rounded-2xl bg-amber-600 text-white px-6 py-3 font-bold uppercase tracking-widest text-xs shadow-lg shadow-amber-500/20 hover:bg-amber-700 transition-all active:scale-95">Ya,
                        Lanjutkan</button>
                    <button type="button" @click="confirmShow = false"
                        class="w-full inline-flex justify-center rounded-2xl bg-white dark:bg-gray-900 border text-gray-700 dark:text-gray-300 px-6 py-3 font-bold uppercase tracking-widest text-xs hover:bg-gray-50 transition-all">Batal</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" @click="showModal = false">
                <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div
                class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full border border-gray-100 dark:border-gray-700/50">
                @if($showModal)
                <button @click="showModal = false" type="button"
                    class="absolute top-0 right-0 mt-4 mr-4 text-gray-400 hover:text-gray-500 focus:outline-none focus:text-gray-500 transition ease-in-out duration-150 z-10">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <form wire:submit="save">
                    <div
                        class="modal-scroll-content bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 max-h-[85vh] overflow-y-scroll text-left">
                        <div class="flex items-center justify-between mb-8">
                            <h3
                                class="text-xl font-black text-gray-900 dark:text-gray-100 tracking-tight flex items-center gap-3">
                                {{ $editMode ? 'Edit Pelanggan' : 'Tambah Pelanggan Baru' }}
                                <span
                                    class="px-3 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-[10px] font-black uppercase tracking-widest rounded-full border border-indigo-100 dark:border-indigo-800/50">
                                    {{ $customer_id }}
                                </span>
                            </h3>
                            <div class="h-1 w-20 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Left Column: Personal Information -->
                            <div class="space-y-6">
                                <div
                                    class="bg-gray-50/50 dark:bg-gray-900/30 p-6 rounded-2xl border border-gray-100 dark:border-gray-700/50">
                                    <h4
                                        class="text-xs font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Informasi Pribadi
                                    </h4>

                                    <div class="space-y-4">
                                        <div>
                                            <label
                                                class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Nama
                                                Lengkap <span class="text-red-500">*</span></label>
                                            <input wire:model="name" type="text"
                                                class="w-full px-4 py-3 bg-white dark:bg-gray-900 border-gray-100 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300">
                                            @error('name') <span
                                                class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label
                                                    class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">No.
                                                    WhatsApp <span class="text-red-500">*</span></label>
                                                <input wire:model="phone" type="text" placeholder="0812..."
                                                    class="w-full px-4 py-3 bg-white dark:bg-gray-900 border-gray-100 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300">
                                                @error('phone') <span
                                                    class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Email
                                                    (Opsional)</label>
                                                <input wire:model="email" type="email"
                                                    class="w-full px-4 py-3 bg-white dark:bg-gray-900 border-gray-100 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300">
                                            </div>
                                        </div>

                                        <div>
                                            <label
                                                class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">NIK/No.
                                                Identitas <span class="text-red-500">*</span></label>
                                            <input wire:model="identity_number" type="text"
                                                class="w-full px-4 py-3 bg-white dark:bg-gray-900 border-gray-100 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300">
                                            @error('identity_number') <span
                                                class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div>
                                            <label
                                                class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Tanggal
                                                Registrasi <span class="text-red-500">*</span></label>
                                            <input wire:model="registered_at" type="date"
                                                class="w-full px-4 py-3 bg-white dark:bg-gray-900 border-gray-100 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300">
                                            @error('registered_at') <span
                                                class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="bg-gray-50/50 dark:bg-gray-900/30 p-6 rounded-2xl border border-gray-100 dark:border-gray-700/50">
                                    <h4
                                        class="text-xs font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Alamat Pemasangan
                                    </h4>

                                    <div class="space-y-4">
                                        <div>
                                            <label
                                                class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Wilayah
                                                (Area) <span class="text-red-500">*</span></label>
                                            <x-searchable-select wire:model.live="village_id" wire:options="villages"
                                                wire:search="villageSearch" placeholder="Cari & Pilih Wilayah..." />
                                            @error('village_id') <span
                                                class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div>
                                            <label
                                                class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Alamat
                                                Lengkap <span class="text-red-500">*</span></label>
                                            <textarea wire:model="address" rows="3"
                                                class="w-full px-4 py-3 bg-white dark:bg-gray-900 border-gray-100 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300"></textarea>
                                            @error('address') <span
                                                class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Map & Technical -->
                            <div class="space-y-6">
                                <div
                                    class="bg-gray-50/50 dark:bg-gray-900/30 p-6 rounded-2xl border border-gray-100 dark:border-gray-700/50">
                                    <div class="flex items-center justify-between mb-4">
                                        <h4
                                            class="text-xs font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 9m0 11V9" />
                                            </svg>
                                            Titik Koordinat
                                        </h4>
                                        <div class="flex gap-2">
                                            <button type="button" onclick="getCurrentLocation()"
                                                class="p-1.5 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 text-indigo-600 hover:bg-indigo-50 transition-all">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <div id="map" class="w-full h-64 rounded-xl shadow-inner z-0" wire:ignore></div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label
                                                class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Latitude</label>
                                            <input wire:model="latitude" type="text" id="latitude"
                                                class="w-full px-4 py-3 bg-white dark:bg-gray-900 border-gray-100 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Longitude</label>
                                            <input wire:model="longitude" type="text" id="longitude"
                                                class="w-full px-4 py-3 bg-white dark:bg-gray-900 border-gray-100 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300">
                                        </div>
                                    </div>

                                    <!-- Near ODP Info -->

                                </div>

                                <!-- Info Box -->
                                <!-- ODP Nearby Widget -->
                                <div
                                    class="bg-gray-50/50 dark:bg-gray-900/30 p-4 rounded-2xl border border-gray-100 dark:border-gray-700/50">
                                    <div class="flex items-center gap-2 mb-3">
                                        <div
                                            class="w-7 h-7 bg-indigo-100 dark:bg-indigo-900/50 rounded-lg flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            </svg>
                                        </div>
                                        <h4
                                            class="text-xs font-black text-gray-700 dark:text-gray-300 uppercase tracking-widest">
                                            ODP Terdekat (Max 150m)
                                        </h4>
                                    </div>

                                    <template x-if="clientNearbyOdps.length > 0">
                                        <div class="space-y-2">
                                            <template x-for="odp in clientNearbyOdps" :key="odp.id">
                                                <div
                                                    class="flex items-center justify-between p-2.5 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm transition-all hover:border-indigo-200 dark:hover:border-indigo-800">
                                                    <div class="flex items-center gap-3">
                                                        <div class="text-left">
                                                            <p class="text-xs font-bold text-gray-900 dark:text-white"
                                                                x-text="odp.name"></p>
                                                            <div class="flex items-center gap-2 mt-0.5">
                                                                <span
                                                                    class="text-[10px] font-medium px-1.5 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded-md"
                                                                    x-text="odp.code"></span>
                                                                <span class="text-[10px] text-gray-400 font-medium"
                                                                    x-text="odp.distance + ' meter'"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="text-right">
                                                        <template x-if="odp.capacity">
                                                            <div class="flex flex-col items-end">
                                                                <span class="text-[11px] font-black"
                                                                    :class="odp.available_ports > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500'"
                                                                    x-text="odp.available_ports + '/' + odp.capacity"></span>
                                                                <span
                                                                    class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">Port
                                                                    Tersedia</span>
                                                            </div>
                                                        </template>
                                                        <template x-if="!odp.capacity">
                                                            <span class="text-[10px] text-gray-400">N/A</span>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="clientNearbyOdps.length === 0">
                                        <div
                                            class="py-6 flex flex-col items-center justify-center text-center px-4 bg-white/50 dark:bg-gray-800/50 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                                            <svg class="w-8 h-8 text-gray-300 dark:text-gray-600 mb-2" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <p
                                                class="text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                                                Tidak ada ODP terdekat</p>
                                            <p class="text-[10px] text-gray-400 mt-1 italic">Radius 150m dari titik peta
                                            </p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="bg-gray-50 dark:bg-gray-900/50 px-4 py-4 sm:px-6 sm:flex sm:flex-row-reverse gap-2 border-t border-gray-100 dark:border-gray-700/50">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-lg shadow-indigo-500/20 px-6 py-2.5 bg-indigo-600 text-xs font-bold uppercase tracking-widest text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto transition-all active:scale-95">
                            {{ $editMode ? 'Simpan' : 'Daftar' }}
                        </button>
                        <button type="button" @click="showModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm px-6 py-2.5 bg-white dark:bg-gray-800 text-xs font-bold uppercase tracking-widest text-gray-700 dark:text-gray-300 sm:mt-0 sm:ml-3 sm:w-auto transition-all">
                            Batal
                        </button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
    <!-- Detail Modal -->
    <div x-show="$wire.showDetailModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" @click="$wire.showDetailModal = false">
                <div class="absolute inset-0 bg-gray-500/75 dark:bg-gray-900/80 backdrop-blur-sm"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div
                class="relative inline-block align-bottom bg-white dark:bg-gray-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-gray-100 dark:border-gray-700/50 antialiased">

                @if($selectedCustomer)
                    <div class="bg-blue-600 px-6 py-6 text-white relative overflow-hidden">
                        <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                        <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-24 h-24 bg-blue-400/20 rounded-full blur-xl">
                        </div>

                        <div class="relative z-10 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-16 h-16 border border-white/20 bg-transparent flex items-center justify-center text-4xl font-extrabold">
                                    {{ substr($selectedCustomer->name, 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="text-2xl font-black tracking-tight flex items-center gap-2">
                                        {{ $selectedCustomer->name }}
                                    </h3>
                                    <p
                                        class="text-blue-100 font-bold uppercase tracking-widest text-[10px] bg-white/10 px-3 py-1 rounded-full w-fit mt-1.5 flex items-center gap-1">
                                        ID: {{ $selectedCustomer->customer_id }}
                                    </p>
                                </div>
                            </div>
                            <button @click="$wire.showDetailModal = false"
                                class="p-2 hover:bg-white/10 rounded-2xl transition-all">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto">
                        <!-- Info Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div
                                class="p-4 bg-gray-50 dark:bg-gray-900/40 rounded-xl border border-gray-100 dark:border-gray-700/50">
                                <p
                                    class="text-[9px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1.5">
                                    Telepon / WA
                                </p>
                                <p class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $selectedCustomer->phone }}
                                </p>
                            </div>
                            <div
                                class="p-4 bg-gray-50 dark:bg-gray-900/40 rounded-xl border border-gray-100 dark:border-gray-700/50">
                                <p
                                    class="text-[9px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1.5">
                                    Status Saat
                                    Ini</p>
                                <span
                                    class="px-2.5 py-1.5 rounded-md text-[10px] font-black uppercase tracking-widest
                                                    @if($selectedCustomer->status === 'active') bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400
                                                    @elseif($selectedCustomer->status === 'suspended') bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400
                                                    @else bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 @endif">
                                    {{ $selectedCustomer->status }}
                                </span>
                            </div>
                            <div
                                class="p-4 bg-gray-50 dark:bg-gray-900/40 rounded-xl border border-gray-100 dark:border-gray-700/50">
                                <p
                                    class="text-[9px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1.5">
                                    Alamat</p>
                                <p class="text-[11px] font-medium text-gray-600 dark:text-gray-400 leading-relaxed">
                                    {{ $selectedCustomer->address }}
                                </p>
                            </div>
                        </div>

                        <!-- History Section -->
                        <div>
                            <h4
                                class="text-[11px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Riwayat Berlangganan
                            </h4>

                            <div class="overflow-hidden rounded-xl border border-gray-100 dark:border-gray-700/50">
                                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700/50">
                                    <thead
                                        class="bg-gray-50/50 dark:bg-gray-900/40 border-b border-gray-100 dark:border-gray-700/50">
                                        <tr>
                                            <th
                                                class="px-5 py-3.5 text-left text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                                                Waktu</th>
                                            <th
                                                class="px-5 py-3.5 text-left text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                                                Tipe</th>
                                            <th
                                                class="px-5 py-3.5 text-left text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                                                Paket</th>
                                            <th
                                                class="px-5 py-3.5 text-left text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                                                Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                        @forelse($selectedCustomer->histories->sortByDesc('created_at') as $history)
                                            <tr
                                                class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors duration-200">
                                                <td
                                                    class="px-5 py-4 whitespace-nowrap text-[11px] font-bold text-gray-900 dark:text-gray-200">
                                                    {{ $history->created_at->format('d/m/Y H:i') }}
                                                </td>
                                                <td class="px-5 py-4 whitespace-nowrap">
                                                    <span
                                                        class="px-2.5 py-1.5 inline-flex text-[10px] leading-4 font-black rounded-md uppercase tracking-widest border
                                                                                        @if($history->type === 'upgrade') bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-500/30
                                                                                        @elseif($history->type === 'downgrade') bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/30 dark:text-amber-400 dark:border-amber-500/30
                                                                                        @elseif($history->type === 'termination') bg-red-50 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-500/30
                                                                                        @else bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-500/30 @endif">
                                                        {{ $history->type_label }}
                                                    </span>
                                                </td>
                                                <td
                                                    class="px-5 py-4 whitespace-nowrap text-[11px] text-gray-700 dark:text-gray-300">
                                                    @if($history->previous_package_id && $history->previous_package_id != $history->current_package_id)
                                                        <span
                                                            class="text-gray-400 dark:text-gray-500">{{ $history->previousPackage?->name }}</span>
                                                        <span class="text-blue-600/80 dark:text-blue-400/80 mx-1">→</span>
                                                        <span class="text-blue-600 dark:text-blue-400">
                                                            {{ $history->currentPackage?->name }}</span>
                                                    @else
                                                        <span
                                                            class="text-gray-600 dark:text-gray-400">{{ $history->currentPackage?->name ?? '-' }}</span>
                                                    @endif
                                                </td>
                                                <td
                                                    class="px-5 py-4 text-[11px] text-gray-500 dark:text-gray-400 max-w-xs leading-relaxed">
                                                    {{ $history->notes }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4"
                                                    class="px-5 py-8 text-center text-[11px] text-gray-500 dark:text-gray-400 italic border-t border-gray-100 dark:border-gray-700/50">
                                                    Belum ada riwayat perubahan.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
                <!-- Bottom Action Row -->
                <div class="bg-gray-50 dark:bg-gray-900/80 px-6 py-4 flex justify-end">
                    <button type="button" @click="$wire.showDetailModal = false"
                        class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm px-6 py-2.5 bg-white dark:bg-gray-800 text-[10px] font-black uppercase tracking-widest text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all dark:border-b-2 border-b-gray-200 dark:border-b-gray-900 active:border-b-0 active:translate-y-[2px]">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN WRAPPER -->
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-slot name="header">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Customer Manager') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Kelola data pelanggan, lokasi, dan informasi kontak
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
                                        placeholder="Cari Nama / ID / No HP..."
                                        class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 dark:bg-gray-900/50 border-gray-100 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 shadow-sm">
                                </div>

                                <div class="relative">
                                    <select wire:model.live="filterStatus"
                                        class="block w-full pl-3 pr-10 py-2.5 bg-gray-50 dark:bg-gray-900/50 border-gray-100 dark:border-gray-700/50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 shadow-sm cursor-pointer appearance-none">
                                        <option value="">Semua Status</option>
                                        <option value="active">Aktif</option>
                                        <option value="isolated">Terisolir</option>
                                        <option value="suspended">Ditangguhkan</option>
                                        <option value="terminated">Berhenti</option>
                                        <option value="inactive">Nonaktif</option>
                                    </select>
                                    <div
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
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
                                <span>Tambah Pelanggan</span>
                            </button>
                        </div>
                    </div>

                    <!-- Table with Bulk Select -->
                    <div class="overflow-x-auto"
                        wire:key="customer-table-{{ $customers->currentPage() }}-{{ $perPage }}-{{ $customers->total() }}"
                        x-data="{
                            selectedIds: [],
                            selectAll: false,
                            pageIds: @js($customers->pluck('id')->toArray()),

                            toggleSelectAll() {
                                this.selectedIds = this.selectAll ? [...this.pageIds] : [];
                            },

                            toggleItem(id) {
                                const idx = this.selectedIds.indexOf(id);
                                if (idx > -1) {
                                    this.selectedIds.splice(idx, 1);
                                } else {
                                    this.selectedIds.push(id);
                                }
                                this.selectAll = this.selectedIds.length === this.pageIds.length && this.pageIds.length > 0;
                            },

                            isSelected(id) {
                                return this.selectedIds.includes(id);
                            },

                            confirmBulkDelete() {
                                $wire.bulkDelete(this.selectedIds).then(() => {
                                    this.showBulkDeleteModal = false;
                                    this.selectedIds = [];
                                    this.selectAll = false;
                                });
                            }
                        }">
                        <!-- Bulk Delete Confirmation Modal -->
                        <div x-show="showBulkDeleteModal" x-cloak class="fixed z-[110] inset-0 overflow-y-auto">
                            <div
                                class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                <div x-show="showBulkDeleteModal" x-transition:enter="ease-out duration-300"
                                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    class="fixed inset-0 bg-gray-900/60 transition-opacity backdrop-blur-sm"
                                    @click="showBulkDeleteModal = false"></div>
                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                                <div x-show="showBulkDeleteModal" x-transition:enter="ease-out duration-300"
                                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                    x-transition:leave="ease-in duration-200"
                                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                    class="inline-block align-bottom bg-white dark:bg-gray-900 rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-white/20 dark:border-gray-800/50">
                                    <div class="px-8 pt-8 pb-6 text-center">
                                        <div
                                            class="w-20 h-20 bg-red-100 dark:bg-red-900/30 rounded-3xl flex items-center justify-center text-red-600 dark:text-red-400 mx-auto mb-6">
                                            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </div>
                                        <h3 class="text-2xl font-black text-gray-900 dark:text-gray-100 mb-2">Hapus
                                            <span x-text="selectedIds.length"></span> Item?
                                        </h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Apakah Anda yakin ingin
                                            menghapus
                                            pelanggan yang dipilih secara massal? Data yang dihapus tidak dapat
                                            dikembalikan.</p>
                                    </div>
                                    <div
                                        class="px-8 py-6 bg-gray-50 dark:bg-gray-800/50 flex flex-col sm:flex-row-reverse gap-3 border-t">
                                        <button type="button" @click="confirmBulkDelete()"
                                            class="w-full inline-flex justify-center rounded-2xl bg-red-600 text-white px-6 py-3 font-bold uppercase tracking-widest text-xs shadow-lg shadow-red-500/20 hover:bg-red-700 transition-all active:scale-95">Ya,
                                            Hapus Semua</button>
                                        <button type="button" @click="showBulkDeleteModal = false"
                                            class="w-full inline-flex justify-center rounded-2xl bg-white dark:bg-gray-900 border text-gray-700 dark:text-gray-300 px-6 py-3 font-bold uppercase tracking-widest text-xs hover:bg-gray-50 transition-all">Batal</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bulk Action Toolbar -->
                        <div x-show="selectedIds.length > 0" x-transition
                            class="flex items-center justify-between bg-indigo-600 text-white px-4 py-3 rounded-xl mb-4 shadow-lg shadow-indigo-500/30">
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-black uppercase tracking-widest"><span
                                        x-text="selectedIds.length"></span> Terpilih</span>
                            </div>
                            <button @click="showBulkDeleteModal = true"
                                class="px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-xs font-black uppercase tracking-widest transition-all">
                                Hapus Massal
                            </button>
                        </div>

                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700/50">
                            <thead
                                class="bg-gray-50/50 dark:bg-gray-900/30 border-b border-gray-100 dark:border-gray-700/50">
                                <tr>
                                    <th class="px-6 py-4 text-left w-12">
                                        <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()"
                                            class="rounded-lg border-gray-200 text-indigo-600 focus:ring-indigo-500 w-5 h-5 transition-all">
                                    </th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Pelanggan</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Paket & Status</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Lokasi</th>
                                    <th
                                        class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                @forelse($customers as $customer)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200 ">
                                        <td class="px-6 py-4">
                                            <input type="checkbox" :checked="isSelected({{ $customer->id }})"
                                                @change="toggleItem({{ $customer->id }})"
                                                class="rounded-lg border-gray-200 text-indigo-600 focus:ring-indigo-500 w-5 h-5 transition-all">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div
                                                    class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-indigo-500/30">
                                                    {{ substr($customer->name, 0, 1) }}
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-bold text-gray-900 dark:text-white">
                                                        {{ $customer->name }}
                                                    </div>
                                                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400">
                                                        {{ $customer->customer_id }} &bull; {{ $customer->phone }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-col items-start gap-1">
                                                @if($customer->activeSubscription)
                                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {{ $customer->activeSubscription->package?->name ?? 'N/A' }}
                                                    </span>
                                                @else
                                                    <span class="text-sm font-medium text-gray-500">Belum Berlangganan</span>
                                                @endif
                                                <span

                                                    class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-bold rounded-lg mt-1
                                                                    @if($customer->status === 'active') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400
                                                                    @elseif($customer->status === 'isolated' || $customer->status === 'suspended') bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400
                                                                    @elseif($customer->status === 'terminated') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                                                    @else bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400 @endif">
                                                    {{ $customer->status_label ?? ucfirst($customer->status) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $customer->area?->name ?? '-' }}
                                            </div>

                                            <div class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-[200px]"
                                                title="{{ $customer->address }}">
                                                {{ $customer->address }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end gap-2">
                                                @if(in_array($customer->status, ['active', 'isolated', 'suspended']))
                                                    <button @click="$wire.openModemModal({{ $customer->id }})"
                                                        class="p-2 rounded-lg text-emerald-600 hover:bg-emerald-50 hover:text-emerald-700 dark:text-emerald-400 dark:hover:bg-emerald-900/30 dark:hover:text-emerald-300 transition-colors duration-200"
                                                        title="Cek Modem OLT via SNMP">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                                                        </svg>
                                                    </button>
                                                @endif
                                                <button @click="$wire.viewCustomer({{ $customer->id }})"
                                                    class="p-2 rounded-lg text-blue-600 hover:bg-blue-50 hover:text-blue-700 dark:text-blue-400 dark:hover:bg-blue-900/30 dark:hover:text-blue-300 transition-colors duration-200"
                                                    title="Lihat Riwayat">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>
                                                <button @click="$wire.editCustomer({{ $customer->id }})"
                                                    class="p-2 rounded-lg text-indigo-600 hover:bg-indigo-50 hover:text-indigo-700 dark:text-indigo-400 dark:hover:bg-indigo-900/30 dark:hover:text-indigo-300 transition-colors duration-200"
                                                    title="Edit">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                        stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                    </svg>
                                                </button>
                                                <button type="button"
                                                    @click="triggerConfirm({ action: 'confirmDelete', id: {{ $customer->id }}, title: 'Hapus Pelanggan?', message: 'Apakah Anda yakin ingin menghapus data pelanggan ini? Data yang dihapus tidak dapat dikembalikan.' })"
                                                    class="p-2 rounded-lg text-red-600 hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-900/30 dark:hover:text-red-300 transition-colors duration-200"
                                                    title="Hapus">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                        stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">Tidak ada data
                                            pelanggan ditemukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-8">
                        {{ $customers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cek Modem Modal -->
    <div x-show="$wire.showModemModal" x-cloak class="fixed inset-0 z-[60] overflow-y-auto"
        x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" @click="$wire.showModemModal = false">
                <div class="absolute inset-0 bg-gray-500/80 dark:bg-gray-900/90 backdrop-blur-sm"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-gray-100 dark:border-gray-700/50">

                @if($selectedCustomer)
                    <div
                        class="bg-indigo-600 dark:bg-indigo-900 px-6 py-5 border-b border-indigo-700 dark:border-indigo-800 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-black text-white flex items-center gap-2">
                                <svg class="w-5 h-5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                                </svg>
                                Cek OLT / Modem
                            </h3>
                            <p class="text-indigo-200 text-xs mt-1 font-medium">{{ $selectedCustomer->name }} &bull;
                                {{ $selectedCustomer->customer_id }}</p>
                        </div>
                        <button type="button" @click="$wire.showModemModal = false"
                            class="text-indigo-200 hover:text-white transition-colors bg-indigo-700/50 hover:bg-indigo-700 rounded-lg p-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-6">
                        <div
                            class="p-6 bg-gray-50 dark:bg-gray-900/40 rounded-xl border border-gray-100 dark:border-gray-700/50">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h4
                                        class="text-[11px] font-black text-gray-700 dark:text-gray-300 uppercase tracking-widest">
                                        Status Koneksi Modem (SNMP)</h4>
                                    <p class="text-[9px] text-indigo-500 font-bold uppercase tracking-widest mt-1"
                                        wire:loading wire:target="checkModemStatus">Sedang menghubungi OLT...</p>
                                </div>
                                <button wire:click="checkModemStatus" wire:loading.attr="disabled"
                                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 border border-transparent rounded-lg text-xs font-bold shadow text-white transition-all flex items-center gap-2 disabled:opacity-50">
                                    <svg wire:loading.class="animate-spin" wire:target="checkModemStatus" class="w-4 h-4"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    <span>Cek Sekarang</span>
                                </button>
                            </div>

                            <div class="relative">
                                <div wire:loading wire:target="checkModemStatus"
                                    class="absolute inset-0 bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm z-10 flex items-center justify-center rounded-lg">
                                </div>
                                @if($modemStatus)
                                    <div
                                        class="grid grid-cols-2 md:grid-cols-2 gap-4 p-5 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                                        <div>
                                            <span
                                                class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-2">State
                                                Pendaftaran OLT</span>
                                            <div class="flex items-center gap-2">
                                                @if($modemStatus['status'] === 'Online')
                                                    <span class="relative flex h-3 w-3"><span
                                                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span><span
                                                            class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span></span>
                                                @elseif($modemStatus['status'] === 'LOS')
                                                    <span class="relative flex h-3 w-3"><span
                                                            class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span></span>
                                                @else
                                                    <span class="relative flex h-3 w-3"><span
                                                            class="relative inline-flex rounded-full h-3 w-3 bg-gray-500"></span></span>
                                                @endif
                                                <span class="px-3 py-1.5 rounded-lg text-xs font-black uppercase tracking-widest
                                                                @if($modemStatus['status'] === 'Online') bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800/50
                                                                @elseif($modemStatus['status'] === 'LOS') bg-red-50 text-red-700 border border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800/50
                                                                @else bg-gray-50 text-gray-700 border border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700 @endif
                                                            ">
                                                    {{ $modemStatus['status'] }}
                                                </span>
                                            </div>
                                        </div>
                                        <div>
                                            <span
                                                class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Optical
                                                Power (Rx)</span>
                                            <div
                                                class="mt-1 flex items-baseline gap-1.5 text-2xl font-black text-gray-900 dark:text-gray-100">
                                                {{ explode(' ', $modemStatus['rx_power'])[0] }}
                                                @if($modemStatus['rx_power'] !== '-')
                                                    <span class="text-sm text-gray-500 font-bold">dBm</span>
                                                @endif
                                            </div>
                                        </div>
                                        @if($modemStatus['error'])
                                            <div class="col-span-2 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                                <span
                                                    class="text-[9px] font-black text-red-400 uppercase tracking-widest block mb-2 block flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                    </svg>
                                                    Log Diagnostik SNMP
                                                </span>
                                                <p
                                                    class="text-xs font-mono text-red-500 bg-red-50 dark:bg-red-900/10 p-3 rounded-lg border border-red-100 dark:border-red-900/30 w-full overflow-x-auto">
                                                    {{ $modemStatus['error'] }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div
                                        class="text-center py-8 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 border-dashed dark:border-gray-700 shadow-sm flex flex-col items-center justify-center">
                                        <div
                                            class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-300 dark:text-indigo-500 rounded-full flex items-center justify-center mb-3">
                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                                            </svg>
                                        </div>
                                        <p class="text-xs uppercase tracking-widest font-black text-gray-400">Tekan "Cek
                                            Sekarang" untuk mengambil data redaman real-time dari OLT.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-gray-50 dark:bg-gray-800/80 px-6 py-4 border-t border-gray-100 dark:border-gray-700/50 flex justify-end">
                        <button type="button" @click="$wire.showModemModal = false"
                            class="px-6 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl text-xs font-bold uppercase tracking-widest text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all shadow-sm">
                            Tutup Panel
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Map Scripts -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-fullscreen@1.0.2/dist/Leaflet.fullscreen.min.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

    <script>
        let map, marker;

        document.addEventListener('livewire:initialized', () => {
            // Map initialization is now handled by modal events and Alpine

            @this.on('customer-saved', () => {
                // showModal = false handled by Alpine @click
            });

            @this.on('refresh-map', (data) => {
                setTimeout(() => {
                    initMap(data.lat, data.lng);
                }, 100);
            });
        });

        function initMap(initialLat, initialLng) {
            const mapContainer = document.getElementById('map');
            if (!mapContainer) return;

            if (map) {
                map.remove();
            }

            // Get coordinates from params or Livewire or defaults
            const lat = initialLat || @this.get('latitude') || {{ $mapLat ?: -6.2 }};
            const lng = initialLng || @this.get('longitude') || {{ $mapLng ?: 106.8 }};

            map = L.map('map', {
                fullscreenControl: true
            }).setView([lat, lng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            const geocoder = L.Control.Geocoder.nominatim();
            const geocoderControl = L.Control.geocoder({
                defaultMarkGeocode: false,
                geocoder: geocoder
            })
                .on('markgeocode', function (e) {
                    const center = e.geocode.center;
                    updateMarker(center.lat, center.lng, true);
                })
                .addTo(map);

            marker = L.marker([lat, lng], {
                draggable: true
            }).addTo(map);

            marker.on('dragend', function (e) {
                const position = marker.getLatLng();
                updateMarker(position.lat, position.lng, true);
            });

            map.on('click', function (e) {
                updateMarker(e.latlng.lat, e.latlng.lng, true);
            });

            // Force recalculate size because modal might be finishing transition
            setTimeout(() => {
                map.invalidateSize();
            }, 300);
        }

        function updateMarker(lat, lng, doReverseGeocode = false) {
            if (!marker || !map) {
                initMap(lat, lng);
                return;
            };
            const newPos = new L.LatLng(lat, lng);
            marker.setLatLng(newPos);
            map.panTo(newPos);
            @this.updateLocation(lat, lng);
        }

        function getCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    updateMarker(position.coords.latitude, position.coords.longitude);
                });
            }
        }

        window.addEventListener('modal-opened', event => {
            setTimeout(() => {
                if (!map) {
                    initMap();
                } else {
                    // Update map to current Livewire coordinates if they exist
                    const lat = @this.get('latitude');
                    const lng = @this.get('longitude');
                    if (lat && lng) {
                        updateMarker(lat, lng);
                    }
          map.invalidateSize();
                }
            }, 300);
        });
    </script>
</div>