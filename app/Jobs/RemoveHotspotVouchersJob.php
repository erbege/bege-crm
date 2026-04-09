<?php

namespace App\Jobs;

use App\Services\RadiusSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RemoveHotspotVouchersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $codes;

    /**
     * Create a new job instance.
     *
     * @param array $codes
     */
    public function __construct(array $codes)
    {
        $this->codes = $codes;
    }

    /**
     * Execute the job.
     */
    public function handle(RadiusSyncService $radiusSyncService): void
    {
        try {
            Log::info("Job: Removing " . count($this->codes) . " vouchers from Radius...");
            $radiusSyncService->bulkRemoveHotspotVouchers($this->codes);
            Log::info("Job: Vouchers removed from Radius successfully.");
        } catch (\Exception $e) {
            Log::error("Job: Failed to remove vouchers from Radius: " . $e->getMessage());
            $this->release(10);
        }
    }
}
