<?php

namespace App\Livewire\Hotspot;

use App\Models\Radius\RadAcct;
use App\Services\MikrotikService;
use App\Traits\RadiusConnectionTrait;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class ActiveSessions extends Component
{
    use WithPagination, RadiusConnectionTrait;

    public $search = '';
    public $perPage = 10;
    public bool $radiusOffline = false;

    public $sessionIdToKick;
    public $confirmingKick = false;

    public function render()
    {
        $sessions = collect(); // Empty fallback
        $this->radiusOffline = false;

        try {
            $query = RadAcct::online()
                ->hotspotOnly()
                ->recent()
                ->with(['voucher.profile']);

            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('username', 'like', '%' . $this->search . '%')
                        ->orWhere('framedipaddress', 'like', '%' . $this->search . '%')
                        ->orWhere('callingstationid', 'like', '%' . $this->search . '%'); // MAC Address
                });
            }

            $sessions = $query->paginate($this->perPage);

        } catch (\Illuminate\Database\QueryException $e) {
            if ($this->isConnectionError($e)) {
                $this->radiusOffline = true;
                Log::warning('Radius database unreachable in ActiveSessions: ' . $e->getMessage());
            } else {
                throw $e;
            }
        }

        return view('livewire.hotspot.active-sessions', [
            'sessions' => $sessions
        ])->layout('layouts.app');
    }

    public function confirmKick($sessionId)
    {
        $this->sessionIdToKick = $sessionId;
        $this->confirmingKick = true;
    }

    public function kick()
    {
        $sessionId = $this->sessionIdToKick;
        if (!$sessionId)
            return;

        $this->confirmingKick = false;

        try {
            $session = RadAcct::find($sessionId);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($this->isConnectionError($e)) {
                $this->dispatch('toast', type: 'error', message: 'Server FreeRadius tidak dapat dijangkau. Tidak dapat memutus koneksi user.');
                return;
            }
            throw $e;
        }

        if (!$session) {
            $this->dispatch('toast', type: 'error', message: 'Sesi tidak ditemukan.');
            return;
        }

        // 1. Find the NAS
        $nas = \App\Models\Nas::where('ip_address', $session->nasipaddress)->first();

        // Fallback: If not found by IP, try by NAS-Identifier if available
        if (!$nas && $session->nasidentifier) {
            $nas = \App\Models\Nas::where('shortname', $session->nasidentifier)->first();
        }

        if (!$nas) {
            $this->dispatch('toast', type: 'error', message: 'NAS tidak ditemukan di database. Pastikan IP NAS (' . $session->nasipaddress . ') terdaftar.');
            return;
        }

        try {
            // 2. Call Mikrotik Service
            $mikrotik = new MikrotikService();
            $result = $mikrotik->kickUserFromNas($nas, $session->username, 'hotspot');

            if ($result) {
                $mikrotik->removeHotspotCookie($session->username);

                // 3. Mark as closed in RadAcct
                $session->update([
                    'acctstoptime' => now(),
                    'acctterminatecause' => 'Admin-Reset',
                ]);

                $this->dispatch('toast', type: 'success', message: "User {$session->username} berhasil diputus koneksinya.");
            } else {
                $this->dispatch('toast', type: 'error', message: 'Gagal memutus koneksi di Router. Cek koneksi ke NAS.');
            }

        } catch (\Exception $e) {
            Log::error("Kick User Error: " . $e->getMessage());
            $this->dispatch('toast', type: 'error', message: 'Error: ' . $e->getMessage());
        }
    }
}
