<?php

namespace App\Http\Controllers\PatientManagement;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Bed;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::with(['currentBed.room.floor', 'creator'])->latest();

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('full_name', 'like', "%{$searchTerm}%")
                  ->orWhere('file_number', 'like', "%{$searchTerm}%")
                  ->orWhere('province', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $patients = $query->paginate(15)->withQueryString();
        $statuses = ['active' => 'Active', 'discharged' => 'Discharged', 'deceased' => 'Deceased', 'transferred' => 'Transferred'];
        return view('patient_management.patients.index', compact('patients', 'statuses'));
    }

    public function create()
    {
        // يمكنك إضافة قائمة بالمحافظات هنا إذا أردت
        // $provinces = ['Province A', 'Province B', ...];
        return view('patient_management.patients.create');
    }

    private function generateFileNumber(): string
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

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'approximate_age' => 'nullable|integer|min:0|max:150',
            'province' => 'nullable|string|max:100',
            'arrival_date' => 'required|date',
            'condition_on_arrival' => 'nullable|string',
            'file_number_manual' => ['nullable', 'string', 'max:50', Rule::unique('patients', 'file_number')],
        ]);

        $data = $request->except(['profile_image', 'file_number_manual']);
        $data['created_by_user_id'] = auth()->id();

        if ($request->filled('file_number_manual')) {
            $data['file_number'] = $request->file_number_manual;
        } else {
            $data['file_number'] = $this->generateFileNumber();
            // Ensure uniqueness again in case of race condition (less likely with prefix)
            while (Patient::where('file_number', $data['file_number'])->exists()) {
                $data['file_number'] = $this->generateFileNumber(); // Regenerate
            }
        }

        if ($request->hasFile('profile_image')) {
            $fileName = time() . '_' . $request->file('profile_image')->getClientOriginalName();
            $path = $request->file('profile_image')->storeAs('patient_profiles', $fileName, 'public');
            $data['profile_image_path'] = $path;
        }

        $patient = Patient::create($data);

        // تم تغيير هنا ليتوافق مع PatientAdmissionController
        // بعد إنشاء المريض، يتم توجيهه إلى صفحة تسكين السرير
        return redirect()->route('patient_management.admissions.show_bed_assignment', $patient->id)
                         ->with('success', 'Patient registered successfully. Please assign a bed.');
    }

    public function show(Patient $patient)
    {
        $patient->load([
            'currentBed.room.floor',
            'media',
            'assessments.assessor',
            'clinicalNotes.author',
            'treatmentPlans.doctor',
            'prescriptions.doctor',
            'labTestRequests.doctor'
        ]);
        return view('patient_management.patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        // $provinces = [...];
        return view('patient_management.patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'approximate_age' => 'nullable|integer|min:0|max:150',
            'province' => 'nullable|string|max:100',
            'arrival_date' => 'required|date',
            'condition_on_arrival' => 'nullable|string',
            'status' => ['required', Rule::in(['active', 'discharged', 'deceased', 'transferred'])],
            'file_number' => ['required', 'string', 'max:50', Rule::unique('patients')->ignore($patient->id)],
        ]);

        $data = $request->except('profile_image');

        if ($request->hasFile('profile_image')) {
            if ($patient->profile_image_path) {
                Storage::disk('public')->delete($patient->profile_image_path);
            }
            $fileName = time() . '_' . $request->file('profile_image')->getClientOriginalName();
            $path = $request->file('profile_image')->storeAs('patient_profiles', $fileName, 'public');
            $data['profile_image_path'] = $path;
        }

        $patient->update($data);

        return redirect()->route('patient_management.patients.show', $patient->id)->with('success', 'Patient details updated successfully.');
    }

    public function destroy(Patient $patient)
    {
        // Consider soft delete or checks before deletion (e.g., active treatments)
        if ($patient->profile_image_path) {
            Storage::disk('public')->delete($patient->profile_image_path);
        }
        // Delete related media, etc. (Can be handled by model events or cascading deletes in DB)
        $patient->media()->get()->each(function ($mediaItem) {
            Storage::disk('public')->delete($mediaItem->file_path);
            $mediaItem->delete();
        });

        // Set bed to vacant if patient was occupying one
        if ($patient->current_bed_id) {
            Bed::where('id', $patient->current_bed_id)->update(['status' => 'vacant']);
        }

        $patient->delete();

        return redirect()->route('patient_management.patients.index')->with('success', 'Patient deleted successfully.');
    }
}