<?php

namespace App\Livewire\Trips;

use App\Models\Trip;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class TripList extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';
    public string $filterStatus = '';
    public string $filterType = '';
    public string $filterDateFrom = '';
    public string $filterDateTo = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }
    public function updatingFilterType(): void { $this->resetPage(); }
    public function updatingFilterDateFrom(): void { $this->resetPage(); }
    public function updatingFilterDateTo(): void { $this->resetPage(); }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function exportCsv()
    {
        $trips = Trip::with('customer')->orderBy($this->sortField, $this->sortDirection)->get();
        $csv = fopen('php://temp', 'r+');
        fputcsv($csv, ['Trip Number', 'Name', 'Customer', 'Destination', 'Status', 'Type', 'Start Date', 'End Date', 'Selling Price', 'Cost Price', 'Currency']);
        foreach ($trips as $t) {
            fputcsv($csv, [
                $t->trip_number, $t->name, $t->customer?->full_name, $t->destination,
                $t->status, $t->type, $t->start_date?->format('Y-m-d'), $t->end_date?->format('Y-m-d'),
                $t->total_selling_price, $t->total_cost_price, $t->currency,
            ]);
        }
        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);
        return response()->streamDownload(fn() => print($content), 'trips_export_'.now()->format('Ymd').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function render()
    {
        $query = Trip::with('customer');

        if ($this->search) {
            $s = '%' . str_replace(['%', '_'], ['\%', '\_'], $this->search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('trip_number', 'like', $s)
                  ->orWhere('name', 'like', $s)
                  ->orWhere('destination', 'like', $s)
                  ->orWhereHas('customer', fn($cq) => $cq->where('first_name', 'like', $s)
                      ->orWhere('last_name', 'like', $s)
                      ->orWhere('company_name', 'like', $s));
            });
        }

        if ($this->filterStatus) $query->where('status', $this->filterStatus);
        if ($this->filterType) $query->where('type', $this->filterType);
        if ($this->filterDateFrom) $query->whereDate('start_date', '>=', $this->filterDateFrom);
        if ($this->filterDateTo) $query->whereDate('start_date', '<=', $this->filterDateTo);

        $query->orderBy($this->sortField, $this->sortDirection);

        return view('livewire.trips.trip-list', [
            'trips' => $query->paginate(10),
        ]);
    }
}
