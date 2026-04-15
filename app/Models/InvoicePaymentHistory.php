<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoicePaymentHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'action',
        'amount',
        'payment_method',
        'reference',
        'notes',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    /**
     * Relationship to Invoice
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Relationship to User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get action label
     */
    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'payment' => 'Pembayaran',
            'rollback' => 'Pembatalan Pembayaran',
            'cancelled' => 'Invoice Dibatalkan',
            default => $this->action,
        };
    }

    /**
     * Get action color for badge
     */
    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'payment' => 'green',
            'rollback' => 'orange',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        $prefix = $this->action === 'rollback' ? '-' : '';
        return $prefix . 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    /**
     * Get payment method label
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'cash' => 'Tunai',
            'transfer' => 'Transfer Bank',
            'e-wallet' => 'E-Wallet',
            'qris' => 'QRIS',
            default => $this->payment_method ?? '-',
        };
    }
}
