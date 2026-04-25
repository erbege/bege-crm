<?php

namespace App\Livewire\MasterData;

use App\Models\Nas;
use App\Services\MikrotikService;
use Illuminate\Contracts\Encryption\DecryptException;
use Livewire\Component;
use Livewire\WithPagination;

class NasManager extends Component
{
    use WithPagination;

    public $search = '';
    public $editMode = false;
    public $nasId = null;

    // Detail Modal Properties
    public $activeTab = 'info';
    public $nasDetail = null;
    public $nasServerList = [];
    public $nasDetailId = null;
    public $selectedServers = [];

    // Form fields
    public $name = '';
    public $shortname = '';
    public $ip_address = '';
    public $api_port = 8728;
    public $username = '';
    public $password = '';
    public $secret = '';
    public $description = '';
    public $is_active = true;
    public $require_message_authenticator = false;

    // Connection status tracking (per-NAS loading state)
    public $checkingConnectionId = null;

    protected $queryString = ['search'];

    protected function rules()
    {
        $uniqueRule = $this->nasId
            ? 'unique:nas,shortname,' . $this->nasId
            : 'unique:nas,shortname';

        return [
            'name' => 'required|string|max:255',
            'shortname' => 'required|string|max:50|' . $uniqueRule,
            'ip_address' => 'required|ip',
            'api_port' => 'required|integer|min:1|max:65535',
            'username' => 'required|string|max:255',
            'password' => $this->editMode ? 'nullable|string|max:255' : 'required|string|max:255',
            'secret' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'require_message_authenticator' => 'boolean',
        ];
    }

    // ──────────────────────────────────────────────
    // Real-time Connection Status (wire:poll)
    // ──────────────────────────────────────────────

    /**
     * Called by wire:poll.15s to refresh connection statuses
     * for all active NAS devices. Uses withoutEvents() to
     * prevent NasObserver from triggering (which could cause
     * DecryptException when accessing encrypted fields).
     */
    public function refreshConnectionStatuses()
    {
        // We do nothing here. The UI will just re-render and pick up 
        // the latest 'is_online' status from the database, which is 
        // updated by the background 'nas:check-status' command or manual checks.
        // This prevents the page from feeling 'heavy' due to sync network calls.
    }

    /**
     * Manual single-NAS connection check (button click).
     * Shows a toast notification with the result.
     */
    public function checkSingleConnection($id, MikrotikService $mikrotikService)
    {
        $this->checkingConnectionId = $id;
        $nas = Nas::findOrFail($id);

        try {
            $isOnline = $mikrotikService->checkConnection($nas);

            Nas::withoutEvents(function () use ($nas, $isOnline) {
                $nas->update([
                    'is_online' => $isOnline,
                    'last_check' => now(),
                ]);
            });

            $status = $isOnline ? 'Connected' : 'Disconnected';
            $type = $isOnline ? 'success' : 'error';
            $this->dispatch('toast', type: $type, message: "NAS {$nas->name}: {$status}");
        } catch (DecryptException $e) {
            $this->dispatch('toast', type: 'error', message: "Password NAS tidak valid (Gagal Dekripsi). Silakan edit dan simpan ulang password.");
        } catch (\Exception $e) {
            Nas::withoutEvents(function () use ($nas) {
                $nas->update([
                    'is_online' => false,
                    'last_check' => now(),
                ]);
            });
            $this->dispatch('toast', type: 'error', message: "NAS {$nas->name}: Disconnected — {$e->getMessage()}");
        }

        $this->checkingConnectionId = null;
    }

    // ──────────────────────────────────────────────
    // CRUD Operations
    // ──────────────────────────────────────────────

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

    public function editNas($id)
    {
        $nas = Nas::findOrFail($id);
        $this->nasId = $nas->id;
        $this->name = $nas->name;
        $this->shortname = $nas->shortname;
        $this->ip_address = $nas->ip_address;
        $this->api_port = $nas->api_port;
        $this->username = $nas->username;
        $this->password = ''; // Don't show encrypted password
        $this->secret = '';   // Don't show encrypted secret
        $this->description = $nas->description ?? '';
        $this->is_active = $nas->is_active;
        $this->require_message_authenticator = $nas->require_message_authenticator;
        $this->editMode = true;
        $this->dispatch('open-modal');
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'shortname' => $this->shortname,
            'ip_address' => $this->ip_address,
            'api_port' => $this->api_port,
            'username' => $this->username,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'require_message_authenticator' => $this->require_message_authenticator,
        ];

        // Only update password/secret if provided
        if ($this->password) {
            $data['password'] = $this->password;
        }
        if ($this->secret) {
            $data['secret'] = $this->secret;
        }

        try {
            if ($this->editMode) {
                $nas = Nas::findOrFail($this->nasId);
                $nas->update($data);
                $this->dispatch('toast', type: 'success', message: 'NAS berhasil diperbarui.');
            } else {
                Nas::create($data);
                $this->dispatch('toast', type: 'success', message: 'NAS berhasil ditambahkan.');
            }
        } catch (DecryptException $e) {
            // This can happen when Observer tries to access encrypted fields during sync
            // The data is still saved, but the Observer sync may have failed
            $this->dispatch('toast', type: 'warning', message: 'Data disimpan, tetapi sinkronisasi Radius gagal (password lama korup). Silakan update password.');
        }

        $this->closeModal();
    }

    public function confirmDelete($id)
    {
        $this->nasId = $id;
        $this->dispatch('open-delete-modal');
    }

    public function deleteNas()
    {
        try {
            $nas = Nas::findOrFail($this->nasId);
            $nas->delete();
            $this->dispatch('toast', type: 'success', message: 'NAS berhasil dihapus.');
        } catch (DecryptException $e) {
            // Observer failed to sync removal due to corrupt encrypted data
            // Force delete using query builder to bypass model events
            Nas::withoutEvents(function () {
                Nas::where('id', $this->nasId)->delete();
            });
            $this->dispatch('toast', type: 'success', message: 'NAS berhasil dihapus (bypass Observer).');
        }

        $this->dispatch('close-modal');
        $this->nasId = null;
    }

    public function closeModal()
    {
        $this->dispatch('close-modal');
        $this->resetForm();
    }

    public function closeDeleteModal()
    {
        $this->dispatch('close-modal');
        $this->nasId = null;
    }

    private function resetForm()
    {
        $this->reset([
            'nasId',
            'name',
            'shortname',
            'ip_address',
            'username',
            'password',
            'secret',
            'description',
            'editMode'
        ]);
        $this->api_port = 8728;
        $this->is_active = true;
        $this->require_message_authenticator = false;
        $this->resetErrorBag();
    }

    // ──────────────────────────────────────────────
    // Detail Modal
    // ──────────────────────────────────────────────

    public function showNasDetails($id, MikrotikService $mikrotikService)
    {
        $nas = Nas::findOrFail($id);
        $this->nasDetailId = $id;

        try {
            $info = $mikrotikService->getSystemInfo($nas);

            if ($info) {
                Nas::withoutEvents(function () use ($nas) {
                    $nas->update(['is_online' => true, 'last_check' => now()]);
                });
                $this->nasDetail = $info;
            } else {
                Nas::withoutEvents(function () use ($nas) {
                    $nas->update(['is_online' => false, 'last_check' => now()]);
                });
                $this->nasDetail = null;
                $this->dispatch('toast', type: 'error', message: "Gagal mengambil data dari NAS {$nas->name}. Pastikan koneksi aman.");
            }
        } catch (\Exception $e) {
            Nas::withoutEvents(function () use ($nas) {
                $nas->update(['is_online' => false, 'last_check' => now()]);
            });
            $this->nasDetail = null;
            $this->dispatch('toast', type: 'error', message: "Gagal mengambil data dari NAS {$nas->name}: {$e->getMessage()}");
        }

        $this->activeTab = 'info';
        $this->loadServers($id);
        $this->dispatch('open-detail-modal');
    }

    public function loadServers($id)
    {
        $nas = Nas::with('servers')->findOrFail($id);
        $this->nasServerList = $nas->servers;
        $this->selectedServers = [];
    }

    public function syncServers($id, MikrotikService $mikrotikService)
    {
        $nas = Nas::findOrFail($id);
        $servers = $mikrotikService->getServerList($nas);

        // Clear existing and insert new
        $nas->servers()->delete();
        foreach ($servers as $server) {
            $nas->servers()->create($server);
        }

        $this->loadServers($id);
        $this->dispatch('toast', type: 'success', message: 'Data server berhasil disinkronisasi.');
    }

    public function closeDetailModal()
    {
        $this->dispatch('close-modal');
        $this->nasDetail = null;
        $this->nasDetailId = null;
        $this->nasServerList = [];
        $this->activeTab = 'info';
        $this->selectedServers = [];
    }

    // ──────────────────────────────────────────────
    // Bulk Delete Servers
    // ──────────────────────────────────────────────

    public function confirmBulkDeleteServers($ids = [])
    {
        if (!empty($ids)) {
            $this->selectedServers = $ids;
        }

        if (empty($this->selectedServers)) {
            $this->dispatch('toast', type: 'error', message: 'Pilih minimal satu server untuk dihapus.');
            return;
        }
        $this->dispatch('open-bulk-delete-modal');
    }

    public function deleteSelectedServers()
    {
        Nas::findOrFail($this->nasDetailId)
            ->servers()
            ->whereIn('id', $this->selectedServers)
            ->delete();

        $this->selectedServers = [];
        $this->dispatch('close-modal');
        $this->loadServers($this->nasDetailId);
        $this->dispatch('toast', type: 'success', message: 'Server terpilih berhasil dihapus dari sistem.');
    }

    public function closeBulkDeleteServerModal()
    {
        $this->dispatch('close-modal');
    }

    // ──────────────────────────────────────────────
    // Render
    // ──────────────────────────────────────────────

    public function render()
    {
        $nasList = Nas::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('shortname', 'like', '%' . $this->search . '%')
                    ->orWhere('ip_address', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.master-data.nas-manager', [
            'nasList' => $nasList,
        ])->layout('layouts.app');
    }
}
