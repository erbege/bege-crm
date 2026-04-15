<?php

namespace App\Livewire\MasterData;

use App\Models\BwProfile;
use App\Models\Package;
use Livewire\Component;
use Livewire\WithPagination;

class PackageManager extends Component
{
    use WithPagination;

    public $search = '';
    public $editMode = false;
    public $packageId = null;

    // Form fields
    public $bw_profile_id = '';
    public $name = '';
    public $code = '';
    public $price = '';
    public $installation_fee = 0;
    public $description = '';
    public $is_active = true;
    public $service_type = 'PPP';

    protected $queryString = ['search'];

    protected function rules()
    {
        $uniqueRule = $this->packageId
            ? 'unique:packages,code,' . $this->packageId
            : 'unique:packages,code';

        return [
            'bw_profile_id' => 'required|exists:bw_profiles,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|' . $uniqueRule,
            'price' => 'required|numeric|min:0',
            'installation_fee' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'service_type' => 'required|in:PPP,DHCP,HOTSPOT',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetForm();
        $this->editMode = false;
        $this->dispatch('open-modal');
    }

    public function editPackage($id)
    {
        $package = Package::findOrFail($id);
        $this->packageId = $package->id;
        $this->bw_profile_id = $package->bw_profile_id;
        $this->name = $package->name;
        $this->code = $package->code;
        $this->price = $package->price;
        $this->installation_fee = $package->installation_fee;
        $this->description = $package->description ?? '';
        $this->is_active = $package->is_active;
        $this->service_type = $package->service_type;
        $this->editMode = true;
        $this->dispatch('open-modal');
    }

    public function save()
    {
        $this->validate();

        $data = [
            'bw_profile_id' => $this->bw_profile_id,
            'name' => $this->name,
            'code' => $this->code,
            'price' => $this->price,
            'installation_fee' => $this->installation_fee ?? 0,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'service_type' => $this->service_type,
        ];

        if ($this->editMode) {
            $package = Package::findOrFail($this->packageId);
            $package->update($data);
            $this->dispatch('toast', type: 'success', message: 'Paket berhasil diperbarui.');
        } else {
            Package::create($data);
            $this->dispatch('toast', type: 'success', message: 'Paket berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function confirmDelete($id)
    {
        $this->packageId = $id;
        $this->dispatch('open-delete-modal');
    }

    public function deletePackage()
    {
        $package = Package::findOrFail($this->packageId);
        $package->delete();
        $this->dispatch('toast', type: 'success', message: 'Paket berhasil dihapus.');
        $this->dispatch('close-modal');
        $this->packageId = null;
    }

    public function closeModal()
    {
        $this->dispatch('close-modal');
        $this->resetForm();
    }

    public function closeDeleteModal()
    {
        $this->dispatch('close-modal');
        $this->packageId = null;
    }

    private function resetForm()
    {
        $this->reset([
            'packageId',
            'bw_profile_id',
            'name',
            'code',
            'price',
            'description',
            'editMode'
        ]);
        $this->installation_fee = 0;
        $this->is_active = true;
        $this->service_type = 'PPP';
        $this->resetErrorBag();
    }

    public function render()
    {
        $packages = Package::query()
            ->with(['bwProfile'])
            ->withCount([
                'subscriptions' => function ($query) {
                    $query->whereIn('status', ['active', 'paid']);
                }
            ])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(10);

        $bwProfiles = BwProfile::active()->orderBy('name')->get();

        return view('livewire.master-data.package-manager', [
            'packages' => $packages,
            'bwProfiles' => $bwProfiles,
        ])->layout('layouts.app');
    }
}
