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

class SyncBwProfileToRadiusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;

    public function __construct(
        public BwProfile $bwProfile
    ) {
    }

    public function handle(RadiusSyncService $radiusSyncService): void
    {
        try {
            $radiusSyncService->syncProfile($this->bwProfile);
            Log::info("SyncBwProfileToRadiusJob: Successfully synced profile '{$this->bwProfile->name}'");
        } catch (\Exception $e) {
            Log::error("SyncBwProfileToRadiusJob: Failed to sync profile '{$this->bwProfile->name}': " . $e->getMessage());
            throw $e; // Re-throw for retry
        }
    }
}
