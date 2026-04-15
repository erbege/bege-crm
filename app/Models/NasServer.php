<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NasServer extends Model
{
    use HasFactory;

    protected $fillable = [
        'nas_id',
        'type',
        'name',
        'interface',
        'profile',
    ];

    public function nas(): BelongsTo
    {
        return $this->belongsTo(Nas::class);
    }
}
