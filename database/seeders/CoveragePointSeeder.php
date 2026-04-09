<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoveragePointSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coveragePoints = [
            [
                'name' => 'ODP-Menteng-01',
                'code' => 'ODP-MTG-01',
                'area_id' => 1, // Jakarta Pusat
                'type' => 'odp',
                'capacity' => 32,
                'used_ports' => 0,
                'latitude' => -6.189745,
                'longitude' => 106.827690,
                'address' => 'Jl. Menteng Raya No. 1, Jakarta Pusat',
                'description' => 'Optical Distribution Point Menteng 01',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ODP-Kemayoran-02',
                'code' => 'ODP-KMY-02',
                'area_id' => 1, // Jakarta Pusat
                'type' => 'odp',
                'capacity' => 32,
                'used_ports' => 0,
                'latitude' => -6.167222,
                'longitude' => 106.840278,
                'address' => 'Jl. Kemayoran Baru No. 5, Jakarta Pusat',
                'description' => 'Optical Distribution Point Kemayoran 02',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ODP-Grogol-03',
                'code' => 'ODP-GRG-03',
                'area_id' => 3, // Jakarta Barat
                'type' => 'odp',
                'capacity' => 32,
                'used_ports' => 0,
                'latitude' => -6.191944,
                'longitude' => 106.762500,
                'address' => 'Jl. Grogol Raya No. 10, Jakarta Barat',
                'description' => 'Optical Distribution Point Grogol 03',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ODP-Cilandak-04',
                'code' => 'ODP-CLD-04',
                'area_id' => 4, // Jakarta Selatan
                'type' => 'odp',
                'capacity' => 32,
                'used_ports' => 0,
                'latitude' => -6.288889,
                'longitude' => 106.798611,
                'address' => 'Jl. TB Simatupang No. 15, Jakarta Selatan',
                'description' => 'Optical Distribution Point Cilandak 04',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ODP-Bekasi-Timur-05',
                'code' => 'ODP-BET-05',
                'area_id' => 7, // Bekasi
                'type' => 'odp',
                'capacity' => 32,
                'used_ports' => 0,
                'latitude' => -6.233333,
                'longitude' => 107.100000,
                'address' => 'Jl. Jendral Ahmad Yani No. 8, Bekasi Timur',
                'description' => 'Optical Distribution Point Bekasi Timur 05',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('coverage_points')->insert($coveragePoints);
    }
}