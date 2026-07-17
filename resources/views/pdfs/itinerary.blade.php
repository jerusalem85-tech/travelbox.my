<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Itinerary – {{ $trip->trip_number }}</title>
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
.grid-2{display:flex;gap:15px}
.grid-2>div{flex:1}
.label{color:#6b7280;font-size:8pt;text-transform:uppercase;margin-bottom:1px}
.value{font-weight:600;font-size:10pt}
.footer{text-align:center;border-top:2px solid #e5e7eb;padding-top:10px;margin-top:20px;font-size:8pt;color:#9ca3af}
.badge{display:inline-block;padding:1px 6px;border-radius:3px;font-size:7pt;font-weight:600;text-transform:uppercase}
.badge-enquiry{background:#fef3c7;color:#92400e}
.badge-confirmed{background:#dbeafe;color:#1e40af}
.badge-in_progress{background:#e0e7ff;color:#3730a3}
.badge-completed{background:#d1fae5;color:#065f46}
.badge-cancelled{background:#fee2e2;color:#991b1b}
.passenger-list{font-size:9pt}
.passenger-list span{display:inline-block;margin:1px 4px 1px 0}
.page-break{page-break-before:always}
</style>
</head>
<body>

<div class="header">
    <h1>TravelBox</h1>
    <p style="font-size:11pt;font-weight:600">Trip Itinerary</p>
    <p>{{ $trip->trip_number }} &bull; {{ str_replace('_', ' ', ucfirst($trip->status)) }}</p>
</div>

<div class="section">
    <div class="grid-2">
        <div>
            <div class="label">Customer</div>
            <div class="value">{{ $trip->customer?->full_name ?? '—' }}</div>
            @if ($trip->customer?->email)<div style="font-size:9pt;color:#6b7280">{{ $trip->customer->email }}</div>@endif
            @if ($trip->customer?->phone)<div style="font-size:9pt;color:#6b7280">{{ $trip->customer->phone }}</div>@endif
        </div>
        <div>
            <div class="label">Trip Details</div>
            <div class="value">{{ $trip->destination ?? '—' }}</div>
            <div style="font-size:9pt;color:#6b7280">
                {{ $trip->start_date?->format('M d, Y') }} – {{ $trip->end_date?->format('M d, Y') }}
                &bull; {{ $trip->type }}
            </div>
        </div>
    </div>
</div>

@if ($trip->passengers->isNotEmpty())
<div class="section">
    <h3>Passengers ({{ $trip->passengers->count() }})</h3>
    <div class="passenger-list">
        @foreach ($trip->passengers as $p)
        <span>{{ $p->first_name }} {{ $p->last_name }}{{ !$loop->last ? ',' : '' }}</span>
        @endforeach
    </div>
</div>
@endif

@if ($trip->flightSegments->isNotEmpty())
<div class="section">
    <h3>Flights</h3>
    <table>
        <thead><tr><th>Airline</th><th>Flight</th><th>From</th><th>To</th><th>Departure</th><th>Arrival</th><th>Class</th></tr></thead>
        <tbody>
        @foreach ($trip->flightSegments as $fs)
        <tr>
            <td>{{ $fs->airline }}</td>
            <td>{{ $fs->flight_number }}</td>
            <td>{{ $fs->departure_airport }}</td>
            <td>{{ $fs->arrival_airport }}</td>
            <td>{{ $fs->departure_datetime?->format('M d, H:i') }}</td>
            <td>{{ $fs->arrival_datetime?->format('M d, H:i') }}</td>
            <td>{{ $fs->travel_class ?? '—' }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

@if ($trip->hotelBookings->isNotEmpty())
<div class="section">
    <h3>Hotels</h3>
    <table>
        <thead><tr><th>Hotel</th><th>City</th><th>Check-in</th><th>Check-out</th><th>Room</th><th>Rooms</th></tr></thead>
        <tbody>
        @foreach ($trip->hotelBookings as $hb)
        <tr>
            <td>{{ $hb->hotel_name }}</td>
            <td>{{ $hb->city }}</td>
            <td>{{ $hb->check_in?->format('M d, Y') }}</td>
            <td>{{ $hb->check_out?->format('M d, Y') }}</td>
            <td>{{ $hb->room_type }}</td>
            <td>{{ $hb->number_of_rooms }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

@if ($trip->transferBookings->isNotEmpty())
<div class="section">
    <h3>Transfers</h3>
    <table>
        <thead><tr><th>Pickup</th><th>Dropoff</th><th>Date/Time</th><th>Vehicle</th></tr></thead>
        <tbody>
        @foreach ($trip->transferBookings as $tb)
        <tr>
            <td>{{ $tb->pickup_location }}</td>
            <td>{{ $tb->dropoff_location }}</td>
            <td>{{ $tb->pickup_datetime?->format('M d, Y H:i') }}</td>
            <td>{{ $tb->vehicle_type }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

@if ($trip->visaApplications->isNotEmpty())
<div class="section">
    <h3>Visas</h3>
    <table>
        <thead><tr><th>Country</th><th>Type</th><th>Status</th></tr></thead>
        <tbody>
        @foreach ($trip->visaApplications as $v)
        <tr><td>{{ $v->country }}</td><td>{{ str_replace('_', ' ', ucfirst($v->visa_type ?? 'tourist')) }}</td><td>{{ $v->status }}</td></tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

@if ($trip->insurancePolicies->isNotEmpty())
<div class="section">
    <h3>Insurance</h3>
    <table>
        <thead><tr><th>Policy</th><th>Type</th><th>Valid From</th><th>Valid To</th></tr></thead>
        <tbody>
        @foreach ($trip->insurancePolicies as $ins)
        <tr><td>{{ $ins->policy_number ?: '—' }}</td><td>{{ str_replace('_', ' ', ucfirst($ins->type ?? 'travel')) }}</td><td>{{ $ins->start_date?->format('M d, Y') }}</td><td>{{ $ins->end_date?->format('M d, Y') }}</td></tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

@if ($trip->activities->isNotEmpty())
<div class="section">
    <h3>Activities</h3>
    <table>
        <thead><tr><th>Activity</th><th>Location</th><th>Date</th><th>Time</th></tr></thead>
        <tbody>
        @foreach ($trip->activities as $act)
        <tr>
            <td>{{ $act->name }}</td>
            <td>{{ $act->location }}</td>
            <td>{{ $act->date?->format('M d, Y') }}</td>
            <td>{{ $act->time?->format('H:i') ?: '—' }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

@if ($trip->cruiseBookings->isNotEmpty())
<div class="section">
    <h3>Cruises</h3>
    <table>
        <thead><tr><th>Cruise Line</th><th>Ship</th><th>Ports</th><th>Departure</th><th>Arrival</th></tr></thead>
        <tbody>
        @foreach ($trip->cruiseBookings as $c)
        <tr>
            <td>{{ $c->cruise_line ?: '—' }}</td>
            <td>{{ $c->ship_name ?: '—' }}</td>
            <td>{{ $c->departure_port ?: '—' }} → {{ $c->arrival_port ?: '—' }}</td>
            <td>{{ $c->departure_date?->format('M d, Y') }}</td>
            <td>{{ $c->arrival_date?->format('M d, Y') }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

@if ($trip->trainBookings->isNotEmpty())
<div class="section">
    <h3>Trains</h3>
    <table>
        <thead><tr><th>Company</th><th>Train #</th><th>From</th><th>To</th><th>Departure</th><th>Arrival</th></tr></thead>
        <tbody>
        @foreach ($trip->trainBookings as $tr)
        <tr>
            <td>{{ $tr->train_company ?: '—' }}</td>
            <td>{{ $tr->train_number ?: '—' }}</td>
            <td>{{ $tr->departure_station ?: '—' }}</td>
            <td>{{ $tr->arrival_station ?: '—' }}</td>
            <td>{{ $tr->departure_datetime?->format('M d, H:i') }}</td>
            <td>{{ $tr->arrival_datetime?->format('M d, H:i') }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

@if ($trip->carRentals->isNotEmpty())
<div class="section">
    <h3>Car Rentals</h3>
    <table>
        <thead><tr><th>Company</th><th>Car Type</th><th>Pickup</th><th>Dropoff</th><th>From</th><th>To</th></tr></thead>
        <tbody>
        @foreach ($trip->carRentals as $ca)
        <tr>
            <td>{{ $ca->company ?: '—' }}</td>
            <td>{{ $ca->car_type ?: '—' }}</td>
            <td>{{ $ca->pickup_location ?: '—' }}</td>
            <td>{{ $ca->dropoff_location ?: '—' }}</td>
            <td>{{ $ca->pickup_datetime?->format('M d, H:i') }}</td>
            <td>{{ $ca->dropoff_datetime?->format('M d, H:i') }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

@if ($trip->packageBookings->isNotEmpty())
<div class="section">
    <h3>Packages</h3>
    <table>
        <thead><tr><th>Package</th><th>Type</th><th>Start</th><th>End</th></tr></thead>
        <tbody>
        @foreach ($trip->packageBookings as $pk)
        <tr>
            <td>{{ $pk->package_name ?: '—' }}</td>
            <td>{{ $pk->package_type ?: '—' }}</td>
            <td>{{ $pk->start_date?->format('M d, Y') }}</td>
            <td>{{ $pk->end_date?->format('M d, Y') }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

@if ($trip->otherServices->isNotEmpty())
<div class="section">
    <h3>Other Services</h3>
    <table>
        <thead><tr><th>Service</th><th>Type</th><th>Date</th></tr></thead>
        <tbody>
        @foreach ($trip->otherServices as $o)
        <tr>
            <td>{{ $o->service_name ?: '—' }}</td>
            <td>{{ $o->service_type ?: '—' }}</td>
            <td>{{ $o->service_date?->format('M d, Y') }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

<div class="footer">
    <p>Generated by TravelBox ERP &bull; {{ now()->format('M d, Y H:i') }} &bull; travelbox.my</p>
    <p style="font-size:7pt">This is a computer-generated itinerary. Please verify all details with your travel consultant.</p>
</div>

</body>
</html>
