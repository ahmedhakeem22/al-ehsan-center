<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\Medication;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MedicationController extends Controller
{
    public function index(Request $request)
    {
        $query = Medication::query();

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('generic_name', 'like', "%{$searchTerm}%")
                  ->orWhere('manufacturer', 'like', "%{$searchTerm}%");
            });
        }
        if ($request->filled('form')) {
            $query->where('form', $request->form);
        }

        $medications = $query->orderBy('name')->paginate(15)->withQueryString();
        $forms = Medication::distinct()->pluck('form')->filter()->sort();

        return view('pharmacy.medications.index', compact('medications', 'forms'));
    }

    public function create()
    {
        // $this->authorize('create', Medication::class);
        return view('pharmacy.medications.create');
    }

    public function store(Request $request)
    {
        // $this->authorize('create', Medication::class);
        $request->validate([
            'name' => 'required|string|max:255|unique:medications,name',
            'generic_name' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'form' => 'nullable|string|max:100', // e.g., Tablet, Syrup, Injection
            'strength' => 'nullable|string|max:100', // e.g., 50mg, 10mg/5ml
            'notes' => 'nullable|string',
            // Add fields for stock management if needed: current_stock, reorder_level, expiry_date_tracking
        ]);

        Medication::create($request->all());

        return redirect()->route('pharmacy.medications.index')
                         ->with('success', 'Medication added successfully.');
    }

    public function show(Medication $medication)
    {
        // $this->authorize('view', $medication);
        // Potentially show usage history or stock levels if implemented
        return view('pharmacy.medications.show', compact('medication'));
    }

    public function edit(Medication $medication)
    {
        // $this->authorize('update', $medication);
        return view('pharmacy.medications.edit', compact('medication'));
    }

    public function update(Request $request, Medication $medication)
    {
        // $this->authorize('update', $medication);
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('medications')->ignore($medication->id)],
            'generic_name' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'form' => 'nullable|string|max:100',
            'strength' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $medication->update($request->all());

        return redirect()->route('pharmacy.medications.index')
                         ->with('success', 'Medication updated successfully.');
    }

    public function destroy(Medication $medication)
    {
        // $this->authorize('delete', $medication);
        // Check if medication is used in any active prescriptions or has stock
        if ($medication->prescriptionItems()->count() > 0) {
            return redirect()->route('pharmacy.medications.index')
                             ->with('error', 'Cannot delete medication. It is used in prescriptions.');
        }
        // Add stock check if inventory management is implemented

        $medication->delete();
        return redirect()->route('pharmacy.medications.index')
                         ->with('success', 'Medication deleted successfully.');
    }
}