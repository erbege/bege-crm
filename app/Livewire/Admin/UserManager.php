<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class UserManager extends Component
{
    use WithPagination;

    // Search & Filter
    public $search = '';
    public $perPage = 10;

    // State
    public $editMode = false;
    public $userToDeleteId = null;

    // Form Fields
    public $user_id;
    public $name;
    public $email;
    public $password;
    public $role;

    // Roles for dropdown
    public $availableRoles = [];

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->user_id,
            'role' => 'required|exists:roles,name',
        ];

        if (!$this->editMode) {
            $rules['password'] = 'required|string|min:8';
        } else {
            $rules['password'] = 'nullable|string|min:8';
        }

        return $rules;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->availableRoles = Role::pluck('name', 'name')->toArray();
    }

    public function render()
    {
        $users = User::with('roles')
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->paginate($this->perPage);

        return view('livewire.admin.user-manager', [
            'users' => $users,
        ])->layout('layouts.app');
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->resetForm();
        $this->editMode = false;
        $this->dispatch('open-user-modal');
    }

    public function editUser($id)
    {
        $this->resetValidation();
        $this->resetForm();
        $this->editMode = true;

        $user = User::findOrFail($id);
        $this->user_id = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->roles->first()?->name;

        $this->dispatch('open-user-modal');
    }

    public function save()
    {
        $this->validate();

        if ($this->editMode) {
            $user = User::findOrFail($this->user_id);
            $user->name = $this->name;
            $user->email = $this->email;
            if ($this->password) {
                $user->password = Hash::make($this->password);
            }
            $user->save();

            // Sync roles
            $user->syncRoles([$this->role]);

            $this->dispatch('toast', type: 'success', message: 'User updated successfully.');
        } else {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);

            $user->assignRole($this->role);

            $this->dispatch('toast', type: 'success', message: 'User created successfully.');
        }

        $this->dispatch('close-modal');
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->userToDeleteId = $id;
        $this->dispatch('open-confirmation-modal');
    }

    public function deleteUser()
    {
        if ($this->userToDeleteId) {
            $user = User::find($this->userToDeleteId);
            // Prevent deleting self
            if ($user->id === auth()->id()) {
                $this->dispatch('toast', type: 'error', message: 'You cannot delete yourself.');
                $this->showConfirmationModal = false;
                return;
            }

            $user->delete();
            $this->dispatch('toast', type: 'success', message: 'User deleted successfully.');
        }

        $this->dispatch('close-modal');
        $this->userToDeleteId = null;
    }

    public function closeModal()
    {
        $this->dispatch('close-modal');
    }

    private function resetForm()
    {
        $this->reset(['user_id', 'name', 'email', 'password', 'role']);
    }
}
