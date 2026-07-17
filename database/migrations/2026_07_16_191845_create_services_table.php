<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('trip_id');
            $table->uuid('supplier_id')->nullable();
            $table->uuid('passenger_id')->nullable();
            $table->string('type');
            $table->string('name');
            $table->string('status')->default('pending');
            $table->decimal('cost_price', 12, 2)->default(0);
            $table->decimal('selling_price', 12, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->text('notes')->nullable();

            // Supplier reservation fields
            $table->string('supplier_booking_reference')->nullable();
            $table->decimal('supplier_cost', 12, 2)->nullable();
            $table->string('supplier_currency', 3)->nullable();
            $table->date('supplier_due_date')->nullable();
            $table->string('supplier_status')->nullable();
            $table->string('confirmation_file')->nullable();

            // Polymorphic relationship to detail models
            $table->string('detail_type');
            $table->unsignedBigInteger('detail_id');

            // Dates
            $table->date('service_date')->nullable();
            $table->date('service_end_date')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('trip_id')->references('id')->on('trips')->cascadeOnDelete();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->nullOnDelete();
            $table->foreign('passenger_id')->references('id')->on('passengers')->nullOnDelete();

            $table->index(['trip_id', 'type']);
            $table->index(['supplier_id', 'supplier_status']);
            $table->index(['detail_type', 'detail_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
