<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Service Summary – {{ $trip->trip_number }}</title>
<style>
@page{margin:20mm 15mm}
body{font-family:'DejaVu Sans',sans-serif;font-size:10pt;color:#333;line-height:1.5}
.header{text-align:center;border-bottom:3px solid #1a56db;padding-bottom:10px;margin-bottom:20px}
.header h1{color:#1a56db;font-size:18pt;margin:0}
.header p{margin:2px 0;font-size:9pt;color:#6b7280}
.section{margin-bottom:18px;page-break-inside:avoid}
.section h3{font-size:10pt;color:#1a56db;text-transform:uppercase;border-bottom:1px solid #e5e7eb;padding-bottom:4px;margin:0 0 8px}
table{width:100%;border-collapse:collapse;font-size:9pt}
table th{background:#f3f4f6;text-align:left;padding:5px 6px;font-weight:600;border:1px solid #d1d5db}
table td{padding:4px 6px;border:1px solid #d1d5db;vertical-align:top}
table td.text-right{text-align:right}
.grid-2{display:flex;gap:15px}
.grid-2>div{flex:1}
.label{color:#6b7280;font-size:8pt;text-transform:uppercase;margin-bottom:1px}
.value{font-weight:600;font-size:10pt}
.footer{text-align:center;border-top:2px solid #e5e7eb;padding-top:10px;margin-top:20px;font-size:8pt;color:#9ca3af}
.badge{display:inline-block;padding:1px 6px;border-radius:3px;font-size:7pt;font-weight:600;text-transform:uppercase}
.badge-confirmed{background:#dbeafe;color:#1e40af}
.badge-enquiry{background:#fef3c7;color:#92400e}
.badge-cancelled{background:#fee2e2;color:#991b1b}
.total-row td{font-weight:700;border-top:2px solid #1a56db;background:#f9fafb}
.grand-total-row td{font-weight:700;font-size:10pt;border-top:3px solid #1a56db;background:#eff6ff}
.summary-meta{font-size:9pt;color:#6b7280;margin-bottom:15px}
.page-break{page-break-before:always}
</style>
</head>
<body>

<div class="header">
    <h1>TravelBox</h1>
    <p style="font-size:11pt;font-weight:600">Trip Service Summary &mdash; Internal Use</p>
    <p>{{ $trip->trip_number }} &bull; {{ $trip->customer?->full_name ?? '—' }}</p>
</div>

<div class="summary-meta">
    {{ $trip->destination ?? '—' }} &bull; {{ $trip->start_date?->format('M d, Y') }} – {{ $trip->end_date?->format('M d, Y') }}
    &bull; {{ $trip->type }}
</div>

@php
    $totalCost = 0;
    $totalSelling = 0;
    $grandCost = 0;
    $grandSelling = 0;
@endphp

{{-- FLIGHTS --}}
@if ($trip->flightSegments->isNotEmpty())
<div class="section">
    <h3>Flights</h3>
    <table>
        <thead><tr><th>Airline / Flight</th><th>Route</th><th>Supplier</th><th class="text-right">Cost</th><th class="text-right">Selling</th><th>Status</th></tr></thead>
        <tbody>
        @php $totalCost = 0; $totalSelling = 0; @endphp
        @foreach ($trip->flightSegments as $fs)
            @php
                $totalCost += $fs->cost ?? 0;
                $totalSelling += $fs->selling_price ?? 0;
            @endphp
            <tr>
                <td>{{ $fs->airline }} {{ $fs->flight_number }}</td>
                <td>{{ $fs->departure_airport }} → {{ $fs->arrival_airport }}</td>
                <td>{{ $fs->relationLoaded('supplier') && $fs->supplier ? $fs->supplier->name : '—' }}</td>
                <td class="text-right">{{ $fs->cost ? number_format($fs->cost, 2) : '—' }}</td>
                <td class="text-right">{{ $fs->selling_price ? number_format($fs->selling_price, 2) : '—' }}</td>
                <td><span class="badge badge-{{ $fs->status ?? 'confirmed' }}">{{ $fs->status ?? 'confirmed' }}</span></td>
            </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="3" class="text-right">Subtotal</td>
            <td class="text-right">{{ number_format($totalCost, 2) }}</td>
            <td class="text-right">{{ number_format($totalSelling, 2) }}</td>
            <td></td>
        </tr>
        @php $grandCost += $totalCost; $grandSelling += $totalSelling; @endphp
        </tbody>
    </table>
</div>
@endif

{{-- HOTELS --}}
@if ($trip->hotelBookings->isNotEmpty())
<div class="section">
    <h3>Hotels</h3>
    <table>
        <thead><tr><th>Hotel</th><th>Room / Plan</th><th>Supplier</th><th class="text-right">Cost</th><th class="text-right">Selling</th><th>Status</th></tr></thead>
        <tbody>
        @php $totalCost = 0; $totalSelling = 0; @endphp
        @foreach ($trip->hotelBookings as $hb)
            @php
                $totalCost += $hb->cost ?? 0;
                $totalSelling += $hb->selling_price ?? 0;
            @endphp
            <tr>
                <td>{{ $hb->hotel_name }}</td>
                <td>{{ $hb->room_type }} / {{ $hb->meal_plan ?? '—' }}</td>
                <td>{{ $hb->relationLoaded('supplier') && $hb->supplier ? $hb->supplier->name : '—' }}</td>
                <td class="text-right">{{ $hb->cost ? number_format($hb->cost, 2) : '—' }}</td>
                <td class="text-right">{{ $hb->selling_price ? number_format($hb->selling_price, 2) : '—' }}</td>
                <td><span class="badge badge-{{ $hb->status ?? 'confirmed' }}">{{ $hb->status ?? 'confirmed' }}</span></td>
            </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="3" class="text-right">Subtotal</td>
            <td class="text-right">{{ number_format($totalCost, 2) }}</td>
            <td class="text-right">{{ number_format($totalSelling, 2) }}</td>
            <td></td>
        </tr>
        @php $grandCost += $totalCost; $grandSelling += $totalSelling; @endphp
        </tbody>
    </table>
</div>
@endif

{{-- TRANSFERS --}}
@if ($trip->transferBookings->isNotEmpty())
<div class="section">
    <h3>Transfers</h3>
    <table>
        <thead><tr><th>Type</th><th>Route</th><th>Vehicle</th><th class="text-right">Cost</th><th class="text-right">Selling</th><th>Status</th></tr></thead>
        <tbody>
        @php $totalCost = 0; $totalSelling = 0; @endphp
        @foreach ($trip->transferBookings as $tb)
            @php
                $totalCost += $tb->cost ?? 0;
                $totalSelling += $tb->selling_price ?? 0;
            @endphp
            <tr>
                <td>{{ ucfirst($tb->type ?? 'transfer') }}</td>
                <td>{{ $tb->pickup_location }} → {{ $tb->dropoff_location }}</td>
                <td>{{ $tb->vehicle_type ?? '—' }}</td>
                <td class="text-right">{{ $tb->cost ? number_format($tb->cost, 2) : '—' }}</td>
                <td class="text-right">{{ $tb->selling_price ? number_format($tb->selling_price, 2) : '—' }}</td>
                <td><span class="badge badge-{{ $tb->status ?? 'confirmed' }}">{{ $tb->status ?? 'confirmed' }}</span></td>
            </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="3" class="text-right">Subtotal</td>
            <td class="text-right">{{ number_format($totalCost, 2) }}</td>
            <td class="text-right">{{ number_format($totalSelling, 2) }}</td>
            <td></td>
        </tr>
        @php $grandCost += $totalCost; $grandSelling += $totalSelling; @endphp
        </tbody>
    </table>
</div>
@endif

{{-- VISAS --}}
@if ($trip->visaApplications->isNotEmpty())
<div class="section">
    <h3>Visas</h3>
    <table>
        <thead><tr><th>Country</th><th>Type</th><th>Supplier</th><th class="text-right">Cost</th><th class="text-right">Selling</th><th>Status</th></tr></thead>
        <tbody>
        @php $totalCost = 0; $totalSelling = 0; @endphp
        @foreach ($trip->visaApplications as $v)
            @php
                $totalCost += $v->cost ?? 0;
                $totalSelling += $v->selling_price ?? 0;
            @endphp
            <tr>
                <td>{{ $v->country }}</td>
                <td>{{ str_replace('_', ' ', ucfirst($v->visa_type ?? 'tourist')) }}</td>
                <td>{{ $v->supplier_name ?? '—' }}</td>
                <td class="text-right">{{ isset($v->cost) ? number_format($v->cost, 2) : '—' }}</td>
                <td class="text-right">{{ isset($v->selling_price) ? number_format($v->selling_price, 2) : '—' }}</td>
                <td>{{ $v->status }}</td>
            </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="3" class="text-right">Subtotal</td>
            <td class="text-right">{{ number_format($totalCost, 2) }}</td>
            <td class="text-right">{{ number_format($totalSelling, 2) }}</td>
            <td></td>
        </tr>
        @php $grandCost += $totalCost; $grandSelling += $totalSelling; @endphp
        </tbody>
    </table>
</div>
@endif

{{-- INSURANCE --}}
@if ($trip->insurancePolicies->isNotEmpty())
<div class="section">
    <h3>Insurance</h3>
    <table>
        <thead><tr><th>Policy</th><th>Type</th><th>Provider</th><th class="text-right">Cost</th><th class="text-right">Selling</th><th>Status</th></tr></thead>
        <tbody>
        @php $totalCost = 0; $totalSelling = 0; @endphp
        @foreach ($trip->insurancePolicies as $ins)
            @php
                $totalCost += $ins->cost ?? 0;
                $totalSelling += $ins->selling_price ?? 0;
            @endphp
            <tr>
                <td>{{ $ins->policy_number ?: '—' }}</td>
                <td>{{ str_replace('_', ' ', ucfirst($ins->type ?? 'travel')) }}</td>
                <td>{{ $ins->provider ?? '—' }}</td>
                <td class="text-right">{{ $ins->cost ? number_format($ins->cost, 2) : '—' }}</td>
                <td class="text-right">{{ $ins->selling_price ? number_format($ins->selling_price, 2) : '—' }}</td>
                <td>{{ $ins->status ?? 'active' }}</td>
            </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="3" class="text-right">Subtotal</td>
            <td class="text-right">{{ number_format($totalCost, 2) }}</td>
            <td class="text-right">{{ number_format($totalSelling, 2) }}</td>
            <td></td>
        </tr>
        @php $grandCost += $totalCost; $grandSelling += $totalSelling; @endphp
        </tbody>
    </table>
</div>
@endif

{{-- ACTIVITIES --}}
@if ($trip->activities->isNotEmpty())
<div class="section">
    <h3>Activities</h3>
    <table>
        <thead><tr><th>Activity</th><th>Location</th><th>Supplier</th><th class="text-right">Cost</th><th class="text-right">Selling</th><th>Status</th></tr></thead>
        <tbody>
        @php $totalCost = 0; $totalSelling = 0; @endphp
        @foreach ($trip->activities as $act)
            @php
                $totalCost += $act->cost ?? 0;
                $totalSelling += $act->selling_price ?? 0;
            @endphp
            <tr>
                <td>{{ $act->name }}</td>
                <td>{{ $act->location }}</td>
                <td>{{ $act->supplier_name ?? '—' }}</td>
                <td class="text-right">{{ $act->cost ? number_format($act->cost, 2) : '—' }}</td>
                <td class="text-right">{{ $act->selling_price ? number_format($act->selling_price, 2) : '—' }}</td>
                <td><span class="badge badge-{{ $act->status ?? 'confirmed' }}">{{ $act->status ?? 'confirmed' }}</span></td>
            </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="3" class="text-right">Subtotal</td>
            <td class="text-right">{{ number_format($totalCost, 2) }}</td>
            <td class="text-right">{{ number_format($totalSelling, 2) }}</td>
            <td></td>
        </tr>
        @php $grandCost += $totalCost; $grandSelling += $totalSelling; @endphp
        </tbody>
    </table>
</div>
@endif

{{-- CRUISES --}}
@if ($trip->cruiseBookings->isNotEmpty())
<div class="section">
    <h3>Cruises</h3>
    <table>
        <thead><tr><th>Cruise Line</th><th>Ship</th><th>Route</th><th class="text-right">Cost</th><th class="text-right">Selling</th><th>Status</th></tr></thead>
        <tbody>
        @php $totalCost = 0; $totalSelling = 0; @endphp
        @foreach ($trip->cruiseBookings as $c)
            @php
                $totalCost += $c->cost_price ?? 0;
                $totalSelling += $c->selling_price ?? 0;
            @endphp
            <tr>
                <td>{{ $c->cruise_line ?: '—' }}</td>
                <td>{{ $c->ship_name ?: '—' }}</td>
                <td>{{ $c->departure_port ?: '—' }} → {{ $c->arrival_port ?: '—' }}</td>
                <td class="text-right">{{ $c->cost_price ? number_format($c->cost_price, 2) : '—' }}</td>
                <td class="text-right">{{ $c->selling_price ? number_format($c->selling_price, 2) : '—' }}</td>
                <td><span class="badge badge-confirmed">confirmed</span></td>
            </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="3" class="text-right">Subtotal</td>
            <td class="text-right">{{ number_format($totalCost, 2) }}</td>
            <td class="text-right">{{ number_format($totalSelling, 2) }}</td>
            <td></td>
        </tr>
        @php $grandCost += $totalCost; $grandSelling += $totalSelling; @endphp
        </tbody>
    </table>
</div>
@endif

{{-- TRAINS --}}
@if ($trip->trainBookings->isNotEmpty())
<div class="section">
    <h3>Trains</h3>
    <table>
        <thead><tr><th>Company</th><th>Train #</th><th>Route</th><th class="text-right">Cost</th><th class="text-right">Selling</th><th>Status</th></tr></thead>
        <tbody>
        @php $totalCost = 0; $totalSelling = 0; @endphp
        @foreach ($trip->trainBookings as $tr)
            @php
                $totalCost += $tr->cost_price ?? 0;
                $totalSelling += $tr->selling_price ?? 0;
            @endphp
            <tr>
                <td>{{ $tr->train_company ?: '—' }}</td>
                <td>{{ $tr->train_number ?: '—' }}</td>
                <td>{{ $tr->departure_station ?: '—' }} → {{ $tr->arrival_station ?: '—' }}</td>
                <td class="text-right">{{ $tr->cost_price ? number_format($tr->cost_price, 2) : '—' }}</td>
                <td class="text-right">{{ $tr->selling_price ? number_format($tr->selling_price, 2) : '—' }}</td>
                <td><span class="badge badge-confirmed">confirmed</span></td>
            </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="3" class="text-right">Subtotal</td>
            <td class="text-right">{{ number_format($totalCost, 2) }}</td>
            <td class="text-right">{{ number_format($totalSelling, 2) }}</td>
            <td></td>
        </tr>
        @php $grandCost += $totalCost; $grandSelling += $totalSelling; @endphp
        </tbody>
    </table>
</div>
@endif

{{-- CAR RENTALS --}}
@if ($trip->carRentals->isNotEmpty())
<div class="section">
    <h3>Car Rentals</h3>
    <table>
        <thead><tr><th>Company</th><th>Car Type</th><th>Route</th><th class="text-right">Cost</th><th class="text-right">Selling</th><th>Status</th></tr></thead>
        <tbody>
        @php $totalCost = 0; $totalSelling = 0; @endphp
        @foreach ($trip->carRentals as $ca)
            @php
                $totalCost += $ca->cost_price ?? 0;
                $totalSelling += $ca->selling_price ?? 0;
            @endphp
            <tr>
                <td>{{ $ca->company ?: '—' }}</td>
                <td>{{ $ca->car_type ?: '—' }}</td>
                <td>{{ $ca->pickup_location ?: '—' }} → {{ $ca->dropoff_location ?: '—' }}</td>
                <td class="text-right">{{ $ca->cost_price ? number_format($ca->cost_price, 2) : '—' }}</td>
                <td class="text-right">{{ $ca->selling_price ? number_format($ca->selling_price, 2) : '—' }}</td>
                <td><span class="badge badge-confirmed">confirmed</span></td>
            </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="3" class="text-right">Subtotal</td>
            <td class="text-right">{{ number_format($totalCost, 2) }}</td>
            <td class="text-right">{{ number_format($totalSelling, 2) }}</td>
            <td></td>
        </tr>
        @php $grandCost += $totalCost; $grandSelling += $totalSelling; @endphp
        </tbody>
    </table>
</div>
@endif

{{-- PACKAGES --}}
@if ($trip->packageBookings->isNotEmpty())
<div class="section">
    <h3>Packages</h3>
    <table>
        <thead><tr><th>Package</th><th>Type</th><th>Dates</th><th class="text-right">Cost</th><th class="text-right">Selling</th><th>Status</th></tr></thead>
        <tbody>
        @php $totalCost = 0; $totalSelling = 0; @endphp
        @foreach ($trip->packageBookings as $pk)
            @php
                $totalCost += $pk->cost_price ?? 0;
                $totalSelling += $pk->selling_price ?? 0;
            @endphp
            <tr>
                <td>{{ $pk->package_name ?: '—' }}</td>
                <td>{{ $pk->package_type ?: '—' }}</td>
                <td>{{ $pk->start_date?->format('M d, Y') }} – {{ $pk->end_date?->format('M d, Y') }}</td>
                <td class="text-right">{{ $pk->cost_price ? number_format($pk->cost_price, 2) : '—' }}</td>
                <td class="text-right">{{ $pk->selling_price ? number_format($pk->selling_price, 2) : '—' }}</td>
                <td><span class="badge badge-confirmed">confirmed</span></td>
            </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="3" class="text-right">Subtotal</td>
            <td class="text-right">{{ number_format($totalCost, 2) }}</td>
            <td class="text-right">{{ number_format($totalSelling, 2) }}</td>
            <td></td>
        </tr>
        @php $grandCost += $totalCost; $grandSelling += $totalSelling; @endphp
        </tbody>
    </table>
</div>
@endif

{{-- OTHER SERVICES --}}
@if ($trip->otherServices->isNotEmpty())
<div class="section">
    <h3>Other Services</h3>
    <table>
        <thead><tr><th>Service</th><th>Type</th><th>Date</th><th class="text-right">Cost</th><th class="text-right">Selling</th><th>Status</th></tr></thead>
        <tbody>
        @php $totalCost = 0; $totalSelling = 0; @endphp
        @foreach ($trip->otherServices as $o)
            @php
                $totalCost += $o->cost_price ?? 0;
                $totalSelling += $o->selling_price ?? 0;
            @endphp
            <tr>
                <td>{{ $o->service_name ?: '—' }}</td>
                <td>{{ $o->service_type ?: '—' }}</td>
                <td>{{ $o->service_date?->format('M d, Y') }}</td>
                <td class="text-right">{{ $o->cost_price ? number_format($o->cost_price, 2) : '—' }}</td>
                <td class="text-right">{{ $o->selling_price ? number_format($o->selling_price, 2) : '—' }}</td>
                <td><span class="badge badge-confirmed">confirmed</span></td>
            </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="3" class="text-right">Subtotal</td>
            <td class="text-right">{{ number_format($totalCost, 2) }}</td>
            <td class="text-right">{{ number_format($totalSelling, 2) }}</td>
            <td></td>
        </tr>
        @php $grandCost += $totalCost; $grandSelling += $totalSelling; @endphp
        </tbody>
    </table>
</div>
@endif

{{-- GRAND TOTALS --}}
<div class="section">
    <table>
        <tr class="grand-total-row">
            <td style="width:50%" class="text-right">GRAND TOTAL</td>
            <td style="width:25%" class="text-right">{{ number_format($grandCost, 2) }}</td>
            <td style="width:25%" class="text-right">{{ number_format($grandSelling, 2) }}</td>
        </tr>
        <tr>
            <td class="text-right" style="font-weight:600;border-top:none">Margin</td>
            <td class="text-right" style="border-top:none" colspan="2">{{ number_format($grandSelling - $grandCost, 2) }}</td>
        </tr>
    </table>
</div>

<div class="footer">
    <p>Generated by TravelBox ERP &bull; {{ now()->format('M d, Y H:i') }} &bull; travelbox.my</p>
    <p style="font-size:7pt">INTERNAL USE ONLY — This document contains confidential cost and pricing information.</p>
</div>

</body>
</html>
