<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('entry_number')->unique();
            $table->foreignUuid('trip_id')->nullable()->constrained('trips')->cascadeOnDelete();
            $table->date('date');
            $table->text('description');
            $table->string('type'); // customer_payment, supplier_payment, expense, refund, invoice, receipt, credit_note, debit_note
            $table->uuidMorphs('reference');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
