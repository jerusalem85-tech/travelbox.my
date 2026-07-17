<?php

namespace App\Livewire\Reports;

use App\Models\Invoice;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class TaxSummaryReport extends Component
{
    public string $dateFrom = '';
    public string $dateTo = '';

    public function mount(): void
    {
        $this->dateFrom = now()->startOfYear()->format('Y-m-d');
        $this->dateTo = now()->endOfYear()->format('Y-m-d');
    }

    public function render()
    {
        $invoices = Invoice::whereBetween('issue_date', [$this->dateFrom, $this->dateTo])
            ->where('status', '!=', 'cancelled')
            ->get();

        $months = $invoices->groupBy(fn($i) => $i->issue_date->format('Y-m'))
            ->map(function ($group, $key) {
                $subtotal = (float) $group->sum('subtotal');
                $tax = (float) $group->sum('tax');
                $total = (float) $group->sum('total');
                return [
                    'month' => \Carbon\Carbon::parse($key . '-01')->format('M Y'),
                    'count' => $group->count(),
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'total' => $total,
                ];
            })
            ->values()
            ->sortBy('month')
            ->values()
            ->all();

        $grandSubtotal = collect($months)->sum('subtotal');
        $grandTax = collect($months)->sum('tax');
        $grandTotal = collect($months)->sum('total');

        return view('livewire.reports.tax-summary-report', compact('months', 'grandSubtotal', 'grandTax', 'grandTotal'));
    }
}
