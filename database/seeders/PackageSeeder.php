<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'bw_profile_id' => 1, // 10Mbps
                'name' => 'Basic 10Mbps',
                'code' => 'BSC-10',
                'price' => 250000,
                'installation_fee' => 100000,
                'description' => 'Paket internet basic dengan kecepatan 10 Mbps',
                'is_active' => true,
                'service_type' => 'PPP',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bw_profile_id' => 2, // 20Mbps
                'name' => 'Standard 20Mbps',
                'code' => 'STD-20',
                'price' => 350000,
                'installation_fee' => 100000,
                'description' => 'Paket internet standar dengan kecepatan 20 Mbps',
                'is_active' => true,
                'service_type' => 'PPP',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bw_profile_id' => 3, // 30Mbps
                'name' => 'Premium 30Mbps',
                'code' => 'PRM-30',
                'price' => 450000,
                'installation_fee' => 100000,
                'description' => 'Paket internet premium dengan kecepatan 30 Mbps',
                'is_active' => true,
                'service_type' => 'PPP',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bw_profile_id' => 4, // 50Mbps
                'name' => 'Business 50Mbps',
                'code' => 'BSN-50',
                'price' => 650000,
                'installation_fee' => 100000,
                'description' => 'Paket internet bisnis dengan kecepatan 50 Mbps',
                'is_active' => true,
                'service_type' => 'PPP',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bw_profile_id' => 5, // 100Mbps
                'name' => 'Enterprise 100Mbps',
                'code' => 'ENT-100',
                'price' => 950000,
                'installation_fee' => 100000,
                'description' => 'Paket internet enterprise dengan kecepatan 100 Mbps',
                'is_active' => true,
                'service_type' => 'PPP',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('packages')->insert($packages);
    }
}