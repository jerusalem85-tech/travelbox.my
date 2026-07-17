<?php

namespace App\Livewire;

use App\Models\Trip;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\TripExpense;
use App\Models\TripTimelineEvent;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class AdminDashboard extends Component
{
    public int $totalTrips = 0;
    public int $totalCustomers = 0;
    public int $totalSuppliers = 0;
    public int $pendingInvoices = 0;
    public int $activeBookings = 0;
    public float $monthlyRevenue = 0;
    public float $monthlyCost = 0;
    public float $monthlyProfit = 0;
    public float $outstandingBalance = 0;
    public int $todayDepartures = 0;
    public float $supplierPaymentsDue = 0;
    public float $customerBalanceOwing = 0;
    public int $pendingExpenses = 0;
    public array $statusCounts = [];
    public array $upcomingTrips = [];
    public array $recentActivity = [];
    public array $monthlyData = [];
    public array $topCustomers = [];
    public array $serviceMix = [];

    public function mount(): void
    {
        $now = now();

        $this->totalTrips = Trip::count();
        $this->totalCustomers = Customer::count();
        $this->totalSuppliers = Supplier::count();
        $this->pendingInvoices = Invoice::whereIn('status', ['pending', 'overdue'])->count();
        $this->activeBookings = Trip::whereIn('status', ['confirmed', 'in_progress'])->count();
        $this->monthlyRevenue = Invoice::whereMonth('issue_date', $now->month)
            ->whereYear('issue_date', $now->year)
            ->where('type', 'sales')->sum('total');
        $this->monthlyCost = Trip::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->sum('total_cost_price');
        $this->monthlyProfit = $this->monthlyRevenue - $this->monthlyCost;
        $this->outstandingBalance = Invoice::whereIn('status', ['pending', 'overdue'])->sum('total')
            - Payment::whereIn('category', ['customer_payment', 'customer_deposit'])
                ->where('status', 'completed')->sum('amount');
        if ($this->outstandingBalance < 0) $this->outstandingBalance = 0;

        $this->todayDepartures = Trip::whereDate('start_date', $now->toDateString())->count();

        $this->supplierPaymentsDue = TripExpense::where('status', 'pending')
            ->whereHas('trip', fn($q) => $q->whereNot('status', 'cancelled'))
            ->sum('amount');

        $this->customerBalanceOwing = Invoice::whereIn('status', ['pending', 'overdue'])
            ->where('type', 'sales')
            ->selectRaw('SUM(total) - COALESCE((SELECT SUM(amount) FROM payments WHERE payments.invoice_id = invoices.id AND status = "completed"), 0) as balance_due')
            ->pluck('balance_due')->sum();
        if ($this->customerBalanceOwing < 0) $this->customerBalanceOwing = 0;

        $this->pendingExpenses = TripExpense::where('status', 'pending')->count();

        $this->statusCounts = Trip::selectRaw('status, count(*) as count')
            ->groupBy('status')->pluck('count', 'status')->toArray();

        $this->upcomingTrips = Trip::with('customer')
            ->whereIn('status', ['enquiry', 'confirmed', 'in_progress'])
            ->whereNotNull('start_date')
            ->orderBy('start_date')
            ->take(6)->get()->toArray();

        $this->recentActivity = TripTimelineEvent::with('trip')
            ->latest()->take(8)->get()->toArray();

        $since = $now->copy()->subMonths(6)->startOfMonth();
        $invoices = Invoice::where('type', 'sales')
            ->where('issue_date', '>=', $since)
            ->get(['issue_date', 'total']);

        $monthlyTotals = [];
        foreach ($invoices as $inv) {
            $key = $inv->issue_date?->format('Y-m') ?? $inv->created_at?->format('Y-m');
            if (!$key) continue;
            $monthlyTotals[$key] = ($monthlyTotals[$key] ?? 0) + (float) $inv->total;
        }

        for ($i = 5; $i >= 0; $i--) {
            $m = $now->copy()->subMonths($i);
            $key = $m->format('Y-m');
            $rev = (float) ($monthlyTotals[$key] ?? 0);
            $this->monthlyData[] = ['month' => $m->format('M'), 'revenue' => $rev];
        }

        $this->topCustomers = Customer::withSum('invoices as total_revenue', 'total')
            ->whereHas('invoices')
            ->orderByDesc('total_revenue')
            ->take(5)->get()->toArray();

        $this->serviceMix = [
            'Flights'    => \App\Models\FlightSegment::count(),
            'Hotels'     => \App\Models\HotelBooking::count(),
            'Transfers'  => \App\Models\TransferBooking::count(),
            'Visas'      => \App\Models\VisaApplication::count(),
            'Insurance'  => \App\Models\InsurancePolicy::count(),
            'Activities' => \App\Models\Activity::count(),
            'Cruises'    => \App\Models\CruiseBooking::count(),
            'Trains'     => \App\Models\TrainBooking::count(),
            'Cars'       => \App\Models\CarRental::count(),
            'Packages'   => \App\Models\PackageBooking::count(),
            'Other'      => \App\Models\OtherService::count(),
        ];
    }

    public function render()
    {
        return view('livewire.admin-dashboard');
    }
}
