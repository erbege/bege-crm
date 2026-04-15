<div x-data="{ showFormModal: false, showDeleteModal: false, showCustomerModal: false }"
    @open-modal.window="showFormModal = true" @open-delete-modal.window="showDeleteModal = true"
    @open-customer-modal.window="showCustomerModal = true"
    @close-modal.window="showFormModal = false; showDeleteModal = false; showCustomerModal = false;">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Titik Coverage') }}
        </h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ __('Halaman ini digunakan untuk mengelola titik coverage') }}
        </p>
    </x-slot>

    @push('styles')
        <!-- Leaflet CSS - Load globally -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <link rel="stylesheet" href="https://unpkg.com/leaflet-fullscreen@1.0.2/dist/Leaflet.fullscreen.css" />
        <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    @endpush

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Flash Messages -->


            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                        <div class="flex flex-col sm:flex-row gap-4 flex-1 w-full">
                            <input wire:model.live.debounce.300ms="search" type="search" placeholder="Cari titik..."
                                class="w-full sm:w-48 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <select wire:model.live="typeFilter"
                                class="w-full sm:w-36 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua Tipe</option>
                                <option value="odp">ODP</option>
                                <option value="odc">ODC</option>
                                <option value="olt">OLT</option>
                                <option value="pole">Tiang</option>
                            </select>
                            <select wire:model.live="areaFilter"
                                class="w-full sm:w-48 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua Wilayah</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}">{{ $area->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button @click="showFormModal = true; $wire.openModal()"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                            Tambah Titik
                        </button>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Nama</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Kode</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Tipe</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Wilayah</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Kapasitas</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Koordinat</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($points as $point)
                                    <tr>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $point->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <code
                                                class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $point->code }}</code>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                                                                                        @if($point->type === 'odp') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                                                                                        @elseif($point->type === 'odc') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                                                                                        @elseif($point->type === 'olt') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                                                                                        @else bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                                                                                                        @endif">
                                                {{ $point->type_label }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $point->area->name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            @if($point->capacity)
                                                <button
                                                    @click="showCustomerModal = true; $wire.showConnectedCustomers({{ $point->id }})"
                                                    class="hover:underline cursor-pointer @if($point->available_ports <= 2) text-red-600 dark:text-red-400 @elseif($point->available_ports <= 5) text-yellow-600 dark:text-yellow-400 @else text-green-600 dark:text-green-400 @endif">
                                                    {{ $point->used_ports }}/{{ $point->capacity }}
                                                </button>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <a href="https://maps.google.com/?q={{ $point->latitude }},{{ $point->longitude }}"
                                                target="_blank"
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                {{ number_format($point->latitude, 5) }},
                                                {{ number_format($point->longitude, 5) }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($point->is_active)
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Aktif</span>
                                            @else
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button @click="showFormModal = true; $wire.editPoint({{ $point->id }})"
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-2"
                                                title="Edit">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                </svg>
                                            </button>
                                            <button @click="showDeleteModal = true; $wire.confirmDelete({{ $point->id }})"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                                title="Hapus">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            Tidak ada data titik coverage.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $points->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Connected Customers Modal -->
    <div x-show="showCustomerModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity"
                @click="showCustomerModal = false; $wire.closeCustomerModal()">
                <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Pelanggan Terhubung: {{ $selectedPoint?->name }} ({{ $selectedPoint?->code }})
                        </h3>
                        <button @click="showCustomerModal = false; $wire.closeCustomerModal()"
                            class="text-gray-400 hover:text-gray-500">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Pelanggan</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Paket</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Kontak</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($connectedCustomers as $customer)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $customer->name }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $customer->customer_id }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                {{ $customer->activeSubscription?->package?->name ?? '-' }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $customer->activeSubscription?->getFormattedTotalAttribute() ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">{{ $customer->phone }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $customer->address }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $customer->status_color }}">
                                                {{ $customer->status_label }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            Tidak ada pelanggan yang terhubung.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="showCustomerModal = false; $wire.closeCustomerModal()"
                        class="w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Create/Edit Modal -->
    <div x-show="showFormModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-data="{ 
                                                 map: null, 
                                                 marker: null,

                                                 init() {
                                                     window.addEventListener('open-modal', () => {
                                                         setTimeout(() => {
                                                             this.updateMapState();
                                                         }, 100);
                                                     });
                                                 },
                                                 
                                                 updateMapState() {
                                                     if (!this.map) {
                                                         this.initMap();
                                                     } else {
                                                         this.map.invalidateSize();
                                                     }

                                                     // Use $wire proxy to get current values (wait, $wire.latitude might be async in some contexts)
                                                     // But usually $wire.latitude works.
                                                     // Let's use get if available or property access.
                                                     
                                                     const lat = $wire.latitude;
                                                     const lng = $wire.longitude;
                                                     
                                                     if (lat && lng) {
                                                         const newLat = parseFloat(lat);
                                                         const newLng = parseFloat(lng);
                                                         this.map.setView([newLat, newLng], 18);
                                                         
                                                         if (this.marker) {
                                                             this.marker.setLatLng([newLat, newLng]);
                                                         } else {
                                                             this.marker = L.marker([newLat, newLng]).addTo(this.map);
                                                         }
                                                     }
                                                 },

                                                 locateMe() {
                                                     if (!navigator.geolocation) {
                                                         alert('Geolocation tidak didukung oleh browser Anda.');
                                                         return;
                                                     }
                                                     
                                                     navigator.geolocation.getCurrentPosition(
                                                         (position) => {
                                                             const lat = position.coords.latitude;
                                                             const lng = position.coords.longitude;
                                                             
                                                             this.map.setView([lat, lng], 18);
                                                             
                                                             if (this.marker) {
                                                                 this.marker.setLatLng([lat, lng]);
                                                             } else {
                                                                 this.marker = L.marker([lat, lng]).addTo(this.map);
                                                             }
                                                             
                                                             @this.set('latitude', lat.toFixed(7));
                                                             @this.set('longitude', lng.toFixed(7));
                                                         },
                                                         (error) => {
                                                             console.error('Geolocation error:', error);
                                                             alert('Tidak dapat mengambil lokasi Anda. Pastikan izin lokasi diaktifkan.');
                                                         },
                                                         { enableHighAccuracy: true }
                                                     );
                                                 },
                                                 initMap() {
                                                     this.$nextTick(() => {
                                                         // Delay to ensure modal transition is somewhat ready
                                                         setTimeout(() => {
                                                             const mapContainer = this.$refs.mapContainer;
                                                             if (!mapContainer || this.map) return;

                                                             const lat = {{ $latitude ?: $mapLat }};
                                                             const lng = {{ $longitude ?: $mapLng }};

                                                             this.map = L.map(mapContainer, {
                                                                 fullscreenControl: true,
                                                                 fullscreenControlOptions: {
                                                                     position: 'topright'
                                                                 }
                                                             }).setView([lat, lng], {{ $mapZoom }});

                                                             L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                                                 attribution: '© OpenStreetMap'
                                                             }).addTo(this.map);

                                                             // Add Geocoder Control
                                                             const geocoder = L.Control.geocoder({
                                                                 defaultMarkGeocode: false,
                                                                 position: 'topleft',
                                                                 placeholder: 'Cari lokasi...',
                                                                 errorMessage: 'Tidak ditemukan.'
                                                             }).addTo(this.map);
                                                             
                                                             geocoder.on('markgeocode', (e) => {
                                                                 const resultLat = e.geocode.center.lat;
                                                                 const resultLng = e.geocode.center.lng;
                                                                 
                                                                 this.map.setView([resultLat, resultLng], 16);
                                                                 
                                                                 if (this.marker) {
                                                                     this.marker.setLatLng([resultLat, resultLng]);
                                                                 } else {
                                                                     this.marker = L.marker([resultLat, resultLng]).addTo(this.map);
                                                                 }
                                                                 
                                                                 @this.set('latitude', resultLat.toFixed(7));
                                                                 @this.set('longitude', resultLng.toFixed(7));
                                                             });
                                                             


                                                             // Add initial marker if coordinates exist
                                                             @if($latitude && $longitude)
                                                                 this.marker = L.marker([{{ $latitude }}, {{ $longitude }}]).addTo(this.map);
                                                             @endif

                                                             this.map.on('click', (e) => {
                                                                 const clickLat = e.latlng.lat.toFixed(7);
                                                                 const clickLng = e.latlng.lng.toFixed(7);

                                                                 if (this.marker) {
                                                                     this.marker.setLatLng(e.latlng);
                                                                 } else {
                                                                     this.marker = L.marker(e.latlng).addTo(this.map);
                                                                 }

                                                                 @this.set('latitude', clickLat);
                                                                 @this.set('longitude', clickLng);
                                                             });

                                                             // Critical: Invalidate size multiple times to catch transition end
                                                             [100, 300, 500].forEach(delay => {
                                                                 setTimeout(() => {
                                                                     this.map.invalidateSize();
                                                                 }, delay);
                                                             });
                                                         }, 100);
                                                     });
                                                 }
                                             }" x-init="initMap()">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" @click="showFormModal = false; $wire.closeModal()">
                <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <form wire:submit="save">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            {{ $editMode ? 'Edit Titik Coverage' : 'Tambah Titik Coverage Baru' }}
                        </h3>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Left Column - Form -->
                            <div class="space-y-4">
                                <div x-data="{
                                                            open: false,
                                                            search: '',
                                                            selectedId: @entangle('area_id'),
                                                            selectedName: '',
                                                            options: {{ $areas->map(fn($a) => ['id' => $a->id, 'name' => $a->name])->values()->toJson() }},

                                                            init() {
                                                                this.initSelected();
                                                                this.$watch('selectedId', value => this.initSelected());
                                                            },

                                                            initSelected() {
                                                                if (this.selectedId) {
                                                                    const option = this.options.find(o => o.id == this.selectedId);
                                                                    if (option) {
                                                                        this.selectedName = option.name;
                                                                    }
                                                                } else {
                                                                    this.selectedName = '';
                                                                }
                                                            },

                                                            get filteredOptions() {
                                                                if (this.search === '') {
                                                                    return this.options;
                                                                }
                                                                return this.options.filter(option => 
                                                                    option.name.toLowerCase().includes(this.search.toLowerCase())
                                                                );
                                                            },

                                                            selectOption(option) {
                                                                this.selectedId = option.id;
                                                                this.selectedName = option.name;
                                                                this.open = false;
                                                                this.search = '';
                                                            }
                                                        }" class="relative">
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Wilayah
                                        *</label>

                                    <!-- Trigger Input -->
                                    <div class="relative">
                                        <input type="text" x-model="selectedName" @click="open = true; search = '';"
                                            @keydown.escape="open = false" readonly placeholder="Pilih Wilayah"
                                            class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 cursor-pointer">
                                        <div
                                            class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd"
                                                    d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Dropdown Menu -->
                                    <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="transform opacity-0 scale-95"
                                        x-transition:enter-end="transform opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="transform opacity-100 scale-100"
                                        x-transition:leave-end="transform opacity-0 scale-95" @click.away="open = false"
                                        class="absolute z-50 mt-1 w-full bg-white dark:bg-gray-800 shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
                                        style="display: none;">
                                        <!-- Search Input inside Dropdown -->
                                        <div
                                            class="sticky top-0 z-10 bg-white dark:bg-gray-800 p-2 border-b border-gray-200 dark:border-gray-700">
                                            <input type="text" x-model="search" x-ref="searchInput"
                                                placeholder="Cari wilayah..."
                                                class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                @keydown.enter.prevent>
                                        </div>

                                        <ul>
                                            <template x-for="option in filteredOptions" :key="option.id">
                                                <li @click="selectOption(option)"
                                                    class="text-gray-900 dark:text-gray-100 cursor-default select-none relative py-2 pl-3 pr-9 hover:bg-indigo-600 hover:text-white">
                                                    <span x-text="option.name"
                                                        class="font-normal block truncate"></span>

                                                    <!-- Checkmark for selected -->
                                                    <span x-show="option.id == selectedId"
                                                        class="text-indigo-600 hover:text-white absolute inset-y-0 right-0 flex items-center pr-4">
                                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                                            viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd"
                                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    </span>
                                                </li>
                                            </template>
                                            <li x-show="filteredOptions.length === 0"
                                                class="text-gray-500 dark:text-gray-400 cursor-default select-none relative py-2 pl-3 pr-9">
                                                Tidak ada hasil.
                                            </li>
                                        </ul>
                                    </div>
                                    @error('area_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama
                                            *</label>
                                        <input type="text" wire:model="name"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kode
                                            *</label>
                                        <input type="text" wire:model="code"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('code') <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipe
                                        *</label>
                                    <select wire:model="type"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="odp">ODP</option>
                                        <option value="odc">ODC</option>
                                        <option value="olt">OLT</option>
                                        <option value="pole">Tiang</option>
                                    </select>
                                    @error('type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kapasitas
                                            Port</label>
                                        <input type="number" wire:model="capacity" min="0"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('capacity') <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Port
                                            Terpakai</label>
                                        <input type="number" wire:model="used_ports" min="0"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('used_ports') <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Latitude
                                            *</label>
                                        <input type="number" step="any" wire:model="latitude" placeholder="-6.2088"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('latitude') <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Longitude
                                            *</label>
                                        <input type="number" step="any" wire:model="longitude" placeholder="106.8456"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('longitude') <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat</label>
                                    <textarea wire:model="address" rows="2"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                    @error('address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" wire:model="is_active" id="is_active"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:border-gray-700 dark:bg-gray-900">
                                    <label for="is_active"
                                        class="ml-2 text-sm text-gray-700 dark:text-gray-300">Aktif</label>
                                </div>
                            </div>

                            <!-- Right Column - Map -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih
                                    Lokasi di Peta</label>
                                <div wire:ignore x-ref="mapContainer" id="map-picker"
                                    class="h-80 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 relative z-0">
                                </div>
                                <div class="mt-2 flex justify-between items-center">
                                    <button type="button" @click="locateMe()"
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 dark:bg-indigo-900 dark:text-indigo-200 dark:hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                        <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Lokasi Saya
                                    </button>
                                </div>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Klik pada peta untuk memilih
                                    koordinat</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            {{ $editMode ? 'Simpan' : 'Tambah' }}
                        </button>
                        <button type="button" @click="showFormModal = false; $wire.closeModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal (Inside Root Div) -->
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" @click="showDeleteModal = false; $wire.closeDeleteModal()">
                <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Hapus Titik
                                Coverage</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Apakah Anda yakin ingin menghapus
                                    titik coverage ini?</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="deletePoint"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Hapus
                    </button>
                    <button @click="showDeleteModal = false; $wire.closeDeleteModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <!-- Leaflet JS - Load globally -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-fullscreen@1.0.2/dist/Leaflet.fullscreen.min.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

@endpush