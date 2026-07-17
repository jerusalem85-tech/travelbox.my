<?php

namespace App\Livewire\Trips;

use App\Models\Trip;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class TripPipeline extends Component
{
    public array $columns = [];
    public array $statuses = ['enquiry', 'confirmed', 'in_progress', 'completed', 'cancelled'];
    public string $search = '';

    public function mount(): void
    {
        $this->loadTrips();
    }

    public function loadTrips(): void
    {
        $this->columns = [];
        $query = Trip::with('customer')->orderBy('updated_at', 'desc');
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('trip_number', 'like', "%{$this->search}%")
                  ->orWhere('destination', 'like', "%{$this->search}%");
            });
        }
        $trips = $query->get();

        foreach ($this->statuses as $status) {
            $this->columns[$status] = $trips->where('status', $status)->values()->toArray();
        }
    }

    public function updatedSearch(): void
    {
        $this->loadTrips();
    }

    public function moveTo(string $id, string $status): void
    {
        Trip::findOrFail($id)->update(['status' => $status]);
        $this->loadTrips();
    }

    public function render()
    {
        return view('livewire.trips.trip-pipeline');
    }
}
