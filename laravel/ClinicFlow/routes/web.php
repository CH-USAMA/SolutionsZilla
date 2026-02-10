<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WhatsAppWebhookController;
use App\Http\Controllers\WhatsAppSettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// WhatsApp Webhook (no auth required - Meta calls this)
Route::post('/webhook/whatsapp', [WhatsAppWebhookController::class, 'handle'])->name('whatsapp.webhook');


Route::middleware(['auth', 'verified', 'clinic.tenant'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Doctors
    Route::resource('doctors', DoctorController::class);

    // Patients
    Route::resource('patients', PatientController::class);
    Route::post('/patients/{patient}/documents', [PatientController::class, 'uploadDocument'])
        ->name('patients.documents.upload');
    Route::delete('/patients/{patient}/documents/{document}', [PatientController::class, 'deleteDocument'])
        ->name('patients.documents.delete');

    // Appointments
    Route::resource('appointments', AppointmentController::class);
    Route::patch('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])
        ->name('appointments.update-status');

    // Reports
    Route::resource('reports', ReportController::class)->only(['index']);

    // WhatsApp Manager
    Route::get('/whatsapp/settings', [WhatsAppSettingsController::class, 'index'])->name('whatsapp.settings');
    Route::post('/whatsapp/settings', [WhatsAppSettingsController::class, 'update'])->name('whatsapp.settings.update');
    Route::get('/whatsapp/logs', [WhatsAppSettingsController::class, 'logs'])->name('whatsapp.logs');
    Route::post('/whatsapp/test', [WhatsAppSettingsController::class, 'test'])->name('whatsapp.test');
    Route::post('/whatsapp/create-test-appointment', [WhatsAppSettingsController::class, 'createTestAppointment'])->name('whatsapp.test.appointment');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Clinic Settings
    Route::get('/clinic/settings', [\App\Http\Controllers\ClinicController::class, 'edit'])->name('clinic.edit');
    Route::patch('/clinic/settings', [\App\Http\Controllers\ClinicController::class, 'update'])->name('clinic.update');

    // Billing
    Route::get('/billing', [\App\Http\Controllers\BillingController::class, 'index'])->name('billing.index');
});

require __DIR__ . '/auth.php';
