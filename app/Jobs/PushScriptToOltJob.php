<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Services\OltScriptService;
use App\Services\OltSshService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PushScriptToOltJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $subscription;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public $maxExceptions = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public $timeout = 120;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = 60;

    /**
     * Delete the job if the model is missing.
     */
    public $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     */
    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * Execute the job.
     */
    public function handle(OltScriptService $scriptService, OltSshService $sshService): void
    {
        // Check if subscription has OLT assigned
        if (!$this->subscription->olt_id) {
            Log::warning("Provisioning ignored: Subscription {$this->subscription->id} has no OLT assigned.");
            return;
        }

        $olt = $this->subscription->olt;

        Log::info("Starting provisioning for Subscription ID: {$this->subscription->id} on OLT {$olt->name} ({$olt->ip_address})");

        try {
            // 1. Generate Script
            $script = $scriptService->generateActivationScript($this->subscription);

            // 2. Connect to OLT
            $sshService->connect($olt->ip_address, $olt->username, $olt->password, $olt->port);

            // 3. Execute
            $logs = $sshService->executeScript($script);

            // 4. Disconnect
            $sshService->disconnect();

            // 5. Update Status
            $this->subscription->update([
                'status' => 'active', // Mark as active if provisioning succeeds
                'provisioned_at' => now(),
                'last_provisioning_log' => $logs
            ]);

            Log::info("Provisioning Success for Subscription ID: {$this->subscription->id}");
        } catch (\Exception $e) {
            Log::error("Provisioning Failed for Subscription ID: {$this->subscription->id}: " . $e->getMessage());
            $this->fail($e);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(?\Throwable $exception): void
    {
        Log::error('PushScriptToOltJob failed', [
            'subscription_id' => $this->subscription->id ?? null,
            'exception' => $exception->getMessage(),
        ]);
    }
}
