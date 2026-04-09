<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\ScriptTemplate;
use Illuminate\Support\Facades\View;

class OltScriptService
{
    /**
     * Generate Activation Script for OLT
     * 
     * @param Subscription $subscription
     * @return string
     */
    public function generateActivationScript(Subscription $subscription)
    {
        // Default to ZTE if not specified
        $brand = $subscription->olt?->brand ?? 'zte';

        // Prepare data for the script
        $data = [
            '{frame}' => $subscription->olt_frame,
            '{slot}' => $subscription->olt_slot,
            '{port}' => $subscription->olt_port,
            '{onu_index}' => $subscription->olt_onu_id,
            '{serial_number}' => $subscription->device_sn,
            '{vlan_id}' => $subscription->service_vlan,
            '{pppoe_username}' => $subscription->pppoe_username,
            '{pppoe_password}' => $subscription->pppoe_password,
            '{tcont_profile}' => $subscription->package?->bwProfile?->mikrotik_group ?? 'UP-50Mbps',
            '{traffic_profile}' => $subscription->package?->bwProfile?->mikrotik_group ?? 'UP-50Mbps',
            '{name}' => $subscription->customer->name ?? 'Unknown',
            '{description}' => ($subscription->customer->name ?? 'Unknown') . ' - ' . ($subscription->customer->identity_number ?? ''),
        ];

        // Fetch template from database
        // We look for a template with matching brand and type='activation'
        $template = ScriptTemplate::where('brand', $brand)
            ->where('type', 'activation')
            ->first();

        // If not found, try fallback or error
        if (!$template) {
            // Fallback to view if DB template not found (backward compatibility)
            $viewName = "scripts.{$brand}.register_onu";
            if (View::exists($viewName)) {
                $viewData = [
                    'frame' => $subscription->olt_frame,
                    'slot' => $subscription->olt_slot,
                    'port' => $subscription->olt_port,
                    'onu_index' => $subscription->olt_onu_id,
                    'serial_number' => $subscription->device_sn,
                    'ont_type' => 'ZTE-F609',
                    'tcont_profile_name' => $subscription->package?->bwProfile?->mikrotik_group ?? 'UP-50Mbps',
                    'traffic_profile_name' => $subscription->package?->bwProfile?->bwProfile?->mikrotik_group ?? 'UP-50Mbps',
                    'vlan_id' => $subscription->service_vlan,
                    'description' => $data['{description}'],
                ];
                return $this->cleanScriptOutput(View::make($viewName, $viewData)->render());
            }

            return "! Script template for {$brand} not found in database or views.";
        }

        $script = $template->content;

        // Replace placeholders
        foreach ($data as $key => $value) {
            $script = str_replace($key, $value ?? '', $script);
        }

        return $this->cleanScriptOutput($script);
    }

    /**
     * Clean up empty lines from script
     */
    private function cleanScriptOutput($script)
    {
        return preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $script);
    }
}
