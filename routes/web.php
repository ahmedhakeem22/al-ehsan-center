<?php

use App\Http\Controllers\HR\AttendanceController;
use App\Http\Controllers\HR\AttendanceRequestWebController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController; // <<< إضافة هذا
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\SystemSettingController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\PatientManagement\PatientController;
use App\Http\Controllers\PatientManagement\PatientMediaController;
use App\Http\Controllers\PatientManagement\PatientAdmissionController;
use App\Http\Controllers\Assessment\FunctionalAssessmentController;
use App\Http\Controllers\Occupancy\OccupancyDashboardController;
use App\Http\Controllers\Occupancy\BedManagementController;
use App\Http\Controllers\Clinical\ClinicalNoteController;
use App\Http\Controllers\Clinical\TreatmentPlanController;
use App\Http\Controllers\Clinical\PrescriptionController;
use App\Http\Controllers\Clinical\LabTestRequestController;
use App\Http\Controllers\HR\EmployeeController;
use App\Http\Controllers\HR\EmployeeDocumentController;
use App\Http\Controllers\HR\ShiftDefinitionController;
use App\Http\Controllers\HR\EmployeeShiftController;
use App\Http\Controllers\Pharmacy\MedicationController;
use App\Http\Controllers\Pharmacy\PharmacyDispenseController;
use App\Http\Controllers\Lab\AvailableLabTestController;
use App\Http\Controllers\Lab\LabResultEntryController;

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
  return view('welcome');
});

// تعديل هنا ليشير إلى DashboardController
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
  Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
  Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
  Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
  Route::get('/dashboard', function () { // يمكن أيضًا تحويل هذا إلى كنترولر إذا لزم الأمر
    return view('admin.dashboard');
  })->name('dashboard');

  Route::resource('users', UserController::class);
  Route::resource('roles', RoleController::class);
  Route::resource('permissions', PermissionController::class);

  Route::name('settings.')->prefix('settings')->group(function () {
    Route::get('/', [SystemSettingController::class, 'index'])->name('index');

    Route::prefix('floors')->name('floors.')->group(function () {
      Route::get('/', [SystemSettingController::class, 'floorsIndex'])->name('index');
      Route::get('/create', [SystemSettingController::class, 'floorsCreate'])->name('create');
      Route::post('/', [SystemSettingController::class, 'floorsStore'])->name('store');
      Route::get('/{floor}/edit', [SystemSettingController::class, 'floorsEdit'])->name('edit');
      Route::put('/{floor}', [SystemSettingController::class, 'floorsUpdate'])->name('update');
      Route::delete('/{floor}', [SystemSettingController::class, 'floorsDestroy'])->name('destroy');
    });

    Route::prefix('rooms')->name('rooms.')->group(function () {
      Route::get('/', [SystemSettingController::class, 'roomsIndex'])->name('index');
      Route::get('/create', [SystemSettingController::class, 'roomsCreate'])->name('create');
      Route::post('/', [SystemSettingController::class, 'roomsStore'])->name('store');
      Route::get('/{room}/edit', [SystemSettingController::class, 'roomsEdit'])->name('edit');
      Route::put('/{room}', [SystemSettingController::class, 'roomsUpdate'])->name('update');
      Route::delete('/{room}', [SystemSettingController::class, 'roomsDestroy'])->name('destroy');
    });

    Route::prefix('labtests')->name('labtests.')->group(function () {
      Route::get('/', [SystemSettingController::class, 'labTestsIndex'])->name('index');
      Route::get('/create', [SystemSettingController::class, 'labTestsCreate'])->name('create');
      Route::post('/', [SystemSettingController::class, 'labTestsStore'])->name('store');
      Route::get('/{availableLabTest}/edit', [SystemSettingController::class, 'labTestsEdit'])->name('edit');
      Route::put('/{availableLabTest}', [SystemSettingController::class, 'labTestsUpdate'])->name('update');
      Route::delete('/{availableLabTest}', [SystemSettingController::class, 'labTestsDestroy'])->name('destroy');
    });
  });

  Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity_logs.index');
  Route::get('activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity_logs.show');
  Route::delete('activity-logs/{activityLog}', [ActivityLogController::class, 'destroy'])->name('activity_logs.destroy');
  Route::post('activity-logs/clear-old', [ActivityLogController::class, 'clearOldLogs'])->name('activity_logs.clearOld');
});


// === المجموعة الرئيسية للتطبيق ===
// يمكنك تطبيق middleware للأدوار هنا إذا أردت صلاحيات عامة لكل مجموعة
// مثال: Route::middleware(['auth', 'can_access_patient_module'])->group(function () { ... });
Route::middleware(['auth'])->group(function () {

  Route::name('patient_management.')->prefix('patient-management')->group(function () {
    // Patient Admission Routes
    Route::name('admissions.')->prefix('admissions')->group(function () {
      Route::get('/register', [PatientAdmissionController::class, 'showRegistrationForm'])->name('show_registration');
      Route::post('/register', [PatientAdmissionController::class, 'registerPatient'])->name('register');
      Route::get('/{patient}/assign-bed', [PatientAdmissionController::class, 'showBedAssignmentForm'])->name('show_bed_assignment');
      Route::post('/{patient}/assign-bed', [PatientAdmissionController::class, 'assignBed'])->name('assign_bed');
    });

    // Patient CRUD Routes
    Route::resource('patients', PatientController::class); // <<< إضافة هذا

    // Patient Media Routes (Nested under patient)
    Route::name('media.')->prefix('patients/{patient}/media')->group(function () {
      Route::get('/', [PatientMediaController::class, 'index'])->name('index');
      Route::get('/upload', [PatientMediaController::class, 'create'])->name('create');
      Route::post('/', [PatientMediaController::class, 'store'])->name('store');
      Route::delete('/{medium}', [PatientMediaController::class, 'destroy'])->name('destroy');
    });
  });


  // Functional Assessment Routes (Nested under patient)
  Route::name('assessment.')->prefix('patients/{patient}/assessments')->group(function () {
    Route::get('/', [FunctionalAssessmentController::class, 'index'])->name('functional.index');
    Route::get('/create', [FunctionalAssessmentController::class, 'create'])->name('functional.create');
    Route::post('/', [FunctionalAssessmentController::class, 'store'])->name('functional.store');
    Route::get('/{assessment}', [FunctionalAssessmentController::class, 'show'])->name('functional.show');
    Route::get('/{assessment}/edit', [FunctionalAssessmentController::class, 'edit'])->name('functional.edit');
    Route::put('/{assessment}', [FunctionalAssessmentController::class, 'update'])->name('functional.update');
    Route::delete('/{assessment}', [FunctionalAssessmentController::class, 'destroy'])->name('functional.destroy');
  });


  // Occupancy Management Routes
  Route::name('occupancy.')->prefix('occupancy')->group(function () {
    Route::get('/dashboard', [OccupancyDashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/beds/{bed}/details', [OccupancyDashboardController::class, 'getBedDetails'])->name('dashboard.bed_details'); // AJAX
    Route::resource('beds', BedManagementController::class);
    Route::get('api/floors/{floor_id}/rooms', [BedManagementController::class, 'getRoomsForFloor'])->name('api.floor_rooms');
  });


  // Clinical Routes (Nested under patient)
  Route::name('clinical.')->prefix('clinical/patients/{patient}')->group(function () {
    Route::resource('notes', ClinicalNoteController::class);
    Route::post('notes/{note}/action', [ClinicalNoteController::class, 'markAsActioned'])->name('notes.action');
    Route::resource('treatment-plans', TreatmentPlanController::class)->names('treatment_plans');
    Route::resource('prescriptions', PrescriptionController::class);
    Route::post('prescriptions/{prescription}/dispense', [PrescriptionController::class, 'markAsDispensed'])->name('prescriptions.dispense');
    Route::resource('lab-requests', LabTestRequestController::class)->names('lab_requests');
    Route::get('lab-requests/{labRequest}/enter-results', [LabTestRequestController::class, 'enterResultsForm'])->name('lab_requests.enter_results_form');
    Route::post('lab-requests/{labRequest}/save-results', [LabTestRequestController::class, 'saveResults'])->name('lab_requests.save_results');
  });


  // HR Routes
  Route::middleware(['auth']) // Or a specific HR role middleware
      ->prefix('hr')->name('hr.')->group(function () {
    Route::resource('employees', EmployeeController::class);
    Route::name('documents.')->prefix('employees/{employee}/documents')->group(function () {
      Route::get('/', [EmployeeDocumentController::class, 'index'])->name('index');
      Route::get('/create', [EmployeeDocumentController::class, 'create'])->name('create');
      Route::post('/', [EmployeeDocumentController::class, 'store'])->name('store');
      Route::get('/{document}/download', [EmployeeDocumentController::class, 'download'])->name('download');
      Route::delete('/{document}', [EmployeeDocumentController::class, 'destroy'])->name('destroy');
    });
      Route::get('/attendance-requests', [AttendanceRequestWebController::class, 'index'])->name('attendance-requests.index');
      Route::get('/attendance-requests/{request}/generate-qr', [AttendanceRequestWebController::class, 'generateQrCode'])->name('attendance-requests.generate-qr');
   // Attendance Management Routes
  Route::get('attendance-reports/dashboard', [App\Http\Controllers\HR\AttendanceReportController::class, 'dashboard'])->name('attendance-reports.dashboard');
Route::get('attendance-reports', [App\Http\Controllers\HR\AttendanceReportController::class, 'index'])->name('attendance-reports.index');
Route::get('employees/{employee}/attendance-report', [App\Http\Controllers\HR\AttendanceReportController::class, 'show'])->name('employees.attendance-report');

    Route::resource('shift-definitions', ShiftDefinitionController::class)->names('shift_definitions');
    Route::get('employee-shifts/calendar', [EmployeeShiftController::class, 'calendarView'])->name('employee_shifts.calendar');
    Route::get('api/employee-shifts', [EmployeeShiftController::class, 'getShiftsApi'])->name('api.employee_shifts.index');
    Route::get('employee-shifts', [EmployeeShiftController::class, 'index'])->name('employee_shifts.index');
    Route::get('employee-shifts/create', [EmployeeShiftController::class, 'create'])->name('employee_shifts.create');
  Route::get('employee-shifts/{employeeShift}/edit', [EmployeeShiftController::class, 'edit'])->name('employee_shifts.edit');
    Route::post('employee-shifts', [EmployeeShiftController::class, 'store'])->name('employee_shifts.store');
    Route::put('employee-shifts/{employeeShift}', [EmployeeShiftController::class, 'update'])->name('employee_shifts.update');
    Route::delete('employee-shifts/{employeeShift}', [EmployeeShiftController::class, 'destroy'])->name('employee_shifts.destroy');
  });


  // Pharmacy Routes
  Route::middleware(['auth', 'admin']) // Or a specific Pharmacy role middleware
      ->prefix('pharmacy')->name('pharmacy.')->group(function () {
    Route::resource('medications', MedicationController::class);
    Route::name('dispense.')->prefix('dispense')->group(function () {
      Route::get('/', [PharmacyDispenseController::class, 'index'])->name('index');
      Route::get('/{prescription}', [PharmacyDispenseController::class, 'showPrescriptionForDispense'])->name('show');
      Route::post('/{prescription}', [PharmacyDispenseController::class, 'processDispense'])->name('process');
    });
  });


  // Lab Routes
  Route::middleware(['auth', 'admin']) // Or a specific Lab role middleware
      ->prefix('lab')->name('lab.')->group(function () {
    Route::resource('available-tests', AvailableLabTestController::class)->names('available_tests');
    Route::name('results.')->prefix('results')->group(function () {
      Route::get('/', [LabResultEntryController::class, 'index'])->name('index');
      Route::get('/entry/{labRequest}', [LabResultEntryController::class, 'showEntryForm'])->name('entry_form');
      Route::post('/entry/{labRequest}', [LabResultEntryController::class, 'saveResults'])->name('save');
    });
  });

}); // End of main auth middleware group

require __DIR__ . '/auth.php';
