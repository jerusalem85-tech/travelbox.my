<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flight_details', function (Blueprint $table) {
            $table->id();
            $table->string('airline');
            $table->string('flight_number');
            $table->string('departure_airport');
            $table->string('arrival_airport');
            $table->string('departure_terminal')->nullable();
            $table->string('arrival_terminal')->nullable();
            $table->datetime('departure_datetime');
            $table->datetime('arrival_datetime');
            $table->string('booking_reference')->nullable();
            $table->string('ticket_number')->nullable();
            $table->string('class')->nullable(); // Economy, Business, First
            $table->string('cabin')->nullable();
            $table->string('fare_basis')->nullable();
            $table->string('baggage')->nullable();
            $table->string('seat')->nullable();
            $table->string('meal')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flight_details');
    }
};