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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id('appointment_id');
            $table->dateTime('scheduled_at');
            $table->string('status')->default('scheduled');
            $table->text('reason')->nullable();
            $table->foreignId('patient_id')->constrained(table: 'patients', column: 'patient_id')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained(table: 'doctors', column: 'doctor_id')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
