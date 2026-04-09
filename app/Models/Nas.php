<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Nas extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'nas';

    protected $fillable = [
        'name',
        'shortname',
        'ip_address',
        'api_port',
        'username',
        'password',
        'secret',
        'description',
        'is_active',
        'is_online',
        'last_check',
        'require_message_authenticator',
    ];

    protected $hidden = [
        'password',
        'secret',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'encrypted',
            'secret' => 'encrypted',
            'is_active' => 'boolean',
            'is_online' => 'boolean',
            'last_check' => 'datetime',
            'api_port' => 'integer',
            'require_message_authenticator' => 'boolean',
        ];
    }

    /**
     * Scope for active NAS only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the servers for the NAS.
     */
    public function servers()
    {
        return $this->hasMany(NasServer::class);
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
