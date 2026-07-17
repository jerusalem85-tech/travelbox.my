<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_details', function (Blueprint $table) {
            $table->id();
            $table->string('company');
            $table->string('car_type');
            $table->string('car_model')->nullable();
            $table->string('pickup_location');
            $table->string('dropoff_location');
            $table->datetime('pickup_datetime');
            $table->datetime('dropoff_datetime');
            $table->string('booking_reference')->nullable();
            $table->string('license_plate')->nullable();
            $table->boolean('include_insurance')->default(false);
            $table->unsignedInteger('included_km')->nullable();
            $table->unsignedInteger('daily_limit_km')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_details');
    }
};