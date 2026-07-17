<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
foreach (App\Models\Supplier::all() as $s) {
    echo $s->id, " | ", $s->company_name, " | ", $s->type, PHP_EOL;
}
