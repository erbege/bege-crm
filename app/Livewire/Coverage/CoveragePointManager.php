<?php

namespace App\Livewire\Coverage;

use App\Models\Area;
use App\Models\CoveragePoint;
use App\Models\Setting;
use Livewire\Component;
use Livewire\WithPagination;

class CoveragePointManager extends Component
{
    use WithPagination;

    public $search = '';
    public $typeFilter = '';
    public $areaFilter = '';
    public $editMode = false;
    public $pointId = null;

    // Form fields
    public $area_id = '';
    public $name = '';
    public $code = '';
    public $type = 'odp';
    public $capacity = '';
    public $used_ports = 0;
    public $latitude = '';
    public $longitude = '';
    public $address = '';
    public $description = '';
    public $is_active = true;

    protected $queryString = ['search', 'typeFilter', 'areaFilter'];

    protected $listeners = ['setCoordinates'];

    protected function rules()
    {
        $uniqueRule = $this->pointId
            ? 'unique:coverage_points,code,' . $this->pointId
            : 'unique:coverage_points,code';

        return [
            'area_id' => 'required|exists:areas,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|' . $uniqueRule,
            'type' => 'required|in:odp,odc,olt,pole',
            'capacity' => 'nullable|integer|min:0',
            'used_ports' => 'integer|min:0',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingAreaFilter()
    {
        $this->resetPage();
    }

    public function setCoordinates($lat, $lng)
    {
        $this->latitude = $lat;
        $this->longitude = $lng;
    }

    public function openModal()
    {
        $this->resetForm();
        $this->editMode = false;
        $this->dispatch('open-modal');
    }

    public function editPoint($id)
    {
        $point = CoveragePoint::findOrFail($id);
        $this->pointId = $point->id;
        $this->area_id = $point->area_id;
        $this->name = $point->name;
        $this->code = $point->code;
        $this->type = $point->type;
        $this->capacity = $point->capacity ?? '';
        $this->used_ports = $point->used_ports;
        $this->latitude = $point->latitude;
        $this->longitude = $point->longitude;
        $this->address = $point->address ?? '';
        $this->description = $point->description ?? '';
        $this->is_active = $point->is_active;
        $this->editMode = true;
        $this->dispatch('open-modal');
    }

    public function save()
    {
        $this->validate();

        $data = [
            'area_id' => $this->area_id,
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type,
            'capacity' => $this->capacity ?: null,
            'used_ports' => $this->used_ports,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'address' => $this->address,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ];

        if ($this->editMode) {
            $point = CoveragePoint::findOrFail($this->pointId);
            $point->update($data);
            $this->dispatch('toast', type: 'success', message: 'Titik coverage berhasil diperbarui.');
        } else {
            CoveragePoint::create($data);
            $this->dispatch('toast', type: 'success', message: 'Titik coverage berhasil ditambahkan.');
        }

        $this->dispatch('close-modal');
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->pointId = $id;
        $this->dispatch('open-delete-modal');
    }

    public function deletePoint()
    {
        $point = CoveragePoint::findOrFail($this->pointId);
        $point->delete();
        $this->dispatch('toast', type: 'success', message: 'Titik coverage berhasil dihapus.');
        $this->dispatch('close-modal');
        $this->pointId = null;
    }

    public function closeModal()
    {
        $this->dispatch('close-modal');
        $this->resetForm();
    }

    public function closeDeleteModal()
    {
        $this->dispatch('close-modal');
        $this->pointId = null;
    }

    private function resetForm()
    {
        $this->reset([
            'pointId',
            'area_id',
            'name',
            'code',
            'capacity',
            'latitude',
            'longitude',
            'address',
            'description',
            'editMode'
        ]);
        $this->type = 'odp';
        $this->used_ports = 0;
        $this->is_active = true;
        $this->resetErrorBag();
    }

    // Connected Customers Modal
    public $connectedCustomers = [];
    public $selectedPoint = null;

    public function showConnectedCustomers($id)
    {
        $this->selectedPoint = CoveragePoint::findOrFail($id);

        // Get customers with active (not cancelled) subscriptions on this point
        $this->connectedCustomers = \App\Models\Customer::whereHas('subscriptions', function ($q) use ($id) {
            $q->where('coverage_point_id', $id)
                ->where('status', '!=', 'cancelled');
        })->with([
                    'activeSubscription' => function ($q) {
                        // We prioritize the subscription on THIS point, but activeSubscription relation 
                        // on model is 'latest' active. Ideally we filter to show relevant one.
                        // For simplicity, we just show their main active subscription info
                        // or we can fetch specific subscription for this point.
                        $q->where('status', '!=', 'cancelled');
                    },
                    'activeSubscription.package' // Prevent N+1 when accessing package info
                ])->get();

        $this->dispatch('open-customer-modal');
    }

    public function closeCustomerModal()
    {
        $this->dispatch('close-modal');
        $this->connectedCustomers = [];
        $this->selectedPoint = null;
    }

    public function render()
    {
        $points = CoveragePoint::query()
            ->with('area.parent.parent.parent')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%')
                    ->orWhere('address', 'like', '%' . $this->search . '%');
            })
            ->when($this->typeFilter, function ($query) {
                $query->where('type', $this->typeFilter);
            })
            ->when($this->areaFilter, function ($query) {
                $query->where('area_id', $this->areaFilter);
            })
            ->orderBy('name')
            ->paginate(15);

        $areas = Area::active()
            ->whereIn('type', ['district', 'village'])
            ->orderBy('name')
            ->get();

        return view('livewire.coverage.coverage-point-manager', [
            'points' => $points,
            'areas' => $areas,
            'mapLat' => Setting::get('general.map_latitude', -6.200000),
            'mapLng' => Setting::get('general.map_longitude', 106.816666),
            'mapZoom' => Setting::get('general.map_zoom', 13),
        ])->layout('layouts.app');
    }
}
