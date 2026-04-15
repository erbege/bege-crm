<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotspotVoucher extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $fillable = [
        'hotspot_profile_id',
        'code',
        'password',
        'batch_id',
        'status',
        'used_at',
        'expired_at',
        'created_by',
        'nas_id',
        'server',
        'user_mode',
        'time_limit',
        'data_limit',
        'comment',
    ];

    protected $casts = [
        'used_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    // Relations
    public function profile(): BelongsTo
    {
        return $this->belongsTo(HotspotProfile::class, 'hotspot_profile_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
