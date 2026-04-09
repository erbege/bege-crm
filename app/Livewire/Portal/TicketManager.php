<?php

namespace App\Livewire\Portal;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketMessage;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class TicketManager extends Component
{
    use WithPagination;


    // Search & Filter
    public $search = '';
    public $statusFilter = '';

    // Create Ticket Fields
    public $category_id;
    public $priority = 'medium';
    public $subject;
    public $message;

    // View Ticket
    public $selectedTicket;
    public $replyMessage;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function setStatusFilter($status)
    {
        $this->statusFilter = $status;
        $this->resetPage();
    }

    public function createTicket()
    {
        $throttleKey = 'create-ticket-' . auth('customer')->id();

        if (RateLimiter::tooManyAttempts($throttleKey, 2)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->dispatch('toast', ['type' => 'error', 'message' => "Terlalu banyak permintaan. Silakan tunggu {$seconds} detik."]);
            return;
        }

        $this->validate([
            'category_id' => 'required|exists:ticket_categories,id',
            'priority' => 'required|in:low,medium,high',
            'subject' => 'required|string|max:100',
            'message' => 'required|string|max:2000',
        ]);

        $ticket = Ticket::create([
            'customer_id' => auth('customer')->id(),
            'category_id' => $this->category_id,
            'subject' => $this->subject,
            'priority' => $this->priority,
            'status' => 'open',
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'customer_id' => auth('customer')->id(),
            'message' => $this->message,
        ]);

        RateLimiter::hit($throttleKey, 120); // 2 tickets per 2 minutes

        $this->reset(['category_id', 'subject', 'message']);
        $this->priority = 'medium';
        $this->dispatch('close-modals');
        $this->dispatch('toast', ['type' => 'success', 'message' => 'Tiket berhasil dikirim.']);
    }

    public function viewTicket($id)
    {
        $this->selectedTicket = Ticket::with(['messages', 'customer.activeSubscription'])
            ->where('customer_id', auth('customer')->id())
            ->findOrFail($id);

        $this->dispatch('open-detail-modal');
    }

    public function closeTicket()
    {
        $this->selectedTicket = null;
    }

    public function sendReply()
    {
        if (!$this->selectedTicket)
            return;

        $customerId = auth('customer')->id();
        $throttleKey = 'reply-ticket-' . $customerId;

        // 1. Rate Limiting (Maks 5 balasan per menit)
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->dispatch('toast', ['type' => 'error', 'message' => "Batas pengiriman tercapai. Coba lagi dalam {$seconds} detik."]);
            return;
        }

        // 2. Cooldown Check (Jeda minimal 8 detik antar pesan)
        $lastMessage = TicketMessage::where('customer_id', $customerId)
            ->latest()
            ->first();

        if ($lastMessage && $lastMessage->created_at->diffInSeconds(now()) < 8) {
            $this->dispatch('toast', ['type' => 'warning', 'message' => 'Mohon tunggu sebentar sebelum mengirim pesan lagi.']);
            return;
        }

        // 3. Max Length Validation
        $this->validate([
            'replyMessage' => 'required|string|max:2000',
        ]);

        TicketMessage::create([
            'ticket_id' => $this->selectedTicket->id,
            'customer_id' => $customerId,
            'message' => $this->replyMessage,
        ]);

        RateLimiter::hit($throttleKey, 60);

        $this->replyMessage = '';
        $this->selectedTicket->refresh()->load(['messages', 'customer.activeSubscription']);
    }

    #[Layout('components.layouts.portal')]
    public function render()
    {
        $customer = auth('customer')->user();

        // Pre-calculate counts for filter pills (efficient)
        $counts = Ticket::where('customer_id', $customer->id)
            ->selectRaw("status, count(*) as count")
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $openTicketsCount = $counts['open'] ?? 0;
        $inProgressTicketsCount = $counts['in_progress'] ?? 0;
        $resolvedTicketsCount = $counts['resolved'] ?? 0;

        $query = Ticket::with(['category', 'customer.activeSubscription'])
            ->where('customer_id', $customer->id);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('subject', 'like', '%' . $this->search . '%')
                    ->orWhere('id', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        return view('livewire.portal.ticket-manager', [
            'tickets' => $query->orderBy('created_at', 'desc')->paginate(10),
            'openTicketsCount' => $openTicketsCount,
            'inProgressTicketsCount' => $inProgressTicketsCount,
            'resolvedTicketsCount' => $resolvedTicketsCount,
            'categories' => \Illuminate\Support\Facades\Cache::remember('ticket_categories', 3600, fn() => TicketCategory::all()),
        ]);
    }
}
