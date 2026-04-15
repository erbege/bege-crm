<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class DummyCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Get all areas with their coordinates
        $areas = DB::table('areas')->get()->toArray();
        
        if (empty($areas)) {
            $this->command->error('No areas found in the database. Please run AreaSeeder first.');
            return;
        }

        // Array of Indonesian names for realistic dummy data (58 names)
        $indonesianNames = [
            'Ahmad Fauzi', 'Siti Aminah', 'Budi Santoso', 'Lina Marlina', 
            'Agus Salim', 'Dewi Kartika', 'Rizki Ramadhan', 'Maya Sari',
            'Hendra Wijaya', 'Fitriani', 'Joko Prasetyo', 'Ani Lestari',
            'Muhammad Arif', 'Sri Wahyuni', 'Dian Permata', 'Andi Saputra',
            'Ratna Sari', 'Fajar Nugraha', 'Yuni Astuti', 'Eko Prasetya',
            'Linda Kusuma', 'Bayu Setiawan', 'Intan Safitri', 'Rudy Hartono',
            'Nurul Hidayah', 'Gilang Ramadhan', 'Putri Maharani', 'Ade Kurniawan',
            'Mila Anggraini', 'Rian Saputra', 'Dina Puspita', 'Tommy Pratama',
            'Wulan Sari', 'Iwan Gunawan', 'Siska Amelia', 'Rendi Pratama',
            'Citra Kirana', 'Adi Lesmana', 'Rahmat Hidayat', 'Novi Susanti',
            'Dedi Kusnadi', 'Lely Oktaviani', 'Arif Budiman', 'Suci Ramadhani',
            'Taufik Hidayat', 'Diana Puspita', 'Roni Firmansyah', 'Silvia Anggraini',
            'Yusuf Maulana', 'Rina Kurniawati', 'Feri Prasetyo', 'Dwi Lestari',
            'Andika Pratama', 'Mega Safitri', 'Ganang Prasetyo', 'Ria Puspitasari',
            'Aditya Prima', 'Nanda Perdana'
        ];

        $customerIds = [];

        for ($i = 0; $i < 58; $i++) {
            $prefix = 'CUST';
            $yearMonth = now()->format('Ym');

            // Generate unique customer ID
            $sequence = str_pad($i + 1, 4, '0', STR_PAD_LEFT);
            $customerId = sprintf('%s-%s-%s', $prefix, $yearMonth, $sequence);

            $customerIds[] = $customerId;

            // Select a random area
            $selectedArea = $areas[array_rand($areas)];
            
            // Use the area's coordinates or generate nearby coordinates
            $baseLatitude = $selectedArea->latitude ? floatval($selectedArea->latitude) : -6.20000000;
            $baseLongitude = $selectedArea->longitude ? floatval($selectedArea->longitude) : 106.80000000;
            
            // Generate coordinates slightly around the area center
            $latitude = $baseLatitude + $faker->randomFloat(6, -0.01, 0.01);
            $longitude = $baseLongitude + $faker->randomFloat(6, -0.01, 0.01);

            DB::table('customers')->insert([
                'customer_id' => $customerId,
                'name' => $indonesianNames[$i],
                'identity_number' => $faker->nik ?? $faker->numerify('################'),
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'area_id' => $selectedArea->id,
                'registered_at' => $faker->dateTimeBetween('-1 year', 'now'),
                'latitude' => $latitude,
                'longitude' => $longitude,
                'notes' => $faker->sentence,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        $this->command->info('Successfully added 58 dummy customer records.');
    }
}