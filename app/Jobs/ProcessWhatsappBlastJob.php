<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessWhatsappBlastJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 300; // Allows 5 minutes for blast processing depending on volume

    protected array $targets;
    protected ?string $message;
    protected ?string $templateName;
    protected array $data;
    protected array $options;

    /**
     * Create a new job instance.
     */
    public function __construct(array $targets, ?string $message = null, ?string $templateName = null, array $data = [], array $options = [])
    {
        $this->targets = $targets;
        $this->message = $message;
        $this->templateName = $templateName;
        $this->data = $data;
        $this->options = $options;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->targets as $target) {
            // Create the record first
            $whatsappMessage = \App\Models\WhatsappMessage::create([
                'target' => $target,
                'message' => $this->message,
                'template_name' => $this->templateName,
                'template_data' => $this->data,
                'status' => 'pending',
                'provider' => 'fonnte',
                'scheduled_at' => now(),
            ]);

            // Dispatch individual job with the model
            SendWhatsappNotificationJob::dispatch(
                whatsappMessage: $whatsappMessage,
                options: $this->options
            )->delay(now()->addSeconds(rand(1, 10))); // Random delay 1-10s
        }
    }
}
