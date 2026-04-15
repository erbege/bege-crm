<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class BwProfile extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'rate_limit',
        'mikrotik_group',
        'radius_group',
        'burst_limit',
        'burst_threshold',
        'burst_time',
        'priority',
        'address_pool',
        'description',
        'is_active',
    ];

    protected static function booted()
    {
        static::deleting(function ($profile) {
            if ($profile->packages()->exists()) {
                throw new \Exception("Profil bandwidth tidak dapat dihapus karena sedang digunakan oleh paket.");
            }
        });
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'priority' => 'integer',
        ];
    }

    /**
     * Relationship to packages
     */
    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    /**
     * Scope for active profiles only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get full rate limit string for Mikrotik
     */
    public function getFullRateLimitAttribute(): string
    {
        $parts = [$this->rate_limit];

        if ($this->burst_limit) {
            $parts[] = $this->burst_limit;
        }
        if ($this->burst_threshold) {
            $parts[] = $this->burst_threshold;
        }
        if ($this->burst_time) {
            $parts[] = $this->burst_time;
        }
        if ($this->priority && $this->priority !== 8) {
            $parts[] = $this->priority;
        }

        return implode(' ', $parts);
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
