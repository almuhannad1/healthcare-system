<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        // Admin sees every invoice; a patient sees their own list.
        // The index query does the narrowing.
        return $user->hasRole('admin')
            || ($user->hasRole('patient') && $user->patient !== null);
    }

    public function view(User $user, Invoice $invoice): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        // A patient sees only their own bills.
        return $user->hasRole('patient')
            && $user->patient?->patient_id === $invoice->patient_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');   // generating invoices is front-desk work
    }

    public function markPaid(User $user, Invoice $invoice): bool
    {
        return $user->hasRole('admin');
    }
}
