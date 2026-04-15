<?php

namespace App\Services\Snmp;

use App\Contracts\OltSnmpInterface;
use Illuminate\Support\Facades\Log;

class ZteSnmpDriver implements OltSnmpInterface
{
    protected string $ip;
    protected string $community;
    protected string $version;
    protected int $snmpPort;

    // Common OIDs for ZTE C320/C300 (examples)
    // OID for Phase state (Online/Offline)
    protected const OID_ONT_PHASE_STATE = '.1.3.6.1.4.1.3902.1012.3.28.2.1.4';
    // OID for ONT Rx Power
    protected const OID_ONT_RX_POWER = '.1.3.6.1.4.1.3902.1012.3.50.12.1.1.14';

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

        // Set global timeout for PHP SNMP (e.g., 2000000 microsec = 2 seconds)
        // to avoid long blocking on dead OLTs.
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
            $snmpIndex = $this->calculateZteIndex($ponIndex, $ontId);

            // Muffle the warning with @ to prevent Laravel Whoops on timeout/packet-loss
            $stateResponse = @snmp2_get($this->ip, $this->community, self::OID_ONT_PHASE_STATE . '.' . $snmpIndex);

            if ($stateResponse !== false) {
                $result['status'] = $this->parsePhaseState($stateResponse);

                // Save time by only querying power if the ONT is online
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
            Log::error("ZTE SNMP Error: " . $e->getMessage());
            $result['status'] = 'Error';
            $result['error'] = 'Exception: ' . $e->getMessage();
        }

        return $result;
    }

    /**
     * ZTE specific index calculation.
     * ZTE requires converting Rack/Slot/Port and ONT ID into a specific integer index.
     * Example format: (Rack << 24) + (Slot << 16) + (Port << 8)
     */
    protected function calculateZteIndex(string $pon, $ontId): string
    {
        // Placeholder for complex bitwise calculation based on ZTE MIB
        // Usually, 1/1/1 format is turned into integer index.
        // E.g., Rack 1, Slot 1, Port 1 -> 268501760

        // This is a dummy logic block representing the calculation idea
        $parts = explode('/', str_replace(':', '/', $pon));
        if (count($parts) >= 3) {
            $rack = (int) $parts[0];
            $slot = (int) $parts[1];
            $port = (int) $parts[2];

            $ponIndexInt = ($rack << 24) + ($slot << 16) + ($port << 8);
            // Append the ONT ID index format depending on MIB version
            return $ponIndexInt . "." . $ontId;
        }

        // Fallback or explicit mapping can be implemented here
        return "268501760." . $ontId;
    }

    protected function parsePhaseState(string $response): string
    {
        // Example response: "INTEGER: 3"
        // In ZTE MIB: 1=logging, 2=los, 3=sync/working, 4=offline, 5=dyingGasp, etc.
        $cleanResponse = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $response));

        if (str_contains($cleanResponse, '3') || str_contains($cleanResponse, 'working')) {
            return 'Online';
        }
        if (str_contains($cleanResponse, '2') || str_contains($cleanResponse, 'los')) {
            return 'LOS';
        }
        if (str_contains($cleanResponse, '5') || str_contains($cleanResponse, 'dying')) {
            return 'Dying Gasp';
        }
        return 'Offline';
    }

    protected function parseRxPower(string $response): string
    {
        // Example response: "INTEGER: -25000" for -25.00 dBm
        preg_match('/(-?\d+)/', $response, $matches);
        if (isset($matches[1])) {
            $val = (float) $matches[1];
            // ZTE usually uses 1000 or 32768, assuming 1000 here
            // Exclude extreme values that sometimes represent "No Signal" (like -65535)
            if ($val <= -60000 || $val == 0) {
                return '-';
            }
            return number_format($val / 1000, 2) . ' dBm';
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
            Log::error("ZTE SNMP System Error: " . $e->getMessage());
            $result['status'] = 'Error';
            $result['error'] = 'Exception: ' . $e->getMessage();
        }

        return $result;
    }

    protected function parseUptime(string $response): string
    {
        // Example response: "Timeticks: (12345678) 1 days, 10:17:36.78"
        // We just want "1 days, 10:17:36"
        if (preg_match('/\)\s*(.*)/', $response, $matches)) {
            // Strip milliseconds
            return preg_replace('/\.\d+$/', '', trim($matches[1]));
        }
        return $response;
    }

    protected function parseSysName(string $response): string
    {
        // Example response: "STRING: OLT-ZTE-C320"
        return trim(str_replace('STRING:', '', $response), " \"\t\n\r\0\x0B");
    }

    public function getAllOntStatuses(): array
    {
        $statuses = [];

        try {
            // Muffle warnings since some snmp calls might time out
            $stateWalk = @snmp2_real_walk($this->ip, $this->community, self::OID_ONT_PHASE_STATE);

            if ($stateWalk !== false) {
                foreach ($stateWalk as $oid => $val) {
                    $oidSuffix = str_replace(self::OID_ONT_PHASE_STATE . '.', '', $oid);

                    // The OID suffix usually is the ponIndex.ontId
                    preg_match('/\.?(\d+\.\d+)$/', $oid, $matches);
                    $index = $matches[1] ?? $oidSuffix;
                    // Strip leading dot if any
                    $index = ltrim($index, '.');

                    $statuses[$index] = [
                        'status' => $this->parsePhaseState($val),
                        'rx_power' => '-',
                        'error' => null
                    ];
                }

                // Fetch power for all ONTs
                $powerWalk = @snmp2_real_walk($this->ip, $this->community, self::OID_ONT_RX_POWER);

                if ($powerWalk !== false) {
                    foreach ($powerWalk as $oid => $val) {
                        preg_match('/\.?(\d+\.\d+)$/', $oid, $matches);
                        $index = $matches[1] ?? '';
                        $index = ltrim($index, '.');

                        if (isset($statuses[$index])) {
                            $statuses[$index]['rx_power'] = $this->parseRxPower($val);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("ZTE SNMP Walk Error: " . $e->getMessage());
        }

        return $statuses;
    }

    public function checkSnmpSupport(): bool
    {
        return function_exists('snmp_set_timeout_microsecond');
    }
}
