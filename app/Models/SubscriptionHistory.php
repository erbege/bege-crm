<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'customer_id',
        'previous_package_id',
        'current_package_id',
        'previous_status',
        'current_status',
        'type',
        'period_start',
        'period_end',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
        ];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function previousPackage(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'previous_package_id');
    }

    public function currentPackage(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'current_package_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'new' => 'Baru',
            'upgrade' => 'Upgrade',
            'downgrade' => 'Downgrade',
            'termination' => 'Berhenti',
            'status_change' => 'Perubahan Status',
            default => ucfirst($this->type),
        };
    }
}
