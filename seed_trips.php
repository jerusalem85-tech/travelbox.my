<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$admin = App\Models\User::where('email', 'admin@travelbox.my')->first();
$customer = App\Models\Customer::first();
if (!$customer) { $customer = App\Models\Customer::factory()->create(['customer_code'=>'CUST-0001','first_name'=>'Ahmed','last_name'=>'Abdullah','email'=>'ahmed@example.com','phone'=>'+60123456789']); }
$destinations = ['Paris, France','Tokyo, Japan','New York, USA','Sydney, Australia','London, UK','Rome, Italy','Bangkok, Thailand','Dubai, UAE','Barcelona, Spain','Singapore','Istanbul, Turkey','Bali, Indonesia','Amsterdam, Netherlands','Berlin, Germany','Prague, Czech Republic','Hong Kong, China','Buenos Aires, Argentina','Cairo, Egypt','Kuala Lumpur, Malaysia','Lisbon, Portugal','Dublin, Ireland','Seoul, South Korea','Mumbai, India','Hanoi, Vietnam','Stockholm, Sweden','Vienna, Austria','Budapest, Hungary','Punta Cana, Dominican Republic','Cancun, Mexico','Phuket, Thailand','Cape Town, South Africa','Rio de Janeiro, Brazil','Venice, Italy','Moscow, Russia','Reykjavik, Iceland','Marrakech, Morocco','St. Martin, Caribbean','Miami, USA','Los Angeles, USA','San Francisco, USA','Madrid, Spain','Queenstown, New Zealand','Fiji Islands','Maldives','Santorini, Greece','Mykonos, Greece','Antalya, Turkey','Tbilisi, Georgia','Zanzibar, Tanzania','Kathmandu, Nepal'];
function genTripNum() { $y = date('Y'); $last = App\Models\Trip::where('trip_number', 'like', 'T' . $y . '-%')->orderBy('trip_number', 'desc')->first(); $num = $last ? (int)substr($last->trip_number, 6) + 1 : 1; return 'T' . $y . '-' . str_pad($num, 5, '0', STR_PAD_LEFT); }
$created = 0;
foreach ($destinations as $i => $dest) {
    $parts = explode(',', $dest); $city = trim($parts[0]);
    $start = new DateTime('+' . $i . ' days');
    $end = clone $start; $end->modify('+' . random_int(4, 12) . ' days');
    $cost = random_int(10, 40) * 100; $selling = $cost + random_int(5, 20) * 100;
    App\Models\Trip::create(['trip_number'=>genTripNum(),'customer_id'=>$customer->id,'type'=>'package','name'=>$dest,'destination'=>$dest,'start_date'=>$start,'end_date'=>$end,'total_cost_price'=>$cost,'total_selling_price'=>$selling,'currency'=>'USD','status'=>['confirmed','pending','cancelled'][array_rand([0,1,2])],'created_by'=>$admin->id]);
    $created++;
}
echo 'Created ' . $created . ' trips.', PHP_EOL;
