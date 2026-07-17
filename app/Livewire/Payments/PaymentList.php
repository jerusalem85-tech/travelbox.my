<?php

namespace App\Livewire\Payments;

use App\Models\Payment;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

#[Layout('layouts.app')]
class PaymentList extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';
    #[Url]
    public string $category = '';

    public function render()
    {
        $query = Payment::with(['trip', 'invoice']);

        if ($this->search) {
            $query->where('payment_number', 'like', "%{$this->search}%")
                ->orWhere('description', 'like', "%{$this->search}%")
                ->orWhere('reference', 'like', "%{$this->search}%");
        }

        if ($this->category) {
            $query->where('category', $this->category);
        }

        $query->orderBy('payment_date', 'desc');

        return view('livewire.payments.payment-list', [
            'payments' => $query->paginate(15),
        ]);
    }
}
