<?php

namespace App\Jobs;

use App\Models\HotspotProfile;
use App\Services\RadiusSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncHotspotProfileToRadiusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(
        public ?HotspotProfile $hotspotProfile = null,
        public string $action = 'sync', // 'sync' or 'remove'
        public ?string $groupNameOverride = null
    ) {
    }

    public function handle(RadiusSyncService $radiusSyncService): void
    {
        $targetName = $this->groupNameOverride ?? ($this->hotspotProfile?->name ?? 'unknown');

        try {
            if ($this->action === 'remove') {
                $radiusSyncService->removeHotspotProfile($this->groupNameOverride ?? $this->hotspotProfile);
                Log::info("SyncHotspotProfileToRadiusJob: Removed profile '{$targetName}' from Radius");
            } else {
                if (!$this->hotspotProfile) {
                    Log::error("SyncHotspotProfileToRadiusJob: Missing hotspotProfile for 'sync' action.");
                    return;
                }
                $radiusSyncService->syncHotspotProfile($this->hotspotProfile);
                Log::info("SyncHotspotProfileToRadiusJob: Synced profile '{$this->hotspotProfile->name}' to Radius");
            }
        } catch (\Exception $e) {
            Log::error("SyncHotspotProfileToRadiusJob: Failed ({$this->action}) for '{$targetName}': " . $e->getMessage());
            throw $e; // Re-throw for retry
        }
    }
}
