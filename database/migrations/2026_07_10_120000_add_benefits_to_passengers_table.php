<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('passengers', function (Blueprint $table) {
            $table->string('meal_preference')->nullable()->after('passport_issue_place');
            $table->string('seat_preference')->nullable()->after('meal_preference');
            $table->string('ffp_number')->nullable()->after('seat_preference');
            $table->string('ffp_airline')->nullable()->after('ffp_number');
            $table->text('special_requests')->nullable()->after('ffp_airline');
        });
    }

    public function down(): void
    {
        Schema::table('passengers', function (Blueprint $table) {
            $table->dropColumn(['meal_preference', 'seat_preference', 'ffp_number', 'ffp_airline', 'special_requests']);
        });
    }
};
