<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        
        $areas = DB::table('areas')->pluck('id')->toArray();
        $customerIds = [];
        
        for ($i = 0; $i < 50; $i++) {
            $prefix = 'CUST';
            $yearMonth = now()->format('Ym');
            
            // Generate unique customer ID
            $sequence = str_pad($i + 1, 4, '0', STR_PAD_LEFT);
            $customerId = sprintf('%s-%s-%s', $prefix, $yearMonth, $sequence);
            
            $customerIds[] = $customerId;
            
            DB::table('customers')->insert([
                'customer_id' => $customerId,
                'name' => $faker->name,
                'identity_number' => $faker->nik ?? $faker->numerify('##########'),
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'area_id' => $faker->randomElement($areas),
                'registered_at' => $faker->dateTimeBetween('-1 year', 'now'),
                'latitude' => $faker->latitude(-6.2, -6.1),
                'longitude' => $faker->longitude(106.7, 106.9),
                'notes' => $faker->sentence,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}