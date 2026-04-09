<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        
        $customerIds = DB::table('customers')->pluck('id')->toArray();
        $packageIds = DB::table('packages')->pluck('id')->toArray();
        $coveragePointIds = DB::table('coverage_points')->pluck('id')->toArray();
        $oltIds = DB::table('olts')->pluck('id')->toArray();
        $nasIds = DB::table('nas')->pluck('id')->toArray();
        
        $statuses = ['paid', 'unpaid', 'partial', 'cancelled'];
        
        foreach ($customerIds as $customerId) {
            // Each customer gets 1-2 subscriptions
            $numSubscriptions = rand(1, 2);
            
            for ($i = 0; $i < $numSubscriptions; $i++) {
                $periodStart = $faker->dateTimeBetween('-6 months', 'now');
                $periodEnd = (clone $periodStart)->modify('+1 month');
                
                DB::table('subscriptions')->insert([
                    'customer_id' => $customerId,
                    'package_id' => $faker->randomElement($packageIds),
                    'coverage_point_id' => $faker->randomElement($coveragePointIds),
                    'period_start' => $periodStart,
                    'period_end' => $periodEnd,
                    'installation_date' => $faker->dateTimeBetween('-6 months', 'now'),
                    'status' => $faker->randomElement($statuses),
                    'notes' => $faker->sentence,
                    'pppoe_username' => 'user_' . strtolower(str_replace(' ', '_', $faker->userName)),
                    'pppoe_password' => $faker->password,
                    'device_sn' => $faker->bothify('DEV-????-####'),
                    'olt_id' => $faker->randomElement($oltIds),
                    'olt_frame' => $faker->numberBetween(1, 20),
                    'olt_slot' => $faker->numberBetween(1, 16),
                    'olt_port' => $faker->numberBetween(1, 32),
                    'olt_onu_id' => $faker->numberBetween(1, 64),
                    'service_vlan' => $faker->numberBetween(100, 2000),
                    'last_online_at' => $faker->dateTimeBetween('-30 days', 'now'),
                    'provisioned_at' => $faker->dateTimeBetween('-6 months', 'now'),
                    'nas_id' => $faker->randomElement($nasIds),
                    'server_name' => $faker->domainName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}