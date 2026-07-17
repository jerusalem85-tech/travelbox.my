<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_details', function (Blueprint $table) {
            $table->id();
            $table->string('hotel_name');
            $table->string('city');
            $table->string('address')->nullable();
            $table->date('check_in');
            $table->date('check_out');
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->string('room_type');
            $table->string('meal_plan')->nullable(); // BB, HB, FB, AI
            $table->unsignedInteger('number_of_rooms')->default(1);
            $table->string('booking_reference')->nullable();
            $table->string('confirmation_number')->nullable();
            $table->string('cancellation_policy')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_details');
    }
};