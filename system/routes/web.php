<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\HearingAidController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PatientRecordFileController;
use App\Http\Controllers\HearingAidSessionController;
use App\Http\Controllers\ServiceResultSessionController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminPatientRecordController;
use App\Http\Controllers\StaffPatientRecordController;
use App\Http\Controllers\PatientAuthController;
use App\Http\Controllers\PatientPageController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\messageController;
use App\Http\Controllers\YourController;



     // Auth Routes (Controller Based with Parameters)
    Route::middleware('guest')->group(function () {
    Route::get('/login/{role?}', [AuthController::class, 'showLoginForm'])->name('login');
    Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->defaults('role', 'admin')->name('admin.login');
    Route::get('/staff/login', [AuthController::class, 'showLoginForm'])->defaults('role', 'staff')->name('staff.login');
    Route::post('/login/{role?}', [AuthController::class, 'login'])->name('login.submit'); });
    Route::post('/logout/{role?}', [AuthController::class, 'logout'])->name('logout');
    // Admin Routes (protected by role middleware)
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Simple view for dashboard
    Route::view('/dashboard', 'admin.admin-dashboard')->name('dashboard');
    // Dashboard API endpoints (backend only)
    Route::get('/api/chart-data', [AdminDashboardController::class, 'chartData'])->name('api.chart.data');
    Route::get('/api/monthly-report', [AdminDashboardController::class, 'monthlyReport'])->name('api.monthly.report');
    Route::get('/patient-record', [AppointmentController::class, 'adminPatients'])->name('patient.record');
    // Allow delete from Patient Record view (reuses adminDelete)
    Route::delete('/patient-record/{appointment}', [AppointmentController::class, 'adminDelete'])->name('patient.delete');
    Route::get('/patient-record/details/{id}', [AdminPatientRecordController::class, 'details'])->name('patient.details');
    Route::get('/appointment-record', [AppointmentController::class, 'adminIndex'])->name('appointment.record');
    Route::delete('/appointment-record/{appointment}', [AppointmentController::class, 'adminDelete'])->name('appointment.delete');
    // Services (controller CRUD subset)
    Route::get('/services', [ServiceController::class, 'index'])->name('services');
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.delete');
    // Hearing Aid
    Route::get('/hearing-aid', [HearingAidController::class, 'index'])->name('hearing.aid');
    Route::post('/hearing-aid', [HearingAidController::class, 'store'])->name('hearing.store');
    Route::put('/hearing-aid/{hearingAid}', [HearingAidController::class, 'update'])->name('hearing.update');
    Route::delete('/hearing-aid/{hearingAid}', [HearingAidController::class, 'destroy'])->name('hearing.delete');
    // Billing & Reports
    Route::get('/billing', [BillingController::class, 'adminIndex'])->name('billing');
    Route::get('/report/appointment', [AppointmentController::class, 'adminReport'])->name('report.appointment');
    Route::get('/report/billing', [BillingController::class, 'adminReport'])->name('report.billing');
    Route::view('/report/hearing-aid', 'admin.admin-report-hearing-aid')->name('report.hearing.aid');
    Route::view('/report/monthly-service', 'admin.admin-report-monthly-service')->name('report.monthly.service');
    Route::view('/report/monthly-hearing-aid-revenue', 'admin.admin-report-monthly-hearing-aid-revenue')->name('report.monthly.hearing.aid.revenue');
    // User Accounts
    Route::get('/user-account/list/{type?}', [UserController::class, 'index'])->defaults('type', 'all')->name('user.account.list');
    Route::get('/user-account/register/{role?}', [UserController::class, 'create'])->defaults('role', 'admin')->name('user.account.register');
    Route::post('/user-account/register/{role?}', [UserController::class, 'store'])->name('user.account.store');
    Route::get('/user-account/show/{id}/{role?}', [UserController::class, 'show'])->name('user.account.show');
    Route::put('/user-account/update/{id}', [UserController::class, 'update'])->name('user.account.update');
    Route::post('/user-account/{id}/branch', [UserController::class, 'setBranch'])->name('user.account.setBranch');
    Route::delete('/user-account/delete/{id}/{role?}', [UserController::class, 'destroy'])->name('user.account.delete'); });
    // Staff Routes (protected by role middleware)
    Route::middleware(['role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::view('/dashboard', 'staff.staff-dashboard')->name('dashboard');
    Route::get('/schedule/today', [AppointmentController::class, 'todaySchedule'])->name('schedule.today');
    Route::get('/appointment/new-patient', [AppointmentController::class, 'staffNew'])->name('appointment.new.patient');
    Route::post('/appointment/{appointment}/confirm', [AppointmentController::class, 'confirm'])->name('appointment.confirm');
    Route::post('/appointment/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointment.cancel');
    Route::post('/appointment/{appointment}/add-patient-record', [AppointmentController::class, 'addPatientRecordFromAppointment'])->name('appointment.add.patient.record');
    Route::get('/appointment/schedule', [AppointmentController::class, 'staffSchedule'])->name('appointment.schedule');
    Route::get('/appointment/record', [AppointmentController::class, 'staffRecord'])->name('appointment.record');
    Route::delete('/appointment/{appointment}', [AppointmentController::class, 'staffDelete'])->name('appointment.delete');
    Route::view('/message', 'staff.staff-message')->name('message');
    Route::get('/patient-record', [AppointmentController::class, 'staffPatients'])->name('patient.record');
    Route::post('/patient-record', [\App\Http\Controllers\PatientRecordController::class, 'store'])->name('patient.record.store');
    // Allow delete from Patient Record view (reuses staffDelete)
    Route::delete('/patient-record/{appointment}', [AppointmentController::class, 'staffDelete'])->name('patient.delete');
    Route::get('/services', [ServiceController::class, 'index'])->name('services');
    Route::get('/hearing-aid', [HearingAidController::class, 'index'])->name('hearing.aid');
    Route::get('/billing', [BillingController::class, 'index'])->name('billing');
    Route::delete('/billing/{patientId}', [BillingController::class, 'destroy'])->name('billing.delete');
    Route::view('/patient/register', 'staff.staff-patient-register')->name('patient.register');
    Route::post('/patient/register', [UserController::class, 'patientStore'])->name('patient.register.store');
    // File-backed Patient Records API 
    Route::get('/api/patient-records', [PatientRecordFileController::class, 'index'])->name('patient.records.index');
    Route::post('/api/patient-records', [PatientRecordFileController::class, 'store'])->name('patient.records.store');
    Route::delete('/api/patient-records/{id}', [PatientRecordFileController::class, 'destroy'])->name('patient.records.destroy');
    Route::get('/api/patient-records/{id}', [PatientRecordFileController::class, 'show'])->name('patient.records.show');
    // Hearing Aid API endpoints (temporary storage)
    Route::get('/api/patient-records/{id}/hearing-aids', [PatientRecordFileController::class, 'getHearingAids'])->name('patient.hearing.aids.index');
    Route::post('/api/patient-records/{id}/hearing-aids', [PatientRecordFileController::class, 'storeHearingAid'])->name('patient.hearing.aids.store');
    Route::delete('/api/patient-records/{patientId}/hearing-aids/{hearingAidId}', [PatientRecordFileController::class, 'destroyHearingAid'])->name('patient.hearing.aids.destroy');
    // Session-backed Hearing Aid endpoints for quick prototyping
    Route::get('/api/session/patient/{id}/hearing-aids', [HearingAidSessionController::class, 'index'])->name('patient.hearing.session.index');
    Route::post('/api/session/patient/{id}/hearing-aids', [HearingAidSessionController::class, 'store'])->name('patient.hearing.session.store');
    Route::delete('/api/session/patient/{patientId}/hearing-aids/{hearingAidId}', [HearingAidSessionController::class, 'destroy'])->name('patient.hearing.session.destroy');
    Route::get('/api/session/patient/{id}/services/{service}', [ServiceResultSessionController::class, 'index'])->name('patient.service.session.index');
    Route::post('/api/session/patient/{id}/services/{service}', [ServiceResultSessionController::class, 'store'])->name('patient.service.session.store');
    Route::delete('/api/session/patient/{id}/services/{service}/{resultId}', [ServiceResultSessionController::class, 'destroy'])->name('patient.service.session.destroy');
    Route::post('/patient-record/details/{id}/service/{service}/save', [ServiceResultSessionController::class, 'storeAndRedirect'])->name('patient.service.session.save');
    Route::post('/patient-record/details/{id}/hearing/save', [HearingAidSessionController::class, 'storeAndRedirect'])->name('patient.hearing.session.save');
    Route::get('/patient-record/details/{id}', [StaffPatientRecordController::class, 'details'])->name('patient.record.details');
    Route::get('/patient-record/details/{id}/oae', [StaffPatientRecordController::class, 'oae'])->name('patient.record.details.oae');
    Route::get('/patient-record/details/{id}/abr', [StaffPatientRecordController::class, 'abr'])->name('patient.record.details.abr');
    Route::get('/patient-record/details/{id}/assr', [StaffPatientRecordController::class, 'assr'])->name('patient.record.details.assr');
    Route::get('/patient-record/details/{id}/pta', [StaffPatientRecordController::class, 'pta'])->name('patient.record.details.pta');
    Route::get('/patient-record/details/{id}/tym', [StaffPatientRecordController::class, 'tym'])->name('patient.record.details.tym');
    Route::get('/patient-record/details/{id}/speech', [StaffPatientRecordController::class, 'speech'])->name('patient.record.details.speech');
    Route::get('/patient-record/details/{id}/hearing', [StaffPatientRecordController::class, 'hearing'])->name('patient.record.details.hearing');
    // API endpoint to fetch patient data with test results
    Route::get('/api/patient-records/{id}/data', [StaffPatientRecordController::class, 'getPatientData'])->name('patient.record.data'); });
    // Root route now redirects to login page
    Route::get('/', function() {
        return redirect()->route('login');
    });
    Route::get('/home', [PatientPageController::class, 'home'])->name('home');
    Route::get('/patient-home', [PatientPageController::class, 'home'])->name('patient.home.alt');
    // Patient login page
    Route::get('/patient/login', [PatientAuthController::class, 'showLogin'])->name('patient.login');
    Route::post('/patient/login', [PatientAuthController::class, 'login'])->name('patient.login.submit');
    Route::post('/patient/logout', [PatientAuthController::class, 'logout'])->name('patient.logout');
    Route::get('/patient/dashboard', [PatientPageController::class, 'dashboard'])->name('patient.dashboard');
    // Patient test result view
    Route::get('/patient/testresult', [PatientPageController::class, 'testResult'])->name('patient.testresult');
    // Patient appointment page
    Route::get('/patient/appointment', [PatientPageController::class, 'appointment'])->name('patient.appointment');
    // Patient messaging page
    Route::get('/patient/message', [PatientPageController::class, 'message'])->name('patient.message');
    // Booking preview: capture form data into session and show preview
    Route::post('/book/preview', [BookingController::class, 'preview'])->name('book.preview');
    // Finalize booking now persisted in DB
    Route::post('/book/confirm', [AppointmentController::class, 'store'])->name('book.confirm');
    // Notification endpoints (appointments)
    Route::get('/notifications/appointments/count', [AppointmentController::class, 'unseenCount'])->name('notifications.appointments.count');
    Route::post('/notifications/appointments/mark-seen', [AppointmentController::class, 'markSeen'])->name('notifications.appointments.markSeen');
    Route::get('/notifications/appointments/list', [AppointmentController::class, 'listRecent'])->name('notifications.appointments.list');
    Route::get('/notifications/stream', [AppointmentController::class, 'streamNotifications'])->name('notifications.stream');
    
    // Profile management routes
    Route::get('/profile/manage', function() {
        if (!Auth::check()) return redirect()->route('login');
        
        // Check user role and return appropriate view
        $user = Auth::user();
        if ($user->role === 'admin') {
            return view('profile.manage-account-profile-admin');
        } else {
            return view('profile.manage-account-profile');
        }
    })->name('profile.manage');
    Route::put('/profile/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    
    // Appointment slot APIs
    Route::get('/api/appointments/check-slot', [AppointmentController::class, 'checkSlot'])->name('appointments.checkSlot');
    Route::get('/api/appointments/next-slot', [AppointmentController::class, 'nextSlot'])->name('appointments.nextSlot');
    Route::get('/api/appointments/available-slots', [AppointmentController::class, 'availableSlots'])->name('appointments.availableSlots');
     // Message storing route
     Route::post('message/store', [messageController::class, 'store'])->name('message.store');
     Route::get('/messages/{user1}/{user2}', [MessageController::class, 'conversation']);