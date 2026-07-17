<?php

namespace App\Livewire\Invoices;

use App\Models\Invoice;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class InvoiceShow extends Component
{
    public Invoice $invoice;

    public function mount(Invoice $invoice): void
    {
        $this->invoice = $invoice->load(['customer', 'trip', 'items']);
    }

    public function render()
    {
        return view('livewire.invoices.invoice-show');
    }
}
