<?php

namespace App\Livewire\Hotspot;

use App\Models\HotspotProfile;
use App\Models\HotspotVoucher;
use App\Services\RadiusSyncService; // Added
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class VoucherGenerator extends Component
{
    use WithPagination;

    public $profiles; // Added property to store collection
    public $profile_id;
    public $nas_id = 'all'; // 'all' or specific ID
    public $server = 'all';
    public $user_mode = 'username_password'; // 'username_password', 'username_equals_password'
    public $quantity = 1;
    public $length = 6;
    public $prefix = '';
    public $character_type = 'alphanumeric'; // numeric, alpha, alphanumeric
    public $time_limit;
    public $data_limit;
    public $data_unit = 'MB'; // MB, GB
    public $comment;

    public $showDialog = false;
    public $lastBatchId;
    public $generatedCount = 0;

    protected $rules = [
        'profile_id' => 'required|exists:hotspot_profiles,id',
        'quantity' => 'required|integer|min:1|max:999',
        'length' => 'required|integer|min:3|max:12', // Lowered min length
        'prefix' => 'nullable|string|max:10',
        'nas_id' => 'nullable',
        'server' => 'required|string',
        'user_mode' => 'required|string',
    ];

    // Printing Modal Logic
    public $showPrintModal = false;
    public $printBatchId = null;
    public $selectedTemplate = null;

    public function openPrintModal($batchId)
    {
        $this->printBatchId = $batchId;
        $this->selectedTemplate = null; // Default to 'Default'
        $this->showPrintModal = true;
    }

    public function closePrintModal()
    {
        $this->showPrintModal = false;
        $this->printBatchId = null;
        $this->selectedTemplate = null;
    }

    public function render()
    {
        $this->profiles = HotspotProfile::where('is_active', true)->get();
        $nases = \App\Models\Nas::where('is_active', true)->get();
        $templates = \App\Models\HotspotVoucherTemplate::active()->orderBy('name')->get();

        // Group by batch for "Batch History"
        $batches = HotspotVoucher::selectRaw('batch_id, hotspot_profile_id, count(*) as count, min(created_at) as created_at')
            ->whereNotNull('batch_id')
            ->groupBy('batch_id', 'hotspot_profile_id')
            ->orderByDesc('created_at')
            ->paginate(5);

        return view('livewire.hotspot.voucher-generator', [
            'profiles' => $this->profiles,
            'nases' => $nases,
            'batches' => $batches,
            'templates' => $templates,
        ])->layout('layouts.app');
    }

    public function generate()
    {
        $this->quantity = (int) $this->quantity;
        $this->validate();

        $batchId = (string) Str::uuid();
        $vouchers = [];
        $now = now();
        $userId = Auth::id();

        // Calculate data limit in bytes if set
        $dataLimitBytes = null;
        if ($this->data_limit) {
            $dataLimitBytes = $this->data_unit === 'GB'
                ? $this->data_limit * 1024 * 1024 * 1024
                : $this->data_limit * 1024 * 1024;
        }

        // Pre-fetch existing codes for in-memory uniqueness check (avoids N+1)
        $existingCodes = HotspotVoucher::pluck('code')->flip();

        for ($i = 0; $i < $this->quantity; $i++) {
            $code = $this->generateCode();

            // Generate password logic
            if ($this->user_mode === 'username_equals_password') {
                $password = $code;
            } else {
                $password = $this->generateCode();
            }

            // Ensure uniqueness via in-memory check (no DB query per iteration)
            while (isset($existingCodes[$code])) {
                $code = $this->generateCode();
                if ($this->user_mode === 'username_equals_password') {
                    $password = $code;
                }
            }
            // Track newly generated code to prevent duplicates within this batch
            $existingCodes[$code] = true;

            $vouchers[] = [
                'hotspot_profile_id' => $this->profile_id,
                'code' => $code,
                'password' => $password,
                'batch_id' => $batchId,
                'status' => 'active',
                'created_by' => $userId,
                'nas_id' => $this->nas_id === 'all' ? null : $this->nas_id,
                'user_mode' => $this->user_mode,
                'time_limit' => $this->parseTimeLimit($this->time_limit),
                'data_limit' => $dataLimitBytes,
                'comment' => $this->comment,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        HotspotVoucher::insert($vouchers);

        // Sync to FreeRadius
        try {
            $selectedProfile = $this->profiles->firstWhere('id', $this->profile_id);
            $profileName = $selectedProfile ? $selectedProfile->name : 'default';
            $mikrotikGroup = $selectedProfile ? $selectedProfile->mikrotik_group : null;

            // Get NAS Shortname if specific NAS is selected
            $nasShortname = null;
            if ($this->nas_id && $this->nas_id !== 'all') {
                $selectedNas = \App\Models\Nas::find($this->nas_id);
                $nasShortname = $selectedNas ? $selectedNas->shortname : null;
            }

            // Prepare data for sync
            $syncData = array_map(function ($voucher) use ($profileName, $mikrotikGroup, $nasShortname) {
                $voucher['profile_name'] = $profileName;
                $voucher['mikrotik_group'] = $mikrotikGroup;
                $voucher['nas_shortname'] = $nasShortname;
                return $voucher;
            }, $vouchers);

            // Dispatch to Queue
            \App\Jobs\SyncHotspotVouchersJob::dispatch($syncData);

        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', message: 'Voucher saved but Radius sync job failed: ' . $e->getMessage());
        }

        $this->lastBatchId = $batchId;
        $this->generatedCount = count($vouchers);
        // $this->showDialog = true; // Removed old dialog

        session()->forget('message');
        $this->dispatch('toast', type: 'success', message: "Generated {$this->generatedCount} vouchers successfully.");
        $this->reset(['quantity', 'prefix']);

        // Open Print Modal immediately
        $this->openPrintModal($batchId);
    }

    private function generateCode()
    {
        $chars = '';

        switch ($this->character_type) {
            case 'lower': // Random abcd
                $chars = 'abcdefghijkmnpqrstuvwxyz';
                break;
            case 'upper': // Random ABCD
                $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
                break;
            case 'mixed': // Random aBcD
                $chars = 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
                break;
            case 'lower_num': // Random 5ab2c34d
                $chars = 'abcdefghijkmnpqrstuvwxyz23456789';
                break;
            case 'upper_num': // Random 5AB2C34D
                $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
                break;
            case 'mixed_num': // Random 5aB2c34D
                $chars = 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
                break;
            case 'numeric':
                $chars = '0123456789';
                break;
            default: // alphanumeric or fallback
                $chars = 'abcdefghijkmnpqrstuvwxyz23456789';
                break;
        }

        $random = '';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $this->length; $i++) {
            $random .= $chars[rand(0, $max)];
        }

        return $this->prefix . $random;
    }

    /**
     * Parse time limit string (e.g. 1h, 30m) to seconds.
     */
    private function parseTimeLimit(?string $input): ?int
    {
        if (empty($input)) {
            return null;
        }

        // Remove whitespace
        $input = strtolower(str_replace(' ', '', $input));

        // Get value and unit
        $value = (int) $input;
        $unit = substr($input, -1);

        // If numeric only, assume minutes (standard hotspot practice) or seconds?
        // Let's assume minutes as per typical hotspot usage.
        if (is_numeric($input)) {
            return $value * 60;
        }

        switch ($unit) {
            case 'h':
                return $value * 3600;
            case 'm':
                return $value * 60;
            case 'd':
                return $value * 86400;
            default:
                return $value; // Fallback to raw value
        }
    }
}
