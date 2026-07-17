<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$admin = User::where('email', 'admin@travelbox.my')->first();
$admin->assignRole('super_admin');
echo 'super_admin role assigned' . PHP_EOL;

$manager = User::where('email', 'manager@travelbox.my')->first();
$manager->assignRole('manager');
echo 'manager role assigned' . PHP_EOL;

$accountant = User::where('email', 'accountant@travelbox.my')->first();
$accountant->assignRole('accountant');
echo 'accountant role assigned' . PHP_EOL;
