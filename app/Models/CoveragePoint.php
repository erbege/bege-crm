<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class CoveragePoint extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'area_id',
        'name',
        'code',
        'type',
        'capacity',
        'used_ports',
        'latitude',
        'longitude',
        'address',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'capacity' => 'integer',
            'used_ports' => 'integer',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    /**
     * Area relationship
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Get available ports
     */
    public function getAvailablePortsAttribute(): ?int
    {
        if ($this->capacity === null) {
            return null;
        }
        return max(0, $this->capacity - $this->used_ports);
    }

    /**
     * Get capacity percentage used
     */
    public function getUsagePercentageAttribute(): ?float
    {
        if ($this->capacity === null || $this->capacity === 0) {
            return null;
        }
        return round(($this->used_ports / $this->capacity) * 100, 1);
    }

    /**
     * Get type label in Indonesian
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'odp' => 'ODP',
            'odc' => 'ODC',
            'olt' => 'OLT',
            'pole' => 'Tiang',
            default => strtoupper($this->type),
        };
    }

    /**
     * Get marker color based on type
     */
    public function getMarkerColorAttribute(): string
    {
        return match ($this->type) {
            'odp' => 'blue',
            'odc' => 'green',
            'olt' => 'red',
            'pole' => 'orange',
            default => 'gray',
        };
    }

    /**
     * Scope for active points
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for filtering by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
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
    /**
     * Update used ports count based on active subscriptions
     */
    public function updateUsedPorts(): void
    {
        // Count unique customers with non-cancelled subscriptions on this point
        $count = \App\Models\Subscription::where('coverage_point_id', $this->id)
            ->where('status', '!=', 'cancelled')
            ->distinct('customer_id')
            ->count('customer_id');

        $this->update(['used_ports' => $count]);
    }
}
