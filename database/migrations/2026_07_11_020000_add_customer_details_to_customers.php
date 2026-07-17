<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('passport_issue_date')->nullable()->after('passport_expiry');
            $table->string('passport_issue_place')->nullable()->after('passport_issue_date');
            $table->string('place_of_birth')->nullable()->after('date_of_birth');
            $table->string('sex')->nullable()->after('place_of_birth');
            $table->text('favorite_destinations')->nullable()->after('notes');
            $table->integer('loyalty_points')->default(0)->after('favorite_destinations');
            $table->json('visa_info')->nullable()->after('loyalty_points');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['passport_issue_date', 'passport_issue_place', 'place_of_birth', 'sex', 'favorite_destinations', 'loyalty_points', 'visa_info']);
        });
    }
};
