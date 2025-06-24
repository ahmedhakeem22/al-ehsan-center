<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Patient; // For patient context in URLs
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PharmacyDispenseController extends Controller
{
    public function index(Request $request) // List of prescriptions for dispensing
    {
        // $this->authorize('viewAny', Prescription::class); // Policy for pharmacist
        $query = Prescription::with(['patient:id,full_name,file_number', 'doctor:id,name'])
                             ->where('status', 'pending') // Only show pending prescriptions
                             ->latest('prescription_date');

        if ($request->filled('patient_name')) {
            $query->whereHas('patient', function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->patient_name . '%')
                  ->orWhere('file_number', 'like', '%' . $request->patient_name . '%');
            });
        }
        if ($request->filled('doctor_name')) {
            $query->whereHas('doctor', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->doctor_name . '%');
            });
        }
        if ($request->filled('date_from')) {
            $query->whereDate('prescription_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('prescription_date', '<=', $request->date_to);
        }

        $pendingPrescriptions = $query->paginate(15)->withQueryString();

        return view('pharmacy.dispense.index', compact('pendingPrescriptions'));
    }

    public function showPrescriptionForDispense(Prescription $prescription)
    {
        // $this->authorize('dispense', $prescription);
        if ($prescription->status !== 'pending') {
            return redirect()->route('pharmacy.dispense.index')
                             ->with('warning', 'This prescription is not pending for dispensing.');
        }
        $prescription->load(['patient', 'doctor', 'items.medication']);
        return view('pharmacy.dispense.show', compact('prescription'));
    }

    public function processDispense(Request $request, Prescription $prescription)
    {
        // $this->authorize('dispense', $prescription);
        if ($prescription->status !== 'pending') {
            return redirect()->route('pharmacy.dispense.index')
                             ->with('error', 'This prescription is not in pending state or already processed.');
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.quantity_dispensed' => 'required|integer|min:0',
            // 'items.*.batch_number' => 'nullable|string', // If tracking batch numbers
            // 'items.*.expiry_date' => 'nullable|date',   // If tracking expiry dates
            'dispensing_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $prescription->update([
                'status' => 'dispensed',
                'pharmacist_id' => auth()->id(),
                'dispensing_date' => now(),
                // Append notes or use a dedicated field for dispensing notes in prescription model
                // 'notes' => $prescription->notes . PHP_EOL . "Dispensed by Pharmacist: " . ($request->dispensing_notes ?? ''),
            ]);

            foreach ($request->items as $itemId => $itemData) {
                $pItem = PrescriptionItem::find($itemId);
                if ($pItem && $pItem->prescription_id === $prescription->id) {
                    $pItem->update([
                        'quantity_dispensed' => $itemData['quantity_dispensed'],
                        // 'batch_number' => $itemData['batch_number'] ?? null,
                        // 'expiry_date' => $itemData['expiry_date'] ?? null,
                    ]);
                    // Here you would also deduct from inventory if stock management is implemented
                }
            }

            DB::commit();
            // Optionally, redirect to a "dispensed successfully" page or back to the list
            return redirect()->route('pharmacy.dispense.index')
                             ->with('success', "Prescription #{$prescription->id} for patient {$prescription->patient->full_name} dispensed successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error dispensing prescription: ' . $e->getMessage())->withInput();
        }
    }
}