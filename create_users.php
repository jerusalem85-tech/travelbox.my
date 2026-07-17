<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Just create the users
use App\Models\User;

$admin = User::factory()->create([
    'name' => 'Admin',
    'email' => 'admin@travelbox.my',
]);

$manager = User::factory()->create([
    'name' => 'Manager',
    'email' => 'manager@travelbox.my',
]);

$accountant = User::factory()->create([
    'name' => 'Accountant',
    'email' => 'accountant@travelbox.my',
]);

echo 'Users created!' . PHP_EOL;
echo 'admin@travelbox.my / password' . PHP_EOL;
