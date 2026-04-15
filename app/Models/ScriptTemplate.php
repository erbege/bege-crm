<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScriptTemplate extends Model
{
    protected $fillable = [
        'name',
        'brand',
        'type',
        'content',
    ];
}
