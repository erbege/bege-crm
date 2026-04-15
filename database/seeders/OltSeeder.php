<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OltSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('olts')->insert([
            [
                'name' => 'OLT-Pusat',
                'ip_address' => '192.168.100.10',
                'username' => 'admin',
                'password' => 'admin123',
                'brand' => 'zte',
                'description' => 'OLT ZTE C320 di Server Utama',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'OLT-Cabang-A',
                'ip_address' => '192.168.101.10',
                'username' => 'admin',
                'password' => 'admin123',
                'brand' => 'zte',
                'description' => 'OLT ZTE C300 di Cabang A',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
