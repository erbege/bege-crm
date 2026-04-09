<?php

namespace App\Livewire\Subscription;

use App\Models\Radius\RadAcct;
use App\Models\Subscription;
use App\Services\MikrotikService;
use App\Traits\RadiusConnectionTrait;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class OnlineUsers extends Component
{
    use WithPagination, RadiusConnectionTrait;

    public $search = '';
    public $perPage = 20;
    public bool $radiusOffline = false;

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    // Modal State
    public $confirmingKick = false;
    public $sessionToKick = null;
    public $userToKickName = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmKick($radacctId, $username)
    {
        $this->sessionToKick = $radacctId;
        $this->userToKickName = $username;
        $this->confirmingKick = true;
    }

    public function kickUser(MikrotikService $mikrotikService)
    {
        if (!$this->sessionToKick)
            return;

        $radacctId = $this->sessionToKick;

        try {
            $session = RadAcct::find($radacctId);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($this->isConnectionError($e)) {
                $this->dispatch('toast', type: 'error', message: 'Server FreeRadius tidak dapat dijangkau. Tidak dapat memutus koneksi user.');
                return;
            }
            throw $e;
        }

        if (!$session) {
            $this->dispatch('toast', type: 'error', message: 'Sesi tidak ditemukan atau sudah berakhir.');
            return;
        }

        // Attempt to find the NAS
        $nasIp = $session->nasipaddress;
        $username = $session->username;

        // Find NAS in our database by IP
        $nas = \App\Models\Nas::where('ip_address', $nasIp)->first();

        if (!$nas) {
            $this->dispatch('toast', type: 'error', message: "NAS dengan IP {$nasIp} tidak terdaftar di sistem.");
            return;
        }

        try {
            // Kick via Mikrotik API using the helper method
            $result = $mikrotikService->kickUserFromNas($nas, $username);

            if ($result) {
                $this->dispatch('toast', type: 'success', message: "Perintah putus koneksi untuk {$username} dikirim ke NAS {$nas->shortname}.");
            } else {
                $this->dispatch('toast', type: 'error', message: "Gagal memutus user {$username} dari Mikrotik. Cek log untuk detail.");
            }

        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', message: 'Error: ' . $e->getMessage());
        }

        $this->confirmingKick = false;
        $this->sessionToKick = null;
    }

    public function refresh()
    {
        $this->resetPage();
    }

    public function render()
    {
        $sessions = collect(); // Empty fallback
        $subscriptions = collect();
        $this->radiusOffline = false;

        try {
            // 1. Fetch Online Users from RadAcct (External DB)
            $query = RadAcct::online()->recent();

            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('username', 'like', '%' . $this->search . '%')
                        ->orWhere('framedipaddress', 'like', '%' . $this->search . '%')
                        ->orWhere('callingstationid', 'like', '%' . $this->search . '%'); // MAC Address usually
                });
            }

            $sessions = $query->paginate($this->perPage);

            // 2. Collect Usernames to fetch local Customer/Subscription Data
            $usernames = $sessions->pluck('username')->toArray();

            // 3. Fetch Local Subscriptions
            $subscriptions = Subscription::with(['customer', 'package'])
                ->whereIn('pppoe_username', $usernames)
                ->get()
                ->keyBy('pppoe_username');

        } catch (\Illuminate\Database\QueryException $e) {
            if ($this->isConnectionError($e)) {
                $this->radiusOffline = true;
                \Illuminate\Support\Facades\Log::warning('Radius database unreachable in OnlineUsers: ' . $e->getMessage());
            } else {
                throw $e;
            }
        }

        return view('livewire.subscription.online-users', [
            'sessions' => $sessions,
            'subscriptions' => $subscriptions,
        ])->layout('layouts.app');
    }

    public function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public function formatDuration($seconds)
    {
        if ($seconds < 60) {
            return $seconds . 's';
        }

        $dtF = new \DateTime('@0');
        $dtT = new \DateTime("@$seconds");
        // Create diff based on seconds
        $diff = $dtF->diff($dtT);

        $parts = [];
        if ($diff->d > 0)
            $parts[] = $diff->d . 'd';
        if ($diff->h > 0)
            $parts[] = $diff->h . 'h';
        if ($diff->i > 0)
            $parts[] = $diff->i . 'm';
        if ($diff->s > 0 && count($parts) < 2)
            $parts[] = $diff->s . 's'; // Only show seconds if less than a day

        return implode(' ', $parts);
    }
}
