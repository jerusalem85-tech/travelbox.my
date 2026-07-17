<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$airports = [
    "TLV"=>["city"=>"Tel Aviv"],"DXB"=>["city"=>"Dubai"],"DMK"=>["city"=>"Bangkok"],
    "JFK"=>["city"=>"New York"],"LHR"=>["city"=>"London"],"CDG"=>["city"=>"Paris"],
    "NRT"=>["city"=>"Tokyo"],"SYD"=>["city"=>"Sydney"],"FCO"=>["city"=>"Rome"],
    "BKK"=>["city"=>"Bangkok"],"IST"=>["city"=>"Istanbul"],"HKG"=>["city"=>"Hong Kong"],
    "SIN"=>["city"=>"Singapore"],"KUL"=>["city"=>"Kuala Lumpur"],"ICN"=>["city"=>"Seoul"],
    "MAD"=>["city"=>"Madrid"],"BCN"=>["city"=>"Barcelona"],"AMS"=>["city"=>"Amsterdam"],
    "LAX"=>["city"=>"Los Angeles"],"SFO"=>["city"=>"San Francisco"],"MIA"=>["city"=>"Miami"],
    "CAI"=>["city"=>"Cairo"],"CPT"=>["city"=>"Cape Town"],"GIG"=>["city"=>"Rio"],
    "EZE"=>["city"=>"Buenos Aires"],"DUB"=>["city"=>"Dublin"],"ARN"=>["city"=>"Stockholm"],
    "VIE"=>["city"=>"Vienna"],"PRG"=>["city"=>"Prague"],"BUD"=>["city"=>"Budapest"],
    "HKT"=>["city"=>"Phuket"],"DPS"=>["city"=>"Bali"],"MLE"=>["city"=>"Maldives"],
    "JTR"=>["city"=>"Santorini"],"JMK"=>["city"=>"Mykonos"],"CUN"=>["city"=>"Cancun"],
    "PUJ"=>["city"=>"Punta Cana"],"AYT"=>["city"=>"Antalya"],"TBS"=>["city"=>"Tbilisi"],
    "ZNZ"=>["city"=>"Zanzibar"],"KTM"=>["city"=>"Kathmandu"],"VCE"=>["city"=>"Venice"],
    "KEF"=>["city"=>"Reykjavik"],"RAK"=>["city"=>"Marrakech"],"SXM"=>["city"=>"St. Martin"],
    "ZQN"=>["city"=>"Queenstown"],"HAN"=>["city"=>"Hanoi"],"BOM"=>["city"=>"Mumbai"]
];
$airlines=["Flydubai","Emirates","Qatar Airways","Turkish Airlines","Etihad","British Airways","Singapore Airlines","Air France","Lufthansa","Thai Airways"];
$codes=["FZ","EK","QR","TK","EY","BA","SQ","AF","LH","TG"];
$s=$app->make(App\Models\Supplier::class)->where("type","airline")->get();
if($s->isEmpty()){echo "Error: No airline suppliers.\n";exit(1);}
$trips=$app->make(App\Models\Trip::class)->whereNull("deleted_at")->get();
if($trips->isEmpty()){echo "Error: No trips found.\n";exit(1);}
$hub="DXB";$created=0;
foreach($trips as $t){
    if(!$t->destination)continue;
    $dest="DXB";
    foreach($airports as $c=>$info){if(stripos($t->destination,$info["city"])!==false){$dest=$c;break;}}
    if($dest===$hub)$dest="DXB";
    $sup=$s->random();$ai=array_rand($airlines);$a=$airlines[$ai];$cd=$codes[$ai];
    $st=\Carbon\Carbon::parse($t->start_date);$en=\Carbon\Carbon::parse($t->end_date);
    if(!$st||!$en)continue;
    $cost=random_int(1500,8000);$selling=$cost+random_int(500,1500);
    $d1=(clone $en)->subDays(random_int(0,2))->setTime(random_int(6,22),random_int(0,59));
    $a1=(clone $d1)->addHours(random_int(3,6));$f1=$cd.random_int(100,999);
    $app->make(App\Models\FlightSegment::class)->create(["trip_id"=>$t->id,"supplier_id"=>$sup->id,"type"=>"outbound","airline"=>$a,"flight_number"=>$f1,"departure_airport"=>$hub,"arrival_airport"=>$dest,"departure_datetime"=>$d1,"arrival_datetime"=>$a1,"class"=>"economy","cost_price"=>$cost,"selling_price"=>$selling,"currency"=>"USD","status"=>"confirmed"]);
    $d2=(clone $en)->setTime(random_int(6,22),random_int(0,59));
    $a2=(clone $d2)->addHours(random_int(3,6));$f2=$cd.random_int(100,999);
    $app->make(App\ModeAs\FlightSegment::class)->create(["trip_id"=>$t->id,"supplier_id"=>$sup->id,"type"=>"inbound","airline"=>$a,"flight_number"=>$f2,"departure_airport"=>$dest,"arrival_airport"=>$hub,"departure_datetime"=>$d2,"arrival_datetime"=>$a2,"class"=>"economy","cost_price"=>$cost,"selling_price"=>$selling,"currency"=>"USD","status"=>"confirmed"]);

    $t->recalculateTotals();
    $created++;
}
echo "Added round-trip flights to $created trips.\n";
echo "Done.\n";
