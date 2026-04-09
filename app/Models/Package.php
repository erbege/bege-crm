<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Package extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'bw_profile_id',
        'name',
        'code',
        'price',
        'installation_fee',
        'description',
        'is_active',
        'service_type',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'price' => 'decimal:2',
            'installation_fee' => 'decimal:2',
        ];
    }



    /**
     * Relationship to bandwidth profile
     */
    public function bwProfile(): BelongsTo
    {
        return $this->belongsTo(BwProfile::class);
    }

    /**
     * Relationship to subscriptions
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Get formatted installation fee
     */
    public function getFormattedInstallationFeeAttribute(): string
    {
        return 'Rp ' . number_format($this->installation_fee, 0, ',', '.');
    }

    /**
     * Scope for active packages only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
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
