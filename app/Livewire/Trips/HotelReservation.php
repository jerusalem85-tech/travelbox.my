<?php

namespace App\Livewire\Trips;

use App\Models\Trip;
use App\Models\HotelBooking;
use App\Models\Supplier;
use Livewire\Component;

class HotelReservation extends Component
{
    public Trip $trip;
    public bool $editing = false;
    public ?HotelBooking $editingBooking = null;

    public string $hotel_name = '';
    public string $city = '';
    public string $address = '';
    public ?string $check_in = null;
    public ?string $check_out = null;
    public string $check_in_time = '';
    public string $check_out_time = '';
    public string $room_type = '';
    public string $meal_plan = '';
    public string $number_of_rooms = '1';
    public string $booking_reference = '';
    public string $confirmation_number = '';
    public string $cancellation_policy = '';
    public string $latitude = '';
    public string $longitude = '';
    public ?string $supplier_id = null;
    public string $status = 'confirmed';
    public string $cost_price = '0';
    public string $selling_price = '0';
    public string $currency = 'USD';
    public string $notes = '';

    protected function rules(): array
    {
        return [
            'hotel_name' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after_or_equal:check_in',
            'check_in_time' => 'nullable|string|max:10',
            'check_out_time' => 'nullable|string|max:10',
            'room_type' => 'nullable|string|max:255',
            'meal_plan' => 'nullable|string|max:50',
            'number_of_rooms' => 'nullable|integer|min:1',
            'booking_reference' => 'nullable|string|max:100',
            'confirmation_number' => 'nullable|string|max:100',
            'cancellation_policy' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
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
        $this->trip = $trip->load('hotelBookings.supplier');
    }

    public function create(): void
    {
        $this->resetForm();
        $this->editing = true;
        $this->editingBooking = null;
        $this->dispatch('open-modal', 'hotel-form');
    }

    public function edit(HotelBooking $booking): void
    {
        $this->editing = true;
        $this->editingBooking = $booking;
        $this->hotel_name = $booking->hotel_name ?? '';
        $this->city = $booking->city ?? '';
        $this->address = $booking->address ?? '';
        $this->check_in = $booking->check_in?->format('Y-m-d');
        $this->check_out = $booking->check_out?->format('Y-m-d');
        $this->check_in_time = $booking->check_in_time ?? '';
        $this->check_out_time = $booking->check_out_time ?? '';
        $this->room_type = $booking->room_type ?? '';
        $this->meal_plan = $booking->meal_plan ?? '';
        $this->number_of_rooms = (string) ($booking->number_of_rooms ?? 1);
        $this->booking_reference = $booking->booking_reference ?? '';
        $this->confirmation_number = $booking->confirmation_number ?? '';
        $this->cancellation_policy = $booking->cancellation_policy ?? '';
        $this->latitude = (string) ($booking->latitude ?? '');
        $this->longitude = (string) ($booking->longitude ?? '');
        $this->supplier_id = $booking->supplier_id;
        $this->status = $booking->status ?? 'confirmed';
        $this->cost_price = (string) $booking->cost_price;
        $this->selling_price = (string) $booking->selling_price;
        $this->currency = $booking->currency ?? 'USD';
        $this->notes = $booking->notes ?? '';
        $this->dispatch('open-modal', 'hotel-form');
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'trip_id' => $this->trip->id,
            'hotel_name' => $this->hotel_name,
            'city' => $this->city ?: null,
            'address' => $this->address ?: null,
            'check_in' => $this->check_in,
            'check_out' => $this->check_out,
            'check_in_time' => $this->check_in_time ?: null,
            'check_out_time' => $this->check_out_time ?: null,
            'room_type' => $this->room_type ?: null,
            'meal_plan' => $this->meal_plan ?: null,
            'number_of_rooms' => (int) ($this->number_of_rooms ?: 1),
            'booking_reference' => $this->booking_reference ?: null,
            'confirmation_number' => $this->confirmation_number ?: null,
            'cancellation_policy' => $this->cancellation_policy ?: null,
            'latitude' => $this->latitude ? (float) $this->latitude : null,
            'longitude' => $this->longitude ? (float) $this->longitude : null,
            'supplier_id' => $this->supplier_id ?: null,
            'status' => $this->status,
            'cost_price' => (float) ($this->cost_price ?: 0),
            'selling_price' => (float) ($this->selling_price ?: 0),
            'currency' => $this->currency,
            'notes' => $this->notes ?: null,
        ];

        if ($this->editingBooking) {
            $this->editingBooking->update($data);
        } else {
            HotelBooking::create($data);
        }

        $this->trip->recalculateTotals();
        $this->trip->load('hotelBookings.supplier');
        $this->dispatch('close-modal', 'hotel-form');
        $this->dispatch('notify', type: 'success', message: $this->editingBooking ? 'Hotel booking updated successfully.' : 'Hotel booking added successfully.');
        $this->resetForm();
    }

    public function delete(HotelBooking $booking): void
    {
        $booking->delete();
        $this->trip->recalculateTotals();
        $this->trip->load('hotelBookings.supplier');
        $this->dispatch('notify', type: 'success', message: 'Hotel booking deleted successfully.');
    }

    public function duplicate(HotelBooking $booking): void
    {
        $clone = $booking->replicate();
        $clone->save();
        $this->trip->recalculateTotals();
        $this->trip->load('hotelBookings.supplier');
        $this->dispatch('notify', type: 'success', message: 'Hotel booking duplicated successfully.');
    }

    private function resetForm(): void
    {
        $this->editing = false;
        $this->editingBooking = null;
        $this->hotel_name = '';
        $this->city = '';
        $this->address = '';
        $this->check_in = null;
        $this->check_out = null;
        $this->check_in_time = '';
        $this->check_out_time = '';
        $this->room_type = '';
        $this->meal_plan = '';
        $this->number_of_rooms = '1';
        $this->booking_reference = '';
        $this->confirmation_number = '';
        $this->cancellation_policy = '';
        $this->latitude = '';
        $this->longitude = '';
        $this->supplier_id = null;
        $this->status = 'confirmed';
        $this->cost_price = '0';
        $this->selling_price = '0';
        $this->currency = 'USD';
        $this->notes = '';
    }

    public function render()
    {
        return view('livewire.trips.hotel-reservation', [
            'bookings' => $this->trip->hotelBookings,
            'suppliers' => Supplier::where('is_active', true)->orderBy('company_name')->get(),
        ]);
    }
}
