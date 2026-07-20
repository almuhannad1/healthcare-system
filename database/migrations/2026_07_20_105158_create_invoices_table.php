<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id('invoice_id');
            $table->foreignId('appointment_id')
                ->constrained(table: 'appointments', column: 'appointment_id')
                ->restrictOnDelete();
            $table->foreignId('patient_id')
                ->constrained(table: 'patients', column: 'patient_id')
                ->restrictOnDelete();
            $table->unsignedInteger('total_cents')->default(0);
            $table->string('status')->default('unpaid');   // unpaid | paid
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
