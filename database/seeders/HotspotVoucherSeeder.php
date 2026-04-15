<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class HotspotVoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        
        $nasIds = DB::table('nas')->pluck('id')->toArray();
        $profiles = [
            ['name' => 'Hotspot 1 Jam', 'price' => 5000, 'validity_value' => 1, 'validity_unit' => 'hours'],
            ['name' => 'Hotspot 3 Jam', 'price' => 10000, 'validity_value' => 3, 'validity_unit' => 'hours'],
            ['name' => 'Hotspot 6 Jam', 'price' => 15000, 'validity_value' => 6, 'validity_unit' => 'hours'],
            ['name' => 'Hotspot 12 Jam', 'price' => 25000, 'validity_value' => 12, 'validity_unit' => 'hours'],
            ['name' => 'Hotspot 24 Jam', 'price' => 40000, 'validity_value' => 1, 'validity_unit' => 'days'],
        ];
        
        // Create hotspot profiles first
        foreach ($profiles as $index => $profile) {
            DB::table('hotspot_profiles')->insert([
                'name' => $profile['name'],
                'price' => $profile['price'],
                'validity_value' => $profile['validity_value'],
                'validity_unit' => $profile['validity_unit'],
                'rate_limit' => '1M/1M',
                'shared_users' => 1,
                'session_timeout' => null,
                'keepalive_timeout' => null,
                'description' => 'Voucher ' . $profile['name'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Create voucher templates
        $templateNames = ['UMUM', 'PELANGGAN', 'GUEST'];
        foreach ($templateNames as $templateName) {
            DB::table('hotspot_voucher_templates')->insert([
                'name' => $templateName,
                'content' => '<p>Voucher ' . $templateName . '</p>',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Create hotspot vouchers
        $hotspotProfileIds = DB::table('hotspot_profiles')->pluck('id')->toArray();
        
        for ($i = 0; $i < 100; $i++) {
            $profile = $profiles[array_rand($profiles)];
            
            // Calculate expiration date based on validity
            $validityValue = $profile['validity_value'];
            $validityUnit = $profile['validity_unit'];
            
            switch ($validityUnit) {
                case 'hours':
                    $expiredAt = now()->addHours($validityValue);
                    break;
                case 'days':
                    $expiredAt = now()->addDays($validityValue);
                    break;
                case 'weeks':
                    $expiredAt = now()->addWeeks($validityValue);
                    break;
                case 'months':
                    $expiredAt = now()->addMonths($validityValue);
                    break;
                default:
                    $expiredAt = now()->addDays(1); // default to 1 day
            }
            
            DB::table('hotspot_vouchers')->insert([
                'code' => $faker->bothify('HS-????-####'),
                'hotspot_profile_id' => $faker->randomElement($hotspotProfileIds),
                'password' => $faker->password,
                'status' => $faker->randomElement(['active', 'used', 'expired']),
                'used_at' => $faker->boolean(40) ? $faker->dateTimeBetween('-3 months', 'now') : null,
                'expired_at' => $expiredAt,
                'nas_id' => $faker->randomElement($nasIds),
                'server' => 'all',
                'user_mode' => 'username_password',
                'created_by' => 1, // Use the first user as creator
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}