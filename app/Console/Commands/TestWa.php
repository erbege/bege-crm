<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestWa extends Command
{
    protected $signature = 'test:wa';
    public function handle()
    {
        $waMessage = \App\Models\WhatsappMessage::create([
            'target' => '081234567890',
            'template_name' => 'PENAGIHAN',
            'template_data' => [
                'invoice' => 'INV-TEST-001',
                'nama_pelanggan' => 'John Doe',
                'nolayanan' => '12345',
                'profile' => 'Internet 10Mbps',
                'jatuh_tempo' => '25/02/2026',
                'total' => 'Rp 150.000',
                'link_invoice' => 'http://localhost'
            ]
        ]);

        $this->info('Created message ID: ' . $waMessage->id);

        $job = new \App\Jobs\SendWhatsappNotificationJob($waMessage);
        $service = new \App\Services\Whatsapp\WhatsappService();
        try {
            $job->handle($service);
            $this->info('Job completed successfully');
        } catch (\Exception $e) {
            $this->error('Job failed: ' . $e->getMessage());
        }

        $waMessage->refresh();
        $this->info('Final Status: ' . $waMessage->status);
        $this->info('Message stored: ' . $waMessage->message);
        $this->info('Error stored: ' . $waMessage->error);
    }
}
