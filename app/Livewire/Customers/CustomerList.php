<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CustomerList extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'type')]
    public string $filterType = '';

    #[Url(as: 'status')]
    public string $filterStatus = '';

    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    protected $queryString = ['search', 'filterType', 'filterStatus'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterType(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function delete(Customer $customer): void
    {
        $this->authorize('delete', $customer);
        $customer->delete();
        $this->dispatch('notify', type: 'success', title: 'Customer Deleted', message: 'Customer deleted successfully.');
    }

    public function exportCsv()
    {
        $customers = Customer::orderBy($this->sortField, $this->sortDirection)->get();
        $csv = fopen('php://temp', 'r+');
        fputcsv($csv, ['Code', 'Type', 'First Name', 'Last Name', 'Company', 'Email', 'Phone', 'Country', 'Active']);
        foreach ($customers as $c) {
            fputcsv($csv, [$c->customer_code, $c->type, $c->first_name, $c->last_name, $c->company_name, $c->email, $c->phone, $c->country, $c->is_active ? 'Yes' : 'No']);
        }
        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);
        return response()->streamDownload(fn() => print($content), 'customers_export_'.now()->format('Ymd').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function render()
    {
        $query = Customer::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('first_name', 'like', "%{$this->search}%")
                  ->orWhere('last_name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('phone', 'like', "%{$this->search}%")
                  ->orWhere('company_name', 'like', "%{$this->search}%")
                  ->orWhere('customer_code', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterType) {
            $query->where('type', $this->filterType);
        }

        if ($this->filterStatus !== '') {
            $query->where('is_active', $this->filterStatus === 'active');
        }

        $query->orderBy($this->sortField, $this->sortDirection);

        return view('livewire.customers.customer-list', [
            'customers' => $query->paginate(10),
        ]);
    }
}
