<?php

namespace App\Services\Snmp;

use App\Contracts\OltSnmpInterface;
use Illuminate\Support\Facades\Log;

class HuaweiSnmpDriver implements OltSnmpInterface
{
    protected string $ip;
    protected string $community;
    protected string $version;
    protected int $snmpPort;

    // Huawei uses completely different OIDs (hwGponDeviceOntControlTable)
    protected const OID_ONT_RUN_STATE = '.1.3.6.1.4.1.2011.6.128.1.1.2.46.1.15';
    protected const OID_ONT_RX_POWER = '.1.3.6.1.4.1.2011.6.128.1.1.2.51.1.4';

    public function setConnection(string $ip, string $community, string $version = '2c', int $port = 161): self
    {
        $this->ip = ($port === 161) ? $ip : "{$ip}:{$port}";
        $this->community = $community;
        $this->version = $version;
        $this->snmpPort = $port;

        // Check if SNMP extension is loaded
        if (!function_exists('snmp_set_timeout_microsecond')) {
            Log::warning('PHP SNMP extension is not installed or enabled. SNMP operations for OLT will fail.');
            return $this;
        }

        @snmp_set_timeout_microsecond(2000000);
        @snmp_set_retries(1);
        @snmp_set_oid_numeric_print(true);

        return $this;
    }

    public function getOntStatus(string $ponIndex, string|int $ontId): array
    {
        $result = [
            'status' => 'Unknown',
            'rx_power' => '-',
            'error' => null
        ];

        try {
            $snmpIndex = $this->calculateHuaweiIndex($ponIndex, $ontId);

            $stateResponse = @snmp2_get($this->ip, $this->community, self::OID_ONT_RUN_STATE . '.' . $snmpIndex);

            if ($stateResponse !== false) {
                $result['status'] = $this->parseRunState($stateResponse);

                if ($result['status'] === 'Online') {
                    $powerResponse = @snmp2_get($this->ip, $this->community, self::OID_ONT_RX_POWER . '.' . $snmpIndex);
                    if ($powerResponse !== false) {
                        $result['rx_power'] = $this->parseRxPower($powerResponse);
                    }
                }
            } else {
                $result['status'] = 'Offline/Timeout';
                $result['error'] = 'No response from OLT or Invalid SNMP Index';
            }

        } catch (\Exception $e) {
            Log::error("Huawei SNMP Error: " . $e->getMessage());
            $result['status'] = 'Error';
            $result['error'] = 'Exception: ' . $e->getMessage();
        }

        return $result;
    }

    /**
     * Huawei specific index calculation.
     * Often uses an Internal IfIndex representation for the PON Port + ONT ID.
     */
    protected function calculateHuaweiIndex(string $pon, $ontId): string
    {
        // Usually you have to fetch the ifIndex of the PON first, or map it.
        // For demonstration, a common representation index:
        return "4194312192." . $ontId;
    }

    protected function parseRunState(string $response): string
    {
        // 1: online, 2: offline, etc.
        $cleanResponse = preg_replace('/[^0-9]/', '', $response);

        if ($cleanResponse === '1') {
            return 'Online';
        }
        if ($cleanResponse === '2') {
            return 'Offline';
        }
        return 'Offline';
    }

    protected function parseRxPower(string $response): string
    {
        // E.g., Huawei returns "-2500" for -25.00 dBm (divisor is 100)
        preg_match('/(-?\d+)/', $response, $matches);
        if (isset($matches[1])) {
            $val = (float) $matches[1];
            // Filter extreme values
            if ($val <= -6000 || $val == 0 || $val == 2147483647) {
                return '-';
            }
            return number_format($val / 100, 2) . ' dBm';
        }

        return '-';
    }

    public function checkSystemStatus(): array
    {
        $result = [
            'status' => 'Unknown',
            'uptime' => '-',
            'name' => '-',
            'error' => null
        ];

        try {
            // Standard MIB-2 OIDs
            $oidSysUpTime = '.1.3.6.1.2.1.1.3.0';
            $oidSysName = '.1.3.6.1.2.1.1.5.0';

            $uptimeResponse = @snmp2_get($this->ip, $this->community, $oidSysUpTime);

            if ($uptimeResponse !== false) {
                $result['status'] = 'Online';
                $result['uptime'] = $this->parseUptime($uptimeResponse);

                $nameResponse = @snmp2_get($this->ip, $this->community, $oidSysName);
                if ($nameResponse !== false) {
                    $result['name'] = $this->parseSysName($nameResponse);
                }
            } else {
                $result['status'] = 'Offline/Timeout';
                $result['error'] = 'No response from OLT';
            }
        } catch (\Exception $e) {
            Log::error("Huawei SNMP System Error: " . $e->getMessage());
            $result['status'] = 'Error';
            $result['error'] = 'Exception: ' . $e->getMessage();
        }

        return $result;
    }

    protected function parseUptime(string $response): string
    {
        if (preg_match('/\)\s*(.*)/', $response, $matches)) {
            return preg_replace('/\.\d+$/', '', trim($matches[1]));
        }
        return $response;
    }

    protected function parseSysName(string $response): string
    {
        return trim(str_replace('STRING:', '', $response), " \"\t\n\r\0\x0B");
    }

    public function getAllOntStatuses(): array
    {
        $statuses = [];

        try {
            $stateWalk = @snmp2_real_walk($this->ip, $this->community, self::OID_ONT_RUN_STATE);

            if ($stateWalk !== false) {
                foreach ($stateWalk as $oid => $val) {
                    preg_match('/\.?(\d+\.\d+)$/', $oid, $matches);
                    $index = ltrim($matches[1] ?? '', '.');

                    if ($index) {
                        $statuses[$index] = [
                            'status' => $this->parseRunState($val),
                            'rx_power' => '-',
                            'error' => null
                        ];
                    }
                }

                $powerWalk = @snmp2_real_walk($this->ip, $this->community, self::OID_ONT_RX_POWER);

                if ($powerWalk !== false) {
                    foreach ($powerWalk as $oid => $val) {
                        preg_match('/\.?(\d+\.\d+)$/', $oid, $matches);
                        $index = ltrim($matches[1] ?? '', '.');

                        if ($index && isset($statuses[$index])) {
                            $statuses[$index]['rx_power'] = $this->parseRxPower($val);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("Huawei SNMP Walk Error: " . $e->getMessage());
        }

        return $statuses;
    }

    public function checkSnmpSupport(): bool
    {
        return function_exists('snmp_set_timeout_microsecond');
    }
}
