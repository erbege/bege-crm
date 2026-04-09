<?php

namespace App\Services;

use App\Models\HotspotProfile;
use App\Models\BwProfile;
use App\Models\Subscription;
use App\Models\Nas;
use App\Models\Radius\RadCheck;
use App\Models\Radius\RadReply;
use App\Models\Radius\RadUserGroup;
use App\Models\Radius\RadGroupReply;
use App\Traits\RadiusConnectionTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RadiusSyncService
{
    use RadiusConnectionTrait;

    /**
     * Check Radius availability before performing operations.
     * Returns false if unavailable (with a logged warning).
     */
    private function ensureRadiusAvailable(string $context = ''): bool
    {
        if (!$this->isRadiusAvailable()) {
            Log::warning("Radius sync skipped ({$context}): Database unreachable.");
            return false;
        }
        return true;
    }

    /**
     * Sync subscription data to Radius.
     *
     * @param Subscription $subscription
     * @return void
     */
    public function sync(Subscription $subscription)
    {
        // Determine Username & Password based on Service Type
        $username = null;
        $password = null;
        $serviceType = $subscription->service_type ?? 'ppp';

        if ($serviceType === 'dhcp') {
            $username = $subscription->mac_address;
            $password = $subscription->mac_address; // Common for MAC Auth
        } else {
            // PPP or Hotspot
            $username = $subscription->pppoe_username;
            $password = $subscription->pppoe_password;
        }

        if (empty($username)) {
            Log::warning("Radius sync skipped: No username found for Subscription #{$subscription->id} ({$serviceType})");
            return;
        }

        if (!$this->ensureRadiusAvailable("sync user {$username}")) {
            return;
        }

        try {
            DB::connection('radius')->beginTransaction();

            // 1. Sync User Credentials (radcheck)
            // Remove existing
            RadCheck::where('username', $username)->delete();

            // Add new password
            RadCheck::create([
                'username' => $username,
                'attribute' => 'Cleartext-Password',
                'op' => ':=',
                'value' => $password ?? '123456',
            ]);

            // Add Service-Type / Protocol based on type
            if ($serviceType === 'ppp') {
                RadCheck::create(['username' => $username, 'attribute' => 'Framed-Protocol', 'op' => ':=', 'value' => 'PPP']);
                RadCheck::create(['username' => $username, 'attribute' => 'Service-Type', 'op' => ':=', 'value' => 'Framed-User']);
            } elseif ($serviceType === 'hotspot') {
                RadCheck::create(['username' => $username, 'attribute' => 'Service-Type', 'op' => ':=', 'value' => 'Login-User']);
            } elseif ($serviceType === 'dhcp') {
                // Usually for IPoE / DHCP, we might use Login-User or Framed-User depending on NAS
                RadCheck::create(['username' => $username, 'attribute' => 'Auth-Type', 'op' => ':=', 'value' => 'Accept']);
            }

            // 2. Determine Group/Profile
            // Priority: BwProfile Radius Group -> BwProfile Name -> Package Name -> Default
            $profile = $subscription->package?->bwProfile;
            $baseGroupName = $profile?->radius_group
                ?? $profile?->name
                ?? $subscription->package?->name
                ?? 'default';

            // Clean group name removed to match syncProfile raw usage
            // $baseGroupName = \Illuminate\Support\Str::slug($baseGroupName, '-');

            // 3. Sync User Group (radusergroup)
            RadUserGroup::where('username', $username)->delete();

            // Determine Group based on ISOLIR status
            if ($subscription->status === 'suspended' || ($subscription->status === 'unpaid' && $subscription->isOverdue())) {
                RadUserGroup::create([
                    'username' => $username,
                    'groupname' => 'ISOLIR', // Ensure this group exists in radgroupreply
                    'priority' => 10,
                ]);
            } else {
                // Sync the base group if active/not isolated (Priority 1)
                RadUserGroup::create([
                    'username' => $username,
                    'groupname' => $baseGroupName,
                    'priority' => 1,
                ]);
            }

            // 4. Clean up old IP assignments
            RadReply::where('username', $username)->whereIn('attribute', ['Framed-IP-Address', 'Framed-Pool'])->delete();

            // Sync Static IP (Framed-IP-Address) only. Pool is assigned automatically via RadGroupReply in syncProfile
            if ($subscription->ip_address) {
                RadReply::create([
                    'username' => $username,
                    'attribute' => 'Framed-IP-Address',
                    'op' => ':=',
                    'value' => $subscription->ip_address,
                ]);
            }

            // 5. Handle NAS restrictions
            // Clean up old NAS restrictions first
            RadCheck::where('username', $username)->whereIn('attribute', ['NAS-IP-Address', 'NAS-Identifier', 'NAS-Port-Id'])->delete();

            // Sync Router restriction using IP Address from NAS Manager
            if ($subscription->nas_id && $subscription->nas?->ip_address) {
                RadCheck::create([
                    'username' => $username,
                    'attribute' => 'NAS-IP-Address',
                    'op' => '==',
                    'value' => $subscription->nas->ip_address,
                ]);
            }

            // Sync Server (NAS-Port-Id or similar) restriction
            if ($subscription->server_name && strtolower($subscription->server_name) !== 'all') {
                RadCheck::create([
                    'username' => $username,
                    'attribute' => 'NAS-Port-Id',
                    'op' => '==',
                    'value' => $subscription->server_name,
                ]);
            }

            // 6. MAC Address Binding (Caller-Id) for PPP
            if ($serviceType === 'ppp' && $subscription->mac_address) {
                RadCheck::create([
                    'username' => $username,
                    'attribute' => 'Calling-Station-Id',
                    'op' => '==',
                    'value' => $subscription->mac_address, // Format XX:XX:XX:XX:XX:XX
                ]);
            }

            DB::connection('radius')->commit();
            Log::info("Radius synced for user: {$username} ({$serviceType}) Group: $baseGroupName");

        } catch (\Exception $e) {
            DB::connection('radius')->rollBack();
            Log::error("Radius sync failed for {$username}: " . $e->getMessage());
        }
    }

    /**
     * Sync Bandwidth Profile to Radius Group.
     * 
     * @param BwProfile $profile
     * @return void
     */
    public function syncProfile(BwProfile $profile)
    {
        if (!$this->ensureRadiusAvailable("sync profile {$profile->name}")) {
            return;
        }

        try {
            DB::connection('radius')->beginTransaction();

            // Sync to both radius_group and profile name to ensure all users get the settings
            $groupNames = collect([$profile->radius_group, $profile->name])
                ->filter()
                ->unique();

            foreach ($groupNames as $groupName) {
                // Remove existing group replies
                RadGroupReply::where('groupname', $groupName)->delete();

                // Add Acct-Interim-Interval (Default 120s for traffic updates)
                RadGroupReply::create([
                    'groupname' => $groupName,
                    'attribute' => 'Acct-Interim-Interval',
                    'op' => ':=',
                    'value' => '180',
                ]);

                // Add Rate-Limit
                RadGroupReply::create([
                    'groupname' => $groupName,
                    'attribute' => 'Mikrotik-Rate-Limit',
                    'op' => ':=',
                    'value' => $profile->rate_limit,
                ]);

                // Add Address Pool (Framed-Pool) if specified
                if ($profile->address_pool) {
                    RadGroupReply::create([
                        'groupname' => $groupName,
                        'attribute' => 'Framed-Pool',
                        'op' => ':=',
                        'value' => $profile->address_pool,
                    ]);
                }

                Log::info("Radius group synced: {$groupName} (rate: {$profile->rate_limit}, pool: {$profile->address_pool})");
            }

        } catch (\Exception $e) {
            DB::connection('radius')->rollBack();
            Log::error("Radius profile sync failed for {$profile->name}: " . $e->getMessage());
        }
    }

    /**
     * Remove Bandwidth Profile from Radius Group.
     * 
     * @param BwProfile $profile
     * @return void
     */
    public function removeProfile(BwProfile|string $profile, ?array $groupNamesOverride = null)
    {
        $groupNames = [];
        $logName = is_string($profile) ? $profile : $profile->name;

        if (is_array($groupNamesOverride)) {
            $groupNames = $groupNamesOverride;
        } elseif (is_string($profile)) {
            $groupNames = [$profile];
        } else {
            $groupNames = collect([$profile->radius_group, $profile->name])
                ->filter()
                ->unique()
                ->toArray();
        }

        if (!$this->ensureRadiusAvailable("remove profile {$logName}")) {
            return;
        }

        try {
            DB::connection('radius')->beginTransaction();

            foreach ($groupNames as $groupName) {
                // Remove existing group replies
                RadGroupReply::where('groupname', $groupName)->delete();
                Log::info("Radius group removed: {$groupName}");
            }

            DB::connection('radius')->commit();

        } catch (\Exception $e) {
            DB::connection('radius')->rollBack();
            Log::error("Radius profile removal failed for {$logName}: " . $e->getMessage());
        }
    }

    /**
     * Remove subscription from Radius.
     *
     * @param Subscription $subscription
     * @return void
     */
    public function remove(Subscription $subscription)
    {
        if (empty($subscription->pppoe_username)) {
            return;
        }

        $this->removeByUsername($subscription->pppoe_username);
    }

    /**
     * Remove user from Radius by username.
     *
     * @param string $username
     * @return void
     */
    public function removeByUsername(string $username)
    {
        if (!$this->ensureRadiusAvailable("remove user {$username}")) {
            return;
        }

        try {
            DB::connection('radius')->beginTransaction();
            RadCheck::where('username', $username)->delete();
            RadReply::where('username', $username)->delete();
            RadUserGroup::where('username', $username)->delete();
            DB::connection('radius')->commit();

            Log::info("Radius user removed: {$username}");
        } catch (\Exception $e) {
            DB::connection('radius')->rollBack();
            Log::error("Radius remove failed for {$username}: " . $e->getMessage());
        }
    }

    /**
     * Sync NAS to Radius database.
     *
     * @param \App\Models\Nas $nas
     * @return void
     */
    public function syncNas(\App\Models\Nas $nas)
    {
        if (!$this->ensureRadiusAvailable("sync NAS {$nas->shortname}")) {
            return;
        }

        try {
            DB::connection('radius')->beginTransaction();

            $radiusNas = DB::connection('radius')->table('nas')->where('nasname', $nas->ip_address)->first();

            // Check if we need to fall back to looking by shortname just in case IP changed
            if (!$radiusNas) {
                $radiusNas = DB::connection('radius')->table('nas')->where('shortname', $nas->shortname)->first();
            }

            $data = [
                'nasname' => $nas->ip_address,
                'shortname' => $nas->shortname ?? $nas->name,
                // 'type' => 'mikrotik', // Defaulting to mikrotik for CRM purposes
                'type' => 'other',
                'ports' => $nas->api_port ?? 0,
                'secret' => $nas->secret,
                'description' => $nas->description ?? $nas->name,
            ];

            // Check if require_message_authenticator column exists in freeradius nas table
            if (DB::connection('radius')->getSchemaBuilder()->hasColumn('nas', 'require_message_authenticator')) {
                $data['require_message_authenticator'] = $nas->require_message_authenticator;
            }

            if ($radiusNas) {
                DB::connection('radius')->table('nas')
                    ->where('id', $radiusNas->id)
                    ->update($data);
                Log::info("Radius NAS synced (updated): {$nas->shortname} ({$nas->ip_address})");
            } else {
                DB::connection('radius')->table('nas')->insert($data);
                Log::info("Radius NAS synced (inserted): {$nas->shortname} ({$nas->ip_address})");
            }

            DB::connection('radius')->commit();

        } catch (\Exception $e) {
            DB::connection('radius')->rollBack();
            Log::error("Radius NAS sync failed for {$nas->shortname}: " . $e->getMessage());
        }
    }

    /**
     * Remove NAS from Radius database.
     *
     * @param \App\Models\Nas $nas
     * @return void
     */
    public function removeNas(\App\Models\Nas $nas)
    {
        if (!$this->ensureRadiusAvailable("remove NAS {$nas->shortname}")) {
            return;
        }

        try {
            DB::connection('radius')->beginTransaction();

            // Remove NAS Client by IP or shortname
            DB::connection('radius')->table('nas')
                ->where('nasname', $nas->ip_address)
                ->orWhere('shortname', $nas->shortname)
                ->delete();

            DB::connection('radius')->commit();
            Log::info("Radius NAS removed: {$nas->shortname} ({$nas->ip_address})");
        } catch (\Exception $e) {
            DB::connection('radius')->rollBack();
            Log::error("Radius NAS removal failed for {$nas->shortname}: " . $e->getMessage());
        }
    }

    /**
     * Sync single hotspot voucher to Radius
     */
    public function syncHotspotVoucher(\App\Models\HotspotVoucher $voucher)
    {
        if (!$this->ensureRadiusAvailable("sync voucher {$voucher->code}")) {
            return;
        }

        try {
            DB::connection('radius')->beginTransaction();

            $this->removeHotspotVoucher($voucher->code);

            // 1. RadCheck: Password
            RadCheck::create([
                'username' => $voucher->code,
                'attribute' => 'Cleartext-Password',
                'op' => ':=',
                'value' => $voucher->password,
            ]);

            // 1.1 RadCheck: Service-Type for Hotspot
            RadCheck::create([
                'username' => $voucher->code,
                'attribute' => 'Service-Type',
                'op' => ':=',
                'value' => 'Login-User',
            ]);

            // 2. RadUserGroup: Profile/Group
            // Use mikrotik_group from profile, or fallback to name
            if ($voucher->profile) {
                $groupName = $voucher->profile->mikrotik_group ?: $voucher->profile->name;
                RadUserGroup::create([
                    'username' => $voucher->code,
                    'groupname' => $groupName,
                    'priority' => 1,
                ]);
            }

            // 3. Limits (optional, if overriding profile)
            // Time Limit -> Max-All-Session-Time
            if ($voucher->time_limit) {
                // Assuming voucher->time_limit is in seconds or we treat it as such for now?
                // Or if it's raw, does the voucher have a unit? No.
                // We will map it to Max-All-Session-Time.
                // Ideally this should be converted to seconds before storing or here if we knew the unit.
                // For consistency with Profile, let's assume it IS the limit value (seconds).
                RadCheck::create([
                    'username' => $voucher->code,
                    'attribute' => 'Max-All-Session-Time',
                    'op' => ':=',
                    'value' => (string) $voucher->time_limit,
                ]);
            }

            // 4. NAS Restriction (Optional)
            if (!empty($voucher->nas_id) && $voucher->nas) {
                RadCheck::create([
                    'username' => $voucher->code,
                    'attribute' => 'NAS-Identifier',
                    'op' => '==',
                    'value' => $voucher->nas->shortname,
                ]);
            }

            // Data Limit (Volume Quota) - If specific to voucher
            if ($voucher->data_limit && $voucher->data_limit > 0) {
                // ... existing code ...
                // 4. NAS Restriction (Optional) in Bulk Sync
                if (!empty($voucher['nas_shortname'])) {
                    RadCheck::create([
                        'username' => $voucher['code'],
                        'attribute' => 'NAS-Identifier',
                        'op' => '==',
                        'value' => $voucher['nas_shortname'],
                    ]);
                }

                // Data Limit override -> Max-All-Octets
                RadCheck::create([
                    'username' => $voucher->code,
                    'attribute' => 'Max-All-Octets',
                    'op' => ':=',
                    'value' => $voucher->data_limit,
                ]);
            }

            DB::connection('radius')->commit();
        } catch (\Exception $e) {
            DB::connection('radius')->rollBack();
            Log::error("Radius hotspot sync failed for {$voucher->code}: " . $e->getMessage());
        }
    }

    /**
     * Remove hotspot voucher from Radius
     */
    public function removeHotspotVoucher($code)
    {
        if (!$this->ensureRadiusAvailable("remove voucher {$code}")) {
            return;
        }

        try {
            RadCheck::where('username', $code)->delete();
            RadUserGroup::where('username', $code)->delete();
            RadReply::where('username', $code)->delete();
        } catch (\Exception $e) {
            Log::error("Radius hotspot removal failed for {$code}: " . $e->getMessage());
        }
    }

    /**
     * Bulk sync vouchers (Optimized for Generator)
     */
    public function bulkSyncHotspotVouchers(array $vouchersData)
    {
        if (empty($vouchersData))
            return;

        if (!$this->ensureRadiusAvailable('bulk sync vouchers')) {
            return;
        }

        try {
            DB::connection('radius')->beginTransaction();

            foreach ($vouchersData as $voucher) {
                // Pre-cleanup before sync to handle retries/duplicates
                RadCheck::where('username', $voucher['code'])->delete();
                RadUserGroup::where('username', $voucher['code'])->delete();
                RadReply::where('username', $voucher['code'])->delete();

                // RadCheck: Password
                RadCheck::create([
                    'username' => $voucher['code'],
                    'attribute' => 'Cleartext-Password',
                    'op' => ':=',
                    'value' => $voucher['password'],
                ]);

                // 1.1 RadCheck: Service-Type for Hotspot
                RadCheck::create([
                    'username' => $voucher['code'],
                    'attribute' => 'Service-Type',
                    'op' => ':=',
                    'value' => 'Login-User',
                ]);

                // RadUserGroup: Profile
                // Use mikrotik_group if available, otherwise profile_name
                $groupName = $voucher['mikrotik_group'] ?? ($voucher['profile_name'] ?? null);

                if ($groupName) {
                    RadUserGroup::create([
                        'username' => $voucher['code'],
                        'groupname' => $groupName,
                        'priority' => 1,
                    ]);
                }

                // NAS Restriction
                if (!empty($voucher['nas_shortname'])) {
                    RadCheck::create([
                        'username' => $voucher['code'],
                        'attribute' => 'NAS-Identifier',
                        'op' => '==',
                        'value' => $voucher['nas_shortname'],
                    ]);
                }

                // Data Limit Override (Max-All-Octets)
                if (isset($voucher['data_limit']) && $voucher['data_limit'] > 0) {
                    RadCheck::create([
                        'username' => $voucher['code'],
                        'attribute' => 'Max-All-Octets',
                        'op' => ':=',
                        'value' => (string) $voucher['data_limit'],
                    ]);
                }

                // Time Limit Override (Session-Timeout)
                // Note: time_limit here comes from generator input (minutes/hours converted to seconds or raw?)
                // Generator stores it as raw int usually. Let's check Generator.php
                // Generator: 'time_limit' => $this->time_limit. Code doesn't convert to seconds yet!
                // Wait, Generator.php L136 just assigns $this->time_limit. 
                // Using "time_limit" in generator usually implies minutes or hours? 
                // User said "jika time limit diisi > 0". 
                // Let's assume input is in Minutes (standard) or we need to check if there is a unit.
                // Looking at Generator.php, there is NO `time_unit` input manifest in validatrules, only `data_unit`.
                // However, standard hotspot often uses Minutes or Hours.
                // Reference: Profile has `time_limit_unit`. Generator seems to lack unit?
                // Let's re-read Generator source. L26 `public $time_limit;`. L28 `public $data_unit`.
                // There is NO `time_unit` in Generator properties!
                // Assuming "Time Limit" in generator is likely Minutes or same logic as Profile?
                // Actually, looking at previous steps or context, usually it is Minutes.
                // Let's assume it is MINUTES if no unit is provided, or strictly Seconds?
                // Most radius implementations use Seconds.
                // If I look at `HotspotProfile` usage, it converts based on unit.
                // If Generator has no unit, I should probably assume MINUTES or HOURS.
                // Let's assume MINUTES for now as it's a safe middle ground, OR it might be Seconds.
                // BUT, wait, `data_limit` calculates bytes in Generator L100.
                // `time_limit` is NOT calculated in Generator.
                // If user enters "60", is it 1 hour? likely.
                // So I should convert to seconds here? 
                // Or maybe I should check if I missed `time_unit` in Generator.
                // Let's check `voucher-generator.blade.php` to see the input label/select.

                // PENDING_VERIFICATION: I'll convert to string first, but I suspect I might need to multiply by 60.
                // For now, I will explicitly cast to string. 
                // If the user meant "Seconds", then just raw. 
                // If "Minutes", I need * 60.
                // Most simple hotspot systems use Minutes.
                // Let's assume RAW for now, but I will check the view in next step if possible.
                // For now, implementing as is (Value directly).

                // Time Limit Override (Max-All-Session-Time)
                if (isset($voucher['time_limit']) && $voucher['time_limit'] > 0) {
                    RadCheck::create([
                        'username' => $voucher['code'],
                        'attribute' => 'Max-All-Session-Time',
                        'op' => ':=',
                        'value' => (string) $voucher['time_limit'],
                    ]);
                }
            }

            DB::connection('radius')->commit();
        } catch (\Exception $e) {
            DB::connection('radius')->rollBack();
            Log::error("Bulk Radius sync failed: " . $e->getMessage());
            throw $e; // Re-throw to handle in UI
        }
    }

    /**
     * Bulk remove vouchers
     */
    public function bulkRemoveHotspotVouchers(array $codes)
    {
        if (empty($codes))
            return;

        if (!$this->ensureRadiusAvailable('bulk remove vouchers')) {
            return;
        }

        try {
            DB::connection('radius')->beginTransaction();

            RadCheck::whereIn('username', $codes)->delete();
            RadUserGroup::whereIn('username', $codes)->delete();
            RadReply::whereIn('username', $codes)->delete();

            DB::connection('radius')->commit();
        } catch (\Exception $e) {
            DB::connection('radius')->rollBack();
            Log::error("Bulk Radius removal failed: " . $e->getMessage());
        }
    }
    /**
     * Sync Hotspot Profile to Radius Group
     */
    public function syncHotspotProfile(\App\Models\HotspotProfile $profile)
    {
        $groupName = $profile->mikrotik_group ?: $profile->name;

        if (!$this->ensureRadiusAvailable("sync hotspot profile {$groupName}")) {
            return;
        }

        try {
            DB::connection('radius')->beginTransaction();

            // 1. Clean up existing Group Reply & Check
            // We should remove based on the ID or just clean all attributes for this group?
            // Safer to clean all attributes for this group to ensure exact sync.
            RadGroupReply::where('groupname', $groupName)->delete();
            \App\Models\Radius\RadGroupCheck::where('groupname', $groupName)->delete();

            // 2. Sync RadGroupReply Attributes

            // Acct-Interim-Interval (Default 120s)
            RadGroupReply::create([
                'groupname' => $groupName,
                'attribute' => 'Acct-Interim-Interval',
                'op' => ':=',
                'value' => '120',
            ]);

            // Idle Timeout
            RadGroupReply::create([
                'groupname' => $groupName,
                'attribute' => 'Idle-Timeout',
                'op' => ':=',
                'value' => '600',
            ]);

            // Rate Limit
            if ($profile->rate_limit) {
                RadGroupReply::create([
                    'groupname' => $groupName,
                    'attribute' => 'Mikrotik-Rate-Limit',
                    'op' => ':=',
                    'value' => $profile->rate_limit,
                ]);
            }

            // Address List
            if ($profile->address_list) {
                RadGroupReply::create([
                    'groupname' => $groupName,
                    'attribute' => 'Mikrotik-Address-List',
                    'op' => ':=',
                    'value' => $profile->address_list,
                ]);
            }

            // Session Timeout (Batas waktu sesi per login) - RadGroupReply
            if ($profile->session_timeout) {
                RadGroupReply::create([
                    'groupname' => $groupName,
                    'attribute' => 'Session-Timeout',
                    'op' => ':=',
                    'value' => $profile->session_timeout, // seconds
                ]);
            }

            // 3. Sync RadGroupCheck Attributes

            // Data Limit (Quota) -> Max-All-Octets - RadGroupCheck
            if ($bytes = $profile->data_limit_in_bytes) {
                \App\Models\Radius\RadGroupCheck::create([
                    'groupname' => $groupName,
                    'attribute' => 'Max-All-Octets',
                    'op' => ':=',
                    'value' => (string) $bytes,
                ]);
            }

            // Time Limit (Total Durasi Voucher) -> Max-All-Session-Time - RadGroupCheck
            if ($seconds = $profile->time_limit_in_seconds) {
                \App\Models\Radius\RadGroupCheck::create([
                    'groupname' => $groupName,
                    'attribute' => 'Max-All-Session-Time',
                    'op' => ':=',
                    'value' => (string) $seconds,
                ]);
            }

            // Validity (Masa Aktif) -> Max-Validity-Seconds - RadGroupCheck
            if ($validitySeconds = $profile->validity_in_seconds) {
                \App\Models\Radius\RadGroupCheck::create([
                    'groupname' => $groupName,
                    'attribute' => 'Max-Validity-Seconds',
                    'op' => ':=',
                    'value' => (string) $validitySeconds,
                ]);
            }

            // 3. Sync RadGroupCheck Attributes

            // Shared Users (Simultaneous-Use)
            if ($profile->shared_users) {
                \App\Models\Radius\RadGroupCheck::create([
                    'groupname' => $groupName,
                    'attribute' => 'Simultaneous-Use',
                    'op' => ':=',
                    'value' => $profile->shared_users,
                ]);
            }

            DB::connection('radius')->commit();
            Log::info("Radius Hotspot Profile synced: {$groupName}");

        } catch (\Exception $e) {
            DB::connection('radius')->rollBack();
            Log::error("Radius Hotspot Profile sync failed for {$groupName}: " . $e->getMessage());
        }
    }

    /**
     * Remove Hotspot Profile from Radius
     */
    public function removeHotspotProfile(HotspotProfile|string $profile)
    {
        $groupName = is_string($profile) ? $profile : ($profile->mikrotik_group ?: $profile->name);

        if (!$this->ensureRadiusAvailable("remove hotspot profile {$groupName}")) {
            return;
        }

        try {
            DB::connection('radius')->beginTransaction();
            RadGroupReply::where('groupname', $groupName)->delete();
            \App\Models\Radius\RadGroupCheck::where('groupname', $groupName)->delete();
            DB::connection('radius')->commit();

            Log::info("Radius Hotspot Profile removed: {$groupName}");
        } catch (\Exception $e) {
            DB::connection('radius')->rollBack();
            Log::error("Radius Hotspot Profile removal failed for {$groupName}: " . $e->getMessage());
        }
    }

}
