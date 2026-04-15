<?php

namespace App\Jobs;

use App\Models\Nas;
use App\Services\MikrotikService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class KickUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $nasId;
    protected $username;
    protected $serviceType;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public $maxExceptions = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public $timeout = 30;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = 60;

    /**
     * Create a new job instance.
     *
     * @param int $nasId
     * @param string $username
     * @param string $serviceType
     */
    public function __construct(int $nasId, string $username, string $serviceType = 'pppoe')
    {
        $this->nasId = $nasId;
        $this->username = $username;
        $this->serviceType = $serviceType;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $nas = Nas::find($this->nasId);

        if (!$nas) {
            Log::warning("KickUserJob canceled: NAS ID {$this->nasId} not found.");
            return;
        }

        try {
            $mikrotik = new MikrotikService();
            // We need to connect first. MikrotikService refactor didn't change connect method signature, 
            // but it relies on connect() being called.
            // The existing connect method: public function connect($ip, $user, $pass, $port = 8728)

            $mikrotik->connect($nas->ip_address, $nas->username, $nas->password, $nas->api_port);

            $mikrotik->kickUser($this->username, $this->serviceType);

            Log::info("KickUserJob success: kicked {$this->username} from NAS {$nas->shortname}");
        } catch (\Exception $e) {
            Log::error("KickUserJob failed for {$this->username} on NAS {$nas->shortname}: " . $e->getMessage());
            $this->fail($e);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(?\Throwable $exception): void
    {
        Log::error('KickUserJob failed', [
            'nas_id' => $this->nasId,
            'username' => $this->username,
            'exception' => $exception->getMessage(),
        ]);
    }
}
