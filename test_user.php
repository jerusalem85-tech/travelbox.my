<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$users = App\Models\User::all();
echo 'Total users: ' . $users->count() . PHP_EOL;
foreach ($users as $u) {
    echo $u->id . ' | ' . $u->name . ' | ' . $u->email . PHP_EOL;
}
