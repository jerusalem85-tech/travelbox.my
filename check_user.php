<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$u = App\Models\User::first();
if ($u) { echo $u->id, " | ", $u->name, " | ", $u->email, PHP_EOL; } else { echo "NO USERS"; }
