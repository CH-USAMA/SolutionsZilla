<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WhatsAppWebhookController;
use App\Http\Controllers\WhatsAppSettingsController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\SuperAdminDashboardController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ClinicManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Health Checks
Route::get('/health', [HealthController::class, 'check'])->name('health');
Route::get('/up', function () {
    return response()->noContent();
});

// Webhooks
Route::match(['get', 'post'], '/webhook/whatsapp', [WhatsAppWebhookController::class, 'handle'])->name('whatsapp.webhook');
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);


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
    Route::middleware(['plan.limit:appointments'])->group(function () {
        Route::resource('appointments', AppointmentController::class);
    });
    Route::patch('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])
        ->name('appointments.update-status');

    // Reports
    Route::resource('reports', ReportController::class)->only(['index']);

    // WhatsApp Manager
    Route::get('/whatsapp/settings', [WhatsAppSettingsController::class, 'index'])->name('whatsapp.settings');
    Route::post('/whatsapp/settings', [WhatsAppSettingsController::class, 'update'])->name('whatsapp.settings.update');
    Route::get('/whatsapp/logs', [WhatsAppSettingsController::class, 'logs'])->name('whatsapp.logs');

    // WhatsApp Dashboard & Analytics
    Route::get('/whatsapp/dashboard', [\App\Http\Controllers\WhatsAppDashboardController::class, 'index'])->name('whatsapp.dashboard');
    Route::get('/api/whatsapp/stats', [\App\Http\Controllers\Api\WhatsAppStatsController::class, 'stats'])->name('api.whatsapp.stats');
    Route::get('/api/whatsapp/messages', [\App\Http\Controllers\Api\WhatsAppStatsController::class, 'messages'])->name('api.whatsapp.messages');

    // WhatsApp Onboarding
    Route::post('/whatsapp/onboarding/callback', [\App\Http\Controllers\WhatsAppOnboardingController::class, 'callback'])->name('whatsapp.onboarding.callback');

    // SMS Manager
    Route::get('/sms/logs', [\App\Http\Controllers\SmsSettingsController::class, 'logs'])->name('sms.logs');
    Route::post('/whatsapp/test', [WhatsAppSettingsController::class, 'test'])->name('whatsapp.test');
    Route::post('/whatsapp/create-test-appointment', [WhatsAppSettingsController::class, 'createTestAppointment'])->name('whatsapp.test.appointment');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Clinic Settings
    Route::get('/clinic/settings', [\App\Http\Controllers\ClinicController::class, 'edit'])->name('clinic.edit');
    Route::patch('/clinic/settings', [\App\Http\Controllers\ClinicController::class, 'update'])->name('clinic.update');

    // Staff (Receptionists)
    Route::resource('staff', \App\Http\Controllers\StaffController::class);

    // Billing
    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
    Route::get('/billing/plans', [BillingController::class, 'plans'])->name('billing.plans');
    Route::post('/billing/checkout', [BillingController::class, 'checkout'])->name('billing.checkout');
    Route::get('/billing/success', [BillingController::class, 'success'])->name('billing.success');

    // Invoices
    Route::get('/invoices/{billingLog}/download', [InvoiceController::class, 'download'])->name('invoices.download');
    Route::get('/invoices/{billingLog}/stream', [InvoiceController::class, 'stream'])->name('invoices.stream');

    // Activity Logs
    Route::get('/admin/logs', [ActivityLogController::class, 'index'])->name('admin.logs.index');
    Route::get('/admin/logs/export', [ActivityLogController::class, 'exportPdf'])->name('admin.logs.export');
});

// Super Admin Routes
Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/admin/dashboard', [SuperAdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/super-admin/logs', [ActivityLogController::class, 'globalIndex'])->name('super-admin.logs.index');
    Route::get('/super-admin/plans', [PlanController::class, 'index'])->name('super-admin.plans.index');
    Route::patch('/super-admin/plans/{plan}/toggle', [PlanController::class, 'toggle'])->name('super-admin.plans.toggle');

    // Clinic Management
    Route::get('/super-admin/clinics', [ClinicManagementController::class, 'index'])->name('super-admin.clinics.index');
    Route::get('/super-admin/clinics/create', [ClinicManagementController::class, 'create'])->name('super-admin.clinics.create');
    Route::post('/super-admin/clinics', [ClinicManagementController::class, 'store'])->name('super-admin.clinics.store');
    Route::patch('/super-admin/clinics/{clinic}/plan', [ClinicManagementController::class, 'updatePlan'])->name('super-admin.clinics.update-plan');
    Route::patch('/super-admin/clinics/{clinic}/toggle-status', [ClinicManagementController::class, 'toggleStatus'])->name('super-admin.clinics.toggle-status');

    // Super Admin User Management
    Route::get('/super-admin/users', [\App\Http\Controllers\UserController::class, 'index'])->name('super-admin.users.index');
    Route::patch('/super-admin/users/{user}/reset-password', [\App\Http\Controllers\UserController::class, 'resetPassword'])->name('super-admin.users.reset-password');

    // API Explorer
    Route::get('/super-admin/api-explorer', [\App\Http\Controllers\SuperAdmin\ApiExplorerController::class, 'index'])->name('super-admin.api-explorer');
    Route::post('/super-admin/api-explorer/execute', [\App\Http\Controllers\SuperAdmin\ApiExplorerController::class, 'execute'])->name('super-admin.api-explorer.execute');
});

require __DIR__ . '/auth.php';
