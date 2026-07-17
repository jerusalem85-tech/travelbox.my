<?php

namespace App\Livewire\Reports;

use App\Models\Trip;
use App\Models\Invoice;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class SalesReport extends Component
{
    public string $period = 'monthly';
    public array $chartData = [];

    public function mount(): void
    {
        $this->loadData();
    }

    public function updatedPeriod(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $data = [];
        $format = $this->period === 'yearly' ? 'Y' : 'Y-m';
        $start = $this->period === 'yearly' ? now()->subYears(3) : now()->subMonths(11);

        $trips = Trip::selectRaw("DATE_FORMAT(created_at, '$format') as period, COUNT(*) as total, SUM(total_selling_price) as revenue, SUM(total_cost_price) as cost")
            ->where('created_at', '>=', $start)
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        $invoices = Invoice::selectRaw("DATE_FORMAT(issue_date, '$format') as period, SUM(total) as invoiced")
            ->where('issue_date', '>=', $start)
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        $periods = collect();
        $date = clone $start;
        $end = now();
        while ($date <= $end) {
            $key = $date->format($format);
            $periods->push($key);
            $this->period === 'yearly' ? $date->addYear() : $date->addMonth();
        }

        foreach ($periods as $p) {
            $t = $trips->firstWhere('period', $p);
            $inv = $invoices->firstWhere('period', $p);
            $data[] = [
                'period' => $p,
                'bookings' => $t->total ?? 0,
                'revenue' => (float) ($t->revenue ?? 0),
                'cost' => (float) ($t->cost ?? 0),
                'invoiced' => (float) ($inv->invoiced ?? 0),
            ];
        }

        $this->chartData = $data;
    }

    public function render()
    {
        $summary = [
            'total_bookings' => Trip::count(),
            'total_revenue' => Trip::sum('total_selling_price'),
            'total_cost' => Trip::sum('total_cost_price'),
            'total_invoiced' => Invoice::sum('total'),
            'pending_invoices' => Invoice::where('status', 'pending')->count(),
        ];

        return view('livewire.reports.sales-report', compact('summary'));
    }
}
