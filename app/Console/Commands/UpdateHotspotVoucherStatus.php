<?php

namespace App\Console\Commands;

use App\Models\HotspotVoucher;
use App\Models\Radius\RadAcct;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class UpdateHotspotVoucherStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hotspot:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update hotspot voucher status based on Radius Accounting records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting hotspot voucher status update...');
        $updatedCount = 0;
        $expiredCount = 0;

        // 1. Mark ACTIVE vouchers as USED if they have login records
        try {
            HotspotVoucher::where('status', 'active')->chunk(200, function ($vouchers) use (&$updatedCount) {
                $codes = $vouchers->pluck('code')->toArray();

                // Efficiently get the earliest start time for each username found in RadAcct
                $usages = RadAcct::whereIn('username', $codes)
                    ->selectRaw('username, MIN(acctstarttime) as first_login')
                    ->groupBy('username')
                    ->pluck('first_login', 'username'); // [username => first_login]

                foreach ($vouchers as $voucher) {
                    if (isset($usages[$voucher->code])) {
                        $usedAt = Carbon::parse($usages[$voucher->code]);

                        // Calculate Expiration Date based on Profile
                        $expiredAt = null;
                        if ($voucher->profile) {
                            $validityValue = $voucher->profile->validity_value;
                            $validityUnit = $voucher->profile->validity_unit; // hours, days, weeks, months

                            $expiredAt = $usedAt->copy();
                            switch ($validityUnit) {
                                case 'minutes':
                                    $expiredAt->addMinutes($validityValue);
                                    break;
                                case 'hours':
                                    $expiredAt->addHours($validityValue);
                                    break;
                                case 'days':
                                    $expiredAt->addDays($validityValue);
                                    break;
                                case 'weeks':
                                    $expiredAt->addWeeks($validityValue);
                                    break;
                                case 'months':
                                    $expiredAt->addMonths($validityValue);
                                    break;
                                default:
                                    $expiredAt->addDays($validityValue);
                                    break;
                            }
                        }

                        $voucher->update([
                            'status' => 'used',
                            'used_at' => $usedAt,
                            'expired_at' => $expiredAt,
                        ]);

                        $updatedCount++;
                        Log::info("Voucher {$voucher->code} marked USED. First login: {$usedAt}. Expires: {$expiredAt}");
                    }
                }
            });
        } catch (QueryException $e) {
            $this->warn('⚠ Server FreeRadius tidak dapat dijangkau. Update status voucher dari data Radius dilewati.');
            Log::warning('Radius database unreachable in hotspot:update-status: ' . $e->getMessage());
        }

        // 2. Check USED vouchers for Expiration (local DB only, no Radius needed)
        HotspotVoucher::where('status', 'used')
            ->where('expired_at', '<', now())
            ->chunk(200, function ($vouchers) use (&$expiredCount) {

                foreach ($vouchers as $voucher) {
                    $voucher->update(['status' => 'expired']);

                    \App\Jobs\ExpireHotspotVoucherJob::dispatch($voucher);

                    $expiredCount++;
                }
            });

        $this->info("Hotspot voucher status update completed. Updated: {$updatedCount}. Expired & Dispatched: {$expiredCount}.");
    }
}
