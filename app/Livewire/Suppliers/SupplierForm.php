<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class SupplierForm extends Component
{
    public ?Supplier $supplier = null;
    public bool $editing = false;

    public string $type = 'hotel';
    public string $company_name = '';
    public string $contact_person = '';
    public string $email = '';
    public string $phone = '';
    public string $mobile = '';
    public string $address = '';
    public string $city = '';
    public string $country = '';
    public string $preferred_currency = 'USD';
    public string $payment_terms = '';
    public string $contract_notes = '';
    public string $current_balance = '0';
    public bool $is_active = true;

    public function mount(?Supplier $supplier = null): void
    {
        $this->supplier = $supplier;
        if ($supplier) {
            $this->editing = true;
            $this->type = $supplier->type;
            $this->company_name = $supplier->company_name;
            $this->contact_person = $supplier->contact_person ?? '';
            $this->email = $supplier->email ?? '';
            $this->phone = $supplier->phone ?? '';
            $this->mobile = $supplier->mobile ?? '';
            $this->address = $supplier->address ?? '';
            $this->city = $supplier->city ?? '';
            $this->country = $supplier->country ?? '';
            $this->preferred_currency = $supplier->preferred_currency;
            $this->payment_terms = $supplier->payment_terms ?? '';
            $this->contract_notes = $supplier->contract_notes ?? '';
            $this->current_balance = (string) $supplier->current_balance;
            $this->is_active = $supplier->is_active;
        }
    }

    public function rules(): array
    {
        $rules = [
            'type' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'preferred_currency' => 'required|string|size:3',
            'payment_terms' => 'nullable|string|max:255',
            'contract_notes' => 'nullable|string',
            'current_balance' => 'nullable|numeric',
            'is_active' => 'boolean',
        ];
        if ($this->editing && $this->supplier) {
            $rules['email'] = 'nullable|email|max:255|unique:suppliers,email,' . $this->supplier->id;
        } else {
            $rules['email'] = 'nullable|email|max:255|unique:suppliers,email';
        }
        return $rules;
    }

    public function save(): void
    {
        $this->validate();
        $data = [
            'type' => $this->type,
            'company_name' => $this->company_name,
            'contact_person' => $this->contact_person ?: null,
            'email' => $this->email ?: null,
            'phone' => $this->phone ?: null,
            'mobile' => $this->mobile ?: null,
            'address' => $this->address ?: null,
            'city' => $this->city ?: null,
            'country' => $this->country ?: null,
            'preferred_currency' => $this->preferred_currency,
            'payment_terms' => $this->payment_terms ?: null,
            'contract_notes' => $this->contract_notes ?: null,
            'current_balance' => (float) $this->current_balance,
            'is_active' => $this->is_active,
        ];

        if ($this->editing && $this->supplier) {
            $this->supplier->update($data);
            $this->dispatch('notify', type: 'success', title: 'Supplier Updated', message: 'Supplier updated successfully.');
            $this->redirect(route('suppliers.show', $this->supplier), navigate: true);
        } else {
            $data['supplier_code'] = $this->generateCode();
            $data['created_by'] = auth()->id() ?? 1;
            $supplier = Supplier::create($data);
            $this->dispatch('notify', type: 'success', title: 'Supplier Created', message: 'Supplier created successfully.');
            $this->redirect(route('suppliers.show', $supplier), navigate: true);
        }
    }

    protected function generateCode(): string
    {
        $prefix = match ($this->type) {
            'airline' => 'AIR', 'hotel' => 'HTL', 'transfer_company' => 'TRN',
            'visa_office' => 'VSA', 'insurance_company' => 'INS',
            'tour_operator' => 'TOP', 'cruise' => 'CRS',
            'train' => 'TRN', 'car_rental' => 'CAR',
            'package' => 'PKG', default => 'SUP',
        };
        $last = Supplier::where('supplier_code', 'like', "{$prefix}-%")->orderBy('supplier_code', 'desc')->first();
        $num = $last ? (int) substr($last->supplier_code, 4) + 1 : 1;
        return sprintf('%s-%04d', $prefix, $num);
    }

    public function render()
    {
        return view('livewire.suppliers.supplier-form');
    }
}
