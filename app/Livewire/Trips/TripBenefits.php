<?php

namespace App\Livewire\Trips;

use App\Models\Trip;
use App\Models\TripBenefit;
use Livewire\Component;

class TripBenefits extends Component
{
    public Trip $trip;

    public bool $showForm = false;
    public ?string $editingId = null;

    public string $type = '';
    public string $description = '';
    public string $provider = '';
    public ?string $cost = null;
    public ?string $selling_price = null;
    public string $currency = '';
    public string $notes = '';

    public function mount(Trip $trip): void
    {
        $this->trip = $trip->load('benefits');
    }

    public function openForm(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(string $id): void
    {
        $b = TripBenefit::findOrFail($id);
        $this->editingId = $id;
        $this->type = $b->type;
        $this->description = $b->description;
        $this->provider = $b->provider ?? '';
        $this->cost = $b->cost ? (string) $b->cost : null;
        $this->selling_price = $b->selling_price ? (string) $b->selling_price : null;
        $this->currency = $b->currency ?? '';
        $this->notes = $b->notes ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'type' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'provider' => 'nullable|string|max:255',
            'cost' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'notes' => 'nullable|string',
        ]);

        $data = [
            'type' => $this->type,
            'description' => $this->description,
            'provider' => $this->provider ?: null,
            'cost' => $this->cost !== null && $this->cost !== '' ? $this->cost : null,
            'selling_price' => $this->selling_price !== null && $this->selling_price !== '' ? $this->selling_price : null,
            'currency' => $this->currency ?: null,
            'notes' => $this->notes ?: null,
        ];

        if ($this->editingId) {
            $b = TripBenefit::findOrFail($this->editingId);
            $b->update($data);
        } else {
            $this->trip->benefits()->create($data);
        }

        $this->resetForm();
        $this->trip->load('benefits');
    }

    public function remove(string $id): void
    {
        TripBenefit::findOrFail($id)->delete();
        $this->trip->load('benefits');
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->type = '';
        $this->description = '';
        $this->provider = '';
        $this->cost = null;
        $this->selling_price = null;
        $this->currency = '';
        $this->notes = '';
    }

    public function render()
    {
        return view('livewire.trips.trip-benefits');
    }
}
