<?php

namespace App\Jobs;

use App\Services\RadiusSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncHotspotVouchersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $vouchersData;

    /**
     * Create a new job instance.
     *
     * @param array $vouchersData
     */
    public function __construct(array $vouchersData)
    {
        $this->vouchersData = $vouchersData;
    }

    /**
     * Execute the job.
     */
    public function handle(RadiusSyncService $radiusSyncService): void
    {
        try {
            Log::info("Job: Syncing " . count($this->vouchersData) . " vouchers to Radius...");
            $radiusSyncService->bulkSyncHotspotVouchers($this->vouchersData);
            Log::info("Job: Local vouchers synced successfully.");
        } catch (\Exception $e) {
            Log::error("Job: Failed to sync vouchers to Radius: " . $e->getMessage());
            // Optionally release the job back to the queue to retry
            $this->release(10);
        }
    }
}
