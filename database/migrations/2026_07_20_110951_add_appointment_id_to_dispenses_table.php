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
        Schema::table('dispenses', function (Blueprint $table) {
            // Nullable: existing rows predate this column, and stock can be
            // dispensed outside an appointment. Null simply means "not billable
            // to a visit". A dispense is an audit record, so losing the
            // appointment must never delete it.
            $table->foreignId('appointment_id')
                ->nullable()
                ->constrained(table: 'appointments', column: 'appointment_id')
                ->nullOnDelete();

            $table->index(['appointment_id', 'patient_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dispenses', function (Blueprint $table) {
            $table->dropIndex(['appointment_id', 'patient_id']);
            $table->dropConstrainedForeignId('appointment_id');
        });
    }
};
