<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CustomerShow extends Component
{
    public Customer $customer;

    public function mount(Customer $customer): void
    {
        $this->customer = $customer->load([
            'contacts', 'familyMembers',
            'trips' => fn($q) => $q->withCount('flightSegments', 'hotelBookings')->latest(),
            'invoices' => fn($q) => $q->latest(),
            'payments' => fn($q) => $q->latest(),
        ]);
    }

    public function render()
    {
        $totalTrips = $this->customer->trips->count();
        $totalSpent = $this->customer->trips->sum('total_selling_price');
        $recentTrips = $this->customer->trips->take(10);
        $destinations = $this->customer->trips->pluck('destination')->filter()->unique()->values();

        return view('livewire.customers.customer-show', [
            'totalTrips' => $totalTrips,
            'totalSpent' => $totalSpent,
            'recentTrips' => $recentTrips,
            'destinations' => $destinations,
        ]);
    }
}
