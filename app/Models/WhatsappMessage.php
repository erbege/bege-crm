<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'target',
        'message',
        'template_name',
        'template_data',
        'status',
        'provider',
        'response',
        'error',
        'scheduled_at',
    ];

    protected $casts = [
        'template_data' => 'array',
        'response' => 'array',
        'scheduled_at' => 'datetime',
    ];
}
