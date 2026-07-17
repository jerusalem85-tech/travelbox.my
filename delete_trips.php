<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$count = App\Models\Trip::count();
echo "Deleting $count trips...\n";

App\Models\Trip::all()->each(function ($trip) {
    $trip->delete(); // soft deletes, cascades to services
});

echo "Deleted " . App\Models\Trip::withTrashed()->count() . " trips (soft-deleted).\n";
