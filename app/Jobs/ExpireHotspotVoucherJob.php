<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExpireHotspotVoucherJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public $voucher;

    /**
     * Create a new job instance.
     */
    public function __construct(\App\Models\HotspotVoucher $voucher)
    {
        $this->voucher = $voucher;
    }

    /**
     * Execute the job.
     */
    public function handle(
        \App\Services\RadiusSyncService $radiusSyncService,
        \App\Services\MikrotikService $mikrotikService
    ): void {
        try {
            // Ensure status is expired if not already (redundant check but safe)
            if ($this->voucher->status !== 'expired') {
                $this->voucher->update(['status' => 'expired']);
            }

            // Remove from Radius
            $radiusSyncService->removeHotspotVoucher($this->voucher->code);

            // Active Disconnect & Cleanup from Mikrotik
            if ($this->voucher->nas) {
                // This will connect to the NAS and kick the user
                $mikrotikService->kickUserFromNas($this->voucher->nas, $this->voucher->code, 'hotspot');

                // Remove Cookie (uses the connection established above)
                $mikrotikService->removeHotspotCookie($this->voucher->code);

                \Illuminate\Support\Facades\Log::info("ExpireJob: Active session terminated & cookie removed for {$this->voucher->code} on NAS {$this->voucher->nas->shortname}");
            } else {
                \Illuminate\Support\Facades\Log::warning("ExpireJob: Voucher {$this->voucher->code} has no NAS assigned. Skipping active disconnect.");
            }

            \Illuminate\Support\Facades\Log::info("ExpireJob: Voucher {$this->voucher->code} fully expired.");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("ExpireJob: Failed to cleanup voucher {$this->voucher->code}: " . $e->getMessage());
            // Release back to queue if transient error? Or just log.
            // For now, log.
        }
    }
}
