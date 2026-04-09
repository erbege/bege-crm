<?php

namespace App\Observers;

use App\Models\HotspotProfile;
use App\Jobs\SyncHotspotProfileToRadiusJob;
use Illuminate\Support\Facades\Log;

class HotspotProfileObserver
{
    /**
     * Handle the HotspotProfile "created" event.
     */
    public function created(HotspotProfile $hotspotProfile): void
    {
        SyncHotspotProfileToRadiusJob::dispatch($hotspotProfile, 'sync');
        Log::info("HotspotProfileObserver: Dispatched Radius sync job for new profile '{$hotspotProfile->name}'");
    }

    /**
     * Handle the HotspotProfile "updated" event.
     */
    public function updated(HotspotProfile $hotspotProfile): void
    {
        SyncHotspotProfileToRadiusJob::dispatch($hotspotProfile, 'sync');
        Log::info("HotspotProfileObserver: Dispatched Radius sync job for updated profile '{$hotspotProfile->name}'");
    }

    /**
     * Handle the HotspotProfile "deleted" event.
     */
    public function deleted(HotspotProfile $hotspotProfile): void
    {
        $groupName = $hotspotProfile->mikrotik_group ?: $hotspotProfile->name;
        SyncHotspotProfileToRadiusJob::dispatch(null, 'remove', $groupName);
        Log::info("HotspotProfileObserver: Dispatched Radius remove job for deleted profile '{$hotspotProfile->name}' (Group: {$groupName})");
    }
}
