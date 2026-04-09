<?php

namespace App\Jobs;

use App\Services\RadiusSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RemoveFromRadiusJob implements ShouldQueue
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

    protected $username;

    /**
     * Create a new job instance.
     */
    public function __construct(string $username)
    {
        $this->username = $username;
    }

    /**
     * Execute the job.
     */
    public function handle(RadiusSyncService $radiusService): void
    {
        if ($this->username) {
            $radiusService->removeByUsername($this->username);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(?\Throwable $exception): void
    {
        Log::error('RemoveFromRadiusJob failed', [
            'username' => $this->username,
            'exception' => $exception->getMessage(),
        ]);
    }
}
