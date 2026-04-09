<?php

namespace App\Livewire\Portal;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Services\MikrotikService;

class Dashboard extends Component
{
    public $customer;
    public $activeSubscription;
    public $recentInvoices;
    public $connectionStatus = null;
    public $isLoadingStatus = true;

    public function mount()
    {
        $this->customer = auth('customer')->user();
        $this->activeSubscription = $this->customer->activeSubscription()->with(['package', 'nas'])->first();
        $this->recentInvoices = \App\Models\Invoice::where('customer_id', $this->customer->id)
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();
    }

    public function fetchConnectionStatus(MikrotikService $mikrotikService)
    {
        if ($this->activeSubscription && $this->activeSubscription->nas) {
            try {
                $nas = $this->activeSubscription->nas;
                $mikrotikService->connect($nas->ip_address, $nas->username, $nas->password, $nas->api_port);

                if ($this->activeSubscription->service_type === 'pppoe') {
                    $this->connectionStatus = $mikrotikService->getPppoeStatus($this->activeSubscription->pppoe_username);
                } else {
                    $this->connectionStatus = ['is_online' => false, 'message' => 'Check current session in router.'];
                }
            } catch (\Exception $e) {
                $this->connectionStatus = ['is_online' => false, 'message' => 'N/A'];
            }
        }
        $this->isLoadingStatus = false;
    }

    #[Layout('components.layouts.portal')]
    public function render()
    {
        return view('livewire.portal.dashboard');
    }
}
