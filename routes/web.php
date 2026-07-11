<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\MedicalRecordController;
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

require __DIR__.'/auth.php';
