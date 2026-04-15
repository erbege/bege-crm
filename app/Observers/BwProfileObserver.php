<?php

namespace App\Observers;

use App\Models\BwProfile;
use App\Jobs\SyncBwProfileToRadiusJob;
use App\Jobs\RemoveBwProfileFromRadiusJob;
use Illuminate\Support\Facades\Log;

class BwProfileObserver
{
    /**
     * Handle the BwProfile "created" event.
     */
    public function created(BwProfile $bwProfile): void
    {
        SyncBwProfileToRadiusJob::dispatch($bwProfile);
        Log::info("BwProfileObserver: Dispatched Radius sync job for new profile '{$bwProfile->name}'");
    }

    /**
     * Handle the BwProfile "updated" event.
     */
    public function updated(BwProfile $bwProfile): void
    {
        if ($bwProfile->wasChanged(['rate_limit', 'mikrotik_group', 'radius_group', 'name', 'address_pool'])) {
            SyncBwProfileToRadiusJob::dispatch($bwProfile);
            Log::info("BwProfileObserver: Dispatched Radius sync job for updated profile '{$bwProfile->name}'");
        }
    }

    /**
     * Handle the BwProfile "deleted" event.
     */
    public function deleted(BwProfile $bwProfile): void
    {
        $groupNames = collect([$bwProfile->radius_group, $bwProfile->name])
            ->filter()
            ->unique()
            ->toArray();

        RemoveBwProfileFromRadiusJob::dispatch(null, $groupNames);
        Log::info("BwProfileObserver: Dispatched Radius removal job for deleted profile '{$bwProfile->name}' (Groups: " . implode(', ', $groupNames) . ")");
    }
}
