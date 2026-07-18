<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use App\Services\PassportOcrService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
class CustomerForm extends Component
{
    use WithFileUploads;

    public ?Customer $customer = null;

    public string $type = 'individual';
    public string $first_name = '';
    public string $last_name = '';
    public string $company_name = '';
    public string $email = '';
    public string $phone = '';
    public string $mobile = '';
    public string $address = '';
    public string $city = '';
    public string $country = '';
    public string $nationality = '';
    public string $sex = '';
    public string $passport_number = '';
    public string $passport_expiry = '';
    public string $passport_issue_date = '';
    public string $passport_issue_place = '';
    public string $date_of_birth = '';
    public string $place_of_birth = '';
    public string $preferred_currency = 'USD';
    public string $favorite_destinations = '';
    public string $visa_info = '';
    public string $notes = '';
    public string $credit_limit = '0';
    public int $loyalty_points = 0;
    public bool $is_active = true;

    public $passportUpload = null;
    public ?string $passportPath = null;
    public string $passportOcrStatus = 'idle';
    public array $passportOcrResults = [];

    public bool $editing = false;

    public function mount(?Customer $customer = null): void
    {
        $this->customer = $customer;

        if ($customer) {
            $this->editing = true;
            $this->type = $customer->type;
            $this->first_name = $customer->first_name;
            $this->last_name = $customer->last_name;
            $this->company_name = $customer->company_name ?? '';
            $this->email = $customer->email ?? '';
            $this->phone = $customer->phone ?? '';
            $this->mobile = $customer->mobile ?? '';
            $this->address = $customer->address ?? '';
            $this->city = $customer->city ?? '';
            $this->country = $customer->country ?? '';
            $this->nationality = $customer->nationality ?? '';
            $this->sex = $customer->sex ?? '';
            $this->passport_number = $customer->passport_number ?? '';
            $this->passport_expiry = $customer->passport_expiry ?? '';
            $this->passport_issue_date = $customer->passport_issue_date ?? '';
            $this->passport_issue_place = $customer->passport_issue_place ?? '';
            $this->date_of_birth = $customer->date_of_birth ?? '';
            $this->place_of_birth = $customer->place_of_birth ?? '';
            $this->preferred_currency = $customer->preferred_currency;
            $this->favorite_destinations = $customer->favorite_destinations ?? '';
            $this->visa_info = $customer->visa_info ? json_encode($customer->visa_info, JSON_PRETTY_PRINT) : '';
            $this->notes = $customer->notes ?? '';
            $this->credit_limit = (string) $customer->credit_limit;
            $this->loyalty_points = $customer->loyalty_points ?? 0;
            $this->is_active = $customer->is_active;
        }
    }

    public function rules(): array
    {
        $rules = [
            'type' => 'required|in:individual,company',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:255',
            'sex' => 'nullable|string|in:Male,Female',
            'passport_number' => 'nullable|string|max:50',
            'passport_expiry' => 'nullable|date',
            'passport_issue_date' => 'nullable|date',
            'passport_issue_place' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'place_of_birth' => 'nullable|string|max:255',
            'preferred_currency' => 'required|string|size:3',
            'favorite_destinations' => 'nullable|string',
            'visa_info' => 'nullable|string',
            'notes' => 'nullable|string',
            'credit_limit' => 'nullable|numeric|min:0',
            'loyalty_points' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ];

        if ($this->editing && $this->customer) {
            $rules['email'] = 'nullable|email|max:255|unique:customers,email,' . $this->customer->id;
        } else {
            $rules['email'] = 'nullable|email|max:255|unique:customers,email';
        }

        return $rules;
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
                $this->dispatch('notify', type: 'error', message: $results['error']);
                return;
            }
            $this->passportOcrResults = $results;
            $this->passportOcrStatus = 'done';
        } catch (\Exception $e) {
            $this->passportOcrStatus = 'error';
            $this->dispatch('notify', type: 'error', message: 'Passport scan failed: ' . $e->getMessage());
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
        if (!empty($r['sex'])) $this->sex = $r['sex'];
        $this->removePassportUpload();
        $this->dispatch('notify', type: 'success', message: 'Passport details applied!');
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

    public function save(): void
    {
        $this->validate();

        $data = [
            'type' => $this->type,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'company_name' => $this->company_name ?: null,
            'email' => $this->email ?: null,
            'phone' => $this->phone ?: null,
            'mobile' => $this->mobile ?: null,
            'address' => $this->address ?: null,
            'city' => $this->city ?: null,
            'country' => $this->country ?: null,
            'nationality' => $this->nationality ?: null,
            'sex' => $this->sex ?: null,
            'passport_number' => $this->passport_number ?: null,
            'passport_expiry' => $this->passport_expiry ?: null,
            'passport_issue_date' => $this->passport_issue_date ?: null,
            'passport_issue_place' => $this->passport_issue_place ?: null,
            'date_of_birth' => $this->date_of_birth ?: null,
            'place_of_birth' => $this->place_of_birth ?: null,
            'preferred_currency' => $this->preferred_currency,
            'favorite_destinations' => $this->favorite_destinations ?: null,
            'visa_info' => $this->visa_info ? json_decode($this->visa_info, true) : null,
            'notes' => $this->notes ?: null,
            'credit_limit' => (float) $this->credit_limit,
            'loyalty_points' => (int) $this->loyalty_points,
            'is_active' => $this->is_active,
        ];

        if ($this->editing && $this->customer) {
            $this->customer->update($data);
            $this->dispatch('notify', type: 'success', title: 'Customer Updated', message: 'Customer updated successfully.');
            $this->redirect(route('customers.show', $this->customer), navigate: true);
        } else {
            $data['customer_code'] = $this->generateCustomerCode();
            $data['created_by'] = auth()->id() ?? 1;
            $customer = Customer::create($data);
            $this->dispatch('notify', type: 'success', title: 'Customer Created', message: 'Customer created successfully.');
            $this->redirect(route('customers.show', $customer), navigate: true);
        }
    }

    protected function generateCustomerCode(): string
    {
        $prefix = match ($this->type) {
            'company' => 'CMP',
            default => 'IND',
        };

        $last = Customer::where('customer_code', 'like', "{$prefix}-%")
            ->orderBy('customer_code', 'desc')
            ->first();

        if ($last) {
            $num = (int) substr($last->customer_code, 4) + 1;
        } else {
            $num = 1;
        }

        return sprintf('%s-%04d', $prefix, $num);
    }

    public function render()
    {
        return view('livewire.customers.customer-form');
    }
}
