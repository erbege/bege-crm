<?php

namespace App\Services;

use Exception;
use RouterOS\Client;
use RouterOS\Query;

class MikrotikService
{
    protected $client;

    /**
     * Connect to Mikrotik
     */
    public function connect($ip, $user, $pass, $port = 8728)
    {
        try {
            $this->client = new Client([
                'host' => $ip,
                'user' => $user,
                'pass' => $pass,
                'port' => (int) $port,
                'timeout' => 3,
            ]);
            return true;
        } catch (Exception $e) {
            throw new Exception("Gagal Konek Mikrotik: " . $e->getMessage());
        }
    }

    /**
     * Get PPPoE Status
     */
    public function getPppoeStatus(string $pppoeUsername)
    {
        if (!$this->client) {
            throw new Exception("Mikrotik client not connected.");
        }

        $query = (new Query('/ppp/active/print'))
            ->where('name', $pppoeUsername);

        $response = $this->client->query($query)->read();

        if (empty($response)) {
            return [
                'is_online' => false,
                'message' => 'Offline / Waiting for Dial'
            ];
        }

        $data = $response[0];

        return [
            'is_online' => true,
            'ip_address' => $data['address'] ?? '-',
            'uptime' => $data['uptime'] ?? '-',
            'mac_address' => $data['caller-id'] ?? '-',
            'session_id' => $data['.id'] ?? null,
            'raw_data' => $data
        ];
    }

    /**
     * Kick User by Username and Service Type
     * 
     * @param string $username
     * @param string $type 'pppoe' or 'hotspot'
     * @return bool
     */
    public function kickUser(string $username, string $type = 'pppoe'): bool
    {
        if (!$this->client) {
            // Attempt to reconnect if using internally cached credentials, 
            // but since we don't store them in the class, we rely on caller to connect() first.
            // OR we can throw exception.
            throw new Exception("Mikrotik client not connected.");
        }

        try {
            // Determine endpoint and property
            $endpoint = '/ppp/active';
            $property = 'name';

            if ($type === 'hotspot') {
                $endpoint = '/ip/hotspot/active';
                $property = 'user';
            }

            // 1. Find the user
            $query = (new Query($endpoint . '/print'))
                ->where($property, $username);

            $users = $this->client->query($query)->read();

            if (empty($users)) {
                // User not online, consider success
                return true;
            }

            // 2. Loop and remove (in case of multiple sessions, though unlikely for PPPoE)
            foreach ($users as $user) {
                // Mikrotik ID
                $id = $user['.id'];

                $removeQuery = (new Query($endpoint . '/remove'))
                    ->equal('.id', $id);

                $this->client->query($removeQuery)->read();
            }

            return true;

        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to kick user {$username}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Remove Hotspot Cookie for a user.
     * 
     * @param string $username
     * @return bool
     */
    public function removeHotspotCookie(string $username): bool
    {
        if (!$this->client) {
            return false;
        }

        try {
            $query = (new Query('/ip/hotspot/cookie/print'))
                ->where('user', $username);

            $cookies = $this->client->query($query)->read();

            foreach ($cookies as $cookie) {
                $removeQuery = (new Query('/ip/hotspot/cookie/remove'))
                    ->equal('.id', $cookie['.id']);
                $this->client->query($removeQuery)->read();
            }

            return true;
        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to remove cookie for {$username}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kick User from specific NAS (Connects automatically).
     *
     * @param \App\Models\Nas $nas
     * @param string $username
     * @param string $type
     * @return bool
     */
    public function kickUserFromNas(\App\Models\Nas $nas, string $username, string $type = 'pppoe'): bool
    {
        try {
            $this->connect($nas->ip_address, $nas->username, $nas->password, $nas->api_port);
            return $this->kickUser($username, $type);
        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to kick user {$username} from NAS {$nas->shortname}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check connection status of a NAS.
     * 
     * @param \App\Models\Nas $nas
     * @return bool
     */
    public function checkConnection(\App\Models\Nas $nas): bool
    {
        try {
            $this->connect($nas->ip_address, $nas->username, $nas->password, $nas->api_port);
            // Just trying to connect is enough, but let's run a lightweight command
            $query = new Query('/system/identity/print');
            $this->client->query($query)->read();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get system resources and identity from NAS.
     *
     * @param \App\Models\Nas $nas
     * @return array|null
     */
    public function getSystemInfo(\App\Models\Nas $nas): ?array
    {
        try {
            $this->connect($nas->ip_address, $nas->username, $nas->password, $nas->api_port);

            $identity = $this->client->query(new Query('/system/identity/print'))->read();
            $resource = $this->client->query(new Query('/system/resource/print'))->read();
            $routerboard = $this->client->query(new Query('/system/routerboard/print'))->read();

            // Try standard disk check
            try {
                $disk = $this->client->query(new Query('/store/disk/print'))->read();
            } catch (Exception $e) {
                $disk = [];
            }

            if (empty($disk)) {
                try {
                    $disk = $this->client->query(new Query('/disk/print'))->read();
                } catch (Exception $e) {
                    $disk = [];
                }
            }

            return [
                'identity' => $identity[0]['name'] ?? 'Unknown',
                'version' => $resource[0]['version'] ?? 'Unknown',
                'platform' => $resource[0]['platform'] ?? 'Unknown',
                'board_name' => $resource[0]['board-name'] ?? ($routerboard[0]['model'] ?? 'Unknown'),
                'uptime' => $resource[0]['uptime'] ?? 'Unknown',
                'cpu_load' => $resource[0]['cpu-load'] ?? 0,
                'free_memory' => $resource[0]['free-memory'] ?? 0,
                'total_memory' => $resource[0]['total-memory'] ?? 0,
                'free_hdd' => $resource[0]['free-hdd-space'] ?? 0,
                'total_hdd' => $resource[0]['total-hdd-space'] ?? 0,
            ];

        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to get info from NAS {$nas->shortname}: " . $e->getMessage());
            return null;
        }
    }
    /**
     * Get list of servers (PPPoE, Hotspot, DHCP) from NAS.
     * 
     * @param \App\Models\Nas $nas
     * @return array
     */
    public function getServerList(\App\Models\Nas $nas): array
    {
        try {
            $this->connect($nas->ip_address, $nas->username, $nas->password, $nas->api_port);

            $servers = [];

            // PPPoE Servers
            $pppoe = $this->client->query(new Query('/interface/pppoe-server/server/print'))->read();
            foreach ($pppoe as $item) {
                $servers[] = [
                    'type' => 'pppoe',
                    'name' => $item['service-name'] ?? $item['name'] ?? 'Unknown', // Mikrotik PPPoE server uses service-name mostly
                    'interface' => $item['interface'] ?? '-',
                    'profile' => $item['default-profile'] ?? '-',
                ];
            }

            // Hotspot Servers (Profile)
            // Hotspot server configuration usually links to a profile.
            $hotspot = $this->client->query(new Query('/ip/hotspot/print'))->read();
            foreach ($hotspot as $item) {
                $servers[] = [
                    'type' => 'hotspot',
                    'name' => $item['name'] ?? 'Unknown',
                    'interface' => $item['interface'] ?? '-',
                    'profile' => $item['profile'] ?? '-',
                ];
            }

            // DHCP Servers
            $dhcp = $this->client->query(new Query('/ip/dhcp-server/print'))->read();
            foreach ($dhcp as $item) {
                $servers[] = [
                    'type' => 'dhcp',
                    'name' => $item['name'] ?? 'Unknown',
                    'interface' => $item['interface'] ?? '-',
                    'profile' => '-', // DHCP doesn't have "profile" in the same sense, maybe lease-script or something else
                ];
            }

            return $servers;

        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to get server list from NAS {$nas->shortname}: " . $e->getMessage());
            return [];
        }
    }
}
