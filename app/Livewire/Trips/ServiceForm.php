<?php

namespace App\Livewire\Trips;

use App\Models\Trip;
use App\Models\Supplier;
use App\Models\FlightSegment;
use App\Models\HotelBooking;
use App\Models\TransferBooking;
use App\Models\VisaApplication;
use App\Models\InsurancePolicy;
use App\Models\Activity;
use App\Services\FlightOcrService;
use App\Services\FlightParserService;
use App\Services\AccountingService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;

class ServiceForm extends Component
{
    use WithFileUploads;
    public Trip $trip;
    public string $serviceType = '';
    public bool $showModal = false;
    public ?string $editingId = null;

    // Flight
    public string $flight_booking_type = 'one_way';
    public array $flight_segments = [];

    // Hotel
    public string $hotel_name = '';
    public string $hotel_city = '';
    public string $check_in = '';
    public string $check_out = '';
    public string $room_type = '';
    public string $meal_plan = '';
    public string $number_of_rooms = '1';

    // Transfer
    public string $transfer_type = 'arrival';
    public string $pickup_location = '';
    public string $dropoff_location = '';
    public string $pickup_datetime = '';
    public string $vehicle_type = '';
    public string $number_of_passengers = '1';

    // Visa
    public string $visa_country = '';
    public string $visa_type = 'tourist';
    public string $application_date = '';
    public string $expected_delivery_date = '';

    // Insurance
    public string $policy_number = '';
    public string $insurance_type = 'travel';
    public string $coverage_details = '';
    public string $insurance_start_date = '';
    public string $insurance_end_date = '';

    // Activity
    public string $activity_name = '';
    public string $activity_type = '';
    public string $activity_location = '';
    public string $activity_date = '';
    public string $activity_time = '';
    public string $duration = '';
    public string $number_of_participants = '1';

    // File upload
    public $confirmationUpload = null;
    public ?string $confirmationPath = null;
    public ?string $confirmationName = null;

    // OCR
    public string $ocrStatus = 'idle';
    public ?string $ocrRawText = null;
    public array $ocrResults = [];

    // Common
    public string $supplier_id = '';
    public string $service_status = 'confirmed';
    public string $currency = 'USD';
    public string $cost_price = '0';
    public string $selling_price = '0';
    public string $service_notes = '';
    public string $booking_reference = '';

    #[On('open-service-modal')]
    public function openModal(string $type): void
    {
        $this->serviceType = $type;
        $this->resetForm();
        if ($type === 'flight') {
            $this->initFlightSegments(1);
        }
        $this->showModal = true;
    }

    protected function initFlightSegments(int $count): void
    {
        $this->flight_segments = [];
        for ($i = 0; $i < $count; $i++) {
            $this->flight_segments[] = $this->emptySegment();
        }
    }

    protected function emptySegment(): array
    {
        return [
            'airline' => '',
            'flight_number' => '',
            'departure_airport' => '',
            'arrival_airport' => '',
            'departure_datetime' => '',
            'arrival_datetime' => '',
            'booking_reference' => '',
            'ticket_number' => '',
            'class' => 'economy',
        ];
    }

    public function updatedFlightBookingType($value): void
    {
        if ($value === 'one_way') {
            $this->flight_segments = count($this->flight_segments) > 0
                ? [array_merge($this->emptySegment(), $this->flight_segments[0])]
                : [$this->emptySegment()];
        } elseif ($value === 'round_trip') {
            while (count($this->flight_segments) < 2) {
                $this->flight_segments[] = $this->emptySegment();
            }
            $this->flight_segments = array_slice($this->flight_segments, 0, 2);
        }
    }

    public function updatedFlightSegments($value, $key): void
    {
        if (!str_ends_with((string)$key, '.flight_number')) {
            return;
        }
        $idx = explode('.', (string)$key)[0];
        $code = strtoupper(substr($value, 0, 2));
        $airlines = [
            'TK' => 'Turkish Airlines',
            'PC' => 'Pegasus',
            'EK' => 'Emirates',
            'QR' => 'Qatar Airways',
            'EY' => 'Etihad',
            'SV' => 'Saudia',
            'WY' => 'Oman Air',
            'GF' => 'Gulf Air',
            'KU' => 'Kuwait Airways',
            'RJ' => 'Royal Jordanian',
            'FZ' => 'Flydubai',
            'G9' => 'Air Arabia',
            'J9' => 'Jazeera Airways',
            'MS' => 'EgyptAir',
            'SM' => 'Air Cairo',
            'ME' => 'Middle East Airlines',
            'LH' => 'Lufthansa',
            'BA' => 'British Airways',
            'AF' => 'Air France',
            'KL' => 'KLM',
            'IB' => 'Iberia',
            'AZ' => 'ITA Airways',
            'A3' => 'Aegean Airlines',
            'RO' => 'Tarom',
            'NH' => 'All Nippon Airways',
            'SQ' => 'Singapore Airlines',
            'CX' => 'Cathay Pacific',
            'JL' => 'Japan Airlines',
            'KE' => 'Korean Air',
            'CA' => 'Air China',
            'MU' => 'China Eastern',
            'CZ' => 'China Southern',
        ];
        if (isset($airlines[$code]) && empty($this->flight_segments[$idx]['airline'])) {
            $this->flight_segments[$idx]['airline'] = $airlines[$code];
        }
    }

    public function addSegment(): void
    {
        $this->flight_segments[] = $this->emptySegment();
    }

    public function removeSegment(int $index): void
    {
        if (count($this->flight_segments) > 1) {
            unset($this->flight_segments[$index]);
            $this->flight_segments = array_values($this->flight_segments);
        }
    }

    #[On('edit-service')]
    public function editService(string $type, string $id): void
    {
        $this->serviceType = $type;
        $this->editingId = $id;
        $this->showModal = true;

        switch ($type) {
            case 'flight':
                $s = FlightSegment::findOrFail($id);
                $this->flight_segments = [[
                    'airline' => $s->airline,
                    'flight_number' => $s->flight_number,
                    'departure_airport' => $s->departure_airport,
                    'arrival_airport' => $s->arrival_airport,
                    'departure_datetime' => $s->departure_datetime?->format('Y-m-d\TH:i') ?? '',
                    'arrival_datetime' => $s->arrival_datetime?->format('Y-m-d\TH:i') ?? '',
                    'booking_reference' => $s->booking_reference ?? '',
                    'ticket_number' => $s->ticket_number ?? '',
                    'class' => $s->class ?? 'economy',
                ]];
                $this->flight_booking_type = 'one_way';
                $this->supplier_id = $s->supplier_id ?? '';
                $this->cost_price = (string) $s->cost_price;
                $this->selling_price = (string) $s->selling_price;
                $this->currency = $s->currency;
                $this->service_status = $s->status;
                $this->service_notes = $s->notes ?? '';
                break;
            case 'hotel':
                $s = HotelBooking::findOrFail($id);
                $this->hotel_name = $s->hotel_name;
                $this->hotel_city = $s->city ?? '';
                $this->check_in = $s->check_in?->format('Y-m-d') ?? '';
                $this->check_out = $s->check_out?->format('Y-m-d') ?? '';
                $this->room_type = $s->room_type ?? '';
                $this->meal_plan = $s->meal_plan ?? '';
                $this->number_of_rooms = (string) ($s->number_of_rooms ?? 1);
                $this->booking_reference = $s->booking_reference ?? '';
                $this->supplier_id = $s->supplier_id ?? '';
                $this->cost_price = (string) $s->cost_price;
                $this->selling_price = (string) $s->selling_price;
                $this->currency = $s->currency;
                $this->service_status = $s->status;
                $this->service_notes = $s->notes ?? '';
                break;
            case 'transfer':
                $s = TransferBooking::findOrFail($id);
                $this->transfer_type = $s->type ?? 'arrival';
                $this->pickup_location = $s->pickup_location;
                $this->dropoff_location = $s->dropoff_location;
                $this->pickup_datetime = $s->pickup_datetime?->format('Y-m-d\TH:i') ?? '';
                $this->vehicle_type = $s->vehicle_type ?? '';
                $this->number_of_passengers = (string) ($s->number_of_passengers ?? 1);
                $this->booking_reference = $s->booking_reference ?? '';
                $this->supplier_id = $s->supplier_id ?? '';
                $this->cost_price = (string) $s->cost_price;
                $this->selling_price = (string) $s->selling_price;
                $this->currency = $s->currency;
                $this->service_status = $s->status;
                $this->service_notes = $s->notes ?? '';
                break;
            case 'visa':
                $s = VisaApplication::findOrFail($id);
                $this->visa_country = $s->country;
                $this->visa_type = $s->visa_type ?? 'tourist';
                $this->application_date = $s->application_date?->format('Y-m-d') ?? '';
                $this->expected_delivery_date = $s->expected_delivery_date?->format('Y-m-d') ?? '';
                $this->supplier_id = $s->supplier_id ?? '';
                $this->cost_price = (string) $s->cost_price;
                $this->selling_price = (string) $s->selling_price;
                $this->currency = $s->currency;
                $this->service_status = $s->status;
                $this->service_notes = $s->notes ?? '';
                break;
            case 'insurance':
                $s = InsurancePolicy::findOrFail($id);
                $this->policy_number = $s->policy_number ?? '';
                $this->insurance_type = $s->type ?? 'travel';
                $this->coverage_details = $s->coverage_details ?? '';
                $this->insurance_start_date = $s->start_date?->format('Y-m-d') ?? '';
                $this->insurance_end_date = $s->end_date?->format('Y-m-d') ?? '';
                $this->supplier_id = $s->supplier_id ?? '';
                $this->cost_price = (string) $s->cost_price;
                $this->selling_price = (string) $s->selling_price;
                $this->currency = $s->currency;
                $this->service_status = $s->status;
                $this->service_notes = $s->notes ?? '';
                break;
            case 'activity':
                $s = Activity::findOrFail($id);
                $this->activity_name = $s->name;
                $this->activity_type = $s->type ?? '';
                $this->activity_location = $s->location ?? '';
                $this->activity_date = $s->date?->format('Y-m-d') ?? '';
                $this->activity_time = $s->time?->format('H:i') ?? '';
                $this->duration = $s->duration ?? '';
                $this->number_of_participants = (string) ($s->number_of_participants ?? 1);
                $this->booking_reference = $s->booking_reference ?? '';
                $this->supplier_id = $s->supplier_id ?? '';
                $this->cost_price = (string) $s->cost_price;
                $this->selling_price = (string) $s->selling_price;
                $this->currency = $s->currency;
                $this->service_status = $s->status;
                $this->service_notes = $s->notes ?? '';
                break;
        }
    }

    #[On('delete-service')]
    public function deleteService(string $type, string $id): void
    {
        $model = match ($type) {
            'flight' => FlightSegment::findOrFail($id),
            'hotel' => HotelBooking::findOrFail($id),
            'transfer' => TransferBooking::findOrFail($id),
            'visa' => VisaApplication::findOrFail($id),
            'insurance' => InsurancePolicy::findOrFail($id),
            'activity' => Activity::findOrFail($id),
        };
        $label = match ($type) {
            'flight' => 'Flight '.$model->airline.' '.$model->flight_number,
            'hotel' => 'Hotel '.$model->hotel_name,
            'transfer' => 'Transfer '.$model->pickup_location.' → '.$model->dropoff_location,
            'visa' => 'Visa '.$model->country,
            'insurance' => 'Insurance '.($model->policy_number ?: ''),
            'activity' => 'Activity '.$model->name,
        };
        $model->delete();
        $this->trip->load([
            'flightSegments', 'hotelBookings', 'transferBookings',
            'visaApplications', 'insurancePolicies', 'activities',
        ]);
        $this->trip->recalculateTotals();
        $this->trip->logTimeline('service_deleted', "Deleted {$label}");
        $this->dispatch('service-saved');
    }

    public function removeConfirmation(): void
    {
        if ($this->confirmationPath) {
            Storage::disk('public')->delete($this->confirmationPath);
        }
        $this->confirmationUpload = null;
        $this->confirmationPath = null;
        $this->confirmationName = null;
        $this->ocrStatus = 'idle';
        $this->ocrRawText = null;
        $this->ocrResults = [];
    }

    public function updatedConfirmationUpload(): void
    {
        $this->validate([
            'confirmationUpload' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);
        $this->confirmationPath = $this->confirmationUpload->store('temp-confirmations', 'public');
        $this->confirmationName = $this->confirmationUpload->getClientOriginalName();
        $this->ocrStatus = 'idle';
        $this->ocrRawText = null;
        $this->ocrResults = [];
    }

    public function scanConfirmation(): void
    {
        if (!$this->confirmationPath) return;

        $this->ocrStatus = 'processing';

        try {
            $ocr = app(FlightOcrService::class);
            $text = $ocr->extractText($this->confirmationPath);

            if (!$text || trim($text) === '') {
                $this->ocrStatus = 'error';
                $this->dispatch('notify', 'Could not extract text from this file. Try a clearer image or PDF.');
                return;
            }

            $parser = app(FlightParserService::class);
            $this->ocrResults = $parser->parse($text);
            $this->ocrRawText = $text;
            $this->ocrStatus = 'done';
        } catch (\Exception $e) {
            $this->ocrStatus = 'error';
            $this->dispatch('notify', 'OCR failed: ' . $e->getMessage());
        }
    }

    public function applyOcrResults(): void
    {
        if (empty($this->ocrResults['segments'])) return;

        $segments = [];
        foreach ($this->ocrResults['segments'] as $seg) {
            $segments[] = [
                'airline' => $seg['airline'] ?? '',
                'flight_number' => $seg['flight_number'] ?? '',
                'departure_airport' => strtoupper($seg['departure_airport'] ?? ''),
                'arrival_airport' => strtoupper($seg['arrival_airport'] ?? ''),
                'departure_datetime' => $seg['departure_datetime'] ?? '',
                'arrival_datetime' => $seg['arrival_datetime'] ?? '',
                'booking_reference' => $this->ocrResults['booking_reference'] ?? $seg['booking_reference'] ?? '',
                'ticket_number' => '',
                'class' => 'economy',
            ];
        }

        if (count($segments) > 0) {
            $this->flight_segments = $segments;
            if (count($segments) >= 2) {
                $this->flight_booking_type = 'round_trip';
            } elseif (count($segments) === 1) {
                $this->flight_booking_type = 'one_way';
            } else {
                $this->flight_booking_type = 'multi_city';
            }
            $this->booking_reference = $this->ocrResults['booking_reference'] ?? $segments[0]['booking_reference'] ?? '';
            $this->dispatch('notify', 'Flight details applied from OCR! Review and save.');
        }
    }

    protected function storeConfirmationFile(): void
    {
        if (!$this->confirmationPath || !Storage::disk('public')->exists($this->confirmationPath)) {
            return;
        }
        $mime = Storage::disk('public')->mimeType($this->confirmationPath);
        $size = Storage::disk('public')->size($this->confirmationPath);
        $type = str_starts_with($mime, 'image/') ? 'booking_confirmation' : 'booking_confirmation';
        $label = match ($this->serviceType) {
            'flight' => 'Flight',
            'hotel' => 'Hotel',
            'transfer' => 'Transfer',
            'visa' => 'Visa',
            'insurance' => 'Insurance',
            'activity' => 'Activity',
        };
        $newPath = 'trip-files/'.$this->trip->id.'/'.$this->confirmationName ?? basename($this->confirmationPath);
        Storage::disk('public')->move($this->confirmationPath, $newPath);
        Document::create([
            'trip_id' => $this->trip->id,
            'type' => $type,
            'title' => "{$label} Confirmation – {$this->confirmationName}",
            'file_path' => $newPath,
            'mime_type' => $mime,
            'size' => $size,
        ]);
        $this->confirmationPath = null;
        $this->confirmationName = null;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->removeConfirmation();
        $this->editingId = null;
        $this->flight_segments = [];
        $this->flight_booking_type = 'one_way';
        $this->reset([
            'hotel_name', 'hotel_city', 'check_in', 'check_out', 'room_type', 'meal_plan', 'number_of_rooms',
            'transfer_type', 'pickup_location', 'dropoff_location', 'pickup_datetime', 'vehicle_type', 'number_of_passengers',
            'visa_country', 'visa_type', 'application_date', 'expected_delivery_date',
            'policy_number', 'insurance_type', 'coverage_details', 'insurance_start_date', 'insurance_end_date',
            'activity_name', 'activity_type', 'activity_location', 'activity_date', 'activity_time', 'duration', 'number_of_participants',
            'supplier_id', 'service_status', 'currency', 'cost_price', 'selling_price', 'service_notes', 'booking_reference',
        ]);
    }

    public function save(): void
    {
        $rules = match ($this->serviceType) {
            'flight' => [
                'flight_segments' => 'required|array|min:1',
                'flight_segments.*.airline' => 'required|string|max:255',
                'flight_segments.*.flight_number' => 'required|string|max:50',
                'flight_segments.*.departure_airport' => 'required|string|max:10',
                'flight_segments.*.arrival_airport' => 'required|string|max:10',
                'cost_price' => 'numeric|min:0',
                'selling_price' => 'numeric|min:0',
            ],
            'hotel' => [
                'hotel_name' => 'required|string|max:255',
                'check_in' => 'nullable|date',
                'check_out' => 'nullable|date|after_or_equal:check_in',
                'cost_price' => 'numeric|min:0',
                'selling_price' => 'numeric|min:0',
            ],
            'transfer' => [
                'pickup_location' => 'required|string|max:255',
                'dropoff_location' => 'required|string|max:255',
                'cost_price' => 'numeric|min:0',
                'selling_price' => 'numeric|min:0',
            ],
            'visa' => [
                'visa_country' => 'required|string|max:255',
                'cost_price' => 'numeric|min:0',
                'selling_price' => 'numeric|min:0',
            ],
            'insurance' => [
                'policy_number' => 'nullable|string|max:255',
                'cost_price' => 'numeric|min:0',
                'selling_price' => 'numeric|min:0',
            ],
            'activity' => [
                'activity_name' => 'required|string|max:255',
                'cost_price' => 'numeric|min:0',
                'selling_price' => 'numeric|min:0',
            ],
            default => [],
        };
        $this->validate($rules);

        $common = [
            'supplier_id' => $this->supplier_id ?: null,
            'status' => $this->service_status,
            'currency' => $this->currency,
            'cost_price' => (float) $this->cost_price,
            'selling_price' => (float) $this->selling_price,
            'notes' => $this->service_notes ?: null,
        ];

        $data = match ($this->serviceType) {
            'flight' => $common + [
                // will be handled per-segment below
            ],
            'hotel' => $common + [
                'hotel_name' => $this->hotel_name,
                'city' => $this->hotel_city ?: null,
                'check_in' => $this->check_in ?: null,
                'check_out' => $this->check_out ?: null,
                'room_type' => $this->room_type ?: null,
                'meal_plan' => $this->meal_plan ?: null,
                'number_of_rooms' => (int) $this->number_of_rooms,
                'booking_reference' => $this->booking_reference ?: null,
            ],
            'transfer' => $common + [
                'type' => $this->transfer_type,
                'pickup_location' => $this->pickup_location,
                'dropoff_location' => $this->dropoff_location,
                'pickup_datetime' => $this->pickup_datetime ?: null,
                'vehicle_type' => $this->vehicle_type ?: null,
                'number_of_passengers' => (int) $this->number_of_passengers,
                'booking_reference' => $this->booking_reference ?: null,
            ],
            'visa' => $common + [
                'country' => $this->visa_country,
                'visa_type' => $this->visa_type,
                'application_date' => $this->application_date ?: null,
                'expected_delivery_date' => $this->expected_delivery_date ?: null,
            ],
            'insurance' => $common + [
                'policy_number' => $this->policy_number ?: null,
                'type' => $this->insurance_type,
                'coverage_details' => $this->coverage_details ?: null,
                'start_date' => $this->insurance_start_date ?: null,
                'end_date' => $this->insurance_end_date ?: null,
            ],
            'activity' => $common + [
                'name' => $this->activity_name,
                'type' => $this->activity_type ?: null,
                'location' => $this->activity_location ?: null,
                'date' => $this->activity_date ?: null,
                'time' => $this->activity_time ?: null,
                'duration' => $this->duration ?: null,
                'number_of_participants' => (int) $this->number_of_participants,
                'booking_reference' => $this->booking_reference ?: null,
            ],
            default => [],
        };

        if ($this->editingId) {
            $model = match ($this->serviceType) {
                'flight' => FlightSegment::findOrFail($this->editingId),
                'hotel' => HotelBooking::findOrFail($this->editingId),
                'transfer' => TransferBooking::findOrFail($this->editingId),
                'visa' => VisaApplication::findOrFail($this->editingId),
                'insurance' => InsurancePolicy::findOrFail($this->editingId),
                'activity' => Activity::findOrFail($this->editingId),
            };
            $model->update($data);
            $this->trip->logTimeline('service_edited', "Updated {$this->serviceType} service");
            $this->storeConfirmationFile();
        } else {
            if ($this->serviceType === 'flight') {
                foreach ($this->flight_segments as $segment) {
                    FlightSegment::create([
                        'trip_id' => $this->trip->id,
                        'type' => 'departure',
                        'airline' => $segment['airline'],
                        'flight_number' => $segment['flight_number'],
                        'departure_airport' => $segment['departure_airport'],
                        'arrival_airport' => $segment['arrival_airport'],
                        'departure_datetime' => $segment['departure_datetime'] ?: null,
                        'arrival_datetime' => $segment['arrival_datetime'] ?: null,
                        'booking_reference' => $segment['booking_reference'] ?: null,
                        'ticket_number' => $segment['ticket_number'] ?: null,
                        'class' => $segment['class'] ?? 'economy',
                        'supplier_id' => $this->supplier_id ?: null,
                        'status' => $this->service_status,
                        'currency' => $this->currency,
                        'cost_price' => (float) $this->cost_price,
                        'selling_price' => (float) $this->selling_price,
                        'notes' => $this->service_notes ?: null,
                    ]);
                }
                $count = count($this->flight_segments);
                $this->trip->logTimeline('service_added', "Added {$count} flight segment(s)");
            } else {
                $data['trip_id'] = $this->trip->id;
                match ($this->serviceType) {
                    'hotel' => HotelBooking::create($data),
                    'transfer' => TransferBooking::create($data),
                    'visa' => VisaApplication::create($data),
                    'insurance' => InsurancePolicy::create($data),
                    'activity' => Activity::create($data),
                };
                $this->trip->logTimeline('service_added', "Added {$this->serviceType} service");
            }
            $this->storeConfirmationFile();
        }

        $this->trip->load([
            'flightSegments', 'hotelBookings', 'transferBookings',
            'visaApplications', 'insurancePolicies', 'activities',
        ]);
        $this->trip->recalculateTotals();

        $this->closeModal();
        $this->dispatch('service-saved');
    }

    public function render()
    {
        return view('livewire.trips.service-form', [
            'suppliers' => Supplier::where('is_active', true)->orderBy('company_name')->get(),
        ]);
    }
}
