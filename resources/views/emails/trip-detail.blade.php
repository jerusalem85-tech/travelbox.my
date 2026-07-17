<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><style>body{font-family:sans-serif;line-height:1.6;color:#333;max-width:600px;margin:0 auto;padding:20px}
h1{font-size:20px;color:#1a56db;margin-bottom:4px}
.header{border-bottom:2px solid #e5e7eb;padding-bottom:12px;margin-bottom:20px}
.section{margin-bottom:16px}
.section h3{font-size:14px;color:#6b7280;text-transform:uppercase;margin:0 0 6px}
table{width:100%;border-collapse:collapse;font-size:13px}
table td{padding:4px 8px;border-bottom:1px solid #f3f4f6}
.fw{font-weight:600}
.footer{border-top:2px solid #e5e7eb;padding-top:12px;margin-top:20px;font-size:12px;color:#9ca3af}
.message{background:#f9fafb;border-left:4px solid #3b82f6;padding:12px;margin-bottom:16px;font-size:14px;white-space:pre-wrap}</style></head>
<body>
<div class="header">
    <h1>TravelBox – Trip Details</h1>
    <p style="margin:0;font-size:13px;color:#6b7280">Trip {{ $trip->trip_number }}</p>
</div>

@if ($customMessage)
<div class="message">{{ $customMessage }}</div>
@endif

<div class="section">
    <h3>Trip Info</h3>
    <table>
        <tr><td class="fw">Status</td><td>{{ ucfirst(str_replace('_', ' ', $trip->status)) }}</td></tr>
        <tr><td class="fw">Type</td><td>{{ ucfirst($trip->type) }}</td></tr>
        <tr><td class="fw">Destination</td><td>{{ $trip->destination ?? '—' }}</td></tr>
        <tr><td class="fw">Dates</td><td>{{ $trip->start_date?->format('M d, Y') }} – {{ $trip->end_date?->format('M d, Y') }}</td></tr>
        <tr><td class="fw">Customer</td><td>{{ $trip->customer?->full_name ?? '—' }}</td></tr>
    </table>
</div>

@if ($trip->flightSegments->isNotEmpty())
<div class="section">
    <h3>Flights</h3>
    <table>
        @foreach ($trip->flightSegments as $fs)
        <tr><td class="fw">{{ $fs->airline }} {{ $fs->flight_number }}</td><td>{{ $fs->departure_airport }} → {{ $fs->arrival_airport }}<br><small>{{ $fs->departure_datetime?->format('M d, H:i') }}</small></td></tr>
        @endforeach
    </table>
</div>
@endif

@if ($trip->hotelBookings->isNotEmpty())
<div class="section">
    <h3>Hotels</h3>
    <table>
        @foreach ($trip->hotelBookings as $hb)
        <tr><td class="fw">{{ $hb->hotel_name }}</td><td>{{ $hb->city }}<br><small>{{ $hb->check_in?->format('M d') }} – {{ $hb->check_out?->format('M d') }}</small></td></tr>
        @endforeach
    </table>
</div>
@endif

@if ($trip->transferBookings->isNotEmpty())
<div class="section">
    <h3>Transfers</h3>
    <table>
        @foreach ($trip->transferBookings as $tb)
        <tr><td class="fw">{{ $tb->pickup_location }} → {{ $tb->dropoff_location }}</td><td><small>{{ $tb->pickup_datetime?->format('M d, Y H:i') }}</small></td></tr>
        @endforeach
    </table>
</div>
@endif

@if ($trip->visaApplications->isNotEmpty())
<div class="section">
    <h3>Visas</h3>
    <table>
        @foreach ($trip->visaApplications as $v)
        <tr><td class="fw">{{ $v->country }}</td><td>{{ str_replace('_', ' ', ucfirst($v->visa_type ?? 'tourist')) }} – {{ $v->status }}</td></tr>
        @endforeach
    </table>
</div>
@endif

@if ($trip->insurancePolicies->isNotEmpty())
<div class="section">
    <h3>Insurance</h3>
    <table>
        @foreach ($trip->insurancePolicies as $ins)
        <tr><td class="fw">{{ $ins->policy_number ?: 'Policy' }}</td><td><small>{{ $ins->start_date?->format('M d, Y') }} – {{ $ins->end_date?->format('M d, Y') }}</small></td></tr>
        @endforeach
    </table>
</div>
@endif

@if ($trip->activities->isNotEmpty())
<div class="section">
    <h3>Activities</h3>
    <table>
        @foreach ($trip->activities as $act)
        <tr><td class="fw">{{ $act->name }}</td><td>{{ $act->location }}<br><small>{{ $act->date?->format('M d, Y') }} @if($act->time) {{ $act->time->format('H:i') }} @endif</small></td></tr>
        @endforeach
    </table>
</div>
@endif

@if ($trip->cruiseBookings->isNotEmpty())
<div class="section">
    <h3>Cruises</h3>
    <table>
        @foreach ($trip->cruiseBookings as $c)
        <tr><td class="fw">{{ $c->cruise_line ?: 'Cruise' }}</td><td>{{ $c->ship_name ?: '—' }}<br><small>{{ $c->departure_port ?: '—' }} → {{ $c->arrival_port ?: '—' }} ({{ $c->departure_date?->format('M d, Y') }})</small></td></tr>
        @endforeach
    </table>
</div>
@endif

@if ($trip->trainBookings->isNotEmpty())
<div class="section">
    <h3>Trains</h3>
    <table>
        @foreach ($trip->trainBookings as $tr)
        <tr><td class="fw">{{ $tr->train_company ?: 'Train' }} {{ $tr->train_number ?: '' }}</td><td><small>{{ $tr->departure_station ?: '—' }} → {{ $tr->arrival_station ?: '—' }} ({{ $tr->departure_datetime?->format('M d, H:i') }})</small></td></tr>
        @endforeach
    </table>
</div>
@endif

@if ($trip->carRentals->isNotEmpty())
<div class="section">
    <h3>Car Rentals</h3>
    <table>
        @foreach ($trip->carRentals as $ca)
        <tr><td class="fw">{{ $ca->company ?: 'Car Rental' }} — {{ $ca->car_type ?: '—' }}</td><td><small>{{ $ca->pickup_location ?: '—' }} → {{ $ca->dropoff_location ?: '—' }} ({{ $ca->pickup_datetime?->format('M d, H:i') }})</small></td></tr>
        @endforeach
    </table>
</div>
@endif

@if ($trip->packageBookings->isNotEmpty())
<div class="section">
    <h3>Packages</h3>
    <table>
        @foreach ($trip->packageBookings as $pk)
        <tr><td class="fw">{{ $pk->package_name ?: 'Package' }}</td><td>{{ $pk->package_type ?: '—' }}<br><small>{{ $pk->start_date?->format('M d, Y') }} – {{ $pk->end_date?->format('M d, Y') }}</small></td></tr>
        @endforeach
    </table>
</div>
@endif

@if ($trip->otherServices->isNotEmpty())
<div class="section">
    <h3>Other Services</h3>
    <table>
        @foreach ($trip->otherServices as $o)
        <tr><td class="fw">{{ $o->service_name ?: 'Service' }}</td><td>{{ $o->service_type ?: '—' }}<br><small>{{ $o->service_date?->format('M d, Y') }}</small></td></tr>
        @endforeach
    </table>
</div>
@endif

<div class="footer">
    <p>Sent via TravelBox ERP | travelbox.my</p>
</div>
</body>
</html>
