<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('payment_number')->unique();
            $table->foreignUuid('trip_id')->constrained('trips')->cascadeOnDelete();
            $table->string('type'); // incoming, outgoing
            $table->string('category'); // customer_payment, supplier_payment, expense, refund
            $table->string('payment_method'); // cash, bank_transfer, credit_card, cheque, online
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->decimal('exchange_rate', 10, 4)->default(1);
            $table->date('payment_date');
            $table->string('reference')->nullable();
            $table->text('description')->nullable();
            $table->uuidMorphs('payer'); // customer or supplier
            $table->foreignUuid('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->string('receipt_number')->nullable();
            $table->string('status')->default('completed');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
