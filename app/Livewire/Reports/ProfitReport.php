<?php

namespace App\Livewire\Reports;

use App\Models\Trip;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ProfitReport extends Component
{
    public array $trips = [];

    public function mount(): void
    {
        $this->trips = Trip::select(['id', 'name', 'trip_number', 'destination', 'status', 'total_selling_price', 'total_cost_price', 'currency', 'start_date'])
            ->where('total_selling_price', '>', 0)
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(fn($t) => [
                'name' => $t->name,
                'trip_number' => $t->trip_number,
                'destination' => $t->destination,
                'status' => $t->status,
                'selling_price' => (float) $t->total_selling_price,
                'cost_price' => (float) $t->total_cost_price,
                'profit' => $t->profit,
                'margin' => $t->profit_margin,
                'currency' => $t->currency,
                'start_date' => $t->start_date?->format('M d, Y'),
            ])->toArray();
    }

    public function render()
    {
        $summary = [
            'total_selling' => collect($this->trips)->sum('selling_price'),
            'total_cost' => collect($this->trips)->sum('cost_price'),
            'total_profit' => collect($this->trips)->sum('profit'),
            'avg_margin' => count($this->trips) > 0 ? collect($this->trips)->avg('margin') : 0,
            'profitable' => collect($this->trips)->where('profit', '>', 0)->count(),
            'losses' => collect($this->trips)->where('profit', '<=', 0)->count(),
        ];

        return view('livewire.reports.profit-report', compact('summary'));
    }
}
