<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = [
            [
                'name' => 'Jakarta Pusat',
                'code' => 'JKP',
                'type' => 'district',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jakarta Utara',
                'code' => 'JKU',
                'type' => 'district',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jakarta Barat',
                'code' => 'JKB',
                'type' => 'district',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jakarta Selatan',
                'code' => 'JKS',
                'type' => 'district',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jakarta Timur',
                'code' => 'JKT',
                'type' => 'district',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Depok',
                'code' => 'DEP',
                'type' => 'city',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bekasi',
                'code' => 'BEK',
                'type' => 'city',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tangerang',
                'code' => 'TGR',
                'type' => 'city',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('areas')->insert($areas);
    }
}