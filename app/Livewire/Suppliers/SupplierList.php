<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class SupplierList extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';
    public string $filterType = '';
    public string $filterStatus = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterType(): void { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function delete(Supplier $supplier): void
    {
        $supplier->delete();
        $this->dispatch('notify', type: 'success', title: 'Supplier Deleted', message: 'Supplier deleted successfully.');
    }

    public function exportCsv()
    {
        $suppliers = Supplier::orderBy($this->sortField, $this->sortDirection)->get();
        $csv = fopen('php://temp', 'r+');
        fputcsv($csv, ['Code', 'Type', 'Company', 'Contact Person', 'Email', 'Phone', 'Country', 'City', 'Active']);
        foreach ($suppliers as $s) {
            fputcsv($csv, [$s->supplier_code, $s->type, $s->company_name, $s->contact_person, $s->email, $s->phone, $s->country, $s->city, $s->is_active ? 'Yes' : 'No']);
        }
        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);
        return response()->streamDownload(fn() => print($content), 'suppliers_export_'.now()->format('Ymd').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function render()
    {
        $query = Supplier::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('company_name', 'like', "%{$this->search}%")
                  ->orWhere('contact_person', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('phone', 'like', "%{$this->search}%")
                  ->orWhere('supplier_code', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterType) $query->where('type', $this->filterType);
        if ($this->filterStatus !== '') $query->where('is_active', $this->filterStatus === 'active');

        $query->orderBy($this->sortField, $this->sortDirection);

        return view('livewire.suppliers.supplier-list', [
            'suppliers' => $query->paginate(10),
        ]);
    }
}
