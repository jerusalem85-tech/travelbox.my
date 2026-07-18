<?php

namespace App\Livewire\Trips;

use App\Models\Trip;
use App\Models\Customer;
use App\Models\Passenger;
use App\Models\Supplier;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
class TripForm extends Component
{
    use WithFileUploads;

    public ?Trip $trip = null;
    public bool $editing = false;

    // Trip fields
    public string $customer_id = '';
    public string $status = 'enquiry';
    public string $type = 'custom';
    public string $name = '';
    public string $destination = '';
    public string $latitude = '';
    public string $longitude = '';
    public string $start_date = '';
    public string $end_date = '';
    public string $total_selling_price = '0';
    public string $total_cost_price = '0';
    public string $currency = 'USD';
    public string $notes = '';
    public string $internal_notes = '';

    // Quick customer
    public bool $showQuickCustomer = false;
    public string $quickCustomerName = '';
    public string $quickCustomerPhone = '';
    public string $quickCustomerEmail = '';

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

    protected $listeners = ['refreshTripForm' => '$refresh'];

    public function mount(?Trip $trip = null): void
    {
        $this->trip = $trip;
        if ($trip) {
            $this->editing = true;
            $this->customer_id = $trip->customer_id;
            $this->status = $trip->status;
            $this->type = $trip->type;
            $this->name = $trip->name ?? '';
            $this->destination = $trip->destination ?? '';
            $this->latitude = (string) $trip->latitude ?: '';
            $this->longitude = (string) $trip->longitude ?: '';
            $this->start_date = $trip->start_date?->format('Y-m-d') ?? '';
            $this->end_date = $trip->end_date?->format('Y-m-d') ?? '';
            $this->total_selling_price = (string) $trip->total_selling_price;
            $this->total_cost_price = (string) $trip->total_cost_price;
            $this->currency = $trip->currency;
            $this->notes = $trip->notes ?? '';
            $this->internal_notes = $trip->internal_notes ?? '';
            $this->trip->load(['passengers', 'flightSegments', 'hotelBookings', 'transferBookings', 'visaApplications', 'insurancePolicies', 'activities', 'cruiseBookings', 'trainBookings', 'carRentals', 'packageBookings', 'otherServices']);
        }
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'status' => 'required|in:enquiry,confirmed,in_progress,completed,cancelled',
            'type' => 'required|in:package,custom',
            'name' => 'nullable|string|max:255',
            'destination' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'total_selling_price' => 'nullable|numeric|min:0',
            'total_cost_price' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'notes' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'customer_id' => $this->customer_id,
            'status' => $this->status,
            'type' => $this->type,
            'name' => $this->name ?: null,
            'destination' => $this->destination ?: null,
            'latitude' => $this->latitude !== '' ? (float) $this->latitude : null,
            'longitude' => $this->longitude !== '' ? (float) $this->longitude : null,
            'start_date' => $this->start_date ?: null,
            'end_date' => $this->end_date ?: null,
            'total_selling_price' => (float) $this->total_selling_price,
            'total_cost_price' => (float) $this->total_cost_price,
            'currency' => $this->currency,
            'notes' => $this->notes ?: null,
            'internal_notes' => $this->internal_notes ?: null,
        ];

        if ($this->editing && $this->trip) {
            $oldStatus = $this->trip->status;
            $this->trip->update($data);
            if ($oldStatus !== $this->status) {
                app(\App\Services\NotificationService::class)->tripStatusChanged($this->trip, $oldStatus, $this->status);
            }
            $this->dispatch('notify', type: 'success', title: 'Trip Updated', message: 'Trip updated successfully.');
            $this->redirect(route('trips.show', $this->trip), navigate: true);
        } else {
            $data['trip_number'] = $this->generateTripNumber();
            $data['created_by'] = auth()->id() ?? 1;
            $trip = Trip::create($data);

            $trip->logTimeline('trip_created', "Trip {$trip->trip_number} created by " . auth()->user()?->name);

            try {
                app(\App\Services\TripAutomationService::class)->run($trip);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Trip automation failed: ' . $e->getMessage());
            }

            $this->dispatch('notify', type: 'success', title: 'Trip Created', message: 'Trip created successfully.');
            $this->redirect(route('trips.show', $trip), navigate: true);
        }
    }

    // ---- QUICK CUSTOMER ----
    public function quickCustomerSave(): void
    {
        $this->validate([
            'quickCustomerName' => 'required|string|max:255',
            'quickCustomerPhone' => 'nullable|string|max:50',
            'quickCustomerEmail' => 'nullable|email|max:255',
        ]);
        $customer = Customer::create([
            'first_name' => $this->quickCustomerName,
            'phone' => $this->quickCustomerPhone ?: null,
            'email' => $this->quickCustomerEmail ?: null,
            'is_active' => true,
        ]);
        $this->customer_id = $customer->id;
        $this->showQuickCustomer = false;
        $this->reset('quickCustomerName', 'quickCustomerPhone', 'quickCustomerEmail');
        $this->dispatch('notify', type: 'success', title: 'Customer Created', message: 'Customer added successfully.');
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
            'p_date_of_birth' => 'nullable|date',
            'p_nationality' => 'nullable|string|max:255',
            'p_passport_number' => 'nullable|string|max:50',
            'p_passport_expiry' => 'nullable|date',
        ]);

        $t = $this->trip ?? $this->getPendingTrip();

        if ($this->editPassengerId) {
            $p = Passenger::findOrFail($this->editPassengerId);
            $p->update($this->passengerData());
        } else {
            $t->passengers()->create($this->passengerData());
        }

        $this->resetPassengerForm();
        $t->load('passengers');
        $this->dispatch('notify', 'Passenger saved');
    }

    public function deletePassenger(string $id): void
    {
        $t = $this->trip ?? $this->getPendingTrip();
        Passenger::where('trip_id', $t->id)->findOrFail($id)->delete();
        $t->load('passengers');
        $this->dispatch('notify', 'Passenger removed');
    }

    // ---- FLIGHTS ----
    public function openFlightForm(): void
    {
        $this->resetFlightForm();
        $this->showFlightForm = true;
    }

    public function editFlight(string $id): void
    {
        $t = $this->trip ?? $this->getPendingTrip();
        $f = \App\Models\FlightSegment::where('trip_id', $t->id)->findOrFail($id);
        $this->editFlightId = $f->id;
        $this->f_airline = $f->airline;
        $this->f_flight_number = $f->flight_number;
        $this->f_departure_airport = $f->departure_airport;
        $this->f_arrival_airport = $f->arrival_airport;
        $this->f_departure_datetime = $f->departure_datetime?->format('Y-m-d\TH:i') ?? '';
        $this->f_arrival_datetime = $f->arrival_datetime?->format('Y-m-d\TH:i') ?? '';
        $this->f_selling_price = (string) $f->selling_price;
        $this->f_cost_price = (string) $f->cost_price;
        $this->f_currency = $f->currency;
        $this->f_status = $f->status;
        $this->f_supplier_id = $f->supplier_id ?? '';
        $this->showFlightForm = true;
    }

    public function saveFlight(): void
    {
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
        $t = $this->trip ?? $this->getPendingTrip();
        $data = [
            'airline' => $this->f_airline,
            'flight_number' => $this->f_flight_number,
            'departure_airport' => $this->f_departure_airport,
            'arrival_airport' => $this->f_arrival_airport,
            'departure_datetime' => $this->f_departure_datetime ?: null,
            'arrival_datetime' => $this->f_arrival_datetime ?: null,
            'selling_price' => (float) $this->f_selling_price,
            'cost_price' => (float) $this->f_cost_price,
            'currency' => $this->f_currency,
            'status' => $this->f_status,
            'supplier_id' => $this->f_supplier_id ?: null,
        ];

        if ($this->editFlightId) {
            \App\Models\FlightSegment::where('trip_id', $t->id)->findOrFail($this->editFlightId)->update($data);
        } else {
            $t->flightSegments()->create($data);
        }

        $this->resetFlightForm();
        $t->load('flightSegments');
        $t->recalculateTotals();
        $this->dispatch('notify', 'Flight saved');
    }

    public function deleteFlight(string $id): void
    {
        $t = $this->trip ?? $this->getPendingTrip();
        \App\Models\FlightSegment::where('trip_id', $t->id)->findOrFail($id)->delete();
        $t->load('flightSegments');
        $t->recalculateTotals();
        $this->dispatch('notify', 'Flight removed');
    }

    // ---- HOTELS ----
    public function openHotelForm(): void
    {
        $this->resetHotelForm();
        $this->showHotelForm = true;
    }

    public function editHotel(string $id): void
    {
        $t = $this->trip ?? $this->getPendingTrip();
        $h = \App\Models\HotelBooking::where('trip_id', $t->id)->findOrFail($id);
        $this->editHotelId = $h->id;
        $this->h_hotel_name = $h->hotel_name;
        $this->h_city = $h->city;
        $this->h_room_type = $h->room_type;
        $this->h_check_in = $h->check_in?->format('Y-m-d') ?? '';
        $this->h_check_out = $h->check_out?->format('Y-m-d') ?? '';
        $this->h_number_of_rooms = (string) $h->number_of_rooms;
        $this->h_selling_price = (string) $h->selling_price;
        $this->h_cost_price = (string) $h->cost_price;
        $this->h_currency = $h->currency;
        $this->h_status = $h->status;
        $this->h_supplier_id = $h->supplier_id ?? '';
        $this->showHotelForm = true;
    }

    public function saveHotel(): void
    {
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
        $t = $this->trip ?? $this->getPendingTrip();
        $data = [
            'hotel_name' => $this->h_hotel_name,
            'city' => $this->h_city,
            'room_type' => $this->h_room_type,
            'check_in' => $this->h_check_in ?: null,
            'check_out' => $this->h_check_out ?: null,
            'number_of_rooms' => (int) $this->h_number_of_rooms,
            'selling_price' => (float) $this->h_selling_price,
            'cost_price' => (float) $this->h_cost_price,
            'currency' => $this->h_currency,
            'status' => $this->h_status,
            'supplier_id' => $this->h_supplier_id ?: null,
        ];

        if ($this->editHotelId) {
            \App\Models\HotelBooking::where('trip_id', $t->id)->findOrFail($this->editHotelId)->update($data);
        } else {
            $t->hotelBookings()->create($data);
        }

        $this->resetHotelForm();
        $t->load('hotelBookings');
        $t->recalculateTotals();
        $this->dispatch('notify', 'Hotel saved');
    }

    public function deleteHotel(string $id): void
    {
        $t = $this->trip ?? $this->getPendingTrip();
        \App\Models\HotelBooking::where('trip_id', $t->id)->findOrFail($id)->delete();
        $t->load('hotelBookings');
        $t->recalculateTotals();
        $this->dispatch('notify', 'Hotel removed');
    }

    // ---- TRANSFERS ----
    public function openTransferForm(): void
    {
        $this->resetTransferForm();
        $this->showTransferForm = true;
    }

    public function editTransfer(string $id): void
    {
        $t = $this->trip ?? $this->getPendingTrip();
        $tr = \App\Models\TransferBooking::where('trip_id', $t->id)->findOrFail($id);
        $this->editTransferId = $tr->id;
        $this->t_pickup = $tr->pickup_location;
        $this->t_dropoff = $tr->dropoff_location;
        $this->t_vehicle_type = $tr->vehicle_type;
        $this->t_passengers = (string) ($tr->number_of_passengers ?? $tr->passengers ?? 1);
        $this->t_pickup_datetime = $tr->pickup_datetime?->format('Y-m-d\TH:i') ?? '';
        $this->t_selling_price = (string) $tr->selling_price;
        $this->t_cost_price = (string) $tr->cost_price;
        $this->t_currency = $tr->currency;
        $this->t_supplier_id = $tr->supplier_id ?? '';
        $this->showTransferForm = true;
    }

    public function saveTransfer(): void
    {
        $this->validate([
            't_pickup' => 'nullable|string|max:255',
            't_dropoff' => 'nullable|string|max:255',
            't_vehicle_type' => 'nullable|string|max:255',
            't_passengers' => 'nullable|integer|min:1',
            't_pickup_datetime' => 'nullable|date',
            't_selling_price' => 'nullable|numeric|min:0',
            't_cost_price' => 'nullable|numeric|min:0',
        ]);
        $t = $this->trip ?? $this->getPendingTrip();
        $data = [
            'pickup_location' => $this->t_pickup,
            'dropoff_location' => $this->t_dropoff,
            'vehicle_type' => $this->t_vehicle_type,
            'number_of_passengers' => (int) $this->t_passengers,
            'pickup_datetime' => $this->t_pickup_datetime ?: null,
            'selling_price' => (float) $this->t_selling_price,
            'cost_price' => (float) $this->t_cost_price,
            'currency' => $this->t_currency,
            'supplier_id' => $this->t_supplier_id ?: null,
        ];

        if ($this->editTransferId) {
            \App\Models\TransferBooking::where('trip_id', $t->id)->findOrFail($this->editTransferId)->update($data);
        } else {
            $t->transferBookings()->create($data);
        }

        $this->resetTransferForm();
        $t->load('transferBookings');
        $t->recalculateTotals();
        $this->dispatch('notify', 'Transfer saved');
    }

    public function deleteTransfer(string $id): void
    {
        $t = $this->trip ?? $this->getPendingTrip();
        \App\Models\TransferBooking::where('trip_id', $t->id)->findOrFail($id)->delete();
        $t->load('transferBookings');
        $t->recalculateTotals();
        $this->dispatch('notify', 'Transfer removed');
    }

    // ---- VISAS ----
    public function openVisaForm(): void
    {
        $this->resetVisaForm();
        $this->showVisaForm = true;
    }

    public function editVisa(string $id): void
    {
        $t = $this->trip ?? $this->getPendingTrip();
        $v = \App\Models\VisaApplication::where('trip_id', $t->id)->findOrFail($id);
        $this->editVisaId = $v->id;
        $this->v_country = $v->country;
        $this->v_visa_type = $v->visa_type;
        $this->v_selling_price = (string) $v->selling_price;
        $this->v_cost_price = (string) $v->cost_price;
        $this->v_currency = $v->currency;
        $this->showVisaForm = true;
    }

    public function saveVisa(): void
    {
        $this->validate([
            'v_country' => 'nullable|string|max:255',
            'v_visa_type' => 'nullable|string|max:50',
            'v_selling_price' => 'nullable|numeric|min:0',
            'v_cost_price' => 'nullable|numeric|min:0',
        ]);
        $t = $this->trip ?? $this->getPendingTrip();
        $data = [
            'country' => $this->v_country,
            'visa_type' => $this->v_visa_type,
            'selling_price' => (float) $this->v_selling_price,
            'cost_price' => (float) $this->v_cost_price,
            'currency' => $this->v_currency,
        ];

        if ($this->editVisaId) {
            \App\Models\VisaApplication::where('trip_id', $t->id)->findOrFail($this->editVisaId)->update($data);
        } else {
            $t->visaApplications()->create($data);
        }

        $this->resetVisaForm();
        $t->load('visaApplications');
        $t->recalculateTotals();
        $this->dispatch('notify', 'Visa saved');
    }

    public function deleteVisa(string $id): void
    {
        $t = $this->trip ?? $this->getPendingTrip();
        \App\Models\VisaApplication::where('trip_id', $t->id)->findOrFail($id)->delete();
        $t->load('visaApplications');
        $t->recalculateTotals();
        $this->dispatch('notify', 'Visa removed');
    }

    // ---- INSURANCE ----
    public function openInsuranceForm(): void
    {
        $this->resetInsuranceForm();
        $this->showInsuranceForm = true;
    }

    public function editInsurance(string $id): void
    {
        $t = $this->trip ?? $this->getPendingTrip();
        $ins = \App\Models\InsurancePolicy::where('trip_id', $t->id)->findOrFail($id);
        $this->editInsuranceId = $ins->id;
        $this->i_type = $ins->type;
        $this->i_policy_number = $ins->policy_number;
        $this->i_start_date = $ins->start_date?->format('Y-m-d') ?? '';
        $this->i_end_date = $ins->end_date?->format('Y-m-d') ?? '';
        $this->i_selling_price = (string) $ins->selling_price;
        $this->i_cost_price = (string) $ins->cost_price;
        $this->i_currency = $ins->currency;
        $this->showInsuranceForm = true;
    }

    public function saveInsurance(): void
    {
        $this->validate([
            'i_type' => 'nullable|string|max:50',
            'i_policy_number' => 'nullable|string|max:100',
            'i_start_date' => 'nullable|date',
            'i_end_date' => 'nullable|date|after:i_start_date',
            'i_selling_price' => 'nullable|numeric|min:0',
            'i_cost_price' => 'nullable|numeric|min:0',
        ]);
        $t = $this->trip ?? $this->getPendingTrip();
        $data = [
            'type' => $this->i_type,
            'policy_number' => $this->i_policy_number ?: null,
            'start_date' => $this->i_start_date ?: null,
            'end_date' => $this->i_end_date ?: null,
            'selling_price' => (float) $this->i_selling_price,
            'cost_price' => (float) $this->i_cost_price,
            'currency' => $this->i_currency,
        ];

        if ($this->editInsuranceId) {
            \App\Models\InsurancePolicy::where('trip_id', $t->id)->findOrFail($this->editInsuranceId)->update($data);
        } else {
            $t->insurancePolicies()->create($data);
        }

        $this->resetInsuranceForm();
        $t->load('insurancePolicies');
        $t->recalculateTotals();
        $this->dispatch('notify', 'Insurance saved');
    }

    public function deleteInsurance(string $id): void
    {
        $t = $this->trip ?? $this->getPendingTrip();
        \App\Models\InsurancePolicy::where('trip_id', $t->id)->findOrFail($id)->delete();
        $t->load('insurancePolicies');
        $t->recalculateTotals();
        $this->dispatch('notify', 'Insurance removed');
    }

    // ---- ACTIVITIES ----
    public function openActivityForm(): void
    {
        $this->resetActivityForm();
        $this->showActivityForm = true;
    }

    public function editActivity(string $id): void
    {
        $t = $this->trip ?? $this->getPendingTrip();
        $a = \App\Models\Activity::where('trip_id', $t->id)->findOrFail($id);
        $this->editActivityId = $a->id;
        $this->a_name = $a->name;
        $this->a_location = $a->location;
        $this->a_date = $a->date?->format('Y-m-d') ?? '';
        $this->a_time = $a->time?->format('H:i') ?? '';
        $this->a_selling_price = (string) $a->selling_price;
        $this->a_cost_price = (string) $a->cost_price;
        $this->a_currency = $a->currency;
        $this->showActivityForm = true;
    }

    public function saveActivity(): void
    {
        $this->validate([
            'a_name' => 'nullable|string|max:255',
            'a_location' => 'nullable|string|max:255',
            'a_date' => 'nullable|date',
            'a_time' => 'nullable|date_format:H:i',
            'a_selling_price' => 'nullable|numeric|min:0',
            'a_cost_price' => 'nullable|numeric|min:0',
        ]);
        $t = $this->trip ?? $this->getPendingTrip();
        $data = [
            'name' => $this->a_name,
            'location' => $this->a_location ?: null,
            'date' => $this->a_date ?: null,
            'time' => $this->a_time ?: null,
            'selling_price' => (float) $this->a_selling_price,
            'cost_price' => (float) $this->a_cost_price,
            'currency' => $this->a_currency,
        ];

        if ($this->editActivityId) {
            \App\Models\Activity::where('trip_id', $t->id)->findOrFail($this->editActivityId)->update($data);
        } else {
            $t->activities()->create($data);
        }

        $this->resetActivityForm();
        $t->load('activities');
        $t->recalculateTotals();
        $this->dispatch('notify', 'Activity saved');
    }

    public function deleteActivity(string $id): void
    {
        $t = $this->trip ?? $this->getPendingTrip();
        \App\Models\Activity::where('trip_id', $t->id)->findOrFail($id)->delete();
        $t->load('activities');
        $t->recalculateTotals();
        $this->dispatch('notify', 'Activity removed');
    }

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
        $t = $this->trip ?? $this->getPendingTrip();
        $c = \App\Models\CruiseBooking::where('trip_id', $t->id)->findOrFail($id);
        $this->editCruiseId = $c->id;
        $this->cr_cruise_line = $c->cruise_line; $this->cr_ship_name = $c->ship_name;
        $this->cr_cabin_type = $c->cabin_type; $this->cr_departure_port = $c->departure_port;
        $this->cr_arrival_port = $c->arrival_port; $this->cr_itinerary = $c->itinerary;
        $this->cr_departure_date = $c->departure_date?->format('Y-m-d') ?? '';
        $this->cr_arrival_date = $c->arrival_date?->format('Y-m-d') ?? '';
        $this->cr_selling_price = (string) $c->selling_price; $this->cr_cost_price = (string) $c->cost_price;
        $this->cr_currency = $c->currency; $this->cr_status = $c->status;
        $this->cr_supplier_id = $c->supplier_id ?? '';
        $this->showCruiseForm = true;
    }
    public function saveCruise(): void {
        $this->validate([
            'cr_cruise_line' => 'nullable|string|max:255', 'cr_ship_name' => 'nullable|string|max:255',
            'cr_cabin_type' => 'nullable|string|max:255', 'cr_departure_port' => 'nullable|string|max:255',
            'cr_arrival_port' => 'nullable|string|max:255', 'cr_departure_date' => 'nullable|date',
            'cr_arrival_date' => 'nullable|date|after:cr_departure_date',
            'cr_selling_price' => 'nullable|numeric|min:0', 'cr_cost_price' => 'nullable|numeric|min:0',
        ]);
        $t = $this->trip ?? $this->getPendingTrip();
        $data = [
            'cruise_line' => $this->cr_cruise_line, 'ship_name' => $this->cr_ship_name,
            'cabin_type' => $this->cr_cabin_type, 'departure_port' => $this->cr_departure_port,
            'arrival_port' => $this->cr_arrival_port, 'itinerary' => $this->cr_itinerary ?: null,
            'departure_date' => $this->cr_departure_date ?: null, 'arrival_date' => $this->cr_arrival_date ?: null,
            'selling_price' => (float) $this->cr_selling_price, 'cost_price' => (float) $this->cr_cost_price,
            'currency' => $this->cr_currency, 'status' => $this->cr_status, 'supplier_id' => $this->cr_supplier_id ?: null,
        ];
        if ($this->editCruiseId) { \App\Models\CruiseBooking::where('trip_id', $t->id)->findOrFail($this->editCruiseId)->update($data); }
        else { $t->cruiseBookings()->create($data); }
        $this->resetCruiseForm(); $t->load('cruiseBookings'); $t->recalculateTotals(); $this->dispatch('notify', 'Cruise saved');
    }
    public function deleteCruise(string $id): void {
        $t = $this->trip ?? $this->getPendingTrip();
        \App\Models\CruiseBooking::where('trip_id', $t->id)->findOrFail($id)->delete();
        $t->load('cruiseBookings'); $t->recalculateTotals(); $this->dispatch('notify', 'Cruise removed');
    }

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
        $t = $this->trip ?? $this->getPendingTrip();
        $tr = \App\Models\TrainBooking::where('trip_id', $t->id)->findOrFail($id);
        $this->editTrainId = $tr->id;
        $this->tr_company = $tr->train_company; $this->tr_train_number = $tr->train_number;
        $this->tr_departure_station = $tr->departure_station; $this->tr_arrival_station = $tr->arrival_station;
        $this->tr_class = $tr->class;
        $this->tr_departure_datetime = $tr->departure_datetime?->format('Y-m-d\TH:i') ?? '';
        $this->tr_arrival_datetime = $tr->arrival_datetime?->format('Y-m-d\TH:i') ?? '';
        $this->tr_selling_price = (string) $tr->selling_price; $this->tr_cost_price = (string) $tr->cost_price;
        $this->tr_currency = $tr->currency; $this->tr_status = $tr->status;
        $this->tr_supplier_id = $tr->supplier_id ?? '';
        $this->showTrainForm = true;
    }
    public function saveTrain(): void {
        $this->validate([
            'tr_company' => 'nullable|string|max:255', 'tr_train_number' => 'nullable|string|max:50',
            'tr_departure_station' => 'nullable|string|max:255', 'tr_arrival_station' => 'nullable|string|max:255',
            'tr_departure_datetime' => 'nullable|date', 'tr_arrival_datetime' => 'nullable|date|after:tr_departure_datetime',
            'tr_class' => 'nullable|string|max:255',
            'tr_selling_price' => 'nullable|numeric|min:0', 'tr_cost_price' => 'nullable|numeric|min:0',
        ]);
        $t = $this->trip ?? $this->getPendingTrip();
        $data = [
            'train_company' => $this->tr_company, 'train_number' => $this->tr_train_number,
            'departure_station' => $this->tr_departure_station, 'arrival_station' => $this->tr_arrival_station,
            'departure_datetime' => $this->tr_departure_datetime ?: null, 'arrival_datetime' => $this->tr_arrival_datetime ?: null,
            'class' => $this->tr_class ?: null,
            'selling_price' => (float) $this->tr_selling_price, 'cost_price' => (float) $this->tr_cost_price,
            'currency' => $this->tr_currency, 'status' => $this->tr_status, 'supplier_id' => $this->tr_supplier_id ?: null,
        ];
        if ($this->editTrainId) { \App\Models\TrainBooking::where('trip_id', $t->id)->findOrFail($this->editTrainId)->update($data); }
        else { $t->trainBookings()->create($data); }
        $this->resetTrainForm(); $t->load('trainBookings'); $t->recalculateTotals(); $this->dispatch('notify', 'Train saved');
    }
    public function deleteTrain(string $id): void {
        $t = $this->trip ?? $this->getPendingTrip();
        \App\Models\TrainBooking::where('trip_id', $t->id)->findOrFail($id)->delete();
        $t->load('trainBookings'); $t->recalculateTotals(); $this->dispatch('notify', 'Train removed');
    }

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
        $t = $this->trip ?? $this->getPendingTrip();
        $c = \App\Models\CarRental::where('trip_id', $t->id)->findOrFail($id);
        $this->editCarId = $c->id;
        $this->ca_company = $c->company; $this->ca_car_type = $c->car_type;
        $this->ca_pickup_location = $c->pickup_location; $this->ca_dropoff_location = $c->dropoff_location;
        $this->ca_pickup_datetime = $c->pickup_datetime?->format('Y-m-d\TH:i') ?? '';
        $this->ca_dropoff_datetime = $c->dropoff_datetime?->format('Y-m-d\TH:i') ?? '';
        $this->ca_selling_price = (string) $c->selling_price; $this->ca_cost_price = (string) $c->cost_price;
        $this->ca_currency = $c->currency; $this->ca_status = $c->status;
        $this->ca_supplier_id = $c->supplier_id ?? '';
        $this->showCarForm = true;
    }
    public function saveCar(): void {
        $this->validate([
            'ca_company' => 'nullable|string|max:255', 'ca_car_type' => 'nullable|string|max:255',
            'ca_pickup_location' => 'nullable|string|max:255', 'ca_dropoff_location' => 'nullable|string|max:255',
            'ca_pickup_datetime' => 'nullable|date', 'ca_dropoff_datetime' => 'nullable|date|after:ca_pickup_datetime',
            'ca_selling_price' => 'nullable|numeric|min:0', 'ca_cost_price' => 'nullable|numeric|min:0',
        ]);
        $t = $this->trip ?? $this->getPendingTrip();
        $data = [
            'company' => $this->ca_company, 'car_type' => $this->ca_car_type,
            'pickup_location' => $this->ca_pickup_location, 'dropoff_location' => $this->ca_dropoff_location,
            'pickup_datetime' => $this->ca_pickup_datetime ?: null, 'dropoff_datetime' => $this->ca_dropoff_datetime ?: null,
            'selling_price' => (float) $this->ca_selling_price, 'cost_price' => (float) $this->ca_cost_price,
            'currency' => $this->ca_currency, 'status' => $this->ca_status, 'supplier_id' => $this->ca_supplier_id ?: null,
        ];
        if ($this->editCarId) { \App\Models\CarRental::where('trip_id', $t->id)->findOrFail($this->editCarId)->update($data); }
        else { $t->carRentals()->create($data); }
        $this->resetCarForm(); $t->load('carRentals'); $t->recalculateTotals(); $this->dispatch('notify', 'Car rental saved');
    }
    public function deleteCar(string $id): void {
        $t = $this->trip ?? $this->getPendingTrip();
        \App\Models\CarRental::where('trip_id', $t->id)->findOrFail($id)->delete();
        $t->load('carRentals'); $t->recalculateTotals(); $this->dispatch('notify', 'Car rental removed');
    }

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
        $t = $this->trip ?? $this->getPendingTrip();
        $p = \App\Models\PackageBooking::where('trip_id', $t->id)->findOrFail($id);
        $this->editPackageId = $p->id;
        $this->pk_name = $p->package_name; $this->pk_type = $p->package_type; $this->pk_description = $p->description;
        $this->pk_start_date = $p->start_date?->format('Y-m-d') ?? '';
        $this->pk_end_date = $p->end_date?->format('Y-m-d') ?? '';
        $this->pk_selling_price = (string) $p->selling_price; $this->pk_cost_price = (string) $p->cost_price;
        $this->pk_currency = $p->currency; $this->pk_status = $p->status;
        $this->pk_supplier_id = $p->supplier_id ?? '';
        $this->showPackageForm = true;
    }
    public function savePackage(): void {
        $this->validate([
            'pk_name' => 'nullable|string|max:255', 'pk_type' => 'nullable|string|max:255',
            'pk_start_date' => 'nullable|date', 'pk_end_date' => 'nullable|date|after:pk_start_date',
            'pk_selling_price' => 'nullable|numeric|min:0', 'pk_cost_price' => 'nullable|numeric|min:0',
        ]);
        $t = $this->trip ?? $this->getPendingTrip();
        $data = [
            'package_name' => $this->pk_name, 'package_type' => $this->pk_type,
            'description' => $this->pk_description ?: null,
            'start_date' => $this->pk_start_date ?: null, 'end_date' => $this->pk_end_date ?: null,
            'selling_price' => (float) $this->pk_selling_price, 'cost_price' => (float) $this->pk_cost_price,
            'currency' => $this->pk_currency, 'status' => $this->pk_status, 'supplier_id' => $this->pk_supplier_id ?: null,
        ];
        if ($this->editPackageId) { \App\Models\PackageBooking::where('trip_id', $t->id)->findOrFail($this->editPackageId)->update($data); }
        else { $t->packageBookings()->create($data); }
        $this->resetPackageForm(); $t->load('packageBookings'); $t->recalculateTotals(); $this->dispatch('notify', 'Package saved');
    }
    public function deletePackage(string $id): void {
        $t = $this->trip ?? $this->getPendingTrip();
        \App\Models\PackageBooking::where('trip_id', $t->id)->findOrFail($id)->delete();
        $t->load('packageBookings'); $t->recalculateTotals(); $this->dispatch('notify', 'Package removed');
    }

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
        $t = $this->trip ?? $this->getPendingTrip();
        $o = \App\Models\OtherService::where('trip_id', $t->id)->findOrFail($id);
        $this->editOtherId = $o->id;
        $this->o_name = $o->service_name; $this->o_type = $o->service_type; $this->o_description = $o->description;
        $this->o_date = $o->service_date?->format('Y-m-d') ?? '';
        $this->o_selling_price = (string) $o->selling_price; $this->o_cost_price = (string) $o->cost_price;
        $this->o_currency = $o->currency; $this->o_status = $o->status;
        $this->o_supplier_id = $o->supplier_id ?? '';
        $this->showOtherForm = true;
    }
    public function saveOther(): void {
        $this->validate([
            'o_name' => 'nullable|string|max:255', 'o_type' => 'nullable|string|max:255',
            'o_date' => 'nullable|date',
            'o_selling_price' => 'nullable|numeric|min:0', 'o_cost_price' => 'nullable|numeric|min:0',
        ]);
        $t = $this->trip ?? $this->getPendingTrip();
        $data = [
            'service_name' => $this->o_name, 'service_type' => $this->o_type,
            'description' => $this->o_description ?: null, 'service_date' => $this->o_date ?: null,
            'selling_price' => (float) $this->o_selling_price, 'cost_price' => (float) $this->o_cost_price,
            'currency' => $this->o_currency, 'status' => $this->o_status, 'supplier_id' => $this->o_supplier_id ?: null,
        ];
        if ($this->editOtherId) { \App\Models\OtherService::where('trip_id', $t->id)->findOrFail($this->editOtherId)->update($data); }
        else { $t->otherServices()->create($data); }
        $this->resetOtherForm(); $t->load('otherServices'); $t->recalculateTotals(); $this->dispatch('notify', 'Other service saved');
    }
    public function deleteOther(string $id): void {
        $t = $this->trip ?? $this->getPendingTrip();
        \App\Models\OtherService::where('trip_id', $t->id)->findOrFail($id)->delete();
        $t->load('otherServices'); $t->recalculateTotals(); $this->dispatch('notify', 'Other service removed');
    }

    // ---- HELPERS ----
    protected function generateTripNumber(): string
    {
        $year = now()->format('Y');
        $last = Trip::where('trip_number', 'like', "T{$year}-%")
            ->orderBy('trip_number', 'desc')
            ->lockForUpdate()
            ->first();
        $num = $last ? (int) substr($last->trip_number, 6) + 1 : 1;
        return sprintf('T%s-%04d', $year, $num);
    }

    protected function getPendingTrip(): ?Trip
    {
        if ($this->trip) return $this->trip;
        if (!$this->editing) {
            $t = Trip::create([
                'trip_number' => 'TEMP-' . uniqid(),
                'customer_id' => $this->customer_id ?: null,
                'status' => 'enquiry',
                'created_by' => auth()->id() ?? 1,
            ]);
            $this->trip = $t;
            $this->editing = true;
            return $t;
        }
        return null;
    }

    protected function passengerData(): array
    {
        return [
            'first_name' => $this->p_first_name,
            'last_name' => $this->p_last_name,
            'date_of_birth' => $this->p_date_of_birth ?: null,
            'nationality' => $this->p_nationality ?: null,
            'passport_number' => $this->p_passport_number ?: null,
            'passport_expiry' => $this->p_passport_expiry ?: null,
            'customer_id' => $this->p_customer_id ?: null,
        ];
    }

    protected function resetPassengerForm(): void
    {
        $this->reset(['p_first_name', 'p_last_name', 'p_date_of_birth', 'p_nationality', 'p_passport_number', 'p_passport_expiry', 'p_customer_id', 'showPassengerForm', 'editPassengerId']);
    }

    protected function resetFlightForm(): void
    {
        $this->reset(['f_airline', 'f_flight_number', 'f_departure_airport', 'f_arrival_airport', 'f_departure_datetime', 'f_arrival_datetime', 'f_selling_price', 'f_cost_price', 'f_currency', 'f_status', 'f_supplier_id', 'showFlightForm', 'editFlightId']);
        $this->f_currency = 'USD'; $this->f_status = 'confirmed'; $this->f_selling_price = '0'; $this->f_cost_price = '0';
    }

    protected function resetHotelForm(): void
    {
        $this->reset(['h_hotel_name', 'h_city', 'h_room_type', 'h_check_in', 'h_check_out', 'h_number_of_rooms', 'h_selling_price', 'h_cost_price', 'h_currency', 'h_status', 'h_supplier_id', 'showHotelForm', 'editHotelId']);
        $this->h_currency = 'USD'; $this->h_status = 'confirmed'; $this->h_selling_price = '0'; $this->h_cost_price = '0'; $this->h_number_of_rooms = '1';
    }

    protected function resetTransferForm(): void
    {
        $this->reset(['t_pickup', 't_dropoff', 't_vehicle_type', 't_passengers', 't_pickup_datetime', 't_selling_price', 't_cost_price', 't_currency', 't_supplier_id', 'showTransferForm', 'editTransferId']);
        $this->t_currency = 'USD'; $this->t_selling_price = '0'; $this->t_cost_price = '0'; $this->t_passengers = '1';
    }

    protected function resetVisaForm(): void
    {
        $this->reset(['v_country', 'v_visa_type', 'v_selling_price', 'v_cost_price', 'v_currency', 'showVisaForm', 'editVisaId']);
        $this->v_visa_type = 'tourist'; $this->v_currency = 'USD'; $this->v_selling_price = '0'; $this->v_cost_price = '0';
    }

    protected function resetInsuranceForm(): void
    {
        $this->reset(['i_type', 'i_policy_number', 'i_start_date', 'i_end_date', 'i_selling_price', 'i_cost_price', 'i_currency', 'showInsuranceForm', 'editInsuranceId']);
        $this->i_type = 'travel'; $this->i_currency = 'USD'; $this->i_selling_price = '0'; $this->i_cost_price = '0';
    }

    protected function resetActivityForm(): void
    {
        $this->reset(['a_name', 'a_location', 'a_date', 'a_time', 'a_selling_price', 'a_cost_price', 'a_currency', 'showActivityForm', 'editActivityId']);
        $this->a_currency = 'USD'; $this->a_selling_price = '0'; $this->a_cost_price = '0';
    }

    protected function resetCruiseForm(): void { $this->reset(['cr_cruise_line', 'cr_ship_name', 'cr_cabin_type', 'cr_departure_port', 'cr_arrival_port', 'cr_departure_date', 'cr_arrival_date', 'cr_itinerary', 'cr_selling_price', 'cr_cost_price', 'cr_currency', 'cr_status', 'cr_supplier_id', 'showCruiseForm', 'editCruiseId']); $this->cr_currency = 'USD'; $this->cr_status = 'confirmed'; $this->cr_selling_price = '0'; $this->cr_cost_price = '0'; }
    protected function resetTrainForm(): void { $this->reset(['tr_company', 'tr_train_number', 'tr_departure_station', 'tr_arrival_station', 'tr_departure_datetime', 'tr_arrival_datetime', 'tr_class', 'tr_selling_price', 'tr_cost_price', 'tr_currency', 'tr_status', 'tr_supplier_id', 'showTrainForm', 'editTrainId']); $this->tr_currency = 'USD'; $this->tr_status = 'confirmed'; $this->tr_selling_price = '0'; $this->tr_cost_price = '0'; }
    protected function resetCarForm(): void { $this->reset(['ca_company', 'ca_car_type', 'ca_pickup_location', 'ca_dropoff_location', 'ca_pickup_datetime', 'ca_dropoff_datetime', 'ca_selling_price', 'ca_cost_price', 'ca_currency', 'ca_status', 'ca_supplier_id', 'showCarForm', 'editCarId']); $this->ca_currency = 'USD'; $this->ca_status = 'confirmed'; $this->ca_selling_price = '0'; $this->ca_cost_price = '0'; }
    protected function resetPackageForm(): void { $this->reset(['pk_name', 'pk_type', 'pk_description', 'pk_start_date', 'pk_end_date', 'pk_selling_price', 'pk_cost_price', 'pk_currency', 'pk_status', 'pk_supplier_id', 'showPackageForm', 'editPackageId']); $this->pk_currency = 'USD'; $this->pk_status = 'confirmed'; $this->pk_selling_price = '0'; $this->pk_cost_price = '0'; }
    protected function resetOtherForm(): void { $this->reset(['o_name', 'o_type', 'o_description', 'o_date', 'o_selling_price', 'o_cost_price', 'o_currency', 'o_status', 'o_supplier_id', 'showOtherForm', 'editOtherId']); $this->o_currency = 'USD'; $this->o_status = 'confirmed'; $this->o_selling_price = '0'; $this->o_cost_price = '0'; }

    public function fetchCoordinates(): void
    {
        $dest = trim($this->destination);
        if (!$dest) return;

        $url = 'https://nominatim.openstreetmap.org/search?q=' . urlencode($dest) . '&format=json&limit=1';
        $context = stream_context_create(['http' => ['header' => 'User-Agent: TravelBox/1.0\r\n']]);
        $response = @file_get_contents($url, false, $context);

        if ($response) {
            $data = json_decode($response, true);
            if (!empty($data[0])) {
                $this->latitude = (string) round((float) $data[0]['lat'], 6);
                $this->longitude = (string) round((float) $data[0]['lon'], 6);
                $this->dispatch('coordinatesFetched', lat: $this->latitude, lng: $this->longitude);
            }
        }
    }

    public function render()
    {
        $sup = Supplier::where('is_active', true)->orderBy('company_name')->get();
        return view('livewire.trips.trip-form', [
            'customers' => Customer::where('is_active', true)->orderBy('first_name')->get(),
            'suppliers' => $sup,
        ]);
    }
}
