<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        
        $subscriptionIds = DB::table('subscriptions')->pluck('id')->toArray();
        
        foreach ($subscriptionIds as $subscriptionId) {
            // Each subscription gets 1-3 invoices
            $numInvoices = rand(1, 3);
            
            for ($i = 0; $i < $numInvoices; $i++) {
                $issueDate = $faker->dateTimeBetween('-6 months', 'now');
                $dueDate = (clone $issueDate)->modify('+14 days');
                
                $subtotal = $faker->randomElement([250000, 350000, 450000, 650000, 950000]);
                $installationFee = $faker->randomElement([0, 100000]);
                $tax = ($subtotal + $installationFee) * 0.11; // 11% tax
                $discount = $faker->boolean(20) ? $faker->randomElement([25000, 50000, 100000]) : 0;
                $total = ($subtotal + $installationFee + $tax) - $discount;
                
                $status = $faker->randomElement(['paid', 'unpaid', 'cancelled']);
                $paidAt = null;
                
                if ($status === 'paid') {
                    $paidAt = $faker->dateTimeBetween($issueDate, 'now');
                }
                
                // Generate unique invoice number
                $prefix = 'INV';
                $yearMonth = $issueDate->format('Ym');
                $sequence = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
                $invoiceNumber = sprintf('%s-%s-%s', $prefix, $yearMonth, $sequence);
                
                DB::table('invoices')->insert([
                    'invoice_number' => $invoiceNumber,
                    'subscription_id' => $subscriptionId,
                    'customer_id' => DB::table('subscriptions')->where('id', $subscriptionId)->value('customer_id'),
                    'issue_date' => $issueDate,
                    'due_date' => $dueDate,
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'installation_fee' => $installationFee,
                    'discount' => $discount,
                    'total' => $total,
                    'status' => $status,
                    'paid_at' => $paidAt,
                    'payment_method' => $faker->randomElement(['transfer', 'cash', 'credit_card']),
                    'notes' => $faker->sentence,
                    'sent_at' => $faker->dateTimeBetween('-6 months', 'now'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}