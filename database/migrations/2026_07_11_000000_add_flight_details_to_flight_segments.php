<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('flight_segments', function (Blueprint $table) {
            $table->string('cabin')->nullable()->after('class');
            $table->string('fare_basis')->nullable()->after('cabin');
            $table->string('departure_terminal')->nullable()->after('arrival_airport');
            $table->string('arrival_terminal')->nullable()->after('departure_terminal');
            $table->string('baggage')->nullable()->after('arrival_terminal');
            $table->string('seat')->nullable()->after('baggage');
            $table->string('meal')->nullable()->after('seat');
        });
    }

    public function down(): void
    {
        Schema::table('flight_segments', function (Blueprint $table) {
            $table->dropColumn(['cabin', 'fare_basis', 'departure_terminal', 'arrival_terminal', 'baggage', 'seat', 'meal']);
        });
    }
};
