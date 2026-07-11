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
        'phone',
        'email',
        'doctor_id',
    ];

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
