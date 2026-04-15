<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $id
 * @property int $customer_id
 * @property int $package_id
 * @property \Carbon\Carbon $period_start
 * @property \Carbon\Carbon $period_end
 * @property \Carbon\Carbon|null $installation_date
 * @property string $status
 * @property \Carbon\Carbon|null $paid_at
 * @property \Carbon\Carbon|null $provisioned_at
 */
class Subscription extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'customer_id',
        'package_id',
        'coverage_point_id',
        'period_start',
        'period_end',
        'installation_date',
        'status',
        'notes',
        'subscription_type',
        'pppoe_username',
        'pppoe_password',
        'device_sn',
        'olt_id',
        'olt_frame',
        'olt_slot',
        'olt_port',
        'olt_onu_id',
        'service_vlan',
        'last_online_at',
        'provisioned_at',
        'last_provisioning_log',
        'nas_id',
        'server_name',
        'service_type',
        'mac_address',
        'ip_address',
    ];

    protected static function booted()
    {
        static::created(function ($subscription) {
            if ($subscription->coverage_point_id) {
                $subscription->coveragePoint?->updateUsedPorts();
            }
        });

        static::updated(function ($subscription) {
            // If coverage point changed or status changed
            if ($subscription->isDirty('coverage_point_id') || $subscription->isDirty('status')) {
                // Update new point
                if ($subscription->coverage_point_id) {
                    $subscription->coveragePoint?->updateUsedPorts();
                }

                // Update old point if changed
                if ($subscription->isDirty('coverage_point_id')) {
                    $oldPointId = $subscription->getOriginal('coverage_point_id');
                    if ($oldPointId) {
                        \App\Models\CoveragePoint::find($oldPointId)?->updateUsedPorts();
                    }
                }
            }
        });

        static::deleted(function ($subscription) {
            if ($subscription->coverage_point_id) {
                $subscription->coveragePoint?->updateUsedPorts();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'installation_date' => 'date',
            'last_online_at' => 'datetime',
            'provisioned_at' => 'datetime',
            'last_provisioning_log' => 'array',
        ];
    }

    /**
     * Relationship to Customer
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relationship to Package
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Relationship to Coverage Point (ODP/ODC)
     */
    public function coveragePoint(): BelongsTo
    {
        return $this->belongsTo(CoveragePoint::class);
    }

    /**
     * Relationship to OLT
     */
    public function olt(): BelongsTo
    {
        return $this->belongsTo(Olt::class);
    }

    /**
     * Relationship to Invoices
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Relationship to Histories
     */
    public function histories(): HasMany
    {
        return $this->hasMany(SubscriptionHistory::class);
    }

    /**
     * Relationship to NAS (Router)
     */
    public function nas(): BelongsTo
    {
        return $this->belongsTo(Nas::class);
    }

    /**
     * Get Status Label (Bahasa Indonesia)
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Aktif',
            'suspended' => 'Terisolir',
            'cancelled' => 'Dibatalkan',
            'pending' => 'Menunggu',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get Status Color Class (Tailwind)
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active' => 'text-emerald-700 bg-emerald-50 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20',
            'suspended' => 'text-orange-700 bg-orange-50 ring-1 ring-inset ring-orange-600/20 dark:bg-orange-500/10 dark:text-orange-400 dark:ring-orange-500/20',
            'cancelled' => 'text-slate-700 bg-slate-50 ring-1 ring-inset ring-slate-600/20 dark:bg-slate-500/10 dark:text-slate-400 dark:ring-slate-500/20',
            'pending' => 'text-amber-700 bg-amber-50 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/20',
            default => 'text-gray-700 bg-gray-50 ring-1 ring-inset ring-gray-600/20 dark:bg-gray-500/10 dark:text-gray-400 dark:ring-gray-500/20',
        };
    }



    /**
     * Get period label (e.g., "Januari 2026")
     */
    public function getPeriodLabelAttribute(): string
    {
        return $this->period_start->isoFormat('MMMM Y');
    }

    /**
     * Scope for unpaid subscriptions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for paid subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for overdue subscriptions (unpaid and past period_end)
     */
    public function scopeOverdue($query)
    {
        return $query->whereIn('status', ['pending', 'active'])
            ->where('period_end', '<', now());
    }

    /**
     * Scope by period (month and year)
     */
    public function scopeByPeriod($query, $month, $year)
    {
        return $query->whereMonth('period_start', $month)
            ->whereYear('period_start', $year);
    }

    /**
     * Check if subscription is overdue
     */
    public function isOverdue(): bool
    {
        // Overdue if pending/active and period_end has passed (checked against end of day)
        return in_array($this->status, ['pending', 'active']) && $this->period_end->endOfDay()->isPast();
    }

    /**
     * Activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
