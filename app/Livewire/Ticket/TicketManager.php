<?php

namespace App\Livewire\Ticket;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Support\Facades\RateLimiter;

class TicketManager extends Component
{
    use WithPagination;

    // Search & Filter
    public $search = '';
    public $statusFilter = '';

    // View Ticket
    public $selectedTicket;
    public $replyMessage;

    // Create Ticket
    public $customerId;
    public $categoryId;
    public $subject;
    public $message;
    public $customerSearch = '';
    public $customersList = [];

    public function updatedCustomerSearch($value)
    {
        if (strlen($value) >= 2) {
            $this->customersList = \App\Models\Customer::where('name', 'like', '%' . $value . '%')
                ->orWhere('customer_id', 'like', '%' . $value . '%')
                ->limit(10)
                ->get(['id', 'name', 'customer_id'])
                ->toArray();
        } else {
            $this->customersList = [];
        }
    }

    public function selectCustomer($id, $name)
    {
        $this->customerId = $id;
        $this->customerSearch = $name;
        $this->customersList = [];
    }

    public function createTicket()
    {
        $throttleKey = 'admin-create-ticket-' . auth()->id();

        // Admins have higher limits: 10 tickets per minute
        if (RateLimiter::tooManyAttempts($throttleKey, 10)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->dispatch('toast', ['type' => 'error', 'message' => "Terlalu banyak permintaan. Silakan tunggu {$seconds} detik."]);
            return;
        }

        $this->validate([
            'customerId' => 'required|exists:customers,id',
            'categoryId' => 'required|exists:ticket_categories,id',
            'subject' => 'required|string|max:150',
            'message' => 'required|string|max:3000',
        ]);

        $ticket = Ticket::create([
            'customer_id' => $this->customerId,
            'ticket_category_id' => $this->categoryId,
            'subject' => $this->subject,
            'status' => 'open',
            'priority' => 'medium',
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $this->message,
        ]);

        RateLimiter::hit($throttleKey, 60);

        $this->reset(['customerId', 'categoryId', 'subject', 'message', 'customerSearch', 'customersList']);
        $this->dispatch('close-modals');
        $this->dispatch('toast', ['type' => 'success', 'message' => 'Tiket berhasil dibuat.']);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function setStatusFilter($status)
    {
        $this->statusFilter = $status;
        $this->resetPage();
    }

    public function viewTicket($id)
    {
        $this->selectedTicket = Ticket::with(['messages.user', 'messages.customer', 'customer.activeSubscription', 'category'])
            ->findOrFail($id);

        $this->dispatch('open-detail-modal');
    }

    public function closeTicket()
    {
        $this->selectedTicket = null;
    }

    public function refreshTicket()
    {
        if ($this->selectedTicket) {
            $this->selectedTicket->refresh()->load(['messages.user', 'messages.customer', 'customer', 'category']);
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Data tiket diperbarui.']);
        }
    }

    public function sendReply()
    {
        if (!$this->selectedTicket)
            return;

        $userId = auth()->id();
        $throttleKey = 'admin-reply-ticket-' . $userId;

        // Admins: 20 replies per minute
        if (RateLimiter::tooManyAttempts($throttleKey, 20)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->dispatch('toast', ['type' => 'error', 'message' => "Batas pengiriman tercapai. Coba lagi dalam {$seconds} detik."]);
            return;
        }

        // Cooldown: 2 seconds for admin (prevent accidental double click)
        $lastMessage = TicketMessage::where('user_id', $userId)
            ->latest()
            ->first();

        if ($lastMessage && $lastMessage->created_at->diffInSeconds(now()) < 2) {
            return;
        }

        $this->validate([
            'replyMessage' => 'required|string|max:3000',
        ]);

        TicketMessage::create([
            'ticket_id' => $this->selectedTicket->id,
            'user_id' => $userId, // Admin replied
            'message' => $this->replyMessage,
        ]);

        RateLimiter::hit($throttleKey, 60);

        // Auto-update status to in_progress if it was open
        if ($this->selectedTicket->status === 'open') {
            $this->selectedTicket->update(['status' => 'in_progress']);
        }

        $this->replyMessage = '';
        $this->selectedTicket->refresh()->load(['messages.user', 'messages.customer', 'customer.activeSubscription', 'category']);
        $this->dispatch('toast', ['type' => 'success', 'message' => 'Balasan terkirim.']);
    }

    public function updateStatus($id, $status)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->update(['status' => $status]);

        if ($this->selectedTicket && $this->selectedTicket->id == $id) {
            $this->selectedTicket->refresh()->load(['messages.user', 'messages.customer', 'customer.activeSubscription', 'category']);
        }

        $this->dispatch('toast', ['type' => 'success', 'message' => 'Status tiket diperbarui.']);
    }

    public function updatePriority($id, $priority)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->update(['priority' => $priority]);

        if ($this->selectedTicket && $this->selectedTicket->id == $id) {
            $this->selectedTicket->refresh()->load(['messages.user', 'messages.customer', 'customer.activeSubscription', 'category']);
        }

        $this->dispatch('toast', ['type' => 'success', 'message' => 'Prioritas tiket diperbarui.']);
    }


    #[Layout('layouts.app')]
    public function render()
    {
        // Pre-calculate counts for filter pills (efficient)
        $counts = Ticket::selectRaw("status, count(*) as count")
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $openTicketsCount = $counts['open'] ?? 0;
        $inProgressTicketsCount = $counts['in_progress'] ?? 0;
        $resolvedTicketsCount = $counts['resolved'] ?? 0;
        $closedTicketsCount = $counts['closed'] ?? 0;

        $query = Ticket::with(['customer.activeSubscription', 'category']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('subject', 'like', '%' . $this->search . '%')
                    ->orWhere('id', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customer', function ($q2) {
                        $q2->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('customer_id', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        return view('livewire.ticket.ticket-manager', [
            'tickets' => $query->orderBy('created_at', 'desc')->paginate(10),
            'openTicketsCount' => $openTicketsCount,
            'inProgressTicketsCount' => $inProgressTicketsCount,
            'resolvedTicketsCount' => $resolvedTicketsCount,
            'closedTicketsCount' => $closedTicketsCount,
            'categories' => \Illuminate\Support\Facades\Cache::remember('ticket_categories', 3600, fn() => \App\Models\TicketCategory::all()),
        ]);
    }
}
