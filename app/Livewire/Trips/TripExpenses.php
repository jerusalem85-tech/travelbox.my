<?php

namespace App\Livewire\Trips;

use App\Models\Trip;
use App\Models\TripExpense;
use Livewire\Component;

class TripExpenses extends Component
{
    public Trip $trip;
    public bool $showForm = false;
    public ?string $editingId = null;
    public string $category = 'transport';
    public string $description = '';
    public string $amount = '0';
    public string $currency = 'USD';
    public string $expense_date = '';

    public function mount(Trip $trip): void
    {
        $this->trip = $trip->load('expenses');
    }

    public function openForm(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(string $id): void
    {
        $e = TripExpense::findOrFail($id);
        $this->editingId = $id;
        $this->category = $e->category;
        $this->description = $e->description;
        $this->amount = (string) $e->amount;
        $this->currency = $e->currency;
        $this->expense_date = $e->expense_date?->format('Y-m-d') ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'category' => 'required|string',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'expense_date' => 'nullable|date',
        ]);

        $data = [
            'category' => $this->category,
            'description' => $this->description,
            'amount' => (float) $this->amount,
            'currency' => $this->currency,
            'expense_date' => $this->expense_date ?: null,
        ];

        if ($this->editingId) {
            TripExpense::findOrFail($this->editingId)->update($data);
            $this->trip->logTimeline('expense_edited', "Updated expense: {$this->description}");
        } else {
            $data['trip_id'] = $this->trip->id;
            TripExpense::create($data);
            $this->trip->logTimeline('expense_added', "Added expense: {$this->description} ({$this->amount})");
        }

        $this->resetForm();
        $this->trip->load('expenses');
    }

    public function delete(string $id): void
    {
        TripExpense::findOrFail($id)->delete();
        $this->trip->load('expenses');
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->category = 'transport';
        $this->description = '';
        $this->amount = '0';
        $this->currency = 'USD';
        $this->expense_date = '';
        $this->showForm = false;
    }

    public function render()
    {
        return view('livewire.trips.trip-expenses');
    }
}
