<?php

namespace App\Policies;

use App\Models\Medication;
use App\Models\User;

class MedicationPolicy
{
    /**
     * The catalog is staff-only: pharmacy stock and pricing is not
     * something patients (or doctors) browse or manage. Admin oversees
     * everything; the pharmacist owns the shelf.
     */
    private function isStaff(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('pharmacist');
    }

    /**
     * Determine whether the user can view the catalog listing.
     */
    public function viewAny(User $user): bool
    {
        return $this->isStaff($user);
    }

    /**
     * Determine whether the user can view a single medication.
     */
    public function view(User $user, Medication $medication): bool
    {
        return $this->isStaff($user);
    }

    /**
     * Determine whether the user can add a medication to the catalog.
     */
    public function create(User $user): bool
    {
        return $this->isStaff($user);
    }

    /**
     * Determine whether the user can update a medication.
     */
    public function update(User $user, Medication $medication): bool
    {
        return $this->isStaff($user);
    }

    /**
     * Determine whether the user can delete a medication.
     */
    public function delete(User $user, Medication $medication): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore a medication.
     */
    public function restore(User $user, Medication $medication): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete a medication.
     */
    public function forceDelete(User $user, Medication $medication): bool
    {
        return false;
    }
}
