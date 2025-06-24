<?php

namespace App\Http\Controllers\Clinical;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\TreatmentPlan;
use App\Models\User; // For doctor list
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TreatmentPlanController extends Controller
{
    public function index(Patient $patient)
    {
        $plans = $patient->treatmentPlans()->with('doctor:id,name')->latest('start_date')->paginate(10);
        return view('clinical.treatment_plans.index', compact('patient', 'plans'));
    }

    public function create(Patient $patient)
    {
        $doctors = User::whereHas('role', function ($q) {
            $q->whereIn('name', ['Doctor', 'Psychologist']); // Or specific roles allowed to create plans
        })->orderBy('name')->pluck('name', 'id');

        $statuses = ['active' => 'Active', 'completed' => 'Completed', 'cancelled' => 'Cancelled', 'on_hold' => 'On Hold'];
        return view('clinical.treatment_plans.create', compact('patient', 'doctors', 'statuses'));
    }

    public function store(Request $request, Patient $patient)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'diagnosis' => 'required|string',
            'plan_details' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => ['required', Rule::in(['active', 'completed', 'cancelled', 'on_hold'])],
        ]);

        $data = $request->all();
        $data['patient_id'] = $patient->id;
        // $data['doctor_id'] = auth()->id(); // Or allow selection if admin is creating for a doctor

        TreatmentPlan::create($data);

        return redirect()->route('clinical.treatment_plans.index', $patient->id)
                         ->with('success', 'Treatment plan created successfully.');
    }

    public function show(Patient $patient, TreatmentPlan $plan)
    {
        if ($plan->patient_id !== $patient->id) abort(404);
        $plan->load('doctor', 'prescriptions.doctor', 'prescriptions.pharmacist'); // Eager load prescriptions
        return view('clinical.treatment_plans.show', compact('patient', 'plan'));
    }

    public function edit(Patient $patient, TreatmentPlan $plan)
    {
        if ($plan->patient_id !== $patient->id) abort(404);
        // Add authorization check: e.g., only the creating doctor or an admin can edit.
        // $this->authorize('update', $plan);

        $doctors = User::whereHas('role', function ($q) {
            $q->whereIn('name', ['Doctor', 'Psychologist']);
        })->orderBy('name')->pluck('name', 'id');
        $statuses = ['active' => 'Active', 'completed' => 'Completed', 'cancelled' => 'Cancelled', 'on_hold' => 'On Hold'];

        return view('clinical.treatment_plans.edit', compact('patient', 'plan', 'doctors', 'statuses'));
    }

    public function update(Request $request, Patient $patient, TreatmentPlan $plan)
    {
        if ($plan->patient_id !== $patient->id) abort(404);
        // $this->authorize('update', $plan);

        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'diagnosis' => 'required|string',
            'plan_details' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => ['required', Rule::in(['active', 'completed', 'cancelled', 'on_hold'])],
        ]);

        $plan->update($request->all());

        return redirect()->route('clinical.treatment_plans.show', [$patient->id, $plan->id])
                         ->with('success', 'Treatment plan updated successfully.');
    }

    public function destroy(Patient $patient, TreatmentPlan $plan)
    {
        if ($plan->patient_id !== $patient->id) abort(404);
        // $this->authorize('delete', $plan);

        // Consider what to do with associated prescriptions: soft delete, reassign, or prevent deletion.
        if ($plan->prescriptions()->count() > 0) {
            return redirect()->route('clinical.treatment_plans.index', $patient->id)
                             ->with('error', 'Cannot delete plan. It has associated prescriptions.');
        }
        $plan->delete();
        return redirect()->route('clinical.treatment_plans.index', $patient->id)
                         ->with('success', 'Treatment plan deleted successfully.');
    }
}