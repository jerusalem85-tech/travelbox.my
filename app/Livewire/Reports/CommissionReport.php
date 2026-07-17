<?php

namespace App\Livewire\Reports;

use App\Models\Trip;
use App\Models\Supplier;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class CommissionReport extends Component
{
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $supplierId = '';
    public string $sortField = 'supplier_name';
    public string $sortDirection = 'asc';

    public function mount(): void
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->endOfMonth()->format('Y-m-d');
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

    public function render()
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('company_name')->get();

        $services = collect();

        $tripIds = Trip::whereBetween('start_date', [$this->dateFrom, $this->dateTo])
            ->orWhereBetween('end_date', [$this->dateFrom, $this->dateTo])
            ->pluck('id');

        if ($tripIds->isNotEmpty()) {
            $tripIds = $tripIds->toArray();

            $flightSegments = \App\Models\FlightSegment::with('trip', 'supplier')
                ->whereIn('trip_id', $tripIds)
                ->when($this->supplierId, fn($q) => $q->where('supplier_id', $this->supplierId))
                ->get()
                ->map(fn($s) => [
                    'supplier_id' => $s->supplier_id,
                    'supplier_name' => $s->supplier?->company_name ?? 'N/A',
                    'service_type' => 'Flight',
                    'service_detail' => $s->airline . ' ' . $s->flight_number,
                    'selling_price' => (float) $s->selling_price,
                    'cost_price' => (float) $s->cost_price,
                    'commission' => (float) ($s->selling_price - $s->cost_price),
                    'trip_id' => $s->trip_id,
                    'trip_name' => $s->trip?->name ?? 'N/A',
                    'date' => $s->departure_datetime?->format('M d, Y') ?? '—',
                ]);

            $hotelBookings = \App\Models\HotelBooking::with('trip', 'supplier')
                ->whereIn('trip_id', $tripIds)
                ->when($this->supplierId, fn($q) => $q->where('supplier_id', $this->supplierId))
                ->get()
                ->map(fn($s) => [
                    'supplier_id' => $s->supplier_id,
                    'supplier_name' => $s->supplier?->company_name ?? 'N/A',
                    'service_type' => 'Hotel',
                    'service_detail' => $s->hotel_name,
                    'selling_price' => (float) $s->selling_price,
                    'cost_price' => (float) $s->cost_price,
                    'commission' => (float) ($s->selling_price - $s->cost_price),
                    'trip_id' => $s->trip_id,
                    'trip_name' => $s->trip?->name ?? 'N/A',
                    'date' => $s->check_in?->format('M d, Y') ?? '—',
                ]);

            $transferBookings = \App\Models\TransferBooking::with('trip', 'supplier')
                ->whereIn('trip_id', $tripIds)
                ->when($this->supplierId, fn($q) => $q->where('supplier_id', $this->supplierId))
                ->get()
                ->map(fn($s) => [
                    'supplier_id' => $s->supplier_id,
                    'supplier_name' => $s->supplier?->company_name ?? 'N/A',
                    'service_type' => 'Transfer',
                    'service_detail' => $s->pickup_location . ' → ' . $s->dropoff_location,
                    'selling_price' => (float) $s->selling_price,
                    'cost_price' => (float) $s->cost_price,
                    'commission' => (float) ($s->selling_price - $s->cost_price),
                    'trip_id' => $s->trip_id,
                    'trip_name' => $s->trip?->name ?? 'N/A',
                    'date' => $s->pickup_datetime?->format('M d, Y') ?? '—',
                ]);

            $activities = \App\Models\Activity::with('trip', 'supplier')
                ->whereIn('trip_id', $tripIds)
                ->when($this->supplierId, fn($q) => $q->where('supplier_id', $this->supplierId))
                ->get()
                ->map(fn($s) => [
                    'supplier_id' => $s->supplier_id,
                    'supplier_name' => $s->supplier?->company_name ?? 'N/A',
                    'service_type' => 'Activity',
                    'service_detail' => $s->name,
                    'selling_price' => (float) $s->selling_price,
                    'cost_price' => (float) $s->cost_price,
                    'commission' => (float) ($s->selling_price - $s->cost_price),
                    'trip_id' => $s->trip_id,
                    'trip_name' => $s->trip?->name ?? 'N/A',
                    'date' => $s->date?->format('M d, Y') ?? '—',
                ]);

            $visaApplications = \App\Models\VisaApplication::with('trip', 'supplier')
                ->whereIn('trip_id', $tripIds)
                ->when($this->supplierId, fn($q) => $q->where('supplier_id', $this->supplierId))
                ->get()
                ->map(fn($s) => [
                    'supplier_id' => $s->supplier_id,
                    'supplier_name' => $s->supplier?->company_name ?? 'N/A',
                    'service_type' => 'Visa',
                    'service_detail' => $s->country . ' (' . $s->visa_type . ')',
                    'selling_price' => (float) $s->selling_price,
                    'cost_price' => (float) $s->cost_price,
                    'commission' => (float) ($s->selling_price - $s->cost_price),
                    'trip_id' => $s->trip_id,
                    'trip_name' => $s->trip?->name ?? 'N/A',
                    'date' => $s->application_date?->format('M d, Y') ?? '—',
                ]);

            $insurancePolicies = \App\Models\InsurancePolicy::with('trip', 'supplier')
                ->whereIn('trip_id', $tripIds)
                ->when($this->supplierId, fn($q) => $q->where('supplier_id', $this->supplierId))
                ->get()
                ->map(fn($s) => [
                    'supplier_id' => $s->supplier_id,
                    'supplier_name' => $s->supplier?->company_name ?? 'N/A',
                    'service_type' => 'Insurance',
                    'service_detail' => $s->policy_number . ' (' . $s->type . ')',
                    'selling_price' => (float) $s->selling_price,
                    'cost_price' => (float) $s->cost_price,
                    'commission' => (float) ($s->selling_price - $s->cost_price),
                    'trip_id' => $s->trip_id,
                    'trip_name' => $s->trip?->name ?? 'N/A',
                    'date' => $s->start_date?->format('M d, Y') ?? '—',
                ]);

            $cruiseBookings = \App\Models\CruiseBooking::with('trip', 'supplier')
                ->whereIn('trip_id', $tripIds)
                ->when($this->supplierId, fn($q) => $q->where('supplier_id', $this->supplierId))
                ->get()
                ->map(fn($s) => [
                    'supplier_id' => $s->supplier_id,
                    'supplier_name' => $s->supplier?->company_name ?? 'N/A',
                    'service_type' => 'Cruise',
                    'service_detail' => $s->cruise_line . ' ' . ($s->ship_name ?? ''),
                    'selling_price' => (float) $s->selling_price,
                    'cost_price' => (float) $s->cost_price,
                    'commission' => (float) ($s->selling_price - $s->cost_price),
                    'trip_id' => $s->trip_id,
                    'trip_name' => $s->trip?->name ?? 'N/A',
                    'date' => $s->departure_date?->format('M d, Y') ?? '—',
                ]);

            $trainBookings = \App\Models\TrainBooking::with('trip', 'supplier')
                ->whereIn('trip_id', $tripIds)
                ->when($this->supplierId, fn($q) => $q->where('supplier_id', $this->supplierId))
                ->get()
                ->map(fn($s) => [
                    'supplier_id' => $s->supplier_id,
                    'supplier_name' => $s->supplier?->company_name ?? 'N/A',
                    'service_type' => 'Train',
                    'service_detail' => $s->train_company . ' ' . ($s->train_number ?? ''),
                    'selling_price' => (float) $s->selling_price,
                    'cost_price' => (float) $s->cost_price,
                    'commission' => (float) ($s->selling_price - $s->cost_price),
                    'trip_id' => $s->trip_id,
                    'trip_name' => $s->trip?->name ?? 'N/A',
                    'date' => $s->departure_datetime?->format('M d, Y') ?? '—',
                ]);

            $carRentals = \App\Models\CarRental::with('trip', 'supplier')
                ->whereIn('trip_id', $tripIds)
                ->when($this->supplierId, fn($q) => $q->where('supplier_id', $this->supplierId))
                ->get()
                ->map(fn($s) => [
                    'supplier_id' => $s->supplier_id,
                    'supplier_name' => $s->supplier?->company_name ?? 'N/A',
                    'service_type' => 'Car Rental',
                    'service_detail' => $s->company . ' ' . ($s->car_type ?? ''),
                    'selling_price' => (float) $s->selling_price,
                    'cost_price' => (float) $s->cost_price,
                    'commission' => (float) ($s->selling_price - $s->cost_price),
                    'trip_id' => $s->trip_id,
                    'trip_name' => $s->trip?->name ?? 'N/A',
                    'date' => $s->pickup_datetime?->format('M d, Y') ?? '—',
                ]);

            $packageBookings = \App\Models\PackageBooking::with('trip', 'supplier')
                ->whereIn('trip_id', $tripIds)
                ->when($this->supplierId, fn($q) => $q->where('supplier_id', $this->supplierId))
                ->get()
                ->map(fn($s) => [
                    'supplier_id' => $s->supplier_id,
                    'supplier_name' => $s->supplier?->company_name ?? 'N/A',
                    'service_type' => 'Package',
                    'service_detail' => $s->package_name . ' ' . ($s->package_type ?? ''),
                    'selling_price' => (float) $s->selling_price,
                    'cost_price' => (float) $s->cost_price,
                    'commission' => (float) ($s->selling_price - $s->cost_price),
                    'trip_id' => $s->trip_id,
                    'trip_name' => $s->trip?->name ?? 'N/A',
                    'date' => $s->start_date?->format('M d, Y') ?? '—',
                ]);

            $otherServices = \App\Models\OtherService::with('trip', 'supplier')
                ->whereIn('trip_id', $tripIds)
                ->when($this->supplierId, fn($q) => $q->where('supplier_id', $this->supplierId))
                ->get()
                ->map(fn($s) => [
                    'supplier_id' => $s->supplier_id,
                    'supplier_name' => $s->supplier?->company_name ?? 'N/A',
                    'service_type' => 'Other',
                    'service_detail' => $s->service_name . ' ' . ($s->service_type ?? ''),
                    'selling_price' => (float) $s->selling_price,
                    'cost_price' => (float) $s->cost_price,
                    'commission' => (float) ($s->selling_price - $s->cost_price),
                    'trip_id' => $s->trip_id,
                    'trip_name' => $s->trip?->name ?? 'N/A',
                    'date' => $s->service_date?->format('M d, Y') ?? '—',
                ]);

            $services = $flightSegments
                ->concat($hotelBookings)
                ->concat($transferBookings)
                ->concat($activities)
                ->concat($visaApplications)
                ->concat($insurancePolicies)
                ->concat($cruiseBookings)
                ->concat($trainBookings)
                ->concat($carRentals)
                ->concat($packageBookings)
                ->concat($otherServices);
        }

        $sortField = $this->sortField;
        $sortDirection = $this->sortDirection;
        $services = $services->sortBy([
            [$sortField, $sortDirection],
        ]);

        $totalSelling = $services->sum('selling_price');
        $totalCost = $services->sum('cost_price');
        $totalCommission = $services->sum('commission');

        return view('livewire.reports.commission-report', compact('services', 'suppliers', 'totalSelling', 'totalCost', 'totalCommission'));
    }
}
