<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    /**
     * Seed comprehensive dummy data:
     * - 15 BwProfiles (15/25/50/100 Mbps variations)
     * - 23 Packages (varied profiles & pricing)
     * - 1200+ Customers (area_id 106-119)
     * - ~96% Subscriptions from customers
     * - Invoices linked to subscriptions
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Comprehensive Dummy Data Seeder...');

        // =====================================================
        // 1. BANDWIDTH PROFILES (15 records)
        // =====================================================
        $this->command->info('📡 Seeding 15 Bandwidth Profiles...');

        $bwProfiles = [
            // --- 15 Mbps variants ---
            [
                'name' => '15Mbps Standard',
                'rate_limit' => '15M/15M',
                'burst_limit' => '20M/20M',
                'burst_threshold' => '12M/12M',
                'burst_time' => '10s/10s',
                'priority' => 8,
                'mikrotik_group' => 'home',
                'radius_group' => 'home',
                'description' => 'Profil bandwidth 15 Mbps standar dengan burst',
                'is_active' => true,
            ],
            [
                'name' => '15Mbps Lite',
                'rate_limit' => '15M/15M',
                'burst_limit' => null,
                'burst_threshold' => null,
                'burst_time' => null,
                'priority' => 8,
                'mikrotik_group' => 'hemat',
                'radius_group' => 'hemat',
                'description' => 'Profil bandwidth 15 Mbps tanpa burst (hemat)',
                'is_active' => true,
            ],
            [
                'name' => '15Mbps Gaming',
                'rate_limit' => '15M/15M',
                'burst_limit' => '25M/25M',
                'burst_threshold' => '10M/10M',
                'burst_time' => '15s/15s',
                'priority' => 1,
                'mikrotik_group' => 'gamer',
                'radius_group' => 'gamer',
                'description' => 'Profil bandwidth 15 Mbps optimized untuk gaming (prioritas tinggi)',
                'is_active' => true,
            ],

            // --- 25 Mbps variants ---
            [
                'name' => '25Mbps Standard',
                'rate_limit' => '25M/25M',
                'burst_limit' => '30M/30M',
                'burst_threshold' => '20M/20M',
                'burst_time' => '10s/10s',
                'priority' => 8,
                'mikrotik_group' => 'home',
                'radius_group' => 'home',
                'description' => 'Profil bandwidth 25 Mbps standar dengan burst',
                'is_active' => true,
            ],
            [
                'name' => '25Mbps Lite',
                'rate_limit' => '25M/25M',
                'burst_limit' => null,
                'burst_threshold' => null,
                'burst_time' => null,
                'priority' => 8,
                'mikrotik_group' => 'hemat',
                'radius_group' => 'hemat',
                'description' => 'Profil bandwidth 25 Mbps tanpa burst (hemat)',
                'is_active' => true,
            ],
            [
                'name' => '25Mbps Gaming',
                'rate_limit' => '25M/25M',
                'burst_limit' => '40M/40M',
                'burst_threshold' => '18M/18M',
                'burst_time' => '15s/15s',
                'priority' => 1,
                'mikrotik_group' => 'gamer',
                'radius_group' => 'gamer',
                'description' => 'Profil bandwidth 25 Mbps optimized untuk gaming (prioritas tinggi)',
                'is_active' => true,
            ],
            [
                'name' => '25Mbps Premium',
                'rate_limit' => '25M/25M',
                'burst_limit' => '35M/35M',
                'burst_threshold' => '22M/22M',
                'burst_time' => '12s/12s',
                'priority' => 4,
                'mikrotik_group' => 'bisnis',
                'radius_group' => 'bisnis',
                'description' => 'Profil bandwidth 25 Mbps premium dengan prioritas menengah',
                'is_active' => true,
            ],

            // --- 50 Mbps variants ---
            [
                'name' => '50Mbps Standard',
                'rate_limit' => '50M/50M',
                'burst_limit' => '60M/60M',
                'burst_threshold' => '40M/40M',
                'burst_time' => '10s/10s',
                'priority' => 8,
                'mikrotik_group' => 'home',
                'radius_group' => 'home',
                'description' => 'Profil bandwidth 50 Mbps standar dengan burst',
                'is_active' => true,
            ],
            [
                'name' => '50Mbps Lite',
                'rate_limit' => '50M/50M',
                'burst_limit' => null,
                'burst_threshold' => null,
                'burst_time' => null,
                'priority' => 8,
                'mikrotik_group' => 'hemat',
                'radius_group' => 'hemat',
                'description' => 'Profil bandwidth 50 Mbps tanpa burst (hemat)',
                'is_active' => true,
            ],
            [
                'name' => '50Mbps Gaming',
                'rate_limit' => '50M/50M',
                'burst_limit' => '75M/75M',
                'burst_threshold' => '35M/35M',
                'burst_time' => '15s/15s',
                'priority' => 1,
                'mikrotik_group' => 'gamer',
                'radius_group' => 'gamer',
                'description' => 'Profil bandwidth 50 Mbps optimized untuk gaming (prioritas tinggi)',
                'is_active' => true,
            ],
            [
                'name' => '50Mbps Premium',
                'rate_limit' => '50M/50M',
                'burst_limit' => '65M/65M',
                'burst_threshold' => '45M/45M',
                'burst_time' => '12s/12s',
                'priority' => 4,
                'mikrotik_group' => 'bisnis',
                'radius_group' => 'bisnis',
                'description' => 'Profil bandwidth 50 Mbps premium dengan prioritas menengah',
                'is_active' => true,
            ],

            // --- 100 Mbps variants ---
            [
                'name' => '100Mbps Standard',
                'rate_limit' => '100M/100M',
                'burst_limit' => '120M/120M',
                'burst_threshold' => '80M/80M',
                'burst_time' => '10s/10s',
                'priority' => 8,
                'mikrotik_group' => 'home',
                'radius_group' => 'home',
                'description' => 'Profil bandwidth 100 Mbps standar dengan burst',
                'is_active' => true,
            ],
            [
                'name' => '100Mbps Lite',
                'rate_limit' => '100M/100M',
                'burst_limit' => null,
                'burst_threshold' => null,
                'burst_time' => null,
                'priority' => 8,
                'mikrotik_group' => 'hemat',
                'radius_group' => 'hemat',
                'description' => 'Profil bandwidth 100 Mbps tanpa burst (hemat)',
                'is_active' => true,
            ],
            [
                'name' => '100Mbps Gaming',
                'rate_limit' => '100M/100M',
                'burst_limit' => '150M/150M',
                'burst_threshold' => '70M/70M',
                'burst_time' => '15s/15s',
                'priority' => 1,
                'mikrotik_group' => 'corporate',
                'radius_group' => 'corporate',
                'description' => 'Profil bandwidth 100 Mbps optimized untuk gaming (prioritas tinggi)',
                'is_active' => true,
            ],
            [
                'name' => '100Mbps Premium',
                'rate_limit' => '100M/100M',
                'burst_limit' => '130M/130M',
                'burst_threshold' => '90M/90M',
                'burst_time' => '12s/12s',
                'priority' => 4,
                'mikrotik_group' => 'bisnis',
                'radius_group' => 'bisnis',
                'description' => 'Profil bandwidth 100 Mbps premium dengan prioritas menengah',
                'is_active' => true,
            ],
        ];

        $now = now();
        $bwProfileIds = [];
        foreach ($bwProfiles as $profile) {
            $profile['created_at'] = $now;
            $profile['updated_at'] = $now;
            $bwProfileIds[] = DB::table('bw_profiles')->insertGetId($profile);
        }

        $bwCount = count($bwProfileIds);
        $this->command->info("   ✅ {$bwCount} BwProfiles created (IDs: {$bwProfileIds[0]}-{$bwProfileIds[$bwCount - 1]})");

        // =====================================================
        // 2. PACKAGES (23 records)
        // =====================================================
        $this->command->info('📦 Seeding 23 Internet Packages...');

        // Map profile names for easy reference (index 0-14 matches bwProfileIds)
        // 0: 15Std, 1: 15Lite, 2: 15Game
        // 3: 25Std, 4: 25Lite, 5: 25Game, 6: 25Prem
        // 7: 50Std, 8: 50Lite, 9: 50Game, 10: 50Prem
        // 11: 100Std, 12: 100Lite, 13: 100Game, 14: 100Prem

        $packages = [
            // === Home Packages (4) ===
            [
                'bw_profile_id' => $bwProfileIds[0], // 15Mbps Standard
                'name' => 'Home 15',
                'code' => 'HOME-15',
                'price' => 150000,
                'installation_fee' => 100000,
                'description' => 'Paket rumahan 15 Mbps — cocok untuk browsing & streaming ringan',
                'is_active' => true,
                'service_type' => 'PPP',
            ],
            [
                'bw_profile_id' => $bwProfileIds[3], // 25Mbps Standard
                'name' => 'Home 25',
                'code' => 'HOME-25',
                'price' => 200000,
                'installation_fee' => 100000,
                'description' => 'Paket rumahan 25 Mbps — ideal untuk keluarga kecil',
                'is_active' => true,
                'service_type' => 'PPP',
            ],
            [
                'bw_profile_id' => $bwProfileIds[7], // 50Mbps Standard
                'name' => 'Home 50',
                'code' => 'HOME-50',
                'price' => 350000,
                'installation_fee' => 100000,
                'description' => 'Paket rumahan 50 Mbps — streaming 4K tanpa buffering',
                'is_active' => true,
                'service_type' => 'PPP',
            ],
            [
                'bw_profile_id' => $bwProfileIds[11], // 100Mbps Standard
                'name' => 'Home 100',
                'code' => 'HOME-100',
                'price' => 500000,
                'installation_fee' => 150000,
                'description' => 'Paket rumahan 100 Mbps — keluarga besar, multi-device',
                'is_active' => true,
                'service_type' => 'PPP',
            ],

            // === Gamer Packages (4) ===
            [
                'bw_profile_id' => $bwProfileIds[2], // 15Mbps Gaming
                'name' => 'Gamer 15',
                'code' => 'GMR-15',
                'price' => 200000,
                'installation_fee' => 100000,
                'description' => 'Paket gamer 15 Mbps — low latency, prioritas tinggi',
                'is_active' => true,
                'service_type' => 'PPP',
            ],
            [
                'bw_profile_id' => $bwProfileIds[5], // 25Mbps Gaming
                'name' => 'Gamer 25',
                'code' => 'GMR-25',
                'price' => 300000,
                'installation_fee' => 100000,
                'description' => 'Paket gamer 25 Mbps — competitive gaming ready',
                'is_active' => true,
                'service_type' => 'PPP',
            ],
            [
                'bw_profile_id' => $bwProfileIds[9], // 50Mbps Gaming
                'name' => 'Gamer 50',
                'code' => 'GMR-50',
                'price' => 450000,
                'installation_fee' => 100000,
                'description' => 'Paket gamer 50 Mbps — streaming + gaming simultan',
                'is_active' => true,
                'service_type' => 'PPP',
            ],
            [
                'bw_profile_id' => $bwProfileIds[13], // 100Mbps Gaming
                'name' => 'Gamer 100',
                'code' => 'GMR-100',
                'price' => 650000,
                'installation_fee' => 150000,
                'description' => 'Paket gamer 100 Mbps — pro streamer & esports',
                'is_active' => true,
                'service_type' => 'PPP',
            ],

            // === Office Packages (4) ===
            [
                'bw_profile_id' => $bwProfileIds[0], // 15Mbps Standard
                'name' => 'Office 15',
                'code' => 'OFC-15',
                'price' => 175000,
                'installation_fee' => 100000,
                'description' => 'Paket kantor kecil 15 Mbps — email & video call',
                'is_active' => true,
                'service_type' => 'PPP',
            ],
            [
                'bw_profile_id' => $bwProfileIds[6], // 25Mbps Premium
                'name' => 'Office 25',
                'code' => 'OFC-25',
                'price' => 275000,
                'installation_fee' => 100000,
                'description' => 'Paket kantor 25 Mbps — multi-user & cloud apps',
                'is_active' => true,
                'service_type' => 'PPP',
            ],
            [
                'bw_profile_id' => $bwProfileIds[10], // 50Mbps Premium
                'name' => 'Office 50',
                'code' => 'OFC-50',
                'price' => 450000,
                'installation_fee' => 150000,
                'description' => 'Paket kantor 50 Mbps — SOHO & startup',
                'is_active' => true,
                'service_type' => 'PPP',
            ],
            [
                'bw_profile_id' => $bwProfileIds[14], // 100Mbps Premium
                'name' => 'Office 100',
                'code' => 'OFC-100',
                'price' => 750000,
                'installation_fee' => 200000,
                'description' => 'Paket kantor 100 Mbps — medium enterprise',
                'is_active' => true,
                'service_type' => 'PPP',
            ],

            // === Lite/Hemat Packages (4) ===
            [
                'bw_profile_id' => $bwProfileIds[1], // 15Mbps Lite
                'name' => 'Hemat 15',
                'code' => 'HMT-15',
                'price' => 100000,
                'installation_fee' => 75000,
                'description' => 'Paket hemat 15 Mbps — tanpa burst, harga terjangkau',
                'is_active' => true,
                'service_type' => 'PPP',
            ],
            [
                'bw_profile_id' => $bwProfileIds[4], // 25Mbps Lite
                'name' => 'Hemat 25',
                'code' => 'HMT-25',
                'price' => 150000,
                'installation_fee' => 75000,
                'description' => 'Paket hemat 25 Mbps — tanpa burst, harga terjangkau',
                'is_active' => true,
                'service_type' => 'PPP',
            ],
            [
                'bw_profile_id' => $bwProfileIds[8], // 50Mbps Lite
                'name' => 'Hemat 50',
                'code' => 'HMT-50',
                'price' => 250000,
                'installation_fee' => 100000,
                'description' => 'Paket hemat 50 Mbps — tanpa burst, harga terjangkau',
                'is_active' => true,
                'service_type' => 'PPP',
            ],
            [
                'bw_profile_id' => $bwProfileIds[12], // 100Mbps Lite
                'name' => 'Hemat 100',
                'code' => 'HMT-100',
                'price' => 400000,
                'installation_fee' => 100000,
                'description' => 'Paket hemat 100 Mbps — tanpa burst, harga terjangkau',
                'is_active' => true,
                'service_type' => 'PPP',
            ],

            // === Bisnis Packages (3) ===
            [
                'bw_profile_id' => $bwProfileIds[6], // 25Mbps Premium
                'name' => 'Bisnis 25',
                'code' => 'BIZ-25',
                'price' => 350000,
                'installation_fee' => 200000,
                'description' => 'Paket bisnis 25 Mbps — SLA guaranteed, prioritas menengah',
                'is_active' => true,
                'service_type' => 'PPP',
            ],
            [
                'bw_profile_id' => $bwProfileIds[10], // 50Mbps Premium
                'name' => 'Bisnis 50',
                'code' => 'BIZ-50',
                'price' => 600000,
                'installation_fee' => 250000,
                'description' => 'Paket bisnis 50 Mbps — dedicated support, SLA 99%',
                'is_active' => true,
                'service_type' => 'PPP',
            ],
            [
                'bw_profile_id' => $bwProfileIds[14], // 100Mbps Premium
                'name' => 'Bisnis 100',
                'code' => 'BIZ-100',
                'price' => 950000,
                'installation_fee' => 300000,
                'description' => 'Paket bisnis 100 Mbps — enterprise grade, SLA 99.5%',
                'is_active' => true,
                'service_type' => 'PPP',
            ],

            // === Corporate Packages (2) ===
            [
                'bw_profile_id' => $bwProfileIds[13], // 100Mbps Gaming (high burst)
                'name' => 'Corporate 100 Plus',
                'code' => 'CORP-100P',
                'price' => 1200000,
                'installation_fee' => 500000,
                'description' => 'Paket korporat 100 Mbps — burst tinggi, prioritas maksimal',
                'is_active' => true,
                'service_type' => 'PPP',
            ],
            [
                'bw_profile_id' => $bwProfileIds[14], // 100Mbps Premium
                'name' => 'Corporate 100',
                'code' => 'CORP-100',
                'price' => 1500000,
                'installation_fee' => 500000,
                'description' => 'Paket korporat 100 Mbps — dedicated, SLA 99.9%, IP public',
                'is_active' => true,
                'service_type' => 'PPP',
            ],

            // === Promo Packages (2) ===
            [
                'bw_profile_id' => $bwProfileIds[0], // 15Mbps Standard
                'name' => 'Promo Spesial 15',
                'code' => 'PROMO-15',
                'price' => 99000,
                'installation_fee' => 0,
                'description' => 'Paket promo 15 Mbps — gratis instalasi, terbatas!',
                'is_active' => true,
                'service_type' => 'PPP',
            ],
            [
                'bw_profile_id' => $bwProfileIds[3], // 25Mbps Standard
                'name' => 'Promo Spesial 25',
                'code' => 'PROMO-25',
                'price' => 149000,
                'installation_fee' => 0,
                'description' => 'Paket promo 25 Mbps — gratis instalasi, terbatas!',
                'is_active' => true,
                'service_type' => 'PPP',
            ],
        ];

        $packageIds = [];
        $packagePrices = [];
        foreach ($packages as $pkg) {
            $pkg['created_at'] = $now;
            $pkg['updated_at'] = $now;
            $id = DB::table('packages')->insertGetId($pkg);
            $packageIds[] = $id;
            $packagePrices[$id] = $pkg['price'];
        }

        $this->command->info("   ✅ " . count($packageIds) . " Packages created (IDs: {$packageIds[0]}-{$packageIds[count($packageIds) - 1]})");

        // =====================================================
        // 3. CUSTOMERS (1200+ records)
        // =====================================================
        $this->command->info('👥 Seeding 1200+ Customers...');

        // Fetch areas with id between 106 and 119
        $areas = DB::table('areas')
            ->whereBetween('id', [106, 119])
            ->get()
            ->toArray();

        if (empty($areas)) {
            $this->command->warn('   ⚠️ No areas found with id 106-119. Falling back to all available areas.');
            $areas = DB::table('areas')->get()->toArray();
        }

        if (empty($areas)) {
            $this->command->error('   ❌ No areas found in the database. Please run AreaSeeder first.');
            return;
        }

        $this->command->info('   📍 Using ' . count($areas) . ' areas (IDs: ' . collect($areas)->pluck('id')->implode(', ') . ')');

        // Area coordinate map — realistic lat/lng per area name
        $areaCoordinates = $this->getAreaCoordinates($areas);

        $faker = Faker::create('id_ID');
        $totalCustomers = 1250;
        $customerBatch = [];
        $customerDbIds = [];

        for ($i = 0; $i < $totalCustomers; $i++) {
            $sequence = str_pad($i + 1, 4, '0', STR_PAD_LEFT);
            $registeredAt = $faker->dateTimeBetween('-2 years', '-1 month');
            $yearMonth = Carbon::parse($registeredAt)->format('Ym');
            $customerId = sprintf('CUST-%s-%s', $yearMonth, $sequence);

            // Pick a random area
            $area = $areas[array_rand($areas)];
            $areaId = $area->id;

            // Get coordinates for area (with small random offset)
            $coords = $areaCoordinates[$areaId] ?? ['lat' => -6.200000, 'lng' => 106.816666];
            $latitude = $coords['lat'] + $faker->randomFloat(6, -0.02, 0.02);
            $longitude = $coords['lng'] + $faker->randomFloat(6, -0.02, 0.02);

            $customerBatch[] = [
                'customer_id' => $customerId,
                'name' => $faker->name,
                'identity_number' => $faker->numerify('################'),
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'area_id' => $areaId,
                'registered_at' => $registeredAt,
                'latitude' => round($latitude, 8),
                'longitude' => round($longitude, 8),
                'notes' => null,
                'password' => bcrypt('password'),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            // Insert in batches of 100
            if (count($customerBatch) >= 100) {
                DB::table('customers')->insert($customerBatch);
                $customerBatch = [];
                $this->command->info("   ... {$this->formatNum($i + 1)} / {$totalCustomers} customers");
            }
        }

        // Insert remaining records
        if (!empty($customerBatch)) {
            DB::table('customers')->insert($customerBatch);
        }

        // Get all inserted customer IDs
        $customerDbIds = DB::table('customers')
            ->orderBy('id', 'desc')
            ->limit($totalCustomers)
            ->pluck('id')
            ->toArray();

        // Reverse to maintain order
        $customerDbIds = array_reverse($customerDbIds);

        $this->command->info("   ✅ {$totalCustomers} Customers created");

        // =====================================================
        // 4. SUBSCRIPTIONS (~96% of customers)
        // =====================================================
        $this->command->info('📋 Seeding Subscriptions (96% of customers)...');

        $subscriptionCount = (int) ceil($totalCustomers * 0.96); // ~1200
        $subscribedCustomers = array_slice($customerDbIds, 0, $subscriptionCount);

        // Status distribution: 70% active, 10% suspended, 5% cancelled, 15% pending
        $statusWeights = array_merge(
            array_fill(0, 70, 'active'),
            array_fill(0, 10, 'suspended'),
            array_fill(0, 5, 'cancelled'),
            array_fill(0, 15, 'pending')
        );

        $subscriptionBatch = [];
        $subscriptionMeta = []; // track customer_id => [package_id, sub_db_id] for invoice generation
        $subIndex = 0;
        $invoiceSeqCounter = 0;

        foreach ($subscribedCustomers as $custDbId) {
            $packageId = $faker->randomElement($packageIds);
            $status = $statusWeights[array_rand($statusWeights)];

            // Period: random month within the last 6 months to current
            $periodStart = Carbon::now()->subMonths(rand(0, 5))->startOfMonth();
            $periodEnd = (clone $periodStart)->endOfMonth();

            $installDate = (clone $periodStart)->subMonths(rand(1, 18));

            $pppoeUser = 'pppoe_' . strtolower($faker->unique()->userName);
            $pppoePass = $faker->bothify('??####??');

            $subscriptionBatch[] = [
                'customer_id' => $custDbId,
                'package_id' => $packageId,
                'coverage_point_id' => null,
                'period_start' => $periodStart->format('Y-m-d'),
                'period_end' => $periodEnd->format('Y-m-d'),
                'installation_date' => $installDate->format('Y-m-d'),
                'status' => $status,
                'notes' => null,
                'pppoe_username' => $pppoeUser,
                'pppoe_password' => $pppoePass,
                'device_sn' => strtoupper($faker->bothify('ZTEG-????????')),
                'olt_id' => null,
                'olt_frame' => 0,
                'olt_slot' => rand(1, 8),
                'olt_port' => rand(1, 16),
                'olt_onu_id' => rand(1, 128),
                'service_vlan' => rand(100, 4000),
                'last_online_at' => $status === 'active' ? $faker->dateTimeBetween('-7 days', 'now') : null,
                'provisioned_at' => in_array($status, ['active', 'suspended']) ? $installDate->format('Y-m-d H:i:s') : null,
                'nas_id' => null,
                'server_name' => null,
                'service_type' => 'PPP',
                'mac_address' => strtoupper($faker->bothify('##:##:##:##:##:##')),
                'ip_address' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $subscriptionMeta[] = [
                'customer_id' => $custDbId,
                'package_id' => $packageId,
                'status' => $status,
                'period_start' => $periodStart->format('Y-m-d'),
                'period_end' => $periodEnd->format('Y-m-d'),
            ];

            $subIndex++;

            // Insert in batches of 100
            if (count($subscriptionBatch) >= 100) {
                DB::table('subscriptions')->insert($subscriptionBatch);
                $subscriptionBatch = [];
                $this->command->info("   ... {$this->formatNum($subIndex)} / {$subscriptionCount} subscriptions");
            }
        }

        // Insert remaining
        if (!empty($subscriptionBatch)) {
            DB::table('subscriptions')->insert($subscriptionBatch);
        }

        $this->command->info("   ✅ {$subscriptionCount} Subscriptions created");

        // =====================================================
        // 5. INVOICES (1 per subscription)
        // =====================================================
        $this->command->info('🧾 Seeding Invoices (1 per subscription)...');

        // Get all subscription IDs in order
        $subscriptionDbRecords = DB::table('subscriptions')
            ->orderBy('id', 'desc')
            ->limit($subscriptionCount)
            ->select('id', 'customer_id', 'package_id', 'status', 'period_start', 'period_end')
            ->get()
            ->reverse()
            ->values();

        $invoiceBatch = [];
        $invIndex = 0;

        foreach ($subscriptionDbRecords as $sub) {
            $invIndex++;
            $price = $packagePrices[$sub->package_id] ?? 200000;

            $subtotal = $price;
            $tax = round($subtotal * 0.11, 2);
            $installationFee = 0;
            $discount = 0;

            // 10% chance of discount
            if (rand(1, 100) <= 10) {
                $discount = $faker->randomElement([10000, 25000, 50000]);
            }

            $total = $subtotal + $tax + $installationFee - $discount;

            // Invoice status based on subscription status
            $invoiceStatus = match ($sub->status) {
                'active' => 'paid',
                'suspended' => 'unpaid',
                'pending' => 'unpaid',
                'cancelled' => 'cancelled',
                default => 'unpaid',
            };

            $periodStart = Carbon::parse($sub->period_start);
            $issueDate = (clone $periodStart)->subDays(rand(0, 5));
            $dueDate = (clone $periodStart)->addDays(14);

            $paidAt = null;
            $paymentMethod = null;
            if ($invoiceStatus === 'paid') {
                $paidAt = $faker->dateTimeBetween($issueDate->format('Y-m-d'), $dueDate->format('Y-m-d'));
                $paymentMethod = $faker->randomElement(['transfer', 'cash', 'qris', 'e-wallet', 'virtual_account']);
            }

            $yearMonth = $issueDate->format('Ym');
            $invoiceSeqCounter++;
            $invoiceNumber = sprintf('INV-%s-%04d', $yearMonth, $invoiceSeqCounter);

            $invoiceBatch[] = [
                'invoice_number' => $invoiceNumber,
                'subscription_id' => $sub->id,
                'customer_id' => $sub->customer_id,
                'issue_date' => $issueDate->format('Y-m-d'),
                'due_date' => $dueDate->format('Y-m-d'),
                'subtotal' => $subtotal,
                'tax' => $tax,
                'installation_fee' => $installationFee,
                'discount' => $discount,
                'total' => $total,
                'status' => $invoiceStatus,
                'paid_at' => $paidAt,
                'payment_method' => $paymentMethod,
                'notes' => null,
                'sent_at' => $faker->dateTimeBetween($issueDate->format('Y-m-d'), 'now'),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            // Insert in batches of 100
            if (count($invoiceBatch) >= 100) {
                DB::table('invoices')->insert($invoiceBatch);
                $invoiceBatch = [];
                $this->command->info("   ... {$this->formatNum($invIndex)} / {$subscriptionCount} invoices");
            }
        }

        // Insert remaining
        if (!empty($invoiceBatch)) {
            DB::table('invoices')->insert($invoiceBatch);
        }

        $this->command->info("   ✅ {$invIndex} Invoices created");

        // =====================================================
        // SUMMARY
        // =====================================================
        $this->command->newLine();
        $this->command->info('🎉 Comprehensive Dummy Data Seeder completed!');
        $this->command->table(
            ['Entity', 'Count'],
            [
                ['BwProfiles', count($bwProfileIds)],
                ['Packages', count($packageIds)],
                ['Customers', $totalCustomers],
                ['Subscriptions', $subscriptionCount],
                ['Invoices', $invIndex],
            ]
        );
    }

    /**
     * Get realistic coordinates based on area name keywords.
     */
    private function getAreaCoordinates(array $areas): array
    {
        // Known Indonesian city/district coordinates
        $knownCoordinates = [
            'jakarta pusat' => ['lat' => -6.186486, 'lng' => 106.834091],
            'jakarta utara' => ['lat' => -6.138414, 'lng' => 106.863953],
            'jakarta barat' => ['lat' => -6.168329, 'lng' => 106.758835],
            'jakarta selatan' => ['lat' => -6.261493, 'lng' => 106.810600],
            'jakarta timur' => ['lat' => -6.225014, 'lng' => 106.900447],
            'depok' => ['lat' => -6.402484, 'lng' => 106.794243],
            'bekasi' => ['lat' => -6.241586, 'lng' => 106.992416],
            'tangerang' => ['lat' => -6.178306, 'lng' => 106.631889],
            'bogor' => ['lat' => -6.595038, 'lng' => 106.816635],
            'bandung' => ['lat' => -6.917464, 'lng' => 107.619123],
            'surabaya' => ['lat' => -7.257472, 'lng' => 112.752090],
            'semarang' => ['lat' => -6.966667, 'lng' => 110.419444],
            'yogyakarta' => ['lat' => -7.795580, 'lng' => 110.369490],
            'malang' => ['lat' => -7.977500, 'lng' => 112.634167],
            'medan' => ['lat' => 3.595196, 'lng' => 98.672226],
            'makassar' => ['lat' => -5.147665, 'lng' => 119.432732],
            'palembang' => ['lat' => -2.976074, 'lng' => 104.775429],
            'cirebon' => ['lat' => -6.706000, 'lng' => 108.557000],
            'serang' => ['lat' => -6.120000, 'lng' => 106.150000],
            'karawang' => ['lat' => -6.306719, 'lng' => 107.295620],
            'cilegon' => ['lat' => -6.017000, 'lng' => 106.050000],
            'tasikmalaya' => ['lat' => -7.327000, 'lng' => 108.220000],
            'sukabumi' => ['lat' => -6.927000, 'lng' => 106.930000],
            'garut' => ['lat' => -7.227000, 'lng' => 107.909000],
            'subang' => ['lat' => -6.571000, 'lng' => 107.755000],
            'purwakarta' => ['lat' => -6.556000, 'lng' => 107.435000],
            'cianjur' => ['lat' => -6.818000, 'lng' => 107.139000],
            'indramayu' => ['lat' => -6.327000, 'lng' => 108.322000],
            'majalengka' => ['lat' => -6.835000, 'lng' => 108.227000],
            'kuningan' => ['lat' => -6.975000, 'lng' => 108.483000],
            'tangerang selatan' => ['lat' => -6.284700, 'lng' => 106.710300],
            'cibinong' => ['lat' => -6.481000, 'lng' => 106.852000],
            'cibubur' => ['lat' => -6.370200, 'lng' => 106.879400],
            'cikarang' => ['lat' => -6.303600, 'lng' => 107.151200],
            'serpong' => ['lat' => -6.319000, 'lng' => 106.668000],
            'bintaro' => ['lat' => -6.272000, 'lng' => 106.739000],
            'pondok gede' => ['lat' => -6.282000, 'lng' => 106.917000],
            'cimahi' => ['lat' => -6.884000, 'lng' => 107.541000],
            'lembang' => ['lat' => -6.811000, 'lng' => 107.617000],
            'ciawi' => ['lat' => -6.699000, 'lng' => 106.837000],
            'sentul' => ['lat' => -6.571000, 'lng' => 106.857000],
            'pamulang' => ['lat' => -6.338000, 'lng' => 106.729000],
            'ciputat' => ['lat' => -6.318000, 'lng' => 106.727000],
            'cinere' => ['lat' => -6.335000, 'lng' => 106.784000],
            'sawangan' => ['lat' => -6.396000, 'lng' => 106.754000],
        ];

        $map = [];
        foreach ($areas as $area) {
            $areaName = strtolower(trim($area->name));
            $matched = false;

            // Try exact match first
            if (isset($knownCoordinates[$areaName])) {
                $map[$area->id] = $knownCoordinates[$areaName];
                $matched = true;
            }

            // Try partial match
            if (!$matched) {
                foreach ($knownCoordinates as $key => $coords) {
                    if (str_contains($areaName, $key) || str_contains($key, $areaName)) {
                        $map[$area->id] = $coords;
                        $matched = true;
                        break;
                    }
                }
            }

            // Fallback: use area's own coordinates or default Jakarta
            if (!$matched) {
                if (!empty($area->latitude) && !empty($area->longitude)) {
                    $map[$area->id] = ['lat' => (float) $area->latitude, 'lng' => (float) $area->longitude];
                } else {
                    // Default: roughly Jakarta area
                    $map[$area->id] = ['lat' => -6.200000, 'lng' => 106.816666];
                }
            }
        }

        return $map;
    }

    /**
     * Format number with dots separator.
     */
    private function formatNum(int $num): string
    {
        return number_format($num, 0, ',', '.');
    }
}
