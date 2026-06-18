<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;
    protected $primaryKey = 'patient_id';
    protected $fillable = [ 
        'first_name', 'middle_name', 'last_name', 'date_of_birth', 'gender', 'phone', 'email', 'address',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'patient_id', 'patient_id');
    }
}
