<?php

namespace App\Jobs;

use App\Services\Whatsapp\WhatsappService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsappNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public \App\Models\WhatsappMessage $whatsappMessage,
        public array $options = []
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(WhatsappService $whatsappService): void
    {
        $log = $this->whatsappMessage;

        // Ensure status is pending/processing
        if ($log->status === 'failed' || $log->status === 'sent') {
            // Optional: prevent re-sending if already sent?
            // For now, we allow resending.
        }

        try {
            if ($log->template_name && empty($log->message)) {
                $template = \App\Models\WhatsappMessageTemplate::where('name', $log->template_name)
                    ->where('is_active', true)
                    ->first();

                if ($template) {
                    $log->message = $template->format($log->template_data ?? []);
                    $log->save();
                } else {
                    Log::warning("SendWhatsappNotificationJob: WhatsApp template '{$log->template_name}' not found or inactive.");
                    $log->update(['status' => 'failed', 'error' => "Template '{$log->template_name}' not found or inactive"]);
                    return;
                }
            }

            if ($log->message) {
                $result = $whatsappService->send($log->target, $log->message, $this->options);
            } else {
                Log::warning("SendWhatsappNotificationJob: No message or template provided for {$log->target}");
                $log->update(['status' => 'failed', 'error' => 'No message or template provided']);
                return;
            }

            if ($result === false) {
                $log->update([
                    'status' => 'failed',
                    'error' => 'WhatsApp Service returned false. Check logs.',
                ]);
                $this->fail(new \Exception("WhatsApp Service returned false. Check logs."));
            } else {
                $log->update([
                    'status' => 'sent',
                    'response' => $result,
                ]);
            }

        } catch (\Exception $e) {
            Log::error("SendWhatsappNotificationJob failed: " . $e->getMessage());
            $log->update([
                'status' => 'failed',
                'error' => $e->getMessage(),
            ]);
            $this->fail($e);
        }
    }
}
