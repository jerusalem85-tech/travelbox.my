<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurance_details', function (Blueprint $table) {
            $table->id();
            $table->string('policy_number');
            $table->string('type'); // travel, medical, cancellation, baggage
            $table->text('coverage_details')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('provider')->nullable();
            $table->unsignedInteger('max_coverage_amount')->nullable();
            $table->string('currency')->default('USD');
            $table->text('exclusions')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_details');
    }
};