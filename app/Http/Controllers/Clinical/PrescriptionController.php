<?php

namespace App\Http\Controllers\Clinical;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\TreatmentPlan;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Medication; // For medication list
use App\Models\User; // For doctor/pharmacist list
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PrescriptionController extends Controller
{
    public function index(Patient $patient, Request $request)
    {
        // Can be filtered by treatment plan or show all for patient
        $query = Prescription::where('patient_id', $patient->id)
                             ->with(['doctor:id,name', 'pharmacist:id,name', 'treatmentPlan:id,diagnosis'])
                             ->latest('prescription_date');

        if ($request->filled('treatment_plan_id')) {
            $query->where('treatment_plan_id', $request->treatment_plan_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $prescriptions = $query->paginate(10)->withQueryString();
        $treatmentPlans = $patient->treatmentPlans()->pluck('diagnosis', 'id'); // For filter
        $statuses = ['pending' => 'Pending', 'dispensed' => 'Dispensed', 'cancelled' => 'Cancelled'];

        return view('clinical.prescriptions.index', compact('patient', 'prescriptions', 'treatmentPlans', 'statuses'));
    }

    public function create(Patient $patient, Request $request)
    {
        // Check if coming from a specific treatment plan
        $treatmentPlanId = $request->query('treatment_plan_id');
        $treatmentPlan = $treatmentPlanId ? TreatmentPlan::find($treatmentPlanId) : null;
        if ($treatmentPlanId && (!$treatmentPlan || $treatmentPlan->patient_id !== $patient->id)) {
            return redirect()->route('clinical.prescriptions.index', $patient->id)->with('error', 'Invalid treatment plan specified.');
        }

        $doctors = User::whereHas('role', fn($q) => $q->where('name', 'Doctor'))->orderBy('name')->pluck('name', 'id');
        $medications = Medication::orderBy('name')->get(['id', 'name', 'strength', 'form']); // For dropdown/autocomplete
        $statuses = ['pending' => 'Pending', 'dispensed' => 'Dispensed', 'cancelled' => 'Cancelled'];


        return view('clinical.prescriptions.create', compact('patient', 'treatmentPlan', 'doctors', 'medications', 'statuses'));
    }

    public function store(Request $request, Patient $patient)
    {
        // $this->authorize('create', Prescription::class); // Policy check
        $request->validate([
            'treatment_plan_id' => 'nullable|exists:treatment_plans,id,patient_id,' . $patient->id,
            // 'doctor_id' => 'required|exists:users,id', // Should be auth()->id() if doctor is creating
            'prescription_date' => 'required|date_format:Y-m-d H:i:s',
            'status' => ['required', Rule::in(['pending', 'dispensed', 'cancelled'])],
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.medication_id' => 'nullable|exists:medications,id',
            'items.*.medication_name_manual' => 'required_without:items.*.medication_id|nullable|string|max:255',
            'items.*.dosage' => 'required|string|max:100',
            'items.*.frequency' => 'required|string|max:100',
            'items.*.duration' => 'required|string|max:100',
            'items.*.instructions' => 'nullable|string',
            'items.*.quantity_prescribed' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $prescription = Prescription::create([
                'patient_id' => $patient->id,
                'treatment_plan_id' => $request->treatment_plan_id,
                'doctor_id' => auth()->id(), // Assuming logged-in user is the doctor
                'prescription_date' => $request->prescription_date,
                'status' => $request->status, // Usually 'pending' on creation by doctor
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $itemData) {
                PrescriptionItem::create([
                    'prescription_id' => $prescription->id,
                    'medication_id' => $itemData['medication_id'] ?? null,
                    'medication_name_manual' => $itemData['medication_name_manual'] ?? null,
                    'dosage' => $itemData['dosage'],
                    'frequency' => $itemData['frequency'],
                    'duration' => $itemData['duration'],
                    'instructions' => $itemData['instructions'] ?? null,
                    'quantity_prescribed' => $itemData['quantity_prescribed'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('clinical.prescriptions.show', [$patient->id, $prescription->id])
                             ->with('success', 'Prescription created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating prescription: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Patient $patient, Prescription $prescription)
    {
        if ($prescription->patient_id !== $patient->id) abort(404);
        $prescription->load(['doctor', 'pharmacist', 'treatmentPlan', 'items.medication']);
        return view('clinical.prescriptions.show', compact('patient', 'prescription'));
    }

    public function edit(Patient $patient, Prescription $prescription)
    {
        if ($prescription->patient_id !== $patient->id) abort(404);
        // $this->authorize('update', $prescription);
        if ($prescription->status === 'dispensed' && !(auth()->user()->role->name === 'Admin') ) { // Example: Admins can edit dispensed
             return redirect()->route('clinical.prescriptions.show', [$patient->id, $prescription->id])
                             ->with('error', 'Cannot edit a dispensed prescription.');
        }


        $treatmentPlans = $patient->treatmentPlans()->pluck('diagnosis', 'id');
        $doctors = User::whereHas('role', fn($q) => $q->where('name', 'Doctor'))->orderBy('name')->pluck('name', 'id');
        $medications = Medication::orderBy('name')->get(['id', 'name', 'strength', 'form']);
        $statuses = ['pending' => 'Pending', 'dispensed' => 'Dispensed', 'cancelled' => 'Cancelled'];

        return view('clinical.prescriptions.edit', compact('patient', 'prescription', 'treatmentPlans', 'doctors', 'medications', 'statuses'));
    }

    public function update(Request $request, Patient $patient, Prescription $prescription)
    {
        if ($prescription->patient_id !== $patient->id) abort(404);
        // $this->authorize('update', $prescription);
        if ($prescription->status === 'dispensed' && !(auth()->user()->role->name === 'Admin') ) {
             return redirect()->route('clinical.prescriptions.show', [$patient->id, $prescription->id])
                             ->with('error', 'Cannot update a dispensed prescription.');
        }

        $request->validate([
            'treatment_plan_id' => 'nullable|exists:treatment_plans,id,patient_id,' . $patient->id,
            'doctor_id' => 'required|exists:users,id', // Keep original doctor or allow change by admin
            'prescription_date' => 'required|date_format:Y-m-d H:i:s',
            'status' => ['required', Rule::in(['pending', 'dispensed', 'cancelled'])],
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:prescription_items,id,prescription_id,' . $prescription->id, // For existing items
            'items.*.medication_id' => 'nullable|exists:medications,id',
            'items.*.medication_name_manual' => 'required_without:items.*.medication_id|nullable|string|max:255',
            'items.*.dosage' => 'required|string|max:100',
            'items.*.frequency' => 'required|string|max:100',
            'items.*.duration' => 'required|string|max:100',
            'items.*.instructions' => 'nullable|string',
            'items.*.quantity_prescribed' => 'nullable|integer|min:0',
        ]);


        DB::beginTransaction();
        try {
            $prescription->update($request->only([
                'treatment_plan_id', 'doctor_id', 'prescription_date', 'status', 'notes'
            ]));

            $existingItemIds = $prescription->items()->pluck('id')->all();
            $newItemIds = [];

            foreach ($request->items as $itemData) {
                $prescriptionItemData = [
                    'medication_id' => $itemData['medication_id'] ?? null,
                    'medication_name_manual' => $itemData['medication_name_manual'] ?? null,
                    'dosage' => $itemData['dosage'],
                    'frequency' => $itemData['frequency'],
                    'duration' => $itemData['duration'],
                    'instructions' => $itemData['instructions'] ?? null,
                    'quantity_prescribed' => $itemData['quantity_prescribed'] ?? null,
                ];

                if (isset($itemData['id']) && in_array($itemData['id'], $existingItemIds)) {
                    // Update existing item
                    $item = PrescriptionItem::find($itemData['id']);
                    $item->update($prescriptionItemData);
                    $newItemIds[] = $item->id;
                } else {
                    // Create new item
                    $newItem = $prescription->items()->create($prescriptionItemData);
                    $newItemIds[] = $newItem->id;
                }
            }

            // Delete items that were removed from the form
            $itemsToDelete = array_diff($existingItemIds, $newItemIds);
            if (!empty($itemsToDelete)) {
                PrescriptionItem::whereIn('id', $itemsToDelete)->delete();
            }


            DB::commit();
            return redirect()->route('clinical.prescriptions.show', [$patient->id, $prescription->id])
                             ->with('success', 'Prescription updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating prescription: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Patient $patient, Prescription $prescription)
    {
        if ($prescription->patient_id !== $patient->id) abort(404);
        // $this->authorize('delete', $prescription);
        if ($prescription->status === 'dispensed' && !(auth()->user()->role->name === 'Admin') ) {
             return redirect()->route('clinical.prescriptions.index', $patient->id)
                             ->with('error', 'Cannot delete a dispensed prescription.');
        }

        DB::beginTransaction();
        try {
            $prescription->items()->delete();
            $prescription->delete();
            DB::commit();
            return redirect()->route('clinical.prescriptions.index', $patient->id)
                             ->with('success', 'Prescription deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
             return redirect()->route('clinical.prescriptions.index', $patient->id)
                             ->with('error', 'Error deleting prescription: ' . $e->getMessage());
        }
    }

    // Action for pharmacist to mark as dispensed
    public function markAsDispensed(Request $request, Patient $patient, Prescription $prescription)
    {
        // $this->authorize('dispense', $prescription); // Policy
        if ($prescription->patient_id !== $patient->id || auth()->user()->role->name !== 'Pharmacist') {
             return back()->with('error', 'Unauthorized action.');
        }
        if ($prescription->status !== 'pending') {
            return back()->with('error', 'Prescription is not in pending state.');
        }

        $request->validate([
            'items.*.quantity_dispensed' => 'required|integer|min:0', // Validate quantity for each item
            'dispensing_notes' => 'nullable|string', // General notes for dispensing
        ]);

        DB::beginTransaction();
        try {
            $prescription->update([
                'status' => 'dispensed',
                'pharmacist_id' => auth()->id(),
                'dispensing_date' => now(),
                // 'notes' => $prescription->notes . PHP_EOL . "Dispensed: " . $request->dispensing_notes, // Append notes
            ]);

            // Update quantity dispensed for each item
            foreach($request->items as $itemId => $itemData) {
                $pItem = PrescriptionItem::find($itemId);
                if($pItem && $pItem->prescription_id === $prescription->id) {
                    $pItem->update(['quantity_dispensed' => $itemData['quantity_dispensed']]);
                }
            }


            DB::commit();
            return redirect()->route('clinical.prescriptions.show', [$patient->id, $prescription->id])
                             ->with('success', 'Prescription marked as dispensed.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error dispensing prescription: ' . $e->getMessage())->withInput();
        }
    }
}