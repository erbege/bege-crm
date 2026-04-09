<?php

namespace App\Livewire\Notification;

use App\Jobs\ProcessWhatsappBlastJob;
use App\Jobs\SendWhatsappNotificationJob;
use App\Models\Area;
use App\Models\Customer;
use App\Models\WhatsappMessage;
use App\Services\Whatsapp\WhatsappService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

#[Layout('layouts.app')]
class WhatsappNotificationManager extends Component
{
    use WithPagination;

    // Device Status
    public $deviceStatus = null;
    public $qrCode = null;

    // Modals
    public $showSingleModal = false;
    public $showBlastModal = false;

    // Single Message Form
    public $singleTarget = '';
    public $singleMessage = '';

    // Blast Message Form
    public $blastType = 'manual'; // manual, all, active, area
    public $blastSpecificAreaId = null;
    public $blastManualTargets = '';
    public $blastMessage = '';

    // Search
    public $search = '';

    // Bulk Actions
    public $selectedMessages = [];
    public $selectAll = false;

    // Confirmation Modals
    public $confirmingBulkDelete = false;
    public $confirmingBulkResend = false;


    // Detail Modal
    public $showDetailModal = false;
    public $detailMessageId;
    public $detailTarget;
    public $detailMessage; // Editable
    public $detailStatus;
    public $detailDate;

    public function mount()
    {
        $this->checkDeviceStatus();
    }

    public function checkDeviceStatus()
    {
        try {
            $service = app(WhatsappService::class);
            $this->deviceStatus = $service->getDeviceStatus();
            \Illuminate\Support\Facades\Log::info('Device Status Response:', (array) $this->deviceStatus);
            $this->qrCode = $service->getQrCode();

            if ($this->deviceStatus) {
                $this->dispatch('toast', ['type' => 'success', 'message' => 'Status perangkat berhasil diperbarui: Terhubung']);
            } else {
                $this->dispatch('toast', ['type' => 'warning', 'message' => 'Status perangkat: Terputus']);
            }
        } catch (\Exception $e) {
            $this->deviceStatus = null;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Gagal memperbarui status: ' . $e->getMessage()]);
        }
    }

    // Computed Property for Areas
    public function getAreasProperty()
    {
        return Area::orderBy('name')->get();
    }

    // Single Message Logic
    public function openSingleModal()
    {
        $this->reset(['singleTarget', 'singleMessage']);
        $this->showSingleModal = true;
    }

    public function sendSingleMessage()
    {
        $this->validate([
            'singleTarget' => 'required|numeric',
            'singleMessage' => 'required|string',
        ]);

        $message = WhatsappMessage::create([
            'target' => $this->singleTarget,
            'message' => $this->singleMessage,
            'status' => 'pending',
            'provider' => 'fonnte',
            'scheduled_at' => now(),
        ]);

        SendWhatsappNotificationJob::dispatch($message);

        $this->showSingleModal = false;
        $this->reset(['singleTarget', 'singleMessage']);
        $this->dispatch('toast', ['type' => 'success', 'message' => 'Pesan tunggal sedang dikirim.']);
    }

    // Blast Message Logic
    public function openBlastModal()
    {
        $this->reset(['blastType', 'blastSpecificAreaId', 'blastManualTargets', 'blastMessage']);
        $this->showBlastModal = true;
    }

    public function sendBlast()
    {
        $this->validate([
            'blastType' => 'required|in:manual,all,active,area',
            'blastMessage' => 'required|string',
            'blastManualTargets' => 'required_if:blastType,manual',
            'blastSpecificAreaId' => 'required_if:blastType,area',
        ]);

        $targets = [];

        switch ($this->blastType) {
            case 'manual':
                $targets = preg_split('/[\s,]+/', $this->blastManualTargets, -1, PREG_SPLIT_NO_EMPTY);
                break;
            case 'all':
                $targets = Customer::whereNotNull('phone')->pluck('phone')->toArray();
                break;
            case 'active':
                $targets = Customer::active()->whereNotNull('phone')->pluck('phone')->toArray();
                break;
            case 'area':
                $targets = Customer::where('area_id', $this->blastSpecificAreaId)
                    ->whereNotNull('phone')
                    ->pluck('phone')
                    ->toArray();
                break;
        }

        // Clean and Unique
        $targets = array_unique($targets);
        $targets = array_filter($targets, fn($val) => !empty($val));

        if (empty($targets)) {
            $this->addError('blastType', 'Tidak ada nomor tujuan yang ditemukan untuk kriteria ini.');
            return;
        }

        // Dispatch Job
        ProcessWhatsappBlastJob::dispatch($targets, $this->blastMessage);

        $this->showBlastModal = false;
        $this->reset(['blastType', 'blastSpecificAreaId', 'blastManualTargets', 'blastMessage']);
        $this->dispatch('toast', ['type' => 'success', 'message' => 'Blast message sedang diproses untuk ' . count($targets) . ' penerima.']);
    }



    public function confirmBulkDelete()
    {
        if (empty($this->selectedMessages)) {
            $this->dispatch('toast', ['type' => 'warning', 'message' => 'Tidak ada pesan yang dipilih.']);
            return;
        }
        $this->confirmingBulkDelete = true;
    }

    public function bulkDelete()
    {
        WhatsappMessage::whereIn('id', $this->selectedMessages)->delete();

        $this->selectedMessages = [];
        $this->selectAll = false;
        $this->confirmingBulkDelete = false;
        $this->dispatch('toast', ['type' => 'success', 'message' => 'Pesan terpilih berhasil dihapus.']);
    }

    public function confirmBulkResend()
    {
        if (empty($this->selectedMessages)) {
            $this->dispatch('toast', ['type' => 'warning', 'message' => 'Tidak ada pesan yang dipilih.']);
            return;
        }
        $this->confirmingBulkResend = true;
    }

    public function bulkResend()
    {
        $messages = WhatsappMessage::whereIn('id', $this->selectedMessages)->get();

        foreach ($messages as $message) {
            /** @var WhatsappMessage $message */
            // Reset status
            $message->update(['status' => 'pending', 'error' => null, 'response' => null]);

            SendWhatsappNotificationJob::dispatch($message);
        }

        $this->selectedMessages = [];
        $this->selectAll = false;
        $this->confirmingBulkResend = false;
        $this->dispatch('toast', ['type' => 'success', 'message' => count($messages) . ' pesan sedang dikirim ulang.']);
    }

    public function openDetailModal($id)
    {
        $message = WhatsappMessage::find($id);
        if ($message) {
            $this->detailMessageId = $message->id;
            $this->detailTarget = $message->target;
            $this->detailMessage = $message->message;
            $this->detailStatus = $message->status;
            $this->detailDate = $message->created_at;
            $this->showDetailModal = true;
        }
    }

    public function resendFromModal()
    {
        $this->validate([
            'detailMessage' => 'required|string',
        ]);

        $message = WhatsappMessage::find($this->detailMessageId);
        if ($message) {
            // Update message if changed
            if ($message->message !== $this->detailMessage) {
                $message->message = $this->detailMessage;
            }

            // Reset status and dispatch job
            $message->update(['status' => 'pending', 'error' => null, 'response' => null]);

            SendWhatsappNotificationJob::dispatch($message);

            $this->showDetailModal = false;
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Pesan diperbarui dan dikirim ulang.']);
        }
    }

    public function resend($id)
    {
        $message = WhatsappMessage::find($id);
        if ($message) {
            // Reset status to pending before resending
            $message->update(['status' => 'pending', 'error' => null, 'response' => null]);

            SendWhatsappNotificationJob::dispatch($message);
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Pesan dikirim ulang.']);
        }
    }

    public function render()
    {
        $query = WhatsappMessage::latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('target', 'like', '%' . $this->search . '%')
                    ->orWhere('message', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.notification.whatsapp-notification-manager', [
            'messages' => $query->paginate(10),
        ]);
    }
}
