<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$admin = App\Models\User::where('email', 'admin@travelbox.my')->first();
if (!$admin) { echo 'No admin user found. Create one first.', PHP_EOL; exit; }
$existing = App\Models\Supplier::count();
if ($existing > 0) { echo $existing . ' suppliers already exist. Skipping creation.', PHP_EOL; } else {
    $data = [
        ['type'=>'airline','company_name'=>'Emirates','contact_person'=>'Ahmed','email'=>'reservations@emirates.ae','phone'=>'+97145551234','city'=>'Dubai','country'=>'UAE','preferred_currency'=>'AED','supplier_code'=>'AIR-0001','is_active'=>1,'current_balance'=>0,'created_by'=>$admin->id],
        ['type'=>'airline','company_name'=>'Qatar Airways','contact_person'=>'Fahad','email'=>'corp@qatarairways.qa','phone'=>'+9745551234','city'=>'Doha','country'=>'Qatar','preferred_currency'=>'QAR','supplier_code'=>'AIR-0002','is_active'=>1,'current_balance'=>0,'created_by'=>$admin->id],
        ['type'=>'airline','company_name'=>'Flydubai','contact_person'=>'Khalid','email'=>'groups@flydubai.ae','phone'=>'+97145555678','city'=>'Dubai','country'=>'UAE','preferred_currency'=>'AED','supplier_code'=>'AIR-0003','is_active'=>1,'current_balance'=>0,'created_by'=>$admin->id],
        ['type'=>'hotel','company_name'=>'Marriott Hotels','contact_person'=>'Sara','email'=>'groups@marriott.ae','phone'=>'+97145555678','city'=>'Dubai','country'=>'UAE','preferred_currency'=>'AED','supplier_code'=>'HTL-0001','is_active'=>1,'current_balance'=>0,'created_by'=>$admin->id],
        ['type'=>'hotel','company_name'=>'Hilton Dubai','contact_person'=>'John','email'=>'res@hilton.ae','phone'=>'+97145559012','city'=>'Dubai','country'=>'UAE','preferred_currency'=>'AED','supplier_code'=>'HTL-0002','is_active'=>1,'current_balance'=>0,'created_by'=>$admin->id],
    ];
    foreach ($data as $d) { App\Models\Supplier::create($d); }
    echo 'Created ' . count($data) . ' suppliers.', PHP_EOL;
}
$s = App\Models\Supplier::where('type', 'airline')->get();
echo 'Airline suppliers: ' . $s->count(), PHP_EOL;
$trips = App\Models\Trip::whereNull('deleted_at')->get();
echo 'Trips: ' . $trips->count(), PHP_EOL;
$airlines = ['Flydubai','Emirates','Qatar Airways','Turkish Airlines','Etihad','British Airways','Singapore Airlines','Air France','Lufthansa','Thai Airways'];
$airportMap = ['Paris'=>'CDG','Tokyo'=>'NRT','New York'=>'JFK','Sydney'=>'SYD','London'=>'LHR','Rome'=>'FCO','Bangkok'=>'BKK','Dubai'=>'DXB','Barcelona'=>'BCN','Singapore'=>'SIN','Istanbul'=>'IST','Bali'=>'DPS','Amsterdam'=>'AMS','Berlin'=>'BER','Prague'=>'PRG','Hong Kong'=>'HKG','Buenos Aires'=>'EZE','Cairo'=>'CAI','Kuala Lumpur'=>'KUL','Lisbon'=>'LIS','Dublin'=>'DUB','Seoul'=>'ICN','Mumbai'=>'BOM','Hanoi'=>'HAN','Stockholm'=>'ARN','Vienna'=>'VIE','Budapest'=>'BUD','Phuket'=>'HKT','Cape Town'=>'CPT','Rio de Janeiro'=>'GIG','Venice'=>'VCE','Moscow'=>'SVO','Reykjavik'=>'KEF','Marrakech'=>'RAK','St. Martin'=>'SXM','Miami'=>'MIA','Los Angeles'=>'LAX','San Francisco'=>'SFO','Madrid'=>'MAD','Queenstown'=>'ZQN','Fiji Islands'=>'FJI','Maldives'=>'MLE','Santorini'=>'JTR','Mykonos'=>'JMK','Cancun'=>'CUN','Antalya'=>'AYT','Tbilisi'=>'TBS','Zanzibar'=>'ZNZ','Kathmandu'=>'KTM','Punta Cana'=>'PUJ','Mexico City'=>'MEX'];
$hub = 'DXB'; $created = 0;
foreach ($trips as $t) {
    if (!$t->destination) continue;
    $parts = explode(',', $t->destination);
    $city = trim($parts[0]);
    $destCode = $airportMap[$city] ?? 'DXB';
    if ($destCode === 'DXB') $destCode = 'DXB';
    $sup = $s->random();
    $ai = array_rand($airlines);
    $airlineName = $airlines[$ai];
    $start = $t->start_date ? new DateTime($t->start_date) : null;
    $end = $t->end_date ? new DateTime($t->end_date) : null;
    if (!$start || !$end) continue;
    $cost = random_int(1500, 8000);
    $selling = $cost + random_int(500, 1500);
    $d1 = clone $end; $d1->modify('-' . random_int(0,2) . ' days'); $d1->setTime(random_int(6,22), random_int(0,59));
    $a1 = clone $d1; $a1->modify('+' . random_int(3,6) . ' hours');
    App\Models\FlightSegment::create(['trip_id'=>$t->id,'supplier_id'=>$sup->id,'type'=>'outbound','airline'=>$airlineName,'flight_number'=>$airlineName[0].$airlineName[1] . random_int(100,999),'departure_airport'=>$hub,'arrival_airport'=>$destCode,'departure_datetime'=>$d1,'arrival_datetime'=>$a1,'class'=>'economy','cost_price'=>$cost,'selling_price'=>$selling,'currency'=>'USD','status'=>'confirmed']);
    $d2 = clone $end; $d2->setTime(random_int(6,22), random_int(0,59));
    $a2 = clone $d2; $a2->modify('+' . random_int(3,6) . ' hours');
    App\Models\FlightSegment::create(['trip_id'=>$t->id,'supplier_id'=>$sup->id,'type'=>'inbound','airline'=>$airlineName,'flight_number'=>$airlineName[0].$airlineName[1] . random_int(100,999),'departure_airport'=>$destCode,'arrival_airport'=>$hub,'departure_datetime'=>$d2,'arrival_datetime'=>$a2,'class'=>'economy','cost_price'=>$cost,'selling_price'=>$selling,'currency'=>'USD','status'=>'confirmed']);
    $t->recalculateTotals();
    $created++;
}
echo 'Added round-trip flights to ' . $created . ' trips.', PHP_EOL;
