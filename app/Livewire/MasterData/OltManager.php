<?php

namespace App\Livewire\MasterData;

use App\Models\Olt;
use Livewire\Component;
use Livewire\WithPagination;

class OltManager extends Component
{
    use WithPagination;

    public $search = '';
    public $editMode = false;
    public $oltId = null;

    // Form fields
    public $name = '';
    public $ip_address = '';
    public $port = 22;
    public $username = '';
    public $password = '';
    public $brand = 'zte';
    public $snmp_version = 'v2c';
    public $snmp_port = 161;
    public $snmp_community_read = 'public';
    public $snmp_community_write = 'private';
    public $description = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'ip_address' => 'required|string|max:255',
        'port' => 'required|integer',
        'username' => 'nullable|string|max:255',
        'password' => 'nullable|string|max:255',
        'brand' => 'required|string|in:zte,huawei,nokia,fiberhome',
        'snmp_version' => 'required|string|in:v1,v2c,v3',
        'snmp_port' => 'required|integer|min:1|max:65535', // Added snmp_port rule
        'snmp_community_read' => 'nullable|string|max:255',
        'snmp_community_write' => 'nullable|string|max:255',
        'description' => 'nullable|string',
    ];

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

    public function editOlt($id)
    {
        $olt = Olt::findOrFail($id);
        $this->oltId = $olt->id;
        $this->name = $olt->name;
        $this->ip_address = $olt->ip_address;
        $this->port = $olt->port;
        $this->username = $olt->username;
        $this->password = $olt->password;
        $this->brand = $olt->brand;
        $this->snmp_version = $olt->snmp_version;
        $this->snmp_port = $olt->snmp_port ?? 161; // Added snmp_port
        $this->snmp_community_read = $olt->snmp_community_read;
        $this->snmp_community_write = $olt->snmp_community_write;
        $this->description = $olt->description;
        $this->editMode = true;
        $this->dispatch('open-modal');
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'ip_address' => $this->ip_address,
            'port' => $this->port,
            'username' => $this->username,
            'password' => $this->password,
            'brand' => $this->brand,
            'snmp_version' => $this->snmp_version,
            'snmp_port' => $this->snmp_port, // Added snmp_port
            'snmp_community_read' => $this->snmp_community_read,
            'snmp_community_write' => $this->snmp_community_write,
            'description' => $this->description,
        ];

        if ($this->editMode) {
            $olt = Olt::findOrFail($this->oltId);
            $olt->update($data);
            $this->dispatch('toast', type: 'success', message: 'Data OLT berhasil diperbarui.');
        } else {
            Olt::create($data);
            $this->dispatch('toast', type: 'success', message: 'Data OLT berhasil ditambahkan.');
        }

        $this->dispatch('close-modal');
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->oltId = $id;
        $this->dispatch('open-delete-modal');
    }

    public function deleteOlt()
    {
        $olt = Olt::findOrFail($this->oltId);
        $olt->delete();
        $this->dispatch('toast', type: 'success', message: 'Data OLT berhasil dihapus.');
        $this->dispatch('close-modal');
        $this->oltId = null;
    }

    public function closeModal()
    {
        $this->dispatch('close-modal');
        $this->resetForm();
    }

    public function closeDeleteModal()
    {
        $this->dispatch('close-modal');
        $this->oltId = null;
    }

    public $oltStatuses = [];

    private function resetForm()
    {
        $this->reset([
            'oltId',
            'name',
            'ip_address',
            'username',
            'password',
            'snmp_community_read',
            'snmp_community_write',
            'description',
            'editMode'
        ]);
        $this->port = 22;
        $this->brand = 'zte';
        $this->snmp_version = 'v2c';
        $this->resetErrorBag();
    }

    public function checkOltStatus($id)
    {
        try {
            $olt = Olt::findOrFail($id);
            $snmpService = \App\Services\Snmp\OltSnmpFactory::make($olt);

            if (!$snmpService->checkSnmpSupport()) {
                $errorMessage = 'PHP SNMP extension is not installed/enabled. Please enable it in your PHP configuration (php.ini).';
                $this->oltStatuses[$id] = [
                    'status' => 'Error',
                    'uptime' => '-',
                    'name' => '-',
                    'error' => $errorMessage
                ];
                $this->dispatch('toast', type: 'error', message: 'Koneksi ke OLT Gagal: ' . $errorMessage);
                return;
            }

            $result = $snmpService->checkSystemStatus();
            $this->oltStatuses[$id] = $result;

            if ($result['status'] === 'Error' || $result['status'] === 'Offline/Timeout') {
                $this->dispatch('toast', type: 'error', message: 'Koneksi ke OLT Gagal: ' . ($result['error'] ?? 'Device Offline'));
            } else {
                $this->dispatch('toast', type: 'success', message: 'Koneksi ke OLT Berhasil.');
            }
        } catch (\Exception $e) {
            $this->oltStatuses[$id] = [
                'status' => 'Error',
                'uptime' => '-',
                'name' => '-',
                'error' => $e->getMessage()
            ];
            $this->dispatch('toast', type: 'error', message: 'Koneksi ke OLT Gagal: ' . $e->getMessage());
        }
    }
    public $showOntMonitorModal = false;
    public $ontStatuses = [];
    public $monitoringOltName = '';
    public $monitoringOltBrand = '';

    public function monitorOnts($id)
    {
        try {
            $olt = Olt::findOrFail($id);
            $this->monitoringOltName = $olt->name;
            $this->monitoringOltBrand = $olt->brand;
            $this->ontStatuses = [];
            $snmpService = \App\Services\Snmp\OltSnmpFactory::make($olt);

            if (!$snmpService->checkSnmpSupport()) {
                $errorMessage = 'PHP SNMP extension is not installed/enabled. Silakan aktifkan di konfigurasi PHP Anda.';
                $this->dispatch('toast', type: 'error', message: 'Gagal memantau ONT: ' . $errorMessage);
                return;
            }

            $this->ontStatuses = $snmpService->getAllOntStatuses();

            if (empty($this->ontStatuses)) {
                $this->dispatch('toast', type: 'warning', message: 'Tidak ada data ONT yang ditemukan atau perangkat tidak merespon (Timeout).');
            } else {
                $this->dispatch('toast', type: 'success', message: 'Berhasil mengambil ' . count($this->ontStatuses) . ' data ONT dari OLT.');
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', message: 'Gagal memantau ONT: ' . $e->getMessage());
        }
    }

    public function closeOntMonitor()
    {
        $this->showOntMonitorModal = false;
        $this->ontStatuses = [];
    }
    public function render()
    {
        $olts = Olt::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('ip_address', 'like', '%' . $this->search . '%')
                    ->orWhere('brand', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.master-data.olt-manager', [
            'olts' => $olts,
        ])->layout('layouts.app');
    }
}
