<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Peta Coverage') }}
        </h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ __('Halaman ini digunakan untuk menampilkan peta coverage') }}
        </p>
    </x-slot>

    <div>
        <!-- Leaflet Assets -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
        <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
        <link rel="stylesheet" href="https://unpkg.com/leaflet-fullscreen@1.0.2/dist/leaflet.fullscreen.css" />

        <style>
            .marker-odp {
                background-color: #3B82F6;
            }

            .marker-odc {
                background-color: #10B981;
            }

            .marker-olt {
                background-color: #EF4444;
            }

            .marker-pole {
                background-color: #F97316;
            }

            .custom-marker {
                width: 30px;
                height: 30px;
                border-radius: 50%;
                border: 3px solid white;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: bold;
                font-size: 10px;
            }
        </style>

        <div class="py-6">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <!-- Filter Bar -->
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex flex-wrap items-center gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Filter
                                Tipe</label>
                            <select wire:model.live="typeFilter"
                                class="w-40 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">Semua Tipe</option>
                                <option value="odp">ODP</option>
                                <option value="odc">ODC</option>
                                <option value="olt">OLT</option>
                                <option value="pole">Tiang</option>
                            </select>
                        </div>

                        <!-- Legend -->
                        <div class="flex items-center gap-4 ml-auto text-sm">
                            <span class="flex items-center gap-1">
                                <span class="w-4 h-4 rounded-full bg-blue-500"></span>
                                <span class="text-gray-600 dark:text-gray-400">ODP</span>
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="w-4 h-4 rounded-full bg-green-500"></span>
                                <span class="text-gray-600 dark:text-gray-400">ODC</span>
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="w-4 h-4 rounded-full bg-red-500"></span>
                                <span class="text-gray-600 dark:text-gray-400">OLT</span>
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="w-4 h-4 rounded-full bg-orange-500"></span>
                                <span class="text-gray-600 dark:text-gray-400">Tiang</span>
                            </span>
                            <span
                                class="text-gray-500 dark:text-gray-400 border-l pl-4 border-gray-300 dark:border-gray-600">
                                Total: {{ count($points) }} titik
                            </span>
                        </div>
                    </div>

                    <!-- Map Container -->
                    <div id="coverage-map" class="h-[calc(100vh-250px)] min-h-[500px]" wire:ignore></div>
                </div>
            </div>
        </div>

        <!-- Leaflet JS -->
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
        <script src="https://unpkg.com/leaflet-fullscreen@1.0.2/dist/Leaflet.fullscreen.min.js"></script>

        <script>
            document.addEventListener('livewire:navigated', initCoverageMap);
            document.addEventListener('DOMContentLoaded', initCoverageMap);

            let coverageMap = null;
            let markersCluster = null;

            function initCoverageMap(pointsToUse = null) {
                if (coverageMap) {
                    coverageMap.remove();
                }

                // Use passed points or initial points from Blade
                const points = pointsToUse || @json($points);

                // Default center (Indonesia)
                let centerLat = -2.5489;
                let centerLng = 118.0149;
                let zoom = 5;

                // Rest of the existing points-based logic...
                if (points.length > 0) {
                    centerLat = points[0].latitude;
                    centerLng = points[0].longitude;
                    zoom = 13;
                }

                coverageMap = L.map('coverage-map', {
                    fullscreenControl: true,
                    fullscreenControlOptions: {
                        position: 'topright'
                    }
                }).setView([centerLat, centerLng], zoom);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(coverageMap);

                markersCluster = L.markerClusterGroup();

                const markerColors = {
                    'odp': '#3B82F6',
                    'odc': '#10B981',
                    'olt': '#EF4444',
                    'pole': '#F97316'
                };

                const markerLetters = {
                    'odp': 'P',
                    'odc': 'C',
                    'olt': 'L',
                    'pole': 'T'
                };

                points.forEach(point => {
                    const color = markerColors[point.type] || '#6B7280';
                    const letter = markerLetters[point.type] || point.typeLabel.charAt(0);

                    const icon = L.divIcon({
                        className: 'custom-div-icon',
                        html: `<div class="custom-marker" style="background-color: ${color}">${letter}</div>`,
                        iconSize: [30, 30],
                        iconAnchor: [15, 15]
                    });

                    const marker = L.marker([point.latitude, point.longitude], {
                        icon: icon
                    });

                    let popupContent = `
                        <div class="text-sm">
                            <h3 class="font-bold text-base">${point.name}</h3>
                            <p class="text-gray-600"><strong>Kode:</strong> ${point.code}</p>
                            <p class="text-gray-600"><strong>Tipe:</strong> ${point.typeLabel}</p>
                            <p class="text-gray-600"><strong>Wilayah:</strong> ${point.area}</p>
                    `;

                    if (point.capacity) {
                        const percentage = ((point.usedPorts / point.capacity) * 100).toFixed(0);
                        popupContent += `
                            <p class="text-gray-600"><strong>Kapasitas:</strong> ${point.usedPorts}/${point.capacity} (${percentage}% terpakai)</p>
                        `;
                    }

                    if (point.address) {
                        popupContent += `<p class="text-gray-600"><strong>Alamat:</strong> ${point.address}</p>`;
                    }

                    popupContent += `
                        <p class="text-gray-500 text-xs mt-2">
                            <a href="https://maps.google.com/?q=${point.latitude},${point.longitude}" target="_blank" class="text-indigo-600 hover:underline">
                                Buka di Google Maps
                            </a>
                        </p>
                    </div>`;

                    marker.bindPopup(popupContent);
                    markersCluster.addLayer(marker);
                });

                coverageMap.addLayer(markersCluster);

                if (points.length > 0) {
                    const group = new L.featureGroup(points.map(p => L.marker([p.latitude, p.longitude])));
                    coverageMap.fitBounds(group.getBounds().pad(0.1));
                }
            }

            window.addEventListener('points-updated', event => {
                initCoverageMap(event.detail.points);
            });
        </script>
    </div>
</div>