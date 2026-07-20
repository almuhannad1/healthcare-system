<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DispenseController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->get('/admin', fn () => 'Admin area');
Route::middleware(['auth', 'role:doctor'])->get('/doctor', fn () => 'Doctor area');
// appointments
Route::resource('appointments', AppointmentController::class)->middleware('auth');

Route::resource('patients.records', MedicalRecordController::class)->middleware('auth');

Route::resource('patients', PatientController::class)
    ->only('index')
    ->middleware('auth');

Route::resource('patients.records', MedicalRecordController::class)
    ->middleware('auth');

Route::resource('appointments', AppointmentController::class)
    ->except('destroy')
    ->middleware('auth');

Route::resource('medications', MedicationController::class)
    ->only(['index', 'create', 'store', 'edit', 'update'])
    ->middleware('auth');

Route::get('medications/{medication}/dispense', [DispenseController::class, 'create'])
    ->name('medications.dispense.create')->middleware('auth');
Route::post('medications/{medication}/dispense', [DispenseController::class, 'store'])
    ->name('medications.dispense.store')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');

    Route::post('appointments/{appointment}/invoice', [InvoiceController::class, 'store'])
        ->name('appointments.invoice.store');

    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])
        ->name('invoices.pdf');

    Route::patch('invoices/{invoice}/paid', [InvoiceController::class, 'markPaid'])
        ->name('invoices.paid');
});

require __DIR__.'/auth.php';
