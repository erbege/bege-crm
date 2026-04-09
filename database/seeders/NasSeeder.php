<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nases = [
            [
                'name' => 'Router-UTAMA',
                'shortname' => 'RTR-UTAMA',
                'ip_address' => '192.168.1.10',
                'api_port' => 8728,
                'username' => 'admin',
                'password' => 'admin123',
                'secret' => 'secret123',
                'description' => 'Router utama ISP',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Router-CABANG-01',
                'shortname' => 'RTR-CAB-01',
                'ip_address' => '192.168.2.10',
                'api_port' => 8728,
                'username' => 'admin',
                'password' => 'admin123',
                'secret' => 'secret123',
                'description' => 'Router cabang Jakarta Pusat',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Router-CABANG-02',
                'shortname' => 'RTR-CAB-02',
                'ip_address' => '192.168.3.10',
                'api_port' => 8728,
                'username' => 'admin',
                'password' => 'admin123',
                'secret' => 'secret123',
                'description' => 'Router cabang Jakarta Selatan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('nas')->insert($nases);
    }
}