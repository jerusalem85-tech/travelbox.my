<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_numbering', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // invoice, voucher, receipt, service-summary, etc.
            $table->string('prefix', 10); // INV, VCH, RCPT, SS
            $table->unsignedInteger('next_number')->default(1);
            $table->unsignedInteger('padding')->default(6); // 000001
            $table->string('separator', 3)->default('-'); // INV-2026-000001
            $table->boolean('use_year')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_numbering');
    }
};