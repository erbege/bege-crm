<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ComprehensiveSeeder extends Seeder
{
    /**
     * Seed the application's database with comprehensive dummy data.
     */
    public function run(): void
    {
        $this->call([
            AreaSeeder::class,
            CoveragePointSeeder::class,
            NasSeeder::class,
            BwProfileSeeder::class,
            PackageSeeder::class,
            CustomerSeeder::class,
            DummyCustomerSeeder::class,
            SubscriptionSeeder::class,
            InvoiceSeeder::class,
            HotspotVoucherSeeder::class,
        ]);
    }
}