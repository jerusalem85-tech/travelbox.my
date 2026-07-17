<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_details', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // airport, hotel, port, station, custom
            $table->string('pickup_location');
            $table->string('dropoff_location');
            $table->datetime('pickup_datetime');
            $table->string('vehicle_type'); // sedan, van, bus, coach
            $table->unsignedInteger('number_of_passengers')->default(1);
            $table->string('booking_reference')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();
            $table->string('vehicle_plate')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_details');
    }
};