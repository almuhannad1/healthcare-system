<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Invoice::class);

        $user = auth()->user();

        $query = Invoice::with('patient', 'appointment.doctor')->latest('invoice_id');

        // A patient sees only their own bills; admin sees the lot.
        if (! $user->hasRole('admin')) {
            $query->where('patient_id', $user->patient?->patient_id);
        }

        $invoices = $query->get();

        return view('invoices.index', compact('invoices'));
    }

    public function store(Appointment $appointment)
    {
        $this->authorize('create', Invoice::class);

        $invoice = Invoice::generateForAppointment($appointment);

        return redirect()
            ->route('invoices.index')
            ->with('success', "Invoice #{$invoice->invoice_id} — \${$invoice->totalDollars()}.");
    }

    public function pdf(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        $invoice->load('items', 'patient', 'appointment.doctor');

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));

        return $pdf->download("invoice-{$invoice->invoice_id}.pdf");
    }

    public function markPaid(Invoice $invoice)
    {
        $this->authorize('markPaid', $invoice);

        $invoice->update(['status' => 'paid']);

        return back()->with('success', "Invoice #{$invoice->invoice_id} marked paid.");
    }
}
