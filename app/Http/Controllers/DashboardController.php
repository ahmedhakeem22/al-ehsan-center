<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Patient;
use App\Models\Bed;
use App\Models\User;
use App\Models\Prescription;
use App\Models\LabTestRequest;
use App\Models\EmployeeShift;
use App\Models\ActivityLog; // تم استخدام \App\Models\ActivityLog مباشرة في السابق، التأكيد هنا
use App\Models\Employee; // تم استخدام \App\Models\Employee مباشرة في السابق، التأكيد هنا
use App\Models\FunctionalAssessmentForm; // تم استخدام \App\Models\FunctionalAssessmentForm مباشرة في السابق
use App\Models\ClinicalNote; // تم استخدام \App\Models\ClinicalNote مباشرة في السابق
// افترض وجود نموذج للمواعيد إذا كنت ستعرضها للـ Admin بشكل مشابه
// use App\Models\Appointment; 
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role->name;

        $data = [
            'greetingName' => $user->name,
            'userRole' => $role // لتسهيل التحقق في الواجهة
        ];

        $notifications_for_box = collect(); // Initialize as empty collection

        switch ($role) {
            case 'Admin':
            case 'Super Admin':
                $data['totalPatients'] = Patient::where('status', 'active')->count();
                $data['totalBeds'] = Bed::count();
                $data['occupiedBeds'] = Bed::where('status', 'occupied')->count();
                $data['vacantBeds'] = $data['totalBeds'] - $data['occupiedBeds'];
                $data['occupancyPercentage'] = ($data['totalBeds'] > 0) ? round(($data['occupiedBeds'] / $data['totalBeds']) * 100, 2) : 0;
                $data['totalUsers'] = User::count();
                
                // للجدول الذي كان يستخدم admin-dashboard-patinets.blade.php
                $data['recent_patients_list_title'] = 'أحدث المرضى المسجلين';
                $data['recent_patients_list'] = Patient::latest('created_at')->take(5)
                                                    ->select('id', 'full_name', 'approximate_age', 'arrival_date', 'profile_image_path', 'status') // افترض وجود status للون
                                                    ->get();

                // للجدول الذي كان يستخدم admin-dashboard-appointments.blade.php
                // سأفترض أن التقييمات الحديثة يمكن أن تكون بمثابة "نشاطات قادمة/حديثة" للعرض
                $data['upcoming_appointments_title'] = 'أحدث التقييمات الوظيفية';
                $data['upcoming_appointments_list'] = FunctionalAssessmentForm::latest('assessment_date_gregorian')->take(5)
                                                        ->with(['patient:id,full_name,profile_image_path', 'assessor:id,name'])
                                                        ->get();
                
                $notifications_for_box = ActivityLog::with('user')->latest()->take(5)->get();
                break;

            case 'Doctor':
                $data['activePatients'] = Patient::where('status', 'active')->count(); // أو فلتر بالطبيب المسؤول
                $data['pendingLabResults'] = LabTestRequest::where('status', 'completed')
                                                           ->whereDoesntHave('clinicalNotes', function($q) use ($user) {
                                                               $q->where('author_id', $user->id)->where('note_type', 'lab_review');
                                                           })
                                                           ->where('doctor_id', $user->id)
                                                           ->count();
                $data['recentAssessmentsTitle'] = 'تقييمات وظيفية حديثة';
                $data['recentAssessments'] = FunctionalAssessmentForm::where('assessor_id', $user->id)
                                                                      ->latest('assessment_date_gregorian')
                                                                      ->take(5)->with('patient:id,full_name,profile_image_path')->get();
                // يمكن إضافة قائمة مرضى الطبيب هنا إذا لزم الأمر لواجهة "Recent Patients" الخاصة بالطبيب
                break;

            case 'Nurse':
                $data['activePatientsInFloor'] = 0; // يحتاج منطق إضافي
                $data['pendingDoctorRecommendations'] = ClinicalNote::where('note_type', 'doctor_recommendation')
                                                                    ->where('is_actioned', false)
                                                                    ->count();
                $data['recentObservationsTitle'] = 'ملاحظات تمريضية حديثة';                                                    
                $data['recentObservations'] = ClinicalNote::where('author_id', $user->id)
                                                           ->where('note_type', 'nurse_observation')
                                                           ->latest()
                                                           ->take(5)->with('patient:id,full_name,profile_image_path')->get();
                break;

            case 'Receptionist':
                $data['newAdmissionsToday'] = Patient::whereDate('arrival_date', Carbon::today())->count();
                $data['totalActivePatients'] = Patient::where('status', 'active')->count();
                $data['vacantBedsCount'] = Bed::where('status', 'vacant')->count();

                $data['recent_patients_list_title'] = 'مرضى مسجلون حديثاً';
                $data['recent_patients_list'] = Patient::latest('created_at')->take(5)
                                                    ->select('id', 'full_name', 'file_number', 'created_at', 'approximate_age', 'arrival_date', 'profile_image_path', 'status')
                                                    ->get();
                break;

            case 'Pharmacist':
                $data['pendingPrescriptions'] = Prescription::where('status', 'pending')->count();
                $data['dispensedToday'] = Prescription::where('status', 'dispensed')
                                                      ->whereDate('dispensing_date', Carbon::today())
                                                      ->count();
                $data['recentlyDispensedTitle'] = 'وصفات تم صرفها حديثاً';
                $data['recentlyDispensed'] = Prescription::where('status', 'dispensed')
                                                            ->where('pharmacist_id', $user->id)
                                                            ->latest('dispensing_date')
                                                            ->take(5)->with('patient:id,full_name,profile_image_path')->get();
                break;

            case 'Lab Technician':
                $data['pendingSamples'] = LabTestRequest::where('status', 'pending_sample')->count();
                $data['samplesToProcess'] = LabTestRequest::where('status', 'sample_collected')->count();
                $data['resultsEnteredToday'] = LabTestRequest::where('status', 'completed')
                                                             ->where('lab_technician_id', $user->id)
                                                             ->whereDate('result_date', Carbon::today())
                                                             ->count();
                $data['recentRequestsForResultsTitle'] = 'طلبات فحوصات أخيرة بانتظار النتائج';
                $data['recentRequestsForResults'] = LabTestRequest::whereIn('status', ['sample_collected', 'processing'])
                                                                    ->latest('request_date')
                                                                    ->take(5)->with(['patient:id,full_name,profile_image_path', 'doctor:id,name'])->get();
                break;
            
            case 'HR Manager':
                $data['totalEmployees'] = Employee::count();
                $data['activeUsers'] = User::where('is_active', true)->count();
                $data['recentHiresTitle'] = 'أحدث الموظفين';
                $data['recentHires'] = Employee::latest('joining_date')->take(3)
                                            ->select('id', 'full_name', 'job_title', 'joining_date', 'profile_picture_path')
                                            ->get();
                break;

            default:
                break;
        }

        return view('dashboard', compact('data', 'role', 'notifications_for_box'));
    }
}