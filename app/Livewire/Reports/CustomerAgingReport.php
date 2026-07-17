<?php

namespace App\Livewire\Reports;

use App\Models\Customer;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CustomerAgingReport extends Component
{
    public string $search = '';

    public function render()
    {
        $customers = Customer::withCount([
            'invoices as total_invoiced' => fn($q) => $q->selectRaw('COALESCE(SUM(total), 0)'),
            'invoices as overdue_invoices_count' => fn($q) => $q->where('status', 'overdue'),
            'payments as total_paid' => fn($q) => $q
                ->whereIn('category', ['customer_payment', 'customer_deposit'])
                ->where('status', 'completed')
                ->selectRaw('COALESCE(SUM(amount), 0)'),
        ])
        ->when($this->search, fn($q) => $q->where(function ($q) {
            $q->where('first_name', 'like', "%{$this->search}%")
              ->orWhere('last_name', 'like', "%{$this->search}%")
              ->orWhere('company_name', 'like', "%{$this->search}%")
              ->orWhere('email', 'like', "%{$this->search}%");
        }))
        ->orderBy('first_name')
        ->get()
        ->map(fn($c) => [
            'id' => $c->id,
            'name' => $c->full_name ?: $c->company_name,
            'company' => $c->company_name,
            'email' => $c->email,
            'total_invoiced' => (float) ($c->total_invoiced ?? 0),
            'total_paid' => (float) ($c->total_paid ?? 0),
            'balance' => (float) (($c->total_invoiced ?? 0) - ($c->total_paid ?? 0)),
            'overdue_invoices' => (int) ($c->overdue_invoices_count ?? 0),
            'last_activity' => $c->invoices()->latest('issue_date')->value('issue_date'),
        ]);

        return view('livewire.reports.customer-aging-report', compact('customers'));
    }
}
