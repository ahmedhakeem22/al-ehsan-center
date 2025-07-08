<?php

namespace App\Http\Controllers\Clinical;

use App\Enums\ClinicalNoteTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\ClinicalNote;
use App\Models\User; // To get current user role
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ClinicalNoteController extends Controller
{
    public function index(Request $request, Patient $patient)
    {
        $query = $patient->clinicalNotes()->with('author:id,name')->latest();

        if ($request->filled('author_role')) {
            $query->where('author_role', $request->author_role);
        }
        if ($request->filled('note_type')) {
            $query->where('note_type', $request->note_type);
        }

        $notes = $query->paginate(15)->withQueryString();

        // For filtering dropdowns
        $authorRoles = ClinicalNote::distinct()->pluck('author_role')->filter()->sort();
        $noteTypes = ClinicalNote::distinct()->pluck('note_type')->filter()->sort();


        return view('clinical.notes.index', compact('patient', 'notes', 'authorRoles', 'noteTypes'));
    }

   public function create(Patient $patient)
    {
        // Optional: Authorization check
        // Gate::authorize('create-clinical-note', $patient);

        // Get the labels from the Enum as an associative array ['value' => 'label']
        // This is the correct way to populate the dropdown.
        $allowedNoteTypes = ClinicalNoteTypeEnum::labels();

        return view('clinical.notes.create', compact('patient', 'allowedNoteTypes'));
    }

    public function store(Request $request, Patient $patient)
    {
        // Optional: Authorization check
        // Gate::authorize('create-clinical-note', $patient);

        $validatedData = $request->validate([
            'note_type' => ['required', Rule::enum(ClinicalNoteTypeEnum::class)],
            'content' => 'required|string|min:10',
        ]);

        // Create the note using the relationship
        $patient->clinicalNotes()->create([
            'author_id' => auth()->id(),
            // Assuming user role is stored in a 'name' attribute of a 'role' relationship. Adjust if needed.
            'author_role' => strtolower(auth()->user()->role->name ?? 'user'),
            'note_type' => $validatedData['note_type'],
            'content' => $validatedData['content'],
        ]);

        // Redirect back to the patient profile, directly to the clinical notes tab
        return redirect()->route('patient_management.patients.show', [$patient->id, '#pat_clinical_notes'])
                         ->with('success', 'تمت إضافة الملاحظة السريرية بنجاح.');
    }

    public function show(Patient $patient, ClinicalNote $note)
    {
        $note->load('author', 'actionedBy', 'parentNote', 'replies.author');
        if ($note->patient_id !== $patient->id) {
            abort(404);
        }
        return view('clinical.notes.show', compact('patient', 'note'));
    }

 public function edit(Patient $patient, ClinicalNote $note)
    {
        // Ensure the note belongs to the patient to prevent unauthorized access
        if ($note->patient_id !== $patient->id) {
            abort(404);
        }

        // Optional: Authorization check
        // Gate::authorize('update', $note);

        // Get the labels from the Enum as an associative array ['value' => 'label']
        $allowedNoteTypes = ClinicalNoteTypeEnum::labels();

        return view('clinical.notes.edit', compact('patient', 'note', 'allowedNoteTypes'));
    }

  public function update(Request $request, Patient $patient, ClinicalNote $note)
    {
        // Ensure the note belongs to the patient
        if ($note->patient_id !== $patient->id) {
            abort(404);
        }

        // Optional: Authorization check
        // Gate::authorize('update', $note);

        $validatedData = $request->validate([
            'note_type' => ['required', Rule::enum(ClinicalNoteTypeEnum::class)],
            'content' => 'required|string|min:10',
        ]);

        $note->update($validatedData);

        return redirect()->route('clinical.notes.show', [$patient->id, $note->id])
                         ->with('success', 'تم تحديث الملاحظة بنجاح.');
    }

    public function destroy(Patient $patient, ClinicalNote $note)
    {
         if ($note->patient_id !== $patient->id || !auth()->user()->can('delete clinical notes')) { // Example permission check
            // $this->authorize('delete', $note);
             return redirect()->route('clinical.notes.index', $patient->id)
                             ->with('error', 'You are not authorized to delete this note.');
        }
        // Add logic for deleting replies if any, or preventing deletion if it has replies.
        $note->delete();
        return redirect()->route('clinical.notes.index', $patient->id)
                         ->with('success', 'Clinical note deleted successfully.');
    }

    public function markAsActioned(Request $request, Patient $patient, ClinicalNote $note)
    {
        // Authorization: e.g., only a nurse can mark a doctor's recommendation as actioned.
        if ($note->patient_id !== $patient->id || !in_array(auth()->user()->role->name, ['Nurse', 'Admin'])) {
             return back()->with('error', 'Unauthorized to perform this action.');
        }

        $request->validate([
            'action_notes' => 'nullable|string|max:1000',
        ]);

        $note->update([
            'is_actioned' => true,
            'actioned_by_user_id' => auth()->id(),
            'actioned_at' => now(),
            'action_notes' => $request->action_notes,
        ]);

        return redirect()->route('clinical.notes.show', [$patient->id, $note->id])
                         ->with('success', 'Note marked as actioned.');
    }
}