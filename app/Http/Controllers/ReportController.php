<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\Medication;
use App\Models\Patient;

class ReportController extends Controller
{
    public function index()
    {
        // Staff only — patients have no business on a clinic-wide dashboard
        abort_unless(auth()->user()->hasRole('admin'), 403);

        // 1. Headline numbers
        $totalPatients = Patient::count();

        $appointmentsThisMonth = Appointment::whereBetween('scheduled_at', [
            now()->startOfMonth(), now()->endOfMonth(),
        ])->count();

        $revenueCents = Invoice::where('status', 'paid')->sum('total_cents');
        $unpaidCents = Invoice::where('status', 'unpaid')->sum('total_cents');

        // 2. Appointments per doctor (top 10)
        $busiestDoctors = Doctor::withCount('appointments')
            ->orderByDesc('appointments_count')
            ->limit(10)
            ->get();

        // 3. Low stock — the alert list
        $lowStock = Medication::whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->orderBy('stock_quantity')
            ->get();

        // 4. Unpaid invoices, oldest first (chase the stalest)
        $unpaidInvoices = Invoice::with('patient')
            ->where('status', 'unpaid')
            ->oldest()
            ->limit(10)
            ->get();

        return view('reports.index', compact(
            'totalPatients', 'appointmentsThisMonth', 'revenueCents', 'unpaidCents',
            'busiestDoctors', 'lowStock', 'unpaidInvoices',
        ));
    }
}
