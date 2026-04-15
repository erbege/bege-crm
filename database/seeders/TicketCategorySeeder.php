<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TicketCategory;

class TicketCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Teknis', 'description' => 'Kendala koneksi, peralatan, atau gangguan teknis lainnya.'],
            ['name' => 'Tagihan', 'description' => 'Pertanyaan atau kendala terkait pembayaran dan tagihan.'],
            ['name' => 'Umum', 'description' => 'Pertanyaan atau informasi umum lainnya.'],
        ];

        foreach ($categories as $category) {
            TicketCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
