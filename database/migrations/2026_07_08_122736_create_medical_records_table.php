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
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id('record_id');
            $table->foreignId('patient_id')
                ->constrained(table: 'patients', column: 'patient_id')
                ->cascadeOnDelete();
            $table->foreignId('doctor_id')
                ->nullable() 
                ->constrained(table: 'doctors', column: 'doctor_id')
                ->nullOnDelete();
            $table->date('visit_date');
            $table->string('diagnosis');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
