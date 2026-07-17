<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_details', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // tour, excursion, adventure, cultural, dining, etc.
            $table->string('location');
            $table->date('date');
            $table->time('time')->nullable();
            $table->unsignedInteger('duration')->nullable(); // in minutes
            $table->unsignedInteger('number_of_participants')->default(1);
            $table->string('booking_reference')->nullable();
            $table->string('guide_name')->nullable();
            $table->string('guide_language')->nullable();
            $table->text('inclusions')->nullable();
            $table->text('exclusions')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_details');
    }
};