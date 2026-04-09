<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Services\RadiusSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncToRadiusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
    public $timeout = 30;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = 30;

    /**
     * Priority for the job (lower is higher priority).
     */
    public $priority = 'high';

    protected $subscriptionId;

    /**
     * Create a new job instance.
     *
     * @param int $subscriptionId
     */
    public function __construct($subscriptionId)
    {
        $this->subscriptionId = $subscriptionId;
    }

    /**
     * Execute the job.
     */
    public function handle(RadiusSyncService $radiusService): void
    {
        // Re-fetch fresh model to ensure latest data
        $subscription = Subscription::with(['package.bwProfile', 'nas'])->find($this->subscriptionId);

        if ($subscription) {
            $radiusService->sync($subscription);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(?\Throwable $exception): void
    {
        // Log the failure
        \Log::error('SyncToRadiusJob failed', [
            'subscription_id' => $this->subscriptionId,
            'exception' => $exception->getMessage(),
        ]);
    }
}
