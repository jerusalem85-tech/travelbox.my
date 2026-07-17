<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Carbon\Carbon;

$customer = App\Models\Customer::first();
if (!$customer) {
    $customer = App\Models\Customer::factory()->create([
        'customer_code' => 'CUST-0001',
        'first_name' => 'Ahmed',
        'last_name' => 'Abdullah',
        'email' => 'ahmed@example.com',
        'phone' => '+60123456789',
    ]);
    echo "Created customer: {$customer->first_name} {$customer->last_name}\n";
}

$user = App\Models\User::first();
if (!$user) {
    echo "Error: No user found.\n";
    exit(1);
}

$destinations = [
    'Paris, France', 'Tokyo, Japan', 'New York, USA', 'Sydney, Australia', 'London, UK',
    'Rome, Italy', 'Bangkok, Thailand', 'Dubai, UAE', 'Barcelona, Spain', 'Singapore',
    'Istanbul, Turkey', 'Bali, Indonesia', 'Amsterdam, Netherlands', 'Berlin, Germany', 'Prague, Czech Republic',
    'Hong Kong, China', 'Buenos Aires, Argentina', 'Cairo, Egypt', 'Kuala Lumpur, Malaysia', 'Lisbon, Portugal',
    'Dublin, Ireland', 'Seoul, South Korea', 'Mumbai, India', 'Hanoi, Vietnam', 'Stockholm, Sweden',
    'Vienna, Austria', 'Budapest, Hungary', 'Punta Cana, Dominican Republic', 'Cancun, Mexico',
    'Phuket, Thailand', 'Cape Town, South Africa', 'Rio de Janeiro, Brazil', 'Venice, Italy', 'Moscow, Russia',
    'Reykjavik, Iceland', 'Marrakech, Morocco', 'St. Martin, Caribbean', 'Miami, USA', 'Los Angeles, USA',
    'San Francisco, USA', 'Madrid, Spain', "Queenstown, New Zealand", "Fiji Islands", "Maldives",
    "Santorini, Greece", "Mykonos, Greece", "Antalya, Turkey", "Tbilisi, Georgia", "Zanzibar, Tanzania",
    "Kathmandu, Nepal",
];

function generateTripNumber() {
    $year = date('Y');
    $last = App\Models\Trip::where('trip_number', 'like', "T{$year}-%")
        ->orderBy('trip_number', 'desc')
        ->first();
    $num = $last ? (int) substr($last->trip_number, 6) + 1 : 1;
    return "T{$year}-" . str_pad($num, 5, '0', STR_PAD_LEFT);
}

$created = 0;
foreach ($destinations as $i => $dest) {
    $start = Carbon::now()->addDays($i);
    $end = (clone $start)->addDays(random_int(4, 12));
    $cost = random_int(10, 40) * 100;
    $selling = $cost + random_int(5, 20) * 100;

    $trip = App\Models\Trip::create([
        'trip_number' => generateTripNumber(),
        'customer_id' => $customer->id,
        'type' => 'package',
        'name' => "Trip to $dest",
        'destination' => $dest,
        'start_date' => $start,
        'end_date' => $end,
        'total_cost_price' => $cost,
        'total_selling_price' => $selling,
        'currency' => 'USD',
        'status' => \Illuminate\Support\Arr::random(['confirmed', 'pending', 'cancelled']),
        'created_by' => $user->id,
    ]);

    $created++;
}

echo "Inserted $created trips with unique destinations.\n";
