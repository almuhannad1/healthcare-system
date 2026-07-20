<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Doctor extends Model
{
    use HasFactory;

    protected $primaryKey = 'doctor_id';

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'specialty',
        'consultation_fee_cents',
        'phone',
        'email',
        'doctor_id',
    ];

    /**
     * This doctor's fee, falling back to the clinic-wide default
     * when they don't set one of their own.
     */
    public function consultationFeeCents(): int
    {
        return $this->consultation_fee_cents ?? config('billing.consultation_fee_cents');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id', 'doctor_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class, 'doctor_id', 'doctor_id');
    }
}
