<?php

namespace App\Services\Snmp;

use App\Contracts\OltSnmpInterface;
use App\Models\Olt;
use InvalidArgumentException;

class OltSnmpFactory
{
    /**
     * Create appropriate SNMP driver instance based on OLT brand.
     *
     * @param Olt $olt
     * @return OltSnmpInterface
     * @throws InvalidArgumentException
     */
    public static function make(Olt $olt): OltSnmpInterface
    {
        $driver = match (strtolower($olt->brand)) {
            'zte' => new ZteSnmpDriver(),
            'huawei' => new HuaweiSnmpDriver(),
            default => throw new InvalidArgumentException("Unsupported OLT brand for SNMP: {$olt->brand}"),
        };

        // Decrypt community string if necessary. Assuming it is plain or already decrypted string here.
        // For SKNET-CRM, modify this if `snmp_community_read` is encrypted in the DB.
        $community = $olt->snmp_community_read ?? 'public';
        $version = $olt->snmp_version ?? '2c';
        $port = $olt->snmp_port ?? 161;

        return $driver->setConnection($olt->ip_address, $community, $version, $port);
    }
}
