<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Services\OltScriptService;
use App\Jobs\PushScriptToOltJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProvisioningController extends Controller
{
    /**
     * Show the generated script for a subscription.
     */
    public function showScript(Subscription $subscription, OltScriptService $scriptService)
    {
        // Generate script
        $scriptText = $scriptService->generateActivationScript($subscription);

        // Ideally returns a view, but we'll return text/plain for MVP or specific view
        // return view('admin.provisioning.script_preview', ...);

        // For now, let's dump it or return a view if we create one. 
        // We'll assume a view exists or we create one next.
        // Let's create a simple view for it.
        return view('admin.customers.provisioning-script', [
            'subscription' => $subscription,
            'script' => $scriptText
        ]);
    }

    /**
     * Push configuration to OLT.
     */
    public function pushToOlt(Request $request, Subscription $subscription)
    {
        // Validate status
        if ($subscription->status === 'paid') {
            // return back()->with('error', 'Langganan sudah aktif!'); // Maybe allow re-provisioning?
        }

        if (!$subscription->olt_id) {
            return back()->with('error', 'Langganan belum diassign ke OLT!');
        }

        // Dispatch Job
        PushScriptToOltJob::dispatch($subscription);

        return back()->with('success', 'Perintah provisioning sedang dikirim ke OLT (Background Job). Cek log provisioning nanti.');
    }
}
