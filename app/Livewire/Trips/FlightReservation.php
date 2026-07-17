<?php

namespace App\Livewire\Trips;

use App\Models\Trip;
use App\Models\FlightSegment;
use App\Models\Supplier;
use Livewire\Component;
use Illuminate\Support\Str;

class FlightReservation extends Component
{
    public Trip $trip;
    public bool $editing = false;
    public ?FlightSegment $editingSegment = null;

    public string $type = 'departure';
    public ?string $selected_segment_id = null;
    public ?string $supplier_id = null;
    public string $airline = '';
    public string $flight_number = '';
    public string $booking_reference = '';
    public string $ticket_number = '';
    public string $class = 'economy';
    public string $cabin = '';
    public string $fare_basis = '';
    public string $departure_airport = '';
    public string $arrival_airport = '';
    public string $departure_terminal = '';
    public string $arrival_terminal = '';
    public ?string $departure_datetime = null;
    public ?string $arrival_datetime = null;
    public string $baggage = '';
    public string $seat = '';
    public string $meal = '';
    public string $status = 'confirmed';
    public string $cost_price = '0';
    public string $selling_price = '0';
    public string $currency = 'USD';
    public string $notes = '';

    protected function rules(): array
    {
        return [
            'type' => 'required|string',
            'airline' => 'required|string|max:255',
            'flight_number' => 'required|string|max:50',
            'booking_reference' => 'nullable|string|max:50',
            'ticket_number' => 'nullable|string|max:50',
            'class' => 'nullable|string|max:50',
            'cabin' => 'nullable|string|max:50',
            'fare_basis' => 'nullable|string|max:50',
            'departure_airport' => 'required|string|max:10',
            'arrival_airport' => 'required|string|max:10',
            'departure_terminal' => 'nullable|string|max:20',
            'arrival_terminal' => 'nullable|string|max:20',
            'departure_datetime' => 'required|date',
            'arrival_datetime' => 'required|date|after:departure_datetime',
            'baggage' => 'nullable|string|max:50',
            'seat' => 'nullable|string|max:20',
            'meal' => 'nullable|string|max:100',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'status' => 'required|string',
            'cost_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'notes' => 'nullable|string',
        ];
    }

    public function mount(Trip $trip): void
    {
        $this->trip = $trip->load('flightSegments.supplier');
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function create(): void
    {
        $this->resetForm();
        $this->editing = true;
        $this->editingSegment = null;
        $this->dispatch('open-modal', 'flight-form');
    }

    public function edit(FlightSegment $segment): void
    {
        $this->editing = true;
        $this->editingSegment = $segment;
        $this->type = $segment->type;
        $this->supplier_id = $segment->supplier_id;
        $this->airline = $segment->airline ?? '';
        $this->flight_number = $segment->flight_number ?? '';
        $this->booking_reference = $segment->booking_reference ?? '';
        $this->ticket_number = $segment->ticket_number ?? '';
        $this->class = $segment->class ?? 'economy';
        $this->cabin = $segment->cabin ?? '';
        $this->fare_basis = $segment->fare_basis ?? '';
        $this->departure_airport = $segment->departure_airport ?? '';
        $this->arrival_airport = $segment->arrival_airport ?? '';
        $this->departure_terminal = $segment->departure_terminal ?? '';
        $this->arrival_terminal = $segment->arrival_terminal ?? '';
        $this->departure_datetime = $segment->departure_datetime?->format('Y-m-d\TH:i');
        $this->arrival_datetime = $segment->arrival_datetime?->format('Y-m-d\TH:i');
        $this->baggage = $segment->baggage ?? '';
        $this->seat = $segment->seat ?? '';
        $this->meal = $segment->meal ?? '';
        $this->status = $segment->status ?? 'confirmed';
        $this->cost_price = (string) $segment->cost_price;
        $this->selling_price = (string) $segment->selling_price;
        $this->currency = $segment->currency ?? 'USD';
        $this->notes = $segment->notes ?? '';
        $this->dispatch('open-modal', 'flight-form');
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'trip_id' => $this->trip->id,
            'type' => $this->type,
            'supplier_id' => $this->supplier_id ?: null,
            'airline' => $this->airline,
            'flight_number' => $this->flight_number,
            'booking_reference' => $this->booking_reference,
            'ticket_number' => $this->ticket_number,
            'class' => $this->class,
            'cabin' => $this->cabin,
            'fare_basis' => $this->fare_basis,
            'departure_airport' => Str::upper($this->departure_airport),
            'arrival_airport' => Str::upper($this->arrival_airport),
            'departure_terminal' => $this->departure_terminal,
            'arrival_terminal' => $this->arrival_terminal,
            'departure_datetime' => $this->departure_datetime,
            'arrival_datetime' => $this->arrival_datetime,
            'baggage' => $this->baggage,
            'seat' => $this->seat,
            'meal' => $this->meal,
            'status' => $this->status,
            'cost_price' => (float) ($this->cost_price ?: 0),
            'selling_price' => (float) ($this->selling_price ?: 0),
            'currency' => $this->currency,
            'notes' => $this->notes,
        ];

        if ($this->editingSegment) {
            $this->editingSegment->update($data);
        } else {
            FlightSegment::create($data);
        }

        $this->trip->recalculateTotals();
        $this->trip->load('flightSegments.supplier');
        $this->dispatch('close-modal', 'flight-form');
        $this->dispatch('notify', type: 'success', message: $this->editingSegment ? 'Flight updated successfully.' : 'Flight added successfully.');
        $this->resetForm();
    }

    public function delete(FlightSegment $segment): void
    {
        $segment->delete();
        $this->trip->recalculateTotals();
        $this->trip->load('flightSegments.supplier');
        $this->dispatch('notify', type: 'success', message: 'Flight deleted successfully.');
    }

    public function duplicate(FlightSegment $segment): void
    {
        $clone = $segment->replicate();
        $clone->save();
        $this->trip->recalculateTotals();
        $this->trip->load('flightSegments.supplier');
        $this->dispatch('notify', type: 'success', message: 'Flight duplicated successfully.');
    }

    private function resetForm(): void
    {
        $this->editing = false;
        $this->editingSegment = null;
        $this->type = 'departure';
        $this->supplier_id = null;
        $this->airline = '';
        $this->flight_number = '';
        $this->booking_reference = '';
        $this->ticket_number = '';
        $this->class = 'economy';
        $this->cabin = '';
        $this->fare_basis = '';
        $this->departure_airport = '';
        $this->arrival_airport = '';
        $this->departure_terminal = '';
        $this->arrival_terminal = '';
        $this->departure_datetime = null;
        $this->arrival_datetime = null;
        $this->baggage = '';
        $this->seat = '';
        $this->meal = '';
        $this->status = 'confirmed';
        $this->cost_price = '0';
        $this->selling_price = '0';
        $this->currency = 'USD';
        $this->notes = '';
    }

    public function render()
    {
        return view('livewire.trips.flight-reservation', [
            'segments' => $this->trip->flightSegments,
            'suppliers' => Supplier::where('is_active', true)->orderBy('company_name')->get(),
        ]);
    }
}
