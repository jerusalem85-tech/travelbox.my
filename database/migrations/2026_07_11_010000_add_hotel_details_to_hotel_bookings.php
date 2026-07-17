<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotel_bookings', function (Blueprint $table) {
            $table->string('address')->nullable()->after('city');
            $table->string('confirmation_number')->nullable()->after('booking_reference');
            $table->string('check_in_time')->nullable()->after('check_in');
            $table->string('check_out_time')->nullable()->after('check_out');
            $table->text('cancellation_policy')->nullable()->after('notes');
            $table->decimal('latitude', 10, 7)->nullable()->after('cancellation_policy');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });
    }

    public function down(): void
    {
        Schema::table('hotel_bookings', function (Blueprint $table) {
            $table->dropColumn(['address', 'confirmation_number', 'check_in_time', 'check_out_time', 'cancellation_policy', 'latitude', 'longitude']);
        });
    }
};
