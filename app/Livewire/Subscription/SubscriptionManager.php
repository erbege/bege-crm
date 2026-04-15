<?php

namespace App\Livewire\Subscription;

use App\Models\CoveragePoint;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Package;
use App\Models\Olt;
use App\Models\Setting;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Illuminate\Support\Str;
use App\Services\RadiusSyncService;

class SubscriptionManager extends Component
{
    use WithPagination;

    // Search & Filter
    public $search = '';
    public $filterStatuses = ['active', 'suspended', 'cancelled', 'pending']; // All enabled by default
    public $filterDateFrom = '';
    public $filterDateTo = '';
    public $perPage = 15;

    // Modal State
    public $editMode = false;
    public $showModal = false;
    public $isCustomerModalOpen = false;
    public $customerSearch = '';

    // Form Fields
    public $subscription_id;
    public $customer_id;
    public $customer_number = '';
    public $customer_name = '';
    public $customer_phone = '';
    public $customer_address = '';
    public $package_id;
    public $period_start;
    public $period_end;
    public $installation_date;
    public $amount = 0;
    public $installation_fee = 0; // New property
    public $discount = 0;
    public $tax = 0;
    public $total = 0;
    public $status = 'unpaid';
    public $paid_at;
    public $payment_method;
    public $notes;
    public $subscription_type = 'prepaid';

    // Technical Fields
    public $pppoe_username;
    public $pppoe_password;
    public $service_type = 'ppp'; // Default
    public $mac_address;
    public $ip_address;
    public $device_sn;
    public $coverage_point_id;
    public $olt_id;
    public $olt_frame;
    public $olt_slot;
    public $olt_port;

    public $olt_onu_id;
    public $nas_id;
    public $server_name;

    // PPPoE Settings
    public $pppoePrefix;
    public $pppoeSuffix;
    public $pppoePasswordLength;


    // Lists for Dropdowns
    public $customers = [];
    // packages, olts, nasList, coverage_points removed (now Computed)

    // Proration tracking
    public $isProrated = false;
    public $originalAmount = 0;
    public $proratedDays = 0;
    public $totalDays = 0;
    public $taxPercentage = 0;
    public $allowedSubscriptionType = 'both';

    // Original package tracking (for upgrade/downgrade detection)
    public $original_package_id = null;
    public $original_package_name = '';
    public $original_package_price = 0;

    protected function rules()
    {
        $rules = [
            'customer_id' => 'required|exists:customers,id',
            'package_id' => 'required|exists:packages,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'installation_date' => 'nullable|date',
            'amount' => 'required|numeric|min:0',
            'installation_fee' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'status' => 'required|in:unpaid,paid,partial,cancelled,active,suspended,terminated,pending',
            'payment_method' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'device_sn' => 'nullable|string|max:100',
            'coverage_point_id' => 'nullable|exists:coverage_points,id',
            'olt_id' => 'nullable|exists:olts,id',
            'olt_frame' => 'nullable|integer',
            'olt_slot' => 'nullable|integer',
            'olt_port' => 'nullable|integer',
            'olt_onu_id' => 'nullable|integer',
            'nas_id' => 'nullable|exists:nas,id',
            'server_name' => 'nullable|string|max:100',
            'service_type' => 'required|in:ppp,dhcp,hotspot',
            'ip_address' => 'nullable|ipv4',
        ];

        if ($this->service_type === 'ppp') {
            $rules['pppoe_username'] = 'required|string|max:100|unique:subscriptions,pppoe_username,' . $this->subscription_id;
            $rules['pppoe_password'] = 'required|string|max:100';
        } elseif ($this->service_type === 'dhcp') {
            $rules['mac_address'] = 'required|string|max:17|unique:subscriptions,mac_address,' . $this->subscription_id;
        } elseif ($this->service_type === 'hotspot') {
            // Re-using pppoe fields for hotspot user/pass interaction
            $rules['pppoe_username'] = 'required|string|max:100';
            $rules['pppoe_password'] = 'required|string|max:100';
        }

        return $rules;
    }

    protected $messages = [
        'customer_id.required' => 'Pelanggan wajib dipilih.',
        'package_id.required' => 'Paket wajib dipilih.',
        'period_start.required' => 'Tanggal mulai wajib diisi.',
        'period_end.required' => 'Tanggal akhir wajib diisi.',
        'period_end.after_or_equal' => 'Tanggal akhir harus sama atau setelah tanggal mulai.',
        'amount.required' => 'Jumlah tagihan wajib diisi.',
        'amount.min' => 'Jumlah tagihan tidak boleh negatif.',
        'installation_fee.min' => 'Biaya instalasi tidak boleh negatif.',
    ];

    #[Computed]
    public function packages()
    {
        $packages = Cache::remember('subscription:packages', 3600, function () {
            return Package::active()->orderBy('name')->get();
        });

        return $packages->where('service_type', strtoupper($this->service_type));
    }


    #[Computed]
    public function olts()
    {
        return Cache::remember('subscription:olts', 3600, function () {
            return Olt::all();
        });
    }

    #[Computed]
    public function nasList()
    {
        return Cache::remember('subscription:nas', 3600, function () {
            return \App\Models\Nas::active()->orderBy('name')->get();
        });
    }

    #[Computed]
    public function coveragePoints()
    {
        if (!$this->customer_id) {
            return collect();
        }

        return Cache::remember(
            "coverage:customer:{$this->customer_id}",
            300,
            function () {
                $customer = Customer::select('id', 'area_id')->find($this->customer_id);
                if (!$customer?->area_id) {
                    return collect();
                }

                return CoveragePoint::where('area_id', $customer->area_id)
                    ->active()
                    ->orderBy('name')
                    ->get();
            }
        );
    }

    public function mount()
    {
        // Don't load all customers initially - use search/autocomplete instead
        $this->customers = [];

        $this->filterDateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->filterDateTo = now()->endOfMonth()->format('Y-m-d');
        $this->taxPercentage = (float) Setting::get('billing.tax_percentage', 0);
        $this->allowedSubscriptionType = Setting::get('billing.subscription_type', 'both');

        // Load PPPoE Settings
        $this->pppoePrefix = Setting::get('technical.pppoe_username_prefix', '');
        $this->pppoeSuffix = Setting::get('technical.pppoe_username_suffix', '');
        $this->pppoePasswordLength = (int) Setting::get('technical.pppoe_password_length', 6);

        $this->initPeriod();
    }

    public function updatedServiceType($value)
    {
        // Reset specific fields when service type changes
        $this->resetValidation();
        if ($value === 'dhcp') {
            $this->pppoe_username = null;
            $this->pppoe_password = null;
        } elseif ($value === 'ppp' || $value === 'hotspot') {
            $this->mac_address = null;
        }
        $this->package_id = null; // Reset package as it might not be available for this service
    }

    public function generateCredentials()
    {
        if (!$this->customer_id) {
            $this->dispatch('toast', type: 'error', message: 'Pilih pelanggan terlebih dahulu.');
            return;
        }

        // Generate Username: PREFIX + CustomerID/Phone + Random? 
        // Let's use simpler logic: Prefix + Random 6 chars
        $prefix = $this->pppoePrefix ?: 'user';
        $random = Str::lower(Str::random(6));
        $this->pppoe_username = $prefix . $random . $this->pppoeSuffix;

        // Generate Password
        $length = $this->pppoePasswordLength ?: 6;
        $this->pppoe_password = Str::random($length);
    }

    public function render()
    {
        $subscriptions = Subscription::query()
            ->select(['subscriptions.*'])
            ->with(['customer:id,customer_id,name,phone', 'package:id,name,price', 'coveragePoint:id,name,code'])
            ->when($this->search, function ($q) {
                $q->whereHas('customer', function ($sub) {
                    $sub->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('customer_id', 'like', '%' . $this->search . '%');
                });
            })
            ->when(count($this->filterStatuses) < 4, function ($q) {
                $q->whereIn('status', $this->filterStatuses);
            })
            ->when($this->filterDateFrom, fn($q) => $q->where('period_start', '>=', $this->filterDateFrom))
            ->when($this->filterDateTo, fn($q) => $q->where('period_start', '<=', $this->filterDateTo))
            ->latest()
            ->paginate($this->perPage);

        // Get filtered customers for modal - Only query if modal is open
        $filteredCustomers = collect();
        if ($this->isCustomerModalOpen) {
            $filteredCustomers = Customer::query()
                ->when($this->customerSearch, function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('name', 'like', '%' . $this->customerSearch . '%')
                            ->orWhere('customer_id', 'like', '%' . $this->customerSearch . '%')
                            ->orWhere('phone', 'like', '%' . $this->customerSearch . '%');
                    });
                })
                ->orderBy('name')
                ->paginate(10, ['*'], 'customerPage');
        }

        return view('livewire.subscription.subscription-manager', [
            'subscriptions' => $subscriptions,
            'filteredCustomers' => $filteredCustomers,
        ])->layout('layouts.app');
    }

    /**
     * Toggle a status filter pill. If disabling removes it from the array;
     * if enabling adds it back. At least one must remain enabled.
     */
    public function toggleFilterStatus(string $status): void
    {
        if (in_array($status, $this->filterStatuses)) {
            // Don't allow disabling all statuses
            if (count($this->filterStatuses) <= 1) {
                return;
            }
            $this->filterStatuses = array_values(array_diff($this->filterStatuses, [$status]));
        } else {
            $this->filterStatuses[] = $status;
        }
        $this->resetPage();
    }

    private function initPeriod()
    {
        $now = Carbon::now();
        $this->period_start = $now->startOfMonth()->format('Y-m-d');
        $this->period_end = $now->endOfMonth()->format('Y-m-d');
    }

    public function updatedCustomerId($value)
    {
        if ($value) {
            $customer = Customer::with('activeSubscription.package')->find($value);
            if ($customer && $customer->activeSubscription) {
                // If customer has active subscription, it might be a renewal/upgrade
                // Set period start to day after current subscription ends
                $this->period_start = $customer->activeSubscription->period_end->addDay()->format('Y-m-d');
                // Set period end to one month after new period start
                $this->period_end = Carbon::parse($this->period_start)->addMonth()->subDay()->format('Y-m-d');
                $this->package_id = $customer->activeSubscription->package_id;
                $this->service_type = $customer->activeSubscription->service_type ?? 'ppp';

                // Copy technical data from previous subscription
                $this->pppoe_username = $customer->activeSubscription->pppoe_username;
                $this->pppoe_password = $customer->activeSubscription->pppoe_password;
                $this->mac_address = $customer->activeSubscription->mac_address;
                $this->ip_address = $customer->activeSubscription->ip_address;
                $this->device_sn = $customer->activeSubscription->device_sn;
                $this->coverage_point_id = $customer->activeSubscription->coverage_point_id;
                $this->olt_id = $customer->activeSubscription->olt_id;
                $this->olt_frame = $customer->activeSubscription->olt_frame;
                $this->olt_slot = $customer->activeSubscription->olt_slot;
                $this->olt_port = $customer->activeSubscription->olt_port;
                $this->olt_onu_id = $customer->activeSubscription->olt_onu_id;
                $this->nas_id = $customer->activeSubscription->nas_id;
                $this->server_name = $customer->activeSubscription->server_name;
                $this->installation_date = $customer->activeSubscription->installation_date?->format('Y-m-d');

                // Set default price from package
                if ($customer->activeSubscription->package) {
                    $this->amount = $customer->activeSubscription->package->price;
                    $this->calculateInstallationFee(); // Check if applies
                    $this->calculateTaxAndTotal();
                }
            } else {
                // If no active subscription, load last subscription data
                $lastSubscription = Subscription::where('customer_id', $value)
                    ->latest()
                    ->first();

                if ($lastSubscription) {
                    // Copy technical data from previous subscription
                    $this->pppoe_username = $lastSubscription->pppoe_username;
                    $this->pppoe_password = $lastSubscription->pppoe_password;
                    $this->service_type = $lastSubscription->service_type ?? 'ppp';
                    $this->mac_address = $lastSubscription->mac_address;
                    $this->ip_address = $lastSubscription->ip_address;
                    $this->device_sn = $lastSubscription->device_sn;
                    $this->package_id = $lastSubscription->package_id;
                    $this->coverage_point_id = $lastSubscription->coverage_point_id;
                    $this->olt_id = $lastSubscription->olt_id;
                    $this->olt_frame = $lastSubscription->olt_frame;
                    $this->olt_slot = $lastSubscription->olt_slot;
                    $this->olt_port = $lastSubscription->olt_port;
                    $this->olt_onu_id = $lastSubscription->olt_onu_id;
                    $this->nas_id = $lastSubscription->nas_id;
                    $this->server_name = $lastSubscription->server_name;
                    $this->installation_date = $lastSubscription->installation_date?->format('Y-m-d');

                    // Update amount from package
                    if ($lastSubscription->package) {
                        $this->amount = $lastSubscription->package->price;
                        $this->calculateInstallationFee(); // Check if applies
                        $this->calculateTaxAndTotal();
                    }
                } else {
                    // Reset period and amount if no previous subscription
                    $this->initPeriod();
                    $this->amount = 0;
                    $this->calculateInstallationFee();
                    $this->calculateTaxAndTotal();
                }
            }
        }
    }

    public function updatedPackageId($value)
    {
        if ($value) {
            $package = Package::find($value);
            if ($package) {
                $basePrice = $package->price;

                // Check if proration is enabled and this is a new subscription
                if (!$this->editMode && Setting::get('billing.proration_enabled', false)) {
                    $basePrice = $this->calculateProratedAmount($package->price);
                }

                $this->amount = $basePrice;

                // Calculate installation fee if needed
                $this->calculateInstallationFee();

                $this->calculateTaxAndTotal();
            }
        }
    }

    /**
     * Calculate prorated amount based on remaining days in the period
     */
    private function calculateProratedAmount($fullPrice)
    {
        // Reset proration tracking
        $this->isProrated = false;
        $this->originalAmount = $fullPrice;
        $this->proratedDays = 0;
        $this->totalDays = 0;

        if (!$this->period_start || !$this->period_end) {
            return $fullPrice;
        }

        $periodStart = Carbon::parse($this->period_start);
        $periodEnd = Carbon::parse($this->period_end);
        $today = Carbon::now()->startOfDay();

        // Total days in the billing period
        $this->totalDays = $periodStart->diffInDays($periodEnd) + 1;

        // If start date is in the future, no proration needed
        if ($today->lte($periodStart)) {
            return $fullPrice;
        }

        // Remaining days from today until period end
        $this->proratedDays = max(0, $today->diffInDays($periodEnd) + 1);

        // Calculate prorated amount
        if ($this->proratedDays <= 0 || $this->totalDays <= 0) {
            return $fullPrice;
        }

        // Check proration threshold
        $thresholdDays = (int) Setting::get('billing.proration_threshold_days', 15);

        // Logic: 
        // If remaining days <= threshold -> Calculate Prorate
        // If remaining days > threshold -> Full Price (No Prorate)
        if ($this->proratedDays > $thresholdDays) {
            return $fullPrice;
        }

        $proratedAmount = ceil(($fullPrice / $this->totalDays) * $this->proratedDays);

        // Mark as prorated
        $this->isProrated = true;

        return $proratedAmount;
    }

    public function updatedAmount()
    {
        // If user manually changes amount, clear proration flag
        $this->isProrated = false;
        $this->calculateTaxAndTotal();
    }

    public function updatedInstallationFee()
    {
        $this->calculateTaxAndTotal();
    }

    public function updatedDiscount()
    {
        $this->calculateTaxAndTotal();
    }

    private function calculateTaxAndTotal()
    {
        $amount = floatval($this->amount);
        $discount = floatval($this->discount);
        $installation = floatval($this->installation_fee);

        // Tax Base = Amount - Discount + Installation Fee
        $subtotal = max(0, $amount - $discount);
        $taxBase = $subtotal + $installation;

        $taxPercentage = floatval(Setting::get('billing.tax_percentage', 0));

        // Tax = Tax Base * Rate
        $this->tax = ceil($taxBase * ($taxPercentage / 100));

        // Total = Tax Base + Tax
        $this->total = ceil($taxBase + $this->tax);
    }

    public function updatedTax()
    {
        // Recalculate total when tax is manually changed
        $amount = floatval($this->amount);
        $discount = floatval($this->discount);
        $installation = floatval($this->installation_fee);

        $subtotal = max(0, $amount - $discount);

        // If tax is manual, total is just (Amount - Discount + Installation) + Manual Tax
        $this->total = ceil($subtotal + $installation + floatval($this->tax));
    }

    public function openCustomerModal()
    {
        $this->customerSearch = '';
        $this->resetPage('customerPage');
        $this->isCustomerModalOpen = true;
        $this->dispatch('open-customer-modal');
    }

    public function closeCustomerModal()
    {
        $this->isCustomerModalOpen = false;
        $this->dispatch('close-customer-picker');
        $this->customerSearch = '';
    }

    public function selectCustomer($customerId)
    {
        $customer = Customer::find($customerId);
        if ($customer) {
            $this->customer_id = $customer->id;
            $this->customer_number = $customer->customer_id;
            $this->customer_name = $customer->name;
            $this->customer_phone = $customer->phone;
            $this->customer_address = $customer->address;
        }
        $this->closeCustomerModal();
    }

    public function updatingCustomerSearch()
    {
        $this->resetPage('customerPage');
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->resetForm();
        $this->editMode = false;
        $this->showModal = true;

        // Initialize subscription type based on settings
        if ($this->allowedSubscriptionType !== 'both') {
            $this->subscription_type = $this->allowedSubscriptionType;
        } else {
            $this->subscription_type = 'prepaid'; // Default for both
        }

        $this->initPeriod();
        $this->dispatch('open-modal');
    }

    public function editSubscription($id)
    {
        $this->resetValidation();
        $this->editMode = true;
        $this->subscription_id = $id;

        $subscription = Subscription::with(['customer', 'package'])->findOrFail($id);

        if ($subscription->status === 'pending') {
            $this->dispatch('toast', type: 'error', message: 'Langganan berstatus "Menunggu" tidak dapat diedit sampai pembayaran dikonfirmasi.');
            return;
        }

        $this->customer_id = $subscription->customer_id;
        $this->customer_number = $subscription->customer->customer_id ?? '';
        $this->customer_name = $subscription->customer->name ?? '';
        $this->customer_phone = $subscription->customer->phone ?? '';
        $this->customer_address = $subscription->customer->address ?? '';
        $this->package_id = $subscription->package_id;
        $this->original_package_id = $subscription->package_id;
        $this->original_package_name = $subscription->package->name ?? '';
        $this->original_package_price = $subscription->package->price ?? 0;
        $this->period_start = $subscription->period_start->format('Y-m-d');
        $this->period_end = $subscription->period_end->format('Y-m-d');
        $this->installation_date = $subscription->installation_date?->format('Y-m-d');
        $this->service_type = $subscription->service_type ?? 'ppp';
        $this->mac_address = $subscription->mac_address;
        $this->ip_address = $subscription->ip_address;
        $this->notes = $subscription->notes;

        // Technical data & missing fields
        $this->pppoe_username = $subscription->pppoe_username;
        $this->pppoe_password = $subscription->pppoe_password;
        $this->device_sn = $subscription->device_sn;
        $this->coverage_point_id = $subscription->coverage_point_id;
        $this->olt_id = $subscription->olt_id;
        $this->olt_frame = $subscription->olt_frame;
        $this->olt_slot = $subscription->olt_slot;
        $this->olt_port = $subscription->olt_port;
        $this->olt_onu_id = $subscription->olt_onu_id;
        $this->nas_id = $subscription->nas_id;
        $this->server_name = $subscription->server_name;
        $this->subscription_type = $subscription->subscription_type ?? 'prepaid';
        $this->status = $subscription->status;

        $this->showModal = true;
        $this->dispatch('open-modal');
    }

    public function save()
    {
        $this->validate();

        // 1. Check for duplicate active subscription with same package
        if (!$this->editMode) {
            $exists = Subscription::where('customer_id', $this->customer_id)
                ->where('package_id', $this->package_id)
                ->where('status', 'active')
                ->exists();

            if ($exists) {
                $this->dispatch('toast', type: 'error', message: 'Pelanggan sudah memiliki langganan aktif untuk paket ini. Mohon periksa kembali.');
                return;
            }
        }

        $data = [
            'customer_id' => $this->customer_id,
            'package_id' => $this->package_id,
            'period_start' => $this->period_start,
            'period_end' => $this->period_end,
            'installation_date' => $this->installation_date,
            'status' => $this->editMode ? $this->status : 'pending', // Default to pending for new
            'notes' => $this->notes,
            'pppoe_username' => $this->pppoe_username,
            'pppoe_password' => $this->pppoe_password,
            'device_sn' => $this->device_sn,
            'coverage_point_id' => $this->coverage_point_id,
            'olt_id' => $this->olt_id,
            'olt_frame' => $this->olt_frame,
            'olt_slot' => $this->olt_slot,
            'olt_port' => $this->olt_port,
            'olt_onu_id' => $this->olt_onu_id,
            'subscription_type' => $this->subscription_type,
            'nas_id' => $this->nas_id ?: null,
            'server_name' => $this->server_name,
            'service_type' => $this->service_type,
            'mac_address' => $this->mac_address,
            'ip_address' => $this->ip_address,
        ];

        if ($this->editMode) {
            $subscription = Subscription::find($this->subscription_id);
            $subscription->update($data);
            $message = 'Data langganan berhasil diperbarui.';
        } else {
            $subscription = Subscription::create($data);

            // Auto-generate invoice for new subscription
            $this->createInvoiceForSubscription($subscription);

            $message = 'Langganan baru berhasil ditambahkan.';
        }

        // Sync to Radius for active subscriptions before resetting form
        if ($this->editMode && $subscription->status === 'active') {
            app(RadiusSyncService::class)->sync($subscription);
        }

        $this->closeModal();
        $this->dispatch('toast', type: 'success', message: $message);
    }

    public function confirmDelete($id)
    {
        $this->triggerConfirm('deleteSubscription', $id, 'Hapus Langganan?', 'Apakah Anda yakin ingin menghapus data langganan ini? Data yang dihapus tidak dapat dikembalikan.');
    }

    #[On('deleteConfirmed')]
    public function deleteSubscription($id)
    {
        $subscription = Subscription::find($id);
        if ($subscription) {
            // Remove from Radius first
            if ($subscription->pppoe_username) {
                app(RadiusSyncService::class)->remove($subscription);
            }

            $subscription->delete();
            $this->dispatch('toast', type: 'success', message: 'Langganan berhasil dihapus.');
        }
    }

    public function manualSync($id)
    {
        $subscription = Subscription::find($id);
        if ($subscription && $subscription->pppoe_username) {
            try {
                app(RadiusSyncService::class)->sync($subscription);
                $this->dispatch('toast', type: 'success', message: 'Sinkronisasi Radius berhasil.');
            } catch (\Exception $e) {
                $this->dispatch('toast', type: 'error', message: 'Gagal sinkronisasi: ' . $e->getMessage());
            }
        } else {
            $this->dispatch('toast', type: 'warning', message: 'Data PPPoE tidak lengkap.');
        }
    }


    public function toggleStatus($id)
    {
        $subscription = Subscription::find($id);
        if (!$subscription) {
            return;
        }

        $currentStatus = $subscription->status;
        $newStatus = null;
        $message = '';

        if ($currentStatus === 'active') {
            $newStatus = 'suspended';
            $message = 'Langganan berhasil diisolir (Suspend).';
        } elseif ($currentStatus === 'suspended') {
            $newStatus = 'active';
            $message = 'Langganan berhasil diaktifkan kembali.';
        } elseif ($currentStatus === 'unpaid') {
            // Allow suspend for unpaid users too
            $newStatus = 'suspended';
            $message = 'Langganan berhasil diisolir (Suspend).';
        } else {
            $this->dispatch('toast', type: 'warning', message: 'Status saat ini tidak dapat diubah secara manual.');
            return;
        }

        if ($newStatus) {
            $subscription->update(['status' => $newStatus]);

            // The SubscriptionObserver will automatically handle:
            // 1. SyncToRadiusJob (Radius DB update: Group changes to/from ISOLIR)
            // 2. KickUserJob (Mikrotik API: Kick user to apply new profile)

            $this->dispatch('toast', type: 'success', message: $message);
        }
    }

    public function terminateSubscription($id)
    {
        $subscription = Subscription::find($id);
        if (!$subscription)
            return;

        // User Rule: Fitur ini tidak berlaku pada pelanggan baru (misalnya yang belum instalasi/active)
        // However, we'll allow it for any saved subscription but maybe check if it's truly existing
        if (!$this->editMode) {
            $this->dispatch('toast', type: 'warning', message: 'Fitur berhenti berlangganan hanya berlaku untuk pelanggan lama.');
            return;
        }

        $subscription->update(['status' => 'cancelled']); // Using 'cancelled' as termination status

        // SubscriptionObserver will handle:
        // 1. Creating History (type: termination)
        // 2. SyncToRadiusJob (removal/update)
        // 3. KickUserJob (Mikrotik API)

        $this->closeModal();
        $this->dispatch('toast', type: 'success', message: 'Pelanggan berhasil berhenti berlangganan.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->dispatch('close-modal');
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'subscription_id',
            'customer_id',
            'customer_phone',
            'customer_address',
            'original_package_id',
            'original_package_name',
            'original_package_price',
            'package_id',
            'period_start',
            'period_end',
            'installation_date',
            'amount',
            'installation_fee',
            'discount',
            'total',
            'status',
            'paid_at',
            'payment_method',
            'notes',
            'pppoe_username',
            'pppoe_password',
            'mac_address',
            'ip_address',
            'device_sn',
            'coverage_point_id',
            'olt_id',
            'olt_frame',
            'olt_slot',
            'olt_port',
            'olt_onu_id',
            'nas_id',
            'server_name',
            'subscription_type',
        ]);
        $this->status = 'pending';
        $this->amount = 0;
        $this->installation_fee = 0;
        $this->discount = 0;
        $this->total = 0;
    }

    private function calculateInstallationFee()
    {
        if ($this->customer_id && $this->package_id && !$this->editMode) {
            $isFirstSubscription = Subscription::where('customer_id', $this->customer_id)
                ->where('id', '!=', $this->subscription_id)
                ->count() === 0;

            if ($isFirstSubscription) {
                $package = Package::find($this->package_id);
                $this->installation_fee = $package ? $package->installation_fee : 0;
            } else {
                $this->installation_fee = 0;
            }
        }
    }

    /**
     * Create invoice for a new subscription
     */
    private function createInvoiceForSubscription(Subscription $subscription)
    {
        // Use form values for the initial invoice
        $amount = $this->amount;
        $discount = $this->discount ?? 0;
        $tax = $this->tax ?? 0;
        $installationFee = $this->installation_fee ?? 0;

        // Ensure total calculation is consistent
        $total = ($amount + $tax + $installationFee) - $discount;

        // Get Grace Period from settings
        $gracePeriodDays = (int) \App\Models\Setting::get('billing.grace_period_days', 7);
        $issueDate = now();
        $dueDate = $issueDate->copy()->addDays($gracePeriodDays);

        Invoice::create([
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'subscription_id' => $subscription->id,
            'customer_id' => $subscription->customer_id,
            'issue_date' => $issueDate,
            'due_date' => $dueDate,
            'subtotal' => $amount,
            'tax' => $tax,
            'installation_fee' => $installationFee,
            'discount' => $discount,
            'total' => $total,
            'status' => 'unpaid', // Always starts as unpaid
        ]);
    }


}
