<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('trip_id');
            $table->uuid('customer_id')->nullable();
            $table->uuid('supplier_id')->nullable();
            $table->uuid('service_id')->nullable();
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->string('type'); // customer (receivable), supplier (payable)
            $table->string('label');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('USD');
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->string('status')->default('pending');
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->string('payment_method')->nullable();
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('trip_id')->references('id')->on('trips')->cascadeOnDelete();
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->nullOnDelete();
            $table->foreign('service_id')->references('id')->on('services')->nullOnDelete();

            $table->index(['trip_id', 'type', 'status']);
            $table->index(['customer_id', 'status']);
            $table->index(['supplier_id', 'status']);
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_schedules');
    }
};
