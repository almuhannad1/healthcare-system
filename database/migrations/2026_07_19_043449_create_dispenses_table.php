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
        Schema::create('dispenses', function (Blueprint $table) {
            $table->id('dispense_id');
            $table->foreignId('medication_id')
                ->constrained(table: 'medications', column: 'medication_id')
                ->restrictOnDelete();
            $table->foreignId('patient_id')
                ->constrained(table: 'patients', column: 'patient_id')
                ->cascadeOnDelete();
            $table->foreignId('user_id')->constrained();   // who dispensed (the staff user)
            $table->unsignedInteger('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispenses');
    }
};
