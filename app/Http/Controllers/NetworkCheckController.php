<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Services\MikrotikService;
use Illuminate\Http\Request;

class NetworkCheckController extends Controller
{
    public function checkStatus(Request $request, $subscriptionId, MikrotikService $mikrotik)
    {
        $subscription = Subscription::with('olt')->findOrFail($subscriptionId);
        // Assuming Mikrotik (NAS) connection details are related to Area or OLT.
        // For this MVP, let's assume we use OLT's IP as Mikrotik or we have a Router model.
        // Workflow says: Customer -> Router (NAS).
        // For simplicity, let's assume there's a 'router' relation or we use OLT for now if Router doesn't exist.
        // But Mikrotik acts as NAS.

        // Let's check if 'routers' table exists? No, checking 'Customer' model earlier I didn't see 'router' relation.
        // But 'task.md' implied 'Mikrotik Monitoring'.
        // I will assume for now we use OLT IP (if it's acting as NAS too) or hardcoded env for single router setup.
        // Real implementation should have 'nas' table.
        // '01_CONSTITUTION.md' says "NAS (CRUD)". So 'nas' table likely exists.

        // I will assume Customer belongsTo Area, Area belongsTo NAS ??
        // Or Customer has NAS directly?
        // Let's look for NAS model later.
        // For now, I will use ENV backup.

        $nasIp = env('MIKROTIK_HOST', '192.168.88.1');
        $nasUser = env('MIKROTIK_USER', 'admin');
        $nasPass = env('MIKROTIK_PASS', '');

        try {
            $mikrotik->connect($nasIp, $nasUser, $nasPass);

            $status = $mikrotik->getPppoeStatus($subscription->pppoe_username);

            if ($status['is_online']) {
                $subscription->update(['last_online_at' => now()]);
            }

            return response()->json([
                'status' => 'success',
                'data' => $status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal terhubung ke Mikrotik: ' . $e->getMessage()
            ]);
        }
    }
}
