<?php

namespace App\Contracts;

interface OltSnmpInterface
{
    /**
     * Get the status of a specific ONT.
     * 
     * @param string $ponIndex E.g., '1/1/1' for ZTE or Huawei
     * @param int|string $ontId E.g., 1
     * @return array Contains status info (e.g., ['status' => 'Online', 'rx_power' => '-22.5 dBm', 'error' => null])
     */
    public function getOntStatus(string $ponIndex, string|int $ontId): array;

    /**
     * Set the connection parameters for the SNMP driver.
     *
     * @param string $ip
     * @param string $community
     * @param string $version typically '2c' or '1'
     * @return self
     */
    public function setConnection(string $ip, string $community, string $version = '2c', int $port = 161): self;
    /**
     * Check device reachability and basic system status (Uptime, SysName)
     *
     * @return array Contains status info (e.g., ['status' => 'Online', 'uptime' => '10 days', 'name' => 'OLT-ZTE'])
     */
    public function checkSystemStatus(): array;

    /**
     * Get the statuses of all ONTs connected to this OLT
     *
     * @return array Array of ONT statuses indexed by their SNMP Index or PON/ONT ID
     */
    public function getAllOntStatuses(): array;

    /**
     * Check if SNMP extension is supported by current environment
     *
     * @return bool
     */
    public function checkSnmpSupport(): bool;
}
