<?php

namespace App\Livewire\Hotspot;

use App\Models\HotspotProfile;
use Livewire\Component;
use Livewire\WithPagination;

class ProfileManager extends Component
{
    use WithPagination;

    public $name;
    public $mikrotik_group = 'BGRADIUS';
    public $address_list;
    public $rate_limit;
    public $shared_users = 1;

    public $data_limit;
    public $data_limit_unit = 'UNLIMITED'; // MB, GB, UNLIMITED

    public $time_limit;
    public $time_limit_unit = 'UNLIMITED'; // minutes, hours, days, UNLIMITED

    public $session_timeout;
    public $keepalive_timeout;
    public $price = 0;
    public $validity_value = 1;
    public $validity_unit = 'days'; // Default unit
    public $description;
    public $is_active = true;

    public $profile_id;
    public $isEdit = false;

    // Confirmation Modal State
    public $confirmationTitle = '';
    public $confirmationMessage = '';
    public $confirmationAction = '';
    public $confirmationId = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'mikrotik_group' => 'required|string|max:50',
        'address_list' => 'nullable|string|max:255',
        'rate_limit' => 'nullable|string|max:50', // e.g., 2M/2M
        'shared_users' => 'required|integer|min:1',

        'data_limit' => 'nullable|integer',
        'data_limit_unit' => 'required|string',

        'time_limit' => 'nullable|integer',
        'time_limit_unit' => 'required|string',

        'price' => 'required|numeric|min:0',
        'validity_value' => 'required|integer|min:1',
        'validity_unit' => 'required|in:minutes,hours,days,weeks,months',
    ];

    public $search = '';

    public function render()
    {
        $profiles = HotspotProfile::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.hotspot.profile-manager', compact('profiles'))->layout('layouts.app');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->resetForm();
        $this->isEdit = false; // Ensure isEdit is false
        $this->dispatch('open-modal');
    }

    public function edit($id)
    {
        $this->resetValidation();
        $this->isEdit = true;
        $this->profile_id = $id;

        $profile = HotspotProfile::findOrFail($id);
        $this->name = $profile->name;
        $this->mikrotik_group = $profile->mikrotik_group;
        $this->address_list = $profile->address_list;
        $this->rate_limit = $profile->rate_limit;
        $this->shared_users = $profile->shared_users;

        $this->data_limit = $profile->data_limit;
        $this->data_limit_unit = $profile->data_limit_unit;

        $this->time_limit = $profile->time_limit;
        $this->time_limit_unit = $profile->time_limit_unit;

        $this->session_timeout = $profile->session_timeout;
        $this->keepalive_timeout = $profile->keepalive_timeout;
        $this->price = $profile->price;
        $this->validity_value = $profile->validity_value;
        $this->validity_unit = $profile->validity_unit;
        $this->description = $profile->description;
        $this->is_active = $profile->is_active;

        $this->dispatch('open-modal');
    }

    public function store()
    {
        $this->validate();

        HotspotProfile::create([
            'name' => $this->name,
            'mikrotik_group' => $this->mikrotik_group,
            'address_list' => $this->address_list,
            'rate_limit' => $this->rate_limit,
            'shared_users' => $this->shared_users,
            'data_limit' => $this->data_limit_unit === 'UNLIMITED' ? null : $this->data_limit,
            'data_limit_unit' => $this->data_limit_unit,
            'time_limit' => $this->time_limit_unit === 'UNLIMITED' ? null : $this->time_limit,
            'time_limit_unit' => $this->time_limit_unit,
            'session_timeout' => $this->session_timeout,
            'keepalive_timeout' => $this->keepalive_timeout,
            'price' => $this->price,
            'validity_value' => $this->validity_value,
            'validity_unit' => $this->validity_unit,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ]);

        $this->dispatch('close-modal');
        $this->resetForm();
        $this->dispatch('toast', type: 'success', message: 'Profile created successfully.');
    }

    public function update()
    {
        $this->validate();

        $profile = HotspotProfile::findOrFail($this->profile_id);
        $profile->update([
            'name' => $this->name,
            'mikrotik_group' => $this->mikrotik_group,
            'address_list' => $this->address_list,
            'rate_limit' => $this->rate_limit,
            'shared_users' => $this->shared_users,
            'data_limit' => $this->data_limit_unit === 'UNLIMITED' ? null : $this->data_limit,
            'data_limit_unit' => $this->data_limit_unit,
            'time_limit' => $this->time_limit_unit === 'UNLIMITED' ? null : $this->time_limit,
            'time_limit_unit' => $this->time_limit_unit,
            'session_timeout' => $this->session_timeout,
            'keepalive_timeout' => $this->keepalive_timeout,
            'price' => $this->price,
            'validity_value' => $this->validity_value,
            'validity_unit' => $this->validity_unit,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ]);

        $this->dispatch('close-modal');
        $this->resetForm();
        $this->dispatch('toast', type: 'success', message: 'Profile updated successfully.');
    }

    public function delete($id)
    {
        $profile = HotspotProfile::findOrFail($id);

        if ($profile->vouchers()->count() > 0) {
            $this->dispatch('toast', type: 'error', message: 'Profil Hotspot tidak dapat dihapus karena masih digunakan oleh voucher.');
            return;
        }

        $profile->delete();
        $this->dispatch('toast', type: 'success', message: 'Profile deleted successfully.');
    }

    public function closeModal()
    {
        $this->dispatch('close-modal');
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'name',
            'mikrotik_group',
            'address_list',
            'rate_limit',
            'shared_users',
            'data_limit',
            'time_limit',
            'session_timeout',
            'keepalive_timeout',
            'price',
            'validity_value',
            'validity_unit',
            'description',
            'is_active',
            'profile_id',
            'isEdit'
        ]);
        $this->mikrotik_group = 'BGRADIUS';
        $this->data_limit_unit = 'UNLIMITED';
        $this->time_limit_unit = 'UNLIMITED';
        $this->shared_users = 1;
        $this->is_active = true;
        $this->price = 0;
        $this->validity_value = 1;
        $this->validity_unit = 'days';
    }

    public function triggerConfirm($action, $id, $title, $message)
    {
        $this->confirmationAction = $action;
        $this->confirmationId = $id;
        $this->confirmationTitle = $title;
        $this->confirmationMessage = $message;
        $this->dispatch('open-confirmation-modal');
    }

    public function closeConfirmationModal()
    {
        $this->dispatch('close-modal');
        $this->confirmationAction = '';
        $this->confirmationId = null;
        $this->confirmationTitle = '';
        $this->confirmationMessage = '';
    }

    public function executeAction()
    {
        if ($this->confirmationAction && method_exists($this, $this->confirmationAction)) {
            $this->{$this->confirmationAction}($this->confirmationId);
        }
        $this->closeConfirmationModal();
    }
}
