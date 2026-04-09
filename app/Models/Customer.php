<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, LogsActivity;

    protected $fillable = [
        'customer_id',
        'name',
        'identity_number',
        'email',
        'phone',
        'address',
        'area_id',
        'registered_at',
        'latitude',
        'longitude',
        'notes',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'registered_at' => 'date',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'last_online_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relationship to Area
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }



    /**
     * Get current coverage point from active subscription
     */
    public function getCurrentCoveragePointAttribute()
    {
        return $this->activeSubscription?->coveragePoint;
    }

    /**
     * Relationship to Subscriptions
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Relationship to Histories
     */
    public function histories(): HasMany
    {
        return $this->hasMany(SubscriptionHistory::class);
    }

    /**
     * Relationship to Invoices
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Relationship to Tickets
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Get Active/Latest Subscription
     */
    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    /**
     * Get current status from latest subscription
     */
    public function getStatusAttribute(): string
    {
        $latest = $this->activeSubscription;

        if (!$latest) {
            return 'inactive';
        }

        return $latest->status;
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
            'inactive' => 'Nonaktif',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get Status Color Class (Tailwind)
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active' => 'text-green-800 bg-green-100 dark:bg-green-900 dark:text-green-300',
            'suspended' => 'text-red-800 bg-red-100 dark:bg-red-900 dark:text-red-300',
            'cancelled' => 'text-gray-800 bg-gray-100 dark:bg-gray-700 dark:text-gray-300',
            'pending' => 'text-yellow-800 bg-yellow-100 dark:bg-yellow-900 dark:text-yellow-300',
            'inactive' => 'text-gray-800 bg-gray-100 dark:bg-gray-700 dark:text-gray-300',
            default => 'text-gray-800 bg-gray-100',
        };
    }

    /**
     * Get current package from latest subscription
     */
    public function getCurrentPackageAttribute(): ?Package
    {
        return $this->activeSubscription?->package;
    }

    /**
     * Scope Active (has paid subscription)
     */
    public function scopeActive($query)
    {
        return $query->whereHas('activeSubscription', function ($q) {
            $q->where('status', 'active');
        });
    }

    /**
     * Scope Isolated (has overdue unpaid subscription)
     */
    public function scopeIsolated($query)
    {
        return $query->whereHas('activeSubscription', function ($q) {
            $q->where('status', 'suspended');
        });
    }

    /**
     * Scope Terminated (latest subscription is cancelled)
     */
    public function scopeTerminated($query)
    {
        return $query->whereHas('activeSubscription', function ($q) {
            $q->where('status', 'cancelled');
        });
    }

    /**
     * Scope Suspended (latest subscription is suspended, partial, or unpaid but NOT overdue)
     */
    public function scopeSuspended($query)
    {
        return $query->whereHas('activeSubscription', function ($q) {
            $q->where('status', 'suspended');
        });
    }

    /**
     * Scope Pending (latest subscription is pending)
     */
    public function scopePending($query)
    {
        return $query->whereHas('activeSubscription', function ($q) {
            $q->where('status', 'pending');
        });
    }

    /**
     * Scope Inactive (no subscription or latest is null/invalid)
     */
    public function scopeInactive($query)
    {
        return $query->doesntHave('activeSubscription');
    }

    /**
     * Activity Log Options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
