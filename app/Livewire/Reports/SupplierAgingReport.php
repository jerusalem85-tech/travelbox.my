<?php

namespace App\Livewire\Reports;

use App\Models\Supplier;
use App\Models\FlightSegment;
use App\Models\HotelBooking;
use App\Models\TransferBooking;
use App\Models\Activity;
use App\Models\VisaApplication;
use App\Models\InsurancePolicy;
use App\Models\Payment;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class SupplierAgingReport extends Component
{
    public string $search = '';

    public function render()
    {
        $suppliers = Supplier::when($this->search, fn($q) => $q->where(function ($q) {
            $q->where('company_name', 'like', "%{$this->search}%")
              ->orWhere('contact_person', 'like', "%{$this->search}%")
              ->orWhere('email', 'like', "%{$this->search}%");
        }))
        ->orderBy('company_name')
        ->get()
        ->map(function ($s) {
            $costFlight = (float) FlightSegment::where('supplier_id', $s->id)->sum('cost_price');
            $costHotel = (float) HotelBooking::where('supplier_id', $s->id)->sum('cost_price');
            $costTransfer = (float) TransferBooking::where('supplier_id', $s->id)->sum('cost_price');
            $costActivity = (float) Activity::where('supplier_id', $s->id)->sum('cost_price');
            $costVisa = (float) VisaApplication::where('supplier_id', $s->id)->sum('cost_price');
            $costInsurance = (float) InsurancePolicy::where('supplier_id', $s->id)->sum('cost_price');

            $totalCost = $costFlight + $costHotel + $costTransfer + $costActivity + $costVisa + $costInsurance;

            $totalPaid = (float) Payment::where('payer_type', (new Supplier)->getMorphClass())
                ->where('payer_id', $s->id)
                ->where('category', 'supplier_payment')
                ->where('status', 'completed')
                ->sum('amount');

            $balance = $totalCost - $totalPaid;

            $lastDate = collect([
                FlightSegment::where('supplier_id', $s->id)->max('created_at'),
                HotelBooking::where('supplier_id', $s->id)->max('created_at'),
                TransferBooking::where('supplier_id', $s->id)->max('created_at'),
                Activity::where('supplier_id', $s->id)->max('created_at'),
                VisaApplication::where('supplier_id', $s->id)->max('created_at'),
                InsurancePolicy::where('supplier_id', $s->id)->max('created_at'),
            ])->filter()->max();

            return [
                'id' => $s->id,
                'company_name' => $s->company_name,
                'contact_person' => $s->contact_person,
                'email' => $s->email,
                'total_cost' => $totalCost,
                'total_paid' => $totalPaid,
                'balance' => $balance,
                'last_booking' => $lastDate,
            ];
        });

        return view('livewire.reports.supplier-aging-report', compact('suppliers'));
    }
}
