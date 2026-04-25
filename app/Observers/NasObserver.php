<?php

namespace App\Observers;

use App\Models\Nas;
use App\Services\RadiusSyncService;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Log;

class NasObserver
{
    protected $radiusSyncService;
    protected $mikrotikService;

    public function __construct(RadiusSyncService $radiusSyncService, \App\Services\MikrotikService $mikrotikService)
    {
        $this->radiusSyncService = $radiusSyncService;
        $this->mikrotikService = $mikrotikService;
    }

    /**
     * Handle the Nas "created" event.
     */
    public function created(Nas $nas): void
    {
        try {
            $this->radiusSyncService->syncNas($nas);
        } catch (DecryptException $e) {
            Log::warning("NasObserver::created - DecryptException saat sync NAS {$nas->shortname}: {$e->getMessage()}");
        }
    }

    /**
     * Handle the Nas "updated" event.
     */
    public function updated(Nas $nas): void
    {
        // Skip sync if only status fields changed (is_online, last_check)
        $statusOnlyFields = ['is_online', 'last_check'];
        $changedFields = array_keys($nas->getDirty());
        $nonStatusChanges = array_diff($changedFields, $statusOnlyFields);

        if (empty($nonStatusChanges)) {
            return;
        }

        try {
            $this->radiusSyncService->syncNas($nas);
            $this->mikrotikService->clearNasCache($nas->id);
        } catch (DecryptException $e) {
            Log::warning("NasObserver::updated - DecryptException saat sync NAS {$nas->shortname}: {$e->getMessage()}");
        }
    }

    /**
     * Handle the Nas "deleted" event.
     */
    public function deleted(Nas $nas): void
    {
        try {
            $this->radiusSyncService->removeNas($nas);
        } catch (DecryptException $e) {
            Log::warning("NasObserver::deleted - DecryptException saat remove NAS {$nas->shortname}: {$e->getMessage()}");
        }
    }
}
