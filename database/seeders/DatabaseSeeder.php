<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles first
        $this->call([
            RoleSeeder::class,
            UserRoleSeeder::class,
            // OltSeeder::class,
        ]);

        // Add comprehensive dummy data
        // $this->call(ComprehensiveSeeder::class);
        // $this->call(DummyDataSeeder::class);
    }
}
