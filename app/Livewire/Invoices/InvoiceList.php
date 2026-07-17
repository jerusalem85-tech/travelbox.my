<?php

namespace App\Livewire\Invoices;

use App\Models\Invoice;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

#[Layout('layouts.app')]
class InvoiceList extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';
    #[Url]
    public string $status = '';
    #[Url]
    public string $sortField = 'issue_date';
    #[Url]
    public string $sortDirection = 'desc';

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $query = Invoice::with(['customer', 'trip']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('invoice_number', 'like', "%{$this->search}%")
                  ->orWhereHas('customer', fn($cq) => $cq->where('first_name', 'like', "%{$this->search}%")->orWhere('last_name', 'like', "%{$this->search}%"));
            });
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        $query->orderBy($this->sortField, $this->sortDirection);

        return view('livewire.invoices.invoice-list', [
            'invoices' => $query->paginate(10),
        ]);
    }
}
