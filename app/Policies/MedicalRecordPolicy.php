<?php

namespace App\Policies;

use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\User;

class MedicalRecordPolicy
{
    /**
     * Can this user view the records of this patient?
     * (index is about a patient's collection, so we pass the Patient.)
     */
    public function viewAny(User $user, Patient $patient): bool
    {
        if ($user->hasRole('admin') || $user->hasRole('doctor')) {
            return true;
        }

        // A patient may only see their own history.
        return $user->hasRole('patient') && $user->patient?->patient_id === $patient->patient_id;
    }

    /**
     * Can this user add a record to this patient's file?
     * Patients never write their own medical records.
     */
    public function create(User $user, Patient $patient): bool
    {
        return $user->hasRole('admin') || $user->hasRole('doctor');
    }

    /**
     * Can this user edit an existing record?
     */
    public function update(User $user, MedicalRecord $record): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        // A doctor may edit only records they authored.
        return $user->hasRole('doctor') && $user->doctor?->doctor_id === $record->doctor_id;
    }
}
