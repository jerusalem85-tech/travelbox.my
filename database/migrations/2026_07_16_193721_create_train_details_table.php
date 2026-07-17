<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('train_details', function (Blueprint $table) {
            $table->id();
            $table->string('company');
            $table->string('train_number');
            $table->string('departure_station');
            $table->string('arrival_station');
            $table->datetime('departure_datetime');
            $table->datetime('arrival_datetime');
            $table->string('class')->nullable();
            $table->string('carriage')->nullable();
            $table->string('seat')->nullable();
            $table->string('booking_reference')->nullable();
            $table->string('ticket_type')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('train_details');
    }
};