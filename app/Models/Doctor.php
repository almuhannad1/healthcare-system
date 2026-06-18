<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id', 'doctor_id');
    }
}
