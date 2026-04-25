<?php

namespace App\Livewire\Customer;

use App\Models\Area;
use App\Models\CoveragePoint;
use App\Models\Customer;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class CustomerManager extends Component
{
    use WithPagination;

    // Search & Filter - with debounce for performance
    public $search = '';
    public $filterStatus = '';
    public $perPage = 10;

    /**
     * Debounce search updates by 300ms to reduce queries.
     */
    public function updatedSearch($value)
    {
        $this->resetPage();
    }

    public function updatedVillageSearch($value)
    {
        if (strlen($value) >= 2) {
            $this->villages = Area::where('type', 'village')
                ->where('name', 'like', '%' . $value . '%')
                ->limit(50)
                ->get(['id', 'name'])
                ->toArray();
        } elseif (empty($value)) {
            // Restore defaults
            $this->villages = Area::where('type', 'village')
                ->limit(100)
                ->get(['id', 'name'])
                ->toArray();
        }
    }

    public function updateLocation($lat, $lng)
    {
        $this->latitude = $lat;
        $this->longitude = $lng;

        // Perform reverse geocoding on the backend separately to avoid blocking the UI
        $this->dispatch('refresh-address', lat: $lat, lng: $lng);
    }

    #[On('refresh-address')]
    public function refreshAddress($lat, $lng)
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'SKNET-CRM/1.0'
            ])->get("https://nominatim.openstreetmap.org/reverse", [
                        'format' => 'jsonv2',
                        'lat' => $lat,
                        'lon' => $lng,
                    ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['display_name'])) {
                    $this->address = $data['display_name'];
                }
            }
        } catch (\Exception $e) {
            Log::error("Backend geocoding failed: " . $e->getMessage());
        }
    }

    // Modal State
    public $editMode = false;
    public $showModal = false;
    public $showDetailModal = false;
    public $customerToDeleteId = null;
    public $selectedCustomer = null;

    // Form Fields
    public $customer_id; // Generated ID
    public $db_id; // Database ID for update
    public $name;
    public $identity_number;
    public $email;
    public $phone;
    public $address;
    public $registered_at;
    public $notes;

    // Modem/OLT Status
    public $modemStatus = null;
    public $isLoadingModemStatus = false;

    // Area - readonly text from map (province, city, district)
    public $province_name = '';
    public $city_name = '';
    public $district_name = '';
    public $district_id = null; // Used to filter villages
    public $village_id; // This is the actual area_id

    // Location
    public $latitude;
    public $longitude;

    // Village dropdown list
    public $villages = [];

    // Village search query (for searchable select)
    public $villageSearch = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'identity_number' => 'required|string|max:20',
        'email' => 'nullable|email|max:255',
        'phone' => 'required|string|max:20',
        'address' => 'required|string',
        'village_id' => 'required|exists:areas,id',
        'registered_at' => 'required|date',
    ];

    protected $messages = [
        'name.required' => 'Nama wajib diisi.',
        'phone.required' => 'No. HP wajib diisi.',
        'address.required' => 'Alamat lengkap wajib diisi.',
        'village_id.required' => 'Wilayah (Area) harus dipilih.',
        'village_id.exists' => 'Wilayah yang dipilih tidak valid.',
        'registered_at.required' => 'Tanggal registrasi wajib diisi.',
    ];

    public function mount()
    {
        $this->registered_at = date('Y-m-d');

        // Pre-load some initial villages for the dropdown
        $this->villages = Area::where('type', 'village')
            ->limit(100)
            ->get(['id', 'name'])
            ->toArray();

        // Pre-load setting values to avoid N+1 in loop
        if (!Cache::has('settings:map_defaults')) {
            Cache::put('settings:map_defaults', [
                'lat' => Setting::get('general.map_latitude', -6.200000),
                'lng' => Setting::get('general.map_longitude', 106.816666),
                'zoom' => Setting::get('general.map_zoom', 13),
            ], 3600);
        }
    }

    public function render()
    {
        $customers = Customer::query()
            ->select(['customers.*'])
            ->with(['area:id,name,code', 'activeSubscription.package:id,name,price', 'activeSubscription.coveragePoint:id,name,code'])
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('customer_id', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus, function ($q) {
                // Filter by status based on subscription
                if ($this->filterStatus === 'active') {
                    $q->active();
                } elseif ($this->filterStatus === 'isolated') {
                    $q->isolated();
                } elseif ($this->filterStatus === 'suspended') {
                    $q->suspended();
                } elseif ($this->filterStatus === 'terminated') {
                    $q->terminated();
                } elseif ($this->filterStatus === 'inactive') {
                    $q->inactive();
                }
            })
            ->latest()
            ->paginate($this->perPage);

        if ($this->selectedCustomer) {
            $this->selectedCustomer->loadMissing([
                'histories.previousPackage',
                'histories.currentPackage',
                'area',
                'subscriptions.package',
                'activeSubscription.olt',
                'subscriptions.olt'
            ]);
        }

        $mapDefaults = Cache::get('settings:map_defaults', [
            'lat' => -6.200000,
            'lng' => 106.816666,
            'zoom' => 13
        ]);

        return view('livewire.customer.customer-manager', [
            'customers' => $customers,
            'mapLat' => $mapDefaults['lat'],
            'mapLng' => $mapDefaults['lng'],
            'mapZoom' => $mapDefaults['zoom'],
        ])->layout('layouts.app');
    }

    /**
     * Called from JavaScript after map click + Nominatim reverse geocode.
     * Matches area names from the address data to areas table.
     */
    public function setAreaFromMap($lat, $lng, $addressData)
    {
        $this->latitude = $lat;
        $this->longitude = $lng;

        // If village is already selected, only update coordinates
        if ($this->village_id) {
            return;
        }

        // Extract area names from Nominatim address response
        $province = $addressData['state'] ?? $addressData['province'] ?? '';
        $city = $addressData['city'] ?? $addressData['county'] ?? $addressData['city_district'] ?? '';
        $district = $addressData['suburb'] ?? $addressData['city_district'] ?? $addressData['village'] ?? '';

        // Set display names
        $this->province_name = $province;
        $this->city_name = $city;
        $this->district_name = $district;

        // Try to match district in the database for village filtering
        $this->district_id = null;
        $this->villages = [];

        if ($district) {
            // Try matching district name in areas table
            $matchedDistrict = Area::where('type', 'district')
                ->where('name', 'like', '%' . $this->normalizeAreaName($district) . '%')
                ->active()
                ->first();

            if ($matchedDistrict) {
                $this->district_id = $matchedDistrict->id;
                $this->loadVillagesByDistrict();
            }
        }
    }

    /**
     * Normalize area name for fuzzy matching.
     * Strips common prefixes like "Kecamatan ", "Kab. ", etc.
     */
    private function normalizeAreaName($name)
    {
        $name = trim($name);
        // Remove common Indonesian prefixes
        $prefixes = ['Kecamatan ', 'Kec. ', 'Kelurahan ', 'Kel. ', 'Kabupaten ', 'Kab. ', 'Kota '];
        foreach ($prefixes as $prefix) {
            if (stripos($name, $prefix) === 0) {
                $name = substr($name, strlen($prefix));
                break;
            }
        }
        return trim($name);
    }

    /**
     * Load villages for the matched district.
     */
    private function loadVillagesByDistrict()
    {
        if ($this->district_id) {
            $cacheKey = 'areas:villages:' . $this->district_id;
            $this->villages = Cache::remember($cacheKey, 3600, function () {
                return Area::where('parent_id', $this->district_id)
                    ->active()
                    ->orderBy('name')
                    ->get()
                    ->toArray();
            });
        }
    }

    /**
     * API-style search for the searchable village select.
     * Returns up to 30 villages with full hierarchy path as label.
     */
    public function searchVillagesApi($search = '')
    {
        $query = Area::where('type', 'village')->active();

        if ($this->district_id) {
            $query->where('parent_id', $this->district_id);
        }

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Add eager loading for parent hierarchy to prevent N+1 queries
        $results = $query->with('parent.parent.parent')
            ->orderBy('name')
            ->limit(30)
            ->get();

        return $results->map(function ($village) {
            // Build full path: Province > City > District > Village
            $path = [];
            $parent = $village->parent;
            while ($parent) {
                array_unshift($path, $parent->name);
                $parent = $parent->parent;
            }
            $fullPath = implode(' > ', $path);

            return [
                'id' => $village->id,
                'name' => $village->name,
                'full_path' => $fullPath,
            ];
        })->toArray();
    }

    /**
     * Reverse flow: selecting a village auto-fills province, city, district.
     */
    public function setAreaFromVillage($villageId)
    {
        if (!$villageId) {
            return null;
        }

        // Eager load parent hierarchy to prevent N+1 queries
        $village = Area::with('parent.parent.parent')->find($villageId);
        if (!$village) {
            return null;
        }

        $this->village_id = $village->id;

        // Walk up the hierarchy and build address parts
        $addressParts = [$village->name];

        $district = $village->parent;
        if ($district) {
            $this->district_id = $district->id;
            $this->district_name = $district->name;
            $addressParts[] = $district->name;

            $city = $district->parent;
            if ($city) {
                $this->city_name = $city->name;
                $addressParts[] = $city->name;

                $province = $city->parent;
                if ($province) {
                    $this->province_name = $province->name;
                    $addressParts[] = $province->name;
                }
            }
        }

        // Also load villages for this district
        $this->loadVillagesByDistrict();

        // Dispatch event for JS map search
        $this->dispatch('search-map-location', address: implode(', ', $addressParts));
    }

    /**
     * Fetch all active ODPs for Async Alpine lazy-loading
     */
    public function getAllOdps()
    {
        return Cache::remember('all_odps', 3600, function () {
            return CoveragePoint::where('type', 'odp')
                ->where('is_active', true)
                ->get(['id', 'name', 'code', 'latitude', 'longitude', 'capacity', 'used_ports', 'address'])
                ->map(function ($odp) {
                    return [
                        'id' => $odp->id,
                        'name' => $odp->name,
                        'code' => $odp->code,
                        'latitude' => $odp->latitude,
                        'longitude' => $odp->longitude,
                        'capacity' => $odp->capacity,
                        'used_ports' => $odp->used_ports,
                        'available_ports' => $odp->capacity ? max(0, $odp->capacity - $odp->used_ports) : null,
                        'address' => $odp->address,
                    ];
                })
                ->toArray();
        });
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->resetForm();
        $this->editMode = false;
        $this->showModal = true;
        $this->generateCustomerId();

        // Dispatch events to refresh map and then open the modal
        $this->dispatch('refresh-map', lat: $this->latitude, lng: $this->longitude);
        $this->dispatch('open-customer-modal');
    }

    public function generateCustomerId()
    {
        // $prefix = 'SKN-' . date('Ym');
        $prefix = date('Ymd');
        $lastCustomer = Customer::where('customer_id', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastCustomer) {
            $lastNumber = intval(substr($lastCustomer->customer_id, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $this->customer_id = $prefix . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function editCustomer($id)
    {
        $this->resetValidation();
        $this->editMode = true;
        $this->db_id = $id;

        // Reset fields to ensure no stale data
        $this->reset([
            'province_name',
            'city_name',
            'district_name',
            'district_id',
            'village_id',
            'villages'
        ]);

        $customer = Customer::findOrFail($id);

        $this->customer_id = $customer->customer_id;
        $this->name = $customer->name;
        $this->identity_number = $customer->identity_number;
        $this->email = $customer->email;
        $this->phone = $customer->phone;
        $this->address = $customer->address;
        $this->registered_at = $customer->registered_at?->format('Y-m-d') ?? date('Y-m-d');
        $this->notes = $customer->notes;

        $this->latitude = $customer->latitude;
        $this->longitude = $customer->longitude;

        // Load Area Hierarchy with eager loading to prevent N+1 queries
        $village = Area::with('parent.parent.parent')->find($customer->area_id);
        if ($village) {
            $this->village_id = $village->id;

            $district = $village->parent;
            if ($district) {
                $this->district_id = $district->id;
                $this->district_name = $district->name;
                $this->loadVillagesByDistrict();

                $city = $district->parent;
                if ($city) {
                    $this->city_name = $city->name;

                    $province = $city->parent;
                    if ($province) {
                        $this->province_name = $province->name;
                    }
                }
            }
        }

        if ($this->latitude && $this->longitude) {
            $this->dispatch('refresh-map', lat: $this->latitude, lng: $this->longitude);
        } else {
            $this->dispatch('refresh-map');
        }

        // Open modal after all data is loaded
        $this->showModal = true;
        $this->dispatch('open-customer-modal');
    }

    public $showModemModal = false;

    public function viewCustomer($id)
    {
        $this->selectedCustomer = Customer::with(['histories.previousPackage', 'histories.currentPackage', 'area', 'subscriptions.package'])
            ->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function openModemModal($id)
    {
        $this->selectedCustomer = Customer::with(['activeSubscription.olt', 'subscriptions.olt'])->findOrFail($id);
        $this->modemStatus = null;

        $this->showModemModal = true;
        // Optionally auto-check when modal opens
        // $this->checkModemStatus();
    }

    public function checkModemStatus()
    {
        $this->isLoadingModemStatus = true;
        try {
            $latestSub = $this->selectedCustomer?->activeSubscription;
            if (!$latestSub || !$latestSub->olt_id) {
                // Return error if no OLT connected
                $this->modemStatus = [
                    'status' => 'N/A',
                    'rx_power' => '-',
                    'error' => 'Pelanggan tidak memiliki data OLT di langganan aktif.'
                ];
                $this->dispatch('toast', type: 'error', message: 'Koneksi Cek Modem Gagal: Pelanggan tidak memiliki data OLT.');
                return;
            }

            $olt = $latestSub->olt;

            // Format PON structure (Frame/Slot/Port)
            $ponIndex = "{$latestSub->olt_frame}/{$latestSub->olt_slot}/{$latestSub->olt_port}";
            $ontId = $latestSub->olt_onu_id;

            $snmpService = \App\Services\Snmp\OltSnmpFactory::make($olt);
            $this->modemStatus = $snmpService->getOntStatus($ponIndex, $ontId);

            if ($this->modemStatus['status'] === 'Error' || $this->modemStatus['status'] === 'Offline/Timeout') {
                $this->dispatch('toast', type: 'error', message: 'Koneksi Cek Modem Gagal: ' . ($this->modemStatus['error'] ?? 'Offline'));
            } else {
                $this->dispatch('toast', type: 'success', message: 'Koneksi Cek Modem Berhasil.');
            }

        } catch (\Exception $e) {
            $this->modemStatus = [
                'status' => 'Error',
                'rx_power' => '-',
                'error' => $e->getMessage()
            ];
            $this->dispatch('toast', type: 'error', message: 'Koneksi Cek Modem Gagal: ' . $e->getMessage());
        } finally {
            $this->isLoadingModemStatus = false;
        }
    }

    public function resetRegionFields()
    {
        $this->reset([
            'province_name',
            'city_name',
            'district_name',
            'district_id',
            'village_id',
            'villages'
        ]);
        // Also clear search/pagination if needed
        $this->villageSearch = '';
    }

    public function save()
    {
        $this->validate();

        if ($this->editMode) {
            $customer = Customer::find($this->db_id);
        } else {
            // Check ID uniqueness one last time
            $exists = Customer::where('customer_id', $this->customer_id)->exists();
            if ($exists) {
                $this->generateCustomerId(); // Retry
            }
            $customer = new Customer();
            $customer->customer_id = $this->customer_id;
        }

        $customer->name = $this->name;
        $customer->identity_number = $this->identity_number;
        $customer->email = $this->email;
        $customer->phone = $this->phone;
        $customer->address = $this->address;
        $customer->area_id = $this->village_id;
        $customer->registered_at = $this->registered_at;
        $customer->latitude = $this->latitude;
        $customer->longitude = $this->longitude;
        $customer->notes = $this->notes;

        $customer->save();

        $this->closeModal();
        $this->dispatch('toast', type: 'success', message: $this->editMode ? 'Data pelanggan berhasil diperbarui.' : 'Pelanggan baru berhasil ditambahkan.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->dispatch('close-modal');
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->customerToDeleteId = $id;
    }

    public function deleteCustomer()
    {
        if ($this->customerToDeleteId) {
            $customer = Customer::find($this->customerToDeleteId);
            if ($customer) {
                $customer->delete();
                $this->dispatch('toast', type: 'success', message: 'Pelanggan berhasil dihapus.');
            }
        }
        $this->customerToDeleteId = null;
    }

    public function bulkDelete(array $ids)
    {
        if (empty($ids)) {
            return;
        }

        $count = Customer::whereIn('id', $ids)->delete();

        $this->dispatch('toast', type: 'success', message: $count . ' pelanggan berhasil dihapus.');
        $this->dispatch('bulk-delete-completed');
    }


    private function resetForm()
    {
        $this->reset([
            'customer_id',
            'db_id',
            'name',
            'identity_number',
            'email',
            'phone',
            'address',
            'registered_at',
            'notes',
            'province_name',
            'city_name',
            'district_name',
            'district_id',
            'village_id',
            'latitude',
            'longitude',
            'villageSearch',
        ]);
        $this->registered_at = date('Y-m-d');

        // Reload initial villages
        $this->villages = Area::where('type', 'village')
            ->limit(100)
            ->get(['id', 'name'])
            ->toArray();
    }
}
