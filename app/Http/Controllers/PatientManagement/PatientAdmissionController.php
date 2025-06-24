<?php

namespace App\Http\Controllers\PatientManagement;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Bed;
use App\Models\Floor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Log;

class PatientAdmissionController extends Controller
{
  // عرض نموذج تسجيل مريض جديد (مشابه لـ PatientController@create ولكن قد يكون له تدفق مختلف)
  public function showRegistrationForm()
  {
    return view('patient_management.admissions.register');
  }

  private function generateFileNumber(): string // Helper function
  {
    $year = Carbon::now()->year;
    $prefix = "EH-{$year}-";
    $lastPatient = Patient::where('file_number', 'like', "{$prefix}%")->orderBy('file_number', 'desc')->first();

    if ($lastPatient) {
      $lastNumber = (int) Str::afterLast($lastPatient->file_number, '-');
      $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    } else {
      $newNumber = '0001';
    }
    return $prefix . $newNumber;
  }

  // حفظ بيانات المريض الجديد وتوجيهه لتسكين السرير
  public function registerPatient(Request $request)
  {
    Log::info('Patient Registration Request Data:', $request->all());

    $request->validate([
      'full_name' => 'required|string|max:255',
      'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
      'approximate_age' => 'nullable|integer|min:0|max:150',
      'gender' => ['nullable', \Illuminate\Validation\Rule::in(['male', 'female', 'other', 'unknown'])],
      'province' => 'nullable|string|max:100',
      'arrival_date' => 'required|date_format:d/m/Y', // <--- تغيير هنا
      'condition_on_arrival' => 'nullable|string',
      'file_number_manual' => ['nullable', 'string', 'max:50', \Illuminate\Validation\Rule::unique('patients', 'file_number')],
    ]);

    $data = $request->except(['profile_image', 'file_number_manual', 'arrival_date']); // استثناء arrival_date مؤقتاً
    $data['created_by_user_id'] = auth()->id();
    $data['status'] = 'active';

    // تحويل التاريخ قبل الحفظ
    try {
      $data['arrival_date'] = Carbon::createFromFormat('d/m/Y', $request->arrival_date)->format('Y-m-d');
    } catch (\Exception $e) {
      // هذا لا ينبغي أن يحدث إذا نجح date_format
      Log::error('Failed to parse arrival_date: ' . $request->arrival_date . ' Error: ' . $e->getMessage());
      return back()->withInput()->withErrors(['arrival_date' => 'تنسيق تاريخ الوصول غير صالح.']);
    }

    if ($request->hasFile('profile_image')) {
      $fileName = time() . '_' . $request->file('profile_image')->getClientOriginalName();
      $path = $request->file('profile_image')->storeAs('patient_profiles', $fileName, 'public');
      $data['profile_image_path'] = $path;
    }

    if ($request->filled('file_number_manual')) {
      $data['file_number'] = $request->file_number_manual;
    } else {
      $data['file_number'] = $this->generateFileNumber();
      while (Patient::where('file_number', $data['file_number'])->exists()) {
        $data['file_number'] = $this->generateFileNumber();
      }
    }

    // $data['gender'] سيتم تضمينه من $request->except إذا قمت بإضافته هناك،
    // أو أضفه مباشرة:
    if ($request->filled('gender')) {
      $data['gender'] = $request->gender;
    }


    $patient = Patient::create($data);

    return redirect()->route('patient_management.admissions.show_bed_assignment', $patient->id)
      ->with('success', 'Patient registered successfully. Please assign a bed.');
  }

  // عرض نموذج تسكين المريض في سرير
  public function showBedAssignmentForm(Patient $patient)
  {
    // جلب الأسرة الشاغرة فقط مع معلومات الطابق والغرفة
    $floors = Floor::with([
      'rooms.beds' => function ($query) {
        $query->where('status', 'vacant')->orderBy('bed_number');
      }
    ])
      ->whereHas('rooms.beds', function ($query) {
        $query->where('status', 'vacant');
      })
      ->orderBy('name')
      ->get();

    return view('patient_management.admissions.assign_bed', compact('patient', 'floors'));
  }

  // حفظ عملية تسكين المريض في السرير
  public function assignBed(Request $request, Patient $patient)
  {
    $request->validate([
      'bed_id' => 'required|exists:beds,id',
    ]);

    $bed = Bed::find($request->bed_id);

    if (!$bed || $bed->status !== 'vacant') {
      return back()->with('error', 'Selected bed is not available or does not exist.')->withInput();
    }

    DB::beginTransaction();
    try {
      // إذا كان المريض يشغل سريراً سابقاً، اجعله شاغراً
      if ($patient->current_bed_id) {
        $oldBed = Bed::find($patient->current_bed_id);
        if ($oldBed) {
          $oldBed->status = 'vacant';
          $oldBed->save();
        }
      }

      // تحديث بيانات المريض بالسرير الجديد
      $patient->current_bed_id = $bed->id;
      $patient->save();

      // تحديث حالة السرير الجديد إلى مشغول
      $bed->status = 'occupied';
      // $bed->patient_id = $patient->id; // If you decide to add patient_id to beds table directly
      $bed->save();

      DB::commit();
      return redirect()->route('patient_management.patients.show', $patient->id)
        ->with('success', "Patient {$patient->full_name} assigned to Bed {$bed->bed_number} in Room {$bed->room->room_number} (Floor: {$bed->room->floor->name}) successfully.");
    } catch (\Exception $e) {
      DB::rollBack();
      return back()->with('error', 'An error occurred during bed assignment: ' . $e->getMessage())->withInput();
    }
  }
}