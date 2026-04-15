<?php

namespace App\Livewire\Hotspot;

use App\Models\HotspotProfile;
use App\Models\HotspotVoucher;
use Livewire\Component;
use Livewire\WithPagination;

class VoucherList extends Component
{
    use WithPagination;

    public $statusFilter = ''; // 'active', 'used', 'expired'
    public $profileFilter = '';
    public $search = '';

    // Bulk Actions
    public $selectedVouchers = [];
    public $confirmingBulkDelete = false;
    public $selectAll = false;

    // Listeners for confirmation
    protected $listeners = ['deleteSelectedConfirmed' => 'deleteSelected'];

    public function updatedSelectAll($value)
    {
        if ($value) {
            // Select all vouchers matching current filters
            $query = HotspotVoucher::query();

            if ($this->statusFilter) {
                $query->where('status', $this->statusFilter);
            }
            if ($this->profileFilter) {
                $query->where('hotspot_profile_id', $this->profileFilter);
            }
            if ($this->search) {
                $query->where('code', 'like', '%' . $this->search . '%');
            }

            $this->selectedVouchers = $query->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedVouchers = [];
        }
    }

    public function updatedSelectedVouchers()
    {
        $this->selectAll = false;
    }

    public function confirmBulkDelete($ids = null)
    {
        // If IDs are passed (even empty array), update the property
        if (!is_null($ids)) {
            $this->selectedVouchers = $ids;
        }

        if (empty($this->selectedVouchers)) {
            $this->dispatch('toast', type: 'error', message: 'Tidak ada voucher yang dipilih.');
            return;
        }

        $this->confirmingBulkDelete = true;
    }

    public function syncUsage()
    {
        \Illuminate\Support\Facades\Artisan::queue('hotspot:update-status');
        $this->dispatch('toast', type: 'success', message: 'Sinkronisasi penggunaan voucher sedang diproses di background.');
    }

    public function deleteSelected()
    {
        \Illuminate\Support\Facades\Log::info('VoucherList: deleteSelected called', ['count' => count($this->selectedVouchers), 'ids' => $this->selectedVouchers]);

        if (empty($this->selectedVouchers)) {
            \Illuminate\Support\Facades\Log::warning('VoucherList: selectedVouchers is empty');
            return;
        }

        $count = count($this->selectedVouchers);

        // Get codes for Radius removal
        $codes = HotspotVoucher::whereIn('id', $this->selectedVouchers)->pluck('code')->toArray();

        // Remove from FreeRadius first (Async)
        if (!empty($codes)) {
            try {
                \App\Jobs\RemoveHotspotVouchersJob::dispatch($codes);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Radius bulk removal job dispatch failed: ' . $e->getMessage());
            }
        }

        HotspotVoucher::whereIn('id', $this->selectedVouchers)->delete();

        $this->selectedVouchers = [];
        $this->selectAll = false;
        $this->confirmingBulkDelete = false;

        $this->dispatch('toast', type: 'success', message: "{$count} voucher berhasil dihapus.");
    }

    public function getSummaryProfilesProperty()
    {
        return HotspotProfile::select('id', 'name')->withCount([
            'vouchers as total_count',
            'vouchers as active_count' => function ($query) {
                $query->where('status', 'active');
            },
            'vouchers as used_count' => function ($query) {
                $query->where('status', 'used');
            }
        ])->get();
    }

    public function render()
    {
        // Main Query
        $query = HotspotVoucher::with('profile')
            ->latest();

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->profileFilter) {
            $query->where('hotspot_profile_id', $this->profileFilter);
        }

        if ($this->search) {
            $query->where('code', 'like', '%' . $this->search . '%');
        }

        $vouchers = $query->paginate(20);

        return view('livewire.hotspot.voucher-list', [
            'vouchers' => $vouchers,
            'summaryProfiles' => $this->summaryProfiles,
        ])->layout('layouts.app');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingProfileFilter()
    {
        $this->resetPage();
    }
}
