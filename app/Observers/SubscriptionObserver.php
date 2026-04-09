<?php

namespace App\Observers;

use App\Models\Subscription;
use App\Services\RadiusSyncService;

class SubscriptionObserver
{
    protected $radiusService;

    public function __construct(RadiusSyncService $radiusService)
    {
        $this->radiusService = $radiusService;
    }

    /**
     * Handle the Subscription "created" event.
     */
    public function created(Subscription $subscription): void
    {
        // DO NOT Sync on creation. Sync only when Invoice is PAID.
        // \App\Jobs\SyncToRadiusJob::dispatch($subscription->id);
    }

    /**
     * Handle the Subscription "updated" event.
     */
    public function updated(Subscription $subscription): void
    {
        // 1. Handle Package Changes (Upgrade/Downgrade)
        if ($subscription->wasChanged('package_id')) {
            $oldPackageId = $subscription->getOriginal('package_id');
            $newPackageId = $subscription->package_id;

            $oldPackage = \App\Models\Package::find($oldPackageId);
            $newPackage = \App\Models\Package::find($newPackageId);

            if ($oldPackage && $newPackage) {
                $type = ($newPackage->price > $oldPackage->price) ? 'upgrade' : 'downgrade';

                \App\Models\SubscriptionHistory::create([
                    'subscription_id' => $subscription->id,
                    'customer_id' => $subscription->customer_id,
                    'previous_package_id' => $oldPackageId,
                    'current_package_id' => $newPackageId,
                    'previous_status' => $subscription->getOriginal('status'),
                    'current_status' => $subscription->status,
                    'type' => $type,
                    'period_start' => $subscription->period_start,
                    'period_end' => $subscription->period_end,
                    'notes' => "Perubahan paket dari {$oldPackage->name} ke {$newPackage->name}",
                ]);

                // User Rule: Sinkronkan hanya jika tidak dalam keadaan isolir/suspended
                if ($subscription->status !== 'suspended') {
                    \App\Jobs\SyncToRadiusJob::dispatch($subscription->id);
                }

                // Kick user to apply new profile
                $this->kickUser($subscription);
            }
        }

        // 2. Handle Status Changes (Termination, etc.)
        if ($subscription->wasChanged('status')) {
            $newStatus = $subscription->status;

            // Handle Berhenti Berlangganan (Cancelled/Terminated)
            if (in_array($newStatus, ['cancelled', 'terminated'])) {
                \App\Models\SubscriptionHistory::create([
                    'subscription_id' => $subscription->id,
                    'customer_id' => $subscription->customer_id,
                    'previous_package_id' => $subscription->package_id,
                    'current_package_id' => $subscription->package_id,
                    'previous_status' => $subscription->getOriginal('status'),
                    'current_status' => $newStatus,
                    'type' => 'termination',
                    'period_start' => $subscription->period_start,
                    'period_end' => now(), // End date is now
                    'notes' => 'Pelanggan berhenti berlangganan',
                ]);

                // Sync removal/update to Radius
                \App\Jobs\SyncToRadiusJob::dispatch($subscription->id);

                // Kick user
                $this->kickUser($subscription);
            }
            // Handle normal status changes (suspended, active, paid)
            elseif (in_array($newStatus, ['suspended', 'active', 'paid'])) {
                \App\Jobs\SyncToRadiusJob::dispatch($subscription->id);
                $this->kickUser($subscription);
            }
        }

        // 3. Handle Other Technical Field Changes
        if ($subscription->wasChanged(['pppoe_username', 'pppoe_password', 'olt_id'])) {
            \App\Jobs\SyncToRadiusJob::dispatch($subscription->id);
        }
    }

    /**
     * Helper to kick user from Mikrotik
     */
    protected function kickUser(Subscription $subscription): void
    {
        if ($subscription->nas_id && $subscription->pppoe_username) {
            \App\Jobs\KickUserJob::dispatch(
                $subscription->nas_id,
                $subscription->pppoe_username,
                $subscription->service_type ?: 'pppoe'
            )->delay(now()->addSeconds(3));
        }
    }

    /**
     * Handle the Subscription "deleted" event.
     */
    public function deleted(Subscription $subscription): void
    {
        if ($subscription->pppoe_username) {
            \App\Jobs\RemoveFromRadiusJob::dispatch($subscription->pppoe_username);
        }
    }
}
