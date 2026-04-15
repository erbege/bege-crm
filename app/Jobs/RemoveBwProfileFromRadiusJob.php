<?php

namespace App\Jobs;

use App\Models\BwProfile;
use App\Services\RadiusSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RemoveBwProfileFromRadiusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public ?BwProfile $bwProfile = null,
        public ?array $groupNamesOverride = null
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(RadiusSyncService $radiusSyncService): void
    {
        $logName = $this->bwProfile?->name ?? (is_array($this->groupNamesOverride) ? implode(', ', $this->groupNamesOverride) : 'unknown');

        try {
            $radiusSyncService->removeProfile($this->bwProfile ?? ($this->groupNamesOverride[0] ?? 'unknown'), $this->groupNamesOverride);
            Log::info("RemoveBwProfileFromRadiusJob: Successfully removed profile '{$logName}'");
        } catch (\Exception $e) {
            Log::error("RemoveBwProfileFromRadiusJob: Failed to remove profile '{$logName}': " . $e->getMessage());
            throw $e;
        }
    }
}
