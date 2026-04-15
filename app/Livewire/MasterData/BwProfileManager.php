<?php

namespace App\Livewire\MasterData;

use App\Models\BwProfile;
use Livewire\Component;
use Livewire\WithPagination;

class BwProfileManager extends Component
{
    use WithPagination;

    public $search = '';
    public $editMode = false;
    public $profileId = null;

    // Form fields
    public $name = '';
    public $rate_limit = '';
    public $burst_limit = '';
    public $burst_threshold = '';
    public $burst_time = '';
    public $priority = 8;
    public $mikrotik_group = '';
    public $radius_group = '';
    public $address_pool = '';
    public $description = '';
    public $is_active = true;

    protected $queryString = ['search'];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'rate_limit' => 'required|string|max:50',
            'burst_limit' => 'nullable|string|max:50',
            'burst_threshold' => 'nullable|string|max:50',
            'burst_time' => 'nullable|string|max:50',
            'priority' => 'required|integer|min:1|max:8',
            'mikrotik_group' => 'nullable|string|max:255',
            'radius_group' => 'nullable|string|max:255',
            'address_pool' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
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

    public function editProfile($id)
    {
        $profile = BwProfile::findOrFail($id);
        $this->profileId = $profile->id;
        $this->name = $profile->name;
        $this->rate_limit = $profile->rate_limit;
        $this->burst_limit = $profile->burst_limit ?? '';
        $this->burst_threshold = $profile->burst_threshold ?? '';
        $this->burst_time = $profile->burst_time ?? '';
        $this->priority = $profile->priority;
        $this->mikrotik_group = $profile->mikrotik_group ?? '';
        $this->radius_group = $profile->radius_group ?? '';
        $this->address_pool = $profile->address_pool ?? '';
        $this->description = $profile->description ?? '';
        $this->is_active = $profile->is_active;
        $this->editMode = true;
        $this->dispatch('open-modal');
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'rate_limit' => $this->rate_limit,
            'burst_limit' => $this->burst_limit ?: null,
            'burst_threshold' => $this->burst_threshold ?: null,
            'burst_time' => $this->burst_time ?: null,
            'priority' => $this->priority,
            'mikrotik_group' => $this->mikrotik_group ?: null,
            'radius_group' => $this->radius_group ?: null,
            'address_pool' => $this->address_pool ?: null,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ];

        if ($this->editMode) {
            $profile = BwProfile::findOrFail($this->profileId);
            $profile->update($data);
            $this->dispatch('toast', type: 'success', message: 'Profil bandwidth berhasil diperbarui.');
        } else {
            BwProfile::create($data);
            $this->dispatch('toast', type: 'success', message: 'Profil bandwidth berhasil ditambahkan.');
        }

        $this->dispatch('close-modal');
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->profileId = $id;
        $this->dispatch('open-delete-modal');
    }

    public function deleteProfile()
    {
        $profile = BwProfile::findOrFail($this->profileId);

        if ($profile->packages()->count() > 0) {
            $this->dispatch('toast', type: 'error', message: 'Profil bandwidth tidak dapat dihapus karena masih memiliki paket terkait.');
            $this->dispatch('close-modal');
            return;
        }

        $profile->delete();
        $this->dispatch('toast', type: 'success', message: 'Profil bandwidth berhasil dihapus.');
        $this->dispatch('close-modal');
        $this->profileId = null;
    }

    public function closeModal()
    {
        $this->dispatch('close-modal');
        $this->resetForm();
    }

    public function closeDeleteModal()
    {
        $this->dispatch('close-modal');
        $this->profileId = null;
    }

    private function resetForm()
    {
        $this->reset([
            'profileId',
            'name',
            'rate_limit',
            'burst_limit',
            'burst_threshold',
            'burst_time',
            'mikrotik_group',
            'radius_group',
            'address_pool',
            'description',
            'editMode'
        ]);
        $this->priority = 8;
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function render()
    {
        $profiles = BwProfile::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('rate_limit', 'like', '%' . $this->search . '%');
            })
            ->withCount('packages')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.master-data.bw-profile-manager', [
            'profiles' => $profiles,
        ])->layout('layouts.app');
    }
}
