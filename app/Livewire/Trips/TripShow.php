<?php

namespace App\Livewire\Trips;

use App\Models\Trip;
use App\Models\Supplier;
use App\Models\Passenger;
use App\Models\TripAutomationLog;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Illuminate\Support\Collection;

#[Layout('layouts.app')]
class TripShow extends Component
{
    public Trip $trip;
    public string $activeTab = 'services';

    // Passenger CRUD
    public bool $showPassengerForm = false;
    public ?string $editPassengerId = null;
    public string $p_first_name = '';
    public string $p_last_name = '';
    public string $p_date_of_birth = '';
    public string $p_nationality = '';
    public string $p_passport_number = '';
    public string $p_passport_expiry = '';
    public string $p_customer_id = '';

    // Flight CRUD
    public bool $showFlightForm = false;
    public ?string $editFlightId = null;
    public string $f_airline = '';
    public string $f_flight_number = '';
    public string $f_departure_airport = '';
    public string $f_arrival_airport = '';
    public string $f_departure_datetime = '';
    public string $f_arrival_datetime = '';
    public string $f_selling_price = '0';
    public string $f_cost_price = '0';
    public string $f_currency = 'USD';
    public string $f_status = 'confirmed';
    public string $f_supplier_id = '';

    // Hotel CRUD
    public bool $showHotelForm = false;
    public ?string $editHotelId = null;
    public string $h_hotel_name = '';
    public string $h_city = '';
    public string $h_room_type = '';
    public string $h_check_in = '';
    public string $h_check_out = '';
    public string $h_number_of_rooms = '1';
    public string $h_selling_price = '0';
    public string $h_cost_price = '0';
    public string $h_currency = 'USD';
    public string $h_status = 'confirmed';
    public string $h_supplier_id = '';

    // Transfer CRUD
    public bool $showTransferForm = false;
    public ?string $editTransferId = null;
    public string $t_pickup = '';
    public string $t_dropoff = '';
    public string $t_vehicle_type = '';
    public string $t_passengers = '1';
    public string $t_pickup_datetime = '';
    public string $t_selling_price = '0';
    public string $t_cost_price = '0';
    public string $t_currency = 'USD';
    public string $t_supplier_id = '';

    // Visa CRUD
    public bool $showVisaForm = false;
    public ?string $editVisaId = null;
    public string $v_country = '';
    public string $v_visa_type = 'tourist';
    public string $v_selling_price = '0';
    public string $v_cost_price = '0';
    public string $v_currency = 'USD';

    // Insurance CRUD
    public bool $showInsuranceForm = false;
    public ?string $editInsuranceId = null;
    public string $i_type = 'travel';
    public string $i_policy_number = '';
    public string $i_start_date = '';
    public string $i_end_date = '';
    public string $i_selling_price = '0';
    public string $i_cost_price = '0';
    public string $i_currency = 'USD';

    // Activity CRUD
    public bool $showActivityForm = false;
    public ?string $editActivityId = null;
    public string $a_name = '';
    public string $a_location = '';
    public string $a_date = '';
    public string $a_time = '';
    public string $a_selling_price = '0';
    public string $a_cost_price = '0';
    public string $a_currency = 'USD';

    public function mount(Trip $trip): void
    {
        $this->loadTrip();
    }

    #[On('passenger-updated')]
    #[On('refresh-trip')]
    public function loadTrip(): void
    {
        $this->trip = Trip::with([
            'customer', 'passengers',
            'flightSegments.supplier',
            'hotelBookings.supplier',
            'transferBookings.supplier',
            'visaApplications', 'insurancePolicies', 'activities',
            'cruiseBookings.supplier', 'trainBookings.supplier', 'carRentals.supplier', 'packageBookings.supplier', 'otherServices.supplier',
            'tripNotes', 'tasks', 'documents', 'expenses',
            'invoices', 'payments',
            'services' => fn($q) => $q->orderBy('sort_order')->orderBy('created_at'),
            'timeline' => fn($q) => $q->latest()->limit(30),
        ])->findOrFail($this->trip->id);
    }

    #[Computed]
    public function automationLogs()
    {
        return TripAutomationLog::where('trip_id', $this->trip->id)
            ->latest()->take(10)->get();
    }

    #[Computed]
    public function totalPaid()
    {
        return (float) $this->trip->payments()
            ->where('status', 'paid')
            ->sum('amount');
    }

    #[Computed]
    public function totalDue()
    {
        return max(0, (float) $this->trip->total_selling_price - $this->totalPaid);
    }

    public function runAutomation(): void
    {
        try {
            app(\App\Services\TripAutomationService::class)->run($this->trip);
            $this->dispatch('notify', type: 'success', title: 'Automation Run', message: 'Trip automation completed.');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: 'Automation Failed', message: $e->getMessage());
        }
        $this->dispatch('refresh-trip');
    }

    public function sendWhatsApp(): void
    {
        try {
            app(\App\Services\NotificationService::class)->sendTripWhatsApp($this->trip);
            $this->dispatch('notify', type: 'success', title: 'WhatsApp Sent', message: 'WhatsApp notification sent.');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: 'Failed', message: $e->getMessage());
        }
    }

    public function sendEmail(): void
    {
        try {
            app(\App\Services\NotificationService::class)->sendTripEmail($this->trip);
            $this->dispatch('notify', type: 'success', title: 'Email Sent', message: 'Email notification sent.');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: 'Failed', message: $e->getMessage());
        }
    }

    // ---- PASSENGERS ----
    public function openPassengerForm(): void
    {
        $this->resetPassengerForm();
        $this->showPassengerForm = true;
    }

    public function editPassenger(string $id): void
    {
        $p = Passenger::findOrFail($id);
        $this->editPassengerId = $p->id;
        $this->p_first_name = $p->first_name;
        $this->p_last_name = $p->last_name;
        $this->p_date_of_birth = $p->date_of_birth?->format('Y-m-d') ?? '';
        $this->p_nationality = $p->nationality ?? '';
        $this->p_passport_number = $p->passport_number ?? '';
        $this->p_passport_expiry = $p->passport_expiry?->format('Y-m-d') ?? '';
        $this->p_customer_id = $p->customer_id ?? '';
        $this->showPassengerForm = true;
    }

    public function savePassenger(): void
    {
        $this->validate([
            'p_first_name' => 'required|string|max:255',
            'p_last_name' => 'required|string|max:255',
        ]);
        $data = [
            'first_name' => $this->p_first_name,
            'last_name' => $this->p_last_name,
            'date_of_birth' => $this->p_date_of_birth ?: null,
            'nationality' => $this->p_nationality ?: null,
            'passport_number' => $this->p_passport_number ?: null,
            'passport_expiry' => $this->p_passport_expiry ?: null,
            'customer_id' => $this->p_customer_id ?: null,
        ];
        if ($this->editPassengerId) {
            Passenger::findOrFail($this->editPassengerId)->update($data);
        } else {
            $this->trip->passengers()->create($data);
        }
        $this->resetPassengerForm();
        $this->loadTrip();
        $this->dispatch('notify', 'Passenger saved');
    }

    public function deletePassenger(string $id): void
    {
        $p = Passenger::where('trip_id', $this->trip->id)->findOrFail($id);
        $p->delete();
        $this->loadTrip();
        $this->dispatch('notify', 'Passenger removed');
    }

    // ---- FLIGHTS ----
    public function openFlightForm(): void { $this->resetFlightForm(); $this->showFlightForm = true; }
    public function editFlight(string $id): void {
        $f = \App\Models\FlightSegment::where('trip_id', $this->trip->id)->findOrFail($id);
        $this->editFlightId = $f->id;
        foreach (['airline' => 'f_airline', 'flight_number' => 'f_flight_number', 'departure_airport' => 'f_departure_airport', 'arrival_airport' => 'f_arrival_airport', 'selling_price' => 'f_selling_price', 'cost_price' => 'f_cost_price', 'currency' => 'f_currency', 'status' => 'f_status', 'supplier_id' => 'f_supplier_id'] as $k => $v) $this->$v = (string) $f->$k;
        $this->f_departure_datetime = $f->departure_datetime?->format('Y-m-d\TH:i') ?? '';
        $this->f_arrival_datetime = $f->arrival_datetime?->format('Y-m-d\TH:i') ?? '';
        $this->showFlightForm = true;
    }
    public function saveFlight(): void {
        $this->validate([
            'f_airline' => 'nullable|string|max:255',
            'f_flight_number' => 'nullable|string|max:50',
            'f_departure_airport' => 'nullable|string|max:10',
            'f_arrival_airport' => 'nullable|string|max:10',
            'f_departure_datetime' => 'nullable|date',
            'f_arrival_datetime' => 'nullable|date|after:f_departure_datetime',
            'f_selling_price' => 'nullable|numeric|min:0',
            'f_cost_price' => 'nullable|numeric|min:0',
        ]);
        $data = [
            'airline' => $this->f_airline, 'flight_number' => $this->f_flight_number,
            'departure_airport' => $this->f_departure_airport, 'arrival_airport' => $this->f_arrival_airport,
            'departure_datetime' => $this->f_departure_datetime ?: null, 'arrival_datetime' => $this->f_arrival_datetime ?: null,
            'selling_price' => (float) $this->f_selling_price, 'cost_price' => (float) $this->f_cost_price,
            'currency' => $this->f_currency, 'status' => $this->f_status, 'supplier_id' => $this->f_supplier_id ?: null,
        ];
        if ($this->editFlightId) { \App\Models\FlightSegment::where('trip_id', $this->trip->id)->findOrFail($this->editFlightId)->update($data); }
        else { $this->trip->flightSegments()->create($data); }
        $this->resetFlightForm(); $this->trip->recalculateTotals(); $this->loadTrip(); $this->dispatch('notify', 'Flight saved');
    }
    public function deleteFlight(string $id): void { \App\Models\FlightSegment::where('trip_id', $this->trip->id)->findOrFail($id)->delete(); $this->trip->recalculateTotals(); $this->loadTrip(); $this->dispatch('notify', 'Flight removed'); }

    // ---- HOTELS ----
    public function openHotelForm(): void { $this->resetHotelForm(); $this->showHotelForm = true; }
    public function editHotel(string $id): void {
        $h = \App\Models\HotelBooking::where('trip_id', $this->trip->id)->findOrFail($id);
        $this->editHotelId = $h->id;
        foreach (['hotel_name' => 'h_hotel_name', 'city' => 'h_city', 'room_type' => 'h_room_type', 'selling_price' => 'h_selling_price', 'cost_price' => 'h_cost_price', 'currency' => 'h_currency', 'status' => 'h_status', 'supplier_id' => 'h_supplier_id'] as $k => $v) $this->$v = (string) $h->$k;
        $this->h_check_in = $h->check_in?->format('Y-m-d') ?? '';
        $this->h_check_out = $h->check_out?->format('Y-m-d') ?? '';
        $this->h_number_of_rooms = (string) $h->number_of_rooms;
        $this->showHotelForm = true;
    }
    public function saveHotel(): void {
        $this->validate([
            'h_hotel_name' => 'nullable|string|max:255',
            'h_city' => 'nullable|string|max:255',
            'h_room_type' => 'nullable|string|max:255',
            'h_check_in' => 'nullable|date',
            'h_check_out' => 'nullable|date|after:h_check_in',
            'h_number_of_rooms' => 'nullable|integer|min:1',
            'h_selling_price' => 'nullable|numeric|min:0',
            'h_cost_price' => 'nullable|numeric|min:0',
        ]);
        $data = [
            'hotel_name' => $this->h_hotel_name, 'city' => $this->h_city, 'room_type' => $this->h_room_type,
            'check_in' => $this->h_check_in ?: null, 'check_out' => $this->h_check_out ?: null,
            'number_of_rooms' => (int) $this->h_number_of_rooms,
            'selling_price' => (float) $this->h_selling_price, 'cost_price' => (float) $this->h_cost_price,
            'currency' => $this->h_currency, 'status' => $this->h_status, 'supplier_id' => $this->h_supplier_id ?: null,
        ];
        if ($this->editHotelId) { \App\Models\HotelBooking::where('trip_id', $this->trip->id)->findOrFail($this->editHotelId)->update($data); }
        else { $this->trip->hotelBookings()->create($data); }
        $this->resetHotelForm(); $this->trip->recalculateTotals(); $this->loadTrip(); $this->dispatch('notify', 'Hotel saved');
    }
    public function deleteHotel(string $id): void { \App\Models\HotelBooking::where('trip_id', $this->trip->id)->findOrFail($id)->delete(); $this->trip->recalculateTotals(); $this->loadTrip(); $this->dispatch('notify', 'Hotel removed'); }

    // ---- TRANSFERS ----
    public function openTransferForm(): void { $this->resetTransferForm(); $this->showTransferForm = true; }
    public function editTransfer(string $id): void {
        $tr = \App\Models\TransferBooking::where('trip_id', $this->trip->id)->findOrFail($id);
        $this->editTransferId = $tr->id;
        foreach (['pickup_location' => 't_pickup', 'dropoff_location' => 't_dropoff', 'vehicle_type' => 't_vehicle_type', 'selling_price' => 't_selling_price', 'cost_price' => 't_cost_price', 'currency' => 't_currency', 'supplier_id' => 't_supplier_id'] as $k => $v) $this->$v = (string) $tr->$k;
        $this->t_passengers = (string) ($tr->number_of_passengers ?? $tr->passengers ?? 1);
        $this->t_pickup_datetime = $tr->pickup_datetime?->format('Y-m-d\TH:i') ?? '';
        $this->showTransferForm = true;
    }
    public function saveTransfer(): void {
        $this->validate([
            't_pickup' => 'nullable|string|max:255',
            't_dropoff' => 'nullable|string|max:255',
            't_vehicle_type' => 'nullable|string|max:255',
            't_passengers' => 'nullable|integer|min:1',
            't_pickup_datetime' => 'nullable|date',
            't_selling_price' => 'nullable|numeric|min:0',
            't_cost_price' => 'nullable|numeric|min:0',
        ]);
        $data = [
            'pickup_location' => $this->t_pickup, 'dropoff_location' => $this->t_dropoff,
            'vehicle_type' => $this->t_vehicle_type, 'number_of_passengers' => (int) $this->t_passengers,
            'pickup_datetime' => $this->t_pickup_datetime ?: null,
            'selling_price' => (float) $this->t_selling_price, 'cost_price' => (float) $this->t_cost_price,
            'currency' => $this->t_currency, 'supplier_id' => $this->t_supplier_id ?: null,
        ];
        if ($this->editTransferId) { \App\Models\TransferBooking::where('trip_id', $this->trip->id)->findOrFail($this->editTransferId)->update($data); }
        else { $this->trip->transferBookings()->create($data); }
        $this->resetTransferForm(); $this->trip->recalculateTotals(); $this->loadTrip(); $this->dispatch('notify', 'Transfer saved');
    }
    public function deleteTransfer(string $id): void { \App\Models\TransferBooking::where('trip_id', $this->trip->id)->findOrFail($id)->delete(); $this->trip->recalculateTotals(); $this->loadTrip(); $this->dispatch('notify', 'Transfer removed'); }

    // ---- VISAS ----
    public function openVisaForm(): void { $this->resetVisaForm(); $this->showVisaForm = true; }
    public function editVisa(string $id): void {
        $visa = \App\Models\VisaApplication::where('trip_id', $this->trip->id)->findOrFail($id);
        $this->editVisaId = $visa->id;
        $this->v_country = $visa->country;
        $this->v_visa_type = $visa->visa_type;
        $this->v_selling_price = (string) $visa->selling_price;
        $this->v_cost_price = (string) $visa->cost_price;
        $this->v_currency = $visa->currency;
        $this->showVisaForm = true;
    }
    public function saveVisa(): void {
        $this->validate([
            'v_country' => 'nullable|string|max:255',
            'v_visa_type' => 'nullable|string|max:50',
            'v_selling_price' => 'nullable|numeric|min:0',
            'v_cost_price' => 'nullable|numeric|min:0',
        ]);
        $data = [
            'country' => $this->v_country, 'visa_type' => $this->v_visa_type,
            'selling_price' => (float) $this->v_selling_price, 'cost_price' => (float) $this->v_cost_price,
            'currency' => $this->v_currency,
        ];
        if ($this->editVisaId) { \App\Models\VisaApplication::where('trip_id', $this->trip->id)->findOrFail($this->editVisaId)->update($data); }
        else { $this->trip->visaApplications()->create($data); }
        $this->resetVisaForm(); $this->trip->recalculateTotals(); $this->loadTrip(); $this->dispatch('notify', 'Visa saved');
    }
    public function deleteVisa(string $id): void { \App\Models\VisaApplication::where('trip_id', $this->trip->id)->findOrFail($id)->delete(); $this->trip->recalculateTotals(); $this->loadTrip(); $this->dispatch('notify', 'Visa removed'); }

    // ---- INSURANCE ----
    public function openInsuranceForm(): void { $this->resetInsuranceForm(); $this->showInsuranceForm = true; }
    public function editInsurance(string $id): void {
        $ins = \App\Models\InsurancePolicy::where('trip_id', $this->trip->id)->findOrFail($id);
        $this->editInsuranceId = $ins->id;
        foreach (['type' => 'i_type', 'policy_number' => 'i_policy_number', 'selling_price' => 'i_selling_price', 'cost_price' => 'i_cost_price', 'currency' => 'i_currency'] as $k => $v) $this->$v = (string) $ins->$k;
        $this->i_start_date = $ins->start_date?->format('Y-m-d') ?? '';
        $this->i_end_date = $ins->end_date?->format('Y-m-d') ?? '';
        $this->showInsuranceForm = true;
    }
    public function saveInsurance(): void {
        $this->validate([
            'i_type' => 'nullable|string|max:50',
            'i_policy_number' => 'nullable|string|max:100',
            'i_start_date' => 'nullable|date',
            'i_end_date' => 'nullable|date|after:i_start_date',
            'i_selling_price' => 'nullable|numeric|min:0',
            'i_cost_price' => 'nullable|numeric|min:0',
        ]);
        $data = [
            'type' => $this->i_type, 'policy_number' => $this->i_policy_number ?: null,
            'start_date' => $this->i_start_date ?: null, 'end_date' => $this->i_end_date ?: null,
            'selling_price' => (float) $this->i_selling_price, 'cost_price' => (float) $this->i_cost_price,
            'currency' => $this->i_currency,
        ];
        if ($this->editInsuranceId) { \App\Models\InsurancePolicy::where('trip_id', $this->trip->id)->findOrFail($this->editInsuranceId)->update($data); }
        else { $this->trip->insurancePolicies()->create($data); }
        $this->resetInsuranceForm(); $this->trip->recalculateTotals(); $this->loadTrip(); $this->dispatch('notify', 'Insurance saved');
    }
    public function deleteInsurance(string $id): void { \App\Models\InsurancePolicy::where('trip_id', $this->trip->id)->findOrFail($id)->delete(); $this->trip->recalculateTotals(); $this->loadTrip(); $this->dispatch('notify', 'Insurance removed'); }

    // ---- ACTIVITIES ----
    public function openActivityForm(): void { $this->resetActivityForm(); $this->showActivityForm = true; }
    public function editActivity(string $id): void {
        $a = \App\Models\Activity::where('trip_id', $this->trip->id)->findOrFail($id);
        $this->editActivityId = $a->id;
        foreach (['name' => 'a_name', 'location' => 'a_location', 'selling_price' => 'a_selling_price', 'cost_price' => 'a_cost_price', 'currency' => 'a_currency'] as $k => $v) $this->$v = (string) $a->$k;
        $this->a_date = $a->date?->format('Y-m-d') ?? '';
        $this->a_time = $a->time?->format('H:i') ?? '';
        $this->showActivityForm = true;
    }
    public function saveActivity(): void {
        $this->validate([
            'a_name' => 'nullable|string|max:255',
            'a_location' => 'nullable|string|max:255',
            'a_date' => 'nullable|date',
            'a_time' => 'nullable|date_format:H:i',
            'a_selling_price' => 'nullable|numeric|min:0',
            'a_cost_price' => 'nullable|numeric|min:0',
        ]);
        $data = [
            'name' => $this->a_name, 'location' => $this->a_location ?: null,
            'date' => $this->a_date ?: null, 'time' => $this->a_time ?: null,
            'selling_price' => (float) $this->a_selling_price, 'cost_price' => (float) $this->a_cost_price,
            'currency' => $this->a_currency,
        ];
        if ($this->editActivityId) { \App\Models\Activity::where('trip_id', $this->trip->id)->findOrFail($this->editActivityId)->update($data); }
        else { $this->trip->activities()->create($data); }
        $this->resetActivityForm(); $this->trip->recalculateTotals(); $this->loadTrip(); $this->dispatch('notify', 'Activity saved');
    }
    public function deleteActivity(string $id): void { \App\Models\Activity::where('trip_id', $this->trip->id)->findOrFail($id)->delete(); $this->trip->recalculateTotals(); $this->loadTrip(); $this->dispatch('notify', 'Activity removed'); }

    // ---- CRUISES ----
    public bool $showCruiseForm = false;
    public ?string $editCruiseId = null;
    public string $cr_cruise_line = '';
    public string $cr_ship_name = '';
    public string $cr_cabin_type = '';
    public string $cr_departure_port = '';
    public string $cr_arrival_port = '';
    public string $cr_departure_date = '';
    public string $cr_arrival_date = '';
    public string $cr_itinerary = '';
    public string $cr_selling_price = '0';
    public string $cr_cost_price = '0';
    public string $cr_currency = 'USD';
    public string $cr_status = 'confirmed';
    public string $cr_supplier_id = '';

    public function openCruiseForm(): void { $this->resetCruiseForm(); $this->showCruiseForm = true; }
    public function editCruise(string $id): void {
        $c = \App\Models\CruiseBooking::where('trip_id', $this->trip->id)->findOrFail($id);
        $this->editCruiseId = $c->id;
        foreach (['cruise_line' => 'cr_cruise_line', 'ship_name' => 'cr_ship_name', 'cabin_type' => 'cr_cabin_type', 'departure_port' => 'cr_departure_port', 'arrival_port' => 'cr_arrival_port', 'itinerary' => 'cr_itinerary', 'selling_price' => 'cr_selling_price', 'cost_price' => 'cr_cost_price', 'currency' => 'cr_currency', 'status' => 'cr_status', 'supplier_id' => 'cr_supplier_id'] as $k => $v) $this->$v = (string) $c->$k;
        $this->cr_departure_date = $c->departure_date?->format('Y-m-d') ?? '';
        $this->cr_arrival_date = $c->arrival_date?->format('Y-m-d') ?? '';
        $this->showCruiseForm = true;
    }
    public function saveCruise(): void {
        $this->validate([
            'cr_cruise_line' => 'nullable|string|max:255',
            'cr_ship_name' => 'nullable|string|max:255',
            'cr_cabin_type' => 'nullable|string|max:255',
            'cr_departure_port' => 'nullable|string|max:255',
            'cr_arrival_port' => 'nullable|string|max:255',
            'cr_departure_date' => 'nullable|date',
            'cr_arrival_date' => 'nullable|date|after:cr_departure_date',
            'cr_selling_price' => 'nullable|numeric|min:0',
            'cr_cost_price' => 'nullable|numeric|min:0',
        ]);
        $data = [
            'cruise_line' => $this->cr_cruise_line, 'ship_name' => $this->cr_ship_name,
            'cabin_type' => $this->cr_cabin_type,
            'departure_port' => $this->cr_departure_port, 'arrival_port' => $this->cr_arrival_port,
            'departure_date' => $this->cr_departure_date ?: null, 'arrival_date' => $this->cr_arrival_date ?: null,
            'itinerary' => $this->cr_itinerary ?: null,
            'selling_price' => (float) $this->cr_selling_price, 'cost_price' => (float) $this->cr_cost_price,
            'currency' => $this->cr_currency, 'status' => $this->cr_status, 'supplier_id' => $this->cr_supplier_id ?: null,
        ];
        if ($this->editCruiseId) { \App\Models\CruiseBooking::where('trip_id', $this->trip->id)->findOrFail($this->editCruiseId)->update($data); }
        else { $this->trip->cruiseBookings()->create($data); }
        $this->resetCruiseForm(); $this->trip->recalculateTotals(); $this->loadTrip(); $this->dispatch('notify', 'Cruise saved');
    }
    public function deleteCruise(string $id): void { \App\Models\CruiseBooking::where('trip_id', $this->trip->id)->findOrFail($id)->delete(); $this->trip->recalculateTotals(); $this->loadTrip(); $this->dispatch('notify', 'Cruise removed'); }

    // ---- TRAINS ----
    public bool $showTrainForm = false;
    public ?string $editTrainId = null;
    public string $tr_company = '';
    public string $tr_train_number = '';
    public string $tr_departure_station = '';
    public string $tr_arrival_station = '';
    public string $tr_departure_datetime = '';
    public string $tr_arrival_datetime = '';
    public string $tr_class = '';
    public string $tr_selling_price = '0';
    public string $tr_cost_price = '0';
    public string $tr_currency = 'USD';
    public string $tr_status = 'confirmed';
    public string $tr_supplier_id = '';

    public function openTrainForm(): void { $this->resetTrainForm(); $this->showTrainForm = true; }
    public function editTrain(string $id): void {
        $t = \App\Models\TrainBooking::where('trip_id', $this->trip->id)->findOrFail($id);
        $this->editTrainId = $t->id;
        foreach (['train_company' => 'tr_company', 'train_number' => 'tr_train_number', 'departure_station' => 'tr_departure_station', 'arrival_station' => 'tr_arrival_station', 'class' => 'tr_class', 'selling_price' => 'tr_selling_price', 'cost_price' => 'tr_cost_price', 'currency' => 'tr_currency', 'status' => 'tr_status', 'supplier_id' => 'tr_supplier_id'] as $k => $v) $this->$v = (string) $t->$k;
        $this->tr_departure_datetime = $t->departure_datetime?->format('Y-m-d\TH:i') ?? '';
        $this->tr_arrival_datetime = $t->arrival_datetime?->format('Y-m-d\TH:i') ?? '';
        $this->showTrainForm = true;
    }
    public function saveTrain(): void {
        $this->validate([
            'tr_company' => 'nullable|string|max:255',
            'tr_train_number' => 'nullable|string|max:50',
            'tr_departure_station' => 'nullable|string|max:255',
            'tr_arrival_station' => 'nullable|string|max:255',
            'tr_departure_datetime' => 'nullable|date',
            'tr_arrival_datetime' => 'nullable|date|after:tr_departure_datetime',
            'tr_class' => 'nullable|string|max:255',
            'tr_selling_price' => 'nullable|numeric|min:0',
            'tr_cost_price' => 'nullable|numeric|min:0',
        ]);
        $data = [
            'train_company' => $this->tr_company, 'train_number' => $this->tr_train_number,
            'departure_station' => $this->tr_departure_station, 'arrival_station' => $this->tr_arrival_station,
            'departure_datetime' => $this->tr_departure_datetime ?: null, 'arrival_datetime' => $this->tr_arrival_datetime ?: null,
            'class' => $this->tr_class ?: null,
            'selling_price' => (float) $this->tr_selling_price, 'cost_price' => (float) $this->tr_cost_price,
            'currency' => $this->tr_currency, 'status' => $this->tr_status, 'supplier_id' => $this->tr_supplier_id ?: null,
        ];
        if ($this->editTrainId) { \App\Models\TrainBooking::where('trip_id', $this->trip->id)->findOrFail($this->editTrainId)->update($data); }
        else { $this->trip->trainBookings()->create($data); }
        $this->resetTrainForm(); $this->trip->recalculateTotals(); $this->loadTrip(); $this->dispatch('notify', 'Train saved');
    }
    public function deleteTrain(string $id): void { \App\Models\TrainBooking::where('trip_id', $this->trip->id)->findOrFail($id)->delete(); $this->trip->recalculateTotals(); $this->loadTrip(); $this->dispatch('notify', 'Train removed'); }

    // ---- CAR RENTALS ----
    public bool $showCarForm = false;
    public ?string $editCarId = null;
    public string $ca_company = '';
    public string $ca_car_type = '';
    public string $ca_pickup_location = '';
    public string $ca_dropoff_location = '';
    public string $ca_pickup_datetime = '';
    public string $ca_dropoff_datetime = '';
    public string $ca_selling_price = '0';
    public string $ca_cost_price = '0';
    public string $ca_currency = 'USD';
    public string $ca_status = 'confirmed';
    public string $ca_supplier_id = '';

    public function openCarForm(): void { $this->resetCarForm(); $this->showCarForm = true; }
    public function editCar(string $id): void {
        $c = \App\Models\CarRental::where('trip_id', $this->trip->id)->findOrFail($id);
        $this->editCarId = $c->id;
        foreach (['company' => 'ca_company', 'car_type' => 'ca_car_type', 'pickup_location' => 'ca_pickup_location', 'dropoff_location' => 'ca_dropoff_location', 'selling_price' => 'ca_selling_price', 'cost_price' => 'ca_cost_price', 'currency' => 'ca_currency', 'status' => 'ca_status', 'supplier_id' => 'ca_supplier_id'] as $k => $v) $this->$v = (string) $c->$k;
        $this->ca_pickup_datetime = $c->pickup_datetime?->format('Y-m-d\TH:i') ?? '';
        $this->ca_dropoff_datetime = $c->dropoff_datetime?->format('Y-m-d\TH:i') ?? '';
        $this->showCarForm = true;
    }
    public function saveCar(): void {
        $this->validate([
            'ca_company' => 'nullable|string|max:255',
            'ca_car_type' => 'nullable|string|max:255',
            'ca_pickup_location' => 'nullable|string|max:255',
            'ca_dropoff_location' => 'nullable|string|max:255',
            'ca_pickup_datetime' => 'nullable|date',
            'ca_dropoff_datetime' => 'nullable|date|after:ca_pickup_datetime',
            'ca_selling_price' => 'nullable|numeric|min:0',
            'ca_cost_price' => 'nullable|numeric|min:0',
        ]);
        $data = [
            'company' => $this->ca_company, 'car_type' => $this->ca_car_type,
            'pickup_location' => $this->ca_pickup_location, 'dropoff_location' => $this->ca_dropoff_location,
            'pickup_datetime' => $this->ca_pickup_datetime ?: null, 'dropoff_datetime' => $this->ca_dropoff_datetime ?: null,
            'selling_price' => (float) $this->ca_selling_price, 'cost_price' => (float) $this->ca_cost_price,
            'currency' => $this->ca_currency, 'status' => $this->ca_status, 'supplier_id' => $this->ca_supplier_id ?: null,
        ];
        if ($this->editCarId) { \App\Models\CarRental::where('trip_id', $this->trip->id)->findOrFail($this->editCarId)->update($data); }
        else { $this->trip->carRentals()->create($data); }
        $this->resetCarForm(); $this->trip->recalculateTotals(); $this->loadTrip(); $this->dispatch('notify', 'Car rental saved');
    }
    public function deleteCar(string $id): void { \App\Models\CarRental::where('trip_id', $this->trip->id)->findOrFail($id)->delete(); $this->trip->recalculateTotals(); $this->loadTrip(); $this->dispatch('notify', 'Car rental removed'); }

    // ---- PACKAGES ----
    public bool $showPackageForm = false;
    public ?string $editPackageId = null;
    public string $pk_name = '';
    public string $pk_type = '';
    public string $pk_description = '';
    public string $pk_start_date = '';
    public string $pk_end_date = '';
    public string $pk_selling_price = '0';
    public string $pk_cost_price = '0';
    public string $pk_currency = 'USD';
    public string $pk_status = 'confirmed';
    public string $pk_supplier_id = '';

    public function openPackageForm(): void { $this->resetPackageForm(); $this->showPackageForm = true; }
    public function editPackage(string $id): void {
        $p = \App\Models\PackageBooking::where('trip_id', $this->trip->id)->findOrFail($id);
        $this->editPackageId = $p->id;
        foreach (['package_name' => 'pk_name', 'package_type' => 'pk_type', 'description' => 'pk_description', 'selling_price' => 'pk_selling_price', 'cost_price' => 'pk_cost_price', 'currency' => 'pk_currency', 'status' => 'pk_status', 'supplier_id' => 'pk_supplier_id'] as $k => $v) $this->$v = (string) $p->$k;
        $this->pk_start_date = $p->start_date?->format('Y-m-d') ?? '';
        $this->pk_end_date = $p->end_date?->format('Y-m-d') ?? '';
        $this->showPackageForm = true;
    }
    public function savePackage(): void {
        $this->validate([
            'pk_name' => 'nullable|string|max:255',
            'pk_type' => 'nullable|string|max:255',
            'pk_start_date' => 'nullable|date',
            'pk_end_date' => 'nullable|date|after:pk_start_date',
            'pk_selling_price' => 'nullable|numeric|min:0',
            'pk_cost_price' => 'nullable|numeric|min:0',
        ]);
        $data = [
            'package_name' => $this->pk_name, 'package_type' => $this->pk_type,
            'description' => $this->pk_description ?: null,
            'start_date' => $this->pk_start_date ?: null, 'end_date' => $this->pk_end_date ?: null,
            'selling_price' => (float) $this->pk_selling_price, 'cost_price' => (float) $this->pk_cost_price,
            'currency' => $this->pk_currency, 'status' => $this->pk_status, 'supplier_id' => $this->pk_supplier_id ?: null,
        ];
        if ($this->editPackageId) { \App\Models\PackageBooking::where('trip_id', $this->trip->id)->findOrFail($this->editPackageId)->update($data); }
        else { $this->trip->packageBookings()->create($data); }
        $this->resetPackageForm(); $this->trip->recalculateTotals(); $this->loadTrip(); $this->dispatch('notify', 'Package saved');
    }
    public function deletePackage(string $id): void { \App\Models\PackageBooking::where('trip_id', $this->trip->id)->findOrFail($id)->delete(); $this->trip->recalculateTotals(); $this->loadTrip(); $this->dispatch('notify', 'Package removed'); }

    // ---- OTHER SERVICES ----
    public bool $showOtherForm = false;
    public ?string $editOtherId = null;
    public string $o_name = '';
    public string $o_type = '';
    public string $o_description = '';
    public string $o_date = '';
    public string $o_selling_price = '0';
    public string $o_cost_price = '0';
    public string $o_currency = 'USD';
    public string $o_status = 'confirmed';
    public string $o_supplier_id = '';

    public function openOtherForm(): void { $this->resetOtherForm(); $this->showOtherForm = true; }
    public function editOther(string $id): void {
        $o = \App\Models\OtherService::where('trip_id', $this->trip->id)->findOrFail($id);
        $this->editOtherId = $o->id;
        foreach (['service_name' => 'o_name', 'service_type' => 'o_type', 'description' => 'o_description', 'selling_price' => 'o_selling_price', 'cost_price' => 'o_cost_price', 'currency' => 'o_currency', 'status' => 'o_status', 'supplier_id' => 'o_supplier_id'] as $k => $v) $this->$v = (string) $o->$k;
        $this->o_date = $o->service_date?->format('Y-m-d') ?? '';
        $this->showOtherForm = true;
    }
    public function saveOther(): void {
        $this->validate([
            'o_name' => 'nullable|string|max:255',
            'o_type' => 'nullable|string|max:255',
            'o_date' => 'nullable|date',
            'o_selling_price' => 'nullable|numeric|min:0',
            'o_cost_price' => 'nullable|numeric|min:0',
        ]);
        $data = [
            'service_name' => $this->o_name, 'service_type' => $this->o_type,
            'description' => $this->o_description ?: null,
            'service_date' => $this->o_date ?: null,
            'selling_price' => (float) $this->o_selling_price, 'cost_price' => (float) $this->o_cost_price,
            'currency' => $this->o_currency, 'status' => $this->o_status, 'supplier_id' => $this->o_supplier_id ?: null,
        ];
        if ($this->editOtherId) { \App\Models\OtherService::where('trip_id', $this->trip->id)->findOrFail($this->editOtherId)->update($data); }
        else { $this->trip->otherServices()->create($data); }
        $this->resetOtherForm(); $this->trip->recalculateTotals(); $this->loadTrip(); $this->dispatch('notify', 'Other service saved');
    }
    public function deleteOther(string $id): void { \App\Models\OtherService::where('trip_id', $this->trip->id)->findOrFail($id)->delete(); $this->trip->recalculateTotals(); $this->loadTrip(); $this->dispatch('notify', 'Other service removed'); }

    // ---- RESETS ----
    protected function resetPassengerForm(): void { $this->reset(['p_first_name', 'p_last_name', 'p_date_of_birth', 'p_nationality', 'p_passport_number', 'p_passport_expiry', 'p_customer_id', 'showPassengerForm', 'editPassengerId']); }
    protected function resetFlightForm(): void { $this->reset(['f_airline', 'f_flight_number', 'f_departure_airport', 'f_arrival_airport', 'f_departure_datetime', 'f_arrival_datetime', 'f_selling_price', 'f_cost_price', 'f_currency', 'f_status', 'f_supplier_id', 'showFlightForm', 'editFlightId']); $this->f_currency = 'USD'; $this->f_status = 'confirmed'; $this->f_selling_price = '0'; $this->f_cost_price = '0'; }
    protected function resetHotelForm(): void { $this->reset(['h_hotel_name', 'h_city', 'h_room_type', 'h_check_in', 'h_check_out', 'h_number_of_rooms', 'h_selling_price', 'h_cost_price', 'h_currency', 'h_status', 'h_supplier_id', 'showHotelForm', 'editHotelId']); $this->h_currency = 'USD'; $this->h_status = 'confirmed'; $this->h_selling_price = '0'; $this->h_cost_price = '0'; $this->h_number_of_rooms = '1'; }
    protected function resetTransferForm(): void { $this->reset(['t_pickup', 't_dropoff', 't_vehicle_type', 't_passengers', 't_pickup_datetime', 't_selling_price', 't_cost_price', 't_currency', 't_supplier_id', 'showTransferForm', 'editTransferId']); $this->t_currency = 'USD'; $this->t_selling_price = '0'; $this->t_cost_price = '0'; $this->t_passengers = '1'; }
    protected function resetVisaForm(): void { $this->reset(['v_country', 'v_visa_type', 'v_selling_price', 'v_cost_price', 'v_currency', 'showVisaForm', 'editVisaId']); $this->v_visa_type = 'tourist'; $this->v_currency = 'USD'; $this->v_selling_price = '0'; $this->v_cost_price = '0'; }
    protected function resetInsuranceForm(): void { $this->reset(['i_type', 'i_policy_number', 'i_start_date', 'i_end_date', 'i_selling_price', 'i_cost_price', 'i_currency', 'showInsuranceForm', 'editInsuranceId']); $this->i_type = 'travel'; $this->i_currency = 'USD'; $this->i_selling_price = '0'; $this->i_cost_price = '0'; }
    protected function resetActivityForm(): void { $this->reset(['a_name', 'a_location', 'a_date', 'a_time', 'a_selling_price', 'a_cost_price', 'a_currency', 'showActivityForm', 'editActivityId']); $this->a_currency = 'USD'; $this->a_selling_price = '0'; $this->a_cost_price = '0'; }

    protected function resetCruiseForm(): void { $this->reset(['showCruiseForm', 'editCruiseId', 'cr_cruise_line', 'cr_ship_name', 'cr_cabin_type', 'cr_departure_port', 'cr_arrival_port', 'cr_departure_date', 'cr_arrival_date', 'cr_itinerary', 'cr_selling_price', 'cr_cost_price', 'cr_currency', 'cr_status', 'cr_supplier_id']); $this->cr_currency = 'USD'; $this->cr_status = 'confirmed'; $this->cr_selling_price = '0'; $this->cr_cost_price = '0'; }
    protected function resetTrainForm(): void { $this->reset(['showTrainForm', 'editTrainId', 'tr_company', 'tr_train_number', 'tr_departure_station', 'tr_arrival_station', 'tr_departure_datetime', 'tr_arrival_datetime', 'tr_class', 'tr_selling_price', 'tr_cost_price', 'tr_currency', 'tr_status', 'tr_supplier_id']); $this->tr_currency = 'USD'; $this->tr_status = 'confirmed'; $this->tr_selling_price = '0'; $this->tr_cost_price = '0'; }
    protected function resetCarForm(): void { $this->reset(['showCarForm', 'editCarId', 'ca_company', 'ca_car_type', 'ca_pickup_location', 'ca_dropoff_location', 'ca_pickup_datetime', 'ca_dropoff_datetime', 'ca_selling_price', 'ca_cost_price', 'ca_currency', 'ca_status', 'ca_supplier_id']); $this->ca_currency = 'USD'; $this->ca_status = 'confirmed'; $this->ca_selling_price = '0'; $this->ca_cost_price = '0'; }
    protected function resetPackageForm(): void { $this->reset(['showPackageForm', 'editPackageId', 'pk_name', 'pk_type', 'pk_description', 'pk_start_date', 'pk_end_date', 'pk_selling_price', 'pk_cost_price', 'pk_currency', 'pk_status', 'pk_supplier_id']); $this->pk_currency = 'USD'; $this->pk_status = 'confirmed'; $this->pk_selling_price = '0'; $this->pk_cost_price = '0'; }
    protected function resetOtherForm(): void { $this->reset(['showOtherForm', 'editOtherId', 'o_name', 'o_type', 'o_description', 'o_date', 'o_selling_price', 'o_cost_price', 'o_currency', 'o_status', 'o_supplier_id']); $this->o_currency = 'USD'; $this->o_status = 'confirmed'; $this->o_selling_price = '0'; $this->o_cost_price = '0'; }

    // ---- ITINERARY ----

    #[Computed]
    public function itineraryDays(): array
    {
        if (!$this->trip->start_date) return [];

        $services = $this->trip->services()
            ->whereNotNull('service_date')
            ->orderBy('service_date')
            ->orderBy('sort_order')
            ->orderBy('created_at')
            ->get();

        $days = [];
        $start = $this->trip->start_date->copy();
        $end = $this->trip->end_date ?? $this->trip->start_date;
        $totalDays = (int) $start->diffInDays($end) + 1;

        for ($i = 0; $i < $totalDays; $i++) {
            $date = $start->copy()->addDays($i);
            $dayServices = $services->filter(fn($s) => $s->day_number === ($i + 1) || $s->service_date?->isSameDay($date));
            $days[] = [
                'day'      => $i + 1,
                'date'     => $date->format('Y-m-d'),
                'label'    => $date->format('D d M'),
                'services' => $dayServices,
            ];
        }

        return $days;
    }

    public function autoAssignDays(): void
    {
        if (!$this->trip->start_date) {
            $this->dispatch('notify', 'Set a start date first');
            return;
        }

        $services = $this->trip->services()
            ->whereNotNull('service_date')
            ->orderBy('service_date')
            ->get();

        foreach ($services as $service) {
            $dayNum = (int) $this->trip->start_date->diffInDays($service->service_date) + 1;
            if ($dayNum < 1) $dayNum = 1;
            $service->updateQuietly([
                'day_number' => $dayNum,
                'sort_order' => $service->sort_order ?: 0,
            ]);
        }

        $this->loadTrip();
        $this->dispatch('notify', 'Services assigned to days');
    }

    public function reorderService(string $serviceId, int $dayNumber, int $sortOrder): void
    {
        $service = $this->trip->services()->findOrFail($serviceId);
        $service->update([
            'day_number' => $dayNumber,
            'sort_order' => $sortOrder,
        ]);
    }

    public function updateServiceDay(string $serviceId, int $dayNumber): void
    {
        $service = $this->trip->services()->findOrFail($serviceId);
        $service->update(['day_number' => $dayNumber]);
        $this->loadTrip();
    }

    public function assignServiceToDay(string $serviceId, string $dayDate): void
    {
        $service = $this->trip->services()->findOrFail($serviceId);
        $service->update([
            'service_date' => $dayDate,
            'day_number' => $this->trip->start_date ? (int) $this->trip->start_date->diffInDays(\Carbon\Carbon::parse($dayDate)) + 1 : null,
        ]);
        $this->loadTrip();
    }

    public function unassignServiceDay(string $serviceId): void
    {
        $service = $this->trip->services()->findOrFail($serviceId);
        $service->update(['day_number' => null, 'sort_order' => 0]);
        $this->loadTrip();
    }

    public function render()
    {
        return view('livewire.trips.trip-show', [
            'suppliers' => Supplier::where('is_active', true)->orderBy('company_name')->get(),
        ]);
    }
}
