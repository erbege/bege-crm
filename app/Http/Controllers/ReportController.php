<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\HotspotVoucher;

class ReportController extends Controller
{
    public function index()
    {
        // 1. Customer Stats
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::active()->count();
        $inactiveCustomers = Customer::inactive()->count();
        $newCustomersThisMonth = Customer::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // 2. Financial Stats (Current Month)
        // Subscriptions (Paid Invoices)
        $subscriptionIncome = Invoice::where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('total');

        // Hotspot Revenue Estimation
        // Used Vouchers used this month
        // logic: get vouchers used this month -> load profile -> sum profile price
        $hotspotIncome = HotspotVoucher::with('profile')
            ->whereNotNull('used_at')
            ->whereMonth('used_at', now()->month)
            ->whereYear('used_at', now()->year)
            ->get()
            ->sum(function ($voucher) {
                return $voucher->profile->price ?? 0;
            });

        $totalIncome = $subscriptionIncome + $hotspotIncome;

        // Charts Data (Last 6 Months Income)
        $chartLabels = [];
        $chartSubscription = [];
        $chartHotspot = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->month;
            $year = $date->year;
            $chartLabels[] = $date->format('F Y');

            // Subscription
            $chartSubscription[] = Invoice::where('status', 'paid')
                ->whereMonth('paid_at', $month)
                ->whereYear('paid_at', $year)
                ->sum('total');

            // Hotspot
            $chartHotspot[] = HotspotVoucher::with('profile')
                ->whereNotNull('used_at')
                ->whereMonth('used_at', $month)
                ->whereYear('used_at', $year)
                ->get()
                ->sum(function ($view) {
                    return $view->profile->price ?? 0;
                });
        }

        return view('reports.index', compact(
            'totalCustomers',
            'activeCustomers',
            'inactiveCustomers',
            'newCustomersThisMonth',
            'subscriptionIncome',
            'hotspotIncome',
            'totalIncome',
            'chartLabels',
            'chartSubscription',
            'chartHotspot'
        ));
    }
}
