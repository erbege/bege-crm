<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BwProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bwProfiles = [
            [
                'name' => '10Mbps',
                'rate_limit' => '10M/10M',
                'burst_limit' => '15M/15M',
                'burst_threshold' => '8M/8M',
                'burst_time' => '10s/10s',
                'priority' => 8,
                'olt_profile_name' => '10Mbps-profile',
                'description' => 'Paket bandwidth 10 Mbps',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '20Mbps',
                'rate_limit' => '20M/20M',
                'burst_limit' => '25M/25M',
                'burst_threshold' => '15M/15M',
                'burst_time' => '10s/10s',
                'priority' => 8,
                'olt_profile_name' => '20Mbps-profile',
                'description' => 'Paket bandwidth 20 Mbps',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '30Mbps',
                'rate_limit' => '30M/30M',
                'burst_limit' => '35M/35M',
                'burst_threshold' => '25M/25M',
                'burst_time' => '10s/10s',
                'priority' => 8,
                'olt_profile_name' => '30Mbps-profile',
                'description' => 'Paket bandwidth 30 Mbps',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '50Mbps',
                'rate_limit' => '50M/50M',
                'burst_limit' => '60M/60M',
                'burst_threshold' => '40M/40M',
                'burst_time' => '10s/10s',
                'priority' => 8,
                'olt_profile_name' => '50Mbps-profile',
                'description' => 'Paket bandwidth 50 Mbps',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '100Mbps',
                'rate_limit' => '100M/100M',
                'burst_limit' => '120M/120M',
                'burst_threshold' => '80M/80M',
                'burst_time' => '10s/10s',
                'priority' => 8,
                'olt_profile_name' => '100Mbps-profile',
                'description' => 'Paket bandwidth 100 Mbps',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('bw_profiles')->insert($bwProfiles);
    }
}