<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class NasCheckStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nas:check-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check connection status of all active NAS devices';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\MikrotikService $mikrotikService)
    {
        $nasList = \App\Models\Nas::where('is_active', true)->get();
        $this->info("Checking status for " . $nasList->count() . " NAS devices...");

        foreach ($nasList as $nas) {
            // Run check in parallel would be better for many devices, but loop is fine for now
            $isOnline = $mikrotikService->checkConnection($nas);

            $nas->update([
                'is_online' => $isOnline,
                'last_check' => now(),
            ]);

            $status = $isOnline ? 'Online' : 'Offline';
            $this->line("- {$nas->name}: {$status}");
        }

        $this->info("NAS status check completed.");
    }
}
