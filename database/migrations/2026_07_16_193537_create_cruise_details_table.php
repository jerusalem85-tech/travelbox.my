<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cruise_details', function (Blueprint $table) {
            $table->id();
            $table->string('cruise_line');
            $table->string('ship_name');
            $table->string('cabin_type');
            $table->string('cabin_number')->nullable();
            $table->string('departure_port');
            $table->string('arrival_port');
            $table->date('departure_date');
            $table->date('arrival_date');
            $table->text('itinerary')->nullable();
            $table->string('booking_reference')->nullable();
            $table->string('deck')->nullable();
            $table->string('meal_plan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cruise_details');
    }
};