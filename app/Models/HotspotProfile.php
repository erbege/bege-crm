<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HotspotProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'mikrotik_group',
        'address_list',
        'rate_limit',
        'shared_users',
        'data_limit',
        'data_limit_unit',
        'time_limit',
        'time_limit_unit',
        'session_timeout',
        'keepalive_timeout',
        'price',
        'validity_value',
        'validity_unit',
        'description',
        'is_active',
    ];

    protected static function booted()
    {
        static::deleting(function ($profile) {
            if ($profile->vouchers()->exists()) {
                throw new \Exception("Profil hotspot tidak dapat dihapus karena sedang digunakan oleh voucher.");
            }
        });
    }

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'validity_value' => 'integer',
        'data_limit' => 'integer',
        'time_limit' => 'integer',
    ];

    // Relations
    public function vouchers(): HasMany
    {
        return $this->hasMany(HotspotVoucher::class);
    }

    /**
     * Get validity formatted for Mikrotik scripts/comments (e.g. 1d, 4h)
     */
    public function getValidityForMikrotikAttribute(): string
    {
        $unitMap = [
            'minutes' => 'm',
            'hours' => 'h',
            'days' => 'd',
            'weeks' => 'w',
            'months' => 'm',
        ];

        if ($this->validity_unit === 'months') {
            return ($this->validity_value * 30) . 'd';
        }

        return $this->validity_value . ($unitMap[$this->validity_unit] ?? 'd');
    }

    /**
     * Get validity in seconds
     */
    public function getValidityInSecondsAttribute(): int
    {
        return $this->convertToSeconds($this->validity_value, $this->validity_unit);
    }

    /**
     * Get time limit in seconds
     */
    public function getTimeLimitInSecondsAttribute(): ?int
    {
        if (!$this->time_limit || $this->time_limit_unit === 'UNLIMITED') {
            return null;
        }
        return $this->convertToSeconds($this->time_limit, $this->time_limit_unit);
    }

    /**
     * Get data limit in bytes
     */
    public function getDataLimitInBytesAttribute(): ?int
    {
        if (!$this->data_limit || $this->data_limit_unit === 'UNLIMITED') {
            return null;
        }

        $value = $this->data_limit;
        switch (strtoupper($this->data_limit_unit)) {
            case 'GB':
                return $value * 1024 * 1024 * 1024;
            case 'MB':
                return $value * 1024 * 1024;
            case 'KB':
                return $value * 1024;
            default:
                return $value;
        }
    }

    protected function convertToSeconds($value, $unit): int
    {
        switch ($unit) {
            case 'minutes':
                return $value * 60;
            case 'hours':
                return $value * 3600;
            case 'days':
                return $value * 86400;
            case 'weeks':
                return $value * 604800;
            case 'months':
                return $value * 2592000; // 30 days approximation
            default:
                return $value; // Assume seconds if unknown
        }
    }
}
