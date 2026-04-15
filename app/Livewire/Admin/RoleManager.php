<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleManager extends Component
{
    use WithPagination;

    // Modal State
    // State
    public $editMode = false;
    public $roleToDeleteId = null;
    public $roleToEditPermissions = null;

    // Form Fields
    public $role_id;
    public $name;
    public $guard_name = 'web';

    // Permissions
    public $permissions = []; // All available permissions
    public $selectedPermissions = []; // Permissions selected for the current role
    public $permissionGroups = [];

    protected $rules = [
        'name' => 'required|string|max:255|unique:roles,name',
        'guard_name' => 'required|string|max:255',
    ];

    public function mount()
    {
        $this->loadPermissions();
    }

    public function loadPermissions()
    {
        $this->permissions = Permission::all();

        // Group permissions by prefix (e.g., 'user-create' -> 'user')
        $this->permissionGroups = $this->permissions->groupBy(function ($permission) {
            $parts = explode('-', $permission->name);
            return $parts[0];
        })->toArray();
    }

    public function render()
    {
        $roles = Role::with('permissions')->orderBy('name')->paginate(10);

        return view('livewire.admin.role-manager', [
            'roles' => $roles,
        ])->layout('layouts.app');
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->resetForm();
        $this->editMode = false;
        $this->dispatch('open-role-modal');
    }

    public function editRole($id)
    {
        $this->resetValidation();
        $this->resetForm();
        $this->editMode = true;

        $role = Role::findOrFail($id);
        $this->role_id = $role->id;
        $this->name = $role->name;
        $this->guard_name = $role->guard_name;

        $this->dispatch('open-role-modal');
    }

    public function save()
    {
        if ($this->editMode) {
            $this->validate([
                'name' => 'required|string|max:255|unique:roles,name,' . $this->role_id,
                'guard_name' => 'required|string|max:255',
            ]);

            $role = Role::findOrFail($this->role_id);
            $role->update([
                'name' => $this->name,
                'guard_name' => $this->guard_name,
            ]);

            $this->dispatch('toast', type: 'success', message: 'Role updated successfully.');
        } else {
            $this->validate();

            Role::create([
                'name' => $this->name,
                'guard_name' => $this->guard_name,
            ]);

            $this->dispatch('toast', type: 'success', message: 'Role created successfully.');
        }

        $this->dispatch('close-modal');
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->roleToDeleteId = $id;
        $this->dispatch('open-confirmation-modal');
    }

    public function deleteRole()
    {
        if ($this->roleToDeleteId) {
            $role = Role::find($this->roleToDeleteId);
            $role->delete();
            $this->dispatch('toast', type: 'success', message: 'Role deleted successfully.');
        }

        $this->dispatch('close-modal');
        $this->roleToDeleteId = null;
    }

    public function managePermissions($id)
    {
        $this->roleToEditPermissions = Role::with('permissions')->findOrFail($id);
        $this->selectedPermissions = $this->roleToEditPermissions->permissions->pluck('name')->toArray();
        $this->dispatch('open-permission-modal', roleName: $this->roleToEditPermissions->name);
    }

    public function togglePermission($permissionName)
    {
        if (in_array($permissionName, $this->selectedPermissions)) {
            $this->selectedPermissions = array_diff($this->selectedPermissions, [$permissionName]);
        } else {
            $this->selectedPermissions[] = $permissionName;
        }
    }

    public function savePermissions()
    {
        if ($this->roleToEditPermissions) {
            $this->roleToEditPermissions->syncPermissions($this->selectedPermissions);
            $this->dispatch('toast', type: 'success', message: 'Permissions updated successfully.');
        }

        $this->dispatch('close-modal');
        $this->roleToEditPermissions = null;
        $this->selectedPermissions = [];
    }

    public function closeModal()
    {
        $this->dispatch('close-modal');
    }

    private function resetForm()
    {
        $this->reset(['role_id', 'name', 'guard_name']);
    }
}
