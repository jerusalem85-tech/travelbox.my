<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_details', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // all_inclusive, city_break, safari, honeymoon, family, group
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('destination');
            $table->unsignedInteger('number_of_nights')->nullable();
            $table->unsignedInteger('number_of_rooms')->default(1);
            $table->string('room_type')->nullable();
            $table->string('meal_plan')->nullable();
            $table->string('booking_reference')->nullable();
            $table->text('inclusions')->nullable();
            $table->text('exclusions')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_details');
    }
};