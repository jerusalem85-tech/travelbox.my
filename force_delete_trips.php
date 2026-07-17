<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
// Force delete all trips permanently
App\Models\Trip::withTrashed()->each(fn($t) => $t->forceDelete());
echo 'Force deleted all trips.', PHP_EOL;
echo App\Models\Trip::count() . ' trips remain.', PHP_EOL;
