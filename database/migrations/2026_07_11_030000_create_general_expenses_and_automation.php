<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('general_expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('category'); // office, utilities, marketing, salaries, travel, other
            $table->string('description');
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->date('expense_date');
            $table->string('payment_method')->nullable(); // cash, bank, credit_card
            $table->string('reference')->nullable();
            $table->string('vendor')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('pending'); // pending, paid, cancelled
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('trip_automation_log', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('trip_id')->constrained('trips')->cascadeOnDelete();
            $table->string('action'); // invoice_created, voucher_sent, accounting_posted, email_sent, whatsapp_sent, calendar_event
            $table->string('status'); // success, failed
            $table->text('result')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_automation_log');
        Schema::dropIfExists('general_expenses');
    }
};
