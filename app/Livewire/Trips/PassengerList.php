<?php

namespace App\Livewire\Trips;

use App\Models\Trip;
use App\Models\Passenger;
use App\Models\Customer;
use App\Services\PassportOcrService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Storage;

class PassengerList extends Component
{
    use WithFileUploads;

    public Trip $trip;

    public string $first_name = '';
    public string $last_name = '';
    public string $date_of_birth = '';
    public string $nationality = '';
    public string $passport_number = '';
    public string $passport_expiry = '';
    public string $passport_issue_date = '';
    public string $passport_issue_place = '';
    public string $customer_id = '';

    public string $meal_preference = '';
    public string $seat_preference = '';
    public string $ffp_number = '';
    public string $ffp_airline = '';
    public string $special_requests = '';

    public bool $showForm = false;
    public ?string $editingPassengerId = null;

    // Passport scan
    public $passportUpload = null;
    public ?string $passportPath = null;
    public string $passportOcrStatus = 'idle';
    public array $passportOcrResults = [];

    public function mount(Trip $trip): void
    {
        $this->trip = $trip->load('passengers');
    }

    public function updatedPassportUpload(): void
    {
        $this->validate([
            'passportUpload' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);
        $this->passportPath = $this->passportUpload->store('temp-passports', 'public');
        $this->passportOcrStatus = 'idle';
        $this->passportOcrResults = [];
    }

    public function scanPassport(): void
    {
        if (!$this->passportPath) return;
        $this->passportOcrStatus = 'processing';
        try {
            $service = app(PassportOcrService::class);
            $results = $service->extractAndParse($this->passportPath);
            if (!empty($results['error'])) {
                $this->passportOcrStatus = 'error';
                $this->dispatch('notify', $results['error']);
                return;
            }
            $this->passportOcrResults = $results;
            $this->passportOcrStatus = 'done';
        } catch (\Exception $e) {
            $this->passportOcrStatus = 'error';
            $this->dispatch('notify', 'Passport scan failed: ' . $e->getMessage());
        }
    }

    public function applyPassportOcr(): void
    {
        $r = $this->passportOcrResults;
        if (empty($r)) return;
        if (!empty($r['first_name'])) $this->first_name = $r['first_name'];
        if (!empty($r['last_name'])) $this->last_name = $r['last_name'];
        if (!empty($r['date_of_birth'])) $this->date_of_birth = $r['date_of_birth'];
        if (!empty($r['nationality'])) $this->nationality = $r['nationality'];
        if (!empty($r['passport_number'])) $this->passport_number = $r['passport_number'];
        if (!empty($r['passport_expiry'])) $this->passport_expiry = $r['passport_expiry'];
        $this->removePassportUpload();
        $this->dispatch('notify', 'Passport details applied!');
    }

    public function removePassportUpload(): void
    {
        if ($this->passportPath) {
            Storage::disk('public')->delete($this->passportPath);
        }
        $this->passportUpload = null;
        $this->passportPath = null;
        $this->passportOcrStatus = 'idle';
        $this->passportOcrResults = [];
    }

    #[On('open-passenger-modal')]
    public function openForm(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    #[On('edit-passenger')]
    public function editPassenger($params): void
    {
        $id = is_string($params) ? $params : ($params['passengerId'] ?? null);
        if ($id) $this->edit($id);
    }

    #[On('delete-passenger')]
    public function deletePassenger($params): void
    {
        $id = is_string($params) ? $params : ($params['passengerId'] ?? null);
        if ($id) $this->remove($id);
    }

    private function resetForm(): void
    {
        $this->reset(['first_name', 'last_name', 'date_of_birth', 'nationality', 'passport_number',
            'passport_expiry', 'passport_issue_date', 'passport_issue_place', 'customer_id',
            'showForm', 'editingPassengerId', 'meal_preference', 'seat_preference',
            'ffp_number', 'ffp_airline', 'special_requests']);
        $this->removePassportUpload();
    }

    public function closeModal(): void
    {
        $this->showForm = false;
        $this->editingPassengerId = null;
        $this->removePassportUpload();
    }

    public function add(): void
    {
        $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'nationality' => 'nullable|string|max:255',
            'passport_number' => 'nullable|string|max:50',
            'passport_expiry' => 'nullable|date',
            'passport_issue_date' => 'nullable|date',
            'passport_issue_place' => 'nullable|string|max:255',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        $this->trip->passengers()->create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'date_of_birth' => $this->date_of_birth ?: null,
            'nationality' => $this->nationality ?: null,
            'passport_number' => $this->passport_number ?: null,
            'passport_expiry' => $this->passport_expiry ?: null,
            'passport_issue_date' => $this->passport_issue_date ?: null,
            'passport_issue_place' => $this->passport_issue_place ?: null,
            'customer_id' => $this->customer_id ?: null,
            'meal_preference' => $this->meal_preference ?: null,
            'seat_preference' => $this->seat_preference ?: null,
            'ffp_number' => $this->ffp_number ?: null,
            'ffp_airline' => $this->ffp_airline ?: null,
            'special_requests' => $this->special_requests ?: null,
        ]);

        $name = $this->first_name.' '.$this->last_name;
        $this->reset(['first_name', 'last_name', 'date_of_birth', 'nationality', 'passport_number', 'passport_expiry', 'passport_issue_date', 'passport_issue_place', 'customer_id', 'showForm', 'meal_preference', 'seat_preference', 'ffp_number', 'ffp_airline', 'special_requests']);
        $this->removePassportUpload();
        $this->trip->load('passengers');
        $this->trip->logTimeline('passenger_added', "Added passenger {$name}");
        $this->dispatch('passenger-updated');
    }

    public function edit(string $passengerId): void
    {
        $p = Passenger::findOrFail($passengerId);
        $this->editingPassengerId = $passengerId;
        $this->first_name = $p->first_name;
        $this->last_name = $p->last_name;
        $this->date_of_birth = $p->date_of_birth?->format('Y-m-d') ?? '';
        $this->nationality = $p->nationality ?? '';
        $this->passport_number = $p->passport_number ?? '';
        $this->passport_expiry = $p->passport_expiry?->format('Y-m-d') ?? '';
        $this->passport_issue_date = $p->passport_issue_date?->format('Y-m-d') ?? '';
        $this->passport_issue_place = $p->passport_issue_place ?? '';
        $this->customer_id = $p->customer_id ?? '';
        $this->meal_preference = $p->meal_preference ?? '';
        $this->seat_preference = $p->seat_preference ?? '';
        $this->ffp_number = $p->ffp_number ?? '';
        $this->ffp_airline = $p->ffp_airline ?? '';
        $this->special_requests = $p->special_requests ?? '';
        $this->showForm = true;
    }

    public function update(): void
    {
        $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'nationality' => 'nullable|string|max:255',
            'passport_number' => 'nullable|string|max:50',
            'passport_expiry' => 'nullable|date',
            'passport_issue_date' => 'nullable|date',
            'passport_issue_place' => 'nullable|string|max:255',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        $p = Passenger::findOrFail($this->editingPassengerId);
        $p->update([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'date_of_birth' => $this->date_of_birth ?: null,
            'nationality' => $this->nationality ?: null,
            'passport_number' => $this->passport_number ?: null,
            'passport_expiry' => $this->passport_expiry ?: null,
            'passport_issue_date' => $this->passport_issue_date ?: null,
            'passport_issue_place' => $this->passport_issue_place ?: null,
            'customer_id' => $this->customer_id ?: null,
            'meal_preference' => $this->meal_preference ?: null,
            'seat_preference' => $this->seat_preference ?: null,
            'ffp_number' => $this->ffp_number ?: null,
            'ffp_airline' => $this->ffp_airline ?: null,
            'special_requests' => $this->special_requests ?: null,
        ]);

        $name = $this->first_name.' '.$this->last_name;
        $this->reset(['first_name', 'last_name', 'date_of_birth', 'nationality', 'passport_number', 'passport_expiry', 'passport_issue_date', 'passport_issue_place', 'customer_id', 'showForm', 'editingPassengerId', 'meal_preference', 'seat_preference', 'ffp_number', 'ffp_airline', 'special_requests']);
        $this->removePassportUpload();
        $this->trip->load('passengers');
        $this->trip->logTimeline('passenger_edited', "Updated passenger {$name}");
        $this->dispatch('passenger-updated');
    }

    public function remove(string $passengerId): void
    {
        $p = Passenger::findOrFail($passengerId);
        $name = $p->full_name;
        $p->delete();
        $this->trip->load('passengers');
        $this->trip->logTimeline('passenger_removed', "Removed passenger {$name}");
        $this->dispatch('passenger-updated');
    }

    public function render()
    {
        return view('livewire.trips.passenger-list', [
            'customers' => Customer::where('is_active', true)->orderBy('first_name')->get(),
        ]);
    }
}
