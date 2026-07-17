<?php

namespace App\Livewire;

use App\Models\Trip;
use App\Models\Customer;
use App\Models\Supplier;
use Livewire\Component;

class GlobalSearch extends Component
{
    public string $query = '';
    public bool $show = false;
    public array $results = ['trips' => [], 'customers' => [], 'suppliers' => []];

    public function updatedQuery(): void
    {
        if (strlen($this->query) < 2) {
            $this->results = ['trips' => [], 'customers' => [], 'suppliers' => []];
            $this->show = false;
            return;
        }
        $q = $this->query;
        $this->results = [
            'trips' => Trip::where('name', 'like', "%{$q}%")
                ->orWhere('trip_number', 'like', "%{$q}%")
                ->orWhere('destination', 'like', "%{$q}%")
                ->take(5)->get()->toArray(),
            'customers' => Customer::where('first_name', 'like', "%{$q}%")
                ->orWhere('last_name', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
                ->take(5)->get()->toArray(),
            'suppliers' => Supplier::where('company_name', 'like', "%{$q}%")
                ->orWhere('supplier_code', 'like', "%{$q}%")
                ->take(5)->get()->toArray(),
        ];
        $this->show = true;
    }

    public function hide(): void
    {
        $this->show = false;
    }

    public function render()
    {
        return view('livewire.global-search');
    }
}
