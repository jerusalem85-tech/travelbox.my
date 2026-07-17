<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('train_bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('trip_id')->constrained('trips')->cascadeOnDelete();
            $table->foreignUuid('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('train_company')->nullable();
            $table->string('train_number')->nullable();
            $table->string('departure_station')->nullable();
            $table->string('arrival_station')->nullable();
            $table->datetime('departure_datetime')->nullable();
            $table->datetime('arrival_datetime')->nullable();
            $table->string('class')->nullable();
            $table->string('seat')->nullable();
            $table->string('booking_reference')->nullable();
            $table->string('status')->default('confirmed');
            $table->decimal('cost_price', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('train_bookings');
    }
};
