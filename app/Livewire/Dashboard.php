<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Subscription;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Livewire\Attributes\Lazy;
use Livewire\Attributes\Layout;

#[Lazy]
#[Layout('layouts.app')]
class Dashboard extends Component
{
    public $customerStats = [];
    public $invoiceStats = [];
    public $revenueStats = [];
    public $hotspotStats = [];
    public $recentInvoices = [];
    public $revenueChartData = [];

    public function mount()
    {
        $this->loadStats();
    }

    public function placeholder()
    {
        return view('livewire.dashboard-placeholder');
    }

    public function loadStats()
    {
        // Subscription Statistics
        $totalSubscriptions = Subscription::count();
        $activeSubscriptions = Subscription::whereIn('status', ['active', 'paid'])->count();
        $suspendedSubscriptions = Subscription::where('status', 'suspended')->count();

        $newSubscriptionsThisMonth = Subscription::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $this->customerStats = [
            'total' => $totalSubscriptions,
            'active' => $activeSubscriptions,
            'suspended' => $suspendedSubscriptions,
            'new_this_month' => $newSubscriptionsThisMonth,
        ];

        // Invoice Statistics
        $unpaidInvoices = Invoice::where('status', 'unpaid')->count();
        $paidInvoicesThisMonth = Invoice::where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->count();
        $overdueInvoices = Invoice::where('status', 'unpaid')
            ->where('due_date', '<', now())
            ->count();

        $this->invoiceStats = [
            'unpaid' => $unpaidInvoices,
            'paid_this_month' => $paidInvoicesThisMonth,
            'overdue' => $overdueInvoices,
        ];

        // Revenue Statistics
        $invoiceRevenueUsingThisMonth = Invoice::where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('total');

        $hotspotRevenueThisMonth = \App\Models\HotspotVoucher::whereNotNull('used_at')
            ->whereMonth('used_at', now()->month)
            ->whereYear('used_at', now()->year)
            ->join('hotspot_profiles', 'hotspot_vouchers.hotspot_profile_id', '=', 'hotspot_profiles.id')
            ->sum('hotspot_profiles.price');

        $revenueThisMonth = $invoiceRevenueUsingThisMonth + $hotspotRevenueThisMonth;

        // Projected revenue (all active subscriptions based on package price)
        $projectedRevenue = Subscription::where('status', '!=', 'cancelled')
            ->join('packages', 'subscriptions.package_id', '=', 'packages.id')
            ->sum('packages.price');

        $this->revenueStats = [
            'this_month' => $revenueThisMonth,
            'projected' => $projectedRevenue,
        ];

        // Generate Daily Revenue Chart Data (using SQL aggregation instead of PHP loops)
        $daysInMonth = now()->daysInMonth;
        $dailyRevenue = array_fill(1, $daysInMonth, 0);

        // Aggregate invoice revenue by day — single query instead of fetching all records
        $dailyInvoiceRevenue = Invoice::where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->selectRaw('DAY(paid_at) as day, SUM(total) as daily_total')
            ->groupByRaw('DAY(paid_at)')
            ->pluck('daily_total', 'day');

        foreach ($dailyInvoiceRevenue as $day => $total) {
            $dailyRevenue[$day] += $total;
        }

        // Aggregate hotspot revenue by day
        $dailyHotspotRevenue = \App\Models\HotspotVoucher::whereNotNull('used_at')
            ->whereMonth('used_at', now()->month)
            ->whereYear('used_at', now()->year)
            ->join('hotspot_profiles', 'hotspot_vouchers.hotspot_profile_id', '=', 'hotspot_profiles.id')
            ->selectRaw('DAY(hotspot_vouchers.used_at) as day, SUM(hotspot_profiles.price) as daily_total')
            ->groupByRaw('DAY(hotspot_vouchers.used_at)')
            ->pluck('daily_total', 'day');

        foreach ($dailyHotspotRevenue as $day => $total) {
            $dailyRevenue[$day] += $total;
        }

        $this->revenueChartData = [
            'labels' => range(1, $daysInMonth),
            'data' => array_values($dailyRevenue),
        ];

        // Hotspot Statistics
        $vouchersGeneratedThisMonth = \App\Models\HotspotVoucher::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $activeVouchers = \App\Models\HotspotVoucher::where('status', 'active')->count();
        $usedVouchersThisMonth = \App\Models\HotspotVoucher::whereNotNull('used_at')
            ->whereMonth('used_at', now()->month)
            ->whereYear('used_at', now()->year)
            ->count();

        $this->hotspotStats = [
            'generated_this_month' => $vouchersGeneratedThisMonth,
            'active' => $activeVouchers,
            'used_this_month' => $usedVouchersThisMonth,
        ];

        // Recent Data
        $this->recentInvoices = Invoice::with(['subscription.customer'])
            ->latest()
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
