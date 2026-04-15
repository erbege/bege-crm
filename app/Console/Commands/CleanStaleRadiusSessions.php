<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanStaleRadiusSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hotspot:clean-stale-sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up stale/idle hotspot sessions in Radius database (no update for 15+ minutes)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Starting stale session cleanup...");
        Log::info("CleanStaleRadiusSessions: Starting stale session cleanup.");

        try {
            $threshold = now()->subMinutes(15);

            // Using direct DB facade for performance (bypassing Eloquent overhead for mass updates)
            $affectedRows = DB::connection('radius')
                ->table('radacct')
                ->whereNull('acctstoptime') // Only online sessions
                ->where(function ($q) {
                    // Hotspot only (not PPP)
                    $q->where('framedprotocol', '!=', 'PPP')
                        ->orWhereNull('framedprotocol')
                        ->orWhere('framedprotocol', '');
                })
                ->where('acctupdatetime', '<', $threshold) // Idle for more than 15 minutes
                ->update([
                    'acctstoptime' => DB::raw('acctupdatetime'), // Close it at the last known active time
                    'acctterminatecause' => 'Idle-Timeout-Script',
                ]);

            if ($affectedRows > 0) {
                $this->info("Cleaned up {$affectedRows} stale sessions.");
                Log::info("CleanStaleRadiusSessions: Cleaned up {$affectedRows} stale hotspot sessions.");
            } else {
                $this->info("No stale sessions found.");
            }

        } catch (\Exception $e) {
            $this->error("Failed to clean up stale sessions: " . $e->getMessage());
            Log::error("CleanStaleRadiusSessions: Error - " . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
