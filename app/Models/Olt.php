<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Olt extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ip_address',
        'snmp_port',
        'port',
        'username',
        'password',
        'snmp_version',
        'snmp_community_read',
        'snmp_community_write',
        'brand', // zte, huawei
        'description',
    ];

    /**
     * Get the customers connected to this OLT.
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
}
