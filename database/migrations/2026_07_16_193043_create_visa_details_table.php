<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visa_details', function (Blueprint $table) {
            $table->id();
            $table->string('country');
            $table->string('visa_type'); // tourist, business, transit, e-visa, on_arrival
            $table->date('application_date');
            $table->date('expected_delivery_date')->nullable();
            $table->date('actual_delivery_date')->nullable();
            $table->string('embassy')->nullable();
            $table->string('visa_number')->nullable();
            $table->unsignedInteger('number_of_entries')->default(1); // single, multiple
            $table->unsignedInteger('validity_days')->nullable();
            $table->text('requirements')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visa_details');
    }
};